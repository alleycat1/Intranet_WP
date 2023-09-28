<?php

class Encryption
{

	protected $encryptMethod = 'AES-256-CBC';
	/**
     * Decrypt string.
     */
	public function decrypt($encryptedString, $key)
	{

		
		$json = json_decode(base64_decode($encryptedString), true);
		
		try {
			$salt = hex2bin($json["salt"]);
			$iv = hex2bin($json["iv"]);
		} catch (Exception $e) {
			return null;
		}

		$cipherText = base64_decode($json['ciphertext']);
		
		$iterations = intval(abs($json['iterations']));
		if ($iterations <= 0) {
			$iterations = 999;
		}

		$hashKey = hash_pbkdf2('sha512', $key, $salt, $iterations, ($this->encryptMethodLength() / 4));	
		
		unset($iterations, $json, $salt);	

		$decrypted= openssl_decrypt($cipherText , $this->encryptMethod, hex2bin($hashKey), OPENSSL_RAW_DATA, $iv);
		unset($cipherText, $hashKey, $iv);
		

		return $decrypted;
	}// decrypt


	/**
     * Encrypt string.
     */
	public function encrypt($string, $key)
	{
		$ivLength = openssl_cipher_iv_length($this->encryptMethod);
		$iv = openssl_random_pseudo_bytes($ivLength);

		$salt = openssl_random_pseudo_bytes(256);
		$iterations = 999;
		$hashKey = hash_pbkdf2('sha512', $key, $salt, $iterations, ($this->encryptMethodLength() / 4));

		$encryptedString = openssl_encrypt($string, $this->encryptMethod, hex2bin($hashKey), OPENSSL_RAW_DATA, $iv);

		$encryptedString = base64_encode($encryptedString);
		unset($hashKey);

		$output = ['ciphertext' => $encryptedString, 'iv' => bin2hex($iv), 'salt' => bin2hex($salt), 'iterations' => $iterations];
		unset($encryptedString, $iterations, $iv, $ivLength, $salt);

		return base64_encode(json_encode($output));
	}// encrypt


	/**
     * Get encrypt method length number (128, 192, 256).
     * 
     * @return integer.
     */
	protected function encryptMethodLength()
	{
	
		$number = filter_var($this->encryptMethod, FILTER_SANITIZE_NUMBER_INT);
		$number = intval($number);
		if($number < 0){
			$number = $number  * -1;
		}
		return $number;
	}// encryptMethodLength


	/**
     * Set encryption method.
     */
	public function setCipherMethod($cipherMethod)
	{
		$this->encryptMethod = $cipherMethod;
	}// setCipherMethod

}