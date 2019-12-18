#!/usr/bin/php

<?php

require_once("/home/jdm68/git/The_Project/path.inc");

require_once("/home/jdm68/git/The_Project/rabbitMQLib.inc");

require_once("/home/jdm68/git/The_Project/get_host_info.inc");





echo "Enter information to the deployment server:\n";

$type = readline("Enter your desired action (bundle, deploy, rollback): ");

$pkgMachineType = readline("Enter your origin machine type (FE, BE, DMZ): ");

$destTier = readline("Enter the destination tier (dev, QA, production): ");

$pkgName = readline("Enter the package's name: ");

if ($type == "rollback" || $type == "roll") {

  $version = readline("Enter the version number of the problematic package: ");

  $rbVersion = readline("Enter the version you would like to revert to: ");

}

else {

  $version = readline("Enter the package's version: ");

  $rbVersion = 0; // just in case we have to send a value for the other two options

}



$client = new rabbitMQClient("/home/jdm68/git/The_Project/deployment/deploy.ini", "testServer");

$request = array();

$request['type'] = $type;

$request['pkgMachineType'] = $pkgMachineType;

$request['destTier'] = $destTier;

$request['pkgName'] = $pkgName;

$request['version'] = $version;

$request['rbVersion'] = $rbVersion;

$request['userName'] = get_current_user(); // get the local user's userName

$request['ipAddress'] = 0; // get the local user's IP address

$response = $client -> send_request($request);



?>

