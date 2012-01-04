# RV_MAP

A Wordpress plugin to add location info to posts and aggregate them on a Google Map. It's now tailored to Berlin, but you should be able to add your own areas and cities pretty easy. This plugin has been developed for [slowtravelberlin.com](http://slowtravelberlin.com), check it out at [http://slowtravelberlin.com/map/](http://slowtravelberlin.com/map/).

## What does it do

*   Adds a meta box to post pages to add address information to a post
    ![Adds a meta box to your edit pages](http://dump.roelvanderven.com/rv_map_edit_meta_box-20120104-133034.png)
*   Resolves the address to latitude / longitude and saves that to it's own table in the database
    ![Table structure](http://dump.roelvanderven.com/rv_map_table-20120104-133509.png)
*   Adds a Google map picture (no js map!) with the address info to every post that has geodata
    ![Post picture map](http://dump.roelvanderven.com/rv_map_post_picture-20120104-133706.png)
*   Displays all the posts with geodata on a page called /map
    ![The map](http://dump.roelvanderven.com/rv_map_page_map-20120104-133842.png)

## What does it not do

*   Generate the wordpress page by itself
*   Supports multiple cities / areas
*   Handle categories nicely
*   Handles the CSS
