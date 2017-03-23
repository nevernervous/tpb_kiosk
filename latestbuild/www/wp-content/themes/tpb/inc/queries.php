<?php

/* =============================================================================
   TEMPLATE QUERIES
   ========================================================================== */

	/**
	 * Get products
	 */
	function tpb_get_products() {
		$args = array(
			'posts_per_page' => -1,
			'post_type' => 'product',
			'order' => 'ASC',
			'orderby' => 'title'
		);

		$products = new WP_Query( $args );

		return $products;
	}


	/**
	 * Get objects
	 */
	function tpb_get_objects() {
		$markers = get_field( 'markers', 'options' );

		$products = array();
		foreach( $markers as $marker ) {
			if ( $marker['pattern'] && $marker['product'] ) {
				$products[$marker['pattern']] = array(
					'ID' 	=> $marker['product']->ID,
					'url' 	=> get_permalink( $marker['product']->ID )
				);
			}
		}

		return $products;
	}

	/**
	 * Get product pattern
	 */
	function tpb_get_pattern( $product_id ) {
		$products = tpb_get_objects();

		foreach( $products as $pattern => $product ) {
			if ( $product['ID'] == $product_id ) {
				return $pattern;
			}
		}

		return false;
	}


	/**
	 * Get categories
	 */
	function tpb_get_categories() {
		$args = array(
			'orderby' => 'name'
		);

		$categories = get_terms( 'product_category', $args );

		foreach( $categories as &$category ) {
			$args = array(
				'order' => 'ASC',
				'orderby' => 'title',
				'post_type' => 'product',
				'posts_per_page' => -1,
				'tax_query' => array(
					array(
						'taxonomy' => 'product_category',
						'field'    => 'term_id',
						'terms'    => array( $category->term_id ),
					)
				),
			);

			$products = get_posts( $args );

			$category->products = $products;
		}

		usort( $categories, 'tpb_categories_usort' );

		return $categories;
	}


	/**
	 * Get product for add to cart screen
	 */
	function tpb_get_add_to_cart_product() {
		if (!isset( $_REQUEST['product_id'] ))
			return false;

		$id = (int)$_REQUEST['product_id']	;
		$product = get_post( $id );

		if ( $product ) {
        	$product->unit_price = get_post_meta( $product->ID, 'price', true );

			return $product;
		} else {
			return false;
		}
	}


	/**
	 * Get cart
	 */
	function tpb_get_cart() {
        // Get cart
        if ( !isset( $_SESSION['cart'] ) || !$_SESSION['cart'] )
        	return false;

        $cart = $_SESSION['cart'];
        $total = 0;

        // Set lines
        foreach( $cart as $product_id => $amounts) {
       		foreach( $amounts as $amount => $qty) {
       			$product = get_post( $product_id );
       			$prices = tbp_get_product_prices( $product_id );
       			$line_price = $prices[$amount]->price * $qty;

       			$lines[] = array(
       				'product' => $product,
       				'qty' => $qty,
       				'amount' => $amount,
       				'prices' => $prices,
       				'line_price' => $line_price
       			);

	        	$total += $line_price;
	        }
        }

        $return = array(
        	'lines' 	=> $lines,
        	'total' 	=> $total,
        	'count' 	=> count( $lines )
        );

        return $return;
	}


	/**
	 * Get product prices
	 */
	function tbp_get_product_prices( $product_id ) {
		$prices = get_post_meta( $product_id, 'prices', true );
		if ( !$prices )
			return false;

		$prices = json_decode( $prices );

		return $prices;
	}


	/**
	 * Get similar products
	 */
	function tpb_get_similar_products( $product_id ) {
		$args = array(
			'posts_per_page' => -1,
			'post_type' => 'product',
			'order' => 'ASC',
			'orderby' => 'title'
		);

		$meta_query = array();

		// Filter categories
		if ( $categories = get_the_terms( $product_id, 'product_category' ) ) {
			$slugs = array();
			foreach( $categories as $category ) {
				$slugs[] = $category->slug;
			}

			$args['tax_query'] = array(
				array(
					'taxonomy' => 'product_category',
					'field'    => 'slug',
					'terms'    => $slugs,
				),
			);
		}

		// Filter prices
		if ( $default_price = get_post_meta( $product_id, 'default_price', true ) ) {
			$percentage = (int)get_field( 'similar_cost', 'options' );
			if ( !$percentage )
				$percentage = 0.2;
			else
				$percentage = $percentage/100;

			$meta_query[] = array(
				'key'     => 'default_price',
				'value'   => array( $default_price*(1-$percentage), $default_price*(1+$percentage) ),
				'compare' => 'BETWEEN',
			);
		}

		// Filter types
		if ( $type = get_post_meta( $product_id, 'type', true ) ) {
			$meta_query[] = array(
				'key'     => 'type',
				'value'   => $type,
				'compare' => '=',
			);
		}

		if ( count($meta_query) > 1 ) {
			$meta_query['relation']	 = 'AND';
		}

		$args['meta_query'] = $meta_query;

		// Get products
		$products = get_posts( $args );

		return $products;
	}


	/**
	 * Get filters for given category
	 */
	function tpb_get_filters( $term_id = null, $products = null ) {
		global $wpdb;

		if ( $term_id != null ) {
			// Get posts
			$post_ids = $wpdb->get_col( $wpdb->prepare(
				"
				SELECT      object_id
				FROM        $wpdb->term_relationships
				WHERE       term_taxonomy_id = %d
				",
				$term_id
			) );
		} else if ( $products != null ) {
			$post_ids = array();

			foreach( $products as $product )
				$post_ids[] = $product->ID;
		} else {
			return false;
		}

		if ( $post_ids ) {
			// Get types
			$types = $wpdb->get_col( $wpdb->prepare(
				"
				SELECT      DISTINCT meta_value
				FROM        $wpdb->postmeta
				WHERE       meta_key = %s
							AND post_id IN (".implode( ',', $post_ids).")
				",
				'type',
				implode( ',', $post_ids)
			) );

			return $types;
		} else {
			return false;
		}

	}



/* =============================================================================
   ACTIONS
   ========================================================================== */

	// ...