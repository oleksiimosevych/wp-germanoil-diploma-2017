<?php namespace Zara4\API\Communication\Grant;


class RefreshTokenGrant extends GrantRequest {

	protected $grantType = "refresh_token";
	protected $refreshToken;


	public function __construct( $client_id, $client_secret, $refreshToken, $scopes = array() ) {
		$this->refreshToken = $refreshToken;
		parent::__construct( $client_id, $client_secret, $scopes );
	}


	protected function data() {
		return array_merge( parent::data(), array(
			"refresh_token" => $this->refreshToken,
		) );
	}

} 