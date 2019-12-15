#!/usr/bin/php

<?php
require_once("../path.inc");
require_once("../rabbitMQLib.inc");
require_once("../get_host_info.inc");
include("deployDB.php");

// FUNCTION LIST
function doDeployPkg($version, $user, $ip, $namePkg, $description, $pkgPath) {
	
	global $db;
	// send package over Secure Copy (SCP) using target machine's user password
	$output = shell_exec("sshpass -f '/home/jdm68/pwDir/jdm68' | sudo tar czf /home/jdm68/packages/$pkgName-$version.tar.gz $pkgPath;
		sshpass -f '/home/jdm68/pwDir/$user' | scp -P 22 /home/jdm68/packages/$namePkg-$version.tar.gz $user@$ip:~/$namePkg-$version.tar.gz");
	
	// insert package information in database deployment table
	$connect = mysqli_connect('localhost', 'deploy', '490password', 'deployment');
	if (!$connect) {echo ("MySQL ERROR: " . mysqli_connect_error());}
	else {echo "SUCCESS: connection established to DB\n";}

	if ($query = mysqli_prepare($connect, "INSERT INTO packages (pkgName, description, version, validated, pkgPath, uploadDateTime) VALUES (?,?,?,?,?,?)")) {
		$query ->bind_param('ssssss', $namePkg, $description, $version, $validated, $pkgPath, NOW());
		$query ->execute();
		if ($query->affected_rows < 0) {
			echo "ERROR: " . $query->error;
			$query->close();
		}
		else {echo "Data successfully inserted into deployment database! Rows changed: " . $query -> affected_rows . PHP_EOL;}
		
	errorCheck($db);
}

function doGetPkg($request) {

	global $db;
	echo "REQUEST TAKEN".PHP_EOL;
	var_dump($request);
	if(!isset($request['type'])) {return "ERROR: Message type not set!";}
	$connect = mysqli_connect("localhost", "deploy", "490password", "deployment");

	if(!$connect) {die("ERROR: No connection." . mysqli_connect_error());}
	else {echo "WE IN\n";}
	var_dump($server);
	errorCheck($db);
}

function doRollback($pkgName, $version) {

	global $db;
	// set passed pkg name and version combo as bad and install package with the version of v - 0.1
	$connect = mysqli_connect('localhost', 'deploy', '490password', 'deployment');
	if (!$connect) {echo ("mySQL ERROR: " . mysqli_connect_error());}
	else {echo "SUCCESS: Connection Established to DB\n";}

	// mark passed package version as bad
	if ($query = mysqli_prepare($connect, "UPDATE packages SET validated = 'F' WHERE pkgName = $pkgName AND version = $version")) {
		$query ->execute();
		if ($query->affected_rows < 0) {
			echo "ERROR: " . $query->error;
			$query->close();
		}
		else {echo "$pkgName-$version successfully marked as bad. Rolling back to previous version... (rows affected: ". $query -> affected_rows . ")" .PHP_EOL;}
	}

	// get most recent validated package and push it
	if ($query = mysqli_prepare($connect, "SELECT version FROM packages WHERE pkgName = $namePkg AND validated = 'T' ORDER BY DATE DESC")) {
		$query ->execute();
		echo "" .PHP_EOL;
	}
	errorCheck($db);
}

function doValidatePkg($pkgName, $version) {

	global $db;
	$connect = mysqli_connect('localhost', 'deploy', '490password', 'deployment');
	if (!$connect) {echo ("MySQL ERROR: " . mysqli_connect_error());}
	else {echo "SUCCESS: Connection Established to DB\n";}
	if ($query = mysqli_prepare($connect, "SELECT pkgName, version FROM packages")) {
		$query ->execute();
		while ($r = mysqli_fetch_array ($query, MYSQLI_ASSOC)) {
			$dbPkgName = $r["pkgName"];
			$dbPkgVersion = $r["version"];
			
			// change validation enum to true if found
			if ($pkgName == $dbPkgName && $version == $dbPkgVersion) {
				echo "Name and version combo found! ($pkgName-$version)" . PHP_EOL;
				if ($query = mysqli_prepare($connect, "UPDATE packages SET validated = 'T' WHERE pkgName = $namePkg AND version = $version")) {
					$query ->execute();
					return "Package has been validated successfully!" . PHP_EOL;
				}
			}
		}
		// let user know either package/version combo is incorrect if not found
		echo "Package name and version combination ($pkgName-$version) is incorrect or doesn't exist." . PHP_EOL;
	}
	errorCheck($db);
}

// CODE "STARTS" HERE (server recieves requests then chooses function)
function requestProcessor($request) {
	echo "Received a request".PHP_EOL;
	var_dump($request);
	if(!isset($request['type'])) {
		return "ERROR: Unsupported message type!";
	}
	
	switch ($request['type']) {
	case "deployPkg":
		return doDeployPkg($request['version'], $request['user'], $request['ip'], $request['namepkg'], $request['description'], $request['path']);
/*	case 'getPkg':
		return doGetPkg($request['request']);
	case 'rollbackPkg':
		return doRollback($request['namepkg'], $request['version']);
}*/
	return array("returnCode" => '0', 'message'=>"Server received request and processed.");
	}
}

function errorCheck($db) {
	if ($db->errno != 0) {
		echo "Failed to execute query: " . PHP_EOL;
		echo __FILE__.':'.__LINE__.":error: " . $db -> error.PHP_EOL;
		exit(0);
	}
}

$server = new rabbitMQServer("deploy.ini", "testServer");
$server->process_requests('requestProcessor');
exit(0);
?>
