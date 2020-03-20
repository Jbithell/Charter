<?php 
require_once 'auth.php';
?>
<h2>Add a new Jape</h2>
<?php
	echo '<form action="add_jape.php" method="post"><table border="1" style="width: 100%;">';
    echo '<tr style="width: 100%;"><td><b>Text:</b></td><td style="width: 100%;"><textarea name="text" style="width: 100%; height: 300px">';
	echo '</textarea></tr>';
	echo '<tr style="width: 100%;"><td><b>Refers To:</b></td><td style="width: 100%;"><textarea name="refersto" style="width: 100%; height: 50px">';
	echo '</textarea></tr>';
	echo '<tr style="width: 100%;"><td><b>Comment:</b></td><td style="width: 100%;"><textarea name="comment" style="width: 100%; height: 100px"></textarea></tr>';
	echo '<tr><td colspan="2" align="right"><input type="submit" value="Table Amendment" /></td></tr>';
	echo '<tr><td colspan="2"><i>Tabling an amendment to the appendix will notify all signatories that they have 24 hours to vote immediately. Amendments can be withdrawn. You have not automatically voted for your own amendment - You must vote separately. </i></td></tr>';
	echo '</table><input type="hidden" name="add" value="true" /></form>';
?>