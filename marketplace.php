<?php

function toutrix_get_adtype_name($adtypeId) {
  if($adtypeId==1)
    return "Popunder";
  if($adtypeId==2)
    return "300x250";
  if($adtypeId==2)
    return "300x250";
  if($adtypeId==3)
    return "728x90";
  if($adtypeId==4)
    return "160x600";
  if($adtypeId==5)
    return "468x60";
  if($adtypeId==6)
    return "336x270";
  if($adtypeId==7)
    return "300x600";
  if($adtypeId==8)
    return "320x50";
  if($adtypeId==9)
    return "Text ad";
  if($adtypeId==10)
    return "120x600";
}

function ToutrixVolumeSort($item1,$item2)
{
    if ($item1->nbr_impressions == $item2->nbr_impressions) return 0;
    return ($item1->nbr_impressions < $item2->nbr_impressions) ? 1 : -1;
}

function mt_toutrix_marketplace_page() {
  global $toutrix_adserver;
  toutrix_get_token();

  $zone = new stdClass();
  $zone->id = get_option("ad_toutrix_zone_id");
  $zone = $toutrix_adserver->zone_get($zone);

  if (empty($_GET['subpage'])) {
    echo "<h2>Marketplace</h2>";
    echo "The volume may be little higher because it's only sold volume.";
    $marketplace = $toutrix_adserver->marketplace_list(null);

    usort($marketplace,'ToutrixVolumeSort');

    $table = new toutrix_marketplace_table();
    $table->set_datas($marketplace);
    $table->prepare_items();
    $table->display();
  }
}

