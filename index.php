<?php
// --- GOLD MEDIA --- //
	error_reporting(0);
	ob_start();
	session_start();
 
	// GOLD BASE
	define('GOLD_BASE', dirname(empty($_SERVER['SCRIPT_FILENAME']) ? __FILE__ : $_SERVER['SCRIPT_FILENAME']).'/');
	$root = 'http://' . $_SERVER['SERVER_NAME'] . dirname($_SERVER['SCRIPT_NAME']);
	if(substr($root, -1)=="/")
	$root = 'http://' . $_SERVER['SERVER_NAME'];
	
	// REQUIRE FILES
	require 'gold-app/gold-includes/GOLD-CLASS.php';
	require 'gold-app/gold-includes/GOLD-CLASS-TEMPLATES.php';
	require 'gold-app/gold-includes/GOLD-CLASS-PLUGINS.php';
	require 'gold-app/gold-includes/GOLD-CLASS-WIDGETS.php';
	// Special Package for MOVIES (IMDB)
	include("gold-app/gold-includes/TMDb.php");

	// GOLD CONNECT
	$GOLD = new GOLD_CONNECT();
	$GOLD->host = GOLD_HOSTNAME;
	$GOLD->username = GOLD_USERNAME;
	$GOLD->password = GOLD_PASSWORD;
	$GOLD->table = GOLD_DATABASE;
	$GOLD->user_username = GOLD_ADMIN_USERNAME;
	$GOLD->user_password = GOLD_ADMIN_PASSWORD;
	$GOLD->user_email = GOLD_ADMIN_EMAIL;
	$GOLD->connect();
	
	// GOLD SKINS
	$tmpl=new SkinFunctions();
	$tmpl->setTemplate($tmpl->skin());
	
	echo $tmpl->GOLD_show();
	
?>