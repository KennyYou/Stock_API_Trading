#!/usr/bin/php
<?php
require __DIR__ . '/vendor/autoload.php';
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
include('db.php');

//INITIATE API CLIENT
use AlphaVantage\Client;
$alpha_vantage = new Client('9J4N8FA67HVHYZG0');

//FUNCTION LIST
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

function doRequestBalance($username) {
	global $db;
	//save query as variable
	$requestBalance = mysqli_query($db, "SELECT bal FROM students WHERE username = '$username'");
	//save fetched data from query as array
	$fetchBalance = mysqli_fetch_array($requestBalance);
	echo "Balance sent to client.";
	return $fetchBalance;
	errorCheck($db);
}

function doBuyStock($username, $symbol, $amount) {
	global $db;
	//Fetch current stock info from API
	$stockinfo = $alpha_vantage
    		->stock()
    		->intraday($symbol, AlphaVantage\Resources\Stock::INTERVAL_1MIN);
	$fetchedsymbol = $stockinfo["Meta Data"]["2. Symbol"];
	//Check if symbol exists
	if ($symbol != $fetchedsymbol)
		return "ERROR! Invalid symbol detected!";
	//Get stock LOW as buy price (NBBO)
	$stocktime = $stockinfo["Meta Data"]["3. Last Refreshed"];
	$stockpricestring = $stockinfo["Time Series (1min)"][$stocktime]["3. low"];
	//convert price and amount to float, multiply stock by amount, then round.
	$stockprice = floatval($stockpricestring);
	$i_amount = intval($amount);
	$t = $stockprice * $i_amount;
	$total = round($t,2);
	//Fetch user data from students
	$s = "select * from students where BINARY username = '$username'";
	($table = mysqli_query( $db,  $s ) )  or die( mysqli_error($db) );
	while ( $r = mysqli_fetch_array($table,MYSQLI_ASSOC) ) 
	{
		$bal= $r["bal"];
		if ($bal - $total < 0)
		{
			return "ERROR! Insufficent funds for stock purchase!";
		}
	};
	$s = "update students set bal = bal - '$total' where BINARY username = '$username' ";
	mysqli_query ($db, $s);
	$s = "insert into trading (username, type, symbol, shares, date, cost) values ('$username', 'buying', '$symbol', '$i_amount', NOW(), $total)";
	mysqli_query ($db, $s);
	$s = "insert into ".$username."_stocks (symbol, amt) values ('$symbol',)";
	mysqli_query ($db, $s);

	return "Your transaction was successful! You have bought ".$i_amount." shares of ".$symbol." stock for $".$total."!";
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
	case "login":
		return doLogin($request['username'], $request['password']);

	case "register":
		return doRegister($request['username'], $request['password']);

	case "validate_session":
		return doValidate($request['sessionId']);

	case "search":
		return doSearch($request['search']);

	case "requestBalance":
		return doRequestBalance($request['username']);

	case "buyStock":
		return doBuyStock($request['username'], $request['symbol'], $request['amount']);
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
