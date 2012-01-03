<?php
/*
Template Name: Maps page
*/

global $themeoptionsprefix; get_header(); ?>

<div id="content">

    <div id="map_canvas"></div>

		<?php if (have_posts()) : while (have_posts()) : the_post();
    the_content();
    endwhile;
    endif;

    ?>

</div>

  <!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="js/libs/jquery-1.6.1.min.js"><\/script>')</script>
  <script src="http://maps.googleapis.com/maps/api/js?sensor=false"></script> 

  <script>
  $(document).ready(function() {
    /* print array stb_locations with PHP, function rv_printLocationsArray */
    <?php print rv_printLocationsArray(); ?>

    $(function() {

      var mapOptions = {
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        center: (new google.maps.LatLng(52.52340510, 13.41139990)),
        zoom: 12,
        panControl: true,
        zoomControl: true,
        mapTypeControl: false,
        streetViewControl: false
      };

    map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
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
        if(lastInfoWindow){
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

		$("#cat_all").click(function(e) {
			e.preventDefault();
      if(lastInfoWindow){
        lastInfoWindow.close();
      }

			$(".map_cat").attr("checked", true);
			for (var i=0; i<markers.length; i++) {
        markers[i].setVisible(true);
      }
		});

		$("#cat_none").click(function(e) {
			e.preventDefault();
      if(lastInfoWindow){
        lastInfoWindow.close();
      }

			$(".map_cat").attr("checked", false);
			for (var i=0; i<markers.length; i++) {
        markers[i].setVisible(false);
      }
		});

		$.each(categories, function(k, v) { 
			if (k == 3) {
				$("#st_categories").append('<li><input type="checkbox" name="category" class="map_cat" value="'+k+'" checked /> '+ v + '</li>');
			} else {
				$("#st_categories").append('<li><input type="checkbox" name="category" class="map_cat" value="'+k+'" /> '+ v + '</li>');
			}
		});

		for (var i=0; i<markers.length; i++) {
			markers[i].setVisible(false);
		}
		$(".map_cat:checked").each(function(k,v) {
			for (var i=0; i< category_posts[v.value].length; i++) {
				id_markers[category_posts[v.value][i]].setVisible(true);
				id_markers[category_posts[v.value][i]].setIcon("http://slowtravelberlin.com/wp-content/themes/news-magazine-theme-640/images/map/"+v.value+".png");
			}	
		});

		$(".map_cat").click(function(e) {
      if(lastInfoWindow){
        lastInfoWindow.close();
      }
			for (var i=0; i<markers.length; i++) {
				markers[i].setVisible(false);
			}
			$(".map_cat:checked").each(function(k,v) {
				for (var i=0; i< category_posts[v.value].length; i++) {
					id_markers[category_posts[v.value][i]].setVisible(true);
					id_markers[category_posts[v.value][i]].setIcon("http://slowtravelberlin.com/wp-content/themes/news-magazine-theme-640/images/map/"+v.value+".png");
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
      }
    });
		
  });

  </script>

<style type="text/css">
#cat_all_none {
	margin: 0;
	margin-bottom: 10px;
	font-size: 10px;
}
#cat_all_none a, .read_more {
	color: blue!important;
}
#map_canvas {
	margin-top: 10px;
}

#st_categories {
}
#st_categories li {
	float: left;
	padding: 4px;
	margin-right: 4px;
	list-style: none;
}

#st_categories li input {
	margin-right: 2px;
}
.infoWindow .thumbnail {
	width: 70px;
	height: 80px;
	border: solid 1px #ccc;
}
.infoWindow .metadata {
	width: 200px;
	float: right;
}
</style>

<?php get_footer(); ?>
