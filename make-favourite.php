<?php
global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');


$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);

if(isset($_POST['action']) && $_POST['action']=="make_favourite_property") {


	$userid = $_POST['user_id'];
	$property_id = $_POST['property_id'];
	$favourite = $_POST['favourite'];

	
	if($favourite==1) {

		$query = mysqli_query($sqlConnect,"INSERT INTO `Wo_Favourite_Peoperties` VALUES('',$property_id,$userid,$favourite,now(),now())");
	} else {
		$query = mysqli_query($sqlConnect,"DELETE FROM `Wo_Favourite_Peoperties` WHERE property_id= ".$property_id." AND user_id=".$userid);
	}

	if($query) {
		echo $favourite;
	}

}

die;

?>