<?php
/**
 *
 * @package PointFinder_APIM
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'PointFinder_APIM' ) ) :

	/**
	 * It's the main class that does all the things.
	 *
	 * @class PointFinder_APIM
	 * @version	1.0.0
	 * @since 1.0.0
	 */
	final class PointFinder_APIM {

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
		 * Plugin data.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @var object
		 */
		private $data;


		/**
		 *
		 * @access private
		 * @since 1.0.0
		 */
		private $envato_api = null;

		/**
		 * The slug.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @var string
		 */
		private $slug;

		/**
		 * The version number.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @var string
		 */
		private $version;

		/**
		 * The web URL to the plugin directory.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @var string
		 */
		private $plugin_url;

		/**
		 * The server path to the plugin directory.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @var string
		 */
		private $plugin_path;

		/**
		 * The web URL to the plugin admin page.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @var string
		 */
		private $page_url;

		/**
		 * The setting option name.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @var string
		 */
		private $option_name;

		/**
		 * The product-name converted to ID.
		 *
		 * @access private
		 * @since 1.0.0
		 * @var string
		 */
		private $product_id = '';

		/**
		 * The Envato token.
		 *
		 * @access private
		 * @since 1.0.0
		 * @var string
		 */
		private $token;

		/**
		 * Whether the token is valid and for the specified product or not.
		 *
		 * @access private
		 * @since 1.0.0
		 * @var array
		 */
		private $registered = array();

		/**
		 * The arguments that are used in the constructor.
		 *
		 * @access private
		 * @since 1.0.0
		 * @var array
		 */
		private $args = array();

		/**
		 * Main PointFinder_APIM Instance
		 *
		 * Ensures only one instance of this class exists in memory at any one time.
		 *
		 * @see PointFinder_APIM()
		 * @uses PointFinder_APIM::init_globals() Setup class globals.
		 * @uses PointFinder_APIM::init_includes() Include required files.
		 * @uses PointFinder_APIM::init_actions() Setup hooks and actions.
		 *
		 * @since 1.0.0
		 * @static
		 * @return object The one true PointFinder_APIM.
		 * @codeCoverageIgnore
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
				self::$_instance->init_globals();
				self::$_instance->init_includes();
				self::$_instance->init_actions();
			}
			return self::$_instance;
		}

		/**
		 * A dummy constructor to prevent this class from being loaded more than once.
		 *
		 * @see PointFinder_APIM::instance()
		 *
		 * @since 1.0.0
		 * @access private
		 * @codeCoverageIgnore
		 */
		private function __construct() {
			/* We do nothing here! */
			$this->product_id = 'Pointfinder';

			// Register the settings.
			add_action( 'admin_init', array( $this, 'register_settings' ) );
		}

		/**
		 * You cannot clone this class.
		 *
		 * @since 1.0.0
		 * @codeCoverageIgnore
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'pointfindert2d' ), '1.0.0' );
		}

		/**
		 * You cannot unserialize instances of this class.
		 *
		 * @since 1.0.0
		 * @codeCoverageIgnore
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'pointfindert2d' ), '1.0.0' );
		}

		/**
		 * Setup the class globals.
		 *
		 * @since 1.0.0
		 * @access private
		 * @codeCoverageIgnore
		 */
		private function init_globals() {
			$this->data        = new stdClass();
			$this->version     = '1.0.0-RC2';
			$this->slug        = 'pointfinder_registration';
			$this->option_name = self::sanitize_key( $this->slug );
			$this->plugin_url  = get_template_directory_uri();
			$this->plugin_path = get_template_directory();
			$this->page_url    = admin_url( 'admin.php?page=' . $this->slug );
			$this->data->admin = true;

			// Set the current version for the Github updater to use.
			if ( version_compare( get_option( 'pointfinder_apim_version' ), $this->version, '<' ) ) {
				update_option( 'pointfinder_apim_version', $this->version );
			}

			$this->registered = get_option( 'pointfinder_registered' );
		}

		/**
		 * Include required files.
		 *
		 * @since 1.0.0
		 * @access private
		 * @codeCoverageIgnore
		 */
		private function init_includes() {
			require $this->plugin_path . '/admin/core/pfapi/pointfinder-api.php';
			require $this->plugin_path . '/admin/core/pfapi/pointfinder-item.php';
			require $this->plugin_path . '/admin/core/pfapi/pointfinder-admin.php';
		}

		/**
		 * Setup the hooks, actions and filters.
		 *
		 * @uses add_action() To add actions.
		 * @uses add_filter() To add filters.
		 *
		 * @since 1.0.0
		 * @access private
		 * @codeCoverageIgnore
		 */
		private function init_actions() {
			// Activate plugin.
			register_activation_hook( __FILE__, array( $this, 'activate' ) );

			// Deactivate plugin.
			register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

			// Load the textdomain.
			//add_action( 'init', array( $this, 'load_textdomain' ) );

			// Load OAuth.
			add_action( 'init', array( $this, 'admin' ) );

			// Load Upgrader.
			add_action( 'init', array( $this, 'items' ) );
		}

		/**
		 * Activate plugin.
		 *
		 * @since 1.0.0
		 * @codeCoverageIgnore
		 */
		public function activate() {
			self::set_plugin_state( true );
		}

		/**
		 * Deactivate plugin.
		 *
		 * @since 1.0.0
		 * @codeCoverageIgnore
		 */
		public function deactivate() {
			self::set_plugin_state( false );
		}

		/**
		 * Loads the plugin's translated strings.
		 *
		 * @since 1.0.0
		 * @codeCoverageIgnore
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'pointfindert2d', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Sanitize data key.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @param string $key An alpha numeric string to sanitize.
		 * @return string
		 */
		private function sanitize_key( $key ) {
			return preg_replace( '/[^A-Za-z0-9\_]/i', '', str_replace( array( '-', ':' ), '_', $key ) );
		}

		/**
		 * Recursively converts data arrays to objects.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @param array $array An array of data.
		 * @return object
		 */
		private function convert_data( $array ) {
			foreach ( (array) $array as $key => $value ) {
				if ( is_array( $value ) ) {
					$array[ $key ] = self::convert_data( $value );
				}
			}
			return (object) $array;
		}

		/**
		 * Set the `is_plugin_active` option.
		 *
		 * This setting helps determine context. Since the plugin can be included in your theme root you
		 * might want to hide the admin UI when the plugin is not activated and implement your own.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @param bool $value Whether or not the plugin is active.
		 */
		private function set_plugin_state( $value ) {
			self::set_option( 'is_plugin_active', $value );
		}

		/**
		 * Set option value.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @param string $name Option name.
		 * @param mixed  $option Option data.
		 */
		private function set_option( $name, $option ) {
			$options = self::get_options();
			$name = self::sanitize_key( $name );
			$options[ $name ] = esc_html( $option );
			update_option( $this->option_name, $options );
		}

		/**
		 * Return the option settings array.
		 *
		 * @since 1.0.0
		 */
		public function get_options() {
			return get_option( $this->option_name, array() );
		}

		/**
		 * Return a value from the option settings array.
		 *
		 * @since 1.0.0
		 *
		 * @param string $name Option name.
		 * @param mixed  $default The default value if nothing is set.
		 * @return mixed
		 */
		public function get_option( $name, $default = '' ) {
			$options = self::get_options();
			$name = self::sanitize_key( $name );
			return isset( $options[ $name ] ) ? $options[ $name ] : $default;
		}

		/**
		 * Set data.
		 *
		 * @since 1.0.0
		 *
		 * @param string $key Unique object key.
		 * @param mixed  $data Any kind of data.
		 */
		public function set_data( $key, $data ) {
			if ( ! empty( $key ) ) {
				if ( is_array( $data ) ) {
					$data = self::convert_data( $data );
				}
				$key = self::sanitize_key( $key );
				$this->data->$key = $data;
			}
		}

		/**
		 * Get data.
		 *
		 * @since 1.0.0
		 *
		 * @param string $key Unique object key.
		 * @return string|object
		 */
		public function get_data( $key ) {
			return isset( $this->data->$key ) ? $this->data->$key : '';
		}

		/**
		 * Return the plugin slug.
		 *
		 * @since 1.0.0
		 *
		 * @return string
		 */
		public function get_slug() {
			return $this->slug;
		}

		/**
		 * Return the plugin version number.
		 *
		 * @since 1.0.0
		 *
		 * @return string
		 */
		public function get_version() {
			return $this->version;
		}

		/**
		 * Return the plugin URL.
		 *
		 * @since 1.0.0
		 *
		 * @return string
		 */
		public function get_plugin_url() {
			return $this->plugin_url;
		}

		/**
		 * Return the plugin path.
		 *
		 * @since 1.0.0
		 *
		 * @return string
		 */
		public function get_plugin_path() {
			return $this->plugin_path;
		}

		/**
		 * Return the plugin page URL.
		 *
		 * @since 1.0.0
		 *
		 * @return string
		 */
		public function get_page_url() {
			return $this->page_url;
		}

		/**
		 * Return the option settings name.
		 *
		 * @since 1.0.0
		 *
		 * @return string
		 */
		public function get_option_name() {
			return $this->option_name;
		}

		/**
		 * Envato API class.
		 *
		 * @since 1.0.0
		 *
		 * @return PointFinder_APIM_API
		 */
		public function api() {
			return PointFinder_APIM_API::instance();
		}


		/**
		 * Admin UI class.
		 *
		 * @since 1.0.0
		 *
		 * @return PointFinder_APIM_Admin
		 */
		public function admin() {
			return PointFinder_APIM_Admin::instance();
		}

		/**
		 * Items class.
		 *
		 * @since 1.0.0
		 *
		 * @return PointFinder_APIM_Items
		 */
		public function items() {
			return PointFinder_APIM_Items::instance();
		}


		/**
		 * Registers the setting field(s) for the registration form.
		 *
		 * @access public
		 * @since 1.0.0
		 * @return void
		 */
		public function register_settings() {

			// Setting.
			register_setting(pointfinder_apim()->get_slug(), pointfinder_apim()->get_option_name(),array( $this, 'check_registration' )
			);

		}

		/**
		 * Checks if the product is part of the themes or plugins
		 * purchased by the user belonging to the token.
		 *
		 * @access public
		 * @since 1.0.0
		 * @param string $new_value The new token to check.
		 */
		public function check_registration( $new_value ) {

			$this->init_globals();

			// Get the old value.
			$old_value   = false;
			
			$old_setting = get_option( $this->get_option_name(), false );
			if ( is_array( $old_setting ) && isset( $old_setting ) && isset( $old_setting['token'] ) ) {
				$old_value = $old_setting['token'];
			}
			if ( false === $old_value || empty( $old_value ) ) {
				$old_value = array();
				$old_value = array(
					'token' => '',
				);
			}


			// Check that the new value is properly formatted.
			if ( is_array( $new_value ) && isset( $new_value['token'] ) ) {
				// If token field is empty, copy is not registered.
				$this->registered = false;
				if ( ! empty( $new_value['token'] ) && 32 === strlen( $new_value['token'] ) ) {
					// Remove spaces from the beginning and end of the token.
					$new_value['token'] = trim( $new_value['token'] );
					// Check if new token is valid.
					$this->registered = $this->product_exists( $new_value['token'] );

				}
			} else {
				$new_value = array(
					'token' => '',
				);
			}

			update_option( 'pointfinder_registered', $this->registered );

			// Return the new value.
			return $new_value;

		}

		/**
		 * Checks if the product is part of the themes or plugins
		 * purchased by the user belonging to the token.
		 *
		 * @access private
		 * @since 1.0.0
		 * @param string $token A token to check.
		 * @param int    $page  The page number if one is necessary.
		 * @return bool
		 */
		private function product_exists( $token = '', $page = '' ) {

			// Set the new token for the API call.
			if ( '' !== $token ) {
				$this->envato_api()->set_token( $token );
			}
		
			$products = $this->envato_api()->themes( array(), $page );
			

			// If a WP Error object is returned we need to check if API is down.
			if ( is_wp_error( $products ) ) {
				// 401 ( unauthorized ) and 403 ( forbidden ) mean the token is invalid, apart from that Envato API is down.
				if ( 401 !== $products->get_error_code() && 403 !== $products->get_error_code() && '' !== $products->get_error_message() ) {
					set_site_transient( 'pointfinder_envato_api_down', true, 600 );
				}
				return false;
			}

			// Check iv product is part of the purchased themes/plugins.
			foreach ( $products as $product ) {
				if ( isset( $product['name'] ) ) {
					if ( $this->product_id === $product['name'] ) {
						return true;
					}
				}
			}

			if ( 100 === count( $products ) ) {
				$page = ( ! $page ) ? 2 : $page + 1;
				return $this->product_exists( '', $page );
			}
			return false;
		}




		/**
		 * Envato API class.
		 *
		 * @access public
		 * @since 1.0.0
		 */
		public function envato_api() {

			if ( null === $this->envato_api ) {
				$this->envato_api = new PointFinder_APIM_API( $this );
			}
			return $this->envato_api;

		}

		/**
		 * Has user associated with current token purchased this product?
		 *
		 * @access public
		 * @since 1.0.0
		 * @return bool
		 */
		public function is_registered() {

			// Is the product registered?
			if ( isset( $this->registered ) && (true === $this->registered || 1 ==  $this->registered)) {
				return true;
			}
			// Is the Envato API down?
			if ( get_site_transient( 'pointfinder_envato_api_down' ) ) {
				return true;
			}
			// Fallback to false.
			return false;

		}
	}

endif;

if ( ! function_exists( 'pointfinder_apim' ) ) :
	/**
	 * The main function responsible for returning the one true
	 * PointFinder_APIM Instance to functions everywhere.
	 *
	 * Use this function like you would a global variable, except
	 * without needing to declare the global.
	 *
	 * Example: <?php $pointfinder_apim = pointfinder_apim(); ?>
	 *
	 * @since 1.0.0
	 * @return PointFinder_APIM The one true PointFinder_APIM Instance
	 */
	function pointfinder_apim() {
		return PointFinder_APIM::instance();
	}
endif;

/**
 * Loads the main instance of PointFinder_APIM to prevent
 * the need to use globals.
 *
 * @since 1.0.0
 * @return object PointFinder_APIM
 */
add_action( 'after_setup_theme', 'pointfinder_apim', 11 );
