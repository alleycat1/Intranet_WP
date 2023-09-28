<?php
if ( ! function_exists( 'func_ajax_nirweb_ticket_user_search' ) ) {
	function func_ajax_nirweb_ticket_user_search( $value ) {
		global $wpdb;
		$value         = sanitize_text_field( $value );
		$user_id       = get_current_user_id();
		$address_table = $wpdb->prefix . 'nirweb_ticket_ticket';
		$search        = "%{$value}%";
		$where         = $wpdb->prepare( 'WHERE (id_receiver=%s OR sender_id=%s) AND (ticket_id LIKE %s OR subject LIKE %s)', $user_id, $user_id, $search, $search );
		$results       = $wpdb->get_results( "SELECT * FROM {$address_table} {$where}" );
		if ( sizeof( $results ) > 0 ) {
			foreach ( $results as $row ) {
				echo '<li><a href="#">' . esc_html( $row->subject ) . '</a></li>';
			}
		} else {
			echo '<p class="not_found">' . esc_html__( 'not found', 'nirweb-support' ) . '</p>';
		}
	}
}
