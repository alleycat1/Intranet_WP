<?php
/**
 * ThemeREX Addons: Matches support in the Sports Reviews Management
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
if ( ! defined('TRX_ADDONS_CPT_MATCHES_PT') )		define('TRX_ADDONS_CPT_MATCHES_PT', trx_addons_cpt_param('matches', 'post_type'));

// Register post types and taxonomies
if (!function_exists('trx_addons_cpt_matches_init')) {
	add_action( 'init', 'trx_addons_cpt_matches_init' );
	function trx_addons_cpt_matches_init() {

		// Add parameters to the Meta Box support
		$players = $posts = array();
		$competition_meta = $round_meta = array();
		$need = false;
		$post = null;
		if (is_admin()) {
			$need_lists = strpos($_SERVER['REQUEST_URI'], 'post-new.php')!==false && trx_addons_get_value_gp('post_type')==TRX_ADDONS_CPT_MATCHES_PT;
			if (!$need_lists && strpos($_SERVER['REQUEST_URI'], 'post.php')!==false && ($post_id = (int) trx_addons_get_value_gp('post')) > 0) {
				$post = get_post($post_id);
				$need_lists = is_object($post) && $post->post_type == TRX_ADDONS_CPT_MATCHES_PT;
			}
			if ($need_lists) {	
				// Detect current round
				$round = trx_addons_get_value_gp('round');
				if ((int) $round == 0 && $post != null) $round = $post->post_parent;
				if ($round > 0) $round_meta = get_post_meta($round, 'trx_addons_options', true);
				// Detect current competition
				$competition = trx_addons_get_value_gp('competition');
				//if ((int) $competition == 0 && $post_id > 0) $competition = get_post_meta($post_id, 'trx_addons_competition', true);
				if ((int) $competition == 0 && $round > 0) {
					$competition_post = get_post($round);
					if (!empty($competition_post->post_parent)) $competition = $competition_post->post_parent;
				}
				if ($competition > 0) {
					$competition_meta = get_post_meta($competition, 'trx_addons_options', true);
					$players = trx_addons_get_list_posts(false, array(
																'post_type' => TRX_ADDONS_CPT_PLAYERS_PT,
																'post_parent' => $competition,
																'orderby' => 'title',
																'order' => 'ASC'
																)
														);
				}
				$posts = trx_addons_get_list_posts(false, array('post_type' => array('post', 'page'), 'orderby' => 'title', 'order' => 'ASC'));
			}
		}
		$meta_box = array(
			"date_start" => array(
				"title" => esc_html__("Start date",  'trx_addons'),
				"desc" => wp_kses_data( __("The start date of the match in format: yyyy-mm-dd", 'trx_addons') ),
				"std" => !empty($round_meta['date_start']) ? $round_meta['date_start'] : date('Y-m-d'),
				"type" => "date"
			),
			"time_start" => array(
				"title" => esc_html__("Start time",  'trx_addons'),
				"desc" => wp_kses_data( __("The start time of the match in 24-hours format: HH:mm", 'trx_addons') ),
				"std" => date('H:i'),
				"type" => "time"
			),
			"main_match" => array(
				"title" => esc_html__("The main match of the round",  'trx_addons'),
				"desc" => wp_kses_data( __("Check if this match is the main match of the round", 'trx_addons') ),
				"std" => 0,
				"type" => "checkbox"
			),
			"place" => array(
				"title" => esc_html__("Match location",  'trx_addons'),
				"desc" => wp_kses_data( __("Location (stadium, city, country, etc.) of the match", 'trx_addons') ),
				"std" => '',
				"type" => "text"
			),
			"player1" => array(
				"title" => esc_html__("Player 1",  'trx_addons'),
				"desc" => wp_kses_data( __("Select first player", 'trx_addons') ),
				"std" => '',
				"options" => $players,
				"type" => "select"
			),
			"player2" => array(
				"title" => esc_html__("Player 2",  'trx_addons'),
				"desc" => wp_kses_data( __("Select second player", 'trx_addons') ),
				"std" => '',
				"options" => $players,
				"type" => "select"
			),
			"score" => array(
				"title" => esc_html__("Match score",  'trx_addons'),
				"desc" => wp_kses_data( __("Enter the score of the match, separated by a colon. For example: 3:1", 'trx_addons') ),
				"std" => '',
				"type" => "text"
			),
			"points" => array(
				"title" => esc_html__("Points for players",  'trx_addons'),
				"desc" => wp_kses_data( __("Comma separated points, credited to players, based on the results of the match. For example: 3,0", 'trx_addons') ),
				"std" => '',
				"type" => "text"
			),
			"review" => array(
				"title" => esc_html__("Review",  'trx_addons'),
				"desc" => wp_kses_data( __("Select the post with match review", 'trx_addons') ),
				"std" => '',
				"options" => $posts,
				"type" => "select"
			),
		);
		// If this competition - is a mass sport (not pairs)
		if (!empty($competition_meta['type']) && $competition_meta['type'] == 'group') {
			unset($meta_box['player1']);
			unset($meta_box['player2']);
			unset($meta_box['score']);
			if (isset($players['none'])) unset($players['none']);
			trx_addons_array_insert_before($meta_box, 'points', array(
				"players" => array(
					"title" => esc_html__("Players",  'trx_addons'),
					"desc" => wp_kses_data( __("Check players of this match", 'trx_addons') ),
					"std" => array(),
					"options" => $players,
					"type" => "checklist"
				),
			));
			$meta_box['points']['type'] = 'textarea';
			$meta_box['points']['desc'] = wp_kses_data( __("Points, credited to players, based on the results of the match. Each value must be on new line! Number lines must be equivalent to the number players", 'trx_addons') );
		}
		trx_addons_meta_box_register(TRX_ADDONS_CPT_MATCHES_PT, $meta_box);
		
		// Register post type and taxonomy
		register_post_type(
			TRX_ADDONS_CPT_MATCHES_PT,
			apply_filters('trx_addons_filter_register_post_type',
				array(
					'label'               => esc_html__( 'Matches', 'trx_addons' ),
					'description'         => esc_html__( 'Matches Description', 'trx_addons' ),
					'labels'              => array(
						'name'                => esc_html__( 'Matches', 'trx_addons' ),
						'singular_name'       => esc_html__( 'Match', 'trx_addons' ),
						'menu_name'           => esc_html__( 'Matches', 'trx_addons' ),
						'parent_item_colon'   => esc_html__( 'Parent Item:', 'trx_addons' ),
						'all_items'           => esc_html__( 'All Matches', 'trx_addons' ),
						'view_item'           => esc_html__( 'View Match', 'trx_addons' ),
						'add_new_item'        => esc_html__( 'Add New Match', 'trx_addons' ),
						'add_new'             => esc_html__( 'Add New', 'trx_addons' ),
						'edit_item'           => esc_html__( 'Edit Match', 'trx_addons' ),
						'update_item'         => esc_html__( 'Update Match', 'trx_addons' ),
						'search_items'        => esc_html__( 'Search Matches', 'trx_addons' ),
						'not_found'           => esc_html__( 'Not found', 'trx_addons' ),
						'not_found_in_trash'  => esc_html__( 'Not found in Trash', 'trx_addons' ),
					),
					'supports'            => trx_addons_cpt_param('matches', 'supports'),
					'public'              => true,
					'hierarchical'        => false,
					'has_archive'         => true,
					'can_export'          => true,
					'show_in_admin_bar'   => true,
					'show_in_menu'        => false,
					'capability_type'     => 'post',
					'rewrite'             => array(
												'slug'         => trx_addons_cpt_param('matches', 'post_type_slug'),
												'with_front'   => false,
												'hierarchical' => false
											)
				),
				TRX_ADDONS_CPT_MATCHES_PT
			)
		);

		// Add rules to show matches
		add_rewrite_tag('%match%', '([^&]+)');
	}
}

	
// Add sort in the query for the matches
if ( !function_exists( 'trx_addons_cpt_matches_add_sort_order' ) ) {
	add_filter('trx_addons_filter_add_sort_order',	'trx_addons_cpt_matches_add_sort_order', 10, 3);
	function trx_addons_cpt_matches_add_sort_order($q, $orderby, $order='desc') {
		if ($orderby == 'match_date') {
			$q['order'] = $order;
			$q['orderby'] = 'meta_value';
			$q['meta_key'] = 'trx_addons_match_date';
		}
		return $q;
	}
}


// Save match's date for search, sorting, etc.
if ( !function_exists( 'trx_addons_cpt_matches_save_post_options' ) ) {
	add_filter('trx_addons_filter_save_post_options', 'trx_addons_cpt_matches_save_post_options', 10, 3);
	function trx_addons_cpt_matches_save_post_options($options, $post_id, $post_type) {
		if ($post_type == TRX_ADDONS_CPT_MATCHES_PT) {
			// Update match date and time as separate option (need for sorting posts)
			update_post_meta($post_id, 'trx_addons_match_date', $options['date_start'].' '.$options['time_start']);
			// If save a new match
			$post = get_post($post_id);
			if ( $post->post_parent == 0)
				trx_addons_update_post($post_id, array('post_parent' => trx_addons_get_value_gp('round')));
//			if ( (int) get_post_meta($post_id, 'trx_addons_competition', true) == 0) {
//				update_post_meta($post_id, 'trx_addons_competition', trx_addons_get_value_gp('competition'));
//				update_post_meta($post_id, 'trx_addons_round', trx_addons_get_value_gp('round'));
//			}
			// Update players points
			$points = explode(',', str_replace(array(' ', "\r", "\n", ":", "-"), array('', '', ',', ',', ','), $options['points']));
			$players = array();
			// If pair competition - add players to the list
			$points_pos = 0;
			if (empty($options['players'])) {
				if ($options['player1'] > 0) $players[$options['player1']] = isset($points[$points_pos]) ? $points[$points_pos++] : 0;
				if ($options['player2'] > 0) $players[$options['player2']] = isset($points[$points_pos]) ? $points[$points_pos++] : 0;
	
			// Else - get list from options
			} else {
				foreach ($options['players'] as $id=>$v)
					if ($v == 1) $players[$id] = isset($points[$points_pos]) ? $points[$points_pos++] : 0;
			}
	
			// Store points to the players
			foreach ($players as $player_id=>$player_points) {
				$points_data = get_post_meta($player_id, 'trx_addons_player_points_data', true);
				if (!is_array($points_data)) $points_data = array();
				if (empty($options['points'])) {
					if (isset($points_data[$post_id])) {
						unset($points_data[$post_id]);
						update_post_meta($player_id, 'trx_addons_player_points_data', $points_data);
						update_post_meta($player_id, 'trx_addons_player_points', array_sum($points_data));
					}
				} else {
					$points_data[$post_id] = $player_points;
					update_post_meta($player_id, 'trx_addons_player_points_data', $points_data);
					update_post_meta($player_id, 'trx_addons_player_points', array_sum($points_data));
				}
			}
		}
		return $options;
	}
}


// Return true if it's matches page
if ( !function_exists( 'trx_addons_is_matches_page' ) ) {
	function trx_addons_is_matches_page() {
		return !is_search() && (
						(trx_addons_is_single() && get_post_type()==TRX_ADDONS_CPT_MATCHES_PT)
						|| is_post_type_archive(TRX_ADDONS_CPT_MATCHES_PT)
						);
	}
}


// Return links with parent competition and sports for the breadcrumbs
if ( !function_exists( 'trx_addons_cpt_matches_get_parents_links' ) ) {
	add_filter('trx_addons_filter_get_parents_links', 'trx_addons_cpt_matches_get_parents_links', 10, 2);
	function trx_addons_cpt_matches_get_parents_links($links='', $args=array()) {
		// Uncomment (remove false) next row if you want create links to the rounds and matches archives
		if (false) {
			$new_links = '';
			if (trx_addons_is_matches_page()) {
				global $post;
				$round = is_post_type_archive(TRX_ADDONS_CPT_MATCHES_PT) ? trx_addons_get_value_gp('round') : $post->post_parent;
				$round_post = (int) $round > 0 ? get_post($round) : null;
				if ($round_post) {
					if (trx_addons_is_single()) {
						$new_links = '<a href="'.esc_url(trx_addons_add_to_url(get_post_type_archive_link(TRX_ADDONS_CPT_MATCHES_PT), array('round'=>$round))).'">'.esc_html(get_the_title($round_post)).'</a>';
					}
					if (($competition = $round_post->post_parent) > 0) {
						$new_links = '<a href="'.esc_url(trx_addons_add_to_url(get_post_type_archive_link(TRX_ADDONS_CPT_ROUNDS_PT), array('competition'=>$competition))).'">'.esc_html(get_the_title($competition)).'</a>'
									. (!empty($new_links) ? $args['delimiter'] : '') . $new_links;
						$terms = get_the_terms($competition, TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY);
						$sport = is_array($terms) && count($terms)>0 ? $terms[0] : null;
						if ($sport) {
							$obj = get_post_type_object(TRX_ADDONS_CPT_COMPETITIONS_PT);
							$new_links = '<a href="'.esc_url(get_post_type_archive_link( TRX_ADDONS_CPT_COMPETITIONS_PT )).'">'.esc_html($obj->labels->all_items).'</a>'
								. $args['delimiter']
								. '<a href="'.esc_url(get_term_link($sport->term_id, $sport->taxonomy)).'">'.esc_html($sport->name).'</a>'
								. (!empty($new_links) ? $args['delimiter'] : '') . $new_links;
						}
					}
				}
				$links = $new_links;
			}
		}
		return $links;
	}
}

// Return link to the all posts for the breadcrumbs
if ( !function_exists( 'trx_addons_cpt_matches_get_blog_all_posts_link' ) ) {
	add_filter('trx_addons_filter_get_blog_all_posts_link', 'trx_addons_cpt_matches_get_blog_all_posts_link', 10, 2);
	function trx_addons_cpt_matches_get_blog_all_posts_link($link='', $args=array()) {
		if ($link=='') {
			if (trx_addons_is_matches_page() && !is_post_type_archive(TRX_ADDONS_CPT_MATCHES_PT)) {
				global $post;
				if (!empty($post->post_parent)) {
					$competition_post = get_post($post->post_parent);
					if (!empty($competition_post->post_parent)) {
						$competition = $competition_post->post_parent;
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
		}
		return $link;
	}
}

// Return current page title
if ( !function_exists( 'trx_addons_cpt_matches_get_blog_title' ) ) {
	add_filter( 'trx_addons_filter_get_blog_title', 'trx_addons_cpt_matches_get_blog_title');
	function trx_addons_cpt_matches_get_blog_title($title='') {
		if ( is_post_type_archive(TRX_ADDONS_CPT_MATCHES_PT) ) {
			$round = trx_addons_get_value_gp('round');
			if ((int) $round > 0)
				$title = get_the_title($round);
/*
			else {
				$obj = get_post_type_object(TRX_ADDONS_CPT_MATCHES_PT);
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
if ( !function_exists( 'trx_addons_cpt_matches_single_template' ) ) {
	add_filter('single_template', 'trx_addons_cpt_matches_single_template');
	function trx_addons_cpt_matches_single_template($template) {
		global $post;
		if (trx_addons_is_single() && $post->post_type == TRX_ADDONS_CPT_MATCHES_PT) {
			if ( ($template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'sport/tpl.single.matches.php')) == '' )
				$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'sport/tpl.single.php');
		}
		return $template;
	}
}

// Change standard archive template for the matches posts
if ( !function_exists( 'trx_addons_cpt_matches_archive_template' ) ) {
	add_filter('archive_template',	'trx_addons_cpt_matches_archive_template');
	function trx_addons_cpt_matches_archive_template( $template ) {
		if ( is_post_type_archive(TRX_ADDONS_CPT_MATCHES_PT) ) {
			if ( ($template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'sport/tpl.archive.matches.php')) == '' )
				$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'sport/tpl.archive.php');
		}
		return $template;
	}	
}



// Admin utils
// -----------------------------------------------------------------


// Show <input type="hidden"> with competition and <select> with rounds in the admin filters area
if (!function_exists('trx_addons_cpt_matches_admin_filters')) {
	add_action( 'restrict_manage_posts', 'trx_addons_cpt_matches_admin_filters' );
	function trx_addons_cpt_matches_admin_filters() {
		if ( get_query_var('post_type') != TRX_ADDONS_CPT_MATCHES_PT) return;
		$competition = trx_addons_get_value_gp('competition');
		$round = trx_addons_get_value_gp('round');
		if (empty($competition) || empty($round)) return;
		// Hidden field with current competition
		?><input type="hidden" name="competition" id="competition" class="postform" value="<?php echo esc_attr($competition); ?>"><?php
		// Select with list of the rounds from current competition
		if ( !($rounds_list = get_transient("trx_addons_rounds_filter_".trim($competition))) ) {
			$rounds_list = trx_addons_get_list_posts(false, array(
															'post_type'=>TRX_ADDONS_CPT_ROUNDS_PT,
															'post_parent'=>$competition
															//'meta_key'=>'trx_addons_competition',
															//'meta_value'=>$competition
															)
														);
			if (is_array($rounds_list) && count($rounds_list) > 0)
				set_transient("trx_addons_rounds_filter_".trim($competition), $rounds_list, 24*60*60);
		}
		$list = '';
		if (is_array($rounds_list) && count($rounds_list) > 0) {
			$list .= '<select name="round" id="round" class="postform">';
			foreach ($rounds_list as $id=>$title) {
				$list .= '<option value="'.esc_attr($id).'"'.($round==$id ? ' selected="selected"' : '').'>'.esc_html($title).'</option>';
			}
			$list .=  "</select>";
		}
		trx_addons_show_layout($list);
	}
}

// Clear competitions cache on the post save
if ( !function_exists( 'trx_addons_cpt_matches_admin_clear_cache_rounds' ) ) {
	add_filter('trx_addons_filter_save_post_options', 'trx_addons_cpt_matches_admin_clear_cache_rounds', 10, 3);
	function trx_addons_cpt_matches_admin_clear_cache_rounds($options, $post_id, $post_type) {
		if ($post_type == TRX_ADDONS_CPT_ROUNDS_PT) {
			//$competition = get_post_meta($post_id, 'trx_addons_competition', true);
			$post = get_post($post_id);
			$competition = $post->post_parent;
			if ( (int) $competition == 0 ) return;
			set_transient("trx_addons_rounds_filter_".trim($competition), '', 24*60*60);
		}
		return $options;
	}
}

// Create additional column in the posts list
if (!function_exists('trx_addons_cpt_matches_add_custom_column')) {
	add_filter('manage_edit-'.trx_addons_cpt_param('matches', 'post_type').'_columns',	'trx_addons_cpt_matches_add_custom_column', 9);
	function trx_addons_cpt_matches_add_custom_column( $columns ){
		if (is_array($columns) && count($columns)>0) {
			$new_columns = array();
			foreach($columns as $k=>$v) {
				$new_columns[$k] = $v;
				if ($k=='title') {
					$new_columns['cpt_matches_date'] = esc_html__('Start date', 'trx_addons');
				}
			}
			$columns = $new_columns;
		}
		return $columns;
	}
}

// Make column 'Date' sortable
if (!function_exists('trx_addons_cpt_matches_sort_custom_column')) {
	add_filter('manage_edit-'.trx_addons_cpt_param('matches', 'post_type').'_sortable_columns',	'trx_addons_cpt_matches_sort_custom_column');
	function trx_addons_cpt_matches_sort_custom_column( $columns ) {
		if (is_array($columns)) $columns['cpt_matches_date'] = array('trx_addons_match_date', trx_addons_get_value_gp('orderby')=='' || trx_addons_get_value_gp('orderby')=='trx_addons_match_date' && trx_addons_get_value_gp('order')=='asc');
		return $columns;
	}
}

// Fill custom columns in the posts list
if (!function_exists('trx_addons_cpt_matches_fill_custom_column')) {
	add_action('manage_'.trx_addons_cpt_param('matches', 'post_type').'_posts_custom_column',	'trx_addons_cpt_matches_fill_custom_column', 9, 2);
	function trx_addons_cpt_matches_fill_custom_column($column_name='', $post_id=0) {
		if ($column_name == 'cpt_matches_date') {
			// Show match's details
			$meta = (array)get_post_meta($post_id, 'trx_addons_options', true);
			if (!empty($meta['date_start'])) {
				?><div class="trx_addons_meta_row">
					<span class="trx_addons_meta_label"><?php esc_html_e('Start:', 'trx_addons'); ?></span>
					<span class="trx_addons_meta_data"><?php echo date_i18n(get_option('date_format').' '.get_option('time_format'), strtotime($meta['date_start'].' '.$meta['time_start'])); ?></span>
				</div><?php
			}
			if (!empty($meta['place'])) {
				?><div class="trx_addons_meta_row">
					<span class="trx_addons_meta_label"><?php esc_html_e('Place:', 'trx_addons'); ?></span>
					<span class="trx_addons_meta_data"><?php echo esc_html($meta['place']); ?></span>
				</div><?php
			}
			if (!empty($meta['score'])) {
				?><div class="trx_addons_meta_row">
					<span class="trx_addons_meta_data trx_addons_meta_match_score"><?php echo nl2br(esc_html($meta['score'])); ?></span>
				</div><?php
			}
		}
	}
}
