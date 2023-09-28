/* global jQuery */

(function() {
	"use strict";

	var $window   = jQuery( window ),
		$document = jQuery( document ),
		$exit 	  = false,
		$first_load = true;

	// Image height proccessing
	trx_addons_lazy_load_process_image_height();	

	// First images load  
	trx_addons_lazy_load_process_media();	

	$window.on( 'scroll', function() {
		if ( ! $first_load ) {
			trx_addons_lazy_load_process_media();
		}
	} );

	$window.on( 'resize', function() {
		trx_addons_lazy_load_process_media();
		$first_load = false;
	} );

	// Popups lazy load
	$document.on('action.opened_popup_elements action.opened_dropdown_elements action.init_hidden_elements', function() {
		trx_addons_lazy_load_process_media();
	});

	// After all content load
	$window.on( 'load', function() {
		$first_load = false;
		$window.scroll();

		// If custom submenu contain media then run lazyload
		if ( jQuery('.sc_layouts_menu').find("img[data-trx-lazyload-src]:not(.lazyload_inited),\
				[data-trx-lazyload-style]:not(.lazyload_inited),\
				.post_featured [class*='_inline_']:not([class*='trx_addons_inline_']):not(.lazyload_inited),\
				.post_featured[class*='_inline_']:not([class*='trx_addons_inline_']):not(.lazyload_inited),\
				.post_thumb[class*='_inline_']:not([class*='trx_addons_inline_']):not(.lazyload_inited),\
				.banner_wrap[class*='_inline_']:not([class*='trx_addons_inline_']):not(.lazyload_inited),\
				.sc_panel_thumb[class*='_inline_']:not([class*='trx_addons_inline_']):not(.lazyload_inited),\
				.page_content_wrap .elementor-element[data-settings]:not(.lazyload_inited),\
				.page_content_wrap .elementor-element[data-settings] > .elementor-column-wrap:not(.lazyload_inited),\
				video[data-trx-lazyload-src]:not(.lazyload_inited),\
				audio[data-trx-lazyload-src]:not(.lazyload_inited),\
				iframe[data-trx-lazyload-src]:not(.lazyload_inited),\
				.trx_addons_video_list_controller_item[data-video]:not(.lazyload_inited)").length > 0 ) {

			jQuery('.sc_layouts_menu, .menu-item-has-children-layout').on('mouseenter', function() {
				trx_addons_lazy_load_process_media();
			});

			jQuery('.sc_layouts_menu, .menu-item-has-children-layout').on('touchstart', function() {
				trx_addons_lazy_load_process_media();
			});
		}
	});	

	// Animated item size
	function trx_addons_lazy_load_get_item_vars(item) {
		return {
			height: item.outerHeight(),
			topPosition: item.offset().top,
			bottomPosition: item.offset().top + item.outerHeight()
		};
	}

	// Browser window size
	function trx_addons_lazy_load_get_window_vars() {
		return {
			height: $window.height(),
			topPosition: $window.scrollTop(),
			bottomPosition: $window.height() + $window.scrollTop()
		};
	}

	function trx_addons_lazy_load_is_item_visible(item, className, type) {
		if ( jQuery(item).length === 0 ) {
			return;
		}	
		var lazyItem = jQuery.find(item);
		jQuery.each(lazyItem, function() {		
			var element = jQuery(this);

			// Return if element in the layout panel
			if ( element.parents('.sc_layouts_panel:not(.sc_layouts_panel_opened)').length > 0 ) {
				return;
			} 

			var potential_parents = trx_addons_lazy_load_parents();
			var parent;

			// Find element parent
			potential_parents.forEach(function(potential_parent){
				if ( element.parents(potential_parent).length > 0 ) {
					parent = element.parents(potential_parent);
				}
			});

			// Exit if element is hidden and it has not parent 
			if ( element.is(':hidden') ) {
				if ( !parent ) {
					return;
				}	
				if ( parent.css('position') == 'fixed' ) {
					trx_addons_lazy_load_process(element, className, type);
					return;
				}
			} 	

			var itemVars = trx_addons_lazy_load_get_item_vars( parent ? parent : element ),
				windowVars = trx_addons_lazy_load_get_window_vars();	

			if ( itemVars.topPosition != 0 && itemVars.bottomPosition != 0 && (itemVars.bottomPosition >= windowVars.topPosition) && (itemVars.topPosition <= windowVars.bottomPosition)) {
				trx_addons_lazy_load_process(element, className, type);
			} 
		});
	}

	function trx_addons_lazy_load_process_media() {
		if ( jQuery('body').hasClass('allow_lazy_load') ) { 

			$document.trigger('action.before_lazy_load');

			// Process <img> tags
			trx_addons_lazy_load_is_item_visible("img[data-trx-lazyload-src]:not(.lazyload_loading):not(.lazyload_inited)", "lazyload_inited", "image");

			// Process style="background-image"
			trx_addons_lazy_load_is_item_visible("[data-trx-lazyload-style]:not(.lazyload_loading):not(.lazyload_inited)", "lazyload_inited", "style-bg-image");

			// Process background images
			trx_addons_lazy_load_is_item_visible(".post_featured [class*='_inline_']:not([class*='trx_addons_inline_']):not(.lazyload_loading):not(.lazyload_inited),\
											.post_featured[class*='_inline_']:not([class*='trx_addons_inline_']):not(.lazyload_loading):not(.lazyload_inited),\
											.post_thumb[class*='_inline_']:not([class*='trx_addons_inline_']):not(.lazyload_loading):not(.lazyload_inited),\
											.banner_wrap[class*='_inline_']:not([class*='trx_addons_inline_']):not(.lazyload_loading):not(.lazyload_inited),\
											.sc_panel_thumb[class*='_inline_']:not([class*='trx_addons_inline_']):not(.lazyload_loading):not(.lazyload_inited)",
											"lazyload_inited",
											"class-bg-image"
											);

			// Process elementor background images
			trx_addons_lazy_load_is_item_visible(".page_content_wrap .elementor-element[data-settings]:not(.lazyload_loading):not(.lazyload_inited),\
											.page_content_wrap .elementor-element[data-settings] > .elementor-column-wrap:not(.lazyload_loading):not(.lazyload_inited)",
											"lazyload_inited",
											"class-elem-bg-image"
											);
			
			// Process video, audio and iframes
			trx_addons_lazy_load_is_item_visible("video[data-trx-lazyload-src]:not(.lazyload_inited)", "lazyload_inited", "video");
			trx_addons_lazy_load_is_item_visible("audio[data-trx-lazyload-src]:not(.lazyload_inited)", "lazyload_inited", "audio");
			trx_addons_lazy_load_is_item_visible("iframe[data-trx-lazyload-src]:not(.lazyload_inited)", "lazyload_inited", "iframe");

			// Process video controlls tags
			trx_addons_lazy_load_is_item_visible(".trx_addons_video_list_controller_item[data-video]:not(.lazyload_loading):not(.lazyload_inited)", "lazyload_inited", "video-control");

			$document.trigger('action.after_lazy_load');
		}
	}

	function trx_addons_lazy_load_process(element, className, type) { 
		if ( $exit ) return;

		// Loading <img>
		if ( 'image' == type ) {
			// Safari border-radius fix 
			if ( jQuery('body[class*="safari"]').length > 0 ) { 
				if ( parseInt(element.css('border-radius'), 10) === 0  &&  parseInt(element.parent().css('border-radius'), 10) > 0 ) {
					element.css('border-radius', 'inherit');
				} 
			}

			// Get css properties			
			var opacity = element.css('opacity');
			var transition = element.css('transition');
			transition = (transition !== '' ? transition + ', ' : '') + 'opacity 0.3s ease, height 0s ease, padding 0s ease';

			// Hide and load image
			element.addClass('lazyload_loading').css({'opacity': '0', 'transition': 'opacity 0s ease'});

			// Action after image is loaded
			element.attr('src', element.data('trx-lazyload-src')).on('load', function() {		
				element.removeAttr('data-trx-lazyload-src').removeClass('lazyload_loading').addClass(className);

				// Remove height and width attribute
				if ( element.is('[data-trx-lazyload-height]') ) {
					element.removeAttr('data-trx-lazyload-height').css({'height': '', 'padding-top': ''});			   
				}

				// Show loaded image
			   	element.css({'opacity': opacity, 'transition': transition});

			   	// Remove styles
			   	setTimeout(function(){
					element.css({'opacity': '', 'transition': '', 'transition-duration': '', 'border-radius': ''});
					// Trigger action to inform other scripts about image is loaded
					$document.trigger( 'action.init_lazy_load_elements', [element] );
				}, 300); 
			}); 
		}

		// Loading style="background-image"
		if ( 'style-bg-image' == type ) {
			// Get image URL
			var image = element.data('trx-lazyload-style').replace('background-image:', '').replace('url(', '').replace(')', '').replace(';', '');

			// Get css properties
			var opacity = element.css( 'opacity' );
			var transition = element.css( 'transition' );
			transition = ( transition !== '' ? transition + ', ' : '' ) + 'opacity 0.3s ease, background-image 0s ease';

			// Hide and load image
			element.addClass('lazyload_loading').css( { 'opacity': '0', 'transition': 'opacity 0s ease' } );

			// Action after image is loaded
			jQuery('<img/>').attr('src', image).on('load', function() {	
				// Show loaded image
				element.removeAttr('data-trx-lazyload-style').removeClass('lazyload_loading').addClass(className).css({'opacity': opacity, 'transition': transition, 'background-image': 'url(' + image + ')'});

				// Remove styles
				setTimeout( function() {
					element.css({'opacity': '', 'transition': ''});
				}, 300 );

				jQuery(this).remove();
			});
		}

		if ( 'class-elem-bg-image' == type || 'class-bg-image' == type ) {
			var image = element.css( 'background-image' );
			if ( image != 'none' && ! image.match( /placeholder.png/g ) ) {
				element.addClass( className );
				return;
			}
		}

		// Loading background images
		if ( 'class-bg-image' == type ) {
			// Get css properties
			var opacity = element.css( 'opacity' );
			var transition = element.css( 'transition' );
			transition = ( transition != '' ? transition + ', ' : '' ) + 'opacity 0.3s ease, background-image 0s ease';

			// Hide and load image	
			element.addClass( className + ' lazyload_loading' ).css( { 'opacity': '0', 'transition': 'opacity 0s ease' } );

			// Get image URL
			var image = element.css( 'background-image' );
			var subX = image.substring( 0, 3 );
			if (image != '' && image != 'none' && subX != 'lin' && subX != 'rad' ) {
				// Get image URL
				image = image.replace('url("', '').replace('")', '').replace("'", '').replace("'", '');

				jQuery('<img/>').attr('src', image).on('load', function() {	
					// Show loaded image	
					element.removeClass('lazyload_loading').css({'opacity': opacity, 'transition': transition});

					// Remove styles
					setTimeout(function(){
						element.css({'opacity': '', 'transition': ''});
					}, 300);

				   	jQuery(this).remove();
				});
			} else {
				element.removeClass('lazyload_loading').css({'opacity': '', 'transition': ''});
			}
		} 

		// Loading elementor background images
		if ( 'class-elem-bg-image' == type ) {
			element.addClass(className + ' lazyload_loading');

			// Get image URL
			var image = element.css('background-image');
			var subX = image.substring(0, 3);
			if (image != '' && image != 'none' && subX != 'lin' && subX != 'rad' ) {
				// Get image URL
				image = image.replace('url("', '').replace('")', '').replace("'", '').replace("'", '');

				// Get css properties
				var opacity = element.css('opacity');
				var transition = element.css('transition');
				transition = (transition != '' ? transition + ', ' : '') + 'opacity 0.3s ease, background-image 0s ease';

				// Hide and load image
				element.css({'opacity': '0', 'transition': 'opacity 0s ease'});

				jQuery('<img/>').attr('src', image).on('load', function() {	
					// Show loaded image	
					element.removeClass('lazyload_loading').css({'opacity': opacity, 'transition': transition});

					// Remove styles
					setTimeout(function(){
						element.css({'opacity': '', 'transition': ''});
					}, 300);

				   	jQuery(this).remove();
				});
			} else {
				element.removeClass('lazyload_loading');
			}
		} 

		// Loading video, audio and iframe
		if ( 'video' == type || 'audio' == type  || 'iframe' == type ) {
			var src = element.data('trx-lazyload-src');
			if ( src ) {
				element.removeAttr('data-trx-lazyload-src').attr('src', src).addClass(className); 
				$document.trigger('action.after_lazy_load_media');
			}
		}

		// Replace image in the video control
		if ( 'video-control' == type ) {
			var txt = element.data('video');
			if ( txt.search('data-trx-lazyload-src') > -1 ) {  
				txt = txt.replace(/ src="[^\s]+"/g, "");
				txt = txt.replace(/ data-trx-lazyload-src=/g, " src="); 
				element.attr('data-video', txt).addClass(className);

				// Preload image
				var image = txt.split(/ src="([^\s]+)"/); 
				if ( image[1] ) {
					jQuery('<img/>').attr('src', image[1]).on('load', function() {	
					   	jQuery(this).remove();
					});
				}
			}
		}
	}

	// Process image height
	function trx_addons_lazy_load_process_image_height() {		
		jQuery( 'img[data-trx-lazyload-src][data-trx-lazyload-height]' )
			.each( function() {
				var img = jQuery( this );
				// Get parent width
				var x = img.parent().width();
				// Get image width
				var img_x = img.width();
				var img_y = img.attr( 'height' );
				// Calculate image height
				if ( x > img_x  && img_y > 0) {
					var y = img_y / x * 100;
					img.css( 'padding-top', y + '%' );
				}
			} );
	}	

	// Parents of hidden elements
	function trx_addons_lazy_load_parents() {
		return ['.trx_addons_video_list_controller_item', '.adp-popup'];
	};

})();
	