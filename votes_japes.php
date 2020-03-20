<?php
require_once 'dblogins.php';
date_default_timezone_set("Europe/London");
$conn = new mysqli($db_hostname, $db_username, $db_password, $db_database);
if ($conn->connect_error) {
	die("Connection failed, please contact support");
}
function votes_japes($amendmentid) {
	//When called function will return votes for an amendment
	global $conn;
	$sql = "SELECT `vote` FROM `votesjapes` WHERE `amendmentid`=" . $amendmentid;
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
	return array($total, $yes, $no, $abstain, $eligible);
}
function decide_japes($amendmentid) {
	global $conn;
	$votes = votes_japes($amendmentid);
	if (($votes[2] <= 0) and ($votes[1] >= 1)) $newstatus = 2; //Prevent division by 0 error
	elseif (($votes[1]/$votes[2]) >= (2/3)) $newstatus = 2;
	else $newstatus = 3;
	
	$sql = "UPDATE `amendmentsjapes` SET `status` = '" . $newstatus . "' WHERE `amendmentid`=" . $amendmentid;
	$result = $conn->query($sql);
	if ($newstatus == 2) {
		//The Amendment has passed - We need to update the charter
		$sql = "SELECT * FROM `amendmentsjapes` WHERE `amendmentid`=" . $amendmentid;
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				if ($row["type"] == 1) {
					//Amendment is a wording change
					$updatesql = "UPDATE `acceptablejapes` SET `text` = '" . $row["textto"] . "' WHERE `id` = " .  $row['japeid'];
					$updateresult = $conn->query($updatesql);
					//Amendment Passed Successfully!
				}
				elseif ($row["type"] == 2) {
					//Strike
					//Amendment is a wording change
					$updatesql = "DELETE FROM acceptablejapes WHERE `id` = " .  $row['japeid'];
					$updateresult = $conn->query($updatesql);
					//Amendment Passed Successfully!
				}
				elseif ($row["type"] == 3) {
					$updatesql = "INSERT INTO `acceptablejapes` (`id`, `text`, `refersto`) VALUES (NULL, '" . $row["textto"] . "', '" . $row["referstoto"] . "');";
					die($updatesql);
					$updateresult = $conn->query($updatesql);
					//New Amendment Passed Successfully!
				}
				else {
					//Must be an other
				}
			}
		} 
	}
}
function decidethatneeddeciding_japes() {
	global $conn;
	$sql = "SELECT * FROM `amendmentsjapes` WHERE `created` <= '" . date("Y-m-d H:i:s", strtotime('-24 hours')) . "' AND `status`=1";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			decide_japes($row['amendmentid']);
		}
	}
	$sql = "SELECT * FROM `amendmentsjapes` WHERE `created` >= '" . date("Y-m-d H:i:s", strtotime('-24 hours')) . "' AND `status`=1";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			//If everybody has voted
			if (votes_japes($row['amendmentid'])[0] == votes_japes($row['amendmentid'])[4]) decide_japes($row['amendmentid']);
		}
	}
	
	
}
decidethatneeddeciding_japes();
?>