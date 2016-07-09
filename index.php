<?php

/*
Plugin Name: Ninja Forms Field: Current Page
Description: Custom field for Ninja Forms to allow sending current page on submission
Author: Ionuț Staicu
Version: 1.0.0
Author URI: http://ionutstaicu.com
 */

if (!defined('ABSPATH')) {
	exit;
}

define('NTZ_NF_CURRENT_PAGE_VERSION', '1.0.0');

define('NTZ_NF_CURRENT_PAGE_BASEFILE', __FILE__);
define('NTZ_NF_CURRENT_PAGE_URL', plugin_dir_url(__FILE__));
define('NTZ_NF_CURRENT_PAGE_PATH', plugin_dir_path(__FILE__));

function ntz_nf_current_url_display($field_id, $data)
{
	$fullURL = 'http';
	if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
		$fullURL .= "s";
	}

	$fullURL .= "://" . $_SERVER["SERVER_NAME"];

	if ($_SERVER["SERVER_PORT"] != "80") {
		$fullURL .= ":" . $_SERVER["SERVER_PORT"];
	}
	$fullURL .= $_SERVER["REQUEST_URI"];
	$baseUrl = $_SERVER["REQUEST_URI"];

	$data['data_format'] = isset($data['data_format']) ? $data['data_format'] : 'url';

	switch ($data['data_format']) {
		case 'id':
			$url = get_the_ID();
			break;

		case 'url_simple':
			$url = $baseUrl;
			break;

		case 'url':
		default:
			$url = $fullURL;
			break;
	}

	printf('<input type="hidden" name="ninja_forms_field_%s" value="%s" />',
		$field_id,
		esc_attr($url)
	);
}

function ntz_nf_current_url_init()
{
	$args = array(
		'name' => 'Current URL',
		'display_label' => false,
		'edit_options' => array(
			array(
				'type' => 'select',
				'name' => 'data_format',
				'label' => 'Format',
				'options' => array(
					array('name' => 'Full URL (permalink)', 'value' => 'url'),
					array('name' => 'ID', 'value' => 'id'),
					array('name' => 'Path (URL without domain name)', 'value' => 'url_simple'),
				),
			),
		),
		'sidebar' => 'template_fields',
		'display_function' => 'ntz_nf_current_url_display',
	);

	if (function_exists('ninja_forms_register_field')) {
		ninja_forms_register_field('ntz_current_url', $args);
	}
}

add_action('init', 'ntz_nf_current_url_init');