<?php

/**********************************************************************************************************************************
*
* Ajax Auto Complete
* 
* Author: Webbu Design
*
***********************************************************************************************************************************/


	add_action( 'PF_AJAX_HANDLER_pfget_autocomplete', 'pf_ajax_autocomplete' );
	add_action( 'PF_AJAX_HANDLER_nopriv_pfget_autocomplete', 'pf_ajax_autocomplete' );
	
	
function pf_ajax_autocomplete(){
	//Security
	check_ajax_referer( 'pfget_autocomplete', 'security' );
	header('Content-Type: application/javascript; charset=UTF-8;');
	

	if(isset($_GET['lang']) && $_GET['lang']!=''){
        $pflang = esc_attr($_GET['lang']);
    }

	if(function_exists('icl_t')) {
        if (!empty($pflang)) {
            do_action( 'wpml_switch_language', $pflang );
        }
    }

	//Get form type 
	if(isset($_GET['ftype']) && $_GET['ftype']!=''){
		$ftype = sanitize_text_field($_GET['ftype']);
	}

	//Get search key
	if(isset($_GET['q']) && $_GET['q']!=''){
		$searchword = sanitize_text_field($_GET['q']);
	}

	//Get search key
	if(isset($_GET['callback']) && $_GET['callback']!=''){
		$callback = sanitize_text_field($_GET['callback']);
	}

	$tax_query = false;

	/* Get admin values */
	$setup3_pointposttype_pt1 = PFSAIssetControl('setup3_pointposttype_pt1','','pfitemfinder');
	$args = array( 'post_type' => $setup3_pointposttype_pt1, 'post_status' => 'publish','posts_per_page' => 5);

	if ($ftype == 'title') {
		$args['orderby'] = 'title';
		$args['order'] = 'ASC';
		$args['search_prod_title'] = $searchword;

	}elseif ($ftype == 'description') {
		$args['orderby'] = 'title';
		$args['order'] = 'ASC';
		$args['search_prod_desc'] = $searchword;

	}elseif ($ftype == 'title_description') {
		$args['orderby'] = 'title';
		$args['order'] = 'ASC';
		$args['search_prod_desc_title'] = $searchword;

	}elseif ($ftype == 'address') {
		$pfcomptype = 'CHAR';

		if(isset($args['meta_query']) == false || isset($args['meta_query']) == NULL){
			$args['meta_query'] = array();
		}
										
		
		$args['meta_query'] = array(
			'relation' => 'AND',
			array(
				'key' => 'webbupointfinder_items_address',
				'value' => $searchword,
				'compare' => 'LIKE',
				'type' => $pfcomptype
			)
		);	
	}elseif (in_array($ftype, array('post_tags','pointfinderltypes','pointfinderitypes','pointfinderlocations','pointfinderfeatures'))) {
		$tax_query = true;			
	}else{

		$pfcomptype = 'CHAR';

		if(isset($args['meta_query']) == false || isset($args['meta_query']) == NULL){
			$args['meta_query'] = array();
		}
										
		
		$args['meta_query'] = array(
			'relation' => 'AND',
			array(
				'key' => 'webbupointfinder_item_'.$ftype,
				'value' => $searchword,
				'compare' => 'LIKE',
				'type' => $pfcomptype
			)
		);	

	}

	$output_arr = array();

	if ($tax_query) {

		if ($ftype == 'post_tags') {
			$args = array(
			    'orderby'           => 'name', 
			    'order'             => 'ASC',
			    'hide_empty'        => false, 
			    'exclude'           => array(), 
			    'exclude_tree'      => array(), 
			    'include'           => array(),
			    'number'            => '', 
			    'fields'            => 'all', 
			    'slug'              => '',
			    'parent'            => '',
			    'hierarchical'      => true, 
			    'child_of'          => 0, 
			    'get'               => '', 
			    'name__like'        => '',
			    'description__like' => '',
			    'pad_counts'        => false, 
			    'offset'            => '', 
			    'search'            => $searchword, 
			); 

			$terms = get_tags($args);
			foreach ($terms as $term_val) {
				$output_arr[] = urldecode($term_val->slug);
			}
			
		}else{
			$args = array(
			    'orderby'           => 'name', 
			    'order'             => 'ASC',
			    'hide_empty'        => false, 
			    'exclude'           => array(), 
			    'exclude_tree'      => array(), 
			    'include'           => array(),
			    'number'            => '', 
			    'fields'            => 'all', 
			    'slug'              => '',
			    'parent'            => '',
			    'hierarchical'      => true, 
			    'child_of'          => 0, 
			    'get'               => '', 
			    'name__like'        => '',
			    'description__like' => '',
			    'pad_counts'        => false, 
			    'offset'            => '', 
			    'search'            => $searchword, 
			    'cache_domain'      => 'core'
			); 

			$terms = get_terms($ftype, $args);
			foreach ($terms as $term_val) {
				$output_arr[] = urldecode($term_val->name);
			}
		}
	}else{
		$the_query = new WP_Query( $args );
	
		if ( $the_query->have_posts() ) :
			
			while ( $the_query->have_posts() ) : $the_query->the_post();
				if ($ftype == 'title' || $ftype == 'description' || $ftype == 'title_description') {
					$output_arr[] = html_entity_decode(get_the_title());
				}else if($ftype == 'address'){
					$output_arr[] = html_entity_decode(get_post_meta(get_the_id(),'webbupointfinder_items_address',true ));
				}else{
					$output_arr[] = html_entity_decode(get_post_meta(get_the_id(),'webbupointfinder_item_'.$ftype,true ));
				}
		 	endwhile;

		 	wp_reset_postdata();
			
		endif;
	}

	echo $callback.'('.json_encode($output_arr).');';
		
	die();
}

?>