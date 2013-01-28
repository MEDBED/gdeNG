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
	<!-- <script type="text/javascript" src="content/tooltip/jquery.tools.min.js"></script> -->
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
	jQuery("input").tooltip({
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
<link rel="stylesheet" type="text/css" media="screen,projection" href="content/Sticklr/style.css" />
<link rel="stylesheet" type="text/css" media="screen,projection" href="content/Sticklr/src/jquery-sticklr-1.4-light-color.css" />
<script type="text/javascript" src="content/Sticklr/src/jquery-1.4.3.min.js"></script>
<script type="text/javascript" src="content/Sticklr/src/jquery-sticklr-1.4.pack.js"></script>
<script type="text/javascript" src="content/Sticklr/src/jquery.localscroll-min.js"></script>
<link rel="stylesheet" type="text/css" href="content/showLoading/css/showLoading.css">
<script type="text/javascript" src="content/showLoading/js/jquery.showLoading.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
                $('#example-1').sticklr({
                        showOn		: 'click',
                        stickTo     : 'left'
                });
        $('#example-2').sticklr({
                        showOn		: 'hover',
                        stickTo     : 'right',
                        size        : 32
                });
                $('#example-3').sticklr({
                    animate     : true,
                    relativeTo  : 'top',
                        showOn		: 'hover',
                        stickTo     : 'right'
                });
                $.localScroll();
    });
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
</script>
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
                        <input id="alias" value="<?php echo $_SESSION["ENTITE"][$_SESSION['id_entite']][alias];?>" title="Saisissez les premières lettres ou chiffres de la ville, du nom ou de l'identifiant de l'entité" onClick="closeTooltip();"/>	
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
		<?php 
		$nbPb=0;				
		/*$db->query("SET lc_time_names = 'fr_FR';");		
		$req="SELECT id FROM user";
		$rec=$db->query($req);
		while ($res=$rec->fetch(PDO::FETCH_OBJ)){						
			$date=explode('-',$res->date);
			$Annee = $date[0];
			$Mois = $date[1];
			$jouReglement='2019-10-10';			
			if (date('Y-m-d')>date('Y-m-d',$jouReglement)){
				if ($nbPb%2 == 0){
					$color='#f7f7f7';
				}else{$color='#c7c7c7';
				}
				echo "<tr bgcolor=\"$color\" style=\"color: #000;\" onMouseOver=\"this.bgColor='#f4d99e'\" onMouseOut=\"this.bgColor='$color'\"><td>Non réglé</td>";
				echo "<td style=\"padding-left:5px;\">Bulletin de salaire n° $res->id</td>";
				echo "<td>Dépassé</td>";
				echo "<tr>";
				$nbPb++;
			}
			
		}	*/		
		?>
		</table>	
	</div>
</div>

<div class="panel">	
	<div class="navig">
		<ul>
			<li><a href="menu.php" title="Accueil">Accueil</a></li>
                        <li><a href="#" title="Mon compte" onclick="javascript:SwitchToContent('main/account.php');" class="link">Mon compte</a></li>
			<li><a href="#" onclick="return hs.htmlExpand(this, { contentId: 'highslide-html-ml',headingText: 'Mentions Légales' } )" title="Mentions Légales">Mentions Légales</a></li>				
			<li><a href="logout.php" title="Se déconnecter">Déconnexion</a></li>	
						
		</ul>
	</div>
	<div style="clear:both;"></div>	
	<div class="columns">
		<div class="colleft">
			<h3>Outils</h3>
			<ul>		
                            <li><a href="#" title="Mon compte" onclick="javascript:SwitchToContent('ocs.php');" class="link">Mise à jour OCS</a></li>
                            <?php 
                            if ($_SESSION['isAdmin']==1){
                                    echo '<li><a href="#" title="Modifier les utilisateurs, leurs permissions" onclick="javascript:SwitchToContent(\'main/users.php\');" class="link">Utilisateurs</a></li>';
                                    echo '<li><a href="#" title="Modifier les groupes, leurs permissions" onclick="javascript:SwitchToContent(\'main/groups.php\');" class="link">Groupes</a></li>';
                                    echo '<li><a href="#" title="Créer les alertes" onclick="javascript:SwitchToContent(\'main/alertes.php\');" class="link">Logiciels sans alerte</a></li>';                                        
                                    echo '<li><a href="#" title="Modifier les alertes" onclick="javascript:SwitchToContent(\'main/alertes.php?a=1\');" class="link">Logiciels</a></li>';
                                    echo '<li><a href="#" title="Gérer les sercies accessibles par l\'IP" onclick="javascript:SwitchToContent(\'main/serviceIP.php\');" class="link">Services IP</a></li>';
                            }
                            ?>							
                            <li class="sub"><a href="#" title="Agenda" onclick="return hs.htmlExpand(this, { src: 'content/wdCalendar/wdCalendar/calendar.php?awq=<?php echo $idForCal;?>&amp;zxs=<?php echo $typeForCal;?>',objectType: 'iframe', headingText: 'Mon agenda',width: 640,preserveContent: false });">Mon agenda</a></li>
                            <li class="sub"><a href="#" title="Calculatrice" onclick="return hs.htmlExpand(this, { src: 'content/calc/index.html',objectType: 'iframe', headingText: 'Calculatrice',width: 330,preserveContent: false });">Calculatrice</a></li>										
			</ul>
		</div>	
		<div class="colright">
		<h3>Gestion</h3>
			<ul>				
				<!-- <li><a href="#" title="Entité" onclick="javascript:SwitchToContent('main/entite.php');" class="link">Entité</a></li> -->
				<?php 				
				$requete="SELECT id,detail,file,isDefault FROM zone WHERE affMenu=1 ORDER BY orderMenu;";
				$rec=$db->query($requete);
				while ($res=$rec->fetch(PDO::FETCH_ASSOC)){										
                                    if ((int)$_SESSION['PERMS']['zone'][$res['id']] & LECTURE)
                                    {
                                        if($res['isDefault']==1){$_SESSION['ZONE_DEFAULT']=$res[id];$_SESSION['PAGE_DEFAULT']=$res[file];}
                                        echo "<li><a href=\"#\" title=\"Liste des matériels\" onclick=\"javascript:SwitchToContent('main/$res[file]?id_zone=$res[id]&detail=".base64_encode($res[detail])."');\" class=\"link\">$res[detail]</a></li>";
                                    }                                    
				?>
                                
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
    <div id="sticky">

        <ul id="example-1" class="sticklr">
            <li>
                <a href="#" class="icon-tag" title="Site switcher"></a>
                <ul>
                    <li class="sticklr-title">
                        <a href="#">Site Switcher</a>
                    </li>
                    <li>
                        <a href="http://themeforest.net/?ref=amatyr4n" class="icon-amazon">ActiveDen</a>
                    </li>
                    <li>
                        <a href="http://codecanyon.net/?ref=amatyr4n" class="icon-flickr">AudioJungle</a>
                    </li>
                    <li>
                        <a href="http://themeforest.net/?ref=amatyr4n" class="icon-facebook">ThemeForest</a>
                    </li>
                    <li>
                        <a href="http://codecanyon.net/?ref=amatyr4n" class="icon-google">VideoHive</a>
                    </li>
                </ul>
                <ul>
                    <li>
                        <a href="http://codecanyon.net/?ref=amatyr4n" class="icon-reddit">GraphicRiver</a>
                    </li>
                    <li>
                        <a href="http://themeforest.net/?ref=amatyr4n" class="icon-lastfm">3DOcean</a>
                    </li>
                    <li>
                        <a href="http://codecanyon.net/?ref=amatyr4n" class="icon-technorati">CodeCanyon</a>
                    </li>
                    <li>
                        <a href="http://themeforest.net/?ref=amatyr4n" class="icon-yahoo">Tuts+</a>
                    </li>
                    <li>
                        <a href="http://themeforest.net/?ref=amatyr4n" class="icon-dribbble">PhotoDune</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#" class="icon-zoom" title="Search"></a>
                <ul>
                    <li>
                        <form action="http://www.google.com/search" method="GET">
                            <input type="text" name="q" value="" placeholder="Type then press Enter.." />
                        </form>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#" class="icon-sitemap" title="Multiple-column ready"></a>
                <ul>
                    <li class="sticklr-title">
                        <a href="#">Multiple-column ready</a>
                    </li>
                    <li>
                        <a href="http://themeforest.net/?ref=amatyr4n" class="icon-amazon">ActiveDen</a>
                    </li>
                </ul>
                <ul>
                    <li>
                        <a href="http://codecanyon.net/?ref=amatyr4n" class="icon-flickr">AudioJungle</a>
                    </li>
                    <li>
                        <a href="http://themeforest.net/?ref=amatyr4n" class="icon-facebook">ThemeForest</a>
                    </li>
                </ul>
                <ul>
                    <li>
                        <a href="http://codecanyon.net/?ref=amatyr4n" class="icon-google">VideoHive</a>
                    </li>
                    <li>
                        <a href="http://codecanyon.net/?ref=amatyr4n" class="icon-reddit">GraphicRiver</a>
                    </li>
                </ul>
                <ul>
                    <li>
                        <a href="http://themeforest.net/?ref=amatyr4n" class="icon-lastfm">3DOcean</a>
                    </li>
                    <li>
                        <a href="http://codecanyon.net/?ref=amatyr4n" class="icon-technorati">CodeCanyon</a>
                    </li>
                </ul>
                <ul>
                    <li>
                        <a href="http://themeforest.net/?ref=amatyr4n" class="icon-yahoo">Tuts+</a>
                    </li>
                    <li>
                        <a href="http://themeforest.net/?ref=amatyr4n" class="icon-dribbble">PhotoDune</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#" class="icon-login" title="Sign-in"></a>
                <ul>
                    <li class="sticklr-title">
                        <a href="#">Form example</a>
                    </li>
                    <li>
                        <form action="http://www.google.com" method="POST">
                            <input type="text" name="username" value="" placeholder="username" />
                            <input type="password" name="password" value="" placeholder="password" />
                            <input type="submit" name="submit" value="Submit" />
                        </form>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#" class="icon-calendar" title="Calendar"></a>
                <ul>
                    <li class="sticklr-title">
                        <a href="#">Example use</a>
                    </li>
                    <li>
                        <table class="calendar">
                            <tr>
                                <td></td><td></td><td></td><td>1</td><td>2</td><td>3</td><td>4</td>
                            </tr>
                            <tr>
                                <td>5</td><td>6</td><td>7</td><td>8</td><td>9</td><td>10</td><td>11</td>
                            </tr>
                            <tr>
                                <td>12</td><td>13</td><td>14</td><td>15</td><td>16</td><td>17</td><td>18</td>
                            </tr>
                            <tr>
                                <td>19</td><td>20</td><td>21</td><td>22</td><td>23</td><td>24</td><td>25</td>
                            </tr>
                            <tr>
                                <td>26</td><td>27</td><td>28</td><td>29</td><td>30</td><td></td><td></td>
                            </tr>
                        </table>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#" class="icon-email" title="Notification"><span class="notification-count">3</span></a>
                <ul>
                    <li class="sticklr-title">
                        <a href="#">Notification (3)</a>
                    </li>
                    <li>
                        <form action="" method="POST">
                            <input type="text" name="search" value="" placeholder="Someone is typing.." />
                        </form>
                    </li>
                    <li>
                        <a href="#" class="icon-user">00:32 Hello!</a>
                    </li>
                    <li>
                        <a href="#" class="icon-user">23:52 Not really works</a>
                    </li>
                    <li>
                        <a href="#" class="icon-user">22:07 Just example use</a>
                    </li>
                </ul>
            </li>
        </ul>
        
        <ul id="example-3" class="sticklr">
            <li>
                <a href="#" class="icon-login" title="Site switcher"></a>
                <ul>
                    <li class="sticklr-title">
                        <a href="#">Show on hover</a>
                    </li>
                    <li>
                        <a href="http://themeforest.net/?ref=amatyr4n" class="icon-amazon">ActiveDen</a>
                    </li>
                    <li>
                        <a href="http://codecanyon.net/?ref=amatyr4n" class="icon-flickr">AudioJungle</a>
                    </li>
                    <li>
                        <a href="http://themeforest.net/?ref=amatyr4n" class="icon-facebook">ThemeForest</a>
                    </li>
                    <li>
                        <a href="http://codecanyon.net/?ref=amatyr4n" class="icon-google">VideoHive</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#" class="icon-user" title="Site switcher"></a>
                <ul>
                    <li class="sticklr-title">
                        <a href="#">Right-side panel</a>
                    </li>
                    <li><a href="#" class="icon-reddit" title="Liste des matériels" onclick="javascript:SwitchToContent('main/materiel.php?id_zone=1&detail=UGFyYyBBZG1pbmlzdHJhdGlm');">Matériels</a></li>
                    <li>
                        <a href="http://codecanyon.net/?ref=amatyr4n" class="icon-reddit">Documents</a>
                    </li>
                    <li>
                        <a href="http://themeforest.net/?ref=amatyr4n" class="icon-lastfm">3DOcean</a>
                    </li>
                    <li>
                        <a href="http://codecanyon.net/?ref=amatyr4n" class="icon-technorati">CodeCanyon</a>
                    </li>
                    <li>
                        <a href="http://themeforest.net/?ref=amatyr4n" class="icon-yahoo">Tuts+</a>
                    </li>
                    <li>
                        <a href="http://themeforest.net/?ref=amatyr4n" class="icon-dribbble">PhotoDune</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#" class="icon-twitter" title="Share"></a>
                <ul>
                    <li class="sticklr-title">
                        <a href="#">Sharing is caring</a>
                    </li>
                    <li>
                        <div class="sticklr-custom" style="padding:10px">
				        <div style="height:20px"><g:plusone size="medium"></g:plusone><script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script></div>
                        </div>
                    </li>
                    <li>
                        <div class="sticklr-custom" style="padding:10px">
				        <a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal"  style="float:none;">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
                        </div>
                    </li>
                    <li>
                        <div class="sticklr-custom" style="padding:10px">
				        <script type="text/javascript">(function(){var s = document.createElement("SCRIPT"), s1 = document.getElementsByTagName("SCRIPT")[0]; s.type = "text/javascript"; s.async = true; s.src = "http://widgets.digg.com/buttons.js"; s1.parentNode.insertBefore(s, s1);})();</script><a class="DiggThisButton DiggCompact"></a>
                        </div>
                    </li>
                    <li>
                        <div class="sticklr-custom" style="padding:10px">
				        <a href="http://twitter.com/amatyr4n" class="twitter-follow-button" data-show-count="false" style="height:20px">Follow @amatyr4n</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
                        </div>
                    </li>
                    <li>
                        <div class="sticklr-custom" style="padding:10px">
				        <iframe src="http://www.facebook.com/plugins/like.php?send=false&amp;layout=button_count&amp;show_faces=false&amp;action=like&amp;colorscheme=light" scrolling="no" frameborder="0" style="border:none; width:150px; height:20px;"></iframe>
                        </div>
                    </li>
                    <li>
                        <div class="sticklr-custom" style="padding:10px">
				        <script src="http://platform.linkedin.com/in.js" type="text/javascript"></script><script type="IN/Share" data-counter="right"></script>
                        </div>
                    </li>
                    <li>
                        <div class="sticklr-custom" style="padding:10px">
				        <su:badge layout="1"></su:badge><script type="text/javascript">(function(){var li = document.createElement("script"); li.type = "text/javascript"; li.async = true; li.src = "https://platform.stumbleupon.com/1/widgets.js"; var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(li, s);})();</script>
                        </div>
                    </li>
                </ul>
            </li>
            <li>
                <a href="https://www.google.com/search?q=sticklr+wp" class="icon-google" title="External link" target="_blank"></a>
            </li>
        </ul>
        
        <ul id="example-2" class="sticklr">
            <li>
                <a href="#testimonials" class="icon-networking32"></a>
                <ul>
                    <li class="sticklr-title">
                        <a href="#">Right-side panel</a>
                    </li>
                    <li>
                        <a href="http://codecanyon.net/?ref=amatyr4n" class="icon-reddit">GraphicRiver</a>
                    </li>
                    <li>
                        <a href="http://themeforest.net/?ref=amatyr4n" class="icon-lastfm">3DOcean</a>
                    </li>
                    <li>
                        <a href="http://codecanyon.net/?ref=amatyr4n" class="icon-technorati">CodeCanyon</a>
                    </li>
                    <li>
                        <a href="http://themeforest.net/?ref=amatyr4n" class="icon-yahoo">Tuts+</a>
                    </li>
                    <li>
                        <a href="http://themeforest.net/?ref=amatyr4n" class="icon-dribbble">PhotoDune</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#download" class="icon-product32" title="Download"></a>
            </li>
            <li>
                <a href="#features" class="icon-login32" title="Features"></a>
            </li>
            <li>
                <a href="#" class="icon-feed32" title="Search"></a>
                <ul>
                    <li class="sticklr-title">
                        <a href="#">Search</a>
                    </li>
                    <li>
                        <form action="http://www.google.com/search" method="GET">
                            <input type="text" name="q" value="" placeholder="Type then press Enter.." />
                        </form>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#testimonials" class="icon-heart32" title="Testimonials"></a> 
            </li>            
            <li>
                <a href="#page" class="icon-top32" title="Back to top"></a>
            </li>
        </ul>
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
