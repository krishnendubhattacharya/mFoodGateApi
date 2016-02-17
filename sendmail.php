<?php
function sendMail($from,$to,$subject,$body) {
	$headers = "From: " . strip_tags($from) . "\r\n";
	$headers .= "Reply-To: ". strip_tags($from) . "\r\n";
	$headers .= "CC: "."\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	mail($to,$subject,$body,$headers);
}
?>