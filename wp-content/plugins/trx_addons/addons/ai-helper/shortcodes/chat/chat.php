<?php
/**
 * Shortcode: AI Chat
 *
 * @package ThemeREX Addons
 * @since v2.22.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

use TrxAddons\AiHelper\OpenAi;


// Load required styles and scripts for the frontend
if ( ! function_exists( 'trx_addons_sc_chat_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_sc_chat_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_sc_chat_load_scripts_front', 10, 1 );
	function trx_addons_sc_chat_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'sc_chat', $force, array(
			'css'  => array(
				'trx_addons-sc_chat' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat/chat.css' ),
			),
			'js' => array(
				'trx_addons-sc_chat' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat/chat.js', 'deps' => 'jquery' ),
			),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_sc_chat' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/chat' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_chat"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_sc_chat' ),
			)
		) );
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'trx_addons_sc_chat_load_scripts_front_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_sc_chat_load_scripts_front_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	add_action( 'trx_addons_action_load_scripts_front_sc_chat', 'trx_addons_sc_chat_load_scripts_front_responsive', 10, 1 );
	function trx_addons_sc_chat_load_scripts_front_responsive( $force = false  ) {
		trx_addons_enqueue_optimized_responsive( 'sc_chat', $force, array(
			'css'  => array(
				'trx_addons-sc_chat-responsive' => array(
					'src' => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat/chat.responsive.css',
					'media' => 'sm'
				),
			),
		) );
	}
}

// Merge shortcode's specific styles to the single stylesheet
if ( ! function_exists( 'trx_addons_sc_chat_merge_styles' ) ) {
	add_filter( "trx_addons_filter_merge_styles", 'trx_addons_sc_chat_merge_styles' );
	function trx_addons_sc_chat_merge_styles( $list ) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat/chat.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( ! function_exists( 'trx_addons_sc_chat_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_sc_chat_merge_styles_responsive' );
	function trx_addons_sc_chat_merge_styles_responsive( $list ) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat/chat.responsive.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific scripts into single file
if ( ! function_exists( 'trx_addons_sc_chat_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_sc_chat_merge_scripts');
	function trx_addons_sc_chat_merge_scripts($list) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat/chat.js' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu
if ( ! function_exists( 'trx_addons_sc_chat_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_sc_chat_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_sc_chat_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_sc_chat_check_in_html_output', 10, 1 );
	function trx_addons_sc_chat_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*sc_chat'
			)
		);
		if ( trx_addons_check_in_html_output( 'sc_chat', $content, $args ) ) {
			trx_addons_sc_chat_load_scripts_front( true );
		}
		return $content;
	}
}


// trx_sc_chat
//-------------------------------------------------------------
/*
[trx_sc_chat id="unique_id" prompt="prompt text for ai" command="blog-post"]
*/
if ( ! function_exists( 'trx_addons_sc_chat' ) ) {
	function trx_addons_sc_chat( $atts, $content = null ) {	
		$atts = trx_addons_sc_prepare_atts( 'trx_sc_chat', $atts, trx_addons_sc_common_atts( 'id,title', array(
			// Individual params
			"type" => "default",
			"prompt" => "",
			"button_text" => "",
			"premium" => 0,
			"show_limits" => 0,
		) ) );

		// Load shortcode-specific scripts and styles
		trx_addons_sc_chat_load_scripts_front( true );

		// Load template
		$output = '';

		ob_start();
		trx_addons_get_template_part( array(
										TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat/tpl.' . trx_addons_esc( $atts['type'] ) . '.php',
										TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat/tpl.default.php'
										),
										'trx_addons_args_sc_chat',
										$atts
									);
		$output = ob_get_contents();
		ob_end_clean();
		return apply_filters( 'trx_addons_sc_output', $output, 'trx_sc_chat', $atts, $content );
	}
}

// Add shortcode [trx_sc_chat]
if ( ! function_exists( 'trx_addons_sc_chat_add_shortcode' ) ) {
	add_action( 'init', 'trx_addons_sc_chat_add_shortcode', 20 );
	function trx_addons_sc_chat_add_shortcode() {
		add_shortcode( "trx_sc_chat", "trx_addons_sc_chat" );
	}
}

// Prepare a data for a requests statistics
if ( ! function_exists( 'trx_addons_sc_chat_prepare_total_generated' ) ) {
	function trx_addons_sc_chat_prepare_total_generated( $data ) {
		if ( ! is_array( $data ) ) {
			$data = array(
				'per_hour' => array_fill( 0, 24, 0 ),
				'per_day' => 0,
				'per_week' => 0,
				'per_month' => 0,
				'per_year' => 0,
				'date' => date( 'Y-m-d' ),
				'week' => date( 'W' ),
				'month' => date( 'm' ),
				'year' => date( 'Y' ),
			);
		}
		if ( $data['date'] != date( 'Y-m-d' ) ) {
			$data['per_hour'] = array_fill( 0, 24, 0 );
			$data['per_day'] = 0;
			$data['date'] = date( 'Y-m-d' );
		}
		if ( ! isset( $data['week'] ) || $data['week'] != date( 'W' ) ) {
			$data['per_week'] = 0;
			$data['week'] = date( 'W' );
		}
		if ( ! isset( $data['month'] ) || $data['month'] != date( 'm' ) ) {
			$data['per_month'] = 0;
			$data['month'] = date( 'm' );
		}
		if ( ! isset( $data['year'] ) || $data['year'] != date( 'Y' ) ) {
			$data['per_year'] = 0;
			$data['year'] = date( 'Y' );
		}
		return $data;
	}
}

// Add number of requests to the total number
if ( ! function_exists( 'trx_addons_sc_chat_set_total_generated' ) ) {
	function trx_addons_sc_chat_set_total_generated( $number, $suffix = '', $user_id = 0 ) {
		$data = trx_addons_sc_chat_prepare_total_generated( $user_id > 0 && ! empty( $suffix )
					? get_user_meta( $user_id, 'trx_addons_sc_chat_total', true )
					: get_transient( "trx_addons_sc_chat_total{$suffix}" )
				);
		$hour = (int) date( 'H' );
		$data['per_hour'][ $hour ] += $number;
		$data['per_day'] += $number;
		$data['per_week'] += $number;
		$data['per_month'] += $number;
		$data['per_year'] += $number;
		if ( $user_id > 0 ) {
			update_user_meta( $user_id, 'trx_addons_sc_chat_total', $data );
		} else {
			set_transient( "trx_addons_sc_chat_total{$suffix}", $data, 24 * 60 * 60 );
		}
	}
}

// Get number of requests
if ( ! function_exists( 'trx_addons_sc_chat_get_total_generated' ) ) {
	function trx_addons_sc_chat_get_total_generated( $per = 'hour', $suffix = '', $user_id = 0 ) {
		$data = trx_addons_sc_chat_prepare_total_generated( $user_id > 0 && ! empty( $suffix )
					? get_user_meta( $user_id, 'trx_addons_sc_chat_total', true )
					: get_transient( "trx_addons_sc_chat_total{$suffix}" )
				);
		if ( $per == 'hour' ) {
			$hour = (int) date( 'H' );
			return $data['per_hour'][ $hour ];
		} else if ( $per == 'day' ) {
			return $data['per_day'];
		} else if ( $per == 'week' ) {
			return $data['per_week'];
		} else if ( $per == 'month' ) {
			return $data['per_month'];
		} else if ( $per == 'year' ) {
			return $data['per_year'];
		} else if ( $per == 'all' ) {
			return $data;
		} else {
			return 0;
		}
	}
}

// Log a visitor ip address to the json file
if ( ! function_exists( 'trx_addons_sc_chat_log_to_json' ) ) {
	function trx_addons_sc_chat_log_to_json( $number ) {
		$ip = ! empty( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 'Unknown';
		$date = date( 'Y-m-d' );
		$time = date( 'H:i:s' );
		$hour = date( 'H' );
		$json = trx_addons_fgc( TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat/chat.log' );
		if ( empty( $json ) ) $json = '[]';
		$ips = json_decode( $json, true );
		if ( ! is_array( $ips ) ) {
			$ips = array();
		}
		if ( empty( $ips[ $date ] ) ) {
			$ips[ $date ] = array( 'total' => 0, 'ip' => array(), 'hour' => array() );
		}
		// Log total
		$ips[ $date ]['total'] += $number;
		// Log by IP
		if ( empty( $ips[ $date ]['ip'][ $ip ] ) ) {
			$ips[ $date ]['ip'][ $ip ] = array();
		}
		if ( empty( $ips[ $date ]['ip'][ $ip ][ $time ] ) ) {
			$ips[ $date ]['ip'][ $ip ][ $time ] = 0;
		}
		$ips[ $date ]['ip'][ $ip ][ $time ] += $number;
		// Log by hour
		if ( empty( $ips[ $date ]['hour'][ $hour ] ) ) {
			$ips[ $date ]['hour'][ $hour ] = array();
		}
		if ( empty( $ips[ $date ]['hour'][ $hour ][ $time ] ) ) {
			$ips[ $date ]['hour'][ $hour ][ $time ] = 0;
		}
		$ips[ $date ]['hour'][ $hour ][ $time ] += $number;
		trx_addons_fpc( TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat/chat.log', json_encode( $ips, JSON_PRETTY_PRINT ) );
	}
}

// Callback function to generate text from the shortcode AJAX request
if ( ! function_exists( 'trx_addons_sc_chat_generate_text' ) ) {
	add_action( 'wp_ajax_nopriv_trx_addons_ai_helper_chat', 'trx_addons_sc_chat_generate_text' );
	add_action( 'wp_ajax_trx_addons_ai_helper_chat', 'trx_addons_sc_chat_generate_text' );
	function trx_addons_sc_chat_generate_text() {

		trx_addons_verify_nonce();

		$count = (int)trx_addons_get_value_gp( 'count' );
		$chat = json_decode( trx_addons_get_value_gp( 'chat' ), true );

		$settings = trx_addons_decode_settings( trx_addons_get_value_gp( 'settings' ) );
		$number = 1;	// Number of requests to increment the total number of generated texts

		$premium = ! empty( $settings['premium'] ) && (int)$settings['premium'] == 1;
		$suffix = $premium ? '_premium' : '';

		$params = compact( 'chat', 'count' );
	
		$answer = array(
			'error' => '',
			'data' => array(
				'text' => '',
				'message' => ''
			)
		);

		if ( is_array( $chat ) && count( $chat ) > 0 ) {

			$limits = (int)trx_addons_get_option( "ai_helper_sc_chat_limits{$suffix}" ) > 0;
			$limit_per_request = 0;
			$lph = $lpv = $lpu = false;
			$used_limits = '';
			$generated = 0;
			$user_id = 0;

			if ( $limits ) {
				$user_level = '';
				$user_limit = false;
				if ( $premium ) {
					$user_id = get_current_user_id();
					$user_level = apply_filters( 'trx_addons_filter_sc_chat_user_level', $user_id > 0 ? 'default' : '', $user_id );
					if ( ! empty( $user_level ) ) {
						$levels = trx_addons_get_option( "ai_helper_sc_chat_levels_premium" );
						$level_idx = trx_addons_array_search( $levels, 'level', $user_level );
						$user_limit = $level_idx !== false ? $levels[ $level_idx ] : false;
						if ( isset( $user_limit['limit'] ) && trim( $user_limit['limit'] ) !== '' ) {
							$generated = trx_addons_sc_chat_get_total_generated( $user_limit['per'], $suffix, $user_id );
							if ( (int)$user_limit['limit'] - $generated > 0 && (int)$user_limit['limit'] - $generated < $number ) {
								$number = $answer['data']['number'] = (int)$user_limit['limit'] - $generated;
							}
							$lpu = (int)$user_limit['limit'] < $generated + $number;
							$used_limits = 'user';
						}
					}
				}
				if ( ! $premium || empty( $user_level ) || ! isset( $user_limit['limit'] ) || trim( $user_limit['limit'] ) === '' ) {
					$generated = trx_addons_sc_chat_get_total_generated( 'hour', $suffix );
					$lph = (int)trx_addons_get_option( "ai_helper_sc_chat_limit_per_hour{$suffix}" ) < $generated + $number;
					$lpv = (int)trx_addons_get_option( "ai_helper_sc_chat_limit_per_visitor{$suffix}" ) < $count;
					$used_limits = 'visitor';
				}
				$limit_per_request = (int)trx_addons_get_option( "ai_helper_sc_chat_limit_per_request{$suffix}" );
			}
	
			$demo = $count == 0 || $lpu || $lph || $lpv;

			if ( OpenAi::instance()->get_api_key() != '' && ! $demo ) {

				// Log a visitor ip address to the json file
				//trx_addons_sc_chat_log_to_json( 1 );	// Save to the log a number of requests or tokens number (use $limit_per_request as an argument)?

				// Save a first prompt from the chat to show it in the shortcode "Chat topics"
				if ( count( $chat ) == 1 ) {
					trx_addons_sc_chat_save_topic( $chat[0]['content'] );
				}

				// Add a system message to the chat
				array_unshift( $chat, array(
					'role' => 'system',
					'content' => apply_filters( 'trx_addons_filter_sc_chat_system_prompt', __( 'Format the response with HTML tags.', 'trx_addons' ) )
				) );
		
				// Call the OpenAI API
				$api = OpenAi::instance();
				$response = $api->chat(
					array(
						'messages' => $chat,
						'n' => 1,
						'max_tokens' => $limit_per_request,
						'temperature' => max( 0, min( 2, (float)trx_addons_get_option( 'ai_helper_sc_chat_temperature' ) ) ),
					),
					$params
				);

				if ( ! empty( $response['choices'][0]['message']['content'] ) ) {
					if ( preg_match( '#<body>([\s\S]*)</body>#U', $response['choices'][0]['message']['content'], $matches ) ) {
						$answer['data']['text'] = wpautop( $matches[1] );
					} else {
						$answer['data']['text'] = preg_match( '/<(br|p|ol|ul|dl|h1|h2|h3|h4|h5|h6)[^>]*>/i', $response['choices'][0]['message']['content'], $matches )
													? wpautop( $response['choices'][0]['message']['content'] )
													: nl2br( str_replace( "\n\n", "\n", $response['choices'][0]['message']['content'] ) );
					}
				} else {
					if ( ! empty( $response['error']['message'] ) ) {
						$answer['error'] = $response['error']['message'];
					} else if ( ! empty( $response['error'] ) && is_string( $response['error'] ) ) {
						$answer['error'] = $response['error'];
					} else {
						$answer['error'] = __( 'Error! Unknown response from the OpenAI API.', 'trx_addons' );
					}
				}

				trx_addons_sc_chat_set_total_generated( $number, $suffix, $used_limits == 'user' ? $user_id : 0 );

			} else {
				if ( OpenAi::instance()->get_api_key() != '' ) {
					$msg = trx_addons_get_option( "ai_helper_sc_chat_limit_alert{$suffix}" );
					$answer['error'] = ! empty( $msg )
													? $msg
													: apply_filters( "trx_addons_filter_sc_chat_limit_alert{$suffix}",
														'<h5 data-lp="' . ( $lpu ? 'lpu' . $generated : ( $lph ? 'lph' . $generated : ( $lpv ? 'lpv' : '' ) ) ) . '">' . __( 'Limits are reached!', 'trx_addons' ) . '</h5>'
														. '<p>' . __( 'The limit of the number of tokens that can be generated per hour has been reached.', 'trx_addons' ) . '</p>'
														. '<p>' . __( ' Please try again later.', 'trx_addons' ) . '</p>'
														);
				} else {
					$answer['error'] = __( 'Error! OpenAI API key is not specified.', 'trx_addons' );
				}
			}
		} else {
			$answer['error'] = __( 'Error! The prompt is empty.', 'trx_addons' );
		}

		// Return response to the AJAX handler
		trx_addons_ajax_response( apply_filters( 'trx_addons_filter_sc_chat_answer', $answer ) );
	}
}

// Save a new topic from the chat to the list to show it in the shortcode "Chat topics"
if ( ! function_exists( 'trx_addons_sc_chat_save_topic' ) ) {
	function trx_addons_sc_chat_save_topic( $topic ) {
		$max_topics = apply_filters( 'trx_addons_filter_sc_chat_topics_max', 20 );
		$topics = get_option( 'trx_addons_sc_chat_topics' );
		if ( ! is_array( $topics ) ) {
			$topics = array();
		}
		$exists = false;
		for ( $i = 0; $i < count( $topics ); $i++ ) {
			if ( $topics[ $i ]['topic'] == $topic ) {
				$exists = true;
				break;
			}
		}
		if ( ! $exists ) {
			array_unshift( $topics, array( 'topic' => $topic ) );
			if ( count( $topics ) > $max_topics ) {
				array_pop( $topics );
			}
			update_option( 'trx_addons_sc_chat_topics', $topics );
		}
	}
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat/chat-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat/chat-sc-gutenberg.php';
}
