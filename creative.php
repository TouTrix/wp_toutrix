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
      $fields->body = $_POST['body'];
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

?>
<div class='wrap'>
<?php
if (!isset($_GET['new'])) {
?>
<h1>Creatives <a href="?page=toutrix_creative&new=1" class="page-title-action">Add New</a></h1>

<?php
    $creatives = $toutrix_adserver->creatives_list(array());
?>

<ul class='subsubsub'>
	<li class='all'><a href='' class="current">All <span class="count">(<?php echo count($creatives);?>)</span></a></li>
</ul>

<table class="wp-list-table widefat fixed striped posts">
 <thead>
  <tr><th>Id</th><th>Title</th><th>Action</th></tr>
 </thead>
 <tbody id="the-list">
<?php
    foreach ($creatives as $creative) {
      echo "<tr><td><a href='?page=toutrix_creative&creativeId=" . $creative->id . "'>" . $creative->id ."</a></td><td><a href='?page=toutrix_creative&creativeId=" . $creative->id . "'>" . $creative->title ."</a></td><td></td></tr>";
    }
?>
 </tbody>
</table>
<?php
} else { 
?>

<h2>Create a new creative</h2>
<?php
    $new = new stdclass();
    toutrix_creative_form($new);
}

  } elseif (!empty($_GET['creativeId'])) {
    if (!empty($_POST['b'])) {
      $fields = new stdclass();
      $fields->id = $_POST['id'];
      $fields->title = $_POST['title'];
      $fields->url = $_POST['url'];
      $fields->banner_url = $_POST['banner_url'];
      $fields->body = $_POST['body'];
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
?>
</div>
<?php
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

<tr><td>Ad Type:</td><td><select id='adtypeId' name='adtypeId'>
<?php foreach ($adtypes as $adtype) { ?>
<option value='<?php echo $adtype->id; ?>'<?php if ($creative->adtypeId == $adtype->id) echo " selected"; ?>><?php echo $adtype->name; ?></option>
<?php } ?>
</select></td>
</tr>

<tr><td>Title:</td><td><input type='text' name='title' value='<?php echo $creative->title;?>' class="regular-text code"></td>
</tr>

<tr id='banner_row'><td>Banner Url:</td><td><input type='text' id='banner_url' name='banner_url' value='<?php echo $creative->banner_url;?>' class="regular-text code"></td></tr>

<tr id='body_row'><td>Body message:</td><td><input type='text' id='body' name='body' value='<?php echo $creative->body;?>' class="regular-text code"></td></tr>

<tr id='url_row'><td>Url:</td><td><input type='text' id='url' name='url' value='<?php echo $creative->url;?>' class="regular-text code"></td></tr>

<tr id='html_row'><td>HTML:</td><td>
<textarea name='html' id='html'>
<?php echo $creative->html;?>
</textarea>
</td></tr>

</table>

<p class="submit">
<input type="submit" name="b" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</p>
</form>
<script type="text/javascript">
jQuery(document).ready( function () { 
  updateForm();

  function updateForm() {
    var adtypeId = jQuery('#adtypeId').find(":selected").val();
    if (adtypeId == 1) {
      jQuery("#url_row").show();
      jQuery("#banner_row").hide();
      jQuery("#html_row").hide();
      jQuery("#body_row").hide();
    } else {
      if (adtypeId == 9) {
        jQuery("#url_row").show();
        jQuery("#body_row").show();
        jQuery("#banner_row").hide();
        jQuery("#html_row").hide();
      } else if (jQuery("#url").val().length > 0) {
        jQuery("#url_row").show();
        jQuery("#banner_row").show();
        jQuery("#html_row").hide();
        jQuery("#body_row").hide();
      } else if (jQuery("#html").val().length > 0) {
        jQuery("#url_row").hide();
        jQuery("#banner_row").hide();
        jQuery("#html_row").show();
        jQuery("#body_row").hide();
      } else {
        jQuery("#url_row").show();
        jQuery("#banner_row").show();
        jQuery("#html_row").show();
        jQuery("#body_row").hide();
      }
    }
  }

  jQuery('#url').change(function() {
    updateForm();
  });

  jQuery('#banner').change(function() {
    updateForm();
  });

  jQuery('#html').change(function() {
    updateForm();
  });

  jQuery( "#adtypeId" ).change(function() {
    updateForm();
  });
});
</script>
<?php
}
?>
