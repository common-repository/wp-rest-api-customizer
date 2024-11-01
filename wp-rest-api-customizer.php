<?php
/*
Plugin Name: WP Rest API Customizer
Plugin URI: https://www.geekdashboard.com/
Description: Customize the WP REST API endpoints to improve your app speed and performance by adding or removing objects fron JSON response.
Version: 1.0
Author: ikva eSolutions
Author URI: http://ikvaesolutions.com/
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


define('WPRAC_PLUGIN_NAME', 'WP Rest API Customizer');
define('WPRAC_PLUGIN_SETTINGS_SLUG', 'wp-rest-api-customizer');


add_action('admin_menu', 'wprac_admin_menu');

if(get_option('wprac_img_url', 0))
  add_action( 'rest_api_init', 'wprac_ws_register_images_field' ); // IMAGES

add_filter( 'rest_prepare_post', 'wprac_my_rest_prepare_post', 10, 3 ); //UNSET FIELDS

if(get_option('wprac_allow_comments', 0))
  add_filter('rest_allow_anonymous_comments','wprac_filter_rest_allow_anonymous_comments'); //ALLOW COMMENTS

if(get_option('wprac_url-to-id', 0))
  add_action( 'rest_api_init', 'wprac_geekdashboard_register_getpostid_routes'); //POST ID FROM POST URL

function ikva_wprac_custom_nonce_message ($translation) {
  if ($translation == 'Are you sure you want to do this?')
    return 'Good try! But this is not allowed';
  else
    return $translation;
  }

add_filter('gettext', 'ikva_wprac_custom_nonce_message');

function wprac_ws_register_images_field() {
    register_rest_field( 
        'post',
        'images',
        array(
            'get_callback'    => 'wprac_ws_get_images_urls',
            'update_callback' => null,
            'schema'          => null,
        )
    );
}

function wprac_ws_get_images_urls( $object, $field_name, $request ) {
    $medium = wp_get_attachment_image_src( get_post_thumbnail_id( $object->id ), 'medium' );
    $medium_url = $medium['0'] == null ? "" : $medium['0'];

    $large = wp_get_attachment_image_src( get_post_thumbnail_id( $object->id ), 'large' );
    $large_url = $large['0'] == null ? "" : $large['0'];

    $small = wp_get_attachment_image_src( get_post_thumbnail_id( $object->id ), 'small' );
    $small_url = $small['0'] == null ? "" : $small['0'];

    add_option('onemorethubf', 'enalbed');

    return array(
        'small' =>  $small_url,
        'medium' => $medium_url,
        'large'  => $large_url
    );
}

function wprac_my_rest_prepare_post( $data, $post, $request ) {
	$_data = $data->data;
	$params = $request->get_params();

	if ( !isset( $params['id'] ) ) {

        if(get_option('wprac_unset_id', 0))
          unset( $_data['id'] );


        if(get_option('wprac_unset_title', 0))
          unset( $_data['title'] );

        if(get_option('wprac_unset_content', 0))
		      unset( $_data['content'] );

        if(get_option('wprac_unset_date_gmt', 0))
          unset( $_data['date_gmt'] );
        
        if(get_option('wprac_unset_guid', 0))
          unset( $_data['guid'] );
        
        if(get_option('wprac_unset_date', 0))
          unset( $_data['date'] );
        
        if(get_option('wprac_unset_modified', 0))
          unset( $_data['modified'] );
        
        if(get_option('wprac_unset_modified_gmt', 0))
          unset( $_data['modified_gmt'] );
        
        if(get_option('wprac_unset_slug', 0))
          unset( $_data['slug'] );
        
        if(get_option('wprac_unset_status', 0))
          unset( $_data['status'] );
        
        if(get_option('wprac_unset_type', 0))
          unset( $_data['type'] );
        
        if(get_option('wprac_unset_link', 0))
          unset( $_data['link'] );
        
        if(get_option('wprac_unset_excerpt', 0))
          unset( $_data['excerpt'] );
        
        if(get_option('wprac_unset_author', 0))
          unset( $_data['author'] );
        
        if(get_option('wprac_unset_featured_media', 0))
          unset( $_data['featured_media'] );
        
        if(get_option('wprac_unset_comment_status', 0))
          unset( $_data['comment_status'] );
        
        if(get_option('wprac_unset_ping_status', 0))
          unset( $_data['ping_status'] );
        
        if(get_option('wprac_unset_sticky', 0))
          unset( $_data['sticky'] );
        
        if(get_option('wprac_unset_template', 0))
          unset( $_data['template'] );
        
        if(get_option('wprac_unset_format', 0))
          unset( $_data['format'] );
        
        if(get_option('wprac_unset_meta', 0))
          unset( $_data['meta'] );
        
        if(get_option('wprac_unset_categories', 0))
          unset( $_data['categories'] );
        
        if(get_option('wprac_unset_tags', 0))
          unset( $_data['tags'] );
	}

	$data->data = $_data;
	return $data;
}

function wprac_filter_rest_allow_anonymous_comments() {
    return true;
}


function wprac_geekdashboard_register_getpostid_routes() {
    register_rest_route( 'wp-rest-api-customizer', 'url-to-id', array(
        'methods'  => WP_REST_Server::READABLE,
        'callback' => 'wprac_get_post_id_from_url',
    ) );
}

function wprac_get_post_id_from_url( WP_REST_Request $request ) {

	$id = 0; 
  $arr = array();

	if($id == 0) {
		$arr[] = array("status" => '0', 'message' => 'no post found');
	} else {
		$arr = array("status" => '1', 'id' => $id);
	}

    $myJSON = json_encode($arr);
    echo $myJSON;
}


function wprac_admin_menu() {
    if(function_exists('add_menu_page')) {

      add_options_page(WPRAC_PLUGIN_NAME, WPRAC_PLUGIN_NAME, 'manage_options', WPRAC_PLUGIN_SETTINGS_SLUG, 'wprac_plugin_settings_screen');
    }
  }



  function wprac_plugin_settings_screen() {

    if (!current_user_can('manage_options'))  {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }

    ?>

    <style type="text/css">

.itemDetail {
    background: #fff;
    border: 1px solid #ccc;
    padding: 15px;
    margin: 15px 10px 10px 0;
}
.itemTitle {
    margin-top: 0;
}
      
@media (min-width: 961px) {
    #wprac_main {float:left;width:69%;}
    }
@media (max-width: 960px) {
    #wprac_main {width:100%;}
}
@media (max-width: 782px) {
    #wprac_main input[type="checkbox"] {margin-left: 10px;}
    #wprac_main .wprac_label {display: block; padding-left: 45px; text-indent: -45px;}    
}
  
*, *:before, *:after {
  -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box;
}


.columnsContainer { 
  position: relative; 
}

.rightColumn { 
  padding: 0.5em; 
  width: 100%;
}

  .promotions {
    margin-top: 15px;
  position:fixed;
}


.leftColumn {
 margin-bottom: .5em; 
}


/* MEDIA QUERIES */
@media screen and (min-width: 47.5em ) {
  .leftColumn { margin-right: 19.5em; }

  .rightColumn {  top: 0; right: 0; width: 18.75em; }   
}

@media screen and (max-width: 759px) {
   .promotions {
    position:absolute!important;
  }
}

</style>

        <!-- SETTINGS STARTED -->

      
    <div class="columnsContainer">

      <div class="leftColumn">
         
      <form method="post" action="">

         <?php 

         wp_nonce_field('wprac-settings-update', 'wprac', FALSE, TRUE);



          if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['wprac-update'])) {

              check_admin_referer('wprac-settings-update', 'wprac');

              if (!current_user_can('manage_options'))  {
                 wp_die( __('You do not have sufficient permissions to access this page.') );
              }

              // TRUE - 1
              // FALSE - 0

              if(isset($_POST['wprac_img_url'])) {
                wprac_save_option('wprac_img_url', 1);
              } else {
                wprac_save_option('wprac_img_url', 0);
              }

              if(isset($_POST['wprac_allow_comments'])) {
                wprac_save_option('wprac_allow_comments', 1);
              } else {
                wprac_save_option('wprac_allow_comments', 0);
              }

              if(isset($_POST['wprac_url-to-id'])) {
                wprac_save_option('wprac_url-to-id', 1);
              } else {
                wprac_save_option('wprac_url-to-id', 0);
              }
              
              if(isset($_POST['wprac_unset_id'])) {
                wprac_save_option('wprac_unset_id', 1);
              } else {
                wprac_save_option('wprac_unset_id', 0);
              }

              if(isset($_POST['wprac_unset_title'])) {
                wprac_save_option('wprac_unset_title', 1);
              } else {
                wprac_save_option('wprac_unset_title', 0);
              }

              if(isset($_POST['wprac_unset_content'])) {
                wprac_save_option('wprac_unset_content', 1);
              } else {
                wprac_save_option('wprac_unset_content', 0);
              }

              if(isset($_POST['wprac_unset_guid'])) {
                wprac_save_option('wprac_unset_guid', 1);
              } else {
                wprac_save_option('wprac_unset_guid', 0);
              }

              if(isset($_POST['wprac_unset_date_gmt'])) {
                wprac_save_option('wprac_unset_date_gmt', 1);
              } else {
                wprac_save_option('wprac_unset_date_gmt', 0);
              }


              if(isset($_POST['wprac_unset_date'])) {
                wprac_save_option('wprac_unset_date', 1);
              } else {
                wprac_save_option('wprac_unset_date', 0);
              }



              if(isset($_POST['wprac_unset_modified'])) {
                wprac_save_option('wprac_unset_modified', 1);
              } else {
                wprac_save_option('wprac_unset_modified', 0);
              }


              if(isset($_POST['wprac_unset_modified_gmt'])) {
                wprac_save_option('wprac_unset_modified_gmt', 1);
              } else {
                wprac_save_option('wprac_unset_modified_gmt', 0);
              }

              if(isset($_POST['wprac_unset_slug'])) {
                wprac_save_option('wprac_unset_slug', 1);
              } else {
                wprac_save_option('wprac_unset_slug', 0);
              }
 

              if(isset($_POST['wprac_unset_status'])) {
                wprac_save_option('wprac_unset_status', 1);
              } else {
                wprac_save_option('wprac_unset_status', 0);
              }

              if(isset($_POST['wprac_unset_type'])) {
                wprac_save_option('wprac_unset_type', 1);
              } else {
                wprac_save_option('wprac_unset_type', 0);
              }

              if(isset($_POST['wprac_unset_link'])) {
                wprac_save_option('wprac_unset_link', 1);
              } else {
                wprac_save_option('wprac_unset_link', 0);
              }

              if(isset($_POST['wprac_unset_excerpt'])) {
                wprac_save_option('wprac_unset_excerpt', 1);
              } else {
                wprac_save_option('wprac_unset_excerpt', 0);
              }

              if(isset($_POST['wprac_unset_author'])) {
                wprac_save_option('wprac_unset_author', 1);
              } else {
                wprac_save_option('wprac_unset_author', 0);
              }

              if(isset($_POST['wprac_unset_featured_media'])) {
                wprac_save_option('wprac_unset_featured_media', 1);
              } else {
                wprac_save_option('wprac_unset_featured_media', 0);
              }


              if(isset($_POST['wprac_unset_comment_status'])) {
                wprac_save_option('wprac_unset_comment_status', 1);
              } else {
                wprac_save_option('wprac_unset_comment_status', 0);
              }


              if(isset($_POST['wprac_unset_ping_status'])) {
                wprac_save_option('wprac_unset_ping_status', 1);
              } else {
                wprac_save_option('wprac_unset_ping_status', 0);
              }


              if(isset($_POST['wprac_unset_sticky'])) {
                wprac_save_option('wprac_unset_sticky', 1);
              } else {
                wprac_save_option('wprac_unset_sticky', 0);
              }

              if(isset($_POST['wprac_unset_template'])) {
                wprac_save_option('wprac_unset_template', 1);
              } else {
                wprac_save_option('wprac_unset_template', 0);
              }

              if(isset($_POST['wprac_unset_format'])) {
                wprac_save_option('wprac_unset_format', 1);
              } else {
                wprac_save_option('wprac_unset_format', 0);
              }

              if(isset($_POST['wprac_unset_meta'])) {
                wprac_save_option('wprac_unset_meta', 1);
              } else {
                wprac_save_option('wprac_unset_meta', 0);
              }

              if(isset($_POST['wprac_unset_categories'])) {
                wprac_save_option('wprac_unset_categories', 1);
              } else {
                wprac_save_option('wprac_unset_categories', 0);
              }

              if(isset($_POST['wprac_unset_tags'])) {
                wprac_save_option('wprac_unset_tags', 1);
              } else {
                wprac_save_option('wprac_unset_tags', 0);
              }

          }

         ?>

        <ul>
          <li class="itemDetail" style="display: list-item;">
            <h2 class="itemTitle">Add new objects</h2>
              <table class="form-table"> 
                <tbody>

                  <!-- ADDING NEW OBJECTS - PART I -->
                  <tr valign="top" style="display: table-row;">
                    <th scope="row">Add large, medium and small image url's?</th>
                    <td><label class="wprac_label"><input <?php echo get_option('wprac_img_url',0) == 1 ? 'checked' : '' ?> id="wprac_img_url" type="checkbox" name="wprac_img_url">
                                This will add new key <code>images</code> to get <code>medium</code> and <code>small</code> featured image url's
                        </label>
                    </td>
                  </tr>

                  <tr valign="top" style="display: table-row;">
                    <th scope="row">Allow adding comments for non-logged in users?</th>
                    <td><label class="wprac_label"><input <?php echo get_option('wprac_allow_comments',0) == 1 ? 'checked' : '' ?> id="wprac_allow_comments" type="checkbox" name="wprac_allow_comments">
                        WordPress will not allow adding comments from third party clients unless user is properly authorized. By enabling this, anyone from your app can add comments. Rest depends on your comment approval settings.
                        </label>
                    </td>
                  </tr>

                  <tr valign="top" style="display: table-row;">
                    <th scope="row">Add new end point to get post id from post url?</th>
                    <td><label class="wprac_label"><input <?php echo get_option('wprac_url-to-id',0) == 1 ? 'checked' : '' ?> id="wprac_url-to-id"  type="checkbox" name="wprac_url-to-id">
                        Route: <strong>GET</strong> - <code><?php echo get_option( 'siteurl' );?>/wp-json/wp-rest-api-customizer/url-to-id</code> and send URL in header with key <code>url</code>
                        </label>
                    </td>
                  </tr>

                  <tr>
                    <th>
                    </th>
                    <td style="text-align: right;">
                      <button type="submit" class="button-primary" name="wprac-update">Update options</button>
                    </td>
                    </tr>
                   
                </tbody>
              </table>
            </li>
          </ul>

          <!-- REMOVING EXISTING OBJECTS - PART II - CAUTION REQUIRED
                   -->

          <ul>
            <li class="itemDetail" style="display: list-item;">
              <p style="font-size: 1.3em; margin: 1em 0; font-weight: 600;">Remove unwanted objects from route <code>wp-json/wp/v2/posts/</code></p>
               <p style="color:red">Removing these may break your theme or other plugins. WordPress won't recommened doing it. Make sure you know why you're doing this</p>
                <table class="form-table"> 
                  <tbody>

                  <tr valign="top" style="display: table-row;">
                      <th scope="row">Remove <code>id</code>?</th>
                      <td><label class="wprac_label"><input <?php echo get_option('wprac_unset_id',0) == 1 ? 'checked' : '' ?> type="checkbox" name="wprac_unset_id" id="wprac_unset_id">
                          </label>
                      </td>
                    </tr>


                    <tr valign="top" style="display: table-row;">
                      <th scope="row">Remove <code>title</code>?</th>
                      <td><label class="wprac_label"><input <?php echo get_option('wprac_unset_title',0) == 1 ? 'checked' : '' ?> type="checkbox" name="wprac_unset_title" id="wprac_unset_title">
                          </label>
                      </td>
                    </tr>


                    <tr valign="top" style="display: table-row;">
                      <th scope="row">Remove <code>content</code>?</th>
                      <td><label class="wprac_label"><input <?php echo get_option('wprac_unset_content',0) == 1 ? 'checked' : '' ?> type="checkbox" name="wprac_unset_content" id="wprac_unset_content">
                          </label>
                      </td>
                    </tr>


                    <tr valign="top" style="display: table-row;">
                      <th scope="row">Remove <code>guid</code>?</th>
                      <td><label class="wprac_label"><input <?php echo get_option('wprac_unset_guid',0) == 1 ? 'checked' : '' ?> type="checkbox" name="wprac_unset_guid" id="wprac_unset_guid">
                          </label>
                      </td>
                    </tr>

                   <tr valign="top" style="display: table-row;">
                      <th scope="row">Remove <code>date_mmt</code>?</th>
                      <td><label class="wprac_label"><input <?php echo get_option('wprac_unset_date_gmt',0) == 1 ? 'checked' : '' ?> type="checkbox" name="wprac_unset_date_gmt" id="wprac_unset_date_gmt">
                          </label>
                      </td>
                    </tr>
                  

                  <tr valign="top" style="display: table-row;">
                      <th scope="row">Remove <code>date</code>?</th>
                      <td><label class="wprac_label"><input <?php echo get_option('wprac_unset_date',0) == 1 ? 'checked' : '' ?> type="checkbox" name="wprac_unset_date" id="wprac_unset_date">
                          </label>
                      </td>
                    </tr>

                    <tr valign="top" style="display: table-row;">
                      <th scope="row">Remove <code>modified</code>?</th>
                      <td><label class="wprac_label"><input <?php echo get_option('wprac_unset_modified',0) == 1 ? 'checked' : '' ?> type="checkbox" name="wprac_unset_modified" id="wprac_unset_modified">
                          </label>
                      </td>
                    </tr>

                    <tr valign="top" style="display: table-row;">
                      <th scope="row">Remove <code>modified_gmt</code>?</th>
                      <td><label class="wprac_label"><input <?php echo get_option('wprac_unset_modified_gmt',0) == 1 ? 'checked' : '' ?> type="checkbox" name="wprac_unset_modified_gmt" id="wprac_unset_modified_gmt">
                          </label>
                      </td>
                    </tr>

                    <tr valign="top" style="display: table-row;">
                      <th scope="row">Remove <code>slug</code>?</th>
                      <td><label class="wprac_label"><input <?php echo get_option('wprac_unset_slug',0) == 1 ? 'checked' : '' ?> type="checkbox" name="wprac_unset_slug" id="wprac_unset_slug">
                          </label>
                      </td>
                    </tr>

                    <tr valign="top" style="display: table-row;">
                      <th scope="row">Remove <code>status</code>?</th>
                      <td><label class="wprac_label"><input <?php echo get_option('wprac_unset_status',0) == 1 ? 'checked' : '' ?> type="checkbox" name="wprac_unset_status" id="wprac_unset_status">
                          </label>
                      </td>
                    </tr>

                    <tr valign="top" style="display: table-row;">
                      <th scope="row">Remove <code>type</code>?</th>
                      <td><label class="wprac_label"><input <?php echo get_option('wprac_unset_type',0) == 1 ? 'checked' : '' ?> type="checkbox" name="wprac_unset_type" id="wprac_unset_type">
                          </label>
                      </td>
                    </tr>

                    <tr valign="top" style="display: table-row;">
                      <th scope="row">Remove <code>link</code>?</th>
                      <td><label class="wprac_label"><input <?php echo get_option('wprac_unset_link',0) == 1 ? 'checked' : '' ?> type="checkbox" name="wprac_unset_link" id="wprac_unset_link">
                          </label>
                      </td>
                    </tr>

                    <tr valign="top" style="display: table-row;">
                      <th scope="row">Remove <code>excerpt</code>?</th>
                      <td><label class="wprac_label"><input <?php echo get_option('wprac_unset_excerpt',0) == 1 ? 'checked' : '' ?> type="checkbox" name="wprac_unset_excerpt" id="wprac_unset_excerpt">
                          </label>
                      </td>
                    </tr>

                    <tr valign="top" style="display: table-row;">
                      <th scope="row">Remove <code>author</code>?</th>
                      <td><label class="wprac_label"><input <?php echo get_option('wprac_unset_author',0) == 1 ? 'checked' : '' ?> type="checkbox" name="wprac_unset_author" id="wprac_unset_author">
                          </label>
                      </td>
                    </tr>

                    <tr valign="top" style="display: table-row;">
                      <th scope="row">Remove <code>featured_media</code>?</th>
                      <td><label class="wprac_label"><input <?php echo get_option('wprac_unset_featured_media',0) == 1 ? 'checked' : '' ?> type="checkbox" name="wprac_unset_featured_media" id="wprac_unset_featured_media">
                          </label>
                      </td>
                    </tr>

                    <tr valign="top" style="display: table-row;">
                      <th scope="row">Remove <code>comment_status</code>?</th>
                      <td><label class="wprac_label"><input <?php echo get_option('wprac_unset_comment_status',0) == 1 ? 'checked' : '' ?> type="checkbox" name="wprac_unset_comment_status" id="wprac_unset_comment_status">
                          </label>
                      </td>
                    </tr>

                    <tr valign="top" style="display: table-row;">
                      <th scope="row">Remove <code>ping_status</code>?</th>
                      <td><label class="wprac_label"><input <?php echo get_option('wprac_unset_ping_status',0) == 1 ? 'checked' : '' ?> type="checkbox" name="wprac_unset_ping_status" id="wprac_unset_ping_status">
                          </label>
                      </td>
                    </tr>

                    <tr valign="top" style="display: table-row;">
                      <th scope="row">Remove <code>sticky</code>?</th>
                      <td><label class="wprac_label"><input <?php echo get_option('wprac_unset_sticky',0) == 1 ? 'checked' : '' ?> type="checkbox" name="wprac_unset_sticky" id="wprac_unset_sticky">
                          </label>
                      </td>
                    </tr>

                    <tr valign="top" style="display: table-row;">
                      <th scope="row">Remove <code>template</code>?</th>
                      <td><label class="wprac_label"><input <?php echo get_option('wprac_unset_template',0) == 1 ? 'checked' : '' ?> type="checkbox" name="wprac_unset_template" id="wprac_unset_template">
                          </label>
                      </td>
                    </tr>

                    <tr valign="top" style="display: table-row;">
                      <th scope="row">Remove <code>format</code>?</th>
                      <td><label class="wprac_label"><input <?php echo get_option('wprac_unset_format',0) == 1 ? 'checked' : '' ?> type="checkbox" name="wprac_unset_format" id="wprac_unset_format">
                          </label>
                      </td>
                    </tr>

                    <tr valign="top" style="display: table-row;">
                      <th scope="row">Remove <code>meta</code>?</th>
                      <td><label class="wprac_label"><input <?php echo get_option('wprac_unset_meta',0) == 1 ? 'checked' : '' ?> type="checkbox" name="wprac_unset_meta" id="wprac_unset_meta">
                          </label>
                      </td>
                    </tr>

                    <tr valign="top" style="display: table-row;">
                      <th scope="row">Remove <code>categories</code>?</th>
                      <td><label class="wprac_label"><input <?php echo get_option('wprac_unset_categories',0) == 1 ? 'checked' : '' ?> type="checkbox" name="wprac_unset_categories" id="wprac_unset_categories">
                          </label>
                      </td>
                    </tr>
                    
                     <tr valign="top" style="display: table-row;">
                      <th scope="row">Remove <code>tags</code>?</th>
                      <td><label class="wprac_label"><input <?php echo get_option('wprac_unset_tags',0) == 1 ? 'checked' : '' ?> type="checkbox" name="wprac_unset_tags" id="wprac_unset_tags">
                          </label>
                      </td>
                    </tr>

                    <tr>
                    <th>
                    </th>
                    <td style="text-align: right;">
                      <button type="submit" class="button-primary" name="wprac-update">Update options</button>
                    </td>
                    </tr>

                  </tbody>
                </table>
              </li>
            </ul>

      </form>

  </div>

      <div class="rightColumn">
        <div class="rightColumn promotions">
          <center>
          <div style="margin-top: 2em;">
              
          </div>

          <h4>Do you find our plugin intersting? Please rate 5 stars here</h4>
          <a class="button-primary" target="_blank" href="https://www.geekdashboard.com/recommends/wp-rest-api-customizer">Rate us here</a>

          <div style="margin-top: 2em;">
              <hr/>
          </div>

          <h4>Do you love tech? Consider checking our blog</h4>
          <a class="button-primary" target="_blank" href="https://www.geekdashboard.com/?utm_source=wp-rest-api-customizer&utm_campaign=devwork&utm_medium=<?php echo get_option( 'siteurl' );?>">Visit blog</a>

          <div style="margin-top: 2em;">
              <hr/>
          </div>

          <p>Developed by <a href="http://ikvaesolutions.com/" target="_blank" title="ikva eSolutions">ikva eSolutions</a></p>

          </center>
        </div>
      </div>

    </div>

      <!-- SETTINGS ENDED -->

<?php 
  } 
  
function wprac_save_option($id, $value) {
    $option_exists = (get_option($id, null) !== null);
    if ($option_exists) {
      update_option($id, $value);
    } else {
      add_option($id, $value);
    }
  }

?>
