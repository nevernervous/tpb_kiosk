<div class="screen screen-catalogue" data-screen="catalogue">

	<div class="screen-tabs">
		<div class="tab tab-catalogue-home is-active">
			<h1 class="tab-title title-lg screen-title"><?php _e( 'Catalogue', 'tpb' ); ?></h1>

			<?php if ( $featured = get_field( 'featured', 'options' ) ): ?>
			<div class="catalogue-home">
				<div class="scroller">
					<div class="featured clearfix">
						<?php foreach( $featured as $ad ): ?>
						<div class="ad link-product <?php echo $ad['acf_fc_layout'] == 'large_banner' ? 'large':'small'; ?>" data-url="<?php echo get_the_permalink( $ad['product']->ID ); ?>">

							<div class="ad-inner">

								<?php if ( $ad['acf_fc_layout'] == 'large_banner' ): ?>

								<?php if ( $image = $ad['product_image'] ): ?>

								<div class="ad-image <?php echo $ad['image_position']; ?>">
									<img src="<?php echo $image['sizes']['featured-large']; ?>" alt="" width="<?php echo $image['sizes']['featured-large-width']; ?>" height="<?php echo $image['sizes']['featured-large-height']; ?>" />
								</div><!-- .ad-image -->

								<?php endif; ?>

								<div class="ad-body">

									<?php if ( $image = $ad['brand_logo'] ): ?>

									<div class="brand-logo">
										<img src="<?php echo $image['sizes']['featured-logo']; ?>" alt="" width="<?php echo $image['sizes']['featured-logo-width']; ?>" height="<?php echo $image['sizes']['featured-logo-height']; ?>" />
									</div><!-- .brand-logo -->

									<?php endif; ?>

									<?php if ( $image = $ad['discount_image'] ): ?>

									<div class="discount-image">
										<img src="<?php echo $image['sizes']['featured-image']; ?>" alt="" width="<?php echo $image['sizes']['featured-image-width']; ?>" height="<?php echo $image['sizes']['featured-image-height']; ?>" />
									</div><!-- .discount-image -->

									<?php elseif ( $ad['discount_text'] ): ?>

									<div class="discount-text">
										<?php echo $ad['discount_text']; ?>
									</div><!-- .discount-text -->

									<?php endif; ?>

									<?php if ( $ad['text'] ): ?>

									<div class="text">
										<?php echo $ad['text']; ?>
									</div><!-- .text -->

									<?php endif; ?>

								</div><!-- .ad-body -->

								<?php else: ?>

								<?php if ( $image = $ad['product_image'] ): ?>

								<div class="ad-image">
									<img src="<?php echo $image['sizes']['featured-small']; ?>" alt="" width="<?php echo $image['sizes']['featured-small-width']; ?>" height="<?php echo $image['sizes']['featured-small-height']; ?>" />
								</div><!-- .ad-image -->

								<?php endif; ?>

								<div class="ad-body">

									<div class="product-name">
										<?php echo $ad['product']->post_title; ?>
									</div><!-- .product-name -->


									<div class="text">
										<?php echo $ad['text']; ?>

										<div class="discount-text">
											<?php echo $ad['discount_text']; ?>
										</div><!-- .discount-text -->
									</div><!-- .text -->

								</div><!-- .ad-body -->

								<?php endif; ?>

							</div><!-- .ad-inner -->

						</div><!-- .ad -->
						<?php endforeach; ?>
					</div><!-- .featured -->
				</div><!-- .scroller -->
			</div><!-- .catalogue-home -->
			<?php endif; ?>
		</div><!-- .tab -->

		<?php if ( $categories = tpb_get_categories() ): ?>
		<?php $cnt=0; foreach( $categories as $category ): ?>
		<div class="tab tab-catalogue">
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
		<?php $cnt++; endforeach; ?>
		<?php endif; ?>
	</div><!-- .screen-tabs -->

	<div class="nav-tabs">
		<ul class="tabs">
			<li class="tab is-active">
				<button type="button" class="btn-tab">
					<i class="fa fa-file-text" aria-hidden="true"></i>
					<span class="label">Home</span>
				</button>
			</li>
			<?php foreach( $categories as $category ): ?>
			<li class="tab">
				<button type="button" class="btn-tab">
					<i class="fa <?php echo get_field( 'icon', 'product_category_'.$category->term_id ); ?>" aria-hidden="true"></i>
					<span class="label"><?php echo $category->name; ?></span>
				</button>
			</li>
			<?php endforeach; ?>
		</ul>
	</div><!-- .nav-tabs -->

</div><!-- .screen-catalogue -->