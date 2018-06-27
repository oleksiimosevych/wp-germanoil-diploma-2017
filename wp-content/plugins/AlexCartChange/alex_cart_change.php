<?php

/*

Plugin Name: Alex Cart Change

Plugin URI: www.alexpluginsforwordpress.blogspot.com

Description: We can change your cart to ukrainian labels.

Author: Oleksii Mosevych

Author URI: https://plus.google.com/+alexmosevych

Text Domain: alexcartchange

Version: 4.0

*/

add_filter( 'woocommerce_product_add_to_cart_text' , 'custom_woocommerce_product_add_to_cart_text' );

/**

 * custom_woocommerce_template_loop_add_to_cart

*/

function custom_woocommerce_product_add_to_cart_text() {

	global $product;

	$product_type = $product->product_type;

	/*moja vlasna funkcija))) robit nema v najavnosti text dla vidsutnix productiv*/

	if(!$product->is_in_stock()){

			return __( 'НЕМАЄ В НАЯВНОСТІ', 'woocommerce' );	

	}

	switch ( $product_type ) {

		case 'external':

			return __( 'КУПИТИ', 'woocommerce' );

		break;

		case 'grouped':

			return __( 'ПЕРЕГЛЯНУТИ', 'woocommerce' );

		break;

		case 'simple':

			return __( 'В КОШИК', 'woocommerce' );

		break;

		case 'variable':

			return __( 'Обрати варіант', 'woocommerce' );

		break;

		/*my*/	

		default:

			return __( 'Немає в наявності', 'woocommerce' );

	}	

}
/*how much prodcts to show in search page*/
add_filter('loop_shop_per_page', create_function('$cols', 'return 20;'));

//REMOVE SUBTOTAL IN MAIL _ RAZOM
add_filter( 'woocommerce_get_order_item_totals', 'adjust_woocommerce_get_order_item_totals' );

function adjust_woocommerce_get_order_item_totals( $totals ) {
  unset($totals['cart_subtotal']);
  unset($totals['shipping']);
  unset($totals['payment_method']);
  return $totals;
}


// add_action( 'woocommerce_before_shop_loop_item_title', function() {

//  global $product; 

//  if ( !$product->is_in_stock() ) {

//  echo '<hr id="soldout">';

//  }

// });

?>