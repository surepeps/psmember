<?php

$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');

require_once('assets/init.php');
global $wo, $sqlConnect;


$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);

if(isset($_POST['action']) && $_POST['action']=="update_package") {

    $userid = $_POST['userid'];

    $query_one = mysqli_query($sqlConnect, "UPDATE `Wo_Users` SET `pro_type`='1' WHERE `user_id`=".$userid); 

    if($query_one)
        echo "Successfully Updated!!";

    die;

} 
die;

?>