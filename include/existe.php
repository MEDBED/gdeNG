<?php
if(!isset($_COOKIE["ID_UTILISATEUR"])){
	header("Location: logout.php");
	exit;
}
session_start();
include_once("../header.inc.php");
include_once("../include/functions.php");
include_once("../include/protect_var.php");
$dirname = '../graphs/icons/'.$_POST['src'].'/';
//$dir = opendir($dirname); 
$fic='';
//$_POST[nom_fic]=base64_encode("windowsxp");
//$_POST[nom_fic]=base64_encode('2008');
$_SESSION['PNG']=array();
//echo $ficCheck=str_replace("/","\\/",base64_decode($_POST[nom_fic]));
if (!empty($_POST[nom_fic])){    
    $ficCheck=trim(str_replace("/","",base64_decode($_POST[nom_fic]))).'.png'; 
    //echo $ficCheck;
    if (in_array($ficCheck,$_SESSION['PNG'])){
        $fic=$_SESSION['PNG'][$ficCheck];        
    }else{
        if($dossier = opendir($dirname)){       
            while(false !== ($fichier = readdir($dossier))){
                if($fichier != '.' && $fichier != '..'){                     
                   if ($fichier==$ficCheck){                             
                       $fic=$fichier;
                       $_SESSION['PNG'][$ficCheck]=$fichier;
                       break;
                   }
                }
            }
            closedir($dossier);
            if (empty($fic)){
                if($dossier = opendir($dirname)){    
                    while(false !== ($fichier = readdir($dossier))){
                        if($fichier != '.' && $fichier != '..'){                           
                          if (stristr($fichier,$ficCheck)){         
                               $fic=$fichier;
                               $_SESSION['PNG'][$ficCheck]=$fichier;
                               break;
                          }
                        }
                    }
                }
                closedir($dossier);
            }        
            if (empty($fic)){
                if($dossier = opendir($dirname)){    
                    while(false !== ($fichier = readdir($dossier))){
                        if($fichier != '.' && $fichier != '..'){                      
                           if(stristr($ficCheck,str_replace('.png','',$fichier))){
                               $fic=$fichier;  
                               $_SESSION['PNG'][$ficCheck]=$fichier;
                               break;
                           }
                        }
                    }
                }
                closedir($dossier);
            }
        }
    }    
}
if (!empty($fic)){
    echo $fic;
}else{ echo "0";}
/*while($file = readdir($dir)) {
    if($file != '.' && $file != '..' && !is_dir($dirname.$file))
    {
        //if (is_file($_SERVER{'DOCUMENT_ROOT'}.$GLOBALS['params']['appli']['root_folder'].base64_decode($_POST['nom_fic']))) {
        echo "/^".base64_decode($_POST[nom_fic])."/i,$file";
        if (preg_match("/^".base64_decode($_POST[nom_fic])."/i",$file)){
            echo "1";exit;
        } else {
            echo "0";exit;
        }
    }
}
closedir($dir);*/
/*if (is_file($_SERVER{'DOCUMENT_ROOT'}.$GLOBALS['params']['appli']['root_folder'].base64_decode($_POST['nom_fic']))) {
	echo "1";
} else {
	echo "0";
}*/
?>