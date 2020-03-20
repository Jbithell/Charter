<?php
$title = 'Vote';
require_once 'nav.php';
require_once 'roman.php';
$conn = new mysqli($db_hostname, $db_username, $db_password, $db_database);
// Check connection
if ($conn->connect_error) {
     die("Connection failed");
} 
if (isset($_GET['vote'])) {
	$sql = 'SELECT * FROM votes WHERE amendmentid=' . mysqli_real_escape_string($conn, $_GET['amendmentid']) . ' AND userid=' . mysqli_real_escape_string($conn, $charteruserid);
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
			$sql = "UPDATE votes SET vote=" . mysqli_real_escape_string($conn, $_GET['vote']) . ' WHERE amendmentid=' . mysqli_real_escape_string($conn, $_GET['amendmentid']) . ' AND userid=' . mysqli_real_escape_string($conn, $charteruserid);
			$result = $conn->query($sql);
	}
	else {
		$sql = 'INSERT INTO votes (`amendmentid`, `userid`, `voteid`, `vote`) VALUES (' . mysqli_real_escape_string($conn, $_GET['amendmentid']) . ',' . mysqli_real_escape_string($conn, $charteruserid) . ',NULL,' . mysqli_real_escape_string($conn, $_GET['vote']) . ')';
		$result = $conn->query($sql);
	}
	$voted = true;
} else $voted = false;

$sql = 'SELECT * FROM amendments LEFT JOIN users ON `amendments`.`tabledby`=`users`.`charteruserid` LEFT JOIN clauses ON `amendments`.`clauseid`=`clauses`.`id`  WHERE `amendments`.amendmentid=' . mysqli_real_escape_string($conn, $_GET['amendmentid']). ' AND `amendments`.`status` != 4';
$result = $conn->query($sql);

if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		echo $row['forename'] . ' ' . $row['surname'] . ' would like to ';
		if ($row["type"] == 1) echo 'change the wording of clause ' . $row['number'] . ' ' . strtolower(roman($row["subclause"])) . ' to ' . $row['textto'] . '. It currently reads ' . $row['textfrom'] . '.';
		 elseif ($row["type"] == 2) echo 'would like to strike clause ' . $row['number'] . ' ' . strtolower(roman($row["subclause"])) . ' which currently reads ' . $row['text'];
		 elseif ($row["type"] == 3) echo 'would like to add a new amendment which will read ' . $row['textto'];
		 else echo 'Error';
		 if ($voted) echo '<br/>Thanks for voting - Would you like to change your vote or return to the <a href="index.php">homepage</a>?';
		 else echo '<br/>How would you like to vote?';
		 
		$sql = "SELECT * FROM votes WHERE userid=" . $charteruserid . ' AND amendmentid=' . mysqli_real_escape_string($conn, $_GET['amendmentid']);
		$result = $conn->query($sql);
		echo '<form action="vote.php" method="GET"><div class="input-group text-center">
              	 <select name="vote" class="form-control input-lg">';
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				 echo '<option value="1"';
				  if ($row['vote'] == 1) echo ' selected ';
				  echo '>Agree and Support Amendment</option>
				  <option value="2"';
				  if ($row['vote'] == 2) echo ' selected ';
				  echo '>Disagree and Do Not Support Amendment</option>
				  <option value="3"';
				  if ($row['vote'] == 3) echo ' selected ';
				  echo '>Abstain</option></select><input type="hidden" name="voteid" value="' . $row['voteid'] . '" />';
				 
			 }
		} else {
			echo '<option value="1">Agree and Support Amendment</option>
				  <option value="2">Disagree and Do Not Support Amendment</option>
				  <option value="3" selected>Abstain</option></select>';
		} echo '<input type="hidden" name="amendmentid" value="' . $_GET['amendmentid'] . '" />
                <span class="input-group-btn"><button class="btn btn-lg btn-primary" type="submit">Vote</button></span>
              </div></form>';
	 }
} else die('Sorry - Amendment not found. Please visit <a href="index.php">homepage</a>');
echo $foot;
?>