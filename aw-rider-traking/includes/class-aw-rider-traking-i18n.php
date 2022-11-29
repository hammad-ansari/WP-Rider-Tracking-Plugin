<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://https://dev.rapidev.tech/
 * @since      1.0.0
 *
 * @package    Aw_Rider_Traking
 * @subpackage Aw_Rider_Traking/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Aw_Rider_Traking
 * @subpackage Aw_Rider_Traking/includes
 * @author     Abdul Wahab <admin@dev.rapidev.tech>
 */
class Aw_Rider_Traking_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'aw-rider-traking',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
