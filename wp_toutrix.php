<?php
/*
Plugin Name: TouTrix AdServer
Plugin URI:  http://toutrix.com/wp_toutrix
Description: This plugin connect to TouTrix AdMedia Server, create zone to earn money to show ads. You can also ask a withdrawal without leaving your website.
Version:     0.6.35
Author:      TouTrix
Author URI:  http://toutrix.com/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: toutrix-adserver
*/

define('toutrix_plugin_version','0.6.35');

// TODO - Error manager from the API. We don't check for error at all for the moment.
// TODO - Validation before submiting
// TODO - Add ads inside article

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once( 'classes/github-updater.php' );
if ( is_admin() ) {
    new GitHubPluginUpdater( __FILE__, 'TouTrix', "wp_toutrix" );
}

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

require "config.php";
require "table.class.php";
require "countries.php";
require "languages.php";
require "classes/toutrix_php/api_toutrix.php";
require "homepage.php";
require "coming.php";
require "settings.php";
require "creative.php";
require "campaign.php";
require "flights.php";
require "target.php";
require "widget.php";
require "bank.php";
require "stats.php";
require "user.php";
require "marketplace.php";
require "content.php";
require "inventory.php";
include_once('classes/github-updater/updater.php');

add_action('plugins_loaded', 'wan_load_textdomain');
function wan_load_textdomain() {
	load_plugin_textdomain( 'wp-toutrix', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
}

add_option( 'ad_toutrix_username', '', '', 'yes' );
add_option( 'ad_toutrix_password', '', '', 'yes' );
add_option( 'ad_toutrix_access_token', '', '', 'yes' );
add_option( 'ad_toutrix_website_id', '', '', 'yes' );
add_option( 'ad_toutrix_zone_id', '', '', 'yes' );

// Hook for adding admin menus
add_action('admin_menu', 'toutrix_add_pages');


//add_action( 'widgets_init', 'register_my_widget' );

global $toutrix_adserver;
$toutrix_adserver = new api_toutrix_adserver();

global $toutrix_zoneId;

if (is_admin())
  toutrix_connect();

// action function for above hook
function toutrix_add_pages() {
    add_menu_page(__('TouTrix','menu-toutrix'), __('TouTrix','wp-toutrix'), 'manage_options', 'mt_toutrix_page-handle', 'mt_toutrix_page');

    //add_submenu_page('mt_toutrix_page-handle', __('Settings','wp-toutrix'), __('Settings','wp-toutrix'), 'manage_options', 'toutrix_setting_page', 'toutrix_settings_page');

    add_submenu_page('mt_toutrix_page-handle', __('Stats','wp-toutrix'), __('Stats','wp-toutrix'), 'manage_options', 'mt_toutrix_stats_page', 'mt_toutrix_stats_page');

    add_submenu_page('mt_toutrix_page-handle', __('Creatives','wp-toutrix'), __('Creatives','wp-toutrix'), 'manage_options', 'toutrix_creative', 'toutrix_creative_page');

    add_submenu_page('mt_toutrix_page-handle', __('Campaigns','wp-toutrix'), __('Campaigns','wp-toutrix'), 'manage_options', 'mt_toutrix_campaign', 'mt_toutrix_campaign_page');

    add_submenu_page('mt_toutrix_page-handle', __('Marketplace','wp-toutrix'), __('Marketplace','wp-toutrix'), 'manage_options', 'mt_toutrix_marketplace', 'mt_toutrix_marketplace_page');

    add_submenu_page('mt_toutrix_page-handle', __('Bank','wp-toutrix'), __('Bank','wp-toutrix'), 'manage_options', 'mt_toutrix_bank', 'mt_toutrix_bank_page');

    add_submenu_page('mt_toutrix_page-handle', __('Inventory','wp-toutrix'), __('Inventory','wp-toutrix'), 'manage_options', 'mt_toutrix_inventory', 'mt_toutrix_inventory_page');
}

function toutrix_get_channels() {
  global $toutrix_adserver;
  return $toutrix_adserver->channels_get(array());
}

function toutrix_get_token() {
    global $toutrix_adserver;

    $toutrix_username = get_option("ad_toutrix_username");
    $toutrix_password  = get_option("ad_toutrix_password");
    $toutrix_access_token  = get_option("ad_toutrix_access_token");
    $toutrix_user_id  = get_option("ad_toutrix_user_id");
//echo "Current username: " . $toutrix_username . "<br/>";
//echo "Current token: " . $toutrix_access_token . "<br/>";
    if (strlen($toutrix_access_token)>0) {
//echo "Set access token : " . $toutrix_access_token . "<br/>";
      $toutrix_adserver->setAccessToken($toutrix_access_token, $toutrix_user_id);
      return true;
    } elseif (strlen($toutrix_username)>0 && strlen($toutrix_password)>0) {
      // Si nous n'avons pas d'access token, ca nous en prend un
      if ($toutrix_adserver->login($toutrix_username, $toutrix_password)) {
         echo "New access Token is now: " . $toutrix_adserver->access_token . "<br/>";
         update_option( "ad_toutrix_access_token", $toutrix_adserver->access_token );
         update_option( "ad_toutrix_user_id", $toutrix_adserver->userId );
//$toutrix_access_token2  = get_option("ad_toutrix_access_token");
//echo "Updated to " . $toutrix_access_token2 . "<br/>";
         return true;
       } else {
?>
<div class="updated"><p><strong><?php _e('Cant connect with these credentials.', 'menu-test' ); ?></strong></p></div>
<?php
         update_option( "ad_toutrix_access_token", '' );
       }
    }
    return false;
}

function mt_toutrix_stats_page() {
  toutrix_site_show_stats(null);
}

function toutrix_connect() {
  global $user;
  global $toutrix_adserver;

  if (toutrix_get_token()) {
    $user = $toutrix_adserver->get_user();
//var_dump($user); echo "<br>";
    if ($user->error) {
      update_option( "ad_toutrix_access_token", '');
//echo "Getting new token..<br/>";
      if (!toutrix_get_token()) {
        echo "TOUTRIX PROBLEM<br/>";
        die();
      } else {
        update_option( "ad_toutrix_access_token", $toutrix_adserver->access_token );
//echo "Saving new address token to : " . $toutrix_adserver->access_token . "<br/>";
      }
      $user = $toutrix_adserver->get_user();
   }
//var_dump($user);
  }
}

function echo_funds_available() {
  global $user;
  global $toutrix_adserver;

  $user = $toutrix_adserver->get_user();
  echo "<font size='5'><b>Funds available: </b> <font color='green'><a href='?page=mt_toutrix_bank'>$" . number_format($user->funds,2) . "</a></font></font><br/>";
}
