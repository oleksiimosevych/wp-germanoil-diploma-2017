<?php

class Api {

	const BASE_URL = "http://dev.zara4.com";


	/**
	 *
	 *
	 * @param $clientId
	 * @param $clientSecret
	 * @return string
	 */
	public static function access_token( $clientId, $clientSecret ) {

		$url = self::BASE_URL . "/oauth/access_token";

		$data = array(
			"grant_type"    => "client_credentials",
			"client_id"     => $clientId,
			"client_secret" => $clientSecret,
		);

		return json_decode( ApiUtils::post( $url, $data ) );
	}


} 