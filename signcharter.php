<?php
$title = 'Thanks for updating your signature status';
require_once 'nav.php';
require_once 'notify.php';
if ($_POST['charteruserid'] == $charteruserid) {
	if ($_POST['iagree']) $signatory = 1;
	else $signatory = 0;
	$conn = new mysqli($db_hostname, $db_username, $db_password, $db_database);
	// Check connection
	if ($conn->connect_error) {
	die("Connection failed, please contact support");
	}
	$sql = "UPDATE `users` SET `signatory` = " . $signatory . " WHERE charteruserid=" . $charteruserid;
	$result = $conn->query($sql);
	notifyall($forename . ' ' . $surname . ' has ' . ($signatory == 1 ? 'signed' : 'unsigned') . ' the charter.','index.php');
	echo 'Your status has been updated and all users have been notified - Please return to <a href="index.php">homepage</a>';
}
else echo 'I think you are in the wrong place...<br/>Please return to homepage to sign charter.';
echo $foot;
?>