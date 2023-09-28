<?php
/**
 * Plugin support: Revolution Slider (Importer support)
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


if ( ! function_exists( 'trx_addons_revslider_importer_required_plugins' ) ) {
	add_filter( 'trx_addons_filter_importer_required_plugins',	'trx_addons_revslider_importer_required_plugins', 10, 2 );
	/**
	 * Check if the required plugins are installed
	 * 
	 * @hooked trx_addons_filter_importer_required_plugins
	 *
	 * @param string $not_installed  Not installed plugins list as HTML string
	 * @param string $list           Required plugins list
	 * 
	 * @return string                Modified list of not installed plugins
	 */
	function trx_addons_revslider_importer_required_plugins( $not_installed = '', $list = '' ) {
		if ( strpos( $list, 'revslider' ) !== false && ! trx_addons_exists_revslider() ) {
			$not_installed .= '<br>' . esc_html__('Revolution Slider', 'trx_addons');
		}
		return $not_installed;
	}
}

if ( ! function_exists( 'trx_addons_revslider_importer_set_options' ) ) {
	add_filter( 'trx_addons_filter_importer_options', 'trx_addons_revslider_importer_set_options', 10, 2 );
	/**
	 * Set plugin's specific importer options: options to export and a file name with Revolution Slider data
	 * 
	 * @hooked trx_addons_filter_importer_options
	 *
	 * @param array $options  Importer options
	 * 
	 * @return array          Modified options
	 */
	function trx_addons_revslider_importer_set_options( $options = array() ) {
		if ( trx_addons_exists_revslider() && in_array( 'revslider', $options['required_plugins'] ) ) {
			$options['additional_options'][] = 'revslider-global-settings';
			if ( is_array( $options['files'] ) && count( $options['files'] ) > 0 ) {
				foreach ( $options['files'] as $k => $v ) {
					$options['files'][$k]['file_with_revslider'] = str_replace( 'name.ext', 'revslider.txt', $v['file_with_'] );
				}
			}
		}
		return $options;
	}
}

if ( ! function_exists( 'trx_addons_revslider_importer_show_params' ) ) {
	add_action( 'trx_addons_action_importer_params', 'trx_addons_revslider_importer_show_params', 10, 1 );
	/**
	 * Add a checkbox with a plugin name to the one-click importer options
	 * 
	 * @hooked trx_addons_action_importer_params
	 *
	 * @param object $importer  Importer object
	 */
	function trx_addons_revslider_importer_show_params( $importer ) {
		if ( trx_addons_exists_revslider() && in_array( 'revslider', $importer->options['required_plugins'] ) ) {
			$importer->show_importer_params( array(
				'slug' => 'revslider',
				'title' => esc_html__('Import Revolution Sliders', 'trx_addons'),
				'part' => 1
			) );
		}
	}
}

if ( ! function_exists( 'trx_addons_revslider_importer_clear_tables' ) ) {
	add_action( 'trx_addons_action_importer_clear_tables', 'trx_addons_revslider_importer_clear_tables', 10, 2 );
	/**
	 * Clear a plugin's tables before import
	 * 
	 * @hooked trx_addons_action_importer_clear_tables
	 *
	 * @param object $importer      Importer object
	 * @param string $clear_tables  Tables to clear (comma separated list)
	 */
	function trx_addons_revslider_importer_clear_tables( $importer, $clear_tables ) {
		if ( trx_addons_exists_revslider() && in_array( 'revslider', $importer->options['required_plugins'] ) ) {
			if ( strpos( $clear_tables, 'revslider' ) !== false ) {
				if ( $importer->options['debug'] ) {
					dfl( __( 'Clear Revolution Slider tables', 'trx_addons' ) );
				}
				trx_addons_revslider_clear_tables();
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_revslider_clear_tables' ) ) {
	/**
	 * Clear Revolution Slider tables
	 */
	function trx_addons_revslider_clear_tables() {
		global $wpdb;
		$res = $wpdb->query( "TRUNCATE TABLE " . esc_sql($wpdb->prefix) . "revslider_sliders" );
		if ( is_wp_error( $res ) ) dfl( __( 'Failed truncate table "revslider_sliders".', 'trx_addons' ) . ' ' . ( $res->get_error_message() ) );
		$res = $wpdb->query( "TRUNCATE TABLE " . esc_sql($wpdb->prefix) . "revslider_slides" );
		if ( is_wp_error( $res ) ) dfl( __( 'Failed truncate table "revslider_slides".', 'trx_addons' ) . ' ' . ( $res->get_error_message() ) );
		$res = $wpdb->query( "TRUNCATE TABLE " . esc_sql($wpdb->prefix) . "revslider_static_slides" );
		if ( is_wp_error( $res ) ) dfl( __( 'Failed truncate table "revslider_static_slides".', 'trx_addons' ) . ' ' . ( $res->get_error_message() ) );
	}
}

if ( ! function_exists( 'trx_addons_revslider_importer_import' ) ) {
	add_action( 'trx_addons_action_importer_import', 'trx_addons_revslider_importer_import', 10, 2 );
	/**
	 * Import Revolution Slider data via a core object RevSliderSliderImport
	 * 
	 * @hooked trx_addons_action_importer_import
	 *
	 * @param object $importer  Importer object
	 * @param string $action    Action to perform: 'import_revslider'
	 */
	function trx_addons_revslider_importer_import( $importer, $action ) {
		if ( trx_addons_exists_revslider() && in_array( 'revslider', $importer->options['required_plugins'] ) ) {
			if ( $action == 'import_revslider' && ! empty( $importer->options['files'][ $importer->options['demo_type'] ]['file_with_revslider'] ) ) {
				if ( file_exists( WP_PLUGIN_DIR . '/revslider/revslider.php' ) ) {
					require_once WP_PLUGIN_DIR . '/revslider/revslider.php';
					if ( $importer->options['debug'] ) dfl( __( 'Import Revolution sliders', 'trx_addons' ) );
					// Get last processed slider
					$last_arh = $importer->response['start_from_id'] = isset( $_POST['start_from_id'] ) ? $_POST['start_from_id'] : '';
					// Get list of the sliders
					if ( ( $txt = get_transient( 'trx_addons_importer_revsliders' ) ) == '' ) {
						if ( ( $txt = $importer->get_file( $importer->options['files'][ $importer->options['demo_type'] ]['file_with_revslider'] ) ) === false ) {
							return;
						} else {
							set_transient('trx_addons_importer_revsliders', $txt, 10 * 60);		// Store to the cache for 10 minutes
						}
					}
					$files = trx_addons_unserialize( $txt );
					if ( ! is_array( $files ) ) {
						$files = explode( "\n", str_replace( "\r\n", "\n", $files ) );
					}
					// Remove empty lines
					foreach ( $files as $k => $file ) {
						if ( trim( $file ) == '' ) {
							unset( $files[ $k ] );
						}
					}
					// Process next slider
					if ( class_exists( 'RevSliderSliderImport' ) ) {
						$slider = new RevSliderSliderImport();		// after v.6.0
					} else {
						$slider = new RevSlider();					// before v.6.0
					}
					// Process files
					$counter = 0;
					$result = 0;
					if ( ! is_array( $_FILES ) ) {
						$_FILES = array();
					}
					foreach ( $files as $file ) {
						$counter++;
						if ( ( $file = trim( $file ) ) == '' ) {
							continue;
						}
						if ( ! empty( $last_arh ) ) {
							if ( $file == $last_arh ) {
								$last_arh = '';
							}
							continue;
						}
						$need_del = false;
						// Load single file into system temp folder
						if ( ( $zip = $importer->download_file( $file, round( max( 0, $counter - 1 ) / count( $files ) * 100 ) ) ) != '' ) {
							$need_del = substr( $zip, 0, 5 ) == 'http:' || substr( $zip, 0, 6 ) == 'https:';
							$_FILES["import_file"] = array( "tmp_name" => $zip, 'error' => UPLOAD_ERR_OK );
							$response = class_exists( 'RevSliderSliderImport' )
											? $slider->import_slider()
											: $slider->importSliderFromPost();
							if ( $need_del && file_exists( $_FILES["import_file"]["tmp_name"] ) ) {
								unlink($_FILES["import_file"]["tmp_name"]);
							}
							if ( $response["success"] == false ) {
								$msg = sprintf( esc_html__( 'Revolution Slider "%s" import error.', 'trx_addons' ), $file );
								unset( $importer->response['attempt'] );
								$importer->response['error'] = $msg;
								if ( $importer->options['debug'] )  {
									dfl( $msg );
									dfo( $response );
								}
							} else {
								$importer->response['start_from_id'] = $file;
								$importer->response['result'] = min( 100, round( $counter / count( $files ) * 100 ) );
								if ( $importer->options['debug'] ) {
									dfl( sprintf( __( 'Slider "%s" imported', 'trx_addons' ), basename( $file ) ) );
								}
							}
						}
						break;
					}
					if ( $counter == count( $files ) ) {
						delete_transient( 'trx_addons_importer_revsliders' );
					}
				} else {
					if ( $importer->options['debug'] ) {
						dfl( sprintf( __( 'Can not locate plugin Revolution Slider: %s', 'trx_addons' ), WP_PLUGIN_DIR . '/revslider/revslider.php' ) );
					}
				}
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_revslider_importer_import_params' ) ) {
	add_action( 'trx_addons_action_importer_import', 'trx_addons_revslider_importer_import_params', 10, 2 );
	/**
	 * Correct url in params after import sliders
	 *
	 * @param TRX_Addons_Importer $importer  Importer object
	 * @param string $action                 Action to perform: 'import_revslider'
	 */
	function trx_addons_revslider_importer_import_params( $importer, $action ) {
		global $wpdb;
		if ( trx_addons_exists_revslider() && in_array( 'revslider', $importer->options['required_plugins'] ) ) {
			if ( $action == 'import_revslider' && ! empty( $importer->options['files'][ $importer->options['demo_type'] ]['file_with_revslider'] ) ) {
				$table = "{$wpdb->prefix}revslider_sliders";
				if ( count( $wpdb->get_results( $wpdb->prepare( "SHOW TABLES LIKE %s", $table ), ARRAY_A ) ) == 0 ) {
					if ( $importer->options['debug'] ) {
						dfl( sprintf( __( 'Table "%s" does not exists! Skip URL correction in the parameter "background_image".', 'trx_addons' ), $table ) );
					}
				} else {
					$rows = $wpdb->get_results( "SELECT * FROM {$table}", ARRAY_A );
					if ( is_array( $rows ) ) {
						foreach ( $rows as $k => $row ) {
							// Update slider params
							if ( ! empty( $row['params'] ) ) {
								$params = json_decode( $row['params'], true );
								$need_update = false;
								// Old versions of RevSlider
								if ( ! empty( $params['background_image'] ) ) {
									$params['background_image'] = $importer->replace_site_url( $importer->prepare_data( $params['background_image'] ) );
									$need_update = true;
								}
								// New versions of RevSlider
								if ( ! empty( $params['bg']['image'] ) ) {
									$params['bg']['image'] = $importer->replace_site_url( $importer->prepare_data( $params['bg']['image'] ) );
									$need_update = true;
								}
								if ( ! empty( $params['thumb']['customAdminThumbSrc'] ) ) {
									$params['thumb']['customAdminThumbSrc'] = $importer->replace_site_url( $importer->prepare_data( $params['thumb']['customAdminThumbSrc'] ) );
									$need_update = true;
								}
								if ( ! empty( $params['layout']['bg']['image'] ) ) {
									$params['layout']['bg']['image'] = $importer->replace_site_url( $importer->prepare_data( $params['layout']['bg']['image'] ) );
									$need_update = true;
								}
								// Update params
								if ( $need_update ) {
									$params = json_encode( $params );
									$wpdb->query( "UPDATE {$table} SET params = '" . esc_sql( $params ) . "' WHERE id = '" . esc_sql( $row['id'] ) . "'" );
								}
							}
							// Update slider layers
							if ( ! empty( $row['layers'] ) ) {
								$layers = json_decode( $row['layers'], true );
								$need_update = false;
								if ( is_array( $layers ) ) {
									foreach( $layers as $k => $v ) {
										if ( ! empty( $v['media']['imageUrl'] ) ) {
											$layers[$k]['media']['imageUrl'] = $importer->replace_site_url( $importer->prepare_data( $v['media']['imageUrl'] ) );
											$need_update = true;
										}
									}
								}
								// Update layers
								if ( $need_update ) {
									$layers = json_encode( $layers );
									$wpdb->query( "UPDATE {$table} SET layers = '" . esc_sql( $layers ) . "' WHERE id = '" . esc_sql( $row['id'] ) . "'" );
								}
							}
						}
					}
				}
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_revslider_importer_import_fields' ) ) {
	add_action( 'trx_addons_action_importer_import_fields',	'trx_addons_revslider_importer_import_fields', 10, 1 );
	/**
	 * Add a checkbox to the 'Import demo data' progress area
	 *
	 * @param TRX_Addons_Importer $importer  Importer object
	 */
	function trx_addons_revslider_importer_import_fields( $importer ) {
		if ( trx_addons_exists_revslider() && in_array( 'revslider', $importer->options['required_plugins'] ) ) {
			$importer->show_importer_fields( array(
				'slug'  => 'revslider', 
				'title' => esc_html__( 'Revolution Slider', 'trx_addons' )
				)
			);
		}
	}
}

if ( ! function_exists( 'trx_addons_revslider_importer_export' ) ) {
	add_action( 'trx_addons_action_importer_export', 'trx_addons_revslider_importer_export', 10, 1 );
	/**
	 * Export data from the plugin
	 *
	 * @param TRX_Addons_Importer $importer  Importer object
	 */
	function trx_addons_revslider_importer_export( $importer ) {
		$list = array_keys( trx_addons_get_list_revsliders() );
		$output = '';
		foreach ( $list as $alias ) {
			$output .= ( $output ? "\n" : '' ) . sprintf( "revslider/%s.zip", $alias );
		}
		trx_addons_fpc( $importer->export_file_dir( 'revslider.txt' ), $output );
	}
}

if ( ! function_exists( 'trx_addons_revslider_importer_export_fields' ) ) {
	//add_action( 'trx_addons_action_importer_export_fields', 'trx_addons_revslider_importer_export_fields', 10, 1 );
	/**
	 * Display a file name with exported data in the list of files to download
	 *
	 * @param TRX_Addons_Importer $importer  Importer object
	 */
	function trx_addons_revslider_importer_export_fields( $importer ) {
		$importer->show_exporter_fields( array(
			'slug'  => 'revslider',
			'title' => esc_html__( 'Revolution Sliders', 'trx_addons' )
		) );
	}
}
