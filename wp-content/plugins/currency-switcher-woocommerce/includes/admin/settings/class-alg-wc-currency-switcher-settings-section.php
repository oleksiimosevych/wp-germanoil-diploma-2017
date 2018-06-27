<?php
/**
 * Currency Switcher - Section Settings
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Tom Anbinder
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Currency_Switcher_Settings_Section' ) ) :

class Alg_WC_Currency_Switcher_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	public function __construct() {
		add_filter( 'woocommerce_get_sections_alg_wc_currency_switcher',              array( $this, 'settings_section' ) );
		add_filter( 'woocommerce_get_settings_alg_wc_currency_switcher_' . $this->id, array( $this, 'get_settings' ), PHP_INT_MAX );
		add_action( 'init',                                                           array( $this, 'add_settings_hooks' ) );
	}

	/**
	 * get_settings.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_settings() {
		$the_id = ( '' == $this->id ) ? 'general' : $this->id;
		return apply_filters( 'alg_currency_switcher_' . $the_id . '_settings', array() );
	}

	/**
	 * add_settings_hooks.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function add_settings_hooks() {
		$the_id = ( '' == $this->id ) ? 'general' : $this->id;
		add_filter( 'alg_currency_switcher_' . $the_id . '_settings', array( $this, 'get_' . $the_id . '_settings' ) );
	}

	/**
	 * settings_section.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function settings_section( $sections ) {
		$sections[ $this->id ] = $this->desc;
		return $sections;
	}

}

endif;
