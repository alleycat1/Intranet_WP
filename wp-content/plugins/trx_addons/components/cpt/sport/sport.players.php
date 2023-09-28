<?php
/**
 * ThemeREX Addons: Players support in the Sports Reviews Management
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
if ( ! defined('TRX_ADDONS_CPT_PLAYERS_PT') )		define('TRX_ADDONS_CPT_PLAYERS_PT', trx_addons_cpt_param('players', 'post_type'));

// Register post types and taxonomies
if (!function_exists('trx_addons_cpt_players_init')) {
	add_action( 'init', 'trx_addons_cpt_players_init' );
	function trx_addons_cpt_players_init() {

		// Add parameters to the Meta Box support
		trx_addons_meta_box_register(TRX_ADDONS_CPT_PLAYERS_PT, array(
			"birthday" => array(
				"title" => esc_html__("Birthday",  'trx_addons'),
				"desc" => wp_kses_data( __("Birthday in format: yyyy-mm-dd", 'trx_addons') ),
				"std" => date('Y-m-d'),
				"type" => "date"
			),
			"club" => array(
				"title" => esc_html__("Club /Country/",  'trx_addons'),
				"desc" => wp_kses_data( __("Specify player's club name and/or country", 'trx_addons') ),
				"std" => '',
				"type" => "text"
			),
			"info" => array(
				"title" => esc_html__("Info",  'trx_addons'),
				"desc" => wp_kses_data( __("Brief info about this player", 'trx_addons') ),
				"std" => '',
				"type" => "textarea"
			)
		));
		
		// Register post type and taxonomy
		register_post_type(
			TRX_ADDONS_CPT_PLAYERS_PT,
			apply_filters('trx_addons_filter_register_post_type',
				array(
					'label'               => esc_html__( 'Players', 'trx_addons' ),
					'description'         => esc_html__( 'Players Description', 'trx_addons' ),
					'labels'              => array(
						'name'                => esc_html__( 'Players', 'trx_addons' ),
						'singular_name'       => esc_html__( 'Player', 'trx_addons' ),
						'menu_name'           => esc_html__( 'Players', 'trx_addons' ),
						'parent_item_colon'   => esc_html__( 'Parent Item:', 'trx_addons' ),
						'all_items'           => esc_html__( 'All Players', 'trx_addons' ),
						'view_item'           => esc_html__( 'View Player', 'trx_addons' ),
						'add_new_item'        => esc_html__( 'Add New Player', 'trx_addons' ),
						'add_new'             => esc_html__( 'Add New', 'trx_addons' ),
						'edit_item'           => esc_html__( 'Edit Player', 'trx_addons' ),
						'update_item'         => esc_html__( 'Update Player', 'trx_addons' ),
						'search_items'        => esc_html__( 'Search Players', 'trx_addons' ),
						'not_found'           => esc_html__( 'Not found', 'trx_addons' ),
						'not_found_in_trash'  => esc_html__( 'Not found in Trash', 'trx_addons' ),
					),
					'supports'            => trx_addons_cpt_param('players', 'supports'),
					'public'              => true,
					'hierarchical'        => false,
					'has_archive'         => true,
					'can_export'          => true,
					'show_in_admin_bar'   => true,
					'show_in_menu'        => false,
					'capability_type'     => 'post',
					'rewrite'             => array(
												'slug'         => trx_addons_cpt_param('players', 'post_type_slug'),
												'with_front'   => false,
												'hierarchical' => false
											)
				),
				TRX_ADDONS_CPT_PLAYERS_PT
			)
		);

		// Add rules to show players
		add_rewrite_tag('%player%', '([^&]+)');
	}
}

	
// Add sort in the query for the players
if ( !function_exists( 'trx_addons_cpt_players_add_sort_order' ) ) {
	add_filter('trx_addons_filter_add_sort_order',	'trx_addons_cpt_players_add_sort_order', 10, 3);
	function trx_addons_cpt_players_add_sort_order($q, $orderby, $order='desc') {
		if ($orderby == 'points') {
			$q['order'] = $order;
			$q['orderby'] = 'meta_value';
			$q['meta_key'] = 'trx_addons_player_points';
		}
		return $q;
	}
}


// Save competition's date for search, sorting, etc.
if ( !function_exists( 'trx_addons_cpt_players_save_post_options' ) ) {
	add_filter('trx_addons_filter_save_post_options', 'trx_addons_cpt_players_save_post_options', 10, 3);
	function trx_addons_cpt_players_save_post_options($options, $post_id, $post_type) {
		if ($post_type == TRX_ADDONS_CPT_PLAYERS_PT) {
			// If save a new player
			//if ( (int) get_post_meta($post_id, 'trx_addons_competition', true) == 0) {
				//update_post_meta($post_id, 'trx_addons_competition', trx_addons_get_value_gp('competition'));
			$post = get_post($post_id);
			if ( $post->post_parent == 0) {
				trx_addons_update_post($post_id, array('post_parent' => trx_addons_get_value_gp('competition')));
				update_post_meta($post_id, 'trx_addons_player_points', 0);
				update_post_meta($post_id, 'trx_addons_player_points_data', array());
			}
		}
		return $options;
	}
}


// Return true if it's players page
if ( !function_exists( 'trx_addons_is_players_page' ) ) {
	function trx_addons_is_players_page() {
		return !is_search() && (
						(trx_addons_is_single() && get_post_type()==TRX_ADDONS_CPT_PLAYERS_PT)
						|| is_post_type_archive(TRX_ADDONS_CPT_PLAYERS_PT)
						);
	}
}

// Return links with parent competition and sports for the breadcrumbs
if ( !function_exists( 'trx_addons_cpt_players_get_parents_links' ) ) {
	add_filter('trx_addons_filter_get_parents_links', 'trx_addons_cpt_players_get_parents_links', 10, 2);
	function trx_addons_cpt_players_get_parents_links($links='', $args=array()) {
		if (is_post_type_archive(TRX_ADDONS_CPT_PLAYERS_PT)) {
			$competition = trx_addons_get_value_gp('competition');
			if ((int)$competition > 0) {
				$terms = get_the_terms($competition, TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY);
				$sport = is_array($terms) && count($terms)>0 ? $terms[0] : null;
				if ($sport) {
					$obj = get_post_type_object(TRX_ADDONS_CPT_COMPETITIONS_PT);
					$links .= '<a href="'.esc_url(get_post_type_archive_link( TRX_ADDONS_CPT_COMPETITIONS_PT )).'">'.esc_html($obj->labels->all_items).'</a>'
							. $args['delimiter']
							. '<a href="'.esc_url(get_term_link($sport->term_id, $sport->taxonomy)).'">'.esc_html($sport->name).'</a>'
							. (!empty($links) ? $args['delimiter'] : '') . $links;
				}
			}
		}
		return $links;
	}
}

// Return link to the all posts for the breadcrumbs
if ( !function_exists( 'trx_addons_cpt_players_get_blog_all_posts_link' ) ) {
	add_filter('trx_addons_filter_get_blog_all_posts_link', 'trx_addons_cpt_players_get_blog_all_posts_link', 10, 2);
	function trx_addons_cpt_players_get_blog_all_posts_link($link='', $args=array()) {
		if ($link=='') {
			if (trx_addons_is_players_page() && !is_post_type_archive(TRX_ADDONS_CPT_PLAYERS_PT)) {
				global $post;
				if (!empty($post->post_parent)) {
					$competition = $post->post_parent;
					$terms = get_the_terms($competition, TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY);
					$sport = is_array($terms) && count($terms)>0 ? $terms[0] : null;
					if ($sport) {
						$url_pt = get_post_type_archive_link( TRX_ADDONS_CPT_COMPETITIONS_PT );
						$url_tx = get_term_link($sport->term_id, $sport->taxonomy);
						if ($url_pt) $obj = get_post_type_object(TRX_ADDONS_CPT_COMPETITIONS_PT);
						$link = ($url_pt
									? '<a href="'.esc_url($url_pt).'">'.esc_html($obj->labels->all_items).'</a>'
										. $args['delimiter']
									: '')
                                . ($url_tx
                                	? '<a href="'.esc_url($url_tx).'">'.esc_html($sport->name).'</a>'
                                	: '');
					}
				}
			}
		}
		return $link;
	}
}

// Return current page title
if ( !function_exists( 'trx_addons_cpt_players_get_blog_title' ) ) {
	add_filter( 'trx_addons_filter_get_blog_title', 'trx_addons_cpt_players_get_blog_title');
	function trx_addons_cpt_players_get_blog_title($title='') {
		if ( is_post_type_archive(TRX_ADDONS_CPT_PLAYERS_PT) ) {
			$competition = trx_addons_get_value_gp('competition');
			if ((int) $competition > 0)
				$title = get_the_title($competition);
/*
			else {
				$obj = get_post_type_object(TRX_ADDONS_CPT_PLAYERS_PT);
				$title = $obj->labels->all_items;
			}
*/
		}
		return $title;
	}
}


// Replace standard theme templates
//-------------------------------------------------------------

// Change standard single template for the matches posts
if ( !function_exists( 'trx_addons_cpt_players_single_template' ) ) {
	add_filter('single_template', 'trx_addons_cpt_players_single_template');
	function trx_addons_cpt_players_single_template($template) {
		global $post;
		if (trx_addons_is_single() && $post->post_type == TRX_ADDONS_CPT_PLAYERS_PT) {
			if ( ($template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'sport/tpl.single.players.php')) == '' )
				$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'sport/tpl.single.php');
		}
		return $template;
	}
}

// Change standard archive template for the matches posts
if ( !function_exists( 'trx_addons_cpt_players_archive_template' ) ) {
	add_filter('archive_template',	'trx_addons_cpt_players_archive_template');
	function trx_addons_cpt_players_archive_template( $template ) {
		if ( is_post_type_archive(TRX_ADDONS_CPT_PLAYERS_PT) ) {
			if ( ($template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'sport/tpl.archive.players.php')) == '' )
				$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'sport/tpl.archive.php');
		}
		return $template;
	}	
}



// Admin utils
// -----------------------------------------------------------------

// Create additional column in the posts list
if (!function_exists('trx_addons_cpt_players_add_custom_column')) {
	add_filter('manage_edit-'.trx_addons_cpt_param('players', 'post_type').'_columns',	'trx_addons_cpt_players_add_custom_column', 9);
	function trx_addons_cpt_players_add_custom_column( $columns ){
		if (is_array($columns) && count($columns)>0) {
			$new_columns = array();
			foreach($columns as $k=>$v) {
				if ($k=='title') {
					$new_columns['cpt_players_avatar'] = esc_html__('Photo', 'trx_addons');
				}
				if ($k=='title') {
					$new_columns['cpt_players_points'] = esc_html__('Points / Matches', 'trx_addons');
				}
				$new_columns[$k] = $v;
				if ($k=='title') {
					$new_columns['cpt_players_details'] = esc_html__('Details', 'trx_addons');
				}
			}
			$columns = $new_columns;
		}
		return $columns;
	}
}

// Make column 'Points' sortable
if (!function_exists('trx_addons_cpt_players_sort_custom_column')) {
	add_filter('manage_edit-'.trx_addons_cpt_param('players', 'post_type').'_sortable_columns',	'trx_addons_cpt_players_sort_custom_column');
	function trx_addons_cpt_players_sort_custom_column( $columns ) {
		if (is_array($columns)) $columns['cpt_players_points'] = array('trx_addons_player_points', !(trx_addons_get_value_gp('orderby')=='' || trx_addons_get_value_gp('orderby')=='trx_addons_player_points' && trx_addons_get_value_gp('order')=='desc'));
		return $columns;
	}
}

// Fill custom columns in the posts list
if (!function_exists('trx_addons_cpt_players_fill_custom_column')) {
	add_action('manage_'.trx_addons_cpt_param('players', 'post_type').'_posts_custom_column',	'trx_addons_cpt_players_fill_custom_column', 9, 2);
	function trx_addons_cpt_players_fill_custom_column($column_name='', $post_id=0) {
		if ($column_name == 'cpt_players_avatar') {
			$image = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), trx_addons_get_thumb_size('masonry') );
			if ( ! empty($image[0]) ) {
				?><img class="trx_addons_cpt_column_image_preview trx_addons_cpt_players_image_preview" src="<?php echo esc_url($image[0]); ?>" alt="<?php esc_attr_e("Player's logo", 'trx_addons'); ?>"<?php if (!empty($image[1])) echo ' width="'.intval($image[1]).'"'; ?><?php if (!empty($image[2])) echo ' height="'.intval($image[2]).'"'; ?>><?php
			}
		} else if ($column_name == 'cpt_players_points') {
			$points_data = get_post_meta($post_id, 'trx_addons_player_points_data', true);
			if (is_array($points_data) && count($points_data)>0) {
				$points = get_post_meta($post_id, 'trx_addons_player_points', true);
				?><span class="trx_addons_cpt_column_points"><?php echo esc_html($points); ?></span><?php
				?> / <span class="trx_addons_cpt_column_matches"><?php echo esc_html(count($points_data)); ?></span><?php
			}
		} else if ($column_name == 'cpt_players_details') {
			// Show player's details
			$meta = (array)get_post_meta($post_id, 'trx_addons_options', true);
			if (!empty($meta['birthday'])) {
				?><div class="trx_addons_meta_row">
					<span class="trx_addons_meta_label"><?php esc_html_e('Birthday:', 'trx_addons'); ?></span>
					<span class="trx_addons_meta_data"><?php echo date_i18n(get_option('date_format'), strtotime($meta['birthday'])); ?></span>
				</div><?php
			}
			if (!empty($meta['club'])) {
				?><div class="trx_addons_meta_row">
					<span class="trx_addons_meta_label"><?php esc_html_e('Club /Country/:', 'trx_addons'); ?></span>
					<span class="trx_addons_meta_data"><?php echo esc_html($meta['club']); ?></span>
				</div><?php
			}
			if (!empty($meta['info'])) {
				?><div class="trx_addons_meta_row">
					<span class="trx_addons_meta_data trx_addons_meta_description"><?php echo nl2br(esc_html($meta['info'])); ?></span>
				</div><?php
			}
		}
	}
}
