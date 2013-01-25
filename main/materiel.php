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
?>
<script type="text/javascript" src="content/jquery-ui-1.9/ui/jquery-ui.js"></script>  
	<script type="text/javascript" src="content/jquery-ui-1.9/ui/jquery.ui.core.js"></script>
	<script type="text/javascript" src="content/jquery-ui-1.9/ui/jquery.ui.widget.js"></script>
	<script type="text/javascript" src="content/jquery-ui-1.9/ui/jquery.ui.position.js"></script>
	<script type="text/javascript" src="content/jquery-ui-1.9/ui/jquery.bgiframe.js"></script> 	
	<script type="text/javascript" src="content/jquery-ui-1.9/ui/jquery.ui.tooltip.js"></script>
<script type="text/javascript">
jQuery(document).ready( function() {
    jQuery("img").tooltip({
        position: {
            my: "center bottom-20",
            at: "center top",
            width: '100px',
            using: function( position, feedback ) {
                $( this ).css( position );
                $( "<div>" )
                .addClass( "arrow" )
                .addClass( feedback.vertical )
                .addClass( feedback.horizontal )
                .appendTo( this );
    }
        }
    });				
});
</script>
<html>
  <head>

   <!-- <link href="themes/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css" />-->	
	
	<!-- <script src="scripts/jquery-1.6.4.min.js" type="text/javascript"></script>
    <script src="scripts/jquery-ui-1.8.16.custom.min.js" type="text/javascript"></script> -->    
	
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
                            id_zone: {
                                    list: false,
                                    create: false,
                                    title: 'Zone',							
                                    options: 'main/liste/getZone.php',
                                    inputClass: 'validate[required]'
                            },
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
		                        $img1 = $('<img src="graphs/icons/note.png" title="Ajouter ou modifier une carte réseau" />')	                      
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
		                                    }, function (data) { //opened handler		                                   
		                                        data.childTable.jtable('load');		                                                                          
		                                    	//$('#childContainer').jtable('load');
		                                    });	 	                                               
		                        });	                       
		                        //Return image to show on the person row	  	                                                    	                                      
		                        return $img1;	                        
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
                                    $img0 = $('<img src="graphs/icons/config.png" title="Ajouter ou modifier une carte réseau" />')	                      
                                    //Open child table when user clicks the image
                                    $img0.click(function () {	                        	                 
                                        $('#masterContainer').jtable('openChildTable',	    	                            
                                            $img0.closest('tr'),
                                            {
                                                title: 'Configuration du matériel',
                                                actions: {
                                                <?php 
                                                if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & LECTURE && 
                                                                (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & LECTURE){
                                                        echo "listAction: 'scripts/update_opt.php?action=list&id_materiel=' + netData.record.id_materiel,";
                                                }
                                                ?>
                                                },
                                                fields: {
                                                    id: {                                                       
                                                        type: 'hidden',
                                                        create: false,
                                                        edit: false,                                                        
                                                        list: false
                                                    },
                                                    add_net: {
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
                                                            $img1 = $('<img src="graphs/icons/net.png" title="Ajouter ou modifier une carte réseau" />')	                      
                                                            //Open child table when user clicks the image
                                                            $img1.click(function () {	                        	                 
                                                                $('#masterContainer').jtable('openChildTable',	    	                            
                                                                        $img1.closest('tr'),
                                                                        {
                                                                            title: 'Cartes réseaux',	  
                                                                            pageSize: <?php echo $_SESSION['pageSize'];?>,
                                                                                sorting: true,	
                                                                                paging: true,
                                                                                defaultSorting: 'ip ASC',					                                     
                                                                            actions: {
                                                                                <?php 
                                                                                if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & LECTURE && 
                                                                                                (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & LECTURE){
                                                                                        echo "listAction: 'scripts/update_net.php?action=list&id_materiel=' + netData.record.id_materiel,";
                                                                                }
                                                                                if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                                                (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                                        echo "updateAction: 'scripts/update_net.php?action=update&id_materiel=' + netData.record.id_materiel,";
                                                                                }
                                                                                if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & CREATION &&
                                                                                                (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & CREATION){
                                                                                        echo "createAction: 'scripts/update_net.php?action=create&id_materiel=' + netData.record.id_materiel,";
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
                                                                                    defaultValue: netData.record.id_materiel
                                                                                }  ,                                       
                                                                                id_net: {	                                            					                                            
                                                                                    key: true,
                                                                                    create: false,
                                                                                    edit: false,
                                                                                    list: false                                          
                                                                                },	
                                                                                id_marque: {
                                                                                    title: 'Marque',
                                                                                    width: '10%',
                                                                                    options: 'main/liste/getMarque.php'
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
                                                                                            var $img2 = new Array();
                                                                                            $img2 = $('<img src="graphs/icons/edit.png" title="Modifier le modèle" />')	                      
                                                                                            //Open child table when user clicks the image
                                                                                            $img2.click(function () {	                        	                 
                                                                                            $('#masterContainer').jtable('openChildTable',	    	                            
                                                                                                $img2.closest('td'),
                                                                                                {
                                                                                                    title: 'Modèle(s) de la marque ' + netChildData.record.marque,
                                                                                                    pageSize: <?php echo $_SESSION['pageSize'];?>,		                    					
                                                                                                        paging: true,	
                                                                                                        sorting: true,	                    					
                                                                                                        defaultSorting: 'modele2 ASC',				                                       
                                                                                                    actions: {
                                                                                                        <?php 
                                                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & LECTURE && 
                                                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & LECTURE){
                                                                                                                echo "listAction: 'scripts/update_modele.php?action=list&id_marque=' + netChildData.record.id_marque + '&id_materiel=' + netChildData.record.id_materiel + '&id_modele=' + netChildData.record.id_modele,";
                                                                                                        }
                                                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                                                                echo "updateAction: 'scripts/update_modele.php?action=update&id_marque=' + netChildData.record.id_marque + '&id_materiel=' + netChildData.record.id_materiel + '&id_modele=' + netChildData.record.id_modele,";
                                                                                                        }
                                                                                                        if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & CREATION &&
                                                                                                                        (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & CREATION){
                                                                                                                echo "createAction: 'scripts/update_modele.php?action=create&id_marque=' + netChildData.record.id_marque + '&id_materiel=' + netChildData.record.id_materiel + '&id_modele=' + netChildData.record.id_modele,";
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
                                                                                                            list: false
                                                                                                        }  ,                    		                                            
                                                                                                        modele_edit2: {
                                                                                                            title: '',
                                                                                                            width: '1%',
                                                                                                            edit: false,
                                                                                                            create: false,		                        								
                                                                                                            //options: 'main/liste/getModele.php',
                                                                                                            display: function (modeleData) {				
                                                                                                                //Create an image that will be used to open child table
                                                                                                                var $img3 = new Array();
                                                                                                                $img3 = $('<img src="graphs/icons/valid.png" title="Choisir ce modèle" />')	                      
                                                                                                                //Open child table when user clicks the image
                                                                                                                $img3.click(function () {	                        	                 
                                                                                                                    $('#masterContainer').jtable('updateRecord', {	    	                            
                                                                                                                        record: {		                        	                                   
                                                                                                                            id_materiel: netChildData.record.id_materiel,		                    			                        	                                    
                                                                                                                            id_materiel2: netChildData.record.id_net,
                                                                                                                            id_modele: modeleData.record.id_modele2,		                    			                        	                                   
                                                                                                                            source: 'net'
                                                                                                                        },	                        	                                    
                                                                                                                        url: 'scripts/update_modele.php?action=change',
                                                                                                                        success: function(){
                                                                                                                            $('#masterContainer').jtable('closeChildTable',$img2.closest('td'));
                                                                                                                        }		                        	                                                      	                                                     	                                     	                               
                                                                                                                    });	                        	                             	                        	                           
                                                                                                                });	                       
                                                                                                                //Return image to show on the person row	  	                        	                                                    	                                      
                                                                                                                return $img3;	                        	                        
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
                                                                                    return $img2;	                        
                                                                            }
                                                                        },					
                                                                        modele:{
                                                                                title: 'Modèle',
                                                                                width: '10%',
                                                                                edit: false,
                                                                                create: false,
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
                                                                            list: false,
                                                                        }, 
                                                                        gw:{
                                                                            title: 'Passerelle',
                                                                            list: false,
                                                                        } ,
                                                                                                                    net:{
                                                                            title: 'Réseau',
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
                                                                            /*create: false,
                                                                            edit: false*/
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
                                                            return $img1;	                        
                                                    }
                                                },
                                                }
                                            }
                                        )
                                    });
                                    return $img0;
                                }
                            },                            							
                            id_type: {							
                                    title: 'Type',
                                    width: '1%',							
                                    options: 'main/liste/getType.php?source=materiel',
                                    inputClass: 'validate[required]',
                                    display: function (studentData) {				
		                        //Create an image that will be used to open child table
		                        var $img4 = new Array();		                       
		                        var icone=studentData.record.type_materiel + '.png';
		                        icone = AccentToNoAccent(icone);			                        
		                        var exist = test_fichier('/graphs/icons/materiel/' + icone.toLowerCase());		                       
		                        if (exist == 1){		                        
                                            $img4 = $('<img src="graphs/icons/materiel/' + icone.toLowerCase() + '" title="' + studentData.record.type_materiel + '" />');
                                            return $img4;	
		                        }else{
                                            return studentData.record.type_materiel;
		                        }	                        		                        	                
                                    }
                            },
                            nom: {
                                title: 'Nom',
                                width: '10%',
                                inputClass: 'validate[required]'
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
		                        var $img5 = new Array();
		                        $img5 = $('<img src="graphs/icons/edit.png" title="Modifier le modèle" />')	                      
		                        //Open child table when user clicks the image
		                        $img5.click(function () {	                        	                 
		                            $('#masterContainer').jtable('openChildTable',	    	                            
		                                    $img5.closest('tr'),
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
                                                                    echo "listAction: 'scripts/update_modele.php?action=list&id_marque=' + studentData.record.id_marque + '&id_materiel=' + studentData.record.id_materiel + '&id_modele=' + studentData.record.id_modele,";
                                                            }
                                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & MODIFICATION &&
                                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                                                    echo "updateAction: 'scripts/update_modele.php?action=update&id_marque=' + studentData.record.id_marque + '&id_materiel=' + studentData.record.id_materiel + '&id_modele=' + studentData.record.id_modele,";
                                                            }
                                                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone_pere']] & CREATION &&
                                                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & CREATION){
                                                                    echo "createAction: 'scripts/update_modele.php?action=create&id_marque=' + studentData.record.id_marque + '&id_materiel=' + studentData.record.id_materiel + '&id_modele=' + studentData.record.id_modele,";
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
                                                                    var $img6 = new Array();
                                                                    $img6 = $('<img src="graphs/icons/valid.png" title="Choisir ce modèle" />')	                      
                                                                    //Open child table when user clicks the image
                                                                    $img6.click(function () {	                        	                 
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
                                                                                $('#masterContainer').jtable('closeChildTable',$img5.closest('tr'));
                                                                            }		                        	                                                      	                                                     	                                     	                               
                                                                        }); 	                        	                             	                        	                           
                                                                    });	                       
                                                                    //Return image to show on the person row	  	                        	                                                    	                                      
                                                                    return $img6;	                        	                        
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
		                        return $img5;	                        
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
                                display: function (studentData) {				
                                    //Create an image that will be used to open child table
                                    var $img7 = new Array();		                       
                                    var icone=studentData.record.systeme + '.png';
                                    icone = AccentToNoAccent(icone);			                        
                                    //var exist = test_fichier('/graphs/icons/se/' + icone.toLowerCase());
                                    var exist = test_fichier(icone.toLowerCase());
                                    //alert(exist);
                                    if (exist != 0){		                        
                                        //$img7 = $('<img src="graphs/icons/se/' + icone.toLowerCase() + '" title="' + studentData.record.systeme + '" />');
                                        $img7 = $('<img src="graphs/icons/se/' + exist + '" title="' + studentData.record.systeme + '" />');
                                        return $img7;	
                                    }else{
                                        return studentData.record.systeme;
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
                                    var info = '';	
                                    var tmp = new Array();
                                    var ips = data.record.ip;
                                    ips = ips.split('<br/>');
                                    for (var i =0;i<ips.length;i++){    
                                        //alert(ips[i]);                                        
                                        $info = $('<div style="text-decoration: underline; cursor: pointer;" title="Services IP">'+ips[i]+'</div>')
                                        /*info = '<div title="Priorité basse" style="background-color: #826666; width: 10px; height:10px;display: inline-block ;">&nbsp;</div>&nbsp;';*/
                                        $info.click(function () {	
                                            var tag = $("<div></div>");
                                                $.ajax({
                                                  url: 'scripts/service.php?ip='+ips[i]+'&id_type='+data.record.id_type,
                                                  success: function(ipservice) {
                                                    tag.html(ipservice).dialog({modal: true, title: 'Service(s) IP pour ce matériel'}).dialog('open');
                                                  }
                                                });
                                        });  
                                        tmp[i] = $info;
                                        //alert(tmp.toSource());
                                    }   
                                    var tmp2='';
                                    for (var i =0;i<ips.length;i++){                                    
                                        tmp = tmp+tmp[i];
                                    }
                                    alert(tmp.toSource());
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
		        $('#UpdateButton').button().click(function () {
		            var $selectedRows = $('#masterContainer').jtable('selectedRows');
		            $('#masterContainer').jtable('deleteRows', $selectedRows);
		        });	
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
  </body>
</html>