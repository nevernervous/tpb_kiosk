<?php get_header(); ?>

	<div class="site-main is-hidden">
		<?php
			if ( false === tpb_object_recognition() )
				get_template_part( 'screen-catalogue' );
			else
				get_template_part( 'screen-select' );
		?>
	</div><!-- .site-main -->

<?php get_footer(); ?>