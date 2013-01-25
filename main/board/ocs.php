<?PHP
session_start();
$page="soft_adm.php";
$script="";
$titre="Titre page";
$pageDescription="Description page";
$pageHelp="";
if(!isset($_COOKIE["ID_UTILISATEUR"])){
	header("Location: ../index.php");
	exit;
}
include_once("../../header.inc.php");
include_once("../../include/functions.php");
include_once("../../include/protect_var.php");
//entete_page($GLOBALS['params']['appli']['title_appli']." - ".$titre,'');
?>
<style type="text/css">


</style>
<script type="text/javascript">
</script>
</head>
<body>
<div class="highslide-html-content" id="highslide-html<?php echo $page;?>">
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
	<div class="content">
            <table class="tbl">
                <thead><tr><th>Zone</th><th>Nom du matériel</th><th>Marque</th><th>Modèle</th><th>Dernier inventaire</th><th>Alerte</th></tr></thead>
            <?php 
            connectSQL();
            //$requete="SELECT a.*,b.*,c.*,d.*,a.id as id_materiel,b.id as id_marque,b.detail as marque,c.detail as modele,d.detail as type_materiel,GROUP_CONCAT(ip SEPARATOR \"<br/>\") as ip,GROUP_CONCAT(e.id,'@@',ip SEPARATOR \"<br/>\") as id_net, a.id_type as id_type2,a.id_zone as id_zone2 FROM materiel a LEFT OUTER JOIN net e ON a.id=e.id_materiel, marque b, modele c, type d,soft e  WHERE a.id_modele=c.id AND c.id_marque=b.id AND a.id_type=d.id AND";                
            //$requete="SELECT a.*,a.nom as nom_logiciel,b.nom as nom_poste,GROUP_CONCAT(ip SEPARATOR \"<br/>\") as ip,c.detail as modele, d.detail as marque FROM soft a,modele c, marque d, materiel b LEFT OUTER JOIN net e ON b.id=e.id_materiel WHERE a.id_materiel=b.id AND b.id_entite=:id_entite AND b.id_zone=1 AND b.id_modele=c.id AND c.id_marque=d.id AND a.alerte_adm>0 AND a.alerte_adm<5 ORDER BY a.alerte_adm;";
            $requete="SELECT a.nom,a.inventorOn, c.detail as modele, d.detail as marque,b.detail as zone,
                IF(a.inventorOn<CURRENT_TIMESTAMP - INTERVAL 7 DAY,
                IF(a.inventorOn>CURRENT_TIMESTAMP - INTERVAL 1 MONTH,3,
                IF(a.inventorOn<CURRENT_TIMESTAMP - INTERVAL 1 MONTH,4,NULL)),NULL) as alerte
                FROM materiel a,zone b,modele c, marque d WHERE a.id_entite=:id_entite AND a.id_zone=b.id AND a.id_modele=c.id AND c.id_marque=d.id AND a.inventorId>0 ORDER BY zone,a.inventorOn,a.nom";
            $prep=$db->prepare($requete);
            $prep->bindParam(":id_entite",$_SESSION['id_entite'],PDO::PARAM_INT);	
            $prep->execute();    
            while($lignes=$prep->fetch(PDO::FETCH_ASSOC)){    
                if ($lignes[zone]!=$zoneSave){
                    echo "<tr><th colspan=6 style=\"background-color: #2F5880;\">$lignes[zone]</th></tr>";
                }
                echo "<tr><td>$lignes[zone]</td><td>$lignes[nom]</td><td>$lignes[marque]</td><td>$lignes[modele]</td><td>$lignes[inventorOn]</td><td style=\"text-align: center;\">";
                if ($lignes[alerte]==3){
                    echo '<div title="Dernier inventaire effectué il y a plus d\'une semaine" style="background-color: #FF8F29; width: 10px; height:10px;display: inline-block ;cursor: help;">&nbsp;</div>&nbsp;';
                }else  if ($lignes[alerte]==4){
                    echo '<div title="Dernier inventaire effectué il y a plus d\'un mois" style="background-color: #FF0000; width: 10px; height:10px;display: inline-block ;cursor: help;">&nbsp;</div>&nbsp;';               
                }else{
                     echo $lignes[alerte];
                }
                echo "</td></tr>";
                $zoneSave=$lignes[zone];
            } 		            
            ?>														
            </table>		
	</div>
</div>
</body>
</html>