<?php
if(!isset($_COOKIE["ID_UTILISATEUR"])){
	header("Location: ../index.php");
	exit;
}
session_start();
include_once("../../header.inc.php");
include_once("../../include/functions.php");
//include_once("../include/protect_var.php");
try
{	
	connectSQL();
	include_once("../../include/protect_var.php");	
		                                                   
        
        $requete="SELECT distinct systeme as detail, count(systeme) as count,id_type ,detail as type FROM materiel a, type b WHERE a.id_type=b.id AND systeme!='' AND id_entite=:id_entite AND id_zone!=4 GROUP BY id_type,systeme; ";//                 
        $prep=$db->prepare($requete);	        
        $prep->bindParam(":id_entite",$_SESSION['id_entite'],PDO::PARAM_INT);	
        $prep->execute();					
        //Add all records to an array
        $rows = array();
        $rows = $prep->fetchAll();
        $requete="SELECT distinct systeme as detail1, count(systeme) as count1 FROM materiel WHERE systeme!='' AND id_entite=:id_entite AND id_zone!=4 GROUP BY systeme; ";//                   
        $prep=$db->prepare($requete);	        
        $prep->bindParam(":id_entite",$_SESSION['id_entite'],PDO::PARAM_INT);	
        $prep->execute();
        $recordCount=$prep->rowCount();	
        $rows1 = array();
        $rows1 = $prep->fetchAll();
        /*while ($res=$prep->fetch(PDO::FETCH_ASSOC)){	
            $rows[]=array($res['detail'],$res['count']);
        }*/
        //Return result to jTable
        $jTableResult = array();
        //$jTableResult['Result'] = "OK";
        $jTableResult['TotalRecordCount'] = $recordCount;
        $jTableResult['xAxis'] = $rows1;
        $jTableResult['Records'] = $rows;
        $prep->closeCursor();
        $prep = NULL;	
        //echo $requete;	
        print json_encode($jTableResult);
        //print_r($rows);
	
	
}
catch(Exception $ex)
{
    //Return error message
	$jTableResult = array();
	$jTableResult['Result'] = "ERROR";
	$jTableResult['Message'] = '';
	print json_encode($jTableResult);
}
	
?>