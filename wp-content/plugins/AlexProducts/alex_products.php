<?php

/*

Plugin Name: Alex Products Changes

Plugin URI: www.alexpluginsforwordpress.blogspot.com

Description: Let me add buyNOW method.

Author: Oleksii Mosevych

Author URI: https://plus.google.com/+alexmosevych

Text Domain: alexplugins

Version: 1.0

*/

add_action( 'woocommerce_after_add_to_cart_button', 'alex_buy_now' );

function alex_buy_now(  ) {
	//echo "<a href = 'http://www.google.com.ua'>-----Google</a>";
}







?>