<?php namespace Zara4\API\Communication\Grant;


class PasswordGrant extends GrantRequest {

	protected $grantType = "password";
	protected $username;
	protected $password;


	public function __construct( $client_id, $client_secret, $username, $password, $scopes = array() ) {
		$this->username = $username;
		$this->password = $password;
		parent::__construct( $client_id, $client_secret, $scopes );
	}


	protected function data() {
		return array_merge( parent::data(), array(
			"username" => $this->username,
			"password" => $this->password,
		) );
	}


} 