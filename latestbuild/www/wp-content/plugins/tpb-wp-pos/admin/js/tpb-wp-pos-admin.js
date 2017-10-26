(function( $ ) {
	'use strict';

	/**
	 * Ajax sync data
	 */
	function ajaxSyncData(e) {

		if (e != undefined) {
			e.preventDefault();

			// Selectors
			var btn = $('#sync-btn');
			var loader = $('<div class="loader"><div class="spinner is-active"></div> <div class="text">Syncing...</div></div>');

			// Add loader
			btn.after(loader);
			btn.hide();
		}

		// Action
		$.post(
			ajaxurl,
			{
				'action': 'tpb_sync_data'
			},
			function(response) {
				// Selectors
				var btn = $('#sync-btn');
				var loader = btn.next('.loader');

				response = jQuery.parseJSON(response);

				console.debug(response);

				if (response.done < response.total) {
					var text = loader.find('.text');
					text.html('Syncing...'+response.done+'/'+response.total);

					ajaxSyncData();
				} else {
					btn.show();
					loader.remove();
				}
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
