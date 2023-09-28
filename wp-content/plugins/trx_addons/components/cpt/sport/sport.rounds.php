<?php
/**
 * ThemeREX Addons: Rounds support in the Sports Reviews Management
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
if ( ! defined('TRX_ADDONS_CPT_ROUNDS_PT') )		define('TRX_ADDONS_CPT_ROUNDS_PT', trx_addons_cpt_param('rounds', 'post_type'));

// Register post types and taxonomies
if (!function_exists('trx_addons_cpt_rounds_init')) {
	add_action( 'init', 'trx_addons_cpt_rounds_init' );
	function trx_addons_cpt_rounds_init() {
		// Check if we need to prepare lists
		$need = false;
		$post = null;
		if (is_admin()) {
			$need = strpos($_SERVER['REQUEST_URI'], 'post-new.php')!==false && trx_addons_get_value_gp('post_type')==TRX_ADDONS_CPT_ROUNDS_PT;
			if (!$need && strpos($_SERVER['REQUEST_URI'], 'post.php')!==false && ($post_id = (int) trx_addons_get_value_gp('post')) > 0) {
				$post = get_post($post_id);
				$need = is_object($post) && $post->post_type == TRX_ADDONS_CPT_ROUNDS_PT;
			}
			if ($need) {
				$competition = trx_addons_get_value_gp('competition');
				//if ((int) $competition == 0) $competition = get_post_meta($post_id, 'trx_addons_competition', true);
				if ((int) $competition == 0 && $post!=null) $competition = $post->post_parent;
				$competition_meta = get_post_meta($competition, 'trx_addons_options', true);
			}
		}
		// Add parameters to the Meta Box support
		trx_addons_meta_box_register(TRX_ADDONS_CPT_ROUNDS_PT, array(
			"date_start" => array(
				"title" => esc_html__("Start date",  'trx_addons'),
				"desc" => wp_kses_data( __("Start date in format: yyyy-mm-dd", 'trx_addons') ),
				"std" => !empty($competition_meta['date_start']) ? $competition_meta['date_start'] : date('Y-m-d'),
				"type" => "date"
			),
			"date_end" => array(
				"title" => esc_html__("End date",  'trx_addons'),
				"desc" => wp_kses_data( __("End date in format: yyyy-mm-dd", 'trx_addons') ),
				"std" => !empty($competition_meta['date_start']) ? $competition_meta['date_start'] : date('Y-m-d'),
				"type" => "date"
			)
		));
		
		// Register post type and taxonomy
		register_post_type(
			TRX_ADDONS_CPT_ROUNDS_PT,
			apply_filters('trx_addons_filter_register_post_type',
				array(
					'label'               => esc_html__( 'Rounds', 'trx_addons' ),
					'description'         => esc_html__( 'Rounds Description', 'trx_addons' ),
					'labels'              => array(
						'name'                => esc_html__( 'Rounds', 'trx_addons' ),
						'singular_name'       => esc_html__( 'Round', 'trx_addons' ),
						'menu_name'           => esc_html__( 'Rounds', 'trx_addons' ),
						'parent_item_colon'   => esc_html__( 'Parent Item:', 'trx_addons' ),
						'all_items'           => esc_html__( 'All Rounds', 'trx_addons' ),
						'view_item'           => esc_html__( 'View Round', 'trx_addons' ),
						'add_new_item'        => esc_html__( 'Add New Round', 'trx_addons' ),
						'add_new'             => esc_html__( 'Add New', 'trx_addons' ),
						'edit_item'           => esc_html__( 'Edit Round', 'trx_addons' ),
						'update_item'         => esc_html__( 'Update Round', 'trx_addons' ),
						'search_items'        => esc_html__( 'Search Rounds', 'trx_addons' ),
						'not_found'           => esc_html__( 'Not found', 'trx_addons' ),
						'not_found_in_trash'  => esc_html__( 'Not found in Trash', 'trx_addons' ),
					),
					'supports'            => trx_addons_cpt_param('rounds', 'supports'),
					'public'              => true,
					'hierarchical'        => false,
					'has_archive'         => true,
					'can_export'          => true,
					'show_in_admin_bar'   => true,
					'show_in_menu'        => false,
					'capability_type'     => 'post',
					'rewrite'             => array(
												'slug'         => trx_addons_cpt_param('rounds', 'post_type_slug'),
												'with_front'   => false,
												'hierarchical' => false
											)
				),
				TRX_ADDONS_CPT_ROUNDS_PT
			)
		);

		// Add rules to show rounds
		add_rewrite_tag('%round%', '([^&]+)');
 	}
}

	
// Add sort in the query for the rounds
if ( !function_exists( 'trx_addons_cpt_rounds_add_sort_order' ) ) {
	add_filter('trx_addons_filter_add_sort_order',	'trx_addons_cpt_rounds_add_sort_order', 10, 3);
	function trx_addons_cpt_rounds_add_sort_order($q, $orderby, $order='desc') {
		if ($orderby == 'round_date') {
			$q['order'] = $order;
			$q['orderby'] = 'meta_value';
			$q['meta_key'] = 'trx_addons_round_date';
		}
		return $q;
	}
}


// Save competition's date for search, sorting, etc.
if ( !function_exists( 'trx_addons_cpt_rounds_save_post_options' ) ) {
	add_filter('trx_addons_filter_save_post_options', 'trx_addons_cpt_rounds_save_post_options', 10, 3);
	function trx_addons_cpt_rounds_save_post_options($options, $post_id, $post_type) {
		if ($post_type == TRX_ADDONS_CPT_ROUNDS_PT) {
			update_post_meta($post_id, 'trx_addons_round_date', $options['date_start']);
			// If save a new round
			//if ( (int) get_post_meta($post_id, 'trx_addons_competition', true) == 0)
				//update_post_meta($post_id, 'trx_addons_competition', trx_addons_get_value_gp('competition'));
			$post = get_post($post_id);
			if ( $post->post_parent == 0)
				trx_addons_update_post($post_id, array('post_parent' => trx_addons_get_value_gp('competition')));
		}
		return $options;
	}
}


// Return true if it's rounds page
if ( !function_exists( 'trx_addons_is_rounds_page' ) ) {
	function trx_addons_is_rounds_page() {
		return !is_search() && (
						(trx_addons_is_single() && get_post_type()==TRX_ADDONS_CPT_ROUNDS_PT)
						|| is_post_type_archive(TRX_ADDONS_CPT_ROUNDS_PT)
						);
	}
}

// Return links with parent competition and sports for the breadcrumbs
if ( !function_exists( 'trx_addons_cpt_rounds_get_parents_links' ) ) {
	add_filter('trx_addons_filter_get_parents_links', 'trx_addons_cpt_rounds_get_parents_links', 10, 2);
	function trx_addons_cpt_rounds_get_parents_links($links='', $args=array()) {
		if (is_post_type_archive(TRX_ADDONS_CPT_ROUNDS_PT)) {
			$competition = trx_addons_get_value_gp('competition');
			if ((int)$competition > 0) {
				$terms = get_the_terms($competition, TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY);
				$sport = is_array($terms) && count($terms)>0 ? $terms[0] : null;
				if ($sport)
					$links .= '<a href="'.esc_url(get_post_type_archive_link( TRX_ADDONS_CPT_COMPETITIONS_PT )).'">'.esc_html__('All Competitions', 'trx_addons').'</a>'
							. $args['delimiter']
							. '<a href="'.esc_url(get_term_link($sport->term_id, $sport->taxonomy)).'">'.esc_html($sport->name).'</a>'
							. (!empty($links) ? $args['delimiter'] : '') . $links;
			}
		}
		return $links;
	}
}

// Return link to the all posts for the breadcrumbs
if ( !function_exists( 'trx_addons_cpt_rounds_get_blog_all_posts_link' ) ) {
	add_filter('trx_addons_filter_get_blog_all_posts_link', 'trx_addons_cpt_rounds_get_blog_all_posts_link', 10, 2);
	function trx_addons_cpt_rounds_get_blog_all_posts_link($link='', $args=array()) {
		if ($link=='') {
			if (trx_addons_is_rounds_page() && !is_post_type_archive(TRX_ADDONS_CPT_ROUNDS_PT)) {
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
									?  '<a href="'.esc_url($url_tx).'">'.esc_html($sport->name).'</a>'
									: '');
					}
				}
			}
		}
		return $link;
	}
}


// Return current page title
if ( !function_exists( 'trx_addons_cpt_rounds_get_blog_title' ) ) {
	add_filter( 'trx_addons_filter_get_blog_title', 'trx_addons_cpt_rounds_get_blog_title');
	function trx_addons_cpt_rounds_get_blog_title($title='') {
		if ( is_post_type_archive(TRX_ADDONS_CPT_ROUNDS_PT) ) {
			$competition = trx_addons_get_value_gp('competition');
			if ((int) $competition > 0)
				$title = get_the_title($competition);
/*
			else {
				$obj = get_post_type_object(TRX_ADDONS_CPT_ROUNDS_PT);
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
if ( !function_exists( 'trx_addons_cpt_rounds_single_template' ) ) {
	add_filter('single_template', 'trx_addons_cpt_rounds_single_template');
	function trx_addons_cpt_rounds_single_template($template) {
		global $post;
		if (trx_addons_is_single() && $post->post_type == TRX_ADDONS_CPT_ROUNDS_PT) {
			if ( ($template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'sport/tpl.single.rounds.php')) == '' )
				$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'sport/tpl.single.php');
		}
		return $template;
	}
}

// Change standard archive template for the matches posts
if ( !function_exists( 'trx_addons_cpt_rounds_archive_template' ) ) {
	add_filter('archive_template',	'trx_addons_cpt_rounds_archive_template');
	function trx_addons_cpt_rounds_archive_template( $template ) {
		if ( is_post_type_archive(TRX_ADDONS_CPT_ROUNDS_PT) ) {
			if ( ($template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'sport/tpl.archive.rounds.php')) == '' )
				$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'sport/tpl.archive.php');
		}
		return $template;
	}	
}



// Admin utils
// -----------------------------------------------------------------

// Create additional column in the posts list
if (!function_exists('trx_addons_cpt_rounds_add_custom_column')) {
	add_filter('manage_edit-'.trx_addons_cpt_param('rounds', 'post_type').'_columns',	'trx_addons_cpt_rounds_add_custom_column', 9);
	function trx_addons_cpt_rounds_add_custom_column( $columns ){
		if (is_array($columns) && count($columns)>0) {
			$new_columns = array();
			foreach($columns as $k=>$v) {
				$new_columns[$k] = $v;
				if ($k=='title') {
					$new_columns['cpt_rounds_date'] = esc_html__('Start date', 'trx_addons');
				}
			}
			$columns = $new_columns;
		}
		return $columns;
	}
}

// Make column 'Date' sortable
if (!function_exists('trx_addons_cpt_rounds_sort_custom_column')) {
	add_filter('manage_edit-'.trx_addons_cpt_param('rounds', 'post_type').'_sortable_columns',	'trx_addons_cpt_rounds_sort_custom_column');
	function trx_addons_cpt_rounds_sort_custom_column( $columns ) {
		if (is_array($columns)) $columns['cpt_rounds_date'] = array('trx_addons_round_date', trx_addons_get_value_gp('orderby')=='' || trx_addons_get_value_gp('orderby')=='trx_addons_round_date' && trx_addons_get_value_gp('order')=='asc');
		return $columns;
	}
}

// Fill custom columns in the posts list
if (!function_exists('trx_addons_cpt_rounds_fill_custom_column')) {
	add_action('manage_'.trx_addons_cpt_param('rounds', 'post_type').'_posts_custom_column',	'trx_addons_cpt_rounds_fill_custom_column', 9, 2);
	function trx_addons_cpt_rounds_fill_custom_column($column_name='', $post_id=0) {
		if ($column_name == 'cpt_rounds_date') {
			// Show start and end date of the round
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
			$competition = trx_addons_get_value_gp('competition');
			?><div class="trx_addons_meta_row">
				<a href="<?php echo esc_url(get_admin_url(null, 'edit.php?post_type='.TRX_ADDONS_CPT_MATCHES_PT.'&competition='.intval($competition).'&round='.intval($post_id))); ?>" class="button-primary"><?php esc_html_e('Matches', 'trx_addons'); ?></a>
			</div><?php
		}
	}
}
