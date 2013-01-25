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
		$check=0;		
		$limHaute=$_GET["jtStartIndex"]+$_GET["jtPageSize"];
		//Get records from database
		if($_GET['source2']=='zone'){
			$requete="SELECT a.id as id_source,detail,b.source as source2,b.id as id_perm,id_u_or_g as id_user,perms FROM zone a LEFT OUTER JOIN perms b ON a.id=b.id_source WHERE b.id_u_or_g=:id_u_or_g AND b.type=:type AND b.source='zone' ORDER BY ".$_GET["jtSorting"]." LIMIT ".$_GET["jtStartIndex"]."," . $limHaute . "";
		}elseif($_GET['source2']=='entite'){
			$requete="SELECT a.id as id_source,detail,b.source as source2,b.id as id_perm,id_u_or_g as id_user,perms FROM type a LEFT OUTER JOIN perms b ON a.id=b.id_source WHERE b.id_u_or_g=:id_u_or_g AND b.type=:type AND b.source='entite' ORDER BY a.source,".$_GET["jtSorting"]." LIMIT ".$_GET["jtStartIndex"]."," . $limHaute . "";
		}
//		$requete="SELECT a.id as id_source,detail,b.source as source2,b.id as id_perm,id_u_or_g as id_user,perms FROM zone a, perms b GROUP BY a.detail ORDER BY a.detail";
		$prep=$db->prepare($requete);
		$prep->bindParam(":id_u_or_g",$_GET["id_user"],PDO::PARAM_INT);
		$prep->bindParam(":type",$_GET["type"],PDO::PARAM_STR);
		$prep->execute();
		$recordCount=$prep->rowCount();		
		$rows = array();	
		$notIN='';	
		while ($res=$prep->fetch(PDO::FETCH_ASSOC)){
			if ($res['perms'] & LECTURE){
				$res['lecture']="1";
			}else{
				$res['lecture']="0";
			}
			if ($res['perms'] & MODIFICATION){
				$res['modification']="1";
			}else{
				$res['modification']="0";
			}
			if ($res['perms'] & CREATION){
				$res['creation']="1";
			}else{
				$res['creation']="0";
			}
			if ($res['perms'] & SUPPRESSION){
				$res['suppression']="1";
			}else{
				$res['suppression']="0";
			}
			if ($notIN==''){
				$notIN.=$res['id_source'];
			}else{
				$notIN.=','.$res['id_source'];
			}
			//$res[]=array("lecture"=>"1");
			$rows[]=$res;
			$check++;
		}
		
		$prep->closeCursor();
		$prep = NULL;
		if (empty($notIN)){$notIN="''";}
		if($_GET['source2']=='zone'){
			$requete="SELECT a.id as id_source,detail,b.source as source2,b.id as id_perm,id_u_or_g as id_user,perms FROM zone a LEFT OUTER JOIN perms b ON 1=2 WHERE a.id NOT IN ($notIN) ORDER BY ".$_GET["jtSorting"]." LIMIT ".$_GET["jtStartIndex"]."," . $limHaute . ";";
		}elseif($_GET['source2']=='entite'){
			$requete="SELECT a.id as id_source,detail,b.source as source2,b.id as id_perm,id_u_or_g as id_user,perms FROM type a LEFT OUTER JOIN perms b ON 1=2 WHERE a.id NOT IN ($notIN) AND (a.source='etab' OR a.source='service') ORDER BY a.source,".$_GET["jtSorting"]." LIMIT ".$_GET["jtStartIndex"]."," . $limHaute . ";";
		}		
		$prep=$db->prepare($requete);
		$prep->bindParam(":id_u_or_g",$_GET["id_user"],PDO::PARAM_INT);
		$prep->execute();
		$recordCount+=$prep->rowCount();
		while ($res=$prep->fetch(PDO::FETCH_ASSOC)){
			if ($res['perms'] & LECTURE){
				$res['lecture']="1";
			}else{
				$res['lecture']="0";
			}
			if ($res['perms'] & MODIFICATION){
				$res['modification']="1";
			}else{
				$res['modification']="0";
			}
			if ($res['perms'] & CREATION){
				$res['creation']="1";
			}else{
				$res['creation']="0";
			}
			if ($res['perms'] & SUPPRESSION){
				$res['suppression']="1";
			}else{
				$res['suppression']="0";
			}
			//$res[]=array("lecture"=>"1");
			$rows[]=$res;
		}						
		//$rows = $prep->fetchAll();
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
	//Updating a record (updateAction)
	else if($_GET["action"] == "update" && !empty($_POST['id_perm']))
	{
		$perms=0;
		if ($_POST['lecture']==1){
			$perms=$perms+1;
		}
		if ($_POST['modification']==1){
			$perms=$perms+2;
		}
		if ($_POST['creation']==1){
			$perms=$perms+4;
		}
		if ($_POST['suppression']==1){
			$perms=$perms+8;
		}
		//Update record in database
		$requete="UPDATE perms SET id_source=:id_source,source=:source,perms=:perms,type=:type,id_u_or_g=:id_u_or_g WHERE id= :id_perm AND source=:source;";
		$prep=$db->prepare($requete);
		$prep->bindParam(":id_source",$_POST["id_source"],PDO::PARAM_INT);
		$prep->bindParam(":id_perm",$_POST["id_perm"],PDO::PARAM_INT);
		$prep->bindParam(":id_u_or_g",$_GET["id_user"],PDO::PARAM_INT);
		$prep->bindParam(":source",$_GET["source2"],PDO::PARAM_STR);
		$prep->bindParam(":perms",$perms,PDO::PARAM_INT);
		$prep->bindParam(":type",$_GET["type"],PDO::PARAM_STR);
		$prep->execute();
		$prep->closeCursor();
		$prep = NULL;
		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		print json_encode($jTableResult);
	}
	//Création
	else if($_GET["action"] == "update" && empty($_POST['id_perm']))
	{		
		$perms=0;
		if ($_POST['lecture']==1){
			$perms=$perms+1;
		}
		if ($_POST['modification']==1){
			$perms=$perms+2;
		}
		if ($_POST['creation']==1){
			$perms=$perms+4;
		}
		if ($_POST['suppression']==1){
			$perms=$perms+8;
		}
		$requete="INSERT INTO perms(id_source,source,perms,type,id_u_or_g) VALUES(:id_source,:source,:perms,:type,:id_u_or_g)";
		$prep=$db->prepare($requete);
		$prep->bindParam(":id_source",$_POST["id_source"],PDO::PARAM_INT);		
		$prep->bindParam(":id_u_or_g",$_GET["id_user"],PDO::PARAM_INT);
		$prep->bindParam(":source",$_GET["source2"],PDO::PARAM_STR);
		$prep->bindParam(":perms",$perms,PDO::PARAM_INT);
		$prep->bindParam(":type",$_GET["type"],PDO::PARAM_STR);
		$prep->execute();
		$prep->closeCursor();
		$prep = NULL;
		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		print json_encode($jTableResult);
	}	

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