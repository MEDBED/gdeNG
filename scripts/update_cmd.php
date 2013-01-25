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
		$requete="SELECT a.*,b.*,b.id as id_fournisseur, a.id as id_commande FROM commande a, fournisseur b WHERE a.id_fournisseur=b.id AND a.id=:id_commande;";// 				
		$prep=$db->prepare($requete);			
		$prep->bindParam(":id_commande",$_GET['id_commande'],PDO::PARAM_INT);	
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
                $requete = "INSERT INTO commande(id_fournisseur,no_commande,financement,montant";
		
                $requete.=",date_achat";

                $requete.=",date_commande";

                $requete.=",date_expedition";

                $requete.=",date_reception";

                $requete.=",date_garantie";
		
                $requete.=",createOn,updateOn) VALUES(:id_fournisseur,:no_commande,:financement,:montant";
				
                $requete.=",:date_achat";


                $requete.=",:date_commande";


                $requete.=",:date_expedition";


                $requete.=",:date_reception";


                $requete.=",:date_garantie";
		
		$requete.=",now(),now())";	
		$prep=$db->prepare($requete);
		$prep->bindParam(":id_fournisseur",$_POST['id_fournisseur'],PDO::PARAM_INT);				
		$prep->bindParam(":no_commande",$_POST["no_commande"],PDO::PARAM_INT);
                $prep->bindParam(":financement",$_POST["financement"],PDO::PARAM_STR);
                $prep->bindParam(":montant",$_POST["montant"],PDO::PARAM_STR);				
		if (!empty($_POST["date_achat"])){
			$tmpDate=explode('-',$_POST["date_achat"]);
			$_POST["date_achat"]="$tmpDate[2]-$tmpDate[1]-$tmpDate[0]";
			$prep->bindParam(":date_achat",$_POST["date_achat"],PDO::PARAM_STR);
		}else{$prep->bindValue(":date_achat",NULL,PDO::PARAM_STR);}
                if (!empty($_POST["date_commande"])){
			$tmpDate=explode('-',$_POST["date_commande"]);
			$_POST["date_commande"]="$tmpDate[2]-$tmpDate[1]-$tmpDate[0]";
			$prep->bindParam(":date_commande",$_POST["date_commande"],PDO::PARAM_STR);
		}else{$prep->bindValue(":date_commande",NULL,PDO::PARAM_STR);}
                if (!empty($_POST["date_expedition"])){
			$tmpDate=explode('-',$_POST["date_expedition"]);
			$_POST["date_expedition"]="$tmpDate[2]-$tmpDate[1]-$tmpDate[0]";
			$prep->bindParam(":date_expedition",$_POST["date_expedition"],PDO::PARAM_STR);
		}else{$prep->bindValue(":date_expedition",NULL,PDO::PARAM_STR);}
                if (!empty($_POST["date_reception"])){
			$tmpDate=explode('-',$_POST["date_reception"]);
			$_POST["date_reception"]="$tmpDate[2]-$tmpDate[1]-$tmpDate[0]";
			$prep->bindParam(":date_reception",$_POST["date_reception"],PDO::PARAM_STR);
		}else{$prep->bindValue(":date_reception",NULL,PDO::PARAM_STR);}
                if (!empty($_POST["date_garantie"])){
			$tmpDate=explode('-',$_POST["date_garantie"]);
			$_POST["date_garantie"]="$tmpDate[2]-$tmpDate[1]-$tmpDate[0]";
			$prep->bindParam(":date_garantie",$_POST["date_garantie"],PDO::PARAM_STR);
		}else{$prep->bindValue(":date_garantie",NULL,PDO::PARAM_STR);}
		$prep->execute();
		$id_nouveau = $db->lastInsertId();
		$prep = NULL;		
                $requete="UPDATE materiel SET id_commande=$id_nouveau WHERE id=:id_materiel";
                $prep=$db->prepare($requete);
                $prep->bindParam(":id_materiel",$_POST['id_materiel'],PDO::PARAM_INT);	
                $prep->execute();
		//Get last inserted record (to return to jTable)		
		$requete = "SELECT * FROM commande WHERE id = :id";
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
		$requete = "UPDATE commande SET id_fournisseur=:id_fournisseur,updateOn=now(),no_commande=:no_commande, financement=:financement, montant=:montant";
		
			$requete.=",date_achat=:date_achat";
		
			$requete.=",date_commande=:date_commande";
		
			$requete.=",date_expedition=:date_expedition";
		
			$requete.=",date_reception=:date_reception";
		
			$requete.=",date_garantie=:date_garantie";
		
		$requete.=" WHERE id = :id;";
		$prep=$db->prepare($requete);
		$prep->bindParam(":id",$_POST["id_commande"],PDO::PARAM_INT);
		$prep->bindParam(":id_fournisseur",$_POST["id_fournisseur"],PDO::PARAM_INT);
                $prep->bindParam(":no_commande",$_POST["no_commande"],PDO::PARAM_STR);
		$prep->bindParam(":financement",$_POST["financement"],PDO::PARAM_STR);
		$prep->bindParam(":montant",$_POST["montant"],PDO::PARAM_STR);				
		if (!empty($_POST["date_achat"])){
			$tmpDate=explode('-',$_POST["date_achat"]);
			$_POST["date_achat"]="$tmpDate[2]-$tmpDate[1]-$tmpDate[0]";
			$prep->bindParam(":date_achat",$_POST["date_achat"],PDO::PARAM_STR);
		}else{$prep->bindValue(":date_achat",NULL,PDO::PARAM_STR);}
                if (!empty($_POST["date_commande"])){
			$tmpDate=explode('-',$_POST["date_commande"]);
			$_POST["date_commande"]="$tmpDate[2]-$tmpDate[1]-$tmpDate[0]";
			$prep->bindParam(":date_commande",$_POST["date_commande"],PDO::PARAM_STR);
		}else{$prep->bindValue(":date_commande",NULL,PDO::PARAM_STR);}
                if (!empty($_POST["date_expedition"])){
			$tmpDate=explode('-',$_POST["date_expedition"]);
			$_POST["date_expedition"]="$tmpDate[2]-$tmpDate[1]-$tmpDate[0]";
			$prep->bindParam(":date_expedition",$_POST["date_expedition"],PDO::PARAM_STR);
		}else{$prep->bindValue(":date_expedition",NULL,PDO::PARAM_STR);}
                if (!empty($_POST["date_reception"])){
			$tmpDate=explode('-',$_POST["date_reception"]);
			$_POST["date_reception"]="$tmpDate[2]-$tmpDate[1]-$tmpDate[0]";
			$prep->bindParam(":date_reception",$_POST["date_reception"],PDO::PARAM_STR);
		}else{$prep->bindValue(":date_reception",NULL,PDO::PARAM_STR);}
                if (!empty($_POST["date_garantie"])){
			$tmpDate=explode('-',$_POST["date_garantie"]);
			$_POST["date_garantie"]="$tmpDate[2]-$tmpDate[1]-$tmpDate[0]";
			$prep->bindParam(":date_garantie",$_POST["date_garantie"],PDO::PARAM_STR);
		}else{$prep->bindValue(":date_garantie",NULL,PDO::PARAM_STR);}
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
		$requete="DELETE FROM commande WHERE id = :id;";		
		$prep=$db->prepare($requete);
		$prep->bindParam(":id",$_POST["id_commande"],PDO::PARAM_INT);
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