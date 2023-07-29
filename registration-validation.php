<?php

$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');

require_once('assets/init.php');
/*require_once($root.'/assets/includes/functions_one.php');*/

/*require_once($root.'/assets/includes/functions_two.php');
require_once($root.'/assets/includes/functions_three.php');*/
global $wo, $sqlConnect;




$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);


if(isset($_POST['action']) && $_POST['action']=="checkvalidation") {

	$field_name = $_POST['field_name'];
	$fieldval = $_POST['fieldval'];
	

	switch ($field_name) {
		case 'username':
			$Userpointquery = mysqli_query($sqlConnect, "SELECT count(*) as countval FROM  `Wo_Users` WHERE username='".$fieldval."'");
		
			$Result = mysqli_fetch_array($Userpointquery);

			if($Result['countval'] > 0) {
				echo "Username already exists!!";
			}
		break;

		case 'email':
			$Userpointquery = mysqli_query($sqlConnect, "SELECT count(*) as countval FROM  `Wo_Users` WHERE email='".$fieldval."'");
		
			$Result = mysqli_fetch_array($Userpointquery);

			if($Result['countval'] > 0) {
				echo "Email already exists!!";
			}
		break;
		
		default:
			# code...
			break;
			die;
	}
}

die;

?>