<?php

/**********************************************************************************************************************************
*
* Common functions for pf system
* 
* Author: Webbu Design
***********************************************************************************************************************************/

if (!function_exists('pointfinder_pfstring2AdvArray')) {
	function pointfinder_pfstring2AdvArray($results,$keyname, $kv = ',',$uearr_count) {
		$user_ids = '';
		if (!empty($results) && is_array($results)) {
			$uek = 1;
			foreach ($results as $result) {
				if (isset($result[$keyname])) {
					$user_ids .= $result[$keyname];
					if ($uek != $uearr_count) {$user_ids .= ',';}
				}
			$uek++;
			}
		}
		return $user_ids;
	} 
}

if (!function_exists('pointfinder_agentitemcount_calc')) {
	function pointfinder_agentitemcount_calc($agent_id, $setup3_pointposttype_pt1,$request_type){

		global $wpdb;


		/* Find; Post ID's which defines as agent */
			$adpi_agentresults = $wpdb->get_results(
				$wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key like %s AND meta_value = %d",'webbupointfinder_item_agents',$agent_id),'ARRAY_A');

			$adpi_agentcount = count($adpi_agentresults);
			$adpi_agentresults = pointfinder_pfstring2AdvArray($adpi_agentresults,'post_id',',',$adpi_agentcount); 
		
		/* Find; User IDS which linked with agent. */
			$adpi_userresults = $wpdb->get_results($wpdb->prepare("SELECT user_id FROM $wpdb->usermeta where meta_key like %s and meta_value = %d",'user_agent_link',$agent_id),'ARRAY_A');

			$adpi_usercount = count($adpi_userresults);
			$adpi_userresults = pointfinder_pfstring2AdvArray($adpi_userresults,'user_id',',',$adpi_usercount);

		/* Find; posts which belongs to this agent */

			
			$adpi_totalfromposts = $wpdb->get_results(
				$wpdb->prepare("SELECT $wpdb->posts.ID FROM 
					
					$wpdb->posts 

					WHERE $wpdb->posts.post_type = %s AND $wpdb->posts.post_status = %s AND $wpdb->posts.post_author IN (%s) AND $wpdb->posts.ID NOT IN(%s)

					group by $wpdb->posts.ID",

					$setup3_pointposttype_pt1,
					'publish',
					$adpi_userresults,
					$adpi_agentresults
				),'ARRAY_A'
			);

			$adpi_totalfrompostscount = count($adpi_totalfromposts);
			$return_ids = '';

			if ($request_type == 'count') {
				$return_array = array(
					'count'=> $adpi_totalfrompostscount + $adpi_agentcount,
					'ids' => $return_ids
				);

			}else{
				if ($adpi_totalfrompostscount > 0) {
					$return_ids = pointfinder_pfstring2AdvArray($adpi_totalfromposts,'ID',',',$adpi_totalfrompostscount);;
				}

				if ($adpi_agentcount > 0) {
					if ($adpi_totalfrompostscount > 0) {
						$return_ids .= ','.$adpi_agentresults;
					}else{
						$return_ids = $adpi_agentresults;
					}
				}

				$return_array = array(
					'count'=> $adpi_totalfrompostscount + $adpi_agentcount,
					'ids' => $return_ids
					);
			}
			

		return $return_array;
	}
}

/**
*Start: Attachment function
**/
	if (!function_exists('pft_insert_attachment')) {
		function pft_insert_attachment($file_handler,$setthumb='false') {
			if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) __return_false();

			require_once(ABSPATH . "wp-admin" . '/includes/image.php');
			require_once(ABSPATH . "wp-admin" . '/includes/file.php');
			require_once(ABSPATH . "wp-admin" . '/includes/media.php');

			$attach_id = media_handle_upload( $file_handler, 0);

			return $attach_id;
		}
	}	
/**
*End: Attachment function
**/



/**
*Start:General Functions
**/
	if (!function_exists('pointfinder_find_requestedfields')) {
		function pointfinder_find_requestedfields($fieldname){

			global $pfsearchfields_options;

			$keyname = array_search($fieldname,$pfsearchfields_options);

			if($keyname != ''){ 
				$keynameexp = explode('_',$keyname);
				if(array_search('posttax', $keynameexp)){
					$keycount = count($keynameexp);
					
					if ($keycount >= 2) {
						$keycountx = $keycount - 1;
						unset($keynameexp[0]);
						unset($keynameexp[$keycountx]);
					}else{
						unset($keynameexp[0]);
					}
					
					if (count($keynameexp) > 1) {
						$new_keyname_exp = '';
						$ik = 0;
						$il = count($keynameexp)-1;

						foreach ($keynameexp as $kvalue) {
							if ($ik < $il) {
								$new_keyname_exp .= $kvalue.'_';
							}else{
								$new_keyname_exp .= $kvalue;
							}
							$ik = $ik+1;
						}
						
						return $new_keyname_exp;
					}else{
						foreach ($keynameexp as $keynvalue) {
							return $keynvalue;
						}
						
					}
				}else{
					return '';
				}
				
			}else{
				return '';
			}
		}
	}
	if (!function_exists('pf_redirect')) {
		function pf_redirect($url){ 
		    if (!headers_sent()){  
		        header('Location: '.$url); exit; 
		    }else{ 
		        echo '<script type="text/javascript">'; 
		        echo 'window.location.href="'.$url.'";'; 
		        echo '</script>'; 
		        echo '<noscript>'; 
		        echo '<meta http-equiv="refresh" content="0;url='.$url.'" />'; 
		        echo '</noscript>'; exit; 
		    } 
		} 
	} 
	if (!function_exists('pointfinder_post_exists')) {
		function pointfinder_post_exists( $id ) {return is_string( get_post_status( $id ) );}
	}
	if (!function_exists('pfcalculatefavs')) {
		function pfcalculatefavs($user_id){
			
			$user_favorites_arr = get_user_meta( $user_id, 'user_favorites', true );
			
			$latest_fav_count = $new_favorite_count = $favorite_count = 0;
			
			if (!empty($user_favorites_arr)) {
				$user_favorites_arr = json_decode($user_favorites_arr,true);
				$favorite_count = count($user_favorites_arr);
				
				if (!empty($user_favorites_arr)) {
					foreach ($user_favorites_arr as $user_favorites_arr_single) {
						if(pointfinder_post_exists($user_favorites_arr_single)){
							$new_user_fav_arr[] = $user_favorites_arr_single;
						}
					}
				}else{
					$new_user_fav_arr = array();
				}
				

				$new_favorite_count = (!empty($new_user_fav_arr))? count($new_user_fav_arr):0;

				if ($favorite_count !== $new_favorite_count) {
					if (isset($new_user_fav_arr)) {
						update_user_meta($user_id,'user_favorites',json_encode($new_user_fav_arr));
						$latest_fav_count = $new_favorite_count;
					}
					
				}else{
					$latest_fav_count = $favorite_count;
				}
			}

			return $latest_fav_count;
		}
	}
	if (!function_exists('pfloadingfunc')) {
		function pfloadingfunc($message,$type = 'show'){
			echo "<script type='text/javascript'>(function($) {'use strict';";
			if($type == 'show'){
				echo '$(function(){';
					echo '$(".pftsrwcontainer-overlay").pfLoadingOverlayex({action:"show","message":"'.$message.'"});';
				echo '});';
			}else{
				echo "$(function(){setTimeout(function() {";
				echo "$('.pftsrwcontainer-overlay').pfLoadingOverlayex({action:'hide'});";
				echo "}, 1000);});";
			}
			echo "})(jQuery);</script>";
		}
	}
	if (!function_exists('pfsocialtoicon')) {
		function pfsocialtoicon($name){
			switch ($name) {
				case 'facebook':
					return 'pfadmicon-glyph-770';
					break;
				
				case 'pinterest':
					return 'pfadmicon-glyph-810';
					break;

				case 'twitter':
					return 'pfadmicon-glyph-769';
					break;

				case 'linkedin':
					return 'pfadmicon-glyph-824';
					break;

				case 'google-plus':
					return 'pfxicon-google';
					break;

				case 'dribbble':
					return 'pfadmicon-glyph-969';
					break;

				case 'dropbox':
					return 'pfadmicon-glyph-952';
					break;

				case 'flickr':
					return 'pfadmicon-glyph-955';
					break;

				case 'github':
					return 'pfadmicon-glyph-871';
					break;

				case 'instagram':
					return 'pfxicon-instagram';
					break;

				case 'skype':
					return 'pfadmicon-glyph-970';
					break;

				case 'rss':
					return 'pfadmicon-glyph-914';
					break;

				case 'tumblr':
					return 'pfadmicon-glyph-959';
					break;

				case 'vk':
					return 'pfadmicon-glyph-980';
					break;

				case 'youtube':
					return 'pfadmicon-glyph-947';
					break;
			}
		}
	}
	
	if (!function_exists('PFSAIssetControl')) {
		/* Main Option Fields */
		function PFSAIssetControl($field, $field2 = '', $default = '',$icl_exit = 0){
			global $pointfindertheme_option;

			if (function_exists('icl_t')) {
				$pointfindertheme_option = get_option('pointfindertheme_options');
			}
			
			if($field2 == ''){
				if(isset($pointfindertheme_option[''.$field.'']) == false || $pointfindertheme_option[''.$field.''] == ""){
					$output = $default;
					
				}else{
					$output = $pointfindertheme_option[''.$field.''];
					
				}
			}else{
				if(isset($pointfindertheme_option[''.$field.''][''.$field2.'']) == false || $pointfindertheme_option[''.$field.''][''.$field2.''] == ""){
					$output = $default;
					
				}else{
					$output = $pointfindertheme_option[''.$field.''][''.$field2.''];
					
				}
			};
			return $output;
		}
	}
	if (!function_exists('PFCFIssetControl')) {
		/* Custom Fields */
		function PFCFIssetControl($field, $field2 = '', $default = '',$icl_exit = 0){
			if (function_exists('icl_t')) {
				$pfcustomfields_options = get_option('pfcustomfields_options');
			}else{
				global $pfcustomfields_options;
			}

			if($field2 == ''){
				if(isset($pfcustomfields_options[''.$field.'']) == false || $pfcustomfields_options[''.$field.''] == ""){
					$output = $default;
					
				}else{
					$output = $pfcustomfields_options[''.$field.''];
					
				}
			}else{
				if(isset($pfcustomfields_options[''.$field.''][''.$field2.'']) == false || $pfcustomfields_options[''.$field.''][''.$field2.''] == ""){
					$output = $default;
					
				}else{
					$output = $pfcustomfields_options[''.$field.''][''.$field2.''];
					
				}
			};
			return $output;
		}
	}
	if (!function_exists('PFSFIssetControl')) {
		/* Search Fields */
		function PFSFIssetControl($field, $field2 = '', $default = '',$icl_exit = 0){
			if (function_exists('icl_t')) {
				$pfsearchfields_options = get_option('pfsearchfields_options');
			}else{
				global $pfsearchfields_options;
			}

			if($field2 == ''){
				if(isset($pfsearchfields_options[''.$field.'']) == false || $pfsearchfields_options[''.$field.''] == ""){
					$output = $default;
					
				}else{
					$output = $pfsearchfields_options[''.$field.''];
					
				}
			}else{
				if(isset($pfsearchfields_options[''.$field.''][''.$field2.'']) == false || $pfsearchfields_options[''.$field.''][''.$field2.''] == ""){
					$output = $default;
					
				}else{
					$output = $pfsearchfields_options[''.$field.''][''.$field2.''];
					
				}
			};

			return $output;
		}
	}
	if (!function_exists('PFMSIssetControl')) {
		/* Mail Options */
		function PFMSIssetControl($field, $field2 = '', $default = '',$icl_exit = 0){
			if (function_exists('icl_t')) {
				if (in_array($field, array(
						'setup33_emailsettings_debug',
						'setup33_emailsettings_secure',
						'setup33_emailsettings_auth',
						'setup33_emailsettings_smtpport',
						'setup33_emailsettings_smtp',
						'setup33_emailsettings_auth',
						'setup33_emailsettings_fromemail',
						'setup33_emailsettings_smtpaccount',
						'setup33_emailsettings_ed'
					))) {
					global $pointfindermail_option;
				}else{
					$pointfindermail_option = get_option('pointfindermail_options');
				}
				
			}else{
				global $pointfindermail_option;
			}

			if($field2 == ''){
				if(isset($pointfindermail_option[''.$field.'']) == false || $pointfindermail_option[''.$field.''] == ""){
					$output = $default;
				}else{
					$output = $pointfindermail_option[''.$field.''];
				}
			}else{
				if(isset($pointfindermail_option[''.$field.''][''.$field2.'']) == false || $pointfindermail_option[''.$field.''][''.$field2.''] == ""){
					$output = $default;
				}else{
					$output = $pointfindermail_option[''.$field.''][''.$field2.''];
				}
			};
			return $output;
		}
	}
	if (!function_exists('PFPFIssetControl')) {
		/* Custom Points */
		function PFPFIssetControl($field, $field2 = '', $default = ''){
			global $pfcustompoints_options;

			if($field2 == ''){if(isset($pfcustompoints_options[''.$field.'']) == false || $pfcustompoints_options[''.$field.''] == ""){$output = $default;}else{$output = $pfcustompoints_options[''.$field.''];}}else{if(isset($pfcustompoints_options[''.$field.''][''.$field2.'']) == false || $pfcustompoints_options[''.$field.''][''.$field2.''] == ""){$output = $default;}else{$output = $pfcustompoints_options[''.$field.''][''.$field2.''];}};
			return $output;
		}
	}
	if (!function_exists('PFSBIssetControl')) {
		/* Sidebar Options */
		function PFSBIssetControl($field, $field2 = '', $default = ''){
			global $pfsidebargenerator_options;
			if($field2 == ''){
				if(isset($pfsidebargenerator_options[''.$field.'']) == false || $pfsidebargenerator_options[''.$field.''] == ""){
					$output = $default;
					
				}else{
					$output = $pfsidebargenerator_options[''.$field.''];
					
				}
			}else{
				if(isset($pfsidebargenerator_options[''.$field.''][''.$field2.'']) == false || $pfsidebargenerator_options[''.$field.''][''.$field2.''] == ""){
					$output = $default;
					
				}else{
					$output = $pfsidebargenerator_options[''.$field.''][''.$field2.''];
					
				}
			};
			return $output;
		}
	}
	if (!function_exists('PFTWIssetControl')) {
		/* Twitter Widget Options */
		function PFTWIssetControl($field, $field2 = '', $default = ''){
			global $pftwitterwidget_options;
			if($field2 == ''){
				if(isset($pftwitterwidget_options[''.$field.'']) == false || $pftwitterwidget_options[''.$field.''] == ""){
					$output = $default;
					
				}else{
					$output = $pftwitterwidget_options[''.$field.''];
					
				}
			}else{
				if(isset($pftwitterwidget_options[''.$field.''][''.$field2.'']) == false || $pftwitterwidget_options[''.$field.''][''.$field2.''] == ""){
					$output = $default;
					
				}else{
					$output = $pftwitterwidget_options[''.$field.''][''.$field2.''];
					
				}
			};
			return $output;
		}
	}
	if (!function_exists('PFRECIssetControl')) {
		/* reCaptcha Options */
		function PFRECIssetControl($field, $field2 = '', $default = ''){
			
			if (function_exists('icl_t')) {
				$pfrecaptcha_options = get_option('pfrecaptcha_options');
			}else{
				global $pfrecaptcha_options;
			}
			if($field2 == ''){
				if(isset($pfrecaptcha_options[''.$field.'']) == false || $pfrecaptcha_options[''.$field.''] == ""){
					$output = $default;
					
				}else{
					$output = $pfrecaptcha_options[''.$field.''];
					
				}
			}else{
				if(isset($pfrecaptcha_options[''.$field.''][''.$field2.'']) == false || $pfrecaptcha_options[''.$field.''][''.$field2.''] == ""){
					$output = $default;
					
				}else{
					$output = $pfrecaptcha_options[''.$field.''][''.$field2.''];
					
				}
			};
			return $output;
		}
	}
	if (!function_exists('PFREVSIssetControl')) {
		/* Review System Options */
		function PFREVSIssetControl($field, $field2 = '', $default = '',$icl_exit = 0){
			global $pfitemreviewsystem_options;
			if($field2 == ''){
				if(isset($pfitemreviewsystem_options[''.$field.'']) == false || $pfitemreviewsystem_options[''.$field.''] == ""){
					$output = $default;
					
				}else{
					$output = $pfitemreviewsystem_options[''.$field.''];
					
				}
			}else{
				if(isset($pfitemreviewsystem_options[''.$field.''][''.$field2.'']) == false || $pfitemreviewsystem_options[''.$field.''][''.$field2.''] == ""){
					$output = $default;
					
				}else{
					$output = $pfitemreviewsystem_options[''.$field.''][''.$field2.''];
					
				}
			};
			return $output;
		}
	}
	if (!function_exists('PFSizeSIssetControl')) {
		/* Size System Options */
		function PFSizeSIssetControl($field, $field2 = '', $default = '',$icl_exit = 0){
			global $pfsizecontrol_options;
			if($field2 == ''){
				if(isset($pfsizecontrol_options[''.$field.'']) == false || $pfsizecontrol_options[''.$field.''] == ""){
					$output = $default;
					
				}else{
					$output = $pfsizecontrol_options[''.$field.''];
					
				}
			}else{
				if(isset($pfsizecontrol_options[''.$field.''][''.$field2.'']) == false || $pfsizecontrol_options[''.$field.''][''.$field2.''] == ""){
					$output = $default;
					
				}else{
					$output = $pfsizecontrol_options[''.$field.''][''.$field2.''];
					
				}
			};
			return $output;
		}
	}
	if (!function_exists('PFPBSIssetControl')) {
		/* Page Builder Options */
		function PFPBSIssetControl($field, $field2 = '', $default = '',$icl_exit = 0){
			global $pfpbcontrol_options;
			if($field2 == ''){
				if(isset($pfpbcontrol_options[''.$field.'']) == false || $pfpbcontrol_options[''.$field.''] == ""){
					$output = $default;
					
				}else{
					$output = $pfpbcontrol_options[''.$field.''];
					
				}
			}else{
				if(isset($pfpbcontrol_options[''.$field.''][''.$field2.'']) == false || $pfpbcontrol_options[''.$field.''][''.$field2.''] == ""){
					$output = $default;
					
				}else{
					$output = $pfpbcontrol_options[''.$field.''][''.$field2.''];
					
				}
			};
			return $output;
		};
	}
	if (!function_exists('PFASSIssetControl')) {
		/* Additional Options */
		function PFASSIssetControl($field, $field2 = '', $default = '',$icl_exit = 0){
			global $pfascontrol_options;
			if($field2 == ''){
				if(isset($pfascontrol_options[''.$field.'']) == false || $pfascontrol_options[''.$field.''] == ""){
					$output = $default;
					
				}else{
					$output = $pfascontrol_options[''.$field.''];
					
				}
			}else{
				if(isset($pfascontrol_options[''.$field.''][''.$field2.'']) == false || $pfascontrol_options[''.$field.''][''.$field2.''] == ""){
					$output = $default;
					
				}else{
					$output = $pfascontrol_options[''.$field.''][''.$field2.''];
					
				}
			};
			return $output;
		};
	}
	if (!function_exists('PFADVIssetControl')) {
		/* Advanced Options */
		function PFADVIssetControl($field, $field2 = '', $default = '',$icl_exit = 0){
			global $pfadvancedcontrol_options;
			if($field2 == ''){
				if(isset($pfadvancedcontrol_options[''.$field.'']) == false || $pfadvancedcontrol_options[''.$field.''] == ""){
					$output = $default;
					
				}else{
					$output = $pfadvancedcontrol_options[''.$field.''];
					
				}
			}else{
				if(isset($pfadvancedcontrol_options[''.$field.''][''.$field2.'']) == false || $pfadvancedcontrol_options[''.$field.''][''.$field2.''] == ""){
					$output = $default;
					
				}else{
					$output = $pfadvancedcontrol_options[''.$field.''][''.$field2.''];
					
				}
			};
			return $output;
		};
	}
	if (!function_exists('PFPGIssetControl')) {
		/* Payment Gateways */
		function PFPGIssetControl($field, $field2 = '', $default = '',$icl_exit = 0){
			global $pfpgcontrol_options;
			if($field2 == ''){
				if(isset($pfpgcontrol_options[''.$field.'']) == false || $pfpgcontrol_options[''.$field.''] == ""){
					$output = $default;
					
				}else{
					$output = $pfpgcontrol_options[''.$field.''];
					
				}
			}else{
				if(isset($pfpgcontrol_options[''.$field.''][''.$field2.'']) == false || $pfpgcontrol_options[''.$field.''][''.$field2.''] == ""){
					$output = $default;
					
				}else{
					$output = $pfpgcontrol_options[''.$field.''][''.$field2.''];
					
				}
			};
			return $output;
		};
	}


	if (!function_exists('pf_get_item_term_id')) {
		function pf_get_item_term_id($the_post_id){
			$item_term = '';
			if (!empty($the_post_id)) {
				$item_term_get = (!empty($the_post_id)) ? wp_get_post_terms($the_post_id, 'pointfinderltypes', array("fields" => "ids")) : '' ;

				if (count($item_term_get) > 1) {
					if (isset($item_term_get[0])) {
						$find_top_parent = pf_get_term_top_most_parent($item_term_get[0],'pointfinderltypes');
						$item_term = $find_top_parent['parent'];
					}
				}else{
					if (isset($item_term_get[0])) {
						$find_top_parent = pf_get_term_top_most_parent($item_term_get[0],'pointfinderltypes');
						$item_term = $find_top_parent['parent'];
					}
				}
			}
			return $item_term;
		}
	}

	if (!function_exists('pf_get_listingmeta_limit')) {
		function pf_get_listingmeta_limit($listing_meta, $item_term, $limit_var){
			if (isset($listing_meta[$item_term])) {
				if (isset($listing_meta[$item_term][$limit_var])) {
					if (!empty($listing_meta[$item_term][$limit_var])) {
						$listing_limit_status = $listing_meta[$item_term][$limit_var];
					}else{
						$listing_limit_status = 1;
					}
				}else{
					$listing_limit_status = 1;
				}
			}else{
				$listing_limit_status = 1;
			}
			return $listing_limit_status;
		}
	}
	

	if (!function_exists('PFPermalinkCheck')) {
		function PFPermalinkCheck(){
			$current_permalinkst = get_option('permalink_structure');

			if ($current_permalinkst == false || $current_permalinkst == '') {
				/* This using ? default. */
				return '&';
			}else{
				$current_permalinkst_last = substr($current_permalinkst, -1);
				if($current_permalinkst_last == '%'){
					return '/?';
				}elseif($current_permalinkst_last == '/'){
					return '?';
				}
			}
		}
	}
	if (!function_exists('PFControlEmptyArr')) {
		function PFControlEmptyArr($value){
			if(is_array($value)){
				if(count($value)>0){
					return true;
				}else{return false;}
			}else{return false;}
		}
	}
	if (!function_exists('pfstring2BasicArray')) {
		function pfstring2BasicArray($string, $kv = ',') {
			$ka = array();
			if($string != ''){
				if(strpos($string, $kv) != false){
					$string_exp = explode($kv,$string);
					foreach($string_exp as $s){
						$ka[]=$s;
					}
				}else{
					return array($string);
				}
			}
			return $ka;
		} 
	}
	if (!function_exists('pfstring2KeyedArray')) {
		function pfstring2KeyedArray($string, $kv = '=') {
			$ka = array();
			if($string != ''){
				foreach ($string as $s) { 
				  if ($s) {
					if ($pos = strpos($s, $kv)) { 
					  $ka[trim(substr($s, 0, $pos))] = trim(substr($s, $pos + strlen($kv)));
					} else {
					  $ka[] = trim($s);
					}
				  }
				}
			}
			return $ka;
		} 
	}
	if (!function_exists('pfKey2StringedArray')) {
		function pfKey2StringedArray($myarray){
			
			$output = array();
			foreach ($myarray as $a) {	
				array_push($output,$a);
			}
			
			return $output;
		}
	}
	if (!function_exists('PFCheckStatusofVar')) {
		function PFCheckStatusofVar($varname){
			$setup1_slides = PFSAIssetControl($varname,'','');
			$checkpfarray = count($setup1_slides);
			
			if(is_array($setup1_slides)){
				if($checkpfarray == 1){
					foreach($setup1_slides as $setup1_slide){
							if (isset($setup1_slide['title'])) {
								if($setup1_slide['title'] != ''){
									$pfstart = true;
								}else{
									$pfstart = false;
								};
							}else{
								$pfstart = false;
							};
							
					};
				}elseif($checkpfarray < 1 || $checkpfarray == NULL){
					$pfstart = false;
				}elseif($checkpfarray > 1 ){
					$pfstart = true;
				}
				
			}else{
				$pfstart = false;
			}
			return $pfstart;
		}
	}
	if (!function_exists('GetPFTermInfo')) {
		function GetPFTermInfo($id, $taxonomy,$pflang = ''){
			$termnames = '';
			$postterms = get_the_terms( $id, $taxonomy );
			$st22srlinklt = PFSAIssetControl('st22srlinklt','','1');
			$pr_it_v = PFSAIssetControl('pr_it_v','','0');

			if($postterms){
				foreach($postterms as $postterm){
					if (isset($postterm->term_id)) {
						if(function_exists('icl_t')) {
							if (!empty($pflang)) {
								$term_idx = icl_object_id($postterm->term_id,$taxonomy,true,$pflang);
							}else{
								$term_idx = icl_object_id($postterm->term_id,$taxonomy,true,PF_current_language());
							}
						} else {
							$term_idx = $postterm->term_id;
						}

						$terminfo = get_term( $term_idx, $taxonomy );

						if (!empty($terminfo->parent) && $pr_it_v == 1) {

							$terminfo_parent = get_term( $terminfo->parent, $taxonomy );

							if (!empty($terminfo_parent->parent)) {
								$terminfo_parent2 = get_term( $terminfo_parent->parent, $taxonomy );

								$term_link_parent2 = get_term_link( $terminfo_parent->parent, $taxonomy );
								if (is_wp_error($term_link_parent2) === true) {$term_link_parent2 = '#';}

								$term_info_parent2_name = $terminfo_parent2->name;
								if (is_wp_error($term_info_parent2_name) === true) {$term_info_parent2_name = '';}

								if ($st22srlinklt == 1) {
									$termnames .= '<a href="'.$term_link_parent2.'">'.$term_info_parent2_name.'</a>';
								}else{
									$termnames .= '<span class="pfdetail-ftext-nolink">'.$term_info_parent2_name.'</span>';
								}
							}

							$term_link_parent = get_term_link( $terminfo->parent, $taxonomy );
							if (is_wp_error($term_link_parent) === true) {$term_link_parent = '#';}

							$term_info_parent_name = $terminfo_parent->name;
							if (is_wp_error($term_info_parent_name) === true) {$term_info_parent_name = '';}

							if ($st22srlinklt == 1) {
								$termnames .= '<a href="'.$term_link_parent.'">'.$term_info_parent_name.'</a>';
							}else{
								$termnames .= '<span class="pfdetail-ftext-nolink">'.$term_info_parent_name.'</span>';
							}
						}

						$term_link = get_term_link( $term_idx, $taxonomy );
						if (is_wp_error($term_link) === true) {$term_link = '#';}

						$term_info_name = $terminfo->name;
						if (is_wp_error($term_info_name) === true) {$term_info_name = '';}
						
						/*if(!empty($termnames)){$termnames .= '';}*/
						
						if ($st22srlinklt == 1) {
							$termnames .= '<a href="'.$term_link.'">'.$term_info_name.'</a>';
						}else{
							$termnames .= '<span class="pfdetail-ftext-nolink">'.$term_info_name.'</span>';
						}
						
					}
				}
			}
			return $termnames;
		}
	}
	if (!function_exists('GetPFTermInfoH')) {
		function GetPFTermInfoH($id, $taxonomy,$pflang = '',$type){
			$termnames = '';
			$postterms = wp_get_post_terms( $id, $taxonomy,array('fields' => 'all','orderby'=>'term_order','order'=>'ASC'));
			
			if($postterms){
				$postterms_count = count($postterms);
				$i = 1;
				foreach($postterms as $postterm){
					if (isset($postterm->term_id)) {
						if(function_exists('icl_t')) {
							if (!empty($pflang)) {
								$term_idx = icl_object_id($postterm->term_id,$taxonomy,true,$pflang);
							}else{
								$term_idx = icl_object_id($postterm->term_id,$taxonomy,true,PF_current_language());
							}

							$postterm = get_term( $term_idx, $taxonomy );
						}

						$term_link = get_term_link( $postterm->term_id, $taxonomy );
						$term_name = $postterm->name;

						if (is_wp_error($term_link) === true) {$term_link = '#';}					
						if (is_wp_error($term_name) === true) {$term_name = '';}
						if ($type == 2) {
							$termnames .= '<a href="'.$term_link.'">'.$term_name.'</a>';
							if ($i != $postterms_count) {
								$termnames .= ' / ';
							}
						}else{
							$termnames .= '<a href="'.$term_link.'">'.$term_name.'</a>';
						}

						$i++;
					}
				}
			}
			return $termnames;
		}
	}
	if (!function_exists('GetPFTermInfoWindow')) {
		function GetPFTermInfoWindow($id, $taxonomy,$pflang = ''){
			$termnames = '';
			$postterms = get_the_terms( $id, $taxonomy );
			$st22srlinklt = PFSAIssetControl('st22srlinklt','','1');

			if($postterms){
				foreach($postterms as $postterm){
					if (isset($postterm->term_id)) {
						if(function_exists('icl_t')) {
							if (!empty($pflang)) {
								$term_idx = icl_object_id($postterm->term_id,$taxonomy,true,$pflang);
							}else{
								$term_idx = icl_object_id($postterm->term_id,$taxonomy,true,PF_current_language());
							}
						} else {
							$term_idx = $postterm->term_id;
						}

						$terminfo = get_term( $term_idx, $taxonomy );
						
						$term_link = get_term_link( $term_idx, $taxonomy );
						if (is_wp_error($term_link) === true) {$term_link = '#';}

						
						$term_info_name = $terminfo->name;
						if (is_wp_error($term_info_name) === true) {$term_info_name = '';}
						
						if(!empty($termnames)){$termnames .= ', ';}

						
						if ($st22srlinklt == 1) {
							$termnames .= '<a href="'.$term_link.'">'.$term_info_name.'</a>';
						}else{
							$termnames .= '<span class="pfdetail-ftext-nolink">'.$term_info_name.'</span>';
						}
					}
				}
			}
			return $termnames;
		}
	}
	if (!function_exists('GetPFTermName')) {
		function GetPFTermName($id,$taxname){
			$post_type_name = wp_get_post_terms($id, $taxname, array("fields" => "names"));
			
			if(PFControlEmptyArr($post_type_name)){
				return $post_type_name[0];
			}
		}
	}
	if (!function_exists('pointfinderhex2rgb')) {		
		function pointfinderhex2rgb($hex,$opacity) {
		   $hex = str_replace("#", "", $hex);

		   if(strlen($hex) == 3) {
			  $r = hexdec(substr($hex,0,1).substr($hex,0,1));
			  $g = hexdec(substr($hex,1,1).substr($hex,1,1));
			  $b = hexdec(substr($hex,2,1).substr($hex,2,1));
		   } else {
			  $r = hexdec(substr($hex,0,2));
			  $g = hexdec(substr($hex,2,2));
			  $b = hexdec(substr($hex,4,2));
		   }
		   
		   if($opacity !=''){
			   return 'rgba('.$r.','.$g.','.$b.','.$opacity.')';
		   }else{
			   return 'rgb('.$r.','.$g.','.$b.')';
		   }
		}
	}
	if (!function_exists('pointfinderhex2rgbex')) {
		function pointfinderhex2rgbex($hex,$opacity='1.0') {
		   $hex = str_replace("#", "", $hex);

		   if(strlen($hex) == 3) {
			  $r = hexdec(substr($hex,0,1).substr($hex,0,1));
			  $g = hexdec(substr($hex,1,1).substr($hex,1,1));
			  $b = hexdec(substr($hex,2,1).substr($hex,2,1));
		   } else {
			  $r = hexdec(substr($hex,0,2));
			  $g = hexdec(substr($hex,2,2));
			  $b = hexdec(substr($hex,4,2));
		   }
		   

		   return array('rgba' => 'rgba('.$r.','.$g.','.$b.','.$opacity.')','rgb'=> 'rgb('.$r.','.$g.','.$b.')');
		}
	}
	if (!function_exists('PFcheck_postmeta_exist')) {
		function PFcheck_postmeta_exist( $meta_key, $post_id) {
			global $wpdb;

			$meta_count = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->postmeta where meta_key like %s and post_id = %d",$meta_key,$post_id) );

			if ($meta_count > 0){
				return true;
			}else{
				return false;
			}
		}
	}
	if (!function_exists('PFIF_SortFields_sg')) {
		function PFIF_SortFields_sg($searchvars,$orderarg_value = ''){
			$pfstart = PFCheckStatusofVar('setup1_slides');
			$if_sorttext = '';
			
			if($pfstart == true){
				
				$available_fields = array(1,2,3,4,5,7,8,14);
				$setup1_slides = PFSAIssetControl('setup1_slides','','');	
				
				
				//Prepare detailtext
				foreach ($setup1_slides as &$value) {
					$stext = '';
					if($orderarg_value != ''){
						if(strcmp($orderarg_value,$value['url']) == 0){
							$stext = 'selected';
						}else{
							$stext = '';
						}
					}
					$Parentcheckresult = PFIF_CheckItemsParent_ld($value['url']);

					if(is_array($searchvars)){
						$res = PFIF_CheckFormVarsforExist_ld($searchvars,$Parentcheckresult);
					}else{
						$res = false;
					}

					$customfield_sortcheck = PFCFIssetControl('setupcustomfields_'.$value['url'].'_sortoption','','0');
					

					if($Parentcheckresult == 'none'){
						if(in_array($value['select'], $available_fields) && $customfield_sortcheck != 0){
							$sortnamecheck = PFCFIssetControl('setupcustomfields_'.$value['url'].'_sortname','','');

							if(empty($sortnamecheck)){
								$sortnamecheck = $value['title'];
							}
							$if_sorttext .= '<option value="'.$value['url'].'" '.$stext.'>'.$sortnamecheck.'</option>';
						}
					}else{
						if($res == true){
							$sortnamecheck = PFCFIssetControl('setupcustomfields_'.$value['url'].'_sortname','','');

							if(empty($sortnamecheck)){
								$sortnamecheck = $value['title'];
							}

							if(in_array($value['select'], $available_fields) && $customfield_sortcheck != 0){
								$if_sorttext .= '<option value="'.$value['url'].'" '.$stext.'>'.$sortnamecheck.'</option>';
							}
						}
					
					}
					
				}
				
			}
			return $if_sorttext;
		}
	}

	/** 
	*Start: Data Validation for all fields
	**/
		if (!function_exists('PFCleanArrayAttr')) {
			function PFCleanArrayAttr($callback, $array) {

				$exclude_list = array('item_desc','item_title','webbupointfinder_item_custombox1','webbupointfinder_item_custombox2','webbupointfinder_item_custombox3','webbupointfinder_item_custombox4','webbupointfinder_item_custombox5','webbupointfinder_item_custombox6');

			    foreach ($array as $key => $value) {
			        if (is_array($array[$key])) {
			        	if (!in_array($key, $exclude_list)) {
			        		$array[$key] = PFCleanArrayAttr($callback, $array[$key]);
			        	}else{
			           		$array[$key] = $array[$key];
			            } 
			        }else{
			        	if(!in_array($key, $exclude_list)){
			            	$array[$key] = call_user_func($callback, $array[$key]);
			            }else{
			           		$array[$key] = $array[$key];
			            } 
			        }
			    }
			    return $array;
			}
		}
		if (!function_exists('PFCleanFilters')) {
			function PFCleanFilters($arrayvalue){
				return esc_attr(sanitize_text_field($arrayvalue));
			}
		}
	/** 
	*End: Data Validation for all fields
	**/

/**
*End:General Functions
**/



/**
*WPML Language Functions
**/
	if (!function_exists('PF_current_language')) {
		function PF_current_language(){
		    global $sitepress;
		    if(isset($sitepress)){
			    $current_language = $sitepress->get_current_language();
			    return $current_language;
		    }
		}
	}
	if (!function_exists('PF_default_language')) {
		function PF_default_language(){
		    global $sitepress;
		    if(isset($sitepress)){
			    $current_language = $sitepress->get_default_language();
			    return $current_language;
		    }
		}
	}
	if (!function_exists('PFLangCategoryID_ld')) {
		function PFLangCategoryID_ld($id,$lang,$setup3_pointposttype_pt1){
			if(function_exists('icl_t')) {
				return icl_object_id($id,$setup3_pointposttype_pt1,true,$lang);
			} else {
				return $id;
			}
		}
	}



/**
*Start:Ajax list data and static grid listing functions
**/
	if (!function_exists('PFIFPageNumbers')) {
		function PFIFPageNumbers(){
			$output = array(3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,25,50,75);
			return $output;
		}
	}
	if (!function_exists('PFGetArrayValues_ld')) {
		function PFGetArrayValues_ld($pfvalue){
			if(!is_array($pfvalue)){
				$pfvalue_arr = array();
				if(strpos($pfvalue,',')){
					$newpfvalues = explode(',',$pfvalue);
					foreach($newpfvalues as $newpfvalue){
						array_push($pfvalue_arr,$newpfvalue);
					}
				}else{
					array_push($pfvalue_arr,$pfvalue);
				}
				return $pfvalue_arr;
			}else{
				return $pfvalue;
			}
		}
	}
	if (!function_exists('PFIF_DetailText_ld')) {
		function PFIF_DetailText_ld($id,$setup22_searchresults_hide_lt,$post_listing_typeval=''){
			$pfstart = PFCheckStatusofVar('setup1_slides');
			$output_text = array();
			if($pfstart == true){
				$if_detailtext = '';
				
				$setup1_slides = PFSAIssetControl('setup1_slides','','');
				if(is_array($setup1_slides)){	
					$i=1;
					foreach ($setup1_slides as &$value) {
						
						$customfield_infocheck = PFCFIssetControl('setupcustomfields_'.$value['url'].'_linfowindow','','0');
						$available_fields = array(1,2,3,4,5,7,8,14,15);

						
						if(in_array($value['select'], $available_fields) && $customfield_infocheck != 0){

							$PFTMParent = '';
							$ShowField = true;

							if(!empty($post_listing_typeval)){
								$PFTMParent = pf_get_term_top_most_parent($post_listing_typeval,'pointfinderltypes');
								$PFTMParent = (isset($PFTMParent['parent']))?$PFTMParent['parent']:'';
							}

							$ParentItem = PFCFIssetControl('setupcustomfields_'.$value['url'].'_parent','','0');
				
							if(PFControlEmptyArr($ParentItem) && function_exists('icl_t')){
								$NewParentItemArr = array();
								foreach ($ParentItem as $ParentItemSingle) {
									$NewParentItemArr[] = apply_filters('wpml_object_id', $ParentItemSingle, 'pointfinderltypes', TRUE);
								}
								$ParentItem = $NewParentItemArr;
							}

							
							/*If it have a parent element*/
							if(PFControlEmptyArr($ParentItem)){
								
								if(function_exists('icl_t')) {
									$PFCLang = PF_current_language();
									foreach ($ParentItem as $key => $valuex) {
										$ParentItem[$key] = icl_object_id($valuex,'pointfinderltypes',true,$PFCLang);
									}
								}

								$PFLTCOVars = get_option('pointfinderltypes_covars');

								if (isset($PFLTCOVars[$PFTMParent]['pf_subcatselect'])) {
									if ($PFLTCOVars[$PFTMParent]['pf_subcatselect'] == 1) {
										$post_listing_typeval = $PFTMParent;
									}
								}
								
								if(in_array($post_listing_typeval, $ParentItem) ){					
									$ShowField = true;										
								}else{
									$ShowField = false;
								}
							}

							if ($ShowField) {
								
								$PF_CF_Val = new PF_CF_Val($id);
								$ClassReturnVal = $PF_CF_Val->GetValue($value['url'],$id,$value['select'],$value['title'],1);
								
								if($ClassReturnVal != ''){
									if(strpos($ClassReturnVal,"pf-price") != false){
										$output_text['priceval'] = $ClassReturnVal;
									}else{
										$if_detailtext .= $ClassReturnVal;
										
									}

								}
							}
							
						}
						
					}
				}

				$output_text['content'] = $if_detailtext;

				if($setup22_searchresults_hide_lt == 0){
					$pfitemtext = '';
					
					
					if($pfitemtext != ''){
						$output_text['ltypes']= '<div class="pflistingitem-subelement pf-ltitem"><span class="pf-ftitle">'.$pfitemtext.'</span>
						<span class="pf-ftext">'.GetPFTermInfo($id,'pointfinderltypes').'</span></div>';
					}else{
						$term_names = get_the_term_list( $id, 'pointfinderltypes', '<ul class="pointfinderpflisttermsgr"><li>', ',</li><li>', '</li></ul>' );

						$output_text['ltypes']= '<div class="pflistingitem-subelement pf-price">';
						$output_text['ltypes'] .= '';
							if (strpos($term_names, ',') != 0) {
								$term_names = explode(',', $term_names);
							}
							
							if (count($term_names) > 1) {
								$output_text['ltypes'] .= $term_names[0];
							}else{
								$output_text['ltypes'] .= $term_names;
							}
							
						$output_text['ltypes'] .= '';
						$output_text['ltypes'] .= '</div>';
					}
				}
			}
			unset($PF_CF_Val);

			return $output_text;
		}
	}
	if (!function_exists('PFIF_CheckItemsParent_ld')) {
		function PFIF_CheckItemsParent_ld($slug){
			$RelationFieldName = 'setupcustomfields_'.$slug.'_parent';
			$ParentItem = PFCFIssetControl($RelationFieldName,'','');
			if($ParentItem != '' && $ParentItem != '0'){return $ParentItem;}else{return 'none';}
		}
	}
	if (!function_exists('PFIF_CheckFieldisNumeric_ld')) {
		function PFIF_CheckFieldisNumeric_ld($pfg_orderby){
			$setup1_slides = PFSAIssetControl('setup1_slides','','');	
			$text = false;
			foreach ($setup1_slides as &$value) {
				if($value['select'] == 4 && strcmp($value['url'], $pfg_orderby) == 0){
					$text = true;
				}
			}
			return $text;
		}
	}
	if (!function_exists('PFIF_CheckFormVarsforExist_ld')) {
		function PFIF_CheckFormVarsforExist_ld($searchvars,$itemvar = array()){
			if($itemvar != 'none' && count($itemvar)>0){
				foreach($searchvars as $searchvar){
					if(in_array($searchvar,$itemvar)){return true;}
				}
			}
		}
	}
	if (!function_exists('PFFindKeysInSearchFieldA_ld')) {
		function PFFindKeysInSearchFieldA_ld($pfformvar){
			$setup1s_slides = PFSAIssetControl('setup1s_slides','','');
			
			foreach($setup1s_slides as $setup1s_slide){
				if($setup1s_slide['url'] == $pfformvar){
					return $setup1s_slide['select'];
					break;
				}
				
			};
		}
	}
	if (!function_exists('PFEX_extract_type_ig')) {
		function PFEX_extract_type_ig($pfarray){
			$output = '';
			if(is_array($pfarray)){
				foreach ($pfarray as $value) {
					if ($output != '') {
						$output .= ',';
					} 
					$output .= $value;
				}
				return $output;
			}else{return $pfarray;}
		}
	}
	if (!function_exists('PF_generate_random_string_ig')) {
		function PF_generate_random_string_ig($name_length = 12) {
			$alpha_numeric = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
			return substr(str_shuffle($alpha_numeric), 0, $name_length);
		}
	}

/**
*End:Ajax list data and static grid listing functions
**/



/**
*Start:reCaptcha Functions
**/
	if (!function_exists('PFreCaptchaWidget')) {
		function PFreCaptchaWidget(){
			$wpf_rndnum = rand(10,1000);
			$publickey = PFRECIssetControl('setupreCaptcha_general_pubkey','','');
			$lang = PFRECIssetControl('setupreCaptcha_general_lang','','en');
			return '<script type="text/javascript">var PFFonloadCallback = function() {jQuery.widgetId'.$wpf_rndnum.' = grecaptcha.render("g-recaptcha-'.$wpf_rndnum.'", {"sitekey" : "'.$publickey.'"});};</script><div id="g-recaptcha-'.$wpf_rndnum.'" class="g-recaptcha-field" data-rekey="widgetId'.$wpf_rndnum.'"></div><script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl='.$lang.'&onload=PFFonloadCallback&render=explicit" async defer></script>';
		}
	}

	if (!function_exists('PFCGreCaptcha')) {
		function PFCGreCaptcha($recaptcha_response_field = ''){
				
				$privatekey = PFRECIssetControl('setupreCaptcha_general_prikey','','');

				$resp = null;
				$error = null;

				$statusofjob = null;


				$reCaptcha = new ReCaptcha($privatekey);

				
				if (!empty($recaptcha_response_field)) {
				    $resp = $reCaptcha->verifyResponse(
				        $_SERVER["REMOTE_ADDR"],
				        $recaptcha_response_field
				    );
				}

				if ($resp != null && $resp->success) {
					$statusofjob = 1;
				}else {
			        $statusofjob = 0;
			    }

				return $statusofjob;
		}
	}
/**
*End:reCaptcha Functions
**/




/**
*Start:Page Functions
**/
	if (!function_exists('PFGetHeaderBar')) {
		function PFGetHeaderBar($post_id='', $post_title=''){
		    
		    if($post_id == ''){
		        $post_id = get_the_ID(); 
		    }

		    $_page_titlebararea = redux_post_meta("pointfinderthemefmb_options", $post_id, "webbupointfinder_page_titlebararea");

		    if($_page_titlebararea == 1){
		    	
		    	$_page_defaultheaderbararea = redux_post_meta("pointfinderthemefmb_options", $post_id, "webbupointfinder_page_defaultheaderbararea");
		    	
		    	if ($_page_defaultheaderbararea == 1) {
		    		if(function_exists('PFGetDefaultPageHeader')){
						PFGetDefaultPageHeader(array('pagename' => get_the_title()));
						return;
					}

		    	}

		    	$_page_titlebarareatext = redux_post_meta("pointfinderthemefmb_options", $post_id, "webbupointfinder_page_titlebarareatext");
		    	$_page_titlebarcustomtext_color = redux_post_meta( "pointfinderthemefmb_options", $post_id, "webbupointfinder_page_titlebarcustomtext_color" );
		    	$_page_titlebarcustomtext = redux_post_meta("pointfinderthemefmb_options", $post_id, "webbupointfinder_page_titlebarcustomtext");
		        $_page_titlebarcustomsubtext = redux_post_meta("pointfinderthemefmb_options", $post_id, "webbupointfinder_page_titlebarcustomsubtext");
		        $_page_titlebarcustomheight = redux_post_meta("pointfinderthemefmb_options", $post_id, "webbupointfinder_page_titlebarcustomheight");
		        $_page_titlebarcustombg = redux_post_meta("pointfinderthemefmb_options", $post_id, "webbupointfinder_page_titlebarcustombg");
		        $_page_titlebarcustomtext_bgcolor = redux_post_meta("pointfinderthemefmb_options", $post_id, "webbupointfinder_page_titlebarcustomtext_bgcolor");
		        $_page_titlebarcustomtext_bgcolorop = redux_post_meta("pointfinderthemefmb_options", $post_id, "webbupointfinder_page_titlebarcustomtext_bgcolorop");
		        $setup43_themecustomizer_headerbar_shadowopt = redux_post_meta("pointfinderthemefmb_options", $post_id, "webbupointfinder_page_shadowopt");
		       
		        if (PFControlEmptyArr($_page_titlebarcustombg)) {
		        	$_page_titlebarcustombg_repeat = $_page_titlebarcustombg['background-repeat'];
			        $_page_titlebarcustombg_color = $_page_titlebarcustombg['background-color'];
			        $_page_titlebarcustombg_fixed = $_page_titlebarcustombg['background-attachment'];
			        $_page_titlebarcustombg_image = $_page_titlebarcustombg['background-image'];
		        }else{
		        	$_page_titlebarcustombg_repeat = '';
			        $_page_titlebarcustombg_color = '';
			        $_page_titlebarcustombg_fixed = '';
			        $_page_titlebarcustombg_image = '';
		        }


		        $_page_custom_css = $_text_custom_css = ' style="';

		        if ($_page_titlebarcustomheight != '') {
		            $_page_custom_css .= 'height:'.$_page_titlebarcustomheight.'px;';
		        } 

		        if ($_page_titlebarcustombg_image != '') {
		            $_page_custom_css .= 'background-image:url('.$_page_titlebarcustombg_image.');';
		        } 
		        if ($_page_titlebarcustombg_repeat != '') {
		            $_page_custom_css .= 'background-repeat: '.$_page_titlebarcustombg_repeat.';';
		        }
		        if ($_page_titlebarcustombg_color != '') {
		            $_page_custom_css .= 'background-color:'.$_page_titlebarcustombg_color.';';
		        } 
		        if ($_page_titlebarcustombg_fixed != '') {
		            $_page_custom_css .= 'background-attachment :'.$_page_titlebarcustombg_fixed.';';
		        }  
		        if ($_page_titlebarcustomtext_color != '') {
		            $_page_custom_css .= 'color:'.$_page_titlebarcustomtext_color.';';
		            $_text_custom_css .= 'color:'.$_page_titlebarcustomtext_color.';';

		        } 

		        if ($_page_titlebarcustomtext_bgcolor != '') {
		        	$color_output = pointfinderhex2rgbex($_page_titlebarcustomtext_bgcolor,$_page_titlebarcustomtext_bgcolorop);
		        	$_text_custom_css .= 'background-color: '.$color_output['rgb'].';background-color: '.$color_output['rgba'].'; ';
		        	$_text_custom_css_main = ' pfwbg';
		    		$_text_custom_css_sub = ' pfwbg';
		        }else{
		        	$_text_custom_css_main = '';
		        	$_text_custom_css_sub = '';
		        }

		        $_page_custom_css .= '';
		        $_text_custom_css .= '"';

		        
		        
		        $pagetitletext = '<div class="main-titlebar-text'.$_text_custom_css_main.'"'.$_text_custom_css.'>';

		        if($_page_titlebarareatext == 1){

		            if ($_page_titlebarcustomtext != '') {
		                $pagetitletext .= $_page_titlebarcustomtext;
		            }else{
		            	$pagetitletext .= get_the_title();
		            }
		            

		            if ($_page_titlebarcustomsubtext != '') {
		                $pagesubtext = '<div class="sub-titlebar-text'.$_text_custom_css_sub.'"'.$_text_custom_css.'>'.$_page_titlebarcustomsubtext.'</div>';
		            }else{
		            	$pagesubtext = '';
		            }
		        }else{
		        	$pagetitletext .= get_the_title();
		        	$pagesubtext = '';
		        }

		        if($post_title != ''){$pagetitletext .= ' / '.$post_title;}
		        $pagetitletext .= '</div>';
		        
		        
	        	echo '
	        	<section role="pageheader"'.$_page_custom_css.'" class="pf-page-header">
	        	';
	        	if ($setup43_themecustomizer_headerbar_shadowopt != 0) {
					echo '<div class="pfheaderbarshadow'.$setup43_themecustomizer_headerbar_shadowopt.'"></div>';
				}
	        	echo '
	        		<div class="pf-container">
	        			<div class="pf-row">
	        				<div class="col-lg-12">
	        					<div class="pf-titlebar-texts">'.$pagetitletext.$pagesubtext.'</div>
	        					<div class="pf-breadcrumbs clearfix">'.pf_the_breadcrumb(
	        						array(
								        '_text_custom_css' => $_text_custom_css,
								        '_text_custom_css_main' => $_text_custom_css_main
										)
	        						).'</div>
	        				</div>
	        			</div>
	        		</div>
	        	</section>';
	  
		    }
		}
	}
	if (!function_exists('PFGetDefaultPageHeader')) {
		function PFGetDefaultPageHeader($params = array()){

			$defaults = array( 
		        'author_id' => '',
		        'agent_id' => '',
		        'taxname' => '',
		        'taxnamebr' => '',
		        'taxinfo' => '',
		        'itemname' => '',
		        'itemaddress' => '',
		        'pagename' => ''
		    );

			$params = array_merge($defaults, $params);

			$setup43_themecustomizer_titlebarcustomtext_bgcolor = PFSAIssetControl('setup43_themecustomizer_titlebarcustomtext_bgcolor','','');
			$setup43_themecustomizer_titlebarcustomtext_bgcolorop = PFSAIssetControl('setup43_themecustomizer_titlebarcustomtext_bgcolorop','','');

			$setup43_themecustomizer_headerbar_shadowopt = PFSAIssetControl('setup43_themecustomizer_headerbar_shadowopt','',0);

		 	$_text_custom_css =' style="';

		    if ($setup43_themecustomizer_titlebarcustomtext_bgcolor != '') {
		    	$color_output = pointfinderhex2rgbex($setup43_themecustomizer_titlebarcustomtext_bgcolor,$setup43_themecustomizer_titlebarcustomtext_bgcolorop);
		    	$_text_custom_css .= 'background-color: '.$color_output['rgb'].';background-color: '.$color_output['rgba'].'; ';
		    	$_text_custom_css_main = ' pfwbg';
		    	$_text_custom_css_sub = ' pfwbg';
		    }else{
		    	$_text_custom_css_main = '';
		    	$_text_custom_css_sub = '';
		    }

		    $_text_custom_css .= '"';

		    $titletext = '';
		    if(empty($params['taxname'])){
			    if (is_author()) {
			    	$user = get_user_by('id', $params['author_id']);
			    	$titletext = $user->nickname;
			    }elseif(is_search()){
			    	if (!empty($_GET['s'])) {
			    		$titletext = sprintf(esc_html__( 'Search Results for %s', 'pointfindert2d' ),$_GET['s']);
			    	}else{
			    		$titletext = esc_html__( 'Search Results', 'pointfindert2d' );
			    	}
			    	
				}elseif(is_category()){
					$categ = get_category_by_path("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",false);
					
					if (empty($categ)) {
						$categ = esc_sql(get_query_var('cat'));
						$titletext = get_cat_name( $categ );
					}else{
						if (isset($categ)) {
							$titletext = $categ->name;
						}
					}
					
					
				}elseif (is_tag()) {
					$titletext = single_tag_title('',false);
				}else{
			    	$titletext = get_the_title();
			    }
			}else{
				$titletext = $params['taxname'];
				$titlesubtext = $params['taxinfo'];
			}

			if (function_exists('is_woocommerce')) {
		    	if (is_woocommerce()) {
		    		ob_start();
		    		woocommerce_page_title();
		    		$titletext = ob_get_contents();
		    		ob_end_clean();
		    	}
		    }

			if(empty($params['itemname'])){

				/* If page is member dashboard. */
			    $setup4_membersettings_dashboard = PFSAIssetControl('setup4_membersettings_dashboard','','');
			    if (get_the_id() == $setup4_membersettings_dashboard) {
			    	
			    	if(isset($_GET['ua']) && $_GET['ua']!=''){
						$ua_action = esc_attr($_GET['ua']);
					}else{
						$ua_action = '';
					}

			    	switch ($ua_action) {
						case 'profile':
							$titletext = PFSAIssetControl('setup29_dashboard_contents_profile_page_title','','Profile');
						break;
						case 'favorites':
							$titletext = PFSAIssetControl('setup29_dashboard_contents_favs_page_title','','My Favorites');
						break;
						case 'newitem':
							$titletext = PFSAIssetControl('setup29_dashboard_contents_submit_page_title','','Submit New Item');
						break;
						case 'edititem':
							$titletext = PFSAIssetControl('setup29_dashboard_contents_submit_page_titlee','','Edit Item');
						break;
						case 'reviews':
							$titletext = PFSAIssetControl('setup29_dashboard_contents_rev_page_title','','My Reviews');
						break;
						case 'myitems':
							$titletext = PFSAIssetControl('setup29_dashboard_contents_my_page_title','','My Items');
						break;
						case 'renewplan':
							$titletext = esc_html__("Renew Current Plan","pointfindert2d" );
						break;
						case 'purchaseplan':
							$titletext = esc_html__("Purchase New Plan","pointfindert2d");
						break;
						case 'upgradeplan':
							$titletext = esc_html__("Upgrade Plan","pointfindert2d" );
						break;
						case 'invoices':
							$titletext = PFSAIssetControl('setup29_dashboard_contents_inv_page_title','','My Invoices');
						break;
						default:
							$titletext = esc_html__('Not Found!','pointfindert2d');
						break;

					}

					

					$titletext = get_the_title().' / '.$titletext;
			    }

				echo '
				<section role="pageheader" class="pf-defaultpage-header">';
					if ($setup43_themecustomizer_headerbar_shadowopt != 0) {
						echo '<div class="pfheaderbarshadow'.$setup43_themecustomizer_headerbar_shadowopt.'"></div>';
					}
				echo '
					<div class="pf-container">
						<div class="pf-row">
							<div class="col-lg-12">';
							
							
							echo '
								<div class="pf-titlebar-texts">
								<h1 class="main-titlebar-text'.$_text_custom_css_main.'"'.$_text_custom_css.'>'.$titletext.'</h1>
								';
								if (!empty($titlesubtext)) {
									echo '<div class="sub-titlebar-text'.$_text_custom_css_sub.'"'.$_text_custom_css.'>'.$titlesubtext.'</div>';
								}
								echo '
								</div>
								';

								if(empty($params['taxname'])){
									echo '<div class="pf-breadcrumbs clearfix'.$_text_custom_css_sub.'"'.$_text_custom_css.'>'.pf_the_breadcrumb(array('_text_custom_css' => $_text_custom_css,'_text_custom_css_main' => $_text_custom_css_main)).'</div>';
								}else{
									echo '<div class="pf-breadcrumbs clearfix'.$_text_custom_css_sub.'"'.$_text_custom_css.'>'.pf_the_breadcrumb(array('taxname'=>$params['taxnamebr'],'_text_custom_css' => $_text_custom_css,'_text_custom_css_main' => $_text_custom_css_main)).'</div>';
								}
								
								echo '
							</div>
						</div>
					</div>
				</section>';
			}else{
				$setup42_itempagedetails_hideaddress = PFSAIssetControl('setup42_itempagedetails_hideaddress','','1');
				echo '
				<section role="itempageheader" class="pf-itempage-header">';
					if ($setup43_themecustomizer_headerbar_shadowopt != 0) {
						echo '<div class="pfheaderbarshadow'.$setup43_themecustomizer_headerbar_shadowopt.'"></div>';
					}
				echo '
					<div class="pf-container">
						<div class="pf-row">
							<div class="col-lg-12">
								<div class="pf-titlebar-texts">
									<div class="main-titlebar-text'.$_text_custom_css_main.'"'.$_text_custom_css.'>'.$params['itemname'].'</div>
									';
									if($setup42_itempagedetails_hideaddress == 1){
									echo '<div class="sub-titlebar-text'.$_text_custom_css_sub.'"'.$_text_custom_css.'>'.$params['itemaddress'].'</div>';
									}
									echo '						
								</div>
								<div class="pf-breadcrumbs clearfix hidden-print'.$_text_custom_css_sub.'"'.$_text_custom_css.'>'.pf_the_breadcrumb(array('_text_custom_css' => $_text_custom_css,'_text_custom_css_main' => $_text_custom_css_main)).'</div>
							</div>
						</div>
					</div>
				</section>';
			}
		}
	}
	if (!function_exists('PFGetDefaultCatPageHeader')) {
		function PFGetDefaultCatPageHeader($params = array()){

			$defaults = array( 
		        'taxname' => '',
		        'taxnamebr' => '',
		        'taxinfo' => '',
		        'pf_cat_textcolor' => '',
				'pf_cat_backcolor' => '',
				'pf_cat_bgimg' => '',
				'pf_cat_bgrepeat' => '',
				'pf_cat_bgsize' => '',
				'pf_cat_bgpos' => '',
				'pf_cat_headerheight' => '',
				'pf_cat_bgattachment' => 'scroll'
		    );

			$params = array_merge($defaults, $params);

			$setup43_themecustomizer_headerbar_shadowopt = PFSAIssetControl('setup43_themecustomizer_headerbar_shadowopt','',0);

		 	$_text_custom_css = $_text_custom_css1 = ' style="';

		    if ($params['pf_cat_backcolor'] != '') {
		    	$color_output = pointfinderhex2rgbex($params['pf_cat_backcolor'],'0.7');
		    	$_text_custom_css .= 'background-color: '.$params['pf_cat_backcolor'].'; background-color:'.$color_output['rgba'].'; ';
		    	$_text_custom_css1 .= 'background-color: '.$params['pf_cat_backcolor'].'; background-color:'.$color_output['rgba'].';';
		    	$_text_custom_css_main = ' pfwbg';
		    	$_text_custom_css_sub = ' pfwbg';
		    }else{
		    	$_text_custom_css_main = '';
		    	$_text_custom_css_sub = '';
		    }

		    if (isset($params['pf_cat_bgimg'][0])) {
		    	$bgimage_defined = wp_get_attachment_url($params['pf_cat_bgimg'][0]);

		    	$_text_custom_css .= 'background: url('.$bgimage_defined.');';
		    	$_text_custom_css .= 'background-position: '.$params['pf_cat_bgpos'].';';
		    	$_text_custom_css .= 'background-size: '.$params['pf_cat_bgsize'].';';
		    	$_text_custom_css .= 'background-repeat: '.$params['pf_cat_bgrepeat'].';';
		    	$_text_custom_css .= 'background-attachment: '.$params['pf_cat_bgattachment'].';';
		    	$_text_custom_css .= 'height: '.$params['pf_cat_headerheight'].'px;';
		    	$_text_custom_css .= 'color: '.$params['pf_cat_textcolor'].';';
		    }

		    $_text_custom_css .= '"';
		    $_text_custom_css1 .= '"';


		    $titletext = '';
		    if(empty($params['taxname'])){
			    if(is_category() || is_archive()){
					$categ = get_category_by_path("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",false);
					
					if (empty($categ)) {
						$categ = esc_sql(get_query_var('cat'));
						$titletext = get_cat_name( $categ );
					}else{
						if (isset($categ)) {
							$titletext = $categ->name;
						}
					}
				}elseif (is_tag()) {
					$titletext = single_tag_title('',false);
				}else{
			    	$titletext = get_the_title();
			    }
			}else{
				$titletext = $params['taxname'];
				$titlesubtext = $params['taxinfo'];
			}


			
			echo '
			<section role="pageheader" class="pf-defaultpage-header"'.$_text_custom_css.'>';
				if ($setup43_themecustomizer_headerbar_shadowopt != 0) {
					echo '<div class="pfheaderbarshadow'.$setup43_themecustomizer_headerbar_shadowopt.'"></div>';
				}
			echo '
				<div class="pf-container" style="height:100%">
					<div class="pf-row" style="height:100%">
						<div class="col-lg-12" style="height:100%">';
						
						
						echo '
							<div class="pf-titlebar-texts">
							<h1 class="main-titlebar-text'.$_text_custom_css_main.'"'.$_text_custom_css1.'>'.$titletext.'</h1>
							';
							if (!empty($titlesubtext)) {
								echo '<div class="sub-titlebar-text'.$_text_custom_css_sub.'"'.$_text_custom_css1.'>'.$titlesubtext.'</div>';
							}
							echo '
							</div>
							';

							if(empty($params['taxname'])){
								echo '<div class="pf-breadcrumbs clearfix'.$_text_custom_css_sub.'"'.$_text_custom_css1.'>'.pf_the_breadcrumb(array('_text_custom_css' => $_text_custom_css1,'_text_custom_css_main' => $_text_custom_css_main)).'</div>';
							}else{
								echo '<div class="pf-breadcrumbs clearfix'.$_text_custom_css_sub.'"'.$_text_custom_css1.'>'.pf_the_breadcrumb(array('taxname'=>$params['taxnamebr'],'_text_custom_css' => $_text_custom_css1,'_text_custom_css_main' => $_text_custom_css_main)).'</div>';
							}
							
							echo '
						</div>
					</div>
				</div>
			</section>';
		
		}
	}
	if (!function_exists('PFPageNotFound')) {
		function PFPageNotFound(){
		  ?>
			<section role="main">
		        <div class="pf-container">
		            <div class="pf-row">
		                <div class="col-lg-12">
		                 
		                    <form method="get" class="form-search" action="<?php echo esc_url(home_url()); ?>" data-ajax="false">
		                    <div class="pf-notfound-page animated flipInY">
		                        <h3><?php esc_html_e( 'Sorry!', 'pointfindert2d' ); ?></h3>
		                        <h4><?php esc_html_e( 'Nothing found...', 'pointfindert2d' ); ?></h4><br>
		                        <p class="text-lightblue-2"><?php esc_html_e( 'You better try to search', 'pointfindert2d' ); ?>:</p>
		                        <div class="row">
		                            <div class="pfadmdad input-group col-sm-4 col-sm-offset-4">
		                                <i class="pfadmicon-glyph-386"></i>
		                                <input type="text" name="s" class="form-control" onclick="this.value='';"  onfocus="if(this.value==''){this.value=''};" onblur="if(this.value==''){this.value=''};" value="<?php esc_html_e( 'Search', 'pointfindert2d' ); ?>">
		                                <span class="input-group-btn">
		                                    <button onc class="btn btn-success" type="submit"><?php esc_html_e( 'Search', 'pointfindert2d' ); ?></button>
		                                  </span>
		                            </div>
		                        </div><br>
		                        <a class="btn btn-primary btn-sm" href="<?php echo esc_url(home_url()); ?>"><i class="pfadmicon-glyph-857"></i><?php esc_html_e( 'Return Home', 'pointfindert2d' ); ?></a>
		                    </div>
		                    </form>
		                
		                </div>
		            </div>
		        </div>
		    </section>
		  <?php
		}
	}
	if (!function_exists('PFLoginWidget')) {
		function PFLoginWidget(){
		  ?>
			<section role="main">
		        <div class="pf-container">
		            <div class="pf-row">
		                <div class="col-lg-12">
		                 
		                    <div class="pf-notlogin-page animated flipInY">
		                        <h3><?php esc_html_e( 'Sorry!', 'pointfindert2d' ); ?></h3>
		                        <h4><?php esc_html_e( 'You must login to see this page.', 'pointfindert2d' ); ?></h4><br>
		                    </div>
		                    <script>
					       (function($) {
				  			"use strict";
					       	$(function(){
					       		$.pfOpenLogin('open','login');
					       	})
					       })(jQuery);
					       </script>
		                
		                </div>
		            </div>
		        </div>
		    </section>
		  <?php
		}
	}
/**
*End:Page Functions
**/





/**
*Start: Breadcrumbs
**/
	if (!function_exists('pf_the_breadcrumb')) {
		function pf_the_breadcrumb($params = array()) {

			$defaults = array( 
		        'taxname' => '',
		        '_text_custom_css' => '',
		        '_text_custom_css_main' => ''
		    );

			$params = array_merge($defaults, $params);

			$_text_custom_css_main = (!empty($params['_text_custom_css_main']))?$params['_text_custom_css_main']:'';
			$_text_custom_css = (!empty($params['_text_custom_css']))?$params['_text_custom_css']:'';



			$mpost_id = get_the_id();

			$setup3_modulessetup_breadcrumbs = PFSAIssetControl('setup3_modulessetup_breadcrumbs','','1');
			if ($setup3_modulessetup_breadcrumbs == 1) {
				
				$act_ok = 1;
				if (function_exists('is_bbpress')) {
					if(!is_bbpress()){ $act_ok = 1;}else{$act_ok = 0;}
				}
				if($act_ok == 1){
					$output = '';
			        $output .= '<ul id="pfcrumbs" class="'.trim($_text_custom_css_main).'"'.trim($_text_custom_css).'>';

			        if (!is_home()) {
			                $output .= '<li><a href="';
			                $output .= esc_url(home_url());
			                $output .= '">';
			                $output .= esc_html__('Home','pointfindert2d');
			                $output .= "</a></li>";
			                if (is_category() || is_single()) {
			                 

			                        $post_type = get_post_type();
									$setup3_pointposttype_pt1 = PFSAIssetControl('setup3_pointposttype_pt1','','pfitemfinder');

									switch ($post_type) {
										case $setup3_pointposttype_pt1:
											$categories = get_the_terms($mpost_id,'pointfinderltypes');
											$output2 = '';
									
											if($categories){
												$cat_count = count($categories);

												foreach($categories as $category) {
													if (!empty($category->parent)) {
														$term_parent_name = get_term_by('id', $category->parent, 'pointfinderltypes','ARRAY_A');
														$get_termname = $term_parent_name['name'].' / '.$category->name;
														$output2 .= '<li>';
														$output2 .= '<a href="'.get_term_link( $category->parent, 'pointfinderltypes' ).'" title="' . esc_attr( sprintf( esc_html__( "View all posts in %s","pointfindert2d" ), $term_parent_name['name']) ) . '">'.$term_parent_name['name'].'</a>';
														$output2 .= '</li>';
													}

													$output2 .= '<li>';
													$output2 .= '<a href="'.get_term_link( $category->term_id,'pointfinderltypes' ).'" title="' . esc_attr( sprintf( esc_html__( "View all posts in %s","pointfindert2d" ), $category->name ) ) . '">'.$category->name.'</a>';
													$output2 .= '</li>';
												}
											$output .= trim($output2);
											}
											break;

										case 'post':

											$list_cats = get_category_by_path("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",false);
											$ci = 0;
											if (isset($list_cats)) {
												$output .= '<li>'.$list_cats->name.'</li>';
											}
											
											break;
										default:
											$list_cats = get_the_category();
											$ci = 0;
											foreach ($list_cats as $list_cat) {
												if($ci < 2){
													$output .= '<li>'.$list_cat->name.'</li>';
												}
												$ci++;
											}

									}

			                        if (is_single()) {
			                                $output .= "<li>";
			                                $output .= '<a href="'.get_permalink().'" title="'.get_the_title().'">'.get_the_title().'</a>';
			                                $output .= '</li>';
			                        }
			                } elseif (is_page()) {
			                		
									$parents = get_post_ancestors($mpost_id);
									$parents = array_reverse($parents);
									if (!empty($parents)) {
										foreach ($parents as $key => $value) {
											$output .= '<li>';
			                        		$output .= '<a href="'.get_permalink($value).'" title="'.get_the_title($value).'">'.get_the_title($value).'</a>';
			                        		$output .= '</li>';
										}
									}
							  
			                        $output .= '<li>';
			                        $output .= '<a href="'.get_permalink().'" title="'.get_the_title().'">'.get_the_title().'</a>';
			                        $output .= '</li>';
			                } elseif (is_tax()) {
			                	$output .= "<li>";
	                            $output .= $params['taxname'];
	                            $output .= '</li>';
			                }elseif (is_tag()) {
			                	$output .= "<li>";
			                	$output .= single_tag_title('',false);
			                	$output .= '</li>';
			                }

			        
			        }elseif (is_day()) {$output .="<li>".esc_html__('Archive for','pointfindert2d')." "; get_the_time('F jS, Y'); $output .='</li>';
			        }elseif (is_month()) {$output .="<li>".esc_html__('Archive for','pointfindert2d')." "; get_the_time('F, Y'); $output .='</li>';
			        }elseif (is_year()) {$output .="<li>".esc_html__('Archive for','pointfindert2d')." "; get_the_time('Y'); $output .='</li>';
			        }elseif (is_author()) {$output .="<li>".esc_html__('Author Archive','pointfindert2d').""; $output .='</li>';
			        }elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {$output .= "<li>".esc_html__('Blog Archives','pointfindert2d').""; $output .='</li>';
			        }elseif (is_search()) {$output .="<li>".esc_html__('Search Results','pointfindert2d').""; $output .='</li>';}
			        $output .= '</ul>';

			        return $output;
			    }
			}
		}
	}
/**
*End: Breadcrumbs
**/
if (!function_exists('pf_get_term_top_most_parent')) {
	function pf_get_term_top_most_parent($term_id, $taxonomy){
	    $parent  = get_term_by( 'id', $term_id, $taxonomy);
	    $k = 0;
	    if (!empty($parent)) {
		    while ($parent->parent != '0'){
		        $term_id = $parent->parent;

		        $parent  = get_term_by( 'id', $term_id, $taxonomy);
		        $k++;
		    } 
		}

	    return array('parent'=>$parent->term_id, 'level'=>$k);
	}
}

if (!function_exists('pf_get_term_top_parent')) {
	function pf_get_term_top_parent($term_id, $taxonomy){
	    $parent  = get_term_by( 'id', $term_id, $taxonomy);
	    return $parent->parent;
	}
}

if (!function_exists('pointfinder_get_vc_version')) {
	function pointfinder_get_vc_version(){
		$vc_version_current = 0;
		if(function_exists('vc_set_as_theme')){
			if ( ! function_exists( 'get_plugins' ) ){
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}
			$plugin_folder = get_plugins( '/');
			$plugin_file = 'js_composer/js_composer.php';
			if (isset($plugin_folder[$plugin_file]['Version'])) {
				$vc_version_current = $plugin_folder[$plugin_file]['Version'];
			}
		}
		return $vc_version_current;
	}
}
if (!function_exists('pf_theme_render_title')) {
	function pf_theme_render_title() {
	    ?>
	    <title><?php wp_title( '|', true, 'right' ); ?></title>
	    <?php
	}
}

if (!function_exists('pf_get_condition_color')) {
	function pf_get_condition_color($value){
		$retunarr = array();
		if (!empty($value)) {
			$pointfindercondition_vars = get_option('pointfindercondition_vars');
			if (isset($pointfindercondition_vars[$value]['pf_condition_bg'])) {
				$retunarr['bg'] = $pointfindercondition_vars[$value]['pf_condition_bg'];
			}
			if (isset($pointfindercondition_vars[$value]['pf_condition_text'])) {
				$retunarr['cl'] = $pointfindercondition_vars[$value]['pf_condition_text'];
			}
		}
		return $retunarr;
	}
}


function pointfinder_price_output_set($value){trigger_error(esc_html__("Deprecated function called.","pointfindert2d"), E_USER_NOTICE);}

/*
* Added with v1.7.2 for check relation between listing type and ta taxonomies.
*/
if (!function_exists('pointfinder_features_tax_output_check')) {
	function pointfinder_features_tax_output_check($term_parent,$listting_id,$taxonomy){

		$output_check = '';
		$controlvalue = 0;
		
		switch ($taxonomy) {
			case 'pointfinderfeatures':
				$controlvalue = PFSAIssetControl('setup4_sbf_c1','','1');
				break;
		}


		if (!empty($term_parent) && !empty($listting_id)) {
			if (is_array($term_parent)) {
				if (in_array($listting_id, $term_parent)) {$output_check = 'ok';}else{$output_check = 'not';}
			}else{
				if ($listting_id == $term_parent) {$output_check = 'ok';}else{$output_check = 'not';}
			}
		}elseif (empty($term_parent) && empty($listting_id)) {
			$output_check = 'ok';
		}elseif (empty($term_parent) && !empty($listting_id)) {
			if ($controlvalue == 1) {$output_check = 'ok';}else{$output_check = 'not';}
		}elseif (!empty($term_parent) && empty($listting_id)) {
			$output_check = 'not';
		}
		return $output_check;
	}
}


/*
* Added with v1.7.2 moved from new-features-pt.php
*/
if (!function_exists('pointfinder_taxonomy_connection_field_creator')) {
	function pointfinder_taxonomy_connection_field_creator($selected_value){

		echo '<tr class="form-field"><th scope="row" valign="top"></th><td>';
	    echo '<section>';  


	    
	    $listdefault = (isset($selected_value))?$selected_value:'';
	    $output_options = $output = "";
	       
	    $fieldvalues = get_terms('pointfinderltypes',array('hide_empty'=>false)); 
	    	
	    foreach( $fieldvalues as $parentfieldvalue){
	        
	        if($parentfieldvalue->parent == 0){

	        	$fieldtaxSelectedValueParent = 0;

	        	if(!empty($listdefault)){
		            if(is_array($listdefault)){
		                if(in_array($parentfieldvalue->term_id, $listdefault)){ $fieldtaxSelectedValueParent = 1;}
		            }else{
		                if(strcmp($listdefault,$parentfieldvalue->term_id) == 0){ $fieldtaxSelectedValueParent = 1;}
		            }
		        }

		        if($fieldtaxSelectedValueParent == 1){
		            $output_options .= '<option value="'.$parentfieldvalue->term_id.'" selected class="pftitlebold">&nbsp;'.$parentfieldvalue->name.'</option>';
		        }else{
		            $output_options .= '<option value="'.$parentfieldvalue->term_id.'" class="pftitlebold">&nbsp;'.$parentfieldvalue->name.'</option>';
		        }

		        foreach( $fieldvalues as $firstchild_fieldvalue){
					if($firstchild_fieldvalue->parent == $parentfieldvalue->term_id){
						$fieldtaxSelectedValueFC = 0;

			        	if(!empty($listdefault)){
				            if(is_array($listdefault)){
				                if(in_array($firstchild_fieldvalue->term_id, $listdefault)){ $fieldtaxSelectedValueFC = 1;}
				            }else{
				                if(strcmp($listdefault,$firstchild_fieldvalue->term_id) == 0){ $fieldtaxSelectedValueFC = 1;}
				            }
				        }

				        if($fieldtaxSelectedValueFC == 1){
				            $output_options .= '<option value="'.$firstchild_fieldvalue->term_id.'" selected>&nbsp;&nbsp;-&nbsp;'.$firstchild_fieldvalue->name.'</option>';
				        }else{
				            $output_options .= '<option value="'.$firstchild_fieldvalue->term_id.'">&nbsp;&nbsp;-&nbsp;'.$firstchild_fieldvalue->name.'</option>';
				        }

				        foreach( $fieldvalues as $secondchild_fieldvalue){
							if($secondchild_fieldvalue->parent == $firstchild_fieldvalue->term_id){
								$fieldtaxSelectedValueSC = 0;

					        	if(!empty($listdefault)){
						            if(is_array($listdefault)){
						                if(in_array($secondchild_fieldvalue->term_id, $listdefault)){ $fieldtaxSelectedValueSC = 1;}
						            }else{
						                if(strcmp($listdefault,$secondchild_fieldvalue->term_id) == 0){ $fieldtaxSelectedValueSC = 1;}
						            }
						        }

						        if($fieldtaxSelectedValueSC == 1){
						            $output_options .= '<option value="'.$secondchild_fieldvalue->term_id.'" selected>&nbsp;&nbsp;--&nbsp;'.$secondchild_fieldvalue->name.'</option>';
						        }else{
						            $output_options .= '<option value="'.$secondchild_fieldvalue->term_id.'">&nbsp;&nbsp;--&nbsp;'.$secondchild_fieldvalue->name.'</option>';
						        }
							}
						}
					}
				}
	        }     
	    }
	    
	    echo '<div class="pf_fr_inner" data-pf-parent="">';
	    echo '<label for="pfupload_listingtypes" class="lbl-text">'.esc_html__("Connection with Listing Type","pointfindert2d").':</label>';
	    echo '<label class="lbl-ui select">
	    <select multiple name="pfupload_listingtypes[]" id="pfupload_listingtypes">
	    ';
	    echo $output_options;
	    echo '
	    </select>
	    </label>';

	    echo '</div>';
	    echo '</section>';


	    echo '
	    <script>
	    jQuery(function(){
	        jQuery("#pfupload_listingtypes").multiselect({
	            buttonWidth: "300px",
	            disableIfEmpty: true,
	            nonSelectedText: "'.esc_html__("Please select","pointfindert2d").'",
	            nSelectedText: "'.esc_html__("selected","pointfindert2d").'",
	            allSelectedText: "'.esc_html__("All selected","pointfindert2d").'",
	            selectAllText: "'.esc_html__("Select all","pointfindert2d").'",
	            includeSelectAllOption: true,
	            enableFiltering: true,
	            filterPlaceholder: "'.esc_html__("Search","pointfindert2d").'",
	            enableFullValueFiltering: true,
	            enableCaseInsensitiveFiltering: true,
	            maxHeight: 300
	        });

	        jQuery("#addtag #submit").on("click",function(){
	        	jQuery(document).ajaxComplete(function() {
	        		jQuery("#pfupload_listingtypes").multiselect("deselectAll", false);
	        		jQuery("#pfupload_listingtypes").multiselect("updateButtonText");
	        	});
	        });
	    });
	    </script>
	    </td>
	    </tr>
	    ';
	}
}

if (!function_exists('pointfinder_getCurrencySymbol')) {
	function pointfinder_getCurrencySymbol($currency){	
		$locale = "";
		
		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			$locale = Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
		}
		
		if (empty($locale)) {$locale = 'en_US';}

	    $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
	    
	    $withCurrency = $formatter->formatCurrency(0, $currency);
	    $formatter->setPattern(str_replace('¤', '', $formatter->getPattern()));
	    $withoutCurrency = $formatter->formatCurrency(0, $currency);

	    return str_replace($withoutCurrency, '', $withCurrency);    
	}
}


if (!function_exists('pointfinder_check_priceformatted')) {
	function pointfinder_check_priceformatted($value){
		if (strpos($value, '.') == false && strpos($value, ',') == false) {
			return false;
		}else{
			return true;
		}
	}
}

if (!function_exists('pointfinder_reformat_pricevalue_for_frontend')) {
	function pointfinder_reformat_pricevalue_for_frontend($value){
		if (empty($value)) {return $value;}

		$setup20_decimals_new = PFSAIssetControl('setup20_decimals_new','',2);
		$setup20_decimalpoint = PFSAIssetControl('setup20_paypalsettings_decimalpoint','','.');
		$setup20_thousands = PFSAIssetControl('setup20_paypalsettings_thousands','',',');
		$price_short = PFSAIssetControl('setup20_paypalsettings_paypal_price_short','','$');
	    $price_pref = PFSAIssetControl('setup20_paypalsettings_paypal_price_pref','',1);

	    if (pointfinder_check_priceformatted($value) === false) {
	    	$value_formatted = number_format($value,$setup20_decimals_new,$setup20_decimalpoint,$setup20_thousands);
	    }else{
	    	$value_formatted = $value;
	    }

	    if (strpos($value, $price_short) === false) {
	    	if ($price_pref == 1) {
		
				return $price_short.$value_formatted;
			
			}else{

				return $value_formatted.$price_short;

			}
	    }else{
	    	return $value_formatted;
	    }
	}
}

if (!function_exists('pointfinder_check_tag')) {
	function pointfinder_check_tag($tag_id){
		$setup3_pointposttype_pt1 = PFSAIssetControl('setup3_pointposttype_pt1','','pfitemfinder');
		$customquery = new WP_Query( array( "post_type" => $setup3_pointposttype_pt1, "tag_id" => $tag_id ) );
		wp_reset_postdata();
		if ($customquery->found_posts > 0){
			return true;
		}else{
			return false;
		}
	}
}

if (!function_exists('pointfinder_getUserIP')) {
	function pointfinder_getUserIP(){
	    $client  = @$_SERVER['HTTP_CLIENT_IP'];
	    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
	    $remote  = $_SERVER['REMOTE_ADDR'];

	    if(filter_var($client, FILTER_VALIDATE_IP)){
	        $ip = $client;
	    }elseif(filter_var($forward, FILTER_VALIDATE_IP)){
	        $ip = $forward;
	    }else{
	        $ip = $remote;
	    }
	    return $ip;
	}
}


/** 
*Start: ajax-infowindow.php Functions
**/
	if (!function_exists('PFCheckMultipleMarker')) {
		function PFCheckMultipleMarker($coordinates,$id){
			if (PFControlEmptyArr($coordinates)) {
				$setup3_pointposttype_pt1 = PFSAIssetControl('setup3_pointposttype_pt1','','pfitemfinder');
				$args = array('post_type' => $setup3_pointposttype_pt1,'meta_query' => array(array('key' => 'webbupointfinder_items_location','value' => ''.$coordinates[0].','.$coordinates[1].'')),'fields' => 'ids', 'post_status' => 'publish', 'posts_per_page'=>-1);
				$q_vid = new WP_Query( $args );
				if ( ! empty( $q_vid->posts ) ) {
					$posts = $q_vid->posts;
					wp_reset_postdata();
					if(count($posts) > 1){
						return $posts;
					}else{
						return array();
					}
				}else{
					wp_reset_postdata();
					return array();
				}
			}else{
				return array();
			}
		}
	}

	if (!function_exists('PFIF_ItemDetails')) {
		function PFIF_ItemDetails($id){
			$ItemDetailArr = array();
			$setup10_infowindow_img_width  = PFSAIssetControl('setup10_infowindow_img_width','','154');
			$setup10_infowindow_img_height  = PFSAIssetControl('setup10_infowindow_img_height','','136');
			$setup10_infowindow_hide_image  = PFSAIssetControl('setup10_infowindow_hide_image','','0');
			$general_retinasupport = PFSAIssetControl('general_retinasupport','','0');
			if($general_retinasupport == 1){$pf_retnumber = 2;}else{$pf_retnumber = 1;}
			
			$setup10_infowindow_img_width  = $setup10_infowindow_img_width*$pf_retnumber;
			$setup10_infowindow_img_height  = $setup10_infowindow_img_height*$pf_retnumber;
		
			$itemvars[$id]['featured_image']  = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'full' );
			if( $setup10_infowindow_hide_image == 0){
				$ItemDetailArr['featured_image_big'] = $itemvars[$id]['featured_image'][0];
				if($itemvars[$id]['featured_image'][0] != '' && $itemvars[$id]['featured_image'][0] != NULL){$ItemDetailArr['featured_image'] = aq_resize($itemvars[$id]['featured_image'][0],$setup10_infowindow_img_width,$setup10_infowindow_img_height,true);}else{$ItemDetailArr['featured_image'] = '';}
				
				if($ItemDetailArr['featured_image'] === false) {
					if($general_retinasupport == 1){
						$ItemDetailArr['featured_image'] = aq_resize($itemvars[$id]['featured_image'][0],$setup10_infowindow_img_width/2,$setup10_infowindow_img_height/2,true);
						if($ItemDetailArr['featured_image'] === false) {
							$ItemDetailArr['featured_image'] = $itemvars[$id]['featured_image'][0];
						}
					}else{
						$ItemDetailArr['featured_image'] = '';
					}
					
				}
			}else{
				$ItemDetailArr['featured_image'] = '';
				$ItemDetailArr['featured_image_big'] = '';
			}
			$ItemDetailArr['if_title'] = html_entity_decode(get_the_title($id));
			$ItemDetailArr['featured_video'] =  get_post_meta( $id, 'webbupointfinder_item_video', true );
			$ItemDetailArr['if_link'] = get_permalink($id);
			$ItemDetailArr['if_address'] = esc_html(get_post_meta( $id, 'webbupointfinder_items_address', true ));
			
			return $ItemDetailArr;
		}
	}

	if (!function_exists('PFIF_OutputData')) {
		function PFIF_OutputData($itemvars,$id){
			$output_data = '';
			$st22srlinknw = PFSAIssetControl('st22srlinknw','','0');
			$targetforitem = '';
			if ($st22srlinknw == 1) {
				$targetforitem = ' target="_blank"';
			}
			$setup10_infowindow_animation_image  = PFSAIssetControl('setup10_infowindow_animation_image','','WhiteSquare');
			$setup10_infowindow_hover_image  = PFSAIssetControl('setup10_infowindow_hover_image','','0');
			$setup10_infowindow_hover_video  = PFSAIssetControl('setup10_infowindow_hover_video','','0');
			$setup10_infowindow_hide_address  = PFSAIssetControl('setup10_infowindow_hide_address','','0');
			
			$setup16_featureditemribbon_hide = PFSAIssetControl('setup16_featureditemribbon_hide','','1');
			$setup4_membersettings_favorites = PFSAIssetControl('setup4_membersettings_favorites','','1');

			if (is_user_logged_in()) {
				$user_favorites_arr = get_user_meta( get_current_user_id(), 'user_favorites', true );
				if (!empty($user_favorites_arr)) {
					$user_favorites_arr = json_decode($user_favorites_arr,true);
				}else{
					$user_favorites_arr = array();
				}
			}			

			$pfbuttonstyletext = 'pfHoverButtonStyle ';
						
			switch($setup10_infowindow_animation_image){
				case 'WhiteRounded':
					$pfbuttonstyletext .= 'pfHoverButtonWhite pfHoverButtonRounded';
					break;
				case 'BlackRounded':
					$pfbuttonstyletext .= 'pfHoverButtonBlack pfHoverButtonRounded';
					break;
				case 'WhiteSquare':
					$pfbuttonstyletext .= 'pfHoverButtonWhite pfHoverButtonSquare';
					break;
				case 'BlackSquare':
					$pfbuttonstyletext .= 'pfHoverButtonBlack pfHoverButtonSquare';
					break;
				
			}
			
			$single_point = 0;

			if(isset($_POST['single']) && !empty($_POST['single'])){
				$single_point = esc_attr($_POST['single']);
			}
			$disable_itempr = (!empty($_POST['disable']))?esc_attr($_POST['disable']):0;
			
			$featured_status_ribbon = get_post_meta( $id, 'webbupointfinder_item_featuredmarker', true );
			
			if($itemvars['featured_image'] != ''){
				$output_data .= "<div class='wpfimage clearfix'><div class='wpfimage-wrapper clearfix'>";
					$setup10_infowindow_hide_ratings = PFSAIssetControl('setup10_infowindow_hide_ratings','','1');
					if($setup10_infowindow_hover_image == 1 && $single_point == 0){
						$output_data .= "<a href='".$itemvars['if_link']."'".$targetforitem."><img src='".$itemvars['featured_image'] ."' alt='' /></a>";
						
						if($setup4_membersettings_favorites == 1 && $disable_itempr != 1){
											
							$fav_check = 'false';
							$favtitle_text = esc_html__('Add to Favorites','pointfindert2d');

							if (is_user_logged_in() && count($user_favorites_arr)>0) {
								if (in_array($id, $user_favorites_arr)) {
									$fav_check = 'true';
									$favtitle_text = esc_html__('Remove from Favorites','pointfindert2d');
								}
							}

							$output_data .= '<div class="RibbonCTR">
	                            <span class="Sign"><a class="pf-favorites-link" data-pf-num="'.$id.'" data-pf-active="'.$fav_check.'" data-pf-item="false" title="'.$favtitle_text.'"><i class="pfadmicon-glyph-629"></i></a>
	                            </span>
	                            <span class="Triangle"></span>
	                        </div>';
	                    }

	                    $setup3_pt14_check = PFSAIssetControl('setup3_pt14_check','',0);
	                    if ($setup3_pt14_check == 1) {

	                    	$item_defaultvalue = wp_get_post_terms($id, 'pointfinderconditions', array("fields" => "all"));
							if (isset($item_defaultvalue[0]->term_id)) {																
	            				$contidion_colors = pf_get_condition_color($item_defaultvalue[0]->term_id);

	            				$condition_c = (isset($contidion_colors['cl']))? $contidion_colors['cl']:'#494949';
	            				$condition_b = (isset($contidion_colors['bg']))? $contidion_colors['bg']:'#f7f7f7';

	                			$output_data .= '
	                			<div class="pfribbon-wrapper-featured3" style="color:'.$condition_c.';background-color:'.$condition_b.'">
	                			<div class="pfribbon-featured3">'.$item_defaultvalue[0]->name.'</div>
	                			</div>';
	            			}

	                    	
	                    }

	                    if ($setup16_featureditemribbon_hide != 0) {
	                    	if (!empty($featured_status_ribbon)) {
	                			$output_data .= '
	                			<div class="pfribbon-wrapper-featured2">
	                			<div class="pfribbon-featured2">'.esc_html__('FEATURED','pointfindert2d').'</div>
	                			</div>';
	                    	}

	                    }

	                    if (PFREVSIssetControl('setup11_reviewsystem_check','','0') == 1 && $setup10_infowindow_hide_ratings == 0) {
	                    	$setup22_searchresults_hide_re = PFREVSIssetControl('setup22_searchresults_hide_re','','1');
	                    	$setup16_reviewstars_nrtext = PFREVSIssetControl('setup16_reviewstars_nrtext','','0');
	                    	if ($setup22_searchresults_hide_re == 0) {
	                    		$reviews = pfcalculate_total_review($id);
	                    		
	                    		if (!empty($reviews['totalresult'])) {
	                    			$rev_total_res = round($reviews['totalresult']);
	                    			
	                    			$output_data .= '<div class="pfrevstars-wrapper-review pf-infowindow-review">';
	                    			$output_data .= ' <div class="pfrevstars-review">';
	                    				for ($ri=0; $ri < $rev_total_res; $ri++) { 
	                    					$output_data .= '<i class="pfadmicon-glyph-377"></i>';
	                    				}
	                    				for ($ki=0; $ki < (5-$rev_total_res); $ki++) { 
	                    					$output_data .= '<i class="pfadmicon-glyph-378"></i>';
	                    				}

	                    			$output_data .= '</div></div>';
	                    		}else{
	                    			if($setup16_reviewstars_nrtext == 0){
	                        			$output_data .= '<div class="pfrevstars-wrapper-review pf-infowindow-review">';
	                        			$output_data .= ' <div class="pfrevstars-review">'.esc_html__('Not rated.','pointfindert2d').'';
	                        			$output_data .= '</div></div>';
	                    			}
	                    		}
	                    	}

	                    }

					}elseif($setup10_infowindow_hover_image == 0 && $single_point == 0){
						$output_data .= "<img src='".$itemvars['featured_image'] ."' alt='' />";

						if($setup4_membersettings_favorites == 1 && $disable_itempr != 1){
											
							$fav_check = 'false';
							$favtitle_text = esc_html__('Add to Favorites','pointfindert2d');

							if (is_user_logged_in() && count($user_favorites_arr)>0) {
								if (in_array($id, $user_favorites_arr)) {
									$fav_check = 'true';
									$favtitle_text = esc_html__('Remove from Favorites','pointfindert2d');
								}
							}

							$output_data .= '<div class="RibbonCTR">
	                            <span class="Sign"><a class="pf-favorites-link" data-pf-num="'.$id.'" data-pf-active="'.$fav_check.'" data-pf-item="false" title="'.$favtitle_text.'"><i class="pfadmicon-glyph-629"></i></a>
	                            </span>
	                            <span class="Triangle"></span>
	                        </div>';
	                    }

	                    if($disable_itempr != 1){
						$buton_q_text = ($setup10_infowindow_hover_video != 1 && !empty($itemvars['featured_video']))? 'pfStyleV':'pfStyleV2';
						$output_data .= '<div class="pfImageOverlayH"></div><div class="pfButtons '.$buton_q_text.' pfStyleVAni"><span class="'.$pfbuttonstyletext.' clearfix"><a class="pficon-imageclick" data-pf-link="'.$itemvars['featured_image_big'].'" style="cursor:pointer"><i class="pfadmicon-glyph-684"></i></a></span>';
						
						if($setup10_infowindow_hover_video != 1 && !empty($itemvars['featured_video'])){			
						$output_data .= '<span class="'.$pfbuttonstyletext.'"><a class="pficon-videoclick" data-pf-link="'.$itemvars['featured_video'] .'" style="cursor:pointer"><i class="pfadmicon-glyph-573"></i></a></span>';
						}
						
						$output_data .='<span class="'.$pfbuttonstyletext.'"><a href="'.$itemvars['if_link'].'"'.$targetforitem.'><i class="pfadmicon-glyph-794"></i></a></span></div>';
						}

						$setup3_pt14_check = PFSAIssetControl('setup3_pt14_check','',0);
	                    if ($setup3_pt14_check == 1) {

	                    	$item_defaultvalue = wp_get_post_terms($id, 'pointfinderconditions', array("fields" => "all"));
							if (isset($item_defaultvalue[0]->term_id)) {																
	            				$contidion_colors = pf_get_condition_color($item_defaultvalue[0]->term_id);

	            				$condition_c = (isset($contidion_colors['cl']))? $contidion_colors['cl']:'#494949';
	            				$condition_b = (isset($contidion_colors['bg']))? $contidion_colors['bg']:'#f7f7f7';

	                			$output_data .= '
	                			<div class="pfribbon-wrapper-featured3" style="color:'.$condition_c.';background-color:'.$condition_b.'">
	                			<div class="pfribbon-featured3">'.$item_defaultvalue[0]->name.'</div>
	                			</div>';
	            			}

	                    	
	                    }



						if ($setup16_featureditemribbon_hide != 0) {
	                    	if (!empty($featured_status_ribbon)) {
	                			$output_data .= '
	                			<div class="pfribbon-wrapper-featured2">
	                			<div class="pfribbon-featured2">'.esc_html__('FEATURED','pointfindert2d').'</div>
	                			</div>';
	                    	}

	                    }


	                    if (PFREVSIssetControl('setup11_reviewsystem_check','','0') == 1 && $setup10_infowindow_hide_ratings == 0) {
	                    	$setup22_searchresults_hide_re = PFREVSIssetControl('setup22_searchresults_hide_re','','1');
	                    	$setup16_reviewstars_nrtext = PFREVSIssetControl('setup16_reviewstars_nrtext','','0');

	                    	if ($setup22_searchresults_hide_re == 0) {

	                    		$reviews = pfcalculate_total_review($id);
	                    		if (!empty($reviews['totalresult'])) {
	                    			$rev_total_res = round($reviews['totalresult']);
	                    			$output_data .= '<div class="pfrevstars-wrapper-review pf-infowindow-review">';
	                    			$output_data .= ' <div class="pfrevstars-review">';
	                    				for ($ri=0; $ri < $rev_total_res; $ri++) { 
	                    					$output_data .= '<i class="pfadmicon-glyph-377"></i>';
	                    				}
	                    				for ($ki=0; $ki < (5-$rev_total_res); $ki++) { 
	                    					$output_data .= '<i class="pfadmicon-glyph-378"></i>';
	                    				}

	                    			$output_data .= '</div></div>';
	                    		}else{
	                    			if($setup16_reviewstars_nrtext == 0){
	                        			$output_data .= '<div class="pfrevstars-wrapper-review pf-infowindow-review">';
	                        			$output_data .= ' <div class="pfrevstars-review">  '.esc_html__('Not rated yet.','pointfindert2d').'';
	                        			$output_data .= '</div></div>';
	                    			}
	                    		}
	                    	}

	                    }

					}elseif($single_point == 1){
						$output_data .= "<img src='".$itemvars['featured_image'] ."'>";
					}
					
				$output_data .= "</div></div>";		
			}
			
			$limit_chr_title = PFSizeSIssetControl('setupsizelimitwordconf_general_infowindowtitle','',20);
			
			$title_extra = (strlen($itemvars['if_title'])<=$limit_chr_title ) ? '' : '...' ;
			$output_data .= "<div class='wpftext'>";
			$output_data .= "<span class='wpftitle'><a href='".$itemvars['if_link']."'".$targetforitem.">".mb_substr($itemvars['if_title'], 0, $limit_chr_title,'UTF-8').$title_extra."</a></span>";
			

			$limit_chr = PFSizeSIssetControl('setupsizelimitwordconf_general_infowindowaddress','',28);
			$limit_chr2 = $limit_chr*2;

			$setup10_infowindow_row_address = PFSAIssetControl('setup10_infowindow_row_address','','1');
			$addresscount = strlen($itemvars['if_address']);
			$addresscount = (strlen($itemvars['if_address'])<=$limit_chr ) ? '' : '...' ;
			
			if ($setup10_infowindow_row_address == 1) {
				$address_text = mb_substr($itemvars['if_address'], 0, $limit_chr,'UTF-8').$addresscount;
			}else{
				$address_text = mb_substr($itemvars['if_address'], 0, $limit_chr2,'UTF-8').$addresscount;
			}
			

			if($setup10_infowindow_hide_address == 0){
				$output_data .= "<span class='wpfaddress'>".$address_text."</span>";
			}
			
			$output_data .= "<span class='wpfdetail'>".PFIF_DetailText($id)."</span>";
			$output_data .= "</div>";
			return $output_data;
		}
	}

	if (!function_exists('PFIF_DetailText')) {
		function PFIF_DetailText($id){
			if(isset($_POST['cl']) && $_POST['cl']!=''){
				$pflang = esc_attr($_POST['cl']);
				
				if(function_exists('icl_t')) {
					if (!empty($pflang)) {
						do_action( 'wpml_switch_language', $pflang );
					}
				}
			}else{
				$pflang = '';
			}


			$setup10_infowindow_animation_image  = PFSAIssetControl('setup10_infowindow_animation_image','','WhiteSquare');
			$setup10_infowindow_hover_image  = PFSAIssetControl('setup10_infowindow_hover_image','','0');
			$setup10_infowindow_hover_video  = PFSAIssetControl('setup10_infowindow_hover_video','','0');
			$setup10_infowindow_hide_address  = PFSAIssetControl('setup10_infowindow_hide_address','','0');
			$setup10_infowindow_hide_lt  = PFSAIssetControl('setup10_infowindow_hide_lt','','0');
			$setup10_infowindow_hide_it  = PFSAIssetControl('setup10_infowindow_hide_it','','0');

			$setup14_multiplepointsettings_check = PFSAIssetControl('setup14_multiplepointsettings_check','','1');
			
			$pfstart = PFCheckStatusofVar('setup1_slides');
			
			if($pfstart == true){
				
				$if_detailtext = '<ul class="pfinfowindowdlist">';
					
					$post_listing_typeval = wp_get_post_terms( $id, 'pointfinderltypes', array('fields'=>'ids') );
					if (isset($post_listing_typeval[0])) {
						$post_listing_typeval = $post_listing_typeval[0];
					}else{
						$post_listing_typeval = '';
					}
					
					$setup1_slides = PFSAIssetControl('setup1_slides','','');	
					if(is_array($setup1_slides)){
						foreach ($setup1_slides as &$value) {
							
							$customfield_infocheck = PFCFIssetControl('setupcustomfields_'.$value['url'].'_sinfowindow','','0');
							$available_fields = array(1,2,3,4,5,7,8,14);
							
							if(in_array($value['select'], $available_fields) && $customfield_infocheck != 0){


								$PFTMParent = '';
								$ShowField = true;

								if(!empty($post_listing_typeval)){
									$PFTMParent = pf_get_term_top_most_parent($post_listing_typeval,'pointfinderltypes');
									$PFTMParent = (isset($PFTMParent['parent']))?$PFTMParent['parent']:'';
								}

								$ParentItem = PFCFIssetControl('setupcustomfields_'.$value['url'].'_parent','','0');
					
								if(PFControlEmptyArr($ParentItem) && function_exists('icl_t')){
									$NewParentItemArr = array();
									foreach ($ParentItem as $ParentItemSingle) {
										$NewParentItemArr[] = apply_filters('wpml_object_id', $ParentItemSingle, 'pointfinderltypes', TRUE);
									}
									$ParentItem = $NewParentItemArr;
								}

								
								/*If it have a parent element*/
								if(PFControlEmptyArr($ParentItem)){
									
									if(function_exists('icl_t')) {
										$PFCLang = PF_current_language();
										foreach ($ParentItem as $key => $valuex) {
											$ParentItem[$key] = icl_object_id($valuex,'pointfinderltypes',true,$PFCLang);
										}
									}

									$PFLTCOVars = get_option('pointfinderltypes_covars');

									if (isset($PFLTCOVars[$PFTMParent]['pf_subcatselect'])) {
										if ($PFLTCOVars[$PFTMParent]['pf_subcatselect'] == 1) {
											$post_listing_typeval = $PFTMParent;
										}
									}
									
									if(in_array($post_listing_typeval, $ParentItem) ){					
										$ShowField = true;										
									}else{
										$ShowField = false;
									}
								}

								if ($ShowField) {


									$PF_CF_Val = new PF_CF_Val($id);
									$ClassReturnVal = $PF_CF_Val->GetValue($value['url'],$id,$value['select'],$value['title']);
									if($ClassReturnVal != ''){
										$if_detailtext .= $ClassReturnVal;
									}
								}
							}
							
						}
					}
					unset($PF_CF_Val);
					
				
				if($setup10_infowindow_hide_lt == 0){
					
					$setup10_infowindow_hide_lt_text = PFSAIssetControl('setup10_infowindow_hide_lt_text','','');
					if($setup10_infowindow_hide_lt_text != ''){ $pfitemtext = $setup10_infowindow_hide_lt_text;}else{$pfitemtext = '';}
					$if_detailtext .= '<li class="pfiflitype pfliittype"><span class="wpfdetailtitle">'.$pfitemtext.'</span>';
					if($pfitemtext != ''){
						$if_detailtext .= ' '.GetPFTermInfoWindow($id,'pointfinderltypes',$pflang).'<span class="pf-fieldspace"></span></li>';
					}else{
						$if_detailtext .= ' <span class="wpfdetailtitle">'.GetPFTermInfoWindow($id,'pointfinderltypes',$pflang).'</span><span class="pf-fieldspace"></span></li>';
					}
				}
				
				
				
				if($setup10_infowindow_hide_it == 0){
					$setup10_infowindow_hide_it_text = PFSAIssetControl('setup10_infowindow_hide_it_text','','');
					if($setup10_infowindow_hide_it_text != ''){ $pfitemtext = $setup10_infowindow_hide_it_text;}else{$pfitemtext = '';}
					$if_detailtext .= '<li class="pfifittype pfliittype"><span class="wpfdetailtitle">'.$pfitemtext.'</span>';
					if($pfitemtext != ''){
						$if_detailtext .= ' '.GetPFTermInfoWindow($id,'pointfinderitypes',$pflang).'<span class="pf-fieldspace"></span></li>';
					}else{
						$if_detailtext .= ' <span class="wpfdetailtitle">'.GetPFTermInfoWindow($id,'pointfinderitypes',$pflang).'</span><span class="pf-fieldspace"></span></li>';
					}
				}
				
				$if_detailtext .= '</ul>';
			
			}
			unset($PF_CF_Val);
			return $if_detailtext;
		}
	}
/** 
*End: ajax-infowindow.php Functions
**/


/** 
*Start: ajax-listdata.php Functions
**/
	if (!function_exists('PFIF_SortFields_ld')) {
		function PFIF_SortFields_ld($searchvars,$orderarg_value = NULL){

			$pfstart = PFCheckStatusofVar('setup1_slides');
			$if_sorttext = '';
			if($pfstart == true){
				$if_sorttext = '';
				$available_fields = array(1,2,3,4,5,7,8,14);
				$setup1_slides = PFSAIssetControl('setup1_slides','','');	
				
				
				/* Prepare detailtext */
				foreach ($setup1_slides as &$value) {
					$stext = '';
					if(!empty($orderarg_value)){
						if(strcmp($orderarg_value,$value['url']) == 0){
							$stext = 'selected';
						}else{
							$stext = '';
						}
					}
					$Parentcheckresult = PFIF_CheckItemsParent_ld($value['url']);
					if(is_array($searchvars)){$res = PFIF_CheckFormVarsforExist_ld($searchvars,$Parentcheckresult);}else{$res = false;}
					$customfield_sortcheck = PFCFIssetControl('setupcustomfields_'.$value['url'].'_sortoption','','0');
					
					if($Parentcheckresult == 'none'){
						if(in_array($value['select'], $available_fields) && $customfield_sortcheck != 0){
							$if_sorttext .= '<option value="'.$value['url'].'" '.$stext.'>'.$value['title'].'</option>';
						}
					}else{
						if($res == true){
							$sortnamecheck = PFCFIssetControl('setupcustomfields_'.$value['url'].'_sortname','','');
							if($sortnamecheck == ''){$sortnamecheck = $value['title'];}
							if(in_array($value['select'], $available_fields) && $customfield_sortcheck != 0){
								$if_sorttext .= '<option value="'.$value['url'].'" '.$stext.'>'.$sortnamecheck.'</option>';
							}
						}
					
					}
					
				}
				
			}
			return $if_sorttext;
		}
	}
/** 
*End: ajax-listdata.php Functions
**/



/** 
*Start: ajax-featuresystem.php Functions
**/
	if (!function_exists('PFgetfield')) {
		function PFgetfield($params = array()){
		    $defaults = array( 
		        'fieldname' => '',
		        'fieldtype' => '',
		        'fieldtitle' => '',
		        'fieldsubtype' => '',
		        'fieldparent' => '',
		        'fieldtooltip' => '',
		        'fieldoptions' => '',
		        'fielddefault' => ''
		    );

		    $params = array_merge($defaults, $params);
		    	$output = '';

		    	if(PFControlEmptyArr($params['fieldparent'])){
	   				$output .= '<div class="pf_fr_inner" data-pf-parent="'.implode(',', $params['fieldparent']).'">';
	   			}else{
	   				$output .= '<div class="pf_fr_inner" data-pf-parent="">';
	   			}
				
		   		switch ($params['fieldtype']) {
		   			/**
		   			*Text
		   			**/
			   			case 'text':
				   			$output .= '
				   				<label for="'.$params['fieldname'].'" class="lbl-text">'.$params['fieldtitle'].'</label>
				                <label class="lbl-ui">';
				            if (is_array($params['fielddefault'])) {
				            	if(isset($params['fielddefault'][0])){
				            		$checkvalue = ($params['fielddefault'][0]!='') ? ' value="'.$params['fielddefault'][0].'"' : '' ;
				            	}else{
				            		$checkvalue = '';
				            	}
				            }else{
				            	$checkvalue = ($params['fielddefault']!='') ? ' value="'.$params['fielddefault'].'"' : '' ;
				            }
				            if($params['fieldsubtype'] == 4){
				            	$output .= '<input type="text" name="'.$params['fieldname'].'"  class="input"'.$checkvalue.' onKeyPress="return pointfinder_numbersonly(this, event)" />';
				            	
				            	$p_control = PFCFIssetControl('setupcustomfields_'.$params['fieldname'].'_currency_check','',0);

								if($p_control == 1){
					            	$CFPrefix = PFCFIssetControl('setupcustomfields_'.$params['fieldname'].'_currency_prefix','','');
									$CFSuffix = PFCFIssetControl('setupcustomfields_'.$params['fieldname'].'_currency_suffix','','');
					            }
					        }else{
					        	$output .= '<input type="text" name="'.$params['fieldname'].'" class="input"'.$checkvalue.' />';
					        }
					        if ($params['fieldtooltip']!='') {
					        	$output .= '<b class="tooltip left-bottom"><em>'.$params['fieldtooltip'].'</em></b>';
					        } 			        
				            $output .= '</label>';
			   			break;
		   			/**
		   			*TextArea
		   			**/
			   			case 'textarea':
				   			$output .= '
				   				<label for="'.$params['fieldname'].'" class="lbl-text">'.$params['fieldtitle'].'</label>
				                <label class="lbl-ui">';
				            if (is_array($params['fielddefault'])) {
				            	if(isset($params['fielddefault'][0])){
				            		$checkvalue = ($params['fielddefault'][0]!='') ? $params['fielddefault'][0] : '' ;
				            	}else{
				            		$checkvalue = '';
				            	}
				            }else{
				            	$checkvalue = ($params['fielddefault']!='') ? $params['fielddefault'] : '' ;
				            }
					        $output .= '<textarea id="desc" name="'.$params['fieldname'].'" class="textarea mini" >'.$checkvalue.'</textarea>';
					        
					        if ($params['fieldtooltip']!='') {
					        	$output .= '<b class="tooltip left-bottom"><em>'.$params['fieldtooltip'].'</em></b>';
					        } 			        
				            $output .= '</label>';
			   			break;
		   			/**
		   			*Select
		   			**/
			   			case 'select':
			   			$description = ($params['fieldtooltip']!='') ? ' <a href="javascript:;" class="info-tip" aria-describedby="helptooltip">?<span role="tooltip">'.$params['fieldtooltip'].'</span></a>' : '' ;
				   			$output .= '
				   				<label for="'.$params['fieldname'].'" class="lbl-text">'.$params['fieldtitle'].' '.$description.'</label>
				                <label class="lbl-ui select">';
				            
					        $output .= '<select name="'.$params['fieldname'].'">';

					        $output .= '<option value="">'.esc_html__('Please select','pointfindert2d').'</option>';

					        $ikk = 0;

					        foreach (pfstring2KeyedArray($params['fieldoptions']) as $key => $value) {
					        	

					        	if (is_array($params['fielddefault'])) {
					            	$checkvalue = (in_array($key,$params['fielddefault'])) ? ' selected' : '' ;
					            }else{
					            	$checkvalue = ($params['fielddefault']!='' && strcmp($params['fielddefault'], $key) == 0) ? ' selected' : '' ;
					            }

					        	if (function_exists('icl_t')) {
					            	 $exvalue = explode('=', icl_t('admin_texts_pfcustomfields_options', '[pfcustomfields_options][setupcustomfields_'.$params['fieldname'].'_rvalues]'.$ikk, $key.'='.$value));
					            	 if (isset($exvalue[1])) {
					            	 	$output .= '<option value="'.$key.'"'.$checkvalue.'>'.$exvalue[1].'</option>';
					            	 }else{
					            	 	$output .= '<option value="'.$key.'"'.$checkvalue.'>'.$value.'</option>';
					            	 }
					            }else{
					            	 $output .= '<option value="'.$key.'"'.$checkvalue.'>'.$value.'</option>';
					            }

					            $ikk++;
					        }
				            $output .= '</select>';
				            $output .= '</label>';
			   			break;
		   			/**
		   			*Select Multiple
		   			**/
			   			case 'selectmulti':
			   			
			   			$description = ($params['fieldtooltip']!='') ? ' <a href="javascript:;" class="info-tip" aria-describedby="helptooltip"> ? <span role="tooltip">'.$params['fieldtooltip'].'</span></a>' : '' ;
				   			$output .= '
				   				<label for="'.$params['fieldname'].'" class="lbl-text">'.$params['fieldtitle'].' '.$description.'</label>
				                <label class="lbl-ui select-multiple">';
				            
					        $output .= '<select name="'.$params['fieldname'].'[]" multiple="multiple" size="6">';

					        $ikk = 0;

					        foreach (pfstring2KeyedArray($params['fieldoptions']) as $key => $value) {
					        	
					        	if (is_array($params['fielddefault'])) {
					            	$checkvalue = (in_array($key,$params['fielddefault'])) ? ' selected' : '' ;
					            }else{
					            	$checkvalue = ($params['fielddefault']!='' && strcmp($params['fielddefault'], $key) == 0) ? ' selected' : '' ;
					            }

					        	/*$output .= '<option value="'.$key.'"'.$checkvalue.'>'.$value.'</option>';*/

					        	if (function_exists('icl_t')) {
					            	 $exvalue = explode('=', icl_t('admin_texts_pfcustomfields_options', '[pfcustomfields_options][setupcustomfields_'.$params['fieldname'].'_rvalues]'.$ikk, $key.'='.$value));
					            	 if (isset($exvalue[1])) {
					            	 	$output .= '<option value="'.$key.'"'.$checkvalue.'>'.$exvalue[1].'</option>';
					            	 }else{
					            	 	$output .= '<option value="'.$key.'"'.$checkvalue.'>'.$value.'</option>';
					            	 }
					            }else{
					            	 $output .= '<option value="'.$key.'"'.$checkvalue.'>'.$value.'</option>';
					            }

					            $ikk++;
					        }
				            $output .= '</select>';
				            $output .= '</label>';
			   			break;
		   			/**
		   			*Radio
		   			**/
			   			case 'radio':
			   			$description = ($params['fieldtooltip']!='') ? ' <a href="javascript:;" class="info-tip" aria-describedby="helptooltip">?<span role="tooltip">'.$params['fieldtooltip'].'</span></a>' : '' ;
			   				$output .= '<label class="lbl-text ext">'.$params['fieldtitle'].' '.$description.'</label>';
			   				$output .= '<div class="option-group">';

			   				$ikk = 0;

			   				foreach (pfstring2KeyedArray($params['fieldoptions']) as $key => $value) {
			   					$output .= '<span class="goption">';
					   			$output .= '<label class="options">';
					   			if (is_array($params['fielddefault'])) {
					            	$checkvalue = (in_array($key,$params['fielddefault'])) ? ' checked' : '' ;
					            }else{
					            	$checkvalue = ($params['fielddefault']!='' && strcmp($params['fielddefault'], $key) == 0) ? ' checked' : '' ;
					            }
						        $output .= '<input type="radio" name="'.$params['fieldname'].'" value="'.$key.'"'.$checkvalue.' />';
						        $output .= '<span class="radio"></span>';
					            $output .= '</label>';
					            if (function_exists('icl_t')) {
					            	 $exvalue = explode('=', icl_t('admin_texts_pfcustomfields_options', '[pfcustomfields_options][setupcustomfields_'.$params['fieldname'].'_rvalues]'.$ikk, $value));
					            	 if (isset($exvalue[1])) {
					            	 	$output .= '<label for="'.$params['fieldname'].'">'.$exvalue[1].'</label>';
					            	 }else{
					            	 	$output .= '<label for="'.$params['fieldname'].'">'.$value.'</label>';
					            	 }
					            }else{
					            	 $output .= '<label for="'.$params['fieldname'].'">'.$value.'</label>';
					            }
					            $output .= '</span>';

					            $ikk++;
							}

				            $output .= '</div>';
			   			break;
		   			/**
		   			*Checkbox
		   			**/
			   			case 'checkbox':
			   				$description = ($params['fieldtooltip']!='') ? ' <a href="javascript:;" class="info-tip" aria-describedby="helptooltip">?<span role="tooltip">'.$params['fieldtooltip'].'</span></a>' : '' ;
			   				$output .= '<label class="lbl-text ext">'.$params['fieldtitle'].' '.$description.'</label>';
			   				$output .= '<div class="option-group">';

			   				$ikk = 0;

			   				foreach (pfstring2KeyedArray($params['fieldoptions']) as $key => $value) {
			   					$output .= '<span class="goption">';
					   			$output .= '<label class="options">';
					   			if (is_array($params['fielddefault'])) {
					            	$checkvalue = (in_array($key,$params['fielddefault'])) ? ' checked' : '' ;
					            }else{
					            	$checkvalue = ($params['fielddefault']!='' && strcmp($params['fielddefault'], $key) == 0) ? ' checked' : '' ;
					            }
						        $output .= '<input type="checkbox" name="'.$params['fieldname'].'[]" value="'.$key.'"'.$checkvalue.' />';
						        $output .= '<span class="checkbox"></span>';
					            $output .= '</label>';
					            if (function_exists('icl_t')) {
					            	 $exvalue = explode('=', icl_t('admin_texts_pfcustomfields_options', '[pfcustomfields_options][setupcustomfields_'.$params['fieldname'].'_rvalues]'.$ikk, $value));
					            	 if (isset($exvalue[1])) {
					            	 	$output .= '<label for="'.$params['fieldname'].'">'.$exvalue[1].'</label>';
					            	 }else{
					            	 	$output .= '<label for="'.$params['fieldname'].'">'.$value.'</label>';
					            	 }
					            }else{
					            	 $output .= '<label for="'.$params['fieldname'].'">'.$value.'</label>';
					            }
					            $output .= '</span>';
					            $ikk++;
							}

				            $output .= '</div>';
			   			break;
			   		/**
		   			*Date
		   			**/
			   			case 'date':


				            $setup4_membersettings_dateformat = PFSAIssetControl('setup4_membersettings_dateformat','','1');
							$setup3_modulessetup_openinghours_ex2 = PFSAIssetControl('setup3_modulessetup_openinghours_ex2','','1');
							
							$date_field_rtl = (!is_rtl())? 'false':'true';
							$date_field_ys = 'true';

							switch ($setup4_membersettings_dateformat) {
								case '1':$date_field_format = 'dd/mm/yy';$date_field_format0 = 'd/m/Y';break;
								case '2':$date_field_format = 'mm/dd/yy';$date_field_format0 = 'm/d/Y';break;
								case '3':$date_field_format = 'yy/mm/dd';$date_field_format0 = 'Y/m/d';break;
								case '4':$date_field_format = 'yy/dd/mm';$date_field_format0 = 'Y/d/m';break;
								default:$date_field_format = 'dd/mm/yy';$date_field_format0 = 'd/m/Y';break;
							}	

				   			$output .= '
				   				<label for="'.$params['fieldname'].'" class="lbl-text">'.$params['fieldtitle'].'</label>
				                <label class="lbl-ui">';
				           			            
				            if (is_array($params['fielddefault'])) {
				            	if(isset($params['fielddefault'][0])){
				            		$checkvalue = ($params['fielddefault'][0]!='') ? ' value="'.date($date_field_format0,$params['fielddefault'][0]).'"' : '' ;
				            	}else{
				            		$checkvalue = '';
				            	}
				            }else{
				            	$checkvalue = ($params['fielddefault']!='') ? ' value="'.date($date_field_format0,$params['fielddefault']).'"' : '' ;
				            }

				            if($params['fieldsubtype'] == 4){
				            	$output .= '<input type="text" id="'.$params['fieldname'].'" name="'.$params['fieldname'].'"  class="input"'.$checkvalue.' />';
					        }else{
					        	$output .= '<input type="text" id="'.$params['fieldname'].'" name="'.$params['fieldname'].'" class="input"'.$checkvalue.' />';
					        }
					        if ($params['fieldtooltip']!='') {
					        	$output .= '<b class="tooltip left-bottom"><em>'.$params['fieldtooltip'].'</em></b>';
					        } 			        
				            $output .= '</label>';

				            $yearrange1 = PFCFIssetControl('setupcustomfields_'.$params['fieldname'].'_yearrange1','','2000');
							$yearrange2 = PFCFIssetControl('setupcustomfields_'.$params['fieldname'].'_yearrange2','',date("Y"));

							if (!empty($yearrange1) && !empty($yearrange2)) {
								$yearrangesetting = 'yearRange:"'.$yearrange1.':'.$yearrange2.'",';
							}elseif (!empty($yearrange1) && empty($yearrange2)) {
								$yearrangesetting = 'yearRange:"'.$yearrange1.':'.date("Y").'",';
							}else{
								$yearrangesetting = '';
							}
								

				            $output .= "
							<script>
							(function($) {
								'use strict';
								$(function(){
									$( '#".$params['fieldname']."' ).datepicker({
								      changeMonth: $date_field_ys,
								      changeYear: $date_field_ys,
								      isRTL: $date_field_rtl,
								      dateFormat: '$date_field_format',
								      $yearrangesetting
								      firstDay: $setup3_modulessetup_openinghours_ex2,/* 0 Sunday 1 monday*/
								      
								    });
								});
							})(jQuery);
							</script>
				            ";
			   			break;

		   		}

		   		$output .= '</div>';



	        return $output;
		}
	}

	if (!function_exists('PFValidationCheckWriteEx')) {
		function PFValidationCheckWriteEx($field_validation_check,$field_validation_text,$itemid){
					
			$itemname = (string)trim($itemid);
			$itemname = (strpos($itemname, '[]') == false) ? $itemname : "'".$itemname."'" ;

			if($field_validation_check == 1){
				return '$("[name=\''.$itemname.'\']").rules( "add", {
				  required: true,
				  messages: {
				    required: "'.$field_validation_text.'",
				  }
				});';
			}
		}
	}

	if (!function_exists('PFGetListFA')) {
		function PFGetListFA($params = array()){
		    $defaults = array( 
		        'listname' => '',
		        'listtype' => '',
		        'listtitle' => '',
		        'listsubtype' => '',
		        'listdefault' => '',
		        'listmultiple' => 0,
		        'connectionkey' => '',
		        'connectionvalue' => '',
		        'connectionstatus' => 1,
		        'place' => ''
		    );
			
		    $params = array_merge($defaults, $params);

		    $i = 0;
		    	$output_options = '';
		    	if($params['listmultiple'] == 1){ $multiplevar = ' multiple';$multipletag = '[]';}else{$multiplevar = '';$multipletag = '';};

		    	$fieldvalues = get_terms($params['listsubtype'],array('hide_empty'=>false));
				


					foreach( $fieldvalues as $parentfieldvalue){
						if($parentfieldvalue->parent == 0){
							/* If connection enabled */
							if ($params['connectionstatus'] == 0) {
								$process = false;

								$term_meta_check = get_term_meta($parentfieldvalue->term_id,$params['connectionkey'],true);
								if (is_array($term_meta_check)) {
									if (in_array($params['connectionvalue'], $term_meta_check)) {
										$process = true;
									}
								}
							}else{
								$process = true;
							}

							

							if ($process) {
								$fieldParenttaxSelectedValuex = 0;
								$i++;
								if(is_array($params['listdefault'])){
									if(in_array($parentfieldvalue->term_id, $params['listdefault'])){ $fieldParenttaxSelectedValuex = 1;}
								}else{
									if(strcmp($params['listdefault'],$parentfieldvalue->term_id) == 0){ $fieldParenttaxSelectedValuex = 1;}
								}

								if($fieldParenttaxSelectedValuex == 1){
									$output_options .= '<option class="pointfinder-parent-field" value="'.$parentfieldvalue->term_id.'" selected>'.$parentfieldvalue->name.'</option>';
								}else{
									$output_options .= '<option class="pointfinder-parent-field" value="'.$parentfieldvalue->term_id.'">'.$parentfieldvalue->name.'</option>';
								}



								foreach( $fieldvalues as $fieldvalue){

									if($fieldvalue->parent == $parentfieldvalue->term_id){

										/* If connection enabled */
										if ($params['connectionstatus'] == 0) {
											$process_child = false;

											$term_meta_check = get_term_meta($fieldvalue->term_id,$params['connectionkey'],true);
											if (is_array($term_meta_check)) {
												if (in_array($params['connectionvalue'], $term_meta_check)) {
													$process_child = true;
												}
											}
										}else{
											$process_child = true;
										}


										if ($process_child) {
											$fieldtaxSelectedValue = 0;

											if($params['listdefault'] != ''){
												if(is_array($params['listdefault'])){
													if(in_array($fieldvalue->term_id, $params['listdefault'])){ $fieldtaxSelectedValue = 1;}
												}else{
													if(strcmp($params['listdefault'],$fieldvalue->term_id) == 0){ $fieldtaxSelectedValue = 1;}
												}
											}
											
											if($fieldtaxSelectedValue == 1){
												$output_options .= '<option value="'.$fieldvalue->term_id.'" selected>&nbsp;&nbsp;&nbsp;&nbsp;'.$fieldvalue->name.'</option>';
											}else{
												$output_options .= '<option value="'.$fieldvalue->term_id.'">&nbsp;&nbsp;&nbsp;&nbsp;'.$fieldvalue->name.'</option>';
											}
										}
									}
								}
									
								
							}
						
						}
					}


		    	$output = '';
				$output .= '<div class="pf_fr_inner" data-pf-parent="">';
					

					if (!empty($params['listtitle'])) {
	   				$output .= '<label for="'.$params['listname'].'" class="lbl-text">'.$params['listtitle'].':</label>';
					}

					$as_mobile_dropdowns = PFASSIssetControl('as_mobile_dropdowns','','0');

				if ($as_mobile_dropdowns == 1) {
					$as_mobile_dropdowns_text = 'class="pf-special-selectbox"';
				} else {
					$as_mobile_dropdowns_text = '';
				}
				
					$output .= '
	            <label class="lbl-ui select">
	            <select'.$multiplevar.' name="'.$params['listname'].$multipletag.'" id="'.$params['listname'].'" '.$as_mobile_dropdowns_text.'>';

	            if ($params['place'] != 'backend') {
	            	$output .= '<option></option>';
	            }elseif ($params['listtype'] == 'conditions' && $params['place'] == 'backend') {
	            	

	            	$ctext_forcond = "";
					if (empty($params['listdefault'])) {
						$ctext_forcond = ' checked="checked"';
					}


	            	$output .= '<option value="" '.$ctext_forcond.' >'.esc_html__("Empty Condition","pointfindert2d").'</option>';
	            }
	            
	            $output .= $output_options.'
	            </select>
	            </label>';
		   		

		   		$output .= '</div>';
		   
		   	if ($i > 0 ) {
		   		return $output;
		   	}
		   
		}
	}

/** 
*End: ajax-featuresystem.php Functions
**/

/** 
*Start: ajax-featuresfilter.php Functions
**/
	if (!function_exists('PFGetListFA_Search')) {
		function PFGetListFA_Search($params = array()){
	        $defaults = array( 
	            'listsubtype' => '',
	            'connectionkey' => '',
	            'connectionvalue' => '',
	            'connectionstatus' => 1,
	        );
	        
	        $params = array_merge($defaults, $params);

	        $i = 0;
	        $output_options = '';

	        $fieldvalues = get_terms($params['listsubtype'],array('hide_empty'=>false));

	        foreach( $fieldvalues as $parentfieldvalue){
	            if($parentfieldvalue->parent == 0){
	                /* If connection enabled */
	                if ($params['connectionstatus'] == 0) {
	                    $process = false;

	                    $term_meta_check = get_term_meta($parentfieldvalue->term_id,$params['connectionkey'],true);
	                    if (is_array($term_meta_check)) {
	                        if (in_array($params['connectionvalue'], $term_meta_check)) {
	                            $process = true;
	                        }
	                    }
	                }else{
	                    $process = true;
	                }


	                if ($process) {

	                    $i++;
	                    
	                    $output_options .= '<option class="pointfinder-parent-field" value="'.$parentfieldvalue->term_id.'">'.$parentfieldvalue->name.'</option>';
	                    

	                    foreach( $fieldvalues as $fieldvalue){

	                        if($fieldvalue->parent == $parentfieldvalue->term_id){

	                            /* If connection enabled */
	                            if ($params['connectionstatus'] == 0) {
	                                $process_child = false;

	                                $term_meta_check = get_term_meta($fieldvalue->term_id,$params['connectionkey'],true);
	                                if (is_array($term_meta_check)) {
	                                    if (in_array($params['connectionvalue'], $term_meta_check)) {
	                                        $process_child = true;
	                                    }
	                                }
	                            }else{
	                                $process_child = true;
	                            }


	                            if ($process_child) {
	                                $output_options .= '<option value="'.$fieldvalue->term_id.'">&nbsp;&nbsp;&nbsp;&nbsp;'.$fieldvalue->name.'</option>';
	                            }
	                        }
	                    }
	                        
	                    
	                }
	            
	            }
	        }


	        if ($i > 0 ) {
	            return $output_options;
	        }
	       
	    }
	}
/** 
*End: ajax-featuresfilter.php Functions
**/

if (!function_exists('pointfinder_clear_invoice_amount')) {
	function pointfinder_clear_invoice_amount($amount){

		$price_short = PFSAIssetControl('setup20_paypalsettings_paypal_price_short','','$');
			$price_pref = PFSAIssetControl('setup20_paypalsettings_paypal_price_pref','',1);

			$setup20_decimals_new = PFSAIssetControl('setup20_decimals_new','',2);
		$setup20_decimalpoint = PFSAIssetControl('setup20_paypalsettings_decimalpoint','','.');
		$setup20_thousands = PFSAIssetControl('setup20_paypalsettings_thousands','',',');

		if ($setup20_decimals_new > 0) {
			$amount = substr( $amount, 0, -($setup20_decimals_new));
		}
		$amount = str_replace($price_short, '', $amount);
		$amount = str_replace($setup20_decimalpoint, '', $amount);
		$amount = str_replace($setup20_thousands, '', $amount);
		return $amount;

	}
}


if (!function_exists('pointfinder_featured_image_getresized')) {
	function pointfinder_featured_image_getresized($pfitemid,$template_directory_uri,$general_crop2,$general_retinasupport,$setupsizelimitconf_general_gridsize1_width,$setupsizelimitconf_general_gridsize1_height){
		$noimg_url = $template_directory_uri.'/images/noimg.png';
		$featured_image = '';
		$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id( $pfitemid ), 'full' );
		
		$featured_image_original = $featured_image[0];

		if($general_retinasupport == 1){$pf_retnumber = 2;}else{$pf_retnumber = 1;}
		$featured_image_width = $setupsizelimitconf_general_gridsize1_width*$pf_retnumber;
		$featured_image_height = $setupsizelimitconf_general_gridsize1_height*$pf_retnumber;

		if(!empty($featured_image[0])){
			
			switch ($general_crop2) {
				case 1:
					$featured_image_output = aq_resize($featured_image[0],$featured_image_width,$featured_image_height,true,true,true);
					break;
				case 2:

					$featured_image_output = aq_resize($featured_image[0],$featured_image_width,$featured_image_height,true);

					if($featured_image_output === false) {
						if($general_retinasupport == 1){
							$featured_image_output = aq_resize($featured_image[0],$featured_image_width/2,$featured_image_height/2,true);
							if($featured_image_output === false) {
								$featured_image_output = $featured_image_original;
								if($featured_image_output == '') {
									$featured_image_output = $noimg_url;
								}
							}
						}else{
							$featured_image_output = aq_resize($featured_image[0],$featured_image_width/2,$featured_image_height/2,true);
							if ($featured_image_output === false) {
								$featured_image_output = aq_resize($featured_image[0],$featured_image_width/4,$featured_image_height/4,true);
								if ($featured_image_output === false) {
									$featured_image_output = $featured_image_original;
									if($featured_image_output == '') {
										$featured_image_output = $noimg_url;
									}
								}
							}
					
							$featured_image_output = $featured_image_original;
							if($featured_image_output == '') {
								$featured_image_output = $noimg_url;
							}
						}
						
					}
					break;

				case 3:
					$featured_image_output = $featured_image_original;
					break;
			}

		}else{
			$featured_image_output = $noimg_url;
		}

		return array('featured_image' => $featured_image_output,'featured_image_org' => $featured_image_original);

	}
}
