#!/usr/bin/php

<?php

$it490path = "/home/jdm68/git/The_Project";

require_once("$it490path/path.inc");

require_once("$it490path/get_host_info.inc");

require_once("$it490path/rabbitMQLib.inc");

require_once("$it490path/deployment/deployDB.php");



// Function List

function doRollbackPkg ($type, $pkgMachineType, $destTier, $pkgName, $version, $rbVersion, $userName, $ipAddress) {

      global $db;



      echo "Rollback request received" . PHP_EOL;

      echo "Request Type: " . $type . PHP_EOL;

      echo "Package Tier Type: " . $pkgMachineType . PHP_EOL;

      echo "Destination Tier (Dev, QA, Production): " . $destTier . PHP_EOL;

      echo "Package Name: " . $pkgName . PHP_EOL;

      echo "Problematic Version: " . $version . PHP_EOL;

      echo "Revert Version: " . $rbVersion . PHP_EOL;



      // get the most recent version of the package marked as good

      $query = mysqli_query($db, "SELECT version, valid FROM packages WHERE pkgName = '$pkgName' AND valid = 1");

      echo "A previous version has been found! Rolling back..." . PHP_EOL;



      $output = shell_exec("sshpass -f '/home/jdm68/pwDir/jdm68' scp jdm68@192.168.2.110:/home/jdm68/packages/$pkgName-$version.tar.gz $pkgName-$version.tar.gz"); //  rm /home/jdm68/packages/$filename

}



function doDeployPkg ($type, $pkgMachineType, $destTier, $pkgName, $version, $unused, $userName, $ipAddress) {

      global $db;



      echo "Deploy request received!" . PHP_EOL;

    	echo "Request Type: " . $type . PHP_EOL;

    	echo "Package Tier Type: " . $pkgMachineType . PHP_EOL;

    	echo "Destination Tier (Dev, QA, Production): " . $destTier . PHP_EOL;

    	echo "Package Name: " . $pkgName . PHP_EOL;

    	echo "Version: " . $version . PHP_EOL;



      // check if the package actually exists

      if (mysqli_num_rows(mysqli_query($db, "SELECT * FROM packages WHERE pkgName = '$pkgName' AND version = '$version'")) <= 0) {

        echo "The package you're trying to deploy ($pkgName-$version.tar.gz) does not exist!";

        return;

      }



      // check if desired package is valid

      $query = mysqli_query($db, "SELECT valid FROM packages WHERE pkgName = '$pkgName' AND version = '$version'");

      while ($r = mysqli_fetch_array ($query, MYSQLI_ASSOC)) {

    		$valid = $r["valid"];

        if ($valid == 0) {

          // tell user the package is bad and will not deploy if marked as bad

          echo "The package you're trying to deploy ($pkgName-$version.tar.gz) has been marked as bad! This file will not be deployed; please deploy a package that is known to be good.";

          return;

        }

      }



    	#echo "Pushing " . $pkgName . " on " . $destTier ." " . $pkgMachineType;

    	# execute shell script to install backend package

    	echo "Deploying " . $pkgName . "-" . $version . ".tar.gz" . " to " . $destTier . " " . $pkgMachineType . "..." . PHP_EOL;

      $filename = "$pkgName-$version.tar.gz";

      shell_exec("cat /home/jdm68/packages/$filename | sshpass -f '/home/jdm68/pwDir/jdm68' ssh jdm68@192.168.2.110 cat > /home/jdm68/deploy/$filename");

      echo "successfully deployed " . $pkgName . "-" . $version . ".tar.gz!" . PHP_EOL;

}



function doBundlePkg ($type, $pkgMachineType, $destTier, $pkgName, $version, $unused, $userName, $ipAddress) {

      global $db;

      echo "Bundle request received!" . PHP_EOL;

      echo "Request Type: " . $type . PHP_EOL;

      echo "Package Tier Type: " . $pkgMachineType . PHP_EOL;

      echo "Destination Tier (Dev, QA, Production): " . $destTier . PHP_EOL;

      echo "Package Name: " . $pkgName . PHP_EOL;

  		echo "SCP initiatied... ";

      $filename = "$pkgName-$version.tar.gz";

      shell_exec("cat /home/jdm68/packages/$filename | sshpass -f '/home/jdm68/pwDir/jdm68' ssh jdm68@192.168.2.110 cat > /home/jdm68/$filename");

  		echo "$pkgName-$version.tar.gz received!";



  		// insert package information in database deployment table

      $dt = date('Y-m-d H:i:s');

  		$query = mysqli_query($db, "INSERT INTO packages (pkgName, version, uploadDateTime, originMachine, destTier, valid) VALUES ('$pkgName', '$version', '$dt', '$pkgMachineType', '$destTier', 1)") or mysqli_error($db);



    		if (mysqli_affected_rows($db) < 0) {echo "\nERROR: " . mysqli_error($db);}

    		else {echo "\nData successfully inserted into deployment database! Rows affected: " . mysqli_affected_rows($db) . PHP_EOL;}

}



function requestProcessor($request) {

      echo "received request".PHP_EOL;

      var_dump($request);

      if(!isset($request['type'])) {return "ERROR: Unsupported message type!";}

      switch ($request['type']) {

        case "rollback":

          doRollbackPkg($request['type'],$request['pkgMachineType'],$request['destTier'],$request['pkgName'],$request['version'],$request['rbVersion'], $request['userName'], $request['ipAddress']);

          break;

        case "deploy":

          doDeployPkg($request['type'],$request['pkgMachineType'],$request['destTier'],$request['pkgName'],$request['version'],$request['rbVersion'], $request['userName'], $request['ipAddress']);

          break;

        case "bundle":

          doBundlePkg($request['type'],$request['pkgMachineType'],$request['destTier'],$request['pkgName'],$request['version'],$request['rbVersion'], $request['userName'], $request['ipAddress']);

          break;

      }

      return array("returnCode" => '0', 'message'=>"Server received request and processed");

    }

    $server = new rabbitMQServer("$it490path/deployment/deploy.ini","testServer");

    echo "Potato Situation Deployment Server BEGIN" . PHP_EOL;

    $server->process_requests('requestProcessor');

    echo "Potato Situation Deployment Server END" . PHP_EOL;

    exit();

?>

