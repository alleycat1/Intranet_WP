<?php
/**
 * Plugins installer helper
 *
 * @package ThemeREX Addons
 * @since v1.6.48
 */

// Return url for install or activate plugin (by slug)
if ( ! function_exists( 'trx_addons_plugins_installer_get_link' ) ) {
	function trx_addons_plugins_installer_get_link( $slug, $state ) {
		$nonce = '';
		if ( ! empty( $slug ) ) {
			if ( $state == 'install' ) {
				if ( class_exists( 'TGM_Plugin_Activation' ) ) {
					$instance = call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) );
					$nonce    = wp_nonce_url(
						add_query_arg(
							array(
								'plugin'        => urlencode( $slug ),
								'tgmpa-install' => 'install-plugin',
							),
							$instance->get_tgmpa_url()
						),
						'tgmpa-install',
						'tgmpa-nonce'
					);
				} else {
					$nonce = wp_nonce_url(
						add_query_arg(
							array(
								'action' => 'install-plugin',
								'from'   => 'import',
								'plugin' => urlencode( $slug ),
							),
							network_admin_url( 'update.php' )
						),
						'install-plugin_' . trim( $slug )
					);
				}
			} elseif ( $state == 'activate' ) {
				if ( class_exists( 'TGM_Plugin_Activation' ) ) {
					$instance = call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) );
					$nonce    = wp_nonce_url(
						add_query_arg(
							array(
								'plugin'         => urlencode( $slug ),
								'tgmpa-activate' => 'activate-plugin',
							),
							$instance->get_tgmpa_url()
						),
						'tgmpa-activate',
						'tgmpa-nonce'
					);
				} else {
					$plugin_link = $slug . '/' . $slug . '.php';
					$nonce       = add_query_arg(
						array(
							'action'        => 'activate',
							'plugin'        => rawurlencode( $plugin_link ),
							'plugin_status' => 'all',
							'paged'         => '1',
							'_wpnonce'      => wp_create_nonce( 'activate-plugin_' . $plugin_link ),
						),
						network_admin_url( 'plugins.php' )
					);
				}
			}
		}
		return $nonce;
	}
}

// Return button (link) to install/activate plugin
if ( ! function_exists( 'trx_addons_plugins_installer_get_button_html' ) ) {
	function trx_addons_plugins_installer_get_button_html( $slug, $show = true ) {
		$output = '';
		if ( ! empty( $slug ) ) {
			$state = trx_addons_plugins_installer_check_plugin_state( $slug );
			$nonce = trx_addons_plugins_installer_get_link( $slug, $state );
			if ( !empty( $nonce ) ) {
				$output .= '<a class="trx_addons_about_block_link trx_addons_plugins_installer_link trx_addons_button trx_addons_button_accent ' . esc_attr( $state ) . '-now"'
								. ' href="' . esc_url( $nonce ) . '"'
								. ' data-slug="' . esc_attr( $slug ) . '"'
								. ' data-name="' . esc_attr( $slug ) . '"'
								. ' data-processing="' . ( $state == 'install' 
																? esc_attr__( 'Installing ...', 'trx_addons' ) 
																: esc_attr__( 'Activating ...', 'trx_addons' )
															)
														. '"'
								. ' aria-label="' . ( $state == 'install' 
																// Translators: Add the plugin's slug to the 'aria-label'
																? esc_attr( sprintf( __( 'Install %s', 'trx_addons' ), $slug ) ) 
																// Translators: Add the plugin's slug to the 'aria-label'
																: esc_attr( sprintf( __( 'Activate %s', 'trx_addons' ), $slug ) )
															)
														. '"'
							. '>'
								. ( $state == 'install' 
																? esc_html__( 'Install', 'trx_addons' )
																: esc_html__( 'Activate', 'trx_addons' )
									)
							. '</a>';
			}
		}
		if ( $show ) {
			trx_addons_show_layout( $output );
		}
		return $output;
	}
}

// Return plugin's state
if ( ! function_exists( 'trx_addons_plugins_installer_check_plugin_state' ) ) {
	function trx_addons_plugins_installer_check_plugin_state( $slug ) {
		$state = 'install';
		if ( is_dir( ABSPATH . 'wp-content/plugins/' . $slug . '/' ) ) {
			$state = 'activate';
			$plugins = get_option( 'active_plugins', array() );
			if ( is_multisite() ) {
				$mu_plugins = get_site_option( 'active_sitewide_plugins');
				if ( is_array( $mu_plugins ) ) {
					$plugins = array_merge( $plugins, array_keys( $mu_plugins ) );
				}
			}
			if (is_array($plugins)) {
				foreach($plugins as $p) {
					if (strpos($p, $slug . '/') !== false) {
						$state = 'deactivate';
						break;
					}
				}
			}
		}
		return $state;
	}
}

// Check plugin's state
if ( ! function_exists( 'trx_addons_plugins_installer_check_plugin_state_ajax_callback' ) ) {
	add_action( 'wp_ajax_trx_addons_check_plugin_state', 'trx_addons_plugins_installer_check_plugin_state_ajax_callback' );
	function trx_addons_plugins_installer_check_plugin_state_ajax_callback() {
		trx_addons_verify_nonce();
		$response = array(
			'error' => '',
			'state'  => '',
		);
		$slug = trx_addons_get_value_gp( 'slug' );
		if (empty($slug)) {
			$response['error'] = __('Slug is empty', 'trx_addons');
		} else {
			$response['state'] = trx_addons_plugins_installer_check_plugin_state($slug);
		}
		trx_addons_ajax_response( $response );
	}
}

// Enqueue scripts
if ( ! function_exists( 'trx_addons_plugins_installer_enqueue_scripts' ) ) {
	function trx_addons_plugins_installer_enqueue_scripts() {
		wp_enqueue_script( 'plugin-install' );
		wp_enqueue_script( 'updates' );
		wp_enqueue_script( 'trx_addons-plugins-installer', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_INSTALLER . 'installer.js' ), array( 'jquery' ), null, true );
	}
}
