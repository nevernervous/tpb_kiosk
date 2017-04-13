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

})( jQuery );
