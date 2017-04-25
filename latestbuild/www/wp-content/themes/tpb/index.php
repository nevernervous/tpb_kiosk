<?php get_header(); ?>

	<div class="site-main is-hidden">
		<?php
			$front_page = get_post( get_option( 'page_on_front' ) );
			if ( $front_page ) {
				get_template_part( 'screen-frontpage' );
			} else {
				if ( false === tpb_object_recognition() )
					get_template_part( 'screen-catalogue' );
				else
					get_template_part( 'screen-select' );
			}
		?>
	</div><!-- .site-main -->

<?php get_footer(); ?>