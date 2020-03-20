<?php
require_once 'dblogins.php';
date_default_timezone_set("Europe/London");
function votes($amendmentid) {
	//When called function will return votes for an amendment
	global $db_hostname, $db_username, $db_password, $db_database;
	$conn = new mysqli($db_hostname, $db_username, $db_password, $db_database);
	if ($conn->connect_error) {
		die("Connection failed, please contact support");
	}
	$sql = "SELECT `vote` FROM `votes` WHERE `amendmentid`=" . $amendmentid;
	$result = $conn->query($sql);
	$total = 0;
	$yes = 0;
	$no = 0;
	$abstain = 0;
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			if ($row["vote"] == 1) $yes += 1;
			elseif  ($row["vote"] == 2) $no += 1;
			elseif  ($row["vote"] == 3) $abstain += 1;
			else $other += 1;
			$total += 1;
		}
	} else {
		$total = 0;
		$yes = 0;
		$no = 0;
		$abstain = 0;
	}
	$sql = "SELECT `signatory` FROM users";//WHERE `signatory`=1
	$result = $conn->query($sql);
	$eligible = $result->num_rows;
	$conn->close();
	return array($total, $yes, $no, $abstain, $eligible);
}
function decide($amendmentid) {
	global $db_hostname, $db_username, $db_password, $db_database;
	$conn = new mysqli($db_hostname, $db_username, $db_password, $db_database);
	if ($conn->connect_error) {
		die("Connection failed, please contact support");
	}
	$votes = votes($amendmentid);
	if ($votes[1] >= ((($votes[1]+$votes[2])/4)*3)) $newstatus = 2;
	else $newstatus = 3;
	
	$sql = "UPDATE `amendments` SET `status` = '" . $newstatus . "' WHERE `amendmentid`=" . $amendmentid;
	$result = $conn->query($sql);
	if ($newstatus == 2) {
		//The Amendment has passed - We need to update the charter
		$sql = "SELECT * FROM `amendments` WHERE `amendmentid`=" . $amendmentid;
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				if ($row["type"] == 1) {
					//Amendment is a wording change
					$sql = "UPDATE `clauses` SET `text` = '" . $row["textto"] . "' WHERE `id` = " .  $row['clauseid'];
					$result = $conn->query($sql);
					//Amendment Passed Successfully!
				}
				elseif  ($row["type"] == 2) {
					//Strike
					//Amendment is a wording change
					$sql = "SELECT subclause FROM clauses WHERE `id` = " .  $row['clauseid'];
					$result = $conn->query($sql);
					$subclause = $result->fetch_assoc();
					$subclause = $subclause['subclause'];
					
					$sql = "DELETE FROM clauses WHERE `id` = " .  $row['clauseid'];
					$result = $conn->query($sql);
					
					$sql = "UPDATE `clauses` SET `subclause`=`subclause`-1 WHERE `subclause` > " . $subclause;
					$result = $conn->query($sql);
					//Amendment Passed Successfully!
				}
				elseif  ($row["type"] == 3) {
					$sql = "SELECT MAX(`number`) FROM clauses";
					$result = $conn->query($sql);
					$number = $result->fetch_assoc();
					$number = $number['MAX(`number`)'];
					$sql = "INSERT INTO `clauses` (`id`, `number`, `subclause`, `text`) VALUES (NULL, '" . $number+1 . "', '1', '" . $row["textto"] . "');";
					$result = $conn->query($sql);
					//New Amendment Passed Successfully!
				}
				else {
					//Must be an other
				}
			}
		} 
	}
}
function decidethatneeddeciding() {
	global $db_hostname, $db_username, $db_password, $db_database;
	$conn = new mysqli($db_hostname, $db_username, $db_password, $db_database);
	if ($conn->connect_error) {
		die("Connection failed, please contact support");
	}
	$sql = "SELECT * FROM `amendments` WHERE `created` <= '" . date("Y-m-d H:i:s", strtotime('-24 hours')) . "' AND `status`=1";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			decide($row['amendmentid']);
		}
	}
	$sql = "SELECT * FROM `amendments` WHERE `created` >= '" . date("Y-m-d H:i:s", strtotime('-24 hours')) . "' AND `status`=1";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			//If everybody has voted
			if (votes($row['amendmentid'])[0] == votes($row['amendmentid'])[4]) decide($row['amendmentid']);
		}
	}
	
	
}
decidethatneeddeciding();
if (isset($_GET['dev'])) decide($_GET['dev']);
?>