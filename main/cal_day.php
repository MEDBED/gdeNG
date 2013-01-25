<?PHP
if(!isset($_COOKIE["ID_UTILISATEUR"])){
	header("Location: ../index.php");
	exit;
}
?>
<tr><td colspan=6 style="border-bottom: solid 2px #fff;margin: 0; padding: 0;"></td></tr>
<tr id="cal_<? echo $res['id'];?>">
<td style="vertical-align: middle;">
<input style="width: 25px;text-align: center;background-color: #7FA584;">Samedi ou dimanche<br/>
<input style="width: 25px;text-align: center;background-color: #86BAE8;">Jour férié
</td>
<td colspan=5>
	<?php 		
	$totalheuresMois=0;
	$totalrepasMois=0;
	$i=1;
	$tabTemp=explode('@@',$res['cal_info']);		
	foreach ($tabTemp as $info){
		$info=explode('#',$info);
		$tabInfo[$i]=array("heures"=>$info[1],"repas"=>$info[2],"conges"=>$info[3],"abs_enfant"=>$info[4],"abs_asm"=>$info[5]);	
		$i++;
	}	
	echo "<table align=\"center\" cellspacing=0 cellpadding=0>";		
	for($i=1; $i<=$NbJours; $i++) {
		if ($i==1 || $i==16){
			$L1="<tr><th style=\"border-left: solid 2px #fff;border-right: solid 2px #fff;padding-left: 3px;padding-right: 3px;text-align: center;";
			if ($i==1){
				$L1.="\"><img src=\"graphs/icons/erase.png\" onclick=\"eraseTable('$NbJours','$res[id]');\" style=\"cursor: pointer;\" title=\"Vider le tableau\">&nbsp;";
			}else{$L1.="border-top: solid 2px #fff;\">";}
			$L1.="Jour</th>";
			$L2="<tr><td style=\"border-left: solid 2px #fff;border-right: solid 2px #fff;border-top: solid 2px #fff;padding-left: 3px;padding-right: 3px;text-align: center;\">Heures</td>";
			$L3="<tr><td style=\"border-left: solid 2px #fff;border-right: solid 2px #fff;padding-left: 3px;padding-right: 3px;text-align: center;\">Repas</td>";
			$L4="<tr><td style=\"border-left: solid 2px #fff;border-right: solid 2px #fff;padding-left: 3px;padding-right: 3px;text-align: center;\">Congé</td>";
			$L5="<tr><td style=\"border-left: solid 2px #fff;border-right: solid 2px #fff;padding-left: 3px;padding-right: 3px;text-align: center;\">Absence enfant</td>";
			$L6="<tr><td style=\"border-left: solid 2px #fff;border-right: solid 2px #fff;padding-left: 3px;padding-right: 3px;text-align: center;\">Absence AM</td>";
		}
		$L1.="<th style=\"width: 20px;text-align: center;border-bottom: solid 2px #fff;font-weight: bold;\">$i</td>";		
		$DateJour = mktime(0, 0, 0, $Mois, $i, $Annee);
		$JourSemaine = date('D', $DateJour);
		if(!in_array(date("Y-m-d",$DateJour), $JoursFeries) && $JourSemaine != 'Sun' && $JourSemaine != 'Sat' ){
			$bgColor="";
			$ouvrable=1;
		}elseif (in_array(date("Y-m-d",$DateJour), $JoursFeries)){
			$bgColor="#86BAE8";//Jour férié
			$ouvrable=0;			
		}else{
			$bgColor="#7FA584";//Samedi ou dimanche
			$ouvrable=0;
		}		
		$L2.="<td style=\"width: 20px;text-align: center;\"><input id=\"heure_".$i."_".$res['id']."\" name=\"heure_$i\" value=\"";
		if (empty($res['cal_info'])){
			if ($ouvrable==1){
				$L2.=$nbHeureJour;$totalheuresMois+=$nbHeureJour;
			}else{$L2.="";}
		}else{$L2.=$tabInfo[$i]['heures'];$totalheuresMois+=$tabInfo[$i]['heures'];
		}
		$L2.="\" style=\"width: 25px;text-align: center;background-color: $bgColor;\"></td>";
		$L3.="<td style=\"width: 20px;text-align: center;\"><input id=\"repas_".$i."_".$res['id']."\" name=\"repas_$i\" value=\"";
		if (empty($res['cal_info'])){
			if ($ouvrable==1){
				$L3.=$resInfo[nb_repas];$totalrepasMois+=$resInfo[nb_repas];
			}else{$L3.="";
			}			
		}else{$L3.=$tabInfo[$i]['repas'];$totalrepasMois+=$tabInfo[$i]['repas'];
		}
		$L3.="\" style=\"width: 25px;text-align: center;background-color: $bgColor;\"></td>";	
		$L4.="<td style=\"width: 20px;text-align: center;\"><input id=\"conges_".$i."_".$res['id']."\" name=\"conges_$i\" value=\"";
		if (empty($res['cal_info'])){
			$L4.="";
		}else{$L4.=$tabInfo[$i]['conges'];$totalCongesMois+=$tabInfo[$i]['conges'];
		}
		$L4.="\" style=\"width: 25px;text-align: center;background-color: $bgColor;\"></td>";
		$L5.="<td style=\"width: 20px;text-align: center;\"><input id=\"abs_enfant_".$i."_".$res['id']."\" name=\"abs_enfant_$i\" value=\"";
		if (empty($res['cal_info'])){
			$L5.="";
		}else{$L5.=$tabInfo[$i]['abs_enfant'];$totalAbsEnfantMois+=$tabInfo[$i]['abs_enfant'];
		}
		$L5.="\" style=\"width: 25px;text-align: center;background-color: $bgColor;\"></td>";
		$L6.="<td style=\"width: 20px;text-align: center;\"><input id=\"abs_asm_".$i."_".$res['id']."\" name=\"abs_asm_$i\" value=\"";
		if (empty($res['cal_info'])){
			$L6.="";
		}else{$L6.=$tabInfo[$i]['abs_asm'];$totalAbsAsmMois+=$tabInfo[$i]['abs_asm'];
		}
		$L6.="\" style=\"width: 25px;text-align: center;background-color: $bgColor;\"></td>";		
		if ($i==15){
			$L1.="</tr>";$L2.="</tr>";$L3.="</tr>";$L4.="</tr>";$L5.="</tr>";$L6.="</tr>";
			$table=$L1.$L2.$L3.$L4.$L5.$L6;
			$L1="";$L2="";$L3='';$L4="";$L5="";$L6="";
		}elseif($i==$NbJours){
			$L1.="</tr>";$L2.="</tr>";$L3.="</tr>";$L4.="</tr>";$L5.="</tr>";$L6.="</tr>";
			$table.=$L1.$L2.$L3.$L4.$L5.$L6;
		}
	}
	echo $table;
	?>		
	</table>	
	</td></tr>
	<tr><td colspan=6 style="border-bottom: solid 2px #fff;margin: 0; padding: 0;"></td></tr>