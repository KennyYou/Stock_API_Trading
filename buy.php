<!DOCTYPE HTML>
<html>
<body>

<div class="topnav">

<a class="active" href="#HOME">Home</a> 
<a class="active" href="#profile">Portfolio</a> 
<a class="active" href="sell.html">Sell</a> 
<a class="active" href="index.html">Logout<a>
</div>



<h2>Welcome to Stock Transaction</h2>

<?php
session_start();
$symbol = $_SESSION['symbol'];
$currPrice = $_SESSION['currPrice'];
echo "Hello $username";
echo nl2br ("\n");
echo "Sybmol : $symbol";
echo nl2br ("\n");
echo "Current Price : $currPrice";
?>
<br>
How many stocks do you want to buy?
<br>

 <form method="POST" action="">
  <input type="text" name="search"><br>
     <input type="text" name="search" placeholder="Enter a stock symbol" required><br>

  <button type="submit"> Submit </button>
</form>









<?php
$quantity = $_POST['search'];
echo "You are buying these $quantity stocks of $symbol";
?>


</body>
</html>


