<?php
/**
 * @package import_excel
 * @version 1.0
 */
/*
Plugin Name: import_excel
Plugin URI: https://prog-web.ru/blog/import-excel-vyivod-tabliczyi-iz-bazyi.html
Description: Plugin for import tables (xlsx) in site database
Version: 1.0
Author: Pavel Ogurtsov
Author URI: http://prog-web.ru/
*/
define ( 'PE_FILE', true );
include_once("functions.php");
register_activation_hook( __FILE__, 'price_excel_install');
register_deactivation_hook( __FILE__, 'price_excel_uninstall');
add_action('admin_menu', 'price_excel_add_admin_page');
add_action('admin_menu', 'price_excel_view_add_admin_page');
add_action('admin_init', 'style_init');
add_action('admin_enqueue_scripts', 'script_init');
?>