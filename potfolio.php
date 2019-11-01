<!DOCTYPE HTML>
<html>

<?php
session_start();
?>


<head>
<title>User Profile</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap.css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<style>
.navbar, .navbar-bar {
color: white;
padding-bottom : 1px;
padding-top : 1px;
}
.container {
        padding-top: 60px;
padding-right : 50px;
}
</style>


</head>

<nav class="navbar header-top fixed-top navbar-expand-lg  navbar-dark bg-dark">

      <a class="navbar-brand">Kya bol reli hai DJ ki Public</a>





        <ul class="navbar-nav ml-md-auto d-md-flex">
          <li class="nav-item">
            <a class="nav-link" href="kumartest1.php">Home
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="portfolio.php">Portfolio</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="https://www.khanacademy.org/economics-finance-domain/ap-macroeconomics/ap-financial-sector/financial-assets-ap/v/introduction-to-interest">Education</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="logout.php">Logout</a>
          </li>



        </ul>
    </nav>
<div class="container">


<body>

<h1> Portfolio </h1>

<?php
echo "Your Current Balance is: $Balance";
?>

<br>
<br>

<div id="oanda_ecc" class="a">
<!-- Note: If you alter or remove the following code, the embedded currency widget will not work-->
<span> </span>
<script src="https://www.oanda.com/embedded/converter/get/b2FuZGFlY2N1c2VyLy9kZWZhdWx0/?lang=en"></script></div>






</body>
</html>
