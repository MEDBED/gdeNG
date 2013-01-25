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
                <tr><th>Nom du matériel</th><th>Marque</th><th>Modèle</th><th>Editeur</th><th>Nom du logiciel</th><th>Version</th><th>Alerte</th></tr>
            <?php 
            connectSQL();
            //$requete="SELECT a.*,b.*,c.*,d.*,a.id as id_materiel,b.id as id_marque,b.detail as marque,c.detail as modele,d.detail as type_materiel,GROUP_CONCAT(ip SEPARATOR \"<br/>\") as ip,GROUP_CONCAT(e.id,'@@',ip SEPARATOR \"<br/>\") as id_net, a.id_type as id_type2,a.id_zone as id_zone2 FROM materiel a LEFT OUTER JOIN net e ON a.id=e.id_materiel, marque b, modele c, type d,soft e  WHERE a.id_modele=c.id AND c.id_marque=b.id AND a.id_type=d.id AND";                
            //$requete="SELECT a.*,a.nom as nom_logiciel,b.nom as nom_poste,GROUP_CONCAT(ip SEPARATOR \"<br/>\") as ip,c.detail as modele, d.detail as marque FROM soft a,modele c, marque d, materiel b LEFT OUTER JOIN net e ON b.id=e.id_materiel WHERE a.id_materiel=b.id AND b.id_entite=:id_entite AND b.id_zone=1 AND b.id_modele=c.id AND c.id_marque=d.id AND a.alerte_peda>0 AND a.alerte_peda<5 ORDER BY a.alerte_peda;";
            $requete="SELECT a.*,a.nom as nom_logiciel,b.nom as nom_poste,c.detail as modele, d.detail as marque FROM soft a,modele c, marque d, materiel b WHERE a.id_materiel=b.id AND b.id_entite=:id_entite AND b.id_modele=c.id AND c.id_marque=d.id AND a.alerte_peda>0 AND a.alerte_peda<5 AND b.id_zone=2 ORDER BY a.alerte_peda DESC;";
            $prep=$db->prepare($requete);
            $prep->bindParam(":id_entite",$_SESSION['id_entite'],PDO::PARAM_INT);	
            $prep->execute();    
            while($lignes=$prep->fetch(PDO::FETCH_ASSOC)){               
                echo "<tr><td>$lignes[nom_poste]</td><td>$lignes[marque]</td><td>$lignes[modele]</td><td>$lignes[editeur]</td><td>$lignes[nom_logiciel]</td><td>$lignes[version]</td><td style=\"text-align: center;\">";
                if ($lignes[alerte_peda]==1){
                    echo'<div title="Aucun risque, contrôle de l\'installation" style="background-color: #339933; width: 10px; height:10px;display: inline-block ;cursor: help;">&nbsp;</div>&nbsp;';
                }else  if ($lignes[alerte_peda]==2){
                    echo'<div title="Risque modéré, installation inutile" style="background-color: #FFC629; width: 10px; height:10px;display: inline-block ;cursor: help;">&nbsp;</div>&nbsp;';
                }else  if ($lignes[alerte_peda]==3){
                    echo '<div title="Risque important, installation gênant le bon fonctionnement du poste" style="background-color: #FF8F29; width: 10px; height:10px;display: inline-block ;cursor: help;">&nbsp;</div>&nbsp;';
                }else  if ($lignes[alerte_peda]==4){
                    echo '<div title="Risque majeur, installation perturbant le poste voir tout le réseau informatique" style="background-color: #FF0000; width: 10px; height:10px;display: inline-block ;cursor: help;">&nbsp;</div>&nbsp;';               
                }else{
                     echo $lignes[alerte_peda];
                }
                echo "</td></tr>";
            } 		            
            ?>														
            </table>		
	</div>
</div>
</body>
</html>