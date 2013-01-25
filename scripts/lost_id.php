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
          
          // Vérification de la validité des champs
          if (!filter_var($_POST["TB_Adresse_Email"], FILTER_VALIDATE_EMAIL)){
               echo "Votre adresse e-mail n'est pas valide";              
          }else{               
               // Connexion à la base de données              
          	   connectSQL();  
          	   include_once("../include/protect_var.php");
          	   $reqTest="SELECT id,actif,login FROM user WHERE mail1='$_POST[TB_Adresse_Email]'";
          	   $recTest=mysql_query($reqTest);
          	   if (mysql_num_rows($recTest)>0){
          	   		$resTest=mysql_fetch_array($recTest);
          	   		if ($resTest['actif']==1){                                                                                                       
                              // Envoi du mail d'activation
                              $sujet = "Votre identifiant";
                              $message ="Vous avez demandé la communication de vote identifiant\n".
                              $message ="Votre identifiant est : $resTest[login]\n\n";                              
                              $mail = new PHPMailer();
                              $mail->IsSMTP();
                              #$mail->Host = $GLOBALS['params']['appli']['exp_name']['smtp_host'];
                              $mail->From = $GLOBALS['params']['appli']['exp_mail'];
                              $mail->FromName = $GLOBALS['params']['appli']['exp_name'];
                              $mail->AddAddress($_POST["TB_Adresse_Email"]);
                              $mail->Subject = $sujet;
                              $mail->Body = utf8_decode($message.$GLOBALS['textes']['mail']['PS'].$GLOBALS['textes']['mail']['sign']);                             
                              // Si une erreur survient
                              if ( !$mail->Send() ) {
                                  echo "Une erreur est survenue lors de l'envoi du mail";                                                                   
                              }else{
                                   
                                   // Message de confirmation
                                   echo "Un email vient de vous être envoyé avec les informations demandées";                                                                                                       
                                   
                              }                              
          	   		}else{
          	   			echo "Ce compte n'est pas actif, vous ne pouvez pas réinitialiser le mot de passe !";
          	   		}                 
          	   }else{
          	   	echo "Cette adresse mail n'existe pas !";
          	   }      
               // Fermeture de la connexion à la base de données
               mysql_close();
	               
          }                             
     }     
}