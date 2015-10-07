<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class toutrix_table extends WP_List_Table {
    var $datas;

    function sort_data() {
    }

    function column_default($item, $column_name){
        switch($column_name){
            case 'the_status':
                return $item[$column_name];
            case 'day':
                return $item[$column_name];
            case 'country':
                return $item[$column_name] . " <img src='" . plugins_url( 'flags/' . strtolower($item[$column_name]) . '.png', __FILE__ ) . "'>";
            case 'nbr_impressions':
                return $item[$column_name];
            case 'nbr_clicks':
                return $item[$column_name];
            case 'cost':
                return "$" . number_format($item[$column_name],2);
            case 'revenu':
                return "$" . number_format($item[$column_name],2);

            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
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
            /*$2%s*/ $item['id']                //The value of the checkbox should be the record's id
        );
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
        $data = $this->datas;
        
        /**
         * This checks for sorting input and sorts the data in our array accordingly.
         * 
         * In a real-world situation involving a database, you would probably want 
         * to handle sorting by passing the 'orderby' and 'order' values directly 
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary.
         */

        $this->sort_data();
        
        
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
        $data = array_slice($this->datas,(($current_page-1)*$per_page),$per_page);
        
        
        
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

class stats_per_country_table extends toutrix_table {
    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'stat_per_country',     //singular name of the listed records
            'plural'    => 'stat_per_countries',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );      
    }

    function set_datas($stats) {
        $arr = array();
        foreach ($stats as $country => $stat) {
          $new_crea = array('country'=>$country, 'nbr_impressions' => $stat->nbr_impressions + $stat->own_impressions, 'nbr_clicks' => $stat->nbr_clicks + $stat->own_clicks, 'cost'=>$stat->cost);
          $arr[] = $new_crea;
        }
        $this->datas = $arr;
    }

    function get_columns(){
        $columns = array(
            'country'     => 'Country',
            'nbr_impressions'     => 'Impressions',
            'nbr_clicks'     => 'Clicks',
            'cost'     => 'Cost',
        );
        return $columns;
    }

    function column_title($item){
        
        //Build row actions
        $actions = array(
        );
        
        //Return the title contents
        return sprintf('%1$s',
            /*$1%s*/ $item['country']
        );
    }
}

class stats_revenu_per_country_table extends stats_per_country_table {

    function set_datas($stats) {
        $arr = array();
        foreach ($stats as $country => $stat) {
          $new_crea = array('country'=>$country, 'nbr_impressions' => $stat->nbr_impressions + $stat->own_impressions, 'nbr_clicks' => $stat->nbr_clicks + $stat->own_clicks, 'revenu'=>$stat->revenu);
          $arr[] = $new_crea;
        }
        $this->datas = $arr;
    }

    function get_columns(){
        $columns = array(
            'country'     => 'Country',
            'nbr_impressions'     => 'Impressions',
            'nbr_clicks'     => 'Clicks',
            'revenu'     => 'Revenu',
        );
        return $columns;
    }

}

class stats_per_day_table extends toutrix_table {

    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'stat_per_day',     //singular name of the listed records
            'plural'    => 'stat_per_days',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );      
    }

    function get_columns(){
        $columns = array(
            'day'     => 'Day',
            'nbr_impressions'     => 'Impressions',
            'nbr_clicks'     => 'Clicks',
            'cost'     => 'Cost',
        );
        return $columns;
    }

    function set_datas($stats) {
        $arr = array();
        foreach ($stats as $day => $stat) {
          $new_crea = array('day'=>$day, 'nbr_impressions' => $stat->nbr_impressions + $stat->own_impressions, 'nbr_clicks' => $stat->nbr_clicks + $stat->own_clicks, 'cost'=>$stat->cost);
          $arr[] = $new_crea;
        }
        $this->datas = $arr;
    }

}

class stats_revenue_per_day_table extends stats_per_day_table {

    function get_columns(){
        $columns = array(
            'day'     => 'Day',
            'nbr_impressions'     => 'Impressions',
            'nbr_clicks'     => 'Clicks',
            'revenu'     => 'Revenu',
        );
        return $columns;
    }

    function set_datas($stats) {
        $arr = array();
        foreach ($stats as $day => $stat) {
          $new_crea = array('day'=>$day, 'nbr_impressions' => $stat->nbr_impressions + $stat->own_impressions, 'nbr_clicks' => $stat->nbr_clicks + $stat->own_clicks, 'revenu'=>$stat->revenu);
          $arr[] = $new_crea;
        }
        $this->datas = $arr;
    }

}


class stats_per_countries_table extends toutrix_table {

    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'stat_per_country',     //singular name of the listed records
            'plural'    => 'stat_per_countries',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );      
    }

}

