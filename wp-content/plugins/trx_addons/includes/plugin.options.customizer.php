<?php
/**
 * Plugin's options customizer
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


if ( ! function_exists( 'trx_addons_add_theme_panel_pages' ) ) {
	add_filter( 'trx_addons_filter_add_theme_panel_pages', 'trx_addons_add_theme_panel_pages' );
	/**
	 * Add ThemeREX Addons options page to the Theme Panel
	 * 
	 * @hooked trx_addons_filter_add_theme_panel_pages
	 * 
	 * @param array $list List of menu pages
	 * 
	 * @return array List of pages
	 */
	function trx_addons_add_theme_panel_pages($list) {
		$list[] = array(
			esc_html__( 'ThemeREX Addons', 'trx_addons' ),
			esc_html__( 'ThemeREX Addons', 'trx_addons' ),
			'manage_options',
			'trx_addons_options',
			'trx_addons_options_page_builder'
		);
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_options_page_load_scripts' ) ) {
	add_action( "trx_addons_action_load_scripts_admin", 'trx_addons_options_page_load_scripts' );
	/**
	 * Load scripts for ThemeREX Addons options page
	 * 
	 * @hooked trx_addons_action_load_scripts_admin
	 * 
	 * @trigger trx_addons_filter_need_options
	 * 
	 * @param bool $all Load all scripts. Default is false. Not used in this function
	 */
	function trx_addons_options_page_load_scripts( $all = false ) {
		if ( apply_filters('trx_addons_filter_need_options', isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'trx_addons_options' ) ) {
			// WP styles & scripts
			wp_enqueue_style( 'wp-color-picker', false, array(), null );
			wp_enqueue_script( 'wp-color-picker', false, array('jquery'), null, true );
			wp_enqueue_script( 'jquery-ui-tabs', false, array('jquery', 'jquery-ui-core'), null, true );
			wp_enqueue_script( 'jquery-ui-accordion', false, array('jquery', 'jquery-ui-core'), null, true );
			wp_enqueue_script( 'jquery-ui-sortable', false, array('jquery', 'jquery-ui-core'), null, true );
			wp_enqueue_script( 'jquery-ui-datepicker', false, array('jquery', 'jquery-ui-core'), null, true );
			wp_enqueue_script( 'jquery-ui-slider', false, array( 'jquery', 'jquery-ui-core' ), null, true );
			// Font with icons must be loaded before main stylesheet
			wp_enqueue_style( 'trx_addons-icons', trx_addons_get_file_url('css/font-icons/css/trx_addons_icons.css'), array(), null );
			// jQuery UI skin 'Fresh'
			wp_enqueue_style( 'jquery-ui-fresh', trx_addons_get_file_url('css/jquery-ui-fresh.min.css'), array(), null );
			// Internal styles & scripts
			wp_enqueue_style( 'trx_addons-options', trx_addons_get_file_url('css/trx_addons.options.css'), array(), null );
			wp_enqueue_script( 'trx_addons-options', trx_addons_get_file_url('js/trx_addons.options.js'), array('jquery'), null, true );
			wp_enqueue_script( 'trx_addons-options-map', trx_addons_get_file_url('js/trx_addons.options.map.js'), array('jquery'), null, true );
			wp_enqueue_script( 'trx_addons-options-maskedinput', trx_addons_get_file_url('js/maskedinput/jquery.maskedinput.min.js'), array('jquery'), null, true );
			// Localize scripts
			if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'trx_addons_options' ) {
				wp_localize_script( 'trx_addons-options', 'TRX_ADDONS_DEPENDENCIES', trx_addons_get_options_dependencies() );
				wp_localize_script( 'trx_addons-options', 'TRX_ADDONS_SOCIAL_SHARE', trx_addons_get_share_url() );
			} else {
				$screen = function_exists('get_current_screen') ? get_current_screen() : false;
				if ( is_object( $screen ) && trx_addons_meta_box_is_registered( $screen->post_type ) ) {
					wp_localize_script( 'trx_addons-options', 'TRX_ADDONS_DEPENDENCIES', 
								trx_addons_get_options_dependencies( trx_addons_meta_box_get( $screen->post_type ) ) );
				}
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_options_page_load_scripts_rtl' ) ) {
	add_action( "trx_addons_action_load_scripts_admin", 'trx_addons_options_page_load_scripts_rtl', 100 );
	/**
	 * Load RTL-styles for ThemeREX Addons options page
	 *
	 * @hooked trx_addons_action_load_scripts_admin
	 *
	 * @trigger trx_addons_filter_need_options
	 *
	 * @param bool $all Load all scripts. Default is false. Not used in this function
	 */
	function trx_addons_options_page_load_scripts_rtl( $all = false ) {
		if ( apply_filters( 'trx_addons_filter_need_options', isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'trx_addons_options' ) ) {
			if ( is_rtl() ) {
				wp_enqueue_style( 'jquery-ui-fresh-rtl', trx_addons_get_file_url('css/jquery-ui-fresh-rtl.min.css'), array(), null );
				wp_enqueue_style( 'trx_addons-options-rtl', trx_addons_get_file_url('css/trx_addons.options-rtl.css'), array(), null );
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_options_page_builder' ) ) {
	/**
	 * Show ThemeREX Addons options page
	 */
	function trx_addons_options_page_builder() {
		?>
		<span class="wp-header-end" style="display:none"></span>
		<div class="trx_addons_options">
			<div class="trx_addons_options_header">
				<h2 class="trx_addons_options_title"><?php esc_html_e('ThemeREX Addons Settings', 'trx_addons'); ?></h2>
				<div class="trx_addons_options_buttons">
					<a href="#" class="trx_addons_options_button_submit trx_addons_button trx_addons_button_accent" tabindex="0"><?php esc_html_e( 'Save Options', 'trx_addons' ); ?></a>
					<a href="#" class="trx_addons_options_button_export trx_addons_button" tabindex="0"><?php esc_html_e( 'Export Options', 'trx_addons' ); ?></a>
					<a href="#" class="trx_addons_options_button_import trx_addons_button" tabindex="0"><?php esc_html_e( 'Import Options', 'trx_addons' ); ?></a>
				</div>
			</div>
			<?php
			$result = trx_addons_get_admin_message();
			if ( ! empty( $result['error'] ) || ! empty( $result['success'] ) ) {
				?>
				<div class="trx_addons_options_result">
					<?php
					if ( ! empty( $result['error'] ) ) {
						?><div class="error"><p><?php echo wp_kses_data($result['error'] ); ?></p></div><?php
					} else if ( ! empty( $result['success'] ) ) {
						?><div class="updated"><p><?php echo wp_kses_data($result['success'] ); ?></p></div><?php
					}
					?>
				</div>
			<?php
			}
			?>
			<form id="trx_addons_options_form" action="#" method="post" enctype="multipart/form-data">
				<input type="hidden" name="trx_addons_nonce" value="<?php echo esc_attr(wp_create_nonce(admin_url())); ?>" />
				<?php trx_addons_options_show_fields(); ?>
			</form>
		</div>
		<?php		
	}
}

if ( ! function_exists( 'trx_addons_options_show_fields' ) ) {
	/**
	 * Show fields in the ThemeREX Addons options
	 * 
	 * @trigger trx_addons_filter_before_show_options
	 *
	 * @param array $options     Options list
	 * @param string $post_type  Current post type
	 */
	function trx_addons_options_show_fields( $options = false, $post_type = false ) {
		global $TRX_ADDONS_STORAGE;
		if ( empty( $options ) ) {
			$options = $TRX_ADDONS_STORAGE['options'];
		}
		// Call filter to fill options-dependent arrays
		$options = apply_filters( 'trx_addons_filter_before_show_options', $options, $post_type );
		$tabs_titles = $tabs_content = $tabs_empty = array();
		$last_section = 'default';
		$last_panel = '';
		foreach ( $options as $k => $v ) {
			if ( $v['type'] == 'section' ) {
				if ( ! isset( $tabs_titles[ $k ] ) ) {
					$tabs_titles[ $k ] = $v;
					$tabs_content[ $k ] = '';
					$tabs_empty[ $k ] = true;
				}
				if ( ! empty( $last_panel ) ) {
					$tabs_content[ $last_section ] .= '</div></div>';
					$last_panel = '';
				}
				$last_section = $k;
			} else if ( $v['type'] == 'panel' ) {
				if ( empty( $last_panel ) ) {
					$tabs_content[ $last_section ] = ( ! isset( $tabs_content[ $last_section ] ) ? '' : $tabs_content[ $last_section ] ) 
													. '<div class="trx_addons_panels">';
				} else {
					$tabs_content[ $last_section ] .= '</div>';
				}
				$tabs_content[ $last_section ] .= '<h4 class="trx_addons_panel_title">' . esc_html( $v['title'] ) . '</h4>'
												. '<div class="trx_addons_panel_content">';
				$last_panel = $k;
			} else if ( $v['type'] == 'panel_end' ) {
				if ( ! empty( $last_panel ) ) {
					$tabs_content[ $last_section ] .= '</div></div>';
					$last_panel = '';
				}
			} else if ( $v['type'] == 'group' ) {
				$tabs_empty[ $last_section ] = false;
				if ( count( $v['fields'] ) > 0 ) {
					$tabs_content[ $last_section ] = ( ! isset( $tabs_content[ $last_section ] ) ? '' : $tabs_content[ $last_section ] )
													. '<div class="trx_addons_options_group"'
														. ( isset( $v['dependency'] ) ? ' data-param="' . esc_attr( $k ) . '" data-type="group"' : '' )
													. '>'
														. '<h4 class="trx_addons_options_group_title'
															. ( ! empty( $v['title_class'] ) ? ' ' . esc_attr( $v['title_class'] ) : '' )
															. '">' . esc_html( $v['title'] ) . '</h4>'
														. ( !empty($v['override']['desc'] ) || !empty($v['desc'] )
															? ( '<div class="trx_addons_options_group_description">'
																. ( ! empty( $v['override']['desc'] ) 	// param 'desc' already processed with wp_kses()!
																	? trim( $v['override']['desc'] ) 
																	: ( ! empty( $v['desc'] ) ? trim( $v['desc'] ) : '' )
																	)
																. '</div>'
																)
															: ''
															);
					if ( ! isset( $v['val'] ) || ! is_array( $v['val'] ) || count( $v['val'] ) == 0 ) {
						if ( isset( $v['std'] ) && is_array( $v['std'] ) && count( $v['std'] ) > 0 ) {
							$v['val'] = $v['std'];
						} else {
							$v['val'] = array( array() );
						}
					}
					foreach ( $v['val'] as $idx => $values ) {
						$tabs_content[ $last_section ] .= '<div class="trx_addons_options_fields_set' 
															. ( ! empty( $v['clone'] ) ? ' trx_addons_options_clone' : '' )
															. '">'
							. ( ! empty( $v['clone'] ) 
									? '<span class="trx_addons_options_clone_control trx_addons_options_clone_control_move" data-tooltip-text="' . esc_attr__('Drag to reorder', 'trx_addons') . '">'
											. '<span class="trx_addons_icon-menu"></span>'
										. '</span>'
									: ''
								);
						foreach ( $v['fields'] as $k1 => $v1 ) {
							$v1['val'] = isset( $values[ $k1 ] ) ? $values[ $k1 ] : $v1['std'];
							$tabs_content[ $last_section ] .= trx_addons_options_show_field( $k1, $v1, "{$k}[{$idx}]" );
						}
						$tabs_content[ $last_section ] .= ! empty( $v['clone'] )
									? '<span class="trx_addons_options_clone_control trx_addons_options_clone_control_add" tabindex="0" data-tooltip-text="' . esc_attr__('Clone items', 'trx_addons') . '">'
											. '<span class="trx_addons_icon-copy"></span>'
										. '</span>'
										. '<span class="trx_addons_options_clone_control trx_addons_options_clone_control_delete" tabindex="0" data-tooltip-text="' . esc_attr__('Delete items', 'trx_addons') . '">'
											. '<span class="trx_addons_icon-cancel-2"></span>'
										. '</span>'
									: ''
								;
						$tabs_content[ $last_section ] .= '</div>';
					}
					if ( ! empty( $v['clone'] ) ) {
						$tabs_content[ $last_section ] .= '<div class="trx_addons_options_clone_buttons">'
															. '<a class="trx_addons_button trx_addons_button_accent trx_addons_options_clone_button_add" tabindex="0">'
																. esc_html__('+ Add New Item', 'trx_addons')
															. '</a>'
														. '</div>';
					}
					$tabs_content[ $last_section ] .= '</div>';
				}
			} else {
				if ( empty( $v['hidden'] ) && $v['type'] != 'hidden' ) {
					$tabs_empty[ $last_section ] = false;
				}
				$tabs_content[ $last_section ] = ( ! isset( $tabs_content[ $last_section ] ) ? '' : $tabs_content[ $last_section ] ) 
												. trx_addons_options_show_field( $k, $v );
			}
		}
		if ( ! empty( $last_panel ) ) {
			$tabs_content[ $last_section ] .= '</div></div>';
		}
		
		if ( count( $tabs_content ) > 0 ) {
			?>
			<div class="trx_addons_tabs trx_addons_tabs_<?php echo esc_attr( trx_addons_get_setting( 'options_tabs_position' ) ); ?> <?php echo count( $tabs_titles ) > 1 ? 'with_tabs' : 'no_tabs'; ?>">
				<?php if ( count( $tabs_titles ) > 1 ) { ?>
					<ul><?php
						foreach ( $tabs_titles as $k => $v ) {
							if ( empty( $tabs_content[ $k ] ) || $tabs_empty[ $k ] ) {
								continue;
							}
							?><li class="trx_addons_tabs_title trx_addons_tabs_title_<?php
								echo esc_attr( $v['type'] );
								if ( $tabs_empty[ $k ] ) {
									echo ' trx_addons_options_item_hidden';
								}
							?>"><a href="#trx_addons_tabs_section_<?php echo esc_attr( $k ); ?>"><?php
							if ( ! empty( $v['icon'] ) ) echo '<i class="' . esc_attr( $v['icon'] ) . '"></i>';
							?><span class="trx_addons_tabs_caption"><?php echo esc_html( $v['title'] ); ?></span></a></li><?php
						}
					?></ul>
				<?php
				}
				foreach ( $tabs_content as $k => $v ) {
					if ( empty( $v ) || $tabs_empty[ $k ] ) {
						continue;
					}
					?>
					<div id="trx_addons_tabs_section_<?php echo esc_attr( $k ); ?>" class="trx_addons_tabs_section trx_addons_options_section">
						<?php trx_addons_show_layout( $v ); ?>
					</div>
					<?php
				}
				?>
			</div>
			<?php
		}
	}
}

if ( ! function_exists( 'trx_addons_options_show_field' ) ) {
	/**
	 * Display a single options's field
	 *
	 * @param string $name   Field name.
	 * @param array  $field  Field params.
	 * @param string $group  Group name.
	 */
	function trx_addons_options_show_field( $name, $field, $group = '' ) {
		static $last_post_type = '';

		// Prepare 'name' for the group fields
		if ( ! empty( $group ) ) {
			$name = $group . "[{$name}]";
		}
		$id = str_replace( array( '[', ']' ), array( '_', '' ), $name );

		$output = ( ! empty( $field['class'] ) && strpos( $field['class'], 'trx_addons_new_row' ) !== false 
					? '<div class="trx_addons_new_row_before"></div>'
					: '' )
				. '<div class="trx_addons_options_item'
						. ' trx_addons_options_item_' . esc_attr( $field['type'] )
						. ( ! empty( $field['hidden'] ) && $field['type'] != 'hidden' ? ' trx_addons_options_item_hidden' : '' )
						. ( ! empty( $field['class'] ) ? ' ' . esc_attr( $field['class'] ) : '' )
						. '">'
							. '<h4 class="trx_addons_options_item_title'
								. ( ! empty( $field['title_class'] ) ? ' ' . esc_attr( $field['title_class'] ) : '' )
								. '">' . esc_html( $field['title'] ) . '</h4>'
							. '<div class="trx_addons_options_item_data">'
								. '<div class="trx_addons_options_item_field' 
									. ( ! empty( $field['dir'] ) ? ' trx_addons_options_item_field_' . esc_attr( $field['dir'] ) : '' )
									. '"'
									. ' data-param="' . esc_attr( $name ) . '"'
									. ' data-type="' . esc_attr( $field['type'] ) . '"'
									. '>';

		// Type 'hidden''' )
		if ( $field['type'] == 'hidden' ) {
			if ( isset( $field['std'] ) ) {
				$output .= '<input type="hidden"' 
									. ( ! empty( $field['class_field'] ) ? ' class="' . esc_attr( $field['class_field'] ) . '"' : '' ) 
									. ' name="trx_addons_options_field_' . esc_attr( $name ) . '"'
									. ' value="' . esc_attr( isset( $field['val'] ) ? $field['val'] : '' ) . '"'
									. ' data-std="' . esc_attr( isset( $field['std'] ) ? $field['std'] : '' ) . '"'
									. ' />';
			}

		// Type 'checkbox'
		} else if ( $field['type'] == 'checkbox' ) {
			$output .= '<label class="trx_addons_options_item_label">'
						. '<input type="checkbox"'
								. ( ! empty( $field['class_field'] ) ? ' class="' . esc_attr( $field['class_field'] ) . '"' : '' )
								. ' name="trx_addons_options_field_' . esc_attr( $name ) . '"'
								. ' value="1"'
								. ' data-std="' . esc_attr( $field['std'] ) . '"'
								. ( ! empty( $field['val'] ) ? ' checked="checked"' : '' )
								. ( ! empty( $field['readonly'] ) ? ' readonly="readonly"' : '' )
								. ' />'
						. '<span class="trx_addons_options_item_caption">'
							. esc_html( $field['title'] )
						. '</span>'
					. '</label>';

		// Type 'switch'
		} else if ( $field['type'] == 'switch' ) {
			$output .= '<label class="trx_addons_options_item_label">'
						. '<input type="checkbox"'
								. ( ! empty( $field['class_field'] ) ? ' class="' . esc_attr( $field['class_field'] ) . '"' : '' ) 
								. ' name="trx_addons_options_field_' . esc_attr( $name ) . '"'
								. ' value="1"'
								. ' data-std="' . esc_attr( $field['std'] ) . '"'
								. ( ! empty( $field['val'] ) ? ' checked="checked"' : '' )
								. ( ! empty( $field['readonly'] ) ? ' readonly="readonly"' : '' )
								. ' />'
						. '<span class="trx_addons_options_item_holder" tabindex="0">'
							. '<span class="trx_addons_options_item_holder_back"></span>'
							. '<span class="trx_addons_options_item_holder_handle"></span>'
						. '</span>'
						. ( ! empty( $field['title'] )
							? '<span class="trx_addons_options_item_caption">' . esc_html( $field['title'] ) . '</span>'
							: ''
							)
					. '</label>';

		// Type 'radio' (many items)
		} else if ( $field['type'] == 'radio' ) {
			$field['options'] = apply_filters( 'trx_addons_filter_options_get_list_choises', $field['options'], $name );
			foreach ( $field['options'] as $k => $v ) {
				$output .= '<label class="trx_addons_options_item_label">'
								. '<input type="radio"'
										. ( ! empty( $field['class_field'] ) ? ' class="' . esc_attr( $field['class_field'] ) . '"' : '' ) 
										. ' name="trx_addons_options_field_' . esc_attr( $name ) . '"'
										. ' value="' . esc_attr( $k ) . '"'
										. ' data-std="' . esc_attr( $field['std'] ) . '"'
										. ( $field['val'] == $k ? ' checked="checked"' : '' )
										. ( ! empty( $field['readonly'] ) ? ' readonly="readonly"' : '' )
										. '>'
								. '<span class="trx_addons_options_item_holder" tabindex="0"></span>'
								. '<span class="trx_addons_options_item_caption">'
									. esc_html( $v)
								. '</span>'
							. '</label>';
			}

		// Type 'checklist'
		} else if ( $field['type'] == 'checklist' ) {
			$field['options'] = apply_filters( 'trx_addons_filter_options_get_list_choises', $field['options'], $name );
			$output .= '<div class="trx_addons_options_item_choises' . ( ! empty( $field['sortable'] ) ? ' trx_addons_options_sortable' : '' ) . '">';
			// Convert string value to the array
			if ( ! empty( $field['val'] ) && ! is_array( $field['val'] ) ) {
				parse_str( str_replace( '|', '&', $field['val'] ), $field['val'] );
			}
			// Remove not exists values (if a key of value is not present in the 'options')
			if ( is_array( $field['val'] ) ) {
				foreach( array_keys( $field['val'] ) as $k ) {
					if ( ! isset( $field['options'][ $k ] ) ) {
						unset( $field['val'][ $k ] );
					}
				}
			}
			// Sortable
			if ( ! empty( $field['sortable'] ) ) {
				// Sort options by values order
				if ( is_array( $field['val'] ) ) {
					$field['options'] = trx_addons_array_merge( $field['val'], $field['options'] );
				}
				if ( ! empty( $field['group'] ) ) {
					$field['group'] = false;
				}
			}
			if ( ! empty( $field['group'] ) ) {
				$last_group = '';
			}
			foreach ( $field['options'] as $k => $v ) {
				if ( ! empty( $field['group'] ) ) {
					if ( preg_match( '/\\(([^\\)]*)\\)/', $v, $matches ) ) {
						$cur_group = $matches[1];
						$v = trim( str_replace( '(' . $cur_group . ')', '', $v ) );
						if ( $cur_group != $last_group ) {
							$last_group = $cur_group;
							$output .= '<p class="trx_addons_options_item_choises_group">' . esc_html( $last_group ) . '</p>';
						}
					}
				}
				$output .= '<label class="trx_addons_options_item_label' . ( ! empty( $field['sortable'] ) ? ' trx_addons_options_item_sortable' : '' ) . '"'
								. ( 'horizontal' == $field['dir'] && substr( $v, 0, 4 ) != 'http' && strlen( $v ) >= 20 ? ' title="' . esc_attr( $v ) . '"' : '' )
								. '>'
							. '<input type="checkbox"'
								. ( ! empty( $field['class_field'] ) ? ' class="' . esc_attr( $field['class_field'] ) . '"' : '' ) 
								. ' name="trx_addons_options_field_' . esc_attr( $name) . '[' . $k . ']"'
								. ' value="1"'
								. ' data-name="' . $k . '"'
								. ( isset($field['val'][ $k ]) && (int)$field['val'][ $k ] == 1 ? ' checked="checked"' : '' )
								. ' />'
							. ( substr( $v, 0, 4 ) == 'http' ? '<img src="' . esc_url( $v ) . '">' : esc_html( $v ) )
						. '</label>';
			}
			$output .= '<input type="hidden" name="trx_addons_options_field_' . esc_attr( $name ) . '"'
							. ' value="' . trx_addons_options_put_field_value( $field ) . '"'
							. ' data-std="' . trx_addons_options_put_field_value( $field, 'std' ) . '"'
							. ' />'
					. '</div>';

		// Type 'button' - call specified js function
		} else if ( $field['type'] == 'button' ) {
			$output .= '<a href="#"'
							. ' class="trx_addons_button'
								. ( ! empty( $field['class_field'] ) ? ' ' . esc_attr( $field['class_field'] ) : '' )
								. ( ! empty( $field['icon'] ) ? ' ' . esc_attr( $field['icon'] ) : '' )
								. '"'
							. ' name="trx_addons_options_field_' . esc_attr( $name ) . '"'
							. ' data-action="' . esc_attr( ! empty( $field['action'] ) ? $field['action'] : $field['std'] ) . '"'
							. ( ! empty( $field['callback'] ) ? ' data-callback="' . esc_attr( $field['callback'] ) . '"' : '' )
						. '>'
							. esc_html( ! empty( $field['caption'] ) ? $field['caption'] : $field['title'] )
						. '</a>';


		// Type 'date'
		} else if ( $field['type'] == 'date') {
			$output .= '<input type="text"'
						. ( ! empty( $field['class_field'] ) ? ' class="' . esc_attr( $field['class_field'] ) . '"' : '' ) 
						. ' name="trx_addons_options_field_' . esc_attr( $name ) . '"'
						. ' value="' . esc_attr( $field['val'] ) . '"'
						. ' data-std="' . esc_attr( $field['std'] ) . '"'
						. ' data-format="' . esc_attr( ! empty( $field['format'] ) ? $field['format'] : 'yy-mm-dd') . '"'
						. ' data-months="' . esc_attr( ! empty( $field['months'] ) ? $field['months'] : 1 ) . '"'
						. ( ! empty( $field['readonly'] ) ? ' readonly="readonly"' : '' )
						. ( ! empty( $field['mask'] ) ? ' data-mask="' . esc_attr( $field['mask'] ) . '"' : '' )
						. ( ! empty( $field['placeholder'] ) ? ' placeholder="' . esc_attr( $field['placeholder'] ) . '"' : '' )
						. ' />';

		// Types 'text', 'time', 'phone', 'email'
		} else if ( in_array( $field['type'], array( 'text', 'time', 'phone', 'email' ) ) ) {
			$output .= '<input type="text"'
						. ( ! empty( $field['class_field'] ) ? ' class="' . esc_attr( $field['class_field'] ) . '"' : '' ) 
						. ' name="trx_addons_options_field_' . esc_attr( $name ) . '"'
						. ' value="' . esc_attr( $field['val'] ) . '"'
						. ' data-std="' . esc_attr( $field['std'] ) . '"'
						. ( ! empty( $field['readonly'] ) ? ' readonly="readonly"' : '' )
						. ( ! empty( $field['placeholder'] ) ? ' placeholder="' . esc_attr( $field['placeholder'] ) . '"' : '' )
						. ' />';

		// Type 'textarea'
		} else if ( $field['type'] == 'textarea' ) {
			$output .= '<textarea'
						. ( ! empty( $field['class_field'] ) ? ' class="' . esc_attr( $field['class_field'] ) . '"' : '' ) 
						. ' name="trx_addons_options_field_' . esc_attr( $name ) . '"'
						. ( ! empty( $field['placeholder'] ) ? ' placeholder="' . esc_attr( $field['placeholder'] ) . '"' : '' )
						. ( ! empty( $field['readonly'] ) ? ' readonly="readonly"' : '' )
						. ' data-std="' . esc_attr( $field['std'] ) . '"'
						. '>'
							. esc_attr( $field['val'] )
						. '</textarea>';

		// Type 'text_editor'
		} elseif ( $field['type'] == 'text_editor' ) {
			$output .= '<input type="hidden" id="trx_addons_options_field_' . esc_attr( $id ) . '"'
							. ' name="trx_addons_options_field_' . esc_attr( $name ) . '"'
							. ' value="' . esc_textarea( $field['val'] ) . '"'
							. ' data-std="' . esc_attr( $field['std'] ) . '"'
							. ' />'
						. trx_addons_options_show_custom_field(
							'trx_addons_options_field_' . esc_attr( $id ) . '_tinymce',
							$field,
							$field['val']
						);
		
		// Type 'select', 'select2', 'post_type', 'taxonomy'
		} else if ( in_array( $field['type'], array( 'select', 'select2', 'post_type', 'taxonomy' ) ) ) {
			$field['options'] = apply_filters( 'trx_addons_filter_options_get_list_choises', $field['options'], $name );
			if ( $field['type'] == 'select2' ) {
				trx_addons_enqueue_select2();
				$field['class_field'] = ( ! empty( $field['class_field'] ) ? $field['class_field'] . ' ' : '' ) . 'select2_field';
			} else if ( $field['type'] == 'post_type' ) {
				if ( empty( $field['options'] ) ) {
					$field['options'] = trx_addons_get_list_posts_types();
				}
				$last_post_type = ! empty( $field['val'] ) ? $field['val'] : trx_addons_array_get_first( $field['options'] );
				$field['class_field'] = ( ! empty( $field['class_field'] ) ? $field['class_field'] . ' ' : '' ) . 'trx_addons_post_type_selector';
			} else if ( $field['type'] == 'taxonomy' && empty( $field['options'] ) ) {
				$field['options'] = empty( $last_post_type ) ? array() : trx_addons_get_list_taxonomies( false, $last_post_type );
				$field['class_field'] = ( ! empty( $field['class_field'] ) ? $field['class_field'] . ' ' : '' ) . 'trx_addons_taxonomy_selector';
			}
			$output .= '<select'
							. ( ! empty( $field['class_field'] ) ? ' class="' . esc_attr( $field['class_field'] ) . '"' : '' ) 
							. ' name="trx_addons_options_field_' . esc_attr( $name ) . ( ! empty( $field['multiple'] ) ? '[]' : '' ) . '"'
							. ( ! empty( $field['multiple'] ) ? ' multiple="multiple"' : ' size="1"' )
							. ( ! empty( $field['readonly'] ) ? ' readonly="readonly"' : '' )
							. ' data-std="' . trx_addons_options_put_field_value( $field, 'std' ) . '"'
							. '>';
			foreach ( $field['options'] as $k => $v ) {
				$output .= '<option value="' . esc_attr( $k ) . '"'
									. ( in_array( $k, (array)$field['val'] ) ? ' selected="selected"' : '' )
									. ( strpos( $k, 'icon-' ) !== false ? ' class="' . esc_attr( $k ) . '"' : '' )
							. '>'
								. esc_html( $v )
							. '</option>';
			}
			$output .= '</select>';

		// Type 'icon'
		} else if ( $field['type'] == 'icon' ) {
			$field['options'] = apply_filters( 'trx_addons_filter_options_get_list_choises', $field['options'], $name );
			$output .= '<select size="1"'
							. ( ! empty( $field['class_field'] ) ? ' class="' . esc_attr( $field['class_field'] ) . '"' : '' ) 
							. ' name="trx_addons_options_field_' . esc_attr( $name ) . '"'
							. ' data-std="' . esc_attr( $field['std'] ) . '"'
							. '>';
			$socials_type = ! empty($field['style'] ) ? $field['style'] : trx_addons_get_setting('socials_type');
			foreach ( $field['options'] as $k => $v ) {
				$sn = $socials_type == 'images' ? $k : $v;
				$output .= '<option class="' . esc_attr( $sn ) . '"'
							. ' value="' . esc_attr( $sn ) . '"'
							. ( $field['val'] == $sn ? ' selected="selected"' : '' )
							. '>'
							. esc_html( str_replace( array( 'trx_addons_icon-', 'icon-' ), '', $sn ) )
							. '</option>';
			}
			$output .= '</select>';

		// Type 'icons'
		//	'show_label' => false,
		//	'mode' => 'inline',		// inline | dropdown
		//	'return' => 'slug',		// slug | full
		//	'style' => 'images',	// icons | images | svg
		} else if ( $field['type'] == 'icons' ) {
			$field['options'] = apply_filters( 'trx_addons_filter_options_get_list_choises', $field['options'], $name );
			$output .= '<input type="hidden" name="trx_addons_options_field_' . esc_attr( $name ) . '"'
							. ' value="' . esc_attr( $field['val'] ) . '"'
							. ' data-std="' . esc_attr( $field['std'] ) . '"'
							. ' />'
						. trx_addons_options_show_custom_field( 'trx_addons_options_field_' . esc_attr( $id ), 
								$field,
								$field['val'] );

		// Type 'color'
		} else if ( $field['type'] == 'color' ) {
			$output .= '<input type="text"'
							. ' class="trx_addons_color_selector'
								. ( ! empty( $field['class_field'] ) ? ' ' . esc_attr( $field['class_field'] ) : '' ) 
								. '"'
							. ' name="trx_addons_options_field_' . esc_attr( $name ) . '"'
							. ' value="' . esc_attr( $field['val'] ) . '"'
							. ' data-std="' . esc_attr( $field['std'] ) . '"'
							. ( ! empty( $field['readonly'] ) ? ' readonly="readonly"' : '' )
							. ' />';

		// Type 'image', 'media', 'video' or 'audio'
		} else if ( in_array( $field['type'], array( 'image', 'media', 'video', 'audio' ) ) ) {
			$output .= '<input type="hidden" id="trx_addons_options_field_' . esc_attr( $id ) . '"'
								. ' name="trx_addons_options_field_' . esc_attr( $name ) . '"'
								. ' value="' . esc_attr( $field['val'] ) .'"'
								. ' data-std="' . esc_attr( $field['std'] ) . '"'
								. '>'
						. trx_addons_options_show_custom_field( 'trx_addons_options_field_' . esc_attr( $id ) . '_button', 
								array(
									'type' => 'mediamanager',
									'multiple' => ! empty( $field['multiple'] ),
									'data_type' => $field['type'],
									'button_caption' => ! empty( $field['button_caption'] ) ? $field['button_caption'] : '',
									'class_field' => ! empty( $field['class_field'] ) ? ' ' . esc_attr( $field['class_field'] ) : '',
									'linked_field_id' => 'trx_addons_options_field_' . esc_attr( $id )
									),
								$field['val'] );

		// Type 'map'
		} else if ( in_array( $field['type'], array( 'map', 'googlemap', 'osmap' ) ) ) {
			$output .= '<input type="hidden" id="trx_addons_options_field_' . esc_attr( $id ) . '"'
							. ' name="trx_addons_options_field_' . esc_attr( $name ) . '"'
							. ' value="' . esc_attr( $field['val'] ) . '"'
							. ' data-std="' . esc_attr( $field['std'] ) . '"'
							. ' />'
						. trx_addons_options_show_custom_field( 'trx_addons_options_field_' . esc_attr( $name ) . '_map', 
								array(
									'type' => $field['type'],
									'class_field' => ! empty( $field['class_field'] ) ? ' ' . esc_attr( $field['class_field'] ) : '',
									'height' => ( ! empty( $field['height'] ) ? $field['height'] : 300 ),
									'linked_field_id' => 'trx_addons_options_field_' . esc_attr( $id )
								),
								$field['val'] );

		// Type 'slider' || 'range'
		} else if ( in_array( $field['type'], array( 'slider', 'range' ) ) ) {
			$field['show_value'] = ! isset( $field['show_value'] ) || $field['show_value'];
			$output .= '<input type="' . ( ! $field['show_value'] ? 'hidden' : 'text' ) . '" id="trx_addons_options_field_' . esc_attr( $name ) . '"'
							. ' name="trx_addons_options_field_' . esc_attr( $name ) . '"'
							. ' value="' . esc_attr( $field['val'] ) . '"'
							. ' data-std="' . esc_attr( $field['std'] ) . '"'
							. ( $field['show_value'] ? ' class="trx_addons_range_slider_value"' : '' )
							. ' data-type="' . esc_attr( $field['type'] ) . '"'
							. ' />'
						. ( $field['show_value'] && ! empty( $field['units'] ) ? '<span class="trx_addons_range_slider_units">' . esc_html( $field['units'] ) . '</span>' : '' )
						. trx_addons_options_show_custom_field(	'trx_addons_options_field_' . esc_attr( $name ) . '_slider', $field, $field['val'] );
		}

		$output .=  		'</div>'
							. ( ! empty($field['override']['desc'] ) || ! empty( $field['desc'] )
								? ( '<div class="trx_addons_options_item_description">'
									. ( ! empty( $field['override']['desc'] ) 	// param 'desc' already processed with wp_kses()!
										? trim( $field['override']['desc'] ) 
										: ( ! empty( $field['desc'] ) ? trim( $field['desc'] ) : '' )
										)
									. '</div>'
									)
								: ''
								)
						. '</div>'
					. '</div>';
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_options_show_custom_field' ) ) {
	/**
	 * Return a layout of a custom field (with custom layout)
	 * such as 'map', 'slider', 'range', 'mediamanager', 'colorpicker', 'switch', 'icons', etc.
	 * 
	 * @param string $id  Field ID
	 * @param array  $field  Field parameters
	 * @param mixed  $value  Field value
	 * 
	 * @return string  HTML with field layout
	 */
	function trx_addons_options_show_custom_field( $id, $field, $value = null ) {
		$output = '';
		switch ( $field['type'] ) {

			case 'mediamanager':
				// Enqueue media is broke the popup 'Media' inside Gutenberg editor
				if ( ! trx_addons_is_preview( 'gutenberg' ) ) {
					wp_enqueue_media();
				}
				$title   = ! empty( $field['button_caption'] ) 
							? $field['button_caption']
							: ( empty( $field['data_type'] ) || $field['data_type'] == 'image'
								? ( ! empty( $field['multiple'] ) ? esc_html__( 'Add Images', 'trx_addons' ) : esc_html__( 'Choose Image', 'trx_addons' ) )
								: ( ! empty( $field['multiple'] ) ? esc_html__( 'Add Media', 'trx_addons' ) : esc_html__( 'Choose Media', 'trx_addons' ) )
								);
				$images  = explode( '|', $value );
				$output .= '<span class="trx_addons_media_selector_preview'
								. ' trx_addons_media_selector_preview_' . ( ! empty( $field['multiple'] ) ? 'multiple' : 'single' )
								. ( is_array( $images ) && count( $images ) > 0 ? ' trx_addons_media_selector_preview_with_image' : '' )
							. '">';
				if ( is_array( $images ) ) {
					foreach ( $images as $img ) {
						$output .= $img 
							? '<span class="trx_addons_media_selector_preview_image" tabindex="0">'
									. ( in_array( trx_addons_get_file_ext( $img ), array( 'gif', 'jpg', 'jpeg', 'png' ) )
											? '<img src="' . esc_url( $img ) . '" alt="' . esc_attr__( "Selected image", 'trx_addons' ) . '">'
											: '<a href="' . esc_attr( $img ) . '">' . esc_html( basename( $img ) ) . '</a>'
										)
								. '</span>' 
							: '';
					}
				}
				//$output .= '<span class="trx_addons_media_selector_image_placeholder" tabindex="0">' . esc_html( $title ) . '</span>';
				$output .= '</span>';
				$output .= '<input type="button"'
								. ' id="' . esc_attr( $id ) . '"'
								. ' class="button mediamanager trx_addons_media_selector'
									. ( ! empty( $field['class_field'] ) ? ' ' . esc_attr( $field['class_field'] ) : '' ) 
									. '"'
								. ' data-choose="' . esc_attr( $title ) . '"'
								. '	data-update="' . esc_attr( $title ) . '"'
								. ' data-multiple="' . esc_attr( ! empty( $field['multiple'] ) ? '1' : '0' ) . '"'
								. ' data-type="' . esc_attr( ! empty( $field['data_type'] ) ? $field['data_type'] : 'image') . '"'
								. ' data-linked-field="' . esc_attr( $field['linked_field_id'] ) . '"'
								. ' value="' . esc_attr( $title ) . '"'
							. '>';
				break;

			case 'map':
			case 'googlemap':
			case 'osmap':
				$map_type = '';
			    if ( $field['type'] == 'googlemap' ) {
					$map_type = 'googlemap';
					trx_addons_enqueue_googlemap();
				} else if ( $field['type'] == 'osmap' ) {
					$map_type = 'osmap';
					trx_addons_enqueue_osmap();
				} else if ( $field['type'] == 'map' ) {
					if ( trx_addons_check_option( 'api_google_load' ) && trx_addons_is_on( trx_addons_get_option( 'api_google_load' ) ) && trx_addons_get_option( 'api_google' ) != '' ) {
						$map_type = 'googlemap';
						trx_addons_enqueue_googlemap();
					} else if ( trx_addons_check_option( 'api_openstreet_load' ) && trx_addons_is_on( trx_addons_get_option( 'api_openstreet_load' ) ) ) {
						$map_type = 'osmap';
						trx_addons_enqueue_osmap();
					}
				}
				$output .= '<div id="' . esc_attr( $id ) . '"'
							. ' class="trx_addons_options_map sc_' . esc_attr( $map_type )
								. ( ! empty( $field['class_field'] ) ? ' ' . esc_attr( $field['class_field'] ) : '' ) 
								. '"'
							. ' data-coords="' . esc_attr( $value ) . '"'
							. ' data-editable="1"'
							. ' style="height:' . esc_attr( empty( $field['height'] )
															? '300px' 
															: trx_addons_prepare_css_value( $field['height'] )
														) . '"'
							. '>'
							. '</div>'
							. '<div class="trx_addons_options_map_search">'
								. '<input type="text" class="trx_addons_options_map_search_text" value="" />'
								. '<input type="button" class="trx_addons_options_map_search_button"'
										. ' value="' . esc_html__( 'Find by address', 'trx_addons' ) . '" />'
							. '</div>';
				break;
		
			case 'icons':
				if ( is_array( $field['options'] ) && count( $field['options'] ) > 0 ) {
					if ( empty( $field['style'] ) ) {
						$field['style'] = trx_addons_get_setting('socials_type');
					}
					if ( empty( $field['return'] ) ) {
						$field['return'] = 'full';
					}
					if ( empty( $field['mode'] ) ) {
						$field['mode'] = 'dropdown';
					}
					$output .= ( $field['mode'] == 'dropdown'
									? '<span class="trx_addons_icon_selector'
													. ( ! empty( $field['class_field'] ) ? ' ' . esc_attr( $field['class_field'] ) : '' ) 
													. ( $field['style'] == 'icons' && ! empty( $value ) ? ' ' . esc_attr( $value ) : '' )
													. '"'
											. ' tabindex="0"'
											. ' title="' . esc_attr__( 'Select icon', 'trx_addons' ) . '"'
											. ' data-style="' . esc_attr( $field['style'] ) . '"'
											. ( in_array( $field['style'], array( 'images', 'svg' ) ) && ! empty( $value ) 
													? ' style="background-image: url(' . esc_url( $field['return'] == 'slug' 
																									? $field['options'][ $value ] 
																									: $value ) . ');"' 
													: '' )
										. '></span>'
									: '' )
								. '<div class="trx_addons_list_icons trx_addons_list_icons_' . esc_attr( $field['mode'] ) . '">'
								. ( $field['mode'] == 'dropdown'
									? '<input type="text" class="trx_addons_list_icons_search" placeholder="' . esc_attr__('Search for an icon', 'trx_addons') . '">'
									: ''
									)
								. '<div class="trx_addons_list_icons_wrap">'
									. '<div class="trx_addons_list_icons_inner">';
					foreach ( $field['options'] as $slug => $icon ) {
						$output .= '<span tabindex="0" class="' . esc_attr( $field['style'] == 'icons' ? $icon : $slug )
												. ( ( $field['return'] == 'full' ? $icon : $slug ) == $value ? ' trx_addons_active' : '' )
											.'"'
											. ' title="' . esc_attr( $slug ) . '"'
											. ' data-icon="' . esc_attr( $field['return'] == 'full' ? $icon : $slug ) . '"'
											. ( ! empty( $icon) && in_array( $field['style'], array( 'images', 'svg' ) )
												? ' style="background-image: url(' . esc_url( $icon ) . ');"'
												: ''
												)
									. '>'
										. ( $field['mode'] != 'dropdown'
											? '<i>' . esc_html( $slug ) . '</i>'
											: ''
											)
									. '</span>';
					}
					$output .= '</div></div></div>';
				}
				break;

			case 'slider':
			case 'range':
				$is_range   = 'range' == $field['type'];
				$field_min  = ! empty( $field['min'] ) ? $field['min'] : 0;
				$field_max  = ! empty( $field['max'] ) ? $field['max'] : 100;
				$field_step = ! empty( $field['step'] ) ? $field['step'] : 1;
				$field_val  = ! empty( $value )
								? ( $value . ( $is_range && strpos( $value, ',' ) === false ? ',' . $field_max : '' ) )
								: ( $is_range ? $field_min . ',' . $field_max : $field_min );
				$output    .= '<div id="' . esc_attr( $id ) . '"'
								. ' class="trx_addons_range_slider"'
								. ' data-range="' . esc_attr( $is_range ? 'true' : 'min' ) . '"'
								. ' data-min="' . esc_attr( $field_min ) . '"'
								. ' data-max="' . esc_attr( $field_max ) . '"'
								. ' data-step="' . esc_attr( $field_step ) . '"'
								. '>'
								. '<span class="trx_addons_range_slider_label trx_addons_range_slider_label_min">'
									. esc_html( $field_min )
								. '</span>'
								. '<span class="trx_addons_range_slider_label trx_addons_range_slider_label_avg">'
									. esc_html( round( ( $field_max + $field_min ) / 2, $field_step == (int)$field_step ? 0 : 2 ) )
								. '</span>'
								. '<span class="trx_addons_range_slider_label trx_addons_range_slider_label_max">'
									. esc_html( $field_max )
								. '</span>';
				$output    .= '<div class="trx_addons_range_slider_scale">';
				for ( $i = 0; $i <= 11; $i++ ) {
					$output    .= '<span></span>';
				}
				$output    .= '</div>';
				$values     = explode( ',', $field_val );
				for ( $i = 0; $i < count( $values ); $i++ ) {
					$output .= '<span class="trx_addons_range_slider_label trx_addons_range_slider_label_cur">'
									. esc_html( $values[ $i ] )
								. '</span>';
				}
				$output .= '</div>';
				break;

			case 'text_editor':
				if ( function_exists( 'wp_enqueue_editor' ) ) {
					wp_enqueue_editor();
				}
				ob_start();
				wp_editor(
					$value, $id, array(
						'default_editor' => 'tmce',
						'wpautop'        => isset( $field['wpautop'] ) ? $field['wpautop'] : false,
						'teeny'          => isset( $field['teeny'] ) ? $field['teeny'] : false,
						'textarea_rows'  => isset( $field['rows'] ) && $field['rows'] > 1 ? $field['rows'] : 10,
						'editor_height'  => 16 * ( isset( $field['rows'] ) && $field['rows'] > 1 ? (int) $field['rows'] : 10 ),
						'tinymce'        => array(
							'resize'             => false,
							'wp_autoresize_on'   => false,
							'add_unload_trigger' => false,
						),
					)
				);
				$editor_html = ob_get_contents();
				ob_end_clean();
				$output .= '<div class="trx_addons_text_editor" data-editor-html="' . esc_attr( $editor_html ) . '">' . $editor_html . '</div>';
				break;
		}

		return $output;
	}
}

if ( ! function_exists( 'trx_addons_options_save' ) ) {
	add_action( 'after_setup_theme', 'trx_addons_options_save', 4 );
	/**
	 * Save plugin options
	 *  
	 * @hooked 'after_setup_theme', 4
	 * 
	 * @trigger trx_addons_filter_options_save
	 * @trigger trx_addons_action_just_save_options
	 */
	function trx_addons_options_save() {

		if ( ! isset( $_REQUEST['page'] ) || $_REQUEST['page'] != 'trx_addons_options' || trx_addons_get_value_gp( 'trx_addons_nonce' ) == '' ) {
			return;
		}

		global $TRX_ADDONS_STORAGE;

		// verify nonce
		if ( ! wp_verify_nonce( trx_addons_get_value_gp('trx_addons_nonce'), admin_url() ) ) {
			trx_addons_set_admin_message( __( 'Bad security code! Options are not saved!', 'trx_addons' ), 'error' );
			return;
		}

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			trx_addons_set_admin_message( __( 'Manage options is denied for the current user! Options are not saved!', 'trx_addons' ), 'error' );
			return;
		}

		// Save options
		$options = array();
		foreach ( $TRX_ADDONS_STORAGE['options'] as $k => $v ) {
			// Skip options without value (section, info, etc.)
			if ( ! isset( $v['std'] ) ) {
				continue;
			}
			// Get option value from POST
			if ( ! empty( $v['hidden'] ) ) {
				if ( $v['std'] != $v['val'] ) {
					$options[ $k ] = $v['val'];
				}
			} else {
				$TRX_ADDONS_STORAGE['options'][ $k ]['val'] = $options[ $k ] = trx_addons_options_get_field_value( $k, $v );
			}
		}

		update_option( 'trx_addons_options', apply_filters('trx_addons_filter_options_save', $options ) );

		do_action( 'trx_addons_action_just_save_options' );

		// Apply action - moved to the delayed state (see below) to load all enabled modules and apply changes after
		// Not need here: do_action('trx_addons_action_save_options');
		update_option( 'trx_addons_action', 'trx_addons_action_save_options' );
		
		// Return result
		trx_addons_set_admin_message( __('Options are saved', 'trx_addons' ), 'success', true );
		if ( ! empty( $_SERVER['HTTP_REFERER'] ) ) {
			wp_safe_redirect( $_SERVER['HTTP_REFERER'] );
			exit();
		}
	}
}
