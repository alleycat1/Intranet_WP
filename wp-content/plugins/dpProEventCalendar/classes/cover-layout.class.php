<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// Cover Layout

class DPPEC_CoverLayout extends DpProEventCalendar {
	
	function __construct( ) 
	{
		
    }

    private function get_cover ( ) {
    	
		global $dpProEventCalendar;


		$event = self::getEventData(self::$event_id);

		
		$html = "";


		$event_timezone = dpProEventCalendar_getEventTimezone($event->id);

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
		$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, 'full' );
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
		
		$html .= '<div class="dp_pec_cover_event dp_pec_isotope '.($no_bg ? 'dp_pec_cover_no_img' : '').'">';

		$href = self::get_permalink( $event );

		//$html .= 	self::get_rating($event->id);

		$html .= '<div class="dp_pec_cover_link_image_bg" style="background-image:url(\''.$event_bg.'\');"></div>';

		$html .= get_the_post_thumbnail( $event->id, 'full', array( 'class' => 'dp_pec_cover_link_image' ) );

		$html .= '<div class="dp_pec_cover_text_wrap">';
			
		$html .= 	'<div class="dp_pec_cover_meta_data">';
		
		
		
		$html .= 	'</div>';	

		$html .= '</div>';
		
		$html .= '<div class="dp_pec_cover_event_center_text">';
		
		$html .= 	'<h2 class="dp_pec_cover_title">'.$title;

		if($event->color != "")
			$html .= '<div class="pec_preview_color" style="background-color:'.$event->color.';"></div>';

		$html .= 	'</h2>';


		$html .= 	'<div class="dp_pec_cover_grid">';

		$html .= 		'<div class="dp_pec_cover_date_left">';

		$html .=			'<i class="fa fa-calendar"></i>';

		$html .= 		'</div>';

		$html .=		'<div class="dp_pec_cover_date_right">';

		if($event->tbc) 
		{
		
			$html .= 	'<span class="pec_date">'.self::$translation['TXT_TO_BE_CONFIRMED'].'</span>';
		
		} else {
		
			$html .= 	'<span class="pec_date">'.self::date_i18n(get_option('date_format'), strtotime($event->date)).$end_date.'</span>';
		
		}

		$pec_time = ($all_working_days != '' ? $all_working_days.' ' : '').(((self::$calendar_obj->show_time && !$event->hide_time) || $event->all_day) ?  $time.$end_time.(self::$calendar_obj->show_timezone && !$event->all_day ? ' '.$event_timezone : '') : '');

		if($pec_time != "" && !$event->tbc) 
		{
		
			$html .= 	'<span class="pec_time">'.$pec_time.'</span>';
		
		}

		$html .= 		'<a href="'.$href.'"><i class="fa fa-angle-right"></i>Event Details</a>';
		
		
		$html .= 		'</div>';

		$html .= 	'</div>';

		$html .= '</div>';

		

		$html .= '</div>';

		
		return $html;

	}

    public function display_layout()
    {

		$html = '<div class="dp_pec_cover_wrapper dp_pec_calendar_'.self::$calendar_obj->id.'" id="'.self::$init_id.'">';

		$html .= 	$this->get_cover();

		$html .= '</div>';


		return $html;


    }
	
}
?>