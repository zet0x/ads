<?php
/**
* Pointfinder Query Builder class for search
*/
class PointfinderSearchQueryBuilder
{
	
	private $args = array();

	function __construct($args){
		$this->args = $args;
	}


	public function setQueryValues($pfformvars,$location,$searchkeys){

		if(!empty($pfformvars)){
			foreach($pfformvars as $pfformvar => $pfvalue){
				
				$process = true;

				if ($location == 'search') {
					if(in_array($pfformvar, $searchkeys)){
						$process = false;
					}
				}

				if($process && !empty($pfvalue)){
					$thiskeyftype = '';
					$thiskeyftype = PFFindKeysInSearchFieldA_ld($pfformvar);
					
					//Get target field & condition
					$target = PFSFIssetControl('setupsearchfields_'.$pfformvar.'_target','','');
					$target_condition = PFSFIssetControl('setupsearchfields_'.$pfformvar.'_target_according','','');

					switch($thiskeyftype){
						case '1':/*Select*/
							$multiple = PFSFIssetControl('setupsearchfields_'.$pfformvar.'_multiple','','0');

							$rvalues_check = PFSFIssetControl('setupsearchfields_'.$pfformvar.'_rvalues_check','','0');
							
							if($rvalues_check == 0){
								$pfvalue_arr = PFGetArrayValues_ld($pfvalue);
								$fieldtaxname = PFSFIssetControl('setupsearchfields_'.$pfformvar.'_posttax','','');
								
								if (is_array($pfvalue_arr)) {
									if (isset($pfvalue_arr[0])) {
										if (empty($pfvalue_arr[0])) {
											$pfvalue_arr = array();
										}
									}
								}

								$this->args['tax_query'][]=array(
									'taxonomy' => $fieldtaxname,
									'field' => 'id',
									'terms' => $pfvalue_arr,
									'operator' => 'IN'
								);
							}else{
								
								$target_r = PFSFIssetControl('setupsearchfields_'.$pfformvar.'_rvalues_target','','');
								if (empty($target_r)) {
									$target_r = PFSFIssetControl('setupsearchfields_'.$pfformvar.'_rvalues_target_target','','');
								}
								$target_condition_r = PFSFIssetControl('setupsearchfields_'.$pfformvar.'_rvalues_target_according','','');
								

								if (is_array($pfvalue)) {
									if ($target_condition_r == '=') {
										$compare_x = 'IN';
									}else{
										$compare_x = $target_condition_r;
									}
									if(is_numeric($pfvalue)){
										$pfcomptype = 'NUMERIC';
									}else{
										$pfcomptype = 'CHAR';
									}
								}else{
									if(is_numeric($pfvalue)){
										$pfcomptype = 'NUMERIC';
									}else{
										$pfcomptype = 'CHAR';
									}

									if (strpos($pfvalue, ",") != 0) {
										$pfvalue = pfstring2BasicArray($pfvalue);
										if ($target_condition_r == '=') {
											$compare_x = 'IN';
										}else{
											$compare_x = $target_condition_r;
										}
									}else{
										$compare_x = $target_condition_r;
									}
								}
								if (!empty($pfvalue)) {
									$this->args['meta_query'][] = array(
										'key' => 'webbupointfinder_item_'.$target_r,
										'value' => $pfvalue,
										'compare' => $compare_x,
										'type' => $pfcomptype
									);
								}
							}
							
							break;
							
						case '2':/*Slider*/
							$slidertype = PFSFIssetControl('setupsearchfields_'.$pfformvar.'_type','','');
							$pfcomptype = 'NUMERIC';
							
							if($slidertype == 'range'){ 
							$pfvalue = trim($pfvalue,"\0");
								$pfvalue_exp = explode(',',$pfvalue);
															
								$this->args['meta_query'][] = array(
									'key' => 'webbupointfinder_item_'.$target,
									'value' => array($pfvalue_exp[0],$pfvalue_exp[1]),
									'compare' => 'BETWEEN',
									'type' => $pfcomptype
								);
							}else{
								$this->args['meta_query'][] = array(
									'key' => 'webbupointfinder_item_'.$target,
									'value' => $pfvalue,
									'compare' => $target_condition,
									'type' => $pfcomptype
								);
							}
							
							
							break;
							
						case '4':/*Text*/

					  		$target = PFSFIssetControl('setupsearchfields_'.$pfformvar.'_target_target','','');
							
							switch ($target) {
								case 'title':
										$this->args['search_prod_title'] = html_entity_decode($pfvalue);
									break;

								case 'description':
										$this->args['search_prod_desc'] = html_entity_decode($pfvalue);
									break;

								case 'title_description':
										$this->args['search_prod_desc_title'] = html_entity_decode($pfvalue);
									break;

								case 'address':
										$pfcomptype = 'CHAR';
										$this->args['meta_query'][] = array(
											'key' => 'webbupointfinder_items_address',
											'value' => $pfvalue,
											'compare' => 'LIKE',
											'type' => $pfcomptype
										);
									break;

								case 'google':
									break;
								case 'post_tags':
								case 'pointfinderltypes':
								case 'pointfinderitypes':
								case 'pointfinderlocations':
								case 'pointfinderfeatures':
								case 'pointfinderconditions':
									if ($target == 'post_tags') {
										$this->args['tag'] = "$pfvalue";
									}else{
										$this->args['tax_query'][] = array(
											'taxonomy' => $target,
											'field' => 'name',
											'terms' => $pfvalue,
											'operator' => 'IN'
										);
									}
									break;
								default:
										$pfcomptype = 'CHAR';
										$this->args['meta_query'][] = array(
											'key' => 'webbupointfinder_item_'.$target,
											'value' => $pfvalue,
											'compare' => 'LIKE',
											'type' => $pfcomptype
										);
									break;
							}


							break;

						case '5':/*Date*/
							$pfcomptype = 'NUMERIC';

							$setup4_membersettings_dateformat = PFSAIssetControl('setup4_membersettings_dateformat','','1');
							switch ($setup4_membersettings_dateformat) {
								case '1':$datetype = "d-m-Y";break;
								case '2':$datetype = "m-d-Y";break;
								case '3':$datetype = "Y-m-d";break;
								case '4':$datetype = "Y-d-m";break;
							}

							$pfvalue = date_parse_from_format($datetype, $pfvalue);

							$pfvalue = strtotime(date("Y-m-d", mktime(0, 0, 0, $pfvalue['month'], $pfvalue['day'], $pfvalue['year'])));

				     		if (!empty($pfvalue)) {
								
								$this->args['meta_query'][] = array(
									'key' => 'webbupointfinder_item_'.$target,
									'value' => intval($pfvalue),
									'compare' => "$target_condition",
									'type' => "$pfcomptype"
								);
							}

							break;

						case '6':/*checkbox*/
							
							

							if (strpos($pfvalue, ",") != 0) {
								$pfvalue = pfstring2BasicArray($pfvalue);
							}

							

							if (is_array($pfvalue)) {

								$system_cb_setup = PFSAIssetControl('system_cb_setup','',3);
								if ($system_cb_setup == 3) {

									$pfcomptype = 'NUMERIC';
									
									$this->args['meta_query'][] = array(
										'key' => 'webbupointfinder_item_'.$target,
										'value' => $pfvalue,
										'compare' => 'IN',
										'type' => $pfcomptype
										
									);
								}elseif ($system_cb_setup == 2) {
									$this->args['meta_query'][] = array(
										'relation' => 'AND'
									);
									foreach ($pfvalue as $pfvalue_single) {

										if(is_numeric($pfvalue_single)){
											$pfcomptype = 'NUMERIC';
										}else{
											$pfcomptype = 'CHAR';
										}

										$this->args['meta_query'][0][] = array(
											'key' => 'webbupointfinder_item_'.$target,
											'value' => $pfvalue_single,
											'compare' => '=',
											'type' => $pfcomptype
										);
									}
								}elseif ($system_cb_setup == 1) {
									$this->args['meta_query'][] = array(
										'relation' => 'OR'
									);
									foreach ($pfvalue as $pfvalue_single) {
										if(is_numeric($pfvalue_single)){
											$pfcomptype = 'NUMERIC';
										}else{
											$pfcomptype = 'CHAR';
										}

										$this->args['meta_query'][0][] = array(
											'key' => 'webbupointfinder_item_'.$target,
											'value' => $pfvalue_single,
											'compare' => '=',
											'type' => $pfcomptype
										);
									}
									
								}

								
							}else{
								if(is_numeric($pfvalue)){
									$pfcomptype = 'NUMERIC';
								}else{
									$pfcomptype = 'CHAR';
								}

								$this->args['meta_query'][] = array(
									'key' => 'webbupointfinder_item_'.$target,
									'value' => $pfvalue,
									'compare' => '=',
									'type' => $pfcomptype
									
								);
							}
							
							
							break;
					}
				}	
			}
		}

	}


	public function getQuery(){
		return $this->args;
	}
}