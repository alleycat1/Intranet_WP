<?php

namespace StableDiffusion\Api;

use Exception;

class Images {
	private string $api_key = "";
	private int $timeout = 0;
	private string $scheduler = "DDPMScheduler";

	public function __construct( $api_key )	{
		$this->api_key = $api_key;
	}

	/**
	 * Return an URL to the API
	 * 
	 * @return string  The URL to the API
	 */
	public function apiUrl( $endpoint ) {
		return "https://stablediffusionapi.com/api/{$endpoint}/";
	}

	private function checkArgs( $args ) {
		if ( ! empty( $args['model_id'] ) && empty( $args['scheduler'] ) ) {
			$args['scheduler'] = $this->scheduler;
		}
		return apply_filters( 'trx_addons_filter_ai_helper_check_args', $args, 'stable-diffusion' );
	}

	/**
	 * Generate an image from a text prompt
	 * 
	 * @param array $opts  The options for the request
	 * 
	 * @return bool|string  The response from the API
	 */
	public function textToImage( $opts ) {
		$url = $this->apiUrl( ! empty( $opts['model_id'] ) ? 'v4/dreambooth' : 'v3/text2img' );
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
		$url = $this->apiUrl( ! empty( $opts['model_id'] ) ? 'v4/dreambooth/img2img' : 'v3/img2img' );
		return $this->sendRequest( $url, 'POST', $this->checkArgs( $opts ) );
	}

	/**
	 * Edit an image with a mask and another image
	 * 
	 * @param array $opts  The options for the request
	 * 
	 * @return bool|string  The response from the API
	 */
	public function imageInpaint( $opts ) {
		$url = $this->apiUrl( ! empty( $opts['model_id'] ) ? 'v4/dreambooth/inpaint' : 'v3/inpaint' );
		return $this->sendRequest( $url, 'POST', $this->checkArgs( $opts ) );
	}

	/**
	 * Fetch queued images
	 * 
	 * @param array $opts  The options for the request
	 * 
	 * @return bool|string  The response from the API
	 */
	public function fetchImages( $opts ) {
		$url = $this->apiUrl( 'v3/fetch/' . $opts['fetch_id'] );
		unset( $opts['fetch_id'] );
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
			CURLOPT_CUSTOMREQUEST  => $method,
			CURLOPT_POSTFIELDS     => json_encode( $opts ),
			CURLOPT_HTTPHEADER     => array(
											'Content-Type: application/json'
										),
		);

		$curl = curl_init();

		curl_setopt_array($curl, $curl_info);
		$response = curl_exec($curl);

		curl_close($curl);

		return $response;
	}

}
