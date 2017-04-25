<?php global $category, $is_active; ?>
<div class="tab tab-catalogue <?php echo $is_active ? 'is-active':''; ?>">
	<div class="products-list">
		<h2 class="tab-title title-lg"><?php echo $category->name; ?></h2>

		<?php $filters = tpb_get_filters( $category->term_id ); ?>
		<div class="filters">
			<ul class="user-select list-filter selectable">
				<li class="option is-selected" data-value="all">
					All types
				</li>
				<?php foreach( $filters as $filter ): ?>
				<li class="option" data-value="<?php echo sanitize_title( $filter ); ?>">
					<?php echo $filter; ?>
				</li>
				<?php endforeach; ?>
			</ul>

			<ul class="user-select list-order selectable">
				<li class="option is-selected" data-value="price-asc">
					Price <i class="fa fa-sort-amount-asc" aria-hidden="true"></i>
				</li>
				<li class="option" data-value="price-desc">
					Price <i class="fa fa-sort-amount-desc" aria-hidden="true"></i>
				</li>
				<li class="option" data-value="name-asc">
					A-Z
				</li>
				<li class="option" data-value="name-desc">
					Z-A
				</li>
			</ul>
		</div><!-- .filters -->

		<?php if ( $category->products ): ?>
		<div class="scroller">
			<div class="products-outer">
				<div class="products clearfix">
					<?php /*for( $i=0; $i < 50; $i++):*/  foreach( $category->products as $product ): ?>

					<?php
						$prices = tbp_get_product_prices( $product->ID );
						$type = get_post_meta( $product->ID, 'type', true );
					?>
					<article class="product link-product" data-url="<?php echo get_the_permalink( $product->ID ); ?>" data-type="<?php echo sanitize_title( $type) ; ?>" data-price="<?php echo $prices[0]->price; ?>" data-name="<?php echo $product->post_title; ?>">
						<?php if ( has_post_thumbnail( $product->ID ) ): ?>
						<?php
							$thumbnail_id = get_post_thumbnail_id( $product->ID );
							$rounded_shape = (bool)get_post_meta( $thumbnail_id, 'rounded_shape', true );
							$image = wp_get_attachment_image_src( $thumbnail_id, 'thumbnail'.($rounded_shape ? '-preserved':''), false );
						?>
						<div class="product-image-thumb <?php echo $rounded_shape ? 'original-shape':''; ?>">
							<img src="<?php echo $image[0]/*.'?v='.rand()*/; ?>" alt="" width="<?php echo $image[1]; ?>" height="<?php echo $image[2]; ?>" class="image" />
						</div><!-- .product-image-thumb -->
						<?php else: ?>
						<div class="product-image-thumb blank">
							<span><?php _e( 'Image unavailable', 'tpb' ); ?></span>
						</div>
						<?php endif; ?>

						<h2 class="product-name">
							<?php echo $product->post_title; ?>

							<?php
								$infos = array();

								if ( $prices )
									$infos[] = '$'.number_format($prices[0]->price, 2).($prices[0]->unit?'<span>/'.$prices[0]->unit.'</span>':'');
								if ( $type )
									$infos[] = $type;
							 ?>
							<small>
								<?php echo implode( ' • ', $infos ); ?>
							</small>
						</h2>
					</article>
					<?php endforeach; /*endfor;*/ ?>
				</div><!-- .products -->
			</div><!-- .products-outer -->
		</div><!-- .scroller -->
		<?php else: ?>
		<?php _e( 'No products found.', 'tpb' ); ?>
		<?php endif; ?>
	</div><!-- .products-list -->
</div><!-- .tab -->