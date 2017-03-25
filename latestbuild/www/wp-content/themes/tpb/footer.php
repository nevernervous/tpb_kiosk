
		<div class="screen-fullscreen small screen-reset">
			<div class="screen-outer">
				<div class="phrase">
					<?php _e( 'Do you want to start <br/>a new session?', 'tpb' ); ?>
				</div><!-- .phrase -->

				<div class="actions clearfix">
					<div class="btn-user btn btn-reset-cancel">
						<?php _e( 'No continue shopping', 'tpb' ); ?>

						<span class="hit"></span>
					</div>

					<div class="btn-user btn btn-reset-session">
						<?php _e( 'Yes start a new session', 'tpb' ); ?>

						<span class="hit"></span>
					</div>
				</div><!-- .actions -->

				<?php $logo = get_field( 'logo', 'options' ); ?>
				<div class="logo">
					<img src="<?php echo $logo['url']; ?>" alt="" width="<?php echo $logo['width']; ?>" height="<?php echo $logo['height']; ?>" />
				</div><!-- .logo -->
			</div><!-- .screen-outer -->
		</div><!-- .screen-reset -->

		<div class="screen-fullscreen screen-inactive">
			<div class="screen-outer">
				<div class="phrase">
					<?php _e( 'Hey are you <br/>still here?', 'tpb' ); ?>
				</div><!-- .phrase -->

				<div class="actions clearfix">
					<div class="btn-user btn btn-inactive-cancel">
						<?php _e( 'Yes I\'m back', 'tpb' ); ?>

						<span class="hit"></span>
					</div>

					<div class="btn-user btn btn-inactive-reset">
						<?php _e( 'I am a new user', 'tpb' ); ?>

						<span class="hit"></span>
					</div>
				</div><!-- .actions -->

				<?php $logo = get_field( 'logo', 'options' ); ?>
				<div class="logo">
					<img src="<?php echo $logo['url']; ?>" alt="" width="<?php echo $logo['width']; ?>" height="<?php echo $logo['height']; ?>" />
				</div><!-- .logo -->
			</div><!-- .screen-outer -->
		</div><!-- .screen-inactive -->

	</div><!-- .global-container -->

	<div class="touch-feedback"></div>

	<?php wp_footer(); ?>

	<?php if ( $urls = tpb_get_objects() ): ?>
	<script type="text/javascript">
		<?php echo 'var OBJECTS = '.json_encode($urls); ?>
	</script>
	<?php endif; ?>

</body>
</html><!--  /\/\/  -->