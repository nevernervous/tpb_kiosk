<?php

	// Session start
	if ( session_id() == '' )
		session_start();


/* =============================================================================
   CLEAN HEAD
   ========================================================================== */

	/**
	 * Remove useless actions
	 */
	remove_action( 'wp_head',			'wp_generator' );
	remove_action( 'wp_head',			'wlwmanifest_link' );
	remove_action( 'wp_head',			'feed_links' );
	remove_action( 'wp_head',			'feed_links_extra' );
	remove_action( 'wp_head',			'rsd_link' );
	remove_action( 'wp_head',			'adjacent_posts_rel_link_wp_head' );
	remove_action( 'wp_head', 			'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 	'print_emoji_styles' );


	/**
	 * Remove comments feed link
	 */
	function tpb_remove_comments_feed_link() {
		return null;
	}
	add_filter( 'post_comments_feed_link', 'tpb_remove_comments_feed_link' );


	/**
	 * Remove comments inline CSS
	 */
	function tpb_remove_recent_comments_style() {
		global $wp_widget_factory;
		remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
	}
	add_action( 'widgets_init', 			'tpb_remove_recent_comments_style' );



/* =============================================================================
   INIT THEME
   ========================================================================== */

	/**
	 * Remove useless items from menu
	 */
	function tpb_clean_admin_menu() {
		global $menu, $submenu;

		// if ( get_current_user_id() !== 1 ) {
			// Remove default menu items
			unset( $menu[2] );    // Dashboard
			unset( $menu[5] );    // Posts
			// unset( $menu[10] );   // Medias
			unset( $menu[15] );   // Links
			unset( $menu[20] );   // Pages
			unset( $menu[25] );   // Comments
			unset( $menu[60] );   // Appearance
			unset( $menu[65] );   // Plugins
			unset( $menu[70] );   // Users
			unset( $menu[75] );   // Tools
			unset( $menu[80] );   // Settings

			// Remove default submenu items
			unset( $submenu['index.php'][10] );   // Updates submenu

			// Remove plugins menu items
			//unset( $menu[26] );  // Contact form
			// unset( $menu['80.025'] );  // ACF
		// }
	}
	add_action( 'admin_menu', 'tpb_clean_admin_menu', 999 );


	/**
	 * Remove useless meta boxes from dashboard
	 */
	function tpb_clean_dashboard() {
		global $wp_meta_boxes;

		// Remove dashboard blocks
		unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now'] );
		unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity'] );

		// Remove admin sidebar
		unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_primary'] );
		unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press'] );
	}
	add_action( 'wp_dashboard_setup', 'tpb_clean_dashboard' );


	/**
	 * Remove useless meta boxes from edit pages
	 */
	function tpb_clean_metaboxes() {
		// Remove useless metabox
		//remove_post_type_support( 'page', 'author' );
		//remove_post_type_support( 'page', 'excerpt' );
		//remove_post_type_support( 'page', 'comments' );
		//remove_post_type_support( 'page', 'custom-fields' );
		//remove_post_type_support( 'page', 'trackbacks' );
	}
	add_action( 'init', 'tpb_clean_metaboxes' );


	/**
	 * Enqueue scripts and styles for frontend
	 */
	function tpb_enqueue_scripts() {
		// Vendor JS
		wp_deregister_script( 'jquery' );
		wp_enqueue_script( 'jquery', get_bloginfo( 'template_directory' ) . '/js/jquery-3.1.1.min.js', false, 1, true );
		wp_enqueue_script( 'jquery-touchswipe', get_bloginfo( 'template_directory' ) . '/js/jquery.touchSwipe.min.js', array( 'jquery' ), 1, true );
		wp_enqueue_script( 'tweenmax', get_bloginfo( 'template_directory' ) . '/js/TweenMax.min.js', false, 1, true );
		wp_enqueue_script( 'tweenmax-cssplugin', get_bloginfo( 'template_directory' ) . '/js/CSSPlugin.min.js', false, 1, true );
		wp_enqueue_script( 'tweenmax-drawsvgplugin', get_bloginfo( 'template_directory' ) . '/js/DrawSVGPlugin.min.js', false, 1, true );
		wp_enqueue_script( 'fancybox', get_bloginfo( 'template_directory' ) . '/js/jquery.fancybox.pack.js', false, 1, true );
		wp_enqueue_script( 'keyboard', get_bloginfo( 'template_directory' ) . '/js/jquery.onScreenKeyboard.min.js', false, 1, true );
		wp_enqueue_script( 'fss', get_bloginfo( 'template_directory' ) . '/js/fss.min.js', false, 1, true );
		wp_enqueue_script( 'oor', get_bloginfo( 'template_directory' ) . '/js/oor.js', false, 1, true );

		// Custom JS
		wp_enqueue_script( 'plugins', get_bloginfo( 'template_directory' ) . '/js/plugins.js', array( 'jquery' ), 1, true );
		wp_enqueue_script( 'main-script', get_bloginfo( 'template_directory' ) . '/js/script.js', array( 'jquery', 'tweenmax' ), 1, true );
		wp_localize_script( 'main-script', 'WRK', array( 'host' => trim( get_home_url(), '/' ), 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

		// Vendor CSS
		wp_enqueue_style( 'googlefonts', 'https://fonts.googleapis.com/css?family=Open+Sans:700|Raleway:100,200,300,500,600,700,800|Vollkorn', false, 1, 'all' );
		wp_enqueue_style( 'normalize', get_bloginfo( 'template_directory' ) . '/css/normalize.css', false, 1, 'all' );
		wp_enqueue_style( 'fancybox', get_bloginfo( 'template_directory' ) . '/css/jquery.fancybox.css', false, 1, 'all' );
		wp_enqueue_style( 'fontawesome', get_bloginfo( 'template_directory' ) . '/css/font-awesome.min.css', false, 1, 'all' );
		wp_enqueue_style( 'keyboard', get_bloginfo( 'template_directory' ) . '/css/onScreenKeyboard.css', false, 1, 'all' );

		// Custom CSS
		wp_enqueue_style( 'main-style', get_bloginfo( 'template_directory' ) . '/style.css', false, 1, 'all' );
	}
	add_action( 'wp_enqueue_scripts', 'tpb_enqueue_scripts' );


	/**
	* Register stuffs and remove useless metabox
	*/
	function tpb_init() {
		tpb_load_customs();
	}
	add_action( 'init', 'tpb_init', 0 );


	/**
	 * Register Theme Features
	 */
	function tpb_theme_features()  {
		add_theme_support( 'post-thumbnails' );

        // Image sizes
        add_image_size( 'avatar', 80, 80, true );
        add_image_size( 'featured-large', 540, 600, true );
        add_image_size( 'featured-small', 200, 200, true );
        add_image_size( 'featured-logo', 390, 130, true );
        add_image_size( 'featured-image', 390, 190, true );
	}
	add_action( 'after_setup_theme', 'tpb_theme_features' );



/* =============================================================================
   INCLUDE EXTERNALS CLASSES
   ========================================================================== */

	/**
	 * Queries
	 */
	require_once(STYLESHEETPATH . '/inc/queries.php');



/* =============================================================================
   ACTIONS
   ========================================================================== */

	/**
	 * Add custom pages
	 */
	function tpb_custom_pages() {
		$screen = trim(parse_url(add_query_arg(array()), PHP_URL_PATH), '/');
		$screens = array( 'catalogue', 'select', 'add-to-cart', 'checkout', 'confirm-add-to-cart' );

		if ( in_array( $screen, $screens ) && ( $load = locate_template( 'screen-'.$screen.'.php', true ) ) )
			exit();
	}
	add_action( 'init', 'tpb_custom_pages' );


	/**
	 * Count cart
	 */
	function tpb_count_cart() {
        $cart = $_SESSION['cart'];

        $total = 0;
        foreach( $cart as $product ) {
        	$total += count($product);
        }

        return $total;
	}


    /**
     * Ajax add to cart
     */
    function tpb_ajax_add_to_cart() {
        $product_id = (int)$_REQUEST['product_id'];
        $qty = (int)$_REQUEST['qty'];
        $amount = (int)$_REQUEST['amount'];

        if ( !$product_id )
            die('0');

        if ( !$qty )
        	$qty = 1;

        if ( !$amount )
        	$amount = 0;

        // Get cart
        if ( isset( $_SESSION['cart'] ) )
        	$cart = $_SESSION['cart'];
        else
        	$cart = array();

        // Add product
        if ( isset( $cart[$product_id] ) && isset( $cart[$product_id][$amount] ) )
	        	$cart[$product_id][$amount] += $qty;
        else
        	$cart[$product_id][$amount] = $qty;

        // Save cart
        $_SESSION['cart'] = $cart;

        echo json_encode( array(
            'count_cart' => tpb_count_cart(),
            'success' => 1
        ) );

        die();
    }
    add_action( 'wp_ajax_tpb_add_to_cart', 'tpb_ajax_add_to_cart' );
    add_action( 'wp_ajax_nopriv_tpb_add_to_cart', 'tpb_ajax_add_to_cart' );


    /**
     * Ajax update cart
     */
    function tpb_ajax_update_cart() {
    	// Get products
        $products = $_REQUEST['products'];

        // Create cart
        $cart = array();
        foreach( $products as $product ) {
        	$cart[(int)$product['product_id']][(int)$product['amount']] = (int)$product['qty'];
        }

        // Save cart
        $_SESSION['cart'] = $cart;

        echo json_encode( array(
            'count_cart' => tpb_count_cart(),
            'success' => 1
        ) );
        die();
    }
    add_action( 'wp_ajax_tpb_update_cart', 'tpb_ajax_update_cart' );
    add_action( 'wp_ajax_nopriv_tpb_update_cart', 'tpb_ajax_update_cart' );


    /**
     * Ajax set user name
     */
    function tpb_ajax_handle_checkout() {
    	// Get products
        $user_name = sanitize_user( $_REQUEST['user_name'] );

        if ( $user_name == '')
        	die('0');

        // Save user name
        $_SESSION['user_name'] = $user_name;

        // Send order
        $result = Tpb_Wp_Pos_Public::send_order();

        if ( $result ) {
	        echo json_encode( array(
	            'user_name' => $user_name,
	            'success' => 1
	        ) );
        	die();
	    } else {
	    	die('0');
	    }
    }
    add_action( 'wp_ajax_tpb_handle_checkout', 'tpb_ajax_handle_checkout' );
    add_action( 'wp_ajax_nopriv_tpb_handle_checkout', 'tpb_ajax_handle_checkout' );


    /**
     * Ajax reset session
     */
    function tpb_ajax_reset_session() {
    	if ( session_destroy() ) {
    		$_SESSION = array();

	        echo json_encode( array(
	            'success' => 1
	        ) );
        	die();
    	} else {
    		die('0');
    	}
    }
    add_action( 'wp_ajax_tpb_reset_session', 'tpb_ajax_reset_session' );
    add_action( 'wp_ajax_nopriv_tpb_reset_session', 'tpb_ajax_reset_session' );





/* =============================================================================
   FILTERS
   ========================================================================== */

	/**
	 * Custom loader for wpcf7
	 */
	function tpb_wpcf7_ajax_loader() {
		return get_bloginfo( 'template_directory' ) . '/img/ajax-loader.gif';
	}
	add_filter( 'wpcf7_ajax_loader', 'tpb_wpcf7_ajax_loader');


	/**
	 * Define excerpt length
	 */
	function tpb_excerpt_length( $length ) {
		return 20;
	}
	add_filter( 'excerpt_length', 'tpb_excerpt_length', 999 );



/* =============================================================================
   HELPERS
   ========================================================================== */

	/**
	 * Load php files in inc/custom
	 */
	function tpb_load_customs() {
		$dir = STYLESHEETPATH . '/inc/custom/';
		$files = scandir( $dir );

		foreach( $files as $file ) {
			if ( substr( $file, -3, 3 ) == 'php' )
				require_once( $dir . $file );
		}
	}


	/**
	 * Add nth classes
	 */
	function aw_nth_class( $nths, $i ) {
		$classes = array();

		foreach ( $nths as $nth ) {
			if ( $i%$nth === 0) {
				$classes[] = 'nth-' . $nth;
			}
		}

		return implode( ' ', $classes );
	}


	/**
	 * Include pagination template
	 */
	function aw_pagination( $pagination_id ) {
		global $wp_query;

		include( 'pagination.php' );
	}


	/**
	 * Get sharing url for given network
	 */
	function aw_sharing_url( $network, $url, $text, $thumbnail = false ) {
		// Encode params
		$url  = urlencode( $url );
		$text = urlencode( $text );

		if ( $thumbnail ) {
			$thumbnail = urlencode( $thumbnail );
		}

		// Networks url
		$networks = array(
			'google'    => 'https://plus.google.com/share?url='.$url,
			'facebook'  => 'http://www.facebook.com/sharer/sharer.php?u='.$url.'&amp;t='.$text,
			'twitter'   => 'https://twitter.com/intent/tweet?text='.$text.'&amp;url='.$url,
			'linkedin'  => 'https://www.linkedin.com/cws/share?url='.$url.'&amp;token=&amp;isFramed=true',
			'pinterest' => 'http://pinterest.com/pin/create/button/?url='.$url.'&amp;media='.$thumbnail.+'&amp;description='.$text
		);

		return isset( $networks[$network] ) ? $networks[$network] : false;
	}


	/**
	 * Return text truncated after n characters
	 */
	function aw_truncate( $text, $chars = 80 ) {
		$text = substr( $text, 0, $chars );
		$text = substr( $text, 0, strrpos( $text, ' ' ) );
		return $text;
	}


	/**
	 * Include inline SVG
	 */
	function inline_svg( $filename, $alt = null, $key = null, $return = false ) {
		$file = STYLESHEETPATH . '/img/svg/' . $filename . '.svg';

		if ( file_exists( $file ) ) {
			$svg = file_get_contents( $file );

			// Clean SVG
			$cleaned_svg = aw_clean_svg( $svg, $key );
			if ( $cleaned_svg ) {
				$output = '<span class="svg svg-' . $filename . ($key?' svg-' . $key:'') .'">';
				$output .= $cleaned_svg;
				$output .= '<span class="alt sr-only">' . $alt . '</span>';
				$output .= '</span>';

				if ( $return )
					return $output;
				else
					echo $output;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}


	/**
	 * Clean SVG file
	 */
	$svg_key = 1;
	function aw_clean_svg( $svg, $key = null ) {
		global $svg_key;

		// Keep only <svg> tag
		preg_match( '~<svg(.*?)</svg>~si', $svg, $matches );

		if ( $matches ) {
			$cleaned_svg = $matches[0];
			if ( $key == null )
				$key = 'svg'.$svg_key.'-';
			else
				$key .= '-';

			// Unique ID
			preg_match_all( '~id="(.*?)"~i', $cleaned_svg, $matches );
			foreach ( $matches[1] as $old_id ) {
				$new_id = $key.$old_id;

				$cleaned_svg = str_replace( '"'.$old_id.'"', '"'.$new_id.'"', $cleaned_svg );
				$cleaned_svg = str_replace( '#'.$old_id, '#'.$new_id, $cleaned_svg );
			}

			// Add <defs>
			$cleaned_svg = str_replace( '<radialGradient', '<defs><radialGradient', $cleaned_svg );
			$cleaned_svg = str_replace( '</radialGradient>', '</radialGradient></defs>', $cleaned_svg );

			$cleaned_svg = preg_replace('/<g>\r\n<\/g>/', '', $cleaned_svg);
			$cleaned_svg = preg_replace('/\r\n\r\n/', '', $cleaned_svg);

			$svg_key++;
			return $cleaned_svg;
		}

		return false;
	}


	/**
	 * Get oembed video datas
	 */
	function tpb_get_oembed_video( $video_url ) {
		if ( !$video_url )
			return false;

		$oembed = _wp_oembed_get_object();

		$provider = $oembed->get_provider( $video_url );
		$data = $oembed->fetch( $provider, $video_url );

		if ( !$data )
			return false;

		preg_match('/src="([^"]+)"/', $data->html, $match);
		$embed_url = $match[1];

		// $data->video_url = $video_url;
		$data->video_url = $embed_url.'&autoplay=1';

		return $data;
	}


	/**
	 * Sort categories
	 */
	function tpb_categories_usort( $a, $b ) {
		$order = array( 'flowers', 'accessories', 'concentrates', 'edibles', 'other' );

		if (!in_array( $a->slug, $order ) || !in_array( $b->slug, $order ) )
			return 0;

		$a_key = array_search( $a->slug, $order );
		$b_key = array_search( $b->slug, $order );

	    return ($a_key < $b_key) ? -1 : 1;
	}


	/**
	 * Sort prices
	 */
	function tpb_prices_usort( $a, $b ) {
		$order = array( 'flowers', 'accessories', 'concentrates', 'edibles', 'other' );

		if ( $a['price'] == $b['price'] )
			return 0;

	    return ($a['price'] < $b['price']) ? -1 : 1;
	}


	/**
	 * Trim text
	 */
	function trim_text($input, $length, $ellipses = true, $strip_html = true) {
	    //strip tags, if desired
	    if ($strip_html) {
	        $input = strip_tags($input);
	    }

	    //no need to trim, already shorter than trim length
	    if (strlen($input) <= $length) {
	        return $input;
	    }

	    //find last space within length
	    $last_space = strrpos(substr($input, 0, $length), ' ');
	    $trimmed_text = substr($input, 0, $last_space);

	    //add ellipses (...)
	    if ($ellipses) {
	        $trimmed_text .= '...';
	    }

	    return $trimmed_text;
	}


	/**
	 * Check if product is flower
	 */
	function tpb_is_flower( $post_id ) {
		$categories = get_the_terms( $post_id, 'product_category' );

		$is_flower = false;
		foreach( $categories as $category )
			if ( $category->name == 'Flowers' )
				$is_flower = true;

		return $is_flower;
	}



/* =============================================================================
   ACF
   ========================================================================== */

	/**
	 * Customize ACF path
	 */
	function tpb_acf_settings_path( $path ) {

	    // update path
	    $path = get_stylesheet_directory() . '/inc/acf/';

	    // return
	    return $path;

	}
	add_filter( 'acf/settings/path', 'tpb_acf_settings_path' );


	/**
	 * Customize ACF dir
	 */
	function tpb_acf_settings_dir( $dir ) {

	    // update path
	    $dir = get_stylesheet_directory_uri() . '/inc/acf/';

	    // return
	    return $dir;

	}
	add_filter('acf/settings/dir', 'tpb_acf_settings_dir');


	/**
	 * Include ACF
	 */
	include_once( get_stylesheet_directory() . '/inc/acf/acf.php' );
	// add_filter( 'acf/settings/show_admin', '__return_false' );


    /**
     * Option page
     */
    if ( function_exists( 'acf_add_options_page'  ) )
        acf_add_options_page( 'UI settings' );