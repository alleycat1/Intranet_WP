<?php
/**
 * Plugin support: WooCommerce Extended Attributes
 *
 * @package ThemeREX Addons
 * @since v1.6.13
 */

if ( ! function_exists( 'trx_addons_woocommerce_attrib_init' ) ) {
	add_action( 'init',	'trx_addons_woocommerce_attrib_init' );
	/**
	 * Init hooks to add custom fields to the WooCommerce attributes
	 */
	function trx_addons_woocommerce_attrib_init() {
		if ( ! trx_addons_exists_woocommerce() ) {
			return;
		}
		$attribute_taxonomies = wc_get_attribute_taxonomies();
		if ( ! empty( $attribute_taxonomies ) ) {
			foreach ( $attribute_taxonomies as $attribute ) {
				$tax = wc_attribute_taxonomy_name( $attribute->attribute_name );
				add_action( $tax . '_edit_form_fields',          'trx_addons_woocommerce_attrib_show_custom_fields', 10, 1 );
				add_action( $tax . '_add_form_fields',           'trx_addons_woocommerce_attrib_show_custom_fields', 10, 1 );
				add_action( 'edited_' . $tax,                    'trx_addons_woocommerce_attrib_save_custom_fields', 10, 1 );
				add_action( 'created_' . $tax,                   'trx_addons_woocommerce_attrib_save_custom_fields', 10, 1 );
				add_filter( 'manage_edit-' . $tax . '_columns',	 'trx_addons_woocommerce_attrib_add_custom_column',   9 );
				add_action( 'manage_' . $tax . '_custom_column', 'trx_addons_woocommerce_attrib_fill_custom_column',  9, 3 );
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_attrib_add_types' ) ) {
	add_filter( 'product_attributes_type_selector',	'trx_addons_woocommerce_attrib_add_types' );
	/**
	 * Add custom types 'Color', 'Image' and 'Button' to the WooCommerce attributes
	 *
	 * @param array $list  List of the types
	 * 
	 * @return array  List of the types
	 */
	function trx_addons_woocommerce_attrib_add_types( $list = array() ) {
		return array_merge( $list, array(
									'color' => esc_html__('Color', 'trx_addons'),
									'image' => esc_html__('Image', 'trx_addons'),
									'button' => esc_html__('Button', 'trx_addons')
							) );
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_attrib_get_type' ) ) {
	/**
	 * Check if a taxomony is a Woocommerce product's attribute and return its type
	 *
	 * @param string $taxonomy  Name of the taxonomy
	 * 
	 * @return string  Type of the attribute
	 */
	function trx_addons_woocommerce_attrib_get_type( $taxonomy ) {
		$type = '';
		if ( trx_addons_exists_woocommerce() ) {
			$attribute_taxonomies = wc_get_attribute_taxonomies();
			if ( ! empty( $attribute_taxonomies ) ) {
				foreach ( $attribute_taxonomies as $attribute ) {
					if ( wc_attribute_taxonomy_name( $attribute->attribute_name ) == $taxonomy ) {
						$type = $attribute->attribute_type;
						break;
					}
				}
			}
		}
		return $type;
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_attrib_get_type_name' ) ) {
	/**
	 * Return the name (title) of the attribute type
	 *
	 * @param string $type  Type of the attribute
	 * 
	 * @return string  Name of the attribute type
	 */
	function trx_addons_woocommerce_attrib_get_type_name( $type ) {
		if (      $type == 'image' )	return __('Image', 'trx_addons');
		else if ( $type == 'color' )	return __('Color', 'trx_addons');
		else if ( $type == 'button' )	return __('Button', 'trx_addons');
		else if ( $type == 'select' ) 	return __('Select', 'trx_addons');
		else 				 			return __('Text', 'trx_addons');
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_attrib_show_custom_fields' ) ) {
	//Hook of the add_action('pa_xxx_edit_form_fields',	'trx_addons_woocommerce_attrib_show_custom_fields', 10, 1 );
	//Hook of the add_action('pa_xxx_add_form_fields',	'trx_addons_woocommerce_attrib_show_custom_fields', 10, 1 );
	/**
	 * Show custom fields in the WooCommerce attributes
	 * 
	 * @hooked pa_xxx_add_form_fields
	 * @hooked pa_xxx_edit_form_fields
	 *
	 * @param object $term  Current term object
	 */
	function trx_addons_woocommerce_attrib_show_custom_fields( $term ) {
		$term_id = ! empty( $term->term_id ) ? $term->term_id : 0;
		$taxonomy = (int) $term_id > 0 ? $term->taxonomy : $term;
		$type = trx_addons_woocommerce_attrib_get_type( $taxonomy );
		if ( empty( $type ) || ! in_array( $type, array( 'color', 'image' ) ) ) {
			return;
		}
		$term_val = $term_id == 0 ? '' : trx_addons_get_term_meta( $term_id ); 
		$field_name = "trx_addons_{$taxonomy}_{$type}";
		echo ( (int) $term_id > 0 ? '<tr' : '<div') . ' class="form-field">'
			. ( (int) $term_id > 0 ? '<th valign="top" scope="row">' : '<div>' );
		?><label for="<?php echo esc_attr($field_name); ?>"><?php echo esc_html( trx_addons_woocommerce_attrib_get_type_name( $type ) ); ?>:</label><?php
		echo ( (int) $term_id > 0 ? '</th>' : '</div>' )
			. ( (int) $term_id > 0 ? '<td valign="top">' : '<div>' );
		if ( $type == 'image' ) {
			?><input type="hidden" class="trx_addons_thumb_selector_field" id="<?php echo esc_attr( $field_name ); ?>" name="<?php echo esc_attr($field_name); ?>" value="<?php echo esc_url($term_val); ?>"><?php
			if ( empty( $term_val ) ) {
				$term_val = apply_filters( 'trx_addons_filter_no_thumb', trx_addons_get_file_url( 'css/images/no-thumb.gif' ) );
			}
			trx_addons_show_layout( trx_addons_options_show_custom_field( $field_name . '_button', array(
					'type' => 'mediamanager',
					'linked_field_id' => $field_name
				), $term_val )
			);
		} else if ( $type == 'color' ) {
			?><input type="text" class="trx_addons_color_selector" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $term_val ); ?>"><?php
		}
		echo (int) $term_id > 0 ? '</td></tr>' : '</div></div>';
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_attrib_save_custom_fields' ) ) {
	//Hook of the add_action('edited_pa_xxx',	'trx_addons_woocommerce_attrib_save_custom_fields', 10, 1 );
	//Hook of the add_action('created_pa_xxx',	'trx_addons_woocommerce_attrib_save_custom_fields', 10, 1 );
	/**
	 * Save custom fields for the WooCommerce attributes ("pa_xxx" taxonomy)
	 * 
	 * @hooked created_pa_xxx
	 * @hooked edited_pa_xxx
	 *
	 * @param int $term_id  Current term ID
	 */
	function trx_addons_woocommerce_attrib_save_custom_fields( $term_id ) {
		$taxonomy = str_replace( array( 'edited_', 'created_' ), '' , current_action() );
		$type = trx_addons_woocommerce_attrib_get_type( $taxonomy );
		if ( empty( $type ) ) {
			return;
		}
		$field_name = "trx_addons_{$taxonomy}_{$type}";
		if ( isset( $_POST[ $field_name ] ) ) {
			trx_addons_set_term_meta( $term_id, $_POST[ $field_name ] );
		}
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_attrib_add_custom_column' ) ) {
	//Hook of the add_filter('manage_edit-pa_xxx_columns',	'trx_addons_woocommerce_attrib_add_custom_column', 9);
	/**
	 * Add custom column in the 'pa_xxx' taxonomy list
	 * 
	 * @hooked manage_edit-pa_xxx_columns
	 *
	 * @param array $columns  List of columns
	 * 
	 * @return array  	  Modified list of columns
	 */
	function trx_addons_woocommerce_attrib_add_custom_column( $columns ) {
		$taxonomy = str_replace( array( 'manage_edit-', '_columns' ), '' , current_action() );
		$type = trx_addons_woocommerce_attrib_get_type( $taxonomy );
		if ( in_array( $type, array( 'color', 'image' ) ) ) { 
			$columns['pa_extended_attribute'] = esc_html( trx_addons_woocommerce_attrib_get_type_name( $type ) );
		}
		return $columns;
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_attrib_fill_custom_column' ) ) {
	//Hook of the add_action('manage_pa_xxx_custom_column',	'trx_addons_woocommerce_attrib_fill_custom_column', 9, 3);
	/**
	 * Fill custom column in the 'pa_xxx' taxonomy list
	 * 
	 * @hooked manage_pa_xxx_custom_column
	 *
	 * @param string $output      Custom column output
	 * @param string $column_name Current column name
	 * @param int    $tax_id      Current term ID
	 */
	function trx_addons_woocommerce_attrib_fill_custom_column( $output = '', $column_name = '', $tax_id = 0 ) {
		if ( $column_name == 'pa_extended_attribute' && ( $val = trx_addons_get_term_meta( $tax_id ) ) ) {
			$taxonomy = str_replace( array( 'manage_', '_custom_column' ), '' , current_action() );
			$type = trx_addons_woocommerce_attrib_get_type( $taxonomy );
			if ( $type == 'image' ) {
				?><img class="trx_addons_thumb_selector_preview" src="<?php echo esc_url( trx_addons_add_thumb_size( $val, trx_addons_get_thumb_size( 'tiny' ) ) ); ?>" alt="<?php esc_attr_e( 'Thumb selector', 'trx_addons' ); ?>"><?php
			} else if ( $type == 'color' ) {
				?><div class="trx_addons_color_selector_preview" style="background-color:<?php echo esc_attr( $val ); ?>"></div><?php
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_attrib_add_fields_to_attributes_tab' ) ) {
	add_action( 'woocommerce_product_option_terms',	'trx_addons_woocommerce_attrib_add_fields_to_attributes_tab', 10, 2 );
	/**
	 * Add custom fields to the 'pa_xxx' taxonomy tab in the product edit mode
	 * 
	 * @hooked woocommerce_product_option_terms
	 * 
	 * @trigger woocommerce_product_attribute_terms
	 * @trigger woocommerce_product_attribute_term_name
	 *
	 * @param object $attribute_taxonomy  Current attribute taxonomy
	 * @param int    $i                   Current attribute number
	 */
	function trx_addons_woocommerce_attrib_add_fields_to_attributes_tab( $attribute_taxonomy, $i ){
		if ( in_array( $attribute_taxonomy->attribute_type, array( 'image', 'color', 'button' ) ) ) {
			?><select multiple="multiple" data-placeholder="<?php esc_attr_e( 'Select terms', 'trx_addons' ); ?>" class="multiselect attribute_values wc-enhanced-select" name="attribute_values[<?php echo esc_attr( $i ); ?>][]">
				<?php
				$args = array(
						'orderby'    => 'name',
						'hide_empty' => 0
						);
				$tax_name  = wc_attribute_taxonomy_name( $attribute_taxonomy->attribute_name );
				$all_terms = get_terms( $tax_name, apply_filters( 'woocommerce_product_attribute_terms', $args ) );
				$post_id   = wp_doing_ajax() && ! empty( $_POST['post_id'] )
								? (int) $_POST['post_id']
								: trx_addons_get_edited_post_id();
				if ( is_array( $all_terms ) ) {
					foreach ( $all_terms as $term ) {
						echo '<option value="' 
								. esc_attr( version_compare( WOOCOMMERCE_VERSION, '3.0', '<' ) ? $term->term_slug : $term->term_id ) . '" ' 
								. ( $post_id > 0 ? selected( has_term( absint( $term->term_id ), $tax_name, $post_id ), true, false ) : '' )
								. '>' 
									. esc_attr( apply_filters( 'woocommerce_product_attribute_term_name', $term->name, $term ) ) 
								. '</option>';
					}
				}
			?></select>
			<button class="button plus select_all_attributes"><?php esc_html_e( 'Select all', 'trx_addons' ); ?></button>
			<button class="button minus select_no_attributes"><?php esc_html_e( 'Select none', 'trx_addons' ); ?></button>
			<button class="button fr plus add_new_attribute"><?php esc_html_e( 'Add new', 'trx_addons' ); ?></button>
			<?php
		}
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_attrib_show_single_product' ) ) {
	add_filter( 'woocommerce_dropdown_variation_attribute_options_html', 'trx_addons_woocommerce_attrib_show_single_product', 10, 2 );
	/**
	 * Show custom attributes on the Single product page
	 * 
	 * @hooked woocommerce_dropdown_variation_attribute_options_html
	 *
	 * @param string $html  HTML with attributes layout
	 * @param array  $args  Arguments for the attributes
	 * 
	 * @return string       Modified HTML with attributes layout
	 */
	function trx_addons_woocommerce_attrib_show_single_product( $html, $args ) {
		$type = trx_addons_woocommerce_attrib_get_type( $args['attribute'] );
		if ( in_array( $type, array( 'image', 'color', 'button' ) ) ) {
			$output = '';
			// Remove 'false' from condition below to show "Choose option" item
			if ( false && $args['show_option_none'] ) {
				$no_img = $type=='image' ? apply_filters('trx_addons_filter_no_thumb', trx_addons_get_file_url('css/images/no-thumb.gif')) : '';
				$output .= '<span class="trx_addons_attrib_item trx_addons_attrib_'.esc_attr($type).' trx_addons_tooltip'
									. ( sanitize_title( $args['selected'] ) == '' ? ' trx_addons_attrib_selected' : '' )
									. '"'
									. ' data-value=""'
									. ' data-tooltip-text="' . esc_attr($args['show_option_none']) . '"'
									. '>'
									. '<span>'
										. ($type=='image'
												? '<img src="' . esc_url($no_img) . '" alt="' . esc_attr($args['show_option_none']) . '">'
												: ( $type=='button' ? esc_html($args['show_option_none']) : '' )
											)
									. '</span>'
							. '</span>';
			}
			if ( ! empty( $args['options'] ) ) {
				if ( $args['product'] && taxonomy_exists( $args['attribute'] ) ) {
					// Get terms if this is a taxonomy - ordered. We need the names too.
					$terms = wc_get_product_terms( $args['product']->get_id(), $args['attribute'], array( 'fields' => 'all' ) );
					foreach ( $terms as $term ) {
						if ( in_array( $term->slug, $args['options'] ) ) {
							$term_val = trx_addons_get_term_meta( $term->term_id );
							if ( $type == 'image' ) {
								$term_val = empty( $term_val ) ? $no_img : trx_addons_add_thumb_size( $term_val, trx_addons_get_thumb_size( 'tiny' ) );
							} else if ( $type == 'color' ) {
								$term_val = empty( $term_val ) ? urldecode( $term->slug ) : $term_val;
							}
							$output .= '<span class="trx_addons_attrib_item trx_addons_attrib_'.esc_attr($type).' trx_addons_tooltip'
												. ( sanitize_title( $args['selected'] ) == $term->slug ? ' trx_addons_attrib_selected' : '' )
												. '"'
												. ' data-value="' . esc_attr( urldecode( $term->slug ) ) . '"'
												. ' data-tooltip-text="' . esc_attr($term->name) . '"'
												. '>'
												. '<span' . ( $type == 'color' ? ' style="background-color:' . esc_attr( $term_val ) . ';"' : '') . '>'
													. ( $type == 'image'
															? '<img src="' . esc_url( $term_val ) . '" alt="' . esc_attr( $term->name ) . '">'
															: ( $type == 'button' ? esc_html( $term->name ) : '')
														)
												. '</span>'
										. '</span>';
						}
					}
				}
			}
			if ( $output ) {
				$html = str_replace('<select ', '<select class="trx_addons_attrib_' . esc_attr( $type ) . '" style="display:none !important;" ', $html );
				$html .= '<div id="' . esc_attr( ! empty( $args['id'] ) ? $args['id'] : sanitize_title( $args['attribute'] ) ) . '_attrib_extended"'
							. ' class="' . esc_attr( ! empty( $args['class'] ) ? $args['class'] : sanitize_title( $args['attribute'] ) ) . '_attrib_extended trx_addons_attrib_extended"'
							. ' data-attrib="' . esc_attr( $args['attribute'] ) . '">'
							. $output
						. '</div>';
			}
		}
		return $html;
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_attrib_show_in_product_list' ) ) {
	add_action( 'trx_addons_action_product_attributes', 'trx_addons_woocommerce_attrib_show_in_product_list', 10, 1 );
	/**
	 * Show custom attributes in products on the Product list page
	 * 
	 * @hooked trx_addons_action_product_attributes
	 *
	 * @param array  $args  Arguments for the attributes
	 */
	function trx_addons_woocommerce_attrib_show_in_product_list( $args ) {
		global $product;
		global $TRX_ADDONS_STORAGE;
		if ( ! empty( $args['attributes'] ) && is_object( $product ) ) {
			if ( empty( $args['action'] ) ) {
				$args['action'] = 'none';
			}
			$need_variations = ( $args['action'] == 'swap' || $args['swap'] > 0 ) && $product->get_type() == 'variable';
			if ( ! $need_variations && $args['action'] == 'swap' ) {
				$args['action'] = 'none';
			}
			$TRX_ADDONS_STORAGE['woocommerce_variations_started'] = true;

			// Get attributes from the theme options
			parse_str( str_replace( '|', '&', $args['attributes'] ), $attributes );

			// Add attributes marked as 'Visible on the products page'
			$product_attributes = $product->get_attributes();
			if ( is_array( $product_attributes ) ) {
				foreach( $product_attributes as $att ) {
					if ( $att->is_taxonomy() && $att->get_visible() && ! isset( $attributes[ $att->get_name() ] ) ) {
						$attributes[] = $att->get_name() . '=1';
					}
				}
			}

			// Add filter to include price html to each variation.
			// By default WooCommerce leave empty a key 'price_html' if all variations have an equal price.
			// Commented because a condition for empty 'price_html' before replace price on variation changed is added.
			//add_filter( 'woocommerce_show_variation_price', '__return_true' );
			$variations = $need_variations ? $product->get_available_variations() : '';
			//remove_filter( 'woocommerce_show_variation_price', '__return_true' );

			$defaults = $need_variations ? $product->get_default_attributes() : '';

			$TRX_ADDONS_STORAGE['woocommerce_variations_started'] = false;
			$tooltips = apply_filters( 'trx_addons_filter_woocommerce_attributes_tooltip', true );
			$html = '';
			foreach( $attributes as $attr_name => $attr_val ) {
				if ( ! empty( $attr_val ) && taxonomy_exists( $attr_name ) ) {
					$type = trx_addons_woocommerce_attrib_get_type( $attr_name );
					$attribute_obj = get_taxonomy( $attr_name );
					$terms = wc_get_product_terms( $product->get_id(), $attr_name, array( 'fields' => 'all' ) );
					$output = '';
					foreach ( $terms as $term ) {
						$term_val = get_term_meta( $term->term_id, 'value', true );
						if ( $type == 'image' ) {
							$term_val = empty( $term_val ) ? trx_addons_get_no_image() : trx_addons_add_thumb_size( $term_val, trx_addons_get_thumb_size( 'tiny' ) );
						} else if ( $type == 'color' ) {
							$term_val = empty( $term_val ) ? urldecode( $term->slug ) : $term_val;
						}
						$output .= '<span class="trx_addons_product_attribute_item'
											. ( $tooltips ? ' trx_addons_tooltip' : '' )
											. ( $args['action'] == 'swap' && ! empty( $defaults[ $attr_name ] ) && $defaults[ $attr_name ] == urldecode( $term->slug )
												? ' trx_addons_product_attribute_item_active'
												: ''
												)
										. '"'
										. ' data-attribute="' . esc_attr( $attr_name ) . '"'
										. ' data-type="' . esc_attr( $type ) . '"'
										. ' data-value="' . esc_attr( urldecode( $term->slug ) ) . '"'
										. ( $tooltips
											? ' data-tooltip-text="' . ( $args['action'] == 'filter'
																			? esc_attr( sprintf( __( 'Filter by %s', 'trx_addons' ), $term->name ) )
																			: esc_attr( $term->name )
																		)
												. '"'
											: '' )
									. '>'
										. ( $args['action'] != 'none'
											? '<a href="' . ( $args['action'] == 'link' ? esc_url( $product->get_permalink() ) : '#' ) . '"'
												. ' class="trx_addons_product_attribute_item_action_' . esc_attr( $args['action'] ) . '"'
												. '>'
											: ''
											)
										. ( $type == 'image' 
												? '<img src="' . esc_url( $term_val ) . '" alt="' . esc_attr( $term->name ) . '">'
												: ( $type == 'color'
													? '<span class="trx_addons_product_attribute_item_inner ' . trx_addons_add_inline_css_class( 'background-color:' . esc_attr( $term_val ) . ';' ) . '"></span>'
													: esc_html( $term->name )
													)
											)
										. ( $args['action'] != 'none'
											? '</a>'
											: ''
											)
									. '</span>';
					}
					if ( ! empty( $output ) ) {
						$html .= sprintf( '<div class="trx_addons_product_attribute trx_addons_product_attribute_type_%1$s"'
												. ' data-attribute="%2$s"'
											. '>'
												. '<span class="trx_addons_product_attribute_label">%3$s</span>'
												. '%4$s'
											. '</div>',
											esc_attr( $type ),
											esc_attr( $attr_name ),
											esc_html( $attribute_obj->labels->singular_name ),
											$output
										);
					}
				}
			}
			if ( ! empty( $html ) ) {
				trx_addons_show_layout(
					$html,
					sprintf( '<div class="trx_addons_product_attributes"%s>',
								$need_variations && ! empty( $variations )
									? ' data-product-variations="' . esc_attr( json_encode( $variations ) ) . '"'
									: ''
							),
					'</div>'
				);
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_attrib_variation_image_size_in_product_list' ) ) {
	add_filter( 'woocommerce_gallery_image_size', 'trx_addons_woocommerce_attrib_variation_image_size_in_product_list', 10, 1 );
	/**
	 * Change image size for variations in the product list
	 *
	 * @param string $size_name  Image size.
	 * 
	 * @return string  		Image size.
	 */
	function trx_addons_woocommerce_attrib_variation_image_size_in_product_list( $size_name ) {
		global $TRX_ADDONS_STORAGE;
		if ( ! empty( $TRX_ADDONS_STORAGE['woocommerce_variations_started'] ) ) {
			$size_name = apply_filters( 'single_product_archive_thumbnail_size', 'woocommerce_thumbnail' );
		}
		return $size_name;
	}
}
