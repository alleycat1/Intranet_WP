<?php
/**
 * ThemeREX Addons Custom post type: Layouts
 *
 * @package ThemeREX Addons
 * @since v1.6.06
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// -----------------------------------------------------------------
// -- Custom post type registration
// -----------------------------------------------------------------

// Define Custom post type and taxonomy constants
if ( ! defined('TRX_ADDONS_CPT_LAYOUTS_PT') ) define('TRX_ADDONS_CPT_LAYOUTS_PT', trx_addons_cpt_param('layouts', 'post_type'));
if ( ! defined('TRX_ADDONS_CPT_LAYOUTS_TAXONOMY') ) define('TRX_ADDONS_CPT_LAYOUTS_TAXONOMY', trx_addons_cpt_param('layouts', 'taxonomy'));

if ( ! defined('TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES')) define('TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES', TRX_ADDONS_PLUGIN_CPT.'layouts/shortcodes/');


// Register post type and taxonomy (if need)
if (!function_exists('trx_addons_cpt_layouts_init')) {
	// Priority 8 needs for WPBakery Frontend mode
	// because it check capabilities on the priority 9
	add_action( 'init', 'trx_addons_cpt_layouts_init', 8 );
	function trx_addons_cpt_layouts_init() {

		if ( ! trx_addons_exists_page_builder() ) return;

		// Add Layouts parameters to the Meta Box support
		trx_addons_meta_box_register(TRX_ADDONS_CPT_LAYOUTS_PT, array(
			"layout_type" => array(
				"title" => __('Type',  'trx_addons'),
				"desc" => __("Type of this layout", 'trx_addons'),
				"std" => 'custom',
				"options" => trx_addons_get_list_layout_types(),
				"type" => "select"
			),
			"margin" => array(
				"title" => __('Margin to content',  'trx_addons'),
				"desc" => __("Specify margin between this layout and page content to override theme's value", 'trx_addons'),
				"dependency" => array(
					"layout_type" => array('header', 'footer')
				),
				"std" => '',
				"type" => "text"
			),
			"columns_allowed" => array(
				"title" => __('Columns allowed',  'trx_addons'),
				"desc" => __("Comma separated (Min,Max) columns number or only Max columns number for this blog type (from 1 to 6)", 'trx_addons'),
				"dependency" => array(
					"layout_type" => array('blog')
				),
				"std" => '4',
				"type" => "text"
			),
			"scripts_required" => array(
				"title" => __('Requires loading scripts',  'trx_addons'),
				"desc" => __("This layout requires loading scripts to display correctly", 'trx_addons'),
				"dependency" => array(
					"layout_type" => array('blog')
				),
				"std" => 'none',
				"options" => apply_filters( 'trx_addons_filter_layout_scripts_required', array(
					'none' => __('Not require', 'trx_addons'),
					'masonry' => __('Masonry', 'trx_addons')
					)
				),
				"type" => "select"
			)
		));

		// Register taxonomies and post types
		// Taxonomy first, because it can using the combined rewrite rule (contains the slug of the post type)
		register_taxonomy(
			TRX_ADDONS_CPT_LAYOUTS_TAXONOMY,
			TRX_ADDONS_CPT_LAYOUTS_PT,
			apply_filters('trx_addons_filter_register_taxonomy',
				array(
					'post_type' 		=> TRX_ADDONS_CPT_LAYOUTS_PT,
					'hierarchical'      => true,
					'labels'            => array(
						'name'              => esc_html__( 'Layouts Group', 'trx_addons' ),
						'singular_name'     => esc_html__( 'Group', 'trx_addons' ),
						'search_items'      => esc_html__( 'Search Groups', 'trx_addons' ),
						'all_items'         => esc_html__( 'All Groups', 'trx_addons' ),
						'parent_item'       => esc_html__( 'Parent Group', 'trx_addons' ),
						'parent_item_colon' => esc_html__( 'Parent Group:', 'trx_addons' ),
						'edit_item'         => esc_html__( 'Edit Group', 'trx_addons' ),
						'update_item'       => esc_html__( 'Update Group', 'trx_addons' ),
						'add_new_item'      => esc_html__( 'Add New Group', 'trx_addons' ),
						'new_item_name'     => esc_html__( 'New Group Name', 'trx_addons' ),
						'menu_name'         => esc_html__( 'Layout Group', 'trx_addons' ),
					),
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'rewrite'           => array(
												'slug'         => trx_addons_cpt_param('layouts', 'taxonomy_slug'),
												'with_front'   => false,
												'hierarchical' => true
											)
				),
				TRX_ADDONS_CPT_LAYOUTS_PT,
				TRX_ADDONS_CPT_LAYOUTS_TAXONOMY
			)
		);

		register_post_type(
			TRX_ADDONS_CPT_LAYOUTS_PT,
			apply_filters('trx_addons_filter_register_post_type',
				array(
					'label'               => esc_html__( 'Layout', 'trx_addons' ),
					'description'         => esc_html__( 'Layout Description', 'trx_addons' ),
					'labels'              => array(
						'name'                => esc_html__( 'Layouts', 'trx_addons' ),
						'singular_name'       => esc_html__( 'Layout', 'trx_addons' ),
						'menu_name'           => esc_html__( 'Layouts', 'trx_addons' ),
						'parent_item_colon'   => esc_html__( 'Parent Item:', 'trx_addons' ),
						'all_items'           => esc_html__( 'All Layouts', 'trx_addons' ),
						'view_item'           => esc_html__( 'View Layout', 'trx_addons' ),
						'add_new_item'        => esc_html__( 'Add New Layout', 'trx_addons' ),
						'add_new'             => esc_html__( 'Add New', 'trx_addons' ),
						'edit_item'           => esc_html__( 'Edit Layout', 'trx_addons' ),
						'update_item'         => esc_html__( 'Update Layout', 'trx_addons' ),
						'search_items'        => esc_html__( 'Search Layout', 'trx_addons' ),
						'not_found'           => esc_html__( 'Not found', 'trx_addons' ),
						'not_found_in_trash'  => esc_html__( 'Not found in Trash', 'trx_addons' ),
					),
					'taxonomies'          => array(TRX_ADDONS_CPT_LAYOUTS_TAXONOMY),
					'supports'            => trx_addons_cpt_param('layouts', 'supports'),
					'public'              => true,
//					'publicly_queryable'  => false,
					'hierarchical'        => false,
					'has_archive'         => false,
					'can_export'          => true,
					'show_in_admin_bar'   => true,
					'show_ui'             => true,
					'show_in_menu'        => true,
					'exclude_from_search' => true,
					// From WordPress 5.3 'menu_position' must be only integer or null (default)!
					// 'menu_position'       => '52.0',
					'menu_icon'			  => 'dashicons-editor-kitchensink',
					'capability_type'     => 'post',
					'rewrite'             => array(
												'slug'         => trx_addons_cpt_param('layouts', 'post_type_slug'),
												'with_front'   => false,
												'hierarchical' => false
												)
				),
				TRX_ADDONS_CPT_LAYOUTS_PT
			)
		);

		// Create theme specific layouts on first load
		if ( is_admin() && (int) get_option( 'trx_addons_cpt_layouts_created', 0 ) != 1 ) {
			trx_addons_cpt_layouts_create( true );
			update_option( 'trx_addons_cpt_layouts_created', 1 );
		}
	}
}


// Allow Gutenberg as main editor for this post type
if ( ! function_exists( 'trx_addons_cpt_layouts_add_pt_to_gutenberg' ) ) {
	add_filter( 'trx_addons_filter_add_pt_to_gutenberg', 'trx_addons_cpt_layouts_add_pt_to_gutenberg', 10, 2 );
	function trx_addons_cpt_layouts_add_pt_to_gutenberg( $allow, $post_type ) {
		return $allow || $post_type == TRX_ADDONS_CPT_LAYOUTS_PT;
	}
}

// Allow Gutenberg as main editor for taxonomies
if ( ! function_exists( 'trx_addons_cpt_layouts_add_taxonomy_to_gutenberg' ) ) {
	add_filter( 'trx_addons_filter_add_taxonomy_to_gutenberg', 'trx_addons_cpt_layouts_add_taxonomy_to_gutenberg', 10, 2 );
	function trx_addons_cpt_layouts_add_taxonomy_to_gutenberg( $allow, $tax ) {
		return $allow || in_array( $tax, array( TRX_ADDONS_CPT_LAYOUTS_TAXONOMY ) );
	}
}

/* ------------------- Old way - moved to the cpt.php now ---------------------
// Add 'Layouts' parameters in the ThemeREX Addons Options
if (!function_exists('trx_addons_cpt_layouts_options')) {
	add_filter( 'trx_addons_filter_options', 'trx_addons_cpt_layouts_options');
	function trx_addons_cpt_layouts_options($options) {
		trx_addons_array_insert_after($options, 'theme_specific_section', trx_addons_cpt_layouts_get_list_options());
		return $options;
	}
}

// Return parameters list for plugin's options
if (!function_exists('trx_addons_cpt_layouts_get_list_options')) {
	function trx_addons_cpt_layouts_get_list_options($add_parameters=array()) {
		return apply_filters('trx_addons_cpt_list_options', array(
			// Layouts settings
			'layouts_info' => array(
				"title" => esc_html__('Custom Layouts', 'trx_addons'),
				"desc" => wp_kses_data( __('Create theme-specific custom layouts (headers, footers, etc.)', 'trx_addons') ),
				"type" => "info"
			),
			'layouts_create' => array(
				"title" => esc_html__('Create Layouts', 'trx_addons'),
				"desc" => wp_kses_data( __('Press the button above to create a set of default theme layouts. Attention! If a post with the same name already exists, it is skipped!', 'trx_addons') ),
				"std" => 'trx_addons_cpt_layouts_create',
				"type" => "button"
			)
		), 'layouts');
	}
}
------------------- /Old way --------------------- */

// Callback for the 'Create Layouts' button
if ( !function_exists( 'trx_addons_callback_ajax_trx_addons_cpt_layouts_create' ) ) {
	add_action('wp_ajax_trx_addons_cpt_layouts_create', 'trx_addons_callback_ajax_trx_addons_cpt_layouts_create');
	function trx_addons_callback_ajax_trx_addons_cpt_layouts_create() {
		trx_addons_verify_nonce();

		$response = array(
			'error' => '',
			'success' => esc_html__('Custom Layouts created successfully!', 'trx_addons')
		);
		
		trx_addons_cpt_layouts_create( true );
		
		trx_addons_ajax_response( $response );
	}
}

// Create theme-specific layouts
if (!function_exists('trx_addons_cpt_layouts_create')) {
	function trx_addons_cpt_layouts_create( $check = true ) {
		$layouts = apply_filters( 'trx_addons_filter_default_layouts', array() );
		if ( count( $layouts ) > 0 ) {
			// Create 'layouts' posts
			foreach( $layouts as $slug => $layout ) {
				$args = array(
					'post_title' => $layout['name'],
					'post_content' => $layout['template'],
					'post_type' => TRX_ADDONS_CPT_LAYOUTS_PT,
					'post_status' => 'publish'
				);
				$exists = false;
				if ( $check ) {
					$post = trx_addons_get_post_by_title( $layout['name'], TRX_ADDONS_CPT_LAYOUTS_PT );
					if ( $post !== null && is_object( $post ) && ! empty( $post->ID ) ) {
						$args[ 'ID' ] = $post->ID;	// Update existing layout instead insert a new layout
						$exists = true;
					}
				}
				if ( ! $exists || apply_filters( 'trx_addons_filter_allow_overwrite_layouts', false ) ) {
					$post_id = wp_insert_post( $args );
					if ( ! is_wp_error( $post_id ) ) {
						if ( ! empty( $layout['meta'] ) && is_array( $layout['meta'] ) ) {
							foreach ( $layout['meta'] as $k => $v ) {
								$v = trx_addons_unserialize( $v );
								$v = $k == 'trx_addons_options' 
									? apply_filters( 'trx_addons_filter_save_post_options', $v, $post_id, TRX_ADDONS_CPT_LAYOUTS_PT )
									: apply_filters( 'trx_addons_filter_create_layout_post_meta', $v, $k, $slug, $post_id );	// wp_slash is removed
								update_post_meta( $post_id, $k, $v );
							}
						}
					}
				}
				do_action( 'trx_addons_action_create_layout', $slug, $layout, $args, ! empty( $args[ 'ID' ] ) ? $args[ 'ID' ] : $post_id, $exists );
			}
			do_action( 'trx_addons_action_create_layouts', $layouts );
		}
	}
}

// Remove slashes before "'"
if ( ! function_exists( 'trx_addons_cpt_layouts_create_layout_post_meta_unslash_quotes' ) ) {
	add_filter( 'trx_addons_filter_create_layout_post_meta', 'trx_addons_cpt_layouts_create_layout_post_meta_unslash_quotes', 10, 4 );
	function trx_addons_cpt_layouts_create_layout_post_meta_unslash_quotes( $meta_val, $meta_key, $layout_slug, $new_id ) {
		if ( is_string( $meta_val ) && substr( $meta_val, 0, 2 ) == '[{' && substr( $meta_val, -2 ) == '}]' ) {
			$meta_val = wp_slash( str_replace( "\\'", "'", $meta_val ) );
		}
		return $meta_val;
	}
}

// Replace site address in urls
if ( ! function_exists( 'trx_addons_cpt_layouts_create_layout_post_meta_replace_site_url' ) ) {
	add_filter( 'trx_addons_filter_create_layout_post_meta', 'trx_addons_cpt_layouts_create_layout_post_meta_replace_site_url', 10, 4 );
	function trx_addons_cpt_layouts_create_layout_post_meta_replace_site_url( $meta_val, $meta_key, $layout_slug, $new_id ) {
		if ( is_string( $meta_val ) && substr( $meta_val, 0, 2 ) == '[{' && substr( $meta_val, -2 ) == '}]' ) {
			$meta_val = preg_replace( '#(http[s]?[:])?\\\\\\\\/\\\\\\\\/[a-zA-Z0-9_\\.\\-]+#', str_replace( '/', '\\\\\\\\/', get_home_url() ), $meta_val );
		}
		return $meta_val;
	}
}

// Put meta box to the sidebar
if ( ! function_exists( 'trx_addons_cpt_layouts_meta_box_context' ) ) {
	add_filter( 'trx_addons_filter_add_meta_box_context', 'trx_addons_cpt_layouts_meta_box_context', 10, 2 );
	function trx_addons_cpt_layouts_meta_box_context( $context, $post_type ) {
		if ( $post_type == TRX_ADDONS_CPT_LAYOUTS_PT ) {
			$context = 'side';
		}
		return $context;
	}
}

// Save data from meta box
if ( ! function_exists( 'trx_addons_cpt_layouts_meta_box_save' ) ) {
	add_filter( 'trx_addons_filter_save_post_options', 'trx_addons_cpt_layouts_meta_box_save', 10, 3 );
	function trx_addons_cpt_layouts_meta_box_save( $options, $post_id, $post_type ) {
		if ( $post_type == TRX_ADDONS_CPT_LAYOUTS_PT && is_array( $options ) && ! empty( $options['layout_type'] ) ) {
			if ( empty( $options['layout_type'] ) ) {
				$options['layout_type'] = 'custom';
			}
			update_post_meta( $post_id, 'trx_addons_layout_type', $options['layout_type'] );
		}
		return $options;
	}
}

	
// Load required styles and scripts for the frontend
if ( ! function_exists( 'trx_addons_cpt_layouts_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_cpt_layouts_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY);
	function trx_addons_cpt_layouts_load_scripts_front() {
		if (trx_addons_exists_page_builder() && trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-cpt_layouts', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT . 'layouts/layouts.css'), array(), null );
			wp_enqueue_script( 'trx_addons-cpt_layouts', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT . 'layouts/layouts.js'), array('jquery'), null, true );
		}
	}
}


// Load responsive styles for the frontend
if ( !function_exists( 'trx_addons_cpt_layouts_load_responsive_styles' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_cpt_layouts_load_responsive_styles', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY);
	function trx_addons_cpt_layouts_load_responsive_styles() {
		if (trx_addons_exists_page_builder() && trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 
				'trx_addons-cpt_layouts-responsive', 
				trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT . 'layouts/layouts.responsive.css'), 
				array(), 
				null, 
				trx_addons_media_for_load_css_responsive( 'cpt-layouts', 'xl' )
			);
		}
	}
}

// Merge shortcode's specific styles to the single stylesheet
if ( !function_exists( 'trx_addons_cpt_layouts_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_cpt_layouts_merge_styles');
	add_filter("trx_addons_filter_merge_styles_layouts", 'trx_addons_cpt_layouts_merge_styles');
	function trx_addons_cpt_layouts_merge_styles($list) {
		if ( trx_addons_exists_page_builder() ) {
			$list[ TRX_ADDONS_PLUGIN_CPT . 'layouts/layouts.css' ] = true;
		}
		return $list;
	}
}

// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_cpt_layouts_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_cpt_layouts_merge_styles_responsive');
	add_filter("trx_addons_filter_merge_styles_responsive_layouts", 'trx_addons_cpt_layouts_merge_styles_responsive');
	function trx_addons_cpt_layouts_merge_styles_responsive($list) {
		if ( trx_addons_exists_page_builder() ) {
			$list[ TRX_ADDONS_PLUGIN_CPT . 'layouts/layouts.responsive.css' ] = true;
		}
		return $list;
	}
}

	
// Merge shortcode's specific scripts to the single file
if ( !function_exists( 'trx_addons_cpt_layouts_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_cpt_layouts_merge_scripts');
	function trx_addons_cpt_layouts_merge_scripts($list) {
		if ( trx_addons_exists_page_builder() ) {
			$list[ TRX_ADDONS_PLUGIN_CPT . 'layouts/layouts.js' ] = true;
		}
		return $list;
	}
}



// Merge all separate layouts styles to the single file to use it in the theme when plugin is inactive
if ( !function_exists( 'trx_addons_cpt_layouts_merge_styles_for_export' ) ) {
	add_action( 'trx_addons_action_save_options', 'trx_addons_cpt_layouts_merge_styles_for_export', 20 );
	function trx_addons_cpt_layouts_merge_styles_for_export() {
		// Merge styles
		trx_addons_merge_css( TRX_ADDONS_PLUGIN_CPT . 'layouts/export/layouts.css', array_keys( apply_filters( 'trx_addons_filter_merge_styles_layouts', array() ) ), false );
		// Merge responsive styles
		trx_addons_merge_css( TRX_ADDONS_PLUGIN_CPT . 'layouts/export/layouts.responsive.css', array_keys( apply_filters( 'trx_addons_filter_merge_styles_responsive_layouts', array() ) ), true );
	}
}

// Check if layouts components are showed or set new state
if (!function_exists('trx_addons_sc_layouts_showed')) {
	function trx_addons_sc_layouts_showed($name, $val=null) {
		global $TRX_ADDONS_STORAGE;
		if ( $val !== null ) {
			if ( ! isset( $TRX_ADDONS_STORAGE['sc_layouts_components'] ) ) {
				$TRX_ADDONS_STORAGE['sc_layouts_components'] = array();
			}
			$TRX_ADDONS_STORAGE['sc_layouts_components'][$name] = $val;
		} else
			return isset( $TRX_ADDONS_STORAGE['sc_layouts_components'][$name] )
					? $TRX_ADDONS_STORAGE['sc_layouts_components'][$name]
					: false;
	}
}



// Admin utils
// -----------------------------------------------------------------

// Add query vars to filter posts
if (!function_exists('trx_addons_cpt_layouts_pre_get_posts')) {
	add_action( 'pre_get_posts', 'trx_addons_cpt_layouts_pre_get_posts' );
	function trx_addons_cpt_layouts_pre_get_posts($query) {
		if (!$query->is_main_query() || !is_admin()) return;
		if ($query->get('post_type') == TRX_ADDONS_CPT_LAYOUTS_PT) {
			$layout_type = trx_addons_get_value_gp('layout_type');
			if (!empty($layout_type)) {
				$query->set('meta_key', 'trx_addons_layout_type');
				$query->set('meta_value', $layout_type);
			}
		}
	}
}

// Create additional column in the posts list
if (!function_exists('trx_addons_cpt_layouts_add_custom_column')) {
	add_filter('manage_edit-'.trx_addons_cpt_param('layouts', 'post_type').'_columns',	'trx_addons_cpt_layouts_add_custom_column', 9);
	function trx_addons_cpt_layouts_add_custom_column( $columns ){
		if (is_array($columns) && count($columns)>0) {
			$new_columns = array();
			$tmp = '';
			foreach($columns as $k=>$v) {
				if ($k == 'author') {
					$tmp = $v;
					continue;
				} else if ($k == 'date') {
					$new_columns['author'] = $tmp;
				}
				$new_columns[$k] = $v;
				if ($k == 'title') {
					$new_columns['cpt_layouts_image'] = esc_html__('Shortcode / Preview', 'trx_addons');
					$new_columns['cpt_layouts_type'] = esc_html__('Type', 'trx_addons');
				}
			}
			$columns = $new_columns;
		}
		return $columns;
	}
}

// Fill image column in the posts list
if (!function_exists('trx_addons_cpt_layouts_fill_custom_column')) {
	add_action('manage_'.trx_addons_cpt_param('layouts', 'post_type').'_posts_custom_column',	'trx_addons_cpt_layouts_fill_custom_column', 9, 2);
	function trx_addons_cpt_layouts_fill_custom_column($column_name='', $post_id=0) {
		if ( $column_name == 'cpt_layouts_image' ) {
			?><div class="trx_addons_cpt_column_shortcode">[trx_sc_layouts layout="<?php echo esc_html( $post_id ); ?>"]</div><?php
			$image = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), trx_addons_get_thumb_size('masonry') );
			if ( ! empty($image[0]) ) {
				?><img class="trx_addons_cpt_column_image_preview trx_addons_cpt_layouts_image_preview" src="<?php echo esc_url($image[0]); ?>" alt="<?php esc_attr_e("Layout's preview", 'trx_addons'); ?>"<?php if (!empty($image[1])) echo ' width="'.intval($image[1]).'"'; ?><?php if (!empty($image[2])) echo ' height="'.intval($image[2]).'"'; ?>><?php
			}
		} else if ( $column_name == 'cpt_layouts_type' ) {
			$type = get_post_meta( $post_id, 'trx_addons_layout_type', true );
			if (!empty($type)) {
				?><div class="trx_addons_meta_row">
					<span class="trx_addons_meta_label"><?php echo esc_html(trx_addons_get_option_title(TRX_ADDONS_CPT_LAYOUTS_PT, 'layout_type', $type)); ?></span>
				</div><?php
			}
		}
	}
}

// Show <select> with layouts categories and with layout types in the admin filters area
if (!function_exists('trx_addons_cpt_layouts_admin_filters')) {
	add_action( 'restrict_manage_posts', 'trx_addons_cpt_layouts_admin_filters' );
	function trx_addons_cpt_layouts_admin_filters() {
		if ( get_query_var('post_type') != TRX_ADDONS_CPT_LAYOUTS_PT) return;
		// Layout's types
		$layout_type = trx_addons_get_value_gp('layout_type');
		$options = trx_addons_get_option_title(TRX_ADDONS_CPT_LAYOUTS_PT, 'layout_type');
		$list = '';
		if (is_array($options) && count($options) > 0) {
			$list .= '<select name="layout_type" id="layout_type" class="postform">';
			$list .= '<option value="">'.esc_html__('Any type', 'trx_addons').'</option>';
			foreach ($options as $id=>$title) {
				$list .= '<option value="'.esc_attr($id).'"'.($layout_type==$id ? ' selected="selected"' : '').'>'.esc_html($title).'</option>';
			}
			$list .=  "</select>";
		}
		trx_addons_show_layout($list);
		// Layout's categories
		trx_addons_admin_filters(TRX_ADDONS_CPT_LAYOUTS_PT, TRX_ADDONS_CPT_LAYOUTS_TAXONOMY);
	}
}
  
// Clear terms cache on the taxonomy save
if (!function_exists('trx_addons_cpt_layouts_admin_clear_cache')) {
	add_action( 'edited_'.TRX_ADDONS_CPT_LAYOUTS_TAXONOMY, 'trx_addons_cpt_layouts_admin_clear_cache', 10, 1 );
	add_action( 'delete_'.TRX_ADDONS_CPT_LAYOUTS_TAXONOMY, 'trx_addons_cpt_layouts_admin_clear_cache', 10, 1 );
	add_action( 'created_'.TRX_ADDONS_CPT_LAYOUTS_TAXONOMY, 'trx_addons_cpt_layouts_admin_clear_cache', 10, 1 );
	function trx_addons_cpt_layouts_admin_clear_cache( $term_id=0 ) {  
		trx_addons_admin_clear_cache_terms(TRX_ADDONS_CPT_LAYOUTS_TAXONOMY);
	}
}


// One-click import support
//------------------------------------------------------------------------

// Export custom layouts
if ( !function_exists( 'trx_addons_cpt_layouts_importer_export' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_importer_export',	'trx_addons_cpt_layouts_importer_export', 10, 1 );
	function trx_addons_cpt_layouts_importer_export($importer) {
		$posts = get_posts( array(
								'post_type' => TRX_ADDONS_CPT_LAYOUTS_PT,
								'post_status' => 'publish',
								'posts_per_page' => -1,
								'ignore_sticky_posts' => true,
								'orderby'	=> 'ID',
								'order'		=> 'ASC'
								)
							);
		$output = '';
		if (is_array($posts) && count($posts) > 0) {
			$output = "<?php"
						. "\n//" . esc_html__('Custom Layouts', 'trx_addons')
						. "\n\$layouts = array(";
			$counter = 0;
			foreach ($posts as $post) {
				$trx_addons_layout_type = get_post_meta( $post->ID, 'trx_addons_layout_type', true );
				$post_content = str_replace(array("\x0D\x0A", "©", " "), array("\x0A", "&copy;", "&nbsp;"), $post->post_content);
				// Remove generated content if it not start with shortcode
				if (substr($post_content, 0, 1) != '[' && substr($post_content, 0, 4) != '<p>[')
					$post_content = '';
				$meta = apply_filters('trx_addons_filter_cpt_layouts_export_meta', array(
					'trx_addons_options' => serialize(get_post_meta( $post->ID, 'trx_addons_options', true )),
					'trx_addons_layout_type' => $trx_addons_layout_type
				), $post);
				$output .= ($counter++ ? ',' : '') 
						. "\n\t\t'" . trim($trx_addons_layout_type) . '_' . $post->ID . "' => array("
						. "\n\t\t\t\t'name' => " . '"' . addslashes($post->post_title) . '",'
						. "\n\t\t\t\t'template' => " . '"' . addslashes($post_content) . '",'
						. "\n\t\t\t\t'meta' => array(";
				foreach ($meta as $k=>$v) {
					$output .= "\n\t\t\t\t\t\t'{$k}' => " . '"' . addslashes($v) . '",';
				}
				$output .= "\n\t\t\t\t\t\t)"
						.  "\n\t\t\t\t)";
			}
			$output .= "\n\t\t);"
						. "\n?>";
		}
		trx_addons_fpc($importer->export_file_dir('layouts.txt'), $output);
	}
}

// Display exported data in the fields
if ( !function_exists( 'trx_addons_cpt_layouts_importer_export_fields' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_importer_export_fields',	'trx_addons_cpt_layouts_importer_export_fields', 11, 1 );
	function trx_addons_cpt_layouts_importer_export_fields($importer) {
		$importer->show_exporter_fields(array(
			'slug'	=> 'layouts',
			'title' => esc_html__('Custom Layouts', 'trx_addons'),
			'download' => 'trx_addons-layouts.php'
			)
		);
	}
}


// Shortcodes utilities
// -----------------------------------------------------------------

// Show layout with specified ID
if ( !function_exists( 'trx_addons_cpt_layouts_show_layout' ) ) {
	add_action( 'trx_addons_action_show_layout', 'trx_addons_cpt_layouts_show_layout', 10, 3 );
	function trx_addons_cpt_layouts_show_layout($layout_id, $post_id=0, $echo=true) {
		// Prevent recursion when show layouts
		static $busy = array();
		if ( ! empty( $busy[ $layout_id ] ) ) return '!';
		$busy[ $layout_id ] = true;
		// Generate layout
		$content = '';
		if ( (int) $layout_id > 0 ) {
			// Load layouts from the cache
			$use_cache = trx_addons_is_on( trx_addons_get_option( 'layouts_cache' ) )
							//&& trx_addons_is_off( trx_addons_get_option( 'debug_mode' ) )
							&& ! is_admin()
							&& ! wp_doing_ajax()
							&& ! trx_addons_is_preview();

			// Check layout's type
			if ( $use_cache ) {
				$use_cache = false;
				$types_to_cache = trx_addons_array_get_keys_by_value( trx_addons_get_option( 'layouts_cache_types' ) );
				if ( count( $types_to_cache ) > 0 ) {
					$layout_meta = get_post_meta( $layout_id, 'trx_addons_options', true );
					if ( ! empty( $layout_meta['layout_type'] )
						&& in_array( $layout_meta['layout_type'], $types_to_cache )
						&& apply_filters( 'trx_addons_filter_layout_cache_by_type', true, compact( 'layout_id', 'post_id', 'layout_meta' ) )
					) {
						$use_cache = true;
					}
				}
			}
			// Cache only on the most visited pages (by default, caching only 1/2 of the most visited pages)
			if ( $use_cache && trx_addons_is_on( trx_addons_get_option( 'layouts_cache_popular' ) ) && function_exists( 'trx_addons_statistics_get_info' ) ) {
				$stats = trx_addons_statistics_get_info();
				if ( is_array( $stats ) && ! empty( $stats['total'] ) ) {
					$use_cache = apply_filters( 'trx_addons_filter_layout_cache_popular', $stats['index'] < $stats['total'] / 2, $stats );
				}
			}
			// Use cached layout
			if ( $use_cache ) {
				// Store layouts for each post and URL separately
				$url = trx_addons_get_current_url();
				$url_hash = md5( $url );
				$cache_key = sprintf('%1$s_%2$s_%3$s', $layout_id, $post_id, $url_hash );
				$cache = trx_addons_cache_load( $cache_key );
				if ( ! empty( $cache['layout'] ) ) {
					$content = $cache['layout'];
					if ( ! empty( $cache['css']) ) {
						trx_addons_add_inline_css( $cache['css'] );
					}
					if ( ! empty( $cache['html']) ) {
						trx_addons_add_inline_html( $cache['html'] );
					}
				}
			}
			// Create layout
			if ( empty( $content ) ) {
				do_action( 'trx_addons_action_before_show_layout', $layout_id );
				$layout = get_post($layout_id);
				if ( ! empty($layout) ) {
					// Remove false and uncomment rest of the row to setup post data from this layout
					// Attention! This damage output of the blog item parts
					$from_content = false;	//trx_addons_sc_stack_check('trx_sc_layouts');
					if ( $from_content ) {
						global $post;
						$post = $layout;
						setup_postdata($layout);
					}
					trx_addons_sc_stack_push('show_layout');
					// Save inline css and html
					$inline_css = $use_cache ? trx_addons_get_inline_css() : '';
					$inline_html = $use_cache ? trx_addons_get_inline_html() : '';
					// Allow any Page Builder substitute content with its layouts
					$content = apply_filters('trx_addons_filter_sc_layout_content', $layout->post_content, $layout->ID, $use_cache );
					// If content unchanged - filter it with 'the_content'
					if ($content == $layout->post_content) {
						// Old way
						//$content = apply_filters('the_content', $content);
						// New way: uncomment next line and comment prev line
						//          if any problem with js calls getComputedStyle() in mediaelement are occurs
						//          on pages without Elementor's layouts (not built in Elementor, but with active Elementor plugin)
						$content = trx_addons_filter_post_content( $content );
					}
					// Replace macros in the content
					$content = apply_filters('trx_addons_filter_sc_layout_prepare_macros', $content, $layout->ID );
					// Save content to cache
					if ( $use_cache ) {
						trx_addons_cache_save( $cache_key, array(
							'layout' => $content,
							'css' => ! empty( $inline_css )
										? str_replace( $inline_css, '', trx_addons_get_inline_css() )
										: trx_addons_get_inline_css(),
							'html' => ! empty( $inline_html )
										? str_replace( $inline_html, '', trx_addons_get_inline_html() )
										: trx_addons_get_inline_html()
						) );
					}
					trx_addons_sc_stack_pop();
					// Restore postdata
					if ($from_content) {
						wp_reset_postdata();
					}
				}
				do_action( 'trx_addons_action_after_show_layout', $layout_id );
			} else {
				do_action( 'trx_addons_action_show_layout_from_cache', $content,  compact( 'layout_id', 'post_id', 'echo' ) );
			}
			if ( $echo && ! empty( $content ) ) {
				// Display content
				trx_addons_show_layout( $content );
			}
		}
		// Mark layout free
		$busy[ $layout_id ] = false;
		return $content;
	}
}


// Replace macros in the content
if ( !function_exists( 'trx_addons_cpt_layouts_prepare_macros' ) ) {
	add_filter('trx_addons_filter_sc_layout_prepare_macros', 'trx_addons_cpt_layouts_prepare_macros');
	function trx_addons_cpt_layouts_prepare_macros($content, $id=0) {
		return str_replace( array( '{{Y}}', '{Y}' ), date( 'Y' ), $content );
	}
}


// Wrap shortcode's output with .sc_layouts_item if shortcode inside custom layout
if ( !function_exists( 'trx_addons_cpt_layouts_sc_wrap' ) ) {
	add_filter( 'trx_addons_sc_output', 'trx_addons_cpt_layouts_sc_wrap', 1000, 4 );
	function trx_addons_cpt_layouts_sc_wrap($output, $sc, $atts, $content) {
		$need = ! empty($output) 
				&& ( trx_addons_sc_stack_check('show_layout')					// Wrap shortcodes in the headers and footers
					|| trx_addons_is_singular( TRX_ADDONS_CPT_LAYOUTS_PT )		// or if it's a preview mode for layout
				)
				&& ! trx_addons_sc_stack_check('trx_sc_layouts') 				// Don't wrap shortcodes inside content
				&& ! in_array($sc, array('trx_sc_layouts', 'trx_sc_content'));	// Don't wrap specified shortcodes anywhere
		$tag = $need ? substr($output, 0, strpos($output, '>'))	: '';
		return ! empty($tag)
					? '<div class="sc_layouts_item'
						. (strpos($tag, 'sc_layouts_menu_mobile_button')!==false && strpos($tag, 'without_menu')!==false 
							? ' sc_layouts_item_menu_mobile_button' 
							: '')
						. (strpos($tag, 'hide_on_mobile')!==false && strpos($output, 'sc_layouts_menu_mobile_button')===false 
							? ' sc_layouts_hide_on_mobile' 
							: '')
						. (strpos($tag, 'hide_on_tablet')!==false && strpos($output, 'sc_layouts_menu_mobile_button')===false 
							? ' sc_layouts_hide_on_tablet' 
							: '')
						. (strpos($tag, 'hide_on_notebook')!==false && strpos($output, 'sc_layouts_menu_mobile_button')===false 
							? ' sc_layouts_hide_on_notebook' 
							: '')
						. (strpos($tag, 'hide_on_desktop')!==false && strpos($output, 'sc_layouts_menu_mobile_button')===false 
							? ' sc_layouts_hide_on_desktop' 
							: '')
						. (strpos($tag, 'hide_on_wide')!==false && strpos($output, 'sc_layouts_menu_mobile_button')===false 
							? ' sc_layouts_hide_on_wide' 
							: '')
						. (strpos($tag, 'hide_on_frontpage')!==false && strpos($output, 'sc_layouts_menu_mobile_button')===false 
							? ' sc_layouts_hide_on_frontpage' 
							: '')
						. (strpos($tag, 'hide_on_singular')!==false && strpos($output, 'sc_layouts_menu_mobile_button')===false 
							? ' sc_layouts_hide_on_singular' 
							: '')
						. (strpos($tag, 'hide_on_other')!==false && strpos($output, 'sc_layouts_menu_mobile_button')===false 
							? ' sc_layouts_hide_on_other' 
							: '')
						. '">'
							. trim($output)
						. '</div>'
					: $output;
	}
}


// Add params to the ThemeREX Addons shortcode's atts
if ( !function_exists( 'trx_addons_cpt_layouts_sc_atts' ) ) {
	add_filter( 'trx_addons_sc_atts', 'trx_addons_cpt_layouts_sc_atts', 10, 2);
	function trx_addons_cpt_layouts_sc_atts($atts, $sc) {
		
		// Param 'hide_on_xxx'
		if (in_array($sc, array('trx_sc_button', 'trx_sc_socials'))) {
			$atts['hide_on_wide']  = '0';
			$atts['hide_on_desktop']  = '0';
			$atts['hide_on_notebook'] = '0';
			$atts['hide_on_tablet']   = '0';
			$atts['hide_on_mobile']   = '0';
		}
		return $atts;
	}
}

// Add params into VC shortcodes map
if ( !function_exists( 'trx_addons_cpt_layouts_sc_map' ) ) {
	add_filter( 'trx_addons_sc_map', 'trx_addons_cpt_layouts_sc_map', 10, 2);
	function trx_addons_cpt_layouts_sc_map($params, $sc) {

		// Param 'hide_on_xxx'
		if (in_array($sc, array('trx_sc_button', 'trx_sc_socials')))
			$params['params'] = array_merge($params['params'], trx_addons_vc_add_hide_param());
		return $params;
	}
}


// Add common classes to the shortcode's output
if ( !function_exists( 'trx_addons_cpt_layouts_sc_add_classes' ) ) {
	function trx_addons_cpt_layouts_sc_add_classes($args) {
		if (!empty($args['hide_on_wide']))		echo ' hide_on_wide';
		if (!empty($args['hide_on_desktop']))	echo ' hide_on_desktop';
		if (!empty($args['hide_on_notebook']))	echo ' hide_on_notebook';
		if (!empty($args['hide_on_tablet']))	echo ' hide_on_tablet';
		if (!empty($args['hide_on_mobile']))	echo ' hide_on_mobile';
		if (!empty($args['hide_on_frontpage']))	echo ' hide_on_frontpage';
		if (!empty($args['hide_on_singular']))	echo ' hide_on_singular';
		if (!empty($args['hide_on_other']))		echo ' hide_on_other';
		if (!empty($args['class']))				echo ' '.esc_attr($args['class']); 
		if (!empty($args['align']) && !trx_addons_is_inherit($args['align']))
												echo ' sc_align_'.esc_attr($args['align']);
		do_action( 'trx_addons_cpt_layouts_sc_add_classes', $args );
	}
}

// Add params into ThemeREX Addons shortcode's output
if ( !function_exists( 'trx_addons_cpt_layouts_sc_output' ) ) {
	add_filter( 'trx_addons_sc_output', 'trx_addons_cpt_layouts_sc_output', 10, 4);
	function trx_addons_cpt_layouts_sc_output($output, $sc, $atts, $content) {
		
		// Param 'hide_on_xxx'
		if (in_array($sc, array('trx_sc_button', 'trx_sc_socials'))) {
			$sc_class = str_replace('trx_', '', $sc);
			if (!empty($atts['hide_on_wide']) && !trx_addons_is_inherit($atts['hide_on_wide']))
				$output = str_replace('class="'.$sc_class.' ', 'class="'.$sc_class.' hide_on_wide ', $output);
			if (!empty($atts['hide_on_desktop']) && !trx_addons_is_inherit($atts['hide_on_desktop']))
				$output = str_replace('class="'.$sc_class.' ', 'class="'.$sc_class.' hide_on_desktop ', $output);
			if (!empty($atts['hide_on_notebook']) && !trx_addons_is_inherit($atts['hide_on_notebook']))
				$output = str_replace('class="'.$sc_class.' ', 'class="'.$sc_class.' hide_on_notebook ', $output);
			if (!empty($atts['hide_on_tablet']) && !trx_addons_is_inherit($atts['hide_on_tablet']))
				$output = str_replace('class="'.$sc_class.' ', 'class="'.$sc_class.' hide_on_tablet ', $output);
			if (!empty($atts['hide_on_mobile']) && !trx_addons_is_inherit($atts['hide_on_mobile']))
				$output = str_replace('class="'.$sc_class.' ', 'class="'.$sc_class.' hide_on_mobile ', $output);
		}
		return $output;
	}
}

// Prepare slides with layouts
if (!function_exists('trx_addons_cpt_layouts_slider_content')) {
	add_filter('trx_addons_filter_slider_content', 'trx_addons_cpt_layouts_slider_content', 10, 3);
	function trx_addons_cpt_layouts_slider_content($image, $args, $data='') {
		if (get_post_type() == TRX_ADDONS_CPT_LAYOUTS_PT) {
			$image['content'] = trx_addons_sc_layouts(array('layout' => get_the_ID()));
			$image['image'] = $image['link'] = $image['url'] = '';
		}
		return $image;
	}
}

// Check if current screen require for the shortcodes support
if (!function_exists('trx_addons_cpt_layouts_sc_required')) {
	function trx_addons_cpt_layouts_sc_required() {
		static $need = -1;
		if ($need === -1) {
			$need = true;
			$wp_action = trx_addons_get_value_gp('action');
			$vc_action = trx_addons_get_value_gp('vc_action');
			if ( is_admin()
				&& get_option('trx_addons_action')==''
				&& !in_array($wp_action, array('ajax_search', 'vc_load_template_preview'))
			) {
				$need = strpos($_SERVER['REQUEST_URI'], 'post-new.php')!==false 
						&& trx_addons_get_value_gp('post_type')==TRX_ADDONS_CPT_LAYOUTS_PT;
				if (!$need
						&& (
							($wp_action == 'editpost' && ($post_id = (int) trx_addons_get_value_gp('post_ID')) > 0)
							||
							(strpos($_SERVER['REQUEST_URI'], 'post.php')!==false && ($post_id = (int) trx_addons_get_value_gp('post')) > 0)
							||
							(($wp_action == 'vc_edit_form' || $vc_action == 'vc_inline') && ($post_id = (int) trx_addons_get_value_gp('post_id')) > 0)
							)
					) {
					$post_obj = get_post($post_id);
					$need = is_object($post_obj) && $post_obj->post_type == TRX_ADDONS_CPT_LAYOUTS_PT;
				}
			}
		}
		return $need;
	}
}

// Include shortcodes for the Layouts builder
// Attention! Use priority 7 because this file is included in the handler with priority 6
if (!function_exists('trx_addons_cpt_layouts_sc_load')) {
	add_action( 'after_setup_theme', 'trx_addons_cpt_layouts_sc_load', 7 );
	function trx_addons_cpt_layouts_sc_load() {
		static $loaded = false;
		if ($loaded!==false) return;
		$loaded = '';
		foreach (trx_addons_components_get_allowed_layouts('cpt', 'layouts', 'sc') as $sc => $title) {
			$loaded .= $sc . ',';
			if (($fdir = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . "{$sc}/{$sc}.php")) != '') {
				include_once $fdir;
			}
		}
		// Load sc 'layouts' anyway
		$sc = 'layouts';
		if (strpos($loaded, $sc.',')===false) {
			if (($fdir = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . "{$sc}/{$sc}.php")) != '') {
				include_once $fdir;
			}
		}
	}
}


// Use layouts as WordPress submenu
if ( ($fdir = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . "layouts/layouts-submenu.php")) != '') { 
	include_once $fdir;
}

// WPBakery PageBuilder support utilities
if ( trx_addons_exists_vc() && trx_addons_api_is_loaded('js_composer')
		&& ($fdir = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . "layouts/layouts-sc-vc.php")) != '') { 
	include_once $fdir;
}

// Elementor support utilities
if ( trx_addons_exists_elementor() && trx_addons_api_is_loaded('elementor')
		&& ($fdir = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . "layouts/layouts-sc-elementor.php")) != '') { 
	include_once $fdir;
}

// Elementor Pro support utilities
if ( trx_addons_exists_elementor_pro() && trx_addons_api_is_loaded('elementor')
		&& ($fdir = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . "layouts/layouts-sc-elementor-pro.php")) != '') { 
	include_once $fdir;
}

// Gutenberg support utilities
if ( trx_addons_exists_gutenberg() && trx_addons_api_is_loaded('gutenberg')
		&& ($fdir = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . "layouts/layouts-sc-gutenberg.php")) != '') { 
	include_once $fdir;
}
