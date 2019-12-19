#!/usr/bin/php
<?php 
require_once("../path.inc");
require_once("../rabbitMQLib.inc");
require_once("../get_host_info.inc");
echo "Enter info for deployment";
echo "\n";
$pkg = read_stdin("ENTER PACKAGE: ");
$tier = read_stdin("What tier is that package going: ");
$Namepkg = read_stdin("ENTER NAME FOR PACKAGE: ");
$ver = read_stdin("WHAT VERSION IS THIS?: " );

$client = new rabbitMQClient("deploy.ini", "testServer");
$request = array();
$request['type'] = 'deployment';
$request['package'] = $pkg;
$request['tier'] = $tier;
$request['version'] = $ver;
$response = $client -> send_request($request);
echo "\n";
?>