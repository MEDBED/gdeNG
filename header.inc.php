<?php
mb_internal_encoding("UTF-8");
$GLOBALS['params']['bdd'] = array(
		'db_host' => 'localhost',
		'db_port' => '3306',
		'db_name' => 'gdeNG',
		'db_user' => 'gdeNG',
		'db_pass' => 'gde',
		'db_type' => 'mysql',		
);
$GLOBALS['params']['appli'] = array(
		'version' => '0.1',
		'allow_user_create' => 0,//active le bouton "créer un compte" sur le page d'accueil
		'titre_haut' => 'Gestion des Etablissements',
		'titre_bas' => 'de l\'Académie de Dijon',
		'image_appli' => 'graphs/logo/image_appli.png',
		'image_appli_min' => 'graphs/logo/image_appli_64.png',
		'logo_appli' => 'graphs/logo/logo_appli.png',
		'logo_right' => 'graphs/logo/logo_right.png',
		'title_appli' => 'GDE NG',
		'root_folder' => '/gdeNG',
                'document_folder' => '/usr/share/gdeNG',
		'proto'	=> 'https',
		'exp_mail' => '',
		'exp_name' => 'GDE',
		'smtp_host' => '',
		'url' => ''	,
		'key' => 'sdfsd879PlçIkdlf8744s',
		'ldap_host' => '',
		'ldap_port' => '389',
		'ldap_basedn' => 'ou=ac-dijon,ou=education,o=gouv,c=fr'	,
                //Pensez à modifier le fichier content/jtable/validationEngine/jquery.validationEngine-fr pour la correspondance des extensions autorisées 
                'extensionOk' => array('jpg','jpeg','png','bmp','tiff','gif','pdf','doc','odt','xls','xlt','pps','ppt','txt'),
                'extensionImgOk' => array('jpg','jpeg','png','bmp','tiff','gif')
);
$GLOBALS['ocs'][0]['bdd']= array(                
                'db_host' => 'localhost',
                'db_port' => '3306',
		'db_name' => 'ocsweb',
		'db_user' => 'ocsweb',
		'db_pass' => 'ocsweb',
		'db_type' => 'mysql',                   
);
$GLOBALS['ocs'][0]['tag']= array(                
                'horus' => 'Serveur Administratif',
                'p' => 'Serveur Pédagogique',
		'a' => 'Parc Administratif',
		'mp' => 'Parc Pédagogique',
		'amon' => 'Pare-feu',
);
$GLOBALS['MESSAGES'] = array(
		'sql_co' => 'Erreur d\'accès à la BDD',		
		'sql_ok' => 'Enregistrement effectué',
		'sql_nok' => 'Erreur pendant l\'enregistrement',		
);
$GLOBALS['rules'] = array();
//Droits appliqués, type bit bashing
define("LECTURE", 0x01);
define("MODIFICATION", 0x02);
define("CREATION", 0x04);
define("SUPPRESSION", 0x08);
?>
