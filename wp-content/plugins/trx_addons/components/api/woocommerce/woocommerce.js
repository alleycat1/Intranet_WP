/* global jQuery */

(function() {
	"use strict";

	var $window   = jQuery( window ),
		$document = jQuery( document ),
		$body     = jQuery( 'body' );

	$document.on('action.before_init_trx_addons', function() {
		// Remove theme-animations inside sliders with WooCommerce products
		jQuery( '.slides.products .slider-slide' ).each( function() {
			var $self = jQuery( this );
			$self.find( '>[data-animation]' ).removeAttr( 'data-animation' );
			$self.find( '>[data-post-animation]' ).removeAttr( 'data-post-animation' );
		} );
	} );

	$document.on('action.ready_trx_addons', function() {

		// WooCommerce categories on homepages
		//----------------------------------------------------------

		// Add arrows to the WooCommerce categories on homepages
		if ( ! $body.hasClass( 'woocommerce' ) ) {
			$body.find( '.widget_area:not(.footer_wrap) .widget_product_categories:not(.inited)' ).each( function() {
				var widget = jQuery(this).addClass('inited');
				widget.find('ul.product-categories .has_children > a').append('<span class="open_child_menu"></span>');
				widget.on('click', 'ul.product-categories.plain li a .open_child_menu', function(e) {
					var $a = jQuery(this).parent();
					if ($a.siblings('ul:visible').length > 0)
						$a.siblings('ul').slideUp().parent().removeClass('opened');
					else {
						jQuery(this).parents('li').siblings('li').find('ul:visible').slideUp().parent().removeClass('opened');
						$a.siblings('ul').slideDown().parent().addClass('opened');
					}
					e.preventDefault();
					return false;
				} );
			
				// Resize handlers
				jQuery(document).on('action.resize_trx_addons', function() {
					trx_addons_woocommerce_resize_actions();
				});
				trx_addons_woocommerce_resize_actions();
			
				// Switch popup menu / hierarchical list on product categories list placed in sidebar
				function trx_addons_woocommerce_resize_actions() {
					var cat_menu = widget.find('ul.product-categories');
					var sb = cat_menu.parents('.widget_area');
					if ( sb.length > 0 && cat_menu.length > 0 ) {
						if ( sb.width() == sb.parents('.content_wrap').width() ) {
							if ( cat_menu.hasClass('inited') ) {
								cat_menu.removeClass('inited').addClass('plain').superfish('destroy');
								cat_menu.find('ul.animated').removeClass('animated').addClass('no_animated');
							}
						} else {
							if ( ! cat_menu.hasClass('inited') ) {
								cat_menu.removeClass('plain').addClass('inited');
								cat_menu.find('ul.no_animated').removeClass('no_animated').addClass('animated');
								trx_addons_init_sfmenu('body:not(.woocommerce) .widget_area:not(.footer_wrap) .widget_product_categories ul.product-categories');
							}
						}
					}
				}
			});
		}

		
		// Extended attributes in single product
		//----------------------------------------------------------

		// Change a behaviour to AJAX for the button 'Add to Cart' placed inside the popup 'Quick View'
		function trx_addons_woocommerce_add_to_cart_ajax( type ) {
			var $popup = $body.find( ( type == 'popup_yith' ? '>#yith-quick-view-modal' : '>.mfp-wrap' ) );
			if ( $popup.length ) {
				var $bt = $popup.find( '.single_add_to_cart_button' );
				if ( $bt.length ) {
					$bt.addClass( 'add_to_cart_button ajax_add_to_cart' )
						.attr( 'data-product_id', $bt.attr( 'value' ) );
					$popup.find( '.input-text.qty' )
						.on( 'change', function() {
							$bt.attr( 'data-quantity', jQuery( this ).val() );
						} );
				}
			}
		}
		if ( trx_addons_apply_filters( 'trx_addons_filter_ajax_add_to_cart_in_quick_view', true ) ) {
			jQuery( document.body ).on( 'woosq_loaded', function() {
				trx_addons_woocommerce_add_to_cart_ajax( 'popup_wpc' );
			} );
			$document.on( 'qv_loader_stop', function() {
				trx_addons_woocommerce_add_to_cart_ajax( 'popup_yith' );
			} );
		}

		// Init product attributes with variations
		jQuery( document.body ).on( 'woosq_loaded', function( e ) {
			trx_addons_woocommerce_init_variations( 'popup_wpc', $body );
		} );
		$body.on( 'wcpt_product_modal_ready', function( e ) {
			$document.trigger( 'action.init_hidden_elements', [$body] );
		} );

		$document.on( 'action.init_hidden_elements qv_loader_stop', function( e, cont ) {
			if ( ! cont ) cont = $body;
			trx_addons_woocommerce_init_variations( e.type == 'qv_loader_stop' ? 'popup_yith' : 'init_hidden', cont );
		} );

		// Check available product variations
		function trx_addons_woocommerce_init_variations( type, cont ) {
			cont.find( '.variations_form.cart:not(.inited)' ).each( function() {
				var form = jQuery(this).addClass('inited');
				var trx_addons_attribs = form.find('.trx_addons_attrib_item');
				if ( trx_addons_attribs.length === 0 ) return;
				
				// First check after variation form inited
				form.on( 'wc_variation_form', function( variation_form ) {
					form.on( 'check_variations', function() {
						trx_addons_woocommerce_check_variations( form );
					} );
					trx_addons_woocommerce_check_variations( form );
				} );

				// Click on our variations attribs
				trx_addons_attribs.on( 'click', function(e) {
					var $attrib = jQuery(this);
					e.preventDefault();
					if ( ! $attrib.hasClass('trx_addons_attrib_disabled') ) {
						$attrib.toggleClass('trx_addons_attrib_selected').siblings().removeClass('trx_addons_attrib_selected');
						var term = $attrib.hasClass('trx_addons_attrib_selected') ? $attrib.data('value') : '';
						if ( term === '' ) {
							$attrib.siblings('.trx_addons_attrib_item[data-value=""]').addClass('trx_addons_attrib_selected');
						}
						var attrib_name = $attrib.parents('.trx_addons_attrib_extended').data('attrib');
						$attrib.parents('.trx_addons_attrib_extended').parent().find('#'+attrib_name).val(term).trigger('change');
					}
					return false;
				} );
			} );
		}
		
		function trx_addons_woocommerce_check_variations( form ) {
			// Refresh attributes on selects are changed
			form.find( '.variations select' ).each( function() {
				var select_box = jQuery(this),
					select_val = select_box.val(),
					attrib_box = select_box.siblings('.trx_addons_attrib_extended').length == 1 
									? select_box.siblings('.trx_addons_attrib_extended')
									: select_box.parent().siblings('.trx_addons_attrib_extended');
				attrib_box.find('.trx_addons_attrib_item').removeClass('trx_addons_attrib_selected').addClass('trx_addons_attrib_disabled');
				select_box.find('option').each( function() {
					var opt = jQuery( this );
					attrib_box.find( '.trx_addons_attrib_item[data-value="' + opt.val() + '"]' )
								.removeClass( 'trx_addons_attrib_disabled' )
								.toggleClass( 'trx_addons_attrib_selected', opt.val() == select_val );//opt.get(0).selected
				} );
			} );
		}

		
		// Extended attributes in the products list (shop page or category/tag list)
		//----------------------------------------------------------
		if ( true || $body.hasClass( 'woocommerce' ) && $body.hasClass( 'archive' ) ) {
			$document.on( 'action.init_hidden_elements', function( e, cont ) {
				// If products with attributes are not present on this page - exit
				if ( ! jQuery( '.trx_addons_product_attributes' ).length ) return;
				// Click on the attribute - add filter
				cont
					.find( '.trx_addons_product_attribute_item_action_filter:not(.attribute_action_filter_inited)' )
					.addClass( 'attribute_action_filter_inited' )
					.on( 'click', function( e ) {
						var flt    = jQuery( this ),
							item   = flt.parent(),
							type   = item.data( 'type' ),
							attr   = item.data( 'attribute' ),
							value  = item.data( 'value' ),
							widget = jQuery( '.trx_addons_woocommerce_search_type_filter' ).eq(0);
						if ( widget.length ) {
							var widget_fld = widget.find( '.sc_form_field_' + attr );
							if ( widget_fld.length ) {
								var widget_item = widget_fld.find( '.sc_form_field_item[data-value="' + value + '"]' ).eq( 0 );
								if ( widget_item.length && ! widget_item.hasClass( 'sc_form_field_item_checked' ) ) {
									widget_item.trigger( 'click' );
									var apply = widget_fld.find( '.trx_addons_search_apply' );
									if ( apply.length ) {
										apply.trigger( 'click' );
									}
								}
							}
						}
						e.preventDefault();
						return false;
					} );
				// Click on the attribute - swap image
				var $swap_atts = cont
									.find( '.trx_addons_product_attribute_item_action_swap:not(.attribute_action_swap_inited)' )
									.addClass( 'attribute_action_swap_inited' );
				if ( $swap_atts.length ) {
					$swap_atts.on( 'click', function( e ) {
						var $link = jQuery( this ),
							$item = $link.parent(),
							active = $item.hasClass( 'trx_addons_product_attribute_item_active' ),
							disabled = $item.hasClass( 'trx_addons_product_attribute_item_disabled' );
						if ( ! disabled ) {
							$item.parents( '.trx_addons_product_attribute' )
								.find( '.trx_addons_product_attribute_item_active' )
								.removeClass( 'trx_addons_product_attribute_item_active' );
							swap_product_image( $item.toggleClass( 'trx_addons_product_attribute_item_active', ! active ) );
							check_available_variations( $item.parents( '.trx_addons_product_attributes' ) );
						}
						e.preventDefault();
						return false;
					} );
					// Swap image and display corresponding price on first run or after new products are loaded
					swap_product_image( $swap_atts.eq(0) );
					// Check variations on first run or after new products are loaded
					$swap_atts.parents( '.trx_addons_product_attributes' ).each( function() {
						check_available_variations( jQuery( this ) );
					} );
				}
				// Hover on the attribute - change the product image
				cont
					.find( '.trx_addons_product_attributes[data-product-variations] .trx_addons_product_attribute_item:not(.attribute_action_swap_inited)' )
					.addClass( 'attribute_action_swap_inited' )
					.on( 'focus mouseover', function( e ) {
						var $self = jQuery( this );
						if ( ! $self.find( '>.trx_addons_product_attribute_item_action_swap' ).length ) {
							swap_product_image( $self );
						}
					} );

				// Check available variations for product in the archive
				function check_available_variations( $wrap ) {
					var variations = $wrap.data( 'product-variations' ),
						variation_sel = {},
						$active_items = $wrap.find( '.trx_addons_product_attribute_item_active' );
					// Collect active items to the current variation
					$active_items.each( function( idx ) {
						variation_sel[ $active_items.eq( idx ).data( 'attribute' ) ] = $active_items.eq( idx ).data( 'value' );
					} );
					$wrap.find( '.trx_addons_product_attribute_item' ).each( function() {
						var $attr = jQuery( this ),
							attr_name = $attr.data( 'attribute' ),
							attr_value = $attr.data( 'value' ),
							attr_variation = trx_addons_object_clone( variation_sel );
						attr_variation[ attr_name ] = attr_value;
						$attr.toggleClass( 'trx_addons_product_attribute_item_disabled', get_closest_variation( variations, attr_variation ) === false );
					} );
				}

				// Get a closest variation
				function get_closest_variation( variations, variation_sel ) {
					var variation = false,
						variation_max = 0,
						variation_cur = 0;
					for ( var i = 0; i < variations.length; i++ ) {
						if ( ! variations[i].variation_is_active || ! variations[i].variation_is_visible ) {
							continue;
						}
						variation_cur = 0;
						for ( var attr in variation_sel ) {
							if ( ! variations[i].attributes['attribute_'+attr] ) {
								variation_cur += 1;
							} else if ( variations[i].attributes['attribute_' + attr] == variation_sel[attr] ) {
								variation_cur += 10;
							} else {
								variation_cur = 0;
								break;
							}
						}
						if ( variation_max < variation_cur ) {
							variation_max = variation_cur;
							variation = variations[i];
						}
					}
					return variation;
				}

				// Return true if all attributes from variation are selected
				function is_full_variation_present( variations, variation_sel_count ) {
					var variation_total = 0;
					for ( var i = 0; i < variations.length; i++ ) {
						if ( ! variations[i].variation_is_active || ! variations[i].variation_is_visible ) {
							continue;
						}
						for ( var p in variations[i].attributes ) {
							if ( variations[i].attributes.hasOwnProperty( p ) ) {
								variation_total++;
							}
						}
						break;
					}
					return variation_total == variation_sel_count;
				}

				// Swap a product image on an attribute hover or click
				function swap_product_image( $flt ) {
					var $wrap = $flt.parents( '.trx_addons_product_attributes' ),
						$product = $flt.parents( '.product' ),
						variations = $wrap.data( 'product-variations' ),
						variation = false,
						variation_sel = {},
						variation_sel_count = 0;
					if ( variations ) {
						// Collect active items to the current variation
						var $active_items = $wrap.find( '.trx_addons_product_attribute_item_action_swap' ).length
												? $wrap.find( '.trx_addons_product_attribute_item_active' )
												: $flt;
						$active_items.each( function( idx ) {
							variation_sel[ $active_items.eq( idx ).data( 'attribute' ) ] = $active_items.eq( idx ).data( 'value' );
							variation_sel_count++;
						} );
						// Get a closest variation
						if ( variation_sel_count ) {
							variation = get_closest_variation( variations, variation_sel );
						}
						// Swap image if a variation found
						// or change back the original image if a variation with selected atributes is not found
						var $img = $product.find( 'img[class*="attachment-woocommerce"],img[class*="woocommerce-placeholder"]' );
						if ( $img.length ) {
							if ( ! $img.data( 'src-old' ) ) {
								$img.data( {
									'src-old': $img.attr( 'src' ),
									'srcset-old': $img.attr( 'srcset' ),
									'sizes-old': $img.attr( 'sizes' )
								} );
							}
							if ( variation ) {
								$img.attr( {
									'src': variation.image.src,
									'srcset': variation.image.srcset ? variation.image.srcset : '',
									'sizes': variation.image.sizes ? variation.image.sizes : ''
								} );
							} else {
								$img.attr( {
									'src': $img.data( 'src-old' ),
									'srcset': $img.data( 'srcset-old' ),
									'sizes': $img.data( 'sizes-old' )
								} );
							}
						}
						// Swap price if a full variation found (all attributes present in the item and selected)
						// or change back the original price if a variation with selected atributes is not found
						if ( trx_addons_apply_filters( 'trx_addons_filter_swap_price_for_variable_products', is_full_variation_present( variations, variation_sel_count ) ) ) {
							var $price = $product.find( '.price_wrap' );
							if ( ! $price.length ) {
								$price = $product.find( '.price' );
							}
							if ( $price.length ) {
								if ( ! $price.data( 'price-old' ) ) {
									$price.data( {
										'price-old': $price.html()
									} );
								}
								if ( variation ) {
									// A 'price_html' can be empty if all variations have an equal price.
									// In this case leave a price unchanged.
									if ( variation.price_html != '' ) {
										$price.html( $price.hasClass( 'price_wrap' ) ? variation.price_html : jQuery( variation.price_html ).html() );
									}
								} else {
									$price.html( $price.data( 'price-old' ) );
								}
							}
						}
					}
				}
			} );
		}


		// WooCommerce Search Widget
		//----------------------------------------------------------
		
		var reopen_after_reload = trx_addons_apply_filters( 'trx_addons_filter_reopen_filter_after_reload_products', false ),
			reopened = false,
			reload_is_busy = false,
			reload_is_allowed = true,
			last_clicked_item = '',
			last_opened_filter = '',
			inline_css_selector = trx_addons_apply_filters( 'trx_addons_filter_reload_inline_css_selector', '#trx_addons-inline-styles-inline-css' ),
			inline_css_wrap = jQuery( inline_css_selector ),
			inline_css_start = trx_addons_apply_filters( 'trx_addons_filter_reload_inline_css_start', '#woocommerce_output_start{}' ),
			inline_css_end = trx_addons_apply_filters( 'trx_addons_filter_reload_inline_css_end', '#woocommerce_output_end{}' ),
			list_products_selector = trx_addons_apply_filters( 'trx_addons_filter_reload_products_selector', '.list_products' ),
			list_products_wrap = jQuery( list_products_selector ),
			list_products_loading = 0,
			list_products_loading_class = '.trx_addons_loading',
			list_products_loading_selector  = trx_addons_apply_filters( 'trx_addons_filter_reload_products_loading_wrap_selector',
																		'.list_products .products,.list_products .woocommerce-info'
																		),
			list_products_loading_html  = trx_addons_apply_filters( 'trx_addons_filter_reload_products_loading_html',
																	'<div class="trx_addons_loading"></div>'
																	),
			single_product_selector = trx_addons_apply_filters( 'trx_addons_filter_single_product_selector', '.post_item_single.post_type_product' ),
			mask_fields_wrap_on_loading = true;

		$document.on( 'action.init_hidden_elements', function() {

			// Reinit search form after page reloaded
			var search_forms = jQuery('.trx_addons_woocommerce_search_form:not(.inited)');
			if ( search_forms.length ) {
				search_forms.each( function() {
					var form   = jQuery(this).addClass('inited'),
						widget = form.parents('.trx_addons_woocommerce_search'),
						type   = widget.hasClass( 'trx_addons_woocommerce_search_type_filter')
									? 'filter'
									: ( widget.hasClass( 'trx_addons_woocommerce_search_type_form')
										? 'form'
										: 'inline'
										),
						apply  = widget.hasClass( 'trx_addons_woocommerce_search_apply' ),
						ajax   = widget.hasClass( 'trx_addons_woocommerce_search_ajax' ),
						number = widget.data('number') || 1,
						expanded = widget.data('expanded') || 0;

					form
						// Submit form ('inline' or 'form' styles)
						.on('submit', function(e) {
							var shop_url = form.attr('action');
							var params = trx_addons_woocommerce_search_form_get_params(form);
							if (params !== false) {
								window.location.href = trx_addons_add_to_url(shop_url, params);
							} else {
								e.preventDefault();
							}
							return false;
						})
						// Enable/Disable submit button ('inline' or 'form' styles)
						.on('change', 'select,input', function(e) {
							var button = form.find('.trx_addons_woocommerce_search_button');
							if ( button.length ) {
								var params = trx_addons_woocommerce_search_form_get_params(form);
								if (params === false) {
									button.attr('disable', 'disable');
								} else {
									button.removeAttr('disable');
								}
							}
						})
						// Open/Close dropdown with items ('inline' style)
						.on('click', '.trx_addons_woocommerce_search_form_field_label', function(e) {
							jQuery(this)
								.parent('.trx_addons_woocommerce_search_form_field')
								.siblings('.trx_addons_woocommerce_search_form_field')
								.find('.trx_addons_woocommerce_search_form_field_list').slideUp();
							jQuery(this).siblings('.trx_addons_woocommerce_search_form_field_list').slideToggle();
							e.preventDefault();
							return false;
						})
						// Select item in the 'inline' form
						.on('click', '.trx_addons_woocommerce_search_form_field_list li', function(e) {
							var list = jQuery(this).parent();
							list.siblings('.trx_addons_woocommerce_search_form_field_label').html(jQuery(this).html());
							list.siblings('input[type="hidden"]').val(jQuery(this).data('value'));
							list.slideUp();
							e.preventDefault();
							return false;
						})
						// Keypress on the field's title
						.on('keyup', '.sc_form_field_title', function(e) {
							if ( type == 'filter' ) {
								if ( e.keyCode == 13 ) {
									jQuery(this).trigger('click');
								}
							}
						})
						// Open/Close dropdown with items ('filter' style)
						.on('click', '.sc_form_field_title', function(e) {
							if ( type == 'filter' ) {
								var $self  = jQuery(this),
									field  = $self.parent(),
									opened = field.hasClass('sc_form_field_opened');
								// Close all opened fields
								if ( $self.parents('.trx_addons_woocommerce_tools').length !== 0
									&& $self.parents('.trx_addons_woocommerce_search_form_fields_wrap_opened').length === 0
								) {
									$self
										.parents('.trx_addons_woocommerce_search_form_fields_wrap')
										.find('.sc_form_field_opened').each( function() {
											var cur_field = jQuery( this ),
												cur_param = cur_field.find( '.sc_form_field_param' ),
												cur_apply = cur_field.find( '.trx_addons_search_apply' ),
												need_apply = apply && cur_apply.length && ! cur_apply.attr('disabled') && cur_param.data('changed');
											if ( need_apply ) {
												cur_apply.trigger( 'click' );
											} else {
												trx_addons_woocommerce_search_form_close_field( cur_field );	// , apply
											}
										} );
								} else if ( opened ) {
									var cur_apply = field.find( '.trx_addons_search_apply' ),
										cur_param = field.find( '.sc_form_field_param' ),
										need_apply = apply && cur_apply.length && ! cur_apply.attr('disabled') && cur_param.data('changed');
									if ( false && need_apply ) {	// Don't apply on click a title in sidebar or mobile
										cur_apply.trigger( 'click' );
									} else {
										trx_addons_woocommerce_search_form_close_field( field );	// , apply
									}
								}
								// Open clicked field
								if ( ! opened ) {
									trx_addons_woocommerce_search_form_open_field( field );
								}
								e.preventDefault();
								return false;
							}
						})
						// Keypress on the field's title
						.on('keyup', '.sc_form_field_item', function(e) {
							if ( type == 'filter' ) {
								if ( e.keyCode == 13 ) {
									jQuery(this).trigger('click');
								} else if ( e.keyCode == 27 ) {
									jQuery(this).parents('.sc_form_field').find('.sc_form_field_title').focus().trigger('click');
								}
							}
						})
						// Check item in the select ('filter' style)
						.on('click', '.sc_form_field .sc_form_field_item', function(e) {
							if ( type == 'filter' ) {
								var $self = jQuery(this),
									wrap  = $self.parents('.sc_form_field_wrap'),
									multi = $self.parents('.sc_form_field').data('multiple') == '1';
								last_clicked_item = $self.data('value');
								if ( ! multi ) {
									wrap.find('.sc_form_field_item_checked').removeClass('sc_form_field_item_checked');
								}
								$self.toggleClass('sc_form_field_item_checked');
								var value = trx_addons_woocommerce_search_form_get_multiple_field_value( wrap );
								wrap.find( '.sc_form_field_param' ).val( value ).trigger( 'change' );
								e.preventDefault();
								return false;
							}
						})
						// All fields: Keypress on the 'Clear all' button
						.on('keyup', '.trx_addons_woocommerce_search_clear_all', function(e) {
							if ( type == 'filter' ) {
								if ( e.keyCode == 27 ) {
									jQuery(this).siblings('.trx_addons_woocommerce_search_close').trigger('click');
								}
							}
						})
						// All fields: Clear all selected items in the multiselect fields
						.on('click', '.trx_addons_woocommerce_search_clear_all', function(e) {
							e.preventDefault();
							if ( type == 'filter' ) {
								var need_reload = false;
								reload_is_allowed = false;
								form.find('.sc_form_field').each( function() {
									var $self = jQuery(this),
										param = $self.find( '.sc_form_field_param' );
									if ( param.attr('name') != 'product_cat' && param.val() !== '' ) {
										var field = param.parents('.sc_form_field'),
											value = '';
										if ( field.hasClass( 'sc_form_field_slider' ) ) {
											value = param.next().data('min');
										} else if ( field.hasClass( 'sc_form_field_range' ) ) {
											value = param.next().data('min') + ',' + param.next().data('max');
										}
										need_reload = true;
										param.val( value ).trigger( 'change' );
										trx_addons_woocommerce_search_form_restore_field_view( $self );
									}
								} );
								reload_is_allowed = true;
								if ( need_reload ) {
									trx_addons_woocommerce_search_form_reload_products( form );
								}
							}
							return false;
						})
						// Keypress on the 'Clear all' button
						.on('keyup', '.sc_form_field_items_selected_clear', function(e) {
							if ( type == 'filter' ) {
								if ( e.keyCode == 27 ) {
									jQuery(this).parents('.sc_form_field').find('.sc_form_field_title').focus().trigger('click');
								}
							}
						})
						// Clear all selected items in the multiselect fields
						.on('click', '.sc_form_field_items_selected_clear', function(e) {
							e.preventDefault();
							if ( type == 'filter' ) {
								var $self = jQuery(this),
									wrap  = $self.parents('.sc_form_field_wrap');
								wrap.find( '.sc_form_field_param' ).val( '' ).trigger( 'change' );
								trx_addons_woocommerce_search_form_restore_field_view( wrap.parents( '.sc_form_field' ) );
							}
							return false;
						})
						// Keypress on the 'Select all' button
						.on('keyup', '.sc_form_field_items_selected_select_all', function(e) {
							if ( type == 'filter' ) {
								if ( e.keyCode == 27 ) {
									jQuery(this).parents('.sc_form_field').find('.sc_form_field_title').focus().trigger('click');
								}
							}
						})
						// Select all items in the multiselect fields
						.on('click', '.sc_form_field_items_selected_select_all', function(e) {
							e.preventDefault();
							if ( type == 'filter' ) {
								var $self = jQuery(this),
									wrap  = $self.parents('.sc_form_field_wrap');
								wrap.find('.sc_form_field_item').toggleClass('sc_form_field_item_checked', true);
								var value = trx_addons_woocommerce_search_form_get_multiple_field_value( wrap );
								wrap.find( '.sc_form_field_param' ).val( value ).trigger( 'change' );
							}
							return false;
						})
						// Update 'param' field on change text ('filter' style)
						.on('change', '.sc_form_field_text .sc_form_field_input', function(e) {
							if ( type == 'filter' ) {
								var $self = jQuery(this),
									wrap  = $self.parents('.sc_form_field_wrap');
								wrap.find( '.sc_form_field_param' ).val( $self.val() ).trigger( 'change' );
							}
						})
						// Prevent submit form on Enter in the text field
						.on('keydown', '.sc_form_field_text .sc_form_field_input', function(e) {
							if ( type == 'filter' ) {
								var $buttons = jQuery(this).parents('.sc_form_field_wrap').find('.sc_form_field_buttons');
								if ( $buttons.length ) {
									if ( e.keyCode == 13 ) {
										var $apply = $buttons.find('.trx_addons_search_apply');
										if ( $apply.length ) {
											e.preventDefault();
											jQuery(this).trigger('change');
											$apply.trigger('click');
										}
									} else if ( e.keyCode == 27 ) {
										var $cancel = $buttons.find('.trx_addons_search_cancel');
										if ( $cancel.length ) {
											e.preventDefault();
											$cancel.trigger('click');
										}
									}
								}
							}
						})
						// Update result in the price range ('filter' style)
						.on('change', '.sc_form_field_price .sc_form_field_param', function(e) {
							if ( type == 'filter' ) {
								var $self  = jQuery(this),
									value  = $self.val().split(','),
									slider = $self.next('.trx_addons_range_slider '),
									result = slider.next('.trx_addons_range_result');
								if ( result.length ) {
									result.find('.trx_addons_range_result_value').html( value[0] + ( value.length > 1 ? ' - ' + value[1] : '' ) );
								}
							}
						})
						// Mark title as filled and update selected total value on param changed ('filter' style)
						.on('change', '.sc_form_field_param', function(e) {
							if ( type == 'filter' ) {
								var $self = jQuery(this).data('changed', 1);
								$self.parents('.trx_addons_woocommerce_search_form_fields_wrap').toggleClass('trx_addons_woocommerce_search_form_fields_changed', true);
								trx_addons_woocommerce_search_form_param_changed( $self );
							}
						} )
						// Remove attr 'disabled' from buttons 'Apply' and 'Cancel' on param changed ('filter' style)
						.on('change', '.sc_form_field_param', function(e) {
							if ( type == 'filter' ) {
								var field_wrap = jQuery(this).parents('.sc_form_field_wrap');
								field_wrap.find('.trx_addons_search_apply').removeAttr('disabled');
								field_wrap.find('.trx_addons_search_cancel').removeAttr('disabled');
							}
						} )
						// Update available (filtered) products counter on param changed ('filter' style)
						.on('change', '.sc_form_field_param', function(e) {
							var $self = jQuery(this),
								fields_wrap = $self.parents('.trx_addons_woocommerce_search_form_fields_wrap').data('param-changed', 1);
							if ( type == 'filter'
									&& $self.attr('name') != 'product_cat'
									&& ( fields_wrap.hasClass('trx_addons_woocommerce_search_form_fields_wrap_opened')
										||
										( fields_wrap.parents('.sidebar').length && ( apply || ajax ) )
										)
							) {
								trx_addons_woocommerce_search_form_update_available_products_counter( form );
							}
						} )
						// Reload page on any param are changed, except 's' and 'price' ('filter' style)
						.on('change', '.sc_form_field_param', function(e) {
							var $self = jQuery(this);
							if ( type == 'filter' && reload_is_allowed ) {
								if ( $self.attr('name') == 'product_cat' ) {
									var $fld = $self.parents('.sc_form_field_opened'),
										fld_idx = $fld.index();
									if ( fld_idx > expanded ) {
										trx_addons_woocommerce_search_form_close_field( $fld );
									}
									trx_addons_woocommerce_search_form_reload_products( form, $self, '.sc_form_field_param[name="product_cat"]', true );
								} else if ( ! apply
											&& ! $self.parents('.trx_addons_woocommerce_search_form_fields_wrap').hasClass('trx_addons_woocommerce_search_form_fields_wrap_opened')
											&& ( ['price', 's'].indexOf($self.attr('name')) == -1 || $self.parents('.sidebar').length )
								) {
									trx_addons_woocommerce_search_form_reload_products_after_timeout( form, $self );
								}
							}
						})
						// Reload page on click button 'Apply' ('filter' style)
						.on('click', '.trx_addons_search_apply', function(e) {
							if ( type == 'filter' ) {
								var $self = jQuery(this);
								if ( ! $self.attr('disabled') ) {
									if ( ! $self.parents('.trx_addons_woocommerce_search_form_fields_wrap').hasClass('trx_addons_woocommerce_search_form_fields_wrap_opened')
										&& $self.parents('.sidebar').length === 0
									) {
										trx_addons_woocommerce_search_form_close_field( $self.parents('.sc_form_field_opened') );
									}
									trx_addons_woocommerce_search_form_reload_products( form, $self.parents('.sc_form_field').find('.sc_form_field_param') );
								}
								e.preventDefault();
								return false;
							}
						})
						// Restore values on click button 'Cancel' ('filter' style)
						.on('click', '.trx_addons_search_cancel', function(e) {
							if ( type == 'filter' ) {
								var $self = jQuery(this);
								if ( ! $self.attr('disabled') ) {
									trx_addons_woocommerce_search_form_close_field( $self.parents('.sc_form_field_opened'), true );
								}
								e.preventDefault();
								return false;
							}
						})
						// Open/Close panel with filters
						.on('click', '.trx_addons_woocommerce_search_button_filters,'
									+'.trx_addons_woocommerce_search_button_show,'
									+'.trx_addons_woocommerce_search_close',
							function(e) {
								var $self = jQuery(this),
									applied = false,
									wrap = form.find('.trx_addons_woocommerce_search_form_fields_wrap');
								if ( $self.hasClass('trx_addons_woocommerce_search_button_filters') && ! wrap.hasClass('trx_addons_woocommerce_search_form_fields_wrap_opened') ) {
									wrap.addClass('trx_addons_woocommerce_search_form_fields_wrap_opened');
								} else {
									if ( wrap.hasClass('trx_addons_woocommerce_search_form_fields_wrap_opened')
										||
										( $self.parents('.sidebar').length
											&&
											$self.parents('.trx_addons_woocommerce_search').hasClass('trx_addons_woocommerce_search_apply')
											)
									) {
										if ( wrap.data('param-changed') > 0 ) {
											var last_apply = last_opened_filter
																? form.find('input[name="'+last_opened_filter+'"]')
																	.parents('.sc_form_field')
																	.find('.trx_addons_search_apply:not([disabled])')
																: false;
											if ( ! last_apply || ! last_apply.length || last_apply.attr('disabled') == 'disabled' ) {
												last_apply = form.find('.trx_addons_search_apply:not([disabled])').eq(0);
											}
											applied = true;
											if ( last_apply && last_apply.length && last_apply.attr('disabled') != 'disabled' ) {
												last_apply.trigger( 'click' );
											} else {
												trx_addons_woocommerce_search_form_reload_products( form );
											}
										}
									}
									if ( true || ! applied ) {	// Always hide fields wrap on mobile
										wrap.removeClass('trx_addons_woocommerce_search_form_fields_wrap_opened');
									}
								}
								e.preventDefault();
								return false;
							}
						);
				
					// Open initially toggled fields
					if ( form.parents('.sidebar').length > 0 ) {
						form.find('.sc_form_field_expanded').each( function() {
							trx_addons_woocommerce_search_form_open_field( jQuery(this).removeClass('sc_form_field_expanded'), 'show', false );
						} );
					}

					// Show/hide 'Clear all' on fields changed (exclude the field "Product category")
					form.find('.trx_addons_woocommerce_search_form_fields_wrap')
						.toggleClass( 'trx_addons_woocommerce_search_form_fields_filled', 
										form.find('.sc_form_field:not(.sc_form_field_product_cat) .sc_form_field_title_filled').length > 0
									);

					// Reopen filter after reload products page ('filter' style)
					if ( type == 'filter' && ! reopened ) {
						reopened = true;
						last_opened_filter = trx_addons_get_value_gp( 'last_filter' );
						if ( last_opened_filter ) {
							var last_stop = trx_addons_get_value_gp( 'last_stop' );
							if ( last_stop > 0 ) {
								trx_addons_document_animate_to( last_stop );
							}
							if ( reopen_after_reload && ! apply ) {
								// Reopen fields wrap on mobile
								/*
								var fields_wrap = form.find('.trx_addons_woocommerce_search_form_fields_wrap');
								if ( fields_wrap.length && ! fields_wrap.hasClass('trx_addons_woocommerce_search_form_fields_wrap_opened') && fields_wrap.css('position') == 'fixed' ) {
									fields_wrap.addClass('trx_addons_woocommerce_search_form_fields_wrap_opened trx_addons_woocommerce_search_form_fields_wrap_show');
									setTimeout( function() {
										fields_wrap.removeClass('trx_addons_woocommerce_search_form_fields_wrap_show');
									}, 500 );
								}
								*/
								// Reopen last active field
								var field = form.find('input[name="'+last_opened_filter+'"]').parents('.sc_form_field');
								trx_addons_woocommerce_search_form_open_field( field );
								last_clicked_item = trx_addons_get_value_gp( 'last_item' );
								if ( last_clicked_item ) {
									field.find('.sc_form_field_item[data-value="'+last_clicked_item+'"]').focus();
								}
							}
						}
					}

					// Reload products ('filter' style)
					function trx_addons_woocommerce_search_form_reload_products( form, filter, selector, force_reload_page ) {
						// Get all filters to the params array
						if ( selector === undefined ) {
							selector = '.sc_form_field_param';
						}
						var params = trx_addons_woocommerce_search_form_get_params( form, selector );
						// If any filter selected
						if ( params !== false ) {
							// Close last filter field
							if ( filter
								&& ( ! ajax || apply || force_reload_page )
								&& filter.parents('.trx_addons_woocommerce_tools').length !== 0
								&& filter.parents('.trx_addons_woocommerce_search_form_fields_wrap_opened').length === 0
							) {
								trx_addons_woocommerce_search_form_close_field( filter.parents('.sc_form_field_opened') );
							}
							// Add filter's name and window scroll position to the parameters
							if ( last_opened_filter ) {
								params['last_filter'] = last_opened_filter;
							}
							if ( last_clicked_item ) {
								params['last_item'] = last_clicked_item;
							}
							// Add scroll top position to the query
							params['last_stop'] = jQuery(window).scrollTop();
							// Make query url
							var shop_url = trx_addons_add_to_url(form.attr('action'), params),
								fields_wrap = form.find('.trx_addons_woocommerce_search_form_fields_wrap').data('param-changed', 0);
							// Reload products by AJAX
							if ( ajax && ! force_reload_page ) {
								trx_addons_woocommerce_search_form_get_products_from_url( shop_url, fields_wrap );
							// Redirect page
							} else {
								jQuery( list_products_loading_selector )
									.addClass( 'trx_addons_woocommerce_search_loading' )
									.append( list_products_loading_html );
								window.location.href = shop_url;
							}
						}
					}

					// Reload products after timeout ('filter' style)
					var trx_addons_woocommerce_search_form_reload_products_after_timeout = trx_addons_throttle(
							trx_addons_woocommerce_search_form_reload_products,
							trx_addons_apply_filters( 'trx_addons_filter_reload_products_timeout', ajax ? 500 : 0 ),
							true
						);

					// Update available products counter ('filter' style)
					var trx_addons_woocommerce_search_form_update_available_products_counter = trx_addons_throttle(
						function( form ) {
							// Get all filters to the params array
							var params = trx_addons_woocommerce_search_form_get_params( form, '.sc_form_field_param' );
							// If any filter selected
							if ( params !== false ) {
								var shop_url = trx_addons_add_to_url(form.attr('action'), params),
									counter  = form.find('.trx_addons_woocommerce_search_button_show_total');
								if ( counter.length ) {
									jQuery.get( shop_url ).done( function( response ) {
										var value = jQuery( response ).find('.trx_addons_woocommerce_search_button_show_total').html();
										if ( value !== '') {
											counter.html( value );
										}
									} );
								}
							}
						},
						trx_addons_apply_filters( 'trx_addons_filter_update_products_counter_timeout', 500 ),
						true
					);

				});
			}

			// WooCommerce NavFilters: AJAX reload products on filter removed (if woocommerce_search_filter is present near this widget)
			jQuery( '.widget_layered_nav_filters:not(.trx_addons_woocommerce_search_compatibility_inited)' ).each( function() {
				var $self = jQuery(this).addClass('trx_addons_woocommerce_search_compatibility_inited'),
					widget_search = $self.siblings('.widget_woocommerce_search');
				// Allow AJAX reload on filter removed only if widget 'Product Filter' is present on same sidebar
				// and ajax search is turned on for the widget
				// and field 'rating' is not present in the fields set (because WooCommerce don't mark this field in the widget Active filters)
				if ( widget_search.length
					&& widget_search.find('.trx_addons_woocommerce_search_ajax').length
					&& widget_search.find('.sc_form_field_rating').length === 0 
				) {
					$self.find('a').on('click', function(e) {
						var $link = jQuery(this),
							list = $link.parents('ul').eq(0),
							url = location.href;	//$link.attr('href');
						e.preventDefault();
						// Remove current filter from active filters
						$link.parents('li').eq(0).fadeOut( 200, function() {
							jQuery(this).remove();
							// Prepare url - remove not exists filters
							var parts = url.split('?'),
								query = trx_addons_parse_query_string(url),
								query_new = {},
								query_val = [],
								query_val_new = [];
							for (var i in query) {
								if ( i == 'min_price' || i == 'max_price' ) {
									list.find('.chosen .amount').each( function() {
										if ( jQuery(this).text().replace(/[\$\.\,]/g, '') == query[i].replace(/[\$\.\,]/g, '') ) {
											query_new[i] = query[i];
										}
									} );
								} else if ( i.substring(0, 7) == 'filter_' ) {
									query_val = query[i].split(',');
									query_val_new = [];
									for (var j = 0; j < query_val.length; j++ ) {
										if ( list.find('.chosen-' + i.substring(7) + '-' +query_val[j] ).length ) {
											query_val_new.push( query_val[j] );
										}
									}
									if ( query_val_new.length > 0 ) {
										query_new[i] = query_val_new.join(',');
									}
								} else {
									query_new[i] = query[i];
								}
							}
							trx_addons_woocommerce_search_form_get_products_from_url( trx_addons_add_to_url( parts[0], trx_addons_woocommerce_search_form_add_orderby_to_query_params( query_new ) ) );
						} );
						return false;
					} );
				}
			} );

			// WooCommerce Ordering: Reload products on 'orderby' is changed
			jQuery('.woocommerce-ordering:not(.trx_addons_woocommerce_search_compatibility_inited)').each( function() {
				var form = jQuery( this ).addClass( 'trx_addons_woocommerce_search_compatibility_inited' ),
					select = form.find( '.orderby' ),
					widget_search = list_products_wrap.find( '.widget_woocommerce_search' ),
					url = location.href.split( '?' );
				if ( widget_search.length === 0 ) {
					widget_search = jQuery( '.trx_addons_woocommerce_search_type_filter' ).eq(0).parents( '.widget_woocommerce_search' );
				}
				if ( widget_search.length ) {
					select.on( 'change', function(e) {
						var params = trx_addons_woocommerce_search_form_get_params( widget_search, '.sc_form_field_param' );
						/*
						var page = form.find('input[name="paged"]').val() || 1;
						if ( page > 1 ) {
							params['page'] = page;
						}
						*/
						// Prevent to execute other scripts
						e.preventDefault();
						e.stopImmediatePropagation();
						// If a widget with filters present and its mode is AJAX - get products via AJAX
						if ( widget_search.find('.trx_addons_woocommerce_search_ajax').length ) {
							trx_addons_woocommerce_search_form_get_products_from_url( trx_addons_add_to_url( url[0], params ) );
						// Else - reload a current page with new params
						} else {
							jQuery( list_products_loading_selector )
								.addClass( 'trx_addons_woocommerce_search_loading' )
								.append( list_products_loading_html );
							window.location.href = trx_addons_add_to_url( url[0], params );
						}
						return false;
					} );
				}
			} );

			// Open field ('filter' style)
			function trx_addons_woocommerce_search_form_open_field( field, open_style, need_focus ) {
				if ( open_style === undefined ) {
					open_style = 'slideDown';
				}
				if ( need_focus === undefined ) {
					need_focus = true;
				}
				var open_time = open_style == 'show'
									? 0
									: trx_addons_apply_filters( 'trx_addons_filter_woocommerce_filter_show_time', field.parents('.trx_addons_woocommerce_tools').length === 0 || field.parents('.trx_addons_woocommerce_search_form_fields_wrap_opened').length > 0 ? 300 : 0 );
				// Save current value
				var param = field.find( '.sc_form_field_param' );
				param.data( 'old-value', param.val() );
				// Mark field unchanged
				param.data( 'changed', 0 );
				// Save last opened filter
				last_opened_filter = param.attr('name');
				// Set buttons 'Apply' and 'Cancel' to disabled
				if ( false && field.parents('.trx_addons_woocommerce_search').hasClass('trx_addons_woocommerce_search_apply') ) {
					field.find('.trx_addons_search_apply').attr('disabled', 'disabled');
					field.find('.trx_addons_search_cancel').attr('disabled', 'disabled');
				}
				// Open field wrap
				var wrap = field.addClass('sc_form_field_opened').find('.sc_form_field_wrap');
				if ( open_style == 'slideDown' && open_time > 0 ) {
					wrap.slideDown( open_time, function() {
						if ( need_focus && field.hasClass('sc_form_field_text') ) {
							field.find( 'input[type="text"]').focus();
						}
					} );
				} else {
					wrap.show();
					if ( need_focus && field.hasClass('sc_form_field_text') ) {
						field.find( 'input[type="text"]').focus();
					}
				}
			}

			// Close field ('filter' style)
			function trx_addons_woocommerce_search_form_close_field( field, restore_value, close_style ) {
				// Restore last value
				if ( restore_value ) {
					trx_addons_woocommerce_search_form_restore_field_value( field );
				}
				last_opened_filter = '';
				// Close field wrap
				field.removeClass('sc_form_field_opened');
				if ( close_style === undefined ) {
					close_style = 'slideUp';
				}
				var close_time = close_style == 'hide'
									? 0
									: trx_addons_apply_filters( 'trx_addons_filter_woocommerce_filter_show_time', field.parents('.trx_addons_woocommerce_tools').length === 0 || field.parents('.trx_addons_woocommerce_search_form_fields_wrap_opened').length > 0 ? 300 : 0 );
				if ( close_style == 'slideUp' && close_time > 0 ) {
					field.find('.sc_form_field_wrap').slideUp( close_time );
				} else {
					field.find('.sc_form_field_wrap').hide();
				}
			}

			// Restore field's value ('filter' style)
			function trx_addons_woocommerce_search_form_restore_field_value( field ) {
				var param = field.find('.sc_form_field_param'),
					value = param.data('old-value');
				// Restore old value in the param
				param.val( value );
				// Mark title as filled and update selected items and restore field's view (mark items as checked, restore search string, etc.)
				trx_addons_woocommerce_search_form_restore_field_view( field );
			}

			// Restore field's view ('filter' style)
			function trx_addons_woocommerce_search_form_restore_field_view( field ) {
				var param = field.find('.sc_form_field_param'),
					value = param.val();
				// Mark title as filled and update selected items
				trx_addons_woocommerce_search_form_param_changed( param );
				// Restore field's view (mark items as checked, restore search string, etc.)
				if ( field.hasClass('sc_form_field_select')
					|| field.hasClass('sc_form_field_image')
					|| field.hasClass('sc_form_field_color')
					|| field.hasClass('sc_form_field_button')
				) {
					field.find('.sc_form_field_item_checked').removeClass('sc_form_field_item_checked');
					var parts = ( '' + value ).split( ',' );
					for (var i = 0; i < parts.length; i++ ) {
						field.find('[data-value="'+parts[i]+'"]').addClass('sc_form_field_item_checked');
					}
				} else if ( field.hasClass('sc_form_field_text') ) {
					field.find('.sc_form_field_input').val( value );
				} else if ( field.hasClass('sc_form_field_slider') ) {
					field.find('.ui-slider').slider( "value", value );
				} else if ( field.hasClass('sc_form_field_range') ) {
					field.find('.ui-slider').slider( "values", ( '' + value ).split( ',' ) );
				}
			}

			// Mark title as filled and update selected total value on param changed ('filter' style)
			function trx_addons_woocommerce_search_form_param_changed(param) {
				var value    = param.val(),
					empty    = value === '',
					title_filled = !empty,
					wrap     = param.parents('.sc_form_field_wrap'),
					title    = wrap.siblings('.sc_form_field_title'),
					selected = title.find('.sc_form_field_selected_items'), //wrap.siblings('.sc_form_field_selected_items'),
					slider   = param.next('.trx_addons_range_slider '),
					fields_wrap = param.parents('.trx_addons_woocommerce_search_form_fields_wrap');
				// Mark title as 'filled'
				if ( slider.length == 1 ) {
					var min = slider.data('min'),
						max = slider.data('max'),
						val = value.split(',');
					title_filled = Number( val[0] ) !== Number( min ) || ( val.length > 1 && Number( val[1] ) !== Number( max ) );
					title.toggleClass( 'sc_form_field_title_filled', title_filled );
				} else {
					title.toggleClass( 'sc_form_field_title_filled', !empty );
				}
				// Update selected items counter
				wrap.find('.sc_form_field_items_selected_value').html( value ? value.split(',').length : 0 );
				// Update info with selected items
				if ( selected.length
					&& ( 
						wrap.parents('.trx_addons_woocommerce_tools').length === 0
						||
						fields_wrap.hasClass( 'trx_addons_woocommerce_search_form_fields_wrap_opened' )
						)
				) {
					// Get value to display below title
					//var value_to_show = decodeURIComponent(''+value).split(',').map(trx_addons_proper).join(', ');
					var value_to_show = '',
						parts = (''+value).split(','),
						$item = null;
					for ( var i=0; i<parts.length; i++ ) {
						// Try find a text label (name) of the selected item
						$item = wrap.find('.sc_form_field_item[data-value="'+parts[i]+'"] .sc_form_field_item_text');
						value_to_show += ( value_to_show ? ', ' : '' )
										+ ( $item.length && $item.text() == $item.html()
											? $item.text()											// Use text label (name)
											: trx_addons_proper( decodeURIComponent( parts[i] ) )	// Use selected slug (value)
											);
					}
					// Display selected items below title
					if ( selected.html() === '' && title_filled ) {
						selected.hide().html( value_to_show ).slideDown();
					} else if ( selected.html() !== '' && ! title_filled ) {
						selected.slideUp( function() {
							selected.html( '' ).show();
						} );
					} else {
						selected.html( title_filled ? value_to_show : '' );
					}
				}
				// Update filters total counters
				var filters_total = 0;
				fields_wrap.find('.sc_form_field_title_filled + .sc_form_field_wrap .sc_form_field_param').each( function() {
					var $self = jQuery( this ),
						field = $self.parents('.sc_form_field');
					// Exclude the field "Category" from totals and prevent 'Clear all' to be displayed on category selected
					if ( ! field.hasClass('sc_form_field_product_cat') ) {
						filters_total += field.hasClass('sc_form_field_text') || field.hasClass('sc_form_field_range') ? 1 : $self.val().split(',').length;
					}
				});
				fields_wrap.parents('form').find( '.trx_addons_woocommerce_search_button_filters_total' )
					.html( filters_total )
					.toggleClass( 'trx_addons_woocommerce_search_button_filters_total_empty', filters_total === 0 );
				// Show/hide 'Clear all'
				fields_wrap.toggleClass( 'trx_addons_woocommerce_search_form_fields_filled', filters_total > 0 );
			}

			// Return comma-separated values from the field with multiple select allowed ('filter' style)
			function trx_addons_woocommerce_search_form_get_multiple_field_value( $wrap ) {
				var value = '';
				$wrap.find('.sc_form_field_item_checked').each( function() {
					value += ( value !== '' ? ',' : '' ) + jQuery(this).data('value');
				} );
				return value;
			}

			// Get products page from specified url and replace products on the page
			function trx_addons_woocommerce_search_form_get_products_from_url( shop_url, fields_wrap ) {
				reload_is_busy = true;
				var need_open_fields_wrap = false,
					widget_number = 0;
				if ( fields_wrap && fields_wrap.length ) {
					if ( mask_fields_wrap_on_loading ) {
						if ( ! list_products_loading ) {
							fields_wrap.append( list_products_loading_html );
						}
						need_open_fields_wrap = false;	//fields_wrap.hasClass('trx_addons_woocommerce_search_form_fields_wrap_opened')
														//	&& ! fields_wrap.parents('.trx_addons_woocommerce_search').hasClass('trx_addons_woocommerce_search_apply');
						widget_number = fields_wrap.parents('.trx_addons_woocommerce_search_type_filter').data('number') || 1;
					}
					fields_wrap.removeClass('trx_addons_woocommerce_search_form_fields_changed');
				}
				// Reset a current page number
				jQuery( 'input[name="paged"]' ).val( 1 );
				jQuery( '.woocommerce-load-more' ).data( 'page', 1 ).parent().show();
				// Mark products wrapper with 'loading'
				if ( ! list_products_loading ) {
					jQuery( list_products_loading_selector )
						.addClass( 'trx_addons_woocommerce_search_loading' )
						.append( list_products_loading_html )
						.find('>' + list_products_loading_class)
							.addClass('trx_addons_hidden')
							.fadeIn(200);
				}
				list_products_loading++;
				// Make GET query to the shop
				jQuery.get( shop_url ).done( function( response ) {
					list_products_loading--;
					if ( list_products_loading ) {
						list_products_replace();
					} else {
						jQuery( list_products_loading_selector )
							.find('>' + list_products_loading_class)
							.fadeOut( 200, function() {
								list_products_replace( true );
							} );
					}
					function list_products_replace( remove_loading ) {
						var $response = jQuery( response ),
							new_products = $response.find( list_products_selector ).html(),
							new_inline_css = $response.find( inline_css_selector ).html(),
							new_total = $response.find('.trx_addons_woocommerce_search_button_show_total').eq(0).text() || 0;
						// If products found
						if ( new_products ) {
							// Replace document url in the browser's address bar
							trx_addons_document_set_location( shop_url );
							// Replace products with new items
							list_products_wrap.html( new_products );
							// Replace inline css with new styles
							if ( inline_css_wrap.length ) {
								// If inline css is not found via jQuery - try search with regular expression
								if ( ! new_inline_css ) {
									var mask = '<style[^>]*id="' + inline_css_selector.substr(1).replace( /\-/g, '\\-' ) + '"[^>]*>([^<]+)</style>';
									var re = new RegExp( mask );
									var matches = response.match( re );
									if ( matches && matches[1] ) {
										new_inline_css = matches[1];
									}
								}
								// If found inline css for list products - replace it
								if ( new_inline_css ) {
									var new_inline_block_start = new_inline_css.indexOf( inline_css_start ),
										new_inline_block_end = new_inline_css.indexOf( inline_css_end ),
										new_inline_block = new_inline_block_start >= 0
																? new_inline_css.substring( new_inline_block_start, new_inline_block_end + inline_css_end.length )
																: '';
									if ( new_inline_block ) {
										var old_inline_css = inline_css_wrap.html(),	
											old_inline_block_start = old_inline_css.indexOf( inline_css_start ),
											old_inline_block_end = old_inline_css.indexOf( inline_css_end );
										if ( old_inline_block_start ) {
											old_inline_css = old_inline_css.substring( 0, old_inline_block_start )
															+ new_inline_block
															+ old_inline_css.substring( old_inline_block_end + inline_css_end.length );
											inline_css_wrap.html( old_inline_css );
										}
									}
								}
							}
							// Place back a 'loading' icon
							if ( ! remove_loading ) {
								jQuery( list_products_loading_selector )
									.toggleClass('trx_addons_woocommerce_search_loading', true)
									.append( list_products_loading_html );
							}
							// Replace total products counters
							jQuery('.trx_addons_woocommerce_search_button_show_total').text(new_total);
							// Got new elements
							jQuery( document ).trigger( 'action.got_ajax_response', {
								action: 'woocommerce_ajax_get_posts',
								result: response,
								products: list_products_wrap.hasClass('products')
												? list_products_wrap
												: list_products_wrap.find('ul.products')
							});
							// Init new elements
							$document.trigger( 'action.init_hidden_elements', [list_products_wrap] );
						} else {
							// If a result is a redirect to the single product - commit it
							if ( $response.find( single_product_selector ).length ) {
								var url = $response.filter( 'link[rel="canonical"]' ).attr( 'href' );
								if ( url ) {
									window.location = url;
									return;
								}
							}
							alert( TRX_ADDONS_STORAGE['msg_no_products_found'] );
						}
						// Remove 'loading' from products container
						if ( remove_loading ) {
							jQuery( list_products_loading_selector )
								.removeClass('trx_addons_woocommerce_search_loading')
								.find('>' + list_products_loading_class).remove();
						}
						// If fields_wrap not empty
						if ( widget_number ) {
							// Get new object for fields_wrap after products replaced (old fields wrap may be removed)
							var widget = jQuery('.trx_addons_woocommerce_search_type_filter[data-number="' + widget_number + '"]').eq(0),
								fields_wrap = widget.find('.trx_addons_woocommerce_search_form_fields_wrap').eq(0);
							if ( fields_wrap.length ) {
								// Remove 'loading'
								if ( remove_loading && mask_fields_wrap_on_loading ) {
									fields_wrap.find('>' + list_products_loading_class).remove();
								}
								// Reopen fields wrap
								if ( need_open_fields_wrap ) {
									widget.find('.trx_addons_woocommerce_search_form_fields_wrap').addClass('trx_addons_woocommerce_search_form_fields_wrap_opened trx_addons_woocommerce_search_form_fields_wrap_show');
									setTimeout( function() {
										widget.find('.trx_addons_woocommerce_search_form_fields_wrap_show').removeClass('trx_addons_woocommerce_search_form_fields_wrap_show');
									}, 500 );
								}
								// Reopen last active field
								if ( last_opened_filter ) {// && ! fields_wrap.parents('.trx_addons_woocommerce_search ').hasClass('trx_addons_woocommerce_search_apply') ) {
									var field = widget.find('input[name="'+last_opened_filter+'"]').parents('.sc_form_field');
									trx_addons_woocommerce_search_form_open_field( field, 'show' );
									if ( last_clicked_item ) {
										field.find('.sc_form_field_item[data-value="'+last_clicked_item+'"]').focus();
									}
								}
								// Clear 'param-changed' data parameter
								fields_wrap.data('param-changed', 0);
							}
						}
						reload_is_busy = false;
					}
				} );
			}

		} );

		// Close opened field on click outside the field wrap
		var trx_addons_woocommerce_search_form_click_outside = trx_addons_throttle(
								function() {
									var opened = jQuery('.trx_addons_woocommerce_search_form .sc_form_field_opened').eq(0);
									if ( opened.length ) {
										var fields_wrap = opened.parents('.trx_addons_woocommerce_search_form_fields_wrap');
										if ( fields_wrap.parents('.trx_addons_woocommerce_tools').length
											&& ! fields_wrap.hasClass('trx_addons_woocommerce_search_form_fields_wrap_opened')
										) {
											opened.find('.sc_form_field_title').trigger('click');
										} else {
											fields_wrap.find('.trx_addons_woocommerce_search_button_show').trigger('click');
										}
									}
								},
								trx_addons_apply_filters( 'trx_addons_filter_woocommerce_search_click_outside_timeout', 10 ),
								true
							);

		$document.on( 'click', function(e) {
			var $self = jQuery( e.target );
			if ( ! $self.hasClass('sc_form_field_wrap')
					&& $self.parents('.sc_form_field_wrap').length === 0
					&& $self.parents('.trx_addons_woocommerce_search_form_fields_wrap').length === 0
					&& ! $self.hasClass(list_products_loading_class)
			) {
				trx_addons_woocommerce_search_form_click_outside();
			}
			// Close opened fields wrap
			/*
			if ( ! $self.hasClass('trx_addons_woocommerce_search_form_fields_wrap_opened')
					&& $self.parents('.trx_addons_woocommerce_search_form_fields_wrap_opened').length === 0
			) {
				jQuery('.trx_addons_woocommerce_search_form_fields_wrap_opened').removeClass('trx_addons_woocommerce_search_form_fields_wrap_opened');
			}
			*/
		} );
		
		// Collect all form params to array (any style)
		function trx_addons_woocommerce_search_form_get_params( form, selector ) {
			var params = {}, not_empty = false;
			// Collect all filters
			form.find( selector ? selector : 'select,input' ).each( function() {
				var $self = jQuery(this),
					val   = $self.val(),
					name  = $self.attr('name'),
					type  = '';
				if ( name && ( ( val !== '' && ( '' + val ) !== '0' ) || name == 'product_cat' ) ) {
					if ( name == 'price' ) {
						var values  = val.split(','),
							$slider = $self.next('.trx_addons_range_slider '),
							min     = $slider.data('min'),
							max     = $slider.data('max');
						if ( values.length == 2 && $slider.length == 1 ) {
							if ( Number( values[0] ) > min ) {
								params['min_price'] = values[0];
							}
							if ( Number( values[1] ) < max ) {
								params['max_price'] = values[1];
							}
						}
						name = '';
					} else if ( name.substring(0, 3) == 'pa_' ) {
						type = 'query_type_' + name.substring(3);
						name = 'filter_' + name.substring(3);
					} else if ( name == 'rating' ) {
						name = name + '_filter';
					}
					if ( name !== '' ) {
						params[name] = val;
					}
					if ( type !== '' && val.indexOf(',') > 0 ) {
						params[type] = 'or';
					}
					not_empty = true;
				}
			} );
			// Add sort order
			if ( not_empty || selector ) {
				params = trx_addons_woocommerce_search_form_add_orderby_to_query_params( params );
			}
			return not_empty || typeof params['orderby'] != 'undefined' ? params : false;
		}

		// Add sort order to the query params
		function trx_addons_woocommerce_search_form_add_orderby_to_query_params( params ) {
			var orderby = jQuery('.woocommerce-ordering .orderby').val();
			if ( orderby && orderby != 'menu_order' ) {
				params['orderby'] = orderby;
			}
			return params;
		}

	});

})();
