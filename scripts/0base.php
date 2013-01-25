<?php
// Redirige l'utilisateur s'il est déjà identifié
if(!isset($_COOKIE["ID_UTILISATEUR"])){
      header("Location: ../index.php");
}else{
	include_once("../header.inc.php");
	include_once("../include/functions.php");	
	/*require_once('../include/phpmailer/class.phpmailer.php');*/         
     // Une fois le formulaire envoyé
     $tabChampObli=array();
     foreach ($tabChampObli as $champ){
     	if (!isset($_POST[$champ])){
     		setcookie("MESSAGE", "Le champ $champ est obligatoire");
     		retour($_POST['page']);
     		exit;
     	}
     }
     if(isset($_POST["valid"])){          
          // Vérification de la validité des champs
          if(!preg_match("/^[A-Za-z0-9_]{5,20}$/", $_POST["TB_Nom_Utilisateur"])){
               echo "Votre nom d'utilisateur doit comporter entre 5 et 20 caractères<br />\n";
               $_SESSION['MESSAGE'] .= "L'utilisation de l'underscore est autorisée";
          }elseif(!preg_match("/^[A-Za-z0-9]{8,}$/", $_POST["TB_Mot_de_Passe"]))
          {
               echo "Votre mot de passe doit comporter au moins 8 caractères";
          }elseif($_POST["TB_Mot_de_Passe"] != $_POST["TB_Confirmation_Mot_de_Passe"])
          {
               echo "Votre mot de passe n'a pas été correctement confirmé";        
		  }elseif (!filter_var($_POST["TB_Adresse_Email"], FILTER_VALIDATE_EMAIL))          
          {
               echo "Votre adresse e-mail n'est pas valide";
          }else{               
               // Connexion à la base de données
          	   connectSQL();          	
          	   include_once("../include/protect_var.php");
               // Vérification de l'unicité
               $result = mysql_query("SELECT id_logement FROM asm WHERE id_user='" . $_COOKIE["ID_UTILISATEUR"] . "';");             
               // Si une erreur survient
               if(mysql_num_rows($result)>0){
                     // Mise à jour
                     $req="UPDATE user SET login='$_POST[login]' WHERE id_user='$_COOKIE[ID_UTILISATEUR]'";                     
                     $result = mysql_query($req);
               }else{                    
                     // Création
                     $req="INSERT INTO user(login) VALUES('" . $_POST["TB_Nom_Utilisateur"] . "')";                       
                     $result = mysql_query($req);                         
                     // Si une erreur survient
                     if(!$result){
                     	echo "Erreur d'accès à la base de données";
                     }else{                                                                                        
					 	//Message de confirmation
                        echo "Enregistrement effectué";                                                                                                                  
                     }                                                                       
               }      
               // Fermeture de la connexion à la base de données
               mysql_close();
          }                             
     }     
}
?>
