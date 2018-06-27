<?php namespace Zara4\API\Communication\AccessToken;

use Zara4\API\Communication\Grant\ClientCredentialsGrantRequest;
use Zara4\API\Communication\Util;


class ReissuableAccessToken extends AccessToken {

	private $scopes = array();


	public function __construct( $client_id, $client_secret, $access_token, $expires_at, array $scopes = array() ) {
		parent::__construct( $client_id, $client_secret, $access_token, $expires_at );
		$this->scopes = $scopes;
	}


	/**
	 * Refresh this AccessToken
	 */
	public function refresh() {
		$grant = new ClientCredentialsGrantRequest( $this->client_id, $this->client_secret, $this->scopes );
		$tokens = $grant->getTokens();

		$this->access_token = $tokens->{"access_token"};
		$this->expires_at = Util::calculate_expiry_time( $tokens->{"expires_in"} );
	}

} 