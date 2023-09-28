<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function dpProEventCalendar_meta_box_save( $post_id ) 
{

	global $wpdb, $dpProEventCalendar;
	
	// Bail if we're doing an auto save
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

	// if our nonce isn't there, or we can't verify it, bail
	if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'my_meta_box_nonce' ) ) return;

	// if our current user can't edit this post, bail
	if( !current_user_can( 'edit_post' ) ) return;

	// now we can actually save the data
	$allowed = array(
		'a' => array( // on allow a tags
			'href' => array() // and those anchors can only have href attribute
		)
	);

	if( get_post_type( $post_id ) == DP_PRO_EVENT_CALENDAR_VENUES_POST_TYPE ) 
	{
	
		update_post_meta( $post_id, 'pec_venue_address', wp_kses( $_POST['pec_venue_address'], $allowed ) );
		update_post_meta( $post_id, 'pec_venue_phone', wp_kses( $_POST['pec_venue_phone'], $allowed ) );
		update_post_meta( $post_id, 'pec_venue_link', wp_kses( $_POST['pec_venue_link'], $allowed ) );

		update_post_meta( $post_id, 'pec_venue_map', wp_kses( $_POST['pec_venue_map'], $allowed ) );
		update_post_meta( $post_id, 'pec_venue_map_lnlat', wp_kses( $_POST['pec_venue_map_lnlat'], $allowed ) );
	
	}

	if( get_post_type( $post_id ) == DP_PRO_EVENT_CALENDAR_EVENT_POST_TYPE ) 
	{

		// make sure data is set, if author has removed the field or not populated it, delete it
		if( isset( $_POST['pec_all_day'] ) && $_POST['pec_all_day'] != '' ) 
			update_post_meta( $post_id, 'pec_all_day', wp_kses( $_POST['pec_all_day'], $allowed ) );
		else 
			delete_post_meta($post_id, 'pec_all_day');
		
		
		if( isset( $_POST['pec_tbc'] ) && $_POST['pec_tbc'] != '' ) 
			update_post_meta( $post_id, 'pec_tbc', wp_kses( $_POST['pec_tbc'], $allowed ) );
		else
			delete_post_meta($post_id, 'pec_tbc');
		

		if( isset( $_POST['pec_hide_time'] ) && $_POST['pec_hide_time'] != '' ) 
			update_post_meta( $post_id, 'pec_hide_time', wp_kses( $_POST['pec_hide_time'], $allowed ) );
		else 
			delete_post_meta($post_id, 'pec_hide_time');
		
		if( isset( $_POST['pec_id_calendar'] ) && $_POST['pec_id_calendar'] != '' ) 
			update_post_meta( $post_id, 'pec_id_calendar', wp_kses( $_POST['pec_id_calendar'], $allowed ) );
		else
			delete_post_meta($post_id, 'pec_id_calendar');
		

		$calendar_list = explode(",", $_POST['pec_id_calendar']);
		
		update_post_meta( $post_id, 'pec_enable_booking', wp_kses( $_POST['pec_enable_booking'], $allowed ) );
		update_post_meta( $post_id, 'pec_booking_continuous', wp_kses( $_POST['pec_booking_continuous'], $allowed ) );
		update_post_meta( $post_id, 'pec_show_limit', wp_kses( $_POST['pec_show_limit'], $allowed ) );
		update_post_meta( $post_id, 'pec_booking_limit', wp_kses( $_POST['pec_booking_limit'], $allowed ) );
		update_post_meta( $post_id, 'pec_booking_block_hours', wp_kses( $_POST['pec_booking_block_hours'], $allowed ) );

		$booking_price = $_POST['pec_booking_price'];
		$booking_price = str_replace(array(","), '.', $booking_price);

		if(strpos($booking_price, '.') === FALSE && $booking_price > 0) 
			$booking_price = $booking_price.'.00';
		
		update_post_meta( $post_id, 'pec_booking_price', wp_kses( $booking_price, $allowed ) );

		$pec_booking_ticket = $_POST['pec_booking_ticket'];
		update_post_meta( $post_id, 'pec_booking_ticket', wp_kses( $pec_booking_ticket, $allowed ) );
		
		update_post_meta( $post_id, 'pec_timezone', wp_kses( $_POST['pec_timezone'], $allowed ) );
		
		update_post_meta( $post_id, 'pec_recurring_frecuency', wp_kses( $_POST['pec_recurring_frecuency'], $allowed ) );
		
		
		if( is_array( $dpProEventCalendar['custom_fields_counter'] ) ) 
		{
		
			$counter = 0;
			
			foreach( $dpProEventCalendar['custom_fields_counter'] as $key ) 
			{
				
				$value = $_POST['pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter]];

				update_post_meta( $post_id, 'pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter], $value );
				
				$counter++;		
			
			}
		
		}
		
		update_post_meta( $post_id, 'pec_excerpt', wp_kses( $_POST['pec_excerpt'], $allowed ) );

		
		update_post_meta( $post_id, 'pec_link', wp_kses( $_POST['pec_link'], $allowed ) );
		update_post_meta( $post_id, 'pec_age_range', wp_kses( $_POST['pec_age_range'], $allowed ) );
		update_post_meta( $post_id, 'pec_use_link', wp_kses( $_POST['pec_use_link'], $allowed ) );
		update_post_meta( $post_id, 'pec_rtl', wp_kses( $_POST['pec_rtl'], $allowed ) );
		update_post_meta( $post_id, 'pec_color', wp_kses( $_POST['pec_color'], $allowed ) );
		update_post_meta( $post_id, 'pec_attendance', wp_kses( $_POST['pec_attendance'], $allowed ) );
		update_post_meta( $post_id, 'pec_status', wp_kses( $_POST['pec_status'], $allowed ) );
		update_post_meta( $post_id, 'pec_fb_event', wp_kses( $_POST['pec_fb_event'], $allowed ) );
		update_post_meta( $post_id, 'pec_share', wp_kses( $_POST['pec_share'], $allowed ) );
		update_post_meta( $post_id, 'pec_location', wp_kses( $_POST['pec_location'], $allowed ) );
		update_post_meta( $post_id, 'pec_organizer', wp_kses( $_POST['pec_organizer'], $allowed ) );
		update_post_meta( $post_id, 'pec_speaker', wp_kses( $_POST['pec_speakers_list'], $allowed ) );
		update_post_meta( $post_id, 'pec_phone', wp_kses( $_POST['pec_phone'], $allowed ) );
		update_post_meta( $post_id, 'pec_video', wp_kses( $_POST['pec_video'], $allowed ) );
		update_post_meta( $post_id, 'pec_map', wp_kses( $_POST['pec_map'], $allowed ) );
		update_post_meta( $post_id, 'pec_map_lnlat', wp_kses( $_POST['pec_map_lnlat'], $allowed ) );
		update_post_meta( $post_id, 'pec_user_rate', wp_kses( $_POST['pec_user_rate'], $allowed ) );
		update_post_meta( $post_id, 'pec_featured_event', wp_kses( $_POST['pec_featured_event'], $allowed ) );
		update_post_meta( $post_id, 'pec_disable_sync', wp_kses( $_POST['pec_disable_sync'], $allowed ) );
		update_post_meta( $post_id, 'pec_rate', wp_kses( $_POST['pec_rate'], $allowed ) );

		$code = get_post_meta( $post_id, 'pec_code', true );
		if( ! isset( $code ) || $code == ""  )
		{

			$code = dpProeventCalendar_generate_code();
			update_post_meta( $post_id, 'pec_code', $code );

		}

		$old_date = get_post_meta( $post_id, 'pec_date', true);
		$new_date_short = wp_kses( $_POST['pec_date_y'] ."-".str_pad($_POST['pec_date_m'], 2, "0", STR_PAD_LEFT)."-".str_pad($_POST['pec_date_d'], 2, "0", STR_PAD_LEFT), $allowed);
		$new_date = $new_date_short. " " . $_POST['pec_time_hours'] . ":" . $_POST['pec_time_minutes'] . ":00";

		if( isset( $old_date ) && $old_date != "" && $old_date != $new_date ) 
		{
		
			$wpdb->update( 
				DP_PRO_EVENT_CALENDAR_TABLE_BOOKING, 
				array( 
					'event_date'	=> $new_date_short
				), 
				array( 
						'id_event' => $post_id,
						'event_date' => date("Y-m-d", strtotime($old_date))
					),
				array(  
					'%s'
				),
				array(  
					'%d',
					'%s'
				) 
			);

		}

		update_post_meta( $post_id, 'pec_date', $new_date);

		$end_time_hh = $_POST['pec_end_time_hh'];

		update_post_meta( $post_id, 'pec_end_time_hh', wp_kses( $end_time_hh, $allowed ) );

		$end_time_mm = $_POST['pec_end_time_mm'];

		if($end_time_mm == '' && $end_time_hh != '')
			$end_time_mm = '00';

		update_post_meta( $post_id, 'pec_end_time_mm', wp_kses( $end_time_mm, $allowed ) );
		
		$end_date = $_POST['pec_end_date_y']."-".str_pad($_POST['pec_end_date_m'], 2, "0", STR_PAD_LEFT)."-".str_pad($_POST['pec_end_date_d'], 2, "0", STR_PAD_LEFT);
		if( strlen( $end_date ) < 10 ) 
			$end_date = '';
		
		if( $end_date == $_POST['pec_date'] && $_POST['pec_recurring_frecuency'] == 0 ) 
			$end_date = '';
		
		update_post_meta( $post_id, 'pec_end_date', wp_kses( $end_date, $allowed ) );
		update_post_meta( $post_id, 'pec_exceptions', $_POST['pec_exceptions'] );
		update_post_meta( $post_id, 'pec_extra_dates',  $_POST['pec_extra_dates'] );
		update_post_meta( $post_id, 'pec_daily_every', $_POST['pec_daily_every'] );
		update_post_meta( $post_id, 'pec_daily_working_days', $_POST['pec_daily_working_days'] );
		update_post_meta( $post_id, 'pec_weekly_day', $_POST['pec_weekly_day'] );
		update_post_meta( $post_id, 'pec_weekly_every', $_POST['pec_weekly_every'] );
		update_post_meta( $post_id, 'pec_monthly_every', $_POST['pec_monthly_every'] );
		update_post_meta( $post_id, 'pec_monthly_position', $_POST['pec_monthly_position'] );
		update_post_meta( $post_id, 'pec_monthly_day', $_POST['pec_monthly_day'] ) ;
		
		
		// Update the post excerpt
	    $wpdb->update( 
			$wpdb->posts, 
			array( 
				'post_excerpt'		=> $_POST['pec_excerpt']
			), 
			array( 'ID' => $post_id ), 
			array( 
				'%s'
			),
			array( 
				'%d'
			) 
		);

	    
	    //wp_update_post( $excerpt_update );

		if( is_array( $calendar_list ) && !empty( $calendar_list ) ) 
		{
			$calendar_data = $wpdb->get_row( 
				$wpdb->prepare("
					SELECT booking_cancel_email_template, booking_cancel_email_enable 
					FROM " . DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS . " 
					WHERE id = %d", $calendar_list[0]
				)
			);
		}

		if(is_array( $_POST['pec_booking_status'] ) ) 
		{
		
			foreach( $_POST['pec_booking_status'] as $booking_id => $booking_status ) 
			{
				
				$wpdb->update( 
					DP_PRO_EVENT_CALENDAR_TABLE_BOOKING, 
					array( 
						'status'		=> $booking_status,
						'cancel_reason' => $_POST['pec_booking_cancel_reason'][$booking_id]
					), 
					array( 'id' => $booking_id ), 
					array( 
						'%s'
					),
					array( 
						'%d'
					) 
				);

				if( $_POST['pec_booking_cancel_send_mail'][$booking_id] && $calendar_data->booking_cancel_email_enable ) 
				{
					
					$booking_cancel_email_template = $calendar_data->booking_cancel_email_template;
					if($booking_cancel_email_template == '') 
						$booking_cancel_email_template = "Hi #USERNAME#,\n\nThe following booking has been canceled:\n\n#EVENT_DETAILS#\n\n#CANCEL_REASON#\n\nPlease contact us if you have questions.\n\nKind Regards.\n#SITE_NAME#";
					
					
					add_filter( 'wp_mail_from_name', 'dpProEventCalendar_wp_mail_from_name' );
					add_filter( 'wp_mail_from', 'dpProEventCalendar_wp_mail_from' );
					$headers = array('Content-Type: text/html; charset=UTF-8');

					// Email to User
					
					if( isset( $_POST['pec_booking_email'][$booking_id] ) ) 
					{

						$booking_user_email = $_POST['pec_booking_email'][$booking_id];
						
						wp_mail( $booking_user_email, get_bloginfo('name'), apply_filters('pec_booking_email_cancel', $booking_cancel_email_template, $booking_id), $headers );

					}
				}
			}
		}
	}	
}

add_action( 'save_post', 'dpProEventCalendar_meta_box_save' );

function dpProEventCalendar_excerpt_display( $post ) 
{
	
	$values = get_post_custom( $post->ID );
	$pec_excerpt = isset( $values['pec_excerpt'] ) ? $values['pec_excerpt'][0] : '';

	wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
	
	?>
	<textarea name="pec_excerpt" id="excerpt"><?php echo $pec_excerpt?></textarea>
    <label class="dp_ui_pec_content_desc"><p><?php _e('Excerpts are optional hand-crafted summaries of your content that can be used in your theme.', 'dpProEventCalendar'); ?> <a href="https://codex.wordpress.org/Excerpt" target="_blank"><?php _e('Learn more about manual excerpts.', 'dpProEventCalendar'); ?></a></p></label>

	<?php
}

function dpProEventCalendar_events_display( $post ) 
{

	global $dpProEventCalendar, $wpdb, $pec_admin;
	
	$values = get_post_custom( $post->ID );
	$pec_link = isset( $values['pec_link'] ) ? $values['pec_link'][0] : '';
	$pec_age_range = isset( $values['pec_age_range'] ) ? $values['pec_age_range'][0] : '';
	$pec_status = isset( $values['pec_status'] ) ? $values['pec_status'][0] : '';
	$pec_color = isset( $values['pec_color'] ) ? $values['pec_color'][0] : '';
	$pec_attendance = isset( $values['pec_attendance'] ) ? $values['pec_attendance'][0] : '';
	$pec_fb_event = isset( $values['pec_fb_event'] ) ? $values['pec_fb_event'][0] : '';
	$pec_share = isset( $values['pec_share'] ) ? $values['pec_share'][0] : '';
	$pec_location = isset( $values['pec_location'] ) ? $values['pec_location'][0] : '';
	$pec_organizer = isset( $values['pec_organizer'] ) ? $values['pec_organizer'][0] : '';
	$pec_speaker = isset( $values['pec_speaker'] ) ? $values['pec_speaker'][0] : '';
	$pec_phone = isset( $values['pec_phone'] ) ? $values['pec_phone'][0] : '';
	$pec_video = isset( $values['pec_video'] ) ? $values['pec_video'][0] : '';
	$pec_map = isset( $values['pec_map'] ) ? $values['pec_map'][0] : '';
	$pec_map_lnlat = isset( $values['pec_map_lnlat'] ) ? $values['pec_map_lnlat'][0] : '';
	$pec_user_rate = isset( $values['pec_user_rate'] ) ? $values['pec_user_rate'][0] : '';
	$pec_featured_event = isset( $values['pec_featured_event'] ) ? $values['pec_featured_event'][0] : '';
	$pec_disable_sync = isset( $values['pec_disable_sync'] ) ? $values['pec_disable_sync'][0] : '';
	$pec_rate = isset( $values['pec_rate'] ) ? $values['pec_rate'][0] : '';
	$pec_use_link = isset( $values['pec_use_link'] ) ? $values['pec_use_link'][0] : '';
	$pec_rtl = isset( $values['pec_rtl'] ) ? $values['pec_rtl'][0] : '';

	$pec_speakers_list = explode( ',', $pec_speaker );

	wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
	

	if( $pec_admin->get('action') == 'edit' ) 
	{
	?>
	

	<div class="misc-pub-section">
		<div>
			<label for="pec_disable_sync"><?php _e('Disable Sync?', 'dpProEventCalendar'); ?> <?php $pec_admin->show_info( __( 'If this event is being synced, you can disable the sync and make changes to the event.','dpProEventCalendar') ); ?></label>
			
		</div>
        <div>
	    	<input type="checkbox" value="1" name="pec_disable_sync" id="pec_disable_sync" <?php if($pec_disable_sync) {?> checked="checked"<?php }?> /> 
	    	
	    </div>
    </div> 

    <div class="clear"></div>
    <?php }?>

    <div class="misc-pub-section">
		<div>
			<label for="pec_status"><?php _e('Status', 'dpProEventCalendar'); ?></label>
			<label class="dp_ui_pec_content_desc"><?php _e('Select the Event Status','dpProEventCalendar'); ?></label>
		</div>
		<div>
			<select name="pec_status">
	        	<option value=""><?php _e('Scheduled', 'dpProEventCalendar')?></option>
	        	<option value="cancelled" <?php echo ($pec_status == 'cancelled' ? 'selected="selected"' : '')?>><?php _e('Cancelled', 'dpProEventCalendar')?></option>
	        	<option value="moved_online" <?php echo ($pec_status == 'moved_online' ? 'selected="selected"' : '')?>><?php _e('Moved Online', 'dpProEventCalendar')?></option>
	        	<option value="postponed" <?php echo ($pec_status == 'postponed' ? 'selected="selected"' : '')?>><?php _e('Postponed', 'dpProEventCalendar')?></option>
	        	<option value="rescheduled" <?php echo ($pec_status == 'rescheduled' ? 'selected="selected"' : '')?>><?php _e('Rescheduled', 'dpProEventCalendar')?></option>
	            
	        </select>
	        
	    </div>
	</div>
    <div class="clear"></div>

    <div class="misc-pub-section">
    	<div>
			<label for="pec_attendance"><?php _e('Attendance Mode', 'dpProEventCalendar'); ?></label>
			<label class="dp_ui_pec_content_desc"></label>
		</div>
		<div>
			<select name="pec_attendance">
	        	<option value=""><?php _e('Offline', 'dpProEventCalendar')?></option>
	        	<option value="online" <?php echo ($pec_attendance == 'online' ? 'selected="selected"' : '')?>><?php _e('Online', 'dpProEventCalendar')?></option>
	        	<option value="mixed" <?php echo ($pec_attendance == 'mixed' ? 'selected="selected"' : '')?>><?php _e('Mixed', 'dpProEventCalendar')?></option>
	            
	        </select>
	        
	    </div>
	</div>
    <div class="clear"></div>
    
    <div class="misc-pub-section">
		<div>
			<label for="pec_featured_event"><?php _e('Featured Event?', 'dpProEventCalendar'); ?> <?php $pec_admin->show_info( __( 'Display it first in some event lists.','dpProEventCalendar') ); ?></label>
			
		</div>
        <div>
	    	<input type="checkbox" value="1" name="pec_featured_event" id="pec_featured_event" <?php if($pec_featured_event) {?> checked="checked"<?php }?> /> 
	    	
	    </div>
    </div> 
    <div class="clear"></div>

    <div class="misc-pub-section">
    	<div>
			<label for="pec_color"><?php _e('Color (optional)', 'dpProEventCalendar'); ?></label>
			<label class="dp_ui_pec_content_desc"><?php _e('Select a color. To create a new one, go to the <a href="'.admin_url( 'admin.php?page=dpProEventCalendar-special' ).'" target="_blank">special dates</a> section','dpProEventCalendar'); ?></label>
		</div>
		<div>
			<select name="pec_color">
	        	<option value=""><?php _e('None', 'dpProEventCalendar')?></option>
	             <?php 
				$counter = 0;
				$querystr = "
				SELECT *
				FROM ".DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES." 
				ORDER BY title ASC
				";
				$sp_dates_obj = $wpdb->get_results($querystr, OBJECT);
				foreach( $sp_dates_obj as $sp_dates ) 
				{
				?>
	            
	            	<option value="<?php echo $sp_dates->id?>" <?php echo ($pec_color == $sp_dates->id ? 'selected="selected"' : '')?>><?php echo $sp_dates->title?></option>
	            
	            <?php }?>
	        </select>
	        
	    </div>
	</div>
    <div class="clear"></div>

    <div class="misc-pub-section">
    	<div>
			<label for="pec_link" style="height:50px;"><?php _e('Link (optional)', 'dpProEventCalendar'); ?></label>
		</div>
		<div>
			<input type="text" name="pec_link" size="80" id="pec_link" value="<?php echo $pec_link; ?>" placeholder="<?php _e('Introduce a URL','dpProEventCalendar'); ?>" />
			<br />
			<input type="checkbox" value="1" name="pec_use_link" id="pec_use_link" <?php if($pec_use_link) {?> checked="checked"<?php }?> /> 
			<span class="pec_data_desc"><?php _e('Use this url instead of the event single page?','dpProEventCalendar'); ?></span>
		</div>
	</div>
    <div class="clear"></div>
    <div class="misc-pub-section">
    	<div>
			<label for="pec_fb_event"><?php _e('Facebook Event URL (optional)', 'dpProEventCalendar'); ?></label>
		</div>
		<div>
			<input type="text" name="pec_fb_event" size="80" id="pec_fb_event" value="<?php echo $pec_fb_event; ?>" placeholder="<?php _e('Introduce a Facebook Page URL','dpProEventCalendar'); ?>" />
		</div>
	</div>
    <div class="clear"></div>
    <div class="misc-pub-section">
		<label for="pec_age_range"><?php _e('Age Range (optional)', 'dpProEventCalendar'); ?></label>
		<div>
			<input type="text" name="pec_age_range" size="80" id="pec_age_range" value="<?php echo $pec_age_range; ?>" placeholder="<?php _e('The expected age range','dpProEventCalendar'); ?>" />
		</div>
	</div>
    <div class="clear"></div>
    <div class="misc-pub-section">
		<label for="pec_phone"><?php _e('Phone (optional)', 'dpProEventCalendar'); ?></label>
		<div>
			<input type="text" name="pec_phone" size="80" id="pec_phone" value="<?php echo $pec_phone; ?>" placeholder="<?php _e('Introduce the Phone number','dpProEventCalendar'); ?>" />
		</div>
	</div>
	<div class="clear"></div>
	<div class="misc-pub-section">
		<label for="pec_video"><?php _e('Video URL (optional)', 'dpProEventCalendar'); ?></label>
		<div>
			<input type="text" name="pec_video" size="80" id="pec_video" value="<?php echo $pec_video; ?>" placeholder="<?php _e('Introduce a Youtube video URL','dpProEventCalendar'); ?>" />
		</div>
	</div>
    <div class="clear"></div>
    <div class="misc-pub-section">
		<div>
			<label for="pec_location"><?php _e('Location / Venue', 'dpProEventCalendar'); ?></label>
		</div>
		<div>
			<select name="pec_location">
	        	<option value=""><?php _e('None', 'dpProEventCalendar')?></option>
	             <?php 
				$args = array(
				'posts_per_page'   => -1,
				'post_type'        => DP_PRO_EVENT_CALENDAR_VENUES_POST_TYPE,
				'post_status'      => 'publish',
				'order'			   => 'ASC', 
				'orderby' 		   => 'title' 
				);

				$venues_list = get_posts($args);
				foreach($venues_list as $venue) {
				?>
	            
	            	<option value="<?php echo $venue->ID?>" <?php echo ($pec_location == $venue->ID ? 'selected="selected"' : '')?>><?php echo $venue->post_title?></option>
	            
	            <?php }?>
	        </select> &nbsp; <a href="post-new.php?post_type=pec-venues" class="button" target="_blank"><?php _e('New Venue', 'dpProEventCalendar'); ?></a>
			<!--<input type="text" name="pec_location" size="80" id="pec_location" value="<?php echo $pec_location; ?>" placeholder="<?php _e('Location to be displayed in the event list','dpProEventCalendar'); ?>" />-->
		</div>
	</div>
    <div class="clear"></div>
    <div class="misc-pub-section">
    	<div>
			<label for="pec_organizer"><?php _e('Organizer', 'dpProEventCalendar'); ?></label>
		</div>
		<div>
			<select name="pec_organizer">
	        	<option value=""><?php _e('None', 'dpProEventCalendar')?></option>
	             <?php 
				$args = array(
				'posts_per_page'   => -1,
				'post_type'        => DP_PRO_EVENT_CALENDAR_ORGANIZERS_POST_TYPE,
				'post_status'      => 'publish',
				'order'			   => 'ASC', 
				'orderby' 		   => 'title' 
				);

				$organizers_list = get_posts($args);
				foreach($organizers_list as $organizer) {
				?>
	            
	            	<option value="<?php echo $organizer->ID?>" <?php echo ($pec_organizer == $organizer->ID ? 'selected="selected"' : '')?>><?php echo $organizer->post_title?></option>
	            
	            <?php }?>
	        </select> &nbsp; <a href="post-new.php?post_type=pec-organizers" class="button" target="_blank"><?php _e('New Organizer', 'dpProEventCalendar'); ?></a>
	    </div>
	</div>
    <div class="clear"></div>

    <div class="misc-pub-section">
		<div>
			<label for="pec_speaker"><?php _e('Speakers', 'dpProEventCalendar'); ?></label>
		</div>
		<div>
			<select name="pec_speaker" id="pec_speaker">
	        	<option value=""></option>
	             <?php 
				$args = array(
					'posts_per_page'   => -1,
					'post_type'        => DP_PRO_EVENT_CALENDAR_SPEAKERS_POST_TYPE,
					'post_status'      => 'publish',
					'order'			   => 'ASC', 
					'orderby' 		   => 'title' 
				);

				$speakers_list = get_posts($args);
				foreach($speakers_list as $speaker) 
				{
				?>
	            
	            	<option value="<?php echo $speaker->ID?>"><?php echo $speaker->post_title?></option>
	            
	            <?php }?>
	        </select> 
	        &nbsp; <a href="post-new.php?post_type=pec-speakers" class="button" target="_blank"><?php _e('New Speaker', 'dpProEventCalendar'); ?></a>

	        <br />

	        <div class="pec-component-tag-wrap">
	        	<input type="hidden" name="pec_speakers_list" id="pec_speakers_list" value="<?php echo $pec_speaker?>" />

	        	<?php
	    	
	        	foreach($pec_speakers_list as $speaker) 
				{

					if( $speaker == '') continue;
				?>

		        <span class="pec-component-tag">
		        	<span class="pec-component-tag-text">
		        		<?php echo get_the_title($speaker)?>
		        	</span>
		        	<button type="button" data-speaker_id="<?php echo $speaker ?>"><span class="dashicons dashicons-dismiss"></span></button>
		        </span>

		    	<?php } ?>

		    </div>

	        <div class="clear"></div>
	    </div>

	</div>
    <div class="clear"></div>
    

    <div class="misc-pub-section"> 
    	<label for="pec_rtl"><?php _e('Right-to-left', 'dpProEventCalendar'); ?></label>  
    	<div> 
        	<input type="checkbox" value="1" name="pec_rtl" id="pec_rtl" <?php if($pec_rtl) {?> checked="checked"<?php }?> /> <?php _e(' (RTL in single page)','dpProEventCalendar'); ?>
        </div>
	</div>
    <div class="clear"></div>
    
    <div class="misc-pub-section">
    	<div>
			<label for="pec_user_rate"><?php _e('Rating (optional)', 'dpProEventCalendar'); ?></label>
		</div>
        
        <div>
			<select name="pec_rate" id="pec_rate">
	        	<option value=""><?php _e('None', 'dpProEventCalendar'); ?></option>
	            <option value="1" <?php echo $pec_rate == 1 ? 'selected="selected"' : ''?>><?php _e('1 Star', 'dpProEventCalendar'); ?></option>
	            <option value="1.5" <?php echo $pec_rate == 1.5 ? 'selected="selected"' : ''?>><?php _e('1.5 Stars', 'dpProEventCalendar'); ?></option>
	            <option value="2" <?php echo $pec_rate == 2 ? 'selected="selected"' : ''?>><?php _e('2 Stars', 'dpProEventCalendar'); ?></option>
	            <option value="2.5" <?php echo $pec_rate == 2.5 ? 'selected="selected"' : ''?>><?php _e('2.5 Stars', 'dpProEventCalendar'); ?></option>
	            <option value="3" <?php echo $pec_rate == 3 ? 'selected="selected"' : ''?>><?php _e('3 Stars', 'dpProEventCalendar'); ?></option>
	            <option value="3.5" <?php echo $pec_rate == 3.5 ? 'selected="selected"' : ''?>><?php _e('3.5 Stars', 'dpProEventCalendar'); ?></option>
	            <option value="4" <?php echo $pec_rate == 4 ? 'selected="selected"' : ''?>><?php _e('4 Stars', 'dpProEventCalendar'); ?></option>
	            <option value="4.5" <?php echo $pec_rate == 4.5 ? 'selected="selected"' : ''?>><?php _e('4.5 Stars', 'dpProEventCalendar'); ?></option>
	            <option value="5" <?php echo $pec_rate == 5 ? 'selected="selected"' : ''?>><?php _e('5 Stars', 'dpProEventCalendar'); ?></option>
	        </select> 
	        <div class="clear"></div>
	    	<input type="checkbox" value="1" name="pec_user_rate" id="pec_user_rate" <?php if($pec_user_rate) {?> checked="checked"<?php }?> /> <?php _e('Allow logged in users to rate events. (The manual rating will be disabled)','dpProEventCalendar'); ?>
	    </div>
    </div>    
    
    <div class="clear"></div>
    
    <?php 
	if( is_array( $dpProEventCalendar['custom_fields_counter'] ) ) 
	{
	
		$counter = 0;
		foreach( $dpProEventCalendar['custom_fields_counter'] as $key ) 
		{
	?>
    <div class="misc-pub-section">
		<label for="pec_map"><?php echo $dpProEventCalendar['custom_fields']['name'][$counter]?></label>
		<div>
        <?php 
        $custom_field = $dpProEventCalendar['custom_fields'];

        switch($dpProEventCalendar['custom_fields']['type'][$counter])
        {
        	case "checkbox":
        ?>

        		<input type="checkbox" name="pec_custom_<?php echo $dpProEventCalendar['custom_fields']['id'][$counter]?>" id="pec_custom_<?php echo $dpProEventCalendar['custom_fields']['id'][$counter]?>" value="1" <?php if(get_post_meta($post->ID, 'pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter], true)) { ?> checked="checked" <?php }?> /> <?php echo htmlentities($dpProEventCalendar['custom_fields']['name'][$counter])?>
        <?php 
        		break;

        	case "text":

        ?>

		<input type="text" name="pec_custom_<?php echo $dpProEventCalendar['custom_fields']['id'][$counter]?>" size="80" id="pec_custom_<?php echo $dpProEventCalendar['custom_fields']['id'][$counter]?>" value="<?php echo htmlentities(get_post_meta($post->ID, 'pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter], true)); ?>" placeholder="<?php echo htmlentities($dpProEventCalendar['custom_fields']['name'][$counter])?>" />

        <?php 
        	break;

        	case "dropdown":

        		$html = '<select autocomplete="off" class="'.(!$custom_field['optional'][$counter] ? 'pec_required' : '').'" id="pec_custom_'.$custom_field['id'][$counter].'" name="pec_custom_'.$custom_field['id'][$counter].'">';

				if(is_array($custom_field['placeholder'][$counter])) 
				{

					foreach($custom_field['placeholder'][$counter] as $key => $value) 
					{
					
						$html .= '<option option="'.$custom_field['placeholder'][$counter][$key].'" '.(get_post_meta($post->ID, 'pec_custom_'.$custom_field['id'][$counter], true) ==  $custom_field['placeholder'][$counter][$key] ? 'selected="selected"' : "").'>'.$custom_field['placeholder'][$counter][$key].'</option>';

					}

				}
				

				$html .= '</select>';
				echo $html;
				
				break;

			case "multiple_checkbox":

				$multiple = get_post_meta($post->ID, 'pec_custom_'.$custom_field['id'][$counter], true);
				if(!is_array($multiple)) 
				{
				
					$multiple = array();
				
				}

        		$html = '<ul>';

				if( is_array( $custom_field['placeholder'][$counter] ) ) 
				{

					foreach( $custom_field['placeholder'][$counter] as $key => $value ) 
					{
					
						$html .= 	'<li>';
						$html .=		'<label>';

						$html .= 			'<input type="checkbox" name="pec_custom_'.$custom_field['id'][$counter].'[]" value="'.esc_attr($value).'" '.(in_array($value, $multiple) ? 'checked="checked"' : "").' />';

						$html .= 		esc_attr($value);

						$html .=		'</label>
									</li>';

					}

				}
				

				$html .= '</ul>';
				echo $html;
				
				break;


    	}?>
    	</div>
	</div>
    <div class="clear"></div>
	<?php
			$counter++;
		}
	}
}

function dpProEventCalendar_booking_display( $post ) 
{

	global $wpdb, $dp_pec_payments, $dpProEventCalendar, $pec_admin;
	
	$values = get_post_custom( $post->ID );
	$pec_enable_booking = isset( $values['pec_enable_booking'] ) ? $values['pec_enable_booking'][0] : '';
	$pec_show_limit = isset( $values['pec_show_limit'] ) ? $values['pec_show_limit'][0] : '';
	$pec_booking_limit = isset( $values['pec_booking_limit'] ) ? $values['pec_booking_limit'][0] : '';
	$pec_booking_block_hours = isset( $values['pec_booking_block_hours'] ) ? $values['pec_booking_block_hours'][0] : '';
	$pec_booking_price = isset( $values['pec_booking_price'] ) ? $values['pec_booking_price'][0] : '';
	$pec_booking_continuous = isset( $values['pec_booking_continuous'] ) ? $values['pec_booking_continuous'][0] : '';
	$pec_booking_ticket = isset( $values['pec_booking_ticket'] ) ? $values['pec_booking_ticket'][0] : '';
	$pec_booking_ticket_arr = ($pec_booking_ticket != "" ? explode(",", $pec_booking_ticket) : '');
	
	$event_code = get_post_meta( $post->ID, 'pec_code', true );
	if( $event_code == '' )
		$event_code = $post->ID;

	wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );


	?>
    <div class="misc-pub-section">
		<div><input type="checkbox" value="1" name="pec_enable_booking" id="pec_enable_booking" <?php if($pec_enable_booking) {?> checked="checked"<?php }?> />  <?php _e('Enable Booking', 'dpProEventCalendar'); ?></div>
	</div>
    
    <div class="misc-pub-section">
	    <div>
			<label for="pec_booking_limit"><?php _e('Limit (optional)', 'dpProEventCalendar'); ?></label>
			<label class="dp_ui_pec_content_desc"><?php _e('Introduce the maximum number of bookings allowed for this event.','dpProEventCalendar'); ?></label>
		</div>
		<div>
			<input type="number" min="0" max="999999" name="pec_booking_limit" size="50" id="pec_booking_limit" value="<?php echo $pec_booking_limit; ?>" placeholder="e.g 30" /><br />
	        
	    </div>
	</div>

	<div class="clear"></div>

	<div class="misc-pub-section">
		<div>
			<label for="pec_booking_block_hours"><?php _e('Block Hours (optional)', 'dpProEventCalendar'); ?></label>
			<label class="dp_ui_pec_content_desc"><?php _e('Block hours prior to the event date, so users can\'t book an event in those hours. i.e: If you need 2 days before approving a booking, you should set 48 hours.','dpProEventCalendar'); ?></label>
		</div>
		<div>
			<input type="number" min="0" max="999999" name="pec_booking_block_hours" size="50" id="pec_booking_block_hours" value="<?php echo $pec_booking_block_hours; ?>" placeholder="e.g 48" /> &nbsp; <?php _e('Hours', 'dpProEventCalendar')?><br />
	        
	    </div>
	</div>
	<div class="clear"></div>

	<div class="misc-pub-section">
		<div>
			<label for="pec_booking_continuous"><?php _e('Continuous booking?', 'dpProEventCalendar'); ?></label>
			<label class="dp_ui_pec_content_desc"><?php _e('The attendees are registered automatically to all the event dates.','dpProEventCalendar'); ?></label>
		</div>
		<div>
			<input type="checkbox" value="1" name="pec_booking_continuous" id="pec_booking_continuous" <?php if($pec_booking_continuous) {?> checked="checked"<?php }?> /> 
	        
	    </div>
	</div>
    
    <div class="clear"></div>
    <?php if ( is_plugin_active( 'dp-pec-payments/dp-pec-payments.php' ) ) {?>
    <div class="misc-pub-section">
    	<div>
			<label for="pec_booking_price"><?php _e('Price (optional)', 'dpProEventCalendar'); ?></label>
			<label class="dp_ui_pec_content_desc"><?php _e('Introduce the booking price.','dpProEventCalendar'); ?></label>
		</div>
		<div>
			<input type="text" name="pec_booking_price" id="pec_booking_price" value="<?php echo $pec_booking_price; ?>" placeholder="e.g 15.00" /> &nbsp; <?php echo $dp_pec_payments['currency']?><br />
	       
	    </div>
	</div>
    <div class="clear"></div>
    <?php } else { ?>
    <div class="misc-pub-section">
		<label for="pec_booking_price"><?php _e('Price (optional)', 'dpProEventCalendar'); ?></label>
        <div>
			<input type="number" min="0" max="999999" name="pec_booking_price" size="50" id="pec_booking_price" value="" disabled="disabled" /><br />
	        <div class="pec_admin_errorCustom" style="float: left;"><p><?php _e('Notice: This feature requires the <a href="'.DP_PRO_EVENT_CALENDAR_PAYMENTS_URL.'" target="_blank">
	Payments Extension</a>.','dpProEventCalendar'); ?></p></div>
	        <label class="dp_ui_pec_content_desc"><?php _e('Introduce the booking price. Leave blank to allow bookings for free.','dpProEventCalendar'); ?></label>
	    </div>
	</div>
    <div class="clear"></div>
    <?php }?>

    <?php if ( is_plugin_active( 'woocommerce-dp-pec/woocommerce-dp-pec.php' ) ) {?>
    <div class="misc-pub-section">
		<label for="pec_booking_ticket"><?php _e('Tickets (optional)', 'dpProEventCalendar'); ?>
		
			<?php $pec_admin->show_info( __('Users will choose a ticket in the booking form and they will be redirected to the Woocommerce product page to purchase it.','dpProEventCalendar') ); ?>

		</label>

		<div>

			<select name="" id="pec_booking_ticket_select">
	        	<option value=""><?php _e('Choose one...', 'dpProEventCalendar')?></option>
	             <?php 
				$args = array(
				'posts_per_page'   => -1,
				'post_type'        => 'product',
				'post_status'      => 'publish',
				'order'			   => 'ASC', 
				'orderby' 		   => 'title',
				'tax_query' => array(
			        array(
			            'taxonomy' => 'product_type',
			            'field'    => 'slug',
			            'terms'    => 'pec_ticket', 
			        ),
			    )
				);

				$products = get_posts($args);
				foreach($products as $ticket) {
				?>
	            
	            	<option value="<?php echo $ticket->ID?>"><?php echo $ticket->post_title?></option>
	            
	            <?php }?>
	        </select> 
	        <input type="hidden" name="pec_booking_ticket" id="pec_booking_ticket" value="<?php echo $pec_booking_ticket?>" />
	        &nbsp; <a href="post-new.php?post_type=product&pec_ticket=1" class="button" target="_blank"><?php _e('Create new Ticket', 'dpProEventCalendar'); ?></a>

	        <br>
	        <div class="pec_ticket_list_wrap">
	        <?php 
	        if(is_array( $pec_booking_ticket_arr ) && !empty( $pec_booking_ticket_arr ) ) 
	        {
	        
	        	foreach( $pec_booking_ticket_arr as $key ) 
	        	{ 
	        	
	        		if( $key == "" ) { continue; }?>
	        <span class="pec_ticket_list" data-ticket-id="<?php echo $key?>"><?php echo get_the_title($key)?> <span class="dashicons dashicons-dismiss"></span></span>
	        <?php }
	    	}?>
	        </div>
	        <label class="dp_ui_pec_content_desc"><?php _e('Choose the booking ticket from Woocommerce.','dpProEventCalendar'); ?></label>
	    </div>
	</div>
    <div class="clear"></div>
    <?php } elseif(false) { ?>
    <div class="misc-pub-section">
		<label for="pec_booking_ticket"><?php _e('Tickets (optional)', 'dpProEventCalendar'); ?>
		
			<?php $pec_admin->show_info( __('Users will choose a ticket in the booking form and they will be redirected to the Woocommerce product page to purchase it.','dpProEventCalendar') ); ?>

		</label>
        
        <div>
			<select name="pec_booking_ticket"  disabled="disabled">
	        	<option value=""><?php _e('None', 'dpProEventCalendar')?></option>
	        </select><br />

	        <div class="pec_admin_errorCustom" style="float: left;"><p><?php _e('Notice: This feature requires the <a href="'.DP_PRO_EVENT_CALENDAR_PAYMENTS_URL.'" target="_blank">
	Woocommerce Event Tickets extension</a>.','dpProEventCalendar'); ?></p></div>
	        <label class="dp_ui_pec_content_desc"><?php _e('Introduce the booking price. Leave blank to allow bookings for free.','dpProEventCalendar'); ?></label>
	    </div>
	</div>
    <div class="clear"></div>
    <?php }?>
    
    <h2><?php _e('List of Bookings', 'dpProEventCalendar'); ?> 
    
    <?php if(current_user_can('edit_others_posts')) {?>
    
    <a class="button" style="float: right; margin-left: 10px;" href="<?php echo dpProEventCalendar_plugin_url( 'includes/export_bookings.php?i=' . $event_code )?>"><?php _e('Export to Excel', 'dpProEventCalendar'); ?></a>

    <a class="button button-primary" style="float: right;"  href="javascript:void(0);" id="pec_new_booking_show"><?php _e('Create Booking', 'dpProEventCalendar'); ?></a>

    <?php }?>
    
    </h2>

    <?php if(current_user_can('edit_others_posts')) {?>

    <div id="pec_new_booking" style="display: none;">
	    <div class="misc-pub-section">
			<label for="pec_new_booking_user"><?php _e('User', 'dpProEventCalendar'); ?></label>
			<div>
				<select name="pec_new_booking_user" id="pec_new_booking_user">
		             <?php 
					$blogusers = get_users( 'orderby=nicename&number=2000' );

					foreach($blogusers as $user ) {
					?>
		            
		            	<option value="<?php echo $user->ID?>"><?php echo $user->display_name?></option>
		            
		            <?php }?>
		        </select> 
		    </div>
		</div>
	    <div class="clear"></div>

	    <div class="misc-pub-section">
			<label for="pec_new_booking_phone"><?php _e('Phone', 'dpProEventCalendar'); ?></label>
			<div>
		    	<input type="text" name="pec_new_booking_phone" size="50" id="pec_new_booking_phone" style="width:150px;" value="" /> 
		    </div>
	    </div>
	    <div class="clear"></div>
	    

	    <div class="misc-pub-section">
            <label for="pec_new_booking_date"><?php _e('Date', 'dpProEventCalendar'); ?></label>
            <div>
	            <input type="text" readonly="readonly" name="pec_new_booking_date" maxlength="10" onfocus="jQuery('.dpProEventCalendar_btn_getBookingEventDate').trigger('click');" id="pec_new_booking_date" class="large-text" style="width:100px;" />
	        </div>
            
        </div>

        <script type="text/javascript">
			jQuery(document).ready(function() {
			jQuery("#pec_new_booking_date").datepicker({
					
					showOn: "button",
					//isRTL: '.$isRTL.',
					buttonText: '<i class="dashicons dashicons-calendar"></i>',
					buttonImageOnly: false,
					minDate: 0,
					dateFormat: "yy-mm-dd",
					
				});
			});
	    </script>
	    <div class="clear"></div>

	    <div class="misc-pub-section">
			<label for="pec_new_booking_quantity"><?php _e('Quantity', 'dpProEventCalendar'); ?></label>
			<div>
		    	<input type="number" min="0" max="9999" name="pec_new_booking_quantity" size="50" id="pec_new_booking_quantity" value="1" /> 
		    </div>
	    </div>
	    <div class="clear"></div>

	    <div class="misc-pub-section">
			<label for="pec_new_booking_status"><?php _e('Status', 'dpProEventCalendar'); ?></label>
			<div>
			    <select name="pec_new_booking_status" id="pec_new_booking_status">
					<option value="<?php echo DP_PRO_EVENT_CALENDAR_BOOKING_COMPLETED ?>"><?php echo __( 'Completed', 'dpProEventCalendar' )?></option>
			        <option value="<?php echo DP_PRO_EVENT_CALENDAR_BOOKING_PENDING ?>"><?php echo __( 'Pending', 'dpProEventCalendar' )?></option>
			        <option value="<?php echo DP_PRO_EVENT_CALENDAR_BOOKING_CANCELED_BY_USER ?>"><?php echo __( 'Canceled By User', 'dpProEventCalendar' )?></option>
			        <option value="<?php echo DP_PRO_EVENT_CALENDAR_BOOKING_CANCELED ?>"><?php echo __( 'Canceled', 'dpProEventCalendar' )?></option>
			    </select>
			</div>
		</div>
	    <div class="clear"></div>
    	
    	<hr>

    	<a class="button button-primary" href="javascript:void(0);" data-dppec-eventid="<?php echo $post->ID?>" id="pec_new_booking_submit"><?php _e('Send', 'dpProEventCalendar'); ?></a>

    	<a class="button" href="javascript:void(0);" id="pec_new_booking_cancel"><?php _e('Cancel', 'dpProEventCalendar'); ?></a>
    </div>

    <?php }

    $id_list = $post->ID;
    if(function_exists('icl_object_id')) 
    {
    
        global $sitepress;
		if( !empty($sitepress) ) 
		{
        
            $id_list_arr = array();
			$trid = $sitepress->get_element_trid( $post->ID, 'post_pec-events' );
			$translation = $sitepress->get_element_translations( $trid, 'post_pec-events' );

			foreach( $translation as $key ) 
			{
			
				$id_list_arr[] = $key->element_id;
			
			}

			if( !empty( $id_list_arr ) )
			{
			
				$id_list = implode( ",", $id_list_arr );
			
			}
		}
	}
	?>

    <p><?php _e('Add extra fields from the <a href="'.admin_url( 'admin.php?page=dpProEventCalendar-settings' ).'" target="_blank"> General settings</a>.','dpProEventCalendar'); ?></p>

    <div class="clear"></div>

    <select id="pec_booking_filter_date">
    	<option value=""><?php _e('All Event Dates', 'dpProEventCalendar'); ?></option>
    	<?php
    	$querystr = "
            SELECT DISTINCT event_date
            FROM " . DP_PRO_EVENT_CALENDAR_TABLE_BOOKING . "
			WHERE id_event IN (".$id_list.")
            ORDER BY event_date ASC
            ";
            $bookings_obj = $wpdb->get_results($querystr, OBJECT);
            foreach($bookings_obj as $booking) {?>
            	<option value="<?php echo $booking->event_date?>"><?php echo date_i18n(get_option('date_format'), strtotime($booking->event_date))?></option>
            <?php
        	}
            ?>
    </select>
    <a class="button" href="javascript:void(0);" data-dppec-eventid="<?php echo $post->ID?>" id="pec_booking_filter"><?php _e('Filter', 'dpProEventCalendar'); ?></a>
    <hr>
    
    <table class="widefat pec_booking_table" cellpadding="0" cellspacing="0" id="pec-sort-table">
        <thead>
            <tr style="cursor:default !important;">
                <th><?php _e('User Info','dpProEventCalendar'); ?></th>
                <th><?php _e('Booking Date','dpProEventCalendar'); ?></th>
                <th><?php _e('Event Date','dpProEventCalendar'); ?></th>
                <th><?php _e('Quantity','dpProEventCalendar'); ?></th>
                <th><?php _e('Comment','dpProEventCalendar'); ?> / <?php _e('Extra Fields','dpProEventCalendar'); ?></th>
                <th><?php _e('Status','dpProEventCalendar'); ?> 
                	<?php $pec_admin->show_info( __('If set in the calendar settings, the user will receive a notification when you cancel a booking.','dpProEventCalendar') ); ?>
                </th>
                <th></th>
             </tr>
        </thead>
        <tbody id="pec-booking-list">
            <?php
            

			$querystr = "
            SELECT COUNT(*) as count
            FROM " . DP_PRO_EVENT_CALENDAR_TABLE_BOOKING . "
			WHERE id_event IN (".$id_list.")";
            $counter = $wpdb->get_row($querystr, OBJECT);
			
			$booking_count = 0;
            $querystr = "
            SELECT *
            FROM " . DP_PRO_EVENT_CALENDAR_TABLE_BOOKING . "
			WHERE id_event IN (".$id_list.")
            ORDER BY id DESC
			LIMIT " . DP_PRO_EVENT_CALENDAR_ADMIN_BOOKINGS_LIST_LIMIT . "
            ";
            $bookings_obj = $wpdb->get_results($querystr, OBJECT);
            foreach($bookings_obj as $booking) {
				if(is_numeric($booking->id_user) && $booking->id_user > 0) {
					//$userdata = FALSE;
					//$userdata = WP_User::get_data_by( 'id', $booking->id_user );

					$userdata = get_userdata($booking->id_user);
				} else {
					$userdata = new stdClass();
					$userdata->display_name = $booking->name;
					$userdata->user_email = $booking->email;	
				}
                ?>
            <tr>
                <td width="200"><?php echo $userdata->display_name?> <br>
                	<?php if($userdata->user_email != "") {?>
                	<span class="dashicons dashicons-email-alt"></span><input type="text" readonly="readonly" name="pec_booking_email[<?php echo $booking->id?>]" class="pec_booking_text" value="<?php echo $userdata->user_email?>" /><br>
                	<?php }?>

                	<?php if($booking->phone != "") {?>
                	<span class="dashicons dashicons-phone"></span><input type="text" readonly="readonly" name="pec_booking_phone[<?php echo $booking->id?>]"  class="pec_booking_text" value="<?php echo $booking->phone?>" />
                	<?php }?>
                </td>
                <td><?php echo date_i18n(get_option('date_format') . ' '. get_option('time_format'), strtotime($booking->booking_date))?></td>
                <td><?php echo date_i18n(get_option('date_format'), strtotime($booking->event_date))?></td>
                <td><?php echo $booking->quantity?></td>
                <td>
                	<?php if($booking->comment != "") {?>
                	<span class="dashicons dashicons-admin-comments"></span> <?php echo nl2br($booking->comment)?> <hr>
                	<?php }?>

                	<?php
                	$extra_fields = unserialize($booking->extra_fields);
                	if(!is_array($extra_fields)) 
                		$extra_fields = array();

                	$html = '';
                	
                	foreach($extra_fields as $key=>$value) {

                		$field_index = array_keys($dpProEventCalendar['booking_custom_fields']['id'], str_replace('pec_custom_', '', $key));
                		
                		if(is_array($field_index)) {
	                		$field_index = $field_index[0];
	                	} else {
	                		$field_index = '';
	                	}

                		if($value != "" && is_numeric($field_index)) {
                			if($dpProEventCalendar['booking_custom_fields']['type'][$field_index] == 'checkbox') {
                				$value = __('Yes', 'dpProEventCalendar');
                			}
							$html .= '<div class="pec_event_page_custom_fields">
										<strong>'.$dpProEventCalendar['booking_custom_fields']['name'][$field_index].': </strong>'.$value;
							$html .= '</div>';
						}

                	}
                	
                	echo $html;
					?>
				</td>
                <td>
                	<select name="pec_booking_status[<?php echo $booking->id?>]" onchange="pec_change_booking_status('<?php echo $booking->status?>', this);">
						<option value="<?php echo DP_PRO_EVENT_CALENDAR_BOOKING_COMPLETED ?>"><?php echo __( 'Completed', 'dpProEventCalendar' )?></option>
                        <option value="<?php echo DP_PRO_EVENT_CALENDAR_BOOKING_PENDING ?>" <?php if($booking->status == DP_PRO_EVENT_CALENDAR_BOOKING_PENDING) {?> selected="selected" <?php }?>><?php echo __( 'Pending', 'dpProEventCalendar' )?></option>
                        <option value="<?php echo DP_PRO_EVENT_CALENDAR_BOOKING_CANCELED_BY_USER ?>" <?php if($booking->status == DP_PRO_EVENT_CALENDAR_BOOKING_CANCELED_BY_USER) {?> selected="selected" <?php }?>><?php echo __( 'Canceled By User', 'dpProEventCalendar' )?></option>
                        <option value="<?php echo DP_PRO_EVENT_CALENDAR_BOOKING_CANCELED ?>" <?php if($booking->status == DP_PRO_EVENT_CALENDAR_BOOKING_CANCELED) {?> selected="selected" <?php }?>><?php echo __( 'Canceled', 'dpProEventCalendar' )?></option>
                    </select>
                    <div class="pec_booking_cancel_reason_wrap" <?php if($booking->status == DP_PRO_EVENT_CALENDAR_BOOKING_CANCELED) {?> style="display:block;" <?php }?>>
	                    <input type="hidden" class="pec_booking_cancel_send_mail" name="pec_booking_cancel_send_mail[<?php echo $booking->id?>]" value="0" />

	                    <textarea class="pec_booking_cancel_reason" name="pec_booking_cancel_reason[<?php echo $booking->id?>]" placeholder="<?php echo __( 'Cancel Reason', 'dpProEventCalendar' )?>"><?php echo $booking->cancel_reason; ?></textarea>
                    </div>
                </td>
                <td><input type="button" value="<?php echo __( 'Delete', 'dpProEventCalendar' )?>" name="delete_booking" class="button-primary" onclick="if(confirm('<?php echo __( 'Are you sure that you want to remove this booking?', 'dpProEventCalendar' )?>')) { pec_removeBooking(<?php echo $booking->id?>, this); }" /></td>
            </tr>
            <?php 
				$booking_count++;
			}
			
			if($booking_count == 0) {
				echo '<tr id="pec_booking_list_zero"><td colspan="5"><p>'.__( 'No Booking Found.', 'dpProEventCalendar' ).'</p></td></tr>';	
			}?>
        </tbody>

        <tfoot>

            <tr style="cursor:default !important;">
                <th><?php _e('User Info','dpProEventCalendar'); ?></th>
                <th><?php _e('Booking Date','dpProEventCalendar'); ?></th>
                <th><?php _e('Event Date','dpProEventCalendar'); ?></th>
                <th><?php _e('Quantity','dpProEventCalendar'); ?></th>
                <th><?php _e('Comment','dpProEventCalendar'); ?> / <?php _e('Extra Fields','dpProEventCalendar'); ?></th>
                <th><?php _e('Status','dpProEventCalendar'); ?></th>
                <th></th>
             </tr>

        </tfoot>

    </table>
    <script type="text/javascript">
    	function pec_change_booking_status(original_status, new_status) {
    		jQuery(new_status).closest('td').find('.pec_booking_cancel_reason_wrap').hide();
    		jQuery(new_status).closest('td').find('.pec_booking_cancel_send_mail').val("0");

    		if(jQuery(new_status).val() == 'canceled') {
    			jQuery(new_status).closest('td').find('.pec_booking_cancel_reason_wrap').show();

    			if(original_status != 'canceled') {
	    			jQuery(new_status).closest('td').find('.pec_booking_cancel_send_mail').val("1");
	    		}

    		}
    	}
    </script>
    <?php 
	if( $counter->count > DP_PRO_EVENT_CALENDAR_ADMIN_BOOKINGS_LIST_LIMIT ) {?>
        <a class="button pec-load-more" data-dppec-offset="<?php echo DP_PRO_EVENT_CALENDAR_ADMIN_BOOKINGS_LIST_LIMIT ?>" data-dppec-eventid="<?php echo $post->ID?>" href="javascript:void(0);"><?php _e('Load More','dpProEventCalendar'); ?> (<span><?php echo ($counter->count - DP_PRO_EVENT_CALENDAR_ADMIN_BOOKINGS_LIST_LIMIT )?></span>)</a>
	<?php
	}
}



function dpProEventCalendar_events_side_display( $post ) 
{

	global $wpdb, $pec_admin;
	
	$values = get_post_custom( $post->ID );
	$pec_all_day = isset( $values['pec_all_day'] ) ? $values['pec_all_day'][0] : '0';
	$pec_tbc = isset( $values['pec_tbc'] ) ? $values['pec_tbc'][0] : '0';
	$pec_hide_time = isset( $values['pec_hide_time'] ) ? $values['pec_hide_time'][0] : '0';
	$pec_id_calendar = isset( $values['pec_id_calendar'] ) ? $values['pec_id_calendar'][0] : '';
	$pec_end_time_hh = isset( $values['pec_end_time_hh'] ) ? $values['pec_end_time_hh'][0] : '';
	$pec_end_time_mm = isset( $values['pec_end_time_mm'] ) ? $values['pec_end_time_mm'][0] : '';
	$pec_date = isset( $values['pec_date'] ) ? $values['pec_date'][0] : '';
	$pec_end_date = isset( $values['pec_end_date'] ) ? $values['pec_end_date'][0] : '';
	$pec_exceptions = isset( $values['pec_exceptions'] ) ? $values['pec_exceptions'][0] : '';
	$pec_extra_dates = isset( $values['pec_extra_dates'] ) ? $values['pec_extra_dates'][0] : '';
	$pec_recurring_frecuency = isset( $values['pec_recurring_frecuency'] ) ? $values['pec_recurring_frecuency'][0] : '0';
	$pec_timezone = isset( $values['pec_timezone'] ) ? $values['pec_timezone'][0] : '';
	$pec_daily_every = isset( $values['pec_daily_every'] ) ? $values['pec_daily_every'][0] : '1';
	$pec_daily_working_days = isset( $values['pec_daily_working_days'] ) ? $values['pec_daily_working_days'][0] : '0';
	$pec_weekly_day = isset( $values['pec_weekly_day'] ) ? unserialize($values['pec_weekly_day'][0]) : array();
	$pec_weekly_every = isset( $values['pec_weekly_every'] ) ? $values['pec_weekly_every'][0] : '1';
	$pec_monthly_every = isset( $values['pec_monthly_every'] ) ? $values['pec_monthly_every'][0] : '1';
	$pec_monthly_position = isset( $values['pec_monthly_position'] ) ? $values['pec_monthly_position'][0] : '';
	$pec_monthly_day = isset( $values['pec_monthly_day'] ) ? $values['pec_monthly_day'][0] : '';
	
	if(!is_array($pec_weekly_day)) $pec_weekly_day = array();

	if( $pec_id_calendar == '' && $pec_admin->get( 'pec-calendar' ) )
		$pec_id_calendar = $pec_admin->get( 'pec-calendar' );
	
	do_action( 'pec_enqueue_admin', 1);
		
	wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
	?>
    <div id="misc-publishing-actions">
        <div class="misc-pub-section">
            <label for="pec_id_calendar"><?php _e('Calendar', 'dpProEventCalendar'); ?>

            	<?php $pec_admin->show_info( __('Assign this event to one or more calendars. Select multiple calendars pressing "ctrl".','dpProEventCalendar') ); ?>

            </label>
            <div>
	            <input type="hidden" name="pec_id_calendar" id="pec_id_calendar" value="<?php echo $pec_id_calendar?>" />
	            <select name="pec_id_calendar_tmp[]" id="pec_id_calendar_tmp" multiple="multiple" style="width:100%;" onchange="pec_update_cal_list(this);">
	                <?php
					$count = 0;
	                $querystr = "
	                SELECT *
	                FROM " . DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS . "
	                ORDER BY title ASC
	                ";
	                $calendars_obj = $wpdb->get_results($querystr, OBJECT);
	                foreach( $calendars_obj as $calendar ) {
	                ?>
	                    <option value="<?php echo $calendar->id?>" <?php if((in_array($calendar->id, explode(',', $pec_id_calendar))) || (empty($pec_id_calendar) && count($calendars_obj) == 1 && $count == 0)) { ?> selected="selected"<?php }?>><?php echo $calendar->title?></option>
	                <?php 
						$count++;
					}?>
	            </select>
	            <script type="text/javascript">
	            function pec_update_cal_list(el) {
					var option_all = jQuery("#pec_id_calendar_tmp option:selected").map(function () {
						return jQuery(this).val();
					}).get().join(',');
					
					jQuery('#pec_id_calendar').val(option_all);
					
				}
				<?php if( empty($pec_id_calendar) && count($calendars_obj) == 1 ) {?>
				pec_update_cal_list(jQuery('#pec_id_calendar_tmp'));
				<?php }?>
	            </script>
	        </div>
        </div>
        <div class="misc-pub-section">
            <label for="pec_date" class="pec_label_head"><?php _e('Date', 'dpProEventCalendar'); ?> <span class="pec_date_format">Y-m-d</span></label><br />
            <div>
	            <input type="number" min="1" max="9999" maxlength="4" name="pec_date_y" style="max-width: 70px;" id="pec_date_y" value="<?php echo $pec_date != '' ? date("Y", strtotime($pec_date)) : current_time('Y')?>" placeholder="----" />
	            <input type="number" min="1" max="12" maxlength="2" name="pec_date_m" style="max-width: 50px;" id="pec_date_m" value="<?php echo $pec_date != '' ? date("m", strtotime($pec_date)) : current_time('m')?>" placeholder="--" />
	            <input type="number" min="1" max="31" maxlength="2" name="pec_date_d" style="max-width: 50px;" id="pec_date_d" value="<?php echo $pec_date != '' ? date("d", strtotime($pec_date)) : current_time('d')?>" placeholder="--" /><br />

	            <div class="dp_pec_clear"></div>
	        </div>
        </div>
        <div class="misc-pub-section misc-pub-tbc">
        	<div>
	            <input type="checkbox" name="pec_tbc" id="pec_tbc" value="1" <?php echo ($pec_tbc ? 'checked="checked"' : ''); ?> />
	            <label for="pec_tbc">
	            	<?php _e('To be confirmed', 'dpProEventCalendar'); ?>
	            	<?php $pec_admin->show_info( __('The date won\'t be displayed to users, but you would need to set a start date to show the event in the upcoming events listings.','dpProEventCalendar') ); ?>
	            	
	            </label>
	        </div>
            
        </div>
        <div class="misc-pub-section misc-pub-start-time">
            <label for="pec_time_hours" class="pec_label_head"><?php _e('Start Time', 'dpProEventCalendar'); ?></label>
            <div>
	            <select name="pec_time_hours" id="pec_time_hours" style="width:80px;">
	                <?php for($i = 0; $i <= 23; $i++) {
						if(strpos(get_option('time_format'), "A")  !== false || strpos(get_option('time_format'), "a")  !== false) {
							$hour = str_pad(($i > 12 ? $i - 12 : ($i == '00' ? '12' : $i)), 2, "0", STR_PAD_LEFT). ' '.date('A', mktime($i, 0));
						} else {
							$hour = str_pad($i, 2, "0", STR_PAD_LEFT);
						}
						?>
	                    <option value="<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>" <?php if(date("H", strtotime($pec_date)) == str_pad($i, 2, "0", STR_PAD_LEFT)) {?> selected="selected" <?php }?>><?php echo $hour?></option>
	                <?php }?>
	            </select>
	            <select name="pec_time_minutes" id="pec_time_minutes" style="width:50px;">
	                <?php for($i = 0; $i <= 59; $i += 15) {?>
	                    <option value="<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>" <?php if(date("i", strtotime($pec_date)) == str_pad($i, 2, "0", STR_PAD_LEFT)) {?> selected="selected" <?php }?>><?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?></option>
	                <?php }?>
	            </select>
	            &nbsp; <input type="checkbox" name="pec_hide_time" class="checkbox" id="pec_hide_time" value="1" <?php if($pec_hide_time) {?> checked="checked" <?php }?> /> <?php _e('Hide Time','dpProEventCalendar'); ?>
	            <div class="dp_pec_clear"></div>
	        </div>
        </div>
        <div class="misc-pub-section misc-pub-end-time">
            <label for="pec_end_time_hh" class="pec_label_head"><?php _e('End Time', 'dpProEventCalendar'); ?></label>
            <div>
	            <select name="pec_end_time_hh" id="pec_end_time_hh" style="width:80px;">
	            	<option value="">--</option>
	                <?php for($i = 0; $i <= 23; $i++) {
						if(strpos(get_option('time_format'), "A")  !== false || strpos(get_option('time_format'), "a")  !== false) {
							$hour = str_pad(($i > 12 ? $i - 12 : ($i == '00' ? '12' : $i)), 2, "0", STR_PAD_LEFT). ' '.date('A', mktime($i, 0));
						} else {
							$hour = str_pad($i, 2, "0", STR_PAD_LEFT);
						}
						?>
	                    <option value="<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>" <?php if($pec_end_time_hh != "" & str_pad($pec_end_time_hh, 2, "0", STR_PAD_LEFT) == str_pad($i, 2, "0", STR_PAD_LEFT)) {?> selected="selected" <?php }?>><?php echo $hour?></option>
	                <?php }?>
	            </select>
	            <select name="pec_end_time_mm" id="pec_end_time_mm" style="width:50px;">
	            	<option value="">--</option>
	                <?php for($i = 0; $i <= 59; $i += 15) {?>
	                    <option value="<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>" <?php if($pec_end_time_mm != "" & str_pad($pec_end_time_mm, 2, "0", STR_PAD_LEFT) == str_pad($i, 2, "0", STR_PAD_LEFT)) {?> selected="selected" <?php }?>><?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?></option>
	                <?php }?>
	            </select>
	            <div class="dp_pec_clear"></div>
	        </div>
        </div>

        <div class="misc-pub-section misc-pub-timezone">
        	<label for="pec_timezone" class="pec_label_head"><?php _e('Select a Timezone', 'dpProEventCalendar'); ?></label>
        	<div>
	            <select name="pec_timezone" id="pec_timezone">

	            	<?php //if($pec_timezone == "") {
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

	            		//$pec_timezone = $tzstring;
	            	//}
					if($pec_timezone != "") {
	            	?>
	            	<option value=""><?php _e('Default', 'dpProEventCalendar'); ?> (<?php echo $tzstring?>)</option>
	            	<?php }?>
	                <?php echo wp_timezone_choice($pec_timezone); ?>
	            </select>
	            <div class="dp_pec_clear"></div>
	        </div>
        </div>
        
        <div class="misc-pub-section misc-pub-all-day">
            <input type="checkbox" name="pec_all_day" id="pec_all_day" value="1" <?php echo ($pec_all_day ? 'checked="checked"' : ''); ?> />
            <div>
	            <label for="pec_all_day"><?php _e('Set if the event is all the day.', 'dpProEventCalendar'); ?></label>
	        </div>
        </div>
        <div class="misc-pub-section misc-pub-frequency">
        	<label for="pec_recurring_frecuency" class="pec_label_head"><?php _e('Select a frequency', 'dpProEventCalendar'); ?></label>
        	<div>
	            <select name="pec_recurring_frecuency" id="pec_recurring_frecuency" onchange="pec_update_frequency(this.value);">
	                <option value="0" <?php if($pec_recurring_frecuency == 0) {?> selected="selected" <?php }?>><?php _e('None','dpProEventCalendar'); ?></option>
	                <option value="1" <?php if($pec_recurring_frecuency == 1) {?> selected="selected" <?php }?>><?php _e('Daily','dpProEventCalendar'); ?></option>
	                <option value="2" <?php if($pec_recurring_frecuency == 2) {?> selected="selected" <?php }?>><?php _e('Weekly','dpProEventCalendar'); ?></option>
	                <option value="3" <?php if($pec_recurring_frecuency == 3) {?> selected="selected" <?php }?>><?php _e('Monthly','dpProEventCalendar'); ?></option>
	                <option value="4" <?php if($pec_recurring_frecuency == 4) {?> selected="selected" <?php }?>><?php _e('Yearly','dpProEventCalendar'); ?></option>
	            </select>
	            <div class="dp_pec_clear"></div>
	        </div>
        </div>
        
        <div class="misc-pub-section pec_daily_frequency" style="display:none;">
			<div id="pec_daily_every_div"><?php _e('Every','dpProEventCalendar'); ?> <input type="number" min="1" max="99" style="width:50px;" maxlength="2" name="pec_daily_every" id="pec_daily_every" value="<?php echo $pec_daily_every?>" /> <?php _e('day(s)','dpProEventCalendar'); ?></div>
            <div id="pec_daily_working_days_div"><input type="checkbox" name="pec_daily_working_days" id="pec_daily_working_days" onclick="pec_check_daily_working_days(this);" <?php if($pec_daily_working_days == 1) {?> checked="checked"<?php }?> value="1" /><?php _e('All working days','dpProEventCalendar'); ?></div>
        </div>
        <div class="misc-pub-section pec_weekly_frequency" style="display:none;">
			<?php _e('Repeat every','dpProEventCalendar'); ?> <input type="number" min="1" max="99" style="width:50px;" maxlength="2" name="pec_weekly_every" value="<?php echo $pec_weekly_every?>" /> <?php _e('week(s) on:','dpProEventCalendar'); ?>
            <br /><br />
            <input type="checkbox" value="1" name="pec_weekly_day[]" <?php if(in_array(1, $pec_weekly_day)) {?> checked="checked" <?php }?> /> &nbsp; <?php _e('Mon','dpProEventCalendar'); ?><br />
            <input type="checkbox" value="2" name="pec_weekly_day[]" <?php if(in_array(2, $pec_weekly_day)) {?> checked="checked" <?php }?> /> &nbsp; <?php _e('Tue','dpProEventCalendar'); ?><br />
            <input type="checkbox" value="3" name="pec_weekly_day[]" <?php if(in_array(3, $pec_weekly_day)) {?> checked="checked" <?php }?> /> &nbsp; <?php _e('Wed','dpProEventCalendar'); ?><br />
            <input type="checkbox" value="4" name="pec_weekly_day[]" <?php if(in_array(4, $pec_weekly_day)) {?> checked="checked" <?php }?> /> &nbsp; <?php _e('Thu','dpProEventCalendar'); ?><br />
            <input type="checkbox" value="5" name="pec_weekly_day[]" <?php if(in_array(5, $pec_weekly_day)) {?> checked="checked" <?php }?> /> &nbsp; <?php _e('Fri','dpProEventCalendar'); ?><br />
            <input type="checkbox" value="6" name="pec_weekly_day[]" <?php if(in_array(6, $pec_weekly_day)) {?> checked="checked" <?php }?> /> &nbsp; <?php _e('Sat','dpProEventCalendar'); ?><br />
            <input type="checkbox" value="7" name="pec_weekly_day[]" <?php if(in_array(7, $pec_weekly_day)) {?> checked="checked" <?php }?> /> &nbsp; <?php _e('Sun','dpProEventCalendar'); ?>
        </div>
        <div class="misc-pub-section pec_monthly_frequency" style="display:none;">
			<?php _e('Repeat every','dpProEventCalendar'); ?> <input type="number" min="1" max="99" style="width:50px;" maxlength="2" name="pec_monthly_every" value="<?php echo $pec_monthly_every?>" /> <?php _e('month(s) on:','dpProEventCalendar'); ?>
            <br /><br />
            <select name="pec_monthly_position" id="pec_monthly_position" style="width:90px;">
	            <option value=""><?php _e('','dpProEventCalendar'); ?></option>
                <option value="first" <?php if($pec_monthly_position == 'first') {?> selected="selected" <?php }?>><?php _e('First','dpProEventCalendar'); ?></option>
                <option value="second" <?php if($pec_monthly_position == 'second') {?> selected="selected" <?php }?>><?php _e('Second','dpProEventCalendar'); ?></option>
                <option value="third" <?php if($pec_monthly_position == 'third') {?> selected="selected" <?php }?>><?php _e('Third','dpProEventCalendar'); ?></option>
                <option value="fourth" <?php if($pec_monthly_position == 'fourth') {?> selected="selected" <?php }?>><?php _e('Fourth','dpProEventCalendar'); ?></option>
                <option value="last" <?php if($pec_monthly_position == 'last') {?> selected="selected" <?php }?>><?php _e('Last','dpProEventCalendar'); ?></option>
            </select>
            
            <select name="pec_monthly_day" id="pec_monthly_day" style="width:150px;">
            <option value=""><?php _e('','dpProEventCalendar'); ?></option>
	            <option value="monday" <?php if($pec_monthly_day == 'monday') {?> selected="selected" <?php }?>><?php _e('Monday','dpProEventCalendar'); ?></option>
                <option value="tuesday" <?php if($pec_monthly_day == 'tuesday') {?> selected="selected" <?php }?>><?php _e('Tuesday','dpProEventCalendar'); ?></option>
                <option value="wednesday" <?php if($pec_monthly_day == 'wednesday') {?> selected="selected" <?php }?>><?php _e('Wednesday','dpProEventCalendar'); ?></option>
                <option value="thursday" <?php if($pec_monthly_day == 'thursday') {?> selected="selected" <?php }?>><?php _e('Thursday','dpProEventCalendar'); ?></option>
                <option value="friday" <?php if($pec_monthly_day == 'friday') {?> selected="selected" <?php }?>><?php _e('Friday','dpProEventCalendar'); ?></option>
                <option value="saturday" <?php if($pec_monthly_day == 'saturday') {?> selected="selected" <?php }?>><?php _e('Saturday','dpProEventCalendar'); ?></option>
                <option value="sunday" <?php if($pec_monthly_day == 'sunday') {?> selected="selected" <?php }?>><?php _e('Sunday','dpProEventCalendar'); ?></option>
            </select>
            <div class="dp_pec_clear"></div>
        </div>
        
        <div class="misc-pub-section pec_frequency_options" style="display:none;">
			<label for="pec_exceptions"><?php _e('Exceptions','dpProEventCalendar'); ?></label> <input type="text" name="pec_exceptions" id="pec_exceptions" value="<?php echo $pec_exceptions?>" />
            <label class="dp_ui_pec_content_desc"><?php _e('Add dates to exclude from the recurring event frequency. Format YYYY-MM-DD. i.e: ','dpProEventCalendar'); ?><?php echo date('Y')?>-12-24,<?php echo date('Y')?>-12-25,<?php echo date('Y')?>-12-31</label>
        </div>
        
        <div class="misc-pub-section">
            <label for="pec_end_date" class="pec_label_head"><?php _e('End Date', 'dpProEventCalendar'); ?> <span class="pec_date_format">Y-m-d</span></label><br />

            <input type="number" min="<?php echo current_time('Y') - 2?>" max="9999" maxlength="4" name="pec_end_date_y" style="max-width: 70px;" id="pec_end_date_y" value="<?php echo $pec_end_date != '' ? date("Y", strtotime($pec_end_date)) : ''?>" placeholder="----" />
            <input type="number" min="1" max="12" maxlength="2" name="pec_end_date_m" style="max-width: 50px;" id="pec_end_date_m" value="<?php echo $pec_end_date != '' ? date("m", strtotime($pec_end_date)) : ''?>" placeholder="--" />
            <input type="number" min="1" max="31" maxlength="2" name="pec_end_date_d" style="max-width: 50px;" id="pec_end_date_d" value="<?php echo $pec_end_date != '' ? date("d", strtotime($pec_end_date)) : ''?>" placeholder="--" /><br />
            

            <label class="dp_ui_pec_content_desc"><?php _e('Select the end date. A frequency option must be selected.','dpProEventCalendar'); ?></label>
            <div class="dp_pec_clear"></div>
        </div>
        
        <div class="misc-pub-section">
			<label for="pec_extra_dates" class="pec_label_head"><?php _e('Extra Dates','dpProEventCalendar'); ?></label> <input type="text" name="pec_extra_dates" id="pec_extra_dates" value="<?php echo $pec_extra_dates?>" />
            <label class="dp_ui_pec_content_desc"><?php _e('Add extra dates to include in the calendar. Format YYYY-MM-DD. i.e: ','dpProEventCalendar'); ?><?php echo date('Y')?>-12-24,<?php echo date('Y')?>-12-25,<?php echo date('Y')?>-12-31</label>
        </div>
    </div>
    <script type="text/javascript">
    	jQuery('#pec_timezone option[value=""]').text("<?php _e('Default', 'dpProEventCalendar'); ?> (<?php echo $tzstring?>)");

		function pec_check_daily_working_days(chk) {

			if(jQuery(chk).is(':checked')) {

				jQuery('#pec_daily_every_div').hide();

			} else {

				jQuery('#pec_daily_every_div').show();

			}

		}
		
		function pec_update_frequency(val) {

			jQuery('.pec_frequency_options').hide();
			jQuery('.pec_daily_frequency').hide();
			jQuery('.pec_weekly_frequency').hide();
			jQuery('.pec_monthly_frequency').hide();
			
			switch(val) {
				case "1":
					jQuery('.pec_daily_frequency').show();
					jQuery('.pec_weekly_frequency').hide();
					jQuery('.pec_monthly_frequency').hide();
					jQuery('.pec_frequency_options').show();
					break;	
				case "2":
					jQuery('.pec_daily_frequency').hide();
					jQuery('.pec_weekly_frequency').show();
					jQuery('.pec_monthly_frequency').hide();
					jQuery('.pec_frequency_options').show();
					break;	
				case "3":
					jQuery('.pec_daily_frequency').hide();
					jQuery('.pec_weekly_frequency').hide();
					jQuery('.pec_monthly_frequency').show();
					jQuery('.pec_frequency_options').show();
					break;	
				case "4":
					jQuery('.pec_daily_frequency').hide();
					jQuery('.pec_weekly_frequency').hide();
					jQuery('.pec_monthly_frequency').hide();
					jQuery('.pec_frequency_options').show();
					break;	
			}
		}
		pec_update_frequency("<?php echo $pec_recurring_frecuency?>");
		pec_check_daily_working_days(jQuery('#pec_daily_working_days'));
	</script>

	<?php
}

