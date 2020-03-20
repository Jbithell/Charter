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
        
        $conn->close();
        //Get user data
        $db_server = mysql_connect($db_hostname, $db_username, $db_password); 
	if (!$db_server) die("Unable to connect to MySQL");
        mysql_select_db($db_database) or die("Unable to select database");
        $query = "SELECT * FROM users WHERE facebookid='" . $tokencheckrow["userid"]  . "'";
        $resultofquery = mysql_query($query);
        if (!$resultofquery) die ("Database access failed please contact support");
		if (mysql_num_rows($resultofquery) == 0) authfail();
	$result = mysql_fetch_array($resultofquery);
        $forename = $result['forename'];        
        $surname = $result['surname'];
        $email = $result['email'];
        $userid = $result['facebookid'];
		//if ($result['signatory'] != 1) authfail();
		$signatory = $result['signatory'];
		$charteruserid = $result['charteruserid'];
        
        //AUTH SUCCESS!
        }}} 
        else authfail();
?>


<?php
//Navigation
?>
<html>
<head>
<title>Charter</title>
<style>
BODY				{FONT-FAMILY: Verdana, Arial; FONT-SIZE: 10pt}
.datestyle			{FONT-FAMILY: Verdana, Arial; FONT-SIZE: 7pt}
.smallstyle			{FONT-FAMILY: Verdana, Arial; FONT-SIZE: 7pt}
.timetablestyle		{FONT-FAMILY: Verdana, Arial; FONT-SIZE: 7pt}
.mediumstyle		{FONT-FAMILY: Verdana, Arial; FONT-SIZE: 10pt}

A					{COLOR: #4333AA;FONT-FAMILY: Verdana, Arial; TEXT-DECORATION: none}
A:visited			{COLOR: #4333AA;FONT-FAMILY: Verdana, Arial; TEXT-DECORATION: none}
A:hover				{COLOR: #ff2233;FONT-FAMILY: Verdana, Arial; TEXT-DECORATION: underline}
A.linka				{COLOR: white; TEXT-DECORATION: none}
A.linka:visited		{COLOR: white; TEXT-DECORATION: none}
A.linka:hover		{COLOR: white; TEXT-DECORATION: underline}

A.notice			{COLOR: #4333AA;FONT-FAMILY: Verdana, Arial; TEXT-DECORATION: underline}
A.notice:visited	{COLOR: #4333AA;FONT-FAMILY: Verdana, Arial; TEXT-DECORATION: underline}
A.notice:hover		{COLOR: #ff2233;FONT-FAMILY: Verdana, Arial; TEXT-DECORATION: underline}

TABLE				{FONT-FAMILY: Verdana, Arial; FONT-SIZE: 10pt}
</style>
</head>
<body>
<body topmargin="0">
<center>

<table border="0" width="95%">
	<tbody><tr>
	<td align="center" width="20%">
	<a href="index.php"><img alt="Home" style="max-height: 120px;" src="book.jpg" border="0"></a>
	</td><td><?php require_once 'count.php'; echo 'Charter contains ' . $linesofcodewords . ' lines of code, mostly PHP.'; ?>
	<hr color="#dddddd" style="HEIGHT: 1px" size="1">
	<table border="0" width="100%">
	<tbody><tr>
	<td colspan="7" height="30" valign="top">
<?php
echo '<font size="4"><b>' . $forename . ' ' . $surname . '</b></font>
	</td><td height="30" valign="top" align="right">
	
	<!--<div style="background:#3366cc; min-height: 30px; max-width: 100px;"><center><a title="Home" href="index.php"><font color="#ffffff">Home</font></a></center></div>-->
	[<a target="_top" href="logout.php">Logout</a>]
		<font size="4"><b>' . '</b></font>
	
	</td></tr>
</tbody></table>
</td></tr></tbody></table>';
?>
<table width=95%>
<tr><td>
<table cellspacing=0 border=0 width='100%'><tr><td align=center bgcolor=#FFC1C1><table cellspacing=0 cellpadding=2 border=0 width='100%'><tr><td align=center bgcolor=#FFC1C1><font color=black><b>Charter</b></font><tr><td bgcolor=white>

<br />

<center>
<table width=95%>
<tr><td>



<?php
$foot = '
</td></tr>
</table>
</center>

</table></table>
</table>
</center>

</body>
</html>';
?>