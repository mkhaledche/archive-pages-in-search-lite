<?php
// Sections and fields for the settings submenu

//Main settings form
function mkapis_lite_queries_page() {

  echo "<h1>". esc_html__( 'Settings', 'archive-pages-in-search' ). "</h1>";

  echo "<form action='' method='post'>";

          settings_errors();
          do_settings_sections('mkapis-lite-main');
          wp_nonce_field( "mkapis-lite-main-options", "mkapis-lite-main-nonce" );
          submit_button();
          echo "</form>";

?>
<div class="w3-threequarter">
  <p class="w3-xlarge"><?php esc_html_e('Want more features? You can find these features in the PRO version:', 'archive-pages-in-search-lite' ); ?></p>
  <ul class="w3-ul w3-large">
    <li><button onclick="mkapisAccordion('a1')" class="w3-btn w3-block w3-teal w3-left-align w3-round-large w3-padding-large"><?php esc_html_e('Show archives pages of custom post types and custom taxonomies', 'archive-pages-in-search-lite' ); ?><span class="dashicons dashicons-plus w3-right w3-xxlarge w3-margin-right"></span></button>
        <div id="a1" class="w3-container w3-hide w3-light-grey">
          <p class="w3-large"><?php esc_html_e('As an example products and their categories or brands for Woocommerce sites, courses for education websites, ..etc. ', 'archive-pages-in-search-lite' ); ?></p>
        </div>
    </li>
    <li><button onclick="mkapisAccordion('a2')" class="w3-btn w3-block w3-teal w3-left-align w3-round-large w3-padding-large"><?php esc_html_e('Display archive pages in search results', 'archive-pages-in-search-lite' ); ?><span class="dashicons dashicons-plus w3-right w3-xxlarge w3-margin-right"></span></button>
        <div id="a2" class="w3-container w3-hide w3-light-grey">
          <p class="w3-large"><?php esc_html_e('If redirecting directly to the search result is not the optimal choice for your website (e.g. for websites using live search or just wanting to show other results on search), PRO version can show the archive page as a result in search results.', 'archive-pages-in-search-lite' ); ?></p>
        </div>
    </li>
    <li><button onclick="mkapisAccordion('a3')" class="w3-btn w3-block w3-teal w3-left-align w3-round-large w3-padding-large"><?php esc_html_e('Support for WPML', 'archive-pages-in-search-lite' ); ?><span class="dashicons dashicons-plus w3-right w3-xxlarge w3-margin-right"></span></button>
        <div id="a3" class="w3-container w3-hide w3-light-grey">
          <p class="w3-large"><?php esc_html_e('For a multilingual website working with WPML, redirect will not work except for main language. PRO version handles compatibility with WPML and Polylang for the basic option of redirecting directly to archive page or displaying the archive page in search results.', 'archive-pages-in-search-lite' ); ?></p>
        </div>
    </li>
    <li><button onclick="mkapisAccordion('a4')" class="w3-btn w3-block w3-teal w3-left-align w3-round-large w3-padding-large"><?php esc_html_e('Creating custom queries for the archive page', 'archive-pages-in-search-lite' ); ?><span class="dashicons dashicons-plus w3-right w3-xxlarge w3-margin-right"></span></button>
        <div id="a4" class="w3-container w3-hide w3-light-grey">
          <p class="w3-large"><?php esc_html_e('If you want to display the soccer category when the user searches for the word "football", PRO version handles this either through redirecting to soccer archive page or through showing "soccer" archive page in search results. You can assign any word you want to the term you like.', 'archive-pages-in-search-lite' ); ?></p>
        </div>
    </li>
    <li><button onclick="mkapisAccordion('a5')" class="w3-btn w3-block w3-teal w3-left-align w3-round-large w3-padding-large"><?php esc_html_e('Control the archive page to be in search', 'archive-pages-in-search-lite' ); ?><span class="dashicons dashicons-plus w3-right w3-xxlarge w3-margin-right"></span></button>
        <div id="a5" class="w3-container w3-hide w3-light-grey">
          <p class="w3-large"><?php esc_html_e('If you do not want an archive page to appear from search, you can simply delete it from the options page.', 'archive-pages-in-search-lite' ); ?></p>
        </div>
    </li>
  </ul>
  <a href="https://www.codester.com/items/14787/archive-pages-in-search-pro-wordpress-plugin.html?ref=mkhaledche"><button class="w3-btn w3-deep-orange w3-left w3-margin-left w3-round-large w3-padding-large"><?php esc_html_e('Upgrade to PRO version', 'archive-pages-in-search-lite', 'archive-pages-in-search-lite' ); ?></button></a>

</div>

<script>
function mkapisAccordion(id) {
  var x = document.getElementById(id);
  if (x.className.indexOf("w3-show") == -1) {
    x.className += " w3-show";
  } else {
    x.className = x.className.replace(" w3-show", "");
  }
}
</script>
<?php
}

// Enqueue css file for settings pages
function mkapis_lite_custom_query_styles($hook) {

  if ($hook === 'settings_page_mkapis-lite-main') {
    wp_enqueue_style( 'w3-css', plugin_dir_url(__FILE__). "../css/w3.css");
  }
}

add_action('admin_enqueue_scripts', 'mkapis_lite_custom_query_styles');

//Insert new mkapis post types if the option to display archive pages in search was chosen and display the new posts added
function mkapis_lite_appearance_section() {


}

//Option to enable the user to choose if he wants to display categories or not
function mkapis_lite_show_categories() {
  //Get the existing settings related to showing categories
  $mkapis_existing_settings_options = get_option('mkapis_lite_settings_options');
  $show_categories = $mkapis_existing_settings_options['show_categories'];

  //Show the option chosen either from the options table or from the post request if the form was submitted
  $checked = (@$show_categories === "1" ? 'checked' : '');
    echo "<input type='checkbox' name='show_categories' value='1' ".$checked."/>";

}

//Option to enable the user to choose if he wants to display tags or not
function mkapis_lite_show_tags() {
  //Get the existing settings related to showing tags
  $mkapis_existing_settings_options = get_option('mkapis_lite_settings_options');
  $show_tags = $mkapis_existing_settings_options['show_tags'];

  //Show the option chosen either from the options table or from the post request if the form was submitted
  $checked = (@$show_tags === "1" ? 'checked' : '');
    echo "<input type='checkbox' name='show_tags' value='1' ".$checked."/>";


}

//Option to enable the user to choose whether to show authors in search or not
function mkapis_lite_show_authors_archives() {
  //Get the existing settings related to showing authors
$mkapis_existing_settings_options = get_option('mkapis_lite_settings_options');
$show_author = $mkapis_existing_settings_options['show_author'];

//Show the option chosen either from the options table or from the post request if the form was submitted
$checked = (@$show_author === "1" ? 'checked' : '');
  echo "<input type='checkbox' name='show_author' value='1' ".$checked."/>";

}

//Option to enable the user to delete plugin data if the plugin was uninstalled
function mkapis_lite_delete_all_plugin_data() {
  //Get the existing settings related to deleting the data
  $mkapis_existing_settings_options = get_option('mkapis_lite_settings_options');
  $delete_data = $mkapis_existing_settings_options['delete_plugin_data'];

  //Show the option chosen either from the options table or from the post request if the form was submitted
  $checked = (@$delete_data === "1" ? 'checked' : '');
    echo "<input type='checkbox' name='delete_plugin_data' value='1' ".$checked."/>";

}
