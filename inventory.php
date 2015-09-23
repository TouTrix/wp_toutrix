<?php

function mt_toutrix_inventory_page() {
  global $toutrix_adserver;

  $inventory = $toutrix_adserver->inventory();

  $inventories = array();

  foreach ($inventory as $inv) {
    $new_inv = array('adtypeId'=>$inv->adtypeId,'title'=>$inv->title,'targets'=>$inv->targets, 'Price'=>$inv->Price);
    $inventories[] = $new_inv;
  }
  //var_dump($inventories);

function PriceSort($item1,$item2)
{
    if ($item1['Price'] == $item2['Price']) return 0;
    return ($item1['Price'] < $item2['Price']) ? 1 : -1;
}
usort($inventories,'PriceSort');

?>
<div class="CSSTableGenerator">
<table>
  <tr><th>Ad Type</th><th>Title</th><th>Price</th><th>Targets</th></tr>
<?php
foreach ($inventories as $num => $inv) {
  if ($inv['adtypeId'] < 1)
    continue;
  $inv["title"] = "Hidden";
  if ($inv["adtypeId"]==1)
    $adtype = "PopUnder";
  elseif ($inv["adtypeId"]==2)
    $adtype = "300x250";
  else
    $adtype = $inv['adtypeId'];

  echo "<tr><td>" . $adtype ."</td><td>" . $inv["title"] ."</td><td>$" . number_format($inv["Price"]/2,2) ."</td><td>";
  $targets = json_decode($inv["targets"], true);
  foreach ($targets as $code => $values) {
    //var_dump($values);
    if ($values['target_type']=='country') {
      $countries = json_decode($values['target_value'], true);
      echo "Country: " ;
      foreach ($countries as $country_code) {
        $lcountry = strtolower($country_code);
        echo "<img src= '" . plugins_url( 'flags/' . $lcountry . '.png', __FILE__ ) . "'> " . $country_code . " ";
      }
      echo "<br/>";
    } elseif ($values['target_type']=='channelId') {
      $channels = json_decode($values['target_value'], true);
      echo "Channel: " ;
      $first = true;
      foreach ($channels as $channelId) {
        if (!$first) echo ", ";
        $first = false;
        if ($channelId == 1)
          echo "Mainstream";
        elseif ($channelId == 2)
          echo "Adult";
        elseif ($channelId == 3)
          echo "Bad Traffic";
        else
          echo $channelId;
      }
      echo "<br/>";
    } else {
      echo $values['target_type'] . " : " . $values['target_value'] . "<br/>";
    }
  }
  echo "</td></tr>";
} 
?>
</table>
</div>

<?php
}

?>
