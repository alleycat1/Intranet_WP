<?php
require NIRWEB_SUPPORT_INC_ADMIN_FUNCTIONS_TICKET . 'func_send_ticket.php';
require NIRWEB_SUPPORT_INC_ADMIN_FUNCTIONS_TICKET . 'func_department.php';
require NIRWEB_SUPPORT_INC_ADMIN_FUNCTIONS_TICKET . 'func_status_and_priority.php';
require NIRWEB_SUPPORT_INC_ADMIN_FUNCTIONS_TICKET . 'func_list_tickets.php';
require NIRWEB_SUPPORT_INC_ADMIN_FUNCTIONS_TICKET . 'func_upload_file.php';

$departments = nirweb_ticket_ticket_get_list_department();
if ( isset( $_GET['id'] ) ) {
	include NIRWEB_SUPPORT_INC_ADMIN_THEMES_TICKET . 'answer-ticket.php';
} else {
	include NIRWEB_SUPPORT_INC_ADMIN_THEMES_TICKET . 'send_ticket.php'; }



