<?php

/**********************************************************************************************************************************
*
* Ajax Info Window Get Results
* 
* Author: Webbu Design
*
***********************************************************************************************************************************/


	add_action( 'PF_AJAX_HANDLER_nopriv_pfget_infowindow', 'pf_ajax_infowindow' );
	add_action( 'PF_AJAX_HANDLER_pfget_infowindow', 'pf_ajax_infowindow' );


function pf_ajax_infowindow(){
	check_ajax_referer( 'pfget_infowindow', 'security' );
    header('Content-Type: text/html; charset=UTF-8;');

	if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
		$id = esc_attr($_REQUEST['id']);

		$multiplemarkers = array();

		$coordinates = explode( ',', esc_attr(get_post_meta( $id, 'webbupointfinder_items_location', true )) );
		$multiplemarkers[0] = PFCheckMultipleMarker($coordinates,$id);
		
		
		/*Info Window*/
		$output_data = '';
		$itemvars = array();

		$setup14_multiplepointsettings_check = PFSAIssetControl('setup14_multiplepointsettings_check','','1');

		if(count($multiplemarkers[0])>0 && $setup14_multiplepointsettings_check == 1){
		$output_data .= '<div id="pf_infowindow_owl" class="owl-carousel">';

			foreach($multiplemarkers[0] as $multiplemarker){
				$output_data .= '<div class="item">';
				$itemvars[$multiplemarker] = PFIF_ItemDetails($multiplemarker);
				$output_data .= PFIF_OutputData($itemvars[$multiplemarker],$multiplemarker);
				$output_data .= '</div>';
			}
		$output_data .= '</div>';
		$output_data .= "<div class='pfifprev pfifbutton'><i class='pfadmicon-glyph-857'></i></div><div class='pfifnext pfifbutton'><i class='pfadmicon-glyph-858'></i></div>";
		}else{
			$itemvars[$id] = PFIF_ItemDetails($id);
			$output_data .= PFIF_OutputData($itemvars[$id],$id);
		}
		
		$output_data .= "<div class='wpf-closeicon'><i class='pfadmicon-glyph-65'></i></div>";
	}
	
	echo $output_data;
	die();
}

?>