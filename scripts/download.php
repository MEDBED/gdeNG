<?php
//session_start();
if(!isset($_COOKIE["ID_UTILISATEUR"])){
	header("Location: ../logout.php");
	exit;
}
include_once("../header.inc.php");
include_once("../include/functions.php");
include_once("../include/protect_var.php");
$finfo=finfo_open(FILEINFO_MIME_TYPE);
$file=$_POST['file'];
$path = $GLOBALS['params']['appli']['document_folder'].'/files/'.$file; // the file made available for download via this PHP file
//$mm_type="application/octet-stream"; // modify accordingly to the file type of $path, but in most cases no need to do so
$mm_type=finfo_file($finfo,$path);
if (empty($mm_type)){
    $mm_type="application/octet-stream";
}
finfo_close($finfo);
if (empty($_POST['filename'])){
	$filename=basename($path);
}else{
	$filename=$_POST['filename'];
}
//echo $filename.'--'.$path.'---'.$mm_type;
/*echo $path.'<br/>';
 print_r($_POST);
echo '<br/>';
print_r($_GET);
exit;*/
$extension=pathinfo($path,PATHINFO_EXTENSION);
$arrayOk=$GLOBALS['params']['appli']['extensionOk'];
if (in_array($extension,$GLOBALS['params']['appli']['extensionOk'])){
    //echo $filename.'--'.$path.'---'.$mm_type;exit;
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: public");
	header("Content-Description: File Transfer");
	header("Content-Type: " . $mm_type);
	header("Content-Length: " .(string)(filesize($path)) );
	header('Content-Disposition: attachment; filename="'.$filename.'"');
	header("Content-Transfer-Encoding: binary\n");
}
readfile($path); // outputs the content of the file
exit();
?>
