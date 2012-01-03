<?php
/*

  Plugin Name:    Roelven's WP Map
  Plugin URI:     https://github.com/Roelven/rv_map
  Description:    Provides map / geodata functionality for your Wordpress site
  Template Name:  Maps page

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
  <script>
    <?php
    // print array stb_locations with PHP, function rv_printLocationsArray */
    print rv_printLocationsArray();
    ?>
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
