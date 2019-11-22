#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function logging($log)
{
	//Ignore this testing out more detailed logs.
	/*
	$logFile = $log . PHP_EOL . date("h:i:sa");
	file_put_contents("/home/tmp/logs.txt",$logFile, FILE_APPEND); 
	*/
	echo "Logging Error: " . PHP_EOL . " | INP: " . $log; 
	//users have to make this file + put file into location @ logs.txt
	file_put_contents("/home/tmp/logs.txt",$log . PHP_EOL,FILE_USE_INCLUDE_PATH | FILE_APPEND);
}

$server = new rabbitMQServer("rabbitMQErrorLog.ini","testServer");
//Running to check connection is active
echo "You are connected to the log server successfully!<br>";

$server->process_requests('logging');
exit();
?>
