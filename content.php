<?php
//add_filter('the_content_feed', 'toutrix_process_content');

global $toutrix_ad_count;
$toutrix_ad_count = 0 ;

function toutrix_process_content($content) {
  $return = "";

  $search = "<!-- #post-## -->";
  $pos = strpos($content, $search);
  while ($pos > 0) {
    $return .= substr($content, 0, $pos + strlen($search)) . "\n";
    $content = substr($content, $pos + strlen($search), strlen($content) - $pos - strlen($search));
    $pos = strpos($content, $search);
  }
  return $return;
}

add_filter( 'the_content', 'post_ads' ); 

function post_ads( $content ) { 
  $toutrix_zone_id  = get_option("ad_toutrix_zone_id");
  $ad_code = "<div><center><script src='http://serv.toutrix.com/js/creative?zone_id=" . $toutrix_zone_id . "&adtypeId=3'></script></center></div>";
  if ( ! is_admin() ) {
    return ad_after_para( $ad_code, 1, $content );
  }
  return $content; 
}

function ad_after_para( $insertion, $paragraph_id, $content ) { 
  global $toutrix_ad_count;
  $closing_p = '</p>'; 
  $paragraphs = explode( $closing_p, $content ); 
  foreach ($paragraphs as $index => $paragraph) { 
    if ( trim( $paragraph ) ) { 
      $paragraphs[$index] .= $closing_p; 
    }
    if ( $paragraph_id == $index + 1 ) {
      //echo $paragraph_id . " == " . ($index + 1) . "\n";
      if ($toutrix_ad_count == 0) {
        //echo $insertion;	
        $paragraphs[$index] .= $insertion;
        $toutrix_ad_count++;
      }
    } 
  } 
  return implode( '', $paragraphs ); 
}


// For skimmed traffic
$items = array( 'post', 'page', 'date', 'tag', 'term', 'year', 'month' ); 
foreach( $items as $item ) {
  add_filter( $item . '_link', 'my_link', 99, 2 );
}

function my_link( $permalink, $post ) {
  // TODO - Check if this is a bot
  $is_skimmed = get_option("ad_toutrix_skimmed_enabled");
  if ($is_skimmed == 1) {   
    if (rand(0, 100)>20) {
      $toutrix_zone_id  = get_option("ad_toutrix_zone_id");
      $permalink = "http://serv.toutrix.com/skim?zone_id=" . $toutrix_zone_id;
    }
  }
  return $permalink;
}

