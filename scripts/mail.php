<?php
// Redirige l'utilisateur s'il est déjà identifié
if(!isset($_COOKIE["ID_UTILISATEUR"])){
      header("Location: ../index.php");
}else{
	include_once("../header.inc.php");
	include_once("../include/functions.php");
	include_once("../include/textes.php");
	require_once('../include/phpmailer/class.phpmailer.php');         
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
         if (!filter_var($_POST["mail"], FILTER_VALIDATE_EMAIL)){
               echo "Votre adresse e-mail n'est pas valide";
         }elseif (empty($_POST["data"])){
               echo "Vous devez renseigner le corps du message";
         }elseif (empty($_POST["objet"])){
               	echo "Vous devez renseigner l'objet du message";
         }else{               
         	$mail = new PHPMailer();
            $mail->IsSMTP();            
            $mail->From = $GLOBALS['params']['appli']['exp_mail'];
            $mail->FromName = $GLOBALS['params']['appli']['exp_name'];
            $mail->AddAddress($_POST["mail"]);
            $mail->Subject = $_POST['objet'];
            $mail->Body = utf8_decode($_POST['data'].$GLOBALS['textes']['mail']['PS'].$GLOBALS['textes']['mail']['sign']);            
			if ( !$mail->Send() ) {
            	echo "Une erreur est survenue lors de l'envoi du mail";                                                                   
            }else{            
				// Message de confirmation
                echo "Le mail a été envoyé !";                                                                     
                // On masque le formulaire
                $masquer_formulaire = true;                                   
			}                    
         }                             
     }     
}
?>
