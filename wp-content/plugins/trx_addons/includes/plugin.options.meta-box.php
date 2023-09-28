<?php
/**
 * Meta Boxes support for custom post types
 *
 * @package ThemeREX Addons
 * @since v1.1
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_meta_box_need_options' ) ) {
	add_filter( 'trx_addons_filter_need_options', 'trx_addons_meta_box_need_options' );
	/**
	 * Check if current screen need to load options scripts and styles
	 * 
	 * @triggered on 'trx_addons_filter_need_options'
	 *
	 * @param bool $need  Filter value
	 *
	 * @return bool     Filtered value
	 */
	function trx_addons_meta_box_need_options( $need = false ) {
		if ( ! $need ) {
			// If current screen is 'Edit Page' with one of ThemeREX Addons custom post types
			$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
			$need = is_object( $screen ) && $screen->id == $screen->post_type && trx_addons_meta_box_is_registered( $screen->post_type );
		}
		return $need;
	}
}

if ( ! function_exists('trx_addons_meta_box_register' ) ) {
	/**
	 * Register meta box for the specified post type
	 * 
	 * @param string $post_type  Post type
	 * @param array $meta_box    Meta box fields array
	 */
	function trx_addons_meta_box_register( $post_type, $meta_box ) {
		if ( ! empty( $post_type ) ) {
			global $TRX_ADDONS_STORAGE;
			if ( ! in_array( $post_type, $TRX_ADDONS_STORAGE['post_types'] ) ) {
				$TRX_ADDONS_STORAGE['post_types'][] = $post_type;
			}
			if ( ! isset( $TRX_ADDONS_STORAGE["meta_box_{$post_type}"] ) ) {
				$TRX_ADDONS_STORAGE["meta_box_{$post_type}"] = array();
			}
			if ( trx_addons_meta_box_check_sections( $meta_box ) && ! trx_addons_meta_box_check_sections( $TRX_ADDONS_STORAGE["meta_box_{$post_type}"] ) ) {
				$TRX_ADDONS_STORAGE["meta_box_{$post_type}"] = array_merge(
																	array(
																		$post_type . "_section" => array(
																			"title" => esc_html__("General", 'trx_addons'),
																			"desc" => wp_kses_data( __('General parameters for this post', 'trx_addons') ),
																			"type" => "section"
																		)
																	),
																	$TRX_ADDONS_STORAGE["meta_box_{$post_type}"]
																);
			}
			$TRX_ADDONS_STORAGE["meta_box_{$post_type}"] = apply_filters(
																'trx_addons_filter_meta_box_fields',
																array_merge(
																	$TRX_ADDONS_STORAGE["meta_box_{$post_type}"],
																	$meta_box
																),
																$post_type
															);
		}
	}
}

if ( ! function_exists( 'trx_addons_meta_box_check_sections' ) ) {
	/**
	 * Check if meta box contains sections
	 *
	 * @param array $meta_box  Meta box fields array
	 *
	 * @return bool  		True if meta box contains at least one section
	 */
	function trx_addons_meta_box_check_sections( $meta_box ) {
		$rez = false;
		if ( is_array( $meta_box ) ) {
			foreach( $meta_box as $v ) {
				if ( $v['type'] == 'section' ) {
					$rez = true;
					break;
				}
			}
		}
		return $rez;
	}
}

if ( ! function_exists( 'trx_addons_meta_box_is_registered' ) ) {
	/**
	 * Check if meta box is registered for the specified post type
	 *
	 * @param string $post_type  Post type
	 *
	 * @return bool  		True if meta box is registered for the specified post type
	 */
	function trx_addons_meta_box_is_registered( $post_type ) {
		global $TRX_ADDONS_STORAGE;
		return ! empty( $TRX_ADDONS_STORAGE['post_types'] ) && in_array( $post_type, $TRX_ADDONS_STORAGE['post_types'] );
	}
}

if ( ! function_exists( 'trx_addons_meta_box_get' ) ) {
	/**
	 * Return meta box fields for the specified post type
	 *
	 * @param string $post_type  Post type
	 *
	 * @return array  		Meta box fields
	 */
	function trx_addons_meta_box_get( $post_type ) {
		global $TRX_ADDONS_STORAGE;
		return isset( $TRX_ADDONS_STORAGE["meta_box_{$post_type}"] ) ? $TRX_ADDONS_STORAGE["meta_box_{$post_type}"] : array();
	}
}

if ( ! function_exists( 'trx_addons_meta_box_add' ) ) {
	add_action( 'add_meta_boxes', 'trx_addons_meta_box_add' );
	/**
	 * Add meta box for the current post type
	 * 
	 * @hooked add_meta_boxes
	 * 
	 * @trigger trx_addons_filter_add_meta_box_context
	 * @trigger trx_addons_filter_add_meta_box_priority
	 */
	function trx_addons_meta_box_add() {
		global $post_type;
		if ( trx_addons_meta_box_is_registered( $post_type ) ) {
			add_meta_box(
				'trx_addons_meta_box', 
				esc_html__('Item Options', 'trx_addons'),
				'trx_addons_meta_box_show', 
				$post_type, 
				apply_filters( 'trx_addons_filter_add_meta_box_context', 'advanced', $post_type ), 
				apply_filters( 'trx_addons_filter_add_meta_box_priority', 'default', $post_type )
			);
		}
		// Custom theme-specific meta-boxes
		$boxes = apply_filters('trx_addons_filter_override_options', array());
		if ( is_array( $boxes ) ) {
			foreach ( $boxes as $box ) {
				$box = trx_addons_array_merge( array( '',		// id
													'',			// title
													'',			// callback
													null,		// screen
													'advanced',	// context
													'default',	// priority
													null		// callbacks
												),
												$box
											);
				add_meta_box( $box[0], $box[1], $box[2], $box[3], $box[4], $box[5], $box[6] );
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_meta_box_show' ) ) {
	/**
	 * Callback function to show fields in meta box for the current post type
	 */
	function trx_addons_meta_box_show() {
		global $post, $post_type;
		if ( trx_addons_meta_box_is_registered( $post_type ) ) {
			// Load saved options 
			$options = apply_filters( 'trx_addons_filter_load_post_options', get_post_meta( $post->ID, 'trx_addons_options', true ), $post->ID, $post_type );
			$meta_box = trx_addons_meta_box_get( $post_type );
			foreach ( $meta_box as $k => $v ) {
				if ( isset( $meta_box[ $k ]['std'] ) ) {
					$meta_box[ $k ]['val'] = isset( $options[ $k ] ) ? $options[ $k ] : $meta_box[ $k ]['std'];
				}
			}
			?>
			<div class="trx_addons_options">
				<input type="hidden" name="meta_box_post_nonce" value="<?php echo esc_attr( wp_create_nonce( admin_url() ) ); ?>" />
				<input type="hidden" name="meta_box_post_type" value="<?php echo esc_attr( $post_type ); ?>" />
				<?php trx_addons_options_show_fields( $meta_box, $post_type ); ?>
			</div>
			<?php		
		}
	}
}

if ( ! function_exists( 'trx_addons_meta_box_save' ) ) {
	add_action( 'save_post', 'trx_addons_meta_box_save' );
	/**
	 * Save data from meta box on post save
	 * 
	 * @hooked save_post
	 * 
	 * @trigger trx_addons_filter_save_post_options 
	 *
	 * @param int $post_id		Post ID
	 */
	function trx_addons_meta_box_save( $post_id ) {

		// verify nonce
		if ( ! wp_verify_nonce( trx_addons_get_value_gp( 'meta_box_post_nonce' ), admin_url() ) ) {
			return $post_id;
		}

		// check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		$post_type = isset( $_POST['meta_box_post_type'] ) ? $_POST['meta_box_post_type'] : $_POST['post_type'];
		if ( ! trx_addons_meta_box_is_registered( $post_type ) ) {
			return $post_id;
		}

		// check permissions
		$capability = 'post';
		$post_types = get_post_types( array( 'name' => $post_type ), 'objects' );
		if ( ! empty( $post_types ) && is_array( $post_types ) ) {
			foreach ( $post_types  as $type ) {
				$capability = $type->capability_type;
				break;
			}
		}
		if ( ! current_user_can( "edit_{$capability}", $post_id ) ) {
			return $post_id;
		}

		// Save meta
		$options = array();
		$meta_box = trx_addons_meta_box_get( $post_type );
		foreach ( $meta_box as $k => $v ) {
			// Skip options without value (section, info, etc.)
			if ( ! isset( $v['std'] ) ) {
				continue;
			}
			// Get option value from POST
			$options[ $k ] = trx_addons_options_get_field_value( $k, $v );
			if ( $k == 'icon' && strtolower( $options[ $k ] ) == 'none' ) {
				$options[$k] = '';
			}
		}
		update_post_meta( $post_id, 'trx_addons_options', apply_filters( 'trx_addons_filter_save_post_options', $options, $post_id, $post_type ) );
	}
}
