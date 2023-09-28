<?php
/**
 * Widget: Downloads Search (Advanced search form)
 *
 * @package ThemeREX Addons
 * @since v1.6.34
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_widget_edd_search_load' ) ) {
	add_action( 'widgets_init', 'trx_addons_widget_edd_search_load' );
	/**
	 * Register widget "Downloads Search"
	 * 
	 * @hook widgets_init
	 */
	function trx_addons_widget_edd_search_load() {
		register_widget('trx_addons_widget_edd_search');
	}
}

/**
 * Widget class: Downloads Search (Advanced search form). Extends the base class TRX_Addons_Widget
 */
class trx_addons_widget_edd_search extends TRX_Addons_Widget {

	/**
	 * Constructor
	 */
	function __construct() {
		$widget_ops = array( 'classname' => 'widget_edd_search', 'description' => esc_html__( 'Advanced search form for downloads', 'trx_addons' ) );
		parent::__construct( 'trx_addons_widget_edd_search', esc_html__( 'ThemeREX EDD Search', 'trx_addons' ), $widget_ops );
	}

	/**
	 * Show widget layout
	 * 
	 * @trigger widget_title
	 * @trigger trx_addons_filter_widget_args
	 * 
	 * @param array $args	Widget area's arguments
	 * @param array $instance Saved values from database
	 */
	function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );
		
		$type = isset( $instance['type'] ) ? $instance['type'] : 'horizontal';
		$orderby = isset( $instance['orderby'] ) ? $instance['orderby'] : 'date';
		$order = isset( $instance['order'] ) ? $instance['order'] : 'desc';

		trx_addons_get_template_part( TRX_ADDONS_PLUGIN_API . 'easy-digital-downloads/tpl.widget.edd-search.php',
										'trx_addons_args_widget_edd_search',
										apply_filters( 'trx_addons_filter_widget_args',
											array_merge( $args, compact( 'title', 'orderby', 'order', 'type' ) ),
											$instance, 'trx_addons_widget_edd_search' )
									);
	}

	/**
	 * Check and sanitize widget options on before update 
	 * 
	 * @trigger trx_addons_filter_widget_args_update
	 * 
	 * @param array $new_instance  New options
	 * @param array $instance      Old options
	 * 
	 * @return array  		   Sanitized options
	 */
	function update( $new_instance, $instance ) {
		$instance = array_merge( $instance, $new_instance );
		return apply_filters( 'trx_addons_filter_widget_args_update', $instance, $new_instance, 'trx_addons_widget_edd_search' );
	}

	/**
	 * Display widget options form on the widgets panel
	 * 
	 * @trigger trx_addons_filter_widget_args_default
	 * @trigger trx_addons_action_before_widget_fields
	 * @trigger trx_addons_action_after_widget_fields
	 * @trigger trx_addons_action_after_widget_title
	 * 
	 * @param array $instance  Previously saved widget options
	 */
	function form( $instance ) {

		// Set up some default widget settings
		$instance = wp_parse_args( (array)$instance, apply_filters( 'trx_addons_filter_widget_args_default', array(
			'title' => '',
			'orderby' => 'date',
			'order' => 'desc'
		), 'trx_addons_widget_edd_search' ) );
		
		do_action( 'trx_addons_action_before_widget_fields', $instance, 'trx_addons_widget_edd_search', $this );
		
		$this->show_field( array( 'name' => 'title',
								'title' => __('Widget title:', 'trx_addons'),
								'value' => $instance['title'],
								'type' => 'text'
						) );
		
		do_action( 'trx_addons_action_after_widget_title', $instance, 'trx_addons_widget_edd_search', $this );

		$this->show_field( array( 'name' => 'orderby',
								'title' => __('Order search results by:', 'trx_addons'),
								'value' => $instance['orderby'],
								'options' => trx_addons_get_list_sc_query_orderby( '', 'date,price,title,rand' ),
								'type' => 'select'
						) );

		$this->show_field( array( 'name' => 'order',
								'title' => __('Order:', 'trx_addons'),
								'value' => $instance['order'],
								'options' => trx_addons_get_list_sc_query_orders(),
								'type' => 'radio'
						) );
		
		do_action( 'trx_addons_action_after_widget_fields', $instance, 'trx_addons_widget_edd_search', $this );
	}
}

if ( ! function_exists( 'trx_addons_widget_edd_search_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_widget_edd_search_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	/**
	 * Load required styles and scripts for the frontend on search page and EDD pages
	 * 
	 * @hook wp_enqueue_scripts
	 */
	function trx_addons_widget_edd_search_load_scripts_front() {
		// Load animations for font icons
		if ( is_search() || trx_addons_is_edd_page() ) {
			wp_enqueue_style( 'trx_addons-icons-animation', trx_addons_get_file_url('css/font-icons/css/animation.css') );
		}
	}
}


// Add shortcodes
//----------------------------------------------------------------------------

require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'easy-digital-downloads/widget.edd-search-sc.php';

// Add shortcodes to Elementor
if ( trx_addons_exists_edd() && trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'easy-digital-downloads/widget.edd-search-sc-elementor.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_edd() && trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'easy-digital-downloads/widget.edd-search-sc-vc.php';
}
