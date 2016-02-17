<?php
function getConnection() {
	$dbhost="localhost";
	$dbuser="root";
	$dbpass="Host@123456";
	$dbname="mfoodgate";
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);	
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $dbh;
}
?>
