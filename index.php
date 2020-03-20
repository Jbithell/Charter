<?php 

require_once 'auth.php';
$littlepage = false;
require_once 'nav.php';
require_once 'votes.php';
require_once 'votes_japes.php';
date_default_timezone_set("Europe/London");
// Create connection
$conn = new mysqli($db_hostname, $db_username, $db_password, $db_database);
// Check connection
if ($conn->connect_error) {
     die("Connection failed");
} 
if(!isset($_GET['appendix']) and !isset($_GET['signatories'])) {
/*echo '<script>
$(document).ready(function(){
	bootbox.alert("Welcome to the new look charter ' . $forename . ' - I hope you like it!<br/><br/>The new charter is designed to be mobile friendly, faster, easier to use and more responsive.<br/><ul><li>Mobile freindly theme</li><li>Responsive design for cross platform viewing</li><li>Faster, thanks to advanced caching</li><li>Amendments are now grouped by type and hidden if not in voting procedure.</li></ul>You may wish to pin the charter to your home screen - to see the nice book icon!<br/>If you don' . "'" . 't like it you can visit the old version <a href=" + ' . "'" . '"legacy/"' . "'" . ' + ">here</a> (All changes are synced between the two versions!).<br/><b>N.B:</b> The terms of the charter have not changed.");
});
</script>';*/
echo '<div class="row">
          <div class="panel panel-default">
          <div class="panel-heading"><a href="addform.php" class="pull-right">Add new Clause</a><h4>Charter</h4></div>
   			<div class="panel-body">';
$sql = "SELECT * FROM clauses ORDER BY number ASC, subclause ASC";
$result = $conn->query($sql);
$previousnumber = 0;
if ($result->num_rows > 0) {
	require_once 'roman.php';
	echo '<style>
			td, th {
		  padding: 4px;
		}
		</style>';
	echo '<table border="0">';
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
		 echo '<a href="amendform.php?clauseid=' . $row["id"] . '"><i class="fa fa-pencil"></i></a>';
		 echo '</td><td>';
		 echo '<a href="strike.php?clauseid=' . $row["id"] . '"><i class="fa fa-strikethrough"></i></a>';
		 echo "</td></tr>";
     }
     echo "</table>";
} else {
     echo "Error";
}
echo '</div>
   		</div>
</div>';
$sql = "SELECT * FROM amendments LEFT JOIN users " . "ON `amendments`.`tabledby`=`users`.`charteruserid` WHERE `amendments`.`tabledby` = " . $charteruserid . " OR `amendments`.`status` != 4 ORDER BY `amendments`.`status`";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		require_once 'roman.php';
		echo '<div class="row"><div class="panel panel-default">
           <div class="panel-heading"><a id="showall" class="pull-right">View all</a><h4>Amendments</h4></div><div class="panel-body">';
		$hidden = false;
		$class = 'col-lg-6 col-xs-12';
		$counter = 0;
		while($row = $result->fetch_assoc()) {
			if ($row["status"] != 1 and !$hidden) {
				$hidden = true;
				$class = 'col-lg-2 col-xs-6';
				echo '<div id="moreamendments" style="display: none;">';
				echo '<script>
						$(document).ready(function(){
							$("#showall").click(function(){
								document.getElementById("moreamendments").style.display = "block";
								document.getElementById("showall").style.display = "none";
								';
								for ($x = 1; $x <= $counter; $x++) {
								   echo 'document.getElementById("current' . $x . '").className = "col-lg-2 col-xs-6";' . "\n";
								}
								echo '
							});
						});
						 </script>';
			} else $counter++;
			 echo '<div class="' . $class . '"' . ($hidden ? null : ' id="current' . $counter . '"') . '>
				<div class="panel panel-default">
				  <div class="panel-thumbnail">';
			 $votesql = "SELECT * FROM votes WHERE amendmentid='" . $row["amendmentid"] . "' AND userid='" . $charteruserid . "'";
			 $voteresult = $conn->query($votesql);
			 if ($row["status"] == 4) echo '<a class="btn btn-info center-block disabled" href="#">Withdrawn</a>';
			 elseif ($row["status"] == 1) {
				if ($row["tabledby"] == $charteruserid) echo '<a href="withdraw.php?amendmentid=' . $row["amendmentid"] . '" class="btn btn-primary center-block">Withdraw</a>';
				if ($voteresult->num_rows > 0) echo '<a href="vote.php?amendmentid=' . $row["amendmentid"] . '" class="btn btn-default center-block"">Voted</a>';
				else echo '<a href="vote.php?amendmentid=' . $row["amendmentid"] . '" class="btn btn-default center-block">Vote</a>';
			 } 
			 elseif ($row["status"] == 2) echo '<a class="btn btn-success center-block disabled" href="#">Passed</a>';
			 elseif ($row["status"] == 3) echo '<a class="btn btn-danger center-block disabled" href="#">Rejected</a>';
			 /*if ($row["status"] == 1) echo '<center><div id="amendementcountdown' . $row["amendmentid"] . '"></div><script>
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
						</script></center>';*/
			echo '</div><div class="panel-body">';
			echo '<p><b>Tabled By: </b>' . $row["forename"] . ' ' . $row["surname"] . '</p>';
			echo '<p><b>Comment: </b>' . $row["comment"] . '</p>';
			if ($row["type"] == 1) {
				echo '<p><b>Wording Change from: </b>' . $row["textfrom"] . '</p>';
				echo '<p><b>To: </b>' . $row["textto"] . '</p>';
			} elseif ($row["type"] == 2) {
				$strikesql = "SELECT text FROM clauses WHERE id='" . $row["clauseid"] . "'";
				$strikeresult = $conn->query($strikesql);
				$strikeresult=mysqli_fetch_row($strikeresult);
				echo '<p><b>Strike Clause which reads: </b>' . $strikeresult[0] . '</p>';
			} elseif ($row["type"] == 3) {
				echo '<p><b>New Amendment to read: </b>' . $row["textto"] . '</p>';
			}
			 $votes = votes($row["amendmentid"]);
			 echo '<p><span class="label label-success">' . $votes[1] . ' For</span> <span class="label label-danger">' . $votes[2] . ' Against</span> <span class="label label-info">' . $votes[0] . ' Voted</span></p>';
			 echo '</div></div></div>';
		 }
		 echo "</div></div></div></div>";
	}
} elseif(isset($_GET['appendix'])) {
$sql = "SELECT * FROM acceptablejapes";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	$japecounter = 1;
	echo '<div class="row"><div class="panel panel-default">
           <div class="panel-heading"><a href="addform_japes.php" class="pull-right">Add new</a> <h4>Acceptable Japes</h4></div>
   			<div class="panel-body">';
	while($row = $result->fetch_assoc()) {
		echo '<div class="col-lg-3"><div class="well well-sm">
                <div class="media">
                    <div class="media-body">
                        <h4 class="media-heading">' . $row['text'] . '</h4>
                		<p><span class="label label-primary">Jape Number ' . $japecounter . '</span> <span class="label label-default"> ' . $row['refersto'] . '</span></p>
                        <p>
                           <a href="amendform_japes.php?japeid=' . $row["id"] . '" class="btn btn-xs btn-default"><i class="fa fa-pencil"></i></a>
                           <a href="strike_japes.php?japeid=' . $row["id"] . '" class="btn btn-xs btn-default"><i class="fa fa-strikethrough"></i></a>
                        </p>
                    </div>
                </div>
               </div></div>';
		$japecounter ++;
	}
		echo '</div></div></div>';
}
$sql = "SELECT * FROM amendmentsjapes LEFT JOIN users " . "ON `amendmentsjapes`.`tabledby`=`users`.`charteruserid` WHERE `amendmentsjapes`.`tabledby` = " . $charteruserid . " OR `amendmentsjapes`.`status` != 4 ORDER BY `amendmentsjapes`.`status`";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
	require_once 'roman.php';
	echo '<div class="row"><div class="panel panel-default">
           <div class="panel-heading"><a id="showalljapes" class="pull-right">View all</a><h4>Jape Amendments</h4></div><div class="panel-body">';
	$hidden = false;
	$class = 'col-lg-6 col-xs-12';
	$counter = 0;
	while($row = $result->fetch_assoc()) {
		 if ($row["status"] != 1 and !$hidden) {
			$hidden = true;
			$class = 'col-lg-2 col-xs-6';
			echo '<div id="moreamendmentsjapes" style="display: none;">';
			echo '<script>
					$(document).ready(function(){
						$("#showalljapes").click(function(){
							document.getElementById("moreamendmentsjapes").style.display = "block";
							document.getElementById("showalljapes").style.display = "none";
							';
							for ($x = 1; $x <= $counter; $x++) {
							   echo 'document.getElementById("japecurrent' . $x . '").className = "col-lg-2 col-xs-6";' . "\n";
							}
							echo '
						});
					});
					 </script>';
		} else $counter++;
		 echo '<div class="' . $class . '"' . ($hidden ? null : ' id="japecurrent' . $counter . '"') . '>
			<div class="panel panel-default">
			  <div class="panel-thumbnail">';
		 $votesql = "SELECT * FROM votesjapes WHERE amendmentid='" . $row["amendmentid"] . "' AND userid='" . $charteruserid . "'";
		 $voteresult = $conn->query($votesql);
		 if ($row["status"] == 4) echo '<a class="btn btn-info center-block disabled" href="#">Withdrawn</a>';
		 elseif ($row["status"] == 1) {
			if ($row["tabledby"] == $charteruserid) echo '<a href="withdraw_japes.php?amendmentid=' . $row["amendmentid"] . '" class="btn btn-primary center-block">Withdraw</a>';
			if ($voteresult->num_rows > 0) echo '<a href="vote.php?amendmentid=' . $row["amendmentid"] . '" class="btn btn-default center-block"">Voted</a>';
			else echo '<a href="vote_japes.php?amendmentid=' . $row["amendmentid"] . '" class="btn btn-default center-block">Vote</a>';
		 } 
		 elseif ($row["status"] == 2) echo '<a class="btn btn-success center-block disabled" href="#">Passed</a>';
		 elseif ($row["status"] == 3) echo '<a class="btn btn-danger center-block disabled" href="#">Rejected</a>';
		 /*if ($row["status"] == 1) echo '<center><div id="japeamendementcountdown' . $row["amendmentid"] . '"></div><script>
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
						japeamendementcountdown' . $row["amendmentid"] . '.innerHTML = (hours' . convert_number_to_words($row["amendmentid"]) . ' + " hours, " + minutes' . convert_number_to_words($row["amendmentid"]) . ' + " minutes and " + seconds' . convert_number_to_words($row["amendmentid"]) . ' + " seconds remaining to vote");
					}, 1000);
					</script></center>';*/
		echo '</div><div class="panel-body">';
		echo '<p><b>Tabled By: </b>' . $row["forename"] . ' ' . $row["surname"] . '</p>';
		echo '<p><b>Comment: </b>' . $row["comment"] . '</p>';
		if ($row["type"] == 1) {
			echo '<p><b>Wording Change from: </b>' . $row["textfrom"] . '</p>';
			echo '<p><b>To: </b>' . $row["textto"] . '</p>';
		} elseif ($row["type"] == 2) {
			$strikesql = "SELECT text, refersto FROM acceptablejapes WHERE id='" . $row["japeid"] . "'";
			 $strikeresult = $conn->query($strikesql);
			 $strikeresult= mysqli_fetch_row($strikeresult);
			 echo '<p><b>Strike Jape which reads: </b>' . $strikeresult[0] . ' (Refers to: ' . $strikeresult[1] . ')</p>';
		} elseif ($row["type"] == 3) {
			echo '<p><b>New Jape: </b></p>
				<div class="well well-sm"><div class="media">
                    <div class="media-body">
                        <h4 class="media-heading">' . $row['textto'] . '</h4>
                		<p><span class="label label-default"> ' . $row['referstoto'] . '</span></p>
                        <p>
                           <a class="btn btn-xs btn-default disabled"><i class="fa fa-pencil"></i></a>
                           <a class="btn btn-xs btn-default disabled"><i class="fa fa-strikethrough"></i></a>
                        </p>
                    </div>
                </div></div>';
		}
		 $votes = votes_japes($row["amendmentid"]);
		 echo '<p><span class="label label-success">' . $votes[1] . ' For</span> <span class="label label-danger">' . $votes[2] . ' Against</span> <span class="label label-info">' . $votes[0] . ' Voted</span></p>';
		 echo '</div></div></div>';
     }
	 echo '</div></div></div></div>';
}
echo '<div class="row"><div class="panel panel-default">
<div class="panel-heading"><h4>Dispute resolution parties</h4></div><div class="panel-body">
1.	Samir Hutchings<br/>
2.	Jeffery Gan
</div></div></div>';
}
if (isset($_GET['signatories'])) {
echo '<div class="row">';
$sql = "SELECT * FROM users";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
$col = (12/$result->num_rows);
// output data of each row
while($row = $result->fetch_assoc()) {
	echo '<div class="col-sm-' . $col . ' col-xs-12">
	<div class="panel panel-default">
	  <div class="panel-thumbnail"><img src="' . $row['signature']  . '" class="img-responsive"></div>
	  <div class="panel-body">
		<p class="lead">' . $row['forename'] . ' ' . $row['surname'] . '</p>';
		if ($row['signatory'] == 1) echo '<i class="fa fa-check"></i> Signed';
		else echo '<i class="fa fa-times"></i>';
		if ($row['alljapesareacceptable'] == 1) echo '<br/><i class="fa fa-check"></i> All Japes are Acceptable';
	  echo '</div>
	</div>
  </div>';
}
}
echo '</div>';
echo '<div class="row">';
echo ' <div class="col-lg-3">
      	 <div class="well"> 
             <form class="form" action="signcharter.php" method="POST">
			 <h4>My Signature</h4>
              <div class="input-group text-center">
              <input class="form-control input-lg" type="checkbox" ';
				if ($signatory == 1) echo 'checked';
				echo ' name="iagree" value="True">
				I, ' . $forename . ' ' . $surname . ', agree to the terms of the charter as outlined above.
				<span class="input-group-btn"><button class="btn btn-lg btn-primary" type="submit">Save</button></span>
              </div>
			  <input type="hidden" name="charteruserid" value="' . $charteruserid . '" />
            </form>
          </div></div>';
		  echo '<div class="col-lg-6">
			<div class="well"> 
             <form class="form" enctype="multipart/form-data" action="signature.php" method="post">
			 <h4>Image Signature</h4>
			 <i>Users can upload images to server as their signature. These are displayed to all users. Uploading an image using the system below does not affect whether or not you are a signatory.</i>
              <div class="input-group text-center">
              <input name="signature" type="file" accept="image/*" required class="form-control input-lg" placeholder="Signature">
                <span class="input-group-btn"><button class="btn btn-lg btn-primary" type="submit">Upload</button></span>
              </div>
            </form>
          </div></div>';
		  echo ' <div class="col-lg-3">
      	 <div class="well"> 
             <form class="form" action="japesareacceptable.php" method="POST">
			 <h4>Acceptable Japes</h4>
              <div class="input-group text-center">
              <input class="form-control input-lg" type="checkbox" ';
				if ($alljapesareacceptable == 1) echo 'checked';
				echo ' name="alljapesareacceptable" value="True">
				I, ' . $forename . ' ' . $surname . ', think that all japes referring to myself are acceptable.
				<span class="input-group-btn"><button class="btn btn-lg btn-primary" type="submit">Save</button></span>
              </div>
			  <input type="hidden" name="charteruserid" value="' . $charteruserid . '" />
            </form>
          </div></div>';
		  
		  
		  echo '</div>';
}

echo $foot;
?>