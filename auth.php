<?php
//require_once 'auth-and-data_header.php';
session_start();

date_default_timezone_set("Europe/London");
function authfail() {
$currenturl = ($_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]);
$currenturl = str_replace('?', '', str_replace((substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1)), '', $currenturl));
$url = ('http://' . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]);
die('<meta http-equiv="refresh" content="0; url=login.php' . '?return=' . $url . '" />');
}


require_once 'dblogins.php';
require_once 'count.php'; //For convert number to words function
if (isset($_SESSION['token'])) {
//Check token          
//Check token is integer  
$is_int = filter_var($_SESSION['token'], FILTER_VALIDATE_INT);
if ($is_int === false) authfail();
//Token is integer
    
//Time to check whether it is valid
$conn = new mysqli($db_hostname, $db_username, $db_password, $db_database);
if ($conn->connect_error) {
    die("Connection failed - contact support");
} 
$tokenchecksql = "SELECT * FROM tokens WHERE token='" . $_SESSION['token'] . "'";
$tokencheckresult = $conn->query($tokenchecksql);
if ($tokencheckresult->num_rows > 0) {
   while($tokencheckrow = $tokencheckresult->fetch_assoc()) {
        //Force token to expire after 1 hour!
	date_default_timezone_set("Europe/London");
	//if ($tokencheckrow["created"] >= date("d-m-Y H:i:s", strtotime($row['data'])+3600)) authfail();
        //End Force token to expire after 1 hour
        //Check if the IP matches that preset in table
	$ip = $_SERVER['REMOTE_ADDR']?:($_SERVER['HTTP_X_FORWARDED_FOR']?:$_SERVER['HTTP_CLIENT_IP']);
	if ($tokencheckrow["ip"] != $ip) authfail();
		
        //The ip matches that in the table
        
        $id = $tokencheckrow["userid"];
        
        //The token looks good!
        
        //Get user data
        $query = "SELECT * FROM users WHERE facebookid='" . $tokencheckrow["userid"]  . "'";
        $resultofquery = $conn->query($query);
        if (!$resultofquery) die ("Database access failed please contact support");
		if (mysqli_num_rows($resultofquery) == 0) authfail();
		$result = mysqli_fetch_row($resultofquery);
        $forename = $result[1];        
        $surname = $result[2];
        $email = $result[4];
        $userid = $result[0];
		//if ($result[5] != 1) authfail();
		$signatory = $result[5];
		$alljapesareacceptable = $result[6];
		$charteruserid = $result[7];
        
        //AUTH SUCCESS!
        }}} 
        else authfail();
?>