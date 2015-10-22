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
    toutrix_echo_funds_available();
?>
<h2>Publishers</h2>

To earn money with ads, you drag a <a href='/wp-admin/widgets.php'>TouTrix Ad Widget</a> in your page.<br/><br/>

<a href='?page=mt_toutrix_stats_page'>Check your stats</a> here.

<h2>Advertisers</h2>

To buy traffic, you need to <a href='?page=mt_toutrix_bank'>make a deposit</a> first, <a href='?page=toutrix_creative'>create a creative</a>, <a href='?page=mt_toutrix_campaign'>create a campaign</a>, add a flight with your targetings and finally, add a creative to your flight.<br/><br/>

<?php
   } elseif ($cur_tab == 'coming') {
     coming_soon();
   } else {
     toutrix_settings_page();
   }
}
