<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_SC_Upgrade' ) ) :

	final class WPSC_SC_Upgrade {

		/**
		 * Update attachment file paths in database
		 *
		 * @param WPSC_Scheduled_Task $task - task object.
		 * @version 3.1.0
		 * @return void
		 */
		public static function update_ticket_attachment_path( $task ) {

			$upload_dir = wp_upload_dir();

			$attachments = WPSC_Attachment::find(
				array(
					'items_per_page' => 100,
					'meta_query'     => array(
						'relation' => 'AND',
						array(
							'slug'    => 'file_path',
							'compare' => 'LIKE',
							'val'     => $upload_dir['basedir'],
						),
					),
				)
			);

			if ( $attachments['total_items'] === 0 ) {
				WPSC_Scheduled_Task::destroy( $task );
				return;
			}

			// save remaining pages of the task. it will be used in ui section.
			$task->pages = $attachments['total_pages'] - 1;
			$task->save();

			foreach ( $attachments['results'] as $attachment ) {
				$attachment->file_path = str_replace( $upload_dir['basedir'], '', $attachment->file_path );
				$attachment->save();
			}
		}

		/**
		 * Upgrade setting conditions
		 *
		 * @param WPSC_Scheduled_Task $task - task object.
		 * @version 3.1.5
		 * @return void
		 */
		public static function upgrade_setting_conditions( $task ) {

			// Visibility conditions.
			$tff = get_option( 'wpsc-tff' );
			foreach ( $tff as $slug => $properties ) {
				if ( ! $properties['visibility'] ) {
					continue;
				}
				$tff[ $slug ]['visibility'] = self::upgrade_condition( $properties['visibility'], $properties['relation'] );
			}
			update_option( 'wpsc-tff', $tff );

			// Email notifications.
			$email_templates = get_option( 'wpsc-email-templates' );
			foreach ( $email_templates as $index => $properties ) {
				if ( ! $properties['conditions'] ) {
					continue;
				}
				$email_templates[ $index ]['conditions'] = self::upgrade_condition( $properties['conditions'], $properties['relation'] );
			}
			update_option( 'wpsc-email-templates', $email_templates );

			// Default agent filters.
			$atl_filters = get_option( 'wpsc-atl-default-filters' );
			foreach ( $atl_filters as $slug => $properties ) {
				if ( ! is_numeric( $slug ) ) {
					continue;
				}
				$atl_filters[ $slug ]['filters'] = self::upgrade_condition( $properties['filters'], 'AND' );
			}
			update_option( 'wpsc-atl-default-filters', $atl_filters );

			// Default customer filters.
			$ctl_filters = get_option( 'wpsc-ctl-default-filters' );
			foreach ( $ctl_filters as $slug => $properties ) {
				if ( ! is_numeric( $slug ) ) {
					continue;
				}
				$ctl_filters[ $slug ]['filters'] = self::upgrade_condition( $properties['filters'], 'AND' );
			}
			update_option( 'wpsc-ctl-default-filters', $ctl_filters );

			// Assigned agent rules filters.
			if ( class_exists( 'WPSC_AAR' ) ) {
				$rules = get_option( 'wpsc-aar-rules', array() );
				foreach ( $rules as $index => $properties ) {
					$rules[ $index ]['conditions'] = self::upgrade_condition( $properties['conditions'], $properties['relation'] );
				}
				update_option( 'wpsc-aar-rules', $rules );
			}

			// SLA policy filters.
			if ( class_exists( 'WPSC_SLA' ) ) {
				$policies = get_option( 'wpsc-sla-policies', array() );
				foreach ( $policies as $index => $properties ) {
					$policies[ $index ]['conditions'] = self::upgrade_condition( $properties['conditions'], $properties['relation'] );
				}
				update_option( 'wpsc-sla-policies', $policies );
			}

			WPSC_Scheduled_Task::destroy( $task );
		}

		/**
		 * Upgrade saved filter conditions for all customers
		 *
		 * @param WPSC_Scheduled_Task $task - task object.
		 * @version 3.1.5
		 * @return void
		 */
		public static function upgrade_saved_filter_conditions( $task ) {

			global $wpdb;

			$current_page = get_transient( 'wpsc_upgrade_saved_filter_conditions_cursor' );
			if ( false === $current_page ) {

				$total_items = $wpdb->get_var( "SELECT COUNT(id) FROM {$wpdb->prefix}psmsc_customers" );
				$total_pages = ceil( $total_items / 50 );
				$task->pages = $total_pages;
				$task->save();

				$current_page = 0;
				set_transient( 'wpsc_upgrade_saved_filter_conditions_cursor', $current_page, MINUTE_IN_SECONDS * 60 * 48 );
			}

			$current_page++;

			$customers = WPSC_Customer::find(
				array(
					'items_per_page' => 50,
					'page_no'        => $current_page,
					'meta_query'     => array(
						'relation' => 'AND',
						array(
							'slug'    => 'custom_query',
							'compare' => '=',
							'val'     => 'c.user>0',
						),
					),
				)
			);

			if ( $customers['results'] ) {

				foreach ( $customers['results'] as $customer ) {

					$saved_filters = get_user_meta( $customer->user->ID, get_current_blog_id() . '-wpsc-tl-saved-filters', true );
					if ( ! $saved_filters ) {
						continue;
					}

					foreach ( $saved_filters as $key => $filter ) {
						$filters = self::upgrade_condition( str_replace( '^^', '\n', $filter['filters'] ), 'AND' );
						$saved_filters[ $key ]['filters'] = str_replace( '\n', PHP_EOL, $filters );
					}
					update_user_meta( $customer->user->ID, get_current_blog_id() . '-wpsc-tl-saved-filters', $saved_filters );
				}
			} else {

				WPSC_Scheduled_Task::destroy( $task );
				delete_transient( 'wpsc_upgrade_saved_filter_conditions_cursor' );
			}

			if ( $current_page == $task->pages ) {

				WPSC_Scheduled_Task::destroy( $task );
				delete_transient( 'wpsc_upgrade_saved_filter_conditions_cursor' );

			} else {

				set_transient( 'wpsc_upgrade_saved_filter_conditions_cursor', $current_page, MINUTE_IN_SECONDS * 60 * 48 );
				return;
			}
		}

		/**
		 * Upgrade and return conditions json
		 *
		 * @param string $conditions - conditions json string.
		 * @param string $relation - AND or OR.
		 * @version 3.1.5
		 * @return string
		 */
		public static function upgrade_condition( $conditions, $relation ) {

			if ( ! $conditions || $conditions == '[]' ) {
				return '[]';
			}

			$conditions = json_decode( html_entity_decode( $conditions ), true );
			$slug_arr = array_keys( $conditions );
			if ( is_numeric( $slug_arr[0] ) ) {
				return wp_json_encode( $conditions );
			}

			$and_conditions = array();
			$or_conditions = array();

			foreach ( $conditions as $slug => $condition ) {
				if ( preg_match( '/^cf_\w+$/', $slug ) ) {
					$slug = str_replace( 'cf_', '', $slug );
				}
				$temp = array_merge( array( 'slug' => $slug ), $condition );
				if ( $relation == 'AND' ) {
					$and_conditions[] = array( $temp );
				} else {
					$or_conditions[] = $temp;
				}
			}

			if ( $relation == 'OR' && $or_conditions ) {
				$and_conditions[] = $or_conditions;
			}

			return wp_json_encode( $and_conditions );
		}

		/**
		 * Repaire setting conditions impacted in v3.1.5
		 *
		 * @param WPSC_Scheduled_Task $task - task object.
		 * @version 3.1.6
		 * @return void
		 */
		public static function repaire_setting_conditions( $task ) {

			// Visibility conditions.
			$tff = get_option( 'wpsc-tff' );
			foreach ( $tff as $slug => $properties ) {

				$relation = isset( $properties['relation'] ) ? $properties['relation'] : null;
				$tff[ $slug ]['visibility'] = self::convert_repair_conditions( $properties['visibility'], $relation );
			}
			update_option( 'wpsc-tff', $tff );

			// Email notifications.
			$email_templates = get_option( 'wpsc-email-templates' );
			foreach ( $email_templates as $index => $properties ) {
				$relation = isset( $properties['relation'] ) ? $properties['relation'] : null;
				$email_templates[ $index ]['conditions'] = self::convert_repair_conditions( $properties['conditions'], $relation );
			}
			update_option( 'wpsc-email-templates', $email_templates );

			// Default agent filters.
			$atl_filters = get_option( 'wpsc-atl-default-filters' );
			foreach ( $atl_filters as $slug => $properties ) {
				if ( ! is_numeric( $slug ) ) {
					continue;
				}
				$atl_filters[ $slug ]['filters'] = self::convert_repair_conditions( $properties['filters'] );
			}
			update_option( 'wpsc-atl-default-filters', $atl_filters );

			// Default customer filters.
			$ctl_filters = get_option( 'wpsc-ctl-default-filters' );
			foreach ( $ctl_filters as $slug => $properties ) {
				if ( ! is_numeric( $slug ) ) {
					continue;
				}
				$ctl_filters[ $slug ]['filters'] = self::convert_repair_conditions( $properties['filters'] );
			}
			update_option( 'wpsc-ctl-default-filters', $ctl_filters );

			// Assigned agent rules filters.
			if ( class_exists( 'WPSC_AAR' ) ) {
				$rules = get_option( 'wpsc-aar-rules', array() );
				foreach ( $rules as $index => $properties ) {
					$relation = isset( $properties['relation'] ) ? $properties['relation'] : null;
					$rules[ $index ]['conditions'] = self::convert_repair_conditions( $properties['conditions'], $relation );
				}
				update_option( 'wpsc-aar-rules', $rules );
			}

			// SLA policy filters.
			if ( class_exists( 'WPSC_SLA' ) ) {
				$policies = get_option( 'wpsc-sla-policies', array() );
				foreach ( $policies as $index => $properties ) {
					$relation = isset( $properties['relation'] ) ? $properties['relation'] : null;
					$policies[ $index ]['conditions'] = self::convert_repair_conditions( $properties['conditions'], $relation );
				}
				update_option( 'wpsc-sla-policies', $policies );
			}

			WPSC_Scheduled_Task::destroy( $task );
		}

		/**
		 * Repair saved filter conditions for all customers
		 *
		 * @param WPSC_Scheduled_Task $task - task object.
		 * @version 3.1.6
		 * @return void
		 */
		public static function repaire_saved_filter_conditions( $task ) {

			global $wpdb;

			$current_page = get_transient( 'wpsc_repair_saved_filter_conditions_cursor' );
			if ( false === $current_page ) {

				$total_items = $wpdb->get_var( "SELECT COUNT(id) FROM {$wpdb->prefix}psmsc_customers" );
				$total_pages = ceil( $total_items / 50 );
				$task->pages = $total_pages;
				$task->save();

				$current_page = 0;
				set_transient( 'wpsc_repair_saved_filter_conditions_cursor', $current_page, MINUTE_IN_SECONDS * 60 * 48 );
			}

			$current_page++;

			$customers = WPSC_Customer::find(
				array(
					'items_per_page' => 50,
					'page_no'        => $current_page,
					'meta_query'     => array(
						'relation' => 'AND',
						array(
							'slug'    => 'custom_query',
							'compare' => '=',
							'val'     => 'c.user>0',
						),
					),
				)
			);

			if ( $customers['results'] ) {

				foreach ( $customers['results'] as $customer ) {

					$saved_filters = get_user_meta( $customer->user->ID, get_current_blog_id() . '-wpsc-tl-saved-filters', true );
					if ( ! $saved_filters ) {
						continue;
					}

					foreach ( $saved_filters as $key => $filter ) {
						$filters = self::convert_repair_conditions( str_replace( PHP_EOL, '\n', $filter['filters'] ) );
						$saved_filters[ $key ]['filters'] = str_replace( '\n', PHP_EOL, $filters );
					}
					update_user_meta( $customer->user->ID, get_current_blog_id() . '-wpsc-tl-saved-filters', $saved_filters );
				}
			} else {

				WPSC_Scheduled_Task::destroy( $task );
				delete_transient( 'wpsc_repair_saved_filter_conditions_cursor' );
			}

			if ( $current_page == $task->pages ) {

				WPSC_Scheduled_Task::destroy( $task );
				delete_transient( 'wpsc_repair_saved_filter_conditions_cursor' );

			} else {

				set_transient( 'wpsc_repair_saved_filter_conditions_cursor', $current_page, MINUTE_IN_SECONDS * 60 * 48 );
				return;
			}
		}

		/**
		 * Convert repaired conditions to valid format depending on relation
		 *
		 * @param string $conditions - json string for conditions.
		 * @param string $relation - AND or OR relation.
		 * @return string
		 */
		public static function convert_repair_conditions( $conditions, $relation = 'AND' ) {

			if ( ! $conditions || $conditions == '[]' ) {
				return '[]';
			}

			$conditions = json_decode( html_entity_decode( $conditions ), true );
			$slug_arr = array_keys( $conditions );
			if ( $relation === null && is_numeric( $slug_arr[0] ) ) {
				return wp_json_encode( $conditions );
			}

			$temp = self::repair_conditions( $conditions );
			$and_conditions = array();
			$or_conditions = array();
			foreach ( $temp as $con ) {
				if ( $relation == 'AND' ) {
					$and_conditions[] = array( $con );
				} else {
					$or_conditions[] = $con;
				}
			}

			if ( $relation == 'OR' && $or_conditions ) {
				$and_conditions[] = $or_conditions;
			}

			return wp_json_encode( $and_conditions );
		}

		/**
		 * Repair conditions changed in v3.1.5
		 *
		 * @param string $conditions - conditions json string.
		 * @version 3.1.6
		 * @return array
		 */
		public static function repair_conditions( $conditions ) {

			if ( ! is_array( $conditions ) ) {
				return array();
			}

			$temp = array();
			foreach ( $conditions as $key => $con ) {

				if ( isset( $con['slug'] ) && ! is_numeric( $con['slug'] ) ) {

					$temp[] = $con;

				} elseif (
					! isset( $con['slug'] ) ||
					( isset( $con['slug'] ) && is_numeric( $con['slug'] ) )
				) {

					$temp1 = self::repair_conditions( $con );
					if ( is_array( $temp1 ) && $temp1 ) {
						foreach ( $temp1 as $con1 ) {
							if ( isset( $con1['slug'] ) && ! is_numeric( $con1['slug'] ) ) {
								$temp[] = $con1;
							}
						}
					}
				}
			}
			return $temp;
		}

		/**
		 * Update attachment status in database
		 *
		 * @param WPSC_Scheduled_Task $task - task object.
		 * @version 3.1.9
		 * @return void
		 */
		public static function update_ticket_attachment_status( $task ) {

			$items_per_page = 100;

			if ( ! $task->pages ) {
				$attachments = WPSC_Attachment::find(
					array(
						'items_per_page' => $items_per_page,
						'page_no'        => 1,
					)
				);

				if ( $attachments['total_items'] === 0 ) {
					WPSC_Scheduled_Task::destroy( $task );
					return;
				}

				$task->pages = $attachments['total_pages'];
				$task->save();
			}

			$attachments = WPSC_Attachment::find(
				array(
					'items_per_page' => $items_per_page,
					'page_no'        => $task->pages,
				)
			);

			// save remaining pages of the task. it will be used in ui section.
			$task->pages = $task->pages - 1;
			$task->save();

			foreach ( $attachments['results'] as $attachment ) {

				if ( ! $attachment->ticket_id && ! $attachment->customer_id ) {

					$attachment->is_active = 0;

				} elseif ( $attachment->ticket_id ) {

					$ticket = new WPSC_Ticket( $attachment->ticket_id );
					if ( $ticket->id ) {
						$attachment->is_active = 1;
					} else {
						$attachment->is_active = 0;
					}
				} elseif ( $attachment->customer_id ) {

					$customer = new WPSC_Customer( $attachment->customer_id );
					if ( $customer->id ) {
						$attachment->is_active = 1;
					} else {
						$attachment->is_active = 0;
					}
				}

				$attachment->save();
			}

			if ( ! $task->pages ) {
				WPSC_Scheduled_Task::destroy( $task );
				return;
			}
		}
	}
endif;
