<?php
date_default_timezone_set("Europe/London");

$xml = simplexml_load_string(file_get_contents('https://s3-eu-west-1.amazonaws.com/tfl.pub/Jamcams/jamcams-camera-list.xml'));
$json = json_encode($xml);
$array = json_decode($json,TRUE)["cameraList"]["camera"];
for($x = 0; $x < count($array); $x++) {
	$data = $array[$x];
	if (!$data["@attributes"]["available"]) continue;
	if ($data["@attributes"]["id"] != '00001.04502') continue;
	$image_link = 'https://s3-eu-west-1.amazonaws.com/tfl.pub/Jamcams/' . $data["file"];
	
	//if (file_exists("images/".$data["file"])) die("Already Got");
	
	$split_image = pathinfo($image_link);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL , $image_link);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	$response= curl_exec ($ch);
	curl_close($ch);
	$file_name = "images/".rand(10,9999999999).$split_image['filename'].".".$split_image['extension'];
	$file = fopen($file_name , 'w') or die("X_x");
	fwrite($file, $response);
	fclose($file);
	echo 'Success';
}
?>