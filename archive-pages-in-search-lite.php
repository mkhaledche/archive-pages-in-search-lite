<?php
/**
 * Plugin Name: Archive Pages in Search Lite
 * Description: Displays archive pages categories, tag, author, custom post types, custom taxonomies archives when searching for them.
 * Version:     1.0.0
 * Author:      Mohamad Khaled
 * Text Domain: archive-pages-in-search-lite
 * Domain Path: languages
 * License: GPLv2 or later
 */

 /* © Copyright 2019 Mohamad Khaled

 	This program is free software; you can redistribute it and/or modify
 	it under the terms of the GNU General Public License, version 2, as
 	published by the Free Software Foundation.

 	This program is distributed in the hope that it will be useful,
 	but WITHOUT ANY WARRANTY; without even the implied warranty of
 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 	GNU General Public License for more details.

 	You should have received a copy of the GNU General Public License
 	along with this program; if not, write to the Free Software
 	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */



 // Exit if accessed directly.
 if ( ! defined( 'ABSPATH' ) ) {
 	exit;
 }


 /** Main actions when activating the plugin:
  * Adding all archive pages name and type in wp_options table
  * Adding the default appearance settings when performing search
  */

function mkapis_lite_activate() {
  global $wpdb;

  //Define constant for Pro version
   if ( defined( 'MKAPIS_PRO' ) ) {
      require_once ABSPATH . 'wp-admin/includes/plugin.php';

 			require_once ABSPATH . 'wp-includes/pluggable.php';
 			deactivate_plugins( plugin_basename( __FILE__ ) ); // Deactivate this plugin

 			// WP does not allow us to send a custom meaningful message, so just tell the plugin has been deactivated
 			//wp_safe_redirect( add_query_arg( 'deactivate', 'true', remove_query_arg( 'activate' ) ) );
 			//exit;

     }


  //Prepare data for custom taxonomies
  ob_start();

  /** Get all taxonomy terms from the database.
  * A direct query from the database is established
  * to include all terms to avoid not getting all terms
  * if a plugin like WPML is activated.
  */


  $category_names = get_terms('category');
  $tag_names  = get_terms('post_tag');
  $authors_names = get_users(array('who'=>'authors'));

  $archives_array = array();

  foreach ($category_names as $key => $category_object) {
  $archives_array[$category_object->name] = 'category';
  }

  foreach ($tag_names as $key => $tag_object) {
  $archives_array[$tag_object->name] = 'post_tag';
  }

  foreach ($authors_names as $key => $authors_object) {
    if(count_user_posts($authors_object->ID) > 0){
    $archives_array[$authors_object->display_name] = 'author';
    }
  }


  $settings_options = get_option('mkapis_lite_settings_options');

  //Establishing default appearance settings
  if(!$settings_options || !is_array($settings_options)) {

    $settings_options = array();

    //Default option for the categories to be shown
    $settings_options['show_categories'] = '1';

    //Default option for the tags to be shown
    $settings_options['show_tags'] = '1';

    //Default option to show author
    $settings_options['show_author'] = '1';

    //Default option to delete plugin data in case of uninstall
    $settings_options['delete_plugin_data'] = '';

    //Default option for the custom post types to be shown
    add_option('mkapis_lite_settings_options', $settings_options);
  }

  //Add all archives with their types to options table if the option is not existing
  $existing_archives = get_option('mkapis_lite_archive_names');
  if(!$existing_archives) {
    add_option('mkapis_lite_archive_names', $archives_array);
  }


  ob_clean();
}

register_activation_hook(__FILE__, 'mkapis_lite_activate');

//Delete all data upon uninstall
function mkapis_lite_uninstall() {
  global $wpdb;
  $settings_options = get_option('mkapis_lite_settings_options');

      //Check if the option to delete upon uninstall is activated
      if($settings_options['delete_plugin_data'] === '1') {

      //Delete all options related to the plugin
      $wpdb->query(
      	$wpdb->prepare(
      		"DELETE FROM $wpdb->options WHERE option_name LIKE %s", '%mkapis_lite%')
        );

  }
}

register_uninstall_hook(__FILE__, 'mkapis_lite_uninstall');



require_once('inc/mkapis-settings.php');
require_once('inc/common.php');
