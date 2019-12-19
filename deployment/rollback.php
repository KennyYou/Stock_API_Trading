#!/usr/bin/php
<?php 
require_once("../path.inc");
require_once("../rabbitMQLib.inc");
require_once("../get_host_info.inc");

$client = new rabbitMQClient("deploy.ini","testServer");
$request = array();
$request = ['type'] = "rollback";
$request =['package'] = "Back End";
$request['tier'] = "QA";
$request['packageName'] = "BEpkg-v";
$response = $client->send_request($request); 

echo "\n";
?>