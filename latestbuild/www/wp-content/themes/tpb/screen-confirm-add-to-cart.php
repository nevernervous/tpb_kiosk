<?php
	// $product = tpb_get_add_to_cart_product();
?>
<div class="screen screen-confirm-add-to-cart" data-screen="confirm-add-to-cart">
	<h2 class="title title-md">
		<?php _e( 'Tap the product you would <br/>like to purchase', 'tpb' ); ?>
	</h2>

	<div class="products">

		<?php $table_product = get_post( (int)$_REQUEST['table_product_id'] ); ?>
		<article class="product btn-add-to-cart" data-id="<?php echo $table_product->ID; ?>">
			<?php if ( has_post_thumbnail( $table_product->ID ) ): ?>
			<?php
				$thumbnail_id = get_post_thumbnail_id( $table_product->ID );
				$rounded_shape = (bool)get_post_meta( $thumbnail_id, 'rounded_shape', true );
				$image = wp_get_attachment_image_src( $thumbnail_id, 'medium'.($rounded_shape ? '-preserved':''), false );
			?>
			<div class="product-image <?php echo $rounded_shape ? 'original-shape':''; ?>">
				<img src="<?php echo $image[0]; ?>" alt="" width="<?php echo $image[1]; ?>" height="<?php echo $image[2]; ?>" class="image" />

				<div class="waves">
					<div class="wave"></div>
					<div class="wave"></div>
					<div class="wave"></div>
				</div><!-- .waves -->
			</div><!-- .product-image -->
			<?php else: ?>
				<div class="product-image blank">
					<div class="waves">
						<div class="wave"></div>
						<div class="wave"></div>
						<div class="wave"></div>
					</div><!-- .waves -->
				</div><!-- .product-image -->
			<?php endif; ?>

			<h2 class="product-name">
				<?php echo $table_product->post_title; ?>
			</h2>

			<div class="info">
				<?php _e( 'Item placed on table', 'tpb' ); ?>
			</div><!-- .info -->
		</article>

		<?php $catalogue_product = get_post( (int)$_REQUEST['catalogue_product_id'] ); ?>
		<article class="product btn-add-to-cart" data-id="<?php echo $catalogue_product->ID; ?>">
			<?php if ( has_post_thumbnail( $catalogue_product->ID ) ): ?>
			<?php
				$thumbnail_id = get_post_thumbnail_id( $catalogue_product->ID );
				$rounded_shape = (bool)get_post_meta( $thumbnail_id, 'rounded_shape', true );
				$image = wp_get_attachment_image_src( $thumbnail_id, 'medium'.($rounded_shape ? '-preserved':''), false );
			?>
			<div class="product-image <?php echo $rounded_shape ? 'original-shape':''; ?>">
				<img src="<?php echo $image[0]; ?>" alt="" width="<?php echo $image[1]; ?>" height="<?php echo $image[2]; ?>" class="image" />

				<div class="waves">
					<div class="wave"></div>
					<div class="wave"></div>
					<div class="wave"></div>
				</div><!-- .waves -->
			</div><!-- .product-image -->
			<?php else: ?>
				<div class="product-image blank">
					<div class="waves">
						<div class="wave"></div>
						<div class="wave"></div>
						<div class="wave"></div>
					</div><!-- .waves -->
				</div><!-- .product-image -->
			<?php endif; ?>

			<h2 class="product-name">
				<?php echo $catalogue_product->post_title; ?>
			</h2>

			<div class="info">
				<?php _e( 'Viewing in the catalogue', 'tpb' ); ?>
			</div><!-- .info -->
		</article>

	</div><!-- .products -->

	<div class="tip">
		<?php _e( 'Slide the jar back down to cancel', 'tpb' ); ?>

		<div class="arrows">
			<div class="arrow"></div>
			<div class="arrow"></div>
			<div class="arrow"></div>
		</div><!-- .arrows -->
	</div><!-- .message -->
</div><!-- .screen-confirm-add-to-cart -->