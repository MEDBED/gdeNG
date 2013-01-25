<?php
if(!isset($_COOKIE["ID_UTILISATEUR"])){
	header("Location: ../index.php");
	exit;
}
session_start();
include_once("../header.inc.php");
include_once("../include/functions.php");
//include_once("../include/protect_var.php");
/*
print_r($_POST);
echo "<br/>**<br/>";
print_r($_FILES);
 */

try
{
	//Open database connection	
	connectSQL();
	include_once("../include/protect_var.php");		        
	//Getting records (listAction)       
        $tabFiltre=array("nom"=>"Fnom","b.detail"=>"Fmarque","c.detail"=>"Fmodele","systeme"=>"Fsysteme");
	if($_GET["action"] == "list"){			           
            //Get records from database
            if (empty($_GET["jtSorting"])){$_GET["jtSorting"]="updateOn DESC";}
            $limHaute=$_GET["jtStartIndex"]+$_GET["jtPageSize"];
            if ($_GET[source]=='doc'){
                $requete="SELECT count(a.id) as recordCount FROM document a, user b, materiel c  WHERE (a.id_source=c.id OR a.id_source='') AND c.id_entite=:id_entite  AND a.createBy=b.id AND (a.acces=0)";		
            }else{
                $requete="SELECT count(a.id) as recordCount FROM document a, user b  WHERE a.id_zone=:id_zone AND a.id_source=:id_mat AND a.createBy=b.id AND (a.acces=0)";		
            }
            foreach ($tabFiltre as $search=>$champ){
                if (!empty($_POST[$champ])){
                    $requete.=" AND $search LIKE :$champ";
                }
            }                		
            $prep=$db->prepare($requete);
            if ($_GET[source]=='doc'){
                $prep->bindParam(":id_entite",$_SESSION['id_entite'],PDO::PARAM_INT);                
            }else{
                $prep->bindParam(":id_mat",$_GET['id_materiel'],PDO::PARAM_INT);
                $prep->bindParam(":id_zone",$_SESSION['id_zone_org'],PDO::PARAM_INT);	
            }
            foreach ($tabFiltre as $search=>$champ){
                if (!empty($_POST[$champ])){				
                    $text="$_POST[$champ]%";
                    $prep->bindParam(":$champ",$text,PDO::PARAM_STR);
                }
            }				
            $prep->execute();		
            $row = $prep->fetch(PDO::FETCH_ASSOC);
            $recordCount=$row['recordCount'];
            $prep->closeCursor();
            $prep = NULL;
            //$requete="SELECT *,a.id as id_suivi,CONCAT(nom,' ',prenom) AS utilisateur FROM suivi a, user b WHERE a.id_user=b.id";// 
            if ($_GET[source]=='doc'){
                $requete="SELECT a.*,a.id as id_document,a.updateOn as updateOnDoc,CONCAT(b.nom,' ',prenom) AS utilisateur FROM document a, user b, materiel c WHERE (a.id_source=c.id OR a.id_source='') AND c.id_entite=:id_entite  AND a.createBy=b.id AND (a.acces=0)";// 
            }else{
                $requete="SELECT a.*,a.id as id_document,a.updateOn as updateOnDoc,CONCAT(nom,' ',prenom) AS utilisateur FROM document a, user b WHERE a.id_zone=:id_zone AND a.id_source=:id_mat AND a.createBy=b.id AND (a.acces=0)";// 
            }
            foreach ($tabFiltre as $search=>$champ){
                    if (!empty($_POST[$champ])){$requete.=" AND $search LIKE :$champ";}
            }
            $requete.=" ORDER BY ";
            if ($_GET[source]=='doc'){
                $requete.=" a.id_zone,";
            }
             $requete.=$_GET["jtSorting"]." LIMIT ".$_GET["jtStartIndex"]."," . $limHaute . ";";	           
            //$requete="SELECT *, a.id as id_document FROM document a WHERE a.id_source=:id_mat";
            $prep=$db->prepare($requete);
             if ($_GET[source]=='doc'){
                $prep->bindParam(":id_entite",$_SESSION['id_entite'],PDO::PARAM_INT);                
            }else{
                $prep->bindParam(":id_mat",$_GET['id_materiel'],PDO::PARAM_INT);	
                $prep->bindParam(":id_zone",$_SESSION['id_zone_org'],PDO::PARAM_INT);
            }
            foreach ($tabFiltre as $search=>$champ){
                    if (!empty($_POST[$champ])){			
                            $text="$_POST[$champ]%";
                            $prep->bindParam(":$champ",$text,PDO::PARAM_STR);
                    }
            }			
            $prep->execute();                      
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
else if($_GET["action"] == "create")// && $_FILES['fic']['size']>0
	{
            //$filename = stripslashes($_FILES['fic']['name']);
            $filename = strtolower(stripslashes(str_replace(' ','_',$_FILES['fic']['name'])));
            $extension = getExtension($filename);
            
            //Insert record into database	
            if( in_array( $extension, $GLOBALS['params']['appli']['extensionOk'] ) ) {               
                $fichier=time().".$extension";
                if (($_FILES["fic"]["type"] == "image/jpeg" || $_FILES["fic"]["type"] == "image/pjpeg" || $_FILES["fic"]["type"] == "image/gif" || $_FILES["fic"]["type"] == "image/x-png") && ($_FILES["fic"]["size"] < 5000000)){
                    // some settings
                    $max_upload_width = 1024;
                    $max_upload_height = 768;                                        

                    // if uploaded image was JPG/JPEG
                    if($_FILES["fic"]["type"] == "image/jpeg" || $_FILES["image_upload_box"]["type"] == "image/pjpeg"){	
                            $image_source = imagecreatefromjpeg($_FILES["fic"]["tmp_name"]);
                    }		
                    // if uploaded image was GIF
                    if($_FILES["fic"]["type"] == "image/gif"){	
                            $image_source = imagecreatefromgif($_FILES["fic"]["tmp_name"]);
                    }	
                    // BMP doesn't seem to be supported so remove it form above image type test (reject bmps)	
                    // if uploaded image was BMP
                    if($_FILES["fic"]["type"] == "image/bmp"){	
                            $image_source = imagecreatefromwbmp($_FILES["fic"]["tmp_name"]);
                    }			
                    // if uploaded image was PNG
                    if($_FILES["fic"]["type"] == "image/x-png"){
                            $image_source = imagecreatefrompng($_FILES["fic"]["tmp_name"]);
                    }

                    $remote_file = $GLOBALS['params']['appli']['document_folder']."/files/".$fichier;
                    imagejpeg($image_source,$remote_file,100);
                    chmod($remote_file,0644);

                    // get width and height of original image
                    list($image_width, $image_height) = getimagesize($remote_file);

                    if($image_width>$max_upload_width || $image_height >$max_upload_height){
                            $proportions = $image_width/$image_height;

                            if($image_width>$image_height){
                                $new_width = $max_upload_width;
                                $new_height = round($max_upload_width/$proportions);
                            }		
                            else{
                                $new_height = $max_upload_height;
                                $new_width = round($max_upload_height*$proportions);
                            }		
                            $new_image = imagecreatetruecolor($new_width , $new_height);
                            $image_source = imagecreatefromjpeg($remote_file);
                            imagecopyresampled($new_image, $image_source, 0, 0, 0, 0, $new_width, $new_height, $image_width, $image_height);
                            imagejpeg($new_image,$remote_file,100);
                            imagedestroy($new_image);
                    }
                    imagedestroy($image_source);
                }elseif ($_FILES["fic"]["size"] < 5000000){
                    //Insertion du fichier                    
                        @copy($_FILES['fic']['tmp_name'], $GLOBALS['params']['appli']['document_folder'].'/files/'.$fichier);                   
                }else{
                    $jTableResult['Result'] = "ERR";
                    $jTableResult['Message'] = "Fichier trop volumineux (>5Mo)";           
                    print json_encode($jTableResult);
                    exit;
                }
                $requete="INSERT into document(id_zone,id_source,fic,ficName,acces,description,updateOn,createOn,updateBy,createBY";
                if (!empty($_POST["dateFin"])){
                    $requete.=",dateFin";
                }
                $requete.=") VALUES(:id_zone,:id_source,:fic,:ficName,:acces,:description,now(),now(),:updateBy,:createBy";
                if (!empty($_POST["dateFin"])){			
                    $requete.=",:dateFin";
                }
                $requete.=");";		
                $prep=$db->prepare($requete);
                $prep->bindParam(':id_source',$_POST[id_source],PDO::PARAM_INT);
                $prep->bindParam(":id_zone",$_SESSION['id_zone_org'],PDO::PARAM_INT);
                $prep->bindParam(':fic',$fichier,PDO::PARAM_STR);
                if (empty($_POST[ficName])){                    
                    $prep->bindParam(':ficName',str_replace(".$extension",'',$filename),PDO::PARAM_STR); 
                }else{
                    $prep->bindParam(':ficName',str_replace('.'.strtolower(getExtension($filename)),'',str_replace(' ','_',strtolower($_POST[ficName]))),PDO::PARAM_STR); 
                }
                $prep->bindParam(':acces',$_POST[acces],PDO::PARAM_STR);                
                $prep->bindParam(':description',$_POST[description],PDO::PARAM_STR);
                $prep->bindParam(':updateBy', $_SESSION[ID_USER],PDO::PARAM_INT);
                $prep->bindParam(':createBy', $_SESSION[ID_USER],PDO::PARAM_INT);  
                if (!empty($_POST["dateFin"])){
                    $tmpDate=explode('-',$_POST["dateFin"]);
                    $_POST["dateFin"]="$tmpDate[2]-$tmpDate[1]-$tmpDate[0]";
                    $prep->bindParam(":dateFin",$_POST["dateFin"],PDO::PARAM_STR);
                }			
                $prep->execute();
                $id_nouveau = $db->lastInsertId();
                $prep = NULL;		
                
                //Get last inserted record (to return to jTable)		
                $requete = "SELECT * FROM document WHERE id = :id";
                $prep=$db->prepare($requete);
                $prep->bindParam(":id",$id_nouveau,PDO::PARAM_INT);	
                $prep->execute();
                $row = $prep->fetch(PDO::FETCH_ASSOC);
                //Return result to jTable
                $jTableResult = array();                
                $jTableResult['Result'] = "OK";
                $jTableResult['Record'] = $row;
                $prep->closeCursor();
                $prep = NULL;
                print json_encode($jTableResult);
            }else{                             
                $jTableResult['Result'] = "ERR";                
                $jTableResult['Message'] = "Extension non valide : $extension";           
                print json_encode($jTableResult);
            }
	}
	//Updating a record (updateAction)
	else if($_GET["action"] == "update")
	{		
            $filename = strtolower(stripslashes(str_replace(' ','_',$_POST[ficName])));
            
            $extension = getExtension($filename);
            /*if( in_array( $extension, $GLOBALS['params']['appli']['extensionOk'] ) ) {                
                $fichier=time().".$extension";
                if (($_FILES["fic"]["type"] == "image/jpeg" || $_FILES["fic"]["type"] == "image/pjpeg" || $_FILES["fic"]["type"] == "image/gif" || $_FILES["fic"]["type"] == "image/x-png") && ($_FILES["fic"]["size"] < 5000000)){
                    // some settings
                    $max_upload_width = 1024;
                    $max_upload_height = 768;                    
                    // if user chosed properly then scale down the image according to user preferances                    

                    // if uploaded image was JPG/JPEG
                    if($_FILES["fic"]["type"] == "image/jpeg" || $_FILES["image_upload_box"]["type"] == "image/pjpeg"){	
                            $image_source = imagecreatefromjpeg($_FILES["fic"]["tmp_name"]);
                    }		
                    // if uploaded image was GIF
                    if($_FILES["fic"]["type"] == "image/gif"){	
                            $image_source = imagecreatefromgif($_FILES["fic"]["tmp_name"]);
                    }	
                    // BMP doesn't seem to be supported so remove it form above image type test (reject bmps)	
                    // if uploaded image was BMP
                    if($_FILES["fic"]["type"] == "image/bmp"){	
                            $image_source = imagecreatefromwbmp($_FILES["fic"]["tmp_name"]);
                    }			
                    // if uploaded image was PNG
                    if($_FILES["fic"]["type"] == "image/x-png"){
                            $image_source = imagecreatefrompng($_FILES["fic"]["tmp_name"]);
                    }

                    $remote_file = $GLOBALS['params']['appli']['document_folder']."/files/".$fichier;
                    imagejpeg($image_source,$remote_file,100);
                    chmod($remote_file,0644);

                    // get width and height of original image
                    list($image_width, $image_height) = getimagesize($remote_file);

                    if($image_width>$max_upload_width || $image_height >$max_upload_height){
                            $proportions = $image_width/$image_height;

                            if($image_width>$image_height){
                                $new_width = $max_upload_width;
                                $new_height = round($max_upload_width/$proportions);
                            }		
                            else{
                                $new_height = $max_upload_height;
                                $new_width = round($max_upload_height*$proportions);
                            }		
                            $new_image = imagecreatetruecolor($new_width , $new_height);
                            $image_source = imagecreatefromjpeg($remote_file);
                            imagecopyresampled($new_image, $image_source, 0, 0, 0, 0, $new_width, $new_height, $image_width, $image_height);
                            imagejpeg($new_image,$remote_file,100);
                            imagedestroy($new_image);
                    }
                    imagedestroy($image_source);
                }elseif ($_FILES["fic"]["size"] < 5000000){
                    //Insertion du fichier                  
                    @copy($_FILES['fic']['tmp_name'], $GLOBALS['params']['appli']['document_folder'].'/files/'.$fichier);                    
                }else{
                    $jTableResult['Result'] = "ERR";
                    $jTableResult['Message'] = "Fichier trop volumineux (>5Mo)";           
                    print json_encode($jTableResult);
                    exit;
                }*/
                //Update record in database            
                $requete="UPDATE document SET id_zone=:id_zone,id_source=:id_source,ficName=:ficName,acces=:acces,description=:description,updateOn=now(),updateBy=:updateBy";
                //$requete = "UPDATE document SET source=:source,id_source=:id_source, detail=:detail, priorite=:priorite";
                if (!empty($_POST["dateFin"])){
                    $tmpDate=explode('-',$_POST["dateFin"]);
                    $_POST["dateFin"]="$tmpDate[2]-$tmpDate[1]-$tmpDate[0]";
                    $requete.=",dateFin=:dateFin";
                }else{$requete.=",dateFin=:dateFin";}		
                $requete.=" WHERE id = :id;";
                $prep=$db->prepare($requete);
                $prep->bindParam(":id",$_POST["id_document"],PDO::PARAM_INT);
                $prep->bindParam(':id_source',$_POST[id_source],PDO::PARAM_INT);
                $prep->bindParam(":id_zone",$_SESSION['id_zone_org'],PDO::PARAM_INT);
                //$prep->bindParam(':fic',$fichier,PDO::PARAM_STR);               
                $prep->bindParam(':ficName',str_replace(".$extension",'',$filename),PDO::PARAM_STR);                
                $prep->bindParam(':acces',$_POST[acces],PDO::PARAM_STR);                
                $prep->bindParam(':description',$_POST[description],PDO::PARAM_STR);
                $prep->bindParam(':updateBy', $_SESSION[ID_USER],PDO::PARAM_INT);                
                if (!empty($_POST["dateFin"])){
                    $tmpDate=explode('-',$_POST["dateFin"]);
                    $_POST["dateFin"]="$tmpDate[2]-$tmpDate[1]-$tmpDate[0]";
                    $prep->bindParam(":dateFin",$_POST["dateFin"],PDO::PARAM_STR);
                }else{$prep->bindValue(":dateFin",NULL,PDO::PARAM_STR);}
                $prep->execute();
                $prep->closeCursor();
                $prep = NULL;
                //Return result to jTable
                $jTableResult = array();
                $jTableResult['Result'] = "OK";
                print json_encode($jTableResult);
        /*    }else{
                $jTableResult['Result'] = "ERR";
                $jTableResult['Message'] = "Extension non valide : $extension";           
                print json_encode($jTableResult);
                
            }                */
	}
	//Deleting a record (deleteAction)
	else if($_GET["action"] == "delete")
	{
            //Delete file
            $requete="SELECT fic FROM document WHERE id = :id;";		
            $prep=$db->prepare($requete);
            $prep->bindParam(":id",$_POST["id_document"],PDO::PARAM_INT);
            $prep->execute();
            $row = $prep->fetch(PDO::FETCH_ASSOC);            
            //Delete from database
            $requete="DELETE FROM document WHERE id = :id;";		
            $prep=$db->prepare($requete);
            $prep->bindParam(":id",$_POST["id_document"],PDO::PARAM_INT);
            $prep->execute();
            if ($prep){
                if (is_file($GLOBALS['params']['appli']['document_folder'].'/files/'.$row[fic]))
                {
                    @unlink($GLOBALS['params']['appli']['document_folder'].'/files/'.$row[fic]);
                }
            }
            $prep->closeCursor();
            $prep = NULL;		            
            //Return result to jTable
            $jTableResult = array();
            $jTableResult['Result'] = "OK";
            print json_encode($jTableResult);
	}else if($_POST["add"] == 1)
	{
            
        }

	//Close database connection
	//mysql_close($con);

}
catch(Exception $ex)
{
    //Return error message
	$jTableResult = array();
	$jTableResult['Result'] = "ERROR";
	$jTableResult['Message'] = "erreur";
	print json_encode($jTableResult);
}
	
?>