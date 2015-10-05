<?php
/*
Plugin Name: TouTrix AdServer
Plugin URI:  http://toutrix.com/wp_toutrix
Description: This plugin connect to TouTrix AdMedia Server, create zone to earn money to show ads. You can also ask a withdrawal without leaving your website.
Version:     0.5.33
Author:      TouTrix
Author URI:  http://toutrix.com/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: toutrix-adserver
*/

define('toutrix_plugin_version','0.5.33');

require_once( 'classes/github-updater.php' );
if ( is_admin() ) {
    new GitHubPluginUpdater( __FILE__, 'TouTrix', "wp_toutrix" );
}

// TODO - Error manager from the API. We don't check for error at all for the moment.
// TODO - Validation before submiting
// TODO - Add ads inside article

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require "config.php";
require "classes/toutrix_php/api_toutrix.php";
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

/*
    if (is_admin()) { // note the use of is_admin() to double check that this is happening in the admin
        $config = array(
            'slug' => plugin_basename(__FILE__), // this is the slug of your plugin
            'proper_folder_name' => 'wp_toutrix', // this is the name of the folder your plugin lives in
            'api_url' => 'https://api.github.com/repos/TouTrix/wp_toutrix', // the GitHub API url of your GitHub repo
            'raw_url' => 'https://raw.github.com/TouTrix/wp_toutrix/master', // the GitHub raw url of your GitHub repo
            'github_url' => 'https://github.com/TouTrix/wp_toutrix', // the GitHub url of your GitHub repo
            'zip_url' => 'https://github.com/TouTrix/wp_toutrix/zipball/master', // the zip url of the GitHub repo
            'sslverify' => true, // whether WP should check the validity of the SSL cert when getting an update, see https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/2 and https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/4 for details
            'requires' => '3.0', // which version of WordPress does your plugin require?
            'tested' => '4.3.1', // which version of WordPress is your plugin tested up to?
            'readme' => 'README.md', // which file to use as the readme for the version number
            'access_token' => '', // Access private repositories by authorizing under Appearance > GitHub Updates when this example plugin is installed
        );
        new WP_GitHub_Updater($config);
    }
*/

add_option( 'ad_toutrix_username', '', '', 'yes' );
add_option( 'ad_toutrix_password', '', '', 'yes' );
add_option( 'ad_toutrix_access_token', '', '', 'yes' );
add_option( 'ad_toutrix_website_id', '', '', 'yes' );
add_option( 'ad_toutrix_zone_id', '', '', 'yes' );

// Hook for adding admin menus
add_action('admin_menu', 'toutrix_add_pages');

//wp_enqueue_style('admin_css_toutrix', plugins_url( 'css/toutrix.css', __FILE__ ), false, '1.0.0', 'all');

if(is_admin()) define('SAM_IS_ADMIN', true);

//include_once('ad.class.php');
include_once('toutrix.class.php');

if (is_admin()) {
  include_once('admin.class.php');
	if (class_exists("ToutrixAdmin") && class_exists("ToutrixManager")) 
		$samObject = new ToutrixManager();
}
else {
	if (class_exists("ToutrixManager")) $samObject = new ToutrixManager();
}



//add_action( 'widgets_init', 'register_my_widget' );

global $toutrix_adserver;
$toutrix_adserver = new api_toutrix_adserver();

global $toutrix_zoneId;

// action function for above hook
function toutrix_add_pages() {
    add_menu_page(__('TouTrix','menu-toutrix'), __('TouTrix','wp-toutrix'), 'manage_options', 'mt_toutrix_page-handle', 'mt_toutrix_page');

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

    //if (strlen($toutrix_access_token)>0) {
    //  $adserver->setAccessToken($toutrix_access_token);
    //} else
    if (strlen($toutrix_username)>0 && strlen($toutrix_password)>0) {
        // Si nous n'avons pas d'access token, ca nous en prend un
        //if (strlen($toutrix_access_token)==0) {
           if ($toutrix_adserver->login($toutrix_username, $toutrix_password)) {
             //echo "Access Token is now: " . $adserver->access_token . "<br/>";
             update_option( "ad_toutrix_access_token", $toutrix_adserver->access_token );
             return true;
           } else {
?>
<div class="updated"><p><strong><?php _e('Cant connect with these credentials.', 'menu-test' ); ?></strong></p></div>
<?php
             update_option( "ad_toutrix_access_token", '' );
           }
        //}
    }
    return false;
}

// mt_toplevel_page() displays the page content for the custom Test Toplevel menu
function mt_toutrix_page() {
    global $toutrix_adserver;
    echo "<script src='http://serv.toutrix.com/serv/tag?tagId=1'></script>";
    echo "<div class='container'>";

    echo "<center><a href='http://toutrix.com/2015/09/07/we-are-looking-for-developpers/'>We are looking for developpers</a></center><br/>";

    echo "<h2>AdMedia configuration</h2>";

    echo "Current version: " . toutrix_plugin_version . "<br/><br/>";

    //must check that the user has the required capability 
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }

    // Read in existing option value from database
    $toutrix_username = get_option("ad_toutrix_username");
    $toutrix_password  = get_option("ad_toutrix_password");
    $toutrix_access_token  = get_option("ad_toutrix_access_token");
    $toutrix_website_id  = get_option("ad_toutrix_website_id");
    $toutrix_zone_id  = get_option("ad_toutrix_zone_id");

    if( isset($_POST[ "signup" ]) && $_POST[ "signup" ] == 'Y' ) {
        update_option( "ad_toutrix_access_token", "" );
        update_option( "ad_toutrix_website_id", "" );
        update_option( "ad_toutrix_zone_id", "" );
        update_option( "ad_channel_id", $_POST[ 'channelId'] );

        // Read their posted value
        $toutrix_username = $_POST[ "ad_toutrix_username" ];
        $toutrix_password = $_POST[ "ad_toutrix_password" ];
        $user = new stdClass();
        $user->username = $_POST[ "ad_toutrix_username" ];
        $user->password = $_POST[ "ad_toutrix_password" ];
        $user->email = $_POST[ "ad_toutrix_email" ];
        $user->refererId = $_POST[ "refererId" ];
//var_dump($adserver);
        $user = $toutrix_adserver->user_create($user);
        if ($user->error && !$user->error->message == "path is not defined") {
?>
<div class="updated"><p><strong><?php _e($user->error->message, 'menu-test' ); ?></strong></p></div>
<?php
        } else {
          update_option( "ad_toutrix_username", $toutrix_username  );
          update_option( "ad_toutrix_password", $toutrix_password  );
?>
<div class="updated"><p><strong><?php _e('TouTrix account is created.', 'menu-test' ); ?></strong></p></div>
<?php
        }
    }

    if( isset($_POST[ "config" ]) && $_POST[ "config" ] == 'Y' ) {
        // Read their posted value
        $toutrix_username = $_POST[ "ad_toutrix_username" ];
        $toutrix_password = $_POST[ "ad_toutrix_password" ];

        // Save the posted value in the database
        update_option( "ad_toutrix_username", $toutrix_username  );
        update_option( "ad_toutrix_password", $toutrix_password  );

        // Put a "settings saved" message on the screen

?>
<div class="updated"><p><strong><?php _e('settings saved.', 'menu-test' ); ?></strong></p></div>
<?php
    }
	
  if (strlen($toutrix_username)==0 && strlen($toutrix_password)==0) {
     $channels = toutrix_get_channels();
     echo '<div class="wrap">';
?>

<h1>Create your account now</h2>
Fill-up the form to create your account now.<br/>
<form name="form1" method="post" action="">
<input type="hidden" name="signup" value="Y">
<input type="hidden" name="refererId" value="<?php echo referer_id; ?>">

<p><?php _e("Username:", 'menu-test' ); ?> 
<input type="text" name="ad_toutrix_username" value="<?php echo $toutrix_username; ?>" size="20">
</p>

<p><?php _e("Password:", 'menu-test' ); ?> 
<input type="text" name="ad_toutrix_password" value="<?php echo $toutrix_password; ?>" size="20">
</p>

<p><?php _e("Channel:", 'menu-test' ); ?> 
<select name='channelId'>
<?php foreach ($channels as $channel) { ?>
<option value='<?php echo $channel->id; ?>'><?php echo $channel->Title; ?></option>
<?php } ?>
</select> Choose the good channel for your website. We may change it for you later.
</p>

<p><?php _e("Your email:", 'menu-test' ); ?> 
<input type="text" name="ad_toutrix_email" value="<?php echo $toutrix_email; ?>" size="20">
</p>

<hr />

<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php _e("Signup on TouTrix", 'menu-test'); ?>" />
</p>

</form>
</div>

<?php } elseif (strlen($toutrix_username)>0 && strlen($toutrix_password)>0) {
       //echo "Getting token<br/>";
       if (toutrix_get_token()) {
          //echo "Got Token<br/>";
          $toutrix_access_token  = get_option("ad_toutrix_access_token");
          $toutrix_website_id  = get_option("ad_toutrix_website_id");
          $toutrix_channel_id  = get_option("ad_channel_id");

          if (strlen($toutrix_website_id)==0) {
              echo "Creating website...<br/>";              

              $site = new stdClass();
              $site->Title = get_bloginfo();;
              $site->Url = get_site_url();
              $site->Description = get_bloginfo ( 'description' );
              $site->channelId = $toutrix_channel_id;
//var_dump($site);
//echo "<br/>";
              $site = $toutrix_adserver->site_create($site);
//              echo "Create website: ";
//              var_dump($site);
//echo "<br/>";
              if ($site->id > 0) {
                update_option( "ad_toutrix_website_id", $site->id);
                $toutrix_website_id = $site->id;
              } else {
                echo "<font color='red'>Can't find WebSite ID</font>";
              }
              echo "<br/>";
          }
          if (strlen($toutrix_zone_id)==0 && strlen($toutrix_website_id)>0) {
              //echo "Creating zone...<br/>";
              $zone = new stdClass();
              $zone->Name = get_bloginfo();
              $zone->siteId = $toutrix_website_id;
              $zone->channelId = $toutrix_channel_id;
	
              $zone = $toutrix_adserver->zone_create($zone);
              echo "Create zone: ";
              //var_dump($zone);
              if ($zone->id > 0) {
                update_option( "ad_toutrix_zone_id", $zone->id);
                $toutrix_zone_id = $zone->id;
              } else {
                echo "<font color='red'>Can't find Zone ID</font>";
              }
              echo "<br/>";
          }
       }
    }

    global $user;
    $user = $toutrix_adserver->get_user();

    echo "<font size='5'><b>Funds available: </b> <font color='green'>$" . number_format($user->funds,2) . "</font></font><br/>";

?>
<h1>Connect with your TouTrix account</h2>
<?php
    echo '<div class="wrap">';
    ?>
<form name="form1" method="post" action="">
<input type="hidden" name="config" value="Y">

<p><?php _e("TouTrix Username:", 'menu-test' ); ?> 
<input type="text" name="ad_toutrix_username"" value="<?php echo $toutrix_username; ?>" size="20">
</p>

<p><?php _e("TouTrix password:", 'menu-test' ); ?> 
<input type="text" name="ad_toutrix_password" value="<?php echo $toutrix_password; ?>" size="20">
</p>

<p><?php _e("Access Token:", 'menu-test' ); ?> 
<?php echo $toutrix_access_token; ?>
</p>

<p><?php _e("Website ID:", 'menu-test' ); ?> 
<?php echo $toutrix_website_id; ?>
</p>

<p><?php _e("Default Zone ID:", 'menu-test' ); ?> 
<?php echo $toutrix_zone_id; ?>
</p>

<hr />

<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</p>

</form>
</div>

<?php
    echo "</div>";

    toutrix_user_form();
}

function mt_toutrix_stats_page() {
  toutrix_site_show_stats(null);
}
