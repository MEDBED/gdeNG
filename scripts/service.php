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
    //Get records from database    
    $requete="SELECT * from ipservice WHERE id_type=:id_type;";
    $prep=$db->prepare($requete);
    $prep->bindParam(":id_type",$_GET[id_type],PDO::PARAM_INT);
    $prep->execute();
    $recordCount=$prep->rowCount();
    $rows = array();
    $row='<table style="width: 100%; border: solid 1px #000; border-spacing: 0px;border-collapse: collapse; "><tr style="background-color: #537793; color: #fff; font-weight: bold;"><th >Service</th><th>Port</th><th>Lien direct</th></tr>';
    while($rows= $prep->fetch(PDO::FETCH_ASSOC)){
        //$row.=$rows[detail].' ('.$rows[port].') : <a href="'.$rows[protocol].'://'.$_GET[ip].':'.$rows[port].'" target="_blank">'.$rows[protocol].'://'.$_GET[ip].':'.$rows[port].'</a><br/>';
        $row.='<tr><td>'.$rows[detail].'</td><td>'.$rows[port].'</td><td><a href="'.$rows[protocol].'://'.$_GET[ip].':'.$rows[port].'" target="_blank">'.$rows[protocol].'://'.$_GET[ip].':'.$rows[port].'</a></td></tr>';
    }
    //$rows = $prep->fetchAll();
    $prep->closeCursor();
    $prep = NULL;
    //Return result to jTable
    $jTableResult = array();
    $jTableResult['Result'] = "OK";	
    $jTableResult['TotalRecordCount'] = $recordCount;
    $jTableResult['Records'] = $row;		
    //print json_encode($jTableResult);	
    $row.='</tbale>';
    echo $row;
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