<?php
$titre="J'ai oublié mon identifiant";
$script="../scripts/lost_id.php";
$pageDescription="Merci de renseigner le formulaire ci-dessous";
include_once("../header.inc.php");
include_once("../include/functions.php");
entete_page('',$GLOBALS['params']['appli']['root_folder']);
?>
<script type="text/javascript" src="../content/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="http://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.min.js"></script>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery("input").focus(function() {
		jQuery("#mess").hide();
	});
	jQuery("#myForm").validate({
		debug: false,
		rules: {			
			TB_Adresse_Email: {
				required: true,
				email: true
			},				
		},
		messages: {			
			TB_Adresse_Email: "Ce champ est obligatoire est doit correspondre à un mail valide"		
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
<div id="container">
	<h1><?php  echo $titre;?></h1>
		<h2><?php  echo $pageDescription;?></h2>	
		<div id="mess" style="display: none;"></div>
		<div id="help" title="Aide"><a href="#" onclick="return hs.htmlExpand(this, { contentId: 'highslide-html',headingText: 'Aide',preserveContent: false } )"></a></div>
	<div class="content">
    <form method="post" id="myForm" action="" target="temp">
	<table>	     
	     <tr>
	          <td>Adresse e-mail :</td><td> <input type="text" name="TB_Adresse_Email" size=30></td>
	     </tr>	     	     
	</table>
	<p id="validButton"><button type="submit" id="submitButton" tabindex="3" name="valid" value="Valider" style="cursor: pointer;">Valider</button></p>
	</form>
	</div>	
</div>
</body>
</html>