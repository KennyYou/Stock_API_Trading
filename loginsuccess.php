<!DOCTYPE html>
<html>
<body>
<head>
<h1> Welcome to the Stocks Page! </h1>
<p> Brought to you by: Potato Situation </p>
 <?php 
 session_start(); 
if ($_SESSION['logged'] == true) 
{
   echo "Welcome: ";
   echo $_SESSION["username"];
//echo "USER NAME DISPLAYED";
//$user = $_SESSION["user"];
// echo $user;
}
//echo "USER NOT DISPLAYED";
 ?>
</head>
<body>

<div class="topnav">

<a class="active" href="loginsuccess.php">HOME</a> 
<a class="active" href="profile.php">PROFILE</a> 
<a class="active" href="buy.php">BUY</a> 
<a class="active" href="sell.php">SELL</a> 
<a class="active" href="index.html">Logout<a>
</div>

<form method="POST">
<h2>Search a Stock:</h2>
	<input type="text" name="search1S" placeholder="Enter a stock symbol:" required>
	<button type="submit">Search</button><br>

</form>
<table class ="table table-bordered">


<form method="POST">
<h2>Stock Rate(NBBO):</h2>
	<input type="text" name="search1N" placeholder="Enter a stock symbol:" required>
	<button type="submit">Search</button>

</form>
    
<table class ="table table-bordered">
<thead>
<tr>
<th> Stock Info </th>
</tr>
</thead>
<tbody>

<?php
if(isset($_POST['search1S'])){
//here goes the function call
//Going through rabbitMQ
	//Grab required files 
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
  $msg = "search1S";
}
//Send search request over
$request['type'] = "search1S";
$request['search1S'] = $_POST['search1S'];
$request['message'] = $msg;
$response = $client->send_request($request);
//PHP_EOL should echo in from backend
echo "".PHP_EOL;
print_r($response);
echo"\n";
}
//Request Balance automatically
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
  $msg = "requestBalance";
}
//Send search request over
$request['type'] = "requestBalance";
$request['username'] = $_SESSION["username"];
$request['message'] = $msg;
$response = $client->send_request($request);
//PHP_EOL should echo in from backend
echo "".PHP_EOL;
print_r($response['bal']);
echo"\n";
?>

<?php
if(isset($_POST['search1N'])){
//here goes the function call
//Going through rabbitMQ
	//Grab required files 
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
  $msg = "search1N";
}
//Send search request over
$request['type'] = "search1N";
$request['search1N'] = $_POST['search1N'];
$request['message'] = $msg;
$response = $client->send_request($request);
//PHP_EOL should echo in from backend

echo "".PHP_EOL;
echo "Balance is: ";
print_r($response);
echo"\n";
}


?>


</tbody>
</table>
</body>
</html>
