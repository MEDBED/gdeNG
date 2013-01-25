<?php
if(!isset($_COOKIE["ID_UTILISATEUR"])){
	header("Location: logout.php");
	exit;
}
session_start();
include_once("../header.inc.php");
include_once("../include/functions.php");
include_once("../include/protect_var.php");
if (isset($_GET[entite])){
	$_SESSION['id_entite'] = $_POST['entite'];	
	echo "0";//Code de retour OK
}elseif (isset($_GET[autocomplete])){
	$arr=array();	
	foreach ($_SESSION['ENTITE'] as $tabEntite){
		//$row=array('alias'=>$tabEntite['alias']);
		if (preg_match("/$_GET[alias_startsWith]/i",$tabEntite['alias']) 
				|| preg_match("/$_GET[alias_startsWith]/i",$tabEntite['nom']) 
				|| preg_match("/$_GET[alias_startsWith]/i",$tabEntite['ville'])				
				){
			$arr[]=array('alias'=>$tabEntite['alias'],'id_entite'=>$tabEntite['id_entite'],'type'=>$tabEntite['type'],'nom'=>$tabEntite['nom'],'ville'=>$tabEntite['ville']);	
			$nbResult++;
		}	
	}
	
	$jTableResult['totalResultsCount'] = $nbResult;
	$jTableResult['alias'] = $arr;
	echo json_encode($arr);
	//echo "0";//Code de retour OK
}elseif (isset($_GET[titre])){
	$_SESSION[titre]=$_SESSION['ENTITE'][$_SESSION['id_entite']][type].' '.$_SESSION['ENTITE'][$_SESSION['id_entite']][nom].' - '.$_SESSION['ENTITE'][$_SESSION['id_entite']][alias].'<br/>'.$_SESSION['ENTITE'][$_SESSION['id_entite']][tel];	
	$_SESSION[titre_adresse]='Adresse : '.$_SESSION['ENTITE'][$_SESSION['id_entite']][adresse].'<br/>'
	.'CP : '.$_SESSION['ENTITE'][$_SESSION['id_entite']][cp].'<br/>'
	.'Ville : '.$_SESSION['ENTITE'][$_SESSION['id_entite']][ville].'<br/>'
	.'Tél : '.$_SESSION['ENTITE'][$_SESSION['id_entite']][tel].'<br/>'
	.'Fax : '.$_SESSION['ENTITE'][$_SESSION['id_entite']][fax].'<br/>'
	.'Email : '.$_SESSION['ENTITE'][$_SESSION['id_entite']][email];
	$_SESSION[titre_personnel]='Directeur : '.$_SESSION['ENTITE'][$_SESSION['id_entite']][directeur].'<br/>'
	.'Adjoint : '.$_SESSION['ENTITE'][$_SESSION['id_entite']][adjoint].'<br/>'
	.'Gestionnaire : '.$_SESSION['ENTITE'][$_SESSION['id_entite']][gestionnaire];
	echo $_SESSION[titre].'@@'.$_SESSION[titre_adresse].'@@'.$_SESSION[titre_personnel];
}else{
	echo "1";//code de retour ECHEC
}
//echo "**$_POST[entite]**";
//print_r($_POST);
?>