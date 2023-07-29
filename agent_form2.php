<?php
global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);



  $count = count($_FILES['proimg']['name']);
  $user_id = $_POST['user_id'];

print_r($_POST);


 for($i=0;$i<$count;$i++){

	$row_id = $_POST['row_id'][$i];

	 if(! $_POST['row_id'][$i]){
	  	$row_id = 0;
	  }
	 		 $images_name = $_FILES['proimg']['name'][$i];
			$tmp_name = $_FILES['proimg']['tmp_name'][$i];
			$path = $_SERVER['DOCUMENT_ROOT'].'/themes/wowonder/criteria_images/'.$images_name;
				if($images_name){
				$move = move_uploaded_file($tmp_name, $path);
				}
			
			$propertyaddress = $_POST['propertyaddress'][$i];
			$pro_users =  $_POST['pro-users'][$i];
			$proPrice = $_POST['proPrice'][$i];
			$prp_date =  $_POST['prp_date'][$i];


	$query = mysqli_query($sqlConnect,"SELECT * FROM `Wo_Recent_listing` WHERE id= $row_id");


			if($query->num_rows>0){

					if($images_name){

			 		//echo "UPDATE `Wo_Recent_listing` SET `user_id`='$user_id',`images`='$images_name',`address`='$propertyaddress',`role`='$pro_users',`price`='$proPrice',`date`='$prp_date' WHERE `id` = {$row_id}";

			 		$query_one = mysqli_query($sqlConnect, "UPDATE `Wo_Recent_listing` SET `user_id`='$user_id',`images`='$images_name',`address`='$propertyaddress',`role`='$pro_users',`price`='$proPrice',`date`='$prp_date' WHERE `id` = {$row_id}");


				 	}else{

				 		//echo " image not found ";

				 		//echo "UPDATE `Wo_Recent_listing` SET `user_id`='$user_id',`address`='$propertyaddress',`role`='$pro_users',`price`='$proPrice',`date`='$prp_date' WHERE `id` = {$row_id}";

				 		$query_one = mysqli_query($sqlConnect, "UPDATE `Wo_Recent_listing` SET `user_id`='$user_id',`address`='$propertyaddress',`role`='$pro_users',`price`='$proPrice',`date`='$prp_date' WHERE `id` = {$row_id}");

				 	}
			}else{
				$query   = "INSERT INTO `Wo_Recent_listing`(`user_id`, `images`, `address`, `role`, `price`, `date`) VALUES ({$user_id},'{$images_name}','{$propertyaddress}','{$user_role}','{$proPrice}','{$prp_date}')";

				$sql_query = mysqli_query($sqlConnect, $query);
			}
}

 die;
?>
	
