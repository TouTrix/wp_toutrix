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
      echo "<a class='nav-tab$class' href='?page=mt_toutrix_stats_page&tab=$tab&startDate=$fields->startDate&endDate=$fields->endDate'>$name</a>";
  }
  echo '</h2>';

  if ($cur_tab == 'homepage') {
?>
<?php
    //var_dump($stats->stats);
    //echo "<hr/>";
    $stats_per_day = $stats->stats->per_day;

    $table = new stats_revenue_per_day_table();
    $table->set_datas($stats->stats->per_day);
    $table->prepare_items();
    $table->display();
  } else {
    $table = new stats_revenu_per_country_table();
    $table->set_datas($stats->stats->per_country);
    $table->prepare_items();
    $table->display();
  }
}
?>
