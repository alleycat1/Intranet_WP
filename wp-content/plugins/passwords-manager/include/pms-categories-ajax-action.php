<?php


/*
**Enter category data in datatable
*/
if ( ! function_exists('get_new_cats') ) {	
	function get_new_cats(){
		global $wpdb, $posts;
		$prefix = $wpdb->prefix;
		if(isset($_POST) && !empty($_POST)){
			extract($_POST);
		
		if(wp_verify_nonce(sanitize_text_field($security_nonce), 'security_nonce' ) && is_user_logged_in() && is_admin()){
			/*
			**category Table query
			*/
			$tr_cate = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$prefix}pms_category"));			
			/*
			**Password Table query
			*/	
			$tr_pass = $wpdb->get_results($wpdb->prepare("SELECT COUNT(*) FROM {$prefix}pms_passwords"));
			/*
			**Start Inner join
			*/		
			$serachString	=	sanitize_text_field($search["value"]);
			$startLimit		=	filter_var($start, FILTER_VALIDATE_INT);
			$lengthLimit	=	filter_var($length, FILTER_VALIDATE_INT);
			$rowCount = $startLimit;
			if($tr_cate >0 && $tr_pass >0){
				
				if(isset($serachString)	&&	!empty($serachString)){
					$searchQuery .= ' WHERE category LIKE "%'.$serachString.'%" ';
				}else{
					$searchQuery .= ' WHERE 1 ';
				}
				/*
				**join password and category table
				*/	
				$query3 = "SELECT {$prefix}pms_category.`category` ,{$prefix}pms_category.`category_color` ,{$prefix}pms_category.`id`, 
							COUNT({$prefix}pms_passwords.category_id) AS Total FROM {$prefix}pms_category 
							LEFT JOIN {$prefix}pms_passwords ON {$prefix}pms_category.`id` = {$prefix}pms_passwords.category_id 
							".$searchQuery."
							GROUP BY {$prefix}pms_category.id";
				$query3 .= ' '.sanitize_text_field($order[0]['dir']);
				
				if(	isset($lengthLimit)	&&	($lengthLimit != -1)	&&	isset($startLimit)	){
					$query3 .= ' LIMIT ' . $startLimit . ', ' . $lengthLimit;
				} else {
					$query3 .= ' LIMIT ' . 0 . ', ' . 10;
				}

				$trs3 = $wpdb->get_results($query3);	

				$data = array();
				$array = json_decode(json_encode($trs3), True);
				$rowCount = $startLimit;
				foreach($array as $row){
					$rowCount++;
					$id  = filter_var($row['id'], FILTER_VALIDATE_INT);
					$sub_array = array();
					$sub_array[] = filter_var($rowCount, FILTER_VALIDATE_INT);
					$sub_array[] =	'<span class="colorcode1 badge" style="background-color:'.esc_attr($row['category_color']).'">'. ucfirst(esc_html($row['category'])) .'</span>';
					$sub_array[] = filter_var($row['Total'], FILTER_VALIDATE_INT);
					$sub_array[] = '<div class="act_box"><a href="javascript:void(0);" name="upcate" id="'.$id.'" class="upcate"><span class="dashicons dashicons-edit text-warning"></span></a>&nbsp;<a href="#" name="delete" class="delete" id="'.$id.'"><span class="dashicons dashicons-trash text-danger"></span></a> 
						</div>';
					$data[] = $sub_array;
				}	
				$output = array(
					"recordsTotal"  	=>  filter_var($tr_cate, FILTER_VALIDATE_INT),
					"recordsFiltered" 	=> 	filter_var($tr_cate, FILTER_VALIDATE_INT),
					"data"				=>	$data
				);
				echo json_encode($output);
				die;
			}else{

				$query = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$prefix}pms_category"));
				if(isset($serachString)	&&	!empty($serachString)	)
				{
					$query .= ' WHERE category LIKE "%'.$serachString.'%" ';				

				}else{
					$query .= ' WHERE 1 ';
				}
	
				if(	isset($lengthLimit)	&&	($lengthLimit != -1)	&&	isset($startLimit)	)
				{
					$query .= ' LIMIT ' . $startLimit . ', ' . $lengthLimit;
				}else{
					$query .= ' LIMIT ' . 0 . ', ' . 10;
				}

				$qrs = $wpdb->get_results($query);
				$data = array();
				$array = json_decode(json_encode($qrs), True);

				foreach($array as $row){
					$rowCount++;
					$id  = filter_var($row['id'], FILTER_VALIDATE_INT);
					$sub_array = array();
					$sub_array[] = filter_var($rowCount, FILTER_VALIDATE_INT);//$row['category_id']
					$sub_array[] =	'<span class="colorcode1 badge"  style="background-color:'.esc_attr($row['category_color']).'">'. ucfirst(esc_html($row['category'])) .'</span>';
					$sub_array[] = 0;
					$sub_array[] = '<div class="act_box">
					<a href="#" name="upcate" id="'.esc_html($id).'" class="upcate"><span class="dashicons dashicons-edit text-warning"></span></a>&nbsp;<a href="#" name="delete" class="delete" id="'.esc_html($id).'"><span class="dashicons dashicons-trash text-danger"></span></a> 
					</div>';
					$data[] = $sub_array;	
				}
				$output = array(
					"recordsTotal"  	=>  filter_var($tr_cate, FILTER_VALIDATE_INT),
					"recordsFiltered" 	=> 	filter_var($tr_cate, FILTER_VALIDATE_INT),
					"data"				=>	$data
				);

				echo json_encode($output);
				die;
			}
		}
	}
	}	
}

/*
**add new category
*/
if ( ! function_exists('post_new_cats') ) {	

	function post_new_cats(){
		
		global $wpdb;
		$prefix = $wpdb->prefix;
		if(isset($_POST) && !empty($_POST)){
		extract($_POST);
		$btn_action	 =	sanitize_text_field($btn_action);
		if(isset($btn_action)  && wp_verify_nonce( sanitize_text_field($security_nonce), 'security_nonce' ) && is_user_logged_in() && is_admin()){  

			$color = (!empty($category_color)) ? sanitize_hex_color($category_color) : '';
			/*
			**Add new category detail in database
			*/		
			if($btn_action == 'Add'){
				$cat_name = strtolower(sanitize_text_field($category));
				// use if for special character
				if (preg_match('/[\'^!"|~£$%&*()}{@#~?><>,|=_+¬-]/', $cat_name)	||	empty($cat_name)){
					$resp['ecode'] = "special character";
					echo json_encode($resp);
					die;
				}
				//use else for insert value if form submit correct	
				else{
					if(isset($cat_name) && !empty($cat_name)){
						$query_cate = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$prefix}pms_category where category LIKE '%s'",$cat_name));
						$cate 	= esc_html($query_cate[0]->category);
						//use if category not exists in database
						if(isset($cate) && $cat_name != $cate){
							$table_name = $wpdb->prefix . "pms_category"; 
							$result	=	$wpdb->insert(
								$table_name, 
								array(
									'category' => $cat_name,
									'category_color' => $color
								),
								array('%s','%s') 
							);
							if($result){
								$resp['resp'] = "Success";	
							}else{			
								$resp['resp'] = "Error";	
							}
						}
						// use else if category exists	
						else{
							$resp['al_exist'] = $cat_name;
						}
					}
				}
				echo json_encode($resp);
				die;
			}

			/*
			**Edit category detail in database
			*/					
			elseif($btn_action == 'Edit') 
			{
				$color = (!empty($category_color)) ? sanitize_hex_color($category_color) : '';
				$cat_id = absint($category_id);
				$cat_name = strtolower(sanitize_text_field($category));
				if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $cat_name) || empty($cat_name)){
					$resp['ecode'] = "special character";
					echo json_encode($resp);
					die;
				}else{
					if(isset($cat_name) && !empty($cat_name)){
						$query_cate  = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$prefix}pms_category where category LIKE '%s'",$cat_name));						
						$cate = esc_html($query_cate[0]->category);

						//fetch value after submit form	
						if($cat_name != $cate){
							$table_name = $wpdb->prefix . "pms_category"; 
							$result	=	$wpdb->update( 
								$table_name, 
								array( 
									'category' => $cat_name,
									'category_color' => $color
								), 
								array( 'id' => $cat_id )
							);
							if($result){
								$resp['resp'] = "Success";	
							}else{			
								$resp['resp'] = "Error";	
							}
						}
						else{
							$newresult = $wpdb->query($wpdb->prepare("UPDATE {$prefix}pms_category SET category_color = '$color' WHERE id = $cat_id"));
							$resp['al_exist'] = "Exists";
						}
						echo json_encode($resp);
						die;
					}
				}
			}
			/*
			**Delete category detail in batabase
			*/		
			elseif($btn_action == 'Delete')
			{
				$cat_id     = absint($category_id);
				$qry_dlt_cate  = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$prefix}pms_passwords"));

				if(isset($cat_id) && wp_verify_nonce(sanitize_text_field( $security_nonce), 'security_nonce' ) && is_user_logged_in() && is_admin()){
					if($qry_dlt_cate>0){
						$table_name = $wpdb->prefix . "pms_passwords"; 
						$result		= $wpdb->delete( $table_name, array( 'category_id' => $cat_id ) );
						$table_name = $wpdb->prefix . "pms_category"; 
						$result		= $wpdb->delete( $table_name, array( 'id' => $cat_id ) );
					}else{
						$table_name = $wpdb->prefix . "pms_category"; 
						$result		= $wpdb->delete( $table_name, array( 'id' => $cat_id ) );
					}
				}
				//execute query
				if($result){
					echo json_encode($result);					
				}else{ 
					echo "error";
				}die;
			}
		}
	}
	}
}
/*
**Fetch category detail in database
*/	
if ( ! function_exists('edit_cats') ) {
	function edit_cats(){
		global $wpdb, $posts;
		$prefix = $wpdb->prefix;
		if(isset($_POST) && !empty($_POST)){
			extract($_POST);
			$cat_id = absint($category_id);
			if( isset($security_nonce )  && wp_verify_nonce(sanitize_text_field( $security_nonce), 'security_nonce' ) && is_user_logged_in() && is_admin()){	  
				$fetch_cate  = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$prefix}pms_category where id LIKE '%d'",$cat_id));	
				
				$output['category_name'] = esc_html(ucwords($fetch_cate->category));
				$output['category_color'] = esc_html($fetch_cate->category_color);
				echo json_encode($output);
			}
			die;
		}	
	}	
}
/*
**Add actions category detail
*/	
add_action('wp_ajax_get_new_cats', 'get_new_cats');	
add_action('wp_ajax_post_new_cats', 'post_new_cats');
add_action('wp_ajax_edit_cats', 'edit_cats');	
?>