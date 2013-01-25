<?php
if(!isset($_COOKIE["ID_UTILISATEUR"])){
	header("Location: ../logout.php");
	exit;
}
include_once("../header.inc.php");
include_once("../include/functions.php");
include_once("../include/protect_var.php");

$file_path=clean_var($_GET['file'],'s');
if (empty($_POST['filename'])){
	$filename=basename($path);
}else{
	$filename=clean_var($_POST['filename'],'s');
}
$path = $GLOBALS['params']['appli']['document_folder'].'/files/'.$file_path; // the file made available for download via this PHP file
$mm_type="application/octet-stream"; // modify accordingly to the file type of $path, but in most cases no need to do so
$imginfo = @getimagesize($path);

header("Content-type: $imginfo[mime]");
if ($_GET[resize]==1){
    $sw = $imginfo[0];
    $sh = $imginfo[1];

    //dest size
    $dSize = $_GET[size];

    //find smallerst part and get needed scale and offset
    $yOff = 0;
    $xOff = 0;
    if($sw < $sh) {
      $scale = $dSize / $sw;
      $yOff = $sh/2 - $dSize/$scale/2; 
    } else {
      $scale = $dSize / $sh;
      $xOff = $sw/2 - $dSize/$scale/2; 
    }

    $im = @ImageCreateFromJPEG ($path) or // Read JPEG Image
    $im = @ImageCreateFromPNG ($path) or // or PNG Image
    $im = @ImageCreateFromGIF ($path) or // or GIF Image
    $im = false; // If image is not JPEG, PNG, or GIF

    if (!$im) {
      // We get errors from PHP's ImageCreate functions...
      // So let's echo back the contents of the actual image.
      readfile ($path);
    } else {
      // Create the resized image destination
      $thumb = @ImageCreateTrueColor ($dSize,$dSize);
      // Copy from image source, resize it, and paste to image destination
      imagecopyresampled($thumb, $im, 
        0, 0, 
        $xOff,$yOff,
        $dSize, $dSize, 
        $dSize / $scale ,$dSize / $scale);
    }
    header('content-type:image/jpeg');
    imagejpeg($thumb);
}else{
    readfile($path); // outputs the content of the file
}


exit();