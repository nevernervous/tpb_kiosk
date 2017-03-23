<?php

/* =============================================================================
   POST TYPES
   ========================================================================== */

	// Register Custom Post Type
	function tpb_wp_pos_product() {

		$labels = array(
			'name'                  => _x( 'Products', 'Post Type General Name', 'tpb' ),
			'singular_name'         => _x( 'Product', 'Post Type Singular Name', 'tpb' ),
			'menu_name'             => __( 'Products', 'tpb' ),
			'name_admin_bar'        => __( 'Product', 'tpb' ),
			'archives'              => __( 'Product Archives', 'tpb' ),
			'attributes'            => __( 'Product Attributes', 'tpb' ),
			'parent_item_colon'     => __( 'Parent Product:', 'tpb' ),
			'all_items'             => __( 'All Products', 'tpb' ),
			'add_new_item'          => __( 'Add New Product', 'tpb' ),
			'add_new'               => __( 'Add New', 'tpb' ),
			'new_item'              => __( 'New Product', 'tpb' ),
			'edit_item'             => __( 'Edit Product', 'tpb' ),
			'update_item'           => __( 'Update Product', 'tpb' ),
			'view_item'             => __( 'View Product', 'tpb' ),
			'view_items'            => __( 'View Products', 'tpb' ),
			'search_items'          => __( 'Search Product', 'tpb' ),
			'not_found'             => __( 'Not found', 'tpb' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'tpb' ),
			'featured_image'        => __( 'Featured Image', 'tpb' ),
			'set_featured_image'    => __( 'Set featured image', 'tpb' ),
			'remove_featured_image' => __( 'Remove featured image', 'tpb' ),
			'use_featured_image'    => __( 'Use as featured image', 'tpb' ),
			'insert_into_item'      => __( 'Insert into Product', 'tpb' ),
			'uploaded_to_this_item' => __( 'Uploaded to this product', 'tpb' ),
			'items_list'            => __( 'Products list', 'tpb' ),
			'items_list_navigation' => __( 'Products list navigation', 'tpb' ),
			'filter_items_list'     => __( 'Filter products list', 'tpb' ),
		);
		$args = array(
			'label'                 => __( 'Product', 'tpb' ),
			'description'           => __( 'Products', 'tpb' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'thumbnail', 'custom-fields' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'page',
		);
		register_post_type( 'product', $args );

	}



/* =============================================================================
   TAXONOMIES
   ========================================================================== */


	// Register Custom Taxonomy
	function tpb_wp_pos_product_category() {

		$labels = array(
			'name'                       => _x( 'Categories', 'Taxonomy General Name', 'tpb' ),
			'singular_name'              => _x( 'Category', 'Taxonomy Singular Name', 'tpb' ),
			'menu_name'                  => __( 'Category', 'tpb' ),
			'all_items'                  => __( 'All categories', 'tpb' ),
			'parent_item'                => __( 'Parent Category', 'tpb' ),
			'parent_item_colon'          => __( 'Parent Category:', 'tpb' ),
			'new_item_name'              => __( 'new Category', 'tpb' ),
			'add_new_item'               => __( 'Add Category', 'tpb' ),
			'edit_item'                  => __( 'Edit Category', 'tpb' ),
			'update_item'                => __( 'Update Category', 'tpb' ),
			'view_item'                  => __( 'View Category', 'tpb' ),
			'separate_items_with_commas' => __( 'Separate categories with commas', 'tpb' ),
			'add_or_remove_items'        => __( 'Add or remove categories', 'tpb' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'tpb' ),
			'popular_items'              => __( 'Popular Categories', 'tpb' ),
			'search_items'               => __( 'Search Categories', 'tpb' ),
			'not_found'                  => __( 'Not Found', 'tpb' ),
			'no_terms'                   => __( 'No categories', 'tpb' ),
			'items_list'                 => __( 'Categories list', 'tpb' ),
			'items_list_navigation'      => __( 'Categories list navigation', 'tpb' ),
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => false,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => true,
		);
		register_taxonomy( 'product_category', array( 'product' ), $args );

	}


	// Filter products per category in admin
	function tpb_add_taxonomy_filters() {
		global $typenow;

		$taxonomies = array('product_category');

		if( $typenow == 'product' ) {
			foreach( $taxonomies as $tax_slug ) {
				$tax_obj = get_taxonomy( $tax_slug );
				$tax_name = $tax_obj->labels->name;
				$terms = get_terms( $tax_slug );
				if( count( $terms ) > 0 ) {
					echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
					echo "<option value=''>Show All $tax_name</option>";
					foreach( $terms as $term ) {
						echo '<option value='. $term->slug, $_GET[$tax_slug] == $term->slug ? ' selected="selected"' : '','>' . $term->name .' (' . $term->count .')</option>';
					}
					echo "</select>";
				}
			}
		}
	}