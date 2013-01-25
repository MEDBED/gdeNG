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
	$tabFiltre=array("nom"=>"Fnom","editeur"=>"Fediteur");     
	//Getting records (listAction)
	if($_GET["action"] == "list")
	{	                                           
                $requete="SELECT count(id) as recordCount FROM ipservice ";		
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
		$requete="SELECT a.*,b.detail as type FROM ipservice a, type b WHERE a.id_type=b.id ";// 
		foreach ($tabFiltre as $search=>$champ){
                    if (!empty($_POST[$champ])){$requete.="AND $search LIKE :$champ ";}
		}
                if (empty($_GET["jtSorting"])){
                    $requete.="ORDER BY id_type,detail ";
                }else{
                    $requete.="ORDER BY ".$_GET["jtSorting"]." ";
                }
		$requete.="LIMIT ".$_GET["jtStartIndex"]."," . $_GET["jtPageSize"] . ";";				
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
            $requete = "INSERT INTO ipservice(id_type,detail,port,protocol)";		
            $requete.=" VALUES(:id_type,:detail,:port,:protocol)";		
            $prep=$db->prepare($requete);
            $prep->bindParam(":id_type",$_POST['id_type'],PDO::PARAM_INT);		
            $prep->bindParam(":detail",$_POST['detail'],PDO::PARAM_STR);		
            $prep->bindParam(":port",$_POST['port'],PDO::PARAM_INT);		
            $prep->bindParam(":protocol",$_POST['protocol'],PDO::PARAM_STR);	

            $prep->execute();
            $id_nouveau = $db->lastInsertId();
            $prep = NULL;		
            //Get last inserted record (to return to jTable)		
            $requete = "SELECT * FROM ipservice WHERE id = :id;";
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
		$requete = 'UPDATE ipservice SET id_type=:id_type,detail=:detail,port=:port,protocol=:protocol ';	
                $requete.='WHERE id=:id';                                   
		$prep=$db->prepare($requete);
                $prep->bindParam(":id",$_POST['id'],PDO::PARAM_INT);		
		$prep->bindParam(":id_type",$_POST['id_type'],PDO::PARAM_INT);		
                $prep->bindParam(":detail",$_POST['detail'],PDO::PARAM_STR);		
                $prep->bindParam(":port",$_POST['port'],PDO::PARAM_INT);		
                $prep->bindParam(":protocol",$_POST['protocol'],PDO::PARAM_STR);	
                              
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
		$requete="DELETE FROM ipservice WHERE id = :id;";		
		$prep=$db->prepare($requete);
		$prep->bindParam(":id",$_POST["id"],PDO::PARAM_INT);
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
	$jTableResult['Message'] = '';
	print json_encode($jTableResult);
}
	
?>