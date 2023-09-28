<?php

//Include Configuration
require_once (dirname (__FILE__) . '/../../../../wp-load.php');

if(!isset($_GET['i']) || strlen($_GET['i']) > 11 || !is_numeric($_GET['d']) || $_GET['d'] <= 0) 
	die(); 


$event_id = $_GET['i'];
$date = $_GET['d'];

//Redirect to new iCal Feed URL

wp_redirect( site_url( '?' . DP_PRO_EVENT_CALENDAR_ICAL_EVENT . '=' . $event_id . '&d=' . $date ) );
die();
