<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Event Modal Layout

class DPPEC_EventModal extends DpProEventCalendar {
	
	private $modal_date;
	private $event_info = array();

	function __construct( $event_id, $date ) 
	{

		$this->current_event = $event_id;
		$this->modal_date = $date;

		$this->layout_types();
		
    }

    private function layout_types ()
    {

    	if( defined( 'PEC_MODAL_TYPE_1' ) )
    		return;

    	define( 'PEC_MODAL_TYPE_1', 'modal' );

    	// General Constants
		define( 'PEC_MODAL_DEFAULT_BG', '#313131' );

    }

    private function set_event_info ( $info )
    {

	    $this->event_info = $info;

    }

    private function get_event_info ( $key )
    {

    	return $this->event_info[$key];

    }

    public function display_modal ( ) 
    {
		
		$html = "";
		
		$event = (object)array_merge( (array)self::getEventData($this->current_event), (array)$event );

		$this->current_event = $event;
		
		
		$all_working_days = '';

		if( $this->current_event->pec_daily_working_days && $this->current_event->recurring_frecuency == 1 ) {
			$all_working_days = self::$translation['TXT_ALL_WORKING_DAYS'];
			$this->current_event->date = $this->current_event->orig_date;
		}
			
		$time = self::date_i18n( self::$time_format, strtotime( $this->current_event->date ) );

		$end_datetime = self::get_end_datetime( $event );
		$end_date = $end_datetime['end_date'];
		$end_time = $end_datetime['end_time'];

		if( isset( $this->current_event->all_day ) && $this->current_event->all_day ) {
			$time = self::$translation['TXT_ALL_DAY'];
			$end_time = "";
		}

		$title = $this->current_event->title;
		
		$post_thumbnail_id = get_post_thumbnail_id( $this->current_event->id );
		$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, 'large' );
		$event_bg = "";
		$no_bg = false;
		
		if( $post_thumbnail_id )
			$event_bg = $image_attributes[0];
		else
			$no_bg = true;	
		
		if( $end_date == ' ' . self::$translation['TXT_TO'] . ' ' . self::date_i18n( get_option( 'date_format' ), strtotime( $this->current_event->date ) ) ) 
			$end_date = '';	

		//$href = self::get_permalink ( $event );

		$href = '';


		$event_location = "";
		if( $this->current_event->location != '' ) 
			$event_location = $this->current_event->location;

		if( $this->current_event->tbc ) 
			$pec_time = self::$translation['TXT_TO_BE_CONFIRMED'];

		$this->set_event_info( 
			array( 'href' => $href, 
					'title' => $title, 
					'end_date' => $end_date, 
					'all_working_days' => $all_working_days, 
					'time' => $time, 
					'end_time' => $end_time 
				) 
		);

		$html = '<div class="dp_pec_event_modal">';
		$html .= 	$this->get_item();
		$html .= '</div>';
		
		return $html;
	
	
	}

	private function get_item ()
	{

		$title = $this->get_event_info( 'title' );
		$href = $this->get_event_info( 'href' );
		$end_date = $this->get_event_info( 'end_date' );
		$all_working_days = $this->get_event_info( 'all_working_days' );
		$time = $this->get_event_info( 'time' );
		$end_time = $this->get_event_info( 'end_time' );
		$event_timezone = dpProEventCalendar_getEventTimezone( $this->current_event->id );

		$pec_time = ( $all_working_days != '' ? $all_working_days.' ' : '' ) . ( ( ( self::$calendar_obj->show_time && !$this->current_event->hide_time) || $this->current_event->all_day) ?  $time.$end_time.(self::$calendar_obj->show_timezone && !$this->current_event->all_day ? ' '.$event_timezone : '') : '');

		$post_thumbnail_id = get_post_thumbnail_id( $this->current_event->id );
		$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, 'large' );


		$html = '<div class="dp_pec_event_photo_wrap">';
		$html .= '	<div class="dp_pec_event_photo_overlay"></div>';

		if( $post_thumbnail_id ) 
			$html .= '	<div class="dp_pec_event_photo" style="background-image: url(' . $image_attributes[0] . ');">';
		else
			$html .= '	<div class="dp_pec_event_photo" style="background-color: ' . PEC_MODAL_DEFAULT_BG . ';">';

		$html .= '</div>';

		$html .= '</div>';


		$html .= 	'<div class="dp_pec_modal_event_center_text">';

		$categories = self::display_meta( $this->current_event, array( 'category' ) );

		if( $categories != '' ) 
		{

			$html .= '<div class="pec_event_page_categories">';

			$html .= $categories;
			
			$html .= '</div>';
			
		}

		// Show Event Date
		if( $this->current_event->tbc ) 
		{
			
			$html .= '<div class="pec_date_wrap">';
			$html .= '<span class="pec_date"><i class="fa fa-calendar"></i>' . self::$translation['TXT_TO_BE_CONFIRMED'] . '</span>';
			$html .= '</div>';
		
		} else {
			
			$html .= '<div class="pec_date_wrap">';
			$html .= '<span class="pec_date"><i class="fa fa-calendar"></i>' . self::date_i18n( get_option('date_format'), strtotime( $this->current_event->date ) ) . '</span>';
			
			if( $pec_time != "" ) 
				$html .= '<span class="pec_time">' . $pec_time . '</span>';

			$html .= '</div>';

		}

		if( $href != "" )
			$html .= 	'<a href="' . $href . '" target="' . self::$calendar_obj->link_post_target . '">';

		$html .= 			'<h2>' . $title . '</h2>';

		if( $href != "" )
			$html .= 	'</a>';

		$html .= '<div class="dp_pec_modal_event_text">';

		$speakers = self::display_meta( $this->current_event, array( 'speakers' ) );
		$location = self::display_meta( $this->current_event, array( 'location_short' ) );
		$phone = self::display_meta( $this->current_event, array( 'phone' ) );
		$organizer = self::display_meta( $this->current_event, array( 'organizer' ) );
		$age_range = self::display_meta( $this->current_event, array( 'age_range' ) );


		$html .= '<div class="dp_pec_modal_event_meta">';

		if( $location )
			$html .= '<div><span class="dp_pec_modal_event_meta_lbl">' . self::$translation['TXT_VENUE'] . '</span>' . $location . '</div>';

		if( $speaker )
			$html .= '<div><span class="dp_pec_modal_event_meta_lbl">' . self::$translation['TXT_SPEAKER'] . '</span>' . $speaker . '</div>';

		if( $organizer )
			$html .= '<div><span class="dp_pec_modal_event_meta_lbl">' . self::$translation['TXT_ORGANIZER'] . '</span>' . $organizer . '</div>';

		if( $phone )
			$html .= '<div><span class="dp_pec_modal_event_meta_lbl">' . self::$translation['TXT_PHONE'] . '</span>' . $phone . '</div>';

		$html .= '</div>';

		$html .= '<hr />';

		$html .= self::show_description( $this->current_event, $href, true, false );

		$html .= '</div>';

		$html .= '</div>';
		
		return $html;

	}
	
}
?>