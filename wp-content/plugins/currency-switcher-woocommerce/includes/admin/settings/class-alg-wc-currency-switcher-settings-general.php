<?php
/**
 * Currency Switcher - General Section Settings
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Tom Anbinder
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Currency_Switcher_Settings_General' ) ) :

class Alg_WC_Currency_Switcher_Settings_General extends Alg_WC_Currency_Switcher_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	public function __construct() {
		$this->id   = '';
		$this->desc = __( 'General', 'currency-switcher-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_general_settings.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_general_settings( $settings ) {
		$settings = array_merge( $settings, array(
			array(
				'title'    => __( 'Currency Switcher Plugin Options', 'currency-switcher-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_currency_switcher_plugin_options',
			),
			array(
				'title'    => __( 'WooCommerce Currency Switcher Plugin', 'currency-switcher-woocommerce' ),
				'desc'     => '<strong>' . __( 'Enable', 'currency-switcher-woocommerce' ) . '</strong>',
				'desc_tip' => __( 'Currency Switcher for WooCommerce', 'currency-switcher-woocommerce' ) . ' [v.' . get_option( 'alg_currency_switcher_version', '' ) . '].',
				'id'       => 'alg_wc_currency_switcher_enabled',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Currency Switcher on per Product Basis', 'currency-switcher-woocommerce' ),
				'desc'     => __( 'Enable', 'currency-switcher-woocommerce' ),
				'desc_tip' => __( 'This will add meta boxes in product edit.', 'currency-switcher-woocommerce' ),
				'id'       => 'alg_currency_switcher_per_product_enabled',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Revert Currency to Default on Checkout', 'currency-switcher-woocommerce' ),
				'desc'     => __( 'Enable', 'currency-switcher-woocommerce' ),
				'id'       => 'alg_currency_switcher_revert',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Switcher Placement', 'currency-switcher-woocommerce' ),
				'desc'     => __( 'Also you can use switcher widget, shortcodes [woocommerce_currency_switcher_drop_down_box], [woocommerce_currency_switcher_radio_list], [woocommerce_currency_switcher_link_list] or functions.', 'currency-switcher-woocommerce' ),
				'id'       => 'alg_currency_switcher_placement',
				'default'  => '',
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'options'  => array(
					'single_page_after_price_radio'  => __( 'Product Single Page - After Price - Radio', 'currency-switcher-woocommerce' ),
					'single_page_after_price_select' => __( 'Product Single Page - After Price - Drop Down', 'currency-switcher-woocommerce' ),
					'single_page_after_price_links'  => __( 'Product Single Page - After Price - Links', 'currency-switcher-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Rounding', 'currency-switcher-woocommerce' ),
				'desc'     => __( 'If using exchange rates, choose rounding here.', 'currency-switcher-woocommerce' ),
				'id'       => 'alg_currency_switcher_rounding',
				'default'  => 'no_round',
				'type'     => 'select',
				'options'  => array(
					'no_round'   => __( 'No rounding', 'currency-switcher-woocommerce' ),
					'round'      => __( 'Round', 'currency-switcher-woocommerce' ),
					'round_up'   => __( 'Round up', 'currency-switcher-woocommerce' ),
					'round_down' => __( 'Round down', 'currency-switcher-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Precision', 'currency-switcher-woocommerce' ),
				'desc'     => __( 'If rounding enabled, set precision here.', 'currency-switcher-woocommerce' ),
				'id'       => 'alg_currency_switcher_rounding_precision',
				'default'  => absint( get_option( 'woocommerce_price_num_decimals', 2 ) ),
				'type'     => 'number',
				'custom_attributes' => array( 'min' => 0 ),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_currency_switcher_plugin_options',
			),
		) );
		return $settings;
	}

}

endif;

return new Alg_WC_Currency_Switcher_Settings_General();
