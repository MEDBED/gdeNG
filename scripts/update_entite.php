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
    try
    {
        //Open database connection
        //$con = mysql_connect("localhost","gdeNG","gde");
        //mysql_select_db("gdeNG", $con);
        //mysql_query("SET NAMES utf8;");
        connectSQL();
        include_once("../include/protect_var.php");	
        $tabFiltre=array("nom"=>"Fnom","b.detail"=>"Fmarque","c.detail"=>"Fmodele","systeme"=>"Fsysteme","e.ip"=>"Fip");
        //Getting records (listAction)
        if($_GET["action"] == "list")
        {		
            $limHaute=$_GET["jtStartIndex"]+$_GET["jtPageSize"];	        
            //Total records
            $requete="SELECT count(id) as recordCount FROM contact WHERE id_entite=:id_entite;";	
            $prep=$db->prepare($requete); 
            $prep->bindParam(":id_entite",$_SESSION['id_entite'],PDO::PARAM_INT);
            $prep->execute();		
            $row = $prep->fetch(PDO::FETCH_ASSOC);
            $recordCount=$row['recordCount'];
            $prep->closeCursor();
            $prep = NULL;
            //Get records from database		           
            $requete="SELECT *,id as id_contact FROM contact WHERE id_entite=:id_entite ORDER BY ".$_GET["jtSorting"]." LIMIT ".$_GET["jtStartIndex"]."," . $limHaute . ";";                               
            $prep=$db->prepare($requete); 
            $prep->bindParam(":id_entite",$_SESSION['id_entite'],PDO::PARAM_INT);	         
            $prep->execute();		
            $_SESSION['REQ_MAT']=$requete;
            //Add all records to an array
            $rows = array();
            $rows = $prep->fetchAll();
            //Return result to jTable
            $jTableResult = array();
            $jTableResult['Result'] = "OK";
            $jTableResult['TotalRecordCount'] = $recordCount;
            $jTableResult['Records'] = $rows;
            $prep->closeCursor();
            $prep = NULL;	
            //echo $requete;	
            print json_encode($jTableResult);
        }
        //Creating a new record (createAction)
        else if($_GET["action"] == "create")
        {        
            $requete = "INSERT INTO contact(id_entite,nom,prenom,fonction,mail1,mail2,tel1,tel2";        
            $requete.=",createOn,updateOn) VALUES(:id_entite,:nom,:prenom,:fonction,:mail1,:mail2,:tel1,:tel2";        
            $requete.=",now(),now())";		
            $prep=$db->prepare($requete);       
            $prep->bindParam(":id_entite",$_SESSION['id_entite'],PDO::PARAM_INT);
            $prep->bindParam(":nom",$_POST["nom"],PDO::PARAM_STR);
            $prep->bindParam(":prenom",$_POST["prenom"],PDO::PARAM_STR);
            $prep->bindParam(":fonction",$_POST["fonction"],PDO::PARAM_STR);
            $prep->bindParam(":mail1",$_POST["mail1"],PDO::PARAM_STR);
            $prep->bindParam(":mail2",$_POST["mail2"],PDO::PARAM_STR);
            $prep->bindParam(":tel1",$_POST["tel1"],PDO::PARAM_STR);        	
            $prep->bindParam(":tel2",$_POST["tel2"],PDO::PARAM_STR);        
            $prep->execute();
            $id_nouveau = $db->lastInsertId();
            $prep = NULL;		
            //Get last inserted record (to return to jTable)		
            $requete = "SELECT * FROM contact WHERE id = :id AND id_dentite=:id_entite;";
            $prep=$db->prepare($requete);
            $prep->bindParam(":id",$id_nouveau,PDO::PARAM_INT);
            $prep->bindParam(":id_entite",$_SESSION['id_entite'],PDO::PARAM_INT);
            $prep->execute();
            $row = $prep->fetch(PDO::FETCH_ASSOC);
            //Return result to jTable
            $jTableResult = array();
            $jTableResult['Result'] = "OK";
            $jTableResult['Record'] = $row;
            $prep->closeCursor();
            $prep = NULL;
            print json_encode($jTableResult);
        }
        //Updating a record (updateAction)
        else if($_GET["action"] == "update")
        {		
            //Update record in database
            $requete = "UPDATE contact SET nom=:nom, prenom=:prenom, fonction=:fonction, mail1=:mail1, mail2=:mail2, tel1=:tel1, tel2=:tel2";        
            $requete.=",updateOn=now() WHERE id = :id;";
            $prep=$db->prepare($requete);
            $prep->bindParam(":id",$_POST["id_contact"],PDO::PARAM_INT);
            $prep->bindParam(":nom",$_POST["nom"],PDO::PARAM_STR);
            $prep->bindParam(":prenom",$_POST["prenom"],PDO::PARAM_STR);
            $prep->bindParam(":fonction",$_POST["fonction"],PDO::PARAM_STR);
            $prep->bindParam(":mail1",$_POST["mail1"],PDO::PARAM_STR);
            $prep->bindParam(":mail2",$_POST["mail2"],PDO::PARAM_STR);
            $prep->bindParam(":tel1",$_POST["tel1"],PDO::PARAM_STR);        	
            $prep->bindParam(":tel2",$_POST["tel2"],PDO::PARAM_STR);     
            $prep->execute();
            $prep->closeCursor();
            $prep = NULL;
            //Return result to jTable
            $jTableResult = array();
            $jTableResult['Result'] = "OK";
            print json_encode($jTableResult);
        }
        //Deleting a record (deleteAction)
        else if($_GET["action"] == "delete")
        {
            //Delete from database
            $requete="DELETE FROM contact WHERE id = :id;";		
            $prep=$db->prepare($requete);
            $prep->bindParam(":id",$_POST["id_contact"],PDO::PARAM_INT);
            $prep->execute();
            $prep->closeCursor();
            $prep = NULL;		
            //Return result to jTable
            $jTableResult = array();
            $jTableResult['Result'] = "OK";
            print json_encode($jTableResult);
        }else{            
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
    }
    catch(Exception $ex)
    {
        //Return error message
        $jTableResult = array();
        $jTableResult['Result'] = "ERROR";
        $jTableResult['Message'] = $titi;
        print json_encode($jTableResult);
    }
}
?>