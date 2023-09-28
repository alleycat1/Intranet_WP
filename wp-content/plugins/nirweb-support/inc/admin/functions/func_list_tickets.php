<?php
if ( ! function_exists( 'wp_list_ticket_check' ) ) {
	function wp_list_ticket_check() {
		global $wpdb;
		$new_ticket_list = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT ticket.* , users.* ,status.*,department.* ,department.name as depname,priority.*,priority.name as proname
   ,post.ID,post.post_title as product_name
                                              FROM {$wpdb->prefix}nirweb_ticket_ticket ticket
                                              LEFT JOIN {$wpdb->prefix}users users
                                              ON sender_id=ID
                               LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_status status
                                              ON status=status_id  
                                              LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_department department
                                              ON department=department_id  
                                LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_priority priority
                                              ON priority=priority_id
                                LEFT JOIN  {$wpdb->prefix}posts post
                                              ON product=post.ID
                                            WHERE %d  ",
				1
			)
		);

		return $new_ticket_list;
	}
}
if ( ! function_exists( 'wp_list_ticket_check_posht' ) ) {
	function wp_list_ticket_check_posht( $id ) {
		global $wpdb;
		$id              = intval( sanitize_text_field( $id ) );
		$new_ticket_list = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT ticket.* , users.* ,status.*,department.* ,department.name as depname,priority.*,priority.name as proname ,post.ID,post.post_title as product_name FROM {$wpdb->prefix}nirweb_ticket_ticket ticket
        LEFT JOIN {$wpdb->prefix}users users
        ON sender_id=ID
        LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_status status
        ON status=status_id  
        LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_department department
        ON department=department_id  
        LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_priority priority
        ON priority=priority_id
        LEFT JOIN  {$wpdb->prefix}posts post
        ON product=post.ID
        WHERE ticket.support_id = %d OR ticket.sender_id = %d
        ",
				$id,
				$id
			)
		);

		return $new_ticket_list;
	}
}
if ( ! function_exists( 'nirweb_ticket_get_list_all_ticket' ) ) {
	function nirweb_ticket_get_list_all_ticket() {
		global $wpdb;
		$items_per_page  = 20;
		$page            = isset( $_GET['cpage'] ) ? abs( (int) sanitize_text_field( $_GET['cpage'] ) ) : 1;
		$offset          = ( $page * $items_per_page ) - $items_per_page;
		$query           = 'SELECT * FROM ' . $wpdb->prefix . 'nirweb_ticket_ticket ticket';
		$total_query     = "SELECT COUNT(1) FROM (${query}) AS combined_table";
		$total           = $wpdb->get_var( $total_query );
		$new_ticket_list = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT ticket.* , users.* ,status.*,department.* ,department.name as depname,priority.*,priority.name as proname
					        ,post.ID,post.post_title as product_name
					        FROM {$wpdb->prefix}nirweb_ticket_ticket ticket
					        LEFT JOIN {$wpdb->prefix}users users
					        ON sender_id=ID
					        LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_status status
					        ON status=status_id  
					        LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_department department
					        ON department=department_id  
					        LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_priority priority
					        ON priority=priority_id
					        LEFT JOIN  {$wpdb->prefix}posts post
					        ON product=post.ID
					        ORDER BY ticket_id DESC
					        LIMIT %d , %d ",
				$offset,
				$items_per_page
			)
		);

		return array(
			$new_ticket_list,
			paginate_links(
				array(
					'base'      => add_query_arg( 'cpage', '%#%' ),
					'format'    => '',
					'prev_text' => esc_html__( '&laquo;' ),
					'next_text' => esc_html__( '&raquo;' ),
					'total'     => ceil( $total / $items_per_page ),
					'current'   => $page,
				)
			),
		);
	}
}
if ( ! function_exists( 'nirweb_ticket_get_list_all_ticket_posht' ) ) {
	function nirweb_ticket_get_list_all_ticket_posht( $id ) {
		global $wpdb;
		$id              = intval( sanitize_text_field( $id ) );
		$items_per_page  = 20;
		$page            = isset( $_GET['cpage'] ) ? abs( (int) sanitize_text_field( $_GET['cpage'] ) ) : 1;
		$offset          = ( $page * $items_per_page ) - $items_per_page;
		$query           = 'SELECT * FROM ' . $wpdb->prefix . 'nirweb_ticket_ticket ticket';
		$total_query     = "SELECT COUNT(1) FROM (${query}) AS combined_table";
		$total           = $wpdb->get_var( $total_query );
		$new_ticket_list = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT ticket.* , users.* ,status.*,department.* ,department.name as depname,priority.*,priority.name as proname
			            ,post.ID,post.post_title as product_name
			            FROM {$wpdb->prefix}nirweb_ticket_ticket ticket
			            LEFT JOIN {$wpdb->prefix}users users
			            ON sender_id=ID
			            LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_status status
			            ON status=status_id  
			            LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_department department
			            ON department=department_id  
			            LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_priority priority
			            ON priority=priority_id
			            LEFT JOIN  {$wpdb->prefix}posts post
			            ON product=post.ID
			            WHERE ticket.support_id = %d OR ticket.sender_id = %d
			            ORDER BY ticket_id DESC
			           LIMIT %d , %d ",
				$id,
				$id,
				$offset,
				$items_per_page
			)
		);

		return array(
			$new_ticket_list,
			paginate_links(
				array(
					'base'      => add_query_arg( 'cpage', '%#%' ),
					'format'    => '',
					'prev_text' => esc_html__( '&laquo;' ),
					'next_text' => esc_html__( '&raquo;' ),
					'total'     => ceil( $total / $items_per_page ),
					'current'   => $page,
				)
			),
		);
	}
}
if ( ! function_exists( 'nirweb_ticket_get_list_new_ticket' ) ) {
	function nirweb_ticket_get_list_new_ticket() {
		global $wpdb;
		$items_per_page      = 20;
		$page                = isset( $_GET['cpage'] ) ? abs( (int) sanitize_text_field( $_GET['cpage'] ) ) : 1;
		$offset              = ( $page * $items_per_page ) - $items_per_page;
		$query               = $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'nirweb_ticket_ticket ticket WHERE status=%d', 1 );
		$total_query         = "SELECT COUNT(1) FROM (${query}) AS combined_table";
		$total               = $wpdb->get_var( $total_query );
		$process_ticket_list = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT ticket.* , users.* ,status.*,department.* ,department.name as depname ,priority.*,priority.name as proname  ,post.ID,post.post_title as product_name
			               FROM {$wpdb->prefix}nirweb_ticket_ticket ticket
			               LEFT JOIN {$wpdb->prefix}users users
			               ON sender_id=ID AND status=%d
			               LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_status status
			               ON status_id=%d
			               LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_department department
			               ON department=department_id
			                   LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_priority priority
			               ON priority=priority_id
			               LEFT JOIN  {$wpdb->prefix}posts post
			               ON product=post.ID
			                WHERE status= %d
			               ORDER BY ticket_id DESC LIMIT  %d, %d ",
				1, 1, 1, $offset, $items_per_page
			)
		);

		return array(
			$process_ticket_list,
			paginate_links(
				array(
					'base'      => add_query_arg( 'cpage', '%#%' ),
					'format'    => '',
					'prev_text' => esc_html__( '&laquo;' ),
					'next_text' => esc_html__( '&raquo;' ),
					'total'     => ceil( $total / $items_per_page ),
					'current'   => $page,
				)
			),
		);
	}
}
if ( ! function_exists( 'nirweb_ticket_get_list_new_ticket_posht' ) ) {
	function nirweb_ticket_get_list_new_ticket_posht( $id ) {
		global $wpdb;
		$id                  = intval( sanitize_text_field( $id ) );
		$items_per_page      = 20;
		$page                = isset( $_GET['cpage'] ) ? abs( (int) sanitize_text_field( $_GET['cpage'] ) ) : 1;
		$offset              = ( $page * $items_per_page ) - $items_per_page;
		$query               = $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'nirweb_ticket_ticket ticket WHERE status=%d', 1 );
		$total_query         = "SELECT COUNT(1) FROM (${query}) AS combined_table";
		$total               = $wpdb->get_var( $total_query );
		$process_ticket_list = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT ticket.* , users.* ,status.*,department.* ,department.name as depname ,priority.*,priority.name as proname  ,post.ID,post.post_title as product_name
			               FROM {$wpdb->prefix}nirweb_ticket_ticket ticket
			               LEFT JOIN {$wpdb->prefix}users users
			               ON sender_id=ID AND status=%d
			               LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_status status
			               ON status_id=%d
			               LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_department department
			               ON department=department_id
			                   LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_priority priority
			               ON priority=priority_id
			               LEFT JOIN  {$wpdb->prefix}posts post
			               ON product=post.ID
			                WHERE (ticket.support_id = %d OR ticket.sender_id = %d) AND status=%d
			               ORDER BY ticket_id DESC  LIMIT %d , %d    ",
				1, 1, $id, $id, 1, $offset, $items_per_page
			)
		);

		return array(
			$process_ticket_list,
			paginate_links(
				array(
					'base'      => add_query_arg( 'cpage', '%#%' ),
					'format'    => '',
					'prev_text' => esc_html__( '&laquo;' ),
					'next_text' => esc_html__( '&raquo;' ),
					'total'     => ceil( $total / $items_per_page ),
					'current'   => $page,
				)
			),
		);
	}
}
if ( ! function_exists( 'nirweb_ticket_get_list_process_ticket' ) ) {
	function nirweb_ticket_get_list_process_ticket() {
		global $wpdb;
		$items_per_page      = 20;
		$page                = isset( $_GET['cpage'] ) ? abs( (int) sanitize_text_field( $_GET['cpage'] ) ) : 1;
		$offset              = ( $page * $items_per_page ) - $items_per_page;
		$query               = $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'nirweb_ticket_ticket ticket WHERE status=%d', 2 );
		$total_query         = "SELECT COUNT(1) FROM (${query}) AS combined_table";
		$total               = $wpdb->get_var( $total_query );
		$process_ticket_list = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT ticket.* , users.* ,status.*,department.* ,department.name as depname,priority.*,priority.name as proname,post.ID,post.post_title as product_name
                       FROM {$wpdb->prefix}nirweb_ticket_ticket ticket
                       LEFT JOIN {$wpdb->prefix}users users
                       ON sender_id=ID AND status=%d
                       LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_status status
                       ON status_id=%d
                           LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_department department
                       ON department=department_id
                           LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_priority priority
                       ON priority=priority_id
                       LEFT JOIN  {$wpdb->prefix}posts post
                       ON product=post.ID
                       WHERE status=%d
                       ORDER BY ticket_id DESC  LIMIT %d , %d   ",
				2,
				2,
				2,
				$offset,
				$items_per_page
			)
		);

		return array(
			$process_ticket_list,
			paginate_links(
				array(
					'base'      => add_query_arg( 'cpage', '%#%' ),
					'format'    => '',
					'prev_text' => esc_html__( '&laquo;' ),
					'next_text' => esc_html__( '&raquo;' ),
					'total'     => ceil( $total / $items_per_page ),
					'current'   => $page,
				)
			),
		);
	}
}
if ( ! function_exists( 'nirweb_ticket_get_list_process_ticket_posht' ) ) {
	function nirweb_ticket_get_list_process_ticket_posht( $id ) {
		global $wpdb;
		$id                  = sanitize_text_field( $id );
		$items_per_page      = 20;
		$page                = isset( $_GET['cpage'] ) ? abs( (int) sanitize_text_field( $_GET['cpage'] ) ) : 1;
		$offset              = ( $page * $items_per_page ) - $items_per_page;
		$query               = $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'nirweb_ticket_ticket ticket WHERE status=%d', 2 );
		$total_query         = "SELECT COUNT(1) FROM (${query}) AS combined_table";
		$total               = $wpdb->get_var( $total_query );
		$process_ticket_list = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT ticket.* , users.* ,status.*,department.* ,department.name as depname,priority.*,priority.name as proname,post.ID,post.post_title as product_name
                   FROM {$wpdb->prefix}nirweb_ticket_ticket ticket
                   LEFT JOIN {$wpdb->prefix}users users
                   ON sender_id=ID AND status=%d
                   LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_status status
                   ON status_id=%d
                       LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_department department
                   ON department=department_id
                       LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_priority priority
                   ON priority=priority_id
                   LEFT JOIN  {$wpdb->prefix}posts post
                   ON product=post.ID
                   WHERE (ticket.support_id = %d OR ticket.sender_id = %d ) AND status= %d 
                   ORDER BY ticket_id DESC LIMIT  %d,  %d  ", 2, 2, $id, $id, 2, $offset, $items_per_page
			)
		);

		return array(
			$process_ticket_list,
			paginate_links(
				array(
					'base'      => add_query_arg( 'cpage', '%#%' ),
					'format'    => '',
					'prev_text' => esc_html__( '&laquo;' ),
					'next_text' => esc_html__( '&raquo;' ),
					'total'     => ceil( $total / $items_per_page ),
					'current'   => $page,
				)
			),
		);
	}
}
if ( ! function_exists( 'wp_yap_get_list_answered_ticket' ) ) {
	function wp_yap_get_list_answered_ticket() {
		global $wpdb;
		$items_per_page       = 20;
		$page                 = isset( $_GET['cpage'] ) ? abs( (int) sanitize_text_field( $_GET['cpage'] ) ) : 1;
		$offset               = ( $page * $items_per_page ) - $items_per_page;
		$query                = $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'nirweb_ticket_ticket ticket WHERE status=%d', 3 );
		$total_query          = "SELECT COUNT(1) FROM (${query}) AS combined_table";
		$total                = $wpdb->get_var( $total_query );
		$answered_ticket_list = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT ticket.* , users.* ,status.*,department.* ,department.name as depname,priority.*,priority.name as proname,post.ID,post.post_title as product_name
                                               FROM {$wpdb->prefix}nirweb_ticket_ticket ticket
                                               LEFT JOIN {$wpdb->prefix}users users
                                               ON sender_id=ID AND status=%d
                                               LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_status status
                                               ON status_id=%d
                                                   LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_department department
                                               ON department=department_id
                                                   LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_priority priority
                                               ON priority=priority_id
                                               LEFT JOIN  {$wpdb->prefix}posts post
                                               ON product=post.ID
                                               WHERE status=%d
                                               ORDER BY ticket_id DESC LIMIT  %d,  %d   ",
				3,
				3,
				3,
				$offset,
				$items_per_page
			)
		);

		return array(
			$answered_ticket_list,
			paginate_links(
				array(
					'base'      => add_query_arg( 'cpage', '%#%' ),
					'format'    => '',
					'prev_text' => esc_html__( '&laquo;' ),
					'next_text' => esc_html__( '&raquo;' ),
					'total'     => ceil( $total / $items_per_page ),
					'current'   => $page,
				)
			),
		);
	}
}
if ( ! function_exists( 'wp_yap_get_list_answered_ticket_posht' ) ) {
	function wp_yap_get_list_answered_ticket_posht( $id ) {
		global $wpdb;
		$id                   = sanitize_text_field( $id );
		$items_per_page       = 20;
		$page                 = isset( $_GET['cpage'] ) ? abs( (int) sanitize_text_field( $_GET['cpage'] ) ) : 1;
		$offset               = ( $page * $items_per_page ) - $items_per_page;
		$query                = $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'nirweb_ticket_ticket ticket WHERE status=%d', 3 );
		$total_query          = "SELECT COUNT(1) FROM (${query}) AS combined_table";
		$total                = $wpdb->get_var( $total_query );
		$answered_ticket_list = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT ticket.* ,users.* ,status.*,department.* ,department.name as depname,priority.*,priority.name as proname,post.ID,post.post_title as product_name
                       FROM {$wpdb->prefix}nirweb_ticket_ticket ticket
                       LEFT JOIN {$wpdb->prefix}users users
                       ON sender_id=ID AND status=%d
                       LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_status status
                       ON status_id=%d
                           LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_department department
                       ON department=department_id
                           LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_priority priority
                       ON priority=priority_id
                       LEFT JOIN  {$wpdb->prefix}posts post
                       ON product=post.ID
                       WHERE (ticket.support_id = %d OR ticket.sender_id = %d ) AND status= %d 
                       ORDER BY ticket_id DESC LIMIT  %d,  %d  ",
				3, 3, $id, $id, 3, $offset, $items_per_page
			)
		);

		return array(
			$answered_ticket_list,
			paginate_links(
				array(
					'base'      => add_query_arg( 'cpage', '%#%' ),
					'format'    => '',
					'prev_text' => esc_html__( '&laquo;' ),
					'next_text' => esc_html__( '&raquo;' ),
					'total'     => ceil( $total / $items_per_page ),
					'current'   => $page,
				)
			),
		);
	}
}
if ( ! function_exists( 'wp_yap_get_list_closed_ticket' ) ) {
	function wp_yap_get_list_closed_ticket() {
		global $wpdb;
		$items_per_page     = 20;
		$page               = isset( $_GET['cpage'] ) ? abs( (int) sanitize_text_field( $_GET['cpage'] ) ) : 1;
		$offset             = ( $page * $items_per_page ) - $items_per_page;
		$query              = $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'nirweb_ticket_ticket ticket WHERE status=%d', 4 );
		$total_query        = "SELECT COUNT(1) FROM (${query}) AS combined_table";
		$total              = $wpdb->get_var( $total_query );
		$closed_ticket_list = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT ticket.* , users.* ,status.*,department.* ,department.name as depname,priority.*,priority.name as proname,post.ID,post.post_title as product_name
						FROM {$wpdb->prefix}nirweb_ticket_ticket ticket
						LEFT JOIN {$wpdb->prefix}users users
						ON sender_id=ID AND status=%d
						LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_status status
						ON status_id=%d
						LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_department department
						ON department=department_id
						LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_priority priority
						ON priority=priority_id
						LEFT JOIN  {$wpdb->prefix}posts post
						ON product=post.ID
						WHERE status=%d ORDER BY ticket_id DESC   LIMIT %d ,  %d ", 4, 4, 4, $offset, $items_per_page
			)
		);

		return array(
			$closed_ticket_list,
			paginate_links(
				array(
					'base'      => add_query_arg( 'cpage', '%#%' ),
					'format'    => '',
					'prev_text' => esc_html__( '&laquo;' ),
					'next_text' => esc_html__( '&raquo;' ),
					'total'     => ceil( $total / $items_per_page ),
					'current'   => $page,
				)
			),
		);
	}
}
if ( ! function_exists( 'wp_yap_get_list_closed_ticket_posht' ) ) {
	function wp_yap_get_list_closed_ticket_posht( $id ) {
		global $wpdb;
		$id                 = sanitize_text_field( $id );
		$items_per_page     = 20;
		$page               = isset( $_GET['cpage'] ) ? abs( (int) sanitize_text_field( $_GET['cpage'] ) ) : 1;
		$offset             = ( $page * $items_per_page ) - $items_per_page;
		$query              = $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'nirweb_ticket_ticket ticket WHERE status=%d', 4 );
		$total_query        = "SELECT COUNT(1) FROM (${query}) AS combined_table";
		$total              = $wpdb->get_var( $total_query );
		$closed_ticket_list = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT ticket.* , users.* ,status.*,department.* ,department.name as depname,priority.*,priority.name as proname,post.ID,post.post_title as product_name
						FROM {$wpdb->prefix}nirweb_ticket_ticket ticket
						LEFT JOIN {$wpdb->prefix}users users
						ON sender_id=ID AND status=%d
						LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_status status
						ON status_id=%d
						LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_department department
						ON department=department_id
						LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_priority priority
						ON priority=priority_id
						LEFT JOIN  {$wpdb->prefix}posts post
						ON product=post.ID
						WHERE (ticket.support_id = %d OR ticket.sender_id = %d ) AND status=%d 
						ORDER BY ticket_id DESC LIMIT  %d,  %d  ", 4, 4, $id, $id, 4, $offset, $items_per_page
			)
		);

		return array(
			$closed_ticket_list,
			paginate_links(
				array(
					'base'      => add_query_arg( 'cpage', '%#%' ),
					'format'    => '',
					'prev_text' => esc_html__( '&laquo;' ),
					'next_text' => esc_html__( '&raquo;' ),
					'total'     => ceil( $total / $items_per_page ),
					'current'   => $page,
				)
			),
		);
	}
}
if ( ! function_exists( 'nirweb_ticket_edit_ticket' ) ) {
	function nirweb_ticket_edit_ticket( $ticket_id ) {
		global $wpdb;
		$ticket_id = intval( sanitize_text_field( $ticket_id ) );
		$ticket    = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT ticket.* ,users.* ,status.*,department.* ,department.name as depname,priority.*,priority.name as proname ,revuser.ID as rev_id,revuser.display_name as rev_name ,posts.ID,posts.post_title as product_name
        FROM {$wpdb->prefix}nirweb_ticket_ticket ticket
        LEFT JOIN {$wpdb->prefix}users users
        ON sender_id=ID                 
        LEFT JOIN {$wpdb->prefix}posts posts
        ON product=posts.ID
        LEFT JOIN {$wpdb->prefix}users revuser
        ON id_receiver=revuser.ID
        LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_status status
        ON status=status_id  
        LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_department department
        ON department=department_id  
        LEFT JOIN  {$wpdb->prefix}nirweb_ticket_ticket_priority priority
        ON priority=priority_id        
      where ticket_id = %s;",
				$ticket_id
			)
		);

		return $ticket;
	}
}
if ( ! function_exists( 'nirweb_ticket_delete_ticket' ) ) {
	function nirweb_ticket_delete_ticket( $item_delete ) {
		global $wpdb;
		for ( $i = 0; $i < count( $item_delete ); $i ++ ) {
			$wpdb->delete( $wpdb->prefix . 'nirweb_ticket_ticket', array( 'ticket_id' => sanitize_text_field( intval( $item_delete[ $i ] ) ) ), [ '%d' ] );
			$wpdb->delete( $wpdb->prefix . 'nirweb_ticket_ticket_answered', array( 'ticket_id' => sanitize_text_field( intval( $item_delete[ $i ] ) ) ), [ '%d' ] );
		}
	}
}

