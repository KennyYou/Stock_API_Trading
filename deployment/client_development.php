#!/usr/bin/php
<?php 
require_once('../include/path.inc');
require_once('../include/get_host_info.inc');
require_once('../include/rabbitMQLib.inc');

function getPkg($request){
	echo "REQUEST TAKEN".PHP_EOL;
	var_dump($request);
	if(!isset($request['type'])){
		return "ERROR: NOT SET";
	}
	$connect = mysqli_connect("localhost", "root", "1234", "deployServer");
	if(!$connect){
		die("ERROR: NO CONNECTION." . mysqli_connect_error());
		
	}
	else{
		echo "WE IN\n";
	}
	$server = new rabbitMQServer("_____.ini", "testserver");
	var_dump($server);
	$server->process_requests('requestProcessor');
	exit
}
?>