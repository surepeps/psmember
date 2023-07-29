<?php
//print_r($_FILES);
error_reporting(E_ALL);
ini_set('display_errors', 1);

$name = $_FILES['file']['name'];
$tmp_name = $_FILES['file']['tmp_name'];
$path = $_SERVER['DOCUMENT_ROOT'].'/themes/wowonder/criteria_images/'.$name;

//echo $path;

$move = move_uploaded_file($tmp_name, $path);
if($move){
	die($name);
}
else{
	die(" not update");
}
?>


