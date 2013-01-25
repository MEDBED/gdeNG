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
     $tabChampObli=array("nom","prenom","adresse","cp","ville");
     foreach ($tabChampObli as $champ){
     	if (!isset($_POST[$champ])){
     		echo "Le champ $champ est obligatoire";     		
			exit;
     	}
     }    
     if(isset($_POST["valid"])){     	
          // Vérification de la validité des champs
          if ((!filter_var($_POST["mail1"], FILTER_VALIDATE_EMAIL) && !empty($_POST["mail1"])) || (!filter_var($_POST["mail2"], FILTER_VALIDATE_EMAIL) && !empty($_POST["mail2"]))){
               echo "Erreur de saisie sur la ou les adresses mail !";               
          }else{   	          	
	      	$checkPass=0;
          	$urssaf=trim(bin2hex(chiffre("$_POST[num_urssaf]", "$GLOBALS[params][appli][key]")));          		          	
	        $secu=trim(bin2hex(chiffre("$_POST[num_secu]", "$GLOBALS[params][appli][key]")));
            // Connexion à la base de données
          	connectSQL();          	
          	include_once("../include/protect_var.php");
          	//Vérification du pseudo          	   
          	if (!empty($_POST['pseudo'])){	
          		$req="SELECT id,password FROM user WHERE pseudo=:pseudo AND id!=:id;" ;
          	   	$prep=$db->prepare($req);
          	   	$prep->bindParam(':pseudo',$_POST[pseudo],PDO::PARAM_STR);
          	   	$prep->bindParam(':id',$_COOKIE[ID_UTILISATEUR],PDO::PARAM_INT);
          	    $prep->execute();            	    
          	    if (!$prep){
	          	   	echo "Le pseudo choisi est déjà utilisé";
	          	   	exit;
          	    }
          	    $prep->closeCursor();
          	    $prep = NULL;
          	}       
            $req="SELECT id,password FROM user WHERE pseudo=:pseudo AND id=:id;" ;
          	$prep=$db->prepare($req);
           	$prep->bindParam(':pseudo',$_POST[pseudo],PDO::PARAM_STR);
           	$prep->bindParam(':id',$_COOKIE[ID_UTILISATEUR],PDO::PARAM_INT);
            $prep->execute();
            $res = $prep->fetch(PDO::FETCH_ASSOC);
            $prep->closeCursor();
            $prep = NULL;          	              	                                                                                         
            // Modification de la table
            $req="UPDATE user SET
                                  nom=:nom,
                                   prenom=:prenom,
                                   pseudo=:pseudo,
                                   adresse=:adresse,
                                   cp=:cp,
                                   ville=:ville,
                                   tel1=:tel1,
                                   tel2=:tel2,
                                   tel_por1=:tel_por1,
                                   tel_por2=:tel_por2,
                                   mail1=:mail1,
                                   mail2=:mail2,
                                   genre=:genre";
              if ($_POST['change_password']==1){                     	
               	if (md5($_POST['old_password'])==$res['password']){
               		if (preg_match("/^[A-Za-z0-9]{8,}$/", $_POST['new_password'])){
	               		if ($_POST['new_password']==$_POST['new_password2'] && !empty($_POST['new_password'])){
	               			$password=md5($_POST['new_password']);
	               			$req.=",password=:password";
	               			$checkPass=1;
	               		}else{
	               			echo "Le mot des passe n'a pas été correctement confirmé !";
	               			exit;
	               		}
               		}else{
               			 echo "Votre mot de passe doit comporter au moins 8 caractères";   
               			exit;
               		}
               	}else{
               		echo "Votre ancien mot de passe est incorrect !";
               		exit;
               	}              	
               }
               $req.=" WHERE id=$_COOKIE[ID_UTILISATEUR];";     
               $prep=$db->prepare($req);
               $prep->bindParam(':nom',$_POST[nom],PDO::PARAM_STR);
               $prep->bindParam(':prenom',$_POST[prenom],PDO::PARAM_STR);
               $prep->bindParam(':pseudo',$_POST[pseudo],PDO::PARAM_STR);
               $prep->bindParam(':adresse',$_POST[adresse],PDO::PARAM_STR);
               $prep->bindParam(':cp',$_POST[cp],PDO::PARAM_INT);
               $prep->bindParam(':ville',$_POST[ville],PDO::PARAM_STR);
               $prep->bindParam(':tel1',$_POST[tel1],PDO::PARAM_STR);
               $prep->bindParam(':tel2',$_POST[tel2],PDO::PARAM_STR);
               $prep->bindParam(':tel_por1',$_POST[tel_por1],PDO::PARAM_STR);
               $prep->bindParam(':tel_por2',$_POST[tel_por2],PDO::PARAM_STR);
               $prep->bindParam(':mail1',$_POST[mail1],PDO::PARAM_STR);               
               $prep->bindParam(':mail2',$_POST[mail2],PDO::PARAM_STR);
               $prep->bindParam(':genre',$_POST[genre],PDO::PARAM_STR);
               if ($checkPass==1){
               	$prep->bindParam(':password',$password,PDO::PARAM_STR);
               }
				$prep->execute();          	    
				// Si une erreur survient
				if(!$prep){
					echo "Erreur d'accès à la base de données";
				}else{
					//Message de confirmation
					echo "Votre compte a été modifié";
				}
          	    $prep->closeCursor();
          	    $prep = NULL;                                                                                                                              
          }                             
     }     
}
?>