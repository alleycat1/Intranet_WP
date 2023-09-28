<?php
/**
 * Widget: Posts or Revolution slider
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Load widget
if (!function_exists('trx_addons_widget_slider_load')) {
	add_action( 'widgets_init', 'trx_addons_widget_slider_load' );
	function trx_addons_widget_slider_load() {
		register_widget( 'trx_addons_widget_slider' );
	}
}

// Widget Class
class trx_addons_widget_slider extends TRX_Addons_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'widget_slider', 'description' => esc_html__('Display theme slider', 'trx_addons') );
		parent::__construct( 'trx_addons_widget_slider', esc_html__('ThemeREX Slider', 'trx_addons'), $widget_ops );
	}

	// Show widget
	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters( 'widget_title', isset($instance['title']) ? $instance['title'] : '', $instance, $this->id_base );
		$engine = isset($instance['engine']) ? $instance['engine'] : 'swiper';

		// Before widget (defined by themes)
		trx_addons_show_layout($before_widget);

		// Display the widget title if one was input (before and after defined by themes)
		if ($title)	trx_addons_show_layout($before_title . $title . $after_title);

		// Widget body
		$html = '';
		if (in_array($engine, array('swiper', 'elastistack'))) {
			$slider_id = isset($instance['slider_id']) && !empty($instance['slider_id'])
									? $instance['slider_id']
									: ( isset($instance['id'])
										? $instance['id'] 
										: trx_addons_generate_id( 'trx_addons_widget_slider_' )
										);
			$slider_style = isset($instance['slider_style']) ? $instance['slider_style'] : 'default';
			$effect = isset($instance['effect']) ? $instance['effect'] : 'slide';
			$slides = isset($instance['slides']) ? $instance['slides'] : array();
			$slides_type = isset($instance['slides_type']) ? $instance['slides_type'] : 'bg';
			$slides_ratio = isset($instance['slides_ratio']) ? $instance['slides_ratio'] : '16:9';
			$slides_per_view = isset($instance['slides_per_view'])
								&& in_array($effect, array('slide', 'coverflow', 'swap', 'cards', 'creative'))
									? $instance['slides_per_view'] 
									: 1;
			$slides_space = isset($instance['slides_space']) ? $instance['slides_space'] : 1;
			$slides_parallax = isset($instance['slides_parallax']) && $effect == 'slide' && $slides_per_view == 1 ? (float)$instance['slides_parallax'] : 0;
			$slides_ratio = isset($instance['slides_ratio']) ? $instance['slides_ratio'] : '16:9';
			$slides_centered = isset($instance['slides_centered']) && (int)$instance['slides_centered'] > 0 ? 'yes' : 'no';
			$slides_overflow = isset($instance['slides_overflow']) && (int)$instance['slides_overflow'] > 0 ? 'yes' : 'no';
			$mouse_wheel = isset($instance['mouse_wheel']) && (int)$instance['mouse_wheel'] > 0 ? 'yes' : 'no';
			$autoplay = ! isset($instance['autoplay']) || (int)$instance['autoplay'] > 0 ? 'yes' : 'no';
			$loop = ! isset($instance['loop']) || (int)$instance['loop'] > 0 ? 'yes' : 'no';
			$free_mode = isset($instance['free_mode']) && (int)$instance['free_mode'] > 0 ? 'yes' : 'no';
			$noresize = isset($instance['noresize']) && (int)$instance['noresize'] > 0 ? 'yes' : 'no';
			$height = isset($instance['height']) ? $instance['height'] : '';
			$post_type = isset($instance['post_type']) ? $instance['post_type'] : 'post';
			$taxonomy = isset($instance['taxonomy']) ? $instance['taxonomy'] : 'category';
			$category = isset($instance['category']) ? (int)$instance['category'] : 0;
			$posts = isset($instance['posts']) ? $instance['posts'] : 5;
			$speed = isset($instance['speed']) ? max(300, (int)$instance['speed']) : 600;
			$interval = isset($instance['interval']) && $instance['interval'] !== '' ? max(0, (int)$instance['interval']) : mt_rand(5000, 10000);
			$titles = isset($instance['titles']) ? $instance['titles'] : 'center';
			$large = isset($instance['large']) && (int)$instance['large'] > 0 ? 'yes' : 'no';
			$noswipe = isset($instance['noswipe']) && (int)$instance['noswipe'] > 0 ? 'yes' : 'no';
			$controls = isset($instance['controls']) && (int)$instance['controls'] > 0 ? 'yes' : 'no';
			$controls_pos = isset($instance['controls_pos']) ? $instance['controls_pos'] : "side";
			$label_prev = isset($instance['label_prev']) ? $instance['label_prev'] : '';
			$label_next = isset($instance['label_next']) ? $instance['label_next'] : '';
			$pagination = isset($instance['pagination']) && (int)$instance['pagination'] > 0 ? 'yes' : 'no';
			$pagination_type = isset($instance['pagination_type']) ? $instance['pagination_type'] : "bullets";
			$pagination_pos = isset($instance['pagination_pos']) ? $instance['pagination_pos'] : "bottom";
			$direction = isset($instance['direction']) && $instance['direction'] == 'vertical' ? "vertical" : "horizontal";
			$slave_id = ! empty($instance['slave_id']) ? $instance['slave_id'] : '';
			$controller = isset($instance['controller']) && (int)$instance['controller'] > 0 ? 'yes' : 'no';
			$controller_pos = isset($instance['controller_pos']) ? $instance['controller_pos'] : "right";
			$controller_style = isset($instance['controller_style']) ? $instance['controller_style'] : "default";
			$controller_controls = isset($instance['controller_controls']) && $instance['controller_controls'] > 0 ? 'yes' : 'no';
			$controller_effect = isset($instance['controller_effect']) ? $instance['controller_effect'] : 'slide';
			$controller_per_view = isset($instance['controller_per_view']) ? $instance['controller_per_view'] : 3;
			$controller_space = isset($instance['controller_space']) ? $instance['controller_space'] : 0;
			$controller_height = isset($instance['controller_height']) ? $instance['controller_height'] : '';
			$count = $ids = $posts;
			if (strpos($ids, ',')!==false) {
				$count = 0;
			} else {
				$ids = '';
				if (empty($count)) $count = count($slides) > 1 ? count($slides) : 3;
			}
			if ($count > 0 || !empty($ids)) {
				$html = trx_addons_get_slider_layout(
							apply_filters('trx_addons_filter_widget_args',
								array(
									'mode'                => empty($slides) ? 'posts' : 'custom',
									'engine'              => $engine,
									'style'               => $slider_style,
									'slides_type'         => $slides_type,
									'slides_space'        => $slides_space,
									'slides_parallax'     => $slides_parallax,
									'slides_ratio'        => $slides_ratio,
									'slides_centered'     => $slides_centered,
									'slides_overflow'     => $slides_overflow,
									'noresize'            => $noresize,
									'effect'              => $effect,
									'noswipe'             => $noswipe,
									'controls'            => $controls,
									'controls_pos'        => $controls_pos,
									'label_prev'          => $label_prev,
									'label_next'          => $label_next,
									'pagination'          => $pagination,
									'pagination_type'     => $pagination_type,
									'pagination_pos'      => $pagination_pos,
									'direction'           => $direction,
									'slave_id'            => $slave_id,
									'controller'          => $controller,
									'controller_pos'      => $controller_pos,
									'controller_style'    => $controller_style,
									'controller_controls' => $controller_controls,
									'controller_per_view' => $controller_per_view,
									'controller_effect'   => $controller_effect,
									'controller_space'    => $controller_space,
									'controller_height'   => $controller_height,
									'titles'              => $titles,
									'large'               => $large,
									'speed'               => $speed,
									'interval'            => $interval,
									'height'              => $height,
									'per_view'            => $slides_per_view,
									'mouse_wheel'         => $mouse_wheel,
									'autoplay'            => $autoplay,
									'loop'                => $loop,
									'free_mode'           => $free_mode,
									'post_type'           => $post_type,
									'taxonomy'            => $taxonomy,
									'cat'                 => $category,
									'ids'                 => $ids,
									'count'               => $count,
									'orderby'             => "date",
									'order'               => "desc",
									'class'               => "",
									'id'                  => $slider_id
								),
								$instance,
								'trx_addons_widget_slider'
							),
							$slides
						);
			}

		} else if ( $engine == 'revo' ) {
			$alias = isset( $instance['alias'] ) ? $instance['alias'] : '';
			if ( ! empty( $alias ) ) {
				// -- Fix to compatibility with RevSlider 6.5+ (part 1)
				global $rs_loaded_by_editor;
				if ( function_exists( 'trx_addons_elm_is_edit_mode' ) && trx_addons_elm_is_edit_mode() ) {
					$rs_loaded_by_editor = true;
				}
				// -- End fix (part 1)
				$html = do_shortcode( '[rev_slider alias="' . esc_attr( $alias ) . '"][/rev_slider]' );
				if ( empty( $html ) ) {
					$html = do_shortcode( '[rev_slider ' . esc_attr($alias) . '][/rev_slider]' );
				}
				// -- Fix to compatibility with RevSlider 6.5+ (part 2)
				if ( ! empty( $html ) ) {
					$html = sprintf( '<div class="wp-block-themepunch-revslider %2$d">%1$s</div>', $html, $rs_loaded_by_editor );
				}
				if ( function_exists( 'trx_addons_elm_is_edit_mode' ) && trx_addons_elm_is_edit_mode() ) {
					$rs_loaded_by_editor = false;
				}
				// -- End fix (part 2)
			}
		}
		if ( ! empty( $html ) ) {
			// Disable lazy load in slider
			$GLOBALS['TRX_ADDONS_STORAGE']['lazy_load_is_off'] = trx_addons_lazy_load_is_off();
			if ( ! $GLOBALS['TRX_ADDONS_STORAGE']['lazy_load_is_off'] ) {
				trx_addons_lazy_load_off();
			}
			// Show slider layout
			?>
			<div class="slider_wrap slider_engine_<?php echo esc_attr($engine); ?><?php if ($engine=='revo') echo ' slider_alias_'.esc_attr($alias); ?>">
				<?php trx_addons_show_layout($html); ?>
			</div>
			<?php 
			// Enable lazy load again
			if ( empty( $GLOBALS['TRX_ADDONS_STORAGE']['lazy_load_is_off'] ) ) {
				$GLOBALS['TRX_ADDONS_STORAGE']['lazy_load_is_off'] = false;
				trx_addons_lazy_load_on();
			}
		}

		// After widget (defined by themes)
		trx_addons_show_layout($after_widget);
	}

	// Update the widget settings.
	function update( $new_instance, $instance ) {
		$instance = array_merge($instance, $new_instance);
		$instance['slides_ratio'] = str_replace( array('-', '/', ' '), array( ':', ':', ''), $new_instance['slides_ratio'] );
		$instance['slides_parallax'] = isset( $new_instance['slides_parallax'] ) ? max( 0, min( 1, (float)$new_instance['slides_parallax'] ) ) : 0;
		$instance['mouse_wheel'] = isset( $new_instance['mouse_wheel'] ) && (int)$new_instance['mouse_wheel'] > 0 ? 1 : 0;
		$instance['autoplay'] = isset( $new_instance['autoplay'] ) && (int)$new_instance['autoplay'] > 0 ? 1 : 0;
		$instance['loop'] = isset( $new_instance['loop'] ) && (int)$new_instance['loop'] > 0 ? 1 : 0;
		$instance['free_mode'] = isset( $new_instance['free_mode'] ) && (int)$new_instance['free_mode'] > 0 ? 1 : 0;
		$instance['noswipe'] = isset( $new_instance['noswipe'] ) && (int)$new_instance['noswipe'] > 0 ? 1 : 0;
		$instance['noresize'] = isset( $new_instance['noresize'] ) && (int)$new_instance['noresize'] > 0 ? 1 : 0;
		$instance['slides_centered'] = isset( $new_instance['slides_centered'] ) && (int)$new_instance['slides_centered'] > 0 ? 1 : 0;
		$instance['slides_overflow'] = isset( $new_instance['slides_overflow'] ) && (int)$new_instance['slides_overflow'] > 0 ? 1 : 0;
		$instance['large'] = max(0, min(1, intval( $new_instance['large'] )));
		$instance['controls'] = max(0, min(1, intval( $new_instance['controls'] )));
		$instance['pagination'] = max(0, min(1, intval( $new_instance['pagination'] )));
		$instance['controller'] = max(0, min(1, intval( $new_instance['controller'] )));
		$instance['controller_controls'] = max(0, min(1, intval( $new_instance['controller_controls'] )));
		$instance['controller_per_view'] = max(0, intval( $new_instance['controller_per_view'] ));
		$instance['controller_space'] = max(0, intval( $new_instance['controller_space'] ));
		return apply_filters('trx_addons_filter_widget_args_update', $instance, $new_instance, 'trx_addons_widget_slider');
	}

	// Displays the widget settings controls on the widget panel.
	function form( $instance ) {
		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, apply_filters('trx_addons_filter_widget_args_default', array(
			'title' => '',
			'engine' => 'swiper',
			'slider_style' => 'default',
			'slides_per_view' => '1',
			'slides_space' => '0',
			'slides_parallax' => '0',
			'slides_ratio' => '16:9',
			'slides_centered' => '0',
			'slides_overflow' => '0',
			'noresize' => '0',
			'mouse_wheel' => '0',
			'free_mode' => '0',
			'noswipe' => '0',
			'autoplay' => '1',
			'loop' => '1',
			'effect' => 'slide',
			'height' => '',
			'alias' => '',
			'titles' => 'center',
			'large' => 0,
			'controls' => 0,
			'controls_pos' => 'side',
			'label_prev' => '',
			'label_next' => '',
			'pagination' => 0,
			'pagination_type' => 'bullets',
			'pagination_pos' => 'bottom',
			'direction' => 'horizontal',
			'post_type' => 'post',
			'taxonomy' => 'category',
			'category' => '0',
			'posts' => '5',
			'speed' => '600',
			'interval' => '7000',
			'slave_id' => '',
			'controller' => 0,				// Show controller with slides images and title
			'controller_pos' => 'right',	// left | right | bottom - position of the slider controller
			'controller_style' => 'default',// Style of controller
			'controller_controls' => 0, 	// Show arrows in the controller
			'controller_effect' => 'slide',	// slide | fade | cube | coverflow | flip - change slides effect for the controller
			'controller_per_view' => 3, 	// Slides per view in the controller
			'controller_space' => 0, 		// Space between slides in the controller
			'controller_height' => '', 		// Height of the the controller
			), 'trx_addons_widget_slider')
		);
		
		do_action('trx_addons_action_before_widget_fields', $instance, 'trx_addons_widget_slider', $this);

		$this->show_field(array('name' => 'title',
								'title' => __('Title:', 'trx_addons'),
								'value' => $instance['title'],
								'type' => 'text'));
		
		do_action('trx_addons_action_after_widget_title', $instance, 'trx_addons_widget_slider', $this);
		
		$this->show_field(array('name' => 'engine',
								'title' => __('Slider engine:', 'trx_addons'),
								'value' => $instance['engine'],
								'options' => trx_addons_get_list_sc_slider_engines(),
								'type' => 'select'));

		if ( trx_addons_exists_revslider() && trx_addons_components_is_allowed('api', 'revslider') ) {
			$this->show_field(array('name' => 'alias',
									'title' => __('Revolution Slider alias:', 'trx_addons'),
									'value' => $instance['alias'],
									'options' => trx_addons_get_list_revsliders(),
									'dependency' => array(
										'engine' => array( 'revo' )
									),
									'type' => 'select'));
		}

		$this->show_field(array('name' => 'slider_style',
								'title' => __('Swiper style:', 'trx_addons'),
								'value' => $instance['slider_style'],
								'options' => trx_addons_components_get_allowed_layouts('widgets', 'slider'),
								'dependency' => array(
									'engine' => array( 'swiper' )
								),
								'type' => 'select'));

		$this->show_field(array('name' => 'effect',
								'title' => __('Swiper effect:', 'trx_addons'),
								'value' => $instance['effect'],
								'options' => trx_addons_get_list_sc_slider_effects(),
								'dependency' => array(
									'engine' => array( 'swiper' )
								),
								'type' => 'select'));

		$this->show_field(array('name' => 'direction',
								'title' => __('Direction:', 'trx_addons'),
								'value' => $instance['direction'],
								'options' => trx_addons_get_list_sc_directions(),
								'dependency' => array(
									'engine' => array( 'swiper' ),
									'effect' => array( 'slide', 'coverflow', 'swap' )
								),
								'type' => 'radio'));

		$this->show_field(array('name' => 'slides_per_view',
								'title' => __('Slides per view in the Swiper:', 'trx_addons'),
								'value' => (int) $instance['slides_per_view'],
								'dependency' => array(
									'engine' => array( 'swiper' ),
									'effect' => array( 'slide', 'coverflow', 'swap', 'cards', 'creative' )
								),
								'type' => 'text'));
		
		$this->show_field(array('name' => 'slides_space',
								'title' => __('Space between slides in the Swiper:', 'trx_addons'),
								'value' => (int) $instance['slides_space'],
								'dependency' => array(
									'engine' => array( 'swiper' ),
									'effect' => array( 'slide', 'coverflow', 'swap', 'cards', 'creative' )
								),
								'type' => 'text'));

		$this->show_field(array('name' => 'slides_parallax',
								'title' => __('Parallax coeff:', 'trx_addons'),
								'description' => wp_kses_data( __("Parallax coefficient from 0.0 to 1.0 to shift images while slides change", 'trx_addons') ),
								'value' => (float) $instance['slides_parallax'],
								'dependency' => array(
									'engine' => array( 'swiper' ),
									'effect' => array( 'slide' ),
									'slides_per_view' => array( 1 )
								),
								'type' => 'text'));

		do_action('trx_addons_action_slider_widget_before_query_params', $instance, 'trx_addons_widget_slider', $this);

		// Query parameters
		$this->show_field(array('name' => 'slider_query_info',
								'title' => __('Query params', 'trx_addons'),
								'type' => 'info'));

		$this->show_field(array('name' => 'post_type',
								'title' => __('Post type:', 'trx_addons'),
								'value' => $instance['post_type'],
								'options' => trx_addons_get_list_posts_types(),
								'class' => 'trx_addons_post_type_selector',
								'dependency' => array(
									'engine' => array( 'swiper', 'elastistack' )
								),
								'type' => 'select'));
		
		$this->show_field(array('name' => 'taxonomy',
								'title' => __('Taxonomy:', 'trx_addons'),
								'value' => $instance['taxonomy'],
								'options' => trx_addons_get_list_taxonomies(false, $instance['post_type']),
								'class' => 'trx_addons_taxonomy_selector',
								'type' => 'select'));
		
		$tax_obj = get_taxonomy($instance['taxonomy']);
		$this->show_field(array('name' => 'category',
								'title' => __('Category:', 'trx_addons'),
								'value' => $instance['category'],
								'options' => trx_addons_array_merge(
													array( 0 => trx_addons_get_not_selected_text( ! empty( $tax_obj->label ) ? $tax_obj->label : __( '- Not Selected -', 'trx_addons' ) ) ),
													trx_addons_get_list_terms( false, $instance['taxonomy'], array( 'pad_counts' => true ) )
											),
								'class' => 'trx_addons_terms_selector',
								'dependency' => array(
									'engine' => array( 'swiper', 'elastistack' )
								),
								'type' => 'select'));
		
		$this->show_field(array('name' => 'posts',
								'title' => __('Number of posts to show in Swiper:', 'trx_addons'),
								'value' => (int) $instance['posts'],
								'dependency' => array(
									'engine' => array( 'swiper', 'elastistack' )
								),
								'type' => 'text'));

		do_action('trx_addons_action_slider_widget_before_controls_params', $instance, 'trx_addons_widget_slider', $this);

		// Controls
		$this->show_field(array('name' => 'slider_controls_info',
								'title' => __('Controls', 'trx_addons'),
								'type' => 'info'));

		$this->show_field(array('name' => 'slave_id',
								'title' => __('Slave ID:', 'trx_addons'),
								'value' => ! empty( $instance['slave_id'] ) ? $instance['slave_id'] : '',
								'dependency' => array(
									'engine' => array( 'swiper' ),
								),
								'type' => 'text'));

		$this->show_field(array('name' => 'controls',
								'title' => __('Show arrows:', 'trx_addons'),
								'value' => (int) $instance['controls'],
								'options' => trx_addons_get_list_show_hide(false, true),
								'dependency' => array(
									'engine' => array( 'swiper', 'elastistack' )
								),
								'type' => 'radio'));

		$this->show_field(array('name' => 'controls_pos',
								'title' => __('Controls position:', 'trx_addons'),
								'value' => $instance['controls_pos'],
								'options' => trx_addons_get_list_sc_slider_controls(''),
								'dependency' => array(
									'engine' => array( 'swiper' ),
									'controls' => array( 1 )
								),
								'type' => 'select'));

		$this->show_field(array('name' => 'label_prev',
								'title' => __('Prev Slide:', 'trx_addons'),
								'value' => $instance['label_prev'],
								'dependency' => array(
									'slider_style' => array( 'modern' ),
									'controls' => array( 1 )
								),
								'type' => 'text'));

		$this->show_field(array('name' => 'label_next',
								'title' => __('Next Slide:', 'trx_addons'),
								'description' => wp_kses_data( __("Label of the 'Next Slide' button in the Swiper (Modern style). Use '|' to break line", 'trx_addons') ),
								'value' => $instance['label_next'],
								'dependency' => array(
									'slider_style' => array( 'modern' ),
									'controls' => array( 1 )
								),
								'type' => 'text'));
		
		$this->show_field(array('name' => 'pagination',
								'title' => __('Show pagination:', 'trx_addons'),
								'value' => (int) $instance['pagination'],
								'options' => trx_addons_get_list_show_hide(false, true),
								'dependency' => array(
									'engine' => array( 'swiper' ),
									'effect' => array( '^swap' )
								),
								'type' => 'radio'));

		$this->show_field(array('name' => 'pagination_type',
								'title' => __('Pagination type:', 'trx_addons'),
								'value' => $instance['pagination_type'],
								'options' => trx_addons_get_list_sc_slider_paginations_types(),
								'dependency' => array(
									'engine' => array( 'swiper' ),
									'pagination' => array( 1 )
								),
								'type' => 'select'));

		$this->show_field(array('name' => 'pagination_pos',
								'title' => __('Pagination position:', 'trx_addons'),
								'value' => $instance['pagination_pos'],
								'options' => trx_addons_get_list_sc_slider_paginations(''),
								'dependency' => array(
									'engine' => array( 'swiper' ),
									'pagination' => array( 1 )
								),
								'type' => 'select'));

		$this->show_field(array('name' => 'mouse_wheel',
								'title' => '',
								'label' => __('Enable mouse wheel', 'trx_addons'),
								'value' => (int) $instance['mouse_wheel'],
								'dependency' => array(
									'engine' => array( 'swiper' ),
								),
								'type' => 'checkbox'));

		$this->show_field(array('name' => 'noswipe',
								'title' => '',
								'label' => __('Disable swipe', 'trx_addons'),
								'value' => (int) $instance['noswipe'],
								'dependency' => array(
									'engine' => array( 'swiper' ),
								),
								'type' => 'checkbox'));

		$this->show_field(array('name' => 'autoplay',
								'title' => '',
								'label' => __('Enable autoplay', 'trx_addons'),
								'value' => (int) $instance['autoplay'],
								'dependency' => array(
									'engine' => array( 'swiper' ),
								),
								'type' => 'checkbox'));
		
		$this->show_field(array('name' => 'speed',
								'title' => __('Slides change speed (in msec., 1000=1sec.)', 'trx_addons'),
								'value' => (int) $instance['speed'],
								'dependency' => array(
									'engine' => array( 'swiper' ),
								),
								'type' => 'text'));
		
		$this->show_field(array('name' => 'interval',
								'title' => __('Swiper interval (in msec., 1000=1sec.)', 'trx_addons'),
								'value' => (int) $instance['interval'],
								'dependency' => array(
									'engine' => array( 'swiper' ),
								),
								'type' => 'text'));

		$this->show_field(array('name' => 'loop',
								'title' => '',
								'label' => __('Enable loop mode', 'trx_addons'),
								'value' => (int) $instance['loop'],
								'dependency' => array(
									'engine' => array( 'swiper' ),
								),
								'type' => 'checkbox'));

		$this->show_field(array('name' => 'free_mode',
								'title' => '',
								'label' => __('Enable free mode', 'trx_addons'),
								'value' => (int) $instance['free_mode'],
								'dependency' => array(
									'engine' => array( 'swiper' ),
								),
								'type' => 'checkbox'));

		do_action('trx_addons_action_slider_widget_before_layout_params', $instance, 'trx_addons_widget_slider', $this);

		// Layout
		$this->show_field(array('name' => 'slider_layout_info',
								'title' => __('Layout', 'trx_addons'),
								'type' => 'info'));

		$this->show_field(array('name' => 'noresize',
								'title' => '',
								'label' => __("No resize slide's content", 'trx_addons'),
								'value' => (int) $instance['noresize'],
								'dependency' => array(
									'engine' => array( 'swiper', 'elastistack' )
								),
								'type' => 'checkbox'));

		$this->show_field(array('name' => 'height',
								'title' => __('Slider height:', 'trx_addons'),
								'value' => $instance['height'],
								'dependency' => array(
									'noresize' => array( 1 )
								),
								'type' => 'text'));

		$this->show_field(array('name' => 'slides_ratio',
								'title' => __('Slides ratio:', 'trx_addons'),
								'value' => $instance['slides_ratio'],
								'dependency' => array(
									'noresize' => array( 0 )
								),
								'type' => 'text'));
		
		$this->show_field(array('name' => 'slides_centered',
								'title' => '',
								'label' => __('Center active slide', 'trx_addons'),
								'value' => (int) $instance['slides_centered'],
								'dependency' => array(
									'engine' => array( 'swiper' )
								),
								'type' => 'checkbox'));
		
		$this->show_field(array('name' => 'slides_overflow',
								'title' => '',
								'label' => __('Slides oveflow visible', 'trx_addons'),
								'value' => (int) $instance['slides_overflow'],
								'dependency' => array(
									'engine' => array( 'swiper' )
								),
								'type' => 'checkbox'));
		
		$this->show_field(array('name' => 'titles',
								'title' => __('Show titles in the Swiper:', 'trx_addons'),
								'value' => $instance['titles'],
								'options' => trx_addons_get_list_sc_slider_titles(),
								'dependency' => array(
									'engine' => array( 'swiper', 'elastistack' )
								),
								'type' => 'select'));

		$this->show_field(array('name' => 'large',
								'title' => __('Only children of the current category:', 'trx_addons'),
								'value' => (int) $instance['large'],
								'options' => array(
													1 => __('Large', 'trx_addons'),
													0 => __('Small', 'trx_addons')
													),
								'dependency' => array(
									'engine' => array( 'swiper', 'elastistack' )
								),
								'type' => 'radio'));

		do_action('trx_addons_action_slider_widget_before_controller_params', $instance, 'trx_addons_widget_slider', $this);

		// Controller
		$this->show_field(array('name' => 'slider_controler_info',
								'title' => __('Table of contents', 'trx_addons'),
								'type' => 'info'));

		$this->show_field(array('name' => 'controller',
								'title' => __('Show TOC:', 'trx_addons'),
								'value' => (int) $instance['controller'],
								'options' => trx_addons_get_list_show_hide(false, true),
								'dependency' => array(
									'engine' => array( 'swiper' )
								),
								'type' => 'radio'));

		$this->show_field(array('name' => 'controller_style',
								'title' => __('Style of the TOC:', 'trx_addons'),
								'value' => $instance['controller_style'],
								'options' => trx_addons_get_list_sc_slider_toc_styles(),
								'dependency' => array(
									'controller' => array( 1 )
								),
								'type' => 'select'));

		$this->show_field(array('name' => 'controller_pos',
								'title' => __('Position of the TOC:', 'trx_addons'),
								'value' => $instance['controller_pos'],
								'options' => trx_addons_get_list_sc_slider_toc_positions(),
								'dependency' => array(
									'controller' => array( 1 )
								),
								'type' => 'select'));

		$this->show_field(array('name' => 'controller_controls',
								'title' => __('Show arrows:', 'trx_addons'),
								'value' => (int) $instance['controller_controls'],
								'options' => trx_addons_get_list_show_hide(false, true),
								'dependency' => array(
									'controller' => array( 1 )
								),
								'type' => 'radio'));
		$this->show_field(array('name' => 'controller_effect',
								'title' => __('Effect for change items:', 'trx_addons'),
								'value' => $instance['controller_effect'],
								'options' => trx_addons_get_list_sc_slider_effects(),
								'dependency' => array(
									'controller' => array( 1 )
								),
								'type' => 'select'));

		$this->show_field(array('name' => 'controller_per_view',
								'title' => __('Items per view:', 'trx_addons'),
								'value' => $instance['controller_per_view'],
								'dependency' => array(
									'controller' => array( 1 ),
									'controller_effect' => array( 'slide', 'coverflow', 'swap', 'cards', 'creative' )
								),
								'type' => 'text'));

		$this->show_field(array('name' => 'controller_space',
								'title' => __('Space between items:', 'trx_addons'),
								'value' => $instance['controller_space'],
								'dependency' => array(
									'controller' => array( 1 )
								),
								'type' => 'text'));

		$this->show_field(array('name' => 'controller_height',
								'title' => __('Height of the TOC:', 'trx_addons'),
								'value' => $instance['controller_height'],
								'dependency' => array(
									'controller' => array( 1 ),
									'controller_pos' => array( 'bottom' ),
								),
								'type' => 'text'));
		
		do_action('trx_addons_action_after_widget_fields', $instance, 'trx_addons_widget_slider', $this);
	}
}

	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_widget_slider_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_widget_slider_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY);
	function trx_addons_widget_slider_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			// Attention! Slider's script and styles will be loaded always, because it used not only in this widget, but in the many CPT, SC, etc.
			wp_enqueue_style( 'trx_addons-widget_slider', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_WIDGETS . 'slider/slider.css'), array(), null );
			wp_enqueue_script( 'trx_addons-widget_slider', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_WIDGETS . 'slider/slider.js'), array('jquery'), null, true );
		}
	}
}
	
// Merge widget's specific styles into single stylesheet
if ( !function_exists( 'trx_addons_widget_slider_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_widget_slider_merge_styles');
	function trx_addons_widget_slider_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_WIDGETS . 'slider/slider.css' ] = true;
		return $list;
	}
}


// Load responsive styles for the frontend
if ( !function_exists( 'trx_addons_widget_slider_load_responsive_styles' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_widget_slider_load_responsive_styles', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY);
	function trx_addons_widget_slider_load_responsive_styles() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 
				'trx_addons-widget_slider-responsive', 
				trx_addons_get_file_url(TRX_ADDONS_PLUGIN_WIDGETS . 'slider/slider.responsive.css'), 
				array(), 
				null, 
				trx_addons_media_for_load_css_responsive( 'widget-slider', 'lg' ) 
			);
		}
	}
}

// Merge widget's specific styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_widget_slider_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_widget_slider_merge_styles_responsive');
	function trx_addons_widget_slider_merge_styles_responsive($list) {
		$list[ TRX_ADDONS_PLUGIN_WIDGETS . 'slider/slider.responsive.css' ] = true;
		return $list;
	}
}

	
// Merge widget's specific scripts into single file
if ( !function_exists( 'trx_addons_widget_slider_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_widget_slider_merge_scripts');
	function trx_addons_widget_slider_merge_scripts($list) {
		$list[ TRX_ADDONS_PLUGIN_WIDGETS . 'slider/slider.js' ] = true;
		return $list;
	}
}

	
// Add messages for JS
if ( !function_exists( 'trx_addons_widget_slider_localize_script' ) ) {
	add_filter("trx_addons_filter_localize_script", 'trx_addons_widget_slider_localize_script');
	function trx_addons_widget_slider_localize_script($storage) {
		$storage['slider_round_lengths'] = trx_addons_get_setting('slider_round_lengths');
		return $storage;
	}
}


// Add shortcodes
//----------------------------------------------------------------------------
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'slider/slider-sc.php';

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'slider/slider-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'slider/slider-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'slider/slider-sc-vc.php';
}
