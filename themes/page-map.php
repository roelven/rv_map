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

<?php print rv_printLocationsArray(); ?>

<?php get_footer(); ?>
