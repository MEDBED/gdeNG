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
	$tabFiltre=array("alias"=>"Falias");
	//Getting records (listAction)
	if($_GET["action"] == "list")
	{			
		//Get records from database
		$limHaute=$_GET["jtStartIndex"]+$_GET["jtPageSize"];
		$requete="SELECT id as id_groupe, alias FROM groupe";// 
		foreach ($tabFiltre as $search=>$champ){
			if (!empty($_POST[$champ])){$requete.=" AND $search LIKE :$champ";}
		}
		$requete.=" ORDER BY alias,".$_GET["jtSorting"]." LIMIT ".$_GET["jtStartIndex"]."," . $limHaute . ";";		
		$prep=$db->prepare($requete);				
		$prep->execute();
		$recordCount=$prep->rowCount();				
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
		$requete = "INSERT INTO groupe(alias)";		
		$requete.=" VALUES(:alias)";		
		$prep=$db->prepare($requete);
		$prep->bindParam(":alias",$_POST['alias'],PDO::PARAM_STR);				
		$prep->execute();
		$id_nouveau = $db->lastInsertId();
		$prep = NULL;		
		//Get last inserted record (to return to jTable)		
		$requete = "SELECT id as id_groupe, alias FROM groupe WHERE id = :id ;";
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
		$requete = "UPDATE groupe SET alias=:alias ";
		$requete.="WHERE id = :id;";
		$prep=$db->prepare($requete);
		$prep->bindParam(":id",$_POST["id_groupe"],PDO::PARAM_INT);
		$prep->bindParam(":alias",$_POST["alias"],PDO::PARAM_STR);		   
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
		$requete="DELETE FROM groupe WHERE id = :id;";		
		$prep=$db->prepare($requete);
		$prep->bindParam(":id",$_POST["id_groupe"],PDO::PARAM_INT);
		$prep->execute();
		$prep->closeCursor();
		$prep = NULL;		
		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		print json_encode($jTableResult);
	}
	else if($_GET["action"] == "listUser")
	{
		$limHaute=$_GET["jtStartIndex"]+$_GET["jtPageSize"];
		$requete="SELECT id as id_user,nom,prenom FROM user WHERE id ";
		if ($_GET["type"]=='a'){$requete.='NOT ';}
		$requete.="IN (SELECT id_user FROM user_group WHERE id_group=:id_group) ORDER BY ".$_GET["jtSorting"]." LIMIT ".$_GET["jtStartIndex"]."," . $limHaute . ";";
		//$requete="SELECT id_user as id_user_group,b.id as id_user, nom, prenom FROM user_group a RIGHT OUTER JOIN user b ON id_user!=b.id  ORDER BY ".$_GET["jtSorting"]." LIMIT ".$_GET["jtStartIndex"]."," . $limHaute . "";		
		$prep=$db->prepare($requete);		
		$prep->bindParam(":id_group",$_GET["id_group"],PDO::PARAM_INT);
		$prep->execute();
		$recordCount=$prep->rowCount();
		//Add all records to an array
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
	else if($_GET["action"] == "removeUser")
	{
		$requete="DELETE FROM user_group WHERE id_user=:id_user AND id_group=:id_group";
		$prep=$db->prepare($requete);
		$prep->bindParam(":id_group",$_POST["id_groupe"],PDO::PARAM_INT);
		$prep->bindParam(":id_user",$_POST["id_user"],PDO::PARAM_INT);
		$prep->execute();
		$prep->closeCursor();
		$prep = NULL;
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		print json_encode($jTableResult);
	}
	else if($_GET["action"] == "addUser")		
	{		
		$requete="INSERT INTO user_group (id_user,id_group) VALUES(:id_user,:id_group)";
		$prep=$db->prepare($requete);
		$prep->bindParam(":id_group",$_POST["id_groupe"],PDO::PARAM_INT);		
		$prep->bindParam(":id_user",$_POST["id_user"],PDO::PARAM_INT);
		$prep->execute();
		$prep->closeCursor();
		$prep = NULL;
		/*$requete="SELECT id as id_user,nom,prenom FROM user WHERE id=:id_user;";		
		$prep=$db->prepare($requete);
		$prep->bindParam(":id_user",$_POST["id_user"],PDO::PARAM_INT);		
		$prep->execute();
		$recordCount=$prep->rowCount();
		//Add all records to an array
		$rows = array();
		$rows = $prep->fetchAll();
		$prep->closeCursor();
		$prep = NULL;*/
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		//$jTableResult['Records'] = $rows;
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