#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
include('db.php');

function doLogin($username, $password) {
	global $db;
    // lookup username and password in database
	$q = mysqli_query($db, "SELECT * FROM students WHERE BINARY username = '$username' AND BINARY password = '$password'");

	// if there's an error, throw it in console	
	if (!$q) {printf("ERROR: %s\n", mysqli_error($db));}

	$c = mysqli_num_rows($q);
	if ($c == 1) {
		// there's a match, return true
		echo "Awesome! The username exists!\n\n";
		return true;	
	}
	
	else {
		// if no match found, return false
		echo "Welp, couldn't find the username specified...\n\n";
		return false;
	}
 	errorCheck($db);
}

function doRegister($username, $password) {
	global $db;
	
	// check to see if the username exists first
	$q = mysqli_query($db, "SELECT * FROM students WHERE BINARY username = '$username'");
	$c = mysqli_num_rows($q);
	if ($c == 1) {
		// if there's a match, send a "username exists" message
		echo "Username exists!";
		return "The username specified already exists! Try again.";
	}
	else {
		// no match, so insert the values into the DB
		$q = mysqli_query($db, "INSERT INTO students (username, password, start_bal, bal) VALUES ('$username', '$password', 10000.00, 10000.00)");
		echo "Inserted values successfully!\n\n";
		//Next, create stock table for user
		$q = mysqli_query($db, "CREATE TABLE ".$username."_stocks (symbol varchar(15), amt int);");
		return "Registration successful!";
	}
	errorCheck($db);
}

function doSearch($search) {
	$ch = curl_init("https://www.alphavantage.co/query?function=SYMBOL_SEARCH&keywords=" . $search . "&apikey=9J4N8FA67HVHYZG0");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$curl_results = curl_exec($ch);
	curl_close($ch);

$jsonarray = json_decode($curl_results, true); 
foreach($jsonarray['bestMatches'] as $variable) {
	
	$symbol = $variable['1. symbol'];
	$name = $variable['2. name'];
	$output .= "<td>".$symbol." </td>";
	$output .= "<td>".$name." </td>";
 	$output .= "</tr>";
	}
return $output;
}


function requestProcessor($request) {
	echo "Received a request".PHP_EOL;
	var_dump($request);
	if(!isset($request['type'])) {
		return "ERROR: Unsupported message type!";
	}
	
	switch ($request['type']) {
	case "login":
		return doLogin($request['username'], $request['password']);

	case "register":
		return doRegister($request['username'], $request['password']);

	case "validate_session":
		return doValidate($request['sessionId']);

	case "search":
		return doSearch($request['search']);
	}
	return array("returnCode" => '0', 'message'=>"Server received request and processed.");
}


function errorCheck($db) {
	if ($db->errno != 0) {
		echo "Failed to execute query:".PHP_EOL;
		echo __FILE__.':'.__LINE__.":error: ".$db->error.PHP_EOL;
		exit(0);
	}
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

$server->process_requests('requestProcessor');
exit();

?>