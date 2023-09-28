<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v1.6.41
 */

extract( get_query_var('trx_addons_args_widget_flickr') );

extract( trx_addons_prepare_widgets_args( trx_addons_generate_id( 'widget_flickr_' ), 'widget_flickr' ) );

// Before widget (defined by themes)
trx_addons_show_layout($before_widget);
			
// Widget title if one was input (before and after defined by themes)
?><#
if (settings.title != '') {
	#><?php trx_addons_show_layout($before_title); ?><#
	print(settings.title);
	#><?php trx_addons_show_layout($after_title); ?><#
}

// Widget body
if (settings.flickr_api_key != '' && settings.flickr_username != '') {
	#><div class="flickr_images flickr_columns_{{ settings.flickr_columns }}"<#
		if (settings.flickr_columns_gap > 0) {
			print(' style="margin-right: -'+settings.flickr_columns_gap.size+settings.flickr_columns_gap.unit+' !important;"');
		}
	#>><#
		// Init hooks after the 1ms, because elementorFrontend.hooks isn't available on 'ready' event
		setTimeout(function(){
			if (typeof window.elementorFrontend !== 'undefined' && typeof window.elementorFrontend.hooks !== 'undefined') {
				// If Elementor is in the Editor's Preview mode
				if (elementorFrontend.isEditMode()) {
					// Init elements after creation
					elementorFrontend.hooks.addAction( 'frontend/element_ready/global', function( $cont ) {
						var flickr_images = $cont.find('.flickr_images');
						if (flickr_images.length == 0) return;
						settings.flickr_count.size = Math.max(1, settings.flickr_count.size);
						if (typeof trx_addons_refresh_flickr !== 'undefined' && trx_addons_refresh_flickr) {
							clearTimeout(trx_addons_refresh_flickr);
						}
						window.trx_addons_refresh_flickr = setTimeout(function() {
							jQuery.get('https://api.flickr.com/services/rest'
														+ '?method=flickr.people.getPhotos'
														+ '&user_id='+settings.flickr_username
														+ '&per_page='+settings.flickr_count.size
														+ '&api_key='+settings.flickr_api_key
														+ '&format=json'
														+ '&nojsoncallback=1')
									.done(function(response) {
										clearTimeout(window.trx_addons_refresh_flickr);
										window.trx_addons_refresh_flickr = null;
										if (response!='' && response!=0) {
											if (typeof response != 'object' && response.substr(0, 1) == '{') {
												try {
													response = JSON.parse(response);
												} catch (e) {
													console.log(response);
												}
											}
											if (typeof response == 'object' && response.stat=='ok' && typeof response.photos != 'undefined' && typeof response.photos.photo != 'undefined') {
												var html = '';
												_.each(response.photos.photo, function(v) {
													var url = 'https://farm' + v.farm + '.staticflickr.com/' + v.server + '/' + v.id + '_' + v.secret;
													var img = url + '_q.jpg';
													var img_big = url + '_b.jpg';
													var style = ( settings.flickr_columns.size > 0
																	? 'width:' + Math.round(100/settings.flickr_columns.size) + '%;'
																	: ''
																)
																+
																( settings.flickr_columns_gap.size > 0
																	? 'padding:0 '+settings.flickr_columns_gap.size+settings.flickr_columns_gap.unit+' '+settings.flickr_columns_gap.size+settings.flickr_columns_gap.unit+' 0;'
																	: ''
																);
													html += '<a href="' + img_big + '" title="' + _.escape(v.title) + '" style="' + style + '"">'
															+ '<img src="' + (settings.flickr_columns.size < 3 ? img_big : img) + '" alt="' + _.escape(v.title) + '" width="150" height="150">'
															+ '</a>';
												});
												flickr_images.html(html);
											}
										}
									});
						}, 500);
					} );
				}
			}
		}, typeof elementorFrontend === 'undefined' || typeof elementorFrontend.hooks === 'undefined' ? 1 : 0);
	#></div><#
}
#><?php	

// After widget (defined by themes)
trx_addons_show_layout($after_widget);
