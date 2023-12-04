<?php
/*
Plugin Name: Gift Cards
Plugin URI:https://wordpress.org/plugins/gift-cards
Description: show and select Gift Cards
Version: 1.0
Author: Oleksandr
Author URI:https://example.com/gift-cards
Text Domain: gift-cards
License: GPL2
*/

/*
Copyright 2023-2023 Oleksandr.P  (email: )
*/
require __DIR__ ."/../../../db_config.php";

define('GIFT_CARDS_VAR', '1.0.0');
define('GIFT_CARDS_NAME', 'gift-cards');
define('GIFT_CARDS_PLUGIN_DIR',plugin_dir_url( __FILE__ ));

require dirname( __FILE__ ) .'/gift_cards_ajax.php';

function gift_cards_content(){
    require __DIR__ ."/gift_cards_content.php";
}

add_shortcode('gift_cards', 'gift_cards_content');
?>