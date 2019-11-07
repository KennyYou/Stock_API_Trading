<!DOCTYPE HTML>
<html>
<body>

<div class="topnav">

<a class="active" href="loginsuccess.php">Home</a>  
<a class="active" href="buy.php">Buy</a> 
<a class="active" href="sell.php">Sell</a> 
<a class="active" href="index.html">Logout<a>
</div>



<h2>Profile!</h2>
<?php
session_start();
if ($_SESSION['logged'] == true) 
{
   echo "Welcome to your profile: ";
   echo $_SESSION["username"];
}

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
  $msg = "showOwnedStock";
}

//Send search request over
$request = array();
$request['type'] = "showOwnedStock";
$request['username'] = $_SESSION["username"];
echo "<br>";
echo"Your stocks are as follows:";
echo "<br>";
//Send msg
$request['message'] = $msg;

$response = $client->send_request($request);
//PHP_EOL should echo in from backend
echo "".PHP_EOL;

print_r($response[0]["symbol"]);
print_r(" ");
print_r($response[0]["amt"]);
echo "<br>";

print_r($response[1]["symbol"]);
print_r(" ");
print_r($response[1]["amt"]);
echo "<br>";


print_r($response[2]["symbol"]);
print_r(" ");
print_r($response[2]["amt"]);
echo "<br>";

print_r($response[3]["symbol"]);
print_r(" ");
print_r($response[3]["amt"]);
echo "<br>";

print_r($response[4]["symbol"]);
print_r(" ");
print_r($response[4]["amt"]);
echo "<br>";


print_r($response[5]["symbol"]);
print_r(" ");
print_r($response[5]["amt"]);
echo "<br>";
echo"\n";

//Printing out transaction history 
if (isset($argv[1]))
{
  $msg1 = $argv[1];
}
else
{
  $msg1 = "showTrading";
}

$request1 = array();
$request1['type'] = "showTrading";
$request1['username'] = $_SESSION["username"];
echo "<br>";
echo"Your transaction history are as follows:";
echo "<br>";
//Send msg
$request1['message'] = $msg1;

$response1 = $client->send_request($request1);
//PHP_EOL should echo in from backend displaying transaction history 
echo "".PHP_EOL;
for ($i = 0; $i < count($request1); $i++) {
    print_r($response[$i]['type']);
	print_r(" ");
    print_r($response[$i]['symbol']);
	print_r(" ");
    print_r($response[$i]['shares']);
	print_r(" ");
    print_r($response[$i]['date']);
	print_r(" ");
    print_r($response[$i]['cost']);
	echo "<br>";
}
?>


</body>
</html>
