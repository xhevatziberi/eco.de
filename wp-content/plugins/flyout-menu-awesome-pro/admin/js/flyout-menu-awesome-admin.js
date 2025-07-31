(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
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

	 $(document).ready(function() {
		$('[href*="admin.php?page=crb_carbon_fields_container_flyout_menu_awesome_doc.php"]').addClass('docs-link');

		$('.docs-link').click(function(e) {
		    e.preventDefault();
		    var url = 'https://themesawesome.zendesk.com/hc/en-us/categories/360004421851-3D-Menu-Awesome'; 
		    window.open(url, '_blank');
		});
	});

})( jQuery );
