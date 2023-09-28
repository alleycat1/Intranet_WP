<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// Carousel Layout

class DPPEC_CarouselLayout extends DpProEventCalendar {
	
	private $event_counter = 0;
	private $event_info = array();
	private $layout_type;

	function __construct( $type = '' ) 
	{

		$this->layout_types();

		$this->layout_type = $type;
		
    }

    private function layout_types ()
    {

    	if(defined('PEC_CAROUSEL_TYPE_1'))
    		return;

    	define('PEC_CAROUSEL_TYPE_1', 'carousel');
    	define('PEC_CAROUSEL_TYPE_2', 'carousel-2');
    	define('PEC_CAROUSEL_TYPE_3', 'carousel-3');

    	// General Constants
		define('PEC_CAROUSEL_DEFAULT_BG', '#313131');

    }

    private function set_event_info ( $info )
    {

	    $this->event_info = $info;

    }

    private function get_event_info ( $key )
    {

    	return $this->event_info[$key];

    }

    private function carousel_list($start_search = null, $end_search = null, $limit = 20) 
    {


		global $dpProEventCalendar;
		
		$html = "";
		$daily_events = array();

		if( self::$columns == '' ) self::$columns = 3;
		
		$pagination = self::get_pagination_number();

		$past = false;
		if(self::$opts['scope'] == 'past') 
		{
		
			$past = true;
		
		}

		$event_list = self::upcomingCalendarLayout( true, $limit, '', $start_search, $end_search, true, false, false, false, $past );

		if(is_array($event_list) && count($event_list) > 0) 
		{

			$html .= '<ul class="dp_pec_carousel_list dp_pec_columns_'.self::$columns.' dp_pec_carousel_gap_'.self::$opts['gap'].'">';

			foreach($event_list as $event) 
			{

				if($this->event_counter >= $limit) { break; }

				if($event->id == "") 
					$event->id = $event->ID;
				
				$event = (object)array_merge((array)self::getEventData($event->id), (array)$event);

				$this->current_event = $event;

				
				if($this->current_event->recurring_frecuency == 1){
					
					if(in_array($this->current_event->id, $daily_events)) {
						continue;	
					}
					
					$daily_events[] = $this->current_event->id;
				}
				
				$all_working_days = '';

				if($this->current_event->pec_daily_working_days && $this->current_event->recurring_frecuency == 1) {
					$all_working_days = self::$translation['TXT_ALL_WORKING_DAYS'];
					$this->current_event->date = $this->current_event->orig_date;
				}
					
				$time = self::date_i18n(self::$time_format, strtotime($this->current_event->date));

				$end_datetime = self::get_end_datetime( $event );
				$end_date = $end_datetime['end_date'];
				$end_time = $end_datetime['end_time'];

				if(isset($this->current_event->all_day) && $this->current_event->all_day) {
					$time = self::$translation['TXT_ALL_DAY'];
					$end_time = "";
				}

				$title = $this->current_event->title;
				
				$post_thumbnail_id = get_post_thumbnail_id( $this->current_event->id );
				$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, 'large' );
				$event_bg = "";
				$no_bg = false;
				
				if($post_thumbnail_id) {

					$event_bg = $image_attributes[0];

				} else {

					$no_bg = true;	

				}
				
				if($end_date == ' '.self::$translation['TXT_TO'].' '.self::date_i18n(get_option('date_format'), strtotime($this->current_event->date))) 
				{

					$end_date = '';	

				}

				$href = self::get_permalink ( $event );


				$event_location = "";
				if($this->current_event->location != '') 
				{

					$event_location = $this->current_event->location;

				}

				if($this->current_event->tbc) 
				{

					$pec_time = self::$translation['TXT_TO_BE_CONFIRMED'];

				}

				$this->set_event_info( 
					array( 'href' => $href, 
							'title' => $title, 
							'end_date' => $end_date, 
							'all_working_days' => $all_working_days, 
							'time' => $time, 
							'end_time' => $end_time 
						) 
				);

				$html .= $this->get_item();

				$this->event_counter++;

			}

			$html .= $this->get_nav();

		} 
		else 
		{

			$html .= $this->no_events();

		}

		
		
		return $html;
	
	
	}

	private function get_item ()
	{

		$title = $this->get_event_info( 'title' );
		$href = $this->get_event_info( 'href' );
		$end_date = $this->get_event_info( 'end_date' );
		$all_working_days = $this->get_event_info( 'all_working_days' );
		$time = $this->get_event_info( 'time' );
		$start_date = self::date_i18n( self::remove_year( get_option('date_format') ), strtotime($this->current_event->date) );
		$end_time = $this->get_event_info( 'end_time' );
		$event_timezone = dpProEventCalendar_getEventTimezone($this->current_event->id);

		$pec_time = ($all_working_days != '' ? $all_working_days.' ' : '').(((self::$calendar_obj->show_time && !$this->current_event->hide_time) || $this->current_event->all_day) ?  $time.$end_time.(self::$calendar_obj->show_timezone && !$this->current_event->all_day ? ' '.$event_timezone : '') : '');

		$html = '<li class="dp_pec_carousel_item '.($this->event_counter < self::$columns ? 'dp_pec_carousel_item_visible' : '').'">';

		$post_thumbnail_id = get_post_thumbnail_id( $this->current_event->id );
		$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, 'large' );

		switch( $this->layout_type )
		{

			case PEC_CAROUSEL_TYPE_1:

				if($post_thumbnail_id) 
				{
				
					$html .= '<div class="dp_pec_event_photo_wrap">';
					$html .= '	<div class="dp_pec_event_photo" style="background-image: url('.$image_attributes[0].');"></div>';
					
					$html .= self::display_featured_tag( $this->current_event );

					$html .= 	'<div class="dp_pec_carousel_event_center_text">';

					if($href != "")
					{

						$html .= 	'<a href="'.$href.'" target="'.self::$calendar_obj->link_post_target.'">';

					}

					$html .= 			'<h2>'.$title.'</h2>';

					if($href != "")
					{

						$html .= 	'</a>';

					}

					//$html .= self::get_rating($this->current_event->id);

					$html .= '	</div>';
					$html .= '	<div class="dp_pec_event_photo_overlay"></div>';
					$html .= '</div>';
				
				} else {
				
					$html .= '<div class="dp_pec_carousel_event_inner">';
					
					

					$html .= 	'<div class="dp_pec_carousel_event_head_noimg">';
					
					if($href != "")
					{

						$html .= 	'<a href="'.$href.'" target="'.self::$calendar_obj->link_post_target.'">';

					}

					$html .= 			'<h2>'.$title.'</h2>';

					if($href != "")
					{

						$html .= 	'</a>';

					}

					$html .= '</div>';

					// Get more options
					$html .= self::get_more_options($this->current_event);

					$html .= self::display_featured_tag( $this->current_event );
				
				}

				$html .= '<div class="dp_pec_clear"></div>';

				if( $post_thumbnail_id ) 
				{
				
					$html .= '<div class="dp_pec_carousel_event_inner">';

					// Get more options
					$html .= self::get_more_options( $this->current_event );
				
				}


				if($this->current_event->tbc) 
				{
				
					$html .= '<span class="pec_time"><i class="fa fa-calendar"></i>' . self::$translation['TXT_TO_BE_CONFIRMED'] . '</span>';
				
				} else {
				
					$html .= '<span class="pec_time"><i class="fa fa-calendar"></i>' . $start_date . $end_date .'</span>';
					
					if($pec_time != "") 
					{
					
						$html .= '<span class="pec_time"><i class="fa fa-clock"></i>' . $pec_time . '</span>';
					
					}
				
				}
				
				$html .= '<div class="dp_pec_clear"></div>';

				$html .= self::display_meta( $this->current_event,  array( 'location', 'speakers', 'organizer' ) );

			break;

			case PEC_CAROUSEL_TYPE_2:

				
				$html .= '<div class="dp_pec_event_photo_wrap">';

				if($post_thumbnail_id) 
				{

					$html .= '	<div class="dp_pec_event_photo" style="background-image: url('.$image_attributes[0].');"></div>';

				} else {

					$html .= '	<div class="dp_pec_event_photo" style="background-color: ' . PEC_CAROUSEL_DEFAULT_BG . ';"></div>';

				}
				
				$html .= self::display_featured_tag( $this->current_event );

				// Get more options
				$html .= self::get_more_options($this->current_event);

				$html .= 	'<div class="dp_pec_carousel_event_center_text">';

				$category = get_the_terms( $this->current_event->id, 'pec_events_category' ); 

				if(!empty($category)) 
				{
				
					$category_count = 0;
					$html .= '<div class="pec_event_page_categories">';
						
					$html .= '<span>';

					foreach ( $category as $cat)
					{
					
						if($category_count > 0) 
						{
						
							$html .= " / ";	
						
						}
						
						$html .= $cat->name;
						$category_count++;
					
					}
					
					$html .= '</span>';
					
					$html .= '</div>';

				}

				if($href != "")
				{

					$html .= 	'<a href="'.$href.'" target="'.self::$calendar_obj->link_post_target.'">';

				}

				$html .= 			'<h2>'.$title.'</h2>';

				if($href != "")
				{

					$html .= 	'</a>';

				}

				$html .= '	</div>';

				if($this->current_event->tbc) 
				{
				
					$html .= '<span class="pec_time"><i class="fa fa-calendar"></i>'.self::$translation['TXT_TO_BE_CONFIRMED'] .'</span>';
				
				} else {
				
					$html .= '<span class="pec_time"><i class="fa fa-calendar"></i>' . $start_date . '</span>';

				}

				$html .= '	<div class="dp_pec_event_photo_overlay"></div>';
				$html .= '</div>';
			
			

			break;

			case PEC_CAROUSEL_TYPE_3:

				$html .= '<div class="dp_pec_event_photo_wrap">';
				$html .= '	<div class="dp_pec_event_photo_overlay"></div>';

				if($post_thumbnail_id) 
				{

					$html .= '	<div class="dp_pec_event_photo" style="background-image: url('.$image_attributes[0].');">';

				} else {

					$html .= '	<div class="dp_pec_event_photo" style="background-color: ' . PEC_CAROUSEL_DEFAULT_BG . ';">';

				}

				$html .= '</div>';

				$html .= '</div>';

				
				$html .= self::display_featured_tag( $this->current_event );

				// Get more options
				$html .= self::get_more_options($this->current_event);


				$html .= 	'<div class="dp_pec_carousel_event_center_text">';

				if($href != "")
				{

					$html .= 	'<a href="'.$href.'" target="'.self::$calendar_obj->link_post_target.'">';

				}

				$html .= 			'<h2>'.$title.'</h2>';

				if($href != "")
				{

					$html .= 	'</a>';

				}

				$html .= '<div class="dp_pec_carousel_event_text">';

				$category = get_the_terms( $this->current_event->id, 'pec_events_category' ); 

				if(!empty($category)) 
				{
				
					$category_count = 0;
					$html .= '<div class="pec_event_page_categories">';
						
					$html .= '<span>';

					foreach ( $category as $cat)
					{
					
						if($category_count > 0) 
						{
						
							$html .= " / ";	
						
						}
						
						$html .= $cat->name;
						$category_count++;
					
					}
					
					$html .= '</span>';
					
					$html .= '</div>';

				}

				if($this->current_event->tbc) 
				{
				
					$html .= '<span class="pec_time"><i class="fa fa-calendar"></i>'.self::$translation['TXT_TO_BE_CONFIRMED'] .'</span>';
				
				} else {
				
					$html .= '<span class="pec_time">' . $start_date . '</span>';
					
					if($pec_time != "") 
					{
					
						$html .= '<span class="pec_time pec_time_big">' . $pec_time . '</span>';
					
					}

				}

				$html .= '<div class="dp_pec_clear"></div>';

				$html .= '<hr />';

				$html .= self::show_description( $this->current_event, $href, true );

				$html .= '	</div>';

				$html .= '	</div>';
				

			break;

		}
		

		$html .= '</li>';

		return $html;

	}

	private function get_nav () 
	{

		$html = '</ul>';

			$html .= '<ul class="dp_pec_carousel_nav">';


			$nav_total = ceil($this->event_counter / self::$columns);

			for($i = 0; $i < $nav_total; $i++) 
			{
			
				$html .= 	'<li>';

				$html .= 		'<a href="#" '.($i == 0 ? ' class="dp_pec_carousel_active"' : '').' data-pec-nav="'.$i.'"></a>';

				$html .= 	'</li>';

			}

			$html .= '</ul>';

			return $html;

	}

	private function no_events ()
	{

		$html = '<div class="dp_pec_carousel_no_events"><span class="dp_pec_event_no_events">'.self::$translation['TXT_NO_EVENTS_FOUND'].'</span></div>';

		return $html;

	}

    public function display_layout()
    {

    	$class = 'dp_pec_carousel_wrapper';

    	$class .= ($this->layout_type == PEC_CAROUSEL_TYPE_2 ? ' dp_pec_carousel_simple_wrapper' : '');

    	$class .= ($this->layout_type == PEC_CAROUSEL_TYPE_3 ? ' dp_pec_carousel_3_wrapper' : '');

    	$html = '<div class="'.$class.' pec_skin_'.self::$calendar_obj->skin.'" id="dp_pec_id'.self::$nonce.'">';
		
		$html .= 	'<div class="dp_pec_clear"></div>';
				
		$html .= 	$this->carousel_list(self::$opts['start_date'], self::$opts['end_date'], self::$limit);
		
		$html .= 	'<div class="dp_pec_clear"></div>';

		$html .= '</div>';


		return $html;


    }
	
}
?>