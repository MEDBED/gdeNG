<?php
//session_start();
function clean_var($var,$check){
	global $key,$source;  		
	if ($check=='s' || empty($check)){		
		$var=filter_var($var, FILTER_SANITIZE_STRING);//nettoyage des var de type string
	}
	if ($check=='i'){		
		$var=filter_var($var, FILTER_VALIDATE_INT);//nettoyage des var de type int
	}
	if ($check=='f'){		
		$var=filter_var($var, FILTER_VALIDATE_FLOAT);//nettoyage des var de type float
	}
	if ($check=='sql'){			
		if(ctype_digit($var)){
			$var = intval($var);			
		}else{                  // Pour tous les autres types
			if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()){
				
				$var = stripslashes($var);				
			}
			$var = mysql_real_escape_string($var);
			$var = addcslashes($var, '%');
		}
	}
	return $var;
}
function connectSQL(){
	/*$db=@mysql_connect($GLOBALS['params']['bdd']['db_host'],$GLOBALS['params']['bdd']['db_user'],$GLOBALS['params']['bdd']['db_pass']);
	@mysql_select_db($GLOBALS['params']['bdd']['db_name'],$db);	
	@mysql_query("SET NAMES 'utf8'");*/
	global $db;		
	/*foreach ($_POST as $key => $valeur){
		$_POST[$key]=clean_var($valeur,'sql');		
	}
	foreach ($_GET as $key => $valeur){
		$_GET[$key]=clean_var($valeur,'sql');
	}*/
	$arrExtraParam= array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
	try{
		$db = new PDO('mysql:host='.$GLOBALS['params']['bdd']['db_host'].';port='.$GLOBALS['params']['bdd']['db_port'].';dbname='.$GLOBALS['params']['bdd']['db_name'], $GLOBALS['params']['bdd']['db_user'], $GLOBALS['params']['bdd']['db_pass'],$arrExtraParam);		
	}
	catch(Exception $e){
		echo 'Erreur d\'accès à la base de données!';
		die();
	}
	return $db;
}
function entete_page($title,$path) {
	if (!empty($path)){$addS="/";}else{$addS='';}
	echo ("<!DOCTYPE html public \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
   \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
	<html xmlns=\"http://www.w3.org/1999/xhtml\">
	<head>
	<script type=\"text/javascript\"></script>	
	<META http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
	<META http-equiv=\"Cache-Control\" content=\"no-cache\">
	<META name=\"Author\" content=\"Guillaume VERNET\">	        
	<LINK rel=\"stylesheet\" type=\"text/css\" href=\"".$path.$addS."content/connexion.css\">\n
	<link rel=\"Stylesheet\" type=\"text/css\" href=\"".$path.$addS."content/jquery-ui/css/start/jquery-ui-1.8.6.custom.css\" />
	<title>$title</title>	
	");
}
function my_old($dob, $now = false){
	if (!$now) $now = date('d-m-Y');
	$dob = explode('-', $dob);
	$now = explode('-', $now);
	$old = $now[2]*12+$now[1]-$dob[2]*12-$dob[1]-($dob[0]>$now[0] ? 1 : 0);
	return array('an' => floor($old / 12), 'mois' => $old % 12);
}
function Cryptage($MDP, $Clef){
	$LClef = strlen($Clef);
	$LMDP = strlen($MDP);	
	if ($LClef < $LMDP){
		$Clef = str_pad($Clef, $LMDP, $Clef, STR_PAD_RIGHT);
	}
	elseif ($LClef > $LMDP){
		$diff = $LClef - $LMDP;
		$_Clef = substr($Clef, 0, -$diff);
	}		
	return $MDP ^ $Clef; // La fonction envoie le texte crypt�
		
}
function chiffre($texte, $cle)
{
	$alg = MCRYPT_RIJNDAEL_256;
	//$td = mcrypt_create_iv(mcrypt_get_iv_size($alg, MCRYPT_MODE_ECB), MCRYPT_RAND);
	$td = mcrypt_module_open(MCRYPT_RIJNDAEL_256, "", MCRYPT_MODE_ECB, "");
	mcrypt_generic_init($td, $cle, "00000000000000000000000000000000");
	$temp = @mcrypt_generic($td, $texte);
	mcrypt_generic_deinit ($td);
	return$temp;
}
function dechiffre($texte, $cle)
{
	$alg = MCRYPT_RIJNDAEL_256;
	$td = mcrypt_module_open(MCRYPT_RIJNDAEL_256, "", MCRYPT_MODE_ECB, "");
	mcrypt_generic_init($td, $cle, "00000000000000000000000000000000");
	$temp = @mdecrypt_generic($td, $texte);
	mcrypt_generic_deinit ($td);
	return $temp;
}
function hex2bin($h)
{
	if (!is_string($h)) return null;
	$r='';
	for ($a=0; $a<strlen($h); $a+=2) {
		$r.=chr(hexdec($h{$a}.$h{($a+1)}));
	}
	return $r;
}
function genererMDP ($longueur = 8){
	// initialiser la variable $mdp
	$mdp = "";

	// D�finir tout les caract�res possibles dans le mot de passe,
	// Il est possible de rajouter des voyelles ou bien des caract�res sp�ciaux
	$possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";

	// obtenir le nombre de caract�res dans la cha�ne pr�c�dente
	// cette valeur sera utilis� plus tard
	$longueurMax = strlen($possible);

	if ($longueur > $longueurMax) {
		$longueur = $longueurMax;
	}

	// initialiser le compteur
	$i = 0;

	// ajouter un caract�re al�atoire � $mdp jusqu'� ce que $longueur soit atteint
	while ($i < $longueur) {
		// prendre un caract�re al�atoire
		$caractere = substr($possible, mt_rand(0, $longueurMax-1), 1);

		// v�rifier si le caract�re est d�j� utilis� dans $mdp
		if (!strstr($mdp, $caractere)) {
			// Si non, ajouter le caract�re � $mdp et augmenter le compteur
			$mdp .= $caractere;
			$i++;
		}
	}

	// retourner le r�sultat final
	return $mdp;
}
function sessionValide(){
	global $db,$row;
	// G�n�ration d'une clef de cryptage
	$caracteres = array("a", "b", "c", "d", "e", "f", 0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
	$caracteres_aleatoires = array_rand($caracteres, 8);
	$clef_activation = "";
		                	 
	foreach($caracteres_aleatoires as $i){
		$clef_activation .= $caracteres[$i];
	}
	
	$_SESSION["UNIQID"]=$clef_activation;
	$_SESSION["pageSize"]=20;
	$_SESSION["ID_USER"]=$row[id];
	//Permissions
	$requete="SELECT id_source,source,perms FROM perms WHERE id_u_or_g=$row[id] AND type='u'";
	$rec=$db->query($requete);
	if ($rec){
		while ($res=$rec->fetch(PDO::FETCH_ASSOC)){
			$tabPerms[$res[source]][$res[id_source]]=(int)$res[perms];
		}
	}
	$_SESSION["PERMS"]=$tabPerms;
	//Entit�s	
	$reqRne="SELECT a.id as id_entite,id_type,alias,nom,ville,adresse,tel,fax,cp,email,detail as type,directeur,adjoint,gestionnaire FROM entite a, type b WHERE a.id_type=b.id and b.source='etab' ORDER BY alias";
    $recRne=$db->query($reqRne);
	if ($recRne){
    	while ($resRne=$recRne->fetch(PDO::FETCH_ASSOC)){
    		if ((int)$_SESSION['PERMS']['entite'][$resRne['id_type']] & LECTURE){
	        	$tabRne[$resRne['id_entite']][id_entite]=$resRne['id_entite'];
	            $tabRne[$resRne['id_entite']][alias]=$resRne['alias'];
	            $tabRne[$resRne['id_entite']][id_type]=$resRne['id_type'];
	            $tabRne[$resRne['id_entite']][type]=$resRne['type'];
	            $tabRne[$resRne['id_entite']][nom]=$resRne['nom'];
	            $tabRne[$resRne['id_entite']][ville]=$resRne['ville'];
	            $tabRne[$resRne['id_entite']][adresse]=$resRne['adresse'];
	            $tabRne[$resRne['id_entite']][cp]=$resRne['cp'];
	            $tabRne[$resRne['id_entite']][tel]=$resRne['tel'];
	            $tabRne[$resRne['id_entite']][fax]=$resRne['fax'];
	            $tabRne[$resRne['id_entite']][email]=$resRne['email'];
	            $tabRne[$resRne['id_entite']][directeur]=$resRne['directeur'];
	            $tabRne[$resRne['id_entite']][adjoint]=$resRne['adjoint'];
	            $tabRne[$resRne['id_entite']][gestionnaire]=$resRne['gestionnaire'];
    		}
		}	
	}
	$_SESSION["ENTITE"]=$tabRne;	
}
?>