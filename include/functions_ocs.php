<?php
function checkModele($config,$modele){
    global $connexionNG,$connexionOCS,$res,$idZone,$tabConfig,$nbInsertMarque,$nbInsertModele,$modeleInconnu,$page_load_time_Marque,$page_load_time_Modele;
    $timestart=microtime(true);     
    //On vérifie l'existense du modèle dans la base                    
    $reqModele="SELECT a.id as id_marque,b.id as id_modele FROM marque a, modele b WHERE b.detail='".addslashes($res[$modele])."' AND a.detail='".addslashes($res[MANUFACTURER])."' AND id_type=$tabConfig[$config] AND id_zone=$idZone";               
    $row = $connexionNG->query($reqModele)->fetch(PDO::FETCH_ASSOC); //Sur une même ligne ...
    if (empty($row['id_modele'])){
        $requete="SELECT id FROM marque WHERE detail='".addslashes($res[MANUFACTURER])."';";
        $stat=$connexionNG->query($requete);        
        if ($stat->rowCount()==0){ 
            $timestart=microtime(true);     
            $requete="INSERT INTO marque(detail) values('".addslashes($res[MANUFACTURER])."');";
            $nbInsertMarque++;
            //echo $requete.'<br/>';
            $connexionNG->query($requete);
            $id_nouveau = $connexionNG->lastInsertId();
            $timeend=microtime(true);
            $time=$timeend-$timestart;
            $page_load_time_Marque +=$time;
            $timestart=microtime(true);     
            $reqInsert="INSERT INTO modele(id_marque,detail,id_zone,id_type) VALUES($id_nouveau,'Inconnu',$idZone,".$tabConfig[$config].");";			
            //echo $reqInsert.'<br/>';
            $connexionNG->query($reqInsert);             
            $timeend=microtime(true);
            $time=$timeend-$timestart;
            $page_load_time_Modele +=$time;
        }else{
            $row = $connexionNG->query($requete)->fetch(PDO::FETCH_ASSOC); //Sur une même ligne ...
            $id_nouveau=$row[id];            
        }
        
        if (empty($res[$modele])){
            $id_modele=$modeleInconnu;
        }else{
            $timestart=microtime(true); 
            $requete="INSERT INTO modele (id_marque,id_type,id_zone,detail) VALUES($id_nouveau,$tabConfig[$config],$idZone,'".addslashes($res[$modele])."');";
            $nbInsertModele++;
            //echo $requete.'<br/>';
            $connexionNG->query($requete);
            $id_modele = $connexionNG->lastInsertId(); 
            $timeend=microtime(true);
            $time=$timeend-$timestart;
            $page_load_time_Modele +=$time;
        }
    }else{$id_modele=$row['id_modele'];}
    return $id_modele;
}
function importDrive(){
    global $connexionNG,$connexionOCS,$idZone,$idOCS,$stat,$mat,$nbInsertDrive,$nbUpdateDrive,$page_load_time_Drive,$tabConfig,$res;       
    $timestart=microtime(true);                                                      
    $tabTypeOCS=array('CD-Rom Drive'=>'CD-Rom','Hard Drive'=>'Disque Dur','Network Drive'=>'Lecteur Réseau','Removable Drive'=>'Disque amovible','none'=>'Inconnu');
    $requete="INSERT INTO drive (id_materiel,id_type,lettre,type,fs,total,free,volume,updateOn,createOn,inventorID) values(:id_materiel,:id_type,:lettre,:type,:fs,:total,:free,:volume,now(),now(),:inventorID)";
    $INSERT=$connexionNG->prepare($requete);

    $requete="UPDATE drive SET total=:total, free=:free, id_type=:id_type, updateOn=now() WHERE id=:id;";
    $UPDATE=$connexionNG->prepare($requete);

    $requete="SELECT * FROM drives WHERE hardware_id=$idOCS"; 
    $loc = $connexionOCS->query($requete);                
    while ($res=$loc->fetch(PDO::FETCH_ASSOC)){   
        if (array_key_exists($res[TYPE],$tabTypeOCS)){
            $res[TYPE]=$tabTypeOCS[$res[TYPE]];
        }
        //Types de drive                     
        $requete="SELECT id,detail FROM type WHERE source='drive' AND detail='$res[TYPE]'";                
        $stat=$connexionNG->query($requete);
        if ($stat->rowCount()==0){    
            $requete="INSERT INTO type(source,detail) VALUES('drive','$res[TYPE]')";
            $connexionNG->query($requete);
            $id_type = $connexionNG->lastInsertId();
        }else{
           $row = $connexionNG->query($requete)->fetch(PDO::FETCH_ASSOC); //Sur une même ligne ...
           $id_type=$row[id];
        }
        $check="SELECT id FROM drive WHERE inventorID='$res[ID]' AND id_materiel=$mat[id]";                     
        $stat=$connexionNG->query($check);
        if ($stat->rowCount()==0){    
            $INSERT->bindParam(":id_materiel",$mat[id],PDO::PARAM_INT);
            $INSERT->bindParam(":id_type",$id_type,PDO::PARAM_INT);
            $INSERT->bindParam(":lettre",$res['LETTER'],PDO::PARAM_STR);
            $INSERT->bindParam(":type",$res['TYPE'],PDO::PARAM_STR);
            $INSERT->bindParam(":fs",$res['FILESYSTEM'],PDO::PARAM_STR);
            $INSERT->bindParam(":total",$res['TOTAL'],PDO::PARAM_STR);
            $INSERT->bindParam(":free",$res['FREE'],PDO::PARAM_STR);
            $INSERT->bindParam(":volume",$res['VOLUMN'],PDO::PARAM_STR);
            $INSERT->bindParam(":inventorID",$res['ID'],PDO::PARAM_INT);   
            $INSERT->execute();
            $nbInsertDrive++;
        }else{
            $row = $connexionNG->query($check)->fetch(PDO::FETCH_ASSOC); //Sur une même ligne ..
            $UPDATE->bindParam(":id",$row['id'],PDO::PARAM_INT);
            $UPDATE->bindParam(":id_type",$id_type,PDO::PARAM_INT);
            $UPDATE->bindParam(":total",$res['TOTAL'],PDO::PARAM_STR);
            $UPDATE->bindParam(":free",$res['FREE'],PDO::PARAM_STR);
            $UPDATE->execute();
            $nbUpdateDrive++;
        }                       
    } 
    $timeend=microtime(true);
    $time=$timeend-$timestart;
    $page_load_time_Drive +=$time; 
}
function importStorage(){
    global $connexionNG,$connexionOCS,$idZone,$idOCS,$stat,$mat,$nbInsertStorage,$nbUpdateStorage,$page_load_time_Storage,$tabConfig,$res;   
    $timestart=microtime(true);  
    //Marque et modele inconnu
    $requete="SELECT b.id as id_modele FROM marque a, modele b WHERE a.id=b.id_marque AND a.detail='Inconnu' AND b.detail='Inconnu' AND id_type=$tabConfig[storage] AND id_zone=$idZone;";
    $row = $connexionNG->query($requete)->fetch(PDO::FETCH_ASSOC); //Sur une même ligne ...
    $modeleInconnu=$row['id_modele'];
    
    $requete="INSERT INTO storage (id_modele,id_materiel,id_type,description,taille,sn,nom,updateOn,createOn,inventorID) values(:id_modele,:id_materiel,:id_type,:description,:taille,:sn,:nom,now(),now(),:inventorID)";
    $INSERT=$connexionNG->prepare($requete);                            

    $requete="UPDATE storage SET updateOn=now() WHERE inventorID=:id_ocs AND id_materiel=:id_materiel";
    $UPDATE=$connexionNG->prepare($requete);
    
    $requete="SELECT * FROM storages WHERE hardware_id=$idOCS"; 
    $loc = $connexionOCS->query($requete);                
    while ($res=$loc->fetch(PDO::FETCH_ASSOC)){    
        $id_modele=checkModele('storage','MODEL');                                        
        $check="SELECT id FROM storage WHERE inventorID='$res[ID]' AND id_materiel=$mat[id]";                    
        $stat=$connexionNG->query($check);
        if ($stat->rowCount()==0){    
            $INSERT->bindParam(":id_modele",$id_modele,PDO::PARAM_INT);
            $INSERT->bindParam(":id_materiel",$mat[id],PDO::PARAM_INT);
            $INSERT->bindParam(":id_type",$tabConfig['storage'],PDO::PARAM_INT);
            $INSERT->bindParam(":description",$res['DESCRIPTION'],PDO::PARAM_STR);
            $INSERT->bindParam(":taille",$res['DISKSIZE'],PDO::PARAM_STR);
            $INSERT->bindParam(":sn",$res['SERIALNUMBER'],PDO::PARAM_STR);                       
            $INSERT->bindParam(":nom",$res['NAME'],PDO::PARAM_STR); 
            $INSERT->bindParam(":inventorID",$res['ID'],PDO::PARAM_INT);   
            $INSERT->execute();
            $nbInsertStorage++;
        }else{
            $UPDATE->bindParam(":id_ocs",$res[ID],PDO::PARAM_INT);
            $UPDATE->bindParam(":id_materiel",$mat[id],PDO::PARAM_INT);
            $UPDATE->execute();
            $nbUpdateStorage++;
        }                      
    }  
    $timeend=microtime(true);
    $time=$timeend-$timestart;
    $page_load_time_Storage += $time;
}
function importSound(){
    global $connexionNG,$connexionOCS,$idZone,$idOCS,$stat,$mat,$nbOCS,$nbInsertSound,$nbUpdateSound,$page_load_time_Sound,$tabConfig,$res;  
    $timestart=microtime(true);  
    //Marque et modele inconnu
    $requete="SELECT b.id as id_modele FROM marque a, modele b WHERE a.id=b.id_marque AND a.detail='Inconnu' AND b.detail='Inconnu' AND id_type=$tabConfig[sound] AND id_zone=$idZone;";
    $row = $connexionNG->query($requete)->fetch(PDO::FETCH_ASSOC); //Sur une même ligne ...
    $modeleInconnu=$row['id_modele'];
    
    $requete="INSERT INTO sound (id_modele,id_materiel,id_type,updateOn,createOn,inventorID) values(:id_modele,:id_materiel,:id_type,now(),now(),:inventorID)";
    $INSERT=$connexionNG->prepare($requete);

    $requete="UPDATE sound SET updateOn=now() WHERE inventorID=:id_ocs AND id_materiel=:id_materiel";
    $UPDATE=$connexionNG->prepare($requete);
    
    $requete="SELECT * FROM sounds WHERE hardware_id=$idOCS"; 
    $loc = $connexionOCS->query($requete);   
    while ($res=$loc->fetch(PDO::FETCH_ASSOC)){                                                      
        $id_modele=checkModele('sound','NAME');
        $check="SELECT id FROM sound WHERE inventorID='$res[ID]' AND id_materiel=$mat[id]";                     
        $stat=$connexionNG->query($check);
        if ($stat->rowCount()==0){    
            $INSERT->bindParam(":id_modele",$id_modele,PDO::PARAM_INT);
            $INSERT->bindParam(":id_materiel",$mat[id],PDO::PARAM_INT);
            $INSERT->bindParam(":id_type",$tabConfig['drive'],PDO::PARAM_INT); 
            $INSERT->bindParam(":inventorID",$res['ID'],PDO::PARAM_INT);   
            $INSERT->execute();
            $nbInsertSound++;
        }else{
            $UPDATE->bindParam(":id_ocs",$res[ID],PDO::PARAM_INT);
            $UPDATE->bindParam(":id_materiel",$mat[id],PDO::PARAM_INT);
            $UPDATE->execute();
            $nbUpdateSound++;
        }                          
    } 
    $timeend=microtime(true);
    $time=$timeend-$timestart;
    $page_load_time_Sound +=$time;
}
function importVideo(){
    global $connexionNG,$connexionOCS,$idZone,$idOCS,$stat,$mat,$nbInsertVideo,$nbUpdateVideo,$page_load_time_Video,$tabConfig,$res; 
    $timestart=microtime(true);  
    //Marque et modele inconnu
    $requete="SELECT b.id as id_modele FROM marque a, modele b WHERE a.id=b.id_marque AND a.detail='Inconnu' AND b.detail='Inconnu' AND id_type=$tabConfig[video] AND id_zone=$idZone;";
    $row = $connexionNG->query($requete)->fetch(PDO::FETCH_ASSOC); //Sur une même ligne ...    
    $modeleInconnu=$row['id_modele'];
    $tabVideo=array(
        "Fujitsu Siemens Computers"=>"Fujitsu Siemens Computers",
        "Carte graphique standard"=>"Carte graphique standard",
        "Intel Corporation"=>"Intel(R)",
        "Mobile Intel(R)"=>"Intel(R)",
        "ATI Technologies Inc."=>"ATI",
        "S3 Inc."=>"S3",
        "S3 Graphics Inc."=>"S3",
        "ATI Technologies Inc"=>"ATI",
        "XGI Technology Inc."=>"XGI",
        "VIA Technologies, Inc."=>"VIA",
        "Jeu de puces Express Intel(R)"=>"Intel(R)",
        "nVidia Corporation"=>"NVIDIA",
        "Matrox Graphics, Inc."=>"Matrox",
        "Matrox Graphics"=>"Matrox",
        "Contrôleur vidéo"=>"Contrôleur vidéo"
        );                
    /*****/
    $requete="INSERT INTO video (id_modele,id_materiel,id_type,name,chipset,resolution,memory,updateOn,createOn,inventorID) values(:id_modele,:id_materiel,:id_type,:name,:chipset,:resolution,:memory,now(),now(),:inventorID)";
    $INSERT=$connexionNG->prepare($requete);
    
    $requete="UPDATE video SET updateOn=now() WHERE inventorID=:id_ocs AND id_materiel=:id_materiel";
    $UPDATE=$connexionNG->prepare($requete);
    
    $requete="SELECT * FROM videos WHERE hardware_id=$idOCS"; 
    $loc = $connexionOCS->query($requete);   
    while ($res=$loc->fetch(PDO::FETCH_ASSOC)){  
        foreach ($tabVideo as $key=>$video){
            if (stristr($res['NAME'],$key)){
                $extractModel=explode($key,$res['NAME']);
                $res[MANUFACTURER]=$video;
                $res[MODEL]=trim($extractModel[1]);
                $ok=1;break;
            }else{$ok=0;}
        }
        if ($ok==0){
            $extractModel=explode(' ',$res['NAME'],2);
            $res[MANUFACTURER]=trim($extractModel[0]);
            $res[MODEL]=($extractModel[1]);
        }                    
        $id_modele=checkModele('video','MODEL');
        $check="SELECT id FROM video WHERE inventorID='$res[ID]' AND id_materiel=$mat[id]";                    
        $stat=$connexionNG->query($check);
        if ($stat->rowCount()==0){    
            $INSERT->bindParam(":id_modele",$id_modele,PDO::PARAM_INT);
            $INSERT->bindParam(":id_materiel",$mat[id],PDO::PARAM_INT);
            $INSERT->bindParam(":id_type",$tabConfig['video'],PDO::PARAM_INT);                       
            $INSERT->bindParam(":name",$res['NAME'],PDO::PARAM_STR);  
            $INSERT->bindParam(":chipset",$res['CHIPSET'],PDO::PARAM_STR);  
            $INSERT->bindParam(":resolution",$res['RESOLUTION'],PDO::PARAM_STR);  
            $INSERT->bindParam(":memory",$res['MEMORY'],PDO::PARAM_STR);  
            $INSERT->bindParam(":inventorID",$res['ID'],PDO::PARAM_INT);   
            $INSERT->execute();
            $nbInsertVideo++;
        }else{
            $UPDATE->bindParam(":id_ocs",$res[ID],PDO::PARAM_INT);
            $UPDATE->bindParam(":id_materiel",$mat[id],PDO::PARAM_INT);
            $UPDATE->execute();
            $nbUpdateVideo++;
        }                          
    }
    $timeend=microtime(true);
    $time=$timeend-$timestart;
    $page_load_time_Video += $time;
}
function importMonitor(){
    global $connexionNG,$connexionOCS,$idZone,$idOCS,$stat,$mat,$tabConfig,$nbInsertMonitor,$nbUpdateMonitor,$page_load_time_Monitor,$tabConfig,$res;   
    $timestart=microtime(true);  
    //Marque et modele inconnu
    $requete="SELECT b.id as id_modele FROM marque a, modele b WHERE a.id=b.id_marque AND a.detail='Inconnu' AND b.detail='Inconnu' AND id_type=$tabConfig[monitor] AND id_zone=$idZone;";
    $row = $connexionNG->query($requete)->fetch(PDO::FETCH_ASSOC); //Sur une même ligne ...
    $modeleInconnu=$row['id_modele'];
    
    $requete="INSERT INTO monitor (id_modele,id_materiel,sn,updateOn,createOn,inventorID) values(:id_modele,:id_materiel,:sn,now(),now(),:inventorID)";
    $INSERT=$connexionNG->prepare($requete);
    
    $requete="UPDATE monitor SET updateOn=now() WHERE inventorID=:id_ocs AND id_materiel=:id_materiel";
    $UPDATE=$connexionNG->prepare($requete);
    
    $requete="SELECT * FROM monitors WHERE hardware_id=$idOCS"; 
    $loc = $connexionOCS->query($requete);   
    while ($res=$loc->fetch(PDO::FETCH_ASSOC)){                                                      
        $id_modele=checkModele('monitor','CAPTION');
        $check="SELECT id FROM monitor WHERE inventorID='$res[ID]' AND id_materiel=$mat[id]";                     
        $stat=$connexionNG->query($check);
        if ($stat->rowCount()==0){    
            $INSERT->bindParam(":id_modele",$id_modele,PDO::PARAM_INT);
            $INSERT->bindParam(":id_materiel",$mat[id],PDO::PARAM_INT);
            $INSERT->bindParam(":sn",$res['SERIAL'],PDO::PARAM_STR);     
            $INSERT->bindParam(":inventorID",$res['ID'],PDO::PARAM_INT);   
            $INSERT->execute();
            $nbInsertMonitor++;
        }else{
            $UPDATE->bindParam(":id_ocs",$res[ID],PDO::PARAM_INT);
            $UPDATE->bindParam(":id_materiel",$mat[id],PDO::PARAM_INT);
            $UPDATE->execute();
            $nbUpdateMonitor++;
        }                          
    }
    $timeend=microtime(true);
    $time=$timeend-$timestart;
    $page_load_time_Monitor += $time;
}
function importMemory(){
    global $connexionNG,$connexionOCS,$idZone,$idOCS,$stat,$mat,$nbInsertMemory,$nbUpdateMemory,$page_load_time_Memory,$tabConfig,$res;   
    $timestart=microtime(true);  
    //Marque et modele inconnu
    $requete="SELECT b.id as id_modele FROM marque a, modele b WHERE a.id=b.id_marque AND a.detail='Inconnu' AND b.detail='Inconnu' AND id_type=$tabConfig[memory] AND id_zone=$idZone;";
    $row = $connexionNG->query($requete)->fetch(PDO::FETCH_ASSOC); //Sur une même ligne ...
    $modeleInconnu=$row['id_modele'];

    $requete="INSERT INTO memory (id_modele,id_materiel,capacite,type,speed,slot,detail,destination,alias,sn,updateOn,createOn,inventorID) values(:id_modele,:id_materiel,:capacite,:type,:speed,:slot,:detail,:destination,:alias,:sn,now(),now(),:inventorID)";
    $INSERT=$connexionNG->prepare($requete);
    
    $requete="UPDATE memory SET updateOn=now() WHERE inventorID=:id_ocs AND id_materiel=:id_materiel";
    $UPDATE=$connexionNG->prepare($requete);
    
    $requete="SELECT * FROM memories WHERE hardware_id=$idOCS"; 
    $loc = $connexionOCS->query($requete);   
    while ($res=$loc->fetch(PDO::FETCH_ASSOC)){                                                      
        $id_modele=checkModele('memory','CAPTION');
        $check="SELECT id FROM memory WHERE inventorID='$res[ID]' AND id_materiel=$mat[id]";                     
        $stat=$connexionNG->query($check);
        if ($stat->rowCount()==0){    
            $INSERT->bindParam(":id_modele",$modeleInconnu,PDO::PARAM_INT);
            $INSERT->bindParam(":id_materiel",$mat[id],PDO::PARAM_INT);
            $INSERT->bindParam(":capacite",$res['CAPACITY'],PDO::PARAM_STR); 
            $INSERT->bindParam(":type",$res['TYPE'],PDO::PARAM_STR); 
            $INSERT->bindParam(":speed",$res['SPEED'],PDO::PARAM_STR); 
            $INSERT->bindParam(":slot",$res['NUMSLOTS'],PDO::PARAM_STR); 
            $INSERT->bindParam(":detail",$res['DESCRIPTION'],PDO::PARAM_STR); 
            $INSERT->bindParam(":destination",$res['PURPOSE'],PDO::PARAM_STR); 
            $INSERT->bindParam(":alias",$res['CAPTION'],PDO::PARAM_STR);                         
            $INSERT->bindParam(":sn",$res['SERIALNUMBER'],PDO::PARAM_STR);                       
            $INSERT->bindParam(":inventorID",$res['ID'],PDO::PARAM_INT);   
            $INSERT->execute();
            $nbInsertMemory++;
        }else{
            $UPDATE->bindParam(":id_ocs",$res[ID],PDO::PARAM_INT);
            $UPDATE->bindParam(":id_materiel",$mat[id],PDO::PARAM_INT);
            $UPDATE->execute();
            $nbUpdateMemory;
        }                          
    } 
    $timeend=microtime(true);
    $time=$timeend-$timestart;
    $page_load_time_Memory += $time;
}
function importPrinter(){
    global $connexionNG,$connexionOCS,$idZone,$idOCS,$stat,$mat,$nbInsertPrinter,$nbUpdatePrinter,$page_load_time_Printer,$tabConfig,$res;    
    $timestart=microtime(true);  
    //Marque et modele inconnu
    $requete="SELECT b.id as id_modele FROM marque a, modele b WHERE a.id=b.id_marque AND a.detail='Inconnu' AND b.detail='Inconnu' AND id_type=$tabConfig[printer] AND id_zone=$idZone;";
    $row = $connexionNG->query($requete)->fetch(PDO::FETCH_ASSOC); //Sur une même ligne ...
    $modeleInconnu=$row['id_modele'];
    $tabPrinter=array(
        "hp"=>"HP",
        "EPSON"=>"Epson"
        );                         
    $requete="INSERT INTO printer (id_modele,id_materiel,name,driver,port,updateOn,createOn,inventorID) values(:id_modele,:id_materiel,:name,:driver,:port,now(),now(),:inventorID)";
    $INSERT=$connexionNG->prepare($requete);
    
    $requete="UPDATE printer SET updateOn=now() WHERE inventorID=:id_ocs AND id_materiel=:id_materiel";
    $UPDATE=$connexionNG->prepare($requete);
    
    $requete="SELECT * FROM printers WHERE hardware_id=$idOCS"; 
    $loc = $connexionOCS->query($requete);   
    while ($res=$loc->fetch(PDO::FETCH_ASSOC)){     
        foreach ($tabPrinter as $key=>$printer){
            if (strstr($res['DRIVER'],$key)){
                $extractModel=explode($key,$res['DRIVER']);
                $res[MANUFACTURER]=$printer;
                $res[MODEL]=trim($extractModel[1]);
                $ok=1;break;
            }else{$ok=0;}
        }
        if ($ok==0){
            $extractModel=explode(' ',$res['DRIVER'],2);
            $res[MANUFACTURER]=trim($extractModel[0]);
            $res[MODEL]=($extractModel[1]);
        }
        $id_modele=checkModele('printer','MODEL');
        $check="SELECT id FROM printer WHERE inventorID='$res[ID]' AND id_materiel=$mat[id]";                     
        $stat=$connexionNG->query($check);
        if ($stat->rowCount()==0){    
            $INSERT->bindParam(":id_modele",$id_modele,PDO::PARAM_INT);
            $INSERT->bindParam(":id_materiel",$mat[id],PDO::PARAM_INT);
            $INSERT->bindParam(":name",$res['NAME'],PDO::PARAM_STR);     
            $INSERT->bindParam(":driver",$res['DRIVER'],PDO::PARAM_STR);     
            $INSERT->bindParam(":port",$res['PORT'],PDO::PARAM_STR);     
            $INSERT->bindParam(":inventorID",$res['ID'],PDO::PARAM_INT);   
            $INSERT->execute();
            $nbInsertPrinter++;
        }else{
            $UPDATE->bindParam(":id_ocs",$res[ID],PDO::PARAM_INT);
            $UPDATE->bindParam(":id_materiel",$mat[id],PDO::PARAM_INT);
            $UPDATE->execute();
            $nbUpdatePrinter++;
        }                          
    }
    $timeend=microtime(true);
    $time=$timeend-$timestart;
    $page_load_time_Printer += $time;
}
function importNet(){
    global $connexionNG,$connexionOCS,$idOCS,$stat,$mat,$nbInsertNet,$nbUpdateNet,$page_load_time_Net,$tabConfig,$res,$idZone;     
    $timestart=microtime(true);  
    //Marque et modele inconnu
    $requete="SELECT b.id as id_modele FROM marque a, modele b WHERE a.id=b.id_marque AND a.detail='Inconnu' AND b.detail='Inconnu' AND id_type=$tabConfig[net] AND id_zone=$idZone;";
    $row = $connexionNG->query($requete)->fetch(PDO::FETCH_ASSOC); //Sur une même ligne ...
    $modeleInconnu=$row['id_modele'];
    $tabNet=array(                    
        "Carte Ethernet Ã base ADMtek"=>"ADMtek",
        "base ADMtek"=>"AMDtek",
        "Carte Ethernet à base"=>"ADMtek",
        "Carte PCI ADMtek"=>"AMDtek",
        "ADM851X"=>"AMDtek",
        "Carte AMD"=>"AMD",
        "Marvell Yukon"=>"Marvell Yukon",
        "Connexion rÃ©seau Intel(R)"=>"Intel(R)",
        "Carte Intel(R)"=>"Intel(R)",
        "Connexion r?seau Intel(R)"=>"Intel(R)",
        "Connexion réseau Intel(R)"=>"Intel(R)",
        " base de "=>"Intel(R)",
        "Intel "=>"Intel(R)",
        "Carte réseau Fast Ethernet PCI Realtek"=>"Realtek",
        "Carte rÃ©seau Fast Ethernet PCI Realtek"=>"Realtek",
        "Carte rÃ©seau Fast Ethernet Realtek"=>"Realtek",
        "Carte rÃ©seau sans fil PCIE Realtek"=>"Realtek",
        "Carte réseau PCI-E Realtek"=>"Realtek",
        "Carte réseau Fast Ethernet Realtek"=>"Realtek",
        "Carte rÃ©seau Realtek"=>"Realtek",
        "Adaptateur rÃ©seau"=>"Realtek",
        "Carte r?seau Realtek"=>"Realtek",
        "Carte rÂ‚seau Realtek"=>"Realtek",
        "Carte réseau Realtek"=>"Realtek",
        "Carte Realtek"=>"Realtek",
        "Carte Ethernet Realtek"=>"Realtek",
        "Adaptateur réseau"=>"Realtek",
        "Contrôleur Fast Ethernet intégré 3Com"=>"3Com",
        "ContrÃ´leur Fast Ethernet intÃ©grÃ© 3Com"=>"3Com",
        "Carte rÃ©seau 3Com"=>"3Com",
        "Carte réseau 3Com"=>"3Com",                   
        "Carte rÃ©seau ASUS"=>"ASUS" ,
        "Carte réseau ASUS"=>"ASUS",
        "Carte réseau Broadcom"=>"Broadcom",
        "Carte rÃ©seau Broadcom"=>"Broadcom",
        "Carte Wi-Fi Broadcom"=>"Broadcom",
        "Contrôleur intégré Broadcom"=>"Broadcom",
        "Réseau local Broadcom"=>"Broadcom",
        "Contrôleur Broadcom"=>"Broadcom",
        "Carte Wifi Broadcom"=>"Broadcom",
        "Carte r?seau Broadcom"=>"Broadcom",
        "Ethernet Gigabit Broadcom"=>"Broadcom",
        "ContrÃ´leur Broadcom"=>"Broadcom",
        "Contr?leur Broadcom"=>"Broadcom",
        "Carte Wi-Fiÿ"=>"Broadcom",
        "Embedded Broadcom"=>"Broadcom",
        "RÃ©seau local Broadcom"=>"Broadcom",
        "Carte rÃ©seau "=>"SMC",
        "Carte SMC"=>"SMC",
        "Carte réseau Fast Ethernet"=>"SMC",                    
        "Carte réseau HP"=>"HP",
        "Carte Fast Ethernet Linksys"=>"Linksys",
        "Carte réseau sans fil Atheros"=>"Atheros",
        "Carte r?seau sans fil Atheros"=>"Atheros",
        "Carte WiFi Atheros"=>"Atheros",
        "Carte Fast Ethernet"=>"SiS",
        "SiS191"=>"SiS",
        "Carte Mini de réseau local sans fil"=>"Dell",
        "Carte Mini de r?seau local sans fil"=>"Dell",
        "Carte Mini Dell"=>"Dell",
        "Carte Mini de rÃ©seau local sans fil"=>"Dell",
        "Mini-carte "=>"Dell",
        "Carte D-Link"=>"D-Link",
        "Carte Ethernet D-Link"=>"D-Link",
        "Carte Accton"=>"Accton",
        "Carte VIA"=>"VIA",
        "Carte OvisLink"=>"OvisLink",
        "Carte Winbond"=>"Winbond",
        "National Semiconductor Corp."=>"National Semiconductor Corp.",
        "Carte KTI"=>"KTI",
        "Contrôleur de réseau NVIDIA"=>"NVIDIA",
        "Contr?leur de r?seau NVIDIA"=>"NVIDIA",
        "vmxnet3"=>"VMware",
        "Connexion au r?seau local"=>"Inconnu",
        "Carte r?seau sans fil"=>"Inconnu",
        "802.11n"=>"Inconnu",
        "10/100Mbps"=>"Inconnu",
        "P?riph?rique"=>"Inconnu",
        "PÃ©riphÃ©rique"=>"Inconnu",
        "PÂ‚riphÂ‚rique"=>"Inconnu",
         "Périphérique"=>"Inconnu",
        "1x1"=>"Inconnu",
        "WAN"=>"Inconnu",
         "Pilote de serveur d''accÃ¨s au rÃ©seau local"=>"Inconnu",
         "Pilote de serveur d''accès au réseau local Bluetooth"=>"Inconnu",
        "802.11bgn"=>"Inconnu",
        "Carte Half-Mini de réseau local sans fil"=>"Inconnu",
        "Carte Half-Mini de r?seau local sans fil"=>"Inconnu",
        "Carte Ethernet"=>"Inconnu",
        "11b/g/n"=>"Inconnu",
        "11b/g"=>"Inconnu",
        "PPP"=>"Inconnu",
         "Bluetooth"=>"Inconnu"
        );       
    $requete="INSERT INTO net (id_modele,id_materiel,type,ip,mac,mask,gw,subnet,speed,dhcp,status,updateOn,createOn,inventorID) values(:id_modele,:id_materiel,:type,:ip,:mac,:mask,:gw,:subnet,:speed,:dhcp,:status,now(),now(),:inventorID)";
    $INSERT=$connexionNG->prepare($requete);

    $requete="UPDATE net SET ip=:ip,mask=:mask,gw=:gw,subnet=:subnet,dhcp=:dhcp,status=:status,updateOn=now() WHERE id=:id";
    $UPDATE=$connexionNG->prepare($requete);

    $requete="SELECT * FROM networks WHERE hardware_id=$idOCS"; 
    $loc = $connexionOCS->query($requete);   
    while ($res=$loc->fetch(PDO::FETCH_ASSOC)){                                                                                                                       
        foreach ($tabNet as $key=>$net){
            if (strstr($res['DESCRIPTION'],$key)){
                $extractModel=explode($key,$res['DESCRIPTION']);
                $res[MANUFACTURER]=$net;
                $res[MODEL]=trim($extractModel[1]);
                $ok=1;break;
            }else{$ok=0;}
        }
        if ($ok==0){
            $extractModel=explode(' ',$res['DESCRIPTION'],2);
            $res[MANUFACTURER]=trim($extractModel[0]);
            $res[MODEL]=($extractModel[1]);
        }     
        $res[MODEL]=preg_replace("/ - Miniport d''ordonnancement de paquets/","",$res[MODEL]);
        $res[MODEL]=preg_replace("/- Trend Micro Common Firewall Miniport/","",$res[MODEL]);
        $res[MODEL]=preg_replace("/- Trend Micro Common Firewall Miniport/","",$res[MODEL]);
        $res[MODEL]=explode("#",$res[MODEL]);
        $res[MODEL]=trim($res[MODEL][0]);
        if (empty($res[MODEL]) && $res[MANUFACTURER]!='Marvell Yukon'){$res[MODEL]=$res[MANUFACTURER];$res[MANUFACTURER]='Inconnu';}
        if (empty($res[MODEL])){$res[MODEL]='Inconnu';}
        if (empty($res[MANUFACTURER])){$res[MANUFACTURER]='Inconnu';}      
        $id_modele=checkModele('net','MODEL');
        $check="SELECT id FROM net WHERE inventorID='$res[ID]' AND id_materiel=$mat[id]";                     
        $stat=$connexionNG->query($check);
        if ($stat->rowCount()==0){    
            $nbInsertNet++;
            $INSERT->bindParam(":id_modele",$id_modele,PDO::PARAM_INT);
            $INSERT->bindParam(":id_materiel",$mat[id],PDO::PARAM_INT);
            $INSERT->bindParam(":type",$res['TYPE'],PDO::PARAM_STR);     
            $INSERT->bindParam(":ip",$res['IPADDRESS'],PDO::PARAM_STR); 
            $INSERT->bindParam(":mask",$res['IPMASK'],PDO::PARAM_STR);
            $INSERT->bindParam(":mac",$res['MACADDR'],PDO::PARAM_STR);     
            $INSERT->bindParam(":gw",$res['IPGATEWAY'],PDO::PARAM_STR);    
            $INSERT->bindParam(":subnet",$res['IPSUBNET'],PDO::PARAM_STR);     
            $INSERT->bindParam(":speed",$res['SPEED'],PDO::PARAM_STR);     
            $INSERT->bindParam(":dhcp",$res['DHCP'],PDO::PARAM_STR);     
            $INSERT->bindParam(":status",$res['STATUS'],PDO::PARAM_STR);   
            $INSERT->bindParam(":inventorID",$res['ID'],PDO::PARAM_INT);                            
            $INSERT->execute();                        
        }else{
            //$row = $connexionNG->query($check)->fetch(PDO::FETCH_ASSOC); //Sur une même ligne ..
            $row = $stat->fetch(PDO::FETCH_ASSOC); //Sur une même ligne ..
            $UPDATE->bindParam(":id",$row['id'],PDO::PARAM_INT);
            $UPDATE->bindParam(":ip",$res['IPADDRESS'],PDO::PARAM_STR);     
            $UPDATE->bindParam(":mask",$res['IPMASK'],PDO::PARAM_STR);
            $UPDATE->bindParam(":gw",$res['IPGATEWAY'],PDO::PARAM_STR);    
            $UPDATE->bindParam(":subnet",$res['IPSUBNET'],PDO::PARAM_STR);                               
            $UPDATE->bindParam(":dhcp",$res['DHCP'],PDO::PARAM_STR);     
            $UPDATE->bindParam(":status",$res['STATUS'],PDO::PARAM_STR);   
            $UPDATE->execute();
            $nbUpdateNet++;
        }                     
    } 
    $timeend=microtime(true);
    $time=$timeend-$timestart;
    $page_load_time_Net += $time;
}
function importSoft(){
    global $connexionNG,$connexionOCS,$idZone,$idOCS,$stat,$mat,$nbInsertSoft,$nbUpdateSoft,$page_load_time_Soft,$tabConfig,$res;    
    $timestart=microtime(true);       
    $requete="SELECT DISTINCT alerte_adm, alerte_peda FROM soft WHERE editeur=:editeur,nom=:nom,version=:version;";
    $prep=$connexionNG->prepare($requete);
    $prep->bindParam(":editeur",$res['PUBLISHER'],PDO::PARAM_STR);     
    $prep->bindParam(":nom",$res['NAME'],PDO::PARAM_STR);     
    $prep->bindParam(":version",$res['VERSION'],PDO::PARAM_STR);     
    $prep->execute();
    $row = $prep->fetch(PDO::FETCH_ASSOC);
    $requete="INSERT INTO soft (id_materiel,editeur,nom,version,description,alerte_adm,alerte_peda,updateOn,createOn,inventorID) values(:id_materiel,:editeur,:nom,:version,:description,now(),now(),:inventorID)";
    $INSERT=$connexionNG->prepare($requete);
    
    $requete="UPDATE soft SET alerte_adm=:alerte_adm,alerte_peda=:alerte_peda,updateOn=now() WHERE inventorID=:id_ocs AND id_materiel=:id_materiel";
    $UPDATE=$connexionNG->prepare($requete);
    
    $requete="SELECT ID,PUBLISHER,NAME,VERSION,COMMENTS FROM softwares WHERE hardware_id=$idOCS"; 
    $loc = $connexionOCS->query($requete);   
    while ($res=$loc->fetch(PDO::FETCH_ASSOC)){            
        $check="SELECT id FROM soft WHERE inventorID='$res[ID]' AND id_materiel=$mat[id]";                     
        $stat=$connexionNG->query($check);
        if ($stat->rowCount()==0){    
            $INSERT->bindParam(":id_materiel",$mat[id],PDO::PARAM_INT);
            $INSERT->bindParam(":editeur",str_replace('"','',$res['PUBLISHER']),PDO::PARAM_STR);     
            $INSERT->bindParam(":nom",str_replace('"','',$res['NAME']),PDO::PARAM_STR);     
            $INSERT->bindParam(":version",$res['VERSION'],PDO::PARAM_STR);     
            $INSERT->bindParam(":description",$res['COMMENTS'],PDO::PARAM_STR);     
            $INSERT->bindParam(":inventorID",$res['ID'],PDO::PARAM_INT); 
            $UPDATE->bindParam(":alerte_adm",$row[alerte_adm],PDO::PARAM_INT);
            $UPDATE->bindParam(":alerte_peda",$row[alerte_peda],PDO::PARAM_INT);
            $INSERT->execute();
            $nbInsertSoft++;
        }else{
            $check="SELECT alerte_adm,alerte_peda FROM soft WHERE ((alerte_adm > 0 AND alerte_adm <= 5) OR (alerte_peda > 0 AND alerte_peda <= 5)) AND editeur='".str_replace('"','',$res['PUBLISHER'])."' AND nom='".str_replace('"','',$res['NAME'])."' LIMIT 1";                     
            $stat=$connexionNG->query($check);
            $row = $stat->fetch(PDO::FETCH_ASSOC); //Sur une même ligne ..
            $UPDATE->bindParam(":id_ocs",$res[ID],PDO::PARAM_INT);
            $UPDATE->bindParam(":id_materiel",$mat[id],PDO::PARAM_INT);
            $UPDATE->bindParam(":alerte_adm",$row[alerte_adm],PDO::PARAM_INT);
            $UPDATE->bindParam(":alerte_peda",$row[alerte_peda],PDO::PARAM_INT);
            $UPDATE->execute();
            $nbUpdateSoft++;
        }                     
    }
    $timeend=microtime(true);
    $time=$timeend-$timestart;
    $page_load_time_Soft += $time;
}
?>