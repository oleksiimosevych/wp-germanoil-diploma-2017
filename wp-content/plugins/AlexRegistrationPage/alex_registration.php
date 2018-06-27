<?php

/*

Plugin Name: Alex Registration
Plugin URI: www.alexpluginsforwordpress.blogspot.com

Description: Let me change security for registration pass.

Author: Oleksii Mosevych

Author URI: https://plus.google.com/+alexmosevych

Text Domain: alexplugins

Version: 1.0

*/


// Add the code below to your theme's functions.php file to add a confirm password field on the register form under My Accounts.
add_filter('woocommerce_registration_errors', 'registration_errors_validation', 10,3);
function registration_errors_validation($reg_errors, $sanitized_user_login, $user_email) {
	global $woocommerce;
	extract( $_POST );
	if ( strcmp( $password, $password2 ) !== 0 ) {
		return new WP_Error( 'registration-error', __( 'Паролі не збігаються.', 'woocommerce' ) );
	}
	$pas_pattern='/^[0-9A-Z]{4,20}$/i';
	preg_match( $pas_pattern, $password , $matches);
	if ( $password!==$matches[0] ) {
		return new WP_Error( 'registration-error', __( '<b>Пароль</b> введено неправильно. Дозволяється від 4 до 20 літер латинкою та цифр.', 'woocommerce' ) );
	}
	return $reg_errors;
}
add_action( 'woocommerce_register_form', 'wc_register_form_password_repeat' );
function wc_register_form_password_repeat() {
	?>
	<p class="form-row form-row-wide">
		<label for="reg_password2"><?php _e( 'Повторіть пароль', 'woocommerce' ); ?> <span class="required">*</span></label>
		<input type="password" class="input-text" name="password2" id="reg_password2" value="<?php if ( ! empty( $_POST['password2'] ) ) echo esc_attr( $_POST['password2'] ); ?>" />
	</p>
	<?php
}

function wc_ninja_remove_password_strength() {
	if ( wp_script_is( 'wc-password-strength-meter', 'enqueued' ) ) {
		wp_dequeue_script( 'wc-password-strength-meter' );
	}
}
add_action( 'wp_print_scripts', 'wc_ninja_remove_password_strength', 100 );



?>