<?php
function toutrix_signup_form() {
?>
<h1>Signup form</h1>
<?php
global $user_toutrix_access_token;

  if (strlen($user_toutrix_access_token)==0) {
?>
<form method='POST'>
Username: <input type='text' name='username' value='<?php echo $_POST['username']; ?>'> <br/>
Password: <input type='password' name='password' value='<?php echo $_POST['password']; ?>'> <br/>
Email: <input type='text' name='email' value='<?php echo $_POST['email']; ?>'> <br/>
<input type='submit' name='b' value='Signup'>
</form>
<?php
  } else {
?>
<a href='?page=homepage<?php echo $toutrix_set_token; ?>'>Login now</a><br/>
<?php
  }
}
?>
