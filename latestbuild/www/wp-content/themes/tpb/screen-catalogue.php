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
		<?php aw_get_template_part( 'tab-category', array( 'category' => $category ) ); ?>
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