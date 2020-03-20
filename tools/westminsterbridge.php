<?php
date_default_timezone_set("Europe/London");

$xml = simplexml_load_string(file_get_contents('https://s3-eu-west-1.amazonaws.com/tfl.pub/Jamcams/jamcams-camera-list.xml'));
$json = json_encode($xml);
$array = json_decode($json,TRUE)["cameraList"]["camera"];
for($x = 0; $x < count($array); $x++) {
	$data = $array[$x];
	if (!$data["@attributes"]["available"]) continue;
	if ($data["@attributes"]["id"] != '00001.04502') continue;
	echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
	echo '<h3>Live camera view for ' . $data["location"] . '</h3>';
	//echo '<h2>' . date("l jS \of F Y h:i:s A", strtotime($data["captureTime"])) . '</h2>';
	echo '<hr/>';
	echo '<img src="https://s3-eu-west-1.amazonaws.com/tfl.pub/Jamcams/' . $data["file"] . '" alt="Error with image" style="width: 100%;" />';
	echo '<hr/>';
	echo '<h4>Camera Location</h4>';
	echo '<iframe frameborder="0" scrolling="no" style="width: 100%; min-height: 300px;" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?q=' . $data["lat"] . ',' . $data["lng"] . '&output=embed"></iframe>';
	//echo '<i>' . '<a href="https://www.google.co.uk/maps/place/' . $data["lat"] . '+' . $data["lng"] . '">Map</a>' . '</i>';
	echo '<hr/>';
	echo '<i>Image coutresy of <a href="//tfl.gov.uk">TFL</a></i>';
}
?>