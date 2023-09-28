<?php

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/************************************************************************/
/*** WIDGET UPCOMING EVENTS 
/************************************************************************/

class DpProEventCalendar_UpcomingEventsWidget extends WP_Widget {
	function __construct() {
		$params = array(
			'description' => __('Display the upcoming events of a calendar.', 'dpProEventCalendar'),
			'name' => __('DP Pro Event Calendar - Upcoming Events', 'dpProEventCalendar')
		);
		
		parent::__construct('EventsCalendarUpcomingEvents', __('DP Pro Event Calendar - Upcoming Events', 'dpProEventCalendar'), $params);
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
            	<label for="<?php echo $this->get_field_id('description');?>"><?php _e('Description', 'dpProEventCalendar')?>n: </label>
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
            	<label for="<?php echo $this->get_field_id('layout');?>"><?php _e('Layout')?>: </label>
            	<select name="<?php echo $this->get_field_name('layout');?>" id="<?php echo $this->get_field_id('layout');?>" onchange="pec_get_skin_accordion_<?php echo str_replace('-', '', $this->get_field_id('layout'));?>(this.value);">
                	<option value=""><?php _e('Default')?></option>
                    <option value="accordion-upcoming" <?php if($layout == 'accordion-upcoming') {?> selected="selected" <?php } ?>><?php _e('Accordion', 'dpProEventCalendar')?></option>
                    <option value="gmap-upcoming" <?php if($layout == 'gmap-upcoming') {?> selected="selected" <?php } ?>><?php _e('Google Map', 'dpProEventCalendar')?></option>
                    <option value="grid-upcoming" <?php if($layout == 'grid-upcoming') {?> selected="selected" <?php } ?>><?php _e('Grid', 'dpProEventCalendar')?></option>
                    <option value="book-btn" <?php if($layout == 'book-btn') {?> selected="selected" <?php } ?>><?php _e('Booking Button', 'dpProEventCalendar')?></option>
                    <option value="card" <?php if($layout == 'card') {?> selected="selected" <?php } ?>><?php _e('Card', 'dpProEventCalendar')?></option>
                    <option value="countdown" <?php if($layout == 'countdown') {?> selected="selected" <?php } ?>><?php _e('Countdown', 'dpProEventCalendar')?></option>
                    <option value="compact-upcoming" <?php if($layout == 'compact-upcoming') {?> selected="selected" <?php } ?>><?php _e('Compact', 'dpProEventCalendar')?></option>
                    <option value="list-upcoming" <?php if($layout == 'list-upcoming') {?> selected="selected" <?php } ?>><?php _e('List', 'dpProEventCalendar')?></option>
                </select>
            </p>
            <p>
            	<label for="<?php echo $this->get_field_id('category');?>"><?php _e('Category')?>: </label>
            	<select name="<?php echo $this->get_field_name('category');?>" id="<?php echo $this->get_field_id('category');?>">
                	<option value=""><?php _e('All')?></option>
                    <?php
                    $categories=  get_categories(array('taxonomy' => 'pec_events_category', 'hide_empty' => 0)); 
					foreach ($categories as $cat) {
                    ?>
                        <option value="<?php echo $cat->term_id?>" <?php if($category == $cat->term_id) {?> selected="selected" <?php } ?>><?php echo $cat->name?></option>
                    <?php }?>
                </select>
            </p>
            
            <p id="list-<?php echo $this->get_field_id('skin');?>">
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
            
            <p id="pec_upcoming_events_count_<?php echo $this->get_field_id('events_count');?>">
            	<label for="<?php echo $this->get_field_id('events_count');?>"><?php _e('Max Number of Events to Display', 'dpProEventCalendar')?>: </label>
                <input type="number" class="widefat" style="width:40px;" min="1" max="10" id="<?php echo $this->get_field_id('events_count');?>" name="<?php echo $this->get_field_name('events_count');?>" value="<?php echo !empty($events_count) ? $events_count : 5; ?>" />
            </p>

            <p id="pec_upcoming_pagination_<?php echo $this->get_field_id('pagination');?>">
            	<label for="<?php echo $this->get_field_id('pagination');?>"><?php _e('Pagination', 'dpProEventCalendar')?>: </label>
                <input type="number" class="widefat" style="width:60px;" min="1" max="50" id="<?php echo $this->get_field_id('pagination');?>" name="<?php echo $this->get_field_name('pagination');?>" value="<?php echo !empty($pagination) ? $pagination : ''; ?>" />
            </p>

            <p id="pec_upcoming_columns_<?php echo $this->get_field_id('columns');?>">
            	<label for="<?php echo $this->get_field_id('columns');?>"><?php _e('Columns', 'dpProEventCalendar')?>: </label>
                <select name="<?php echo $this->get_field_name('columns');?>" id="<?php echo $this->get_field_id('columns');?>">
                    <option value="1"><?php _e('1 Column','dpProEventCalendar'); ?></option>
                    <option value="2" <?php if($columns == 2) {?>selected="selected"<?php }?>><?php _e('2 Columns','dpProEventCalendar'); ?></option>
                    <option value="3" <?php if($columns == 3) {?>selected="selected"<?php }?>><?php _e('3 Columns','dpProEventCalendar'); ?></option>
                    <option value="4" <?php if($columns == 4) {?>selected="selected"<?php }?>><?php _e('4 Columns','dpProEventCalendar'); ?></option>
                </select>
            </p>
            <p id="pec_upcoming_limit_description_<?php echo $this->get_field_id('limit_description');?>">
            	<label for="<?php echo $this->get_field_id('limit_description');?>"><?php _e('Limit Description', 'dpProEventCalendar')?>: </label>
                <input type="number" min="0" max="500" id="<?php echo $this->get_field_id('limit_description');?>" name="<?php echo $this->get_field_name('limit_description');?>" value="<?php if(isset($limit_description)) echo esc_attr($limit_description); ?>" />&nbsp;words
            </p>
            
            <script type="text/javascript">
			function pec_get_skin_accordion_<?php echo str_replace('-', '', $this->get_field_id('layout'));?>(val) {
				jQuery('#list-<?php echo $this->get_field_id('skin');?>').hide(); 
				jQuery('#pec_upcoming_columns_<?php echo $this->get_field_id('columns');?>').show(); 
				jQuery('#pec_upcoming_pagination_<?php echo $this->get_field_id('pagination');?>').show(); 
				jQuery('#pec_upcoming_events_count_<?php echo $this->get_field_id('events_count');?>').show(); 
				jQuery('#pec_upcoming_limit_description_<?php echo $this->get_field_id('limit_description');?>').show(); 
				
				if(val == 'accordion-upcoming' || val == 'compact-upcoming') { 
				
					jQuery('#list-<?php echo $this->get_field_id('skin');?>').show(); 
				
				} 	
				
				if(val == 'gmap-upcoming' || val == 'compact-upcoming' || val == 'card' || val == 'book-btn') {
					jQuery('#pec_upcoming_columns_<?php echo $this->get_field_id('columns');?>').hide();
					jQuery('#pec_upcoming_pagination_<?php echo $this->get_field_id('pagination');?>').hide();
				}

				if(val == 'card' || val == 'book-btn') {
					jQuery('#pec_upcoming_events_count_<?php echo $this->get_field_id('events_count');?>').hide();
					jQuery('#pec_upcoming_limit_description_<?php echo $this->get_field_id('limit_description');?>').hide();
				}
				
				if(val == 'grid-upcoming') {
					jQuery('#pec_upcoming_pagination_<?php echo $this->get_field_id('pagination');?>').hide();
				}
			}

			<?php if($layout != "") {?>
				pec_get_skin_accordion_<?php echo str_replace('-', '', $this->get_field_id('layout'));?>("<?php echo $layout?>");
			<?php }?>

			</script>
        <?php
	}
	
	public function widget($args, $instance) {
		
		extract($args);
		extract($instance);
		
		$title = apply_filters('widget_title', $title);
		$description = apply_filters('widget_description', $description);
		$type = 'upcoming';
		
		//if(empty($title)) $title = 'DP Pro Event Calendar - Upcoming Events';
		if(!is_numeric($events_count)) { $events_count = 5; }
		
		if($layout != "") {
			$type = $layout;
		}
		
		echo $before_widget;
			if(!empty($title))
				echo $before_title . $title . $after_title;
			echo '<p>'. $description. '</p>';
			echo do_shortcode('[dpProEventCalendar id='.$calendar.' widget=1 type="'.$type.'" category="'.$category.'" limit="'.$events_count.'" limit_description="'.$limit_description.'" columns="'.$columns.'" skin="'.$skin.'" pagination="'.$pagination.'"]');
		echo $after_widget;
		
	}
}

add_action('widgets_init', 'dpProEventCalendar_register_upcomingeventswidget');
function dpProEventCalendar_register_upcomingeventswidget() 
{

	register_widget('DpProEventCalendar_UpcomingEventsWidget');

}

?>