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
		$requete="SELECT id as id_materiel, id_commande, nom FROM materiel WHERE id=:id_materiel";// 						
            //$requete="SELECT id FROM materiel";
		$prep=$db->prepare($requete);			
                $prep->bindParam(":id_materiel",$_GET['id_materiel'],PDO::PARAM_INT);	
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
		//Insert record into database		
		$reqMarque="SELECT id FROM modele WHERE id_marque=:id_marque AND detail='Inconnu' LIMIT 1";
		//$resMarque=mysql_fetch_array(mysql_query($reqMarque));
		$prep=$db->prepare($reqMarque);
		$prep->bindParam(":id_marque",$_POST[id_marque],PDO::PARAM_INT);		
		$prep->execute();		
		$resMarque = $prep->fetch(PDO::FETCH_ASSOC);		
		$prep = NULL;
		$requete = "INSERT INTO net(id_materiel,id_modele,ip,mac,mask,gw,sn,net,description";
		if (!empty($_POST["date_installe"])){
			$requete.=",date_installe";
		}
		$requete.=",createOn,updateOn) VALUES(:id_materiel,:id_modele,:ip,:mac,:mask,:gw,:sn,:net,:description";
		if (!empty($_POST["date_installe"])){			
			$requete.=",:date_installe";
		}
		$requete.=",now(),now())";		
		$prep=$db->prepare($requete);
		$prep->bindParam(":id_materiel",$_POST['id_materiel'],PDO::PARAM_INT);		
		$prep->bindParam(":id_modele",$resMarque["id"],PDO::PARAM_INT);
		$prep->bindParam(":ip",$_POST["ip"],PDO::PARAM_INT);
		$prep->bindParam(":mac",$_POST["mac"],PDO::PARAM_STR);
		$prep->bindParam(":mask",$_POST["mask"],PDO::PARAM_STR);
		$prep->bindParam(":gw",$_POST["gw"],PDO::PARAM_STR);
		$prep->bindParam(":sn",$_POST["sn"],PDO::PARAM_STR);
		$prep->bindParam(":net",$_POST["net"],PDO::PARAM_STR);
		$prep->bindParam(":description",$_POST["description"],PDO::PARAM_STR);
		if (!empty($_POST["date_installe"])){
			$tmpDate=explode('-',$_POST["date_installe"]);
			$_POST["date_installe"]="$tmpDate[2]-$tmpDate[1]-$tmpDate[0]";
			$prep->bindParam(":date_installe",$_POST["date_installe"],PDO::PARAM_STR);
		}			
		$prep->execute();
		$id_nouveau = $db->lastInsertId();
		$prep = NULL;		
		//Get last inserted record (to return to jTable)		
		$requete = "SELECT * FROM net WHERE id = :id";
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
		$requete = "UPDATE net SET id_modele=:id_modele,ip=:ip, mac=:mac, mask=:mask, gw=:gw, sn=:sn, net=:net, description=:description";
		if (!empty($_POST["date_installe"])){
			$tmpDate=explode('-',$_POST["date_installe"]);
			$_POST["date_installe"]="$tmpDate[2]-$tmpDate[1]-$tmpDate[0]";
			$requete.=",date_installe=:date_installe";
		}		
		$requete.=",updateOn=now() WHERE id = :id;";
		$prep=$db->prepare($requete);
		$prep->bindParam(":id",$_POST["id_net"],PDO::PARAM_INT);
		$prep->bindParam(":id_modele",$_POST["id_modele"],PDO::PARAM_INT);
                $prep->bindParam(":ip",$_POST["ip"],PDO::PARAM_STR);
		$prep->bindParam(":mac",$_POST["mac"],PDO::PARAM_STR);
		$prep->bindParam(":mask",$_POST["mask"],PDO::PARAM_STR);
		$prep->bindParam(":gw",$_POST["gw"],PDO::PARAM_STR);
		$prep->bindParam(":sn",$_POST["sn"],PDO::PARAM_STR);
		$prep->bindParam(":net",$_POST["net"],PDO::PARAM_STR);
		$prep->bindParam(":description",$_POST["description"],PDO::PARAM_STR);
		if (!empty($_POST["date_installe"])){
			$tmpDate=explode('-',$_POST["date_installe"]);
			$_POST["date_installe"]="$tmpDate[2]-$tmpDate[1]-$tmpDate[0]";
			$prep->bindParam(":date_installe",$_POST["date_installe"],PDO::PARAM_STR);
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