<?php
if(!isset($_COOKIE["ID_UTILISATEUR"])){
	header("Location: ../index.php");
	exit;
}
session_start();
include_once("../../header.inc.php");
include_once("../../include/functions.php");
include_once("../../include/protect_var.php");
try
{
	//Open database connection
	//$con = mysql_connect("localhost","gdeNG","gde");
	//mysql_select_db("gdeNG", $con);
	connectSQL();	      
        //Get records from database
        //$requete="SELECT DISTINCT a.id as Value,a.detail as DisplayText FROM marque a, modele b WHERE b.id_marque=a.id ORDER BY a.detail;";
        $requete="SELECT DISTINCT a.id as Value,a.detail as DisplayText FROM marque a, modele b WHERE b.id_marque=a.id ";
        if (!empty($_GET['id_type'])){
            $requete.="AND id_type=:id_type AND b.id_zone=$_SESSION[id_zone_org]";
        }else{
            $requete.="AND b.id_zone IN ($_SESSION[id_zone])";
        }
        $requete.=" ORDER BY a.detail;";
        $prep=$db->prepare($requete);
         if (!empty($_GET['id_type'])){
            $prep->bindParam(":id_type",$_GET[id_type],PDO::PARAM_INT);
        } 
        $prep->execute();	

        //Add all records to an array    
	$rows = array();
        $rows = $prep->fetchAll();

        //Return result to jTable
        $jTableResult = array();
        $jTableResult['Result'] = "OK";
        $jTableResult['Options'] = $rows;
        print json_encode($jTableResult);	
	//Close database connection	

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