<?php
define("ADMINEMAIL","postmaster@codelaamaa.com");
define("EMAILPASSWORD","CodeLaamaa99@");
define("MAILSERVER","box669.bluehost.com");
define("MAILPORT","587");
//define("ISSSL",true);
define("SITEURL","http://107.170.152.166/mFoodGateAPI/");

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
