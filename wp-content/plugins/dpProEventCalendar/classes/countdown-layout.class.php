<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// Countdown Layout

class DPPEC_CountdownLayout extends DpProEventCalendar {
	

	function __construct( ) 
	{
		
    }

    public function display_layout()
    {


    	$event_list = self::upcomingCalendarLayout( true, (self::$limit + 1), '', null, null, true, false, true, false, false, '', false );

		$html = '<div class="dp_pec_countdown_wrapper" id="dp_pec_id'.self::$nonce.'">';

		$html .= '
			<div class="dp_pec_clear"></div>
			<div class="dp_pec_content">';

		$event_count = 0;
		$daily_events = array();

		if(is_array($event_list)) 
		{

			foreach ($event_list as $event) 
			{

				if($event->id == "") 
					$event->id = $event->ID;
				
				$event = (object)array_merge((array)self::getEventData($event->id), (array)$event);

				if($event_count >= self::$limit) 
				
					break;


				if($event->recurring_frecuency == 1 && self::$opts['group'])
				{
			
					if(in_array($event->id, $daily_events)) 
						continue;	
					
					$daily_events[] = $event->id;

				}

				$title = $event->title;
				$permalink = "";
				if( self::$calendar_obj->link_post ) 
				{

					$permalink = dpProEventCalendar_get_permalink( $event->id );
					$title = '<a href="'.$permalink.'" target="'.self::$calendar_obj->link_post_target.'">'.$title.'</a>';

				}
				
				
				$time = strtotime( $event->date );

				if( $event->all_day && $time <= current_time( 'timestamp' ) ) 
					continue;
					

				$tzo = get_option( 'gmt_offset' ) * 60;

				if( substr( $tzo, 0, 1 ) == "-" ) 
					$tzo = str_replace( "-", "", $tzo );
				else
					$tzo = str_replace( "+", "-", $tzo );

				$html .= '<div class="dp_pec_countdown_event" data-countdown-tzo="'.$tzo.'" data-current-year="'.current_time("Y").'" data-current-month="'.current_time("m").'" data-current-day="'.current_time("d").'" data-current-hour="'.current_time("H").'" data-current-minute="'.current_time("i").'" data-current-second="'.current_time("s").'" data-countdown-year="'.date("Y",$time).'" data-countdown-month="'.date("m",$time).'" data-countdown-day="'.date("d",$time).'" data-countdown-hour="'.date("H",$time).'" data-countdown-minute="'.date("i",$time).'">';

				$post_thumbnail_id = get_post_thumbnail_id( $event->id );
				$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, 'large' );
				
				$html .= '<div class="dp_pec_event_photo_wrap">';
				$html .= '	<div class="dp_pec_event_photo" style="background-image: url('.(isset($image_attributes[0]) ? $image_attributes[0] : '').');"></div>';

				$html .= '	<div class="dp_pec_countdown_event_center_text">';
				$html .= '		<h2>'.$title.'</h2>';

				$html .= '		<ul class="dp_pec_countdown">';

				$html .= '			<li class="dp_pec_countdown_days_wrap"><span class="dp_pec_countdown_days">--</span><p class="dp_pec_countdown_days_txt" data-day="'.self::$translation['TXT_DAY'].'" data-day="'.self::$translation['TXT_DAYS'].'">'.self::$translation['TXT_DAYS'].'</p></li>';

				$html .= '			<li class="dp_pec_countdown_hours_wrap"><span class="dp_pec_countdown_hours">--</span><p class="dp_pec_countdown_hours_txt" data-hour="'.self::$translation['TXT_HOUR'].'" data-hours="'.self::$translation['TXT_HOURS'].'">'.self::$translation['TXT_HOURS'].'</p></li>';

				$html .= '			<li class="dp_pec_countdown_minutes_wrap"><span class="dp_pec_countdown_minutes">--</span><p class="dp_pec_countdown_minutes_txt">'.self::$translation['TXT_MINUTES'].'</p></li>';

				$html .= '			<li class="dp_pec_countdown_seconds_wrap"><span class="dp_pec_countdown_seconds">--</span><p class="dp_pec_countdown_seconds_txt">'.self::$translation['TXT_SECONDS'].'</p></li>';

				$html .= '		</ul>';

				$html .= '	</div>';
				$html .= '	<div class="dp_pec_event_photo_overlay"></div>';
				$html .= '</div>';
				$html .= '<div class="dp_pec_clear"></div>';

				$html .= '</div>';

				$event_count++;

			}

		}

		$html .= '
			</div>
			<div class="dp_pec_clear"></div>
		</div>';


		return $html;


    }
	
}
?>