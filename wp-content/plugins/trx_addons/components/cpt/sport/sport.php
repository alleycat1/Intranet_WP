<?php
/**
 * ThemeREX Addons: Sports Reviews Management (SRM).
 *                  Support different sports, championships, rounds, matches and players.
 *
 * @package ThemeREX Addons
 * @since v1.6.17
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ($fdir = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . "sport/sport.competitions.php")) != '') { include_once $fdir; }
if ( ($fdir = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . "sport/sport.rounds.php")) != '') { include_once $fdir; }
if ( ($fdir = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . "sport/sport.matches.php")) != '') { include_once $fdir; }
if ( ($fdir = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . "sport/sport.players.php")) != '') { include_once $fdir; }

// -----------------------------------------------------------------
// -- Custom post type registration
// -----------------------------------------------------------------

// Add Admin menu item to show Sports management panel
if (!function_exists('trx_addons_cpt_sport_admin_menu')) {
	add_action( 'admin_menu', 'trx_addons_cpt_sport_admin_menu' );
	function trx_addons_cpt_sport_admin_menu() {
		add_menu_page(
			esc_html__('Sport', 'trx_addons'),	//page_title
			esc_html__('Sport', 'trx_addons'),	//menu_title
			'edit_posts',						//capability
			'trx_addons_sport',					//menu_slug
			'trx_addons_sport_page',			//callback
			'dashicons-universal-access'		//icon
			// From WordPress 5.3 'menu_position' must be only integer or null (default)!
			//'53.7'								//menu position
		);
	}
}

/* ------------------- Old way - moved to the cpt.php now ---------------------
// Add 'Sport' parameters in the ThemeREX Addons Options
if (!function_exists('trx_addons_cpt_sport_options')) {
	add_filter( 'trx_addons_filter_options', 'trx_addons_cpt_sport_options');
	function trx_addons_cpt_sport_options($options) {
		trx_addons_array_insert_after($options, 'cpt_section', trx_addons_cpt_sport_get_list_options());
		return $options;
	}
}

// Return parameters list for plugin's options
if (!function_exists('trx_addons_cpt_sport_get_list_options')) {
	function trx_addons_cpt_sport_get_list_options($add_parameters=array()) {
		return apply_filters('trx_addons_cpt_list_options', array(
			'sport_info' => array(
				"title" => esc_html__('Sport', 'trx_addons'),
				"desc" => wp_kses_data( __('Settings of the sport reviews system', 'trx_addons') ),
				"type" => "info"
			),
			'sport_favorite' => array(
				"title" => esc_html__('Default sport', 'trx_addons'),
				"desc" => wp_kses_data( __('Select default sport for the shortcodes editor', 'trx_addons') ),
				"std" => '',
				"options" => is_admin() ? trx_addons_get_list_terms(false, TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY) : array(),
				"type" => "select"
			),
			'competitions_style' => array(
				"title" => esc_html__('Style', 'trx_addons'),
				"desc" => wp_kses_data( __('Style of the competitions archive', 'trx_addons') ),
				"std" => 'default_3',
				"options" => apply_filters('trx_addons_filter_cpt_archive_styles', 
											trx_addons_components_get_allowed_layouts('cpt', 'sport', 'arh'), 
											TRX_ADDONS_CPT_COMPETITIONS_PT),
				"type" => "select"
			)
		), 'sport');
	}
}
------------------- /Old way --------------------- */


// Return true if it's sport page
if ( !function_exists( 'trx_addons_is_sport_page' ) ) {
	function trx_addons_is_sport_page() {
		return defined('TRX_ADDONS_CPT_COMPETITIONS_PT') 
					&& !is_search()
					&& (
						(trx_addons_is_single() && in_array(get_post_type(), array(TRX_ADDONS_CPT_COMPETITIONS_PT,
																		TRX_ADDONS_CPT_ROUNDS_PT,
																		TRX_ADDONS_CPT_PLAYERS_PT,
																		TRX_ADDONS_CPT_MATCHES_PT)))
						|| is_post_type_archive(TRX_ADDONS_CPT_MATCHES_PT)
						|| is_post_type_archive(TRX_ADDONS_CPT_PLAYERS_PT)
						|| is_post_type_archive(TRX_ADDONS_CPT_ROUNDS_PT)
						|| is_post_type_archive(TRX_ADDONS_CPT_COMPETITIONS_PT)
						|| is_tax(TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY)
						);
	}
}


// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_cpt_sport_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_cpt_sport_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY);
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_cpt_sport_load_scripts_front', 10, 1 );
	function trx_addons_cpt_sport_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'cpt_sport', $force, array(
			'css'  => array(
				'trx_addons-cpt_sport' => array( 'src' => TRX_ADDONS_PLUGIN_CPT . 'sport/sport.css' ),
			),
			'js' => array(
				'trx_addons-cpt_sport' => array( 'src' => TRX_ADDONS_PLUGIN_CPT . 'sport/sport.js', 'deps' => 'jquery' ),
			),
			'need' => trx_addons_is_sport_page(),
			'check' => array(
				// Matches
				array( 'type' => 'sc',  'sc' => 'trx_sc_matches' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/matches' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_matches"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_sc_matches' ),
				// Points
				array( 'type' => 'sc',  'sc' => 'trx_sc_points' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/points' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_points"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_sc_points' ),
			)
		) );
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'trx_addons_cpt_sport_load_scripts_front_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_cpt_sport_load_scripts_front_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	add_action( 'trx_addons_action_load_scripts_front_cpt_sport', 'trx_addons_cpt_sport_load_scripts_front_responsive', 10, 1 );
	function trx_addons_cpt_sport_load_scripts_front_responsive( $force = false ) {
		trx_addons_enqueue_optimized_responsive( 'cpt_sport', $force, array(
			'css'  => array(
				'trx_addons-cpt_sport-responsive' => array(
					'src' => TRX_ADDONS_PLUGIN_CPT . 'sport/sport.responsive.css',
					'media' => 'md'
				),
			),
		) );
	}
}

// Merge shortcode's specific styles into single stylesheet
if ( !function_exists( 'trx_addons_cpt_sport_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_cpt_sport_merge_styles');
	function trx_addons_cpt_sport_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_CPT . 'sport/sport.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_cpt_sport_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_cpt_sport_merge_styles_responsive');
	function trx_addons_cpt_sport_merge_styles_responsive($list) {
		$list[ TRX_ADDONS_PLUGIN_CPT . 'sport/sport.responsive.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific scripts into single file
if ( !function_exists( 'trx_addons_cpt_sport_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_cpt_sport_merge_scripts');
	function trx_addons_cpt_sport_merge_scripts($list) {
		$list[ TRX_ADDONS_PLUGIN_CPT . 'sport/sport.js' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu
if ( !function_exists( 'trx_addons_cpt_sport_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_cpt_sport_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_cpt_sport_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_cpt_sport_check_in_html_output', 10, 1 );
	function trx_addons_cpt_sport_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*sc_(sport|matches|points)',
				'class=[\'"][^\'"]*type\\-('  . TRX_ADDONS_CPT_MATCHES_PT
										. '|' . TRX_ADDONS_CPT_PLAYERS_PT
										. '|' . TRX_ADDONS_CPT_ROUNDS_PT
										. '|' . TRX_ADDONS_CPT_COMPETITIONS_PT
										. ')',
				'class=[\'"][^\'"]*' . TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY . '\\-',
			)
		);
		if ( trx_addons_check_in_html_output( 'cpt_sport', $content, $args ) ) {
			trx_addons_cpt_sport_load_scripts_front( true );
		}
		return $content;
	}
}


// Load required styles and scripts for the backend
if ( !function_exists( 'trx_addons_cpt_sport_load_scripts_admin' ) ) {
	add_action("admin_enqueue_scripts", 'trx_addons_cpt_sport_load_scripts_admin');
	function trx_addons_cpt_sport_load_scripts_admin() {
		wp_enqueue_script('trx_addons-cpt_sport', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT . 'sport/sport.admin.js'), array('jquery'), null, true );
	}
}


// Admin utils
// -----------------------------------------------------------------

// Add query vars to filter posts
if (!function_exists('trx_addons_cpt_sport_pre_get_posts')) {
	add_action( 'pre_get_posts', 'trx_addons_cpt_sport_pre_get_posts' );
	function trx_addons_cpt_sport_pre_get_posts($query) {
		if (!$query->is_main_query()) return;
		$post_type = $query->get('post_type');
		// Filters and sort for the admin lists
		if (is_admin()) {
			$orderby = trx_addons_get_value_gp('orderby');
			$order = trx_addons_get_value_gp('order');
			if ($post_type == TRX_ADDONS_CPT_COMPETITIONS_PT) {
				// Sort competitions by start date
				if (empty($orderby) || $orderby=='trx_addons_competition_date') {
					$query->set('meta_key', 'trx_addons_competition_date');
					$query->set('orderby', 'meta_value');
					$query->set('order', $order == 'desc' ? 'DESC' : 'ASC');
				}
			} else if (in_array($post_type, array(TRX_ADDONS_CPT_ROUNDS_PT, TRX_ADDONS_CPT_PLAYERS_PT))) {
				$competition = trx_addons_get_value_gp('competition');
				if ((int) $competition > 0) {
					//$query->set('meta_key', 'trx_addons_competition');
					//$query->set('meta_value', $competition);
					$query->set('post_parent', $competition);
					// Sort rounds by start date
					if ($post_type==TRX_ADDONS_CPT_ROUNDS_PT) {
						if (empty($orderby) || $orderby=='trx_addons_round_date') {
							$query->set('meta_key', 'trx_addons_round_date');
							$query->set('orderby', 'meta_value');
							$query->set('order', $order == 'desc' ? 'DESC' : 'ASC');
						}
					// Sort players
					} else {
						if (empty($orderby) || $orderby=='trx_addons_player_points') {
							$query->set('meta_key', 'trx_addons_player_points');
							$query->set('orderby', 'meta_value');
							$query->set('order', $order == 'asc' ? 'ASC' : 'DESC');
						}
					}
				}
			} else if ($post_type == TRX_ADDONS_CPT_MATCHES_PT) {
				$round = trx_addons_get_value_gp('round');
				if ((int) $round > 0) {
					//$query->set('meta_key', 'trx_addons_round');
					//$query->set('meta_value', $round);
					$query->set('post_parent', $round);
					// Sort matches by start date
					if (empty($orderby) || $orderby=='trx_addons_match_date') {
						$query->set('meta_key', 'trx_addons_match_date');
						$query->set('orderby', 'meta_value');
						$query->set('order', $order == 'desc' ? 'DESC' : 'ASC');
					}
				}
				
			}

		// Filters and sort for the foreground lists
		} else {
			if ($post_type == TRX_ADDONS_CPT_COMPETITIONS_PT) {
				$sport = trx_addons_get_value_gp('sport');
				// Filter competitions by sport
				if (!empty($sport)) {
				}
				$query->set('meta_key', 'trx_addons_competition_date');
				$query->set('orderby', 'meta_value');
				$query->set('order', 'ASC');
			} else if (in_array($post_type, array(TRX_ADDONS_CPT_ROUNDS_PT, TRX_ADDONS_CPT_PLAYERS_PT))) {
				$competition = trx_addons_get_value_gp('competition');
				// Filter rounds and players by competition
				if ((int) $competition > 0) {
					$query->set('post_parent', $competition);
					// Sort rounds by start date
					if ($post_type==TRX_ADDONS_CPT_ROUNDS_PT) {
						$query->set('meta_key', 'trx_addons_round_date');
						$query->set('orderby', 'meta_value');
						$query->set('order', 'ASC');
					// Sort players
					} else {
						$query->set('meta_key', 'trx_addons_player_points');
						$query->set('orderby', 'meta_value');
						$query->set('order', 'DESC');
					}
				}
			} else if ($post_type == TRX_ADDONS_CPT_MATCHES_PT) {
				$round = trx_addons_get_value_gp('round');
				if ((int) $round > 0) {
					$query->set('post_parent', $round);
					$query->set('meta_key', 'trx_addons_match_date');
					$query->set('orderby', 'meta_value');
					$query->set('order', 'ASC');
				}
			}
		}
	}
}

// Show breadcrumbs in the admin notices
if ( !function_exists( 'trx_addons_cpt_sport_admin_notice' ) ) {
	add_action('admin_notices', 'trx_addons_cpt_sport_admin_notice', 1);
	function trx_addons_cpt_sport_admin_notice() {
		if (in_array(trx_addons_get_value_gp('action'), array('vc_load_template_preview'))) return;
		$screen = function_exists('get_current_screen') ? get_current_screen() : false;
		if (!is_object($screen) || !in_array($screen->post_type, array(TRX_ADDONS_CPT_COMPETITIONS_PT, TRX_ADDONS_CPT_ROUNDS_PT, TRX_ADDONS_CPT_PLAYERS_PT, TRX_ADDONS_CPT_MATCHES_PT)) || $screen->base=='edit-tags') return;
		global $post;
		?>
		<div id="trx_addons_sport_breadcrumbs" class="notice notice-info">
			<h3 class="trx_addons_sport_breadcrumbs_title">
				<?php
				if ($screen->post_type == TRX_ADDONS_CPT_COMPETITIONS_PT) {
					$sport = trx_addons_get_value_gp('cpt_competitions_sports');
					if ( empty($sport) ) {
						$terms = get_the_terms($post->ID, TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY);
						if (is_array($terms) && count($terms)>0) $sport = $terms[0];
					} else {
						$sport = get_term_by('slug', $sport, TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY);
					}
					if (is_object($sport)) {
						if (substr($screen->id, 0, 5)!='edit-') {		// Edit single competition
							?><a href="<?php echo esc_url(get_admin_url(null, 'edit.php?post_type='.TRX_ADDONS_CPT_COMPETITIONS_PT.'&'.TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY.'='.$sport->slug)); ?>" class="trx_addons_sport_breadcrumbs_item"><?php echo esc_html($sport->name); ?></a><span class="trx_addons_sport_breadcrumbs_item"><?php echo esc_html($post->post_title); ?></span><?php
						} else {										// List of competitions
							?><span class="trx_addons_sport_breadcrumbs_item"><?php echo esc_html($sport->name); ?></span><?php
						}
					}

				} else if ( in_array($screen->post_type, array(TRX_ADDONS_CPT_ROUNDS_PT, TRX_ADDONS_CPT_PLAYERS_PT, TRX_ADDONS_CPT_MATCHES_PT)) ) {
					// Detect round
					$round = null;
					if ($screen->post_type == TRX_ADDONS_CPT_MATCHES_PT) {
						$round = trx_addons_get_value_gp('round');
						//if ( (int) $round == 0) $round = get_post_meta($post->ID, 'trx_addons_round', true);
						if ( (int) $round == 0) $round = $post->post_parent;
						$round = get_post($round);
					}
					// Detect competition
					$competition = trx_addons_get_value_gp('competition');
					//if ( (int) $competition == 0) $competition = get_post_meta($post->ID, 'trx_addons_competition', true);
					if ( (int) $competition == 0) $competition = is_object($round) ? $round->post_parent : $post->post_parent;
					$competition = get_post($competition);
					// Detect sport
					$terms = get_the_terms($competition->ID, TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY);
					$sport = is_array($terms) && count($terms)>0 ? $terms[0] : null;
					if (is_object($sport)) {
						?><a href="<?php echo esc_url(get_admin_url(null, 'edit.php?post_type='.TRX_ADDONS_CPT_COMPETITIONS_PT.'&'.TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY.'='.$sport->slug)); ?>" class="trx_addons_sport_breadcrumbs_item"><?php echo esc_html($sport->name); ?></a><?php
					}
					if (is_object($competition)) {
						// Competition link
						if (substr($screen->id, 0, 5)!='edit-' || $screen->post_type == TRX_ADDONS_CPT_MATCHES_PT) {
							?><a href="<?php echo esc_url(get_admin_url(null, 'edit.php?post_type='.($screen->post_type == TRX_ADDONS_CPT_PLAYERS_PT ? TRX_ADDONS_CPT_PLAYERS_PT : TRX_ADDONS_CPT_ROUNDS_PT).'&competition='.intval($competition->ID))); ?>" class="trx_addons_sport_breadcrumbs_item"><?php echo esc_html($competition->post_title); ?></a><?php
							// Round link
							if ($screen->post_type == TRX_ADDONS_CPT_MATCHES_PT) {
								if (substr($screen->id, 0, 5)!='edit-') {
									?><a href="<?php echo esc_url(get_admin_url(null, 'edit.php?post_type='.TRX_ADDONS_CPT_MATCHES_PT.'&competition='.intval($competition->ID).'&round='.intval($round->ID))); ?>" class="trx_addons_sport_breadcrumbs_item"><?php echo esc_html($round->post_title); ?></a><?php
								} else {											// List of matches
									// Current round title
									?><span class="trx_addons_sport_breadcrumbs_item"><?php echo esc_html($round->post_title); ?></span><?php
								}
							} else {
								if (substr($screen->id, 0, 5)!='edit-') {			// Edit single round/player
									// Current round/player/match title
									?><span class="trx_addons_sport_breadcrumbs_item"><?php echo !empty($post->post_title) ? esc_html($post->post_title) : esc_html__('New item', 'trx_addons'); ?></span><?php
								}
							}
						} else {													// List of rounds/players
							// Current competition title
							?><span class="trx_addons_sport_breadcrumbs_item"><?php echo esc_html($competition->post_title); ?></span><?php
						}
					}
				}
				?>
			</h3>
		</div>
		<?php
	}
}


// Get list competitions by specified sport
if ( !function_exists( 'trx_addons_cpt_sport_refresh_list_competitions' ) ) {
	add_filter('trx_addons_filter_refresh_list_competitions', 'trx_addons_cpt_sport_refresh_list_competitions', 10, 3);
	function trx_addons_cpt_sport_refresh_list_competitions($list, $sport, $not_selected=false) {
		return trx_addons_get_list_posts(false, array(
													'post_type' => TRX_ADDONS_CPT_COMPETITIONS_PT,
													'taxonomy' => TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY,
													'taxonomy_value' => $sport,
													'meta_key' => 'trx_addons_competition_date',
													'orderby' => 'meta_value',
													'order' => 'ASC',
													'not_selected' => $not_selected
													));
	}
}


// Get list rounds by specified competition
if ( !function_exists( 'trx_addons_cpt_sport_refresh_list_rounds' ) ) {
	add_filter('trx_addons_filter_refresh_list_rounds', 'trx_addons_cpt_sport_refresh_list_rounds', 10, 3);
	function trx_addons_cpt_sport_refresh_list_rounds($list, $competition, $not_selected=false) {
		return trx_addons_array_merge(array(
											'last' => esc_html__('Last round', 'trx_addons'),
											'next' => esc_html__('Next round', 'trx_addons')
											),
										trx_addons_get_list_posts(false, array(
													'post_type' => TRX_ADDONS_CPT_ROUNDS_PT,
													'post_parent' => $competition,
													'meta_key' => 'trx_addons_round_date',
													'orderby' => 'meta_value',
													'order' => 'ASC',
													'not_selected' => $not_selected
													))
		);
	}
}


// Add shortcodes
//----------------------------------------------------------------------------
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'sport/sport-sc.php';

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'sport/sport-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'sport/sport-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'sport/sport-sc-vc.php';
}
