<?php
define("ADMINEMAIL","postmaster@codelaamaa.com");
define("EMAILPASSWORD","CodeLaamaa99@");
define("MAILSERVER","box669.bluehost.com");
define("MAILPORT","587");
//define("ISSSL",true);
define("SITEURL","http://107.170.152.166/mFoodGateAPI/");
define("WEBSITEURL","http://107.170.152.166/mFood/#/");

/*************** Paypal Details ***************/
define("PAYPAL_CLIENT_ID","ATDKp0ZoSqKOTPk39c_ZKiFVVrGmhS-nvGyj7dwyq7x6eAIwcxBOQscynDDhNLhOb4JBKuCtkSziQ-pU");
define("PAYPAL_SECRET","EOtwxXtc9l8tzpPuCoXy8RadcrTXaStDWpdN7bcP3qFFArrJpcQoBdHqo45dYo-N0yaraXZ1vESxv_0B");

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
