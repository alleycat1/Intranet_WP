<?php
/**
 * Plugin support: BBPress and BuddyPress (OCDI support)
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_ocdi_bbpress_set_options' ) ) {
	add_filter( 'trx_addons_filter_ocdi_options', 'trx_addons_ocdi_bbpress_set_options' );
	/**
	 * Set plugin's specific importer options
	 * 
	 * @hooked trx_addons_filter_ocdi_options
	 * 
	 * @param array $ocdi_options  OCDI plugin options
	 * 
	 * @return array 			 Modified options
	 */
	function trx_addons_ocdi_bbpress_set_options( $ocdi_options ){
		$ocdi_options['import_bbpress_file_url'] = 'bbpress.txt';
		return $ocdi_options;		
	}
}

if ( ! function_exists( 'trx_addons_ocdi_bbpress_export' ) ) {
	add_filter( 'trx_addons_filter_ocdi_export_files', 'trx_addons_ocdi_bbpress_export' );
	/**
	 * Export Buddy Press and BBPress data via OCDI
	 * 
	 * @hooked trx_addons_filter_ocdi_export_files
	 * 
	 * @param array $output  HTML layout with a list of exported files
	 * 
	 * @return array 		 Modified list
	 */
	function trx_addons_ocdi_bbpress_export($output){
		$list = array();
		if ( trx_addons_exists_bbpress() && in_array( 'bbpress', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			// Export tables
			$tables = array( 'bp_activity', 'bp_activity_meta', 'bp_friends', 'bp_groups', 'bp_groups_groupmeta', 'bp_groups_members', 'bp_messages_messages', 'bp_messages_meta', 'bp_messages_notices', 'bp_messages_recipients', 'bp_user_blogs', 'bp_user_blogs_blogmeta', 'bp_notifications', 'bp_notifications_meta', 'bp_xprofile_data', 'bp_xprofile_fields', 'bp_xprofile_groups', 'bp_xprofile_meta' );
			$list = trx_addons_ocdi_export_tables( $tables, $list );
			// Export options
			$options = array( 'bp-active-components', 'bp-pages', 'widget_bp_core_login_widget', 'widget_bp_core_members_widget', 'widget_bp_core_whos_online_widget', 'widget_bp_core_recently_active_widget', 'widget_bp_groups_widget', 'widget_bp_messages_sitewide_notices_widget', 'bp-deactivated-components', 'bb-config-location', 'bp-xprofile-base-group-name', 'bp-xprofile-fullname-field-name', 'hide-loggedout-adminbar', 'bp-disable-account-deletion', 'bp-disable-avatar-uploads', 'bp-disable-cover-image-uploads', 'bp-disable-profile-sync', 'bp_restrict_group_creation', 'bp-disable-group-avatar-uploads', 'bp-disable-group-cover-image-uploads', 'bp-disable-blogforum-comments', '_bp_enable_heartbeat_refresh' );
			$list = trx_addons_ocdi_export_options( $options, $list );
			// Serialize BuddyPress and BBPress data to the file
			$file_path = TRX_ADDONS_PLUGIN_OCDI . "export/bbpress.txt";
			trx_addons_fpc( trx_addons_get_file_dir( $file_path ), serialize( $list ) );
			
			// Return file path
			$output .= '<h4><a href="' . trx_addons_get_file_url( $file_path ) . '" download>' . esc_html__('BB Press & Buddy Press', 'trx_addons') . '</a></h4>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_bbpress_import_field' ) ) {
	add_filter( 'trx_addons_filter_ocdi_import_fields', 'trx_addons_ocdi_bbpress_import_field' );
	/**
	 * Add plugin to the import list
	 * 
	 * @hooked trx_addons_filter_ocdi_import_fields
	 * 
	 * @param array $output  HTML layout with a list of importer options
	 * 
	 * @return array 		 Modified list
	 */
	function trx_addons_ocdi_bbpress_import_field( $output ){
		$list = array();
		if ( trx_addons_exists_bbpress() && in_array( 'bbpress', trx_addons_ocdi_options( 'required_plugins' ) ) ) {
			$output .= '<label><input type="checkbox" name="bbpress" value="bbpress">' . esc_html__( 'BBPress and BuddyPress', 'trx_addons' ) . '</label><br/>';
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_ocdi_bbpress_import' ) ) {
	add_action( 'trx_addons_action_ocdi_import_plugins', 'trx_addons_ocdi_bbpress_import', 10, 1 );
	/**
	 * Import BuddyPress and BBPress data via OCDI
	 * 
	 * @hooked trx_addons_action_ocdi_import_plugins
	 * 
	 * @param array $import_plugins  List of plugins to import
	 */
	function trx_addons_ocdi_bbpress_import( $import_plugins ) {
		if ( trx_addons_exists_bbpress() && in_array( 'bbpress', $import_plugins ) ) {
			// Check if BuddyPress and BBPress tables are exists and recreate it (if need)
			trx_addons_bbpress_recreate_tables();
			trx_addons_ocdi_import_dump('bbpress');
			echo esc_html__('BBPress and BuddyPress import complete.', 'trx_addons') . "\r\n";
		}
	}
}
