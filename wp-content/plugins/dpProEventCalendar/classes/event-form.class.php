<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// Frontend Event Form

class DPPEC_EventForm extends DpProEventCalendar {
	
	private $edit_event = '';

	private $time = '';

	private $modal = false;

	private $event_data = array();

	private $form_customization = array();


	function __construct( $event_id = "", $modal = false, $time = '' ) 
	{

		$this->form_customization = json_decode( parent::$calendar_obj->form_customization, true );
		
		if ( is_numeric($event_id) && $event_id > 0 ) 
		{

			$this->edit_event = $event_id;
			$this->get_event_data();

		}

		if( is_numeric( $time ) ) 
		{

			$this->time = $time;
			$this->set_time();

		}

		$this->modal = $modal;

    }

    private function is_edit() {

    	if ( is_numeric( $this->edit_event ) && $this->edit_event > 0) 
			return true;

		return false;

    }

    private function is_modal() {

    	if ($this->modal) {

			return true;

		}

		return false;

    }

    private function get_custom_field_value( $field )

    {

    	if(!$this->is_edit()) 
    		return;


    	return get_post_meta($this->edit_event, 'pec_custom_'.$field, true);


    }

    private function get_featured_image ( $size = 'large' )

    {

    	if(!$this->is_edit()) 
    		return;

    	$post_thumbnail_id = get_post_thumbnail_id( $this->edit_event );

    	if(!$post_thumbnail_id)
    		return'';

		$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, $size );

		return $image_attributes;

    }

    private function set_time()
    {

    	$event_data = array();


		$event_data['start_date'] = date( 'Y-m-d', $this->time );
		$event_data['start_date_formatted'] = self::date_i18n( get_option('date_format'), $this->time, true );

		$event_data['start_time_hh'] = date( 'H', $this->time );
		$event_data['start_time_mm'] = date( 'i', $this->time );

		$start_hour = $event_data['start_time_hh'];
		
		// All day ?
		if( $start_hour == '00' )
		{

			unset( $event_data['start_time_hh'] );
			unset( $event_data['start_time_mm'] );

			$event_data['all_day'] = true;

		} else {	

			if( dpProEventCalendar_is_ampm() ) 
			
				$start_hour = ($start_hour > 12 ? $start_hour - 12 : ($start_hour == '00' ? '12' : $start_hour)) . ' ' . date('A', mktime($start_hour, 0));

			$event_data['start_time'] = $start_hour . ':' . $event_data['start_time_mm'];

		}

		$this->event_data = $event_data;

    }

    private function get_event_data() 
    {

    	if( ! $this->is_edit() ) 
    		return;

    	$edit = $this->edit_event;

    	$event_data = array();

    	$event_data['post_category_ids'] = array();

    	$event_data['post_category_names'] = array();

    	$event_data['speaker_ids'] = array();

    	$event_data['speaker_names'] = array();

    	$event_data['categories'] = '';

		$event_data['pec_weekly_every'] = 1;

		$event_data['pec_daily_every'] = 1;

		$event_data['pec_monthly_every'] = 1;

		$event_data['pec_monthly_day'] = '';

		$event_data['pec_monthly_position'] = '';

		$event_data['pec_daily_working_days'] = 0;

		$event_data['pec_weekly_day'] = array();

	

		
		$id_calendar = get_post_meta($edit, 'pec_id_calendar', true);

		$id_calendar = explode(',', $id_calendar);

		$event_data['id_calendar'] = $id_calendar[0];
		
		
		$event_data['title'] = get_the_title($edit);

		$event_data['description'] = get_post_field('post_content', $edit);

		$post_category = get_the_terms($edit, 'pec_events_category');
		

		if( is_array( $post_category ) ) 
		{

			foreach($post_category as $category) 
			{

				$event_data['post_category_ids'][] = $category->term_id;

				$event_data['post_category_names'][] = $category->name;

			}

			$event_data['categories'] = implode(', ', $event_data['post_category_names']);
		
		}

		$speakers = get_post_meta( $edit, 'pec_speaker', true );
		if( ! empty( $speakers ) )
			$speakers = explode( ',', $speakers );

		if( is_array( $speakers ) ) 
		{

			foreach($speakers as $speaker) 
			{

				if( $speaker == '' ) continue;

				$event_data['speakers_ids'][] = $speaker;

				$event_data['speakers_names'][] = get_the_title( $speaker );

			}

			$event_data['speakers'] = implode(', ', $event_data['speakers_names']);
		
		}


		$event_data['date'] = get_post_meta($edit, 'pec_date', true);
		$event_data['start_date'] = date('Y-m-d', strtotime($event_data['date']));
		$event_data['start_date_formatted'] = self::date_i18n(get_option('date_format'),strtotime($event_data['date']), true);

		$event_data['start_time_hh'] = date('H', strtotime($event_data['date']));
		$event_data['start_time_mm'] = date('i', strtotime($event_data['date']));


		$event_data['end_time_hh'] = get_post_meta($edit, 'pec_end_time_hh', true);
		$event_data['end_time_mm'] = get_post_meta($edit, 'pec_end_time_mm', true);

		$start_hour = $event_data['start_time_hh'];
		$end_hour = $event_data['end_time_hh'];
							
		if(dpProEventCalendar_is_ampm()) 
		{
			
			if( is_numeric( $start_hour ) )
				$start_hour = ($start_hour > 12 ? $start_hour - 12 : ($start_hour == '00' ? '12' : $start_hour)) . ' ' . date('A', mktime($start_hour, 0));
			if( is_numeric( $end_hour ) )
				$end_hour = ($end_hour > 12 ? $end_hour - 12 : ($end_hour == '00' ? '12' : $end_hour)) . ' ' . date('A', mktime( $end_hour, 0 ) );
	
		}

		$event_data['start_time'] = $start_hour . ':' . $event_data['start_time_mm'];
		$event_data['end_time'] = $end_hour . ':' . $event_data['end_time_mm'];


		$event_data['all_day'] = get_post_meta($edit, 'pec_all_day', true);

		$event_data['timezone'] = get_post_meta($edit, 'pec_timezone', true);

		$event_data['recurring_frecuency'] = get_post_meta($edit, 'pec_recurring_frecuency', true);
		$event_data['recurring_frecuency_name'] = $this->get_frequency_name($event_data['recurring_frecuency']);

		$event_data['end_date'] = get_post_meta($edit, 'pec_end_date', true);
		$event_data['end_date_formatted'] = self::date_i18n(get_option('date_format'),strtotime($event_data['end_date']), true);

		$event_data['pec_extra_dates'] = get_post_meta( $edit, 'pec_extra_dates', true );
		$extra_dates_arr = explode(',', $event_data['pec_extra_dates']);
		$extra_dates_formatted = array();

		foreach($extra_dates_arr as $key) 
		{

			$extra_dates_formatted[] = self::date_i18n(get_option('date_format'),strtotime(trim($key)), true);

		}

		$event_data['extra_dates_formatted'] = implode(', ', $extra_dates_formatted);

		$event_data['extra_dates_parsed'] = implode(' /// ', $extra_dates_formatted);


		$event_data['link'] = get_post_meta($edit, 'pec_link', true);
		$event_data['share'] = get_post_meta($edit, 'pec_share', true);

		$event_data['color'] = get_post_meta($edit, 'pec_color', true);
		$event_data['pec_daily_every'] = get_post_meta($edit, 'pec_daily_every', true);
		$event_data['pec_daily_working_days'] = get_post_meta($edit, 'pec_daily_working_days', true);

		$event_data['pec_weekly_every'] = get_post_meta($edit, 'pec_weekly_every', true);
		$event_data['pec_monthly_every'] = get_post_meta($edit, 'pec_monthly_every', true);
		$event_data['pec_monthly_day'] = get_post_meta($edit, 'pec_monthly_day', true);

		$event_data['pec_monthly_position'] = get_post_meta($edit, 'pec_monthly_position', true);
		$event_data['pec_weekly_day'] = get_post_meta($edit, 'pec_weekly_day', true);


		if( ! is_array( $event_data['pec_weekly_day'] ) ) 
			$event_data['pec_weekly_day'] = array();

		$event_data['map'] = "";
		$event_data['hide_time'] = get_post_meta($edit, 'pec_hide_time', true);
		$event_data['location'] = get_post_meta($edit, 'pec_location', true);
		
		$event_data['location_name'] = '';
		$event_data['address'] = '';
		$event_data['map'] = '';
		$event_data['map_lnlat'] = '';

		if(is_numeric($event_data['location'])) 
		{
			$location = $event_data['location'];
			$event_data['location_name'] = get_the_title($location);
			$event_data['address'] = get_post_meta($location, 'pec_venue_address', true);
			$event_data['map'] = get_post_meta($location, 'pec_venue_map', true);
			$event_data['map_lnlat'] = get_post_meta($location, 'pec_venue_map_lnlat', true);

		}

		$event_data['phone'] = get_post_meta($edit, 'pec_phone', true);
		$event_data['booking_enable'] = get_post_meta($edit, 'pec_enable_booking', true);
		$event_data['limit'] = get_post_meta($edit, 'pec_booking_limit', true);
		$event_data['block_hours'] = get_post_meta($edit, 'pec_booking_block_hours', true);
		$event_data['price'] = get_post_meta($edit, 'pec_booking_price', true);


		$this->event_data = $event_data;

    }


    private function check_permissions() 
    {

    	$allow_user_add_event_roles = explode(',', parent::$calendar_obj->allow_user_add_event_roles);
		$allow_user_add_event_roles = array_filter($allow_user_add_event_roles);

		if(!is_array($allow_user_add_event_roles) || empty($allow_user_add_event_roles) || $allow_user_add_event_roles == "") {

			$allow_user_add_event_roles = array('all');	

		}
		
		if ( 
			(in_array(dpProEventCalendar_get_user_role(), $allow_user_add_event_roles) || 
			 in_array('all', $allow_user_add_event_roles) || 
			 (!is_user_logged_in() && !parent::$calendar_obj->assign_events_admin)
			)
		) {

			return true;

		}

		return false;
    }
	


	private function header() 
	{

		$html = '<div class="dp_pec_content_header">
					<h2 class="actual_month">'.str_replace('+', '', parent::$translation['TXT_ADD_EVENT']).'</h2>
				</div>';
		return $html;
	}

	private function is_field_enabled( $field )
	{

		if( ! empty( $this->form_customization ) ) 
		{
		
			return ( isset( $this->form_customization[ $field ] ) ? 1 : 0 );
		
		} else {

			return ( isset( parent::$calendar_obj->$field ) && parent::$calendar_obj->$field ? 1 : 0 );

		}

	}

	private function get_field( $field, $default_value = '' ) 
	{
		
		global $wpdb, $dpProEventCalendar, $dp_pec_payments;


		$html = '';

		switch( $field ) 
		{
			
			case 'category':

				if( $this->is_field_enabled( 'form_show_category' ) ) 
				{

					$cat_arr = array();
					if( ! empty( parent::$category ) ) 
						$cat_arr = explode(",", parent::$category);

					$cat_args = array(
							'taxonomy' => 'pec_events_category',
							'hide_empty' => 0
						);

					if(parent::$calendar_obj->category_filter_include != "") 
						$cat_args['include'] = parent::$calendar_obj->category_filter_include;

					$categories = get_categories($cat_args); 

					if(count($categories) > 0) 
					{

						$html .= '<div class="dp_pec_event_form_options_item dp_pec_event_form_options_item_category">
									<h4>' . $this->get_form_item_title( 'TXT_CATEGORY' ) . '</h4>
									<span class="dp_pec_event_form_options_item_sub">'.$this->parse_value($this->parse_event_data('categories')).'</span>

									<div class="dp_pec_event_form_options_item_hidden">
										<ul>';

						foreach ($categories as $category) 
						{

							if( ! empty( $cat_arr ) && ! in_array( $category->term_id, $cat_arr ) ) 
								continue;

							$html .= 	'<li>';
							$html .=		'<label>
												<input type="checkbox" '.(in_array( $category->term_id, $this->parse_value( $this->parse_event_data('post_category_ids'), array() ) )  ? 'checked="checked"' : '').' data-cat-name="'.esc_attr($category->cat_name).'" name="category-'.esc_attr($category->term_id).'" class="new_event_checkbox" value="'.esc_attr($category->term_id).'" />';
							$html .= 		$category->cat_name;
							$html .=	'	</label>
										</li>';

						}

						$html .= '		</ul>
									</div>
								</div>';

					}
				}

				break;


			case 'date':

				$html .= '<div class="dp_pec_event_form_options_item">
							<h4>' . $this->get_form_item_title( 'TXT_EVENT_DATE' ) . '</h4>
							<div class="dp_pec_event_form_options_item_sub">
								<p>'.parent::$translation['TXT_EVENT_START'].' <span class="dp_pec_event_form_options_item_date_start">'.$this->parse_value($this->parse_event_data('start_date_formatted')).'</span></p>';

				if( $this->is_field_enabled( 'form_show_end_date' ) )
				{
				
					$html .=		'<p>'.parent::$translation['TXT_EVENT_END'].' <span class="dp_pec_event_form_options_item_date_end">'.$this->parse_value($this->parse_event_data('end_date_formatted')).'</span></p>';
				
				}

				$html .=	'</div>
							

							<div class="dp_pec_event_form_options_item_hidden">

								<ul>
									<li>
										<p>'.parent::$translation['TXT_EVENT_START_DATE'].'</p>
										<input type="hidden" value="'.$this->parse_value($this->parse_event_data('start_date'), date('Y-m-d')).'" class="dp_pec_date_input_hidden" name="date_hidden" />
										<input type="text" readonly="readonly" name="date" maxlength="10" id="" class="dp_pec_new_event_text dp_pec_date_input" value="'.$this->parse_value($this->parse_event_data('start_date_formatted'), date('Y-m-d')).'" />
									</li>';

				if( $this->is_field_enabled( 'form_show_end_date' ) ) 
				{

					$html .= '
					<li>
						<p>'.parent::$translation['TXT_EVENT_END_DATE'].'</p>
						<input type="hidden" value="'.$this->parse_value($this->parse_event_data('end_date')).'" class="dp_pec_end_date_input_hidden" name="end_date_hidden" />
						<input type="text" readonly="readonly" name="end_date" maxlength="10" id="" class="dp_pec_new_event_text dp_pec_end_date_input" value="'.$this->parse_value($this->parse_event_data('end_date_formatted')).'" />
						<button type="button" class="dp_pec_clear_end_date">
							<i class="fas fa-trash-alt"></i>
						</button>
					</li>';

				}

				if( $this->is_field_enabled( 'form_show_extra_dates' ) ) 
				{

					$html .= '<li>';

					$html .= '<p>'.parent::$translation['TXT_EXTRA_DATES'].'</p>';

					$html .= '<input type="hidden" value="'.$this->parse_value($this->parse_event_data('pec_extra_dates')).'" name="extra_dates_hidden" class="dp_pec_extra_dates_hidden" />';

					$html .= '<input type="text" data-extra-dates-parsed="'.$this->parse_value($this->parse_event_data('extra_dates_parsed')).'" value="'.$this->parse_value($this->parse_event_data('extra_dates_formatted')).'" placeholder="" id="" class="dp_pec_extra_dates dp_pec_new_event_text" readonly="readonly" style="max-width: 300px;" name="extra_dates" />';

					$html .= '</li>';

				}

				$html .= 		'</ul>
							</div>';

				$html .= '</div>';

				break;


			case 'time':

				if( $this->is_field_enabled( 'form_show_start_time' ) ) 
				{
					$html .= '<div class="dp_pec_event_form_options_item dp_pec_event_form_options_item_time">';

					$html .= '	<h4>' . $this->get_form_item_title( 'TXT_EVENT_TIME' ) . '</h4>
								<div class="dp_pec_event_form_options_item_sub">
									<p>'.parent::$translation['TXT_EVENT_START'].' <span class="dp_pec_event_form_options_item_time_start">'.$this->parse_value($this->parse_event_data('start_time')).'</span></p>';

					if( $this->is_field_enabled( 'form_show_end_time' ) ) 
					{
					
						$html .= 	'<p>'.parent::$translation['TXT_EVENT_END'].' <span class="dp_pec_event_form_options_item_time_end">'.$this->parse_value($this->parse_event_data('end_time')).'</span></p>';

					}

					$html .= '		</div>

								<div class="dp_pec_event_form_options_item_hidden">
									
									<ul>';
					$html .= 			'<li>';

					$html .= 				'<p>'.parent::$translation['TXT_EVENT_START'].'</p>';


					$html.= 				'<select autocomplete="off" class="dp_pec_new_event_time dp_pec_start_time_hh" name="time_hours" id="" style="width:'.(dpProEventCalendar_is_ampm() ? '70' : '50').'px;">';
							for($i = 0; $i <= 23; $i++) 
							{
							
								$hour = str_pad($i, 2, "0", STR_PAD_LEFT);
							
								if(dpProEventCalendar_is_ampm()) 
								{
							
									$hour = ($hour > 12 ? $hour - 12 : ($hour == '00' ? '12' : $hour)) . ' ' . date('A', mktime($hour, 0));
							
								}
							
								$hour_format = str_pad($i, 2, "0", STR_PAD_LEFT);

								$html .= '
								<option value="'.$hour_format.'" ' . ($this->parse_value($this->parse_event_data('start_time_hh')) == $hour_format ? 'selected="selected"' : '') . '>'.$hour.'</option>';
							
							}
						$html .= '
						</select>
						<select autocomplete="off" class="dp_pec_new_event_time dp_pec_start_time_mm" name="time_minutes" id="pec_time_minutes" style="width:50px;">';
							for($i = 0; $i <= 59; $i += 15) {

								$minute_format = str_pad($i, 2, "0", STR_PAD_LEFT);

								$html .= '
								<option value="'.$minute_format.'" ' . ($this->parse_value($this->parse_event_data('start_time_mm')) == $minute_format ? 'selected="selected"' : '') . '>'.$minute_format.'</option>';
							}
						$html .= '
						</select>
										</li>';

					

					if( $this->is_field_enabled( 'form_show_end_time' ) ) 
					{

						$html .= '<li>';

						$html .= 	'<p>'.parent::$translation['TXT_EVENT_END'].'</p>';

						$html .= '
							<div class="dp_pec_clear"></div>
							<select autocomplete="off" class="dp_pec_new_event_time dp_pec_end_time_hh" name="end_time_hh" id="" style="width:'.(dpProEventCalendar_is_ampm() ? '70' : '50').'px;">
								<option value="">--</option>';

							for($i = 0; $i <= 23; $i++) {

								$hour = str_pad($i, 2, "0", STR_PAD_LEFT);

								if(dpProEventCalendar_is_ampm()) {

									$hour = ($hour > 12 ? $hour - 12 : ($hour == '00' ? '12' : $hour)) . ' ' . date('A', mktime($hour, 0));

								}

								$hour_format = str_pad($i, 2, "0", STR_PAD_LEFT);

								$html .= '
								<option value="'.$hour_format.'" ' . ($this->parse_value($this->parse_event_data('end_time_hh')) == $hour_format ? 'selected="selected"' : '') . '>'.$hour.'</option>';
							}
						$html .= '
						</select>
						<select autocomplete="off" class="dp_pec_new_event_time dp_pec_end_time_mm" name="end_time_mm" id="" style="width:50px;">
							<option value="">--</option>';

							for($i = 0; $i <= 59; $i += 15) {

								$minute_format = str_pad($i, 2, "0", STR_PAD_LEFT);

								$html .= '
								<option value="'.$minute_format.'" ' . ($this->parse_value($this->parse_event_data('end_time_mm')) == $minute_format ? 'selected="selected"' : '') . '>'.$minute_format.'</option>';

							}

						$html .= '
						</select>';

						$html .= 			'</li>';

					}

					if( $this->is_field_enabled( 'form_show_timezone' ) ) 
					{
						
						$html .= '<li>';

						$html .= 	'<p>'.parent::$translation['TXT_SELECT_TIMEZONE'].'</p>';

						$html .= '
						<select autocomplete="off" name="timezone" class="pec_timezone_form">';
						
						$current_offset = get_option('gmt_offset');
						$tzstring = get_option('timezone_string');
						
						if ( empty($tzstring) ) { // Create a UTC+- zone if no timezone string exists
							
							$check_zone_info = false;
							
							if ( 0 == $current_offset )
								$tzstring = 'UTC+0';
							elseif ($current_offset < 0)
								$tzstring = 'UTC' . $current_offset;
							else
								$tzstring = 'UTC+' . $current_offset;
						}

						$pec_timezone = $this->parse_value($this->parse_event_data('timezone'), $tzstring);


		                $html .= wp_timezone_choice($pec_timezone); 
						
						$html .= '</select>';

						$html .= '</li>';
					}
					

					if( $this->is_field_enabled( 'form_show_hide_time' ) ) 
					{

						$html .= '<li>';

						$html .= '<label class="dp_pec_form_desc dp_pec_form_desc_left"><input type="checkbox" class="new_event_checkbox" name="hide_time" id="" value="1" ' . ( 1 == $this->parse_value( $this->parse_event_data('hide_time') )  ? 'checked="checked"' : '') . ' />'.parent::$translation['TXT_EVENT_HIDE_TIME'].'</label>';

						$html .= '</li>';
							

					} 

					if ( $this->is_field_enabled( 'form_show_all_day' ) ) 
					
					{
						$html .= '<li>';

						$html .= '<label class="dp_pec_form_desc dp_pec_form_desc_left"><input type="checkbox" class="new_event_checkbox" name="all_day" id="" value="1" ' . ( 1 == $this->parse_value( $this->parse_event_data('all_day') )  ? 'checked="checked"' : '') . ' />'.parent::$translation['TXT_EVENT_ALL_DAY'].'</label>';

						$html .= '</li>';
					}

					$html .= 		'</ul>';

					$html .= 	'</div>';

					$html .= '</div>';
				}

				break;

			case 'frequency':

				if( $this->is_field_enabled( 'form_show_frequency' ) ) 
				{

					$html .= '<div class="dp_pec_event_form_options_item dp_pec_event_form_options_item_frequency">
					<h4>' . $this->get_form_item_title( 'TXT_EVENT_FREQUENCY' ) . '</h4>

					<div class="dp_pec_event_form_options_item_sub">
						'.$this->parse_value($this->parse_event_data('recurring_frecuency_name')).'
					</div>

					<div class="dp_pec_event_form_options_item_hidden">

						<ul>
							
							<li>

								<select autocomplete="off" name="recurring_frecuency" id="pec_recurring_frecuency" class="pec_recurring_frequency">';

								for ($i = 0; $i <= 4; $i++)
								{

									$html .= '<option value="'.$i.'" ' . ($this->parse_value($this->parse_event_data('recurring_frecuency')) == $i ? 'selected="selected"' : '') . '>'.$this->get_frequency_name($i).'</option>';

								}

					$html .=	'</select>

							</li>';
					
					$html .= '
					<li class="pec_daily_frequency" style="display:none;">
						<div id="pec_daily_every_div">
							<p>'
						 . parent::$translation['TXT_EVERY'] . 

						 ' <input type="number" min="1" max="99" style="width:40px;display: inline-block;" maxlength="2" class="dp_pec_new_event_text" name="pec_daily_every" id="pec_daily_every" value="'.$this->parse_value( $this->parse_event_data('pec_daily_every'), 1).'" /> '

						 .parent::$translation['TXT_DAYS'] . ' 

						 	</p>

						</div>
						<div id="pec_daily_working_days_div">

							<p>

							<input type="checkbox" name="pec_daily_working_days" id="pec_daily_working_days" class="new_event_checkbox" onclick="pec_check_daily_working_days(this);" value="1" ' . ( 1 == $this->parse_value( $this->parse_event_data('pec_daily_working_days') )  ? 'checked="checked"' : '').' />'. parent::$translation['TXT_ALL_WORKING_DAYS'] . '

							</p>

						</div>
					</li>';
					
					$html .= '
					<li class="pec_weekly_frequency" style="display:none;">
						
						<p>
						
						'. parent::$translation['TXT_REPEAT_EVERY'].' <input type="number" min="1" max="99" style="width:40px;display: inline-block;" class="dp_pec_new_event_text" maxlength="2" name="pec_weekly_every" value="'.$this->parse_value( $this->parse_event_data('pec_weekly_every'), 1).'" /> '. parent::$translation['TXT_WEEKS_ON'].'
						
						</p>
						
						<ul>';


					for($i = 1; $i <= 7; $i++)
					{

						$html .= '
							<li>
								<input type="checkbox" class="new_event_checkbox" value="'.$i.'" name="pec_weekly_day[]" ' . ( in_array( $i, $this->parse_value( $this->parse_event_data('pec_weekly_day'), array() ))  ? 'checked="checked"' : '').' /> &nbsp; '. $this->get_day_name($i) . '
							</li>';

					}

					$html .= '
						</ul>
						
					</li>';
					
					$html .= '
					<li class="pec_monthly_frequency" style="display:none;">
						
						<p>
						
						'. parent::$translation['TXT_REPEAT_EVERY'].' <input type="number" min="1" max="99" style="width:40px;display: inline-block;" class="dp_pec_new_event_text" maxlength="2" name="pec_monthly_every" value="'.$this->parse_value( $this->parse_event_data('pec_monthly_every'), 1).'" /> ' . parent::$translation['TXT_MONTHS_ON'] . '
						
						</p>
						
						<select autocomplete="off" name="pec_monthly_position" id="pec_monthly_position" style="width:90px;">
							<option value=""> ' . parent::$translation['TXT_RECURRING_OPTION'] . '</option>
							<option value="first" ' . ($this->parse_value($this->parse_event_data('pec_monthly_position')) == 'first' ? 'selected="selected"' : '') . '> ' . parent::$translation['TXT_FIRST'] . '</option>
							<option value="second" ' . ($this->parse_value($this->parse_event_data('pec_monthly_position')) == 'second' ? 'selected="selected"' : '') . '> ' . parent::$translation['TXT_SECOND'] . '</option>
							<option value="third" ' . ($this->parse_value($this->parse_event_data('pec_monthly_position')) == 'third' ? 'selected="selected"' : '') . '> ' . parent::$translation['TXT_THIRD'] . '</option>
							<option value="fourth" ' . ($this->parse_value($this->parse_event_data('pec_monthly_position')) == 'fourth' ? 'selected="selected"' : '') . '> ' . parent::$translation['TXT_FOURTH'] . '</option>
							<option value="last" ' . ($this->parse_value($this->parse_event_data('pec_monthly_position')) == 'last' ? 'selected="selected"' : '') . '> ' . parent::$translation['TXT_LAST'] . '</option>
						</select>
						
						<select autocomplete="off" name="pec_monthly_day" id="pec_monthly_day" style="width:150px;">
						<option value=""> ' . parent::$translation['TXT_RECURRING_OPTION'] . '</option>
							<option value="monday" ' . ($this->parse_value($this->parse_event_data('pec_monthly_day')) == 'monday' ? 'selected="selected"' : '') . '> ' . parent::$translation['DAY_MONDAY'] . '</option>
							<option value="tuesday" ' . ($this->parse_value($this->parse_event_data('pec_monthly_day')) == 'tuesday' ? 'selected="selected"' : '') . '> ' . parent::$translation['DAY_TUESDAY'] . '</option>
							<option value="wednesday" ' . ($this->parse_value($this->parse_event_data('pec_monthly_day')) == 'wednesday' ? 'selected="selected"' : '') . '> ' . parent::$translation['DAY_WEDNESDAY'] . '</option>
							<option value="thursday" ' . ($this->parse_value($this->parse_event_data('pec_monthly_day')) == 'thursday' ? 'selected="selected"' : '') . '> ' . parent::$translation['DAY_THURSDAY'] . '</option>
							<option value="friday" ' . ($this->parse_value($this->parse_event_data('pec_monthly_day')) == 'friday' ? 'selected="selected"' : '') . '> ' . parent::$translation['DAY_FRIDAY'] . '</option>
							<option value="saturday"' . ($this->parse_value($this->parse_event_data('pec_monthly_day')) == 'saturday' ? 'selected="selected"' : '') . '> ' . parent::$translation['DAY_SATURDAY'] . '</option>
							<option value="sunday" ' . ($this->parse_value($this->parse_event_data('pec_monthly_day')) == 'sunday' ? 'selected="selected"' : '') . '> ' . parent::$translation['DAY_SUNDAY'] . '</option>
						</select>
					</li>';

					$html .= 		'</ul>';
					$html .= 	'</div>';
					$html .= '</div>';

				}

				break;

			case 'booking':

				$booking_form = '';

				if( $this->is_field_enabled( 'form_show_booking_enable' ) ) 
				{

					$booking_form .= '<li>';
					$booking_form .= '
						<input type="checkbox" class="new_event_checkbox"  name="booking_enable" id="" value="1" ' . ( 1 == $this->parse_value( $this->parse_event_data('booking_enable') )  ? 'checked="checked"' : '').' />
						<span class="dp_pec_form_desc dp_pec_form_desc_left">'.parent::$translation['TXT_ALLOW_BOOKINGS'].'</span>';
					$booking_form .= '</li>';

				}
				

				if( $this->is_field_enabled( 'form_show_booking_price' ) && is_plugin_active( 'dp-pec-payments/dp-pec-payments.php' ) ) 
				{

					$booking_form .= '<li>';
					$booking_form .= '
						<input type="number" min="0" class="dp_pec_new_event_text" style="width: 120px;" placeholder="'.parent::$translation['TXT_PRICE'].'" value="'.$this->parse_value( $this->parse_event_data('price')).'" id="" name="price" /> <span class="dp_pec_form_desc dp_pec_form_desc_left">'.$dp_pec_payments['currency'].'</span>';
					$booking_form .= '</li>';

				}
				
				if( $this->is_field_enabled( 'form_show_booking_block_hours' ) ) 
				{

					$booking_form .= '<li>';
					$booking_form .= '
						<input type="number" min="0" value="'.$this->parse_value( $this->parse_event_data('block_hours')).'" class="dp_pec_new_event_text" style="width: 140px;" placeholder="'.parent::$translation['TXT_BOOKING_BLOCK_HOURS'].'" id="" name="block_hours" />';
					$booking_form .= '</li>';

				}

				if( $this->is_field_enabled( 'form_show_booking_limit' ) ) 
				{

					$booking_form .= '<li>';
					$booking_form .= '
						<input type="number" min="0" value="'.$this->parse_value( $this->parse_event_data('limit')).'" class="dp_pec_new_event_text" style="width: 140px;" placeholder="'.parent::$translation['TXT_BOOKING_LIMIT'].'" id="" name="limit" />';
					$booking_form .= '</li>';

				}

				if($booking_form != '') 
				{

					$html .= '<div class="dp_pec_event_form_options_item">';

					$html .= 	'<h4>' . $this->get_form_item_title( 'TXT_BOOKINGS' ) . '</h4>';
					$html .= 	'<span class="dp_pec_event_form_options_item_sub"></span>';

					$html .= 	'<div class="dp_pec_event_form_options_item_hidden">';

					$html .=		'<ul>';
					$html .= 			$booking_form;
					$html .=		'<ul>';

					$html .= '	</div>';

					$html .= '</div>';

				}

				break;

			case 'image':

				if( $this->check_image() ) 
				{

					$rand_image = rand();

					$img = $this->get_featured_image( 'thumbnail' );
					$featured_image = '';

					if( is_array($img) && isset( $img[0] ) )
					{

						$featured_image = '<div class="dp_pec_add_image_preview"  style="background-image: url(' . $img[0] . ');"></div>';

					}



					$html .= '
					<div class="dp_pec_add_image_wrapper '.($featured_image != "" ? 'dp_pec_add_has_image' : '').'">

						<h4>' . $this->get_form_item_title( 'TXT_EVENT_IMAGE' ) . '</h4>

						<label for="event_image_'.parent::$nonce.'_'.$rand_image.'">';

					$html .= $featured_image;

					$html .= '<i class="fas fa-camera-retro dp_pec_add_image_wrap"></i>';

					
					$html .= '
						</label>';


					$html .= '

						<input type="text" class="dp_pec_new_event_text" value="" readonly="readonly" id="event_image_lbl" name="" />

						<input type="file" name="event_image" id="event_image_'.parent::$nonce.'_'.$rand_image.'" class="event_image" style="visibility:hidden; position: absolute;" />';
					
					$html .= '
					</div>';

				}

				break;

			case 'speakers':

				if( $this->is_field_enabled( 'form_show_speakers' ) ) 
				{

					$args = array(
						'posts_per_page'   => -1,
						'post_type'        => DP_PRO_EVENT_CALENDAR_SPEAKERS_POST_TYPE,
						'post_status'      => 'publish',
						'order'			   => 'ASC', 
						'orderby' 		   => 'title' 
					);

					$speakers_list = get_posts( $args );

					if( count( $speakers_list ) > 0 ) 
					{

						$html .= '<div class="dp_pec_event_form_options_item dp_pec_event_form_options_item_speakers">
									<h4>' . $this->get_form_item_title( 'TXT_SPEAKERS' ) . '</h4>
									<span class="dp_pec_event_form_options_item_sub">' . $this->parse_value( $this->parse_event_data( 'speakers' ) ) . '</span>

									<div class="dp_pec_event_form_options_item_hidden">
										<ul>';

						foreach( $speakers_list as $speaker ) 
						{

							$html .= 	'<li>';
							$html .=		'<label>
												<input type="checkbox" ' . ( in_array( $speaker->ID, $this->parse_value( $this->parse_event_data( 'speakers_ids' ), array() ) )  ? 'checked="checked"' : '') . ' data-speaker-name="' . esc_attr( $speaker->post_title ) . '" name="speaker-' . esc_attr( $speaker->ID ) . '" class="new_event_checkbox" value="' . esc_attr( $speaker->ID ) . '" />';
							$html .= 		$speaker->post_title;
							$html .=	'	</label>
										</li>';

						}

						$html .= '		</ul>
									</div>
								</div>';

					}
				}

				break;

			case 'location':

				if( $this->is_field_enabled( 'form_show_location' ) ) 
				{

					$html .= '
					<div class="dp_pec_event_form_options_item dp_pec_event_form_options_item_location">
						<h4>' . $this->get_form_item_title( 'TXT_EVENT_LOCATION' ) . '</h4>

						<div class="dp_pec_event_form_options_item_sub">' . $this->parse_value( $this->parse_event_data('location_name')) .'</div>';

					$html .= '<div class="dp_pec_event_form_options_item_hidden">';

					$html .=	'<ul>';

					$html .=		'<li>';

					$html .=	'
						<select autocomplete="off" name="location" class="pec_location_form">
							<option value="">'.parent::$translation['TXT_NONE'].'</option>
						 ';

						$args = array(
						'posts_per_page'   => -1,
						'post_type'        => DP_PRO_EVENT_CALENDAR_VENUES_POST_TYPE,
						'post_status'      => 'publish',
						'order'			   => 'ASC', 
						'orderby' 		   => 'title' );

						$venues_list = get_posts($args);

						foreach($venues_list as $venue) 
						{

							$html .= '<option value="'.$venue->ID.'" ' . ($this->parse_value($this->parse_event_data('location')) == $venue->ID ? 'selected="selected"' : '') . '>' . esc_attr( $venue->post_title ).'</option>';
						
						}

						if( $this->is_field_enabled( 'form_show_location_options' ) ) 
						{
							$html .= '<option value="-1">'.parent::$translation['TXT_OTHER'].'</option>';
						}
						
					$html .= ' 
					</select>
					</li>';

					if( $this->is_field_enabled( 'form_show_location_options' ) ) 
					{

						$html .= '
						<li class="pec_location_options" style="display:none;">
							<ul>
								<li>
									<input type="text" class="dp_pec_new_event_text" value="" placeholder="'.parent::$translation['TXT_EVENT_LOCATION_NAME'].'" id="" name="location_name" />
								</li>';

								$html .= '
								<li>
									<input type="text" class="dp_pec_new_event_text" value="" placeholder="'.parent::$translation['TXT_EVENT_ADDRESS'].'" id="" name="location_address" />
								</li>';

								$html .= '
								<li>
									<input type="text" class="dp_pec_new_event_text" value="" placeholder="'.parent::$translation['TXT_EVENT_GOOGLEMAP'].'" id="pec_map_address" name="googlemap" />
								</li>

								<li>
									<input type="hidden" value="" id="map_lnlat" name="map_lnlat" />
									<div class="map_lnlat_wrap" style="display:none;">
										<span class="dp_pec_form_desc">'.parent::$translation['TXT_DRAG_MARKER'].'</span>
										<div id="pec_mapCanvas" style="height: 400px;"></div>
									</div>
								</li>
							</ul>
						</li>
						';
					}

					$html .= 		'</ul>';
					$html .= 	'</div>';
					$html .= '</div>';

				}

				break;

			case 'color':

				if( $this->is_field_enabled( 'form_show_color' ) ) 
				{

					$html .= '<div class="dp_pec_event_form_options_item">
					<h4>' . $this->get_form_item_title( 'TXT_SELECT_COLOR' ) . '</h4>
					<select autocomplete="off" name="color" class="pec_color_form">
						<option value="">' . parent::$translation['TXT_NONE'] . '</option>
						 ';
						 
						$counter = 0;

						$querystr = "
						SELECT *
						FROM " . DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES . " 
						ORDER BY title ASC";

						$sp_dates_obj = $wpdb->get_results($querystr, OBJECT);

						foreach( $sp_dates_obj as $sp_dates ) 
						{
						
							$html .= '<option value="'.$sp_dates->id.'" ' . ($this->parse_value($this->parse_event_data('color')) == $sp_dates->id ? 'selected="selected"' : '') . '>'.esc_attr($sp_dates->title).'</option>';
						
						}

					$html .= ' 
					</select>
					<div class="dp_pec_clear"></div>';

					$html .= '</div>';
				}	

				break;

			case 'link':

				if( $this->is_field_enabled( 'form_show_link' ) ) 
				{

					$html .= '<div class="dp_pec_event_form_options_item">
								<h4>' . $this->get_form_item_title( 'TXT_EVENT_LINK' ) . '</h4>
								<input type="url" class="dp_pec_new_event_text" value="' . $this->parse_value( $this->parse_event_data('link')) . '" placeholder="" id="" name="link" />
							  </div>';


				}

				break;

			case 'phone':

				if( $this->is_field_enabled( 'form_show_phone' ) ) 
				{

					$html .= '<div class="dp_pec_event_form_options_item">
								<h4>' . $this->get_form_item_title( 'TXT_EVENT_PHONE' ).'</h4>
								<input type="text" class="dp_pec_new_event_text" value="'.$this->parse_value( $this->parse_event_data('phone')).'" placeholder="" id="" name="phone" />
							  </div>';

				}

				break;

			case 'custom_fields':

				$cal_form_custom_fields = parent::$calendar_obj->form_custom_fields;
				$cal_form_custom_fields_arr = explode(',', $cal_form_custom_fields);

				if(is_array($dpProEventCalendar['custom_fields_counter'])) {

					$counter = 0;
					
					foreach($dpProEventCalendar['custom_fields_counter'] as $key) {
						
						if(!empty($cal_form_custom_fields) && $cal_form_custom_fields != "all" && $cal_form_custom_fields != "" && !in_array($dpProEventCalendar['custom_fields']['id'][$counter], $cal_form_custom_fields_arr)) {
							$counter++;
							continue;
						}

						$custom_field = $dpProEventCalendar['custom_fields'];

						switch($custom_field['type'][$counter])
						{

							case "checkbox":

								$html .= '<div class="dp_pec_event_form_options_item">';

								$html .= '<h4>'.$custom_field['name'][$counter].'</h4>';

								$html .= '
								<div class="dp_pec_wrap_checkbox">
								<input type="checkbox" class="new_event_checkbox '.(!$custom_field['optional'][$counter] ? 'pec_required' : '').'" value="1" '.(1 == $this->parse_value( $this->get_custom_field_value($dpProEventCalendar['custom_fields']['id'][$counter])) ? 'checked="checked"' : '' ).' id="pec_custom_'.$custom_field['id'][$counter].'" name="pec_custom_'.$custom_field['id'][$counter].'" />';

								if(!$custom_field['optional'][$counter] ) {
									$html .= '
									<div class="dp_pec_new_event_validation_msg">'.parent::$translation['TXT_FIELD_REQUIRED'].'</div>';
								}

								$html .= '

								</div>';
								
								$html .= '</div>';

								break;

							case "text":

								$html .= '<div class="dp_pec_event_form_options_item">';

								$html .= '<h4>'.$custom_field['name'][$counter].'</h4>';



								$html .= '
								<input type="text" class="dp_pec_new_event_text '.(!$custom_field['optional'][$counter] ? 'pec_required' : '').'" value="'.$this->parse_value( $this->get_custom_field_value($dpProEventCalendar['custom_fields']['id'][$counter])).'" placeholder="" id="pec_custom_'.$custom_field['id'][$counter].'" name="pec_custom_'.$custom_field['id'][$counter].'" />';

								if(!$custom_field['optional'][$counter] ) {
									$html .= '
									<div class="dp_pec_new_event_validation_msg">'.parent::$translation['TXT_FIELD_REQUIRED'].'</div>';
								}

								$html .= '</div>';

								break;

							case "multiple_checkbox":

								$multiple_checkbox_value = $this->parse_value( $this->get_custom_field_value($dpProEventCalendar['custom_fields']['id'][$counter]));

								$multiple_checkbox_txt = '';

								if(is_array($multiple_checkbox_value))
								{

									$multiple_checkbox_txt = implode(', ', $multiple_checkbox_value);

								} else {

									$multiple_checkbox_value = array();
									
								}

								$html .= '<div class="dp_pec_event_form_options_item dp_pec_event_form_options_item_custom_multiple">';

								$html .= '<h4>'.$custom_field['name'][$counter].'</h4>';
								$html .= '<span class="dp_pec_event_form_options_item_sub">'.$multiple_checkbox_txt.'</span>';

								$html .= '<div class="dp_pec_event_form_options_item_hidden">';

								$html .= '<ul class="'.(!$custom_field['optional'][$counter] ? 'pec_required' : '').'" id="pec_custom_'.$custom_field['id'][$counter].'">';

								if(is_array($custom_field['placeholder'][$counter])) 
								{

									foreach($custom_field['placeholder'][$counter] as $key => $value) 
									{
										$value = $custom_field['placeholder'][$counter][$key];

										$html .= 	'<li>';
										$html .=		'<label>
															<input type="checkbox" '.(in_array($value, $multiple_checkbox_value)  ? 'checked="checked"' : '').' name="pec_custom_'.$custom_field['id'][$counter].'[]" class="new_event_checkbox" value="'.esc_attr($value).'" />';
										$html .= 		$value;
										$html .=	'	</label>
													</li>';

									}

								}
								

								$html .= '</ul>';

								$html .= '</div>';

								if(!$custom_field['optional'][$counter] ) {
									$html .= '
									<div class="dp_pec_new_event_validation_msg">'.parent::$translation['TXT_FIELD_REQUIRED'].'</div>';
								}

								$html .= '</div>';
								
								break;

							case "dropdown":

								$html .= '<div class="dp_pec_event_form_options_item">';

								$html .= '<h4>'.$custom_field['name'][$counter].'</h4>';

								$html .= '<select autocomplete="off" class=" '.(!$custom_field['optional'][$counter] ? 'pec_required' : '').'" id="pec_custom_'.$custom_field['id'][$counter].'" name="pec_custom_'.$custom_field['id'][$counter].'">';

								if(is_array($custom_field['placeholder'][$counter])) 
								{

									foreach($custom_field['placeholder'][$counter] as $key => $value) 
									{
										$value = $custom_field['placeholder'][$counter][$key];

										$html .= '<option option="'.$value.'" '.($value == $this->parse_value( $this->get_custom_field_value($dpProEventCalendar['custom_fields']['id'][$counter])) ? 'selected="selected"' : '' ).'>'.$value.'</option>';

									}

								}
								

								$html .= '</select>';
								if(!$custom_field['optional'][$counter] ) {
									$html .= '
									<div class="dp_pec_new_event_validation_msg">'.parent::$translation['TXT_FIELD_REQUIRED'].'</div>';
								}

								$html .= '</div>';
								
								break;
							
						}
						$counter++;		
					}
				}

				break;

			case 'recaptcha':

				if($this->check_recaptcha()) 
				{
			
					$html .= '<div id="pec_new_event_captcha"></div>';

				}

				break;

		}

		return $html;

	}

	private function check_recaptcha() 
	{
		global $dpProEventCalendar;

		return (isset($dpProEventCalendar['recaptcha_enable']) && $dpProEventCalendar['recaptcha_enable'] && $dpProEventCalendar['recaptcha_site_key'] != "");


	}

	private function check_image() 
	{

		return ( $this->is_field_enabled( 'form_show_image' ) );


	}

	private function parse_value( $value, $default = '', $except = false ) 
	{

		if( $this->is_edit() || is_numeric( $this->time ) ) 
		{
			
			if( is_array( $default ) && !is_array( $value ) )
				return $default;

			if( is_array( $default ) || is_array( $value ) || $except )
				return $value;

			
			return esc_attr($value);

		}

		return $default;

	}

	private function parse_event_data ( $key )
	{

		if( isset( $this->event_data[$key] ) )
			return $this->event_data[$key];

		return '';

	}


	private function first_step() 
	{

		global $dpProEventCalendar;

		$html = '<div class="dp_pec_new_event_steps dp_pec_form_step_visible">';

		$html .= '<input type="text" class="dp_pec_new_event_text dp_pec_form_title pec_required" value="'.$this->parse_value($this->parse_event_data('title')).'" placeholder="'.parent::$translation['TXT_EVENT_ADD_A_TITLE'].'" name="title" />';

		$html .= '<div class="dp_pec_new_event_validation_msg">'.parent::$translation['TXT_FIELD_REQUIRED'].'</div>';

		$css_file = 'tinymce';
		if( self::$calendar_obj->skin == 'dark' ) 
			$css_file = 'tinymce_dark';

		if( $this->is_field_enabled( 'form_show_description' ) ) {
			if(parent::$calendar_obj->form_text_editor) {
				// Turn on the output buffer
				ob_start();
				
				// Echo the editor to the buffer
				wp_editor($this->parse_value($this->parse_event_data('description'), '', true), parent::$nonce.'_event_description', array(
					'editor_class' => 'pec-editor-test', 
					'media_buttons' => false, 
					'textarea_name' => 'description', 
					'quicktags' => false, 
					'textarea_rows' => ( isset( $dpProEventCalendar['editor_rows'] ) ? $dpProEventCalendar['editor_rows'] : 10 ), 
					'teeny' => true,
					'tinymce' => array(
				        'toolbar1' => 'bold italic underline blockquote strikethrough | bullist numlist | alignleft aligncenter alignright | undo redo | link unlink',
				        'content_css' => dpProEventCalendar_plugin_url( 'css/'.$css_file.'.css' )
				    )
				));
				
				// Store the contents of the buffer in a variable
				$editor_contents = ob_get_clean();
				
				$html .= $editor_contents;
			} else {
				$html .= '<textarea placeholder="'.parent::$translation['TXT_EVENT_ADD_DESCRIPTION'].'" class="dp_pec_new_event_text dp_pec_form_description" id="" name="description" cols="50" rows="5">'.$this->parse_value($this->parse_event_data('description')).'</textarea>';
			}
			
		}
		$html .= '</div>';
		return $html;
	}



	private function second_step() 
	{

		$html = '<div class="dp_pec_new_event_steps">';

		$html .= 	'<div class="dp_pec_event_form_grid">';

		$html .= 		'<div class="dp_pec_event_form_preview">

							<div class="dp_pec_event_form_preview_title">'.$this->parse_value($this->parse_event_data('title')).'</div>

							<div class="dp_pec_event_form_preview_description">'.$this->parse_value($this->parse_event_data('description')).'</div>

						</div>';

		$html .= 		'<div class="dp_pec_event_form_options">';


		$html .= $this->get_field('category');

		$html .= $this->get_field('date');

		$html .= $this->get_field('time');

		$html .= $this->get_field('frequency');
							
		$html .= $this->get_field('booking');	

		$html .= $this->get_field('speakers');

		$html .= $this->get_field('location');

		$html .= $this->get_field('color');

		$html .= $this->get_field('link');

		$html .= $this->get_field('phone');

		$html .= $this->get_field('custom_fields');
							

		$html .= 		'</div>
					</div>';
		$html .= '</div>';
		return $html;
	}



	private function third_step() 
	{

		$html = '<div class="dp_pec_new_event_steps">';

		$html .= $this->get_field('image');	

		$html .= '</div>';

		return $html;

	}

	private function fourth_step() 
	{

		$html = '<div class="dp_pec_new_event_steps">';

		$html .= $this->get_field('recaptcha');	

		$html .= '</div>';

		return $html;

	}



	public function footer() 
	{

		$html = '
			<div class="pec-add-footer">
				
				<div class="pec-add-footer-wrap">

					<button class="pec_form_back pec_action_btn" type="button">'.parent::$translation['TXT_BACK'].'</button>

					<button class="pec_form_next pec_action_btn" type="button">'.parent::$translation['TXT_NEXT'].'</button>

					<button class="dp_pec_submit_event" data-lang-sending="'.parent::$translation['TXT_SENDING'].'">'.(parent::$calendar_obj->publish_new_event ? parent::$translation['TXT_SUBMIT'] : parent::$translation['TXT_SUBMIT']).'</button>

				</div>

				<div class="dp_pec_clear"></div>
			</div>';

		return $html;

	}

	private function get_form_item_title ( $key )
	{

		return trim( str_replace( '(optional)', '', parent::$translation[$key] ) );

	}


	public function display_form() 
	{
		
		if( $this->check_permissions() ) {
			$html = '';
		
			$html .= '
			<div class="dp_pec_new_event_wrapper pec_skin_'.self::$calendar_obj->skin.'" id="dp_pec_id'.parent::$nonce.'">';

			if( ! $this->is_modal() )
	
				$html .= $this->header();


			$html .= '
				<div class="dp_pec_content">
					';
				if( ! is_user_logged_in() && !parent::$calendar_obj->assign_events_admin ) {

					$html .= self::$notifications->message('warning', parent::$translation['TXT_EVENT_LOGIN'], true);

				} else {

					$form_class = '';

					if($this->is_edit() ) 
					{

						$form_class = 'edit_event_form ';

						$html .= self::$notifications->message('success', parent::$translation['TXT_EVENT_THANKS_EDIT']);

					} else {

						$html .= self::$notifications->message('success', parent::$translation['TXT_EVENT_THANKS']);

					}

					$html .= '
					<form enctype="multipart/form-data" method="post" class="'.$form_class.'add_new_event_form">';

					if($this->is_edit() )
					{

						$html .= '<input type="hidden" name="edit_calendar" value="'.$this->parse_value($this->parse_event_data('id_calendar')).'" />';
						$html .= '<input type="hidden" name="edit_event" value="'.$this->edit_event.'" />';

					}

					$html .= "<div>";

					$html .= $this->first_step();

					$html .= $this->second_step();

					if($this->check_image())

						$html .= $this->third_step();

					if($this->check_recaptcha())
	
						$html .= $this->fourth_step();
					
					$html .= "</div>";

					$html .= $this->footer();

					$html .= '
					</form>';

				}
					$html .= '
				</div>
				<div class="dp_pec_clear"></div>
			</div>';

			return $html;

		}
	}
}
?>