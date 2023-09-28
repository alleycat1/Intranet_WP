<?php

//OLD RSS Feed URL

//Include Configuration
require_once ( dirname (__FILE__) . '/../../../../wp-load.php' );

if( ! is_numeric( $_GET['calendar_id'] ) || $_GET['calendar_id'] <= 0 ) 
	die(); 

$calendar_id = $_GET['calendar_id'];

//Redirect to new RSS Feed URL

wp_redirect( site_url( '?' . DP_PRO_EVENT_CALENDAR_RSS . '=' . $calendar_id . ( isset( $_GET['all'] ) ? '&all=1' : '' ) ) );
die();