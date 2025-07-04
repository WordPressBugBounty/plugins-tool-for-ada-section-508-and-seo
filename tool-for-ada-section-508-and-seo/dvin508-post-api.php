<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
//error_reporting(0);
/*
matching image with missing alt

SELECT post_content FROM `wp_posts` WHERE post_type in ('page','post') AND ( post_content REGEXP '^.*<img.*(alt).*(=).*\".*\".*>.*$' )

*/

class Dvin508_Post_api{

    public int $page_number;
    public string $post_type;
    public int $post_per_page = 25;
    public $post_list = [];

    public function __construct(){
        add_action( 'rest_api_init', [$this,'post_route'] );
        // add_action( 'rest_api_init', [$this,'update_post_route'] );
    }

    /**
     * Register custom REST API routes.
     */
    public function post_route(){
        register_rest_route('dvin508-seo/v1', '/post_type/(?P<post_type>[a-z]+)/(?P<page_number>\d+)', [
            'methods'  => 'GET',
            'callback' => [$this, 'get_post_list'],
            'permission_callback' => [$this, 'check_permission']
        ]);

        register_rest_route('dvin508-seo/v1', '/update_post/', [
            'methods'  => 'POST',
            'callback' => [$this, 'update_post'],
            'permission_callback' => [$this, 'check_permission']
        ]);
    }

    public function check_permission($request){
        return current_user_can('manage_options'); 
    }

    /**
     * Get list of posts with image data.
     */
    public function get_post_list( $request ){
        /* getting the varibles from the url */
        $present_page_array = $request->get_url_params();

        $this->page_number = $present_page_array['page_number'];
        $this->post_type = $present_page_array['post_type'];

        global $wpdb;

        $query1 = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT COUNT(*) as count FROM {$wpdb->prefix}posts WHERE post_type = %s AND post_status = 'publish' AND post_content LIKE %s",
                $this->post_type,
                '%<img%'
            )
        );        
        $total_results = ($query1->count);

        $max_pages = ceil($total_results / $this->post_per_page);

        $offset = ($this->page_number - 1) * $this->post_per_page;

        $query =  $wpdb->get_results('Select ID, post_content, post_title from '.$wpdb->prefix.'posts where post_type = "'.$this->post_type.'" AND post_status = "publish" AND post_content LIKE "%<img%>%" Limit '.$this->post_per_page.' Offset '.$offset);

        $query = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT ID, post_content, post_title FROM {$wpdb->prefix}posts WHERE post_type = %s AND post_status = 'publish' AND post_content LIKE %s LIMIT %d OFFSET %d",
                $this->post_type,
                '%<img%',
                $this->post_per_page,
                $offset
            )
        );

        $this->post_list['present_page'] = (int)$this->page_number;

        $this->post_list['max_pages'] = $max_pages;

        $this->post_list['data'] = $this->prepare_image_data($query);

        return $this->post_list;
    }

    /**
     * Prepare image data for the response.
     */
    private function prepare_image_data($query) {
        $data = [];

        foreach ($query as $post) {
            $data[] = [
                'id'     => $post->ID,
                'title'  => $post->post_title,
                'images' => $this->extract_images_src($post->post_content)
            ];
        }

        return $data;
    }

    /**
     * Extract image sources and alt attributes from post content.
     */
    private function extract_images_src($post_content) {
        $doc = new DOMDocument();
        @$doc->loadHTML($post_content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $xml = simplexml_import_dom($doc);
        $images = $xml->xpath('//img');

        $matches = [];
        foreach ($images as $index => $img) {
            $matches[] = [
                'src' => (string) $img['src'],
                'alt' => isset($img['alt']) ? (string) $img['alt'] : ''
            ];
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

    /*
        Passing all the media detial at once and it will be updated
    */
    public function update_post( $request ){
        $parameters_list = $request_data->get_params();

        foreach ($parameters_list as $parameters) {
            $update = [
                'ID'           => (int) $parameters['id'],
                'post_content' => $this->content_merge((int) $parameters['id'], $parameters['images'])
            ];

            wp_update_post($update);
        }

        return ['result' => true];
    }

    /**
     * Merge content with updated image alt attributes.
     */
    private function content_merge($post_id, $post_images){
        /* step 1: use reg expression to match image based on image url
            step 2: extract that complete image and put it in Doomparser
            step  3: add alt to image using doomparser
            step 4:get new image from doomparser
            step 5: match the image again in content and replace that image with our
                    new doomparsed image 
            step 6 repeat the whole step for next image
        */
        
        $post_content = get_post_field('post_content', $post_id);

        $doc = new DOMDocument();
        @$doc->loadHTML($post_content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $xml = simplexml_import_dom($doc);
        $images = $xml->xpath('//img');

        foreach ($images as $img) {
            $img_alt = $this->get_alt_tag((string) $img['src'], $post_images);
            $img['alt'] = $img_alt;
        }

        return $doc->saveHTML();
    }

    /**
     * Get corresponding alt tag for a given image src.
     */
    private function get_alt_tag($img_src, $post_images){
        foreach($post_images as $image){
            if($image['src'] === $img_src){
                return $image['alt'];
            }
        }
        return '';
    }

}

new Dvin508_Post_api();