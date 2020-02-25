<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://bartiff.net/
 * @since             0.1.0
 * @package           Woocommerce_Siret_Number_Checks
 *
 * @wordpress-plugin
 * Plugin Name:       Siret Number Checks for WooCommerce
 * Plugin URI:        https://bartiff.net/wsnc
 * Description:       Siret Number Checks for WooCommerce is a plugin allowing the registration to WooCommerce with the SIRET number.
 * Version:           0.1.0
 * Author:            Bartiff
 * Author URI:        https://bartiff.net/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woocommerce-siret-number-checks
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 0.1.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WOOCOMMERCE_SIRET_NUMBER_CHECKS_VERSION', '0.1.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woocommerce-siret-number-checks-activator.php
 */
function activate_woocommerce_siret_number_checks() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-siret-number-checks-activator.php';
	Woocommerce_Siret_Number_Checks_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woocommerce-siret-number-checks-deactivator.php
 */
function deactivate_woocommerce_siret_number_checks() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-siret-number-checks-deactivator.php';
	Woocommerce_Siret_Number_Checks_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woocommerce_siret_number_checks' );
register_deactivation_hook( __FILE__, 'deactivate_woocommerce_siret_number_checks' );

/**
 * Class containing useful methods for frontend and admin fields
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-siret-number-checks-fields.php';

/**
 * Static class to call the checks of SIRET
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-siret-number-checks-ndsapi.php';

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-siret-number-checks.php';


/**
 * Update checker library for WordPress plugins : Plugin Update Checker
 * https://github.com/YahnisElsts/plugin-update-checker
 */
require plugin_dir_path( __FILE__ ) . 'includes/plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/Bartiff/woocommerce-siret-number-checks.git',
	__FILE__,
	'woocommerce-siret-number-checks'
);
$myUpdateChecker->getVcsApi()->enableReleaseAssets();

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.1.0
 */
function run_woocommerce_siret_number_checks() {

	$plugin = new Woocommerce_Siret_Number_Checks();
	$plugin->run();

}
run_woocommerce_siret_number_checks();
