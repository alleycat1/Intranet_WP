<?php
namespace TrxAddons\AiHelper\MediaLibrary;

use TrxAddons\AiHelper\OpenAi;
use TrxAddons\AiHelper\StableDiffusion;
use TrxAddons\AiHelper\Lists;
use TrxAddons\AiHelper\Utils;

if ( ! class_exists( 'Helper' ) ) {

	/**
	 * Main class for AI Helper MediaSelector support
	 */
	class Helper {

		/**
		 * Constructor
		 */
		function __construct() {
			add_action( 'trx_addons_action_load_scripts_admin', array( $this, 'enqueue_scripts_admin' ) );
			add_filter( 'trx_addons_filter_localize_script_admin', array( $this, 'localize_script_admin' ) );

			// AJAX callback for the 'Generate images' button
			add_action( 'wp_ajax_trx_addons_ai_helper_generate_images', array( $this, 'generate_images' ) );

			// AJAX callback for the 'Make variations' button
			add_action( 'wp_ajax_trx_addons_ai_helper_make_variations', array( $this, 'make_variations' ) );

			// AJAX callback for the 'Add to Uploads' button
			add_action( 'wp_ajax_trx_addons_ai_helper_add_to_uploads', array( $this, 'add_to_uploads' ) );

			// AJAX callback for the 'Fetch images'
			add_action( 'wp_ajax_trx_addons_ai_helper_fetch_images', array( $this, 'fetch_images' ) );
			add_action( 'wp_ajax_nopriv_trx_addons_ai_helper_fetch_images', array( $this, 'fetch_images' ) );
		}

		/**
		 * Check if AI Helper is allowed for MediaSelector
		 */
		public static function is_allowed() {
			return OpenAi::instance()->get_api_key() != '' || StableDiffusion::instance()->get_api_key() != '';
		}

		/**
		 * Enqueue scripts and styles for the admin mode
		 * 
		 * @hooked 'admin_enqueue_scripts'
		 */
		function enqueue_scripts_admin() {
			if ( self::is_allowed() ) {
				wp_enqueue_style( 'trx_addons-ai-helper-media-selector', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/support/MediaLibrary/assets/css/index.css' ), array(), null );
				wp_enqueue_script( 'trx_addons-ai-helper-media-selector', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/support/MediaLibrary/assets/js/index.js' ), array( 'jquery' ), null, true );
			}
		}

		/**
		 * Localize script to show messages in the admin mode
		 * 
		 * @hooked 'trx_addons_filter_localize_script_admin'
		 * 
		 * @param array $vars  Array of variables to be passed to the script
		 * 
		 * @return array  Modified array of variables
		 */
		function localize_script_admin( $vars ) {
			if ( self::is_allowed() ) {
				$vars['msg_ai_helper_error'] = esc_html__( "AI Helper unrecognized response", 'trx_addons' );
				$vars['msg_ai_helper_prompt_error'] = esc_html__( "Prompt is empty!", 'trx_addons' );
				$vars['ai_helper_generate_image_models'] = Lists::get_list_ai_image_models();
				$vars['ai_helper_generate_image_styles'] = Lists::get_list_stability_ai_styles();
				$vars['ai_helper_generate_image_sizes'] = Lists::get_list_ai_image_sizes();
				$vars['ai_helper_generate_image_openai_sizes'] = Lists::get_list_ai_image_sizes( 'openai' );
				$vars['ai_helper_generate_image_numbers'] = trx_addons_get_list_range( 1, 10 );
			}
			return $vars;
		}

		/**
		 * Send a query to API to generate images from the prompt
		 * 
		 * @hooked 'wp_ajax_trx_addons_ai_helper_generate_images'
		 * 
		 * @param WP_REST_Request  $request  Full details about the request.
		 */
		function generate_images( $request = false ) {

			trx_addons_verify_nonce();

			$answer = array(
				'error' => '',
				'data' => array(
					'images' => array()
				)
			);
			if ( current_user_can( 'edit_posts' ) ) {
				if ( $request ) {
					$params = $request->get_params();
					$model  = ! empty( $params['model'] ) ? $params['model'] : Utils::get_default_image_model();
					$style  = ! empty( $params['style'] ) ? $params['style'] : '';
					$size   = ! empty( $params['size'] ) ? $params['size'] : Utils::get_default_image_size();
					$width  = $size == 'custom' && ! empty( $params['width'] ) ? (int)$params['width'] : 0;
					$height = $size == 'custom' && ! empty( $params['height'] ) ? (int)$params['height'] : 0;
					$number = ! empty( $params['number'] ) ? (int)$params['number'] : 1;
					$prompt = ! empty( $params['prompt'] ) ? $params['prompt'] : '';
				} else {
					$model  = trx_addons_get_value_gp( 'model', Utils::get_default_image_model() );
					$style  = trx_addons_get_value_gp( 'style', '' );
					$size   = trx_addons_get_value_gp( 'size', Utils::get_default_image_size() );
					$width  = $size == 'custom' ? (int)trx_addons_get_value_gp( 'width', 0 ) : 0;
					$height = $size == 'custom' ? (int)trx_addons_get_value_gp( 'height', 0 ) : 0;
					$number = (int)trx_addons_get_value_gp( 'number', 1 );
					$prompt = trx_addons_get_value_gp( 'prompt' );
					$params = compact( 'model', 'size', 'width', 'height', 'number', 'prompt' );
				}
				$number = max( 1, min( 10, $number ) );
				if ( Utils::is_stable_diffusion_model( $model ) ) {
					$number = max( 1, min( 4, $number ) );
				}
				if ( ! empty( $prompt ) ) {
					$api = Utils::get_image_api( $model );
					$args = array(
						'prompt' => apply_filters( 'trx_addons_filter_ai_helper_prompt', $prompt, $params, 'media_library_generate_images' ),
						'size' => Utils::check_image_size( $size ),
						'n' => (int)$number,
					);
					if ( Utils::is_stable_diffusion_model( $model ) || Utils::is_stability_ai_model( $model ) ) {
						$args['model'] = $model;
						$width  = max( 0, min( Utils::get_max_image_width(), $width ) );
						$height = max( 0, min( Utils::get_max_image_height(), $height ) );
						if ( $size == 'custom' && $width > 0 && $height > 0 ) {
							$args['width'] = (int)$width;
							$args['height'] = (int)$height;
						}
					}
					if ( Utils::is_stability_ai_model( $model ) && ! empty( $style ) ) {
						$args['style'] = $style;
					}
					$response = $api->generate_images( apply_filters( 'trx_addons_filter_ai_helper_generate_images_args', $args, 'media_library_generate_images' ) );
					$answer = Utils::parse_response( $response, $model, $answer );
				} else {
					$answer['error'] = __( 'Error! Empty prompt.', 'trx_addons' );
				}
			}

			if ( $request ) {
				// Return response to the REST API
				return rest_ensure_response( $answer );
			} else {
				// Return response to the AJAX handler
				trx_addons_ajax_response( $answer );
			}
		}

		/**
		 * Send a query to API to make variations of the image
		 * 
		 * @hooked 'wp_ajax_trx_addons_ai_helper_make_variations'
		 * 
		 * @param WP_REST_Request  $request  Full details about the request.
		 */
		function make_variations( $request = false ) {

			trx_addons_verify_nonce();

			$answer = array(
				'error' => '',
				'data' => array(
					'images' => array()
				)
			);
			if ( current_user_can( 'edit_posts' ) ) {
				if ( $request ) {
					$params = $request->get_params();
					$prompt = ! empty( $params['prompt'] ) ? $params['prompt'] : '';
					$model  = ! empty( $params['model'] ) ? $params['model'] : Utils::get_default_image_model();
					$style  = ! empty( $params['style'] ) ? $params['style'] : '';
					$size   = ! empty( $params['size'] ) ? (int)$params['size'] : Utils::get_default_image_size();
					$width  = ! empty( $params['width'] ) ? (int)$params['width'] : 0;
					$height = ! empty( $params['height'] ) ? (int)$params['height'] : 0;
					$number = ! empty( $params['number'] ) ? (int)$params['number'] : 1;
					$image  = ! empty( $params['image'] ) ? $params['image'] : '';
				} else {
					$prompt = trx_addons_get_value_gp( 'prompt', '' );
					$model  = trx_addons_get_value_gp( 'model', Utils::get_default_image_model() );
					$style  = trx_addons_get_value_gp( 'style', '' );
					$size   = trx_addons_get_value_gp( 'size', Utils::get_default_image_size() );
					$width  = (int)trx_addons_get_value_gp( 'width', 0 );
					$height = (int)trx_addons_get_value_gp( 'height', 0 );
					$number = (int)trx_addons_get_value_gp( 'number', 1 );
					$image  = trx_addons_get_value_gp( 'image' );
					$params = compact( 'prompt', 'model', 'size', 'width', 'height', 'number', 'image' );
				}
				$number = max( 1, min( 10, $number ) );
				if ( Utils::is_stable_diffusion_model( $model ) ) {
					$number = max( 1, min( 4, $number ) );
				}
				if ( ! empty( $image ) ) {
					$api = Utils::get_image_api( $model );
					$args = array(
						'image' => $image,
						'size'  => Utils::check_image_size( $size ),
						'n'     => (int)$number,
					);
					if ( Utils::is_stable_diffusion_model( $model ) || Utils::is_stability_ai_model( $model ) ) {
						$args['model']  = $model;
						$args['prompt'] = apply_filters( 'trx_addons_filter_ai_helper_prompt', $prompt, $args, 'media_library_variations' );
						$width  = max( 0, min( Utils::get_max_image_width(), $width ) );
						$height = max( 0, min( Utils::get_max_image_height(), $height ) );
						if ( $width > 0 && $height > 0 ) {
							$args['width'] = (int)$width;
							$args['height'] = (int)$height;
						}
					}
					if ( Utils::is_stability_ai_model( $model ) && ! empty( $style ) ) {
						$args['style'] = $style;
					}
					$response = $api->make_variations( apply_filters( 'trx_addons_filter_ai_helper_variations_args', $args, 'media_library_variations' ) );
					$answer = Utils::parse_response( $response, $model, $answer );
				} else {
					$answer['error'] = __( 'Error! Image is not specified.', 'trx_addons' );
				}
			}

			if ( $request ) {
				// Return response to the REST API
				return rest_ensure_response( $answer );
			} else {
				// Return response to the AJAX handler
				trx_addons_ajax_response( $answer );
			}
		}

		/**
		 * Add an image to the media library
		 * 
		 * @hooked 'wp_ajax_trx_addons_ai_helper_add_to_uploads'
		 * 
		 * @param WP_REST_Request  $request  Full details about the request.
		 */
		function add_to_uploads( $request = false ) {

			trx_addons_verify_nonce();

			$answer = array(
				'error' => '',
				'data' => ''
			);
			if ( current_user_can( 'edit_posts' ) ) {
				if ( $request ) {
					$params = $request->get_params();
					$image = ! empty( $params['image'] ) ? $params['image'] : '';
					$filename = ! empty( $params['filename'] ) ? $params['filename'] : '';
					$caption = ! empty( $params['caption'] ) ? $params['caption'] : '';
				} else {
					$image = trx_addons_get_value_gp( 'image' );
					$filename = trx_addons_get_value_gp( 'filename' );
					$caption = trx_addons_get_value_gp( 'caption' );
				}
				if ( ! empty( $image ) ) {
					$parts = explode( '.', trim( $filename ) );
					$filename = trx_addons_esc( str_replace( ' ', '-', $parts[0] ) . '.png' );
					$attach_id = trx_addons_save_image_to_uploads( array(
						'image' => '',				// binary data of the image
						'image_url' => $image,		// or URL of the image
						'filename' => $filename,	// filename for the image in the media library
						'caption' => $caption,		// caption for the image in the media library
					) );
					if ( $attach_id == 0 || is_wp_error( $attach_id ) ) {
						$answer['error'] = is_wp_error( $attach_id ) ? $attach_id->get_error_message() : __( "Error! Can't insert an image into the media library.", 'trx_addons' );
					} else {
						$answer['data'] = $attach_id;
					}
				} else {
					$answer['error'] = __( 'Error! Image URL is empty.', 'trx_addons' );
				}
			}
			if ( $request ) {
				// Return response to the REST API
				return rest_ensure_response( $answer );
			} else {
				// Return response to the AJAX handler
				trx_addons_ajax_response( $answer );
			}
		}

		/**
		 * Fetch images from the Stable Diffusion API
		 * 
		 * @hooked 'wp_ajax_trx_addons_ai_helper_fetch_images'
		 * 
		 * @param WP_REST_Request  $request  Full details about the request.
		 */
		function fetch_images( $request = false ) {

			trx_addons_verify_nonce();

			$answer = array(
				'error' => '',
				'data' => array(
					'images' => array()
				)
			);

			if ( $request ) {
				$params = $request->get_params();
				$model  = ! empty( $params['fetch_model'] ) ? $params['fetch_model'] : Utils::get_default_image_model();
				$id     = ! empty( $params['fetch_id'] ) ? $params['fetch_id'] : '';
			} else {
				$model   = trx_addons_get_value_gp( 'fetch_model', Utils::get_default_image_model() );
				$id      = trx_addons_get_value_gp( 'fetch_id', '' );
			}

			if ( ! empty( $id ) ) {
				// Check if the id is in the cache and it is the same model
				$saved_model = Utils::get_data_from_cache( $id );
				if ( $saved_model == $model ) {
					$api = StableDiffusion::instance();
					$response = $api->fetch_images( array(
						'fetch_id' => $id,
						'model'    => $model,
					) );
					$answer = Utils::parse_response( $response, $model, $answer );
					// Remove id from the cache if images are fetched
					if ( count( $answer['data']['images'] ) > 0 ) {
						Utils::delete_data_from_cache( $id );
					}
				} else {
					$answer['error'] = __( 'Error! Incorrect the queue ID for fetch images from server.', 'trx_addons' );
				}
			} else {
				$answer['error'] = __( 'Error! Need the queue ID for fetch images from server.', 'trx_addons' );
			}

			if ( $request ) {
				// Return response to the REST API
				return rest_ensure_response( $answer );
			} else {
				// Return response to the AJAX handler
				trx_addons_ajax_response( $answer );
			}
		}
	}
}
