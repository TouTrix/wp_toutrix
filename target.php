<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function get_form_target() {
  $fields = new stdclass();
  if (!empty($_POST['id']))
    $fields->id = $_POST['id'];
  if (isset($_GET['flightId'])) {
    $fields->flightId = $_GET['flightId'];
  } else {
    $fields->campaignId = $_GET['campaignId'];
  }
  $fields->target_type = $_POST['target_type'];
  if ($fields->target_type == 'country') {
    $countries = array();
    foreach ($_POST['country_code'] as $num => $country_code) {
      $countries[] = $country_code;
    }
    $countries = json_encode($countries);
    $fields->target_value = $countries;
  } else if ($fields->target_type == 'language') {
    $langs = array();
    foreach ($_POST['language'] as $num => $lang) {
      $langs[] = $lang;
    }
    $langs = json_encode($langs);
    $fields->target_value = $langs;
  } else if ($fields->target_type == 'channelId') {
    foreach ($_POST['channel_ids'] as $num => $chanId) {
      $channels[] = $chanId;
    }
    $channels = json_encode($channels);
    $fields->target_value = $channels;
  } else if ($fields->target_type == 'is_mobile') {
    $fields->target_value = $_POST['true_false'];
  } else {
    $fields->target_value = $_POST['target_value']; 
  }
  $fields->isExcept = false;
  stripslashes_deep( $fields );
  return $fields;
}

function toutrix_show_targets($targets) {
  global $toutrix_adserver;
?>
<div id='wrap'>
<?
  $targets_table = new targets_table();
  $targets_table->set_datas($targets);
  $targets_table->prepare_items();
  $targets_table->display();
?>
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
  global $_countries;
  global $_languages;
?>
<form method='POST'>
<input type='hidden' name='target' value='yes'>
<?php if (!empty($target->id)) { ?>
<input type='hidden' name='id' value='<?php echo $target->id; ?>'>
Target type: <br/>
<?php echo $target->target_type . "<br/>"; ?>
<input type='hidden' name='target_type' id='target_type' value='<?php echo $target->target_type; ?>'>
<?php }  else { ?>
Exception: <br/>
<input type='checkbox' name='exception'><br/>
Target type: <br/>
<select id='sel_target_type' name='target_type'>
  <option value='choose'>Select a targeting type</option>
  <option value='country' <?php if ($target->target_type == 'country') echo "selected"; ?>>Target country</option>
<!--  <option value='city' <?php if ($target->target_type == 'city') echo "selected"; ?>>Target city</option> -->
  <option value='is_mobile' <?php if ($target->target_type == 'is_mobile') echo "selected"; ?>>Is mobile</option>
  <option value='channelId' <?php if ($target->target_type == 'channelId') echo "selected"; ?>>By channel</option>
  <option value='language' <?php if ($target->target_type == 'language') echo "selected"; ?>>By user language</option>
</select>
<br/>
<?php } ?>
<div id='target_table_value'>
Target value: <br/>
<input type='text' name='target_value' value="<?php echo $target->target_value; ?>"><br/>
</div>
<div id='target_countries'>
<?php 
foreach ($_countries as $country_code => $country_name) {
  echo "<input type='checkbox' name='country_code[]' value='" . $country_code . "'> ";
  echo "<img src= '" . plugins_url( 'flags/' . strtolower($country_code) . '.png', __FILE__ ) . "'> ";
  echo $country_code . " " . $country_name . "<br/>";
}
?>
</div>
<div id='target_language'>
<?php 
foreach ($_languages as $lang_code => $lang) {
  echo "<input type='checkbox' name='language[]' value='" . $lang_code . "'> ";
  echo $lang . "<br/>";
}
?>
</div>
<div id='target_true_false'>
<select name='true_false'>
  <option value='true'>True</option>
  <option value='false'>False</option>
</select>
</div>
<div id='target_channels'>
<?php
$channels = toutrix_get_channels();
foreach ($channels as $channel) {
  echo "<input type='checkbox' name='channel_ids[]' value='" . $channel->id . "'> ";
  echo $channel->Title . "<br/>";
}
?>
</div>
<script type="text/javascript">
jQuery(document).ready( function () { 
  updateForm();

  jQuery('#sel_target_type').change(function() {
    updateForm();
  });

  function updateForm() {
    var target_type = jQuery('#sel_target_type').find(":selected").val();
    if (target_type == 'country') {
      jQuery("#target_table_value").hide();
      jQuery("#target_countries").show();
      jQuery("#target_channels").hide();
      jQuery("#target_language").hide();
      jQuery("#target_true_false").hide();
    } else if (target_type == 'channelId') {
      jQuery("#target_table_value").hide();
      jQuery("#target_countries").hide();
      jQuery("#target_channels").show();
      jQuery("#target_language").hide();
      jQuery("#target_true_false").hide();
    } else if (target_type == 'language') {
      jQuery("#target_table_value").hide();
      jQuery("#target_countries").hide();
      jQuery("#target_channels").hide();
      jQuery("#target_language").show();
      jQuery("#target_true_false").hide();
    } else if (target_type == 'is_mobile') {
      jQuery("#target_table_value").hide();
      jQuery("#target_countries").hide();
      jQuery("#target_channels").hide();
      jQuery("#target_language").hide();
      jQuery("#target_true_false").show();
    } else {
      jQuery("#target_table_value").show();
      jQuery("#target_countries").hide();
      jQuery("#target_channels").hide();
      jQuery("#target_language").hide();
      jQuery("#target_true_false").hide();
    }
  }
});
</script>
<input type='submit' name='b' value='Save'>
</form>
<hr/>
It's easier to delete a target and create a one. You can't update a target yet.<br/>
We are going to work on a better form. We understand it's not easily.
<?php
}

class targets_table extends WP_List_Table {

    var $datas;

    /** ************************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We 
     * use the parent reference to set some default configs.
     ***************************************************************************/
    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'target',     //singular name of the listed records
            'plural'    => 'targets',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );      
    }

    function set_datas($stats) {
        $arr = array();
        foreach ($stats as $target) {
          $new_crea = array('id'=>$target->id, 'isExcept'=>$target->isExcept, 'target_type' => $target->target_type, 'target_value' => $target->target_value);
          $arr[] = $new_crea;
        }
        $this->datas = $arr;
    }


    /** ************************************************************************
     * Recommended. This method is called when the parent class can't find a method
     * specifically build for a given column. Generally, it's recommended to include
     * one method for each column you want to render, keeping your package class
     * neat and organized. For example, if the class needs to process a column
     * named 'title', it would first see if a method named $this->column_title() 
     * exists - if it does, that method will be used. If it doesn't, this one will
     * be used. Generally, you should try to use custom column methods as much as 
     * possible. 
     * 
     * Since we have defined a column_title() method later on, this method doesn't
     * need to concern itself with any column with a name of 'title'. Instead, it
     * needs to handle everything else.
     * 
     * For more detailed insight into how columns are handled, take a look at 
     * WP_List_Table::single_row_columns()
     * 
     * @param array $item A singular item (one full row's worth of data)
     * @param array $column_name The name/slug of the column to be processed
     * @return string Text or HTML to be placed inside the column <td>
     **************************************************************************/
    function column_default($item, $column_name){
        switch($column_name){
            case 'isExcept':
                if ($item[$column_name]==1) {
	                return "Yes";
                } else {
	                return "No";
                }
            case 'target_type':
                return $item[$column_name];
            case 'target_value':
                return $item[$column_name];
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }


    /** ************************************************************************
     * Recommended. This is a custom column method and is responsible for what
     * is rendered in any column with a name/slug of 'title'. Every time the class
     * needs to render a column, it first looks for a method named 
     * column_{$column_title} - if it exists, that method is run. If it doesn't
     * exist, column_default() is called instead.
     * 
     * This example also illustrates how to implement rollover actions. Actions
     * should be an associative array formatted as 'slug'=>'link html' - and you
     * will need to generate the URLs yourself. You could even ensure the links
     * 
     * 
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_target($item){
        
        //Build row actions
        if (isset($_GET['flightId'])) {
          $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&campaignId=%s&flightId=%s&tab=targets&id=%s">Edit</a>',$_REQUEST['page'],'edit',$_GET['campaignId'],$_GET['flightId'],$item['id']),
            'delete'    => sprintf('<a href="?page=%s&action=%s&campaignId=%s&flightId=%s&removetargetid=%s&tab=targets">Delete</a>',$_REQUEST['page'],'edit',$_GET['campaignId'],$_GET['flightId'],$item['id']),
/*
            'stats'      => sprintf('<a href="?page=%s&action=%s&campaignId=%s">Stats</a>',$_REQUEST['page'],'stats',$item['id']),
*/
          );
        } else {
          $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&campaignId=%s&tab=targets&id=%s">Edit</a>',$_REQUEST['page'],'edit',$_GET['campaignId'],$_GET['flightId'],$item['id']),
            'delete'    => sprintf('<a href="?page=%s&action=%s&campaignId=%s&removetargetid=%s&tab=targets">Delete</a>',$_REQUEST['page'],'edit',$_GET['campaignId'],$item['id']),
/*
            'stats'      => sprintf('<a href="?page=%s&action=%s&campaignId=%s">Stats</a>',$_REQUEST['page'],'stats',$item['id']),
*/
          );
        }
        
        //Return the title contents
        return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
            /*$1%s*/ $item['name'],
            /*$2%s*/ $item['id'],
            /*$3%s*/ $this->row_actions($actions)
        );

    }


    /** ************************************************************************
     * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
     * is given special treatment when columns are processed. It ALWAYS needs to
     * have it's own method.
     * 
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['ID']                //The value of the checkbox should be the record's id
        );
    }


    /** ************************************************************************
     * REQUIRED! This method dictates the table's columns and titles. This should
     * return an array where the key is the column slug (and class) and the value 
     * is the column's title text. If you need a checkbox for bulk actions, refer
     * to the $columns array below.
     * 
     * The 'cb' column is treated differently than the rest. If including a checkbox
     * column in your table you must create a column_cb() method. If you don't need
     * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
     * 
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'target'     => 'Target',
            'target_type'     => 'Target Type',
            'target_value'     => 'Target Value'
        );
        return $columns;
    }


    /** ************************************************************************
     * Optional. If you want one or more columns to be sortable (ASC/DESC toggle), 
     * you will need to register it here. This should return an array where the 
     * key is the column that needs to be sortable, and the value is db column to 
     * sort by. Often, the key and value will be the same, but this is not always
     * the case (as the value is a column name from the database, not the list table).
     * 
     * This method merely defines which columns should be sortable and makes them
     * clickable - it does not handle the actual sorting. You still need to detect
     * the ORDERBY and ORDER querystring variables within prepare_items() and sort
     * your data accordingly (usually by modifying your query).
     * 
     * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
     **************************************************************************/
    function get_sortable_columns() {
        $sortable_columns = array(
/*
            'title'     => array('title',false),     //true means it's already sorted
            'rating'    => array('rating',false),
            'director'  => array('director',false)
*/
        );
        return $sortable_columns;
    }


    /** ************************************************************************
     * Optional. If you need to include bulk actions in your list table, this is
     * the place to define them. Bulk actions are an associative array in the format
     * 'slug'=>'Visible Title'
     * 
     * If this method returns an empty value, no bulk action will be rendered. If
     * you specify any bulk actions, the bulk actions box will be rendered with
     * the table automatically on display().
     * 
     * Also note that list tables are not automatically wrapped in <form> elements,
     * so you will need to create those manually in order for bulk actions to function.
     * 
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_bulk_actions() {
        $actions = array(
//            'delete'    => 'Delete'
        );
        return $actions;
    }


    /** ************************************************************************
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     * 
     * @see $this->prepare_items()
     **************************************************************************/
    function process_bulk_action() {
        
        //Detect when a bulk action is being triggered...
        if( 'delete'===$this->current_action() ) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
        
    }


    /** ************************************************************************
     * REQUIRED! This is where you prepare your data for display. This method will
     * usually be used to query the database, sort and filter the data, and generally
     * get it ready to be displayed. At a minimum, we should set $this->items and
     * $this->set_pagination_args(), although the following properties and methods
     * are frequently interacted with here...
     * 
     * @global WPDB $wpdb
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     **************************************************************************/
    function prepare_items() {
        global $wpdb; //This is used only if making any database queries

        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 31;
        
        
        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        
        /**
         * REQUIRED. Finally, we build an array to be used by the class for column 
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        
        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();
        
        $data = $this->datas;
        
        /**
         * This checks for sorting input and sorts the data in our array accordingly.
         * 
         * In a real-world situation involving a database, you would probably want 
         * to handle sorting by passing the 'orderby' and 'order' values directly 
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary.
         */

        usort($data, 'usort_reorder');        
                
        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently 
         * looking at. We'll need this later, so you should always include it in 
         * your own package classes.
         */
        $current_page = $this->get_pagenum();
        
        /**
         * REQUIRED for pagination. Let's check how many items are in our data array. 
         * In real-world use, this would be the total number of items in your database, 
         * without filtering. We'll need this later, so you should always include it 
         * in your own package classes.
         */
        $total_items = count($data);
        
        
        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to 
         */
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        
        
        
        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where 
         * it can be used by the rest of the class.
         */
        $this->items = $data;
        
        
        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }

}
?>
