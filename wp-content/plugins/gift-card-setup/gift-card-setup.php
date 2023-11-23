<?php
/*
Plugin Name: Gift Card Setup
Plugin URI:https://wordpress.org/plugins/gift-card-setup
Description: Gift Card Setup
Version: 1.0
Author: Oleksandr
Author URI:https://example.com/gift-card-setup
Text Domain: gift-card-setup
License: GPL2
*/

/*
Copyright 2023-2023 Oleksandr.P  (email: )
*/
require __DIR__ ."/../../../db_config.php";

define('GIFT_CARD_SETUP_VAR', '1.0.0');
define('GIFT_CARD_SETUP_NAME', 'gift-card-setup');
define('GIFT_CARD_SETUP_PLUGIN_DIR',plugin_dir_url( __FILE__ ));

add_action( 'wp', 'add_jqwidgets' );

require dirname( __FILE__ ) .'/gift_card_setup_ajax.php';

function gift_card_setup_content(){
    require __DIR__ ."/gift_card_setup_content.php";
}

add_shortcode('gift_card_setup', 'gift_card_setup_content');
?>