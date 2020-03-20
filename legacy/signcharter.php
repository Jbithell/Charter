<?php
require_once 'auth.php';
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
	echo 'Status Updated - Please return to <a href="index.php">homepage</a>';
}
else echo 'I think you are in the wrong place...<br/>Please return to homepage to sign charter.' . $foot;
?>