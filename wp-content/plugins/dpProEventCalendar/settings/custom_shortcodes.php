<?php

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// This function displays the admin page content
function dpProEventCalendar_custom_shortcodes_page() 
{

    global $wpdb, $pec_admin;
    ?>

    <div class="wrap" style="clear:both;" id="dp_options">
    
    <h2></h2>
    <div style="clear:both;"></div>
     <!--end of poststuff --> 
        <div id="dp_ui_content">
            
            <?php $pec_admin->template_left() ?>
            
            <div id="rightSide">
                <div id="menu_general_settings">
                    <div class="titleArea">
                        <div class="wrapper">
                            <div class="pec_pageTitle">
                                <h2><?php _e('Custom Shortcodes','dpProEventCalendar'); ?></h2>
                                
                                <div class="pec_pageSubtitle">
                                    <span><?php _e('Get a calendar custom shortcode.','dpProEventCalendar'); ?></span>
                                </div>
                            </div>
                            
                            <div class="clear"></div>
                        </div>
                    </div>
                    
                    <div class="wrapper">
                        <div class="option option-select">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Calendar','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <select name="pec_custom_shortcode_calendar" id="pec_custom_shortcode_calendar" onchange="pec_updateShortcode();">
                                            <?php
                                            $querystr = "
                                            SELECT *
                                            FROM " . DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS . "
                                            ORDER BY title ASC
                                            ";
                                            $calendars_obj = $wpdb->get_results($querystr, OBJECT);
                                            foreach($calendars_obj as $calendar_key) {
                                            ?>
                                                <option value="<?php echo $calendar_key->id?>"><?php echo $calendar_key->title?></option>
                                            <?php }?>
                                        </select>
                                        <br>
                                    </div>
                                    <div class="desc"><?php _e('Select a calendar','dpProEventCalendar'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        
                        <div class="option option-select">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Layout','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <select name="pec_custom_shortcode_layout" id="pec_custom_shortcode_layout" onchange="pec_updateShortcode();">
                                            <?php foreach($pec_admin->layouts as $key) {?>

                                                <option value="<?php echo $key['value']; ?>"><?php echo $key['title']; ?></option>

                                            <?php }?>
                                        </select>
                                        <br>
                                    </div>
                                    <div class="desc"><?php _e('Select a layout type.','dpProEventCalendar'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        
                        <div class="option option-select" id="list-category">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Category','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <select name="pec_custom_shortcode_category" id="pec_custom_shortcode_category" onchange="pec_updateShortcode();">
                                            <option value=""><?php _e('All Categories...','dpProEventCalendar'); ?></option>
                                            <?php 
                                             $categories = get_categories(array('taxonomy' => 'pec_events_category', 'hide_empty' => 0)); 
                                              foreach ($categories as $category) {

                                                $option = '<option value="'.$category->term_id.'">';
                                                $option .= $category->cat_name;
                                                $option .= '</option>';
                                                echo $option;
                                              }
?>
                                        </select>
                                        <br>
                                    </div>
                                    <div class="desc"><?php _e('Select a category.','dpProEventCalendar'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        
                        <div class="option option-select" id="list-events" style="display:none;">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Event','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <select name="pec_custom_shortcode_event" id="pec_custom_shortcode_event" onchange="pec_updateShortcode();">
                                            <option value=""><?php _e('All Events...','dpProEventCalendar'); ?></option>
                                            <?php 
                                             $events = get_posts(array('post_type' => DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE, 'posts_per_page' => -1)); 
                                              foreach ($events as $event) {

                                                $option = '<option value="'.$event->ID.'">';
                                                $option .= $event->post_title;
                                                $option .= '</option>';
                                                echo $option;
                                              }
?>
                                        </select>
                                        <br>
                                    </div>
                                    <div class="desc"><?php _e('Select an event (optional).','dpProEventCalendar'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>

                        <div class="option option-select" id="include-all-events">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Events from all calendars','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <select name="pec_custom_shortcode_include_all_events" id="pec_custom_shortcode_include_all_events" onchange="pec_updateShortcode();">
                                            <option value=""><?php _e('No','dpProEventCalendar'); ?></option>
                                            <option value="1"><?php _e('Yes','dpProEventCalendar'); ?></option>
?>
                                        </select>
                                        <br>
                                    </div>
                                    <div class="desc"><?php _e('Display the events from all the calendars and unassigned events.','dpProEventCalendar'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>

                        <div class="option option-select" id="loop">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Loop?','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <select name="pec_custom_shortcode_loop" id="pec_custom_shortcode_loop" onchange="pec_updateShortcode();">
                                            <option value=""><?php _e('No','dpProEventCalendar'); ?></option>
                                            <option value="1"><?php _e('Yes','dpProEventCalendar'); ?></option>
?>
                                        </select>
                                        <br>
                                    </div>
                                    <div class="desc"></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        
                        <div class="option option-select" id="list-view">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('View','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <select name="pec_custom_shortcode_view" id="pec_custom_shortcode_view" onchange="pec_updateShortcode();">
                                            <option value=""><?php _e('Default','dpProEventCalendar'); ?></option>
                                            <option value="monthly"><?php _e('Calendar','dpProEventCalendar'); ?></option>
                                            <option value="monthly-all-events"><?php _e('Monthly Events List'); ?></option>
                                            <option value="weekly"><?php _e('Weekly','dpProEventCalendar'); ?></option>
                                            <option value="daily"><?php _e('Daily','dpProEventCalendar'); ?></option>
                                        </select>
                                        <br>
                                    </div>
                                    <div class="desc"><?php _e('Select the view.','dpProEventCalendar'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        
                        <div class="option option-select" id="list-authors" style="display:none;">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Authors','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <select name="pec_custom_shortcode_authors" id="pec_custom_shortcode_authors" onchange="pec_updateShortcode();">
                                            <option value="current"><?php _e('Current logged in user','dpProEventCalendar'); ?></option>
                                            <?php 
                                            $blogusers = get_users('who=authors');
                                            foreach ($blogusers as $user) {
                                                echo '<option value="'.$user->ID.'">' . $user->display_name . ' ('.$user->user_nicename.')</option>';
                                            }?>
                                        </select>
                                        <br>
                                    </div>
                                    <div class="desc"><?php _e('Select an author.','dpProEventCalendar'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        
                        <div class="option option-select" id="list-columns" style="display:none;">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Columns','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <select name="pec_custom_shortcode_columns" id="pec_custom_shortcode_columns" onchange="pec_updateShortcode();">
                                            <option value="1"><?php _e('1 Column','dpProEventCalendar'); ?></option>
                                            <option value="2"><?php _e('2 Columns','dpProEventCalendar'); ?></option>
                                            <option value="3"><?php _e('3 Columns','dpProEventCalendar'); ?></option>
                                            <option value="4"><?php _e('4 Columns','dpProEventCalendar'); ?></option>
                                        </select>
                                        <br>
                                    </div>
                                    <div class="desc"><?php _e('Select the number of columns.','dpProEventCalendar'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>

                        <div class="option option-select" id="list-gap" style="display:none;">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Gap','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <select name="pec_custom_shortcode_gap" id="pec_custom_shortcode_gap" onchange="pec_updateShortcode();">
                                            <option value="0"><?php _e('None','dpProEventCalendar'); ?></option>
                                            <option value="5"><?php _e('5px','dpProEventCalendar'); ?></option>
                                            <option value="10"><?php _e('10px','dpProEventCalendar'); ?></option>
                                            <option value="15"><?php _e('15px','dpProEventCalendar'); ?></option>
                                            <option value="20"><?php _e('20px','dpProEventCalendar'); ?></option>
                                        </select>
                                        <br>
                                    </div>
                                    <div class="desc"><?php _e('Select the gap.','dpProEventCalendar'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        
                        <div class="option option-select" id="list-skin">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Skin color','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <select name="pec_custom_shortcode_skin" id="pec_custom_shortcode_skin" onchange="pec_updateShortcode();">
                                            <option value=""><?php _e('None','dpProEventCalendar'); ?></option>
                                            <option value="dark"><?php _e('Dark','dpProEventCalendar'); ?></option>
                                            <option value="red"><?php _e('Red','dpProEventCalendar'); ?></option>
                                            <option value="pink"><?php _e('Pink','dpProEventCalendar'); ?></option>
                                            <option value="purple"><?php _e('Purple','dpProEventCalendar'); ?></option>
                                            <option value="deep_purple"><?php _e('Deep Purple','dpProEventCalendar'); ?></option>
                                            <option value="indigo"><?php _e('Indigo','dpProEventCalendar'); ?></option>
                                            <option value="blue"><?php _e('Blue','dpProEventCalendar'); ?></option>
                                            <option value="light_blue"><?php _e('Light Blue','dpProEventCalendar'); ?></option>
                                            <option value="cyan"><?php _e('Cyan','dpProEventCalendar'); ?></option>
                                            <option value="teal"><?php _e('Teal','dpProEventCalendar'); ?></option>
                                            <option value="green"><?php _e('Green','dpProEventCalendar'); ?></option>
                                            <option value="light_green"><?php _e('Light Green','dpProEventCalendar'); ?></option>
                                            <option value="lime"><?php _e('Lime','dpProEventCalendar'); ?></option>
                                            <option value="yellow"><?php _e('Yellow','dpProEventCalendar'); ?></option>
                                            <option value="amber"><?php _e('Amber','dpProEventCalendar'); ?></option>
                                            <option value="orange"><?php _e('Orange','dpProEventCalendar'); ?></option>
                                            <option value="deep_orange"><?php _e('Deep Orange','dpProEventCalendar'); ?></option>
                                            <option value="brown"><?php _e('Brown','dpProEventCalendar'); ?></option>
                                            <option value="grey"><?php _e('Grey','dpProEventCalendar'); ?></option>
                                            <option value="blue_grey"><?php _e('Blue Grey','dpProEventCalendar'); ?></option>
                                        </select>
                                        <br>
                                    </div>
                                    <div class="desc"><?php _e('Select the skin color for this layout.','dpProEventCalendar'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>

                        <div class="option option-select" id="daily-group" style="display:none;">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Group','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <select name="pec_custom_shortcode_daily_group" id="pec_custom_shortcode_daily_group" onchange="pec_updateShortcode();">
                                            <option value=""><?php _e('No','dpProEventCalendar'); ?></option>
                                            <option value="1"><?php _e('Yes','dpProEventCalendar'); ?></option>
?>
                                        </select>
                                    </div>
                                    <div class="desc"><?php _e('Group daily events.','dpProEventCalendar'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>

                        <div class="option option-select" id="scope" style="display:none;">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Scope','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        <select name="pec_custom_shortcode_scope" id="pec_custom_shortcode_scope" onchange="pec_updateShortcode();">
                                            <option value=""><?php _e('Default','dpProEventCalendar'); ?></option>
                                            <option value="past"><?php _e('Past Events','dpProEventCalendar'); ?></option>
?>
                                        </select>
                                    </div>
                                    <div class="desc"><?php _e('Change the scope for this layout.','dpProEventCalendar'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        
                        <div class="option option-select" id="limit-param" style="display:none;">
                            <div class="option-inner">
                                <label class="titledesc"><?php _e('Limit','dpProEventCalendar'); ?></label>
                                <div class="formcontainer">
                                    <div class="forminp">
                                        
                                        <input type="number" min="1" max="99" name="pec_custom_shortcode_limit" id="pec_custom_shortcode_limit" value="5" onchange="pec_updateShortcode();" />
                                        <br>
                                    </div>
                                    <div class="desc"><?php _e('Select a limit of posts to display.','dpProEventCalendar'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        
                        <div class="submit">
                        
                            <span class="pec_custom_shortcode"></span> 

                            <div class="clear"></div>
                            
                            <p class="pec_custom_shortcode_help"></p>
                            
                            <div id="pec_custom_shortcode_preview"></div>
                        </div>
                    </div>
                </div>           
            </div>
        </div>
                    
</div> <!--end of float wrap -->

<script type="text/javascript">
    function pec_updateShortcode() {
        var shortcode = '[dpProEventCalendar';
        
        jQuery('#list-authors').hide();
        jQuery('#list-category').hide();
        jQuery('#list-view').hide();
        jQuery('#list-events').hide();
        jQuery('#include-all-events').hide();
        jQuery('#loop').hide();
        jQuery('#list-columns').hide();
        jQuery('#list-gap').hide();
        jQuery('#list-skin').hide();
        jQuery('#limit-param').hide();
        jQuery('#daily-group').hide();
        jQuery('#scope').hide();
        
        if(jQuery('#pec_custom_shortcode_calendar').val() != "") {
            shortcode += ' id="'+jQuery('#pec_custom_shortcode_calendar').val()+'"';
        }
        
        if(jQuery('#pec_custom_shortcode_layout').val() != "" && jQuery('#pec_custom_shortcode_layout').val() != "calendar-author") {
            shortcode += ' type="'+jQuery('#pec_custom_shortcode_layout').val()+'"';
        }
        
        if(jQuery('#pec_custom_shortcode_layout').val() == "" 
            || jQuery('#pec_custom_shortcode_layout').val() == "calendar-author"
            || jQuery('#pec_custom_shortcode_layout').val() == "countdown"
            || jQuery('#pec_custom_shortcode_layout').val() == "timeline"
            || jQuery('#pec_custom_shortcode_layout').val() == "compact"
            || jQuery('#pec_custom_shortcode_layout').val() == "slider"
            || jQuery('#pec_custom_shortcode_layout').val() == "slider-2"
            || jQuery('#pec_custom_shortcode_layout').val() == "slider-3"
            || jQuery('#pec_custom_shortcode_layout').val() == "carousel"
            || jQuery('#pec_custom_shortcode_layout').val() == "carousel-2"
            || jQuery('#pec_custom_shortcode_layout').val() == "carousel-3"
            || jQuery('#pec_custom_shortcode_layout').val() == "yearly"
            || jQuery('#pec_custom_shortcode_layout').val() == "modern"
            || jQuery('#pec_custom_shortcode_layout').val() == "grid-upcoming") {
            jQuery('#list-category').show();
            jQuery('#include-all-events').show();
            
            if(jQuery('#pec_custom_shortcode_category').val() != "") {
                shortcode += ' category="'+jQuery('#pec_custom_shortcode_category').val()+'"';
            }
            
            if(jQuery('#pec_custom_shortcode_include_all_events').val() != "") {
                shortcode += ' include_all_events="1"';
            }

        }

        if(jQuery('#pec_custom_shortcode_layout').val() == "grid-upcoming"
            || jQuery('#pec_custom_shortcode_layout').val() == "carousel"
            || jQuery('#pec_custom_shortcode_layout').val() == "carousel-2"
            || jQuery('#pec_custom_shortcode_layout').val() == "carousel-3") 
        {

            jQuery('#list-gap').show();

            if(jQuery('#pec_custom_shortcode_gap').val() != "0") {
                shortcode += ' gap="'+jQuery('#pec_custom_shortcode_gap').val()+'"';
            }

        }

        if(jQuery('#pec_custom_shortcode_layout').val() == "slider"
            || jQuery('#pec_custom_shortcode_layout').val() == "slider-2"
            || jQuery('#pec_custom_shortcode_layout').val() == "slider-3") {
            jQuery('#loop').show();

            if(jQuery('#pec_custom_shortcode_loop').val() != "") {
                shortcode += ' loop="1"';
            }
        }

        if(jQuery('#pec_custom_shortcode_layout').val() == "cover"
            || jQuery('#pec_custom_shortcode_layout').val() == "countdown"
            || jQuery('#pec_custom_shortcode_layout').val() == "book-btn" ) {
            jQuery('#list-events').show();
            
            if(jQuery('#pec_custom_shortcode_event').val() != "") {
                
                shortcode += ' event_id="'+jQuery('#pec_custom_shortcode_event').val()+'"';
                
            }
        }

        if(jQuery('#pec_custom_shortcode_layout').val() == "accordion-upcoming" || jQuery('#pec_custom_shortcode_layout').val() == "accordion") {
            jQuery('#list-category').show();
            jQuery('#include-all-events').show();
            
            if(jQuery('#pec_custom_shortcode_category').val() != "") {
                shortcode += ' category="'+jQuery('#pec_custom_shortcode_category').val()+'"';
            }

            if(jQuery('#pec_custom_shortcode_include_all_events').val() != "") {
                shortcode += ' include_all_events="1"';
            }

        }
        
        if(jQuery('#pec_custom_shortcode_layout').val() == "list-author" || jQuery('#pec_custom_shortcode_layout').val() == "calendar-author") {
            jQuery('#list-authors').show();
            shortcode += ' author="'+jQuery('#pec_custom_shortcode_authors').val()+'"';
            //jQuery('.pec_custom_shortcode_help').text('<?php echo __( 'This shortcode should be implemented inside the author template of your theme.', 'dpProEventCalendar' )?>');
        } else {
            jQuery('.pec_custom_shortcode_help').text('');
        }
        
        if(jQuery('#pec_custom_shortcode_layout').val() == "upcoming" 
            || jQuery('#pec_custom_shortcode_layout').val() == "accordion-upcoming" 
            || jQuery('#pec_custom_shortcode_layout').val() == "grid-upcoming" 
            || jQuery('#pec_custom_shortcode_layout').val() == "compact-upcoming" 
            || jQuery('#pec_custom_shortcode_layout').val() == "bookings-user"
            || jQuery('#pec_custom_shortcode_layout').val() == "past"
            || jQuery('#pec_custom_shortcode_layout').val() == "slider"
            || jQuery('#pec_custom_shortcode_layout').val() == "slider-2"
            || jQuery('#pec_custom_shortcode_layout').val() == "slider-3"
            || jQuery('#pec_custom_shortcode_layout').val() == "carousel"
            || jQuery('#pec_custom_shortcode_layout').val() == "carousel-2"
            || jQuery('#pec_custom_shortcode_layout').val() == "carousel-3"
            || jQuery('#pec_custom_shortcode_layout').val() == "countdown"
            || jQuery('#pec_custom_shortcode_layout').val() == "timeline"
            || jQuery('#pec_custom_shortcode_layout').val() == "list-upcoming") {
            jQuery('#limit-param').show();
            shortcode += ' limit="'+jQuery('#pec_custom_shortcode_limit').val()+'"';
        }
        
        if(jQuery('#pec_custom_shortcode_layout').val() == "") {
            jQuery('#list-view').show();
            if(jQuery('#pec_custom_shortcode_view').val() != "") {
                shortcode += ' view="'+jQuery('#pec_custom_shortcode_view').val()+'"';
            }
        }
        
        if(jQuery('#pec_custom_shortcode_layout').val() == "countdown" || jQuery('#pec_custom_shortcode_layout').val() == "list-upcoming") {
            jQuery('#daily-group').show();
            if(jQuery('#pec_custom_shortcode_daily_group').val() != "") {
                shortcode += ' group="'+jQuery('#pec_custom_shortcode_daily_group').val()+'"';
            }
        }
        
        if(jQuery('#pec_custom_shortcode_layout').val() == "grid-upcoming" || jQuery('#pec_custom_shortcode_layout').val() == "card" || jQuery('#pec_custom_shortcode_layout').val() == "list-upcoming" || jQuery('#pec_custom_shortcode_layout').val() == "compact-upcoming") {
            jQuery('#scope').show();
            if(jQuery('#pec_custom_shortcode_scope').val() != "") {
                shortcode += ' scope="'+jQuery('#pec_custom_shortcode_scope').val()+'"';
            }
        }

        
        if(jQuery('#pec_custom_shortcode_layout').val() == "grid-upcoming" 
            || jQuery('#pec_custom_shortcode_layout').val() == "accordion-upcoming" 
            || jQuery('#pec_custom_shortcode_layout').val() == "upcoming" 
            || jQuery('#pec_custom_shortcode_layout').val() == "carousel" 
            || jQuery('#pec_custom_shortcode_layout').val() == "carousel-2" 
            || jQuery('#pec_custom_shortcode_layout').val() == "carousel-3" 
            || jQuery('#pec_custom_shortcode_layout').val() == "past") {
            jQuery('#list-columns').show();
            shortcode += ' columns="'+jQuery('#pec_custom_shortcode_columns').val()+'"';
        }
        
        if(jQuery('#pec_custom_shortcode_layout').val() == "accordion-upcoming" || jQuery('#pec_custom_shortcode_layout').val() == "compact-upcoming" || jQuery('#pec_custom_shortcode_layout').val() == "accordion" || jQuery('#pec_custom_shortcode_layout').val() == "add-event" || jQuery('#pec_custom_shortcode_layout').val() == "") {
            jQuery('#list-skin').show();
            if(jQuery('#pec_custom_shortcode_skin').val() != "") {
                shortcode += ' skin="'+jQuery('#pec_custom_shortcode_skin').val()+'"';
            }
        }

        shortcode += ']';
        
        jQuery('.pec_custom_shortcode').text(shortcode);
    };
    
    pec_updateShortcode();
</script>
<?php   
}
?>