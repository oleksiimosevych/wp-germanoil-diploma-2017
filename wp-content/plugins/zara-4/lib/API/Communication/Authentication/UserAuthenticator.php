<?php namespace Zara4\API\Communication\Authentication;

use Zara4\API\Communication\AccessToken\AccessToken;
use Zara4\API\Communication\AccessToken\RefreshableAccessToken;
use Zara4\API\Communication\Grant\PasswordGrant;
use Zara4\API\Communication\Util;


class UserAuthenticator extends Authenticator {

	private $username;
	private $password;


	public function __construct( $client_id, $client_secret, $username, $password ) {
		parent::__construct( $client_id, $client_secret );
		$this->username = $username;
		$this->password = $password;
	}


	/**
	 * Get an AccessToken for use when communicating with the Zara 4 API service.
	 *
	 * @return AccessToken
	 */
	public function acquire_access_token() {
		$grant = new PasswordGrant( $this->client_id, $this->client_secret, $this->username, $this->password, $this->scopes );
		$tokens = $grant->getTokens();

		$accessToken = $tokens->{"access_token"};
		$refreshToken = $tokens->{"refresh_token"};
		$expiresAt = Util::calculate_expiry_time( $tokens->{"expires_in"} );

		return new RefreshableAccessToken( $this->client_id, $this->client_secret, $accessToken, $expiresAt, $refreshToken );
	}

}