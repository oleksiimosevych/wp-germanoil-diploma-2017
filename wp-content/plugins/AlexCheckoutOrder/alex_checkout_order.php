<?php

/*

Plugin Name: Alex Checkout Order

Plugin URI: www.alexpluginsforwordpress.blogspot.com

Description: Let me change your checkout order.

Author: Oleksii Mosevych

Author URI: https://plus.google.com/+alexmosevych

Text Domain: alexplugins

Version: 1.0

*/
add_filter("woocommerce_checkout_fields", "order_fields");

function order_fields($fields) {

    $order = array(
        "billing_first_name", 
        "billing_last_name", 
        "billing_company", 
        "billing_address_1", 
        "billing_address_2", 
        "billing_postcode", 
        "billing_email", 
        "billing_phone",
        "billing_country",
        "billing_state",
        "billing_city" 
        

    );
    foreach($order as $field)
    {
        $ordered_fields[$field] = $fields["billing"][$field];
    }

    $fields["billing"] = $ordered_fields;
    return $fields;

}



?>