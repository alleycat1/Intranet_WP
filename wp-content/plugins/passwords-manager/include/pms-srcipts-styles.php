<?php
	/**
	**Include css js files
	*/

	if(isset($_REQUEST['page'])){
		//     ||  ($_REQUEST['page'] == 'pms_export')
		if(!function_exists('pwdms_add_admin_scripts')  && (sanitize_text_field($_GET['page']) == 'pms_menu')){
			function pwdms_add_admin_scripts() {
				/** 
				** Admin Dashboard Style
				**/
				wp_enqueue_style( 'wp-color-picker' ); 
				wp_enqueue_style(PWDMS_NAME . '_fontawesome_min', PWDMS_ASSETS . 'libs/fontawesome/all.css', array(), PWDMS_VAR);
				wp_enqueue_style(PWDMS_NAME . '_bootstrap_min', PWDMS_ASSETS . 'libs/bootstrap/css/bootstrap.min.css', array(), PWDMS_VAR);
				wp_enqueue_style(PWDMS_NAME . '_datatable', PWDMS_ASSETS . 'libs/datatable/datatables.min.css', array(), PWDMS_VAR);
				wp_enqueue_style(PWDMS_NAME . '_rowdatatable', PWDMS_ASSETS . 'libs/datatable/rowReorder.dataTables.min.css', array(), PWDMS_VAR);
				wp_enqueue_style(PWDMS_NAME . '_respdatatable', PWDMS_ASSETS . 'libs/datatable/responsive.dataTables.min.css', array(), PWDMS_VAR);
				wp_enqueue_style(PWDMS_NAME . '_sweetalert', PWDMS_ASSETS . 'libs/sweetalert/sweetalert2.min.css', array(), PWDMS_VAR);
				wp_enqueue_style(PWDMS_NAME . '_admin', PWDMS_ASSETS . 'css/pms-admin.css', array(), PWDMS_VAR);

				/** 
				** Admin Dashboard Script
				**/
				wp_enqueue_script('jquery');			
				wp_enqueue_script('jquery-ui-tabs');
				wp_enqueue_script('jquery-ui-progressbar');      
				

				wp_enqueue_script(PWDMS_NAME . '_clipboard', includes_url() . '/js/clipboard.min.js', array('jquery'), PWDMS_VAR, true);
				wp_enqueue_script(PWDMS_NAME . '_datatable', PWDMS_ASSETS . 'libs/datatable/datatables.min.js', array('jquery'), PWDMS_VAR, true);
				wp_enqueue_script(PWDMS_NAME . '_rowdatatable', PWDMS_ASSETS . 'libs/datatable/dataTables.rowReorder.min.js', array('jquery'), PWDMS_VAR, true);
				wp_enqueue_script(PWDMS_NAME . '_responsivedatatable', PWDMS_ASSETS . 'libs/datatable/dataTables.responsive.min.js', array('jquery'), PWDMS_VAR, true);
				wp_enqueue_script(PWDMS_NAME . '_popper', PWDMS_ASSETS . 'libs/popper/popper.min.js', array('jquery'), PWDMS_VAR, true);
				wp_enqueue_script(PWDMS_NAME . '_bootstrap_min', PWDMS_ASSETS . 'libs/bootstrap/js/bootstrap.min.js', array('jquery'), PWDMS_VAR, true);
				wp_enqueue_script(PWDMS_NAME . '_sweetalert', PWDMS_ASSETS . 'libs/sweetalert/sweetalert2.min.js', array('jquery'), PWDMS_VAR, true);
				wp_enqueue_script(PWDMS_NAME . '_crypto', PWDMS_ASSETS . 'js/crypto.js', array('jquery'), PWDMS_VAR, true);
				wp_enqueue_script(PWDMS_NAME . '_encry', PWDMS_ASSETS . 'js/encryption.js', array('jquery'), PWDMS_VAR, true);
				wp_enqueue_script(PWDMS_NAME . '_settings', PWDMS_ASSETS . 'js/pms-settings.js', array('jquery'), PWDMS_VAR, true);
				wp_enqueue_script(PWDMS_NAME . '_admin', PWDMS_ASSETS . 'js/pms-admin.js', array('jquery','wp-color-picker'), PWDMS_VAR, true);
				wp_enqueue_script(PWDMS_NAME . '_inkpass', PWDMS_ASSETS . 'js/pms-password.js', array('jquery'), PWDMS_VAR, true);
				wp_enqueue_script(PWDMS_NAME . '_category', PWDMS_ASSETS . 'js/pms-category.js', array('jquery'), PWDMS_VAR, true);				
				wp_enqueue_script(PWDMS_NAME . '_csv_export', PWDMS_INC_URL . 'admin-page/addon/csv-export/js/pms_csv_export.js', array('jquery'), PWDMS_VAR, true);				
				$admin_url = strtok( admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ), '?' );
				wp_localize_script( PWDMS_NAME . '_inkpass', 'MyAjax', array( 
					'ajaxurl' => $admin_url,
					'no_export_data' => __('There are no exporting data in your selection fields','passwords-manager'),
					'export_data' => __('Data export successfully','passwords-manager'),
					'import_success' => __('Data Imported successfully','passwords-manager'),
					'no_upload' => __('No File Upload','passwords-manager'),
					'add_encry' => __('Please add encryption key in setting page.','passwords-manager'),
					'fill_field_properly' => __('Please fill all field properly.','passwords-manager'),
					'email_success' => __('Email sent successfully.','passwords-manager'),
					'email_failed' => __('Failed to send an e-mail. Please contact us directly on','passwords-manager'),

					'security_nonce' => wp_create_nonce( 'security_nonce' ),
					'security_nonce' => wp_create_nonce( 'security_nonce' ),
					'add_password' => __('Add Password','passwords-manager'),
					'edit_password' => __('Edit Password','passwords-manager'),
					'add' => __('Add','passwords-manager'),
					'edit' => __('edit','passwords-manager'),
					'add_category' => __('Add Category','passwords-manager'),
					'edit_category' => __('Edit Category','passwords-manager'),
					'del_cat_txt' => __('Deleting this category will also delete all passwords belonging to this category. Are you sure you want to do this?','passwords-manager'),
					'del_pswd_txt' => __('Are you sure you want to do this?','passwords-manager'),
					'cnfrm_btn_text' => __('Yes, delete it!','passwords-manager'),
					'cncl_btn_text' => __('Cancel','passwords-manager'),
					'deleted_title' => __('Deleted','passwords-manager'),
					'deleted_text' => __('Your file has been deleted.','passwords-manager'),
					'shw_menu' => __('Show','passwords-manager'),
					'prev_pg' => __('Previous','passwords-manager'),
					'nxt_pg' => __('Next','passwords-manager'),
					'search_bar' => __('Search','passwords-manager'),
					'no_records' => __('No matching records found','passwords-manager'),
					'filter_record' => sprintf(__('Showing %s to %s entries / Total %s entries','passwords-manager'),'_START_','_END_','_TOTAL_'),
					'empty_record' => __('Showing 0 to 0 of 0 entries','passwords-manager'),
					'cloned' => __('Cloned','passwords-manager'),
					'yes' => __('Yes','passwords-manager'),
					'leave_feedback' => __('Leave plugin developers any feedback here...','passwords-manager'),


				));	
			}
			add_action( 'admin_enqueue_scripts', 'pwdms_add_admin_scripts' );
		}
	}else{
		function pwdms_front_add_admin_scripts() {
			/** 
			** Front user Style
			**/
			wp_enqueue_style(PWDMS_NAME . '_fontawesome_min', PWDMS_ASSETS . 'libs/fontawesome/all.css', array(), PWDMS_VAR);
			wp_enqueue_style(PWDMS_NAME . '_front', PWDMS_ASSETS . 'css/pms-front.css', array(), PWDMS_VAR);
			/** 
			** Front user Script
			**/
			wp_enqueue_script(PWDMS_NAME . '_clipboard',  includes_url() . '/js/clipboard.min.js', array('jquery'), PWDMS_VAR, true);
			wp_enqueue_script(PWDMS_NAME . '_datatable', PWDMS_ASSETS . 'libs/datatable/datatables.min.js', array('jquery'), PWDMS_VAR, true);
			wp_enqueue_script(PWDMS_NAME . '_crypto', PWDMS_ASSETS . 'js/crypto.js', array('jquery'), PWDMS_VAR, true);
			wp_enqueue_script(PWDMS_NAME . '_encry', PWDMS_ASSETS . 'js/encryption.js', array('jquery'), PWDMS_VAR, true);
			wp_enqueue_script(PWDMS_NAME . '_front', PWDMS_ASSETS . 'js/pms-front.js', array('jquery'), PWDMS_VAR, true);
			wp_enqueue_script(PWDMS_NAME . '_category', PWDMS_ASSETS . 'js/pms-category.js', array('jquery'), PWDMS_VAR, true);	
			$admin_url = strtok( admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ), '?' );
			wp_localize_script(PWDMS_NAME . '_front', 'MyAjax', array( 
				'ajaxurl' => $admin_url,
				'no_export_data' => 'There are no exporting data in your selection fields',
				'security_nonce' => wp_create_nonce( 'security_nonce' ),
			));					
		}		
		add_action( 'wp_enqueue_scripts', 'pwdms_front_add_admin_scripts' );

		function pwdms_admin_shortcode_scripts() {
			wp_enqueue_style(PWDMS_NAME . '_shortcode', PWDMS_ASSETS . 'css/pms-shortcode.css', array(), PWDMS_VAR);
		}
		add_action( 'wp_enqueue_scripts', 'pwdms_admin_shortcode_scripts' );
	}