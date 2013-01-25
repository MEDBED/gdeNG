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
	{	$requete="SELECT count(a.id) as recordCount FROM vlan a WHERE id_materiel=:id_materiel ";// 
		foreach ($tabFiltre as $search=>$champ){
			if (!empty($_POST[$champ])){$requete.=" AND $search LIKE :$champ ";}
		}
		$requete.=" ;";
                $prep=$db->prepare($requete);
                $prep->bindParam(":id_materiel",$_GET['id_materiel'],PDO::PARAM_INT);
		$prep->execute();		
                $row = $prep->fetch(PDO::FETCH_ASSOC);
                $recordCount=$row['recordCount'];
                $prep->closeCursor();
		$prep = NULL;
		//Get records from database
		$limHaute=$_GET["jtStartIndex"]+$_GET["jtPageSize"];
		$requete="SELECT *,a.id as id_vlan FROM vlan a WHERE id_materiel=:id_materiel ";// 
		foreach ($tabFiltre as $search=>$champ){
			if (!empty($_POST[$champ])){$requete.=" AND $search LIKE :$champ ";}
		}
		$requete.="ORDER BY port LIMIT ".$_GET["jtStartIndex"]."," . $limHaute . ";";		
		$prep=$db->prepare($requete);
		foreach ($tabFiltre as $search=>$champ){
			if (!empty($_POST[$champ])){			
				$text="$_POST[$champ]%";
				$prep->bindParam(":$champ",$text,PDO::PARAM_STR);
			}
		}		
		$prep->bindParam(":id_materiel",$_GET['id_materiel'],PDO::PARAM_INT);	
		$prep->execute();		
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
		$requete = "INSERT INTO vlan(id_materiel,port,vlan_name,vlan_config,description,createOn,updateOn)";		
		$requete.=" VALUES(:id_materiel,:editeur,:nom,:version,:description,now(),now())";		
		$prep=$db->prepare($requete);
		$prep->bindParam(":id_materiel",$_POST['id_materiel'],PDO::PARAM_INT);					
                $prep->bindParam(":port",$_POST["port"],PDO::PARAM_STR);	                					
                $prep->bindParam(":vlan_name",$_POST["vlan_name"],PDO::PARAM_STR);
                $prep->bindParam(":vlan_config",$_POST["vlan_config"],PDO::PARAM_STR);	
                $prep->bindParam(":description",$_POST["description"],PDO::PARAM_STR);	
		$prep->execute();
		$id_nouveau = $db->lastInsertId();
		$prep = NULL;		
		//Get last inserted record (to return to jTable)		
		$requete = "SELECT * FROM vlan WHERE id = :id";
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
		$requete = "UPDATE vlan SET port=:port,vlan_name=:vlan_name,vlan_config=:vlan_config,description=:description,updateOn=now() WHERE id = :id;";
		$prep=$db->prepare($requete);
		$prep->bindParam(":id",$_POST["id_vlan"],PDO::PARAM_INT);						
                $prep->bindParam(":port",$_POST["port"],PDO::PARAM_STR);	                					
                $prep->bindParam(":vlan_name",$_POST["vlan_name"],PDO::PARAM_STR);
                $prep->bindParam(":vlan_config",$_POST["vlan_config"],PDO::PARAM_STR);	
                $prep->bindParam(":description",$_POST["description"],PDO::PARAM_STR);			
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
		$requete="DELETE FROM vlan WHERE id = :id;";		
		$prep=$db->prepare($requete);
		$prep->bindParam(":id",$_POST["id_vlan"],PDO::PARAM_INT);
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