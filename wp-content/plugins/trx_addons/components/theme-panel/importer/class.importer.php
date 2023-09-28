<?php
// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

class trx_addons_demo_data_importer {

	// Theme specific settings
	var $options = array(
		'stand_alone'	=> false,									// Add menu item (true) or show inside Theme Panel (false)
		'allow_import'	=> true,									// Allow import functionality
		'allow_export'	=> true,									// Allow export functionality
		'debug'			=> false,									// Enable debug output
		'demo_style'	=> 1,										// 1 | 2 - Progress bar style when import demo data
		'demo_timeout'	=> 1200,									// Timeframe for PHP scripts when import demo data
		'demo_type'		=> 'default',								// Default demo data type
		'demo_set'		=> 'part',									// full | part - Default demo data set
		'demo_parts'	=> '',										// Comma separated list of the checked items to be imported
		'demo_pages'	=> array(),									// List of the checked pages to be imported
		'demo_url'		=> '',										// URL or local path to the folder with demo data
		'files'			=> array(									// Demo data files: path to the local file with demo content
																	// or URL from external (cloud) server
			'default'	=> array(
				'title'				=> '',						// Installation title ('Light version', 'Portfolio style', etc.)
																// MUST BE SET IN THE THEME!
				'file_with_'		=> 'name.ext',				// Placeholder of the file with data to create new entries
				'file_with_posts'	=> 'posts.txt',				// File with posts content
				'file_with_users'	=> 'users.txt',				// File with users
				'file_with_mods'	=> 'theme_mods.txt',		// File with theme options: WP modifications
				'file_with_options'	=> 'theme_options.txt',		// File with plugins settings: ThemeREX Addons and other plugins options
				'file_with_widgets' => 'widgets.txt',			// File with widgets data
				'file_with_uploads' => 'uploads.txt',			// File with attachments data: list of the archive's parts or files
				'domain_dev'		=> '',						// Domain on the developer's server
																// MUST BE SET IN THE THEME!
				'domain_demo'		=> ''						// Domain on the demo-server
																// MUST BE SET IN THE THEME!
			)
		),
		'ignore_post_types'		=> array(						// Ignore specified post types when export posts and postmeta
			'revision'
		),
		'set_permalinks'		=> true,						// Change permalink structure to 'Post name' after demo data installation
		'regenerate_thumbnails' => 3,							// Set number of thumbnails to regenerate when its imported
																// (if demo data was zipped without cropped images)
																// Set 0 to prevent regenerate thumbnails 
																// (if demo data archive is already contain cropped images)
		'banners'				=> array(),						// List of banners to display its during import demo-data
																// MUST BE SET IN THE THEME!
		'required_plugins'		=> array(),						// List of the required plugins
																// MUST BE SET IN THE THEME!
		'plugins_initial_state'	=> 0,							// The initial state of the plugin's checkboxes: 1 - checked, 0 - unchecked
																// MUST BE SET OR CHANGED IN THE THEME!
		'taxonomies'			=> array(),						// List of the required taxonomies: 'post_type' => 'taxonomy', ...
																// MUST BE SET OR CHANGED IN THE THEME!
		'additional_options'	=> array(						// Additional options slugs (for export plugins settings)
																// MUST BE SET OR CHANGED IN THE THEME!
			// ThemeREX Addons options
			'trx_addons_%',

			// WP options
			'blogname',
			'blogdescription',
			'site_icon',
			'posts_per_page',
			'show_on_front',
			'page_on_front',
			'page_for_posts',
			'sticky_posts',
			'wp_page_for_privacy_policy'
		),
		'skip_options'			=> array()						// Skip options slugs (do not export this)
																// MUST BE SET OR CHANGED IN THE THEME!
	);

	var $error    = '';				// Error message
	var $result   = 0;				// Import posts percent (if break inside)

	var $action 	= '';			// Current AJAX action

	var $uploads_url = '';
	var $uploads_dir = '';

	var $start_time = 0;
	var $max_time = 0;
	
	var $part_replace = array();	// List of ID to be replaced after particular import
	var $part_image = array();		// Uploaded no-image.jpg to replace all images on the pages (if 'demo_set' == 'part')
	
	var	$response = array(
			'action' => '',
			'error' => '',
			'start_from_id' => 0,
			'result' => 100
		);

	//-----------------------------------------------------------------------------------
	// Constuctor
	//-----------------------------------------------------------------------------------
	function __construct() {
		// Add menu item
		if ( ! function_exists('trx_addons_exists_ocdi') || ! trx_addons_exists_ocdi()) {
			add_filter('trx_addons_filter_add_theme_panel_pages',		array($this, 'add_theme_panel_page'));
			add_filter('trx_addons_filter_theme_panel_tabs',			array($this, 'add_theme_panel_tab'), 12);
			add_filter('trx_addons_filter_theme_panel_steps',			array($this, 'add_theme_panel_step'));
			add_action('trx_addons_action_theme_panel_section',			array($this, 'add_theme_panel_section'), 10, 2);
		}
		// Add menu item
		add_action('trx_addons_action_load_scripts_admin',				array($this, 'admin_scripts'));
		add_action('trx_addons_action_load_scripts_admin',				array($this, 'admin_scripts_rtl'), 100);
		add_filter('trx_addons_filter_localize_script_admin',			array($this, 'admin_scripts_localize'));
		// AJAX handler of the import actions
		add_action('wp_ajax_trx_addons_importer_start_import',			array($this, 'importer'));
		// AJAX handler of the get_list_pages actions
		add_action('wp_ajax_trx_addons_importer_get_list_pages',		array($this, 'get_list_pages_callback'));
		// Check if row will be imported in the set='part'
		add_filter('trx_addons_filter_importer_import_row',				array($this, 'import_check_row'), 9, 4);
		// Clear API keys while export demo data
		add_filter('trx_addons_filter_export_options',					array($this, 'export_clear_api_keys'));
	}

	function prepare_vars() {
		// Detect current uploads folder and url
		$uploads_info = wp_upload_dir();
		$this->uploads_dir = $uploads_info['basedir'];
		$this->uploads_url = $uploads_info['baseurl'];
		// Filter importer options
		$this->options['debug'] = trx_addons_is_on(trx_addons_get_option('debug_mode'));
	    $this->options = apply_filters('trx_addons_filter_importer_options', $this->options);
		// Check if demo data present in the theme folder
		$demo_dir = get_template_directory() . '/demo';
		if (is_dir($demo_dir)) {
			$this->options['demo_url'] = trailingslashit($demo_dir);
		} else if (get_template_directory() != get_stylesheet_directory()) {
			$demo_dir = get_stylesheet_directory() . '/demo';
			if (is_dir($demo_dir)) $this->options['demo_url'] = trailingslashit($demo_dir);
		}
		// Get allowed execution time
		$this->start_time = time();
		$this->max_time = round( 0.9 * max(30, ini_get('max_execution_time')));
		// Get current percent
		$this->result = isset($_POST['result']) ? $_POST['result'] : 0;
		// Type of the demo data
		if (isset($_POST['demo_type']))
			$this->options['demo_type'] = $_POST['demo_type'];
		// Set of the demo data
		if (isset($_POST['demo_set']))
			$this->options['demo_set'] = $_POST['demo_set'];
		// Parts to be imported
		if (isset($_POST['demo_parts']))
			$this->options['demo_parts'] = $_POST['demo_parts'];
		// Pages to be imported
		if (isset($_POST['demo_pages']))
			$this->options['demo_pages'] = explode(',', $_POST['demo_pages']);
	}

	//-----------------------------------------------------------------------------------
	// Admin Interface
	//-----------------------------------------------------------------------------------
	
	// Add page to the Theme Panel menu
	function add_theme_panel_page($list) {
		if ( current_user_can( 'manage_options' ) && $this->options['stand_alone'] ) {
			$list[] = array(
				esc_html__('Install Demo Data', 'trx_addons'),
				esc_html__('Install Demo Data', 'trx_addons'),
				'edit_theme_options',
				'trx_importer',
				array($this, 'build_page')
			);
		}
		return $list;
	}


	// Add step to the Theme Panel
	function add_theme_panel_step( $steps ) {
		trx_addons_array_insert_after( $steps, 'plugins', array( 'demo' => esc_html__( 'Demo Data Import', 'trx_addons' ) ) );
		return $steps;
	}


	// Add tab to the Theme Panel
	function add_theme_panel_tab( $tabs ) {
		trx_addons_array_insert_after( $tabs, 'plugins', array( 'demo' => esc_html__( 'Demo Data', 'trx_addons' ) ) );
		return $tabs;
	}

	// Add section to the Theme Panel
	function add_theme_panel_section($tab_id, $theme_info) {
		if ( $tab_id !== 'demo' ) return;
		if ( !current_user_can( 'manage_options' ) || $this->options['stand_alone'] ) return;
		?>
		<div id="trx_addons_theme_panel_section_<?php echo esc_attr($tab_id); ?>" class="trx_addons_tabs_section">
			<?php
			do_action('trx_addons_action_theme_panel_section_start', $tab_id, $theme_info);
			?>
			<div class="trx_addons_theme_panel_section_content">
				<?php
				if ( trx_addons_is_theme_activated() ) {
					$this->build_page($tab_id, $theme_info);
				} else {
					?>
					<div class="trx_addons_info_box trx_addons_info_box_warning"><p>
						<?php esc_html_e( 'Activate your theme in order to be able to install demo data.', 'trx_addons' ); ?>
					</p></div>
					<?php
				}
				?>
			</div>
			<?php
			do_action('trx_addons_action_theme_panel_section_end', $tab_id, $theme_info);
			?>
		</div>
		<?php
	}

	// Add script
	function admin_scripts( $all = false ) {
		wp_enqueue_style(  'trx_addons-importer', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_IMPORTER . 'importer.css'), array(), null );
		wp_enqueue_script( 'trx_addons-importer', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_IMPORTER . 'importer.js'), array('jquery'), null, true );
	}

	// Add RTL styles
	function admin_scripts_rtl( $all = false) {
		if ( is_rtl() ) {
			wp_enqueue_style( 'trx_addons-importer-rtl', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_IMPORTER . 'importer-rtl.css'), array(), null );
		}
	}

	// Localize script
	function admin_scripts_localize($vars) {
		$vars['msg_importer_error']      = esc_html__('Problem(s) that occurred during the import process:', 'trx_addons');
		$vars['msg_importer_full_alert'] = '<p>' 
										. esc_html__("In this case ALL OF THE OLD DATA WILL BE ERASED,\nand YOU WILL GET A NEW SET of posts, pages and menu items.", 'trx_addons')
										. '</p><p>'
										. esc_html__("It is strongly advised to use this option exclusively for new WordPress installations\n(without posts, pages and any other data)!", 'trx_addons')
										. '</p><p>'
										. esc_html__("Press 'OK' to continue or 'Cancel' to return to a partial installation.", 'trx_addons')
										. '</p>';
		return $vars;
	}

	// Return path to the file in the 'export' directory
	function export_file_dir($fname) {
		return trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_IMPORTER . "export/{$fname}");
	}

	// Return url to the file in the 'export' directory
	function export_file_url($fname) {
		return trx_addons_get_file_url(TRX_ADDONS_PLUGIN_IMPORTER . "export/{$fname}");
	}


	//-----------------------------------------------------------------------------------
	// Build the Main Page
	//-----------------------------------------------------------------------------------
	function build_page($tab_id='', $theme_info=array()) {
		
		$this->prepare_vars();

		// Export data
		if ( $this->options['allow_export'] && isset($_POST['exporter_action']) ) {
			if ( !wp_verify_nonce( trx_addons_get_value_gp('nonce'), admin_url() ) )
				$this->error = esc_html__('Incorrect WP-nonce data! Operation canceled!', 'trx_addons');
			else
				$this->exporter();
		}

		?><div class="trx_importer"><?php

			// Section 'Exporter'
			if ($this->options['allow_export']) {
				$this->show_exporter($tab_id, $theme_info);
			}

			// Section 'Importer'
			if ( $this->options['allow_import'] && !isset($_POST['exporter_action']) ) { 
				$this->show_importer($tab_id, $theme_info);
			}

		?></div><?php

		// Banner rotator on the standalone version
		if (empty($tab_id)) {
			trx_addons_show_layout( $this->get_banners_layout($tab_id, $theme_info) );
		}

		do_action('trx_addons_action_theme_panel_after_section_data', $tab_id, $theme_info);
	}

	
	// Section 'Importer'
	function show_importer($tab_id, $theme_info) {
		?><div class="trx_importer_section">

			<?php do_action('trx_addons_action_theme_panel_before_section_title', $tab_id, $theme_info); ?>

			<h1 class="trx_addons_theme_panel_section_title">
				<?php esc_html_e( 'Demo Data Import', 'trx_addons' ); ?>
			</h1>

			<?php
			do_action('trx_addons_action_theme_panel_after_section_title', $tab_id, $theme_info);
			
			// Banners in the tabs
			if ( ! empty($tab_id) ) {
				trx_addons_show_layout( $this->get_banners_layout($tab_id, $theme_info) );
			}

			// Display system info and check requirements (moved to the tab General of the Theme Dashboard)
			$this->show_sys_info( $tab_id, $theme_info );

			// Importer form
			?><form id="trx_importer_form">
				<?php if (count($this->options['files']) > 1) { ?>
					<p class="trx_importer_subtitle"><?php esc_html_e('Select the demo to be imported:', 'trx_addons'); ?></p>
					<div class="trx_importer_demo_type">
						<?php
						foreach ($this->options['files'] as $k=>$v) {
							?><label><input type="radio"<?php if ($this->options['demo_type']==$k) echo ' checked="checked"'; ?> value="<?php echo esc_attr($k); ?>" name="demo_type" /><?php echo esc_html($v['title']); ?></label><?php
						}
						?>
					</div>
					
					<p class="trx_importer_subtitle"><?php esc_html_e('Select the demo-data set to be imported:', 'trx_addons'); ?></p>
				<?php } ?>

				<div class="trx_importer_demo_set">
					<div class="trx_importer_demo_set_controls">
						<label>
							<input type="radio"<?php if ($this->options['demo_set']=='part') echo ' checked="checked"'; ?> value="part" name="demo_set" /><?php esc_html_e('Partial import', 'trx_addons'); ?>
						</label><label>
							<input type="radio"<?php if ($this->options['demo_set']=='full') echo ' checked="checked"'; ?> value="full" name="demo_set" /><?php esc_html_e('Full import', 'trx_addons'); ?>
						</label>
					</div>
					<div class="trx_addons_info_box trx_importer_description trx_importer_description_part<?php if ($this->options['demo_set']!='part') echo ' trx_importer_hidden'; ?>">
						<p><?php
							echo wp_kses_data(
									__('In this case only <b>pages, form layouts and sliders</b> will be added to the existing content.', 'trx_addons')
									. ' ' . __('All images will be replaced with placeholders.', 'trx_addons')
									. ' ' . __('The new pages will not be included in the menu, you have to do it manually.', 'trx_addons')
								);
						?></p>
					</div>
					<div class="trx_addons_info_box trx_addons_info_box_warning trx_importer_description trx_importer_description_full<?php if ($this->options['demo_set']!='full') echo ' trx_importer_hidden'; ?>">
						<p><?php
							echo wp_kses_data(
								__('This step is recommended <b>for new WordPress installations only</b>.', 'trx_addons')
								. ' ' . __('It will <b>irreversibly erase all your current website data</b> and you will get a new set of posts, pages and menu items - a complete copy of our demo site.', 'trx_addons')
							);
						?></p>
					</div>
				</div>

				<div class="trx_importer_advanced_settings"><?php

					// Pages, options, media
					?><div class="trx_importer_advanced_settings_block trx_importer_advanced_settings_pages">
						<p class="trx_importer_subtitle"><?php esc_html_e('Select the elements to be imported:', 'trx_addons'); ?></p>
						<?php
						$this->show_importer_params(array(
							'slug' => 'posts',
							'title' => esc_html__('Import posts and pages', 'trx_addons'),
							'part' => 1,
							'checked' => $this->options['demo_set']=='full',
							'class' => 'trx_importer_separator',
							'atts' => array(
								'data-part-title' => __('Import only selected pages', 'trx_addons')
							)
						));
						?>
						<div class="trx_importer_part_pages<?php if ($this->options['demo_set']=='full') echo ' trx_importer_hidden"'; ?>">
							<?php
								$pages = $this->get_list_pages_from_demo($this->options['demo_type']);
								if (is_array($pages)) {
									foreach ($pages as $id=>$title) {
										?>
										<label class="trx_importer_checkbox_label trx_importer_pages_label">
											<input class="trx_importer_pages" type="checkbox" value="<?php echo esc_attr($id); ?>" name="import_pages_<?php echo esc_attr($id); ?>" id="import_pages_<?php echo esc_attr($id); ?>" />
											<?php echo esc_html( strip_tags( $title ) ); ?>
										</label>
										<?php
									}
								}
							?>
						</div>
						<?php
						$this->show_importer_params(array(
							'slug' => 'users',
							'title' => esc_html__('Import Users', 'trx_addons'),
							'part' => 0,
							'checked' => true,
							'class' => 'trx_importer_separator'
						));

						$this->show_importer_params(array(
							'slug' => 'tm',
							'title' => esc_html__('Import Theme Options', 'trx_addons'),
							'part' => 1,
							'checked' => $this->options['demo_set']=='full'
						));
						$this->show_importer_params(array(
							'slug' => 'to',
							'title' => esc_html__('Import Plugins Settings', 'trx_addons'),
							'part' => 1,
							'checked' => $this->options['demo_set']=='full'
						));
						$this->show_importer_params(array(
							'slug' => 'widgets',
							'title' => esc_html__('Import Widgets', 'trx_addons'),
							'part' => 1,
							'checked' => $this->options['demo_set']=='full',
							'class' => 'trx_importer_separator'
						));

						$this->show_importer_params(array(
							'slug' => 'uploads',
							'title' => esc_html__('Import media', 'trx_addons'),
							'part' => 0,
							'checked' => $this->options['demo_set']=='full',
							'class' => 'trx_importer_separator_before'
						));

						if (!empty($this->options['regenerate_thumbnails'])) {
							?><p class="trx_importer_description trx_importer_description_full<?php if ($this->options['demo_set']!='full') echo ' trx_importer_hidden'; ?>"><?php echo wp_kses_data('Regeneration of thumbnails takes a long time. You can skip this step, but then you have to do it with third-party plugins (for example, Regenerate Thumbnails) to display images correctly on your site.', 'trx_addons'); ?></p><?php
							$this->show_importer_params(array(
								'slug' => 'thumbnails',
								'title' => esc_html__('Regenerate thumbnails', 'trx_addons'),
								'part' => 0,
								'checked' => $this->options['demo_set']=='full'
							));
						}
						?>
					</div><?php

					// Select plugins
					?><div class="trx_importer_advanced_settings_block trx_importer_advanced_settings_plugins">
						<p class="trx_importer_subtitle"><?php esc_html_e('Select the plugins data to be imported:', 'trx_addons'); ?></p>
						<?php
						do_action('trx_addons_action_importer_params', $this);
						?>
					</div>

				</div>

				<div class="trx_buttons">
					<input type="button" value="<?php esc_attr_e('Start Import', 'trx_addons'); ?>" class="trx_addons_button trx_addons_button_accent">
				</div>

			</form>
			
			<div id="trx_importer_progress" class="style_<?php echo esc_attr($this->options['demo_style']); ?>">
				<table border="0" cellpadding="4">
				<?php

				// Show first part of import fields
				$fields = array(
					'posts'		=> esc_html__('Posts', 'trx_addons'),
					'users'		=> esc_html__('Users', 'trx_addons'),
					'tm'		=> esc_html__('Theme Options', 'trx_addons'),
					'to'		=> esc_html__('Plugins Settings', 'trx_addons'),
					'widgets'	=> esc_html__('Widgets', 'trx_addons'),
				);
				foreach ($fields as $slug=>$title) {
					$this->show_importer_fields(array('slug' => $slug, 'title' => $title));
				}

				// Show supported plugins
				do_action('trx_addons_action_importer_import_fields', $this);

				// Show second part of import fields
				$fields = array(
					'uploads'	=> esc_html__('Media', 'trx_addons'),
				);
				if (!empty($this->options['regenerate_thumbnails'])) {
					$fields['thumbnails'] = esc_html__('Thumbnails', 'trx_addons');
				}
				foreach ($fields as $slug=>$title) {
					$this->show_importer_fields(array('slug' => $slug, 'title' => $title));
				}
				?>
				</table>
				<h4 class="trx_importer_progress_result">
					<span class="trx_importer_progress_result_msg"><?php esc_html_e('Congratulations! Data import complete!', 'trx_addons'); ?></span>
					<?php if (empty($tab_id)) { ?>
					<a href="<?php echo esc_url(home_url('/')); ?>" class="trx_importer_view_site"><?php esc_html_e('View site', 'trx_addons'); ?></a>
					<?php } ?>
				</h4>
			</div>
			
		</div><?php
	}

	
	// Section 'Exporter'
	function show_exporter($tab_id, $theme_info) {
		?><div class="trx_exporter_section">

			<h1 class="trx_addons_theme_panel_section_title">
				<?php esc_html_e( 'Export Demo data', 'trx_addons' ); ?>
			</h1>
			
			<?php 
			if ($this->error) {
				?><div class="trx_exporter_error notice notice-error"><?php trx_addons_show_layout($this->error); ?></div><?php
			}
			?>
			
			<form id="trx_exporter_form" action="<?php echo !empty($tab_id) ? get_admin_url( null, 'admin.php?page=trx_addons_theme_panel#trx_addons_theme_panel_section_demo' ) : '#'; ?>" method="post">

				<input type="hidden" value="<?php echo esc_attr(wp_create_nonce(admin_url())); ?>" name="nonce" />
				<input type="hidden" value="all" name="exporter_action" />

				<?php
				if ( isset($_POST['exporter_action']) ) { 
					?><table border="0" cellpadding="6"><?php
					$fields = array(
						'users'			=> esc_html__('Users', 'trx_addons'),
						'posts'			=> esc_html__('Posts', 'trx_addons'),
						'uploads'		=> esc_html__('Uploads', 'trx_addons'),
						'theme_mods'	=> esc_html__('Theme Options', 'trx_addons'),
						'theme_options'	=> esc_html__('Plugins Settings', 'trx_addons'),
						'widgets'		=> esc_html__('Widgets', 'trx_addons'),
					);
					foreach ($fields as $slug=>$title) {
						$this->show_exporter_fields(array('slug' => $slug, 'title' => $title));
					}
					do_action('trx_addons_action_importer_export_fields', $this);
					?></table><?php

				} else {
						
					if (false && count($this->options['files']) > 1) {

						?><p><b><?php esc_html_e('Select the demo type to be exported', 'trx_addons'); ?></b></p><?php

						foreach ($this->options['files'] as $k=>$v) {
							if (!empty($v['file_with_posts'])) {
								?>
								<label><input type="radio"<?php if ($this->options['demo_type']==$k) echo ' checked="checked"'; ?> value="<?php echo esc_attr($k); ?>" name="demo_type" /><?php echo esc_html($v['title']); ?></label>
								<?php
							}
						}
					}
					
					?>
					<div class="trx_buttons">
						<input type="submit" value="<?php esc_attr_e('Export Demo Data', 'trx_addons'); ?>" class="trx_addons_button trx_addons_button_accent">
					</div>
					<?php
				}
				?>
			</form>
		</div><?php
	}
	

	// Section 'Banner rotator'
	function get_banners_layout($tab_id, $theme_info) {
		$banners = '';
		$banners_url = trailingslashit(dirname($this->options['demo_url'])) . '_banners/';
		if (count($this->options['banners']) == 0) {
			$txt = trx_addons_fgc(trailingslashit($banners_url) . 'banners.json');
			if (!empty($txt) && substr($txt, 0, 1) == '[') {
				$this->options['banners'] = json_decode($txt, true);
			}
		}
		if (is_array($this->options['banners']) && count($this->options['banners']) > 0) {
			$banners .= '<div class="trx_banners_section' . (true || empty($tab_id) ? ' trx_addons_hidden' : '') . '">';
			foreach ($this->options['banners'] as $banner) {
				// Prepare links
				if (!empty($banner['image']) && ! trx_addons_is_url( $banner['image'] ) ) {
					$banner['image'] = trailingslashit($banners_url) . trim($banner['image']);
				}
				if (!empty($banner['link_url']) && substr($banner['link_url'], 0, 1) === '#') {
					$banner['link_url'] = apply_filters( 'trx_addons_filter_get_theme_data', '', substr($banner['link_url'], 1) );
				}
				// Build banner's layout
				$banners .=	'<div class="trx_banners_item"'
								. (!empty($banner['duration'])
									? ' data-duration="' . esc_attr(max(1000, min(60000, $banner['duration']*($banner['duration']<1000 ? 1000 : 1)))) . '"'
									: ''
								)
							. '>';
				// Image
				if (!empty($banner['image'])) {
					$banners .= '<div class="trx_banners_item_image"><img src="' . esc_url($banner['image']) . '"></div>';
				}
				$banners .= '<div class="trx_banners_item_text">';
				// Title
				if (!empty($banner['title'])) {
					$banners .= '<h2 class="trx_banners_item_title">' . esc_html($banner['title']) . '</h2>';
				}
				// Content
				if (!empty($banner['content'])) {
					$banners .= '<div class="trx_banners_item_content">' . wp_kses($banner['content'], 'trx_addons_kses_content') . '</div>';
				}
				// Link
				if (!empty($banner['link_url'])) {
					$banners .= '<a class="trx_banners_item_link' . (empty($banner['link_caption'])	? ' trx_banners_item_link_block' : ' trx_addons_button trx_addons_button_accent') . '"'
									. ' href="' . esc_url($banner['link_url']) . '" target="_blank">'
									. esc_html($banner['link_caption'])
								. '</a>';
				}
				$banners .= '</div></div>';
			}
			$banners .= '</div>';
		}
		return $banners;
	}


	// Display importer param's checkbox
	function show_importer_params($args=array()) {
		$args = array_merge(array(
				'slug' => '',
				'title' => '',
				'description' => '',
				'full' => '1',
				'part' => '0',
				'class' => '',
				'atts'  => ''
				), $args);
		?>
		<label class="trx_importer_checkbox_label trx_importer_item_label<?php if (!empty($args['class'])) echo ' '.esc_attr($args['class']); ?>">
			<input type="checkbox"
					class="trx_importer_item trx_importer_item_<?php echo esc_attr($args['slug']); ?>"
					data-set-full="<?php echo esc_attr($args['full']); ?>"
					data-set-part="<?php echo esc_attr($args['part']); ?>"<?php
					echo (isset($args['checked']) && $args['checked']) || (in_array($args['slug'], $this->options['required_plugins']) && $this->options['plugins_initial_state'])
								? ' checked="checked"' 
								: '';
					if ( ! empty( $args['atts'] ) && is_array( $args['atts'] ) ) {
						foreach( $args['atts'] as $k => $v ) {
							echo ' ' . $k . '="' . esc_attr( $v ) . '"';
						}
					}
					?>
					value="1"
					name="import_<?php echo esc_attr($args['slug']); ?>"
					id="import_<?php echo esc_attr($args['slug']); ?>" />
			<span class="trx_importer_checkbox_caption"><?php trx_addons_show_layout($args['title']); ?></span>
		</label>
		<?php
		if (!empty($args['description'])) {
			?><div class="trx_importer_description trx_importer_item_description"><?php trx_addons_show_layout($args['description']); ?></div><?php
		}
	}
	
	// Display importer field's layout
	function show_importer_fields($args=array()) {
		$args = array_merge(array(
				'slug' => '',
				'title' => ''
				), $args);
		?>
		<tr class="import_<?php echo esc_attr($args['slug']); ?>">
			<td class="import_progress_item"><?php trx_addons_show_layout($args['title']); ?></td>
			<td class="import_progress_status"></td>
		</tr>
		<?php
	}
	
	// Display exporter field's layout
	function show_exporter_fields($args=array()) {
		$args = array_merge(array(
				'slug' => '',
				'title' => '',
				'download' => ''
				), $args);
		?>
		<tr>
			<th align="left"><?php trx_addons_show_layout($args['title']); ?></th>
			<td><a download="<?php echo esc_attr(!empty($args['download']) ? $args['download'] : $args['slug'].'.txt'); ?>" href="<?php echo esc_url($this->export_file_url($args['slug'].'.txt')); ?>"><?php esc_html_e('Download', 'trx_addons'); ?></a></td>
		</tr>
		<?php
	}
	
	// Check for required plugings
	function check_required_plugins($list='') {
		$not_installed = apply_filters('trx_addons_filter_importer_required_plugins', '', $list);
		if ($not_installed) {
			$this->error = '<b>'.esc_html__('Attention! For correct installation of the selected demo data, you must install and activate the following plugins: ', 'trx_addons').'</b><br>'.($not_installed);
			return false;
		}
		return true;
	}

	// Display system info
	function show_sys_info() {
		?><div class="trx_importer_sys_info"><?php
			// System info is moved to the General tab of the Theme Dashboard
			if ( false ) {
				?>
				<table class="trx_importer_table" border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<th class="trx_importer_info_param"><span class="dashicons dashicons-yes-alt"></span><?php esc_html_e('System Check', 'trx_addons'); ?></th>
						<th class="trx_importer_info_value"><?php esc_html_e('Current', 'trx_addons'); ?></th>
						<th class="trx_importer_info_advise"><?php esc_html_e('Suggested', 'trx_addons'); ?></th>
					</tr>
					<?php
					$sys_info = trx_addons_get_sys_info();
					$checked  = true;
					foreach ($sys_info as $k=>$item) {
						$checked = $checked && ( ! isset($item['checked']) || $item['checked'] );
						?>
						<tr>
							<td class="trx_importer_info_param"><?php echo esc_html($item['title']); ?></td>
							<td class="trx_importer_info_value<?php
								if (isset($item['checked'])) {
									echo ' trx_importer_info_param_' . ( $item['checked'] ? 'checked' : 'unchecked' );
								}
							?>"><?php echo esc_html($item['value']); ?></td>
							<td class="trx_importer_info_advise"><?php echo esc_html($item['recommended']); ?></td>
						</tr>
						<?php
					}
					?>
				</table>
				<?php
				if ( ! $checked ) {
					?>
					<div class="trx_importer_sys_info_check_result trx_addons_info_box trx_addons_info_box_warning">
						<p><?php
							echo wp_kses_data(
									__("It seems that your server doesn't comply with the theme requirements. You may encounter problems during the demo data installation.", 'trx_addons')
								);
						?></p>
						<p><?php
							echo wp_kses(
									sprintf(
										__("You may want to check with your Hosting Provider if they can fix the issues for you. Or consider using %s that provides a 50%% discount for our customers.", 'trx_addons'),
										'<a href="//www.siteground.com/themerex" target="_blank">' . esc_html__( 'Siteground Hosting', 'trx_addons' ) . '</a>'
									),
									'trx_addons_kses_content'
								);
						?></p>
					</div>
					<?php
				}
			}
			?>
			<div class="trx_importer_sys_info_manual">
				<h5 class="trx_importer_sys_info_manual_title">
					<span class="dashicons dashicons-download"></span>
					<?php esc_html_e('Manual Demo Data Installation', 'trx_addons'); ?>
				</h5>
				<p><?php
					echo wp_kses_data(
							__("If for some reason you have troubles importing demo data, you can always upload it manually:", 'trx_addons')
						);
				?></p>
				<ol>
					<li><?php
						echo wp_kses(
								sprintf(
									__("Download %s", 'trx_addons'),
									'<a href="' . esc_url(
													trailingslashit( trx_addons_get_protocol() . ':'
																		. apply_filters( 'trx_addons_filter_get_theme_data', '', 'theme_demofiles_url' )
																	)
													. apply_filters( 'trx_addons_filter_get_theme_data', 'demo.zip', 'theme_demofiles_archive_name' )
												) . '"'
										. ' target="_blank">'
											. esc_html__( 'this archive', 'trx_addons' )
									. '</a>'
								),
								'trx_addons_kses_content'
							);
						?></li>
					<li><?php
						echo wp_kses(
								sprintf(
									__("Unzip it and upload the 'demo' folder to your theme's root folder, e.g. <code>/wp-content/themes/%s/</code>", 'trx_addons'),
									get_stylesheet()
								),
								'trx_addons_kses_content'
							);
					?></li>
					<li><?php
						esc_html_e("Return to this page and try importing demo again.", 'trx_addons');
					?></li>
				</ol>
			</div>
		</div><?php
	}
	
	
	//-----------------------------------------------------------------------------------
	// Export demo data
	//-----------------------------------------------------------------------------------
	function exporter() {
		global $wpdb;
		$suppress = $wpdb->suppress_errors();

		// Export theme mods
		trx_addons_fpc($this->export_file_dir('theme_mods.txt'), serialize($this->prepare_data(apply_filters('trx_addons_filter_export_mods', get_theme_mods(), $this))));

		// Export plugins settings and WordPress options
		$options = array();
		if (is_array($this->options['additional_options']) && count($this->options['additional_options']) > 0) {
			foreach ($this->options['additional_options'] as $opt) {
				$rows = $wpdb->get_results( $wpdb->prepare( "SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE %s", $opt ) );
				if (is_array($rows) && count($rows) > 0) {
					foreach ($rows as $row) {
						if ( ! in_array( $row->option_name, $this->options['skip_options'] ) ) {
							$options[$row->option_name] = trx_addons_unserialize($row->option_value);
						}
					}
				}
			}
		}
		trx_addons_fpc($this->export_file_dir('theme_options.txt'), serialize($this->prepare_data(apply_filters('trx_addons_filter_export_options', $options, $this))));

		// Export widgets
		$rows = $wpdb->get_results( "SELECT option_name, option_value 
										FROM {$wpdb->options} 
										WHERE option_name = 'sidebars_widgets' 
											OR option_name = 'trx_addons_widgets_areas'
											OR option_name LIKE 'widget_%'"
									);
		$options = array();
		if (is_array($rows) && count($rows) > 0) {
			foreach ($rows as $row) {
				$options[$row->option_name] = trx_addons_unserialize($row->option_value);
			}
		}
		trx_addons_fpc($this->export_file_dir('widgets.txt'), serialize($this->prepare_data(apply_filters('trx_addons_filter_export_widgets', $options, $this))));

		// Export posts
		trx_addons_fpc($this->export_file_dir('posts.txt'), serialize(array(
				"posts"					=> $this->export_dump("posts"),
				"postmeta"				=> $this->export_dump("postmeta"),
				"comments"				=> $this->export_dump("comments"),
				"commentmeta"			=> $this->export_dump("commentmeta"),
				"terms"					=> $this->export_dump("terms"),
				"termmeta"				=> $this->export_dump("termmeta"),
				"term_taxonomy"			=> $this->export_dump("term_taxonomy"),
				"term_relationships"	=> $this->export_dump("term_relationships")
				)));
		
		// Expost WP Users
		$users = array();
		$rows = $this->export_dump("users");
		if (is_array($rows) && count($rows)>0) {
			foreach ($rows as $k=>$v) {
				$v['user_login']	= sprintf('user%s', $v['ID']);
				$v['user_nicename']	= sprintf('user%s', $v['ID']);
				$v['display_name']	= sprintf(esc_html__('User %d', 'trx_addons'), $v['ID']);
				$v['user_email']	= sprintf('user%s',$v['ID']).'@user-mail.net';
				$v['user_pass']		= '';
				$rows[$k] = apply_filters( 'trx_addons_filter_export_single_user', $v, $rows[$k], $this );
			}
		}
		$users['users'] = apply_filters('trx_addons_filter_export_users', $rows, $this);
		$rows = $this->export_dump("usermeta");
		if (is_array($rows) && count($rows)>0) {
			foreach ($rows as $k=>$v) {
				if      ($v['meta_key'] == 'nickname')				$v['meta_value'] = sprintf('user%s', $v['user_id']);
				else if ($v['meta_key'] == 'first_name')			$v['meta_value'] = sprintf(esc_html__('FName%d', 'trx_addons'), $v['user_id']);
				else if ($v['meta_key'] == 'last_name')				$v['meta_value'] = sprintf(esc_html__('LName%d', 'trx_addons'), $v['user_id']);
				else if ($v['meta_key'] == 'billing_first_name')	$v['meta_value'] = sprintf(esc_html__('FName%d', 'trx_addons'), $v['user_id']);
				else if ($v['meta_key'] == 'billing_last_name')		$v['meta_value'] = sprintf(esc_html__('LName%d', 'trx_addons'), $v['user_id']);
				else if ($v['meta_key'] == 'billing_email')			$v['meta_value'] = sprintf('user%s', $v['user_id']).'@user-mail.net';
				$rows[$k] = apply_filters( 'trx_addons_filter_export_single_usermeta', $v, $rows[$k], $this );
			}
		}
		$users['usermeta'] = apply_filters('trx_addons_filter_export_usermeta', $rows, $this);
		trx_addons_fpc($this->export_file_dir('users.txt'), serialize($users));

		// Export Theme specific post types
		do_action('trx_addons_action_importer_export', $this);

		$wpdb->suppress_errors( $suppress );
	}
	
	
	// Export specified table
	function export_dump($table) {
		global $wpdb;
		$rows = array();
		if ( count( $wpdb->get_results( $wpdb->prepare( "SHOW TABLES LIKE %s", $wpdb->prefix . trim($table) ), ARRAY_A ) ) == 1 ) {
			$order = $table=='posts' 
						? 'ID' 
						: (in_array( $table, array( 'postmeta', 'termmeta' ) )
							? 'meta_id' 
							: ($table=='terms' 
								? 'term_id' 
								: ''));
			
			if ($table=='posts' && count($this->options['ignore_post_types'])>0) {
				$query = $wpdb->prepare(
										"SELECT t.* FROM ".esc_sql($wpdb->prefix.trim($table))." AS t WHERE t.post_type NOT IN (" . join(",", array_fill(0, count($this->options['ignore_post_types']), '%s')) . ")" . ($order ? ' ORDER BY t.' . esc_sql($order) . ' ASC' : ''),
										$this->options['ignore_post_types']
										);
				$rows = $this->prepare_data( $wpdb->get_results( $query, ARRAY_A ) );
			} else {
				$query = "SELECT t.* FROM ".esc_sql($wpdb->prefix.trim($table))." AS t".($order ? ' ORDER BY t.' . esc_sql($order) . ' ASC' : '');
				$rows = $this->prepare_data( $wpdb->get_results( $query, ARRAY_A ) );
			}
			if ($this->options['debug']) dfl(sprintf(__("Export %d rows from table '%s'. Used query: %s", 'organic_beauty'), count($rows), $table, $query));
		}
		return $rows;
	}

	// Clear API keys before export
	function export_clear_api_keys($options) {
		// Google maps
		if ( ! empty( $options['api_google'] ) ) {
			$options['api_google'] = '';
		}
		// Google analytics
		if ( ! empty( $options['api_google_analitics'] ) ) {
			$options['api_google_analitics'] = '';
		}
		// Google remarketing
		if ( ! empty( $options['api_google_remarketing'] ) ) {
			$options['api_google_remarketing'] = '';
		}
		// Open Street map
		if ( ! empty( $options['api_openstreet_tiler_vector'] ) && is_array( $options['api_openstreet_tiler_vector'] ) ) {
			foreach( $options['api_openstreet_tiler_vector'] as $k => $v ) {
				if ( ! empty( $v['token'] ) ) $options['api_openstreet_tiler_vector'][$k]['token'] = '';
			}
		}
		// Facebook
		if ( ! empty( $options['api_fb_app_id'] ) ) {
			$options['api_fb_app_id'] = '';
		}
		// Instagram
		if ( ! empty( $options['api_instagram_client_id'] ) ) {
			$options['api_instagram_client_id'] = '';
		}
		if ( ! empty( $options['api_instagram_client_secret'] ) ) {
			$options['api_instagram_client_secret'] = '';
		}
		if ( ! empty( $options['api_instagram_access_token'] ) ) {
			$options['api_instagram_access_token'] = '';
		}
		if ( ! empty( $options['api_instagram_user_id'] ) ) {
			$options['api_instagram_user_id'] = '';
		}
		return $options;
	}
	
	
	//-----------------------------------------------------------------------------------
	// Import demo data
	//-----------------------------------------------------------------------------------
	//Handler of the add_action('wp_ajax_trx_addons_importer_start_import',	array($this, 'importer'));
	function importer() {

		if ($this->options['debug']) dfl(__('AJAX handler for importer', 'trx_addons'));

		trx_addons_verify_nonce( 'ajax_nonce' );

		if ( ! isset($_POST['importer_action']) ) {
			trx_addons_forbidden();
		}

		$this->prepare_vars();

		$this->action = $this->response['action'] = $_POST['importer_action'];

		if ($this->options['debug']) dfl( sprintf(__('Dispatch action: %s', 'trx_addons'), $this->action) );
		
		global $wpdb;
		$suppress = $wpdb->suppress_errors();

		ob_start();

		// Change PHP settings
		if ( function_exists( 'ini_get' ) ) {
			// Change max_execution_time (if allowed by server)
			$admin_tm = max(0, min(1800, $this->options['demo_timeout']));
			$tm = max(30, (int) ini_get('max_execution_time'));
			if ($tm < $admin_tm) {
				@set_time_limit( $admin_tm );
				$this->max_time = round( 0.9 * max(30, ini_get('max_execution_time')));
			}
			// Increase memory limit if free memory less then specified value
			$memory_need  = ( version_compare( phpversion(), '7.0', '<' ) ? 128 : 64 ) * pow( 1024, 2 );
			$memory_usage = memory_get_usage();
			if ( trx_addons_size2num( ini_get( 'memory_limit' ) ) - $memory_usage < $memory_need ) {
				@ini_set( 'memory_limit', trx_addons_num2size( $memory_usage + $memory_need ) );
			}
			/*
			// Increase upload max file size to 32M
			$upload_max_filesize = 32 * pow( 1024, 2 );
			if ( trx_addons_size2num( ini_get( 'upload_max_filesize' ) ) < $upload_max_filesize ) {
				@ini_set( 'upload_max_filesize', trx_addons_num2size( $upload_max_filesize ) );
			}
			*/
		}

		// Start import - clear tables, etc.
		if ( $this->action == 'import_start' ) {
			do_action('trx_addons_action_importer_import_start', $this);
			if ( ! $this->check_required_plugins( $this->options['demo_parts'] ) ) {
				$this->response['error'] = $this->error;
			} else {
				if ( ! empty( $this->options['demo_parts'] ) ) {
					$this->clear_tables();
				}
			}
			if ( $this->options['debug'] ) dfl( sprintf( __( 'Start import from "%s"', 'trx_addons' ), $this->options['demo_url'] ) );

		// Import posts and users
		} else if ($this->action == 'import_posts') {
			wp_suspend_cache_invalidation( true );
			$this->import_posts();
			if ($this->response['result'] >= 100 && $this->options['demo_set']=='full') {
				do_action('trx_addons_action_importer_after_import_posts', $this);
			}
			wp_suspend_cache_invalidation( false );

		// Import posts and users
		} else if ($this->action == 'import_users') {
			$this->import_users();

		// Import attachments
		} else if ($this->action == 'import_uploads') {
			$this->import_uploads();

		// Regenerate thumbnails
		} else if ($this->action == 'import_thumbnails') {
			$this->import_thumbnails();

		// Import Theme Options: WP Modifications with Theme Options
		} else if ($this->action == 'import_tm') {
			$this->import_theme_mods();

		// Import Plugins Settings: ThemeREX Addons and other plugins options
		} else if ($this->action == 'import_to') {
			$this->import_theme_options();

		// Import Widgets
		} else if ($this->action == 'import_widgets') {
			$this->import_widgets();

		// End import - clear cache, flush rules, etc.
		} else if ($this->action == 'import_end') {
			trx_addons_clear_cache('all');
			$this->set_permalink_structure();
			flush_rewrite_rules();
			do_action('trx_addons_action_importer_import_end', $this);

		// Import Theme specific posts
		} else {
			do_action('trx_addons_action_importer_import', $this, $this->action);
		}

		ob_end_clean();

		$wpdb->suppress_errors($suppress);

		if ($this->options['debug']) dfl( sprintf(__("AJAX handler finished - send results to client: %s", 'trx_addons'), json_encode($this->response)) );
	
		trx_addons_ajax_response( $this->response );
	}

	// Set permalink structure to 'Post name'
	function set_permalink_structure() {
		if ($this->options['set_permalinks']) {
			$permalink_structure = '/%postname%/';
			if ( ! got_url_rewrite() ) {
				$permalink_structure = "/index.php{$permalink_structure}";
			} else if ( is_multisite() && ! is_subdomain_install() && is_main_site() && 0 === strpos( get_option( 'permalink_structure' ), '/blog/' ) ) {
				$permalink_structure = "/blog{$permalink_structure}";
			}
			$permalink_structure = sanitize_option( 'permalink_structure', $permalink_structure );
			global $wp_rewrite;
			$wp_rewrite->set_permalink_structure( $permalink_structure );
		}
	}

	// Delete all data from tables
	function clear_tables() {
		global $wpdb;
		if ($this->options['demo_set']=='full') {
			if (strpos($this->options['demo_parts'], 'posts')!==false) {
				if ($this->options['debug']) {
					dfl( __('Clear tables', 'trx_addons') );
				}

				$res = $wpdb->query("TRUNCATE TABLE {$wpdb->posts}");
				if ( $res === false ) {
					$res = $wpdb->query("DELETE FROM {$wpdb->posts}");
					if ( $res !== false && ! is_wp_error( $res ) ) $wpdb->query("ALTER TABLE {$wpdb->posts} AUTO_INCREMENT = 1;");
				}
				if ( is_wp_error( $res ) ) dfl( __( 'Failed truncate table POSTS.', 'trx_addons' ) . ' ' . ($res->get_error_message()) );

				$res = $wpdb->query("TRUNCATE TABLE {$wpdb->postmeta}");
				if ( $res === false ) {
					$res = $wpdb->query("DELETE FROM {$wpdb->postmeta}");
					if ( $res !== false && ! is_wp_error( $res ) ) $wpdb->query("ALTER TABLE {$wpdb->postmeta} AUTO_INCREMENT = 1;");
				}
				if ( is_wp_error( $res ) ) dfl( __( 'Failed truncate table POSTMETA.', 'trx_addons' ) . ' ' . ($res->get_error_message()) );

				$res = $wpdb->query("TRUNCATE TABLE {$wpdb->comments}");
				if ( $res === false ) {
					$res = $wpdb->query("DELETE FROM {$wpdb->comments}");
					if ( $res !== false && ! is_wp_error( $res ) ) $wpdb->query("ALTER TABLE {$wpdb->comments} AUTO_INCREMENT = 1;");
				}
				if ( is_wp_error( $res ) ) dfl( __( 'Failed truncate table COMMENTS.', 'trx_addons' ) . ' ' . ($res->get_error_message()) );

				$res = $wpdb->query("TRUNCATE TABLE {$wpdb->commentmeta}");
				if ( $res === false ) {
					$res = $wpdb->query("DELETE FROM {$wpdb->commentmeta}");
					if ( $res !== false && ! is_wp_error( $res ) ) $wpdb->query("ALTER TABLE {$wpdb->commentmeta} AUTO_INCREMENT = 1;");
				}
				if ( is_wp_error( $res ) ) dfl( __( 'Failed truncate table COMMENTMETA.', 'trx_addons' ) . ' ' . ($res->get_error_message()) );

				$res = $wpdb->query("TRUNCATE TABLE {$wpdb->terms}");
				if ( $res === false ) {
					$res = $wpdb->query("DELETE FROM {$wpdb->terms}");
					if ( $res !== false && ! is_wp_error( $res ) ) $wpdb->query("ALTER TABLE {$wpdb->terms} AUTO_INCREMENT = 1;");
				}
				if ( is_wp_error( $res ) ) dfl( __( 'Failed truncate table TERMS.', 'trx_addons' ) . ' ' . ($res->get_error_message()) );

				$res = $wpdb->query("TRUNCATE TABLE {$wpdb->termmeta}");
				if ( $res === false ) {
					$res = $wpdb->query("DELETE FROM {$wpdb->termmeta}");
					if ( $res !== false && ! is_wp_error( $res ) ) $wpdb->query("ALTER TABLE {$wpdb->termmeta} AUTO_INCREMENT = 1;");
				}
				if ( is_wp_error( $res ) ) dfl( __( 'Failed truncate table TERMMETA.', 'trx_addons' ) . ' ' . ($res->get_error_message()) );

				$res = $wpdb->query("TRUNCATE TABLE {$wpdb->term_relationships}");
				if ( $res === false ) {
					$res = $wpdb->query("DELETE FROM {$wpdb->term_relationships}");
					if ( $res !== false && ! is_wp_error( $res ) ) $wpdb->query("ALTER TABLE {$wpdb->term_relationships} AUTO_INCREMENT = 1;");
				}
				if ( is_wp_error( $res ) ) dfl( __( 'Failed truncate table TERM_RELATIONSHIPS.', 'trx_addons' ) . ' ' . ($res->get_error_message()) );

				$res = $wpdb->query("TRUNCATE TABLE {$wpdb->term_taxonomy}");
				if ( $res === false ) {
					$res = $wpdb->query("DELETE FROM {$wpdb->term_taxonomy}");
					if ( $res !== false && ! is_wp_error( $res ) ) $wpdb->query("ALTER TABLE {$wpdb->term_taxonomy} AUTO_INCREMENT = 1;");
				}
				if ( is_wp_error( $res ) ) dfl( __( 'Failed truncate table TERM_TAXONOMY.', 'trx_addons' ) . ' ' . ($res->get_error_message()) );
			}
			do_action('trx_addons_action_importer_clear_tables', $this, $this->options['demo_parts']);
		}
	}

	
	// Import users
	function import_users() {
		if ($this->options['debug']) 
			dfl(__('Import users', 'trx_addons'));
		$this->response['start_from_id'] = 0;
		$this->import_dump('users', __('Users', 'trx_addons'));
	}

	// Import posts, terms and comments
	function import_posts() {
		if ($this->options['debug']) 
			dfl(__('Import posts, terms and comments', 'trx_addons'));
		$this->response['start_from_id'] = isset($_POST['start_from_id']) ? $_POST['start_from_id'] : 0;
		if ($this->options['demo_set'] == 'part' && $this->response['start_from_id'] == 0) {
			$this->import_prepare_no_image();
		}
		$this->import_dump('posts', __('Posts', 'trx_addons'));
	}

	// Import media (uploads folder)
	function import_uploads($uploads = '', $import_type = '') {
		if ($this->options['debug']) 
			dfl(__('Import media', 'trx_addons'));
		if ($uploads == '') 
			$uploads = $this->options['files'][$this->options['demo_type']]['file_with_uploads'];
		if (empty($uploads))
			return;
		// Detect current uploads folder
		if ($import_type == 'ocdi') {
			$uploads_info = wp_upload_dir();
			$this->uploads_dir = $uploads_info['basedir'];
		}
		// Get last processed arh
		$last_arh = $this->response['start_from_id'] = isset($_POST['start_from_id']) ? $_POST['start_from_id'] : '';
		// Get list of the files
		$txt = !$this->options['debug'] ? get_transient('trx_addons_importer_uploads') : '';
		if ( empty($last_arh) || empty($txt) ) {
			if ( ($txt = $this->get_file($uploads, 0, $import_type)) === false) {
				return;
			} else if (!$this->options['debug']) {
				set_transient('trx_addons_importer_uploads', $txt, 20*60);	// Store to the cache for 20 minutes
			}
		}
		$files = trx_addons_unserialize($txt);
		if (!is_array($files)) $files = explode("\n", str_replace("\r\n", "\n", $files));
		// Remove empty lines and comments
		foreach ($files as $k=>$file) {
			$file = trim($file);
			if ($file=='' || substr($file, 0, 1) == '#') unset($files[$k]);
		}
		// Make archive parts
		$ext = trx_addons_get_file_ext(trx_addons_array_get_first($files, false));
		$parts = (int) $ext;
		if (count($files)==1 && $parts > 0) {
			$new_files = array();
			for ($i=1; $i<=$parts; $i++) {
				$new_files[] = str_replace('.'.trim($ext), sprintf('.%03d', $i), $files[0]);
			}
			$files = $new_files;
		}
		// Process files
		$counter = 0;
		$result = 0;
		foreach ($files as $file) {
			$counter++;
			$result = $counter < count($files) ? round($counter / count($files) * 100, 2) : 100;
			if ( ($file = trim($file)) == '' ) {
				continue;
			}
			if (!empty($last_arh)) {
				if ($file==$last_arh) {
					$last_arh = '';
				}
				continue;
			}
			$need_del = false;
			$need_extract = false;
			$need_exit = false;
			$zip = '';
			// Load single file into system temp folder
			if (trx_addons_get_file_ext($file)=='zip') {
				if ( ($zip = $this->download_file($file, round( max(0, $counter-1) / count($files) * 100, 2 ), $import_type)) === '') {
					$need_exit = true;
				} else {
					$need_del = substr($zip, 0, 5)=='http:' || substr($zip, 0, 6)=='https:';
					$need_extract = true;
				}

			// Append next part (*.001, *.002 ...) to archive
			} else if ((int) trx_addons_get_file_ext($file) > 0) {
				if ( ($txt = $this->get_file($file, round( max(0, $counter-1) / count($files) * 100, 2 ), $import_type)) === false) {
					$need_exit = true;
				} else {
					$zip = $this->uploads_dir.'/import_media.tmp';
					$res = trx_addons_fpc($zip, $txt, $file==$files[0] ? 0 : FILE_APPEND);
					if ($this->options['debug']) {
						dfl(sprintf( __('Loaded %d bytes', 'trx_addons'), $res));
					}
					$need_extract = $need_del = ($counter == count($files));
				}
			}
			// Unrecoverable error is appear
			if ($need_exit) break;
			// Save to log last processed file
			$this->response['start_from_id'] = $file;
			// Check time
			if ($this->options['debug']) {
				dfl(sprintf( __('File %s imported. Current import progress: %s. Time limit: %s sec. Elapsed time: %s sec.', 'trx_addons'), $file, $result.'%', $this->max_time, time() - $this->start_time));
			}
			// Unzip file
			if ($need_extract) {
				if (!empty($zip) && file_exists($zip)) {
					if ($this->options['debug'])
						dfl(sprintf(__('Extract zip-file "%s"', 'trx_addons'), $zip));
					$rez = trx_addons_unzip_file( $zip, $this->uploads_dir );
					if ( is_wp_error($rez) ) {
						$msg = sprintf(__('Unable to unzip file "%s"', 'themerex'), $zip);
						$this->response['error'] = $msg;
						if ($this->options['debug']) {
							dfl($msg);
							dfo($rez);
						}
					}
					if ($need_del) unlink($zip);
				} else {
					$msg = sprintf(__('File "%s" not found', 'themerex'), $zip);
					$this->response['error'] = $msg;
					if ($this->options['debug']) 
						dfl($msg);
				}
			}
			// Break import after timeout or if attachments loading from parts - to show percent loading after each part
			//if (time() - $this->start_time >= $this->max_time)
			if ($import_type != 'ocdi')
				break;
		}
		if ($result >= 100) delete_transient('trx_addons_importer_uploads');
		$this->response['result'] = $result;
	}

	// Regenerate thumbnails
	function import_thumbnails() {
		if ( $this->options['debug'] )  {
			dfl(__('Regenerate thumbnails', 'trx_addons'));
		}
		// Get last processed attachment
		$last_arh = $this->response['start_from_id'] = isset($_POST['start_from_id']) ? $_POST['start_from_id'] : '';
		// Get list of the attachments
		$files = ! $this->options['debug'] ? get_transient('trx_addons_importer_attachments') : '';
		if ( empty($last_arh) || empty($files) ) {
			$list = get_posts( array(
								'post_type' => 'attachment',
								'posts_per_page' => -1,
								'post_status' => 'any',
								'post_parent' => null,
								'orderby' => 'ID',
								'order' => 'asc'
								)
							);
			if (!is_array($list) || count($list) == 0)
				return;
			$files = array();
			foreach ($list as $post) {
				$files[$post->ID] = get_attached_file($post->ID);
			}
			if ( ! $this->options['debug']) {
				set_transient('trx_addons_importer_attachments', $list, 20*60);	// Store to the cache for 20 minutes
			}
		}
		// Process files
		$counter = $processed = $result = 0;
		foreach ($files as $id=>$file) {
			$counter++;
			$result = $counter < count($files) ? round($counter / count($files) * 100, 2) : 100;
			if (!empty($last_arh)) {
				if ($id == $last_arh) 
					$last_arh = '';
				continue;
			}
			// Regenerate metadata
			wp_update_attachment_metadata( $id,  wp_generate_attachment_metadata( $id, $file ) );
			// Save to log last processed file
			$this->response['start_from_id'] = $id;
			// Check time
			if ($this->options['debug']) 
				dfl(sprintf( __('Thumbnails of the attachments %s: %s regenerated. Current import progress: %s. Time limit: %s sec. Elapsed time: %s sec.', 'trx_addons'), $id, $file, $result.'%', $this->max_time, time() - $this->start_time));
			// Break import after timeout or if attachments loading from parts - to show percent loading after each part
			if (time() - $this->start_time >= $this->max_time || ++$processed >= $this->options['regenerate_thumbnails'])
				break;
		}
		if ($result >= 100) delete_transient('trx_addons_importer_attachments');
		$this->response['result'] = $result;
	}
	
	// Import theme options: WP Modifications with Theme Options
	function import_theme_mods() {
		if ($this->options['debug']) 
			dfl(__('Import Theme Options', 'trx_addons'));
		if ( empty($this->options['files'][$this->options['demo_type']]['file_with_mods']) )
			return;
		if ( ($txt = $this->get_file($this->options['files'][$this->options['demo_type']]['file_with_mods'])) === false )
			return;
		$data = trx_addons_unserialize($txt);
		// Replace upload url in options
		if (is_array($data) && count($data) > 0) {
			$data = $this->replace_site_url($data);
			$theme = get_stylesheet();
			update_option( "theme_mods_$theme", $data );
		} else {
			if ($this->options['debug'])
				dfl(sprintf(__('Unable to unserialize data from the file %s', 'trx_addons'), trx_addons_get_file_name($this->options['files'][$this->options['demo_type']]['file_with_mods'])));
		}
	}


	// Import Plugins settings
	function import_theme_options() {
		if ( $this->options['debug'] )  {
			dfl(__('Import Plugins Settings', 'trx_addons'));
		}
		if ( empty( $this->options['files'][$this->options['demo_type']]['file_with_options'] ) ) {
			return;
		}
		if ( ( $txt = $this->get_file( $this->options['files'][$this->options['demo_type']]['file_with_options'] ) ) === false ) {
			return;
		}
		$data = trx_addons_unserialize($txt);
		// Replace upload url in options
		if ( is_array( $data ) && count( $data ) > 0 ) {
			foreach ( apply_filters( 'trx_addons_filter_import_theme_options_data', $data ) as $k => $v ) {
				if ( apply_filters( 'trx_addons_filter_import_theme_options', true, $k, $v, $this->options ) ) {
					update_option( $k, apply_filters( 'trx_addons_filter_import_theme_options_value', $this->replace_site_url( $v ), $k ) );
				}
			}
		} else {
			if ( $this->options['debug'] ) {
				dfl(sprintf(__('Unable to unserialize data from the file %s', 'trx_addons'), trx_addons_get_file_name($this->options['files'][$this->options['demo_type']]['file_with_options'])));
			}
		}
	}


	// Import widgets
	function import_widgets() {
		if ( $this->options['debug'] ) {
			dfl(__('Import Widgets', 'trx_addons'));
		}
		if ( empty( $this->options['files'][$this->options['demo_type']]['file_with_widgets'] ) ) {
			return;
		}
		if ( ( $txt = $this->get_file( $this->options['files'][$this->options['demo_type']]['file_with_widgets'] ) ) === false ) {
			return;
		}
		$data = trx_addons_unserialize($txt);
		if ( is_array( $data ) && count( $data ) > 0 ) {
			foreach ( apply_filters( 'trx_addons_filter_import_widgets_data', $data ) as $k => $v ) {
				if ( apply_filters( 'trx_addons_filter_import_widgets', true, $k, $v ) ) {
					update_option( $k, apply_filters('trx_addons_filter_import_widgets_value', $this->replace_site_url($v), $k) );
				}
			}
		} else {
			if ( $this->options['debug'] ) {
				dfl(sprintf(__('Unable to unserialize data from the file %s', 'trx_addons'), trx_addons_get_file_name($this->options['files'][$this->options['demo_type']]['file_with_widgets'])));
			}
		}
	}


	// Import any SQL dump
	function import_dump( $slug, $title, $required = true ) {
		if ($this->options['debug']) {
			dfl(sprintf(__('Import dump file: "%s"', 'trx_addons'), $this->options['files'][$this->options['demo_type']]['file_with_' . $slug]));
		}
		if ( empty($this->options['files'][$this->options['demo_type']]['file_with_' . $slug]) ) {
			return;
		}
		if ( ($txt = $this->get_file($this->options['files'][$this->options['demo_type']]['file_with_' . $slug])) === false ) {
			if ( ! $required ) {
				$this->response['error'] = '';
			}
			return;
		}
		$result = 100;
		$data = trx_addons_unserialize($txt);
		if (is_array($data) && count($data) > 0) {
			global $wpdb;
			$wpdb->query("set session wait_timeout=10");				// 10s.		Default is 5s
			$wpdb->query("set session connect_timeout=300");			// 300s.	Default is 60s
			//$wpdb->query("set global max_allowed_packet=65536");		// 64K.		Default is 16M
			foreach ($data as $table=>$rows) {
				$values = $fields = '';
				$result = 100;
				$break = false;
				// In partial import skip all tables except 'posts' and 'postmeta'
				if ( $this->options['demo_set'] == 'part'
					&& $this->action == 'import_posts'
					&& ! in_array( $table, array( 'posts', 'postmeta' ) )
				) {
					if ($this->options['debug']) {
						dfl(sprintf(__('Skip table "%s"', 'trx_addons'), $table));
					}
					continue;
				}
				// Process table
				if ($this->options['debug']) {
					dfl(sprintf(__('Process table "%s"', 'trx_addons'), $table));
				}
				if ( count( $wpdb->get_results( $wpdb->prepare( "SHOW TABLES LIKE %s", $wpdb->prefix . trim($table) ), ARRAY_A ) ) == 0 ) {
					if ( $this->options['debug'] ) {
						dfl(sprintf(__('Table "%s" does not exists! Skip dump import for this table.', 'trx_addons'), $table));
					}
					continue;
				}
				// Clear table, if it is not 'users' or 'usermeta' and not any posts, terms or comments table
				if ($this->options['demo_set']=='full' && !in_array($table, array('users', 'usermeta')) && $this->action!='import_posts') {
					$wpdb->query("TRUNCATE TABLE " . esc_sql($wpdb->prefix . $table));
				}
				// Restore previous state (if import was split on parts)
				if ($this->options['demo_set']=='part' && $table=='posts' && $this->response['start_from_id'] > 0) {
					$this->part_replace = get_transient('trx_addons_importer_part_replace');	//get_option('trx_addons_importer_part_replace', array());
					if (!is_array($this->part_replace)) $this->part_replace = array();
					$this->part_image = get_transient('trx_addons_importer_part_image');		//get_option('trx_addons_importer_part_image', array());
					if (!is_array($this->part_image)) $this->part_image = array();
				}
				if (is_array($rows) && ($posts_all=count($rows)) > 0) {
					$posts_counter = $posts_imported = 0;
					foreach ($rows as $row) {
						$posts_counter++;
						$result = $posts_counter < $posts_all ? round($posts_counter / $posts_all * 100) : 100;
						// Skip previously imported posts
						if (!empty($row['ID']) && $row['ID'] <= $this->response['start_from_id']) continue;
						// Check if this row will be imported in the set='part'
						if (!apply_filters('trx_addons_filter_importer_import_row', $this->options['demo_set']=='full', $table, $row, $this->options['demo_parts'])) continue;
						// Replace demo URL to current site URL
						$row = $this->replace_site_url($row);
						$f = '';
						$v = '';
						if (is_array($row) && count($row) > 0) {
							// If 'demo_set' == 'part' - prepare data
							if ($this->options['demo_set']=='part') {
								if ( $table=='posts' ) {
									// Replace images in the post's content
									$row['post_content'] = preg_replace('/(\s+image=["\']\d+["\'])/', ' image="'.esc_attr($this->part_image['id']).'"', $row['post_content']);
									$row['post_content'] = preg_replace('/(\s+url=["\']\d+["\'])/', ' url="'.esc_attr($this->part_image['id']).'"', $row['post_content']);
									$row['post_content'] = preg_replace('/(url\([^\)]+\))/', 'url('.esc_attr($this->part_image['url']).')', $row['post_content']);
									// Replace category in the shortcodes
									$row['post_content'] = preg_replace('/(\s+category=["\']\d+["\'])/', ' category="0"', $row['post_content']);
									$row['post_content'] = preg_replace('/(\s+cat=["\']\d+["\'])/', ' cat="0"', $row['post_content']);
								}
								if ( $table=='postmeta' ) {
									// Replace images in the meta values
									if ($row['meta_key']=='_elementor_data' ) {
										$row['meta_value'] = preg_replace('/(url\([^\)]+\))/', 'url('.esc_attr($this->part_image['url']).')', $row['meta_value']);
									}
									if ($row['meta_key']=='_wpb_shortcodes_custom_css' ) {
										$row['meta_value'] = preg_replace('/(url\([^\)]+\))/', 'url('.esc_attr($this->part_image['url']).')', $row['meta_value']);
									}
									if ($row['meta_key']=='_thumbnail_id' ) {
										$row['meta_value'] = $this->part_image['id'];
									}
									// Change post ID in the post meta
									$row['post_id'] = $this->part_replace[$row['post_id']];
								}
								// Content filter
								$row = apply_filters('trx_addons_filter_importer_row_content', $row, $table );
							}
							// Merge fields and values to string
							foreach ($row as $field => $value) {
								// If 'demo_set' == 'part' - skip autoincrement fields
								if ($this->options['demo_set']=='part') {
									if ($table=='posts' && $field=='ID') continue;
									if ($table=='postmeta' && $field=='meta_id') continue;
								}
								$f .= ($f ? ',' : '') . '`' . esc_sql($field) . '`';
								$v .= ($v ? ',' : '') . ( is_null( $value ) ? 'NULL' : "'" . esc_sql($value) . "'" );
							}
						}
						if ($fields == '') $fields = '(' . trim($f) . ')';
						$values .= ($values ? ',' : '') . '(' . trim($v) . ')';
						// If query length exceed 64K - run query, because MySQL not accept long query string ( > 65535 bytes)
						// If current table 'users' or 'usermeta' - run queries row by row, because we append data
						if (strlen($values . $v) > 64000 
							|| in_array($table, apply_filters('trx_addons_filter_importer_separate_insert', array('users', 'usermeta')))
							|| ($this->options['demo_set']=='part' && $table=='posts')) {
							// Attention! All items in the variable $values are escaped in the loop above - esc_sql($value)
							// We can't use wpdb::prepare because we need calculate real query's length (with real values, but not with %s)
							$q = ( substr( $table, 0, 4) == 'user' ? 'INSERT' : 'REPLACE' ) . ' INTO ' . esc_sql($wpdb->prefix . $table)
									. (true || $this->options['demo_set']=='part'   // Always add field's names
										? ' ' . $fields
										: ''
										)
									. " VALUES {$values}";
							$wpdb->query($q);
							$values = $fields = '';
							if ($this->options['demo_set']=='part' && $table=='posts') {
								$this->part_replace[$row['ID']] = $wpdb->insert_id;
								$rez = $wpdb->update( $wpdb->posts, array( 'guid' => get_permalink( $this->part_replace[$row['ID']] ) ), array( 'ID' => $this->part_replace[$row['ID']] ) );
							}
						}
						
						// Save last ID to the log
						$this->response['start_from_id'] = isset($row['ID']) ? max($row['ID'], $this->response['start_from_id']) : 0;
						if ($this->options['debug']) {
							dfl( sprintf( __('Record (ID=%s) is imported. Progress: %s. Time: %s sec. from %s sec.', 'trx_addons'),
											!empty($row['ID']) 
												? $row['ID'] . ($this->options['demo_set']=='part' 
													? '->' . $this->part_replace[$row['ID']]
													: ''
													)
												: (!empty($row['meta_id']) 
													? $row['meta_id']
													: (!empty($row['term_id']) 
														? $row['term_id']
														: (!empty($row['post_id']) 
															? $row['post_id']
															: ''
															)
														)
													),
											$result.'%',
											time() - $this->start_time,
											$this->max_time
										)
								);
						}
						// Break import after timeout or if leave one post and execution time > half of max_time
						if (time() - $this->start_time >= $this->max_time) {
							$break = true;
							break;
						}
					}
				}
				if (!empty($values)) {
					// Attention! All items in the variable $values are escaped in the loop above - esc_sql($value)
					// We can't use wpdb::prepare because we need calculate real query's length (with real values, but not with %s)
					$q = ( substr( $table, 0, 4) == 'user' ? 'INSERT' : 'REPLACE' ) . ' INTO ' . esc_sql($wpdb->prefix . $table)
							. (true || $this->options['demo_set']=='part'   // Always add field's names
								? ' ' . $fields
								: ''
								)
							. " VALUES {$values}";
					$wpdb->query($q);
				}
				if ($this->options['demo_set']=='part' && $table=='posts') {
					set_transient('trx_addons_importer_part_replace',	$result < 100 ? $this->part_replace : array(), 10*60);		// Store to the cache for 10 minutes
					set_transient('trx_addons_importer_part_image',		$result < 100 ? $this->part_image 	: array(), 10*60);		// Store to the cache for 10 minutes
				}
				if ($this->options['debug']) dfl(sprintf(__('Imported %s. Elapsed time %s sec. of %s sec.', 'trx_addons'), $result.'%', time() - $this->start_time, $this->max_time));
				if ($break) break;
			}
		} else {
			if ($this->options['debug']) 
				dfl(sprintf(__('Unable to unserialize data from the file %s', 'trx_addons'), trx_addons_get_file_name($this->options['files'][$this->options['demo_type']]['file_with_' . $slug])));
		}
		$this->response['result'] = $result;
	}
	
	// Check if the row will be imported
	// Handler of the add_filter('trx_addons_filter_importer_import_row', array($this, 'import_check_row'), 9, 4);
	function import_check_row($flag, $table, $row, $parts) {
		// If demo_set=='full' or previous handler set flag to true - return true
		if ($flag) return $flag;
		// Check posts, pages, etc.
		if ($table == 'posts') {
			$flag = $row['post_type']=='page' && in_array($row['ID'], $this->options['demo_pages']);
		} else if ($table == 'postmeta') {
			$flag = !empty($this->part_replace[$row['post_id']]);
		} else {
			$flag = true;
		}
		return $flag;
	}
	
	// Copy no-image.jpg to the uploads folder
	function import_prepare_no_image() {
		$no_image_title = esc_html__('No-Image placeholder', 'trx_addons');
		$no_image_post = trx_addons_get_post_by_title( $no_image_title, 'attachment' );
		if ( empty( $no_image_post->ID ) ) {
			if ( ( $img = trx_addons_get_no_image() ) != '' ) {
				// Copy to the 'uploads' folder
				$this->part_image = wp_upload_bits( 'no-image.jpg', 0, trx_addons_fgc($img));
				if ( empty( $this->part_image['error'] ) ) {
					// Prepare an array of post data for the attachment.
					$attachment = array(
						'guid'           => $this->part_image['url'], 
						'post_mime_type' => $this->part_image['type'],
						'post_title'     => $no_image_title,
						'post_content'   => '',
						'post_status'    => 'publish'
					);
					$this->part_image['id'] = wp_insert_attachment( $attachment, $this->part_image['file'] );
					// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
					require_once trailingslashit(ABSPATH) . 'wp-admin/includes/image.php';
					wp_update_attachment_metadata( $this->part_image['id'], wp_generate_attachment_metadata( $this->part_image['id'], $this->part_image['file'] ) );
				}
			}
		} else {
			$this->part_image = array(
				'id' => $no_image_post->ID,
				'url' => wp_get_attachment_url($no_image_post->ID),
				'file' => '',
				'type' => ''
			);
		}
	}
	
	// Return array with pages id and title from the selected demo
	function get_list_pages_from_demo($demo_type) {
		$list = get_transient( "trx_addons_installer_posts" );
		if ( ! $list || ! is_array( $list ) ) {
			$list = array();
			if ( ( $txt = $this->get_file( $this->options['files'][$demo_type]['file_with_posts'] ) ) === false ) {
				return $list;
			}
			$data = trx_addons_unserialize($txt);
			if ( is_array( $data ) && is_array( $data['posts'] ) ) {
				foreach ( $data['posts'] as $row ) {
					if ( $row['post_type'] == 'page' ) {
						$list[$row['ID']] = $row['post_title'];
					}
				}
			}
			set_transient( "trx_addons_installer_posts", $list, 5 * 60 );	// Store to cache for 5 minutes
		}
		return $list;
	}
	
	// Callback of the get_list_pages action
	function get_list_pages_callback() {
		trx_addons_verify_nonce( 'ajax_nonce' );

		$this->prepare_vars();

		$response = array(
			'error' => empty($_POST['demo_type']) ? esc_html__('Incorrect parameters', 'trx_addons') : '',
		);

		if (!empty($_POST['demo_type']))
			$response['data'] = $this->get_list_pages_from_demo($_POST['demo_type']);

		trx_addons_ajax_response( $response );
	}

	// Replace demo site url to current site url
	function replace_site_url($str) {
		return trx_addons_url_replace( $this->options['files'][$this->options['demo_type']]['domain_demo'], get_home_url(), $str );
	}
	
	// Replace strings then export data
	function prepare_data($str) {
		if ( is_array( $str ) ) {
			foreach ( $str as $k => $v ) {
				$str[ $k ] = $this->prepare_data( $v );
			}
		} else if ( is_object( $str ) ) {
			foreach ( $str as $k => $v ) {
				$str->{$k} = $this->prepare_data( $v );
			}
		} else if ( is_string( $str ) ) {
			if ( is_serialized( $str ) ) {
				$str = serialize( $this->prepare_data( trx_addons_unserialize( $str ) ) );
			} else {
				// Replace developers domain to the demo domain
				if ( $this->options['files'][$this->options['demo_type']]['domain_dev'] != $this->options['files'][$this->options['demo_type']]['domain_demo'] ) {
					$str = str_replace(
										trx_addons_get_domain_from_url( $this->options['files'][$this->options['demo_type']]['domain_dev'] ),
										trx_addons_get_domain_from_url( $this->options['files'][$this->options['demo_type']]['domain_demo'] ),
										$str
									);
				}
				// Replace DOS-style line endings to UNIX-style
				$str = str_replace( "\r\n", "\n", $str );
			}
		}
		return $str;
	}

	
	// Return path of the downloaded demo file or false
	function download_file($fname, $result=0, $import_type = '') {
		$rez = '';
		if ($import_type == 'ocdi') {
			$fname = trx_addons_ocdi_options('demo_url') . trim($fname);
		} else {			
			$fname = trailingslashit($this->options['demo_url']) . trim($this->options['demo_type']) . '/' . trim($fname);
		}
		
		// Download remote file
		if (substr($fname, 0, 5)=='http:' || substr($fname, 0, 6)=='https:') {
			$attempt = !empty($_POST['attempt']) ? (int) $_POST['attempt']+1 : 1;
			$response = download_url($fname, $this->max_time);
			if (is_string($response)) {
				$rez = $response;
				unset($this->response['attempt']);
				if ($this->options['debug']) 
					dfl(sprintf(__('Download file %s successful', 'trx_addons'), $fname));
			} else {
				if ($attempt < 3) {
					$this->response['attempt'] = $attempt;
					$this->response['result'] = $result;
					if ($this->options['debug']) {
						$error_log = sprintf(__("Attempt %d. Download the file '%s' failed.", 'trx_addons'), $attempt, $fname);
						dfl($error_log);
					}
				} else {
					unset($this->response['attempt']);
					$this->response['error'] = sprintf(__("Can't download the file '%s'.", 'trx_addons'), $fname);
					if ($this->options['debug']) 
						dfl($this->response['error']);
				}
			}
		} else {
			// File packed with theme
			$rez = file_exists($fname) ? $fname : trx_addons_get_file_dir($fname);
		}
		return $rez;
	}

	
	// Return content of the downloaded demo file or false
	function get_file($fname, $result=0, $import_type = '') {
		$attempt = !empty($_POST['attempt']) ? (int) $_POST['attempt']+1 : 1;
		if ($import_type == 'ocdi') {
			$fname = trx_addons_ocdi_options('demo_url') . trim($fname);
		} else {
			$fname = trailingslashit($this->options['demo_url']) . trim($this->options['demo_type']) . '/' . trim($fname);
		}
		$txt = trx_addons_fgc($fname, true);
		if (empty($txt)) {
			if ($attempt < 3) {
				$this->response['attempt'] = $attempt;
				$this->response['result'] = $result;
				if ($this->options['debug']) {
					$error_log = sprintf(__("Attempt %d. Loading data from the file '%s' is failed. ", 'trx_addons'), $attempt, $fname);
					dfl($error_log);
				}
			} else {
				unset($this->response['attempt']);
				$this->response['error'] = sprintf(__("Can't load data from the file '%s'.", 'trx_addons'), $fname);
				if ($this->options['debug']) {
					dfl($this->response['error']);
				}
			}
			$txt = false;
		} else {
			unset($this->response['attempt']);
			if ($this->options['debug']) {
				dfl(sprintf(__("File '%s' is loaded successful", 'trx_addons'), $fname));
			}
		}
		return $txt;
	}
}
