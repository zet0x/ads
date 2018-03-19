<?php
/**********************************************************************************************************************************
*
* Ajax Advert On/Off
* 
* Author: Webbu Design
*
***********************************************************************************************************************************/


add_action( 'PF_AJAX_HANDLER_pfget_onoffsystem', 'pf_ajax_onoffsystem' );
add_action( 'PF_AJAX_HANDLER_nopriv_pfget_onoffsystem', 'pf_ajax_onoffsystem' );

function pf_ajax_onoffsystem(){
  	check_ajax_referer( 'pfget_onoffsystem', 'security');
	header('Content-Type: application/json; charset=UTF-8;');

	$itemid = $current_status = $result = '';

	if(isset($_POST['itemid']) && $_POST['itemid']!=''){
		$itemid = esc_attr($_POST['itemid']);
	}

	$lang_c = '';
	if(isset($_POST['lang']) && $_POST['lang']!=''){
		$lang_c = sanitize_text_field($_POST['lang']);
	}


	if(function_exists('icl_t')) {
		if (!empty($lang_c)) {
			do_action( 'wpml_switch_language', $lang_c );
		}
	}


	if (!empty($itemid)) {
		global $current_user;
	    $user_id = $current_user->ID;

	    if (!empty($user_id)) {

	    	/*Check if item user s item*/
            global $wpdb;
            $setup3_pointposttype_pt1 = PFSAIssetControl('setup3_pointposttype_pt1','','pfitemfinder');
            $result = $wpdb->get_results( $wpdb->prepare( 
              "SELECT ID, post_author FROM $wpdb->posts WHERE ID = %s and post_author = %s and post_type = %s", 
              $itemid,
              $user_id,
              $setup3_pointposttype_pt1
            ) );
            
            if (is_array($result) && count($result)>0) {  
              if ($result[0]->ID == $itemid) {

		    	$post_status = get_post_status($itemid);
		    	global $wpdb;

		    	if ($post_status == 'pfonoff') {
		    		

		    		$old_post_status = get_post_meta($itemid, 'pointfinder_item_onoffstatus_old',true);

		    		if (empty($old_post_status)) {$old_post_status = "pendingapproval";}

		    		$wpdb->UPDATE($wpdb->posts,array('post_status' => $old_post_status),array('ID' => $itemid));

		    		delete_post_meta($itemid, 'pointfinder_item_onoffstatus_old');

		    		$result = 0;

		    	}else{

		    		update_post_meta($itemid, 'pointfinder_item_onoffstatus_old', $post_status);

		    		$wpdb->UPDATE($wpdb->posts,array('post_status' => 'pfonoff'),array('ID' => $itemid));

		    		$result = 1;
		    	}
		     }
			}

	    }
	}

	echo json_encode($result);
die();
}

?>