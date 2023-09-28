<?php
if ( ! function_exists( 'nirweb_ticket_send_ticket' ) && is_admin() ) {
	function nirweb_ticket_send_ticket() {
		$text = preg_replace( '/\\\\/', '', wpautop( $_POST['send_content']  ) );
		global $wpdb;
		$frm_ary_elements = array(
			'sender_id'     => get_current_user_id(),
			'id_receiver'   => sanitize_text_field( $_POST['id_receiver'] ),
			'receiver_type' => sanitize_text_field( $_POST['receiver_type'] ),
			'subject'       => sanitize_text_field( $_POST['subject'] ),
			'content'       => wp_unslash( $text ),
			'department'    => isset( $_POST['department_id'] ) ? intval( sanitize_text_field( $_POST['department_id'] ) ) : '',
			'priority'      => isset( $_POST['priority_id'] ) ? intval( sanitize_text_field( $_POST['priority_id'] ) ) : '',
			'website'       => isset( $_POST['website'] ) ? sanitize_text_field( $_POST['website'] ) : '',
			'product'       => isset( $_POST['product'] ) ? intval( sanitize_text_field( $_POST['product'] ) ) : '',
			'support_id'    => isset( $_POST['support_id'] ) ? sanitize_text_field( $_POST['support_id'] ) : '',
			'status'        => isset( $_POST['status'] ) ? intval( sanitize_text_field( $_POST['status'] ) ) : '',
			'file_url'      => isset( $_POST['file_url'] ) ? sanitize_url( $_POST['file_url'] ) : '',
			'time_update'   => current_time( 'Y-m-d H:i:s' ),
			'date_qustion'  => current_time( 'Y-m-d H:i:s' ),
		);
		$wpdb->insert( $wpdb->prefix . 'nirweb_ticket_ticket', $frm_ary_elements);
		if ( $_POST['check_mail']) {
			$lastid       = $wpdb->insert_id;
			$recever_name = sanitize_text_field( $_POST['receiver_name'] );
			$current_user = wp_get_current_user();
			$fromName  = sanitize_text_field( $current_user->display_name );
			$department   = sanitize_text_field( $_POST['department'] );
			$priority     = sanitize_text_field( $_POST['priority'] );
			$to           = sanitize_email( get_user_by('id',intval(sanitize_text_field($_POST['id_receiver'])))->user_email );
			$from         = bloginfo( 'name' );
			$subject      = sanitize_text_field( $_POST['subject'] );
			$time         = current_time( 'd/m/Y - H:i:s', time() );
			$htmlContent  = wpautop( str_replace( '{username}', $recever_name, wp_kses_post(get_option( 'template_send_ticket_email') )) );
			$headers      = array( 'Content-Type: text/html; charset=UTF-8' );
			$body         = wp_kses_post( $htmlContent );
			var_dump($body);
		 
			var_dump($subject);
		
			wp_mail( $to, $subject, html_entity_decode($body, ENT_COMPAT, 'UTF-8'), $headers );
		}
		exit();
	}
}
