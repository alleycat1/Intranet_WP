<?php
/**
 * Plugin support: Calculated Fields Form (Importer support)
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_calculated_fields_form_importer_required_plugins' ) ) {
	add_filter( 'trx_addons_filter_importer_required_plugins',	'trx_addons_calculated_fields_form_importer_required_plugins', 10, 2 );
	/**
	 * Check if the required plugin is installed
	 * 
	 * @hooked trx_addons_filter_importer_required_plugins
	 *
	 * @param string $not_installed Not installed plugins list
	 * @param string $list          Required plugins list
	 * 
	 * @return string               Not installed plugins list
	 */
	function trx_addons_calculated_fields_form_importer_required_plugins( $not_installed = '', $list = '' ) {
		if ( strpos( $list, 'calculated-fields-form' ) !== false && ! trx_addons_exists_calculated_fields_form() ) {
			$not_installed .= '<br>' . esc_html__( 'Calculated Fields Form', 'trx_addons' );
		}
		return $not_installed;
	}
}

if ( ! function_exists( 'trx_addons_calculated_fields_form_importer_set_options' ) ) {
	add_filter( 'trx_addons_filter_importer_options', 'trx_addons_calculated_fields_form_importer_set_options', 10, 1 );
	/**
	 * Set plugin's specific importer options and add 'file_with_calculated-fields-form' to the list of export files
	 * 
	 * @hooked trx_addons_filter_importer_options
	 *
	 * @param array $options Importer options
	 * 
	 * @return array         Modified options
	 */
	function trx_addons_calculated_fields_form_importer_set_options( $options = array() ) {
		if ( trx_addons_exists_calculated_fields_form() && in_array( 'calculated-fields-form', $options['required_plugins'] ) ) {
			$options['additional_options'][] = 'CP_CFF_LOAD_SCRIPTS';				// Add slugs to export options of this plugin
			$options['additional_options'][] = 'CP_CALCULATEDFIELDSF_USE_CACHE';
			$options['additional_options'][] = 'CP_CALCULATEDFIELDSF_EXCLUDE_CRAWLERS';
			if ( is_array( $options['files'] ) && count( $options['files'] ) > 0 ) {
				foreach ( $options['files'] as $k => $v ) {
					$options['files'][$k]['file_with_calculated-fields-form'] = str_replace( 'name.ext', 'calculated-fields-form.txt', $v['file_with_'] );
				}
			}
		}
		return $options;
	}
}

if ( ! function_exists( 'trx_addons_calculated_fields_form_importer_check_options' ) ) {
	add_filter( 'trx_addons_filter_import_theme_options', 'trx_addons_calculated_fields_form_importer_check_options', 10, 4 );
	/**
	 * Prevent to import plugin's specific options if a plugin is not installed
	 * 
	 * @hooked trx_addons_filter_import_theme_options
	 *
	 * @param string $allow   Allow import or not
	 * @param string $k       Option name
	 * @param string $v       Option value. Not used
	 * @param array  $options Importer options
	 * 
	 * @return string         Allow import or not
	 */
	function trx_addons_calculated_fields_form_importer_check_options( $allow, $k, $v, $options ) {
		if ( $allow && in_array( $k, array( 'CP_CFF_LOAD_SCRIPTS', 'CP_CALCULATEDFIELDSF_USE_CACHE', 'CP_CALCULATEDFIELDSF_EXCLUDE_CRAWLERS' ) ) ) {
			$allow = trx_addons_exists_calculated_fields_form() && in_array( 'calculated-fields-form', $options['required_plugins'] );
		}
		return $allow;
	}
}

if ( ! function_exists( 'trx_addons_calculated_fields_form_importer_show_params' ) ) {
	add_action( 'trx_addons_action_importer_params', 'trx_addons_calculated_fields_form_importer_show_params', 10, 1 );
	/**
	 * Show a plugin name in the checklist with required plugins to import data
	 * 
	 * @hooked trx_addons_action_importer_params
	 *
	 * @param array $importer Importer options
	 */
	function trx_addons_calculated_fields_form_importer_show_params( $importer ) {
		if ( trx_addons_exists_calculated_fields_form() && in_array( 'calculated-fields-form', $importer->options['required_plugins'] ) ) {
			$importer->show_importer_params(array(
				'slug' => 'calculated-fields-form',
				'title' => esc_html__('Import Calculated Fields Form', 'trx_addons'),
				'part' => 1
			));
		}
	}
}

if ( ! function_exists( 'trx_addons_calculated_fields_form_importer_import' ) ) {
	add_action( 'trx_addons_action_importer_import', 'trx_addons_calculated_fields_form_importer_import', 10, 2 );
	/**
	 * Import Calculated Fields Forms data from the file if action is 'import_calculated-fields-form'
	 * 
	 * @hooked trx_addons_action_importer_import
	 *
	 * @param string $importer Importer object
	 * @param string $action   Action to perform
	 */
	function trx_addons_calculated_fields_form_importer_import( $importer, $action ) {
		if ( trx_addons_exists_calculated_fields_form() && in_array( 'calculated-fields-form', $importer->options['required_plugins'] ) ) {
			if ( $action == 'import_calculated-fields-form' ) {
				$importer->response['start_from_id'] = 0;
				$importer->import_dump( 'calculated-fields-form', esc_html__( 'Calculated Fields Form', 'trx_addons' ) );
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_calculated_fields_form_importer_import_fields' ) ) {
	add_action( 'trx_addons_action_importer_import_fields',	'trx_addons_calculated_fields_form_importer_import_fields', 10, 1 );
	/**
	 * Display a plugin name in the import progress area
	 * 
	 * @hooked trx_addons_action_importer_import_fields
	 *
	 * @param string $importer Importer object
	 */
	function trx_addons_calculated_fields_form_importer_import_fields($importer) {
		if ( trx_addons_exists_calculated_fields_form() && in_array( 'calculated-fields-form', $importer->options['required_plugins'] ) ) {
			$importer->show_importer_fields( array(
				'slug'	=> 'calculated-fields-form', 
				'title'	=> esc_html__('Calculated Fields Form', 'trx_addons')
			) );
		}
	}
}

if ( ! function_exists( 'trx_addons_calculated_fields_form_importer_export' ) ) {
	add_action( 'trx_addons_action_importer_export', 'trx_addons_calculated_fields_form_importer_export', 10, 1 );
	/**
	 * Export Calculated Fields Forms to the data file
	 * 
	 * @hooked trx_addons_action_importer_export
	 *
	 * @param string $importer Importer object
	 */
	function trx_addons_calculated_fields_form_importer_export( $importer ) {
		if ( trx_addons_exists_calculated_fields_form()
			&& defined( 'CP_CALCULATEDFIELDSF_FORMS_TABLE' )
			&& in_array( 'calculated-fields-form', $importer->options['required_plugins'] )
		) {
			trx_addons_fpc($importer->export_file_dir('calculated-fields-form.txt'), serialize( apply_filters( 'trx_addons_filter_importer_export_tables', array(
				CP_CALCULATEDFIELDSF_FORMS_TABLE => $importer->export_dump( CP_CALCULATEDFIELDSF_FORMS_TABLE )
			), 'calculated_fields_form' ) )	);
		}
	}
}

if ( ! function_exists( 'trx_addons_calculated_fields_form_importer_export_fields' ) ) {
	add_action( 'trx_addons_action_importer_export_fields',	'trx_addons_calculated_fields_form_importer_export_fields', 10, 1 );
	/**
	 * Display a plugin name in the list with exported files
	 * 
	 * @hooked trx_addons_action_importer_export_fields
	 *
	 * @param string $importer Importer object
	 */
	function trx_addons_calculated_fields_form_importer_export_fields( $importer ) {
		if ( trx_addons_exists_calculated_fields_form() && in_array( 'calculated-fields-form', $importer->options['required_plugins'] ) ) {
			$importer->show_exporter_fields( array(
				'slug'	=> 'calculated-fields-form',
				'title' => esc_html__('Calculated Fields Form', 'trx_addons')
			) );
		}
	}
}
