<?php
require_once 'dblogins.php';

//Begin get browser and OS
$user_agent     =   $_SERVER['HTTP_USER_AGENT'];
function getOS() { 
	global $user_agent;
	$os_platform    =   "Unknown OS Platform (Could be windows 10)";
	$os_array       =   array(
							'/windows nt 6.3/i'     =>  'Windows 8.1',
							'/windows nt 6.2/i'     =>  'Windows 8',
							'/windows nt 6.1/i'     =>  'Windows 7',
							'/windows nt 6.0/i'     =>  'Windows Vista',
							'/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
							'/windows nt 5.1/i'     =>  'Windows XP',
							'/windows xp/i'         =>  'Windows XP',
							'/windows nt 5.0/i'     =>  'Windows 2000',
							'/windows me/i'         =>  'Windows ME',
							'/win98/i'              =>  'Windows 98',
							'/win95/i'              =>  'Windows 95',
							'/win16/i'              =>  'Windows 3.11',
							'/macintosh|mac os x/i' =>  'Mac OS X',
							'/mac_powerpc/i'        =>  'Mac OS 9',
							'/linux/i'              =>  'Linux',
							'/ubuntu/i'             =>  'Ubuntu',
							'/iphone/i'             =>  'iPhone',
							'/ipod/i'               =>  'iPod',
							'/ipad/i'               =>  'iPad',
							'/android/i'            =>  'Android',
							'/blackberry/i'         =>  'BlackBerry',
							'/webos/i'              =>  'Mobile'
						);

	foreach ($os_array as $regex => $value) { 
		if (preg_match($regex, $user_agent)) {
			$os_platform    =   $value;
		}

	}   
	return $os_platform;
}
function getBrowser() {
	global $user_agent;
	$browser        =   "Unknown Browser";
	$browser_array  =   array(
							'/msie/i'       =>  'Internet Explorer',
							'/firefox/i'    =>  'Firefox',
							'/safari/i'     =>  'Safari',
							'/chrome/i'     =>  'Chrome',
							'/opera/i'      =>  'Opera',
							'/netscape/i'   =>  'Netscape',
							'/maxthon/i'    =>  'Maxthon',
							'/konqueror/i'  =>  'Konqueror',
							'/mobile/i'     =>  'Mobile'
						);

	foreach ($browser_array as $regex => $value) { 
		if (preg_match($regex, $user_agent)) {
			$browser    =   $value;
		}
	}
	return $browser;
}
$user_os        =   getOS();
$user_browser   =   getBrowser();
/* End Get Browser and OS */
//Generate Token
function generatetoken($urlreturn, $id) {           
	global $db_hostname;
	global $db_username;
	global $db_password;
	global $db_database;
	global $user_os;
	global $user_browser;
	$db_servertoken = mysql_connect($db_hostname, $db_username, $db_password);
	if (!$db_servertoken) die("Unable to connect to MySQL");
	mysql_select_db($db_database) or die("Unable to select database: " . mysql_error());
	$tokenquery = "INSERT INTO tokens VALUES ('','" . date('Y-m-d G:i:s') . "','" . $id . "','" . ($_SERVER['REMOTE_ADDR']?:($_SERVER['HTTP_X_FORWARDED_FOR']?:$_SERVER['HTTP_CLIENT_IP'])) .  "', '1', '" . $_SERVER[HTTP_HOST] . $_SERVER[REQUEST_URI] .  "', 'Unknown', '" . $_SERVER['HTTP_USER_AGENT'] . "', '" . $user_os . "', '" . $user_browser . "');";
	$resultoftokenquery = mysql_query($tokenquery);
	if (!$resultoftokenquery) die ("Database access failed - contact support");
	$tokenquery = "SELECT LAST_INSERT_ID()";
	$resultoftokenquery = mysql_query($tokenquery);
	if (!$resultoftokenquery) die ("Database access failed - contact support");
	
	$arraytokenresult = mysql_fetch_array($resultoftokenquery);
	$token = $arraytokenresult['LAST_INSERT_ID()'];
	$_SESSION['token'] = $token;
	
	if ($urlreturn != '') die('<meta http-equiv="refresh" content="0;url=' . $urlreturn . '" />');
	else die('<meta http-equiv="refresh" content="0;url=index.php" />');
} 
?>