<?php
global $wo, $sqlConnect;
$ds = DIRECTORY_SEPARATOR;

$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);
$storeFolder = 'themes/wondertag/uploads_images';
$uploadDir = 'themes/wondertag/uploads_docs';
$uploadDir_jpg = 'themes/wondertag/uploads_docs/converted_image';


$targetFilePath = "themes/wondertag/uploads_docs/pro_61577a5e7cc45_Go_Pro.pdf";
$h = Wo_convert_pdf_to_image($targetFilePath);

if($h != ""){
    echo $h; exit;
}else{
    echo "No"; exit;
}


