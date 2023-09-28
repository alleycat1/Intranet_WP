<?php
/**
 * Shortcode: Display Search form
 *
 * @package ThemeREX Addons
 * @since v1.6.08
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_cpt_layouts_search_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_cpt_layouts_search_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY);
	function trx_addons_cpt_layouts_search_load_scripts_front() {
		if (trx_addons_exists_page_builder() && trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-sc_layouts-search', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'search/search.css'), array(), null );
		}
	}
}
	
// Merge shortcode specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_layouts_search_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_sc_layouts_search_merge_styles');
	add_filter("trx_addons_filter_merge_styles_layouts", 'trx_addons_sc_layouts_search_merge_styles');
	function trx_addons_sc_layouts_search_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'search/search.css' ] = true;
		return $list;
	}
}

	
// Merge shortcode's specific scripts into single file
if ( !function_exists( 'trx_addons_sc_layouts_search_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_sc_layouts_search_merge_scripts');
	function trx_addons_sc_layouts_search_merge_scripts($list) {
		$list[ TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'search/search.js' ] = true;
		return $list;
	}
}

// Add 'Search' form
if (!function_exists('trx_addons_add_search_form')) {
	add_action('trx_addons_action_search', 'trx_addons_add_search_form', 10, 1);
	function trx_addons_add_search_form($args=array()) {
		$args = array_merge(
					array(
						'style' => 'normal',
						'class' => '',
						'ajax'  => true,
						'post_types' => ''
					),
					$args
				);

		if (trx_addons_is_on(trx_addons_get_option('debug_mode')))
			wp_enqueue_script( 'trx_addons-sc_layouts_search', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'search/search.js'), array('jquery'), null, true );

		trx_addons_get_template_part( 'templates/tpl.search-form.php', 'trx_addons_args_search', $args );
	}
}


// Get and prepare param 'post_types' from GET params
if ( !function_exists('trx_addons_sc_layouts_search_get_param_post_types')) {
	function trx_addons_sc_layouts_search_get_param_post_types() {
		$post_types = trx_addons_get_value_gp('post_types');
		if ( is_array( $post_types ) ) {
			$post_types = join( ',', $post_types );
		}
		return str_replace(' ', '', $post_types);
	}
}


// Add GET param 'post_type' to filter posts if the param 'post_types' contain a single value
if ( !function_exists('trx_addons_sc_layouts_search_add_get_param')) {
	add_action( 'init', 'trx_addons_sc_layouts_search_add_get_param', 1 );
	function trx_addons_sc_layouts_search_add_get_param() {
		if ( ! empty( $_GET['post_types'] ) && ! empty( $_GET['s'] ) ) {
			$post_types = trx_addons_sc_layouts_search_get_param_post_types();
			if ( strpos( $post_types, ',' ) === false ) {
				$_GET['post_type'] = $post_types;
			}
		}
	}
}


// Add param 'post_type' to query vars to filter posts if it's not AJAX request and param 'post_types' contain multiple value
if ( !function_exists('trx_addons_sc_layouts_search_pre_get_posts')) {
	add_action( 'pre_get_posts', 'trx_addons_sc_layouts_search_pre_get_posts' );
	function trx_addons_sc_layouts_search_pre_get_posts( $query ) {
		if ( ! $query->is_main_query()
			|| ! empty( $query->is_admin )
			|| ( wp_doing_ajax() && empty( $_GET['paged'] ) )
		) {
			return;
		}
		if ( ! empty( $_GET['post_types'] ) && ! empty( $_GET['s'] ) ) {
			$post_types = trx_addons_sc_layouts_search_get_param_post_types();
			if ( strpos( $post_types, ',' ) !== false ) {
				$q = $query->get( 'post_type' );
				if ( ! is_array( $q ) ) {
					if ( empty( $q ) ) {
						$q = array();
					} else {
						$q = array( $q );
					}
				}
				$post_types = explode( ',', $post_types );
				foreach( $post_types as $pt ) {
					if ( ! in_array( $pt, $q ) ) {
						$q[] = $pt;
					}
				}
				$query->set( 'post_type', $q );
			}
		}
	}
}


// AJAX incremental search
if ( !function_exists( 'trx_addons_callback_ajax_search' ) ) {
	add_action('wp_ajax_ajax_search',			'trx_addons_callback_ajax_search');
	add_action('wp_ajax_nopriv_ajax_search',	'trx_addons_callback_ajax_search');
	function trx_addons_callback_ajax_search() {

		trx_addons_verify_nonce();

		$response = array('error'=>'', 'data' => '');
		
		$s = $_REQUEST['text'];
		$post_types = trx_addons_sc_layouts_search_get_param_post_types();
	
		if ( ! empty( $s ) ) {
			$args = array(
				'post_status' => 'publish',
				'orderby' => 'date',
				'order' => 'desc', 
				'posts_per_page' => 4,
				's' => esc_html($s),
			);	
			if ( ! empty( $post_types ) ) {
				$args['post_type'] = strpos( $post_types, ',' ) !== false ? explode( ',', $post_types ) : $post_types;
			}

			$args = apply_filters( 'trx_addons_filter_query_args', $args, 'sc_layouts_search' );

			do_action( 'trx_addons_action_add_terms_to_search' );

			$query = new WP_Query( apply_filters( 'trx_addons_ajax_search_query', $args ) );

			set_query_var( 'trx_addons_output_widgets_posts', '' );

			$post_number = 0;
			while ( $query->have_posts() ) { $query->the_post();
				$post_number++;
				trx_addons_get_template_part('templates/tpl.posts-list.php',
												'trx_addons_args_widgets_posts', 
												array(
													'components' => 'views',
													'show_image' => 1,
													'show_date' => 1,
													'show_author' => 1,
													'show_counters' => 1,
										            'show_categories' => 1
								   	            )
											);
			}
			$response['data'] = get_query_var('trx_addons_output_widgets_posts');
			if (empty($response['data'])) {
				$response['data'] .= '<article class="post_item">' . esc_html__('Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'trx_addons') . '</article>';
			} else {
				$response['data'] .= '<article class="post_item"><a href="#" class="post_more search_more">' . esc_html__('More results ...', 'trx_addons') . '</a></article>';
			}
		} else {
			$response['error'] = '<article class="post_item">' . esc_html__('The query string is empty!', 'trx_addons') . '</article>';
		}
		
		trx_addons_ajax_response( $response );
	}
}



// trx_sc_layouts_search
//-------------------------------------------------------------
/*
[trx_sc_layouts_search id="unique_id" style="normal|expand|fullscreen" ajax="0|1"]
*/
if ( !function_exists( 'trx_addons_sc_layouts_search' ) ) {
	function trx_addons_sc_layouts_search($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_sc_layouts_search', $atts, trx_addons_sc_common_atts('id,hide', array(
			// Individual params
			"type"       => "default",
			"style"      => "normal",
			"ajax"       => "1",
			"post_types" => ''
			))
		);

		ob_start();
		trx_addons_get_template_part(array(
										TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'search/tpl.'.trx_addons_esc($atts['type']).'.php',
										TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'search/tpl.default.php'
										), 
										'trx_addons_args_sc_layouts_search',
										$atts
									);
		$output = ob_get_contents();
		ob_end_clean();
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_layouts_search', $atts, $content);
	}
}


// Add shortcode [trx_sc_layouts_search]
if (!function_exists('trx_addons_sc_layouts_search_add_shortcode')) {
	function trx_addons_sc_layouts_search_add_shortcode() {
		
		if (!trx_addons_cpt_layouts_sc_required()) return;

		add_shortcode("trx_sc_layouts_search", "trx_addons_sc_layouts_search");
	}
	add_action('init', 'trx_addons_sc_layouts_search_add_shortcode', 15);
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'search/search-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'search/search-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'search/search-sc-vc.php';
}
