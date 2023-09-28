<?php
if ( ! function_exists( 'user_wpyar_answer_ticket' ) ) {
	function user_wpyar_answer_ticket() {
		 global $wpdb;
		if ( $_FILES ) {
			if ( $_FILES['updoc']['type'] == 'text/javascript' || $_FILES['updoc']['type'] == 'application/octet-stream' ) {
				echo 'error_valid_type';
				exit;
			}

			require_once ABSPATH . 'wp-admin' . '/includes/image.php';
			require_once ABSPATH . 'wp-admin' . '/includes/file.php';
			require_once ABSPATH . 'wp-admin' . '/includes/media.php';
			$file_handler = 'updoc';
			 global $post;
            $loop = get_posts('numberposts=1&order=DESC');
            $first = $loop[0]->ID;
            $post_id = intval($first) + 1;
			$attach_id    = media_handle_upload( $file_handler, sanitize_text_field($post_id) );
			$url_file     = wp_get_attachment_url( $attach_id );
		} else {
			$url_file = '';
		}
		$time = current_time( 'Y-m-d H:i:s' );

		if (isset( $_POST['closed_answer']) ) {
			$status = 4;
		} else {
		    $tiket = $_POST['tik_id'];
		   $check_info_ticket = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}nirweb_ticket WHERE ticket_id=%s",$tiket));
		    if($check_info_ticket && $check_info_ticket->support_id == get_current_user_id()){
		        	$status = 3;
		    }else{
		        	$status = 1;
		    }
		
		}
		$frm_ary_elements = array(
			'user_id'     => get_current_user_id(),
			'time_answer' => current_time( 'Y-m-d H:i:s' ),
			'text'        => isset( $_POST['user_answer'] ) ? sanitize_textarea_field( wpautop( $_POST['user_answer'] ) ) : '',
			'attach_url'  => sanitize_url( $url_file ),
			'ticket_id'   => isset( $_POST['tik_id'] ) ? sanitize_text_field( $_POST['tik_id'] ) : '',
		);
		if ( sanitize_text_field( $_POST['user_answer'] ) or $url_file ) {
			$wpdb->insert( $wpdb->prefix . 'nirweb_ticket_ticket_answered', $frm_ary_elements );
		}

		$wpdb->update(
			$wpdb->prefix . 'nirweb_ticket_ticket',
			array(
				'status'      => sanitize_text_field( $status ),
				'time_update' => sanitize_text_field( $time ),
			),
			array( 'ticket_id' => sanitize_text_field( intval( $_POST['tik_id'] ) ) )
		);
		if ( isset($attach_id) ) {
				 global $wpdb;
			$wpyar_user_upload = array(
				'user_id'     => get_current_user_id(),
				'url_file'    => sanitize_url( $url_file ),
				'file_id'     => sanitize_text_field( $attach_id ),
				'time_upload' => current_time( 'Y-m-d H:i:s' ),
			);
			$wpdb->insert( $wpdb->prefix . 'nirweb_ticket_ticket_user_upload', $wpyar_user_upload ,['%d','%s','%s','%s']);
		}

			// ----------- Start Mail Department User
		if ( get_option('active_send_mail_to_poshtiban') == '1' ) {
		    
			$user_poshtiban   = get_user_by( 'id', intval( sanitize_text_field( $_POST['id_user'] ) ) );
			$user_poshtiban   = sanitize_text_field( $user_poshtiban->user_email );
			$ticket_id        = sanitize_text_field( $_POST['tik_id'] );
			$ticket_title     = sanitize_text_field( $_POST['subject'] );
			$name_poshtiban   = get_user_by( 'id', intval( sanitize_text_field( $_POST['id_user'] ) ) );
			$ticket_poshtiban = sanitize_text_field( $name_poshtiban->display_name );
			$ticket_dep       = sanitize_text_field( $_POST['dep_name'] );
			$ticket_pri       = sanitize_text_field( $_POST['priority_name'] );
			$search           = array( '{{ticket_id}}', '{{ticket_title}}', '{{ticket_poshtiban}}', '{{ticket_dep}}', '{{ticket_pri}}', '{{ticket_stu}}' );
			$replace          = array( $ticket_id, $ticket_title, $ticket_poshtiban, $ticket_dep, $ticket_pri, esc_html__( 'new', 'nirweb-support' ) );
			$to               = $user_poshtiban;
			$headers          = array( 'Content-Type: text/html; charset=UTF-8' );
			$body             = wpautop( str_replace( $search, $replace,wp_kses_post( get_option('poshtiban_text_email_send_answer')) ) );
			$subject = str_replace( $search, $replace,get_option('subject_mail_poshtiban_answer'));
			wp_mail( esc_html( $to ), esc_html( $subject ), html_entity_decode($body, ENT_COMPAT, 'UTF-8'), $headers );
		}
	}
}

if ( ! function_exists( 'func_list_answer_ajax_user' ) ) {
	function func_list_answer_ajax_user() {

		$ticket_id = intval(sanitize_text_field( $_POST['tik_id'] ));
		global $wpdb;
		$process_answer_list = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT answered.* ,users.ID , users.display_name
                        FROM {$wpdb->prefix}nirweb_ticket_ticket_answered answered   JOIN {$wpdb->prefix}users users ON user_id=ID
                        WHERE ticket_id=%d  ORDER BY answer_id ASC",
                                $ticket_id
			)
		);
		foreach ( $process_answer_list as $row ) :
			  $user   = get_userdata( $row->user_id );
				$role = $user->roles;
			if ( in_array( 'user_support', $role ) or in_array( 'administrator', $role ) ) {
				$cls = 'user_support_wpyar';
			} else {
				$cls = '';  } ?>
				<li class="<?php echo esc_html( $cls ); ?>">
					<div class="img_avatar_wpyartick">
						<?php echo get_avatar( $row->user_id, 100 ); ?>
					</div>
					<div class="info_answer_box_wpyartick">
						<div class="text_message_wpyartick">
							<?php echo wpautop( wp_unslash( $row->text ) ); ?>
							<?php if ( $row->attach_url ) { ?>
							<p class="file_atach_url">
								<a href="<?php echo esc_url_raw( $row->attach_url ); ?>" target="_blank">
								<?php echo esc_html__( 'show attachment file', 'nirweb-support' ); ?>
								 </a>
							</p>
							<?php } ?>
						</div>
						<div class="head_answer">
							<span class="name">
								<?php echo esc_html( $row->display_name ); ?>
							</span>
							<?php if ( in_array( 'user_support', $role ) ) { ?>
							  <span class="time">
								<?php echo esc_html__( 'Hour', 'nirweb-support' ); ?>
								<?php echo date( 'H:i:s', strtotime( esc_html($row->time_answer) ) ); ?>
							</span>
								<span class="date">
								<?php echo esc_html__( 'Date :', 'nirweb-support' ); ?>
								<?php echo  wp_date( 'd F Y', strtotime( esc_html($row->time_answer ) ) ); ?>
							</span>
							<?php } else { ?>
							<span class="date">
								<?php echo esc_html__( 'Date :', 'nirweb-support' ); ?>
								<?php echo  wp_date( 'd F Y', strtotime( esc_html($row->time_answer ) ) ); ?>
							</span>
							<span class="time">
								<?php echo esc_html__( 'Hour', 'nirweb-support' ); ?>
								<?php echo  date( 'H:i:s', strtotime( esc_html( $row->time_answer ) ) ); ?>
							</span>
						<?php } ?>
						</div>
					</div>
				</li>
			<?php
		endforeach;
		exit();
	}
}