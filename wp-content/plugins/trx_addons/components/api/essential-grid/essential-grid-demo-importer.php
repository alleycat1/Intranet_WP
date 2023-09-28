<?php
/**
 * Plugin support: Essential Grid (Importer support)
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_essential_grid_importer_required_plugins' ) ) {
	add_filter( 'trx_addons_filter_importer_required_plugins',	'trx_addons_essential_grid_importer_required_plugins', 10, 2 );
	/**
	 * Check if the required plugins are installed
	 * 
	 * @hooked trx_addons_filter_importer_required_plugins
	 * 
	 * @param string $not_installed		Not installed plugins list
	 * @param string $list				Required plugins list
	 * 
	 * @return string					Modified list with not installed plugins
	 */
	function trx_addons_essential_grid_importer_required_plugins( $not_installed = '', $list = '' ) {
		if ( strpos( $list, 'essential-grid' ) !== false && ! trx_addons_exists_essential_grid() ) {
			$not_installed .= '<br>' . esc_html__('Essential Grids', 'trx_addons');
		}
		return $not_installed;
	}
}

if ( ! function_exists( 'trx_addons_essential_grid_importer_set_options' ) ) {
	add_filter( 'trx_addons_filter_importer_options', 'trx_addons_essential_grid_importer_set_options', 10, 1 );
	/**
	 * Set plugin's specific options for export and add a key 'file_with_essential-grid' to the list of files
	 * with the exported data
	 * 
	 * @hooked trx_addons_filter_importer_options
	 * 
	 * @param array $options		Options to set
	 * 
	 * @return array				Modified options
	 */
	function trx_addons_essential_grid_importer_set_options( $options = array() ) {
		if ( trx_addons_exists_essential_grid() && in_array( 'essential-grid', $options['required_plugins'] ) ) {
			if ( is_array( $options['files'] ) && count( $options['files'] ) > 0 ) {
				foreach ( $options['files'] as $k => $v ) {
					$options['files'][ $k ]['file_with_essential-grid'] = str_replace( 'name.ext', 'ess_grid.json', $v['file_with_'] );
				}
			}
		}
		return $options;
	}
}

if ( ! function_exists( 'trx_addons_essential_grid_importer_show_params' ) ) {
	add_action( 'trx_addons_action_importer_params', 'trx_addons_essential_grid_importer_show_params', 10, 1 );
	/**
	 * Add a checkbox with a plugin name to the one-click importer checklist of plugins to be imported
	 * 
	 * @hooked trx_addons_action_importer_params
	 * 
	 * @param object $importer		Importer object
	 */
	function trx_addons_essential_grid_importer_show_params( $importer ) {
		if ( trx_addons_exists_essential_grid() && in_array( 'essential-grid', $importer->options['required_plugins'] ) ) {
			$importer->show_importer_params( array(
				'slug' => 'essential-grid',
				'title' => esc_html__('Import Essential Grid', 'trx_addons'),
				'part' => 1
			) );
		}
	}
}

if ( ! function_exists( 'trx_addons_essential_grid_importer_clear_tables' ) ) {
	add_action( 'trx_addons_action_importer_clear_tables', 'trx_addons_essential_grid_importer_clear_tables', 10, 2 );
	/**
	 * Clear tables before import Essential Grid data
	 * 
	 * @hooked trx_addons_action_importer_clear_tables
	 * 
	 * @param object $importer		Importer object
	 * @param string $clear_tables	Tables to clear
	 */
	function trx_addons_essential_grid_importer_clear_tables( $importer, $clear_tables ) {
		if ( trx_addons_exists_essential_grid() && in_array( 'essential-grid', $importer->options['required_plugins'] ) ) {
			if ( strpos( $clear_tables, 'essential-grid' ) !== false ) {
				if ( $importer->options['debug'] ) {
					dfl( __( 'Clear Essential Grid tables', 'trx_addons' ) );
				}
				trx_addons_essential_grid_clear_tables();
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_essential_grid_clear_tables' ) ) {
	/**
	 * Clear tables with Essential Grid data
	 */
	function trx_addons_essential_grid_clear_tables() {
		global $wpdb;
		$table = class_exists( 'Essential_Grid' ) && defined( 'Essential_Grid::TABLE_GRID' )				// 3.0.16-
					? Essential_Grid::TABLE_GRID
					: ( class_exists( 'Essential_Grid_Db' ) && defined( 'Essential_Grid_Db::TABLE_GRID' )	// 3.0.17+
						? Essential_Grid_Db::TABLE_GRID
						: 'eg_grids'
						);
		$res = $wpdb->query( "TRUNCATE TABLE " . esc_sql( $wpdb->prefix ) . esc_sql( $table ) );
		if ( is_wp_error( $res ) ) {
			dfl( sprintf( __( 'Failed truncate table "%s". Error message: %s', 'trx_addons' ), $table, $res->get_error_message() ) );
		}
	}
}

if ( ! function_exists( 'trx_addons_essential_grid_importer_import' ) ) {
	add_action( 'trx_addons_action_importer_import', 'trx_addons_essential_grid_importer_import', 10, 2 );
	/**
	 * Import Essential Grid data
	 * 
	 * @hooked trx_addons_action_importer_import
	 * 
	 * @param object $importer		Importer object
	 * @param string $action		Action to perform: 'import_essential-grid'
	 */
	function trx_addons_essential_grid_importer_import( $importer, $action ) {
		if ( trx_addons_exists_essential_grid() && in_array( 'essential-grid', $importer->options['required_plugins'] ) ) {
			if ( $action == 'import_essential-grid' && ! empty( $importer->options['files'][ $importer->options['demo_type'] ]['file_with_essential-grid'] ) ) {
				if ( ( $txt = $importer->get_file( $importer->options['files'][ $importer->options['demo_type'] ]['file_with_essential-grid'] ) ) != '' ) {
					trx_addons_essential_grid_import( $importer->replace_site_url( $txt ), $importer );
				}
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_essential_grid_import' ) ) {
	/**
	 * Import Essential Grid data via core plugin's class Essential_Grid_Import
	 *
	 * @param string $txt			Serialized data
	 * @param object $importer		Importer object
	 */
	function trx_addons_essential_grid_import( $txt, $importer = null ) {
		$data = json_decode( $txt, true );
		try {
			$im = new Essential_Grid_Import();
	
			if ( is_array( $data ) && count( $data ) > 0 ) {
				// Sort arrays by id (needs for latest versions of the plugin)
				foreach ( $data as $k => $v ) {
					if ( in_array( $k, array( 'grids', 'skins', 'elements', 'navigation-skins' ) ) ) {
						usort( $data[ $k ], function( $a, $b ) {
							return (int)$a['id'] < (int)$b['id'] ? -1 : ( (int)$a['id'] > (int)$b['id'] ? 1 : 0 );
						} );
					}
				}

				// Prepare arrays with overwrite flags
				$tmp = array();
				foreach ( $data as $k => $v ) {
					if (      $k == 'grids' ) {				$name = 'grids';			$name_1 = 'grid';			$name_id = 'id'; }
					else if ( $k == 'skins' ) {				$name = 'skins'; 			$name_1 = 'skin';			$name_id = 'id'; }
					else if ( $k == 'elements' ) {			$name = 'elements';			$name_1 = 'element';		$name_id = 'id'; }
					else if ( $k == 'navigation-skins' ) {	$name = 'navigation-skins';	$name_1 = 'nav-skin';		$name_id = 'id'; }
					else if ( $k == 'punch-fonts' ) {		$name = 'punch-fonts';		$name_1 = 'punch-fonts';	$name_id = 'handle'; }
					else if ( $k == 'custom-meta' ) {		$name = 'custom-meta';		$name_1 = 'custom-meta';	$name_id = 'handle'; }

					if ( $k == 'global-css' ) {
						$tmp['import-global-styles'] = "on";
						$tmp['global-styles-overwrite'] = "append";	//"overwrite";
					} else {
						$tmp[ 'import-' . $name ] = "true";
						$tmp[ 'import-' . $name . '-' . $name_id ] = array();
						if ( is_array( $v ) && count( $v ) > 0 ) {
							foreach ( $v as $v1 ) {
								$tmp[ 'import-' . $name . '-' . $name_id ][] = $v1[ $name_id ];
								$tmp[ $name_1 . '-overwrite-' . $name_id ] = "append";	//"overwrite";
							}
						}
					}
				}
				$im->set_overwrite_data( $tmp ); //set overwrite data global to class
			}
								
			$skins = @$data['skins'];
			if ( ! empty( $skins ) && is_array( $skins ) ) {
				foreach ( $skins as $key => $skin ) {
					if ( class_exists( 'Essential_Grid_Plugin_Update' ) && method_exists( 'Essential_Grid_Plugin_Update', 'process_update_216' ) ) {
						$skins[ $key ] = Essential_Grid_Plugin_Update::process_update_216( $skin, true );
					}
				}
				$skins_ids = @$tmp['import-skins-id'];
				$skins_imported = $im->import_skins( $skins, $skins_ids );
			}
						
			$navigation_skins = @$data['navigation-skins'];
			if ( ! empty( $navigation_skins ) && is_array( $navigation_skins ) ) {
				$navigation_skins_ids = @$tmp['import-navigation-skins-id'];
				$navigation_skins_imported = $im->import_navigation_skins( @$navigation_skins, $navigation_skins_ids );
			}
						
			$grids = @$data['grids'];
			if ( ! empty( $grids ) && is_array( $grids ) ) {
				$grids_ids = @$tmp['import-grids-id'];
				$grids_imported = $im->import_grids( $grids, $grids_ids );
			}
						
			$elements = @$data['elements'];
			if ( ! empty( $elements ) && is_array( $elements ) ) {
				$elements_ids = @$tmp['import-elements-id'];
				$elements_imported = $im->import_elements( @$elements, $elements_ids );
			}
						
			$custom_metas = @$data['custom-meta'];
			if ( ! empty( $custom_metas ) && is_array( $custom_metas ) ) {
				$custom_metas_handle = @$tmp['import-custom-meta-handle'];
				$custom_metas_imported = $im->import_custom_meta( $custom_metas, $custom_metas_handle );
			}
						
			$custom_fonts = @$data['punch-fonts'];
			if ( ! empty( $custom_fonts ) && is_array( $custom_fonts ) ) {
				$custom_fonts_handle = @$tmp['import-punch-fonts-handle'];
				$custom_fonts_imported = $im->import_punch_fonts( $custom_fonts, $custom_fonts_handle );
			}
						
			if ( @$tmp['import-global-styles'] == 'on' ) {
				$global_css = @$data['global-css'];
				$global_styles_imported = $im->import_global_styles( $global_css );
			}

			if ( is_object( $importer ) ){
				if ( $importer->options['debug'] ) {
					dfl( __( 'Essential Grid import complete', 'trx_addons' ) );
				}
			} else {
				// Add result to log file
				echo esc_html__( 'Essential Grid import complete. ', 'trx_addons' ) . "\r\n";
			}
		
		} catch ( Exception $d ) {
			$msg = sprintf( esc_html__( 'Essential Grid import error: %s', 'trx_addons' ), $d->getMessage() );
			if ( is_object( $importer ) ) {
				$importer->response['error'] = $msg;
				if ( $importer->options['debug'] ) {
					dfl( $msg );
				}
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_essential_grid_importer_check_row' ) ) {
	add_filter( 'trx_addons_filter_importer_import_row', 'trx_addons_essential_grid_importer_check_row', 9, 4 );
	/**
	 * Check if the row will be imported
	 * 
	 * @hooked trx_addons_filter_importer_import_row
	 * 
	 * @trigger essgrid_PunchPost_custom_post_type
	 *
	 * @param boolean $flag  Allow to import or not
	 * @param string $table  Table name
	 * @param array $row     Row data
	 * @param array $list	 List with slugs of the plugins for import
	 * 
	 * @return boolean       Allow to import or not
	 */
	function trx_addons_essential_grid_importer_check_row( $flag, $table, $row, $list ) {
		if ( $flag || strpos( $list, 'essential-grid' ) === false ) {
			return $flag;
		}
		if ( trx_addons_exists_essential_grid() ) {
			if ( $table == 'posts' ) {
				$flag = $row['post_type'] == apply_filters( 'essgrid_PunchPost_custom_post_type', 'essential_grid' );
			}
		}
		return $flag;
	}
}

if ( ! function_exists( 'trx_addons_essential_grid_importer_import_fields' ) ) {
	add_action( 'trx_addons_action_importer_import_fields',	'trx_addons_essential_grid_importer_import_fields', 10, 1 );
	/**
	 * Add checkbox with a plugin's name to the one-click importer
	 * 
	 * @hooked trx_addons_action_importer_import_fields
	 *
	 * @param array $importer	One-click importer
	 */
	function trx_addons_essential_grid_importer_import_fields( $importer ) {
		if ( trx_addons_exists_essential_grid() && in_array( 'essential-grid', $importer->options['required_plugins'] ) ) {
			$importer->show_importer_fields( array(
				'slug'=>'essential-grid', 
				'title' => esc_html__('Essential Grid', 'trx_addons')
			) );
		}
	}
}
