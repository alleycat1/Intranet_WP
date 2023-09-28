<?php
/**
 * Plugin support: Calculated Fields Form
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_exists_calculated_fields_form' ) ) {
	/**
	 * Check if plugin 'Calculated Fields Form' is installed and activated
	 * 
	 * @return bool  True if plugin is installed and activated
	 */
	function trx_addons_exists_calculated_fields_form() {
		return class_exists( 'CP_SESSION' ) || class_exists( 'CPCFF_MAIN' );
	}
}

if ( ! function_exists( 'trx_addons_get_list_calculated_fields_form' ) ) {
	/**
	 * Return list of Calculated Fields Form forms from the database
	 * 
	 * @param bool $prepend_inherit  Add inherit item in the beggining of the list
	 * 
	 * @return array  List of forms
	 */
	function trx_addons_get_list_calculated_fields_form( $prepend_inherit = false ) {
		static $list = false;
		if ( $list === false ) {
			$list = array();
			if ( trx_addons_exists_calculated_fields_form() && defined( 'CP_CALCULATEDFIELDSF_FORMS_TABLE' ) ) {
				global $wpdb;
				$rows = $wpdb->get_results( 'SELECT id, form_name FROM ' . esc_sql( $wpdb->prefix . CP_CALCULATEDFIELDSF_FORMS_TABLE ) );
				if ( is_array( $rows ) && count( $rows ) > 0 ) {
					foreach ( $rows as $row ) {
						$list[ $row->id ] = $row->form_name;
					}
				}
			}
		}
		return $prepend_inherit ? trx_addons_array_merge( array( 'inherit' => esc_html__( "Inherit", 'trx_addons' ) ), $list ) : $list;
	}
}

if ( ! function_exists( 'trx_addons_calculated_fields_form_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_calculated_fields_form_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_calculated_fields_form_load_scripts_front', 10, 1 );
	/**
	 * Enqueue styles and scripts for frontend
	 * 
	 * @hooked wp_enqueue_scripts
	 * @hooked trx_addons_action_pagebuilder_preview_scripts
	 * 
	 * @param bool $force  Force load scripts. Default - false
	 */
	function trx_addons_calculated_fields_form_load_scripts_front( $force = false ) {
		if ( ! trx_addons_exists_calculated_fields_form() ) {
			return;
		}
		trx_addons_enqueue_optimized( 'calculated_fields_form', $force, array(
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'CP_CALCULATED_FIELDS' ),
				array( 'type' => 'sc',  'sc' => 'CP_CALCULATED_FIELDS_VAR' ),
				//array( 'type' => 'gb',  'sc' => 'wp:trx-addons/events' ),	// This sc is not exists for GB
				array( 'type' => 'elm', 'sc' => '"widgetType":"calculated-fields-form"' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"calculated-fields-form-variable"' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_calculated_fields_form"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[CP_CALCULATED_FIELDS' ),
			)
		) );
	}
}

if ( ! function_exists( 'trx_addons_calculated_fields_form_check_in_html_output' ) ) {
//	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_calculated_fields_form_check_in_html_output', 10, 1 );
//	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_calculated_fields_form_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_calculated_fields_form_check_in_html_output', 10, 1 );
	/**
	 * Check if the plugin's output is present in the page output HTML
	 * and force loading required styles and scripts
	 *
	 * @param string $content  Page output HTML to check
	 * 
	 * @return string  Checked page output HTML
	 */
	function trx_addons_calculated_fields_form_check_in_html_output( $content = '' ) {
		if ( ! trx_addons_exists_calculated_fields_form() ) {
			return $content;
		}
		$args = array(
			'check' => array(
				'id=[\'"][^\'"]*cp_calculatedfieldsf'
				)
		);
		if ( trx_addons_check_in_html_output( 'calculated_fields_form', $content, $args ) ) {
			trx_addons_calculated_fields_form_load_scripts_front( true );
		}
		return $content;
	}
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_calculated_fields_form() && trx_addons_exists_elementor() && function_exists( 'trx_addons_elm_init' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'calculated-fields-form/calculated-fields-form-sc-elementor.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_calculated_fields_form() && trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'calculated-fields-form/calculated-fields-form-sc-vc.php';
}


// Demo data install
//----------------------------------------------------------------------------

// One-click import support
if ( is_admin() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'calculated-fields-form/calculated-fields-form-demo-importer.php';
}

// OCDI support
if ( is_admin() && trx_addons_exists_calculated_fields_form() && function_exists( 'trx_addons_exists_ocdi' ) && trx_addons_exists_ocdi() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'calculated-fields-form/calculated-fields-form-demo-ocdi.php';
}
