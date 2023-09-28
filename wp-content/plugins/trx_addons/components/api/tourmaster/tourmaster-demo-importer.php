<?php
/**
 * Plugin support: Tour Master (Importer support)
 *
 * @package ThemeREX Addons
 * @since v1.6.38
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_tourmaster_importer_required_plugins' ) ) {
	add_filter( 'trx_addons_filter_importer_required_plugins', 'trx_addons_tourmaster_importer_required_plugins', 10, 2 );
	/**
	 * Check if the required plugins are installed
	 * 
	 * @hooked trx_addons_filter_importer_required_plugins
	 *
	 * @param string $not_installed  Not installed plugins list as HTML string
	 * @param string $list           Required plugins list as comma separated string
	 * 
	 * @return string                Not installed plugins list as HTML string
	 */
	function trx_addons_tourmaster_importer_required_plugins( $not_installed = '', $list = '' ) {
		if ( strpos( $list, 'tourmaster' ) !== false && ! trx_addons_exists_tourmaster() ) {
			$not_installed .= '<br>' . esc_html__('Tour Master', 'trx_addons');
		}
		return $not_installed;
	}
}

if ( ! function_exists( 'trx_addons_tourmaster_importer_set_options' ) ) {
	add_filter( 'trx_addons_filter_importer_options', 'trx_addons_tourmaster_importer_set_options' );
	/**
	 * Set plugin's specific importer options
	 *
	 * @hooked trx_addons_filter_importer_options
	 *
	 * @param array $options		Options to set
	 * 
	 * @return array				Modified options
	 */
	function trx_addons_tourmaster_importer_set_options($options=array()) {
		if ( trx_addons_exists_tourmaster() && in_array( 'tourmaster', $options['required_plugins'] ) ) {
			$options['additional_options'][] = 'tourmaster_general';					// Add slugs to export options for this plugin
			$options['additional_options'][] = 'tourmaster_color';
			$options['additional_options'][] = 'tourmaster_plugin';
			// Do not export this option, because it contain secret keys
			//$options['additional_options'][] = 'tourmaster_payment';
			if ( is_array( $options['files'] ) && count( $options['files'] ) > 0 ) {
				foreach ( $options['files'] as $k => $v ) {
					$options['files'][$k]['file_with_tourmaster'] = str_replace( 'name.ext', 'tourmaster.txt', $v['file_with_'] );
				}
			}
		}
		return $options;
	}
}

if ( ! function_exists( 'trx_addons_tourmaster_importer_check_options' ) ) {
	add_filter( 'trx_addons_filter_import_theme_options', 'trx_addons_tourmaster_importer_check_options', 10, 4 );
	/**
	 * Prevent to import plugin's specific options if plugin is not installed
	 *
	 * @hooked trx_addons_filter_import_theme_options
	 *
	 * @param boolean $allow		Allow to import options or not
	 * @param string $k				Option name
	 * @param mixed $v				Option value
	 * @param array $options		Importer options
	 * 
	 * @return boolean				Allow to import options or not
	 */
	function trx_addons_tourmaster_importer_check_options( $allow, $k, $v, $options ) {
		if ( $allow && strpos( $k, 'tourmaster_' ) === 0 ) {
			$allow = trx_addons_exists_tourmaster() && in_array( 'tourmaster', $options['required_plugins'] );
		}
		return $allow;
	}
}

if ( ! function_exists( 'trx_addons_tourmaster_importer_show_params' ) ) {
	add_action( 'trx_addons_action_importer_params', 'trx_addons_tourmaster_importer_show_params', 10, 1 );
	/**
	 *  Add checkbox with a plugin name to the list of plugins to import
	 *
	 * @hooked trx_addons_action_importer_params
	 *
	 * @param array $importer		Importer object
	 */
	function trx_addons_tourmaster_importer_show_params( $importer ) {
		if ( trx_addons_exists_tourmaster() && in_array('tourmaster', $importer->options['required_plugins']) ) {
			$importer->show_importer_params( array(
				'slug' => 'tourmaster',
				'title' => esc_html__('Import Tour Master', 'trx_addons'),
				'part' => 0
			) );
		}
	}
}

if ( ! function_exists( 'trx_addons_tourmaster_importer_import' ) ) {
	add_action( 'trx_addons_action_importer_import', 'trx_addons_tourmaster_importer_import', 10, 2 );
	/**
	 * Import Tour Master data from the file
	 *
	 * @hooked trx_addons_action_importer_import
	 *
	 * @param array $importer		Importer object
	 * @param string $action		Action to perform: import_tourmaster
	 */
	function trx_addons_tourmaster_importer_import( $importer, $action ) {
		if ( trx_addons_exists_tourmaster() && in_array( 'tourmaster', $importer->options['required_plugins'] ) ) {
			if ( $action == 'import_tourmaster' ) {
				$importer->response['start_from_id'] = 0;
				$importer->import_dump( 'tourmaster', esc_html__( 'Tour Master data', 'trx_addons' ) );
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_tourmaster_importer_check_row' ) ) {
	add_filter( 'trx_addons_filter_importer_import_row', 'trx_addons_tourmaster_importer_check_row', 9, 4 );
	/**
	 * Check if the row will be imported
	 *
	 * @hooked trx_addons_filter_importer_import_row
	 *
	 * @param boolean $flag		Allow to import or not
	 * @param string $table		Table name
	 * @param array $row		Row data
	 * @param array $list		List of the plugins to import data for
	 * 
	 * @return boolean			Allow to import or not
	 */
	function trx_addons_tourmaster_importer_check_row( $flag, $table, $row, $list ) {
		if ( $flag || strpos( $list, 'tourmaster' ) === false ) {
			return $flag;
		}
		if ( trx_addons_exists_tourmaster() ) {
			if ( $table == 'posts' ) {
				$flag = in_array( $row['post_type'], array( TRX_ADDONS_TOURMASTER_CPT_TOUR, TRX_ADDONS_TOURMASTER_CPT_TOUR_COUPON, TRX_ADDONS_TOURMASTER_CPT_TOUR_SERVICE ) );
			}
		}
		return $flag;
	}
}

if ( ! function_exists( 'trx_addons_tourmaster_importer_import_fields' ) ) {
	add_action( 'trx_addons_action_importer_import_fields',	'trx_addons_tourmaster_importer_import_fields', 10, 1 );
	/**
	 * Display a plugin's name in the import progress area
	 *
	 * @hooked trx_addons_action_importer_import_fields
	 *
	 * @param array $importer		Importer object
	 */
	function trx_addons_tourmaster_importer_import_fields( $importer ) {
		if ( trx_addons_exists_tourmaster() && in_array( 'tourmaster', $importer->options['required_plugins'] ) ) {
			$importer->show_importer_fields( array(
				'slug'=>'tourmaster', 
				'title' => esc_html__('Tour Master', 'trx_addons')
			) );
		}
	}
}

if ( ! function_exists( 'trx_addons_tourmaster_importer_export' ) ) {
	add_action( 'trx_addons_action_importer_export', 'trx_addons_tourmaster_importer_export', 10, 1 );
	/**
	 * Export Tour Master data to the file
	 *
	 * @hooked trx_addons_action_importer_export
	 * 
	 * @trigger trx_addons_filter_importer_export_tables
	 *
	 * @param array $importer		Importer object
	 */
	function trx_addons_tourmaster_importer_export( $importer ) {
		if ( trx_addons_exists_tourmaster() && in_array( 'tourmaster', $importer->options['required_plugins'] ) ) {
			trx_addons_fpc( $importer->export_file_dir( 'tourmaster.txt' ), serialize( apply_filters( 'trx_addons_filter_importer_export_tables', array(
				"tourmaster_order"	=> $importer->export_dump("tourmaster_order"),
				"tourmaster_review"	=> $importer->export_dump("tourmaster_review")
				), 'tourmaster' ) )
			);
		}
	}
}

if ( ! function_exists( 'trx_addons_tourmaster_importer_export_fields' ) ) {
	add_action( 'trx_addons_action_importer_export_fields',	'trx_addons_tourmaster_importer_export_fields', 10, 1 );
	/**
	 * Display a plugin's name in the export progress area
	 *
	 * @hooked trx_addons_action_importer_export_fields
	 *
	 * @param array $importer		Importer object
	 */
	function trx_addons_tourmaster_importer_export_fields( $importer ) {
		if ( trx_addons_exists_tourmaster() && in_array( 'tourmaster', $importer->options['required_plugins'] ) ) {
			$importer->show_exporter_fields( array(
				'slug'	=> 'tourmaster',
				'title' => esc_html__('Tour Master', 'trx_addons')
			) );
		}
	}
}
