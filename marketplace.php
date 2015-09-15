<?php

function mt_toutrix_marketplace_page() {
  global $toutrix_adserver;
  $toutrix_adserver->toutrix_get_token();

    if (empty($_GET['subpage'])) {
      echo "<h2>Marketplace</h2>";
      echo "Coming soon";
    }
}
?>
