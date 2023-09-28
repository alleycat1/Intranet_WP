<?php
/*
 #--------------- Ajax Search in ticketes
 */
add_action( 'wp_ajax_ajax_search_in_ticketes_wpyar', function () {
	if ( isset( $_POST['once'] ) && wp_verify_nonce( $_POST['once'], 'act_nirweb_ticket_ajax_search' ) ) {
		include_once NIRWEB_SUPPORT_INC_ADMIN_FUNCTIONS_TICKET . 'ajax_search_in_ticketes_wpyar.php';
		func_ajax_search_in_ticketes_wpyar( sanitize_text_field( $_POST['value'] ) );
	}

	exit();
});
/*
 #--------------- Ajax Send type user
 */
add_action( 'wp_ajax_send_type_role_user', function () {
	if ( isset( $_POST['once'] ) && wp_verify_nonce( $_POST['once'], 'admin_send_ticket_act' ) ) {
		if ( sanitize_text_field( $_POST['selectedtypsender'] ) == 1 ) {
			$get_users = get_users();
		} elseif ( sanitize_text_field( $_POST['selectedtypsender'] ) == 2 ) {
			$get_users = get_users( array( 'role__in' => array( 'user_support' ) ) );
		}
		foreach ( $get_users as $user ) {
			echo( '<option value="' . esc_html( $user->ID ) . '">' . esc_html( $user->display_name ) . '</option>' );
		}
	}
	exit();
});


/*
 #--------------- Ajax send_new_ticket_admin
 */
add_action( 'wp_ajax_send_new_ticket', function () {
	if ( isset( $_POST['once'] ) && wp_verify_nonce( $_POST['once'], '_act__admin_send_ticket_nirweb' ) ) {
		include_once NIRWEB_SUPPORT_INC_ADMIN_FUNCTIONS_TICKET . 'func_send_ticket.php';
		nirweb_ticket_send_ticket();
	}

});
/*
// --------------- Ajax answer_ticket_admin
*/
add_action( 'wp_ajax_answerd_ticket', function () {
	if ( isset( $_POST['once'] ) && wp_verify_nonce( $_POST['once'], 'admin_answer_nirweb_ticker__act' ) ) {
		include_once NIRWEB_SUPPORT_INC_ADMIN_FUNCTIONS_TICKET . 'func_answerd_ticket.php';
		nirweb_ticket_answer_ticket( sanitize_text_field( $_POST['id_form'] ) );
		func_list_answer_ajax( sanitize_text_field( $_POST['id_form'] ) );
	}
	exit();
});


/*
 #--------------- Ajax delete_tickets_admin
 */
add_action( 'wp_ajax_delete_tickets_admin', function () {

		if (current_user_can( 'administrator' ) && isset( $_POST['once'] ) && wp_verify_nonce( $_POST['once'], 'delete_ticket_admin_act__' )){
			include_once NIRWEB_SUPPORT_INC_ADMIN_FUNCTIONS_TICKET . 'func_list_tickets.php';
			nirweb_ticket_delete_ticket( sanitize_post( $_POST['check'] ) );
		}
	exit();
});
/*
 #--------------- Ajax Add Department
 */
add_action( 'wp_ajax_add_department_wpyt',function () {
	if (current_user_can( 'administrator' )&& isset( $_POST['once'] ) && wp_verify_nonce( $_POST['once'], 'add_department_wpyt_once_act' )) {
	        var_dump('sasasasassasa');
		include_once NIRWEB_SUPPORT_INC_ADMIN_FUNCTIONS_TICKET . 'func_department.php';
		nirweb_ticket_ticket_add_department();
		get_list_department_ajax();
	}
	exit();
});

/*
 #--------------- Ajax delete department
 */
add_action( 'wp_ajax_delete_department', function () {
	if (current_user_can( 'administrator' ) && isset( $_POST['once'] ) && wp_verify_nonce( $_POST['once'], 'del_department_wpyt_once_act' )){
		include_once NIRWEB_SUPPORT_INC_ADMIN_FUNCTIONS_TICKET . 'func_department.php';
		nirweb_ticket_delete_department();
	}

	exit();
});


/*
 #--------------- Ajax edit department
 */
add_action( 'wp_ajax_edite_department', function () {
	if (current_user_can( 'administrator' ) && isset( $_POST['once'] ) && wp_verify_nonce( $_POST['once'], 'add_department_wpyt_once_act' )){
		include_once NIRWEB_SUPPORT_INC_ADMIN_FUNCTIONS_TICKET . 'func_department.php';
		nirweb_ticket_edite_department( sanitize_post( $_POST ) );
		get_list_department_ajax();
	}
	exit();
});

/*
 #--------------- Ajax Add Question
 */
add_action( 'wp_ajax_add_question_faq', function () {
		if (current_user_can( 'administrator' ) && isset( $_POST['once'] ) && wp_verify_nonce( $_POST['once'], 'add_question_faq_once_act' )){
			include_once NIRWEB_SUPPORT_INC_ADMIN_FUNCTIONS_TICKET . 'func_FAQ.php';
			nirweb_ticket_add_question_faq();
			nirweb_ticket_ajax_get_all_faq();
		}
	exit();
});


/*
 #--------------- Ajax Delete Question
 */
add_action( 'wp_ajax_delete_faq', function () {
	if (current_user_can( 'administrator' ) && isset( $_POST['once'] ) && wp_verify_nonce( $_POST['once'], 'del_question_faq_once_act' )) {
		include_once NIRWEB_SUPPORT_INC_ADMIN_FUNCTIONS_TICKET . 'func_FAQ.php';
		nirweb_ticket_delete_faq();
	}
	exit();
});


/*
 #--------------- Ajax Delete Files
 */
add_action( 'wp_ajax_ticket_wpyar_file_user_delete', function () {
	if (current_user_can( 'administrator' ) && isset( $_POST['once'] ) && wp_verify_nonce( $_POST['once'], 'admin_del_files_act' )) {
	   global $wpdb;
		
		for ( $i = 0; $i < count( $_POST['check'] ); $i++ ) {
			$wpdb->delete( $wpdb->prefix . 'nirweb_ticket_ticket_user_upload', array( 'id' =>sanitize_text_field( $_POST['check'][ $i ]) ) , ['%d'] );
			wp_delete_attachment(sanitize_text_field($_POST['checkeds_id_file'][ $i ]));
		}
	}
	exit();
} );


/*
``````````````````````````````````   USER ```````````````````````````````````````````````
*/

/*
// --------------- Ajax send  ticketes
*/
add_action( 'wp_ajax_user_send_tiket', function () {
	if ( isset( $_POST['once'] ) && wp_verify_nonce( $_POST['once'], 'nirweb_ticket_user_send_ticket_act' ) ) {
		include_once NIRWEB_SUPPORT_INC_USER_FUNCTIONS_TICKET . 'ajax_user_send_tiket.php';
		func_user_send_tiket();
	}
	exit();
});


/*
 #--------------- Ajax send Answer
 */
add_action( 'wp_ajax_user_answer_ticket', function () {
	if ( isset( $_POST['once'] ) && wp_verify_nonce( $_POST['once'], 'nirweb_ticket_user_send_answer_act' ) ) {
		include_once NIRWEB_SUPPORT_INC_USER_FUNCTIONS_TICKET . 'ajax_user_send_answer.php';
		user_wpyar_answer_ticket();
		func_list_answer_ajax_user();
	}
	exit();
} );
/*
 #--------------- Ajax Filtter Ststus
 */
add_action( 'wp_ajax_filtter_ticket_status', function () {
	if ( isset( $_POST['once'] ) && wp_verify_nonce( $_POST['once'], 'nirweb_ticket_filtter_ticket_status_act' ) ) {
		include_once NIRWEB_SUPPORT_INC_USER_FUNCTIONS_TICKET . 'filter_ajax_ticket.php';
		filter_ajax_ticket_func();
	}
	exit();
});

