<?php
if ( ! function_exists( 'nirweb_ticket_get_list_answerd' ) ) {
	function nirweb_ticket_get_list_answerd() {
		 $ticket_id = intval( sanitize_text_field( $_GET['id'] ));
		global $wpdb;
		$query               = $wpdb->prepare(
			"SELECT answered.* ,users.ID , users.display_name
               FROM {$wpdb->prefix}nirweb_ticket_ticket_answered answered
              JOIN {$wpdb->prefix}users users ON user_id=ID WHERE ticket_id=%s ORDER BY answer_id ASC ",
			$ticket_id
		);
		$process_answer_list = $wpdb->get_results( $query );
		return $process_answer_list;
	}
}
