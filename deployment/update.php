<?php 
require_once("../path.inc");
require_once("../rabbitMQLib.inc");
require_once("../get_host_info.inc");

$client = new rabbitMQClient("deploy.ini","testServer");

$request = array();
$request['type'] = "update";
$request['package'] = "BACKEND";
$request['tier'] = "QA";
$request['packagename'] = "";

$response = client->send_request($request);

echo "\n";
?>