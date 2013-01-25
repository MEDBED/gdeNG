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
                <thead><tr><th>Type</th><th>Nom du matériel</th><th>Marque</th><th>Modèle</th><th></th></tr></thead>
            <?php 
            connectSQL();
            //$requete="SELECT a.*,b.*,c.*,d.*,a.id as id_materiel,b.id as id_marque,b.detail as marque,c.detail as modele,d.detail as type_materiel,GROUP_CONCAT(ip SEPARATOR \"<br/>\") as ip,GROUP_CONCAT(e.id,'@@',ip SEPARATOR \"<br/>\") as id_net, a.id_type as id_type2,a.id_zone as id_zone2 FROM materiel a LEFT OUTER JOIN net e ON a.id=e.id_materiel, marque b, modele c, type d,soft e  WHERE a.id_modele=c.id AND c.id_marque=b.id AND a.id_type=d.id AND";                
            //$requete="SELECT a.*,a.nom as nom_logiciel,b.nom as nom_poste,GROUP_CONCAT(ip SEPARATOR \"<br/>\") as ip,c.detail as modele, d.detail as marque FROM soft a,modele c, marque d, materiel b LEFT OUTER JOIN net e ON b.id=e.id_materiel WHERE a.id_materiel=b.id AND b.id_entite=:id_entite AND b.id_zone=1 AND b.id_modele=c.id AND c.id_marque=d.id AND a.alerte_adm>0 AND a.alerte_adm<5 ORDER BY a.alerte_adm;";
            $requete="SELECT a.nom,f.detail as suivi, c.detail as modele, d.detail as marque,b.detail as zone,e.detail as type,f.date,CONCAT (g.prenom,' ',g.nom) as user,
                IF(f.date<=CURRENT_TIMESTAMP - INTERVAL 1 DAY,
                IF(f.date>CURRENT_TIMESTAMP - INTERVAL 7 DAY,3,
                IF(f.date>CURRENT_TIMESTAMP - INTERVAL 1 MONTH,4,NULL)),1) as alerte
                FROM materiel a,zone b,modele c, marque d,type e, suivi f, user g WHERE a.id_entite=:id_entite AND a.id_zone=b.id AND a.id_modele=c.id AND c.id_marque=d.id AND f.id_source=a.id AND a.id_type=e.id AND f.id_user=g.id AND f.date>CURRENT_TIMESTAMP - INTERVAL 1 MONTH ORDER BY zone,f.date DESC,a.nom";
            $prep=$db->prepare($requete);
            $prep->bindParam(":id_entite",$_SESSION['id_entite'],PDO::PARAM_INT);	
            $prep->execute();    
            while($lignes=$prep->fetch(PDO::FETCH_ASSOC)){    
                if ($lignes[zone]!=$zoneSave){
                    echo "<tr><th colspan=5 style=\"background-color: #2F5880;\">$lignes[zone]</th></tr>";
                }
                echo "<tr title=\"$lignes[suivi]\" style=\"background-color: #ECF2F6;\"><td>$lignes[type]</td><td>$lignes[nom]</td><td>$lignes[marque]</td><td>$lignes[modele]</td><td style=\"text-align: center;\">";
                if ($lignes[alerte]==4){
                    echo'<div title="Information ajouté dans le mois" style="background-color: #339933; width: 10px; height:10px;display: inline-block ;cursor: help;">&nbsp;</div>&nbsp;';                
                }else  if ($lignes[alerte]==3){
                    echo '<div title="Information ajouté dans la semaine" style="background-color: #FF8F29; width: 10px; height:10px;display: inline-block ;cursor: help;">&nbsp;</div>&nbsp;';
                }else  if ($lignes[alerte]==1){
                    echo '<div title="Information ajouté ce jour" style="background-color: #FF0000; width: 10px; height:10px;display: inline-block ;cursor: help;">&nbsp;</div>&nbsp;';               
                }else{
                     echo $lignes[alerte];
                }
                echo "</td></tr>";
                echo "<tr style=\"background-color: #fff;\"><td colspan=5><div style=\"width:600px; color: #000;margin: auto;padding: 3px;border: dotted 1px #000;\">$lignes[suivi]<div style=\"right: 0; bottom: 0;font-style: italic;text-align: right;\">Ajouté le $lignes[date] par $lignes[user]</div></div></td></tr>";
                $zoneSave=$lignes[zone];
            } 		            
            ?>														
            </table>		
	</div>
</div>
</body>
</html>