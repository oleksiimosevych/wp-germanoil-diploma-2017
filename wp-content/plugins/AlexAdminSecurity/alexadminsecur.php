<?php
/*
Plugin Name: Alex Admin Security
Plugin URI: www.alexpluginsforwordpress.blogspot.com
Description: No more error message and no wp-version visibility.
Author: Oleksii Mosevych
Author URI: https://plus.google.com/+alexmosevych
Text Domain: alexcartchange
Version: 1.0
*/
/*SECURITY NOT VISIBLE EERORS IF WE GO TO ADMIN*/
add_filter('login_errors',create_function('$a', "return null;"));
/*SSL-conn to wp-config!!!*/
//define('FORCE_SSL_ADMIN', true);
/*remove VERSION OF WP. it is needed for security from bots and hacks*/
remove_action('wp_head', 'wp_generator');
?>