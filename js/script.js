/*

  Plugin Name:    Roelven's WP Map
  Plugin URI:     https://github.com/Roelven/rv_map
  Description:    Add controls to /map page, add assets

*/

jQuery(document).ready(function($) {
  var zIndex = 500,
    mapOptions = {
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      center: (new google.maps.LatLng(52.52340510, 13.41139990)),
      zoom: 12,
      panControl: true,
      zoomControl: true,
      mapTypeControl: false,
      streetViewControl: false
    },
    lastInfoWindow,
    openMarker,
    markers = [],
    map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions),
    id_markers = {};

  $.each(stb_locations, function() {
    var stb_location = this,
        marker;

    stbMarker = new google.maps.MarkerImage('http://slowtravelberlin.com/wp-content/themes/news-magazine-theme-640/images/map/'+stb_location.cat+'.png');
    marker = new google.maps.Marker({
      map: map,
      draggable: false,
      icon: stbMarker,
      animation: google.maps.Animation.DROP,
      title: stb_location.post_title,
      position: (new google.maps.LatLng(stb_location.lat, stb_location.lon))
    });

    markers.push(marker);
    id_markers[stb_location.ID] = marker;
    marker.stb_location = stb_location;
    google.maps.event.addListener(marker, 'click', function() {
      if(lastInfoWindow) {
        lastInfoWindow.close();
      }
      var marker = this,
          content = infoWindowForTrack(stb_location),
          infowindow = new google.maps.InfoWindow({content: $(content)[0], maxWidth: 550});
      openMarker = marker;
      lastInfoWindow = infowindow;

      infowindow.open(map, marker);
    });

  });

  $('<h1>Categories</h1><p id="cat_all_none">(<a href="#" id="cat_all">Show All</a> - <a href="#" id="cat_none">Show None</a>)</p><ul id="st_categories"></ul><br class="clear" />').insertBefore("#map_canvas");

  $('#cat_all').click(function(e) {
    e.preventDefault();
    if(lastInfoWindow){
      lastInfoWindow.close();
    }

    $('.map_cat').attr('checked', true);
    for (var i=0; i<markers.length; i++) {
      markers[i].setVisible(true);
    }
  });

  $('#cat_none').click(function(e) {
    e.preventDefault();
    if(lastInfoWindow){
      lastInfoWindow.close();
    }

    $('.map_cat').attr('checked', false);
    for (var i=0; i<markers.length; i++) {
      markers[i].setVisible(false);
    }
  });

  $.each(categories, function(k, v) { 
    if (k == 3) {
      $('#st_categories').append('<li><input type="checkbox" name="category" class="map_cat" value="'+k+'" checked /> '+ v + '</li>');
    } else {
      $('#st_categories').append('<li><input type="checkbox" name="category" class="map_cat" value="'+k+'" /> '+ v + '</li>');
    };
  });

  for (var i=0; i<markers.length; i++) {
    markers[i].setVisible(false);
  }
  $('.map_cat:checked').each(function(k, v) {
    for (var i=0; i< category_posts[v.value].length; i++) {
      id_markers[category_posts[v.value][i]].setVisible(true);
      id_markers[category_posts[v.value][i]].setIcon('http://slowtravelberlin.com/wp-content/themes/news-magazine-theme-640/images/map/' + v.value + '.png');
    }
  });

  $(".map_cat").click(function(e) {
    if(lastInfoWindow){
      lastInfoWindow.close();
    }
    for (var i=0; i<markers.length; i++) {
      markers[i].setVisible(false);
    }
    $('.map_cat:checked').each(function(k, v) {
      for (var i=0; i< category_posts[v.value].length; i++) {
        id_markers[category_posts[v.value][i]].setVisible(true);
        id_markers[category_posts[v.value][i]].setIcon('http://slowtravelberlin.com/wp-content/themes/news-magazine-theme-640/images/map/' + v.value + '.png');
      }
    });
  });

  function infoWindowForTrack(stb_location) {
    var artwork_url = stb_location.artwork_url ? stb_location.artwork_url.replace('large', 't67x67') : '/images/placeholder-t67x67.png?1',
      content = '<div class="infoWindow">';
      content += '<div class="thumbnail"><a target="_blank" href="/'+ stb_location.post_name +'"><img src="'+ stb_location.thumbnail +'" width="auto" height="80px" /></a></div>';
      content += '<div class="metadata">';
      content += '<h2 class="title"><a target="_blank" href="/'+ stb_location.post_name +'">'+ stb_location.post_title +'</a></h2>';
      content += stb_location.address +'<br />';
      content += stb_location.postal_code +' Berlin<br /><br />';
      content += '<a target="_blank" href="/' + stb_location.post_name + '" class="read_more">Read more &rarr;</a><br />';
      content += '</div>';
    return content;
  };

});