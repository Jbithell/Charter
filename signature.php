<?php
$title = 'Signature Upload';
require_once 'nav.php';
$target_dir = "signatures/";
$imageFileType = pathinfo(basename($target_dir . $_FILES["signature"]["name"],PATHINFO_EXTENSION));
$imageFileType = $imageFileType['extension'];
$target_file = $target_dir . $charteruserid . '_' . str_replace(' ', '_', $forename) . str_replace(' ', '_', $surname) . '.' . $imageFileType;
$uploadOk = 1;
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["signature"]["tmp_name"]);
    if($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
}
// Check if file already exists
if (file_exists($target_file)) {
	while (file_exists($target_file)) {
		$target_file_parts= explode(".", $target_file); 
		$target_file = $target_file_parts[0] . "new" . '.' . $target_file_parts[1];  
	}
}

// Check file size
if ($_FILES["signature"]["size"] > 500000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" && $imageFileType != "JPG" && $imageFileType != "PNG" && $imageFileType != "JPEG"
&& $imageFileType != "GIF") {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["signature"]["tmp_name"], $target_file)) {
		$path = basename($_FILES["signature"]["name"]);
        $conn = new mysqli($db_hostname, $db_username, $db_password, $db_database);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed, please contact support");
		}
		$sql = "UPDATE `charter`.`users` SET `signature` = '" . $target_file . "' WHERE charteruserid='" . $charteruserid . "'";
		$result = $conn->query($sql);
		echo 'Your signature (<img src="' . $target_file . '" />) has been uploaded.';
    } else {
        echo "Sorry, there was an error uploading your signature.";
    }
}
echo $foot;
?>