<?php
/*
Plugin Name: Forms for Mailograph
Plugin URI: http://www.aytech.ca/wordpress-plugins/forms-for-mailograph.zip
Description: Forms for Mailograph email marketing.
Version: 0.2
Author: AYTechnologies Agency
Author URI: http://www.aytech.ca
License: GPLv2 or later.
*/
?>

<?php
$url = 'http://app.mailograph.com/subscribe';
$data = $_POST;

$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
    ),
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
if ($result === FALSE) {
	echo "ERROR"; 
} else if ($result === "1") {
	echo "You have subscribed successfully.";
} else {
	echo $result;
}

?>