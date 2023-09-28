<?php
/**
 * ThemeREX Addons Posts and Comments Reviews
 *
 * @package ThemeREX Addons
 * @since v1.6.47 ( new functionality in v1.6.57 )
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Define component's subfolder
if ( !defined('TRX_ADDONS_PLUGIN_REVIEWS') ) define('TRX_ADDONS_PLUGIN_REVIEWS', TRX_ADDONS_PLUGIN_COMPONENTS . 'reviews/');


// Add component to the global list
if (!function_exists('trx_addons_reviews_add_to_components')) {
	add_filter( 'trx_addons_components_list', 'trx_addons_reviews_add_to_components' );
	function trx_addons_reviews_add_to_components($list=array()) {
		$list['reviews'] = array(
					'title' => __('Reviews for posts and comments', 'trx_addons')
					);
		return $list;
	}
}

// Check if module is enabled
if (!function_exists('trx_addons_reviews_enable')) {
	function trx_addons_reviews_enable( $cpt = '' ) {
		static $enable = null;
		if ( $enable === null ) {
			$enable = trx_addons_components_is_allowed('components', 'reviews');
		}
		$rez = $enable;
		if ( $rez && ! empty( $cpt ) ) {
			$post_types = trx_addons_get_option( 'reviews_post_types' );
			$rez = is_array( $post_types ) && ! empty( $post_types[ $cpt ] );
		}
		return $rez;
	}
}

// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_reviews_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_reviews_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY);
	function trx_addons_reviews_load_scripts_front() {
		if (trx_addons_reviews_enable() && trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-reviews', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_REVIEWS . 'reviews.css'), array(), null );
			wp_enqueue_script( 'trx_addons-reviews', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_REVIEWS . 'reviews.js'), array('jquery'), null, true );
		}
	}
}

// Load responsive styles for the frontend
if ( !function_exists( 'trx_addons_reviews_load_responsive_styles' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_reviews_load_responsive_styles', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY);
	function trx_addons_reviews_load_responsive_styles() {
		if (trx_addons_reviews_enable() && trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 
				'trx_addons-reviews-responsive', 
				trx_addons_get_file_url(TRX_ADDONS_PLUGIN_REVIEWS . 'reviews.responsive.css'), 
				array(), 
				null, 
				trx_addons_media_for_load_css_responsive( 'reviews', 'sm' ) 
			);
		}
	}
}

	
// Merge styles to the single stylesheet
if ( !function_exists( 'trx_addons_reviews_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_reviews_merge_styles');
	function trx_addons_reviews_merge_styles($list) {
		if ( trx_addons_reviews_enable() ) {
			$list[ TRX_ADDONS_PLUGIN_REVIEWS . 'reviews.css' ] = true;
		}
		return $list;
	}
}


// Merge styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_reviews_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_reviews_merge_styles_responsive');
	function trx_addons_reviews_merge_styles_responsive($list) {
		if ( trx_addons_reviews_enable() ) {
			$list[ TRX_ADDONS_PLUGIN_REVIEWS . 'reviews.responsive.css' ] = true;
		}
		return $list;
	}
}

	
// Merge specific scripts into single file
if ( !function_exists( 'trx_addons_reviews_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_reviews_merge_scripts', 11);
	function trx_addons_reviews_merge_scripts($list) {
		if ( trx_addons_reviews_enable() ) {
			$list[ TRX_ADDONS_PLUGIN_REVIEWS . 'reviews.js' ] = true;
		}
		return $list;
	}
}


// Add 'Reviews' section in the ThemeREX Addons Options
if (!function_exists('trx_addons_reviews_options')) {
	add_filter( 'trx_addons_filter_options', 'trx_addons_reviews_options');
	function trx_addons_reviews_options($options) {
		// Add section 'Reviews'
		if (trx_addons_reviews_enable()) {
			trx_addons_array_insert_before($options, 'sc_section', array(
				'reviews_section' => array(
					"title" => esc_html__('Reviews', 'trx_addons'),
					'icon' => 'trx_addons_icon-star-filled',
					"type" => "section"
				),
				'reviews_section_info' => array(
					"title" => esc_html__('Reviews settings', 'trx_addons'),
					"desc" => wp_kses_data( __("Settings of posts and comments reviews", 'trx_addons') ),
					"type" => "info"
				),
				'reviews_enable' => array(
					"title" => esc_html__('Allow reviews',  'trx_addons'),
					"desc" => wp_kses_data( __('Allow to review posts and comments',  'trx_addons') ),
					"std" => "1",
					"type" => "switch"
				),
				"reviews_post_types" => array(
					"title" => esc_html__("Post types", 'trx_addons'),
					"desc" => wp_kses_data( __("Select post types to show reviews", 'trx_addons') ),
					"dir" => 'horizontal',
					"dependency" => array(
						"reviews_enable" => array('1')
					),
					"std" => array( 'post' => 1 ),
					"options" => array(),
					"type" => "checklist"
				),					
				'reviews_in_comments_enable' => array(
					"title" => esc_html__('Allow rating in comments',  'trx_addons'),
					"desc" => wp_kses_data( __('Allow visitors to leave ratings in comments',  'trx_addons') ),
					"std" => "1",
					"type" => "switch"
				),
				'reviews_in_comments_min' => array(
					"title" => esc_html__('Min user ratings',  'trx_addons'),
					"desc" => wp_kses_data( __('The minimum number of visitor ratings required for a post to participate in the ranking',  'trx_addons') ),
					"dependency" => array(
						"reviews_in_comments_enable" => array('1')
					),
					"std" => "3",
					"type" => "text"
				),
				'reviews_mark_max' => array(
					"title" => esc_html__('Max mark',  'trx_addons'),
					"desc" => wp_kses_data( __('Maximum level for grading marks',  'trx_addons') ),
					"dependency" => array(
						"reviews_enable" => array('1')
					),
					"std" => "5",
					"options" => trx_addons_reviews_mark_max_list(),
					"type" => "radio"
				),
				'reviews_mark_decimals' => array(
					"title" => esc_html__('Allow decimals',  'trx_addons'),
					"desc" => wp_kses_data( __('Allow visitors to leave decimal ratings',  'trx_addons') ),
					"std" => "1",
					"type" => "switch"
				),
				"reviews_mark_icon" => array(
					"title" => esc_html__("Icon", 'trx_addons'),
					"desc" => wp_kses_data( __('Select icon to show before reviews marks', 'trx_addons') ),
					"dependency" => array(
						"reviews_enable" => array('1')
					),
					"std" => "trx_addons_icon-star",
					"options" => array(),
					"style" => trx_addons_get_setting('icons_type'),
					"type" => "icons"
				),
				'reviews_in_content_short' => array(
					"title" => esc_html__('Insert short review before',  'trx_addons'),
					"desc" => wp_kses_data( __('Insert short review to the single post before specified paragraph. 0 - do not insert, 999 - insert after the content', 'trx_addons') ),
					"dependency" => array(
						"reviews_enable" => array('1')
					),
					"std" => "1",
					"type" => "text"
				),
				'reviews_in_content_short_align' => array(
					"title" => esc_html__('Align short review',  'trx_addons'),
					"desc" => wp_kses_data( __('Alignment of the short review block', 'trx_addons') ),
					"dependency" => array(
						"reviews_enable" => array('1'),
						"reviews_in_content_short" => array('^0'),
					),
					"std" => "right",
					"options" => trx_addons_get_list_sc_floats(false, false),
					"type" => "select"
				),
				'reviews_in_content_detailed' => array(
					"title" => esc_html__('Insert detailed review before',  'trx_addons'),
					"desc" => wp_kses_data( __('Insert detailed review to the single post before specified paragraph. 0 - do not insert, 999 - insert after the content', 'trx_addons') ),
					"dependency" => array(
						"reviews_enable" => array('1')
					),
					"std" => "999",
					"type" => "text"
				),
			));
		}		
		return $options;
	}
}


// Add Reviews parameters to the Meta Box support
if (!function_exists('trx_addons_reviews_init')) {
	add_action( 'init', 'trx_addons_reviews_init', 11 );
	function trx_addons_reviews_init() {
		if ( trx_addons_reviews_enable() && trx_addons_get_option('reviews_enable') ) {
			$post_types = trx_addons_get_option('reviews_post_types');
			if ( is_array($post_types) ) {
				foreach( $post_types as $pt => $v ) {
					if ( empty( $v ) ) continue;
					trx_addons_meta_box_register( $pt, array(
						"reviews_section" => array(
							"title" => esc_html__("Review", 'trx_addons'),
							"desc" => wp_kses_data( __('Review parameters for this post', 'trx_addons') ),
							"type" => "section"
						),
						"reviews_enable" => array(
							"title" => esc_html__("Enable review", 'trx_addons'),
							"desc" => wp_kses_data( __("Enable review for this post", 'trx_addons') ),
							"class" => "trx_addons_column-1_3",
							"std" => "0",
							"type" => "checkbox"
						),
						"reviews_mark" => array(
							"title" => esc_html__("Mark",  'trx_addons'),
							"desc" => wp_kses_data( __("Summary mark of this review (only digits). Leave it empty if you want to fill reviews criterias below", 'trx_addons') ),
							"class" => "trx_addons_column-1_3",
							"dependency" => array(
								"reviews_enable" => array( 1 )
							),
							"std" => 0,
							"min" => 0,
							"max" => trx_addons_get_option( 'reviews_mark_max' ),
							"step" => trx_addons_get_option( 'reviews_mark_max' ) == 100 ? 1 : 0.1,
							"type" => "slider"
						),
						"reviews_mark_text" => array(
							"title" => esc_html__("Mark text",  'trx_addons'),
							"desc" => wp_kses_data( __("Summary mark of this review as text. For example: 'Excellent', 'Good', 'Poor', 'Very bad', etc.", 'trx_addons') ),
							"class" => "trx_addons_column-1_3",
							"dependency" => array(
								"reviews_enable" => array( 1 )
							),
							"std" => "",
							"type" => "text"
						),
						"reviews_title" => array(
							"title" => esc_html__("Title",  'trx_addons'),
							"desc" => wp_kses_data( __("Alternative title of this review", 'trx_addons') ),
							"class" => "trx_addons_column-1_3 trx_addons_new_row",
							"dependency" => array(
								"reviews_enable" => array( 1 )
							),
							"std" => "",
							"type" => "text"
						),
						"reviews_summary" => array(
							"title" => esc_html__("Summary",  'trx_addons'),
							"desc" => wp_kses_data( __("Short summary of this review", 'trx_addons') ),
							"class" => "trx_addons_column-1_3",
							"dependency" => array(
								"reviews_enable" => array( 1 )
							),
							"std" => "",
							"type" => "textarea"
						),
						"reviews_image" => array(
							"title" => esc_html__("Image", 'trx_addons'),
							"desc" => wp_kses_data( __("Select image to present this review", 'trx_addons') ),
							"class" => "trx_addons_column-1_3",
							"dependency" => array(
								"reviews_enable" => array( 1 )
							),
							"std" => "",
							"type" => "image"
						),
						"reviews_link_title" => array(
							"title" => esc_html__("Button block title",  'trx_addons'),
							"desc" => wp_kses_data( __("Add a button block title", 'trx_addons') ),
							"class" => "trx_addons_column-1_3",
							"dependency" => array(
								"reviews_enable" => array( 1 )
							),
							"std" => "",
							"type" => "text"
						),
						"reviews_link" => array(
							"title" => esc_html__("Button URL",  'trx_addons'),
							"desc" => wp_kses_data( __("Add a button block that links to the URL specified in this field", 'trx_addons') ),
							"class" => "trx_addons_column-1_3 trx_addons_new_row",
							"dependency" => array(
								"reviews_enable" => array( 1 )
							),
							"std" => "",
							"type" => "text"
						),
						"reviews_link_caption" => array(
							"title" => esc_html__("Button text",  'trx_addons'),
							"desc" => wp_kses_data( __("Add button text", 'trx_addons') ),
							"class" => "trx_addons_column-1_3",
							"dependency" => array(
								"reviews_enable" => array( 1 )
							),
							"std" => "",
							"type" => "text"
						),
						"reviews_positives" => array(
							"title" => esc_html__("Positives",  'trx_addons'),
							"desc" => wp_kses_data( __("List the positive sides of the object (one per line)", 'trx_addons') ),
							"class" => "trx_addons_column-1_3 trx_addons_new_row",
							"dependency" => array(
								"reviews_enable" => array( 1 )
							),
							"std" => "",
							"type" => "textarea"
						),
						"reviews_negatives" => array(
							"title" => esc_html__("Negatives",  'trx_addons'),
							"desc" => wp_kses_data( __("List the negative sides of the object (one per line)", 'trx_addons') ),
							"class" => "trx_addons_column-1_3",
							"dependency" => array(
								"reviews_enable" => array( 1 )
							),
							"std" => "",
							"type" => "textarea"
						),
						"reviews_criterias_info" => array(
							"title" => esc_html__("Review criteria",  'trx_addons'),
							"desc" => wp_kses_data( __("Add multiple criteria and specify the marks for them", 'trx_addons') ),
							"dependency" => array(
								"reviews_enable" => array( 1 )
							),
							"type" => "info"
						),
						"reviews_criterias" => array(
							"title" => esc_html__("Review criteria", 'trx_addons'),
							"desc" => wp_kses_data( __("Review criteria and corresponding marks", 'trx_addons') ),
							"dependency" => array(
								"reviews_enable" => array( 1 )
							),
							"clone" => true,
							"std" => array( array() ),
							"type" => "group",
							"fields" => array(
								"title" => array(
									"title" => esc_html__("Title", 'trx_addons'),
									"class" => "trx_addons_column-1_2",
									"std" => "",
									"type" => "text"
								),
								"mark" => array(
									"title" => esc_html__("Mark", 'trx_addons'),
									"class" => "trx_addons_column-1_2",
									"std" => 0,
									"min" => 0,
									"max" => trx_addons_get_option( 'reviews_mark_max' ),
									"step" => trx_addons_get_option( 'reviews_mark_max' ) == 100 ? 1 : 0.1,
									"type" => "slider"
								),
							)
						),
						"reviews_attributes_info" => array(
							"title" => esc_html__("Additional fields",  'trx_addons'),
							"desc" => wp_kses_data( __("Add some fields and specify its values, links and type", 'trx_addons') ),
							"dependency" => array(
								"reviews_enable" => array( 1 )
							),
							"type" => "info"
						),
						"reviews_attributes" => array(
							"title" => esc_html__("Attributes", 'trx_addons'),
							"desc" => wp_kses_data( __("Additional parameters to show in the small review block on the single post", 'trx_addons') ),
							"dependency" => array(
								"reviews_enable" => array( 1 )
							),
							"clone" => true,
							"std" => array( array() ),
							"type" => "group",
							"fields" => array(
								"title" => array(
									"title" => esc_html__("Title", 'trx_addons'),
									"class" => "trx_addons_column-1_4",
									"std" => "",
									"type" => "text"
								),
								"value" => array(
									"title" => esc_html__("Value", 'trx_addons'),
									"class" => "trx_addons_column-1_4",
									"std" => "",
									"type" => "text"
								),
								"link" => array(
									"title" => esc_html__("Link", 'trx_addons'),
									"class" => "trx_addons_column-1_4",
									"std" => "",
									"type" => "text"
								),
								"type" => array(
									"title" => esc_html__("Type", 'trx_addons'),
									"class" => "trx_addons_column-1_4",
									"std" => "text",
									"options" => array(
										"text" => __('Text', 'trx_addons'),
										"button" => __('Button', 'trx_addons'),
									),
									"type" => "select"
								),
							)
						),
					));
				}
			}
		}
	}
}


// Return list with allowed layouts of the shortcode 'Reviews'
if (!function_exists('trx_addons_reviews_sc_type_list')) {
	function trx_addons_reviews_sc_type_list() {
		return array(
						'short' => __( 'Short (default)', 'trx_addons'),
						'detailed' => __( 'Detailed', 'trx_addons'),
					);
	}
}

// Return list with type of marks in the Reviews
if (!function_exists('trx_addons_reviews_mark_type_list')) {
	function trx_addons_reviews_mark_type_list() {
		return array(
						'post' => __( 'Author', 'trx_addons'),
						'user' => __( 'Visitors', 'trx_addons'),
					);
	}
}

// Return list with allowed max levels of review marks
if (!function_exists('trx_addons_reviews_mark_max_list')) {
	function trx_addons_reviews_mark_max_list( $inherit = false ) {
		$list = array(
					'5'   => esc_html__('5 stars', 'trx_addons'),
					'10'  => esc_html__('10 points', 'trx_addons'),
					'100' => esc_html__('100%', 'trx_addons')
				);
		return $inherit
					? array_merge(
							array( 'inherit' => __( 'Inherit', 'trx_addons') ),
							$list
						)
					: $list;
	}
}

// Fill 'Post types' before show ThemeREX Addons Options
if (!function_exists('trx_addons_reviews_before_show_options')) {
	add_filter( 'trx_addons_filter_before_show_options', 'trx_addons_reviews_before_show_options', 10, 2);
	function trx_addons_reviews_before_show_options($options, $pt='') {
		if ( trx_addons_reviews_enable() && isset( $options['reviews_post_types'] ) ) {
			$options['reviews_post_types']['options'] = trx_addons_get_list_reviews_posts_types();
		}
		return $options;
	}
}

// Return list of allowed post's types
if ( !function_exists( 'trx_addons_get_list_reviews_posts_types' ) ) {
	function trx_addons_get_list_reviews_posts_types($prepend_inherit=false) {
		static $list = false;
		if ($list === false) {
			$list = array();
			$post_types = get_post_types(
								array(
									'public' => true,
									'exclude_from_search' => false
								),
								'objects'
							);
			if (is_array($post_types)) {
				foreach ($post_types as $pt) {
					$list[$pt->name] = $pt->label;
				}
			}
		}
		return $prepend_inherit 
					? trx_addons_array_merge(array('inherit' => esc_html__("Inherit", 'trx_addons')), $list) 
					: $list;
	}
}

// Add 'Rating' to the order list
if (!function_exists('trx_addons_reviews_add_rating_to_order_list')) {
	add_filter( 'trx_addons_filter_popular_posts_orderby', 'trx_addons_reviews_add_rating_to_order_list');
	add_filter( 'trx_addons_filter_get_list_sc_query_orderby', 'trx_addons_reviews_add_rating_to_order_list', 10, 2);
	function trx_addons_reviews_add_rating_to_order_list($list, $keys=array()) {
		if ( trx_addons_reviews_enable() && trx_addons_get_option('reviews_enable') ) {
			$list['rating'] = __('Rating', 'trx_addons');
		}
		return $list;
	}
}

// Add order 'Rating' to the query params
if (!function_exists('trx_addons_reviews_add_rating_to_query_args')) {
	add_filter( 'trx_addons_filter_add_sort_order', 'trx_addons_reviews_add_rating_to_query_args', 10, 3);
	function trx_addons_reviews_add_rating_to_query_args($q_args, $orderby, $order) {
		if ( trx_addons_reviews_enable() && trx_addons_get_option('reviews_enable') ) {
			if ($orderby =='rating') {
				$q_args['meta_key'] = 'trx_addons_reviews_post_mark';
				$q_args['orderby'] = 'meta_value_num';
			}
		}
		return $q_args;
	}
}

// Convert rating value to save
if (!function_exists('trx_addons_reviews_mark2save')) {
	function trx_addons_reviews_mark2save($mark, $max = 0) {
		if ($max == 0) $max = (int) trx_addons_get_option('reviews_mark_max');
		return round( $max > 0 && $max != 100 ? (float) $mark * 100 / $max : $mark, 1);
	}
}


// Convert rating value to display
if (!function_exists('trx_addons_reviews_mark2show')) {
	function trx_addons_reviews_mark2show($mark, $max = 0) {
		if ($max == 0) $max = (int) trx_addons_get_option('reviews_mark_max');
		$decimals = $max < 100 && trx_addons_get_option('reviews_mark_decimals') > 0 ? 1 : 0;
		return number_format( round( $max > 0 && $max != 100 ? (float) $mark * $max / 100 : $mark, $decimals ), $decimals );
	}
}


// Return the post rating
if (!function_exists('trx_addons_reviews_get_post_mark')) {
	function trx_addons_reviews_get_post_mark($post_id='p0', $max=0, $type='post') {
		$mark = 0;
		$from = substr($post_id, 0, 1) == 'c' ? 'comment' : 'post';
		$post_id = (int) substr($post_id, 1);
		if ( ! $post_id && $from == 'post' ) {
			$post_id = trx_addons_get_the_ID();				
		}
		if ( $post_id ) {
			$mark = $from == 'comment' 
						? get_comment_meta($post_id, 'trx_addons_reviews_user_mark', true)
						: get_post_meta($post_id, $type == 'post' ? 'trx_addons_reviews_post_mark' : 'trx_addons_reviews_user_mark', true);
		}
		return $mark;
	}
}

// Add stars to the meta
if (!function_exists('trx_addons_reviews_show_post_meta')) {
	add_action( 'trx_addons_action_show_post_meta', 'trx_addons_reviews_show_post_meta', 10, 3);
	function trx_addons_reviews_show_post_meta($meta, $post_id='p0', $args=array()) {
		if ( trx_addons_reviews_enable() && in_array( $meta, array('rating', 'reviews') ) ) {
			if ( substr($post_id, 0, 1) != 'p' ) {
				$post_id = 'p' . $post_id;
			}
			if ( $post_id == 'p0' ) {
				$post_id = 'p' . get_the_ID();
			}
			$reviews_post_types = trx_addons_get_option('reviews_post_types');
			if ( !empty($reviews_post_types) && !empty( $reviews_post_types[ get_post_type( (int) substr($post_id, 1) ) ] ) ) {
				$post_mark = trx_addons_reviews_get_post_mark($post_id, !empty($args['rating_type']) ? $args['rating_type'] : 'post');
				if ( $post_mark > 0 ) {
					echo '<a href="' . esc_url( get_permalink() ) . '" class="post_meta_item post_meta_rating trx_addons_icon-star">'
							. '<span class="post_meta_number">' . wp_kses_data( trx_addons_reviews_mark2show( $post_mark ) ) . '</span>'
						. '</a> ';
				}
			}
		}
	}
}

// Show stars
if (!function_exists('trx_addons_reviews_show_stars')) {
	add_action('trx_addons_action_post_rating', 'trx_addons_reviews_show_stars', 10, 2);
	function trx_addons_reviews_show_stars($post_id='p0', $args=array()) {
		$args = array_merge(
			array(
				'mark' => '',
				'mark_max' => '',
//				'mark_decimals' => '',
				'vote' => false,
				'type' => 'post',
				'echo' => true
			),
			$args
		);
		$output = '';
		if ( trx_addons_reviews_enable() ) {
			if ( !in_array(substr($post_id, 0, 1), array('c', 'p')) ) {
				$post_id = 'p' . $post_id;
			}
			if ( $post_id == 'p0' ) {
				$post_id = 'p' . get_the_ID();
			}
			$reviews_post_types = trx_addons_get_option('reviews_post_types');
			if (
				! empty( $args['mark'] )
				||
				( substr($post_id, 0, 1) == 'c' && trx_addons_get_option('reviews_in_comments_enable') )
				||
				( substr($post_id, 0, 1) == 'p' && !empty($reviews_post_types) && !empty( $reviews_post_types[ get_post_type( (int) substr($post_id, 1) ) ] ) )
			) {
				$mark_decimals = ! isset($args['mark_decimals']) ? trx_addons_get_option('reviews_mark_decimals') : $args['mark_decimals'];
				$mark_max  = empty($args['mark_max']) ? trx_addons_get_option('reviews_mark_max') : $args['mark_max'];
				$mark_real = empty($args['mark']) ? trx_addons_reviews_get_post_mark( $post_id, $args['type'] ) : trx_addons_reviews_mark2save( $args['mark'], $mark_max );
				if ( $mark_real > 0 && $mark_decimals == 0 ) {
					$mark_real = trx_addons_reviews_mark2save( trx_addons_reviews_mark2show( $mark_real, $mark_max ), $mark_max );
				}
				if ( $mark_real > 0 || !empty($args['vote'] ) ) {
					$output .= '<span class="trx_addons_reviews_mark">';
					// Stars
					$output .= '<span class="trx_addons_reviews_stars"'
								. ( !empty($args['vote'])
									? ( ' data-mark-max="' . esc_attr($mark_max) . '"'
										. ' data-mark-decimals="' . esc_attr($mark_decimals) . '"'
										)
									: ''
									)
								. '>'
								. '<span class="trx_addons_reviews_stars_default">';
					$icon = trx_addons_get_option('reviews_mark_icon');
					for ($i = 0; $i < 5; $i++) {
						$output .= '<span class="trx_addons_reviews_star ' . esc_attr( !empty($icon) ? $icon : 'trx_addons_icon-star' ) . '"></span>';
					}
					$output  .= '</span>';
					// Stars hover
					$output  .= '<span class="trx_addons_reviews_stars_hover" style="width:' . esc_attr( (int) $mark_real ) . '%;">';
					for ($i = 0; $i < 5; $i++) {
						$output .= '<span class="trx_addons_reviews_star ' . esc_attr( !empty($icon) ? $icon : 'trx_addons_icon-star' ) . '"></span>';
					}
					$output  .= '</span>';
					// Bubble with mark
					if ( !empty($args['vote'] ) ) {
						$output  .= '<span class="trx_addons_reviews_bubble">'
										. '<span class="trx_addons_reviews_bubble_value"></span>'
										. '<span class="trx_addons_reviews_bubble_loader"></span>'
									. '</span>'
									. '<input type="hidden" name="trx_addons_reviews_vote" value="0">';
					}
					$output  .= '</span>';
					// Text
					$output  .= '<span class="trx_addons_reviews_text">'
									. '<span class="trx_addons_reviews_text_mark">' . sprintf($mark_max < 100 && $mark_decimals == 1 ? '%.1f' : '%d', trx_addons_reviews_mark2show($mark_real, $mark_max)) . '</span>'
									. '<span class="trx_addons_reviews_text_delimiter">/</span>'
									. '<span class="trx_addons_reviews_text_max">' . esc_html($mark_max) . '</span>'
								. '</span>';
					$output  .= '</span>';
					$output   = apply_filters( 'trx_addons_filter_reviews_show_stars', $output, $post_id, $args );
				}
			}
		}
		if ( !empty($args['echo']) ) {
			trx_addons_show_layout( $output );
		}
		return $output;
	}
}

// Show round
if (!function_exists('trx_addons_reviews_show_round')) {
	add_action('trx_addons_action_post_rating_round', 'trx_addons_reviews_show_round', 10, 2);
	function trx_addons_reviews_show_round($post_id='p0', $args=array()) {
		$args = array_merge(
			array(
				'mark' => '',
				'mark_max' => '',
//				'mark_decimals' => '',
				'mark_text' => '',
				'size' => 1,	// Coefficient to reduce/enlarge size
				'type' => 'post',
				'echo' => true
			),
			$args
		);
		$output = '';
		if ( trx_addons_reviews_enable() ) {
			if ( !in_array(substr($post_id, 0, 1), array('c', 'p')) ) {
				$post_id = 'p' . $post_id;
			}
			if ( $post_id == 'p0' ) {
				$post_id = 'p' . get_the_ID();
			}
			$reviews_post_types = trx_addons_get_option('reviews_post_types');
			if (
				! empty( $args['mark'] )
				||
				( substr($post_id, 0, 1) == 'c' && trx_addons_get_option('reviews_in_comments_enable') )
				||
				( substr($post_id, 0, 1) == 'p' && !empty($reviews_post_types) && !empty( $reviews_post_types[ get_post_type( (int) substr($post_id, 1) ) ] ) )
			) {
				$mark_decimals = ! isset($args['mark_decimals']) ? trx_addons_get_option('reviews_mark_decimals') : $args['mark_decimals'];
				$mark_max  = empty($args['mark_max']) ? trx_addons_get_option('reviews_mark_max') : $args['mark_max'];
				$mark_real = empty($args['mark']) ? trx_addons_reviews_mark2show( trx_addons_reviews_get_post_mark( $post_id, $args['type'] ), $mark_max ) : $args['mark'];
				if ( $mark_real > 0 ) {
					$output .= '<div class="trx_addons_reviews_block_mark'
									. ( $args['size'] != 1
										? ' ' . trx_addons_add_inline_css_class(
													'--trx-addons-reviews-block-mark-border: ' . min( 9, max( 3, round( 6 * $args['size'] ) ) ) . 'px;'
													. 'font-size:' . $args['size'] . 'em;'
												) 
										: '' )
								. '">'
									. '<canvas' . ( ! empty( $args['id'] ) ? ' id="' . esc_attr( $args['id'] ) . '_mark"' : '' )
										. ' width="' . esc_attr( round( 120 * $args['size'] ) ) . '"'
										. ' height="' . esc_attr( round( 120 * $args['size'] ) ) . '"'
										. ' data-max-value="' . esc_attr( $mark_max ) . '"'
										. ' data-decimals="' . esc_attr( $mark_decimals ) . '"'
										. ' data-value="' . esc_attr( $mark_real ) . '"'
										. ' data-color="' . esc_attr( apply_filters( 'trx_addons_filter_get_theme_accent_color', '#efa758') ) . '"'
									. '></canvas>'
									. '<span class="trx_addons_reviews_block_mark_value"'
										. ' data-max-value="' . esc_attr( $mark_max) . '"'
										. ' data-decimals="' . esc_attr( $mark_decimals ) . '"'
									. '>'
										. esc_html( $mark_real )
									. '</span>'
									. ( ! empty( $args['mark_text'] )
										? '<span class="trx_addons_reviews_block_mark_text">'
											. esc_html( $args['mark_text'] )
											. '</span>'
										: ''
										)
									. '<span class="trx_addons_reviews_block_mark_progress"></span>'
								. '</div>';
					$output = apply_filters( 'trx_addons_filter_reviews_show_round', $output, $post_id, $args );
				}
			}
		}
		if ( ! empty( $args['echo'] ) ) {
			trx_addons_show_layout( $output );
		}
		return $output;
	}
}

// Return value of the custom field for the custom blog items
if ( !function_exists( 'trx_addons_reviews_custom_meta_value' ) ) {
	add_filter( 'trx_addons_filter_custom_meta_value', 'trx_addons_reviews_custom_meta_value', 11, 2 );
	function trx_addons_reviews_custom_meta_value($value, $key) {
		if ( trx_addons_reviews_enable() ) {
			if ( in_array($key, array('rating', 'rating_text', 'rating_icons', 'rating_stars'))) {
				$new_value = trx_addons_reviews_get_post_mark( get_the_ID() );
				if ( ! empty( $new_value ) ) {
					$value = $new_value;
				}
			}
		}
		return $value;
	}
}


// Save reviews mark for search, sorting, etc.
if ( !function_exists( 'trx_addons_reviews_save_post_options' ) ) {
	add_filter('trx_addons_filter_save_post_options', 'trx_addons_reviews_save_post_options', 10, 3);
	function trx_addons_reviews_save_post_options($options, $post_id, $post_type) {
		if ( trx_addons_reviews_enable( $post_type ) ) {
			if ( ! empty($options['reviews_enable']) ) {
				if ( ! empty($options['reviews_attributes'] )
					&& is_array( $options['reviews_attributes'] )
					&& count( $options['reviews_attributes'] ) > 0
				) {
					foreach( $options['reviews_attributes'] as $k => $v ) {
						if ( empty( $v['type'] ) ) {
							$options['reviews_attributes'][$k]['type'] = 'text';
						}
					}
				}
				if ( ! empty( $options['reviews_criterias'] )
					&& is_array( $options['reviews_criterias'] )
					&& count( $options['reviews_criterias'] ) > 0
					&& $options['reviews_criterias'][0]['mark'] > 0
				) {
					$total = 0;
					foreach( $options['reviews_criterias'] as $k => $v ) {
						$total += (float) $v['mark'];
						$options['reviews_criterias'][$k]['mark'] = trx_addons_reviews_mark2save( (float) $v['mark'] );
					}
					$options['reviews_mark'] = $total / count($options['reviews_criterias']);
				}
				$options['reviews_mark'] = trx_addons_reviews_mark2save( $options['reviews_mark'] );
				update_post_meta( $post_id, 'trx_addons_reviews_post_mark', $options['reviews_mark'] );
			} else {
				delete_post_meta( $post_id, 'trx_addons_reviews_post_mark' );
				delete_post_meta( $post_id, 'trx_addons_reviews_user_mark' );
			}
		}
		return $options;
	}
}


// Prepare saved marks to editing
if ( !function_exists( 'trx_addons_reviews_load_post_options' ) ) {
	add_filter('trx_addons_filter_load_post_options', 'trx_addons_reviews_load_post_options', 10, 3);
	function trx_addons_reviews_load_post_options($options, $post_id, $post_type) {
		if ( trx_addons_reviews_enable( $post_type ) ) {
			if ( ! empty( $options['reviews_enable'] ) ) {
				if ( ! empty( $options['reviews_criterias'] )
					&& is_array( $options['reviews_criterias'] )
					&& count( $options['reviews_criterias'] ) > 0
				) {
					foreach( $options['reviews_criterias'] as $k => $v ) {
						$options['reviews_criterias'][$k]['mark'] = trx_addons_reviews_mark2show( (float) $v['mark'] );
					}
				}
				$options['reviews_mark'] = trx_addons_reviews_mark2show( $options['reviews_mark'] );
			}
		}
		return $options;
	}
}

// Add rating stars to the comments form
if ( ! function_exists( 'trx_addons_reviews_add_to_comment_form' ) ) {
	add_action( 'comment_form_top', 'trx_addons_reviews_add_to_comment_form' );
	function trx_addons_reviews_add_to_comment_form() {
		if ( trx_addons_reviews_enable( get_post_type() ) && trx_addons_get_option('reviews_in_comments_enable') ) {
			$meta = (array)get_post_meta( get_the_ID(), 'trx_addons_options', true );
			if ( ! isset( $meta['reviews_enable'] ) || ! empty( $meta['reviews_enable'] ) ) {
				trx_addons_reviews_show_stars( 'c0', array( 'vote' => true ) );
			}
		}
	}
}

// Save review mark on save comment
if ( ! function_exists( 'trx_addons_reviews_save_comment' ) ) {
	add_action( 'wp_insert_comment', 'trx_addons_reviews_save_comment', 10, 2 );
	function trx_addons_reviews_save_comment( $comment_id, $comment_obj ) {
		if ( trx_addons_reviews_enable() && trx_addons_get_option('reviews_in_comments_enable') ) {
			if ( $comment_id > 0 ) {
				$vote = (int) trx_addons_get_value_gp( 'trx_addons_reviews_vote' );
				if ( $vote > 0 && $vote <= 100 ) {
					update_comment_meta( $comment_id, 'trx_addons_reviews_user_mark', $vote );
				}
			}
		}
	}
}

// Recalc users mark for post on any comment's action
if ( ! function_exists( 'trx_addons_reviews_calc_user_marks' ) ) {
	add_action( 'wp_update_comment_count', 'trx_addons_reviews_calc_user_marks', 10, 3 );
	function trx_addons_reviews_calc_user_marks( $post_id, $new=0, $old=0 ) {
		if ( trx_addons_reviews_enable() && trx_addons_get_option('reviews_in_comments_enable') ) {
			$post_id = (int) $post_id;
			if ( ! $post_id ) return;
			$post = get_post($post_id);
			if ( ! $post ) return;
			$post_types = trx_addons_get_option('reviews_post_types');
			if ( is_array( $post_types ) && ! empty( $post_types[ $post->post_type ] ) ) {
				$meta = (array)get_post_meta( $post_id, 'trx_addons_options', true );
				if ( ! empty( $meta['reviews_enable'] ) ) {
					global $wpdb;
					$rez = (array) $wpdb->get_results(
								$wpdb->prepare(
									"SELECT COUNT(*) as 'votes', SUM(m.meta_value) as 'marks'"
										. " FROM {$wpdb->comments} AS c"
										. " LEFT JOIN {$wpdb->commentmeta} AS m"
											. " ON m.comment_id=c.comment_ID"
										. " WHERE c.comment_post_ID = %d AND c.comment_approved = '1' AND m.meta_key='trx_addons_reviews_user_mark'",
									$post_id
								)
							);
					$meta = array(
						'marks' => 0,
						'votes' => 0
					);
					if ( count($rez) == 1 && ! empty( $rez[0]->votes ) ) {
						$meta['votes'] = $rez[0]->votes;
						$meta['marks'] = $rez[0]->marks;
					}
					update_post_meta( $post_id, 'trx_addons_reviews_user_marks', $meta );
					if ( $meta['votes'] > 0 && $meta['votes'] >= trx_addons_get_option('reviews_in_comments_min') ) {
						update_post_meta( $post_id, 'trx_addons_reviews_user_mark', round( $meta['marks'] / $meta['votes'] ) );
					}
				}
			}
		}
	}
}

// Add rating to the comments counters
if ( ! function_exists( 'trx_addons_reviews_add_comments_mark' ) ) {
	add_action( 'trx_addons_filter_get_comment_counters', 'trx_addons_reviews_add_comments_mark', 10, 2 );
	function trx_addons_reviews_add_comments_mark( $output, $counters ) {
		if ( trx_addons_reviews_enable() && trx_addons_get_option('reviews_in_comments_enable') ) {
			if ( strpos( $counters, 'rating' ) !==false || strpos( $counters, 'reviews' ) !==false ) {
				$mark_real = trx_addons_reviews_get_post_mark('c0');
				if ( $mark_real > 0 ) {
					$mark_max  = trx_addons_get_option('reviews_mark_max');
					$icon = trx_addons_get_option('reviews_mark_icon');
					if ( empty( $icon ) || trx_addons_is_off( $icon ) ) {
						$icon = 'trx_addons_icon-star';
					}
					$output .= '<span class="comment_counters_item comment_counters_rating ' . esc_attr($icon) . '">'
									. '<span class="comment_counters_number">'
										. '<span class="comment_counters_number_value">' . esc_html( trx_addons_reviews_mark2show( $mark_real, $mark_max ) ) . '</span>'
										. '<span class="comment_counters_number_separator">/</span>'
										. '<span class="comment_counters_number_total">' . esc_html( $mark_max ) . '</span>'
									. '</span>'
									. '<span class="comment_counters_label">' . esc_html__('Rating', 'trx_addons') . '</span>'
								. '</span>';
				}
			}
		}
		return $output;
	}
}


// Add reviews blocks to the post's content
//--------------------------------------------------------------------
if (!function_exists('trx_addons_reviews_add_to_content')) {
	add_filter( 'the_content', 'trx_addons_reviews_add_to_content', 100, 1);
	function trx_addons_reviews_add_to_content($content) {
		if ( trx_addons_is_singular() && trx_addons_reviews_enable( get_post_type() ) ) {
			$short = trx_addons_get_option('reviews_in_content_short');
			$short_align = trx_addons_get_option('reviews_in_content_short_align');
			$detailed = trx_addons_get_option('reviews_in_content_detailed');
			if ( $short > 0 || $detailed > 0 ) {
				if ( $short == 999 ) {
					$content .= trx_addons_sc_reviews(array('type' => 'short', 'align' => $short_align));
				}
				if ( $detailed == 999 ) {
					$content .= trx_addons_sc_reviews(array('type' => 'detailed'));
				}
				if ( ( $short > 0 && $short != 999 ) || ( $detailed > 0 && $detailed != 999 ) ) {
					$p_number = 0;
					$short_inserted = false;
					$detailed_inserted = false;
					$in_quote = false;
					for ( $i = 0; $i < strlen( $content ) - 3; $i++ ) {
						if ( $content[ $i ] != '<' ) {
							continue;
						}
						if ( $in_quote ) {
							if ( strtolower( substr( $content, $i + 1, 12 ) ) == '/blockquote>' ) {
								$in_quote = false;
								$i += 12;
							}
							continue;
						} else if ( strtolower( substr( $content, $i + 1, 10 ) ) == 'blockquote' && in_array( $content[ $i + 11 ], array( '>', ' ' ) ) ) {
							$in_quote = true;
							$i += 11;
							continue;
						} else if ( $content[ $i + 1 ] == 'p' && in_array( $content[ $i + 2 ], array( '>', ' ' ) ) ) {
							$p_number++;
							if ( $short == $p_number ) {
								$short_inserted = true;
								$content = ( $i > 0 ? substr( $content, 0, $i ) : '' )
													. trx_addons_sc_reviews(array('type' => 'short', 'align' => $short_align))
													. substr( $content, $i );
								if ($detailed == 0 || $detailed == 999) {
									break;
								}
							}
							if ( $detailed == $p_number ) {
								$detailed_inserted = true;
								$content = ( $i > 0 ? substr( $content, 0, $i ) : '' )
													. trx_addons_sc_reviews(array('type' => 'detailed'))
													. substr( $content, $i );
								if ($short == 0 || $short == 999) {
									break;
								}
							}
						}
					}
					if ( $short > 0 && $short != 999 && ! $short_inserted ) {
						$content .= trx_addons_sc_reviews(array('type' => 'short', 'align' => $short_align));
					}
					if ( $detailed > 0 && $detailed != 999 && ! $detailed_inserted ) {
						$content .= trx_addons_sc_reviews(array('type' => 'detailed'));
					}
				}
			}
		}
		return $content;
	}
}


// Add widget "Posts by rating"
//--------------------------------------------------------------------
if (!function_exists('trx_addons_reviews_add_widget')){
	add_filter('trx_addons_widgets_list', 'trx_addons_reviews_add_widget', 10, 1);
	function trx_addons_reviews_add_widget($array=array()){		
		if (trx_addons_reviews_enable()) {
			$array['rating_posts'] = array(
				'title' => __('Posts by rating', 'trx_addons')
			);
		}
		return $array;
	}
}

// Include files with widget
if (!function_exists('trx_addons_reviews_widgets_load')) {
	add_action( 'after_setup_theme', 'trx_addons_reviews_widgets_load', 6 );
	function trx_addons_reviews_widgets_load() {
		if (trx_addons_reviews_enable()) {
			$fdir = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_REVIEWS . "rating_posts/rating_posts.php");
			if (trx_addons_components_is_allowed('widgets', 'rating_posts') && $fdir != '') { 
				include_once $fdir;
			}
		}
	}
}


// Add shortcodes
//----------------------------------------------------------------------------
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_REVIEWS . 'reviews-sc.php';

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_REVIEWS . 'reviews-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_REVIEWS . 'reviews-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_REVIEWS . 'reviews-sc-vc.php';
}

// Add Google Places API support
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_REVIEWS . 'google-places/google-places.php';
