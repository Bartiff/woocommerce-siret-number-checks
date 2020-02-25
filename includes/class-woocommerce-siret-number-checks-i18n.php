<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://bartiff.net
 * @since      0.1.0
 *
 * @package    Woocommerce_Siret_Number_Checks
 * @subpackage Woocommerce_Siret_Number_Checks/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      0.1.0
 * @package    Woocommerce_Siret_Number_Checks
 * @subpackage Woocommerce_Siret_Number_Checks/includes
 * @author     Bartiff <bartiff@gmail.com>
 */
class Woocommerce_Siret_Number_Checks_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    0.1.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'woocommerce-siret-number-checks',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
