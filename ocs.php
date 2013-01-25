<?php
/*if(!isset($_COOKIE["ID_UTILISATEUR"])){
	header("Location: ../index.php");
	exit;
}
session_start();*/
include('header.inc.php');
//--Augmentation du temps d'execution des requetes
ini_set('max_execution_time',3600);
session_start();
$arrExtraParam= array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
$connexionNG = new PDO('mysql:host='.$GLOBALS['params']['bdd']['db_host'].';port='.$GLOBALS['params']['bdd']['db_port'].';dbname='.$GLOBALS['params']['bdd']['db_name'], $GLOBALS['params']['bdd']['db_user'], $GLOBALS['params']['bdd']['db_pass'],$arrExtraParam);
//$connexion = new PDO('mysql:host=localhost;port=3306;dbname=gde',"gde","osijekun",$arrExtraParam);
$connexionOCS = new PDO('mysql:host='.$GLOBALS['ocs'][0]['bdd']['db_host'].';port='.$GLOBALS['ocs'][0]['bdd']['db_port'].';dbname='.$GLOBALS['ocs'][0]['bdd']['db_name'], $GLOBALS['ocs'][0]['bdd']['db_user'], $GLOBALS['ocs'][0]['bdd']['db_pass'],$arrExtraParam);
$tabDonnee=array('soft','sound','net','printer','drive','storage','video','monitor','memory');
//Initialisation des données à importer, mettre à 1 pour l'import
if (!empty($_SESSION['alias_entite'])){ 
   foreach ($tabDonnee as $donnee){
    if ($_POST[$donnee]=='on'){
        ${$donnee}=1;       
    }else{
        ${$donnee}=0;
    }
   }
}else{
    $soft=1;//ok
    $sound=1;//ok
    $net=1;//ok
    $printer=1;//ok
    $drive=1;//ok
    $storage=1;//ok
    $video=1;//ok
    $monitor=1;//ok
    $memory=1;//ok
}

//Variable
$timestart=microtime(true);
$limit="";//LIMIT 5
$nbOCS=0;
$tabErr=array();
$nbInsertNet=$nbUpdateNet=$nbInsertPrinter=$nbUpdatePrinter=$nbInsertMemory=$nbUpdateMemory=$nbInsertMonitor=$nbUpdateMonitor=$nbInsertVideo=$nbUpdateVideo=$nbInsertSound=$nbUpdateSound=$nbInsertStorage=$nbUpdateStorage=$nbInsertDrive=$nbUpdateDrive=$nbInsertSoft=$nbUpdateSoft=0;
$nbInsertMarque=$nbInsertModele=$nbErreurOCS=$nbInsertOCS=$nbUpdateOCS=$nbRapproOCS=0;
//Fonctions
include_once('include/functions_ocs.php');
//Types de matériel
$requete="SELECT id,detail FROM type WHERE source='materiel'";
$arr = $connexionNG->query($requete);
while ($enr=$arr->fetch(PDO::FETCH_ASSOC)){
    if (in_array($enr['detail'],$GLOBALS['ocs'][0]['tag']) || $enr['detail']=='Ordinateur'){
        $tabType[$enr['detail']]=$enr['id'];
    }
}
//Types de config
$requete="SELECT id,detail FROM type WHERE source='config'";
$arr = $connexionNG->query($requete);
while ($enr=$arr->fetch(PDO::FETCH_ASSOC)){    
    $tabConfig[$enr['detail']]=$enr['id'];
}
//Zones
$requete="SELECT id,detail FROM zone";
$arr = $connexionNG->query($requete);
while ($enr=$arr->fetch(PDO::FETCH_ASSOC)){
    if (in_array($enr['detail'],$GLOBALS['ocs'][0]['tag'])){
        $tabZone[$enr['detail']]=$enr['id'];
    }
}
//Entités
$requete="SELECT id,alias FROM entite";
$arr = $connexionNG->query($requete);
while ($enr=$arr->fetch(PDO::FETCH_ASSOC)){    
    $tabEntite[strtolower($enr['alias'])]=$enr['id'];
}
//**************
//Parcours d'OCS
if (!empty($_SESSION['alias_entite'])){    
    $requete="SELECT a.hardware_id,a.tag FROM accountinfo a WHERE a.tag like '%$_SESSION[alias_entite]%' $limit";
}else{    
    $requete="SELECT a.hardware_id,a.tag FROM accountinfo a $limit";
}
$arr = $connexionOCS->query($requete);
while ($enr=$arr->fetch(PDO::FETCH_ASSOC)){
    //Récupération des infos
    $tag=explode('_',strtolower($enr[tag]));
    $idEntite=$tabEntite[$tag[1]];
    $idZone=$tabZone[$GLOBALS['ocs'][0]['tag'][$tag[0]]];
    if (empty($tabType[$GLOBALS['ocs'][0]['tag'][$tag[0]]])){
        $idType=$tabType['Ordinateur'];
    }else{
        $idType=$tabType[$GLOBALS['ocs'][0]['tag'][$tag[0]]];
    }
    $idOCS=$enr['hardware_id'];          
    //echo strtolower($enr[tag])." : $idZone $idEntite<br/>";
    if (!empty($tag[0]) && !empty($tag[1]) && !empty($idZone) && !empty($idEntite) && $idOCS>0){
        $requeteGDE="SELECT id,inventorId FROM materiel WHERE inventorId=$idOCS AND inventorBy='ocs'";
        $stat=$connexionNG->query($requeteGDE);
        if ($stat->rowCount()==0){
            $reqSN="SELECT SSN FROM bios WHERE hardware_id=$idOCS";    
            $resSN = $connexionOCS->query($reqSN)->fetch(PDO::FETCH_ASSOC); //Sur une même ligne ..
            $reqCheck="SELECT id FROM materiel WHERE sn='$resSN[SSN]'";
            $recCheck=$connexionNG->query($reqCheck);
            if ($recCheck->rowCount()==0 || $resSN[SSN]!='Inconnu'){
                $requete="INSERT INTO materiel(id_entite,id_zone,id_type,inventorId,inventorBy) VALUES(:id_entite,:id_zone,:id_type,:inventorId,'ocs')";
                $INSERT=$connexionNG->prepare($requete);
                $INSERT->bindParam(":id_entite",$idEntite,PDO::PARAM_INT);
                $INSERT->bindParam(":id_zone",$idZone,PDO::PARAM_INT);
                $INSERT->bindParam(":id_type",$idType,PDO::PARAM_INT);
                $INSERT->bindParam(":inventorId",$idOCS,PDO::PARAM_INT);
                $INSERT->execute();
                $nbInsertOCS++;                    
            }else{
                $reqUPDATE="UPDATE materiel SET inventorBy='ocs',inventorID='$idOCS' WHERE sn='$resSN[SSN]'";
                $connexionNG->query($reqUPDATE);                
                $nbRapproOCS++;                    
            }
            $requeteGDE="SELECT id,inventorId FROM materiel WHERE inventorId=$idOCS AND inventorBy='ocs'";
            $stat=$connexionNG->query($requeteGDE);
        }else{
            $nbUpdateOCS++;
        }
        if ($stat->rowCount()!=0){
            $mat = $stat->fetch(PDO::FETCH_ASSOC);
            //*****
            //Drive
            //*****          
            if ($drive==1){                             
                importDrive();                
            }
            //*******
            //Storage
            //*******            
            if ($storage==1){                 
                importStorage(); 
            }
            //*****
            //Sound
            //*****            
            if ($sound==1){      
                importSound();                          
            }
            //*****
            //Video
            //*****            
            if ($video==1){     
                importVideo();                           
            }
            //*******
            //Monitor
            //*******            
            if ($monitor==1){  
                 importMonitor();                                    
            }
            //******
            //Memory
            //******            
            if ($memory==1){  
                importMemory();                           
            }
            //*******
            //Printer
            //*******            
            if ($printer==1){  
                importPrinter();                         
            }
            //***
            //Net
            //***            
            if ($net==1){    
                importNet();                    
            }   
            //***
            //Soft
            //***            
            if ($soft==1){    
                importSoft();                    
            }   
        }
            //$nbUpdateOCS++;
        /*}else{
            //echo "n'existe pas dans GDE<br/>";
            $requete="INSERT INTO materiell(id_entite,id_zone,id_type) VALUES(:id_entite,:id_zone,:id_type)";
            $INSERT=$connexionNG->prepare($requete);
            $INSERT->bindParam(":id_entite",$idEntite,PDO::PARAM_INT);
            $INSERT->bindParam(":id_zone",$idZone,PDO::PARAM_INT);
            $INSERT->bindParam(":id_type",$idType,PDO::PARAM_INT);
            $INSERT->execute();
            $nbInsertOCS++;            
        }*/
    }else{        
        $nbErreurOCS++;
        if (!in_array($enr[tag],$tabErr)){
            $tabErr[]=$enr[tag];                           
        }  
    }
    $nbOCS++; 
}
$timeend=microtime(true);
$time=$timeend-$timestart;
$page_load_time = number_format($time, 3);
if (!empty($_SESSION['alias_entite'])){ 
    echo '<div id="container2">
            <h1>OCS</h1>
                <h2>Résultats de l\'importation</h2>	
		<div id="mess" style="display: none;"></div>			
	<div class="content">';
    echo '<table class="tbl" align="center"><tr><th>Donnée</th><th style="text-align: right">Ajout(s)</th><th style="text-align: right">Mise(s) à jour</th><th style="text-align: right">Temps d\'exécution (en sec)</th></tr>';
    if ($drive==1){
        echo "<tr><td>Disques</td><td style=\"text-align: right\">$nbInsertDrive</td><td style=\"text-align: right\">$nbUpdateDrive</td><td style=\"text-align: right\">".number_format($page_load_time_Drive,3)."</td></tr>";
    }
    if ($monitor==1){
        echo "<tr><td>Ecran</td><td style=\"text-align: right\">$nbInsertMonitor</td><td style=\"text-align: right\">$nbUpdateMonitor</td><td style=\"text-align: right\">".number_format($page_load_time_Monitor,3)."</td></tr>";
    }
    if ($printer==1){
        echo "<tr><td>Impression</td><td style=\"text-align: right\">$nbInsertPrinter</td><td style=\"text-align: right\">$nbUpdatePrinter</td><td style=\"text-align: right\">".number_format($page_load_time_Printer,3)."</td></tr>";
    }    
    if ($soft==1){
        echo "<tr><td>Logiciel</td><td style=\"text-align: right\">$nbInsertSoft</td><td style=\"text-align: right\">$nbUpdateSoft</td><td style=\"text-align: right\">".number_format($page_load_time_Soft,3)."</td></tr>";
    }       
    if ($memory==1){
        echo "<tr><td>Mémoire</td><td style=\"text-align: right\">$nbInsertMemory</td><td style=\"text-align: right\">$nbUpdateMemory</td><td style=\"text-align: right\">".number_format($page_load_time_Memory,3)."</td></tr>";
    }
    if ($storage==1){
        echo "<tr><td>Partitions</td><td style=\"text-align: right\">$nbInsertStorage</td><td style=\"text-align: right\">$nbUpdateStorage</td><td style=\"text-align: right\">".number_format($page_load_time_Storage,3)."</td></tr>";
    }
    if ($net==1){
        echo "<tr><td>Réseau</td><td style=\"text-align: right\">$nbInsertNet</td><td style=\"text-align: right\">$nbUpdateNet</td><td style=\"text-align: right\">".number_format($page_load_time_Net,3)."</td></tr>";
    } 
    if ($sound==1){
        echo "<tr><td>Son</td><td style=\"text-align: right\">$nbInsertSound</td><td style=\"text-align: right\">$nbUpdateSound</td><td style=\"text-align: right\">".number_format($page_load_time_Sound,3)."</td></tr>";
    }
    if ($video==1){
        echo "<tr><td>Vidéo</td><td style=\"text-align: right\">$nbInsertVideo</td><td style=\"text-align: right\">$nbUpdateVideo</td><td style=\"text-align: right\">".number_format($page_load_time_Video,3)."</td></tr>";
    }  
    echo "<tr><th colspan=4 style=\"background-color: #2F5880;\">Marques & Modèles</th></tr>";
    echo "<tr><td>Marques</td><td style=\"text-align: right\">$nbInsertMarque</td><td style=\"text-align: right\">0</td><td style=\"text-align: right\">".number_format($page_load_time_Marque,3)."</td></tr>";
    echo "<tr><td>Modèles</td><td style=\"text-align: right\">$nbInsertModele</td><td style=\"text-align: right\">0</td><td style=\"text-align: right\">".number_format($page_load_time_Modele,3)."</td></tr>";
    echo "<tr><th colspan=4 style=\"background-color: #2F5880;text-align: right\">$page_load_time</th></tr>";   
    echo "<tr><th colspan=4 style=\"background-color: #2F5880;\">Matériels importés</th></tr>";
    echo "<tr><td colspan=3 style=\"text-align: right\">Nombre de postes ajoutés</td><td style=\"text-align: right\">$nbInsertOCS</td></tr>";
    echo "<tr><td colspan=3 style=\"text-align: right\">Nombre de postes raprrochés (via le SN)</td><td style=\"text-align: right\">$nbRapproOCS</td></tr>";
    echo "<tr><td colspan=3 style=\"text-align: right\">Nombre de postes mis à jour</td><td style=\"text-align: right\">$nbUpdateOCS</td></tr>";
    echo "<tr><td colspan=3 style=\"text-align: right\">Nombre de postes en erreur (TAG)</td><td style=\"text-align: right\">$nbErreurOCS *</td></tr>";
    echo "<tr><td colspan=3 style=\"text-align: right\">Nombre total de postes OCS</td><td style=\"text-align: right\">$nbOCS</td></tr>";
    echo '</table>';
    if ($nbErreurOCS>0){
        echo "<br/>* TAG en erreur :&nbsp;";
        foreach($tabErr as $err){
            echo $err.',';
        }
    }
    
  
    echo "<div style=\"position:absolute; width:150px; height:50px;font-style: italic;color: #000;padding-top: 5px;\"><br/>Debut du script: ".date("H:i:s", $timestart);
    echo "<br>Fin du script: ".date("H:i:s", $timeend);
    echo "<br>Script exécuté en " . $page_load_time . " sec";
    echo "</div></div>";
}
?>
