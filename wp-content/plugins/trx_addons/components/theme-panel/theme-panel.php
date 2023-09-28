<?php
/**
 * ThemeREX Addons: Panel with installation wizard, Theme Options and Support info
 *
 * @package ThemeREX Addons
 * @since v1.6.48
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Define component's subfolder
if ( !defined('TRX_ADDONS_PLUGIN_THEME_PANEL') ) define('TRX_ADDONS_PLUGIN_THEME_PANEL', TRX_ADDONS_PLUGIN_COMPONENTS . 'theme-panel/');
if ( !defined('TRX_ADDONS_PLUGIN_IMPORTER') )    define('TRX_ADDONS_PLUGIN_IMPORTER', TRX_ADDONS_PLUGIN_THEME_PANEL . 'importer/');
if ( !defined('TRX_ADDONS_PLUGIN_INSTALLER') )   define('TRX_ADDONS_PLUGIN_INSTALLER', TRX_ADDONS_PLUGIN_THEME_PANEL . 'installer/');

// Add Admin menu item to show Theme panel
if (!function_exists('trx_addons_theme_panel_admin_menu')) {
	add_action( 'admin_menu', 'trx_addons_theme_panel_admin_menu' );
	function trx_addons_theme_panel_admin_menu() {
		$theme_info  = trx_addons_get_theme_info();
		if (empty($theme_info['theme_pro_key'])) {
			add_menu_page(
				esc_html__('ThemeREX Addons', 'trx_addons'),	//page_title
				esc_html__('ThemeREX Addons', 'trx_addons'),	//menu_title
				'manage_options',								//capability
				'trx_addons_options',							//menu_slug
				'trx_addons_options_page_builder',				//callback
				'dashicons-welcome-widgets-menus',				//icon
				'50'											//menu position (after Dashboard)
			);
		} else {
			add_menu_page(
				esc_html__('Theme Panel', 'trx_addons'),	//page_title
				esc_html__('Theme Panel', 'trx_addons'),	//menu_title
				'manage_options',							//capability
				'trx_addons_theme_panel',					//menu_slug
				'trx_addons_theme_panel_page_builder',		//callback
				'dashicons-welcome-widgets-menus',			//icon
				'4'											//menu position (after Dashboard)
			);
			$submenu = apply_filters('trx_addons_filter_add_theme_panel_pages', array(
				array(
					esc_html__('Theme Dashboard', 'trx_addons'),//page_title
					esc_html__('Theme Dashboard', 'trx_addons'),//menu_title
					'manage_options',							//capability
					'trx_addons_theme_panel',					//menu_slug
					'trx_addons_theme_panel_page_builder'		//callback
					)
				)
			);
			if (is_array($submenu)) {
				foreach($submenu as $item) {
					add_submenu_page(
						'trx_addons_theme_panel',			//parent menu slug
						$item[0],							//page_title
						$item[1],							//menu_title
						$item[2],							//capability
						$item[3],							//menu_slug
						$item[4]							//callback
					);
				}
			}
		}
	}
}


// Load scripts and styles
if (!function_exists('trx_addons_theme_panel_load_scripts')) {
	add_action("trx_addons_action_load_scripts_admin", 'trx_addons_theme_panel_load_scripts');
	function trx_addons_theme_panel_load_scripts( $all = false ) {
		if (isset($_REQUEST['page']) && $_REQUEST['page']=='trx_addons_theme_panel') {
			wp_enqueue_style( 'trx_addons-msgbox', trx_addons_get_file_url('js/msgbox/msgbox.css'), array(), null );
			wp_enqueue_script( 'trx_addons-msgbox', trx_addons_get_file_url('js/msgbox/msgbox.js'), array('jquery'), null, true );
			wp_enqueue_style( 'trx_addons-options', trx_addons_get_file_url('css/trx_addons.options.css'), array(), null );
			wp_enqueue_style( 'trx_addons-theme_panel', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_THEME_PANEL . 'theme-panel.css'), array(), null );
			wp_enqueue_script( 'trx_addons-theme_panel', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_THEME_PANEL . 'theme-panel.js'), array('jquery'), null, true );
		}
	}
}


// Load RTL styles
if (!function_exists('trx_addons_theme_panel_load_scripts_rtl')) {
	add_action("trx_addons_action_load_scripts_admin", 'trx_addons_theme_panel_load_scripts_rtl', 100);
	function trx_addons_theme_panel_load_scripts_rtl( $all = false ) {
		if (isset($_REQUEST['page']) && $_REQUEST['page']=='trx_addons_theme_panel') {
			if ( is_rtl() ) {
				wp_enqueue_style( 'trx_addons-options-rtl', trx_addons_get_file_url('css/trx_addons.options-rtl.css'), array(), null );
				wp_enqueue_style( 'trx_addons-theme_panel-rtl', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_THEME_PANEL . 'theme-panel-rtl.css'), array(), null );
			}
		}
	}
}


// Return true if current screen need to load options scripts and styles
if ( !function_exists( 'trx_addons_theme_panel_need_options' ) ) {
	add_filter('trx_addons_filter_need_options', 'trx_addons_theme_panel_need_options');
	function trx_addons_theme_panel_need_options($need = false) {
		if (!$need) {
			// If current screen is 'Theme Panel'
			$need = isset($_REQUEST['page']) && $_REQUEST['page']=='trx_addons_theme_panel';
		}
		return $need;
	}
}

// Get the upgrade server domain URL
if ( !function_exists( 'trx_addons_get_upgrade_domain_url' ) ) {
	function trx_addons_get_upgrade_domain_url() {
		return '//upgrade.themerex.net/';
	}
}

// Get the upgrade server handler URL
if ( !function_exists( 'trx_addons_get_upgrade_url' ) ) {
	function trx_addons_get_upgrade_url( $params=array() ) {
		$url = trx_addons_get_upgrade_domain_url() . 'upgrade.php';
		if ( count( $params ) > 0 ) {
			$url = trx_addons_add_to_url( $url, $params );
		}
		return $url;
	}
}

// Call the upgrade server
if ( !function_exists( 'trx_addons_get_upgrade_data' ) ) {
	function trx_addons_get_upgrade_data( $params=array(), $info=array() ) {
		$theme_info = trx_addons_array_merge( trx_addons_get_theme_info(false), $info );
		$params = array_merge(
					array(
						'action'     => '',
						'key'        => '',
						'src'        => $theme_info['theme_pro_key'],
						'theme_slug' => $theme_info['theme_slug'],
						'theme_name' => $theme_info['theme_name'],
						'domain'     => trx_addons_remove_protocol( get_home_url(), true ),
					),
					$params
				);
		// Allow caching all info requests to reduce server load
		if ( strpos( $params['action'], 'info_' ) === false ) {
			$params['rnd'] = mt_rand();
		}
		$result = trx_addons_fgc( trx_addons_get_upgrade_url( $params ) );
		if ( is_serialized( $result ) ) {
			try {
				$result = trx_addons_unserialize( $result );
			} catch ( Exception $e ) {
			}
		}
		if ( ! isset( $result['error'] ) || ! isset( $result['data'] ) ) {
			global $TRX_ADDONS_STORAGE;
			$result = array(
				'error' => esc_html__( 'Unrecognized server answer!', 'trx_addons' )
							. ( ! empty( $TRX_ADDONS_STORAGE['last_remote_error'] )
								? ' ' . $TRX_ADDONS_STORAGE['last_remote_error']
								: ''
								),
				'data'  => ''
			);
		}
		return $result;
	}
}

// Check 'theme activated' status
if ( !function_exists( 'trx_addons_is_theme_activated' ) ) {
	function trx_addons_is_theme_activated( $suppress_filters = false ) {
		$template = get_template();
		$activated = get_option( sprintf( 'trx_addons_theme_%s_activated', $template ) ) == 1
					&& get_option( sprintf( 'purchase_code_%s', $template ) ) != '';
		return $suppress_filters
				? $activated
				: apply_filters( 'trx_addons_filter_is_theme_activated', $activated );
	}
}

// Set 'theme activated' status
if ( !function_exists( 'trx_addons_set_theme_activated' ) ) {
	function trx_addons_set_theme_activated($code='', $pro_key='', $token='') {
		$template = get_template();
		update_option( sprintf( 'trx_addons_theme_%s_activated', $template ), 1);
		if ( ! empty($code) ) {
			update_option( sprintf( 'purchase_code_%s', $template ), $code );
			update_option( sprintf( 'purchase_code_src_%s', $template ), $pro_key );
			if ( ! empty($token) ) {
				update_option( sprintf( 'access_token_%s', $template ), $token );
			}
		}
	}
}

// Remove 'theme activated' status
if ( !function_exists( 'trx_addons_remove_theme_activated' ) ) {
	function trx_addons_remove_theme_activated() {
		$template = get_template();
		delete_option( sprintf( 'trx_addons_theme_%s_activated', $template ) );
		delete_option( sprintf( 'purchase_code_%s', $template ) );
		delete_option( sprintf( 'purchase_code_src_%s', $template ) );
		delete_option( sprintf( 'access_token_%s', $template ) );
	}
}

// Return 'theme activated' status
if ( !function_exists( 'trx_addons_get_theme_activated_status' ) ) {
	function trx_addons_get_theme_activated_status() {
		return trx_addons_is_theme_activated() ? 'active' : 'inactive';
	}
}

// Return theme activation code
if ( !function_exists( 'trx_addons_get_theme_activation_code' ) ) {
	function trx_addons_get_theme_activation_code() {
		$template = get_template();
		return get_option( sprintf( 'trx_addons_theme_%s_activated', $template ) ) == 1
				? get_option( sprintf( 'purchase_code_%s', $template ) )
				: '';
	}
}

// Activate theme
if ( !function_exists( 'trx_addons_theme_panel_activate_theme' ) ) {
	add_action('init', 'trx_addons_theme_panel_activate_theme', 9);
	function trx_addons_theme_panel_activate_theme() {

		if (is_admin() && isset($_REQUEST['page']) && $_REQUEST['page']=='trx_addons_theme_panel' && trx_addons_get_value_gp('trx_addons_deactivate_theme')=='') {

			// If submit form with activation code
			$nonce  = trx_addons_get_value_gp('trx_addons_nonce');
			$source = trx_addons_get_value_gp('trx_addons_activate_theme_source');
			if ( $source == 'token' ) {
				$code = trx_addons_get_value_gp('trx_addons_activate_theme_token');
			} else {
				$code = trx_addons_get_value_gp('trx_addons_activate_theme_code');
			}

			if ( !empty( $nonce ) ) {

				// Check nonce
				if ( ! wp_verify_nonce( $nonce, admin_url() ) ) {
					trx_addons_set_admin_message(__('Security code is invalid! Theme is not activated!', 'trx_addons'), 'error');
				
				// Check user 
				} else if ( ! current_user_can( 'manage_options' ) ) {
					trx_addons_set_admin_message(__('Activation theme is denied for the current user!', 'trx_addons'), 'error');

				// Code is not specified
				} else if ( empty( $code ) ) {
					if ( $source == 'token' ) {
						trx_addons_set_admin_message(__('Please, specify the License code (Token) from Envato Elements!', 'trx_addons'), 'error');
					} else {
						trx_addons_set_admin_message(__('Please, specify the purchase code!', 'trx_addons'), 'error');
					}

				// Check code
				} else {
					$theme_info = trx_addons_get_theme_info(false);
					if ( $source == 'token' ) {
						$theme_info['theme_pro_key'] = 'env-elements';
					}
					$params = array(
						'action' => 'check',
						'key' => $code,
					);
					if ( (int) trx_addons_get_value_gp('trx_addons_user_agree') == 1 ) {
						$user_name = sanitize_text_field(trx_addons_get_value_gp('trx_addons_user_name'));
						$user_email = sanitize_email(trx_addons_get_value_gp('trx_addons_user_email'));
						if ( ! empty($user_name) && ! empty($user_email) ) {
							$params['user_name'] = $user_name;
							$params['user_email'] = $user_email;
						}
					}
					$result = trx_addons_get_upgrade_data( $params, array( 'theme_pro_key' => $theme_info['theme_pro_key'] ) );
					if ( $result['data'] === 1 ) {
						trx_addons_set_theme_activated( $code, $theme_info['theme_pro_key'] );
						trx_addons_set_admin_message(__('Congratulations! Your theme is activated successfully.', 'trx_addons'), 'success');
					} else {
						trx_addons_set_admin_message( sprintf( __("Theme is not activated! Reason: %s", 'trx_addons'),
																! empty($result['error']) && substr($result['error'], 0, 3) != '>>>'
																	? $result['error']
																	: __("Purchase Code is either invalid or previously used. If it was used and requires reactivation contact our Customer Support.", 'trx_addons')
																),
													'error'
													);
					}
					if ( ! empty($result['error']) && substr($result['error'], 0, 3) == '>>>' ) {
						wp_redirect(substr($result['error'], 3));
					}
				}
			}
		}
	}
}


// Deactivate theme ( disconnect from current domain )
if ( !function_exists( 'trx_addons_theme_panel_deactivate_theme' ) ) {
	add_action('init', 'trx_addons_theme_panel_deactivate_theme', 9);
	function trx_addons_theme_panel_deactivate_theme() {
		if (is_admin() && isset($_REQUEST['page']) && $_REQUEST['page']=='trx_addons_theme_panel') {
			// If submit form with activation code
			$nonce  = trx_addons_get_value_gp('trx_addons_nonce');
			$action = trx_addons_get_value_gp('trx_addons_deactivate_theme');
			if ( $action == 'deactivate' && ! empty( $nonce ) ) {

				$code = get_option( sprintf( 'purchase_code_%s', get_template() ) );

				// Check nonce
				if ( !wp_verify_nonce( $nonce, admin_url() ) ) {
					trx_addons_set_admin_message(__('Security code is invalid! Theme is not deactivated!', 'trx_addons'), 'error');
				
				// Check user
				} else if (!current_user_can('manage_options')) {
					trx_addons_set_admin_message(__('Deactivation theme is denied for the current user!', 'trx_addons'), 'error');

				// Check code
				} else if ( empty( $code ) ) {
					trx_addons_set_admin_message(__('Purchase code is invalid! Theme is not deactivated!', 'trx_addons'), 'error');

				// Deactivate
				} else {
					$result = trx_addons_get_upgrade_data( array(
						'action' => 'deactivate',
						'key' => $code,
					) );
					if ( $result['data'] === 1 ) {
						trx_addons_remove_theme_activated();
						trx_addons_set_admin_message( __( 'Your theme is deactivated successfully.', 'trx_addons' ), 'success' );
					} else {
						trx_addons_set_admin_message( sprintf( __( "Theme is not deactivated! Reason: %s", 'trx_addons' ),
																! empty( $result['error'] )
																	? $result['error']
																	: __( "Your purchase code is invalid!", 'trx_addons' )
															),
													'error'
													);
					}
				}
			}
		}
	}
}


// Build Theme panel page
if (!function_exists('trx_addons_theme_panel_page_builder')) {
	function trx_addons_theme_panel_page_builder() {
		$tabs   = apply_filters('trx_addons_filter_theme_panel_tabs', array(
								'general' => esc_html__( 'General', 'trx_addons' ),
								'plugins' => esc_html__( 'Plugins', 'trx_addons' ),
								));
		?>
		<span class="wp-header-end" style="display:none"></span>

		<div id="trx_addons_theme_panel" class="trx_addons_theme_panel">

			<?php do_action( 'trx_addons_action_theme_panel_start' ); ?>

			<div class="trx_addons_result">
				<?php
				$result = trx_addons_get_admin_message();
				if (!empty($result['error'])) {
					?><div class="error"><p><?php echo wp_kses_data($result['error']); ?></p></div><?php
				} else if (!empty($result['success'])) {
					?><div class="updated"><p><?php echo wp_kses_data($result['success']); ?></p></div><?php
				}
				?>
			</div>

			<?php do_action( 'trx_addons_action_theme_panel_before_tabs' ); ?>

			<div class="trx_addons_tabs trx_addons_tabs_theme_panel">
				<ul>
					<?php
					foreach($tabs as $tab_id => $tab_title) {
						?><li><a href="#trx_addons_theme_panel_section_<?php echo esc_attr($tab_id); ?>"><?php echo esc_html( $tab_title ); ?></a></li><?php
					}
					?>
				</ul>
				<?php
					$theme_info = trx_addons_get_theme_info();
					foreach($tabs as $tab_id => $tab_title) {
						do_action('trx_addons_action_theme_panel_section', $tab_id, $theme_info);
					}
				?>
			</div>

			<?php do_action( 'trx_addons_action_theme_panel_after_tabs' ); ?>

			<?php do_action( 'trx_addons_action_theme_panel_end' ); ?>

		</div>
		<?php		
	}
}


// Display 'General' section
if ( !function_exists( 'trx_addons_theme_panel_section_general' ) ) {
	add_action('trx_addons_action_theme_panel_section', 'trx_addons_theme_panel_section_general', 10, 2);
	function trx_addons_theme_panel_section_general($tab_id, $theme_info) {
		if ($tab_id !== 'general') return;
		$theme_status = trx_addons_get_theme_activated_status();
		$need_child   = get_template_directory() == get_stylesheet_directory() && ! is_multisite() && current_user_can( 'install_themes' );
		?>
		<div id="trx_addons_theme_panel_section_<?php echo esc_attr($tab_id); ?>" class="trx_addons_tabs_section">

			<?php do_action('trx_addons_action_theme_panel_section_start', $tab_id, $theme_info); ?>

			<div class="trx_addons_theme_panel_section_content trx_addons_theme_panel_theme_<?php echo esc_attr($theme_status); ?>">

				<?php do_action('trx_addons_action_theme_panel_before_section_title', $tab_id, $theme_info); ?>
	
				<h1 class="trx_addons_theme_panel_section_title">
					<?php
					echo esc_html(
						sprintf(
							// Translators: Add theme name and version to the 'Welcome' message
							__( 'Welcome to %1$s v.%2$s', 'trx_addons' ),
							$theme_info['theme_name'],
							$theme_info['theme_version']
						)
					);
					?>
					<span class="trx_addons_theme_panel_section_title_label_<?php echo esc_attr($theme_status); ?>"><?php
						if ($theme_status == 'active') {
							esc_html_e('Activated', 'trx_addons');
						} else {
							esc_html_e('Not activated', 'trx_addons');
						}
					?></span>
				</h1><?php

				do_action('trx_addons_action_theme_panel_after_section_title', $tab_id, $theme_info);

				?><div class="trx_addons_theme_panel_section_description">
					<p><?php
						if ( $theme_status == 'active' ) {
							if ( $need_child ) {
								// Main theme is active
								esc_html_e('We strongly recommend installing a Child Theme on this step. It saves you from losing any changes you make to the theme files during updates. Even if you\'re not going to do that we still recommend it. If you want to skip the step - click "Start Setup" button.', 'trx_addons');
							} else {
								// Child theme is active
								esc_html_e('Thank you for choosing our theme! In order to get started, you need to select a demo, install recommended plugins and import the demo data. You can do all these steps manually, or follow our setup wizard by clicking the "Start Setup" button below:', 'trx_addons');
							}
						} else {
							esc_html_e('Thank you for choosing our theme! Please activate your copy of the theme in order to get access to plugins, demo content, support and updates.', 'trx_addons');
						}
					?></p>
				</div><?php
				if ( $theme_status != 'active' ) {
					do_action('trx_addons_action_theme_panel_activation_form', $tab_id, $theme_info);
				}

				do_action('trx_addons_action_theme_panel_after_section_description', $tab_id, $theme_info);

				if ($theme_status == 'active') {
					?><div class="trx_addons_theme_panel_buttons"><?php
						if ( $need_child ) {
							$child_present = is_dir( get_template_directory() . '/../' . get_template() . '-child' );
							?>
							<a href="<?php
								echo esc_url( admin_url( $child_present ? 'themes.php' : 'theme-install.php' ) );
								?>" class="trx_addons_theme_panel_child_theme trx_addons_button"><?php echo esc_html( $child_present ? __('Activate Child Theme', 'trx_addons') : __('Install Child Theme', 'trx_addons') ); ?></a>
							<a href="#" class="trx_addons_theme_panel_next_step trx_addons_button trx_addons_button_accent"><?php esc_html_e('Start Setup', 'trx_addons'); ?></a>
							<?php
						} else {
							?>
							<a href="#" class="trx_addons_theme_panel_next_step trx_addons_button trx_addons_button_accent"><?php esc_html_e('Start Setup', 'trx_addons'); ?></a>
							<?php
						}
					?></div><?php
				}

				if ( $theme_status == 'active' && trx_addons_is_theme_activated(true) ) {
	
					do_action('trx_addons_action_theme_panel_before_section_license', $tab_id, $theme_info);

					?><div class="trx_addons_theme_panel_section_license">
						<h2 class="trx_addons_theme_panel_section_license_title"><?php esc_html_e( 'License information', 'trx_addons' ); ?></h2>
						<div class="trx_addons_theme_panel_section_license_description">
							<p class="trx_addons_theme_panel_section_license_info"><?php esc_html_e('1 theme license = 1 domain. In order to connect the license to a different domain, first click "Disconnect domain" and then re-enter the purchase key on a different WordPress installation.', 'trx_addons'); ?></p>
							<p class="trx_addons_theme_panel_section_license_info"><?php esc_html_e('Please Note! Before you take any actions that will require another Theme Activation (Data Base cleaning, WordPress re-installation, etc) make sure to Deactivate theme on this domain by pressing "Disconnect domain" button. Without deactivation any further activation attempt will fail (even on the same domain).', 'trx_addons'); ?></p>
							<p class="trx_addons_theme_panel_section_license_status">
								<span class="trx_addons_theme_panel_section_license_status_label"><?php esc_html_e('Status:', 'trx_addons'); ?></span>
								<span class="trx_addons_theme_panel_section_license_status_state"><?php esc_html_e('Activated', 'trx_addons'); ?></span>
							</p>
							<p class="trx_addons_theme_panel_section_license_domain">
								<span class="trx_addons_theme_panel_section_license_domain_label"><?php esc_html_e('Domain:', 'trx_addons'); ?></span>
								<span class="trx_addons_theme_panel_section_license_domain_state"><?php echo esc_html( trx_addons_remove_protocol( get_home_url(), true ) ); ?></span>
							</p>
						</div>
						<div class="trx_addons_theme_panel_section_license_buttons">
							<form action="<?php echo esc_url(get_admin_url(null, 'admin.php?page=trx_addons_theme_panel')); ?>" name="trx_addons_theme_panel_deactivate_form" method="post">
								<input type="hidden" name="trx_addons_nonce" value="<?php echo esc_attr(wp_create_nonce(admin_url())); ?>" />
								<input type="hidden" name="trx_addons_deactivate_theme" value="deactivate" />
								<a href="#" class="trx_addons_button trx_addons_theme_panel_license_disconnect"><?php esc_html_e('Disconnect domain', 'trx_addons'); ?></a>
							</form>
						</div>
					</div><?php

					do_action('trx_addons_action_theme_panel_after_section_license', $tab_id, $theme_info);
				}

			?></div><?php

			// Attention! This is inline-blocks and no spaces allow
			?><div class="trx_addons_theme_panel_featured_item_wrap"><?php

				if ( $theme_status == 'active' ) {
					trx_addons_theme_panel_featured_item( $tab_id, $theme_info );
					trx_addons_theme_panel_show_sys_info( $tab_id, $theme_info );
				}

			?></div><?php

			do_action('trx_addons_action_theme_panel_section_end', $tab_id, $theme_info);

		?></div><?php
	}
}


// Display footer icons on the tab 'General'
if ( !function_exists( 'trx_addons_theme_panel_footer_icons' ) ) {
	add_action('trx_addons_action_theme_panel_section_end', 'trx_addons_theme_panel_footer_icons', 100, 2);
	function trx_addons_theme_panel_footer_icons($tab_id, $theme_info) {
		if ( $tab_id == 'general' ) {
			// Footer icons
			?>
			<div class="trx_addons_theme_panel_footer">
				<?php
				if (count($theme_info['theme_actions']) > 0) {
					?>
					<div class="trx_addons_theme_panel_links trx_addons_theme_panel_links_iconed">
						<?php
						foreach ($theme_info['theme_actions'] as $action=>$item) {
							if ( empty( $item['button'] ) ) {
								continue;
							}
							?><div class="trx_addons_iconed_block"><div class="trx_addons_iconed_block_inner">
								<?php
								if (!empty($item['icon']) && trx_addons_is_url( $item['icon'] ) ) {
									$item['image'] = $item['icon'];
									$item['icon'] = '';
								}
								if (!empty($item['icon'])) {
									?><span class="trx_addons_iconed_block_icon <?php echo esc_attr($item['icon']); ?>"><?php
								} else if (!empty($item['image'])) {
									?><img src="<?php echo esc_attr($item['image']); ?>" class="trx_addons_iconed_block_image"><?php
								}
								?>
								<h2 class="trx_addons_iconed_block_title"><?php
									echo esc_html($item['title']);
								?></h2>
								<div class="trx_addons_iconed_block_description"><?php
									echo esc_html($item['description']);
								?></div>
								<?php
								$links = array(
									array(
										'link' => $item['link'],
										'button' => $item['button']
									)
								);
								if ( strpos( $item['link'], 'customize.php' ) !== false && function_exists('menu_page_url') ) {
									$links[] = array(
										'link' => menu_page_url( 'theme_options', false ),
										'button' => esc_html__( 'Theme Options', 'trx_addons' )
									);
								}
								$cnt = 0;
								foreach( $links as $link ) {
									$cnt++;
									if ($cnt > 1) {
										?><span class="trx_addons_iconed_block_link_delimiter"></span><?php
									}
									?>
									<a href="<?php echo esc_url( $link['link'] ); ?>" class="trx_addons_iconed_block_link"<?php
										if (strpos($link['link'], home_url()) === false) {
											echo ' target="_blank"';
										}
									?>>
										<?php echo esc_html($link['button']); ?>
									</a>
									<?php
								}
								?>
							</div></div><?php
						}
					?>
					</div>
					<?php
				}
				?>
			</div>
			<?php
		}
	}
}


// Display featured item (theme) from our server
if ( !function_exists( 'trx_addons_theme_panel_featured_item' ) ) {
	function trx_addons_theme_panel_featured_item($tab_id, $theme_info) {
		$banners = get_transient( 'trx_addons_welcome_banners' );
		$banners_url = trailingslashit( dirname( esc_url( trx_addons_get_protocol() . ':' . apply_filters( 'trx_addons_filter_get_theme_data', '', 'theme_demofiles_url' ) ) ) ) . '_welcome/';
		if ( ! $banners ) {
			$txt = trx_addons_fgc( $banners_url . 'welcome.json' );
			if (!empty($txt) && substr($txt, 0, 1) == '[') {
				$banners = json_decode($txt, true);
				if ( is_array($banners) && count($banners) > 0 ) {
					set_transient('trx_addons_welcome_banners', $banners, 4*60*60);		//Save for 4 hours
				}
			}
		}
		$html = '';
		if ( is_array($banners) && count($banners) > 0 ) {
			$html .= '<div class="trx_addons_theme_panel_banners">';
			foreach ($banners as $banner) {
				// Prepare links
				if (!empty($banner['image']) && ! trx_addons_is_url( $banner['image'] ) ) {
					$banner['image'] = $banners_url . trim($banner['image']);
				}
				if (!empty($banner['icon']) && ! trx_addons_is_url( $banner['icon'] ) && strpos($banner['icon'], 'dashicons') === false && strpos($banner['icon'], 'trx_addons_icon') === false) {
					$banner['icon'] = $banners_url . trim($banner['icon']);
				}
				if (!empty($banner['url']) && substr($banner['url'], 0, 1) === '#') {
					$banner['url'] = apply_filters( 'trx_addons_filter_get_theme_data', '', substr($banner['url'], 1) );
				}
				if (!empty($banner['link_url']) && substr($banner['link_url'], 0, 1) === '#') {
					$banner['link_url'] = apply_filters( 'trx_addons_filter_get_theme_data', '', substr($banner['link_url'], 1) );
				}
				// Build banner's layout
				$html .= '<div class="trx_addons_theme_panel_banners_item' . ( count( $banners ) > 1 ? ' trx_banners_item' : '' ) . '"'
							. (!empty($banner['duration'])
								? ' data-duration="' . esc_attr(max(1000, min(60000, $banner['duration']*($banner['duration']<1000 ? 1000 : 1)))) . '"'
								: ''
							)
						. '>';
				// Title
				if (!empty($banner['title'])) {
					$html .= '<div class="trx_addons_theme_panel_banners_item_header">'
								. ( ! empty($banner['link_url'])
										? '<a class="trx_addons_theme_panel_banners_item_link" href="' . esc_url($banner['link_url']) . '" target="_blank">' . wp_kses($banner['link_text'], 'trx_addons_kses_content') . '</a>'
										: ''
									)
								. ( ! empty($banner['icon'])
										? ( trx_addons_is_url( $banner['icon'] )
											? '<span class="trx_addons_theme_panel_banners_item_icon with_image"><img src="' . esc_url($banner['icon']) . '"></span>'
											: '<span class="trx_addons_theme_panel_banners_item_icon ' . esc_attr($banner['icon']) . '"></span>'
											)
										: ''
									)
								. '<h2 class="trx_addons_theme_panel_banners_item_title">' . esc_html($banner['title']) . '</h2>'
							. '</div>';
				}
				// Image
				if (!empty($banner['image'])) {
					$html .= '<div class="trx_addons_theme_panel_banners_item_image">'
									. ( !empty($banner['url'])
										? '<a href="' . esc_url($banner['url']) . '" target="_blank">'
										: ''
										)
									. '<img src="' . esc_url($banner['image']) . '">'
									. ( !empty($banner['url'])
										? '<span class="trx_addons_theme_panel_banners_item_image_mask">' . ( ! empty($banner['url_text']) ? $banner['url_text'] : esc_html__( 'Live Preview', 'trx_addons' ) ) . '</span></a>'
										: ''
										)
								. '</div>';
				}
				$html .= '</div>';
			}
			$html .= '</div>';
		}
		if ( ! empty( $html ) ) {
			?><div class="trx_addons_theme_panel_featured_item">
				<?php trx_addons_show_layout( $html ); ?>
			</div><?php
		}
	}
}

// Display system info
if ( ! function_exists( 'trx_addons_theme_panel_show_sys_info' ) ) {
	function trx_addons_theme_panel_show_sys_info( $tab_id, $theme_info ) {
		?><div class="trx_addons_theme_panel_sys_info">
			<table class="trx_addons_theme_panel_table" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<th class="trx_addons_theme_panel_info_param"><span class="dashicons dashicons-yes-alt"></span><?php esc_html_e('System Check', 'trx_addons'); ?></th>
					<th class="trx_addons_theme_panel_info_value"><?php esc_html_e('Current', 'trx_addons'); ?></th>
					<th class="trx_addons_theme_panel_info_advise"><?php esc_html_e('Suggested', 'trx_addons'); ?></th>
				</tr>
				<?php
				$sys_info = trx_addons_get_sys_info();
				$checked  = true;
				foreach ( $sys_info as $k => $item ) {
					$checked = $checked && ( ! isset($item['checked']) || $item['checked'] );
					?>
					<tr>
						<td class="trx_addons_theme_panel_info_param<?php
							if ( ! empty( $item['description'] ) ) {
								echo ' trx_addons_tooltip_present';
							}
						?>"><?php
							if ( ! empty( $item['description'] ) ) {
								?><span class="trx_addons_tooltip" data-tooltip-text="<?php echo esc_attr( $item['description'] ); ?>"><?php
									echo esc_html($item['title']);
								?></span><?php
							} else {
								echo esc_html($item['title']);
							}
						?></td>
						<td class="trx_addons_theme_panel_info_value<?php
						if (isset($item['checked'])) {
							echo ' trx_addons_theme_panel_info_param_' . ( $item['checked'] ? 'checked' : 'unchecked' );
						}
						?>"><?php echo esc_html($item['value']); ?></td>
						<td class="trx_addons_theme_panel_info_advise"><?php echo esc_html($item['recommended']); ?></td>
					</tr>
					<?php
				}
				?>
			</table>
			<?php
			if ( ! $checked ) {
				?>
				<div class="trx_addons_theme_panel_sys_info_check_result trx_addons_info_box trx_addons_info_box_warning">
					<p><?php
						echo wp_kses_data(
							__("It seems that your server doesn't comply with the theme requirements. You may encounter problems during the upload skins or addons or demo data installation.", 'trx_addons')
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
		?></div><?php
	}
}

// Display the theme activation form
if ( !function_exists( 'trx_addons_theme_panel_activation_form' ) ) {
	add_action('trx_addons_action_theme_panel_activation_form', 'trx_addons_theme_panel_activation_form');
	function trx_addons_theme_panel_activation_form() {
		$activation_methods = apply_filters( 'trx_addons_filter_activation_methods', array(
												'purchase_key' => true,
												'elements_key' => true
											) );
		$activation_methods_total = ( ! empty( $activation_methods['purchase_key'] ) ? 1 : 0 )
									+ ( ! empty( $activation_methods['elements_key'] ) ? 1 : 0 );
		if ( $activation_methods_total > 0 ) {
			?>
			<div class="trx_addons_theme_panel_section_form_wrap">
				<form action="<?php echo esc_url(get_admin_url(null, 'admin.php?page=trx_addons_theme_panel')); ?>" class="trx_addons_theme_panel_section_form" name="trx_addons_theme_panel_activate_form" method="post">
					<input type="hidden" name="trx_addons_nonce" value="<?php echo esc_attr(wp_create_nonce(admin_url())); ?>" />
					<h3 class="trx_addons_theme_panel_section_form_title"><?php esc_html_e('Activate Your Theme and Support Account', 'trx_addons'); ?></h3>
					<div class="trx_addons_columns_wrap">
						<div class="trx_addons_column-1_2">
							<div class="trx_addons_theme_panel_section_form_field trx_addons_theme_panel_section_form_field_text">
								<label>
									<span class="trx_addons_theme_panel_section_form_field_label"><?php esc_attr_e('Name:', 'trx_addons'); ?></span>
									<input type="text" name="trx_addons_user_name" placeholder="<?php esc_attr_e('Your name', 'trx_addons'); ?>">
								</label>
							</div>
						</div><div class="trx_addons_column-1_2">
							<div class="trx_addons_theme_panel_section_form_field trx_addons_theme_panel_section_form_field_text">
								<label>
									<span class="trx_addons_theme_panel_section_form_field_label"><?php esc_attr_e('E-mail:', 'trx_addons'); ?></span>
									<input type="text" name="trx_addons_user_email" placeholder="<?php esc_attr_e('Your e-mail', 'trx_addons'); ?>">
								</label>
							</div>
						</div><div class="trx_addons_column-1_1">
							<div class="trx_addons_theme_panel_section_form_field trx_addons_theme_panel_section_form_field_text<?php if ( $activation_methods_total == 1 ) echo ' trx_addons_hidden'; ?>">
								<?php
								if ( ! empty( $activation_methods['purchase_key'] ) ) {
									?>
									<label>
										<input type="radio" name="trx_addons_activate_theme_source" value="code" checked="checked">
										<span class="trx_addons_theme_panel_section_form_field_caption"><?php esc_attr_e('I have a purchase code', 'trx_addons'); ?></span>
									</label>
									<?php
								}
								if ( $activation_methods_total > 1 ) {
									?><br><?php
								}
								if ( ! empty( $activation_methods['elements_key'] ) ) {
									?>
									<label>
										<input type="radio" name="trx_addons_activate_theme_source" value="token"<?php if ( $activation_methods_total == 1 ) echo ' checked="checked"'; ?>>
										<span class="trx_addons_theme_panel_section_form_field_caption"><?php esc_attr_e('I downloaded the theme from Envato Elements', 'trx_addons'); ?></span>
									</label>
									<?php
								}
								?>
							</div>
							<?php
							if ( ! empty( $activation_methods['purchase_key'] ) ) {
								?>
								<div class="trx_addons_theme_panel_section_form_field trx_addons_theme_panel_section_form_field_text trx_addons_theme_panel_section_form_field_param_code">
									<label>
										<span class="trx_addons_theme_panel_section_form_field_label"><?php esc_attr_e('Purchase code', 'trx_addons'); ?> <sup class="required">*</sup></span>
										<input type="text" name="trx_addons_activate_theme_code" placeholder="<?php esc_attr_e('Purchase code (required)', 'trx_addons'); ?>">
										<span class="trx_addons_theme_panel_section_form_field_description"><?php
											echo esc_html__( "Can't find the purchase code?", 'trx_addons' )
													. ' '
													. apply_filters( 'trx_addons_filter_get_purchase_code_link',
														'<a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank">'
															. esc_html__('Follow this guide.', 'trx_addons')
														. '</a>'
														);
										?></span>
									</label>
								</div>
								<?php
							}
							if ( ! empty( $activation_methods['elements_key'] ) ) {
								?>
								<div class="trx_addons_theme_panel_section_form_field trx_addons_theme_panel_section_form_field_text trx_addons_theme_panel_section_form_field_param_token trx_addons_hidden">
									<label>
										<span class="trx_addons_theme_panel_section_form_field_label"><?php esc_attr_e('Envato Elements Token', 'trx_addons'); ?> <sup class="required">*</sup></span>
										<input type="text" name="trx_addons_activate_theme_token" placeholder="<?php esc_attr_e('Envato Elements Token (required)', 'trx_addons'); ?>">
										<span class="trx_addons_theme_panel_section_form_field_description"><?php
											echo sprintf(
													esc_html__( '%1$s or try using %2$s to generate a new Envato Elements Token.', 'trx_addons' ),
													apply_filters( 'trx_addons_filter_get_elements_token_link',
														'<a href="'
															. esc_url( 'https://api.extensions.envato.com/extensions/begin_activation'
																. '?extension_id=bec21c4c-b621-4fef-9080-cf24c6415957'
																. '&extension_type=envato-wordpress'
																. '&extension_description=' . wp_get_theme()->get( 'Name' ) . ' (' . get_home_url() . ')'
																. '&utm_content=settings'
																)
														. '" target="_blank">'
																. esc_html__('Follow this link', 'trx_addons')
														. '</a>'
													),
													apply_filters( 'trx_addons_filter_get_elements_token_link_alter',
														'<a href="'
															. esc_url( 'https://api.extensions.envato.com/extensions/begin_activation'
																. '?extension_id=7c7270f606fb77cc9c8b45ccad352290'
																. '&extension_type=envato-wordpress'
																. '&extension_description=' . wp_get_theme()->get( 'Name' ) . ' (' . get_home_url() . ')'
																)
														. '" target="_blank">'
																. esc_html__('an alternative link', 'trx_addons')
														. '</a>'
													)
												);
										?></span>
									</label>
								</div>
								<?php
							}
							?>
							<div class="trx_addons_theme_panel_section_form_field trx_addons_theme_panel_section_form_field_checkbox">
								<label>
									<input type="checkbox" name="trx_addons_user_agree" value="1">
									<?php
										echo sprintf(
												wp_kses( __('Your data is stored and processed in accordance with our %s.', 'trx_addons'), 'trx_addons_kses_content' ),
												'<a href="' . apply_filters('trx_addons_filter_privacy_url', '//themerex.net/privacy-policy/') . '" target="_blank">' . esc_html__('Privacy Policy', 'trx_addons') . '</a>'
												);
									?>
								</label>
							</div>
							<div class="trx_addons_theme_panel_section_form_field trx_addons_theme_panel_section_form_field_submit">
								<input type="submit" class="trx_addons_button trx_addons_button_large trx_addons_button_accent" value="<?php esc_attr_e('Submit', 'trx_addons'); ?>">
							</div>
						</div>
					</div>
				</form>
			</div>
			<?php
		}
	}
}


// Display 'Plugins' section
if ( !function_exists( 'trx_addons_theme_panel_section_plugins' ) ) {
	add_action('trx_addons_action_theme_panel_section', 'trx_addons_theme_panel_section_plugins', 10, 2);
	function trx_addons_theme_panel_section_plugins($tab_id, $theme_info) {
		if ($tab_id !== 'plugins') return;
		?>
		<div id="trx_addons_theme_panel_section_<?php echo esc_attr($tab_id); ?>" class="trx_addons_tabs_section">
			
			<?php
			do_action('trx_addons_action_theme_panel_section_start', $tab_id, $theme_info);

			if ( trx_addons_is_theme_activated() ) {
				?>
				<div class="trx_addons_theme_panel_section_content trx_addons_theme_panel_plugins_installer">

					<?php do_action('trx_addons_action_theme_panel_before_section_title', $tab_id, $theme_info); ?>
		
					<h1 class="trx_addons_theme_panel_section_title">
						<?php esc_html_e( 'Plugins', 'trx_addons' ); ?>
					</h1>

					<?php do_action('trx_addons_action_theme_panel_after_section_title', $tab_id, $theme_info); ?>

					<div class="trx_addons_theme_panel_section_description">
						<p><?php echo wp_kses_data( __( "Install and activate theme-related plugins. Select only those plugins that you're planning to use. You can also install plugins via \"Appearance - Install Plugins\".", 'trx_addons' ) ); ?></p>
					</div>

					<div class="trx_addons_info_box">
						<p class="trx_addons_theme_panel_section_info_notice"><b><?php esc_html_e('Attention!', 'trx_addons'); ?></b> <?php echo wp_kses_data( __( "Sometimes, the activation of some plugins interferes with the process of other plugins' installation. If a plugin is still on the 'Activating' stage after 1 minute, just reload the page (by pressing F5) and then switch to the 'Plugins' tab; there you should check the required plugins that remained uninstalled and proceed with the installation ('Install & Activate' button below the list of plugins)", 'trx_addons' ) ); ?></p>
					</div>

					<?php do_action('trx_addons_action_theme_panel_before_section_buttons', $tab_id, $theme_info); ?>

					<div class="trx_addons_theme_panel_plugins_buttons">
						<a href="#" class="trx_addons_theme_panel_plugins_button_select trx_addons_button trx_addons_button_small"><?php esc_html_e('Select all', 'trx_addons'); ?></a>
						<a href="#" class="trx_addons_theme_panel_plugins_button_deselect trx_addons_button trx_addons_button_small"><?php esc_html_e('Deselect all', 'trx_addons'); ?></a>
					</div><?php

					do_action('trx_addons_action_theme_panel_before_list_items', $tab_id, $theme_info);

					// List of plugins
					?>
					<div class="trx_addons_theme_panel_plugins_list"><?php
						if ( is_array( $theme_info['theme_plugins'] ) ) {
							foreach ($theme_info['theme_plugins'] as $plugin_slug => $plugin_data) {
								if (isset($plugin_data['install']) && $plugin_data['install'] === false) {
									continue;
								}
								$plugin_state = trx_addons_plugins_installer_check_plugin_state( $plugin_slug );
								// Uncomment next line if you want to hide already activated plugins
								//if ($plugin_state == 'deactivate') continue;
								$plugin_link = trx_addons_plugins_installer_get_link( $plugin_slug, $plugin_state );
								$plugin_image = !empty($plugin_data['logo'])
														? ( trx_addons_is_url( $plugin_data['logo'] )
															? $plugin_data['logo']
															: apply_filters( 'trx_addons_filter_plugin_logo', trailingslashit( get_template_directory_uri() ) . 'plugins/' . sanitize_file_name($plugin_slug) . '/' . $plugin_data['logo'], $plugin_slug, $plugin_data )
															)
														: trx_addons_get_no_image();
								?><div class="trx_addons_theme_panel_plugins_list_item<?php
									if ( !empty($plugin_data['required']) && $plugin_state != 'deactivate' ) echo ' trx_addons_theme_panel_plugins_list_item_checked';
								?>">
									<a href="<?php echo esc_url($plugin_link); ?>"
											class="trx_addons_theme_panel_plugins_list_item_link"
											data-slug="<?php echo esc_attr( $plugin_slug ); ?>"
											data-name="<?php echo esc_attr( $plugin_slug ); ?>"
											data-required="<?php echo !empty($plugin_data['required']) ? '1' : '0'; ?>"
											data-state="<?php echo esc_attr( $plugin_state ); ?>"
											data-activate-nonce="<?php echo esc_url(trx_addons_plugins_installer_get_link( $plugin_slug, 'activate' )); ?>"
											data-install-label="<?php esc_attr_e( 'Not installed', 'trx_addons' ); ?>"
											data-install-label-selected="<?php esc_attr_e( 'Install', 'trx_addons' ); ?>"
											data-install-progress="<?php esc_attr_e( 'Installing ...', 'trx_addons' ); ?>"
											data-activate-label="<?php esc_attr_e( 'Not activated', 'trx_addons' ); ?>"
											data-activate-label-selected="<?php esc_attr_e( 'Activate', 'trx_addons' ); ?>"
											data-activate-progress="<?php esc_attr_e( 'Activating ...', 'trx_addons' ); ?>"
											data-deactivate-label="<?php esc_attr_e( 'Active', 'trx_addons' ); ?>"
											tabindex="<?php echo 'deactivate' == $plugin_state ? '-1' : '0'; ?>"
									><?php
										// Check and state
										?><span class="trx_addons_theme_panel_plugins_list_item_status">
											<span class="trx_addons_theme_panel_plugins_list_item_check"></span>
											<span class="trx_addons_theme_panel_plugins_list_item_state"><?php
												if ($plugin_state == 'install') {
													// Unhovered text
													?><span class="trx_addons_theme_panel_plugins_list_item_state_label"><?php
														esc_html_e('Not installed', 'trx_addons');
													?></span><?php
													// Hovered text
													?><span class="trx_addons_theme_panel_plugins_list_item_state_label_selected"><?php
														esc_html_e('Install', 'trx_addons');
													?></span><?php
												} elseif ($plugin_state == 'activate') {
													// Unhovered text
													?><span class="trx_addons_theme_panel_plugins_list_item_state_label"><?php
														esc_html_e('Not activated', 'trx_addons');
													?></span><?php
													// Hovered text
													?><span class="trx_addons_theme_panel_plugins_list_item_state_label_selected"><?php
														esc_html_e('Activate', 'trx_addons');
													?></span><?php
												} else {
													// Unhovered text
													?><span class="trx_addons_theme_panel_plugins_list_item_state_label"><?php
														esc_html_e('Active', 'trx_addons');
													?></span><?php
												}
											?></span>
										</span><?php
										// Plugin's logo
										?><span class="trx_addons_theme_panel_plugins_list_item_image" style="background-image: url(<?php echo esc_url($plugin_image); ?>)"></span><?php
										// Plugin's title
										?><span class="trx_addons_theme_panel_plugins_list_item_title"><?php echo esc_html( $plugin_data['title'] ); ?></span>
									</a>
								</div><?php
							}
						}
					?></div>

					<?php do_action('trx_addons_action_theme_panel_after_list_items', $tab_id, $theme_info); ?>

					<div class="trx_addons_theme_panel_plugins_buttons">
						<a href="#" class="trx_addons_theme_panel_plugins_install trx_addons_button trx_addons_button_accent" disabled="disabled" data-need-reload="0"><?php
							esc_html_e('Install & Activate', 'trx_addons');
						?></a>
						<div class="trx_addons_percent_loader">
							<div class="trx_addons_percent_loader_bg"></div>
							<div class="trx_addons_percent_loader_value">0%</div>
						</div>						
					</div>
					
				</div>

				<?php
				do_action('trx_addons_action_theme_panel_after_section_data', $tab_id, $theme_info);

			} else {
				?>
				<div class="trx_addons_info_box trx_addons_info_box_warning"><p>
					<?php esc_html_e( 'Activate your theme in order to be able to install additional plugins.', 'trx_addons' ); ?>
				</p></div>
				<?php
			}
			
			do_action('trx_addons_action_theme_panel_section_end', $tab_id, $theme_info);
			?>
		</div>
		<?php
	}
}


// Display buttons after the section's data
if (!function_exists('trx_addons_theme_panel_after_section_data')) {
	add_action('trx_addons_action_theme_panel_after_section_data', 'trx_addons_theme_panel_after_section_data', 10, 2);
	function trx_addons_theme_panel_after_section_data($tab_id, $theme_info) {
		?>
		<div class="trx_addons_theme_panel_buttons">
			<a href="<?php
				if ( $tab_id == 'qsetup' )
					echo esc_url(admin_url());
				else
					echo '#';
			?>" class="trx_addons_theme_panel_next_step<?php if ( $tab_id == 'qsetup' ) { echo ' trx_addons_theme_panel_last_step trx_addons_button_accent'; } ?> trx_addons_button"><?php
				if ( $tab_id == 'qsetup' )
					esc_html_e('Finish', 'trx_addons');
				else
					esc_html_e('Skip Step', 'trx_addons');
			?></a>
			<a href="#" class="trx_addons_theme_panel_prev_step trx_addons_button"><?php
				esc_html_e('Go Back', 'trx_addons');
			?></a>
		</div>
		<?php
	}
}


// Correct theme-specific info
if (!function_exists('trx_addons_theme_panel_change_theme_info')) {
	add_filter('trx_addons_filter_get_theme_info', 'trx_addons_theme_panel_change_theme_info', 100);
	function trx_addons_theme_panel_change_theme_info($theme_info) {
		if ( ! empty($theme_info['theme_actions']['custom']['description']) ) {
			$theme_info['theme_actions']['custom']['description'] = __( 'You can order professional website customization. Experienced web studio will do it for you at a reasonable fee.', 'trx_addons' );
		}
		return $theme_info;
	}
}


// Import demo data
if (!function_exists('trx_addons_theme_panel_load_importer')) {
	add_action( 'after_setup_theme', 'trx_addons_theme_panel_load_importer' );
	function trx_addons_theme_panel_load_importer() {
		if (is_admin() && current_user_can('import') && file_exists(TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_IMPORTER . 'class.importer.php')) {
			require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_IMPORTER . 'class.importer.php';
			new trx_addons_demo_data_importer();
		}
	}
}

// Plugins installer
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_INSTALLER . 'installer.php';
