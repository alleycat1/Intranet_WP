<?php
/**
 * ThemeREX Addons Custom post type: Team
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// -----------------------------------------------------------------
// -- Custom post type registration
// -----------------------------------------------------------------

// Define Custom post type and taxonomy constants
if ( ! defined('TRX_ADDONS_CPT_TEAM_PT') ) define('TRX_ADDONS_CPT_TEAM_PT', trx_addons_cpt_param('team', 'post_type'));
if ( ! defined('TRX_ADDONS_CPT_TEAM_TAXONOMY') ) define('TRX_ADDONS_CPT_TEAM_TAXONOMY', trx_addons_cpt_param('team', 'taxonomy'));


// Register post type and taxonomy
if (!function_exists('trx_addons_cpt_team_init')) {
	add_action( 'init', 'trx_addons_cpt_team_init' );
	function trx_addons_cpt_team_init() {

		// Add Team parameters to the Meta Box support
		trx_addons_meta_box_register(TRX_ADDONS_CPT_TEAM_PT, array(
			'user' => array(
				"title" => __('Link to WordPress user',  'trx_addons'),
				"desc" => __("Select a WordPress user to display posts written by this team member", 'trx_addons'),
				"std" => '',
				"options" => array(),
				"type" => "select2"
			),
			"subtitle" => array(
				"title" => esc_html__("Position",  'trx_addons'),
				"desc" => wp_kses_data( __("Team member's position or any other text", 'trx_addons') ),
				"std" => "",
				"type" => "text"
			),
			"brief_info" => array(
				"title" => esc_html__("Brief info",  'trx_addons'),
				"desc" => wp_kses_data( __("Brief info about this team member to display on the member's single page near the avatar", 'trx_addons') ),
				"std" => "",
				"type" => "textarea"
			),
			'email' => array(
				"title" => esc_html__("E-mail",  'trx_addons'),
				"desc" => wp_kses_data( __("Team member's email", 'trx_addons') ),
				"std" => "",
				"details" => true,	// Display this field in the 'Details' area on the single page
				"type" => "email"
			),
			'phone' => array(
				"title" => esc_html__("Phone",  'trx_addons'),
				"desc" => wp_kses_data( __("Team member's phone number", 'trx_addons') ),
				"std" => "",
				"details" => true,
				"type" => "phone"
			),
			'address' => array(
				"title" => esc_html__("Address",  'trx_addons'),
				"desc" => wp_kses_data( __("Team member's post address", 'trx_addons') ),
				"std" => "",
				"details" => true,
				"type" => "text"
			),
			'socials' => array(
				"title" => esc_html__("Socials", 'trx_addons'),
				"desc" => wp_kses_data( __("Clone this field group, select an icon/image, specify social network's title and provide the URL to your profile", 'trx_addons') ),
				"clone" => true,
				"std" => array(array()),
				"type" => "group",
				"fields" => array(
					"name" => array(
						"title" => esc_html__("Icon", 'trx_addons'),
						"desc" => wp_kses_data( __('Select an icon for the network', 'trx_addons') ),
						"class" => "trx_addons_column-1_3",
						"std" => "",
						"options" => array(),
						"style" => trx_addons_get_setting('socials_type'),
						"type" => "icons"
					),
					'title' => array(
						"title" => esc_html__('Title', 'trx_addons'),
						"desc" => wp_kses_data( __("The name of the social network. If left empty, the icon's name will be used", 'trx_addons') ),
						"class" => "trx_addons_column-1_3 trx_addons_new_row",
						"std" => "",
						"type" => "text"
					),
					'url' => array(
						"title" => esc_html__('URL to your profile', 'trx_addons'),
						"desc" => wp_kses_data( __("Provide a link to the profile in the chosen network", 'trx_addons') ),
						"class" => "trx_addons_column-1_3",
						"std" => "",
						"type" => "text"
					),
				)
			),
		));

		// Register taxonomies and post types
		// Taxonomy first, because it can using the combined rewrite rule (contains the slug of the post type)
		register_taxonomy(
			TRX_ADDONS_CPT_TEAM_TAXONOMY,
			TRX_ADDONS_CPT_TEAM_PT,
			apply_filters('trx_addons_filter_register_taxonomy',
				array(
					'post_type' 		=> TRX_ADDONS_CPT_TEAM_PT,
					'hierarchical'      => true,
					'labels'            => array(
						'name'              => esc_html__( 'Team Group', 'trx_addons' ),
						'singular_name'     => esc_html__( 'Group', 'trx_addons' ),
						'search_items'      => esc_html__( 'Search Groups', 'trx_addons' ),
						'all_items'         => esc_html__( 'All Groups', 'trx_addons' ),
						'parent_item'       => esc_html__( 'Parent Group', 'trx_addons' ),
						'parent_item_colon' => esc_html__( 'Parent Group:', 'trx_addons' ),
						'edit_item'         => esc_html__( 'Edit Group', 'trx_addons' ),
						'update_item'       => esc_html__( 'Update Group', 'trx_addons' ),
						'add_new_item'      => esc_html__( 'Add New Group', 'trx_addons' ),
						'new_item_name'     => esc_html__( 'New Group Name', 'trx_addons' ),
						'menu_name'         => esc_html__( 'Team Groups', 'trx_addons' ),
					),
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'rewrite'           => array(
												'slug'         => trx_addons_cpt_param('team', 'taxonomy_slug'),
												'with_front'   => false,
												'hierarchical' => true
											)
				),
				TRX_ADDONS_CPT_TEAM_PT,
				TRX_ADDONS_CPT_TEAM_TAXONOMY
			)
		);

		register_post_type(
			TRX_ADDONS_CPT_TEAM_PT,
			apply_filters('trx_addons_filter_register_post_type',
				array(
					'label'               => esc_html__( 'Team', 'trx_addons' ),
					'description'         => esc_html__( 'Team Description', 'trx_addons' ),
					'labels'              => array(
						'name'                => esc_html__( 'Team', 'trx_addons' ),
						'singular_name'       => esc_html__( 'Team member', 'trx_addons' ),
						'menu_name'           => esc_html__( 'Team', 'trx_addons' ),
						'parent_item_colon'   => esc_html__( 'Parent Item:', 'trx_addons' ),
						'all_items'           => esc_html__( 'All Team', 'trx_addons' ),
						'view_item'           => esc_html__( 'View Team member', 'trx_addons' ),
						'add_new_item'        => esc_html__( 'Add New Team member', 'trx_addons' ),
						'add_new'             => esc_html__( 'Add New', 'trx_addons' ),
						'edit_item'           => esc_html__( 'Edit Team member', 'trx_addons' ),
						'update_item'         => esc_html__( 'Update Team member', 'trx_addons' ),
						'search_items'        => esc_html__( 'Search Team member', 'trx_addons' ),
						'not_found'           => esc_html__( 'Not found', 'trx_addons' ),
						'not_found_in_trash'  => esc_html__( 'Not found in Trash', 'trx_addons' ),
					),
					'taxonomies'          => array(TRX_ADDONS_CPT_TEAM_TAXONOMY),
					'supports'            => trx_addons_cpt_param('team', 'supports'),
					'public'              => true,
					'hierarchical'        => false,
					'has_archive'         => true,
					'can_export'          => true,
					'show_in_admin_bar'   => true,
					'show_in_menu'        => true,
					// From WordPress 5.3 'menu_position' must be only integer or null (default)!
					// 'menu_position'       => '53.8',
					'menu_icon'			  => 'dashicons-admin-users',
					'capability_type'     => 'post',
					'rewrite'             => array(
												'slug'         => trx_addons_cpt_param('team', 'post_type_slug'),
												'with_front'   => false,
												'hierarchical' => false
											)
				),
				TRX_ADDONS_CPT_TEAM_PT
			)
		);
	}
}


// Allow Gutenberg as main editor for this post type
if ( ! function_exists( 'trx_addons_cpt_team_add_pt_to_gutenberg' ) ) {
	add_filter( 'trx_addons_filter_add_pt_to_gutenberg', 'trx_addons_cpt_team_add_pt_to_gutenberg', 10, 2 );
	function trx_addons_cpt_team_add_pt_to_gutenberg( $allow, $post_type ) {
		return $allow || $post_type == TRX_ADDONS_CPT_TEAM_PT;
	}
}

// Allow Gutenberg as main editor for taxonomies
if ( ! function_exists( 'trx_addons_cpt_team_add_taxonomy_to_gutenberg' ) ) {
	add_filter( 'trx_addons_filter_add_taxonomy_to_gutenberg', 'trx_addons_cpt_team_add_taxonomy_to_gutenberg', 10, 2 );
	function trx_addons_cpt_team_add_taxonomy_to_gutenberg( $allow, $tax ) {
		return $allow || in_array( $tax, array( TRX_ADDONS_CPT_TEAM_TAXONOMY ) );
	}
}

// Fill 'options' arrays when its need in the admin mode
if (!function_exists('trx_addons_cpt_team_options_get_list_choises')) {
	add_filter('trx_addons_filter_options_get_list_choises', 'trx_addons_cpt_team_options_get_list_choises', 10, 2);
	function trx_addons_cpt_team_options_get_list_choises($list, $name) {
		if ( is_array( $list ) && count( $list ) == 0 ) {
			if ($name == 'user') {
				$list = trx_addons_get_list_users();
			} else if ($name == 'team_form') {
				$list = apply_filters('trx_addons_filter_page_contact_form',
					trx_addons_array_merge(
						array(
							'none' => esc_html__('None', 'trx_addons'),
							'default' => esc_html__('Default', 'trx_addons')
							),
						function_exists('trx_addons_exists_cf7') && trx_addons_exists_cf7() && is_admin() && (in_array(trx_addons_get_value_gp('page'), array('trx_addons_options', 'theme_options')) || strpos($_SERVER['REQUEST_URI'], 'customize.php')!==false)
							? trx_addons_get_list_cf7()
							: array()
					), 'team'
				);
			}
		}
		return $list;
	}
}

/* ------------------- Old way - moved to the cpt.php now ---------------------
// Add 'Team' parameters in the ThemeREX Addons Options
if (!function_exists('trx_addons_cpt_team_options')) {
	add_filter( 'trx_addons_filter_options', 'trx_addons_cpt_team_options');
	function trx_addons_cpt_team_options($options) {
		trx_addons_array_insert_after($options, 'cpt_section', trx_addons_cpt_team_get_list_options());
		return $options;
	}
}

// Return parameters list for plugin's options
if (!function_exists('trx_addons_cpt_team_get_list_options')) {
	function trx_addons_cpt_team_get_list_options($add_parameters=array()) {
		return apply_filters('trx_addons_cpt_list_options', array(
			'team_info' => array(
				"title" => esc_html__('Team', 'trx_addons'),
				"desc" => wp_kses_data( __('Settings of the team members archive', 'trx_addons') ),
				"type" => "info"
			),
			'team_style' => array(
				"title" => esc_html__('Style', 'trx_addons'),
				"desc" => wp_kses_data( __('Style of the team archive', 'trx_addons') ),
				"std" => 'default_2',
				"options" => apply_filters('trx_addons_filter_cpt_archive_styles',
											trx_addons_components_get_allowed_layouts('cpt', 'team', 'arh'), 
											TRX_ADDONS_CPT_TEAM_PT),
				"type" => "select"
			)
		), 'team');
	}
}
------------------- /Old way --------------------- */

// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_cpt_team_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_cpt_team_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_cpt_team_load_scripts_front', 10, 1 );
	function trx_addons_cpt_team_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'cpt_team', $force, array(
			'css'  => array(
				'trx_addons-cpt_team' => array( 'src' => TRX_ADDONS_PLUGIN_CPT . 'team/team.css' ),
			),
			'need' => trx_addons_is_team_page(),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_sc_team' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/team' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_team"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_sc_team' ),
			)
		) );
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'trx_addons_cpt_team_load_scripts_front_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_cpt_team_load_scripts_front_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	add_action( 'trx_addons_action_load_scripts_front_cpt_team', 'trx_addons_cpt_team_load_scripts_front_responsive', 10, 1 );
	function trx_addons_cpt_team_load_scripts_front_responsive( $force = false ) {
		trx_addons_enqueue_optimized_responsive( 'cpt_team', $force, array(
			'css'  => array(
				'trx_addons-cpt_team-responsive' => array(
					'src' => TRX_ADDONS_PLUGIN_CPT . 'team/team.responsive.css',
					'media' => 'xl'
				),
			),
		) );
	}
}
	
// Merge shortcode's specific styles to the single stylesheet
if ( !function_exists( 'trx_addons_cpt_team_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_cpt_team_merge_styles');
	function trx_addons_cpt_team_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_CPT . 'team/team.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_cpt_team_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_cpt_team_merge_styles_responsive');
	function trx_addons_cpt_team_merge_styles_responsive($list) {
		$list[ TRX_ADDONS_PLUGIN_CPT . 'team/team.responsive.css' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu
if ( !function_exists( 'trx_addons_cpt_team_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_cpt_team_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_cpt_team_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_cpt_team_check_in_html_output', 10, 1 );
	function trx_addons_cpt_team_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*sc_team',
				'class=[\'"][^\'"]*type\\-' . TRX_ADDONS_CPT_TEAM_PT,
				'class=[\'"][^\'"]*' . TRX_ADDONS_CPT_TEAM_TAXONOMY . '\\-',
			)
		);
		if ( trx_addons_check_in_html_output( 'cpt_team', $content, $args ) ) {
			trx_addons_cpt_team_load_scripts_front( true );
		}
		return $content;
	}
}


// Return true if it's team page
if ( !function_exists( 'trx_addons_is_team_page' ) ) {
	function trx_addons_is_team_page() {
		return defined('TRX_ADDONS_CPT_TEAM_PT') 
					&& !is_search()
					&& (
						(trx_addons_is_single() && get_post_type()==TRX_ADDONS_CPT_TEAM_PT)
						|| is_post_type_archive(TRX_ADDONS_CPT_TEAM_PT)
						|| is_tax(TRX_ADDONS_CPT_TEAM_TAXONOMY)
						);
	}
}



// Replace standard theme templates
//-------------------------------------------------------------

// Change standard single template for team posts
if ( !function_exists( 'trx_addons_cpt_team_single_template' ) ) {
	add_filter('single_template', 'trx_addons_cpt_team_single_template');
	function trx_addons_cpt_team_single_template($template) {
		global $post;
		if (trx_addons_is_single() && $post->post_type == TRX_ADDONS_CPT_TEAM_PT)
			$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'team/tpl.single.php');
		return $template;
	}
}

// Change standard archive template for team posts
if ( !function_exists( 'trx_addons_cpt_team_archive_template' ) ) {
	add_filter('archive_template',	'trx_addons_cpt_team_archive_template');
	function trx_addons_cpt_team_archive_template( $template ) {
		if ( is_post_type_archive(TRX_ADDONS_CPT_TEAM_PT) )
			$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'team/tpl.archive.php');
		return $template;
	}	
}

// Change standard category template for team categories (groups)
if ( !function_exists( 'trx_addons_cpt_team_taxonomy_template' ) ) {
	add_filter('taxonomy_template',	'trx_addons_cpt_team_taxonomy_template');
	function trx_addons_cpt_team_taxonomy_template( $template ) {
		if ( is_tax(TRX_ADDONS_CPT_TEAM_TAXONOMY) )
			$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'team/tpl.archive.php');
		return $template;
	}	
}



// Posts of this author
//-------------------------------------------------------------

// Show related posts
if ( !function_exists( 'trx_addons_cpt_team_related_posts_after_article' ) ) {
	add_action('trx_addons_action_after_article', 'trx_addons_cpt_team_related_posts_after_article', 20, 1);
	function trx_addons_cpt_team_related_posts_after_article( $mode ) {
		if ($mode == 'team.single' && apply_filters('trx_addons_filter_show_related_posts_after_article', true)) {
			do_action('trx_addons_action_related_posts', $mode);
		}
	}
}

if ( !function_exists( 'trx_addons_cpt_team_related_posts_show' ) ) {
	add_filter('trx_addons_filter_show_related_posts', 'trx_addons_cpt_team_related_posts_show');
	function trx_addons_cpt_team_related_posts_show( $show ) {
		if (!$show && trx_addons_is_single() && get_post_type() == TRX_ADDONS_CPT_TEAM_PT) {
			do_action('trx_addons_action_related_posts', 'team.single');
			$show = true;
		}
		return $show;
	}
}

if ( !function_exists( 'trx_addons_cpt_team_related_posts' ) ) {
	add_action('trx_addons_action_related_posts', 'trx_addons_cpt_team_related_posts', 10, 1);
	function trx_addons_cpt_team_related_posts( $mode ) {
		if ($mode == 'team.single') {
			$meta = (array)get_post_meta(get_the_ID(), 'trx_addons_options', true);
			$user_id = !empty($meta['user']) ? $meta['user'] : 0;
			if ( ! $user_id ) return;
			trx_addons_get_template_part( apply_filters( 'trx_addons_filter_posts_related_template', 'templates/tpl.posts-related.php', 'team' ),
												'trx_addons_args_related',
												apply_filters('trx_addons_filter_args_related', array(
																	'class' => 'team_page_related sc_team sc_team_posts',
																	'posts_per_page' => apply_filters( 'trx_addons_filter_related_posts', 3, 'team_posts'),
																	'columns' => apply_filters( 'trx_addons_filter_related_columns', 3, 'team_posts'),
																	'template' => TRX_ADDONS_PLUGIN_CPT . 'team/tpl.team-posts.php',
																	'template_args_name' => 'trx_addons_args_sc_team',
																	'author' => $user_id,
																	'orderby' => 'date',
																	// Translator: insert post title to the Related posts caption
																	'title' => sprintf( __('Posts by %s', 'trx_addons'), get_the_title() )
																	)
															)
											);
		}
	}
}




// Projects of this author
//-------------------------------------------------------------

// Show projects
if ( !function_exists( 'trx_addons_cpt_team_projects_after_article' ) ) {
	add_action('trx_addons_action_after_article', 'trx_addons_cpt_team_projects_after_article', 30, 1);
	function trx_addons_cpt_team_projects_after_article( $mode ) {
		if ($mode == 'team.single' && apply_filters('trx_addons_filter_show_projects_after_article', defined('TRX_ADDONS_CPT_PORTFOLIO_PT'))) {
			ob_start();
			trx_addons_get_template_part(
				TRX_ADDONS_PLUGIN_CPT . 'portfolio/tpl.default.php',
				'trx_addons_args_sc_portfolio',
				trx_addons_sc_prepare_atts(
					'trx_sc_portfolio',
					// New values
					apply_filters(
						'trx_addons_filter_args_team_projects',
						array(
							'count' => apply_filters( 'trx_addons_filter_args_team_projects_count', 3),
							'columns' => apply_filters( 'trx_addons_filter_args_team_projects_columns', 3),
							'project_author' => get_the_ID(),
							'orderby' => 'date',
							'order' => 'DESC',
						)
					),
					// Default parameters
					trx_addons_sc_common_atts(
						// Common params
						'id,title,slider,query',
						// Individual (shortcode-specific) params
						array(
							'type' => "default",
							'more_text' => esc_html__('Read more', 'trx_addons'),
							'pagination' => "none",
							'page' => 1,
							'project_author' => 0,
							)
						)
					)
			);
			$output = ob_get_contents();
			ob_end_clean();
			if ( !empty($output) ) {
				?>
				<section class="team_member_projects">
					<h3 class="section_title team_member_projects_title"><?php echo esc_html( sprintf( __('Projects by %s', 'trx_addons'), get_the_title() ) ); ?></h3>
					<?php trx_addons_show_layout( $output ); ?>
				</section>
				<?php
			}
		}
	}
}



// Contact form
//-------------------------------------------------------------

// Show contact form
if ( !function_exists( 'trx_addons_cpt_team_contact_form_after_article' ) ) {
	add_action('trx_addons_action_after_article', 'trx_addons_cpt_team_contact_form_after_article', 50, 1);
	function trx_addons_cpt_team_contact_form_after_article( $mode ) {
		if ($mode == 'team.single') {
			do_action('trx_addons_action_contact_form', $mode);
		}
	}
}

if ( !function_exists( 'trx_addons_cpt_team_contact_form' ) ) {
	add_action('trx_addons_action_contact_form', 'trx_addons_cpt_team_contact_form', 10, 1);
	function trx_addons_cpt_team_contact_form( $mode ) {
		if ($mode == 'team.single') {
			$form_id = trx_addons_get_option('team_form');
			if ( !empty($form_id) && !trx_addons_is_off($form_id) ) {
				?><section class="page_contact_form team_page_form">
					<h3 class="section_title page_contact_form_title"><?php
						echo esc_html( apply_filters( 'trx_addons_filter_team_posts_title', __('Contact me', 'trx_addons') ) );
					?></h3><?php
					// Display Contact Form 7
					if ( (int) $form_id > 0 ) {
						trx_addons_show_layout( do_shortcode('[contact-form-7 id="'.esc_attr($form_id).'"]') );
			
					// Default form
					} else if ($form_id == 'default' && function_exists( 'trx_addons_sc_form' ) ) {
						trx_addons_show_layout( trx_addons_sc_form( array() ) );
					}
				?></section><?php
			}
		}
	}
}



// Admin utils
// -----------------------------------------------------------------

// Show <select> with team categories in the admin filters area
if (!function_exists('trx_addons_cpt_team_admin_filters')) {
	add_action( 'restrict_manage_posts', 'trx_addons_cpt_team_admin_filters' );
	function trx_addons_cpt_team_admin_filters() {
		trx_addons_admin_filters(TRX_ADDONS_CPT_TEAM_PT, TRX_ADDONS_CPT_TEAM_TAXONOMY);
	}
}
  
// Clear terms cache on the taxonomy save
if (!function_exists('trx_addons_cpt_team_admin_clear_cache')) {
	add_action( 'edited_'.TRX_ADDONS_CPT_TEAM_TAXONOMY, 'trx_addons_cpt_team_admin_clear_cache', 10, 1 );
	add_action( 'delete_'.TRX_ADDONS_CPT_TEAM_TAXONOMY, 'trx_addons_cpt_team_admin_clear_cache', 10, 1 );
	add_action( 'created_'.TRX_ADDONS_CPT_TEAM_TAXONOMY, 'trx_addons_cpt_team_admin_clear_cache', 10, 1 );
	function trx_addons_cpt_team_admin_clear_cache( $term_id=0 ) {  
		trx_addons_admin_clear_cache_terms(TRX_ADDONS_CPT_TEAM_TAXONOMY);
	}
}


// Add shortcodes
//----------------------------------------------------------------------------
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'team/team-sc.php';

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'team/team-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'team/team-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'team/team-sc-vc.php';
}

// Create our widget
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'team/team-widget.php';
