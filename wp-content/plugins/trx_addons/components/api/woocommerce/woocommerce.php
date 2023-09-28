<?php
/**
 * Plugin support: WooCommerce
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Check if plugin installed and activated
// Attention! This function is used in many files and was moved to the api.php
/*
if ( ! function_exists( 'trx_addons_exists_woocommerce' ) ) {
	function trx_addons_exists_woocommerce() {
		return class_exists('Woocommerce');
	}
}
*/

if ( ! function_exists( 'trx_addons_is_woocommerce_page' ) ) {
	/**
	 * Check if current page is any WooCommerce page
	 * 
	 * @return boolean  	  True if page is WooCommerce page
	 */
	function trx_addons_is_woocommerce_page() {
		$rez = false;
		if ( trx_addons_exists_woocommerce() ) {
			$rez = is_woocommerce()
					|| is_shop()
					|| is_product()
					|| is_product_category()
					|| is_product_tag()
					|| is_product_taxonomy()
					|| is_cart()
					|| is_checkout()
					|| is_account_page();
		}
		return $rez;
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_post_type_taxonomy' ) ) {
	add_filter( 'trx_addons_filter_post_type_taxonomy',	'trx_addons_woocommerce_post_type_taxonomy', 10, 2 );
	/**
	 * Return taxonomy name for the post type (this post_type have 2+ taxonomies)
	 * 
	 * @hook trx_addons_filter_post_type_taxonomy
	 *
	 * @param string $tax     Taxonomy name
	 * @param string $post_type  Post type name
	 * 
	 * @return string  	 Taxonomy name
	 */
	function trx_addons_woocommerce_post_type_taxonomy($tax='', $post_type='') {
		if ($post_type == 'product') {
			$tax = 'product_cat';
		}
		return $tax;
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_get_blog_all_posts_link' ) ) {
	add_filter( 'trx_addons_filter_get_blog_all_posts_link', 'trx_addons_woocommerce_get_blog_all_posts_link', 10, 2 );
	/**
	 * Return URL to main shop page for the breadcrumbs
	 * 
	 * @hook trx_addons_filter_get_blog_all_posts_link
	 *
	 * @param string $link  URL to main shop page
	 * @param array $args   Additional arguments
	 * 
	 * @return string  	 URL to main shop page
	 */
	function trx_addons_woocommerce_get_blog_all_posts_link( $link = '', $args = array() ) {
		if ( empty( $link ) && trx_addons_is_woocommerce_page() && ! is_shop() ) {
			if ( ( $url = trx_addons_woocommerce_get_shop_page_link() ) != '' ) {
				$id = trx_addons_woocommerce_get_shop_page_id();
				$front_id = get_option( 'show_on_front' ) == 'page' ? (int) get_option( 'page_on_front' ) : 0;
				if ( $front_id == 0 || $id == 0 || $front_id != $id ) {
					$link = '<a href="' . esc_url( $url ) . '">' . ( $id ? get_the_title( $id ) : esc_html__('Shop', 'trx_addons') ) . '</a>';
				} else {
					$link = '#';	// To disable link
				}
			}
		}
		return $link;
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_search_form_url' ) ) {
	add_filter( 'trx_addons_filter_search_form_url', 'trx_addons_woocommerce_search_form_url' );
	/**
	 * Return URL to main shop page for the search form
	 * 
	 * @hook trx_addons_filter_search_form_url
	 *
	 * @param string $url  URL to main shop page
	 * 
	 * @return string  	 URL to main shop page
	 */
	function trx_addons_woocommerce_search_form_url( $url ) {
		if ( trx_addons_exists_woocommerce() && trx_addons_is_woocommerce_page() && is_shop() ) {
			$shop_url = trx_addons_woocommerce_get_shop_page_link();
			if ( ! empty( $shop_url ) ) {
				$url = $shop_url;
			}
		}
		return $url;
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_get_shop_page_id' ) ) {
	/**
	 * Return shop page ID
	 *
	 * @return int  	 Shop page ID
	 */
	function trx_addons_woocommerce_get_shop_page_id() {
		return get_option('woocommerce_shop_page_id');
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_get_shop_page_link' ) ) {
	/**
	 * Return shop page link
	 *
	 * @return string  	 Shop page link
	 */
	function trx_addons_woocommerce_get_shop_page_link() {
		$url = '';
		$id = trx_addons_woocommerce_get_shop_page_id();
		if ( $id ) {
			$url = get_permalink( $id );
		}
		return $url;
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_get_blog_title' ) ) {
	add_filter( 'trx_addons_filter_get_blog_title', 'trx_addons_woocommerce_get_blog_title' );
	/**
	 * Return a title for the shop page
	 *
	 * @param string $title  Default title
	 * 
	 * @return string  	 Shop title
	 */
	function trx_addons_woocommerce_get_blog_title( $title = '' ) {
		if ( trx_addons_exists_woocommerce() && trx_addons_is_woocommerce_page() && is_shop() ) {
			$id = trx_addons_woocommerce_get_shop_page_id();
			$title = $id ? get_the_title( $id ) : esc_html__('Shop', 'trx_addons');
		}
		return $title;
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_get_filter_name_from_attribute' ) ) {
	/**
	 * Return a filter name for the WooCommerce attribute by the taxonomy (attribute) name
	 *
	 * @param string $tax_name  Attribute name
	 * @param bool $reverse     Reverse mode: add 'filter_' (if true) or '_filter' (if false) to the attribute name
	 * 
	 * @return string  	 Filter name
	 */
	function trx_addons_woocommerce_get_filter_name_from_attribute( $tax_name, $reverse = false ) {
		return ( ! $reverse ? 'filter_' : '' )
				. ( function_exists( 'wc_attribute_taxonomy_slug' )
					? wc_attribute_taxonomy_slug( $tax_name )
					: ( substr( $tax_name, 0, 3 ) == 'pa_' ? substr($tax_name, 3) : $tax_name )
					)
				. ( $reverse ? '_filter' : '' );
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_get_attribute_by_id' ) ) {
	/**
	 * Return an attribute data by ID
	 *
	 * @param int $id     Attribute ID
	 * @param string $field  Field name to return. If empty - return full attribute object
	 * 
	 * @return string  	 Attribute data
	 */
	function trx_addons_woocommerce_get_attribute_by_id( $id, $field = '' ) {
		global $wpdb;
		$attribute = $wpdb->get_row(
			$wpdb->prepare( "SELECT attribute_type, attribute_label, attribute_name, attribute_orderby, attribute_public
								FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_id = %d",
							$id
			)
		);
		return ! empty( $field )
					? ( ! empty( $attribute->{$field} ) ? $attribute->{$field} : '' )
					: $attribute;
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_get_attributes_data' ) ) {
	/**
	 * Return an extended data for WooCommerce attributes
	 *
	 * @param string $name     Attribute name
	 * @param string $field    Field name to return. If empty - return full attribute object
	 * @param string $default  Default value to return
	 *
	 * @return string  	 Attribute data
	 */
	function trx_addons_woocommerce_get_attributes_data( $name = '', $field = '', $default = '' ) {
		$att_data = get_option( 'woocommerce_attributes_data' );
		if ( empty( $att_data ) || ! is_array( $att_data ) ) {
			$att_data = array();
		}
		if ( empty( $name ) ) {
			return $att_data;
		} else {
			if ( substr( $name, 0, 3 ) == 'pa_' ) {
				$name = substr( $name, 3 );
			}
			if ( empty( $field ) ) {
				return isset( $att_data[ $name ] ) ? $att_data[ $name ] : array();
			} else {
				return isset( $att_data[ $name ][ $field ] ) ? $att_data[ $name ][ $field ] : $default;
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_set_attributes_data' ) ) {
	/**
	 * Set an extended data for WooCommerce attributes
	 *
	 * @param mixed  $data   Attribute data
	 * @param string $name   Attribute name
	 * @param string $field  Field name to update. If empty - update full attribute object
	 */
	function trx_addons_woocommerce_set_attributes_data( $data, $name = '', $field = '' ) {
		$att_data = get_option( 'woocommerce_attributes_data' );
		if ( empty( $att_data ) || ! is_array( $att_data ) ) {
			$att_data = array();
		}
		if ( empty( $name ) ) {
			$att_data = $data;
		} else {
			if ( substr( $name, 0, 3 ) == 'pa_' ) {
				$name = substr( $name, 3 );
			}
			if ( empty( $field ) ) {
				$att_data[ $name ] = $data;
			} else {
				$att_data[ $name ][ $field ] = $data;
			}
		}
		update_option( 'woocommerce_attributes_data', $att_data );
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_login_menu_settings' ) ) {
	add_action( "trx_addons_action_login_menu_settings", 'trx_addons_woocommerce_login_menu_settings' );
	/**
	 * Add a WooCommerce link 'My Account' to the user menu
	 * 
	 * @hooked trx_addons_action_login_menu_settings
	 */
	function trx_addons_woocommerce_login_menu_settings() {
		if ( trx_addons_exists_woocommerce() ) {
			$myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
			if ( ! empty( $myaccount_page_id ) ) {
				?><li class="menu-item trx_addons_icon-edit"><a href="<?php echo esc_url( get_permalink( $myaccount_page_id ) ); ?>"><span><?php esc_html_e('My account', 'trx_addons'); ?></span></a></li><?php
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_custom_meta_value' ) ) {
	add_filter( 'trx_addons_filter_custom_meta_value', 'trx_addons_woocommerce_custom_meta_value', 10, 2 );
	/**
	 * Return a value of the custom field 'price' or 'rating' for the custom blog items
	 * 
	 * @hooked trx_addons_filter_custom_meta_value
	 *
	 * @param string $value  Value to return
	 * @param string $key    Meta key
	 * 
	 * @return string  	Value to return
	 */
	function trx_addons_woocommerce_custom_meta_value( $value, $key ) {
		if ( get_post_type() == 'product' && trx_addons_exists_woocommerce() ) {
			global $product;
			if ( is_object( $product ) ) {
				if ( $key == 'price' ) {
					$value = $product->get_price_html();
				} else if ( in_array( $key, array( 'rating', 'rating_text', 'rating_icons', 'rating_stars' ) ) && get_option( 'woocommerce_enable_review_rating' ) !== 'no' ) {
					$value = $key == 'rating_text'
								? $product->get_average_rating()
								: wc_get_rating_html( $product->get_average_rating() );
				}
			}
		}
		return $value;
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_blog_item_button' ) ) {
	add_filter( 'trx_addons_filter_blog_item_button', 'trx_addons_woocommerce_blog_item_button', 10, 2 );
	/**
	 * Return a button 'Add to cart' for the custom blog items
	 * 
	 * @hooked trx_addons_filter_blog_item_button
	 *
	 * @param string $output  Button HTML
	 * @param array  $args    Button parameters
	 * 
	 * @return string  	Button HTML
	 */
	function trx_addons_woocommerce_blog_item_button( $output, $args ) {
		if ( ! empty( $args['button_link'] ) && $args['button_link'] == 'cart' && trx_addons_exists_woocommerce() && get_post_type() == 'product' ) {
			$ajax = 'yes' === get_option( 'woocommerce_enable_ajax_add_to_cart' );
			if ( $ajax ) {
				wp_enqueue_script( 'wc-add-to-cart' );
			}
			ob_start();
			woocommerce_template_loop_add_to_cart( array(
				'class' => 'sc_button button add_to_cart_button' . ($ajax ? ' ajax_add_to_cart' : '')
			) );
			$output = ob_get_contents();
			ob_end_clean();
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_blog_item_button_class' ) ) {
	add_filter( 'trx_addons_filter_blog_item_button_class', 'trx_addons_woocommerce_blog_item_button_class', 10, 2 );
	/**
	 * Add class 'woocommerce' to the button 'Add to cart' for the custom blog items
	 * 
	 * @hooked trx_addons_filter_blog_item_button_class
	 *
	 * @param string $class  Button class
	 * @param array  $args   Button parameters
	 * 
	 * @return string  	Button class
	 */
	function trx_addons_woocommerce_blog_item_button_class( $class, $args ) {
		if ( !empty($args['button_link']) && $args['button_link'] == 'cart' && trx_addons_exists_woocommerce() && get_post_type() == 'product' ) {
			$class .= ' woocommerce';
		}
		return $class;
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_prevent_admin_access' ) ) {
	add_filter( 'woocommerce_prevent_admin_access', 'trx_addons_woocommerce_prevent_admin_access' );
	/**
	 * Prevent WooCommerce from redirecting to the "My Account" page
	 * 
	 * @hooked woocommerce_prevent_admin_access
	 * 
	 * @trigger trx_addons_filter_allow_admin_access
	 *
	 * @param string $redirect_to  URL to redirect
	 * 
	 * @return string  	URL to redirect
	 */
	function trx_addons_woocommerce_prevent_admin_access( $redirect_to ) {
		$current_user = wp_get_current_user();
		if ( is_array( $current_user->roles ) && apply_filters( 'trx_addons_filter_allow_admin_access', false, $current_user->roles ) ) {
			return false;
		}
		return $redirect_to;
	}
}


// WooCommerce Tools widgets area (before the products loop)
//------------------------------------------------------------------------

if ( ! function_exists( 'trx_addons_woocommerce_register_sidebar' ) ) {
	add_action( 'widgets_init', 'trx_addons_woocommerce_register_sidebar', 20 );
	/**
	 * Register WooCommerce Tools widgets area
	 * 
	 * @hooked widgets_init, 20
	 * 
	 * @trigger trx_addons_filter_register_sidebar
	 */
	function trx_addons_woocommerce_register_sidebar() {
		global $TRX_ADDONS_STORAGE;
		register_sidebar( apply_filters( 'trx_addons_filter_register_sidebar', array(
										'name'          => __( 'WooCommerce Tools', 'trx_addons' ),
										'description'   => __( 'Widgets before the products loop', 'trx_addons' ),
										'id'            => 'trx_addons_woocommerce_tools',
										'before_widget' => $TRX_ADDONS_STORAGE['widgets_args']['before_widget'],
										'after_widget'  => $TRX_ADDONS_STORAGE['widgets_args']['after_widget'],
										'before_title'  => $TRX_ADDONS_STORAGE['widgets_args']['before_title'],
										'after_title'   => $TRX_ADDONS_STORAGE['widgets_args']['after_title']
										) )
								);
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_show_sidebar' ) ) {
	add_action( 'woocommerce_before_shop_loop', 'trx_addons_woocommerce_show_sidebar' );
	add_action( 'woocommerce_no_products_found', 'trx_addons_woocommerce_show_sidebar', 1 );
	/**
	 * Show WooCommerce Tools widgets area
	 * 
	 * @hooked woocommerce_before_shop_loop
	 * @hooked woocommerce_no_products_found, 1
	 * 
	 * @trigger trx_addons_action_before_woocommerce_tools
	 * @trigger trx_addons_action_after_woocommerce_tools
	 */
	function trx_addons_woocommerce_show_sidebar() {
		if ( is_active_sidebar( 'trx_addons_woocommerce_tools' ) ) {
			?><div class="trx_addons_woocommerce_tools widget_area"><?php
				do_action( 'trx_addons_action_before_woocommerce_tools' );
				dynamic_sidebar( 'trx_addons_woocommerce_tools' );
				do_action( 'trx_addons_action_after_woocommerce_tools' );
			?></div><?php
		}
	}
}


// Child categories in the header
//------------------------------------------------------------------------

if ( ! function_exists( 'trx_addons_woocommerce_show_child_categories' ) ) {
	add_action( 'trx_addons_action_after_layouts_title_block', 'trx_addons_woocommerce_show_child_categories' );
	/**
	 * Show child categories in the custom header
	 * 
	 * @hooked trx_addons_action_after_layouts_title_block
	 * 
	 * @trigger trx_addons_filter_woocommerce_show_child_categories
	 */
	function trx_addons_woocommerce_show_child_categories() {
		// Change false to true in the filter argument below to display child categories in the custom header
		if ( apply_filters( 'trx_addons_filter_woocommerce_show_child_categories', false ) && trx_addons_exists_woocommerce() && ( is_shop() || is_product_category() ) ) {
			$taxonomy = 'product_cat';
			$params = trx_addons_widget_woocommerce_search_query_params( array(
																			array( 'filter' => $taxonomy )
																			),
																			true
																		);
			$terms = trx_addons_get_list_terms( false, $taxonomy, array(
																		'hide_empty' => 1,
																		'parent' => $params[$taxonomy],
																		'return_key' => 'id',
																		'pad_count' => 1
																		)
												);

			if ( count( $terms ) > 0 ) {
				$terms = array_filter( $terms, function( $term ) {
					return substr( $term, 0, 2) !== '- ';
				} );

				if ( count( $terms ) > 0 ) {
					$buttons = array();
					foreach ( $terms as $id => $title ) {
						$image = trx_addons_get_term_image($id, $taxonomy);
						$image = ! empty($image) ? trx_addons_add_thumb_size($image, trx_addons_get_thumb_size( 'medium' ) ) : "";
						$image_small = trx_addons_get_term_image_small($id, $taxonomy);
						if ( empty( $image_small ) ) {
							$icon = trx_addons_get_term_icon($id, $taxonomy);
						}
						$buttons[] = apply_filters( 'trx_addons_filter_categories_list_button_args', array(
							"type" => "default",
							"size" => "normal",
							"text_align" => "none",
							"bg_image" => ! empty($image) ? $image : "",
							"image" => ! empty($image_small) ? $image_small : "",
							"icon" => empty($image_small) && ! empty( $icon ) ? $icon : "",
							"icon_position" => "left",
							"title" => $title,
							"subtitle" => "",
							"link" => get_term_link($id, $taxonomy),	// trx_addons_add_to_url( trx_addons_woocommerce_get_shop_page_link(), array( $taxonomy => $k ) )
							"css" => ""
						) );
					}
					trx_addons_show_layout( trx_addons_sc_button( array( 'buttons' => $buttons ) ), '<div class="trx_addons_woocommerce_child_categories">', '</div>' );
				}
			}

			trx_addons_sc_layouts_showed('child_categories', true);
		}
	}
}


// Load required scripts and styles
//------------------------------------------------------------------------
	
if ( ! function_exists( 'trx_addons_woocommerce_not_defer_scripts' ) ) {
	add_filter( "trx_addons_filter_skip_move_scripts_down", 'trx_addons_woocommerce_not_defer_scripts' );
	add_filter( "trx_addons_filter_skip_async_scripts_load", 'trx_addons_woocommerce_not_defer_scripts' );
	/**
	 * Add plugin-specific slugs to the list of scripts that should not be deferred or loaded asynchronously
	 * 
	 * @hooked trx_addons_filter_skip_move_scripts_down
	 * @hooked trx_addons_filter_skip_async_scripts_load
	 *
	 * @param array $list List of scripts to skip defer/async
	 * 
	 * @return array      Modified list
	 */
	function trx_addons_woocommerce_not_defer_scripts( $list ) {
		if ( trx_addons_exists_woocommerce() ) {
			$list[] = 'js.cookie';
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_woocommerce_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_woocommerce_load_scripts_front', 10, 1 );
	/**
	 * Enqueue custom styles and scripts for the frontend for WooCommerce
	 * 
	 * @hooked wp_enqueue_scripts
	 * @hooked trx_addons_action_pagebuilder_preview_scripts
	 * 
	 * @trigger trx_addons_action_load_scripts_front
	 * 
	 * @param boolean $force  Load styles and scripts always, not only if plugin is added to the page
	 */
	function trx_addons_woocommerce_load_scripts_front( $force = false ) {
		if ( ! trx_addons_exists_woocommerce() ) {
			return;
		}
		trx_addons_enqueue_optimized( 'woocommerce', $force, array(
			'css'  => array(
				'trx_addons-woocommerce' => array( 'src' => TRX_ADDONS_PLUGIN_API . 'woocommerce/woocommerce.css' ),
			),
			'js' => array(
				'trx_addons-woocommerce' => array( 'src' => TRX_ADDONS_PLUGIN_API . 'woocommerce/woocommerce.js', 'deps' => 'jquery' ),
			),
			'need' => trx_addons_is_woocommerce_page(),
			'check' => array(
				//array( 'type' => 'gb',  'sc' => 'wp:trx-addons/events' ),	// This sc is not exists for GB
				// Core WooCommerce shortcodes
				array( 'type' => 'sc',  'sc' => 'product' ),
				array( 'type' => 'sc',  'sc' => 'product_page' ),
				array( 'type' => 'sc',  'sc' => 'product_category' ),
				array( 'type' => 'sc',  'sc' => 'product_categories' ),
				array( 'type' => 'sc',  'sc' => 'product_add_to_cart' ),
				array( 'type' => 'sc',  'sc' => 'product_add_to_cart_url' ),
				array( 'type' => 'sc',  'sc' => 'product_attribute' ),
				array( 'type' => 'sc',  'sc' => 'recent_products' ),
				array( 'type' => 'sc',  'sc' => 'sale_products' ),
				array( 'type' => 'sc',  'sc' => 'best_selling_products' ),
				array( 'type' => 'sc',  'sc' => 'top_rated_products' ),
				array( 'type' => 'sc',  'sc' => 'featured_products' ),
				array( 'type' => 'sc',  'sc' => 'related_products' ),
				array( 'type' => 'sc',  'sc' => 'shop_messages' ),
				array( 'type' => 'sc',  'sc' => 'order_tracking' ),
				array( 'type' => 'sc',  'sc' => 'cart' ),
				array( 'type' => 'sc',  'sc' => 'checkout' ),
				array( 'type' => 'sc',  'sc' => 'my_account' ),
				// Our shortcodes and widgets
				array( 'type' => 'sc',  'sc' => 'trx_sc_extended_products' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_extended_products"' ),
				array( 'type' => 'sc',  'sc' => 'trx_widget_woocommerce_search' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_widget_woocommerce_search"' ),
				array( 'type' => 'sc',  'sc' => 'trx_widget_woocommerce_title' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_widget_woocommerce_title"' ),
				// Shortcodes in Elementor
				array( 'type' => 'elm', 'sc' => '"shortcode":"[product' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[recent_products' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[sale_products' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[best_selling_products' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[top_rated_products' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[featured_products' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[related_products' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[shop_messages' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[order_tracking' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[cart' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[checkout' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[my_account' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_sc_extended_products' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_widget_woocommerce_search' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_widget_woocommerce_title' ),
				// Shortcodes from the plugin "WC Product Table Pro"
				array( 'type' => 'sc',  'sc' => 'product_table' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[product_table' ),
			)
		) );
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_load_scripts_front_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_woocommerce_load_scripts_front_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	add_action( 'trx_addons_action_load_scripts_front_woocommerce', 'trx_addons_woocommerce_load_scripts_front_responsive', 10, 1 );
	/**
	 * Load responsive styles for the frontend for WooCommerce
	 * 
	 * @hooked wp_enqueue_scripts
	 * @hooked trx_addons_action_load_scripts_front_woocommerce
	 * 
	 * @param bool $force  Load responsive styles even if it's not necessary
	 */
	function trx_addons_woocommerce_load_scripts_front_responsive( $force = false ) {
		if ( ! trx_addons_exists_woocommerce() ) {
			return;
		}
		trx_addons_enqueue_optimized_responsive( 'woocommerce', $force, array(
			'css'  => array(
				'trx_addons-woocommerce-responsive' => array(
					'src' => TRX_ADDONS_PLUGIN_API . 'woocommerce/woocommerce.responsive.css',
					'media' => 'sm'
				),
			),
		) );
	}
}
	
if ( ! function_exists( 'trx_addons_woocommerce_merge_styles' ) ) {
	add_filter( 'trx_addons_filter_merge_styles', 'trx_addons_woocommerce_merge_styles' );
	/**
	 * Add WooCommerce styles to the list of merged styles
	 * 
	 * @hooked trx_addons_filter_merge_styles
	 *
	 * @param array $list  List of stylesheets to merge. Use URL as key and true|false as value to load or not load this stylesheet
	 *                     always or only if the plugin is present on the current page.
	 * 
	 * @return array  Modified list
	 */
	function trx_addons_woocommerce_merge_styles( $list ) {
		if ( trx_addons_exists_woocommerce() ) {
			$list[ TRX_ADDONS_PLUGIN_API . 'woocommerce/woocommerce.css' ] = false;
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_merge_styles_responsive' ) ) {
	add_filter( 'trx_addons_filter_merge_styles_responsive', 'trx_addons_woocommerce_merge_styles_responsive' );
	/**
	 * Add WooCommerce responsive styles to the list of merged styles
	 * 
	 * @hooked trx_addons_filter_merge_styles_responsive
	 *
	 * @param array $list  List of stylesheets to merge. Use URL as key and true|false as value to load or not load this stylesheet
	 *                     always or only if the plugin is present on the current page.
	 * 
	 * @return array  Modified list
	 */
	function trx_addons_woocommerce_merge_styles_responsive( $list ) {
		if ( trx_addons_exists_woocommerce() ) {
			$list[ TRX_ADDONS_PLUGIN_API . 'woocommerce/woocommerce.responsive.css' ] = false;
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_merge_scripts' ) ) {
	add_action( 'trx_addons_filter_merge_scripts', 'trx_addons_woocommerce_merge_scripts', 11 );
	/**
	 * Add WooCommerce scripts to the list of merged scripts
	 * 
	 * @hooked trx_addons_filter_merge_scripts
	 *
	 * @param array $list  List of scripts to merge. Use URL as key and true|false as value to load or not load this script
	 *                     always or only if the plugin is present on the current page.
	 * 
	 * @return array  Modified list
	 */
	function trx_addons_woocommerce_merge_scripts( $list ) {
		if ( trx_addons_exists_woocommerce() ) {
			$list[ TRX_ADDONS_PLUGIN_API . 'woocommerce/woocommerce.js' ] = false;
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_woocommerce_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_woocommerce_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_woocommerce_check_in_html_output', 10, 1 );
	/**
	 * Check if WooCommerce is present in the specified HTML content and force load required styles and scripts
	 * 
	 * @hooked trx_addons_filter_get_menu_cache_html
	 * @hooked trx_addons_action_show_layout_from_cache
	 * @hooked trx_addons_action_check_page_content
	 * 
	 * @param string $content  HTML content to check
	 * 
	 * @return string  Modified HTML content
	 */
	function trx_addons_woocommerce_check_in_html_output( $content = '' ) {
		if ( ! trx_addons_exists_woocommerce() ) {
			return $content;
		}
		$args = array(
			'check' => array(
				'<(div|ul|li)[^>]*class=[\'"][^\'"]*(woocommerce|wc\\-block\\-grid__product)',
//				'class=[\'"][^\'"]*sc_layouts_cart',
				'class=[\'"][^\'"]*type\\-(product|product_variation|shop_coupon|shop_webhook)',
				'class=[\'"][^\'"]*(product_type|product_visibility|product_cat|product_tag|product_shipping_class)\\-',
			)
		);
		if ( trx_addons_check_in_html_output( 'woocommerce', $content, $args ) ) {
			trx_addons_woocommerce_load_scripts_front( true );
		}
		return $content;
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_filter_head_output' ) ) {
	add_filter( 'trx_addons_filter_page_head', 'trx_addons_woocommerce_filter_head_output', 10, 1 );
	/**
	 * Remove WooCommerce styles and scripts from the page head if they are not used on the current page
	 * and optimize CSS/JS loading is enabled
	 * 
	 * @hooked trx_addons_filter_page_head
	 * 
	 * @trigger trx_addons_filter_remove_3rd_party_styles
	 * 
	 * @param string $content  Page head content
	 * 
	 * @return string  Modified page head content
	 */
	function trx_addons_woocommerce_filter_head_output( $content = '' ) {
		if ( ! trx_addons_exists_woocommerce() ) {
			return $content;
		}
		return trx_addons_filter_head_output( 'woocommerce', $content, array(
			'check' => array(
				'#<link[^>]*href=[\'"][^\'"]*/woocommerce/assets/[^>]*>#',
				'#<style[^>]*id=[\'"]woocommerce-[^>]*>[\\s\\S]*</style>#U'
			)
		) );
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_filter_body_output' ) ) {
	add_filter( 'trx_addons_filter_page_content', 'trx_addons_woocommerce_filter_body_output', 10, 1 );
	/**
	 * Remove WooCommerce styles and scripts from the page body if they are not used on the current page
	 * and optimize CSS/JS loading is enabled
	 * 
	 * @hooked trx_addons_filter_page_content
	 * 
	 * @trigger trx_addons_filter_remove_3rd_party_styles
	 * 
	 * @param string $content  Page body content
	 * 
	 * @return string  Modified page body content
	 */
	function trx_addons_woocommerce_filter_body_output( $content = '' ) {
		if ( ! trx_addons_exists_woocommerce() ) {
			return $content;
		}
		return trx_addons_filter_body_output( 'woocommerce', $content, array(
			'check' => array(
				'#<link[^>]*href=[\'"][^\'"]*/woocommerce/assets/[^>]*>#',
				'#<script[^>]*src=[\'"][^\'"]*/woocommerce/assets/[^>]*>[\\s\\S]*</script>#U',
				'#<script[^>]*id=[\'"]woocommerce-[^>]*>[\\s\\S]*</script>#U',
				'#<script[^>]*id=[\'"]wc-cart-[^>]*>[\\s\\S]*</script>#U',
				'#<script[^>]*id=[\'"]wc-add-to-cart-[^>]*>[\\s\\S]*</script>#U',
			)
		) );
	}
}


// Load WooCommerce Extended Attributes
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'woocommerce/woocommerce-extended-attributes.php';

// Load WooCommerce Extended Shortcode 'Products'
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'woocommerce/woocommerce-extended-products.php';

// Load WooCommerce Search Widget
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'woocommerce/widget.woocommerce_search.php';

// Load WooCommerce Title Widget
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'woocommerce/widget.woocommerce_title.php';

// Add Elementor's support
if ( trx_addons_exists_woocommerce() && trx_addons_exists_elementor() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'woocommerce/woocommerce-sc-elementor.php';
}


// Demo data install
//----------------------------------------------------------------------------

// One-click import support
if ( is_admin() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'woocommerce/woocommerce-demo-importer.php';
}

// OCDI support
if ( is_admin() && trx_addons_exists_woocommerce() && function_exists( 'trx_addons_exists_ocdi' ) && trx_addons_exists_ocdi() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'woocommerce/woocommerce-demo-ocdi.php';
}
