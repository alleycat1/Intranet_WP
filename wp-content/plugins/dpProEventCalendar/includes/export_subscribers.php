<?php

//Include Configuration
require_once (dirname (__FILE__) . '/../../../../wp-load.php');

global $dpProEventCalendar, $wpdb, $table_prefix;

if(!is_user_logged_in()) 
{

	die();	

}

if(!current_user_can('edit_others_posts') || !is_numeric($_GET['calendar_id']) || $_GET['calendar_id'] <= 0) 
{ 

	die(); 

}

$calendar_id = $_GET['calendar_id'];

header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=subscribers_".$calendar_id.".csv");

$booking_count = 0;
$querystr = "
SELECT *
FROM " .DP_PRO_EVENT_CALENDAR_TABLE_SUBSCRIBERS_CALENDAR . "
WHERE calendar = %d
ORDER BY subscription_date ASC";

$subscriber_email = array();
$subscribers_obj = $wpdb->get_results($wpdb->prepare($querystr, $calendar_id), OBJECT);

foreach($subscribers_obj as $subscriber) 
{

	$subscriber_email[] = $subscriber->email;

}

echo implode(",", $subscriber_email);
?>