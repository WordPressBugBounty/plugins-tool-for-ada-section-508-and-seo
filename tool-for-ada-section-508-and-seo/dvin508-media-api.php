<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Dvin508_Media_api{

    private $page_number;
    private $media_type;
    private $media_per_page = 25;
    private $media_list = [];
    private $media_size = [80,80];

    public function __construct(){
        add_action( 'rest_api_init', [$this,'media_route'] );
    }

    /* 
        Getting different types of media 
        there are 5 types of media query
        1)  all - show all the mdia
        2)  caption - show media missing caption
        3)  alt - show media missing alt
        4)  content - show meida missing alt

        Other variable are 
        1)  page_number
        2)  per_page

        Register API root
    */
    public function media_route(){
        register_rest_route( 'dvin508-seo/v1', '/media/missing/(?P<media_type>[a-z]+)/(?P<page_number>\d+)', [
            'methods' => 'GET',
            'callback' =>  [$this,'get_media_type'],
            'permission_callback' => [$this, 'check_permission']
        ]);

        register_rest_route('dvin508-seo/v1', '/update_media/', [
            'methods' => 'POST',
            'callback' => [$this, 'update_media'],
            'permission_callback' => [$this, 'check_permission']
        ]);
    }

    public function check_permission($request){
        return is_super_admin();
    }

    public function get_media_type( $request ){
        /* getting the varibles from the url */
        $params = $request->get_url_params();

        $this->page_number = (int) $params['page_number'];
        $this->media_type = $params['media_type'];
        /* end */

        // Choose appropriate media retrieval method
        switch($this->media_type){
            case 'caption':
                $this->get_missing_caption_or_content('excerpt');
                break;
            case 'alt':
                $this->get_missing_alt_media();
                break;
            case 'content':
                $this->get_missing_caption_or_content('content');
                break;
            default:
                $this->get_all_media();
        }
        
        return $this->media_list;
    }

    /*
        Gell all the media in the site 
    */
    private function get_all_media(){
        $query = new WP_Query([
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'post_status'    => 'any',
            'posts_per_page' => $this->media_per_page,
            'paged'          => $this->page_number
        ]);

        $this->media_list = [
            'present_page' => $this->page_number,
            'data'         => [],
            'max_pages'    => $query->max_num_pages
        ];

        foreach ($query->get_posts() as $media) {
            $this->media_list['data'][] = $this->format_media($media);
        }
    }

    /*
        This is used for getting missing caption and content both    
        that is controlled my input $missing -> 'excerpt' or 'content'
    */
    private function get_missing_caption_or_content($missing){

        global $wpdb;

        $total_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}posts WHERE post_type = 'attachment' AND post_mime_type LIKE 'image/%%' AND post_meta_key = %s",
            $missing
        ));

        $this->fetch_media_with_offset("post_{$missing} = ''", $total_count);
    }

    private function get_missing_alt_media(){

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

        $total_count = (int) $wpdb->get_var($query);
        $this->fetch_media_with_offset("
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
        ", $total_count);
    }

    /**
     * Retrieves media with an offset
     */
    private function fetch_media_with_offset($condition, $total_count) {
        global $wpdb;

        $max_pages = ceil($total_count / $this->media_per_page);
        $offset = ($this->page_number - 1) * $this->media_per_page;
        
        $base_sql = "SELECT * FROM {$wpdb->prefix}posts WHERE post_type = %s";
        $args = ['attachment'];

        if (!empty($some_filter)) {
            $base_sql .= " AND post_status = %s";
            $args[] = $some_filter;
        }

        $base_sql .= " LIMIT %d OFFSET %d";
        $args[] = $this->media_per_page;
        $args[] = $offset;

        $query = $wpdb->get_results($wpdb->prepare($base_sql, ...$args));


        $this->media_list = [
            'present_page' => $this->page_number,
            'data'         => array_map([$this, 'format_media'], $query),
            'max_pages'    => $max_pages
        ];
    }

    /**
     * Formats media data for output
     */
    private function format_media($media) {
        return [
            'id'          => $media->ID,
            'caption'     => $media->post_excerpt,
            'description' => $media->post_content,
            'image'       => wp_get_attachment_image($media->ID, $this->media_size),
            'alt'         => get_post_meta($media->ID, '_wp_attachment_image_alt', true)
        ];
    }

    /*
        Passing all the media detial at once and it will be updated
    */
    public function update_media( $request ){
        $media_items = $request->get_params();

        foreach($media_items as $media){
        $update = array(
            'ID'		    => $media['id'],
			'post_excerpt'  => sanitize_text_field($media['caption']),
            'post_content'  => sanitize_textarea_field($media['description'])	
        );
            update_post_meta($media['id'], '_wp_attachment_image_alt', sanitize_text_field($media['alt']));
        }
        return ['result' => true];
    }
    
}

new Dvin508_Media_api();