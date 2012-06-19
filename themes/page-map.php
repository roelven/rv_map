<?php
/*

  Plugin Name:    Roelven's WP Map
  Plugin URI:     https://github.com/Roelven/rv_map
  Description:    Provides map / geodata functionality for your Wordpress site
  Template Name:  Maps page

*/

global $themeoptionsprefix; get_header(); ?>

<div id="content" class="mapwrapper">

    <div id="map_canvas"></div>

    <div class="map_filter">
      <h4>Categories</h4>
      <p class="cat_all_none">
        (<a href="#" class="cat_all">Show all</a> - <a href="#" class="cat_none">Show none</a>)
        <ul class="st_categories"></ul>
      </p>

      <h4>Neighbourhoods</h4>
      <p class="cat_all_none">
        <ul class="st_areas"></ul>
      </p>
    </div>


    <?php if (have_posts()) : while (have_posts()) : the_post();
    the_content();
    endwhile;
    endif;

    ?>

</div>

<?php print rv_printLocationsArray(); ?>

<?php get_footer(); ?>
