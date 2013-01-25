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
	$tabFiltre=array("nom"=>"Fnom","b.detail"=>"Fmarque","c.detail"=>"Fmodele","systeme"=>"Fsysteme","e.ip"=>"Fip");
	//Getting records (listAction)
	if($_GET["action"] == "list")
	{		
                $limHaute=$_GET["jtStartIndex"]+$_GET["jtPageSize"];	
                if($_GET["jtSorting"]=='ip DESC'){
                    $_GET["jtSorting"]="INET_ATON(ip) DESC";
                }elseif ($_GET["jtSorting"]=='ip ASC'){
                    $_GET["jtSorting"]="INET_ATON(ip) ASC";
                }
		//Total records
		$requete="SELECT count(a.id) as recordCount FROM materiel a, marque b, modele c, type d  WHERE a.id_modele=c.id AND c.id_marque=b.id AND a.id_type=d.id";		
		foreach ($tabFiltre as $search=>$champ){
                    if (!empty($_POST[$champ])){
                        $requete.=" AND $search LIKE :$champ";
                    }
		}                
		$requete.=" AND id_entite=:id_entite AND a.id_zone IN ($_SESSION[id_zone]);";
		$prep=$db->prepare($requete);
		foreach ($tabFiltre as $search=>$champ){
                    if (!empty($_POST[$champ])){				
                        $text="$_POST[$champ]%";
                        $prep->bindParam(":$champ",$text,PDO::PARAM_STR);
                    }
		}		
		$prep->bindParam(":id_entite",$_SESSION['id_entite'],PDO::PARAM_INT);
		$prep->execute();		
                $row = $prep->fetch(PDO::FETCH_ASSOC);
                $recordCount=$row['recordCount'];
		$prep->closeCursor();
		$prep = NULL;
		//Get records from database		
		//$requete="SELECT *,a.id as id_materiel,b.id as id_marque,b.detail as marque,c.detail as modele,d.detail as type_materiel,ip FROM materiel a LEFT OUTER JOIN net e ON a.id=e.id_materiel, marque b, modele c, type d  WHERE a.id_modele=c.id AND c.id_marque=b.id AND a.id_type=d.id";// 
		$requete="SELECT a.*,b.*,c.*,d.*,a.id as id_materiel,b.id as id_marque,b.detail as marque,c.detail as modele,d.detail as type_materiel,GROUP_CONCAT(IF(ip='0.0.0.0',ip,'') SEPARATOR \"<br/>\") as ip,GROUP_CONCAT(e.id,'@@',IF(ip!='0.0.0.0',ip,'') SEPARATOR \"<br/>\") as id_net, a.id_type as id_type2,a.id_zone as id_zone2 FROM materiel a LEFT OUTER JOIN net e ON a.id=e.id_materiel, marque b, modele c, type d  WHERE a.id_modele=c.id AND c.id_marque=b.id AND a.id_type=d.id";                
		foreach ($tabFiltre as $search=>$champ){
			if (!empty($_POST[$champ])){$requete.=" AND $search LIKE :$champ";}
		}
		$requete.=" AND id_entite=:id_entite AND a.id_zone IN ($_SESSION[id_zone]) GROUP BY a.id ORDER BY a.id_zone,".$_GET["jtSorting"]." LIMIT ".$_GET["jtStartIndex"]."," . $_GET["jtPageSize"] . ";";		
		$prep=$db->prepare($requete);
		foreach ($tabFiltre as $search=>$champ){
			if (!empty($_POST[$champ])){
				//$prep->bindParam(":$search",$search,PDO::PARAM_STR);
				$text="$_POST[$champ]%";
				$prep->bindParam(":$champ",$text,PDO::PARAM_STR);
			}
		}		
		$prep->bindParam(":id_entite",$_SESSION['id_entite'],PDO::PARAM_INT);	
		//$prep->bindValue(":id_zone","",PDO::PARAM_INT);
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
		//Insert record into database		
		$reqMarque="SELECT id FROM modele WHERE id_marque=:id_marque AND detail='Inconnu' LIMIT 1";
		//$resMarque=mysql_fetch_array(mysql_query($reqMarque));
		$prep=$db->prepare($reqMarque);
		$prep->bindParam(":id_marque",$_POST[id_marque],PDO::PARAM_INT);		
		$prep->execute();		
		$resMarque = $prep->fetch(PDO::FETCH_ASSOC);		
		$prep = NULL;
		$requete = "INSERT INTO materiel(id_zone,id_entite,id_modele,id_type,nom,sn,systeme,systeme_version";
		if (!empty($_POST["date_installe"])){
			$requete.=",date_installe";
		}
		$requete.=",emplacement,description,contact,createOn,updateOn) VALUES(:id_zone,:id_entite,:id_modele,:id_type,:nom,:sn,:systeme,:systeme_version";
		if (!empty($_POST["date_installe"])){			
			$requete.=",:date_installe";
		}
		$requete.=",:emplacement,:description,:contact,now(),now())";		
		$prep=$db->prepare($requete);
		$prep->bindParam(":id_zone",$_POST['id_zonec'],PDO::PARAM_INT);
		$prep->bindParam(":id_entite",$_SESSION['id_entite'],PDO::PARAM_INT);
		$prep->bindParam(":id_modele",$resMarque["id"],PDO::PARAM_INT);
		$prep->bindParam(":id_type",$_POST["id_type2"],PDO::PARAM_INT);
		$prep->bindParam(":nom",$_POST["nom"],PDO::PARAM_STR);
		$prep->bindParam(":sn",$_POST["sn"],PDO::PARAM_STR);
		$prep->bindParam(":systeme",$_POST["systeme"],PDO::PARAM_STR);
		$prep->bindParam(":systeme_version",$_POST["systeme_version"],PDO::PARAM_STR);
		if (!empty($_POST["date_installe"])){
			$tmpDate=explode('-',$_POST["date_installe"]);
			$_POST["date_installe"]="$tmpDate[2]-$tmpDate[1]-$tmpDate[0]";
			$prep->bindParam(":date_installe",$_POST["date_installe"],PDO::PARAM_STR);
		}	
		$prep->bindParam(":emplacement",$_POST["emplacement"],PDO::PARAM_STR);
		$prep->bindParam(":description",$_POST["description"],PDO::PARAM_STR);
		$prep->bindParam(":contact",$_POST["contact"],PDO::PARAM_STR);
		$prep->execute();
		$id_nouveau = $db->lastInsertId();
		$prep = NULL;		
		//Get last inserted record (to return to jTable)		
		$requete = "SELECT * FROM materiel WHERE id = :id AND id_dentite=:id_entite;";
		$prep=$db->prepare($requete);
		$prep->bindParam(":id",$id_nouveau,PDO::PARAM_INT);
		$prep->bindParam(":id_entite",$_SESSION['id_entite'],PDO::PARAM_INT);
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
		$requete = "UPDATE materiel SET id_zone=:id_zone,id_modele=:id_modele,id_type=:id_type, nom=:nom, sn=:sn, systeme=:systeme, systeme_version=:systeme_version";
		if (!empty($_POST["date_installe"])){
			$tmpDate=explode('-',$_POST["date_installe"]);
			$_POST["date_installe"]="$tmpDate[2]-$tmpDate[1]-$tmpDate[0]";
			$requete.=",date_installe=:date_installe";
		}
		if (!empty($_POST['id_zone'])){
			$requete.=",id_zone=:id_zone";
		}
		$requete.=",emplacement=:emplacement,description=:description,contact=:contact,updateOn=now() WHERE id = :id;";
		$prep=$db->prepare($requete);
		$prep->bindParam(":id",$_POST["id_materiel"],PDO::PARAM_INT);
                $prep->bindParam(":id_zone",$_POST['id_zone2'],PDO::PARAM_INT);
		$prep->bindParam(":id_modele",$_POST["id_modele"],PDO::PARAM_INT);
		$prep->bindParam(":id_type",$_POST["id_type2"],PDO::PARAM_INT);
	    $prep->bindParam(":nom",$_POST["nom"],PDO::PARAM_STR);
		$prep->bindParam(":sn",$_POST["sn"],PDO::PARAM_STR);
		$prep->bindParam(":systeme",$_POST["systeme"],PDO::PARAM_STR);
		$prep->bindParam(":systeme_version",$_POST["systeme_version"],PDO::PARAM_STR);
		if (!empty($_POST["date_installe"])){
			$tmpDate=explode('-',$_POST["date_installe"]);
			$_POST["date_installe"]="$tmpDate[2]-$tmpDate[1]-$tmpDate[0]";
			$prep->bindParam(":date_installe",$_POST["date_installe"],PDO::PARAM_STR);
		}		
		if (!empty($_POST['id_zone'])){
			$prep->bindParam(":id_zone",$_POST["id_zone"],PDO::PARAM_STR);
		}
		$prep->bindParam(":emplacement",$_POST["emplacement"],PDO::PARAM_STR);
		$prep->bindParam(":description",$_POST["description"],PDO::PARAM_STR);
		$prep->bindParam(":contact",$_POST["contact"],PDO::PARAM_STR);
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
		$requete="DELETE FROM materiel WHERE id = :id;";		
		$prep=$db->prepare($requete);
		$prep->bindParam(":id",$_POST["id_materiel"],PDO::PARAM_INT);
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