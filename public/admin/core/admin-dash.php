<?php
/**********************************************************************************************************************************
*
* System Status Widget for PointFinder Theme
* 
* Author: Webbu Design
*
***********************************************************************************************************************************/

if (is_user_logged_in()) {
	if (current_user_can('activate_plugins')) {
		add_action( 'admin_enqueue_scripts', 'pf_dashboard_widget_scripts' );

		function pf_dashboard_widget_scripts() {
			$screen = get_current_screen();
			if ($screen->id == 'dashboard') {
				wp_register_style( 'dashboard-widget-style', get_template_directory_uri() . '/admin/core/css/dashboard-custom.css', false, '1.0.0' );
		        wp_enqueue_style( 'dashboard-widget-style' );
			}     
		}

		add_action( 'wp_dashboard_setup', 'pf_prefix_add_dashboard_widget' );

		function pf_prefix_add_dashboard_widget() {
		    wp_add_dashboard_widget( 'pfstatusofsystemwidget', esc_html__( 'PF SYSTEM STATUS', 'pointfindert2d' ), 'pf_status_of_system' );
		}

		add_action( 'wp_dashboard_setup', 'pf_prefix_add_dashboard_widget2' );

		function pf_prefix_add_dashboard_widget2() {
		    wp_add_dashboard_widget( 'pfstatusofsystemwidget2', esc_html__( 'PF SYSTEM HEALTH', 'pointfindert2d' ), 'pf_status_of_system2' );
		}


		function pf_status_of_system() {

			global $wpdb;
			$theme = wp_get_theme();

			
			echo '<div class="pfawidget">';
			echo '<div class="pfawidget-body">';
		 	echo '<div class="pfaflash">'.esc_html__('You are using','pointfindert2d').'  <strong>Point Finder v'.$theme->version.'</div>';
		 	
		 	global $current_user;
        	$user_id = $current_user->ID;

		 	$user_update_not = get_user_meta($user_id, 'pointfinder_afterinstall_admin_notice',true );
		 	$user_update_not2 = get_user_meta($user_id, 'pointfinder_afterv16_admin_notice',true );
		 	if (!empty($user_update_not)) {		 	
		 		//echo '<div class="updatenotpf1"><a href="?pointfinderafterinstall_nag_enable=0"><strong>View Point Finder Help Doc Information</strong></a></div>';
		 	}
		 	if (!empty($user_update_not2)) {		 	
		 		//echo '<div class="updatenotpf1"><a href="?pointfinderafterv16_nag_enable=0"><strong>View v1.6 Update Notification</strong></a></div>';
		 	}

		 	echo '<div class="accordion">';

			if(PFSAIssetControl('setup4_membersettings_loginregister','','1') == 1){


				$setup3_pointposttype_pt1 = PFSAIssetControl('setup3_pointposttype_pt1','','pfitemfinder');
				$pf_published_items = $wpdb->get_var($wpdb->prepare("select count(ID) from $wpdb->posts where post_type='%s' and post_status='%s'",$setup3_pointposttype_pt1,'publish'));

				if(PFSAIssetControl('setup4_membersettings_frontend','','1') == 1){
					
					$pf_pendingapproval_items = $wpdb->get_var($wpdb->prepare("select count(ID) from $wpdb->posts where post_type='%s' and post_status='%s'",$setup3_pointposttype_pt1,'pendingapproval'));
					$pf_pendingpayment_items = $wpdb->get_var($wpdb->prepare("select count(ID) from $wpdb->posts where post_type='%s' and post_status='%s'",$setup3_pointposttype_pt1,'pendingpayment'));

					echo '
					<div class="accordion-header"><h2>'.esc_html__('MAIN SYSTEM STATUS','pointfindert2d').'</h2></div>
					<div class="accordion-body">
						<div class="accordion-mainit">
							<div class="accordion-status-text"><a href="'.admin_url("edit.php?post_status=publish&post_type=$setup3_pointposttype_pt1").'">'.$pf_published_items.'</a></div>
							'.esc_html__('Published','pointfindert2d').'         
						</div>
						<div class="accordion-mainit">
							<div class="accordion-status-text"><a href="'.admin_url("edit.php?post_status=pendingapproval&post_type=$setup3_pointposttype_pt1").'">'.$pf_pendingapproval_items.'</a></div>
							'.esc_html__('Pending Approval','pointfindert2d').'          
						</div>
						<div class="accordion-mainit">
							<div class="accordion-status-text"><a href="'.admin_url("edit.php?post_status=pendingpayment&post_type=$setup3_pointposttype_pt1").'">'.$pf_pendingpayment_items.'</a></div>
							'.esc_html__('Pending Payment','pointfindert2d').'
						</div>
					</div>
					';

				}else{
					echo '
					<div class="accordion-header"><h2>'.esc_html__('MAIN SYSTEM STATUS','pointfindert2d').'</h2></div>
					<div class="accordion-body">
						<div class="accordion-mainit">
							<div class="accordion-status-text">'.$pf_published_items.'</div>
							'.esc_html__('Published','pointfindert2d').'         
						</div>
					</div>
					';

				}
			}


			if (PFREVSIssetControl('setup11_reviewsystem_check','','0') == 1) {
				$pf_published_reviews = $wpdb->get_var($wpdb->prepare("select count(ID) from $wpdb->posts where post_type='%s' and post_status='%s'",'pointfinderreviews','publish'));
				$pf_pendingapproval_reviews = $wpdb->get_var($wpdb->prepare("select count(ID) from $wpdb->posts where post_type='%s' and post_status='%s'",'pointfinderreviews','pendingapproval'));
				$pf_pendingpayment_reviews = $wpdb->get_var($wpdb->prepare("select count(ID) from $wpdb->posts where post_type='%s' and post_status='%s'",'pointfinderreviews','pendingpayment'));
				
				echo '
				<div class="accordion-header">
					<h2>'.esc_html__('REVIEW SYSTEM STATUS','pointfindert2d').'</h2>
				</div>
				<div class="accordion-body">
					<div class="accordion-mainit">
						<div class="accordion-status-text">'.$pf_published_reviews.'</div>
						'.esc_html__('Published','pointfindert2d').'        
					</div>
					<div class="accordion-mainit">
						<div class="accordion-status-text">'.$pf_pendingapproval_reviews.'</div>
						'.esc_html__('Pending Approval','pointfindert2d').'          
					</div>
					<div class="accordion-mainit">
						<div class="accordion-status-text">'.$pf_pendingpayment_reviews.'</div>
						'.esc_html__('Pending Check','pointfindert2d').'
					</div>
				</div>
				';
			}

			echo '</div></div></div>';
		}

		function pf_status_of_system2() {
			
			echo '<div class="pfawidget">';
			echo '<div class="pfawidget-body">';

		 	echo '<div class="accordion">';

			$ssl_text = $api_text = $api_text2 = $dash_text = $miv_text = $met_text = $ml_text = $pms_text = $umfs_text = $curl_text = $php_text = $mfu_text = $mit_text = '';

			$miv_css = $met_css = $api_css = $api_css2 = $ssl_css = $dash_css = $ml_css = $pms_css = $umfs_css = $curl_css = $php_css = $mfu_css = $mit_css = ' pf-st-ok';

			$ssl_check = (is_ssl())? '<span class="dashicons dashicons-yes"></span>':'<span class="dashicons dashicons-no-alt"></span>';
			if (!is_ssl()) {
				$ssl_text = '<br/><small>'.sprintf(esc_html__('You are not using ssl and you may have problems on google map. Please read %sthis article%s.','pointfindert2d'),'<a href="http://support.webbudesign.com/forums/topic/no-https-then-say-goodbye-to-geolocation-in-chrome-50/" target="blank">','</a>').'</small>';
				$ssl_css = '';
			}

			$setup5_map_key = PFSAIssetControl('setup5_map_key','','');
			$api_check = (!empty($setup5_map_key))? '<span class="dashicons dashicons-yes"></span>':'<span class="dashicons dashicons-no-alt"></span>';
			if (empty($setup5_map_key)) {
				$api_text = '<br/><small>'.sprintf(esc_html__('You are not using Google Map API key and you may have problems on google map. Please read %sthis article%s.','pointfindert2d'),'<a href="http://docs.pointfindertheme.com/?p=765" target="blank">','</a>').'</small>';
				$api_css = '';
			}
			/*
			$setup5_map_keys = PFSAIssetControl('setup5_map_keys','','');
			$api_check2 = (!empty($setup5_map_keys))? '<span class="dashicons dashicons-yes"></span>':'<span class="dashicons dashicons-no-alt"></span>';
			if (empty($setup5_map_keys)) {
				$api_text2 = '<br/><small>'.sprintf(esc_html__('Please add this Server API key for validate frontend upload form coordinates. Please read %sthis article%s.','pointfindert2d'),'<a href="https://developers.google.com/maps/faq#switch-key-type" target="blank">','</a>').'</small>';
				$api_css2 = '';
			}
			*/

			$setup4_membersettings_dashboard = PFSAIssetControl('setup4_membersettings_dashboard','','');
			$dash_check = (!empty($setup5_map_key))? '<span class="dashicons dashicons-yes"></span>':'<span class="dashicons dashicons-no-alt"></span>';
			if (empty($setup5_map_key)) {
				$dash_text = '<br/><small>'.sprintf(esc_html__('Your dashboard page not configured and you may have problems on you site. Please read %sthis article%s.','pointfindert2d'),'<a href="http://docs.pointfindertheme.com/?p=405" target="blank">','</a>').'</small>';
				$dash_css = '';
			}

			echo '
			<div class="accordion-header">
				<h2>'.esc_html__('SYSTEM HEALTH CHECK','pointfindert2d').'</h2>
			</div>
			<div class="accordion-body">
				<div class="accordion-mainit">
					<div class="accordion-status-text'.$ssl_css.'">'.$ssl_check.'</div>
					'.esc_html__('SSL Check','pointfindert2d').$ssl_text.'   
				</div>
				<div class="accordion-mainit">
					<div class="accordion-status-text'.$api_css.'">'.$api_check.'</div>
					'.esc_html__('Google API Key Check (Browser)','pointfindert2d').$api_text.'          
				</div>

				<div class="accordion-mainit">
					<div class="accordion-status-text'.$dash_css.'">'.$dash_check.'</div>
					'.esc_html__('Dashboard Page Check','pointfindert2d').$dash_text.'
				</div>
			</div>
			';


			$miv_check = ini_get('max_input_vars');

			if ($miv_check <= 3000) {
				$miv_text = '<br/><small>'.sprintf(esc_html__('You have to increase this value otherwise you may have problems. Please read %sthis article%s.','pointfindert2d'),'<a href="http://docs.pointfindertheme.com/?p=109" target="blank">','</a>').'</small>';
				$miv_css = '';
			}

			$ml_check = ini_get('memory_limit');
			if (in_array($ml_check, array('32M','64M','128M'))) {
				$ml_text = '<br/><small>'.sprintf(esc_html__('You have to increase this value otherwise you may have problems. Please read %sthis article%s.','pointfindert2d'),'<a href="http://docs.pointfindertheme.com/?p=705" target="blank">','</a>').'</small>';
				$ml_css = '';
			}
			
			$met_check = ini_get('max_execution_time');
			if ($met_check < 400) {
				$met_text = '<br/><small>'.esc_html__('You have to increase this value otherwise you may have problems. Recommended value: 400 or more','pointfindert2d').'</small>';
				$met_css = '';
			}

			$pms_check = ini_get('post_max_size');
			if (in_array($pms_check, array('2M','4M','8M','16M'))) {
				$pms_text = '<br/><small>'.sprintf(esc_html__('You have to increase this value otherwise you may have problems. Please read %sthis article%s.','pointfindert2d'),'<a href="http://docs.pointfindertheme.com/?p=10" target="blank">','</a>').'</small>';
				$pms_css = '';
			}

			$umfs_check = ini_get('post_max_size');
			if (in_array($umfs_check, array('2M','4M','8M','16M'))) {
				$umfs_text = '<br/><small>'.sprintf(esc_html__('You have to increase this value otherwise you may have problems. Please read %sthis article%s.','pointfindert2d'),'<a href="http://docs.pointfindertheme.com/?p=10" target="blank">','</a>').'</small>';
				$umfs_css = '';
			}

			$php_version_num = (function_exists('phpversion'))?phpversion():'';
			$curl_version_num = (function_exists('curl_version'))?curl_version():'';
			$curl_version_num = (isset($curl_version_num['version']))?$curl_version_num['version']:'<span class="dashicons dashicons-no-alt"></span>';

			$mfu_check = ini_get('max_file_uploads');
			$mit_check = ini_get('max_input_time');

			if(version_compare($curl_version_num, "7.34.0", "<=")){
				$curl_text = '<br/><small>'.sprintf(esc_html__('You have to use v7.34.0 with TLS 1.2 for Paypal Payments otherwise you may have problems. Please read %sthis article%s.','pointfindert2d'),'<a href="http://support.webbudesign.com/forums/topic/paypal-tls-v1-2-upgrade/" target="blank">','</a>').'</small>';
				$curl_css = '';
			}

			if(version_compare($php_version_num, "5.4.0", "<=")){
				$php_text = '<br/><small>'.esc_html__('You have to use php v5.3.x otherwise you may have problems.','pointfindert2d').'</small>';
				$php_css = '';
			}


			if ($mfu_check < 20) {
				$mfu_text = '<br/><small>'.esc_html__('You have to increase this value otherwise you may have problems. Recommended value: 20 or more','pointfindert2d').'</small>';
				$mfu_css = '';
			}

			if ($mit_check < 20) {
				$mit_text = '<br/><small>'.esc_html__('You have to increase this value otherwise you may have problems. Recommended value: 20 or more','pointfindert2d').'</small>';
				$mit_css = '';
			}

			echo '
			<div class="accordion-header">
				<h2>'.esc_html__('PHP VARIABLES CHECK','pointfindert2d').'</h2>
			</div>
			<div class="accordion-body">
				<div class="accordion-mainit">
					<div class="accordion-status-text'.$miv_css.'">'.$miv_check.'</div>
					'.esc_html__('max_input_vars','pointfindert2d').$miv_text.'   
				</div>
				<div class="accordion-mainit">
					<div class="accordion-status-text'.$ml_css.'">'.$ml_check.'</div>
					'.esc_html__('memory_limit','pointfindert2d').$ml_text.'          
				</div>
				<div class="accordion-mainit">
					<div class="accordion-status-text'.$met_css.'">'.$met_check.'</div>
					'.esc_html__('max_execution_time','pointfindert2d').$met_text.'
				</div>
				<div class="accordion-mainit">
					<div class="accordion-status-text'.$pms_css.'">'.$pms_check.'</div>
					'.esc_html__('post_max_size','pointfindert2d').$pms_text.'
				</div>
				<div class="accordion-mainit">
					<div class="accordion-status-text'.$umfs_css.'">'.$umfs_check.'</div>
					'.esc_html__('upload_max_filesize','pointfindert2d').$umfs_text.'
				</div>

				<div class="accordion-mainit">
					<div class="accordion-status-text'.$mfu_css.'">'.$mfu_check.'</div>
					'.esc_html__('max_file_uploads','pointfindert2d').$mfu_text.'
				</div>

				<div class="accordion-mainit">
					<div class="accordion-status-text'.$mit_css.'">'.$mit_check.'</div>
					'.esc_html__('max_input_time','pointfindert2d').$mit_text.'
				</div>

				<div class="accordion-mainit">
					<div class="accordion-status-text'.$curl_css.'">'.$curl_version_num.'</div>
					'.esc_html__('cURL Version Check','pointfindert2d').$curl_text.'
				</div>

				<div class="accordion-mainit">
					<div class="accordion-status-text'.$php_css.'">'.$php_version_num.'</div>
					'.esc_html__('Php Version Check','pointfindert2d').$php_text.'
				</div>
			</div>
			';

			echo '</div></div></div>';
		}
	}

	
}