<?php

    /**
    * Adding the setting pages for both the appearance
    * and the custom search queries in the admin panel
    */


    function mkapis_lite_add_submenu_page() {
    //Adding plugin submenu page
    add_submenu_page('options-general.php',  __('Archive Pages in Search Lite', 'archive-pages-in-search-lite' ), __( 'Archive Pages in Search Lite', 'archive-pages-in-search-lite' ), 'manage_options', 'mkapis-lite-main', 'mkapis_lite_queries_page');
    add_action('admin_init', 'mkapis_lite_appearance_settings');
    }
    add_action('admin_menu', 'mkapis_lite_add_submenu_page' );

    //Add sections and fields for the submenu pages
    function mkapis_lite_appearance_settings() {

      require_once("mkapis-options.php");

      //Adding section and fields for the settings page
      add_settings_section('appearance', '', 'mkapis_lite_appearance_section', 'mkapis-lite-main');
      add_settings_field('show-categories', __('Show Categories?', 'archive-pages-in-search-lite' ), 'mkapis_lite_show_categories', 'mkapis-lite-main', 'appearance');
      add_settings_field('show-tags', __('Show tags?', 'archive-pages-in-search-lite' ), 'mkapis_lite_show_tags', 'mkapis-lite-main', 'appearance');
      add_settings_field('show-author', __('Show Authors?', 'archive-pages-in-search-lite' ), 'mkapis_lite_show_authors_archives', 'mkapis-lite-main', 'appearance');
      add_settings_field('delete-data', __('Delete plugin data when plugin is uninstalled', 'archive-pages-in-search-lite' ), 'mkapis_lite_delete_all_plugin_data', 'mkapis-lite-main', 'appearance');

    }

    require_once("appearance-settings.php");
