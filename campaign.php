<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

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
      echo "<tr><td><a href='?page=mt_toutrix_campaign&campaignId=" . $campaign->id . "'>" . $campaign->id ."</a></td><td>" . $campaign->name ."</td><td></td></tr>";
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
?>
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
