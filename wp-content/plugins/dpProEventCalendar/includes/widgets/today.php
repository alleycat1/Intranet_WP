<?php

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/************************************************************************/
/*** WIDGET TODAY EVENTS 
/************************************************************************/

class DpProEventCalendar_TodayEventsWidget extends WP_Widget {
	
	function __construct() {
		$params = array(
			'description' => __('Display today\'s events in a list.', 'dpProEventCalendar'),
			'name' => __('DP Pro Event Calendar - Today\'s Events', 'dpProEventCalendar')
		);
		
		parent::__construct('EventsCalendarTodayEvents', __('DP Pro Event Calendar - Today\'s Events', 'dpProEventCalendar'), $params);
	}
	
	public function form($instance) 
	{
	
		global $wpdb, $table_prefix;
		$table_name_calendars = DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS;
		
		extract($instance);
		?>
        	<p>
            	<label for="<?php echo $this->get_field_id('title');?>"><?php _e('Title', 'dpProEventCalendar')?>: </label>
                <input class="widefat" id="<?php echo $this->get_field_id('title');?>" name="<?php echo $this->get_field_name('title');?>" value="<?php if(isset($title)) echo esc_attr($title); ?>" />
            </p>
            
            <p>
            	<label for="<?php echo $this->get_field_id('description');?>"><?php _e('Description', 'dpProEventCalendar')?>: </label>
                <textarea class="widefat" rows="5" id="<?php echo $this->get_field_id('description');?>" name="<?php echo $this->get_field_name('description');?>"><?php if(isset($description)) echo esc_attr($description); ?></textarea>
            </p>
            
            <p>
            	<label for="<?php echo $this->get_field_id('calendar');?>"><?php _e('Calendar', 'dpProEventCalendar')?>: </label>
            	<select name="<?php echo $this->get_field_name('calendar');?>" id="<?php echo $this->get_field_id('calendar');?>">
                    <?php
                    $querystr = "
                    SELECT *
                    FROM $table_name_calendars
                    ORDER BY title ASC
                    ";
                    $calendars_obj = $wpdb->get_results($querystr, OBJECT);
                    foreach($calendars_obj as $calendar_key) {
                    ?>
                        <option value="<?php echo $calendar_key->id?>" <?php if($calendar == $calendar_key->id) {?> selected="selected" <?php } ?>><?php echo $calendar_key->title?></option>
                    <?php }?>
                </select>
            </p>
        <?php
	}
	
	public function widget($args, $instance) 
	{
		
		extract($args);
		extract($instance);
		
		$title = apply_filters('widget_title', $title);
		$description = apply_filters('widget_description', $description);
		
		//if(empty($title)) $title = 'DP Pro Event Calendar - Upcoming Events';
		
		echo $before_widget;
			if(!empty($title))
				echo $before_title . $title . $after_title;
			echo '<p>'. $description. '</p>';
			echo do_shortcode('[dpProEventCalendar id='.$calendar.' type="today-events" widget=1]');
		echo $after_widget;
		
	}
}

add_action('widgets_init', 'dpProEventCalendar_register_todayeventswidget');
function dpProEventCalendar_register_todayeventswidget() 
{

	register_widget('DpProEventCalendar_TodayEventsWidget');

}
?>