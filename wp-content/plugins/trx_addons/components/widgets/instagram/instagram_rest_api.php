<?php
/**
 * Instagram support: REST API callbacks
 *
 * @package ThemeREX Addons
 * @since v1.6.47
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

define( 'TRX_ADDONS_WIDGET_INSTAGRAM_CACHE_TIME', 12 * 60 * 60 );

// Get recent photos
if ( ! function_exists( 'trx_addons_widget_instagram_get_recent_photos' ) ) {
	function trx_addons_widget_instagram_get_recent_photos( $args ) {
		// Check photos in the cache
		$client_id  = ! empty( $args['demo'] ) ? 'demo' : trx_addons_widget_instagram_get_client_id();
		$cache_data = sprintf( 'trx_addons_instagram_data_%1$s_%2$s',
								$client_id,
								! empty( $args['demo'] ) ? 'demo' : $args['hashtag']
							);
		$data = ! empty( $args['demo'] ) ? false : get_transient($cache_data);

		// If no photos or saved photos less then need - request its from Instagram and put to the cache for 4 hours
		if ( ! is_array($data) || ( ! empty($data['data']) && is_array($data['data']) && count($data['data']) < $args['count'] ) ) {

			// Demo mode - show images from internal folder
			if ( ! empty( $args['demo'] ) ) {

				$data = array(
					'data' => trx_addons_widget_instagram_demo_images( $args )
				);

			// Real mode - get images from Instagram
			} else {

				$access_token = trx_addons_get_option('api_instagram_access_token');

				// Get Instagram photos via Facebook API with sandbox application
				if ( ! empty( $access_token ) && empty( $args['hashtag'] ) ) {
					$access_token_valid = get_transient('trx_addons_instagram_access_token_valid');
					if ( ! $access_token_valid ) {
						$access_token = trx_addons_widget_instagram_refresh_access_token( $access_token );
					}
					$count = max(1, $args['count']);
 					// Get items from Instagram
 					// Example: https://graph.instagram.com/me/media
 					//					?fields=id,media_type,media_url,caption,timestamp,thumbnail_url,permalink,
 					//							children{fields=id,media_url,thumbnail_url,permalink}
 					//					&limit=50 (default is 25)
 					//					&access_token=$accessToken
 					$resp = trx_addons_remote_get( 'https://graph.instagram.com/me/media'
														. '?fields=id,media_type,media_url,thumbnail_url,caption,username,timestamp'
														. "&access_token={$access_token}"
													);
					if ( substr($resp, 0, 1) == '{' ) {
						$data = array(
							'data' => trx_addons_widget_instagram_parse_page_output( $resp, $args )
						);
					}

				// Get Instagram photos via direct GET (parse html output)
				} else {
					// If parameter 'hashtag' not start with '#' - use it as user name
					if ( empty( $args['username'] ) && ! empty( $args['hashtag'] ) && $args['hashtag'][0] != '#' ) {
						$args['username'] = $args['hashtag'];
						$args['hashtag']  = '';
					}
					$data = array(
						'data' => trx_addons_widget_instagram_get_page_output( $args )
					);
				}
			}

			// Save data to the cache
			if ( is_array( $data ) && is_array( $data['data'] ) && count( $data['data'] ) > 0 ) {
				set_transient( $cache_data, $data, TRX_ADDONS_WIDGET_INSTAGRAM_CACHE_TIME );
			}
		}

		return $data;
	}
}


//------------------------------------------------
//--  REST API support
//------------------------------------------------

// Register endpoints
if ( ! function_exists( 'trx_addons_widget_instagram_rest_register_endpoints' ) ) {
	add_action( 'rest_api_init', 'trx_addons_widget_instagram_rest_register_endpoints');
	function trx_addons_widget_instagram_rest_register_endpoints() {
		// Get access token from Instagram
		register_rest_route( 'trx_addons/v1', '/widget_instagram/get_access', array(
			'methods' => 'GET,POST',
			'callback' => 'trx_addons_widget_instagram_rest_get_access',
			'permission_callback' => '__return_true'
			));
	}
}


// Return redirect url for Instagram API
if ( ! function_exists( 'trx_addons_widget_instagram_rest_get_redirect_url' ) ) {
	function trx_addons_widget_instagram_rest_get_redirect_url() {
		$client_id  = trx_addons_get_option('api_instagram_client_id');
		$return_url = trx_addons_widget_instagram_rest_get_return_url();
		return ! empty( $client_id )
					? $return_url
					: "https://cb.themerex.net/instagram/";
	}
}


// Return return url for Instagram API
if ( ! function_exists( 'trx_addons_widget_instagram_rest_get_return_url' ) ) {
	function trx_addons_widget_instagram_rest_get_return_url() {
		return trailingslashit( home_url() ) . "wp-json/trx_addons/v1/widget_instagram/get_access/";
	}
}

// Callback: Get authorization code from Instagram
if ( ! function_exists( 'trx_addons_widget_instagram_rest_get_access' ) && class_exists( 'WP_REST_Request' ) ) {
	function trx_addons_widget_instagram_rest_get_access( WP_REST_Request $request ) {

		// Get response code
		$params = $request->get_params();
		$nonce = get_transient('trx_addons_instagram_nonce');

		if ( empty($params['error']) && ! empty($params['state']) && ! empty( $nonce ) && $params['state'] == $nonce ) {
			
			$code = !empty($params['code']) ? $params['code'] : '';
			$access_token = !empty($params['access_token']) ? $params['access_token'] : '';
			$access_token_valid_time = 2 * 24 * 60 * 60;
			$user_id = !empty($params['user_id']) ? $params['user_id'] : '';
			
			// Receive authorization code - request for access token
			if ( empty( $access_token ) && ! empty( $code ) ) {
				$client_id = trx_addons_widget_instagram_get_client_id();
				$client_secret = trx_addons_widget_instagram_get_client_secret();
				// Request for short-time access token
				$resp = trx_addons_remote_post( 'https://api.instagram.com/oauth/access_token',
												array(),	// Request args: headers, timeout, etc.
												array(		// Request vars: post to server as request body
													'client_id' => $client_id,
													'client_secret' => $client_secret,
													'grant_type' => 'authorization_code',
													'code' => $code,
													'response_type' => 'code',
													'redirect_uri' => trx_addons_widget_instagram_rest_get_redirect_url()
												)
											);
				if ( substr($resp, 0, 1) == '{' ) {
					$resp = json_decode($resp, true);
					if ( ! empty( $resp['access_token'] ) ) {
						$access_token = $resp['access_token'];
						if ( ! empty( $resp['user_id'] ) ) {
							$user_id = $resp['user_id'];
						}
						// Request for long-time access token
						$resp = trx_addons_remote_get( 'https://graph.instagram.com/access_token'
														. '?grant_type=ig_exchange_token'
														. "&client_secret={$client_secret}"
														. "&access_token={$access_token}"
													);
						if ( substr($resp, 0, 1) == '{' ) {
							$resp = json_decode($resp, true);
							if ( ! empty( $resp['access_token'] ) ) {
								$access_token = $resp['access_token'];
								$access_token_valid_time = max( $access_token_valid_time, ! empty( $resp['expires-in'] ) ? $resp['expires-in'] - 2 * 24 * 60 * 60 : 0 );
							}
						}
					}
				}
			}
			
			// Save access token
			if ( ! empty( $access_token ) ) {
				$options = get_option('trx_addons_options');
				$options['api_instagram_access_token'] = strip_tags($access_token);
				if ( ! empty( $user_id ) ) {
					$options['api_instagram_user_id'] = strip_tags($user_id);
				}
				update_option('trx_addons_options', $options);
				set_transient('trx_addons_instagram_access_token_valid', 1, $access_token_valid_time );
			}
		}		
		
		// Redirect to the options page
		wp_redirect( get_admin_url( null, 'admin.php?page=trx_addons_options#trx_addons_options_section_api_section' ) );
		exit;
	}
}

// Refresh long-time access token
if( ! function_exists( 'trx_addons_widget_instagram_refresh_access_token' ) ) {
	function trx_addons_widget_instagram_refresh_access_token( $access_token ) {
		$resp = trx_addons_remote_get( 'https://graph.instagram.com/refresh_access_token'
										. '?grant_type=ig_refresh_token'
										. "&access_token={$access_token}"
									);
		if ( substr($resp, 0, 1) == '{' ) {
			$resp = json_decode($resp, true);
			if ( ! empty( $resp['access_token'] ) ) {
				$access_token = $resp['access_token'];
				set_transient('trx_addons_instagram_access_token_valid', 1, max( 2 * 24 * 60 * 60, ! empty( $resp['expires-in'] ) ? $resp['expires-in'] - 2 * 24 * 60 * 60 : 0 ) );

				// Save access token to options
				if ( ! empty( $access_token ) ) {
					$options = get_option('trx_addons_options');
					$options['api_instagram_access_token'] = strip_tags($access_token);
					update_option('trx_addons_options', $options);
				}
			}
		}
		return $access_token;
	}
}


//------------------------------------------------
//--  Alternative way 1: Parse Instagram html output
//------------------------------------------------

// Get output from Instagram public feed
if( ! function_exists( 'trx_addons_widget_instagram_get_page_output' ) ) {
	function trx_addons_widget_instagram_get_page_output( $args ) {
		$username = ! empty( $args['username'] ) ? strtolower( $args['username'] ) : '';
		$hashtag  = ! empty( $args['hashtag'] ) ? str_replace( '#', '', $args['hashtag'] ) : '';
		$url      = 'https://www.instagram.com/'
											. ( ! empty( $hashtag )
												? 'explore/tags/' . trim( $hashtag )	// Get output by hashtag
												: trim( $username )						// Get output by username
												)
											. '/';

		// Att.1: native request
		$output = trx_addons_remote_get( $url, array(
			'headers' => array(
				'Cache-Control' => "max-age=0",
				'Connection' => "keep-alive",
				'Accept-Encoding' => "gzip, deflate",
				'Accept-Language' => "ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3",
				'Accept' => "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8"

			)
		) );

		return empty( $output ) ? false : trx_addons_widget_instagram_parse_page_output( $output, $args );
	}
}

// Parse output from Instagram public feed
if( ! function_exists( 'trx_addons_widget_instagram_parse_page_output' ) ) {
	function trx_addons_widget_instagram_parse_page_output( $output, $args ) {

		$instagram = false;

		// New Facebook API - get JSON with media in output
		if ( substr($output, 0, 1) == '{' ) {

			$output = json_decode($output, true);

			if ( ! empty( $output['data'] ) && is_array( $output['data'] ) ) {

				$user_id = trx_addons_get_option('api_instagram_user_id');

				$instagram = array();

				foreach ( $output['data'] as $image ) {

					if ( empty( $image['media_url'] ) ) continue;

					$type = ! empty( $image['media_type'] ) && 'VIDEO' == $image['media_type'] ? 'video' : 'image';

					// Skip a video posts without thumbnails
					if ( $type == 'video' && empty( $image['thumbnail_url'] ) && apply_filters( 'trx_addons_filter_instagram_skip_video', true ) ) {
						continue;
					}

					$media = array(
									'url'    => $image['media_url'],
									'width'  => '',
									'height' => '',
									);

					$medias = array(
						'standard_resolution'	=> $media,
						'small_resolution'		=> $media,
						'medium_resolution'		=> $media,
						'large_resolution'		=> $media,
					);

					if ( $type == 'video' && ! empty( $image['thumbnail_url'] ) ) {
						$thumb = array(
										'url'    => $image['thumbnail_url'],
										'width'  => '',
										'height' => '',
										);
						$thumbs = array(
							'standard_resolution'	=> $thumb,
							'small_resolution'		=> $thumb,
							'medium_resolution'		=> $thumb,
							'large_resolution'		=> $thumb,
						);
					}

					$instagram[] = array_merge(
						array(
							'type'		=> $type,
							'link'		=> ! empty( $image['username'] ) ? '//instagram.com/' . $image['username'] : '',
							'caption'	=> array(
												'text' => ! empty( $image['caption'] )
															? $image['caption']
															: esc_html__( 'Instagram Image', 'trx_addons' )
												),
							'user'		=> array(
												'username'	=> ! empty( $image['username'] ) ? $image['username'] : '',
												'id' 		=> $user_id,
												),
							'comments'	=> array( 'count' => -1 ),
							'likes'		=> array( 'count' => -1 ),
						),
						$type == 'video'
							? array( 'videos' => $medias )
							: array( 'images' => $medias ),
						$type == 'video' && ! empty( $image['thumbnail_url'] )
							? array( 'images' => $thumbs )
							: array()
					);

					if ( count( $instagram ) >= $args['count'] ) {
						break;
					}
				}
			}

		// Alter way - parse page output
		} else {

			$data = explode( 'window._sharedData = ', $output );

			if ( count( $data ) >= 2 ) {

				$json        = explode( ';</script>', $data[1] );
				$images_list = json_decode( $json[0], true );
				$images      = false;

				if ( $images_list ) {
					if ( isset( $images_list['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'] ) ) {
						$images = $images_list['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'];
					} elseif( isset( $images_list['entry_data']['TagPage'][0]['graphql']['hashtag']['edge_hashtag_to_media']['edges'] ) ) {
						$images = $images_list['entry_data']['TagPage'][0]['graphql']['hashtag']['edge_hashtag_to_media']['edges'];
					}
				}

				if ( is_array( $images ) ) {

					$instagram = array();

					foreach ( $images as $image ) {
						$image = $image['node'];

						$thumbs = array(
							'standard_resolution'	=> array(
															'url'    => preg_replace( "/^https:/i", "", $image['thumbnail_src'] ),
															'width'  => ! empty( $image['dimensions']['width'] ) ? $image['dimensions']['width'] : '',
															'height' => ! empty( $image['dimensions']['height'] ) ? $image['dimensions']['height'] : '',
															),
							'small_resolution'		=> array(
															'url'    => preg_replace( "/^https:/i", "", $image['thumbnail_resources'][0]['src'] ),
															'width'  => ! empty( $image['thumbnail_resources'][0]['config_width'] ) ? $image['thumbnail_resources'][0]['config_width'] : '',
															'height' => ! empty( $image['thumbnail_resources'][0]['config_height'] ) ? $image['thumbnail_resources'][0]['config_height'] : '',
															),
							'medium_resolution'		=> array(
															'url'    => preg_replace( "/^https:/i", "", $image['thumbnail_resources'][2]['src'] ),
															'width'  => ! empty( $image['thumbnail_resources'][2]['config_width'] ) ? $image['thumbnail_resources'][2]['config_width'] : '',
															'height' => ! empty( $image['thumbnail_resources'][2]['config_height'] ) ? $image['thumbnail_resources'][2]['config_height'] : '',
															),
							'large_resolution'		=> array(
															'url'    => preg_replace( "/^https:/i", "", $image['thumbnail_resources'][4]['src'] ),
															'width'  => ! empty( $image['thumbnail_resources'][4]['config_width'] ) ? $image['thumbnail_resources'][4]['config_width'] : '',
															'height' => ! empty( $image['thumbnail_resources'][4]['config_height'] ) ? $image['thumbnail_resources'][4]['config_height'] : '',
															),
						);

						$type = ( $image['is_video'] ) ? 'video' : 'image';

						$instagram[] = array(
							'type'		=> $type,
							'link'		=> '//instagram.com/p/' . $image['shortcode'],
							'caption'	=> array(
												'text' => ! empty( $image['edge_media_to_caption']['edges'][0]['node']['text'] )
															? $image['edge_media_to_caption']['edges'][0]['node']['text']
															: esc_html__( 'Instagram Image', 'trx_addons' )
												),
							'user'		=> array(
												'username'	=> ! empty( $image['owner']['username'] ) ? $image['owner']['username'] : '',
												'id' 		=> ! empty( $image['owner']['id'] ) ? $image['owner']['id'] : '',
												),
							'comments'	=> array( 'count' => $image['edge_media_to_comment']['count'] ),
							'likes'		=> array( 'count' => $image['edge_liked_by']['count'] ),
							'images'	=> $thumbs,
						);

						if ( count($instagram) >= $args['count'] ) {
							break;
						}
					}
				}
			}
		}

		return $instagram;
	}
}

// Get data from client
if ( ! function_exists( 'trx_addons_widget_instagram_ajax_load_images' ) ) {
	add_action( 'wp_ajax_trx_addons_instagram_load_images',			'trx_addons_widget_instagram_ajax_load_images' );
	add_action( 'wp_ajax_nopriv_trx_addons_instagram_load_images',	'trx_addons_widget_instagram_ajax_load_images' );
	function trx_addons_widget_instagram_ajax_load_images() {

		trx_addons_verify_nonce();

		$response = array( 'error' => '', 'data' => '' );
	
		$output = trx_addons_get_value_gp( 'output' );
		$hash   = str_replace( array( ' ', "\t", "\r", "\n" ), '', trx_addons_get_value_gp( 'hash' ) );

		if ( ! empty( $output ) ) {
			if ( $hash ) {
				$args = get_transient( sprintf( 'trx_addons_instagram_args_%s', $hash ) );
				if ( is_array($args) && ! empty( $args['hashtag'] ) ) {
					$data = array(
						'data' => trx_addons_widget_instagram_parse_page_output( $output, $args )
					);
					if ( is_array($data) && is_array($data['data']) && count($data['data']) > 0 ) {
						set_transient( sprintf( 'trx_addons_instagram_data_%1$s_%2$s',
													trx_addons_widget_instagram_get_client_id(),
													$args['hashtag']
												),
										$data,
										TRX_ADDONS_WIDGET_INSTAGRAM_CACHE_TIME
										);
					}
					ob_start();
					trx_addons_get_template_part( TRX_ADDONS_PLUGIN_WIDGETS . 'instagram/tpl.default.php',
												'trx_addons_args_widget_instagram', 
												apply_filters( 'trx_addons_filter_widget_args',
																$args,
																$args,
																'trx_addons_widget_instagram'
																)
												);
					trx_addons_show_layout( apply_filters( 'trx_addons_filter_inline_css', trx_addons_get_inline_css() ), '<style type="text/css" id="trx_socials-inline-styles-inline-css">', '</style>' );
					$response['data'] = ob_get_contents();
					ob_end_clean();
				} else {
					$response['error'] = esc_html__( 'Instagram Widget misconfigured, check widget settings.', 'trx_addons' );
				}
			} else {
				$response['error'] = esc_html__( 'Invalid hash value.', 'trx_addons' );
			}
		} else {
			$response['error'] = esc_html__( 'Get items from the Public Feed failed. Malformed data structure.', 'trx_addons' );
		}

		trx_addons_ajax_response( $response );
	}
}


//------------------------------------------------
//--  Alternative way 2: Show demo images
//------------------------------------------------
if( ! function_exists( 'trx_addons_widget_instagram_demo_images' ) ) {
	function trx_addons_widget_instagram_demo_images( $args ) {
		$instagram = array();
		$list = array();
		$titles = array();
		$url = '';

		if ( ! empty( $args['demo_files'] ) && is_array( $args['demo_files'] ) && count( $args['demo_files'] ) > 0
			&& ( ! empty( $args['demo_files'][0]['image'] ) || ! empty( $args['demo_files'][0]['video'] ) )
		) {
			foreach( $args['demo_files'] as $file ) {
				if ( ! empty( $file['image'] ) ) {
					$list[] = trx_addons_get_attachment_url(
									$file['image'],
									! empty( $args['demo_thumb_size'] )
										? $args['demo_thumb_size']
										: apply_filters( 'trx_addons_filter_thumb_size',
											trx_addons_get_thumb_size('avatar'),
											'trx_addons_widget_instagram',
											$args
										)
								);
					$titles[] = trx_addons_get_attachment_caption(
									(int) $file['image'] > 0
										? $file['image']
										: ( ! empty( $file['image_extra']['id'] )
											? $file['image_extra']['id']
											: trx_addons_attachment_url_to_postid( $file['image'] )
											)
								);
				} else if ( ! empty( $file['video'] ) ) {
					$list[] = $file['video'];
				}
			}
		} else {
			$dir = trx_addons_get_folder_dir( TRX_ADDONS_PLUGIN_WIDGETS . 'instagram/demo' );
			$url = trx_addons_get_folder_url( TRX_ADDONS_PLUGIN_WIDGETS . 'instagram/demo' );
			$list = ! empty($dir) ? trx_addons_list_files( $dir ) : array();
		}
		if ( is_array( $list ) ) {
			foreach ( $list as $k => $v) {
				$ext  = trx_addons_get_file_ext( $v );
				$type = in_array( $ext, array( 'mp4', 'mpg', 'avi' ) ) ? 'video' : 'image';
				$size = $type == 'image'
							? trx_addons_getimagesize( $v )
							: array( 1280, 720 );
				$thumb = array(
								'url'    => ! empty( $url ) ? trailingslashit( $url ) . basename( $v ) : $v,
								'width'  => ! empty( $size[0] ) ? $size[0] : '',
								'height' => ! empty( $size[1] ) ? $size[1] : '',
								);
				$thumbs = array(
					'standard_resolution' => $thumb,
					'small_resolution'    => $thumb,
					'medium_resolution'   => $thumb,
					'large_resolution'    => $thumb,
				);
				$instagram[] = array(
					'type'		=> $type,
					'link'		=> ! empty( $url ) ? trailingslashit( $url ) . basename( $v ) : $v,
					'caption'	=> array(
										'text' => ! empty( $titles[ $k ] ) ? $titles[ $k ] : ''
										),
					'user'		=> array(
										'username'	=> esc_html__( 'Demo Instagram User', 'trx_addons' ),
										'id' 		=> '',
										),
					'comments'	=> array( 'count' => mt_rand(0, 100) ),
					'likes'		=> array( 'count' => mt_rand(0, 100) ),
					( $type == 'image' ? 'images' : 'videos' ) => $thumbs,
				);
				if ( count($instagram) >= $args['count'] ) {
					break;
				}
			}
		}
		return ! empty( $instagram ) ? $instagram : false;
	}
}
