<?php
//PMS Settings

if ( ! function_exists('pms_save_setting') ) {
	function pms_save_setting(){
		if(isset($_POST) && !empty($_POST)){
		extract($_POST);
			$btn_action		=	sanitize_text_field($btn_action);
			if(isset($btn_action) && !empty($btn_action) && wp_verify_nonce( sanitize_text_field($security_nonce), 'security_nonce' ) && is_user_logged_in() && is_admin()){  
				global $wpdb;			
				/*
				**Add new encryption password key detail in database
				*/		
				if($btn_action == 'Save'){				
					$pwd= sanitize_text_field($setting_key);
					if(isset($pwd) && !empty($pwd)){
						update_option('pms_encrypt_key',$pwd);
						//execute query
						if(esc_html(get_option('pms_encrypt_key',true)) == true){
							$requ['requ'] = $pwd;
						}
						else{			
							$requ['requ'] = "error";	
						}
					}
					else{
						$requ['requ'] = "error";	
					}
					echo json_encode($requ);
					die;
				}
			}
		}
	}
	add_action('wp_ajax_pms_save_setting', 'pms_save_setting');
}

add_action('wp_ajax_pms_send_email_help','pms_send_email_help');
function pms_send_email_help(){
	if(isset($_POST) && !empty($_POST)){
		extract($_POST);
		$btn_action = sanitize_text_field($btn_action);
		if(isset($btn_action) && !empty($btn_action)){
			if($btn_action == 'send_email' && wp_verify_nonce(sanitize_text_field($security_nonce), 'security_nonce' ) && is_user_logged_in() && is_admin()){
				$subject = str_replace('-',' ',sanitize_text_field($form_type));
				$subject = ucfirst($subject);
				date_default_timezone_set(get_option('timezone_string'));
				$date = date('d-M-Y H:i');
				$subjects = 'Password Management -'.$subject.' - '.$date;
				$email_from = sanitize_email($fdbk_email);
				$headers[] = 'Content-type: text/html; charset=utf-8';
				$headers[] = 'From:' . $email_from;
				$body = nl2br($_POST['fdbk_msg']);
				$sent = wp_mail( "coder426@gmail.com", $subjects, $body, $headers );
				if($sent){
					echo json_encode("Success");
				}
			}
		}
		die;
	}
}

?>