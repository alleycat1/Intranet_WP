/* global jQuery */


( function( api ) {

	"use strict";

	// Set initial state of controls
	api.bind( 'ready', function() {

		// Add class to the body
		jQuery('body').addClass('trx_addons_customizer_demo');

		// Add caption to the button 'Refresh preview area'
		setTimeout( function() {
			jQuery('#customize-header-actions .customize-action-refresh').html( trx_addons_customizer_vars['msg_refresh_preview_area'] );
		}, 10 );

		// Add welcome block over controls
		jQuery('#customize-controls').append(
			trx_addons_apply_filters('trx_addons_filter_customizer_welcome_block',
				'<div class="trx_addons_customizer_welcome_wrap">'
					+ '<div class="'		// Add class 'trx_addons_customizer_welcome_block_style_round' to get round block
						+ trx_addons_apply_filters('trx_addons_filter_customizer_welcome_block_class', 'trx_addons_customizer_welcome_block' )
					+ '">'
						+ '<div class="trx_addons_customizer_welcome_block_inner">'
							+ '<h1 class="trx_addons_customizer_welcome_block_title">' + trx_addons_customizer_vars['msg_welcome_hi'] + '</h1>'
							+ '<p class="trx_addons_customizer_welcome_block_text">' + trx_addons_customizer_vars['msg_welcome_text'] + '</p>'
							+ '<div class="trx_addons_customizer_welcome_block_button_wrap"><a href="" class="trx_addons_customizer_welcome_block_button_close">' + trx_addons_customizer_vars['msg_welcome_button'] + '</a></div>'
						+ '</div>'
						+ '<a href="#" class="trx_addons_customizer_welcome_block_close"></a>'
					+ '</div>'
				+ '</div>'
			)
		);

		// Close welcome block
		jQuery('.trx_addons_customizer_welcome_block_close,.trx_addons_customizer_welcome_block_button_close')
			.on( 'click', function(e) {
				jQuery(this).parents('.trx_addons_customizer_welcome_wrap').addClass('trx_addons_customizer_welcome_wrap_closed');
				setTimeout( function() {
					jQuery('.trx_addons_customizer_welcome_wrap_closed').remove();
				}, 2000 );
				e.preventDefault();
				return false;
			} );
	} );

} )( wp.customize );
