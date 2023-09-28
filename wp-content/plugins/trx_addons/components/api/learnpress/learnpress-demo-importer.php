<?php
/**
 * Plugin support: LearnPress (Importer support)
 *
 * @package ThemeREX Addons
 * @since v1.6.62
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


if ( ! function_exists( 'trx_addons_learnpress_importer_required_plugins' ) ) {
	if ( is_admin() ) {
		add_filter( 'trx_addons_filter_importer_required_plugins', 'trx_addons_learnpress_importer_required_plugins', 10, 2 );
	}
	/**
	 * Check if the required plugins are installed
	 * 
	 * @hooked trx_addons_filter_importer_required_plugins
	 *
	 * @param string $not_installed Not installed plugins list
	 * @param string $list          Required plugins list
	 * 
	 * @return string               Not installed plugins list (with new plugins)
	 */
	function trx_addons_learnpress_importer_required_plugins( $not_installed = '', $list = '' ) {
		if ( strpos( $list, 'learnpress' ) !== false && ! trx_addons_exists_learnpress() ) {
			$not_installed .= '<br>' . esc_html__('learnpress', 'trx_addons');
		}
		return $not_installed;
	}
}

if ( ! function_exists( 'trx_addons_learnpress_importer_set_options' ) ) {
	if ( is_admin() ) {
		add_filter( 'trx_addons_filter_importer_options', 'trx_addons_learnpress_importer_set_options' );
	}
	/**
	 * Set plugin's specific importer options
	 * 
	 * @hooked trx_addons_filter_importer_options
	 *
	 * @param array $options Importer options
	 * 
	 * @return array         Modified options
	 */
	function trx_addons_learnpress_importer_set_options( $options = array() ) {
		if ( trx_addons_exists_learnpress() && in_array( 'learnpress', $options['required_plugins'] ) ) {
			$options['additional_options'][]	= 'learn_press_%';					// Add slugs to export options for this plugin

			if ( is_array( $options['files']) && count( $options['files'] ) > 0 ) {
				foreach ( $options['files'] as $k => $v ) {
					$options['files'][$k]['file_with_learnpress'] = str_replace( 'name.ext', 'learnpress.txt', $v['file_with_'] );
				}
			}
		}
		return $options;
	}
}

if ( ! function_exists( 'trx_addons_learnpress_importer_check_options' ) ) {
	add_filter( 'trx_addons_filter_import_theme_options', 'trx_addons_learnpress_importer_check_options', 10, 4 );
	/**
	 * Check if options will be imported
	 * 
	 * @hooked trx_addons_filter_import_theme_options
	 *
	 * @param boolean $allow    Allow import or not
	 * @param string  $k        Option name to import
	 * @param string  $v        Option value to import
	 * @param array   $options  Importer options
	 * 
	 * @return boolean          Allow import or not
	 */
	function trx_addons_learnpress_importer_check_options( $allow, $k, $v, $options ) {
		if ( $allow && strpos( $k, 'learn_press_' ) === 0 ) {
			$allow = trx_addons_exists_learnpress() && in_array( 'learnpress', $options['required_plugins'] );
		}
		return $allow;
	}
}

if ( ! function_exists( 'trx_addons_learnpress_importer_show_params' ) ) {
	if ( is_admin() ) {
		add_action( 'trx_addons_action_importer_params', 'trx_addons_learnpress_importer_show_params', 10, 1 );
	}
	/**
	 * Display a plugin name in the required plugins list on the Importer settings page
	 * 
	 * @hooked trx_addons_action_importer_params
	 *
	 * @param object $importer  Importer object
	 */
	function trx_addons_learnpress_importer_show_params( $importer ) {
		if ( trx_addons_exists_learnpress() && in_array( 'learnpress', $importer->options['required_plugins'] ) ) {
			$importer->show_importer_params( array(
				'slug' => 'learnpress',
				'title' => esc_html__('Import LearnPress', 'trx_addons'),
				'part' => 0
			) );
		}
	}
}

if ( ! function_exists( 'trx_addons_learnpress_importer_import' ) ) {
	if ( is_admin() ) {
		add_action( 'trx_addons_action_importer_import', 'trx_addons_learnpress_importer_import', 10, 2 );
	}
	/**
	 * Import LearnPress data
	 * 
	 * @hooked trx_addons_action_importer_import
	 *
	 * @param object $importer  Importer object
	 * @param string $action    Action to perform: 'import_learpress'
	 */
	function trx_addons_learnpress_importer_import( $importer, $action ) {
		if ( trx_addons_exists_learnpress() && in_array( 'learnpress', $importer->options['required_plugins'] ) ) {
			if ( $action == 'import_learnpress' ) {
				$importer->response['start_from_id'] = 0;
				$importer->import_dump( 'learnpress', esc_html__( 'LearPress meta', 'trx_addons' ) );
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_learnpress_importer_check_row' ) ) {
	if ( is_admin() ) {
		add_filter('trx_addons_filter_importer_import_row', 'trx_addons_learnpress_importer_check_row', 9, 4);
	}
	/**
	 * Check if the row will be imported
	 * 
	 * @hooked trx_addons_filter_importer_import_row
	 *
	 * @param boolean $flag   Allow import or not
	 * @param string  $table  Table name
	 * @param array   $row    Row data
	 * @param array   $list   List of required plugins
	 * 
	 * @return boolean        Allow import or not
	 */
	function trx_addons_learnpress_importer_check_row( $flag, $table, $row, $list ) {
		if ( $flag || strpos( $list, 'learnpress' ) === false ) {
			return $flag;
		}
		if ( trx_addons_exists_learnpress() ) {
			if ( $table == 'posts' ) {
				$flag = in_array( $row['post_type'], array( LP_COURSE_CPT, LP_LESSON_CPT, LP_QUESTION_CPT, LP_QUIZ_CPT, LP_ORDER_CPT ) );
			}
		}
		return $flag;
	}
}

if ( ! function_exists( 'trx_addons_learnpress_importer_import_fields' ) ) {
	if ( is_admin() ) {
		add_action( 'trx_addons_action_importer_import_fields',	'trx_addons_learnpress_importer_import_fields', 10, 1 );
	}
	/**
	 * Add a plugin's fields in the Importer's fields list
	 * 
	 * @hooked trx_addons_action_importer_import_fields
	 *
	 * @param object $importer  Importer object
	 */
	function trx_addons_learnpress_importer_import_fields($importer) {
		if ( trx_addons_exists_learnpress() && in_array('learnpress', $importer->options['required_plugins']) ) {
			$importer->show_importer_fields(array(
				'slug'=>'learnpress', 
				'title' => esc_html__('LearnPress meta', 'trx_addons')
			) );
		}
	}
}

if ( ! function_exists( 'trx_addons_learnpress_importer_clear_cache' ) ) {
	if ( is_admin() ) {
		add_action( 'trx_addons_action_importer_import_end', 'trx_addons_learnpress_importer_clear_cache', 10, 1 );
	}
	/**
	 * Clear LearnPress cache after the import
	 * 
	 * @hooked trx_addons_action_importer_import_end
	 *
	 * @param object $importer  Importer object
	 */
	function trx_addons_learnpress_importer_clear_cache( $importer ) {
		if ( trx_addons_exists_learnpress() && in_array( 'learnpress', $importer->options['required_plugins'] ) ) {
			global $wpdb;
			$thim_cache_table = apply_filters( 'trx_addons_filter_thim_cache_table', $wpdb->prefix . 'thim_cache' );
			if ( count( $wpdb->get_results( $wpdb->prepare( "SHOW TABLES LIKE %s", $thim_cache_table ), ARRAY_A ) ) == 1 ) {
				$wpdb->query( "DELETE FROM {$thim_cache_table} WHERE key_cache LIKE 'learn_press%'" );
			}
		}
	}
}


if ( ! function_exists( 'trx_addons_learnpress_importer_export' ) ) {
	if ( is_admin() ) {
		add_action( 'trx_addons_action_importer_export', 'trx_addons_learnpress_importer_export', 10, 1 );
	}
	/**
	 * Export LearnPress data
	 * 
	 * @hooked trx_addons_action_importer_export
	 * 
	 * @trigger trx_addons_filter_importer_export_tables
	 *
	 * @param object $importer  Importer object
	 */
	function trx_addons_learnpress_importer_export( $importer ) {
		if ( trx_addons_exists_learnpress() && in_array( 'learnpress', $importer->options['required_plugins'] ) ) {
			trx_addons_fpc( $importer->export_file_dir( 'learnpress.txt' ), serialize( apply_filters( 'trx_addons_filter_importer_export_tables', array(
				"learnpress_order_itemmeta"			=> $importer->export_dump("learnpress_order_itemmeta"),
				"learnpress_order_items"			=> $importer->export_dump("learnpress_order_items"),
				"learnpress_question_answermeta"	=> $importer->export_dump("learnpress_question_answermeta"),
				"learnpress_question_answers"		=> $importer->export_dump("learnpress_question_answers"),
				"learnpress_quiz_questions"			=> $importer->export_dump("learnpress_quiz_questions"),
				"learnpress_review_logs"			=> $importer->export_dump("learnpress_review_logs"),
				"learnpress_sections"				=> $importer->export_dump("learnpress_sections"),
				"learnpress_section_items"			=> $importer->export_dump("learnpress_section_items"),
				"learnpress_sessions"				=> $importer->export_dump("learnpress_sessions"),
				"learnpress_user_items"				=> $importer->export_dump("learnpress_user_items"),
				"learnpress_user_itemmeta"			=> $importer->export_dump("learnpress_user_itemmeta"),
				"learnpress_user_item_results"		=> $importer->export_dump("learnpress_user_item_results"),
				), 'learnpress' ) )
			);
		}
	}
}

if ( ! function_exists( 'trx_addons_learnpress_importer_export_fields' ) ) {
	if ( is_admin() ) {
		add_action( 'trx_addons_action_importer_export_fields',	'trx_addons_learnpress_importer_export_fields', 10, 1 );
	}
	/**
	 * Add a plugin's name to the Exporter's fields list
	 * 
	 * @hooked trx_addons_action_importer_export_fields
	 *
	 * @param object $importer  Importer object
	 */
	function trx_addons_learnpress_importer_export_fields( $importer ) {
		if ( trx_addons_exists_learnpress() && in_array( 'learnpress', $importer->options['required_plugins'] ) ) {
			$importer->show_exporter_fields( array(
				'slug'	=> 'learnpress',
				'title' => esc_html__('LearnPress', 'trx_addons')
			) );
		}
	}
}
