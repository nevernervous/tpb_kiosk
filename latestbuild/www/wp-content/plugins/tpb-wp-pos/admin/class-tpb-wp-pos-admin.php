<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://waaark.com
 * @since      1.0.0
 *
 * @package    Tpb_Wp_Pos
 * @subpackage Tpb_Wp_Pos/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Tpb_Wp_Pos
 * @subpackage Tpb_Wp_Pos/admin
 * @author     Antoine Wodniack <antoine@wodniack.fr>
 */
class Tpb_Wp_Pos_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		// Add admin page
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );

        // Ajax actions
        add_action( 'wp_ajax_tpb_sync_data', array( $this, 'ajax_sync_data' ) );
        add_action( 'wp_ajax_tpb_sync_data_light', array( $this, 'sync_data_light' ) );

        // Scheduled actions
        add_filter( 'cron_schedules', array( $this, 'tpb_cron_schedules' ) );
        add_action( 'tpb_cron_sync', array( $this, 'sync_data_light' ) );
        if ( ! wp_next_scheduled( 'tpb_cron_sync' ) ) {
            wp_schedule_event( time(), 'ten_minutes',  'tpb_cron_sync' );
        }
	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tpb-wp-pos-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tpb-wp-pos-admin.js', array( 'jquery' ), $this->version, false );

	}

    /**
     * Add custom schedules
     *
     * @since    1.0.0
     */
    public function tpb_cron_schedules( $schedules ) {
        $schedules['ten_minutes'] = array(
            'interval' => 10 * 60, // 10 minutes * 60 seconds
            'display' => __( 'Every ten minues', 'tpb' )
        );

        return $schedules;
    }

	/**
	 * Init the plugin page.
	 *
	 * @since    1.0.0
	 */
	public function page_init() {
		register_setting( 'tpb-wp-pos-handle', 'tpb_wp_pos_api_key' );
		register_setting( 'tpb-wp-pos-handle', 'tpb_wp_pos_data_url' );

        add_settings_section(
            'tpb_wp_pos_action', // ID
            'Actions', // Title
            '', // Callback
            'tpb-wp-pos-handle' // Page
        );

        add_settings_field(
            'sync_btn', // ID
            'Sync data from your MJ Freeway POS', // Title
            array( $this, 'sync_btn_callback' ), // Callback
            'tpb-wp-pos-handle', // Page
            'tpb_wp_pos_action' // Section
        );

        add_settings_section(
            'tpb_wp_pos_setting', // ID
            'Settings', // Title
            '', // Callback
            'tpb-wp-pos-handle' // Page
        );

        add_settings_field(
            'data_url', // ID
            'Data URL', // Title
            array( $this, 'data_url_callback' ), // Callback
            'tpb-wp-pos-handle', // Page
            'tpb_wp_pos_setting' // Section
        );

        // add_settings_field(
        //     'api_key', // ID
        //     'MJ Freeway API key', // Title
        //     array( $this, 'api_key_callback' ), // Callback
        //     'tpb-wp-pos-handle', // Page
        //     'tpb_wp_pos_setting' // Section
        // );
	}

	/**
	 * Add the plugin page in admin menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_page() {
	    add_menu_page(
	    	__( 'POS Sync / Synchronise', 'tpb' ),
	    	__( 'POS Sync', 'tpb' ),
	    	'manage_options',
	    	'tpb-wp-pos-handle',
	    	array( $this, 'show_plugin_page')
	    );
	}

	/**
	 * Show the plugin admin page.
	 *
	 * @since    1.0.0
	 */
	public function show_plugin_page() {
        // Set class property
        $this->options = get_option( 'my_option_name' );
        ?>
        <div class="wrap">
            <h1>TPB MJ Freeway POS sync</h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'tpb-wp-pos-handle' );
                do_settings_sections( 'tpb-wp-pos-handle' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
	}

	/**
	 * Sync button field
	 *
	 * @since    1.0.0
	 */
	public function sync_btn_callback()
    {
        printf(
            '<button id="sync-btn" value="sync" class="button" type="button">%s</button> <button id="sync-btn-light" value="sync-light" class="button" type="button">%s</button>',
            __( 'Sync data', 'tpb' ),
            __( 'Sync data (light)', 'tpb' )
        );
    }

	/**
	 * API key field
	 *
	 * @since    1.0.0
	 */
	public function api_key_callback()
    {
        echo '<input name="tpb_wp_pos_api_key" id="tpb_wp_pos_api_key" type="text" value="' . get_option( 'tpb_wp_pos_api_key' ) . '" size="80" />';
    }

	/**
	 * Data URL field
	 *
	 * @since    1.0.0
	 */
	public function data_url_callback()
    {
        echo '<input name="tpb_wp_pos_data_url" id="tpb_wp_pos_data_url" type="text" value="' . get_option( 'tpb_wp_pos_data_url' ) . '" size="80" />';
    }

    /**
     * Sync data
     *
     * @since    1.0.0
     */
    public function ajax_sync_data() {
        global $wpdb;

        if ( $chunks = get_transient( 'tpb_sync_chunks' ) ) {

            $products = array_shift( $chunks );
            $done = get_transient( 'tpb_sync_done' );
            $total = get_transient( 'tpb_sync_total' );

            set_transient( 'tpb_sync_chunks', $chunks );

        } else {

            $data_url = get_option( 'tpb_wp_pos_data_url' );

            // Get data
            $data = file_get_contents( $data_url );
            $all_products = json_decode( $data, true );

            $chunks = array_chunk( $all_products, 10 );
            $products = array_shift( $chunks );
            $done = 0;
            $total = count($all_products);

            set_transient( 'tpb_sync_chunks', $chunks );
            set_transient( 'tpb_sync_total', $total );

            // Unpublish products before sync
            $this->unpublish_products();
        }

        // Parse products
        $remaining_products = $this->parse_products( $products, 'full' );
        $this->disable_products( $remaining_products );

        // Increment done counter
        $done += count($products);
        set_transient( 'tpb_sync_done', $done );

        if ( $done == $total ) {
            // Set categories icons
            $categories = get_terms( 'product_category' );
            foreach( $categories as $category ) {
                $icons = array(
                    'Flowers' => 'fa-leaf',
                    'Concentrates' => 'fa-tint',
                    'Edibles' => 'fa-spoon'
                );

                update_term_meta( $category->term_id, '_icon', 'field_586fdac0bd5cd' );
                update_term_meta( $category->term_id, 'icon', (isset($icons[$category->name]) ? $icons[$category->name] : 'fa-leaf') );
            }

            // Clean cache
            if ( function_exists( 'rocket_clean_domain' ) )
                rocket_clean_domain();

            // Delete transient
            delete_transient( 'tpb_sync_done' );
            delete_transient( 'tpb_sync_total' );
            delete_transient( 'tpb_sync_chunks' );
        }

        // Return results
        echo json_encode( array(
            'done'              => $done,
            'total'             => $total,
            'products'          => $products,
        ) );

        die();
    }

    /**
     * Sync data light
     *
     * @since    1.0.0
     */
    public function sync_data_light() {
        global $wpdb;

        $home = get_option( 'home' );
        if ( strpos( $home, 'the.peak.beyond' ) !== false )
            return false;

        $data_url = get_option( 'tpb_wp_pos_data_url' );

        // Get data
        $data = file_get_contents( $data_url );
        $products = json_decode( $data, true );
        $total = count($products);

        // Unpublish products before sync
        $this->unpublish_products();

        // Parse products
        $remaining_products = $this->parse_products( $products );
        $this->disable_products( $remaining_products );

        // Clean cache
        if ( function_exists( 'rocket_clean_domain' ) )
            rocket_clean_domain();

        // Return results
        echo json_encode( array(
            'products'          => $products,
        ) );

        die();
    }


    /**
     * Unpublish products before sync
     */
    private function unpublish_products() {
        global $wpdb;

        // Products to ignore
        $ids = $wpdb->get_col( $wpdb->prepare(
            "
            SELECT      pm.post_id
            FROM        $wpdb->postmeta pm
            WHERE       pm.meta_key = %s
                        AND pm.meta_value = 1
            ",
            'sync_post_status'
        ) );

        // Disable all products before importing new ones
        $status = $wpdb->query(
            "
            UPDATE $wpdb->posts
            SET post_status = 'draft'
            WHERE post_type = 'product' ".
                ($ids ? "AND ID NOT IN (" . implode( ',', $ids ) . ")":"")
        );

    }


    /**
     * Parse and handle products during sync
     */
   private function parse_products( $products, $type = 'light' ) {

        $products_data = $this->get_products_data();

        // Parse products
        foreach( $products as $product ) {
            $product_id = $product['record_id'];
            $post = $products_data[$product_id];
            $post_id = $post->ID;

            // Remove product from list
            unset($products_data[$product_id]);

            $prices = explode( ',',  $product['prices'] );
            $formated_prices = array();
            foreach( $prices as $raw ) {
                $raw = explode( ':',  $raw );
                $formated_prices[] = array( 'unit' => $raw[0], 'price' => round($raw[1]) );
            }

            usort( $formated_prices, 'tpb_prices_usort' );

            // Set datas
            $post_data = array(
                'post_title'    => stripslashes($product['strain_name']),
                'post_content'  => print_r( $product, true ),
                'post_status'   => ( (bool)$product['in_stock'] ? 'publish' : 'draft' ),
                'post_type'     => 'product',
                'meta_input'    => array(
                    'product_id'    => $product_id,
                    'in_stock'      => $product['in_stock'],
                    'prices'        => json_encode( $formated_prices ),
                    'default_price' => $formated_prices[0]['price'],
                    'brand'         => stripslashes($product['brand']),
                    'type'          => stripslashes($product['type']),
                    'sku'           => $product['SKU']
                )
            );

            // Product already exists
            if ( $post_id ) {
                $post_data['ID'] = $post_id;

                // Ignore fields from sync
                $fields = array( 'post_title', 'post_status', 'in_stock', 'prices', 'default_price', 'brand', 'type', 'sku' );

                    foreach( $fields as $field ) {
                        $ignore_field = get_field( 'sync_'.$field, $post_id );

                         if ($ignore_field === true || $ignore_field == 1) {
                            if ( isset( $post_data[$field] ) )
                                unset( $post_data[$field] );
                            else if ( isset( $post_data['meta_input'][$field] ) )
                                unset( $post_data['meta_input'][$field] );
                        }
                    }


                if ( !isset( $post_data['post_title'] ) || !$post_data['post_title'] )
                    $post_data['post_title'] = $post->post_title;
                if ( !isset( $post_data['post_status'] ) || !$post_data['post_status'] )
                    $post_data['post_status'] = $post->post_status;

                // If product has not been updated since last time, continue
                if (strtotime( $post->post_modified ) > strtotime( $product['last_updated'] ) ) {
                    // Insert/update post
                    $inserted_id = wp_insert_post( $post_data );

                    continue;
                }
            } else {

                if ( $type == 'light' )
                    continue;

                // ACF tabs
                $tabs = null;
                if ( $product['category'] == 'Flowers') {
                    // Highlights
                    $tabs[] = array(
                        'type' => 'highlights',
                        'icon' => 'fa-newspaper-o',
                        'title' => 'Highlights',
                        'text' => trim_text( stripslashes($product['description']), 400, false, false ),
                        // 'graphs' => array(
                        //     array(
                        //         'label' => 'LOREM',
                        //         'value' => '123456789',
                        //         'percent' => '0.5'
                        //     ),
                        //     array(
                        //         'label' => 'LOREM',
                        //         'value' => '123456789',
                        //         'percent' => '0.5'
                        //     )
                        // ),
                        'video' => $product['video']
                    );

                    // Flavor
                    $tabs[] = array(
                        'type' => 'flavor',
                        'icon' => 'fa-leaf',
                        'title' => 'Flavor',
                        'aroma' => trim_text( stripslashes($product['aroma']), 350, false, false ),
                        'flavor' => trim_text( stripslashes($product['flavor']), 350, false, false )
                    );

                    // Attributes
                    if ( $product['moods'] || $product['medical'] || $product['side_effects']) {

                        $attributes = array( 'moods', 'medical', 'side_effects' );
                        $groups = array();

                        foreach ( $attributes as $attribute ) {
                            $effects = $product[$attribute];
                            if ( !$effects )
                                continue;

                            $effects = explode( ',',  $effects );
                            $formated_effects = array();
                            foreach( $effects as $raw ) {
                                $raw = explode( ':',  $raw );
                                $formated_effects[] = array( 'label' => $raw[0], 'value' => $raw[1] );
                            }
                            $effects = $formated_effects;

                            if ( $attribute == 'moods' ) {
                                $title = 'Moods';
                                $color = 'green';
                            } else if ( $attribute == 'medical' ) {
                                $title = 'Medical';
                                $color = 'blue';
                            } else if ( $attribute == 'side_effects' ) {
                                $title = 'Side Effects';
                                $color = 'red';
                            }

                            $group = array(
                                'title' => $title,
                                'attributes' => array()
                            );

                            foreach ( $effects as $effect) {
                                $group['attributes'][] = array(
                                    'label' => $effect['label'],
                                    'color' => $color,
                                    'percent' => $effect['value']/100
                                );
                            }

                            $groups[] = $group;
                        }

                        $tabs[] = array(
                            'type' => 'attributes',
                            'icon' => 'fa-bar-chart',
                            'title' => 'Attributes',
                            'groups' => $groups
                        );
                    }

                    // Reviews
                    // $tabs[] = array(
                    //     'type' => 'reviews',
                    //     'icon' => 'fa-comments',
                    //     'title' => 'Reviews',
                    //     'reviews' => array(
                    //         array(
                    //             'name' => 'John Doe',
                    //             'photo' => '#',
                    //             'note' => '3',
                    //             'text' => 'Lorem ipsum'
                    //         )
                    //     )
                    // );
                } else if ( $product['category'] == 'Concentrates') {
                    $tabs = array(
                        array(
                            'type' => 'highlights',
                            'icon' => 'fa-newspaper-o',
                            'title' => 'Highlights',
                            'text' => trim_text( stripslashes($product['description']), 400, false, false ),
                            // 'graphs' => array(
                            //     array(
                            //         'label' => 'LOREM',
                            //         'value' => '123456789',
                            //         'percent' => '0.5'
                            //     ),
                            //     array(
                            //         'label' => 'LOREM',
                            //         'value' => '123456789',
                            //         'percent' => '0.5'
                            //     )
                            // ),
                            'video' => $product['video']
                        ),
                        // array(
                        //     'type' => 'reviews',
                        //     'icon' => 'fa-comments',
                        //     'title' => 'Reviews',
                        //     'reviews' => array(
                        //         array(
                        //             'name' => 'John Doe',
                        //             'photo' => '#',
                        //             'note' => '3',
                        //             'text' => 'Lorem ipsum'
                        //         )
                        //     )
                        // )
                    );
                } else if ( $product['category'] == 'Edibles') {
                    $tabs = array(
                        array(
                            'type' => 'highlights',
                            'icon' => 'fa-newspaper-o',
                            'title' => 'Highlights',
                            'text' => trim_text( stripslashes($product['description']), 400, false, false ),
                            // 'graphs' => array(
                            //     array(
                            //         'label' => 'LOREM',
                            //         'value' => '123456789',
                            //         'percent' => '0.5'
                            //     ),
                            //     array(
                            //         'label' => 'LOREM',
                            //         'value' => '123456789',
                            //         'percent' => '0.5'
                            //     )
                            // ),
                            'video' => $product['video']
                        ),
                        // array(
                        //     'type' => 'reviews',
                        //     'icon' => 'fa-comments',
                        //     'title' => 'Reviews',
                        //     'reviews' => array(
                        //         array(
                        //             'name' => 'John Doe',
                        //             'photo' => '#',
                        //             'note' => '3',
                        //             'text' => 'Lorem ipsum'
                        //         )
                        //     )
                        // )
                    );
                } else if ( $product['category'] == 'Other') {
                    $tabs = array(
                        array(
                            'type' => 'highlights',
                            'icon' => 'fa-newspaper-o',
                            'title' => 'Highlights',
                            'text' => trim_text( stripslashes($product['description']), 400, false, false ),
                            // 'graphs' => array(
                            //     array(
                            //         'label' => 'LOREM',
                            //         'value' => '123456789',
                            //         'percent' => '0.5'
                            //     ),
                            //     array(
                            //         'label' => 'LOREM',
                            //         'value' => '123456789',
                            //         'percent' => '0.5'
                            //     )
                            // ),
                            'video' => $product['video']
                        ),
                        // array(
                        //     'type' => 'reviews',
                        //     'icon' => 'fa-comments',
                        //     'title' => 'Reviews',
                        //     'reviews' => array(
                        //         array(
                        //             'name' => 'John Doe',
                        //             'photo' => '#',
                        //             'note' => '3',
                        //             'text' => 'Lorem ipsum'
                        //         )
                        //     )
                        // )
                    );
                }

                if ( $tabs ) {
                    $types = array();
                    $cnt = 0;
                    foreach( $tabs as $tab ) {
                        $types[] = $tab['type'];

                        if ( $tab['type'] == 'highlights' ) {

                            $post_data['meta_input']['_tabs_'.$cnt.'_icon'] = 'field_586be0a8ad234';
                            $post_data['meta_input']['tabs_'.$cnt.'_icon'] = $tab['icon'];
                            $post_data['meta_input']['_tabs_'.$cnt.'_title'] = 'field_586be0a1ad233';
                            $post_data['meta_input']['tabs_'.$cnt.'_title'] = $tab['title'];

                            $post_data['meta_input']['_tabs_'.$cnt.'_text'] = 'field_586bb2c9b118c';
                            $post_data['meta_input']['tabs_'.$cnt.'_text'] = $tab['text'];

                            if ( $tab['graphs'] ) {
                                $post_data['meta_input']['_tabs_'.$cnt.'_graphs'] = 'field_586cc97c03ec8';
                                $post_data['meta_input']['tabs_'.$cnt.'_graphs'] = count($tab['graphs']);

                                $i = 0;
                                foreach( $tab['graphs'] as $graph ) {
                                    $post_data['meta_input']['_tabs_'.$cnt.'_graphs_'.$i.'_label'] = 'field_586cc9cb03eca';
                                    $post_data['meta_input']['tabs_'.$cnt.'_graphs_'.$i.'_label'] = $graph['label'];
                                    $post_data['meta_input']['_tabs_'.$cnt.'_graphs_'.$i.'_value'] = 'field_586cc9d603ecb';
                                    $post_data['meta_input']['tabs_'.$cnt.'_graphs_'.$i.'_value'] = $graph['value'];
                                    $post_data['meta_input']['_tabs_'.$cnt.'_graphs_'.$i.'_percent'] = 'field_586cc9a703ec9';
                                    $post_data['meta_input']['tabs_'.$cnt.'_graphs_'.$i.'_percent'] = $graph['percent'];

                                    $i++;
                                }
                            }

                            if ( $tab['video'] ) {
                                $post_data['meta_input']['_tabs_'.$cnt.'_video'] = 'field_586cc9de03ecc';
                                $post_data['meta_input']['tabs_'.$cnt.'_video'] = $tab['video'];
                            }

                        }  else if ( $tab['type'] == 'attributes' ) {

                            $post_data['meta_input']['_tabs_'.$cnt.'_icon'] = 'field_586be0cead238';
                            $post_data['meta_input']['tabs_'.$cnt.'_icon'] = $tab['icon'];
                            $post_data['meta_input']['_tabs_'.$cnt.'_title'] = 'field_586be0c9ad237';
                            $post_data['meta_input']['tabs_'.$cnt.'_title'] = $tab['title'];

                            $post_data['meta_input']['_tabs_'.$cnt.'_groups'] = 'field_586d09197f56f';
                            $post_data['meta_input']['tabs_'.$cnt.'_groups'] = count($tab['groups']);

                            $i = 0;
                            foreach( $tab['groups'] as $group ) {
                                $post_data['meta_input']['_tabs_'.$cnt.'_groups_'.$i.'_title'] = 'field_586d09497f571';
                                $post_data['meta_input']['tabs_'.$cnt.'_groups_'.$i.'_title'] = $group['title'];
                                $post_data['meta_input']['_tabs_'.$cnt.'_groups_'.$i.'_attributes'] = 'field_586d092f7f570';
                                $post_data['meta_input']['tabs_'.$cnt.'_groups_'.$i.'_attributes'] = count($group['attributes']);

                                $j = 0;
                                foreach( $group['attributes'] as $attribute ) {
                                    $post_data['meta_input']['_tabs_'.$cnt.'_groups_'.$i.'_attributes_'.$j.'_label'] = 'field_586d09547f572';
                                    $post_data['meta_input']['tabs_'.$cnt.'_groups_'.$i.'_attributes_'.$j.'_label'] = $attribute['label'];
                                    $post_data['meta_input']['_tabs_'.$cnt.'_groups_'.$i.'_attributes_'.$j.'_color'] = 'field_586d09827f574';
                                    $post_data['meta_input']['tabs_'.$cnt.'_groups_'.$i.'_attributes_'.$j.'_color'] = $attribute['color'];
                                    $post_data['meta_input']['_tabs_'.$cnt.'_groups_'.$i.'_attributes_'.$j.'_percent'] = 'field_586d09587f573';
                                    $post_data['meta_input']['tabs_'.$cnt.'_groups_'.$i.'_attributes_'.$j.'_percent'] = $attribute['percent'];

                                    $j++;
                                }

                                $i++;
                            }

                        }  else if ( $tab['type'] == 'reviews' ) {

                            $post_data['meta_input']['_tabs_'.$cnt.'_icon'] = 'field_586e0eaf98008';
                            $post_data['meta_input']['tabs_'.$cnt.'_icon'] = $tab['icon'];
                            $post_data['meta_input']['_tabs_'.$cnt.'_title'] = 'field_586e0ea798007';
                            $post_data['meta_input']['tabs_'.$cnt.'_title'] = $tab['title'];

                            $post_data['meta_input']['_tabs_'.$cnt.'_reviews'] = 'field_586e066b0eb9e';
                            $post_data['meta_input']['tabs_'.$cnt.'_reviews'] = count($tab['reviews']);

                            $i = 0;
                            foreach( $tab['reviews'] as $review ) {
                                $post_data['meta_input']['_tabs_'.$cnt.'_reviews_'.$i.'_name'] = 'field_586e06770eb9f';
                                $post_data['meta_input']['tabs_'.$cnt.'_reviews_'.$i.'_name'] = $review['name'];
                                $post_data['meta_input']['_tabs_'.$cnt.'_reviews_'.$i.'_photo'] = 'field_586e067f0eba0';
                                $post_data['meta_input']['tabs_'.$cnt.'_reviews_'.$i.'_photo'] = $review['photo'];
                                $post_data['meta_input']['_tabs_'.$cnt.'_reviews_'.$i.'_note'] = 'field_586e068b0eba1';
                                $post_data['meta_input']['tabs_'.$cnt.'_reviews_'.$i.'_note'] = $review['note'];
                                $post_data['meta_input']['_tabs_'.$cnt.'_reviews_'.$i.'_text'] = 'field_586e06bf0eba2';
                                $post_data['meta_input']['tabs_'.$cnt.'_reviews_'.$i.'_text'] = $review['text'];

                                $i++;
                            }

                        }  else if ( $tab['type'] == 'text' ) {

                            $post_data['meta_input']['_tabs_'.$cnt.'_icon'] = 'field_586be0b5ad236';
                            $post_data['meta_input']['tabs_'.$cnt.'_icon'] = $tab['icon'];
                            $post_data['meta_input']['_tabs_'.$cnt.'_title'] = 'field_586be0b0ad235';
                            $post_data['meta_input']['tabs_'.$cnt.'_title'] = $tab['title'];
                            $post_data['meta_input']['_tabs_'.$cnt.'_text'] = 'field_586bb4a1b118f';
                            $post_data['meta_input']['tabs_'.$cnt.'_text'] = $tab['text'];

                        }  else if ( $tab['type'] == 'flavor' ) {

                            $post_data['meta_input']['_tabs_'.$cnt.'_icon'] = 'field_589daa24ef33d';
                            $post_data['meta_input']['tabs_'.$cnt.'_icon'] = $tab['icon'];
                            $post_data['meta_input']['_tabs_'.$cnt.'_title'] = 'field_589daa24ef33c';
                            $post_data['meta_input']['tabs_'.$cnt.'_title'] = $tab['title'];
                            $post_data['meta_input']['_tabs_'.$cnt.'_aroma'] = 'field_589daa24ef33e';
                            $post_data['meta_input']['tabs_'.$cnt.'_aroma'] = $tab['aroma'];
                            $post_data['meta_input']['_tabs_'.$cnt.'_flavor'] = 'field_589daa52ef33f';
                            $post_data['meta_input']['tabs_'.$cnt.'_flavor'] = $tab['flavor'];

                        }

                        $cnt++;
                    }
                    $post_data['meta_input']['_tabs'] = 'field_586bb209b118a';
                    $post_data['meta_input']['tabs'] = serialize( $types );
                }
            }

            // Default status
            $post_data['post_status'] = 'draft';
            $post_data['meta_input']['sync_post_status'] = 1;

            // Insert/update post
            $inserted_id = wp_insert_post( $post_data );

            if ( $type == 'full' ) {
                // Set category
                if ( $inserted_id > 0 ) {
                    wp_set_post_terms( $inserted_id, $product['category'], 'product_category' );
                }

                // Set image
                if ( $product['photo'] ) {
                    $thumbnail_id = get_post_thumbnail_id( $inserted_id );
                    if ( $thumbnail_id )
                        $photo_update = get_post_meta( $thumbnail_id, 'photo_update', true );
                    else
                        $photo_update = null;

                    // If image has been updated since last time, import it
                    if ( !$photo_update || ($product['photo_update'] && strtotime( $photo_update ) < strtotime( $product['photo_update'] )) ) {

                        // Delete previous one
                        if ( $thumbnail_id ) {
                            wp_delete_attachment( $thumbnail_id );
                        }

                        // Get the path to the upload directory.
                        $wp_upload_dir = wp_upload_dir();

                        // $filename should be the path to a file in the upload directory.
                        $filename = $wp_upload_dir['basedir'].'/import/thumbnail_'.$inserted_id.'.jpg';

                        // Copy image
                        $copy_result = copy(stripslashes($product['photo']), $filename);

                        // The ID of the post this attachment is for.
                        $parent_post_id = $inserted_id;

                        // Check the type of file. We'll use this as the 'post_mime_type'.
                        $filetype = wp_check_filetype( basename( $filename ), null );

                        // Prepare an array of post data for the attachment.
                        $attachment = array(
                            'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
                            'post_mime_type' => $filetype['type'],
                            'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
                            'post_content'   => '',
                            'post_status'    => 'inherit'
                        );

                        // Insert the attachment.
                        $attach_id = wp_insert_attachment( $attachment, $filename, $parent_post_id );

                        // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
                        require_once( ABSPATH . 'wp-admin/includes/image.php' );

                        // Generate the metadata for the attachment, and update the database record.
                        $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
                        wp_update_attachment_metadata( $attach_id, $attach_data );
                        update_post_meta( $attach_id, 'photo_update', $product['photo_update'] );

                        set_post_thumbnail( $parent_post_id, $attach_id );
                    }
                } else {
                    if ( $thumbnail_id = has_post_thumbnail( $inserted_id ) ) {
                        delete_post_meta( $inserted_id, '_thumbnail_id' );
                        wp_delete_attachment( $thumbnail_id );
                    }
                }
            }
        }

        // Return remaining products
        return $products_data;
    }


    /**
     * Get products data
     *
	 * @since    1.0.0
	 * @access   private
     */
    private function get_products_data() {
    	if ( !isset( $this->products_data ) ) {
    		global $wpdb;

    		$results = $wpdb->get_results( $wpdb->prepare( "
		        SELECT p.ID, p.post_title, p.post_status, pm.meta_value, p.post_modified FROM {$wpdb->postmeta} pm
		        LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
		        WHERE pm.meta_key = '%s'
		        AND p.post_type = '%s'
		    ", 'product_id', 'product' ));

    		foreach ( $results as $result )
    			$this->products_data[$result->meta_value] = $result;
		}

		return $this->products_data;
    }

    /**
     * Disable products that are no longer in feed
     *
     * @since    1.0.0
     * @access   private
     */
    function disable_products( $products ) {
        foreach( $products as $product ) {
            $post_id= $product->ID;

            update_post_meta( $post_id, 'sync_post_status', 1 );
        }
    }

    /**
     * Get post_id for given product
     *
	 * @since    1.0.0
	 * @access   private
     */
    private function get_post_id( $product_id ) {
    	$data = $this->get_products_data();

		// Get post_id
		if ( isset( $data[$product_id] ) ) {
			return (int)$data[$product_id]->ID;
		} else {
			return false;
		}
    }



}


