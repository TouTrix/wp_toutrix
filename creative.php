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

<?php

    $table = new creative_table();
    $table->prepare_items();
    $table->display();

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
      $isActive = 0;
      if ($_POST['IsActive'] == 'on')
        $isActive = 1;
      $fields->id = $_POST['id'];
      $fields->IsActive = $isActive;
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

<tr><td>Active:</td><td><input type='checkbox' name='IsActive' <?php if ($creative->IsActive==1) echo "checked";?>></td>
</tr>

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

<h2>Macros</h2>

Macros are all between %<br/>
<br/>
%clickId% - Create a unique clickId<br/>

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

class creative_table extends toutrix_table {

    /** ************************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We 
     * use the parent reference to set some default configs.
     ***************************************************************************/
    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'creative',     //singular name of the listed records
            'plural'    => 'creatives',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
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
            case 'the_status':
                if ($item['isActive']) {
                  return "Active";
                } else {
                  return "Not active";
                }
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
    function column_title($item){
        
        //Build row actions
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&creativeId=%s">Edit</a>',$_REQUEST['page'],'edit',$item['id']),
            'delete'    => sprintf('<a href="?page=%s&action=%s&creativeId=%s">Delete</a>',$_REQUEST['page'],'delete',$item['id']),
        );
        
        //Return the title contents
        return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
            /*$1%s*/ $item['title'],
            /*$2%s*/ $item['id'],
            /*$3%s*/ $this->row_actions($actions)
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
            'title'     => 'Title',
            'the_status'     => 'Status',
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
        $per_page = 30;
        
        
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
        
        
        /**
         * Instead of querying a database, we're going to fetch the example data
         * property we created for use in this plugin. This makes this example 
         * package slightly different than one you might build on your own. In 
         * this example, we'll be using array manipulation to sort and paginate 
         * our data. In a real-world implementation, you will probably want to 
         * use sort and pagination data to build a custom query instead, as you'll
         * be able to use your precisely-queried data immediately.
         */
        //$data = $this->example_data;
        global $toutrix_adserver;
        $creatives = $toutrix_adserver->creatives_list(array());
        $arr = array();
        foreach ($creatives as $creative) {
          $new_crea = array('id'=>$creative->id, 'title'=>$creative->title, 'isActive'=>$creative->IsActive);
          $arr[] = $new_crea;
        }
        $data = $arr;
        
        /**
         * This checks for sorting input and sorts the data in our array accordingly.
         * 
         * In a real-world situation involving a database, you would probably want 
         * to handle sorting by passing the 'orderby' and 'order' values directly 
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary.
         */

        usort($data, 'usort_reorder');
        
        
        /***********************************************************************
         * ---------------------------------------------------------------------
         * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
         * 
         * In a real-world situation, this is where you would place your query.
         *
         * For information on making queries in WordPress, see this Codex entry:
         * http://codex.wordpress.org/Class_Reference/wpdb
         * 
         * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
         * ---------------------------------------------------------------------
         **********************************************************************/
        
                
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
