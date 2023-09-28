<?php
if ( ! function_exists( 'func_ajax_search_in_ticketes_wpyar' ) ) {
	function func_ajax_search_in_ticketes_wpyar( $value ) {
		global $wpdb;
		$address_table = $wpdb->prefix . 'nirweb_ticket_ticket';
		 $search       = "%{$value}%";
		 $where        = $wpdb->prepare( 'WHERE ticket_id LIKE %s OR subject LIKE %s', $search, $search );
		 $results      = $wpdb->get_results( "SELECT * FROM {$address_table} {$where}" );
		if ( sizeof( $results ) > 0 ) {
			foreach ( $results as $row ) {
				 echo ( '<li><a href="' . get_bloginfo( 'url' ) . '/wp-admin/admin.php?page=nirweb_ticket_manage_tickets&amp;tab=all_ticket&amp;action=edit&amp;id=' . esc_html( $row->ticket_id ) . '">' . esc_html( $row->subject ) . '</a></li>' );
			}
		} else {
			 echo ( '<p class="not_found">' . esc_html__( 'not found', 'nirweb-support' ) . '</p>' );
		}
	}
}
