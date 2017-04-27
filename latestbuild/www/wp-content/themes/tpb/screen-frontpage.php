<div class="screen screen-frontpage" data-screen="frontpage">
	<h1 class="tab-title title-lg screen-title sr-only"><?php _e( 'About', 'tpb' ); ?></h1>

	<?php if ( have_rows( 'tabs', get_option( 'page_on_front' ) ) ): ?>
	<div class="screen-tabs">
		<?php $cnt = 0; while ( have_rows( 'tabs', get_option( 'page_on_front' ) ) ) : the_row(); ?>

		<?php if ( get_row_layout() == 'sections' ): ?>

		<div class="tab tab-front-<?php echo get_row_layout(); ?> <?php echo $cnt == 0 ? 'is-active':''; ?>">
			<div class="tab-header">
				<?php if ( $logo = get_sub_field( 'page_logo' ) ): ?>
				<img src="<?php echo $logo['url']; ?>" alt="" width="<?php echo $logo['width']; ?>" height="<?php echo $logo['height']; ?>" class="page-logo" />
				<?php endif; ?>

				<h2 class="tab-title title-lg"><?php echo get_sub_field( 'page_title' ); ?></h2>

			</div><!-- .tab-header -->

			<?php if ( have_rows( 'sections' ) ): ?>
			<div class="scroller">
				<div class="sections">
					<?php while ( have_rows( 'sections' ) ) : the_row(); ?>
					<div class="section section-<?php echo get_row_layout(); ?>">
						<?php if ( get_row_layout() == 'text' ): ?>

						<?php echo get_sub_field( 'text' ); ?>

						<?php elseif ( get_row_layout() == 'text-image' ): ?>

						<div class="image-container <?php echo get_sub_field( 'image_position' ); ?>">
							<?php $image = get_sub_field( 'image' ); ?>
							<a href="<?php echo $image['sizes']['large']; ?>" class="fancybox">
								<img src="<?php echo $image['sizes']['large']; ?>" alt="" width="<?php echo $image['sizes']['large-width']; ?>" height="<?php echo $image['sizes']['large-height']; ?>" class="image" />
							</a>
						</div><!-- .image-container -->

						<div class="text">
							<?php echo get_sub_field( 'text' ); ?>
						</div><!-- .text -->

						<?php elseif ( get_row_layout() == 'gallery' ): ?>

						<?php $images = get_sub_field( 'gallery' ); ?>
						<div class="images">
							<?php foreach( $images as $image ): ?>
							<a href="<?php echo $image['url']; ?>" class="fancybox">
								<img src="<?php echo $image['sizes']['thumbnail-preserved']; ?>" alt="" width="<?php echo $image['sizes']['thumbnail-preserved-width']; ?>" height="<?php echo $image['sizes']['thumbnail-preserved-height']; ?>" class="image" />
							</a>
							<?php endforeach; ?>
						</div><!-- .images -->

						<?php elseif ( get_row_layout() == 'image' ): ?>

						<?php $image = get_sub_field( 'image' ); ?>
						<a href="<?php echo $image['sizes']['large']; ?>" class="fancybox">
							<img src="<?php echo $image['sizes']['large']; ?>" alt="" width="<?php echo $image['sizes']['large-width']; ?>" height="<?php echo $image['sizes']['large-height']; ?>" class="image" />
						</a>

						<?php endif; ?>
					</div><!-- .section -->
					<?php endwhile; ?>
				</div><!-- .sections -->
			</div><!-- .scroller -->
			<?php endif; ?>
		</div><!-- .tab -->

		<?php elseif ( get_row_layout() == 'category' ): ?>

		<?php
			$categories = tpb_get_grouped_categories( get_sub_field( 'categories' ), get_sub_field( 'page_title' ) );
			$is_active = ($cnt == 0);
			aw_get_template_part( 'tab-category', array( 'category' => $categories, 'is_active' => $is_active ) );
		?>

		<?php endif; ?>

		<?php $cnt++; endwhile; ?>
	</div><!-- .screen-tabs -->
	<?php endif; ?>

	<div class="nav-tabs">
		<ul class="tabs">
			<?php $cnt = 0; while ( have_rows( 'tabs', get_option( 'page_on_front' ) ) ) : the_row(); ?>
			<li class="tab <?php echo $cnt == 0 ? 'is-active':''; ?>">
				<button type="button" class="btn-tab">
					<i class="fa <?php echo get_sub_field( 'icon' ); ?>" aria-hidden="true"></i>
					<span class="label"><?php echo get_sub_field( 'tab_title' ); ?></span>
				</button>
			</li>
			<?php $cnt++; endwhile; ?>
		</ul>
	</div><!-- .nav-tabs -->
</div><!-- .screen-frontpage -->