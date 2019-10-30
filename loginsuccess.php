<!DOCTYPE html>
<html>
<body>
<head>
<h1> Welcome to the Sotcks Page! </h1>
<p> Brought to you by: Potato Situation </p>
 <?php 
 session_start(); 
if ($_SESSION['logged'] == true) 
{
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

<a class="active" href="#HOME">HOME</a> 
<a class="active" href="#profile">PROFILE</a> 
<a class="active" href="#Suggestions">SUGGESTIONS</a> 
<a class="active" href="#buy">BUY</a> 
<a class="active" href="#sell">SELL</a> 
<a class="active" href="index.html">Logout<a>
</div>

<form method="POST">
<h2>Search a Stock:</h2>
	<input type="text" name="search" placeholder="Enter a stock symbol:" required>
	<button type="submit">Search</button>
</form>

<table class ="table table-bordered">
<thead>
<tr>
<th> Stock Info </th>
</tr>
</thead>
<tbody>
</tr>

<?php
if(isset($_POST['search'])){
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
  $msg = "search";
}

//Send search request over
$request['type'] = "search";
$request['search'] = $_POST['search'];
$request['message'] = $msg;


$response = $client->send_request($request);
//PHP_EOL should echo in from backend
echo "".PHP_EOL;
print_r($response);
echo"\n";
}


?>

</tbody>
</table>
</body>
</html>
