<?php
/**
 * Switcher Functions
 *
 * Switcher Functions file.
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Tom Anbinder
 */

if ( ! function_exists( 'alg_get_function_currencies' ) ) {
	/**
	 * alg_get_function_currencies.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function alg_get_function_currencies() {
		$function_currencies = array();
		$default_currency = get_option( 'woocommerce_currency' );
		$function_currencies[] = $default_currency;
		$total_number = ( PHP_INT_MAX === apply_filters( 'alg_wc_currency_switcher_plugin_option', 1 ) ) ? get_option( 'alg_currency_switcher_total_number', 1 ) : 1;
		for ( $i = 1; $i <= $total_number; $i++ ) {
			if ( 'yes' === get_option( 'alg_currency_switcher_currency_enabled_' . $i, 'yes' ) ) {
				$function_currencies[] = get_option( 'alg_currency_switcher_currency_' . $i, $default_currency );
			}
		}
		return array_unique( $function_currencies );
	}
}

if ( ! function_exists( 'alg_get_currency_selector' ) ) {
	/**
	 * alg_get_currency_selector.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function alg_get_currency_selector( $type = 'select' ) {
		$html = '';
		$html .= '<form action="" method="post">';
		if ( 'select' === $type ) {
			$html .= '<select name="alg_currency" id="alg_currency_select" class="alg_currency_select" onchange="this.form.submit()">';
		}
		// Options
		$function_currencies = alg_get_function_currencies();
		$currencies          = get_woocommerce_currencies();
		$selected_currency   = ( isset( $_SESSION['alg_currency'] ) ) ? $_SESSION['alg_currency'] : '';
		foreach ( $function_currencies as $currency_code ) {
			if ( isset( $currencies[ $currency_code ] ) ) {
				if ( '' == $selected_currency ) {
					$selected_currency = $currency_code;
				}
				if ( 'select' === $type ) {
					$html .= '<option value="' . $currency_code . '" ' . selected( $currency_code, $selected_currency, false ) . '>' .
						$currencies[ $currency_code ] . '</option>';
				} elseif ( 'radio' === $type ) {
					$html .= '<input type="radio" id="alg_currency_' . $currency_code . '" name="alg_currency" class="alg_currency_radio" value="' . $currency_code . '" ' .
						checked( $currency_code, $selected_currency, false ) . ' onclick="this.form.submit()"> ' .
						'<label for="alg_currency_' . $currency_code . '">' . $currencies[ $currency_code ] . '</label>' . '<br>';
				}
			}
		}
		if ( 'select' === $type ) {
			$html .= '</select>';
		}
		$html .= '</form>';
		return $html;
	}
}

if ( ! function_exists( 'alg_currency_select_drop_down_list' ) ) {
	/**
	 * alg_currency_select_drop_down_list.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function alg_currency_select_drop_down_list() {
		return alg_get_currency_selector( 'select' );
	}
}

if ( ! function_exists( 'alg_currency_select_radio_list' ) ) {
	/**
	 * alg_currency_select_radio_list.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function alg_currency_select_radio_list() {
		return alg_get_currency_selector( 'radio' );
	}
}

if ( ! function_exists( 'alg_currency_select_link_list' ) ) {
	/**
	 * alg_currency_select_link_list.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function alg_currency_select_link_list() {
		$function_currencies = alg_get_function_currencies();
		$currencies          = get_woocommerce_currencies();
		$selected_currency   = ( isset( $_SESSION['alg_currency'] ) ) ? $_SESSION['alg_currency'] : '';
		$html       = '';
		$links      = array();
		$first_link = '';
		foreach ( $function_currencies as $currency_code ) {
			if ( isset( $currencies[ $currency_code ] ) ) {
				$the_link = '<a href="' . add_query_arg( 'alg_currency', $currency_code ) . '">' . $currencies[ $currency_code ] . '</a>';
				if ( $currency_code != $selected_currency ) {
					$links[] = $the_link;
				} else {
					$first_link = $the_link;
				}
			}
		}
		if ( '' != $first_link ) {
			$links = array_merge( array( $first_link ), $links );
		}
		$html .= implode( '<br>', $links );
		return $html;
	}
}

if ( ! function_exists( 'alg_currency_select' ) ) {
	/**
	 * alg_currency_select.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function alg_currency_select( $atts ) {
		if ( ! isset( $atts['type'] ) ) {
			$atts['type'] = 'drop_down';
		}
		switch ( $atts['type'] ) {
			case 'radio':
				return alg_currency_select_radio_list();
			case 'links':
				return alg_currency_select_link_list();
			default: // 'drop_down'
				return alg_currency_select_drop_down_list();
		}
	}
}

// Shortcodes
add_shortcode( 'woocommerce_currency_switcher',               'alg_currency_select' );
add_shortcode( 'woocommerce_currency_switcher_drop_down_box', 'alg_currency_select_drop_down_list' );
add_shortcode( 'woocommerce_currency_switcher_radio_list',    'alg_currency_select_radio_list' );
add_shortcode( 'woocommerce_currency_switcher_link_list',     'alg_currency_select_link_list' );
