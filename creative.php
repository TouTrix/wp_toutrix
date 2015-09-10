<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function toutrix_creative_page() {
  global $toutrix_adserver;
  toutrix_get_token();

  if (empty($_GET['creativeId'])) {
    if (!empty($_POST['b'])) {
      $fields = new stdclass();
      $fields->user_id = $adserver->userId;
      $fields->title = $_POST['title'];
      $fields->url = $_POST['url'];
      $fields->banner_url = $_POST['banner_url'];
      $fields->html = $_POST['html'];
      $fields->adtypeId = $_POST['adtypeId'];
      $fields->IsDeleted = 0;
      $fields->IsActive = 1;
      stripslashes_deep( $fields );
      $creative = $toutrix_adserver->creative_create($fields);
//var_dump($creative);
?>
<div class="updated"><p><strong><?php _e('Creative added', 'menu-test' ); ?></strong></p></div>
<?php
    }

    echo "<h2>Creatives</h2>";

    $creatives = $toutrix_adserver->creatives_list(array());
?>
<div class="CSSTableGenerator">
<table>
  <tr><th>Id</th><th>Title</th><th>Action</th></tr>
<?php
    foreach ($creatives as $creative) {
      echo "<tr><td><a href='?page=toutrix_creative&creativeId=" . $creative->id . "'>" . $creative->id ."</a></td><td><a href='?page=toutrix_creative&creativeId=" . $creative->id . "'>" . $creative->title ."</a></td><td></td></tr>";
    }
?>
</table>
</div>

<h2>Create a new creative</h2>
<?php
    $new = new stdclass();
    toutrix_creative_form($new);
  } elseif (!empty($_GET['creativeId'])) {
    if (!empty($_POST['b'])) {
      $fields = new stdclass();
      $fields->id = $_POST['id'];
      $fields->title = $_POST['title'];
      $fields->url = $_POST['url'];
      $fields->banner_url = $_POST['banner_url'];
      $fields->html = $_POST['html'];
      $fields->adtypeId = $_POST['adtypeId'];
      stripslashes_deep( $fields );
//var_dump($fields);
//echo "<br/>";
      $creative = $toutrix_adserver->creative_update($fields);
//var_dump($creative);
?>
<div class="updated"><p><strong><?php _e('Creative saved', 'menu-test' ); ?></strong></p></div>
<?php
    }
    $fields = new stdclass();
    $fields->creativeId = $_GET['creativeId'];
    $creative = $toutrix_adserver->creative_get($fields)
?>
<h2>Update creative</h2>
<?php
    toutrix_creative_form($creative);
    //var_dump($creative);
  }
}

function toutrix_creative_form($creative) {
  global $toutrix_adserver;
  $adtypes = $toutrix_adserver->adtypes_get(array());
?>
<form method='POST'>
<?php if (!empty($creative->id)) {?>
<input type='hidden' name='id' value='<?php echo $creative->id;?>'>
<?php } ?>
Title: <input type='text' name='title' value='<?php echo $creative->title;?>'><br/>

Ad Type: <select name='adtypeId'>
<?php foreach ($adtypes as $adtype) { ?>
<option value='<?php echo $adtype->id; ?>'<?php if ($creative->adtypeId == $adtype->id) echo " selected"; ?>><?php echo $adtype->name; ?></option>
<?php } ?>
</select><br/>
	
Url: <input type='text' name='url' value='<?php echo $creative->url;?>'><br/>
Banner Url: <input type='text' name='banner_url' value='<?php echo $creative->banner_url;?>'> (optional)<br/>
HTML:<br/>
<textarea name='html'>
<?php echo $creative->html;?>
</textarea> (optional)<br/>
<input type='submit' name='b' value='Save'>
</form>
<?php
}
?>