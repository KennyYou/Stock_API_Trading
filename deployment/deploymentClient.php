<?php
require_once("../path.inc");
require_once("../rabbitMQLib.inc");
require_once("../get_host_info.inc");

echo "Enter information to the deployment server:\n";
$type = readline("Enter your desired action (bundle, deploy, rollback): ");
if ($type != "bundle" || $type != "deploy" || $type != "rollback") {
  
}
else{echo "Not a valid action! Please try again.";
  return;
}
$pkgMachineType = readline("Enter your origin machine type (FE, BE, DMZ): ");
if ($pkgMachineType != "FE" || $pkgMachineType != "BE" || $pkgMachineType != "DMZ") {
  
}
else{echo "Not a valid action! Please try again.";
  return;
}
if ($type == "bundle") {
$folderName = readline("ENTER NAME OF FOLDER TO BUNDLE: ");

$destTier = readline("Enter the tier where the folder is coming from (DEV, QA, PROD): ");}
else {$destTier = readline("Enter the destination tier (DEV, QA, PROD): ");}
if ($destTier != "DEV" || $destTier != "QA" || $destTier != "PROD") {
  
}
else{echo "Not a valid action! Please try again.";
  return;
}
$pkgName = readline("Enter the package's name: ");
if ($type == "rollback") {
  $version = readline("Enter the version number of the problematic package: ");

}
else {
  $version = readline("Enter the package's version: ");
 
}

//if (isset($argv[1])) {$msg = $argv[1];}
//else {$msg = "deploy";}
$user = get_current_user();
$client = new rabbitMQClient("deploy.ini", "testServer");
$request = array();
$request['type'] = strtolower($type);
$request['pkgMachineType'] = strtoupper($pkgMachineType);
$request['destTier'] = strtoupper($destTier);
$request['pkgName'] = $pkgName;
$request['version'] = $version;
$request['userName'] = $user; // get the local user's userName

if($type == "bundle"){
$request['folderName'] = $folderName;
$output = shell_exec("tar -zcvf /home/$user/Packages/$pkgName-$version.tar.gz /home/$user/Packages/$folderName/");
}
//$request['message'] = $msg;

// bundle the folder


$response = $client -> send_request($request);
echo "Sent your request message for $type!\n";
exit();
?>
