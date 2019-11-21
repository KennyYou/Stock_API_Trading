#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
function logger($ermsg){
	//Connect to rabbitMQ
	$client = new rabbitMQClient("rabbitMQErrorLog.ini","testServer");
	
	$return = date('Y-m-d H:i:s', time())." ".gethostname()." ".$ermsg.PHP_EOL;
	//Have the logs files set up on every machine
	file_put_contents("/home/tmp/logs.txt",$return,FILE_USE_INCLUDE_PATH | FILE_APPEND);

	$response = $client->publish($return);
}
?>
