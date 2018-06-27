<?php namespace Zara4\API\ImageProcessing;

use Zara4\API\Communication\Util;


class Image {


	private static function optimise_image( $data ) {
		$url = Util::url( '/v1/image-processing/request' );
    return  Util::post( $url, $data );
	}


	/**
	 * Optimise the image at the given file path.
	 *
	 * @param $file_path
	 * @param array $params
	 * @return array
	 */
	public static function optimise_image_from_file( $file_path, array $params = array() ) {

		//
		// Attach file
		//   - As of 5.5.0  -> @ is depreciated, now use curl_file_create
		//   - Before       -> prefix file full path with @
		//
		if ( function_exists( 'curl_file_create' ) ) {
			$params['file'] = curl_file_create( $file_path );
		} else {
			$params['file'] = '@' . realpath( $file_path ) . ';filename=' . basename( $file_path );
		}


		return self::optimise_image( $params );
	}


}