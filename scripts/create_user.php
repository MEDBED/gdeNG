<?php
// Redirige l'utilisateur s'il est déjà identifié
session_start();
if(isset($_COOKIE["ID_UTILISATEUR"])){
      header("Location: ../menu.php");
}else{
	include_once("../header.inc.php");
	include_once("../include/functions.php");	
	include_once("../include/textes.php");
	require_once('../include/phpmailer/class.phpmailer.php');
     // Formulaire visible par défaut
     $masquer_formulaire = false;
     
     // Une fois le formulaire envoyé
     if(isset($_POST["valid"])){
          if ($_POST["cgu"]=='on'){
	          // Vérification de la validité des champs
	          if(!preg_match("/^[A-Za-z0-9_]{5,20}$/", $_POST["TB_Nom_Utilisateur"])){
	               echo "Votre nom d'utilisateur doit comporter entre 5 et 20 caractères";               
	          }elseif(!preg_match("/^[A-Za-z0-9]{8,}$/", $_POST["TB_Mot_de_Passe"])){
	               echo "Votre mot de passe doit comporter au moins 8 caractères";               
	          }elseif($_POST["TB_Mot_de_Passe"] != $_POST["TB_Confirmation_Mot_de_Passe"]){
	               echo "Votre mot de passe n'a pas été correctement confirmé";               
			  }elseif (!filter_var($_POST["TB_Adresse_Email"], FILTER_VALIDATE_EMAIL)){
	               echo "Votre adresse e-mail n'est pas valide";              
	          }else{               
	               // Connexion à la base de données              
	          	   connectSQL();  
	          	   include_once("../include/protect_var.php");
	               // Vérification de l'unicité du nom d'utilisateur et de l'adresse e-mail
	               $result = mysql_query("
	                    SELECT login, mail1
	                    FROM user
	                    WHERE login = '" . $_POST["TB_Nom_Utilisateur"] . "'
	                    OR mail1 = '" . $_POST["TB_Adresse_Email"] . "'
	               ");
	               
	               // Si une erreur survient
	               if(!$result){
	                    echo "Erreur d'accès à la base de données !";                                       
	               }else{                    
	                    // Si un enregistrement est trouvé
	                    if(mysql_num_rows($result) > 0){                         
	                         while($row = mysql_fetch_array($result)){
	                              if($_POST["TB_Nom_Utilisateur"] == $row["login"]){
	                                   echo "Le nom d'utilisateur " . $_POST["TB_Nom_Utilisateur"];
	                                   echo " est déjà utilisé";
	                                   exit;
	                              }elseif($_POST["TB_Adresse_Email"] == $row["mail1"]){
	                                   echo "L'adresse e-mail " . $_POST["TB_Adresse_Email"];
	                                   echo " est déjà utilisée";
	                                   exit;
	                              }
	                              
	                         }
	                         
	                    }else{                        
	                         // Génération de la clef d'activation
	                         $caracteres = array("a", "b", "c", "d", "e", "f", 0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
	                         $caracteres_aleatoires = array_rand($caracteres, 8);
	                         $clef_activation = "";
	                         
	                         foreach($caracteres_aleatoires as $i){
	                              $clef_activation .= $caracteres[$i];
	                         }
	                         $uniqId=uniqid();
	                         // Création du compte utilisateur
	                         $req="
	                              INSERT INTO user(
	                                   login
	                                   , password
	                                   , mail1
	                                   , date_inscription
	                                   , clef_activation
	                                   , uniqId
	                                   , cgu
	                              )
	                              VALUES(
	                                   '" . $_POST["TB_Nom_Utilisateur"] . "'
	                                   , '" . md5($_POST["TB_Mot_de_Passe"]) . "'
	                                   , '" . $_POST["TB_Adresse_Email"] . "'
	                                   , '" . date('Y-m-d') . "'
	                                   , '" . $clef_activation . "'
	                                   , '" . $uniqId . "'
	                                   , '1'
	                              )
	                         ";                       
	                         $result = mysql_query($req);
	                         
	                         // Si une erreur survient
	                         if(!$result){
	                              echo "Erreur d'accès à la base de données lors de la création du compte utilisateur";                                                         
	                         }else{                              
	                              // Envoi du mail d'activation
	                              $sujet = "Activation de votre compte utilisateur";
	                              
	                              $message = "Pour valider votre inscription, merci de cliquer sur le lien suivant :\n";
	                              $message .= $GLOBALS['params']['appli']['proto']."://" . $_SERVER["SERVER_NAME"];
	                              $message .= $GLOBALS['params']['appli']['root_folder']."/resources/activate_user.php?id=" . mysql_insert_id();
	                              $message .= "&activation=" . $clef_activation;
	                              $mail = new PHPMailer();
	                              $mail->IsSMTP();                                                        
	                              #$mail->Host = $GLOBALS['params']['appli']['exp_name']['smtp_host'];
	                              $mail->From = $GLOBALS['params']['appli']['exp_mail'];
	                              $mail->FromName = $GLOBALS['params']['appli']['exp_name'];
	                              $mail->AddAddress($_POST["TB_Adresse_Email"]);
	                              $mail->Subject = $sujet;
	                              $mail->Body = utf8_decode($message.$GLOBALS['textes']['mail']['PS'].$GLOBALS['textes']['mail']['sign']);
	                             // $mail->Send();
	                              // Si une erreur survient
	                              //if(!@mail($_POST["TB_Adresse_Email"], $sujet, $message)){
	                              if ( !$mail->Send() ) {
	                                  echo "Une erreur est survenue lors de l'envoi du mail d'activation";                                                                   
	                              }else{
	                                   
	                                   // Message de confirmation
	                                   echo "Compte créé ! Un email vient de vous être envoyé afin de l'activer";                                                                     
	                                   // On masque le formulaire
	                                   $masquer_formulaire = true;
	                                   
	                              }                              
	                         }                        
	                    }                     
	               }      
	               // Fermeture de la connexion à la base de données
	               mysql_close();
	          } 
          }else{
          	echo "Vous devez lire et accepter les Conditions Générales d'Utilisation";
          }
	                                   
     }     
}