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
		<table id="parent" <?php if (@mysql_num_rows($recParent)==0){echo " style=\"display: none;\"";}?>>					
			<tr>
				<td colspan=2 class="separ">Parent</td>
			</tr>
			<tr>
				<td>N° URSSAF</td><td><input name="num_urssaf" value="<?php if (!empty($resParent[num_urssaf])){echo trim(dechiffre(hex2bin("$resParent[num_urssaf]"), "$GLOBALS[params][appli][key]"));}?>"></td>
			</tr> 		
		</table>
		<table id="asm" <?php if (@mysql_num_rows($recAsm)==0){echo " style=\"display: none;\"";}?>>						
			<tr>
				<td colspan=2 class="separ">Assistance maternelle</td>
			</tr>
			<tr>
				<td>N° Sécurité sociale</td><td><input name="num_secu" value="<?php if (!empty($resAsm[num_secu])){echo trim(dechiffre(hex2bin("$resAsm[num_secu]"), "$GLOBALS[params][appli][key]"));}?>"></td>
			</tr>
			<tr>
				<td>Agrément pour</td>
				<td>
				<select name="agrement">
				<?php 
				for($i=0;$i<=10;$i++){ 
					echo "<option value=\"$i\"";
					if ($resAsm['agrement']==$i){echo " selected";}
					echo ">$i</option>";
				}
				?>
				</select>
				&nbsp;enfant(s)</td>
			</tr>
			<tr>
				<td>Fin de l'agrément</td>
				<td align="left" style="vertical-align: middle;">
					<input style="vertical-align: middle;" readonly type="text" size=5 name="agrement_fin" id="data"  value="<?php echo $resAsm['agrement_fin'];?>">
                    <img src="graphs/icons/cal.png" id="f_trigger_a"
                    style="text-align: left;cursor: pointer; vertical-align: middle;"
					title="Choisissez une date"
					onmouseover="this.style.background='blue';"
					onmouseout="this.style.background=''"/>
						<script type="text/javascript">
							Calendar.setup({
                            	inputField     :    "data",
                                ifFormat       :    "%Y-%m-%d",
                                button         :    "f_trigger_a",
                                singleClick    :    true
                             });
                        </script>
				</td>
			</tr>
			<tr>
				<td>Permis B</td><td><input type="checkbox" name="permis" value="1" 
				<?php 
				if ($resAsm['permis']==1){echo ' checked="checked"';}
				?>
				></td>
			</tr>
			<tr>
				<td colspan=2>J'accepte les moyens de paiement suivant :</td>
			</tr>
			<tr>
				<td>Carte bancaire</td><td><input type="checkbox" name="CB" value="1"
				<?php
				if ($resAsm['CB']==1){ echo ' checked="checked"';}
				?>
				></td>
			</tr>				
			<tr>
				<td>Chèque</td><td><input type="checkbox" name="CHQ" value="1"
				<?php
				if ($resAsm['CHQ']==1){echo ' checked="checked"';}
				?>
				></td>
			</tr>
			<tr>
				<td>Viremement bancaire</td><td><input type="checkbox" name="VIR" value="1"
				<?php
				if ($resAsm['VIR']==1){echo ' checked="checked"';}
				?>
				></td>
			</tr>
			<tr>
				<td>Espèce</td><td><input type="checkbox" name="ESP" value="1"
				<?php
				if ($resAsm['ESP']==1){echo ' checked="checked"';}
				?>
				></td>
			</tr>
			<tr>
				<td>Tickets CESU</td><td><input type="checkbox" name="CESU" value="1"
				<?php
				if ($resAsm['CESU']==1){echo ' checked="checked"';}
				?>
				></td>
			</tr>			
			</table>
			<table id= "change_password" style="display: none;">
				<tr><td colspan=2 class="separ">Modification du mot de passe</td></tr>
				<tr><td>Ancien mot de passe</td><td><input type="password" name="old_password"></td></tr>
				<tr><td>Nouveau mot de passe</td><td><input type="password" name="new_password"></td></tr>
				<tr><td>Confirmer</td><td><input type="password" name="new_password2"></td></tr>
				</table>
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
	</div>
</div>
</body>
</html>