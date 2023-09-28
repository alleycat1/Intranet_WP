<?php
namespace TrxAddons\AiHelper;

use TrxAddons\AiHelper\OpenAi;
use TrxAddons\AiHelper\StableDiffusion;
use TrxAddons\AiHelper\StabilityAi;

if ( ! class_exists( 'Utils' ) ) {

	/**
	 * Utilities for text and image generation
	 */
	class Utils {

		static $cache_time = 10 * 60;	// Cache time (in seconds)
		static $cache_name = 'trx_addons_ai_helper_cache';	// Cache name

		/**
		 * Save a cache data
		 * 
		 * @param array  Cache data
		 * 
		 * @return void
		 */
		static function set_cache_data( $cache ) {
			set_transient( self::$cache_name, $cache, self::$cache_time );
		}

		/**
		 * Return a cache data
		 * 
		 * @return array  Cache data
		 */
		static function get_cache_data() {
			return self::clear_expired_cache_data( get_transient( self::$cache_name, array() ) );
		}

		/**
		 * Clear expired data from the cache
		 * 
		 * @param array $cache  Cache data
		 * 
		 * @return array  Cache data
		 */
		static function clear_expired_cache_data( $cache ) {
			if ( ! empty( $cache ) && is_array( $cache ) ) {
				foreach( $cache as $id => $data ) {
					if ( ! empty( $data['time'] ) && $data['time'] + self::$cache_time < time() ) {
						unset( $cache[ $id ] );
					}
				}
			}
			return $cache;
		}


		/**
		 * Save a value for the specified id to the cache for the future use
		 * 
		 * @param string $id     Cache id
		 * @param string $value  Value to save
		 * 
		 * @return void
		 */
		static function save_data_to_cache( $id, $value ) {
			$cache = self::get_cache_data();
			$cache[ $id ] = array( 'value' => $value, 'time' => time() );
			self::set_cache_data( $cache );
		}

		/**
		 * Return an entry value for the specified id from the cache if exists
		 * 
		 * @param string $id  Cache id
		 * 
		 * @return string  Entry value saved with a specified id or empty string
		 */
		static function get_data_from_cache( $id ) {
			$cache = self::get_cache_data();
			return isset( $cache[ $id ]['value'] ) ? $cache[ $id ]['value'] : '';
		}

		/**
		 * Delete a cache entry with the specified id from the cache
		 * 
		 * @param string $id  Cache id
		 * 
		 * @return void
		 */
		static function delete_data_from_cache( $id ) {
			$cache = self::get_cache_data();
			if ( isset( $cache[ $id ] ) ) {
				unset( $cache[ $id ] );
				self::set_cache_data( $cache );
			}
		}

		/**
		 * Parse the response and return an array with answer
		 * 
		 * @param string $response  Response from the API
		 * @param string $model     Image model
		 * 
		 * @return array  Array with answer
		 */
		static function parse_response( $response, $model, $answer ) {
			// Stable Diffusion API response
			if ( self::is_stable_diffusion_model( $model ) ) {
				if ( ! empty( $response['status'] ) ) {
					if ( $response['status'] == 'success' ) {
						if ( ! empty( $response['output'] ) && is_array( $response['output'] ) && ! empty( $response['output'][0] ) ) {
							foreach( $response['output'] as $image_url ) {
								$answer['data']['images'][] = array(
									'url' => $image_url,
								);
								// Save image name and url to the cache
								self::save_data_to_cache( trx_addons_get_file_name( $image_url, false ), $image_url );
							}
						} else {
							$answer['error'] = __( 'Error! Unexpected answer from the Stable Diffusion API.', 'trx_addons' );
						}
					} else if ( $response['status'] == 'processing' ) {
						if ( ! empty( $response['fetch_result'] ) ) {
							$answer['data'] = array_merge( $answer['data'], apply_filters( 'trx_addons_filter_ai_helper_fetch', array(
								'fetch_model' => $model,
								'fetch_id'  => $response['id'],
								'fetch_img' => trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/images/fetch.png' ),
								'fetch_msg' => __( 'wait for render...', 'trx_addons' ),
								'fetch_time' => 4000,
							) ) );
							// Save id to the cache
							self::save_data_to_cache( $response['id'], $model );
						}
					} else if ( $response['status'] == 'error' ) {
						if ( ! empty( $response['message'] ) ) {
								$answer['error'] = is_array( $response['message'] ) ? join( ', ', trx_addons_array_get_values( $response['message'] ) ) : $response['message'];
						} else if ( ! empty( $response['messege'] ) ) {
								$answer['error'] = is_array( $response['messege'] ) ? join( ', ', trx_addons_array_get_values( $response['messege'] ) ) : $response['messege'];
						} else {
							$answer['error'] = __( 'Error! Unexpected response from the Stable Diffusion API.', 'trx_addons' );
						}
					} else {
						$answer['error'] = __( 'Error! Unexpected response from the Stable Diffusion API.', 'trx_addons' );
					}
				} else {
					$answer['error'] = __( 'Error! Unknown response from the Stable Diffusion API.', 'trx_addons' );
				}

			// Stability AI API response
			} else if ( self::is_stability_ai_model( $model ) ) {
				if ( ! empty( $response['artifacts'] ) ) {
					if ( is_array( $response['artifacts'] ) && ! empty( $response['artifacts'][0]['base64'] ) ) {
						foreach( $response['artifacts'] as $data ) {
							if ( empty( $data['base64'] ) || empty( $data['finishReason'] ) || ! in_array( $data['finishReason'], apply_filters( 'trx_addons_filter_ai_helper_stability_finish_reasons', array( 'SUCCESS', 'CONTENT_FILTERED' ) ) ) ) {
								continue;
							}
							$image_url = trx_addons_uploads_save_data( base64_decode( $data['base64'] ), array(
								'expire' => apply_filters( 'trx_addons_filter_ai_helper_stability_expire_time', self::$cache_time ),
							) );
							$answer['data']['images'][] = array(
								'url' => $image_url,
							);
							// Save image name and url to the cache
							self::save_data_to_cache( trx_addons_get_file_name( $image_url, false ), $image_url );
						}
					} else {
						$answer['error'] = __( 'Error! Unexpected answer from the Stable Diffusion API.', 'trx_addons' );
					}
				} else if ( ! empty( $response['message'] ) ) {
					$answer['error'] = $response['message'];
				} else {
					$answer['error'] = __( 'Error! Unknown response from the Stability AI API.', 'trx_addons' );
				}

			// OpenAI API response
			} else {
				if ( ! empty( $response['data'] ) && ! empty( $response['data'][0]['url'] ) ) {
					$answer['data']['images'] = $response['data'];
					// Save image name and url to the cache
					foreach( $response['data'] as $image_data ) {
						if ( ! empty( $image_data['url'] ) ) {
							self::save_data_to_cache( trx_addons_get_file_name( $image_data['url'], false ), $image_data['url'] );
						}
					}
				} else {
					if ( ! empty( $response['error']['message'] ) ) {
						$answer['error'] = $response['error']['message'];
					} else {
						$answer['error'] = __( 'Error! Unknown response from the OpenAI API.', 'trx_addons' );
					}
				}
			}
			return $answer;
		}

		/**
		 * Return an API object for the specified model
		 * 
		 * @param string $model  Image model
		 * 
		 * @return object  API object
		 */
		static function get_image_api( $model ) {
			if ( self::is_stable_diffusion_model( $model ) ) {
				return StableDiffusion::instance();
			} else if ( self::is_stability_ai_model( $model ) ) {
				return StabilityAi::instance();
			} else {
				return OpenAi::instance();
			}
		}

		/**
		 * Return default image model for the 'Generate images' button and the 'Make variations' button
		 * 
		 * @return string  Image model
		 */
		static function get_default_image_model() {
			return 'openai/default';
		}

		/**
		 * Return default image size for the 'Generate images' button and the 'Make variations' button
		 * 
		 * @return string  Image size in format 'WxH'
		 */
		static function get_default_image_size( $mode = 'media' ) {
			return $mode == 'media' ? '1024x1024' : '256x256';
		}

		/**
		 * Check if the image is in the list of allowed sizes
		 * 
		 * @return string  Image size in format 'WxH'
		 */
		static function check_image_size( $size, $mode = 'media' ) {
			$allowed_sizes = Lists::get_list_ai_image_sizes();
			return ! empty( $allowed_sizes[ $size ] ) ? $size : self::get_default_image_size( $mode );
		}

		/**
		 * Return a maximum width of the image for the 'Generate images' button and the 'Make variations' button
		 * 
		 * @return int  Max width in pixels
		 */
		static function get_max_image_width( $model = '' ) {
			return apply_filters( 'trx_addons_filter_ai_helper_max_image_width', empty( $model ) || self::is_stability_ai_model( $model ) ? 1536 : 1024, $model );
		}

		/**
		 * Return a maximum height of the image for the 'Generate images' button and the 'Make variations' button
		 * 
		 * @return int  Max height in pixels
		 */
		static function get_max_image_height( $model = '' ) {
			return apply_filters( 'trx_addons_filter_ai_helper_max_image_height', empty( $model ) || self::is_stability_ai_model( $model ) ? 1536 : 1024, $model );
		}

		/**
		 * Check if the model is support an image style presets
		 * 
		 * @param string $model  Image model
		 * 
		 * @return bool  True if the model support an image style presets
		 */
		static function is_model_support_image_style( $model ) {
			return self::is_stability_ai_model( $model );
		}

		/**
		 * Check if the model is support separate image dimensions 'width' and 'height' or only 'size'
		 * 
		 * @param string $model  Image model
		 * 
		 * @return bool  True if the model support separate image dimensions 'width' and 'height'
		 */
		static function is_model_support_image_dimensions( $model ) {
			return self::is_stable_diffusion_model( $model ) || self::is_stability_ai_model( $model );
		}

		/**
		 * Check if the model is a Stable Diffusion model
		 * 
		 * @param string $model  Image model
		 * 
		 * @return bool  True if the model is a Stable Diffusion model
		 */
		static function is_stable_diffusion_model( $model ) {
			return strpos( $model, 'stabble-diffusion/' ) !== false;
		}

		/**
		 * Check if the model is a Stability AI model
		 * 
		 * @param string $model  Image model
		 * 
		 * @return bool  True if the model is a Stability AI model
		 */
		static function is_stability_ai_model( $model ) {
			return strpos( $model, 'stability-ai/' ) !== false;
		}
	}
}
