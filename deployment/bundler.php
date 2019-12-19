#!/usr/bin/php
<?php 
require_once("../path.inc");
require_once("../rabbitMQLib.inc");
require_once("../get_host_info.inc");

exec('./packaging.sh');

$db = new mysqli();

if ($mydb->errno !=0){
	echo "Connection to Database FAILED: ".$db->error.PHP_EOL;
	exit(0);
}

$type = read_stdin("Enter What Type: ");
$package = read_stdin ("Enter Package: ");
$tier = 	read_stdin("Where is the package going?: ");
$name = read_stdin("Package Name: ");
#rollback
if ($type == 'rollback' || $type == 'Rollback'){
	$RBVer = read_stdin("What version to Rollback to: ");
	
}
$version_value = "1";

$query = mysqli_query($db , "SELECT * FROM packages WHERE pkgName = '$name'");
$count = mysqli_num_rows($query); 

#package
if($type == "package"){
$dex_check = mysqli_query($db, "SELECT * FROM packages WHERE pkgName = '$name' ORDER BY (pkgVer+0) DESC LIMIT 1");
$row = mysqli_fetch_assoc($check);
$ver = $row['pkgVer'];
echo "Birthing new File...Creating next clone(version) #" . ($version+ $increment_value);
}
else{
	echo "Could not find lost child....replacing child (creating new filename)"; 
	$version = "0";
}

#deploy
if(type == 'deploy'){
	$dex_check = mysqli_query($db, "SELECT * FROM packages WHERE pkgName = '$name' ORDER BY (pkgVer+0) DESC LIMIT 1");
	$row = mysqli_fetch_assoc($check);
	$ver = $row['pkgVer'];
	echo "FIRING " . $pkgName . "-" . $version . "NOW";
}

$client = new rabbitMQLib("deploy.ini" , "testServer");
request = array();
$request['type'] = $type;
$request['package'] = $package;
$request['tier'] = $tier;
$request['pkgName'] = $name;
switch($type){
	case "bundle":
		$request['version'] = $ver + $version_value;
	case "deploy":
		$request['version'] = $ver;
	case "rollback":
		$request['RBVer'] = $RBVer;
}
$response = $client->send_request($request);

echo "\n";

if ($type == 'bundle'){
	rename("/home/jdm68/packages/ ","/home/jdm68/packages/".$request['namePkg']."-"$request['ver'].".tgz");
	
	exec('./packaging.sh');
}
?>
