<?php
$dirname = "images/";
$images = glob($dirname."*.jpg");
$allimages = array();
foreach($images as $image) {
	$allimages[filemtime($image)] = $image;
}
ksort($allimages);
foreach($allimages as $x => $x_value) {
	echo '<img src="'.$x_value.'" />' . date("l jS \of F Y h:i:s A",$x) .'<br />';
}
echo '1';
?>