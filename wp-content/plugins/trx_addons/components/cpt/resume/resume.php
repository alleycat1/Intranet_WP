<?php
/**
 * ThemeREX Addons Custom post type: Resume
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// -----------------------------------------------------------------
// -- Custom post type registration
// -----------------------------------------------------------------

// Define Custom post type and taxonomy constants
if ( ! defined('TRX_ADDONS_CPT_RESUME_PT') ) define('TRX_ADDONS_CPT_RESUME_PT', trx_addons_cpt_param('resume', 'post_type'));

// Register post type and taxonomy
if (!function_exists('trx_addons_cpt_resume_init')) {
	add_action( 'init', 'trx_addons_cpt_resume_init' );
	function trx_addons_cpt_resume_init() {
		
		global $TRX_ADDONS_STORAGE;
		
		// Add Services parameters to the Meta Box support
		trx_addons_meta_box_register(TRX_ADDONS_CPT_RESUME_PT, array(
			"subtitle" => array(
				"title" => esc_html__("Item's subtitle",  'trx_addons'),
				"desc" => wp_kses_data( __("Resume item subtitle", 'trx_addons') ),
				"std" => "",
				"type" => "text"
			),
			"type" => array(
				"title" => esc_html__("Type", 'trx_addons'),
				"desc" => wp_kses_data( __('Select the type of this resume item.', 'trx_addons') ),
				"std" => "skills",
				"options" => $TRX_ADDONS_STORAGE['cpt_resume_types'],
				"type" => "radio"
			),
			"period" => array(
				"title" => esc_html__("Date range",  'trx_addons'),
				"desc" => wp_kses_data( __("Date range for the education item or work experience", 'trx_addons') ),
				"dependency" => array(
					"type" => array("work", "education")
				),
				"std" => "",
				"type" => "text"
			),
			"skill" => array(
				"title" => esc_html__("Skill level",  'trx_addons'),
				"desc" => wp_kses_data( __("Specify the level of this skill on a scale from 1 to 100.", 'trx_addons') ),
				"dependency" => array(
					"type" => array("skills")
				),
				"std" => "",
				"type" => "text"
			),
			"icon" => array(
				"title" => esc_html__("Item's icon", 'trx_addons'),
				"desc" => wp_kses_data( __('Select an icon for this resume item', 'trx_addons') ),
				"dependency" => array(
					"type" => array("services")
				),
				"std" => '',
				"options" => array(),
				"style" => trx_addons_get_setting('icons_type'),
				"type" => "icons"
			)
		));
		
		// Register post type
		register_post_type(
			TRX_ADDONS_CPT_RESUME_PT,
			apply_filters('trx_addons_filter_register_post_type',
				array(
					'label'               => esc_html__( 'Resume', 'trx_addons' ),
					'description'         => esc_html__( 'Resume Description', 'trx_addons' ),
					'labels'              => array(
						'name'                => esc_html__( 'Resume', 'trx_addons' ),
						'singular_name'       => esc_html__( 'Resume', 'trx_addons' ),
						'menu_name'           => esc_html__( 'Resume', 'trx_addons' ),
						'parent_item_colon'   => esc_html__( 'Parent Item:', 'trx_addons' ),
						'all_items'           => esc_html__( 'All Resume Items', 'trx_addons' ),
						'view_item'           => esc_html__( 'View Resume Item', 'trx_addons' ),
						'add_new_item'        => esc_html__( 'Add New Resume Item', 'trx_addons' ),
						'add_new'             => esc_html__( 'Add New', 'trx_addons' ),
						'edit_item'           => esc_html__( 'Edit Resume Item', 'trx_addons' ),
						'update_item'         => esc_html__( 'Update Resume Item', 'trx_addons' ),
						'search_items'        => esc_html__( 'Search Resume Items', 'trx_addons' ),
						'not_found'           => esc_html__( 'Not found', 'trx_addons' ),
						'not_found_in_trash'  => esc_html__( 'Not found in Trash', 'trx_addons' ),
					),
					'supports'            => trx_addons_cpt_param('resume', 'supports'),
					'public'              => true,
					'publicly_queryable'  => false,
					'hierarchical'        => false,
					'has_archive'         => false,
					'can_export'          => true,
					'show_in_admin_bar'   => true,
					'show_ui'             => true,
					'show_in_menu'        => true,
					'exclude_from_search' => true,
					// From WordPress 5.3 'menu_position' must be only integer or null (default)!
					// 'menu_position'       => '53.4',
					'menu_icon'			  => 'dashicons-media-document',
					'capability_type'     => 'post',
					'rewrite'             => array(
												'slug'         => trx_addons_cpt_param('resume', 'post_type_slug'),
												'with_front'   => false,
												'hierarchical' => false
											)
				),
				TRX_ADDONS_CPT_RESUME_PT
			)
		);
	}
}


// Allow Gutenberg as main editor for this post type
if ( ! function_exists( 'trx_addons_cpt_resume_add_pt_to_gutenberg' ) ) {
	add_filter( 'trx_addons_filter_add_pt_to_gutenberg', 'trx_addons_cpt_resume_add_pt_to_gutenberg', 10, 2 );
	function trx_addons_cpt_resume_add_pt_to_gutenberg( $allow, $post_type ) {
		return $allow || $post_type == TRX_ADDONS_CPT_RESUME_PT;
	}
}



// Admin utils
// -----------------------------------------------------------------

// Show <select> with portfolio categories in the admin filters area
if (!function_exists('trx_addons_cpt_resume_admin_filters')) {
	add_action( 'restrict_manage_posts', 'trx_addons_cpt_resume_admin_filters' );
	function trx_addons_cpt_resume_admin_filters() {
		if (get_query_var('post_type') != TRX_ADDONS_CPT_RESUME_PT) return;

		global $TRX_ADDONS_STORAGE;
		$tax = 'cpt_resume_type';
		$types = $TRX_ADDONS_STORAGE['cpt_resume_types'];
		$list = '<select name="'.esc_attr($tax).'" id="'.esc_attr($tax).'" class="postform">'
					.  "<option value=''>" . esc_html__('All Resume types', 'trx_addons') . "</option>";
		foreach ($types as $slug=>$name) {
			$list .= '<option value='. esc_attr($slug) . (isset($_REQUEST[$tax]) && $_REQUEST[$tax] == $slug ? ' selected="selected"' : '') . '>' . esc_html($name) . '</option>';
		}
		$list .=  "</select>";
		trx_addons_show_layout($list);
	}
}

// Pre query: Filter resume items
if ( !function_exists( 'trx_addons_cv_resume_admin_pre_get_posts' ) ) {
	add_action( 'pre_get_posts', 'trx_addons_cv_resume_admin_pre_get_posts' );
	function trx_addons_cv_resume_admin_pre_get_posts($query) {
		$tax = 'cpt_resume_type';
		if (!is_admin() || !$query->is_main_query() || empty($_REQUEST[$tax])) return;
		$query->set('meta_query', array(
				array(
					'key' => 'trx_addons_options_resume_type',
					'value' => $_REQUEST[$tax],
					'compare' => '='
				   )
			)
		);
	}
}

// Create additional column
if (!function_exists('trx_addons_cv_resume_add_type_column')) {
	add_filter('manage_edit-'.TRX_ADDONS_CPT_RESUME_PT.'_columns',	'trx_addons_cv_resume_add_type_column', 9);
	function trx_addons_cv_resume_add_type_column( $columns ){
		trx_addons_array_insert_after($columns, 'title', array('resume_type' => esc_html__('Type', 'trx_addons')));
		return $columns;
	}
}

// Fill column with data
if (!function_exists('trx_addons_cv_resume_fill_type_column')) {
	add_action('manage_'.TRX_ADDONS_CPT_RESUME_PT.'_posts_custom_column',	'trx_addons_cv_resume_fill_type_column', 9, 2);
	function trx_addons_cv_resume_fill_type_column($column_name='', $post_id=0) {
		if ($column_name != 'resume_type') return;
		if (($type = get_post_meta($post_id, 'trx_addons_options_resume_type', true))!='') {
			global $TRX_ADDONS_STORAGE;
			if (!empty($TRX_ADDONS_STORAGE['cpt_resume_types'][$type])) trx_addons_show_layout($TRX_ADDONS_STORAGE['cpt_resume_types'][$type]);
		}
	}
}

// Save data from meta box to the separate option field
// Used as the sort order and the filter field
if (!function_exists('trx_addons_cv_resume_meta_box_save')) {
	add_action('save_post', 'trx_addons_cv_resume_meta_box_save');
	function trx_addons_cv_resume_meta_box_save($post_id) {

		// verify nonce
		if ( !wp_verify_nonce( trx_addons_get_value_gp('meta_box_post_nonce'), admin_url() ) )
			return $post_id;

		// check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $post_id;
		}

		$post_type = isset($_POST['meta_box_post_type']) ? $_POST['meta_box_post_type'] : $_POST['post_type'];
		if ($post_type != TRX_ADDONS_CPT_RESUME_PT) {
			return $post_id;
		}

		// check permissions
		$capability = 'post';
		$post_types = get_post_types( array( 'name' => $post_type), 'objects' );
		if (!empty($post_types) && is_array($post_types)) {
			foreach ($post_types  as $type) {
				$capability = $type->capability_type;
				break;
			}
		}
		if (!current_user_can('edit_'.($capability), $post_id)) {
			return $post_id;
		}

		// Get option value from POST
		$meta_box = trx_addons_meta_box_get(TRX_ADDONS_CPT_RESUME_PT);
		$resume_type = trx_addons_options_get_field_value('type', $meta_box['type']);
		update_post_meta($post_id, 'trx_addons_options_resume_type', $resume_type);
	}
}
