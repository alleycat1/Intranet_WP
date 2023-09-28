<?php
// Disable direct call
if ( !defined( 'ABSPATH' ) ) { exit; }

// Define component's subfolder
if ( !defined('TRX_ADDONS_PLUGIN_OCDI') ) define('TRX_ADDONS_PLUGIN_OCDI', TRX_ADDONS_PLUGIN_COMPONENTS . 'ocdi/');



// Check if plugin is installed and activated
if ( !function_exists( 'trx_addons_exists_ocdi' ) ) {
	function trx_addons_exists_ocdi() {
		return class_exists( 'OCDI_Plugin' );
	}
}
		
// Load ocdi scripts for the backend
if (!function_exists('trx_addons_ocdi_scripts_admin')) {
	add_action( 'admin_enqueue_scripts', 'trx_addons_ocdi_scripts_admin', 1 );	
	function trx_addons_ocdi_scripts_admin() {  
		if (is_admin() && trx_addons_exists_ocdi()) { 	 
			wp_enqueue_style( 'ocdi-css', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_OCDI . 'ocdi.css'), array(), null );
			wp_enqueue_script( 'ocdi-js', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_OCDI . 'ocdi.js'), array('jquery'), null, true );
			wp_enqueue_script( 'ocdi-main-js', PT_OCDI_URL . 'assets/js/main.js', array('jquery', 'ocdi-js'), null, true );
		}
	}
}

// Get path of import files
if (!function_exists('trx_addons_ocdi_import_files')){
	add_filter( 'pt-ocdi/import_files', 'trx_addons_ocdi_import_files' );
	function trx_addons_ocdi_import_files() {
		return array(trx_addons_ocdi_options());
	}
}

// OCDI options
if (!function_exists('trx_addons_ocdi_options')){
	function trx_addons_ocdi_options($name='') {
		static $ocdi_options = false;
		if ( $ocdi_options === false ) {
			$ocdi_options = array(
							  'import_file_name'			=> esc_html__('Demo Import', 'trx_addons'),
							  'categories'					=> array(),
							  'import_file_url'				=> 'posts.xml',	  
							  'import_site_data_file_url'	=> 'site-data.txt',
							  'import_uploads_file_url'		=> 'uploads.txt',
							  'demo_timeout'				=> 1200,
							  'demo_url'					=> '',
							  'required_plugins'			=> '',
							  'files'						=> array(),
							  'title'						=> '',
							  'domain_demo'					=> '',
							);
			$ocdi_options = apply_filters('trx_addons_filter_ocdi_options', $ocdi_options);
			
			// Add url to import files
			$ocdi_options['demo_type'] = get_option('trx_addons_ocdi_demo_type', trx_addons_array_get_first($ocdi_options['files']));
			if ( !empty($ocdi_options['demo_type']) ) {
				$ocdi_options['demo_url'] = $ocdi_options['demo_url'] .  $ocdi_options['demo_type'] . '/';
				$ocdi_options['title'] = $ocdi_options['files'][$ocdi_options['demo_type']]['title'];
				$ocdi_options['domain_demo'] = $ocdi_options['files'][$ocdi_options['demo_type']]['domain_demo'];
				$ocdi_options = trx_addons_ocdi_import_files_url($ocdi_options['demo_url'], $ocdi_options);
			}
		}
		if ( empty($name) )
			return $ocdi_options;
		elseif ( isset($ocdi_options[$name]) )
			return $ocdi_options[$name];
		else
			wp_die(__("Undefined OCDI option '{$name}'", 'trx_addons'));
	}
}

// Add url to import files
if (!function_exists('trx_addons_ocdi_import_files_url')) {
	function trx_addons_ocdi_import_files_url($demo_url, $array){
		$new_array = $array;
		foreach ($array as $k => $v){
			if (substr($k, -8) == 'file_url'){
				if (is_array($v))
					$v = trx_addons_ocdi_import_files_url($demo_url, $v);
				else 
					$new_array[$k] = $demo_url . $v ;
			}
		}
		return $new_array;
	}
}


// Export
//------------------------------------------------------------------------

// Display export files
if (!function_exists('trx_addons_ocdi_export_files')) {
	add_action( 'export_filters', 'trx_addons_ocdi_export_files' );
	function trx_addons_ocdi_export_files(){
		if (is_admin() && trx_addons_exists_ocdi()) {
			$output = apply_filters( 'trx_addons_filter_ocdi_export_files', '' );
			if(!empty($output))
				echo '<h3>' . esc_html__('Download an additional export data:', 'trx_addons') . '</h3>' . $output;
			else 
				echo esc_html__('There is no additional export data.', 'trx_addons');
		}
	}
}

// Export tables
if (!function_exists('trx_addons_ocdi_export_tables')) {
	function trx_addons_ocdi_export_tables($tables, $list) { 
		global $wpdb;
		$importer = new trx_addons_demo_data_importer();
		foreach($tables as $table_name){
			// Check if table exists
			$num_rows = count($wpdb->get_results( $wpdb->prepare("SHOW TABLES LIKE %s", $wpdb->prefix . $table_name), ARRAY_A )) == 1;
			if ( $num_rows != 0 ){
				// Get content from table
				$rows = $wpdb->get_results( "SELECT * FROM " . esc_sql($wpdb->prefix . $table_name), ARRAY_A );
				if ( $table_name == 'users' ){
					// Change user pass and key
					$users_rows = array();
					foreach( $rows as $row ) {	
						$row['user_pass']	 		= '';
						$row['user_activation_key'] = '';
						$row['display_name']		= sprintf(esc_html__('User %d', 'trx_addons'), $row['ID']);
						$row['user_email']			= sprintf('user%s',$row['ID']) . '@user-mail.net';
						$users_rows[] = $row;
					} 
					$list['tables']['users'] = $importer->prepare_data($users_rows);		
				} else {
					$list['tables'][$table_name] = $importer->prepare_data($rows);	
				}
			}
		}
		return $list;
	}
}

// Export options
if (!function_exists('trx_addons_ocdi_export_options')) {
	function trx_addons_ocdi_export_options($options, $list) { 
		global $wpdb;
		$importer = new trx_addons_demo_data_importer();
		foreach($options as $option_name){
			if (strpos($option_name, '%') !== false) {
				$rows = $wpdb->get_results( "SELECT option_name, option_value FROM " . esc_sql($wpdb->prefix . 'options') . " WHERE option_name LIKE '{$option_name}'", ARRAY_A );
				foreach($rows as $row) {
					$list['options'][$row['option_name']][]['option_value'] = $importer->prepare_data($row['option_value']);	
				}
			} else {			
				$rows = $wpdb->get_results( "SELECT option_value FROM " . esc_sql($wpdb->prefix . 'options') . " WHERE option_name = '{$option_name}'", ARRAY_A );
				$list['options'][$option_name] = $importer->prepare_data($rows);	
				
				// Export attachments from Theme Options
				if ( $option_name == 'theme_mods_' . get_stylesheet() ) {
					$list['theme_options'] = trx_addons_ocdi_theme_opt_attach($rows);	
				}
			}
		}
		return $list;	
	}
}

// Export postmeta
if (!function_exists('trx_addons_ocdi_export_postmeta')) {
	function trx_addons_ocdi_export_postmeta($keys, $list) { 
		global $wpdb;
		$importer = new trx_addons_demo_data_importer();
		foreach($keys as $key){
			$rows = $wpdb->get_results( "SELECT meta_id, meta_value FROM " . esc_sql($wpdb->prefix . 'postmeta') . " WHERE meta_key = '{$key}' AND meta_value != '' AND meta_value != '#'", ARRAY_A );
			foreach($rows as $row) {
				$list['postmeta'][$row['meta_id']][][$key] = $importer->prepare_data($row['meta_value']);	
			}	
		}
		return $list;
	}
}

// Get attachments from Theme Options
if (!function_exists('trx_addons_ocdi_theme_opt_attach')) {	
	function trx_addons_ocdi_theme_opt_attach($rows){
		global $wpdb;
		$list = array();
		$fields = trx_addons_unserialize($rows[0]['option_value']);					
		foreach ($fields as $option => $value) {		
			if(is_int($value) && $value > 0) {	
				// Get attachment url
				$attachment = $wpdb->get_col($wpdb->prepare("SELECT guid FROM " . esc_sql($wpdb->prefix . 'posts') . " WHERE id = '%s';", $value ));
				if($attachment) {
					$image_url = $attachment[0];
					$list[$option] = stristr($image_url, 'wp-content/uploads');
				}
			}		
		}
		return $list;
	}
}

// Import
//------------------------------------------------------------------------
// Display text on OCDI import page
if (!function_exists('trx_addons_ocdi_intro_text')){
	if (is_admin()) add_filter( 'pt-ocdi/plugin_intro_text', 'trx_addons_ocdi_intro_text' );	
	function trx_addons_ocdi_intro_text($output) {
		$output .= 
		'<div class="trx_addons_ocdi_intro_text">';
		
		// Display demo type
		$files = trx_addons_ocdi_options('files');
		$current_demo_type = get_option( 'trx_addons_ocdi_demo_type', '' );
		if (count($files) > 1){
			$output .= 
			'<div class="ocdi_demo_type">
				<h4>'. esc_html__( 'Select the demo to be imported', 'trx_addons' ) .'</h4>
				<select class="demo_type">';	
			foreach($files as $type => $value){
				$output .= 
				'<option value="'. esc_attr($type) .'"'. ($current_demo_type == $type ? ' selected' : '') .'>'. esc_html($value['title']) .'</option>';
			}			
			$output .= 
				'<select>
			</div>';		
		}
		
		// Display import components
		$output .= 		
			'<div class="ocdi_import_components">
				<h4>'. esc_html__( 'When you import the data, the following things might happen:', 'trx_addons' ) .'</h4>
				<label><input type="radio" value="full" name="import_type" checked>'.  esc_html__( 'The whole demo-site content might be imported instead of the posts and pages', 'trx_addons' ) .'</label>
				<div class="ocdi_import_components_descr">
					<ol>
						<li>'.  __( '<b>Attention!</b> In this case, <b>all the old data will be erased and you will get a new set of posts, pages, and menu items</b> - a complete copy of our demo site.', 'trx_addons' ) .'</li>
						<li>'.  __( '<b>The import of full demo-site content is strongly recommended ONLY FOR NEW INSTALLATIONS of WordPress</b> (without posts, pages, and any other data).', 'trx_addons' ) .'</li>	
						<li>'.  __( 'The import of some components (such as Revolution Sliders, Essential Grid galleries, etc.) may take quite a long time - <b>please wait until the end of the procedure, do not navigate away from this page</b>.', 'trx_addons' ) .'</li>
					</ol>
				</div>
				<label><input type="radio" value="update" name="import_type">'.  esc_html__( 'Updating or adding the content that does not exist on your website.', 'trx_addons' ) .'</label>						
				<div class="ocdi_import_components_descr">
					<ol>
						<li>'.  __( 'In this case, the posts, pages, categories, images, custom post types or any other data on your website <b>will not be deleted or modified</b>. ', 'trx_addons' ) .'</li>
						<li>'.  esc_html__( 'The content that does not exist on your website will be imported.', 'trx_addons' ) .'</li>
						<li>'.  esc_html__( 'The theme settings and the plugins settings will be updated.', 'trx_addons' ) .'</li>
					</ol>
				</div>';
		
		// Allow the "input" tag display in dashboard
		global $allowedposttags;
		$allowed_atts = array(
			'class'     => array(),
			'type'      => array(),
			'id'        => array(),
			'value'     => array(),
			'name'      => array(),
			'selected'  => array(),
			'checked'   => array(),
		);
		$allowedposttags['input'] = $allowed_atts;	
		$allowedposttags['select'] = $allowed_atts;	
		$allowedposttags['option'] = $allowed_atts;	
		
		// Display elements to import
		$elements_list = apply_filters('trx_addons_filter_ocdi_import_fields', '');
		$elements_list .= '<label><input type="checkbox" name="uploads" value="uploads">'. esc_html__( 'Import media', 'trx_addons' ).'</label><br/>';
		$output .= !empty($elements_list) ? 
				'<h4>'. esc_html__('Select the elements to be imported:', 'trx_addons') .'</h4>' . $elements_list : '';
		$output .= 
			'</div>
		</div>';
		
		return $output;
	}
}

// Change demo type
if (!function_exists('trx_addons_ocdi_demo_settings_change')) {
	add_action( 'wp_ajax_trx_addons_ocdi_demo_settings_change', 'trx_addons_ocdi_demo_settings_change' );
	function trx_addons_ocdi_demo_settings_change(){			
		trx_addons_verify_nonce();

		// Update demo type
		$ocdi_demo_type = trx_addons_get_value_gp('trx_addons_ocdi_demo_type', trx_addons_ocdi_options('demo_type'));
		if (!empty($ocdi_demo_type)) {			
			update_option( 'trx_addons_ocdi_demo_type', $ocdi_demo_type );
		} 
		
		// Update import type
		$ocdi_import_type = trx_addons_get_value_gp('trx_addons_ocdi_import_type');
		if (!empty($ocdi_import_type)) {
			update_option( 'trx_addons_ocdi_import_type', $ocdi_import_type );
		}	
		
		// Get selected plugins 
		$ocdi_import_elements = trx_addons_get_value_gp('trx_addons_ocdi_import_elements');
		if (!empty($ocdi_import_elements)) {
			update_option( 'trx_addons_ocdi_import_elements', explode('|', $ocdi_import_elements ) );		
		}
		
		$response = array('error' => '');
		
		if (empty($ocdi_demo_type) || empty($ocdi_import_type) || empty($ocdi_import_elements)){
			$response['error'] = esc_html__('Something going wrong. Please refresh page.', 'trx_addons');
		}
		
		header("Refresh:0");

		trx_addons_ajax_response( $response );
	}
}

// Before content import action
if (!function_exists('trx_addons_ocdi_before_content_import')) {
	if (is_admin()) add_action( 'pt-ocdi/before_content_import', 'trx_addons_ocdi_before_content_import' );
	function trx_addons_ocdi_before_content_import(){		
		// Change max_execution_time (if allowed by server)
		$admin_tm = max(0, min(1800, trx_addons_ocdi_options('demo_timeout')));
		$tm = max(30, (int) ini_get('max_execution_time'));
		if ($tm < $admin_tm) {
			@set_time_limit($admin_tm);
		}
		
		// Delete all data from tables
		$import_type = get_option( 'trx_addons_ocdi_import_type', '' );
		if ($import_type == 'full'){
			trx_addons_ocdi_clear_tables(); 
		}	
		
		// Download uploads zip and exctract it
		$import_elements = get_option( 'trx_addons_ocdi_import_elements', array() );
		if (!empty($import_elements) && in_array('uploads', $import_elements)){
			$uploads = basename(trx_addons_ocdi_options('import_uploads_file_url'));		
			$importer = new trx_addons_demo_data_importer();
			$importer->import_uploads($uploads, 'ocdi');
		}
			
		// Import Options, Users, Terms and Widgets
		trx_addons_ocdi_import_site_data();
	}
}

// Delete all data from tables
if (!function_exists('trx_addons_ocdi_clear_tables')) {
	function trx_addons_ocdi_clear_tables(){ 
		global $wpdb;
		$tables = array('posts', 'postmeta', 'comments', 'commentmeta', 'terms', 'term_relationships', 'term_taxonomy');
		foreach($tables as $table) {
			$res = $wpdb->query("TRUNCATE TABLE " . esc_sql($wpdb->prefix . $table) );
			if ( is_wp_error( $res ) ) dfl( __( 'Failed truncate table '. $table .'.', 'trx_addons' ) . ' ' . ($res->get_error_message()) );
		}
	}
}

// After import action
if (!function_exists('trx_addons_ocdi_after_import')) {
	if (is_admin()) add_action( 'pt-ocdi/after_import', 'trx_addons_ocdi_after_import' );
	function trx_addons_ocdi_after_import(){
		$import_elements = get_option( 'trx_addons_ocdi_import_elements', array() );		
		if(!empty($import_elements)){
			// Import selected plugins 
			do_action( 'trx_addons_action_ocdi_import_plugins', $import_elements );		
		
			// Process post meta 
			$keys = array('trx_addons_options', 'trx_addons_edd_demo_url',  get_stylesheet() . '_options', '_menu_item_url', '_wpb_shortcodes_custom_css', 'panels_data'); 	
			$keys = apply_filters('trx_addons_filter_ocdi_process_post_meta', $keys, $import_elements); 
			$list = trx_addons_ocdi_export_postmeta($keys, array());
			trx_addons_ocdi_import_dump('', $list);
		} 
	}
} 

// Disable the download of attachments
if (!function_exists('trx_addons_ocdi_importer_options')){
	add_filter( 'pt-ocdi/importer_options', 'trx_addons_ocdi_importer_options' );
	function trx_addons_ocdi_importer_options() {
		return array( 'fetch_attachments' => false );
	}
}

// Insert an attachment into the media library
if (!function_exists('trx_addons_ocdi_process_attachment')){
	add_filter( 'wp_import_post_data_processed', 'trx_addons_ocdi_process_attachment', 10, 2 );
	function trx_addons_ocdi_process_attachment($post, $data) {
		$import_elements = get_option( 'trx_addons_ocdi_import_elements', array() );
		if ( 'attachment' === $post['post_type'] && !empty($import_elements) && in_array('uploads', $import_elements) ) {	
			$domain_demo = trx_addons_ocdi_options('domain_demo');
			$attachment_url = ! empty( $data['attachment_url'] ) ? $data['attachment_url'] : $data['guid'];
			$attachment_url = trx_addons_ocdi_replace_site_url($attachment_url, $domain_demo);			
			$attachment_path = $_SERVER['DOCUMENT_ROOT'] . parse_url( $attachment_url, PHP_URL_PATH );
			
			// Retrieve the file type from the file name
			$info = wp_check_filetype( $attachment_url );
			$post['post_mime_type'] = $info['type'];
			
			// Insert an attachment
			$id = wp_insert_attachment( $post, $attachment_path );	
		
			// Regenerate thumbnails
			wp_update_attachment_metadata( $id,  wp_generate_attachment_metadata( $id, $attachment_path ) );			
		}
		return $post;
	}
}

// Replace site url to current site url
if (!function_exists('trx_addons_ocdi_replace_site_url')) {
	function trx_addons_ocdi_replace_site_url($str, $old_url = '') {
		$site_url = get_home_url();
		if (substr($site_url, -1) == '/') {
			$site_url = substr($site_url, 0, strlen($site_url)-1);
		}
		if (substr($old_url, -1) == '/') {
			$old_url = substr($old_url, 0, strlen($old_url)-1);
		}
		return trx_addons_str_replace(
					array(
						$old_url,
						str_replace('/', '\\/', $old_url),
						trx_addons_remove_protocol($old_url),
						str_replace('/', '\\/', trx_addons_remove_protocol($old_url)),
						trx_addons_remove_protocol($old_url, true),
						str_replace('/', '\\/', trx_addons_remove_protocol($old_url, true))
						),
					array(
						$site_url,
						str_replace('/', '\\/', $site_url),
						trx_addons_remove_protocol($site_url),
						str_replace('/', '\\/', trx_addons_remove_protocol($site_url)),
						trx_addons_remove_protocol($site_url, true),
						str_replace('/', '\\/', trx_addons_remove_protocol($site_url, true))
						),
					$str
				);
	}
}
	
// Import any SQL dump
if (!function_exists('trx_addons_ocdi_import_dump')) {
	function trx_addons_ocdi_import_dump($slug = '', $data = array()) { 		
		global $wpdb;

		$domain_demo = trx_addons_ocdi_options('domain_demo');
		
		// Get an import file url
		if ($slug != '') {
			$file_txt = trx_addons_ocdi_options("import_{$slug}_file_url");
			$file_txt = trx_addons_fgc($file_txt);
			$data = trx_addons_unserialize( $file_txt );
		}
			
		// Read data
		if (is_array($data) && count($data) > 0) {		
			foreach ($data as $type => $content) {			
				foreach ($content as $table => $rows) {
					$values = $fields = $key = '';
					if (is_array($rows) && count($rows) > 0) {
						foreach ($rows as $row) {
							$f = $v = '';
							$row = trx_addons_ocdi_replace_site_url($row, $domain_demo);
							if (is_array($row) && count($row) > 0) {
								foreach ($row as $field => $value) {
									$key = esc_sql($field);
									$f .= ($f ? ',' : '') . "'" . esc_sql($field) . "'";
									$v .= ($v ? ',' : '') . "'" . esc_sql($value) . "'";
								}
							}
							if ($fields == '') $fields = '(' . $f . ')';
							$values .= ($values ? ',' : '') . '(' . $v . ')';
						}
					}
					
					// Attention! All items in the variable $values escaped on the loop above - esc_sql($value)
					// Import tables
					if($type == 'tables'){	
						// Check if table exists
						$num_rows = count($wpdb->get_results( $wpdb->prepare("SHOW TABLES LIKE %s", $wpdb->prefix . $table), ARRAY_A )) == 1;
						if( $num_rows > 0 ){
							// Clear table
							if (!in_array($table, array('users', 'usermeta'))) {
								$wpdb->query("TRUNCATE TABLE " . esc_sql($wpdb->prefix . $table));		
							}
							
							// Insert content into table
							if(!empty($values)) {
								$wpdb->query("INSERT IGNORE INTO ".esc_sql($wpdb->prefix . $table)." VALUES {$values}");
							}			
						}			
					}
					
					// Import options
					else if($type == 'options'){		
						$option_name = $table;
						// Create option if not exist
						if(get_option($option_name) == false)  
							add_option($option_name, '');						 
						$wpdb->query("UPDATE ".esc_sql($wpdb->prefix . 'options')." SET option_value = {$values} WHERE option_name = '{$option_name}'");
					}
					
					// Import postmeta
					else if($type == 'postmeta'){	
						$meta_id = $table;
						$wpdb->query("UPDATE ".esc_sql($wpdb->prefix . 'postmeta')." SET meta_value = {$values} WHERE meta_id = '{$meta_id}' AND meta_key = '{$key}'");			
					}
					
					// Change attachment id in Theme Options
					else if ($type == 'theme_options'){		
						$option = $table; // custom_logo
						$image_url = '%'.$rows;
						$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM " . esc_sql($wpdb->prefix . 'posts') . " WHERE guid LIKE '%s'", $image_url));						
						if(is_array($attachment) && count($attachment) > 0){
							$image_id = $attachment[0];							
							set_theme_mod( $option, $image_id );
						}
					}
				}
			}
		}
	}
}

// Options, Users, Terms and Widgets
//------------------------------------------------------------------------
// Export Options, Users, Terms and Widgets
if (!function_exists('trx_addons_ocdi_export_site_data')) {
	add_filter( 'trx_addons_filter_ocdi_export_files', 'trx_addons_ocdi_export_site_data' );
	function trx_addons_ocdi_export_site_data($output){		
		// Get wp settings from database
		global $wpdb;
		$list = array();		
		
		// Export tables 
		$tables = array('users', 'usermeta', 'terms', 'termmeta', 'term_relationships', 'term_taxonomy');
		$list = trx_addons_ocdi_export_tables($tables, $list);
		
		// Export options and widgets
		$options = array('blogname', 'blogdescription', 'posts_per_page', 'page_on_front', 'show_on_front', 'page_for_posts', 'theme_mods_' . get_stylesheet(), 'sidebars_widgets', 'trx_addons_widgets_areas', 'trx_addons_options', 'widget_%' );
		$list = trx_addons_ocdi_export_options($options, $list);	
		
		// Save as file
		$file_path = TRX_ADDONS_PLUGIN_OCDI . "export/site-data.txt";
		trx_addons_fpc(trx_addons_get_file_dir($file_path), serialize($list));
		
		// Return file path
		$output .= '<h4><a href="'. trx_addons_get_file_url($file_path).'" download>'. esc_html__('Export Options, Users, Terms and Widgets', 'trx_addons') .'</a></h4>';
		return $output;
	}
}

// Export Options, Users, Terms and Widgets
if (!function_exists('trx_addons_ocdi_import_site_data')) {	
	function trx_addons_ocdi_import_site_data(){
		trx_addons_ocdi_import_dump('site_data');
		echo esc_html__('Site data import complete.', 'trx_addons') . "\r\n";	
	}
}
