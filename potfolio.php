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
print_r($response);
echo"\n";


?>


</body>
</html>
