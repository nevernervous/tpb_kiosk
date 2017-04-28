<?php
	$product = tpb_get_add_to_cart_product();
	$prices = tbp_get_product_prices( $product->ID );
?>
<div class="screen screen-add-to-cart" data-screen="add-to-cart">
	<h2 class="title title-lg">
		<?php _e( 'Add to cart', 'tpb' ); ?>
	</h2>

	<?php if ( has_post_thumbnail( $product->ID ) ): ?>
	<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $product->ID ), 'medium', false ); ?>
	<div class="product-image">
		<img src="<?php echo $image[0]; ?>" alt="" width="<?php echo $image[1]; ?>" height="<?php echo $image[2]; ?>" class="image" />

		<div class="waves">
			<div class="wave"></div>
			<div class="wave"></div>
			<div class="wave"></div>
		</div><!-- .waves -->
	</div><!-- .product-image -->
	<?php endif; ?>

	<form class="form-add-to-cart qty-ancestor">
		<div class="product">
			<div class="block product-name title-sm">
				<?php echo $product->post_title; ?>
			</div><!-- .product-name -->

			<div class="block product-amounts">
				<div class="block-title">
					<?php _e( 'Amount' ); ?>
				</div><!-- .block-title -->

				<ul class="user-select select-amount <?php echo count($prices)>1 ? 'selectable':''; ?> in-line">
					<?php $cnt= 0; foreach( $prices as $price ): ?>
					<li class="option <?php echo ((!isset($_GET['price']) && $cnt==0) || ($_GET['price'] == $cnt)) ? 'is-selected"':''; ?>" data-value="<?php echo $cnt; ?>" data-price="<?php echo $price->price; ?>">
						<div class="price">
							<?php
								$display_price = '$'.number_format($price->price, 2);

								if ( get_field( 'tax', 'options' ) === false )
									$display_price.= '*';

								if ( $price->unit )
									$display_price .= '<span>/'.$price->unit.'</span>';

								echo $display_price;
							?>
						</div>
					</li>
					<?php $cnt++; endforeach; ?>
				</ul>
			</div><!-- .product-amounts -->

			<div class="block product-qty">
				<div class="block-title">
					<?php _e( 'Quantity' ); ?>
				</div><!-- .block-title -->

				<div class="btn-user btn-qty btn-minus">
					-

					<span class="hit"></span>
				</div>

				<div class="qty">
					<input type="text" name="qty" value="1" class="input-qty" />
				</div>

				<div class="btn-user btn-qty btn-plus">
					+

					<span class="hit"></span>
				</div>
			</div><!-- .product-qty -->
		</div><!-- .product -->

		<div class="actions">
			<input type="hidden" name="product_id" value="<?php echo $product->ID; ?>" />
			<input type="hidden" name="unit_price" value="<?php echo $product->unit_price; ?>" />

			<div class="btn-user btn-text btn-text-lg btn-cart-back is-hidden">
				<?php _e( 'Continue shopping', 'tpb' ); ?>

				<span class="hit"></span>
			</div>

			<div class="btn-user btn-text btn-text-xl btn-add-to-cart btn-submit">
				<span class="text-on">
					<span class="qty-price">

						<?php
							if ( !isset($_GET['price']) )
								$display_price = '$'.number_format($prices[0]->price, 2);
							else
								$display_price = '$'.number_format($prices[$_GET['price']]->price, 2);

							if ( get_field( 'tax', 'options' ) === false )
								$display_price.= '*';

							echo $display_price;
						?>
					</span>

					<span class="cta">
						<?php _e( 'Add to cart', 'tpb' ); ?>
					</span>
				</span><!-- .text-on -->

				<span class="text-off">
					<?php _e( 'Added', 'tpb' ); ?>

					<?php inline_svg( 'check' ); ?>
				</span><!-- .text-off -->

				<span class="hit"></span>
			</div>

			<div class="btn-user btn-text btn-text-xl btn-cart-checkout is-hidden">
				<?php _e( 'Checkout', 'tpb' ); ?>

				<span class="hit"></span>
			</div>
		</div><!-- .actions -->
	</form>

	<?php if ( get_field( 'tax', 'options' ) === false ): ?>
	<div class="note-footer">
		<?php _e( '* tax not included', 'tpb' ); ?>
	</div><!-- .note-footer -->
	<?php endif; ?>
</div><!-- .screen-add-to-cart -->