<?php
/**
 * WooCommerce Currency Switcher Exchange Rates Crons
 *
 * The WooCommerce Currency Switcher Exchange Rates Crons class.
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Tom Anbinder
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Alg_Currency_Switcher_Exchange_Rates_Crons' ) ) :

class Alg_Currency_Switcher_Exchange_Rates_Crons {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	public function __construct() {
		$this->update_intervals  = array(
			'minutely'   => __( 'Update Every Minute', 'currency-switcher-woocommerce' ),
			'hourly'     => __( 'Update Hourly', 'currency-switcher-woocommerce' ),
			'twicedaily' => __( 'Update Twice Daily', 'currency-switcher-woocommerce' ),
			'daily'      => __( 'Update Daily', 'currency-switcher-woocommerce' ),
			'weekly'     => __( 'Update Weekly', 'currency-switcher-woocommerce' ),
		);
		add_action( 'init',                           array( $this, 'schedule_the_events' ) );
		add_action( 'admin_init',                     array( $this, 'schedule_the_events' ) );
		add_action( 'alg_update_exchange_rates_hook', array( $this, 'update_the_exchange_rates' ) );
		add_filter( 'cron_schedules',                 array( $this, 'cron_add_custom_intervals' ) );
	}

	/**
	 * On an early action hook, check if the hook is scheduled - if not, schedule it.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function schedule_the_events() {
		$selected_interval = get_option( 'alg_currency_switcher_exchange_rate_update', 'manual' );
		foreach ( $this->update_intervals as $interval => $desc ) {
			$event_hook = 'alg_update_exchange_rates_hook';
			$event_timestamp = wp_next_scheduled( $event_hook, array( $interval ) );
			if ( $selected_interval === $interval ) {
				update_option( 'alg_currency_switcher_exchange_rate_cron_time', $event_timestamp );
			}
			if ( ! $event_timestamp && $selected_interval === $interval ) {
				wp_schedule_event( time(), $selected_interval, $event_hook, array( $selected_interval ) );
			} elseif ( $event_timestamp && $selected_interval !== $interval ) {
				wp_unschedule_event( $event_timestamp, $event_hook, array( $interval ) );
			}
		}
	}

	/*
	 * get_exchange_rate.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  float rate on success, else 0
	 */
	function get_exchange_rate( $currency_from, $currency_to ) {
		$url = "http://query.yahooapis.com/v1/public/yql?q=select%20rate%2Cname%20from%20csv%20where%20url%3D'http%3A%2F%2Fdownload.finance.yahoo.com%2Fd%2Fquotes%3Fs%3D" . $currency_from . $currency_to . "%253DX%26f%3Dl1n'%20and%20columns%3D'rate%2Cname'&format=json";
		ob_start();
		$max_execution_time = ini_get( 'max_execution_time' );
		set_time_limit( 5 );
		$exchange_rate = json_decode( file_get_contents( $url ) );
		set_time_limit( $max_execution_time );
		ob_end_clean();
		return ( isset( $exchange_rate->query->results->row->rate ) ) ? floatval( $exchange_rate->query->results->row->rate ) : 0;
	}

	/**
	 * On the scheduled action hook, run a function.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function update_the_exchange_rates( $interval ) {
		if ( 'yes' === get_option( 'alg_wc_currency_switcher_enabled', 'yes' ) ) {
			if ( 'manual' != get_option( 'alg_currency_switcher_exchange_rate_update', 'manual' ) ) {
				$currencies = alg_get_function_currencies();
				$currency_from = get_option( 'woocommerce_currency' );
				foreach ( $currencies as $currency ) {
					if ( $currency_from != $currency ) {
						$the_rate = $this->get_exchange_rate( $currency_from, $currency );
						if ( 0 != $the_rate ) {
							update_option( 'alg_currency_switcher_exchange_rate_' . $currency_from . '_' . $currency, $the_rate );
						}
					}
				}
				/* todo
				if ( 'yes' === get_option( 'alg_price_by_country_price_filter_widget_support_enabled', 'no' ) ) {
					alg_update_products_price_by_country();
				}
				*/
			}
		}
	}

	/**
	 * cron_add_custom_intervals.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function cron_add_custom_intervals( $schedules ) {
		$schedules['weekly'] = array(
			'interval' => 604800,
			'display' => __( 'Once Weekly', 'currency-switcher-woocommerce' )
		);
		$schedules['minutely'] = array(
			'interval' => 60,
			'display' => __( 'Once a Minute', 'currency-switcher-woocommerce' )
		);
		return $schedules;
	}
}

endif;

return new Alg_Currency_Switcher_Exchange_Rates_Crons();
