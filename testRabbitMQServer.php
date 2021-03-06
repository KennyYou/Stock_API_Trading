#!/usr/bin/php
<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
include('db.php');
include('logfunction.inc');

//FUNCTION LIST
function doLogin($username, $password) {
	global $db;
	//Sanatize username and password
	$cleanusername = mysqli_real_escape_string($db, $username);
	$cleanpassword = mysqli_real_escape_string($db, $password);
	//get hash from inserted password
	$salty = "taterswithsalt";
	$passhash = hash('sha256',$salty.$cleanpassword);
	
    // lookup username and password hash in database
	$q = mysqli_query($db, "SELECT * FROM students WHERE BINARY username = '$cleanusername' AND BINARY password = '$passhash'");

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
		return 0;
	}
 	errorCheck($db);
}

function doRegister($username, $password, $email) {
	global $db;
	//sanitize username password and email
	$cleanusername = mysqli_real_escape_string($db, $username);
	$cleanpassword = mysqli_real_escape_string($db, $password);
	$cleanemail = mysqli_real_escape_string($db, $email);
	
	// check to see if the username exists first
	$q = mysqli_query($db, "SELECT * FROM students WHERE BINARY username = '$cleanusername'");
	$c = mysqli_num_rows($q);
	if ($c == 1) {
		// if there's a match, send a "username exists" message
		echo "Username exists!";
		return "The username specified already exists! Try again.";
	}
	else {
		//Create password hash with salt
		$salty = "taterswithsalt";
		$passhash = hash('sha256',$salty.$cleanpassword);
		// no match, so insert the values into the DB
		$q = mysqli_query($db, "INSERT INTO students (username, password, email, start_bal, bal, p_percent) VALUES ('$cleanusername', '$passhash', '$cleanemail', 10000.00, 10000.00, 10)");
		echo "Inserted values successfully!\n\n";
		//Next, create stock table for user
		$q = mysqli_query($db, "CREATE TABLE ".$cleanusername."_stocks (symbol varchar(15), amt int, old_price decimal(12,2), cur_price decimal(12,2));");
		return "Registration successful!";
	}
	errorCheck($db);
}

function doRSSFeed(){

	$client = new rabbitMQClient("DMZRabbitMQ.ini","testServer");
	if (isset($argv[1]))
	{
	$msg = $argv[1];
	}
	else
	{
	$msg = "RSS";
	}
	$request = array();
	$request['type'] = "RSS";
	//Get response and save as variable
	$response = $client->send_request($request);
	//PHP_EOL should echo in from backend 
	//May induce unintended effects
	echo "".PHP_EOL;
	echo "recieved RSS info.";
	echo"\n";

	//return API data
	return $response;
}

function doSearch($search) {

	$client = new rabbitMQClient("DMZRabbitMQ.ini","testServer");
	if (isset($argv[1]))
	{
	$msg = $argv[1];
	}
	else
	{
	$msg = "search2S";
	}
	$request = array();
	$request['type'] = "search2S";
	//send search to DMZ end
	$request['search2S'] = $search;
	//Get response and save as variable
	$response = $client->send_request($request);
	//PHP_EOL should echo in from backend 
	//May induce unintended effects
	echo "".PHP_EOL;
	echo "Sent search info.";
	echo"\n";

	//return API data
	return $response;
}

function doDetailSearch($search) {

	$client = new rabbitMQClient("DMZRabbitMQ.ini","testServer");
	if (isset($argv[1]))
	{
	$msg = $argv[1];
	}
	else
	{
	$msg = "search2N";
	}
	$request = array();
	$request['type'] = "search2N";
	//send search to DMZ end
	$request['search2N'] = $search;
	//Send/Get response and save as variable
	$response = $client->send_request($request);
	//PHP_EOL should echo in from backend 
	//May induce unintended effects
	echo "".PHP_EOL;
	echo "Sent and recieved detail search info.";
	echo"\n";

	//return API data
	return $response;

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
	$ShowOwnedStock = mysqli_query($db, "SELECT * FROM ".$username."_stocks");
	$fetchStock = array();
	while ($row_user = mysqli_fetch_assoc($ShowOwnedStock)){
		$old_price = $row_user["old_price"];
		$cur_price = $row_user["cur_price"];
		$percent_diff = (($cur_price-$old_price)/$old_price) * 100;
		$p_round = round($percent_diff, 2);
		$p_format = "".$p_round."% stock price since yesterday.";
		$fetchStock[] = array($row_user["symbol"], $row_user["amt"], $p_format);
		}
	echo $percent_diff;
	echo "Stocks sent to client.";
	return $fetchStock;
	errorCheck($db);
}

function doShowTrading($username) {
	global $db;
	$ShowTrading = mysqli_query($db, "SELECT type, symbol, shares, date, cost FROM trading WHERE username = '$username'");
	$fetchTrading = array();
	while ($row_user = mysqli_fetch_assoc($ShowTrading))
		$fetchTrading[] = $row_user;
	echo "Trade history sent to client.";
	return $fetchTrading;
	errorCheck($db);
}

function doBuyStock($username, $symbol, $amount) {
	global $db;

$client = new rabbitMQClient("DMZRabbitMQ.ini","testServer");
	if (isset($argv[1]))
	{
	$msg = $argv[1];
	}
	else
	{
	$msg = "buyStock2";
	}
	$request = array();
	$request['type'] = "buyStock2";
	//send buy variables to DMZ end
	$request['amount'] = $amount;
	$request['symbol'] = $symbol;
	$request['message'] = $msg;
	//recieve api info from DMZ as variable
	$response = $client->send_request($request);
	//PHP_EOL should echo in from backend 
	//May induce unintended effects
	echo "".PHP_EOL;
	echo "Sent and recieved buy info.";
	echo"\n";

	//set new variables from fetched API info
	$i_amount = $response[0];
	$total = $response[1];
	$old_price = $response[2];
	$cur_price = $response[3];

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
	//If new stock, insert into table and add Portfolio values. Else, update existing data.
	$s = "select * from ".$username."_stocks where symbol = '$symbol'";
	$testq = mysqli_query($db, $s);
	$testarray = mysqli_fetch_array($testq);
	if (is_null($testarray))
	{
		$s = "insert into ".$username."_stocks (symbol, amt, old_price, cur_price) values ('$symbol','$i_amount', '$old_price', '$cur_price')";
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

	//Call for API data here
	$client = new rabbitMQClient("DMZRabbitMQ.ini","testServer");
	if (isset($argv[1]))
	{
	$msg = $argv[1];
	}
	else
	{
	$msg = "sellStock2";
	}
	$request = array();
	$request['type'] = "sellStock2";
	//send sell variables to DMZ end
	$request['amount'] = $amount;
	$request['symbol'] = $symbol;
	$request['message'] = $msg;
	//set new variables from fetched API info
	$response = $client->send_request($request);
	//PHP_EOL should echo in from backend 
	//May induce unintended effects
	echo "".PHP_EOL;
	echo "Sent and recieved sell info.";
	echo"\n";

	//return API data
	$i_amount = $response[0];
	$total = $response[1];

	$s = "update students set bal = bal + '$total' where BINARY username = '$username' ";
	mysqli_query ($db, $s);
	$s = "insert into trading (username, type, symbol, shares, date, cost) values ('$username', 'selling', '$symbol', '$i_amount', NOW(), $total)";
	mysqli_query ($db, $s);
	$s = "update ".$username."_stocks set amt = amt - '$i_amount' where symbol = '$symbol' ";
	mysqli_query ($db, $s);
	echo "Successful sell!\n\n";
	//Drop stock from table if all stocks from that specific share are sold
	$s = "DELETE FROM ".$username."_stocks WHERE amt ='0'";
	mysqli_query ($db, $s);
	return "Your transaction was successful! You have sold ".$i_amount." shares of ".$symbol." stock for $".$total."!";
	errorCheck($db);
}

function doUpdateEmail($username, $pert){
	global $db;
	$s = "update students set p_percent = '$pert' where BINARY username = '$username' ";
	mysqli_query ($db, $s);
	return "Updated email percent of ".$username." to ".$pert."% !";
	errorCheck($db);
}

function doReturnGraph($searchgraph){
	$client = new rabbitMQClient("DMZRabbitMQ.ini","testServer");
	if (isset($argv[1]))
	{
	$msg = $argv[1];
	}
	else
	{
	$msg = "graph2";
	}
	$request = array();
	$request['type'] = "graph2";
	//send buy variables to DMZ end
	$request['searchgraph'] = $searchgraph;
	//recieve api info from DMZ as variable
	$response = $client->send_request($request);
	return $response;
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
		return doRegister($request['username'], $request['password'],$request['email']);

	case "RSS":
		return doRSSFeed();

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

	case "showTrading":
		return doShowTrading($request['username']);

	case "buyStock":
		return doBuyStock($request['username'], $request['symbol'], $request['amount']);
	
	case "sellStock":
		return doSellStock($request['username'], $request['symbol'], $request['amount']);

	case "updateEmail":
		return doUpdateEmail($request['username'], $request['pert']);

	case "graph":
		return doReturnGraph($request['searchgraph']);

	}
	return array("returnCode" => '0', 'message'=>"Server received request and processed.");
}


function errorCheck($db) {
	if ($db->errno != 0) {
		for ($x = 0; $x <= 2; $x++) {
    			echo "REQUEST ERROR!" . PHP_EOL;
			logger( __FILE__ . " : " . __LINE__ . " :error: " . "Database error! Check Database server.");
		} 
		echo "Failed to execute query:".PHP_EOL;
		echo __FILE__.':'.__LINE__.":error: ".$db->error.PHP_EOL;
		exit(0);
	}
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

$server->process_requests('requestProcessor');
exit();

?>
