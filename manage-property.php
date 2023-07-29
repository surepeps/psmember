<?php
global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');
/*require_once($root.'/config.php');
require_once($root.'/assets/init.php');
die;*/

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);

if(isset($_POST['action']) && $_POST['action']=="delete_property") {

	$property_id = $_POST['property_id'];
	
	$query = mysqli_query($sqlConnect,"DELETE FROM `Wo_Listing` WHERE id=".$property_id);

	if($query) {
		echo "Property Deleted Successfully!!";
	}
	die;
}


?>