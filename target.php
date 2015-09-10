<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function toutrix_show_targets($targets) {
?>
<div class="CSSTableGenerator">
<table>
  <tr><th>Target type</th><th>Target values</th></tr>
<?php
foreach ($targets as $target) {
  toutrix_show_target_line($target);
} 
?>
</table>
</div>
<?php
//var_dump($targets);
}

function toutrix_show_target_line($target) {
  echo "  <tr><td>" . $target->target_type ."</td><td>";
  if ($target->target_type == 'country') {
    $first = true;
    $values = json_decode($target->target_value, true);
    foreach ($values as $country_code) {
      if (!$first) echo "<br/>";
      $lcountry = strtolower($country_code);
      echo "<img src= '" . plugins_url( 'flags/' . $lcountry . '.png', __FILE__ ) . "'> " . $country_code;
      $first = false;
    }
  } else {
    echo $target->target_value;
  }
  echo "</td></tr>";
}

function toutrix_show_target_form($target) {
?>
<form method='POST'>
<input type='hidden' name='target' value='yes'>
<?php if (!empty($target->id)) { ?>
<input type='hidden' name='id' value='<?php echo $target->id; ?>'>
<?php } ?>
Target type: <br/>
<select name='target_type'>
  <option value='country' <?php if ($target->target_type == 'country') echo "selected"; ?>>Target country</option>
  <option value='city' <?php if ($target->target_type == 'city') echo "selected"; ?>>Target city</option>
  <option value='is_mobile' <?php if ($target->target_type == 'is_mobile') echo "selected"; ?>>Is mobile</option>
  <option value='channel' <?php if ($target->target_type == 'channel') echo "selected"; ?>>By channel</option>
</select>
<br/>
Target value: <br/>
<input type='text' name='target_value' value='<?php echo $target->target_value; ?>'><br/>
<input type='submit' name='b' value='Save'>
</form>
<hr/>
<h3>Target values</h3>

Country value exemple: ["US","FR"]<br/>
Is mobile value example: true or false<br/>
Channel value example: [2,3]<br/>
City value example: ["Montreal","France"]<br/>
<br/>
It's easier to delete a target and create one. You can't update a target yet.<br/>
We are going to work on a better form. We understand it's not easily.

<?php
}
?>
