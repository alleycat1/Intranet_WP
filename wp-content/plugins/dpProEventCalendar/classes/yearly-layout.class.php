<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// Yearly Layout

class DPPEC_YearlyLayout extends DpProEventCalendar {
	

	function __construct( ) 
	{
		
    }

    public function yearlyCalendarLayout() 
    {

		global $dpProEventCalendar_cache;

		$html = "";

		for($i = 1; $i <= 12; $i++) {

			$general_count = 0;

			self::$datesObj->initialize( strtotime( self::$datesObj->currentYear . '-' . str_pad( $i, 2, "0", STR_PAD_LEFT ) . '-01' ) );

			$html .= '
					<div class="dp_pec_yearly_calendar_month">
						<h4>' . self::$translation['MONTHS'][($i - 1)] . '</h4>
						<div class="dp_pec_month_grid">';

			if(self::$calendar_obj->first_day == 1) {
				if(self::$datesObj->firstDayNum == 0) { self::$datesObj->firstDayNum == 7;  }
				self::$datesObj->firstDayNum--;
				$html .= '
						<div class="dp_pec_yearly_calendar_month_header"><span>'.substr(self::$translation['DAY_MONDAY'],0 ,1).'</span></div>';
			} else {
				$html .= '
						<div class="dp_pec_yearly_calendar_month_header"><span>'.substr(self::$translation['DAY_SUNDAY'],0 ,1).'</span></div>
						<div class="dp_pec_yearly_calendar_month_header"><span>'.substr(self::$translation['DAY_MONDAY'],0 ,1).'</span></div>';

			}
			$html .= '
						<div class="dp_pec_yearly_calendar_month_header"><span>'.substr(self::$translation['DAY_TUESDAY'],0 ,1).'</span></div>
						<div class="dp_pec_yearly_calendar_month_header"><span>'.substr(self::$translation['DAY_WEDNESDAY'],0 ,1).'</span></div>
						<div class="dp_pec_yearly_calendar_month_header"><span>'.substr(self::$translation['DAY_THURSDAY'],0 ,1).'</span></div>
						<div class="dp_pec_yearly_calendar_month_header"><span>'.substr(self::$translation['DAY_FRIDAY'],0 ,1).'</span></div>
						<div class="dp_pec_yearly_calendar_month_header"><span>'.substr(self::$translation['DAY_SATURDAY'],0 ,1).'</span></div>';
			if(self::$calendar_obj->first_day == 1) {
				$html .= '
						<div class="dp_pec_yearly_calendar_month_header"><span>'.substr(self::$translation['DAY_SUNDAY'],0 ,1).'</span></div>';
			}

			if( self::$datesObj->firstDayNum != 6 ) {
			
				for($k = (self::$datesObj->daysInPrevMonth - self::$datesObj->firstDayNum); $k <= self::$datesObj->daysInPrevMonth; $k++) 
				{

					$html .= '
							<div class="dp_pec_yearly_calendar_month_date"><span class="dp_pec_disabled_date">'.$k.'</span></div>';
					
					$general_count++;
				}
				
			}

			$month_number = '01';
			$year = self::$datesObj->currentYear;
			
			$start = $year."-".$month_number."-01 00:00:00";

			if((self::$calendar_obj->hide_old_dates || self::$opts['hide_old_dates']) && date("Y-m") == $year."-".$month_number) {
				$start = date("Y-m-d H:i:s");
			}

			$list = self::upcomingCalendarLayout( true, 20, '', $start, $year."-12-31 23:59:59", true );

			
			for($k = 1; $k <= self::$datesObj->daysInCurrentMonth; $k++) 
			{
				$result = array();

				$curDate = self::$datesObj->currentYear.'-'.str_pad(self::$datesObj->currentMonth, 2, "0", STR_PAD_LEFT).'-'.str_pad($k, 2, "0", STR_PAD_LEFT);
				$countEvents = 0;
				$eventsCurrDate = array();

				if(is_array($list)) {
					foreach ($list as $key) {
						if(substr($key->date, 0, 10) == $curDate) {
							$result[] = $key;
						}		
					}
				}

				
				if(is_array($result)) {
					foreach($result as $event) {
						
						if($event->id == "") 
							$event->id = $event->ID;

						// Reset featured option
						unset($event->featured_event);
						
						$event = (object)array_merge((array)self::getEventData($event->id), (array)$event);

						if($event->pec_exceptions != "") {
							$exceptions = explode(',', $event->pec_exceptions);
							
							if($event->recurring_frecuency != "" && in_array($curDate, $exceptions)) {
								continue;
							}
						}

						if($event->pec_daily_working_days && $event->recurring_frecuency == 1 && (date('w', strtotime($curDate)) == "0" || date('w', strtotime($curDate)) == "6")) {
							continue;
						}
						
						if(!$event->pec_daily_working_days && $event->recurring_frecuency == 1 && $event->pec_daily_every > 1 && 
							( ((strtotime($curDate) - strtotime(substr($event->orig_date,0,11))) / (60 * 60 * 24)) % $event->pec_daily_every != 0 )
						) {
							continue;
						}
						
						if($event->recurring_frecuency == 2 && $event->pec_weekly_every > 1 && 
							( ((strtotime(substr($event->date,0,11)) - strtotime(substr($event->date,0,11))) / (60 * 60 * 24)) % ($event->pec_weekly_every * 7) != 0 )
						) {
							continue;
						}
						
						if($event->recurring_frecuency == 3 && $event->pec_monthly_every > 1 && 
							( !is_int (((date('m', strtotime($curDate)) - date('m', strtotime(substr($event->orig_date,0,11))))) / ($event->pec_monthly_every)) )
						) {
							continue;
						}
						
						if($event->featured_event) {
							array_unshift($eventsCurrDate, $event);
						} else {
							$eventsCurrDate[] = $event;
						}

						$countEvents++;
					}
				}

				$sp_date = $this->getSpecialDates( $curDate, true);

				if(isset($sp_date->color) && $sp_date->color) 
				{
				
					$special_date = " style='background-color: ".$sp_date->color.";' ";
				
				} else {
				
					$special_date = "";
				
				}

				$html .= '
					<div class="dp_pec_yearly_calendar_month_date dp_yearly_calendar_pec_date_'.strtolower(date('l', strtotime($curDate))).' '.($countEvents > 0 ? 'pec_has_events' : '').' '.(isset($special_date) && $special_date != "" ? 'dp_pec_special_date' : '').'" data-dppec-date="'.$curDate.'" data-count="'.$countEvents.'"><span'. $special_date .' class="dp_pec_yearly_calendar_number">'.$k.'</span>';
				if($countEvents > 0) {
					$html .= '<div class="dp_pec_has_events"></div>';

					$html .= '
					<div class="pec_yearly_eventsPreview">
						<ul>
					';

					foreach($eventsCurrDate as $event) {
						
						if($event->id == "") 
							$event->id = $event->ID;
						
						$event = (object)array_merge((array)self::getEventData($event->id), (array)$event);

						$time = self::date_i18n(self::$time_format, strtotime($event->date));

						if( self::$calendar_obj->link_post || true )
							$href = self::get_permalink ( $event );
						else
							$href = '#';
										
						$html .= '<li data-dppec-event="'.$event->id.'">';
						if( isset( $event->all_day ) && $event->all_day ) {
							$time = self::$translation['TXT_ALL_DAY'];
							$end_time = "";
						}

						$notime = false;
						if( self::$calendar_obj->show_time && ! $event->hide_time ) {
							$html .= '<span>'.$time.'</span>';
						} else 
							$notime = true;

						$html .= '<a ' . ( $notime ? 'class="dp_pec_yearly_notime" ' : '' ) . ' href="'.$href.'" '.($href != '#' ? 'target="'.self::$calendar_obj->link_post_target.'"' : '').'><h4>'.($event->color != "" ? '<div class="dp_pec_yearly_event_color" style="background-color:'.$event->color.';"></div>' : '').$event->title.'</h4></a>';
						
						
						$html .= '</li>';
					}
					
				$html .= '
						</ul>
					</div>';
			
				}
				$html .= '
					</div>
						';
						$general_count++;
			}


			$k = 1;

			while( $general_count <= 41 ) 
			{

				$html .= '
						<div class="dp_pec_yearly_calendar_month_date"><span class="dp_pec_disabled_date">'.$k.'</span></div>';
				
				$general_count++;
				$k++;
				
			}
			
		
			$html .= '</div>
				</div>';



		}

		return $html;
	
	}

	public function display_references()
    {

    	$html = '';

		if(self::$calendar_obj->show_references) 
		{

			$specialDatesList = self::getSpecialDatesList();

	    	$html = '<ul class="dp_pec_yearly_references">';

	    	$html .= '<li>';
			$html .= 	'<div class="dp_pec_yearly_references_color" style="background-color: '.self::$calendar_obj->current_date_color.'"></div>';
			$html .= 	'<span>'.self::$translation['TXT_CURRENT_DATE'].'</span>';
			$html .= '</li>';

	    	if(count($specialDatesList) > 0) 
			{
			
				foreach($specialDatesList as $key) 
				{

					$html .= '<li><div class="dp_pec_yearly_references_color" style="background-color: '.$key->color.'"></div> <span>'.$key->title.'</span></li>';

				}

			}

	    	$html .= '</ul>';

	    }

    	return $html;

    }

    public function display_layout()
    {

    	$categories_dropdown = self::get_categories_dropdown();
    	$location_dropdown = self::get_location_dropdown();
    	$speaker_dropdown = self::get_speaker_dropdown();

    	$html = '
			<div class="dp_pec_yearly_wrapper pec_skin_' . self::$calendar_obj->skin . '" id="dp_pec_id' . self::$nonce . '">
			';
			$html .= '
				<div class="dp_pec_clear"></div>

				<div class="dp_pec_yearly_header">
					<h3>' . self::$datesObj->currentYear . '</h3>
					<div class="dp_pec_yearly_nav">';

			if( $categories_dropdown != '' || $location_dropdown != '' || $speaker_dropdown != '' )
				$html .= '<a class="dp_pec_filter" href=""><i class="fas fa-sliders-h"></i></a>';

			$html .= '
						<a class="prev_year" href=""><i class="fa fa-chevron-left"></i></a>
						<a class="next_year" href=""><i class="fa fa-chevron-right"></i></a>
					</div>
				</div>

				<div class="dp_pec_nav">';

			$html .= $categories_dropdown;

			$html .= $location_dropdown;

			$html .= $speaker_dropdown;

			$html .= '

				</div>

				<div class="dp_pec_yearly_content">
					<div class="dp_pec_yearly_calendar">';
				
				$html .= $this->yearlyCalendarLayout();

				$html .= '</div>
						
					</div>';

				$html .= $this->display_references();

			$html .= '
				<div class="dp_pec_clear"></div>
			</div>';


		return $html;


    }
	
}
?>