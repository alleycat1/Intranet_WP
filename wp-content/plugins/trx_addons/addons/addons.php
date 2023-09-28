<?php
/**
 * ThemeREX Addons Pluggable modules (Theme-specific addons)
 *
 * @package ThemeREX Addons
 * @since v1.82.0
 */


// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Load addons
if ( ! function_exists( 'trx_addons_load_addons' ) ) {
	add_action( 'after_setup_theme', 'trx_addons_load_addons', 2 );
	function trx_addons_load_addons() {
		static $loaded = false;
		if ( $loaded ) return;
		$loaded = true;
		global $TRX_ADDONS_STORAGE;
		$TRX_ADDONS_STORAGE['addons_list'] = apply_filters( 'trx_addons_addons_list', array() );
		$TRX_ADDONS_STORAGE['addons_required'] = false;
		$activated = get_option( 'trx_addons_activated_addons_list' );
		if ( empty( $activated ) ) {
			$activated = array();
		}
		if ( is_array( $TRX_ADDONS_STORAGE['addons_list'] ) && count( $TRX_ADDONS_STORAGE['addons_list'] ) > 0 ) {
			foreach ( $TRX_ADDONS_STORAGE['addons_list'] as $w => $params ) {
				if ( ! empty( $params['required'] ) ) {
					$TRX_ADDONS_STORAGE['addons_required'] = true;
					if ( ( $fdir = trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_ADDONS . trx_addons_esc( "{$w}/{$w}.php" ) ) ) != '') { 
						if ( ! empty( $activated[ $w ] ) || ! isset( $activated[ $w ] ) ) {
							include_once $fdir;
							$TRX_ADDONS_STORAGE['addons_list'][$w]['loaded'] = true;
							$activated[ $w ] = true;
						}
						$TRX_ADDONS_STORAGE['addons_list'][$w]['activated'] = $activated[ $w ];
					}
				}
			}
			update_option( 'trx_addons_activated_addons_list', $activated );
		}
	}
}


// Retrieve available addons from the upgrade-server
if ( ! function_exists( 'trx_addons_get_available_addons' ) ) {
	add_filter( 'trx_addons_addons_list', 'trx_addons_get_available_addons' );
	function trx_addons_get_available_addons( $addons = array() ) {
		$addons_file      = trx_addons_get_file_dir( 'addons/addons.json' );
		$addons_installed = json_decode( trx_addons_fgc( $addons_file ), true );
		$addons           = get_transient( 'trx_addons_list_addons' );
		if ( ! is_array( $addons ) || count( $addons ) == 0 ) {
			$addons_available = trx_addons_get_upgrade_data( array( 'action' => 'info_addons' ) );
			if ( empty( $addons_available['error'] ) && ! empty( $addons_available['data'] ) && $addons_available['data'][0] == '{' ) {
				$addons = json_decode( $addons_available['data'], true );
			}
			if ( ! is_array( $addons ) || count( $addons ) == 0 ) {
				$addons = $addons_installed;
			} else {
				$addons = apply_filters( 'trx_addons_addons_available', $addons );
			}
			set_transient( 'trx_addons_list_addons', $addons, 24 * 60 * 60 );       // Store to the cache for 24 hours
		}
		// Add installed addons to the list to allow forced activation addons by special parameter 'required' => true in the 'addons.json'
		if ( is_array( $addons_installed ) && count( $addons_installed ) > 0 ) {
			foreach( $addons_installed as $k => $v ) {
				if ( ! isset( $addons[ $k ] ) ) {
					$addons[ $k ] = $v;
				}
			}
		}
		// Check the state of each addon
		if ( is_array( $addons ) && count( $addons ) > 0 ) {
			foreach( $addons as $k => $v ) {
				if ( ! is_array( $v ) ) {
					unset( $addons[ $k ] );
				} else {
					$addons[ $k ][ 'installed' ] = ! empty( $addons_installed[ $k ][ 'version' ] )
														? $addons_installed[ $k ][ 'version' ]
														: '';
					if ( empty( $v[ 'description' ] ) && ! empty( $addons_installed[ $k ][ 'description' ] ) ) {
						$addons[ $k ][ 'description' ] = $addons_installed[ $k ][ 'description' ];
					}
					if ( ! isset( $v[ 'required' ] ) && ! empty( $addons_installed[ $k ][ 'required' ] ) ) {
						$addons[ $k ][ 'required' ] = true;
					}
				}
			}
		}
		return $addons;
	}
}



// Notice with info about new addons or new versions of installed addons
//------------------------------------------------------------------------

// Show admin notice
if ( ! function_exists( 'trx_addons_show_addons_admin_notice' ) ) {
	add_action('admin_notices', 'trx_addons_show_addons_admin_notice');
	function trx_addons_show_addons_admin_notice() {
		global $TRX_ADDONS_STORAGE;
		// Check if new addons is available
		if ( current_user_can( 'manage_options' ) && ! empty( $TRX_ADDONS_STORAGE['addons_required'] ) && trx_addons_is_theme_activated() ) {
			$download = 0;
			$update  = 0;
			foreach ( $TRX_ADDONS_STORAGE['addons_list'] as $addon => $data ) {
				if ( empty( $data['required'] ) ) continue;
				if ( ! empty( $data['installed'] ) || isset( $data['activated'] ) ) {
					if ( ! empty( $data['installed'] ) && version_compare( $data['installed'], $data['version'], '<' ) ) {
						$update++;
					}
				} else { 
					$download++;
				}
			}
			// Show notice
			$hide = get_transient( 'trx_addons_hide_notice_addons' );
			if ( $hide || $update + $download == 0 ) {
				return;
			}
			trx_addons_get_template_part( TRX_ADDONS_PLUGIN_ADDONS . 'addons-notice.php',
											'trx_addons_args_addons_notice',
											compact( 'update', 'download' )
										);
		}
	}
}

// Hide admin notice
if ( ! function_exists( 'trx_addons_callback_hide_addons_notice' ) ) {
	add_action('wp_ajax_trx_addons_hide_addons_notice', 'trx_addons_callback_hide_addons_notice');
	function trx_addons_callback_hide_addons_notice() {
		if ( wp_verify_nonce( trx_addons_get_value_gp( 'nonce' ), admin_url( 'admin-ajax.php' ) ) && current_user_can( 'manage_options' ) ) {
			set_transient( 'trx_addons_hide_notice_addons', true, 7 * 24 * 60 * 60 );	// 7 days
		}
		trx_addons_exit();
	}
}



// Add tab with addons to the 'Theme Panel'
//------------------------------------------------------

// Add step 'Addons'
if ( ! function_exists( 'trx_addons_theme_panel_steps_addons' ) ) {
	add_filter( 'trx_addons_filter_theme_panel_steps', 'trx_addons_theme_panel_steps_addons' );
	function trx_addons_theme_panel_steps_addons( $steps ) {
		global $TRX_ADDONS_STORAGE;
		if ( ! empty( $TRX_ADDONS_STORAGE['addons_required'] ) ) {
			$steps = trx_addons_array_merge( array( 'addons' => wp_kses_data( __( 'Download and Update addons.', 'trx_addons' ) ) ), $steps );
		}
		return $steps;
	}
}

// Add tab link 'Addons'
if ( ! function_exists( 'trx_addons_theme_panel_tabs_addons' ) ) {
	add_filter( 'trx_addons_filter_theme_panel_tabs', 'trx_addons_theme_panel_tabs_addons', 11 );
	function trx_addons_theme_panel_tabs_addons( $tabs ) {
		global $TRX_ADDONS_STORAGE;
		if ( ! empty( $TRX_ADDONS_STORAGE['addons_required'] ) ) {
			trx_addons_array_insert_after( $tabs, 'general', array( 'addons' => esc_html__( 'Addons', 'trx_addons' ) ) );
		}
		return $tabs;
	}
}

// Display 'Addons' section in the Theme Panel
if ( ! function_exists( 'trx_addons_theme_panel_section_addons' ) ) {
	add_action( 'trx_addons_action_theme_panel_section', 'trx_addons_theme_panel_section_addons', 10, 2);
	function trx_addons_theme_panel_section_addons( $tab_id, $theme_info ) {
		global $TRX_ADDONS_STORAGE;
		if ( 'addons' !== $tab_id || empty( $TRX_ADDONS_STORAGE['addons_required'] ) ) return;
		$addons_list = array();
		if ( trx_addons_is_theme_activated() ) {
			$addons_list = $TRX_ADDONS_STORAGE['addons_list'];
			ksort($addons_list);
		}
		?>
		<div id="trx_addons_theme_panel_section_<?php echo esc_attr($tab_id); ?>" class="trx_addons_tabs_section trx_addons_section_mode_<?php echo count( $addons_list ) > 4 ? 'list' : 'thumbs'; ?>">

			<?php
			do_action('trx_addons_action_theme_panel_section_start', $tab_id, $theme_info);

			if ( trx_addons_is_theme_activated() ) {
				?>
				<div class="trx_addons_theme_panel_section_content trx_addons_theme_panel_addons_selector">

					<?php do_action('trx_addons_action_theme_panel_before_section_title', $tab_id, $theme_info); ?>

					<span class="trx_addons_theme_panel_section_view_mode">
						<span class="trx_addons_theme_panel_section_view_mode_thumbs" data-mode="thumbs" title="<?php esc_attr_e( 'Large thumbnails', 'trx_addons' ); ?>"></span>
						<span class="trx_addons_theme_panel_section_view_mode_list" data-mode="list" title="<?php esc_attr_e( 'List with details', 'trx_addons' ); ?>"></span>
					</span>

					<h1 class="trx_addons_theme_panel_section_title">
						<?php esc_html_e( 'Download and Update addons', 'trx_addons' ); ?>
					</h1>

					<?php do_action('trx_addons_action_theme_panel_after_section_title', $tab_id, $theme_info); ?>

					<div class="trx_addons_theme_panel_section_description">
						<p><?php echo wp_kses_data( __( 'The list of addons required for the current theme. Feel free to "Deactivate" the addons not needed for your website, or use the "Download" option in case some addons are not installed. Otherwise, skip this step.', 'trx_addons' ) ); ?></p>
					</div>

					<?php do_action('trx_addons_action_theme_panel_before_list_items', $tab_id, $theme_info); ?>
					
					<div class="trx_addons_theme_panel_addons_list trx_addons_image_block_wrap">
						<?php
						foreach ( $addons_list as $addon => $data ) {
							if ( empty( $data['required'] ) ) continue;
							$addon = trx_addons_esc( $addon );
							$classes = array();
							if ( ! empty( $data['installed'] ) || isset( $data['activated'] ) ) {
								$classes[] = 'addon_installed';
								$classes[] = 'addon_' . ( ! empty( $data['activated'] ) ? 'activated' : 'deactivated' );
							} else {
								$classes[] = 'addon_required';
							}
							// 'trx_addons_image_block' is a inline-block element and spaces around it are not allowed
							?><div class="trx_addons_image_block <?php echo esc_attr( join( ' ', $classes ) ); ?>">
								<div class="trx_addons_image_block_inner" tabindex="0">
									<div class="trx_addons_image_block_image
									 	<?php 
										// Addon image
										$img = ! empty( $data['installed'] ) || isset( $data['activated'] )
												? trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . trx_addons_esc( "{$addon}/{$addon}.jpg" ) )
												: trx_addons_get_upgrade_domain_url() . trx_addons_esc( "addons/{$addon}/{$addon}.jpg" );
												//: trx_addons_get_no_image();
										if ( ! empty( $img ) ) {
											echo trx_addons_add_inline_css_class( 'background-image: url(' . esc_url( $img ) . ');' );
										}				 	
									 	?>">
								 	</div>
								 	<div class="trx_addons_image_block_footer">
										<?php
										if ( ! empty( $data['installed'] ) || isset( $data['activated'] ) ) {
											if ( ! empty( $data['installed'] ) && version_compare( $data['installed'], $data['version'], '<' ) ) {
												?>
												<a href="#"
													class="trx_addons_image_block_link trx_addons_image_block_link_update trx_addons_image_block_link_update_addon trx_addons_button trx_addons_button_small trx_addons_button_accent"
													data-addon="<?php echo esc_attr( $addon ); ?>">
														<?php
														// Translators: Add new version of the addon to the string
														echo esc_html( sprintf( __( 'Update to v.%s', 'trx_addons' ), $data['version'] ) );
														?>
												</a>
												<?php
											}
											if ( ! empty( $data['activated'] ) ) {
												?>
												<a href="#"
													class="trx_addons_image_block_link trx_addons_image_block_link_deactivate trx_addons_image_block_link_deactivate_addon trx_addons_button trx_addons_button_small trx_addons_button_fail"
													data-addon="<?php echo esc_attr( $addon ); ?>">
														<?php
														echo esc_html__( 'Deactivate', 'trx_addons' );
														?>
												</a>
												<?php
											} else {
												?>
												<a href="#"
													class="trx_addons_image_block_link trx_addons_image_block_link_activate trx_addons_image_block_link_activate_addon trx_addons_button trx_addons_button_small trx_addons_button_accent"
													data-addon="<?php echo esc_attr( $addon ); ?>">
														<?php
														echo esc_html__( 'Activate', 'trx_addons' );
														?>
												</a>
												<?php
											}

										} else {
											?>
											<a href="#" tabindex="0"
												class="trx_addons_image_block_link trx_addons_image_block_link_download trx_addons_image_block_link_download_addon trx_addons_button trx_addons_button_small"
												data-addon="<?php echo esc_attr( $addon ); ?>">
													<?php
													esc_html_e( 'Download', 'trx_addons' );
													?>
											</a>
											<?php
										}
										// Addon title
										if ( ! empty( $data['title'] ) ) {
											?>
											<h5 class="trx_addons_image_block_title">
												<?php
												// Translators: Add version of the addon to the string
												echo esc_html( $data['title'] )
													. ( ! empty( $data['installed'] )
														? ' ' . esc_html( sprintf( __( 'v.%s', 'trx_addons' ), $data['installed'] ) )
														: ''
														);
												?>
											</h5>
											<?php
										}
										// Addon description
										if ( ! empty( $data['description'] ) ) {
											?>
											<div class="trx_addons_image_block_description">
												<?php
												echo wp_kses( $data['description'], 'trx_addons_kses_content' );
												?>
											</div>
											<?php
										}
										?>
									</div>
								</div>
							</div><?php // No spaces allowed after this <div>, because it is an inline-block element
						}
						?>
					</div>

					<?php do_action('trx_addons_action_theme_panel_after_list_items', $tab_id, $theme_info); ?>

				</div>
				<?php
				do_action('trx_addons_action_theme_panel_after_section_data', $tab_id, $theme_info);
			} else {
				?>
				<div class="trx_addons_info_box trx_addons_info_box_warning"><p>
					<?php esc_html_e( 'Activate your theme in order to be able to download and update addons.', 'trx_addons' ); ?>
				</p></div>
				<?php
			}

			do_action('trx_addons_action_theme_panel_section_end', $tab_id, $theme_info);
			?>
		</div>
		<?php
	}
}


// Load page-specific scripts and styles
if ( ! function_exists( 'trx_addons_enqueue_scripts_addons' ) ) {
	add_action( 'admin_enqueue_scripts', 'trx_addons_enqueue_scripts_addons' );
	function trx_addons_enqueue_scripts_addons() {
		global $TRX_ADDONS_STORAGE;
		if ( ! empty( $TRX_ADDONS_STORAGE['addons_required'] ) ) {
			$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
			if ( ! empty( $screen->id ) && ( false !== strpos($screen->id, '_page_trx_addons_theme_panel') || in_array( $screen->id, array( 'update-core', 'update-core-network' ) ) ) ) {
				wp_enqueue_script( 'trx_addons-admin-addons', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'addons-admin.js' ), array( 'jquery' ), null, true );
			}
		}
	}
}

// Add page-specific vars to the localize array
if ( ! function_exists( 'trx_addons_localize_script_addons' ) ) {
	add_filter( 'trx_addons_filter_localize_script_admin', 'trx_addons_localize_script_addons' );
	function trx_addons_localize_script_addons( $arr ) {

		global $TRX_ADDONS_STORAGE;
		if ( ! empty( $TRX_ADDONS_STORAGE['addons_required'] ) ) {

			// Download a new addon
			$arr['msg_download_addon_caption']         = esc_html__( "Download addon", 'trx_addons' );
			$arr['msg_download_addon']                 = apply_filters( 'trx_addons_filter_msg_download_addon',
				'<p>'
				. esc_html__( "The new addon will be installed in the 'addons' folder inside ThemeREX Addons plugin.", 'trx_addons' )
				. '</p>'
			);
			$arr['msg_download_addon_success']         = esc_html__( 'A new addon is installed. The page will be reloaded.', 'trx_addons' );
			$arr['msg_download_addon_success_caption'] = esc_html__( 'Addon is installed!', 'trx_addons' );
			$arr['msg_download_addon_error_caption']   = esc_html__( 'Addon download error!', 'trx_addons' );

			// Update an installed addon
			$arr['msg_update_addon_caption']         = esc_html__( "Update addon", 'trx_addons' );
			$arr['msg_update_addon']                 = apply_filters( 'trx_addons_filter_msg_update_addon',
				'<p>'
				. esc_html__( "Attention! The new version of the addon will be installed in the same folder instead the current version!", 'trx_addons' )
				. '</p>'
			);
			$arr['msg_update_addon_success']         = esc_html__( 'The addon is updated. The page will be reloaded.', 'trx_addons' );
			$arr['msg_update_addon_success_caption'] = esc_html__( 'Addon is updated!', 'trx_addons' );
			$arr['msg_update_addon_error_caption']   = esc_html__( 'Addon update error!', 'trx_addons' );
			$arr['msg_update_addons_success']        = esc_html__( 'Selected addons are updated.', 'trx_addons' );
			$arr['msg_update_addons_error']          = esc_html__( 'Selected addons are not updated.', 'trx_addons' );
			$arr['msg_update_addons_warning']        = esc_html__( 'Some addons are not updated.', 'trx_addons' );

			// Deactivate an installed addon
			$arr['msg_deactivate_addon_success']         = esc_html__( 'The addon is deactivated. The page will be reloaded.', 'trx_addons' );
			$arr['msg_deactivate_addon_success_caption'] = esc_html__( 'Deactivate addon', 'trx_addons' );
			$arr['msg_deactivate_addon_error_caption']   = esc_html__( 'Addon is not deactivated!', 'trx_addons' );

			// Activate an installed addon
			$arr['msg_activate_addon_success']         = esc_html__( 'The addon is activated. The page will be reloaded.', 'trx_addons' );
			$arr['msg_activate_addon_success_caption'] = esc_html__( 'Activate addon', 'trx_addons' );
			$arr['msg_activate_addon_error_caption']   = esc_html__( 'Addon is not activated!', 'trx_addons' );
		}

		return $arr;
	}
}


// AJAX handler for the 'trx_addons_download_addon' action
if ( ! function_exists( 'trx_addons_ajax_download_addon' ) ) {
	add_action( 'wp_ajax_trx_addons_download_addon', 'trx_addons_ajax_download_addons' );
	add_action( 'wp_ajax_trx_addons_update_addon', 'trx_addons_ajax_download_addons' );
	function trx_addons_ajax_download_addons() {
		global $TRX_ADDONS_STORAGE;
		
		$response = array( 'error' => '' );

		if ( ! empty( $TRX_ADDONS_STORAGE['addons_required'] ) ) {

			trx_addons_verify_nonce();

			if ( ! current_user_can( 'manage_options' ) ) {
				$response['error'] = esc_html__( 'Sorry, you are not allowed to download/update addons.', 'trx_addons' );

			} else {
				$action = current_action() == 'wp_ajax_trx_addons_download_addon' ? 'download' : 'update';
				$key    = trx_addons_get_theme_activation_code();
				$addon  = trx_addons_get_value_gp( 'addon' );

				if ( empty( $key ) ) {
					$response['error'] = esc_html__( 'Theme is not activated!', 'trx_addons' );

				} else if ( empty( $addon ) || ! isset( $TRX_ADDONS_STORAGE['addons_list'][ $addon ] ) || empty( $TRX_ADDONS_STORAGE['addons_list'][ $addon ]['required'] ) ) {
					// Translators: Add the addon's name to the message
					$response['error'] = sprintf( esc_html__( 'Can not download the addon "%s"', 'trx_addons' ), $addon );

				} else if ( ! empty( $TRX_ADDONS_STORAGE['addons_list'][ $addon ]['installed'] ) && 'update' != $action ) {
					// Translators: Add the addon's name to the message
					$response['error'] = sprintf( esc_html__( 'Addon %s is already installed', 'trx_addons' ), $addon );

				} else {
					$result = trx_addons_get_upgrade_data( array(
						'action' => 'download_addon',
						'addon'  => $addon,
						'key'    => $key,
					) );
					if ( isset( $result['error'] ) && isset( $result['data'] ) ) {
						if ( substr( $result['data'], 0, 2 ) == 'PK' ) {
							$tmp_name = 'tmp-' . rand() . '.zip';
							$tmp      = wp_upload_bits( $tmp_name, null, $result['data'] );
							if ( $tmp['error'] ) {
								$response['error'] = esc_html__( 'Problem with save upgrade file to the folder with uploads', 'trx_addons' );
							} else {
								$response['error'] .= trx_addons_download_addon( $addon, $tmp['file'] );
							}
						} else {
							$response['error'] = ! empty( $result['error'] )
															? $result['error']
															: esc_html__( 'Package with upgrade is corrupt', 'trx_addons' );
						}
					} else {
						$response['error'] = esc_html__( 'Incorrect server answer', 'trx_addons' );
					}
				}
			}
		} else {
			$response['error'] = esc_html__( 'Current theme is not require additional addons.', 'trx_addons' );
		}

		trx_addons_ajax_response( $response );
	}
}


// Unpack and download addon
if ( ! function_exists( 'trx_addons_download_addon' ) ) {
	function trx_addons_download_addon( $addon, $file ) {
		global $TRX_ADDONS_STORAGE;
		if ( file_exists( $file ) ) {
			ob_start();
			// Unpack addon
			$dest = trx_addons_get_folder_dir( '/addons' );
			if ( ! empty( $dest ) ) {
				trx_addons_unzip_file( $file, $dest );
			}
			// Remove uploaded archive
			unlink( $file );
			$log = ob_get_contents();
			ob_end_clean();
			// Update addons list
			$addons_file      = trx_addons_get_file_dir( 'addons/addons.json' );
			$addons_installed = json_decode( trx_addons_fgc( $addons_file ), true );
			$addons_available = $TRX_ADDONS_STORAGE['addons_list'];
			if ( isset( $addons_available[ $addon ][ 'installed' ] ) )	unset( $addons_available[ $addon ][ 'installed' ] );
			if ( isset( $addons_available[ $addon ][ 'loaded' ] ) )		unset( $addons_available[ $addon ][ 'loaded' ] );
			if ( isset( $addons_available[ $addon ][ 'required' ] ) )	unset( $addons_available[ $addon ][ 'required' ] );
			$addons_installed[ $addon ] = $addons_available[ $addon ];
			trx_addons_fpc( $addons_file, json_encode( $addons_installed, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_FORCE_OBJECT ) );
			// Remove stored list to reload it while next site visit occurs
			delete_transient( 'trx_addons_list_addons' );
			// Set this flag to regenerate styles and scripts on next run
			update_option('trx_addons_action', 'trx_addons_action_save_options');
		} else {
			return esc_html__( 'Uploaded file with addon package is not available', 'trx_addons' );
		}
	}
}


// AJAX handler for the 'trx_addons_deactivate_addon' action
if ( ! function_exists( 'trx_addons_ajax_deactivate_addon' ) ) {
	add_action( 'wp_ajax_trx_addons_deactivate_addon', 'trx_addons_ajax_deactivate_addon' );
	add_action( 'wp_ajax_trx_addons_activate_addon', 'trx_addons_ajax_deactivate_addon' );
	function trx_addons_ajax_deactivate_addon() {
		global $TRX_ADDONS_STORAGE;
		
		$response = array( 'error' => '' );

		if ( ! empty( $TRX_ADDONS_STORAGE['addons_required'] ) ) {

			trx_addons_verify_nonce();

			if ( ! current_user_can( 'manage_options' ) ) {
				$response['error'] = esc_html__( 'Sorry, you are not allowed to activate/deactivate addons.', 'trx_addons' );

			} else {
				$action = current_action() == 'wp_ajax_trx_addons_deactivate_addon' ? 'deactivate' : 'activate';
				$addon  = trx_addons_get_value_gp( 'addon' );

				if ( empty( $addon ) || ! isset( $TRX_ADDONS_STORAGE['addons_list'][ $addon ] ) || empty( $TRX_ADDONS_STORAGE['addons_list'][ $addon ]['required'] ) ) {
					// Translators: Add the addon's name to the message
					$response['error'] = sprintf( esc_html__( 'Can not process the addon "%s"', 'trx_addons' ), $addon );

				} else if ( ! empty( $TRX_ADDONS_STORAGE['addons_list'][ $addon ]['activated'] ) && 'activate' == $action ) {
					// Translators: Add the addon's name to the message
					$response['error'] = sprintf( esc_html__( 'Addon %s is already activated', 'trx_addons' ), $addon );

				} else if ( empty( $TRX_ADDONS_STORAGE['addons_list'][ $addon ]['activated'] ) && 'deactivate' == $action ) {
					// Translators: Add the addon's name to the message
					$response['error'] = sprintf( esc_html__( 'Addon %s is already deactivated', 'trx_addons' ), $addon );

				} else {

					$activated = get_option( 'trx_addons_activated_addons_list' );
					if ( empty( $activated ) ) {
						$activated = array();
					}
					$activated[ $addon ] = 'activate' == $action;
					update_option( 'trx_addons_activated_addons_list', $activated );
				}
			}
		} else {
			$response['error'] = esc_html__( 'Current theme is not require addons.', 'trx_addons' );
		}

		trx_addons_ajax_response( $response );
	}
}



//-------------------------------------------------------
//-- Update addons via WordPress update screen
//-------------------------------------------------------

// Add new addons versions to the WordPress update screen
if ( ! function_exists( 'trx_addons_update_list_addons' ) ) {
	add_action( 'core_upgrade_preamble', 'trx_addons_update_list_addons' );
	function trx_addons_update_list_addons() {
		global $TRX_ADDONS_STORAGE;
		if ( current_user_can( 'update_themes' ) && ! empty( $TRX_ADDONS_STORAGE['addons_required'] ) && trx_addons_is_theme_activated() ) {
			$update = 0;
			foreach ( $TRX_ADDONS_STORAGE['addons_list'] as $addon => $data ) {
				if ( empty( $data['required'] ) ) continue;
				if ( ! empty( $data['installed'] ) && version_compare( $data['installed'], $data['version'], '<' ) ) {
					$update++;
				}
			}
			?>
			<h2>
				<?php esc_html_e( 'Active theme components: Addons', 'trx_addons' ); ?>
			</h2>
			<?php
			if ( $update == 0 ) {
				?>
				<p><?php esc_html_e( 'Addons are all up to date.', 'trx_addons' ); ?></p>
				<?php
				return;
			}
			?>
			<p>
				<?php esc_html_e( 'The following addons have new versions available. Check the ones you want to update and then click &#8220;Update Addons&#8221;.', 'trx_addons' ); ?>
			</p>
			<div class="upgrade trx_addons_upgrade_addons">
				<p><input id="upgrade-addons" class="button trx_addons_upgrade_addons_button" type="button" value="<?php esc_attr_e( 'Update Addons', 'trx_addons' ); ?>" /></p>
				<table class="widefat updates-table" id="update-addons-table">
					<thead>
					<tr>
						<td class="manage-column check-column"><input type="checkbox" id="addons-select-all" /></td>
						<td class="manage-column"><label for="addons-select-all"><?php esc_html_e( 'Select All', 'trx_addons' ); ?></label></td>
					</tr>
					</thead>
					<tbody class="plugins">
						<?php
						foreach ( $TRX_ADDONS_STORAGE['addons_list'] as $addon => $data ) {
							if ( empty( $data['required'] ) ) {
								continue;
							}
							if ( empty( $data['installed'] ) || ! version_compare( $data['installed'], $data['version'], '<' ) ) {
								continue;
							}
							$checkbox_id = 'checkbox_' . md5( $addon );
							?>
							<tr>
								<td class="check-column">
									<input type="checkbox" name="checked[]" id="<?php echo esc_attr( $checkbox_id ); ?>" value="<?php echo esc_attr( $addon ); ?>" />
									<label for="<?php echo esc_attr( $checkbox_id ); ?>" class="screen-reader-text">
										<?php
										// Translators: %s: Addon name
										printf( esc_html__( 'Select %s', 'trx_addons' ), $data['title'] );
										?>
									</label>
								</td>
								<td class="plugin-title"><p>
									<img src="<?php echo esc_url( trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . trx_addons_esc( "{$addon}/{$addon}.jpg" ) ) ); ?>" width="85" class="updates-table-screenshot" alt="<?php echo esc_attr( $data['title'] ); ?>" />
									<strong><?php echo esc_html( $data['title'] ); ?></strong>
									<?php
									// Translators: 1: addon version, 2: new version
									printf(
										esc_html__( 'You have version %1$s installed. Update to %2$s.', 'trx_addons' ),
										$data['installed'],
										$data['version']
									);
									?>
								</p></td>
							</tr>
							<?php
						}
						?>
					</tbody>
					<tfoot>
						<tr>
							<td class="manage-column check-column"><input type="checkbox" id="addons-select-all-2" /></td>
							<td class="manage-column"><label for="addons-select-all-2"><?php esc_html_e( 'Select All', 'trx_addons' ); ?></label></td>
						</tr>
					</tfoot>
				</table>
				<p><input id="upgrade-addons-2" class="button trx_addons_upgrade_addons_button" type="button" value="<?php esc_attr_e( 'Update Addons', 'trx_addons' ); ?>" /></p>
			</div>
			<?php
		}
	}
}


// Add new addons count to the WordPress updates count
if ( ! function_exists( 'trx_addons_update_counts_addons' ) ) {
	add_filter('wp_get_update_data', 'trx_addons_update_counts_addons', 10, 2);
	function trx_addons_update_counts_addons($update_data, $titles) {
		global $TRX_ADDONS_STORAGE;
		if ( current_user_can( 'update_themes' ) && ! empty( $TRX_ADDONS_STORAGE['addons_required'] ) && trx_addons_is_theme_activated() ) {
			$update = 0;
			foreach ( $TRX_ADDONS_STORAGE['addons_list'] as $addon => $data ) {
				if ( empty( $data['required'] ) ) continue;
				if ( ! empty( $data['installed'] ) && version_compare( $data['installed'], $data['version'], '<' ) ) {
					$update++;
				}
			}
			if ( $update > 0 ) {
				$update_data[ 'counts' ][ 'addons' ] = $update;
				$update_data[ 'counts' ][ 'total' ] += $update;
				// Translators: %d: number of updates available to installed addons
				$titles['addons']                    = sprintf( _n( '%d Addon Update', '%d Addon Updates', $update, 'trx_addons' ), $update );
				$update_data[ 'title' ]              = esc_attr( implode( ', ', $titles ) );
			}
		}
		return $update_data;
	}
}
