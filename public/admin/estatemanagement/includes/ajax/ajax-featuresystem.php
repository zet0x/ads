<?php
/**********************************************************************************************************************************
*
* Ajax Module System - Updated with v1.7.2
* 
* Author: Webbu Design
*
***********************************************************************************************************************************/


add_action( 'PF_AJAX_HANDLER_pfget_featuresystem', 'pf_ajax_featuresystem' );
add_action( 'PF_AJAX_HANDLER_nopriv_pfget_featuresystem', 'pf_ajax_featuresystem' );

function pf_ajax_featuresystem(){
  
	check_ajax_referer( 'pfget_featuresystem', 'security');
  
	header('Content-Type: application/json; charset=UTF-8;');

	$id = $postid = $place = $lang_c = $output_features = $output_itemtypes = $output_video = $output_conditions = $output_customtab = $output_customfields = $post_terms_itemtype = $post_terms = $post_terms_comditions = $output_ohours = $scriptoutput = '';

	if(isset($_POST['id']) && $_POST['id']!=''){
		$id = sanitize_text_field($_POST['id']);
	}

	if(isset($_POST['postid']) && $_POST['postid']!=''){
		$postid = sanitize_text_field($_POST['postid']);
	}

	if(isset($_POST['place']) && $_POST['place']!=''){
		$place = sanitize_text_field($_POST['place']);
	}

	if(isset($_POST['lang']) && $_POST['lang']!=''){
		$lang_c = sanitize_text_field($_POST['lang']);
	}

	if(function_exists('icl_t')) {
		if (!empty($lang_c)) {
			do_action( 'wpml_switch_language', $lang_c );
		}
		$pointfindertheme_option = get_option('pointfindertheme_options');
		$pfadvancedcontrol_options = get_option('pfadvancedcontrol_options');
	}else{
		global $pointfindertheme_option;
		global $pfadvancedcontrol_options;
	}

	/*
	* START : Features
	*/
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
		    'search'            => '', 
		    'cache_domain'      => 'core'
		); 

		$terms = get_terms('pointfinderfeatures', $args);

		$output_features .= '<section class="pfsubmit-inner-sub"><div class="option-group">';
		if ($place != 'backend') {
			$output_features .= "<a class='pfitemdetailcheckall'>".esc_html__('Check All','pointfindert2d')."</a> / <a class='pfitemdetailuncheckall'>".esc_html__('Uncheck All','pointfindert2d')."</a><br/><br>";
		}
		
		$i = 0;

		if (isset($terms)) {
			if (is_array($terms)) {

				if (!empty($postid)) {
					$post_terms = wp_get_post_terms($postid, 'pointfinderfeatures', array("fields" => "ids"));
				}

				foreach ($terms as $term) {

					$term_parent = get_option( 'pointfinder_features_customlisttype_' . $term->term_id );

					if (PFSAIssetControl('setup4_sbf_c1','','1') != 1) {
						$output_check = pointfinder_features_tax_output_check($term_parent,$id,'pointfinderfeatures');
					}else{
						$output_check = "ok";
					}
					


					if ($output_check == 'ok') {

						$checked_text = '';


						if (!empty($post_terms)) {
							if (in_array($term->term_id, $post_terms)) {
								$checked_text = ' checked=""';
							}
						}
						
						$output_features .= '<span class="goption">';
	                    	$output_features .= '<label class="options">';
	                            $output_features .= '<input type="checkbox" id="pffeature'.$term->term_id.'" name="pffeature[]" value="'.$term->term_id.'"'.$checked_text.'>';
	                            $output_features .= '<span class="checkbox"></span>';
	                        $output_features .= '</label>';
	                    	$output_features .= '<label for="pffeature'.$term->term_id.'">'.$term->name.'</label>';
	                    $output_features .= '</span>';
	                    $i++;
					}

				}
				$output_features .= '<input name="pointfinderfeaturecount" type="hidden" value="'.$i.'"/>';
			}
		}
		$output_features .= '</div></section>';

		if ($place == 'backend' && $i == 0) {
			$output_features = esc_html__("Features not defined.","pointfindert2d");
   		}
	/*
	* END : Features
	*/


	/*
	* START : Item Types
	*/	
		$setup3_pointposttype_pt4_check = PFSAIssetControl('setup3_pointposttype_pt4_check','','1');
		$setup4_submitpage_itemtypes_check = PFSAIssetControl('setup4_submitpage_itemtypes_check','','1');
		if ($place == 'backend') {
			$setup4_submitpage_itemtypes_check = 1;
		}
		if ($setup3_pointposttype_pt4_check == 1 && $setup4_submitpage_itemtypes_check == 1) {

			$stp_syncs_it = PFSAIssetControl('stp_syncs_it','',1);
			$setup4_submitpage_itemtypes_multiple = PFSAIssetControl('setup4_submitpage_itemtypes_multiple','','0');
			$setup4_submitpage_itemtypes_validation = PFSAIssetControl('setup4_submitpage_itemtypes_validation','','1');
			$setup4_submitpage_itemtypes_verror = PFSAIssetControl('setup4_submitpage_itemtypes_verror','','Please select an item type.');

			if ($setup4_submitpage_itemtypes_multiple == 1) {
				$setup4_submitpage_itemtypes_multiple_output = 'true';
			}else{
				$setup4_submitpage_itemtypes_multiple_output = 'false';
			}
			$itemfieldname = ($setup4_submitpage_itemtypes_multiple == 1) ? 'pfupload_itemtypes[]' : 'pfupload_itemtypes' ;

			$item_defaultvalue = (!empty($postid)) ? wp_get_post_terms($postid, 'pointfinderitypes', array("fields" => "ids")) : '' ;

			$output_itemtypes .= '<section class="pfsubmit-inner-sub">';
			$fields_output_arr = array(
				'listname' => 'pfupload_itemtypes',
		        'listtype' => 'itemtypes',
		        'listtitle' => '',
		        'listsubtype' => 'pointfinderitypes',
		        'listdefault' => $item_defaultvalue,
		        'listmultiple' => $setup4_submitpage_itemtypes_multiple,
		        'connectionkey' => 'pointfinder_itemtype_clt',
		        'connectionvalue' => $id,
		        'connectionstatus' => $stp_syncs_it,
		        'place' => $place
			);

			$output_function_it = PFGetListFA($fields_output_arr);

			$output_itemtypes .= $output_function_it;
			$output_itemtypes .= '</section>';

			if ($place == 'backend') {
				$output_itemtypes .= '
			    <script>
			    jQuery(function(){
			        jQuery("#pfupload_itemtypes").multiselect({
			            buttonWidth: "100%",
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
			            maxHeight: 300,
			            multiple:'.$setup4_submitpage_itemtypes_multiple_output.'
			        });
			    });
			    </script>
			    </td>
			    </tr>
			    ';
			}else{

				$output_itemtypes .= '
					<script type="text/javascript">
					(function($) {
					"use strict";';

				if ($place == 'backend') {
					$output_itemtypes .= '
						$("#pfupload_itemtypes").select2({
						placeholder: "'.esc_html__("Please select","pointfindert2d").'", 
						formatNoMatches:"'.esc_html__("Nothing found.","pointfindert2d").'",
						allowClear: true, 
						minimumResultsForSearch: 10
						});
					';
				}else{
					$output_itemtypes .= '
						if ($.pf_tablet_check()) {
							$("#pfupload_itemtypes").select2({
							placeholder: "'.esc_html__("Please select","pointfindert2d").'", 
							formatNoMatches:"'.esc_html__("Nothing found.","pointfindert2d").'",
							allowClear: true, 
							minimumResultsForSearch: 10
							});
						}
					';
				}

				if ($setup4_submitpage_itemtypes_validation == 1 && $place != 'backend') {
					$output_itemtypes .= '$("#pfupload_itemtypes").rules("add",{required: true,messages:{required:"'.$setup4_submitpage_itemtypes_verror.'"}});';
				}
				$output_itemtypes .= '
				})(jQuery);
				</script>
				';
			}

			if (empty($output_function_it)) {
				$output_itemtypes = '';
			}

			if(empty($output_itemtypes) && $place == 'backend'){
				$output_itemtypes = esc_html__("Item Types not defined.","pointfindert2d");
			}
			
		}

	/*
	* END : Item Types
	*/


	/*
	* START : Conditions
	*/
		$setup4_submitpage_conditions_check = PFSAIssetControl('setup4_submitpage_conditions_check','',0);
		$setup3_pt14_check = PFSAIssetControl('setup3_pt14_check','',0);

		if ($place == 'backend') {
			$setup4_submitpage_conditions_check = 1;
		}
		
		if ($setup3_pt14_check == 1 && $setup4_submitpage_conditions_check == 1) {

			$stp_syncs_co = PFSAIssetControl('stp_syncs_co','',1);

			$setup4_submitpage_conditions_validation = PFSAIssetControl('setup4_submitpage_conditions_validation','',0);
			$setup4_submitpage_conditions_verror = PFSAIssetControl('setup4_submitpage_conditions_verror','','');
			$setup4_submitpage_conditions_check = PFSAIssetControl('setup4_submitpage_conditions_check','',0);

			$item_defaultvalue = ($postid != '') ? wp_get_post_terms($postid, 'pointfinderconditions', array("fields" => "ids")) : '' ;

			$output_conditions .= '<section class="pfsubmit-inner-sub">';
			$fields_output_arr = array(
				'listname' => 'pfupload_conditions',
		        'listtype' => 'conditions',
		        'listtitle' => '',
		        'listsubtype' => 'pointfinderconditions',
		        'listdefault' => $item_defaultvalue,
		        'connectionkey' => 'pointfinder_condition_clt',
		        'connectionvalue' => $id,
		        'connectionstatus' => $stp_syncs_co,
		        'place' => $place
			);

			$output_function_co = PFGetListFA($fields_output_arr);
			$output_conditions .= $output_function_co;
			$output_conditions .= '</section>';

			if ($place == 'backend') {
				$output_conditions .= '
			    <script>
			    jQuery(function(){
			        jQuery("#pfupload_conditions").multiselect({
			            buttonWidth: "100%",
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
			            maxHeight: 300,
			            multiple:false
			        });
			    });
			    </script>
			    </td>
			    </tr>
			    ';
			}else{
				$output_conditions .= '
				<script type="text/javascript">
				(function($) {
				"use strict";';

				if ($place == 'backend') {
					$output_conditions .= '
						$("#pfupload_conditions").select2({
						placeholder: "'.esc_html__("Please select","pointfindert2d").'", 
						formatNoMatches:"'.esc_html__("Nothing found.","pointfindert2d").'",
						allowClear: true, 
						minimumResultsForSearch: 10
						});
					';
				}else{
					$output_conditions .= '
						if ($.pf_tablet_check()) {
							$("#pfupload_conditions").select2({
							placeholder: "'.esc_html__("Please select","pointfindert2d").'", 
							formatNoMatches:"'.esc_html__("Nothing found.","pointfindert2d").'",
							allowClear: true, 
							minimumResultsForSearch: 10
							});
						}
					';
				}
				if ($setup4_submitpage_conditions_validation == 1) {
					$output_conditions .= '$("#pfupload_conditions").rules("add",{required: true,messages:{required:"'.$setup4_submitpage_conditions_verror.'"}});';
				}
				$output_conditions .= '
				})(jQuery);
				</script>';
			}
			
			
			

			if (empty($output_function_co)) {
				$output_conditions = '';
			}

			if(empty($output_conditions) && $place == 'backend'){
				$output_conditions = esc_html__("Conditions not defined.","pointfindert2d");
			}
			
		}
	/*
	* END : Conditions
	*/


	/*
	* START : Custom Fields
	*/

		/** 
		*Start : Field foreach
		**/
			$setup1_slides = PFSAIssetControl('setup1_slides','','');
			$fields_output_arr = array();
			$pfstart = PFCheckStatusofVar('setup1_slides');

			if(is_array($setup1_slides) && $pfstart){
				
				if (function_exists('icl_t')) {
					$pfcustomfields_options = get_option('pfcustomfields_options');
				}else{
					global $pfcustomfields_options;
				}

				foreach ($setup1_slides as &$value) {
				
		          $customfield_statuscheck = PFCFIssetControl('setupcustomfields_'.$value['url'].'_frontupload','','0');
		          if ($place == 'backend') {
		          	$customfield_statuscheck = 1;
		          }
		          $available_fields = array(1,2,3,4,5,7,8,9,14,15);
		        
		          if(in_array($value['select'], $available_fields) && $customfield_statuscheck != 0){


					$fieldtitle = (empty($pfcustomfields_options['setupcustomfields_'.$value['url'].'_frontendname'])) ? $value['title'] : $pfcustomfields_options['setupcustomfields_'.$value['url'].'_frontendname'];


					$fieldarr = array(
						'fieldname' => $value['url'],
						'fieldtitle' => $fieldtitle
					);

					/***
					Check parent item 
					***/
					$ParentItem = PFCFIssetControl('setupcustomfields_'.$value['url'].'_parent','','');
					if(PFControlEmptyArr($ParentItem)){
						if (function_exists('icl_t')) {
							$NewParentItemArr = array();
							foreach ($ParentItem as $ParentItemSingle) {
								$NewParentItemArr[] = apply_filters('wpml_object_id', $ParentItemSingle, 'pointfinderltypes', TRUE);
							}
							$fieldarr['fieldparent'] = $NewParentItemArr;
						}else{
							$fieldarr['fieldparent'] = $ParentItem;
						}
						
					}
					/***
					End :
					***/

					$status = 'not';
			
					if (!empty($id)) {
						if (isset($fieldarr['fieldparent'])) {
						if (is_array($fieldarr['fieldparent'])) {
							if (in_array($id, $fieldarr['fieldparent'])) {
								$status = 'ok';
							}
						}
						}
					}else{
						if (empty($fieldarr['fieldparent'])) {
							$status = 'ok';
						}
					}

					if ($status == 'ok' || empty($fieldarr['fieldparent'])) {
					
						switch ($value['select']) {
							case '1':/*Text*/
							case '2':/*URL*/
							case '3':/*Email*/
							case '4':/*Number*/
								$fieldarr['fieldtype'] = 'text';
								$fieldarr['fieldsubtype'] = $value['select'];
								break;
							case '5':
								$fieldarr['fieldtype'] = 'textarea';
								break;
							case '7':
								$fieldarr['fieldtype'] = 'radio';
								$fieldarr['fieldoptions'] = PFCFIssetControl('setupcustomfields_'.$value['url'].'_rvalues','','');
								break;
							case '9':
								$fieldarr['fieldtype'] = 'checkbox';
								$fieldarr['fieldoptions'] = PFCFIssetControl('setupcustomfields_'.$value['url'].'_rvalues','','');
								break;
							case '8':
								$fieldarr['fieldtype'] = 'select';
								$fieldarr['fieldoptions'] = PFCFIssetControl('setupcustomfields_'.$value['url'].'_rvalues','','');
								break;
							case '14':
								$fieldarr['fieldtype'] = 'selectmulti';
								$fieldarr['fieldoptions'] = PFCFIssetControl('setupcustomfields_'.$value['url'].'_rvalues','','');
								break;
							case '15':
								$fieldarr['fieldtype'] = 'date';
						}


						/*** 
						Field Options from Admin Panel
						***/
						$field_description = PFCFIssetControl('setupcustomfields_'.$value['url'].'_descriptionfront','','');
						$field_validation_check = PFCFIssetControl('setupcustomfields_'.$value['url'].'_validation_required','','0');
						$fieldarr['fieldtooltip'] = $field_description;
						/***
						End :
						***/



						/***
						*Validation check and add rules. 
						***/

						if($place != 'backend'){
							if($fieldarr['fieldtype'] == 'selectmulti' || $fieldarr['fieldtype'] == 'checkbox'){
								$field_validation_text = PFCFIssetControl('setupcustomfields_'.$value['url'].'_message','','');
								$scriptoutput .= PFValidationCheckWriteEx($field_validation_check,$field_validation_text,$value['url'].'[]');
							}else{
								$field_validation_text = PFCFIssetControl('setupcustomfields_'.$value['url'].'_message','','');
								$scriptoutput .= PFValidationCheckWriteEx($field_validation_check,$field_validation_text,$value['url']);
							}
						}
						/***
						*End :
						***/

						$fullwidth = PFCFIssetControl('setupcustomfields_'.$value['url'].'_fwr','','0');
						if ($fullwidth == 1) {
							$fwtext = ' pf-cf-inner-fullwidth';
						}elseif ($fullwidth == 2) {
							$fwtext = ' pf-cf-inner-halfwidth';
						}
						else{$fwtext = '';}
						$output_customfields .= '<section class="pf-cf-inner-elements'.$fwtext.'">';
						
						/** Default Value **/
						$fieldarr['fielddefault'] = ($postid != '') ? get_post_meta($postid,'webbupointfinder_item_'.$value['url'],false) : PFCFIssetControl('setupcustomfields_'.$value['url'].'_defaultvalue','','') ;
						

						$output_customfields .= PFgetfield($fieldarr);

						$output_customfields .= '</section>';
					}

		          }
		          
		        }

			}
		/** 
		*End : Field foreach
		**/

		if($place != 'backend' && !empty($output_customfields)){
			$output_customfields .= "<script>";
			$output_customfields .= "(function($) {'use strict'; $(function(){";
			$output_customfields .= $scriptoutput;
			$output_customfields .= "});})(jQuery);";
			$output_customfields .= "</script>";
		}

		if (empty($output_customfields) && $place == 'backend') {
			$output_customfields = esc_html__("Custom fields not defined.","pointfindert2d");
			
		}
		
	/*
	* END : Custom Fields
	*/




	/* Check permission first */

	$stp4_ctt1 = PFSAIssetControl('stp4_ctt1','',0);
	$stp4_ctt2 = PFSAIssetControl('stp4_ctt2','',0);
	$stp4_ctt3 = PFSAIssetControl('stp4_ctt3','',0);
	$stp4_ctt4 = PFSAIssetControl('stp4_ctt4','',0);
	$stp4_ctt5 = PFSAIssetControl('stp4_ctt5','',0);
	$stp4_ctt6 = PFSAIssetControl('stp4_ctt6','',0);

	$stp4_ctt1_t = PFSAIssetControl('stp4_ctt1_t','',0);
	$stp4_ctt2_t = PFSAIssetControl('stp4_ctt2_t','',0);
	$stp4_ctt3_t = PFSAIssetControl('stp4_ctt3_t','',0);
	$stp4_ctt4_t = PFSAIssetControl('stp4_ctt4_t','',0);
	$stp4_ctt5_t = PFSAIssetControl('stp4_ctt5_t','',0);
	$stp4_ctt6_t = PFSAIssetControl('stp4_ctt6_t','',0);
	
	$st8_nasys = PFASSIssetControl('st8_nasys','',0);

	$advanced_term_status_new = 0;

	$top_term_id = pf_get_term_top_most_parent($id,'pointfinderltypes');
	if (isset($top_term_id['parent'])) {
		if (!empty($top_term_id['parent'])) {
			$id = $top_term_id['parent'];
		}
	}

	if ($st8_nasys != 1){
		$advanced_term_status = PFADVIssetControl('setupadvancedconfig_'.$id.'_advanced_status','','0');
	}

	if ($st8_nasys == 1 && !empty($id)) {
		$advanced_term_status = 0;
		$listing_config_meta = get_option('pointfinderltypes_aslvars');
		if (isset($listing_config_meta[$id])) {
			if (!empty($listing_config_meta[$id]['pflt_advanced_status'])) {
				$advanced_term_status_new = 1; 
			}
		}
	}


	if (empty($id)) {

		$setup42_itempagedetails_configuration = (isset($pointfindertheme_option['setup42_itempagedetails_configuration']))? $pointfindertheme_option['setup42_itempagedetails_configuration'] : array();
		$setup4_submitpage_video = PFSAIssetControl('setup4_submitpage_video','',0);
		$setup3_modulessetup_openinghours = PFSAIssetControl('setup3_modulessetup_openinghours','',0);

	}elseif ( $advanced_term_status == 1 && !empty($id)) {

		$setup42_itempagedetails_configuration = (isset($pfadvancedcontrol_options['setupadvancedconfig_'.$id.'_configuration']))? $pfadvancedcontrol_options['setupadvancedconfig_'.$id.'_configuration'] : array();
		$setup4_submitpage_video = PFADVIssetControl('setupadvancedconfig_'.$id.'_videomodule','',0);
		$setup3_modulessetup_openinghours = PFADVIssetControl('setupadvancedconfig_'.$id.'_ohoursmodule','',0);
	
	}elseif ($st8_nasys == 1 && $advanced_term_status_new == 1 && !empty($id)) {

		$setup42_itempagedetails_configuration = (isset($listing_config_meta[$id]['pflt_configuration']))? $listing_config_meta[$id]['pflt_configuration'] : array();
		$setup4_submitpage_video = (isset($listing_config_meta[$id]['pflt_videomodule']))? $listing_config_meta[$id]['pflt_videomodule'] : array();
		$setup3_modulessetup_openinghours = (isset($listing_config_meta[$id]['pflt_ohoursmodule']))? $listing_config_meta[$id]['pflt_ohoursmodule'] : array();
	
	}else{

		$setup42_itempagedetails_configuration = (isset($pointfindertheme_option['setup42_itempagedetails_configuration']))? $pointfindertheme_option['setup42_itempagedetails_configuration'] : array();
		$setup4_submitpage_video = PFSAIssetControl('setup4_submitpage_video','',0);
		$setup3_modulessetup_openinghours = PFSAIssetControl('setup3_modulessetup_openinghours','',0);

	}



	/*
	* START : Event Details
	*/
		$output_eventdetails = '';
		$eare_status = PFSAIssetControl('eare_status','',1);

		if(array_key_exists('events', $setup42_itempagedetails_configuration) && $eare_status == 1){
			if ($setup42_itempagedetails_configuration['events']['status'] == 1) {
				
				if (function_exists('icl_t')) {
					if ($st8_nasys == 1) {
						$events_title = $setup42_itempagedetails_configuration['events']['title'];
					}else{
						$events_title = icl_t('admin_texts_pointfindertheme_options','[pointfindertheme_options][setup42_itempagedetails_configuration][events]title');
					}
					
				}else{
					$events_title = $setup42_itempagedetails_configuration['events']['title'];
				}

				$setup4_membersettings_dateformat = PFSAIssetControl('setup4_membersettings_dateformat','','1');
				$setup3_modulessetup_openinghours_ex2 = PFSAIssetControl('setup3_modulessetup_openinghours_ex2','','1');
				$eare_times = PFSAIssetControl('eare_times','',1);
				$yearrange1 = PFSAIssetControl('earea_y1','',date("Y"));
				$yearrange2 = PFSAIssetControl('earea_y2','',(date("Y")+10));

				switch ($setup4_membersettings_dateformat) {
					case '1':$date_field_format = 'dd/mm/yy';break;
					case '2':$date_field_format = 'mm/dd/yy';break;
					case '3':$date_field_format = 'yy/mm/dd';break;
					case '4':$date_field_format = 'yy/dd/mm';break;
					default:$date_field_format = 'dd/mm/yy';break;
				}

				switch ($setup4_membersettings_dateformat) {
					case '1':$date_field_format2 = 'd/m/Y';break;
					case '2':$date_field_format2 = 'm/d/Y';break;
					case '3':$date_field_format2 = 'Y/m/d';break;
					case '4':$date_field_format2 = 'Y/d/m';break;
					default:$date_field_format2 = 'd/m/Y';break;
				}	


				$output_eventdetails .= '<div class="pfsubmit-title">'.$events_title.'</div>';

				$output_eventdetails .= '<section class="pfsubmit-inner">';

					$field_startdate = ($postid != '') ? get_post_meta($postid,'webbupointfinder_item_field_startdate',true) : '' ;
					$field_enddate = ($postid != '') ? get_post_meta($postid,'webbupointfinder_item_field_enddate',true) : '' ;
					$field_starttime = ($postid != '') ? get_post_meta($postid,'webbupointfinder_item_field_starttime',true) : '' ;
					$field_endtime = ($postid != '') ? get_post_meta($postid,'webbupointfinder_item_field_endtime',true) : '' ;

					if (!empty($field_startdate)) {
						$field_startdate = date($date_field_format2,$field_startdate);
					}
					if (!empty($field_enddate)) {
						$field_enddate = date($date_field_format2,$field_enddate);
					}

					$output_eventdetails .= '<section class="pfsubmit-inner-sub" style="margin-bottom: 0!important;">';


	                    $output_eventdetails .= '<section class="pf-cf-inner-elements pf-cf-inner-halfwidth">';
		                    $output_eventdetails .= '<div class="pf_fr_inner" data-pf-parent="">';
			   					$output_eventdetails .= '<label for="field_startdate" class="lbl-text">'.esc_html__("Start Date","pointfindert2d").'</label>';
			               		$output_eventdetails .= '<label class="lbl-ui"><input type="text" name="field_startdate" class="input" value="'.$field_startdate.'"></label>';
			                $output_eventdetails .= '</div>';
		                $output_eventdetails .= '</section>';


		                $output_eventdetails .= '<section class="pf-cf-inner-elements pf-cf-inner-halfwidth">';
		                    $output_eventdetails .= '<div class="pf_fr_inner" data-pf-parent="">';
			   					$output_eventdetails .= '<label for="field_enddate" class="lbl-text">'.esc_html__("End Date","pointfindert2d").'</label>';
			               		$output_eventdetails .= '<label class="lbl-ui"><input type="text" name="field_enddate" class="input" value="'.$field_enddate.'"></label>';
			                $output_eventdetails .= '</div>';
		                $output_eventdetails .= '</section>';

		                if ($eare_times == 1) {
		                $output_eventdetails .= '<section class="pf-cf-inner-elements pf-cf-inner-halfwidth">';
		                    $output_eventdetails .= '<div class="pf_fr_inner" data-pf-parent="">';
			   					$output_eventdetails .= '<label for="field_starttime" class="lbl-text">'.esc_html__("Start Time","pointfindert2d").'</label>';
			               		$output_eventdetails .= '<label class="lbl-ui"><input type="text" name="field_starttime" class="input" value="'.$field_starttime.'"></label>';
			                $output_eventdetails .= '</div>';
		                $output_eventdetails .= '</section>';


		                $output_eventdetails .= '<section class="pf-cf-inner-elements pf-cf-inner-halfwidth">';
		                    $output_eventdetails .= '<div class="pf_fr_inner" data-pf-parent="">';
			   					$output_eventdetails .= '<label for="field_endtime" class="lbl-text">'.esc_html__("End Time","pointfindert2d").'</label>';
			               		$output_eventdetails .= '<label class="lbl-ui"><input type="text" name="field_endtime" class="input" value="'.$field_endtime.'"></label>';
			                $output_eventdetails .= '</div>';
		                $output_eventdetails .= '</section>';
		            	}

						if (is_rtl()) {
							$rtltext_oh = 'true';
						}else{
							$rtltext_oh = 'false';
						}


						

						if (!empty($yearrange1) && !empty($yearrange2)) {
							$yearrangesetting = 'yearRange:"'.$yearrange1.':'.$yearrange2.'",';
						}elseif (!empty($yearrange1) && empty($yearrange2)) {
							$yearrangesetting = 'yearRange:"'.$yearrange1.':'.date("Y").'",';
						}else{
							$yearrangesetting = '';
						}


		                $output_eventdetails .= "<script>
						(function($) {
						'use strict';";
							if ($eare_times == 1) {
							$output_eventdetails .= "
							$.timepicker.timeRange(
								$('input[name=\"field_starttime\"]'),
								$('input[name=\"field_endtime\"]'),
								{
									minInterval: (1000*60*60),
									timeFormat: 'HH:mm',
									start: {},
									end: {},
									timeOnlyTitle: '".esc_html__('Choose Time','pointfindert2d')."',
									timeText: '".esc_html__('Time','pointfindert2d')."',
									hourText: '".esc_html__('Hour','pointfindert2d')."',
									currentText: '".esc_html__('Now','pointfindert2d')."',
									minuteText: '".esc_html__('Minute','pointfindert2d')."',
									secondText: '".esc_html__('Second','pointfindert2d')."',
									millisecText: '".esc_html__('Millisecond','pointfindert2d')."',
									microsecText: '".esc_html__('Microsecond','pointfindert2d')."',
									timezoneText: '".esc_html__('Time Zone','pointfindert2d')."',
									currentText: '".esc_html__('Now','pointfindert2d')."',
									closeText: '".esc_html__('Done','pointfindert2d')."',
									isRTL: ".$rtltext_oh."
								}
							);
							";
							}
							$output_eventdetails .= "
							$('input[name=\"field_startdate\"]').datepicker({
						      changeMonth: true,
						      changeYear: true,
						      isRTL: $rtltext_oh,
						      dateFormat: '$date_field_format',
						      firstDay: $setup3_modulessetup_openinghours_ex2,
						      $yearrangesetting
						      prevText: '',
						      nextText: '',
						      onClose: function( selectedDate ) {
						        $('input[name=\"field_enddate\"]').datepicker( 'option', 'minDate', selectedDate );
						      }
						    });

						    $('input[name=\"field_enddate\"]').datepicker({
						      changeMonth: true,
						      changeYear: true,
						      isRTL: $rtltext_oh,
						      dateFormat: '$date_field_format',
						      firstDay: $setup3_modulessetup_openinghours_ex2,
						      $yearrangesetting
						      prevText: '',
						      nextText: '',
						      onClose: function( selectedDate ) {
						        $('input[name=\"field_startdate\"]').datepicker( 'option', 'maxDate', selectedDate );
						      }
						    });
						    ";
						    $earea_vcheck = PFSAIssetControl('earea_vcheck','',0);
						    $earea_verror = PFSAIssetControl('earea_verror','',esc_html__('Please choose event date and time.', 'pointfindert2d'));
						    $output_eventdetails .= PFValidationCheckWriteEx($earea_vcheck,$earea_verror,'field_starttime');
						    $output_eventdetails .= PFValidationCheckWriteEx($earea_vcheck,$earea_verror,'field_endtime');
						    $output_eventdetails .= PFValidationCheckWriteEx($earea_vcheck,$earea_verror,'field_startdate');
						    $output_eventdetails .= PFValidationCheckWriteEx($earea_vcheck,$earea_verror,'field_enddate');

						   
						    $output_eventdetails .= "
						})(jQuery);</script>
						";


                    $output_eventdetails .= '</section>';
				$output_eventdetails .= '</section>';
			}
		}

	/*
	* END : Event Details
	*/



	/*
	* START : Video
	*/
		if($setup4_submitpage_video == 1 && $place != 'backend'){

			$pfuploadfeaturedvideo = ($postid != '') ? get_post_meta($postid, 'webbupointfinder_item_video', true) : '' ;

			$output_video .= '<div class="pfsubmit-title pf-videomodule-div">'.esc_html__('VIDEO','pointfindert2d').'</div>';
			$output_video .= '<section class="pfsubmit-inner pf-videomodule-div">';
			$output_video .= '<section class="pfsubmit-inner-sub">';
			$output_video .= '<small style="margin-bottom:4px">'.esc_html__('Please copy & paste video sharing url. ','pointfindert2d').'</small>';
				$output_video .= '
                <label for="file" class="lbl-ui" >
                <input class="input" name="pfuploadfeaturedvideo" placeholder="" value="'.$pfuploadfeaturedvideo.'">
                </label> 
				';
			$output_video .= '</section>';
			$output_video .= '</section>';
			
		}
	/*
	* END : Video
	*/


	/*
	* START : Opening Hours
	*/
		if($setup3_modulessetup_openinghours == 1 ){

			$setup3_modulessetup_openinghours_ex = PFSAIssetControl('setup3_modulessetup_openinghours_ex','','1');
			$setup3_modulessetup_openinghours_ex2 = PFSAIssetControl('setup3_modulessetup_openinghours_ex2','','1');

			if($setup3_modulessetup_openinghours_ex == 1){

				$output_ohours .= '<div class="pfsubmit-title pf-openinghours-div">'.esc_html__('Opening Hours','pointfindert2d').' <small>('.esc_html__('Leave blank to show closed','pointfindert2d' ).')</small></div>';
				$output_ohours .= '<section class="pfsubmit-inner pf-openinghours-div">';

				$output_ohours .= '<section class="pfsubmit-inner-sub">';
				$output_ohours .= '
                <label class="lbl-ui">
                <input type="text" name="o1" class="input" placeholder="'.esc_html__('Monday-Friday: 09:00 - 22:00','pointfindert2d').'" value="'.esc_attr(get_post_meta($postid,'webbupointfinder_items_o_o1',true)).'" />
	            </label>
	            </section>
	            ';
	            $output_ohours .= '</section>';
				
			}elseif( $setup3_modulessetup_openinghours_ex == 0){
				
				
				if ($setup3_modulessetup_openinghours_ex2 == 1) {
					$text_ohours1 = esc_html__('Monday','pointfindert2d');
					$text_ohours2 = esc_html__('Sunday','pointfindert2d');
				}else{
					$text_ohours1 = esc_html__('Sunday','pointfindert2d');
					$text_ohours2 = esc_html__('Monday','pointfindert2d');
				}

				$ohours_first = '<section>
					<label for="o1" class="lbl-text">'.esc_html__('Monday','pointfindert2d').':</label>
		            <label class="lbl-ui">
		            <input type="text" name="o1" class="input" placeholder="09:00 - 22:00" value="'.esc_attr(get_post_meta($postid,'webbupointfinder_items_o_o1',true)).'" />
		            </label>
		            </section>';

		        $ohours_last = '<section>
		            <label for="o7" class="lbl-text">'.esc_html__('Sunday','pointfindert2d').':</label>
		            <label class="lbl-ui">
		            <input type="text" name="o7" class="input" placeholder="09:00 - 22:00" value="'.esc_attr(get_post_meta($postid,'webbupointfinder_items_o_o7',true)).'"/>
		            </label>
		            </section>';

				if ($setup3_modulessetup_openinghours_ex2 != 1) {
					$ohours_first = $ohours_last . $ohours_first;
					$ohours_last = '';
				}

				$output_ohours .= '<div class="pfsubmit-title pf-openinghours-div">'.esc_html__('Opening Hours','pointfindert2d').' <small>('.esc_html__('Leave blank to show closed','pointfindert2d' ).')</small></div>';
				$output_ohours .= '<section class="pfsubmit-inner pf-openinghours-div">';
				$output_ohours .= '<section class="pfsubmit-inner-sub">';
				
				$output_ohours .= $ohours_first;
				$output_ohours .= '
		            <section>
		            <label for="o2" class="lbl-text">'.esc_html__('Tuesday','pointfindert2d').':</label>
	                <label class="lbl-ui">
	                <input type="text" name="o2" class="input" placeholder="09:00 - 22:00" value="'.esc_attr(get_post_meta($postid,'webbupointfinder_items_o_o2',true)).'"/>
		            </label>
		            </section>
		            <section>
		            <label for="o3" class="lbl-text">'.esc_html__('Wednesday','pointfindert2d').':</label>
	                <label class="lbl-ui">
	                <input type="text" name="o3" class="input" placeholder="09:00 - 22:00" value="'.esc_attr(get_post_meta($postid,'webbupointfinder_items_o_o3',true)).'"/>
		            </label>
		            </section>
		            <section>
		            <label for="o4" class="lbl-text">'.esc_html__('Thursday','pointfindert2d').':</label>
	                <label class="lbl-ui">
	                <input type="text" name="o4" class="input" placeholder="09:00 - 22:00" value="'.esc_attr(get_post_meta($postid,'webbupointfinder_items_o_o4',true)).'"/>
		            </label>
		            </section>
		            <section>
		            <label for="o5" class="lbl-text">'.esc_html__('Friday','pointfindert2d').':</label>
	                <label class="lbl-ui">
	                <input type="text" name="o5" class="input" placeholder="09:00 - 22:00" value="'.esc_attr(get_post_meta($postid,'webbupointfinder_items_o_o5',true)).'"/>
		            </label>
		            </section>
		            <section>
		            <label for="o6" class="lbl-text">'.esc_html__('Saturday','pointfindert2d').':</label>
	                <label class="lbl-ui">
	                <input type="text" name="o6" class="input" placeholder="09:00 - 22:00" value="'.esc_attr(get_post_meta($postid,'webbupointfinder_items_o_o6',true)).'"/>
		            </label>
		            </section>
	            ';
	            $output_ohours .= $ohours_last;
	            $output_ohours .= '</section>';
	            $output_ohours .= '</section>';
				
			}elseif($setup3_modulessetup_openinghours_ex == 2){
			
				$output_ohours .= '<div class="pfsubmit-title pf-openinghours-div">'.esc_html__('Opening Hours','pointfindert2d').' <small>('.esc_html__('Leave blank to show closed','pointfindert2d' ).')</small></div>';

				$output_ohours .= '<section class="pfsubmit-inner pf-openinghours-div">';									
				$output_ohours .= '<section class="pfsubmit-inner-sub">';
				
				if (is_rtl()) {
					$rtltext_oh = 'true';
				}else{
					$rtltext_oh = 'false';
				}

				for ($i=0; $i < 7; $i++) { 
					$o_value[$i] = get_post_meta($postid,'webbupointfinder_items_o_o'.($i+1),true);
					if (!empty($o_value[$i])) {
						$o_value[$i] = explode("-", $o_value[$i]);
						if (count($o_value[$i]) < 1) {
							$o_value[$i] = array("","");
						}elseif (count($o_value[$i]) < 2) {
							$o_value[$i][1] = "";
						}
					}else{
						$o_value[$i] = array("","");
					}

					
					$output_ohours .= "<script>
					(function($) {
					'use strict';
					$.timepicker.timeRange(
						$('input[name=\"o".($i+1)."_1\"]'),
						$('input[name=\"o".($i+1)."_2\"]'),
						{
							minInterval: (1000*60*60),
							timeFormat: 'HH:mm',
							start: {},
							end: {},
							timeOnlyTitle: '".esc_html__('Choose Time','pointfindert2d')."',
							timeText: '".esc_html__('Time','pointfindert2d')."',
							hourText: '".esc_html__('Hour','pointfindert2d')."',
							currentText: '".esc_html__('Now','pointfindert2d')."',
							minuteText: '".esc_html__('Minute','pointfindert2d')."',
							secondText: '".esc_html__('Second','pointfindert2d')."',
							millisecText: '".esc_html__('Millisecond','pointfindert2d')."',
							microsecText: '".esc_html__('Microsecond','pointfindert2d')."',
							timezoneText: '".esc_html__('Time Zone','pointfindert2d')."',
							currentText: '".esc_html__('Now','pointfindert2d')."',
							closeText: '".esc_html__('Done','pointfindert2d')."',
							isRTL: ".$rtltext_oh."
						}
					);
				})(jQuery);</script>
					";
				}
				

				$ohours_first = '
					<section>
					<label for="o1" class="lbl-text">'.esc_html__('Monday','pointfindert2d').':</label>
	   				<div class="row">
	   					<div class="col6 first">
			                <label class="lbl-ui">
			                <input type="text" name="o1_1" class="input" placeholder="'.__('Start','pointfindert2d').'" value="'.$o_value[0][0].'" />
				            </label>
	   					</div>
	   					<div class="col6 last">
			                <label class="lbl-ui">
			                <input type="text" name="o1_2" class="input" placeholder="'.__('End','pointfindert2d').'" value="'.$o_value[0][1].'" />
				            </label>
	   					</div>
	   				</div>
		            </section>
				';
				$ohours_last = '
					<section>
		            <label for="o7" class="lbl-text">'.esc_html__('Sunday','pointfindert2d').':</label>
		            <div class="row">
	                	<div class="col6 first">
			                <label class="lbl-ui">
			                <input type="text" name="o7_1" class="input" placeholder="'.__('Start','pointfindert2d').'" value="'.$o_value[6][0].'" />
				            </label>
	   					</div>
	   					<div class="col6 last">
			                <label class="lbl-ui">
			                <input type="text" name="o7_2" class="input" placeholder="'.__('End','pointfindert2d').'" value="'.$o_value[6][1].'" />
				            </label>
	   					</div>
	   				</div>
		            </section>

				';

				if ($setup3_modulessetup_openinghours_ex2 != 1) {
					$ohours_first = $ohours_last . $ohours_first;
					$ohours_last = '';
				}

				
				$output_ohours .= $ohours_first;
				$output_ohours .= '
		            <section>
		            <label for="o2" class="lbl-text">'.esc_html__('Tuesday','pointfindert2d').':</label>
		            <div class="row">
	                	<div class="col6 first">
			                <label class="lbl-ui">
			                <input type="text" name="o2_1" class="input" placeholder="'.__('Start','pointfindert2d').'" value="'.$o_value[1][0].'" />
				            </label>
	   					</div>
	   					<div class="col6 last">
			                <label class="lbl-ui">
			                <input type="text" name="o2_2" class="input" placeholder="'.__('End','pointfindert2d').'" value="'.$o_value[1][1].'" />
				            </label>
	   					</div>
	   				</div>
		            </section>
		            <section>
		            <label for="o3" class="lbl-text">'.esc_html__('Wednesday','pointfindert2d').':</label>
		            <div class="row">
	                	<div class="col6 first">
			                <label class="lbl-ui">
			                <input type="text" name="o3_1" class="input" placeholder="'.__('Start','pointfindert2d').'" value="'.$o_value[2][0].'" />
				            </label>
	   					</div>
	   					<div class="col6 last">
			                <label class="lbl-ui">
			                <input type="text" name="o3_2" class="input" placeholder="'.__('End','pointfindert2d').'" value="'.$o_value[2][1].'" />
				            </label>
	   					</div>
	   				</div>
		            </section>
		            <section>
		            <label for="o4" class="lbl-text">'.esc_html__('Thursday','pointfindert2d').':</label>
		            <div class="row">
	                	<div class="col6 first">
			                <label class="lbl-ui">
			                <input type="text" name="o4_1" class="input" placeholder="'.__('Start','pointfindert2d').'" value="'.$o_value[3][0].'" />
				            </label>
	   					</div>
	   					<div class="col6 last">
			                <label class="lbl-ui">
			                <input type="text" name="o4_2" class="input" placeholder="'.__('End','pointfindert2d').'" value="'.$o_value[3][1].'" />
				            </label>
	   					</div>
	   				</div>
		            </section>
		            <section>
		            <label for="o5" class="lbl-text">'.esc_html__('Friday','pointfindert2d').':</label>
		            <div class="row">
	                	<div class="col6 first">
			                <label class="lbl-ui">
			                <input type="text" name="o5_1" class="input" placeholder="'.__('Start','pointfindert2d').'" value="'.$o_value[4][0].'" />
				            </label>
	   					</div>
	   					<div class="col6 last">
			                <label class="lbl-ui">
			                <input type="text" name="o5_2" class="input" placeholder="'.__('End','pointfindert2d').'" value="'.$o_value[4][1].'" />
				            </label>
	   					</div>
	   				</div>
		            </section>
		            <section>
		            <label for="o6" class="lbl-text">'.esc_html__('Saturday','pointfindert2d').':</label>
		            <div class="row">
	                	<div class="col6 first">
			                <label class="lbl-ui">
			                <input type="text" name="o6_1" class="input" placeholder="'.__('Start','pointfindert2d').'" value="'.$o_value[5][0].'" />
				            </label>
	   					</div>
	   					<div class="col6 last">
			                <label class="lbl-ui">
			                <input type="text" name="o6_2" class="input" placeholder="'.__('End','pointfindert2d').'" value="'.$o_value[5][1].'" />
				            </label>
	   					</div>
	   				</div>
		            </section>
	            ';
	            $output_ohours .= $ohours_last;
	            $output_ohours .= '</section>';
	            $output_ohours .= '</section>';
				
			}
		}else{
			if ($place == 'backend' ) {
				$output_ohours = esc_html__("Opening Hours not enabled for this Listing Type.","pointfindert2d");
	   		}
		}
	/*
	* END : Opening Hours
	*/
	

	/*
	* START : Custom Tabs
	*/
		if (($stp4_ctt1 == 1 || $stp4_ctt2 == 1 || $stp4_ctt3 == 1 || $stp4_ctt4 == 1 || $stp4_ctt5 == 1 || $stp4_ctt6 == 1) && $place != 'backend') {

			$ctt1tx = $ctt2tx = $ctt3tx = $ctt4tx = $ctt5tx = $ctt6tx ='';

			/* Start: Custom Tab 1 */
				if(array_key_exists('customtab1', $setup42_itempagedetails_configuration) && $stp4_ctt1 == 1){
					if ($setup42_itempagedetails_configuration['customtab1']['status'] == 1) {
						
						if (function_exists('icl_t')) {
							if ($st8_nasys == 1) {
								$customtab1_title = $setup42_itempagedetails_configuration['customtab1']['title'];
							}else{
								$customtab1_title = icl_t('admin_texts_pointfindertheme_options','[pointfindertheme_options][setup42_itempagedetails_configuration][customtab1]title');
							}
							
						}else{
							$customtab1_title = $setup42_itempagedetails_configuration['customtab1']['title'];
						}

						$output_customtab .= '<div class="pfsubmit-title">'.$customtab1_title.'</div>';

						$output_customtab .= '<section class="pfsubmit-inner">';
							$ct1_content = ($postid != '') ? get_post_meta($postid,'webbupointfinder_item_custombox1',true) : '' ;

							$output_customtab .= '
							<section class="pfsubmit-inner-sub">
		                        <label class="lbl-ui">';
		                        if ($stp4_ctt1_t == 1) {
		                        	$ed_settings = array(
										'media_buttons' => false,
										'teeny' => true,
										'editor_class' => 'textarea mini',
										'textarea_name' => 'webbupointfinder_item_custombox1',
										'drag_drop_upload' => false,
										'dfw' => false,
										'tinymce' => true,
										'quicktags' => false
									);
									ob_start();
										wp_editor( $ct1_content, 'webbupointfinder_item_custombox1', $ed_settings );
										$content = ob_get_contents();
									ob_end_clean();
									$ctt1tx = ' textareaadv';
									$output_customtab .= $content;
		                        }
		                        $output_customtab .= '<textarea id="webbupointfinder_item_custombox1" name="webbupointfinder_item_custombox1" class="textarea mini'.$ctt1tx.'">'.$ct1_content.'</textarea>';
		                    $output_customtab .= '</label></section>';
						$output_customtab .= '</section>';
					}
				}
			/* End: Custom Tab 1 */

			/* Start: Custom Tab 2 */
				if(array_key_exists('customtab2', $setup42_itempagedetails_configuration) && $stp4_ctt2 == 1){
					if ($setup42_itempagedetails_configuration['customtab2']['status'] == 1) {
						
						if (function_exists('icl_t')) {
							if ($st8_nasys == 1) {
								$customtab2_title = $setup42_itempagedetails_configuration['customtab2']['title'];
							}else{
								$customtab2_title = icl_t('admin_texts_pointfindertheme_options','[pointfindertheme_options][setup42_itempagedetails_configuration][customtab2]title');
							}
							
						}else{
							$customtab2_title = $setup42_itempagedetails_configuration['customtab2']['title'];
						}

						$output_customtab .= '<div class="pfsubmit-title">'.$customtab2_title.'</div>';


						$output_customtab .= '<section class="pfsubmit-inner">';
							$ct2_content = ($postid != '') ? get_post_meta($postid,'webbupointfinder_item_custombox2',true) : '' ;

							$output_customtab .= '
							<section class="pfsubmit-inner-sub">
		                        <label class="lbl-ui">';
		                        
		                        if ($stp4_ctt2_t == 1) {
		                        	$ed_settings = array(
										'media_buttons' => false,
										'teeny' => true,
										'editor_class' => 'textarea mini',
										'textarea_name' => 'webbupointfinder_item_custombox2',
										'drag_drop_upload' => false,
										'dfw' => false,
										'tinymce' => true,
										'quicktags' => false
									);
									ob_start();
										wp_editor( $ct2_content, 'webbupointfinder_item_custombox2', $ed_settings );
										$content = ob_get_contents();
									ob_end_clean();
									$ctt2tx = ' textareaadv';
									$output_customtab .= $content;
		                        }

		                        $output_customtab .= '<textarea id="webbupointfinder_item_custombox2" name="webbupointfinder_item_custombox2" class="textarea mini'.$ctt2tx.'">'.$ct2_content.'</textarea>';
		                    $output_customtab .= '</label></section>';
						$output_customtab .= '</section>';
					}
				}
			/* End: Custom Tab 2 */

			/* Start: Custom Tab 3 */
				if(array_key_exists('customtab3', $setup42_itempagedetails_configuration) && $stp4_ctt3 == 1){
					if ($setup42_itempagedetails_configuration['customtab3']['status'] == 1) {
						
						if (function_exists('icl_t')) {
							if ($st8_nasys == 1) {
								$customtab3_title = $setup42_itempagedetails_configuration['customtab3']['title'];
							}else{
								$customtab3_title = icl_t('admin_texts_pointfindertheme_options','[pointfindertheme_options][setup42_itempagedetails_configuration][customtab3]title');
							}
							
						}else{
							$customtab3_title = $setup42_itempagedetails_configuration['customtab3']['title'];
						}

						$output_customtab .= '<div class="pfsubmit-title">'.$customtab3_title.'</div>';


						$output_customtab .= '<section class="pfsubmit-inner">';
							$ct3_content = ($postid != '') ? get_post_meta($postid,'webbupointfinder_item_custombox3',true) : '' ;

							$output_customtab .= '
							<section class="pfsubmit-inner-sub">
		                        <label class="lbl-ui">';
		                        
		                        if ($stp4_ctt3_t == 1) {
		                        	$ed_settings = array(
										'media_buttons' => false,
										'teeny' => true,
										'editor_class' => 'textarea mini',
										'textarea_name' => 'webbupointfinder_item_custombox3',
										'drag_drop_upload' => false,
										'dfw' => false,
										'tinymce' => true,
										'quicktags' => false
									);
									ob_start();
										wp_editor( $ct3_content, 'webbupointfinder_item_custombox3', $ed_settings );
										$content = ob_get_contents();
									ob_end_clean();
									$ctt3tx = ' textareaadv';
									$output_customtab .= $content;
		                        }

		                        $output_customtab .= '<textarea id="webbupointfinder_item_custombox3" name="webbupointfinder_item_custombox3" class="textarea mini'.$ctt3tx.'">'.$ct3_content.'</textarea>';
		                    $output_customtab .= '</label></section>';
						$output_customtab .= '</section>';
					}
				}
			/* End: Custom Tab 3 */

			/* Start: Custom Tab 4 */
				if(array_key_exists('customtab4', $setup42_itempagedetails_configuration) && $stp4_ctt4 == 1){
					if ($setup42_itempagedetails_configuration['customtab4']['status'] == 1) {
						
						if (function_exists('icl_t')) {
							if ($st8_nasys == 1) {
								$customtab4_title = $setup42_itempagedetails_configuration['customtab4']['title'];
							}else{
								$customtab4_title = icl_t('admin_texts_pointfindertheme_options','[pointfindertheme_options][setup42_itempagedetails_configuration][customtab4]title');
							}
							
						}else{
							$customtab4_title = $setup42_itempagedetails_configuration['customtab4']['title'];
						}

						$output_customtab .= '<div class="pfsubmit-title">'.$customtab4_title.'</div>';


						$output_customtab .= '<section class="pfsubmit-inner">';
							$ct4_content = ($postid != '') ? get_post_meta($postid,'webbupointfinder_item_custombox4',true) : '' ;

							$output_customtab .= '
							<section class="pfsubmit-inner-sub">
		                        <label class="lbl-ui">';
		                        
		                        if ($stp4_ctt4_t == 1) {
		                        	$ed_settings = array(
										'media_buttons' => false,
										'teeny' => true,
										'editor_class' => 'textarea mini',
										'textarea_name' => 'webbupointfinder_item_custombox4',
										'drag_drop_upload' => false,
										'dfw' => false,
										'tinymce' => true,
										'quicktags' => false
									);
									ob_start();
										wp_editor( $ct4_content, 'webbupointfinder_item_custombox4', $ed_settings );
										$content = ob_get_contents();
									ob_end_clean();
									$ctt4tx = ' textareaadv';
									$output_customtab .= $content;
		                        }

		                        $output_customtab .= '<textarea id="webbupointfinder_item_custombox4" name="webbupointfinder_item_custombox4" class="textarea mini'.$ctt4tx.'">'.$ct4_content.'</textarea>';
		                    $output_customtab .= '</label></section>';
						$output_customtab .= '</section>';
					}
				}
			/* End: Custom Tab 4 */

			/* Start: Custom Tab 5 */
				if(array_key_exists('customtab5', $setup42_itempagedetails_configuration) && $stp4_ctt5 == 1){
					if ($setup42_itempagedetails_configuration['customtab5']['status'] == 1) {
						
						if (function_exists('icl_t')) {
							if ($st8_nasys == 1) {
								$customtab5_title = $setup42_itempagedetails_configuration['customtab5']['title'];
							}else{
								$customtab5_title = icl_t('admin_texts_pointfindertheme_options','[pointfindertheme_options][setup42_itempagedetails_configuration][customtab5]title');
							}
							
						}else{
							$customtab5_title = $setup42_itempagedetails_configuration['customtab5']['title'];
						}

						$output_customtab .= '<div class="pfsubmit-title">'.$customtab5_title.'</div>';


						$output_customtab .= '<section class="pfsubmit-inner">';
							$ct5_content = ($postid != '') ? get_post_meta($postid,'webbupointfinder_item_custombox5',true) : '' ;

							$output_customtab .= '
							<section class="pfsubmit-inner-sub">
		                        <label class="lbl-ui">';
		                        
		                        if ($stp4_ctt5_t == 1) {
		                        	$ed_settings = array(
										'media_buttons' => false,
										'teeny' => true,
										'editor_class' => 'textarea mini',
										'textarea_name' => 'webbupointfinder_item_custombox5',
										'drag_drop_upload' => false,
										'dfw' => false,
										'tinymce' => true,
										'quicktags' => false
									);
									ob_start();
										wp_editor( $ct5_content, 'webbupointfinder_item_custombox5', $ed_settings );
										$content = ob_get_contents();
									ob_end_clean();
									$ctt5tx = ' textareaadv';
									$output_customtab .= $content;
		                        }

		                        $output_customtab .= '<textarea id="webbupointfinder_item_custombox5" name="webbupointfinder_item_custombox5" class="textarea mini'.$ctt5tx.'">'.$ct5_content.'</textarea>';
		                    $output_customtab .= '</label></section>';
						$output_customtab .= '</section>';
					}
				}
			/* End: Custom Tab 5 */

			/* Start: Custom Tab 6 */
				if(array_key_exists('customtab6', $setup42_itempagedetails_configuration) && $stp4_ctt6 == 1){
					if ($setup42_itempagedetails_configuration['customtab6']['status'] == 1) {
						
						if (function_exists('icl_t')) {
							if ($st8_nasys == 1) {
								$customtab6_title = $setup42_itempagedetails_configuration['customtab6']['title'];
							}else{
								$customtab6_title = icl_t('admin_texts_pointfindertheme_options','[pointfindertheme_options][setup42_itempagedetails_configuration][customtab6]title');
							}
							
						}else{
							$customtab6_title = $setup42_itempagedetails_configuration['customtab6']['title'];
						}

						$output_customtab .= '<div class="pfsubmit-title">'.$customtab6_title.'</div>';


						$output_customtab .= '<section class="pfsubmit-inner">';
							$ct6_content = ($postid != '') ? get_post_meta($postid,'webbupointfinder_item_custombox6',true) : '' ;

							$output_customtab .= '
							<section class="pfsubmit-inner-sub">
		                        <label class="lbl-ui">';
		                        
		                        if ($stp4_ctt6_t == 1) {
		                        	$ed_settings = array(
										'media_buttons' => false,
										'teeny' => true,
										'editor_class' => 'textarea mini',
										'textarea_name' => 'webbupointfinder_item_custombox6',
										'drag_drop_upload' => false,
										'dfw' => false,
										'tinymce' => true,
										'quicktags' => false
									);
									ob_start();
										wp_editor( $ct6_content, 'webbupointfinder_item_custombox6', $ed_settings );
										$content = ob_get_contents();
									ob_end_clean();
									$ctt6tx = ' textareaadv';
									$output_customtab .= $content;
		                        }

		                        $output_customtab .= '<textarea id="webbupointfinder_item_custombox6" name="webbupointfinder_item_custombox6" class="textarea mini'.$ctt6tx.'">'.$ct6_content.'</textarea>';
		                    $output_customtab .= '</label></section>';
						$output_customtab .= '</section>';
					}
				}
			/* End: Custom Tab 6 */
		}
	/*
	* END : Custom Tabs
	*/

	echo json_encode(array('features' => $output_features, 'itemtypes' => $output_itemtypes, 'conditions' => $output_conditions, 'customfields' => $output_customfields, 'customtabs' => $output_customtab, 'video'=>$output_video, 'ohours'=> $output_ohours,'eventdetails' => $output_eventdetails));
	
	die();
}

?>