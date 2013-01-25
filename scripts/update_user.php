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
	$tabFiltre=array("nom"=>"Fnom","prenom"=>"Fprenom");     
	//Getting records (listAction)
	if($_GET["action"] == "list")
	{	                           
                $requete="SELECT count(a.id) as recordCount FROM user a  WHERE 1=1";		
		foreach ($tabFiltre as $search=>$champ){
			if (!empty($_POST[$champ])){
                            $requete.=" AND $search LIKE :$champ";
			}
		}                		
		$prep=$db->prepare($requete);
		foreach ($tabFiltre as $search=>$champ){
			if (!empty($_POST[$champ])){				
                            $text="$_POST[$champ]%";
                            $prep->bindParam(":$champ",$text,PDO::PARAM_STR);
			}
		}				
		$prep->execute();		
                $row = $prep->fetch(PDO::FETCH_ASSOC);
                $recordCount=$row['recordCount'];
		$prep->closeCursor();
		$prep = NULL;
		//Get records from database
		$limHaute=$_GET["jtStartIndex"]+$_GET["jtPageSize"];
		$requete="SELECT *,id as id_user,password as passwordOrg FROM user WHERE 1=1";// 
		foreach ($tabFiltre as $search=>$champ){
                    if (!empty($_POST[$champ])){$requete.=" AND $search LIKE :$champ";}
		}
		$requete.=" ORDER BY ".$_GET["jtSorting"]." LIMIT ".$_GET["jtStartIndex"]."," . $limHaute . ";";				
                $prep=$db->prepare($requete);	
                foreach ($tabFiltre as $search=>$champ){
                    if (!empty($_POST[$champ])){
                        $text="$_POST[$champ]%";
                        $prep->bindParam(":$champ",$text,PDO::PARAM_STR);
                    }
		}
		$prep->execute();					
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
		$prep=$db->prepare($reqMarque);
		$prep->bindParam(":id_marque",$_POST[id_marque],PDO::PARAM_INT);		
		$prep->execute();		
		$resMarque = $prep->fetch(PDO::FETCH_ASSOC);		
		$prep = NULL;*/
		// Génération de la clef d'activation
		$caracteres = array("a", "b", "c", "d", "e", "f", 0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
		$caracteres_aleatoires = array_rand($caracteres, 8);
		$clef_activation = "";
		
		foreach($caracteres_aleatoires as $i){
			$clef_activation .= $caracteres[$i];
		}
		$uniqId=uniqid();		
		$requete = "INSERT INTO user(nom,prenom,login,ldap,actif, date_inscription, clef_activation, uniqId)";		
		$requete.=" VALUES(:nom,:prenom,:login,:ldap,:actif,'".date('Y-m-d')."','$clef_activation','$uniqId')";		
		$prep=$db->prepare($requete);
		$prep->bindParam(":nom",$_POST['nom'],PDO::PARAM_STR);		
		$prep->bindParam(":prenom",$resMarque["prenom"],PDO::PARAM_STR);
		$prep->bindParam(":ldap",$_POST["ldap"],PDO::PARAM_STR);
		if (!empty($_POST["ldap"])){
	    	$prep->bindParam(":ldap",$_POST["ldap"],PDO::PARAM_INT);
	    }else{
	    	$prep->bindValue(":ldap",0,PDO::PARAM_INT);
	    }		
		if (!empty($_POST["actif"])){
	    	$prep->bindParam(":actif",$_POST["actif"],PDO::PARAM_INT);
	    }else{
	    	$prep->bindValue(":actif",0,PDO::PARAM_INT);
	    }		
		$prep->execute();
		$id_nouveau = $db->lastInsertId();
		$prep = NULL;		
		//Get last inserted record (to return to jTable)		
		$requete = "SELECT * FROM user WHERE id = :id;";
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
		$requete = "UPDATE user SET nom=:nom,prenom=:prenom, login=:login,ldap=:ldap,actif=:actif ";		
		if (!empty($_POST["password"]) && ($_POST["password"] != $_POST["passwordOrg"])){								
			$requete.=",password=:password ";
		}		
		$requete.="WHERE id = :id;";
		$prep=$db->prepare($requete);
		$prep->bindParam(":id",$_POST["id_user"],PDO::PARAM_INT);
		$prep->bindParam(":nom",$_POST["nom"],PDO::PARAM_STR);
		$prep->bindParam(":prenom",$_POST["prenom"],PDO::PARAM_STR);
	    $prep->bindParam(":login",$_POST["login"],PDO::PARAM_STR);
	    if (!empty($_POST["ldap"])){
	    	$prep->bindParam(":ldap",$_POST["ldap"],PDO::PARAM_INT);
	    }else{
	    	$prep->bindValue(":ldap",0,PDO::PARAM_INT);
	    }		
		if (!empty($_POST["actif"])){
	    	$prep->bindParam(":actif",$_POST["actif"],PDO::PARAM_INT);
	    }else{
	    	$prep->bindValue(":actif",0,PDO::PARAM_INT);
	    }
		if (!empty($_POST["password"]) && ($_POST["password"] != $_POST["passwordOrg"])){			
			$prep->bindParam(":password",md5($_POST["password"]),PDO::PARAM_STR);			
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
		$requete="DELETE FROM user WHERE id = :id;";		
		$prep=$db->prepare($requete);
		$prep->bindParam(":id",$_POST["id_user"],PDO::PARAM_INT);
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