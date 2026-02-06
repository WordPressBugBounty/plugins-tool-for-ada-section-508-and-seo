<?php
/**
 * Post API for Accessibility Tools Plugin
 *
 * @package Accessibility_Tools_Alt_Text_Finder
 * @since 3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Post API class for handling post-related operations
 */
class Dvin508_Post_api {

    /**
     * Current page number
     *
     * @var int
     */
    public $page_number;

    /**
     * Post type filter
     *
     * @var string
     */
    public $post_type;

    /**
     * Number of posts per page
     *
     * @var int
     */
    public $post_per_page = 25;

    /**
     * Post list array
     *
     * @var array
     */
    public $post_list = array();

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'rest_api_init', array( $this, 'post_route' ) );
    }

    /**
     * Register custom REST API routes.
     */
    public function post_route() {
        register_rest_route( 'dvin508-seo/v1', '/post_type/(?P<post_type>[a-z]+)/(?P<page_number>\d+)', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'get_post_list' ),
            'permission_callback' => array( $this, 'check_permission' ),
            'args'                => array(
                'post_type'   => array(
                    'required'          => true,
                    'validate_callback' => function( $param, $request, $key ) {
                        return in_array( $param, array( 'post', 'page' ), true );
                    },
                ),
                'page_number' => array(
                    'required'          => true,
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_numeric( $param ) && $param > 0;
                    },
                ),
            ),
        ) );

        register_rest_route( 'dvin508-seo/v1', '/update_post/', array(
            'methods'             => 'POST',
            'callback'            => array( $this, 'update_post' ),
            'permission_callback' => array( $this, 'check_permission' ),
        ) );
    }

    /**
     * Check if user has permission to access post API
     *
     * @param WP_REST_Request $request The request object.
     * @return bool
     */
    public function check_permission( $request ) {
        return current_user_can( 'manage_options' );
    }

    /**
     * Get list of posts with image data.
     *
     * @param WP_REST_Request $request The request object.
     * @return array Post list with image data.
     */
    public function get_post_list( $request ) {
        // Get variables from the URL
        $present_page_array = $request->get_url_params();

        $this->page_number = (int) $present_page_array['page_number'];
        $this->post_type = sanitize_text_field( $present_page_array['post_type'] );

        global $wpdb;

        $query1 = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT COUNT(*) as count FROM {$wpdb->prefix}posts WHERE post_type = %s AND post_status = 'publish' AND post_content LIKE %s",
                $this->post_type,
                '%<img%'
            )
        );        
        $total_results = (int) $query1->count;

        $max_pages = ceil( $total_results / $this->post_per_page );
        $offset = ( $this->page_number - 1 ) * $this->post_per_page;

        $query = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT ID, post_content, post_title FROM {$wpdb->prefix}posts WHERE post_type = %s AND post_status = 'publish' AND post_content LIKE %s LIMIT %d OFFSET %d",
                $this->post_type,
                '%<img%',
                $this->post_per_page,
                $offset
            )
        );

        $this->post_list = array(
            'present_page' => (int) $this->page_number,
            'max_pages'    => $max_pages,
            'data'         => $this->prepare_image_data( $query ),
        );

        return $this->post_list;
    }

    /**
     * Prepare image data for the response.
     *
     * @param array $query Query results.
     * @return array Processed image data.
     */
    private function prepare_image_data( $query ) {
        $data = array();

        foreach ( $query as $post ) {
            $data[] = array(
                'id'     => (int) $post->ID,
                'title'  => $post->post_title,
                'images' => $this->extract_images_src( $post->post_content ),
            );
        }

        return $data;
    }

    /**
     * Extract image sources and alt attributes from post content.
     *
     * @param string $post_content The post content.
     * @return array Array of image data.
     */
    private function extract_images_src( $post_content ) {
        $doc = new DOMDocument();
        libxml_use_internal_errors( true );
        $doc->loadHTML( $post_content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
        libxml_clear_errors();
        
        $xml = simplexml_import_dom( $doc );
        if ( false === $xml ) {
            return array();
        }
        
        $images = $xml->xpath( '//img' );
        if ( false === $images ) {
            return array();
        }

        $matches = array();
        foreach ( $images as $index => $img ) {
            $matches[] = array(
                'src' => (string) $img['src'],
                'alt' => isset( $img['alt'] ) ? (string) $img['alt'] : '',
            );
        }

        return $matches;
    }

    /*
        Register root for update single post
    */
    // public function update_post_route(){
    //     register_rest_route( 
    //         'dvin508-seo/v1',
    //         '/update_post/',
    //         array(
    //             'methods' => 'POST',
    //             'callback' =>  array($this,'update_post'),
    //             'permission_callback' => array($this, 'check_permission')
    //         )
    //     );
    // }

    /**
     * Update post content with new image alt attributes
     *
     * @param WP_REST_Request $request The request object.
     * @return array Result array.
     */
    public function update_post( $request ) {
        $parameters_list = $request->get_params();

        foreach ( $parameters_list as $parameters ) {
            if ( ! isset( $parameters['id'] ) || ! is_numeric( $parameters['id'] ) ) {
                continue;
            }

            $update = array(
                'ID'           => (int) $parameters['id'],
                'post_content' => $this->content_merge( (int) $parameters['id'], $parameters['images'] ?? array() ),
            );

            wp_update_post( $update );
        }

        return array( 'result' => true );
    }

    /**
     * Merge content with updated image alt attributes.
     *
     * @param int   $post_id The post ID.
     * @param array $post_images Array of image data.
     * @return string Updated post content.
     */
    private function content_merge( $post_id, $post_images ) {
        $post_content = get_post_field( 'post_content', $post_id );

        $doc = new DOMDocument();
        libxml_use_internal_errors( true );
        $doc->loadHTML( $post_content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
        libxml_clear_errors();
        
        $xml = simplexml_import_dom( $doc );
        if ( false === $xml ) {
            return $post_content;
        }
        
        $images = $xml->xpath( '//img' );
        if ( false === $images ) {
            return $post_content;
        }

        foreach ( $images as $img ) {
            $img_alt = $this->get_alt_tag( (string) $img['src'], $post_images );
            $img['alt'] = $img_alt;
        }

        return $doc->saveHTML();
    }

    /**
     * Get corresponding alt tag for a given image src.
     *
     * @param string $img_src The image source URL.
     * @param array  $post_images Array of image data.
     * @return string Alt text for the image.
     */
    private function get_alt_tag( $img_src, $post_images ) {
        foreach ( $post_images as $image ) {
            if ( $image['src'] === $img_src ) {
                return sanitize_text_field( $image['alt'] ?? '' );
            }
        }
        return '';
    }

}

new Dvin508_Post_api();