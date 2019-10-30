<?php

$db = new mysqli('192.168.2.102','testuser','12345','testdb');

if ($db->errno != 0)
{
	echo "Failed to connect to the database. ERROR: ". $db->error . PHP_EOL;
	exit(0);
}

echo "Successfully connected to the database!".PHP_EOL;

?>
