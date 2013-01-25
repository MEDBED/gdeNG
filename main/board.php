<?PHP
session_start();
$page="board.php";
$script="scripts/.php";
$titre="Tableau de bord";
$pageDescription="Tableau de bord d'état de l'entité";
$pageHelp="";
if(!isset($_COOKIE["ID_UTILISATEUR"])){
	header("Location: ../index.php");
	exit;
}
include_once("../header.inc.php");
include_once("../include/functions.php");
include_once("../include/protect_var.php");
include("../include/check_perms.php");
entete_page($GLOBALS['params']['appli']['title_appli']." - ".$titre,'');
?>
<script type="text/javascript" src="content/highcharts/js/highcharts.js"></script>
<script type="text/javascript" src="content/highcharts/js/themes/gray.js"></script>
<script type="text/javascript" src="content/jquery.json-2.3.min.js"></script>
<script type="text/javascript" src="content/easytab/jquery.easytabs.min.js"></script>
<script type="text/javascript" src="content/easytab/jquery.hashchange.min.js"></script>
<script type="text/javascript" src="content/highslide/highslide-full.min.js"></script>
<script type="text/javascript" src="content/highslide/highslide-with-html.js"></script>
<script type="text/javascript" src="content/highslide/highslide.config.js" charset="utf-8"></script>
<link rel="stylesheet" type="text/css" href="content/highslide/highslide.css" />

<script type="text/javascript">
function affNbPoste(chart,title,page){
    //jQuery('#'+chart).toggle();
    //jQuery('.panel-container').toggle();
    var chart1; // globally available   
    //jQuery(document).ready(function() {
    //function affNbPoste(){
           chart1 = new Highcharts.Chart({
           chart: {
              renderTo: chart,
              type: 'column',
              events: {
                  load: requestData(page)
              }              
           },
           title: {
              text: title
           },
           xAxis: {           
              title: {
                 text: 'Type'
              }
           },
           yAxis: {
              title: {
                 text: 'Quantité'
              }
           }       
        });
    //});
    function requestData(page) {
        jQuery.ajax({
            url: 'scripts/stats/'+page+'.php',
            datatype: "json",
            success: function(donnees) {
                var obj = jQuery.parseJSON(donnees);            
                var categorie = new Array();                       
                for (i=0;i<obj.Records.length;i++){ 
                   if (categorie.indexOf(obj.Records[i].type) == -1){
                        categorie.push(obj.Records[i].type);
                    }
                }               
                chart1.xAxis[0].setCategories(categorie);
                for (i=0;i<obj.xAxis.length;i++){
                    //var nombre = new Array(); 
                    var nombre=[];
                    var systeme = obj.xAxis[i].detail1;                 
                    for (j=0;j<categorie.length;j++){ 
                        var find = 0;                  
                        jQuery.each(obj.Records, function(o, v) {                                 
                            //var test = v.type.search(categorie[j]);
                            //var newRow = "row" + j;                       
                            if (v.type.search(categorie[j]) != -1) {
                                if (v.detail.search(systeme) != -1) {                               
                                    nombre.push(parseInt(v.count));                                
                                    find = 1;
                                    return false;
                                }
                            }
                        });
                        if (find==0){
                            nombre.push(null);                                                 
                        }                    
                    }                 
                    //alert(nombre.toSource());
                    var series = {                   
                        id: 'series'+i,
                        name: systeme,
                        data: nombre
                    }                
                    chart1.addSeries(series);                
                    //chart1.series[i].setData(nombre);                
                }                        

            },
            cache: false
        });
    }    
} 
    var normalLeft = 0;
    var normalRight = 0;		
    var normalWidth = 0;
    var normalHeight = 0;	
    hs.graphicsDir = 'content/highslide/graphics/';
    hs.outlineType = 'rounded-white';
    hs.showCredits = false;
    hs.registerOverlay({		
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
    
jQuery(document).ready(function() {
    jQuery('#tab-container').easytabs({
        //collapsedByDefault: true,
        animate: false,
        tabActiveClass: "selected-tab",
        panelActiveClass: "displayed"
    });    
    affNbPoste('charts4','Répartition des alertes cumulées','nb_alertes');
    
 });
</script>
<link rel="stylesheet" type="text/css" href="content/easytab/easytab.css">
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
</head>
<body>
<!--<div class="highslide-html-content" id="highslide-html<?php echo $page;?>">
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
</div>-->

<div id="container2">
    <div id="titleNetwork">
            <?php
            if (!empty($_SESSION['titre_network'])){
                echo "<img src=\"graphs/icons/inf.png\">&nbsp;".$_SESSION['titre_network'];
            }
            ?>
            </div>
	<h1><?php  echo $titre;?></h1>            
            <h2><?php  echo $pageDescription;?></h2>                
            <div id="mess" style="display: none;"></div>
		<!--<div id="help" title="Aide"><a href="#" onclick="return hs.htmlExpand(this, { contentId: 'highslide-html<?php echo $page;?>',headingText: 'Aide',preserveContent: false } )"></a></div>-->
	<!--<div class="content">-->
            <?php 
            connectSQL();	
            $requete="SELECT count(DISTINCT a.nom) as total,                
                COUNT(IF(a.alerte_adm=1,1,NULL)) as alerte_adm_1,
                COUNT(IF(a.alerte_adm=2,1,NULL)) as alerte_adm_2,
                COUNT(IF(a.alerte_adm=3,1,NULL)) as alerte_adm_3,
                COUNT(IF(a.alerte_adm=4,1,NULL)) as alerte_adm_4
                FROM soft a,materiel b WHERE a.id_materiel=b.id AND b.id_entite=:id_entite AND b.id_zone=1;";
            $prep=$db->prepare($requete);
            $prep->bindParam(":id_entite",$_SESSION['id_entite'],PDO::PARAM_INT);	
            $prep->execute();    
            $rowAdm = $prep->fetch(PDO::FETCH_ASSOC); //Sur une même ligne ...
            $requete="SELECT count(DISTINCT a.nom) as total,  
                COUNT(IF(a.alerte_adm=1,1,NULL)) as alerte_adm_1,
                COUNT(IF(a.alerte_adm=2,1,NULL)) as alerte_adm_2,
                COUNT(IF(a.alerte_adm=3,1,NULL)) as alerte_adm_3,
                COUNT(IF(a.alerte_adm=4,1,NULL)) as alerte_adm_4,
                COUNT(IF(a.alerte_peda=1,1,NULL)) as alerte_peda_1,
                COUNT(IF(a.alerte_peda=2,1,NULL)) as alerte_peda_2,
                COUNT(IF(a.alerte_peda=3,1,NULL)) as alerte_peda_3,
                COUNT(IF(a.alerte_peda=4,1,NULL)) as alerte_peda_4
                FROM soft a,materiel b WHERE a.id_materiel=b.id AND b.id_entite=:id_entite AND b.id_zone=2;";
            $prep=$db->prepare($requete);
            $prep->bindParam(":id_entite",$_SESSION['id_entite'],PDO::PARAM_INT);	
            $prep->execute();    
            $rowPeda = $prep->fetch(PDO::FETCH_ASSOC); //Sur une même ligne ...
            $requete="SELECT count(DISTINCT a.nom) as total,   
                COUNT(IF(a.alerte_adm=1,1,NULL)) as alerte_adm_1,
                COUNT(IF(a.alerte_adm=2,1,NULL)) as alerte_adm_2,
                COUNT(IF(a.alerte_adm=3,1,NULL)) as alerte_adm_3,
                COUNT(IF(a.alerte_adm=4,1,NULL)) as alerte_adm_4,
                COUNT(IF(a.alerte_peda=1,1,NULL)) as alerte_peda_1,
                COUNT(IF(a.alerte_peda=2,1,NULL)) as alerte_peda_2,
                COUNT(IF(a.alerte_peda=3,1,NULL)) as alerte_peda_3,
                COUNT(IF(a.alerte_peda=4,1,NULL)) as alerte_peda_4
                FROM soft a,materiel b WHERE a.id_materiel=b.id AND b.id_entite=:id_entite AND b.id_zone NOT IN (1,2);";
            $prep=$db->prepare($requete);
            $prep->bindParam(":id_entite",$_SESSION['id_entite'],PDO::PARAM_INT);	
            $prep->execute();    
            $rowSrv = $prep->fetch(PDO::FETCH_ASSOC); //Sur une même ligne ...
            $requete="SELECT COUNT(a.id) as total,
                COUNT(IF(a.inventorId>0,1,NULL)) as totalInventor,
                COUNT(IF(a.inventorId>0,IF(a.inventorOn>CURRENT_TIMESTAMP - INTERVAL 7 DAY,1,NULL),NULL)) as alerte_ok,
                COUNT(IF(a.inventorId>0,IF(a.inventorOn<CURRENT_TIMESTAMP - INTERVAL 7 DAY,IF(a.inventorOn>CURRENT_TIMESTAMP - INTERVAL 1 MONTH,1,NULL),NULL),NULL)) as alerte_hebdo,
                COUNT(IF(a.inventorId>0,IF(a.inventorOn<CURRENT_TIMESTAMP - INTERVAL 7 DAY,IF(a.inventorOn<CURRENT_TIMESTAMP - INTERVAL 1 MONTH,1,NULL),NULL),NULL)) as alerte_mois 
                FROM materiel a WHERE a.id_entite=:id_entite;";
            $prep=$db->prepare($requete);
            $prep->bindParam(":id_entite",$_SESSION['id_entite'],PDO::PARAM_INT);	
            $prep->execute();    
            $rowOcs = $prep->fetch(PDO::FETCH_ASSOC); //Sur une même ligne ...
            $requete="SELECT COUNT(a.id) as total,               
                COUNT(IF(a.date>=DATE_SUB(NOW(), INTERVAL 1 DAY),1,NULL)) as alerte_jour,
                COUNT(IF(a.date<DATE_SUB(NOW(), INTERVAL 1 DAY),IF(a.date>=DATE_SUB(NOW(), INTERVAL 7 DAY),1,NULL),NULL)) as alerte_hebdo,
                COUNT(IF(a.date<DATE_SUB(NOW(), INTERVAL 7 DAY),IF(a.date>=DATE_SUB(NOW(), INTERVAL 1 MONTH),1,NULL),NULL)) as alerte_mois 
                FROM suivi a,materiel b WHERE a.id_source=b.id AND b.id_entite=:id_entite;";
            $prep=$db->prepare($requete);
            $prep->bindParam(":id_entite",$_SESSION['id_entite'],PDO::PARAM_INT);	
            $prep->execute();    
            $rowSuivi = $prep->fetch(PDO::FETCH_ASSOC); //Sur une même ligne ...           
            $requete="SELECT COUNT(a.id) as total,               
                COUNT(IF(free<=(total*0.06),IF(free>(total*0.03),1,NULL),NULL)) as warning,
                COUNT(IF(free<=(total*0.03),1,NULL)) as critical
                FROM drive a,materiel b WHERE a.total!=0 AND a.id_materiel=b.id AND b.id_entite=:id_entite;";
            $prep=$db->prepare($requete);
            $prep->bindParam(":id_entite",$_SESSION['id_entite'],PDO::PARAM_INT);	
            $prep->execute();    
            $rowDrive = $prep->fetch(PDO::FETCH_ASSOC); //Sur une même ligne ...
            ?>		
            <div class="formLeftBoard">
                <!--<div id="charts" style="width: 100%; height: 400px"></div>-->               
                <div id="tab-container" class="tab-side-container">
                    <ul class="tab-side-container">
                      <!--<li><a href="#empty"></a></li>-->
                      <li><a class="signup" onclick="affNbPoste('charts4','Répartition des alertes cumulées','nb_alertes');" data-target="#tab-alertes">Alertes</a></li>
                      <li><a class="signup" onclick="affNbPoste('charts1','Répartition des systèmes d\'exploitation','nb_poste');" data-target="#tab-se">SE</a></li>
                      <li><a class="signup" onclick="affNbPoste('charts2','Répartition des types de matériel','nb_type');" data-target="#tab-type">Types de matériel</a></li>
                      <li><a class="signup" onclick="affNbPoste('charts3','Dernière remontée supérieure à 7 jours','nb_ocs');" data-target="#tab-ocs">Inventaire auto</a></li>
                    </ul>                    
                    <div class='panel-container' id="panel-container">
                        <!--<div id="empty"><div style="width: 750px; height: 252px;">Choisissez un graphique à afficher !</div></div>-->
                        <div id="tab-se" ><div id="charts1" style="width:90%;min-width: 745px; max-width: 1200px;height: 250px;"></div></div>
                        <div id="tab-type"><div id="charts2" style="width:90%;min-width: 745px; max-width: 1200px; height: 250px;"></div></div>
                        <div id="tab-ocs"><div id="charts3" style="width:90%;min-width: 745px; max-width: 1200px; height: 250px;"></div></div>
                        <div id="tab-alertes" ><div id="charts4" style="width:90%;min-width: 745px; max-width: 1200px; height: 250px;"></div></div>
                    </div>
               </div>
            </div>
            <div class="formRightBoard">                             
                <div id="pricing-table" class="clear">
                    <div class="plan">
                        <h3>Evénements saisis<span title="Nombe total d'événements de l'entité"><?php echo $rowSuivi['total']; ?></span></h3>
                        <a class="signup" href="main/board/suivi.php" onclick="return hs.htmlExpand(this, { 
                            objectType: 'ajax',
                            width: 850,
                            preserveContent: false,
                            headingText: 'GDE - Liste des événements&nbsp;&nbsp;<a href=\'#\' onclick=\'return hs.getExpander(this).printHtml()\' title=\'Imprimer\'>|Imp|</a>'
                        } )">Voir</a>         
                        <ul>
                            <?php
                            if ($rowSuivi['alerte_mois']>0){
                                echo '<li><b>Mois <div class="alerte" style="background-color:#339933;"></div></b>'.$rowSuivi['alerte_mois'].'</li>       ';
                            }
                            if ($rowSuivi['alerte_hebdo']>0){
                                echo '<li><b>Semaine <div class="alerte" style="background-color:#FF8F29;"></div></b>'.$rowSuivi['alerte_hebdo'].'</li>';
                            }
                            if ($rowSuivi['alerte_jour']>0){
                                echo '<li><b>Aujourd\'hui <div class="alerte" style="background-color:#FF0000;"></div></b>'.$rowSuivi['alerte_jour'].'</li>';
                            }                                                        
                            ?>
                        </ul> 
                    </div> 
                    <div class="plan">
                        <h3>Inventaire automatique<span title="Nombe de matériels inventoriés automatiquement parmis les <?php echo $rowOcs['total']; ?> matériels de l'entité"><?php echo $rowOcs['totalInventor']; ?></span></h3>
                        <a class="signup" href="main/board/ocs.php" onclick="return hs.htmlExpand(this, { 
                            objectType: 'ajax',
                            width: 850,
                            preserveContent: false,
                            headingText: 'GDE - Inventaire automatique&nbsp;&nbsp;<a href=\'#\' onclick=\'return hs.getExpander(this).printHtml()\' title=\'Imprimer\'>|Imp|</a>'
                        } )">Voir</a>         
                        <ul>
                            <?php
                            if ($rowOcs['alerte_ok']>0){
                                echo '<li><b>Cette semaine <div class="alerte" style="background-color:#339933;"></div></b><?php echo '.$rowOcs['alerte_ok'].'</li>';
                            }
                           if ($rowOcs['alerte_hebdo']>0){
                                echo '<li><b>+ 1 semaine <div class="alerte" style="background-color:#FF8F29;"></div></b>'.$rowOcs['alerte_hebdo'].'</li>';
                            }if ($rowOcs['alerte_mois']>0){
                                echo '<li><b>+ 1 mois <div class="alerte" style="background-color:#FF0000;"></div></b>'.$rowOcs['alerte_mois'].'</li>';
                            }                                                        
                            ?>
                        </ul> 
                    </div> 
                    <div class="plan">
                        <h3>Logiciels administratifs<span title="Nombre de logiciels administratifs distincts installés dans l'entité"><?php echo $rowAdm['total']; ?></span></h3>
                        <a class="signup" href="main/board/soft_adm.php" onclick="return hs.htmlExpand(this, { 
                            objectType: 'ajax',
                            width: 850,
                            preserveContent: false,
                            headingText: 'GDE - Logiciels administratifs&nbsp;&nbsp;<a href=\'#\' onclick=\'return hs.getExpander(this).printHtml()\' title=\'Imprimer\'>|Imp|</a>'
                        } )">Voir</a>         
                        <ul>
                            <?php
                            if ($rowAdm['alerte_adm_1']>0){
                                echo '<li><b>Niveau <div class="alerte" style="background-color:#339933;">1</div></b>'.$rowAdm['alerte_adm_1'].'</li>';
                            }
                            if ($rowAdm['alerte_adm_2']>0){
                                echo '<li><b>Niveau <div class="alerte" style="background-color:#FFC629;">1</div></b>'.$rowAdm['alerte_adm_2'].'</li>';
                            }
                            if ($rowAdm['alerte_adm_3']>0){
                                echo '<li><b>Niveau <div class="alerte" style="background-color:#FF8F29;">1</div></b>'.$rowAdm['alerte_adm_3'].'</li>';
                            }
                            if ($rowAdm['alerte_adm_4']>0){
                                echo '<li><b>Niveau <div class="alerte" style="background-color:#FF0000;">1</div></b>'.$rowAdm['alerte_adm_4'].'</li>';
                            }
                            ?>
                        </ul> 
                    </div> 
                    <div class="plan">
                        <h3>Logiciels pédagogiques<span title="Nombre de logiciels pédagogiques distincts installés dans l'entité"><?php echo $rowPeda['total']; ?></span></h3>
                        <a class="signup" href="main/board/soft_peda.php" onclick="return hs.htmlExpand(this, { 
                            objectType: 'ajax',
                            width: 850,
                            preserveContent: false,
                            headingText: 'GDE - Logiciels pédagogiques&nbsp;&nbsp;<a href=\'#\' onclick=\'return hs.getExpander(this).printHtml()\' title=\'Imprimer\'>|Imp|</a>'
                        } )">Voir</a>         
                        <ul>
                            <?php
                            if ($rowPeda['alerte_peda_1']>0){
                                echo '<li><b>Niveau <div class="alerte" style="background-color:#339933;">1</div></b>'.$rowPeda['alerte_peda_1'].'</li>';
                            }
                            if ($rowPeda['alerte_peda_2']>0){
                                echo '<li><b>Niveau <div class="alerte" style="background-color:#FFC629;">1</div></b>'.$rowPeda['alerte_peda_2'].'</li>';
                            }
                            if ($rowPeda['alerte_peda_3']>0){
                                echo '<li><b>Niveau <div class="alerte" style="background-color:#FF8F29;">1</div></b>'.$rowPeda['alerte_peda_3'].'</li>';
                            }
                            if ($rowPeda['alerte_peda_4']>0){
                                echo '<li><b>Niveau <div class="alerte" style="background-color:#FF0000;">1</div></b>'.$rowPeda['alerte_peda_4'].'</li>';
                            }
                            ?>                            
                        </ul> 
                    </div> 
                    <div class="plan">
                        <h3>Logiciels serveurs<span title="Nombre de logiciels serveurs distincts installés dans l'entité"><?php echo $rowSrv['total']; ?></span></h3>
                        <a class="signup" href="main/board/soft_srv.php" onclick="return hs.htmlExpand(this, { 
                            objectType: 'ajax',
                            width: 850,
                            preserveContent: false,
                            headingText: 'GDE - Logiciels serveurs&nbsp;&nbsp;<a href=\'#\' onclick=\'return hs.getExpander(this).printHtml()\' title=\'Imprimer\'>|Imp|</a>'
                        } )">Voir</a>         
                        <ul>
                            <?php
                            if ($rowSrv['alerte_adm_1']>0){
                                echo '<li><b>Niveau <div class="alerte" style="background-color:#339933;">1</div></b>'.$rowSrv['alerte_adm_1'].'</li>';
                            }
                            if ($rowSrv['alerte_adm_2']>0){
                                echo '<li><b>Niveau <div class="alerte" style="background-color:#FFC629;">1</div></b>'.$rowSrv['alerte_adm_2'].'</li>';
                            }
                            if ($rowSrv['alerte_adm_3']>0){
                                echo '<li><b>Niveau <div class="alerte" style="background-color:#FF8F29;">1</div></b>'.$rowSrv['alerte_adm_3'].'</li>';
                            }
                            if ($rowSrv['alerte_adm_4']>0){
                                echo '<li><b>Niveau <div class="alerte" style="background-color:#FF0000;">1</div></b>'.$rowSrv['alerte_adm_4'].'</li>';
                            }
                            ?>                             
                        </ul> 
                    </div>                     
                    <div class="plan">
                        <h3>Disques & partitions<span title="Nombe de disques inventoriés dans l'entité"><?php echo $rowDrive['total']; ?></span></h3>
                        <a class="signup" href="main/board/drive.php" onclick="return hs.htmlExpand(this, { 
                            objectType: 'ajax',
                            width: 850,
                            preserveContent: false,
                            headingText: 'GDE - Espace libre&nbsp;&nbsp;<a href=\'#\' onclick=\'return hs.getExpander(this).printHtml()\' title=\'Imprimer\'>|Imp|</a>'
                        } )">Voir</a>         
                        <ul>          
                            <?php
                            if ($rowDrive['warning']>0){
                                echo '<li><b>Alerte <div class="alerte" style="background-color:#FF8F29;"></div></b>'.$rowDrive['warning'].'</li>';
                            }if ($rowDrive['critical']>0){
                                echo '<li><b>Critique <div class="alerte" style="background-color:#FF0000;"></div></b>'.$rowDrive['critical'].'</li>';
                            }
                            ?>                            
                        </ul> 
                    </div>                    
                </div>
            </div>               
                
	<!--</div>-->
</div>
</body>
</html>