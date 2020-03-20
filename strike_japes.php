<?php
$title = 'Strike a Jape';
require_once 'nav.php';
if ((!isset($_GET['japeid'])) and (!isset($_POST['japeid']))) die('Please return to <a href="index.php">homepage</a> and click on a clause to strike');
elseif (!isset($_POST['comment'])) die('<form method="post" class="form">
<div class="input-group text-center">
               <input type="text" placeholder="Comment" name="comment" class="form-control input-lg" />
                <span class="input-group-btn"><button class="btn btn-lg btn-primary" type="submit" type="button">Confirm</button></span>
              </div><input type="hidden" name="japeid" value="' . $_GET['japeid'] . '" /></form>');
// Create connection
$conn = new mysqli($db_hostname, $db_username, $db_password, $db_database);
// Check connection
if ($conn->connect_error) {
     die("Connection failed");
} 
date_default_timezone_set("Europe/London");
$sql = 'SELECT * FROM `amendmentsjapes` WHERE `tabledby`=' . $charteruserid . ' AND `created` > ' . "'" . date("Y-m-d H:i:s", strtotime('-4 hours')) . "'";
$result = $conn->query($sql);
if ($result->num_rows != 0) echo 'You can only submit one amendment every four hours!';
else {
$sql = "INSERT INTO `amendmentsjapes` (`amendmentid`, `japeid`, `textfrom`, `textto`,`referstofrom`, `referstoto`, `comment`, `tabledby`, `created`, `type`, `status`) VALUES (NULL, '" . mysqli_real_escape_string($conn, $_POST['japeid']) . "', NULL, NULL, NULL, NULL, '" . mysqli_real_escape_string($conn, $_POST['comment']) . "', '" . mysqli_real_escape_string($conn, $charteruserid) . "', '" . date("Y-m-d H:i:s") . "', 2, 1)";
$result = $conn->query($sql);
$amendmentid = $conn->insert_id;

//Notify Users
require_once 'notify.php';
notifyall($forename . ' ' . $surname . ' has tabled an amendment to strike a jape. You have 24 hours to vote!','vote_japes.php?amendmentid=' . $amendmentid);
echo 'Success - Your amendment has been tabled and the users have been notified! Your amendment id is: ' . $amendmentid . ' please <a href="vote_japes.php?amendmentid=' . $amendmentid . '"> vote now</a>';
}
echo $foot;
?>