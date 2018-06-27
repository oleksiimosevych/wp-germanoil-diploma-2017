<?php namespace Zara4\API\Communication;


class Config {

	public static function BASE_URL() {
		if ( ! ZARA4_DEV ) {
			return "https://api.zara4.com";
		} else {
			return "http://api.zara4.dev";
		}
	}

} 