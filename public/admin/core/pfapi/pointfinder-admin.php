<?php
/**
 * Admin UI class.
 *
 * @package PointFinder_APIM
 */

if ( ! class_exists( 'PointFinder_APIM_Admin' ) && class_exists( 'PointFinder_APIM' ) ) :

	/**
	 * Creates an admin page to save the Envato API OAuth token.
	 *
	 * @class PointFinder_APIM_Admin
	 * @version 1.0.0
	 * @since 1.0.0
	 */
	class PointFinder_APIM_Admin {

		/**
		 * Action nonce.
		 *
		 * @type string
		 */
		const AJAX_ACTION = 'pointfinder_apim';

		/**
		 * The single class instance.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @var object
		 */
		private static $_instance = null;

		/**
		 * Main PointFinder_APIM_Admin Instance
		 *
		 * Ensures only one instance of this class exists in memory at any one time.
		 *
		 * @see PointFinder_APIM_Admin()
		 * @uses PointFinder_APIM_Admin::init_actions() Setup hooks and actions.
		 *
		 * @since 1.0.0
		 * @static
		 * @return object The one true PointFinder_APIM_Admin.
		 * @codeCoverageIgnore
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
				self::$_instance->init_actions();
			}
			return self::$_instance;
		}

		/**
		 * A dummy constructor to prevent this class from being loaded more than once.
		 *
		 * @see PointFinder_APIM_Admin::instance()
		 *
		 * @since 1.0.0
		 * @access private
		 * @codeCoverageIgnore
		 */
		private function __construct() {
			/* We do nothing here! */
		}

		/**
		 * You cannot clone this class.
		 *
		 * @since 1.0.0
		 * @codeCoverageIgnore
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'pointfindert2d' ), '1.0.0' );
		}

		/**
		 * You cannot unserialize instances of this class.
		 *
		 * @since 1.0.0
		 * @codeCoverageIgnore
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'pointfindert2d' ), '1.0.0' );
		}

		/**
		 * Setup the hooks, actions and filters.
		 *
		 * @uses add_action() To add actions.
		 * @uses add_filter() To add filters.
		 *
		 * @since 1.0.0
		 */
		public function init_actions() {
			// @codeCoverageIgnoreStart
			if ( false === pointfinder_apim()->get_data( 'admin' ) ) {
				return;
			}

			// Maybe delete the site transients.
			add_action( 'init', array( $this, 'maybe_delete_transients' ), 11 );

			// Register the settings.
			add_action( 'admin_init', array( $this, 'register_settings' ) );

			// We may need to redirect after an item is enabled.
			add_action( 'current_screen', array( $this, 'maybe_redirect' ) );

			// Add authorization notices.
			add_action( 'current_screen', array( $this, 'add_notices' ) );

			// Set the API values.
			add_action( 'current_screen', array( $this, 'set_items' ) );
		}




		

		/**
		 * Returns the bearer arguments for a request with a single use API Token.
		 *
		 * @since 1.0.0
		 *
		 * @param int $id The item ID.
		 * @return array
		 */
		public function set_bearer_args( $id ) {
			$token = '';
			$args = array();
			foreach ( pointfinder_apim()->get_option( 'items', array() ) as $item ) {
				if ( $item['id'] === $id ) {
					$token = $item['token'];
					break;
				}
			}
			if ( ! empty( $token ) ) {
				$args = array(
					'headers' => array(
						'Authorization' => 'Bearer ' . $token,
					),
				);
			}
			return $args;
		}

		/**
		 * Maybe delete the site transients.
		 *
		 * @since 1.0.0
		 * @codeCoverageIgnore
		 */
		public function maybe_delete_transients() {
			if ( isset( $_POST[ pointfinder_apim()->get_option_name() ] ) ) {

				// Nonce check.
				if ( isset( $_POST['_wpnonce'] ) && ! wp_verify_nonce( $_POST['_wpnonce'], pointfinder_apim()->get_slug() . '-options' ) ) {
		 			wp_die( __( 'You do not have sufficient permissions to delete transients.', 'pointfindert2d' ) );
				}

				self::delete_transients();
			}
		}

		/**
		 * Delete the site transients.
		 *
		 * @since 1.0.0
		 * @access private
		 */
		private function delete_transients() {
			delete_site_transient( pointfinder_apim()->get_option_name() . '_themes' );
			delete_site_transient( pointfinder_apim()->get_option_name() . '_plugins' );
		}

		/**
		 * Prints out all settings sections added to a particular settings page in columns.
		 *
		 * @global array $wp_settings_sections Storage array of all settings sections added to admin pages
		 * @global array $wp_settings_fields Storage array of settings fields and info about their pages/sections
		 * @since 1.0.0
		 *
		 * @param string $page The slug name of the page whos settings sections you want to output.
		 * @param int    $columns The number of columns in each row.
		 */
		public static function do_settings_sections( $page, $columns = 2 ) {
			global $wp_settings_sections, $wp_settings_fields;

			// @codeCoverageIgnoreStart
			if ( ! isset( $wp_settings_sections[ $page ] ) ) {
				return;
			}
			// @codeCoverageIgnoreEnd
			$index = 0;

			foreach ( (array) $wp_settings_sections[ $page ] as $section ) {
				// @codeCoverageIgnoreStart
				if ( ! isset( $wp_settings_fields ) || ! isset( $wp_settings_fields[ $page ] ) || ! isset( $wp_settings_fields[ $page ][ $section['id'] ] ) ) {
					continue;
				}
				// @codeCoverageIgnoreEnd
				$index++;

				// Set the column class.
				$class = 'col col-' . $index;
				if ( $columns === $index ) {
					$class .= ' last-feature';
					$index = 0;
				}
				?>
				<div class="<?php echo esc_attr( $class ); ?>">
					<?php
					if ( ! empty( $section['title'] ) ) {
						echo '<h3>' . esc_html( $section['title'] ) . '</h3>' . "\n";
					}
					if ( ! empty( $section['callback'] ) ) {
						call_user_func( $section['callback'], $section );
					}
					?>
					<table class="form-table">
						<?php do_settings_fields( $page, $section['id'] ); ?>
					</table>
				</div>
				<?php
			}
		}




		/**
		 * Registers the settings.
		 *
		 * @since 1.0.0
		 */
		public function register_settings() {
			// Setting.
			register_setting( pointfinder_apim()->get_slug(), pointfinder_apim()->get_option_name() );

			// OAuth section.
			add_settings_section(
				pointfinder_apim()->get_option_name() . '_oauth_section','',
				array( $this, 'render_oauth_section_callback' ),
				pointfinder_apim()->get_slug()
			);

			// Token setting.
			add_settings_field(
				'token',
				__( 'Token', 'pointfindert2d' ),
				array( $this, 'render_token_setting_callback' ),
				pointfinder_apim()->get_slug(),
				pointfinder_apim()->get_option_name() . '_oauth_section'
			);


			// Items setting.
			add_settings_field(
				'items',
				__( 'Pointfinder Items', 'pointfindert2d' ),
				array( $this, 'render_items_setting_callback' ),
				pointfinder_apim()->get_slug(),
				pointfinder_apim()->get_option_name() . '_items_section'
			);
		}

		/**
		 * Redirect after the enable action runs.
		 *
		 * @since 1.0.0
		 * @codeCoverageIgnore
		 */
		public function maybe_redirect() {
			if ( 'pf-settings_page_' . pointfinder_apim()->get_slug() === get_current_screen()->id ) {

				if ( ! empty( $_GET['action'] ) && 'install-theme' === $_GET['action'] && ! empty( $_GET['enabled'] ) ) {
					wp_safe_redirect( esc_url( pointfinder_apim()->get_page_url() ) );
					exit;
				}
			}
		}

		/**
		 * Add authorization notices.
		 *
		 * @since 1.0.0
		 */
		public function add_notices() {
			if ( 'pf-settings_page_' . pointfinder_apim()->get_slug() === get_current_screen()->id ) {

				// @codeCoverageIgnoreStart
				if ( isset( $_GET['authorization'] ) && 'check' === $_GET['authorization'] ) {
					self::authorization_redirect();
				}
				// @codeCoverageIgnoreEnd
				// Get the option array.
				$option = pointfinder_apim()->get_options();

				// Display success/error notices.
				
				if ( ! empty( $option['notices'] ) ) {
					self::delete_transients();

					// Show succes notice.
					if ( in_array( 'success', $option['notices'] ) ) {
						add_action( 'admin_notices', array( $this, 'render_success_notice' ) );
					}

					// Show succes no-items notice.
					if ( in_array( 'success-no-items', $option['notices'] ) ) {
						add_action( 'admin_notices', array( $this, 'render_success_no_items_notice' ) );
					}

					// Show single-use succes notice.
					if ( in_array( 'success-single-use', $option['notices'] ) ) {
						add_action( 'admin_notices', array( $this, 'render_success_single_use_notice' ) );
					}

					// Show error notice.
					if ( in_array( 'error', $option['notices'] ) ) {
						add_action( 'admin_notices', array( $this, 'render_error_notice' ) );
					}

					// Show single-use error notice.
					if ( in_array( 'error-single-use',$option['notices'] ) ) {
						add_action( 'admin_notices', array( $this, 'render_error_single_use_notice' ) );
					}

					// Update the saved data so the notice disappears on the next page load.
					unset( $option['notices'] );
					update_option( pointfinder_apim()->get_option_name(), $option );
				}
			}
		}

		/**
		 * Set the API values.
		 *
		 * @since 1.0.0
		 */
		public function set_items() {
			if ( 'pf-settings_page_' . pointfinder_apim()->get_slug() === get_current_screen()->id ) {
				pointfinder_apim()->items()->set_themes();
				pointfinder_apim()->items()->set_plugins();
			}
		}

		/**
		 * Check for authorization and redirect.
		 *
		 * @since 1.0.0
		 * @access private
		 * @codeCoverageIgnore
		 */
		private function authorization_redirect() {
			self::authorization();
			wp_safe_redirect( esc_url( pointfinder_apim()->get_page_url()) );
			exit;
		}

		/**
		 * Set the Envato API authorization value.
		 *
		 * @since 1.0.0
		 */
		public function authorization() {
			// Get the option array.
			$option = pointfinder_apim()->get_options();

			// Check for global token.
			if ( pointfinder_apim()->get_option( 'token' ) || pointfinder_apim()->api()->token ) {
				$failed = false;
				$option['notices'] = array();

				if ( 'error' === $this->authorize_total_items() ) {
					$failed = true;
				}


				if ( false === $failed ) {
					$themes_notice = $this->authorize_themes();
					if ( 'success-no-themes' === $themes_notice ) {
						$themes_empty = true;
					} elseif ( 'error' === $themes_notice ) {
						$failed = true;
					}
				}

				if ( false === $failed ) {
					$plugins_notice = $this->authorize_plugins();
					if ( 'success-no-plugins' === $plugins_notice ) {
						$plugins_empty = true;
					} elseif ( 'error' === $plugins_notice ) {
						$failed = true;
					}
				}

				if ( true === $failed ) {
					$option['notices'][] = 'error';
				} else {
					if ( false === $failed && isset( $themes_empty ) && isset( $plugins_empty ) ) {
						$option['notices'][] = 'success-no-items';
					} else {
						$option['notices'][] = 'success';
					}
				}
			}

			// Check for single-use token.
			if ( ! empty( $option['items'] ) ) {
				$failed = false;

				foreach ( $option['items'] as $key => $item ) {
					if ( empty( $item['name'] ) || empty( $item['token'] ) || empty( $item['id'] ) || empty( $item['type'] ) || empty( $item['authorized'] ) ) {
						continue;
					}

					$request_args = array(
						'headers' => array(
							'Authorization' => 'Bearer ' . $item['token'],
						),
					);

					// Uncached API response with single-use token.
					$response = pointfinder_apim()->api()->item( $item['id'], $request_args );

					if ( ! is_wp_error( $response ) && isset( $response['id'] ) ) {
						$option['items'][ $key ]['authorized'] = 'success';
					} else {
						$failed = true;
						$option['items'][ $key ]['authorized'] = 'failed';
					}
				}

				if ( true === $failed ) {
					$option['notices'][] = 'error-single-use';
				} else {
					$option['notices'][] = 'success-single-use';
				}
			}

			// Set the option array.
			if ( isset( $option['notices'] ) ) {
				update_option( pointfinder_apim()->get_option_name(), $option );
			}
		}

		/**
		 * Check that themes are authorized.
		 *
		 * @since 1.0.0
		 *
		 * @return bool
		 */
		public function authorize_total_items() {
			$response = pointfinder_apim()->api()->request( 'https://api.envato.com/v1/market/total-items.json' );
			$notice = 'success';

			if ( is_wp_error( $response ) || ! isset( $response['total-items'] ) ) {
				$notice = 'error';
			}

			return $notice;
		}


		


		/**
		 * Check that themes or plugins are authorized and downloadable.
		 *
		 * @since 1.0.0
		 *
		 * @param string $type The filter type, either 'themes' or 'plugins'. Default 'themes'.
		 * @return bool|null
		 */
		public function authorize_items( $type = 'themes' ) {
			$api_url = 'https://api.envato.com/v2/market/buyer/list-purchases?filter_by=wordpress-' . $type;
			$response = pointfinder_apim()->api()->request( $api_url );
			$notice = 'success';

			if ( is_wp_error( $response ) || empty( $response ) ) {
				$notice = 'error';
			} elseif ( empty( $response['results'] ) ) {
				$notice = 'success-no-' . $type;
			} else {
				shuffle( $response['results'] );
				$item = array_shift( $response['results'] );
				if ( ! isset( $item['item']['id'] ) || ! pointfinder_apim()->api()->download( $item['item']['id'] ) ) {
					$notice = 'error';
				}
			}

			return $notice;
		}

		/**
		 * Check that themes are authorized.
		 *
		 * @since 1.0.0
		 *
		 * @return bool
		 */
		public function authorize_themes() {
			return $this->authorize_items( 'themes' );
		}

		/**
		 * Check that plugins are authorized.
		 *
		 * @since 1.0.0
		 *
		 * @return bool
		 */
		public function authorize_plugins() {
			return $this->authorize_items( 'plugins' );
		}

		
		/**
		 * Admin page callback.
		 *
		 * @since 1.0.0
		 */
		public function render_admin_callback() {
			require( pointfinder_apim()->get_plugin_path() . '/admin/core/pfapi/view/callback/admin.php' );
		}

		/**
		 * OAuth section callback.
		 *
		 * @since 1.0.0
		 */
		public function render_oauth_section_callback() {}


		/**
		 * Token setting callback.
		 *
		 * @since 1.0.0
		 */
		public function render_token_setting_callback() {
			require( pointfinder_apim()->get_plugin_path() . '/admin/core/pfapi/view/token.php' );
		}

		/**
		 * Items setting callback.
		 *
		 * @since 1.0.0
		 */
		public function render_items_setting_callback() {}

		/**
		 * Intro
		 *
		 * @since 1.0.0
		 */
		public function render_intro_partial() {}

		/**
		 * Tabs
		 *
		 * @since 1.0.0
		 */
		public function render_tabs_partial() {}

		/**
		 * Settings panel
		 *
		 * @since 1.0.0
		 */
		public function render_settings_panel_partial() {}

		/**
		 * Themes panel
		 *
		 * @since 1.0.0
		 */
		public function render_themes_panel_partial() {}

		/**
		 * Plugins panel
		 *
		 * @since 1.0.0
		 */
		public function render_plugins_panel_partial() {}

		/**
		 * Success notice.
		 *
		 * @since 1.0.0
		 */
		public function render_success_notice() {
			require( pointfinder_apim()->get_plugin_path() . '/admin/core/pfapi/view/notice/success.php' );
		}

		/**
		 * Success no-items notice.
		 *
		 * @since 1.0.0
		 */
		public function render_success_no_items_notice() {
			require( pointfinder_apim()->get_plugin_path() . '/admin/core/pfapi/view/notice/success-no-items.php' );
		}

		/**
		 * Success single-use notice.
		 *
		 * @since 1.0.0
		 */
		public function render_success_single_use_notice() {
			require( pointfinder_apim()->get_plugin_path() . '/admin/core/pfapi/view/notice/success-single-use.php' );
		}

		/**
		 * Error notice.
		 *
		 * @since 1.0.0
		 */
		public function render_error_notice() {
			require( pointfinder_apim()->get_plugin_path() . '/admin/core/pfapi/view/notice/error.php' );
		}

		/**
		 * Error single-use notice.
		 *
		 * @since 1.0.0
		 */
		public function render_error_single_use_notice() {
			require( pointfinder_apim()->get_plugin_path() . '/admin/core/pfapi/view/notice/error-single-use.php' );
		}
	}

endif;
