<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

if(!isset($_COOKIE["ID_UTILISATEUR"])){
	header("Location: logout.php");
	exit;
}
session_start();
$titre="Menu Principal";
$pageDescription="";
include_once("header.inc.php");
include_once("include/functions.php");
include_once("include/protect_var.php");
connectSQL();
$reqUser="SELECT nom, prenom, genre,is_admin FROM user WHERE id=$_COOKIE[ID_UTILISATEUR];";
$resUser=$db->query($reqUser)->fetch(PDO::FETCH_OBJ);
setcookie("NOM", $resUser->nom, time()+3600, "/");
setcookie("PRENOM", $resUser->prenom, time()+3600, "/");
setcookie("GENRE", $resUser->genre, time()+3600, "/");
if ($resUser->is_admin == 1){
	$_SESSION['isAdmin']=1;
}
entete_page($GLOBALS['params']['appli']['title_appli']." - ".$titre,'');
if (isset($_GET['message']) && stristr($_GET['message'],'merci')){	
	$message=clean_var($_GET['message']);	
}else{
	$message = ' ';
}
setlocale (LC_TIME, 'fr_FR.utf8');
$dateFormat = date('Y-m-d');
$dateFR = strftime("%A %d %B %Y",strtotime("$dateFormat"));
$idForCal=bin2hex(chiffre("$_COOKIE[ID_UTILISATEUR]", "$_SESSION[UNIQID]"));
$typeForCal=bin2hex(chiffre("user", "$_SESSION[UNIQID]"));
?>
<script type="text/javascript" src="include/functions.js"></script>
<script type="text/javascript" src="content/jquery-1.8.1.min.js"></script>
<script type="text/javascript" src="content/jquery.base64.min.js"></script>
<script type="text/javascript" src="content/ajaxFileUpload/ajaxfileupload.js"></script>
<!--<script type="text/javascript" src="content/jquery.form.js"></script>-->
<!-- UI -->
	<!-- 1.8 -->
	<!-- <script type="text/javascript" src="content/jquery-ui/js/jquery-ui-1.8.23.custom.min.js"></script>
	<script type="text/javascript" src="content/jquery-ui/js/jquery.ui.core.js"></script>
	<script type="text/javascript" src="content/jquery-ui/js/jquery.ui.widget.js"></script>
	<script type="text/javascript" src="content/jquery-ui/js/jquery.ui.position.js"></script>
	<script type="text/javascript" src="content/jquery-ui/js/jquery.ui.autocomplete.js"></script>
	 --> 
	<!-- 1.9 -->	
	<script type="text/javascript" src="content/jquery-ui-1.9/ui/jquery-ui.js"></script>  
	<script type="text/javascript" src="content/jquery-ui-1.9/ui/jquery.ui.core.js"></script>
	<script type="text/javascript" src="content/jquery-ui-1.9/ui/jquery.ui.widget.js"></script>
	<script type="text/javascript" src="content/jquery-ui-1.9/ui/jquery.ui.position.js"></script>
	<script type="text/javascript" src="content/jquery-ui-1.9/ui/jquery.ui.autocomplete.js"></script>
	<script type="text/javascript" src="content/jquery-ui-1.9/ui/jquery.bgiframe.js"></script> 	
	<script type="text/javascript" src="content/jquery-ui-1.9/ui/jquery.ui.tooltip.js"></script>	
<!-- PrettyPhoto -->
<link rel="stylesheet" href="content/prettyPhoto/css/prettyPhoto.css" type="text/css" media="screen" charset="utf-8" />
<script src="content/prettyPhoto/js/jquery.prettyPhoto.js" type="text/javascript" charset="utf-8"></script>
<!-- -->
<link type="text/css" href="content/jquery-ui/css/start/jquery-ui-1.8.6.custom.css" rel="Stylesheet">
<script type="text/javascript" src="content/jquery.validate.min.js"></script>
<!-- Jtable -->
	<link href="content/jtable/themes/standard/blue/jtable_blue.css" rel="stylesheet" type="text/css" /> 
	<!-- <link href="content/jtable/themes/lightcolor/blue/jtable.css" rel="stylesheet" type="text/css" />-->
	<script type="text/javascript" src="content/jtable/jquery.jtable.js"></script>		
	
<style>
	.ui-autocomplete-loading { background: white url('graphs/icons/ui-anim_basic_16x16.gif') right center no-repeat; }
	#city { width: 25em; }
	</style>
 <!-- Select Menu -->
	<link href="content/selectmenu/ui.selectmenu.css" rel="stylesheet" type="text/css" />
	<script src="content/selectmenu/ui.selectmenu.js" type="text/javascript"></script>
<!-- Import Javascript files for validation engine (in Head section of HTML) -->
<link href="content/jtable/validationEngine/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="content/jtable/validationEngine/jquery.validationEngine.js"></script>
<script type="text/javascript" src="content/jtable/validationEngine/jquery.validationEngine-fr.js"></script>
<!-- SyntaxHighLigher -->
	<!-- <link href="content/jtable/syntaxhighligher/shCore.css" rel="stylesheet" type="text/css" />
    <link href="content/jtable/syntaxhighligher/shThemeDefault.css" rel="stylesheet" type="text/css" />
	<script src="content/jtable/syntaxhighligher/shCore.js" type="text/javascript"></script>
    <script src="content/jtable/syntaxhighligher/shBrushJScript.js" type="text/javascript"></script>
    <script src="content/jtable/syntaxhighligher/shBrushXml.js" type="text/javascript"></script>
    <script src="content/jtable/syntaxhighligher/shBrushCSharp.js" type="text/javascript"></script>
    <script src="content/jtable/syntaxhighligher/shBrushSql.js" type="text/javascript"></script>
    <script src="content/jtable/syntaxhighligher/shBrushPhp.js" type="text/javascript"></script>-->
<!-- Tooltip -->
    <!--<script type="text/javascript" src="content/tooltip/jquery.tools.min.js"></script>-->
<!-- Highslide -->
<script type="text/javascript" src="content/winDim.js"></script>
<script type="text/javascript" src="content/highslide/highslide-full.min.js"></script>
<script type="text/javascript" src="content/highslide/highslide-with-html.js"></script>
<script type="text/javascript" src="content/highslide/highslide.config.js" charset="utf-8"></script>
<!-- <script type="text/javascript" src="content/jquery.cookie.js"></script> -->
<link rel="stylesheet" type="text/css" href="content/highslide/highslide.css" />
<!-- <link rel="stylesheet" type="text/css" href="content/sliding_panel/style.css" media="screen" /> -->
<!--showloading-->
<link rel="stylesheet" type="text/css" href="content/showLoading/css/showLoading.css">
<script type="text/javascript" src="content/showLoading/js/jquery.showLoading.js"></script>
<!-- Calendrier -->      
<link rel="stylesheet" type="text/css" href="content/calendar/calendar-blue2.css">
<script type="text/javascript" src="content/calendar/calendar.js"></script>
<script type="text/javascript" src="content/calendar/lang/calendar-fr.js"></script>
<script type="text/javascript" src="content/calendar/calendar-setup.js"></script>
<script type="text/javascript">
	var normalLeft = 0;
	var normalRight = 0;		
	var normalWidth = 0;
	var normalHeight = 0;	
    hs.graphicsDir = 'content/highslide/graphics/';
    hs.outlineType = 'rounded-white';
	hs.registerOverlay({
		//html: '<div class="highslide-header"><ul><li class="highslide-restore"><a href="#" title="Restaurer" onclick="return hs.restore(this)"></a></li><li class="highslide-maximize"><a href="#" title="Agrandir" onclick="return hs.maximize(this)"></a></li></ul></div><div class="closebutton" onclick="return hs.close(this)" title="Fermer"></div>',
		html: '<div class="moveMenu"><div class="highslide-maximize" title="Agrandir" onclick="return hs.maximize(this)"></div><div class="highslide-space"></div><div class="highslide-restore" title="Restaurer" onclick="return hs.restore(this)"></div></div><div class="closebutton" onclick="return hs.close(this)" title="Fermer"></div>',
		position: 'top right',
		fade: 2,
		useOnHtml: true
	});	
	hs.maximize = function(el) {
		var exp = hs.getExpander(el);		
		normalWidth = exp.x.size;
		normalHeight = exp.y.size;		
		hs.getPageSize();	
		 for (i = 0; i < hs.expanders.length; i++) {
		      exp = hs.expanders[i];
		      if (exp) {
		         var x = exp.x,
		            y = exp.y;

		         // get new thumb positions
		         exp.tpos = hs.getPosition(exp.el);
		         x.calcThumb();
		         y.calcThumb();

		         // calculate new popup position
		         x.pos = x.tpos - x.cb + x.tb;
		         x.scroll = hs.page.scrollLeft;
		         x.clientSize = hs.page.width;
		         y.pos = y.tpos - y.cb + y.tb;
		         y.scroll = hs.page.scrollTop;
		         y.clientSize = hs.page.height;
		         exp.justify(x, true);
		         exp.justify(y, true);

		         // set new left and top to wrapper and outline
		         //exp.moveTo(x.pos, y.pos);
		         normalLeft = x.pos;
		         normalRight = y.pos;
		      }
		   }		
		exp.moveTo (0, 14);
		exp.resizeTo(hs.page.width - 15, hs.page.height - 25);	
		return false;
	}
	hs.restore = function(el) {		
		var exp = hs.getExpander(el);				
		hs.getPageSize();		
		exp.moveTo (normalLeft, normalRight);	
		exp.resizeTo(normalWidth, normalHeight);
		hs.align = 'center';		
		return false;
	}
	//hs.Expander.prototype.onBeforeClose = function (sender) {}								
	hs.Expander.prototype.printIframe = function () {
	   var name = this.iframe.name;
	   frames[name].focus();
	   frames[name].print();
	   return false;
	}
	hs.Expander.prototype.printHtml = function ()
	{
	    var pw = window.open("about:blank", "_new");
	    pw.document.open();
	    pw.document.write(this.getHtmlPrintPage());
	    pw.document.close();
	    return false;
	};
	hs.Expander.prototype.getHtmlPrintPage = function()
	{
	    // We break the closing script tag in half to prevent
	    // the HTML parser from seeing it as a part of
	    // the *main* page.
	    var body = hs.getElementByClass(this.innerContent, 'DIV', 'highslide-body')
	        || this.innerContent;

	    return "<html>\n" +
	        "<head>\n" +
	        "<title>Temporary Printing Window</title>\n" +
	        "<script>\n" +"function step1() {\n" +
	        "  setTimeout('step2()', 10);\n" +
	        "}\n" +
	        "function step2() {\n" +
	        "  window.print();\n" +
	        "  window.close();\n" +
	        "}\n" +
	        "</scr" + "ipt>\n" +
	        "</head>\n" +
	        "<body onLoad='step1()'>\n"
	        +body.innerHTML +
	        "</body>\n" +
	        "</html>\n";
	};
</script>

<script type="text/javascript">
jQuery(document).ready( function() {
	jQuery("img[title]").tooltip({
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
	jQuery("input[title]").tooltip({
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
<script type="text/javascript">
jQuery(document).ready(function(){    
    jQuery("a[rel^='prettyPhoto']").prettyPhoto();
    jQuery(".trigger").click(function(){
            jQuery(".panel").toggle("fast");
            jQuery(this).toggleClass("active");
            return false;
    });	
    jQuery(".link").click(function(){
            jQuery(".panel").toggle("fast");
            jQuery(".trigger").toggleClass("active");
            return false;
    });
    jQuery("#listeEntite").change(changeEntite);
    $(function(){		
            //$('select#listeEntite').selectmenu();	
            $('select#listeEntite').selectmenu({
                style:'dropdown',
                width: 178,
                menuWidth: 400,
                format: addressFormatting
            });	
    });	
    //a custom format option callback
    var addressFormatting = function(text){
            var newText = text;
            //array of find replaces
            var findreps = [
                {find:/^([^\-]+) \- /g, rep: '<span class="ui-selectmenu-item-header" style="font-weight: bold;">$1</span>'},
                /*{find:/([^\|><]+) \| /g, rep: '<span class="ui-selectmenu-item-content">$1</span>'},
                {find:/([^\|><\(\)]+) (\()/g, rep: '<span class="ui-selectmenu-item-content">$1</span>$2'},
                {find:/([^\|><\(\)]+)$/g, rep: '<span class="ui-selectmenu-item-content">$1</span>'},
                {find:/(\([^\|><]+\))$/g, rep: '<span class="ui-selectmenu-item-footer">$1</span>'}*/
            ];

            for(var i in findreps){
                newText = newText.replace(findreps[i].find, findreps[i].rep);
            }
            return newText;
    }	
    jQuery( "#dialog-form" ).dialog({
        autoOpen: false,
        height: 290,
        width: 220,
        modal: true,
        buttons: {
            "Importer": function() { 
               jQuery('div#container').showLoading(); 
               jQuery.ajax( {                                                                  
                    data: jQuery('#formOCS :input').serialize(), // get the form data
                    type: jQuery('#formOCS').attr('method'), // GET or POST
                    url: jQuery('#formOCS').attr('action'), // the file to call
                    beforeSubmit: function(){
                        
                    },
                    success: function(response) { // on success..
                        jQuery('div#container').html(response); // update the DIV
                        jQuery('div#container').hideLoading();
                    }                                                                                                
                });
                jQuery( this ).dialog( "close" );            
            },
            Annuler: function() {
                $( this ).dialog( "close" );
            }
        }/*,
        close: function() {
            allFields.val( "" ).removeClass( "ui-state-error" );
        }*/
    });
});


function affiche(element,element2){
	jQuery("#"+element).toggle();
	if (element2!=''){
		jQuery("#"+element2).toggle();
	}
}
function afficheSi(element,si){
	if (jQuery("#"+si).val()==0){
		jQuery("#"+element).toggle();
	}
}
function execPHP(page) {
	window.location.href=page;
    /*return false;*/
}
function SwitchToContent(contentName)
{
    if (contentName!='ocs.php'){
        jQuery('div#container').showLoading();
        jQuery("div#container").load(contentName, function() {
            jQuery('div#container').hideLoading();
        });	
    }else{
        jQuery( "#dialog-form" ).dialog( "open" );        
        /*jQuery('div#container').showLoading();
        jQuery("div#container").load(contentName, function() {
            jQuery('div#container').hideLoading();
        });*/
    }        
}

function changeEntite() {
	 var entite = jQuery("#listeEntite").val();
	 //jQuery("#listeEntite option[value='".entite."']").attr('selected','selected');
	 jQuery.post('resources/backend.php?entite=', { entite: entite}, function(ret){	
            //alert(ret);	
            if(ret==0){		
                var tabAlias = jQuery("#listeEntite option:selected").text();
                var alias = tabAlias.split('-');			   
                jQuery("#alias").val(jQuery.trim( alias[0] ));    
                changeEntiteInfo();    		        
            }
        });			 		
}
<!-- Autocomplete -->
jQuery(function() {	
	jQuery.fn.extend({
		 propAttr: $.fn.prop || $.fn.attr
		});	
	jQuery("#alias").autocomplete({
            source: function( request, response ) {
                var objData = {};
                objData = { alias_startsWith: request.term, maxRows: 10, featureClass: "P" };
                $.ajax({
                    url: "resources/backend.php?autocomplete=",
                    dataType: "json",//jsonp
                    data: objData,
                    //type: 'POST',
                    success: function( data ) {					
                        response( $.map( data, function( item ) {
                            //alert(item.alias+' *** '+item.id_entite);						
                            return {
                                label: item.alias + ' - ' + item.type + ' ' + item.nom + ' de ' + item.ville,
                                labelCourt: item.alias,							
                                value: function ()
                                {								
                                        $('#alias').val(item.id_entite);
                                        return item.alias;								
                                },
                                retour: item.id_entite
                            }
                            return data;
                        }));
                    }
                });
            },
            minLength: 2,		
         //define select handler  
        select: function( event, ui ) {								      
        	jQuery.post('resources/backend.php?entite=', { entite: ui.item.retour, alias: ui.item.labelCourt}, function(ret){	            	
        		if(ret==0 && ui.item){	
        			var entite = ui.item.retour;
        			var alias = ui.item.labelCourt;        			        		
        			jQuery('#listeEntite-button span').text(alias);        			        			     			    			 	
        			changeEntiteInfo();   
        		 }
			}); 				
         }  	    
	});
});
function closeTooltip() {
	jQuery("#alias").tooltip("close");
}

</script>
<style type="text/css">
.highslide-maximize {	
	position: absolute;	
	margin-left: 16px;		
	width: 12px;
	height: 8px;
	border: 2px solid #aaaaaa;
}
.highslide-restore {	
	position: absolute;		
	width: 5px;
	height: 8px;
	border: 2px solid #aaaaaa;
	/*border: 2px solid #ff0000;*/
}
.highslide-space {	
	position: absolute;	
	margin-left: 14px;
	width: 15px;
	height: 4px;	
	/*border: 2px solid #ff0000;*/
}
</style>
<!-- Menu Vertical !-->
<link rel="stylesheet" href="content/fancymenu/style.css" />
<script type="text/javascript" src="content/fancymenu/treeMenu.js"></script>
<?php

?>
</head>    
<body>
    <div id="idletimeout">
	En raison de votre inactivité, vous allez être déconnecté dans <span><!-- countdown place holder --></span>&nbsp;secondes. 
	<a id="idletimeout-resume" href="#">Cliquez ici pour continuer à naviguer sur ce site</a>.
</div>
<div class="highslide-html-content" id="highslide-html<?php echo $page;?>">
	<div class="highslide-header"></div>
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
<div class="highslide-html-content" id="highslide-html-ml">
	<div class="highslide-header">	
	</div>
	<div class="highslide-body">
		 <?php include('include/ml.html');?>		 
	</div>

    <div class="highslide-footer">
        <div>
            <span class="highslide-resize" title="Resize">
                <span></span>
            </span>
        </div>
    </div>
</div>
<div class="connexionMenu">
    <div style="position: absolute;top: 0;left: 0;margin: 0; padding: 0;" title="">		
            <img src="<?php  echo $GLOBALS['params']['appli']['image_appli_min']?>" alt="Aide" border="0"/>	
    </div>

    <div class="affichIdent">
        <table>
        <tr><td style="width: 16px;"><img title="Se déconnecter" style="cursor: pointer; display: inline;" onclick="execPHP('logout.php');" src="graphs/icons/logout.png" ></td>
        <td style="padding-left: 5px;"><?php echo "$resUser->prenom $resUser->nom";?></td></tr>
        <!--<tr><td colspan=2>
                <select name='identite' id="listeEntite" style="cursor: pointer; display: inline;padding-left: 5px;color: #fff;" title="">
                <option></option>
                <?php 
                /*foreach ($_SESSION['ENTITE'] as $tabEtab){
                        echo "<option value=\"$tabEtab[id_entite]\"";
                        if ($_SESSION['id_entite']==$tabEtab[id_entite]){echo " selected";}
                        echo ">$tabEtab[alias] - $tabEtab[type] $tabEtab[nom] de $tabEtab[ville]</option>";
                }*/
                ?>
                </select>
        </tr>-->
        <tr><td colspan="2">
                <div class="ui-widget">                    
                        <input id="alias" style="width: 125px;" value="<?php echo $_SESSION["ENTITE"][$_SESSION['id_entite']][alias];?>" title="Saisissez les premières lettres ou chiffres de la ville, du nom ou de l'identifiant de l'entité" onClick="closeTooltip();"/>	
                </div>	
        </td></tr>
        </table>
    </div>
</div>
<div class="contentEntite" style="<?php if (empty($_SESSION[titre]) || empty($_SESSION['id_entite'])){echo "display: none;";}?>" id="contentEntite">
	<div id="imgEntite"><img style=" padding-top: 10px;display: inline; margin:0; padding:0;" id="titreAdresse" src="graphs/icons/event20.png" title="<?php echo $_SESSION[titre_adresse];?>"></div>
	<div id="personnelEntite"><img style=" padding-top: 10px;display: inline; margin:0; padding:0;" id="titrePersonnel" src="graphs/icons/infoUser20.png" title="<?php echo $_SESSION[titre_personnel];?>"></div>	
        <div id="Network"><img style=" padding-top: 10px;display: inline; margin:0; padding:0;" id="titreNetwork" src="graphs/icons/infoUser20.png" title="<?php echo $_SESSION[titre_network];?>"></div>	
	<div  id="textEntite" style="display: inline; margin:0; padding:0;font-weight:bold;font-size: 12px;"><?php echo $_SESSION[titre];?></div>
</div>
<div id="container" name="container">	
	<h1><?php  echo $titre;?></h1>
		<h2><?php  echo $pageDescription;?></h2>	
		<?php if (!empty($_SESSION['message'])){echo "<p id=\"mess_err\">".$_SESSION['message']."</p>";};?>	
		<div id="help" title="Aide"><a href="#" onclick="return hs.htmlExpand(this, { contentId: 'highslide-html<?php echo $page;?>',headingText: 'Aide',preserveContent: false } )"></a></div>
	<div class="content">
		<p style="padding: 0;">Bienvenue !
		<br/>Nous sommes le <?php  echo $dateFR;?>
		<br/>Dernière connexion le <?php echo $_COOKIE[LAST_CO];?>
		</p>
		Liste de vos rappels en cours :
		<table style="margin: 0;padding:0;" cellspacing=0 cellpadding=0 width=100%>
		<tr><th class="barreTitre">Rappel</th><th style="padding-left:5px;" class="barreTitre">Description du rappel</th><th style="padding-left:5px;" class="barreTitre">Délai</th></tr>		
		</table>	
	</div>
</div>

<div class="panel">	
	<div class="navig">
            <ul>
                <li><a href="menu.php" title="Accueil">Accueil</a></li>                        
                <li><a href="#" onclick="return hs.htmlExpand(this, { contentId: 'highslide-html-ml',headingText: 'Mentions Légales' } )" title="Mentions Légales">Mentions Légales</a></li>										
            </ul>
	</div>
	<div style="clear:both;"></div>	
	<div class="columns">
            <div class="colleft">
                <h3>Outils</h3>
                <ul>		                   
                </ul>
            </div>	
            <div class="colright">
            <h3>Gestion</h3>
                <ul>								

                </ul>			
            </div>
	</div>
	<div style="clear:both;"></div>
</div>
<div id="dialog-form" title="Choisissez les éléments à importer" style="display: none;">
    <!--<p class="validateTips">All form fields are required.</p>--> 
    <form id="formOCS" method="post" action="ocs.php">
        <input type="checkbox" name="storage" id="storage" checked/>       
        <label for="drive">Disque</label><br/>        
        <input type="checkbox" name="monitor" id="monitor" checked/>       
        <label for="monitor">Ecran</label><br/>
        <input type="checkbox" name="printer" id="printer" checked/>       
        <label for="printer">Impression</label><br/>
        <input type="checkbox" name="soft" id="soft" />
        <label for="soft">Logiciels</label><br/>
        <input type="checkbox" name="memory" id="memory" checked/>       
        <label for="memory">Mémoire</label><br/>
        <input type="checkbox" name="drive" id="drive" checked/>               
        <label for="storage">Partition</label><br/>
        <input type="checkbox" name="net" id="net" checked/>       
        <label for="net">Réseau</label><br/>
        <input type="checkbox" name="sound" id="sound" checked/>       
        <label for="sound">Son</label><br/>                                
        <input type="checkbox" name="video" id="video" checked/>       
        <label for="video">Vidéo</label>               
    
    </form>
</div>
 <div id="main" style="height:1200px;">
 <div id="treeMenu">
  <h2></h2>
        <ul>
          <li><a href="menu.php" title="Accueil" class="entete">Accueil</a><div class="imageMenu" style="padding-top:3px; cursor: pointer;"><a href="menu.php"><img src="graphs/icons/home-24.png"></a></div></li> 
          <li><a href="#" title="Mon compte" onclick="javascript:SwitchToContent('main/account.php');" class="entete">Mon compte </a><div class="imageMenu" style="padding-top:3px; cursor: pointer;"><a href="#"><img src="graphs/icons/user-24.png" onclick="javascript:SwitchToContent('main/account.php');"></a></div></li>
          <li><a href="#" class="entete">Gestion</a><span class="nv1"></span>
            <div class="mainDiv">
              <ul>
                <?php 				
                $requete="SELECT id,detail,file,isDefault,img FROM zone WHERE affMenu=1 ORDER BY orderMenu;";
                $rec=$db->query($requete);
                while ($res=$rec->fetch(PDO::FETCH_ASSOC)){										
                    if ((int)$_SESSION['PERMS']['zone'][$res['id']] & LECTURE)
                    {
                        if($res['isDefault']==1){$_SESSION['ZONE_DEFAULT']=$res[id];$_SESSION['PAGE_DEFAULT']=$res[file];}
                        echo "<li><span class=\"nv2\"></span><a href=\"#\" title=\"$res[detail]\" onclick=\"javascript:SwitchToContent('main/$res[file]?id_zone=$res[id]&detail=".base64_encode($res[detail])."');\" class=\"ligne\">$res[detail]</a>";
                        if (!empty($res[img]) && @file_exists("graphs/icons/zone/".$res[img])){
                            echo "<div class=\"imageMenu\"><img src=\"graphs/icons/zone/$res[img]\"></div>";
                        }
                        echo "</li>";
                    }  
                }
                ?>    
            </div>
          </li>
          <li><a href="#" class="entete">Outils</a><span class="nv1"></span>
            <div class="mainDiv">
              <ul>
                   <li><a href="#" title="Mettre à jour les données à partir d'OCS" onclick="javascript:SwitchToContent('ocs.php');" class="ligne">Mise à jour OCS</a><div class="imageMenu"><img src="graphs/icons/ocs-24.png"></div></li>
                    						
                    <li class="sub"><a href="#" title="Agenda" onclick="return hs.htmlExpand(this, { src: 'content/wdCalendar/wdCalendar/calendar.php?awq=<?php echo $idForCal;?>&amp;zxs=<?php echo $typeForCal;?>',objectType: 'iframe', headingText: 'Mon agenda',width: 640,preserveContent: false });" class="ligne">Mon agenda</a><div class="imageMenu"><img src="graphs/icons/calendar-24.png"></div></li>
                    <li class="sub"><a href="#" title="Calculatrice" onclick="return hs.htmlExpand(this, { src: 'content/calc/index.html',objectType: 'iframe', headingText: 'Calculatrice',width: 330,preserveContent: false });"class="ligne">Calculatrice</a><div class="imageMenu"><img src="graphs/icons/cal-24.png"></div></li>										               
              </ul>
            </div>
          </li>
          <?php 
            if ($_SESSION['isAdmin']==1){
                echo '<li><a href="#" class="entete">Administration</a><span class="nv1"></span>
                    <div class="mainDiv">
                    <ul>';
                    echo '<li><span class="nv2"></span><a href="#" title="Modifier les utilisateurs, leurs permissions" onclick="javascript:SwitchToContent(\'main/users.php\');" class="ligne">Utilisateurs</a><div class="imageMenu"><img src="graphs/icons/users-24.png"></div></li>';
                    echo '<li><span class="nv2"></span><a href="#" title="Modifier les groupes, leurs permissions" onclick="javascript:SwitchToContent(\'main/groups.php\');" class="ligne">Groupes</a><div class="imageMenu"><img src="graphs/icons/groups-24.png"></div></li>';
                    echo '<li><span class="nv2"></span><a href="#" title="Créer les alertes" onclick="javascript:SwitchToContent(\'main/alertes.php\');" class="ligne">Logiciels sans alerte</a><div class="imageMenu"><img src="graphs/icons/applis-24.png"></div></li>';                                        
                    echo '<li><span class="nv2"></span><a href="#" title="Modifier les alertes" onclick="javascript:SwitchToContent(\'main/alertes.php?a=1\');" class="ligne">Logiciels</a><div class="imageMenu"><img src="graphs/icons/logiciels_alertes-24.png"></div></li>';
                    echo '<li><span class="nv2"></span><a href="#" title="Gérer les sercies accessibles par l\'IP" onclick="javascript:SwitchToContent(\'main/serviceIP.php\');" class="ligne">Services IP</a><div class="imageMenu"><img src="graphs/icons/ip_services-24.png"></div></li>';
                    echo '
                    </ul>
                    </div>
                    </li>';
            }
            ?>	                                
          <li><a href="logout.php" class="entete">Déconnexion</a><div class="imageMenu" style="padding-top:3px; cursor: pointer;"><a href="logout.php"><img src="graphs/icons/logout-24.png"></a></div></li>
        </ul>
 </div>  
 </div>
    <script src="content/timeout/src/jquery.idletimer.js" type="text/javascript"></script>
<script src="content/timeout/src/jquery.idletimeout.js" type="text/javascript"></script>
<script type="text/javascript">
$.idleTimeout('#idletimeout', '#idletimeout a', {
	idleAfter: 3000,	
	pollingInterval: 30,
	keepAliveURL: 'content/timeout/keepalive.php',
	serverResponseEquals: 'OK',
	onTimeout: function(){
		$(this).slideUp();
		window.location = "logout.php";
	},
	onIdle: function(){
		$(this).slideDown(); // show the warning bar
	},
	onCountdown: function( counter ){
		$(this).find("span").html( counter ); // update the counter
	},
	onResume: function(){
		$(this).slideUp(); // hide the warning bar
	}
});
function changeEntiteInfo(){
	SwitchToContent('main/<?php echo $_SESSION['PAGE_DEFAULT']?>?id_zone=<?php echo $_SESSION['ZONE_DEFAULT']?>');
    jQuery.ajax({
		url: "resources/backend.php?titre=",		
		success: function( data ) {	
			var tabData = data.split('@@');  
		    jQuery('#contentEntite').show();         
		    jQuery('#textEntite').html(tabData[0]);				
		    jQuery('#titreAdresse').attr("title",tabData[1]);
		    jQuery('#titrePersonnel').attr("title",tabData[2]);
                    jQuery('#titreNetwork').attr("title",tabData[3]);
		    //jQuery('#titreAdresse').attr("title","aaa");
		}        				       			
	});
}
</script>
</body>
</html>
