/**
 * Widget Audio
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

/* global jQuery */

(function() {
	"use strict";

	jQuery(document).on( 'action.init_hidden_elements', trx_addons_init_audio );

	jQuery( window ).on( 'load', function() {
		trx_addons_init_audio();
	} );

	jQuery( window ).on( 'resize', function(){
		trx_addons_audio_height();
	} );

	// Init audio
	function trx_addons_init_audio() {		
		jQuery( '.trx_addons_audio_wrap:not(.inited)' ).addClass( 'inited' ).each(
			function() {
				var audio_wrap = jQuery( this );

				if (audio_wrap.hasClass( 'list' )) {
					var current = audio_wrap.find( '.trx_addons_audio_player:first-child' );
					current.addClass( 'current' );

					if (current.hasClass( 'with_cover' )) {
						audio_wrap.addClass( 'with_cover' );
					}

					// Preload all item
					audio_wrap.find( 'audio' ).each(
						function(){
							var id   = jQuery( this ).attr( 'id' );
							var item = document.getElementById( id );
							if (item) {
								item.load();
								item.pause();
							}
						}
					);

					// Change player status to "Play/Pause"
					audio_wrap.on(
						'click', '.mejs-playpause-button', function(){
							if (jQuery( this ).hasClass( 'mejs-play' )) {
								audio_wrap.addClass( 'play' ).removeClass( 'pause' );
							} else {
								audio_wrap.addClass( 'pause' ).removeClass( 'play' );
							}
						}
					);

					// Change player status to "Mute/Unmute"
					audio_wrap.on(
						'click', '.mejs-volume-button', function(){
							if (jQuery( this ).hasClass( 'mejs-mute' )) {
								audio_wrap.addClass( 'unmute' ).removeClass( 'mute' );
							} else {
								audio_wrap.addClass( 'mute' ).removeClass( 'unmute' );
							}
						}
					);

					// Change player status to "Unmute"
					audio_wrap.on(
						'click', '.mejs-horizontal-volume-slider', function(){
							if (audio_wrap.hasClass( 'mute' )) {
								audio_wrap.addClass( 'unmute' ).removeClass( 'mute' );
							}
						}
					);

					// Change audio track/radio station
					audio_wrap.find( '.trx_addons_audio_navigation' ).on(
						'click', '.nav_btn', function(e){
							current = audio_wrap.find( '.trx_addons_audio_player.current' );

							var id   = current.find( 'audio' ).attr( 'id' );
							var item = document.getElementById( id );
							if (item) {
								item.pause();
							}

							var volume = current.find( '.mejs-horizontal-volume-slider' ).attr( 'aria-valuenow' );
							current.removeClass( 'current' );

							if (jQuery( this ).hasClass( 'prev' )) {
								if (current.is( ':first-child' )) {
									current = audio_wrap.find( '.trx_addons_audio_player:last-child' ).addClass( 'current' ).show();
								} else {
									current = current.prev().addClass( 'current' ).show();
								}
							}

							if (jQuery( this ).hasClass( 'next' )) {
								if (current.is( ':last-child' )) {
									current = audio_wrap.find( '.trx_addons_audio_player:first-child' ).addClass( 'current' ).show();
								} else {
									current = current.next().addClass( 'current' ).show();
								}
							}

							id   = current.find( 'audio' ).attr( 'id' );
							item = document.getElementById( id );

							if (item) {
								// If player has status "Play" than start to play current media item
								if (audio_wrap.hasClass( 'play' )) {
									item.play();
								}

								// Change player status to "Mute/Unmute"
								if (audio_wrap.hasClass( 'mute' )) {
									item.setMuted( true );
								} else if (audio_wrap.hasClass( 'unmute' )) {
									item.setMuted( false );
								}

								// Change volume
								if (volume > 0) {
									var current_volume = current.find( '.mejs-horizontal-volume-slider' ).attr( 'aria-valuenow' );
									if (current_volume != volume) {
										item.setVolume( volume / 100 );
									}
								}
							}

							if (current.hasClass( 'with_cover' )) {
								audio_wrap.addClass( 'with_cover' );
							} else {
								audio_wrap.removeClass( 'with_cover' );
							}

							e.preventDefault();
						}
					);
				}
			}
		);

		trx_addons_audio_height();
	}

	function trx_addons_audio_height(){

		if (window.elementor !== undefined) {
			return;
		}

		jQuery( '.trx_addons_audio_wrap' ).each(
			function() {
				jQuery( this ).removeClass( 'resized' );
				if (jQuery( this ).hasClass( 'list' )) {
					var height = 0;
					jQuery( this ).find( '.trx_addons_audio_player' ).each(
						function(){
							var item_h = jQuery( this ).outerHeight();
							if (item_h > height) {
								height = item_h;
							}
						}
					);
					jQuery( this ).find( '.trx_addons_audio_list' ).height( height );
				}
				jQuery( this ).addClass( 'resized' );
			}
		);
	}

})();
