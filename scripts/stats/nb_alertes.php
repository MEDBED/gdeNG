<?php
if(!isset($_COOKIE["ID_UTILISATEUR"])){
	header("Location: ../index.php");
	exit;
}
session_start();
include_once("../../header.inc.php");
include_once("../../include/functions.php");
//include_once("../include/protect_var.php");
try
{	
	connectSQL();
	include_once("../../include/protect_var.php");	
        $tabZone=array();
        //***********
	//Alertes OCS	                                                   
            $requete="SELECT distinct c.detail, count(id_type) as count,id_type,b.detail as type,a.id_zone FROM materiel a, type b, zone c WHERE a.id_type=b.id AND a.id_zone=c.id AND id_entite=:id_entite AND inventorId>0 AND inventorOn < CURRENT_TIMESTAMP - INTERVAL '7' DAY GROUP BY id_type,id_zone; ";//                 
            //$requete="SELECT distinct c.detail, count(id_type) as count,id_type,b.detail as type FROM materiel a, type b, zone c WHERE a.id_type=b.id AND a.id_zone=c.id AND id_entite=:id_entite GROUP BY id_type,id_zone; ";//                            
            $prep=$db->prepare($requete);	        
            $prep->bindParam(":id_entite",$_SESSION['id_entite'],PDO::PARAM_INT);	
            $prep->execute();					
            //Add all records to an array
            $rows = array();
            //$rows = $prep->fetchAll();
            while ($res=$prep->fetch(PDO::FETCH_ASSOC)){
                if (!in_array($res['id_zone'],$tabZone)){
                    $tabZone[]=$res['id_zone'];                    
                }
                $rows[]=array("detail"=>$res['detail'],$res['detail'],"count"=>$res['count'],$res['count'],"id_type"=>$res['id_type'],$res['id_type'],"type"=>$res['type'],$res['type']);
            }    
        //*****************
        //Alertes logiciels
            $requete="SELECT distinct c.detail, count(a.id_type) as count,a.id_type,b.detail as type,a.id_zone FROM materiel a,type b,zone c,soft d WHERE a.id_type=b.id AND a.id_zone=c.id AND d.id_materiel=a.id AND a.id_entite=:id_entite AND d.alerte_adm>1 AND d.alerte_adm<5 GROUP BY a.id_type,id_zone;";           
            $prep=$db->prepare($requete);	        
            $prep->bindParam(":id_entite",$_SESSION['id_entite'],PDO::PARAM_INT);	
            $prep->execute();
            while ($res=$prep->fetch(PDO::FETCH_ASSOC)){
                $exist=in_array_r($res[detail],$rows);
                if ($exist>=0){
                    //echo $exist.'=>'.$res['detail'].'=>'.$res['count'].'<br/>';
                    $rows[$exist]['count']+=$res['count'];
                }else{
                   $rows[]=array("detail"=>$res['detail'],$res['detail'],"count"=>$res['count'],$res['count'],"id_type"=>$res['id_type'],$res['id_type'],"type"=>$res['type'],$res['type']);
                }
                if (!in_array($res['id_zone'],$tabZone)){
                    $tabZone[]=$res['id_zone'];                    
                }
            }
        //***************
        //Alertes disques            
            $requete="SELECT distinct c.detail, count(a.id_type) as count,a.id_type,b.detail as type,a.id_zone FROM materiel a,type b,zone c,drive d WHERE a.id_type=b.id AND a.id_zone=c.id AND d.id_materiel=a.id AND a.id_entite=:id_entite AND d.free<=(d.total*0.06) AND d.total!='' GROUP BY a.id_type,id_zone;";           
            $prep=$db->prepare($requete);	        
            $prep->bindParam(":id_entite",$_SESSION['id_entite'],PDO::PARAM_INT);	
            $prep->execute();
            while ($res=$prep->fetch(PDO::FETCH_ASSOC)){
                $exist=in_array_r($res[detail],$rows);
                if ($exist>=0){
                    //echo $exist.'=>'.$res['detail'].'=>'.$res['count'].'<br/>';
                    $rows[$exist]['count']+=$res['count'];
                }else{
                   $rows[]=array("detail"=>$res['detail'],$res['detail'],"count"=>$res['count'],$res['count'],"id_type"=>$res['id_type'],$res['id_type'],"type"=>$res['type'],$res['type']);
                }
                if (!in_array($res['id_zone'],$tabZone)){
                    $tabZone[]=$res['id_zone'];                    
                }
            }
        //*****
        //xAxis            
            $zones="(";
            foreach ($tabZone as $key=>$zone){
                if ($key==0){
                    $zones.=$zone;
                }else{
                    $zones.=','.$zone;
                }
            }
            $zones.=")";
            $requete="SELECT distinct detail as detail1, count(id_zone) as count1 FROM materiel a, zone b WHERE a.id_zone=b.id AND id_entite=:id_entite AND id_zone IN $zones GROUP BY id_zone; ";//                   
            $prep=$db->prepare($requete);
            //echo $requete.'<br/><br/>';
            $prep->bindParam(":id_entite",$_SESSION['id_entite'],PDO::PARAM_INT);	
            $prep->execute();
            $recordCount=$prep->rowCount();	
            $rows1 = array();                
            $rows1 = $prep->fetchAll();
            //Nettoyage des valeurs inutilisées
            foreach ($rows1 as $key=>$item){
               $exist=in_array_r($item[detail1],$rows);
               //echo print_r($item).'=>'.$exist.'<br/>';
               if (!is_numeric($exist)){
                   //unset($rows1[$key]);
               }
            }
           //print_r($rows1);
        //*********
        //Résultats
            //Return result to jTable
            $jTableResult = array();
            //$jTableResult['Result'] = "OK";
           /* print_r($rows1);
            echo "<br/><br/>";
            print_r($rows);
            echo "<br/><br/>";     */   
            $jTableResult['TotalRecordCount'] = $recordCount;
            $jTableResult['xAxis'] = $rows1;
            $jTableResult['Records'] = $rows;
            $prep->closeCursor();
            $prep = NULL;	        	
            print json_encode($jTableResult);
            //print_r($rows);
	
	
}
catch(Exception $ex)
{
    //Return error message
	$jTableResult = array();
	$jTableResult['Result'] = "ERROR";
	$jTableResult['Message'] = '';
	print json_encode($jTableResult);
}
	
?>