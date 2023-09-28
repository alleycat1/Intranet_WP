<?php
/*
**Get password data in datatable
*/	
if ( ! function_exists('get_new_pass') ) {
	function get_new_pass(){  	
		global $wpdb, $posts;
		$prefix = $wpdb->prefix;
		$content = get_the_content();
		if(isset($_POST) && !empty($_POST)){
			extract($_POST);

			if(wp_verify_nonce( sanitize_text_field($security_nonce), 'security_nonce' ) || has_shortcode($contect,'pms_pass')){
			
				/**
				**options table fetch record
				*/
				$category_name = strtolower(sanitize_text_field($cat_name));

				$startLimit		=	filter_var($start, FILTER_VALIDATE_INT);
				$lengthLimit	=	filter_var($length, FILTER_VALIDATE_INT);

				if(isset($category_name) && !empty($category_name)){
					$get_cate	=	'';
					$get_cate = $wpdb->get_var("SELECT id FROM {$prefix}pms_category WHERE category LIKE '%$category_name%'");
					$get_cate = absint($get_cate);
					$pass_rec = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$prefix}pms_passwords WHERE category_id =%d LIMIT 0 , 10",$get_cate));	
					$array = json_decode(json_encode($pass_rec), True);			
					$pass_c	=	count($pass_rec);			
				}else{		
					$query = "SELECT * FROM {$prefix}pms_passwords";
					$pass_rec = $wpdb->get_results($wpdb->prepare($query));		
					$array = json_decode(json_encode($qrs_pass), True);
					$pass_c	=	count($pass_rec);
					$searchVal	=	sanitize_text_field($search["value"]);
					if(isset($searchVal) && !empty($searchVal)){
						$query .= ' WHERE user_name LIKE "%'.ucfirst($searchVal).'%" ';
					}else{
						$query .= ' WHERE 1 ';
					}
				
					$data_order 	= 	sanitize_text_field($order[0]['dir']);
					if(	isset($data_order)	&& !empty($data_order)){
						$query .= ' ORDER BY pass_id '.$data_order;
					}

					if(	isset($lengthLimit)	&& ($lengthLimit != -1) && isset($startLimit)){
						$query .= ' LIMIT ' . $startLimit . ', ' . $lengthLimit;
					} else {
						$query .= ' LIMIT ' . 0 . ', ' . 10;
					}
					$qrs_pass = $wpdb->get_results($wpdb->prepare($query));
					$array = json_decode(json_encode($qrs_pass), True);
				}//end else

				$data = array();
				$rowCount = $startLimit;
				foreach($array as $row){
					$rowCount++;
					$id  = filter_var($row['pass_id'], FILTER_VALIDATE_INT);
					$cId = filter_var($row['category_id'], FILTER_VALIDATE_INT);

					/* Fetch Category from category*/
					$query  = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$prefix}pms_category where id = %d",$cId));
					$category = $query->category;
					$category_name = ucfirst(esc_html($category));
					$sub_array = array();
					$sub_array[] = absint($rowCount);//$row['pass_id']
					$sub_array[] = ucfirst(esc_html($row['user_name']));
					$sub_array[] = esc_html($row['user_email']);
					$sub_array[] = '<input id="user_pwd'.esc_html($id).'" name="user_pwd" type="password" value="'.esc_html($row['user_password']).'" class="pass_inp border-0" readonly="readonly" style="box-shadow: none;background: none;">';
					$sub_array[] = '<style>.colorcode'.$rowCount.'.tag::before{   border-right: 10px solid '.esc_attr($query->category_color).';}</style><span class="colorcode'.$rowCount.' tag"  style="background-color:'.esc_attr($query->category_color).'">'. esc_html($category_name) .'</span>';
					$sub_array[] = '<a href="'.esc_url($row['url']).'" target="_blank" data-bs-toggle="tooltip" data-bs-placement="top" title="'.esc_url($row['url']).'"><i class="fas fa-link"></i></a>';
					$sub_array[] = '
					<div class="share">	
						<div class="label"><i class="fas fa-ellipsis-v"></i></div>					
						<div class="social icon first"><a href="javascript:void(0);" data-id="'.esc_html($id).'" id="s_'.esc_html($id).'" class="decrypt" onclick="getpwd(this)" title="View password"><i class="fa fa-eye text-success"></i></a></div>
						<div class="social icon"><a href="#" name="update" id="'.esc_html($id).'" class="update" title="Edit password"><span class="dashicons dashicons-edit text-warning"></span></a></div>
						<div class="social icon"><a href="#" name="dlt" class="dlt" id="'.esc_html($id).'" title="Delete password"><span class="dashicons dashicons-trash text-danger"></span></a></div>
						<div class="social icon"><a href="#" name="note_preview" id="'.esc_html($id).'" class="note_preview" title="Preview your note"><span class="dashicons dashicons-clipboard"></span></a></div>
						<div class="social icon"><a href="javascript:void(0);" data-clipboard-action="copy" data-clipboard-target="#user_pwd'.esc_html($id).'" id="'.esc_html($id).'" class="copy_clipboard" title="Password copy to clipboard"><span class="dashicons dashicons-admin-page"></span></a></div>
						<div class="social icon last"><a href="javascript:void(0);" id="'.esc_html($id).'" class="clonepass" title="Duplicate Password"><i class="fas fa-clone text-warning"></i></a></div>
					</div>
					';
					$data[] = $sub_array;	
				}// end foreach
				$output = array(
					"recordsTotal"  	=>  filter_var($pass_c, FILTER_VALIDATE_INT),
					"recordsFiltered" 	=> 	filter_var($pass_c, FILTER_VALIDATE_INT),
					"data"				=>	$data
				);	
				echo json_encode($output);
				die;
			}
		}
	}	
}

//add new password
if ( ! function_exists('post_new_pass') ) {	
	function post_new_pass(){
		if(isset($_POST) && !empty($_POST)){
			extract($_POST);
			$btn_action		=	sanitize_text_field($btn_action);
			if(isset($btn_action) && !empty($btn_action) && wp_verify_nonce( sanitize_text_field($security_nonce), 'security_nonce' ) ){  
				global $wpdb;
				$prefix = $wpdb->prefix;
				/*
				**Add new password detail in database
				*/	
				$user_name  = sanitize_text_field($user_name);
				$user_email = sanitize_text_field($user_email);
				$pass_cate  = filter_var($pass_cat, FILTER_VALIDATE_INT);
				$encry_pass = sanitize_text_field($ency);
				$user_note  = sanitize_textarea_field($user_note);
				$user_url 	= sanitize_url($user_url);	
				if($btn_action == 'Add'){			
					if(($user_name == '') || ($user_email == '') || ($pass_cate == '')){
						$resp['blnkspc'] = "blank";
						echo json_encode($resp);
						die;
					}else{
						if((isset($user_name) && !empty($user_name)) || (isset($user_email) && !empty($user_email)) || (isset($pass_cate) && !empty($pass_cate)) || (isset($encry_pass) && !empty($encry_pass))){

							$table_name = $prefix . "pms_passwords"; 
							$final_rslt	=	$wpdb->insert(
								$table_name, 
								array('user_name' 		=> $user_name,
									'user_email' 		=> $user_email,
									'user_password' 	=> $encry_pass,
									'category_id' 	=> $pass_cate,
									'note'			=> $user_note,
									'url' 			=> $user_url,
									) , 
								array('%s','%s','%s','%d','%s','%s') 
							);
							//execute query
							if($final_rslt){
								echo json_encode($final_rslt);
							}else{ 
								echo "Failed";
							}die;
						}
					}
				}
				/*
				**Edit password detail in database
				*/					
				elseif($btn_action == 'Edit'){			
					$pass_id 	= absint($pass_id);	
					if(($user_name == '') || ($user_email == '') || ($pass_cate == '')){
						$resp['blnkspc'] = "blank";
						echo json_encode($resp);
						die;
					}else{
						if((isset($user_name) && !empty($user_name)) || (isset($user_email) && !empty($user_email)) || (isset($pass_cate) && !empty($pass_cate))){
							if($encry_pass != ''){
								$table_name = $prefix . "pms_passwords"; 
								$final_rslt		= $wpdb->update( 
									$table_name, 
									array('user_name' 		=> $user_name,
										'user_email' 		=> $user_email,
										'user_password'	=> $encry_pass,
										'category_id' 	=> $pass_cate,
										'note'			=> $user_note,
										'url'				=> $user_url,
										) ,
									array( 'pass_id' => $pass_id )
								);
							}else{
								$table_name = $prefix . "pms_passwords"; 
								$final_rslt		= $wpdb->update(
									$table_name, 
									array('user_name'  => $user_name,
										'user_email' => $user_email,
										'category_id'=> $pass_cate,
										'note'		=> $user_note,
										'url'			=> $user_url,
										) ,
									array( 'pass_id' => $pass_id )
								);
							}
							if($final_rslt){
								echo json_encode($final_rslt);	
							}else{			
								echo "error";	
							}
						}
					}
				}
				/*
				**Delete password detail in batabase
				*/		
				elseif($btn_action == 'Delete'){
					$pass_id = absint($pass_id);
					$table_name = $prefix . "pms_passwords"; 
					if((isset($pass_id)) && !empty($pass_id)){
						$final_rslt = $wpdb->delete( $table_name, array( 'pass_id' => $pass_id ) );
					}
					//execute query
					if($final_rslt){
						echo json_encode($final_rslt);
						die;
					}else{ 
						echo "error";
					}
				}
			}
		}
	}
}

/*
**Fetch category detail in database
*/	
if ( ! function_exists('edit_pass') ) {
	function edit_pass(){
		global $wpdb, $posts;
		$prefix = $wpdb->prefix;
		if(isset($_POST) && !empty($_POST)){
			extract($_POST);
			$key_qry  = get_option('pms_encrypt_key');
			$stng_key = esc_html($key_qry);
			if (class_exists('Encryption')) {
				$Encryption = new Encryption();
			} else { 
				echo "Failed";
				die;
			}
			$pass_id = absint($pass_id);
			if((isset($pass_id)) && !empty($pass_id) && wp_verify_nonce( sanitize_text_field($security_nonce), 'security_nonce' ) ){
				$query  = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$prefix}pms_passwords where pass_id = %d",$pass_id));			
				$value= json_decode(json_encode($query), True);

				if(count($value)>0){
					foreach ($value as $row) {
						$output['user_name']  	  	= esc_html($row['user_name']);
						$output['user_email'] 		= esc_html($row['user_email']);
						$output['user_password']  	= $Encryption->decrypt(esc_html($row['user_password']),$stng_key);
						$output['user_category'] 	= absint($row['category_id']);
						$output['user_note'] 	    = esc_textarea($row['note']);
						$output['user_url'] 	    = esc_url($row['url']);
					}
				}
			}else{ 
				echo "error";
			}
			echo json_encode($output);
			die;
		}
	}	
}


/**
**decrypt key
*/
if ( ! function_exists('decrypt_pass') ) {
	function decrypt_pass(){
		global $wpdb;
		$prefix = $wpdb->prefix;
		if(isset($_POST) && !empty($_POST)){
			extract($_POST);
			$key_qry  = get_option('pms_encrypt_key');      
			$stng_key = esc_html($key_qry);

			if (class_exists('Encryption')) {
				$Encryption = new Encryption();
			} else { 
				echo "Failed";
				die;
			}
			$saction  = sanitize_text_field($saction);
			$enc_pass = sanitize_text_field($user_pwd);

			if(wp_verify_nonce( sanitize_text_field($security_nonce), 'security_nonce' )){
				if(	isset($saction)	&&	($saction	==	'decrypt')){
					$dcryppwd 	 = $Encryption->decrypt($enc_pass, $stng_key);				
					echo esc_html($dcryppwd);
					die;
				}
				else if(isset($saction)	&&	($saction	==	'encrypt')){
					$id = absint($did);
					$query  = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$prefix}pms_passwords where pass_id = %d",$id));		
					$ecryppwd = $query[0]->user_password;
					echo esc_html($ecryppwd);
					die;
				}
			}
		}
	}
}

/**
**clone password
*/
if ( ! function_exists('clone_password') ) {
	function clone_password(){
		global $wpdb;
		$prefix = $wpdb->prefix;
		if(isset($_POST) && !empty($_POST)){
			extract($_POST);

			if(wp_verify_nonce( sanitize_text_field($security_nonce), 'security_nonce' )){
				$query  = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$prefix}pms_passwords where pass_id = %d",$pass_id ),ARRAY_A);
				$table_name = $prefix . "pms_passwords"; 
				$final_rslt	=	$wpdb->insert(
					$table_name, 
					array('user_name' 		=> $query['user_name'],
						'user_email' 		=> $query['user_email'],
						'user_password' 	=> $query['user_password'],
						'category_id' 		=> $query['category_id'],
						'note'				=> $query['note'],
						'url' 				=> $query['url'],
						) , 
					array('%s','%s','%s','%d','%s','%s') 
				);
				if($final_rslt == 1){
					echo "success";
				}else{
					echo "failed";
				}
				die;
			}
		}
	}
}
/*
**Add actions category detail
*/	
add_action('wp_ajax_get_new_pass', 'get_new_pass');	
add_action( 'wp_ajax_nopriv_get_new_pass', 'get_new_pass' );
add_action('wp_ajax_post_new_pass', 'post_new_pass');
add_action('wp_ajax_edit_pass', 'edit_pass');
add_action('wp_ajax_clone_password', 'clone_password');
add_action('wp_ajax_decrypt_pass', 'decrypt_pass');
add_action( 'wp_ajax_nopriv_decrypt_pass', 'decrypt_pass' );

?>