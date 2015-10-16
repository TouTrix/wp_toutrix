<?php
function toutrix_settings_page() {
    //must check that the user has the required capability 
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }

    global $toutrix_adserver;
    echo "<script src='http://serv.toutrix.com/serv/tag?tagId=1'></script>";
    echo "<div class='container'>";

    echo "<center><a href='http://toutrix.com/2015/09/07/we-are-looking-for-developpers/'>We are looking for developpers</a></center><br/>";

    // Read in existing option value from database
    $toutrix_username = get_option("ad_toutrix_username");
    $toutrix_password  = get_option("ad_toutrix_password");
    $toutrix_access_token  = get_option("ad_toutrix_access_token");
    $toutrix_website_id  = get_option("ad_toutrix_website_id");
    $toutrix_zone_id  = get_option("ad_toutrix_zone_id");

    if( isset($_POST["wp_config"]) && $_POST[ "wp_config" ] == 'Y' ) {
        $is_skimmed = 0;
        if ($_POST['is_skimmed']=='on')
          $is_skimmed = 1;
        update_option( "ad_toutrix_skimmed_enabled", $is_skimmed);

        $replace_links = 0;
        if ($_POST['replace_links']=='on')
          $replace_links = 1;
        update_option( "ad_toutrix_replace_links", $replace_links);

?>
<div class="updated"><p><strong><?php _e($user->error->message, 'menu-test' ); ?></strong></p></div>
<?php
    } elseif( isset($_POST[ "signup" ]) && $_POST[ "signup" ] == 'Y' ) {
        update_option( "ad_toutrix_access_token", "" );
        update_option( "ad_toutrix_website_id", "" );
        update_option( "ad_toutrix_zone_id", "" );
        update_option( "ad_channel_id", intval($_POST[ 'channelId']) );

        // Read their posted value
        $toutrix_username = sanitize_text_field($_POST[ "ad_toutrix_username" ]);
        $toutrix_password = sanitize_text_field($_POST[ "ad_toutrix_password" ]);
        $user = new stdClass();
        $user->username = sanitize_text_field($_POST[ "ad_toutrix_username" ]);
        $user->password = sanitize_text_field($_POST[ "ad_toutrix_password" ]);
        $user->email = sanitize_text_field($_POST[ "ad_toutrix_email" ]);
        $user->refererId = intval($_POST[ "refererId" ]);
//var_dump($adserver);
        $user = $toutrix_adserver->user_create($user);
        if ($user->error && $user->error->status <> 500) {
?>
<div class="updated"><p><strong><?php _e($user->error->message, 'menu-test' ); ?></strong></p></div>
<?php
          $toutrix_username = "";
          $toutrix_password = "";
        } else {
          update_option( "ad_toutrix_username", $toutrix_username  );
          update_option( "ad_toutrix_password", $toutrix_password  );
?>
<div class="updated"><p><strong><?php _e('TouTrix account is created.', 'menu-test' ); ?></strong></p></div>
<?php
        }
    }

    if( isset($_POST[ "config" ]) && $_POST[ "config" ] == 'Y' ) {
        // Read their posted value
        $toutrix_username = sanitize_text_field($_POST[ "ad_toutrix_username" ]);
        $toutrix_password = sanitize_text_field($_POST[ "ad_toutrix_password" ]);

        // Save the posted value in the database
        update_option( "ad_toutrix_username", $toutrix_username  );
        update_option( "ad_toutrix_password", $toutrix_password  );

        // Put a "settings saved" message on the screen

?>
<div class="updated"><p><strong><?php _e('settings saved.', 'menu-test' ); ?></strong></p></div>
<?php
    }
	
  if (strlen($toutrix_username)==0 && strlen($toutrix_password)==0) {
     $channels = toutrix_get_channels();
     echo '<div class="wrap">';
?>

<h1>Create your account now</h2>
Fill-up the form to create your account now.<br/>
<form name="form1" method="post" action="">
<input type="hidden" name="signup" value="Y">
<input type="hidden" name="refererId" value="<?php echo toutrix_referer_id; ?>">
<input type="hidden" name="tab" value="setting">

<p><?php _e("Username:", 'menu-test' ); ?> 
<input type="text" name="ad_toutrix_username" value="<?php echo $toutrix_username; ?>" size="20">
</p>

<p><?php _e("Password:", 'menu-test' ); ?> 
<input type="text" name="ad_toutrix_password" value="<?php echo $toutrix_password; ?>" size="20">
</p>

<p><?php _e("Channel:", 'menu-test' ); ?> 
<select name='channelId'>
<?php foreach ($channels as $channel) { ?>
<option value='<?php echo $channel->id; ?>'><?php echo $channel->Title; ?></option>
<?php } ?>
</select> Choose the good channel for your website. We may change it for you later.
</p>

<p><?php _e("Your email:", 'menu-test' ); ?> 
<input type="text" name="ad_toutrix_email" value="<?php echo $toutrix_email; ?>" size="20">
</p>

<hr />

<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php _e("Signup on TouTrix", 'menu-test'); ?>" />
</p>

</form>
</div>

<?php } elseif (strlen($toutrix_username)>0 && strlen($toutrix_password)>0) {
       //echo "Getting token<br/>";
       if (toutrix_get_token()) {
          //echo "Got Token<br/>";
          $toutrix_access_token  = get_option("ad_toutrix_access_token");
          $toutrix_website_id  = get_option("ad_toutrix_website_id");
          $toutrix_channel_id  = get_option("ad_channel_id");

          if (strlen($toutrix_website_id)==0) {
              echo "Creating website...<br/>";              

              $site = new stdClass();
              $site->Title = get_bloginfo();;
              $site->Url = get_site_url();
              $site->Description = get_bloginfo ( 'description' );
              $site->channelId = $toutrix_channel_id;
//var_dump($site);
//echo "<br/>";
              $site = $toutrix_adserver->site_create($site);
//              echo "Create website: ";
//              var_dump($site);
//echo "<br/>";
              if ($site->id > 0) {
                update_option( "ad_toutrix_website_id", $site->id);
                $toutrix_website_id = $site->id;
              } else {
                echo "<font color='red'>Can't find WebSite ID</font>";
              }
              echo "<br/>";
          }
          if (strlen($toutrix_zone_id)==0 && strlen($toutrix_website_id)>0) {
              //echo "Creating zone...<br/>";
              $zone = new stdClass();
              $zone->Name = get_bloginfo();
              $zone->siteId = $toutrix_website_id;
              $zone->channelId = $toutrix_channel_id;
	
              $zone = $toutrix_adserver->zone_create($zone);
              echo "Create zone: ";
              //var_dump($zone);
              if ($zone->id > 0) {
                update_option( "ad_toutrix_zone_id", $zone->id);
                $toutrix_zone_id = $zone->id;
              } else {
                echo "<font color='red'>Can't find Zone ID</font>";
              }
              echo "<br/>";
          }
       }
    }

  $is_skimmed = get_option("ad_toutrix_skimmed_enabled");
  $replace_links = get_option("ad_toutrix_replace_links");
?>
<div class='wrap'>
<h1>AdServer setting</h1>
<form name="form_setting" method="POST">
<input type="hidden" name="wp_config" value="Y">
<!--<table class="form-table">-->
<table class="form-table">
<tr>
<td><input type="checkbox" name="is_skimmed" <?php if ($is_skimmed == 1) echo "checked"; ?>></td>
<td><?php _e("Skimmed some traffic", 'toutrix' ); ?></td>
</tr>

<tr>
<td><input type="checkbox" name="replace_links" <?php if ($replace_links == 1) echo "checked"; ?>></td>
<td><?php _e("Replace links with bitcoin address referer", 'toutrix' ); ?></td>
</tr>

</table>
<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</p>
</form>
</div>

<h1>Connect with your TouTrix account</h2>
<?php
    echo '<div class="wrap">';
    ?>

<form name="form1" method="post" action="">
<input type="hidden" name="config" value="Y">

<table class="form-table">

<tr><td><?php _e("TouTrix Username:", 'menu-test' ); ?></td>
<td><input type="text" name="ad_toutrix_username"" value="<?php echo $toutrix_username; ?>" size="20"></td>
</tr>

<tr><td><?php _e("TouTrix password:", 'menu-test' ); ?></td>
<td><input type="text" name="ad_toutrix_password"" value="<?php echo $toutrix_password; ?>" size="20"></td>
</tr>

</table>

<?php
  if (strlen($toutrix_username) > 0 && strlen($toutrix_password)>0) {
?>
<p><?php _e("Access Token:", 'menu-test' ); ?> 
<?php echo $toutrix_access_token; ?>
</p>

<p><?php _e("Website ID:", 'menu-test' ); ?> 
<?php echo $toutrix_website_id; ?>
</p>

<p><?php _e("Default Zone ID:", 'menu-test' ); ?> 
<?php echo $toutrix_zone_id; ?>
</p>

<hr />
<?php
  }
?>

<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</p>

</form>

<?php
    echo "</div>";

  if (strlen($toutrix_username) > 0 && strlen($toutrix_password)>0) {
    toutrix_user_form();
  }
}

