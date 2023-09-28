<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// Slider Layout

class DPPEC_SliderLayout extends DpProEventCalendar {
	
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

    	if(defined('PEC_SLIDER_TYPE_1'))
    		return;

    	define('PEC_SLIDER_TYPE_1', 'slider');
    	define('PEC_SLIDER_TYPE_2', 'slider-2');
    	define('PEC_SLIDER_TYPE_3', 'slider-3');

    	// General Constants
		define('PEC_SLIDER_DEFAULT_BG', '#313131');

    }

    private function sliderList($start_search = null, $end_search = null, $limit = 20) 
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

		if(is_array($event_list) && count($event_list) > 0) 
		{

			foreach($event_list as $event) 
			{

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
				
				if($end_date == ' '.self::$translation['TXT_TO'].' '.self::date_i18n(get_option('date_format'), strtotime($event->date))) 
				{

					$end_date = '';	

				}

				$href = self::get_permalink ( $event );

				$pec_time = ($all_working_days != '' ? $all_working_days.' ' : '').(((self::$calendar_obj->show_time && !$event->hide_time) || $event->all_day) ?  $time.$end_time.(self::$calendar_obj->show_timezone && !$event->all_day ? ' '.$event_timezone : '') : '');


				$event_location = "";
				if($event->location != '') 
				{

					$event_location = $event->location;

				}

				if($event->tbc) 
				{

					$pec_time = self::$translation['TXT_TO_BE_CONFIRMED'];

				}

				$category = get_the_terms( $event->id, 'pec_events_category' ); 
				$category_list_html = '';

				if(!empty($category)) 
				{

					$category_count = 0;
					foreach ( $category as $cat)
					{

						if($category_count > 0) 
						{

							$category_list_html .= " / ";	
						}

						$category_list_html .= $cat->name;
						$category_count++;

					}

				}
				if($category_list_html != "" && $event_location != "") 
				{

					$event_location = "/ ".$event_location;

				}

				if($event_counter == 1) 
				{

					$html .= '<ul class="dp_pec_slider_list">';

				}

				switch( $this->layout_type )
				{

					case PEC_SLIDER_TYPE_1:

						$html .= '
						<li class="dp_pec_slider_item '.($event_counter == 1 ? 'dp_pec_slider_item_visible' : '').'">';


						$html .= '
							<div class="dp_pec_slider_item_image" style="background-image:url(\''.$event_bg.'\');"></div>
							<div class="dp_pec_slider_details">';

						$html .= self::get_more_options($event);
						
						$html .= '
								<div class="dp_pec_slider_date">';
						if($event->tbc) 
						{

							$html .= '<div class="pec_date_slider_wrap"><span class="pec_date_slider_month pec_to_confirm">'.self::$translation['TXT_TO_BE_CONFIRMED'].'</span></div>';

						} else {
							
							$html .= '<div class="pec_date_slider_wrap"><div class="pec_date_slider_number"><span>'.self::date_i18n('d', strtotime($event->date)).'</span></div>';
							$html .= '<span class="pec_date_slider_month">'.self::date_i18n('F', strtotime($event->date)).'</span></div>';

							if($end_date != "") 
							{

								$html .= '<div class="pec_date_slider_wrap pec_date_slider_wrap_end_date"><div class="pec_date_slider_number"><span>'.self::date_i18n('d', strtotime($event->end_date)).'</span></div>';
								$html .= '<span class="pec_date_slider_month">'.self::date_i18n('F', strtotime($end_date)).'</span></div>';

							}
						
						}
						$html .= '<div class="dp_pec_clear"></div>';

						$html .= '</div>';

						

						$html .= '
								<h3>'.$title.'</h3>

								<ul class="dp_pec_slider_meta">
									<li '.($event_location == "" && $category_list_html == "" ? "style=\"display:none;\"" : "").'>
										<span>'.$category_list_html . $event_location.'</span>
									</li>
								</ul>';

						$html .= '<hr />';

						$html .= self::show_description( $event, $href, true );

						$html .= '
							</div>
						</li>';

					break;

					case PEC_SLIDER_TYPE_2:

						$html .= '<li class="dp_pec_slider_item '.($event_counter == 1 ? 'dp_pec_slider_item_visible' : '').'">';

						$html .= '<div class="dp_pec_event_photo_wrap">';

						if($post_thumbnail_id) 
						{

							$html .= '	<div class="dp_pec_event_photo" style="background-image: url('.$event_bg.');"></div>';

						} else {

							$html .= '	<div class="dp_pec_event_photo" style="background-color: ' . PEC_SLIDER_DEFAULT_BG . ';"></div>';

						}
						
						$html .= self::display_featured_tag( $event, false );

						// Get more options
						$html .= self::get_more_options($event);

						$html .= 	'<div class="dp_pec_slider_event_center_text">';

						$category = get_the_terms( $event->id, 'pec_events_category' ); 

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

						if($event->tbc) 
						{
						
							$html .= '<span class="pec_date">'.self::$translation['TXT_TO_BE_CONFIRMED'] .'</span>';
						
						} else {
						
							$html .= '<span class="pec_date">'.self::date_i18n(get_option('date_format'), strtotime($event->date));
					
							if($pec_time != "") 
							{
							
								$html .= '<span class="pec_time">'.$pec_time.'</span>';
							
							}

							$html .= '</span>';

						}

						$html .= '	<div class="dp_pec_event_photo_overlay"></div>';
						$html .= '</div>';

						$html .= '</li>';

					break;

					case PEC_SLIDER_TYPE_3:

						$html .= '
						<li class="dp_pec_slider_item '.($event_counter == 1 ? 'dp_pec_slider_item_visible' : '').'">';

						$html .= '<div class="dp_pec_event_photo_wrap">';
						$html .= '	<div class="dp_pec_event_photo_overlay"></div>';

						if($post_thumbnail_id) 
						{

							$html .= '	<div class="dp_pec_event_photo" style="background-image: url('.$event_bg.');">';

						} else {

							$html .= '	<div class="dp_pec_event_photo" style="background-color: ' . PEC_SLIDER_DEFAULT_BG . ';">';

						}

						$html .= '</div>';

						$html .= '</div>';

						
						$html .= self::display_featured_tag( $event, false );

						$html .= self::get_more_options($event);


						$html .= 	'<div class="dp_pec_slider_event_center_text">';

						if($href != "")
						{

							$html .= 	'<a href="'.$href.'" target="'.self::$calendar_obj->link_post_target.'">';

						}

						$html .= 			'<h2>'.$title.'</h2>';

						if($href != "")
						{

							$html .= 	'</a>';

						}

						$html .= '<div class="dp_pec_slider_event_text">';

						$category = get_the_terms( $event->id, 'pec_events_category' ); 

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

						if($event->tbc) 
						{
						
							$html .= '<span class="pec_time"><i class="fa fa-calendar"></i>'.self::$translation['TXT_TO_BE_CONFIRMED'] .'</span>';
						
						} else {
						
							$html .= '<span class="pec_time">'.self::date_i18n(get_option('date_format'), strtotime($event->date)).'</span>';

							if($pec_time != "") 
							{
							
								$html .= '<span class="pec_time pec_time_big">'.$pec_time.'</span>';
							
							}

						}

						$html .= '<div class="dp_pec_clear"></div>';

						$html .= '<hr />';

						$html .= self::show_description( $event, $href, true );

						$html .= '
								
							</div>
						</li>';

					break;

				}

				
				$event_counter++;
			}

			$html .= '</ul>';

			$html .= '
					<a class="dp_pec_slider_prev" '.(self::$opts['loop'] ? '' : 'style="display:none;"').' href="javascript:"><i class="fa fa-chevron-left"></i></a>
					<a class="dp_pec_slider_next" '.(count($event_list) == 1 ? 'style="display:none;"' : '').' href="javascript:"><i class="fa fa-chevron-right"></i></a>';

		} 
		else 
		{

			$html .= $this->no_events();

		}

		
		
		return $html;
	
	
	}

	private function no_events ()
	{

		$html = '<div class="dp_pec_slider_no_events"><span class="dp_pec_event_no_events">'.self::$translation['TXT_NO_EVENTS_FOUND'].'</span></div>';

		return $html;

	}

    public function display_layout()
    {

    	$class = 'dp_pec_slider_wrapper';

    	$class .= ($this->layout_type == PEC_SLIDER_TYPE_2 ? ' dp_pec_slider_2_wrapper' : '');

    	$class .= ($this->layout_type == PEC_SLIDER_TYPE_3 ? ' dp_pec_slider_3_wrapper' : '');

    	$html = '
			<div class="'.$class.''.(self::$opts['loop'] ? ' dp_pec_slider_loop' : '').' pec_skin_'.self::$calendar_obj->skin.'" id="dp_pec_id'.self::$nonce.'">
			';
		
		$html .= '
				<div class="dp_pec_clear"></div>';
				
		$html .= $this->sliderList(self::$opts['start_date'], self::$opts['end_date'], self::$limit);
		
		$html .= '
				<div class="dp_pec_clear"></div>
			</div>';


		return $html;


    }
	
}
?>