<?php
/*

  Plugin Name:    STB Maps
  Plugin URI:     http://slowtravelberlin.com
  Description:    Provides map / geodata functionality for Slow Travel Berlin
  Version:        0.9.6
  Author:         Roel van der Ven
  Author URI:     http://roelvanderven.com

*/

// Disallow direct access to the plugin file
if (basename($_SERVER['PHP_SELF']) == basename (__FILE__)) {
  die('Sorry, but you cannot access this page directly.');
}

// Include function library
include_once(WP_PLUGIN_DIR . '/RV_map/functions/functions.php');

// Only run on wp-admin pages:
if(is_admin()) {
  add_action('admin_init',      'rv_maps_register_style');
  add_action('admin_init',      'rv_maps_register_script');
  add_action('add_meta_boxes',  'rv_add_custom_box');
  add_action('save_post',       'rv_save_postdata');
}

add_action('template_redirect', 'rv_map_redirect');


?>