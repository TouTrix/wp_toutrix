<?php

function toutrix_user_form() {
  global $user;
  global $toutrix_adserver;

  $user = $toutrix_adserver->get_user();

  if (isset($_GET['withdraw_bitcoin'])) {
    //$fields = new stdclass();

    //$fields->id = $user->id;
    $user->withdraw_bitcoin = $_GET['withdraw_bitcoin'];
    $user->withdraw_at = $_GET['withdraw_at'];
    $user = $toutrix_adserver->user_update($user);
?>
<div class="updated"><p><strong><?php _e('User has been updated', 'user_updated' ); ?></strong></p></div>
<?php
  }
?>
<h2>User profile</h2>
<form>
<input type='hidden' name='page' value='mt_toutrix_page-handle'>
<table class="form-table">
<tr><td>Withdrawal bitcoin address:</td><td><input type='text' name='withdraw_bitcoin' value='<?php echo $user->withdraw_bitcoin; ?>'></td>
</tr>
<tr>
<td>Automatic withdrawal after:</td><td>$<input type='text' name='withdraw_at' value='<?php echo $user->withdraw_at; ?>' length='4'>
<br/>A payment will be sent when your account will be at that amount)</td>
</tr>
</table>

<p class="submit">
<input type="submit" name="Submit" name="b" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</p>

</form>
<?php
}
?>
