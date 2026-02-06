<?php
/**
 * Media API for Accessibility Tools Plugin
 *
 * @package Accessibility_Tools_Alt_Text_Finder
 * @since 3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Dvin508_Media_api {

    /**
     * Current page number
     *
     * @var int
     */
    private $page_number;

    /**
     * Media type filter
     *
     * @var string
     */
    private $media_type;

    /**
     * Number of media items per page
     *
     * @var int
     */
    private $media_per_page = 25;

    /**
     * Media list array
     *
     * @var array
     */
    private $media_list = array();

    /**
     * Media size for thumbnails
     *
     * @var array
     */
    private $media_size = array( 80, 80 );

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'rest_api_init', array( $this, 'media_route' ) );
    }

    /**
     * Register REST API routes for media operations
     *
     * Getting different types of media:
     * 1) all - show all the media
     * 2) caption - show media missing caption
     * 3) alt - show media missing alt
     * 4) content - show media missing content
     *
     * Other variables:
     * 1) page_number
     * 2) per_page
     */
    public function media_route() {
        register_rest_route( 'dvin508-seo/v1', '/media/missing/(?P<media_type>[a-z]+)/(?P<page_number>\d+)', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'get_media_type' ),
            'permission_callback' => array( $this, 'check_permission' ),
            'args'                => array(
                'media_type'  => array(
                    'required'          => true,
                    'validate_callback' => function( $param, $request, $key ) {
                        return in_array( $param, array( 'all', 'caption', 'alt', 'content' ), true );
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

        register_rest_route( 'dvin508-seo/v1', '/update_media/', array(
            'methods'             => 'POST',
            'callback'            => array( $this, 'update_media' ),
            'permission_callback' => array( $this, 'check_permission' ),
        ) );
    }

    /**
     * Check if user has permission to access media API
     *
     * @param WP_REST_Request $request The request object.
     * @return bool
     */
    public function check_permission( $request ) {
        return current_user_can( 'manage_options' );
    }

    /**
     * Get media based on type and page number
     *
     * @param WP_REST_Request $request The request object.
     * @return array Media list
     */
    public function get_media_type( $request ) {
        // Get variables from the URL
        $params = $request->get_url_params();

        $this->page_number = (int) $params['page_number'];
        $this->media_type = sanitize_text_field( $params['media_type'] );

        // Choose appropriate media retrieval method
        switch ( $this->media_type ) {
            case 'caption':
                $this->get_missing_caption_or_content( 'excerpt' );
                break;
            case 'alt':
                $this->get_missing_alt_media();
                break;
            case 'content':
                $this->get_missing_caption_or_content( 'content' );
                break;
            default:
                $this->get_all_media();
        }
        
        return $this->media_list;
    }

    /**
     * Get all media in the site
     */
    private function get_all_media() {
        $query = new WP_Query( array(
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'post_status'    => 'any',
            'posts_per_page' => $this->media_per_page,
            'paged'          => $this->page_number,
        ) );

        $this->media_list = array(
            'present_page' => $this->page_number,
            'data'         => array(),
            'max_pages'    => $query->max_num_pages,
        );

        foreach ( $query->get_posts() as $media ) {
            $this->media_list['data'][] = $this->format_media( $media );
        }
    }

    /**
     * Get media missing caption or content
     * 
     * This is used for getting missing caption and content both
     * that is controlled by input $missing -> 'excerpt' or 'content'
     *
     * @param string $missing The field to check (excerpt or content).
     */
    private function get_missing_caption_or_content( $missing ) {
        global $wpdb;

        $total_count = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}posts WHERE post_type = 'attachment' AND post_mime_type LIKE 'image/%%' AND post_{$missing} = ''",
            $missing
        ) );

        $this->fetch_media_with_offset( "post_{$missing} = ''", $total_count );
    }

    /**
     * Get media missing alt text
     */
    private function get_missing_alt_media() {
        global $wpdb;

        $query = "
            SELECT COUNT(*) FROM (
                SELECT post_id FROM {$wpdb->prefix}postmeta 
                WHERE post_id IN (
                    SELECT ID FROM {$wpdb->prefix}posts 
                    WHERE post_type = 'attachment' AND post_mime_type LIKE 'image/%%'
                ) 
                AND meta_key = '_wp_attachment_image_alt' AND meta_value = ''
                UNION
                SELECT ID FROM {$wpdb->prefix}posts 
                WHERE post_type = 'attachment' AND post_mime_type LIKE 'image/%%' 
                AND ID NOT IN (
                    SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '_wp_attachment_image_alt'
                )
            ) AS missing_alt
        ";

        $total_count = (int) $wpdb->get_var( $query );
        $this->fetch_media_with_offset( "
            ID IN (
                SELECT post_id FROM (
                    SELECT post_id FROM {$wpdb->prefix}postmeta 
                    WHERE post_id IN (
                        SELECT ID FROM {$wpdb->prefix}posts 
                        WHERE post_type = 'attachment' AND post_mime_type LIKE 'image/%%'
                    ) 
                    AND meta_key = '_wp_attachment_image_alt' AND meta_value = ''
                    UNION
                    SELECT ID FROM {$wpdb->prefix}posts 
                    WHERE post_type = 'attachment' AND post_mime_type LIKE 'image/%%' 
                    AND ID NOT IN (
                        SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '_wp_attachment_image_alt'
                    )
                ) AS missing_alt
            )
        ", $total_count );
    }

    /**
     * Retrieves media with an offset
     *
     * @param string $condition The WHERE condition.
     * @param int    $total_count Total number of records.
     */
    private function fetch_media_with_offset( $condition, $total_count ) {
        global $wpdb;

        $max_pages = ceil( $total_count / $this->media_per_page );
        $offset = ( $this->page_number - 1 ) * $this->media_per_page;
        
        $base_sql = "SELECT * FROM {$wpdb->prefix}posts WHERE post_type = %s AND post_mime_type LIKE 'image/%%'";
        $args = array( 'attachment' );

        if ( ! empty( $condition ) ) {
            $base_sql .= " AND {$condition}";
        }

        $base_sql .= " LIMIT %d OFFSET %d";
        $args[] = $this->media_per_page;
        $args[] = $offset;

        $query = $wpdb->get_results( $wpdb->prepare( $base_sql, $args ) );

        $this->media_list = array(
            'present_page' => $this->page_number,
            'data'         => array_map( array( $this, 'format_media' ), $query ),
            'max_pages'    => $max_pages,
        );
    }

    /**
     * Formats media data for output
     *
     * @param object $media The media object.
     * @return array Formatted media data.
     */
    private function format_media( $media ) {
        return array(
            'id'          => (int) $media->ID,
            'caption'     => $media->post_excerpt,
            'description' => $media->post_content,
            'image'       => wp_get_attachment_image( $media->ID, $this->media_size ),
            'alt'         => get_post_meta( $media->ID, '_wp_attachment_image_alt', true ),
        );
    }

    /**
     * Update media details
     * 
     * Passing all the media details at once and it will be updated
     *
     * @param WP_REST_Request $request The request object.
     * @return array Result array.
     */
    public function update_media( $request ) {
        $media_items = $request->get_params();

        foreach ( $media_items as $media ) {
            if ( ! isset( $media['id'] ) || ! is_numeric( $media['id'] ) ) {
                continue;
            }

            $update = array(
                'ID'           => (int) $media['id'],
                'post_excerpt' => sanitize_text_field( $media['caption'] ?? '' ),
                'post_content' => sanitize_textarea_field( $media['description'] ?? '' ),
            );
            
            wp_update_post( $update );
            update_post_meta( (int) $media['id'], '_wp_attachment_image_alt', sanitize_text_field( $media['alt'] ?? '' ) );
        }
        
        return array( 'result' => true );
    }
    
}

new Dvin508_Media_api();