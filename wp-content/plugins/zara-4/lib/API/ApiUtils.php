<?php

class ApiUtils {


	/**
	 *
	 *
	 * @param $url
	 * @param array $data
	 * @return string
	 */
	public static function get( $url, array $data ) {
		$options = array(
			"http" => array(
				"method"  => "GET",
				"content" => http_build_query( $data ),
			),
		);
		$context = stream_context_create( $options );
		return file_get_contents( $url, false, $context );
	}


	/**
	 *
	 *
	 * @param $url
	 * @param array $data
	 * @return string
	 */
	public static function post( $url, array $data ) {
		$options = array(
			"http" => array(
				"header"  => "Content-type: application/x-www-form-urlencoded\r\n",
				"method"  => "POST",
				"content" => http_build_query( $data ),
			),
		);
		$context = stream_context_create( $options );
		return file_get_contents( $url, false, $context );
	}


} 