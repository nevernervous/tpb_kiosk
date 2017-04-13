<?php get_header(); ?>

	<div class="site-main is-hidden">
		<?php
			if ( false === get_field( 'object_recognition', 'options' ) )
				get_template_part( 'screen-catalogue' );
			else
				get_template_part( 'screen-select' );
		?>
	</div><!-- .site-main -->

<?php get_footer(); ?>