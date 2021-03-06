<?php
$titre="Créer un compte";
$script="../scripts/create_user.php";
$pageDescription="Merci de renseigner le formulaire ci-dessous";
include_once("../header.inc.php");
include_once("../include/functions.php");
entete_page('','../');
?>
<script type="text/javascript" src="../content/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="http://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.min.js"></script>
<!-- <script type="text/javascript" src="content/jquery-ui/js/jquery-1.4.2.min.js"></script>-->
<script type="text/javascript" src="../content/tooltip/jquery.tools.min.js"></script>
<script type="text/javascript" src="../content/winDim.js"></script>
<script type="text/javascript" src="../content/highslide/highslide-full.min.js"></script>
<script type="text/javascript" src="../content/highslide/highslide-with-html.js"></script>
<script type="text/javascript" src="../content/highcharts/highslide/highslide.config.js" charset="utf-8"></script>
<link rel="stylesheet" type="text/css" href="../content/highslide/highslide.css" />
<link type="text/css" href="../content/jquery-ui/css/start/jquery-ui-1.8.6.custom.css" rel="Stylesheet">
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery("input").focus(function() {
		jQuery("#mess").hide();
	});
	jQuery("#myForm").validate({
		debug: false,
		rules: {
			TB_Nom_Utilisateur: "required",
			TB_Mot_de_Passe: "required",
			TB_Question: "required",
			TB_Reponse: "required",
			TB_Confirmation_Mot_de_Passe: "required",
			TB_Adresse_Email: {
				required: true,
				email: true
			},	
			cgu: "required"			
		},
		messages: {
			TB_Nom_Utilisateur: "Ce champ est obligatoire",
			TB_Mot_de_Passe: "Ce champ est obligatoire",
			TB_Question: "Ce champ est obligatoire",
			TB_Reponse: "Ce champ est obligatoire",
			TB_Confirmation_Mot_de_Passe: "Ce champ est obligatoire",
			TB_Adresse_Email: "Ce champ est obligatoire est doit correspondre à un mail valide",
			cgu: "Vous devez lire et accepeter les Conditions Générales d\'Utilisation"		
		},
		submitHandler: function(form) {			
			var datas = jQuery("#myForm").serialize();		
            jQuery.ajax({
                cache: false,
                type: 'POST',
                data: datas,                
                url : '<?php echo $script;?>',
                success: function (html) {                    
                    jQuery("#mess").attr('class','mess');
                    jQuery("#mess").show(1500);                   
                    jQuery("#mess").html(html);
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
function temp(){
	alert("<?php echo $_SESSION['MESSAGE'];?>");
	jQuery("#mess").html("<?php echo $_SESSION['MESSAGE'];?>");
}
</script>
<body>
<div class="highslide-html-content" id="highslide-html">
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
<div id="container">
	<h1><?php  echo $titre;?></h1>
		<h2><?php  echo $pageDescription;?></h2>	
		<div id="mess" style="display: none;"></div>
		<div id="help" title="Aide"><a href="#" onclick="return hs.htmlExpand(this, { contentId: 'highslide-html',headingText: 'Aide',preserveContent: false } )"></a></div>
	<div class="content">
    <form method="post" id="myForm" action="" target="temp">
	<table>
	     <tr>
	          <td>Nom de connexion :</td><td> <input type="text" name="TB_Nom_Utilisateur" /></td>
	     </tr>
	     <tr>
	          <td>Mot de passe :</td><td> <input type="password" name="TB_Mot_de_Passe" /></td>
	     </tr>
	     <tr>
	          <td>Confirmation du mot de passe :</td><td> <input type="password" name="TB_Confirmation_Mot_de_Passe" /></td>
	     </tr>
	     <tr>
	          <td>Adresse e-mail :</td><td> <input type="text" name="TB_Adresse_Email" size=30></td>
	     </tr>	
	     <tr>
	          <td colspan=2><a href="#" onclick="jQuery('#cgu').toggle();" style="text-align: left;color: #fff; background-color: transparent;font-weight: normal;font-size: small; text-decoration: underline; border: none;padding: 0;float: left;">Afficher les conditions générales d'utilisation</a></td>
	     </tr>	
	     <tr>
	     	<td colspan=2>J'ai lu et j'accepte les Conditions Générales d'Utilisation <input type="checkbox" name ="cgu"></td>
	     </tr>     
	     <tr>
	     <td colspan=2 style="background-color: #fff; color: #000;display: none;" id="cgu">
	     <?php include('../include/cgu.html');?>
	     </td>
	     </tr>
	     <!-- <tr>
	          <td>Choisissez une question :</td><td> <select name="TB_Question">
	          <option></option>
	          <option value="Quel est votre acteur ou actrice préféré">Quel est votre acteur ou actrice préféré</option>
	          <option value="Quelle est la marque de votre première voiture">Quelle est la marque de votre première voiture</option>
			  <option value=""></option>
	          <option value=""></option>
	          </select></td>
	     </tr>
	     <tr>
	          <td>Réponse à la question :</td><td> <input type="text" name="TB_Reponse" size=30></td>
	     </tr> -->
	</table>
	<p id="validButton"><button type="submit" id="submitButton" tabindex="3" name="valid" value="Valider" style="cursor: pointer;">Valider</button></p>
	</form>
	</div>	
</div>
</body>
</html>