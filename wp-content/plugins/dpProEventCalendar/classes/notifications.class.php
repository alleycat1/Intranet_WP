<?php

/*
 * DP Pro Event Calendar
 *
 * Copyright 2012, Diego Pereyra
 *
 * @Web: https://www.wpsleek.com
 * @Email: dpereyra90@gmail.com
 *
 * Notification Messages for all the layouts
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DPPEC_Notifications extends DpProEventCalendar {
	
    /**
     * Constructor
     * 
     * @return void
     */
	function __construct( ) 
	{
		// Do nothing
    }

    /**
     * Display a notification message
     * 
     * @param $type <string>
     * @param $message <string>
     * @param $visible <bool>
     * @param $extra_class <array>
     * @param $with_icon <bool>
     * @param $close <bool>
     * @return <string> Message's HTML code
     */
    public function message ( $type, $message, $visible = false, $extra_class = array(), $with_icon = true, $close = true )
    {

    	if(!is_array($extra_class))
    		$extra_class = array();

    	if($with_icon)
    		$extra_class[] = 'dp_pec_notification_with_icon';

    	if($visible)
    		$extra_class[] = 'dp_pec_notification_visible';

    	switch($type) 
    	{

    		case 'success':

    			$class_name = 'dp_pec_notification_event_succesfull';
    			$icon = 'fas fa-calendar-check';
    			break;

    		case 'error':

    			$class_name = 'dp_pec_notification_event_error';
    			$icon = 'fas fa-calendar-times';
    			break;

    		case 'warning':

    			$class_name = 'dp_pec_notification_event_warning';
    			$icon = 'fas fa-exclamation-circle';
    			break;

    	}

    	$html = '<div class="dp_pec_notification_event '.$class_name.' '.esc_attr(implode(" ", $extra_class)).'">';

    	if($with_icon) {

    		$html .= '<div class="dp_pec_notification_icon"><i class="'.$icon.'"></i></div>';

    	}
		
		$html .= $message;

		if($close && !$visible) {

			$html .= '<a href="#" class="dp_pec_notification_close" title=""><i class="fas fa-times-circle"></i></a>';

		}
				
		$html .= '</div>';

		return $html;


    }
	
}
?>