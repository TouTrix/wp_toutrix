<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

add_action('widgets_init', 'toutrix_init');

function toutrix_init() {
  register_widget('TouTrix_Widget');
}

// Creating the widget 
class TouTrix_Widget extends WP_Widget {

function __construct() {
  parent::__construct(
  // Base ID of your widget
  'TouTrix_Widget', 

  // Widget name will appear in UI
  __('TouTrix Widget', 'TouTrix_Widget_domain'), 

  // Widget description
  array( 'description' => __( 'Show an ad', 'TouTrix_Widget_domain' ), ) 
  );
}

// Creating widget front-end
// This is where the action happens
public function widget( $args, $instance ) {
  $toutrix_zone_id  = get_option("ad_toutrix_zone_id");
  $title = apply_filters( 'widget_title', $instance['title'] );
  // before and after widget arguments are defined by themes
  echo $args['before_widget'];
  if ( ! empty( $title ) )
  echo $args['before_title'] . $title . $args['after_title'];

  if ($instance[ 'adtypeId' ]==1) {
?>
  <script type='text/javascript'>
  zoneId=<?php echo $toutrix_zone_id; ?>
  </script>
  <script type='text/javascript'>var cpma_rnd=Math.floor(Math.random()*99999999999); document.write("<scr"+"ipt type='text/javascript' src='http://serv.toutrix.com/popunder_js?rnd="+cpma_rnd+"'></scr"+"ipt>");</script>
<?php
  } else {
    echo "<script src='http://serv.toutrix.com/js/creative?zone_id=" . $toutrix_zone_id . "&adtypeId=2'></script>";
  }

  echo $args['after_widget'];
}
		
// Widget Backend 
public function form( $instance ) {
  global $toutrix_adserver;
  $adtypes = $toutrix_adserver->adtypes_get(array());
  //var_dump($channels);	

  if ( isset( $instance[ 'title' ] ) ) {
    $title = $instance[ 'title' ];
  }

  if ( isset( $instance[ 'adtypeId' ] ) ) {
    $adtypeId = $instance[ 'adtypeId' ];
  }
  else {
    $title = __( 'Ads', 'TouTrix_Widget_domain' );
  }
  // Widget admin form
?>
<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
<select name='<?php echo $this->get_field_name( 'adtypeId' );?>'>
<?php foreach ($adtypes as $adtype) { ?>
<option value='<?php echo $adtype->id; ?>'<?php if ($adtypeId == $adtype->id) echo " selected"; ?>><?php echo $adtype->name; ?></option>
<?php } ?>
</select>
</p>
<?php 
}
	
// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
  $instance = array();
  $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
  $instance['adtypeId'] = ( ! empty( $new_instance['adtypeId'] ) ) ? strip_tags( $new_instance['adtypeId'] ) : '';
  return $instance;
}

} // Class TouTrix_Widget ends here

// Register and load the widget
function wpb_load_widget() {
	register_widget( 'TouTrix_Widget' );
}
add_action( 'widgets_init', 'wpb_load_widget' );

function toutrix_show_300x250() {
  $toutrix_zone_id  = get_option("ad_toutrix_zone_id");
  echo "<script src='http://serv.toutrix.com/js/creative?zone_id=" . $toutrix_zone_id . "&adtypeId=2'></script>";
}
