<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
function logger($error){

	$client = new rabbitMQClient("rabbitMQErrorLog.ini","testServer");
	
	$host_stamp = date('Y-m-d H:i:s', time())." ".gethostname()." ".$error.PHP_EOL;
	//Have the logs files set up on every machine
	file_put_contents("/home/tmp/logs.txt",$host_stamp,FILE_USE_INCLUDE_PATH | FILE_APPEND);

	$response = $client->publish($host_stamp);
}
?>


