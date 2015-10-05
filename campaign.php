<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function toutrix_campaign_show_stats($campaign) {
  global $toutrix_adserver;
  toutrix_get_token();

  $fields = new stdClass();
  $fields->id = $_GET['campaignId'];
  if (isset($_GET['startDate'])) {
    $fields->startDate = $_GET['startDate'];
    $fields->endDate = $_GET['endDate'];
  } else {
    $fields->startDate = date("m/01/Y");
    $fields->endDate = date("m/t/Y");
  }
  $stats = $toutrix_adserver->campaign_report($fields);
?>
<h1>Statistique</h1>

<form method='GET'>
<input type='hidden' name='page' value='mt_toutrix_campaign'>
<input type='hidden' name='campaignId' value='<?php echo $_GET['campaignId'];?>'>
<input type='hidden' name='action' value='stats'>
Start date: <input type='text' name='startDate' value='<?php echo $fields->startDate; ?>'><br/>
End date: <input type='text' name='endDate' value='<?php echo $fields->endDate; ?>'><br/>
<input type='submit' name='b' value='Go'><br/>
</form>

<h2>Per day</h2>
<?php
  $stats_per_day = $stats->stats->per_day;
?>
<table class="wp-list-table widefat fixed striped posts">
  <tr><th>Day</th><th>Nbr. impressions</th><th>Nbr. clicks</th><th>Own impressions</th><th>Own clicks</th><th>Cost</th></tr>
<?php
  $total_nbr_impressions = 0;
  $total_nbr_clicks = 0;
  $total_own_clicks = 0;
  $total_own_impressions = 0;
  $total_cost = 0;
  foreach ($stats_per_day as $day => $country) {
    $total_nbr_impressions += $country->nbr_impressions;
    $total_nbr_clicks += $country->nbr_clicks;
    $total_own_clicks += $country->own_clicks;
    $total_own_impressions += $country->own_impressions;
    $total_cost += $country->cost;

    echo "  <tr><td>" . $day . "</td><td>" . $country->nbr_impressions . "</td><td>" . $country->nbr_clicks . "</td><td>" . $country->own_impressions . "</td><td>" . $country->own_clicks . "</td><td>$" . number_format($country->cost,4) . "</td></tr>";
  }
  echo "  <tr><td>Total:</td><td>" . $total_nbr_impressions . "</td><td>" . $total_nbr_clicks . "</td><td>" . $total_own_impressions . "</td><td>" . $total_own_clicks . "</td><td>$" . number_format($total_cost,4) . "</td></tr>";
?>
</table>

<h2>Per country</h2>
<?php
  //var_dump($stats->stats);
  //echo "<hr/>";
  $stats_per_day = $stats->stats->per_country;
?>
<table class="wp-list-table widefat fixed striped posts">
  <tr><th>Country</th><th>Nbr. impressions</th><th>Nbr. clicks</th><th>Own impressions</th><th>Own clicks</th><th>Cost</th></tr>
<?php
  $total_nbr_impressions = 0;
  $total_nbr_clicks = 0;
  $total_own_clicks = 0;
  $total_own_impressions = 0;
  $total_cost = 0;
  foreach ($stats_per_day as $country_code => $country) {
    $total_nbr_impressions += $country->nbr_impressions;
    $total_nbr_clicks += $country->nbr_clicks;
    $total_own_clicks += $country->own_clicks;
    $total_own_impressions += $country->own_impressions;
    $total_cost += $country->cost;

    echo "  <tr><td>" .$country_code . " <img src='" . plugins_url( 'flags/' . strtolower($country_code) . '.png', __FILE__ ) . "'></td><td>" . $country->nbr_impressions . "</td><td>" . $country->nbr_clicks . "</td><td>" . $country->own_impressions . "</td><td>" . $country->own_clicks . "</td><td>$" . number_format($country->cost,4) . "</td></tr>";
  }
  echo "  <tr><td>Total:</td><td>" . $total_nbr_impressions . "</td><td>" . $total_nbr_clicks . "</td><td>" . $total_own_impressions . "</td><td>" . $total_own_clicks . "</td><td>$" . number_format($total_cost,4) . "</td></tr>";
?>
</table>
<?php
}

function mt_toutrix_campaign_page() {
  global $toutrix_adserver;
  toutrix_get_token();
  
  if (!empty($_GET['flightId'])) {
    toutrix_flight();
    return;
  }

  if ($_GET['action']=='new') {
?>
<h2>Create a new campaign</h2>
<?php
    $new = new stdclass();
    toutrix_campaign_form($new);
  }

  if (empty($_GET['campaignId'])) {
    if (!empty($_POST['b'])) {
      $fields = new stdclass();
      $fields->user_id = $adserver->userId;
      $fields->name = $_POST['name'];
      $fields->isDeleted = 0;
      $fields->isActive = 1;
      stripslashes_deep( $fields );
      $campaign = $toutrix_adserver->campaign_create($fields);
?>
<div class="updated"><p><strong><?php _e('Campaign added', 'wp-toutrix' ); ?></strong></p></div>
<?php
    }
?>
<div class='wrap'>
<h1>Campaigns <a href="?page=mt_toutrix_campaign&action=new" class="page-title-action">Add New</a></h1>
<?php
    $table = new campaigns_table();
    $table->prepare_items();
    $table->display();
?>
</div>
<?php
  } elseif (!empty($_GET['campaignId'])) {
    if (!empty($_POST['b']) && $_POST['target']=='yes') {
      $fields = new stdclass();
      if (!empty($_POST['id']))
        $fields->id = $_POST['id'];
      $fields->campaignId = $_GET['campaignId'];
      $fields->target_type = $_POST['target_type'];
      $fields->target_value = $_POST['target_value']; 
      $fields->isExcept = false;     
      stripslashes_deep( $fields );
//var_dump($fields);
//echo "<br/>";
      $target = $toutrix_adserver->target_create($fields);
//var_dump($target);
?>
<div class="updated"><p><strong><?php _e('Target added', 'wp-toutrix' ); ?></strong></p></div>
<?php
    } elseif (!empty($_POST['b']) && $_POST['flight']=='yes') {
      $fields = new stdclass();
      if (!empty($_POST['id']))
        $fields->id = $_POST['id'];
      $fields->campaignId = $_POST['campaignId'];
      $fields->Name = $_POST['Name'];
      $fields->Price = $_POST['Price'];
      $fields->MaxPerIp = $_POST['MaxPerIp'];
      $fields->IsDeleted = false;
      $fields->IsActive = true;
      $fields->IsUnlimited = true;
      $fields->NoEndDate = true;
      stripslashes_deep( $fields );
//var_dump($fields);
//echo "<br/>";
      $flight = $toutrix_adserver->flight_create($fields);
//var_dump($flight);
    } elseif (!empty($_POST['b'])) {
      $fields = new stdclass();
      $fields->id = $_POST['id'];
      $fields->name = $_POST['name'];
      stripslashes_deep( $fields );
//var_dump($fields);
//echo "<br/>";
      $campaign = $toutrix_adserver->campaign_update($fields);
//var_dump($campaign);
?>
<div class="updated"><p><strong><?php _e('Campaign saved', 'wp-toutrix' ); ?></strong></p></div>
<?php
    }
    $fields = new stdclass();
    $fields->campaignId = $_GET['campaignId'];
    //var_dump($fields); echo "<br/>";
    $campaign = $toutrix_adserver->campaign_get($fields);

    if (isset($_GET['action']) && $_GET['action']== 'stats') {
      toutrix_campaign_show_stats(campaign);
    } else {
?>
<a href='?page=mt_toutrix_campaign&campaignId=<?php echo $_GET['campaignId'];?>&action=stats'>Show stats</a>
<h2>Update campaign</h2>
<?php
      toutrix_campaign_form($campaign);
      //var_dump($campaign);

      toutrix_flights($campaign);
?>
<h2>Targeting for this campaign</h2>
It applies to all flights.<br/>
<?php

      $fields = new stdclass();
      $fields->campaignId = $_GET['campaignId'];
      $targets = $toutrix_adserver->campaign_targets($fields);
      toutrix_show_targets($targets);

      echo "<h2>Add a new target</h2>";
      toutrix_show_target_form($fields);
    }
  }
}

function toutrix_campaign_form($campaign) {
  global $toutrix_adserver;
?>
<form method='POST'>
<?php if (!empty($campaign->id)) {?>
<input type='hidden' name='id' value='<?php echo $campaign->id;?>'>
<?php } ?>
Name: <input type='text' name='name' value='<?php echo $campaign->name;?>'><br/>

<input type='submit' name='b' value='Save'>
</form>
<?php
}

class campaigns_table extends WP_List_Table {

    /** ************************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We 
     * use the parent reference to set some default configs.
     ***************************************************************************/
    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'campaign',     //singular name of the listed records
            'plural'    => 'campaigns',    //plural name of the listed records
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
    function column_title($item){
        
        //Build row actions
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&campaignId=%s">Edit</a>',$_REQUEST['page'],'edit',$item['id']),
            'stats'      => sprintf('<a href="?page=%s&action=%s&campaignId=%s">Stats</a>',$_REQUEST['page'],'stats',$item['id']),
            'delete'    => sprintf('<a href="?page=%s&action=%s&campaignId=%s">Delete</a>',$_REQUEST['page'],'delete',$item['id']),
        );
        
        //Return the title contents
        return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
            /*$1%s*/ $item['title'],
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
        $campaigns = $toutrix_adserver->campaigns_list(array());
        $arr = array();
        foreach ($campaigns as $campaign) {
          if ($campaign->isDeleted) continue;
          $status2 = '<p color="red">Not active</p>';
          if ($campaign->isActive==1)
            $status2 = '<p color=\"green\">Active</p>';
          $new_crea = array('id'=>$campaign->id, 'title'=>$campaign->name, 'the_status'=>$status2);
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
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'title'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
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


class campaign_stats_table extends WP_List_Table {

    /** ************************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We 
     * use the parent reference to set some default configs.
     ***************************************************************************/
    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'stat',     //singular name of the listed records
            'plural'    => 'stats',    //plural name of the listed records
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
            case 'day':
                return $item[$column_name];
            case 'nbr_impressions':
                return $item[$column_name];
            case 'nbr_clics':
                return $item[$column_name];
            case 'own_impressions':
                return $item[$column_name];
            case 'own_clics':
                return $item[$column_name];
            case 'cost':
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
    function column_title($item){
        
        //Build row actions
        $actions = array(
        );
        
        //Return the title contents
        return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
            /*$1%s*/ $item['title'],
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
        $campaigns = $toutrix_adserver->campaigns_list(array());
        $arr = array();
        foreach ($campaigns as $campaign) {
          if ($campaign->isDeleted) continue;
          $status2 = 'Not active';
          if ($campaign->isActive==1)
            $status2 = 'Active';
          $new_crea = array('id'=>$campaign->id, 'title'=>$campaign->name, 'the_status'=>$status2);
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
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'title'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
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
