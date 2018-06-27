<?php
/**
 * Currency Switcher Plugin
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Tom Anbinder
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Currency_Switcher_Main' ) ) :

class Alg_WC_Currency_Switcher_Main {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {

		if ( 'yes' === get_option( 'alg_wc_currency_switcher_enabled', 'yes' ) ) {
			if ( ! session_id() ) {
				session_start();
			}
			if ( isset( $_REQUEST['alg_currency'] ) ) {
				$_SESSION['alg_currency'] = $_REQUEST['alg_currency'];
			}
			if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				// Prices
				add_filter( 'woocommerce_get_price',                      array( $this, 'change_price_by_currency' ), PHP_INT_MAX, 2 );
				add_filter( 'woocommerce_get_sale_price',                 array( $this, 'change_price_by_currency' ), PHP_INT_MAX, 2 );
				add_filter( 'woocommerce_get_regular_price',              array( $this, 'change_price_by_currency' ), PHP_INT_MAX, 2 );
				// Currency
				add_filter( 'woocommerce_currency',                       array( $this, 'change_currency_code' ),   PHP_INT_MAX, 1 );
				// Shipping
				add_filter( 'woocommerce_package_rates',                  array( $this, 'change_shipping_price_by_currency' ), PHP_INT_MAX, 2 );
				// Variations
				add_filter( 'woocommerce_variation_prices_price',         array( $this, 'change_price_by_currency' ), PHP_INT_MAX, 2 );
				add_filter( 'woocommerce_variation_prices_regular_price', array( $this, 'change_price_by_currency' ), PHP_INT_MAX, 2 );
				add_filter( 'woocommerce_variation_prices_sale_price',    array( $this, 'change_price_by_currency' ), PHP_INT_MAX, 2 );
				add_filter( 'woocommerce_get_variation_prices_hash',      array( $this, 'get_variation_prices_hash' ), PHP_INT_MAX, 3 );
				// Grouped products
				add_filter( 'woocommerce_get_price_including_tax',        array( $this, 'change_price_by_currency_grouped' ), PHP_INT_MAX, 3 );
				add_filter( 'woocommerce_get_price_excluding_tax',        array( $this, 'change_price_by_currency_grouped' ), PHP_INT_MAX, 3 );
				// Switcher
				$placements = get_option( 'alg_currency_switcher_placement', '' );
				if ( ! empty( $placements ) ) {
					foreach ( $placements as $placement ) {
						switch ( $placement ) {
							case 'single_page_after_price_radio':
								add_action( 'woocommerce_single_product_summary', array( $this, 'output_switcher_radio' ), 15 );
								break;
							case 'single_page_after_price_select':
								add_action( 'woocommerce_single_product_summary', array( $this, 'output_switcher_select' ), 15 );
								break;
							case 'single_page_after_price_links':
								add_action( 'woocommerce_single_product_summary', array( $this, 'output_switcher_links' ), 15 );
								break;
						}
					}
				}
			}
		}
	}

	/**
	 * output_switcher_radio.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function output_switcher_radio() {
		echo alg_currency_select_radio_list();
	}

	/**
	 * output_switcher_select.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function output_switcher_select() {
		echo alg_currency_select_drop_down_list();
	}

	/**
	 * output_switcher_links.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function output_switcher_links() {
		echo alg_currency_select_link_list();
	}

	/**
	 * change_price_by_currency_grouped.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function change_price_by_currency_grouped( $price, $qty, $_product ) {
		if ( $_product->is_type( 'grouped' ) ) {
			if ( 'yes' === get_option( 'alg_currency_switcher_per_product_enabled' , 'yes' ) ) {
				$get_price_method = 'get_price_' . get_option( 'woocommerce_tax_display_shop' ) . 'uding_tax';
				foreach ( $_product->get_children() as $child_id ) {
					$the_price = get_post_meta( $child_id, '_price', true );
					$the_product = wc_get_product( $child_id );
					$the_price = $the_product->$get_price_method( 1, $the_price );
					if ( $the_price == $price ) {
						return $this->change_price_by_currency( $price, $the_product );
					}
				}
			} else {
				return $this->change_price_by_currency( $price, null );
			}
		}
		return $price;
	}

	/**
	 * get_variation_prices_hash.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_variation_prices_hash( $price_hash, $_product, $display ) {
		$currency_code = $this->get_current_currency_code();
		$currency_exchange_rate = $this->get_currency_exchange_rate( $currency_code );
		$price_hash['alg_currency_switcher_data'] = array(
			$currency_code,
			$currency_exchange_rate,
			get_option( 'alg_currency_switcher_per_product_enabled', 'yes' ),
		);
		return $price_hash;
	}

	/**
	 * get_current_currency_code.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_current_currency_code( $default_currency = '' ) {
		return ( isset( $_SESSION['alg_currency'] ) ) ? $_SESSION['alg_currency'] : $default_currency;
	}

	/**
	 * get_currency_exchange_rate.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_currency_exchange_rate( $currency_code ) {
		$currency_from = get_option( 'woocommerce_currency' );
		return ( $currency_from == $currency_code ) ? 1 : get_option( 'alg_currency_switcher_exchange_rate_' . $currency_from . '_' . $currency_code, 1 );
	}

	/**
	 * do_revert.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function do_revert() {
		return ( 'yes' === get_option( 'alg_currency_switcher_revert', 'no' ) && is_checkout() );
	}

	/**
	 * change_shipping_price_by_currency.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function change_shipping_price_by_currency( $package_rates, $package ) {
		if ( $this->do_revert() ) {
			return $package_rates;
		}
		$currency_exchange_rate = $this->get_currency_exchange_rate( $this->get_current_currency_code() );
		$modified_package_rates = array();
		foreach ( $package_rates as $id => $package_rate ) {
			if ( 1 != $currency_exchange_rate && isset( $package_rate->cost ) ) {
				$package_rate->cost = $package_rate->cost * $currency_exchange_rate;
				if ( isset( $package_rate->taxes ) && ! empty( $package_rate->taxes ) ) {
					foreach ( $package_rate->taxes as $tax_id => $tax ) {
						$package_rate->taxes[ $tax_id ] = $package_rate->taxes[ $tax_id ] * $currency_exchange_rate;
					}
				}
			}
			$modified_package_rates[ $id ] = $package_rate;
		}
		return $modified_package_rates;
	}

	/**
	 * change_price_by_currency.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function change_price_by_currency( $price, $_product ) {

		if ( '' === $price ) {
			return $price;
		}

		if ( $this->do_revert() ) {
			return $price;
		}

		// Per product
		if ( 'yes' === get_option( 'alg_currency_switcher_per_product_enabled' , 'yes' ) ) {
//			if ( 'yes' === get_post_meta( $_product->id, '_' . 'alg_currency_switcher_per_product_settings_enabled', true ) ) {
				$the_product_id = ( isset( $_product->variation_id ) ) ? $_product->variation_id : $_product->id;
				if ( '' != ( $regular_price_per_product = get_post_meta( $the_product_id, '_' . 'alg_currency_switcher_per_product_regular_price_' . $this->get_current_currency_code(), true ) ) ) {
					$the_current_filter = current_filter();
					if ( 'woocommerce_get_price_including_tax' == $the_current_filter || 'woocommerce_get_price_excluding_tax' == $the_current_filter ) {
						$get_price_method = 'get_price_' . get_option( 'woocommerce_tax_display_shop' ) . 'uding_tax';
						return $_product->$get_price_method();
					} elseif ( 'woocommerce_get_price' == $the_current_filter || 'woocommerce_variation_prices_price' == $the_current_filter ) {
						$sale_price_per_product = get_post_meta( $the_product_id, '_' . 'alg_currency_switcher_per_product_sale_price_' . $this->get_current_currency_code(), true );
						return ( '' != $sale_price_per_product && $sale_price_per_product < $regular_price_per_product ) ? $sale_price_per_product : $regular_price_per_product;

					} elseif ( 'woocommerce_get_regular_price' == $the_current_filter || 'woocommerce_variation_prices_regular_price' == $the_current_filter ) {
						return $regular_price_per_product;

					} elseif ( 'woocommerce_get_sale_price' == $the_current_filter || 'woocommerce_variation_prices_sale_price' == $the_current_filter ) {
						$sale_price_per_product = get_post_meta( $the_product_id, '_' . 'alg_currency_switcher_per_product_sale_price_' . $this->get_current_currency_code(), true );
						return ( '' != $sale_price_per_product ) ? $sale_price_per_product : $price;
					}
				}
//			}
		}

		// Global
		if ( 1 != ( $currency_exchange_rate = $this->get_currency_exchange_rate( $this->get_current_currency_code() ) ) ) {
			$price = $price * $currency_exchange_rate;
			switch ( get_option( 'alg_currency_switcher_rounding', 'no_round' ) ) {
				case 'round':
					$price = round( $price, get_option( 'alg_currency_switcher_rounding_precision', absint( get_option( 'woocommerce_price_num_decimals', 2 ) ) ) );
					break;
				case 'round_up':
					$price = ceil( $price );
					break;
				case 'round_down':
					$price = floor( $price );
					break;
			}
			return $price;
		}

		// No changes
		return $price;
	}

	/**
	 * change_currency_code.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function change_currency_code( $currency ) {
		return ( $this->do_revert() ) ? $currency : $this->get_current_currency_code( $currency );
	}

}

endif;

return new Alg_WC_Currency_Switcher_Main();
