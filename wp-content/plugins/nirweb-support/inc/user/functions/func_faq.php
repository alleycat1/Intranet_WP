<?php
if ( ! function_exists( 'nirweb_ticket_get_all_faq_user' ) ) {
	function nirweb_ticket_get_all_faq_user() {
		 global $wpdb;
		$faqs = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}nirweb_ticket_ticket_faq  WHERE %d ORDER BY id DESC   ", 1 ) );
		return $faqs;
	}
}
