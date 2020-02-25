<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://bartiff.net
 * @since      0.1.0
 *
 * @package    Woocommerce_Siret_Number_Checks
 * @subpackage Woocommerce_Siret_Number_Checks/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woocommerce_Siret_Number_Checks
 * @subpackage Woocommerce_Siret_Number_Checks/admin
 * @author     Bartiff <bartiff@gmail.com>
 */
class Woocommerce_Siret_Number_Checks_Admin extends Woocommerce_Siret_Number_Checks_Fields {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woocommerce-siret-number-checks-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woocommerce-siret-number-checks-admin.js', array( 'jquery' ), $this->version, false );
		$this->getWsncOptions($this->plugin_name);

	}

	/**
	 * Check if WooCommerce is activated
	 * 
	 * @since 	0.1.0
	 */
	private function is_woocommerce_activated() {
		if (class_exists('woocommerce')) { return true; } else { return false; }
	}

	/**
	 * Show WSNC Fields to the WordPress Admin Area
	 * 
	 * @since	0.1.0
	 */
	public function wsnc_print_user_admin_fields() {
		$fields = $this->wsnc_get_account_fields();
		if (get_option( 'wsnc-request-siret-number' ) === 'on') {
			?>
			<h2><?php _e('Company identification', 'woocommerce-siret-number-checks'); ?></h2>
			<table class="form-table" id="fauneshop-company-informations">
				<tbody>
				<?php foreach ($fields as $key => $field_args) { ?>
					<?php
					if (!empty($field_args['hide_in_admin'])) { continue; }
					$user_id = $this->wsnc_get_edit_user_id();
					$value = $this->wsnc_get_userdata($user_id, $key);
					?>
					<tr>
						<th>
							<label for="<?php echo $key; ?>"><?php echo $field_args['label']; ?></label>
						</th>
						<td>
							<?php $field_args['label'] = false; ?>
							<?php woocommerce_form_field($key, $field_args, $value); ?>
						</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
			<?php
		}
	}

	/**
	 * Set transient only when the plugin is activated
	 *
	 * @since	0.1.0
	 */
	public static function add_the_transient() {
	    set_transient('wsnc-activation-notice', true, 5);
	}

	/**
	 * Alert notice if WooCommerce is desactivated
	 *
	 * @since	0.1.0
	 */
	public function wsnc_verify_woocommerce_activation() {
		set_transient('wsnc-verify-woocommerce-activation', true, 5);
		if (get_transient('wsnc-verify-woocommerce-activation')) {
			if (!$this->is_woocommerce_activated()) {
				echo '<div class="error notice is-dismissible">
					<p>' . __('<strong>Siret Number Checks for WooCommerce</strong> requires <a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a> to be installed and active!', 'woocommerce-siret-number-checks') . '</p>
				</div>';
			}
			delete_transient('wsnc-verify-woocommerce-activation');
		}
	}

	/**
	 * Admin notice on activation
	 *
	 * @since	0.1.0
	 */
	public function wsnc_notice_to_configure_settings() {
		if (get_transient('wsnc-activation-notice')) {
			if ($this->is_woocommerce_activated()) {
				echo '<div class="updated notice is-dismissible">
					<p>' . __('<strong>Siret Number Checks for WooCommerce</strong> must be configured. You can do it by clicking here : <a href="/wp-admin/admin.php?page=' . $this->plugin_name . '-settings">WSNC</a> settings', 'woocommerce-siret-number-checks') . '</p>
				</div>';
			}
			delete_transient('wsnc-activation-notice');
		}
	}


	/**
	 * Add admin menu and page
	 * 
	 * @since	0.1.0
	 */
	public function wsnc_add_admin_menu() {
		add_submenu_page(
			'woocommerce',
			__('Woocommerce Siret Number Checks - Settings', 'woocommerce-siret-number-checks'),
			__('SIRET verification', 'woocommerce-siret-number-checks'),
			'administrator',
			$this->plugin_name.'-settings',
			array( $this, 'wsnc_submenu_page_callback' )
		);
	}

	/**
	 * Render admin page
	 * 
	 * @since	0.1.0
	 */
	public function wsnc_submenu_page_callback() {
		include_once 'partials/'.$this->plugin_name.'-admin-display.php';
	}

	/**
	 * Building the settings page and register settings
	 * 
	 * @since	0.1.0
	 */
	public function wsnc_register_setting() {
		add_settings_section( // General section
			'wsnc_general_section',
			__('General settings', 'woocommerce-siret-number-checks'),
			array( $this, 'wsnc_display_general_settings' ),
			$this->plugin_name
		);
		add_settings_field( // wsnc-request-siret-number field
			'wsnc-request-siret-number',
			__('Enable SIRET number request for WooCommerce accounts', 'woocommerce-siret-number-checks'),
			array( $this, 'wsnc_display_render_request_siret_number' ),
			$this->plugin_name,
			'wsnc_general_section',
			array( 'label_for' => 'wsnc-request-siret-number' )
		);
		add_settings_field( // wsnc-required-siret-number field
			'wsnc-required-siret-number',
			__('Make the SIRET number mandatory for registration', 'woocommerce-siret-number-checks'),
			array( $this, 'wsnc_display_render_required_siret_number' ),
			$this->plugin_name,
			'wsnc_general_section',
			array( 'label_for' => 'wsnc-required-siret-number' )
		);
		add_settings_field( // wsnc-check-siret-number field
			'wsnc-check-siret-number',
			__('Automatic control of the SIRET number', 'woocommerce-siret-number-checks'),
			array( $this, 'wsnc_display_render_check_siret_number' ),
			$this->plugin_name,
			'wsnc_general_section',
			array( 'label_for' => 'wsnc-check-siret-number' )
		);
		
		add_settings_section( // numero-de-siret.com API section
			'wsnc_ndsapi_section',
			__('Verification of company information by www.numero-de-siret.com - API', 'woocommerce-siret-number-checks'),
			array( $this, 'wsnc_display_ndsapi_settings' ),
			$this->plugin_name
		);
		add_settings_field( // wsnc-ndsapi-apikey field
			'wsnc-ndsapi-apikey',
			__('API key', 'woocommerce-siret-number-checks'),
			array( $this, 'wsnc_display_render_ndsapi_apikey' ),
			$this->plugin_name,
			'wsnc_ndsapi_section',
			array( 'label_for' => 'wsnc-ndsapi-apikey' )
		);
		add_settings_field( // wsnc-ndsapi-secretkey field
			'wsnc-ndsapi-secretkey',
			__('Secret key', 'woocommerce-siret-number-checks'),
			array( $this, 'wsnc_display_render_ndsapi_secretkey' ),
			$this->plugin_name,
			'wsnc_ndsapi_section',
			array( 'label_for' => 'wsnc-ndsapi-secretkey' )
		);

		// Saves options
		register_setting( $this->plugin_name, 'wsnc-request-siret-number' );
		register_setting( $this->plugin_name, 'wsnc-required-siret-number' );
		register_setting( $this->plugin_name, 'wsnc-check-siret-number' );
		register_setting( $this->plugin_name, 'wsnc-ndsapi-apikey', 'sanitize_text_field' );
		register_setting( $this->plugin_name, 'wsnc-ndsapi-secretkey', array($this, 'wsnc_bad_or_good_api_keys') );
	}

	/**
	 * Render text for General section
	 * 
	 * @since	0.1.0
	 */
	public function wsnc_display_general_settings() {
		echo '<p>' . __('Please update the general settings of WooCommerce Siret Number Checks.</p>', 'woocommerce-siret-number-checks') . '</p>';
	}

	/**
	 * Render text for numero-de-siret.com API section
	 * 
	 * @since	0.1.0
	 */
	public function wsnc_display_ndsapi_settings() {
		echo '<div id="api-numero-de-siret">';
		echo '<p>' . __('Please insert your keys here to use the API. Here\'s the procedure to follow :', 'woocommerce-siret-number-checks') . '</p>';
		echo '<ol>';
		echo '<li>' . __('To retrieve your API keys, fill out the form at this address:', 'woocommerce-siret-number-checks') . ' <a href="https://www.numero-de-siret.com/api/documentation/" target="_blank">' . __('retreive your API keys', 'woocommerce-siret-number-checks') . '</a>.</li>';
		echo '<li>' . __('Copy both keys displayed.', 'woocommerce-siret-number-checks') . '</li>';
		echo '<li>' . __('Paste the two keys in the fields below and validate the parameters.', 'woocommerce-siret-number-checks') . '</li>';
		echo '</ol>';
		echo '</div>';
	}

	/**
	 * Render the wsnc-request-siret-number checkbox
	 * 
	 * @since	0.1.0
	 */
	public function wsnc_display_render_request_siret_number() {
		$request_siret_number = get_option( 'wsnc-request-siret-number' );
		echo '<input type="checkbox" id="wsnc-request-siret-number" name="wsnc-request-siret-number" ' . checked('on', $request_siret_number, false) . '>';
		echo '<p class="description">' . __('Enable this option to request the SIRET number from your WooCommerce customers.', 'woocommerce-siret-number-checks') . '</p>';
	}

	/**
	 * Render the wsnc-required-siret-number checkbox
	 * 
	 * @since	0.1.0
	 */
	public function wsnc_display_render_required_siret_number() {
		$required_siret_number = get_option( 'wsnc-required-siret-number' );
		echo '<input type="checkbox" id="wsnc-required-siret-number" name="wsnc-required-siret-number" ' . checked('on', $required_siret_number, false) . '>';
        echo '<p class="description">' . __('Enable this option to have the SIRET number a required field.', 'woocommerce-siret-number-checks') . '</p>';
	}

	/**
	 * Render the wsnc-check-siret-number checkbox
	 * 
	 * @since	0.1.0
	 */
	public function wsnc_display_render_check_siret_number() {
		$check_siret_number = get_option( 'wsnc-check-siret-number' );
		echo '<input type="checkbox" id="wsnc-check-siret-number" name="wsnc-check-siret-number" ' . checked('on', $check_siret_number, false) . '>';
        echo '<p class="description">' . __('Enable this option to automatically control the SIRET number via the service <a target="_blank" href="https://www.numero-de-siret.com">www.numero-de-siret.com</a> offered by <a target="_blank" href="http://www.atafotostudio.com"> ATAFOTO.studio </a>.', 'woocommerce-siret-number-checks') . '</p>';
	}

	/**
	 * Render the wsnc-check-siret-number API Key field
	 * 
	 * @since	0.1.0
	 */
	public function wsnc_display_render_ndsapi_apikey() {
		$apikey = get_option( 'wsnc-ndsapi-apikey' );
		if (get_option( 'wsnc-check-siret-number' ) === 'on') {
			echo '<input required type="text" name="wsnc-ndsapi-apikey" id="wsnc-ndsapi-apikey" class="wsnc-ndsapi-apikey" value="' . $apikey . '">';
		} else {
			echo '<input readonly type="text" name="wsnc-ndsapi-apikey" id="wsnc-ndsapi-apikey" class="wsnc-ndsapi-apikey" value="' . $apikey . '">';
		}
	}

	/**
	 * Render the wsnc-check-siret-number secret Key field
	 * 
	 * @since	0.1.0
	 */
	public function wsnc_display_render_ndsapi_secretkey() {
		$secretkey = get_option( 'wsnc-ndsapi-secretkey' );
		if (get_option( 'wsnc-check-siret-number' ) === 'on') {
			echo '<input required type="text" name="wsnc-ndsapi-secretkey" id="wsnc-ndsapi-secretkey" class="wsnc-ndsapi-secretkey" value="' . $secretkey . '"> ';
		} else {
			echo '<input readonly type="text" name="wsnc-ndsapi-secretkey" id="wsnc-ndsapi-secretkey" class="wsnc-ndsapi-secretkey" value="' . $secretkey . '"> ';
		}
	}

	/**
	 * Admin global alert when API keys isn't valid
	 * 
	 * @since	0.1.0
	 */
	public function wsnc_alert_keys_not_valid() {
		if (get_option('wsnc-check-siret-number') === 'on') {
			if (get_transient('wsnc-bad-keys-notice') === '1') {
				$message = 'Woocommerce Siret Number Checks - ' . __('Your API keys don\'t appear to be valid.', 'woocommerce-siret-number-checks') . __(' Please, <a href="admin.php?page=' . $this->plugin_name . '-settings">enter your API keys.</a>', 'woocommerce-siret-number-checks');
				add_settings_error('wsnc_bad_keys_notice', 'wsnc_bad_keys_notice', $message, 'error');
				if (!isset($_GET['page']) || isset($_GET['page']) && $_GET['page'] !== $this->plugin_name . '-settings') {
					settings_errors('wsnc_bad_keys_notice');
				}
			}
		}
	}

	/**
	 * Verify if API keys are valid
	 * 
	 * @since	0.1.0
	 */
	public function wsnc_bad_or_good_api_keys($input) {
		if (get_option('wsnc-check-siret-number') === 'on') {
			$input = sanitize_text_field($input);
			$api_connection = Woocommerce_Siret_Number_Checks_Ndsapi::test_connection(false, get_option( 'wsnc-ndsapi-apikey' ), $input);
			if (isset($api_connection['error']) && $api_connection['error']) {
				$message = __('Error API connection : ' . $api_connection['errors']['message'], 'woocommerce-siret-number-checks');
				add_settings_error('wsnc_bad_keys_notice', 'wsnc_bad_keys_notice', $message, 'error');
				settings_errors('wsnc_bad_keys_notice');
				set_transient('wsnc-bad-keys-notice', true);
			} else {
				$message = 'Well done ! Your API keys are valid !';
				add_settings_error('wsnc_good_keys_notice', 'wsnc_good_keys_notice', $message, 'success');
				settings_errors('wsnc_good_keys_notice');
				delete_transient('wsnc-bad-keys-notice', true);
			}
		}
		return $input;
	}

	/**
	 * Add a link to plugin settings page in the plugin list
	 * 
	 * @since	0.1.0
	 */
	public function wsnc_settings_link($links) {
		$url = esc_url(add_query_arg(
			'page',
			$this->plugin_name . '-settings',
			get_admin_url() . 'admin.php'
		));
		$settings_link = "<a href='$url'>" . __( 'Settings' ) . '</a>';
		array_push($links, $settings_link);
		return $links;
	}

}