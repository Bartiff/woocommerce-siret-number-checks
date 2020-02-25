<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://bartiff.net
 * @since      0.1.0
 *
 * @package    Woocommerce_Siret_Number_Checks
 * @subpackage Woocommerce_Siret_Number_Checks/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Woocommerce_Siret_Number_Checks
 * @subpackage Woocommerce_Siret_Number_Checks/public
 * @author     Bartiff <bartiff@gmail.com>
 */
class Woocommerce_Siret_Number_Checks_Public extends Woocommerce_Siret_Number_Checks_Fields {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woocommerce_Siret_Number_Checks_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocommerce_Siret_Number_Checks_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woocommerce-siret-number-checks-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Remove 'optional' label field in frontend
	 * 
	 * @since	 0.1.0
	 */
	public function wsnc_remove_optional_fields_label_in_front($field, $key, $args, $value) {
		if((is_checkout() && ! is_wc_endpoint_url()) || (is_account_page() && ! is_wc_endpoint_url())) {
			$optional = '&nbsp;<span class="optional">(' . esc_html__( 'optional', 'woocommerce' ) . ')</span>';
			$field = str_replace( $optional, '', $field );
		}

		return $field;
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woocommerce_Siret_Number_Checks_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocommerce_Siret_Number_Checks_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woocommerce-siret-number-checks-public.js', array( 'jquery' ), $this->version, false );
		$this->getWsncOptions($this->plugin_name);

	}

	/**
	 * Show WSNC fields to the registration form
	 * 
	 * @since	0.1.0
	 */
	public function wsnc_print_user_frontend_fields() {
		$fields = $this->wsnc_get_account_fields();
		$is_user_logged_in = is_user_logged_in();
		foreach ($fields as $key => $field_args) {
			$value = null;
			if ($is_user_logged_in && !empty($field_args['hide_in_account'])) { continue; }
			if (!$is_user_logged_in && !empty($field_args['hide_in_registration'])) { continue; }
			if ($is_user_logged_in) {
				$user_id = $this->wsnc_get_edit_user_id();
				$value = $this->wsnc_get_userdata($user_id, $key);
			}
			$value = isset($field_args['value']) ? $field_args['value'] : $value;
			woocommerce_form_field($key, $field_args, $value);
		}

	}

	/**
	 * Show WSNC WooCommerce registration fields to the checkout
	 * 
	 * @since	0.1.0
	 */
	public function wsnc_checkout_fields($checkout_fields) {
		$fields = $this->wsnc_get_account_fields();
		foreach ($fields as $key => $field_args) {
			if (!empty($field_args['hide_in_checkout'])) { continue; }
			$field_args['priority'] = isset($field_args['priority']) ? $field_args['priority'] : 0;
			$checkout_fields['account'][$key] = $field_args;
		}
		if (!empty($checkout_fields['account']['account_password']) && !isset($checkout_fields['account']['account_password']['priority'])) {
			$checkout_fields['account']['account_password']['priority'] = 0;
		}

		return $checkout_fields;
	}

	/**
	 * Validate frontend submission
	 * 
	 * @since	 0.1.0
	 */
	public function wsnc_validate_user_frontend_fields($errors) {
		$fields = $this->wsnc_get_account_fields();
		foreach ($fields as $key => $field_args) {
			if (empty( $field_args['required'])) { continue; }
			if (!isset($_POST['register']) && !empty($field_args['hide_in_account'])) { continue; }
			if (isset($_POST['register']) && !empty($field_args['hide_in_registration'])) { continue; }
			if (empty($_POST[$key])) {
				$message = sprintf(__('%s is a required field.', 'woocommerce-siret-number-checks'), '<strong>' . $field_args['label'] . '</strong>');
				$errors->add($key, $message);
			}
		}

		return $errors;
	}

}
//https://iconicwp.com/blog/the-ultimate-guide-to-adding-custom-woocommerce-user-account-fields/