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
		//$requete="SELECT *,a.id as id_suivi,CONCAT(nom,' ',prenom) AS utilisateur FROM suivi a, user b WHERE a.id_user=b.id";// 
                $requete="SELECT *,a.id as id_suivi,CONCAT(nom,' ',prenom) AS utilisateur FROM suivi a, user b WHERE a.source='materiel' AND a.id_source=:id_mat AND a.id_user=b.id";// 
		foreach ($tabFiltre as $search=>$champ){
			if (!empty($_POST[$champ])){$requete.=" AND $search LIKE :$champ";}
		}
		$requete.=" ORDER BY ".$_GET["jtSorting"]." LIMIT ".$_GET["jtStartIndex"]."," . $limHaute . ";";		
		$prep=$db->prepare($requete);
                $prep->bindParam(":id_mat",$_GET['id_materiel'],PDO::PARAM_INT);	
		foreach ($tabFiltre as $search=>$champ){
			if (!empty($_POST[$champ])){			
				$text="$_POST[$champ]%";
				$prep->bindParam(":$champ",$text,PDO::PARAM_STR);
			}
		}			
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
		$requete = "INSERT INTO suivi(source,id_source,id_user,detail,priorite";
		if (!empty($_POST["date"])){
			$requete.=",date";
		}
		$requete.=",updateOn) VALUES(:source,:id_source,:id_user,:detail,:priorite";
		if (!empty($_POST["date"])){			
			$requete.=",:date";
		}
		$requete.=",now())";		
		$prep=$db->prepare($requete);
		$prep->bindParam(":id_source",$_POST['id_source'],PDO::PARAM_INT);		
		$prep->bindParam(":id_user",$_SESSION["ID_USER"],PDO::PARAM_INT);
		$prep->bindParam(":source",$_POST["source"],PDO::PARAM_INT);
		$prep->bindParam(":detail",$_POST["detail"],PDO::PARAM_STR);
		$prep->bindParam(":priorite",$_POST["priorite"],PDO::PARAM_INT);
		if (!empty($_POST["date"])){
			$tmpDate=explode('-',$_POST["date"]);
			$_POST["date"]="$tmpDate[2]-$tmpDate[1]-$tmpDate[0]";
			$prep->bindParam(":date",$_POST["date"],PDO::PARAM_STR);
		}			
		$prep->execute();
		$id_nouveau = $db->lastInsertId();
		$prep = NULL;		
		//Get last inserted record (to return to jTable)		
		$requete = "SELECT * FROM suivi WHERE id = :id";
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
		$requete = "UPDATE suivi SET source=:source,id_source=:id_source, detail=:detail, priorite=:priorite";
		if (!empty($_POST["date"])){
			$tmpDate=explode('-',$_POST["date"]);
			$_POST["date"]="$tmpDate[2]-$tmpDate[1]-$tmpDate[0]";
			$requete.=",date=:date";
		}		
		$requete.=",updateOn=now() WHERE id = :id;";
		$prep=$db->prepare($requete);
		$prep->bindParam(":id",$_POST["id_suivi"],PDO::PARAM_INT);
		$prep->bindParam(":id_source",$_POST["id_source"],PDO::PARAM_INT);
	    $prep->bindParam(":source",$_POST["source"],PDO::PARAM_STR);
		$prep->bindParam(":detail",$_POST["detail"],PDO::PARAM_STR);
		$prep->bindParam(":priorite",$_POST["priorite"],PDO::PARAM_INT);	
		if (!empty($_POST["date"])){
			$tmpDate=explode('-',$_POST["date"]);
			$_POST["date"]="$tmpDate[2]-$tmpDate[1]-$tmpDate[0]";
			$prep->bindParam(":date",$_POST["date"],PDO::PARAM_STR);
		}						
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
		$requete="DELETE FROM net WHERE id = :id;";		
		$prep=$db->prepare($requete);
		$prep->bindParam(":id",$_POST["id_net"],PDO::PARAM_INT);
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