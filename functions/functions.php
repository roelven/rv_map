<?php
/*

  Functionality to add Map info to post edit pages for Slowtravelberlin.com
  Add_meta_box function inspired by http://codex.wordpress.org/Function_Reference/add_meta_box
  August 2011 / Roel van der Ven

*/

function rv_enqueue_scripts() {
  if (!is_admin()) {
    wp_register_script('jquery');
    wp_register_script('gmaps', 'http://maps.googleapis.com/maps/api/js?sensor=false', null, '', true);
    wp_register_script('rv_map_script', '/wp-content/plugins/rv_map/js/script.js', 'jquery', '', true);

    // Enqueue the scripts in the preferred order
    wp_enqueue_script('jquery');
    wp_enqueue_script('gmaps');
    wp_enqueue_script('rv_map_script');
  }
}


function rv_showMap($postID) {
  global $wpdb;

  $output = '';
  $mapHeight = '100';
  $mapWidth = '470';
  $maps_uri = 'http://maps.google.com/maps/api/staticmap';
  $mapIcon = 'http://slowtravelberlin.com/stb_map_icon3.png';

  $locationTable = $wpdb->prefix.'stbgeodata';
  $locationQuery = $wpdb->get_row("SELECT address, postal_code, city, country FROM $locationTable WHERE post_id = $postID", ARRAY_A);
  $address = $locationQuery[address].', '.$locationQuery[postal_code].' '.$locationQuery[city].', '.$locationQuery[country];
  $mapImage= $maps_uri.'?center='.urlencode($address).'&sensor=false&zoom=15&size='.$mapWidth.'x'.$mapHeight.'&markers=icon:'.$mapIcon.'|'.urlencode($address);

  if ($locationQuery[address] && $locationQuery[postal_code]) {
    $output .= '<a onclick="stb_recordOutboundLink(this, \'Map_clicks\', \'http://maps.google.com/maps?q='.urlencode($locationQuery[address]).',+'.$locationQuery[city].',+'.$locationQuery[country].'\');return false;" href="http://maps.google.com/maps?q='.urlencode($locationQuery[address]).',+'.$locationQuery[city].',+'.$locationQuery[country].'" target="_blank" title="View on Google Maps">';
    $output .= '  <img class="stb_map" src="'.$mapImage.'" alt="'.$address.'" width="'.$mapWidth.'" height="'.$mapHeight.'" />';
    $output .= '</a>';
  } else {
    $output .= '';
  }

  print $output;
}

function rv_printLocationsArray() {
  global $wpdb;

  $output = '';
  $rvLocationTable = $wpdb->prefix.'stbgeodata';
  $wpPostsTable = $wpdb->prefix.'posts';
  $locationQuery = 'SELECT '.$wpPostsTable.'.ID, '.$wpPostsTable.'.post_title, '.$wpPostsTable.'.post_name, '.$rvLocationTable.'.location, '.$rvLocationTable.'.address, '.$rvLocationTable.'.postal_code, '.$rvLocationTable.'.area, '.$rvLocationTable.'.latitude, '.$rvLocationTable.'.longitude FROM wp_posts, '.$rvLocationTable.' WHERE '.$wpPostsTable.'.ID = '.$rvLocationTable.'.post_id AND '.$wpPostsTable.'.post_status = "publish"';
  $locations = $wpdb->get_results($locationQuery, ARRAY_A);

  $output .= '[';
  foreach($locations as $location) {
    if ($location['post_title'] && $location['latitude'] && $location['longitude']) {
      $stb_title = rv_translatethis($location['post_title']);
      $stb_image = rv_getAttachment($location['ID']);
      $stb_thumbnail = wp_get_attachment_thumb_url($stb_image[0]->ID);
      $output .= '{';
      $output .= '"ID":'.$location['ID'].',';
      $output .= '"thumbnail":"'.$stb_thumbnail.'",';
      $output .= '"post_title":"'.htmlentities($stb_title['en']).'",';
      $output .= '"post_name":"'.htmlentities($location['post_name']).'",';
      $output .= '"location":"'.$location['location'].'",';
      $output .= '"address":"'.$location['address'].'",';
      $output .= '"postal_code":'.$location['postal_code'].',';
      $output .= '"area":"'.$location['area'].'",';
      $output .= '"lat":'.$location['latitude'].',';
      $output .= '"lon":'.$location['longitude'];
      $output .= '},'."\n";
    }
  }
  $output .= ']';

  return $output;

}

// Get custom styles in plugin on admin
function rv_map_register_style() {
  wp_register_style('rv_maptyles', WP_PLUGIN_URL . '/rv_map/css/style.css?v4');
  wp_enqueue_style('rv_maptyles');
}

// Load custom javascript through wp script loader
function rv_map_register_script() {
  wp_enqueue_script('jquery');
  wp_register_script('rv_map_js', WP_PLUGIN_URL . '/rv_map/js/admin-script.js', 'jquery');
  wp_enqueue_script('rv_map_js');
}


// Adds a box to the main column on the Post and Page edit screens
function rv_add_custom_box() {
  add_meta_box('rv_sectionid', 'Slow Travel Map Info', 'rv_inner_custom_box', 'post', 'normal', 'high');
}

// Prints the box content
function rv_inner_custom_box() {

  global $post;
  global $wpdb;
  $locationTable = $wpdb->prefix.'stbgeodata';

  $locationQuery = $wpdb->get_row("SELECT location, address, postal_code, area, latitude, longitude FROM $locationTable WHERE post_id = $post->ID", ARRAY_A);

  // Use nonce for verification
  wp_nonce_field(plugin_basename( __FILE__ ), 'rv_noncename');

  print '
      <div class="stb_map_container">
        <img class="stb_map loading" src="/wp-content/plugins/rv_map/img/loading.gif" alt="Preview location" width="30" height="30" />
      </div>
    ';

  // Apologize for ugly print code here. Oh Wordpress..
  print '
      <div class="stb_map_wrapper">
        <div class="stb_map_wrapper_inner">
          <label for="rv_location">Location: </label> <input autocomplete="off" id="rv_location" name="rv_location" type="text" value="'.$locationQuery['location'].'" placeholder="Knofi" size="25" />
          <label for="rv_address">Address: </label> <input autocomplete="off" id="rv_address" name="rv_address" type="text" placeholder="Bergmannstraße 27" value="'.$locationQuery['address'].'" size="25" />
          <label for="rv_postal_code">PLZ: </label> <input autocomplete="off" id="rv_postal_code" name="rv_postal_code" type="text" placeholder="10999" value="'.$locationQuery['postal_code'].'" size="25" />
          <label for="rv_area">Area: </label>
          <select id="rv_area" name="rv_area">
            <option value="">- select -</option>
            <option value="Charlottenburg" '; if($locationQuery['area'] == 'Charlottenburg') { print 'selected="selected"';}; print '>Charlottenburg</option>
            <option value="Friedrichshain" '; if($locationQuery['area'] == 'Friedrichshain') { print 'selected="selected"';}; print '>Friedrichshain</option>
            <option value="Grunewald" '; if($locationQuery['area'] == 'Grunewald') { print 'selected="selected"';}; print '>Grunewald</option>
            <option value="Kreuzberg" '; if($locationQuery['area'] == 'Kreuzberg') { print 'selected="selected"';}; print '>Kreuzberg</option>
            <option value="Mitte" '; if($locationQuery['area'] == 'Mitte') { print 'selected="selected"';}; print '>Mitte</option>
            <option value="Neukölln" '; if($locationQuery['area'] == 'Neukölln') { print 'selected="selected"';}; print '>Neukölln</option>
            <option value="Pankow" '; if($locationQuery['area'] == 'Pankow') { print 'selected="selected"';}; print '>Pankow</option>
            <option value="Prenzlauer Berg" '; if($locationQuery['area'] == 'Prenzlauer Berg') { print 'selected="selected"';}; print '>Prenzlauer Berg</option>
            <option value="Schöneberg" '; if($locationQuery['area'] == 'Schöneberg') { print 'selected="selected"';}; print '>Schöneberg</option>
            <option value="Tiergarten" '; if($locationQuery['area'] == 'Tiergarten') { print 'selected="selected"';}; print '>Tiergarten</option>
            <option value="Zehlendorf" '; if($locationQuery['area'] == 'Zehlendorf') { print 'selected="selected"';}; print '>Zehlendorf</option>
          </select>
          <input type="hidden" name="rv_latitude" id="rv_latitude" value="'.$locationQuery['latitude'].'" />
          <input type="hidden" name="rv_longitude" id="rv_longitude" value="'.$locationQuery['longitude'].'" />
        </div>
      </div>
    ';
}

// When the post is saved, saves our custom data
function rv_save_postdata() {

  global $wpdb;
  global $post;
  $geotable = $wpdb->prefix.'stbgeodata';

  // verify if this is an auto save routine. 
  // If our form has not been submitted, so we dont want to do anything
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
    return;

  // verify this came from the our screen and with proper authorization,
  // because save_post can be triggered at other times
  if (!wp_verify_nonce($_POST['rv_noncename'], plugin_basename(__FILE__)))
    return;

  // Check permissions
  if (!current_user_can('edit_post', $post_id))
    return;

  // If an update fails, then attempt to insert it as a new row in the table
  // use all the form fields as database fields, be sure to add the post_id on the insert statement
  if (!$wpdb->update($geotable, array(
    'location'    => $_POST['rv_location'],
    'address'     => $_POST['rv_address'],
    'postal_code' => $_POST['rv_postal_code'],
    'area'        => $_POST['rv_area'],
    'city'        => 'Berlin',
    'country'     => 'Germany',
    'latitude'    => $_POST['rv_latitude'],
    'longitude'   => $_POST['rv_longitude']
  ), array('post_id' => $post->ID), array('%s', '%s', '%s', '%s', '%s', '%s'), array('%d'))) {
    $wpdb->insert($geotable, array(
      'post_id'     => $post->ID,
      'location'    => $_POST['rv_location'],
      'address'     => $_POST['rv_address'],
      'postal_code' => $_POST['rv_postal_code'],
      'area'        => $_POST['rv_area'],
      'city'        => 'Berlin',
      'country'     => 'Germany',
      'latitude'    => $_POST['rv_latitude'],
      'longitude'   => $_POST['rv_longitude']
    ), array('%d', '%s', '%s', '%s', '%s', '%s', '%s'));
  };

}

function rv_theme_redirect($url) {
  global $post, $wp_query;
  if (have_posts()) {
    include($url);
    die();
  } else {
    $wp_query->is_404 = true;
  }
}

function rv_map_redirect() {
  global $wp;
  // one dir up since we're in /functions/
  $plugindir = dirname(__FILE__).'/../';

  if ($wp->query_vars['pagename'] === 'map') {
    $templatefilename = 'page-map.php';
    rv_theme_redirect($plugindir . '/themes/' . $templatefilename);
  }
}
