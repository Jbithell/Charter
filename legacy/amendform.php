<?php 
require_once 'auth.php';
if (!isset($_GET['clauseid'])) die('Please return to <a href="index.php">homepage</a> and click on a clause to ammend');
?>
<?php
// Create connection
$conn = new mysqli($db_hostname, $db_username, $db_password, $db_database);
// Check connection
if ($conn->connect_error) {
     die("Connection failed");
} 
$sql = 'SELECT * FROM `amendments` WHERE `tabledby`=' . $charteruserid . ' AND `created` > ' . "'" . date("Y-m-d H:i:s", strtotime('-4 hours')) . "'";
$result = $conn->query($sql);
if ($result->num_rows != 0) echo 'You can only submit one amendment every four hours!';
else {
$sql = "SELECT * FROM clauses WHERE id='" . mysqli_real_escape_string($conn, $_GET['clauseid']) . "'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
	require_once 'roman.php';
	echo '<form action="amend.php" method="post"><table border="1" style="width: 100%;">';
     while($row = $result->fetch_assoc()) {
		 echo "<tr><td><b>Clause Number:</b></td><td>";
		 echo $row["number"];
		 echo "</td></tr>";
		 echo "<tr><td><b>Sub Clause:</b></td><td>";
		 echo strtolower(roman($row["subclause"]) . '.');
		 echo '<tr><td><b>Current Text:</b><input type="hidden" name="previous" value="' . $row["text"] . '" /></td><td>';
		 echo $row["text"];
		 echo "</td></tr>";
		 echo '<tr style="width: 100%;"><td><b>Amended Text:</b></td><td style="width: 100%;"><textarea name="text" style="width: 100%; height: 300px">';
		 echo $row["text"];
		 echo '</textarea></tr>';
		 echo '<tr style="width: 100%;"><td><b>Amendment Comment:</b></td><td style="width: 100%;"><textarea name="comment" style="width: 100%; height: 100px"></textarea></tr>';
		 echo '<tr><td colspan="2" align="right"><input type="submit" value="Table Amendment" /></td></tr>';
		 echo '<tr><td colspan="2"><i>Tabling an amendment will notify all signatories that they have 24 hours to vote immediately. Amendments can be withdrawn. You have not automatically voted for your own amendment - You must vote separately. </i></td></tr>';
     }
     echo '</table><input type="hidden" name="amend" value="true" /><input type="hidden" name="clauseid" value="' . $_GET['clauseid'] . '" /></form>';
} else {
     echo "Error - Please try again later <a href=\"index.php\">homepage</a>";
}

}
$conn->close();
?>  