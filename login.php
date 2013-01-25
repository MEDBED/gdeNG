<?php
include_once("header.inc.php");
include_once("include/functions.php");
// Redirige l'utilisateur s'il est déjà identifié
if(isset($_COOKIE["ID_UTILISATEUR"])){
     header("Location: menu.php");
}else{
     // Une fois le formulaire envoyé
     if(isset($_POST["connexion"])){          
	     // Connexion à la base de données	      	
         connectSQL();        
         //include_once("include/protect_var.php");
         // Sélection de l'utilisateur concerné
         //@mysql_query("SET lc_time_names = 'fr_FR';");
         $db->query("SET lc_time_names = 'fr_FR';")->fetch(PDO::FETCH_OBJ); //Sur une même ligne ...         
         $query = "SELECT id, login, password, actif, date_format(date_co, '%W %d %M %Y à %Hh%i') as last_co_fr,ldap
             FROM user
             WHERE login = '" . $_POST["login"] . "';";
         $row = $db->query($query)->fetch(PDO::FETCH_ASSOC); //Sur une même ligne ...                
         /*$result = mysql_query("
    	     SELECT id, login, password, actif, date_format(date_co, '%W %d %M %Y à %Hh%i') as last_co_fr,ldap
             FROM user
             WHERE login = '" . $_POST["login"] . "'
          ");*/
         if(!$row){
         	$message= "Le nom d'utilisateur " . $_POST["login"] . " n'existe pas";
         }else{
         	  
         		// Récupération des données
         		//$row = mysql_fetch_array($result);         		
         		if ($row['ldap']==1){         			
         			$connect = ldap_connect($GLOBALS['params']['appli']['ldap_host'],$GLOBALS['params']['appli']['ldap_port']);  // connexion en anonymous
         			if(!$connect){
         				$message="Connexion au serveur LDAP impossible";
         			}else{                          				       				
		                $read = ldap_search($connect,$GLOBALS['params']['appli']['ldap_basedn'],'uid='.$_POST["login"]) ;//		               
		                $info = ldap_get_entries($connect, $read); 
		                $rne_cours=$info[0]['rne'][0];				                  
		                if (!empty($res_params['ldap_rne'])){
		                        $ldap_rne=$res_params['ldap_rne'];
		                        if (!empty($info[0][$ldap_rne])){
		                                foreach ($info[0][$ldap_rne] as $rnes){
		                                        if (!is_numeric($rnes)){
		                                                $tab_temp=explode('$',$rnes);
		                                                $tab_rnes[]=$tab_temp[0];
		                                        }
		                                }
		                        }	                        
		                }
		                $bind = @ldap_bind($connect,$info[0]["dn"],$_POST["pass"]);
		                if ( !$bind ){
		                	$sql = "INSERT INTO log(date,type,detail) VALUES(now(),:type,:text)";
		                	$type="Connexion";		                			                	
		                	$prep=$db->prepare($sql) ;//or die('2Erreur SQL !<br>'.$sql.'<br>'.mysql_error());		                
		                	$prep->bindValue(':type',"1", PDO::PARAM_INT);
		                	$prep->bindValue(':text',"Echec de connexion depuis $_SERVER[REMOTE_ADDR]",PDO::PARAM_STR);
		                	$prep->execute();
		                	$prep->closeCursor();
		                	$prep = NULL;
		                	$message="Mauvais login / password. Merci de recommencer";
		                }	else{
		                	// Définition du temps d'expiration des cookies
		                	$expiration =
		                	empty($_POST["connexion_auto"]) ? time() + 3600 : time() + 90 * 24 * 60 * 60;
		                	// Création des cookies
		                	setcookie("ID_UTILISATEUR", $row['id'], $expiration, "/");
		                	setcookie("NOM_UTILISATEUR", $row['login'], $expiration, "/");
		                	setcookie("LAST_CO", $row['last_co_fr'], $expiration, "/");
		                	$req="UPDATE user SET date_co=now() WHERE id=:id";
		                	$prep=$db->prepare($req);
		                	$prep->bindParam('id',$row[id],PDO::PARAM_INT);
		                	$prep->execute();
		                	$prep->closeCursor();
		                	$prep = NULL;
		                	session_start();
		                	sessionValide();
		                }		                		               		               
         			} 
         		}else{
		          if(!preg_match("/^[A-Za-z0-9_]{2,20}$/", $_POST["login"])){
		               $message = "Votre nom d'utilisateur doit comporter entre 2 et 20 caractères<br />\n";
		               $message .= "L'utilisation de l'underscore est autorisée";
		          }elseif(!preg_match("/^[A-Za-z0-9]{8,}$/", $_POST["pass"])){
		               $message = "Votre mot de passe doit comporter au moins 8 caractères";
		          }else{                        
                         // Si le compte n'a pas été activé
                         if($row['actif'] == 0){
                              $message = "Votre compte utilisateur n'a pas été activé";
                         }else{                              
                              // Vérification du mot de passe
                              if(md5($_POST["pass"]) != $row["password"]){                              	
                                $message = "Votre mot de passe est incorrect";
                              }else{
                                   
                                   // Définition du temps d'expiration des cookies
                                   $expiration =
                                        empty($_POST["connexion_auto"]) ? time() + 3600 : time() + 90 * 24 * 60 * 60;                                  
                                   // Création des cookies
                                    setcookie("ID_UTILISATEUR", $row['id'], $expiration, "/");
				                	setcookie("NOM_UTILISATEUR", $row['login'], $expiration, "/");
				                	setcookie("LAST_CO", $row['last_co_fr'], $expiration, "/");
				                	$req="UPDATE user SET date_co=now() WHERE id=:id";
				                	$prep=$db->prepare($req);
				                	$prep->bindParam('id',$row[id],PDO::PARAM_INT);
				                	$prep->execute();  
				                	$prep->closeCursor();
				                	$prep = NULL;
                                   session_start();                                
                                   sessionValide();                                   
                              }                              
                         }                         
                    }                            		                   
               }                             
          }          
     }     
}
if(isset($message)) {echo $message;}
?>