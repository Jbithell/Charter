<?php
require_once 'fbdetails.php';
require_once 'dblogins.php';
define("FB_APP_TOKEN", $appid . "|" . $appsecret);
 
/**
 * Send Facebook notification using CURL 
 * @param string $recipientFbid Scoped recipient's FB ID
 * @param string $text Text of notification (<150 chars)
 * @param string $url Relative URL to use when user clicks the notification
 * @return String
 */
function notify($recipientFbid, $text, $url) {
	$href = urlencode('https://applicationsystemhost.com/charter/redirect.php?url=' . $url);
	$post_data = "access_token=". FB_APP_TOKEN ."&template={$text}&href={$href}";
	$curl = curl_init(); 
	curl_setopt($curl, CURLOPT_URL, "https://graph.facebook.com/v2.1/". $recipientFbid ."/notifications"); 
	curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data); 
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
	$data = curl_exec($curl); 
	curl_close($curl); 
	return $data;
}
function notifyall($text, $url) {
	//Connect to SQL
	global $db_hostname, $db_username, $db_password, $db_database;
	$conn = new mysqli($db_hostname, $db_username, $db_password, $db_database);
	if ($conn->connect_error) {
		die("Connection failed");
	}
	$sql = "SELECT facebookid FROM users";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$href = urlencode('https://applicationsystemhost.com/charter/redirect.php?url=' . $url);
			$post_data = "access_token=". FB_APP_TOKEN ."&template={$text}&href={$href}";
			$curl = curl_init(); 
			curl_setopt($curl, CURLOPT_URL, "https://graph.facebook.com/v2.1/". $row["facebookid"] ."/notifications"); 
			curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data); 
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
			$data = curl_exec($curl); 
			curl_close($curl); 
		}
	}
	$conn->close();
}
?>
<?php 
if (isset($_GET['special'])) {
	print_r(notify($_GET['special'], 'Hi - This is a test', 'http://www.google.com/'));
	echo 'Send';
}
?>