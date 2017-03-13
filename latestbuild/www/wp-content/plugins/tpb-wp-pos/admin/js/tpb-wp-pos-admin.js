(function( $ ) {
	'use strict';

	/**
	 * Ajax sync data
	 */
	function ajaxSyncData(e) {
		e.preventDefault();

		// Selectors
		var btn = $(this);
		var loader = $('<div class="loader"><div class="spinner is-active"></div> Syncing...</div>');

		// Add loader
		btn.after(loader);
		btn.hide();

		// Action
		$.post(
			ajaxurl,
			{
				'action': 'tpb_sync_data'
			},
			function(response) {
				response = jQuery.parseJSON(response);

				console.debug(response);

				btn.show();
				loader.remove();
			}
		);
	}


	/**
	 * DOM ready
	 */
	$(function() {
		$('#sync-btn').on('click', ajaxSyncData);
	});

})( jQuery );
