<?php
/**
 * Plugin support: Contact Form 7
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


if ( ! function_exists( 'trx_addons_exists_cf7' ) ) {
	/**
	 * Check if plugin 'Contact Form 7' is installed and activated
	 * 
	 * @return bool  True if plugin is installed and activated
	 */
	function trx_addons_exists_cf7() {
		return class_exists( 'WPCF7' ) && class_exists( 'WPCF7_ContactForm' );
	}
}

if ( ! function_exists( 'trx_addons_cf7_load_scripts_front' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_cf7_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_cf7_load_scripts_front', 10, 1 );
	/**
	 * Enqueue custom styles and scripts for the frontend
	 * 
	 * @hooked wp_enqueue_scripts
	 * @hooked trx_addons_action_pagebuilder_preview_scripts
	 * 
	 * @param bool $force  Load scripts forcibly
	 */
	function trx_addons_cf7_load_scripts_front( $force = false ) {
		if ( ! trx_addons_exists_cf7() ) {
			return;
		}
		trx_addons_enqueue_optimized( 'cf7', $force, array(
			'css'  => array(
				'trx_addons-cf7' => array( 'src' => TRX_ADDONS_PLUGIN_API . 'contact-form-7/contact-form-7.css' ),
			),
			'js' => array(
				'trx_addons-cf7' => array( 'src' => TRX_ADDONS_PLUGIN_API . 'contact-form-7/contact-form-7.js', 'deps' => 'jquery' ),
			),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'contact-form-7' ),
				//array( 'type' => 'gb',  'sc' => 'wp:trx-addons/events' ),	// This sc is not exists for GB
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_contact_form_7"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[contact-form-7' ),
			)
		) );
	}
}

if ( ! function_exists( 'trx_addons_cf7_merge_styles' ) ) {
	add_filter( "trx_addons_filter_merge_styles", 'trx_addons_cf7_merge_styles' );
	/**
	 * Merge custom styles to the single stylesheet to increase page upload speed
	 * 
	 * @hooked trx_addons_filter_merge_styles
	 *
	 * @param array $list  List of styles to merge
	 * 
	 * @return array       Modified list of styles to merge
	 */
	function trx_addons_cf7_merge_styles( $list ) {
		if ( trx_addons_exists_cf7() ) {
			$list[ TRX_ADDONS_PLUGIN_API . 'contact-form-7/contact-form-7.css' ] = false;
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_cf7_merge_scripts' ) ) {
	add_action( "trx_addons_filter_merge_scripts", 'trx_addons_cf7_merge_scripts' );
	/**
	 * Merge custom scripts to the single file to increase page upload speed
	 * 
	 * @hooked trx_addons_filter_merge_scripts
	 *
	 * @param array $list  List of scripts to merge
	 * 
	 * @return array       Modified list of scripts to merge
	 */
	function trx_addons_cf7_merge_scripts( $list ) {
		if ( trx_addons_exists_cf7() ) {
			$list[ TRX_ADDONS_PLUGIN_API . 'contact-form-7/contact-form-7.js' ] = false;
		}
		return $list;
	}
}


if ( ! function_exists( 'trx_addons_cf7_check_in_html_output' ) ) {
//	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_cf7_check_in_html_output', 10, 1 );
//	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_cf7_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_cf7_check_in_html_output', 10, 1 );
	/**
	 * Check if CF7's shortcodes is present in the current page content and force loading CF7 scripts and styles
	 * 
	 * @hooked trx_addons_action_check_page_content
	 *
	 * @param string $content  The text to check
	 * 
	 * @return string          Checked text
	 */
	function trx_addons_cf7_check_in_html_output( $content = '' ) {
		if ( ! trx_addons_exists_cf7() ) {
			return $content;
		}
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*wpcf7-form',
				'class=[\'"][^\'"]*type\\-wpcf7_contact_form',
			)
		);
		if ( trx_addons_check_in_html_output( 'cf7', $content, $args ) ) {
			trx_addons_cf7_load_scripts_front( true );
		}
		return $content;
	}
}

if ( ! function_exists( 'trx_addons_cf7_filter_head_output' ) ) {
	add_filter( 'trx_addons_filter_page_head', 'trx_addons_cf7_filter_head_output', 10, 1 );
	/**
	 * Remove plugin-specific styles and scripts from the page head on the frontend if it is not used on the current page
	 * and optimize CSS and JS loading is 'full' in the ThemeREX Addons Options
	 * 
	 * @hooked trx_addons_filter_page_head
	 *
	 * @param string $content  The text to filter
	 * 
	 * @return string          Filtered text
	 */
	function trx_addons_cf7_filter_head_output( $content = '' ) {
		if ( ! trx_addons_exists_cf7() ) {
			return $content;
		}
		return trx_addons_filter_head_output( 'cf7', $content, array(
			'check' => array(
				'#<link[^>]*href=[\'"][^\'"]*/contact-form-7/[^>]*>#'
			)
		) );
	}
}

if ( ! function_exists( 'trx_addons_cf7_filter_body_output' ) ) {
	add_filter( 'trx_addons_filter_page_content', 'trx_addons_cf7_filter_body_output', 10, 1 );
	/**
	 * Remove plugin-specific styles and scripts from the page body on the frontend if it is not used on the current page
	 * and optimize CSS and JS loading is 'full' in the ThemeREX Addons Options
	 * 
	 * @hooked trx_addons_filter_page_content
	 *
	 * @param string $content  The text to filter
	 * 
	 * @return string          Filtered text
	 */
	function trx_addons_cf7_filter_body_output( $content = '' ) {
		if ( ! trx_addons_exists_cf7() ) {
			return $content;
		}
		return trx_addons_filter_body_output( 'cf7', $content, array(
			'check' => array(
				'#<link[^>]*href=[\'"][^\'"]*/contact-form-7/[^>]*>#',
				'#<script[^>]*src=[\'"][^\'"]*/contact-form-7/[^>]*>[\\s\\S]*</script>#U',
				'#<script[^>]*id=[\'"]contact-form-7-[^>]*>[\\s\\S]*</script>#U'
			)
		) );
	}
}

if ( ! function_exists( 'trx_addons_get_list_cf7' ) ) {
	/**
	 * Return list of CF7 forms, prepended inherit (if need)
	 * 
	 * @param bool $prepend_inherit  Prepend inherit to the list
	 * 
	 * @return array                 List of forms or empty array
	 */
	function trx_addons_get_list_cf7( $prepend_inherit = false ) {
		static $list = false;
		if ( $list === false ) {
			$list = array();
			if ( trx_addons_exists_cf7() ) {
				// Attention! Using WP_Query is damage 'post_type' in the main query
				global $wpdb;
				$rows = $wpdb->get_results( 'SELECT id, post_title'
												. ' FROM ' . esc_sql( $wpdb->prefix . 'posts' ) 
												. ' WHERE post_type="' . esc_sql( WPCF7_ContactForm::post_type ) . '"'
														. ' AND post_status' . ( current_user_can( 'read_private_pages' ) && current_user_can( 'read_private_posts' ) ? ' IN ("publish", "private")' : '="publish"' )
														. ' AND post_password=""'
												. ' ORDER BY post_title' );
				if ( is_array( $rows ) && count( $rows ) > 0 ) {
					foreach ( $rows as $row ) {
						$list[ $row->id ] = $row->post_title;
					}
				}
			}
		}
		return $prepend_inherit ? trx_addons_array_merge( array( 'inherit' => esc_html__( "Inherit", 'trx_addons' ) ), $list ) : $list;
	}
}


if ( ! function_exists( 'trx_addons_wpcf7_in_post_details_in_popup' ) ) {
	add_filter( 'trx_addons_filter_post_details_in_popup',	'trx_addons_wpcf7_in_post_details_in_popup', 10, 2 );
	/**
	 * Add styles and scripts to the AJAX response with a post details (for services and dishes in the popup)
	 * if a CF7 form is present in the content
	 * 
	 * @hooked trx_addons_filter_post_details_in_popup
	 *
	 * @param array $response  AJAX response
	 * @param string $type     Post type
	 * 
	 * @return array           Modified AJAX response: add styles and scripts to the 'css', 'js' and 'code' keys
	 */
	function trx_addons_wpcf7_in_post_details_in_popup( $response, $type ) {
		if ( ! empty( $response['data'] ) && strpos( $response['data'], 'wpcf7-form' ) !== false ) {
			// Add styles
			//--------------------------------
			if ( ! isset( $response['css'] ) ) {
				$response['css'] = array();
			}
			// Add CF7 styles
			if ( function_exists( 'wpcf7_plugin_url' ) ) {
				$response['css']['contact-form-7'] = array( 'url' => wpcf7_plugin_url( 'includes/css/styles.css' ) );
				if ( function_exists( 'wpcf7_is_rtl' ) && wpcf7_is_rtl() ) {
					$response['css']['contact-form-7-rtl'] = array( 'url' => wpcf7_plugin_url( 'includes/css/styles-rtl.css' ) );
				}
			}
			// Add our styles
			$response['css']['trx_addons-cf7'] = trx_addons_get_file_url(TRX_ADDONS_PLUGIN_API . 'contact-form-7/contact-form-7.css');

			// Add scripts
			//--------------------------------
			if ( ! isset( $response['js'] ) ) {
				$response['js'] = array();
			}
			// Add CF7 scripts
			if ( function_exists( 'wpcf7_plugin_url' ) ) {
				$wpcf7 = array(
							'api' => array(
								'root' => esc_url_raw( get_rest_url() ),
								'namespace' => 'contact-form-7/v1',
							)
						);
				if ( defined( 'WP_CACHE' ) and WP_CACHE ) {
					$wpcf7['cached'] = 1;
				}
				$response['js']['contact-form-7-extra'] = array(
					'code' => 'var wpcf7 = ' . json_encode( $wpcf7 ) . ';'
				);
				$response['js']['contact-form-7'] = array(
					'url' => wpcf7_plugin_url( 'includes/js/index.js' )
				);
			}
			// Add out scripts
			$response['js']['trx_addons-cf7'] = trx_addons_get_file_url(TRX_ADDONS_PLUGIN_API . 'contact-form-7/contact-form-7.js');
		}
		return $response;
	}
}

if ( ! function_exists( 'trx_addons_cpt_properties_wpcf7_mail_components' ) ) {
	add_filter( 'wpcf7_mail_components', 'trx_addons_cpt_properties_wpcf7_mail_components', 10, 3 );
	/**
	 * Filter 'wpcf7_mail_components' before Contact Form 7 send mail to replace recipient for 'Cars' and 'Properties'.
	 * Also customer can use the '{{ title }}' in the 'Subject' and 'Message' to replace it with the post title when send a mail
	 *
	 * @param array $components  Mail components
	 * @param object $form       WPCF7_ContactForm object
	 * @param object $mail_obj	 WPCF7_Mail object
	 * 
	 * @return array             Modified mail components
	 */
	function trx_addons_cpt_properties_wpcf7_mail_components( $components, $form, $mail_obj = null ) {
		if ( is_object( $form ) && method_exists( $form, 'id' ) && (int)$form->id() > 0 ) {
			$recipient_mail = '';
			if ( class_exists( 'WPCF7_Submission' ) ) {
				$mail_2 = $form->prop('mail_2');
				if ( is_array( $mail_2 ) && ! empty( $mail_2['recipient'] ) ) {
					$mail_2_recipient = wpcf7_sanitize_unit_tag( $mail_2['recipient'] );
					$submission = WPCF7_Submission::get_instance();
					$recipient_mail = $submission->get_posted_string( $mail_2_recipient );
				}
			}
			$data = get_transient( sprintf( 'trx_addons_cf7_%d_data', (int)$form->id() ) );
			if ( ( empty( $recipient_mail ) || $recipient_mail != $components['recipient'] ) && ! empty( $data['agent'] ) ) {
				$agent_id = (int)$data['agent'];
				$agent_email = '';
				if ( $agent_id > 0 ) {				// Agent
					$meta = (array)get_post_meta( $agent_id, 'trx_addons_options', true );
					$agent_email = $meta['email'];
				} else if ( $agent_id < 0 ) {		// Author
					$user_id = abs( $agent_id );
					$user_data = get_userdata( $user_id );
					$agent_email = $user_data->user_email;
				}
				if ( ! empty( $agent_email ) ) {
					$components['recipient'] = $agent_email;
				}
			}
			if ( ! empty( $data['item'] ) && (int)$data['item'] > 0 ) {
				$post = get_post( $data['item'] );
				foreach( array( 'subject', 'body' ) as $k ) {
					$components[ $k ] = str_replace(
													array(
														'{{ title }}'
													),
													array(
														$post->post_title
													),
													$components[ $k ]
												);
				}
			}
		}
		return $components;
	}
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_cf7() && trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'contact-form-7/contact-form-7-sc-elementor.php';
}


// Demo data install
//----------------------------------------------------------------------------

// One-click import support
if ( is_admin() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'contact-form-7/contact-form-7-demo-importer.php';
}

// OCDI support
if ( is_admin() && trx_addons_exists_cf7() && function_exists( 'trx_addons_exists_ocdi' ) && trx_addons_exists_ocdi() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'contact-form-7/contact-form-7-demo-ocdi.php';
}
