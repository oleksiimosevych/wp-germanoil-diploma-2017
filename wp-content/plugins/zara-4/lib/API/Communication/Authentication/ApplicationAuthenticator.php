<?php namespace Zara4\API\Communication\Authentication;

use Zara4\API\Communication\AccessToken\AccessToken;
use Zara4\API\Communication\AccessToken\ReissuableAccessToken;
use Zara4\API\Communication\Grant\ClientCredentialsGrantRequest;
use Zara4\API\Communication\Util;


class ApplicationAuthenticator extends Authenticator {

	/**
	 * Get an AccessToken for use when communicating with the Zara 4 API service.
	 *
	 * @return AccessToken
	 */
	public function acquire_access_token() {
		$grant = new ClientCredentialsGrantRequest( $this->client_id, $this->client_secret, $this->scopes );
		$tokens = $grant->getTokens();

		$accessToken = $tokens->{"access_token"};
		$expiresAt = Util::calculate_expiry_time( $tokens->{"expires_in"} );

		return new ReissuableAccessToken( $this->client_id, $this->client_secret, $accessToken, $expiresAt, $this->scopes );
	}

}