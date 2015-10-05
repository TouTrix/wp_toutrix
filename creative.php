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
<div class="updated"><p><strong><?php _e('Creative added', 'wp-toutrix' ); ?></strong></p></div>
<?php
    }

    echo "<h2>Creatives</h2>";

    $creatives = $toutrix_adserver->creatives_list(array());
?>
<table class="wp-list-table widefat fixed striped posts">
  <tr><th>Id</th><th>Title</th><th>Action</th></tr>
<?php
    foreach ($creatives as $creative) {
      echo "<tr><td><a href='?page=toutrix_creative&creativeId=" . $creative->id . "'>" . $creative->id ."</a></td><td><a href='?page=toutrix_creative&creativeId=" . $creative->id . "'>" . $creative->title ."</a></td><td></td></tr>";
    }
?>
</table>

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
<div class="updated"><p><strong><?php _e('Creative saved', 'wp-toutrix' ); ?></strong></p></div>
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
<table class="form-table">
<tr><td>Title:</td><td><input type='text' name='title' value='<?php echo $creative->title;?>' class="regular-text code"></td>
</tr>

<tr><td>Ad Type:</td><td><select name='adtypeId'>
<?php foreach ($adtypes as $adtype) { ?>
<option value='<?php echo $adtype->id; ?>'<?php if ($creative->adtypeId == $adtype->id) echo " selected"; ?>><?php echo $adtype->name; ?></option>
<?php } ?>
</select></td>
</tr>

<tr><td>Url:</td><td><input type='text' name='url' value='<?php echo $creative->url;?>' class="regular-text code"></td></tr>

<tr><td>Banner Url:</td><td><input type='text' name='banner_url' value='<?php echo $creative->banner_url;?>' class="regular-text code"><br/>(optional)</td></tr>

<tr><td>HTML:</td><td>
<textarea name='html'>
<?php echo $creative->html;?>
</textarea><br/>(Optional)
</td></tr>
</table>

<p class="submit">
<input type="submit" name="b" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</p>
</form>
<?php
}
?>
