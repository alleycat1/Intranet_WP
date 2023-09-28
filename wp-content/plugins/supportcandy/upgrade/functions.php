<?php
/**
 * Includes common functions for upgrade
 *
 * @package SupportCandy
 */

/**
 * Return email notification events mapping
 *
 * @return array
 */
function wpsc_get_en_event_map() {

	return array(
		'new_ticket'      => 'create-ticket',
		'ticket_reply'    => 'reply-ticket',
		'change_status'   => 'change-ticket-status',
		'assign_agent'    => 'change-assignee',
		'delete_ticket'   => 'delete-ticket',
		'private_note'    => 'submit-note',
		'change_category' => 'change-ticket-category',
		'change_priority' => 'change-ticket-priority',
		'out_of_sla'      => 'out-of-sla',
		'ticket_rating'   => 'ticket-feedback',
		'ticket_feedback' => 'ticket-feedback',
	);
}

/**
 * Upgrade macros
 *
 * @param string $str - string to upgrade macros in.
 * @return string
 */
function wpsc_upgrade_macros( $str ) {

	$cf_map = get_option( 'wpsc_upgrade_cf_term_id_map' );
	preg_match_all( '/\{(.*?)\}/', $str, $matches );
	if ( $matches[1] ) {
		foreach ( array_unique( $matches[1] ) as $slug ) {
			$field = get_term_by( 'slug', $slug, 'wpsc_ticket_custom_fields' );
			if ( $field !== false && isset( $cf_map[ $field->term_id ] ) ) {
				$cf = wpsc_get_cf_by( 'id', $cf_map[ $field->term_id ] );
				if ( $cf->id ) {
					$str = str_replace( '{' . $slug . '}', '{{' . $cf->slug . '}}', $str );
				} else {
					$str = str_replace( '{' . $slug . '}', '', $str );
				}
			} elseif ( $field !== false ) {
				$str = str_replace( '{' . $slug . '}', '{{' . $slug . '}}', $str );
			} else {
				$str = str_replace( '{' . $slug . '}', '', $str );
			}
		}
	}
	return $str;
}

/**
 * Extract BCC from old template
 *
 * @param mixed $template - term object for email template.
 * @return array
 */
function wpsc_en_extract_bcc( $template ) {

	$recipients = get_term_meta( $template->term_id, 'recipients', true );
	$recipient_map = array(
		'customer'                  => 'customer',
		'assigned_agent'            => 'assignee',
		'usergroup_supervisors'     => 'usergroup-supervisors',
		'usergroup_members'         => 'usergroup-members',
		'extra_ticket_users'        => 'add-recipients',
		'previously_assigned_agent' => 'prev-assignee',
		'current_user'              => 'current-user',
	);
	$general_recipients = array();
	$agent_roles = array();
	foreach ( $recipients as $recipient ) {
		if ( is_numeric( $recipient ) ) {
			$agent_roles[] = $recipient;
		} else {
			$general_recipients[] = $recipient_map[ $recipient ];
		}
	}
	$additional_recipients = get_term_meta( $template->term_id, 'extra_recipients', true );
	return array(
		'general-recipients' => $general_recipients,
		'agent-roles'        => $agent_roles,
		'custom'             => $additional_recipients,
	);
}

/**
 * Add customer record if not exists and return customer object
 *
 * @param string $name - Name of the customer.
 * @param string $email - Email address of the customer.
 * @return stdClass
 */
function wpsc_import_customer( $name, $email ) {

	global $wpdb;
	$customer = wpsc_get_customer_by( 'email', $email );
	if ( ! $customer ) {
		$user = get_user_by( 'email', $email );
		$user_id = $user ? $user->ID : 0;
		$user_display_name = $user ? $user->display_name : $name;
		$wpdb->insert(
			$wpdb->prefix . 'psmsc_customers',
			array(
				'user'  => $user_id,
				'name'  => $user_display_name,
				'email' => $email,
			)
		);
		$customer = wpsc_get_customer_by( 'id', $wpdb->insert_id );
	}
	return $customer;
}

/**
 * Delete old ticket data and its threads
 *
 * @param WP_Post $ticket - ticket post object.
 * @return void
 */
function wpsc_upgrade_delete_ticket( $ticket ) {

	$ticket_id = get_post_meta( $ticket->ID, 'ticket_id', true );

	// delete thread posts.
	$threads = get_posts(
		array(
			'post_type'      => 'wpsc_ticket_thread',
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'ASC',
			'posts_per_page' => -1,
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'     => 'ticket_id',
					'value'   => $ticket_id,
					'compare' => '=',
				),
			),
		)
	);
	foreach ( $threads as $thread ) {
		wp_delete_post( $thread->ID, true );
	}
	// delete ticket.
	wp_delete_post( $ticket->ID, true );
}

/**
 * Return installed plugin info
 *
 * @return array
 */
function wpsc_upgrade_get_installed_plugin_info() {

	$installed_addons = array(
		0  => array(
			'name'        => 'Automatic close tickets',
			'installer'   => 'WPSC_ATC_Installation',
			'plugin_file' => WP_PLUGIN_DIR . '/wpsc-automatic-close-ticket/wpsc-automatic-close-ticket.php',
		),
		1  => array(
			'name'        => 'Canned reply',
			'installer'   => 'WPSC_CR_Installation',
			'plugin_file' => WP_PLUGIN_DIR . '/wpsc-canned-reply/wpsc-canned-reply.php',
		),
		2  => array(
			'name'        => 'Export tickets',
			'installer'   => 'WPSC_EXPORT_Installation',
			'plugin_file' => WP_PLUGIN_DIR . '/wpsc-export-ticket/wpsc-export-ticket.php',
		),
		3  => array(
			'name'        => 'Email piping',
			'installer'   => 'WPSC_EP_Installation',
			'plugin_file' => WP_PLUGIN_DIR . '/wpsc-email-piping/wpsc-email-piping.php',
		),
		4  => array(
			'name'        => 'SLA',
			'installer'   => 'WPSC_SLA_Installation',
			'plugin_file' => WP_PLUGIN_DIR . '/wpsc-sla/wpsc-sla.php',
		),
		5  => array(
			'name'        => 'WooCommerce integration',
			'installer'   => 'WPSC_WOO_Installation',
			'plugin_file' => WP_PLUGIN_DIR . '/wpsc-woocommerce/wpsc-woocommerce.php',
		),
		6  => array(
			'name'        => 'Reports',
			'installer'   => 'WPSC_RP_Installation',
			'plugin_file' => WP_PLUGIN_DIR . '/wpsc-reports/wpsc-reports.php',
		),
		7  => array(
			'name'        => 'Usergroups',
			'installer'   => 'WPSC_UG_Installation',
			'plugin_file' => WP_PLUGIN_DIR . '/wpsc-usergroup/wpsc-usergroup.php',
		),
		8  => array(
			'name'        => 'EDD intergration',
			'installer'   => 'WPSC_EDD_Installation',
			'plugin_file' => WP_PLUGIN_DIR . '/wpsc-edd/wpsc-edd.php',
		),
		9  => array(
			'name'        => 'Gravity Forms integration',
			'installer'   => 'WPSC_GF_Installation',
			'plugin_file' => WP_PLUGIN_DIR . '/wpsc-gravity-forms/wpsc-gravity-form-integration.php',
		),
		10 => array(
			'name'        => 'Assign agent rules',
			'installer'   => 'WPSC_AAR_Installation',
			'plugin_file' => WP_PLUGIN_DIR . '/wpsc-assign-agent-rules/wpsc-assign-agent-rules.php',
		),
		11 => array(
			'name'        => 'FAQ integration',
			'installer'   => 'WPSC_FAQ_Installation',
			'plugin_file' => WP_PLUGIN_DIR . '/wpsc-ultimate-faq/wpsc-ultimate-faq.php',
		),
		12 => array(
			'name'        => 'Knowledgebase integration',
			'installer'   => 'WPSC_KB_Installation',
			'plugin_file' => WP_PLUGIN_DIR . '/wpsc-pressapps-knowledge-base/wpsc-pressapps-knowledge-base.php',
		),
		13 => array(
			'name'        => 'Timer',
			'installer'   => 'WPSC_Timer_Installation',
			'plugin_file' => WP_PLUGIN_DIR . '/wpsc-timer/wpsc-timer.php',
		),
		14 => array(
			'name'        => 'Schedule tickets',
			'installer'   => 'WPSC_ST_Installation',
			'plugin_file' => WP_PLUGIN_DIR . '/wpsc-schedule-tickets/wpsc-schedule-tickets.php',
		),
		15 => array(
			'name'        => 'Satisfaction survey',
			'installer'   => 'WPSC_SF_Installation',
			'plugin_file' => WP_PLUGIN_DIR . '/wpsc-satisfaction-survey/wpsc-satisfaction-survey.php',
		),
		16 => array(
			'name'        => 'Agentgroups',
			'installer'   => 'WPSC_AG_Installation',
			'plugin_file' => WP_PLUGIN_DIR . '/wpsc-agentgroup/wpsc-agentgroup.php',
		),
		17 => array(
			'name'        => 'Private credentials',
			'installer'   => 'WPSC_PC_Installation',
			'plugin_file' => WP_PLUGIN_DIR . '/wpsc-private-credentials/wpsc_private_credentials.php',
		),
		18 => array(
			'name'        => 'Print ticket',
			'installer'   => 'WPSC_PRINT_Installation',
			'plugin_file' => WP_PLUGIN_DIR . '/wpsc-print-ticket/wpsc-print-ticket.php',
		),
	);

	foreach ( $installed_addons as $index => $addon ) {

		$plugin_data = array();
		$is_active = 0;
		if ( file_exists( $addon['plugin_file'] ) ) {
			$plugin_data = get_plugin_data( $addon['plugin_file'] );
			preg_match( '/plugins\/(.*\.php)/', $addon['plugin_file'], $matches );
			$is_active = is_plugin_active( $matches[1] );
		}
		$installed_addons[ $index ]['is_installed'] = $plugin_data ? true : false;
		$installed_addons[ $index ]['version'] = $plugin_data ? $plugin_data['Version'] : 0;
		$installed_addons[ $index ]['is_active'] = $is_active;
	}

	return $installed_addons;
}

/**
 * Enqueue scripts for upgrade
 *
 * @return void
 */
function wpsc_upgrade_enqueue_scripts() {

	// jquery.
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_style( 'wpsc-jquery-ui', WPSC_PLUGIN_URL . 'asset/css/jquery-ui.css', array(), WPSC_VERSION );

	// Progrss bar.
	wp_enqueue_script( 'jquery-ui-progressbar' );

	// localize scripts.
	wp_enqueue_script( 'wpsc-admin', WPSC_PLUGIN_URL . 'asset/js/admin.js', array( 'jquery' ), WPSC_VERSION, true );
	wp_localize_script(
		'wpsc-admin',
		'supportcandy',
		array(
			'ajax_url'    => admin_url( 'admin-ajax.php' ),
			'plugin_url'  => WPSC_PLUGIN_URL,
			'version'     => WPSC_VERSION,
			'loader_html' => wpsc_upgrade_loader_html(),
		)
	);
}

/**
 * Loader html
 *
 * @return string
 */
function wpsc_upgrade_loader_html() {

	ob_start();
	?>
	<div class="wpsc-loader">
		<img src="<?php echo esc_url( WPSC_PLUGIN_URL . 'asset/images/loader.gif' ); ?>" alt="Loading..." />
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Check whether current use is site admin or not
 *
 * @return boolean
 */
function wpsc_upgrade_is_site_admin() {

	global $current_user;
	return $current_user->ID && $current_user->has_cap( 'manage_options' ) ? true : false;
}

/**
 * Send an email for site administrator for upgrade only once.
 *
 * @return void
 */
function wpsc_upgrade_send_admin_email() {

	$is_sent = get_option( 'wpsc_upgrade_v3_admin_email', 0 );
	if ( ! $is_sent ) {
		update_option( 'wpsc_upgrade_v3_admin_email', 1 );
		$to           = get_bloginfo( 'admin_email' );
		$subject      = '[SupportCandy] Action needed!';
		$upgrade_link = admin_url( 'admin.php?page=wpsc-tickets' );
		$body         = '<p>Hello,</p><p>This is to inform you that SupportCandy has been updated to version v3.0.0. This version has significant changes that may break your current workflow. To avoid this, we need you to manually review the changes and proceed to the database upgrade.</p><p><a href="' . $upgrade_link . '">Click here</a> to review the changes.</p><p>If you are not yet ready for this update, you can revert it back to the old version manually.</p><p>Please note that SupportCandy is in disabled mode. That means neither your customers can create tickets nor your agents can view/reply to tickets until you do this.</p><p>Regards,</p><p>SupportCandy Team</p>';
		$headers      = array( 'Content-Type: text/html; charset=UTF-8' );
		wp_mail( $to, $subject, $body, $headers );
	}
}

/**
 * Register post types in order to get data
 *
 * @return void
 */
function wpsc_upgrade_register_post_type() {

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
 * Insert custom field
 *
 * @param array $data - array of data to create custom field.
 * @return array
 */
function wpsc_upgrade_insert_custom_field( $data ) {

	global $wpdb;

	// insert record.
	$wpdb->insert(
		$wpdb->prefix . 'psmsc_custom_fields',
		$data
	);

	// update slug.
	$id = $wpdb->insert_id;
	$slug = 'cust_' . $id;
	$wpdb->update(
		$wpdb->prefix . 'psmsc_custom_fields',
		array( 'slug' => $slug ),
		array( 'id' => $id )
	);

	return array(
		'id'   => $id,
		'slug' => $slug,
	);
}

/**
 * Get custom field row by id or slug.
 *
 * @param string $field - id or slug.
 * @param string $value - field value.
 * @return Collection
 */
function wpsc_get_cf_by( $field, $value ) {

	global $wpdb;

	if ( $field == 'id' ) {

		return $wpdb->get_row( "SELECT * from {$wpdb->prefix}psmsc_custom_fields WHERE id = " . $value );

	} else {

		return $wpdb->get_row( "SELECT * from {$wpdb->prefix}psmsc_custom_fields WHERE slug = '" . $value . "'" );
	}
}

/**
 * Get customer by either id, email or user_id
 *
 * @param string $field - either id, email or user_id.
 * @param string $value - field value.
 * @return stdClass
 */
function wpsc_get_customer_by( $field, $value ) {

	global $wpdb;
	if ( $field == 'id' ) {

		return $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}psmsc_customers WHERE id = " . $value );

	} elseif ( $field == 'email' ) {

		return $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}psmsc_customers WHERE email = '" . $value . "'" );

	} else {

		return $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}psmsc_customers WHERE user = '" . $value . "'" );
	}
}

/**
 * Get agent by either id, customer_id or user_id
 *
 * @param string $field - either id, customer_id or user_id.
 * @param string $value - field value.
 * @return stdClass
 */
function wpsc_get_agent_by( $field, $value ) {

	global $wpdb;
	if ( $field == 'id' ) {

		return $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}psmsc_agents WHERE id = " . $value );

	} elseif ( $field == 'customer_id' ) {

		return $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}psmsc_agents WHERE customer = '" . $value . "'" );

	} else {

		return $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}psmsc_agents WHERE user = '" . $value . "'" );
	}
}

/**
 * Get custom field options
 *
 * @param int $cf_id - custom field id.
 * @return array
 */
function wpsc_get_cf_options( $cf_id ) {

	global $wpdb;
	$sql = 'SELECT * FROM ' . $wpdb->prefix . 'psmsc_options WHERE custom_field = ' . $cf_id;
	return $wpdb->get_results( $sql );
}

/**
 * Get ticket meta for version 2
 *
 * @param integer $ticket_id - ticket id.
 * @param string  $meta_key - meta key.
 * @param boolean $flag - flag.
 * @return string
 */
function get_ticket_meta( $ticket_id, $meta_key, $flag = false ) {

	global $wpdb;
	if ( $flag ) {

		$meta_value = $wpdb->get_var( "SELECT meta_value FROM {$wpdb->prefix}wpsc_ticketmeta WHERE ticket_id= " . $ticket_id . " AND meta_key = '" . $meta_key . "'" );
		$meta_value = stripslashes( $meta_value ) ? stripslashes( $meta_value ) : '';

	} else {

		$meta_value = array();
		$results = $wpdb->get_results( "SELECT meta_value FROM {$wpdb->prefix}wpsc_ticketmeta WHERE ticket_id= " . $ticket_id . " AND meta_key = '" . $meta_key . "'" );
		if ( $results ) {
			foreach ( $results as $result ) {
				$meta_value[] = stripslashes( $result->meta_value );
			}
		}
	}
	return $meta_value;
}

/**
 * Convert seconds to date interval string
 *
 * @param integer $seconds - seconds.
 * @return string
 */
function convert_sec_to_date_interval_string( $seconds ) {

	$m = floor( ( $seconds % 3600 ) / 60 );
	$h = floor( ( $seconds % 86400 ) / 3600 );
	$days = floor( ( $seconds % 2592000 ) / 86400 );

	$str = 'P';

	if ( $days ) {

		$str .= $days . 'D';

	}

	if ( $h || $m ) {

		$str .= 'T';
		if ( $h ) {
			$str .= $h . 'H';
		}
		if ( $m ) {
			$str .= $m . 'M';
		}
	}

	if ( $str === 'P' ) {
		$str = 'PT0M';
	}

	return $str;
}

/**
 * Upgrade email notification conditions, Assign agent rules, sla policies.
 *
 * @param string $prev_conditions - conditions.
 * @return string
 */
function upgrade_conditions( $prev_conditions ) {

	global $wpdb;
	if ( ! $prev_conditions ) {
		return '{}';
	}

	$cf_map = get_option( 'wpsc_upgrade_cf_term_id_map' );
	$options_map = get_option( 'wpsc_upgrade_cf_options_map' );
	$status_map = get_option( 'wpsc_upgrade_status_map' );
	$category_map = get_option( 'wpsc_upgrade_category_map' );
	$priority_map = get_option( 'wpsc_upgrade_priority_map' );
	$usergroup_map = get_option( 'wpsc_upgrade_ug_term_id_map' );

	$other_cnds = array(
		'agent_created'     => 'submitted_by',
		'user_type'         => 'cf_user_type',
		'user_wp_roles'     => 'user_role',
		'ep_forwarded_from' => 'en_from',
		'usergroup'         => 'cf_usergroups',
	);

	$conditions = array();
	$prev_conditions = json_decode( $prev_conditions, true );
	foreach ( $prev_conditions as $key => $val ) {

		$compare = '';

		switch ( $val['compare'] ) {
			case 'match':
				$compare = 'IN';
				break;

			case 'not_match':
				$compare = 'NOT IN';
				break;

			case 'contain':
				$compare = 'LIKE';
				break;
		}

		if ( array_key_exists( $val['field'], $cf_map ) ) {

			$cf = wpsc_get_cf_by( 'id', $cf_map[ $val['field'] ] );
			if ( ! $cf->id || in_array( $cf->type, array( 'df_customer_name' ) ) ) {
				continue;
			}

			if ( array_key_exists( 'cf_' . $cf->slug, $conditions ) &&
				(
					( in_array( $conditions[ 'cf_' . $cf->slug ]['operator'], array( 'IN', 'LIKE' ) ) && $compare == 'NOT IN' ) ||
					( $conditions[ 'cf_' . $cf->slug ]['operator'] == 'NOT IN' && in_array( $compare, array( 'IN', 'LIKE' ) ) )
				)
			) {
				unset( $conditions[ 'cf_' . $cf->slug ] );
				continue;
			}

			switch ( $cf->slug ) {

				case 'status':
					if ( array_key_exists( 'cf_status', $conditions ) ) {

						$conditions['cf_status']['operand_val_1'][] = $status_map[ $val['cond_val'] ];

					} else {

						$conditions['cf_status'] = array(
							'operator'      => $compare,
							'operand_val_1' => array(
								$status_map[ $val['cond_val'] ],
							),
						);
					}
					break;

				case 'category':
					if ( array_key_exists( 'cf_category', $conditions ) ) {

						$conditions['cf_category']['operand_val_1'][] = $category_map[ $val['cond_val'] ];

					} else {

						$conditions['cf_category'] = array(
							'operator'      => $compare,
							'operand_val_1' => array(
								$category_map[ $val['cond_val'] ],
							),
						);
					}
					break;

				case 'priority':
					if ( array_key_exists( 'cf_priority', $conditions ) ) {

						$conditions['cf_priority']['operand_val_1'][] = $priority_map[ $val['cond_val'] ];

					} else {

						$conditions['cf_priority'] = array(
							'operator'      => $compare,
							'operand_val_1' => array(
								$priority_map[ $val['cond_val'] ],
							),
						);
					}
					break;

				case 'assigned_agent':
					$map = get_option( 'wpsc_upgrade_agent_map' );
					if ( array_key_exists( 'cf_' . $cf->slug, $conditions ) ) {

						$conditions[ 'cf_' . $cf->slug ]['operand_val_1'][] = $map[ $val['cond_val'] ];

					} else {

						$conditions[ 'cf_' . $cf->slug ] = array(
							'operator'      => $compare,
							'operand_val_1' => array(
								$map[ $val['cond_val'] ],
							),
						);
					}
					break;

				case 'description':
					if ( $compare == 'IN' || $compare == 'LIKE' ) {

						if ( array_key_exists( 'cf_' . $cf->slug, $conditions ) ) {

							$prev_val = array( $conditions[ 'cf_' . $cf->slug ]['operand_val_1'] );
							$prev_val[] = $val['cond_val'];
							$conditions[ 'cf_' . $cf->slug ]['operand_val_1'] = implode( "\n", $prev_val );

						} else {

							$conditions[ 'cf_' . $cf->slug ] = array(
								'operator'      => 'LIKE',
								'operand_val_1' => $val['cond_val'],
							);
						}
					}
					break;

				default:
					if ( in_array( $cf->type, array( 'cf_checkbox', 'cf_radio_button', 'cf_single_select' ) ) ) {

						if ( array_key_exists( 'cf_' . $cf->slug, $conditions ) ) {

							$conditions[ 'cf_' . $cf->slug ]['operand_val_1'][] = $options_map[ $cf->id ][ $val['cond_val'] ];

						} else {

							$conditions[ 'cf_' . $cf->slug ] = array(
								'operator'      => $compare,
								'operand_val_1' => array(
									$options_map[ $cf->id ][ $val['cond_val'] ],
								),
							);
						}
					} elseif ( in_array( $cf->type, array( 'df_customer_email' ) ) ) {

						$customer = wpsc_get_customer_by( 'email', $val['cond_val'] );
						if ( $customer ) {
							if ( array_key_exists( 'cf_customer', $conditions ) ) {

								$conditions['cf_customer']['operand_val_1'][] = $customer->id;

							} else {

								$conditions['cf_customer'] = array(
									'operator'      => $compare,
									'operand_val_1' => array(
										$customer->id,
									),
								);
							}
						}
					} elseif ( $cf->type == 'cf_woo_product' || $cf->type == 'cf_edd_product' ) {

						if ( array_key_exists( 'cf_' . $cf->slug, $conditions ) ) {

							$conditions[ 'cf_' . $cf->slug ]['operand_val_1'][] = $val['cond_val'];

						} else {

							$conditions[ 'cf_' . $cf->slug ] = array(
								'operator'      => $compare,
								'operand_val_1' => array(
									$val['cond_val'],
								),
							);
						}
					} else {
						if ( array_key_exists( 'cf_' . $cf->slug, $conditions ) ) {

							$prev_val = array( $conditions[ 'cf_' . $cf->slug ]['operand_val_1'] );
							$prev_val[] = $val['cond_val'];
							$conditions[ 'cf_' . $cf->slug ]['operand_val_1'] = implode( "\n", $prev_val );

						} else {

							$conditions[ 'cf_' . $cf->slug ] = array(
								'operator'      => $compare,
								'operand_val_1' => $val['cond_val'],
							);
						}
					}
			}
		} elseif ( array_key_exists( $val['field'], $other_cnds ) ) {

			$new_slug = $other_cnds[ $val['field'] ];
			if ( array_key_exists( $new_slug, $conditions ) &&
				(
					( in_array( $conditions[ $new_slug ]['operator'], array( 'IN', 'LIKE' ) ) && $compare == 'NOT IN' ) ||
					( $conditions[ $new_slug ]['operator'] == 'NOT IN' && in_array( $compare, array( 'IN', 'LIKE' ) ) )
				)
			) {
				unset( $conditions[ $new_slug ] );
				continue;
			}

			switch ( $val['field'] ) {

				case 'agent_created':
					if ( array_key_exists( 'submitted_by', $conditions ) ) {
						unset( $conditions['submitted_by'] );
					} else {
						$value = $val['compare'] == 'not_match' && $val['cond_val'] == 'user' ? 'agent' : 'user';
						$conditions['submitted_by'] = array(
							'operator'      => '=',
							'operand_val_1' => $value,
						);
					}
					break;

				case 'user_type':
					if ( array_key_exists( 'cf_user_type', $conditions ) ) {
						unset( $conditions['cf_user_type'] );
					} else {
						$value = $val['cond_val'] == 'user' ? 'registered' : 'guest';
						$value = $val['compare'] == 'not_match' && $val['cond_val'] == 'user' ? 'guest' : 'registered';
						$conditions['cf_user_type'] = array(
							'operator'      => '=',
							'operand_val_1' => $value,
						);
					}
					break;

				case 'user_wp_roles':
					if ( array_key_exists( 'user_role', $conditions ) ) {
						$conditions['user_role']['operand_val_1'][] = $val['cond_val'];
					} else {

						$conditions['user_role'] = array(
							'operator'      => $compare,
							'operand_val_1' => array(
								$val['cond_val'],
							),
						);
					}
					break;

				case 'ep_forwarded_from':
					if ( array_key_exists( 'en_from', $conditions ) ) {
						$prev_val = array( $conditions['en_from']['operand_val_1'] );
						$prev_val[] = $val['cond_val'];
						$conditions['en_from']['operand_val_1'] = implode( "\n", $prev_val );
					} else {

						$conditions['en_from'] = array(
							'operator'      => $compare,
							'operand_val_1' => $val['cond_val'],
						);
					}
					break;

				case 'usergroup':
					if ( array_key_exists( 'cf_usergroups', $conditions ) ) {

						$conditions['cf_usergroups']['operand_val_1'][] = $usergroup_map[ $val['cond_val'] ];
					} else {

						$conditions['cf_usergroups'] = array(
							'operator'      => $compare,
							'operand_val_1' => array(
								$usergroup_map[ $val['cond_val'] ],
							),
						);
					}
					break;
			}
		}
	}

	return wp_json_encode( $conditions );
}

/**
 * Check whether given customer is an agent
 *
 * @param int $customer_id - customer id.
 * @return boolean
 */
function wpsc_is_agent( $customer_id ) {

	global $wpdb;
	$agent_id = $wpdb->get_var( "SELECT id FROM {$wpdb->prefix}psmsc_agents WHERE customer = " . $customer_id );
	return $agent_id ? true : false;
}

/**
 * Perform sum of date intervals
 *
 * @param array $arr - date interval array.
 * @return DateInterval
 */
function wpsc_date_interval_sum( $arr ) {

	$response    = $arr[0];
	$arrau_count = count( $arr );
	for ( $i = 1; $i < $arrau_count; $i++ ) {
		$today     = new DateTime();
		$sum_today = clone $today;
		$sum_today->add( $response );
		$sum_today->add( $arr[ $i ] );
		$response = $today->diff( $sum_today );
	}
	return $response;
}

/**
 * Return string representation of date interval
 *
 * @param DateInterval $diff - date interval.
 * @return string
 */
function wpsc_date_interval_to_string( $diff ) {

	$str = 'P';

	if ( $diff->days ) {

		$str .= $diff->format( '%aD' );

	} elseif ( $diff->d ) {

		$str .= $diff->format( '%dD' );

	}

	if ( $diff->h || $diff->i ) {

		$str .= 'T';
		$str .= $diff->h ? $diff->format( '%hH' ) : '';
		$str .= $diff->i ? $diff->format( '%iM' ) : '';
	}

	if ( $str === 'P' ) {
		$str = 'PT0M';
	}

	return $str;
}

/**
 * Update total ticket count for the customer in database
 *
 * @param stdClass $customer - customer db object.
 * @return void
 */
function wpsc_update_customer_ticket_count( $customer ) {

	global $wpdb;
	$count = $wpdb->get_var( "SELECT count(id) from {$wpdb->prefix}psmsc_tickets WHERE is_active=1 AND customer=" . $customer->id );
	$wpdb->update(
		$wpdb->prefix . 'psmsc_customers',
		array( 'ticket_count' => $count ),
		array( 'id' => $customer->id )
	);
}

/**
 * Update customer usergroups
 *
 * @param stdClass $customer - customer db object.
 * @return void
 */
function wpsc_update_customer_usergroups( $customer ) {

	global $wpdb;
	$sql = 'SELECT * FROM ' . $wpdb->prefix . 'psmsc_usergroups WHERE members RLIKE \'(^|[|])' . $customer->id . '($|[|])\'';
	$usergroups = array_map(
		function( $group ) {
			return $group->id;
		},
		$wpdb->get_results( $sql )
	);
	if ( $usergroups ) {
		$wpdb->update(
			$wpdb->prefix . 'psmsc_customers',
			array( 'usergroups' => implode( '|', $usergroups ) ),
			array( 'id' => $customer->id )
		);
	}
}

/**
 * Return whether or not agent has given capability
 *
 * @param stdClass $agent - agent db object.
 * @param string   $cap - capability slug.
 * @return boolean
 */
function wpsc_agent_has_cap( $agent, $cap ) {

	$roles = get_option( 'wpsc-agent-roles', array() );
	if ( ! isset( $roles[ $agent->role ] ) ) {
		return false;
	}
	$role = $roles[ $agent->role ];
	return isset( $role['caps'][ $cap ] ) && $role['caps'][ $cap ] ? true : false;
}

/**
 * Reset unresoved count for given agent
 *
 * @param stdClass $agent - agent db object.
 * @return void
 */
function wpsc_agent_reset_unresolved_count( $agent ) {

	global $wpdb;
	$more_settings = get_option( 'wpsc-tl-ms-agent-view' );
	if ( ! $more_settings['unresolved-ticket-statuses'] ) {

		$wpdb->update(
			$wpdb->prefix . 'psmsc_agents',
			array( 'unresolved_count' => 0 ),
			array( 'id' => $agent->id )
		);
	} else {

		$sql = "SELECT count(id) FROM {$wpdb->prefix}psmsc_tickets WHERE is_active=1 AND status IN(" . implode( ',', $more_settings['unresolved-ticket-statuses'] ) . ') AND ';
		// system query.
		$where = array( 'customer=' . $agent->customer );
		if ( wpsc_agent_has_cap( $agent, 'view-assigned-me' ) ) {
			$where[] = 'assigned_agent=' . $agent->id;
		}
		if ( wpsc_agent_has_cap( $agent, 'view-unassigned' ) ) {
			$where[] = "assigned_agent=''";
		}
		if ( wpsc_agent_has_cap( $agent, 'view-assigned-others' ) ) {
			$where[] = "assigned_agent NOT IN( '', " . $agent->id . ' )';
		}

		// merge system query.
		$sql = $sql . '( ' . implode( ' OR ', $where ) . ' )';
		$count = $wpdb->get_var( $sql );

		// update in db.
		$wpdb->update(
			$wpdb->prefix . 'psmsc_agents',
			array( 'unresolved_count' => $count ),
			array( 'id' => $agent->id )
		);
	}
}

/**
 * Reset wordkload for given agent
 *
 * @param stdClass $agent - agent db object.
 * @return void
 */
function wpsc_agent_reset_workload( $agent ) {

	global $wpdb;
	$more_settings = get_option( 'wpsc-tl-ms-agent-view' );
	if ( ! $more_settings['unresolved-ticket-statuses'] ) {
		$wpdb->update(
			$wpdb->prefix . 'psmsc_agents',
			array( 'workload' => 0 ),
			array( 'id' => $agent->id )
		);
	} else {
		$sql = "SELECT count(id) FROM {$wpdb->prefix}psmsc_tickets WHERE is_active=1 AND status IN(" . implode( ',', $more_settings['unresolved-ticket-statuses'] ) . ') AND assigned_agent=' . $agent->id;
		$count = $wpdb->get_var( $sql );

		// update in db.
		$wpdb->update(
			$wpdb->prefix . 'psmsc_agents',
			array( 'workload' => $count ),
			array( 'id' => $agent->id )
		);
	}
}

/**
 * Destroy existing ticket
 *
 * @param int $ticket_id - ticket id to destroy.
 * @return void
 */
function wpsc_destroy_existing_ticket( $ticket_id ) {

	global $wpdb;

	// delete ticket record.
	$wpdb->delete(
		$wpdb->prefix . 'psmsc_tickets',
		array( 'id' => $ticket_id )
	);

	// delete threads.
	$wpdb->delete(
		$wpdb->prefix . 'psmsc_threads',
		array( 'ticket' => $ticket_id )
	);

	// delete attachments.
	$wpdb->delete(
		$wpdb->prefix . 'psmsc_attachments',
		array( 'ticket_id' => $ticket_id )
	);
}
