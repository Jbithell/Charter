<?php
if (isset($_GET["url"])) {
	echo "<script type='text/javascript'>";
	echo "top.location.href = '" . urldecode($_GET['url']) . "'";
	echo "</script>";
 }
 else {
	echo "<script type='text/javascript'>";
	echo "top.location.href = 'https://" . $_SERVER['HTTP_HOST'] . "/charter/'";
	echo "</script>";
}
echo 'Loading...';
?>