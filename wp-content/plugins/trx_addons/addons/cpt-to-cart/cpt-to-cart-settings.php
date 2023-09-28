<?php
/**
* Add a group with settings to Woocommerce - Settings - Product
*
* @addon cpt-to-cart
* @version 1.0
*
* @package ThemeREX Addons
* @since v2.13.0
*/

if ( ! function_exists( 'trx_addons_cpt_to_cart_add_product_settings_section' ) ) {
	add_filter( 'woocommerce_get_sections_products', 'trx_addons_cpt_to_cart_add_product_settings_section', 200, 1 );
	/**
	 * Add a new section to the tab 'Products' to the WooCommerce - Settings.
	 * 
	 * @param array $sections  An array with a section names and titles.
	 * 
	 * @return array  A modified array with sections.
	 */
	function trx_addons_cpt_to_cart_add_product_settings_section( $sections ) {
		$sections['cpt_to_cart'] = __( 'Custom post types', 'trx_addons' );
		return $sections;
	}
}

if ( ! function_exists( 'trx_addons_cpt_to_cart_add_product_settings' ) ) {
	add_filter( 'woocommerce_get_settings_products', 'trx_addons_cpt_to_cart_add_product_settings', 200, 2 );
	/**
	 * Add settings for each post_type to the WooCommerce - Settings - Products - Custom post types.
	 * 
	 * @param array $settings     An array with a section settings.
	 * @param string $section_id  ID of the current section
	 * 
	 * @return array  A modified array with settings of the section.
	 */
	function trx_addons_cpt_to_cart_add_product_settings( $settings, $section_id ) {
		if ( 'cpt_to_cart' === $section_id ) {
			$settings[] = array(
				'title' => __( 'Custom post types', 'trx_addons' ),
				'desc'  => __( 'Allow custom type posts to be added to WooCommerce cart same as products', 'trx_addons' ),
				'type'  => 'title',
			);
			$settings[] = array(
				'type' => 'cpt_to_cart_options',
			);
			$settings[] = array( 'type' => 'sectionend' );
		}
		
		return $settings;
	}
}

if ( ! function_exists( 'trx_addons_cpt_to_cart_display_product_settings' ) ) {
	add_action( 'woocommerce_admin_field_cpt_to_cart_options', 'trx_addons_cpt_to_cart_display_product_settings' );
	/**
	 * Output settings for each post type in the WooCommerce - Settings - Products - Custom post types.
	 * 
	 * Hook: add_action( 'woocommerce_admin_field_cpt_to_cart_options', 'trx_addons_cpt_to_cart_display_product_settings' );
	 */
	function trx_addons_cpt_to_cart_display_product_settings() {
		$options = get_option( 'trx_addons_options_cpt_to_cart' );
		?>
		<tr valign="top">
			<td class="cpt_to_cart_options_wrapper" colspan="2">
				<table class="cpt_to_cart_options widefat" cellspacing="0">
					<thead>
						<tr>
							<?php
							$default_columns = array(
								'label'   => __( 'Post type', 'trx_addons' ),
								'name'    => __( 'Name', 'trx_addons' ),
								'allowed' => __( 'Allowed', 'trx_addons' ),
								'options' => __( 'Options', 'trx_addons' ),
							);

							$columns = apply_filters( 'trx_addons_filter_cpt_to_cart_options_columns', $default_columns );

							foreach ( $columns as $key => $column ) {
								echo '<th class="cpt_to_cart_options_column_' . esc_attr( $key ) . '">' . esc_html( $column ) . '</th>';
							}
							?>
						</tr>
					</thead>
					<tbody>
						<?php
						// Exclude post types
						$exclude = apply_filters( 'trx_addons_filter_exclude_cpt_from_cart', array( 'product', 'post', 'page', 'attachment' ) );
						// Get registered post types
						$types = get_post_types( array( 'public' => true, 'exclude_from_search' => false ), 'objects' );
						if ( is_array( $types ) ) {
							foreach ( $types as $slug => $type ) {
								if ( in_array( $type->name, $exclude ) ) {
									continue;
								}
								echo '<tr data-cpt_name="' . esc_attr( $type->name ) . '">';
								foreach ( $columns as $key => $column ) {
									if ( ! array_key_exists( $key, $default_columns ) ) {
										do_action( 'trx_addons_action_cpt_to_cart_options_column_' . $key, $post_type );
										continue;
									}
									echo '<td class="cpt_to_cart_options_column_' . esc_attr( $key ) . '">';
									switch ( $key ) {
										case 'label':
											echo esc_html( $type->label );
											break;
										case 'name':
											echo esc_html( $type->name );
											break;
										case 'allowed':
											echo '<input type="checkbox" class="cpt_to_cart_options_field_allow"'
														. ' name="' . sprintf( 'trx_addons_options_cpt_to_cart[%s][allow]', $type->name ) . '"'
														. ( ! empty( $options[ $type->name ]['allow'] ) ? ' checked="checked"' : '' )
														. ' value="1"'
													. ' />';
											break;
										case 'options':
											// Button 'Options'
											echo '<input type="button" class="cpt_to_cart_options_button_popup"'
														. ( empty( $options[ $type->name ]['allow'] ) ? ' disabled="disabled"' : '' )
														. ' value="' . esc_attr__( 'Options', 'trx_addons' ) . '"'
													. ' />';
											// Popup with options
											echo '<div class="cpt_to_cart_options_popup">';
											// Button 'Close'
											echo '<a href="javascript:void" class="cpt_to_cart_options_popup_close"></a>';
											// Title
											echo '<p class="cpt_to_cart_options_popup_title">' . esc_html( sprintf( __( '%s options', 'trx_addons' ), $type->label ) ) . '</p>';
											// Field 'New price'
											echo '<div class="cpt_to_cart_options_popup_field cpt_to_cart_options_popup_field_type_checkbox">'
													. '<label>'
														. '<input type="checkbox"'
															. ' name="' . sprintf( 'trx_addons_options_cpt_to_cart[%s][price_new]', $type->name ) . '"'
															. ( ! empty( $options[ $type->name ]['price_new'] ) ? ' checked="checked"' : '' )
															. ' value="1"'
														. ' />'
														. wp_kses_data( 'Add a new meta field for the price', 'trx_addons' )
													. '</label>'
												. '</div>';
											// Field 'Price meta name'
											echo '<div class="cpt_to_cart_options_popup_field cpt_to_cart_options_popup_field_type_text">'
													. '<label>'
														. wp_kses_data( 'or specify a custom field name with a price', 'trx_addons' )
													. '</label>'
													. '<input type="text"'
														. ' name="' . sprintf( 'trx_addons_options_cpt_to_cart[%s][price_name]', $type->name ) . '"'
														. ' value="' . ( ! empty( $options[ $type->name ]['price_name'] ) ? $options[ $type->name ]['price_name'] : '' ) . '"'
													. ' />'
													. '<p class="cpt_to_cart_options_popup_description">'
														. wp_kses_data( 'If the price field is part of an array with options - specify it like this: <b>options_array_name[price_field_name]</b>', 'trx_addons' )
														. '<br>'
														. wp_kses_data( 'To combine two or more fields - list them through the plus sign, for example: <b>_EventCurrencySymbol + _EventCost</b>', 'trx_addons' )
													. '</p>'
												. '</div>';
											// Event listeners
											echo '<p class="cpt_to_cart_options_popup_subtitle">' . esc_html__( 'Add event listeners', 'trx_addons' ) . '</p>';
											echo '<p class="cpt_to_cart_options_popup_description">' . esc_html__( 'to inject a link or a button into an existing post layout', 'trx_addons' ) . '</p>';
											echo '<div class="cpt_to_cart_options_popup_event_listeners">';
											// Labels
											echo '<div class="cpt_to_cart_options_popup_field cpt_to_cart_options_popup_field_type_group">'
												// Event type
												. '<div class="cpt_to_cart_options_popup_group_item cpt_to_cart_options_popup_group_type_select cpt_to_cart_options_popup_group_field_event_type">'
													. '<label>' . esc_html__( 'Event', 'trx_addons' ) . '</label>'
												. '</div>'
												// Event name
												. '<div class="cpt_to_cart_options_popup_group_item cpt_to_cart_options_popup_group_type_text cpt_to_cart_options_popup_group_field_event_name">'
													. '<label>' . esc_html__( 'Event name', 'trx_addons' ) . '</label>'
												. '</div>'
												// Link type
												. '<div class="cpt_to_cart_options_popup_group_item cpt_to_cart_options_popup_group_type_select cpt_to_cart_options_popup_group_field_link_type">'
													. '<label>' . esc_html__( 'Link type', 'trx_addons' ) . '</label>'
												. '</div>'
												// Area
												. '<div class="cpt_to_cart_options_popup_group_item cpt_to_cart_options_popup_group_type_select cpt_to_cart_options_popup_group_field_area">'
													. '<label>' . esc_html__( 'Area', 'trx_addons' ) . '</label>'
												. '</div>'
												// Place to
												. '<div class="cpt_to_cart_options_popup_group_item cpt_to_cart_options_popup_group_type_select cpt_to_cart_options_popup_group_field_place">'
													. '<label data-tooltip-text="' . esc_attr__( 'Where to place link to the filtered value? Only for event Filter!', 'trx_addons' ) . '">'
														. esc_html__( 'Place to', 'trx_addons' )
														. '<span class="trx_addons_icon-help-circled"></span>'
													. '</label>'
												. '</div>'
												// Button 'Remove listener'
												. '<div class="cpt_to_cart_options_popup_group_item cpt_to_cart_options_popup_group_type_button cpt_to_cart_options_popup_group_field_remove_listener">'
													//. '<label>' . esc_html__( 'Remove', 'trx_addons' ) . '</label>'
												. '</div>'
											. '</div>';
											$total = 0;
											if ( ! empty( $options[ $type->name ]['events'] ) && is_array( $options[ $type->name ]['events'] ) ) {
												$total = count( $options[ $type->name ]['events'] );
												for ( $i = 0; $i < $total; $i++ ) {
													trx_addons_show_layout( trx_addons_cpt_to_cart_get_settings_group( $type->name, $i, $options[ $type->name ]['events'][ $i ] ) );
												}
											}
											echo '</div>';
											// Button 'Add listener'
											echo '<input type="button" class="cpt_to_cart_options_popup_add_listener"'
													. ' value="' . esc_attr__( 'Add listener', 'trx_addons' ) . '"'
													. ' data-index="' . $total . '"'
													. ' data-group="' . esc_attr( str_replace(
																						'class="cpt_to_cart_options_popup_field ',
																						'style="display:none" class="cpt_to_cart_options_popup_field ',
																						trx_addons_cpt_to_cart_get_settings_group( $type->name )
																		) ) . '"'
													. ' />';
											// Close popup
											echo '</div>';
											// Screen shadow
											echo '<div class="cpt_to_cart_options_screen_shadow"></div>';
											break;
									}
									echo '</td>';
								}
								echo '</tr>';
							}
						}
						?>
					</tbody>
				</table>
			</td>
		</tr>
		<?php
	}
}

if ( ! function_exists( 'trx_addons_cpt_to_cart_get_settings_group' ) ) {
	/**
	 * Return a layout of a single group of fields for the CPT options popup.
	 * 
	 * @param string $post_type  A post type name for the group.
	 * @param int $idx           Optional. An index of the group. Default is 0.
	 * @param array $settings    Optional. An array with current settings of fields of the group.
	 * 
	 * @return string  A HTML layout of the group.
	 */
	function trx_addons_cpt_to_cart_get_settings_group( $post_type, $idx = -1, $settings = [] ) {
		// Default values
		$settings = array_merge( trx_addons_cpt_to_cart_get_default_settings_group(), $settings );
		// Start group block
		$group = '<div class="cpt_to_cart_options_popup_field cpt_to_cart_options_popup_field_type_group">';
		// Event type
		$group .= '<div class="cpt_to_cart_options_popup_group_item cpt_to_cart_options_popup_group_type_select cpt_to_cart_options_popup_group_field_event_type">'
					. '<select size="1" name="' . sprintf( "trx_addons_options_cpt_to_cart[%s][events][%d][event_type]", $post_type, $idx ) . '">'
						. '<option value="action"' . ( $settings['event_type'] == 'action' ? ' selected="selected"' : '' ) . '>'
							. esc_html__( 'Action', 'trx_addons' )
						. '</option>'
						. '<option value="filter"' . ( $settings['event_type'] == 'filter' ? ' selected="selected"' : '' ) . '>'
							. esc_html__( 'Filter', 'trx_addons' )
						. '</option>'
					. '</select>'
				. '</div>';
		// Event name
		$group .= '<div class="cpt_to_cart_options_popup_group_item cpt_to_cart_options_popup_group_type_text cpt_to_cart_options_popup_group_field_event_name">'
					. '<input type="text"'
						. ' name="' . sprintf( "trx_addons_options_cpt_to_cart[%s][events][%d][event_name]", $post_type, $idx ) . '"'
						. ' value="' . ( ! empty( $settings['event_name'] ) ? $settings['event_name'] : '' ) . '"'
					. ' />'
				. '</div>';
		// Link type
		$group .= '<div class="cpt_to_cart_options_popup_group_item cpt_to_cart_options_popup_group_type_select cpt_to_cart_options_popup_group_field_link_type">'
					. '<select size="1" name="' . sprintf( "trx_addons_options_cpt_to_cart[%s][events][%d][link_type]", $post_type, $idx ) . '">'
						. '<option value="link"' . ( $settings['link_type'] == 'link' ? ' selected="selected"' : '' ) . '>'
							. esc_html__( 'Link', 'trx_addons' )
						. '</option>'
						. '<option value="button"' . ( $settings['link_type'] == 'button' ? ' selected="selected"' : '' ) . '>'
							. esc_html__( 'Button', 'trx_addons' )
						. '</option>'
						. '<option value="url"' . ( $settings['link_type'] == 'url' ? ' selected="selected"' : '' ) . '>'
							. esc_html__( 'URL', 'trx_addons' )
						. '</option>'
					.'</select>'
				. '</div>';
		// Area
		$group .= '<div class="cpt_to_cart_options_popup_group_item cpt_to_cart_options_popup_group_type_select cpt_to_cart_options_popup_group_field_area">'
					. '<select size="1" name="' . sprintf( "trx_addons_options_cpt_to_cart[%s][events][%d][area]", $post_type, $idx ) . '">'
						. '<option value="any"' . ( $settings['area'] == 'any' ? ' selected="selected"' : '' ) . '>'
							. esc_html__( 'Any', 'trx_addons' )
						. '</option>'
						. '<option value="archive"' . ( $settings['area'] == 'archive' ? ' selected="selected"' : '' ) . '>'
							. esc_html__( 'Posts archive', 'trx_addons' )
						. '</option>'
						. '<option value="single"' . ( $settings['area'] == 'single' ? ' selected="selected"' : '' ) . '>'
							. esc_html__( 'Single post', 'trx_addons' )
						. '</option>'
					.'</select>'
				. '</div>';
		// Place to
		$group .= '<div class="cpt_to_cart_options_popup_group_item cpt_to_cart_options_popup_group_type_select cpt_to_cart_options_popup_group_field_place">'
					. '<select size="1" name="' . sprintf( "trx_addons_options_cpt_to_cart[%s][events][%d][place]", $post_type, $idx ) . '"'
						. ( $settings['event_type'] != 'filter' ? ' disabled="disabled"' : '' )
					. '>'
						. '<option value="prepend"' . ( $settings['place'] == 'prepend' ? ' selected="selected"' : '' ) . '>'
							. esc_html__( 'Prepend', 'trx_addons' )
						. '</option>'
						. '<option value="append"' . ( $settings['place'] == 'append' ? ' selected="selected"' : '' ) . '>'
							. esc_html__( 'Append', 'trx_addons' )
						. '</option>'
						. '<option value="replace"' . ( $settings['place'] == 'replace' ? ' selected="selected"' : '' ) . '>'
							. esc_html__( 'Replace', 'trx_addons' )
						. '</option>'
					.'</select>'
				. '</div>';
		// Button 'Remove listener'
		$group .= '<div class="cpt_to_cart_options_popup_group_item cpt_to_cart_options_popup_group_type_button cpt_to_cart_options_popup_group_field_remove_listener">'
					. '<a href="javascript:void(0)" class="cpt_to_cart_options_popup_remove_listener"></a>'
				. '</div>';
		// End group block
		$group .= '</div>';

		return $group;
	}
}

if ( ! function_exists( 'trx_addons_cpt_to_cart_save_options' ) ) {
	add_action( 'woocommerce_update_options_products_cpt_to_cart', 'trx_addons_cpt_to_cart_save_options' );
	/**
	 * Save settings from the section WooCommerce - Settings - Products - Custom post types.
	 * 
	 * Hook: add_action( 'woocommerce_update_options_products_cpt_to_cart', 'trx_addons_cpt_to_cart_save_options' );
	 */
	function trx_addons_cpt_to_cart_save_options() {
		if ( ! empty( $_POST['trx_addons_options_cpt_to_cart'] ) && is_array( $_POST['trx_addons_options_cpt_to_cart'] ) ) {
			$options = array();
			foreach( trx_addons_stripslashes( $_POST['trx_addons_options_cpt_to_cart'] ) as $post_type => $settings ) {
				$options[ $post_type ] = array(
					'allow'      => ! empty( $settings['allow'] ) && (int)$settings['allow'] > 0,
					'price_new'  => ! empty( $settings['price_new'] ) && (int)$settings['price_new'] > 0,
					'price_name' => isset( $settings['price_name'] ) ? trim( $settings['price_name'] ) : ''
				);
				$options[ $post_type ]['events'] = array();
				if ( ! empty( $settings['events'] ) && is_array( $settings['events'] ) ) {
					foreach ( $settings['events'] as $v ) {
						if ( ! empty( ! empty( $v['event_name'] ) ) ) {
							$options[ $post_type ][ 'events' ][] = array_merge( trx_addons_cpt_to_cart_get_default_settings_group(), $v );
						}
					}
				}
			}
			update_option( 'trx_addons_options_cpt_to_cart', apply_filters( 'trx_addons_filter_cpt_to_cart_save_options', $options ) );
		}
	}
}

if ( ! function_exists( 'trx_addons_cpt_to_cart_get_default_settings_group' ) ) {
	/**
	 * Return an array with default settings for the each event row in the Options popup.
	 * 
	 * @return array  An array with default settings.
	 */
	function trx_addons_cpt_to_cart_get_default_settings_group() {
		return array(
			'event_type' => 'action',
			'event_name' => '',
			'link_type'  => 'link',
			'area'       => 'any',
			'place'      => 'prepend'
		);
	}
}

if ( ! function_exists( 'trx_addons_cpt_to_cart_load_options' ) ) {
	/**
	 * Return an array with custom post type options.
	 */
	function trx_addons_cpt_to_cart_load_options() {
		return apply_filters( 'trx_addons_filter_cpt_to_cart_load_options', get_option( 'trx_addons_options_cpt_to_cart' ) );
	}
}


// Load required styles and scripts for the admin mode
//-------------------------------------------------------------------
if ( ! function_exists( 'trx_addons_cpt_to_cart_load_scripts_admin' ) ) {
	add_action( 'admin_enqueue_scripts', 'trx_addons_cpt_to_cart_load_scripts_admin' );
	/**
	 * Load required styles and scripts for the CPT to Cart options page.
	 * 
	 * Hook: add_action("admin_enqueue_scripts", 'trx_addons_cpt_to_cart_load_scripts_admin');
	 */
	function trx_addons_cpt_to_cart_load_scripts_admin() {
		static $loaded = false;
		if ( $loaded ) return;
		$loaded = true;
		if ( trx_addons_check_url( 'admin.php' ) && trx_addons_get_value_gp( 'page' ) == 'wc-settings' && trx_addons_get_value_gp( 'section' ) == 'cpt_to_cart' ) {
			wp_enqueue_style(  'trx_addons-cpt-to-cart-admin', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'cpt-to-cart/cpt-to-cart.admin.css' ), array(), null );
			wp_enqueue_script( 'trx_addons-cpt-to-cart-admin', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'cpt-to-cart/cpt-to-cart.admin.js'), array('jquery'), null, true );
		}
	}
}
