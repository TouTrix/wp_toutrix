<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function toutrix_flights($campaign) {
  global $toutrix_adserver;
  toutrix_get_token();
?>
<div class='wrap'>
<h1>Flights <a href="?page=mt_toutrix_campaign&action=new&campaignId=<?php echo $_GET['campaignId'];?>&tab=flights" class="page-title-action">Add New</a></h1>

<?php
  if (isset($_GET['action']) && $_GET['action']=='edit') {
    $fields = new stdclass();
    $fields->campaignId = $campaign->id;
    $flights = $toutrix_adserver->flights_get($fields);

    $flights_table = new flights_table();
    $flights_table->set_datas($flights);
    $flights_table->prepare_items();
    $flights_table->display();

    //var_dump($flights);
  } else {
?>
<h2>Create a new flight</h2>
<?php
    $flight = new stdclass();
    $flight->max_per_ip = 0;
    $flight->campaignId = $_GET['campaignId'];
    toutrix_flight_form($flight);
  }
?>
</div>
<?php
}

function toutrix_flight() {
  global $toutrix_adserver;
  toutrix_get_token();

  if (isset($_GET['activeId']) && $_GET['activeId'] > 0) {
    $fields = new stdclass();
    $fields->id = $_GET['activeId'];
    $fields->IsActive = true;
    $target = $toutrix_adserver->creative_flight_save($fields);
?>
<div class="updated"><p><strong><?php _e('Creative started', 'wp-toutrix' ); ?></strong></p></div>
<?php
  }

  if (isset($_GET['removetargetid']) && isset($_GET['flightId'])) {
    $fields = new stdclass();
    $fields->flightId = $_GET['flightId'];
    $fields->id = $_GET['removetargetid'];

    $response = $toutrix_adserver->flight_targets_delete($fields);
?>
<div class="updated"><p><strong><?php _e('Targeting deleted', 'wp-toutrix' ); ?></strong></p></div>
<?php
  }


  if (isset($_GET['deactiveId']) && $_GET['deactiveId'] > 0) {
    $fields = new stdclass();
    $fields->id = $_GET['deactiveId'];
    $fields->IsActive = false;
    $target = $toutrix_adserver->creative_flight_save($fields);
?>
<div class="updated"><p><strong><?php _e('Creative stopped', 'wp-toutrix' ); ?></strong></p></div>
<?php
  }

  if (isset($_GET['removeTargetId']) && $_GET['removeTargetId'] > 0) {
    $fields = new stdclass();
    $fields->id = $_GET['removeTargetId'];
    $fields->IsDeleted = true;
    $fields->IsActive = false;
    $target = $toutrix_adserver->creative_flight_save($fields);
?>
<div class="updated"><p><strong><?php _e('Creative removed', 'wp-toutrix' ); ?></strong></p></div>
<?php
  }

  if (isset($_POST['target']) && $_POST['target']=='yes') {
    // We are adding a new target
    $fields = get_form_target();
    $target = $toutrix_adserver->target_create($fields);
    //var_dump($target);
?>
<div class="updated"><p><strong><?php _e('Flight target added', 'wp-toutrix' ); ?></strong></p></div>
<?php
  }

  $fields = new stdclass();
  $fields->campaignId = $_GET['campaignId'];
  //var_dump($fields); echo "<br/>";
  $campaign = $toutrix_adserver->campaign_get($fields);

  $fields = new stdclass();
  $fields->campaignId = $_GET['campaignId'];
  $fields->flightId = $_GET['flightId'];
  $flight = $toutrix_adserver->flights_get($fields);

  if (isset($_GET['b'])) {
    $flight->Price = $_GET['Price'];
    $flight->MaxPerIp = $_GET['MaxPerIp'];
    $flight = $toutrix_adserver->flight_update($flight);
  }

  if (isset($_POST['creative']) && $_POST['creative']=='Y') {
    $fields = new stdclass();
    $fields->flightId = $_GET['flightId'];
    $fields->campaignId = $_GET['campaignId'];
    $fields->creativeId = $_POST['creativeId'];
    $fields->IsActive = true;
    $creative_flights = $toutrix_adserver->creative_flight_create($fields);
    //var_dump($creative_flights);
?>
<div class="updated"><p><strong><?php _e('Creative added to the flight', 'wp-toutrix' ); ?></strong></p></div>
<?php
  }

  $cur_tab = 'homepage';	
  if (isset($_GET['tab']))
    $cur_tab = $_GET['tab'];

  $tabs = array( 'homepage' => 'Settings', 'creative' => 'Creatives', 'targets' => 'Targets');
  echo '<div id="icon-themes" class="icon32"><br></div>';
  echo '<h2 class="nav-tab-wrapper">';
  foreach( $tabs as $tab => $name ){
      $class = ( $tab == $cur_tab ) ? ' nav-tab-active' : '';
      echo "<a class='nav-tab$class' href='?page=mt_toutrix_campaign&action=edit&campaignId=" . $_GET['campaignId'] . "&flightId=" . $_GET['flightId'] . "&tab=$tab'>$name</a>";
  }

  echo '</h2>';

  if ($cur_tab == 'homepage') {
    echo "<h1>Update flight for " . $campaign->name . "</h1>";
?>
<form>
<input type='hidden' name='page' value='mt_toutrix_campaign'>
<input type='hidden' name='campaignId' value='<?php echo $_GET['campaignId']; ?>'>
<input type='hidden' name='flightId' value='<?php echo $_GET['flightId']; ?>'>
Price: $<input type='text' name='Price' value='<?php echo $flight->Price; ?>'> (CPM price)<br/>
Max Per IP: <input type='text' name='MaxPerIp' value='<?php echo $flight->MaxPerIp; ?>'><br/>
<input type='submit' name='b' value='Save'>
</form>
<?php
  } elseif ($cur_tab == 'creative') {
    $fields = new stdclass();
    $fields->flightId = $_GET['flightId'];
    $creative_flights = $toutrix_adserver->creative_flight_get($fields);
//var_dump($creative_flights);

    echo "<h2>Creatives for " . $campaign->name . "</h2>";

    $creatives_table = new creatives_table();
    $creatives_table->set_datas($creative_flights);
    $creatives_table->prepare_items();
    $creatives_table->display();
?>
<h2>Add a creative to this flight</h2>
<?php
  $creatives = $toutrix_adserver->creatives_list(array());
?>
<form method='POST'>
<input type='hidden' name='creative' value='Y'>
<select name='creativeId'>
<?php 
foreach ($creatives as $one_crea) {
  if ($all_used[$one_crea->id] <> 1)
    echo "<option value='" . $one_crea->id . "'>" . $one_crea->title . "</option>";
}
?>
</select>
<input type='submit' name='b' value='Add'>
</form>
<?php
  } elseif ($cur_tab == 'targets') {
    echo "<h2>Targets for " . $campaign->name . "</h2>";
    $fields = new stdclass();
    $fields->flightId = $_GET['flightId'];
    $targets = $toutrix_adserver->flight_targets_get($fields);
    toutrix_show_targets($targets);

    $fields = new stdclass();
    $fields->flightId = $_GET['flightId'];

    echo "<h2>Add a new target</h2>";
    toutrix_show_target_form($fields);
  }
}

function toutrix_flight_form($flight) {
?>
<form method='POST'>
<input type='hidden' name='flight' value='yes'>
<?php if (!empty($flight->id)) { ?>
<input type='hidden' name='id' value='<?php echo $flight->id; ?>'>
<?php } ?>
<input type='hidden' name='campaignId' value='<?php echo $flight->campaignId; ?>'>
Name : <input type='text' name='Name' value='<?php echo $flight->Name; ?>'><br/>
Price : <input type='text' name='Price' value='<?php echo $flight->Price; ?>'><br/>
Max per IP : <input type='text' name='MaxPerIp' value='<?php echo $flight->MaxPerIp; ?>'> (0-unlimited)<br/>
<input type='submit' name='b' value='Save'>
</form>
<?php
}

class creatives_table extends WP_List_Table {

    var $datas;

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

    function set_datas($stats) {
        global $toutrix_adserver;

        $arr = array();
        foreach ($stats as $cr_fl) {
          $new_crea = array('id'=>$cr_fl->id, 'flightId' => $cr_fl->flightId, 'creativeId' => $cr_fl->creativeId, 'isActive'=>$cr_fl->IsActive);

          $creative = new stdclass();
          $creative->creativeId = $cr_fl->creativeId;
          $creative = $toutrix_adserver->creative_get($creative);

          //$html = "<span title='Creative # " . $creative->id . "'></span><br/>";
          $html = "#" . $creative->id . "<br/>";
          if (!empty($creative->banner_url)) {
            $html .= "<a href='" . $creativer->url . "' target='_blank'><img src='" .  $creative->banner_url . "'></a>";
          } elseif (!empty($creative->html)) {
            $html .= $creative->html;
          } else {
            $html .= "<a href='" .  $creative->url . "' target='_blank'>" .  $creative->url . "</a>";
          }
          $html .= "<br/>";
          $new_crea['html'] = $html;

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
            case 'status':
                if ($item['isActive']==1) {
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
    function column_creative($item){        
        //Build row actions
        $actions = array(
//            'edit'      => sprintf('<a href="?page=%s&action=%s&campaignId=%s&flightId=%s">Edit</a>',$_REQUEST['page'],'edit',$_GET['campaignId'],$item['id']),
            'delete'    => sprintf('<a href="?page=%s&action=%s&campaignId=%s&flightId=%s&removeTargetId=%s&tab=creative">Delete</a>',$_REQUEST['page'],'edit',$_GET['campaignId'],$item['flightId'],$item['id']),
/*
            'stats'      => sprintf('<a href="?page=%s&action=%s&campaignId=%s">Stats</a>',$_REQUEST['page'],'stats',$item['id']),
*/
        );
        
        //Return the title contents
        return sprintf('%1$s<br/>%2$s',
      	    /*$2%s*/ //$item['id'],
            /*$1%s*/ $item['html'],
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
            'creative'     => 'Creative',
            'status'     => 'Status'
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

class flights_table extends WP_List_Table {

    var $datas;

    /** ************************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We 
     * use the parent reference to set some default configs.
     ***************************************************************************/
    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'flight',     //singular name of the listed records
            'plural'    => 'flights',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );      
    }

    function set_datas($stats) {
        $arr = array();
        foreach ($stats as $flight) {
          $new_crea = array('id'=>$flight->id, 'name' => $flight->Name, 'price' => $flight->Price, 'IsActive'=>$flight->IsActive);
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
            case 'name':
                return $item[$column_name];
            case 'price':
                return '$' . $item[$column_name];
            case 'status':
                if ($item['IsActive']==1) {
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
    function column_name($item){
        
        //Build row actions
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&campaignId=%s&flightId=%s">Edit</a>',$_REQUEST['page'],'edit',$_GET['campaignId'],$item['id']),
            'delete'    => sprintf('<a href="?page=%s&action=%s&campaignId=%s&flightId=%s">Delete</a>',$_REQUEST['page'],'delete',$_GET['campaignId'],$item['id']),
/*
            'stats'      => sprintf('<a href="?page=%s&action=%s&campaignId=%s">Stats</a>',$_REQUEST['page'],'stats',$item['id']),
*/
        );
        
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
            'name'     => 'Name',
            'price'     => 'Price',
            'status'     => 'Status'
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
