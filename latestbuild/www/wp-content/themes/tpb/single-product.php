<div class="screen screen-product" data-product-id="<?php the_ID(); ?>" data-screen="product-<?php the_ID(); ?>" data-pattern="<?php echo tpb_get_pattern( get_the_ID() ); ?>">
	<div class="product-body">
		<div class="product-header">
			<h1 class="product-name title-md screen-title">
				<?php the_title(); ?>
			</h1><!-- .product-name -->

			<div class="product-subname">
				<?php if ( $brand = get_post_meta( get_the_ID(), 'brand', true ) ): ?>
				<i class="fa fa-tag" aria-hidden="true"></i> <?php echo $brand; ?>
				<?php endif; ?>

				<?php if ( $type = get_post_meta( get_the_ID(), 'type', true ) ): ?>
				<i class="fa fa-envira" aria-hidden="true"></i> <?php echo $type; ?>
				<?php endif; ?>
			</div><!-- .product-subname -->
		</div><!-- .product-header -->

		<?php if ( has_post_thumbnail() ): ?>
		<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'medium', false ); ?>
		<div class="product-image">
			<img src="<?php echo $image[0]; ?>" alt="" width="<?php echo $image[1]; ?>" height="<?php echo $image[2]; ?>" class="image" />

			<div class="waves">
				<div class="wave"></div>
				<div class="wave"></div>
				<div class="wave"></div>
			</div><!-- .waves -->
		</div><!-- .product-image -->
		<?php endif; ?>

		<?php if ( $prices = tbp_get_product_prices( get_the_ID() ) ): ?>
		<div class="product-add-to-cart">
			<div class="product-prices <?php echo count($prices) == 1 ? 'prices-simple':'prices-multiple'; ?>">
				<?php $cnt=0; foreach( $prices as $price ): ?>
				<div class="price btn-add-to-cart" data-value="<?php echo $cnt; ?>" data-id="<?php the_ID(); ?>">
					$<?php echo number_format($price->price, 2).($price->unit?'<small>/'.$price->unit.'</small>':''); ?>
				</div><!-- .price -->
				<?php $cnt++; endforeach; ?>
			</div><!-- .product-prices -->

			<div class="btn-user btn-text btn-text-lg btn-add-to-cart" data-id="<?php the_ID(); ?>">
				<?php _e( 'Add to cart', 'tpb' ); ?>

				<span class="hit"></span>
			</div>
		</div><!-- .product-add-to-cart -->
		<?php endif; ?>

		<?php if ( have_rows( 'tabs' ) ): ?>
		<div class="screen-tabs">
			<?php $cnt = 0; while ( have_rows( 'tabs' ) ) : the_row(); ?>
			<div class="tab tab-product tab-<?php echo get_row_layout(); ?> <?php echo $cnt == 0 ? 'is-active':''; ?>">

				<?php if ( get_row_layout() == 'highlights' ): ?>

				<div class="description">
					<?php echo get_sub_field( 'text' ); ?>
				</div><!-- .description -->

				<?php $bottom_border = false; ?>
				<div class="aside clearfix">
					<?php if ( tpb_is_flower( get_the_ID() ) ): ?>
					<div class="border has-logo">
						<div class="logo">
							<span class="label"><?php _e( 'Powered by', 'tpb' ); ?></span>
							<?php inline_svg( 'logo-potguide' ); ?>
						</div><!-- .logo -->
					</div><!-- .border -->
					<?php else: ?>
					<div class="border"></div>
					<?php endif; ?>

					<?php if ( $graphs = get_sub_field( 'graphs' ) ): $bottom_border = true; ?>
					<div class="graphs clearfix">
						<?php foreach( $graphs as $graph ): ?>
						<div class="graph">
							<div class="svg line">
								<svg xmlns="http://www.w3.org/2000/svg" width="170" height="170" viewBox="0 0 170 170">
								  <circle fill="none" stroke-width="10" stroke-miterlimit="10" cx="85" cy="85" r="80"/>
								  <circle fill="none" stroke-dasharray="502" stroke-dashoffset="<?php echo 502 - 502*$graph['percent']; ?>" stroke-width="10" stroke-miterlimit="10" cx="85" cy="85" r="80"/>
								</svg>
							</div><!-- .line -->

							<div class="title">
								<span><?php echo $graph['label']; ?></span> <small>%</small>
							</div><!-- .title -->

							<div class="values">
								<?php echo $graph['value']; ?>
							</div><!-- .values -->
						</div><!-- .graph -->
						<?php endforeach; ?>
					</div><!-- .graphs -->
					<?php endif; ?>

					<?php if ( $video = tpb_get_oembed_video( get_sub_field( 'video' ) ) ): $bottom_border = true; ?>
					<div class="video-container">
						<a href="<?php echo $video->video_url; ?>" class="btn-play link-video" target="_blank">
							<div class="thumbnail" style="background-image: url('<?php echo $video->thumbnail_url; ?>');">
								<div class="play"></div>
							</div>

							<span class="label">
								<?php echo $video->title; ?>
							</span><!-- .label -->
						</a>
					</div><!-- .video-container -->
					<?php endif; ?>

					<?php if ( $bottom_border ): ?>
					<div class="border"></div>
					<?php endif; ?>
				</div><!-- .aside -->

				<?php elseif ( get_row_layout() == 'text' ): ?>

				<div class="description">
					<?php echo get_sub_field( 'text' ); ?>
				</div><!-- .description -->

				<?php elseif ( get_row_layout() == 'flavor' ): ?>

				<div class="description">
					<?php if ( $aroma = get_sub_field( 'aroma' ) ): ?>
					<p>
						<b><?php _e( 'Aroma', 'tpb' ); ?></b><br/>
						<?php echo $aroma; ?>
					</p>
					<?php endif; ?>
					<?php if ( $flavor = get_sub_field( 'flavor' ) ): ?>
					<p>
						<b><?php _e( 'Flavor', 'tpb' ); ?></b><br/>
						<?php echo $flavor; ?>
					</p>
					<?php endif; ?>
				</div><!-- .description -->

				<?php elseif ( get_row_layout() == 'attributes' ): ?>

				<?php if ( $groups = get_sub_field( 'groups' ) ): ?>
				<div class="columns clearfix">
					<?php foreach( $groups as $group ): ?>
					<div class="column">
						<div class="column-title title-sm">
							<?php echo $group['title']; ?>
						</div><!-- .column-title -->

						<ul class="values">
							<?php foreach( $group['attributes'] as $attribute ): ?>
							<li class="value">
								<?php echo $attribute['label']; ?>

								<div class="bar <?php echo $attribute['color']; ?>">
									<div class="progress" style="width: <?php echo $attribute['percent']*100; ?>%;"></div>
								</div>
							</li>
							<?php endforeach; ?>
						</ul>
					</div><!-- .column -->
					<?php endforeach; ?>
				</div><!-- .columns -->
				<?php endif; ?>

				<?php elseif ( get_row_layout() == 'reviews' ): ?>

				<?php if ( $reviews = get_sub_field( 'reviews' ) ): ?>
				<div class="slider slider-reviews">
					<div class="slides reviews">
						<?php $cnt=0; foreach( $reviews as $review ): ?>
						<article class="slide review <?php echo $cnt==0 ? 'is-active':''; ?>">
							<header class="review-header">
								<?php if ( $review['photo'] ): ?>
								<img src="<?php echo $review['photo']['sizes']['avatar']; ?>" alt="" width="<?php echo $review['photo']['sizes']['avatar-width']; ?>" height="<?php echo $review['photo']['sizes']['avatar-height']; ?>" class="portrait" />
								<?php endif; ?>

								<div class="name title-sm">
									<?php echo $review['name']; ?>
								</div><!-- .name -->

								<div class="note">
									<?php for( $i=1; $i <= $review['note']; $i++ ): ?>
									<svg xmlns="http://www.w3.org/2000/svg" width="27" height="16" viewBox="0 0 27 16">
									  <path fill="#FFF" d="M8.5 0L5.873 5.267 0 6.11l4.25 4.1L3.247 16 8.5 13.267 13.753 16l-1.003-5.79L17 6.11l-5.873-.843"/>
									</svg>
									<?php endfor; ?>
								</div><!-- .note -->
							</header>

							<div class="review-content">
								<?php echo $review['text']; ?>
							</div><!-- .review-content -->
						</article><!-- .review -->
						<?php $cnt++; endforeach; ?>
					</div><!-- .reviews -->

					<div class="navigation">
						<div class="arrow prev">
							<div class="svg">
								<svg xmlns="http://www.w3.org/2000/svg" width="17.479" height="9.125" viewBox="0 0 17.479 9.125">
								  <path fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" d="M.98 4.542h16M4.563.5L.5 4.562l4.063 4.063"/>
								</svg>
							</div><!-- .svg -->
						</div><!-- .arrow -->

						<div class="pager">
							<span class="current-page">1</span>/<span class="total-page"><?php echo count($reviews); ?></span>
						</div><!-- .pager -->

						<div class="arrow next">
							<div class="svg">
								<svg xmlns="http://www.w3.org/2000/svg" width="17.479" height="9.125" viewBox="0 0 17.479 9.125">
								  <path fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" d="M16.5 4.583H.5M12.917 8.625l4.062-4.062L12.916.5"/>
								</svg>
							</div><!-- .svg -->
						</div><!-- .arrow -->
					</div><!-- .navigation -->
				</div><!-- .slider-reviews -->
				<?php endif; ?>

				<?php endif; ?>

			</div><!-- .tab -->
			<?php $cnt++; endwhile; ?>
		</div><!-- .screen-tabs -->
		<?php endif; ?>

	</div><!-- .product-body -->

	<?php if ( $products = tpb_get_similar_products( get_the_ID() ) ): ?>
	<div class="product-similars">
		<div class="tab-header">
			<h2 class="title-tab">
				<strong><?php echo count($products); ?></strong>
				<?php _e( 'Similar items', 'tpb' ); ?><br/>
				<?php _e( 'to', 'tpb' ); ?> <?php the_title(); ?>
			</h2>

			<?php if ( has_post_thumbnail() ): ?>
			<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'medium', false ); ?>
			<div class="product-image">
				<img src="<?php echo $image[0]; ?>" alt="" width="<?php echo $image[1]; ?>" height="<?php echo $image[2]; ?>" class="image" />

				<div class="waves">
					<div class="wave"></div>
					<div class="wave"></div>
					<div class="wave"></div>
				</div><!-- .waves -->
			</div><!-- .product-image -->
			<?php endif; ?>

		</div><!-- .tab-header -->

		<div class="products-list small">
			<?php $filters = tpb_get_filters( null, $products ); ?>
			<div class="filters">
				<?php /*
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
				*/ ?>

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

			<div class="scroller">
				<div class="products-outer">
					<div class="products clearfix">
						<?php foreach( $products as $product ): ?>

						<?php
							$prices = tbp_get_product_prices( $product->ID );
							$type = get_post_meta( $product->ID, 'type', true );
						?>
						<article class="product link-product" data-url="<?php echo get_the_permalink( $product->ID ); ?>" data-type="<?php echo sanitize_title( $type ); ?>" data-price="<?php echo $prices[0]->price; ?>" data-name="<?php echo $product->post_title; ?>">
							<?php if ( has_post_thumbnail( $product->ID ) ): ?>
							<?php
								$image = wp_get_attachment_image_src( get_post_thumbnail_id( $product->ID ), 'thumbnail', false );
							?>
							<div class="product-image-thumb">
								<img src="<?php echo $image[0]; ?>" alt="" width="<?php echo $image[1]; ?>" height="<?php echo $image[2]; ?>" class="image" />
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
						<?php endforeach; ?>
					</div><!-- .products -->
				</div><!-- .products-outer -->
			</div><!-- .scroller -->
		</div><!-- .products-list -->
	</div><!-- .product-similars -->
	<?php endif; ?>

	<div class="nav-tabs">
		<ul class="tabs">
			<?php $cnt = 0; while ( have_rows( 'tabs' ) ) : the_row(); ?>
			<li class="tab <?php echo $cnt == 0 ? 'is-active':''; ?>">
				<button type="button" class="btn-tab">
					<i class="fa <?php echo get_sub_field( 'icon' ); ?>" aria-hidden="true"></i>
					<span class="label"><?php echo get_sub_field( 'title' ); ?></span>
				</button>
			</li>
			<?php $cnt++; endwhile; ?>

			<?php if ( $products ): ?>
			<li class="tab">
				<button type="button" class="btn-tab btn-tab-similar">
					<i class="fa fa-ellipsis-h" aria-hidden="true"></i>
					<span class="label"><?php _e( 'Similar items', 'tpb' ); ?></span>
				</button>
			</li>
			<?php endif; ?>
		</ul>
	</div><!-- .nav-tabs -->
</div><!-- .screen-product -->