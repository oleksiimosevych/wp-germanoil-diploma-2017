<?php
/*
Plugin Name: Back Button
Plugin URI: https://www.gnnpls.com
Description: Simple Back Button for Posts, Pages and Taxonomies. Make navigation through pages easier for editors and clients.
Version: 0.0
Author: Giannopoulos Nikolaos
Author URI: https://www.gnnpls.com
*/
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if (is_admin())
	{
	add_action( 'init', 'babu_back_button' );

	function babu_back_button() {

	global $pagenow;
	$args=array();
	$final=$acttt='';
	if (isset($_GET['action']))
		{
		$acttt=sanitize_text_field(esc_js(esc_attr(esc_html($_GET['action']))));
		}

	if (($pagenow=='edit.php' || $pagenow=='edit-tags.php') && $acttt!='edit')
		{
		$string='';
		if (isset($_GET) && !empty($_GET))
			{
			$string=array();
			foreach ($_GET as $key=>$value)
				{
				$string[]=sanitize_text_field( $key).'='.sanitize_text_field( $value);
				}
			$string=implode('&',$string);
		}
			$final=$pagenow.'?'.$string;
			$args['version']='link';
			$args['link']=$final;

		}
	else
		{
		if ($acttt=='edit')
			{
			$act=0;
			if ($pagenow=='post.php')
				{
				$args['version']='post';
				}
			else if ($pagenow=='edit-tags.php')
				{
				$args['version']='tag';
				}
			else
				{
				$act=1;
				}
			if ($act==0)
				{
				$args['link']=get_admin_url();
				}
			}
		else if ($pagenow=='post-new.php')
			{
			$args['version']='post-new';
			$args['link']= get_admin_url().'edit.php?';
			if (isset($_GET['post_type']) && !empty($_GET['post_type']))
				{
				$args['link'].='post_type='.sanitize_text_field( $_GET['post_type']);
				}
			}
		}
		$args['value_back']='Back';
		$args['value_question']='Your changes will be lost. Are you sure;';
		if (!empty($args))
			{
			wp_enqueue_style( 'ba_bu_css',plugin_dir_url( __FILE__ ) . 'css/ba_bu_index.css', false, '1.0.0' );
			wp_enqueue_script( 'babu_back_button', plugin_dir_url( __FILE__ ) . 'js/back_button.js', array( 'jquery' ), '1.0');
			wp_localize_script('babu_back_button', 'ba_bu_settings', $args);
			}
	}


	add_action('plugins_loaded', 'babu_translations');
	function babu_translations() {
	load_plugin_textdomain( 'babu-back', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
	}

}


?>