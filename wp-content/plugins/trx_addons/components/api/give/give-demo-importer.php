<?php
/**
 * Plugin support: Give (Importer support)
 *
 * @package ThemeREX Addons
 * @since v1.6.50
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_give_importer_required_plugins' ) ) {
	add_filter( 'trx_addons_filter_importer_required_plugins',	'trx_addons_give_importer_required_plugins', 10, 2 );
	/**
	 * Check if the required plugins are installed
	 * 
	 * @hooked trx_addons_filter_importer_required_plugins
	 *
	 * @param string $not_installed Not installed plugins list
	 * @param string $list          List of the plugins to check
	 * 
	 * @return string               Not installed plugins list
	 */
	function trx_addons_give_importer_required_plugins( $not_installed = '', $list = '' ) {
		if ( strpos( $list, 'give' ) !== false && ! trx_addons_exists_give() ) {
			$not_installed .= '<br>' . esc_html__( 'Give (Donation Form)', 'trx_addons' );
		}
		return $not_installed;
	}
}

if ( ! function_exists( 'trx_addons_give_importer_set_options' ) ) {
	add_filter( 'trx_addons_filter_importer_options', 'trx_addons_give_importer_set_options', 10, 1 );
	/**
	 * Add plugin's specific options to the importer options: a key 'additional_options' contain a list of options for export.
	 * Add options if plugin is in the required plugins list
	 * 
	 * @hooked trx_addons_filter_importer_options
	 *
	 * @param array $options Importer options
	 * 
	 * @return array  	 Modified options
	 */
	function trx_addons_give_importer_set_options( $options = array() ) {
		if ( trx_addons_exists_give() && in_array( 'give', $options['required_plugins'] ) ) {
			$options['additional_options'][] = 'give_settings';
			if (is_array($options['files']) && count($options['files']) > 0) {
				foreach ($options['files'] as $k => $v) {
					$options['files'][$k]['file_with_give'] = str_replace('name.ext', 'give.txt', $v['file_with_']);
				}
			}
		}
		return $options;
	}
}

if ( ! function_exists( 'trx_addons_give_importer_check_options' ) ) {
	add_filter( 'trx_addons_filter_import_theme_options', 'trx_addons_give_importer_check_options', 10, 4 );
	/**
	 * Check if an option will be imported: if plugin is in the required plugins list
	 * and the option is in the list of the plugin's options
	 * 
	 * @hooked trx_addons_filter_import_theme_options
	 *
	 * @param boolean $allow   Allow import or not
	 * @param string  $k       Option name
	 * @param mixed   $v       Option value
	 * @param array   $options Importer options
	 * 
	 * @return boolean         Allow import or not
	 */
	function trx_addons_give_importer_check_options( $allow, $k, $v, $options ) {
		if ($allow && $k == 'give_settings') {
			$allow = trx_addons_exists_give() && in_array('give', $options['required_plugins']);
		}
		return $allow;
	}
}

if ( ! function_exists( 'trx_addons_give_importer_show_params' ) ) {
	add_action( 'trx_addons_action_importer_params',	'trx_addons_give_importer_show_params', 10, 1 );
	/**
	 * Add a plugin to the checklist in the 'Import demo' step to enable import of this plugin's data
	 * 
	 * @hooked trx_addons_action_importer_params
	 *
	 * @param array $importer Importer object
	 */
	function trx_addons_give_importer_show_params( $importer ) {
		if ( trx_addons_exists_give() && in_array( 'give', $importer->options['required_plugins'] ) ) {
			$importer->show_importer_params( array(
				'slug' => 'give',
				'title' => esc_html__('Import Give (Donation Form)', 'trx_addons'),
				'part' => 1
			) );
		}
	}
}

if ( ! function_exists( 'trx_addons_give_importer_import' ) ) {
	add_action( 'trx_addons_action_importer_import',	'trx_addons_give_importer_import', 10, 2 );
	/**
	 * Import Give (Donation Form) data from the file with a demo data dump
	 * 
	 * @hooked trx_addons_action_importer_import
	 *
	 * @param array $importer Importer object
	 * @param string $action  Action to perform: 'import_{$slug}'
	 */
	function trx_addons_give_importer_import( $importer, $action ) {
		if ( trx_addons_exists_give() && in_array( 'give', $importer->options['required_plugins'] ) ) {
			if ( $action == 'import_give' ) {
				$importer->response['start_from_id'] = 0;
				$importer->import_dump( 'give', esc_html__( 'Give (Donation Form)', 'trx_addons' ) );
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_give_importer_check_row' ) ) {
	add_filter( 'trx_addons_filter_importer_import_row', 'trx_addons_give_importer_check_row', 9, 4 );
	/**
	 * Check if the row will be imported to the table 'posts': if plugin is in the required plugins list
	 * and the row's post type is supported by the plugin
	 * 
	 * @hooked trx_addons_filter_importer_import_row
	 *
	 * @param boolean $flag   Allow import or not
	 * @param string  $table  Table name
	 * @param array   $row    Row data
	 * @param array   $list   List of the required plugins
	 * 
	 * @return boolean        Allow import or not
	 */
	function trx_addons_give_importer_check_row( $flag, $table, $row, $list ) {
		if ( $flag || strpos( $list, 'give' ) === false ) {
			return $flag;
		}
		if ( trx_addons_exists_give() ) {
			if ( $table == 'posts' ) {
				$flag = in_array( $row['post_type'], array( TRX_ADDONS_GIVE_FORMS_PT_FORMS, TRX_ADDONS_GIVE_FORMS_PT_PAYMENT ) );
			}
		}
		return $flag;
	}
}

if ( ! function_exists( 'trx_addons_give_importer_import_fields' ) ) {
	add_action( 'trx_addons_action_importer_import_fields',	'trx_addons_give_importer_import_fields', 10, 1 );
	/**
	 * Add a plugin name to import progress log
	 * 
	 * @hooked trx_addons_action_importer_import_fields
	 *
	 * @param array $importer Importer object
	 */
	function trx_addons_give_importer_import_fields( $importer ) {
		if ( trx_addons_exists_give() && in_array( 'give', $importer->options['required_plugins'] ) ) {
			$importer->show_importer_fields( array(
				'slug'	=> 'give', 
				'title'	=> esc_html__('Give (Donation Form)', 'trx_addons')
			) );
		}
	}
}

if ( ! function_exists( 'trx_addons_give_importer_export' ) ) {
	add_action( 'trx_addons_action_importer_export',	'trx_addons_give_importer_export', 10, 1 );
	/**
	 * Export Give (Donation Form) data to the file
	 * 
	 * @hooked trx_addons_action_importer_export
	 * 
	 * @trigger trx_addons_filter_importer_export_tables
	 *
	 * @param array $importer Importer object
	 */
	function trx_addons_give_importer_export( $importer ) {
		if ( trx_addons_exists_give() && in_array( 'give', $importer->options['required_plugins'] ) ) {
			trx_addons_fpc( $importer->export_file_dir('give.txt'), serialize( apply_filters( 'trx_addons_filter_importer_export_tables', array(
				'give_commentmeta'        => $importer->export_dump('give_commentmeta'),
				'give_comments'           => $importer->export_dump('give_comments'),
				'give_donationmeta'       => $importer->export_dump('give_donationmeta'),
				'give_donormeta'          => $importer->export_dump('give_donormeta'),
				'give_donors'             => $importer->export_dump('give_donors'),
				'give_formmeta'           => $importer->export_dump('give_formmeta'),
				'give_logmeta'            => $importer->export_dump('give_logmeta'),
				'give_logs'               => $importer->export_dump('give_logs'),
				'give_revenue'            => $importer->export_dump('give_revenue'),
				'give_sequental_ordering' => $importer->export_dump('give_sequental_ordering'),
			), 'give' ) ) );
		}
	}
}

if ( ! function_exists( 'trx_addons_give_importer_export_fields' ) ) {
	add_action( 'trx_addons_action_importer_export_fields',	'trx_addons_give_importer_export_fields', 10, 1 );
	/**
	 * Display a file with an exported data in the list of files to download
	 * 
	 * @hooked trx_addons_action_importer_export_fields
	 *
	 * @param array $importer Importer object
	 */
	function trx_addons_give_importer_export_fields( $importer ) {
		if ( trx_addons_exists_give() && in_array( 'give', $importer->options['required_plugins'] ) ) {
			$importer->show_exporter_fields( array(
				'slug'	=> 'give',
				'title' => esc_html__( 'Give (Donation Form)', 'trx_addons' )
			) );
		}
	}
}
