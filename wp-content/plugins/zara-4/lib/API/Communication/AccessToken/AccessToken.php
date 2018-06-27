<?php namespace Zara4\API\Communication\AccessToken;


abstract class AccessToken {

	protected $client_id;
	protected $client_secret;
	protected $access_token;
	protected $expires_at;


	public function __construct( $client_id, $client_secret, $access_token, $expires_at ) {
		$this->client_id = $client_id;
		$this->client_secret = $client_secret;
		$this->access_token = $access_token;
		$this->expires_at = $expires_at;
	}


	/**
	 * Get the token.
	 *
	 * @return String
	 */
	public function token() {
		if ( $this->has_expired() ) {
			$this->refresh();
		}
		return $this->access_token;
	}


	/**
	 * Represent this AccessToken as a String.
	 *
	 * @return String
	 */
	public function __toString() {
		return $this->token();
	}


	/**
	 * Refresh this AccessToken.
	 *
	 * @return void
	 */
	public abstract function refresh();


	/**
	 * Has this AccessToken expired?
	 *
	 * @return bool
	 */
	public function has_expired() {
		return time() > $this->expires_at;
	}


} 