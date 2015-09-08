<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function flights($campaign) {
  global $adserver;

  echo "<h2>Flights</h2>";
  $fields = new stdclass();
  $fields->campaignId = $campaign->id;
  $flights = $adserver->flights_get($fields);
  //var_dump($flights);
?>
<div class="CSSTableGenerator">
<table>
  <tr><th>ID</th><th>Name</th><th>Price</th><th>Action</th></tr>
<?php
  foreach ($flights as $flight) { 
    echo "<tr><td><a href='?page=mt_campaign&campaignId=" . $_GET['campaignId'] . "&flightId=" . $flight->id . "'>" . $flight->id . "</a></td><td>" . $flight->Name . "</td><td>$" . $flight->Price . "</td><td></td></tr>";
  }
?>
</table>
</div>

<h2>Create a new flight</h2>
<?php
  $flight = new stdclass();
  $flight->max_per_ip = 0;
  $flight->campaignId = $_GET['campaignId'];
  flight_form($flight);
}

function flight() {
  global $adserver;

  if (isset($_GET['activeId']) && $_GET['activeId'] > 0) {
    $fields = new stdclass();
    $fields->id = $_GET['activeId'];
    $fields->IsActive = true;
    $target = $adserver->creative_flight_save($fields);
?>
<div class="updated"><p><strong><?php _e('Creative started', 'menu-test' ); ?></strong></p></div>
<?php
  }

  if (isset($_GET['deactiveId']) && $_GET['deactiveId'] > 0) {
    $fields = new stdclass();
    $fields->id = $_GET['deactiveId'];
    $fields->IsActive = false;
    $target = $adserver->creative_flight_save($fields);
?>
<div class="updated"><p><strong><?php _e('Creative stopped', 'menu-test' ); ?></strong></p></div>
<?php
  }

  if (isset($_GET['removeTargetId']) && $_GET['removeTargetId'] > 0) {
    $fields = new stdclass();
    $fields->id = $_GET['removeTargetId'];
    $fields->IsDeleted = true;
    $fields->IsActive = false;
    $target = $adserver->creative_flight_save($fields);
?>
<div class="updated"><p><strong><?php _e('Creative removed', 'menu-test' ); ?></strong></p></div>
<?php
  }


  if (isset($_POST['target']) && $_POST['target']=='yes') {
    // We are adding a new target
    $fields = new stdclass();
    $fields->flightId = $_GET['flightId'];
    $fields->isExcept = false;
    $fields->target_type = $_POST['target_type'];
    $fields->target_value = $_POST['target_value'];
    $target = $adserver->target_create($fields);
    //var_dump($target);
?>
<div class="updated"><p><strong><?php _e('Flight target added', 'menu-test' ); ?></strong></p></div>
<?php
  }

  $fields = new stdclass();
  $fields->campaignId = $_GET['campaignId'];
  //var_dump($fields); echo "<br/>";
  $campaign = $adserver->campaign_get($fields);

  $fields = new stdclass();
  $fields->campaignId = $_GET['campaignId'];
  $fields->flightId = $_GET['flightId'];
  $flight = $adserver->flights_get($fields);
//echo "Current flight:<br/>";
//var_dump($flight);
//echo "<br/>";

  if (isset($_POST['creative']) && $_POST['creative']=='Y') {
    $fields = new stdclass();
    $fields->flightId = $_GET['flightId'];
    $fields->campaignId = $_GET['campaignId'];
    $fields->creativeId = $_POST['creativeId'];
    $creative_flights = $adserver->creative_flight_create($fields);
//var_dump($creative_flights);
  }

  $fields = new stdclass();
  $fields->flightId = $_GET['flightId'];
  $creative_flights = $adserver->creative_flight_get($fields);

  echo "<h2>Creatives for " . $campaign->name . "</h2>";

  echo "<b>Price:</b> $" . $flight->Price . "<br/>";
?>
<div class="CSSTableGenerator">
<table>
  <tr><th>ID</th><th>Active</th><th>Creative</th><th>Action</th></tr>
<?php
  $all_used = array();
  $i = 0;
  foreach ($creative_flights as $creative_flight) {
    $i++;
    if ($creative_flight->IsActive)
      $active = "<a href='?page=mt_campaign&campaignId=17&flightId=2335&deactiveId=" . $creative_flight->id . "'>Yes</a>";
    else
      $active = "<a href='?page=mt_campaign&campaignId=17&flightId=2335&activeId=" . $creative_flight->id . "'>No</a>";
    echo "<tr><td>" . $creative_flight->id . "</td><td>" . $active ."</td><td>";
    $first = true;
//echo "--";
//var_dump($creatives);

    $creative = new stdclass();
    $creative->creativeId = $creative_flight->creativeId;
    $creative = $adserver->creative_get($creative);

    $all_used[$creative->id] = 1;

    echo "<span title='Creative # " . $creative->id . "'>";
    echo "#" . $creative->id . " - ";
    if (!empty($creative->banner_url)) {
      echo "<img src='" .  $creative->banner_url . "'>";
    } else {
      echo "Site URL: " .  $creative->url;
    }
    echo "</span><br/>";
    $first = false;

    echo "</td><td><a href='?page=mt_campaign&campaignId=" . $_GET['campaignId'] . "&flightId=" . $_GET['flightId'] . "&removeTargetId=" . $creative_flight->id . "'><img src='/wp-content/plugins/wp_toutrix/images/Remove.png' height='25' width='25' border='0'></a></td></tr>";
  }
  if ($i==0) {
    echo "<tr><td colspan='4'>No creative added yet. You should add at least one.</td></tr>";
  }
?>
</table>
</div>

<h2>Add a creative to this flight</h2>
<?php
  $creatives = $adserver->creatives_list(array());
?>
<form method='POST'>
<input type='hidden' name='creative' value='Y'>
<select name='creativeId'>
<?php 
foreach ($creatives as $one_crea) {
  if ($all_used[$one_crea->id] <> 1)
    echo "<option value='" . $one_crea->id . "'>" . $one_crea->title . "</option>";
}
?>
</select>
<input type='submit' name='b' value='Add'>
</form>
<?php

  echo "<h2>Targets for " . $campaign->name . "</h2>";
  $fields = new stdclass();
  $fields->flightId = $_GET['flightId'];
  $targets = $adserver->flight_targets_get($fields);
  show_targets($targets);

  $fields = new stdclass();
  $fields->flightId = $_GET['flightId'];

  echo "<h2>Add a new target</h2>";
  show_target_form($fields);
}

function flight_form($flight) {
?>
<form method='POST'>
<input type='hidden' name='flight' value='yes'>
<?php if (!empty($flight->id)) { ?>
<input type='hidden' name='id' value='<?php echo $flight->id; ?>'>
<?php } ?>
<input type='hidden' name='campaignId' value='<?php echo $flight->campaignId; ?>'>
Name : <input type='text' name='Name' value='<?php echo $flight->Name; ?>'><br/>
Price : <input type='text' name='Price' value='<?php echo $flight->Price; ?>'><br/>
Max per IP : <input type='text' name='MaxPerIp' value='<?php echo $flight->MaxPerIp; ?>'> (0-unlimited)<br/>
<input type='submit' name='b' value='Save'>
</form>
<?php
}
?>
