<?php 
require_once 'auth.php';
if (!isset($_POST['add'])) die('Please return to <a href="index.php">homepage</a> and click on a clause to ammend');
// Create connection
$conn = new mysqli($db_hostname, $db_username, $db_password, $db_database);
// Check connection
if ($conn->connect_error) {
     die("Connection failed");
} 

date_default_timezone_set("Europe/London");
$sql = "INSERT INTO `amendmentsjapes` (`amendmentid`, `japeid`, `textfrom`, `textto`,`referstofrom`, `referstoto`, `comment`, `tabledby`, `created`, `type`, `status`) VALUES (NULL, NULL, NULL, '" . mysqli_real_escape_string($conn, $_POST['text']) . "', NULL, '" . mysqli_real_escape_string($conn, $_POST['refersto']) . "', '" . mysqli_real_escape_string($conn, $_POST['comment']) . "', '" . mysqli_real_escape_string($conn, $charteruserid) . "', '" . date("Y-m-d H:i:s") . "', 3, 1)";
$result = $conn->query($sql);
$amendmentid = $conn->insert_id;

//Notify Users
require_once 'notify.php';
notifyall($forename . ' ' . $surname . ' has tabled an amendment to add a new jape. You have 24 hours to vote!','vote_japes.php?amendmentid=' . $amendmentid);
echo 'Success - Your amendment has been tabled and the users have been notified! Your amendment id is: ' . $amendmentid . ' please <a href="vote_japes.php?amendmentid=' . $amendmentid . '"> vote now</a>';
?>
