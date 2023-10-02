<?php 

if ( ! function_exists('wta_test_action') ) {
	function wta_test_action(){
		if(isset($_POST) && !empty($_POST)){
            //echo "1";
		}
        echo "2";
        die();
	}
    add_action('wp_ajax_wta_test_action', 'wta_test_action');
}
?>