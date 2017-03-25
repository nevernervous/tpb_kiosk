<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://waaark.com
 * @since             1.0.0
 * @package           Tpb_Wp_Pos
 *
 * @wordpress-plugin
 * Plugin Name:       TPB WP POS sync
 * Plugin URI:        http://www.thepeakbeyond.com
 * Description:       Sync data from a MJ Freeway POS system to WordPress.
 * Version:           1.0.0
 * Author:            Waaark
 * Author URI:        http://waaark.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tpb-wp-pos
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-tpb-wp-pos-activator.php
 */
function activate_tpb_wp_pos() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tpb-wp-pos-activator.php';
	Tpb_Wp_Pos_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-tpb-wp-pos-deactivator.php
 */
function deactivate_tpb_wp_pos() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tpb-wp-pos-deactivator.php';
	Tpb_Wp_Pos_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_tpb_wp_pos' );
register_deactivation_hook( __FILE__, 'deactivate_tpb_wp_pos' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-tpb-wp-pos.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_tpb_wp_pos() {

	$plugin = new Tpb_Wp_Pos();
	$plugin->run();

}
run_tpb_wp_pos();
