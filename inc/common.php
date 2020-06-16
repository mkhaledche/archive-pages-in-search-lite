<?php


//Redirect to archive page from search either directly or through mkapis post type
function mkapis_lite_add_archive_to_results() {

  global $post;
  global $wpdb;
  if( ! defined( 'MKAPIS_PRO' ) ) {

  //Get the archive pages to be shown or redirected
  $archive_terms = get_option('mkapis_lite_archive_names');

  //Get the appearance settings
  $mkapis_lite_existing_settings_options = get_option('mkapis_lite_settings_options');

  //Redirect search result page to the queried archive page directly
  if(null !== get_search_query() && !empty(get_search_query()))  {

    //Get and prepare the search query to compare it with the data in options table
    $search_query = get_search_query();
    $search_query = mkapis_lite_prepare_string($search_query);

    //Get the registered archive pages and compare them with the search query
    foreach ($archive_terms as $term => $type) {

      //Prepare the archive page name
      $term = mkapis_lite_prepare_string($term);

      //Checking the search query against the term
      if($search_query == esc_attr($term) || $search_query == $term ) {

        //If the condition is met, get the term name and type to be added as parameters to the redirect function
        $related_term = $term;
        $related_term_type = $type;
      }

    }

    //Call the redirect function if a match is found
    if(isset($related_term)) {
      mkapis_lite_adjust_redirect($related_term, $related_term_type);
        }
      }
    }
  }

  add_filter('template_redirect', 'mkapis_lite_add_archive_to_results');


//Prepare strings to make their comparison applicable (used to check if search query can find a match in archive pages data)
function mkapis_lite_prepare_string($str) {
  $prepared_string = wp_unslash(strtolower(remove_accents(trim($str))));
  $prepared_string = html_entity_decode($prepared_string, ENT_QUOTES);
  return $prepared_string;
}


//Check the type of archive page whether it's a taxonomy or an author or a custom post type and redirect to the relevant archive page
function mkapis_lite_adjust_redirect($title, $type) {
  global $wpdb;

  //Get the appearance settings
  $mkapis_lite_existing_settings_options = get_option('mkapis_lite_settings_options');

  //check the cases if the term is a custom post type or an author page or a taxonomy
  switch ($type) {

      //Author page case
      case 'author':

      //Check if redirecting to author pages is enabled to enable redirect
      if($mkapis_lite_existing_settings_options['show_author'] === "1") {

        //Get the author ID to get the redirect link
        $author = $wpdb->get_row( $wpdb->prepare(
                "SELECT `ID` FROM $wpdb->users WHERE `display_name` = %s", $title
            ) );

        //Get the redirect link
        $archive_link = get_author_posts_url($author->ID);

        //Redirect to author archive link
        if (! is_wp_error( $archive_link )) {
            wp_safe_redirect( $archive_link );
            exit;
          }
        }
      break;

    default:

    //Check if the redirect is enabled by the user to this taxonomy
    if(   ( $type === 'category' && $mkapis_lite_existing_settings_options['show_categories'] === '1')
       || ( $type === 'post_tag' && $mkapis_lite_existing_settings_options['show_tags'] === '1') ) {
        //Get the term object and archive link
        $related_taxonomy= get_term_by('name', $title, $type);
        $archive_link = get_term_link( $related_taxonomy );

        //Redirect to term archive link
        if (! is_wp_error( $archive_link )) {
          wp_safe_redirect( $archive_link );
          exit;
        }
    break;
      }
    }
  }


//Adds the new term to the database once it is created
function mkapis_lite_add_new_terms($term_id, $tt_id, $taxonomy) {

  //Get needed archive names options (to add the new term to them)
  $existing_archives = get_option('mkapis_lite_archive_names');
  $existing_archives_total = get_option('mkapis_lite_all_archive_names');

  //Get needed settings options (to check if the taxonomy is included by the user)
  $settings_options = get_option('mkapis_lite_settings_options');

  //In case taxonomy is included
  if ( $taxonomy === 'category' || $taxonomy === 'post_tag' ) {

  //Get all term and taxonomy related data
  $term_object = get_term($term_id);
  $taxonomy_object = get_taxonomy($taxonomy);

  //Otherwise add the term name directly to the archive names options
  $existing_archives[$term_object->name] = $taxonomy;
  $existing_archives_total[$term_object->name] = $taxonomy;

  //Update options in the database
  update_option('mkapis_lite_archive_names', $existing_archives);

    }
  }

add_action( 'create_term', 'mkapis_lite_add_new_terms', 10, 3 );


//Add the new author to the database once he is registered
function mkapis_lite_add_new_user() {

  //  Get needed archive names options (to add the new post type to them)
  $existing_archives = get_option('mkapis_lite_archive_names');

    //  Get all authors
    $authors = get_users(array('who'=>'authors'));

    // Get the names of all authors
    $author_names = array();

    foreach ($authors as $author) {
      array_push($author_names, $author->display_name);
    }

    //  Loop through author names
    foreach ($author_names as $author_name) {

      //  If an author is not in the all_archive_names option, he shall be added to both options
      if (!array_key_exists($author_name, $existing_archives)) {

        //  Update archive_names option
        $existing_archives[$author_name] = "author";
        update_option('mkapis_lite_archive_names', $existing_archives);

      }

    }

}

if(isset($_POST['show_author']) && $_POST['show_author'] === "1") {
  add_action( 'admin_init', 'mkapis_lite_add_new_user');
}


//  If the user edited the term name, we shall need to store the old name to use it afterwards
function mkapis_lite_get_term_data_before_edit($term_id) {

  //  Get the old term data
  $term = get_term($term_id);
  $old_terms_existing_option = get_option('mkapis_lite_old_term_names');
  $old_terms_option = array();

  if( !in_array($term->taxonomy, array('category', 'post_tag') ) ) {
    return;
  }

  //  Add the term id as a key and the old name as a value to options table
  if(!isset($old_terms_option)) {
    $old_terms_option[$term_id] = $term->name;
    add_option('mkapis_lite_old_term_names', $old_terms_option);

  } else {

    $old_terms_existing_option[$term_id] = $term->name;
    update_option('mkapis_lite_old_term_names', $old_terms_existing_option);

  }
}
add_action('edit_terms', 'mkapis_lite_get_term_data_before_edit');


//  Get the old term name, unset it from archive_names options and change mkapis post title
function mkapis_lite_set_new_term_data($term_id, $tt_id, $taxonomy) {

  //  Get new term data
  $term = get_term($term_id);

  //  Get old term data
  $old_terms_existing_option = get_option('mkapis_lite_old_term_names');

  //  Get archive_names options data
  $archive_terms = get_option('mkapis_lite_archive_names');

  //  Get the existing mkapis post id
  $old_term_name = $old_terms_existing_option[$term_id];
  $new_term_name = $term->name;

  //  Update archive_names options to remove old name and replace them with new name
  if(array_key_exists($old_term_name, $archive_terms)) {
    unset($archive_terms[$old_term_name]);
  }

  $archive_terms[$new_term_name] = $taxonomy;

  //  Update new name in the database
  update_option('mkapis_lite_archive_names', $archive_terms);

  }
add_action( 'edited_term' , 'mkapis_lite_set_new_term_data' , 10 , 3 );
