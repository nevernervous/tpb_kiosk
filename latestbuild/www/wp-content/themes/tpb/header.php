<!doctype html>
<html class="no-js" lang="<?php bloginfo( 'language' ); ?>">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">

	<title><?php
		// Add the blog name.
		bloginfo( 'name' );

		// Add the blog description for the home/front page.
		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) )
			echo " - $site_description";
	?></title>

	<meta name="format-detection" content="telephone=no">

	<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>

	<div class="global-container">
		<div class="site-background">
			<!-- <?php $background = get_field( 'background', 'options' ); ?>
			<img src="<?php echo $background['url']; ?>" alt="" width="<?php echo $background['width']; ?>" height="<?php echo $background['height']; ?>" class="image" />
			<canvas class="canvas"></canvas> -->

			<div class="canvas"></div>

		</div><!-- .site-background -->

		<div class="screen-fullscreen screen-intro is-visible">
			<div class="screen-outer">
				<div class="phrase">
					<?php _e( 'This is an interactive <br/>smart table', 'tpb' ); ?>
				</div><!-- .phrase -->

				<div class="tips">
					<div class="tip is-active">
						<?php inline_svg( 'intro-illus-1' ); ?>

						<div class="text">
							<?php _e( 'Place an object or <br/>explore our inventory', 'tpb' ); ?>
						</div><!-- .text -->
					</div><!-- .tip -->

					<div class="tip">
						<?php inline_svg( 'intro-illus-2' ); ?>

						<div class="text">
							<?php _e( 'Learn about products <br/>& find great deals', 'tpb' ); ?>
						</div><!-- .text -->
					</div><!-- .tip -->

					<div class="tip">
						<?php inline_svg( 'intro-illus-3' ); ?>

						<div class="text">
							<?php _e( 'Skip the line. <br/><strong>Enjoy!</strong>', 'tpb' ); ?>
						</div><!-- .text -->
					</div><!-- .tip -->
				</div><!-- .tips -->

				<div class="phrase-small">
					<?php _e( 'Touch screen to begin', 'tpb' ); ?>
				</div><!-- .phrase -->
			</div><!-- .screen-outer -->
		</div><!-- .screen-intro -->

		<div class="site-ui is-hidden">
			<div class="site-sidebar">
				<?php $logo = get_field( 'logo', 'options' ); ?>
				<header class="sidebar-head is-active">
					<img src="<?php echo $logo['url']; ?>" alt="" width="<?php echo $logo['width']; ?>" height="<?php echo $logo['height']; ?>" class="logo" />
				</header><!-- .sidebar-head -->

				<div class="sidebar-areas">
					<div class="area area-checkout">
						<div class="area-text">
							<div class="tip">
								Slide jar here to add to cart
							</div><!-- .tip -->
						</div><!-- .area-text -->

						<div class="area-border">
							<?php inline_svg( 'area-border' ); ?>
						</div><!-- .area-border -->

						<div class="area-arrows">
							<div class="arrow"></div>
							<div class="arrow"></div>
							<div class="arrow"></div>
							<div class="arrow"></div>
						</div><!-- .area-arrows -->
					</div><!-- .area-checkout -->

					<div class="separator">
						<div class="dot"></div>
						<div class="dot"></div>
						<div class="dot"></div>
						<div class="dot"></div>
						<div class="dot"></div>
					</div><!-- .separator -->

					<div class="area area-info">
						<div class="area-text">
							<div class="tip tip-off">
								Place a jar here
							</div><!-- .tip -->

							<div class="tip tip-back">
								Slide here to cancel
							</div><!-- .tip -->
						</div><!-- .area-text -->

						<div class="area-border">
							<?php inline_svg( 'area-border' ); ?>
						</div><!-- .area-border -->

						<div class="area-arrows">
							<div class="arrow"></div>
							<div class="arrow"></div>
							<div class="arrow"></div>
							<div class="arrow"></div>
						</div><!-- .area-arrows -->
					</div><!-- .area-infos -->
				</div><!-- .sidebar-areas -->

				<div class="btn-user btn-text btn-reset-session-confirm btn-session">
					<?php _e( 'Start a new session', 'tpb' ); ?>

					<span class="hit"></span>
				</div>

				<div class="background"></div>
			</div><!-- .site-sidebar -->

			<div class="site-topbar">
				<div class="btn-user link-back">
					<?php _e( 'Back to', 'tpb' ); ?>

					<span class="output"></span>
					<span class="hit"></span>
				</div>

				<?php $cart = tpb_get_cart(); ?>
				<div class="btn-user toggle-cart <?php echo $cart['count'] > 0 ? 'is-filled':''; ?>" data-url="<?php echo home_url( '/checkout' ); ?>">
					<div class="open">
						<?php inline_svg( 'icon-cart' ); ?>

						<div class="counter">
							<?php echo $cart['count']; ?>
						</div><!-- .counter -->
					</div><!-- .open -->

					<div class="close">
						<div class="icon"></div>
					</div><!-- .close -->

					<span class="hit"></span>
				</div><!-- .toggle-cart -->

				<div data-url="<?php echo home_url( '/catalogue' ); ?>" class="btn-user btn-text btn-catalogue">
					<?php inline_svg( 'icon-catalogue' ); ?>
					<?php _e( 'Catalogue', 'tpb' ); ?>

					<span class="hit"></span>
				</div>
			</div><!-- .site-topbar -->
		</div><!-- .site-ui -->
