<?php
// TODO - Add caching on some models like creatives

define("channel_mainstream",1);
define("channel_adult",2);

class api_toutrix_adserver extends api_toutrix {
  // TODO - Change to real server, and to https
  var $endpoint = "http://serv.toutrix.com/api";

  var $p_login_user = "/users/login";
  var $p_user = "/users";
  var $p_channels = "/channels";
  var $p_adtypes = "/adtypes";
  var $p_campaign = "/users/:userId/campaigns";
  var $p_campaign_target = "/campaigns/:campaignId/target";
  var $p_campaign_update = "/campaigns/:id";
  var $p_campaign_flight = "/campaigns/:campaignId/flights/:flightId";
  var $p_creative = "/users/:userId/creatives";
  var $p_creative_update = "/creatives/:id";
  var $p_user_creative = "/users/:userId/creatives/:creativeId";
  var $p_user_campaign = "/users/:userId/campaigns/:campaignId";
  var $p_sites = "/users/:userId/sites";
  var $p_zones = "/sites/:siteId/zones";
  var $p_flight_update = "/flights/:id";
  var $p_flight = "/campaigns/:campaignId/flights";
  var $p_flight_target = "/flights/:flightId/targets";
  var $p_creative_flight = "/creatives_flight";
  var $p_creatives_flight = "/flights/:flightId/creative_flight";
  var $p_creative_flight_one = "/creatives_flight/:id";
  var $p_target = "/targetings";

  // Users

  function login($username, $password) {
     $datas = array('path'=> $this->p_login_user,
                    'method'=> 'POST',
                    'fields'=> array('username'=>$username, 'password'=>$password) );
     $output = $this->launch_request($datas);
     if (strlen($output)==0) {
       echo "Server not reachable\n";
       return false;
     }

     $returns = json_decode($output,false);
     if ($returns->error != null) {
       echo "ERROR\n";
       return false;
     }
     $this->access_token = $returns->id;
     $this->userId = $returns->userId;
     //echo "Access token is: " . $this->access_token . "\n";
     if (strlen($this->access_token)>0) {
       return true;
     } else {
       return false;
     }
  }

  function user_create($fields) {
     $path = $this->do_path($this->p_user, $fields);
     return $this->model_create($path, $fields);
  }


  function setAccessToken($token) {
    $this->access_token = $token;
    // TODO - We have to get the userId
  }

  function model_create($path,$fields) {
     $datas = array('path'=> $path,
                    'method'=> 'POST',
                    'fields'=> $fields
                   );
     $output = $this->launch_request($datas);
     return json_decode($output,false);
  }

  function model_get($path,$fields) {
     $datas = array('path'=> $path,
                    'method'=> 'GET',
                    'fields'=> $fields
                   );
     $output = $this->launch_request($datas);
     return json_decode($output, false);
  }

  function model_put($path,$fields) {
     $datas = array('path'=> $path,
                    'method'=> 'PUT',
                    'fields'=> $fields
                   );
     $output = $this->launch_request($datas);
     return json_decode($output, false);
  }

  // Channels

  function channels_get($fields) {
     $path = $this->do_path($this->p_channels, $fields);
     return $this->model_get($path, $fields);
  }

  // AdTypes

  function adtypes_get($fields) {
     $path = $this->do_path($this->p_adtypes, $fields);
     return $this->model_get($path, $fields);
  }

  // Campaigns

  function campaign_create($fields) {
     $path = $this->do_path($this->p_campaign, $fields);
     return $this->model_create($path, $fields);
  }

  function campaigns_get($fields) {
     $path = $this->do_path($this->p_campaign, $fields);
     return $this->model_get($path, $fields);
  }

  function campaign_get($fields) {
     $path = $this->do_path($this->p_user_campaign, $fields);
     return $this->model_get($path, $fields);
  }

  function campaigns_list($fields) {
     $path = $this->do_path($this->p_campaign, $fields);
     return $this->model_get($path, $fields);
  }

  function campaign_update($fields) {
     $path = $this->do_path($this->p_campaign_update, $fields);
     return $this->model_put($path, $fields);
  }

  function campaign_targets($fields) {
     $path = $this->do_path($this->p_campaign_target, $fields);
     return $this->model_get($path, $fields);
  }

  // Creative

  function creative_create($fields) {
     $path = $this->do_path($this->p_creative, $fields);
     return $this->model_create($path, $fields);
  }

  function creatives_list($fields) {
     $path = $this->do_path($this->p_creative, $fields);
     return $this->model_get($path, $fields);
  }

  function creative_get($fields) {
     $path = $this->do_path($this->p_user_creative, $fields);
     return $this->model_get($path, $fields);
  }

  function creative_update($fields) {
     $path = $this->do_path($this->p_creative_update, $fields);
     return $this->model_put($path, $fields);
  }

  // Site

  function site_create($fields) {
     $path = $this->do_path($this->p_sites, $fields);
     return $this->model_create($path, $fields);
  }

  // Zone

  function zone_create($fields) {
     $path = $this->do_path($this->p_zones, $fields);
     return $this->model_create($path, $fields);
  }

  // Flight

  function flight_create($fields) {
     $path = $this->do_path($this->p_flight, $fields);
     return $this->model_create($path, $fields);
  }

  function flights_get($fields) {  
    if (isset($fields->campaignId) && isset($fields->flightId)) {
      $path = $this->do_path($this->p_campaign_flight, $fields);
    } else {
      $path = $this->do_path($this->p_flight, $fields);
    }
    //echo $path . "<br/>";
    return $this->model_get($path, $fields);
  }

  function flight_update($fields) {
     $path = $this->do_path($this->p_flight_update, $fields);
     // TODO - Create an update function
     $datas = array('path'=> $path,
                    'method'=> 'PUT',
                    'fields'=> $fields
                   );
     $output = $this->launch_request($datas);
     $returns = json_decode($output,false);
     return $returns;
  }

  function flight_targets_get($fields) {
     $path = $this->do_path($this->p_flight_target, $fields);
     //echo $path . "<br/>";
     return $this->model_get($path, $fields);
  }

  // Creative Flight

  function creative_flight_create($fields) {
     $path = $this->do_path($this->p_creative_flight, $fields);
     return $this->model_create($path, $fields);
  }

  function creative_flight_get($fields) {
     $path = $this->do_path($this->p_creatives_flight . "?filter[where][IsDeleted][neq]=1", $fields);
     return $this->model_get($path, $fields);
  }

  function creative_flight_save($fields) {
     if (isset($fields->id)) {
       $path = $this->do_path($this->p_creative_flight_one, $fields);
     } else {
       $path = $this->do_path($this->p_creative_flight, $fields);
     }
     return $this->model_put($path, $fields);
  }

  // Target

  function target_create($fields) {
     $path = $this->do_path($this->p_target, $fields);
     return $this->model_create($path, $fields);
  }

  function targeting_get($fields) {
     $path = $this->do_path($this->p_target, $fields);
     return $this->model_get($path, $fields);
  }
}

class api_toutrix {
  var $ch;
  var $last_code;
  var $access_token;
  var $userId;

  function __construct() {

  }

  function reset_ch() {
    $this->ch = curl_init();
    $arr = array();

    curl_setopt ($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt ($this->ch, CURLOPT_SSL_VERIFYPEER, 0); 
    curl_setopt ($this->ch, CURLOPT_RETURNTRANSFER, 1); 
    $headers = array(
      'Accept: application/json',
      'Content-Type: application/json',
    );
    curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
  }

  function do_path($path, $fields) {
    //var_dump($fields);
    $result = $path;
    if (!empty($fields->id))
      $result = str_replace(':id', $fields->id, $result);
    if (!empty($fields->campaignId))
      $result = str_replace(':campaignId', $fields->campaignId, $result);
    if (!empty($this->userId))
      $result = str_replace(':userId', $this->userId, $result);
    if (!empty($fields->siteId))
      $result = str_replace(':siteId', $fields->siteId, $result);
    if (!empty($fields->zoneId))
      $result = str_replace(':zoneId', $fields->zoneId, $result);
    if (!empty($fields->creativeId))
      $result = str_replace(':creativeId', $fields->creativeId, $result);
    if (!empty($fields->flightId))
      $result = str_replace(':flightId', $fields->flightId, $result);
    return $result;
  }

  function launch_request($datas) {
    $this->reset_ch();

    $url = $this->endpoint . $datas['path'];

    if (strlen($this->access_token)>0)
      if (strpos($url,'?')>0) {
        $url .= "&access_token=" . $this->access_token;
      } else {
        $url .= "?access_token=" . $this->access_token;
      }

//echo "URL : " . $url . "<br/>\n";

    $fields = json_encode($datas['fields']);
//echo "Fields: " . $fields . "\n";
//echo "Methode used: " . $datas['method'] . "\n";

    curl_setopt($this->ch, CURLOPT_URL, $url); 
    if ($datas['method'] == 'POST' || $datas['method'] == 'PUT') {
      curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $datas['method']);   
      curl_setopt($this->ch, CURLOPT_POSTFIELDS, $fields);
      curl_setopt($this->ch, CURLOPT_HTTPHEADER, array(                                                                          
        'Content-Type: application/json',                                                                                
        'Content-Length: ' . strlen($fields))                                                                       
      );
    } elseif ($datas['method'] == 'GET') {
      curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $datas['method']);   
      //curl_setopt($this->ch, CURLOPT_POSTFIELDS, $fields);
      curl_setopt($this->ch, CURLOPT_HTTPHEADER, array(                                                                          
        'Content-Type: application/json')
      );
    }
    $output = curl_exec($this->ch); 
    $this->last_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
    return $output;
  }
}

