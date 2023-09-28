<?php
	/**
	**Create PMS menu
	*/		
	add_action('admin_menu', 'pms_cat_menu');
	if ( ! function_exists('pms_cat_menu') ){
		function pms_cat_menu(){
			$menu_slug = 'pms_menu';
			add_menu_page(__('Password Manager System','passwords-manager'), 'PWDMS', 'manage_options', esc_html($menu_slug), 'pms_pass_output','',2);
		}
	}
    
	/**
	**Create password sub-menu
	*/		
	if ( ! function_exists('pms_pass_output') ){
		function pms_pass_output(){
			
			global $wpdb;
			$prefix = $wpdb->prefix;
			//query for get setting key
			$key_qry  = get_option('pms_encrypt_key',false );     
			if(isset($key_qry)){
				$stng_key = esc_html($key_qry);
			}

			if (isset($_GET['tab']) && !empty($_GET['tab']) &&  sanitize_text_field($_GET['page']) == 'pms_menu') {
				$tab = strtolower(sanitize_text_field($_GET['tab']));
			} else {
				$tab = 'passwords';
			}

			$menus = [
				'fa-lock' => ['url' => 'passwords', 'label' => __('Passwords','passwords-manager')],
				'fa-th-large' => ['url' => 'categories', 'label' => __('Categories','passwords-manager')],
				'fa-sliders-h' => ['url' => 'settings', 'label' => __('Settings','passwords-manager')],
				'fa-code' => ['url' => 'shortcode', 'label' => __('Shortcode','passwords-manager')],
				'fa-file-export' => ['url' => 'export', 'label' => __('Export','passwords-manager')],
                'fa-file-import' => ['url' => 'import', 'label' => __('Import','passwords-manager')],
				'fa-headset' => ['url' => 'support', 'label' => __('Support','passwords-manager')],
			];
			$menus = apply_filters('pwdms_extand_menu', $menus);

			?>
            <!-- Side Nav Bar Start-->
            <div class="side-navbar active-nav" id="sidebar">
                <h2 class="text-white p-3 m-0">PWDMS</h2>
                <hr class="m-3 text-white" />
                <ul class="nav flex-column text-white w-100"> 
					<?php 
						foreach($menus as $menukey => $menuval){
							if (isset($_GET['page']) && sanitize_text_field($_GET['page']) == 'pms_menu') {
								$tab_url = add_query_arg(array(
									'page' => 'pms_menu',
									'tab' => esc_html(strtolower($menuval['url'])),
								), admin_url('admin.php'));
							}
							if (esc_html($tab) == esc_html(strtolower($menuval['url']))) {
								$tab_active =  'active';
							} else {
								$tab_active = '';
							}
						?>
						<li class="<?php echo esc_html($tab_active);?>">
							<a href="<?php echo esc_url($tab_url);?>" class="nav-link text-white">
								<i class="fas <?php echo sanitize_html_class($menukey);?>"></i>
								<span class="mx-2"><?php echo esc_html($menuval['label']);?></span>
							</a>
						</li>
						<?php
						}					
					?> 
				</ul>
            </div>
            <!-- Side Nav Bar End-->
            <!-- Main Wrapper Start-->
            <div class="my-container active-cont">
                <!-- Top Nav -->
                <nav class="navbar top-navbar navbar-light px-3 ps-0">
                    <a class="btn border-0" id="menu-btn"><i class="fas fa-bars"></i></a>
                </nav>
                <!--End Top Nav --> 
                <?php
                    if($tab == 'passwords'){
                        include(PWDMS_INC .'pms-passwords.php');
                    }elseif($tab == 'categories'){
                        include(PWDMS_INC .'pms-categories.php');
                    }elseif($tab == 'settings'){
                        include(PWDMS_INC .'pms-settings.php');
                    }elseif($tab == 'import'){
						include(PWDMS_INC .'admin-page/addon/csv-import/index.php');
                    }elseif($tab == 'export'){
						include (PWDMS_INC . 'admin-page/addon/csv-export/pms-csv-export-setting-page/pms_export_html.php');
                    }elseif($tab == 'shortcode'){
                        include(PWDMS_INC .'pms-admin-shortcode.php');
                    }elseif($tab == 'support'){
                        include (PWDMS_INC . 'pms-support.php');
                    }
                ?>
            </div>
            <!-- Main Wrapper End--> 
        <?php
		}
	}	

add_action('wp_ajax_import_dummy_data', 'import_dummy_data');
function import_dummy_data(){

    if(wp_verify_nonce( sanitize_text_field($_POST['security_nonce']), 'security_nonce' )){
       
		$dummy_json = file_get_contents(PWDMS_PLUGIN_DIR.'/assets/data/dummy.json');
		$dummy_object =  json_decode($dummy_json);
		foreach($dummy_object as $dummy_data){
			$new_record = pwdms_custom_create_passwords_from_csv( $dummy_data->Name, $dummy_data->Email,$dummy_data->Password,$dummy_data->URL,$dummy_data->Category,$dummy_data->Category_color ,$dummy_data->Note);
		}
		if($new_record == 1){
			echo "success";
		}else{
			echo "failed";
		}
    }
	die;
}


function pwdms_custom_create_passwords_from_csv( $name, $email, $password,$url, $category,$category_color ,$note){

    global $wpdb;
    $prefix = $wpdb->prefix;
    $cat_name = strtolower(sanitize_text_field(trim($category)));
    $cat_name = str_replace('"', '', $cat_name);	
    $color = (!empty($category_color)) ? sanitize_hex_color($category_color) : '';
    $get_all_cate = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$prefix}pms_category WHERE category LIKE '%s'",$cat_name));
    if($get_all_cate == 0){
        // use if for special character
            
        $table_name = $wpdb->prefix . "pms_category"; 
        $result	=	$wpdb->insert(
        $table_name,
        array('category' => $cat_name,'category_color' => $color) , 
        array('%s','%s') 
        );       
    }
    $category = str_replace('"', '', $category);
    $get_cate_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$prefix}pms_category WHERE category LIKE '%s'",$category));
    $user_name  = sanitize_text_field($name);
    $user_email = sanitize_text_field($email);
    $pass_cate  = absint($get_cate_id);
    if (class_exists('Encryption')) {
        $Encryption = new Encryption();
    } else { 
        echo "Failed";
        die;
    }
    $qry  = get_option('pms_encrypt_key');
	$stng_key = esc_html($qry);
    $encryppwd 	 = $Encryption->encrypt($password, $stng_key);
    $encryppwd = esc_html($encryppwd);
	$user_name = str_replace('"', '', $user_name);
    $user_note  = str_replace('"', '', $note);
    $url = esc_url($url);
    $table_name = $prefix . "pms_passwords"; 
    $final_rslt	=	$wpdb->insert(
    $table_name, 
    array('user_name' 		=> $user_name,
            'user_email' 	=> $user_email,
            'user_password' => $encryppwd,
            'category_id' 	=> $pass_cate,
            'note'			=> $user_note,
            'url'           => $url,
    ) , 
    array('%s','%s','%s','%d','%s','%s') 
    );

    return $final_rslt;
}