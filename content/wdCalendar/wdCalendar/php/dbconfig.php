<?php
session_start();
class DBConnection{
	function getConnection(){
	  //change to your database server/user name/password
		mysql_connect($GLOBALS['params']['bdd']['db_host'],$GLOBALS['params']['bdd']['db_user'],$GLOBALS['params']['bdd']['db_pass']) or
		//mysql_connect("localhost","nounou","nounou") or
         die("Could not connect: " . mysql_error());
    //change to your database name
		mysql_select_db($GLOBALS['params']['bdd']['db_name']) or 
		     die("Could not select database: " . mysql_error());
		@mysql_query("SET lc_time_names = 'fr_FR';");
	}
}
?>