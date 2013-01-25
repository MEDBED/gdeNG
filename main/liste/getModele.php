<?php
if(!isset($_COOKIE["ID_UTILISATEUR"])){
	header("Location: ../index.php");
	exit;
}
session_start();
include_once("../../include/protect_var.php");
try
{
	//Open database connection
	$con = mysql_connect("localhost","gdeNG","gde");
	mysql_select_db("gdeNG", $con);
			
		//Get records from database
		$result = mysql_query("SELECT DISTINCT id as Value,detail as DisplayText FROM modele ORDER BY detail");
		
		//Add all records to an array
		$rows = array();
		while($row = mysql_fetch_array($result))
		{
		    $rows[] = $row;		    
			//$rows[$row['id']] = $row['detail'];
		}

		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		$jTableResult['Options'] = $rows;
		print json_encode($jTableResult);	

	//Close database connection
	mysql_close($con);

}
catch(Exception $ex)
{
    //Return error message
	$jTableResult = array();
	$jTableResult['Result'] = "ERROR";
	$jTableResult['Message'] = $ex->getMessage();
	print json_encode($jTableResult);
}	
?>