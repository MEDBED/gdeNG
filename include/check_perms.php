<?php
if(!isset($_COOKIE["ID_UTILISATEUR"])){
	header("Location: ../logout.php");
	exit;
}
session_start();
$_SESSION['id_zone']=$_GET['id_zone'];
$tabPerm=array('materiel.php','entite.php');
if (in_array($page,$tabPerm)){	
	if (empty($_SESSION['id_entite'])){
		echo "<div id=\"errPerms\">Vous devez sélectionner une entité pour accèder à cette page</div>";exit;
	}
	if (!((int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & LECTURE)){
		echo "<div id=\"errPerms\">Vous n'avez pas les droits nécessaire pour accèder à ce type d'entité</div>";exit;
	}	
	if (!((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone']] & LECTURE)){
		echo "<div id=\"errPerms\">Vous n'avez pas les droits nécessaire pour accèder à cette zone</div>";exit;
	}
}
?>