<?php

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);

$rating_val = $_POST['rating_val'];
$message = $_POST['message'];
$user_id = $_POST['user_id'];
$prop_id = $_POST['prop_id'];
$date = date('Y-m-d H:i:s');
$custom = "";

//$query   = "INSERT INTO Wo_Listing (`user_id`,`dtae_time`,`tab1`) VALUES ({$user_id},'{$date}','{$form_data}')";


$query   = "INSERT INTO Wo_Property_Review (`property_id`,`user_id`,`time`,`rating`,`comment`,`custom`) VALUES ({$prop_id},{$user_id},'{$date}',{$rating_val},'{$message}','{$custom}')";

//echo "INSERT INTO Wo_Property_Review (`property_id`,`user_id`,`time`,`rating`,`comment`,`	custom`) VALUES ({$prop_id},{$user_id},'{$date}',{$rating_val},'{$message}','{$custom}')";

$sql_query = mysqli_query($sqlConnect, $query);
echo "1";

die;

?>