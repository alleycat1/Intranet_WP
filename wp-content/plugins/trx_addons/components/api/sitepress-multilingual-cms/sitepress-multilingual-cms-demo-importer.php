<?php
/**
 * Plugin support: WPML (Importer support)
 *
 * @package ThemeREX Addons
 * @since v1.6.38
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_wpml_importer_required_plugins' ) ) {
	add_filter( 'trx_addons_filter_importer_required_plugins',	'trx_addons_wpml_importer_required_plugins', 10, 2 );
	/**
	 * Check if WPML is in the required plugins list and if it's installed
	 * 
	 * @hooked trx_addons_filter_importer_required_plugins
	 *
	 * @param string $not_installed  Not installed plugins list
	 * @param string $list           Plugins list
	 * 
	 * @return string  			Not installed plugins list
	 */
	function trx_addons_wpml_importer_required_plugins( $not_installed = '', $list = '' ) {
		if ( strpos( $list, 'sitepress-multilingual-cms' ) !== false && ! trx_addons_exists_wpml() ) {
			$not_installed .= '<br>' . esc_html__('WPML - Sitepress Multilingual CMS', 'trx_addons');
		}
		return $not_installed;
	}
}

if ( ! function_exists( 'trx_addons_wpml_importer_set_options' ) ) {
	add_filter( 'trx_addons_filter_importer_options', 'trx_addons_wpml_importer_set_options' );
	/**
	 * Set plugin's specific importer options for export
	 * 
	 * @hooked trx_addons_filter_importer_options
	 *
	 * @param array $options		Importer options
	 * 
	 * @return array				Modified options
	 */
	function trx_addons_wpml_importer_set_options( $options = array() ) {
		if ( trx_addons_exists_wpml() && in_array('sitepress-multilingual-cms', $options['required_plugins']) ) {
			$options['additional_options'][] = 'icl_sitepress_settings';
		}
		if ( is_array( $options['files'] ) && count( $options['files'] ) > 0 ) {
			foreach ( $options['files'] as $k => $v ) {
				$options['files'][$k]['file_with_sitepress-multilingual-cms'] = str_replace( 'name.ext', 'sitepress-multilingual-cms.txt', $v['file_with_'] );
			}
		}
		return $options;
	}
}

if ( ! function_exists( 'trx_addons_wpml_importer_check_options' ) ) {
	add_filter( 'trx_addons_filter_import_theme_options', 'trx_addons_wpml_importer_check_options', 10, 4 );
	/**
	 * Prevent import plugin's specific options if plugin is not installed
	 * 
	 * @hooked trx_addons_filter_import_theme_options
	 *
	 * @param string $allow			Allow to import theme options or not
	 * @param string $k				Option name
	 * @param string $v				Option value
	 * @param array $options		Importer options
	 * 
	 * @return string				Allow to import theme options or not
	 */
	function trx_addons_wpml_importer_check_options( $allow, $k, $v, $options ) {
		if ( $allow && $k == 'icl_sitepress_settings' ) {
			$allow = trx_addons_exists_wpml() && in_array('sitepress-multilingual-cms', $options['required_plugins']);
		}
		return $allow;
	}
}

if ( ! function_exists( 'trx_addons_wpml_importer_show_params' ) ) {
	add_action( 'trx_addons_action_importer_params', 'trx_addons_wpml_importer_show_params', 10, 1 );
	/**
	 * Show the checkbox with a plugin's name in the plugins import form
	 * 
	 * @hooked trx_addons_action_importer_params
	 *
	 * @param object $importer		Importer object
	 */
	function trx_addons_wpml_importer_show_params( $importer ) {
		if ( trx_addons_exists_wpml() && in_array( 'sitepress-multilingual-cms', $importer->options['required_plugins'] ) ) {
			$importer->show_importer_params( array(
				'slug' => 'sitepress-multilingual-cms',
				'title' => esc_html__('Import Sitepress Multilingual CMS (WPML)', 'trx_addons'),
				'part' => 0
			) );
		}
	}
}

if ( ! function_exists( 'trx_addons_wpml_importer_import' ) ) {
	add_action( 'trx_addons_action_importer_import', 'trx_addons_wpml_importer_import', 10, 2 );
	/**
	 * Import plugin's data
	 * 
	 * @hooked trx_addons_action_importer_import
	 *
	 * @param object $importer		Importer object
	 * @param string $action		Action to perform: 'import_sitepress-multilingual-cms'
	 */
	function trx_addons_wpml_importer_import( $importer, $action ) {
		if ( trx_addons_exists_wpml() && in_array( 'sitepress-multilingual-cms', $importer->options['required_plugins'] ) ) {
			if ( $action == 'import_sitepress-multilingual-cms' ) {
				$importer->response['start_from_id'] = 0;
				$importer->import_dump('sitepress-multilingual-cms', esc_html__('Sitepress Multilingual CMS (WPML) data', 'trx_addons'));
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_wpml_importer_import_fields' ) ) {
	add_action( 'trx_addons_action_importer_import_fields',	'trx_addons_wpml_importer_import_fields', 10, 1 );
	/**
	 * Display plugin's name in the import progress area
	 * 
	 * @hooked trx_addons_action_importer_import_fields
	 *
	 * @param object $importer		Importer object
	 */
	function trx_addons_wpml_importer_import_fields( $importer ) {
		if ( trx_addons_exists_wpml() && in_array('sitepress-multilingual-cms', $importer->options['required_plugins']) ) {
			$importer->show_importer_fields( array(
				'slug'=>'sitepress-multilingual-cms', 
				'title' => esc_html__('Sitepress Multilingual CMS (WPML) data', 'trx_addons')
			) );
		}
	}
}

if ( ! function_exists( 'trx_addons_wpml_importer_import_separate_insert' ) ) {
	add_filter( 'trx_addons_filter_importer_separate_insert', 'trx_addons_wpml_importer_import_separate_insert', 10, 1 );
	/**
	 * Add the table 'icl_translations' to the list of tables, whos records must be imported 'one by one'
	 * (not in the large combined insert statement)
	 * 
	 * @hooked trx_addons_filter_importer_separate_insert
	 *
	 * @param array $tables		List of tables with separate queries
	 * 
	 * @return array			Modified list
	 */
	function trx_addons_wpml_importer_import_separate_insert( $tables ) {
		if ( is_array( $tables ) ) {
			$tables[] = 'icl_translations';
		}
		return $tables;
	}
}

if ( ! function_exists( 'trx_addons_wpml_importer_export' ) ) {
	add_action( 'trx_addons_action_importer_export', 'trx_addons_wpml_importer_export', 10, 1 );
	/**
	 * Export plugin's data to the file
	 * 
	 * @hooked trx_addons_action_importer_export
	 * 
	 * @trigger trx_addons_filter_importer_export_tables
	 *
	 * @param object $importer		Importer object
	 */
	function trx_addons_wpml_importer_export( $importer ) {
		if ( trx_addons_exists_wpml() && in_array( 'sitepress-multilingual-cms', $importer->options['required_plugins'] ) ) {
			trx_addons_fpc( $importer->export_file_dir( 'sitepress-multilingual-cms.txt'), serialize( apply_filters( 'trx_addons_filter_importer_export_tables', array(
				"icl_languages"				=> $importer->export_dump("icl_languages"),
				"icl_locale_map"			=> $importer->export_dump("icl_locale_map"),
				"icl_strings"				=> $importer->export_dump("icl_strings"),
				"icl_string_packages"		=> $importer->export_dump("icl_string_packages"),
				"icl_string_pages"			=> $importer->export_dump("icl_string_pages"),
				"icl_string_positions"		=> $importer->export_dump("icl_string_positions"),
				"icl_translate"				=> $importer->export_dump("icl_translate"),
				"icl_translate_job"			=> $importer->export_dump("icl_translate_job"),
				"icl_translations"			=> $importer->export_dump("icl_translations"),
				"icl_translation_batches"	=> $importer->export_dump("icl_translation_batches"),
				"icl_translation_status"	=> $importer->export_dump("icl_translation_status"),
				), 'wpml' ) )
			);
		}
	}
}

if ( ! function_exists( 'trx_addons_wpml_importer_export_fields' ) ) {
	add_action( 'trx_addons_action_importer_export_fields',	'trx_addons_wpml_importer_export_fields', 10, 1 );
	/**
	 * Display plugin's name in the export area to allow download the data file
	 * 
	 * @hooked trx_addons_action_importer_export_fields
	 *
	 * @param object $importer		Importer object
	 */
	function trx_addons_wpml_importer_export_fields( $importer ) {
		if ( trx_addons_exists_wpml() && in_array( 'sitepress-multilingual-cms', $importer->options['required_plugins'] ) ) {
			$importer->show_exporter_fields( array(
				'slug'	=> 'sitepress-multilingual-cms',
				'title' => esc_html__('Sitepress Multilingual CMS (WPML) data', 'trx_addons')
			) );
		}
	}
}
