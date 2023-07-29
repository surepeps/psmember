<?php
global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);

$user_id = $_POST['user_id'];
$status=1;
$query_one = mysqli_query($sqlConnect, "UPDATE `Wo_Listing` SET `status` = {$status} WHERE `id` = {$user_id}");
	
	if($query_one){
		die;
	}else{
		die(" Error for updating ");
	}

?>