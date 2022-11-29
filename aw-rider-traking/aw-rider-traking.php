<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://https://dev.rapidev.tech/
 * @since             1.0.0
 * @package           Aw_Rider_Traking
 *
 * @wordpress-plugin
 * Plugin Name:       Rider Traking
 * Plugin URI:        https://https://dev.rapidev.tech/aw-rider-traking
 * Description:       This is a description of the plugin.
 * Version:           1.0.0
 * Author:            Abdul Wahab
 * Author URI:        https://https://dev.rapidev.tech/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       aw-rider-traking
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'AW_RIDER_TRAKING_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-aw-rider-traking-activator.php
 */
function activate_aw_rider_traking() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-aw-rider-traking-activator.php';
	Aw_Rider_Traking_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-aw-rider-traking-deactivator.php
 */
function deactivate_aw_rider_traking() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-aw-rider-traking-deactivator.php';
	Aw_Rider_Traking_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_aw_rider_traking' );
register_deactivation_hook( __FILE__, 'deactivate_aw_rider_traking' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-aw-rider-traking.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_aw_rider_traking() {

	$plugin = new Aw_Rider_Traking();
	$plugin->run();

}
run_aw_rider_traking();
