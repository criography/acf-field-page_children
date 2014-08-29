<?php

/*
Plugin Name: Advanced Custom Fields: Page Children
Plugin URI: PLUGIN_URL
Description: Displays a list of current page's children, if present
Version: 1.0.0
Author: Marek Lenik
Author URI: http://criography.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/




// 1. set text domain
// Reference: https://codex.wordpress.org/Function_Reference/load_plugin_textdomain
load_plugin_textdomain( 'acf-page_children', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );




// 2. Include field type for ACF5
// $version = 5 and can be ignored until ACF6 exists
function include_field_types_page_children( $version ) {
	
	include_once('acf-page_children-v5.php');
	
}

add_action('acf/include_field_types', 'include_field_types_page_children');


add_action('acf/register_fields', 'register_fields_page_children');



	
?>