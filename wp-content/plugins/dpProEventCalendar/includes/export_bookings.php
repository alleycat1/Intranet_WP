<?php

//Include Configuration
require_once ( dirname ( __FILE__ ) . '/../../../../wp-load.php' );

global $dpProEventCalendar, $wpdb, $pec_init;

if( !is_user_logged_in() ) 
	die();	

if( !current_user_can( 'edit_others_posts' ) || !isset($_GET['i']) || strlen($_GET['i']) > 11 ) 
	die(); 

$event_id = $_GET['i'];

// Init Base
$pec_init->init_base();
$event_id = $pec_init->base->get_event_id_by_code( $event_id );

header( "Content-type: application/vnd.ms-excel" );
header( "Content-Disposition: attachment; filename=bookings_".$event_id.".xls" );

?>

<table class="widefat" cellpadding="0" cellspacing="0" id="sort-table">
	<thead>
		<tr style="cursor:default !important;">
			<th><?php _e('Name','dpProEventCalendar'); ?></th>
			<th><?php _e('Email','dpProEventCalendar'); ?></th>
			<th><?php _e('Booking Date','dpProEventCalendar'); ?></th>
			<th><?php _e('Event Date','dpProEventCalendar'); ?></th>
			<?php if($event_id == 0) {?>
			<th><?php _e('Event Name','dpProEventCalendar'); ?></th>
			<?php }?>
            <th><?php _e('Quantity','dpProEventCalendar'); ?></th>
			<th><?php _e('Phone','dpProEventCalendar'); ?></th>
            <th><?php _e('Comment','dpProEventCalendar'); ?></th>
            <th><?php _e('Extra Fields','dpProEventCalendar'); ?></th>
			<th><?php _e('Status','dpProEventCalendar'); ?></th>
		 </tr>
	</thead>
	<tbody>
		<?php
		$id_list = $event_id;
        if(function_exists('icl_object_id') && $event_id > 0) {
            global $sitepress;
            if(is_object($sitepress)) {
	            $id_list_arr = array();
				$trid = $sitepress->get_element_trid($event_id, 'post_pec-events');
				$translation = $sitepress->get_element_translations($trid, 'post_pec-events');

				foreach($translation as $key) {
					$id_list_arr[] = $key->element_id;
				}

				if(!empty($id_list_arr)) {
					$id_list = implode(",", $id_list_arr);
				}
			}
		}

		$booking_count = 0;
		$querystr = "
		SELECT *
		FROM " . DP_PRO_EVENT_CALENDAR_TABLE_BOOKING;
		if($event_id > 0) {
			$querystr .= "
			WHERE id_event IN(".$id_list.")";
		}
		$querystr .= "
		ORDER BY booking_date ASC
		";
		$bookings_obj = $wpdb->get_results($querystr, OBJECT);
		foreach($bookings_obj as $booking) 
		{
		
			if(is_numeric($booking->id_user) && $booking->id_user > 0) {
				$userdata = get_userdata($booking->id_user);
				$userdata->display_name = $userdata->display_name . "(".$userdata->user_login." - ".$userdata->user_url.")";
			} else {
				$userdata = new stdClass();
				$userdata->display_name = $booking->name;
				$userdata->user_email = $booking->email;	
			}

			$status = "";
			switch($booking->status) 
			{
			
				case DP_PRO_EVENT_CALENDAR_BOOKING_COMPLETED:
					$status = __( 'Completed', 'dpProEventCalendar' );
					break;
				case DP_PRO_EVENT_CALENDAR_BOOKING_PENDING:
					$status = __( 'Pending', 'dpProEventCalendar' );
					break;
				case DP_PRO_EVENT_CALENDAR_BOOKING_CANCELED_BY_USER:
					$status = __( 'Canceled By User', 'dpProEventCalendar' );
					break;
				case DP_PRO_EVENT_CALENDAR_BOOKING_CANCELED:
					$status = __( 'Canceled', 'dpProEventCalendar' );
					break;

			}
			?>
		<tr>
			<td><?php echo $userdata->display_name?></td>
			<td><?php echo $userdata->user_email?></td>
			<td><?php echo date_i18n(get_option('date_format') . ' '. get_option('time_format'), strtotime($booking->booking_date))?></td>
			<td><?php echo date_i18n(get_option('date_format'), strtotime($booking->event_date))?></td>
			<?php if($event_id == 0) {?>
			<th><?php echo get_the_title($booking->id_event); ?></th>
			<?php }?>
			<td><?php echo $booking->quantity?></td>
            <td><?php echo $booking->phone?></td>
            <td><?php echo nl2br($booking->comment)?></td>
            <td>
            	<?php
            	$extra_fields = unserialize($booking->extra_fields);
            	if(!is_array($extra_fields)) 
            		$extra_fields = array();

            	$html = '';

            	foreach( $extra_fields as $key=>$value ) {

            		$field_index = array_keys($dpProEventCalendar['booking_custom_fields']['id'], str_replace('pec_custom_', '', $key));
            		
            		if(is_array($field_index)) {
                		$field_index = $field_index[0];
                	} else {
                		$field_index = '';
                	}

            		if($value != "" && is_numeric($field_index)) {
            			if($dpProEventCalendar['booking_custom_fields']['type'][$field_index] == 'checkbox') {
            				$value = __('Yes', 'dpProEventCalendar');
            			}
						$html .= '<div class="pec_event_page_custom_fields">
									<strong>'.$dpProEventCalendar['booking_custom_fields']['name'][$field_index].': </strong>'.$value;
						$html .= '</div>';
					}

            	}
            	
            	echo $html;
				?>
            </td>
			<td><?php echo $status?></td>
			
		</tr>
		<?php 
			$booking_count++;
		}
		
		if($booking_count == 0) 
			echo '<tr><td colspan="5"><p>'.__( 'No Booking Found.', 'dpProEventCalendar' ).'</p></td></tr>';	
		?>
	</tbody>
	<tfoot>
		<tr style="cursor:default !important;">
			<th><?php _e('Name','dpProEventCalendar'); ?></th>
			<th><?php _e('Email','dpProEventCalendar'); ?></th>
			<th><?php _e('Booking Date','dpProEventCalendar'); ?></th>
			<th><?php _e('Event Date','dpProEventCalendar'); ?></th>
			<?php if($event_id == 0) {?>
			<th><?php _e('Event Name','dpProEventCalendar'); ?></th>
			<?php }?>
            <th><?php _e('Quantity','dpProEventCalendar'); ?></th>
            <th><?php _e('Phone','dpProEventCalendar'); ?></th>
			<th><?php _e('Comment','dpProEventCalendar'); ?></th>
			<th><?php _e('Extra Fields','dpProEventCalendar'); ?></th>
			<th><?php _e('Status','dpProEventCalendar'); ?></th>
		 </tr>
	</tfoot>
	</table>