<?php
/**
 * Add widget to the WordPress Dashboard
 *
 * @package ThemeREX Addons
 * @since v1.6.44
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Define component's subfolder
if ( !defined('TRX_ADDONS_PLUGIN_DASHBOARD_WIDGET') ) 			define('TRX_ADDONS_PLUGIN_DASHBOARD_WIDGET', TRX_ADDONS_PLUGIN_COMPONENTS . 'dashboard-widget/');

// Refresh interval (in hours)
if ( !defined('TRX_ADDONS_PLUGIN_DASHBOARD_WIDGET_REFRESH') )	define('TRX_ADDONS_PLUGIN_DASHBOARD_WIDGET_REFRESH', 6);


// Add component to the global list
if (!function_exists('trx_addons_dashboard_widget_add_to_components')) {
	add_filter( 'trx_addons_components_list', 'trx_addons_dashboard_widget_add_to_components' );
	function trx_addons_dashboard_widget_add_to_components($list=array()) {
		$list['dashboard_widget'] = array(
					'title' => __('WP Dashboard widget', 'trx_addons')
					);
		return $list;
	}
}



//-------------------------------------------------------
//--  Dashboard widget
//-------------------------------------------------------
	
// Register dashboard widget
if ( !function_exists( 'trx_addons_dashboard_widget_register' ) ) {
	add_action( 'wp_dashboard_setup', 'trx_addons_dashboard_widget_register' );
	function trx_addons_dashboard_widget_register() {
		if ( trx_addons_components_is_allowed('components', 'dashboard_widget') ) {
			// Register widget
			// Translators: Add theme name
			wp_add_dashboard_widget( 'trx_addons-dashboard-widget', sprintf(__( '%s Overview', 'trx_addons' ), wp_get_theme()->get( 'Name' ) ), 'trx_addons_dashboard_widget_display' );
			// Move our widget to top.
			global $wp_meta_boxes;
			$dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
			$ours = array(
				'trx_addons-dashboard-widget' => $dashboard['trx_addons-dashboard-widget'],
			);
			$wp_meta_boxes['dashboard']['normal']['core'] = array_merge( $ours, $dashboard );
		}
	}
}


// Displays dashboard widget
if ( !function_exists( 'trx_addons_dashboard_widget_display' ) ) {
	function trx_addons_dashboard_widget_display() {
		// Get theme info & feed
		$theme_info = apply_filters('trx_addons_filter_get_theme_feed', trx_addons_get_theme_info());
		?><div class="trx_addons_dashboard_widget"><?php

			// Header
			?><div class="trx_addons_dashboard_widget_header">
				<h6 class="trx_addons_dashboard_widget_title"><?php
					if (!empty($theme_info['theme_page_url'])) {
						?><a href="<?php echo esc_url($theme_info['theme_page_url']); ?>" class="button trx_addons_dashboard_widget_title_button"><i class="dashicons dashicons-nametag"></i> <?php
							esc_html_e('Theme Dashboard', 'trx_addons');
						?></a><?php
					}
					?><span class="trx_addons_dashboard_widget_title_text"><?php
						// Translators: Add theme name and version
						echo esc_html(sprintf(__('Welcome to %1$s v.%2$s', 'trx_addons'),
							$theme_info['theme_name'],
							$theme_info['theme_version']
						));
					?></span>
				</h6>
			</div><?php

			// Top5 most visited pages
			$top = apply_filters( 'trx_addons_dashboard_widget_top_visited_pages', 5 );
			$visits = trx_addons_statistics_get_top_visited( apply_filters( 'trx_addons_dashboard_widget_top_visited_pages', $top ) );
			if ( is_array( $visits ) && count( $visits ) > 0 ) {
				?><div class="trx_addons_dashboard_widget_section trx_addons_dashboard_widget_section_most_visited">
					<h5 class="trx_addons_dashboard_widget_section_title"><?php
						echo esc_html( sprintf( __( 'Top %d most visited pages', 'trx_addons' ), $top ) );
					?></h5>
					<ul class="trx_addons_dashboard_widget_section_list">
						<li class="trx_addons_dashboard_widget_section_item trx_addons_dashboard_widget_section_item_status_info">
							<?php foreach( $visits as $data ) { ?>
								<h3 class="trx_addons_dashboard_widget_section_item_header">
									<a href="<?php echo esc_url( trim( get_home_url(), '/' ) . $data['url'] ); ?>" target="_blank" class="trx_addons_dashboard_widget_section_item_title">
										<span class="trx_addons_dashboard_widget_section_item_header_text"><?php echo esc_html( $data['title'] ); ?></span>
										<span class="trx_addons_dashboard_widget_section_item_header_data"><?php echo esc_html( trx_addons_num2kilo( $data['count'], 1 ) ) . ' (' . esc_html( $data['percent'] ) . '%)'; ?></span>
									</a>
								</h3>
							<?php } ?>
						</li>
					</ul>
				</div><?php
			}

			// News feed
			if ( is_array( $theme_info['theme_feed'] ) && count( $theme_info['theme_feed'] ) > 0 ) {
				foreach( $theme_info['theme_feed'] as $section => $data ) {
					?><div class="trx_addons_dashboard_widget_section">
						<h5 class="trx_addons_dashboard_widget_section_title"><?php
							echo esc_html( $data['title'] );
						?></h5>
						<ul class="trx_addons_dashboard_widget_section_list"><?php
							foreach( $data['items'] as $post ) {
								?><li class="trx_addons_dashboard_widget_section_item<?php
									if ( ! empty( $post['status'] ) && $post['status'] != 'none' ) {
										echo ' trx_addons_dashboard_widget_section_item_status_' . esc_attr($post['status']);
									}
									if ( ! empty( $post['label'] ) && $post['label'] != 'none' ) {
										echo ' trx_addons_dashboard_widget_section_item_label_' . esc_attr($post['label']);
									}
								?>"><?php
									if ( ! empty($post['date']) || ! empty($post['title'] ) ) {
										?><h3 class="trx_addons_dashboard_widget_section_item_header"><?php
											if ( ! empty( $post['date'] ) ) {
												?><span class="trx_addons_dashboard_widget_section_item_date"><?php echo esc_html($post['date']); ?></span><?php
											}
											if ( ! empty( $post['title'] ) ) {
												if ( ! empty( $post['link'] ) ) {
													?><a href="<?php echo esc_url($post['link']); ?>" target="_blank" class="trx_addons_dashboard_widget_section_item_title"><?php echo esc_html($post['title']); ?></a><?php
												} else {
													?><span class="trx_addons_dashboard_widget_section_item_title"><?php echo esc_html($post['title']); ?></span><?php
												}
											}
										?></h3><?php
									}
									?><div class="trx_addons_dashboard_widget_section_item_description"><?php echo wp_kses($post['description'], 'trx_addons_kses_content'); ?></div>
								</li><?php
							}
						?></ul>
					</div><?php
				}
			}

			// Footer
			if ( is_array($theme_info['theme_actions']) && count($theme_info['theme_actions']) > 0) {
				?><ul class="trx_addons_dashboard_widget_section_footer"><?php
					foreach ($theme_info['theme_actions'] as $id => $action ) {
						if ( empty( $action['link_text'] ) ) {
							continue;
						}
						?><li class="trx_addons_dashboard_widget_section_footer_item trx_addons_dashboard_widget_section_footer_item_<?php echo esc_attr( $id ); ?>">
							<a href="<?php echo esc_attr( $action['link'] ); ?>" target="_blank"><?php
								echo esc_html( $action['link_text'] ); ?>
								<span class="screen-reader-text"><?php esc_html_e( '(opens in a new window)', 'trx_addons' ); ?></span>
								<span aria-hidden="true" class="dashicons dashicons-external"></span>
							</a>
						</li><?php
					}
				?></ul><?php
			}
		?></div><?php
	}
}


// Get theme-specific feed
if ( !function_exists( 'trx_addons_get_theme_feed' ) ) {
	add_filter( 'trx_addons_filter_get_theme_feed', 'trx_addons_get_theme_feed' );
	function trx_addons_get_theme_feed($theme_info) {
		$data = get_transient("trx_addons_dashboard_feed");
		if ( ! $data ) {
			//$user = wp_get_current_user();
			// Detect active skin and version
			$skin_slug = '';
			$skin_version = '';
			$skins_path = trailingslashit( get_template_directory() ) . 'skins';
			if ( is_dir( $skins_path ) ) {
				$skin_slug = get_option(
								sprintf( 'theme_skin_%s', get_stylesheet() ),
								is_dir( $skins_path . '/default' ) ? 'default' : ''
							);
				$skins_file = $skins_path . '/skins.json';
				if ( ! empty( $skin_slug ) && file_exists( $skins_file ) ) {
					$skins_installed = json_decode( trx_addons_fgc( $skins_file ), true );
					if ( ! empty( $skins_installed[ $skin_slug ]['version'] ) ) {
						$skin_version = $skins_installed[ $skin_slug ]['version'];
					}
				}
			}
			$response = wp_remote_post( trx_addons_get_protocol() . '://themerex.net/wp-json/trx_feed/v1/get/data', array(
				'body' => apply_filters( 'trx_addons_filter_get_theme_feed_info',
								array_merge(
									$theme_info,
									array(
										'theme_code' => trx_addons_get_theme_activation_code(),
										'skin_slug' => $skin_slug,
										'skin_version' => $skin_version,
										'site_url' => home_url( '/' ),
										'site_name' => get_bloginfo( 'site_name' ),
										//'site_admin' => get_option( 'admin_email' ),
										//'site_user' => ! empty( $user->data->user_email ) ? $user->data->user_email : '',
										//'site_user_name' => ! empty( $user->data->display_name ) ? $user->data->display_name : '',
									)
								)
							),
				'headers' => array( 'accept' => 'application/json' ),
				'blocking' => true,
				'sslverify' => false,
				//'method' => 'POST',
				//'timeout' => 45,
				//'redirection' => 5,
				//'httpversion' => '1.0',
				//'cookies' => array()
	    		)
			);
			if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) == 200 ) {
				$data = json_decode( wp_remote_retrieve_body( $response ), true );
				// Replace macros inside item's title, link and description
				// and store data to the cache
				if ( is_array( $data ) ) {
					foreach( $data as $section => $content ) {
						if ( ! empty( $content['title'] ) ) {
							$data[$section]['title'] = trx_addons_feed_prepare_macros( $content['title'], $theme_info );
						}
						if ( is_array( $content['items'] ) ) {
							foreach( $content['items'] as $k => $v ) {
								if ( ! empty( $v['link'] ) )        $data[$section]['items'][$k]['link']        = trx_addons_feed_prepare_macros($v['link'], $theme_info);
								if ( ! empty( $v['title'] ) )       $data[$section]['items'][$k]['title']       = trx_addons_feed_prepare_macros($v['title'], $theme_info);
								if ( ! empty( $v['description'] ) ) $data[$section]['items'][$k]['description'] = nl2br(trx_addons_feed_prepare_macros($v['description'], $theme_info));
							}
						}
					}
					set_transient("trx_addons_dashboard_feed", $data, TRX_ADDONS_PLUGIN_DASHBOARD_WIDGET_REFRESH*60*60);
				}
			}
		}
		if ( is_array( $data ) ) {
			// Leave single random FAQ item
			if (!empty($data['faq']['items']) && is_array($data['faq']['items']) && ($total = count($data['faq']['items'])) > 1) {
				$data['faq']['items'] = array($data['faq']['items'][mt_rand(0, $total - 1)]);
			}
			$theme_info['theme_feed'] = $data;
		}
		return $theme_info;
	}
}


// Replace macros '{theme_name}', '{user_name}', '{theme_page}', etc.
if ( !function_exists( 'trx_addons_feed_prepare_macros' ) ) {
	function trx_addons_feed_prepare_macros($str, $theme_info) {
		return str_replace(
							array(
								'{theme_page}',
								'{theme_name}',
								'{theme_version}',
								'{theme_doc}',
								'{theme_support}',
								'{user_name}',
								'{site_name}',
								'{site_url}',
							),
							array(
								! empty( $theme_info['theme_page_url'] ) ? $theme_info['theme_page_url'] : '',
								! empty( $theme_info['theme_name'] ) ? $theme_info['theme_name'] : '',
								! empty( $theme_info['theme_version'] ) ? $theme_info['theme_version'] : '',
								! empty( $theme_info['theme_actions']['doc']['link'] ) ? $theme_info['theme_actions']['doc']['link'] : '',
								! empty( $theme_info['theme_actions']['support']['link'] ) ? $theme_info['theme_actions']['support']['link'] : '',
								! empty( $theme_info['site_user_name'] ) ? $theme_info['site_user_name'] : '',
								! empty( $theme_info['site_name'] ) ? $theme_info['site_name'] : '',
								! empty( $theme_info['site_url'] ) ? $theme_info['site_url'] : '',
							),
							$str
						);
	}
}
