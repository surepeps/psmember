<?php
$ds          = DIRECTORY_SEPARATOR;  //1
 
$storeFolder = '/uploads_images';   //2

if (!empty($_FILES)) {
     
    $tempFile = $_FILES['file']['tmp_name'];          //3    
      
    $targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;  //4
     
    $targetFile =  $targetPath. $_FILES['file']['name'];  //5
 
    move_uploaded_file($tempFile,$targetFile); //6
     
}

global $wo, $sqlConnect;
require_once('/var/www/html/codecanyon/Script/config.php');

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);

$property_id = $_POST['inserted_id'];

$first_img = array();

$first_img[] = $_FILES['file']['name'];

$serialize_images = serialize($first_img);

$query_one = "SELECT tab6 FROM `Wo_Listing` WHERE `id` = {$property_id}";
$sql       = mysqli_query($sqlConnect, $query_one);
$sql_fetch_one = mysqli_fetch_assoc($sql);

if(!empty($sql_fetch_one['tab6'])){
	$unserialize_data = unserialize($sql_fetch_one['tab6']);
	print_r($unserialize_data);
	$pre_data = array($_FILES['file']['name']);
	print_r($pre_data);
	$total_data = array_merge($unserialize_data, $pre_data);
	print_r($total_data);

	$multipel_serialize_images = serialize($total_data);

	$query = mysqli_query($sqlConnect, "UPDATE `Wo_Listing` SET `tab6` = '$multipel_serialize_images' WHERE `id` = {$property_id}");

	
}
else{
	$query = mysqli_query($sqlConnect, "UPDATE `Wo_Listing` SET `tab6` = '$serialize_images' WHERE `id` = {$property_id}");
	 
	if($query){
		die('ho gya');
	}else{
		die(" not updated ");
	}
}

?>    
<!-- 
$b=serialize($images_data);

	$c=unserialize($b);

	print_r($c);exit; -->
