<?php
/**
 * Widget: Display Contacts info
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Load widget
if (!function_exists('trx_addons_widget_contacts_load')) {
	add_action( 'widgets_init', 'trx_addons_widget_contacts_load' );
	function trx_addons_widget_contacts_load() {
		register_widget('trx_addons_widget_contacts');
	}
}

// Widget Class
class trx_addons_widget_contacts extends TRX_Addons_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_contacts', 'description' => esc_html__('Contacts - logo and short description, address, phone and email', 'trx_addons'));
		parent::__construct( 'trx_addons_widget_contacts', esc_html__('ThemeREX Contacts', 'trx_addons'), $widget_ops );
	}

	// Show widget
	function widget($args, $instance) {

		$title = apply_filters( 'widget_title', isset($instance['title']) ? $instance['title'] : '', $instance, $this->id_base );
	
		$logo = isset($instance['logo']) ? $instance['logo'] : '';
		$logo_retina = isset($instance['logo_retina']) ? $instance['logo_retina'] : '';
		// Uncomment next section (remove false from the condition)
		// if you want to get logo from current theme (if parameter 'logo' is empty)
		if (false && empty($logo)) {
			$logo = apply_filters('trx_addons_filter_theme_logo', '');
			if (is_array($logo)) {
				$logo = !empty($logo['logo']) ? $logo['logo'] : '';
				$logo_retina = !empty($logo['logo_retina']) ? $logo['logo_retina'] : $logo_retina;
			}
		}
		if (!empty($logo)) {
			$logo = trx_addons_get_attachment_url($logo, 'full');
		}
		if (!empty($logo_retina)) {
			$logo_retina = trx_addons_get_attachment_url($logo_retina, 'full');
		}
		if (empty($logo) && !empty($logo_retina)) {
			$logo = $logo_retina;
		}
		if (!empty($logo)) {
			$attr = trx_addons_getimagesize($logo);
			$logo = '<img src="'.esc_url($logo).'"'
						. (!empty($logo_retina) ? ' srcset="' . esc_url( $logo_retina) . ' 2x"' : '' )
						. ' alt="' . esc_attr__("Contact's logo", 'trx_addons') . '"'
						. (!empty($attr[3]) ? ' '.trim($attr[3]) : '')
						. '>';
		}

		$description = isset($instance['description']) ? $instance['description'] : '';
		$content = isset($instance['content']) ? $instance['content'] : '';

		$address = isset($instance['address']) ? $instance['address'] : '';
		$phone = isset($instance['phone']) ? $instance['phone'] : '';
		$email = isset($instance['email']) ? $instance['email'] : '';
		$columns = isset($instance['columns']) ? (int) $instance['columns'] : 0;
		$socials = isset($instance['socials']) ? (int) $instance['socials'] : 0;
		$map = isset($instance['map']) ? (int) $instance['map'] : 0;
		$map_height = !empty($instance['map_height']) ? $instance['map_height'] : 130;
		$map_position = isset($instance['map_position']) ? $instance['map_position'] : 'top';


		// Load widget-specific scripts and styles
		trx_addons_widget_contacts_load_scripts_front( true );

		// Load template
		trx_addons_get_template_part(TRX_ADDONS_PLUGIN_WIDGETS . 'contacts/tpl.default.php',
									'trx_addons_args_widget_contacts', 
									apply_filters('trx_addons_filter_widget_args',
												array_merge($args, compact('title', 'logo', 'logo_retina', 'description', 'content',
																			'email', 'columns', 'address', 'phone', 'socials',
																			'map', 'map_height', 'map_position')),
												$instance, 'trx_addons_widget_contacts')
									);
	}

	// Update the widget settings.
	function update($new_instance, $instance) {
		$instance = array_merge($instance, $new_instance);
		$instance['description'] = wp_kses_data($new_instance['description']);
		$instance['address'] = wp_kses_data($new_instance['address']);
		$instance['phone'] = wp_kses_data($new_instance['phone']);
		$instance['email'] = wp_kses_data($new_instance['email']);
		$instance['columns'] = isset( $new_instance['columns'] ) && (int)$new_instance['columns'] > 0 ? 1 : 0;
		$instance['socials'] = isset( $new_instance['socials'] ) && (int)$new_instance['socials'] > 0 ? 1 : 0;
		$instance['map'] = isset( $new_instance['map'] ) && (int)$new_instance['map'] > 0 ? 1 : 0;
		$instance['map_height'] = isset( $new_instance['map_height'] ) ? strip_tags( $new_instance['map_height'] ) : 140;
		$instance['map_position'] = isset( $new_instance['map_position'] ) ? strip_tags( $new_instance['map_position'] ) : 'top';
		return apply_filters('trx_addons_filter_widget_args_update', $instance, $new_instance, 'trx_addons_widget_contacts');
	}

	// Displays the widget settings controls on the widget panel.
	function form($instance) {

		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, apply_filters('trx_addons_filter_widget_args_default', array(
			'title' => '',
			'logo' => '',
			'logo_retina' => '',
			'description' => '',
			'address' => '',
			'phone' => '',
			'email' => '',
			'columns' => 0,
			'socials' => 0,
			'map' => 0,
			'map_height' => 140,
			'map_position' => 'top',
			), 'trx_addons_widget_contacts')
		);
		
		do_action('trx_addons_action_before_widget_fields', $instance, 'trx_addons_widget_contacts', $this);
		
		$this->show_field(array('name' => 'title',
								'title' => __('Widget title:', 'trx_addons'),
								'value' => $instance['title'],
								'type' => 'text'));
		
		do_action('trx_addons_action_after_widget_title', $instance, 'trx_addons_widget_contacts', $this);

		$this->show_field(array('name' => 'logo',
								'title' => __('Logo:', 'trx_addons'),
								'value' => $instance['logo'],
								'type' => 'image'));

		$this->show_field(array('name' => 'logo_retina',
								'title' => __('Logo for Retina:', 'trx_addons'),
								'value' => $instance['logo_retina'],
								'type' => 'image'));

		$this->show_field(array('name' => 'description',
								'title' => __('Short description about user:', 'trx_addons'),
								'value' => $instance['description'],
								'type' => 'textarea'));

		$this->show_field(array('name' => 'address',
								'title' => __('Address:', 'trx_addons'),
								'value' => $instance['address'],
								'type' => 'text'));

		$this->show_field(array('name' => 'phone',
								'title' => __('Phone:', 'trx_addons'),
								'value' => $instance['phone'],
								'type' => 'text'));

		$this->show_field(array('name' => 'email',
								'title' => __('E-mail:', 'trx_addons'),
								'value' => $instance['email'],
								'type' => 'text'));

		$this->show_field(array('name' => 'columns',
								'title' => '',
								'label' => __('Break into columns', 'trx_addons'),
								'value' => (int) $instance['columns'],
								'type' => 'checkbox'));

		$this->show_field(array('name' => 'socials',
								'title' => '',
								'label' => __('Show Social icons', 'trx_addons'),
								'value' => (int) $instance['socials'],
								'type' => 'checkbox'));

		$this->show_field(array('name' => 'map',
								'title' => '',
								'label' => __('Show map', 'trx_addons'),
								'value' => (int) $instance['map'],
								'type' => 'checkbox'));

		$this->show_field(array('name' => 'map_height',
								'title' => __('Map height:', 'trx_addons'),
								'value' => $instance['map_height'],
								'dependency' => array(
									'map' => array( 1 ),
								),
								'type' => 'text'));
		
		$this->show_field(array('name' => 'map_position',
								'title' => __('Map position:', 'trx_addons'),
								'value' => $instance['map_position'],
								'options' => array(
													'top' => esc_html__('Top', 'trx_addons'),
													'left' => esc_html__('Left', 'trx_addons'),
													'right' => esc_html__('Right', 'trx_addons')
													),
								'dependency' => array(
									'map' => array( 1 ),
								),
								'type' => 'radio'));
		
		do_action('trx_addons_action_after_widget_fields', $instance, 'trx_addons_widget_contacts', $this);
	}
}


// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_widget_contacts_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_widget_contacts_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY);
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_widget_contacts_load_scripts_front', 10, 1 );
	function trx_addons_widget_contacts_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'widget_contacts', $force, array(
			'css'  => array(
				'trx_addons-widget_contacts' => array( 'src' => TRX_ADDONS_PLUGIN_WIDGETS . 'contacts/contacts.css' ),
			),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_widget_contacts' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/contacts' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_widget_contacts"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_widget_contacts' ),
			)
		) );
	}
}
	
// Merge widget specific styles into single stylesheet
if ( !function_exists( 'trx_addons_widget_contacts_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_widget_contacts_merge_styles');
	function trx_addons_widget_contacts_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_WIDGETS . 'contacts/contacts.css' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu
if ( !function_exists( 'trx_addons_widget_contacts_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_widget_contacts_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_widget_contacts_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_widget_contacts_check_in_html_output', 10, 1 );
	function trx_addons_widget_contacts_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*widget_contacts'
			)
		);
		if ( trx_addons_check_in_html_output( 'widget_contacts', $content, $args ) ) {
			trx_addons_widget_contacts_load_scripts_front( true );
		}
		return $content;
	}
}


// Add shortcodes
//----------------------------------------------------------------------------
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'contacts/contacts-sc.php';

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'contacts/contacts-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'contacts/contacts-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'contacts/contacts-sc-vc.php';
}
