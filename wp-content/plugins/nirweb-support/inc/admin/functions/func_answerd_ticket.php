<?php
$ticket_id = sanitize_text_field( $_POST['id_form'] );
if ( ! function_exists( 'nirweb_ticket_answer_ticket' ) && is_admin() ) {
	function nirweb_ticket_answer_ticket( $ticket_id ) {
		$text = preg_replace( '/\\\\/', '', wpautop( $_POST['content']) );
		global $wpdb;
		$frm_ary_elements = array(
			'user_id'     => get_current_user_id(),
			'time_answer' => current_time( 'Y-m-d H:i:s' ),
			'text'        => wp_unslash( $text ),
			'attach_url'  => isset( $_POST['file_url'] ) && wp_http_validate_url( $_POST['file_url'] ) ? sanitize_url( $_POST['file_url'] ) : '',
			'ticket_id'   => isset( $_POST['id_form'] ) ? intval(sanitize_text_field( $_POST['id_form'] )) : '',
		);
		if ( strlen( sanitize_text_field( $_POST['content'] ) ) > 3 ) {
			$wpdb->insert( $wpdb->prefix . 'nirweb_ticket_ticket_answered', $frm_ary_elements,['%d','%s','%s','%s','%d'] );
		}

		$wpdb->update(
			$wpdb->prefix . 'nirweb_ticket_ticket',
			array(
				'department' => intval( sanitize_text_field( $_POST['department'] ) ),
				'status'     => intval( sanitize_text_field( $_POST['status'] ) ),
			),
			array( 'ticket_id' => intval( sanitize_text_field( $_POST['id_form'] ) ) )
		);
		if ( get_option('active_send_mail_to_user') == '1' ) {
		   
			$user             = get_user_by( 'id', intval( sanitize_text_field( $_POST['sender_id'] ) ) );
			$user             = sanitize_text_field( $user->user_email );
			$ticket_id        = sanitize_text_field( $_POST['id_form'] );
			$ticket_title     = sanitize_text_field( $_POST['subject'] );
			$name_poshtiban   = get_user_by( 'id', intval( sanitize_text_field( $_POST['resivered_id'] ) ) );
			$ticket_poshtiban = sanitize_text_field( $name_poshtiban->display_name );
			$ticket_dep       = sanitize_text_field( $_POST['department_name'] );
			$ticket_pri       = sanitize_text_field( $_POST['proname'] );
			$status_name      = sanitize_text_field( $_POST['status_name'] );
			$search           = array( '{{ticket_id}}', '{{ticket_title}}', '{{ticket_poshtiban}}', '{{ticket_dep}}', '{{ticket_pri}}', '{{ticket_stu}}' );
			$replace          = array( $ticket_id, $ticket_title, $ticket_poshtiban, $ticket_dep, $ticket_pri, $status_name );
			$to               = $user;
			$headers          = array( 'Content-Type: text/html; charset=UTF-8' );
 		    $body             = wpautop( str_replace( $search, $replace,wp_kses_post( get_option('user_text_email_send_answer')) ) );
 		    $subject             = str_replace( $search, $replace,get_option('subject_mail_user_answer') ) ;

			wp_mail( $to, $subject, html_entity_decode($body, ENT_COMPAT, 'UTF-8'), $headers );
		}
	}
}


if ( ! function_exists( 'func_list_answer_ajax' ) ) {
	function func_list_answer_ajax( $ticket_id ) {
		$t_id = sanitize_text_field( $ticket_id );
		global $wpdb;
		$query               = $wpdb->prepare( "SELECT answered.* ,users.ID , users.display_name FROM {$wpdb->prefix}nirweb_ticket_ticket_answered answered   JOIN {$wpdb->prefix}users users ON user_id=ID WHERE ticket_id= %d  ORDER BY answer_id ASC", $t_id );
		$process_answer_list = $wpdb->get_results( $query );
		foreach ( $process_answer_list as $row ) :
			echo '<li> <div class="head_answer"> <span class="name">' . esc_html( $row->display_name ) . '  </span>
                    <span class="time">' . date( '(H:i:s)', strtotime( esc_html($row->time_answer) ) )  .  wp_date( ' Y-m-d', strtotime( esc_html($row->time_answer )) )   . ' </span>  </div> 
                    <div class="content">' . wpautop( wp_unslash( $row->text ) ); ?>
			<?php
			if ( $row->attach_url ) {
				echo ( '<p>' . esc_html__( 'Attachment File', 'nirweb-support' ) . '  ' . esc_url_raw( $row->attach_url ) . ' </p>' ); }
			?>
			<?php
			echo ' </div></li> ';
endforeach;
		exit();
	}
}

