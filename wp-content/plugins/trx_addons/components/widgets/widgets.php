<?php
/**
 * ThemeREX Widgets
 *
 * @package ThemeREX Addons
 * @since v1.1
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Define list with widgets
if (!function_exists('trx_addons_widgets_setup')) {
	add_action( 'after_setup_theme', 'trx_addons_widgets_setup', 2 );
	function trx_addons_widgets_setup() {
		static $loaded = false;
		if ($loaded) return;
		$loaded = true;
		global $TRX_ADDONS_STORAGE;
		$TRX_ADDONS_STORAGE['widgets_list'] = apply_filters('trx_addons_widgets_list', array(
			'aboutme' => array(
							'title' => __('About Me', 'trx_addons')
						),
			'audio' => array(
							'title' => __('Audio player', 'trx_addons')
						),
			'banner' => array(
							'title' => __('Banner', 'trx_addons')
						),
			'calendar' => array(
							'title' => __('Calendar', 'trx_addons')
						),
			'categories_list' => array(
							'title' => __('Categories list', 'trx_addons'),
							'layouts_sc' => array(
								1 => esc_html__('Style 1'),
								2 => esc_html__('Style 2'),
								3 => esc_html__('Style 3'),
								4 => esc_html__('Style 4'),
							)
						),
			'contacts' => array(
							'title' => __('Contacts', 'trx_addons')
						),
			'custom_links' => array(
							'title' => __('Custom links', 'trx_addons')
						),
			'flickr' => array(
							'title' => __('Flickr', 'trx_addons')
						),
			'instagram' => array(
							'title' => __('Instagram', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons'),
							),
						),
			'popular_posts' => array(
							'title' => __('Popular posts', 'trx_addons')
						),
			'recent_news' => array(
							'title' => __('Recent news', 'trx_addons'),
							'layouts_sc' => array(
								'news-announce'	=> esc_html__('Announcement', 'trx_addons'),
								'news-excerpt'	=> esc_html__('Excerpt', 'trx_addons'),
								'news-magazine'	=> esc_html__('Magazine', 'trx_addons'),
								'news-portfolio'=> esc_html__('Portfolio', 'trx_addons')
							)
						),
			'recent_posts' => array(
							'title' => __('Recent posts', 'trx_addons')
						),
			'slider' => array(
							'title' => __('Slider', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons'),
								'modern' => esc_html__('Modern', 'trx_addons')
							),
							// Always enabled!!!
							'std' => 1,
							'hidden' => false
						),
			'socials' => array(
							'title' => __('Social icons', 'trx_addons')
						),
			'twitter' => array(
							'title' => __('Twitter feed', 'trx_addons'),
							'layouts_sc' => array(
								'list' => esc_html__('List', 'trx_addons'),
								'default' => esc_html__('Default', 'trx_addons')
							)
						),
			'video' => array(
							'title' => __('Video player', 'trx_addons'),
							// Always enabled!!!
							'std' => 1,
							'hidden' => false
						),
			'video_list' => array(
							'title' => __('Video list', 'trx_addons'),
						)
			)
		);
	}
}

// Include files with widgets
if (!function_exists('trx_addons_widgets_load')) {
	add_action( 'after_setup_theme', 'trx_addons_widgets_load', 6 );
	function trx_addons_widgets_load() {
		static $loaded = false;
		if ($loaded) return;
		$loaded = true;
		// Get theme-specific widget's args (if need)
		global $TRX_ADDONS_STORAGE;
		$TRX_ADDONS_STORAGE['widgets_args'] = apply_filters('trx_addons_widgets_args', $TRX_ADDONS_STORAGE['widgets_args']);
		if (is_array($TRX_ADDONS_STORAGE['widgets_list']) && count($TRX_ADDONS_STORAGE['widgets_list']) > 0) {
			foreach ($TRX_ADDONS_STORAGE['widgets_list'] as $w=>$params) {
				if (trx_addons_components_is_allowed('widgets', $w)
					&& ($fdir = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_WIDGETS . "{$w}/{$w}.php")) != '') { 
					include_once $fdir;
					trx_addons_widgets_is_loaded($w, true);
				}
			}
		}
	}
}

// Disable a new Widgets block editor
if (!function_exists('trx_addons_widgets_disable_block_editor')) {
	add_action( 'after_setup_theme', 'trx_addons_widgets_disable_block_editor' );
	function trx_addons_widgets_disable_block_editor() {
		if ( (int) trx_addons_get_option( 'disable_widgets_block_editor' ) > 0 ) {
			remove_theme_support( 'widgets-block-editor' );
		}
	}
}

// Return true if component is loaded
if (!function_exists('trx_addons_widgets_is_loaded')) {
	function trx_addons_widgets_is_loaded($slug, $set=-1) {
		return trx_addons_components_is_loaded('widgets', $slug, $set);
	}
}


// Add 'Widgets' block in the ThemeREX Addons Components
if (!function_exists('trx_addons_widgets_components')) {
	add_filter( 'trx_addons_filter_components_blocks', 'trx_addons_widgets_components');
	function trx_addons_widgets_components($blocks=array()) {
		$blocks['widgets'] = __('Widgets', 'trx_addons');
		return $blocks;
	}
}



/* Widgets utilities
------------------------------------------------------------------------------------- */

// Prepare widgets args - substitute id and class in parameter 'before_widget'
// And add numeric suffix to the same widgets used on page
if (!function_exists('trx_addons_prepare_widgets_args')) {
	function trx_addons_prepare_widgets_args($id, $class, $args=false) {
		global $TRX_ADDONS_STORAGE;
		static $widgets = array();
		$widgets[$id] = ( ! isset( $widgets[$id] ) ? 0 : $widgets[$id] ) + 1;
		$args = $args === false
				? $TRX_ADDONS_STORAGE['widgets_args']
				: trx_addons_array_merge($args, $TRX_ADDONS_STORAGE['widgets_args']);
		if (!empty($args['widget_id'])) {
			$id .= $widgets[$id] > 1 ? sprintf('-%d', $widgets[$args['widget_id']]) : '';
			$args['widget_id'] = $id;
		}
		if (!empty($args['before_widget'])) {
			$args['before_widget'] = str_replace(array('%1$s', '%2$s'), array($id, $class), $args['before_widget']);
		}
		return $args;
	}
}


// Custom Widgets areas
//--------------------------------------------------------------------

// Add Form to register a new custom widgets area
if (!function_exists('trx_addons_widgets_add_form')) {
	add_action('widgets_admin_page', 'trx_addons_widgets_add_form');
	function trx_addons_widgets_add_form() {
		?><div class="trx_addons_widgets_form_wrap">
			<h2 class="trx_addons_widgets_form_title"><?php esc_html_e('Add custom widgets area', 'trx_addons'); ?></h2>
			<form class="trx_addons_widgets_form" method="post">
				<?php wp_nonce_field( 'trx_addons_action_create_widgets_area', 'trx_addons_widgets_wpnonce' ); ?>
				<div class="trx_addons_widgets_area_name">
					<div class="trx_addons_widgets_area_label"><?php esc_html_e('Name (required):', 'trx_addons'); ?></div>
					<div class="trx_addons_widgets_area_field"><input name="trx_addons_widgets_area_name" value="" type="text"></div>
				</div>
				<div class="trx_addons_widgets_area_description">
					<div class="trx_addons_widgets_area_label"><?php esc_html_e('Description:', 'trx_addons'); ?></div>
					<div class="trx_addons_widgets_area_field"><input name="trx_addons_widgets_area_description" value="" type="text"></div>
				</div>
				<div class="trx_addons_widgets_area_submit">
					<div class="trx_addons_widgets_area_field">
						<input value="<?php esc_html_e('Add', 'trx_addons'); ?>" class="trx_addons_widgets_area_button trx_addons_widgets_area_add button-primary" type="submit" title="<?php esc_html_e('To create new widgets area specify it name (required) and description (optional) and press this button', 'trx_addons'); ?>">
						<input value="<?php esc_html_e('Delete', 'trx_addons'); ?>" class="trx_addons_widgets_area_button trx_addons_widgets_area_delete button" name="trx_addons_widgets_area_delete" type="submit" title="<?php esc_html_e('To delete custom widgets area specify it name (required) and press this button', 'trx_addons'); ?>">
					</div>
				</div>
			</form>
		</div><?php
	}
}


// Create/Delete a custom widgets area
if (!function_exists('trx_addons_widgets_create_sidebar')) {
	add_action('widgets_init', 'trx_addons_widgets_create_sidebar', 2);
	function trx_addons_widgets_create_sidebar() {
		if ( ! empty( $_POST['trx_addons_widgets_area_name'] ) ) {
			if ( is_admin() && current_user_can('manage_options')
					&& (
						( trx_addons_get_value_gp( 'trx_addons_widgets_wpnonce' ) != '' && check_admin_referer( 'trx_addons_action_create_widgets_area', 'trx_addons_widgets_wpnonce' ) )
						||
						( trx_addons_get_value_gp( 'trx_addons_widgets_area_nonce' ) != '' && wp_verify_nonce( trx_addons_get_value_gp( 'trx_addons_widgets_area_nonce' ), admin_url( 'admin-ajax.php' ) ) )
					)
			) {
				$name = trim(trx_addons_get_value_gp('trx_addons_widgets_area_name'));
				$sidebars = get_option('trx_addons_widgets_areas', false);
				if ( $sidebars === false ) {
					$sidebars = array();
				}
				if ( ! empty( $_POST['trx_addons_widgets_area_delete'] ) ) {
					foreach ( $sidebars as $i => $sb ) {
						if ( $sidebars[ $i ]['name'] == $name ) {
							unset( $sidebars[ $i ] );
							break;
						}
					}
				} else {
					// Detect next id
					$id = 0;
					foreach ( $sidebars as $sb ) {
						if ( $sb['id'] > $id ) {
							$id = $sb['id'];
						}
					}
					$id++;
					// Add new sidebar
					$sidebars[] = array(
									'id' => $id,
									'name' => $name,
									'description' => trim(trx_addons_get_value_gp('trx_addons_widgets_area_description'))
									);
				}
				update_option('trx_addons_widgets_areas', $sidebars);
			}
		}
	}
}

// Register custom widgets areas after the theme's areas
if (!function_exists('trx_addons_widgets_register_sidebars')) {
	add_action('widgets_init', 'trx_addons_widgets_register_sidebars', 11);
	function trx_addons_widgets_register_sidebars() {
		global $TRX_ADDONS_STORAGE;
		// Load previously created sidebars
		$sidebars = get_option('trx_addons_widgets_areas', false);
		if (is_array($sidebars) && count($sidebars) > 0) {
			foreach ($sidebars as $sb) {
				register_sidebar( apply_filters( 'trx_addons_filter_register_sidebar', array(
										'name'          => $sb['name'],
										'description'   => $sb['description'],
										'id'            => 'custom_widgets_'.intval($sb['id']),
										'before_widget' => $TRX_ADDONS_STORAGE['widgets_args']['before_widget'],
										'after_widget'  => $TRX_ADDONS_STORAGE['widgets_args']['after_widget'],
										'before_title'  => $TRX_ADDONS_STORAGE['widgets_args']['before_title'],
										'after_title'   => $TRX_ADDONS_STORAGE['widgets_args']['after_title']
										) )
								);
			}
		}
	}
}



// Widget class
//--------------------------------------------------------------------

if (!class_exists('TRX_Addons_Widget')) {
	class TRX_Addons_Widget extends WP_Widget {
		function __construct($class, $title, $params) {
			$params = array_merge(
						array(
							'customize_selective_refresh' => true,
							'show_instance_in_rest'       => true,
						),
						$params
			);
			parent::__construct($class, $title, $params);
		}

		// Show one field in the widget's form
		function show_field($params=array()) {
			$params = array_merge(
						array(
							'type' => 'text',		// Field's type
							'name' => '',			// Field's name
							'title' => '',			// Title
							'description' => '',	// Description
							'class' => '',			// Additional classes
							'class_button' => '',	// Additional classes for button in mediamanager
							'multiple' => false,	// Allow select multiple images
							'rows' => 5,			// Number of rows in textarea
							'options' => array(),	// Options for select, checklist, radio, switch
							'params' => array(),	// Additional params for icons, etc.
							'dependency' => array(),// Current field dependencies
							'label' => '',			// Alternative label for checkbox
							'value' => ''			// Field's value
						),
						$params
					);
			?><div class="widget_field_type_<?php echo esc_attr($params['type']);
					if (!empty($params['dir'])) echo ' widget_field_dir_'.esc_attr($params['dir']);
			?>"><?php
				if (!empty($params['title'])) {
					?><label class="widget_field_title"<?php if ($params['type']!='info') echo ' for="'.esc_attr($this->get_field_id($params['name'])).'"'; ?>><?php
						echo wp_kses_data($params['title']);
					?></label><?php
				}
				if (!empty($params['description'])) {
					?><div class="widget_field_description"><?php echo wp_kses($params['description'], 'trx_addons_kses_content'); ?></div>
					<?php
				}
				
				$dependencies = !empty($params['dependency']) ? json_encode($params['dependency']) : '';

				if ($params['type'] == 'select') {
					?><select id="<?php echo esc_attr($this->get_field_id($params['name'])); ?>"
							name="<?php echo esc_attr($this->get_field_name($params['name']).(!empty($params['multiple']) ? '[]' : '')); ?>"
							class="widgets_param_fullwidth<?php
								if (!empty($params['class'])) echo ' '.esc_attr($params['class']);
							?>"
							<?php
							if (!empty($params['multiple'])) {
								?> multiple="multiple"<?php
							}
							if (!empty($params['size']) || !empty($params['multiple'])) {
								?> size="<?php echo esc_attr( !empty($params['size']) ? max(1, $params['size']) : 8); ?>"<?php
							}
							?>
							data-param-name="<?php echo esc_attr($params['name']); ?>"
							data-param-dependency="<?php echo esc_attr($dependencies); ?>"
					><?php
					if (is_array($params['options']) && count($params['options']) > 0) {
						foreach ($params['options'] as $slug => $name) {
							echo '<option value="' . esc_attr($slug) . '"'
										. ( ( is_array($params['value']) ? in_array($slug, $params['value']) : $slug==$params['value'] )
												? ' selected="selected"'
												: ''
											)
								.'>'
									. esc_html($name)
								. '</option>';
						}
					}
					?></select><?php
	
				} else if (in_array($params['type'], array('radio'))) {
					if (is_array($params['options']) && count($params['options']) > 0) {
						?><div class="widgets_param_box<?php
							if (!empty($params['class'])) echo ' class="'.esc_attr($params['class']).'"';
						?>"><?php
						foreach ($params['options'] as $slug => $name) {
							?><label><input type="radio"
										id="<?php echo esc_attr($this->get_field_id($params['name']).'_'.$slug); ?>"
										name="<?php echo esc_attr($this->get_field_name($params['name'])); ?>"
										data-param-name="<?php echo esc_attr($params['name']); ?>"
										data-param-dependency="<?php echo esc_attr($dependencies); ?>"
										value="<?php echo esc_attr($slug); ?>"
										<?php if ($params['value']==$slug) echo ' checked="checked"'; ?> />
							<?php echo esc_html($name); ?></label> <?php
						}
						?></div><?php
					}

				} else if ($params['type'] == 'checkbox') {
					?><label<?php if (!empty($params['class'])) echo ' class="'.esc_attr($params['class']).'"'; ?>><?php
						?><input type="checkbox" id="<?php echo esc_attr($this->get_field_id($params['name'])); ?>" 
									name="<?php echo esc_attr($this->get_field_name($params['name'])); ?>" 
									data-param-name="<?php echo esc_attr($params['name']); ?>"
									data-param-dependency="<?php echo esc_attr($dependencies); ?>"
									value="1" <?php echo (1==$params['value'] ? ' checked="checked"' : ''); ?> /><?php
							echo esc_html(!empty($params['label']) ? $params['label'] : $params['title']);
					?></label><?php

				} else if ($params['type'] == 'checklist') {
					?><span class="widgets_param_box<?php
									if (!empty($params['class'])) echo ' '.esc_attr($params['class']);
									?>"
							data-field_name="<?php echo esc_attr($this->get_field_name($params['name'])); ?>[]">
						<?php 
						foreach ($params['options'] as $slug => $name) {
							?><label><input type="checkbox"
										value="<?php echo esc_attr($slug); ?>" 
										name="<?php echo esc_attr($this->get_field_name($params['name'])); ?>[]"
										data-param-name="<?php echo esc_attr($params['name']); ?>"
										data-param-dependency="<?php echo esc_attr($dependencies); ?>"
										<?php
										if ( strpos( ',' . ( is_array($params['value']) ? join(',', $params['value']) : $params['value'] ) . ',', ','.$slug.',' ) !== false) echo ' checked="checked"';
										?>><?php
								echo wp_kses( $name, 'trx_addons_kses_content' );
							?></label><?php
						}
					?></span><?php
	
				} else if ($params['type'] == 'color') {
					?><input type="text"
							id="<?php echo esc_attr($this->get_field_id($params['name'])); ?>" 
							name="<?php echo esc_attr($this->get_field_name($params['name'])); ?>"
							data-param-name="<?php echo esc_attr($params['name']); ?>"
							data-param-dependency="<?php echo esc_attr($dependencies); ?>"
							value="<?php echo esc_attr($params['value']); ?>"
							class="trx_addons_color_selector<?php if (!empty($params['class'])) echo ' '.esc_attr($params['class']); ?>" /><?php
	
				} else if (in_array($params['type'], array('image', 'media', 'video', 'audio'))) {
					?><input type="hidden"
							id="<?php echo esc_attr($this->get_field_id($params['name'])); ?>" 
							name="<?php echo esc_attr($this->get_field_name($params['name'])); ?>"
							data-param-name="<?php echo esc_attr($params['name']); ?>"
							data-param-dependency="<?php echo esc_attr($dependencies); ?>"
							<?php if (!empty($params['class'])) echo ' class="'.esc_attr($params['class']).'"'; ?>
							value="<?php echo esc_attr($params['value']); ?>" /><?php
					trx_addons_show_layout(trx_addons_options_show_custom_field($this->get_field_id($params['name']).'_button', 
									array(
										'type' => 'mediamanager',
										'multiple' => !empty($params['multiple']),
										'data_type' => $params['type'],
										'class_field' => !empty($params['class_button']) ? ' '.esc_attr($params['class_button']) : '',
										'linked_field_id' => $this->get_field_id($params['name'])
										),
									$params['value']));
	
				} else if ($params['type'] == 'icons') {
					?><input type="hidden"
							id="<?php echo esc_attr($this->get_field_id($params['name'])); ?>" 
							name="<?php echo esc_attr($this->get_field_name($params['name'])); ?>"
							data-param-name="<?php echo esc_attr($params['name']); ?>"
							data-param-dependency="<?php echo esc_attr($dependencies); ?>"
							value="<?php echo esc_attr($params['value']); ?>" /><?php
					trx_addons_show_layout(trx_addons_options_show_custom_field('trx_addons_options_field_'.esc_attr($this->get_field_id($params['name'])), 
									array_merge($params, $params['params']),
									$params['value']));
	
	
				} else if ($params['type'] == 'textarea') {
					?><textarea id="<?php echo esc_attr($this->get_field_id($params['name'])); ?>" 
							name="<?php echo esc_attr($this->get_field_name($params['name'])); ?>"
							data-param-name="<?php echo esc_attr($params['name']); ?>"
							data-param-dependency="<?php echo esc_attr($dependencies); ?>"
							rows="<?php echo esc_attr($params['rows']); ?>"
							class="widgets_param_fullwidth<?php if (!empty($params['class'])) echo ' '.esc_attr($params['class']); ?>"><?php
								echo esc_html($params['value']);
					?></textarea><?php
	
				} else if ($params['type'] == 'text') {
					?><input type="text"
							id="<?php echo esc_attr($this->get_field_id($params['name'])); ?>" 
							name="<?php echo esc_attr($this->get_field_name($params['name'])); ?>"
							data-param-name="<?php echo esc_attr($params['name']); ?>"
							data-param-dependency="<?php echo esc_attr($dependencies); ?>"
							value="<?php echo esc_attr($params['value']); ?>"
							class="widgets_param_fullwidth<?php if (!empty($params['class'])) echo ' '.esc_attr($params['class']); ?>" /><?php
				}
				?>
			</div><?php
		}


		// Display widget's common params
		//---------------------------------------------------------
		
		// Show ID, Class
		function show_fields_id_param($instance, $group=false) {
			if ($group===false)
				$group = __('ID &amp; Class', 'trx_addons');
			if (!empty($group))
				$this->show_field(array('title' => $group,
										'type' => 'info'));
			
			$this->show_field(array('name' => 'id',
									'title' => __('Element ID:', 'trx_addons'),
									'value' => $instance['id'],
									'type' => 'text'));

			$this->show_field(array('name' => 'class',
									'title' => __('Element CSS class:', 'trx_addons'),
									'value' => $instance['class'],
									'type' => 'text'));
		}
		
		// Show slider params
		function show_fields_slider_param($instance, $group=false, $add_params=array()) {
			if ($group===false) {
				$group = __('Slider', 'trx_addons');
			}
			if (!empty($group)) {
				$this->show_field(array('title' => $group,
										'type' => 'info'));
			}
			
			$this->show_field(array('name' => 'slider',
									'title' => '',
									'label' => __('Slider', 'trx_addons'),
									'value' => (int) $instance['slider'],
									'type' => 'checkbox'));

			$this->show_field(array('name' => 'slider_effect',
									'title' => __('Slider effect:', 'trx_addons'),
									'value' => ! empty( $instance['slider_effect'] ) ? $instance['slider_effect'] : 'slide',
									'options' => trx_addons_get_list_sc_slider_effects(),
									'dependency' => array(
										'slider' => array( '1' )
									),
									'type' => 'select'));

			$this->show_field(array('name' => 'slides_space',
									'title' => __('Space between slides:', 'trx_addons'),
									'value' => (int) $instance['slides_space'],
									'dependency' => array(
										'slider' => array( '1' ),
									),
									'type' => 'text'));

			$this->show_field(array('name' => 'slider_controls',
									'title' => __('Slider controls:', 'trx_addons'),
									'value' => $instance['slider_controls'],
									'options' => trx_addons_get_list_sc_slider_controls(),
									'dependency' => array(
										'slider' => array( '1' ),
									),
									'type' => 'radio'));

			$this->show_field(array('name' => 'slider_pagination',
									'title' => __('Slider pagination:', 'trx_addons'),
									'value' => $instance['slider_pagination'],
									'options' => trx_addons_get_list_sc_slider_paginations(),
									'dependency' => array(
										'slider' => array( '1' ),
									),
									'type' => 'radio'));
			
			$this->show_field(array('name' => 'slides_centered',
									'title' => '',
									'label' => __('Slides centered', 'trx_addons'),
									'value' => empty( $instance['slides_centered'] ) ? 0 : (int) $instance['slides_centered'],
									'dependency' => array(
										'slider' => array( '1' ),
									),
									'type' => 'checkbox'));
			
			$this->show_field(array('name' => 'slides_overflow',
									'title' => '',
									'label' => __('Slides overflow visible', 'trx_addons'),
									'value' => empty( $instance['slides_overflow'] ) ? 0 : (int) $instance['slides_overflow'],
									'dependency' => array(
										'slider' => array( '1' ),
									),
									'type' => 'checkbox'));
			
			$this->show_field(array('name' => 'slider_mouse_wheel',
									'title' => '',
									'label' => __('Enable mouse wheel', 'trx_addons'),
									'value' => empty( $instance['slider_mouse_wheel'] ) ? 0 : (int) $instance['slider_mouse_wheel'],
									'dependency' => array(
										'slider' => array( '1' ),
									),
									'type' => 'checkbox'));
			
			$this->show_field(array('name' => 'slider_autoplay',
									'title' => '',
									'label' => __('Enable autoplay', 'trx_addons'),
									'value' => empty( $instance['slider_autoplay'] ) ? 0 : (int) $instance['slider_autoplay'],
									'dependency' => array(
										'slider' => array( '1' ),
									),
									'type' => 'checkbox'));

			$this->show_field(array('name' => 'slider_loop',
									'title' => '',
									'label' => __('Enable loop_mode', 'trx_addons'),
									'value' => empty( $instance['slider_loop'] ) ? 0 : (int) $instance['slider_loop'],
									'dependency' => array(
										'slider' => array( '1' )
									),
									'type' => 'checkbox'));

			$this->show_field(array('name' => 'slider_free_mode',
									'title' => '',
									'label' => __('Enable free mode', 'trx_addons'),
									'value' => empty( $instance['slider_free_mode'] ) ? 0 : (int) $instance['slider_free_mode'],
									'dependency' => array(
										'slider' => array( '1' ),
									),
									'type' => 'checkbox'));

			// Additional params
			if (is_array($add_params) && count($add_params) > 0) {
				foreach ($add_params as $v)
					$this->show_field($v);
			}
		}
		
		// Show title params
		function show_fields_title_param($instance, $group=false, $button=true) {
			if ($group===false)
				$group = __('Titles', 'trx_addons');
			if (!empty($group))
				$this->show_field(array('title' => $group,
										'type' => 'info'));
			
			$this->show_field(array('name' => 'title_style',
									'title' => __('Title style:', 'trx_addons'),
									'value' => $instance['title_style'],
									'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'title'), 'trx_sc_title' ),
									'type' => 'radio'));

			$this->show_field(array('name' => 'title_tag',
									'title' => __('Title tag:', 'trx_addons'),
									'value' => $instance['title_tag'],
									'options' => trx_addons_get_list_sc_title_tags(),
									'type' => 'select'));

			$this->show_field(array('name' => 'title_align',
									'title' => __('Title alignment:', 'trx_addons'),
									'value' => $instance['title_align'],
									'options' => trx_addons_get_list_sc_aligns(),
									'type' => 'radio'));

			$this->show_field(array('name' => 'title_color',
									'title' => __('Title color:', 'trx_addons'),
									'value' => $instance['title_color'],
									'type' => 'color'));

			$this->show_field(array('name' => 'title_color2',
									'title' => __('Title color 2:', 'trx_addons'),
									'value' => $instance['title_color2'],
									'dependency' => array(
										'title_tag' => array( 'gradient' ),
									),
									'type' => 'color'));

			$this->show_field(array('name' => 'gradient_fill',
									'title' => __('Gradient fill:', 'trx_addons'),
									'options' => trx_addons_get_list_sc_title_gradient_fills(),
									'value' => $instance['gradient_fill'],
									'type' => 'select'));

			$this->show_field(array('name' => 'gradient_direction',
									'title' => __('Gradient direction (0-360):', 'trx_addons'),
									'value' => $instance['gradient_direction'],
									'dependency' => array(
										'title_tag' => array( 'gradient' ),
									),
									'type' => 'text'));

			$this->show_field(array('name' => 'title',
									'title' => __('Title:', 'trx_addons'),
									'value' => $instance['title'],
									'type' => 'text'));

			$this->show_field(array('name' => 'subtitle',
									'title' => __('Subtitle:', 'trx_addons'),
									'value' => $instance['subtitle'],
									'type' => 'text'));

			$this->show_field(array('name' => 'subtitle_align',
									'title' => __('Subtitle align:', 'trx_addons'),
									'options' => trx_addons_get_list_sc_aligns(),
									'value' => $instance['subtitle_align'],
									'type' => 'select'));

			$this->show_field(array('name' => 'subtitle_position',
									'title' => __('Subtitle position:', 'trx_addons'),
									'options' => trx_addons_get_list_sc_subtitle_positions(),
									'value' => $instance['subtitle_position'],
									'type' => 'select'));

			$this->show_field(array('name' => 'description',
									'title' => __('Description:', 'trx_addons'),
									'value' => $instance['description'],
									'type' => 'textarea'));
			
			// Add button's params
			if ($button) {
				$this->show_field(array('name' => 'link',
										'title' => __('Button URL:', 'trx_addons'),
										'value' => $instance['link'],
										'type' => 'text'));
				$this->show_field(array('name' => 'link_text',
										'title' => __('Button text:', 'trx_addons'),
										'value' => $instance['link_text'],
										'type' => 'text'));
				$this->show_field(array('name' => 'link_style',
										'title' => __('Button style:', 'trx_addons'),
										'value' => $instance['link_style'],
										'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'button'), 'trx_sc_button'),
										'type' => 'select'));
				$this->show_field(array('name' => 'link_image',
										'title' => __('Background image for the button:', 'trx_addons'),
										'value' => $instance['link_image'],
										'type' => 'image'));
			}
		}
		
		// Show query params
		function show_fields_query_param($instance, $group=false, $add_params=array()) {
			if ($group===false) {
				$group = __('Query', 'trx_addons');
			}
			if (!empty($group)) {
				$this->show_field(array('title' => $group,
										'type' => 'info'));
			}

			if ( ! isset( $add_params['ids'] ) || ! empty( $add_params['ids'] ) ) {
				$this->show_field(array('name' => 'ids',
										'title' => __('IDs to show (comma-separated list):', 'trx_addons'),
										'value' => $instance['ids'],
										'type' => 'text'));
			}
			if ( ! isset( $add_params['count'] ) || ! empty( $add_params['count'] ) ) {
				$this->show_field(array('name' => 'count',
										'title' => __('Count:', 'trx_addons'),
										'value' => (int) $instance['count'],
										'dependency' => array(
											'ids' => array( 'is_empty' ),
										),
										'type' => 'text'));
			}
			if ( ! isset( $add_params['columns'] ) || ! empty( $add_params['columns'] ) ) {
				$this->show_field(array('name' => 'columns',
										'title' => __('Columns:', 'trx_addons'),
										'value' => (int) $instance['columns'],
										'type' => 'text'));
			}
			if ( ! isset( $add_params['offset'] ) || ! empty( $add_params['offset'] ) ) {
				$this->show_field(array('name' => 'offset',
										'title' => __('Offset:', 'trx_addons'),
										'value' => (int) $instance['offset'],
										'dependency' => array(
											'ids' => array( 'is_empty' ),
										),
										'type' => 'text'));
			}
			if ( ! isset( $add_params['orderby'] ) || ! empty( $add_params['orderby'] ) ) {
				$this->show_field(array('name' => 'orderby',
										'title' => __('Order by:', 'trx_addons'),
										'value' => $instance['orderby'],
										'options' => trx_addons_get_list_sc_query_orderby('', 'date,price,title'),
										'type' => 'select'));
			}
			if ( ! isset( $add_params['order'] ) || ! empty( $add_params['order'] ) ) {
				$this->show_field(array('name' => 'order',
										'title' => __('Order:', 'trx_addons'),
										'value' => $instance['order'],
										'options' => trx_addons_get_list_sc_query_orders(),
										'type' => 'radio'));
			}

			// Additional params
			if (is_array($add_params) && count($add_params) > 0) {
				foreach ($add_params as $v) {
					if ( ! empty($v) ) {
						$this->show_field($v);
					}
				}
			}
		}
		
		// Show icon params
		function show_fields_icon_param($instance, $group=false, $only_socials=false) {
			if ($group===false)
				$group = __('Icons', 'trx_addons');
			if (!empty($group))
				$this->show_field(array('title' => $group,
										'type' => 'info'));

			// Internal popup with icons list
			$style = $only_socials ? trx_addons_get_setting('socials_type') : trx_addons_get_setting('icons_type');
	
			$this->show_field(array('name' => 'icon',
									'title' => __('Icon:', 'trx_addons'),
									'value' => $instance['icon'],
									'style' => $style,
									'options' => trx_addons_get_list_icons($style),
									'type' => 'icons'));
		}
	}
}
