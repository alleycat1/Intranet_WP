<?php
/**
 * Widget: WooCommerce Title
 *
 * @package ThemeREX Addons
 * @since v1.90.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists('trx_addons_widget_woocommerce_title_load') ) {
	add_action( 'widgets_init', 'trx_addons_widget_woocommerce_title_load', 21 );
	/**
	 * Register widget "Woocommerce Title"
	 * 
	 * @hooked widgets_init, 21
	 */
	function trx_addons_widget_woocommerce_title_load() {
		if ( ! trx_addons_exists_woocommerce() ) {
			return;
		}
		register_widget( 'trx_addons_widget_woocommerce_title' );
	}
}

if ( ! function_exists( 'trx_addons_get_list_woocommerce_title_parts' ) ) {
	/**
	 * Return list of WooCommerce title parts
	 * 
	 * @trigger trx_addons_filter_get_list_woocommerce_title_parts
	 *
	 * @param boolean $archive  true for archive, false for single product
	 * 
	 * @return array  list of parts
	 */
	function trx_addons_get_list_woocommerce_title_parts( $archive = true ) {
		$list = array(
				'breadcrumbs' => __( 'Breadcrumbs', 'trx_addons' ),
				'title' => __( 'Title', 'trx_addons' )
				);
		if ( $archive ) {
			$list['description'] = __( 'Description', 'trx_addons' );
		}
		return apply_filters( 'trx_addons_filter_get_list_woocommerce_title_parts', $list, $archive );
	}
}

/**
 * Class for the widget "Woocommerce Title"
 */
class trx_addons_widget_woocommerce_title extends TRX_Addons_Widget {

	var $breadcrumbs_showed = false;
	var $title_showed = false;
	var $description_showed = false;

	/**
	 * Constructor
	 */
	function __construct() {
		$widget_ops = array('classname' => 'widget_woocommerce_title', 'description' => esc_html__('Display page title and breadcrumbs', 'trx_addons'));
		parent::__construct( 'trx_addons_widget_woocommerce_title', esc_html__('ThemeREX WooCommerce Title', 'trx_addons'), $widget_ops );

		add_filter( 'woocommerce_show_page_title', array( $this, 'hide_title_if_showed' ) );
		add_filter( 'trx_addons_filter_woocommerce_show_title', array( $this, 'hide_title_if_showed' ) );
		add_filter( 'trx_addons_filter_woocommerce_show_breadcrumbs', array( $this, 'hide_breadcrumbs_if_showed' ) );
		add_filter( 'trx_addons_filter_woocommerce_show_description', array( $this, 'hide_description_if_showed' ) );

	}

	/**
	 * Mark breadcrumbs as showed and remove standard handlers
	 */
	function set_breadcrumbs_showed() {
		$this->breadcrumbs_showed = true;
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_breadcrumb' );
	}

	/**
	 * Hide breadcrumbs if it already showed
	 * 
	 * @param boolean $show  true to show breadcrumbs, false to hide
	 */
	function hide_breadcrumbs_if_showed( $show = true ) {
		return $show && ! $this->breadcrumbs_showed;
	}

	/**
	 * Mark title as showed and remove standard handlers
	 */
	function set_title_showed() {
		$this->title_showed = true;
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
	}

	/**
	 * Hide title on the archive page if it already showed
	 * 
	 * @param boolean $show  true to show title, false to hide
	 */
	function hide_title_if_showed( $show = true ) {
		return $show && ! $this->title_showed;
	}

	/**
	 * Mark description as showed and remove standard handlers
	 */
	function set_description_showed() {
		$this->description_showed = true;
		remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
		remove_action( 'woocommerce_archive_description', 'woocommerce_product_archive_description', 10 );
	}

	/**
	 * Hide description if it already showed
	 * 
	 * @param boolean $show  true to show description, false to hide
	 */
	function hide_description_if_showed( $show = true ) {
		return $show && ! $this->description_showed;
	}

	/**
	 * Show widget
	 * 
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	function widget($args, $instance) {

		$is_archive = is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy();
		$is_single  = is_product();
		$is_other   = is_cart() || is_checkout() || is_account_page();

		// Hide widget if not on the WooCommerce page
		if ( ! apply_filters( 'trx_addons_filter_woocommerce_title', $is_archive || $is_single ) ) {
			return;
		}

		$archive = isset( $instance['archive'] )
					? ( empty( $instance['archive'] ) || ! is_array( $instance['archive'] )
						? array()
						: $instance['archive']
						)
					: array( 'breadcrumbs', 'title', 'description' );
		$single = isset( $instance['single'] )
					? ( empty( $instance['single'] ) || ! is_array( $instance['single'] )
						? array()
						: $instance['single']
						)
					: array( 'breadcrumbs', 'title', 'description' );

		if ( ( $is_archive && count( $archive ) == 0 ) || ( $is_single && count( $single ) == 0 ) ) {
			return;
		}

		trx_addons_get_template_part( TRX_ADDONS_PLUGIN_API . 'woocommerce/tpl.widget.woocommerce_title.php',
									'trx_addons_args_widget_woocommerce_title',
									apply_filters(
										'trx_addons_filter_widget_args',
										array_merge( $args, compact('archive', 'single', 'is_archive', 'is_single', 'is_other'), array('widget' => $this) ),
										$instance,
										'trx_addons_widget_woocommerce_title'
										)
								);
	}

	/**
	 * Update the widget settings.
	 * 
	 * @trigger trx_addons_filter_widget_args_update
	 * 
	 * @param array $new_instance  New instance values.
	 * @param array $instance      Old instance values.
	 */
	function update( $new_instance, $instance ) {
		$instance = array_merge( $instance, $new_instance );
		$instance['archive'] = ! empty( $new_instance['archive'] ) ? $new_instance['archive'] : '';
		$instance['single'] = ! empty( $new_instance['single'] ) ? $new_instance['single'] : '';
		return apply_filters('trx_addons_filter_widget_args_update', $instance, $new_instance, 'trx_addons_widget_woocommerce_title');
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * 
	 * @trigger trx_addons_action_before_widget_fields
	 * @trigger trx_addons_action_after_widget_fields
	 * 
	 * @param array $instance  Widget settings.
	 */
	function form( $instance ) {

		// Set up some default widget settings
		$default = array(
			'archive' => '',
			'single' => '',
		);
		$instance = wp_parse_args( (array) $instance, apply_filters('trx_addons_filter_widget_args_default', $default, 'trx_addons_widget_woocommerce_title') );
		
		do_action( 'trx_addons_action_before_widget_fields', $instance, 'trx_addons_widget_woocommerce_title', $this );

		$this->show_field( array('name' => "archive",
								'title' => __('Products archive', 'trx_addons'),
								'description' => __('Select components to show on the products archive page.', 'trx_addons'),
								'options' => trx_addons_get_list_woocommerce_title_parts(),
								'value' => $instance["archive"],
								'type' => 'checklist'));

		$this->show_field( array('name' => "single",
								'title' => __('Single product', 'trx_addons'),
								'description' => __('Select components to show on the single product page.', 'trx_addons'),
								'options' => trx_addons_get_list_woocommerce_title_parts( false ),
								'value' => $instance["single"],
								'type' => 'checklist') );

		do_action( 'trx_addons_action_after_widget_fields', $instance, 'trx_addons_widget_woocommerce_title', $this );
	}
}


// Add shortcodes
//----------------------------------------------------------------------------

require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'woocommerce/widget.woocommerce_title-sc.php';

// Add shortcodes to Elementor
if ( trx_addons_exists_woocommerce() && trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'woocommerce/widget.woocommerce_title-sc-elementor.php';
}
