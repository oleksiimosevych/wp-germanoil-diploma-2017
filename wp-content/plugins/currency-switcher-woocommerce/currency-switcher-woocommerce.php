<?php
/*
Plugin Name: Currency Switcher for WooCommerce
Plugin URI: http://coder.fm/item/currency-switcher-woocommerce-plugin/
Description: Currency Switcher for WooCommerce.
Version: 1.0.1
Author: Tom Anbinder
Author URI: http://www.algoritmika.com
Text Domain: currency-switcher-woocommerce
Domain Path: /langs
Copyright: © 2016 Tom Anbinder
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Check if WooCommerce is active
$plugin = 'woocommerce/woocommerce.php';
if (
	! in_array( $plugin, apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) ) ) &&
	! ( is_multisite() && array_key_exists( $plugin, get_site_option( 'active_sitewide_plugins', array() ) ) )
) return;

// Check if Pro is active, if so then return
$plugin = 'currency-switcher-woocommerce-pro/currency-switcher-woocommerce-pro.php';
if (
	in_array( $plugin, apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) ) ) ||
	( is_multisite() && array_key_exists( $plugin, get_site_option( 'active_sitewide_plugins', array() ) ) )
) return;

if ( ! class_exists( 'Alg_WC_Currency_Switcher' ) ) :

/**
 * Main Alg_WC_Currency_Switcher Class
 *
 * @class   Alg_WC_Currency_Switcher
 * @version 1.0.0
 * @since   1.0.0
 */

final class Alg_WC_Currency_Switcher {

	/**
	 * Currency Switcher plugin version.
	 *
	 * @var   string
	 * @since 1.0.0
	 */
	public $version = '1.0.1';

	/**
	 * @var   Alg_WC_Currency_Switcher The single instance of the class
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main Alg_WC_Currency_Switcher Instance
	 *
	 * Ensures only one instance of Alg_WC_Currency_Switcher is loaded or can be loaded.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @static
	 * @return Alg_WC_Currency_Switcher - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Alg_WC_Currency_Switcher Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @access  public
	 */
	public function __construct() {

		add_filter( 'alg_wc_currency_switcher_plugin_option', array( $this, 'currency_switcher_plugin_option' ) );

		// Include required files
		$this->includes();

		add_action( 'init', array( $this, 'init' ), 0 );

		// Settings & Scripts
		if ( is_admin() ) {
			add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_woocommerce_settings_tab' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );

			/*  // todo
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_script' ) );
			add_action( 'wp_ajax_alg_admin',     array( $this, 'alg_admin_ajax' ) );
			*/
		}
	}

	/**
	 * enqueue_admin_script.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	/* function enqueue_admin_script( $hook ) {
		/* if ( 'edit.php' != $hook ) {
			return;
		} *//*
		wp_enqueue_script(  'alg_admin_script', plugin_dir_url( __FILE__ ) . 'includes/admin/admin.js' );
		wp_localize_script( 'alg_admin_script', 'ajax_object', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'currencies_select_options' => '<option value="AED">United Arab Emirates dirham</option><option value="AFN">Afghan afghani</option>',
		) );
	}* /

	/**
	 * currency_switcher_plugin_option.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	public function currency_switcher_plugin_option( $option ) {
		return $option;
	}

	/**
	 * Show action links on the plugin screen
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @param   mixed $links
	 * @return  array
	 */
	public function action_links( $links ) {
		$settings_link   = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_currency_switcher' ) . '">' . __( 'Settings', 'woocommerce' )   . '</a>';
		$unlock_all_link = '<a target="_blank" href="' . esc_url( 'http://coder.fm/item/currency-switcher-woocommerce-plugin/' ) . '">' . __( 'Unlock all', 'woocommerce' ) . '</a>';
		$custom_links    = ( PHP_INT_MAX === apply_filters( 'alg_wc_currency_switcher_plugin_option', 1 ) ) ? array( $settings_link ) : array( $settings_link, $unlock_all_link );
		return array_merge( $custom_links, $links );

	}

	/**
	 * Include required core files used in admin and on the frontend.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	private function includes() {

		require_once( 'includes/admin/settings/class-alg-wc-currency-switcher-settings-section.php' );
		$settings = array();
		$settings[] = require_once( 'includes/admin/settings/class-alg-wc-currency-switcher-settings-general.php' );
		$settings[] = require_once( 'includes/admin/settings/class-alg-wc-currency-switcher-settings-currencies.php' );
		$settings[] = require_once( 'includes/admin/settings/class-alg-wc-currency-switcher-settings-exchange-rates.php' );
		if ( is_admin() && get_option( 'alg_currency_switcher_version', '' ) !== $this->version ) {
			foreach ( $settings as $section ) {
				foreach ( $section->get_settings() as $value ) {
					if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
						/* if ( isset ( $_GET['alg_wc_currency_switcher_plugin_admin_options_reset'] ) ) { // todo
							require_once( ABSPATH . 'wp-includes/pluggable.php' );
							if ( is_super_admin() ) {
								delete_option( $value['id'] );
							}
						} */
						$autoload = isset( $value['autoload'] ) ? ( bool ) $value['autoload'] : true;
						add_option( $value['id'], $value['default'], '', ( $autoload ? 'yes' : 'no' ) );
					}
				}
			}
			update_option( 'alg_currency_switcher_version', $this->version );
		}

		if ( 'yes' === get_option( 'alg_currency_switcher_per_product_enabled', 'yes' ) ) {
			require_once( 'includes/admin/class-alg-wc-currency-switcher-per-product.php' );
		}

		if ( 'yes' === get_option( 'alg_wc_currency_switcher_enabled', 'yes' ) ) {
			if ( 'manual' != get_option( 'alg_currency_switcher_exchange_rate_update', 'manual' ) ) {
				require_once( 'includes/class-alg-exchange-rates-crons.php' );
			}
			if ( is_admin() ) {
				require_once( 'includes/admin/class-alg-currency-reports.php' );
			}
		}

//		require_once( 'includes/functions/alg-price-functions.php' );
		require_once( 'includes/functions/alg-switcher-functions.php' );
		require_once( 'includes/class-alg-wc-currency-switcher.php' );
		require_once( 'includes/class-alg-widget-currency-switcher.php' );
	}

	/**
	 * Add Currency Switcher Plugin settings tab to WooCommerce settings.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	public function add_woocommerce_settings_tab( $settings ) {
		$settings[] = include( 'includes/admin/settings/class-wc-settings-currency-switcher.php' );
		return $settings;
	}

	/**
	 * Init Alg_WC_Currency_Switcher when WordPress initialises.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	public function init() {
		// Set up localisation
		load_plugin_textdomain( 'currency-switcher-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/langs/' );
	}

	/**
	 * Get the plugin url.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  string
	 */
	public function plugin_url() {
		return untrailingslashit( plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

}

endif;

/**
 * Returns the main instance of Alg_WC_Currency_Switcher to prevent the need to use globals.
 *
 * @version 1.0.0
 * @since   1.0.0
 * @return  Alg_WC_Currency_Switcher
 */
if ( ! function_exists( 'alg_wc_currency_switcher_plugin' ) ) {
	function alg_wc_currency_switcher_plugin() {
		return Alg_WC_Currency_Switcher::instance();
	}
}

alg_wc_currency_switcher_plugin();
