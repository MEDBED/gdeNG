<?php
if(!isset($_COOKIE["ID_UTILISATEUR"])){
	header("Location: ../index.php");
	exit;
}
session_start();
include_once("../../header.inc.php");
include_once("../../include/functions.php");
try
{
	//Open database connection
	connectSQL();
	include_once("../../include/protect_var.php");	
			
		//Get records from database
		$requete = "SELECT DISTINCT id as Value,detail as DisplayText FROM type WHERE source='materiel' ORDER BY detail";
		$prep=$db->prepare($requete);
		$prep->bindParam(":source",$_GET[source],PDO::PARAM_STR);
		$prep->execute();		
		//Add all records to an array
		$rows = array();
		$rowInconnu=array();
		$rowInconnu[]=array("Value"=>"","DisplayText"=>"");
		$rows = $prep->fetchAll();
		$rowsSend=array_merge($rowInconnu,$rows);
		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		$jTableResult['Options'] = $rowsSend;
		print json_encode($jTableResult);		

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