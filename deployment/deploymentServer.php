#!/usr/bin/php
<?php
require_once('/home/jdm68/git/The_Project/path.inc');
require_once('/home/jdm68/git/The_Project/get_host_info.inc');
require_once('/home/jdm68/git/The_Project/rabbitMQLib.inc');
require_once('/home/jdm68/git/The_Project/deployment/deployDB.php');

// Function List
function doRollbackPkg ($type, $pkgMachineType, $destTier, $pkgName, $version, $rbVersion) {

      global $db;
      echo "Rollback request received" . PHP_EOL;
      echo "Request Type: " . $type . PHP_EOL;
      echo "Package Tier Type: " . $pkgMachineType . PHP_EOL;
      echo "Destination Tier (Dev, QA, Production): " . $destTier . PHP_EOL;
      echo "Package Name: " . $pkgName . PHP_EOL;
      echo "Problematic Version: " . $version . PHP_EOL;
      echo "Revert Version: " . $rbVersion . PHP_EOL;

	$query = mysqli_query($db, "SELECT pkgName, version FROM packages");
	echo "A previous version has been found! Rolling back..." . PHP_EOL;
	$sourcefile = "/home/jdm68/packages/" . $pkgName . "-" . $rbVersion . ".tar.gz";
	echo "File path: " . $sourcefile . PHP_EOL;
	$sourcefile = escapeshellarg($sourcefile);
	$output = shell_exec("./rollback.sh $sourcefile"); //  rm /home/jdm68/packages/$filename
    }
}

function doDeployPkg ($type, $pkgMachineType, $destTier, $pkgName, $version, $rbVersion) {

      	global $db;
      	echo "Deploy request received!" . PHP_EOL;
	echo "Request Type: " . $type . PHP_EOL;
	echo "Package Tier Type: " . $pkgMachineType . PHP_EOL;
	echo "Destination Tier (Dev, QA, Production): " . $destTier . PHP_EOL;
	echo "Package Name: " . $pkgName . PHP_EOL;
	echo "Version: " . $version . PHP_EOL;

      // check if desired package is valid
      $query = mysqli_query($db, "SELECT pkgName, version, valid FROM packages WHERE pkgName = '$pkgName'");
      while ($r = mysqli_fetch_array ($query, MYSQLI_ASSOC)) {

    		$valid = $r["valid"];
		if ($valid == 0) {
		  // tell user the package is bad and will not deploy
		}
      }

      # execute shell commands to install package
      echo "Deploying " . $pkgName . "-" . $version . ".tar.gz" . " to " . $destTier . " " . $pkgMachineType . "..." . PHP_EOL;
      $filename = "$pkgName-$version.tar.gz";
      shell_exec("cat /home/jdm68/packages/$filename | sshpass -f '/home/jdm68/pwDir/jdm68' ssh jdm68@192.168.2.110 cat > /home/jdm68/deploy/$filename");
      echo "successfully deployed " . $pkgName . "-" . $version . ".tar.gz!" . PHP_EOL;*/
}

function doBundlePkg ($type, $pkgMachineType, $destTier, $pkgName, $version, $rbVersion) {

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
      $query = mysqli_query($db, "INSERT INTO packages (pkgName, version, uploadDateTime, originMachine, destTier) VALUES ('$pkgName', '$version', '$dt', '$pkgMachineType', '$destTier', 1)");
/*	if ($query->affected_rows < 0) {
		echo "ERROR: " . $query->error;
		$query->close();
	}*/
	/*else {*/echo "\nData successfully inserted into deployment database!" . PHP_EOL;//}
}

function requestProcessor($request) {
      echo "received request".PHP_EOL;
      var_dump($request);
      if(!isset($request['type'])) {return "ERROR: Unsupported message type!";}

      switch ($request['type']) {
        case "rollback":
          doRollbackPkg($request['type'],$request['pkgMachineType'],$request['destTier'],$request['pkgName'],$request['version'],$request['rbVersion']);
          break;

        case "deploy":
          doDeployPkg($request['type'],$request['pkgMachineType'],$request['destTier'],$request['pkgName'],$request['version'],$request['rbVersion']);
          break;

        case "bundle":
          doBundlePkg($request['type'],$request['pkgMachineType'],$request['destTier'],$request['pkgName'],$request['version'],$request['rbVersion']);
          break;
      }
      return array("returnCode" => '0', 'message'=>"Server received request and processed");
    }

    $server = new rabbitMQServer("/home/jdm68/git/The_Project/deployment/deploy.ini","testServer");
    echo "Potato Situation Deployment Server BEGIN" . PHP_EOL;
    $server->process_requests('requestProcessor');
    echo "Potato Situation Deployment Server END" . PHP_EOL;
    exit();
?>
