<?php
$title = 'Thanks for updating your acceptable jape status';
require_once 'nav.php';
require_once 'notify.php';
if ($_POST['charteruserid'] == $charteruserid) {
	if ($_POST['alljapesareacceptable']) $alljapesareacceptable = 1;
	else $alljapesareacceptable = 0;
	$conn = new mysqli($db_hostname, $db_username, $db_password, $db_database);
	// Check connection
	if ($conn->connect_error) {
	die("Connection failed, please contact support");
	}
	$sql = "UPDATE `users` SET `alljapesareacceptable` = " . $alljapesareacceptable . " WHERE charteruserid=" . $charteruserid;
	$result = $conn->query($sql);
	notifyall($forename . ' ' . $surname . ' has decided that ' . ($alljapesareacceptable == 1 ? 'all japes' : 'only those japes mentioned in the acceptable japes list') . ' are acceptable.','index.php');
	echo 'Your status has been updated and all users have been notified - Please return to <a href="index.php">homepage</a>';
}
else echo 'I think you are in the wrong place...<br/>Please return to homepage to sign charter.';
echo $foot;
?>