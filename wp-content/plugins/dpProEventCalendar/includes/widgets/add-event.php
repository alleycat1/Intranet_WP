<?php

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/************************************************************************/
/*** WIDGET ADD EVENTS 
/************************************************************************/

class DpProEventCalendar_AddEventsWidget extends WP_Widget {
	function __construct() {
		$params = array(
			'description' => __('Allow logged in users to submit events.', 'dpProEventCalendar'),
			'name' => __('DP Pro Event Calendar - Add Events', 'dpProEventCalendar')
		);
		
		parent::__construct('EventsCalendarAddEvents', __('DP Pro Event Calendar - Add Events', 'dpProEventCalendar'), $params);
	}
	
	public function form($instance) {
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
            
            <p>
            	<label for="<?php echo $this->get_field_id('skin');?>"><?php _e('Skin', 'dpProEventCalendar')?>: </label>
            	<select name="<?php echo $this->get_field_name('skin');?>" id="<?php echo $this->get_field_id('skin');?>">
                    <option value=""><?php _e('None','dpProEventCalendar'); ?></option>
                    <option value="dark" <?php if($skin == 'dark') {?> selected="selected" <?php } ?>><?php _e('Dark','dpProEventCalendar'); ?></option>
                    <option value="red" <?php if($skin == 'red') {?> selected="selected" <?php } ?>><?php _e('Red','dpProEventCalendar'); ?></option>
                    <option value="pink" <?php if($skin == 'pink') {?> selected="selected" <?php } ?>><?php _e('Pink','dpProEventCalendar'); ?></option>
                    <option value="purple" <?php if($skin == 'purple') {?> selected="selected" <?php } ?>><?php _e('Purple','dpProEventCalendar'); ?></option>
                    <option value="deep_purple" <?php if($skin == 'deep_purple') {?> selected="selected" <?php } ?>><?php _e('Deep Purple','dpProEventCalendar'); ?></option>
                    <option value="indigo" <?php if($skin == 'indigo') {?> selected="selected" <?php } ?>><?php _e('Indigo','dpProEventCalendar'); ?></option>
                    <option value="blue" <?php if($skin == 'blue') {?> selected="selected" <?php } ?>><?php _e('Blue','dpProEventCalendar'); ?></option>
                    <option value="light_blue" <?php if($skin == 'light_blue') {?> selected="selected" <?php } ?>><?php _e('Light Blue','dpProEventCalendar'); ?></option>
                    <option value="cyan" <?php if($skin == 'cyan') {?> selected="selected" <?php } ?>><?php _e('Cyan','dpProEventCalendar'); ?></option>
                    <option value="teal" <?php if($skin == 'teal') {?> selected="selected" <?php } ?>><?php _e('Teal','dpProEventCalendar'); ?></option>
                    <option value="green" <?php if($skin == 'green') {?> selected="selected" <?php } ?>><?php _e('Green','dpProEventCalendar'); ?></option>
                    <option value="light_green" <?php if($skin == 'light_green') {?> selected="selected" <?php } ?>><?php _e('Light Green','dpProEventCalendar'); ?></option>
                    <option value="lime" <?php if($skin == 'lime') {?> selected="selected" <?php } ?>><?php _e('Lime','dpProEventCalendar'); ?></option>
                    <option value="yellow" <?php if($skin == 'yellow') {?> selected="selected" <?php } ?>><?php _e('Yellow','dpProEventCalendar'); ?></option>
                    <option value="amber" <?php if($skin == 'amber') {?> selected="selected" <?php } ?>><?php _e('Amber','dpProEventCalendar'); ?></option>
                    <option value="orange" <?php if($skin == 'orange') {?> selected="selected" <?php } ?>><?php _e('Orange','dpProEventCalendar'); ?></option>
                    <option value="deep_orange" <?php if($skin == 'deep_orange') {?> selected="selected" <?php } ?>><?php _e('Deep Orange','dpProEventCalendar'); ?></option>
                    <option value="brown" <?php if($skin == 'brown') {?> selected="selected" <?php } ?>><?php _e('Brown','dpProEventCalendar'); ?></option>
                    <option value="grey" <?php if($skin == 'grey') {?> selected="selected" <?php } ?>><?php _e('Grey','dpProEventCalendar'); ?></option>
                    <option value="blue_grey" <?php if($skin == 'blue_grey') {?> selected="selected" <?php } ?>><?php _e('Blue Grey','dpProEventCalendar'); ?></option>
                </select>
            </p>
        <?php
	}
	
	public function widget($args, $instance) {
		
		extract($args);
		extract($instance);
		
		$title = apply_filters('widget_title', $title);
		$description = apply_filters('widget_description', $description);
		
		//if(empty($title)) $title = 'DP Pro Event Calendar - Upcoming Events';
		
		echo $before_widget;
			if(!empty($title))
				echo $before_title . $title . $after_title;
			echo '<p>'. $description. '</p>';
			echo do_shortcode('[dpProEventCalendar id='.$calendar.' type="add-event" category="'.$category.'" skin="'.$skin.'"]');
		echo $after_widget;
		
	}
}

add_action('widgets_init', 'dpProEventCalendar_register_addeventswidget');
function dpProEventCalendar_register_addeventswidget() 
{

	register_widget('DpProEventCalendar_AddEventsWidget');

}
?>