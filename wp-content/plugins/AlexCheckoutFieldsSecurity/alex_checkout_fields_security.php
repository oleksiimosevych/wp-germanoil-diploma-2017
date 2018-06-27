<?php

/*

Plugin Name: Alex Checkout Fields Security

Plugin URI: www.alexpluginsforwordpress.blogspot.com

Description: WooRegexChechoutByAlex. Let me use REGEX for your fields. Let me show you all errors in your wrong filled fields

Author: Oleksii Mosevych

Author URI: https://plus.google.com/+alexmosevych

Text Domain: alexplugins

Version: 8.0

*/

/*VALIDATION*/

/*РЕГУЛЯРКИ*/

/*imia*/

add_action('woocommerce_checkout_process', 'proof_my_name');

function proof_my_name() { 

  //  $phone_number = $_POST['billing_phone'];

    $pattern="/^[А-ЯІЇҐЄ]{1}[а-яґєії\D]{1,19}+$/u";

    $b_name=$_POST["billing_first_name"];

    preg_match( $pattern, $b_name , $matches);

    if($b_name!==$matches[0]){

        wc_add_notice( __( '<b>Ім\'я</b> має бути з великої букви, від 2 до 20 літер. Тільки кирилиця!' ), 'error' );

    }
    else{wc_add_notice( __( '<b>Ім\'я</b> введено вірно.' ));}

}

add_action('woocommerce_checkout_process', 'proof_my_bl_name');

function proof_my_bl_name() { 

  //  $phone_number = $_POST['billing_phone'];

    $pattern="/^[А-ЯІЇҐЄ]{1}[а-яґєії\D]{1,29}+$/u";

    $b_lname=$_POST["billing_last_name"];

    preg_match( $pattern, $b_lname , $matches);

    if($b_lname!==$matches[0]){

        wc_add_notice( __( '<b>Прізвище</b> має бути з великої букви, від 2 до 30 літер. Тільки кирилиця!' ), 'error' );

    }
    else{wc_add_notice( __( '<b>Прізвище</b> введено вірно.' ));}

}

/*for email*/

add_action('woocommerce_checkout_process', 'proof_my_mail');

function proof_my_mail() { 

   // $phone_number = $_POST['billing_phone'];

    $pattern='/^([a-zA-Z0-9_\-\.]+)@([a-zA-ZА-Яа-я0-9_\-\.]+)\.([a-zA-Z]{2,5})$/u';

    $mail=$_POST["billing_mail"];

    preg_match( $pattern, $mail , $matches);

    if($mail!==$matches[0]){

        wc_add_notice( __( 'Введіть справжню адресу електронної пошти, щоб ми могли перевірити її.' ), 'error' );

    }
    else{ if(!is_empty($mail)){wc_add_notice( __( '<b>Формат пошти</b> правильний.' ));} }

}

/*for phone*/

add_action('woocommerce_checkout_process', 'is_phone_ua');

//dla ukriny

function is_phone_ua() { 

  //  $phone_number = $_POST['billing_phone'];

    $pattern='/^(\+38\ \([0-9]{3}\)\ [0-9]{3}-[0-9]{2}-[0-9]{2})*(\+38[0-9]{10})*$/'; // +38 (093) 207-58-90
    //$pattern='/^\([0-9]{3}\)\s[0-9]{3}-[0-9]{4}$/';
    $phone_number=$_POST["billing_phone"];

    preg_match( $pattern, $phone_number , $matches);

    if($phone_number!==$matches[0]){

        wc_add_notice( __( '<b>Номер</b> введено неправильно. Вводіть +38 і ваші 10 цифр.' ), 'error' );

    }
    else{wc_add_notice( __( '<b>Номер телефону</b> введено правильно.' ));}
}

add_action('woocommerce_checkout_process', 'password_streng');

//dla ukriny

function password_streng() { 

  //  $phone_number = $_POST['billing_phone'];

    $pattern='/^[0-9A-Z]{4,20}$/i';
    //$pattern='/^\([0-9]{3}\)\s[0-9]{3}-[0-9]{4}$/';
    $passs=$_POST["account_password"];

    preg_match( $pattern, $passs , $matches);

    if($_POST['createaccount']){
        //
        if($passs!==$matches[0]){

            wc_add_notice( __( '<b>Пароль</b> введено неправильно. Дозволяється від 4 до 20 літер латинкою та цифр' ), 'error' );

        }
        else{wc_add_notice( __( '<b>Пароль</b> введено правильно.' ));}
        //
    }
}


add_action('woocommerce_checkout_process', 'check_no_payment');
function check_no_payment(){
    if($_POST['payment_method_spyr_authorizenet_aim']){
        wc_add_notice( __( '<b>МЕТОД ОПЛАТИ НЕ ОБРАНО!</b> Оберіть' ), 'error' );
    }    
}

?>