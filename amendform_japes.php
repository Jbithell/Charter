<?php 
$title = 'Amend a Jape';
require_once 'nav.php';
if (!isset($_GET['japeid'])) die('Please return to <a href="index.php">homepage</a> and click on a clause to ammend');
?>
<?php

// Create connection
$conn = new mysqli($db_hostname, $db_username, $db_password, $db_database);
// Check connection
if ($conn->connect_error) {
     die("Connection failed");
} 
$sql = 'SELECT * FROM `amendmentsjapes` WHERE `tabledby`=' . $charteruserid . ' AND `created` > ' . "'" . date("Y-m-d H:i:s", strtotime('-4 hours')) . "'";
//$result = $conn->query($sql);
if ($result->num_rows != 0) echo 'You can only submit one amendment every four hours!';
else {
$sql = "SELECT * FROM acceptablejapes WHERE id='" . mysqli_real_escape_string($conn, $_GET['japeid']) . "'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
	require_once 'roman.php';
	echo '<form action="amend_japes.php" method="post"><table border="0" style="width: 100%;">';
     while($row = $result->fetch_assoc()) {
		 echo "<tr><td><b>Jape ID:</b></td><td>";
		 echo $row["id"];
		 echo "</td></tr>";
		 echo '<tr><td><b>Current Text:</b><input type="hidden" name="previous" value="' . $row["text"] . '" /></td><td>';
		 echo $row["text"];
		 echo "</td></tr>";
		 echo '<tr style="width: 100%;"><td><b>Amended Text:</b></td><td style="width: 100%;"><textarea name="text" style="width: 100%; height: 300px">';
		 echo $row["text"];
		 echo '</textarea></tr>';
		 echo '<tr><td><b>Currently Refers To:</b><input type="hidden" name="referstofrom" value="' . $row["refersto"] . '" /></td><td>';
		 echo $row["refersto"];
		 echo "</td></tr>";
		 echo '<tr style="width: 100%;"><td><b>Amended Refers To:</b></td><td style="width: 100%;"><textarea name="referstoto" style="width: 100%; height: 300px">';
		 echo $row["refersto"];
		 echo '</textarea></tr>';
		 echo '<tr style="width: 100%;"><td><b>Amendment Comment:</b></td><td style="width: 100%;"><textarea name="comment" style="width: 100%; height: 100px"></textarea></tr>';
		 echo '<tr><td colspan="2" align="right"><input type="submit" value="Table Amendment" /></td></tr>';
		 echo '<tr><td colspan="2"><i>Tabling an amendment will notify all signatories that they have 24 hours to vote immediately. Amendments can be withdrawn. You have not automatically voted for your own amendment - You must vote separately. </i></td></tr>';
     }
     echo '</table><input type="hidden" name="amend" value="true" /><input type="hidden" name="japeid" value="' . $_GET['japeid'] . '" /></form>';
} else {
     echo "Error - Please try again later <a href=\"index.php\">homepage</a>";
}

}
$conn->close();
echo $foot;
?>  