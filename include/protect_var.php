<?php
if (isset($_SESSION['message'])){
	$message=clean_var($_SESSION['message'],'s');
}
/*if (!@mysql_ping()){		
	foreach ($_POST as $key => $valeur){
		$_POST[$key]=clean_var($valeur,'sql');	
	}
	foreach ($_GET as $key => $valeur){
		$_GET[$key]=clean_var($valeur,'sql');
	}		
}else{	
	foreach ($_POST as $key => $valeur){
		if (preg_match('/^id/',$key)){
			$_POST[$key]=clean_var($valeur,'i');
		}else{
			$_POST[$key]=clean_var($valeur,'s');
		}		
	}
	foreach ($_GET as $key => $valeur){
		if (preg_match('/^id/',$key)){
			$_GET[$key]=clean_var($valeur,'i');
		}else{
			$_GET[$key]=clean_var($valeur,'s');
		}
	}	
}*/
foreach ($_POST as $key => $valeur){
	if (preg_match('/^id/',$key)){
		$_POST[$key]=clean_var($valeur,'i');
	}else{
		$_POST[$key]=clean_var($valeur,'s');
	}
}
foreach ($_GET as $key => $valeur){
	if (preg_match('/^id/',$key)){
		$_GET[$key]=clean_var($valeur,'i');
	}else{
		$_GET[$key]=clean_var($valeur,'s');
	}
}
?>