<?php
require_once "websites.php";
require_once "signup.php";

add_shortcode( 'dashboard', 'toutrix_network_shortcode_callback' );

function toutrix_user_scripts() {
	wp_enqueue_style( 't_admin_load_style', "/wp-admin/load-styles.php?c=0&amp;dir=ltr&amp;load=dashicons,admin-bar,wp-admin,buttons,wp-auth-check,media-views&amp;ver=4.3.1" );
	wp_enqueue_style( 't_table', "/wp-admin/css/list-tables.css" );
	wp_enqueue_style( 'jquery-ui', "http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/flick/jquery-ui.css?ver=4.3.1" );
	wp_enqueue_style( 'tag-it', get_template_directory_uri() . "/css/jquery.tagit.css" );
	wp_enqueue_script( 'jquery-ui', "https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js?ver=4.3.1", array(), '1.0.0', true );
	wp_enqueue_script( 'tag-it', get_template_directory_uri() . '/js/tag-it.js', array(), '4.3.1', true );
}
add_action( 'wp_enqueue_scripts', 'toutrix_user_scripts' );


function toutrix_logout() {
?>
<center>You are logged out.</center>
<?php
}

function toutrix_network_shortcode_callback() {
  //nocache_headers();

  ob_start();
?>
<style>
#wrapper {
  margin-right: 150px;
}
#sidebar {
  float: left;
  width: 150px;
}
#content {
  float: left;
  width: 100%;
  margin-right: -150px;
}
#cleared {
  clear: both;
}
</style>
<?php

//echo "<div class='wrap'>";

global $user_toutrix_access_token;
global $user_toutrix_id;
global $toutrix_adserver;
$toutrix_set_token = '';

  if (isset($_POST['b'])) {
    if ($_POST['b'] == 'Login') {
      if ($toutrix_adserver->login($_POST['username'], $_POST['password'])) {
        $user_toutrix_access_token = $toutrix_adserver->access_token;
        $user_toutrix_id = $toutrix_adserver->userId;
        echo "New access Token is now: " . $toutrix_adserver->access_token . "<br/>";
        $toutrix_set_token = '&toutrix_access_token=' . $user_toutrix_access_token . "&toutrix_user_id=" . $user_toutrix_id;
      } else {
?>
<div class="updated"><p><strong><?php _e('Cant connect with these credentials.', 'menu-test' ); ?></strong></p></div>
<?php
      }
    } elseif ($_POST['b'] == 'Signup') {
      // Read their posted value
      $toutrix_username = sanitize_text_field($_POST[ "username" ]);
      $toutrix_password = sanitize_text_field($_POST[ "password" ]);
      $user = new stdClass();
      $user->realm = $_SERVER['SERVER_NAME'];
      $user->username = sanitize_text_field($_POST[ "username" ]);
      $user->password = sanitize_text_field($_POST[ "password" ]);
      $user->email = sanitize_text_field($_POST[ "email" ]);
      $user->refererId = get_option("ad_toutrix_user_id");

      $user = $toutrix_adserver->user_create($user);
      if (($user == NULL) || ($user->error && $user->error->status <> 500)) {
?>
<div class="updated"><p><strong>ERROR : <?php _e($user->error->message, 'menu-test' ); ?></strong></p></div>
<?php
        $toutrix_username = "";
        $toutrix_password = "";
      } else {
        if ($toutrix_adserver->login($_POST['username'], $_POST['password'])) {
          $user_toutrix_access_token = $toutrix_adserver->access_token;
          $user_toutrix_id = $toutrix_adserver->userId;
          $toutrix_set_token = '&toutrix_access_token=' . $user_toutrix_access_token . "&toutrix_user_id=" . $user_toutrix_id;
        }
?>
<div class="updated"><p><strong><?php _e('TouTrix account is created.', 'menu-test' ); ?></strong></p></div>
<?php
      }
    }
  }

  if (strlen($user_toutrix_access_token)==0) {
    if ($_GET['page']=='signup') {
      toutrix_signup_form();      
    } else {  
?>
<center>Please log in.

<form method='POST'>
Username: <input type='text' name='username'> <br/>
Password: <input type='password' name='password'> <br/>

<input type='submit' name='b' value='Login'>
</form>
<br/><br/>

Or <a href='?page=signup'>Signup here</a> <br/>
<br/>


</center>
<?php
    }
  } else {
?>
<div id='wrapper'>
  <div id="sidebar">

<a href='?page=homepage<?php echo $toutrix_set_token; ?>'>Home</a><br/>
<!-- <a href='?page=websites<?php echo $toutrix_set_token; ?>'>Websites</a><br/>-->
<a href='?page=stats<?php echo $toutrix_set_token; ?>'>Stats</a><br/>
<a href='?page=creative<?php echo $toutrix_set_token; ?>'>Creatives</a><br/>
<a href='?page=mt_toutrix_campaign<?php echo $toutrix_set_token; ?>'>Campaigns</a><br/>
<a href='?page=mt_toutrix_bank<?php echo $toutrix_set_token; ?>'>Bank</a><br/>
<a href='?page=mt_toutrix_marketplace<?php echo $toutrix_set_token; ?>'>Marketplace</a><br/>
<a href='?page=logout'>Logout</a><br/>

  </div>
  <div id="content">
<?php
    if (isset($_GET['page'])) {
      switch ($_GET['page']) {
        case 'signup':
          toutrix_signup_form();
          break;
        case 'stats':
          mt_toutrix_stats_page();
          break;
        case 'mt_toutrix_stats_page':
          mt_toutrix_stats_page();
          break;
        case 'creative':
          toutrix_creative_page();
          break;
        case 'mt_toutrix_creative':
          toutrix_creative_page();
          break;
        case 'toutrix_creative':
          toutrix_creative_page();
          break;
        case 'mt_toutrix_campaign':
          mt_toutrix_campaign_page();
          break;
        case 'mt_toutrix_marketplace':
          mt_toutrix_marketplace_page();
          break;
        case 'mt_toutrix_inventory':
          mt_toutrix_inventory_page();
          break;
        case 'mt_toutrix_bank':
          mt_toutrix_bank_page();
          break;
        case 'websites':
          toutrix_websites_page();
          break;
        case 'logout':
          toutrix_logout();
      }
    }
  }
?>
</div>
  <div id="cleared"></div>
</div>
<?php
  //echo "</div>";
  $page = ob_get_contents();
  ob_end_clean();
  return $page;
}
