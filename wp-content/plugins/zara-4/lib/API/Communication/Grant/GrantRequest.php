<?php namespace Zara4\API\Communication\Grant;

use Zara4\API\Communication\Util;


abstract class GrantRequest {

	protected $grantType;
	protected $scopes;
	protected $client_id;
	protected $client_secret;


	public function __construct( $client_id, $client_secret, $scopes = array() ) {
		$this->client_id = $client_id;
		$this->client_secret = $client_secret;
		$this->scopes = $scopes;
	}


	/**
	 * @return array
	 */
	public function getTokens() {
		return Util::post(
			Util::url( "/oauth/access_token" ),
			array( "body" => $this->data() )
		);
	}



	protected function data() {
		return array(
			"grant_type"    => $this->grantType,
			"client_id"     => $this->client_id,
			"client_secret" => $this->client_secret,
			"scope"         => implode( ",", array_unique( $this->scopes ) ),
		);
	}



	/**
	 * Add image processing to the request scope.
	 *
	 * @return $this
	 */
	public function withImageProcessing() {
		array_push( $this->scopes, "image-processing" );
		return $this;
	}


	/**
	 * Add usage to the request scope.
	 *
	 * @return $this
	 */
	public function withUsage() {
		array_push( $this->scopes, "usage" );
		return $this;
	}

} 