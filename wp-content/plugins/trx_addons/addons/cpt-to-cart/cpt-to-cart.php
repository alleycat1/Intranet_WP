<?php
/**
* Allow to add to the WooCommerce cart custom post types
*
* @addon cpt-to-cart
* @version 1.1
*
* @package ThemeREX Addons
* @since v2.13.0
*/

if ( ! defined( 'TRX_ADDONS_CPT_TO_CART_PRICE_FIELD_NAME' ) ) define( 'TRX_ADDONS_CPT_TO_CART_PRICE_FIELD_NAME', 'trx_addons_options_cpt_to_cart__price' );

// Load styles and scripts
//---------------------------------------------------------------------------

if ( ! function_exists( 'trx_addons_cpt_to_cart_load_scripts_front' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_cpt_to_cart_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_cpt_to_cart_load_scripts_front', 10, 1 );
	/**
	 * Load required styles and scripts for the frontend.
	 * 
	 * Hook: add_action( 'wp_enqueue_scripts', 'trx_addons_cpt_to_cart_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	 * 
	 * add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_cpt_to_cart_load_scripts_front', 10, 1 );
	 *  
	 * @param bool $force  Force loading if true.
	 */
	function trx_addons_cpt_to_cart_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'cpt_to_cart', $force, array(
			'css'  => array(
				'trx_addons-cpt-to-cart' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'cpt-to-cart/cpt-to-cart.css' ),
			),
		) );
	}
}
	
// Merge styles to the single stylesheet
if ( ! function_exists( 'trx_addons_cpt_to_cart_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_cpt_to_cart_merge_styles');
	function trx_addons_cpt_to_cart_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'cpt-to-cart/cpt-to-cart.css' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu or layouts or finally in the whole page output
if ( !function_exists( 'trx_addons_cpt_to_cart_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_cpt_to_cart_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_cpt_to_cart_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_cpt_to_cart_check_in_html_output', 10, 1 );
	function trx_addons_cpt_to_cart_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*cpt_to_cart_button',
				'class=[\'"][^\'"]*cpt_to_cart_link',
			)
		);
		if ( trx_addons_check_in_html_output( 'cpt_to_cart', $content, $args ) ) {
			trx_addons_cpt_to_cart_load_scripts_front( true );
		}
		return $content;
	}
}


// Template functions to get allowed CPT, its prices, etc.
//---------------------------------------------------------------------------

if ( ! function_exists( 'trx_addons_cpt_to_cart_get_allowed_post_types' ) ) {
	/**
	 * Return an array with post types which allowed to place to the cart.
	 * 
	 */
	function trx_addons_cpt_to_cart_get_allowed_post_types() {
		static $post_types = false;
		if ( $post_types === false ) {
			$post_types = array(); 
			$options = trx_addons_cpt_to_cart_load_options();
			if ( is_array( $options ) ) {
				foreach ( $options as $pt => $settings ) {
					if ( ! empty( $settings['allow'] ) ) {
						$post_types[] = $pt;
					}
				}
			}
		}
		return $post_types;
	}
}

if ( ! function_exists( 'trx_addons_cpt_to_cart_get_price_field' ) ) {
	/**
	 * Return a name of the meta-field, contains a price for the specified post_type.
	 * 
	 * @param string $post_type  A post type name.
	 * 
	 * @return string  A name of the meta-field with a price of the specified post type.
	 */
	function trx_addons_cpt_to_cart_get_price_field( $post_type ) {
		$options = trx_addons_cpt_to_cart_load_options();
		return in_array( $post_type, trx_addons_cpt_to_cart_get_allowed_post_types() )
				? ( ! empty( $options[ $post_type ]['price_new'] ) && (int)$options[ $post_type ]['price_new'] > 0
					? TRX_ADDONS_CPT_TO_CART_PRICE_FIELD_NAME
					: ( ! empty( $options[ $post_type ]['price_name'] )
						? $options[ $post_type ]['price_name']
						: ''
						)
					)
				: '';
	}
}

if ( ! function_exists( 'trx_addons_cpt_to_cart_get_price' ) ) {
	/**
	 * Return a price of the CPT product from the meta field specified in the CPT product settings
	 * 
	 * @param int $post_id        ID of the post to get price.
	 * @param string $post_itype  Optional. A type of the post specified by ID. If empty - make request to detect a post type by ID.
	 * 
	 * @return number  A new price from the meta field
	 */
	function trx_addons_cpt_to_cart_get_price( $post_id, $post_type = '' ) {
		$price = '';
		if ( empty( $post_type ) ) {
			$post_type = get_post_type( $post_id );
		}
		if ( ! empty( $post_type ) ) {
			$fields = array_map( 'trim', explode( '+', trx_addons_cpt_to_cart_get_price_field( $post_type ) ) );
			foreach ( $fields as $field ) {
				if ( in_array( $field, array( "' '", '" "' ) ) ) {
					$price .= ' ';
				} else if ( ! empty( $field ) ) {
					if ( preg_match( '/([a-zA-Z0-9_\\-]+)\\[([a-zA-Z0-9_\\-]*)\\]/', $field, $matches) ) {
						$post_meta = get_post_meta( $post_id, $matches[1], true );
						if ( ! empty( $post_meta[ $matches[2] ] ) ) {
							$price .= $post_meta[ $matches[2] ];	//trx_addons_parse_num( $post_meta[ $matches[2] ] );
						}
					} else {
						$post_meta = get_post_meta( $post_id, $field, true );
						if ( ! empty( $post_meta ) ) {
							$price .= $post_meta;
						}
					}
				}
			}
		}
		return empty( $price ) ? 0 : $price;
	}
}

if ( ! function_exists( 'trx_addons_cpt_to_cart_woocommerce_product_get_price' ) ) {
	add_filter('woocommerce_product_get_price', 'trx_addons_cpt_to_cart_woocommerce_product_get_price', 10, 2 );
	/**
	 * Return a price of the CPT product from the meta field specified in the CPT product settings
	 * 
	 * Hook: add_filter('woocommerce_product_get_price', 'trx_addons_cpt_to_cart_woocommerce_product_get_price', 10, 2 );
	 * 
	 * @param string|number $price  A default price of the product
	 * @param WC_Product $product   An object of a product to detect price.
	 * 
	 * @return number  A new price from the meta field
	 */
	function trx_addons_cpt_to_cart_woocommerce_product_get_price( $price, $product ) {
		$post_id = $product->get_id();
		if ( $post_id ) {
			$post_price = trx_addons_parse_num( trx_addons_cpt_to_cart_get_price( $post_id ) );
			if ( ! empty( $post_price ) ) {
				$price = $post_price;
			}
		}
		return $price;
	}
}

if ( ! function_exists( 'trx_addons_cpt_to_cart_make_product_global_object' ) ) {
	add_action( 'the_post', 'trx_addons_cpt_to_cart_make_product_global_object', 20 );
	/**
	 * Make a global object $product on base the current post.
	 * 
	 * Hook: add_action( 'the_post', 'trx_addons_cpt_to_cart_make_product_global_object' );
	 * 
	 * @param int|WP_Post $post  An object with the current post or ID of the current post.
	 * 
	 * @return number  A new price from the meta field
	 */
	function trx_addons_cpt_to_cart_make_product_global_object( $post ) {
		if ( is_int( $post ) ) {
			$post = get_post( $post );
		}
		if ( ! trx_addons_exists_woocommerce() || empty( $post->post_type ) || ! in_array( $post->post_type, trx_addons_cpt_to_cart_get_allowed_post_types() ) ) {
			return;
		}
		$GLOBALS['product'] = wc_get_product( $post );
		return $GLOBALS['product'];
	}
}



// 'Add to cart' URL, text and link
//------------------------------------------------------------

if ( ! function_exists( 'trx_addons_cpt_to_cart_add_to_cart_url' ) ) {
	add_filter( 'trx_addons_filter_cpt_add_to_cart_url', 'trx_addons_cpt_to_cart_add_to_cart_url' );
	/**
	 * Return an url to add a current post with a custom post type to the Woocommerce cart.
	 * 
	 * Hook: add_filter( 'trx_addons_filter_cpt_add_to_cart_url', 'trx_addons_cpt_to_cart_add_to_cart_url' );
	 * 
	 * @return string  An URL for the current post to add it to the cart.
	 */
	function trx_addons_cpt_to_cart_add_to_cart_url( $url = '' ) {
		global $product;
		return empty( $url ) && ! empty( $product ) && is_object( $product ) ? $product->add_to_cart_url() : $url;
	}
}

if ( ! function_exists( 'trx_addons_cpt_to_cart_add_to_cart_text' ) ) {
	add_filter( 'trx_addons_filter_cpt_add_to_cart_text', 'trx_addons_cpt_to_cart_add_to_cart_text' );
	/**
	 * Return a link text to add a current post with a custom post type to the Woocommerce cart.
	 * 
	 * Hook: add_filter( 'trx_addons_filter_cpt_add_to_cart_text', 'trx_addons_cpt_to_cart_add_to_cart_text' );
	 * 
	 * @return string  A link text for the current post to add it to the cart.
	 */
	function trx_addons_cpt_to_cart_add_to_cart_text( $text = '' ) {
		global $product;
		return empty( $text) && ! empty( $product ) && is_object( $product ) ? $product->add_to_cart_text() : $text;
	}
}

if ( ! function_exists( 'trx_addons_cpt_to_cart_add_to_cart_icon' ) ) {
	add_filter( 'trx_addons_filter_cpt_add_to_cart_icon', 'trx_addons_cpt_to_cart_add_to_cart_icon' );
	/**
	 * Return a class with an icon for the link 'Add to cart'.
	 * 
	 * Hook: add_filter( 'trx_addons_filter_cpt_add_to_cart_icon', 'trx_addons_cpt_to_cart_add_to_cart_icon' );
	 * 
	 * @return string  A class name with an icon.
	 */
	function trx_addons_cpt_to_cart_add_to_cart_icon( $icon = '' ) {
		return empty( $icon ) ? 'trx_addons_icon-cart' : $icon;
	}
}

if ( ! function_exists( 'trx_addons_cpt_to_cart_add_to_cart_link' ) ) {
	add_filter( 'trx_addons_filter_cpt_add_to_cart_link', 'trx_addons_cpt_to_cart_add_to_cart_link', 10, 3 );
	add_filter( 'trx_addons_filter_cpt_add_to_cart_button', 'trx_addons_cpt_to_cart_add_to_cart_link', 10, 3 );
	/**
	 * Return a tag <a> to add a current post with a custom post type to the Woocommerce cart.
	 * 
	 * Hook: add_filter( 'trx_addons_filter_cpt_add_to_cart_link', 'trx_addons_cpt_to_cart_add_to_cart_link', 10, 3 );
	 * 
	 *       add_filter( 'trx_addons_filter_cpt_add_to_cart_button', 'trx_addons_cpt_to_cart_add_to_cart_link', 10, 3 );
	 * 
	 * @param string $link     A default link layout (empty string).
	 * @param string $classes  A string with additional classes for the link.
	 * 
	 * @return string  A new link layout for the current post type
	 */
	function trx_addons_cpt_to_cart_add_to_cart_link( $link, $classes = '', $text = '' ) {
		global $product;
		if ( ! empty( $product ) && is_object( $product ) ) {
			$link = apply_filters(
						'woocommerce_loop_add_to_cart_link',
						'<a rel="nofollow" href="' . esc_url( apply_filters( 'trx_addons_filter_cpt_add_to_cart_url', '' ) ) . '"'
							. ' title="' . esc_attr( apply_filters( 'trx_addons_filter_cpt_add_to_cart_text', '' ) ) . '"'
							. ' aria-hidden="true"'
							. ' data-quantity="1"' 
							. ' data-product_id="' . esc_attr( $product->get_id() ) . '"'
							. ' data-product_sku="' . esc_attr( $product->get_sku() ) . '"'
							. ' class="' . esc_attr( apply_filters( 'trx_addons_filter_cpt_add_to_cart_class',
														'cpt_to_cart_' . ( current_action() == 'trx_addons_filter_cpt_add_to_cart_button' ? 'button' : 'link' )
														. ' ' . apply_filters( 'trx_addons_filter_cpt_add_to_cart_icon', '' )
														. ' shop_cart button add_to_cart_button'
														. ' product_type_' . $product->get_type()
														. ' product_' . ( $product->is_purchasable() && $product->is_in_stock() ? 'in' : 'out' ) . '_stock'
														. ( $product->supports( 'ajax_add_to_cart' ) ? ' ajax_add_to_cart' : '' )
														. ( ! empty( $classes ) ? " {$classes}" : '' )
													) )
										. '"'
						. '>'
							. apply_filters( 'trx_addons_filter_cpt_add_to_cart_text',
								! empty( $text )
									? '<span class="cpt_to_cart_text">' . $text . '</span>'
									: ''
							)
						. '</a>',
						$product
					);
		}
		return $link;
	}
}



// Inject 'Add to cart' link and button to the posts archive and single post output
//-------------------------------------------------------------------------------------

if ( ! function_exists( 'trx_addons_cpt_to_cart_init_action_and_filter_handlers' ) ) {
	add_action( 'init', 'trx_addons_cpt_to_cart_init_action_and_filter_handlers' );
	/**
	 * Add an action and a filter handlers to inject a links to the post archive and single post's output.
	 * 
	 * Hook: add_action( 'init', 'trx_addons_cpt_to_cart_init_action_and_filter_handlers' );
	 */
	function trx_addons_cpt_to_cart_init_action_and_filter_handlers() {
		$used = array();
		$options = trx_addons_cpt_to_cart_load_options();
		if ( is_array( $options ) ) {
			foreach ( $options as $pt => $settings ) {
				if ( empty( $settings['allow'] ) ) {
					continue;
				}
				foreach ( $settings['events'] as $v ) {
					if (   ! empty( $v['event_type'] )
						&& ! empty( $v['event_name'] )
						&& ! in_array( $v['event_name'], $used )
					) {
						add_filter( $v['event_name'], "trx_addons_cpt_to_cart_event_handler" );
						$used[] = $v['event_name'];
					}
				}
			}
			// Check if inside related posts
			add_action( 'trx_addons_action_before_related_wrap', 'trx_addons_cpt_to_cart_detect_related_wrap_start' );
			add_action( str_replace( '-', '_', get_template() ) . '_action_before_related_wrap', 'trx_addons_cpt_to_cart_detect_related_wrap_start' );
			add_action( 'trx_addons_action_after_related_wrap', 'trx_addons_cpt_to_cart_detect_related_wrap_end' );
			add_action( str_replace( '-', '_', get_template() ) . '_action_after_related_wrap', 'trx_addons_cpt_to_cart_detect_related_wrap_end' );
		}
	}
}

if ( ! function_exists( 'trx_addons_cpt_to_cart_detect_related_wrap_start' ) ) {
	/**
	 * Detect what a section with related posts is started.
	 * 
	 * Hook: add_action( 'trx_addons_action_before_related_wrap', 'trx_addons_cpt_to_cart_detect_related_wrap_start' );
	 * 
	 *       add_action( 'theme_slug_action_before_related_wrap', 'trx_addons_cpt_to_cart_detect_related_wrap_start' );
	 */
	function trx_addons_cpt_to_cart_detect_related_wrap_start() {
		global $TRX_ADDONS_STORAGE;
		$TRX_ADDONS_STORAGE['related_posts_inside'] = true;
	}
}

if ( ! function_exists( 'trx_addons_cpt_to_cart_detect_related_wrap_end' ) ) {
	/**
	 * Detect what a section with related posts is ended.
	 * 
	 * Hook: add_action( 'trx_addons_action_before_related_wrap', 'trx_addons_cpt_to_cart_detect_related_wrap_end' );
	 * 
	 *       add_action( 'theme_slug_action_before_related_wrap', 'trx_addons_cpt_to_cart_detect_related_wrap_end' );
	 */
	function trx_addons_cpt_to_cart_detect_related_wrap_end() {
		global $TRX_ADDONS_STORAGE;
		$TRX_ADDONS_STORAGE['related_posts_inside'] = false;
	}
}

if ( ! function_exists( 'trx_addons_cpt_to_cart_inside_related_wrap' ) ) {
	/**
	 * Check if a current output is inside the related posts section.
	 * 
	 * @return boolean  Return true if current output is insude the section with related posts.
	 */
	function trx_addons_cpt_to_cart_inside_related_wrap() {
		global $TRX_ADDONS_STORAGE;
		return ! empty( $TRX_ADDONS_STORAGE['related_posts_inside'] );
	}
}

if ( ! function_exists( 'trx_addons_cpt_to_cart_event_handler' ) ) {
	/**
	 * Add a price and a link 'Add to cart' to the filtered value or output a link layout.
	 * 
	 * Hook: add_filter( 'filter_name_from_cpt_options', 'trx_addons_cpt_to_cart_event_handler' );
	 * 
	 * @param string $value  A value to be filtered (used only in filters).
	 * 
	 * @return string  A modified value.
	 */
	function trx_addons_cpt_to_cart_event_handler( $value = '' ) {
		$options = trx_addons_cpt_to_cart_load_options();
		$post_id = get_the_ID();
		$post_type = get_post_type();
		if ( ! empty( $options[ $post_type ]['allow'] )
			&& ! empty( $options[ $post_type ]['events'] )
			&& is_array( $options[ $post_type ]['events'] )
		) {
			foreach ( $options[ $post_type ]['events'] as $v ) {
				if ( $v['event_name'] !== current_filter() ) {
					continue;
				}
				if ( $v['area'] == 'any'
					|| ( $v['area'] == 'single' && trx_addons_is_single() )
					|| ( $v['area'] == 'archive'
							&& ( is_post_type_archive( $post_type )
								|| is_tax( trx_addons_get_post_type_taxonomy( $post_type ) )
								|| trx_addons_cpt_to_cart_inside_related_wrap()
								) 
							&& in_the_loop()
						)
				) {
					$price = trx_addons_cpt_to_cart_get_price( $post_id, $post_type );
					if ( ! empty( $price ) ) {
						// Link type 'url'
						if ( $v['link_type'] == 'url' ) {
							$url = apply_filters( 'trx_addons_filter_cpt_add_to_cart_url', '' );
							if ( ! empty( $url ) ) {
								// In the filter 'trx_addons_filter_get_blog_title' an array is need to return
								if ( current_filter() == 'trx_addons_filter_get_blog_title' ) {
									$value = array(
										'text' => is_string( $value )
													? $value
													: ( ! empty( $value['text'] )
														? $value['text']
														: get_the_title()
														),
										//'class' => 'cpt_to_cart_page_title',
										'link' => $url,
										'link_text' => sprintf( __( 'Buy for %s', 'trx_addons' ), $price )
									);
								} else {
									if ( $v['place'] == 'prepend' ) {
										$value = $url . $value;
									} else if ( $v['place'] == 'append' ) {
										$value .= $url;
									} else {
										$value = $url;
									}
									if ( $v['event_type'] == 'action' ) {
										trx_addons_show_layout( $url );
									}
								}
							}
						// Link type 'link' or 'button'
						} else {
							$add_to_cart_link = apply_filters(
													'trx_addons_filter_cpt_add_to_cart_' . $v['link_type'],
													'',
													'',
													$price
												);
							if ( ! empty( $add_to_cart_link ) ) {
								if ( $v['place'] == 'prepend' ) {
									$value = $add_to_cart_link . $value;
								} else if ( $v['place'] == 'append' ) {
									$value .= $add_to_cart_link;
								} else {
									$value = $add_to_cart_link;
								}
								if ( $v['event_type'] == 'action' ) {
									trx_addons_show_layout( $add_to_cart_link );
								}
							}
						}
					}
				}
			}
		}
		return $value;
	}
}


// Add a section 'Custom post types' with options to the WooCommerce - Settings - Products
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'cpt-to-cart/cpt-to-cart-settings.php';

// Include an extension for the class 'Woocommerce cart'
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'cpt-to-cart/cpt-to-cart-class.php';

// Add a meta box with a field 'Price' for CPT, specified in settings
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'cpt-to-cart/cpt-to-cart-meta-box.php';
