<!DOCTYPE HTML>
<html>
<body>

<div class="topnav">

<a class="active" href="loginsuccess.php">Home</a> 
<a class="active" href="#profile">Portfolio</a> 
<a class="active" href="sell.php">Sell</a> 
<a class="active" href="index.html">Logout<a>
</div>



<h2>Welcome to Stock Transaction</h2>

<br>
How many stocks do you want to buy?
<br>

 <form method="POST">
  <input type="text" name="amount" placeholder="Enter a amount" required><br> 
  <input type="text" name="symbol" placeholder="Enter a stock symbol" required><br>

  <button type="submit"> Submit </button>
</form>

<?php

if(isset($_POST['amount'])){
session_start();
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
if (isset($argv[1]))
{
  $msg = $argv[1];
}
else
{
  $msg = "buyStock";
}

//Send search request over
$request['search'] = $_POST['search'];
$request['message'] = $msg;
$request = array();
$request['type'] = "buyStock";
$request['username'] = $_SESSION["username"];

$request['amount'] = $_POST['amount'];
$request['symbol'] = $_POST['symbol'];

//Send msg
$request['message'] = $msg;

$response = $client->send_request($request);
//PHP_EOL should echo in from backend
echo "".PHP_EOL;
print_r($response);
echo"\n";
}

?>


</body>
</html>
