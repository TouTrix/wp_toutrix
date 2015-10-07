<?
function toutrix_site_show_stats($site) {
  global $toutrix_adserver;
  toutrix_get_token();

  $toutrix_website_id  = get_option("ad_toutrix_website_id");

  $fields = new stdClass();
  $fields->id = $toutrix_website_id;
  if (isset($_GET['startDate'])) {
    $fields->startDate = $_GET['startDate'];
    $fields->endDate = $_GET['endDate'];
  } else {
    $fields->startDate = date("m/01/Y");
    $fields->endDate = date("m/t/Y");
  }
  $stats = $toutrix_adserver->site_report($fields);
?>
<h1>Statistique</h1>

<form method='GET'>
<input type='hidden' name='page' value='mt_toutrix_stats_page'>
Start date: <input type='text' name='startDate' value='<?php echo $fields->startDate; ?>'><br/>
End date: <input type='text' name='endDate' value='<?php echo $fields->endDate; ?>'><br/>
<input type='submit' name='b' value='Go'><br/>
</form>
<?php
$cur_tab = 'homepage';	
  if (isset($_GET['tab']))
    $cur_tab = $_GET['tab'];

  $tabs = array( 'homepage' => 'Per day', 'per_country' => 'Per country');
  echo '<div id="icon-themes" class="icon32"><br></div>';
  echo '<h2 class="nav-tab-wrapper">';
  foreach( $tabs as $tab => $name ){
      $class = ( $tab == $cur_tab ) ? ' nav-tab-active' : '';
      echo "<a class='nav-tab$class' href='?page=mt_toutrix_stats_page&tab=$tab'>$name</a>";
  }
  echo '</h2>';

  if ($cur_tab == 'homepage') {
?>
<?php
  //var_dump($stats->stats);
  //echo "<hr/>";
  $stats_per_day = $stats->stats->per_day;
?>
<table class="wp-list-table widefat fixed striped posts">
  <tr><th>Day</th><th>Nbr. impressions</th><th>Nbr. clicks</th><th>Own impressions</th><th>Own clicks</th><th>Revenu</th></tr>
<?php
  $total_nbr_impressions = 0;
  $total_nbr_clicks = 0;
  $total_own_clicks = 0;
  $total_own_impressions = 0;
  $total_cost = 0;
  foreach ($stats_per_day as $day => $country) {
    $total_nbr_impressions += $country->nbr_impressions;
    $total_nbr_clicks += $country->nbr_clicks;
    $total_own_clicks += $country->own_clicks;
    $total_own_impressions += $country->own_impressions;
    $total_cost += $country->cost;

    echo "  <tr><td>" . $day . "</td><td>" . $country->nbr_impressions . "</td><td>" . $country->nbr_clicks . "</td><td>" . $country->own_impressions . "</td><td>" . $country->own_clicks . "</td><td>$" . number_format($country->cost,4) . "</td></tr>";
  }
  echo "  <tr><td>Total:</td><td>" . $total_nbr_impressions . "</td><td>" . $total_nbr_clicks . "</td><td>" . $total_own_impressions . "</td><td>" . $total_own_clicks . "</td><td>$" . number_format($total_cost,4) . "</td></tr>";
?>
</table>
<?php
  } else {
  //var_dump($stats->stats);
  //echo "<hr/>";
  $stats_per_day = $stats->stats->per_country;
?>
<table class="wp-list-table widefat fixed striped posts">
 <thead>
  <tr><th>Country</th><th scope="col" id='nbr_impressions' class='manage-column column-title column-primary sortable desc'>Nbr. impressions</th><th>Nbr. clicks</th><th>Own impressions</th><th>Own clicks</th><th>Revenu</th></tr>
 </thead>
 <tbody id="the-list">
<?php
  $total_nbr_impressions = 0;
  $total_nbr_clicks = 0;
  $total_own_clicks = 0;
  $total_own_impressions = 0;
  $total_cost = 0;
  foreach ($stats_per_day as $country_code => $country) {
    $total_nbr_impressions += $country->nbr_impressions;
    $total_nbr_clicks += $country->nbr_clicks;
    $total_own_clicks += $country->own_clicks;
    $total_own_impressions += $country->own_impressions;
    $total_cost += $country->cost;

    echo "  <tr><td>" .$country_code . " <img src='" . plugins_url( 'flags/' . strtolower($country_code) . '.png', __FILE__ ) . "'></td><td>" . $country->nbr_impressions . "</td><td>" . $country->nbr_clicks . "</td><td>" . $country->own_impressions . "</td><td>" . $country->own_clicks . "</td><td>$" . number_format($country->cost,4) . "</td></tr>";
  }
  echo "  <tr><td>Total:</td><td>" . $total_nbr_impressions . "</td><td>" . $total_nbr_clicks . "</td><td>" . $total_own_impressions . "</td><td>" . $total_own_clicks . "</td><td>$" . number_format($total_cost,4) . "</td></tr>";
?>
  </tbody>
</table>
<?php
  }
}
?>
