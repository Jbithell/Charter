<?php

require_once 'dblogins.php';
require_once 'auth.php';

//Logout System
// Initialize the session.
// If you are using session_name("something"), don't forget it now!
session_start();
$db_server = mysql_connect($db_hostname, $db_username, $db_password);

if (!$db_server) die("Unable to connect to MySQL - Please contact support");

mysql_select_db($db_database)
    or die("Unable to select database - Please contact support");
                                
$query = "UPDATE tokens SET valid = '0' WHERE token = '" . $_SESSION['token'] . "'";

$result = mysql_query($query);
if (!$result) die ("Database access failed - Please contact support");

// Unset all of the session variables.
$_SESSION = array();
// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,$params["path"], $params["domain"],$params["secure"], $params["httponly"]);
}
// Finally, destroy the session.
session_destroy();
?>
<center><i>You have now been logged out. Have a nice day</i></center>

<?php
echo $footer;
?>