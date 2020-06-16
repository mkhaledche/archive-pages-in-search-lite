<?php
//Make sure the form is submitted
if(isset($_GET['page']) && $_GET['page'] === "mkapis-lite-main" && isset($_POST['submit'])) {

    //Protection against CSRF
    if( !isset($_POST['mkapis-lite-main-nonce']) || ! wp_verify_nonce($_POST['mkapis-lite-main-nonce'], "mkapis-lite-main-options" )) {
      return;
    }

    //Admin validation
    if( !current_user_can( 'manage_options' ) ) {
      return;
    }

    //Get existing settings
    $mkapis_existing_settings_options = get_option('mkapis_lite_settings_options');

    //Prepare for new settings
    $mkapis_settings_options = array();

      $mkapis_settings_options['show_categories'] = isset($_POST['show_categories']) ? "1" : '';
      $mkapis_settings_options['show_tags'] = isset($_POST['show_tags']) ? "1" : '';
      $mkapis_settings_options['show_author'] = isset($_POST['show_author']) ? "1" : '';
      $mkapis_settings_options['delete_plugin_data'] = isset($_POST['delete_plugin_data']) ? "1" : '';


    //Send the new settings array to the database to update the settings option
      if(!$mkapis_existing_settings_options) {
          add_option('mkapis_lite_settings_options', $mkapis_settings_options);
        } else {
          update_option('mkapis_lite_settings_options', $mkapis_settings_options);
        }
}
