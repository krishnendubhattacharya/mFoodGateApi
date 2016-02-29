<?php
define("ADMINEMAIL","mfoogateadmin@mitralink-sinergi.com");
define("EMAILPASSWORD","Mitralink03");
define("MAILSERVER","mail.mitralink-sinergi.com");
define("MAILPORT","25");
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
