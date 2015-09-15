<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function toutrix_campaign_show_stats($campaign) {
  global $toutrix_adserver;
  toutrix_get_token();

  $fields = new stdClass();
  $fields->id = $_GET['campaignId'];
  if (isset($_GET['startDate'])) {
    $fields->startDate = $_GET['startDate'];
    $fields->endDate = $_GET['endDate'];
  } else {
    $fields->startDate = date("m/01/Y");
    $fields->endDate = date("m/t/Y");
  }
  $stats = $toutrix_adserver->campaign_report($fields);
?>
<h1>Statistique</h1>

<form method='GET'>
<input type='hidden' name='page' value='mt_toutrix_campaign'>
<input type='hidden' name='campaignId' value='<?php echo $_GET['campaignId'];?>'>
<input type='hidden' name='subpage' value='stats'>
Start date: <input type='text' name='startDate' value='<?php echo $fields->startDate; ?>'><br/>
End date: <input type='text' name='endDate' value='<?php echo $fields->endDate; ?>'><br/>
<input type='submit' name='b' value='Go'><br/>
</form>

<h2>Per day</h2>
<?php
  //var_dump($stats->stats);
  //echo "<hr/>";
  $stats_per_day = $stats->stats->per_day;
?>
<div class="CSSTableGenerator">
<table>
  <tr><th>Day</th><th>Nbr. impressions</th><th>Nbr. clicks</th><th>Own impressions</th><th>Own clicks</th><th>Cost</th></tr>
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
</div>

<h2>Per country</h2>
<?php
  //var_dump($stats->stats);
  //echo "<hr/>";
  $stats_per_day = $stats->stats->per_country;
?>
<div class="CSSTableGenerator">
<table>
  <tr><th>Country</th><th>Nbr. impressions</th><th>Nbr. clicks</th><th>Own impressions</th><th>Own clicks</th><th>Cost</th></tr>
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
</table>
</div>
<?php
}

function mt_toutrix_campaign_page() {
  global $toutrix_adserver;
  toutrix_get_token();
  
  if (!empty($_GET['flightId'])) {
    toutrix_flight();
    return;
  }

  if (empty($_GET['campaignId'])) {
    if (!empty($_POST['b'])) {
      $fields = new stdclass();
      $fields->user_id = $adserver->userId;
      $fields->name = $_POST['name'];
      $fields->isDeleted = 0;
      $fields->isActive = 1;
      stripslashes_deep( $fields );
      $campaign = $toutrix_adserver->campaign_create($fields);
//var_dump($campaign);
?>
<div class="updated"><p><strong><?php _e('Campaign added', 'menu-test' ); ?></strong></p></div>
<?php
    }

    echo "<h2>Campaigns</h2>";

    $campaigns = $toutrix_adserver->campaigns_list(array());
?>
<div class="CSSTableGenerator">
<table>
  <tr><th>Id</th><th>Title</th><th>Action</th></tr>
<?php
    foreach ($campaigns as $campaign) {
      echo "<tr><td><a href='?page=mt_toutrix_campaign&campaignId=" . $campaign->id . "'>" . $campaign->id ."</a></td><td>" . $campaign->name ."</td><td><a href='?page=mt_toutrix_campaign&campaignId=" . $campaign->id . "&subpage=stats'>Stats</a></td></tr>";
    }
?>
</table>
</div>

<h2>Create a new campaign</h2>
<?php
    $new = new stdclass();
    toutrix_campaign_form($new);
  } elseif (!empty($_GET['campaignId'])) {
    if (!empty($_POST['b']) && $_POST['target']=='yes') {
      $fields = new stdclass();
      if (!empty($_POST['id']))
        $fields->id = $_POST['id'];
      $fields->campaignId = $_GET['campaignId'];
      $fields->target_type = $_POST['target_type'];
      $fields->target_value = $_POST['target_value']; 
      $fields->isExcept = false;     
      stripslashes_deep( $fields );
//var_dump($fields);
//echo "<br/>";
      $target = $toutrix_adserver->target_create($fields);
//var_dump($target);
?>
<div class="updated"><p><strong><?php _e('Target added', 'menu-test' ); ?></strong></p></div>
<?php
    } elseif (!empty($_POST['b']) && $_POST['flight']=='yes') {
      $fields = new stdclass();
      if (!empty($_POST['id']))
        $fields->id = $_POST['id'];
      $fields->campaignId = $_POST['campaignId'];
      $fields->Name = $_POST['Name'];
      $fields->Price = $_POST['Price'];
      $fields->MaxPerIp = $_POST['MaxPerIp'];
      $fields->IsDeleted = false;
      $fields->IsActive = true;
      $fields->IsUnlimited = true;
      $fields->NoEndDate = true;
      stripslashes_deep( $fields );
//var_dump($fields);
//echo "<br/>";
      $flight = $toutrix_adserver->flight_create($fields);
//var_dump($flight);
    } elseif (!empty($_POST['b'])) {
      $fields = new stdclass();
      $fields->id = $_POST['id'];
      $fields->name = $_POST['name'];
      stripslashes_deep( $fields );
//var_dump($fields);
//echo "<br/>";
      $campaign = $toutrix_adserver->campaign_update($fields);
//var_dump($campaign);
?>
<div class="updated"><p><strong><?php _e('Campaign saved', 'menu-test' ); ?></strong></p></div>
<?php
    }
    $fields = new stdclass();
    $fields->campaignId = $_GET['campaignId'];
    //var_dump($fields); echo "<br/>";
    $campaign = $toutrix_adserver->campaign_get($fields);

    if (isset($_GET['subpage']) && $_GET['subpage']== 'stats') {
      toutrix_campaign_show_stats(campaign);
    } else {
?>
<a href='?page=mt_toutrix_campaign&campaignId=<?php echo $_GET['campaignId'];?>&subpage=stats'>Show stats</a>
<h2>Update campaign</h2>
<?php
      toutrix_campaign_form($campaign);
      //var_dump($campaign);

      toutrix_flights($campaign);
?>
<h2>Targeting for this campaign</h2>
It applies to all flights.<br/>
<?php

      $fields = new stdclass();
      $fields->campaignId = $_GET['campaignId'];
      $targets = $toutrix_adserver->campaign_targets($fields);
      toutrix_show_targets($targets);

      echo "<h2>Add a new target</h2>";
      toutrix_show_target_form($fields);
    }
  }
}

function toutrix_campaign_form($campaign) {
  global $toutrix_adserver;
?>
<form method='POST'>
<?php if (!empty($campaign->id)) {?>
<input type='hidden' name='id' value='<?php echo $campaign->id;?>'>
<?php } ?>
Name: <input type='text' name='name' value='<?php echo $campaign->name;?>'><br/>

<input type='submit' name='b' value='Save'>
</form>
<?php
}

?>
