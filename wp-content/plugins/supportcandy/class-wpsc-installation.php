<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Installation' ) ) :

	final class WPSC_Installation {

		/**
		 * Currently installed version
		 *
		 * @var string
		 */
		public static $current_version;

		/**
		 * Current database version
		 *
		 * @var string
		 */
		public static $current_db_version;

		/**
		 * For checking whether upgrade available or not
		 *
		 * @var boolean
		 */
		public static $is_upgrade = false;

		/**
		 * Initialize installation
		 */
		public static function init() {

			self::get_current_version();
			self::check_upgrade();

			// database upgrade available.
			if ( self::$current_version > 0 && self::$current_db_version < WPSC_DB_VERSION ) {

				define( 'WPSC_DB_UPGRADING', true );
				return;
			}

			// fresh installation or regular update.
			if ( self::$is_upgrade ) {

				define( 'WPSC_INSTALLING', true );

				// Do not allow parallel process to run.
				if ( 'yes' === get_transient( 'wpsc_installing' ) ) {
					return;
				}

				// Set transient.
				set_transient( 'wpsc_installing', 'yes', MINUTE_IN_SECONDS * 10 );

				// Create or update database tables.
				self::create_db_tables();

				// Run installation.
				if ( self::$current_version == 0 ) {

					add_action( 'init', array( __CLASS__, 'initial_setup' ), 1 );
					add_action( 'init', array( __CLASS__, 'set_upgrade_complete' ), 1 );

				} else {

					add_action( 'init', array( __CLASS__, 'upgrade' ), 1 );
				}

				// Delete transient.
				delete_transient( 'wpsc_installing' );
			}

			// Deactivate functionality.
			register_deactivation_hook( WPSC_PLUGIN_FILE, array( __CLASS__, 'deactivate' ) );
		}

		/**
		 * Check version
		 */
		public static function get_current_version() {

			self::$current_version    = get_option( 'wpsc_current_version', '0' );
			self::$current_db_version = get_option( 'wpsc_db_version', '1.0' );
		}

		/**
		 * Check for upgrade
		 */
		public static function check_upgrade() {

			if ( self::$current_version != WPSC_VERSION ) {
				self::$is_upgrade = true;
			}
		}

		/**
		 * Create database tables
		 */
		public static function create_db_tables() {

			global $wpdb;

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';

			$collate = '';
			if ( $wpdb->has_cap( 'collation' ) ) {
				$collate = $wpdb->get_charset_collate();
			}

			$tables = "
				CREATE TABLE {$wpdb->prefix}psmsc_tickets (
					id BIGINT NOT NULL AUTO_INCREMENT,
					is_active INT(1) NOT NULL DEFAULT '1',
					customer BIGINT NOT NULL,
					subject TEXT NOT NULL,
					status INT NOT NULL,
					priority INT NOT NULL,
					category INT NOT NULL,
					assigned_agent TEXT NULL,
					date_created DATETIME NOT NULL,
					date_updated DATETIME NOT NULL,
					agent_created INT DEFAULT NULL,
					ip_address VARCHAR(50) DEFAULT NULL,
					source VARCHAR(50) DEFAULT NULL,
					browser VARCHAR(50) DEFAULT NULL,
					os VARCHAR(50) DEFAULT NULL,
					add_recipients TEXT NULL,
					prev_assignee TEXT NULL,
					date_closed DATETIME DEFAULT NULL,
					user_type VARCHAR(100) NOT NULL,
					last_reply_on DATETIME DEFAULT NULL,
					last_reply_by BIGINT NOT NULL,
					auth_code VARCHAR(50) DEFAULT NULL,
					tags TINYTEXT NULL DEFAULT NULL,
					live_agents TINYTEXT NULL DEFAULT NULL,
					PRIMARY KEY (id)
				) $collate;
				CREATE TABLE {$wpdb->prefix}psmsc_customers (
					id BIGINT NOT NULL AUTO_INCREMENT,
					user BIGINT NOT NULL,
					ticket_count INT NOT NULL DEFAULT '0',
					name VARCHAR(200) NOT NULL,
					email VARCHAR(200) NOT NULL,
					PRIMARY KEY (id)
				) $collate;
				CREATE TABLE {$wpdb->prefix}psmsc_custom_fields (
					id INT NOT NULL AUTO_INCREMENT,
					name VARCHAR(200) NOT NULL,
					extra_info TEXT NOT NULL DEFAULT '',
					slug VARCHAR(200) NULL,
					field VARCHAR(50) NULL,
					type VARCHAR(100) NULL,
					default_value TEXT NULL,
					placeholder_text TEXT NULL,
					char_limit INT NULL,
					date_display_as VARCHAR(50) NULL,
					date_format VARCHAR(50) NULL,
					date_range VARCHAR(50) NULL,
					start_range DATETIME NULL,
					end_range DATETIME NULL,
					time_format INT NULL,
					is_personal_info INT(1) NOT NULL DEFAULT 0,
					is_auto_fill INT(1) NULL,
					allow_ticket_form INT(1) NULL DEFAULT 1,
					allow_my_profile INT(1) NULL DEFAULT 1,
					tl_width INT(3) NOT NULL DEFAULT 100,
					load_order INT NOT NULL DEFAULT 1,
					PRIMARY KEY (id)
				) $collate;
				CREATE TABLE {$wpdb->prefix}psmsc_options (
					id INT NOT NULL AUTO_INCREMENT,
					name VARCHAR(200) NOT NULL,
					custom_field INT NOT NULL DEFAULT 0,
					date_created DATETIME NOT NULL,
					load_order INT NOT NULL DEFAULT 1,
					PRIMARY KEY (id)
				) $collate;
				CREATE TABLE {$wpdb->prefix}psmsc_threads (
					id BIGINT NOT NULL AUTO_INCREMENT,
					ticket BIGINT NOT NULL,
					is_active INT(1) NOT NULL DEFAULT 1,
					customer BIGINT NULL DEFAULT NULL,
					type VARCHAR(50) NOT NULL,
					body LONGTEXT NOT NULL,
					attachments TEXT NULL DEFAULT NULL,
					ip_address VARCHAR(50) NULL DEFAULT NULL,
					source VARCHAR(50) NULL DEFAULT NULL,
					os VARCHAR(50) NULL DEFAULT NULL,
					browser VARCHAR(100) NULL DEFAULT NULL,
					seen DATETIME NULL DEFAULT NULL,
					date_created DATETIME NOT NULL,
					date_updated DATETIME NOT NULL,
					PRIMARY KEY (id)
				) $collate;
				CREATE TABLE {$wpdb->prefix}psmsc_statuses (
					id BIGINT NOT NULL AUTO_INCREMENT,
					name VARCHAR(200) NOT NULL,
					color VARCHAR(50) NOT NULL,
					bg_color VARCHAR(50) NOT NULL,
					load_order INT NOT NULL DEFAULT 1,
					PRIMARY KEY (id)
				) $collate;
				CREATE TABLE {$wpdb->prefix}psmsc_categories (
					id BIGINT NOT NULL AUTO_INCREMENT,
					name VARCHAR(200) NOT NULL,
					load_order INT NOT NULL DEFAULT 1,
					PRIMARY KEY (id)
				) $collate;
				CREATE TABLE {$wpdb->prefix}psmsc_priorities (
					id BIGINT NOT NULL AUTO_INCREMENT,
					name VARCHAR(200) NOT NULL,
					color VARCHAR(50) NOT NULL,
					bg_color VARCHAR(50) NOT NULL,
					load_order INT NOT NULL DEFAULT 1,
					PRIMARY KEY (id)
				) $collate;
				CREATE TABLE {$wpdb->prefix}psmsc_attachments (
					id BIGINT NOT NULL AUTO_INCREMENT,
					name VARCHAR(200) NOT NULL,
					file_path TEXT NOT NULL,
					is_image INT(1) NOT NULL DEFAULT 0,
					is_active INT(1) NOT NULL DEFAULT 0,
					is_uploaded INT(1) NOT NULL DEFAULT 0,
					date_created DATETIME NOT NULL,
					source VARCHAR(200) NOT NULL,
					source_id BIGINT NOT NULL DEFAULT 0,
					ticket_id BIGINT NOT NULL DEFAULT 0,
					customer_id BIGINT NOT NULL DEFAULT 0,
					PRIMARY KEY (id)
				) $collate;
				CREATE TABLE {$wpdb->prefix}psmsc_agents (
					id BIGINT NOT NULL AUTO_INCREMENT,
					user BIGINT NULL DEFAULT 0,
					customer BIGINT NULL DEFAULT 0,
					role INT NOT NULL DEFAULT 0,
					name VARCHAR(200) NOT NULL,
					workload INT NULL DEFAULT NULL,
					unresolved_count INT NULL DEFAULT NULL,
					is_agentgroup INT(1) NOT NULL DEFAULT 0,
					is_active INT(1) NOT NULL DEFAULT 0,
					PRIMARY KEY (id)
				) $collate;
				CREATE TABLE {$wpdb->prefix}psmsc_logs (
					id BIGINT NOT NULL AUTO_INCREMENT,
					type VARCHAR(200) NOT NULL,
					ref_id BIGINT NOT NULL,
					modified_by BIGINT NOT NULL,
					body LONGTEXT NOT NULL,
					date_created DATETIME NOT NULL,
					PRIMARY KEY (id)
				) $collate;
				CREATE TABLE {$wpdb->prefix}psmsc_working_hrs (
					id INT NOT NULL AUTO_INCREMENT,
					agent BIGINT NOT NULL,
					day TINYINT NOT NULL,
					start_time VARCHAR(20) NOT NULL,
					end_time VARCHAR(20) NOT NULL,
					PRIMARY KEY (id)
				) $collate;
				CREATE TABLE {$wpdb->prefix}psmsc_holidays (
					id INT NOT NULL AUTO_INCREMENT,
					agent BIGINT NOT NULL,
					holiday DATETIME NOT NULL,
					is_recurring TINYINT NOT NULL,
					PRIMARY KEY (id)
				) $collate;
				CREATE TABLE {$wpdb->prefix}psmsc_wh_exceptions (
					id INT NOT NULL AUTO_INCREMENT,
					agent BIGINT NOT NULL,
					title VARCHAR(200) NOT NULL,
					exception_date DATETIME NOT NULL,
					start_time VARCHAR(20) NOT NULL,
					end_time VARCHAR(20) NOT NULL,
					PRIMARY KEY (id)
				) $collate;
				CREATE TABLE {$wpdb->prefix}psmsc_email_otp (
					id INT NOT NULL AUTO_INCREMENT,
					email VARCHAR(200) NOT NULL,
					otp INT NOT NULL,
					date_expiry DATETIME NOT NULL,
					data LONGTEXT NULL DEFAULT NULL,
					PRIMARY KEY (id)
				) $collate;
				CREATE TABLE {$wpdb->prefix}psmsc_email_notifications (
					id INT NOT NULL AUTO_INCREMENT,
					from_name VARCHAR(200) NULL DEFAULT NULL,
					from_email VARCHAR(200) NULL DEFAULT NULL,
					reply_to VARCHAR(200) NULL DEFAULT NULL,
					subject TEXT NULL DEFAULT NULL,
					body LONGTEXT NULL DEFAULT NULL,
					to_email TEXT NULL DEFAULT NULL,
					cc_email TEXT NULL DEFAULT NULL,
					bcc_email TEXT NULL DEFAULT NULL,
					attachments TEXT NULL DEFAULT NULL,
					priority INT(1) NOT NULL DEFAULT 1,
					attempt INT(1) DEFAULT 0,
					PRIMARY KEY (id)
				) $collate;
				CREATE TABLE {$wpdb->prefix}psmsc_scheduled_tasks (
					id INT NOT NULL AUTO_INCREMENT,
					class VARCHAR(200) NOT NULL,
					method VARCHAR(200) NOT NULL,
					args TINYTEXT NULL DEFAULT NULL,
					is_manual INT(1) NOT NULL DEFAULT 0,
					warning_text TINYTEXT NULL DEFAULT NULL,
					warning_link_text TINYTEXT NULL DEFAULT NULL,
					progressbar_text TINYTEXT NULL DEFAULT NULL,
					pages INT NOT NULL DEFAULT 0,
					PRIMARY KEY (id)
				) $collate;
				CREATE TABLE {$wpdb->prefix}psmsc_ticket_tags (
					id INT NOT NULL AUTO_INCREMENT, 
					name VARCHAR(200) NOT NULL,
					description TINYTEXT NOT NULL,
					color VARCHAR(50) NOT NULL,
					bg_color VARCHAR(50) NOT NULL,
					PRIMARY KEY (id)
				) $collate;
			";

			dbDelta( $tables );
		}

		/**
		 * First time installation
		 */
		public static function initial_setup() {

			global $wpdb;

			$wpdb->query( 'SET auto_increment_increment = 1' );
			$wpdb->query( 'SET auto_increment_offset = 1' );

			// string translations.
			$string_translations = array();

			// Insert default custom fields.
			$name = esc_attr__( 'ID', 'supportcandy' );
			$wpdb->insert(
				$wpdb->prefix . 'psmsc_custom_fields',
				array(
					'name'       => $name,
					'slug'       => 'id',
					'field'      => 'ticket',
					'type'       => 'df_id',
					'tl_width'   => 50,
					'load_order' => 1,
				)
			);
			$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $name;

			$name = esc_attr__( 'Customer', 'supportcandy' );
			$extra_info = esc_attr__( 'Select customer for whom you wish to create a ticket', 'supportcandy' );
			$wpdb->insert(
				$wpdb->prefix . 'psmsc_custom_fields',
				array(
					'name'       => $name,
					'extra_info' => $extra_info,
					'slug'       => 'customer',
					'field'      => 'ticket',
					'type'       => 'df_customer',
					'load_order' => 2,
				)
			);
			$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $name;
			$string_translations[ 'wpsc-cf-exi-' . $wpdb->insert_id ] = $extra_info;

			$name = esc_attr__( 'Name', 'supportcandy' );
			$extra_info = esc_attr__( 'Please insert your name', 'supportcandy' );
			$wpdb->insert(
				$wpdb->prefix . 'psmsc_custom_fields',
				array(
					'name'             => $name,
					'extra_info'       => $extra_info,
					'slug'             => 'name',
					'field'            => 'customer',
					'type'             => 'df_customer_name',
					'tl_width'         => 150,
					'load_order'       => 3,
					'is_personal_info' => 1,
				)
			);
			$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $name;
			$string_translations[ 'wpsc-cf-exi-' . $wpdb->insert_id ] = $extra_info;

			$name = esc_attr__( 'Email Address', 'supportcandy' );
			$extra_info = esc_attr__( 'Please insert your email address', 'supportcandy' );
			$wpdb->insert(
				$wpdb->prefix . 'psmsc_custom_fields',
				array(
					'name'             => $name,
					'extra_info'       => $extra_info,
					'slug'             => 'email',
					'field'            => 'customer',
					'type'             => 'df_customer_email',
					'tl_width'         => 150,
					'load_order'       => 4,
					'is_personal_info' => 1,
				)
			);
			$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $name;
			$string_translations[ 'wpsc-cf-exi-' . $wpdb->insert_id ] = $extra_info;

			$name = esc_attr__( 'Subject', 'supportcandy' );
			$extra_info = esc_attr__( 'Short description of the ticket', 'supportcandy' );
			$wpdb->insert(
				$wpdb->prefix . 'psmsc_custom_fields',
				array(
					'name'          => $name,
					'extra_info'    => $extra_info,
					'slug'          => 'subject',
					'field'         => 'ticket',
					'type'          => 'df_subject',
					'tl_width'      => 200,
					'default_value' => 'Not Applicable',
					'load_order'    => 5,
				)
			);
			$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $name;
			$string_translations[ 'wpsc-cf-exi-' . $wpdb->insert_id ] = $extra_info;

			$name = esc_attr__( 'Description', 'supportcandy' );
			$extra_info = esc_attr__( 'Detailed description of the ticket', 'supportcandy' );
			$wpdb->insert(
				$wpdb->prefix . 'psmsc_custom_fields',
				array(
					'name'          => $name,
					'extra_info'    => $extra_info,
					'slug'          => 'description',
					'field'         => 'ticket',
					'type'          => 'df_description',
					'default_value' => 'Not Applicable',
					'load_order'    => 6,
				)
			);
			$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $name;
			$string_translations[ 'wpsc-cf-exi-' . $wpdb->insert_id ] = $extra_info;

			$name = esc_attr__( 'Status', 'supportcandy' );
			$extra_info = esc_attr__( 'Please select status', 'supportcandy' );
			$wpdb->insert(
				$wpdb->prefix . 'psmsc_custom_fields',
				array(
					'name'          => $name,
					'extra_info'    => $extra_info,
					'slug'          => 'status',
					'field'         => 'ticket',
					'type'          => 'df_status',
					'tl_width'      => 100,
					'default_value' => 1,
					'load_order'    => 7,
				)
			);
			$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $name;
			$string_translations[ 'wpsc-cf-exi-' . $wpdb->insert_id ] = $extra_info;

			$name = esc_attr__( 'Priority', 'supportcandy' );
			$extra_info = esc_attr__( 'Please select priority', 'supportcandy' );
			$wpdb->insert(
				$wpdb->prefix . 'psmsc_custom_fields',
				array(
					'name'          => $name,
					'extra_info'    => $extra_info,
					'slug'          => 'priority',
					'field'         => 'ticket',
					'type'          => 'df_priority',
					'tl_width'      => 100,
					'default_value' => 1,
					'load_order'    => 8,
				)
			);
			$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $name;
			$string_translations[ 'wpsc-cf-exi-' . $wpdb->insert_id ] = $extra_info;

			$name = esc_attr__( 'Category', 'supportcandy' );
			$extra_info = esc_attr__( 'Please select category', 'supportcandy' );
			$wpdb->insert(
				$wpdb->prefix . 'psmsc_custom_fields',
				array(
					'name'          => $name,
					'extra_info'    => $extra_info,
					'slug'          => 'category',
					'field'         => 'ticket',
					'type'          => 'df_category',
					'tl_width'      => 100,
					'default_value' => 1,
					'load_order'    => 9,
				)
			);
			$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $name;
			$string_translations[ 'wpsc-cf-exi-' . $wpdb->insert_id ] = $extra_info;

			$name = esc_attr__( 'Assignee', 'supportcandy' );
			$extra_info = esc_attr__( 'Please select an agent', 'supportcandy' );
			$wpdb->insert(
				$wpdb->prefix . 'psmsc_custom_fields',
				array(
					'name'       => $name,
					'extra_info' => $extra_info,
					'slug'       => 'assigned_agent',
					'field'      => 'ticket',
					'type'       => 'df_assigned_agent',
					'tl_width'   => 150,
					'load_order' => 10,
				)
			);
			$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $name;
			$string_translations[ 'wpsc-cf-exi-' . $wpdb->insert_id ] = $extra_info;

			$name = esc_attr__( 'Date Created', 'supportcandy' );
			$wpdb->insert(
				$wpdb->prefix . 'psmsc_custom_fields',
				array(
					'name'            => $name,
					'slug'            => 'date_created',
					'field'           => 'ticket',
					'type'            => 'df_date_created',
					'date_display_as' => 'date',
					'tl_width'        => 165,
					'load_order'      => 11,
				)
			);
			$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $name;

			$name = esc_attr__( 'Date Updated', 'supportcandy' );
			$wpdb->insert(
				$wpdb->prefix . 'psmsc_custom_fields',
				array(
					'name'            => $name,
					'slug'            => 'date_updated',
					'field'           => 'ticket',
					'type'            => 'df_date_updated',
					'date_display_as' => 'diff',
					'tl_width'        => 165,
					'load_order'      => 12,
				)
			);
			$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $name;

			$name = esc_attr__( 'Agent Created', 'supportcandy' );
			$wpdb->insert(
				$wpdb->prefix . 'psmsc_custom_fields',
				array(
					'name'       => $name,
					'slug'       => 'agent_created',
					'field'      => 'ticket',
					'type'       => 'df_agent_created',
					'tl_width'   => 100,
					'load_order' => 13,
				)
			);
			$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $name;

			$name = esc_attr__( 'IP Address', 'supportcandy' );
			$wpdb->insert(
				$wpdb->prefix . 'psmsc_custom_fields',
				array(
					'name'             => $name,
					'slug'             => 'ip_address',
					'field'            => 'ticket',
					'type'             => 'df_ip_address',
					'tl_width'         => 100,
					'load_order'       => 14,
					'is_personal_info' => 1,
				)
			);
			$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $name;

			$name = esc_attr__( 'Source', 'supportcandy' );
			$wpdb->insert(
				$wpdb->prefix . 'psmsc_custom_fields',
				array(
					'name'       => $name,
					'slug'       => 'source',
					'field'      => 'ticket',
					'type'       => 'df_source',
					'tl_width'   => 100,
					'load_order' => 15,
				)
			);
			$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $name;

			$name = esc_attr__( 'Browser', 'supportcandy' );
			$wpdb->insert(
				$wpdb->prefix . 'psmsc_custom_fields',
				array(
					'name'       => $name,
					'slug'       => 'browser',
					'field'      => 'ticket',
					'type'       => 'df_browser',
					'tl_width'   => 100,
					'load_order' => 16,
				)
			);
			$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $name;

			$name = esc_attr__( 'Operating System', 'supportcandy' );
			$wpdb->insert(
				$wpdb->prefix . 'psmsc_custom_fields',
				array(
					'name'       => $name,
					'slug'       => 'os',
					'field'      => 'ticket',
					'type'       => 'df_os',
					'tl_width'   => 100,
					'load_order' => 17,
				)
			);
			$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $name;

			$name = esc_attr__( 'Additional Recipients', 'supportcandy' );
			$extra_info = esc_attr__( 'Please insert email addresses (one per line)', 'supportcandy' );
			$wpdb->insert(
				$wpdb->prefix . 'psmsc_custom_fields',
				array(
					'name'             => $name,
					'extra_info'       => $extra_info,
					'slug'             => 'add_recipients',
					'field'            => 'ticket',
					'type'             => 'df_add_recipients',
					'is_personal_info' => 1,
					'load_order'       => 18,
				)
			);
			$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $name;
			$string_translations[ 'wpsc-cf-exi-' . $wpdb->insert_id ] = $extra_info;

			$name = esc_attr__( 'Previous Assignee', 'supportcandy' );
			$wpdb->insert(
				$wpdb->prefix . 'psmsc_custom_fields',
				array(
					'name'             => $name,
					'slug'             => 'prev_assignee',
					'field'            => 'ticket',
					'type'             => 'df_prev_assignee',
					'tl_width'         => 100,
					'is_personal_info' => 0,
					'load_order'       => 19,
				)
			);
			$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $name;

			$name = esc_attr__( 'Date Closed', 'supportcandy' );
			$wpdb->insert(
				$wpdb->prefix . 'psmsc_custom_fields',
				array(
					'name'             => $name,
					'slug'             => 'date_closed',
					'field'            => 'ticket',
					'type'             => 'df_date_closed',
					'date_display_as'  => 'date',
					'is_personal_info' => 0,
					'tl_width'         => 165,
					'load_order'       => 20,
				)
			);
			$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $name;

			$name = esc_attr__( 'User Type', 'supportcandy' );
			$wpdb->insert(
				$wpdb->prefix . 'psmsc_custom_fields',
				array(
					'name'             => $name,
					'slug'             => 'user_type',
					'field'            => 'ticket',
					'type'             => 'df_user_type',
					'tl_width'         => 100,
					'is_personal_info' => 0,
					'load_order'       => 21,
				)
			);
			$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $name;

			$name = esc_attr__( 'Last Reply On', 'supportcandy' );
			$wpdb->insert(
				$wpdb->prefix . 'psmsc_custom_fields',
				array(
					'name'             => $name,
					'slug'             => 'last_reply_on',
					'field'            => 'ticket',
					'type'             => 'df_last_reply_on',
					'date_display_as'  => 'date',
					'is_personal_info' => 0,
					'tl_width'         => 165,
					'load_order'       => 22,
				)
			);
			$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $name;

			$name = esc_attr__( 'Last Reply By', 'supportcandy' );
			$wpdb->insert(
				$wpdb->prefix . 'psmsc_custom_fields',
				array(
					'name'             => $name,
					'slug'             => 'last_reply_by',
					'field'            => 'ticket',
					'type'             => 'df_last_reply_by',
					'is_personal_info' => 0,
					'tl_width'         => 100,
					'load_order'       => 23,
				)
			);
			$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $name;

			$name = esc_attr__( 'Tags', 'supportcandy' );
			$wpdb->insert(
				$wpdb->prefix . 'psmsc_custom_fields',
				array(
					'name'             => $name,
					'slug'             => 'tags',
					'field'            => 'ticket',
					'type'             => 'df_tags',
					'is_personal_info' => 0,
					'tl_width'         => 100,
					'load_order'       => 24,
				)
			);
			$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $name;

			// Insert default category.
			$name = esc_attr__( 'General', 'supportcandy' );
			$wpdb->insert(
				$wpdb->prefix . 'psmsc_categories',
				array(
					'name'       => $name,
					'load_order' => 1,
				)
			);
			$string_translations[ 'wpsc-category-' . $wpdb->insert_id ] = $name;

			// Insert default statuses.
			$name = esc_attr__( 'Open', 'supportcandy' );
			$wpdb->insert(
				$wpdb->prefix . 'psmsc_statuses',
				array(
					'name'       => $name,
					'color'      => '#ec1010',
					'bg_color'   => '#ffe7e1',
					'load_order' => 1,
				)
			);
			$string_translations[ 'wpsc-status-' . $wpdb->insert_id ] = $name;

			$name = esc_attr__( 'Awaiting customer reply', 'supportcandy' );
			$wpdb->insert(
				$wpdb->prefix . 'psmsc_statuses',
				array(
					'name'       => $name,
					'color'      => '#8222E6',
					'bg_color'   => '#E9D3FF',
					'load_order' => 2,
				)
			);
			$string_translations[ 'wpsc-status-' . $wpdb->insert_id ] = $name;

			$name = esc_attr__( 'Awaiting agent reply', 'supportcandy' );
			$wpdb->insert(
				$wpdb->prefix . 'psmsc_statuses',
				array(
					'name'       => $name,
					'color'      => '#EB961C',
					'bg_color'   => '#FFEBCE',
					'load_order' => 3,
				)
			);
			$string_translations[ 'wpsc-status-' . $wpdb->insert_id ] = $name;

			$name = esc_attr__( 'Closed', 'supportcandy' );
			$wpdb->insert(
				$wpdb->prefix . 'psmsc_statuses',
				array(
					'name'       => $name,
					'color'      => '#22940d',
					'bg_color'   => '#c1ffcf',
					'load_order' => 4,
				)
			);
			$string_translations[ 'wpsc-status-' . $wpdb->insert_id ] = $name;

			// Insert default priorities.
			$name = esc_attr__( 'Low', 'supportcandy' );
			$wpdb->insert(
				$wpdb->prefix . 'psmsc_priorities',
				array(
					'name'       => $name,
					'color'      => '#22940d',
					'bg_color'   => '#c1ffcf',
					'load_order' => 1,
				)
			);
			$string_translations[ 'wpsc-priority-' . $wpdb->insert_id ] = $name;

			$name = esc_attr__( 'Medium', 'supportcandy' );
			$wpdb->insert(
				$wpdb->prefix . 'psmsc_priorities',
				array(
					'name'       => $name,
					'color'      => '#EB961C',
					'bg_color'   => '#FFEBCE',
					'load_order' => 2,
				)
			);
			$string_translations[ 'wpsc-priority-' . $wpdb->insert_id ] = $name;

			$name = esc_attr__( 'High', 'supportcandy' );
			$wpdb->insert(
				$wpdb->prefix . 'psmsc_priorities',
				array(
					'name'       => $name,
					'color'      => '#ec1010',
					'bg_color'   => '#ffe7e1',
					'load_order' => 3,
				)
			);
			$string_translations[ 'wpsc-priority-' . $wpdb->insert_id ] = $name;

			// company working hrs.
			for ( $i = 1; $i <= 7; $i++ ) {
				$wpdb->insert(
					$wpdb->prefix . 'psmsc_working_hrs',
					array(
						'agent'      => 0,
						'day'        => $i,
						'start_time' => '00:00:00',
						'end_time'   => '23:59:59',
					)
				);
			}

			// Agent roles.
			update_option(
				'wpsc-agent-roles',
				array(
					1 => array(
						'label' => 'Administrator',
						'caps'  => array(

							'view-unassigned'       => true, // View ticket.
							'view-assigned-me'      => true,
							'view-assigned-others'  => true,

							'reply-unassigned'      => true, // Reply ticket.
							'reply-assigned-me'     => true,
							'reply-assigned-others' => true,

							'pn-unassigned'         => true, // Private notes.
							'pn-assigned-me'        => true,
							'pn-assigned-others'    => true,

							'aa-unassigned'         => true, // Assignee.
							'aa-assigned-me'        => true,
							'aa-assigned-others'    => true,

							'cs-unassigned'         => true, // Change status.
							'cs-assigned-me'        => true,
							'cs-assigned-others'    => true,

							'ctf-unassigned'        => true, // Change ticket fields.
							'ctf-assigned-me'       => true,
							'ctf-assigned-others'   => true,

							'caof-unassigned'       => true, // Change agent only fields.
							'caof-assigned-me'      => true,
							'caof-assigned-others'  => true,

							'crb-unassigned'        => true, // Change raised by.
							'crb-assigned-me'       => true,
							'crb-assigned-others'   => true,

							'eth-unassigned'        => true, // Edit thread.
							'eth-assigned-me'       => true,
							'eth-assigned-others'   => true,

							'dth-unassigned'        => true, // Delete thread.
							'dth-assigned-me'       => true,
							'dth-assigned-others'   => true,

							'vl-unassigned'         => true, // View logs.
							'vl-assigned-me'        => true,
							'vl-assigned-others'    => true,

							'dtt-unassigned'        => true, // Delete ticket.
							'dtt-assigned-me'       => true,
							'dtt-assigned-others'   => true,

							'dt-unassigned'         => true, // Duplicate ticket.
							'dt-assigned-me'        => true,
							'dt-assigned-others'    => true,

							'ar-unassigned'         => true, // Additional recipients.
							'ar-assigned-me'        => true,
							'ar-assigned-others'    => true,

							'backend-access'        => true, // Dashboard support menu access.
							'create-as'             => true, // Create ticket on others behalf.
							'dtt-access'            => true, // Deleted ticket access.
							'eci-access'            => true, // Edit customer info.

							'tt-unassigned'         => true, // Ticket tags.
							'tt-assigned-me'        => true,
							'tt-assigned-others'    => true,
						),
					),
					2 => array(
						'label' => 'Agent',
						'caps'  => array(

							'view-unassigned'       => true,
							'view-assigned-me'      => true,
							'view-assigned-others'  => false,

							'reply-unassigned'      => true,
							'reply-assigned-me'     => true,
							'reply-assigned-others' => false,

							'pn-unassigned'         => true,
							'pn-assigned-me'        => true,
							'pn-assigned-others'    => false,

							'aa-unassigned'         => true,
							'aa-assigned-me'        => true,
							'aa-assigned-others'    => false,

							'cs-unassigned'         => true,
							'cs-assigned-me'        => true,
							'cs-assigned-others'    => false,

							'ctf-unassigned'        => true,
							'ctf-assigned-me'       => true,
							'ctf-assigned-others'   => false,

							'caof-unassigned'       => true,
							'caof-assigned-me'      => true,
							'caof-assigned-others'  => false,

							'crb-unassigned'        => false,
							'crb-assigned-me'       => false,
							'crb-assigned-others'   => false,

							'eth-unassigned'        => false,
							'eth-assigned-me'       => false,
							'eth-assigned-others'   => false,

							'dth-unassigned'        => false,
							'dth-assigned-me'       => false,
							'dth-assigned-others'   => false,

							'vl-unassigned'         => true,
							'vl-assigned-me'        => true,
							'vl-assigned-others'    => true,

							'dtt-unassigned'        => false,
							'dtt-assigned-me'       => false,
							'dtt-assigned-others'   => false,

							'dt-unassigned'         => true,
							'dt-assigned-me'        => true,
							'dt-assigned-others'    => true,

							'ar-unassigned'         => true,
							'ar-assigned-me'        => true,
							'ar-assigned-others'    => false,

							'backend-access'        => true,
							'create-as'             => true,
							'dtt-access'            => false,
							'eci-access'            => false,

							'tt-unassigned'         => true, // Ticket tags.
							'tt-assigned-me'        => true,
							'tt-assigned-others'    => false,
						),
					),
				)
			);

			// default agents.
			$users = get_users( array( 'role' => 'administrator' ) );
			foreach ( $users as $user ) {
				// customer record.
				$wpdb->insert(
					$wpdb->prefix . 'psmsc_customers',
					array(
						'user'  => $user->ID,
						'name'  => $user->display_name,
						'email' => $user->user_email,
					)
				);
				// agent record.
				$wpdb->insert(
					$wpdb->prefix . 'psmsc_agents',
					array(
						'user'      => $user->ID,
						'customer'  => $wpdb->insert_id,
						'role'      => 1,
						'name'      => $user->display_name,
						'is_active' => 1,
					)
				);
				// agent working hrs.
				$agent_id = $wpdb->insert_id;
				for ( $i = 1; $i <= 7; $i++ ) {
					$wpdb->insert(
						$wpdb->prefix . 'psmsc_working_hrs',
						array(
							'agent'      => $agent_id,
							'day'        => $i,
							'start_time' => '00:00:00',
							'end_time'   => '23:59:59',
						)
					);
				}
				if ( ! is_multisite() && ! $user->has_cap( 'wpsc_agent' ) ) {
					$user->add_cap( 'wpsc_agent' );
				}
			}

			// ticket form fields.
			update_option(
				'wpsc-tff',
				array(
					'name'        => array(
						'is-required' => 1,
						'width'       => 'half',
						'visibility'  => '',
					),
					'email'       => array(
						'is-required' => 1,
						'width'       => 'half',
						'visibility'  => '',
					),
					'subject'     => array(
						'is-required' => 1,
						'width'       => 'full',
						'visibility'  => '',
						'relation'    => 'AND',
					),
					'description' => array(
						'is-required' => 1,
						'width'       => 'full',
						'visibility'  => '',
						'relation'    => 'AND',
					),
					'category'    => array(
						'is-required' => 1,
						'width'       => 'half',
						'visibility'  => '',
						'relation'    => 'AND',
					),
				)
			);

			// agent ticket list.
			update_option(
				'wpsc-atl-list-items',
				array( 'id', 'status', 'subject', 'name', 'category', 'priority', 'assigned_agent', 'date_updated' )
			);
			update_option(
				'wpsc-atl-filter-items',
				array( 'id', 'status', 'customer', 'subject', 'category', 'priority', 'assigned_agent', 'date_updated', 'date_created' )
			);
			$labels = array(
				'all'        => esc_attr__( 'All', 'supportcandy' ),
				'unresolved' => esc_attr__( 'Unresolved', 'supportcandy' ),
				'unassigned' => esc_attr__( 'Unassigned', 'supportcandy' ),
				'mine'       => esc_attr__( 'Mine', 'supportcandy' ),
				'closed'     => esc_attr__( 'Closed', 'supportcandy' ),
				'deleted'    => esc_attr__( 'Deleted', 'supportcandy' ),
			);
			foreach ( $labels as $key => $string ) {
				$string_translations[ 'wpsc-atl-' . $key ] = $string;
			}
			update_option(
				'wpsc-atl-default-filters',
				array(
					'all'        => array(
						'label'     => $labels['all'],
						'is_enable' => 1,
					),
					'unresolved' => array(
						'label'     => $labels['unresolved'],
						'is_enable' => 1,
					),
					'unassigned' => array(
						'label'     => $labels['unassigned'],
						'is_enable' => 1,
					),
					'mine'       => array(
						'label'     => $labels['mine'],
						'is_enable' => 1,
					),
					'closed'     => array(
						'label'     => $labels['closed'],
						'is_enable' => 1,
					),
					'deleted'    => array(
						'label'     => $labels['deleted'],
						'is_enable' => 1,
					),
				)
			);

			// customer ticket list.
			update_option(
				'wpsc-ctl-list-items',
				array( 'id', 'status', 'subject', 'category', 'date_created', 'date_updated' )
			);
			update_option(
				'wpsc-ctl-filter-items',
				array( 'id', 'status', 'subject', 'category', 'date_updated', 'date_created' )
			);
			$labels = array(
				'all'        => esc_attr__( 'All', 'supportcandy' ),
				'unresolved' => esc_attr__( 'Unresolved', 'supportcandy' ),
				'closed'     => esc_attr__( 'Closed', 'supportcandy' ),
			);
			foreach ( $labels as $key => $string ) {
				$string_translations[ 'wpsc-ctl-' . $key ] = $string;
			}
			update_option(
				'wpsc-ctl-default-filters',
				array(
					'all'        => array(
						'label'     => $labels['all'],
						'is_enable' => 1,
					),
					'unresolved' => array(
						'label'     => $labels['unresolved'],
						'is_enable' => 1,
					),
					'closed'     => array(
						'label'     => $labels['closed'],
						'is_enable' => 1,
					),
				)
			);

			// ticket list more settings.
			update_option(
				'wpsc-tl-ms-agent-view',
				array(
					'default-sort-by'            => 'date_updated',
					'default-sort-order'         => 'DESC',
					'number-of-tickets'          => 20,
					'unresolved-ticket-statuses' => array( 1, 2, 3 ),
					'default-filter'             => 'all',
					'ticket-reply-redirect'      => 'no-redirect',
				)
			);
			update_option(
				'wpsc-tl-ms-customer-view',
				array(
					'default-sort-by'            => 'date_updated',
					'default-sort-order'         => 'DESC',
					'number-of-tickets'          => 20,
					'unresolved-ticket-statuses' => array( 1, 2, 3 ),
					'default-filter'             => 'all',
					'ticket-reply-redirect'      => 'no-redirect',
				)
			);
			update_option(
				'wpsc-tl-ms-advanced',
				array(
					'closed-ticket-statuses'   => array( 4 ),
					'auto-refresh-list-status' => 0,
				)
			);

			// email notifications.
			update_option(
				'wpsc-en-general',
				array(
					'from-name'                   => '',
					'from-email'                  => '',
					'reply-to'                    => '',
					'cron-email-count'            => 5,
					'blocked-emails'              => array(),
					'attachments-in-notification' => 'actual-files',
				)
			);
			$translations = array(
				1 => array(
					'subject' => 'Your ticket has been created successfully!',
					'body'    => '<p>Thanks for reaching out, we\'ve received your request!</p>',
				),
				2 => array(
					'body' => '<p>You have received a new ticket!</p><p><strong>{{customer}}</strong><em>reported</em></p><p>{{description}}</p><p>{{ticket_url}}</p>',
				),
				3 => array(
					'body' => '<p><strong>{{last_reply_user_name}}</strong> <em>replied</em></p><p>{{last_reply}}</p><p>{{ticket_url}}</p><p>{{ticket_history}}</p>',
				),
				4 => array(
					'subject' => 'Your ticket has been closed!',
					'body'    => '<p>Dear {{customer}},</p><p>Your ticket #{{id}} has been closed.</p>',
				),
			);
			foreach ( $translations as $index => $translation ) {
				if ( isset( $translation['subject'] ) ) {
					$string_translations[ 'wpsc-en-tn-subject-' . $index ] = $translation['subject'];
				}
				$string_translations[ 'wpsc-en-tn-body-' . $index ] = $translation['body'];
			}
			update_option(
				'wpsc-email-templates',
				array(
					'1' => array(
						'title'      => 'New ticket customer confirmation',
						'event'      => 'create-ticket',
						'is_enable'  => 1,
						'subject'    => $translations[1]['subject'],
						'body'       => array(
							'text'   => $translations[1]['body'],
							'editor' => 'html',
						),
						'to'         => array(
							'general-recipients' => array( 'customer' ),
							'agent-roles'        => array(),
							'custom'             => array(),
						),
						'cc'         => array(
							'general-recipients' => array(),
							'agent-roles'        => array(),
							'custom'             => array(),
						),
						'bcc'        => array(
							'general-recipients' => array(),
							'agent-roles'        => array(),
							'custom'             => array(),
						),
						'conditions' => '',
					),
					'2' => array(
						'title'      => 'New ticket staff notification',
						'event'      => 'create-ticket',
						'is_enable'  => 1,
						'subject'    => '{{subject}}',
						'body'       => array(
							'text'   => $translations[2]['body'],
							'editor' => 'html',
						),
						'to'         => array(
							'general-recipients' => array( 'assignee' ),
							'agent-roles'        => array( 1 ),
							'custom'             => array(),
						),
						'cc'         => array(
							'general-recipients' => array(),
							'agent-roles'        => array(),
							'custom'             => array(),
						),
						'bcc'        => array(
							'general-recipients' => array(),
							'agent-roles'        => array(),
							'custom'             => array(),
						),
						'conditions' => '',
					),
					'3' => array(
						'title'      => 'Reply ticket notification',
						'event'      => 'reply-ticket',
						'is_enable'  => 1,
						'subject'    => '{{subject}}',
						'body'       => array(
							'text'   => $translations[3]['body'],
							'editor' => 'html',
						),
						'to'         => array(
							'general-recipients' => array( 'customer', 'assignee' ),
							'agent-roles'        => array( 1 ),
							'custom'             => array(),
						),
						'cc'         => array(
							'general-recipients' => array(),
							'agent-roles'        => array(),
							'custom'             => array(),
						),
						'bcc'        => array(
							'general-recipients' => array(),
							'agent-roles'        => array(),
							'custom'             => array(),
						),
						'conditions' => '',
					),
					'4' => array(
						'title'      => 'Close ticket customer notification',
						'event'      => 'change-ticket-status',
						'is_enable'  => 1,
						'subject'    => $translations[4]['subject'],
						'body'       => array(
							'text'   => $translations[4]['body'],
							'editor' => 'html',
						),
						'to'         => array(
							'general-recipients' => array( 'customer' ),
							'agent-roles'        => array(),
							'custom'             => array(),
						),
						'cc'         => array(
							'general-recipients' => array(),
							'agent-roles'        => array(),
							'custom'             => array(),
						),
						'bcc'        => array(
							'general-recipients' => array(),
							'agent-roles'        => array(),
							'custom'             => array(),
						),
						'conditions' => '[[{"slug":"status","operator":"=","operand_val_1":"4"}]]',
					),
				)
			);

			// user registration otp.
			$subject = 'User registration OTP - {{otp}}';
			$body = '<p>Hello {{firstname}},</p><p>Below is your one time password for user registration:</p><p><strong>{{otp}}</strong></p>';
			update_option(
				'wpsc-en-user-reg',
				array(
					'subject' => $subject,
					'body'    => $body,
					'editor'  => 'html',
				)
			);
			$string_translations['wpsc-user-otp-subject'] = $subject;
			$string_translations['wpsc-user-otp-body'] = $body;

			// guest login otp.
			$subject = 'Guest Login - {{otp}}';
			$body = '<p>Hello {{name}},</p><p>Below is your one time password for guest login:</p><p><strong>{{otp}}</strong></p>';
			update_option(
				'wpsc-en-guest-login',
				array(
					'subject' => $subject,
					'body'    => $body,
					'editor'  => 'html',
				)
			);
			$string_translations['wpsc-guest-otp-subject'] = $subject;
			$string_translations['wpsc-guest-otp-body'] = $body;

			// General settings.
			update_option(
				'wpsc-gs-general',
				array(
					'ticket-status-after-customer-reply' => 3,
					'ticket-status-after-agent-reply'    => 2,
					'close-ticket-status'                => 4,
					'reply-form-position'                => 'top',
					'default-date-format'                => 'Y-m-d H:i:s',
					'ticket-alice'                       => 'Ticket #',
					'allow-close-ticket'                 => array( 'customer', 1, 2 ),
					'allow-ar-thread-email'              => array( 1, 2 ),
					'allow-create-ticket'                => array( 'registered-user', 1, 2 ),
					'allowed-search-fields'              => array( 'id', 'customer', 'subject', 'threads' ),
				)
			);

			update_option(
				'wpsc-gs-page-settings',
				array(
					'support-page'            => 0,
					'open-ticket-page'        => 0,
					'ticket-url-page'         => 'support-page',
					'new-ticket-page'         => 'default',
					'new-ticket-url'          => '',
					'user-login'              => 'default',
					'custom-login-url'        => '',
					'user-registration'       => 'disable',
					'custom-registration-url' => '',
					'otp-login'               => 1,
					'load-scripts'            => 'all-pages',
					'load-script-pages'       => array(),
				)
			);

			update_option(
				'wpsc-gs-file-attachments',
				array(
					'attachments-max-filesize' => 20,
					'allowed-file-extensions'  => 'jpg, jpeg, png, gif, pdf, doc, docx, ppt, pptx, pps, ppsx, odt, xls, xlsx, mp3, m4a, ogg, wav, mp4, m4v, mov, wmv, avi, mpg, ogv, 3gp, 3g2, zip, eml',
					'image-download-behaviour' => 'open-browser',
				)
			);

			$translation = '<p>Thanks for reaching out, we\'ve received your request!</p><p>{{ticket_url}}</p>';
			$string_translations['wpsc-thankyou-html'] = $translation;
			$string_translations['wpsc-thankyou-html-agent'] = $translation;
			update_option(
				'wpsc-gs-thankyou-page-settings',
				array(
					'action-agent'      => 'text',
					'action-customer'   => 'text',
					'html-agent'        => $translation,
					'html-customer'     => $translation,
					'page-url-agent'    => '',
					'page-url-customer' => '',
					'editor-agent'      => 'html',
					'editor-customer'   => 'html',
				)
			);

			// misc settings.
			$translation = '<p>I understand my personal information like Name, Email address, IP address, etc. will be stored in database.</p>';
			$string_translations['wpsc-gdpr'] = $translation;
			update_option(
				'wpsc-gdpr-settings',
				array(
					'allow-gdpr'                   => 1,
					'gdpr-text'                    => $translation,
					'personal-data-retention-time' => 0,
					'personal-data-retention-unit' => 'days',
					'editor'                       => 'html',
				)
			);

			update_option(
				'wpsc-recaptcha-settings',
				array(
					'allow-recaptcha'      => 0,
					'recaptcha-version'    => 3,
					'recaptcha-site-key'   => '',
					'recaptcha-secret-key' => '',
				)
			);

			update_option(
				'wpsc-ms-advanced-settings',
				array(
					'public-mode'                   => 0,
					'public-mode-reply'             => 0,
					'reply-confirmation'            => 1,
					'thread-date-display-as'        => 'diff',
					'thread-date-format'            => 'F d, Y h:i A',
					'do-not-notify-owner'           => 1,
					'do-not-notify-owner-status'    => 1,
					'ticket-id-format'              => 'sequential',
					'starting-ticket-id'            => 1,
					'random-id-length'              => 8,
					'ticket-history-macro-threads'  => 5,
					'register-user-if-not-exist'    => 0,
					'auto-delete-tickets-time'      => 0,
					'auto-delete-tickets-unit'      => 'days',
					'permanent-delete-tickets-time' => 0,
					'permanent-delete-tickets-unit' => 'days',
					'allow-bcc'                     => 0,
					'allow-cc'                      => 0,
					'view-more'                     => 1,
					'allow-reply-to-close-ticket'   => array( 'customer', 'agent' ),
					'raised-by-user'                => 'customer',
					'allow-my-profile'              => 1,
					'allow-agent-profile'           => 1,
					'ticket-url-auth'               => 0,
					'rest-api'                      => 1,
					'agent-collision'               => 1,
				)
			);

			$translation = '<p>I agree to the terms and conditions</p>';
			$string_translations['wpsc-term-and-conditions'] = $translation;
			update_option(
				'wpsc-term-and-conditions',
				array(
					'allow-term-and-conditions' => 1,
					'tandc-text'                => $translation,
					'editor'                    => 'html',
				)
			);

			// ticket widgets.
			$labels = array(
				'change-status'         => esc_attr__( 'Ticket status', 'supportcandy' ),
				'raised-by'             => esc_attr__( 'Customer', 'supportcandy' ),
				'ticket-info'           => esc_attr__( 'Ticket info', 'supportcandy' ),
				'assignee'              => esc_attr__( 'Assignee', 'supportcandy' ),
				'ticket-fields'         => esc_attr__( 'Ticket fields', 'supportcandy' ),
				'agentonly-fields'      => esc_attr__( 'Agent only fields', 'supportcandy' ),
				'additional-recipients' => esc_attr__( 'Additional recipients', 'supportcandy' ),
				'biographical-info'     => esc_attr__( 'Biographical Info', 'supportcandy' ),
				'tags'                  => esc_attr__( 'Tags', 'supportcandy' ),
			);
			foreach ( $labels as $key => $string ) {
				$string_translations[ 'wpsc-twt-' . $key ] = $string;
			}
			update_option(
				'wpsc-ticket-widget',
				array(
					'change-status'         => array(
						'title'                     => $labels['change-status'],
						'is_enable'                 => 1,
						'allow-customer'            => 1,
						'allowed-agent-roles'       => array( 1, 2 ),
						'show-priority-to-customer' => 0,
						'callback'                  => 'wpsc_get_tw_ticket_status()',
						'class'                     => 'WPSC_ITW_Change_Status',
					),
					'raised-by'             => array(
						'title'               => $labels['raised-by'],
						'is_enable'           => 1,
						'allow-customer'      => 0,
						'allowed-agent-roles' => array( 1, 2 ),
						'callback'            => 'wpsc_get_tw_raised_by()',
						'class'               => 'WPSC_ITW_Raisedby',
					),
					'ticket-info'           => array(
						'title'               => $labels['ticket-info'],
						'is_enable'           => 1,
						'allow-customer'      => 0,
						'allowed-agent-roles' => array( 1, 2 ),
						'callback'            => 'wpsc_get_tw_ticket_info()',
						'class'               => 'WPSC_ITW_Ticket_Info',
					),
					'assignee'              => array(
						'title'               => $labels['assignee'],
						'is_enable'           => 1,
						'allow-customer'      => 0,
						'allowed-agent-roles' => array( 1, 2 ),
						'callback'            => 'wpsc_get_tw_agents()',
						'class'               => 'WPSC_ITW_Assigned_Agents',
					),
					'ticket-fields'         => array(
						'title'               => $labels['ticket-fields'],
						'is_enable'           => 1,
						'allow-customer'      => 1,
						'allowed-agent-roles' => array( 1, 2 ),
						'callback'            => 'wpsc_get_tw_ticket_fields()',
						'class'               => 'WPSC_ITW_Ticket_Fields',
					),
					'agentonly-fields'      => array(
						'title'               => $labels['agentonly-fields'],
						'is_enable'           => 1,
						'allow-customer'      => 0,
						'allowed-agent-roles' => array( 1, 2 ),
						'callback'            => 'wpsc_get_tw_agentonly_fields()',
						'class'               => 'WPSC_ITW_Agentonly_Fields',
					),
					'additional-recipients' => array(
						'title'               => $labels['additional-recipients'],
						'is_enable'           => 1,
						'allow-customer'      => 1,
						'allowed-agent-roles' => array( 1, 2 ),
						'callback'            => 'wpsc_get_tw_additional_recipients()',
						'class'               => 'WPSC_ITW_Additional_Recipients',
					),
					'biographical-info'     => array(
						'title'               => $labels['biographical-info'],
						'is_enable'           => 0,
						'allow-customer'      => 1,
						'allowed-agent-roles' => array( 1, 2 ),
						'callback'            => 'wpsc_get_tw_biographical_info()',
						'class'               => 'WPSC_ITW_Biographical_Info',
					),
					'tags'                  => array(
						'title'               => $labels['tags'],
						'is_enable'           => 1,
						'allow-customer'      => 0,
						'allowed-agent-roles' => array( 1, 2 ),
						'callback'            => 'wpsc_get_tw_ticket_tags()',
						'class'               => 'WPSC_ITW_Ticket_Tags',
					),
				)
			);

			// Rich text editor.
			$notice = sprintf(
				/* translators: %1$s: attachment max file size, %2$s: allowed file extenstions. */
				esc_attr__( 'You can upload files maximum size %1$s mb of types %2$s.', 'supportcandy' ),
				20,
				'jpg, jpeg, png, gif, pdf, doc, docx, ppt, pptx, pps, ppsx, odt, xls, xlsx, mp3, m4a, ogg, wav, mp4, m4v, mov, wmv, avi, mpg, ogv, 3gp, 3g2, zip, eml'
			);
			update_option(
				'wpsc-te-agent',
				array(
					'enable'                      => 1,
					'allow-attachments'           => 1,
					'toolbar'                     => array( 'bold', 'italic', 'underline', 'blockquote', 'alignleft aligncenter alignright', 'bullist', 'numlist', 'rtl', 'link', 'wpsc_insert_editor_img' ),
					'file-attachment-notice'      => 0,
					'file-attachment-notice-text' => $notice,
				)
			);
			update_option(
				'wpsc-te-registered-user',
				array(
					'enable'                      => 1,
					'allow-attachments'           => 1,
					'toolbar'                     => array( 'bold', 'italic', 'underline', 'blockquote', 'alignleft aligncenter alignright', 'bullist', 'numlist', 'rtl', 'link', 'wpsc_insert_editor_img' ),
					'file-attachment-notice'      => 0,
					'file-attachment-notice-text' => $notice,
				)
			);
			update_option(
				'wpsc-te-guest-user',
				array(
					'enable'                      => 0,
					'allow-attachments'           => 0,
					'toolbar'                     => array( 'bold', 'italic', 'underline', 'blockquote', 'alignleft aligncenter alignright', 'bullist', 'numlist', 'rtl', 'link', 'wpsc_insert_editor_img' ),
					'file-attachment-notice'      => 0,
					'file-attachment-notice-text' => $notice,
				)
			);
			update_option( 'wpsc-te-advanced', array( 'html-pasting' => 0 ) );

			// working hrs settings.
			update_option(
				'wpsc-wh-settings',
				array(
					'allow-agent-modify-wh'     => 0,
					'allow-agent-modify-leaves' => 0,
				)
			);

			// appearence settings.
			update_option(
				'wpsc-ap-general',
				array(
					'primary-color'         => '#313042',
					'menu-link-color'       => '#fff',
					'main-background-color' => '#fff',
					'main-text-color'       => '#2c3e50',
					'link-color'            => '#2271b1',
				)
			);
			update_option(
				'wpsc-ap-individual-ticket',
				array(
					'reply-primary-color'      => '#2c3e50',
					'reply-secondary-color'    => '#777777',
					'reply-icon-color'         => '#777777',
					'note-primary-color'       => '#8e6600',
					'note-secondary-color'     => '#8e8d45',
					'note-icon-color'          => '#8e8d45',
					'log-text'                 => '#2c3e50',
					'widget-header-bg-color'   => '#fff8e5',
					'widget-header-text-color' => '#ff8f2b',
					'widget-body-bg-color'     => '#f9f9f9',
					'widget-body-label-color'  => '#777',
					'widget-body-text-color'   => '#2c3e50',
				)
			);
			update_option(
				'wpsc-ap-modal',
				array(
					'header-bg-color'   => '#fff8e5',
					'header-text-color' => '#ff8f2b',
					'body-bg-color'     => '#fff',
					'body-label-color'  => '#777',
					'body-text-color'   => '#2c3e50',
					'footer-bg-color'   => '#fff',
					'z-index'           => 900000000,
				)
			);
			update_option(
				'wpsc-ap-agent-collision',
				array(
					'header-bg-color'   => '#e6e6e6',
					'header-text-color' => '#2c3e50',
				)
			);
			update_option(
				'wpsc-ap-ticket-list',
				array(
					'list-header-background-color'     => '#2c3e50',
					'list-header-text-color'           => '#fff',
					'list-item-odd-background-color'   => '#fff',
					'list-item-odd-text-color'         => '#2c3e50',
					'list-item-even-background-color'  => '#f2f2f2',
					'list-item-even-text-color'        => '#2c3e50',
					'list-item-hover-background-color' => '#dfe4ea',
					'list-item-hover-text-color'       => '#2c3e50',
				)
			);

			// ticket tags general setting.
			update_option(
				'wpsc-ticket-tags-general-settings',
				array(
					'color'    => '#000000',
					'bg-color' => '#ffc300',
				)
			);

			// upload directory.
			$upload_dir = wp_upload_dir();
			$filepath   = $upload_dir['basedir'] . '/wpsc';
			if ( ! file_exists( $filepath ) ) {
				mkdir( $filepath, 0755, true );
			}

			self::attachment_security_file();

			// update string translations.
			update_option( 'wpsc-string-translation', $string_translations );
		}

		/**
		 * Upgrade the version
		 */
		public static function upgrade() {

			global $wpdb;

			if ( version_compare( self::$current_version, '3.0.4', '<' ) ) {

				$advanced = get_option( 'wpsc-ms-advanced-settings' );
				$advanced['allow-my-profile'] = 1;
				$advanced['allow-agent-profile'] = 1;
				update_option( 'wpsc-ms-advanced-settings', $advanced );
			}

			if ( version_compare( self::$current_version, '3.0.5', '<' ) ) {

				$advanced = get_option( 'wpsc-ms-advanced-settings' );
				$page_settings = get_option( 'wpsc-gs-page-settings' );
				if ( ! isset( $advanced['allow-my-profile'] ) ) {
					$advanced['allow-my-profile'] = 1;
					$advanced['allow-agent-profile'] = 1;
				}
				$page_settings['otp-login'] = 1;
				update_option( 'wpsc-gs-page-settings', $page_settings );
				$advanced['ticket-url-auth'] = 0;
				update_option( 'wpsc-ms-advanced-settings', $advanced );
			}

			if ( version_compare( self::$current_version, '3.0.7', '<' ) ) {

				$page_settings = get_option( 'wpsc-gs-page-settings' );
				$page_settings['load-scripts'] = 'all-pages';
				$page_settings['load-script-pages'] = array();
				update_option( 'wpsc-gs-page-settings', $page_settings );
			}

			if ( version_compare( self::$current_version, '3.1.1', '<' ) ) {

				$ap_general = get_option( 'wpsc-ap-general' );
				$ap_general['menu-link-color'] = '#fff';
				update_option( 'wpsc-ap-general', $ap_general );
			}

			if ( version_compare( self::$current_version, '3.1.3', '<' ) ) {

				$advanced = get_option( 'wpsc-ms-advanced-settings' );
				$advanced['rest-api'] = 1;
				update_option( 'wpsc-ms-advanced-settings', $advanced );
			}

			if ( version_compare( self::$current_version, '3.1.4', '<' ) ) {

				// add scheduled task for ticket data upgrade.
				if ( ! $wpdb->get_var( "SELECT id FROM {$wpdb->prefix}psmsc_scheduled_tasks WHERE class='WPSC_SC_Upgrade' AND method='update_ticket_attachment_path'" ) ) {
					$wpdb->insert(
						$wpdb->prefix . 'psmsc_scheduled_tasks',
						array(
							'class'             => 'WPSC_SC_Upgrade',
							'method'            => 'update_ticket_attachment_path',
							'is_manual'         => 1,
							'warning_text'      => 'SupportCandy - Database attachment paths upgrade needed.',
							'warning_link_text' => 'Upgrade Now',
							'progressbar_text'  => 'Updating attachment paths...',
						)
					);
				}

				self::attachment_security_file();
			}

			if ( version_compare( self::$current_version, '3.1.5', '<' ) ) {

				// scheduled task for setting conditions upgrade.
				if ( ! $wpdb->get_var( "SELECT id FROM {$wpdb->prefix}psmsc_scheduled_tasks WHERE class='WPSC_SC_Upgrade' AND method='upgrade_setting_conditions'" ) ) {
					$wpdb->insert(
						$wpdb->prefix . 'psmsc_scheduled_tasks',
						array(
							'class'     => 'WPSC_SC_Upgrade',
							'method'    => 'upgrade_setting_conditions',
							'is_manual' => 0,
						)
					);
				}

				// scheduled task for saved filter conditions upgrade.
				if ( ! $wpdb->get_var( "SELECT id FROM {$wpdb->prefix}psmsc_scheduled_tasks WHERE class='WPSC_SC_Upgrade' AND method='upgrade_saved_filter_conditions'" ) ) {
					$wpdb->insert(
						$wpdb->prefix . 'psmsc_scheduled_tasks',
						array(
							'class'             => 'WPSC_SC_Upgrade',
							'method'            => 'upgrade_saved_filter_conditions',
							'is_manual'         => 1,
							'warning_text'      => 'SupportCandy - Filters database upgrade needed.',
							'warning_link_text' => 'Upgrade Now',
							'progressbar_text'  => 'Upgrading filters...',
						)
					);
				}
			}

			if ( version_compare( self::$current_version, '3.1.6', '<' ) ) {

				if ( ! $wpdb->get_var( "SELECT id FROM {$wpdb->prefix}psmsc_scheduled_tasks WHERE class='WPSC_SC_Upgrade' AND method='repaire_setting_conditions'" ) ) {
					$wpdb->insert(
						$wpdb->prefix . 'psmsc_scheduled_tasks',
						array(
							'class'     => 'WPSC_SC_Upgrade',
							'method'    => 'repaire_setting_conditions',
							'is_manual' => 0,
						)
					);
				}

				if ( ! $wpdb->get_var( "SELECT id FROM {$wpdb->prefix}psmsc_scheduled_tasks WHERE class='WPSC_SC_Upgrade' AND method='repaire_saved_filter_conditions'" ) ) {
					$wpdb->insert(
						$wpdb->prefix . 'psmsc_scheduled_tasks',
						array(
							'class'             => 'WPSC_SC_Upgrade',
							'method'            => 'repaire_saved_filter_conditions',
							'is_manual'         => 1,
							'warning_text'      => 'SupportCandy - Filters database upgrade needed.',
							'warning_link_text' => 'Upgrade Now',
							'progressbar_text'  => 'Upgrading filters...',
						)
					);
				}
			}

			if ( version_compare( self::$current_version, '3.1.7', '<' ) ) {

				$gs = get_option( 'wpsc-gs-general' );
				$gs['allowed-search-fields'] = array( 'id', 'customer', 'subject', 'threads' );
				update_option( 'wpsc-gs-general', $gs );

				$en = get_option( 'wpsc-en-general' );
				$en['attachments-in-notification'] = 'actual-files';
				update_option( 'wpsc-en-general', $en );
			}

			if ( version_compare( self::$current_version, '3.1.9', '<' ) ) {

				// add scheduled task for attachment status upgrade.
				if ( ! $wpdb->get_var( "SELECT id FROM {$wpdb->prefix}psmsc_scheduled_tasks WHERE class='WPSC_SC_Upgrade' AND method='update_ticket_attachment_status'" ) ) {
					$wpdb->insert(
						$wpdb->prefix . 'psmsc_scheduled_tasks',
						array(
							'class'             => 'WPSC_SC_Upgrade',
							'method'            => 'update_ticket_attachment_status',
							'is_manual'         => 1,
							'warning_text'      => 'SupportCandy - Database attachment status upgrade needed.',
							'warning_link_text' => 'Upgrade Now',
							'progressbar_text'  => 'Updating attachment status...',
						)
					);
				}
			}

			if ( version_compare( self::$current_version, '3.2.0', '<' ) ) {

				$roles = get_option( 'wpsc-agent-roles' );
				foreach ( $roles as $key => $role ) {
					$role['caps']['dt-unassigned'] = true;
					$role['caps']['dt-assigned-me'] = true;
					$role['caps']['dt-assigned-others'] = true;

					$roles[ $key ] = $role;
				}
				update_option( 'wpsc-agent-roles', $roles );

				// set customer custom fields as personal info.
				$wpdb->update(
					$wpdb->prefix . 'psmsc_custom_fields',
					array( 'is_personal_info' => 1 ),
					array( 'field' => 'customer' )
				);
			}

			if ( version_compare( self::$current_version, '3.2.1', '<' ) ) {

				$string_translations = get_option( 'wpsc-string-translation' );

				$thankyou = get_option( 'wpsc-gs-thankyou-page-settings' );
				if ( $thankyou['thank-you-page-url'] ) {
					$action = 'url';
				} else {
					$action = 'text';
				}

				update_option(
					'wpsc-gs-thankyou-page-settings',
					array(
						'action-agent'      => $action,
						'action-customer'   => $action,
						'html-agent'        => $thankyou['thankyou-html'],
						'html-customer'     => $thankyou['thankyou-html'],
						'page-url-agent'    => $thankyou['thank-you-page-url'],
						'page-url-customer' => $thankyou['thank-you-page-url'],
						'editor-agent'      => $thankyou['editor'],
						'editor-customer'   => $thankyou['editor'],
					)
				);
				$string_translations['wpsc-thankyou-html-agent'] = $thankyou['thankyou-html'];

				update_option( 'wpsc-string-translation', $string_translations );

				// ticket tags.
				$widgets = get_option( 'wpsc-ticket-widget', array() );
				if ( ! isset( $widgets['tags'] ) ) {

					$agent_roles = array_keys( get_option( 'wpsc-agent-roles', array() ) );
					$label = esc_attr( wpsc__( 'Tags', 'supportcandy' ) );
					$widgets['tags'] = array(
						'title'               => $label,
						'is_enable'           => 1,
						'allow-customer'      => 0,
						'allowed-agent-roles' => $agent_roles,
						'callback'            => 'wpsc_get_tw_ticket_tags()',
						'class'               => 'WPSC_ITW_Ticket_Tags',
					);
					update_option( 'wpsc-ticket-widget', $widgets );

					$string_translations['wpsc-twt-tags'] = $label;

				}

				$name = esc_attr__( 'Tags', 'supportcandy' );
				$wpdb->insert(
					$wpdb->prefix . 'psmsc_custom_fields',
					array(
						'name'             => $name,
						'slug'             => 'tags',
						'field'            => 'ticket',
						'type'             => 'df_tags',
						'is_personal_info' => 0,
						'tl_width'         => 100,
						'load_order'       => 24,
					)
				);
				$string_translations[ 'wpsc-cf-name-' . $wpdb->insert_id ] = $name;

				update_option( 'wpsc-string-translation', $string_translations );

				$roles = get_option( 'wpsc-agent-roles' );
				foreach ( $roles as $key => $role ) {
					$role['caps']['tt-unassigned'] = true;
					$role['caps']['tt-assigned-me'] = true;
					$role['caps']['tt-assigned-others'] = true;

					$roles[ $key ] = $role;
				}
				update_option( 'wpsc-agent-roles', $roles );

				// ticket tags general setting.
				update_option(
					'wpsc-ticket-tags-general-settings',
					array(
						'color'    => '#000000',
						'bg-color' => '#ffc300',
					)
				);

				// Add agent collision appearance setting.
				update_option(
					'wpsc-ap-agent-collision',
					array(
						'header-bg-color'   => '#e6e6e6',
						'header-text-color' => '#2c3e50',
					)
				);

				// Add agent collision setting.
				$ms_advanced = get_option( 'wpsc-ms-advanced-settings' );
				$ms_advanced['agent-collision'] = 1;
				update_option( 'wpsc-ms-advanced-settings', $ms_advanced );
			}

			self::set_upgrade_complete();
		}

		/**
		 * Mark upgrade as complete
		 */
		public static function set_upgrade_complete() {

			update_option( 'wpsc_current_version', WPSC_VERSION );
			update_option( 'wpsc_db_version', WPSC_DB_VERSION );
			self::$current_version = WPSC_VERSION;
			self::$is_upgrade      = false;
		}

		/**
		 * Actions to perform after plugin deactivated
		 *
		 * @return void
		 */
		public static function deactivate() {

			// Remove cron jobs.
			WPSC_Cron::unschedule_events();
		}

		/**
		 * Add htaccess file in supportcandy attachment folder
		 *
		 * @return void
		 */
		public static function attachment_security_file() {

			$upload_dir = wp_upload_dir();
			$wpsc_dir = $upload_dir['basedir'] . '/wpsc';
			if ( ! is_file( $wpsc_dir . '/.htaccess' ) ) {
				@file_put_contents( $wpsc_dir . '/.htaccess', 'deny from all' ); //phpcs:ignore
			}
		}
	}
endif;

WPSC_Installation::init();
