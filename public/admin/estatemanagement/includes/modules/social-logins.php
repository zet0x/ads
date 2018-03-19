<?php
/**********************************************************************************************************************************
*
* Social Logins (Facebook)
* 
* Author: Webbu Design
*
***********************************************************************************************************************************/
function PointFinder_Social_Facebook_Logout() {
	/*if(PFASSIssetControl('setup4_membersettings_facebooklogin','','0') == 1){
		$setup4_membersettings_facebooklogin_appid = PFASSIssetControl('setup4_membersettings_facebooklogin_appid','','');
		$setup4_membersettings_facebooklogin_secretid = PFASSIssetControl('setup4_membersettings_facebooklogin_secretid','','');
		
		if ($setup4_membersettings_facebooklogin_appid != '' && $setup4_membersettings_facebooklogin_secretid != '') {
			$facebook = new Facebook(array(
			'appId'  => $setup4_membersettings_facebooklogin_appid,
			'secret' => $setup4_membersettings_facebooklogin_secretid,
			'cookie' => true
			));
			$facebook->destroySession();
		}
	}*/
}
add_action('wp_logout', 'PointFinder_Social_Facebook_Logout');

if(isset($_GET['pferror']) && $_GET['pferror']!=''){
	$pferror = esc_attr($_GET['pferror']);
}


add_action('wp_footer','PF_SocialErrorHandler',400);
function PF_SocialErrorHandler($pferror){

	if(!empty($pferror)){
		switch ($pferror) {
			case 'fbem':
				$pferror_text = sprintf(esc_html__('Please complete %s Api setup from Admin Panel first.','pointfindert2d'),esc_html__('Facebook','pointfindert2d'));
				$pftype = 0;
				break;
			case 'tbem':
				$pferror_text = sprintf(esc_html__('Please complete %s Api setup from Admin Panel first.','pointfindert2d'),esc_html__('Twitter','pointfindert2d'));
				$pftype = 0;
				break;
			
			case 'gbem':
				$pferror_text = sprintf(esc_html__('Please complete %s Api setup from Admin Panel first.','pointfindert2d'),esc_html__('Google+','pointfindert2d'));
				$pftype = 0;
				break;
			case 'vbem':
				$pferror_text = sprintf(esc_html__('Please complete %s Api setup from Admin Panel first.','pointfindert2d'),esc_html__('VK','pointfindert2d'));
				$pftype = 0;
				break;

			case 'gbue':
				$pferror_text = sprintf(esc_html__('Can not login with %s','pointfindert2d'),esc_html__('Google+','pointfindert2d'));
				$pftype = 0;
				break;
			case 'vbue':
				$pferror_text = sprintf(esc_html__('Can not login with %s','pointfindert2d'),esc_html__('VK','pointfindert2d'));
				$pftype = 0;
				break;


			case 'fbux':
				$pferror_text = esc_html__('The user can not found in our system. Login not completed.','pointfindert2d');
				$pftype = 0;
				break;



			case 'tbem2':
				$pferror_text = esc_html__('Could not connect to the Twitter. Please try again later.','pointfindert2d');
				$pftype = 0;
				break;
			case 'tbem3':
				$pferror_text = esc_html__('The session is old. Please close/reopen your browser and try again.','pointfindert2d');
				$pftype = 0;
				break;
			case 'tbem4':
				$pferror_text = esc_html__('We could not verify your twitter account.','pointfindert2d');
				$pftype = 0;
				break;

			default:
				$pferror_text = esc_html__('No information','pointfindert2d');
				$pftype = 0;
				break;
		}

		echo '<script type="text/javascript">(function($) {"use strict";$(function() {$.pfOpenLogin("open","error","'.$pferror_text.'","'.$pftype.'");});})(jQuery);</script>';
	}
}


add_action('wp_footer','PF_SocialModalHandler',400);
function PF_SocialModalHandler(){
	if(isset($_GET['ctype'])){
		$scontenttype = $_GET['ctype'];
	}else{
		$scontenttype = '';
	}
	if(isset($_GET['ctext'])){
		$scontenttext = $_GET['ctext'];
	}else{
		$scontenttext = '';
	}

	if(!empty($scontenttype) && !empty($scontenttext)){
		echo '<script type="text/javascript">(function($) {"use strict";$(function() {
		$.pfOpenLogin("open","scontent","'.$scontenttext.'","'.$scontenttype.'");
		});})(jQuery);</script>';
	}
}


function PointFinder_Social_Facebook_Login(){
		if(PFASSIssetControl('setup4_membersettings_facebooklogin','','0') == 1){
			require_once( get_template_directory().'/admin/core/Facebook/autoload.php' );
		};

		if(isset($_GET['uaf']) && $_GET['uaf']!=''){
			$ua_action = esc_attr($_GET['uaf']);
		}

		$homeurllink = site_url($path = '/');
		$pfmenu_perout = PFPermalinkCheck();


		$setup4_membersettings_requestupdateinfo = PFASSIssetControl('setup4_membersettings_requestupdateinfo','','1');
		$setup4_membersettings_dashboard = PFSAIssetControl('setup4_membersettings_dashboard','',site_url());
		$setup4_membersettings_dashboard_link = get_permalink($setup4_membersettings_dashboard);


		$special_linkurl = $homeurllink;
		switch ($setup4_membersettings_requestupdateinfo) {
			case '1':
				$special_linkurl = $setup4_membersettings_dashboard_link.$pfmenu_perout.'ua=profile';
				break;
			case '2':
				$special_linkurl = $setup4_membersettings_dashboard_link.$pfmenu_perout.'ua=myitems';
				break;
			case '3':
				$special_linkurl = $homeurllink;
				break;
		}

		if(isset($ua_action)){

			

			/**
			*Start : Facebook Login
			**/
			if ($ua_action == 'fblogin') {

				$setup4_membersettings_facebooklogin_appid = PFASSIssetControl('setup4_membersettings_facebooklogin_appid','','');
				$setup4_membersettings_facebooklogin_secretid = PFASSIssetControl('setup4_membersettings_facebooklogin_secretid','','');


				if ($setup4_membersettings_facebooklogin_appid == '' && $setup4_membersettings_facebooklogin_secretid == '') {
					wp_redirect($homeurllink.$pfmenu_perout.'pferror=fbem');
					exit;
				}

				$facebook = new Facebook\Facebook([
					'app_id'     => $setup4_membersettings_facebooklogin_appid,
					'app_secret' => $setup4_membersettings_facebooklogin_secretid,
					'callbackURL' => ''.$homeurllink.$pfmenu_perout.'uaf=fblogin'
				]);

				if (isset($_GET['code'])) {
					$helper = $facebook->getRedirectLoginHelper();

					try {
					  $accessToken = $helper->getAccessToken(''.$homeurllink.$pfmenu_perout.'uaf=fblogin');
					} catch(Facebook\Exceptions\FacebookResponseException $e) {
					  echo 'Graph returned an error: ' . $e->getMessage();
					  exit;
					} catch(Facebook\Exceptions\FacebookSDKException $e) {
					  echo 'Facebook SDK returned an error: ' . $e->getMessage();
					  exit;
					}

					if (isset($accessToken)) {
						 $response = $facebook->get('/me?fields=id,email,name,last_name,first_name', $accessToken);
						 if (isset($response)) {
						 	$user_profile = $response->getGraphUser();
						 	$user_fb = true;
						 };
					}
					
				}else{
					$user_fb = false;
				}
				
				if($user_fb) {
					
			        if (isset($user_profile['id'])) {
			        	$email = $username = '';
			        	global $wpdb;
			        	
			        	$resultid = $wpdb->get_var( $wpdb->prepare(
							"SELECT user_id FROM $wpdb->usermeta WHERE meta_key = %s and meta_value = %s", 
							'user_socialloginid',
							'fb'.$user_profile['id']
						) );
			        	
			        	if ( empty($resultid) ) {
			        		$resultid = $wpdb->get_var( $wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = %s and meta_value = %s", 'user_socialloginidfb','fb'.$user_profile['id']) );
			        	}
			        	
						if ( !empty($resultid) ) {
						/* If user found in db */
						  $user = get_user_by( 'id', $resultid ); 
						
							if( $user ) {
							  wp_set_current_user( $user->ID, $user->user_login );
							  wp_set_auth_cookie( $user->ID );
							  do_action( 'wp_login', $user->user_login );
							}
							update_user_meta($user->ID, 'user_facebook', 'http://facebook.com/'.$user_profile['id']);

							wp_redirect($special_linkurl);exit;

						}elseif(empty($result_id)) {
						/* If no user into the database */

							if (!isset($user_profile['email'])) {
								$user_profile['email'] = $user_profile['id'].'@facebook.com';
							}
							$email = sanitize_email($user_profile['email']);
							$username = sanitize_user( 'fb'.$user_profile['id'], $strict = true );

							$scontenttext = array('email'=>$email,'username'=>$username,'dbid'=>'fb'.$user_profile['id']);

							if (isset($user_profile['name'])) {
								$scontenttext['name'] = $user_profile['name'];
			                }
			                if (isset($user_profile['first_name'])) {
						      $scontenttext['first_name'] = $user_profile['first_name'];
					      	}
					      	if (isset($user_profile['last_name'])) {
					      		$scontenttext['last_name'] = $user_profile['last_name'];
					      	}

							$scontenttext = base64_encode(json_encode($scontenttext));
							$scontenttype = 1; /*facebook*/

							wp_redirect($homeurllink.$pfmenu_perout.'ctype='.$scontenttype.'&ctext='.$scontenttext);
							exit;
						  
						}else{
							wp_redirect($homeurllink.$pfmenu_perout.'pferror=fbux');
							exit;
						}
			        }
			       
			    } else {
			    	$helper = $facebook->getRedirectLoginHelper();
					$permissions = ['email'];
					$loginUrl = $helper->getLoginUrl(''.$homeurllink.$pfmenu_perout.'uaf=fblogin', $permissions);
					wp_redirect($loginUrl);
					exit;
			    }
				
			}
			/**
			*End : Facebook Login
			**/




			/**
			*Start : Twitter Login
			**/
			if ($ua_action == 'twlogin') {
				require_once( get_template_directory().'/admin/core/Twitter/twitteroauth.php');

				$setup4_membersettings_twitterlogin_appid = PFASSIssetControl('setup4_membersettings_twitterlogin_appid','','');
				$setup4_membersettings_twitterlogin_secretid = PFASSIssetControl('setup4_membersettings_twitterlogin_secretid','','');

				$special_linkurl = $setup4_membersettings_dashboard_link.$pfmenu_perout.'ua=profile';


				if ($setup4_membersettings_twitterlogin_appid == '' && $setup4_membersettings_twitterlogin_secretid == '') {
					wp_redirect($homeurllink.$pfmenu_perout.'pferror=tbem');
					exit;
				}

				$twitter_arr = array(
				  'CONSUMER_KEY'  => $setup4_membersettings_twitterlogin_appid,
				  'CONSUMER_SECRET' => $setup4_membersettings_twitterlogin_secretid,
				  'OAUTH_CALLBACK' => $setup4_membersettings_dashboard_link.$pfmenu_perout.'uaf=twlogin'
				);

				if (!isset($_REQUEST['oauth_token'])) {
				
					$connection = new TwitterOAuth($twitter_arr['CONSUMER_KEY'], $twitter_arr['CONSUMER_SECRET']);
					$request_token = $connection->getRequestToken($twitter_arr['OAUTH_CALLBACK']);
					$_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
					$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

					switch ($connection->http_code) {
					  case 200:
					    $url = $connection->getAuthorizeURL($token,1);
					    wp_redirect($url);exit;
					    break;
					  default:
					    wp_redirect($homeurllink.$pfmenu_perout.'pferror=tbem2');
						exit;
					}

				}elseif (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
				  $_SESSION['oauth_status'] = 'oldtoken';
				  session_destroy();
				  wp_redirect($homeurllink.$pfmenu_perout.'pferror=tbem3');
				  exit;

				}elseif(isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] === $_REQUEST['oauth_token']){
					$connection = new TwitterOAuth($twitter_arr['CONSUMER_KEY'], $twitter_arr['CONSUMER_SECRET'], $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
					$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
					$_SESSION['access_token'] = $access_token;
					
					unset($_SESSION['oauth_token']);
					unset($_SESSION['oauth_token_secret']);
					
					if (200 == $connection->http_code) {
						$_SESSION['status'] = 'verified';

						$user_profile = $connection->get('account/verify_credentials');

						if(!empty($user_profile)){
							global $wpdb;
				        	
				        	$resultid = $wpdb->get_var( $wpdb->prepare(
								"SELECT user_id FROM $wpdb->usermeta WHERE meta_key = %s and meta_value = %s", 
								'user_socialloginid',
								'tw'.$user_profile->id
							) );

							if ( empty($resultid) ) {
				        		$resultid = $wpdb->get_var( $wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = %s and meta_value = %s", 'user_socialloginidtw','tw'.$user_profile->id) );
				        	}
				        }else{
				        	$resultid = '';
				        }

						if ( !empty($resultid) ) {
						
						    $user = get_user_by( 'id', $resultid ); 
						
							if( $user ) {
							  wp_set_current_user( $user->ID, $user->user_login );
							  wp_set_auth_cookie( $user->ID );
							  do_action( 'wp_login', $user->user_login );
							}
							update_user_meta($user_id, 'user_twitter', 'http://twitter.com/'.$user_profile->screen_name);
						
							wp_redirect($special_linkurl);exit;

						}elseif(empty($result_id)) {
							
							$scontenttext = array('email'=>'','username'=>'tw'.$user_profile->id,'dbid'=>'tw'.$user_profile->id,'screen_name' => $user_profile->screen_name);

							$scontenttext = base64_encode(json_encode($scontenttext));
							$scontenttype = 2; /*twitter*/

							wp_redirect($homeurllink.$pfmenu_perout.'ctype='.$scontenttype.'&ctext='.$scontenttext);
							exit;

						}
						
					} else {
						wp_redirect($homeurllink.$pfmenu_perout.'pferror=tbem4');exit;
					}
			       
			    } else {
					wp_redirect($homeurllink);exit;
			    }
				
			}
			/**
			*End : Twitter Login
			**/




			/**
			*Start : Google Login
			**/
			if ($ua_action == 'gologin') {

				$setup4_membersettings_googlelogin_clientid = PFASSIssetControl('setup4_membersettings_googlelogin_clientid','','');
				$setup4_membersettings_googlelogin_secretid = PFASSIssetControl('setup4_membersettings_googlelogin_secretid','','');
				

				if ($setup4_membersettings_googlelogin_clientid == '' && $setup4_membersettings_googlelogin_secretid == '') {
					wp_redirect($homeurllink.$pfmenu_perout.'pferror=gbem');
					exit;
				}


				$google_url = "https://accounts.google.com/o/oauth2/auth";

				$google_params = array(
				    "response_type" => "code",
				    "client_id" => $setup4_membersettings_googlelogin_clientid,
				    "redirect_uri" => $special_linkurl,
				    "scope" => "email profile"/*openid*/
				    );

				$google_request_to = $google_url . '?' . http_build_query($google_params);
				
				wp_redirect($google_request_to);
				exit;
				
			}
			/**
			*End : Google Login
			**/


			/**
			*Start : VK Login
			**/
			if ($ua_action == 'vklogin') {

				$setup4_membersettings_vklogin_clientid = PFASSIssetControl('setup4_membersettings_vklogin_clientid','','');


				if ($setup4_membersettings_vklogin_clientid == '') {
					wp_redirect($homeurllink.$pfmenu_perout.'pferror=vbem');
					exit;
				}


				$vk_url = "https://oauth.vk.com/authorize";

				$vk_params = array(
				    "client_id" => $setup4_membersettings_vklogin_clientid,
				    "redirect_uri" => $special_linkurl.'&ltype=vk',
				    "scope" => 'email',
				    "logintype" => 'vk'
				    );

				$vk_request_to = $vk_url . '?' . http_build_query($vk_params);

				wp_redirect($vk_request_to);
				exit;
			}
			/**
			*End : VK Login
			**/
		}

		/**
		*Start : VK Login End Process
		**/
		if(isset($_GET['code']) && isset($_GET['ltype']) && PFASSIssetControl('setup4_membersettings_vklogin','','0') == 1) {
		    
		    $setup4_membersettings_vklogin_clientid = PFASSIssetControl('setup4_membersettings_vklogin_clientid','','');
			$setup4_membersettings_vklogin_secretid = PFASSIssetControl('setup4_membersettings_vklogin_secretid','','');

		    $vk_code = sanitize_text_field($_GET['code']);
		    $vk_url = 'https://oauth.vk.com/access_token';
		    $gparams = array(
		        "code" => $vk_code,
		        "client_id" => $setup4_membersettings_vklogin_clientid,
		        "client_secret" => $setup4_membersettings_vklogin_secretid,
		        "redirect_uri" => $special_linkurl.'&ltype=vk'
		    );

		    $args = array(
			    'body' => $gparams,
			);
		    $request = wp_remote_post( $vk_url, $args );
		
		    if (isset($request['body'])) {
		    	$get_vk_body = json_decode($request['body'],true);

		    	if (isset($get_vk_body['access_token'])) {
		    	
		    		$access_return_values = wp_remote_get("https://api.vk.com/method/getProfiles?access_token=".$get_vk_body['access_token']);

		    		if(isset($access_return_values['body'])) {
		    			
				        $user_profile = json_decode($access_return_values['body'],true);
				       	$user_profile = isset($user_profile['response'][0])?$user_profile['response'][0]:'';
				       

				        if (is_array($user_profile) && isset($user_profile['uid'])) {
				        	
				        	global $wpdb;
				        	
				        	$resultid = $wpdb->get_var( $wpdb->prepare(
								"SELECT user_id FROM $wpdb->usermeta WHERE meta_key = %s and meta_value = %s", 
								'user_socialloginid',
								'vk'.$user_profile['uid']
							) );

							if ( empty($resultid) ) {
				        		$resultid = $wpdb->get_var( $wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = %s and meta_value = %s", 'user_socialloginidvk','vk'.$user_profile['uid']) );
				        	}
				        	
				        	
							if ( !empty($resultid) ) {
							
							  $user = get_user_by( 'id', $resultid ); 
							
								if( $user ) {
								  wp_set_current_user( $user->ID, $user->user_login );
								  wp_set_auth_cookie( $user->ID );
								  do_action( 'wp_login', $user->user_login );
								}
							
							wp_redirect($special_linkurl);exit;

							} elseif(empty($result_id)) {

								
							    $username = sanitize_user( 'vk'.$user_profile['uid'], $strict = true );

								$scontenttext = array('email'=>'','username'=>$username,'dbid'=>'vk'.$user_profile['uid']);

								if (isset($user_profile['first_name'])) {
									$scontenttext['first_name'] = $user_profile['first_name'];
				                }
				                if (isset($user_profile['last_name'])) {
							      $scontenttext['last_name'] = $user_profile['last_name'];
						      	}


								$scontenttext = base64_encode(json_encode($scontenttext));
								$scontenttype = 4; /*vk*/

								wp_redirect($homeurllink.$pfmenu_perout.'ctype='.$scontenttype.'&ctext='.$scontenttext);
								exit;
								  
							}else{
								wp_redirect($homeurllink.$pfmenu_perout.'pferror=fbux');exit;
							}
				        }else{
				        	wp_redirect($homeurllink.$pfmenu_perout.'pferror=vbue');
				        }
				       
				    }

		    		
		    	}else{
		    		echo esc_html__('Error:','pointfindert2d' );
		    		if (isset($get_gogle_body['error'])) {
		    			echo $get_gogle_body['error'];
		    		}
		    		if (isset($get_gogle_body['error_description'])) {
		    			echo '<br/>'.esc_html__('Description:','pointfindert2d').' '.$get_gogle_body['error_description'];
		    		}
		    		wp_die();
		    	}
		    }

		
		}
		/**
		*End : VK Login End Process
		**/




		/**
		*Start : Google Login End Process
		**/
		if(isset($_GET['code']) && !isset($_GET['ltype']) && PFASSIssetControl('setup4_membersettings_googlelogin','','0') == 1) {
		    
		    $setup4_membersettings_googlelogin_clientid = PFASSIssetControl('setup4_membersettings_googlelogin_clientid','','');
			$setup4_membersettings_googlelogin_secretid = PFASSIssetControl('setup4_membersettings_googlelogin_secretid','','');
			

		    $google_code = sanitize_text_field($_GET['code']);
		    $gurl = 'https://accounts.google.com/o/oauth2/token';
		    $gparams = array(
		        "code" => $google_code,
		        "client_id" => $setup4_membersettings_googlelogin_clientid,
		        "client_secret" => $setup4_membersettings_googlelogin_secretid,
		        "redirect_uri" => $special_linkurl,
		        "grant_type" => "authorization_code"
		    );

		    $args = array(
			    'body' => $gparams,
			);
		    $request = wp_remote_post( $gurl, $args );

		    if (isset($request['body'])) {
		    	$get_gogle_body = json_decode($request['body'],true);

		    	if (isset($get_gogle_body['access_token'])) {
		    	
		    		$access_return_values = wp_remote_get("https://www.googleapis.com/oauth2/v1/userinfo?alt=json&access_token=".$get_gogle_body['access_token']);

		    		if(isset($access_return_values['body'])) {

				        $user_profile = json_decode($access_return_values['body'],true);
				       
				        if (is_array($user_profile) && isset($user_profile['id'])) {
				        	
				        	global $wpdb;
				        	
				        	$resultid = $wpdb->get_var( $wpdb->prepare(
								"SELECT user_id FROM $wpdb->usermeta WHERE meta_key = %s and meta_value = %s", 
								'user_socialloginid',
								'g'.$user_profile['id']
							) );

							if ( empty($resultid) ) {
				        		$resultid = $wpdb->get_var( $wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = %s and meta_value = %s", 'user_socialloginidgl','g'.$user_profile['id']) );
				        	}
				        	
				        	
							if ( !empty($resultid) ) {
							
							  $user = get_user_by( 'id', $resultid ); 
							
								if( $user ) {
								  wp_set_current_user( $user->ID, $user->user_login );
								  wp_set_auth_cookie( $user->ID );
								  do_action( 'wp_login', $user->user_login );
								}
							
							wp_redirect($special_linkurl);exit;

							} elseif(empty($result_id)) {

								$email = sanitize_email($user_profile['email']);
							    $username = sanitize_user( 'g'.$user_profile['id'], $strict = true );

								$scontenttext = array('email'=>$email,'username'=>$username,'dbid'=>'g'.$user_profile['id']);

								$scontenttext = base64_encode(json_encode($scontenttext));
								$scontenttype = 3; /*google*/

								wp_redirect($homeurllink.$pfmenu_perout.'ctype='.$scontenttype.'&ctext='.$scontenttext);
								exit;
								  
								}else{
									wp_redirect($homeurllink.$pfmenu_perout.'pferror=fbux');exit;
								}
				        }else{
				        	wp_redirect($homeurllink.$pfmenu_perout.'pferror=gbue');
				        }
				       
				    }

		    		
		    	}else{
		    		echo esc_html__('Error:','pointfindert2d' );
		    		if (isset($get_gogle_body['error'])) {
		    			echo $get_gogle_body['error'];
		    		}
		    		if (isset($get_gogle_body['error_description'])) {
		    			echo '<br/>'.esc_html__('Description:','pointfindert2d').' '.$get_gogle_body['error_description'];
		    		}
		    		wp_die();
		    	}
		    }

		
		}
		/**
		*End : Google Login End Process
		**/

	
}
add_action('init','PointFinder_Social_Facebook_Login',10 );

?>