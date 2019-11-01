#!/usr/bin/php
<?php
require_once __DIR__ . '/vendor/autoload.php';
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

function doDetailSearch($search) {
	$ch = curl_init("https://www.alphavantage.co/query?function=SYMBOL_SEARCH&keywords=" . $search . "&apikey=9J4N8FA67HVHYZG0");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$curl_results = curl_exec($ch);
	curl_close($ch);

$jsonarray = json_decode($curl_results, true); 
	
$symbol = $jsonarray['bestMatches'][0]['1. symbol'];
$name = $jsonarray['bestMatches'][0]['2. name'];
//Curl for each symbol and get open, high, low, and close.
$ch2 = curl_init("https://www.alphavantage.co/query?function=TIME_SERIES_INTRADAY&symbol=".$symbol."&interval=1min&apikey=9J4N8FA67HVHYZG0");
curl_setopt($ch2, CURLOPT_HEADER, 0);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, TRUE);
$curl_results2 = curl_exec($ch2);
curl_close($ch2);
$jsonarray2 = json_decode($curl_results2, true);
$time = $jsonarray2['Meta Data']['3. Last Refreshed'];
$open = $jsonarray2['Time Series (1min)'][$time]['1. open'];
$high = $jsonarray2['Time Series (1min)'][$time]['2. high'];
$low = $jsonarray2['Time Series (1min)'][$time]['3. low'];
$close = $jsonarray2['Time Series (1min)'][$time]['4. close'];
$volume = $jsonarray2['Time Series (1min)'][$time]['5. volume'];
$output .= "<td>".$symbol." </td>";
$output .= "<td>".$name." </td>";
$output .= "<td>".$open." </td>";
$output .= "<td>".$high." </td>";
$output .= "<td>".$low." </td>";
$output .= "<td>".$close." </td>";
$output .= "<td>".$volume." </td>";
$output .= "</tr>";
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

function doShowOwnedStock($username) {
	global $db;
	$ShowOwnedStock = mysqli_query($db, "SELECT symbol FROM ".$username."_stocks");
	}

function doBuyStock($username, $symbol, $amount) {
	global $db;
	//Fetch current stock info from API
	$alpha_vantage = new Client('9J4N8FA67HVHYZG0');
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
			echo "Nope! Not enough cash.\n\n";
			return "ERROR! Insufficent funds for stock purchase!";
		}
	};
	$s = "update students set bal = bal - '$total' where BINARY username = '$username' ";
	mysqli_query ($db, $s);
	$s = "insert into trading (username, type, symbol, shares, date, cost) values ('$username', 'buying', '$symbol', '$i_amount', NOW(), $total)";
	mysqli_query ($db, $s);
	//If new stock, insert into table. Else, update existing data.
	$s = "select * from ".$username."_stocks where symbol = '$symbol'";
	$testq = mysqli_query($db, $s);
	$testarray = mysqli_fetch_array($testq);
	if (is_null($testarray))
	{
		$s = "insert into ".$username."_stocks (symbol, amt) values ('$symbol','$i_amount')";
	mysqli_query ($db, $s);
	}
	else
	{
	$s = "update ".$username."_stocks set amt = amt + '$i_amount' where symbol = '$symbol' ";
	mysqli_query ($db, $s);
	}
	echo "Successful buy!\n\n";
	return "Your transaction was successful! You have bought ".$i_amount." shares of ".$symbol." stock for $".$total."!";
	errorCheck($db);
}

function doSellStock($username, $symbol, $amount) {
	global $db;
	//Fetch stock amounts from [username]_stocks
	$s = "select * from ".$username."_stocks where symbol = '$symbol'";
	$testq = mysqli_query($db, $s);
	$testarray = mysqli_fetch_array($testq);
	if (is_null($testarray))
	{
		echo "User doesn't have that stock!\n\n";
		return "ERROR! You haven't bought that stock!";
	}
	if ($testarray['amt'] - (intval($amount)) < 0)
	{
		echo "User doesn't have enough stock!\n\n";
		return "ERROR! You don't have enough of that stock!";
	}
	//Fetch current stock info from API
	$alpha_vantage = new Client('9J4N8FA67HVHYZG0');
	$stockinfo = $alpha_vantage
    	->stock()
    	->intraday($symbol, AlphaVantage\Resources\Stock::INTERVAL_1MIN);
	//Get stock HIGH as sell price (NBBO)
	$stocktime = $stockinfo["Meta Data"]["3. Last Refreshed"];
	$stockpricestring = $stockinfo["Time Series (1min)"][$stocktime]["2. high"];
	//convert price and amount to float, multiply stock by amount, then round.
	$stockprice = floatval($stockpricestring);
	$i_amount = intval($amount);
	$t = $stockprice * $i_amount;
	$total = round($t,2);
	$s = "update students set bal = bal + '$total' where BINARY username = '$username' ";
	mysqli_query ($db, $s);
	$s = "insert into trading (username, type, symbol, shares, date, cost) values ('$username', 'selling', '$symbol', '$i_amount', NOW(), $total)";
	mysqli_query ($db, $s);
	$s = "update ".$username."_stocks set amt = amt - '$i_amount' where symbol = '$symbol' ";
	mysqli_query ($db, $s);
	echo "Successful sell!\n\n";
	return "Your transaction was successful! You have sold ".$i_amount." shares of ".$symbol." stock for $".$total."!";
	errorCheck($db);
}
	

//CODE "STARTS" HERE (server recieves requests then chooses function)
function requestProcessor($request) {
	$alpha_vantage = new Client('9J4N8FA67HVHYZG0');
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

	case "search1S":
		return doSearch($request['search1S']);

	case "search1N":
		return doDetailSearch($request['search1N']);

	case "requestBalance":
		return doRequestBalance($request['username']);

	case "showOwnedStock":
		return doShowOwnedStock($request['username']);

	case "buyStock":
		return doBuyStock($request['username'], $request['symbol'], $request['amount']);
	
	case "sellStock":
		return doSellStock($request['username'], $request['symbol'], $request['amount']);
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
