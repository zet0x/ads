<?php
/**********************************************************************************************************************************
*
* GEO filter
* 
* Author: Webbu Design
*
***********************************************************************************************************************************/
if (!class_exists('Pointfinder_WP_GeoQuery')) {

    class Pointfinder_WP_GeoQuery extends WP_Query {

        /**
         * Constructor - adds necessary filters to extend Query hooks
         */
        public function __construct($args = array()) {
            global $wpdb;
            
            $this->_sw = $this->_sw2 = $this->_ne = $this->_ne2 = '';

            if(!empty($args['pf_sw'])){$this->_sw = $wpdb->esc_like($args['pf_sw']);}
            if(!empty($args['pf_sw2'])){$this->_sw2 = $wpdb->esc_like($args['pf_sw2']);}
            if(!empty($args['pf_ne'])){$this->_ne = $wpdb->esc_like($args['pf_ne']);}
            if(!empty($args['pf_ne2'])){$this->_ne2 = $wpdb->esc_like($args['pf_ne2']);}

            if(!empty($args['pf_sw'])){
                add_filter('posts_where', array(&$this, 'posts_where'), 10, 1);
                add_filter('posts_join', array(&$this, 'posts_join'), 10, 2);
            }
            parent::query($args);

        }



        public function posts_join($join, $query) {
            global $wpdb;

            $join .= " INNER JOIN ".$wpdb->postmeta." AS latitude ON ".$wpdb->posts.".ID = latitude.post_id ";
            $join .= " INNER JOIN ".$wpdb->postmeta." AS longitude ON ".$wpdb->posts.".ID = longitude.post_id ";

            return $join;
        }

        /**
         * Adds where clauses to compliment joins
         */
        public function posts_where($where) {
            if ( !empty($this->_sw) ) {
                $where .= "AND (latitude.meta_key='webbupointfinder_items_location' AND SUBSTRING_INDEX(latitude.meta_value,',',1) BETWEEN $this->_sw AND $this->_ne )";
                 $where .= "AND (longitude.meta_key='webbupointfinder_items_location' AND SUBSTRING_INDEX(longitude.meta_value,',',-1) BETWEEN $this->_sw2 AND $this->_ne2)";
            }
            return $where;
        }


    }

}