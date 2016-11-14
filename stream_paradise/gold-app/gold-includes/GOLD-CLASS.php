<?php
// --- GOLD MEDIA --- //

require GOLD_BASE.'gold-config.php';

class GOLD_CONNECT {

  var $host;
  var $username;
  var $password;
  var $table;
  var $user_username;
  var $user_password;
  var $user_email;
  
  public function connect() {
    if($this->host != '' && $this->username != '' && $this->password != '') {
    	mysql_connect($this->host,$this->username,$this->password) or die("Gold MOVIES could not connect to MYSQL database " . mysql_error());
     	mysql_select_db($this->table) or die("Gold MOVIES could not select database " . mysql_error());
     	mysql_query("SET CHARACTER SET utf8");
  	  mysql_query("SET NAMES 'utf8'");
    }
  }

}
?>