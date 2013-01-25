<?PHP
if(!isset($_COOKIE["ID_UTILISATEUR"])){
	header("Location: ../logout.php");
	exit;
}
session_start();
$page="entite.php";
$script="scripts/update_entite.php";
$titre="Entité";
$pageDescription="Détails administratifs de l'entité";

include_once("../header.inc.php");
include_once("../include/functions.php");
include_once("../include/protect_var.php");
include_once("../include/textes.php");
include("../include/check_perms.php");
?>
<script type="text/javascript">
jQuery(document).ready(function(){	
	jQuery("input").focus(function() {
		jQuery("#mess").hide();
	});	
	jQuery("#myForm").validate({
		debug: false,
		rules: {
			nom: "required",
			alias: "required",
			adresse: "required",
			cp: "required",
			ville: "required",
			email: {
				required: true,
				email: true
			},
			tel: "required",
		},
		messages: {
			nom: "Le nom est obligatoire",
			alias: "L'alias est obligatoire",
			adresse: "L'adresse est obligatoire",
			cp: "Le code postal est obligatoire",
			ville: "La ville est obligatoire",
			email: "Une adresse mail valide est obligatoire",
			tel: "le téléphone est obligatoire"
		},
		submitHandler: function(form) {			
			var datas = jQuery("#myForm").serialize();	
			jQuery("#mess").hide();				
            jQuery.ajax({
                cache: false,
                type: 'POST',
                data: datas,
                url : '<?php echo $script;?>',
                success: function (response) {                    
                    jQuery("#mess").attr('class','mess');
                    jQuery("#mess").show(1500);
                    jQuery("#mess").html(response);
                },
                error: function(data, textStatus, jqXHR) {
                	jQuery("#mess").attr('class','messErr');
                    jQuery("#mess").show(1500);
                    jQuery("#mess").html(data);                	
                }
            })
		}
	});	
});
function affTab(tab){
	jQuery("#"+tab).toggle();
}
/*jQuery(document).ready( function() {
    jQuery("img[title]").tooltip({
		// tweak the position
        offset: [0, 0],
        // use the "slide" effect        
        //position: 'bottom right' ,
        effect: 'slide'              
        // add dynamic plugin with optional configuration for bottom edge
    }).dynamic({ bottom: { direction: 'down', bounce: true } });    
});*/
</script>
</head>
<body>
<?php 
	connectSQL();
	$requete="SELECT * FROM entite WHERE id=:id_entite;";
	$prep=$db->prepare($requete) ;
	$prep->bindParam(":id_entite",$_SESSION['id_entite'],PDO::PARAM_INT);
	$prep->execute();	
	$row = $prep->fetch(PDO::FETCH_ASSOC);
	$prep->closeCursor();
	$prep = NULL;
	$requete="SELECT id,source,detail FROM type WHERE source='etab'";
	$rowType = $db->query($requete);	
	//$resAcount=@mysql_fetch_array(@mysql_query($req)); 	
?>	
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
		<form method="post" id="myForm" action="#">
		<input type="hidden" name="page" value="<?php echo $page;?>">		
		<div class="formLeft">
			<table>		
			<tr><td>Alias</td><td><input name="alias" value="<?php echo $row['alias'];?>"></td></tr>
			<tr style="vertical-align: middle;"><td colspan=2 style="vertical-align: middle;"></td></tr>				
			<tr><td>Type</td><td><select name="id_type">		
			<?php 
			while ($enr=$rowType->fetch(PDO::FETCH_ASSOC)){
				echo "<option value=\"$enr[id]\"";
				if ($row['id_type']==$enr[id]){
					echo ' selected';
				}
				echo ">$enr[detail]</option>";
			}
			?>			
			</select></td></tr>			
			<tr><td>Nom</td><td><input name="nom" value="<?php echo $row['nom'];?>"></td></tr>
			<tr><td>Adresse</td><td><input name="adresse" value="<?php echo $row['adresse'];?>"></td></tr>
			<tr><td>Ville</td><td><input name="ville" value="<?php echo $row['ville'];?>"></td></tr><tr>
			<tr><td>CP</td><td><input name="cp" value="<?php echo $row['cp'];?>"></td></tr>
			<tr><td>Tél</td><td><input name="tel" value="<?php echo $row['tel'];?>"></td></tr>
			<tr><td>Fax</td><td><input name="fax" value="<?php echo $row['fax'];?>"></td></tr>
			<tr><td>Email</td><td><input name="email" value="<?php echo $row['email'];?>"></td></tr>
			<tr><td>Directeur</td><td><input name="directeur" value="<?php echo $row['directeur'];?>"></td></tr>
			<tr><td>Adjoint</td><td><input name="adjoint" value="<?php echo $row['adjoint'];?>"></td></tr>
			<tr><td>Gestionnaire</td><td><input name="gestionnaire" value="<?php echo $row['gestionnaire'];?>"></td></tr>						
			</table>
		</div>
		<div class="formRight">
                    <div id="SelectedRowList"></div>	
                    <div id="masterContainer" style="width: 100%;padding-top: 0px;">	
			<div id="childContainer"></div>	
                    </div>			                    
		</div>
		<?php 
		if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone']] & MODIFICATION &&
				(int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
			echo '<p id="validButton"><button type="submit" id="submitButton" name="valid" value="Valider" style="cursor: pointer;" class="buttonValid">Valider</button></p>';
		}else{
			echo '<p id="validButton"></p>';
		}
		?>
		<!-- <p id="validButton"><button type="submit" id="submitButton" name="valid" value="Valider" style="cursor: pointer;" class="buttonValid">Valider</button></p> -->	
		</form>                
                <script type="text/javascript">	                    
		$(document).ready(function () {	
                    //Prepare jTable               
                    $('#masterContainer').jtable({
                        title: 'Liste des contacts',
                        paging: true,	                   												
                        pageSize: <?php echo $_SESSION['pageSize'];?>,
                        sorting: true,					
                        defaultSorting: 'nom ASC',
                        actions: {
                            <?php 						
                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone']] & LECTURE &&
                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & LECTURE){
                                    echo "listAction: '$script?action=list',";
                            }
                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone']] & CREATION && 
                                (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & CREATION){
                                    echo "createAction: '$script?action=create',";
                            }
                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone']] & MODIFICATION &&
				(int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                                    echo "updateAction: '$script?action=update',";
                            }
                            if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone']] & SUPPRESSION && 
                                            (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & SUPPRESSION){
                                    echo "deleteAction: '$script?action=delete',";
                            }
                            ?>                        
                        },
                        fields: {
                            id_contact: {
                                key: true,
                                create: false,
                                edit: false,
                                list: false
                            },
                            nom: {                           
                                title: 'Nom',							                            
                                inputClass: 'validate[required]'
                            }, 
                             prenom: {                           
                                title: 'Prénom',							                            
                                inputClass: 'validate[required]'
                            }, 
                             fonction: {                           
                                title: 'Fonction',							                            
                                inputClass: 'validate[required]'
                            }, 
                             mail1: {                           
                                title: 'Mail Pro',							                            
                                inputClass: 'validate[required,email]'
                            },
                             mail2: {                           
                                title: 'Mail Autre',							                            
                                inputClass: 'validate[required,email]',
                                list:false
                            },
                             tel1: {                           
                                title: 'Tél Pro',	
                                list:false
                            },
                             tel2: {                           
                                title: 'Tél Autre',							                                                           
                                list:false
                            },
                        }
                    });
                    $('#masterContainer').jtable('load');
                });
                </script>
	</div>
</div>
</body>
</html>