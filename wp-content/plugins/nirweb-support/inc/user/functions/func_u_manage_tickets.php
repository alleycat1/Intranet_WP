<?php
if ( ! function_exists( 'nirweb_ticket_get_all_ticket_user' ) ) {
	function nirweb_ticket_get_all_ticket_user( $user_id ) {
		global $wpdb;
		$tickets_user = $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM  {$wpdb->prefix}nirweb_ticket_ticket   WHERE sender_id=%d", $user_id )
		);
		return $tickets_user;
	}
}
