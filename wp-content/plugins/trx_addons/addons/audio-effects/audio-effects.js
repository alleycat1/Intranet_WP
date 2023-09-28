/* global jQuery */

jQuery( document ).ready( function() {

	"use strict";

	if ( typeof window.elementorFrontend !== 'undefined' && elementorFrontend.isEditMode() ) {
		return;
	}

	// Global vars
	var requestAnimationFrame = trx_addons_request_animation_frame();

	var _sounds  = {},																				// List of sounds
		_playing_max = 2,																			// Max state: 0 - all sounds off, 1 - only effects, 2 - all sounds: effects and background
		_playing_saved = trx_addons_get_cookie( 'trx_addons_audio_effects_playing', -1 ),			// Saved state
		_playing = _playing_saved == -1 ? 2 : parseInt(_playing_saved, 10),							// Current state
		_playing_bg_url_saved = trx_addons_get_cookie( 'trx_addons_audio_effects_playing_url' ),	// Last played music on the previous page
		_playing_bg_id = '',																		// Id of current background music
		_playing_bg_id_next = '',																	// Id of next background music
		_playing_bg_time = 0;																		// Time of current background music

	var id = '', idx = 0, data, items;

	var _playing_3rd_party_audio = false;

	var $window        = jQuery( window ),
		$document      = jQuery( document ),
		$body          = jQuery( 'body' ),
		$audio_effects = jQuery( '.sc_audio_effects' );

	if ( typeof TRX_ADDONS_STORAGE == 'undefined' || typeof TRX_ADDONS_STORAGE['audio_effects_allowed'] == 'undefined' || TRX_ADDONS_STORAGE['audio_effects_allowed'] != 1 || typeof window.Howl == 'undefined' ) {
		init_indicator();
		return;
	}

	// Update links and values after the new post added
	$document.on( 'action.got_ajax_response', update_jquery_links );
	$document.on( 'action.init_hidden_elements', update_jquery_links );
	var first_run = true;
	function update_jquery_links(e) {
		if ( first_run && e && e.namespace == 'init_hidden_elements' ) {
			first_run = false;
			return; 
		}
		// Check blocks with audio effects
		jQuery( '.trx_addons_audio_effects_on:not(.audio_effects_inited)' ).each( function() {
			var $self = jQuery( this ).addClass('audio_effects_inited'),
				data = $self.data( 'trx-addons-audio-effects' ),
				id = '';
			if ( typeof data == 'object' ) {
				var $items;
				for (var i in data) {
					$items = data[i].selectors !== '' ? $self.find( data[i].selectors ) : $self;
					if ( $items.length > 0 ) {
						id = add_sound( data[i], 'sc_' );
						if ( id ) {
							init_event_handler( id, $self );
						}
					}
				}
			}
		} );
		// Add listeners to audio tags
		jQuery( 'audio:not(.audio_effects_listeners_inited)' ).each( function() {
			var media = jQuery( this ).addClass( 'audio_effects_listeners_inited' ).get(0);
			media.addEventListener( 'play', function() {
				_playing_3rd_party_audio = true;
				sound_stop( 'page_load' );
				page_background( false );
			} );
			media.addEventListener( 'pause', function() {
				_playing_3rd_party_audio = false;
				page_background( true );
			} );
			media.addEventListener( 'ended', function() {
				_playing_3rd_party_audio = false;
				page_background( true );
			} );
		} );
	}

	// Check page settings
	if ( typeof TRX_ADDONS_STORAGE['audio_effects_allowed'] != 'undefined'
		&& TRX_ADDONS_STORAGE['audio_effects_allowed'] == 1 
		&& typeof TRX_ADDONS_STORAGE['audio_effects'] != 'undefined'
	) {
		for (var i = 0; i < TRX_ADDONS_STORAGE['audio_effects'].length; i++ ) {
			data = TRX_ADDONS_STORAGE['audio_effects'][i];
			id = add_sound( data, 'page_' );
			if ( id && data.selectors && ['click', 'hover'].indexOf(data.event) != -1 ) {
				items = jQuery( data.selectors );
				if ( items.length > 0 ) {
					init_event_handler( id, items );
				}
			}
		}
	}
	
	// Check individual block's settings (override page settings)
	update_jquery_links();

	// Switch playing state off if no audio effects presents on this page
	if ( idx === 0) {
		_playing = 0;
	} else if ( _playing_bg_id === '' ) {
		_playing_max = 1;
		_playing = Math.min( _playing, _playing_max );
	} else {
		_playing_bg_id_next = get_next_background( _playing_bg_id );
	}

	// Init state indicator
	init_indicator();

	// Play "on load" and/or "background" events
	if ( _sounds.hasOwnProperty( 'page_load' ) && _sounds['page_load'].url !== '' ) {
		page_load();
	} else {
		page_background(true);
	}


	// Once play sound on page load (if set)
	function page_load() {
		if ( _sounds.hasOwnProperty( 'page_load' ) && _sounds['page_load'].url !== '' && _playing > 0 && ! _playing_3rd_party_audio ) {
			sound_play( 'page_load' );
		}
	}


	// Infinite play sound on background (if set)
	function page_background( play ) {
		if ( _playing_bg_id ) {
			if ( play ) {
				if (  _playing > 1 && ! _playing_3rd_party_audio ) {
					var time = 0;
					if ( _sounds[_playing_bg_id].state === '' ) {
						time = trx_addons_get_cookie( 'trx_addons_audio_effects_playing_time', 0 );
						if ( time > 0 ) {
							if ( _sounds[_playing_bg_id].url.substring( _sounds[_playing_bg_id].url.lastIndexOf('/') + 1 ) != trx_addons_get_cookie( 'trx_addons_audio_effects_playing_url' ) ) {
								time = 0;
							}
						} else {
							time = 0;
						}
					}
					sound_play( _playing_bg_id, time );
				}
			} else {
				sound_pause( _playing_bg_id );
			}
		}
	}

	// Return id of a next background (if set)
	function get_next_background( id ) {
		var id_next = '', i, start = false;
		// Check rest sounds
		for( i in _sounds ) {
			if ( i == id ) {
				start = true;
			} else if ( start && _sounds[i].event == 'background' ) {
				id_next = i;
				break;
			}
		}
		// Check from start
		if ( id_next === '' ) {
			start = true;
			for( i in _sounds ) {
				if ( i == id ) {
					break;
				} else if ( _sounds[i].event == 'background' ) {
					id_next = i;
					break;
				}
			}
		}
		return id_next;
	}

	// Save state of background music
	function preload_next_background() {
		if ( _playing_bg_id ) {
			_playing_bg_time = _sounds[_playing_bg_id].howl.seek();
			if ( _playing_bg_id_next && _playing_bg_id != _playing_bg_id_next && _sounds[_playing_bg_id_next].state === '' ) {
				var duration = _sounds[_playing_bg_id].howl.duration();
				if ( ! duration || duration - _playing_bg_time < 30 ) {	// Load next bg music if left less then 30s
					_sounds[_playing_bg_id_next].state = 'loading';
					sound_load( _playing_bg_id_next );
				}
			}
			setTimeout( preload_next_background, 500 );
		}
	}

	$window.on( 'unload', function() {
		trx_addons_set_cookie( 'trx_addons_audio_effects_playing_time', _playing_bg_time );
		trx_addons_set_cookie( 'trx_addons_audio_effects_playing_url', _playing_bg_id ? _sounds[_playing_bg_id].url.substring( _sounds[_playing_bg_id].url.lastIndexOf('/') + 1 ) : '' );
	} );


	// Init event handler
	function init_event_handler( id, items ) {
		// Uncomment if you want preload sound on page init. Otherwise sound have been loaded on first event occur
		//sound_get( id );

		// Add handlers
		items.each( function() {
			var $self = jQuery(this),
				ae_class = _sounds[id].event + '_inited',
				event = _sounds[id].event.replace('hover', 'mouseenter')+'.trx_addons_audio_effects';
			if ( $self.hasClass(ae_class) ) {
				$self.off( event );
			}
			$self
				.toggleClass( ae_class, true )
				.on( event, get_event_handler( event, id, function(id) {
					if ( _playing > 0 ) {
						sound_play( id );
					}
				} ) );
		} );
	}

	// Generate callback for event
	function get_event_handler( event, id, cb ) {
		return function(e) {
					cb(id);
					// No prevent default actions!
					//e.preventDefault();
					//return false;
				};
	}

	// Return sound callbacks for state
	function get_event_callbacks( event ) {
		if ( event == 'load' ) {
			return {
				onend: function() {
					page_background( true );
				}
			};
		} else if ( event == 'background' ) {
			return {
				onplay: function() {
					setTimeout( preload_next_background, 500 );
				},
				onend: function() {
					if ( _playing_bg_id_next && _playing_bg_id != _playing_bg_id_next ) {
						sound_stop( _playing_bg_id );
						_playing_bg_id = _playing_bg_id_next;
						_playing_bg_id_next = get_next_background( _playing_bg_id );
						page_background( true );
					}
				}
			};
		}
	}


	// Add a sound data to the list
	function add_sound( data, prefix ) {
		var local = data.hasOwnProperty('local')
					? ( typeof data.local == 'object' ? data.local.url : data.local )
					: '',
			url = local !== '' ? local : data.link,
			id = '';
		if ( url ) {
			id = prefix + data.event + ( 'load' != data.event ? '_' + idx : '' );
			idx++;
			_sounds[ id ] = {
				event: data.event,
				selectors: data.hasOwnProperty('selectors') ? data.selectors : '',
				state: '',
				howl: null,
				sound_id: null,
				loop: data.hasOwnProperty('loop') ? data.loop : 'background' == data.event,
				preload: data.hasOwnProperty('preload') ? data.preload : 'background' != data.event || _playing_bg_id === '',
				url: url,
				volume: Math.max( 0, Math.min( 100, typeof data.volume == 'object' ? data.volume.size : data.volume ) ) / 100,
				callbacks: get_event_callbacks( data.event )
			};
			if ( data.event == 'background' ) {
				if ( _playing_bg_id === '' || ( _playing_bg_url_saved && url.substring( url.lastIndexOf('/') + 1 ) == _playing_bg_url_saved ) ) {
					if ( _playing_bg_id !== '' ) {
						_sounds[ _playing_bg_id ].preload = false;
					}
					_playing_bg_id = id;
					_sounds[ _playing_bg_id ].preload = true;
				}
			}
		}
		return id;
	}


	// Sound control utilities
	//--------------------------------

	// Play/Resume specified sound
	function sound_play( id, time ) {
		if ( _sounds.hasOwnProperty( id ) && _sounds[id].url !== '' ) {
			var howl = sound_get( id );
			if ( howl ) {
				if ( true || _sounds[id].state !== '' ) {	// Play immediately
					// Remove false if you want to stop current playing before a new started
					if ( false && _sounds[id].sound_id && _sounds[id].state == 'playing' ) {
						howl.stop( _sounds[id].sound_id );
						_sounds[id].sound_id = null;
					}
					_sounds[id].sound_id = howl.play();
					if ( time > 0 && _sounds[id].sound_id ) {
						howl.seek( time, _sounds[id].sound_id );
					}
				} else {									// Play on loaded
					howl.once( 'load', function() {
						_sounds[id].sound_id = howl.play();
					} );
				}
			}
		}
	}

	// Pause specified sound
	function sound_pause( id ) {
		if ( _sounds.hasOwnProperty( id ) && _sounds[id].howl ) {
			_sounds[id].howl.pause();
		}
	}

	// Stop specified sound
	function sound_stop( id ) {
		if ( _sounds.hasOwnProperty( id ) && _sounds[id].howl ) {
			_sounds[id].howl.stop();
		}
	}

	// Load specified sound
	function sound_load( id ) {
		if ( _sounds.hasOwnProperty( id ) && _sounds[id].url !== '' ) {
			var howl = sound_get( id );
			if ( howl ) {
				howl.load();
			}
		}
	}

	// Create sound object and preload sound
	function sound_get( id ) {
		var howl = false,
			cb = {};
		if ( _sounds.hasOwnProperty( id ) && _sounds[id].url !== '' ) {
			if ( ! _sounds[id].howl ) {
				if ( _sounds[id].callbacks ) {
					cb = _sounds[id].callbacks;
				}
				howl = _sounds[id].howl = new Howl( {
					// Options
					src: [ _sounds[id].url ],
					//html5: true, // Force to HTML5 so that the audio can stream in (best for large files).
					preload: _sounds[id].preload,
					loop: _sounds[id].loop,
					volume: _sounds[id].volume,
					// Events
					onplayerror: function( sound_id, error ) {
					},
					onplay: function( sound_id ) {
						_sounds[id].state = 'playing';
						// ToDo: Actions while sound playing.
						// For example: Display the duration.
						// duration.innerHTML = self.formatTime(Math.round(sound.duration()));
						if ( cb.hasOwnProperty( 'onplay' ) ) {
							cb.onplay( _sounds[id], sound_id );
						}
					},
					onload: function( sound_id ) {
						if ( _sounds[id].state === '' || _sounds[id].state === 'loading' ) {
							_sounds[id].state = 'loaded';
						}
						// ToDo: Actions on a sound file is loaded.
						if ( cb.hasOwnProperty( 'onload' ) ) {
							cb.onload( _sounds[id], sound_id );
						}
					},
					onend: function( sound_id ) {
						_sounds[id].state = 'finished';
						// ToDo: Actions on end playing.
						if ( cb.hasOwnProperty( 'onend' ) ) {
							cb.onend( _sounds[id], sound_id );
						}
					},
					onpause: function( sound_id ) {
						_sounds[id].state = 'paused';
						// ToDo: Actions on pause.
						if ( cb.hasOwnProperty( 'onpause' ) ) {
							cb.onpause( _sounds[id], sound_id );
						}
					},
					onstop: function( sound_id ) {
						_sounds[id].state = 'stopped';
						// ToDo: Actions on stop playing.
						if ( cb.hasOwnProperty( 'onstop' ) ) {
							cb.onstop( _sounds[id], sound_id );
						}
					},
					onseek: function( sound_id ) {
						// ToDo: Actions on seek.
						if ( cb.hasOwnProperty( 'onseek' ) ) {
							cb.onseek( _sounds[id], sound_id );
						}
					}
				} );
			} else {
				howl = _sounds[id].howl;
			}
		}
		return howl;
	}


	// Button/Indicator Audio Effects
	//---------------------------------------
	function init_indicator() {
		$audio_effects.each( function() {
			jQuery(this)
				.addClass( 'audio_effects_inited sc_audio_effects_' + ( idx === 0 || _playing === 0 ? 'off' : ( _playing == 1 ? 'events' : 'on') ) )
				.on('click', function(e) {
					if ( idx > 0 ) {
						_playing = ( _playing + 1 ) % ( _playing_max + 1 );
						trx_addons_set_cookie( 'trx_addons_audio_effects_playing', _playing );
						$audio_effects
							.removeClass('sc_audio_effects_on sc_audio_effects_off sc_audio_effects_events')
							.addClass( 'sc_audio_effects_' + ( _playing === 0 ? 'off' : ( _playing == 1 ? 'events' : 'on') ) );
						page_background( _playing > 1 );
					}
					e.preventDefault();
					return false;
				});
		} );
	}
});
