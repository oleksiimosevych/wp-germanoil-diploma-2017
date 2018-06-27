<?php
/**
 * Currency Switcher - Settings
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Tom Anbinder
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Settings_Currency_Switcher' ) ) :

class Alg_WC_Settings_Currency_Switcher extends WC_Settings_Page {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id    = 'alg_wc_currency_switcher';
		$this->label = __( 'Currency Switcher', 'currency-switcher-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	public function get_settings() {
		global $current_section;
		return apply_filters( 'woocommerce_get_settings_' . $this->id . '_' . $current_section, array() );
	}

}

endif;

return new Alg_WC_Settings_Currency_Switcher();
