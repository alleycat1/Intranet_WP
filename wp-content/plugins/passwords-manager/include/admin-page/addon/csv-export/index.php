<?php
add_action( 'wp_ajax_pwdms_export_detail_list','pwdms_export_detail_list' );
function pwdms_export_detail_list(){
	global $wpdb;//add $wpdb
if(isset($_POST['from_data']) && !empty($_POST['from_data']) && is_array($_POST['from_data'])){
	extract($_POST['from_data']);

	if ( wp_verify_nonce( sanitize_text_field($security_nonce), 'security_nonce' ) && is_user_logged_in() && is_admin()) {
		$formdata = esc_html($pwdms_document_title);
		error_reporting( E_ERROR );
		if ( !session_id() ) {
			session_start();
		}
		$time_start = microtime( true );			
		ini_set( 'max_input_time', 3600 * 3 );
		ini_set( 'max_execution_time', 3600 * 3 );
		set_time_limit( 0 );
		$per_page = apply_filters( 'pwdms_csv_export_per_page_limit', 66 );
		$page = max( 1, absint($_POST['page_num']));
		$category_id = esc_html($pwdms_csv_category);
		$export_res =   $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}pms_passwords ".
		"JOIN  {$wpdb->prefix}pms_category ON {$wpdb->prefix}pms_category.id  =   {$wpdb->prefix}pms_passwords.category_id"
		." AND {$wpdb->prefix}pms_category.id IN (".$category_id.") where 1 LIMIT 200"),OBJECT);
		
		if ( $page == 1 ) {
			unset( $_SESSION['pwdms_csv_array'] );
			$pwdms_csv_array = array();
			$_SESSION['pwdms_csv_array'] = $pwdms_csv_array;
		} else {
			if(isset($_SESSION['pwdms_csv_array']) && !empty($_SESSION['pwdms_csv_array']) && is_array($_SESSION['pwdms_csv_array'])){
				$multi_array = [];
				foreach($_SESSION['pwdms_csv_array'] as $values){
					$csv_array[] = array_map('sanitize_text_field', $values);
				}
			}else{
				$csv_array = [];
			}
			$pwdms_csv_array = $csv_array;
		}

		foreach ($export_res as $res){
				
			//CHECK TO SEE IF NAME IS CHECKED
			if ( isset( $col_pwdms_name ) ) {
		
				$pwdms_name_array = array(
					__( 'Name', 'pwdms' ) => esc_html($res->user_name),
				);
			} else {
				$pwdms_name_array = array();
			}

			//CHECK TO SEE IF EMAIL IS CHECKED
			if ( isset( $col_pwdms_email ) ) {
		
				$pwdms_email_array = array(
					__( 'Email', 'pwdms' ) => esc_html($res->user_email),
				);
			} else {
				$pwdms_email_array = array();
			}

			//CHECK TO SEE IF PASSWORD IS CHECKED
			if ( isset( $col_pwdms_password ) ) {
				$qry  = get_option('pms_encrypt_key');
				$stng_key = esc_html($qry);
				if (class_exists('Encryption')) {
					$Encryption = new Encryption();
				} else { 
					echo "Failed";
					die;
				}
				$enc_pass = sanitize_text_field($res->user_password);
				$dcryppwd 	 = $Encryption->decrypt($enc_pass, $stng_key);
				$pwdms_password_array = array(
					__( 'Password', 'pwdms' ) => esc_html($dcryppwd),
				);
			} else {
				$pwdms_password_array = array();
			}

			//CHECK TO SEE IF CATEGORY IS CHECKED				
			if ( isset( $col_pwdms_category ) ) {
		
				$pwdms_category_array = array(
					__( 'Category', 'pwdms' ) => esc_html($res->category),
				);
			} else {
				$pwdms_category_array = array();
			}

			//CHECK TO SEE IF CATEGORY IS CHECKED				
			if ( isset( $col_pwdms_category ) ) {
		
				$pwdms_category_color_array = array(
					__( 'Category Color', 'pwdms' ) => esc_html($res->category_color),
				);
			} else {
				$pwdms_category_color_array = array();
			}

			//CHECK TO SEE IF URL IS CHECKED				
			if ( isset( $col_pwdms_url ) ) {
		
				$pwdms_url_array = array(
					__( 'URL', 'pwdms' ) => esc_url($res->url),
				);
			} else {
				$pwdms_url_array = array();
			}

			//CHECK TO SEE IF DESCRIPTION IS CHECKED
			if ( isset( $col_pwdms_desc ) ) {
		
				$pwdms_note_array = array(
					__( 'Note', 'pwdms' ) => esc_textarea($res->note),
				);
			} else {
				$pwdms_note_array = array();
			}

			$pwdms_csv_array[] = array_merge(
				$pwdms_name_array,
				$pwdms_email_array,
				$pwdms_password_array,
				$pwdms_url_array,
				$pwdms_category_array,
				$pwdms_category_color_array,
				$pwdms_note_array
			);	
			$_SESSION['pwdms_csv_array'] = $pwdms_csv_array;			
			
		}//end foreach
		$exported = $page * $per_page;
		$time_end = microtime( true );
		$execution_time = $time_end - $time_start;
		$fount_data = count($export_res);
		$response = array(	
			'page'           => $page + 1,
			'done'           => false,
			'execution_time' => $execution_time,
			'found_posts'    => $fount_data,
		);
		
		if ( $exported <= $fount_data || $exported >= $fount_data) {
			$response['done'] = true;
		}else{
			$response['done'] = false;
		}
		wp_send_json_success( $response );
	}
}
}

add_action( 'wp_ajax_pwdms_export_csv', 'pwdms_export');
function pwdms_export(){	
	if (isset($_GET['_wpnonce']) && wp_verify_nonce( sanitize_text_field($_GET['_wpnonce']), 'security_nonce' ) && is_user_logged_in() && is_admin()) {

		if ( !session_id() ) {
			session_start();
		}

		if ( defined( 'pwdms_DEBUG' ) ) {
			error_reporting( E_ALL );
			ini_set( 'display_errors', 1 );
		} else {
			error_reporting( 0 );
		}		

		$array = $_SESSION['pwdms_csv_array'];
		
		if (count($array) == 0) {
			return null;
		}
		ob_start();
		$df = fopen( "php://output", 'w' );
		fputcsv( $df, array_keys( reset( $array ) ) );
		foreach ( $array as $row ) {
			fputcsv( $df, $row );
		}
		fclose( $df );

		if (isset($_GET['document_title']) && !empty(sanitize_text_field($_GET['document_title'])) ) {
			$filename = (!empty(sanitize_text_field($_GET['document_title']))) ? sanitize_text_field($_GET['document_title']). ".csv" : '';
			$now = gmdate( "D, d M Y H:i:s" );
			header( "Expires: Tue, 03 Jul 2020 06:00:00 GMT" );
			header( "Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate" );
			header( "Last-Modified: {$now} GMT" );
			// force download
			header( "Content-Type: application/force-download" );
			header( "Content-Type: application/octet-stream" );
			header( "Content-Type: application/download" );
			// disposition / encoding on response body
			header( "Content-Disposition: attachment;filename={$filename}" );
			header( "Content-Transfer-Encoding: binary" );
			//unset( $_POST );

			flush();
			@readfile($filename);
			exit;
		}
	}
}	


?>