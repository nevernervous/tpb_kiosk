(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	
	 
})( jQuery );

		$(window).on('markerProduct',function() {
			heap.track('Marker: loaded Product');
			console.log('product');
		});
		$(window).on('markerCart',function() {
			heap.track('Marker: added to cart');
			console.log('cart');
			
		});

		/*
		//////////// Uncomment this function to enable printing ////////////////
		$(window).on('printorder', function() {	
			// set this to the name of the printer
			var printerName = "add printer name here";
			
			qz.security.setCertificatePromise(function(resolve, reject) {
				$.ajax("wp-content/plugins/tpb-wp-pos/assets/signing/digital-certificate.txt").then(resolve, reject);
			});

			qz.security.setSignaturePromise(function(toSign) {
				return function(resolve, reject) {
					$.ajax("wp-content/plugins/tpb-wp-pos/assets/signing/sign-message.php?request=" + toSign).then(resolve, reject);
				};
			});


			qz.websocket.connect().then(function() { 
			  return qz.printers.find(printerName)              
			}).then(function(printer) {
				var config = qz.configs.create(printer);       // Create a default config for the found printer
				var data = [{
					type: 'raw',
					format: 'file', // or 'plain' if the data is raw HTML
					data:'wp-content/plugins/tpb-wp-pos/receipt.txt'
				}];
				return qz.print(config, data);
				}).catch(function(e) { console.error(e); });
				
		  })
		*/	  
	
 