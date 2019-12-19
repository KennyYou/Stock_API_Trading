<?php 

require_once('../include/path.inc');
require_once('../include/rabbitMQLib.inc');
require_once('../include/logger.inc');


$client = new rabbitMQClient("testRabbitMQ.ini","testserver");

function send ($version, $user,$ip,$namepkg,$description){
	
	$output = shell_exec("cat ~/git/The_Project/$namepkg.tar.gz $user@$ip:~/ ");
	
	$conn = mysqli_connect('localhost', 'root','password','dbname'); 
	if(!$conn){
		echo ("mySQL ERROR: " . mysqli_connect_error());
		
	}
	else {
		echo "SUCCESS: Connection Established to DB\n";
		
	}
	$request["type"] = "pkg";
	$request["pkg_name"]= $namepkg;
	$request["path"] = $path; 
	$request["description"] = $description
	
	$version_string = substr($namepkg, -3);
	$version_string[1] = ",";
	$ver_ID = floatval($version_string);
	
	if($query = mysqli_prepare($conn, "INSERT INTO packages (pkgName,description,validated,pkgPath) VALUES (?,?,?,?)")){
		$query ->bind_param("ssh", $namepk, $user, $version,$description);
		$query ->execute(); 
		echo "INSERTED INTO dbname database " .PHP_EOL;
		echo "ROWS CHANGED: " . $query -> affected_rows . PHP_EOL;
		
	}
	
}
exit(0);