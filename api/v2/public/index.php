<?php


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require "../src/config/db.php";

$app = new \Slim\App;
$app->add(function (Request $request, Response $response, $next)  {
  $auth = $request->getHeader("Authorization")[0];
  if (strpos($auth, '|') !== false) {
    $auth = explode("|",$auth);
  }
  $db = new Db();

  if(is_array($auth)){
      $devices = $db->query("SELECT * FROM devices WHERE device_id = :deviceId and mac = :mac" ,array(":mac"=>$auth[0],":deviceId"=>$auth[1]) ,2);
  }else{
      $company = $db->query("SELECT id FROM companies WHERE token = :token " ,array(":token"=>$auth) ,1);
      if(isset($company->id)){
        $devices = $db->query("SELECT * FROM devices WHERE company_id = :companyId " ,array(":companyId"=>$company->id) ,2);
      }
    }

   if( isset($devices[0]->id)){
     $devices = formatDevice($devices);
     $newRequest = $request->withAttribute('devices', $devices);
     $response = $next($newRequest, $response ) ;
   }else{
     $response = $response->withStatus(403);
   }

	return $response;

});

function formatDevice($devices){
  $return = array();
  foreach ($devices as $key => $device) {
    $returndevice['device_id'] = $device->device_id;
    $returndevice['company_id'] = $device->company_id;
    $returndevice['name'] = $device->name;
    $returndevice['last_at'] = $device->last_at;
    $tags = json_decode($device->tags, true);
    $last_data = json_decode($device->last_data, true);
    $returndevice['tags'] = array();
    foreach ($tags as $key2 => $tag) {
      $data['name'] = $tag;
      $data['value'] = $last_data[$key2];
      $returndevice['tags'][$key2] =  (object)$data;
    }
    $return[$key] = (object)$returndevice;
  }
  return $return ;
}

// Devices Routes...
require "../src/routes/devices.php";

$app->run();
