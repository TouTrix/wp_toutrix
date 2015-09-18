<?php

function get_adtype_name($adtypeId) {
  if($adtypeId==1)
    return "Popunder";
  if($adtypeId==2)
    return "300x250";
}

function mt_toutrix_marketplace_page() {
  global $toutrix_adserver;
  toutrix_get_token();

  $zone = new stdClass();
  $zone->id = get_option("ad_toutrix_zone_id");
  $zone = $toutrix_adserver->zone_get($zone);
  //echo "Zone: ";
  //var_dump($zone);


    if (empty($_GET['subpage'])) {
      echo "<h2>Marketplace</h2>";
      echo "The volume may be little higher because it's only sold volume.";
      $marketplace = $toutrix_adserver->marketplace_list(null);
?>
<div class="CSSTableGenerator">
<table>
  <tr><th>Domain</th><th>Ad Type</th><th>Last 24 hours impressions</th><th>Last 24 hours clicks</th><th>Last 24 hours leads</th></tr>
<?php
      //var_dump($marketplace);
      foreach ($marketplace as $place) {
        if ($place->channelId == $zone->channelId)
          echo "<tr><td>" . $place->domain . "</td><td>" . get_adtype_name($place->adtypeId) . "</td><td><p align='right'>" . $place->nbr_impressions . "</p></td><td><p align='right'>" . $place->nbr_clics . "</p></td><td><p align='right'>" . $place->nbr_leads . "</p></td></tr>";
      }
    }
?>
</table>
</div>
<?php
}
?>
