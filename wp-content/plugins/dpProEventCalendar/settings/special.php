<?php

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// This function displays the admin page content
function dpProEventCalendar_special_page() 
{

	global $wpdb, $dpProEventCalendar, $pec_admin;
	
	if ( isset($_POST['add']) && $_POST['add'] ) 
    {
		
		foreach( $_POST as $key=>$value ) { $$key = $value; }
		
		$title = strip_tags( str_replace( "'", '"', $title ) );

        $data = array( 'title' => $title, 'color' => $color );
        $format = array( '%s', '%s' );
        $wpdb->insert( DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES, $data, $format );
		
		wp_redirect( admin_url( 'admin.php?page=dpProEventCalendar-special&settings-updated=1' ) );
		exit;

	}
	
	if ( isset( $_POST['edit'] ) && $_POST['edit'] ) 
    {
		
		foreach( $_POST as $key=>$value ) { $$key = $value; }
		
		$title = strip_tags( str_replace( "'", '"', $title ) );

        $data = array( 'title' => $title, 'color' => $color );
        $format = array( '%s', '%s' );
        $where = array( 'id' => $id );
        $where_format = array( '%d' );

        $wpdb->update( DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES, $data, $where, $format, $where_format );
		
		wp_redirect( admin_url('admin.php?page=dpProEventCalendar-special&settings-updated=1') );
		exit;

	}
	
	if ( is_numeric( $pec_admin->get('delete_sp_date') ) ) 
    {
	
       $sp_date_id = $pec_admin->get('delete_sp_date');
	   
       $where = array( 'id' => $sp_date_id );
       $where_format = array( '%d' );

       $wpdb->delete( DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES, $where, $where_format );
	   	   
	   wp_redirect( admin_url( 'admin.php?page=dpProEventCalendar-special&settings-updated=1' ) );
	   exit;
	
    }
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
                                <h2><?php _e('Special Dates / Event Color','dpProEventCalendar'); ?></h2>
                                
                                <div class="pec_pageSubtitle">
                                    <span><?php _e('Add special dates to use in the calendars. Such as holidays, company events, personal events, etc... Assign them to calendars and events.','dpProEventCalendar'); ?></span>
                                </div>
                            </div>
                            
                            <div class="clear"></div>
                        </div>
                    </div>
                    
                    <div class="wrapper">
                    	<div id="dpProEventCalendar_SpecialDates" class="dpProEventCalendar_ModalManager">
                            <a href='#' class="dpProEventCalendar_Modal_close"><?php _e('Close', 'dpProEventCalendar')?></a>
                        <form method="post" action="<?php echo admin_url('admin.php?page=dpProEventCalendar-special&noheader=true'); ?>" onsubmit="return special_checkform();">
                        <input type="hidden" name="add" value="1" />

                                <h3><?php _e('Choose Color', 'dpProEventCalendar');?></h3>

                                <div>
                                    <div class="pec_modal_block">
                                        <span><?php _e('Color Title','dpProEventCalendar'); ?></span>
                                        <div class="pec_modal_row">
                                            <input type="text" class="pec_modal_input" name="title" id="dpEventsCalendar_title" placeholder="<?php _e('Enter Title (Will be displayed in the calendar)', 'dpProEventCalendar')?>" />
                                        </div>
                                    </div>
									
                                    <div class="pec_modal_block">
                                        <span><?php _e('Color','dpProEventCalendar'); ?></span>
                                        <div class="pec_modal_row">
                                            
                                            <input type="text" class="pec_modal_input" readonly="readonly" name="color" id="dpProEventCalendar_color" placeholder="#cccccc" value="#cccccc" />
                                            <div id="specialDate_colorSelector" class="colorSelector"><div style="background-color: #ccc"></div></div>

                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                    
                                    <div>
                                    	<input type="submit" class="button" value="<?php _e('Save special date / event color', 'dpProEventCalendar') ?>" />
                                    </div>
                            	</div>

                        <div class="clear"></div>
                        </form>
                        </div>
                        
                        <div id="dpProEventCalendar_SpecialDatesEdit" class="dpProEventCalendar_ModalManager">
                            <a href='#' class="dpProEventCalendar_Modal_close"><?php _e('Close', 'dpProEventCalendar')?></a>
                        <form method="post" action="<?php echo admin_url('admin.php?page=dpProEventCalendar-special&noheader=true'); ?>" onsubmit="return special_checkform_edit();">
                        <input type="hidden" name="edit" value="1" />
                        <input type="hidden" name="id" id="dpPEC_special_id" value="" />
                            <h3><?php _e('Choose Color', 'dpProEventCalendar');?></h3>

                            <div>
                                <div class="pec_modal_block">
                                    <span><?php _e('Color Title','dpProEventCalendar'); ?></span>
                                    <div class="pec_modal_row">
                                        <input type="text" class="pec_modal_input" name="title" id="dpPEC_special_title" />
                                    </div>
                                </div>
								
                                <div class="pec_modal_block">
                                    <span><?php _e('Color','dpProEventCalendar'); ?></span>
                                    <div class="pec_modal_row">
                                        <input type="text" class="pec_modal_input" readonly="readonly" name="color" id="dpPEC_special_color" placeholder="#cccccc" value="" />
                                        <div id="specialDate_colorSelector_Edit" class="colorSelector"><div></div></div>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                
                                <div>
                                	<input type="submit" class="button" value="<?php _e('Save special date / event color', 'dpProEventCalendar') ?>" />
                                </div>
                            </div>
                        <div class="clear"></div>
                        </form>
                        </div>
                        
                        <div class="submit">
                        
                        <input type="button" value="<?php echo __( 'Add Special Date', 'dpProEventCalendar' )?> / <?php echo __( 'Event Color', 'dpProEventCalendar' )?>" class="btn_add_special_date button-primary" />
                        
                        </div>
                        <table class="widefat" cellpadding="0" cellspacing="0" id="sort-table">
                        	<thead>
                        		<tr style="cursor:default !important;">
                                    <th width="5%"><?php _e('Color','dpProEventCalendar'); ?></th>
                                    <th width="80%"><?php _e('Title','dpProEventCalendar'); ?></th>
                                    
                                    <th width="15%"><?php _e('Actions','dpProEventCalendar'); ?></th>
                                 </tr>
                            </thead>
                            <tbody>
                        <?php 
						$counter = 0;
                        $querystr = "SELECT * FROM ".DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES." ORDER BY id DESC";

                        $sp_dates_obj = $wpdb->get_results( $querystr, OBJECT );

                        if( ! empty( $sp_dates_obj )) 
                        {
                            foreach( $sp_dates_obj as $sp_dates ) 
                            {
                            
                                echo '<tr id="'.$sp_dates->id.'">

                                        <td><div style="background-color: '.$sp_dates->color.'; height: 24px; width: 48px; border-radius: 5px;"></div></td>

    									<td>'.$sp_dates->title.'</td>
    									
    									<td>
    										<input type="button" value="'.__( 'Edit', 'dpProEventCalendar' ).'" name="edit_special" data-special-date-id="'.$sp_dates->id.'" data-special-date-title=\''.$sp_dates->title.'\' data-special-date-color="'.$sp_dates->color.'" class="btn_edit_special_date button-secondary" />
    										<input type="button" value="'.__( 'Delete', 'dpProEventCalendar' ).'" name="delete_special" class="button-secondary" onclick="if(confirmSpecialDelete()) { location.href=\''.admin_url('admin.php?page=dpProEventCalendar-special&delete_sp_date='.$sp_dates->id.'&noheader=true').'\'; }" />
    									</td>
    								</tr>'; 
                                    
    							$counter++;

                            }
                        }
                            ?>

                        
                    		</tbody>
                        </table>
                    </div>
                </div>           
            </div>
        </div>

                    
</div> <!--end of float wrap -->


<?php }?>