<?php
/**
 * ThemeREX Addons: Competitions support in the Sports Reviews Management
 *
 * @package ThemeREX Addons
 * @since v1.6.17
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// -----------------------------------------------------------------
// -- Custom post type registration
// -----------------------------------------------------------------

// Define Custom post type and taxonomy constants
if ( ! defined('TRX_ADDONS_CPT_COMPETITIONS_PT') )			define('TRX_ADDONS_CPT_COMPETITIONS_PT', trx_addons_cpt_param('competitions', 'post_type'));
if ( ! defined('TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY') )	define('TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY', trx_addons_cpt_param('competitions', 'taxonomy'));

// Register post types and taxonomies
if (!function_exists('trx_addons_cpt_competitions_init')) {
	add_action( 'init', 'trx_addons_cpt_competitions_init' );
	function trx_addons_cpt_competitions_init() {

		// Add parameters to the Meta Box support
		trx_addons_meta_box_register(TRX_ADDONS_CPT_COMPETITIONS_PT, array(
			"date_start" => array(
				"title" => esc_html__("Start date",  'trx_addons'),
				"desc" => wp_kses_data( __("Start date in format: yyyy-mm-dd", 'trx_addons') ),
				"std" => date('Y-m-d'),
				"type" => 'date'
			),
			"date_end" => array(
				"title" => esc_html__("End date",  'trx_addons'),
				"desc" => wp_kses_data( __("End date in format: yyyy-mm-dd", 'trx_addons') ),
				"std" => date('Y-m-d'),
				"type" => 'date'
			),
			"type" => array(
				"title" => esc_html__("Member's type",  'trx_addons'),
				"desc" => esc_html__("Select type of members in this competitions: pair (football, hokey, basketball, etc.) or group (motocross, maraphone, etc.)", 'trx_addons'),
				"std" => 'pair',
				"options" => array(
					'pair' => esc_html__('Pair', 'trx_addons'),
					'group' => esc_html__('Group', 'trx_addons'),
				),
				"type" => 'radio'
			)
		));
		
		// Register taxonomies and post types
		// Taxonomy first, because it can using the combined rewrite rule (contains the slug of the post type)
		register_taxonomy(
			TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY,
			TRX_ADDONS_CPT_COMPETITIONS_PT,
			apply_filters('trx_addons_filter_register_taxonomy',
				array(
					'post_type' 		=> TRX_ADDONS_CPT_COMPETITIONS_PT,
					'hierarchical'      => true,
					'labels'            => array(
						'name'              => esc_html__( 'Sports', 'trx_addons' ),
						'singular_name'     => esc_html__( 'Sport', 'trx_addons' ),
						'search_items'      => esc_html__( 'Search Sports', 'trx_addons' ),
						'all_items'         => esc_html__( 'All Sports', 'trx_addons' ),
						'parent_item'       => esc_html__( 'Parent Sport', 'trx_addons' ),
						'parent_item_colon' => esc_html__( 'Parent Sport:', 'trx_addons' ),
						'edit_item'         => esc_html__( 'Edit Sport', 'trx_addons' ),
						'update_item'       => esc_html__( 'Update Sport', 'trx_addons' ),
						'add_new_item'      => esc_html__( 'Add New Sport', 'trx_addons' ),
						'new_item_name'     => esc_html__( 'New Sport Name', 'trx_addons' ),
						'menu_name'         => esc_html__( 'Sports', 'trx_addons' ),
					),
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'rewrite'           => array(
												'slug'         => trx_addons_cpt_param('competitions', 'taxonomy_slug'),
												'with_front'   => false,
												'hierarchical' => true
											)
				),
				TRX_ADDONS_CPT_COMPETITIONS_PT,
				TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY
			)
		);

		register_post_type(
			TRX_ADDONS_CPT_COMPETITIONS_PT,
			apply_filters('trx_addons_filter_register_post_type',
				array(
					'label'               => esc_html__( 'Competitions', 'trx_addons' ),
					'description'         => esc_html__( 'Competitions Description', 'trx_addons' ),
					'labels'              => array(
						'name'                => esc_html__( 'Competitions', 'trx_addons' ),
						'singular_name'       => esc_html__( 'Competition', 'trx_addons' ),
						'menu_name'           => esc_html__( 'Competitions', 'trx_addons' ),
						'parent_item_colon'   => esc_html__( 'Parent Item:', 'trx_addons' ),
						'all_items'           => esc_html__( 'All Competitions', 'trx_addons' ),
						'view_item'           => esc_html__( 'View Competition', 'trx_addons' ),
						'add_new_item'        => esc_html__( 'Add New Competition', 'trx_addons' ),
						'add_new'             => esc_html__( 'Add New', 'trx_addons' ),
						'edit_item'           => esc_html__( 'Edit Competition', 'trx_addons' ),
						'update_item'         => esc_html__( 'Update Competition', 'trx_addons' ),
						'search_items'        => esc_html__( 'Search Competitions', 'trx_addons' ),
						'not_found'           => esc_html__( 'Not found', 'trx_addons' ),
						'not_found_in_trash'  => esc_html__( 'Not found in Trash', 'trx_addons' ),
					),
					'taxonomies'          => array(TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY),
					'supports'            => trx_addons_cpt_param('competitions', 'supports'),
					'public'              => true,
					'hierarchical'        => false,
					'has_archive'         => true,
					'can_export'          => true,
					'show_in_admin_bar'   => true,
					'show_in_menu'        => false,
					'capability_type'     => 'post',
					'rewrite'             => array( 
												'slug'         => trx_addons_cpt_param('competitions', 'post_type_slug'),
												'with_front'   => false,
												'hierarchical' => false
											)
				),
				TRX_ADDONS_CPT_COMPETITIONS_PT
			)
		);

		// Add rewrite rules to show competitions
		add_rewrite_tag('%competition%', '([^&]+)');
	}
}

	
// Add sort in the query for the competitions
if ( !function_exists( 'trx_addons_cpt_competitions_add_sort_order' ) ) {
	add_filter('trx_addons_filter_add_sort_order',	'trx_addons_cpt_competitions_add_sort_order', 10, 3);
	function trx_addons_cpt_competitions_add_sort_order($q, $orderby, $order='desc') {
		if ($orderby == 'competition_date') {
			$q['order'] = $order;
			$q['orderby'] = 'meta_value';
			$q['meta_key'] = 'trx_addons_competition_date';
		}
		return $q;
	}
}


// Save competition's date for search, sorting, etc.
if ( !function_exists( 'trx_addons_cpt_competitions_save_post_options' ) ) {
	add_filter('trx_addons_filter_save_post_options', 'trx_addons_cpt_competitions_save_post_options', 10, 3);
	function trx_addons_cpt_competitions_save_post_options($options, $post_id, $post_type) {
		if ($post_type == TRX_ADDONS_CPT_COMPETITIONS_PT) {
			update_post_meta($post_id, 'trx_addons_competition_date', $options['date_start']);
		}
		return $options;
	}
}


// Return true if it's competitions page
if ( !function_exists( 'trx_addons_is_competitions_page' ) ) {
	function trx_addons_is_competitions_page() {
		return !is_search()	&& (
						(trx_addons_is_single() && get_post_type()==TRX_ADDONS_CPT_COMPETITIONS_PT)
						|| is_post_type_archive(TRX_ADDONS_CPT_COMPETITIONS_PT)
						|| is_tax(TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY)
						);
	}
}


// Replace standard theme templates
//-------------------------------------------------------------

// Change standard single template for the competitions posts
if ( !function_exists( 'trx_addons_cpt_competitions_single_template' ) ) {
	add_filter('single_template', 'trx_addons_cpt_competitions_single_template');
	function trx_addons_cpt_competitions_single_template($template) {
		global $post;
		if (trx_addons_is_single() && $post->post_type == TRX_ADDONS_CPT_COMPETITIONS_PT) {
			if ( ($template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'sport/tpl.single.competitions.php')) == '' )
				$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'sport/tpl.single.php');
		}
		return $template;
	}
}

// Change standard archive template for the competitions posts
if ( !function_exists( 'trx_addons_cpt_competitions_archive_template' ) ) {
	add_filter('archive_template',	'trx_addons_cpt_competitions_archive_template');
	function trx_addons_cpt_competitions_archive_template( $template ) {
		if ( is_post_type_archive(TRX_ADDONS_CPT_COMPETITIONS_PT) ) {
			if ( ($template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'sport/tpl.archive.competitions.php')) == '' )
				$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'sport/tpl.archive.php');
		}
		return $template;
	}	
}

// Change standard category template for the competitions categories (sports)
if ( !function_exists( 'trx_addons_cpt_competitions_taxonomy_template' ) ) {
	add_filter('taxonomy_template',	'trx_addons_cpt_competitions_taxonomy_template');
	function trx_addons_cpt_competitions_taxonomy_template( $template ) {
		if ( is_tax(TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY) ) {
			if ( ($template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'sport/tpl.archive.competitions.php')) == '' )
				$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'sport/tpl.archive.php');
		}
		return $template;
	}	
}



// Admin utils
// -----------------------------------------------------------------

// Add Admin menu 'Sport' items
if (!function_exists('trx_addons_cpt_competitions_admin_menu')) {
	add_action( 'admin_menu', 'trx_addons_cpt_competitions_admin_menu' );
	function trx_addons_cpt_competitions_admin_menu() {
		$tax = TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY;
		if ( !($terms = get_transient("trx_addons_terms_filter_".trim($tax)))) {
			$terms = get_terms(array('taxonomy'=>$tax, 'hide_empty'=>false));
			if (is_array($terms) && count($terms) > 0)
				set_transient("trx_addons_terms_filter_".trim($tax), $terms, 24*60*60);
		}
		if (is_array($terms) && count($terms) > 0) {
			foreach ($terms as $term) {
				add_submenu_page('trx_addons_sport',	//parent_menu_slug
					esc_html($term->name),				//page_title
					esc_html($term->name),				//menu_title
					'edit_posts',						//capability
					'edit.php?post_type='.TRX_ADDONS_CPT_COMPETITIONS_PT.'&'.TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY.'='.$term->slug,	//menu_link
					null								//callback
				);
			}
		}
		// Link to taxonomies editor
		add_submenu_page('trx_addons_sport',
			esc_html__('Add/Delete Sport', 'trx_addons'),
			esc_html__('Add/Delete Sport', 'trx_addons'),
			'edit_posts',						//capability
			'edit-tags.php?post_type='.TRX_ADDONS_CPT_COMPETITIONS_PT.'&taxonomy='.TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY,
			null
		);
	}
}


// Show <select> with sports in the admin filters area
if (!function_exists('trx_addons_cpt_competitions_admin_filters_sport')) {
	add_action( 'restrict_manage_posts', 'trx_addons_cpt_competitions_admin_filters_sport' );
	function trx_addons_cpt_competitions_admin_filters_sport() {
		trx_addons_admin_filters(TRX_ADDONS_CPT_COMPETITIONS_PT, TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY);
	}
}
  
// Clear terms cache on the taxonomy save
if (!function_exists('trx_addons_cpt_competitions_admin_clear_cache_sport')) {
	add_action( 'edited_'.TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY, 'trx_addons_cpt_competitions_admin_clear_cache_sport', 10, 1 );
	add_action( 'delete_'.TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY, 'trx_addons_cpt_competitions_admin_clear_cache_sport', 10, 1 );
	add_action( 'created_'.TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY, 'trx_addons_cpt_competitions_admin_clear_cache_sport', 10, 1 );
	function trx_addons_cpt_competitions_admin_clear_cache_sport( $term_id=0 ) {  
		trx_addons_admin_clear_cache_terms(TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY);
	}
}

// Clear terms cache on the post save
if ( !function_exists( 'trx_addons_cpt_competitions_admin_clear_cache_sport2' ) ) {
	add_filter('trx_addons_filter_save_post_options', 'trx_addons_cpt_competitions_admin_clear_cache_sport2', 10, 3);
	function trx_addons_cpt_competitions_admin_clear_cache_sport2($options, $post_id, $post_type) {
		if ($post_type == TRX_ADDONS_CPT_COMPETITIONS_PT) {
			set_transient("trx_addons_terms_filter_".TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY, '', 24*60*60);
		}
		return $options;
	}
}


// Show <select> with competitions in the admin filters area
if (!function_exists('trx_addons_cpt_competitions_admin_filters_competition')) {
	add_action( 'restrict_manage_posts', 'trx_addons_cpt_competitions_admin_filters_competition' );
	function trx_addons_cpt_competitions_admin_filters_competition() {
		if ( !in_array(get_query_var('post_type'), array(TRX_ADDONS_CPT_ROUNDS_PT, TRX_ADDONS_CPT_PLAYERS_PT)) ) return;
		$competition = trx_addons_get_value_gp('competition');
		if (empty($competition)) return;
		$terms = get_the_terms($competition, TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY);
		if (!is_array($terms) || count($terms)==0) return;
		$tax = $terms[0]->slug;
		if ( !($competitions_list = get_transient("trx_addons_competitions_filter_".trim($tax))) ) {
			$competitions_list = trx_addons_get_list_posts(false, array(
															'post_type'=>TRX_ADDONS_CPT_COMPETITIONS_PT,
															'taxonomy'=>TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY,
															'taxonomy_value'=>$tax));
			if (is_array($competitions_list) && count($competitions_list) > 0)
				set_transient("trx_addons_competitions_filter_".trim($tax), $competitions_list, 24*60*60);
		}
		$list = '';
		if (is_array($competitions_list) && count($competitions_list) > 0) {
			$list .= '<select name="competition" id="competition" class="postform">';
			foreach ($competitions_list as $id=>$title) {
				$list .= '<option value="'.esc_attr($id).'"'.($competition==$id ? ' selected="selected"' : '').'>'.esc_html($title).'</option>';
			}
			$list .=  "</select>";
		}
		trx_addons_show_layout($list);
	}
}

// Clear competitions cache on the post save
if ( !function_exists( 'trx_addons_cpt_competitions_admin_clear_cache_competition' ) ) {
	add_filter('trx_addons_filter_save_post_options', 'trx_addons_cpt_competitions_admin_clear_cache_competition', 10, 3);
	function trx_addons_cpt_competitions_admin_clear_cache_competition($options, $post_id, $post_type) {
		if ($post_type == TRX_ADDONS_CPT_COMPETITIONS_PT) {
			$terms = get_the_terms($post_id, TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY);
			if (!is_array($terms) || count($terms)==0) return;
			$tax = $terms[0]->slug;
			set_transient("trx_addons_competitions_filter_".trim($tax), '', 24*60*60);
		}
		return $options;
	}
}


// Create additional column in the posts list
if (!function_exists('trx_addons_cpt_competitions_add_custom_column')) {
	add_filter('manage_edit-'.trx_addons_cpt_param('competitions', 'post_type').'_columns',	'trx_addons_cpt_competitions_add_custom_column', 9);
	function trx_addons_cpt_competitions_add_custom_column( $columns ){
		if (is_array($columns) && count($columns)>0) {
			$new_columns = array();
			foreach($columns as $k=>$v) {
				if ($k=='title') {
					$new_columns['cpt_competitions_image'] = esc_html__('Image', 'trx_addons');
				}
				$new_columns[$k] = $v;
				if ($k=='title') {
					$new_columns['cpt_competitions_date'] = esc_html__('Date', 'trx_addons');
				}
			}
			$columns = $new_columns;
		}
		return $columns;
	}
}

// Make column 'Date' sortable
if (!function_exists('trx_addons_cpt_competitions_sort_custom_column')) {
	add_filter('manage_edit-'.trx_addons_cpt_param('competitions', 'post_type').'_sortable_columns',	'trx_addons_cpt_competitions_sort_custom_column');
	function trx_addons_cpt_competitions_sort_custom_column( $columns ) {
		if (is_array($columns)) $columns['cpt_competitions_date'] = array('trx_addons_competition_date', trx_addons_get_value_gp('orderby')=='' || trx_addons_get_value_gp('orderby')=='trx_addons_competition_date' && trx_addons_get_value_gp('order')=='asc');
		return $columns;
	}
}

// Fill custom columns in the posts list
if (!function_exists('trx_addons_cpt_competitions_fill_custom_column')) {
	add_action('manage_'.trx_addons_cpt_param('competitions', 'post_type').'_posts_custom_column',	'trx_addons_cpt_competitions_fill_custom_column', 9, 2);
	function trx_addons_cpt_competitions_fill_custom_column($column_name='', $post_id=0) {
		if ($column_name == 'cpt_competitions_image') {
			$image = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), trx_addons_get_thumb_size('masonry') );
			if ( ! empty($image[0]) ) {
				?><img class="trx_addons_cpt_column_image_preview trx_addons_cpt_competitions_image_preview" src="<?php echo esc_url($image[0]); ?>" alt="<?php esc_attr_e("Competition's logo", 'trx_addons'); ?>"<?php if (!empty($image[1])) echo ' width="'.intval($image[1]).'"'; ?><?php if (!empty($image[2])) echo ' height="'.intval($image[2]).'"'; ?>><?php
			}
		} else if ($column_name == 'cpt_competitions_date') {
			// Show start and end date of the competition
			$meta = (array)get_post_meta($post_id, 'trx_addons_options', true);
			?><div class="trx_addons_meta_row"><?php
			trx_addons_show_layout(date_i18n(get_option('date_format'), strtotime($meta['date_start'])),
									'<span class="trx_addons_meta_date trx_addons_meta_date_start">',
									'</span>'
									);
			?><span class="trx_addons_meta_delimiter"> - </span><?php
			trx_addons_show_layout(date_i18n(get_option('date_format'), strtotime($meta['date_end'])),
									'<span class="trx_addons_meta_date trx_addons_meta_date_end">',
									'</span>'
									);
			?></div><?php
			
			// Links to rounds and members lists
			?><div class="trx_addons_meta_row">
				<a href="<?php echo esc_url(get_admin_url(null, 'edit.php?post_type='.TRX_ADDONS_CPT_ROUNDS_PT.'&competition='.intval($post_id))); ?>" class="button-primary"><?php esc_html_e('Rounds', 'trx_addons'); ?></a>
				<a href="<?php echo esc_url(get_admin_url(null, 'edit.php?post_type='.TRX_ADDONS_CPT_PLAYERS_PT.'&competition='.intval($post_id))); ?>" class="button"><?php esc_html_e('Players', 'trx_addons'); ?></a>
			</div><?php
		}
	}
}

// Add hidden field with sport or competition to the edit form
if (!function_exists('trx_addons_cpt_competitions_add_sports_field')) {
	add_action('edit_form_top',	'trx_addons_cpt_competitions_add_sports_field', 1, 1);
	function trx_addons_cpt_competitions_add_sports_field($post) {
		if (!is_object($post)) return;
		if ($post->post_type == TRX_ADDONS_CPT_COMPETITIONS_PT) {
			// Add sport
			$sport = trx_addons_get_value_gp('cpt_competitions_sports');
			if ( empty($sport) ) {
				$terms = get_the_terms($post->ID, TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY);
				if (!is_array($terms) || count($terms)==0) return;
				$sport = $terms[0]->slug;
			} else {
				// Save sport ID to the storage
				$term = get_term_by('slug', $sport, TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY);
				global $TRX_ADDONS_STORAGE;
				$TRX_ADDONS_STORAGE['sport_id'] = $term!==false ? $term->term_id : 0;
			}
			?><input type="hidden" id="cpt_competitions_sports" name="cpt_competitions_sports" value="<?php echo esc_attr($sport); ?>"><?php
		
		} else if (in_array($post->post_type, array(TRX_ADDONS_CPT_ROUNDS_PT, TRX_ADDONS_CPT_PLAYERS_PT, TRX_ADDONS_CPT_MATCHES_PT))) {
			// Add round
			$round = 0;
			if ($post->post_type == TRX_ADDONS_CPT_MATCHES_PT) {
				$round = trx_addons_get_value_gp('round');
				//if ( (int) $round == 0) $round = get_post_meta($post->ID, 'trx_addons_round', true);
				if ( (int) $round == 0) $round = $post->post_parent;
				?><input type="hidden" id="round" name="round" value="<?php echo esc_attr($round); ?>"><?php
			}
			// Add competition
			$competition = trx_addons_get_value_gp('competition');
			//if ( (int) $competition == 0) $competition = get_post_meta($post->ID, 'trx_addons_competition', true);
			if ( (int) $competition == 0) {
				if ($round > 0) {
					$round_post = get_post($round);
					$competition = $round_post->post_parent;
				} else {
					$competition = $post->post_parent;
				}
			}
			?><input type="hidden" id="competition" name="competition" value="<?php echo esc_attr($competition); ?>"><?php
		}
		
	}
}

// Check Sport in the terms list when edit Competition
if (!function_exists('trx_addons_cpt_competitions_check_sports_term')) {
	add_action('wp_terms_checklist_args',	'trx_addons_cpt_competitions_check_sports_term', 10, 2);
	function trx_addons_cpt_competitions_check_sports_term($args, $post_id) {
		global $TRX_ADDONS_STORAGE;
		if (!empty($TRX_ADDONS_STORAGE['sport_id'])) $args['selected_cats'] = array($TRX_ADDONS_STORAGE['sport_id']);
		return $args;
	}
}
