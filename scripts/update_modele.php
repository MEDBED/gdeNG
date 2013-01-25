<?php
if(!isset($_COOKIE["ID_UTILISATEUR"])){
	header("Location: ../index.php");
	exit;
}
session_start();
include_once("../header.inc.php");
include_once("../include/functions.php");
try
{
	//Open database connection
	connectSQL();
	include_once("../include/protect_var.php");	
	//Getting records (listAction)
	if($_GET["action"] == "list")
	{
                //Total
                $requete="SELECT count(id) as CountModele FROM modele WHERE id_marque=:id_marque";
                if (!empty($_GET['id_type'])){
                    $requete.=" AND id_type=:id_type";
                }
                $requete.=" AND id_zone IN ($_SESSION[id_zone_pere]) ;";
                $prep=$db->prepare($requete);
		$prep->bindParam(":id_marque",$_GET[id_marque],PDO::PARAM_INT);
                if (!empty($_GET['id_type'])){
                    $prep->bindParam(":id_type",$_GET[id_type],PDO::PARAM_INT);
                }     
                $prep->execute();		
                $row = $prep->fetch(PDO::FETCH_ASSOC);
                $recordCount=$row['CountModele'];
		$prep->closeCursor();
		$prep = NULL;
		//Get records from database
		$limHaute=$_GET["jtStartIndex"]+$_GET["jtPageSize"];
		$requete="SELECT detail as modele2, id as id_modele2, id_marque as id_marque2, id_zone FROM modele WHERE id_marque=:id_marque";
                if (!empty($_GET['id_type'])){
                    $requete.=" AND id_type=:id_type";
                }
                $requete.=" AND id_zone IN ($_SESSION[id_zone_pere]) ORDER BY ".$_GET["jtSorting"]." LIMIT ".$_GET["jtStartIndex"]."," . $limHaute . ";";
		$prep=$db->prepare($requete);
		$prep->bindParam(":id_marque",$_GET[id_marque],PDO::PARAM_INT);
                if (!empty($_GET['id_type'])){
                    $prep->bindParam(":id_type",$_GET[id_type],PDO::PARAM_INT);
                }               
		$prep->execute();	
		$rows = array();
		$rows = $prep->fetchAll();
		$prep->closeCursor();
		$prep = NULL;
		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";	
		$jTableResult['TotalRecordCount'] = $recordCount;
		$jTableResult['Records'] = $rows;		
		print json_encode($jTableResult);
	}
	//Creating a new record (createAction)
else if($_GET["action"] == "create")
	{
		//Insert record into database
		$requete="INSERT INTO modele(id_marque,detail,id_type) VALUES(:id_marque,:modele,:id_type,:id_zone)";		
		$prep=$db->prepare($requete);
		$prep->bindParam(":id_marque",$_POST["id_marque"],PDO::PARAM_INT);
		$prep->bindParam(":modele", $_POST["modele2"],PDO::PARAM_INT);
                $prep->bindParam(":id_type", $_POST["id_type"],PDO::PARAM_INT);
                $prep->bindParam(":id_zone", $_SESSION[id_zone],PDO::PARAM_INT);                
		$prep->execute();
		$id_nouveau = $db->lastInsertId();
		//Get last inserted record (to return to jTable)
		$requete="SELECT detail as modele2, id as id_modele, id_marque FROM modele WHERE id = :id_nouveau;";
		$prep=$db->prepare($requete);
		$prep->bindParam(":id_nouveau",$id_nouveau,PDO::PARAM_INT);		
		$prep->execute();
		$row = $prep->fetch(PDO::FETCH_ASSOC);
		$prep->closeCursor();
		$prep = NULL;
		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		$jTableResult['Record'] = $row;
		print json_encode($jTableResult);
	}
	//Updating a record (updateAction)
	else if($_GET["action"] == "update")
	{
		//Update record in database
		$requete="UPDATE modele SET detail=:detail,id_type=:id_type WHERE id= :id;";
		$prep=$db->prepare($requete);
		$prep->bindParam(":id",$_POST["id_modele2"],PDO::PARAM_INT);
                $prep->bindParam(":id_type",$_POST["id_type"],PDO::PARAM_INT);
		$prep->bindParam(":detail",$_POST["modele2"],PDO::PARAM_STR);
		$prep->execute();
		$prep->closeCursor();
		$prep = NULL;
		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		print json_encode($jTableResult);
	}
	//Deleting a record (deleteAction)
	else if($_GET["action"] == "delete")
	{
		//Delete from database
		$requete = "DELETE FROM modele WHERE id=:id;";
		$prep=$db->prepare($requete);
		$prep->bindParam(":id",$_POST["id_modele2"],PDO::PARAM_INT);		
		$prep->execute();
		$prep->closeCursor();
		$prep = NULL;
		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		print json_encode($jTableResult);
	}	
	//Modifier le modele (deleteAction)
	else if($_GET["action"] == "change")
	{
		//Delete from database
		$requete="UPDATE $_POST[source] SET id_modele=:id_modele WHERE id = :id;";
		$prep=$db->prepare($requete);
		//$prep->bindParam(":table",$_POST["source"],PDO::PARAM_INT);
		$prep->bindParam(":id",$_POST["id_materiel2"],PDO::PARAM_INT);
		$prep->bindParam(":id_modele",$_POST["id_modele"],PDO::PARAM_INT);
		$prep->execute();						
		$prep->closeCursor();
		$prep = NULL;
		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";		
		//$jTableResult['Records'] = $rows;
		print json_encode($jTableResult);
	}
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