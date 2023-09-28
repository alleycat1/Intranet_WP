<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// Bookings by User Layout

class DPPEC_BookingsUserLayout extends DpProEventCalendar {
	

	function __construct( ) 
	{
		
    }

    public function display_layout()
    {


    	global $current_user;
			
		$html = '<div class="dp_pec_wrapper pec_skin_'.self::$calendar_obj->skin.' dp_pec_calendar_'.self::$calendar_obj->id.'" id="dp_pec_id'.self::$nonce.'">';
		$html .= '<div style="clear:both;"></div>';
			
		$html .= '<div class="dp_pec_content">';
		
		$bookings_list = self::get_bookings_by_user($current_user->ID);

		if(!is_array($bookings_list) || empty($bookings_list)) 
		{
			
			$html .= self::no_events_found();

		} else {

			foreach($bookings_list as $key) 
			{
			
				$title = '<span class="dp_pec_event_title_sp">'.get_the_title($key->id_event).'</span>';

				if(self::$calendar_obj->link_post) 
				{
				
					$title = '<a href="'.dpProEventCalendar_get_permalink($key->id_event).'" target="'.self::$calendar_obj->link_post_target.'">'.$title.'</a>';	
				
				}

				$status = "";
				switch($key->status)
				{
				
					case DP_PRO_EVENT_CALENDAR_BOOKING_COMPLETED:
						$status = '';
						break;
				
					case DP_PRO_EVENT_CALENDAR_BOOKING_PENDING:
						$status = self::$translation['TXT_PENDING'];
						break;
				
					case DP_PRO_EVENT_CALENDAR_BOOKING_CANCELED_BY_USER:
						$status = self::$translation['TXT_CANCELED_BY_USER'];
						break;
				
					case DP_PRO_EVENT_CALENDAR_BOOKING_CANCELED:
						$status = self::$translation['TXT_CANCELED'];
						break;
				
				}
			
					$html .= '<div class="dp_pec_date_event">';

					$html .= '<span class="dp_pec_date_time"><i class="fa fa-clock"></i>'.self::date_i18n(get_option('date_format'), strtotime($key->event_date)). ' ' .self::date_i18n(self::$time_format, strtotime(get_post_meta($key->id_event, 'pec_date', true))).'</span>

					<span class="dp_pec_date_time">'.$status.'</span>';

					if($key->status == DP_PRO_EVENT_CALENDAR_BOOKING_CANCELED && $key->cancel_reason != '') 
					{
					
						$html .= '<p class="dp_pec_cancel_reason">'.nl2br($key->cancel_reason).'</p>';
					
					}

					if(self::$calendar_obj->booking_cancel && $key->status != DP_PRO_EVENT_CALENDAR_BOOKING_CANCELED_BY_USER && $key->status != DP_PRO_EVENT_CALENDAR_BOOKING_CANCELED) 
					{
					
						$html .= '<a href="#" class="pec_cancel_booking">'.self::$translation['TXT_CANCEL_BOOKING'].'</a>
						<div style="display:none;">
							<form enctype="multipart/form-data" method="post" class="dp_pec_new_event_wrapper add_new_event_form remove_event_form">
						
							<input type="hidden" value="'.$key->id.'" name="cancel_booking_id">
							<input type="hidden" value="'.$key->id_event.'" name="cancel_booking_event">
							<p>'.self::$translation['TXT_CANCEL_BOOKING_CONFIRM'].'</p>
							<div class="dp_pec_clear"></div>
							<div class="pec-add-footer">
								<button class="dp_pec_cancel_booking pec_action_btn">'.self::$translation['TXT_YES'].'</button>
								<button class="dp_pec_close pec_action_btn pec_action_btn_secondary">'.self::$translation['TXT_NO'].'</button>
								<div class="dp_pec_clear"></div>
							</div>
							</form>
						</div>';		
					
					}

					$html .= '
					<div class="dp_pec_clear"></div>
					<h2 class="dp_pec_event_title">
						'.$title.'
					</h2>';

					

				$html .= '
				</div>';
				
			}

		}
		

		$html .= 	'</div>';

		$html .= '</div>';


		return $html;


    }
	
}
?>