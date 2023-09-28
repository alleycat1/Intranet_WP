<?php
/**
 * Plugin support: Content Timeline (Importer support)
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_content_timeline_importer_required_plugins' ) ) {
	add_filter( 'trx_addons_filter_importer_required_plugins', 'trx_addons_content_timeline_importer_required_plugins', 10, 2 );
	/**
	 * Check if the required plugins are installed
	 * 
	 * @hooked trx_addons_filter_importer_required_plugins
	 *
	 * @param string $not_installed  Not installed plugins list
	 * @param string $list           Required plugins list
	 * 
	 * @return string                Not installed plugins list
	 */
	function trx_addons_content_timeline_importer_required_plugins( $not_installed = '', $list = '' ) {
		if ( strpos( $list, 'content_timeline' ) !== false && ! trx_addons_exists_content_timeline() ) {
			$not_installed .= '<br>' . esc_html__('Content Timeline', 'trx_addons');
		}
		return $not_installed;
	}
}

if ( ! function_exists( 'trx_addons_content_timeline_importer_set_options' ) ) {
	add_filter( 'trx_addons_filter_importer_options', 'trx_addons_content_timeline_importer_set_options' );
	/**
	 * Add plugin's options to the list of options should be exported
	 * 
	 * @hooked trx_addons_filter_importer_options
	 *
	 * @param array $options    Importer options
	 * 
	 * @return array            Modified options
	 */
	function trx_addons_content_timeline_importer_set_options( $options = array() ) {
		if ( trx_addons_exists_content_timeline() && in_array( 'content_timeline', $options['required_plugins'] ) ) {
			//$options['additional_options'][] = 'content_timeline_calendar_options';
			if ( is_array( $options['files'] ) && count( $options['files'] ) > 0 ) {
				foreach ( $options['files'] as $k => $v ) {
					$options['files'][$k]['file_with_content_timeline'] = str_replace( 'name.ext', 'content_timeline.txt', $v['file_with_'] );
				}
			}
		}
		return $options;
	}
}

if ( ! function_exists( 'trx_addons_content_timeline_importer_show_params' ) ) {
	add_action( 'trx_addons_action_importer_params', 'trx_addons_content_timeline_importer_show_params', 10, 1 );
	/**
	 * Add checkbox for this plugin to the one-click importer checklist
	 * 
	 * @hooked trx_addons_action_importer_params
	 *
	 * @param array $importer    Posts importer object
	 */
	function trx_addons_content_timeline_importer_show_params( $importer ) {
		if ( trx_addons_exists_content_timeline() && in_array( 'content_timeline', $importer->options['required_plugins'] ) ) {
			$importer->show_importer_params( array(
				'slug' => 'content_timeline',
				'title' => esc_html__('Import Content Timeline', 'trx_addons'),
				'part' => 0
			) );
		}
	}
}

if ( ! function_exists( 'trx_addons_content_timeline_importer_import' ) ) {
	add_action( 'trx_addons_action_importer_import', 'trx_addons_content_timeline_importer_import', 10, 2 );
	/**
	 * Import plugin-specific data from the file
	 * 
	 * @hooked trx_addons_action_importer_import
	 *
	 * @param array $importer    Posts importer object
	 * @param string $action     Action to perform
	 */
	function trx_addons_content_timeline_importer_import($importer, $action) {
		if ( trx_addons_exists_content_timeline() && in_array( 'content_timeline', $importer->options['required_plugins'] ) ) {
			if ( $action == 'import_content_timeline' ) {
				$importer->response['start_from_id'] = 0;
				$importer->import_dump( 'content_timeline', esc_html__( 'Content Timeline', 'trx_addons' ) );
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_content_timeline_importer_import_fields' ) ) {
	add_action( 'trx_addons_action_importer_import_fields',	'trx_addons_content_timeline_importer_import_fields', 10, 1 );
	/**
	 * Show plugin's name in the import progress area
	 * 
	 * @hooked trx_addons_action_importer_import_fields
	 *
	 * @param array $importer    Posts importer object
	 */
	function trx_addons_content_timeline_importer_import_fields( $importer) {
		if ( trx_addons_exists_content_timeline() && in_array( 'content_timeline', $importer->options['required_plugins'] ) ) {
			$importer->show_importer_fields( array(
				'slug'	=> 'content_timeline', 
				'title'	=> esc_html__('Content Timeline', 'trx_addons')
			) );
		}
	}
}

if ( ! function_exists( 'trx_addons_content_timeline_importer_export' ) ) {
	add_action( 'trx_addons_action_importer_export', 'trx_addons_content_timeline_importer_export', 10, 1 );
	/**
	 * Export plugin-specific posts to the data file
	 * 
	 * @hooked trx_addons_action_importer_export
	 * 
	 * @trigger trx_addons_filter_importer_export_tables
	 *
	 * @param array $importer    Posts importer object
	 */
	function trx_addons_content_timeline_importer_export( $importer ) {
		if ( trx_addons_exists_content_timeline() && in_array( 'content_timeline', $importer->options['required_plugins'] ) ) {
			trx_addons_fpc( $importer->export_file_dir( 'content_timeline.txt' ), serialize( apply_filters( 'trx_addons_filter_importer_export_tables', array(
				'ctimelines' => $importer->export_dump( 'ctimelines' )
			), 'content_timeline' ) ) );
		}
	}
}

if ( ! function_exists( 'trx_addons_content_timeline_importer_export_fields' ) ) {
	add_action( 'trx_addons_action_importer_export_fields',	'trx_addons_content_timeline_importer_export_fields', 10, 1 );
	/**
	 * Show plugin's name in the list of exported files
	 * 
	 * @hooked trx_addons_action_importer_export_fields
	 *
	 * @param array $importer    Posts importer object
	 */
	function trx_addons_content_timeline_importer_export_fields( $importer ) {
		if ( trx_addons_exists_content_timeline() && in_array( 'content_timeline', $importer->options['required_plugins'] ) ) {
			$importer->show_exporter_fields( array(
				'slug'	=> 'content_timeline',
				'title' => esc_html__('Content Timeline', 'trx_addons')
			) );
		}
	}
}
