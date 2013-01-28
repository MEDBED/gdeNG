<?php
$titre="Activation du compte";
// Redirige l'utilisateur s'il est déjà identifié
if(isset($_COOKIE["ID_UTILISATEUR"])){	
     header("Location: ../menu.php");    
}else{
     // Vérifie que de bonnes valeurs sont passées en paramètres
     if(!preg_match("/^[0-9]+$/", $_GET["id"]) || !preg_match("/^[a-f0-9]{8}$/", strtolower($_GET["activation"])))
     {
          header("Location: index.php");
     }else{
     	  include_once("../header.inc.php");
          include_once("../include/functions.php");
          // Connexion à la base de données
          // Valeurs à modifier selon vos paramètres configuration
          connectSQL();          
          // Sélection de l'utilisateur concerné
          $result = mysql_query("
               SELECT id
                    , actif
                    , clef_activation
               FROM user
               WHERE id = '" . $_GET["id"] . "'
               AND clef_activation = '" . strtolower($_GET["activation"]) . "'
          ");
          
          // Si une erreur survient
          if(!$result){
               $message = "Une erreur est survenue lors de l'activation de votre compte utilisateur<br/>
                              <a href=\"".$GLOBALS['params']['appli']['proto']."://" . $_SERVER["SERVER_NAME"]
                              .$GLOBALS['params']['appli']['root_folder']."\">Retour au site</a>";
          }else{
               
               // Si aucun enregistrement n'est trouvé
               if(mysql_num_rows($result) == 0){
                     header("Location: ../index.php");
               }else{
                    
                    // Récupération du tableau de données retourné
                    $row = mysql_fetch_array($result);
                    
                    // Vérification que le compte ne soit pas déjà activé
                    if($row["actif"] != 0){
                         $message = "Votre compte utilisateur a déjà été activé<br/>
                              Vous pouvez maintenant vous connecter : <a href=\"".$GLOBALS['params']['appli']['proto']."://" . $_SERVER["SERVER_NAME"]
                              .$GLOBALS['params']['appli']['root_folder']."\">Accès au site</a>";
                    }else{
                         
                         // Activation du compte utilisateur
                         $result = mysql_query("
                              UPDATE user
                              SET actif = '1'
                              WHERE id = '" . $_GET["id"] . "'
                              AND clef_activation = '" . strtolower($_GET["activation"]) . "'
                         ");
                         
                         // Si une erreur survient
                         if(!$result){
                              $message = "Une erreur est survenue lors de l'activation de votre compte utilisateur<br/>
                              <a href=\"".$GLOBALS['params']['appli']['proto']."://" . $_SERVER["SERVER_NAME"]
                              .$GLOBALS['params']['appli']['root_folder']."\">Retour au site</a>";
                         }else{
                              $message = "Votre compte utilisateur a correctement été activé<br/>
                              Vous pouvez maintenant vous connecter.<a href=\"".$GLOBALS['params']['appli']['proto']."://" . $_SERVER["SERVER_NAME"]
                              .$GLOBALS['params']['appli']['root_folder']."\">Accès au site</a>";
                         }
                         
                    }
                    
               }
               
          }
          
          // Fermeture de la connexion à la base de données
          mysql_close();
          
     }
     
}

include_once("../include/functions.php");
include_once("../header.inc.php");
entete_page('',$GLOBALS['params']['appli']['root_folder']);
?>
<body>
<div id="container">
	<h1><?php  echo $titre;?></h1>
		<h2><?php  echo $pageDescription;?></h2>	
		<div id="mess" style="display: none;"></div>		
	<div class="content">
		<p><?php echo $message; ?></p>
	</div>
</div>
</body>
</html>