<?php
$title = 'Withdraw an Amendment';
require_once 'nav.php';
if (!isset($_GET['amendmentid'])) die('Please return to <a href="index.php">homepage</a> and click on a clause to withdraw');
// Create connection
$conn = new mysqli($db_hostname, $db_username, $db_password, $db_database);
// Check connection
if ($conn->connect_error) {
     die("Connection failed");
} 

date_default_timezone_set("Europe/London");
$sql = "UPDATE `amendments` SET `status` = '4' WHERE `amendmentid` = " . mysqli_real_escape_string($conn, $_GET['amendmentid']) . " AND `tabledby` = " . $charteruserid;
$result = $conn->query($sql);

//Notify Users
require_once 'notify.php';
notifyall($forename . ' ' . $surname . ' has withdrawn an amendment.','index.php');
echo 'Success - Your amendment has been withdrawn and the users have been notified! <a href="index.php">Return to homepage</a>';
echo $foot;
?>