<?php
//add_filter('the_content_feed', 'toutrix_process_content');

global $toutrix_ad_count;
$toutrix_ad_count = 0 ;

function toutrix_process_content($content) {
/*
  if ((is_feed()) ||
			(strpos($content,'<!--NoAds-->')!==false) ||
			(strpos($content,'<!--OffAds-->')!==false) ||
			(is_single() && !(get_option('AppPost'))) ||
			(is_page() && !(get_option('AppPage'))) ||
			(is_home() && !(get_option('AppHome'))) ||			
			(is_category() && !(get_option('AppCate'))) ||
			(is_archive() && !(get_option('AppArch'))) ||
			(is_tag() && !(get_option('AppTags'))) ||
			(is_user_logged_in() && (get_option('AppLogg'))) ) { 
    return $content; 
  }
*/

  $return = "";

  $search = "<!-- #post-## -->";
  $pos = strpos($content, $search);
  while ($pos > 0) {
    $return .= substr($content, 0, $pos + strlen($search)) . "\nADS HERE";
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

