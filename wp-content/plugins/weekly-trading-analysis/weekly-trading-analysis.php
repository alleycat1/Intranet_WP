<?php
/*
Plugin Name: Weekly Trading Analysis
Plugin URI:https://wordpress.org/plugins/weekly-trading-analysis
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

define('WTA_VAR', '1.0.0');
define('WTA_NAME', 'weekly-trading-analysis');
define('WTA_PLUGIN_DIR',plugin_dir_url( __FILE__ ));

function add_jqwidgets() {
	// registers jQWidgets JavaScript files
	wp_register_script( 'jqxcore', get_template_directory_uri() . '/js/jqxcore.js', array( 'jquery' ), '3.0.4', false );
	wp_register_script( 'jqxdatetimeinput', get_template_directory_uri() . '/js/jqxdatetimeinput.js', array( 'jquery' ), '3.0.4', false );
	wp_register_script( 'jqxcalendar', get_template_directory_uri() . '/js/jqxcalendar.js', array( 'jquery' ), '3.0.4', false );
	wp_register_script( 'jqxtooltip', get_template_directory_uri() . '/js/jqxtooltip.js', array( 'jquery' ), '3.0.4', false );
	wp_register_script( 'jqxbuttons', get_template_directory_uri() . '/js/jqxbuttons.js', array( 'jquery' ), '3.0.4', false );
	wp_register_script( 'jqxmenu', get_template_directory_uri() . '/js/jqxmenu.js', array( 'jquery' ), '3.0.4', false );
    wp_register_script( 'jqxdata', get_template_directory_uri() . '/js/jqxdata.js', array( 'jquery' ), '3.0.4', false ); 
    wp_register_script( 'jqxscrollbar', get_template_directory_uri() . '/js/jqxscrollbar.js', array( 'jquery' ), '3.0.4', false );
    wp_register_script( 'jqxgrid', get_template_directory_uri() . '/js/jqxgrid.js', array( 'jquery' ), '3.0.4', false );
    wp_register_script( 'jqxgrid.edit', get_template_directory_uri() . '/js/jqxgrid.edit.js', array( 'jquery' ), '3.0.4', false );  
    wp_register_script( 'jqxgrid.columnsresize', get_template_directory_uri() . '/js/jqxgrid.columnsresize.js', array( 'jquery' ), '3.0.4', false );
    wp_register_script( 'jqxgrid.sort', get_template_directory_uri() . '/js/jqxgrid.sort.js', array( 'jquery' ), '3.0.4', false );  
    wp_register_script( 'jqxgrid.selection', get_template_directory_uri() . '/js/jqxgrid.selection.js', array( 'jquery' ), '3.0.4', false ); 
    wp_register_script( 'jqxlistbox', get_template_directory_uri() . '/js/jqxlistbox.js', array( 'jquery' ), '3.0.4', false );
    wp_register_script( 'jqxdropdownlist', get_template_directory_uri() . '/js/jqxdropdownlist.js', array( 'jquery' ), '3.0.4', false );
    wp_register_script( 'jqxcheckbox', get_template_directory_uri() . '/js/jqxcheckbox.js', array( 'jquery' ), '3.0.4', false );
    wp_register_script( 'jqxnumberinput', get_template_directory_uri() . '/js/jqxnumberinput.js', array( 'jquery' ), '3.0.4', false );
    wp_register_script( 'jqxsplitter', get_template_directory_uri() . '/js/jqxsplitter.js', array( 'jquery' ), '3.0.4', false );
    wp_register_script( 'jqxdata.export', get_template_directory_uri() . '/js/jqxdata.export.js', array( 'jquery' ), '3.0.4', false ); 
    wp_register_script( 'jqxgrid.export', get_template_directory_uri() . '/js/jqxgrid.export.js', array( 'jquery' ), '3.0.4', false ); 
    wp_register_script( 'jqxcombobox', get_template_directory_uri() . '/js/jqxcombobox.js', array( 'jquery' ), '3.0.4', false ); 
    wp_register_script( 'jqxpopover', get_template_directory_uri() . '/js/jqxpopover.js', array( 'jquery' ), '3.0.4', false ); 
    
	// register jQWidgets CSS files
	wp_register_style( 'jqx.base', get_template_directory_uri() . '/css/jqx.base.css', array(), '3.0.4', 'all' );
	wp_register_style( 'jqx.orange', get_template_directory_uri() . '/css/jqx.orange.css', array(), '3.0.4', 'all' );
}
add_action( 'wp', 'add_jqwidgets' );

require dirname( __FILE__ ) .'/wta_ajax.php';

function weekly_trading_analysis_table(){
    require __DIR__ ."/wta.php";
}

add_shortcode('weekly_trading_analysis', 'weekly_trading_analysis_table');
?>