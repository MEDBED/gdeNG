<?php 
if(!isset($_COOKIE["ID_UTILISATEUR"])){
	header("Location: ../logout.php");
	exit;
}
session_start();
$page="materiel.php";
$script="scripts/update_materiel.php";
if (empty($_GET['detail'])){
	$titre="Matériels";
}else{
	$titre=base64_decode($_GET['detail']);
}
$pageDescription="Liste des matériels";
include_once("../header.inc.php");
include_once("../include/functions.php");
include_once("../include/protect_var.php");
include_once("../include/textes.php");
include("../include/check_perms.php");
connectSQL();
$requete="SELECT id FROM zone WHERE id_pere=:id_pere";
$prep=$db->prepare($requete);
$prep->bindParam(":id_pere",$_SESSION['id_zone'],PDO::PARAM_INT);
$prep->execute();
$arrAll = $prep->fetchAll();
$_SESSION['id_zone_pere']=$_SESSION['id_zone'];
if(!empty($arrAll))
{
	$zone=$_SESSION['id_zone'];
	foreach ($arrAll as $res){		
		$zone.=','.$res[id];
	}	
	$_SESSION['id_zone']=$zone;
}
$prep->closeCursor();
$prep = NULL;
$_SESSION['PNG']=array();
echo '<a href="" style="display:none;" title=""></a>';
?>

<!--<script type="text/javascript" src="content/jquery-ui-1.9/ui/jquery-ui.js"></script>  
	<script type="text/javascript" src="content/jquery-ui-1.9/ui/jquery.ui.core.js"></script>
	<script type="text/javascript" src="content/jquery-ui-1.9/ui/jquery.ui.widget.js"></script>
	<script type="text/javascript" src="content/jquery-ui-1.9/ui/jquery.ui.position.js"></script>
	<script type="text/javascript" src="content/jquery-ui-1.9/ui/jquery.bgiframe.js"></script> 	
	<script type="text/javascript" src="content/jquery-ui-1.9/ui/jquery.ui.tooltip.js"></script>
-->

<html>
  <head>      
  </head>
  <body>       
  <div class="highslide-html-content" id="highslide-html2">
	<div class="highslide-header">	
	</div>
	<div class="highslide-body">
		<?php echo $pageHelp;?>	 
	</div>
    <div class="highslide-footer">
        <div>
            <span class="highslide-resize" title="Resize">
                <span></span>
            </span>
        </div>
    </div>
</div>
<div id="container2">
	<h1><?php  echo $titre;?></h1>
		<h2><?php  echo $pageDescription;?></h2>	
		<div id="mess" style="display: none;"></div>	
		<div id="help" title="Aide"><a href="#" onclick="return hs.htmlExpand(this, { contentId: 'highslide-html2',headingText: 'Aide',preserveContent: false } )"></a></div>
	<div class="content">		
		<div class="filtering">
	    <form>       
	     	Nom: <input type="text" name="Fnom" id="Fnom" size=5/>
	        Marque: <input type="text" name="Fmarque" id="Fmarque" size=5 style="height: 14px;font-size: small;"/>
	        Modèle: <input type="text" name="Fmodele" id="Fmodele" size=5/>       
	        Systéme: <input type="text" name="Fsysteme" id="Fsysteme" size=5/>
                @IP: <input type="text" name="Fip" id="Fip" size=5/>
	        <button type="submit" id="LoadRecordsButton" class="buttonValid">Filtrer</button>
	    </form>
		</div>
		<div id="SelectedRowList"></div>	
		<div id="masterContainer" style="width: 100%;padding-top: 0px;">	
			<div id="childContainer"></div>	
		</div>	
		<script type="text/javascript">	                    
		$(document).ready(function () {	
                //Prepare jTable               
		$('#masterContainer').jtable({
                    title: 'Liste des matériels',
                    paging: true,	
                    <?php 
                    if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & SUPPRESSION && 
                                    (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & SUPPRESSION){
                            echo '
                            selecting: true,
                            selectingCheckboxes: true,		
                            multiselect: true,
                            selectOnRowClick: false,	';
                    }
                    ?>														
                    pageSize: <?php echo $_SESSION['pageSize'];?>,
                    sorting: true,					
                    defaultSorting: 'nom ASC',
                    actions: {
                        <?php 						
                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & LECTURE &&
                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & LECTURE){
                                echo "listAction: '$script?action=list',";
                        }
                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & CREATION && 
                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & CREATION){
                                echo "createAction: '$script?action=create',";
                        }
                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION && 
                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                echo "updateAction: '$script?action=update',";
                        }
                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & SUPPRESSION && 
                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & SUPPRESSION){
                                echo "deleteAction: '$script?action=delete',";
                        }
                        ?>
                        /*listAction: '<?php echo $script;?>?action=list',*/
                        /*createAction: '<?php echo $script;?>?action=create',*/
                        /*updateAction: '<?php echo $script;?>?action=update',*/
                        /*deleteAction: '<?php echo $script;?>?action=delete'*/
                    },
                    fields: {
                        id_materiel: {
                            key: true,
                            create: false,
                            edit: false,
                            list: false
                        },
                        id_commande: {                           
                            create: false,
                            edit: false,
                            list: false
                        },
                        id_zone2: {                           
                            create: false,
                            list: false,
                            title: 'Zone',							
                            options: 'main/liste/getZone.php',
                            inputClass: 'validate[required]'
                        },
                        /*id_zone2: {                           
                            list: true,
                            title: 'Zone',							                            
                        },                        
                        id_type2: {                           
                            list: true,
                            title: 'Zone',							                            
                        },     */                   
                        id_zonec: {
                            list: false,
                            edit: false,
                            title: 'Zone',							
                            options: 'main/liste/getZone.php?strict',
                            inputClass: 'validate[required]'
                        },
                        add_suivi: {
                            title: '',
                            width: '1%',
                            listClass: 'jtableOption',
                            <?php 
                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                    echo "list: true,";
                            }else{echo "list: false,";}
                            ?>
                            edit: false,
                            create: false,	
                            sorting: false,						
                            display: function (netData) {				
                                //Create an image that will be used to open child table
                                var $img1 = new Array();
                                $img1 = $('<img src="graphs/icons/note.png" title="Ajouter ou modifier un événement" />')	                      
                                //Open child table when user clicks the image
                                $img1.click(function () {	                        	                 
                                    $('#masterContainer').jtable('openChildTable',	    	                            
                                        $img1.closest('tr'),
                                        {
                                            title: 'Suivi',	  
                                            pageSize: <?php echo $_SESSION['pageSize'];?>,
                                                sorting: true,	
                                                paging: true,
                                                defaultSorting: 'date DESC',					                                     
                                            actions: {
                                            <?php 
                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & LECTURE && 
                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & LECTURE){
                                                    echo "listAction: 'scripts/update_suivi.php?action=list&id_materiel=' + netData.record.id_materiel,";
                                            }
                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                    echo "updateAction: 'scripts/update_suivi.php?action=update&id_materiel=' + netData.record.id_materiel,";
                                            }
                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & CREATION &&
                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & CREATION){
                                                    echo "createAction: 'scripts/update_suivi.php?action=create&id_materiel=' + netData.record.id_materiel,";
                                            }
                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & SUPPRESSION &&
                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & SUPPRESSION){
                                                    echo "deleteAction: 'scripts/update_suivi.php?action=delete',";
                                            }
                                            ?>		                                            		                                            		                                           		                                            		                                            
                                            },
                                            fields: {   
                                                id_source: {
                                                    type: 'hidden',
                                                    create: true,
                                                    edit: true,
                                                    //list: true,
                                                    defaultValue: netData.record.id_materiel
                                                }  , 
                                                source: {
                                                    type: 'hidden',
                                                    create: true,
                                                    edit: true,
                                                    //list: true,
                                                    defaultValue: 'materiel'
                                                }  ,                                      
                                                id_suivi: {	                                            					                                            
                                                    key: true,
                                                    create: false,
                                                    edit: false,
                                                    list: false                                          
                                                },			                                            		                                           						                    						
                                                utilisateur:{
                                                    title: 'Utilisateur',
                                                    width: '10%',
                                                    create: false,
                                                    edit : false,
                                                } ,		                                            
                                                date: {
                                                    title: 'Date',
                                                    width: '10%',
                                                    type: 'date',
                                                    displayFormat: 'dd-mm-yy',
                                                    inputClass: 'validate[custom[datefr]]'
                                                    /*create: false,
                                                    edit: false*/
                                                }, 
                                                detail:{
                                                    title: 'Suivi',
                                                    type: 'textarea',
                                                    width: '80%',
                                                    display: function (data){
                                                        var info = '';
                                                        if (data.record.priorite==1){
                                                                info = '<div title="Priorité basse" style="background-color: #826666; width: 10px; height:10px;display: inline-block ;">&nbsp;</div>&nbsp;';
                                                        }else  if (data.record.priorite==2){
                                                                info = '<div title="Priorité normale" style="background-color: #98A3F2; width: 10px; height:10px;display: inline-block ;">&nbsp;</div>&nbsp;';
                                                        }else  if (data.record.priorite==3){
                                                                info = '<div title="Priorité haute" style="background-color: #F76C6C; width: 10px; height:10px;display: inline-block ;">&nbsp;</div>&nbsp;';
                                                        }
                                                        return info+data.record.detail;
                                                    },
                                                    //list: false,
                                                }, 
                                                priorite: {
                                                    title: 'Priorité',
                                                    width: '3%',
                                                    type: 'radiobutton',
                                                    options: { '1': 'Faible','2': 'Normal','3': 'Haute' },			                                            			                                           
                                                    list: false,
                                                },                                    	                                          		                                            	                                                                                    
                                            }
                                        }, 
                                        function (data) { //opened handler		                                   
                                            data.childTable.jtable('load');		                                                                                                                      
                                        });	 	                                               
                                    });	                       
                                    //Return image to show on the person row	  	                                                    	                                      
                                    return $img1;	                        
	                    	}
                            },
                            add_doc: {
                                title: '',
                                width: '1%',
                                listClass: 'jtableOption',
                                <?php 
                                if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                        echo "list: true,";
                                }else{echo "list: false,";}
                                ?>
                                edit: false,
                                create: false,	
                                sorting: false,						
                                display: function (netData) {				
                                //Create an image that will be used to open child table
                                var $img33 = new Array();
                                $img33 = $('<img src="graphs/icons/doc.png" title="Ajouter ou modifier un document" />')	                      
                                //Open child table when user clicks the image
                                $img33.click(function () {	                                    
                                    $('#masterContainer').jtable('openChildTable',	    	                            
                                        $img33.closest('tr'),
                                        {
                                            title: '<a href="#" onclick="return hs.htmlExpand(this, { src: \'main/visionneuse.php?id='+netData.record.id_materiel+'\',objectType: \'iframe\', headingText: \'Visionneuse\',width: 900,preserveContent: false });"><img src="graphs/icons/visionneuse.png" alt="Visionneuse" title="Lancer la visionneuse"></a>&nbsp;Liste des documents ',	  
                                            pageSize: <?php echo $_SESSION['pageSize'];?>,
                                            sorting: true,	
                                            paging: true,
                                            defaultSorting: 'date DESC',						                                     
                                            actions: {
                                            <?php 
                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & LECTURE && 
                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & LECTURE){
                                                    echo "listAction: 'scripts/update_document.php?action=list&source=materiel&id_materiel=' + netData.record.id_materiel,";
                                            }
                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                    echo "updateAction: 'scripts/update_document.php?action=update&source=materiel&id_materiel=' + netData.record.id_materiel,";
                                            }
                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & CREATION &&
                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & CREATION){
                                                    echo "createAction: 'scripts/update_document.php?action=create&source=materiel&id_materiel=' + netData.record.id_materiel,";
                                            }
                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & SUPPRESSION &&
                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & SUPPRESSION){
                                                    echo "deleteAction: 'scripts/update_document.php?action=delete&source=materiel',";
                                            }
                                            ?>		                                            		                                            		                                           		                                            		                                            
                                            },
                                            fields: {   
                                                id_source: {
                                                    type: 'hidden',
                                                    create: true,
                                                    edit: true,
                                                    //list: true,
                                                    defaultValue: netData.record.id_materiel
                                                }  , 
                                                source: {
                                                    type: 'hidden',
                                                    create: true,
                                                    edit: true,
                                                    //list: true,
                                                    defaultValue: 'materiel'
                                                }  ,                                      
                                                id_document: {	                                            					                                            
                                                    key: true,
                                                    create: false,
                                                    edit: false,
                                                    list: false                                          
                                                },		
                                                fic:{
                                                    title: '',
                                                    width: '1%',                                                   
                                                    type: 'file',
                                                    edit: false,
                                                    sorting: false,
                                                    display: function (data){                                                        
                                                        info = '<div><form method="post" action="scripts/download.php"><input type="hidden" name="file" value="'+data.record.fic+'"><input type="hidden" name="filename" value="'+data.record.ficName+'"><input type="submit" value="" style="cursor: pointer;" class="buttonDownload" title="Télécharger"></form>';
                                                        return info;
                                                    },
                                                    //inputClass: 'validate[custom[fileExtension]]'
                                                } ,
                                                voir: {
                                                    title: '',
                                                    width: '1%',
                                                    edit: false,
                                                    create: false,
                                                    sorting: false,
                                                    display: function(data){
                                                        if(checkExt(data.record.fic,'img')){
                                                            return '<a class="titi" href="scripts/affImage.php?file='+data.record.fic+'" rel="prettyPhoto" alt="'+data.record.ficName+'" title="'+data.record.description+'"><img src="graphs/icons/voir.png" alt="'+data.record.ficName+'"></a>';
                                                        }
                                                    }
                                                },
                                                /*visionneuse: {
                                                    title: '',
                                                    width: '1%',
                                                    sorting: false,
                                                    edit: false,
                                                    create: false,
                                                    display: function(data){
                                                        return '<a href="#" onclick="return hs.htmlExpand(this, { src: \'main/visionneuse.php?source=materiel&id='+data.record.id_source+'\',objectType: \'iframe\', headingText: \'Visionneuse\',width: 900,preserveContent: false });"><img src="graphs/icons/visionneuse.png" alt="'+data.record.ficName+'"></a>';
                                                    }
                                                },*/
                                                ficName:{
                                                    title: 'Nom',
                                                    width: '10%',                                                                                                    
                                                    /*edit: false,
                                                    create: false,*/
                                                    //inputClass: 'validate[custom[fileExtension]]'
                                                } ,
                                                ext: {
                                                    title: 'Extension',
                                                    width: '5%',
                                                    edit: false,
                                                    create: false,
                                                    display: function (data){
                                                        f = data.record.fic;
                                                        return f.substring((Math.max(0, f.lastIndexOf(".")) || f.length) + 1);
                                                    }
                                                },
                                                description:{
                                                    title: 'Description',                                                                            
                                                    width: '30%',
                                                    type: 'textarea',
                                                },  
                                                utilisateur:{
                                                    title: 'Ajouté par',
                                                    width: '15%',
                                                    create: false,
                                                    edit : false,
                                                } ,
                                                acces: {
                                                    title: 'Accessible à',
                                                    width: '15%',                                                    
                                                    options: { '0':'Tout le monde','1': 'A mon groupe','2': 'Moi uniquement','3': 'Tous sauf entité' },			                                            			                                                                                               
                                                }, 
                                                dateFin: {
                                                    title: 'Fin de validité',
                                                    width: '10%',
                                                    type: 'date',
                                                    displayFormat: 'dd-mm-yy',
                                                    inputClass: 'validate[custom[datefr]]'
                                                    /*create: false,
                                                    edit: false*/
                                                }, 
                                                updateOnDoc: {
                                                    title: 'Modifié le',
                                                    width: '10%',
                                                    type: 'date',
                                                    displayFormat: 'dd-mm-yy',
                                                    inputClass: 'validate[custom[datefr]]',
                                                    create: false,
                                                    edit: false
                                                }, 
                                            }
                                        }, 
                                        function (data) { //opened handler		                                   
                                            data.childTable.jtable('load','',function(){
                                                jQuery("a[rel^='prettyPhoto']").prettyPhoto({
                                                    social_tools: false
                                                });                                                
                                            });                                                                                       
                                        });	 	                                               
                                    });	                       
                                    //Return image to show on the person row	  	                                                    	                                      
                                    return $img33;	                        
	                    	}
                            },
                            add_opt: {
                                title: '',
                                width: '1%',
                                listClass: 'jtableOption',
                                <?php 
                                    if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                    (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                            echo "list: true,";
                                    }else{echo "list: false,";}
                                 ?>
                                 edit: false,
                                 create: false,	                                   
                                 sorting: false,
                                 display: function (netData) {				
                                    //Create an image that will be used to open child table
                                    var $img0 = new Array();
                                    $img0 = $('<img src="graphs/icons/config.png" title="Détails du matériel" />')	                      
                                    //Open child table when user clicks the image
                                    $img0.click(function () {	                        	                 
                                        $('#masterContainer').jtable('openChildTable',	    	                            
                                            $img0.closest('tr'),
                                            {
                                                title: '<div style="display: inline;margin-right: 3px;"><img src="graphs/icons/config.png" width="16" height="16"></div>Configuration du matériel',
                                                titleClass: 'jtable-title-child jtable-title',                                                
                                                actions: {
                                                <?php 
                                                if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & LECTURE && 
                                                                (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & LECTURE){
                                                        echo "listAction: 'scripts/update_opt.php?action=list&id_materiel=' + netData.record.id_materiel,";
                                                }
                                                ?>
                                                    listAction: 'scripts/update_opt.php?action=list&id_materiel=' + netData.record.id_materiel,
                                                },
                                                fields: {
                                                    id_materiel: {                                                       
                                                        type: 'hidden',
                                                        create: false,
                                                        edit: false,                                                        
                                                        //list: false
                                                    },
                                                    //Vlan
                                                    add_vlan: {
                                                        title: 'Ports',  
                                                        width: '2%',
                                                        //listClass: 'jtableOption',
                                                        <?php 
                                                        if ($_SESSION['id_zone']==4 && (int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                echo "list: true,";
                                                        }else{echo "list: false,";}
                                                        ?>
                                                        edit: false,
                                                        create: false,	
                                                        sorting: false,						
                                                        display: function (optData) {				
                                                            //Create an image that will be used to open child table
                                                            var $img29 = new Array();
                                                            $img29 = $('<img src="graphs/icons/vlan.png" title="Ajouter ou modifier la configuration des ports" />')	                      
                                                            //Open child table when user clicks the image
                                                            $img29.click(function () {	                                                           
                                                                $('#masterContainer').jtable('openChildTable',	    	                            
                                                                    $img0.closest('tr'),
                                                                    {
                                                                        title: '<div style="display: inline;margin-right: 3px;"><img src="graphs/icons/vlan.png" width="16" height="16"></div>Configuration des ports pour : '+optData.record.nom,	
                                                                        titleClass: 'jtable-title-child jtable-title',
                                                                        pageSize: <?php echo $_SESSION['pageSize'];?>,
                                                                        sorting: true,	
                                                                        paging: true,
                                                                        defaultSorting: 'port ASC',					                                     
                                                                        actions: {
                                                                        <?php 
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & LECTURE && 
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & LECTURE){
                                                                                echo "listAction: 'scripts/update_vlan.php?action=list&id_materiel=' + optData.record.id_materiel,";
                                                                        }
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                                echo "updateAction: 'scripts/update_vlan.php?action=update&id_materiel=' + optData.record.id_materiel,";
                                                                        }
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & CREATION &&
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & CREATION){
                                                                                echo "createAction: 'scripts/update_vlan.php?action=create&id_materiel=' + optData.record.id_materiel,";
                                                                        }
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & SUPPRESSION &&
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & SUPPRESSION){
                                                                                echo "deleteAction: 'scripts/update_vlan.php?action=delete',";
                                                                        }
                                                                        ?>		                                            		                                            		                                           		                                            		                                            
                                                                        },
                                                                        fields: {   
                                                                            id_materiel: {
                                                                                type: 'hidden',
                                                                                create: true,
                                                                                list: true,
                                                                                defaultValue: optData.record.id_materiel
                                                                            }  ,                                       
                                                                            id_vlan: {	                                            					                                            
                                                                                key: true,
                                                                                create: false,
                                                                                edit: false,
                                                                                list: false                                          
                                                                            },	
                                                                            port: {
                                                                                edit: false,
                                                                                title: 'Port',
                                                                                width: '2%'
                                                                            },
                                                                            vlan_name:{
                                                                                title: 'Vlan',
                                                                                width: '5%'
                                                                            },  
                                                                            vlan_config:{
                                                                                title: 'Configuration',
                                                                                width: '15%',
                                                                                options: {'':'','No':'No','Tagged':'Tagged','Untagged':'Untagged'}
                                                                            },
                                                                            description:{
                                                                                title: 'Description',                                                                            
                                                                                width: '58%',
                                                                                type: 'textarea',
                                                                            },                                                                                                                                                                                                                              
                                                                    }
                                                                }, function (data) { //opened handler		                                   
                                                                    data.childTable.jtable('load');		                                                                          
                                                                    //$('#childContainer').jtable('load');
                                                                });	 	                                               
                                                            });	                       
                                                            //Return image to show on the person row	  	                                                    	                                      
                                                            return $img29;	                        
                                                        }
                                                    },
                                                    //Soft
                                                    add_soft: {
                                                        title: 'Logiciels',  
                                                        width: '2%',
                                                        //listClass: 'jtableOption',
                                                        <?php 
                                                        if ($_SESSION['id_zone']!=6 && $_SESSION['id_zone']!=4 && (int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                echo "list: true,";
                                                        }else{echo "list: false,";}
                                                        ?>
                                                        edit: false,
                                                        create: false,	
                                                        sorting: false,						
                                                        display: function (optData) {				
                                                            //Create an image that will be used to open child table
                                                            var $img28 = new Array();
                                                            $img28 = $('<img src="graphs/icons/software.png" title="Ajouter ou modifier un logiciel" />')	                      
                                                            //Open child table when user clicks the image
                                                            $img28.click(function () {	                                                           
                                                                $('#masterContainer').jtable('openChildTable',	    	                            
                                                                    $img0.closest('tr'),
                                                                    {
                                                                        title: '<div style="display: inline;margin-right: 3px;"><img src="graphs/icons/software.png" width="16" height="16"></div>Liste des logiciels pour : '+optData.record.nom,	
                                                                        titleClass: 'jtable-title-child jtable-title',
                                                                        pageSize: <?php echo $_SESSION['pageSize'];?>,
                                                                        sorting: true,	
                                                                        paging: true,
                                                                        defaultSorting: 'editeur,nom ASC',					                                     
                                                                        actions: {
                                                                        <?php 
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & LECTURE && 
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & LECTURE){
                                                                                echo "listAction: 'scripts/update_soft.php?action=list&id_materiel=' + optData.record.id_materiel,";
                                                                        }
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                                echo "updateAction: 'scripts/update_soft.php?action=update&id_materiel=' + optData.record.id_materiel,";
                                                                        }
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & CREATION &&
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & CREATION){
                                                                                echo "createAction: 'scripts/update_soft.php?action=create&id_materiel=' + optData.record.id_materiel,";
                                                                        }
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & SUPPRESSION &&
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & SUPPRESSION){
                                                                                echo "deleteAction: 'scripts/update_soft.php?action=delete',";
                                                                        }
                                                                        ?>		                                            		                                            		                                           		                                            		                                            
                                                                        },
                                                                        fields: {   
                                                                            id_materiel: {
                                                                                type: 'hidden',
                                                                                create: true,
                                                                                list: true,
                                                                                defaultValue: optData.record.id_materiel
                                                                            }  ,                                       
                                                                            id_soft: {	                                            					                                            
                                                                                key: true,
                                                                                create: false,
                                                                                edit: false,
                                                                                list: false                                          
                                                                            },	                                                                                                                                                                                                                                                                                                                 
                                                                            editeur:{
                                                                                title: 'Editeur',
                                                                                width: '30%'
                                                                            },  
                                                                            nom:{
                                                                                title: 'Nom',
                                                                                width: '60%'
                                                                            },
                                                                            version:{
                                                                                title: 'Version',                                                                            
                                                                                width: '10%'
                                                                            }, 
                                                                            alerte_adm:{
                                                                                title: 'Alerte A',
                                                                                width: '1%',
                                                                                listClass: 'jtableTdCenter',
                                                                                create: false,
                                                                                edit: false,
                                                                                display: function (data){
                                                                                    var alerte = '';
                                                                                    if (data.record.alerte_adm==1){
                                                                                        info = '<div title="Aucun risque, contrôle de l\'installation" style="background-color: #339933; width: 10px; height:10px;display: inline-block ;">&nbsp;</div>&nbsp;';
                                                                                    }else  if (data.record.alerte_adm==2){
                                                                                        info = '<div title="Risque modéré, installation inutile" style="background-color: #FFC629; width: 10px; height:10px;display: inline-block ;">&nbsp;</div>&nbsp;';
                                                                                    }else  if (data.record.alerte_adm==3){
                                                                                        info = '<div title="Risque important, installation gênant le bon fonctionnement du poste" style="background-color: #FF8F29; width: 10px; height:10px;display: inline-block ;">&nbsp;</div>&nbsp;';
                                                                                    }else  if (data.record.alerte_adm==4){
                                                                                        info = '<div title="Risque majeur, installation perturbant le poste voir tout le réseau informatique" style="background-color: #FF0000; width: 10px; height:10px;display: inline-block ;">&nbsp;</div>&nbsp;';
                                                                                    }else  if (data.record.alerte_adm==5){
                                                                                        info = '<div title="Aucune alerte" style="background-color: #7d7d7d; width: 10px; height:10px;display: inline-block ;">&nbsp;</div>&nbsp;';
                                                                                    }else{
                                                                                        info=data.record.alerte_adm;
                                                                                    }
                                                                                    return info;
                                                                                },
                                                                            },
                                                                            alerte_peda:{
                                                                                title: 'Alerte P',
                                                                                width: '1%',
                                                                                listClass: 'jtableTdCenter',
                                                                                create: false,
                                                                                edit: false,
                                                                                display: function (data){
                                                                                    var alerte = '';
                                                                                    if (data.record.alerte_peda==1){
                                                                                        info = '<div title="Aucun risque, contrôle de l\'installation" style="background-color: #339933; width: 10px; height:10px;display: inline-block ;">&nbsp;</div>&nbsp;';
                                                                                    }else  if (data.record.alerte_peda==2){
                                                                                        info = '<div title="Risque modéré, installation inutile" style="background-color: #FFC629; width: 10px; height:10px;display: inline-block ;">&nbsp;</div>&nbsp;';
                                                                                    }else  if (data.record.alerte_peda==3){
                                                                                        info = '<div title="Risque important, installation gênant le bon fonctionnement du poste" style="background-color: #FF8F29; width: 10px; height:10px;display: inline-block ;">&nbsp;</div>&nbsp;';
                                                                                    }else  if (data.record.alerte_peda==4){
                                                                                        info = '<div title="Risque majeur, installation perturbant le poste voir tout le réseau informatique" style="background-color: #FF0000; width: 10px; height:10px;display: inline-block ;">&nbsp;</div>&nbsp;';
                                                                                    }else  if (data.record.alerte_peda==5){
                                                                                        info = '<div title="Aucune alerte" style="background-color: #7d7d7d; width: 10px; height:10px;display: inline-block ;">&nbsp;</div>&nbsp;';    
                                                                                    }else{
                                                                                        info=data.record.alerte_adm;
                                                                                    }
                                                                                    return info;
                                                                                },
                                                                            },
                                                                            description:{
                                                                                title: 'Description',
                                                                                type: 'textarea',
                                                                                list: false 
                                                                            } ,                                                                                                                                                   
                                                                    }
                                                                }, function (data) { //opened handler		                                   
                                                                    data.childTable.jtable('load');		                                                                          
                                                                    //$('#childContainer').jtable('load');
                                                                });	 	                                               
                                                            });	                       
                                                            //Return image to show on the person row	  	                                                    	                                      
                                                            return $img28;	                        
                                                        }
                                                    },
                                                    //Net
                                                    add_net: {
                                                        title: 'Réseau',
                                                        width: '2%',
                                                        //listClass: 'jtableOption',
                                                        <?php 
                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                echo "list: true,";
                                                        }else{echo "list: false,";}
                                                        ?>
                                                        edit: false,
                                                        create: false,	
                                                        sorting: false,						
                                                        display: function (optData) {				
                                                            //Create an image that will be used to open child table
                                                            var $img2 = new Array();
                                                            $img2 = $('<img src="graphs/icons/net.png" title="Ajouter ou modifier une carte réseau" id="mat_'+optData.record.id_materiel+'" />')	                      
                                                            //Open child table when user clicks the image
                                                            $img2.click(function () {	
                                                                //alert($img2.closest('td').toSource());
                                                                $('#masterContainer').jtable('openChildTable',	    	                            
                                                                    $img0.closest('tr'),
                                                                    //$img0.parent().siblings(":first"),
                                                                    {
                                                                        title: '<div style="display: inline;margin-right: 3px;"><img src="graphs/icons/net.png" width="16" height="16"></div>Cartes réseaux pour : '+optData.record.nom,
                                                                        titleClass: 'jtable-title-child jtable-title',
                                                                        pageSize: <?php echo $_SESSION['pageSize'];?>,
                                                                        //sorting: true,	
                                                                        paging: true,
                                                                        //defaultSorting: 'ip ASC',					                                     
                                                                        actions: {
                                                                        <?php 
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & LECTURE && 
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & LECTURE){
                                                                                echo "listAction: 'scripts/update_net.php?action=list&id_materiel=' + optData.record.id_materiel,";
                                                                        }
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                                echo "updateAction: 'scripts/update_net.php?action=update&id_materiel=' + optData.record.id_materiel,";
                                                                        }
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & CREATION &&
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & CREATION){
                                                                                echo "createAction: 'scripts/update_net.php?action=create&id_materiel=' + optData.record.id_materiel,";
                                                                        }
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & SUPPRESSION &&
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & SUPPRESSION){
                                                                                echo "deleteAction: 'scripts/update_net.php?action=delete',";
                                                                        }
                                                                        ?>		                                            		                                            		                                           		                                            		                                            
                                                                        },
                                                                        fields: {   
                                                                            id_materiel: {
                                                                                type: 'hidden',
                                                                                create: true,
                                                                                list: true,
                                                                                defaultValue: optData.record.id_materiel
                                                                            }  ,                                       
                                                                            id_net: {	                                            					                                            
                                                                                key: true,
                                                                                create: false,
                                                                                edit: false,
                                                                                list: false                                          
                                                                            },
                                                                            status:{
                                                                                title: 'Status', 
                                                                                width:'2%'
                                                                            },
                                                                            id_marque: {
                                                                                title: 'Marque',
                                                                                width: '10%',
                                                                                options: 'main/liste/getMarque.php?id_type=<?php echo $_SESSION["CONFIG"]['net'];?>'                                                                                  
                                                                            },	 
                                                                            id_modele: {
                                                                                type: 'hidden',                                                                                   
                                                                            },
                                                                            net_modele_edit: {
                                                                                title: '',
                                                                                width: '1%',
                                                                                <?php 
                                                                                if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                                                (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                                        echo "list: true,";
                                                                                }else{echo "list: false,";}
                                                                                ?>
                                                                                edit: false,
                                                                                sorting: false,								
                                                                                create: false,													                    						
                                                                                display: function (netChildData) {				
                                                                                    //Create an image that will be used to open child table
                                                                                    var $img3 = new Array();
                                                                                    $img3 = $('<img src="graphs/icons/edit.png" title="Modifier le modèle" />')	                      
                                                                                    //Open child table when user clicks the image
                                                                                    $img3.click(function () {	                        	                 
                                                                                    $('#masterContainer').jtable('openChildTable',	    	                            
                                                                                        $img2.closest('td'),
                                                                                        {
                                                                                            title: 'Modèle(s) de la marque ' + netChildData.record.marque,
                                                                                            titleClass: 'jtable-title-child jtable-title',
                                                                                            pageSize: <?php echo $_SESSION['pageSize'];?>,		                    					
                                                                                            paging: true,	
                                                                                            sorting: true,	                    					
                                                                                            defaultSorting: 'modele2 ASC',				                                       
                                                                                            actions: {
                                                                                            <?php 
                                                                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & LECTURE && 
                                                                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & LECTURE){
                                                                                                    echo "listAction: 'scripts/update_modele.php?action=list&id_marque=' + netChildData.record.id_marque + '&id_materiel=' + netChildData.record.id_materiel + '&id_modele=' + netChildData.record.id_modele + '&id_type=$_SESSION[CONFIG][net]',";
                                                                                            }
                                                                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                                                    echo "updateAction: 'scripts/update_modele.php?action=update&id_marque=' + netChildData.record.id_marque + '&id_materiel=' + netChildData.record.id_materiel + '&id_modele=' + netChildData.record.id_modele + '&id_type=$_SESSION[CONFIG][net]',";
                                                                                            }
                                                                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & CREATION &&
                                                                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & CREATION){
                                                                                                    echo "createAction: 'scripts/update_modele.php?action=create&id_marque=' + netChildData.record.id_marque + '&id_materiel=' + netChildData.record.id_materiel + '&id_modele=' + netChildData.record.id_modele + '&id_type=$_SESSION[CONFIG][net]',";
                                                                                            }
                                                                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & SUPPRESSION &&
                                                                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & SUPPRESSION){
                                                                                                    echo "deleteAction: 'scripts/update_modele.php?action=delete',";
                                                                                            }
                                                                                            ?>		                                            		                                            		                                           		                                            		                                            
                                                                                            },
                                                                                            fields: {   		                    		                                        	                                                                                                                          
                                                                                                id_net: {	  
                                                                                                    key: true,                                          					                                            
                                                                                                    type: 'hidden',		                    			                                            
                                                                                                    defaultValue: netChildData.record.id_net	                                            
                                                                                                },		
                                                                                                id_modele2: {		                    		                                        		  	                                        		                                        	                      			
                                                                                                    create: false,
                                                                                                    edit: false,
                                                                                                    list: false,
                                                                                                },                    		                                            
                                                                                                modele_edit2: {
                                                                                                    title: '',
                                                                                                    width: '1%',
                                                                                                    edit: false,
                                                                                                    create: false,		                        								
                                                                                                    //options: 'main/liste/getModele.php',
                                                                                                    display: function (modeleData) {				
                                                                                                        //Create an image that will be used to open child table
                                                                                                        var $img5 = new Array();
                                                                                                        $img5 = $('<img src="graphs/icons/valid.png" title="Choisir ce modèle" />')	                      
                                                                                                        //Open child table when user clicks the image
                                                                                                        $img5.click(function () {	                        	                 
                                                                                                            $('#masterContainer').jtable('updateRecord', {	    	                            
                                                                                                                record: {		                        	                                   
                                                                                                                    id_materiel: netChildData.record.id_materiel,		                    			                        	                                    
                                                                                                                    id_materiel2: netChildData.record.id_net,
                                                                                                                    id_modele: modeleData.record.id_modele2,		                    			                        	                                   
                                                                                                                    source: 'net'
                                                                                                                },	                        	                                    
                                                                                                                url: 'scripts/update_modele.php?action=change',
                                                                                                                success: function(){
                                                                                                                    $('#masterContainer').jtable('closeChildTable',$img3.closest('td'));
                                                                                                                }		                        	                                                      	                                                     	                                     	                               
                                                                                                            });	                        	                             	                        	                           
                                                                                                        });	                       
                                                                                                        //Return image to show on the person row	  	                        	                                                    	                                      
                                                                                                        return $img5;	                        	                        
                                                                                                    }  
                                                                                                },                                         	                                          
                                                                                                modele2: {
                                                                                                    title: 'Modèle',
                                                                                                    width: '90%',
                                                                                                    sorting: true
                                                                                                },  	                                                                                     
                                                                                            }
                                                                                        }, function (data2) { //opened handler		                                   
                                                                                            data2.childTable.jtable('load');		                                                                          
                                                                                        });	 	                                               
                                                                                    });	                       
                                                                                    //Return image to show on the person row	  	                                                    	                                      
                                                                                    return $img3;	                        
                                                                                }
                                                                        },					
                                                                        modele:{
                                                                            title: 'Modèle',
                                                                            width: '10%',
                                                                            edit: false,
                                                                            create: false,
                                                                        },
                                                                        id_type_net:{
                                                                            type: 'hidden',
                                                                            value: '<?php echo $_SESSION[CONFIG][net];?>'
                                                                        }, 
                                                                        ip:{
                                                                            title: '@ IP',
                                                                            inputClass: 'validate[custom[ipv4]]'			                                            
                                                                        },
                                                                        mac:{
                                                                            title: '@ MAC',
                                                                        } ,
                                                                        mask:{
                                                                            title: 'Masque',                                                                           
                                                                            inputClass: 'validate[custom[ipv4]]'
                                                                        }, 
                                                                        gw:{
                                                                            title: 'Passerelle',
                                                                            list: false,
                                                                            inputClass: 'validate[custom[ipv4]]'
                                                                        } ,
                                                                        subnet:{
                                                                            title: 'Réseau',
                                                                            list: false,
                                                                            inputClass: 'validate[custom[ipv4]]'
                                                                        },
                                                                        dhcp:{
                                                                            title: 'DHCP',
                                                                            list: false,
                                                                            inputClass: 'validate[custom[ipv4]]'
                                                                        },
                                                                        speed:{
                                                                            title: 'Vitesse',
                                                                            list: false,
                                                                        },
                                                                        type:{
                                                                            title: 'Type',
                                                                            list: false,
                                                                        },                                                                        
                                                                        sn:{
                                                                            title: 'SN',
                                                                            list: false,
                                                                        },	                                                                       
                                                                        date_installe: {
                                                                            title: 'Installé le',
                                                                            width: '10%',
                                                                            type: 'date',
                                                                            displayFormat: 'dd-mm-yy',
                                                                            inputClass: 'validate[custom[datefr]]'                                                                          
                                                                        },
                                                                        description:{
                                                                            title: 'Description',
                                                                            list: false,
                                                                            type: 'textarea'
                                                                        },													
                                                                    }
                                                                }, function (data) { //opened handler		                                   
                                                                    data.childTable.jtable('load');		                                                                          
                                                                    //$('#childContainer').jtable('load');
                                                                });	 	                                               
                                                            });	                       
                                                            //Return image to show on the person row	  	                                                    	                                      
                                                            return $img2;	                        
                                                        }
                                                    },
                                                    //Storage
                                                    add_storage: {
                                                        title: 'Stockage',
                                                        width: '2%',
                                                        //listClass: 'jtableOption',
                                                        <?php 
                                                        if ($_SESSION['id_zone']!=6 && $_SESSION['id_zone']!=4 && (int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                echo "list: true,";
                                                        }else{echo "list: false,";}
                                                        ?>
                                                        edit: false,
                                                        create: false,	
                                                        sorting: false,						
                                                        display: function (optData) {				
                                                            //Create an image that will be used to open child table
                                                            var $img9 = new Array();
                                                            $img9 = $('<img src="graphs/icons/storage.png" title="Ajouter ou modifier une unité de stockage" />')	                      
                                                            //Open child table when user clicks the image
                                                            $img9.click(function () {	                                                           
                                                                $('#masterContainer').jtable('openChildTable',	    	                            
                                                                    $img0.closest('tr'),
                                                                    {
                                                                        title: '<div style="display: inline;margin-right: 3px;"><img src="graphs/icons/storage.png" width="16" height="16"></div>Unité d\'impression pour : '+optData.record.nom,	
                                                                        titleClass: 'jtable-title-child jtable-title',
                                                                        pageSize: <?php echo $_SESSION['pageSize'];?>,
                                                                        sorting: true,	
                                                                        paging: true,
                                                                        defaultSorting: 'nom ASC',					                                     
                                                                        actions: {
                                                                        <?php 
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & LECTURE && 
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & LECTURE){
                                                                                echo "listAction: 'scripts/update_storage.php?action=list&id_materiel=' + optData.record.id_materiel,";
                                                                        }
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                                echo "updateAction: 'scripts/update_storage.php?action=update&id_materiel=' + optData.record.id_materiel,";
                                                                        }
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & CREATION &&
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & CREATION){
                                                                                echo "createAction: 'scripts/update_storage.php?action=create&id_materiel=' + optData.record.id_materiel,";
                                                                        }
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & SUPPRESSION &&
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & SUPPRESSION){
                                                                                echo "deleteAction: 'scripts/update_storage.php?action=delete',";
                                                                        }
                                                                        ?>		                                            		                                            		                                           		                                            		                                            
                                                                        },
                                                                        fields: {   
                                                                            id_materiel: {
                                                                                type: 'hidden',
                                                                                create: true,
                                                                                list: true,
                                                                                defaultValue: optData.record.id_materiel
                                                                            }  ,                                       
                                                                            id_storage: {	                                            					                                            
                                                                                key: true,
                                                                                create: false,
                                                                                edit: false,
                                                                                list: false                                          
                                                                            },
                                                                            nom:{
                                                                              title: 'Nom' ,
                                                                            },
                                                                            id_marque: {
                                                                                title: 'Marque',
                                                                                width: '10%',
                                                                                options: 'main/liste/getMarque.php?id_type=<?php echo $_SESSION["CONFIG"]['storage'];?>'
                                                                            },	 
                                                                            id_modele: {
                                                                                type: 'hidden',                                                                                   
                                                                            },
                                                                            storage_modele_edit: {
                                                                                title: '',
                                                                                width: '1%',
                                                                                <?php 
                                                                                if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                                                (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                                        echo "list: true,";
                                                                                }else{echo "list: false,";}
                                                                                ?>
                                                                                edit: false,
                                                                                sorting: false,								
                                                                                create: false,													                    						
                                                                                display: function (netChildData) {				
                                                                                    //Create an image that will be used to open child table
                                                                                    var $img10 = new Array();
                                                                                    $img10 = $('<img src="graphs/icons/edit.png" title="Modifier le modèle" />')	                      
                                                                                    //Open child table when user clicks the image
                                                                                    $img10.click(function () {	                        	                 
                                                                                    $('#masterContainer').jtable('openChildTable',	    	                            
                                                                                        $img10.closest('td'),
                                                                                        {
                                                                                            title: 'Modèle(s) de la marque ' + netChildData.record.marque,
                                                                                            titleClass: 'jtable-title-child jtable-title',
                                                                                            pageSize: <?php echo $_SESSION['pageSize'];?>,		                    					
                                                                                            paging: true,	
                                                                                            sorting: true,	                    					
                                                                                            defaultSorting: 'modele2 ASC',				                                       
                                                                                            actions: {
                                                                                            <?php 
                                                                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & LECTURE && 
                                                                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & LECTURE){
                                                                                                    echo "listAction: 'scripts/update_modele.php?action=list&id_marque=' + netChildData.record.id_marque + '&id_materiel=' + netChildData.record.id_materiel + '&id_modele=' + netChildData.record.id_modele + '&id_type=$_SESSION[CONFIG][storage]',";
                                                                                            }
                                                                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                                                    echo "updateAction: 'scripts/update_modele.php?action=update&id_marque=' + netChildData.record.id_marque + '&id_materiel=' + netChildData.record.id_materiel + '&id_modele=' + netChildData.record.id_modele + '&id_type=$_SESSION[CONFIG][storage]',";
                                                                                            }
                                                                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & CREATION &&
                                                                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & CREATION){
                                                                                                    echo "createAction: 'scripts/update_modele.php?action=create&id_marque=' + netChildData.record.id_marque + '&id_materiel=' + netChildData.record.id_materiel + '&id_modele=' + netChildData.record.id_modele + '&id_type=$_SESSION[CONFIG][storage]',";
                                                                                            }
                                                                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & SUPPRESSION &&
                                                                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & SUPPRESSION){
                                                                                                    echo "deleteAction: 'scripts/update_modele.php?action=delete',";
                                                                                            }
                                                                                            ?>		                                            		                                            		                                           		                                            		                                            
                                                                                            },
                                                                                            fields: {   		                    		                                        	                                                                                                                          
                                                                                                id_storage: {	  
                                                                                                    key: true,                                          					                                            
                                                                                                    type: 'hidden',		                    			                                            
                                                                                                    defaultValue: netChildData.record.id_storage	                                            
                                                                                                },		
                                                                                                id_modele2: {		                    		                                        		  	                                        		                                        	                      			
                                                                                                    create: false,
                                                                                                    edit: false,
                                                                                                    list: false,
                                                                                                },                    		                                            
                                                                                                modele_edit2: {
                                                                                                    title: '',
                                                                                                    width: '1%',
                                                                                                    edit: false,
                                                                                                    create: false,		                        								
                                                                                                    //options: 'main/liste/getModele.php',
                                                                                                    display: function (modeleData) {				
                                                                                                        //Create an image that will be used to open child table
                                                                                                        var $img11 = new Array();
                                                                                                        $img11 = $('<img src="graphs/icons/valid.png" title="Choisir ce modèle" />')	                      
                                                                                                        //Open child table when user clicks the image
                                                                                                        $img11.click(function () {	                        	                 
                                                                                                            $('#masterContainer').jtable('updateRecord', {	    	                            
                                                                                                                record: {		                        	                                   
                                                                                                                    id_materiel: netChildData.record.id_materiel,		                    			                        	                                    
                                                                                                                    id_materiel2: netChildData.record.id_storage,
                                                                                                                    id_modele: modeleData.record.id_modele2,		                    			                        	                                   
                                                                                                                    source: 'printer'
                                                                                                                },	                        	                                    
                                                                                                                url: 'scripts/update_modele.php?action=change',
                                                                                                                success: function(){
                                                                                                                    $('#masterContainer').jtable('closeChildTable',$img10.closest('td'));
                                                                                                                }		                        	                                                      	                                                     	                                     	                               
                                                                                                            });	                        	                             	                        	                           
                                                                                                        });	                       
                                                                                                        //Return image to show on the person row	  	                        	                                                    	                                      
                                                                                                        return $img11;	                        	                        
                                                                                                    }  
                                                                                                },                                         	                                          
                                                                                                modele2: {
                                                                                                    title: 'Modèle',
                                                                                                    width: '90%',
                                                                                                    sorting: true
                                                                                                },  	                                                                                     
                                                                                            }
                                                                                        }, function (data2) { //opened handler		                                   
                                                                                            data2.childTable.jtable('load');		                                                                          
                                                                                        });	 	                                               
                                                                                    });	                       
                                                                                    //Return image to show on the person row	  	                                                    	                                      
                                                                                    return $img10;	                        
                                                                                }
                                                                        },					
                                                                        modele:{
                                                                            title: 'Modèle',
                                                                            width: '10%',
                                                                            edit: false,
                                                                            create: false,
                                                                        },   
                                                                        id_type_storage:{
                                                                            type: 'hidden',
                                                                            value: '<?php echo $_SESSION[CONFIG][storage];?>'
                                                                        }, 
                                                                        nom:{
                                                                            title: 'Nom',                                                                           
                                                                        },                                                                        
                                                                        description:{
                                                                            title: 'Description',                                                                           
                                                                        },
                                                                        taille:{
                                                                            title: 'Taille (Mo)',                                                                            
                                                                        },    
                                                                        sn:{
                                                                            title: 'S/N',                                                                            
                                                                        },                                                                         
                                                                        date_installe: {
                                                                            title: 'Installé le',
                                                                            width: '10%',
                                                                            type: 'date',
                                                                            displayFormat: 'dd-mm-yy',
                                                                            inputClass: 'validate[custom[datefr]]'                                                                          
                                                                        }
                                                                    }
                                                                }, function (data) { //opened handler		                                   
                                                                    data.childTable.jtable('load');		                                                                          
                                                                    //$('#childContainer').jtable('load');
                                                                });	 	                                               
                                                            });	                       
                                                            //Return image to show on the person row	  	                                                    	                                      
                                                            return $img9;	                        
                                                        }
                                                    },   
                                                    //Stockage
                                                    add_drive: {
                                                        title: 'Lecteur/Volume',
                                                        width: '2%',
                                                        //listClass: 'jtableOption',
                                                        <?php 
                                                        if ($_SESSION['id_zone']!=6 && $_SESSION['id_zone']!=4 && (int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                echo "list: true,";
                                                        }else{echo "list: false,";}
                                                        ?>
                                                        edit: false,
                                                        create: false,	
                                                        sorting: false,						
                                                        display: function (optData) {				
                                                            //Create an image that will be used to open child table
                                                            var $img6 = new Array();
                                                            $img6 = $('<img src="graphs/icons/drive.png" title="Ajouter ou modifier un lecteur ou volume" />')	                      
                                                            //Open child table when user clicks the image
                                                            $img6.click(function () {	                                                           
                                                                $('#masterContainer').jtable('openChildTable',	    	                            
                                                                    $img0.closest('tr'),
                                                                    {
                                                                        title: '<div style="display: inline;margin-right: 3px;"><img src="graphs/icons/drive.png" width="16" height="16"></div>Unité de stockage pour : '+optData.record.nom,	
                                                                        titleClass: 'jtable-title-child jtable-title',
                                                                        pageSize: <?php echo $_SESSION['pageSize'];?>,
                                                                        sorting: true,	
                                                                        paging: true,
                                                                        defaultSorting: 'type ASC',					                                     
                                                                        actions: {
                                                                        <?php 
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & LECTURE && 
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & LECTURE){
                                                                                echo "listAction: 'scripts/update_drive.php?action=list&id_materiel=' + optData.record.id_materiel,";
                                                                        }
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                                echo "updateAction: 'scripts/update_drive.php?action=update&id_materiel=' + optData.record.id_materiel,";
                                                                        }
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & CREATION &&
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & CREATION){
                                                                                echo "createAction: 'scripts/update_drive.php?action=create&id_materiel=' + optData.record.id_materiel,";
                                                                        }
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & SUPPRESSION &&
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & SUPPRESSION){
                                                                                echo "deleteAction: 'scripts/update_drive.php?action=delete',";
                                                                        }
                                                                        ?>		                                            		                                            		                                           		                                            		                                            
                                                                        },
                                                                        fields: {   
                                                                            id_materiel: {
                                                                                type: 'hidden',
                                                                                create: true,
                                                                                list: true,
                                                                                defaultValue: optData.record.id_materiel
                                                                            }  ,                                       
                                                                            id_drive: {	                                            					                                            
                                                                                key: true,
                                                                                create: false,
                                                                                edit: false,
                                                                                list: false                                          
                                                                            },	                                                                                                                                                                                                                                    
                                                                             id_type_drive: {							
                                                                                title: 'Type',
                                                                                width: '1%',							
                                                                                options: 'main/liste/getType.php?source=drive',
                                                                                inputClass: 'validate[required]',
                                                                             },
                                                                            lettre:{
                                                                                title: 'Lettre',                                                                            
                                                                            },  
                                                                            volume:{
                                                                                title: 'Volume',                                                                           
                                                                            },
                                                                            fs:{
                                                                                title: 'FS',                                                                            
                                                                            }, 
                                                                            total:{
                                                                                title: 'Capacité (Mo)',
                                                                                inputClass: 'validate[custom[integer]]'
                                                                            } ,
                                                                            free:{
                                                                                title: 'Espace libre (Mo)',
                                                                                inputClass: 'validate[custom[integer]]'         
                                                                            }                                                                        
                                                                    }
                                                                }, function (data) { //opened handler		                                   
                                                                    data.childTable.jtable('load');		                                                                          
                                                                    //$('#childContainer').jtable('load');
                                                                });	 	                                               
                                                            });	                       
                                                            //Return image to show on the person row	  	                                                    	                                      
                                                            return $img6;	                        
                                                        }
                                                    },
                                                    //Printer
                                                    add_printer: {
                                                        title: 'Impression',
                                                        width: '2%',
                                                        //listClass: 'jtableOption',
                                                        <?php 
                                                        if ($_SESSION['id_zone']!=6 && $_SESSION['id_zone']!=4 && (int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                echo "list: true,";
                                                        }else{echo "list: false,";}
                                                        ?>
                                                        edit: false,
                                                        create: false,	
                                                        sorting: false,						
                                                        display: function (optData) {				
                                                            //Create an image that will be used to open child table
                                                            var $img9 = new Array();
                                                            $img9 = $('<img src="graphs/icons/printer.png" title="Ajouter ou modifier une unité d\'impression" />')	                      
                                                            //Open child table when user clicks the image
                                                            $img9.click(function () {	                                                           
                                                                $('#masterContainer').jtable('openChildTable',	    	                            
                                                                    $img0.closest('tr'),
                                                                    {
                                                                        title: '<div style="display: inline;margin-right: 3px;"><img src="graphs/icons/printer.png" width="16" height="16"></div>Unité d\'impression pour : '+optData.record.nom,	
                                                                        titleClass: 'jtable-title-child jtable-title',
                                                                        pageSize: <?php echo $_SESSION['pageSize'];?>,
                                                                        sorting: true,	
                                                                        paging: true,
                                                                        defaultSorting: 'name ASC',					                                     
                                                                        actions: {
                                                                        <?php 
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & LECTURE && 
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & LECTURE){
                                                                                echo "listAction: 'scripts/update_printer.php?action=list&id_materiel=' + optData.record.id_materiel,";
                                                                        }
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                                echo "updateAction: 'scripts/update_printer.php?action=update&id_materiel=' + optData.record.id_materiel,";
                                                                        }
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & CREATION &&
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & CREATION){
                                                                                echo "createAction: 'scripts/update_printer.php?action=create&id_materiel=' + optData.record.id_materiel,";
                                                                        }
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & SUPPRESSION &&
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & SUPPRESSION){
                                                                                echo "deleteAction: 'scripts/update_printer.php?action=delete',";
                                                                        }
                                                                        ?>		                                            		                                            		                                           		                                            		                                            
                                                                        },
                                                                        fields: {   
                                                                            id_materiel: {
                                                                                type: 'hidden',
                                                                                create: true,
                                                                                list: true,
                                                                                defaultValue: optData.record.id_materiel
                                                                            }  ,                                       
                                                                            id_printer: {	                                            					                                            
                                                                                key: true,
                                                                                create: false,
                                                                                edit: false,
                                                                                list: false                                          
                                                                            },	
                                                                            id_marque: {
                                                                                title: 'Marque',
                                                                                width: '10%',
                                                                                options: 'main/liste/getMarque.php?id_type=<?php echo $_SESSION["CONFIG"]['printer'];?>'
                                                                            },	 
                                                                            id_modele: {
                                                                                type: 'hidden',                                                                                   
                                                                            },
                                                                            printer_modele_edit: {
                                                                                title: '',
                                                                                width: '1%',
                                                                                <?php 
                                                                                if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                                                (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                                        echo "list: true,";
                                                                                }else{echo "list: false,";}
                                                                                ?>
                                                                                edit: false,
                                                                                sorting: false,								
                                                                                create: false,													                    						
                                                                                display: function (netChildData) {				
                                                                                    //Create an image that will be used to open child table
                                                                                    var $img10 = new Array();
                                                                                    $img10 = $('<img src="graphs/icons/edit.png" title="Modifier le modèle" />')	                      
                                                                                    //Open child table when user clicks the image
                                                                                    $img10.click(function () {	                        	                 
                                                                                    $('#masterContainer').jtable('openChildTable',	    	                            
                                                                                        $img10.closest('td'),
                                                                                        {
                                                                                            title: 'Modèle(s) de la marque ' + netChildData.record.marque,
                                                                                            titleClass: 'jtable-title-child jtable-title',
                                                                                            pageSize: <?php echo $_SESSION['pageSize'];?>,		                    					
                                                                                            paging: true,	
                                                                                            sorting: true,	                    					
                                                                                            defaultSorting: 'modele2 ASC',				                                       
                                                                                            actions: {
                                                                                            <?php 
                                                                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & LECTURE && 
                                                                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & LECTURE){
                                                                                                    echo "listAction: 'scripts/update_modele.php?action=list&id_marque=' + netChildData.record.id_marque + '&id_materiel=' + netChildData.record.id_materiel + '&id_modele=' + netChildData.record.id_modele + '&id_type=$_SESSION[CONFIG][printer]',";
                                                                                            }
                                                                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                                                    echo "updateAction: 'scripts/update_modele.php?action=update&id_marque=' + netChildData.record.id_marque + '&id_materiel=' + netChildData.record.id_materiel + '&id_modele=' + netChildData.record.id_modele + '&id_type=$_SESSION[CONFIG][printer]',";
                                                                                            }
                                                                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & CREATION &&
                                                                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & CREATION){
                                                                                                    echo "createAction: 'scripts/update_modele.php?action=create&id_marque=' + netChildData.record.id_marque + '&id_materiel=' + netChildData.record.id_materiel + '&id_modele=' + netChildData.record.id_modele + '&id_type=$_SESSION[CONFIG][printer]',";
                                                                                            }
                                                                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & SUPPRESSION &&
                                                                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & SUPPRESSION){
                                                                                                    echo "deleteAction: 'scripts/update_modele.php?action=delete',";
                                                                                            }
                                                                                            ?>		                                            		                                            		                                           		                                            		                                            
                                                                                            },
                                                                                            fields: {   		                    		                                        	                                                                                                                          
                                                                                                id_printer: {	  
                                                                                                    key: true,                                          					                                            
                                                                                                    type: 'hidden',		                    			                                            
                                                                                                    defaultValue: netChildData.record.id_printer	                                            
                                                                                                },		
                                                                                                id_modele2: {		                    		                                        		  	                                        		                                        	                      			
                                                                                                    create: false,
                                                                                                    edit: false,
                                                                                                    list: false,
                                                                                                },                    		                                            
                                                                                                modele_edit2: {
                                                                                                    title: '',
                                                                                                    width: '1%',
                                                                                                    edit: false,
                                                                                                    create: false,		                        								
                                                                                                    //options: 'main/liste/getModele.php',
                                                                                                    display: function (modeleData) {				
                                                                                                        //Create an image that will be used to open child table
                                                                                                        var $img11 = new Array();
                                                                                                        $img11 = $('<img src="graphs/icons/valid.png" title="Choisir ce modèle" />')	                      
                                                                                                        //Open child table when user clicks the image
                                                                                                        $img11.click(function () {	                        	                 
                                                                                                            $('#masterContainer').jtable('updateRecord', {	    	                            
                                                                                                                record: {		                        	                                   
                                                                                                                    id_materiel: netChildData.record.id_materiel,		                    			                        	                                    
                                                                                                                    id_materiel2: netChildData.record.id_printer,
                                                                                                                    id_modele: modeleData.record.id_modele2,		                    			                        	                                   
                                                                                                                    source: 'printer'
                                                                                                                },	                        	                                    
                                                                                                                url: 'scripts/update_modele.php?action=change',
                                                                                                                success: function(){
                                                                                                                    $('#masterContainer').jtable('closeChildTable',$img10.closest('td'));
                                                                                                                }		                        	                                                      	                                                     	                                     	                               
                                                                                                            });	                        	                             	                        	                           
                                                                                                        });	                       
                                                                                                        //Return image to show on the person row	  	                        	                                                    	                                      
                                                                                                        return $img11;	                        	                        
                                                                                                    }  
                                                                                                },                                         	                                          
                                                                                                modele2: {
                                                                                                    title: 'Modèle',
                                                                                                    width: '90%',
                                                                                                    sorting: true
                                                                                                },  	                                                                                     
                                                                                            }
                                                                                        }, function (data2) { //opened handler		                                   
                                                                                            data2.childTable.jtable('load');		                                                                          
                                                                                        });	 	                                               
                                                                                    });	                       
                                                                                    //Return image to show on the person row	  	                                                    	                                      
                                                                                    return $img10;	                        
                                                                                }
                                                                        },					
                                                                        modele:{
                                                                            title: 'Modèle',
                                                                            width: '10%',
                                                                            edit: false,
                                                                            create: false,
                                                                        },   
                                                                        id_type_printer:{
                                                                            type: 'hidden',
                                                                            value: '<?php echo $_SESSION[CONFIG][printer];?>'
                                                                        }, 
                                                                        name:{
                                                                            title: 'Nom',                                                                            
                                                                        },  
                                                                        driver:{
                                                                            title: 'Driver',                                                                           
                                                                        },
                                                                        port:{
                                                                            title: 'Port',                                                                            
                                                                        },    
                                                                        sn:{
                                                                            title: 'S/N',                                                                            
                                                                        },                                                                         
                                                                        date_installe: {
                                                                            title: 'Installé le',
                                                                            width: '10%',
                                                                            type: 'date',
                                                                            displayFormat: 'dd-mm-yy',
                                                                            inputClass: 'validate[custom[datefr]]'                                                                          
                                                                        }
                                                                    }
                                                                }, function (data) { //opened handler		                                   
                                                                    data.childTable.jtable('load');		                                                                          
                                                                    //$('#childContainer').jtable('load');
                                                                });	 	                                               
                                                            });	                       
                                                            //Return image to show on the person row	  	                                                    	                                      
                                                            return $img9;	                        
                                                        }
                                                    },                                                    
                                                    //Video
                                                    add_video: {
                                                        title: 'Vidéo',
                                                        width: '2%',
                                                        //listClass: 'jtableOption',
                                                        <?php 
                                                        if ($_SESSION['id_zone']!=6 && $_SESSION['id_zone']!=4 && (int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                echo "list: true,";
                                                        }else{echo "list: false,";}
                                                        ?>
                                                        edit: false,
                                                        create: false,	
                                                        sorting: false,						
                                                        display: function (optData) {				
                                                            //Create an image that will be used to open child table
                                                            var $img12 = new Array();
                                                            $img12 = $('<img src="graphs/icons/video.png" title="Ajouter ou modifier une unité de stockage" />')	                      
                                                            //Open child table when user clicks the image
                                                            $img12.click(function () {	                                                           
                                                                $('#masterContainer').jtable('openChildTable',	    	                            
                                                                   $img0.closest('tr'),
                                                                    {
                                                                        title: '<div style="display: inline;margin-right: 3px;"><img src="graphs/icons/video.png" width="16" height="16"></div>Unité vidéo pour : '+optData.record.nom,
                                                                        titleClass: 'jtable-title-child jtable-title',
                                                                        pageSize: <?php echo $_SESSION['pageSize'];?>,
                                                                        sorting: true,	
                                                                        paging: true,
                                                                        defaultSorting: 'nom ASC',					                                     
                                                                        actions: {
                                                                        <?php 
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & LECTURE && 
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & LECTURE){
                                                                                echo "listAction: 'scripts/update_video.php?action=list&id_materiel=' + optData.record.id_materiel,";
                                                                        }
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                                echo "updateAction: 'scripts/update_video.php?action=update&id_materiel=' + optData.record.id_materiel,";
                                                                        }
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & CREATION &&
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & CREATION){
                                                                                echo "createAction: 'scripts/update_video.php?action=create&id_materiel=' + optData.record.id_materiel,";
                                                                        }
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & SUPPRESSION &&
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & SUPPRESSION){
                                                                                echo "deleteAction: 'scripts/update_video.php?action=delete',";
                                                                        }
                                                                        ?>		                                            		                                            		                                           		                                            		                                            
                                                                        },
                                                                        fields: {   
                                                                            id_materiel: {
                                                                                type: 'hidden',
                                                                                create: true,
                                                                                list: true,
                                                                                defaultValue: optData.record.id_materiel
                                                                            }  ,                                       
                                                                            id_video: {	                                            					                                            
                                                                                key: true,
                                                                                create: false,
                                                                                edit: false,
                                                                                list: false                                          
                                                                            },	
                                                                            id_marque: {
                                                                                title: 'Marque',
                                                                                width: '10%',
                                                                                options: 'main/liste/getMarque.php?id_type=<?php echo $_SESSION["CONFIG"]['video'];?>'
                                                                            },	 
                                                                            id_modele: {
                                                                                type: 'hidden',                                                                                   
                                                                            },
                                                                            net_modele_edit: {
                                                                                title: '',
                                                                                width: '1%',
                                                                                <?php 
                                                                                if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                                                (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                                        echo "list: true,";
                                                                                }else{echo "list: false,";}
                                                                                ?>
                                                                                edit: false,
                                                                                sorting: false,								
                                                                                create: false,													                    						
                                                                                display: function (netChildData) {				
                                                                                    //Create an image that will be used to open child table
                                                                                    var $img13 = new Array();
                                                                                    $img13 = $('<img src="graphs/icons/edit.png" title="Modifier le modèle" />')	                      
                                                                                    //Open child table when user clicks the image
                                                                                    $img13.click(function () {	                        	                 
                                                                                    $('#masterContainer').jtable('openChildTable',	    	                            
                                                                                        $img13.closest('td'),
                                                                                        {
                                                                                            title: 'Modèle(s) de la marque ' + netChildData.record.marque,
                                                                                            titleClass: 'jtable-title-child jtable-title',
                                                                                            pageSize: <?php echo $_SESSION['pageSize'];?>,		                    					
                                                                                            paging: true,	
                                                                                            sorting: true,	                    					
                                                                                            defaultSorting: 'modele2 ASC',				                                       
                                                                                            actions: {
                                                                                            <?php 
                                                                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & LECTURE && 
                                                                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & LECTURE){
                                                                                                    echo "listAction: 'scripts/update_modele.php?action=list&id_marque=' + netChildData.record.id_marque + '&id_materiel=' + netChildData.record.id_materiel + '&id_modele=' + netChildData.record.id_modele + '&id_type=$_SESSION[CONFIG][video]',";
                                                                                            }
                                                                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                                                    echo "updateAction: 'scripts/update_modele.php?action=update&id_marque=' + netChildData.record.id_marque + '&id_materiel=' + netChildData.record.id_materiel + '&id_modele=' + netChildData.record.id_modele + '&id_type=$_SESSION[CONFIG][video]',";
                                                                                            }
                                                                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & CREATION &&
                                                                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & CREATION){
                                                                                                    echo "createAction: 'scripts/update_modele.php?action=create&id_marque=' + netChildData.record.id_marque + '&id_materiel=' + netChildData.record.id_materiel + '&id_modele=' + netChildData.record.id_modele + '&id_type=$_SESSION[CONFIG][video]',";
                                                                                            }
                                                                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & SUPPRESSION &&
                                                                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & SUPPRESSION){
                                                                                                    echo "deleteAction: 'scripts/update_modele.php?action=delete',";
                                                                                            }
                                                                                            ?>		                                            		                                            		                                           		                                            		                                            
                                                                                            },
                                                                                            fields: {   		                    		                                        	                                                                                                                          
                                                                                                id_video: {	  
                                                                                                    key: true,                                          					                                            
                                                                                                    type: 'hidden',		                    			                                            
                                                                                                    defaultValue: netChildData.record.id_video	                                            
                                                                                                },		
                                                                                                id_modele2: {		                    		                                        		  	                                        		                                        	                      			
                                                                                                    create: false,
                                                                                                    edit: false,
                                                                                                    list: false,
                                                                                                },                    		                                            
                                                                                                modele_edit2: {
                                                                                                    title: '',
                                                                                                    width: '1%',
                                                                                                    edit: false,
                                                                                                    create: false,		                        								
                                                                                                    //options: 'main/liste/getModele.php',
                                                                                                    display: function (modeleData) {				
                                                                                                        //Create an image that will be used to open child table
                                                                                                        var $img14 = new Array();
                                                                                                        $img14 = $('<img src="graphs/icons/valid.png" title="Choisir ce modèle" />')	                      
                                                                                                        //Open child table when user clicks the image
                                                                                                        $img14.click(function () {	                        	                 
                                                                                                            $('#masterContainer').jtable('updateRecord', {	    	                            
                                                                                                                record: {		                        	                                   
                                                                                                                    id_materiel: netChildData.record.id_materiel,		                    			                        	                                    
                                                                                                                    id_materiel2: netChildData.record.id_video,
                                                                                                                    id_modele: modeleData.record.id_modele2,		                    			                        	                                   
                                                                                                                    source: 'video'
                                                                                                                },	                        	                                    
                                                                                                                url: 'scripts/update_modele.php?action=change',
                                                                                                                success: function(){
                                                                                                                    $('#masterContainer').jtable('closeChildTable',$img13.closest('td'));
                                                                                                                }		                        	                                                      	                                                     	                                     	                               
                                                                                                            });	                        	                             	                        	                           
                                                                                                        });	                       
                                                                                                        //Return image to show on the person row	  	                        	                                                    	                                      
                                                                                                        return $img14;	                        	                        
                                                                                                    }  
                                                                                                },                                         	                                          
                                                                                                modele2: {
                                                                                                    title: 'Modèle',
                                                                                                    width: '90%',
                                                                                                    sorting: true
                                                                                                },  	                                                                                     
                                                                                            }
                                                                                        }, function (data2) { //opened handler		                                   
                                                                                            data2.childTable.jtable('load');		                                                                          
                                                                                        });	 	                                               
                                                                                    });	                       
                                                                                    //Return image to show on the person row	  	                                                    	                                      
                                                                                    return $img13;	                        
                                                                                }
                                                                        },					
                                                                        modele:{
                                                                            title: 'Modèle',
                                                                            width: '10%',
                                                                            edit: false,
                                                                            create: false,
                                                                        },
                                                                        id_type_video:{
                                                                            type: 'hidden',
                                                                            value: '<?php echo $_SESSION[CONFIG][video];?>'
                                                                        }, 
                                                                        /*name:{
                                                                            title: 'Nom',                                                                            
                                                                        },*/  
                                                                        chipset:{
                                                                            title: 'Chipset',                                                                            
                                                                        },
                                                                        memory:{
                                                                            title: 'Mémoire (Mo)',                                                                            
                                                                        }, 
                                                                        resolution:{
                                                                            title: 'Résolution',
                                                                            list: false,                                                                           
                                                                        } ,                                                                        
                                                                        sn:{
                                                                            title: 'SN',
                                                                            list: false,
                                                                        },		                                           
                                                                        date_installe: {
                                                                            title: 'Installé le',
                                                                            width: '10%',
                                                                            type: 'date',
                                                                            displayFormat: 'dd-mm-yy',
                                                                            inputClass: 'validate[custom[datefr]]'                                                                          
                                                                        }
                                                                    }
                                                                }, function (data) { //opened handler		                                   
                                                                    data.childTable.jtable('load');		                                                                          
                                                                    //$('#childContainer').jtable('load');
                                                                });	 	                                               
                                                            });	                       
                                                            //Return image to show on the person row	  	                                                    	                                      
                                                            return $img12;	                        
                                                        }
                                                    },
                                                    //Monitor
                                                    add_monitor: {
                                                        title: 'Ecran',
                                                        width: '2%',
                                                        //listClass: 'jtableOption',
                                                        <?php 
                                                        if ($_SESSION['id_zone']!=6 && $_SESSION['id_zone']!=4 && (int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                echo "list: true,";
                                                        }else{echo "list: false,";}
                                                        ?>
                                                        edit: false,
                                                        create: false,	
                                                        sorting: false,						
                                                        display: function (optData) {				
                                                            //Create an image that will be used to open child table
                                                            var $img15 = new Array();
                                                            $img15 = $('<img src="graphs/icons/monitor.png" title="Ajouter ou modifier un écran" />')	                      
                                                            //Open child table when user clicks the image
                                                            $img15.click(function () {	                                                           
                                                                $('#masterContainer').jtable('openChildTable',	    	                            
                                                                    $img0.closest('tr'),
                                                                    {
                                                                        title: '<div style="display: inline;margin-right: 3px;"><img src="graphs/icons/monitor.png" width="16" height="16"></div>Ecran pour : '+optData.record.nom,	
                                                                        titleClass: 'jtable-title-child jtable-title',
                                                                        pageSize: <?php echo $_SESSION['pageSize'];?>,
                                                                        sorting: true,	
                                                                        paging: true,
                                                                        defaultSorting: 'nom ASC',					                                     
                                                                        actions: {
                                                                        <?php 
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & LECTURE && 
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & LECTURE){
                                                                                echo "listAction: 'scripts/update_monitor.php?action=list&id_materiel=' + optData.record.id_materiel,";
                                                                        }
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                                echo "updateAction: 'scripts/update_monitor.php?action=update&id_materiel=' + optData.record.id_materiel,";
                                                                        }
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & CREATION &&
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & CREATION){
                                                                                echo "createAction: 'scripts/update_monitor.php?action=create&id_materiel=' + optData.record.id_materiel,";
                                                                        }
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & SUPPRESSION &&
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & SUPPRESSION){
                                                                                echo "deleteAction: 'scripts/update_monitor.php?action=delete',";
                                                                        }
                                                                        ?>		                                            		                                            		                                           		                                            		                                            
                                                                        },
                                                                        fields: {   
                                                                            id_materiel: {
                                                                                type: 'hidden',
                                                                                create: true,
                                                                                list: true,
                                                                                defaultValue: optData.record.id_materiel
                                                                            }  ,                                       
                                                                            id_monitor: {	                                            					                                            
                                                                                key: true,
                                                                                create: false,
                                                                                edit: false,
                                                                                list: false                                          
                                                                            },	
                                                                            id_marque: {
                                                                                title: 'Marque',
                                                                                width: '10%',
                                                                                options: 'main/liste/getMarque.php?id_type=<?php echo $_SESSION["CONFIG"]['monitor'];?>'
                                                                            },	 
                                                                            id_modele: {
                                                                                type: 'hidden',                                                                                   
                                                                            },
                                                                            monitor_modele_edit: {
                                                                                title: '',
                                                                                width: '1%',
                                                                                <?php 
                                                                                if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                                                (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                                        echo "list: true,";
                                                                                }else{echo "list: false,";}
                                                                                ?>
                                                                                edit: false,
                                                                                sorting: false,								
                                                                                create: false,													                    						
                                                                                display: function (netChildData) {				
                                                                                    //Create an image that will be used to open child table
                                                                                    var $img16 = new Array();
                                                                                    $img16 = $('<img src="graphs/icons/edit.png" title="Modifier le modèle" />')	                      
                                                                                    //Open child table when user clicks the image
                                                                                    $img16.click(function () {	                        	                 
                                                                                    $('#masterContainer').jtable('openChildTable',	    	                            
                                                                                        $img16.closest('td'),
                                                                                        {
                                                                                            title: 'Modèle(s) de la marque ' + netChildData.record.marque,
                                                                                            titleClass: 'jtable-title-child jtable-title',
                                                                                            pageSize: <?php echo $_SESSION['pageSize'];?>,		                    					
                                                                                            paging: true,	
                                                                                            sorting: true,	                    					
                                                                                            defaultSorting: 'modele2 ASC',				                                       
                                                                                            actions: {
                                                                                            <?php 
                                                                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & LECTURE && 
                                                                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & LECTURE){
                                                                                                    echo "listAction: 'scripts/update_modele.php?action=list&id_marque=' + netChildData.record.id_marque + '&id_materiel=' + netChildData.record.id_materiel + '&id_modele=' + netChildData.record.id_modele + '&id_type=$_SESSION[CONFIG][monitor]',";
                                                                                            }
                                                                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                                                    echo "updateAction: 'scripts/update_modele.php?action=update&id_marque=' + netChildData.record.id_marque + '&id_materiel=' + netChildData.record.id_materiel + '&id_modele=' + netChildData.record.id_modele + '&id_type=$_SESSION[CONFIG][monitor]',";
                                                                                            }
                                                                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & CREATION &&
                                                                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & CREATION){
                                                                                                    echo "createAction: 'scripts/update_modele.php?action=create&id_marque=' + netChildData.record.id_marque + '&id_materiel=' + netChildData.record.id_materiel + '&id_modele=' + netChildData.record.id_modele + '&id_type=$_SESSION[CONFIG][monitor]',";
                                                                                            }
                                                                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & SUPPRESSION &&
                                                                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & SUPPRESSION){
                                                                                                    echo "deleteAction: 'scripts/update_modele.php?action=delete',";
                                                                                            }
                                                                                            ?>		                                            		                                            		                                           		                                            		                                            
                                                                                            },
                                                                                            fields: {   		                    		                                        	                                                                                                                          
                                                                                                id_monitor: {	  
                                                                                                    key: true,                                          					                                            
                                                                                                    type: 'hidden',		                    			                                            
                                                                                                    defaultValue: netChildData.record.id_monitor	                                            
                                                                                                },		
                                                                                                id_modele2: {		                    		                                        		  	                                        		                                        	                      			
                                                                                                    create: false,
                                                                                                    edit: false,
                                                                                                     list: false,
                                                                                                },                    		                                            
                                                                                                modele_edit2: {
                                                                                                    title: '',
                                                                                                    width: '1%',
                                                                                                    edit: false,
                                                                                                    create: false,		                        								
                                                                                                    //options: 'main/liste/getModele.php',
                                                                                                    display: function (modeleData) {				
                                                                                                        //Create an image that will be used to open child table
                                                                                                        var $img17 = new Array();
                                                                                                        $img17 = $('<img src="graphs/icons/valid.png" title="Choisir ce modèle" />')	                      
                                                                                                        //Open child table when user clicks the image
                                                                                                        $img17.click(function () {	                        	                 
                                                                                                            $('#masterContainer').jtable('updateRecord', {	    	                            
                                                                                                                record: {		                        	                                   
                                                                                                                    id_materiel: netChildData.record.id_materiel,		                    			                        	                                    
                                                                                                                    id_materiel2: netChildData.record.id_monitor,
                                                                                                                    id_modele: modeleData.record.id_modele2,		                    			                        	                                   
                                                                                                                    source: 'monitor'
                                                                                                                },	                        	                                    
                                                                                                                url: 'scripts/update_modele.php?action=change',
                                                                                                                success: function(){
                                                                                                                    $('#masterContainer').jtable('closeChildTable',$img16.closest('td'));
                                                                                                                }		                        	                                                      	                                                     	                                     	                               
                                                                                                            });	                        	                             	                        	                           
                                                                                                        });	                       
                                                                                                        //Return image to show on the person row	  	                        	                                                    	                                      
                                                                                                        return $img17;	                        	                        
                                                                                                    }  
                                                                                                },                                         	                                          
                                                                                                modele2: {
                                                                                                    title: 'Modèle',
                                                                                                    width: '90%',
                                                                                                    sorting: true
                                                                                                },  	                                                                                     
                                                                                            }
                                                                                        }, function (data2) { //opened handler		                                   
                                                                                            data2.childTable.jtable('load');		                                                                          
                                                                                        });	 	                                               
                                                                                    });	                       
                                                                                    //Return image to show on the person row	  	                                                    	                                      
                                                                                    return $img16;	                        
                                                                                }
                                                                        },					
                                                                        modele:{
                                                                            title: 'Modèle',
                                                                            width: '10%',
                                                                            edit: false,
                                                                            create: false,
                                                                        },  
                                                                        id_type_monitor:{
                                                                            type: 'hidden',
                                                                            value: '<?php echo $_SESSION[CONFIG][monitor];?>'
                                                                        }, 
                                                                        sn:{
                                                                            title: 'SN',
                                                                            list: false,
                                                                        },		                                           
                                                                        date_installe: {
                                                                            title: 'Installé le',
                                                                            width: '10%',
                                                                            type: 'date',
                                                                            displayFormat: 'dd-mm-yy',
                                                                            inputClass: 'validate[custom[datefr]]'                                                                          
                                                                        }
                                                                    }
                                                                }, function (data) { //opened handler		                                   
                                                                    data.childTable.jtable('load');		                                                                          
                                                                    //$('#childContainer').jtable('load');
                                                                });	 	                                               
                                                            });	                       
                                                            //Return image to show on the person row	  	                                                    	                                      
                                                            return $img15;	                        
                                                        }
                                                    },
                                                    //Sound
                                                    add_sound: {
                                                        title: 'Son',
                                                        width: '2%',
                                                        //listClass: 'jtableOption',
                                                        <?php 
                                                        if ($_SESSION['id_zone']!=6 && $_SESSION['id_zone']!=4 && (int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                echo "list: true,";
                                                        }else{echo "list: false,";}
                                                        ?>
                                                        edit: false,
                                                        create: false,	
                                                        sorting: false,						
                                                        display: function (optData) {				
                                                            //Create an image that will be used to open child table
                                                            var $img21 = new Array();
                                                            $img21 = $('<img src="graphs/icons/sound.png" title="Ajouter ou modifier une unité de son" />')	                      
                                                            //Open child table when user clicks the image
                                                            $img21.click(function () {	                                                           
                                                                $('#masterContainer').jtable('openChildTable',	    	                            
                                                                    $img0.closest('tr'),
                                                                    {
                                                                        title: '<div style="display: inline;margin-right: 3px;"><img src="graphs/icons/sound.png" width="16" height="16"></div>Unité mémoire pour : '+optData.record.nom,
                                                                        titleClass: 'jtable-title-child jtable-title',
                                                                        pageSize: <?php echo $_SESSION['pageSize'];?>,
                                                                        sorting: true,	
                                                                        paging: true,
                                                                        defaultSorting: 'marque ASC',					                                     
                                                                        actions: {
                                                                        <?php 
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & LECTURE && 
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & LECTURE){
                                                                                echo "listAction: 'scripts/update_sound.php?action=list&id_materiel=' + optData.record.id_materiel,";
                                                                        }
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                                echo "updateAction: 'scripts/update_sound.php?action=update&id_materiel=' + optData.record.id_materiel,";
                                                                        }
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & CREATION &&
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & CREATION){
                                                                                echo "createAction: 'scripts/update_sound.php?action=create&id_materiel=' + optData.record.id_materiel,";
                                                                        }
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & SUPPRESSION &&
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & SUPPRESSION){
                                                                                echo "deleteAction: 'scripts/update_sound.php?action=delete',";
                                                                        }
                                                                        ?>		                                            		                                            		                                           		                                            		                                            
                                                                        },
                                                                        fields: {   
                                                                            id_materiel: {
                                                                                type: 'hidden',
                                                                                create: true,
                                                                                list: true,
                                                                                defaultValue: optData.record.id_materiel
                                                                            }  ,                                       
                                                                            id_sound: {	                                            					                                            
                                                                                key: true,
                                                                                create: false,
                                                                                edit: false,
                                                                                list: false                                          
                                                                            },	
                                                                            id_marque: {
                                                                                title: 'Marque',
                                                                                width: '10%',
                                                                                options: 'main/liste/getMarque.php?id_type=<?php echo $_SESSION["CONFIG"]['sound'];?>'
                                                                            },	 
                                                                            id_modele: {
                                                                                type: 'hidden',                                                                                   
                                                                            },
                                                                            memory_modele_edit: {
                                                                                title: '',
                                                                                width: '1%',
                                                                                <?php 
                                                                                if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                                                (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                                        echo "list: true,";
                                                                                }else{echo "list: false,";}
                                                                                ?>
                                                                                edit: false,
                                                                                sorting: false,								
                                                                                create: false,													                    						
                                                                                display: function (netChildData) {				
                                                                                    //Create an image that will be used to open child table
                                                                                    var $img25 = new Array();
                                                                                    $img25 = $('<img src="graphs/icons/edit.png" title="Modifier le modèle" />')	                      
                                                                                    //Open child table when user clicks the image
                                                                                    $img25.click(function () {	                        	                 
                                                                                    $('#masterContainer').jtable('openChildTable',	    	                            
                                                                                        $img25.closest('td'),
                                                                                        {
                                                                                            title: 'Modèle(s) de la marque ' + netChildData.record.marque,
                                                                                            titleClass: 'jtable-title-child jtable-title',
                                                                                            pageSize: <?php echo $_SESSION['pageSize'];?>,		                    					
                                                                                            paging: true,	
                                                                                            sorting: true,	                    					
                                                                                            defaultSorting: 'modele2 ASC',				                                       
                                                                                            actions: {
                                                                                            <?php 
                                                                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & LECTURE && 
                                                                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & LECTURE){
                                                                                                    echo "listAction: 'scripts/update_modele.php?action=list&id_marque=' + netChildData.record.id_marque + '&id_materiel=' + netChildData.record.id_materiel + '&id_modele=' + netChildData.record.id_modele + '&id_type=$_SESSION[CONFIG][sound]',";
                                                                                            }
                                                                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                                                    echo "updateAction: 'scripts/update_modele.php?action=update&id_marque=' + netChildData.record.id_marque + '&id_materiel=' + netChildData.record.id_materiel + '&id_modele=' + netChildData.record.id_modele + '&id_type=$_SESSION[CONFIG][sound]',";
                                                                                            }
                                                                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & CREATION &&
                                                                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & CREATION){
                                                                                                    echo "createAction: 'scripts/update_modele.php?action=create&id_marque=' + netChildData.record.id_marque + '&id_materiel=' + netChildData.record.id_materiel + '&id_modele=' + netChildData.record.id_modele + '&id_type=$_SESSION[CONFIG][sound]',";
                                                                                            }
                                                                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & SUPPRESSION &&
                                                                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & SUPPRESSION){
                                                                                                    echo "deleteAction: 'scripts/update_modele.php?action=delete',";
                                                                                            }
                                                                                            ?>		                                            		                                            		                                           		                                            		                                            
                                                                                            },
                                                                                            fields: {   		                    		                                        	                                                                                                                          
                                                                                                id_sound: {	  
                                                                                                    key: true,                                          					                                            
                                                                                                    type: 'hidden',		                    			                                            
                                                                                                    defaultValue: netChildData.record.id_sound	                                            
                                                                                                },		
                                                                                                id_modele2: {		                    		                                        		  	                                        		                                        	                      			
                                                                                                    create: false,
                                                                                                    edit: false,
                                                                                                    list: false,
                                                                                                },                    		                                            
                                                                                                modele_edit2: {
                                                                                                    title: '',
                                                                                                    width: '1%',
                                                                                                    edit: false,
                                                                                                    create: false,		                        								
                                                                                                    //options: 'main/liste/getModele.php',
                                                                                                    display: function (modeleData) {				
                                                                                                        //Create an image that will be used to open child table
                                                                                                        var $img26 = new Array();
                                                                                                        $img26 = $('<img src="graphs/icons/valid.png" title="Choisir ce modèle" />')	                      
                                                                                                        //Open child table when user clicks the image
                                                                                                        $img26.click(function () {	                        	                 
                                                                                                            $('#masterContainer').jtable('updateRecord', {	    	                            
                                                                                                                record: {		                        	                                   
                                                                                                                    id_materiel: netChildData.record.id_materiel,		                    			                        	                                    
                                                                                                                    id_materiel2: netChildData.record.id_sound,
                                                                                                                    id_modele: modeleData.record.id_modele2,		                    			                        	                                   
                                                                                                                    source: 'video'
                                                                                                                },	                        	                                    
                                                                                                                url: 'scripts/update_modele.php?action=change',
                                                                                                                success: function(){
                                                                                                                    $('#masterContainer').jtable('closeChildTable',$img25.closest('td'));
                                                                                                                }		                        	                                                      	                                                     	                                     	                               
                                                                                                            });	                        	                             	                        	                           
                                                                                                        });	                       
                                                                                                        //Return image to show on the person row	  	                        	                                                    	                                      
                                                                                                        return $img26;	                        	                        
                                                                                                    }  
                                                                                                },                                         	                                          
                                                                                                modele2: {
                                                                                                    title: 'Modèle',
                                                                                                    width: '90%',
                                                                                                    sorting: true
                                                                                                },  	                                                                                     
                                                                                            }
                                                                                        }, function (data2) { //opened handler		                                   
                                                                                            data2.childTable.jtable('load');		                                                                          
                                                                                        });	 	                                               
                                                                                    });	                       
                                                                                    //Return image to show on the person row	  	                                                    	                                      
                                                                                    return $img25;	                        
                                                                                }
                                                                        },					
                                                                        modele:{
                                                                            title: 'Modèle',
                                                                            width: '10%',
                                                                            edit: false,
                                                                            create: false,
                                                                        },
                                                                        id_type_sound:{
                                                                            type: 'hidden',
                                                                            value: '<?php echo $_SESSION[CONFIG][sound];?>'
                                                                        },                                                                        
                                                                        sn: {
                                                                          title: 'S/N',
                                                                        },
                                                                        date_installe: {
                                                                            title: 'Installé le',
                                                                            width: '10%',
                                                                            type: 'date',
                                                                            displayFormat: 'dd-mm-yy',
                                                                            inputClass: 'validate[custom[datefr]]'                                                                          
                                                                        }
                                                                    }
                                                                }, function (data) { //opened handler		                                   
                                                                    data.childTable.jtable('load');		                                                                          
                                                                    //$('#childContainer').jtable('load');
                                                                });	 	                                               
                                                            });	                       
                                                            //Return image to show on the person row	  	                                                    	                                      
                                                            return $img21;	                        
                                                        }
                                                    },
                                                    //Memory
                                                    add_memory: {
                                                        title: 'Mémoire',
                                                        width: '2%',
                                                        //listClass: 'jtableOption',
                                                        <?php 
                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                echo "list: true,";
                                                        }else{echo "list: false,";}
                                                        ?>
                                                        edit: false,
                                                        create: false,	
                                                        sorting: false,						
                                                        display: function (optData) {				
                                                            //Create an image that will be used to open child table
                                                            var $img24 = new Array();
                                                            $img24 = $('<img src="graphs/icons/memory.png" title="Ajouter ou modifier une unité de mémoire" />')	                      
                                                            //Open child table when user clicks the image
                                                            $img24.click(function () {	                                                           
                                                                $('#masterContainer').jtable('openChildTable',	    	                            
                                                                    $img0.closest('tr'),
                                                                    {
                                                                        title: '<div style="display: inline;margin-right: 3px;"><img src="graphs/icons/memory.png" width="16" height="16"></div>Unité mémoire pour : '+optData.record.nom,
                                                                        titleClass: 'jtable-title-child jtable-title',
                                                                        pageSize: <?php echo $_SESSION['pageSize'];?>,
                                                                        sorting: true,	
                                                                        paging: true,
                                                                        defaultSorting: 'detail ASC',					                                     
                                                                        actions: {
                                                                        <?php 
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & LECTURE && 
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & LECTURE){
                                                                                echo "listAction: 'scripts/update_memory.php?action=list&id_materiel=' + optData.record.id_materiel,";
                                                                        }
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                                echo "updateAction: 'scripts/update_memory.php?action=update&id_materiel=' + optData.record.id_materiel,";
                                                                        }
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & CREATION &&
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & CREATION){
                                                                                echo "createAction: 'scripts/update_memory.php?action=create&id_materiel=' + optData.record.id_materiel,";
                                                                        }
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & SUPPRESSION &&
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & SUPPRESSION){
                                                                                echo "deleteAction: 'scripts/update_memory.php?action=delete',";
                                                                        }
                                                                        ?>		                                            		                                            		                                           		                                            		                                            
                                                                        },
                                                                        fields: {   
                                                                            id_materiel: {
                                                                                type: 'hidden',
                                                                                create: true,
                                                                                list: true,
                                                                                defaultValue: optData.record.id_materiel
                                                                            }  ,                                       
                                                                            id_memory: {	                                            					                                            
                                                                                key: true,
                                                                                create: false,
                                                                                edit: false,
                                                                                list: false                                          
                                                                            },	
                                                                            id_marque: {
                                                                                title: 'Marque',
                                                                                width: '10%',
                                                                                options: 'main/liste/getMarque.php?id_type=<?php echo $_SESSION["CONFIG"]['memory'];?>'
                                                                            },	 
                                                                            id_modele: {
                                                                                type: 'hidden',                                                                                   
                                                                            },
                                                                            memory_modele_edit: {
                                                                                title: '',
                                                                                width: '1%',
                                                                                <?php 
                                                                                if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                                                (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                                        echo "list: true,";
                                                                                }else{echo "list: false,";}
                                                                                ?>
                                                                                edit: false,
                                                                                sorting: false,								
                                                                                create: false,													                    						
                                                                                display: function (netChildData) {				
                                                                                    //Create an image that will be used to open child table
                                                                                    var $img25 = new Array();
                                                                                    $img25 = $('<img src="graphs/icons/edit.png" title="Modifier le modèle" />')	                      
                                                                                    //Open child table when user clicks the image
                                                                                    $img25.click(function () {	                        	                 
                                                                                    $('#masterContainer').jtable('openChildTable',	    	                            
                                                                                        $img25.closest('td'),
                                                                                        {
                                                                                            title: 'Modèle(s) de la marque ' + netChildData.record.marque,
                                                                                            titleClass: 'jtable-title-child jtable-title',
                                                                                            pageSize: <?php echo $_SESSION['pageSize'];?>,		                    					
                                                                                            paging: true,	
                                                                                            sorting: true,	                    					
                                                                                            defaultSorting: 'modele2 ASC',				                                       
                                                                                            actions: {
                                                                                            <?php 
                                                                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & LECTURE && 
                                                                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & LECTURE){
                                                                                                    echo "listAction: 'scripts/update_modele.php?action=list&id_marque=' + netChildData.record.id_marque + '&id_materiel=' + netChildData.record.id_materiel + '&id_modele=' + netChildData.record.id_modele + '&id_type=$_SESSION[CONFIG][memory]',";
                                                                                            }
                                                                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                                                    echo "updateAction: 'scripts/update_modele.php?action=update&id_marque=' + netChildData.record.id_marque + '&id_materiel=' + netChildData.record.id_materiel + '&id_modele=' + netChildData.record.id_modele + '&id_type=$_SESSION[CONFIG][memory]',";
                                                                                            }
                                                                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & CREATION &&
                                                                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & CREATION){
                                                                                                    echo "createAction: 'scripts/update_modele.php?action=create&id_marque=' + netChildData.record.id_marque + '&id_materiel=' + netChildData.record.id_materiel + '&id_modele=' + netChildData.record.id_modele + '&id_type=$_SESSION[CONFIG][memory]',";
                                                                                            }
                                                                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & SUPPRESSION &&
                                                                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & SUPPRESSION){
                                                                                                    echo "deleteAction: 'scripts/update_modele.php?action=delete',";
                                                                                            }
                                                                                            ?>		                                            		                                            		                                           		                                            		                                            
                                                                                            },
                                                                                            fields: {   		                    		                                        	                                                                                                                          
                                                                                                id_memory: {	  
                                                                                                    key: true,                                          					                                            
                                                                                                    type: 'hidden',		                    			                                            
                                                                                                    defaultValue: netChildData.record.id_memory	                                            
                                                                                                },		
                                                                                                id_modele2: {		                    		                                        		  	                                        		                                        	                      			
                                                                                                    create: false,
                                                                                                    edit: false,
                                                                                                    list: false,
                                                                                                },                    		                                            
                                                                                                modele_edit2: {
                                                                                                    title: '',
                                                                                                    width: '1%',
                                                                                                    edit: false,
                                                                                                    create: false,		                        								
                                                                                                    //options: 'main/liste/getModele.php',
                                                                                                    display: function (modeleData) {				
                                                                                                        //Create an image that will be used to open child table
                                                                                                        var $img26 = new Array();
                                                                                                        $img26 = $('<img src="graphs/icons/valid.png" title="Choisir ce modèle" />')	                      
                                                                                                        //Open child table when user clicks the image
                                                                                                        $img26.click(function () {	                        	                 
                                                                                                            $('#masterContainer').jtable('updateRecord', {	    	                            
                                                                                                                record: {		                        	                                   
                                                                                                                    id_materiel: netChildData.record.id_materiel,		                    			                        	                                    
                                                                                                                    id_materiel2: netChildData.record.id_memory,
                                                                                                                    id_modele: modeleData.record.id_modele2,		                    			                        	                                   
                                                                                                                    source: 'video'
                                                                                                                },	                        	                                    
                                                                                                                url: 'scripts/update_modele.php?action=change',
                                                                                                                success: function(){
                                                                                                                    $('#masterContainer').jtable('closeChildTable',$img25.closest('td'));
                                                                                                                }		                        	                                                      	                                                     	                                     	                               
                                                                                                            });	                        	                             	                        	                           
                                                                                                        });	                       
                                                                                                        //Return image to show on the person row	  	                        	                                                    	                                      
                                                                                                        return $img26;	                        	                        
                                                                                                    }  
                                                                                                },                                         	                                          
                                                                                                modele2: {
                                                                                                    title: 'Modèle',
                                                                                                    width: '90%',
                                                                                                    sorting: true
                                                                                                },  	                                                                                     
                                                                                            }
                                                                                        }, function (data2) { //opened handler		                                   
                                                                                            data2.childTable.jtable('load');		                                                                          
                                                                                        });	 	                                               
                                                                                    });	                       
                                                                                    //Return image to show on the person row	  	                                                    	                                      
                                                                                    return $img25;	                        
                                                                                }
                                                                        },					
                                                                        modele:{
                                                                            title: 'Modèle',
                                                                            width: '10%',
                                                                            edit: false,
                                                                            create: false,
                                                                        },                                                                        
                                                                        alias:{
                                                                            title: 'Alias',                                                                            
                                                                        },  
                                                                        type: {
                                                                            title: 'Type'
                                                                        },
                                                                        detail_memory:{
                                                                            title: 'Détail',                                                                            
                                                                        },
                                                                        destination:{
                                                                            title: 'Destination',                                                                            
                                                                        }, 
                                                                        capacite:{
                                                                            title: 'Capacité (Mo)',                                                                                                                                                    
                                                                        } ,                                                                        
                                                                        speed:{
                                                                            title: 'Vitesse',
                                                                        },
                                                                        slot:{
                                                                            title: 'Slot',
                                                                        },
                                                                        sn: {
                                                                          title: 'S/N',
                                                                        },
                                                                        date_installe: {
                                                                            title: 'Installé le',
                                                                            width: '10%',
                                                                            type: 'date',
                                                                            displayFormat: 'dd-mm-yy',
                                                                            inputClass: 'validate[custom[datefr]]'                                                                          
                                                                        }
                                                                    }
                                                                }, function (data) { //opened handler		                                   
                                                                    data.childTable.jtable('load');		                                                                          
                                                                    //$('#childContainer').jtable('load');
                                                                });	 	                                               
                                                            });	                       
                                                            //Return image to show on the person row	  	                                                    	                                      
                                                            return $img24;	                        
                                                        }
                                                    },
                                                    //Commande
                                                    add_cmd: {
                                                        title: 'Commande',
                                                       width: '2%',
                                                        //listClass: 'jtableOption',
                                                        <?php 
                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                echo "list: true,";
                                                        }else{echo "list: false,";}
                                                        ?>
                                                        edit: false,
                                                        create: false,	
                                                        sorting: false,						
                                                        display: function (cmdChildData) {	                                                            
                                                            //Create an image that will be used to open child table
                                                            //alert(cmdChildData.record.id_commande);
                                                            var $img18 = new Array();
                                                            $img18 = $('<img src="graphs/icons/commande.png" title="Ajouter ou modifier une commande" />')	                      
                                                            //Open child table when user clicks the image
                                                            $img18.click(function () {	                                                           
                                                                $('#masterContainer').jtable('openChildTable',	    	                            
                                                                    $img0.closest('tr'),
                                                                    {
                                                                        title: '<div style="display: inline;margin-right: 3px;"><img src="graphs/icons/commande.png" width="16" height="16"></div>Commande pour : '+cmdChildData.record.nom,
                                                                        titleClass: 'jtable-title-child jtable-title',
                                                                        pageSize: <?php echo $_SESSION['pageSize'];?>,
                                                                        sorting: true,	
                                                                        paging: true,
                                                                        defaultSorting: 'ip ASC',					                                     
                                                                        actions: {
                                                                        <?php 
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & LECTURE && 
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & LECTURE){
                                                                                echo "listAction: 'scripts/update_cmd.php?action=list&id_commande=' + cmdChildData.record.id_commande,";
                                                                        }
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                                echo "updateAction: 'scripts/update_cmd.php?action=update&id_materiel=' + cmdChildData.record.id_materiel,";
                                                                        }
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & CREATION &&
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & CREATION){
                                                                                echo "createAction: 'scripts/update_cmd.php?action=create&id_materiel=' + cmdChildData.record.id_materiel,";
                                                                        }
                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & SUPPRESSION &&
                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & SUPPRESSION){
                                                                                echo "deleteAction: 'scripts/update_cmd.php?action=delete',";
                                                                        }
                                                                        ?>		                                            		                                            		                                           		                                            		                                            
                                                                        },
                                                                        fields: {   
                                                                            id_materiel: {
                                                                                type: 'hidden',
                                                                                create: true,
                                                                                list: true,
                                                                                defaultValue: cmdChildData.record.id_materiel
                                                                            } ,                                       
                                                                            id_commande: {
                                                                                key: true,
                                                                                create: false,
                                                                                edit: false,
                                                                                list: false,                                                                                  
                                                                            },                                                                             
                                                                            no_commande: {
                                                                                title: 'N°',                                                                                                                                                               
                                                                            },
                                                                            id_fournisseur: {
                                                                                title: 'Fournisseur',
                                                                                width: '10%',
                                                                                options: 'main/liste/getFournisseur.php'
                                                                            },	                                                                             		                                           
                                                                            date_achat: {
                                                                                title: 'Acheté le',
                                                                                width: '10%',
                                                                                type: 'date',
                                                                                displayFormat: 'dd-mm-yy',
                                                                                inputClass: 'validate[custom[datefr]]'                                                                          
                                                                            },
                                                                            date_commande: {
                                                                                title: 'Commandé le',
                                                                                width: '10%',
                                                                                type: 'date',
                                                                                displayFormat: 'dd-mm-yy',
                                                                                inputClass: 'validate[custom[datefr]]'                                                                          
                                                                            },
                                                                            date_expedition: {
                                                                                title: 'Expédié le',
                                                                                width: '10%',
                                                                                type: 'date',
                                                                                displayFormat: 'dd-mm-yy',
                                                                                inputClass: 'validate[custom[datefr]]'                                                                          
                                                                            },
                                                                            date_reception: {
                                                                                title: 'Receptionné le',
                                                                                width: '10%',
                                                                                type: 'date',
                                                                                displayFormat: 'dd-mm-yy',
                                                                                inputClass: 'validate[custom[datefr]]'                                                                          
                                                                            },
                                                                            date_garantie: {
                                                                                title: 'Fin de garantie',
                                                                                width: '10%',
                                                                                type: 'date',
                                                                                displayFormat: 'dd-mm-yy',
                                                                                inputClass: 'validate[custom[datefr]]'                                                                          
                                                                            },
                                                                            financement: {
                                                                                title: 'Financement',
                                                                                width: '10%',                                                                                                                                                    
                                                                            },
                                                                            montant: {
                                                                                title: 'Montant',
                                                                                width: '10%',                                                                                
                                                                                inputClass: 'validate[custom[number]]'                                                                          
                                                                            },
                                                                    }
                                                                }, function (data) { //opened handler		                                   
                                                                    data.childTable.jtable('load');		                                                                          
                                                                    //$('#childContainer').jtable('load');
                                                                });	 	                                               
                                                            });	                       
                                                            //Return image to show on the person row	  	                                                    	                                      
                                                            return $img18;	                        
                                                        }
                                                    },
                                                }
                                            }, 
                                        function (data) { //opened handler		                                   
                                            data.childTable.jtable('load');		                                                                                                                      
                                        });                                        
                                    });
                                    return $img0;
                                }
                            },                            							
                            id_type2: {							
                                    title: 'Type',
                                    width: '1%',							
                                    options: 'main/liste/getType.php?source=materiel',
                                    inputClass: 'validate[required]',
                                    display: function (studentData) {	
                                        if (studentData.record.type_materiel){
                                            //Create an image that will be used to open child table
                                            var $img99 = new Array();                                        
                                            var icone=studentData.record.type_materiel;
                                            icone = AccentToNoAccent(icone);			                        
                                            var exist = test_fichier(icone.toLowerCase(),'materiel');    
                                            //alert(exist);
                                            if (exist != 0){
                                                $img99 = $('<img src="graphs/icons/materiel/' + exist + '" title="' + studentData.record.type_materiel + '" />');
                                                return $img99;	
                                            }else{
                                                return studentData.record.type_materiel;
                                            }	
                                        }                                            
                                    }
                            },
                            nom: {
                                title: 'Nom',
                                width: '10%',                                 
                                inputClass: 'validate[required]',                                
                            },						
                            id_marque: {
                                title: 'Marque',
                                width: '10%',   
                                options: 'main/liste/getMarque.php'                               
                            },
                            id_modele: {
                                type: 'hidden',                                                                                   
	                    },
                            modele_edit: {
                                    title: '',
                                    width: '1%',
                                    <?php 
                                    if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                    (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                            echo "list: true,";
                                    }else{echo "list: false,";}
                                    ?>
                                    edit: false,							
                                    create: false,	
                                    sorting: false,										
                                    //options: 'main/liste/getModele.php',
                                    display: function (studentData) {				
		                        //Create an image that will be used to open child table
		                        var $img100 = new Array();
		                        $img100 = $('<img src="graphs/icons/edit.png" title="Modifier le modèle" />')	                      
		                        //Open child table when user clicks the image
		                        $img100.click(function () {	                        	                 
		                            $('#masterContainer').jtable('openChildTable',	    	                            
		                                    $img100.closest('tr'),
		                                    {
		                                        title: 'Modèle(s) de la marque ' + studentData.record.marque,
		                                        pageSize: <?php echo $_SESSION['pageSize'];?>,		                    					
                                                        paging: true,	
                                                        sorting: true,	                    					
                                                        defaultSorting: 'modele2 ASC',				                                       
		                                        actions: {
                                                            <?php 
                                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & LECTURE && 
                                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & LECTURE){
                                                                    echo "listAction: 'scripts/update_modele.php?action=list&id_marque=' + studentData.record.id_marque + '&id_materiel=' + studentData.record.id_materiel + '&id_modele=' + studentData.record.id_modele + '&id_type=$_SESSION[CONFIG][drive]',";
                                                            }
                                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                    echo "updateAction: 'scripts/update_modele.php?action=update&id_marque=' + studentData.record.id_marque + '&id_materiel=' + studentData.record.id_materiel + '&id_modele=' + studentData.record.id_modele + '&id_type=$_SESSION[CONFIG][drive]',";
                                                            }
                                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & CREATION &&
                                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & CREATION){
                                                                    echo "createAction: 'scripts/update_modele.php?action=create&id_marque=' + studentData.record.id_marque + '&id_materiel=' + studentData.record.id_materiel + '&id_modele=' + studentData.record.id_modele + '&id_type=$_SESSION[CONFIG][drive]',";
                                                            }
                                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & SUPPRESSION &&
                                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & SUPPRESSION){
                                                                    echo "deleteAction: 'scripts/update_modele.php?action=delete',";
                                                            }
                                                            ?>		                                            		                                            		                                           		                                            		                                            
		                                        },
		                                        fields: {   
                                                            id_modele2: {
                                                                key: true,  	                                        		                                        	                      				
                                                                create: false,
                                                                edit: false,
                                                                list: false
                                                            }  ,                                       
		                                            id_materiel: {	                                            					                                            
                                                                type: 'hidden',
                                                                edit: false,
                                                                create: false,
                                                                defaultValue: studentData.record.id	                                            
		                                            },	 
		                                            modele_edit2: {
                                                                title: '',
                                                                width: '1%',
                                                                edit: false,
                                                                create: false,		                        								
                                                                //options: 'main/liste/getModele.php',
                                                                display: function (modeleData) {				
                                                                    //Create an image that will be used to open child table
                                                                    var $img101 = new Array();
                                                                    $img101 = $('<img src="graphs/icons/valid.png" title="Choisir ce modèle" />')	                      
                                                                    //Open child table when user clicks the image
                                                                    $img101.click(function () {	                        	                 
                                                                        $('#masterContainer').jtable('updateRecord', {	    	                            
                                                                            record: {		                        	                                   
                                                                                id_materiel: studentData.record.id_materiel,
                                                                                id_materiel2: studentData.record.id_materiel,
                                                                                id_modele: modeleData.record.id_modele2,
                                                                                modele: modeleData.record.modele2,
                                                                                source: 'materiel'
                                                                            },	                        	                                    
                                                                            url: 'scripts/update_modele.php?action=change',
                                                                            success: function(){
                                                                                $('#masterContainer').jtable('closeChildTable',$img100.closest('tr'));
                                                                            }		                        	                                                      	                                                     	                                     	                               
                                                                        }); 	                        	                             	                        	                           
                                                                    });	                       
                                                                    //Return image to show on the person row	  	                        	                                                    	                                      
                                                                    return $img101;	                        	                        
		                                            	}  
		                                            },                                         	                                          
		                                            modele2: {
		                                                title: 'Modèle',
		                                                width: '90%',
		                                                sorting: true
		                                            },  	                                                                                     
		                                        }
		                                    }, function (data) { //opened handler		                                   
		                                        data.childTable.jtable('load');		                                                                          
		                                    });	 	                                               
		                        });	                       
		                        //Return image to show on the person row	  	                                                    	                                      
		                        return $img100;	                        
	                    	}
                            },					
                            modele:{
                                    title: 'Modèle',
                                    width: '10%',
                                    edit: false,
                                    create: false,
                            },					
                            systeme: {
                                title: 'SE',													
                                width: '1%',	
                                <?php 
                                if ($_SESSION['id_zone']!=6 && $_SESSION['id_zone']!=4){
                                        echo "list: true,";
                                }else{echo "list: false,";}
                                ?>
                                display: function (studentData) {	
                                    if (studentData.record.systeme){
                                        //Create an image that will be used to open child table
                                        var $img102 = new Array();		                       
                                        var icone=studentData.record.systeme;
                                        icone = AccentToNoAccent(icone);			                        
                                        //var exist = test_fichier('/graphs/icons/se/' + icone.toLowerCase());
                                        var exist = test_fichier(icone.toLowerCase(),'se');
                                        //alert(exist);
                                        if (exist != 0){		                        
                                            //$img7 = $('<img src="graphs/icons/se/' + icone.toLowerCase() + '" title="' + studentData.record.systeme + '" />');
                                            $img102 = $('<img src="graphs/icons/se/' + exist + '" title="' + studentData.record.systeme + '" />');
                                            return $img102;	
                                        }else{
                                            return studentData.record.systeme;
                                        }	
                                    }                                    
                                }							
                            },
                            systeme_version: {
                                title: 'Version',
                                width: '10%',
                                list: false
                            },
                            sn: {
                                title: 'SN',
                                width: '10%',
                                inputClass: 'validate[required]'
                            },
                            ip:{
                                title: '@ IP',
                                create: false,
                                edit: false,
                                display: function(data){                                       	
                                    var tmp = new Array();   
                                    if (data.record.id_net){
                                        var net = data.record.id_net.split('<br/>');                                   
                                        for (var i =0;i<net.length;i++){                                        
                                            var tabNet = net[i].split('@@');                                       
                                            var ipTmp = 'info'+i;                                                                                                                          
                                            $ipTmp = $('<div class="ipC" id="ip_'+tabNet[0]+'_'+data.record.id_materiel+'" style="text-decoration: underline; cursor: pointer;" title="'+tabNet[1]+'">'+tabNet[1]+'</div>');                                        
                                            ip=tabNet[1];
                                            $ipTmp.click(function () {                                             
                                                var ip2 = $(this).html();                                            
                                                var tag = $("<div></div>");
                                                $.ajax({
                                                  url: 'scripts/service.php?ip='+ip2+'&id_type='+data.record.id_type2,
                                                  success: function(ipservice) {
                                                    tag.html(ipservice).dialog({modal: true, title: 'Service(s) IP pour ce matériel '+ip2}).dialog('open');
                                                  }
                                                });
                                            });
                                            tmp[i] = $ipTmp;
                                        }
                                    }  
                                    return tmp;                                    
                                }                                
                            },
                            date_installe: {
                                list: false,
                                title: 'Installé le',
                                width: '10%',
                                type: 'date',
                                displayFormat: 'dd-mm-yy',
                                inputClass: 'validate[custom[datefr]]'
                                /*create: false,
                                edit: false*/
                            },
                            emplacement: {
                                title: 'Emplacement',
                                width: '10%'						
                            },
                            inventorOn: {
                                title: 'Inventaire',
                                width: '1%',
                                listClass: 'jtableTdCenter',
                                create: false,
                                edit: false,
                                <?php 
                                if ($_SESSION['id_zone']!=6 && $_SESSION['id_zone']!=4){
                                        echo "list: true,";
                                }else{echo "list: false,";}
                                ?>
                                display: function (data){
                                    var info='';
                                    if (data.record.inventorOn){
                                    if (data.record.inventorOn!='0000-00-00 00:00:00'){
                                        var alerte = '';
                                        var today = new Date();                                    
                                        var invent=data.record.inventorOn.split(' ');
                                        var inventDay=invent[0].split('-');
                                        var inventHour=invent[1].split(':');
                                        var olday = new Date(inventDay[0],inventDay[1]-1,inventDay[2],inventHour[0],inventHour[1],inventHour[2]);
                                        //alert(inventDay[0]+'***'+inventDay[1]+'***'+inventDay[2]);
                                        //alert(today+' *** '+olday+' *** '+diffdate(olday,today));
                                        if (diffdate(olday,today)<=7){
                                            info = '<div title="Vu dans les 7 derniers jours (Le '+data.record.inventorOn+' par '+data.record.inventorBy+')" style="background-color: #339933; width: 10px; height:10px;display: inline-block ;cursor: help;">&nbsp;</div>&nbsp;';
                                        }else  if (diffdate(olday,today)<=31){
                                            info = '<div title="Vu dans les 31 derniers jours (Le '+data.record.inventorOn+' par '+data.record.inventorBy+')" style="background-color: #FFC629; width: 10px; height:10px;display: inline-block ;cursor: help;">&nbsp;</div>&nbsp;';                                  
                                        }else {
                                            info = '<div title="N\'a pas été vu depuis plus d\'un mois (Le '+data.record.inventorOn+' par '+data.record.inventorBy+')" style="background-color: #FF0000; width: 10px; height:10px;display: inline-block ;cursor: help;">&nbsp;</div>&nbsp;';
                                        }
                                    }
                                    }
                                    return info;
                                }
                            },
                            contact: {
                                title: "Contact",
                                list: false																		
                            },							
                            description: {
                                title: "Description",
                                list: false,							
                                type: 'textarea'					
                            }								
                        }, 
	                selectionChanged: function () {
	                    //Get all selected rows
	                    var $selectedRows = $('#masterContainer').jtable('selectedRows');
						alert(selectedRows);
	                    $('#SelectedRowList').empty();
	                    if ($selectedRows.length > 0) {
	                        //Show selected rows
	                        $selectedRows.each(function () {
	                            var record = $(this).data('record');
	                            $('#SelectedRowList').append();//'<b>id_materiel</b>: ' + record.id_materiel 
	                        });
	                    } else {
	                        //No rows selected
	                        $('#SelectedRowList').append();
	                    }
	                },
	              //Initialize validation logic when a form is created
	                formCreated: function (event, data) {                	
	                    data.form.validationEngine();
	                },
	                //Validate form when it is being submitted
	                formSubmitting: function (event, data) {
	                    return data.form.validationEngine('validate');
	                },
	                //Dispose validation logic when form is closed
	                formClosed: function (event, data) {
	                    data.form.validationEngine('hide');
	                    data.form.validationEngine('detach');
	                }
			});
				//Re-load records when user click 'load records' button.
		        $('#LoadRecordsButton').click(function (e) {
		            e.preventDefault();
		            $('#masterContainer').jtable('load', {
		                Fnom: $('#Fnom').val(),
		                Fmarque: $('#Fmarque').val(),
		                Fmodele: $('#Fmodele').val(),
		                Fsysteme: $('#Fsysteme').val(),	                
                                Fip: $('#Fip').val()
		                //cityId: $('#cityId').val()
		            });
		        });                         
		        //Load all records when page is first shown
		        $('#LoadRecordsButton').click();
				//Load person list from server
				//$('#masterContainer').jtable('load');
		      //Delete selected students
                      /*$(".ip").click(function () { 
                        alert('ok');
                            var tag = $("<div></div>");
                            var ip = jQuery('this').html();
                            var type = '';
                            $.ajax({          
                              url: 'scripts/service.php?ip='+ip+'&id_type='+type,
                              success: function(ipservice) {
                                tag.html(ipservice).dialog({modal: true, title: 'Service(s) IP pour ce matériel '+ip2}).dialog('open');
                              }
                            });
                        });*/
		        $('#UpdateButton').button().click(function () {
		            var $selectedRows = $('#masterContainer').jtable('selectedRows');
		            $('#masterContainer').jtable('deleteRows', $selectedRows);
		        });
                        //alert('ok');
                        
                    });                  
		</script>
		<?php 		
		if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & SUPPRESSION &&
				(int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & SUPPRESSION){
			echo '
			<p style="margin:0; padding: 0;padding-top: 5px;">Pour la sélection : <button id="UpdateButton">Supprimer</button></p>';
		}
		?>			
 	</div>
 	</div>     
    <script type="text/javascript">
       
    </script>
    <?php       
    /*$thumb=1;
    echo '<div class="hidden-container">'.$req_doc_file;
    while ($res_doc_file=@mysql_fetch_array($rec_doc_file))
    {
            $extension=strtolower(substr(strrchr($res_doc_file['file'], '.'), 1));
            if (in_array($extension,$extensions_ok) && is_file("$file_path/upload/$res_doc_file[file]"))
            {
                    echo "<a id=\"thumb\" ";                       
                    echo "class='highslide' href='$_SESSION[path]/scripts/affImage.php?file=/upload/$res_doc_file[file]'
                    onclick=\"return hs.expand(this, miniGalleryOptions1,$thumb)\">
                    <img src='$_SESSION[path]/scripts/affImage.php?file=/upload/$res_doc_file[file]' alt=\"$res_doc_file[nom]\" style=\"height:64px; width: 64px;\"/></a>
                    ";
                    $thumb++;
            }
    }
    echo '</div>';*/
    ?>
  </body>
</html>