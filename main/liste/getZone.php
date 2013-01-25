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
		if (isset($_GET['strict'])){
			$requete = "SELECT DISTINCT id as Value,detail as DisplayText FROM zone WHERE id IN($_SESSION[id_zone]) ORDER BY id";
		}else{
			$requete = "SELECT DISTINCT id as Value,detail as DisplayText FROM zone ORDER BY id";
		}
		$rec=$db->query($requete);
		if ($rec){
			while ($res=$rec->fetch(PDO::FETCH_ASSOC)){	
				$rows[] = $res;
			}	
		}				
		$rowInconnu=array();
		$rowInconnu[]=array("Value"=>"","DisplayText"=>"");		
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