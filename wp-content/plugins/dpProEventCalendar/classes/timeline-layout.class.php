<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// Timeline Layout

class DPPEC_TimelineLayout extends DpProEventCalendar {
	

	function __construct( ) 
	{
		
    }

    public function display_layout()
    {


    	$event_list = self::upcomingCalendarLayout( true, (self::$limit + 1), '', null, null, true, false, true, false, false, '', false );

		

		$event_count = 0;
		$margin = 0;

		$total_width = 0;
		$items_arr = array();
		$month_margin = array();
		$year_margin = array();

		$last_month = '';
		$last_year = '';
		$last_day = 0;
		$last_date = 0;
		$html_tmp = '';

		if(is_array($event_list)) 
		{

			foreach ($event_list as $event) 
			{

				if($event->id == "") 
					$event->id = $event->ID;
				
				$event = (object)array_merge((array)self::getEventData($event->id), (array)$event);

				if($event_count >= self::$limit) 
				{

					break;

				}

				$time = strtotime($event->date);

				$curDate = date('Y-m-d', $time);

				$sp_date = $this->getSpecialDates( $curDate, true);
				$special_date = '';
				$special_date_title = '';

				if(isset($sp_date->color) && $sp_date->color) 
				{
				
					$special_date = " style='color: #fff; background-color: ".$sp_date->color.";' ";
					$special_date_title = $sp_date->title;
				
				}

				$title = $event->title;

				$permalink = "#";
				//if(self::$calendar_obj->link_post) 
				//{

					$permalink = self::get_permalink($event, $curDate);

				//}
				

				$year = date("Y",$time);
				$month = date("m",$time);
				$day = date("d",$time);

				
				

				if($day > $last_day && $month == $last_month) {

					$margin_diff = $day - $last_day;
					$margin_diff = ($margin_diff * 10);

					if($margin_diff <= 50)
						$margin_diff = 50;

					$margin += $margin_diff;

					

				} elseif($month != $last_month) {

					$margin += 100;

				}

				if($curDate != $last_date) {

					if($year != $last_year) {
						$year_margin[$year] = array( 
								'margin'	=> $margin
							);
					}

					if($month != $last_month) {
						$month_margin[$year][$month] = array( 
								'margin'	=> $margin
							);
					}

						$items_arr[$year][$month][$day] = array( 
								'margin'	=> $margin,
								'special_date' => $special_date,
								'special_date_title' => $special_date_title
							);
					

					$last_date = $curDate;
				}

				$items_arr[$year][$month][$day][] = array('title' => $title,
									'permalink' => $permalink);
				

				$last_month = $month;
				$last_year = $year;
				$last_day = $day;
				$event_count++;

			}

			$total_width = ($margin + 200);

			if($total_width < 1000) {

				$total_width = 1000;

			}

		}

		$html = '<div class="dp_pec_timeline_wrapper" id="dp_pec_id'.self::$nonce.'">';

		$html .= '<div class="dp_pec_timeline_drag" style="width: '.$total_width.'px;">';

		$html .= '<div class="dp_pec_timeline_line">';

		if(is_array($items_arr)) 
		{

			foreach ($items_arr as $year => $yk) 
			{

				$html .= '<div class="dp_pec_timeline_year_separator" style="left: '.$year_margin[$year]['margin'].'px;"><span>'.$year.'</span></div>';

				foreach ($yk as $month => $mk) 
				{

					$html .= '<div class="dp_pec_timeline_month_separator" style="left: '.$month_margin[$year][$month]['margin'].'px;"><span>'.self::$translation['MONTHS'][$month - 1].'</span></div>';


					foreach ($mk as $day => $dk) 
					{

						$html .= '<div class="dp_pec_timeline_date_separator" style="left: '.$dk['margin'].'px;">';

						$html .= '<div class="dp_pec_timeline_date_popup">';

						$html .= '<ul>';

						foreach ($dk as $event => $key) 
						{

							if(isset($key['title'])) {

								$html .= '<li><h4><a href="'.$key['permalink'].'" target="'.self::$calendar_obj->link_post_target.'">'.$key['title'].'</a></h4></li>';

							}
						
						}

						$html .= '</ul>';

						$html .= '</div>';

						$html .= '<div class="dp_pec_timeline_date_mark" '.$dk['special_date'].'>';
						if($dk['special_date_title'] != '' && self::$calendar_obj->show_references)
						{

							$html .= '<span class="dp_pec_timeline_special_date">'.$dk['special_date_title'].'</span>';
						
						}

						$html .= '</div><span>'.$day.'</span>';

						$html .= '</div>';

					}

				}

			}

		}

		
		$html .= '</div>';

		$html .= '</div>';

		$html .= '</div>';


		return $html;


    }
	
}
?>