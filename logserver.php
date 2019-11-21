#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function logging($log)
{
 	echo "Logging Error: ".PHP_EOL; 
	echo $log;
	//put file into location @ logs.txt
	//users have to make this file
	file_put_contents("/home/tmp/logs.txt",$log.PHP_EOL,FILE_USE_INCLUDE_PATH | FILE_APPEND);
}

$server = new rabbitMQServer("rabbitMQErrorLog.ini","testServer");
//Running to check connection is active
echo "You are connected to the log server successfully!<br>";

$server->process_requests('logging');
exit();
?>
