#!/usr/bin/php
<?php

$db = new mysqli('localhost', 'deploy', '490password', 'deployment');

if ($db->errno != 0)
{
	echo "Failed to connect to the database. ERROR: ". $db->error . PHP_EOL;
	exit(0);
}

echo "Successfully connected to the deployment database!".PHP_EOL;

?>
