<?php
/**
 * Addional featured image for any post types
 *
 * @addon secondary-image
 * @version 1.2
 *
 * @package ThemeREX Addons
 * @since v1.84.0
 */


class TRX_Addons_Secondary_Image {

	/**
	 * The class arguments.
	 *
	 * @since 1.84.0
	 * @access private
	 * @var array
	 */
	private $args = array();

	/**
	 * Constructor.
	 *
	 * @since 1.84.0
	 * @access public
	 * @param array $args The arguments.
	 * @return void
	 */
	public function __construct( $args = array() ) {

		$this->args = wp_parse_args( $args, array(
			'store_as_url' => true,
			'post_type'    => 'post',
			'name'         => esc_attr__( 'Secondary Image', 'trx_addons' ),
			'label_set'    => esc_attr__( 'Set secondary image', 'trx_addons' ),
			'label_remove' => esc_attr__( 'Remove secondary image', 'trx_addons' ),
		) );
		$this->args['metabox_id']       = 'secondary_image';
		$this->args['post_meta_key']    = 'trx_addons_' . $this->args['metabox_id'];
		$this->args['nonce_action']     = $this->args['metabox_id'] . '_nonce_action';
		$this->args['nonce_name']       = $this->args['metabox_id'] . '_nonce_name';

		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_meta_box' ) );
	}

	/**
	 * Add metabox for a secondary image.
	 *
	 * @since 1.84.0
	 * @access public
	 * @return void
	 */
	public function add_meta_box() {
		add_meta_box(
			$this->args['metabox_id'],
			$this->args['name'],
			array( $this, 'show_meta_box' ),
			$this->args['post_type'],
			'side',
			'default'
		);
	}

	/**
	 * Output the metabox content.
	 *
	 * @since 1.84.0
	 * @access public
	 * @global object $post
	 * @return void
	 */
	public function show_meta_box() {
		global $post;

		$image_id  = get_post_meta( $post->ID, $this->args['post_meta_key'], true );
		if ( is_numeric( $image_id ) && (int) $image_id > 0 ) {
			$attachment = wp_get_attachment_image_src( $image_id, 'post-thumbnail' );
			$image_url = empty( $attachment[0] ) ? '' : $attachment[0];
		} else {
			$image_url = $image_id;
		}

		$output = '<div class="trx_addons-secondary-image-meta-box">'
						. wp_nonce_field( $this->args['nonce_action'], $this->args['nonce_name'], true, false )
						. '<input type="hidden"'
								. ' id="' . esc_attr( $this->args['post_meta_key'] ) . '_pt"'
								. ' name="' . esc_attr( $this->args['post_meta_key'] ) . '_pt"'
								. ' value="' . esc_attr( $this->args['post_type'] ) . '"'
								. '>'
						. '<input type="hidden"'
								. ' id="' . esc_attr( $this->args['post_meta_key'] ) . '"'
								. ' name="' . esc_attr( $this->args['post_meta_key'] ) . '"'
								. ' value="' . esc_attr( $image_url ) . '"'
								. '>'
						. trx_addons_options_show_custom_field( $this->args['post_meta_key'] . '_button',
								array(
									'type' => 'mediamanager',
									'multiple' => false,
									'data_type' => 'image',
									'button_caption' => '',
									'class_field' => '',
									'linked_field_id' => $this->args['post_meta_key']
									),
								$image_url
							)
					. '</div>';


		trx_addons_show_layout( $output );
	}

	/**
	 * Saves the metabox.
	 *
	 * @since 1.84
	 * @access public
	 * @param string|int $post_id The post ID.
	 * @return void.
	 */
	public function save_meta_box( $post_id ) {
		// check nonce
		if ( ! wp_verify_nonce( trx_addons_get_value_gp( $this->args['nonce_name'] ), $this->args['nonce_action'] ) ) {
			return;
		}
		// check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $post_id;
		}
		// check permissions
		$capability = 'post';
		$post_type = trx_addons_get_value_gp( $this->args['post_meta_key'] . '_pt', trx_addons_get_value_gp( 'post_type' ) );
		if ( ! empty( $post_type ) ) {
			$post_types = get_post_types( array( 'name' => $post_type ), 'objects' );
			if (!empty($post_types) && is_array($post_types)) {
				foreach ($post_types  as $type) {
					$capability = $type->capability_type;
					break;
				}
			}
		}
		if ( ! current_user_can( "edit_{$capability}", $post_id ) ) {
			return $post_id;
		}
		// save post meta
		$image_url = sanitize_text_field( trx_addons_get_value_gp( $this->args['post_meta_key'] ) );
		$image_id  = empty( $this->args['store_as_url'] ) ? trx_addons_attachment_url_to_postid( $image_url ) : 0;
		update_post_meta( $post_id, $this->args['post_meta_key'], is_numeric( $image_id ) && (int) $image_id > 0 ? $image_id : $image_url );
	}

	/**
	 * Return the ID of the secondary image.
	 *
	 * @since 1.84.0
	 * @static
	 * @access public
	 * @param int    $post_id A custom post ID.
	 * @return int The secondary image ID.
	 */
	public static function get_secondary_image_id( $post_id = -1, $check_pt = true ) {
		$image_id = 0;
		if ( $post_id == -1 ) {
			$post_id = get_the_ID();
		}
		$checked = ! $check_pt;
		if ( ! $checked ) {
			$post_types = trx_addons_get_option('secondary_image_post_types');
			$post_type = get_post_type( $post_id );
			$checked = is_array($post_types) && ! empty( $post_types[ $post_type ] );
		}
		if ( $checked ) {
			$image_id = get_post_meta( $post_id, 'trx_addons_secondary_image', true );
			if ( ! empty( $image_id ) && (int) $image_id == 0 ) {
				$image_id = trx_addons_attachment_url_to_postid( trx_addons_clear_thumb_size( $image_id ) );
			}
		}
		return apply_filters( 'trx_addons_filter_secondary_image_id', $image_id );
	}

	/**
	 * Return the url of the secondary image.
	 *
	 * @since 1.84.0
	 * @static
	 * @access public
	 * @param string $post_type The post type of the post the featured image belongs to.
	 * @param int    $post_id A custom post ID.
	 * @return int The secondary image ID.
	 */
	public static function get_secondary_image_url( $thumb_size, $post_id = -1, $check_pt = true ) {
		$image_url = 0;
		if ( $post_id == -1 ) {
			$post_id = get_the_ID();
		}
		$checked = ! $check_pt;
		if ( ! $checked ) {
			$post_types = trx_addons_get_option('secondary_image_post_types');
			$post_type = get_post_type( $post_id );
			$checked = is_array($post_types) && ! empty( $post_types[ $post_type ] );
		}
		if ( $checked ) {
			$image_id = get_post_meta( $post_id, 'trx_addons_secondary_image', true );
			if ( is_numeric( $image_id ) && (int) $image_id > 0 ) {
				$attachment = wp_get_attachment_image_src( $image_id, $thumb_size );
				$image_url = empty( $attachment[0] ) ? '' : $attachment[0];
			} else {
				$image_url = trx_addons_add_thumb_size( $image_id, $thumb_size );
			}
		}
		return apply_filters( 'trx_addons_filter_secondary_image_url', $image_url );
	}
}


// Return the ID of the secondary image.
if ( ! function_exists( 'trx_addons_get_secondary_image_id' ) ) {
	function trx_addons_get_secondary_image_id( $post_id = -1 ) {
		return TRX_Addons_Secondary_Image::get_secondary_image_id( $post_id );
	}
}


// Return the url of the secondary image.
if ( ! function_exists( 'trx_addons_get_secondary_image_url' ) ) {
	function trx_addons_get_secondary_image_url( $thumb_size, $post_id = -1 ) {
		return TRX_Addons_Secondary_Image::get_secondary_image_url( $thumb_size, $post_id );
	}
}


// Add params to the ThemeREX Addons Options.
if ( ! function_exists( 'trx_addons_secondary_image_add_options' ) ) {
	add_filter( 'trx_addons_filter_options', 'trx_addons_secondary_image_add_options' );
	function trx_addons_secondary_image_add_options( $options ) {
		trx_addons_array_insert_before($options, 'sc_section', apply_filters( 'trx_addons_filter_options_secondary_image', array(
			'secondary_image_section' => array(
				"title" => esc_html__('Secondary image', 'trx_addons'),
				'icon' => 'trx_addons_icon-format-image',
				"type" => "section"
			),
			'secondary_image_section_info' => array(
				"title" => esc_html__('Secondary image settings', 'trx_addons'),
				"desc" => wp_kses_data( __("Settings of the secondary images for any post type", 'trx_addons') ),
				"type" => "info"
			),
			"secondary_image_post_types" => array(
				"title" => esc_html__("Post types", 'trx_addons'),
				"desc" => wp_kses_data( __("Select post types to add secondary image (showed on hover in shortcodes and post type archives)", 'trx_addons') ),
				"dir" => 'horizontal',
				"std" => array( 'post' => 1 ),
				"options" => array(),
				"type" => "checklist"
			),					
		)));
		return $options;
	}
}

// Fill 'Post types' before show ThemeREX Addons Options
if ( ! function_exists('trx_addons_secondary_image_before_show_options')) {
	add_filter( 'trx_addons_filter_before_show_options', 'trx_addons_secondary_image_before_show_options', 10, 2);
	function trx_addons_secondary_image_before_show_options($options, $pt='') {
		if ( isset($options['secondary_image_post_types']) ) {
			$options['secondary_image_post_types']['options'] = trx_addons_get_list_posts_types();
		}
		return $options;
	}
}


// Init secondary image for selected post types
if ( ! function_exists( 'trx_addons_secondary_image_init_post_types' ) ) {
	add_filter( 'init', 'trx_addons_secondary_image_init_post_types' );
	function trx_addons_secondary_image_init_post_types() {
		$post_types = trx_addons_get_option('secondary_image_post_types');
		if ( is_array($post_types) ) {
			foreach( $post_types as $pt => $v ) {
				if ( empty($v) ) continue;
				new TRX_Addons_Secondary_Image( array(
					'post_type' => $pt
				) );
			}
		}
	}
}


// Load required styles and scripts for the frontend
if ( ! function_exists( 'trx_addons_secondary_image_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_secondary_image_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY);
	function trx_addons_secondary_image_load_scripts_front() {
		if ( trx_addons_is_on( trx_addons_get_option('debug_mode') ) ) {
			wp_enqueue_style( 'trx_addons-secondary-image', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . "secondary-image/secondary-image.css" ), array(), null );
		}
	}
}

	
// Merge styles to the single stylesheet
if ( ! function_exists( 'trx_addons_secondary_image_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_secondary_image_merge_styles');
	function trx_addons_secondary_image_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . "secondary-image/secondary-image.css" ] = true;
		return $list;
	}
}


// Return secondary image layout
if ( ! function_exists( 'trx_addons_secondary_image_get_layout' ) ) {
	function trx_addons_secondary_image_get_layout() {
		$output = '';
		$image_url = trx_addons_get_secondary_image_url(
							apply_filters('trx_addons_filter_thumb_size', trx_addons_is_single() ? 'full' : trx_addons_get_thumb_size( 'masonry-big' ), 'secondary-image')
							);
		if ( ! empty( $image_url ) ) {
			$output = '<div class="trx_addons_secondary_image ' . trx_addons_add_inline_css_class( 'background-image:url(' . esc_url( $image_url ) . ');' ) . '"></div>';
		}
		return $output;
	}
}

// Start capture the featured image output
if ( ! function_exists( 'trx_addons_secondary_image_start_capture_featured_image_output' ) ) {
	add_filter( 'trx_addons_filter_featured_image', 'trx_addons_secondary_image_start_capture_featured_image_output', 1, 2 );
	function trx_addons_secondary_image_start_capture_featured_image_output( $done, $args=array() ) {
		$post_types = trx_addons_get_option('secondary_image_post_types');
		$post_type = get_post_type();
		if ( is_array($post_types) && ! empty( $post_types[ $post_type ] ) ) {
			ob_start();
		}
		return $done;
	}
}

// Add secondary image to the featured image output
if ( ! function_exists( 'trx_addons_secondary_image_add_to_featured_image_output' ) ) {
	add_filter( 'trx_addons_filter_featured_image', 'trx_addons_secondary_image_add_to_featured_image_output', 1000, 2 );
	function trx_addons_secondary_image_add_to_featured_image_output( $done, $args=array() ) {
		$post_types = trx_addons_get_option('secondary_image_post_types');
		$post_type = get_post_type();
		if ( is_array($post_types) && ! empty( $post_types[ $post_type ] ) ) {
			$output = ob_get_contents();
			ob_end_clean();
			if ( ! empty( $output ) ) {
				if ( strpos( $output, 'post_featured') !== false && strpos( $output, 'trx_addons_secondary_image') === false ) {
					$html = trx_addons_secondary_image_get_layout();
					if ( ! empty( $html ) ) {
						$output = preg_replace(
												'/(<div[\s]*class="[^"]*post_featured)([^>]*>)/',
												'$1 with_secondary_image $2' . $html,
												$output
												);
					}
				}
				trx_addons_show_layout( $output );
			}
		}
		return $done;
	}
}


// Add secondary image to the featured image output
if ( ! function_exists( 'trx_addons_secondary_image_add_to_featured_image_internal' ) ) {
	add_action( 'trx_addons_action_before_featured', 'trx_addons_secondary_image_add_to_featured_image_internal' );
	function trx_addons_secondary_image_add_to_featured_image_internal() {
		$output = trx_addons_secondary_image_get_layout();
		if ( ! empty( $output ) ) {
			echo wp_kses( trx_addons_secondary_image_get_layout(), 'trx_addons_kses_content' );
		}
	}
}
