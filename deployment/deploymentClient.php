#!/usr/bin/php
<?php
require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');

$client = new rabbitMQClient("deploy.ini","testServer");
if (isset($argv[1]))
{
  $msg = $argv[1];
}
else
{
  $msg = "test message";
}

$request = array();
$request['type'] = "sendPkgInfo";
$request['version'] = "0.1";
$request['user'] = "jdm68";
$request['ip'] = "192.168.2.110";	// <-- changed to 'localhost' for testing purposes
$request['namepkg'] = "my_files";
$request['description'] = "apple";
$request['path'] = "/home/jdm68/";
$response = $client->send_request($request);
//$response = $client->publish($request);

echo "client received response: ".PHP_EOL;
print_r($response);
echo "\n\n";

echo $argv[0]." END".PHP_EOL;

/*
require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');

function getPkg($request) {
	echo "REQUEST TAKEN".PHP_EOL;
	var_dump($request);
	if(!isset($request['type'])) {
		return "ERROR: NOT SET";
	}
	$connect = mysqli_connect("localhost", "deploy", "490password", "deployment");
	if(!$connect) {
		die("ERROR: NO CONNECTION." . mysqli_connect_error());
	}
	else {
		echo "WE IN\n";
	}
	$server = new rabbitMQServer("deploy.ini", "deployHost");
	var_dump($server);
	$server->process_requests('requestProcessor');
	exit;
}
*/
?>
