<?php
/**
 * Widget: Posts by Rating
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Load widget
if (!function_exists('trx_addons_widget_rating_posts_load')) {
	add_action( 'widgets_init', 'trx_addons_widget_rating_posts_load' );
	function trx_addons_widget_rating_posts_load() {
		register_widget('trx_addons_widget_rating_posts');
	}
}

// Widget Class
class trx_addons_widget_rating_posts extends TRX_Addons_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_rating_posts', 'description' => esc_html__('The most rating posts (extended) with images and post meta', 'trx_addons'));
		parent::__construct( 'trx_addons_widget_rating_posts', esc_html__('ThemeREX Posts by Rating', 'trx_addons'), $widget_ops );
	}

	// Show widget
	function widget($args, $instance) {

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '', $instance, $this->id_base);
		$number = !empty($instance['number']) ? (int) $instance['number'] : 4;
		$post_type = !empty($instance['post_type']) ? $instance['post_type'] : 'post';
		$mark_type = !empty($instance['mark_type']) ? $instance['mark_type'] : 'post';

		$q_args = array(
			'numberposts' => $number,
			'offset' => 0,
			'orderby' => 'meta_value_num',
			'order' => 'DESC',
			'meta_key' => sprintf('trx_addons_reviews_%s_mark', $mark_type == 'post' ? 'post' : 'user'),
			'post_type' => $post_type,
			'post_status' => current_user_can('read_private_pages') && current_user_can('read_private_posts') ? array('publish', 'private') : 'publish',
			'ignore_sticky_posts' => true,
			// Attention! Parameter 'suppress_filters' is damage WPML-queries!
    	);

		$q_args = apply_filters( 'trx_addons_filter_query_args', $q_args, 'widget_rating_posts' );

		$q = new WP_Query($q_args); 

		// Loop posts
		if ( $q->have_posts() ) {
			$post_number = 0;
			set_query_var('trx_addons_output_widgets_posts', '');
			while ($q->have_posts()) { $q->the_post();
				$post_number++;
				trx_addons_get_template_part('templates/tpl.posts-list.php',
											'trx_addons_args_widgets_posts', 
											apply_filters('trx_addons_filter_widget_posts_args', array(
															'components' => 'rating',
															'mark_type' => $mark_type,
															'show_image' => isset($instance['show_image']) ? (int) $instance['show_image'] : 0,
															'show_date' => isset($instance['show_date']) ? (int) $instance['show_date'] : 0,
															'show_author' => isset($instance['show_author']) ? (int) $instance['show_author'] : 0,
															'show_counters'	=> isset($instance['show_counters']) ? (int) $instance['show_counters'] : 0,
															'show_categories' => isset($instance['show_categories']) ? (int) $instance['show_categories'] : 0,
															'show_rating' => 1
															),
															$instance, 'trx_addons_widget_rating_posts')
											);
				if ($post_number >= $number) break;
			}
			wp_reset_postdata();
		}

		$output = get_query_var('trx_addons_output_widgets_posts');

		if (!empty($output)) {
			trx_addons_get_template_part(TRX_ADDONS_PLUGIN_REVIEWS . 'rating_posts/tpl.default.php',
											'trx_addons_args_widget_rating_posts', 
											apply_filters('trx_addons_filter_widget_args',
												array_merge($args, compact('title', 'output')),
											$instance, 'trx_addons_widget_rating_posts')
										);
		}
	}

	// Update the widget settings
	function update($new_instance, $instance) {
		$instance = array_merge($instance, $new_instance);
		$instance['number'] = (int)$new_instance['number'];
		$instance['show_date'] = (int)$new_instance['show_date'];
		$instance['show_image'] = (int)$new_instance['show_image'];
		$instance['show_author'] = (int)$new_instance['show_author'];
		$instance['show_counters'] = (int)$new_instance['show_counters'];
		$instance['show_categories'] = (int)$new_instance['show_categories'];
		return apply_filters('trx_addons_filter_widget_args_update', $instance, $new_instance, 'trx_addons_widget_rating_posts');
	}

	// Displays the widget settings controls on the widget panel
	function form($instance) {
		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, apply_filters('trx_addons_filter_widget_args_default', array(
			'title' => '',
			'number' => '4',
			'post_type' => '',
			'mark_type' => 'post',
			'show_date' => '1',
			'show_image' => '1',
			'show_author' => '1',
			'show_counters' => '1',
			'show_categories' => '1'
			), 'trx_addons_widget_rating_posts')
		);
		
		do_action('trx_addons_action_before_widget_fields', $instance, 'trx_addons_widget_rating_posts', $this);
		
		$this->show_field(array('name' => 'title',
								'title' => __('Widget title:', 'trx_addons'),
								'value' => $instance['title'],
								'type' => 'text'));
		
		do_action('trx_addons_action_after_widget_title', $instance, 'trx_addons_widget_rating_posts', $this);
		
		$this->show_field(array('name' => 'number',
								'title' => __('Number posts to show:', 'trx_addons'),
								'value' => max(1, (int) $instance['number']),
								'type' => 'text'));
								
		$this->show_field(array('name' => 'mark_type',
								'title' => __('Whose marks to use?', 'trx_addons'),
								'value' => $instance['mark_type'],
								'options' => trx_addons_reviews_mark_type_list(),
								'type' => 'radio'));
								
		$this->show_field(array('name' => 'post_type',
								'title' => __('Post type:', 'trx_addons'),
								'value' => $instance['post_type'],
								'options' => trx_addons_get_list_reviews_posts_types(),
								'class' => 'trx_addons_post_type_selector',
								'type' => 'select'));

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
		
		do_action('trx_addons_action_after_widget_fields', $instance, 'trx_addons_widget_rating_posts', $this);
	}
}


// Add shortcodes
//----------------------------------------------------------------------------
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_REVIEWS . 'rating_posts/rating_posts-sc.php';

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_REVIEWS . 'rating_posts/rating_posts-sc-elementor.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_REVIEWS . 'rating_posts/rating_posts-sc-vc.php';
}
