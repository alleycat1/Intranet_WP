<?php
/**
 * ThemeREX Addons Custom post type: Car's agents
 *
 * @package ThemeREX Addons
 * @since v1.6.25
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// -----------------------------------------------------------------
// -- Custom post type registration
// -----------------------------------------------------------------

// Define Custom post type and taxonomy constants for 'Agents'
if ( ! defined('TRX_ADDONS_CPT_CARS_AGENTS_PT') )
		define('TRX_ADDONS_CPT_CARS_AGENTS_PT', trx_addons_cpt_param('cars_agents', 'post_type'));
if ( ! defined('TRX_ADDONS_CPT_CARS_AGENTS_TAXONOMY') )
		define('TRX_ADDONS_CPT_CARS_AGENTS_TAXONOMY', trx_addons_cpt_param('cars_agents', 'taxonomy'));

// Register post type and taxonomy
if (!function_exists('trx_addons_cpt_cars_agents_init')) {
	add_action( 'init', 'trx_addons_cpt_cars_agents_init' );
	function trx_addons_cpt_cars_agents_init() {
		
		trx_addons_meta_box_register(TRX_ADDONS_CPT_CARS_AGENTS_PT, array(
			"description" => array(
				"title" => esc_html__("Short description", 'trx_addons'),
				"desc" => wp_kses_data( __("Brief information about this agent. Will be used on the agent's single page", 'trx_addons') ),
				"std" => "",
				"type" => "textarea"
			),
			"position" => array(
				"title" => esc_html__("Position", 'trx_addons'),
				"desc" => wp_kses_data( __("Agent's position in the company (agency)", 'trx_addons') ),
				"class" => "trx_addons_column-1_2",
				"std" => "",
				"type" => "text"
			),
			"languages" => array(
				"title" => esc_html__("Languages", 'trx_addons'),
				"desc" => wp_kses_data( __("Comma separated languages list", 'trx_addons') ),
				"class" => "trx_addons_column-1_2",
				"std" => "",
				"type" => "text"
			),
			"address" => array(
				"title" => esc_html__("Address", 'trx_addons'),
				"desc" => wp_kses_data( __("Agent's address - it will be used for invoices", 'trx_addons') ),
				"class" => "trx_addons_column-1_2",
				"std" => "",
				"type" => "text"
			),
			"phone_mobile" => array(
				"title" => esc_html__("Mobile phone", 'trx_addons'),
				"desc" => '',
				"class" => "trx_addons_column-1_2",
				"std" => "",
				"type" => "text"
			),
			"phone_office" => array(
				"title" => esc_html__("Office phone", 'trx_addons'),
				"desc" => '',
				"class" => "trx_addons_column-1_2",
				"std" => "",
				"type" => "text"
			),
			"phone_fax" => array(
				"title" => esc_html__("Fax", 'trx_addons'),
				"desc" => '',
				"class" => "trx_addons_column-1_2",
				"std" => "",
				"type" => "text"
			),
			"email" => array(
				"title" => esc_html__("E-mail", 'trx_addons'),
				"desc" => wp_kses_data( __('E-mail address', 'trx_addons') ),
				"class" => "trx_addons_column-1_2",
				"std" => "",
				"type" => "text"
			),
			"skype" => array(
				"title" => esc_html__("Skype", 'trx_addons'),
				"desc" => wp_kses_data( __("Agent's Skype handle", 'trx_addons') ),
				"class" => "trx_addons_column-1_2",
				"std" => "",
				"type" => "text"
			),
			"socials_info" => array(
				"title" => esc_html__("Social profiles", 'trx_addons'),
				"desc" => wp_kses_data( __("Select icons and provide URLs of agent's profiles in the popular social networks", 'trx_addons') ),
				"type" => "info"
			),
			'socials' => array(
				"title" => esc_html__("Socials", 'trx_addons'),
				"desc" => wp_kses_data( __("Select icons and provide URLs of agent's profiles in the popular social networks", 'trx_addons') ),
				"clone" => true,
				"std" => array(array()),
				"type" => "group",
				"fields" => array(
					'title' => array(
						"title" => esc_html__('Title', 'trx_addons'),
						"desc" => wp_kses_data( __("Social network's name. If empty - icon's name will be used", 'trx_addons') ),
						"class" => "trx_addons_column-1_3 trx_addons_new_row",
						"std" => "",
						"type" => "text"
					),
					'url' => array(
						"title" => esc_html__('URL to your profile', 'trx_addons'),
						"desc" => wp_kses_data( __("Specify URL of your profile in this network", 'trx_addons') ),
						"class" => "trx_addons_column-1_3",
						"std" => "",
						"type" => "text"
					),
					"name" => array(
						"title" => esc_html__("Icon", 'trx_addons'),
						"desc" => wp_kses_data( __('Select icon of this network', 'trx_addons') ),
						"class" => "trx_addons_column-1_3",
						"std" => "",
						"options" => array(),
						"style" => trx_addons_get_setting('socials_type'),
						"type" => "icons"
					)
				)
			)
		));
		
		// Register taxonomies and post types
		// Taxonomy first, because it can using the combined rewrite rule (contains the slug of the post type)
		register_taxonomy(
			TRX_ADDONS_CPT_CARS_AGENTS_TAXONOMY,
			TRX_ADDONS_CPT_CARS_AGENTS_PT,
			apply_filters('trx_addons_filter_register_taxonomy', 
				array(
					'post_type' 		=> TRX_ADDONS_CPT_CARS_AGENTS_PT,
					'hierarchical'      => true,
					'labels'            => array(
						'name'              => esc_html__( 'Agencies', 'trx_addons' ),
						'singular_name'     => esc_html__( 'Agency', 'trx_addons' ),
						'search_items'      => esc_html__( 'Search Agencies', 'trx_addons' ),
						'all_items'         => esc_html__( 'All Agencies', 'trx_addons' ),
						'parent_item'       => esc_html__( 'Parent Agency', 'trx_addons' ),
						'parent_item_colon' => esc_html__( 'Parent Agency:', 'trx_addons' ),
						'edit_item'         => esc_html__( 'Edit Agency', 'trx_addons' ),
						'update_item'       => esc_html__( 'Update Agency', 'trx_addons' ),
						'add_new_item'      => esc_html__( 'Add New Agency', 'trx_addons' ),
						'new_item_name'     => esc_html__( 'New Agency Name', 'trx_addons' ),
						'menu_name'         => esc_html__( 'Agencies', 'trx_addons' ),
					),
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'rewrite'           => array(
												'slug'         => trx_addons_cpt_param('cars_agents', 'taxonomy_slug'),
												'with_front'   => false,
												'hierarchical' => true
											)
				),
				TRX_ADDONS_CPT_CARS_AGENTS_PT,
				TRX_ADDONS_CPT_CARS_AGENTS_TAXONOMY
			)
		);

		register_post_type(
			TRX_ADDONS_CPT_CARS_AGENTS_PT,
			apply_filters( 'trx_addons_filter_register_post_type',
				array(
					'label'               => esc_html__( 'Car Agents', 'trx_addons' ),
					'description'         => esc_html__( 'Agent Description', 'trx_addons' ),
					'labels'              => array(
						'name'                => esc_html__( 'Car Agents', 'trx_addons' ),
						'singular_name'       => esc_html__( 'Car Agent', 'trx_addons' ),
						'menu_name'           => esc_html__( 'Car Agents', 'trx_addons' ),
						'parent_item_colon'   => esc_html__( 'Parent Item:', 'trx_addons' ),
						'all_items'           => esc_html__( 'All Agents', 'trx_addons' ),
						'view_item'           => esc_html__( 'View Agent', 'trx_addons' ),
						'add_new_item'        => esc_html__( 'Add New Agent', 'trx_addons' ),
						'add_new'             => esc_html__( 'Add New', 'trx_addons' ),
						'edit_item'           => esc_html__( 'Edit Agent', 'trx_addons' ),
						'update_item'         => esc_html__( 'Update Agent', 'trx_addons' ),
						'search_items'        => esc_html__( 'Search Agent', 'trx_addons' ),
						'not_found'           => esc_html__( 'Not found', 'trx_addons' ),
						'not_found_in_trash'  => esc_html__( 'Not found in Trash', 'trx_addons' ),
					),
					'taxonomies'          => array(TRX_ADDONS_CPT_CARS_AGENTS_TAXONOMY),
					'supports'            => trx_addons_cpt_param('cars_agents', 'supports'),
					'public'              => true,
					'hierarchical'        => false,
					'has_archive'         => true,
					'can_export'          => true,
					'show_in_admin_bar'   => true,
					'show_in_menu'        => true,
					// From WordPress 5.3 'menu_position' must be only integer or null (default)!
					// 'menu_position'       => '52.05',
					'menu_icon'			  => 'dashicons-id',
					'map_meta_cap'		  => true,
					'capability_type'     => 'cars_agent',
					'rewrite'             => array(
												'slug'         => trx_addons_cpt_param('cars_agents', 'post_type_slug'),
												'with_front'   => false,
												'hierarchical' => false
											)
				),
				TRX_ADDONS_CPT_CARS_AGENTS_PT
			)
		);
		
	}
}

/* ------------------- Old way - moved to the cpt.php now ---------------------
// Add 'Agents' parameters in the ThemeREX Addons Options
if (!function_exists('trx_addons_cpt_cars_agents_options')) {
	add_filter( 'trx_addons_filter_options', 'trx_addons_cpt_cars_agents_options');
	function trx_addons_cpt_cars_agents_options($options) {
		trx_addons_array_insert_after($options, 'cpt_section', trx_addons_cpt_cars_agents_get_list_options());
		return $options;
	}
}

// Return parameters list for plugin's options
if (!function_exists('trx_addons_cpt_cars_agents_get_list_options')) {
	function trx_addons_cpt_cars_agents_get_list_options($add_parameters=array()) {
		return apply_filters('trx_addons_cpt_list_options', array(
			'cars_agents_info' => array(
				"title" => esc_html__('Car agents', 'trx_addons'),
				"desc" => wp_kses_data( __('Settings of the car agents profile', 'trx_addons') ),
				"type" => "info"
			),
			'cars_agents_style' => array(
				"title" => esc_html__('Style of the archive', 'trx_addons'),
				"desc" => wp_kses_data( __("Style of the agents archive", 'trx_addons') ),
				"std" => 'default_2',
				"options" => apply_filters('trx_addons_filter_cpt_archive_styles', array(
					'default_1' => esc_html__('Default /1 column/', 'trx_addons'),
					'default_2' => esc_html__('Default /2 columns/', 'trx_addons'),
					'default_3' => esc_html__('Default /3 columns/', 'trx_addons')
				), TRX_ADDONS_CPT_CARS_AGENTS_PT),
				"type" => "select"
			),
			'cars_agents_list_style' => array(
				"title" => esc_html__('Style of the cars list', 'trx_addons'),
				"desc" => wp_kses_data( __("Style of the cars archive on the Agent's profile page", 'trx_addons') ),
				"std" => 'default_3',
				"options" => apply_filters('trx_addons_filter_cpt_single_styles', array(
					'default_1' => esc_html__('Default /1 column/', 'trx_addons'),
					'default_2' => esc_html__('Default /2 columns/', 'trx_addons'),
					'default_3' => esc_html__('Default /3 columns/', 'trx_addons')
				), TRX_ADDONS_CPT_CARS_AGENTS_PT),
				"type" => "select"
			)
		), 'cars_agents');
	}
}
------------------- /Old way --------------------- */


// Fill 'options' arrays when its need in the admin mode
if (!function_exists('trx_addons_cpt_cars_agents_options_get_list_choises')) {
	add_filter('trx_addons_filter_options_get_list_choises', 'trx_addons_cpt_cars_agents_options_get_list_choises', 10, 2);
	function trx_addons_cpt_cars_agents_options_get_list_choises($list, $name) {
		if ( is_array( $list ) && count( $list ) == 0 ) {
			if ($name == 'cars_agents_form') {
				$list = apply_filters('trx_addons_filter_page_contact_form',
					trx_addons_array_merge(
						array(
							'default' => esc_html__('Default', 'trx_addons')
							),
						function_exists('trx_addons_exists_cf7') && trx_addons_exists_cf7() && is_admin() && (in_array(trx_addons_get_value_gp('page'), array('trx_addons_options', 'theme_options')) || strpos($_SERVER['REQUEST_URI'], 'customize.php') !== false )
							? trx_addons_get_list_cf7()
							: array()
					), 'cars_agents'
				);
			}
		}
		return $list;
	}
}


// Return true if it's agents page
if ( !function_exists( 'trx_addons_is_cars_agents_page' ) ) {
	function trx_addons_is_cars_agents_page() {
		return defined('TRX_ADDONS_CPT_CARS_AGENTS_PT') 
					&& !is_search()
					&& (
						( trx_addons_is_single() && get_post_type() == TRX_ADDONS_CPT_CARS_AGENTS_PT )
							|| is_post_type_archive(TRX_ADDONS_CPT_CARS_AGENTS_PT)
							|| is_tax(TRX_ADDONS_CPT_CARS_AGENTS_TAXONOMY)
						);
	}
}


// Add 'Agents' to the team-compatible post types
if ( !function_exists( 'trx_addons_cars_add_agents_to_team_list' ) ) {
	add_filter( 'trx_addons_filter_get_list_team_posts_types', 'trx_addons_cars_add_agents_to_team_list' );
	function trx_addons_cars_add_agents_to_team_list( $list ) {
		return array_merge( $list, array( TRX_ADDONS_CPT_CARS_AGENTS_PT => __( 'Cars Agent', 'trx_addons' ) ) );
	}
}


// Return agent or author parameters (avatar, name, link, socials, etc.)
if ( !function_exists( 'trx_addons_cars_get_agent_data' ) ) {
	function trx_addons_cars_get_agent_data($meta) {
		$data = array(
			'image' => '',
			'image_id' => 0,
			'title' => __('Agent:', 'trx_addons'),
			'name' => '',
			'position' => '',
			'description' => '',
			'email' => '',
			'skype' => '',
			'socials' => array(),
			'address' => '',
			'phone_mobile' => '',
			'phone_office' => '',
			'phone_fax' => '',
			'posts_link' => '',
			'languages' => ''
		);
		
		// Owner
		if ($meta['agent_type']=='owner') {
			$data['title'] = __('Owner:', 'trx_addons');
			$data['name'] = $meta['owner_name'];
			$data['email'] = $meta['owner_email'];
			$data['skype'] = $meta['owner_skype'];
			$data['phone_mobile'] = $meta['owner_phone'];
			$data['position'] = __("Car's owner", 'trx_addons');

		// Agent
		} else if ($meta['agent_type']=='agent') {
			$agent_id = $meta['agent'];
			$agent_meta = get_post_meta($agent_id, 'trx_addons_options', true);
			if (is_array($agent_meta)) $data = array_merge($data, $agent_meta);
			$data['image_id'] = get_post_thumbnail_id($agent_id);
			$data['name'] = get_the_title($agent_id);
			$data['posts_link'] = get_permalink($agent_id);

		// Author
		} else {
			$user_id = get_the_author_meta('ID');
			$user_data = get_userdata($user_id);
			$data['name'] = $user_data->display_name;
			$data['description'] = get_user_meta($user_id, 'description', true);
			$data['email'] = $user_data->user_email;
			$data['posts_link'] = get_author_posts_url($user_id);
			$user_meta = trx_addons_users_get_meta($user_id);
			if (!empty($user_meta['socials'])) $data['socials'] = $user_meta['socials'];
		}
		if (empty($data['image']) && empty($data['image_id']) && !empty($data['email'])) {
			if (($avatar = get_avatar($data['email'], 512))!='')
				$data['image'] = trx_addons_get_tag_attrib($avatar, '<img>', 'src');
		}
		return $data;
	}
}

// Return agent's email, skype and socials, prepared for output
if ( !function_exists( 'trx_addons_cars_get_agent_socials' ) ) {
	function trx_addons_cars_get_agent_socials($meta) {
		$icons = array();
		$socials_type = trx_addons_get_setting('socials_type');
		if (!empty($meta['email']))
			$icons[] = array(
						'name' => $socials_type == 'images'
										? (($fdir=trx_addons_get_file_url('css/socials.png/mail.png'))!='' ? $fdir : '')
										: 'trx_addons_icon-mail',
						'title' => __('Mail to the agent', 'trx_addons'),
						'url' => sprintf('mailto:%s', antispambot($meta['email']))
						);
		if (!empty($meta['skype']))
			$icons[] = array(
						'name' => $socials_type == 'images'
										? (($fdir=trx_addons_get_file_url('css/socials.png/skype.png'))!='' ? $fdir : '')
										: 'trx_addons_icon-skype',
						'title' => __('Start conversation by Skype', 'trx_addons'),
						'url' => sprintf('skype:%s', $meta['skype'])
						);
		return !empty($meta['socials']) && is_array($meta['socials']) ? array_merge($icons, $meta['socials']) : $icons;
	}
}



// Replace standard theme templates
//-------------------------------------------------------------

// Change standard single template for agents posts
if ( !function_exists( 'trx_addons_cpt_cars_agents_single_template' ) ) {
	add_filter('single_template', 'trx_addons_cpt_cars_agents_single_template');
	function trx_addons_cpt_cars_agents_single_template($template) {
		global $post;
		if (trx_addons_is_single() && $post->post_type == TRX_ADDONS_CPT_CARS_AGENTS_PT)
			$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'cars/tpl.agents.single.php');
		return $template;
	}
}

// Change standard archive template for agents posts
if ( !function_exists( 'trx_addons_cpt_cars_agents_archive_template' ) ) {
	add_filter('archive_template',	'trx_addons_cpt_cars_agents_archive_template');
	function trx_addons_cpt_cars_agents_archive_template( $template ) {
		if ( is_post_type_archive(TRX_ADDONS_CPT_CARS_AGENTS_PT) )
			$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'cars/tpl.agents.archive.php');
		return $template;
	}	
}

// Change standard category template for agents categories (groups)
if ( !function_exists( 'trx_addons_cpt_cars_agents_taxonomy_template' ) ) {
	add_filter('taxonomy_template',	'trx_addons_cpt_cars_agents_taxonomy_template');
	function trx_addons_cpt_cars_agents_taxonomy_template( $template ) {
		if ( is_tax(TRX_ADDONS_CPT_CARS_AGENTS_TAXONOMY) )
			$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'cars/tpl.agents.archive.php');
		return $template;
	}	
}



// Admin utils
// -----------------------------------------------------------------

// Show <select> with agents categories in the admin filters area
if (!function_exists('trx_addons_cpt_cars_agents_admin_filters')) {
	add_action( 'restrict_manage_posts', 'trx_addons_cpt_cars_agents_admin_filters' );
	function trx_addons_cpt_cars_agents_admin_filters() {
		trx_addons_admin_filters(TRX_ADDONS_CPT_CARS_AGENTS_PT, TRX_ADDONS_CPT_CARS_AGENTS_TAXONOMY);
	}
}
  
// Clear terms cache on the taxonomy save
if (!function_exists('trx_addons_cpt_cars_agents_admin_clear_cache')) {
	add_action( 'edited_'.TRX_ADDONS_CPT_CARS_AGENTS_TAXONOMY, 'trx_addons_cpt_cars_agents_admin_clear_cache', 10, 1 );
	add_action( 'delete_'.TRX_ADDONS_CPT_CARS_AGENTS_TAXONOMY, 'trx_addons_cpt_cars_agents_admin_clear_cache', 10, 1 );
	add_action( 'created_'.TRX_ADDONS_CPT_CARS_AGENTS_TAXONOMY, 'trx_addons_cpt_cars_agents_admin_clear_cache', 10, 1 );
	function trx_addons_cpt_cars_agents_admin_clear_cache( $term_id=0 ) {  
		trx_addons_admin_clear_cache_terms(TRX_ADDONS_CPT_CARS_AGENTS_TAXONOMY);
	}
}


// Additional parameters to the taxonomy
//--------------------------------------------------------------------------

// Save additional parameters
if (!function_exists('trx_addons_cpt_cars_agents_taxonomy_save_custom_fields')) {
	add_action('edited_'.TRX_ADDONS_CPT_CARS_AGENTS_TAXONOMY,	'trx_addons_cpt_cars_agents_taxonomy_save_custom_fields', 10, 1 );
	add_action('created_'.TRX_ADDONS_CPT_CARS_AGENTS_TAXONOMY,	'trx_addons_cpt_cars_agents_taxonomy_save_custom_fields', 10, 1 );
	function trx_addons_cpt_cars_agents_taxonomy_save_custom_fields($term_id) {
		if (isset($_POST['trx_addons_image'])) {
			trx_addons_set_term_meta(array(
											'term_id' => $term_id,
											'key' => TRX_ADDONS_CPT_CARS_TAXONOMY_IMAGE_KEY
											),
										$_POST['trx_addons_image']
										);
		}
	}
}

// Display additional fields
if (!function_exists('trx_addons_cpt_cars_agents_taxonomy_show_custom_fields')) {
	add_action(TRX_ADDONS_CPT_CARS_AGENTS_TAXONOMY.'_edit_form_fields', 'trx_addons_cpt_cars_agents_taxonomy_show_custom_fields', 10, 1 );
	add_action(TRX_ADDONS_CPT_CARS_AGENTS_TAXONOMY.'_add_form_fields', 'trx_addons_cpt_cars_agents_taxonomy_show_custom_fields', 10, 1 );
	function trx_addons_cpt_cars_agents_taxonomy_show_custom_fields($term) {
		$term_id = !empty($term->term_id) ? $term->term_id : 0;

		// Image
		echo ((int) $term_id > 0 ? '<tr' : '<div') . ' class="form-field">'
			. ((int) $term_id > 0 ? '<th valign="top" scope="row">' : '<div>');
		?><label id="trx_addons_image_label" for="trx_addons_image"><?php esc_html_e('Image URL:', 'trx_addons'); ?></label><?php
		echo ((int) $term_id > 0 ? '</th>' : '</div>')
			. ((int) $term_id > 0 ? '<td valign="top">' : '<div>');
		$img = $term_id > 0 ? trx_addons_get_term_image( $term_id, TRX_ADDONS_CPT_CARS_AGENTS_TAXONOMY, TRX_ADDONS_CPT_CARS_TAXONOMY_IMAGE_KEY ) : '';
		?><input type="hidden" id="trx_addons_image" class="trx_addons_image_selector_field" name="trx_addons_image" value="<?php echo esc_url($img); ?>"><?php
		if (empty($img)) $img = trx_addons_get_no_image();
		trx_addons_show_layout(trx_addons_options_show_custom_field('trx_addons_image_button', array(
								'type' => 'mediamanager',
								'linked_field_id' => 'trx_addons_image'
								), $img));
		echo (int) $term_id > 0 ? '</td></tr>' : '</div></div>';
	}
}

// Create additional column in the terms list
if (!function_exists('trx_addons_cpt_cars_agents_taxonomy_add_custom_column')) {
	add_filter('manage_edit-'.TRX_ADDONS_CPT_CARS_AGENTS_TAXONOMY.'_columns',	'trx_addons_cpt_cars_agents_taxonomy_add_custom_column', 9);
	function trx_addons_cpt_cars_agents_taxonomy_add_custom_column( $columns ){
		$columns['image'] = esc_html__('Image', 'trx_addons');
		return $columns;
	}
}

// Fill additional column in the terms list
if (!function_exists('trx_addons_cpt_cars_agents_taxonomy_fill_custom_column')) {
	add_action('manage_'.TRX_ADDONS_CPT_CARS_AGENTS_TAXONOMY.'_custom_column',	'trx_addons_cpt_cars_agents_taxonomy_fill_custom_column', 9, 3);
	function trx_addons_cpt_cars_agents_taxonomy_fill_custom_column($output='', $column_name='', $term_id=0) {
		if ($column_name == 'image') {
			$img = $term_id > 0 ? trx_addons_get_term_image( $term_id, TRX_ADDONS_CPT_CARS_AGENTS_TAXONOMY, TRX_ADDONS_CPT_CARS_TAXONOMY_IMAGE_KEY ) : '';
			if (!empty($img)) {
				?><img class="trx_addons_image_selector_preview trx_addons_image_preview" src="<?php
							echo esc_url(trx_addons_add_thumb_size($img, trx_addons_get_thumb_size('masonry')));
						?>" alt="<?php esc_attr_e('Agencies logo', 'trx_addons'); ?>"><?php
			}
		}
	}
}



// User roles
// ------------------------------------------------------------------------

// Add users role and capabilities for "Cars" and "Car Agents"
if (!function_exists('trx_addons_cpt_cars_agents_add_roles_and_caps')) {
	add_action( 'trx_addons_action_add_roles_and_caps', 'trx_addons_cpt_cars_agents_add_roles_and_caps' );
	function trx_addons_cpt_cars_agents_add_roles_and_caps() {
		// Create new role with default capabilities
		add_role(
			'trx_addons_cars_agent',
			esc_html__( 'Cars Agent','booked' ),
			array(
				'read'         => true,
				'edit_posts'   => false,
				'upload_files' => true,
				'delete_posts' => false,
			)
		);
		// Add caps to roles
		trx_addons_add_capabilities(
			array( 'administrator', 'editor' ),
			array( 'cars_agent' )
		);
		trx_addons_add_capabilities(
			array( 'trx_addons_cars_agent' ),
			array( 'car', 'cars_agent' ),
			array( '_others', 'manage_%s_terms', 'edit_%s_terms', 'delete_%s_terms' )
		);
	}
}

// Allow access to admin area for "Car Agents" 
if ( ! function_exists( 'trx_addons_cpt_cars_agents_allow_admin_access' ) ) {
	add_filter( 'trx_addons_filter_allow_admin_access', 'trx_addons_cpt_cars_agents_allow_admin_access', 10, 2 );
	function trx_addons_cpt_cars_agents_allow_admin_access( $allow, $roles ) {
		if ( ! $allow && is_array( $roles ) && in_array( 'trx_addons_cars_agent', $roles ) ) {
			$allow = true;
		}
		return $allow;
	}
}

// Display only own posts for users 'cars_agent'
if ( ! function_exists( 'trx_addons_cpt_cars_agents_disallow_other_posts_for_agents' ) ) {
	add_filter( 'pre_get_posts', 'trx_addons_cpt_cars_agents_disallow_other_posts_for_agents' );
	function trx_addons_cpt_cars_agents_disallow_other_posts_for_agents( $query ) {
		global $pagenow;
		if ( ! empty( $pagenow ) && 'edit.php' == $pagenow && ! empty( $query->is_admin ) ) {// && $query->is_main_query()
			$post_type = $query->get('post_type');
			if ( in_array( $post_type, array( TRX_ADDONS_CPT_CARS_PT, TRX_ADDONS_CPT_CARS_AGENTS_PT ) ) ) {
				if ( ( $post_type == TRX_ADDONS_CPT_CARS_PT && ! current_user_can( sprintf( 'edit_others_%ss', 'car' ) ) && trx_addons_is_on( trx_addons_get_option('cars_agents_hide_others_cars') ) )
					||
					( $post_type == TRX_ADDONS_CPT_CARS_AGENTS_PT && ! current_user_can( sprintf( 'edit_others_%ss', 'cars_agent' ) ) )
				) {
					$user = wp_get_current_user();
					if ( ! empty( $user->ID ) ) {
						$query->set( 'author', $user->ID );
					}
				}
			}
		}
		return $query;
	}
}

// Add vars to the admin js
if ( ! function_exists( 'trx_addons_cpt_cars_agents_localize_admin_scripts' ) ) {
	add_filter( 'trx_addons_filter_localize_script_admin',	'trx_addons_cpt_cars_agents_localize_admin_scripts');
	function trx_addons_cpt_cars_agents_localize_admin_scripts( $vars = array() ) {
		$vars['hide_add_new_cars_agent'] = ! current_user_can( sprintf( 'edit_others_%ss', 'cars_agent' ) );
		return $vars;
	}
}

// Disable URL post-new.php?post_type=cpt_cars_agents for users 'properties_agent'
if ( ! function_exists( 'trx_addons_cpt_cars_agents_disable_post_new' ) ) {
	add_action( 'init', 'trx_addons_cpt_cars_agents_disable_post_new' );
	function trx_addons_cpt_cars_agents_disable_post_new() {
		global $pagenow, $post_type;
		if ( is_admin()
			&& ! current_user_can( sprintf( 'edit_others_%ss', 'cars_agent' ) )
			&& ! empty( $pagenow )
			&& $pagenow == 'post-new.php'
			&& trx_addons_get_value_gp('post_type') == TRX_ADDONS_CPT_CARS_AGENTS_PT
		) {
			nocache_headers();
			if ( wp_safe_redirect( admin_url( 'edit.php?post_type=' . TRX_ADDONS_CPT_CARS_AGENTS_PT ) ) ) {
				exit;
			}
		}
	}
}

// Add "Cars Agents" to the authors list
if ( ! function_exists( 'trx_addons_cpt_cars_agents_add_to_authors_list' ) ) {
//	add_filter( 'quick_edit_dropdown_authors_args',	'trx_addons_cpt_cars_agents_add_to_authors_list', 10, 2 );
	add_filter( 'wp_dropdown_users_args', 'trx_addons_cpt_cars_agents_add_to_authors_list', 10, 2 );
	function trx_addons_cpt_cars_agents_add_to_authors_list( $users_args, $params = false ) {
		global $pagenow, $post_type;
		if ( is_admin()
			&& current_user_can( sprintf( 'edit_others_%ss', 'cars_agent' ) )
			&& ! empty( $pagenow )
			&& (
				( in_array( $pagenow, array( 'edit.php', 'post-new.php' ) ) && trx_addons_get_value_gp('post_type') == TRX_ADDONS_CPT_CARS_AGENTS_PT )
				||
				( in_array( $pagenow, array( 'post.php' ) ) && trx_addons_get_value_gp('action') == 'edit' && ! empty( $post_type ) && $post_type == TRX_ADDONS_CPT_CARS_AGENTS_PT )
				)
		) {
			if ( ! isset( $users_args['role__in'] ) || ! is_array( $users_args['role__in'] ) ) {
				$users_args['role__in'] = array();
			}
			$users_args['role__in'] = array_merge(
										$users_args['role__in'],
										array( 'trx_addons_cars_agent', 'administrator', 'editor' )
									);
			$users_args['who'] = '';
		}
		return $users_args;
	}
}


// Booked Compatibility
// -----------------------------------------------------

// Add Capabilities to User Roles of 'trx_addons_cars_agent' to make it equal to 'Booked Agent'
if ( ! function_exists( 'trx_addons_cpt_cars_agents_add_role_to_booked' ) ) {
	add_filter( 'booked_user_roles', 'trx_addons_cpt_cars_agents_add_role_to_booked', 10, 1 );
	function trx_addons_cpt_cars_agents_add_role_to_booked( $roles ) {
		$roles[] = 'trx_addons_cars_agent';
		return $roles;
	}
}

// Add a role 'booked_agent' to a current_user (if it is equal to  'trx_addons_properties_agent')
if ( ! function_exists( 'trx_addons_cpt_cars_agents_add_booked_role' ) ) {
	add_action( 'init', 'trx_addons_cpt_cars_agents_add_booked_role' );
	function trx_addons_cpt_cars_agents_add_booked_role() {
		if ( is_user_logged_in() ) {
			$user = wp_get_current_user();
			if ( ! empty( $user->roles )
				&& is_array( $user->roles )
				&& in_array( 'trx_addons_cars_agent', $user->roles )
				&& ! in_array( 'booked_booking_agent', $user->roles )
			) {
				$user->add_role( 'booked_booking_agent' );
			}
		}
	}
}
