<?php

function mt_toutrix_page() {
  //must check that the user has the required capability 
  if (!current_user_can('manage_options')) {
    wp_die( __('You do not have sufficient permissions to access this page.') );
  }

  global $toutrix_adserver;

  $cur_tab = 'homepage';	
  if (isset($_GET['tab']))
    $cur_tab = $_GET['tab'];

  $toutrix_username = get_option("ad_toutrix_username");
  $toutrix_password  = get_option("ad_toutrix_password");
  $toutrix_access_token  = get_option("ad_toutrix_access_token");

  $redirect = false;
  if (strlen($toutrix_username)==0 && strlen($toutrix_password)==0)
    $redirect = true;

  if ($redirect && $cur_tab <> "setting") {
    echo "<a href='/wp-admin/admin.php?page=mt_toutrix_page-handle&tab=setting'>Redirecting....</a>";
    echo "<script>window.location.href='http://" . $_SERVER['SERVER_NAME'] . "/wp-admin/admin.php?page=mt_toutrix_page-handle&tab=setting';</script>";
    exit;
  }

  echo "<script src='http://serv.toutrix.com/serv/tag?tagId=1'></script>";


    $tabs = array( 'homepage' => 'Homepage', 'setting' => 'Settings', 'coming' => 'Coming soon', 'support' => 'Support');
    echo '<div id="icon-themes" class="icon32"><br></div>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach( $tabs as $tab => $name ){
      $class = ( $tab == $cur_tab ) ? ' nav-tab-active' : '';
      if ($tab == 'support') {
        echo "<a class='nav-tab' href='http://toutrix.com/forums/forum/wordpress-adserver-plugin/' target='_blank'>$name</a>";
      } else {
        echo "<a class='nav-tab$class' href='?page=mt_toutrix_page-handle&tab=$tab'>$name</a>";
      }
    }

    echo '</h2>';

   if ($cur_tab == 'homepage') {
    echo "Current version: " . toutrix_plugin_version . "<br/><br/>";
    echo_funds_available();
?>
<h2>Publishers</h2>

To earn money with ads, you drag a TouTrix Ad Widget in your page.<br/><br/>

<h2>Advertisers</h2>

To buy traffic, you need to make a deposit first, create a creative, create a campaign, add a flight with your targetings and finally, add a creative to your flight.<br/><br/>

<?php
   } elseif ($cur_tab == 'coming') {
     coming_soon();
   } else {

     toutrix_settings_page();
   }
}
