/* global jQuery */

(function() {
	"use strict";
	
	// Uncomment next row to disable init VC prettyPhoto on the gallery images
	// to prevent conflict with the PrettyPhoto from WooCommerce 3+
	// Attention! In this case you need manually init some lightbox on the gallery images
	//window.vc_prettyPhoto = function() {};

	jQuery(document).on( 'action.init_hidden_elements', trx_addons_js_composer_init );
	
	function trx_addons_js_composer_init(e, container) {
		if (container === undefined) container = jQuery('body');
		if (container.length === undefined || container.length == 0) return;
		
		// Close button in the messagebox
		container.find('.vc_message_box_closeable:not(.inited)').addClass('inited').on('click', function(e) {
			jQuery(this).fadeOut();
			e.preventDefault();
			return false;
		});

		// Wrap .vc_element to the .sc_layouts_item when editing layouts
		jQuery('.compose-mode.single-cpt_layouts .vc_element[class*="vc_trx_sc_"]:not(.vc_trx_sc_content):not(.vc_trx_sc_layouts)').addClass('sc_layouts_item');
	}

	// Add resize on fullwidth rows
	jQuery(document).on('vc-full-width-row', function(e, container) {
		if ( typeof window.trx_addons_resize_actions != 'undefined' ) {
			trx_addons_resize_actions( jQuery(container) );
		}
	});
	
})();
