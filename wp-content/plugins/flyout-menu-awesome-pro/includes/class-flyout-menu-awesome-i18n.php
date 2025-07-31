<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://themesawesome.com/
 * @since      1.0.0
 *
 * @package    Flyout_Menu_Awesome
 * @subpackage Flyout_Menu_Awesome/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Flyout_Menu_Awesome
 * @subpackage Flyout_Menu_Awesome/includes
 * @author     Themes Awesome <admin@themesawesome.com>
 */
class Flyout_Menu_Awesome_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'flyout-menu-awesome',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
