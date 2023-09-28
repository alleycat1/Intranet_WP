<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// Card Layout

class DPPEC_CardLayout extends DpProEventCalendar {
	

	function __construct( ) 
	{
		
    }

    private function cardList($start_search = null, $end_search = null, $limit = 20) 
    {

		global $dpProEventCalendar;
		
		$html = "";
		$daily_events = array();
		$event_counter = 1;
		
		$pagination = self::get_pagination_number();

		$past = false;
		if(self::$opts['scope'] == 'past') {
			$past = true;
		}

		$event_list = self::upcomingCalendarLayout( true, $limit, '', $start_search, $end_search, true, false, false, false, $past );

		if(is_array($event_list) && count($event_list) > 0) {

			$html .= '<div class="dp_pec_content">';

			foreach($event_list as $event) {
				if($event_counter >= 5) {
					break;
				}
				if($event->id == "") 
					$event->id = $event->ID;
				
				$event = (object)array_merge((array)self::getEventData($event->id), (array)$event);

				$event_timezone = dpProEventCalendar_getEventTimezone($event->id);

				if($event_counter > $limit) { break; }
				
				if($event->recurring_frecuency == 1){
					
					if(in_array($event->id, $daily_events)) {
						continue;	
					}
					
					$daily_events[] = $event->id;
				}
				
				$all_working_days = '';
				if($event->pec_daily_working_days && $event->recurring_frecuency == 1) {
					$all_working_days = self::$translation['TXT_ALL_WORKING_DAYS'];
					$event->date = $event->orig_date;
				}
					
				$time = self::date_i18n(self::$time_format, strtotime($event->date));

				$end_datetime = self::get_end_datetime( $event );
				$end_date = $end_datetime['end_date'];
				$end_time = $end_datetime['end_time'];

				if(isset($event->all_day) && $event->all_day) {
					$time = self::$translation['TXT_ALL_DAY'];
					$end_time = "";
				}

				$title = $event->title;
				
				$post_thumbnail_id = get_post_thumbnail_id( $event->id );
				$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, 'large' );
				$event_bg = "";
				$no_bg = false;
				
				if($post_thumbnail_id) {
					$event_bg = $image_attributes[0];
				} else {
					$no_bg = true;	
				}

				$href = self::get_permalink ( $event );

				$pec_time = ($all_working_days != '' ? $all_working_days.' ' : '').(((self::$calendar_obj->show_time && !$event->hide_time) || $event->all_day) ?  $time.$end_time.(self::$calendar_obj->show_timezone && !$event->all_day ? ' '.$event_timezone : '') : '');


				$event_location = "";
				if($event->location != '') {
					$event_location = $event->location;
				}

				if($event->tbc) {
					$pec_time = self::$translation['TXT_TO_BE_CONFIRMED'];
				}

				if($event_counter == 1) {
					$html .= '
					<div class="dp_pec_card_selected" style="background-image:url(\''.$event_bg.'\');">
						<h3>'.$title.'</h3>

						<ul>
							<li class="dp_pec_card_location" '.($event_location == "" ? "style=\"display:none;\"" : "").'>
								<i class="fa fa-map"></i><span>'.$event_location.'</span>
							</li>
							<li class="dp_pec_card_time">
								<i class="fa fa-clock"></i><span>'.$pec_time.'</span>
							</li>
						</ul>
						<div class="dp_pec_card_selected_foot" '.($href == "" ? "style=\"display:none;\"" : "").'>
							<a href="'.$href.'"><i class="fa fa-arrow-right"></i></a>
							<div class="dp_pec_clear"></div>
						</div>
					</div>

					<ul class="dp_pec_card_list">';
				}
					
				$html .= '<li class="dp_pec_card_event '.($event_counter == 1 ? 'dp_pec_card_active' : "").' dp_pec_isotope dp_pec_card_columns_'.self::$columns.' '.($no_bg ? 'dp_pec_card_no_img' : '').'" data-event-title="'.$title.'" data-event-location="'.$event_location.'" data-event-time="'.$pec_time.'" data-event-link="'.$href.'" data-event-background="'.$event_bg.'" data-event-number="'.$event_counter.'" style="background-image:url(\''.$event_bg.'\');">';

					if($event->tbc) {
						$html .= '<span class="pec_date pec_to_confirm">'.self::$translation['TXT_TO_BE_CONFIRMED'].'</span>';
					} else {
						$html .= '<div class="pec_date_number"><span>'.self::date_i18n('d', strtotime($event->date)).'</span></div>';
						$html .= '<span class="pec_date">'.self::date_i18n('F', strtotime($event->date)).'</span>';
					}

			$html .= '</li>';
				
				$event_counter++;
			}

			$html .= '</ul>';

			$html .= '</div>';

		} else {
			$html .= $this->no_events();	
		}

		
		return $html;
	
	}

	private function no_events ()
	{

		$html = '<div class="dp_pec_card_no_events"><p class="dp_pec_event_no_events">'.self::$translation['TXT_NO_EVENTS_FOUND'].'</p></div>';

		return $html;

	}

    public function display_layout()
    {


    	$html = '<div class="dp_pec_card_wrapper '.(isset(self::$opts['orientation']) && self::$opts['orientation'] == 'vertical' ? 'dp_pec_card_vertical' : '').'" id="dp_pec_id'.self::$nonce.'">';
				
		$html .= 	$this->cardList(self::$opts['start_date'], self::$opts['end_date'], self::$limit);
			
		$html .= '</div>';


		return $html;


    }
	
}
?>