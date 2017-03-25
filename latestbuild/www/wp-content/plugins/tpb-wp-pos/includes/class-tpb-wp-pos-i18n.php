<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://waaark.com
 * @since      1.0.0
 *
 * @package    Tpb_Wp_Pos
 * @subpackage Tpb_Wp_Pos/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Tpb_Wp_Pos
 * @subpackage Tpb_Wp_Pos/includes
 * @author     Antoine Wodniack <antoine@wodniack.fr>
 */
class Tpb_Wp_Pos_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'tpb-wp-pos',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
