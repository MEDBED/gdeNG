<?php
$page="pdf.php";
if(!isset($_COOKIE["ID_UTILISATEUR"])){
	header("Location: ../index.php");
	exit;
}
include_once("../header.inc.php");
include_once("../include/functions.php");
include_once("../include/protect_var.php");

require('../content/fpdf/fpdf.php');
$tabMois=array("01"=>"Janvier","02"=>"Février","03"=>"Mars","04"=>"Avril","05"=>"Mai","06"=>"Juin","07"=>"Juillet","08"=>"Août","09"=>"Septembre",10=>"Octbore",11=>"Novembre",12=>"Décembre");
connectSQL();
$req="SELECT a.*,date_format(a.date_contrat,'%d/%m/%Y') as date_contrat_fr,a.id as id_s,b.*,b.id as id_c,c.nom as nom_asm,c.prenom as prenom_asm,c.adresse as adresse_asm,c.ville as ville_asm,c.cp as cp_asm,e.num_secu,d.nom as nom_enfant,d.prenom as prenom_enfant,d.id_parent1,d.id_parent2
FROM contrat a, salaire b, user c, enfant d ,asm e
WHERE a.id='$_POST[id_contrat]' AND b.id='$_POST[id_salaire]' AND a.id_asm=c.id AND a.id_enfant=d.id AND a.id_asm=e.id_user";
$res=mysql_fetch_array(mysql_query($req));
class PDF extends FPDF
{
	// Page header
	function Header()
	{
		// Logo
		//$this->Image('logo.png',10,6,30);
		// Arial bold 15
		$this->SetFont('Times','B',15);
		// Move to the right
		$this->Cell(1);
		// Title
		$this->SetTextColor(70,68,178);
		$this->Cell(0,6,utf8_decode("BULLETIN DE PAIE \n"),0,1,'C');
		$this->Ln(10);		
	}

	// Page footer
	function Footer()
	{
		// Position at 1.5 cm from bottom
		$this->SetY(-15);
		// Arial italic 8
		$this->SetFont('Arial','I',8);
		$this->Cell(0,3,utf8_decode("* (1)-10% mensuel (2)-En une seule fois (3)-Lors de la prise des congés"),0,1,"R");		
		// Page end
		$this->SetFillColor(224,224,224);//gris
		$this->SetFont('Arial','B',8);
		$this->MultiCell(0,3,utf8_decode('Dans votre intérêt et pour faire valoir vos droits, il vous est recommandé de conserver ce bulletin sans limitation de durée.'),0,'C',1);
	}
}

// Instanciation of inherited class
$reqParent="SELECT nom, prenom, adresse,ville,cp,num_urssaf FROM user a, parent b WHERE a.id=b.id_user AND (a.id='$res[id_parent1]' OR a.id='$res[id_parent2]')";
$recParent=mysql_query($reqParent);
$nbParent=mysql_num_rows($recParent);
$parent=1;
while ($resParent=mysql_fetch_array($recParent)){
	$parents.=$resParent['nom'].' '.$resParent['prenom'];
	if ($nbParent>1 && $parent!=$nbParent){
		$parents.=" et ";
	}
	if (!empty($resParent['adresse'])){
		$adresse=$resParent['adresse']."\n".$resParent['cp']." ".$resParent['ville'];
	}
	if (!empty($resParent['num_urssaf'])){
		$urssaf=$resParent['num_urssaf'];
	}
	$parent++;
}
$parents.="\n".$adresse;
$parents.="\nN° URSSAF : ".$urssaf;
$parents.=$resParent['urssaf'];
$date=explode('-',$res['date']);
//Informations
$infos='';
$infos.="Période : ".$tabMois[$date[1]]." $date[0]\n";
if ($res['date_reglement']=='0000-00-00'){$res['date_reglement']='PAIEMENT NON EFFECTUE';}
$infos.="Date de paiement : $res[date_reglement]\n";
$infos.="Mode de paiement : $res[mode_reglement]\n";
$infos.="Emploi : $res[emploi]\n";
$infos.="Convention Coll. : Assistants Maternels du Particulier Employeur code NAF 88.91A\n";
$infos.="Contrat : $res[type_contrat]\n";
$infos.="Début du contrat : $res[date_contrat_fr]\n\n";
$infos.="Enfant : $res[nom_enfant] $res[prenom_enfant]\n";
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','B',10);
$pdf->SetFillColor(70,68,178);//violet foncé
$pdf->SetTextColor(255,255,255);
$ligne_depart = $pdf->GetY();
$col_depart = $pdf->GetX();
$pdf->Cell(94,5,'INFORMATIONS ',1,0,'C',1);
$pdf->SetXY(105,$ligne_depart);
$pdf->Cell(95,5,'EMPLOYEUR ',1,0,'C',1);
$pdf->SetXY(105,$ligne_depart+18);
$pdf->Cell(95,5,'SALARIE ',1,1,'C',1);
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Times','',8);
$pdf->SetXY($col_depart,$ligne_depart+5);
$pdf->MultiCell(94,3,utf8_decode($infos),1,'L');
$pdf->SetXY(105,$ligne_depart+5);
$pdf->MultiCell(95,3,utf8_decode($parents),1,'L');
$pdf->SetXY(105,$ligne_depart+23);
$pdf->SetFont('Times','',8);
$pdf->MultiCell(95,3,utf8_decode("$res[nom_asm] $res[prenom_asm]\n$res[adresse_asm]\n$res[cp_asm]$res[ville_asm]\nN° SECU : $res[num_secu]"),1,'L');
$pdf->Ln(10);
/*Planning*/
$pdf->SetFont('Times','B',8);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(0,5,utf8_decode("PLANNING ".$tabMois[$date[1]]." $date[0]"),1,1,'C',1);
$pdf->SetFont('Times','',8);
$pdf->SetTextColor(0,0,0);
$i=1;
$tabTemp=explode('@@',$res['cal_info']);
foreach ($tabTemp as $info){
	$info=explode('#',$info);
	$tabInfo[$i]=array("heures"=>$info[1],"repas"=>$info[2],"conges"=>$info[3],"abs_enfant"=>$info[4],"abs_asm"=>$info[5]);
	$i++;
}
$date=explode('-',$res['date']);
$Annee = $date[0];
$Mois = $date[1];
$dateSalaire = mktime( 0, 0, 0, $Mois, 1, $Annee );
$NbJours = date('t',$dateSalaire);
$PaquesDim = easter_date($Annee);
$PaquesLun = date("Y-m-d", ($PaquesDim + 24*3600));
$PentecoteLun = date("Y-m-d", ($PaquesDim + 50*24*3600));
$AscensionJeu = date("Y-m-d", ($PaquesDim + 39*24*3600));
$JoursFeries = array("$Annee-01-01", "$Annee-05-01", "$Annee-05-08", "$Annee-07-14",
		"$Annee-08-15", "$Annee-11-01", "$Annee-11-11", "$Annee-12-25", "$PaquesLun",
		"$AscensionJeu", "$PentecoteLun");

function affLigne($titre,$champ,$seq,$fillColor){
	global $tabInfo,$pdf,$res,$Mois,$Annee,$NbJours,$PaquesDim,$PaquesLun,$PentecoteLun,$AscensionJeu,$JoursFeries;
	if ($seq==1){$deb=1;$fin=15;}else{$deb=16;$fin=$NbJours;}	
	if (empty($champ)){
		$pdf->SetFillColor(189,188,242);//violet clair
		$pdf->SetFont('Times','B',8);
	}else{
		$pdf->SetFillColor(255,255,255);$pdf->SetFont('Times','',8);
	}
	$pdf->Cell(30,3,utf8_decode($titre),1,0,'C',1);	
	$pdf->SetFillColor(255,255,255);
	for ($i=$deb;$i<=$fin;$i++){		
		$DateJour = mktime(0, 0, 0, $Mois, $i, $Annee);
		$JourSemaine = date('D', $DateJour);
		if(!in_array(date("Y-m-d",$DateJour), $JoursFeries) && $JourSemaine != 'Sun' && $JourSemaine != 'Sat' ){
			$pdf->SetFillColor($fillColor);
		}elseif (in_array(date("Y-m-d",$DateJour), $JoursFeries)){
			$pdf->SetFillColor(134,186,232);//Jour férié
		}else{
			$pdf->SetFillColor(127,165,132);//Samedi ou dimanche
		}
		if ($i==15 || $i==$NbJours){
			if (empty($champ)){
				$pdf->SetFont('Times','B',8);
				$pdf->SetFillColor(189,188,242);//violet clair
				$pdf->Cell(10,3,$i,1,1,'C',1);
				$pdf->SetFillColor(255,255,255);
			}else{				
				$pdf->SetFont('Times','',8);
				$pdf->Cell(10,3,$tabInfo[$i][$champ],1,1,'C','1');
			}
		}else{
			if (empty($champ)){
				$pdf->SetFont('Times','B',8);
				$pdf->SetFillColor(189,188,242);//violet clair
				$pdf->Cell(10,3,$i,1,0,'C',1);
				$pdf->SetFillColor(255,255,255);
			}else{
				$pdf->SetFont('Times','',8);
				$pdf->Cell(10,3,$tabInfo[$i][$champ],1,0,'C',1);
			}
		}
	}
}
for ($seq=1;$seq<=2;$seq++){
	affLigne('Jour','',$seq,'255,255,255');
	affLigne('Heures','heures',$seq,'224,224,224');
	affLigne('Repas','repas',$seq,'255,255,255');
	affLigne('Congés','conges',$seq,'224,224,224');
	affLigne('Absence enfant','abs_enfant',$seq,'255,255,255');
	affLigne('Absence AM','abs_asm',$seq,'224,224,224');
}
/*Récapitulatif*/
$pdf->SetFont('Times','B',8);
$pdf->SetFillColor(70,68,178);//violet foncé
$pdf->SetTextColor(255,255,255);
$pdf->Cell(0,5,utf8_decode("RECAPITULATIF"),1,1,'C',1);
$pdf->SetFont('Times','',8);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->Cell(75,5,'Nombre d\'heures d\'accueil dans le mois',1,0,'',1);
$pdf->Cell(20,5,$res['heure_mois'],1,0,'C',1);
$pdf->Cell(75,5,utf8_decode('Heures mensualisées (total + supp)'),1,0,'',1);
$nb_heure_mois=($res['nb_sem_travail']/12)*$res['heures_gardes'];
$pdf->Cell(20,5,$nb_heure_mois+$res['heure_supp_mois'],1,1,'C',1);
$pdf->SetFillColor(224,224,224);//gris
$pdf->Cell(75,5,utf8_decode('Nombre d\'heures supplémentaires dans le mois'),1,0,'',1);
$pdf->Cell(20,5,$res['heure_supp_mois'],1,0,'C',1);
$pdf->Cell(75,5,utf8_decode('Heures hebdomadaire prévues au contrat'),1,0,'',1);
$pdf->Cell(20,5,$res['heures_gardes'],1,1,'C',1);
$pdf->SetFillColor(255,255,255);
$pdf->Cell(75,5,utf8_decode('Nombre d\'heures complémentaires dans le mois'),1,0,'',1);
$pdf->Cell(20,5,$res['heure_comp_mois'],1,0,'C',1);
$pdf->Cell(75,5,utf8_decode('Taux horaire net'),1,0,'',1);
$pdf->Cell(20,5,$res['salaire_horaire_net'],1,1,'C',1);
$pdf->SetFillColor(224,224,224);//gris
$pdf->Cell(75,5,'Nombre de jours d\'accueil dans le mois',1,0,'',1);
$pdf->Cell(20,5,$res['jour_mois'],1,0,'C',1);
$pdf->Cell(75,5,utf8_decode('Majoration heure supplémentaires net'),1,0,'',1);
$pdf->Cell(20,5,$res['salaire_heure_supp'],1,1,'C',1);
$pdf->SetFillColor(255,255,255);
$pdf->Cell(75,5,'Nombre de repas dans le mois',1,0,'',1);
$pdf->Cell(20,5,$res['repas_mois'],1,0,'C',1);
$pdf->Cell(75,5,'',1,0,'',1);
$pdf->Cell(20,5,'',1,1,'C',1);
$pdf->SetFillColor(224,224,224);//gris
$pdf->Cell(75,5,'Nombre de jours d\'absence',1,0,'',1);
$pdf->Cell(20,5,$res['jour_formation_asm'],1,0,'C',1);
$pdf->Cell(75,5,'',1,0,'',1);
$pdf->Cell(20,5,'',1,1,'C',1);
/*Details*/
$pdf->SetFont('Times','B',8);
$pdf->SetFillColor(70,68,178);//violet foncé
$pdf->SetTextColor(255,255,255);
$pdf->Cell(0,5,utf8_decode("DETAILS"),1,1,'C',1);
$pdf->SetFont('Times','B',8);
/*$pdf->SetFillColor(70,68,178);//violet foncé
$pdf->SetTextColor(255,255,255);*/
$pdf->SetFillColor(189,188,242);//violet clair
$pdf->SetTextColor(0,0,0);
$pdf->Cell(95,5,utf8_decode('Libellé'),1,0,'C',1);
$pdf->Cell(30,5,'Nombre ou base',1,0,'C',1);
$pdf->Cell(30,5,'Taux',1,0,'C',1);
$pdf->Cell(35,5,'Montant',1,1,'C',1);
$pdf->SetFont('Times','',8);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(224,224,224);//gris
$affCadre=1;
//
$pdf->Cell(95,5,utf8_decode("Salaire de base mensuel"),$affCadre,0);
$pdf->Cell(30,5,$res['nb_heures_mois'],$affCadre,0,'R');
$pdf->Cell(30,5,$res['salaire_horaire_net'],$affCadre,0,'R');
$pdf->Cell(35,5,number_format($res['salaire_base'],2,'.',' '),$affCadre,1,'R');
//
$pdf->Cell(95,5,utf8_decode("Heures complémentaires"),$affCadre,0,'L',1);
$pdf->Cell(30,5,$res['heure_comp_mois'],$affCadre,0,'R',1);
$pdf->Cell(30,5,$res['salaire_heure_comp'],$affCadre,0,'R',1);
$pdf->Cell(35,5,$res['salaire_comp'],$affCadre,1,'R',1);
//
$pdf->Cell(95,5,utf8_decode("Heures supplémentaires"),$affCadre,0);
$pdf->Cell(30,5,$res['heure_supp_mois'],$affCadre,0,'R');
$pdf->Cell(30,5,$res['salaire_heure_supp'],$affCadre,0,'R');
$pdf->Cell(35,5,$res['salaire_supp'],$affCadre,1,'R');
//
$pdf->Cell(95,5,utf8_decode("Indemnités mensuelles de congés payés"),$affCadre,0,'L',1);
if ($res['conges_payes']==3){
	$pdf->Cell(30,5,$res['conge_mois'],$affCadre,0,'R',1);
}else{
	$pdf->Cell(30,5,'',$affCadre,0,'R',1);
}
$pdf->Cell(30,5,$res['conges_payes']."*",$affCadre,0,'R',1);
$pdf->Cell(35,5,$res['salaire_conges_payes'],$affCadre,1,'R',1);
//
$pdf->Cell(95,5,utf8_decode("Absence enfant justifiée"),$affCadre,0,'L');
$pdf->Cell(30,5,$res['absence_enfant'],$affCadre,0,'R');
$pdf->Cell(30,5,"0.00",$affCadre,0,'R');
$pdf->Cell(35,5,$res['salaire_abscence_enfant'],$affCadre,1,'R');
//
$pdf->Cell(95,5,utf8_decode("Absence pour convenace personelle de l'AM"),$affCadre,0,'L',1);
$pdf->Cell(30,5,$res['jour_formation_asm'],$affCadre,0,'R',1);
$pdf->Cell(30,5,"0.00",$affCadre,0,'R',1);
$pdf->Cell(35,5,$res['salaire_abscence_asm'],$affCadre,1,'R',1);
//
$pdf->Cell(95,5,utf8_decode("Régularisation"),$affCadre,0,'L');
$pdf->Cell(30,5,'',$affCadre,0,'R');
$pdf->Cell(30,5,'',$affCadre,0,'R');
$pdf->Cell(35,5,$res['salaire_regul'],$affCadre,1,'R');
//Sous-Total 1
$pdf->SetFont('Times','B',8);
$pdf->SetFillColor(189,188,242);//violet clair
/*$pdf->Cell(95,5,"",$affCadre,0,'L',1);*/
$pdf->Cell(155,5,"Sous-Total 1",$affCadre,0,'R',1);
$pdf->Cell(35,5,$res['salaire_abscence_enfant']+$res['salaire_abscence_asm']+$res['salaire_conges_payes']+$res['salaire_supp']+$res['salaire_comp']+$res['salaire_base'],$affCadre,1,'R',1);
$pdf->SetFont('Times','',8);
$pdf->SetFillColor(224,224,224);//gris
//
$pdf->Cell(95,5,utf8_decode("Indemnités d'entretient"),$affCadre,0,'L');
$pdf->Cell(30,5,$res['jour_mois'],$affCadre,0,'R');
$pdf->Cell(30,5,$res['indemnite_entretien'],$affCadre,0,'R');
$pdf->Cell(35,5,$res['salaire_entretient'],$affCadre,1,'R');
//
$pdf->Cell(95,5,utf8_decode("Indemnités de repas"),$affCadre,0,'L',1);
$pdf->Cell(30,5,$res['repas_mois'],$affCadre,0,'R',1);
$pdf->Cell(30,5,$res['indemnite_repas'],$affCadre,0,'R',1);
$pdf->Cell(35,5,$res['salaire_repas'],$affCadre,1,'R',1);
//
$pdf->Cell(95,5,utf8_decode("Indemnités kilométriques"),$affCadre,0,'L');
$pdf->Cell(30,5,$res['jour_mois'],$affCadre,0,'R');
$pdf->Cell(30,5,$res['indemnite_kilometrique'],$affCadre,0,'R');
$pdf->Cell(35,5,$res['salaire_kilometre'],$affCadre,1,'R');
//Sous-Total 2
$pdf->SetFont('Times','B',8);
$pdf->SetFillColor(189,188,242);//violet clair
/*$pdf->Cell(95,5,"",$affCadre,0,'L',1);*/
$pdf->Cell(155,5,"Sous-Total 2",$affCadre,0,'R',1);
$pdf->Cell(35,5,$res['salaire_entretient']+$res['salaire_repas']+$res['salaire_kilometre'],$affCadre,1,'R',1);
$pdf->SetFont('Times','',8);
$pdf->SetFillColor(224,224,224);//gris
$pdf->Ln(10);
//Total
$affCadre=1;
$pdf->SetX(85);
$pdf->SetFont('Times','B',8);
$pdf->SetFillColor(70,68,178);//violet foncé
$pdf->SetTextColor(255,255,255);
$pdf->Cell(15,5,"",0,0,'C');
$pdf->Cell(30,5,utf8_decode("Semaines travaillés"),$affCadre,0,'C',1);
$pdf->Cell(30,5,utf8_decode("Congés pris"),$affCadre,0,'C',1);
$pdf->Cell(40,5,utf8_decode("Salaire Net"),$affCadre,1,'C',1);
$pdf->SetFillColor(189,188,242);//violet clair
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Times','',8);
//
$reqConges="SELECT SUM(conge_mois) as total_conges,SUM(semaines_travaillees) as total_semaines,SUM(salaire_net) as total_salaire FROM salaire a, contrat b WHERE date>'01-01-$date[0]' AND date<'31-12-$date[0]' AND a.id_contrat=b.id AND b.id_enfant='$res[id_enfant]' AND b.id_asm='$res[id_asm]'";
$resConges=@mysql_fetch_array(@mysql_query($reqConges));
$pdf->SetX(85);
$pdf->SetFont('Times','I',7);
$pdf->Cell(15,5,"Cumul $date[0]",$affCadre,0,'L');
$pdf->Cell(30,5,$resConges['total_semaines'],$affCadre,0,'R');
$pdf->Cell(30,5,$resConges['total_conges'],$affCadre,0,'R');
$pdf->Cell(40,5,$resConges['total_salaire'],$affCadre,1,'R');
$pdf->SetFont('Times','',8);
$pdf->SetFillColor(224,224,224);//gris
$pdf->SetTextColor(0,0,0);
//
$pdf->SetX(85);
$pdf->Cell(15,5,"Mensuel",$affCadre,0,'L',1);
$pdf->Cell(30,5,$res['semaines_travaillees'],$affCadre,0,'R',1);
$pdf->Cell(30,5,$res['conge_mois'],$affCadre,0,'R',1);
$pdf->SetFont('Times','B',12);
$pdf->Cell(40,5,number_format($res['salaire_net'],2,'.',' ').' EUR',$affCadre,1,'R',1);
$pdf->SetFont('Times','',8);
/*
//
$pdf->Cell(90,5,"",1,0,'L');
$pdf->Cell(30,5,$res[''],1,0,'R');
$pdf->Cell(30,5,$res[''],1,0,'R');
$pdf->Cell(40,5,$res[''],1,1,'R');
*/
//$pdf->MultiCell(0,10,$reqParent);
$pdf->Output("salaire_no$res[id_s]_".$tabMois[$date[1]]."_$date[0]","I");
// enregistre le document test.PDF dans le répertoire local du serveur.
//$PDF->Output("test.PDF", "F");

// affiche le document test.PDF dans une iframe.
/*echo '
 <iframe src="test.PDF" width="100%" height="100%">
[Your browser does <em>not</em> support <code>iframe</code>,
or has been configured not to display inline frames.
You can access <a href="./test.PDF">the document</a>
via a link though.]</iframe>';*/
?>