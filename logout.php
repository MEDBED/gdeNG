<?php
// Redirige l'utilisateur s'il n'est pas identifi�
/*if(empty($_COOKIE["ID_UTILISATEUR"])){
     header("Location: index.php");
}else{    */ 
     // Suppression des cookies
     setcookie("ID_UTILISATEUR", "", time() - 1, "/");
     setcookie("NOM_UTILISATEUR", "", time() - 1, "/");    
     setcookie("LAST_CO", "", time() - 1, "/");
     setcookie("UNIQID", "", time() - 1, "/");
     session_start();     
     // Détruit toutes les variables de session
     $_SESSION = array();
     session_destroy();
     // Redirection de l'utilisateur   
     header("Location: index.php");    
//}

?>