<?php
if ( ! function_exists( 'nirweb_ticket_get_status' ) ) {
	function nirweb_ticket_get_status() {
		global $wpdb;
		$list_status = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}nirweb_ticket_ticket_status WHERE %d ORDER BY status_id", 1 ) );
		return $list_status;
	}
}
if ( ! function_exists( 'nirweb_ticket_get_priority' ) ) {
	function nirweb_ticket_get_priority() {
		global $wpdb;
		$list_priority = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}nirweb_ticket_ticket_priority WHERE %d ORDER BY priority_id", 1 ) );
		return $list_priority;
	}
}

