<?php
  /*
   * Default Token Passport Generator - cen be replaced by similar class
   */
require_once 'NetSuiteService.php';

class MyTokenPassportGenerator implements iTokenPassportGenerator
{
    protected $account; // NS account

    protected $consumer_key;  // Consumer Key shown once on Integration detail page
    protected $consumer_secret; // Consumer Secret shown once on Integration detail page
		// following token has to be for role having those permissions: Log in using Access Tokens, Web Services
	protected $token; // Token Id shown once on Access Token detail page
	protected $token_secret; // Token Secret shown once on Access Token detail page
    
	/**
	 * Shows how to generate TokenPassport for SuiteTalk, called by PHP Toolkit before each request
	 */
	public function generateTokenPassport($account = null, $consumer_key = null, $consumer_secret = null, $token = null,  $token_secret = null) {


        if (isset($account)) {
            $this->account = $account;
        } elseif (defined("NS_ACCOUNT")) {
            $this->account = NS_ACCOUNT;
        }
        if (isset($consumer_key)) {
            $this->consumer_key = $consumer_key;
        } elseif (defined("NS_CONSUMER_KEY")) {
            $this->consumer_key = NS_CONSUMER_KEY;
        }
        if (isset($consumer_secret)) {
            $this->consumer_secret = $consumer_secret;
        } elseif (defined("NS_CONSUMER_SECRET")) {
            $this->consumer_secret = NS_CONSUMER_SECRET;
        }
        if (isset($token)) {
            $this->token = $token;
        } elseif (defined("NS_TOKEN")) {
            $this->token = NS_TOKEN;
        }
        if (isset($token_secret)) {
            $this->token_secret = $token_secret;
        } elseif (defined("NS_TOKEN_SECRET")) {
            $this->token_secret = NS_TOKEN_SECRET;
        }

		$nonce = $this->generateRandomString();// CAUTION: this sample code does not generate cryptographically secure values
		$timestamp = time();

		$baseString = urlencode($this->account) ."&". urlencode($this->consumer_key) ."&". urlencode($this->token) ."&". urlencode($nonce) ."&". urlencode($timestamp);
		$secret = urlencode($this->consumer_secret) .'&'. urlencode($this->token_secret);
		$method = 'sha256'; 
		$signature = base64_encode(hash_hmac($method, $baseString, $secret, true));
		
		$tokenPassport = new TokenPassport();
		$tokenPassport->account = $this->account;
		$tokenPassport->consumerKey = $this->consumer_key;
		$tokenPassport->token = $this->token;
		$tokenPassport->nonce = $nonce;                                    
		$tokenPassport->timestamp = $timestamp; 
		$tokenPassport->signature = new TokenPassportSignature();
		$tokenPassport->signature->_ = $signature;
		$tokenPassport->signature->algorithm = "HMAC-SHA256";  
		
		return $tokenPassport;
	}

	// CAUTION: it does not generate cryptographically secure values
	private function generateRandomString() {
		$length = 20;
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)]; // CAUTION: The rand function does not generate cryptographically secure values
			// Since PHP 7 the cryptographically secure random_int can be used
		}
		return $randomString;
	}

}

?> 