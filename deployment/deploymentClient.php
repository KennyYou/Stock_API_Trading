#!/usr/bin/php
<?php
require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');

$client = new rabbitMQClient("deploy.ini","testServer");

if (isset($argv[1])) {$msg = $argv[1];}
else {$msg = "test message";}

$request = array();
$request['type'] = "deployPkg";	// message to send to deployment server
$request['user'] = "kenny"; // username
$request['ip'] = "192.168.2.106";	// target IP address
$request['namepkg'] = "tester";
$request['description'] = "A test package.";
$request['version'] = '0.1';
$request['path'] = "/home/jdm68/packages";
$request['tierType'] = '0';
$response = $client->send_request($request);
//$response = $client->publish($request);
echo "client received response: ".PHP_EOL;
print_r($response);
echo "\n\n";
echo $argv[0]." END".PHP_EOL;
?>
