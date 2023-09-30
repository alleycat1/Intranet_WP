<?php
/*
Plugin Name: Weekly Trading Analysis
Plugin URI: https://example.com/weekly-trading-analysis
Description: show and change weekly trading data
Version: 1.0
Author: Oleksandr
Author URI:https://example.com/weekly-trading-analysis
Text Domain: weekly-trading-analysis
License: GPL2
*/

/*
Copyright 2023-20237 Oleksandr.P  (email: )
*/
require __DIR__ ."/../../../db_config.php";

function weekly_trading_analysis_table(){
    global $serverName;
    global $connectionInfo;

    $conn = sqlsrv_connect($serverName, $connectionInfo);

    if($conn)
    {
        echo "asdf";
    }
    else
        print_r(sqlsrv_errors());
    
    return "Hello, weekly trading";
}

add_shortcode('weekly_trading_analysis', 'weekly_trading_analysis_table');
?>