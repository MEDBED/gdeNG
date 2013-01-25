<?php
if(!isset($_COOKIE["ID_UTILISATEUR"])){
	header("Location: ../index.php");
	exit;
}
session_start();
include_once("../header.inc.php");
include_once("../include/functions.php");
//include_once("../include/protect_var.php");
try
{
	//Open database connection
	//$con = mysql_connect("localhost","gdeNG","gde");
	//mysql_select_db("gdeNG", $con);
	//mysql_query("SET NAMES utf8;");
	connectSQL();
	include_once("../include/protect_var.php");	
	$tabFiltre=array("nom"=>"Fnom","b.detail"=>"Fmarque","c.detail"=>"Fmodele","systeme"=>"Fsysteme");
	//Getting records (listAction)
	if($_GET["action"] == "list")
	{			
		//Get records from database
		$limHaute=$_GET["jtStartIndex"]+$_GET["jtPageSize"];
		$requete="SELECT *,a.id as id_drive,a.id_type as id_type_drive FROM drive a WHERE";// 
		foreach ($tabFiltre as $search=>$champ){
			if (!empty($_POST[$champ])){$requete.=" AND $search LIKE :$champ";}
		}
		$requete.=" id_materiel=:id_materiel ORDER BY type,volume LIMIT ".$_GET["jtStartIndex"]."," . $limHaute . ";";		
		$prep=$db->prepare($requete);
		foreach ($tabFiltre as $search=>$champ){
			if (!empty($_POST[$champ])){			
				$text="$_POST[$champ]%";
				$prep->bindParam(":$champ",$text,PDO::PARAM_STR);
			}
		}		
		$prep->bindParam(":id_materiel",$_GET['id_materiel'],PDO::PARAM_INT);	
		$prep->execute();
		$recordCount=$prep->rowCount();		
		$_SESSION['REQ_MAT']=$requete;
		//Add all records to an array
		$rows = array();
		$rows = $prep->fetchAll();
		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		$jTableResult['TotalRecordCount'] = $recordCount;
		$jTableResult['Records'] = $rows;
		$prep->closeCursor();
		$prep = NULL;	
		//echo $requete;	
		print json_encode($jTableResult);
	}
	//Creating a new record (createAction)
else if($_GET["action"] == "create")
	{
		//Insert record into database		
		/*$reqMarque="SELECT id FROM modele WHERE id_marque=:id_marque AND detail='Inconnu' LIMIT 1";
		//$resMarque=mysql_fetch_array(mysql_query($reqMarque));
		$prep=$db->prepare($reqMarque);
		$prep->bindParam(":id_marque",$_POST[id_marque],PDO::PARAM_INT);		
		$prep->execute();		
		$resMarque = $prep->fetch(PDO::FETCH_ASSOC);		
		$prep = NULL;*/
		$requete = "INSERT INTO drive(id_materiel,lettre,id_type,fs,total,free,volume";		
		$requete.=",createOn,updateOn) VALUES(:id_materiel,:lettre,:id_type,:fs,:total,:free,:volume";		
		$requete.=",now(),now())";		
		$prep=$db->prepare($requete);
		$prep->bindParam(":id_materiel",$_POST['id_materiel'],PDO::PARAM_INT);		
		$prep->bindParam(":lettre",$_POST["lettre"],PDO::PARAM_INT);
		$prep->bindParam(":id_type",$_POST["id_type"],PDO::PARAM_INT);
		$prep->bindParam(":fs",$_POST["fs"],PDO::PARAM_STR);
		$prep->bindParam(":total",$_POST["total"],PDO::PARAM_STR);
		$prep->bindParam(":free",$_POST["free"],PDO::PARAM_STR);
		$prep->bindParam(":volume",$_POST["volume"],PDO::PARAM_STR);		
		$prep->execute();
		$id_nouveau = $db->lastInsertId();
		$prep = NULL;		
		//Get last inserted record (to return to jTable)		
		$requete = "SELECT * FROM drive WHERE id = :id";
		$prep=$db->prepare($requete);
		$prep->bindParam(":id",$id_nouveau,PDO::PARAM_INT);	
		$prep->execute();
		$row = $prep->fetch(PDO::FETCH_ASSOC);
		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		$jTableResult['Record'] = $row;
		$prep->closeCursor();
		$prep = NULL;
		print json_encode($jTableResult);
	}
	//Updating a record (updateAction)
	else if($_GET["action"] == "update")
	{		
		//Update record in database
		$requete = "UPDATE drive SET lettre=:lettre, id_type=:id_type, fs=:fs, total=:total, free=:free, volume=:volume";			
		$requete.=",updateOn=now() WHERE id = :id;";
		$prep=$db->prepare($requete);
		$prep->bindParam(":id",$_POST["id_drive"],PDO::PARAM_INT);		
                $prep->bindParam(":lettre",$_POST["lettre"],PDO::PARAM_STR);
		$prep->bindParam(":id_type",$_POST["id_type"],PDO::PARAM_INT);
		$prep->bindParam(":fs",$_POST["fs"],PDO::PARAM_STR);
		$prep->bindParam(":total",$_POST["total"],PDO::PARAM_STR);
		$prep->bindParam(":free",$_POST["free"],PDO::PARAM_STR);
		$prep->bindParam(":volume",$_POST["volume"],PDO::PARAM_STR);		
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
		$requete="DELETE FROM drive WHERE id = :id;";		
		$prep=$db->prepare($requete);
		$prep->bindParam(":id",$_POST["id_drive"],PDO::PARAM_INT);
		$prep->execute();
		$prep->closeCursor();
		$prep = NULL;		
		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		print json_encode($jTableResult);
	}

	//Close database connection
	//mysql_close($con);

}
catch(Exception $ex)
{
    //Return error message
	$jTableResult = array();
	$jTableResult['Result'] = "ERROR";
	$jTableResult['Message'] = $titi;
	print json_encode($jTableResult);
}
	
?>