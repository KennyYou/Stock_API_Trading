<?php
//Check for any errors (uneeded for now)
/*
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', 1);
*/
//Grab required files 
session_start();
//THIS FILE MAY NOT BE NEEDED AFTER DELETE IF NOT IN USE
require_once('logfunction.inc');
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
//Add new connection for logging to rabbitMQ
$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
if (isset($argv[1]))
{
  $msg = $argv[1];
}
else
{
  $msg = "login";
}
//Get Username + Password
$request = array();
$request['type'] = "login";
$request['username'] = $_POST['username'];
$request['password'] = $_POST['pass'];
$request['message'] = $msg;
//May not need double check before merging
for ($x = 0; $x <= 2; $x++) {
echo "REQUEST ERROR!" . PHP_EOL;
logger(__FILE__ . " | " . "Error! CHECK CONNECTION");
}
$response = $client->send_request($request);
echo "Request Failure".PHP_EOL;
print_r($response);
//See if there is any response msg as of now we don't have.
echo "\n\n";
//If not in the database
if ($response == 0) {
	for ($x = 0; $x <= 2; $x++) {
  	echo "REQUEST ERROR!" . PHP_EOL;
	logger( __FILE__ . " | " . "Error! Bad Request Type: Check connection with database");
	} 
	header("location:loginerror.html");
	//Sends error log out 3 times
	}
//Testing out response types either this or | Else will work
if ($response == 1) {
	$_SESSION['logged'] = true;
	$_SESSION["username"] = $request['username'];
	//$name = $_SESSION['username'];
	header("Location: loginsuccess.php");
}
//Testing this out
if (!$response) {
	//set for 2 
	for ($x = 0; $x <= 2; $x++) {
    	echo "REQUEST ERROR!" . PHP_EOL;
	logger( __FILE__ . " | " . "Error! Check Connection: Connection Timed out");
	} 
	or die("Login Error, Check connection and try again");
	header("location:loginerror.html");
}
?>
