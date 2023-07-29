<?php
global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);
//print_r($_POST);
   
    $property_id = $_POST['property_id'];

    $imagepath = $_POST['imagepath'];



    $query = mysqli_query($sqlConnect,"SELECT * FROM `Wo_Listing` where id=$property_id");
    $row = mysqli_fetch_array($query);

    $tab6 = unserialize($row["tab6"]);

    foreach ($tab6 as $key => $value) {
        if($value==$imagepath)
            unset($tab6[$key]);
    }

    $tab6 = serialize( $tab6);
    $query = mysqli_query($sqlConnect, "UPDATE `Wo_Listing` SET `tab6` = '$tab6' WHERE `id` = {$property_id}");

    //echo "UPDATE `wo_Strastic_point` SET `offer`='$offer',`email`='$Email',`text`='$Text',`message`='$Messages',`add_listing`='$Add_Listing',`add_buyer`='$Add_Buyers',`featured_listing`='$Featured_Listing' where `id`=1 ";


die;

?>