<?php
if(!isset($_COOKIE["ID_UTILISATEUR"])){
	header("Location: ../index.php");
	exit;
}
session_start();
setcookie("MESSAGE", "", time() - 1, "/");
// Redirige l'utilisateur s'il est déjà identifié
if(!isset($_COOKIE["ID_UTILISATEUR"])){
      header("Location: ../index.php");
}else{		
	include_once("../header.inc.php");
	include_once("../include/functions.php");	
	/*require_once('../include/phpmailer/class.phpmailer.php');*/         
     // Une fois le formulaire envoyé
     $tabChampObli=array("nom","alias","adresse","cp","ville","tel");
     foreach ($tabChampObli as $champ){
     	if (!isset($_POST[$champ])){
     		echo "Le champ $champ est obligatoire";     		
			exit;
     	}
     }    
     if(isset($_POST["valid"])){     	
          // Vérification de la validité des champs
          if ((!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) && !empty($_POST["email"]))){
               echo "Erreur de saisie sur l'adresse mail !";               
          }else{   	          	          	
            // Connexion à la base de données
          	connectSQL();          	
          	include_once("../include/protect_var.php");
          	//Vérification du pseudo          	   
          	if (!empty($_POST['pseudo'])){	
          		$req="SELECT id,alias FROM entite WHERE alias=:alias AND id!=:id;" ;
          	   	$prep=$db->prepare($req);
          	   	$prep->bindParam(':alias',$_POST[alias],PDO::PARAM_STR);
          	   	$prep->bindParam(':id',$_SESSION[id_entite],PDO::PARAM_INT);
          	    $prep->execute();            	    
          	    if (!$prep){
	          	   	echo "L'alias choisi est déjà utilisé";
	          	   	exit;
          	    }
          	    $prep->closeCursor();
          	    $prep = NULL;
          	}                   	                                                                                        
            // Modification de la table
            $req="UPDATE entite SET
            					   alias=:alias,
            					   id_type=:id_type,
                                   nom=:nom,
                                   adresse=:adresse,
                                   cp=:cp,
                                   ville=:ville,
                                   tel=:tel,
                                   fax=:fax,
                                   email=:email,
                                   directeur=:directeur,
                                   adjoint=:adjoint,
                                   gestionnaire=:gestionnaire,
            					   updateOn=now()";              
               $req.=" WHERE id=:id;";     
               $prep=$db->prepare($req);
               $prep->bindParam(':id',$_SESSION[id_entite],PDO::PARAM_INT);
               $prep->bindParam(':id_type',$_POST[id_type],PDO::PARAM_INT);
               $prep->bindParam(':alias',$_POST[alias],PDO::PARAM_STR);
               $prep->bindParam(':nom',$_POST[nom],PDO::PARAM_STR);               
               $prep->bindParam(':adresse',$_POST[adresse],PDO::PARAM_STR);
               $prep->bindParam(':cp',$_POST[cp],PDO::PARAM_INT);
               $prep->bindParam(':ville',$_POST[ville],PDO::PARAM_STR);
               $prep->bindParam(':tel',$_POST[tel],PDO::PARAM_STR);
               $prep->bindParam(':fax',$_POST[fax],PDO::PARAM_STR);               
               $prep->bindParam(':email',$_POST[email],PDO::PARAM_STR);               
               $prep->bindParam(':directeur',$_POST[directeur],PDO::PARAM_STR);
               $prep->bindParam(':adjoint',$_POST[adjoint],PDO::PARAM_STR);
               $prep->bindParam(':gestionnaire',$_POST[gestionnaire],PDO::PARAM_STR);               
			   $prep->execute();          	    
				// Si une erreur survient
				if(!$prep){
					echo "Erreur d'accès à la base de données";
				}else{
					//Message de confirmation
					echo "L'entité a été modifiée";
				}
          	    $prep->closeCursor();
          	    $prep = NULL;
          	    //Modification des variables de session
          	    $tabRne[$_SESSION[id_entite]][id_entite]=$_SESSION[id_entite];
          	    $tabRne[$_SESSION[id_entite]][alias]=$_POST['alias'];
          	    $tabRne[$_SESSION[id_entite]][id_type]=$_POST['id_type'];
          	    $tabRne[$_SESSION[id_entite]][type]=$resRne['type'];
          	    $tabRne[$_SESSION[id_entite]][nom]=$_POST['nom'];
          	    $tabRne[$_SESSION[id_entite]][ville]=$_POST['ville'];
          	    $tabRne[$_SESSION[id_entite]][adresse]=$_POST['adresse'];
          	    $tabRne[$_SESSION[id_entite]][cp]=$_POST['cp'];
          	    $tabRne[$_SESSION[id_entite]][tel]=$_POST['tel'];
          	    $tabRne[$_SESSION[id_entite]][fax]=$_POST['fax'];
          	    $tabRne[$_SESSION[id_entite]][email]=$_POST['email'];
          	    $tabRne[$_SESSION[id_entite]][directeur]=$_POST['directeur'];
          	    $tabRne[$_SESSION[id_entite]][adjoint]=$_POST['adjoint'];
          	    $tabRne[$_SESSION[id_entite]][gestionnaire]=$_POST['gestionnaire'];
          }                             
     }     
}
?>