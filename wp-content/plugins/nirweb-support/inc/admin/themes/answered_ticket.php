<?php
require NIRWEB_SUPPORT_INC_ADMIN_FUNCTIONS_TICKET . 'func_list_tickets.php';
if ( current_user_can( 'administrator' ) ) {
	$tickets = wp_yap_get_list_answered_ticket();
} else {
	$tickets = wp_yap_get_list_answered_ticket( get_current_user_id() );
}
?>
<form method="post" id="list_all_ticket" name="list_all_ticket[]">
<table class="wp-list-table widefat striped">
	<thead>
	<tr>
		<th> <input type="checkbox" id="selectAll" /></th>
		<th>ID</th>
		<th><?php echo esc_html__( 'Status', 'nirweb-support' ); ?></th>
		<th><?php echo esc_html__( 'Subject', 'nirweb-support' ); ?></th>
		<th><?php echo esc_html__( 'Sender', 'nirweb-support' ); ?></th>
		<th><?php echo esc_html__( 'Department', 'nirweb-support' ); ?></th>
		<th><?php echo esc_html__( 'Priority', 'nirweb-support' ); ?></th>
		<th><?php echo esc_html__( 'Product', 'nirweb-support' ); ?></th>
		<th><?php echo esc_html__( 'Answer', 'nirweb-support' ); ?></th>
	</tr>
	</thead>
	<tfoot>
	<tr>
		<th></th>
		<th>ID</th>
		<th><?php echo esc_html__( 'Status', 'nirweb-support' ); ?></th>
		<th><?php echo esc_html__( 'Subject', 'nirweb-support' ); ?></th>
		<th><?php echo esc_html__( 'Sender', 'nirweb-support' ); ?></th>
		<th><?php echo esc_html__( 'Department', 'nirweb-support' ); ?></th>
		<th><?php echo esc_html__( 'Priority', 'nirweb-support' ); ?></th>
		<th><?php echo esc_html__( 'Product', 'nirweb-support' ); ?></th>
		<th><?php echo esc_html__( 'Answer', 'nirweb-support' ); ?></th>

	</tr>
	</tfoot>

	<tbody>
	<?php foreach ( $tickets[0] as $ticket ) : ?>
		<tr style="border: solid 1px #ccc">
			<th><input type="checkbox" id="frm_check_items" name="frm_check_items[]" value="<?php echo esc_html( $ticket->ticket_id ); ?>"></th>
			<th><?php echo esc_html( $ticket->ticket_id); ?></th>
			<th><?php echo esc_html( $ticket->name_status ); ?></th>
			<th><a href="<?php echo get_bloginfo( 'url' ) . '/wp-admin/admin.php?page=nirweb_ticket_send_ticket&action=edit&id=' . esc_html( $ticket->ticket_id ); ?>" class="su_link_tik">
                    <?php echo esc_html( $ticket->subject ); ?></a></th>
			<th><?php echo esc_html( $ticket->user_login ); ?></th>
			<th><?php echo esc_html( $ticket->depname ); ?></th>
			<th><?php echo esc_html( $ticket->proname ); ?></th>
			<th><?php echo esc_html( $ticket->product_name ); ?></th>
			<th><a href="<?php echo get_bloginfo( 'url' ) . '/wp-admin/admin.php?page=nirweb_ticket_send_ticket&action=edit&id='. esc_html( $ticket->ticket_id ); ?>" class="answer_ticket_wpys"><span class="dashicons dashicons-edit"></span></a></a></th>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>

</form>
<div class="nirweb_ticket_pagination">
	<?php echo wp_unslash( $tickets[1] ); ?>
</div>
