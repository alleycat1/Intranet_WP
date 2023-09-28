<?php

//Include Configuration
require_once (dirname (__FILE__) . '/../../../../wp-load.php');

if( ! is_numeric( $_GET['calendar_id'] ) && ! is_numeric( $_GET['c'] ) ) 
	die(); 


$calendar_id = ( isset( $_GET['calendar_id'] ) ? $_GET['calendar_id'] : $_GET['c'] );

//Redirect to new iCal Feed URL

wp_redirect( site_url( '?' . DP_PRO_EVENT_CALENDAR_ICAL . '=' . $calendar_id . ( isset( $_GET['all'] ) ? '&all=1' : '' ) . ( isset($_GET['cat'] ) && is_numeric($_GET['cat'] ) ? '&cat=' . $_GET['cat'] : '' ) ) );
die();