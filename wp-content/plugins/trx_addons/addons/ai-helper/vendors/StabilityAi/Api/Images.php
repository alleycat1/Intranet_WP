<?php

namespace StabilityAi\Api;

use Exception;

class Images {
	private string $api_key = "";
	private int $timeout = 0;

	public function __construct( $api_key )	{
		$this->api_key = $api_key;
	}

	/**
	 * Return an URL to the API
	 * 
	 * @param string $engine    The engine to use. For image generation, use the model ID.
	 * @param string $endpoint  The endpoint to use: text-to-image, image-to-image
	 * 
	 * @return string  The URL to the API
	 */
	public function apiUrl( $engine, $endpoint ) {
		return "https://api.stability.ai/v1/{$engine}/{$endpoint}";
	}

	private function checkArgs( $args ) {
		return apply_filters( 'trx_addons_filter_ai_helper_check_args', $args, 'stability-ai' );
	}

	/**
	 * Return a list of available models
	 * 
	 * @return bool|string  The response from the API
	 */
	public function listModels() {
		$url = $this->apiUrl( 'engines', 'list' );
		return $this->sendRequest( $url, 'GET', array( 'key' => $this->api_key ) );
	}

	/**
	 * Generate an image from a text prompt
	 * 
	 * @param array $opts  The options for the request
	 * 
	 * @return bool|string  The response from the API
	 */
	public function textToImage( $opts ) {
		$url = $this->apiUrl( "generation/{$opts['model_id']}", 'text-to-image' );
		return $this->sendRequest( $url, 'POST', $this->checkArgs( $opts ) );
	}

	/**
	 * Generate an image from another image
	 * 
	 * @param array $opts  The options for the request
	 * 
	 * @return bool|string  The response from the API
	 */
	public function imageToImage( $opts ) {
		$url = $this->apiUrl( "generation/{$opts['model_id']}", 'image-to-image' );
		return $this->sendRequest( $url, 'POST', $this->checkArgs( $opts ) );
	}

	/**
	* @param  string  $url
	* @param  string  $method
	* @param  array   $opts
	* @return bool|string
	*/
	private function sendRequest( string $url, string $method, array $opts = array() ) {
		if ( empty( $this->api_key ) ) {
			throw new Exception( 'API key is missing' );
		}
		if ( empty( $opts['key'] ) ) {
			$opts['key'] = $this->api_key;
		}
		$curl_info = array(
			CURLOPT_URL            => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING       => '',
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_TIMEOUT        => $this->timeout,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_SSL_VERIFYPEER => 0,
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_CUSTOMREQUEST  => $method,
			CURLOPT_HTTPHEADER     => array(
				'Content-Type: application/json',
				'Accept: application/json',
				"Authorization: Bearer {$opts['key']}",
			),
		);

		if ( $method === 'POST' ) {
			$curl_info[CURLOPT_POSTFIELDS] = json_encode( $opts );
		}

		$curl = curl_init();
		curl_setopt_array($curl, $curl_info);
		$response = curl_exec($curl);
		curl_close($curl);

		return $response;
	}

}
