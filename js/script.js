/*

	JS 

*/

var $ = jQuery.noConflict();

$(document).ready(function() {

  // coördinates available? Then show the map tile already!
  if ($('#rv_latitude').val() && $('#rv_longitude').val()) {
    $('.stb_map').addClass('loading').attr('src', '/wp-content/plugins/RV_maps/img/loading.gif').attr('width', 30).attr('height', 30);
    var maptile = 'http://maps.google.com/maps/api/staticmap?center='+ $('#rv_latitude').val() + ',' + $('#rv_longitude').val() +'&sensor=false&zoom=15&size=170x100&markers=icon:http://slowtravelberlin.com/stb_map_icon3.png|'+ $('#rv_latitude').val() + ',' + $('#rv_longitude').val();
    console.log(maptile);
    $('.stb_map').removeClass('loading').attr('src', maptile).attr('width', 170).attr('height', 100);
  };

  // onBlur of postalcode, try to locate the exact spot and populate the latitude / longitude fields
  $('#rv_postal_code').blur(function() {
    $('.stb_map').addClass('loading').attr('src', '/wp-content/plugins/RV_maps/img/loading.gif').attr('width', 30).attr('height', 30);
    var address = $('#rv_address').val();
    var postal_code = $('#rv_postal_code').val();
    var city = 'Berlin';
    var country = 'DE';

     var dataString = encodeURI('street='+ address + '&postal_code=' + postal_code + '&city=' + city + '&country=' + country);
     console.log(dataString);
     $.ajax({
       type: 'GET',
       processData: true,
       url: '/wp-content/plugins/RV_maps/functions/proxy.php',
       dataType: 'json',
       data: dataString,
       success: function(results) {
         // console.log(results);
         var newLat = results.lat;
         var newLon = results.lon;
         var maptile = 'http://maps.google.com/maps/api/staticmap?center='+ newLat + ',' + newLon +'&sensor=false&zoom=15&size=170x100&markers=icon:http://slowtravelberlin.com/stb_map_icon3.png|'+ newLat + ',' + newLon;
         $('#rv_latitude').val(newLat);
         $('#rv_longitude').val(newLon);
         $('.stb_map').removeClass('loading').attr('src', maptile).attr('width', 170).attr('height', 100);
         return false;
       }
     });
    return false;
  });

});