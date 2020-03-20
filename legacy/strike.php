<?php
require_once 'auth.php';
if ((!isset($_GET['clauseid'])) and (!isset($_POST['clauseid']))) die('Please return to <a href="index.php">homepage</a> and click on a clause to strike');
elseif (!isset($_POST['comment'])) die('<form method="post">Comment: <input type="text" placeholder="comment" name="comment" /><input type="hidden" name="clauseid" value="' . $_GET['clauseid'] . '" /><input type="submit" value="Confirm" /></form>');
// Create connection
$conn = new mysqli($db_hostname, $db_username, $db_password, $db_database);
// Check connection
if ($conn->connect_error) {
     die("Connection failed");
} 
date_default_timezone_set("Europe/London");
$sql = 'SELECT * FROM `amendments` WHERE `tabledby`=' . $charteruserid . ' AND `created` > ' . "'" . date("Y-m-d H:i:s", strtotime('-4 hours')) . "'";
$result = $conn->query($sql);
if ($result->num_rows != 0) echo 'You can only submit one amendment every four hours!';
else {
$sql = "INSERT INTO `amendments` (`amendmentid`, `clauseid`, `textfrom`, `textto`, `comment`, `tabledby`, `created`, `type`, `status`) VALUES (NULL, '" . mysqli_real_escape_string($conn, $_POST['clauseid']) . "', NULL, NULL, '" . mysqli_real_escape_string($conn, $_POST['comment']) . "', '" . mysqli_real_escape_string($conn, $charteruserid) . "', '" . date("Y-m-d H:i:s") . "', 2, 1)";
$result = $conn->query($sql);
$amendmentid = $conn->insert_id;

//Notify Users
require_once 'notify.php';
notifyall($forename . ' ' . $surname . ' has tabled an amendment to strike a clause. You have 24 hours to vote!','vote.php?amendmentid=' . $amendmentid);
echo 'Success - Your amendment has been tabled and the users have been notified! Your amendment id is: ' . $amendmentid . ' please <a href="vote.php?amendmentid=' . $amendmentid . '"> vote now</a>';
}
?>