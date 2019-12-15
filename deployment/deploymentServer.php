#!/usr/bin/php
<?php 

require_once('../path.inc');
require_once('../rabbitMQLib.inc');
require_once('../get_host_info.inc');
include('deployDB.php');

// FUNCTION LIST
function doSend($version, $user, $ip, $namepkg, $description, $path) {
	
	global $db;
	$output = shell_exec("sshpass -f '/home/jdm68/git/The_Project/sshpass' scp -P 22 ~/git/The_Project/packages/my_files.tar.gz jdm68@localhost:/home/jdm68/ ");
	$connect = mysqli_connect('localhost', 'deploy', '490password', 'deployment');

	if (!$connect) {
		echo ("mySQL ERROR: " . mysqli_connect_error());	
	}
	else {
		echo "SUCCESS: Connection Established to DB\n";		
	}
/*
	$request["type"] = "pkg";
	$request["pkg_name"]= $namepkg;
	$request["path"] = $path; 
	$request["description"] = $description;
	
	$version = substr($namepkg, -3);
	$version_string[1] = ",";
	$ver_ID = floatval($version);
*/
	$pkgName = "my_files";
	$desc = "apple";
	$version = "0.1";
	$validated = 'T';
	$pkgPath = '/home/jdm68/';
	
	if ($query = mysqli_prepare($connect, "INSERT INTO packages (pkgName, description, version, validated, pkgPath) VALUES (?,?,?,?,?)")) {
		$query ->bind_param('sssss', $pkgName, $desc, $version, $validated, $pkgPath);
		$query ->execute(); 
		echo "INSERTED INTO dbname database " .PHP_EOL;
		echo "ROWS CHANGED: " . $query -> affected_rows . PHP_EOL;
	}
	
	errorCheck($db);
}


function doGetPkg($request) {
	
	global $db;
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

	var_dump($server);
	errorCheck($db);
}
//CODE "STARTS" HERE (server recieves requests then chooses function)
function requestProcessor($request) {
	echo "Received a request".PHP_EOL;
	var_dump($request);
	if(!isset($request['type'])) {
		return "ERROR: Unsupported message type!";
	}
	
	switch ($request['type']) {
	case "sendPkgInfo":
		return doSend($request['version'], $request['user'], $request['ip'], $request['namepkg'], $request['description'], $request['path']);
	
/*	case 'getPkg':
		return doGetPkg($request['request']);
	}
*/
	return array("returnCode" => '0', 'message'=>"Server received request and processed.");
	}
}


function errorCheck($db) {
	if ($db->errno != 0) {
		echo "Failed to execute query:".PHP_EOL;
		echo __FILE__.':'.__LINE__.":error: ".$db->error.PHP_EOL;
		exit(0);
	}
}

$server = new rabbitMQServer("deploy.ini", "testServer");

$server->process_requests('requestProcessor');
exit(0);
?>
