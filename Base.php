<?php

namespace App\OKCoin;

class Base {
	const API_BASE = '/api/v1/';
	
	const WEB_BASE = 'https://www.okcoin.com/';//OKCoin国际站

	//const WEB_BASE = 'https://www.okcoin.com/';//OKCoinz中国站
	
	private $_rpc;
	private $_authentication;

	// This constructor is deprecated.
	public function __construct($authentication, $tokens = null, $apiKeySecret = null) {
		// First off, check for a legit authentication class type
		if (is_a($authentication, 'App\OKCoin\Authentication')) {
			$this -> _authentication = $authentication;
		} else {
			// Here, $authentication was not a valid authentication object, so
			// analyze the constructor parameters and return the correct object.
			// This should be considered deprecated, but it's here for backward compatibility.
			// In older versions of this library, the first parameter of this constructor
			// can be either an API key string or an OAuth object.
			if ($tokens !== null) {
				$this -> _authentication = new OAuthAuthentication($authentication, $tokens);
			} else if ($authentication !== null && is_string($authentication)) {
				$apiKey = $authentication;
				if ($apiKeySecret === null) {
					// Simple API key
					$this -> _authentication = new SimpleApiKeyAuthentication($apiKey);
				} else {
					$this -> _authentication = new ApiKeyAuthentication($apiKey, $apiKeySecret);
				}
			} else {
				throw new Exception('Could not determine API authentication scheme');
			}
		}

		$this -> _rpc = new Rpc(new Requestor(), $this -> _authentication);
	}

	// Used for unit testing only
	public function setRequestor($requestor) {
		$this -> _rpc = new Rpc($requestor, $this -> _authentication);
		return $this;
	}

	public function get($path, $params = array()) {
		return $this -> _rpc -> request("GET", $path, $params);
	}

	public function post($path, $params = array()) {
		return $this -> _rpc -> request("POST", $path, $params);
	}

	public function delete($path, $params = array()) {
		return $this -> _rpc -> request("DELETE", $path, $params);
	}

	public function put($path, $params = array()) {
		return $this -> _rpc -> request("PUT", $path, $params);
	}

}
