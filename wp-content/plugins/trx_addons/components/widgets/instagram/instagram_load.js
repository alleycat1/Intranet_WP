/**
 * Get images from Instagram
 *
 * @package ThemeREX Addons
 * @since v1.85.1
 */

/* global jQuery, TRX_ADDONS_STORAGE */

(function() {

	"use strict";
	
	jQuery(document).on( 'action.init_hidden_elements', function() {

		jQuery('.widget_instagram_images[data-instagram-load="1"]:not(.widget_instagram_loader_inited)').each( function() {

			var wrap = jQuery(this).addClass('widget_instagram_loader_inited'),
				hash = wrap.data('instagram-hash'),
				hashtag = wrap.data('instagram-hashtag');
			if ( hash && hashtag ) {
				var url = 'https://www.instagram.com/' + ( hashtag.substring(0, 1) != '#'
															? hashtag.toLowerCase()
															: 'explore/tags/' + hashtag.substring(1)
															)
														+ '/';
				jQuery
					.get( url )
					.done( function( output ) {
						if ( output ) {
							// If redirect to the login page is not occurs (profile page or hashtag page is returned)
							if ( output.indexOf( '<link rel="canonical" href="https://www.instagram.com/accounts/login/"' ) < 0 ) {
								sendInstagramOutputToServer( {
									output: output,
									wrap: wrap,
									hash: hash
								} );
							}
						}
					} );
			}

		} );

	} );


	// Send native IG output to server
	function sendInstagramOutputToServer( params ) {
		jQuery.post(
			TRX_ADDONS_STORAGE['ajax_url'],
			{
				'action': 'trx_addons_instagram_load_images',
				'nonce': TRX_ADDONS_STORAGE['ajax_nonce'],
				'output': params['output'],
				'hash': params['hash']
			},
			function(response) {
				var rez = {};
				try {
					rez = JSON.parse(response);
				} catch (e) {
					rez = { error: TRX_ADDONS_STORAGE['msg_ajax_error'] };
					console.log(response);
				}
				if (rez.error === '') {
					var parent = params['wrap'].parent();
					parent.html( jQuery( rez.data ).find('.widget_instagram_images') );
					// To prevent recursive calls
					parent
						.find('.widget_instagram_images[data-instagram-load="1"]:not(.widget_instagram_loader_inited)')
						.addClass('widget_instagram_loader_inited');
				}
			}
		);
	}

})();