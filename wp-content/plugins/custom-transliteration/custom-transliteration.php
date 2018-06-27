<?php
/**
 * Plugin Name: Custom Transliteration
 * Plugin URI:  http://thekrotek.com/wordpress-extensions/miscellaneous
 * Description: Transliterates slug from any language to Latin characters, using custom character arrays.
 * Version:     1.0.0
 * Author:      The Krotek
 * Author URI:  http://thekrotek.com
 * Text Domain: transliteration 
 * License:     GPL2
 */
 
defined("ABSPATH") or die("Restricted access");

$transliteration = new Transliteration();

class Transliteration
{
	var $textdomain;
	
	public function __construct()
	{
		add_action('admin_init', array($this, 'admin_init'));
		add_filter('plugin_row_meta', array($this, 'updatePluginMeta'), 10, 2);
		add_filter('wp_insert_post_data', array($this, 'updateSlug'), 1000, 2);
		
		$this->textdomain = 'transliteration';
	}
		
	public function admin_init()
	{
		$page = 'writing';
		$section = 'transliteration_section';
		
		add_settings_section($section, __('Transliteration settings', $this->textdomain), array($this, 'addSection'), $page);

		// Transliteration Input
	
		$id = 'transliteration-input';
		$name = str_replace('-', '_', $id);
		
		$params = array(
			'id' => $id,
			'name' => $name,
			'default' => '');
		
		register_setting($page, $name);
		add_settings_field($name, '<label for="'.$id.'">'.__('Input characters', $this->textdomain).'</label>', array($this, 'addFieldTextarea'), $page, $section, $params);
		
		// Transliteration Output
	
		$id = 'transliteration-output';
		$name = str_replace('-', '_', $id);
		
		$params = array(
			'id' => $id,
			'name' => $name,
			'default' => '');
		
		register_setting($page, $name);
		add_settings_field($name, '<label for="'.$id.'">'.__('Output characters', $this->textdomain).'</label>', array($this, 'addFieldTextarea'), $page, $section, $params);
		
		// Transliteration Update
	
		$id = 'transliteration-update';
		$name = str_replace('-', '_', $id);
		
		$params = array(
			'id' => $id,
			'name' => $name,
			'default' => '0',
			'options' => array('1' => __('Yes', $this->textdomain), '0' => __('No', $this->textdomain)));
		
		register_setting($page, $name);
		add_settings_field($name, '<label for="'.$id.'">'.__('Always update', $this->textdomain).'</label>', array($this, 'addFieldRadio'), $page, $section, $params);			
	}
		
	public function addSection($note)
	{
		echo '<p>'.__('NOTE: Number of elements, separated by comma, in both input and output fields MUST be equal!', $this->textdomain).'</p>';
	}

	public function addFieldTextarea($params)
	{
		echo '<textarea id="'.$params['id'].'" class="large-text code" rows="3" name="'.esc_attr($params['name']).'">'.get_option($params['name'], $params['default']).'</textarea>';
	}

	public function addFieldRadio($params)
	{
		echo '<fieldset id="'.$params['id'].'-fieldset">';
	
		foreach ($params['options'] as $value => $title) {
			echo '<label><input type="radio" id="'.$params['id'].'-'.$value.'" name="'.esc_attr($params['name']).'" value="'.esc_attr($value).'"'.(get_option($params['name'], $params['default']) == $value ? ' checked="checked"' : '').' /> '.$title.'</label><br>';
		}
	
		echo '</fieldset>';
	}
	
	public function updateSlug($data, $postarr)
	{
		if (!$data['post_name'] || get_option('transliteration_update', '0')) {
			$input = array_map('mb_strtolower', array_map('trim', explode(',', get_option('transliteration_input', ''))));
			$output = array_map('mb_strtolower', array_map('trim', explode(',', get_option('transliteration_output', ''))));
		
			if (!empty($input) && !empty($output) && (count($input) == count($output))) {
				$data['post_name'] = mb_strtolower(stripslashes($data['post_title']));
				$data['post_name'] = str_replace($input, $output, $data['post_name']);
				$data['post_name'] = str_replace(" ", "-", preg_replace("/[^a-zA-Z0-9\s]/", "-", $data['post_name'])); // Sanitize and replace spaces with dashes.
				$data['post_name'] = preg_replace("/(-{2,})/", "-", $data['post_name']); // Remove double dashes.
				$data['post_name'] = preg_replace("/(-$)/", "", preg_replace("/(^-)/", "", $data['post_name'])); // Remove dashes at the start and at the end.
			}
		}

    	return $data;
	}

	public function updatePluginMeta($links, $file)
	{
		if ($file == plugin_basename(__FILE__)) {
			$links = array_merge($links, array('<a href="options-writing.php">'.__('Settings', $this->textdomain).'</a>'));
			$links = array_merge($links, array('<a href="https://thekrotek.com/support">'.__('Donate & Support', $this->textdomain).'</a>'));
		}
	
		return $links;
	}	
}

?>