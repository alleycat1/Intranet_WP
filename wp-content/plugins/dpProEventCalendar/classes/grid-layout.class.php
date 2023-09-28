<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// Grid Layout

class DPPEC_GridLayout extends DpProEventCalendar {
	

	function __construct( ) 
	{
		
    }

    private function gridMonthList($start_search = null, $end_search = null, $limit = 20) {
    	
		global $dpProEventCalendar;
		
		$html = "";
		$daily_events = array();
		$event_counter = 1;
		
		$pagination = self::get_pagination_number();

		$past = false;
		
		if(self::$opts['scope'] == 'past') 
		{
		
			$past = true;
		
		}

		$event_list = self::upcomingCalendarLayout( true, $limit, '', $start_search, $end_search, true, false, false, false, $past );

		if(is_array($event_list) && count($event_list) > 0) 
		{

			foreach($event_list as $event) 
			{
			
				if($event->id == "") 
					$event->id = $event->ID;
				
				$event = (object)array_merge((array)self::getEventData($event->id), (array)$event);

				$event_timezone = dpProEventCalendar_getEventTimezone($event->id);

				if($event_counter > $limit) { break; }
				
				if($event->recurring_frecuency == 1)
				{
					
					if(in_array($event->id, $daily_events)) {
						continue;	
					}
					
					$daily_events[] = $event->id;
				}
				
				$all_working_days = '';
				if($event->pec_daily_working_days && $event->recurring_frecuency == 1) 
				{
				
					$all_working_days = self::$translation['TXT_ALL_WORKING_DAYS'];
					$event->date = $event->orig_date;
				
				}
					
				$time = self::date_i18n(self::$time_format, strtotime($event->date));

				$end_datetime = self::get_end_datetime( $event );
				$end_date = $end_datetime['end_date'];
				$end_time = $end_datetime['end_time'];

				if(isset($event->all_day) && $event->all_day) 
				{
				
					$time = self::$translation['TXT_ALL_DAY'];
				
					$end_time = "";
				
				}

				$title = $event->title;
				
				$post_thumbnail_id = get_post_thumbnail_id( $event->id );
				$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, 'large' );
				$event_bg = "";
				$no_bg = false;
				
				if($post_thumbnail_id) 
				{
				
					$event_bg = $image_attributes[0];
				
				} else {
				
					$no_bg = true;	
				
				}
				
				if($end_date == ' '.self::$translation['TXT_TO'].' '.self::date_i18n(get_option('date_format'), strtotime($event->date))) {
					$end_date = '';	
				}
				
				$html .= '<li class="dp_pec_grid_event dp_pec_isotope '.($no_bg ? 'dp_pec_grid_no_img' : '').'" data-event-number="'.$event_counter.'" '.($event_counter > $pagination ? 'style="display:none;"' : '').'>';

				$html .= self::display_featured_tag($event);

				$href = self::get_permalink( $event, $event->date );

				$html .= '<a href="'.$href.'" class="dp_pec_grid_link_image" target="'.self::$calendar_obj->link_post_target.'" title="" style="background-image:url(\''.$event_bg.'\');"></a>';
				
				$html .= '<div class="dp_pec_grid_event_center_text">';
				
				$html .= 	'<h2 class="dp_pec_grid_title">'.$title.'</h2>';

				if($event->tbc) 
				{
				
					$html .= '<span class="pec_date">'.self::$translation['TXT_TO_BE_CONFIRMED'].'</span>';
				
				} else {
				
					$html .= '<span class="pec_date">'.self::date_i18n(get_option('date_format'), strtotime($event->date)).$end_date.'</span>';
				
				}

				$html .= 	self::show_organizer( $event->organizer );
				
				//$html .= 	self::get_rating($event->id);
			
				$html .= '</div>';

				$html .= '<div class="dp_pec_grid_text_wrap">';
					
				$html .= 	'<div class="dp_pec_grid_meta_data">';
						
				$html .= 	self::display_author ( $event );
				
				$pec_time = ($all_working_days != '' ? $all_working_days.' ' : '').(((self::$calendar_obj->show_time && !$event->hide_time) || $event->all_day) ?  $time.$end_time.(self::$calendar_obj->show_timezone && !$event->all_day ? ' '.$event_timezone : '') : '');

				if($pec_time != "" && !$event->tbc) 
				{
				
					$html .= '<span class="pec_time">'.$pec_time.'</span>';
				
				}
				
				$html .= self::display_location ( $event, false, false );
				
				$html .= self::display_phone ( $event );
				
				$html .= 	'</div>';	

				$html .= '</div>';

				$html .= '</li>';

				$event_counter++;
			}

			if(($event_counter - 1) > $pagination) 
			{
			
				$html .= '<a href="#" class="pec_action_btn dpProEventCalendar_load_more" data-total="'.($event_counter - 1).'" data-pagination="'.$pagination.'">'.self::$translation['TXT_MORE'].'</a>';
			
			}

		} else {
		
			$html .= $this->no_events();	
		
		}
		
		return $html;
	}

	private function no_events ()
	{

		$html = '<li class="dp_pec_grid_no_events"><p class="dp_pec_event_no_events">'.self::$translation['TXT_NO_EVENTS_FOUND'].'</p></li>';

		return $html;

	}

    public function display_layout()
    {


    	$html = '<div class="dp_pec_grid_wrapper" id="dp_pec_id'.self::$nonce.'">';

				
		$html .= 	'<div class="dp_pec_content">';

		$html .= 		'<ul class="dp_pec_grid_columns_'.self::$columns.' dp_pec_grid_gap_'.self::$opts['gap'].'">';
				
		$html .= 			$this->gridMonthList(self::$opts['start_date'], self::$opts['end_date'], self::$limit);

		$html .= 		'</ul>';

		$html .= 	'</div>';

		$html .= '</div>';


		return $html;


    }
	
}
?>