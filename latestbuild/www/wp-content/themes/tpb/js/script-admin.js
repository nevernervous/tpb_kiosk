(function( $ ) {
	'use strict';

	/**
	 * Toggle checkboxes
	 */
	function toggleCheckboxesMain() {
		var toggleCheckbox = $(this);
		var checkboxes = toggleCheckbox.closest('.acf-fields').find('.toggle-all-child input[type="checkbox"]');

		checkboxes.prop('checked', toggleCheckbox.prop('checked')).trigger('change');
	}

	function toggleCheckboxesChild() {
		var checkbox = $(this);
		var toggleCheckbox = checkbox.closest('.acf-fields').find('.toggle-all input[type="checkbox"]');

		if (checkbox.prop('checked') === false)
			toggleCheckbox.prop('checked', false).trigger('change');
	}


	/**
	 * DOM ready
	 */
	$(function() {
		$('.toggle-all input[type="checkbox"]').on('click', toggleCheckboxesMain);
		$('.toggle-all-child input[type="checkbox"]').on('click', toggleCheckboxesChild);
	});


	/**
	 * Quick edit box
	 */
	// we create a copy of the WP inline edit post function
	var $wp_inline_edit = inlineEditPost.edit;

	// and then we overwrite the function with our own code
	inlineEditPost.edit = function( id ) {

		// "call" the original WP edit function
		// we don't want to leave WordPress hanging
		$wp_inline_edit.apply( this, arguments );

		// get the post ID
		var $post_id = 0;
		if ( typeof( id ) == 'object' ) {
			$post_id = parseInt( this.getId( id ) );
		}

		if ( $post_id > 0 ) {
			// define the edit row
			var $edit_row = $( '#edit-' + $post_id );
			var $post_row = $( '#post-' + $post_id );

			// get the data
			var $sync_status = $( 'input[name="sync_post_status"]', $post_row ).val();

			// populate the data
			$( ':input[name="sync_post_status"]', $edit_row ).prop('checked', ($sync_status == 1 ? true : false));
		}
	};


	/**
	 * Bulk edit box
	 */
	$( document ).on( 'click', '#bulk_edit', function() {
		// define the bulk edit row
		var $bulk_row = $( '#bulk-edit' );

		// get the selected post ids that are being edited
		var $post_ids = new Array();
		$bulk_row.find( '#bulk-titles' ).children().each( function() {
			$post_ids.push( $( this ).attr( 'id' ).replace( /^(ttle)/i, '' ) );
		});

		// get the data
		var $sync_post_status = $bulk_row.find('input[name="sync_post_status"]').attr('checked') ? 1 : 0;

		// save the data
		$.ajax({
			url: ajaxurl, // this is a variable that WordPress has already defined for us
			type: 'POST',
			async: false,
			cache: false,
			data: {
				action: 'tpb_save_bulk_edit_product', // this is the name of our WP AJAX function that we'll set up next
				post_ids: $post_ids, // and these are the 2 parameters we're passing to our function
				sync_post_status: $sync_post_status
			}
		});
	});


})( jQuery );
