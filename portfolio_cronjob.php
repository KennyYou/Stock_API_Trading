#!/usr/bin/php
<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
include('db.php');

function errorCheck($db) {
	if ($db->errno != 0) {
		echo "Failed to execute query:".PHP_EOL;
		echo __FILE__.':'.__LINE__.":error: ".$db->error.PHP_EOL;
		exit(0);
	}
}
//Get list of Users
global $db;
$ShowUsers = mysqli_query($db, "SELECT username FROM students");
$fetchUsers = array();
while ($row_user = mysqli_fetch_assoc($ShowUsers))
	$fetchUsers[] = $row_user["username"];
echo "User array compiled.";
echo"\n";
//For each User, collect array of stocks and send to DMZ
foreach ($fetchUsers as $User) {
	$ShowStocks = mysqli_query($db, "SELECT symbol FROM ".$User."_stocks");
	$fetchStocks = array();
	$client = new rabbitMQClient("DMZRabbitMQ.ini","testServer");
	while ($row_stock = mysqli_fetch_assoc($ShowStocks)){
		$fetchStocks[] = $row_stock["symbol"];
	}
	//Start DMZ connection and return array of 
	if (isset($argv[1]))
	{
	$msg = $argv[1];
	}
	else
	{
	$msg = "portfolioCron";
	}
	$request = array();
	$request['type'] = "portfolioCron";
	//send stock array to DMZ end
	$request['stockArray'] = $fetchStocks;
	$request['message'] = $msg;
	//recieve stock prices from DMZ as associative array
	$response = $client->send_request($request);
	//PHP_EOL should echo in from backend 
	//May induce unintended effects
	echo "".PHP_EOL;
	echo "Sent and recieved stock info.";
	echo "\n";
	//Get user email and portfolio percent before looking at stocks
	$s = "SELECT * FROM students WHERE username = $User";
	$t = mysqli_query ($db, $s);
	while ($r = mysqli_fetch_assoc($t)){
		$p_percent = $r["p_percent"];
		$mailaddress = $r["email"];
	}
	//Here you deconstruct the array and assign the values to each stock in the user's tables.
	foreach ($response as $symbol => $stock){
		echo $symbol;
		echo "\n";
		$old_price = $response[$symbol]["old"];
		$cur_price = $response[$symbol]["cur"];
		$percent_diff = (($cur_price - $old_price)/$old_price)*100;
		//if current price is (percent) more or less than old price, send email to user!
		if (($percent_diff > $p_percent) or ($percent_diff < -$p_percent)){
			$mailsubject = "Stock alert for ".$User." for stock ".$symbol."!";
			$mailbody .= "<br><br> This email has been sent automatically to inform you of a major change to one of your stocks!<br>";
			$mailbody .= "<br>Stock ".$symbol." is now at ".$percent_diff."% of it's current value! you should act accordingly!<br>";
			$mailbody .= "<br>(As a reminder, your email alert percentage is set to ".$p_percent."%. This can be changed in your settings.<br>";
			mail($mailaddress, $mailsubject, $mailbody);
			}
		$s = "update ".$User."_stocks set old_price = $old_price, cur_price = $cur_price where symbol = '$symbol' ";
		mysqli_query ($db, $s);
	}
}
errorCheck($db);

exit(0);
?>
