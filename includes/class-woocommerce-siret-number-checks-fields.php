<?php

/**
 * Title
 *
 * Description
 *
 * @since      0.1.0
 * @package    Woocommerce_Siret_Number_Checks
 * @subpackage Woocommerce_Siret_Number_Checks/includes
 * @author     Bartiff <bartiff@gmail.com>
 */
class Woocommerce_Siret_Number_Checks_Fields {
   
	/**
	 * Send options plugin to a data for a Javascript variable
	 * 
	 * @since	 0.1.0
	 */
	protected function getWsncOptions($handle) {
		$WsncOptions = [
			'wsnc_check_siret_number' => get_option( 'wsnc-check-siret-number' )
		];
		wp_localize_script( $handle, 'WsncOptions', $WsncOptions );
	}

    /**
	 * WSNC fields to add with their options
	 * 
	 * @since	 0.1.0
	 * @access   protected
	 */
	protected function wsnc_get_account_fields() {
		$required = (get_option( 'wsnc-required-siret-number' ) === 'on') ? true : false;
		$readonly = (get_option( 'wsnc-required-siret-number' ) === 'on') ? ['readonly' => 'readonly'] : '';
		return apply_filters('wsnc_account_fields', array(
			'wsnc_company' => array(
				'type'        		   => 'text',
				'label'       		   => __('Company name', 'woocommerce-siret-number-checks'),
				'required'    		   => $required,
				'input_class'		   => ['wsnc-input'],
				'custom_attributes'	   => $readonly,
				'hide_in_account'      => false,
				'hide_in_admin'        => false,
				'hide_in_checkout'     => false,
				'hide_in_registration' => false,
				'sanitize' 			   => 'wc_clean'
			),
			'wsnc_siret'   => array(
				'type'  			   => 'text',
				'label' 			   => __('SIRET number', 'woocommerce-siret-number-checks'),
				'maxlength' 		   => 14,
				'required'    		   => $required,
				'input_class'		   => ['wsnc-input'],
				'hide_in_account'      => false,
				'hide_in_admin'        => false,
				'hide_in_checkout'     => false,
				'hide_in_registration' => false,
				'sanitize' 			   => 'wc_clean'
			),
		));

    }
    
	/**
	 * Get currently editing user ID (frontend account/edit profile/edit other user)
	 * 
	 * @since	 0.1.0
	 * @access   protected
	 */
	protected function wsnc_get_edit_user_id() {

		return isset($_GET['user_id']) ? (int) $_GET['user_id'] : get_current_user_id();
	}

	/**
	 * Accessing the saved user data
	 * 
	 * @since	 0.1.0
	 * @access   protected
	 */
	protected function wsnc_get_userdata($user_id, $key) {
		if (!$this->wsnc_is_userdata($key)) {
			return get_user_meta($user_id, $key, true);
		}
		$userdata = get_userdata($user_id);
		if (!$userdata || !isset($userdata->{$key})) { return ''; }

		return $userdata->{$key};
	}

	/**
	 * Determine whether WSNC fields is one that WordPress has predefined
	 * 
	 * @since	 0.1.0
     * @access   protected
	 */
	protected function wsnc_is_userdata($key) {
		$userdata = array(
			'user_pass',
			'user_login',
			'user_nicename',
			'user_url',
			'user_email',
			'display_name',
			'nickname',
			'first_name',
			'last_name',
			'description',
			'rich_editing',
			'user_registered',
			'role',
			'jabber',
			'aim',
			'yim',
			'show_admin_bar_front',
		);

		return in_array($key, $userdata);
	}
	
	/**
	 * Prevent hidden fields from saving with blank data
	 * 
	 * @since	 0.1.0
	 * @access   protected
	 */
	protected function wsnc_is_field_visible($field_args) {
		$visible = true;
		$action = filter_input(INPUT_POST, 'action');
		if (is_admin() && ! empty($field_args['hide_in_admin'])) {
			$visible = false;
		} elseif ((is_account_page() || $action === 'save_account_details') && is_user_logged_in() && !empty($field_args['hide_in_account'])) {
			$visible = false;
		} elseif ((is_account_page() || $action === 'save_account_details') && !is_user_logged_in() && !empty($field_args['hide_in_registration'])) {
			$visible = false;
		} elseif (is_checkout() && ! empty($field_args['hide_in_checkout'])) {
			$visible = false;
		}

		return $visible;
	}

	/**
	 * Displays registration warning with SIRET number
	 * 
	 * @since	0.1.0
	 */
	public function wsnc_print_notice_siret_info() {
		printf(
			__( '<p>The %2$s service is only available on the company with a SIRET number. If you do not have one, please <a href="%1$s" title="Contact us">contact us.</a></p>', 'woocommerce-siret-number-checks' )
		, get_page_link(476), get_bloginfo('name'));

    }

	/**
	 * Save the WSNC field data to the user
	 * 
	 * @since	 0.1.0
	 */
	public function wsnc_save_account_fields($customer_id) {
		$fields = $this->wsnc_get_account_fields();
		$sanitized_data = [];
		$i = 0;
		foreach ($fields as $key => $field_args) {
			if ($i === 1) {
				$siret = $_POST[$key];
				$i = 2;
			} else {
				$name = $_POST[$key];
			}
			$i = 1;
		}
		$check_valid_siret = Woocommerce_Siret_Number_Checks_Ndsapi::verif_siret($name, $siret);
		if ($check_valid_siret) {
			foreach ($fields as $key => $field_args) {
				if (!$this->wsnc_is_field_visible($field_args)) { continue; }
				$sanitize = isset($field_args['sanitize']) ? $field_args['sanitize'] : 'wc_clean';
				$value = isset($_POST[$key]) ? call_user_func($sanitize, $_POST[$key]) : '';
				if ($this->wsnc_is_userdata($key)) {
					$sanitized_data[$key] = $value;
					continue;
				}
				update_user_meta($customer_id, $key, $value);
			}
			if (!empty($sanitized_data)) {
				$sanitized_data['ID'] = $customer_id;
				wp_update_user($sanitized_data);
			}
		} else {
			if (!is_admin()) {
				wc_add_notice( __( 'The SIRET number does not appear to be valid.', 'woocommerce-siret-number-checks' ), 'error' );
			}
		}
	}

	/**
	 * Default values after submission errors
	 * 
	 * @since	 0.1.0
	 */
	public function wsnc_add_post_data_to_account_fields($fields) {
		if (empty($_POST)) { return $fields; }
		foreach ($fields as $key => $field_args) {
			if (empty($_POST[$key])) {
				$fields[$key]['value'] = '';
				continue;
			}
			$fields[$key]['value'] = $_POST[$key];
		}

		return $fields;
	}

	/**
	 * Starts a request to check the siret via the external API
	 * 
	 * @since	0.1.0
	 */
	public function wsnc_fetch_siret() {
		$resp = Woocommerce_Siret_Number_Checks_Ndsapi::check_siret($_REQUEST['siret']);       
		wp_send_json($resp);
		exit;
	}

}