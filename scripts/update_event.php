<?php
session_start();
// Redirige l'utilisateur s'il est déjà identifié
if(!isset($_COOKIE["ID_UTILISATEUR"])){
      header("Location: ../index.php");
}else{
	include_once("../header.inc.php");
	include_once("../include/functions.php");	
	/*require_once('../include/phpmailer/class.phpmailer.php');*/         
     // Une fois le formulaire envoyé
     $tabChampObli=array('');  
     $tabChampObli=array("event","date_debut","heure_debut","date_fin","heure_fin","description");       
     foreach ($tabChampObli as $champ){
     	if (!isset($_POST[$champ])){
     		echo "Le champ $champ est obligatoire";
     		exit;
     	}
     }     
    // print_r($_POST["dupliqId"]);exit;
     if(isset($_POST["awq"])){
               // Connexion à la base de données
     	
          	   connectSQL();              	   
          	   $tabId=array();
          	   $tabId=$_POST["dupliqId"];
          	   $idForCal=trim(dechiffre(hex2bin("$_POST[awq]"), "$_SESSION[UNIQID]"));
          	   include_once("../include/protect_var.php");           	     
          	   $dateDebut=$_POST[date_debut].' '.$_POST[heure_debut].':00';
          	   $dateFin=$_POST[date_fin].' '.$_POST[heure_fin].':00';
          	   $req = "insert into `jqcalendar` (`subject`,`description`,`starttime`,`endtime`,`isalldayevent`,`type_user`,`color`,`create_by`,`id_user`) values ('"
          	   .mysql_real_escape_string($_POST[event])."', '"
          	   .mysql_real_escape_string($_POST[description])."', '"
          	   .$dateDebut."', '"
          	   .$dateFin."', '"
          	   .mysql_real_escape_string(0)."', '"          	  
          	   .mysql_real_escape_string('enfant')."', '7', '"
          	   .mysql_real_escape_string($_COOKIE[ID_UTILISATEUR])."','";          	   
          	    $req.=mysql_real_escape_string($idForCal)."')";            	   
          	     	                	                                                       
          	   if ($_POST["dupliq"]=='on'){
          	   	foreach($tabId as $id){
          	   		$req.=",('".
          	   	mysql_real_escape_string($_POST[event])."', '"
          	   .mysql_real_escape_string($_POST[description])."', '"
          	   .$dateDebut."', '"
          	   .$dateFin."', '"
          	   .mysql_real_escape_string(0)."', '"          	  
          	   .mysql_real_escape_string('enfant')."', '7', '"
          	   .mysql_real_escape_string($_COOKIE[ID_UTILISATEUR])."','";          	   
          	    $req.=mysql_real_escape_string($id)."')";            	         	   		
          	   	}
          	   }              
          	 // echo $req;exit;         	   
          	   $result = @mysql_query($req);
          	   //Si une erreur survient
          	   if(!$result){
          	   	echo "Erreur d'accès à la base de données";
          	   }else{
          	   	//Message de confirmation
          	   	echo "Enregistrement effectué";
          	   }
     }     
}
?>