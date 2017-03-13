<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://waaark.com
 * @since      1.0.0
 *
 * @package    Tpb_Wp_Pos
 * @subpackage Tpb_Wp_Pos/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Tpb_Wp_Pos
 * @subpackage Tpb_Wp_Pos/public
 * @author     Antoine Wodniack <antoine@wodniack.fr>
 */
class Tpb_Wp_Pos_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
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
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Tpb_Wp_Pos_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Tpb_Wp_Pos_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tpb-wp-pos-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Tpb_Wp_Pos_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Tpb_Wp_Pos_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tpb-wp-pos-public.js', array( 'jquery' ), $this->version, false );

	}


    /**
     * Send order
     *
	 * @since    1.0.0
     */
    public static function send_order() {
    	if ( !isset( $_SESSION['cart'] ) || !$_SESSION['cart'] )
    		return false;

    	$cart = $_SESSION['cart'];

    	// Do things here to create order in POS
    	// ...
    	$result = true;

    	return $result;
    }
}
