<?php namespace Zara4\API\Communication\AccessToken;

use Zara4\API\Communication\Grant\RefreshTokenGrant;
use Zara4\API\Communication\Util;


class RefreshableAccessToken extends AccessToken {

	protected $refresh_token;


	public function __construct( $client_id, $client_secret, $access_token, $expires_at, $refresh_token ) {
		parent::__construct( $client_id, $client_secret, $access_token, $expires_at );
		$this->refresh_token = $refresh_token;
	}


	/**
	 * Refresh this AccessToken
	 */
	public function refresh() {
		$grant = new RefreshTokenGrant( $this->client_id, $this->client_secret, $this->refresh_token );
		$tokens = $grant->getTokens();

		$this->access_token = $tokens->{"access_token"};
		$this->expires_at = Util::calculate_expiry_time( $tokens->{"expires_in"} );
		$this->refresh_token = $tokens->{"refresh_token"};
	}

} 