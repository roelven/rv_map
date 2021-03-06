<?php
/*

  Plugin Name:    Roelven's WP Map
  Plugin URI:     https://github.com/Roelven/rv_map
  Description:    Provides map / geodata functionality for your Wordpress site
  Version:        0.8.1
  Author:         Roel van der Ven
  Author URI:     http://roelvanderven.com

*/

// Disallow direct access to the plugin file
if (basename($_SERVER['PHP_SELF']) == basename (__FILE__)) {
  die('Sorry, but you cannot access this page directly.');
}

// Include function library
include_once(WP_PLUGIN_DIR . '/rv_map/functions/functions.php');

// Only run on wp-admin pages:
if(is_admin()) {
  add_action('admin_init',      'rv_map_register_style');
  add_action('admin_init',      'rv_map_register_script');
  add_action('add_meta_boxes',  'rv_add_custom_box');
  add_action('save_post',       'rv_save_postdata');
}

add_action('template_redirect', 'rv_map_redirect');
add_action('init',              'rv_enqueue_scripts');

?>