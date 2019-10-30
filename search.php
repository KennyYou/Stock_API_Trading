<?php

//Check for any errors
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', 1);
//
session_start();
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
if (isset($argv[1]))
{
  $msg = $argv[1];
}
else
{
  $msg = "search";
}

//Send search request over
$request['search'] = $_POST['search'];
$request['message'] = $msg;


$response = $client->send_request($request);
//PHP_EOL should echo in from backend
echo "".PHP_EOL;
print_r($response);

	echo"\n";
	echo"Testing output if this is seen but not resonse something went wrong";
//
?>