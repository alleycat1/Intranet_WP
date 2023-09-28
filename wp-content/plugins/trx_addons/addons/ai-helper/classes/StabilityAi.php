<?php
namespace TrxAddons\AiHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to make queries to the Stability AI API
 */
class StabilityAi extends Singleton {

	/**
	 * The object to log queries to the API
	 *
	 * @access private
	 * 
	 * @var Logger  The object to log queries to the API
	 */
	var $logger = null;

	/**
	 * The object of the API
	 *
	 * @access private
	 * 
	 * @var api  The object of the API
	 */
	var $api = null;

	/**
	 * Plugin constructor.
	 *
	 * @access protected
	 */
	protected function __construct() {
		parent::__construct();
		$this->logger = Logger::instance();
		$this->logger->set_section( 'stability-ai' );
	}

	/**
	 * Return an object of the API
	 * 
	 * @param string $token  API token for the API
	 * 
	 * @return api  The object of the API
	 */
	public function get_api( $token = '' ) {
		if ( empty( $this->api ) ) {
			if ( empty( $token ) ) {
				$token = $this->get_token();
			}
			if ( ! empty( $token ) ) {
				$this->api = new \StabilityAi\Api\Images( $token );
			}
		}
		return $this->api;
	}

	/**
	 * Return an API token for the API from the plugin options.
	 * This method is a wrapper for the get_token() method to allow to override it in the child classes.
	 * 
	 * @access public
	 * 
	 * @return string  API key for the API
	 */
	public function get_api_key() {
		return $this->get_token();
	}

	/**
	 * Return an API token for the API from the plugin options
	 * 
	 * @access protected
	 * 
	 * @return string  API token for the API
	 */
	protected function get_token() {
		return trx_addons_get_option( 'ai_helper_token_stability_ai' );
	}


	/**
	 * Return a cfg scale for the API
	 * 
	 * @access protected
	 * 
	 * @return float  Cfg scale for the API
	 */
	protected function get_cfg_scale() {
		return (float)trx_addons_get_option( 'ai_helper_cfg_scale_stability_ai', 7 );
	}

	/**
	 * Return diffusion steps for the API
	 * 
	 * @access protected
	 * 
	 * @return int  Diffusion steps for the API
	 */
	protected function get_diffusion_steps() {
		return (int)trx_addons_get_option( 'ai_helper_diffusion_steps_stability_ai', 50 );
	}

	/**
	 * Return a weight of the text prompt for the API
	 * 
	 * @access protected
	 * 
	 * @return float  Weight of the text prompt for the API
	 */
	protected function get_prompt_weight() {
		return (float)trx_addons_get_option( 'ai_helper_prompt_weight_stability_ai', 1.0 );
	}

	/**
	 * Return a default model for the API
	 * 
	 * @access protected
	 * 
	 * @return string  Default model for the API
	 */
	protected function get_default_model() {
		return 'stable-diffusion-xl-1024-v1-0';
	}

	/**
	 * Prepare arguments for the API format
	 * 
	 * @access protected
	 * 
	 * @param array $args  Arguments to prepare
	 * 
	 * @return array  Prepared arguments
	 */
	protected function prepare_args( $args ) {
		// token => key
		if ( ! isset( $args['key'] ) ) {
			$args['key'] = $args['token'];
			unset( $args['token'] );
		}
		// prompt => text_prompts
		if ( ! isset( $args['text_prompts'] ) ) {
			$args['text_prompts'] = array(
				array(
					'text' => $args['prompt'],
					'weight' => $this->get_prompt_weight()
				)
			);
			unset( $args['prompt'] );
		}
		// size => width, height
		if ( ! isset( $args['width'] ) ) {
			$size = explode( 'x', $args['size'] );
			unset( $args['size'] );
			if ( count( $size ) == 2 ) {
				$args['width'] = (int)$size[0];
				$args['height'] = (int)$size[1];
			}
		}
		// n => samples
		if ( ! isset( $args['samples'] ) ) {
			$args['samples'] = max( 1, min( 10, (int)$args['n'] ) );
			unset( $args['n'] );
		}
		// model => model_id
		if ( ! isset( $args['model_id'] ) ) {
			$args['model_id'] = $args['model'];
			unset( $args['model'] );
		}
		$args['model_id'] = str_replace( 'stability-ai/', '', $args['model_id'] );
		// image => init_image
		if ( ! isset( $args['init_image'] ) && isset( $args['image'] ) ) {
			$args['init_image'] = $args['image'];
			unset( $args['image'] );
		}
		// Style preset
		if ( ! isset( $args['style_preset'] ) && isset( $args['style'] ) ) {
			$args['style_preset'] = $args['style'];
			unset( $args['style'] );
		}
		return $args;
	}

	/**
	 * Generate images via API
	 *
	 * @access public
	 * 
	 * @param array $args  Query arguments
	 * 
	 * @return array  Response from the API
	 */
	public function generate_images( $args = array() ) {
		$args = array_merge( array(
			'token' => $this->get_token(),
			'prompt' => '',
			'size' => '1024x1024',
			'n' => 1,
			'model' => $this->get_default_model(),
			'steps' => (int)$this->get_diffusion_steps(),
			'cfg_scale' => (float)$this->get_cfg_scale()
		), $args );

		// Save a model name for the log
		$model = str_replace( 'stability-ai/', '', ! empty( $args['model'] ) ? $args['model'] : $this->get_default_model() );
		$args_orig = $args;

		// Prepare arguments for SD API format
		$args = $this->prepare_args( $args );

		$response = false;

		if ( ! empty( $args['key'] ) && ! empty( $args['text_prompts'] ) ) {
			$api = $this->get_api( $args['key'] );
			$response = $api->textToImage( $args );
			if ( is_string( $response ) ) {
				$response = trim( $response );
				if ( substr( trim( $response ), 0, 1 ) == '{' ) {
					$response = json_decode( $response, true );
					$this->logger->log( $response, $model, $args_orig );
				}
			} else {
				$response = false;
			}
		}

		return $response;

	}


	/**
	 * Make an image variations via API
	 *
	 * @access public
	 * 
	 * @param array $args  Query arguments
	 * 
	 * @return array  Response from the API
	 */
	public function make_variations( $args = array() ) {
		$args = array_merge( array(
			'token' => $this->get_token(),
			'prompt' => '',
			'image' => '',
			'size' => '1024x1024',
			'n' => 1,
			'model' => $this->get_default_model(),
			'steps' => (int)$this->get_diffusion_steps(),
			'cfg_scale' => (float)$this->get_cfg_scale()
		), $args );

		// Save a model name for the log
		$model = str_replace( 'stability-ai/', '', ! empty( $args['model'] ) ? $args['model'] : $this->get_default_model() );
		$args_orig = $args;

		// Prepare arguments for SD API format
		$args = $this->prepare_args( $args );

		$response = false;

		if ( ! empty( $args['key'] ) && ! empty( $args['init_image'] ) ) {

			$api = $this->get_api( $args['key'] );

			if ( empty( $args['prompt'] ) ) {
				$args['prompt'] = __( 'Make variations of the image.', 'trx_addons' );
			}

			$response = $api->imageToImage( $args );
			if ( is_string( $response ) ) {
				$response = trim( $response );
				if ( substr( trim( $response ), 0, 1 ) == '{' ) {
					$response = json_decode( $response, true );
					$this->logger->log( $response, $model, $args_orig );
				}
			} else {
				$response = false;
			}
		}

		return $response;

	}

}
