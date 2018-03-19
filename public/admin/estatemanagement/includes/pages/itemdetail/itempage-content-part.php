<?php 
/**********************************************************************************************************************************
*
* Item Detail Page - Item Page Content Part
* 
* Author: Webbu Design
***********************************************************************************************************************************/
/**
*Start : Section Header
**/
	if (!function_exists('PFGetItemPageSectionTitle')) {
		function PFGetItemPageSectionTitle($var){
			global $pointfindertheme_option;
			$setup42_itempagedetails_configuration = (isset($pointfindertheme_option['setup42_itempagedetails_configuration']))? $pointfindertheme_option['setup42_itempagedetails_configuration'] : array();
			$output = (isset($setup42_itempagedetails_configuration[$var]['title'])) ? $setup42_itempagedetails_configuration[$var]['title'] : 'None' ;
			
			return $output;
			
		}
	}
/**
*End : Section Header
**/



/**
*Start : Detail Text
**/
	if (!function_exists('PFIF_DetailText_id')) {
		function PFIF_DetailText_id($id){
				
			$pfstart = PFCheckStatusofVar('setup1_slides');
			$output_text = array();
			if($pfstart == true){
				$if_detailtext = '';


				$post_listingtype = wp_get_post_terms( $id, 'pointfinderltypes', array("fields" => "ids") );
				$post_listingtype = isset($post_listingtype[0])?$post_listingtype[0]:'';
				
				$setup22_searchresults_hide_lt  = PFSAIssetControl('setup22_searchresults_hide_lt','','0');
				
				//Prepare detailtext
				
				$setup1_slides = PFSAIssetControl('setup1_slides','','');
				if(is_array($setup1_slides)){	
					foreach ($setup1_slides as &$value) {
						
						$customfield_infocheck = PFCFIssetControl('setupcustomfields_'.$value['url'].'_sitempage','','0');
						$available_fields = array(1,2,3,4,5,7,8,9,14,15);
						
						if(in_array($value['select'], $available_fields) && $customfield_infocheck != 0){


							$PFTMParent = '';
							$ShowField = true;

							if(!empty($post_listingtype)){
								$PFTMParent = pf_get_term_top_most_parent($post_listingtype,'pointfinderltypes');
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
										$post_listingtype = $PFTMParent;
									}
								}
								
								if(in_array($post_listingtype, $ParentItem) ){					
									$ShowField = true;										
								}else{
									$ShowField = false;
								}
							}

							if ($ShowField) {
								if(isset($value['url'])){
									$PF_CF_Val = new PF_CF_Val($id);
									$ClassReturnVal = $PF_CF_Val->GetValue($value['url'],$id,$value['select'],$value['title'],2);
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
				}

				$output_text['content'] = $if_detailtext;

				
				$pfitemtext = '';
				$setup3_pointposttype_pt7s = PFSAIssetControl('setup3_pointposttype_pt7s','','Listing Type');
				$setup3_pointposttype_pt4s = PFSAIssetControl('setup3_pointposttype_pt4s','','Item Type');
				$setup3_pointposttype_pt5s = PFSAIssetControl('setup3_pointposttype_pt5s','','Location');
				$pr_it_v = PFSAIssetControl('pr_it_v','','0');
				if ($pr_it_v == 1) {
					$nstyle_text = ' pfshowallelements';
				}else{$nstyle_text = '';}
				
				if($pfitemtext != ''){
					$info_output_1 = GetPFTermInfo($id,'pointfinderltypes');
					if (!empty($info_output_1)) {
						$output_text['ltypes']= '<div class="pfdetailitem-subelement pf-ltitem clearfix'.$nstyle_text.'">
							<span class="pf-ftitle">'.$pfitemtext.'</span>
							<span class="pf-ftext">'.$info_output_1.'</span>
						</div>';
					}
					
				}else{
					$info_output_1 = GetPFTermInfo($id,'pointfinderltypes');
					if (!empty($info_output_1)) {
						$output_text['ltypes']= '<div class="pfdetailitem-subelement pf-onlyitem clearfix'.$nstyle_text.'">
							<span class="pf-ftitle">'.$setup3_pointposttype_pt7s.' : </span>
							<span class="pfdetail-ftext">'.$info_output_1.'</span>
						</div>';
					}
				}
			

				$setup3_pointposttype_pt4_check = PFSAIssetControl('setup3_pointposttype_pt4_check','','1');
	        	$setup3_pointposttype_pt5_check = PFSAIssetControl('setup3_pointposttype_pt5_check','','1');
				
				if ($setup3_pointposttype_pt4_check == 1) {
					$info_output_2 = GetPFTermInfo($id,'pointfinderitypes');
					if(!empty($info_output_2)){
						$output_text['ltypes'] .= '<div class="pfdetailitem-subelement pf-onlyitem clearfix'.$nstyle_text.'">
							<span class="pf-ftitle">'.$setup3_pointposttype_pt4s.' : </span>
							<span class="pfdetail-ftext">'.$info_output_2.'</span>
						</div>';
					}
				}

				$item_term = pf_get_item_term_id($id);
				$listing_meta = get_option('pointfinderltypes_fevars');

				if ($setup3_pointposttype_pt5_check == 1 && pf_get_listingmeta_limit($listing_meta, $item_term, 'pf_location_area') == 1) {
					$stp4_loc_new = PFSAIssetControl('stp4_loc_new','','0');
					$setup4_submitpage_locationtypes_multiple = PFSAIssetControl('setup4_submitpage_locationtypes_multiple','','0');

					if ($stp4_loc_new == 1 || $setup4_submitpage_locationtypes_multiple == 0) {
						$info_output_3 = GetPFTermInfoH($id,'pointfinderlocations','',1);
						if(!empty($info_output_3)){
							$output_text['ltypes'] .= '<div class="pfdetailitem-subelement pf-onlyitem clearfix">
								<span class="pf-ftitle">'.$setup3_pointposttype_pt5s.' : </span>
								<span class="pfdetail-ftext">'.$info_output_3.'</span>
							</div>';
						}
					}else{
						$info_output_3 = GetPFTermInfoH($id,'pointfinderlocations','',1);
						if(!empty($info_output_3)){
							$output_text['ltypes'] .= '<div class="pfdetailitem-subelement pf-onlyitem clearfix">
								<span class="pf-ftitle">'.$setup3_pointposttype_pt5s.' : </span>
								<span class="pfdetail-ftext">'.$info_output_3.'</span>
							</div>';
						}
					}

					
				}
				$setup3_pt14s = PFSAIssetControl('setup3_pt14s','','Condition');
				$setup3_pt14_check = PFSAIssetControl('setup3_pt14_check','','0');
				
				if ($setup3_pt14_check == 1) {
					$info_output_3 = GetPFTermInfo($id,'pointfinderconditions');
					if(!empty($info_output_3)){
						$output_text['ltypes'] .= '<div class="pfdetailitem-subelement pf-onlyitem clearfix">
							<span class="pf-ftitle">'.$setup3_pt14s.' : </span>
							<span class="pfdetail-ftext">'.$info_output_3.'</span>
						</div>';
					}
				}
				
			}
			unset($PF_CF_Val);
			return $output_text;
		}
	}
/**
*End : Detail Text
**/




/**
*Start : Description Block
**/
	if (!function_exists('pfitempage_description_block')) {
		function pfitempage_description_block(){
			$description_block = '';
			$description_block .= '<section role="itempagedesc" class="pf-itempage-desc-block pf-itempage-elements">';
					
					$description_block .= '<div class="pf-itempage-desc descexpf" itemprop="description">';
						
						$output = do_shortcode(get_the_content());
						$output = apply_filters('convert_chars', $output);
						$output = apply_filters('the_content', $output);


						$description_block .= $output;
					$description_block .= '</div>';
			$description_block .= '</section>';

			return $description_block;
		}
	}

/**
*End : Description Block
**/



/**
*Start : Description 1 Block
**/
	if (!function_exists('pfitempage_description_block1')) {
		function pfitempage_description_block1(){
			$description_block = '';
			$description_block .= '<section role="itempagedesc" class="pf-itempage-desc-block pf-itempage-elements">';
					
					$description_block .= '<div class="pf-itempage-desc descexpf" itemprop="description">';
						$description_block .= do_shortcode(get_the_content());
						$description_block = apply_filters('convert_chars', $description_block);
						$description_block = apply_filters('the_content', $description_block);
					$description_block .= '</div>';
			$description_block .= '</section>';

			return $description_block;
		}
	}

/**
*End : Description 1 Block
**/




/**
*Start : Description 2 Block
**/
	if (!function_exists('pfitempage_description_block2')) {
		function pfitempage_description_block2(){

			$details_block = '';

			$details_block .= '<section role="itempagedetails" class="pf-itempage-details-block pf-itempage-elements">';
				
				$details_block .= '<div class="pf-itempage-details">';
					$output_data = PFIF_DetailText_id(get_the_id());
					if (is_array($output_data)) {
						if (!empty($output_data['ltypes'])) {
							$output_data_ltypes = $output_data['ltypes'];
						} else {
							$output_data_ltypes = '';
						}
						if (!empty($output_data['content'])) {
							$output_data_content = $output_data['content'];
							
						} else {
							$output_data_content = '';
						}
						if (!empty($output_data['priceval'])) {
							$output_data_priceval = $output_data['priceval'];
						} else {
							$output_data_priceval = '';
						}
					} else {
						$output_data_priceval = '';
						$output_data_content = '';
						$output_data_ltypes = '';
					}

					$details_block .= $output_data_priceval;
					$details_block .= $output_data_ltypes;
					$details_block .= $output_data_content;
					
				$details_block .= '</div>';
			$details_block .= '</section>';
			return $details_block;
		}
	}
/**
*End : Description 2 Block
**/



/**
*Start : Featured Image Block
**/
	if (!function_exists('pfitempage_fimage_block')) {
		function pfitempage_fimage_block(){
			$featured_image_output = '';
			$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
			if(!empty($featured_image)){$featured_image_output = '<img src="'.$featured_image[0].'" alt="'.get_the_title().'" class="itp-featured-img"/>';}
			return $featured_image_output;
		}
	}

/**
*End : Featured Image Block
**/




/**
*Start : Details Block
**/
	if (!function_exists('pfitempage_details_block')) {
		function pfitempage_details_block(){
			$details_block = '';

			$details_block .= '<section role="itempagedetails" class="pf-itempage-details-block pf-itempage-elements">';
				
				$details_block .= '<div class="pf-itempage-details">';
					$output_data = PFIF_DetailText_id(get_the_id());
					if (is_array($output_data)) {
						if (!empty($output_data['ltypes'])) {
							$output_data_ltypes = $output_data['ltypes'];
						} else {
							$output_data_ltypes = '';
						}
						if (!empty($output_data['content'])) {
							$output_data_content = $output_data['content'];
						} else {
							$output_data_content = '';
						}
						if (!empty($output_data['priceval'])) {
							$output_data_priceval = $output_data['priceval'];
						} else {
							$output_data_priceval = '';
						}
					} else {
						$output_data_priceval = '';
						$output_data_content = '';
						$output_data_ltypes = '';
					}

					$details_block .= $output_data_priceval;
					$details_block .= $output_data_ltypes;
					$details_block .= $output_data_content;
					
				$details_block .= '</div>';
			$details_block .= '</section>';
			return $details_block;
		}
	}
/**
*End : Details Block
**/



/**
*Start : Features Block
**/
	if (!function_exists('pfitempage_features_block')) {
		function pfitempage_features_block($cols,$subcat_select,$multiple_select){

			$setup3_pointposttype_pt6_check = PFSAIssetControl('setup3_pointposttype_pt6_check','','1');
			$setup3_pointposttype_pt6 = PFSAIssetControl('setup3_pointposttype_pt6','',esc_html__('Features','pointfindert2d'));
			$setup3_modulessetup_awfeatures = PFSAIssetControl('setup3_modulessetup_awfeatures','','0');

			if ($setup3_pointposttype_pt6_check == 1) {

				$features_block = '';

				if ($cols == 1) {
					$col_text = ' col-lg-12 col-md-3 col-sm-4 col-xs-12';
				}elseif($cols == 2){
					$col_text = ' col-lg-6 col-md-4 col-sm-4 col-xs-6';
				}elseif($cols == 3){
					$col_text = ' col-lg-4 col-md-4 col-sm-4 col-xs-6';
				}elseif($cols == 4){
					$col_text = ' col-lg-3 col-md-3 col-sm-4 col-xs-6';
				}else{
					$col_text = ' col-lg-12 col-md-3 col-sm-4 col-xs-12';
				}

				$post_id = get_the_id();

				$featured_terms_item = wp_get_post_terms($post_id, 'pointfinderfeatures',array('fields' => 'ids','hide_empty'=> false));
				$listting_id = wp_get_post_terms($post_id, 'pointfinderltypes', array("fields" => "ids"));
				$listting_id = (isset($listting_id[0]))?$listting_id[0]:'';

				$args = array('orderby'=> 'name', 'order'=> 'ASC','hide_empty'=> false, 'exclude'=> array(), 'exclude_tree'=> array(), 'include'=> array(),'number'=> '', 'fields'=> 'all', 'slug'=> '','parent'=> '','hierarchical'=> true, 'child_of'=> 0, 'get'=> '', 'name__like'=> '','description__like' => '','pad_counts'=> false, 'offset'=> '', 'search'=> '', 'cache_domain'=> 'core'); 

				$featured_terms = get_terms('pointfinderfeatures', $args);
				

		        $features_block_ex = '<div class="pf-row">';

		        if ($subcat_select != 2 || $multiple_select != 2) {
		        	$listting_idn = pf_get_term_top_most_parent($listting_id,'pointfinderltypes');
		        	$listting_id = (isset($listting_idn['parent']))?$listting_idn['parent']:$listting_id;
		        }

		        
		        $ftshlink = PFSAIssetControl('ftshlink','','0');

		        foreach ($featured_terms as $featured_terms_single) {

		        	/*Get term parent*/
					$term_parent = get_option( 'pointfinder_features_customlisttype_' . $featured_terms_single->term_id );

					$output_check = pointfinder_features_tax_output_check($term_parent,$listting_id,'pointfinderfeatures');
					
					if ($output_check == 'ok') {

						$checked_text = '';

						if (!empty($post_id)) {
						
							
							if (is_array($featured_terms_item)) {
								if (in_array($featured_terms_single->term_id, $featured_terms_item)) {
									$checked_text = '921'; $checked_text2 = '';
								}else{
									$checked_text = '471'; $checked_text2 = ' pfcanceldet';
								}
							}
						}
						if ($setup3_modulessetup_awfeatures == 0){
							$features_block_ex .= '<div class="pf-features-detail'.$col_text.$checked_text2.'"><i class="pfadmicon-glyph-'.$checked_text.'"></i> ';
							if($ftshlink==1){$features_block_ex .= '<a href="'.get_term_link($featured_terms_single->term_id,'pointfinderfeatures').'">';}
				        	$features_block_ex .= $featured_terms_single->name;
				        	if($ftshlink==1){$features_block_ex .= '</a>';}
				        	$features_block_ex .= '</div>';	
						}elseif ($setup3_modulessetup_awfeatures == 1) {
							if ($checked_text == '921') {
								$features_block_ex .= '<div class="pf-features-detail'.$col_text.$checked_text2.'"><i class="pfadmicon-glyph-'.$checked_text.'"></i> ';
								if($ftshlink==1){$features_block_ex .= '<a href="'.get_term_link($featured_terms_single->term_id,'pointfinderfeatures').'">';}
				        		$features_block_ex .= $featured_terms_single->name;
				        		if($ftshlink==1){$features_block_ex .= '</a>';}
				        		$features_block_ex .= '</div>';	
							}
						}
						
					}
		        	
		        }

		        $features_block_ex .= '</div>';

		        $features_block = '';

		        if(count($featured_terms) > 0){
			        $features_block .= '<div class="pftrwcontainer hidden-print pf-itempagedetail-element">
											<div class="pfitempagecontainerheader">'.$setup3_pointposttype_pt6.'</div>
											<div class="pfmaincontactinfo">';

					
					$features_block .= '<section role="itempagedetails" class="pf-itempage-features-block pf-itempage-elements">';
						
						$features_block .= '<div class="pf-itempage-features">';
							$features_block .= $features_block_ex;
						$features_block .= '</div>';
					$features_block .= '</section>';
					$features_block .= '</div>
										</div>';
					return $features_block;
				}
			}
		}
	}
/**
*End : Features Block
**/



/**
*Start : Tags Block
**/
	if (!function_exists('pfitempage_tags_block')) {
		function pfitempage_tags_block(){

		        $tags_block = '';

		        $tags_get = wp_get_post_tags( get_the_id());
		        $tags_output = '<ul>';
		        $i = 0;
		        foreach ($tags_get as $tag) {
		        	$tags_output .= '<li><a href="'.get_term_link($tag->term_id,'post_tag').'">'.$tag->name.'</a></li>';$i++;
		        }
		        $tags_output .= '</ul>';

		        if($i>0){
			        $tags_block .= '<div class="pftrwcontainer hidden-print pf-itempagedetail-element pftagscontainer-up">
									<div class="pfitempagecontainerheader pftagscontainer">'.esc_html__('TAGS','pointfindert2d' ).'<div class="pftagsarrow_box"></div></div>
									<div class="pftagsinfo">';
					$tags_block .= $tags_output;
					$tags_block .= '</div></div>';
					return $tags_block;
				}
		}
	}

/**
*End : Tags Block
**/



/**
*Start : Attachments Block
**/
	if (!function_exists('pfitempage_files_block')) {
		function pfitempage_files_block(){

		        $tags_block = '';


		        $files_of_thispost = get_post_meta(get_the_id(),'webbupointfinder_item_files'); 

		        if(PFControlEmptyArr($files_of_thispost)){

		        	$files_count = count($files_of_thispost);
					$output_files = '';
					$i = 1;
					$output_files = '<ul>';
					foreach ($files_of_thispost as $file_number) {
						$file_src_link = wp_get_attachment_url($file_number);
						$file_src = get_attached_file($file_number);
						$output_files .= '<li>';
						$output_files .= '<div class="pf-itemfile-container-id">';
						$output_files .= '<i class="pfadmicon-glyph-33"></i> <a href="'.$file_src_link.'" target="_blank">'.basename($file_src).'</a>';
						$output_files .= '</div>';
						$output_files .= '</li>';
						$i++;
					}
					$output_files .= '</ul>';

		        	$tags_block .= '<div class="pftrwcontainer hidden-print pf-itempagedetail-element">
											<div class="pfitempagecontainerheader">'.esc_html__('Attachments','pointfindert2d' ).'</div>
											<div class="pffileup-container">';
			      
					$tags_block .= $output_files;
					$tags_block .= '</div></div>';
					return $tags_block;
				}
		}
	}
/**
*End : Attachments Block
**/




/**
*Start : Opening Hours Block
**/
	if (!function_exists('pfitempage_ohours_block')) {
		function pfitempage_ohours_block(){
			global $ohour_list_permission;
			$setup3_modulessetup_openinghours = PFSAIssetControl('setup3_modulessetup_openinghours','','0');
			

			if($setup3_modulessetup_openinghours == 1 && $ohour_list_permission == 1){
				$ohours_block = ''; $post_id = get_the_id();
				$ohours_block .= '<section role="itempagefeatures" class="pf-itempage-ohours-block pf-itempage-elements">';
					$ohours_block .= '<div class="pf-itempage-subheader">';
					$ohours_block .= esc_html__( 'Opening Hours', 'pointfindert2d' );
					$ohours_block .= '</div>';
					$ohours_block .= '<div class="pf-itempage-ohours">';
						$ohours_block .= '<ul>';
							$setup3_modulessetup_openinghours_ex = PFSAIssetControl('setup3_modulessetup_openinghours_ex','','1');
							$setup3_modulessetup_openinghours_ex2 = PFSAIssetControl('setup3_modulessetup_openinghours_ex2','','1');
							if ($setup3_modulessetup_openinghours_ex == 1) {
								$is_status_ok = PFcheck_postmeta_exist('webbupointfinder_items_o_o1', $post_id);
								if(!empty($is_status_ok)){
								$ohours_block .= '<li>';
								$ohours_block .= get_post_meta( $post_id, 'webbupointfinder_items_o_o1', true );
								$ohours_block .= '</li>';
								}
							}else{


								$ohours_value = get_post_meta( $post_id, 'webbupointfinder_items_o_o1', true );
								if ($ohours_value == '-' || empty($ohours_value)) {
									$ohours_value = esc_html__('Closed','pointfindert2d' );
								}

								$ohours_first = '<li><span class="pf-ftitle">';
								$ohours_first .= __('Monday','pointfindert2d');
								$ohours_first .= ' :</span><span class="pfdetail-ftext">'.$ohours_value;
								$ohours_first .= '</span></li>';




								$ohours_value = get_post_meta( $post_id, 'webbupointfinder_items_o_o7', true );
								if ($ohours_value == '-' || empty($ohours_value)) {
									$ohours_value = esc_html__('Closed','pointfindert2d' );
								}

								$ohours_last = '<li><span class="pf-ftitle">';
								$ohours_last .= __('Sunday','pointfindert2d');
								$ohours_last .= ' :</span><span class="pfdetail-ftext">'.$ohours_value;
								$ohours_last .= '</span></li>';


								if ($setup3_modulessetup_openinghours_ex2 != 1) {
									$ohours_first = $ohours_last . $ohours_first;
									$ohours_last = '';
								}


								$ohours_block .= $ohours_first;

								$ohours_value = get_post_meta( $post_id, 'webbupointfinder_items_o_o2', true );
								if ($ohours_value == '-' || empty($ohours_value)) {
									$ohours_value = esc_html__('Closed','pointfindert2d' );
								}

								$ohours_block .= '<li><span class="pf-ftitle">';
								$ohours_block .= __('Tuesday','pointfindert2d');
								$ohours_block .= ' :</span><span class="pfdetail-ftext">'.$ohours_value;
								$ohours_block .= '</span></li>';


								$ohours_value = get_post_meta( $post_id, 'webbupointfinder_items_o_o3', true );
								if ($ohours_value == '-' || empty($ohours_value)) {
									$ohours_value = esc_html__('Closed','pointfindert2d' );
								}

								$ohours_block .= '<li><span class="pf-ftitle">';
								$ohours_block .= __('Wednesday','pointfindert2d');
								$ohours_block .= ' :</span><span class="pfdetail-ftext">'.$ohours_value;
								$ohours_block .= '</span></li>';


								$ohours_value = get_post_meta( $post_id, 'webbupointfinder_items_o_o4', true );
								if ($ohours_value == '-' || empty($ohours_value)) {
									$ohours_value = esc_html__('Closed','pointfindert2d' );
								}

								$ohours_block .= '<li><span class="pf-ftitle">';
								$ohours_block .= __('Thursday','pointfindert2d');
								$ohours_block .= ' :</span><span class="pfdetail-ftext">'.$ohours_value;
								$ohours_block .= '</span></li>';



								$ohours_value = get_post_meta( $post_id, 'webbupointfinder_items_o_o5', true );
								if ($ohours_value == '-' || empty($ohours_value)) {
									$ohours_value = esc_html__('Closed','pointfindert2d' );
								}

								$ohours_block .= '<li><span class="pf-ftitle">';
								$ohours_block .= __('Friday','pointfindert2d');
								$ohours_block .= ' :</span><span class="pfdetail-ftext">'.$ohours_value;
								$ohours_block .= '</span></li>';


								$ohours_value = get_post_meta( $post_id, 'webbupointfinder_items_o_o6', true );
								if ($ohours_value == '-' || empty($ohours_value)) {
									$ohours_value = esc_html__('Closed','pointfindert2d' );
								}

								$ohours_block .= '<li><span class="pf-ftitle">';
								$ohours_block .= __('Saturday','pointfindert2d');
								$ohours_block .= ' :</span><span class="pfdetail-ftext">'.$ohours_value;
								$ohours_block .= '</span></li>';



								$ohours_block .= $ohours_last;

								
						
							}
						
						$ohours_block .= '</ul>';
					$ohours_block .= '</div>';
				$ohours_block .= '</section>';
				return $ohours_block;
			}
		}
	}
/**
*End : Opening Hours Block
**/




/**
*Start : Function for Column
**/
	if (!function_exists('pf_itemdetail_fullcol')) {
		function pf_itemdetail_fullcol($content){
			$return_val = '';
			$return_val .= '<div class="pf-container"><div class="pf-row"><div class="col-lg-12">';
				$return_val .= $content;
			$return_val .= '</div></div></div>';
			return $return_val;
		}
	}
	if (!function_exists('pf_itemdetail_halfcol')) {
		function pf_itemdetail_halfcol($content1,$content2){
			$return_val = '';
			$return_val .= '<div class="pf-container"><div class="pf-row"><div class="col-lg-6">';
				$return_val .= $content1;
			$return_val .= '</div>';
			$return_val .= '<div class="col-lg-6">';
				$return_val .= $content2;
			$return_val .= '</div></div></div>';
			return $return_val;
		}
	}
	if (!function_exists('pf_itemdetail_thirdcol')) {
		function pf_itemdetail_thirdcol($content1,$content2){
			$return_val = '';
			$return_val .= '<div class="pf-container"><div class="pf-row"><div class="col-lg-4">';
				$return_val .= $content1;
			$return_val .= '</div>';
			$return_val .= '<div class="col-lg-8 pf-offsetarea">';
				$return_val .= $content2;
			$return_val .= '</div></div></div>';
			return $return_val;
		}
	}
	if (!function_exists('pf_itemdetail_thirdcolx')) {
		function pf_itemdetail_thirdcolx($content1,$content2){
			$return_val = '';
			$return_val .= '<div class="pf-container"><div class="pf-row"><div class="col-lg-8">';
				$return_val .= $content1;
			$return_val .= '</div>';
			$return_val .= '<div class="col-lg-4 pf-offsetarea">';
				$return_val .= $content2;
			$return_val .= '</div></div></div>';
			return $return_val;
		}
	}
	if (!function_exists('pf_itemdetail_thirdcols1')) {
		function pf_itemdetail_thirdcols1($content1,$content2){
			$return_val = '';
			$return_val .= '<div class="pf-container"><div class="pf-row"><div class="col-lg-4 col-md-4 col-sm-3">';
				$return_val .= $content1;
			$return_val .= '</div>';
			$return_val .= '<div class="col-lg-8 col-md-8 col-sm-8">';
				$return_val .= $content2;
			$return_val .= '</div></div></div>';
			return $return_val;
		}
	}
	if (!function_exists('pf_itemdetail_thirdcolxs1')) {
		function pf_itemdetail_thirdcolxs1($content1,$content2){
			$return_val = '';
			$return_val .= '<div class="pf-container"><div class="pf-row"><div class="col-lg-8 col-md-8 col-sm-9">';
				$return_val .= $content1;
			$return_val .= '</div>';
			$return_val .= '<div class="col-lg-4 col-md-4 col-sm-3">';
				$return_val .= $content2;
			$return_val .= '</div></div></div>';
			return $return_val;
		}
	}
	if (!function_exists('pf_itemdetail_forthcol')) {
		function pf_itemdetail_forthcol($content1,$content2,$content3){
			$return_val = '';

			$return_val .= '<div class="pf-container">';
					$return_val .= '<div class="pf-row">';
							$return_val .= '<div class="col-lg-4">';
									$return_val .= '<div class="pf-row">';
										$return_val .= '<div class="col-lg-12">';
											$return_val .= $content1;
										$return_val .= '</div>';
										$return_val .= '<div class="col-lg-12">';
											$return_val .= $content2;
										$return_val .= '</div>';
									$return_val .= '</div>';
							$return_val .= '</div>';


							$return_val .= '<div class="col-lg-8 pf-offsetarea">';
								$return_val .= $content3;
							$return_val .= '</div>';

					$return_val .= '</div>';
			$return_val .= '</div>';

			return $return_val;
		}
	}
	if (!function_exists('pf_itemdetail_forthcolx')) {
		function pf_itemdetail_forthcolx($content1,$content2,$content3){
			$return_val = '';

			$return_val .= '<div class="pf-container">';
					$return_val .= '<div class="pf-row">';

							$return_val .= '<div class="col-lg-8">';
								$return_val .= $content1;
							$return_val .= '</div>';

							$return_val .= '<div class="col-lg-4 pf-offsetarea">';
									$return_val .= '<div class="pf-row">';
										$return_val .= '<div class="col-lg-12">';
											$return_val .= $content2;
										$return_val .= '</div>';
										$return_val .= '<div class="col-lg-12">';
											$return_val .= $content3;
										$return_val .= '</div>';
									$return_val .= '</div>';
							$return_val .= '</div>';

					$return_val .= '</div>';
			$return_val .= '</div>';

			return $return_val;
		}
	}
	if (!function_exists('pf_itemdetail_fifthcol')) {
		function pf_itemdetail_fifthcol($content1,$content2,$content3){
			$return_val = '';

			$return_val .= '<div class="pf-container">';
					$return_val .= '<div class="pf-row">';
							$return_val .= '<div class="col-lg-3">';
								$return_val .= $content1;
							$return_val .= '</div>';


							$return_val .= '<div class="col-lg-9 pf-offsetarea">';
								$return_val .= '<div class="pf-row">';
										$return_val .= '<div class="col-lg-12">';
											$return_val .= $content2;
										$return_val .= '</div>';
										$return_val .= '<div class="col-lg-12">';
											$return_val .= $content3;
										$return_val .= '</div>';
									$return_val .= '</div>';
							$return_val .= '</div>';

					$return_val .= '</div>';
			$return_val .= '</div>';

			return $return_val;
		}
	}

/**
*End : Function for Column
**/
?>