<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://bartiff.net
 * @since      0.1.0
 *
 * @package    Woocommerce_Siret_Number_Checks
 * @subpackage Woocommerce_Siret_Number_Checks/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      0.1.0
 * @package    Woocommerce_Siret_Number_Checks
 * @subpackage Woocommerce_Siret_Number_Checks/includes
 * @author     Bartiff <bartiff@gmail.com>
 */
class Woocommerce_Siret_Number_Checks {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      Woocommerce_Siret_Number_Checks_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function __construct() {
		if ( defined( 'WOOCOMMERCE_SIRET_NUMBER_CHECKS_VERSION' ) ) {
			$this->version = WOOCOMMERCE_SIRET_NUMBER_CHECKS_VERSION;
		} else {
			$this->version = '0.1.0';
		}
		$this->plugin_name = 'woocommerce-siret-number-checks';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		if (get_option( 'wsnc-request-siret-number' ) === 'on') {
			$this->define_public_hooks();
		}

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Woocommerce_Siret_Number_Checks_Loader. Orchestrates the hooks of the plugin.
	 * - Woocommerce_Siret_Number_Checks_i18n. Defines internationalization functionality.
	 * - Woocommerce_Siret_Number_Checks_Admin. Defines all hooks for the admin area.
	 * - Woocommerce_Siret_Number_Checks_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woocommerce-siret-number-checks-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woocommerce-siret-number-checks-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-woocommerce-siret-number-checks-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-woocommerce-siret-number-checks-public.php';

		$this->loader = new Woocommerce_Siret_Number_Checks_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Woocommerce_Siret_Number_Checks_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Woocommerce_Siret_Number_Checks_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Woocommerce_Siret_Number_Checks_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'wsnc_add_admin_menu', 99 );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'wsnc_register_setting' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'wsnc_verify_woocommerce_activation' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'wsnc_notice_to_configure_settings' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'wsnc_alert_keys_not_valid' );
		$this->loader->add_action( 'show_user_profile', $plugin_admin, 'wsnc_print_user_admin_fields' );
		$this->loader->add_action( 'edit_user_profile', $plugin_admin, 'wsnc_print_user_admin_fields' );
		$this->loader->add_action( 'personal_options_update', $plugin_admin, 'wsnc_save_account_fields' );
		$this->loader->add_action( 'edit_user_profile_update', $plugin_admin, 'wsnc_save_account_fields' );
		$this->loader->add_action( 'wsnc_account_fields', $plugin_admin, 'wsnc_add_post_data_to_account_fields' );
		$this->loader->add_action( 'wp_ajax_wsnc_fetch_siret', $plugin_admin, 'wsnc_fetch_siret' );
		$this->loader->add_action( 'wp_ajax_nopriv_wsnc_fetch_siret', $plugin_admin, 'wsnc_fetch_siret' );
		$this->loader->add_filter( 'plugin_action_links_'. $this->plugin_name .'/'. $this->plugin_name .'.php', $plugin_admin, 'wsnc_settings_link' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Woocommerce_Siret_Number_Checks_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_filter( 'woocommerce_form_field', $plugin_public, 'wsnc_remove_optional_fields_label_in_front', 10, 4 );
		$this->loader->add_action( 'woocommerce_register_form_start', $plugin_public, 'wsnc_print_notice_siret_info' );
		$this->loader->add_action( 'woocommerce_register_form', $plugin_public, 'wsnc_print_user_frontend_fields' );
		$this->loader->add_action( 'woocommerce_edit_account_form', $plugin_public, 'wsnc_print_user_frontend_fields' );
		$this->loader->add_filter( 'woocommerce_checkout_fields', $plugin_public, 'wsnc_checkout_fields' );
		$this->loader->add_filter( 'woocommerce_created_customer', $plugin_public, 'wsnc_save_account_fields' );
		$this->loader->add_filter( 'woocommerce_save_account_details', $plugin_public, 'wsnc_save_account_fields' );
		$this->loader->add_filter( 'woocommerce_registration_errors', $plugin_public, 'wsnc_validate_user_frontend_fields' );
		$this->loader->add_filter( 'woocommerce_save_account_details_errors', $plugin_public, 'wsnc_validate_user_frontend_fields' );
		$this->loader->add_action( 'wsnc_account_fields', $plugin_public, 'wsnc_add_post_data_to_account_fields' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    0.1.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     0.1.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     0.1.0
	 * @return    Woocommerce_Siret_Number_Checks_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     0.1.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
