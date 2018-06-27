<?php namespace Zara4\API\Communication\Authentication;

use Zara4\API\Communication\AccessToken\AccessToken;


abstract class Authenticator {

	protected $client_id;
	protected $client_secret;
	protected $scopes = array();


	public function __construct( $client_id, $client_secret ) {
		$this->client_id = $client_id;
		$this->client_secret = $client_secret;
	}


	/**
	 * Get an AccessToken for use when communicating with the Zara 4 API service.
	 *
	 * @return AccessToken
	 */
	public abstract function acquire_access_token();



		/**
		 * Add image processing to the Authenticator scope.
		 *
		 * @return $this
		 */
	public function with_image_processing() {
		array_push( $this->scopes, "image-processing" );
		return $this;
	}


	/**
	 * Add usage to the Authenticator scope.
	 *
	 * @return $this
	 */
	public function with_usage() {
		array_push( $this->scopes, "usage" );
		return $this;
	}


} 