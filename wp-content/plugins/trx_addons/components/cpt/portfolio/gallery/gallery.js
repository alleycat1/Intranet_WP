;// GalleryFx - animated gallery items with preview mode
(function(window) {

	'use strict';

	if ( ! window.Modernizr) {
		return;
	}

	var GalleryFxCollection = {};

	var support            = { transitions: Modernizr.csstransitions },
		transEndEventNames = { 'WebkitTransition': 'webkitTransitionEnd', 'MozTransition': 'transitionend', 'OTransition': 'oTransitionEnd', 'msTransition': 'MSTransitionEnd', 'transition': 'transitionend' },
		transEndEventName  = transEndEventNames[ Modernizr.prefixed( 'transition' ) ],
		onEndTransition    = function( el, callback ) {
			var onEndCallbackFn = function( e ) {
				if ( support.transitions ) {
					if ( e.target != this ) {
						return;
					}
					this.removeEventListener( transEndEventName, onEndCallbackFn );
				}
				if ( callback && typeof callback === 'function' ) {
					callback.call( this ); }
			};
			if ( support.transitions ) {
				el.addEventListener( transEndEventName, onEndCallbackFn );
			} else {
				onEndCallbackFn();
			}
		};

	// GalleryFx obj
	function GalleryFx(el, options) {
		this.galleryEl = el;
		this.options   = extend( {}, this.options );
		extend( this.options, options );

		this.items = [].slice.call( this.galleryEl.querySelectorAll( '.sc_portfolio_item' ) );

		if ( jQuery( this.galleryEl ).next( '.sc_portfolio_preview' ).length === 0 ) {
			jQuery( this.galleryEl ).after(
				'<div class="sc_portfolio_preview' + (this.options.previewClass ? ' ' + this.options.previewClass : '') + '">\
					<span class="sc_portfolio_preview_close trx_addons_button_close"><span class="sc_portfolio_preview_close_icon trx_addons_button_close_icon"></span></span>\
					<div class="sc_portfolio_preview_description"></div>\
				</div>'
			);
		}
		this.previewEl            = nextSibling( this.galleryEl );
		this.isExpanded           = false;
		this.isAnimating          = false;
		this.closeCtrl            = this.previewEl.querySelector( '.sc_portfolio_preview_close' );
		this.previewDescriptionEl = this.previewEl.querySelector( '.sc_portfolio_preview_description' );

		this._init();
	}

	// options
	GalleryFx.prototype.options = {
		pagemargin : 0,						// Additional margins for the preview window
		imgPosition : { x : 1, y : 1 },		// Preview window size
											// x and y can have values from 0 to 1 (percentage).
											// If negative then it means the alignment is left and/or top rather than right and/or bottom
											// So, as an example, if we want our large image to be positioned vertically on 25% of the screen and centered horizontally the values would be x:1,y:-0.25
		previewClass : '',					// Extra class for the preview block
		onInit : function(instance) { return false; },
		onResize : function(instance) {	return false; },
		onOpenItem : function(instance, item) { return false; },
		onCloseItem : function(instance, item) { return false; },
		onExpand : function() { return false; }
	};

	GalleryFx.prototype._init = function() {
		// callback
		this.options.onInit( this );

		var self = this;
		// init gallery after all images are loaded
		trx_addons_when_images_loaded( jQuery( this.galleryEl ), function() {
			// init/bind events
			self._initEvents();
			// create the media container and append it to the DOM
			self._initMediaContainer();
		} );
	};

	// initialize/bind events
	GalleryFx.prototype._initEvents = function () {
		var self = this;
		// var clickEvent = (document.ontouchstart !== null ? 'click' : 'touchstart');

		this.items.forEach( function(item) {
			if ( classie.has( item, 'inited' ) ) {
				return;
			}
			classie.add( item, 'inited' );

			var with_img = item.querySelector( 'img' ) || item.querySelector( '.post_video' ) || item.querySelector( '.post_featured' );

			var disableClick = function(e) {
				return ( ! with_img && e.target.nodeName == 'A' )
						|| ( e.target.className || '' ).indexOf('video_frame_control_') != -1
						|| ( e.target.className || '' ).indexOf('mejs') != -1
						|| ( e.target.parentNode.className || '' ).indexOf('mejs') != -1
						|| ( e.target.getAttribute('id') || '' ).indexOf('mejs') != -1
						|| ( e.target.parentNode.getAttribute('id') || '' ).indexOf('mejs') != -1;
			};

			// var touchend = function(e) {
			// 	if ( disableClick(e) ) {
			// 		return;
			// 	}
			// 	e.preventDefault();
			// 	self._openItem( e, item );
			// 	item.removeEventListener( 'touchend', touchend );
			// };
			// var touchmove = function(e) {
			// 	item.removeEventListener( 'touchend', touchend );
			// };
			// var manageTouch = function() {
			// 	item.addEventListener( 'touchend', touchend );
			// 	item.addEventListener( 'touchmove', touchmove );
			// };
			// item.addEventListener( clickEvent, function(e) {
			// 	if ( clickEvent === 'click' ) {
			// 		if ( disableClick(e) ) {
			// 			return;
			// 		}
			// 		e.preventDefault();
			// 		self._openItem( e, item );
			// 	} else {
			// 		manageTouch();
			// 	}
			// } );

			// Use only a click event for opening the item
			// (a code above is for emulating click event on mobile devices via touch events and it's broke scrolling on mobile devices on some themes)
			item.addEventListener( 'click', function(e) {
				if ( disableClick(e) ) {
					return;
				}
				e.preventDefault();
				self._openItem( e, item );
			} );
		} );

		// close expanded image
		this.closeCtrl.addEventListener( 'click', function(e) {
			self._closeItem();
			e.preventDefault();
			return false;
		} );

		window.addEventListener( 'resize', throttle(
			function(e) {
				self.options.onResize( self );
			},
			10
		) );
	};

	// create the media container and style blocks
	GalleryFx.prototype._initMediaContainer = function() {
		// Media container
		this.mediaContainer = document.createElement( 'div' );
		this.mediaContainer.className = 'sc_portfolio_preview_media_container';
		this.mediaContainer.style.opacity = 0;
		this.mediaContainer.style.maxWidth = 'calc(' + parseInt( Math.abs( this.options.imgPosition.x ) * 100 ) + 'vw - ' + this.options.pagemargin + 'px)';
		this.mediaContainer.style.maxHeight = 'calc(' + parseInt( Math.abs( this.options.imgPosition.y ) * 100 ) + 'vh - ' + this.options.pagemargin + 'px)';
		this.previewEl.appendChild( this.mediaContainer );
		// Style
		this.styleTag = document.createElement( 'style' );
		this.stylePrefix = 'sc_portfolio_preview_style_' + ( '' + Math.random() ).replace('.', '');
		this.previewEl.appendChild( this.styleTag );
	};
	
	// reset the media container
	GalleryFx.prototype._resetMediaContainer = function() {
		this.mediaContainer.style.opacity = 0;
		this.mediaContainer.style.webkitTransform = 'translate3d(0,0,0) scale3d(1,1,1)';
		this.mediaContainer.style.transform       = 'translate3d(0,0,0) scale3d(1,1,1)';
		this.mediaContainer.innerHTML = '';
	};

	// add the original/large image element to the preview area
	GalleryFx.prototype._setImage = function(src) {
		var image = document.createElement( 'img' );
		image.src = src;
		this.mediaContainer.appendChild( image );
	};

	// move the video element to the preview area
	GalleryFx.prototype._setVideo = function(video) {
		this.mediaContainer.innerHTML = video;
	};

	// create/set the clone image element
	GalleryFx.prototype._setStart = function( largeBounds, mediaBounds ) {
		var dx = mediaBounds.left - largeBounds.left,
			dy = mediaBounds.top - largeBounds.top,
			z  = Math.min(
					mediaBounds.width / largeBounds.width,
					mediaBounds.height / largeBounds.height
					);
		this.styleTag.innerHTML = '\
			@-webkit-keyframes sc-portfolio-show-media-container {\
				0%{\
					-webkit-transform: translate3d(' + dx + 'px,' + dy + 'px,0) scale3d(' + z + ', ' + z + ',1);\
					transform: translate3d(' + dx + 'px,' + dy + 'px,0) scale3d(' + z + ', ' + z + ',1);\
				}\
				100%{\
					-webkit-transform: translate3d(0,0,0) scale3d(1,1,1);\
					transform: translate3d(0,0,0) scale3d(1,1,1);\
				}\
			}\
			@keyframes sc-portfolio-show-media-container {\
				0%{\
					-webkit-transform: translate3d(' + dx + 'px,' + dy + 'px,0) scale3d(' + z + ', ' + z + ',1);\
					transform: translate3d(' + dx + 'px,' + dy + 'px,0) scale3d(' + z + ', ' + z + ',1);\
				}\
				100%{\
					-webkit-transform: translate3d(0,0,0) scale3d(1,1,1);\
					transform: translate3d(0,0,0) scale3d(1,1,1);\
				}\
			}\
		';
	};

	// open a gallery item
	GalleryFx.prototype._openItem = function(ev, item) {

		var media = item.querySelector( '.post_video' );
		if ( ! media ) {
			media = item.querySelector( 'img' );
			if ( ! media ) {
				media = item.querySelector( '.post_featured' );
			}
		}
		if ( this.isAnimating || this.isExpanded || ! media ) {
			return;
		}

		this.isAnimating = true;
		this.isExpanded  = true;

		// index of current item
		this.current = this.items.indexOf( item );

		// set the original image (large image) or video to the preview area
		var image = item.getAttribute( 'data-src' ),
			video = item.getAttribute( 'data-video' );
		if ( video ) {
			this._setVideo( video );
			var player = jQuery(media).data( 'video-player' );
			if ( player && typeof player.pauseVideo == 'function' ) {	// Youtube video with autoplay
				player.pauseVideo();
			}
		} else {
			this._setImage( image );
		}
		
		// continue after the large image are loaded
		var self = this;
//		imagesLoaded( this.mediaContainer, function() {
		trx_addons_when_images_loaded( jQuery( this.mediaContainer ), function() {
			// callback
			self.options.onOpenItem( self, item );
			// set start position
			var mediaBounds = media.getBoundingClientRect(),
				largeBounds = self.mediaContainer.getBoundingClientRect();
			self._setStart( largeBounds, mediaBounds );

			// add the description if any
			var description = item.getAttribute( 'data-details' );
			if ( description ) {
				self.previewDescriptionEl.innerHTML = description;
			}

			setTimeout( function() {
				// controls the elements inside the expanded view
				classie.add( self.previewEl, 'sc_portfolio_preview_show' );
				// large image will animate
				classie.add( self.mediaContainer, 'sc_portfolio_preview_image_animate' );
				// make media container visible
				self.mediaContainer.style.opacity = 1;
				// hide original gallery item
				classie.add( item, 'sc_portfolio_item_current' );
				// callback (commented, because while animation is running - an object dimensions are changing
				//           and we can't get correct values for an image or an internal video to init it's size)
				// self.options.onExpand();
			}, 0 );

			// after the animation
			var afterAnimationDone = false;
			function afterAnimation() {
				if ( ! afterAnimationDone ) {
					afterAnimationDone = true;
					// close button just gets shown after the large image gets loaded
					classie.add( self.previewEl, 'sc_portfolio_preview_image_loaded' );
					// large image end animate
					classie.remove( self.mediaContainer, 'sc_portfolio_preview_image_animate' );
					// end animating
					self.isAnimating = false;
					// callback
					self.options.onExpand();
				}
			}
			onEndTransition( self.mediaContainer, afterAnimation );
			setTimeout( afterAnimation, 1000 );
		} );
	};

	// close the original/large image view
	GalleryFx.prototype._closeItem = function() {

		if ( ! this.isExpanded || this.isAnimating ) {
			return;
		}
		this.isExpanded  = false;
		this.isAnimating = true;

		// the gallery item's image and its offset
		var galleryItem  = this.items[this.current],
			galleryImg   = galleryItem.querySelector( 'img' ) || galleryItem.querySelector( '.post_featured' ),
			galleryVideo = galleryItem.querySelector( '.post_video' ),
			galleryMedia = galleryVideo ? galleryVideo : galleryImg,
			self         = this;

		var mediaBounds = galleryMedia.getBoundingClientRect(),
			largeBounds = this.mediaContainer.getBoundingClientRect();

		classie.remove( this.previewEl, 'sc_portfolio_preview_show' );
		classie.remove( this.previewEl, 'sc_portfolio_preview_image_loaded' );

		// callback
		this.options.onCloseItem( this, galleryItem );

		// large image will animate back to the position of its gallery's item
		classie.add( this.mediaContainer, 'sc_portfolio_preview_image_animate' );

		// add transition after timeout, otherwise previous class is not yet applied
		setTimeout( function() {
			// set the transform to the original/large image
			var dx = mediaBounds.left - largeBounds.left,
				dy = mediaBounds.top - largeBounds.top,
				z  = Math.min(
						mediaBounds.width / largeBounds.width,
						mediaBounds.height / largeBounds.height
						);

			self.mediaContainer.style.webkitTransform = 'translate3d(' + dx + 'px, ' + dy + 'px, 0) scale3d(' + z + ', ' + z + ', 1)';
			self.mediaContainer.style.transform       = 'translate3d(' + dx + 'px, ' + dy + 'px, 0) scale3d(' + z + ', ' + z + ', 1)';

			// once that's done..
			var afterAnimationDone = false;
			function afterAnimation() {
				if ( ! afterAnimationDone ) {
					afterAnimationDone = true;
					// clear description
					self.previewDescriptionEl.innerHTML = '';
					// show original gallery item
					classie.remove( galleryItem, 'sc_portfolio_item_current' );
					// resume video
					if ( galleryVideo ) {
						var player = jQuery(galleryVideo).data( 'video-player' );
						if ( player && typeof player.playVideo == 'function' ) {		// Youtube video with autoplay
							player.playVideo();
						}
					}
					// fade out the original image
					setTimeout( function() {
						self.mediaContainer.style.opacity = 0;
					}, 60 );
					// and after that
//					onEndTransition( self.mediaContainer, afterAnimation2 );
					setTimeout( afterAnimation2, 100 );
				}
				function afterAnimation2() {
					// reset media container
					classie.remove( self.mediaContainer, 'sc_portfolio_preview_image_animate' );
					self._resetMediaContainer();
					// end animating
					self.isAnimating = false;
				}
			}
			onEndTransition( self.mediaContainer, afterAnimation );
			setTimeout( afterAnimation, 500 );
		}, 10 );
	};

	// gets the window sizes
	GalleryFx.prototype._getWinSize = function() {
		return {
			width: document.documentElement.clientWidth,
			height: window.innerHeight
		};
	};

	// Make global object
	window.GalleryFx = GalleryFx;

	// Create and init GalleryFx object
	jQuery( document ).on( 'action.init_hidden_elements', function( e, cont ) {

		if (cont === undefined) cont = jQuery( 'body' );

		cont.find( '.sc_portfolio_masonry_wrap:not(.preview_inited):not([data-gallery="0"]),.sc_portfolio_columns_wrap:not(.preview_inited):not([data-gallery="0"])' )
			.each( function(idx) {
				if (jQuery( this ).parents( 'div:hidden,article:hidden' ).length > 0) {
					return;
				}
				var id = jQuery( this ).addClass( 'preview_inited' ).attr( 'id' );
				if ( ! id ) {
					id = ('gallery_fx_' + Math.random()).replace( '.', '' );
					jQuery( this ).attr( 'id', id );
				}
				GalleryFxCollection[id] = new GalleryFx(
					this,
					{
						previewClass: 'scheme_dark',
						imgPosition: {
							x: -0.5,
							y: 1
						},
						onOpenItem: function( instance, item ) {
							var animated = false;
							instance.items.forEach( function( el ) {
								if (item != el && ! animated ) {
									if ( ! classie.has( el, 'animated' ) ) {
										var delay                 = Math.floor( Math.random() * 250 );
										el.style.webkitTransition = 'opacity .5s ' + delay + 'ms cubic-bezier(.7,0,.3,1), -webkit-transform .5s ' + delay + 'ms cubic-bezier(.7,0,.3,1) !important';
										el.style.transition       = 'opacity .5s ' + delay + 'ms cubic-bezier(.7,0,.3,1), transform .5s ' + delay + 'ms cubic-bezier(.7,0,.3,1) !important';
										el.style.webkitTransform  = 'scale3d(0.1,0.1,1) !important';
										el.style.transform        = 'scale3d(0.1,0.1,1) !important';
										el.style.opacity          = '0 !important';
									} else {
										animated = true;
									}
								}
							} );
						},
						onCloseItem: function( instance, item ) {
							var animated = false;
							instance.items.forEach( function( el ) {
								if ( item != el && ! animated ) {
									if ( ! classie.has( el, 'animated' ) ) {
										el.style.webkitTransition = 'opacity .4s, -webkit-transform .4s !important';
										el.style.transition       = 'opacity .4s, transform .4s !important';
										el.style.webkitTransform  = 'scale3d(1,1,1) !important';
										el.style.transform        = 'scale3d(1,1,1) !important';
										el.style.opacity          = '1 !important';

//										onEndTransition( el, function() {
										setTimeout( function() {
											el.style.webkitTransition = 'none';
											el.style.transition       = 'none';
											el.style.webkitTransform  = 'none';
											el.style.transform        = 'none';
										}, 500 );
									} else {
										animated = true;
									}
								}
							} );
						},
						onExpand: function() {
							var content = jQuery( '.sc_portfolio_preview' );
							if ( content.length > 0 ) {
								content.find( '.inited' ).removeClass( 'inited' );
								jQuery( document ).trigger( 'action.init_hidden_elements', [content] );
								jQuery( window ).trigger( 'resize' );
							}
						}
					}
				);
			} );
	} );

	// some helper functions
	function throttle(fn, delay) {
		var allowSample = true;
		return function(e) {
			if (allowSample) {
				allowSample = false;
				setTimeout( function() { allowSample = true; }, delay );
				fn( e );
			}
		};
	}
	function nextSibling(el) {
		var nextSibling = el.nextSibling;
		while (nextSibling && nextSibling.nodeType != 1) {
			nextSibling = nextSibling.nextSibling;
		}
		return nextSibling;
	}
	function extend( a, b ) {
		for ( var key in b ) {
			if ( b.hasOwnProperty( key ) ) {
				a[key] = b[key];
			}
		}
		return a;
	}

})( window );
