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
        $_SESSION['alias_entite'] = $_POST['alias'];	
	echo "0";//Code de retour OK
}elseif (isset($_GET[autocomplete])){
	$arr=array();	
	foreach ($_SESSION['ENTITE'] as $tabEntite){
		//$row=array('alias'=>$tabEntite['alias']);
		if (preg_match("/$_GET[alias_startsWith]/i",$tabEntite['alias']) 
				|| preg_match("/$_GET[alias_startsWith]/i",$tabEntite['nom']) 
				|| preg_match("/$_GET[alias_startsWith]/i",$tabEntite['ville'])
                                || preg_match("/$_GET[alias_startsWith]/i",$tabEntite['type'])
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
         connectSQL();
        //$requete="SELECT a.id_materiel, a.ip, a.mask, a.subnet,b.id_zone,c.detail as zone, d.detail as marque, e.detail as modele FROM net a , materiel b, zone c, marque d, modele e WHERE a.id_materiel=b.id AND b.id_modele=e.id AND d.id=e.id_marque AND b.id_zone=c.id AND c.id_pere=3 AND id_entite=:id_entite ORDER BY b.id_zone,INET_ATON(ip);";
        $requete="SELECT DISTINCT a.mask, a.subnet FROM net a, materiel b WHERE a.id_materiel=b.id AND b.id_zone=9 AND id_entite=:id_entite AND a.subnet!='' ORDER BY b.id_zone,INET_ATON(ip);";
        $prep=$db->prepare($requete);
        $prep->bindParam(":id_entite",$_SESSION['id_entite'],PDO::PARAM_INT);	
        $prep->execute();    
        $network='<b>Réseaux de l\'entité :</b></br>';
        while ($res=$prep->fetch(PDO::FETCH_ASSOC)){
            $network.=''.$res[subnet].' / '.$res[mask].'<br/>';
        }
        $_SESSION[titre_network]=$network;
	echo $_SESSION[titre].'@@'.$_SESSION[titre_adresse].'@@'.$_SESSION[titre_personnel].'@@'.$_SESSION[titre_network];
}else{
	echo "1";//code de retour ECHEC
}
//echo "**$_POST[entite]**";
//print_r($_POST);
?>