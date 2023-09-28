<?php
/**
 * Widget: Popular posts
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Load widget
if (!function_exists('trx_addons_widget_popular_posts_load')) {
	add_action( 'widgets_init', 'trx_addons_widget_popular_posts_load' );
	function trx_addons_widget_popular_posts_load() {
		register_widget('trx_addons_widget_popular_posts');
	}
}

// Widget Class
class trx_addons_widget_popular_posts extends TRX_Addons_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_popular_posts', 'description' => esc_html__('Display any post types', 'trx_addons'));
		parent::__construct( 'trx_addons_widget_popular_posts', esc_html__('ThemeREX Universal Posts Listing', 'trx_addons'), $widget_ops );
	}

	// Show widget
	function widget($args, $instance) {

		$title = apply_filters( 'widget_title', isset($instance['title']) ? $instance['title'] : '', $instance, $this->id_base );
		$tabs = array(
			array(
				'title'   => isset($instance['title_1']) ? $instance['title_1'] : '',
				'orderby' => isset($instance['orderby_1']) ? $instance['orderby_1'] : 'views',
				'post_type'	  => isset($instance['post_type_1']) ? $instance['post_type_1'] : 'post',
				'taxonomy'=> isset($instance['taxonomy_1']) ? $instance['taxonomy_1'] : 'category',
				'cat'    => isset($instance['cat_1']) ? $instance['cat_1'] : 0,
				'content' => ''
				),
			array(
				'title'   => isset($instance['title_2']) ? $instance['title_2'] : '',
				'orderby' => isset($instance['orderby_2']) ? $instance['orderby_2'] : 'comments',
				'post_type'	  => isset($instance['post_type_2']) ? $instance['post_type_2'] : 'post',
				'taxonomy'=> isset($instance['taxonomy_2']) ? $instance['taxonomy_2'] : 'category',
				'cat'    => isset($instance['cat_2']) ? $instance['cat_2'] : 0,
				'content' => ''
				),
			array(
				'title'   => isset($instance['title_3']) ? $instance['title_3'] : '',
				'orderby' => isset($instance['orderby_3']) ? $instance['orderby_3'] : 'likes',
				'post_type'	  => isset($instance['post_type_3']) ? $instance['post_type_3'] : 'post',
				'taxonomy'=> isset($instance['taxonomy_3']) ? $instance['taxonomy_3'] : 'category',
				'cat'    => isset($instance['cat_3']) ? $instance['cat_3'] : 0,
				'content' => ''
				)
			);

		$number = isset($instance['number']) ? (int) $instance['number'] : '';

		if ( isset( $instance['show_date'] ) && ( $instance['show_date'] === true || $instance['show_date'] === 'true' ) ) $instance['show_date'] = 1;
		if ( isset( $instance['show_image'] ) && ( $instance['show_image'] === true || $instance['show_image'] === 'true' ) ) $instance['show_image'] = 1;
		if ( isset( $instance['show_author'] ) && ( $instance['show_author'] === true || $instance['show_author'] === 'true' ) ) $instance['show_author'] = 1;
		if ( isset( $instance['show_counters'] ) && ( $instance['show_counters'] === true || $instance['show_counters'] === 'true' ) ) $instance['show_counters'] = 1;
		if ( isset( $instance['show_categories'] ) && ( $instance['show_categories'] === true || $instance['show_categories'] === 'true' ) ) $instance['show_categories'] = 1;
		if ( isset( $instance['show_rating'] ) && ( $instance['show_rating'] === true || $instance['show_rating'] === 'true' ) ) $instance['show_rating'] = 1;

		$tabs_count = 0;

		for ($i=0; $i<3; $i++) {
			if (empty($tabs[$i]['title'])) continue;
			$tabs_count++;
			$q_args = array(
				'post_status' => current_user_can('read_private_pages') && current_user_can('read_private_posts') ? array('publish', 'private') : 'publish',
				'post_password' => '',
				'posts_per_page' => $number,
				'ignore_sticky_posts' => true,
				'order' => 'DESC',
			);
			if ($tabs[$i]['orderby'] == 'views') {				// Most popular
				$q_args['meta_key'] = 'trx_addons_post_views_count';
				$q_args['orderby'] = 'meta_value_num';
			} else if ($tabs[$i]['orderby'] == 'likes') {		// Most liked
				$q_args['meta_key'] = 'trx_addons_post_likes_count';
				$q_args['orderby'] = 'meta_value_num';
			} else if ($tabs[$i]['orderby'] == 'comments') {	// Most commented
				$q_args['orderby'] = 'comment_count';
			} else if ($tabs[$i]['orderby'] == 'rating') {		// Ordered by rating
				$q_args['meta_key'] = 'trx_addons_reviews_post_mark';
				$q_args['orderby'] = 'meta_value_num';
			} else if ($tabs[$i]['orderby'] == 'title' || $tabs[$i]['orderby'] == 'post_title') {	// Title
				$q_args['orderby'] = 'title';
				$q_args['order'] = 'asc';
			} else if ($tabs[$i]['orderby'] == 'rand' || $tabs[$i]['orderby'] == 'random') {		// Random posts
				$q_args['orderby'] = 'rand';
			} else {											// Recent posts
				$q_args['orderby'] = 'date';
			}
			$q_args = trx_addons_query_add_posts_and_cats(apply_filters('trx_addons_filter_widget_posts_query_args', $q_args), '', $tabs[$i]['post_type'], $tabs[$i]['cat'], $tabs[$i]['taxonomy']);

			$q_args = apply_filters( 'trx_addons_filter_query_args', $q_args, 'widget_popular_posts' );

			$q = new WP_Query($q_args); 
			
			// Loop posts
			if ( $q->have_posts() ) {
				$post_number = 0;
				set_query_var('trx_addons_output_widgets_posts', '');
				while ($q->have_posts()) { $q->the_post();
					$post_number++;
					trx_addons_get_template_part('templates/tpl.posts-list.php',
												'trx_addons_args_widgets_posts',
												apply_filters('trx_addons_filter_widget_posts_args',
													array(
														'components' => in_array($tabs[$i]['orderby'], array('views', 'likes', 'comments', 'rating')) ? $tabs[$i]['orderby'] : 'comments',
														'show_image' => isset($instance['show_image']) ? (int) $instance['show_image'] : 0,
														'show_date' => isset($instance['show_date']) ? (int) $instance['show_date'] : 0,
														'show_author' => isset($instance['show_author']) ? (int) $instance['show_author'] : 0,
														'show_counters'	=> isset($instance['show_counters']) ? (int) $instance['show_counters'] : 0,
														'show_categories' => isset($instance['show_categories']) ? (int) $instance['show_categories'] : 0,
														'show_rating' => $tabs[$i]['orderby'] == 'rating'
														),
													$instance, 'trx_addons_widget_popular_posts')
												);
					if ($post_number >= $number) break;
				}
				$tabs[$i]['content'] .= get_query_var('trx_addons_output_widgets_posts');
			}
		}

		wp_reset_postdata();

		if ( $tabs[0]['title'].$tabs[0]['content'].$tabs[1]['title'].$tabs[1]['content'].$tabs[2]['title'].$tabs[2]['content'] ) {

			trx_addons_get_template_part(TRX_ADDONS_PLUGIN_WIDGETS . 'popular_posts/tpl.default.php',
										'trx_addons_args_widget_popular_posts', 
										apply_filters('trx_addons_filter_widget_args',
											array_merge($args, compact('title', 'tabs', 'tabs_count')),
											$instance, 'trx_addons_widget_popular_posts')
										);

			if ( ! is_customize_preview() && $tabs_count > 1 ) {
				wp_enqueue_script('jquery-ui-tabs', false, array('jquery','jquery-ui-core'), null, true);
				wp_enqueue_script('jquery-effects-fade', false, array('jquery','jquery-effects-core'), null, true);
			}
		}
	}

	// Update the widget settings
	function update($new_instance, $instance) {
		$instance = array_merge($instance, $new_instance);
		$instance['number'] = (int) $new_instance['number'];
		$instance['show_date'] = (int) $new_instance['show_date'];
		$instance['show_image'] = (int) $new_instance['show_image'];
		$instance['show_author'] = (int) $new_instance['show_author'];
		$instance['show_counters'] = (int) $new_instance['show_counters'];
		$instance['show_categories'] = (int) $new_instance['show_categories'];
		return apply_filters('trx_addons_filter_widget_args_update', $instance, $new_instance, 'trx_addons_widget_popular_posts');
	}

	// Displays the widget settings controls on the widget panel
	function form($instance) {
		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, apply_filters('trx_addons_filter_widget_args_default', array(
			'title' => '', 
			'title_1' => __('Viewed', 'trx_addons'), 
			'title_2' => __('Commented', 'trx_addons'), 
			'title_3' => __('Liked', 'trx_addons'), 
			'post_type_1' => 'post', 
			'post_type_2' => 'post', 
			'post_type_3' => 'post', 
			'taxonomy_1' => 'category', 
			'taxonomy_2' => 'category', 
			'taxonomy_3' => 'category', 
			'cat_1' => 0, 
			'cat_2' => 0, 
			'cat_3' => 0, 
			'orderby_1' => 'views', 
			'orderby_2' => 'comments', 
			'orderby_3' => 'likes', 
			'number' => '4', 
			'show_date' => '1', 
			'show_image' => '1', 
			'show_author' => '1', 
			'show_counters' => '1',
			'show_categories' => '1'
			), 'trx_addons_widget_popular_posts')
		);

		do_action('trx_addons_action_before_widget_fields', $instance, 'trx_addons_widget_popular_posts', $this);

		$this->show_field(array('name' => 'title',
								'title' => __('Widget title:', 'trx_addons'),
								'value' => $instance['title'],
								'type' => 'text'));
		
		do_action('trx_addons_action_after_widget_title', $instance, 'trx_addons_widget_popular_posts', $this);

		
		$this->show_field(array('name' => 'tab_1',
								'title' => __('Tab 1', 'trx_addons'),
								'type' => 'info'));

		$this->show_field(array('name' => 'title_1',
								'title' => __('Title:', 'trx_addons'),
								'value' => $instance['title_1'],
								'type' => 'text'));

		$this->show_field(array('name' => 'orderby_1',
								'title' => __("Order by:", 'trx_addons'),
								'value' => $instance['orderby_1'],
								'options' => trx_addons_get_list_widget_query_orderby(),
								'type' => 'select'));

		$this->show_field(array('name' => 'post_type_1',
								'title' => __('Post type:', 'trx_addons'),
								'value' => $instance['post_type_1'],
								'options' => trx_addons_get_list_posts_types(),
								'class' => 'trx_addons_post_type_selector',
								'type' => 'select'));
		
		$this->show_field(array('name' => 'taxonomy_1',
								'title' => __('Taxonomy:', 'trx_addons'),
								'value' => $instance['taxonomy_2'],
								'options' => trx_addons_get_list_taxonomies(false, $instance['post_type_1']),
								'class' => 'trx_addons_taxonomy_selector',
								'type' => 'select'));


		$tax_obj = get_taxonomy($instance['taxonomy_1']);

		$this->show_field(array('name' => 'cat_1',
								'title' => __('Category:', 'trx_addons'),
								'value' => $instance['cat_1'],
								'options' => trx_addons_array_merge(
												array( 0 => trx_addons_get_not_selected_text( ! empty( $tax_obj->label ) ? $tax_obj->label : __( '- Not Selected -', 'trx_addons' ) ) ),
												trx_addons_get_list_terms( false, $instance['taxonomy_1'], array( 'pad_counts' => true ) )
											),
								'class' => 'trx_addons_terms_selector',
								'type' => 'select'));


		$this->show_field(array('name' => 'tab_2',
								'title' => __('Tab 2', 'trx_addons'),
								'type' => 'info'));
		
		$this->show_field(array('name' => 'title_2',
								'title' => __('Title:', 'trx_addons'),
								'value' => $instance['title_2'],
								'type' => 'text'));

		$this->show_field(array('name' => 'orderby_2',
								'title' => __("Order by:", 'trx_addons'),
								'value' => $instance['orderby_2'],
								'options' => trx_addons_get_list_widget_query_orderby(),
								'type' => 'select'));

		$this->show_field(array('name' => 'post_type_2',
								'title' => __('Post type:', 'trx_addons'),
								'value' => $instance['post_type_2'],
								'options' => trx_addons_get_list_posts_types(),
								'class' => 'trx_addons_post_type_selector',
								'type' => 'select'));
		
		$this->show_field(array('name' => 'taxonomy_2',
								'title' => __('Taxonomy:', 'trx_addons'),
								'value' => $instance['taxonomy_2'],
								'options' => trx_addons_get_list_taxonomies(false, $instance['post_type_2']),
								'class' => 'trx_addons_taxonomy_selector',
								'type' => 'select'));


		$tax_obj = get_taxonomy($instance['taxonomy_2']);

		$this->show_field(array('name' => 'cat_2',
								'title' => __('Category:', 'trx_addons'),
								'value' => $instance['cat_2'],
								'options' => trx_addons_array_merge(
												array( 0 => trx_addons_get_not_selected_text( ! empty( $tax_obj->label ) ? $tax_obj->label : __( '- Not Selected -', 'trx_addons' ) ) ),
												trx_addons_get_list_terms( false, $instance['taxonomy_2'], array( 'pad_counts' => true ) )
											),
								'class' => 'trx_addons_terms_selector',
								'type' => 'select'));
		
		
		$this->show_field(array('name' => 'tab_3',
								'title' => __('Tab 3', 'trx_addons'),
								'type' => 'info'));
		
		$this->show_field(array('name' => 'title_3',
								'title' => __('Title:', 'trx_addons'),
								'value' => $instance['title_3'],
								'type' => 'text'));

		$this->show_field(array('name' => 'orderby_3',
								'title' => __("Order by:", 'trx_addons'),
								'value' => $instance['orderby_3'],
								'options' => trx_addons_get_list_widget_query_orderby(),
								'type' => 'select'));

		$this->show_field(array('name' => 'post_type_3',
								'title' => __('Post type:', 'trx_addons'),
								'value' => $instance['post_type_3'],
								'options' => trx_addons_get_list_posts_types(),
								'class' => 'trx_addons_post_type_selector',
								'type' => 'select'));
		
		$this->show_field(array('name' => 'taxonomy_3',
								'title' => __('Taxonomy:', 'trx_addons'),
								'value' => $instance['taxonomy_3'],
								'options' => trx_addons_get_list_taxonomies(false, $instance['post_type_3']),
								'class' => 'trx_addons_taxonomy_selector',
								'type' => 'select'));


		$tax_obj = get_taxonomy($instance['taxonomy_3']);

		$this->show_field(array('name' => 'cat_3',
								'title' => __('Category:', 'trx_addons'),
								'value' => $instance['cat_3'],
								'options' => trx_addons_array_merge(
												array( 0 => trx_addons_get_not_selected_text( ! empty( $tax_obj->label ) ? $tax_obj->label : __( '- Not Selected -', 'trx_addons' ) ) ),
												trx_addons_get_list_terms( false, $instance['taxonomy_3'], array( 'pad_counts' => true ) )
											),
								'class' => 'trx_addons_terms_selector',
								'type' => 'select'));
		
		$this->show_field(array('name' => 'info',
								'title' => __('Common params', 'trx_addons'),
								'type' => 'info'));

		$this->show_field(array('name' => 'number',
								'title' => __('Number posts to show:', 'trx_addons'),
								'value' => max(1, (int) $instance['number']),
								'type' => 'text'));

		$this->show_field(array('name' => 'show_image',
								'title' => __("Show post's image:", 'trx_addons'),
								'value' => (int) $instance['show_image'],
								'options' => trx_addons_get_list_show_hide(false, true),
								'type' => 'radio'));

		$this->show_field(array('name' => 'show_author',
								'title' => __("Show post's author:", 'trx_addons'),
								'value' => (int) $instance['show_author'],
								'options' => trx_addons_get_list_show_hide(false, true),
								'type' => 'radio'));

		$this->show_field(array('name' => 'show_date',
								'title' => __("Show post's date:", 'trx_addons'),
								'value' => (int) $instance['show_date'],
								'options' => trx_addons_get_list_show_hide(false, true),
								'type' => 'radio'));

		$this->show_field(array('name' => 'show_counters',
								'title' => __("Show post's counters:", 'trx_addons'),
								'value' => (int) $instance['show_counters'],
								'options' => trx_addons_get_list_show_hide(false, true),
								'type' => 'radio'));

		$this->show_field(array('name' => 'show_categories',
								'title' => __("Show post's categories:", 'trx_addons'),
								'value' => (int) $instance['show_categories'],
								'options' => trx_addons_get_list_show_hide(false, true),
								'type' => 'radio'));
		
		do_action('trx_addons_action_after_widget_fields', $instance, 'trx_addons_widget_popular_posts', $this);
	}
}


// Add shortcodes
//----------------------------------------------------------------------------
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'popular_posts/popular_posts-sc.php';

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'popular_posts/popular_posts-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'popular_posts/popular_posts-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'popular_posts/popular_posts-sc-vc.php';
}
