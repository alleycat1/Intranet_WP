/**
 * Shortcode Accordion
 *
 * @package ThemeREX Addons
 * @since v1.2
 */
/* global jQuery, TRX_ADDONS_STORAGE */

// Init handlers
(function() {
	"use strict";

	jQuery(document).on( 'action.init_hidden_elements', trx_addons_init_accordion );

	// Init accordionposts
	function trx_addons_init_accordion(e, container) {
		if ( jQuery('.sc_accordionposts:not(.inited)').length > 0 ) {
			jQuery('.sc_accordionposts:not(.inited)')
				.addClass('inited')
				.on('click', '.sc_accordionposts_item_header, .sc_accordionposts_item_icon, .sc_accordionposts_item_subtitle, .section_icon', function(e) {
					var $wrapper = jQuery(this).closest('.sc_accordionposts').eq(0),
						$parent = jQuery(this).closest('.sc_accordionposts_item').eq(0);

					// Close others
					if (!$parent.hasClass('active')) {
						jQuery('.sc_accordionposts_item', $wrapper).removeClass('active')
							.find('.sc_accordionposts_item_header').slideDown();
						jQuery('.sc_accordionposts_item_inner, .sc_accordionposts_item_subtitle', $wrapper).slideUp(0);
					}
					jQuery('body,html').animate( { scrollTop: $parent.offset().top - trx_addons_fixed_rows_height() }, 300 );
					jQuery('.sc_accordionposts_item_header, .sc_accordionposts_item_inner, .sc_accordionposts_item_subtitle', $parent).slideToggle(300, function() {
						jQuery(document).trigger( 'action.init_hidden_elements', [$parent] );
						jQuery(window).trigger( 'resize' );
					});
					$parent.toggleClass( 'active' );
					return false;
				});
		}
	}

})();