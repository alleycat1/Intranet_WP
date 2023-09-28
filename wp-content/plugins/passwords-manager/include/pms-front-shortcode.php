<?php


// hooks your functions into the correct filters
function pwdms_add_mce() {

	// check user permissions
	if ( !current_user_can( 'edit_posts' ) &&  !current_user_can( 'edit_pages' ) ) {
		return;
	}
	// check if WYSIWYG is enabled
	if ( 'true' == get_user_option( 'rich_editing' ) ) {
		add_filter( 'mce_external_plugins', 'pwdms_add_tinymce_plugin' );
		add_filter( 'mce_buttons', 'pwdms_register_mce_button' );
	}
}
add_action('admin_head', 'pwdms_add_mce');

// register shortcode button in the editor
function pwdms_register_mce_button( $buttons ) {
	array_push( $buttons,  "pwdms_shrtcd" ); 
	return $buttons;
}

// declare a script for the Shortcode button
// the script will insert the shortcode on the click event
function pwdms_add_tinymce_plugin( $plugin_array ) {
	$plugin_array['pmsinsertshortcode'] = PWDMS_ASSETS .'js/pms-shortcode.js';
	return $plugin_array;
}


add_shortcode('pms_pass','pms_front_pass_table');
// function that runs when shortcode is called
function pms_front_pass_table($message) { 
	static $count = 0;
	if(isset($message['cat_name'])){
		$cate_name = $message['cat_name'];
	}     
	$count++;	
	// Password table header  
	$table_header = array('No.','Name','Email','Password','Url','Category','Action'); 
	//Front html
	$message = '';
	$message .= '<div id="cname_'.absint($count).'" class="cname">'.esc_html($cate_name).'</div><div class="table-responsive pwdms_table_responsive" id="table-responsive" style="max-width:100%;overflow: auto;">';
	$message .=     '<table id="front_pass_table_'.absint($count).'" class="table table-borderless table-striped pms_data_table front_table">';
	$message .=        '<thead>';
	$message .=           '<tr>';
	foreach($table_header as $header){
		$message .= '<th class="bg-primary text-white">'.esc_html($header).'</th>';
	}
	$message .=            '</tr>';
	$message .=          '</thead>';
	$message .=      '</table>';
	$message .= '</div>';
	// Output needs to be return
	return $message;
}
?>