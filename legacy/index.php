<?php 
require_once 'auth.php';
require_once 'votes.php';
require_once 'votes_japes.php';
date_default_timezone_set("Europe/London");
?>
<?php

// Create connection
$conn = new mysqli($db_hostname, $db_username, $db_password, $db_database);
// Check connection
if ($conn->connect_error) {
     die("Connection failed");
} 

$sql = "SELECT * FROM clauses ORDER BY number ASC, subclause ASC";
$result = $conn->query($sql);
$previousnumber = 0;
if ($result->num_rows > 0) {
	require_once 'roman.php';
	echo '<table border="1">';
     while($row = $result->fetch_assoc()) {
         echo "<tr><td>";
		 if ($row["number"] != $previousnumber) echo $row["number"];
		 $previousnumber = $row["number"];
		 echo "</td><td>";
		 echo strtolower(roman($row["subclause"]) . '.');
		 echo "</td><td>";
		 echo $row["text"];
		 echo "</td>";
		 echo "<td>";
		 echo '<a href="amendform.php?clauseid=' . $row["id"] . '">Amend</a>';
		 echo '</td><td>';
		 echo '<a href="strike.php?clauseid=' . $row["id"] . '">Strike</a>';
		 echo "</td></tr>";
     }
     echo "</table>";
} else {
     echo "Error";
}
?>
<a href="addform.php">Add new Clause</a>
<center><h2>Appendix</h2></center>
<h4>Acceptable Japes</h4>
<?php
$sql = "SELECT * FROM acceptablejapes";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	echo '<table border="1"><tr><th>Number.</th><th>Refers to</th><th>Jape</th></tr>';
	$japecounter = 1;
	while($row = $result->fetch_assoc()) {
		echo "<tr><td>" . $japecounter . "</td><td>" . $row['refersto'] . "</td><td>" . $row['text'] .'</td><td><a href="amendform_japes.php?japeid=' . $row["id"] . '">Amend</a> <a href="strike_japes.php?japeid=' . $row["id"] . '">Strike</a></td></tr>';
		$japecounter ++;
	}
		echo "</table>";
} else {
	echo '<tr><td colspan="4"><center><i>No acceptable Japes</i></center></td></tr></table>';
}
?>
<a href="addform_japes.php">Add new Jape</a>

<h3>Jape Amendments</h3>
<?php
$sql = "SELECT * FROM amendmentsjapes LEFT JOIN users " . "ON `amendmentsjapes`.`tabledby`=`users`.`charteruserid` WHERE `amendmentsjapes`.`tabledby` = " . $charteruserid . " OR `amendmentsjapes`.`status` != 4 ORDER BY `amendmentsjapes`.`created` DESC ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
	require_once 'roman.php';
	echo '<table border="1">';
	echo '<tr><th>Tabled By</th><th>Vote Time Remaining</th><th>Comment</th><th>Type</th><th>Change Wording From</th><th>Change Wording To</th><th>For/Against/Total Voted</th></tr>';
	while($row = $result->fetch_assoc()) {
		echo "<tr ";
		 if ($row["status"] == 4) echo 'bgcolor="#D8D8D8"';
		 elseif ($row["status"] == 1) echo 'bgcolor="#FFC266"';
		 elseif ($row["status"] == 2) echo 'bgcolor="#C0F2C0"';
		 elseif ($row["status"] == 3) echo 'bgcolor="#FF3333"';
		 echo ">";
		 echo "<td>";
		 echo $row["forename"] . ' ' . $row["surname"];
		 echo "</td>";
		 echo "<td>";
		 $votesql = "SELECT * FROM votesjapes WHERE amendmentid='" . $row["amendmentid"] . "' AND userid='" . $charteruserid . "'";
		 $voteresult = $conn->query($votesql);
		 if ($voteresult->num_rows > 0 and $row["status"] == 1) echo '&#10004;';
		 elseif ($row["status"] == 1) echo '<div id="amendementcountdown' . $row["amendmentid"] . '"></div><script>
					var timestamp' . convert_number_to_words($row["amendmentid"]) . ' = ' . (strtotime('+24 hours', strtotime($row["created"])) - time()) . ';
					function component(x, v) {
						return Math.floor(x / v);
					}
					setInterval(function() {
						timestamp' . convert_number_to_words($row["amendmentid"]) . '--;
						var days' . convert_number_to_words($row["amendmentid"]) . '    = component(timestamp' . convert_number_to_words($row["amendmentid"]) . ', 24 * 60 * 60),
							hours' . convert_number_to_words($row["amendmentid"]) . '   = component(timestamp' . convert_number_to_words($row["amendmentid"]) . ',      60 * 60) % 24,
							minutes' . convert_number_to_words($row["amendmentid"]) . ' = component(timestamp' . convert_number_to_words($row["amendmentid"]) . ',           60) % 60,
							seconds' . convert_number_to_words($row["amendmentid"]) . ' = component(timestamp' . convert_number_to_words($row["amendmentid"]) . ',            1) % 60;
						amendementcountdown' . $row["amendmentid"] . '.innerHTML = (hours' . convert_number_to_words($row["amendmentid"]) . ' + " hours, " + minutes' . convert_number_to_words($row["amendmentid"]) . ' + " minutes and " + seconds' . convert_number_to_words($row["amendmentid"]) . ' + " seconds remaining to vote");
					}, 1000);
					</script>';
		 else echo 'Voting Closed';
		 echo "</td>";
		 echo "<td>";
		 echo $row["comment"];
		 echo "</td>";
		 echo "<td>";
		 if ($row["type"] == 1) echo 'Wording Change';
		 elseif ($row["type"] == 2) echo 'Strike';
		 elseif ($row["type"] == 3) echo 'New Amendment';
		 else echo 'Other';
		 echo "</td>";
		 echo "<td>";
		 echo $row["textfrom"];
		 echo "</td>";
		 echo "<td>";
		 if ($row["type"] == 2) {
			 $strikesql = "SELECT text, refersto FROM acceptablejapes WHERE id='" . $row["japeid"] . "'";
			 $strikeresult = $conn->query($strikesql);
			 $strikeresult=mysqli_fetch_row($strikeresult);
			 echo $strikeresult[0] . ' (Refers to: ' . $strikeresult[1] . ')';
		 }
		 else echo $row["textto"];
		 echo "</td>";
		 echo "<td>";
		 $votes = votes_japes($row["amendmentid"]);
		 echo $votes[1] . '/' . $votes[2] . '/' . $votes[0];
		 echo "</td>";
		 if ($row["status"] == 1) {
			 echo "<td>";
			 echo '<a href="vote_japes.php?amendmentid=' . $row["amendmentid"] . '">Vote</a>';
			if ($row["tabledby"] == $charteruserid and ($row["status"] != 4)) echo '<i> or </i><a href="withdraw_japes.php?amendmentid=' . $row["amendmentid"] . '">Withdraw</a>';
			echo "</td>";
		 }
		 echo "</tr>";
     }
     echo "</table>";
} else {
     echo "<center><i>Currently No Amendments</a></center>";
}
?>  
<h4>Dispute resolution parties</h4>
1.	Samir Hutchings<br/>
2.	Jeffery Gan

<h3>General Charter Amendments</h3>
<?php

$sql = "SELECT * FROM amendments LEFT JOIN users " . "ON `amendments`.`tabledby`=`users`.`charteruserid` WHERE `amendments`.`tabledby` = " . $charteruserid . " OR `amendments`.`status` != 4 ORDER BY `amendments`.`created` DESC ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
	require_once 'roman.php';
	echo '<table border="1">';
	echo '<tr><th>Tabled By</th><th>Vote Time Remaining</th><th>Comment</th><th>Type</th><th>Change Wording From</th><th>Change Wording To</th><th>For/Against/Total Voted</th></tr>';
     while($row = $result->fetch_assoc()) {
         echo "<tr ";
		 if ($row["status"] == 4) echo 'bgcolor="#D8D8D8"';
		 elseif ($row["status"] == 1) echo 'bgcolor="#FFC266"';
		 elseif ($row["status"] == 2) echo 'bgcolor="#C0F2C0"';
		 elseif ($row["status"] == 3) echo 'bgcolor="#FF3333"';
		 echo ">";
		 echo "<td>";
		 echo $row["forename"] . ' ' . $row["surname"];
		 echo "</td>";
		 echo "<td>";
		 $votesql = "SELECT * FROM votes WHERE amendmentid='" . $row["amendmentid"] . "' AND userid='" . $charteruserid . "'";
		 $voteresult = $conn->query($votesql);
		 if ($voteresult->num_rows > 0) echo '&#10004;';
		 elseif ($row["status"] == 1) echo '<div id="amendementcountdown' . $row["amendmentid"] . '"></div><script>
					var timestamp' . convert_number_to_words($row["amendmentid"]) . ' = ' . (strtotime('+24 hours', strtotime($row["created"])) - time()) . ';
					function component(x, v) {
						return Math.floor(x / v);
					}
					setInterval(function() {
						timestamp' . convert_number_to_words($row["amendmentid"]) . '--;
						var days' . convert_number_to_words($row["amendmentid"]) . '    = component(timestamp' . convert_number_to_words($row["amendmentid"]) . ', 24 * 60 * 60),
							hours' . convert_number_to_words($row["amendmentid"]) . '   = component(timestamp' . convert_number_to_words($row["amendmentid"]) . ',      60 * 60) % 24,
							minutes' . convert_number_to_words($row["amendmentid"]) . ' = component(timestamp' . convert_number_to_words($row["amendmentid"]) . ',           60) % 60,
							seconds' . convert_number_to_words($row["amendmentid"]) . ' = component(timestamp' . convert_number_to_words($row["amendmentid"]) . ',            1) % 60;
						amendementcountdown' . $row["amendmentid"] . '.innerHTML = (hours' . convert_number_to_words($row["amendmentid"]) . ' + " hours, " + minutes' . convert_number_to_words($row["amendmentid"]) . ' + " minutes and " + seconds' . convert_number_to_words($row["amendmentid"]) . ' + " seconds remaining to vote");
					}, 1000);
					</script>';
		else echo 'Voting Closed';
		 echo "</td>";
		 echo "<td>";
		 echo $row["comment"];
		 echo "</td>";
		 echo "<td>";
		 if ($row["type"] == 1) echo 'Wording Change';
		 elseif ($row["type"] == 2) echo 'Strike';
		 elseif ($row["type"] == 3) echo 'New Amendment';
		 else echo 'Other';
		 echo "</td>";
		 echo "<td>";
		 echo $row["textfrom"];
		 echo "</td>";
		 echo "<td>";
		 if ($row["type"] == 2) {
			 $strikesql = "SELECT text FROM clauses WHERE id='" . $row["clauseid"] . "'";
			 $strikeresult = $conn->query($strikesql);
			 $strikeresult=mysqli_fetch_row($strikeresult);
			 echo $strikeresult[0];
		 }
		 else echo $row["textto"];
		 echo "</td>";
		 echo "<td>";
		 $votes = votes($row["amendmentid"]);
		 echo $votes[1] . '/' . $votes[2] . '/' . $votes[0];
		 echo "</td>";
		 if ($row["status"] == 1) {
			echo "<td>";
			 echo '<a href="vote.php?amendmentid=' . $row["amendmentid"] . '">Vote</a>';
			if ($row["tabledby"] == $charteruserid and ($row["status"] != 4)) echo '<i> or </i><a href="withdraw.php?amendmentid=' . $row["amendmentid"] . '">Withdraw</a>';
			echo "</td>";
		 }
		 echo "</tr>";
     }
     echo "</table>";
} else {
     echo "<center><i>Currently No Amendments</a></center>";
}
?>  
<center><h2>Signatories</h2></center>
<style>
table { table-layout: fixed; }
</style>
<?php
$sql = "SELECT * FROM users WHERE signatory = 1";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
$tdwidth = (100/$result->num_rows);
echo "<table style=\"width: 100%;\">";
$row1 = '<tr style="width: 100%;">';
$row2 = '<tr style="width: 100%;">';
// output data of each row
while($row = $result->fetch_assoc()) {
	$row1 .= '<td style="width: ' . $tdwidth . '%;"><center>';
	if ($row['signature'] == '') $row1 .=  $row['forename'];
	else $row1 .=  '<img style="max-height: 100px;" src="../' . $row['signature'] . '"/>';
	$row1 .= '</center></td>';
	$row2 .= '<td style="width: ' . $tdwidth . '%;"><center>' . $row['forename'] . ' ' . $row['surname'] . '</center></td>';
}
$row1 .= '</tr>';
$row2 .= '</tr>';
echo $row1 . $row2 . "</table>";
} else {
echo "<center><i>No Signatories</i></center>";
}

$sql = "SELECT * FROM users WHERE signatory != 1";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
echo '<center><h3>Non-Signatories</h3><i>Users of the system who can vote and sign the charter, but have chosen not to.</i></center>';
echo "<ol>";
// output data of each row
while($row = $result->fetch_assoc()) {
	echo '<li>' . $row['forename'] . ' ' . $row['surname'] . '</li>';
}
echo "</ol>";
}
echo '<center><h3>My Signature</h3></center>';
echo '<center><form action="signcharter.php" method="POST"><input type="checkbox" ';
if ($signatory == 1) echo 'checked';
echo ' name="iagree" value="True"> I ' . $forename . ' ' . $surname . ' agree to the terms of the charter as outlined above.';
echo '     <input type="hidden" name="charteruserid" value="' . $charteruserid . '" /><input type="submit" value="Save"></form></center>';
?>
<hr/>
<i>Users can upload images to server as their signature, in place of their first name. These are displayed to all users. Uploading an image using the system below does not affect wheather or not you are a signatory.</i>
<form enctype="multipart/form-data" action="signature.php" method="post"><input name="signature" type="file" accept="image/*" /><input type="submit" value="Submit" /></form>
<?php
echo $foot;
?>