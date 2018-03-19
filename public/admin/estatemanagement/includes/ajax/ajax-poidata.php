<?php

/**********************************************************************************************************************************
*
* Ajax POI data
* 
* Author: Webbu Design
*
***********************************************************************************************************************************/


add_action( 'PF_AJAX_HANDLER_pfget_markers', 'pf_ajax_markers' );
add_action( 'PF_AJAX_HANDLER_nopriv_pfget_markers', 'pf_ajax_markers' );
	
	
function pf_ajax_markers(){
	check_ajax_referer( 'pfget_markers', 'security' );
	header('Content-type: text/javascript');		
	
		if(isset($_POST['cl']) && $_POST['cl']!=''){
			$pflang = esc_attr($_POST['cl']);
		}else{
			$pflang = '';
		}

		/* WPML Fix */
		if(function_exists('icl_t')) {
			if (!empty($pflang)) {
				do_action( 'wpml_switch_language', $pflang );
			}
		}



		/** 
		*Start: ajax-poidata.php Functions
		**/
			
			function pf_term_sub_check($myval){
				$term_sub_check = get_term_by( 'term_id', $myval, 'pointfinderltypes');
			
				if ($term_sub_check != false) {

					if ($term_sub_check->parent == 0) {
						$output = $myval;
					}else{
						$output = pf_term_sub_check($term_sub_check->parent);
					}
				}else{
					$output = $myval;
				}

				return $output;
			}

			function pf_term_sub_check_ex($myval){
				$term_sub_check = get_term_by( 'term_id', $myval, 'pointfinderltypes');
			
				if ($term_sub_check != false) {

					if ($term_sub_check->parent == 0) {
						return true;
					}else{
						return false;
					}
				}
			}

			/** 
			*Start: GET - Marker Point Vars
			**/
				function pf_get_markerimage($postid,$setup8_pointsettings_pointopacity,$setup8_pointsettings_retinapoints,$st8_npsys){
					
					$pfitemicon = array();

					/* Check if item have a custom icon */

					$webbupointfinder_item_point_type = esc_attr(get_post_meta( $postid, "webbupointfinder_item_point_type", true ));
					$webbupointfinder_item_point_typenew = (empty($webbupointfinder_item_point_type))? 3:$webbupointfinder_item_point_type;

					switch ($webbupointfinder_item_point_typenew) {
						case 1:
							$pf_custom_point_images = redux_post_meta("pointfinderthemefmb_options", $postid, "webbupointfinder_item_custom_marker");

							/** 
							*Start: Custom icon check result = Image Icon
							**/
								$pfitemicon['is_image'] = 1;
								$pfitemicon['is_cat'] = 0;

								$pf_custom_point_image_height = (!empty($pf_custom_point_images['height']))? $pf_custom_point_images['height'] : 0;
								$pf_custom_point_image_width = (!empty($pf_custom_point_images['width']))? $pf_custom_point_images['width'] : 0;

								$retina_number = ($setup8_pointsettings_retinapoints == 1)?2:1;

								$width_calculated = $pf_custom_point_image_width/$retina_number;
								$height_calculated = $pf_custom_point_image_height/$retina_number;

								$pfitemicon['content']= '<div class=\'pf-map-pin-x\' style=\'background-image:url('.$pf_custom_point_images['url'].'); background-size:'.$width_calculated.'px '.$height_calculated.'px; width:'.$width_calculated.'px; height:'.$height_calculated.'px;opacity:'.$setup8_pointsettings_pointopacity.'\' ></div>';
							/** 
							*End: Custom icon check result = Image Icon
							**/
						break;

					case 2:

						/** 
						*Start: Custom icon check result = Css Icon
						**/
							$cssmarker_icontype = esc_attr(get_post_meta( $postid, 'webbupointfinder_item_cssmarker_icontype', true ));
							$cssmarker_icontype = (empty($cssmarker_icontype)) ? 1 : $cssmarker_icontype ;
							$cssmarker_iconsize = esc_attr(get_post_meta( $postid, 'webbupointfinder_item_cssmarker_iconsize', true ));
							$cssmarker_iconsize = (empty($cssmarker_iconsize)) ? 'middle' : $cssmarker_iconsize ;
							$cssmarker_iconname = esc_attr(get_post_meta( $postid, 'webbupointfinder_item_cssmarker_iconname', true ));

							$cssmarker_bgcolor = esc_attr(get_post_meta( $postid, 'webbupointfinder_item_cssmarker_bgcolor', true ));
							$cssmarker_bgcolor = (empty($cssmarker_bgcolor)) ? '#b00000' : $cssmarker_bgcolor ;
							$cssmarker_bgcolorinner = esc_attr(get_post_meta( $postid, 'webbupointfinder_item_cssmarker_bgcolorinner', true ));
							$cssmarker_bgcolorinner = (empty($cssmarker_bgcolorinner)) ? '#ffffff' : $cssmarker_bgcolorinner ;
							$cssmarker_iconcolor = esc_attr(get_post_meta( $postid, 'webbupointfinder_item_cssmarker_iconcolor', true ));
							$cssmarker_iconcolor = (empty($cssmarker_iconcolor)) ? '#b00000' : $cssmarker_iconcolor ;
							
							$arrow_text = ($cssmarker_icontype == 2)? '<div class=\'pf-pinarrow\' style=\'border-color: '.$cssmarker_bgcolor.' transparent transparent transparent;\'></div>': '';

							$pfitemicon['is_image'] = 1;
							$pfitemicon['is_cat'] = 0;

							$pfitemicon['content'] = '';
							
							
							$pfitemicon['content'] .= '<div ';
							$pfitemicon['content'] .= 'class=\'pfcatdefault-mapicon pf-map-pin-'.$cssmarker_icontype.' pf-map-pin-'.$cssmarker_icontype.'-'.$cssmarker_iconsize.' pfcustom-mapicon-'.$postid.'\'';
							$pfitemicon['content'] .= ' style=\'background-color:'.$cssmarker_bgcolor.';opacity:'.$setup8_pointsettings_pointopacity.';\' >';
							$pfitemicon['content'] .= '<i class=\''.$cssmarker_iconname.'\' style=\'color:'.$cssmarker_iconcolor.'\' ></i></div>'.$arrow_text;
							$pfitemicon['content'] .= '<style>.pfcustom-mapicon-'.$postid.':after{background-color:'.$cssmarker_bgcolorinner.'!important}</style>';

						/** 
						*End: Custom icon check result = Css Icon
						**/	
						break;

					default:
						/** 
						*Start: Check category icon 
						**/
							$pfitemicon['is_image'] = 0;
							$pfitemicon['is_cat'] = 1;

							$pf_item_terms = get_the_terms( $postid, 'pointfinderltypes');
							
							/* If marker term is available and array not empty */
							if(count($pf_item_terms) > 0){

								if ( $pf_item_terms && ! is_wp_error( $pf_item_terms ) ) {
									
									if($st8_npsys == 1){
										foreach ( $pf_item_terms as $pf_item_term ) {
											$pf_item_term_id = $pf_item_term->term_id;
										}
									}else{
										foreach ( $pf_item_terms as $pf_item_term ) {
										
											if ($pf_item_term->parent != 0) {
												$pf_item_term_subcheck = pf_term_sub_check_ex($pf_item_term->parent);
												if ($pf_item_term_subcheck) {
													$pf_item_term_id = $pf_item_term->term_id;
												}else{
													$pf_item_term_id = pf_term_sub_check($pf_item_term->term_id);
												}
												if (!empty($pf_item_term_id)) {
													break;
												}
											}else{
												$pf_item_term_id = $pf_item_term->term_id;
											}
											
											
										}
									}
									

								} 

								if(function_exists('icl_t')) { /* If wpml enabled */
									$pf_item_term_id = icl_object_id($pf_item_term_id,'pointfinderltypes',true,PF_default_language());
								}

								if (!empty($pf_item_term_id)) {
									$pfitemicon['cat'] = 'pfcat'.$pf_item_term_id;
								}else{
									$pfitemicon['cat'] = 'pfcatdefault';
								}
							
								
							}
							
						/** 
						*End: Check category icon 
						**/
						break;
					}
					
					return $pfitemicon;
				}
			/** 
			*End: GET - Marker Point Vars
			**/
			



			/** 
			*Start: GET - Marker Category CSS Name - minimized
			**/
				function pointfinder_get_category_points($params = array()){

					$defaults = array( 
				        'pf_get_term_detail_idm' => '',
				        'pf_get_term_detail_idm_parent' => '',
				        'listing_meta' => '',
				        'setup8_pointsettings_pointopacity' => 1,
				        'cpoint_type' => 0,
						'cpoint_icontype' => 1,
						'cpoint_iconsize' => 'middle',
						'cpoint_iconname' => '',
						'cpoint_bgcolor' => '#b00000',
						'setup8_pointsettings_retinapoints' => 1,
						'dlang' => '',
						'clang' => '',
						'st8_npsys' => 0
				    );

					$params = array_merge($defaults, $params);

					$listing_meta = $params['listing_meta'];
				   
					$pf_get_term_detail_id = $pf_get_term_detail_idxx = $params['pf_get_term_detail_idm'];
					$pf_get_term_detail_idm_parent = $params['pf_get_term_detail_idm_parent'];
					
					$output_data = $pf_get_term_detail_id_output = '';

					if(function_exists('icl_t')) {
						$pf_get_term_detail_id = icl_object_id($params['pf_get_term_detail_idm'],'pointfinderltypes',true,$params['dlang']);
						$pf_get_term_detail_idm_parent = icl_object_id($params['pf_get_term_detail_idm_parent'],'pointfinderltypes',true,$params['dlang']);
						$pf_get_term_detail_idxx = icl_object_id($params['pf_get_term_detail_idm'],'pointfinderltypes',true,$params['clang']);
					}

					if ($params['st8_npsys'] == 1) {
						$run_parent_check = false;

						if(isset($listing_meta[$pf_get_term_detail_id])){
							$slisting_meta = $listing_meta[$pf_get_term_detail_id];
							$icon_type = (isset($slisting_meta['cpoint_type']))?$slisting_meta['cpoint_type']:0;
							if (empty($icon_type)) {
								$run_parent_check = true;
							}else{
								$run_parent_check = false;
								$pf_get_term_detail_id_output = $pf_get_term_detail_id;
							}
						}else{
							$slisting_meta = '';
							$run_parent_check = true;
						}

						/* If 2nd level */
						if ($run_parent_check && !empty($pf_get_term_detail_idm_parent)) {
							if(isset($listing_meta[$pf_get_term_detail_idm_parent])){
								$slisting_meta = $listing_meta[$pf_get_term_detail_idm_parent];
								$icon_type = (isset($slisting_meta['cpoint_type']))?$slisting_meta['cpoint_type']:0;
								if (empty($icon_type)) {
									$run_parent_check = true;
								}else{
									$run_parent_check = false;
									$pf_get_term_detail_id_output = $pf_get_term_detail_idm_parent;
								}

							}else{
								$slisting_meta = '';
								$run_parent_check = true;
							}
						}

					
						/* If 3rd level */
						if ($run_parent_check && !empty($pf_get_term_detail_idm_parent)) {
							$top_most_parent = pf_get_term_top_most_parent($pf_get_term_detail_id,"pointfinderltypes");
							$top_most_parent = (isset($top_most_parent['parent']))?$top_most_parent['parent']:'';
							
							if(isset($listing_meta[$top_most_parent])){
								$slisting_meta = $listing_meta[$top_most_parent];
								$pf_get_term_detail_id_output = $top_most_parent;
							}else{
								$slisting_meta = '';
							}
							$run_parent_check = false;
						}

						

						if (!empty($slisting_meta)) {

							$icon_type = (isset($slisting_meta['cpoint_type']))?$slisting_meta['cpoint_type']:0;
							$icon_layout_type = (isset($slisting_meta['cpoint_icontype']))?$slisting_meta['cpoint_icontype']:1;
							$icon_size = (isset($slisting_meta['cpoint_iconsize']))?$slisting_meta['cpoint_iconsize']:'middle';
							$icon_bg_color = (isset($slisting_meta['cpoint_bgcolor']))?$slisting_meta['cpoint_bgcolor']:'#b00000';
							$icon_name = (isset($slisting_meta['cpoint_iconname']))?$slisting_meta['cpoint_iconname']:'';

							if ($icon_type == 2) {
								$arrow_text = ($icon_layout_type == 2)? '<div class=\'pf-pinarrow\' style=\'border-color: '.$icon_bg_color.' transparent transparent transparent;\'></div>': '';

								$output_data .= 'var pfcat'.$pf_get_term_detail_id.' =';
								$output_data .= ' "<div ';
								$output_data .= 'class=\'pfcat'.$pf_get_term_detail_id_output.'-mapicon pf-map-pin-'.$icon_layout_type.' pf-map-pin-'.$icon_layout_type.'-'.$icon_size.'\'';
								$output_data .= '>';
								$output_data .= '<i class=\''.$icon_name.'\'></i></div>'.$arrow_text.'";'.PHP_EOL;
							}else{
								$output_data .= 'var pfcat'.$pf_get_term_detail_id.' =';
								$output_data .= ' "<div ';
								$output_data .= 'class=\'pfcat'.$pf_get_term_detail_id_output.'-mapicon\'';
								$output_data .= '>';
								$output_data .= '</div>";'.PHP_EOL;
							}
						}else{

							/* Check parent term has settings */

							
							if ($params['cpoint_type'] == 0) {
								$arrow_text = ($params['cpoint_icontype'] == 2)? '<div class=\'pf-pinarrow\' style=\'border-color: '.$params['cpoint_bgcolor'].' transparent transparent transparent;\'></div>': '';

								$output_data .= 'var pfcat'.$pf_get_term_detail_id.' =';
								$output_data .= ' "<div ';
								$output_data .= 'class=\'pfcatdefault-mapicon pf-map-pin-'.$params['cpoint_icontype'].' pf-map-pin-'.$params['cpoint_icontype'].'-'.$params['cpoint_iconsize'].'\'';
								$output_data .= ' >';
								$output_data .= '<i class=\''.$params['cpoint_iconname'].'\' ></i></div>'.$arrow_text.'";'.PHP_EOL;
							}else{
								$output_data .= 'var pfcat'.$pf_get_term_detail_id.' =';
								$output_data .= ' "<div ';
								$output_data .= 'class=\'pfcatdefault-mapicon\'';
								$output_data .= '>';
								$output_data .= '</div>";'.PHP_EOL;
							}
						}
							
					}else{
						
						$retina_number = ($params['setup8_pointsettings_retinapoints'] == 1)?2:1;

						$icon_type = PFPFIssetControl('pscp_'.$pf_get_term_detail_id.'_type','','0');

						$icon_bg_image = PFPFIssetControl('pscp_'.$pf_get_term_detail_id.'_bgimage','','0');

						$icon_layout_type = PFPFIssetControl('pscp_'.$pf_get_term_detail_id.'_icontype','','1');
						$icon_name = PFPFIssetControl('pscp_'.$pf_get_term_detail_id.'_iconname','','');
						$icon_size = PFPFIssetControl('pscp_'.$pf_get_term_detail_id.'_iconsize','','middle');
						$icon_bg_color = PFPFIssetControl('pscp_'.$pf_get_term_detail_id.'_bgcolor','','#b00000');
						
						$arrow_text = ($icon_layout_type == 2)? '<div class=\'pf-pinarrow\' style=\'border-color: '.$icon_bg_color.' transparent transparent transparent;\'></div>': '';

						if ($icon_type == 0 && empty($icon_bg_image)) {

							$output_data .= 'var pfcat'.$pf_get_term_detail_id.' =';
							$output_data .= ' "<div ';
							$output_data .= 'class=\'pfcat'.$pf_get_term_detail_id.'-mapicon pf-map-pin-'.$icon_layout_type.' pf-map-pin-'.$icon_layout_type.'-'.$icon_size.'\'';
							$output_data .= ' >';
							$output_data .= '<i class=\''.$icon_name.'\' ></i></div>";'.PHP_EOL;
						
						}elseif ($icon_type != 0 && !empty($icon_bg_image)){

							$height_calculated = $icon_bg_image['height']/$retina_number;
							$width_calculated = $icon_bg_image['width']/$retina_number;

							$output_data .= 'var pfcat'.$pf_get_term_detail_id.' =';
							$output_data .= ' "<div ';
							$output_data .= 'class=\'pf-map-pin-x\' ';
							$output_data .= 'style=\'background-image:url('.$icon_bg_image['url'].');opacity:'.$params['setup8_pointsettings_pointopacity'].'; background-size:'.$width_calculated.'px '.$height_calculated.'px; width:'.$width_calculated.'px; height:'.$height_calculated.'px;\'';
							$output_data .= ' >';
							$output_data .= '</div>";'.PHP_EOL;
						
						}else{

							$output_data .= 'var pfcat'.$pf_get_term_detail_id.' =';
							$output_data .= ' "<div ';
							$output_data .= 'class=\'pfcat'.$pf_get_term_detail_id.'-mapicon pf-map-pin-'.$icon_layout_type.' pf-map-pin-'.$icon_layout_type.'-'.$icon_size.'\'';
							$output_data .= ' >';
							$output_data .= '<i class=\''.$icon_name.'\' ></i></div>'.$arrow_text.'";'.PHP_EOL;

						}
					}
					
					return $output_data;
				}
			/** 
			*End: GET - Marker Category CSS Name
			**/


			/** 
			*Start: GET - Cats Point Vars - minimized
			**/
				function pf_get_default_cat_images($pflang = ''){
					
					$wpflistdata = '';

					/**
					*Start: Default Point Variables
					**/
						if (PFASSIssetControl('st8_npsys','',0) != 1) {
							$icon_layout_type = PFPFIssetControl('pscp_pfdefaultcat_icontype','','1');
							$icon_name = PFPFIssetControl('pscp_pfdefaultcat_iconname','','');
							$icon_size = PFPFIssetControl('pscp_pfdefaultcat_iconsize','','middle');
							$icon_bg_color = PFPFIssetControl('pscp_pfdefaultcat_bgcolor','','#b00000');

							$arrow_text = ($icon_layout_type == 2)? '<div class=\'pf-pinarrow\' style=\'border-color: '.$icon_bg_color.' transparent transparent transparent;\'></div>': '';

							$wpflistdata .= 'var pfcatdefault =';
							$wpflistdata .= ' "<div ';
							$wpflistdata .= 'class=\'pfcatdefault-mapicon pf-map-pin-'.$icon_layout_type.' pf-map-pin-'.$icon_layout_type.'-'.$icon_size.'\'';
							$wpflistdata .= ' >';
							$wpflistdata .= '<i class=\''.$icon_name.'\' ></i></div>'.$arrow_text.'";'.PHP_EOL;
						}else{
							$icon_layout_type = PFASSIssetControl('cpoint_icontype','',1);
							$icon_name = PFASSIssetControl('cpoint_iconname','','');
							$icon_size = PFASSIssetControl('cpoint_iconsize','','middle');
							$icon_bg_color = PFASSIssetControl('cpoint_bgcolor','','#b00000');

							$arrow_text = ($icon_layout_type == 2)? '<div class=\'pf-pinarrow\' style=\'border-color: '.$icon_bg_color.' transparent transparent transparent;\'></div>': '';

							$wpflistdata .= 'var pfcatdefault =';
							$wpflistdata .= ' "<div ';
							$wpflistdata .= 'class=\'pfcatdefault-mapicon pf-map-pin-'.$icon_layout_type.' pf-map-pin-'.$icon_layout_type.'-'.$icon_size.'\'';
							$wpflistdata .= ' >';
							$wpflistdata .= '<i class=\''.$icon_name.'\' ></i></div>'.$arrow_text.'";'.PHP_EOL;
						}
					/**
					*End: Default Point Variables
					**/



					/**
					*Start: Cat Point Variables
					**/
						
						$pf_get_term_details = get_terms('pointfinderltypes',array('hide_empty'=>false)); 

						if (!empty($pflang) && function_exists('icl_t')) {
							global $sitepress;
							do_action( 'wpml_switch_language', $pflang );
						}

						if(count($pf_get_term_details) > 0){
							$default_language = $current_language = $listing_meta = $cpoint_type = $cpoint_icontype = $cpoint_iconsize = $cpoint_iconname = $cpoint_bgcolor = '';
							
							if (function_exists('icl_t')) {
								$default_language = PF_default_language();
								$current_language = PF_current_language();
							}

							if (PFASSIssetControl('st8_npsys','',0) == 1) {
								$listing_meta = get_option('pointfinderltypes_style_vars');
								$cpoint_type = PFASSIssetControl('cpoint_type','',0);
								$cpoint_icontype = PFASSIssetControl('cpoint_icontype','',1);
								$cpoint_iconsize = PFASSIssetControl('cpoint_iconsize','','middle');
								$cpoint_iconname = PFASSIssetControl('cpoint_iconname','','');
								$cpoint_bgcolor = PFASSIssetControl('cpoint_bgcolor','','#b00000');
							}
							$st8_npsys = PFASSIssetControl('st8_npsys','',0);
						    $setup8_pointsettings_pointopacity = PFSAIssetControl('setup8_pointsettings_pointopacity','','0.7');
						    $setup8_pointsettings_retinapoints = PFSAIssetControl('setup8_pointsettings_retinapoints','','1');

						    if ($st8_npsys == 1) {
						    	foreach ( $pf_get_term_details as $pf_get_term_detail ) {

								$wpflistdata .= pointfinder_get_category_points(
									array(
										'pf_get_term_detail_idm' => $pf_get_term_detail->term_id,
										'pf_get_term_detail_idm_parent' => $pf_get_term_detail->parent,
								        'listing_meta' => $listing_meta,
								        'setup8_pointsettings_pointopacity' => $setup8_pointsettings_pointopacity,
								        'cpoint_type' => $cpoint_type,
										'cpoint_icontype' => $cpoint_icontype,
										'cpoint_iconsize' => $cpoint_iconsize,
										'cpoint_iconname' => $cpoint_iconname,
										'cpoint_bgcolor' => $cpoint_bgcolor,
										'setup8_pointsettings_retinapoints' => $setup8_pointsettings_retinapoints,
										'dlang' => $default_language,
										'clang' => $current_language,
										'st8_npsys' => $st8_npsys
									));

								}
						    }else{
						    	foreach ( $pf_get_term_details as $pf_get_term_detail ) {
									if ($pf_get_term_detail->parent == 0) {
										
										$wpflistdata .= pointfinder_get_category_points(
											array(
											'pf_get_term_detail_idm' => $pf_get_term_detail->term_id,
									        'listing_meta' => $listing_meta,
									        'setup8_pointsettings_pointopacity' => $setup8_pointsettings_pointopacity,
									        'cpoint_type' => $cpoint_type,
											'cpoint_icontype' => $cpoint_icontype,
											'cpoint_iconsize' => $cpoint_iconsize,
											'cpoint_iconname' => $cpoint_iconname,
											'cpoint_bgcolor' => $cpoint_bgcolor,
											'setup8_pointsettings_retinapoints' => $setup8_pointsettings_retinapoints,
											'dlang' => $default_language,
											'clang' => $current_language,
											'st8_npsys' => $st8_npsys
											));

										$pf_get_term_details_sub = get_terms('pointfinderltypes',array('hide_empty'=>false,'parent'=>$pf_get_term_detail->term_id)); 

										foreach ($pf_get_term_details_sub as $pf_get_term_detail_sub) {
											$wpflistdata .= pointfinder_get_category_points(
												array(
													'pf_get_term_detail_idm' => $pf_get_term_detail_sub->term_id,
											        'listing_meta' => $listing_meta,
											        'setup8_pointsettings_pointopacity' => $setup8_pointsettings_pointopacity,
											        'cpoint_type' => $cpoint_type,
													'cpoint_icontype' => $cpoint_icontype,
													'cpoint_iconsize' => $cpoint_iconsize,
													'cpoint_iconname' => $cpoint_iconname,
													'cpoint_bgcolor' => $cpoint_bgcolor,
													'setup8_pointsettings_retinapoints' => $setup8_pointsettings_retinapoints,
													'dlang' => $default_language,
													'clang' => $current_language,
													'st8_npsys' => $st8_npsys
												));
										}

									}
									
								}
						    }
							
							/*
								Loop End from PF Custom Points
							*/

				
						}

					/**
					*End: Cat Point Variables
					**/

					return $wpflistdata;
				}
			/** 
			*End: GET - Marker Cats Vars
			**/

		/** 
		*End: ajax-poidata.php Functions
		**/

		/* Define taxonomy point icons */
		echo pf_get_default_cat_images();
		/* Define taxonomy point icons finished */

		/* Get admin values */
		$setup3_pointposttype_pt1 = PFSAIssetControl('setup3_pointposttype_pt1','','pfitemfinder');
		
		/*Get point limits*/
		if(isset($_POST['spl']) && $_POST['spl']!=''){
			$setup8_pointsettings_limit = $_POST['spl'];
			$setup8_pointsettings_orderby = $_POST['splob'];
			$setup8_pointsettings_order = $_POST['splo'];
		}else{
			$setup8_pointsettings_limit = -1;
		}
		
		/*Search form check*/
		if(isset($_POST['act']) && $_POST['act']!=''){
			$pfaction = esc_attr($_POST['act']);
		}else{
			$pfaction = '';
		}
		

		$pfgetdata['manual_args'] = (!empty($manualargs))? maybe_unserialize(base64_decode($manualargs)): '';
		
		$args = array( 'post_type' => $setup3_pointposttype_pt1, 'posts_per_page' => $setup8_pointsettings_limit, 'post_status' => 'publish');
		
		if(isset($args['meta_query']) == false || isset($args['meta_query']) == NULL){
			$args['meta_query'] = array();
		}

		if(isset($args['tax_query']) == false || isset($args['tax_query']) == NULL){
			$args['tax_query'] = array();
		}
		
		if($setup8_pointsettings_limit > 0){
			
			if($setup8_pointsettings_orderby != ''){$args['orderby']=$setup8_pointsettings_orderby;};
			if($setup8_pointsettings_order != ''){$args['order']=$setup8_pointsettings_order;};
			
		}
		
					
		if($pfaction == 'search'){
			if(isset($_POST['dt']) && $_POST['dt']!=''){
				$pfgetdata = $_POST['dt'];
				if (!is_array($pfgetdata)) {
					$pfgetdata = maybe_unserialize(base64_decode($pfgetdata,true));
					if (is_array($pfgetdata)) {
						foreach ($pfgetdata as $key => $value) {
							$pfnewgetdata[] = array('name' => $key, 'value'=>$value);
						}
						$pfgetdata = $pfnewgetdata;
					}
				}
			}else{
				$pfgetdata = '';
			}
			
				if(is_array($pfgetdata)){
					
					$pfformvars = array();
					
						foreach($pfgetdata as $singledata){
							
							/*Get Values & clean*/
							if (is_array($singledata['value'])) {
								$pfformvars[esc_attr($singledata['name'])] = $singledata['value'];
							}else{
								if(esc_attr($singledata['value']) != ''){
									
									if(isset($pfformvars[esc_attr($singledata['name'])])){
										$pfformvars[esc_attr($singledata['name'])] = $pfformvars[esc_attr($singledata['name'])]. ',' .$singledata['value'];
									}else{
										$pfformvars[esc_attr($singledata['name'])] = $singledata['value'];
									}
								}
							}
						
						}

						$pfgetdata = PFCleanArrayAttr('PFCleanFilters',$pfgetdata);

						/* Added with v1.8.7 */
						$pf_query_builder = new PointfinderSearchQueryBuilder($args);
						$pf_query_builder->setQueryValues($pfformvars,'poidata',array());
						$args = $pf_query_builder->getQuery();	
					
				}
		}else{
			if(isset($_POST['singlepoint']) && !empty($_POST['singlepoint'])){
				$pfitem_singlepoint = esc_attr($_POST['singlepoint']);
				$args['p'] = $pfitem_singlepoint;
				$args['suppress_filters'] = true;
			}
			
		}


		if(isset($_POST['dtx']) && $_POST['dtx']!='' && isset($_POST['dt']) == false ){

			$pfgetdatax = $_POST['dtx'];
			$pfgetdatax = PFCleanArrayAttr('PFCleanFilters',$pfgetdatax);


			if (is_array($pfgetdatax)) {
				foreach ($pfgetdatax as $key => $value) {

					if(isset($value['value'])){
						if (!empty($value['value'])) {
							$args['tax_query'][]=array(
									'taxonomy' => $value['name'],
									'field' => 'id',
									'terms' => pfstring2BasicArray($value['value']),
									'operator' => 'IN'
							);
						}
					}
				}
			}

		}

		/* Check paged for archive and category */
		if(isset($_POST['ppp']) && $_POST['ppp']!=''){
			$ppp = esc_attr($_POST['ppp']);
			$paged = intval(esc_attr($_POST['paged']));
			$order = sanitize_text_field($_POST['order']);
			$orderby = sanitize_text_field($_POST['orderby']);
			if ($ppp != -1) {
				$args['posts_per_page'] = $ppp;
				$args['paged'] = $paged;
				$args['orderby'] = $orderby;
				$args['order'] = $order;
				if($orderby == 'date' || $orderby == 'title'){
					
					$args['orderby'] = array('meta_value_num' => 'DESC' , $orderby => $order);
					$args['meta_key'] = 'webbupointfinder_item_featuredmarker';

					if ($pfrandomize == 'yes') {
						if(isset($args['orderby'][$orderby])){unset($args['orderby'][$orderby]);}
						$args['orderby']['rand']='';
					}

				}else{
					
					$args['meta_key']='webbupointfinder_item_'.$orderby;
					
					if(PFIF_CheckFieldisNumeric_ld($orderby) == false){
						$args['orderby']= array('meta_value' => $order);
					}else{
						$args['orderby']= array('meta_value_num' => $order);
					}
					
				}
			}

		}

		/* Check if lat,lng empty */
		if(isset($_POST['ne']) && $_POST['ne']!=''){
			$ne = esc_attr($_POST['ne']);
		}else{
			$ne = "";
		}
		
		if(isset($_POST['ne2']) && $_POST['ne2']!=''){
			$ne2 = esc_attr($_POST['ne2']);
		}else{
			$ne2 = "";
		}
		
		if(isset($_POST['sw']) && $_POST['sw']!=''){
			$sw = esc_attr($_POST['sw']);
		}else{
			$sw = "";
		}
		
		if(isset($_POST['sw2']) && $_POST['sw2']!=''){
			$sw2 = esc_attr($_POST['sw2']);
		}else{
			$sw2 = "";
		}

		$args['meta_query'][] = array(
			'key' => 'webbupointfinder_items_location',
			'compare' => 'EXISTS'
			
		);

		$args['pf_sw'] = $sw;
		$args['pf_sw2'] = $sw2;
		$args['pf_ne'] = $ne;
		$args['pf_ne2'] = $ne2;


		/* Cleanup query */
		if (isset($args['meta_query'])) {
			if (empty($args['meta_query'])) {
				unset($args['meta_query']);
			}
		}
		if (isset($args['tax_query'])) {
			if (empty($args['tax_query'])) {
				unset($args['tax_query']);
			}
		}

		if (isset($pfgetdata['manual_args']['meta_query'])) {
			if (empty($pfgetdata['manual_args']['meta_query'])) {
				unset($pfgetdata['manual_args']['meta_query']);
			}
		}
		if (isset($pfgetdata['manual_args']['tax_query'])) {
			if (empty($pfgetdata['manual_args']['tax_query'])) {
				unset($pfgetdata['manual_args']['tax_query']);
			}
		}

		
		$setup8_pointsettings_pointopacity = PFSAIssetControl('setup8_pointsettings_pointopacity','','0.7');
		$setup8_pointsettings_retinapoints = PFSAIssetControl('setup8_pointsettings_retinapoints','','1');
		$st8_npsys = PFASSIssetControl('st8_npsys','',0);

		echo PHP_EOL.'var wpflistdata = [';
		if (!empty($args['pf_sw'])) {
			$loop = new Pointfinder_WP_GeoQuery( $args );
		}else{
			$loop = new WP_Query( $args );
		}
			/*
				Check Results	
					print_r($loop->query).PHP_EOL;
					echo $loop->request.PHP_EOL;
					echo $loop->found_posts.PHP_EOL;
				*/
			
			if($loop->post_count > 0){
		
				while ( $loop->have_posts() ) : $loop->the_post();
				
				$coordinates = explode( ',', rwmb_meta('webbupointfinder_items_location') );
				if (!empty($coordinates[0])) {
					if (is_numeric($coordinates[0])) {
							$post_id = get_the_id();

							$pfitemicon = pf_get_markerimage($post_id,$setup8_pointsettings_pointopacity,$setup8_pointsettings_retinapoints,$st8_npsys);

							$pf_cat_idld = PFLangCategoryID_ld($post_id,$pflang,$setup3_pointposttype_pt1);
								
							echo  '{latLng:['.$coordinates[0].','.$coordinates[1].'],';
							if (!empty($pflang)) {
								echo 'data:{id:'.$pf_cat_idld.'},id:'.$pf_cat_idld.',';
							}else{
								echo 'data:{id:'.$post_id.'},id:'.$post_id.',';
							}
							echo 'options:{';
							if(PFControlEmptyArr($pfitemicon)){
								
								if ($pfitemicon['is_cat'] == 1) {
									echo "content:".$pfitemicon['cat'].",flat: true,";
								}

								if ($pfitemicon['is_image'] == 1) {
									echo "content:\"".$pfitemicon['content']."\",flat: true,";
								}
								
							}
							
							echo 'tag:"pfmarker"';
								
							echo '}},';
					}
				}	
				endwhile;
			
			}
			
			
		echo '];';
		
	die();
}

?>