<?php
require_once('include/path.inc');
require_once('include/get_host_info.inc');
require_once('include/rabbitMQLib.inc');
require_once('include/logger.inc');
$client = new rabbitMQClient("deploy.ini", "testserver");
$request["type"] = "createPkg";
$request["pkgName"] = "testPkg";
$request["desc"] = "testDesc";
$request["path"] = "/path";
$request["hostname"] = "jdm68-it490vm";
var_dump($request);
$response = $client->send_request($request);
echo $response;
?>