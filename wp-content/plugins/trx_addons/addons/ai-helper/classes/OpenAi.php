<?php
namespace TrxAddons\AiHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to make queries to the OpenAI API
 */
class OpenAI extends Singleton {

	/**
	 * The coefficient to calculate the maximum number of tokens from a number of words in the text prompt
	 *
	 * @access public
	 * 
	 * @var float  The coefficient value
	 */
	var $words_to_tokens_coeff = 1.25;

	/**
	 * The coefficient to calculate the maximum number of tokens from a number of words in the html prompt
	 *
	 * @access public
	 * 
	 * @var float  The coefficient value
	 */
	var $blocks_to_tokens_coeff = 2.5;

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
				$this->api = new \Orhanerday\OpenAi\OpenAi( $token );
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
		return trx_addons_get_option( 'ai_helper_token_openai' );
	}

	/**
	 * Return a model name for the API
	 * 
	 * @access static
	 * 
	 * 
	 * @return string  Model name for the API
	 */
	static function get_model() {
		return trx_addons_get_option( 'ai_helper_model_openai', 'gpt-3.5-turbo' );
	}

	/**
	 * Return a temperature for the API
	 * 
	 * @access protected
	 * 
	 * @return float  Temperature for the API
	 */
	protected function get_temperature() {
		return trx_addons_get_option( 'ai_helper_temperature_openai', 1.0 );
	}

	/**
	 * Return a maximum number of tokens in the prompt and response for specified model
	 *
	 * @access static
	 * 
	 * @param string $model  Model name for the API
	 * 
	 * @return int  The maximum number of tokens in the prompt and response for specified model
	 */
	static function get_max_tokens( $model = '' ) {
		$max_tokens = 0;
		if ( empty( $model ) ) {
			$model = self::get_model();
		}
		if ( ! empty( $model ) ) {
			$models = Lists::get_ai_models();
			if ( ! empty( $models ) && is_array( $models ) ) {
				foreach ( $models as $k => $v ) {
					if ( $k == $model ) {
						$max_tokens = $v['max_tokens'];
						break;
					}
				}
			}
		}
		return $max_tokens;
	}

	/**
	 * Return a list of available models for the API
	 *
	 * @access public
	 */
	public function list_models( $args = array() ) {
		$args = array_merge( array(
			'token' => $this->get_token(),
		), $args );

		$response = false;

		if ( ! empty( $args['token'] ) ) {
			$api = $this->get_api( $args['token'] );
			$response = $api->listModels();
		}

		return $response;
	}

	/**
	 * Send a query to the API
	 *
	 * @access public
	 * 
	 * @param array $args  Query arguments
	 * 
	 * @return array  Response from the API
	 */
	public function query( $args = array(), $params = array() ) {
		$args = array_merge( array(
			'token' => $this->get_token(),
			'model' => $this->get_model(),
			'role' => 'gb_assistant',
			'prompt' => '',
			'system_prompt' => '',
			'temperature' => $this->get_temperature(),
			'n' => 1,
			'frequency_penalty' => 0,
			'presence_penalty' => 0,
		), $args );
		
		$args['max_tokens'] = ! empty( $args['max_tokens'] )
								? min( $args['max_tokens'], self::get_max_tokens( $args['model'] ) )
								: self::get_max_tokens( $args['model'] );

		$response = false;

		if ( ! empty( $args['token'] ) && ! empty( $args['model'] ) && ! empty( $args['prompt'] ) ) {

			$api = $this->get_api( $args['token'] );

			$chat_args = $this->prepare_args( array(
				'model' => $args['model'],
				'messages' => [
					[
						"role" => "system",
						"content" => ! empty( $args['system_prompt'] ) ? $args['system_prompt'] : $this->get_system_prompt( $args['role'] )
					],
					[
						"role" => "user",
						"content" => $args['prompt']
					],
				],
				'temperature' => (float)$args['temperature'],
				'max_tokens' => (int)$args['max_tokens'],
				'frequency_penalty' => (float)$args['frequency_penalty'],
				'presence_penalty' => (float)$args['presence_penalty'],
				'n' => (int)$args['n'],
			) );

			if ( $chat_args['max_tokens'] > 0 ) {
				$response = $api->chat( $chat_args );
				if ( is_string( $response ) ) {
					$response = trim( $response );
					if ( substr( trim( $response ), 0, 1 ) == '{' ) {
						$response = json_decode( $response, true );
						$this->logger->log( $response, 'query', $args );
					}
				} else {
					$response = false;
				}
			} else {
				$response = array(
					'error' => esc_html__( 'The number of tokens in request is over limits.', 'trx_addons' )
				);
			}
		}

		return $response;

	}


	/**
	 * Send a chat messages to the API
	 *
	 * @access public
	 * 
	 * @param array $args  Query arguments
	 * 
	 * @return array  Response from the API
	 */
	public function chat( $args = array(), $params = array() ) {
		$args = array_merge( array(
			'token' => $this->get_token(),
			'model' => $this->get_model(),
			'temperature' => $this->get_temperature(),
			'messages' => array(),
			'n' => 1,
			'frequency_penalty' => 0,
			'presence_penalty' => 0,
		), $args );

		$args['max_tokens'] = ! empty( $args['max_tokens'] )
								? min( $args['max_tokens'], self::get_max_tokens( $args['model'] ) )
								: self::get_max_tokens( $args['model'] );

		$response = false;

		if ( ! empty( $args['token'] ) && ! empty( $args['model'] ) && count( $args['messages'] ) > 1 ) {

			$api = $this->get_api( $args['token'] );

			$chat_args = $this->prepare_args( array(
				'model' => $args['model'],
				'messages' => $args['messages'],
				'temperature' => (float)$args['temperature'],
				'max_tokens' => (int)$args['max_tokens'],
				'frequency_penalty' => (float)$args['frequency_penalty'],
				'presence_penalty' => (float)$args['presence_penalty'],
				'n' => (int)$args['n'],
			) );

			if ( $chat_args['max_tokens'] > 0 ) {
				$response = $api->chat( $chat_args );
				if ( is_string( $response ) ) {
					$response = trim( $response );
					if ( substr( trim( $response ), 0, 1 ) == '{' ) {
						$response = json_decode( $response, true );
						$this->logger->log( $response, 'chat', $args );
					}
				} else {
					$response = false;
				}
			} else {
				$response = array(
					'error' => esc_html__( 'The number of tokens in request is over limits.', 'trx_addons' )
				);
			}
		}

		return $response;

	}

	/**
	 * Get the system prompt
	 *
	 * @access private
	 * 
	 * @param string $role  Role of the assistant
	 * 
	 * @return string  System prompt
	 */
	private function get_system_prompt( $role ) {
		$prompt = '';

		if ( $role == 'gb_assistant' ) {
			$prompt = __( 'You are an assistant for writing posts. Return only the result without any additional messages. Format the response with HTML tags.', 'trx_addons' );
		} else if ( $role == 'translator' ) {
			$prompt = __( 'You are translator. Translate the text to English or leave it unchanged if it is already in English. Return only the translation result without any additional messages and formatting.', 'trx_addons' );
		}

		return $prompt;
	}

	/**
	 * Prepare args for the API: limit the number of tokens
	 *
	 * @access private
	 * 
	 * @param array $args  Query arguments
	 * 
	 * @return array  Prepared query arguments
	 */
	private function prepare_args( $args = array() ) {
		if ( ! empty( $args['messages'] ) && is_array( $args['messages'] ) ) {
			$tokens_total = 0;
			foreach ( $args['messages'] as $k => $message ) {
				// Remove all HTML tags
				//$message['content'] = strip_tags( $message['content'] );
				// Remove duplicate newlines
				$message['content'] = preg_replace( '/[\\r\\n]{2,}/', "\n", $message['content'] );
				// Remove all Gutenberg block comments
				$message['content'] = preg_replace( '/<!--[^>]*-->/', '', $message['content'] );
				// Count tokens
				$tokens_total += $this->count_tokens( $message['content'] );
				// Save the message
				$args['messages'][ $k ]['content'] = $message['content'];
			}
			$args['max_tokens'] = max( 0, $args['max_tokens'] - $tokens_total );
		}
		return $args;
	}

	/**
	 * Calculate the number of tokens for the API
	 * 
	 * @access private
	 * 
	 * @param string $text  Text to calculate
	 * 
	 * @return int  Number of tokens for the API
	 */
	private function count_tokens( $text ) {
		$tokens = 0;

		// Way 1: Get number of words and multiply by coefficient		
		// $words = count( explode( ' ', $text ) );
		// $coeff = strpos( $text, '<!-- wp:' ) !== false ? $this->blocks_to_tokens_coeff : $this->words_to_tokens_coeff;
		// $tokens = round( $words * $coeff );

		// Way 2: Get number of tokens via utility function with tokenizer
		// if ( ! function_exists( 'gpt_encode' ) ) {
		// 	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/vendors/gpt3-encoder/gpt3-encoder.php';
		// }
		// $tokens = count( (array) gpt_encode( $text ) );

		// Way 3: Get number of tokens via class tokenizer (same algorithm)
		$tokens = count( (array) \Rahul900day\Gpt3Encoder\Encoder::encode( $text ) );

		return $tokens;
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
			'response_format' => 'url',
			'n' => 1,
		), $args );

		$response = false;

		if ( ! empty( $args['token'] ) && ! empty( $args['prompt'] ) ) {

			$api = $this->get_api( $args['token'] );
			unset( $args['token'] );

			$response = $api->image( $args );

			if ( is_string( $response ) ) {
				$response = trim( $response );
				if ( substr( trim( $response ), 0, 1 ) == '{' ) {
					$response = json_decode( $response, true );
					$this->logger->log( $response, 'images', $args );
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
			'image' => '',
			'size' => '1024x1024',
			'response_format' => 'url',
			'n' => 1,
		), $args );

		$response = false;

		if ( ! empty( $args['token'] ) && ! empty( $args['image'] ) ) {

			$api = $this->get_api( $args['token'] );
			unset( $args['token'] );

			// Get image content
			$args['image'] = curl_file_create( $args['image'] );

			if ( ! empty( $args['image'] ) ) {
				$response = $api->createImageVariation( $args );
				if ( is_string( $response ) ) {
					$response = trim( $response );
					if ( substr( trim( $response ), 0, 1 ) == '{' ) {
						$response = json_decode( $response, true );
						$this->logger->log( $response, 'images', $args );
					}
				} else {
					$response = false;
				}
			}
		}

		return $response;

	}

}
