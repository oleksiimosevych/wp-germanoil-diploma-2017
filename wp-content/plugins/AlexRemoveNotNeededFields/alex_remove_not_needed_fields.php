<?php

/*

Plugin Name: Alex Remove Not Needed Checkout Fields

Plugin URI: www.alexpluginsforwordpress.blogspot.com

Description: REMOVES ALL NOT NEEDED FOR UA checkout FIELDS.

Author: Oleksii Mosevych

Author URI: https://plus.google.com/+alexmosevych

Text Domain: alexplug

Version: 3.0

*/

add_filter( 'woocommerce_checkout_fields' , 'alex_remove_billing_checkout_fields' );

// Our hooked in function - $fields is passed via the filter!

function alex_remove_billing_checkout_fields( $fields ) {

    //unset($fields['billing']['billing_first_name']);

    //unset($fields['billing']['billing_last_name']);

    //unset($fields['billing']['billing_phone']);
    
    //unset($fields['billing']['billing_email']);
    
    unset($fields['billing']['billing_company']);

    unset($fields['billing']['billing_address_1']);

    unset($fields['billing']['billing_address_2']);

    unset($fields['billing']['billing_city']);

    unset($fields['billing']['billing_postcode']);

    unset($fields['billing']['billing_country']);

    unset($fields['billing']['billing_state']);

    unset($fields['shipping']['shipping_first_name']);
    unset($fields['shipping']['shipping_last_name']);
    unset($fields['shipping']['shipping_company']);
    unset($fields['shipping']['shipping_address_1']);
    unset($fields['shipping']['shipping_address_2']);
    unset($fields['shipping']['shipping_city']);
    unset($fields['shipping']['shipping_country']);
    unset($fields['shipping']['shipping_postcode']);
    unset($fields['shipping']['shipping_state']);
     if($_GET['do_click']=='В один клік'){ 
        //unset($fields['billing']['billing_first_name']);

        //unset($fields['billing']['billing_last_name']);

        //unset($fields['billing']['billing_phone']);
    
        //unset($fields['billing']['billing_email']);
    
        ?>
                    <!-- <h3>В один клік</h3> -->
              <?php } //else{ ?>
                <? //} //end of else from do click 
    
    
    
    //unset($fields['order']['order_comments']);

    
    //unset($fields['account']['account_username']);

    //unset($fields['account']['account_password']);

    //unset($fields['account']['account_password-2']);

    return $fields;

}

?>