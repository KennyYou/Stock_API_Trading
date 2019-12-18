#!/usr/bin/php

<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

//INITIATE API CLIENT
use AlphaVantage\Client;
$alpha_vantage = new Client('9J4N8FA67HVHYZG0');

//This is a rough edited version of testRabbitMQServer.php only including the functions done at the DMZ. Extra comments are included for context

//FUNCTION LIST

//Doing RSS from google news source

function RSS() {
$xml=("https://news.google.com/rss/search?q=stocks&hl=en-US&gl=US&ceid=US:en");
return $xml;
}
//END RSS FEATURE


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
//send output back to backend
}

function doDetailSearch($search) {
	$ch = curl_init("https://www.alphavantage.co/query?function=SYMBOL_SEARCH&keywords=" . $search . "&apikey=9J4N8FA67HVHYZG0");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$curl_results = curl_exec($ch);
	curl_close($ch);

$jsonarray = json_decode($curl_results, true);
$output .= "<th>Symbol</th>";
$output .= "<th>Name</th>";
$output .= "<th>Open</th>";
$output .= "<th>High</th>";
$output .= "<th>Low</th>";
$output .= "<th>Close</th>";
$output .= "<th>Volume</th>";
$output .= "</tr>";
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
//send output back to backend
}

function doPortfolioCron($stockArray) {
	//$fullPriceArray = array();
	foreach ($stockArray as $stock) {
		echo $stock;
		echo "\n";
		$ch = curl_init("https://www.alphavantage.co/query?function=TIME_SERIES_DAILY&symbol=" .$stock. "&apikey=9J4N8FA67HVHYZG0");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$curl_results = curl_exec($ch);
	curl_close($ch);
	$today = date('Y-m-d');
	$yesterday = date('Y-m-d', strtotime("-1 days"));
	echo $today;
	echo "\n";
	echo $yesterday;
	$jsonarray = json_decode($curl_results, true);
	$os_unround = floatval($jsonarray['Time Series (Daily)'][$yesterday]['4. close']);
	$cs_unround = floatval($jsonarray['Time Series (Daily)'][$today]['4. close']);
	$old_stock = round($os_unround,2);
	$cur_stock = round($cs_unround,2);
		echo $old_stock;
		echo "\n";
		//Get stock CLOSE as old_stock and cur_stock
		$priceArray = array("old"=>$old_stock, "cur"=>$cur_stock);
		$fullPriceArray[$stock] = $priceArray;
	}
	return $fullPriceArray;
}

function doBuyStock($symbol, $amount) {
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
	$buyarray = array($i_amount, $total);
	echo "Sent buying info.";
	//print_r($response);
	echo"\n";
	return $buyarray;
	

	//At this point, you are done with collecting API data and need database data, so idealy the needed values would be sent back to the backend through RabbitMQ. The rest of this function would be done on the backend and is unnedded here.

}


function doSellStock($symbol, $amount) {

	//The beginning of this function starts on the backend, and if successful, would pass on to a dmz function

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
	$sellarray = array($i_amount, $total);
	return $sellarray;

	//Send needed values to backend. Rounding can happen either beforehand or afterwards.


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
	
	case "RSS":
		return RSS($request['RSS']);

	case "search2S":
		return doSearch($request['search2S']);

	case "search2N":
		return doDetailSearch($request['search2N']);

	case "portfolioCron":
		return doPortfolioCron($request['stockArray']);

	case "buyStock2":
		return doBuyStock($request['symbol'], $request['amount']);
	
	case "sellStock2":
		return doSellStock($request['symbol'], $request['amount']);
	}
	return array("returnCode" => '0', 'message'=>"Server received request and processed.");
}


$server = new rabbitMQServer("DMZRabbitMQ.ini","testServer");

$server->process_requests('requestProcessor');
exit();

?>
