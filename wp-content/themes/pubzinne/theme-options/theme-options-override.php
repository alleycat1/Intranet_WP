<?php
/**
 * Override Theme Options on a posts and pages
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.0.29
 */


// -----------------------------------------------------------------
// -- Override Theme Options
// -----------------------------------------------------------------

if ( ! function_exists( 'pubzinne_options_override_init' ) ) {
	add_action( 'after_setup_theme', 'pubzinne_options_override_init' );
	function pubzinne_options_override_init() {
		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', 'pubzinne_options_override_add_scripts' );
			add_action( 'save_post', 'pubzinne_options_override_save_options' );
			add_filter( 'pubzinne_filter_override_options', 'pubzinne_options_override_add_options' );
		}
	}
}


// Check if override options is allowed for specified post type
if ( ! function_exists( 'pubzinne_options_allow_override' ) ) {
	function pubzinne_options_allow_override( $post_type ) {
		return apply_filters( 'pubzinne_filter_allow_override_options', in_array( $post_type, array( 'page', 'post' ) ), $post_type );
	}
}

// Load required styles and scripts for admin mode
if ( ! function_exists( 'pubzinne_options_override_add_scripts' ) ) {
	//Handler of the add_action("admin_enqueue_scripts", 'pubzinne_options_override_add_scripts');
	function pubzinne_options_override_add_scripts() {
		// If current screen is 'Edit Page' - load font icons
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
		if ( is_object( $screen ) && pubzinne_options_allow_override( ! empty( $screen->post_type ) ? $screen->post_type : $screen->id ) ) {
			wp_enqueue_style( 'pubzinne-fontello', pubzinne_get_file_url( 'css/font-icons/css/fontello.css' ), array(), null );
			wp_enqueue_script( 'jquery-ui-tabs', false, array( 'jquery', 'jquery-ui-core' ), null, true );
			wp_enqueue_script( 'jquery-ui-accordion', false, array( 'jquery', 'jquery-ui-core' ), null, true );
			wp_enqueue_script( 'pubzinne-options', pubzinne_get_file_url( 'theme-options/theme-options.js' ), array( 'jquery' ), null, true );
			wp_localize_script( 'pubzinne-options', 'pubzinne_dependencies', pubzinne_get_theme_dependencies() );
		}
	}
}

// Add overriden options
if ( ! function_exists( 'pubzinne_options_override_add_options' ) ) {
	//Handler of the add_filter('pubzinne_filter_override_options', 'pubzinne_options_override_add_options');
	function pubzinne_options_override_add_options( $list ) {
		global $post_type;
		if ( pubzinne_options_allow_override( $post_type ) ) {
			$list[] = array(
				sprintf( 'pubzinne_override_options_%s', $post_type ),
				esc_html__( 'Theme Options', 'pubzinne' ),
				'pubzinne_options_override_show',
				$post_type,
				'post' == $post_type ? 'side' : 'advanced',
				'default',
			);
		}
		return $list;
	}
}

// Callback function to show override options
if ( ! function_exists( 'pubzinne_options_override_show' ) ) {
	function pubzinne_options_override_show( $post = false, $args = false ) {
		if ( empty( $post ) || ! is_object( $post ) || empty( $post->ID ) ) {
			global $post, $post_type;
			$mb_post_id   = $post->ID;
			$mb_post_type = $post_type;
		} else {
			$mb_post_id   = $post->ID;
			$mb_post_type = $post->post_type;
		}
		if ( pubzinne_options_allow_override( $mb_post_type ) ) {
			// Load saved options
			$meta         = get_post_meta( $mb_post_id, 'pubzinne_options', true );
			$tabs_titles  = array();
			$tabs_content = array();
			global $PUBZINNE_STORAGE;
			// Refresh linked data if this field is controller for the another (linked) field
			// Do this before show fields to refresh data in the $PUBZINNE_STORAGE
			foreach ( $PUBZINNE_STORAGE['options'] as $k => $v ) {
				if ( ! isset( $v['override'] ) || strpos( $v['override']['mode'], $mb_post_type ) === false ) {
					continue;
				}
				if ( ! empty( $v['linked'] ) ) {
					$v['val'] = isset( $meta[ $k ] ) ? $meta[ $k ] : 'inherit';
					if ( ! empty( $v['val'] ) && ! pubzinne_is_inherit( $v['val'] ) ) {
						pubzinne_refresh_linked_data( $v['val'], $v['linked'] );
					}
				}
			}
			// Show fields
			foreach ( $PUBZINNE_STORAGE['options'] as $k => $v ) {
				if ( ! isset( $v['override'] ) || strpos( $v['override']['mode'], $mb_post_type ) === false || 'hidden' == $v['type'] ) {
					continue;
				}
				if ( empty( $v['override']['section'] ) ) {
					$v['override']['section'] = esc_html__( 'General', 'pubzinne' );
				}
				if ( ! isset( $tabs_titles[ $v['override']['section'] ] ) ) {
					$tabs_titles[ $v['override']['section'] ]  = $v['override']['section'];
					$tabs_content[ $v['override']['section'] ] = '';
				}
				$v['val'] = isset( $meta[ $k ] ) ? $meta[ $k ] : 'inherit';
				if ( 'group' == $v['type'] ) {
					// Fields set (group)
					if ( count( $v['fields'] ) > 0 ) {
						$tabs_content[ $v['override']['section'] ] .= pubzinne_options_show_group( $k, $v, $mb_post_type );
					}
				} else {
					// Regular field
					$tabs_content[ $v['override']['section'] ] .= pubzinne_options_show_field( $k, $v, $mb_post_type );
				}
			}
			// Add Options presets
			$tabs_titles[ 'presets' ]  = esc_html__( 'Options presets', 'pubzinne' );
			$tabs_content[ 'presets' ] = pubzinne_options_show_field( 'presets', array(
												'title' => esc_html__( 'Options Presets', 'pubzinne' ),
												'desc'  => esc_html__( 'Select a preset to override options of the current page or save current options as a new preset', 'pubzinne' ),
												'type'  => 'presets',
											), $mb_post_type );

			// Display options
			if ( count( $tabs_titles ) > 0 ) {
				?>
				<div class="pubzinne_options pubzinne_options_override">
					<input type="hidden" name="override_options_nonce" value="<?php echo esc_attr( wp_create_nonce( admin_url() ) ); ?>" />
					<input type="hidden" name="override_options_post_type" value="<?php echo esc_attr( $mb_post_type ); ?>" />
					<div id="pubzinne_options_tabs" class="pubzinne_tabs pubzinne_tabs_vertical">
						<ul>
							<?php
							$cnt = 0;
							foreach ( $tabs_titles as $k => $v ) {
								$cnt++;
								?>
								<li><a href="#pubzinne_options_<?php echo esc_attr( $cnt ); ?>"><?php echo esc_html( $v ); ?></a></li>
								<?php
							}
							?>
						</ul>
						<?php
						$cnt = 0;
						foreach ( $tabs_content as $k => $v ) {
							$cnt++;
							?>
							<div id="pubzinne_options_<?php echo esc_attr( $cnt ); ?>" class="pubzinne_tabs_section pubzinne_options_section">
								<?php pubzinne_show_layout( $v ); ?>
							</div>
							<?php
						}
						?>
					</div>
				</div>
				<?php
			}
		}
	}
}


// Save overriden options
if ( ! function_exists( 'pubzinne_options_override_save_options' ) ) {
	//Handler of the add_action('save_post', 'pubzinne_options_override_save_options');
	function pubzinne_options_override_save_options( $post_id ) {
		// verify nonce
		if ( ! wp_verify_nonce( pubzinne_get_value_gp( 'override_options_nonce' ), admin_url() ) ) {
			return $post_id;
		}

		// check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		$post_type = wp_kses_data( wp_unslash( isset( $_POST['override_options_post_type'] ) ? $_POST['override_options_post_type'] : $_POST['post_type'] ) );

		// Check permissions
		$capability = 'page';
		$post_types = get_post_types( array( 'name' => $post_type ), 'objects' );
		if ( ! empty( $post_types ) && is_array( $post_types ) ) {
			foreach ( $post_types  as $type ) {
				$capability = $type->capability_type;
				break;
			}
		}
		if ( ! current_user_can( 'edit_' . ( $capability ), $post_id ) ) {
			return $post_id;
		}

		// Save options
		$meta    = array();
		$options = pubzinne_storage_get( 'options' );
		foreach ( $options as $k => $v ) {
			// Skip not overriden options
			if ( ! isset( $v['override'] ) || strpos( $v['override']['mode'], $post_type ) === false ) {
				continue;
			}
			// Skip inherited options
			if ( ! empty( $_POST[ "pubzinne_options_inherit_{$k}" ] ) ) {
				continue;
			}
			// Skip hidden options
			if ( ! isset( $_POST[ "pubzinne_options_field_{$k}" ] ) && 'hidden' == $v['type'] ) {
				continue;
			}
			// Get option value from POST
			$meta[ $k ] = isset( $_POST[ "pubzinne_options_field_{$k}" ] )
							? pubzinne_get_value_gp( "pubzinne_options_field_{$k}" )
							: ( 'checkbox' == $v['type'] ? 0 : '' );
		}
		$meta = apply_filters( 'pubzinne_filter_update_post_options', $meta, $post_id );

		update_post_meta( $post_id, 'pubzinne_options', $meta );

		// Save separate meta options to search template pages
		if ( 'page' == $post_type ) {
			$page_template = isset( $_POST['page_template'] )
								? $_POST['page_template']
								: get_post_meta( $post_id, '_wp_page_template', true );
			if ( 'blog.php' == $page_template ) {
				update_post_meta( $post_id, 'pubzinne_options_post_type', isset( $meta['post_type'] ) ? $meta['post_type'] : 'post' );
				update_post_meta( $post_id, 'pubzinne_options_parent_cat', isset( $meta['parent_cat'] ) ? $meta['parent_cat'] : 0 );
			}
		}
	}
}


//------------------------------------------------------
// Extra column for posts/pages lists
// with overriden options
//------------------------------------------------------

// Create additional column
if ( ! function_exists( 'pubzinne_add_options_column' ) ) {
	add_filter( 'manage_edit-post_columns', 'pubzinne_add_options_column', 9 );
	add_filter( 'manage_edit-page_columns', 'pubzinne_add_options_column', 9 );
	function pubzinne_add_options_column( $columns ) {
		$columns['theme_options'] = esc_html__( 'Theme Options', 'pubzinne' );
		return $columns;
	}
}

// Fill column with data
if ( ! function_exists( 'pubzinne_fill_options_column' ) ) {
	add_filter( 'manage_post_posts_custom_column', 'pubzinne_fill_options_column', 9, 2 );
	add_filter( 'manage_page_posts_custom_column', 'pubzinne_fill_options_column', 9, 2 );
	function pubzinne_fill_options_column( $column_name = '', $post_id = 0 ) {
		if ( 'theme_options' != $column_name ) {
			return;
		}
		$options = '';
		$props = get_post_meta( $post_id, 'pubzinne_options', true);
		if ( $props ) {
			if ( is_array( $props ) && count( $props ) > 0 ) {
				foreach ( $props as $prop_name => $prop_value ) {
					if ( ! pubzinne_is_inherit( $prop_value ) && pubzinne_storage_get_array( 'options', $prop_name, 'type' ) != 'hidden' ) {
						$prop_title = pubzinne_storage_get_array( 'options', $prop_name, 'title' );
						if ( empty( $prop_title ) ) {
							$prop_title = $prop_name;
						}
						$options .= '<div class="pubzinne_options_prop_row">'
										. '<span class="pubzinne_options_prop_name">' . esc_html( $prop_title ) . '</span>'
										. '&nbsp;=&nbsp;'
										. '<span class="themerex_options_prop_value">'
											. ( is_array( $prop_value )
												? esc_html__('[Complex Data]', 'pubzinne')
												: '"' . esc_html( pubzinne_strshort( $prop_value, 80 ) ) . '"'
												)
										. '</span>'
									. '</div>';
					}
				}
			}
		}
		pubzinne_show_layout( $options, '<div class="pubzinne_options_list">', '</div>' );
	}
}

// Display 'Blog archive' as post state
if ( ! function_exists( 'pubzinne_display_post_states' ) ) {
	add_filter( 'display_post_states', 'pubzinne_display_post_states', 9, 2 );
	function pubzinne_display_post_states( $post_states, $post ) {
		if ( is_object( $post ) && ! empty( $post->post_type ) && 'page' == $post->post_type ) {
			if ( get_post_meta( $post->ID, '_wp_page_template', true ) == 'blog.php' ) {
				$props = get_post_meta( $post->ID, 'pubzinne_options', true);
				$post_type_and_cat = '';
				if ( empty( $props['post_type'] ) ) {
					if ( ! is_array( $props ) ) {
						$props = array();
					}
					$props['post_type'] = 'post';
				}
				$post_obj = get_post_type_object( $props['post_type'] );
				$post_type_and_cat = is_object( $post_obj )
										? $post_obj->labels->name
										: $props['post_type'];
				if ( ! empty( $props['parent_cat'] ) ) {
					$term = get_term_by( 'id', $props['parent_cat'], pubzinne_get_post_type_taxonomy( $props['post_type'] ), OBJECT );
					if ( $term ) {
						$post_type_and_cat .= ' -> ' . $term->name;
					}
				}
				$post_states[] = ! empty( $post_type_and_cat )
									// Translators: Add post type and category to the page state
									? sprintf( esc_html__( 'Blog archive for "%s"', 'pubzinne' ), $post_type_and_cat )
									: esc_html__( 'Blog archive', 'pubzinne' );
			}
		}
		return $post_states;
	}
}


//------------------------------------------------------
// Options presets
//------------------------------------------------------

// AJAX: Add a new preset
if ( ! function_exists( 'pubzinne_callback_add_options_preset' ) ) {
	add_action( 'wp_ajax_pubzinne_add_options_preset', 'pubzinne_callback_add_options_preset' );
	function pubzinne_callback_add_options_preset() {
		pubzinne_verify_nonce();
		if ( ! current_user_can( 'manage_options' ) ) {
			pubzinne_forbidden( esc_html__( 'Sorry, you are not allowed to manage options.', 'pubzinne' ) );
		}
		$response  = array( 'error' => '', 'success' => '' );
		if ( ! empty( $_REQUEST['preset_name'] ) && ! empty( $_REQUEST['preset_data'] ) ) {
			$preset_name = wp_kses_data( wp_unslash( $_REQUEST['preset_name'] ) );
			$preset_data = wp_kses_data( wp_unslash( $_REQUEST['preset_data'] ) );
			$preset_type = wp_kses_data( wp_unslash( $_REQUEST['preset_type'] ) );
			if ( empty( $preset_type ) ) {
				$preset_type = '#';
			}
			$presets = get_option( 'pubzinne_options_presets' );
			if ( empty( $presets ) || ! is_array( $presets ) ) {
				$presets = array();
			}
			if ( empty( $presets[ $preset_type ] ) || ! is_array( $presets[ $preset_type ] ) ) {
				$presets[ $preset_type ] = array();
			}
			$presets[ $preset_type ][ $preset_name ] = $preset_data;
			update_option( 'pubzinne_options_presets', $presets );
			// Translators: Add preset name to the message
			$response['success'] = esc_html( sprintf( __( 'Preset "%s" is added!', 'pubzinne' ), $preset_name ) );
		} else {
			$response['error'] = esc_html__( 'Wrong preset name or options data is received! Preset is not added!', 'pubzinne' );
		}
		pubzinne_ajax_response( $response );
	}
}

// AJAX: Delete a new preset
if ( ! function_exists( 'pubzinne_callback_delete_options_preset' ) ) {
	add_action( 'wp_ajax_pubzinne_delete_options_preset', 'pubzinne_callback_delete_options_preset' );
	function pubzinne_callback_delete_options_preset() {
		pubzinne_verify_nonce();
		if ( ! current_user_can( 'manage_options' ) ) {
			pubzinne_forbidden( esc_html__( 'Sorry, you are not allowed to manage options.', 'pubzinne' ) );
		}
		$response  = array( 'error' => '', 'success' => '' );
		if ( ! empty( $_REQUEST['preset_name'] ) ) {
			$preset_name = wp_kses_data( wp_unslash( $_REQUEST['preset_name'] ) );
			$preset_type = wp_kses_data( wp_unslash( $_REQUEST['preset_type'] ) );
			if ( empty( $preset_type ) ) {
				$preset_type = '#';
			}
			$presets = get_option( 'pubzinne_options_presets' );
			if ( isset( $presets[ $preset_type ][ $preset_name ] ) ) {
				unset( $presets[ $preset_type ][ $preset_name ] );
				update_option( 'pubzinne_options_presets', $presets );
			}
			// Translators: Add preset name to the message
			$response['success'] = esc_html( sprintf( __( 'Preset "%s" is deleted!', 'pubzinne' ), $preset_name ) );
		} else {
			$response['error'] = esc_html__( 'Wrong preset name is received! Preset is not deleted!', 'pubzinne' );
		}
		pubzinne_ajax_response( $response );
	}
}
