(function($) {

	"use strict";

	$.fn.spasticNav = function(options) {
	
		options = $.extend({
			overlap : 0,
			style: 'box', //'line'
			reset : 50,
			color : '#00c6ff',
			colorOverride: true,
		}, options);
	
		return this.each( function() {
		
		 	var nav = $(this),
		 		currentPageItem = nav.find('>.current-menu-item,>.current-menu-parent,>.current-menu-ancestor'),	//>.current_page_parent
				hidden = false,
		 		blob,
		 		reset;
			if (currentPageItem.length === 0) {
		 		currentPageItem = nav.find('li').eq(0);
				hidden = true;
			}
			var a = currentPageItem.find('>a');
			var pl = parseInt(a.css('paddingLeft'), 10);
			if (isNaN(pl)) pl = 0;
		 	$('<li id="blob"></li>').css({
					width : options.style=='box' ? a.outerWidth() : a.width(),	//.css('width'),
					left : currentPageItem.position().left + (options.style=='box' ? 0 : pl),
					top : currentPageItem.position().top - options.overlap / 2,
					opacity: hidden ? 0 : 1
				}).appendTo(this);
		 	blob = $('#blob', nav);
			if (options.style == 'box') {
				blob.css({height: currentPageItem.outerHeight() + options.overlap});	//.css('height') + options.overlap
			}
			if (options.colorOverride) {
				var bg = a.css('backgroundColor');
				blob.css({backgroundColor : hidden || bg=='transparent' ? options.color : bg});
			}
					 	
			nav.find('>li:not(#blob)')
				.on( 'mouseenter', function() {
					clearTimeout(reset);
					var a = $(this).find('>a');
					var pl = parseInt(a.css('paddingLeft'), 10);
					if (isNaN(pl)) pl = 0;
					if (options.colorOverride) {
						var bg = a.css('backgroundColor');
						if (bg!='transparent') blob.css({backgroundColor: bg});
					}
					$(this).addClass('blob_over');
					blob.css({
						left: $(this).position().left + (options.style=='box' ? 0 : pl),
						top: $(this).position().top - options.overlap / 2,
						width: options.style=='box' ? a.outerWidth() : a.width(),	//.css('width')
						//height: $(this).outerHeight() + options.overlap,		//.css('height') + options.overlap,
						opacity: $(this).hasClass('menu-collapse') ? 0 : 1
					});

				} )
				.on( 'mouseleave', function() {
					reset = setTimeout(function() {
						var a = currentPageItem.find('>a');
						var pl = parseInt(a.css('paddingLeft'), 10);
						if (isNaN(pl)) pl = 0;
						if (options.colorOverride) {
							var bg = a.css('backgroundColor');
							if (bg!='transparent') blob.css({backgroundColor: bg});
						}
						blob.css({
							width: options.style=='box' ? a.outerWidth() : a.width(),
							left: currentPageItem.position().left + (options.style=='box' ? 0 : pl),
							opacity: hidden ? 0 : 1,
						});
					}, options.reset);
					$(this).removeClass('blob_over');
				} );
		
		}); // end each
	
	};

})(jQuery);