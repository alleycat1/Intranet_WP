<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

/**
 * Garbage collector class for supportcandy
 */
if ( ! class_exists( 'WPSC_Cleaner' ) ) :

	final class WPSC_Cleaner {

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// schedule cron jobs.
			add_action( 'init', array( __CLASS__, 'schedule' ) );

			// upgrade cleanup.
			add_action( 'wpsc_v1_upgrade_cleanup', array( __CLASS__, 'v1_upgrade_cleanup' ) );
			add_action( 'wpsc_v2_upgrade_cleanup', array( __CLASS__, 'v2_upgrade_cleanup' ) );
		}

		/**
		 * Schedule cron jobs
		 *
		 * @return void
		 */
		public static function schedule() {

			// upgrade cleanup.
			$upgrade = get_option( 'wpsc_upgrade_cleanup', array() );
			if ( $upgrade ) {
				if ( $upgrade['version'] == 1 && ! wp_next_scheduled( 'wpsc_v1_upgrade_cleanup' ) ) {
					wp_schedule_event(
						time(),
						'hourly',
						'wpsc_v1_upgrade_cleanup'
					);
				}
				if ( $upgrade['version'] == 2 && ! wp_next_scheduled( 'wpsc_v2_upgrade_cleanup' ) ) {
					wp_schedule_event(
						time(),
						'hourly',
						'wpsc_v2_upgrade_cleanup'
					);
				}
			}
		}

		/**
		 * V1 upgrade cleanup
		 *
		 * @return void
		 */
		public static function v1_upgrade_cleanup() {

			self::wpsc_upgrade_register_post_type();
			$upgrade = get_option( 'wpsc_upgrade_cleanup', array() );

			switch ( $upgrade['status'] ) {

				case 'texonomy':
					self::wpsc_upgrade_delete_all_terms( 'wpsc_statuses' );
					self::wpsc_upgrade_delete_all_terms( 'wpsc_categories' );
					self::wpsc_upgrade_delete_all_terms( 'wpsc_priorities' );
					self::wpsc_upgrade_delete_all_terms( 'wpsc_ticket_custom_fields' );
					self::wpsc_upgrade_delete_all_terms( 'wpsc_ticket_widget' );
					self::wpsc_upgrade_delete_all_terms( 'wpsc_agents' );
					self::wpsc_upgrade_delete_all_terms( 'wpsc_attachment' );
					self::wpsc_upgrade_delete_all_terms( 'wpsc_en' );
					self::wpsc_upgrade_delete_all_terms( 'wpsc_canned_reply_categories' );
					self::wpsc_upgrade_delete_all_terms( 'wpsc_sf_rating' );
					self::wpsc_upgrade_delete_all_terms( 'wpsc_caa' );
					self::wpsc_upgrade_delete_all_terms( 'wpsc_ep_rules' );
					self::wpsc_upgrade_delete_all_terms( 'wpsc_schedule_tickets' );
					self::wpsc_upgrade_delete_all_terms( 'wpsc_sla' );
					self::wpsc_upgrade_delete_all_terms( 'wpsc_usergroup_data' );
					$upgrade['status'] = 'options';
					update_option( 'wpsc_upgrade_cleanup', $upgrade );
					break;

				case 'options':
					// ticket list more settings.
					delete_option( 'wpsc_tl_agent_orderby' );
					delete_option( 'wpsc_tl_agent_orderby_order' );
					delete_option( 'wpsc_tl_agent_no_of_tickets' );
					delete_option( 'wpsc_tl_agent_unresolve_statuses' );
					delete_option( 'wpsc_tl_customer_orderby' );
					delete_option( 'wpsc_tl_customer_orderby_order' );
					delete_option( 'wpsc_tl_customer_no_of_tickets' );
					delete_option( 'wpsc_tl_customer_unresolve_statuses' );
					// email notification general.
					delete_option( 'wpsc_en_from_name' );
					delete_option( 'wpsc_en_from_email' );
					delete_option( 'wpsc_en_reply_to' );
					delete_option( 'wpsc_en_ignore_emails' );
					delete_option( 'wpsc_support_page_id' );
					// general settings.
					delete_option( 'wpsc_default_ticket_status' );
					delete_option( 'wpsc_default_ticket_category' );
					delete_option( 'wpsc_default_ticket_priority' );
					delete_option( 'wpsc_ticket_status_after_customer_reply' );
					delete_option( 'wpsc_ticket_status_after_agent_reply' );
					delete_option( 'wpsc_close_ticket_status' );
					delete_option( 'wpsc_allow_customer_close_ticket' );
					delete_option( 'wpsc_reply_form_position' );
					delete_option( 'wpsc_ticket_alice' );
					delete_option( 'wpsc_allow_guest_ticket' );
					delete_option( 'wpsc_allow_tinymce_in_guest_ticket' );
					delete_option( 'wpsc_reply_to_close_ticket' );
					delete_option( 'wpsc_default_login_setting' );
					delete_option( 'wpsc_user_registration' );
					// thank you page.
					delete_option( 'wpsc_thankyou_html' );
					delete_option( 'wpsc_thankyou_url' );
					// GDPR, terms & conditions.
					delete_option( 'wpsc_terms_and_conditions' );
					delete_option( 'wpsc_terms_and_conditions_html' );
					delete_option( 'wpsc_set_in_gdpr' );
					delete_option( 'wpsc_gdpr_html' );
					delete_option( 'wpsc_personal_data_retention_period_time' );
					delete_option( 'wpsc_personal_data_retention_period_unit' );
					// advanced settings.
					delete_option( 'wpsc_guest_can_upload_files' );
					delete_option( 'wpsc_ticket_public_mode' );
					delete_option( 'wpsc_allow_reply_confirmation' );
					delete_option( 'wpsc_tinymce_toolbar' );
					delete_option( 'wpsc_tinymce_toolbar_active' );
					delete_option( 'wpsc_thread_date_format' );
					delete_option( 'wpsc_do_not_notify_setting' );
					// captcha.
					delete_option( 'wpsc_captcha' );
					delete_option( 'wpsc_recaptcha_type' );
					delete_option( 'wpsc_get_site_key' );
					delete_option( 'wpsc_get_secret_key' );
					// satisfaction survey.
					delete_option( 'wpsc_upgrade_sf_rating_map' );
					delete_option( 'wpsc_sf_page' );
					delete_option( 'wpsc_sf_thankyou_text' );
					delete_option( 'wpsc_sf_age' );
					delete_option( 'wpsc_sf_age_unit' );
					delete_option( 'wpsc_sf_subject' );
					delete_option( 'wpsc_sf_email_body' );
					// email piping.
					delete_option( 'wpsc_ep_block_emails' );
					delete_option( 'wpsc_ep_cron_execution_time' );
					delete_option( 'wpsc_ep_piping_type' );
					delete_option( 'wpsc_ep_client_id' );
					delete_option( 'wpsc_ep_client_secret' );
					delete_option( 'wpsc_ep_email_address' );
					delete_option( 'wpsc_ep_imap_email_address' );
					delete_option( 'wpsc_ep_imap_email_password' );
					delete_option( 'wpsc_ep_imap_incoming_mail_server' );
					delete_option( 'wpsc_ep_imap_port' );
					delete_option( 'wpsc_ep_refresh_token' );
					delete_option( 'wpsc_ep_historyId' );
					delete_option( 'wpsc_ep_block_subject' );
					delete_option( 'wpsc_ep_allowed_user' );
					delete_option( 'wpsc_ep_email_type' );
					delete_option( 'wpsc_ep_debug_mode' );
					$upgrade['status'] = 'canned_reply';
					update_option( 'wpsc_upgrade_cleanup', $upgrade );
					break;

				case 'canned_reply':
					$results = get_posts(
						array(
							'post_type'      => 'wpsc_canned_reply',
							'post_status'    => array( 'publish', 'trash', 'auto-draft', 'draft' ),
							'posts_per_page' => 10,
						)
					);
					if ( $results ) {
						foreach ( $results as $post ) {
							wp_delete_post( $post->ID, true );
						}
					} else {
						$upgrade['status'] = 'tickets';
						update_option( 'wpsc_upgrade_cleanup', $upgrade );
					}
					break;

				case 'tickets':
					$results = get_posts(
						array(
							'post_type'      => 'wpsc_ticket',
							'post_status'    => array( 'publish', 'trash' ),
							'posts_per_page' => 10,
						)
					);
					if ( $results ) {
						foreach ( $results as $post ) {
							wp_delete_post( $post->ID, true );
						}
					} else {
						$upgrade['status'] = 'threads';
						update_option( 'wpsc_upgrade_cleanup', $upgrade );
					}
					break;

				case 'threads':
					$results = get_posts(
						array(
							'post_type'      => 'wpsc_ticket_thread',
							'post_status'    => array( 'publish', 'trash' ),
							'posts_per_page' => 10,
						)
					);
					if ( $results ) {
						foreach ( $results as $post ) {
							wp_delete_post( $post->ID, true );
						}
					} else {
						delete_option( 'wpsc_upgrade_cleanup' );
					}
					break;
			}
		}

		/**
		 * V2 upgrade cleanup
		 *
		 * @return void
		 */
		public static function v2_upgrade_cleanup() {

			self::wpsc_upgrade_register_post_type();
			$upgrade = get_option( 'wpsc_upgrade_cleanup', array() );

			switch ( $upgrade['status'] ) {

				case 'texonomy':
					self::wpsc_upgrade_delete_all_terms( 'wpsc_statuses' );
					self::wpsc_upgrade_delete_all_terms( 'wpsc_categories' );
					self::wpsc_upgrade_delete_all_terms( 'wpsc_priorities' );
					self::wpsc_upgrade_delete_all_terms( 'wpsc_ticket_custom_fields' );
					self::wpsc_upgrade_delete_all_terms( 'wpsc_ticket_widget' );
					self::wpsc_upgrade_delete_all_terms( 'wpsc_agents' );
					self::wpsc_upgrade_delete_all_terms( 'wpsc_attachment' );
					self::wpsc_upgrade_delete_all_terms( 'wpsc_en' );
					self::wpsc_upgrade_delete_all_terms( 'wpsc_canned_reply_categories' );
					self::wpsc_upgrade_delete_all_terms( 'wpsc_sf_rating' );
					self::wpsc_upgrade_delete_all_terms( 'wpsc_caa' );
					self::wpsc_upgrade_delete_all_terms( 'wpsc_ep_rules' );
					self::wpsc_upgrade_delete_all_terms( 'wpsc_schedule_tickets' );
					self::wpsc_upgrade_delete_all_terms( 'wpsc_sla' );
					self::wpsc_upgrade_delete_all_terms( 'wpsc_usergroup_data' );
					self::wpsc_upgrade_delete_all_terms( 'wpsc_usergroup_custom_field' );
					self::wpsc_upgrade_delete_all_terms( 'wpsc_gf' );
					$upgrade['status'] = 'options';
					update_option( 'wpsc_upgrade_cleanup', $upgrade );
					break;

				case 'options':
					// agent settings.
					delete_option( 'wpsc_agent_role' );
					// ticket list more settings.
					delete_option( 'wpsc_tl_agent_orderby' );
					delete_option( 'wpsc_tl_agent_orderby_order' );
					delete_option( 'wpsc_tl_agent_no_of_tickets' );
					delete_option( 'wpsc_tl_agent_unresolve_statuses' );
					delete_option( 'wpsc_tl_customer_orderby' );
					delete_option( 'wpsc_tl_customer_orderby_order' );
					delete_option( 'wpsc_tl_customer_no_of_tickets' );
					delete_option( 'wpsc_tl_customer_unresolve_statuses' );
					delete_option( 'wpsc_close_ticket_group' );
					delete_option( 'wpsc_tl_statuses' );
					// email notification general.
					delete_option( 'wpsc_en_from_name' );
					delete_option( 'wpsc_en_from_email' );
					delete_option( 'wpsc_en_reply_to' );
					delete_option( 'wpsc_en_ignore_emails' );
					delete_option( 'wpsc_support_page_id' );
					delete_option( 'wpsc_email_sending_method' );
					delete_option( 'wpsc_en_send_mail_count' );
					// general settings.
					delete_option( 'wpsc_default_ticket_status' );
					delete_option( 'wpsc_default_ticket_category' );
					delete_option( 'wpsc_default_ticket_priority' );
					delete_option( 'wpsc_ticket_status_after_customer_reply' );
					delete_option( 'wpsc_ticket_status_after_agent_reply' );
					delete_option( 'wpsc_close_ticket_status' );
					delete_option( 'wpsc_allow_customer_close_ticket' );
					delete_option( 'wpsc_reply_form_position' );
					delete_option( 'wpsc_ticket_alice' );
					delete_option( 'wpsc_allow_guest_ticket' );
					delete_option( 'wpsc_allow_tinymce_in_guest_ticket' );
					delete_option( 'wpsc_reply_to_close_ticket' );
					delete_option( 'wpsc_default_login_setting' );
					delete_option( 'wpsc_user_registration' );
					delete_option( 'wpsc_allow_to_create_ticket' );
					delete_option( 'wpsc_allow_reply_to_close_ticket' );
					delete_option( 'wpsc_user_registration_method' );
					delete_option( 'wpsc_custom_registration_url' );
					delete_option( 'wpsc_calender_date_format' );
					delete_option( 'wpsc_custom_login_url' );
					// thank you page.
					delete_option( 'wpsc_thankyou_html' );
					delete_option( 'wpsc_thankyou_url' );
					// GDPR, terms & conditions.
					delete_option( 'wpsc_terms_and_conditions' );
					delete_option( 'wpsc_terms_and_conditions_html' );
					delete_option( 'wpsc_set_in_gdpr' );
					delete_option( 'wpsc_gdpr_html' );
					delete_option( 'wpsc_personal_data_retention_period_time' );
					delete_option( 'wpsc_personal_data_retention_period_unit' );
					// advanced settings.
					delete_option( 'wpsc_guest_can_upload_files' );
					delete_option( 'wpsc_ticket_public_mode' );
					delete_option( 'wpsc_allow_reply_confirmation' );
					delete_option( 'wpsc_tinymce_toolbar' );
					delete_option( 'wpsc_tinymce_toolbar_active' );
					delete_option( 'wpsc_thread_date_format' );
					delete_option( 'wpsc_do_not_notify_setting' );
					delete_option( 'wpsc_thread_date_time_format' );
					delete_option( 'wpsc_allow_reply_to_public_tickets' );
					delete_option( 'wpsc_default_do_not_notify_option' );
					delete_option( 'wpsc_hide_show_priority' );
					delete_option( 'wpsc_view_more' );
					delete_option( 'wpsc_on_and_off_auto_refresh' );
					delete_option( 'wpsc_ticket_id_type' );
					delete_option( 'wpsc_custom_ticket_count' );
					delete_option( 'wpsc_rt_id_length' );
					delete_option( 'wpsc_thread_limit' );
					delete_option( 'wpsc_redirect_to_ticket_list' );
					delete_option( 'wpsc_reg_guest_user_after_create_ticket' );
					delete_option( 'wpsc_auto_delete_ticket_time' );
					delete_option( 'wpsc_auto_delete_ticket_time_period_unit' );
					delete_option( 'wpsc_reply_bcc_visibility' );
					delete_option( 'wpsc_new_ticket_btn_url' );
					delete_option( 'wpsc_raised_by_user' );
					delete_option( 'wpsc_allow_rich_text_editor' );
					delete_option( 'wpsc_allow_html_pasting' );
					// captcha.
					delete_option( 'wpsc_captcha' );
					delete_option( 'wpsc_recaptcha_type' );
					delete_option( 'wpsc_get_site_key' );
					delete_option( 'wpsc_get_secret_key' );
					delete_option( 'wpsc_registration_captcha' );
					delete_option( 'wpsc_login_captcha' );
					// appearance.
					delete_option( 'wpsc_create_ticket' );
					delete_option( 'wpsc_appearance_login_form' );
					delete_option( 'wpsc_appearance_general_settings' );
					delete_option( 'wpsc_individual_ticket_page' );
					delete_option( 'wpsc_modal_window' );
					delete_option( 'wpsc_appearance_signup' );
					delete_option( 'wpsc_appearance_ticket_list' );
					// assign agent rules.
					delete_option( 'wpsc_assign_auto_responder' );
					// automatic close ticket.
					delete_option( 'wpsc_atc_age' );
					delete_option( 'wpsc_atc_status' );
					delete_option( 'wpsc_atc_waring_email_age' );
					delete_option( 'wpsc_atc_subject' );
					delete_option( 'wpsc_atc_email_body' );
					// satisfaction survey.
					delete_option( 'wpsc_upgrade_sf_rating_map' );
					delete_option( 'wpsc_sf_page' );
					delete_option( 'wpsc_sf_thankyou_text' );
					delete_option( 'wpsc_sf_age' );
					delete_option( 'wpsc_sf_age_unit' );
					delete_option( 'wpsc_sf_subject' );
					delete_option( 'wpsc_sf_email_body' );
					// email piping.
					delete_option( 'wpsc_ep_block_emails' );
					delete_option( 'wpsc_ep_cron_execution_time' );
					delete_option( 'wpsc_ep_piping_type' );
					delete_option( 'wpsc_ep_client_id' );
					delete_option( 'wpsc_ep_client_secret' );
					delete_option( 'wpsc_ep_email_address' );
					delete_option( 'wpsc_ep_imap_email_address' );
					delete_option( 'wpsc_ep_imap_email_password' );
					delete_option( 'wpsc_ep_imap_incoming_mail_server' );
					delete_option( 'wpsc_ep_imap_port' );
					delete_option( 'wpsc_ep_refresh_token' );
					delete_option( 'wpsc_ep_historyId' );
					delete_option( 'wpsc_ep_block_subject' );
					delete_option( 'wpsc_ep_allowed_user' );
					delete_option( 'wpsc_ep_email_type' );
					delete_option( 'wpsc_ep_debug_mode' );
					delete_option( 'wpsc_ep_emails_forwarded' );
					delete_option( 'wpsc_ep_from_email' );
					delete_option( 'wpsc_ep_imap_encryption' );
					delete_option( 'wpsc_close_user_warn_email_status' );
					delete_option( 'wpsc_close_user_warn_email_body' );
					delete_option( 'wpsc_ct_warn_email_status' );
					delete_option( 'wpsc_ct_warn_email_body' );
					delete_option( 'wpsc_ep_accept_emails' );
					delete_option( 'wpsc_add_additional_recepients' );
					// FAQ.
					delete_option( 'wpsc_select_faq_set' );
					// knowledgebase.
					delete_option( 'wpsc_select_knowledgbase_set' );
					// export ticket.
					delete_option( 'wpsc_selected_user_roll_data' );
					delete_option( 'wpsc_export_ticket_list' );
					delete_option( 'wpsc_customer_export_ticket_list' );
					// woocommerce.
					delete_option( 'wpsc_dashboard_support_tab' );
					delete_option( 'wpsc_dashboard_support_tab_label' );
					delete_option( 'wpsc_order_help_button' );
					delete_option( 'wpsc_order_help_button_label' );
					delete_option( 'wpsc_woo_ticket_url' );
					// schedule ticket.
					delete_option( 'wpsc_schedule_ticket_btn' );
					// sla.
					delete_option( 'wpsc_out_sla_color' );
					delete_option( 'wpsc_in_sla_color' );
					// usergroups.
					delete_option( 'wpsc_usergroup_change_category' );
					delete_option( 'wpsc_allow_ug_sup_close_ticket' );
					// timer.
					delete_option( 'wpsc_timer_enable' );
					delete_option( 'wpsc_timer_stop' );
					delete_option( 'wpsc_timer_visibility_for_customer' );
					// private credentials.
					delete_option( 'wpsc_pc_role_permissions' );
					// print ticket.
					delete_option( 'wpsc_print_th_btn_setting' );
					delete_option( 'wpsc_print_btn_lbl' );
					delete_option( 'wpsc_print_cust_btn_setting' );
					delete_option( 'wpsc_print_page_header_height' );
					delete_option( 'wpsc_print_page_footer_height' );
					delete_option( 'wpsc_print_ticket_header' );
					delete_option( 'wpsc_print_ticket_body' );
					delete_option( 'wpsc_print_ticket_footer' );
					delete_option( 'wpsc_appearance_print_ticket' );
					// attachment setting.
					delete_option( 'wpsc_attachment_max_filesize' );
					delete_option( 'wpsc_allow_attachment_type' );
					delete_option( 'wpsc_image_download_method' );
					delete_option( 'wpsc_allow_attach_create_ticket' );
					delete_option( 'wpsc_allow_attach_reply_form' );
					delete_option( 'wpsc_show_attachment_notice' );
					delete_option( 'wpsc_attachment_notice' );
					// upgrade options.
					delete_option( 'wpsc_upgrade_permission_v2' );
					delete_option( 'wpsc_v2_upgrade_cron_status' );
					delete_option( 'wpsc_upgrade_installed_addons' );
					delete_option( 'wpsc_upgrade_cf_slug_map' );
					delete_option( 'wpsc_upgrade_cf_options_map' );
					delete_option( 'wpsc_upgrade_status_map' );
					delete_option( 'wpsc_upgrade_category_map' );
					delete_option( 'wpsc_upgrade_priority_map' );
					delete_option( 'wpsc_upgrade_agent_map' );
					delete_option( 'wpsc_upgrade_ug_term_id_map' );
					delete_option( 'wpsc_upgrade_saved_filters_map' );
					delete_option( 'wpsc_upgrade_cf_term_id_map' );
					delete_option( 'wpsc_upgrade_cf_slug_map' );
					delete_option( 'wpsc_upgrade_en_term_id_map' );
					delete_option( 'wpsc_upgrade_aar_map' );
					delete_option( 'wpsc_upgrade_sla_policy_map' );
					$upgrade['status'] = 'canned_reply';
					update_option( 'wpsc_upgrade_cleanup', $upgrade );
					break;

				case 'canned_reply':
					$results = get_posts(
						array(
							'post_type'      => 'wpsc_canned_reply',
							'post_status'    => array( 'publish', 'trash', 'auto-draft', 'draft' ),
							'posts_per_page' => 10,
						)
					);
					if ( $results ) {
						foreach ( $results as $post ) {
							wp_delete_post( $post->ID, true );
						}
					} else {
						$upgrade['status'] = 'threads';
						update_option( 'wpsc_upgrade_cleanup', $upgrade );
					}
					break;

				case 'threads':
					$results = get_posts(
						array(
							'post_type'      => 'wpsc_ticket_thread',
							'post_status'    => array( 'publish', 'trash' ),
							'posts_per_page' => 10,
						)
					);
					if ( $results ) {
						foreach ( $results as $post ) {
							wp_delete_post( $post->ID, true );
						}
					} else {
						delete_option( 'wpsc_upgrade_cleanup' );
					}
					break;
			}
		}

		/**
		 * Register post types in order to get data upgrade cleanup
		 *
		 * @return void
		 */
		public static function wpsc_upgrade_register_post_type() {

			$args = array(
				'public'  => false,
				'rewrite' => false,
			);
			register_post_type( 'wpsc_ticket', $args );
			register_post_type( 'wpsc_ticket_thread', $args );
			register_taxonomy( 'wpsc_categories', 'wpsc_ticket', $args );
			register_taxonomy( 'wpsc_statuses', 'wpsc_ticket', $args );
			register_taxonomy( 'wpsc_priorities', 'wpsc_ticket', $args );
			register_taxonomy( 'wpsc_ticket_custom_fields', 'wpsc_ticket', $args );
			register_taxonomy( 'wpsc_ticket_widget', 'wpsc_ticket', $args );
			register_taxonomy( 'wpsc_agents', 'wpsc_ticket', $args );
			register_taxonomy( 'wpsc_attachment', 'wpsc_ticket', $args );
			register_taxonomy( 'wpsc_en', 'wpsc_ticket', $args );
			// conditional assigned agent (assigned agent rules).
			register_taxonomy( 'wpsc_caa', 'wpsc_ticket', $args );
			// canned reply.
			register_post_type( 'wpsc_canned_reply', $args );
			register_taxonomy( 'wpsc_canned_reply_categories', 'wpsc_canned_reply', $args );
			// satisfaction survey.
			register_taxonomy( 'wpsc_sf_rating', 'wpsc_ticket', $args );
			// email piping.
			register_taxonomy( 'wpsc_ep_rules', 'wpsc_ticket', $args );
			// schedule tickets.
			register_taxonomy( 'wpsc_schedule_tickets', 'wpsc_ticket', $args );
			// sla.
			register_taxonomy( 'wpsc_sla', 'wpsc_ticket', $args );
			// usergroup.
			register_taxonomy( 'wpsc_usergroup_data', 'wpsc_ticket', $args );
			register_taxonomy( 'wpsc_usergroup_custom_field', 'wpsc_ticket', $args );
			// gravity forms.
			register_taxonomy( 'wpsc_gf', 'wpsc_ticket', $args );
		}

		/**
		 * Delete all terms of given texonomy
		 *
		 * @param string $taxonomy_name - texonomy name.
		 * @return void
		 */
		public static function wpsc_upgrade_delete_all_terms( $taxonomy_name ) {

			$terms = get_terms(
				array(
					'taxonomy'   => $taxonomy_name,
					'hide_empty' => false,
				)
			);
			foreach ( $terms as $term ) {
				wp_delete_term( $term->term_id, $taxonomy_name );
			}
		}
	}
endif;

WPSC_Cleaner::init();
