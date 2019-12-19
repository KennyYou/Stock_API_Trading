#!/usr/bin/php

<?php

$it490path = "/home/jdm68/git/The_Project";

require_once("$it490path/path.inc");

require_once("$it490path/get_host_info.inc");

require_once("$it490path/rabbitMQLib.inc");

require_once("$it490path/deployment/deployDB.php");



// Function List

function doRollbackPkg ($type, $pkgMachineType, $destTier, $pkgName, $version, $userName) {



      global $db;

      echo "\nRollback request received!" . PHP_EOL;

      echo "Request Type: " . $type . PHP_EOL;

      echo "Origin Machine Type: " . $pkgMachineType . PHP_EOL;

      echo "Tier (DEV, QA, PROD): " . $destTier . PHP_EOL;

      echo "Package Name: " . $pkgName . PHP_EOL;

      echo "Problematic Version: " . $version . PHP_EOL;



			// mark the given package version as bad

			// check if it exists first, then mark as bad

			$q1 = mysqli_query($db, "SELECT * FROM packages WHERE pkgName = '$pkgName' AND version = '$version'");

			if (mysqli_num_rows($q1) <= 0) {

        echo "The package you're trying to mark as bad ($pkgName-$version) does not exist!\n";

        return;

			}

			$q2 = mysqli_query($db, "UPDATE packages SET valid = 0 WHERE pkgName = '$pkgName' AND version = '$version'");

			echo "$pkgName-$version successfully marked as bad! Rows changed: " . mysqli_affected_rows($db) . PHP_EOL;



      // get the most recent version of the package marked as good; check if it exists first

      $q3 = mysqli_query($db, "SELECT version FROM packages WHERE pkgName = '$pkgName' AND valid = 1 ORDER BY version DESC LIMIT 1");

			if (mysqli_num_rows($q3) <= 0) {

        echo "No rollback version exists for this package ($pkgName)!\n";

        return;

			}

			while ($r = mysqli_fetch_array ($q3, MYSQLI_ASSOC)) {

    		$dbRbVersion = $r["version"];

				echo "A previous version ($dbRbVersion) has been found! Rolling back..." . PHP_EOL;



				// delete the bad package from the target system

        // copy the older version to the target

        $targetHost = strtolower($pkgMachineType) . strtolower($destTier);

	      $output = shell_exec("sshpass -f '/home/jdm68/pwDir/$userName' ssh $userName@$targetHost 'rm -r /home/$userName/Packages/$pkgName-$version/';

        sshpass -f '/home/jdm68/pwDir/$userName' scp -P 22 /home/jdm68/Packages/$pkgName-$dbRbVersion.tar.gz $userName@$targetHost:/home/$userName/Packages/$pkgName-$dbRbVersion.tar.gz;

        sshpass -f '/home/jdm68/pwDir/$userName' ssh $userName@$targetHost 'tar -C /home/$userName/Packages/$pkgName-$dbRbVersion/ -xzfP /home/$userName/Packages/$pkgName-$dbRbVersion.tar.gz'");



        echo "Successfully rolled back " . $pkgName . " to " . $dbRbVersion . "!\n" . PHP_EOL;

      }

}



function doDeployPkg ($type, $pkgMachineType, $destTier, $pkgName, $version, $userName) {



      global $db;

      echo "\nDeploy request received!" . PHP_EOL;

      echo "Request Type: " . $type . PHP_EOL;

      echo "Origin Machine Type: " . $pkgMachineType . PHP_EOL;

      echo "Tier (DEV, QA, PROD): " . $destTier . PHP_EOL;

      echo "Package Name: " . $pkgName . PHP_EOL;



      // check if the package actually exists

      if (mysqli_num_rows(mysqli_query($db, "SELECT * FROM packages WHERE pkgName = '$pkgName' AND version = '$version'")) <= 0) {

        echo "The package you're trying to deploy ($pkgName-$version) does not exist!\n";

        return;

      }



      // check if desired package is valid

      $query = mysqli_query($db, "SELECT valid FROM packages WHERE pkgName = '$pkgName' AND version = '$version'");

      while ($r = mysqli_fetch_array ($query, MYSQLI_ASSOC)) {

    		$valid = $r["valid"];

        if ($valid == 0) {

          // tell user the package is bad and will not deploy if marked as bad

          echo "The package you're trying to deploy ($pkgName-$version.tar.gz) has been marked as bad! This file will not be deployed; please deploy a package that is known to be good.\n";

          return;

        }

      }



    	echo "Deploying " . $pkgName . "-" . $version . ".tar.gz" . " to " . strtoupper($destTier) . " " . strtoupper($pkgMachineType) . "..." . PHP_EOL;



      $filename = "$pkgName-$version.tar.gz";

      $targetHost = strtolower($pkgMachineType) . strtolower($destTier);



      shell_exec("sshpass -f '/home/jdm68/pwDir/$userName' scp -P 22 /home/jdm68/Packages/$filename $userName@$targetHost:/home/$userName/Packages/$filename;

      sshpass -f '/home/jdm68/pwDir/$userName' ssh $userName@$targetHost 'mkdir /home/$userName/Packages/$pkgName-$version/';

      sshpass -f '/home/jdm68/pwDir/$userName' ssh $userName@$targetHost 'tar -C /home/$userName/Packages/$pkgName-$version/ -xzfP /home/$userName/Packages/$filename'");



      echo "Successfully deployed " . $pkgName . "-" . $version . ".tar.gz to " . strtoupper($destTier) . " " . strtoupper($pkgMachineType) . "!\n" . PHP_EOL;

}



function doBundlePkg ($type, $pkgMachineType, $destTier, $pkgName, $version, $userName) {



      global $db;

      echo "\nBundle request received!" . PHP_EOL;

      echo "Request Type: " . $type . PHP_EOL;

      echo "Origin Machine Type: " . $pkgMachineType . PHP_EOL;

      echo "Tier (DEV, QA, PROD): " . $destTier . PHP_EOL;

      echo "Package Name: " . $pkgName . PHP_EOL;

  		echo "Bundle transfer initiatied... ";



      $filename = "$pkgName-$version.tar.gz";

      $targetHost = strtolower($pkgMachineType) . strtolower($destTier);



      shell_exec("sshpass -f '/home/jdm68/pwDir/$userName' scp -P 22 $userName@$targetHost:/home/$userName/Packages/$filename /home/jdm68/Packages/$filename");



  		echo "$filename received!";



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

          doRollbackPkg($request['type'],$request['pkgMachineType'],$request['destTier'],$request['pkgName'],$request['version'], $request['userName']);

          break;

        case "deploy":

          doDeployPkg($request['type'],$request['pkgMachineType'],$request['destTier'],$request['pkgName'],$request['version'], $request['userName']);

          break;

        case "bundle":

          doBundlePkg($request['type'],$request['pkgMachineType'],$request['destTier'],$request['pkgName'],$request['version'], $request['userName']);

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

