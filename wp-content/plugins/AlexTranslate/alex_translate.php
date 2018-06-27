<?php

/*

Plugin Name: Alex Translate

Plugin URI: www.alexpluginsforwordpress.blogspot.com

Description: Let me change your text.

Author: Oleksii Mosevych

Author URI: https://plus.google.com/+alexmosevych

Text Domain: alexplugins

Version: 3.0

*/

// function wc_alex_dostavka( $translated_text, $text, $domain ) {
//     switch ( $translated_text ) {
//         case 'Shipping' :
//             $translated_text = __( '', 'woocommerce' );
//             break;
//     }
//     return $translated_text;
// }
// add_filter( 'gettext', 'wc_alex_dostavka', 20, 3 );

//Change the Billing Address checkout label
function wc_billing_field_strings( $translated_text, $text, $domain ) {
    switch ( $translated_text ) {
        case 'Платіжні дані' :
            $translated_text = __( 'Контактні дані', 'woocommerce' );
            break;
    }
    return $translated_text;
}
add_filter( 'gettext', 'wc_billing_field_strings', 20, 3 );


function change_my_oblast( $translated_text, $text, $domain ) {
    switch ( $translated_text ) {
        case 'Штат / Округ' :
            $translated_text = __( 'Область', 'woocommerce' );
            break;
    }
    return $translated_text;
}
add_filter( 'gettext', 'change_my_oblast', 20, 3 );



 
//Change the Shipping Address checkout label
function wc_shipping_field_strings( $translated_text, $text, $domain ) {
    switch ( $translated_text ) {
        case 'Shipping Address' :
            $translated_text = __( 'Shipping Info', 'woocommerce' );
            break;
    }
    return $translated_text;
}
add_filter( 'gettext', 'wc_shipping_field_strings', 20, 3 );

/**/
function comments_field_strings( $translated_text, $text, $domain ) {
    switch ( $translated_text ) {
        case 'Submit your review' :
            $translated_text = __( 'Надіслати відгук', 'woocommerce' );
            break;
    }
    return $translated_text;
}
add_filter( 'gettext', 'comments_field_strings', 20, 3 );


?>
