<!DOCTYPE html>
<html>
<body>
<div class="topnav">

<a class="active" href="loginsuccess.php">HOME</a> 
<a class="active" href="profile.php">PROFILE</a> 
<a class="active" href="buy.php">BUY</a> 
<a class="active" href="sell.php">SELL</a> 
<a class="active" href="index.html">LOGOUT<a>
</div>

<form method="POST">
<h2>Search a Stock:</h2>
	<input type="text" name="searchg" placeholder="Enter a stock symbol:" required>
	<button type="submit">Search</button><br>
</form>
<?php

include('cache.php');

$title = $_POST['searchg'];
echo "<br>Stock History of stock symbol: " . $title;
function beliefmedia_alphavantage_quotes($symbol, $args = '') {
$response = '9J4N8FA67HVHYZG0';
  $farray = array(
    'width' => '1200',
    'height' => '500',
    'time' => '2',
    'number' => '90',
    'size' => 'full', 
    'interval' => '60', 
    'apikey' => $response,
    'cache' => 3600
  );

 /* Merge $args with $farray */
 $farray = (empty($args)) ? $farray : array_merge($farray, $args);

 $transient = 'alpha_vantage_stock_' . md5(serialize($farray) . $symbol);
 $cachedposts = beliefmedia_get_transient($transient, $farray['cache']);

 if ($cachedposts !== false) {
  return $cachedposts;
 } else {

    switch ($farray['time']) {
        case 1:
            $series = 'TIME_SERIES_INTRADAY';
            $series_name = 'Time Series (' . $farray['interval'] . 'min)';
            break;
        case 2:
            $series = 'TIME_SERIES_DAILY';
            $series_name = 'Time Series (Daily)';
            break;
        case 3:
            $series = 'TIME_SERIES_DAILY_ADJUSTED';
            $series_name = 'Time Series (Daily)';
            break;
        case 4:
            $series = 'TIME_SERIES_WEEKLY';
            $series_name = 'Weekly Time Series';
            break;
        case 5:
            $series = 'TIME_SERIES_MONTHLY';
            $series_name = 'Monthly Time Series';
            break;
        default:
            $series = 'Time Series (Daily)';
            break;
    }

    $data = @file_get_contents('https://www.alphavantage.co/query?function=' . $series . '&symbol=' . $symbol . '&interval=' . $farray['interval'] . 'min&apikey=' . $farray['apikey'] . '&interval=' . $farray['interval'] . 'min&outputsize=' . $farray['size']);
    if ($data === false) return '<p>Data currently unavailable.</p>';
    $data = json_decode($data, true);
    $data = $data[$series_name];
    if ($farray['number'] != '') $data = array_slice($data, 0, $farray['number'], true);
    $data = array_reverse($data, true);

    foreach ($data AS $key => $value) {
      $chart .= ',[new Date(' . str_replace(array('-', ' ', ':'), ',', $key) . '), ' . $value['4. close'] . ']';
    }

    $chart = ltrim($chart, ',');

  /*
	Borrowed Base code from this website, full credit for base code goes to owners. 
	http://www.beliefmedia.com/stock-quote-graph-wordpress
  Added in user interface for input and sending through rabbitMQ
  */
    
   $return = "<script type='text/javascript' src='https://www.gstatic.com/charts/loader.js'></script>
    <script type='text/javascript'>
      google.charts.load('current', {packages: ['corechart', 'line']});
      google.charts.setOnLoadCallback(drawTrendlines);

    function drawTrendlines() {
      var data = new google.visualization.DataTable();
        data.addColumn('date', 'Date');
        data.addColumn('number', 'Close');

      data.addRows([
        $chart
      ]);

      var options = {
        hAxis: {
          title: 'Date'
        },
        backgroundColor: 'transparent',
        vAxis: {
          title: 'Stock Price'
        },
        colors: ['#06ab09'],
        trendlines: {
          // 0: {type: 'exponential', color: '#333', opacity: 1},
          // 1: {type: 'linear', color: '#111', opacity: .3}
        }
      };

      var chart = new google.visualization.LineChart(document.getElementById('chart_div_$interval'));
      chart.draw(data, options);
    }
    </script>";
 
    $return .= '<div id="chart_div_' . $interval . '" style="width: ' . $farray['width'] . 'px; height: ' . $farray['height'] . 'px;"></div>';

   beliefmedia_set_transient($transient, $return, $farray['cache']);
   return $return;
 }
}

// Send out the user inputed function for graphing.
if(isset($_POST['searchg'])){
$request = $_POST['searchg'];
echo beliefmedia_alphavantage_quotes($symbol = $request);
}
//END PHP
?>

</body>
</html>
