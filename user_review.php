<?php

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);

$rating_val = $_POST['rating_val'];
$message = $_POST['message'];
$review_to = $_POST['review_to'];
$review_from = $_POST['review_from'];
$date = date('Y-m-d H:i:s');
$custom = "";

//print_r($_POST);



$query   = "INSERT INTO Wo_User_review (`review_to`,`rating`, `message`, `date`, `custom`,`review_from`) VALUES ({$review_to},{$rating_val},'{$message}','{$date}','{$custom}','{$review_from}')";

//echo "INSERT INTO Wo_User_review (`review_to`,`rating`, `message`, `date`, `custom`,`review_from`) VALUES ({$review_to},{$rating_val},'{$message}','{$date}','{$custom}','{$review_from}')";

$sql_query = mysqli_query($sqlConnect, $query);

die;

?>