<?php
// GOLD PLUGIN
/*
	Plugin Name: Social Login
	Plugin Description: Users can log in via Facebook, Twitter, Google and other Social Networks Providers.
	Plugin Version: 1.0.0
	Plugin Date: 2014-09-01
	Plugin For: Gold MEDIA
	Plugin Author: ThemesGold
	Plugin License: GPLv2
	Plugin URL: 
*/

$plugin_id = basename(__FILE__);

$data['name'] = "Social Login";
$data['author'] = "ThemesGold";
$data['url'] = "http://www.themesgold.com/";

// Register GOLD Plugin Data
register_plugin($plugin_id, $data);

class GOLD-PLUGIN {
	function install_plugin() {
		echo 'GOLD PLUGIN hooks into TEST, priority = 2<br />';
	}
}

$plugin = new GOLD-PLUGIN();
add_plugin('plugin_sql', array(&$plugin,'plugin_sql'), 2);

?>