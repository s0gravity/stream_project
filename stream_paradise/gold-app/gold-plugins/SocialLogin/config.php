<?php
	$root = 'http://' . $_SERVER['SERVER_NAME'] . dirname($_SERVER['SCRIPT_NAME']);
	if(substr($root, -1)=="/")
	$root = 'http://' . $_SERVER['SERVER_NAME'];
	require '../../../gold-config.php';
	mysql_connect(GOLD_HOSTNAME,GOLD_USERNAME,GOLD_PASSWORD) or die("Gold MEDIA could not connect to MYSQL database " . mysql_error());
    mysql_select_db(GOLD_DATABASE) or die("Gold MEDIA could not select database " . mysql_error());
	mysql_query("SET CHARACTER SET utf8"); 
	mysql_query("SET NAMES 'utf8'"); 
	
/*!
* HybridAuth
* http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
* (c) 2009-2012, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html
*/

// ----------------------------------------------------------------------------------------
//	HybridAuth Config file: http://hybridauth.sourceforge.net/userguide/Configuration.html
// ----------------------------------------------------------------------------------------
$config =array(
		"base_url" => "http://nismedia-shop.nisgeo.com/gold-app/gold-plugins/gold-login/hybridauth/index.php", 
		"providers" => array ( 

			"Google" => array ( 
				"enabled" => true,
				"keys"    => array ( "id" => "XXXXXXXXXXXX", "secret" => "XXXXXXXX" ), 
			),

			"Facebook" => array ( 
				"enabled" => true,
				"keys"    => array ( "id" => "1505484089690866", "secret" => "9462fa12ecd784d3143062ecc5eb3076" ), 
			),

			"Twitter" => array ( 
				"enabled" => true,
				"keys"    => array ( "key" => "nWjcF8EQ8acToG8NrtCz2cXre", "secret" => "zBVV3c65OHJ0eBmT4uXkgO5bIVhvhldFNMPG6pwnoLclbmfV34" ) 
			),
		),
		// if you want to enable logging, set 'debug_mode' to true  then provide a writable file by the web server on "debug_file"
		"debug_mode" => false,
		"debug_file" => "",
	);
