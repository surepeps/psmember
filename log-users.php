<?php
global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php'); 

$data = (array)json_decode($wo['user']['custom_data']);
	
if($_POST['action']=="check"){

	
	if($wo['user']['pro_type'] > 0){
		$modules_limit = $wo['pro_packages'][$wo['pro_packages_types'][$wo['user']['pro_type']]][$_POST['module']];

		if($data["month"]==date("m")){  //there is record
			  
				  if(empty($data[$_POST['module']."_usage"])){
					  echo $modules_limit;
				  }else{

					 if($modules_limit>$data[$_POST['module']."_usage"]){
						 echo ($modules_limit-$data[$_POST['module']."_usage"]);

					 }else{
						 echo "over_limit";
			
					}
				  }
		}else{
					  echo $modules_limit;
		}
	}else{
		 echo "over_limit";
	}
	//$wo['pro_packages'][$wo['pro_packages_types'][$wo['user']['pro_type']]]['posts_promotion']
	die();
} 

if($_POST['action']=="log"){
		
			if($data["month"]==date("m")){  //there is record
			 /*  print_r($data);
			  echo "\n";
  			  echo $data["view_listings_usage"];	
			  echo "-";
			  echo $data[$_POST['module']."_usage"];*/
			  foreach($data as $key => $val){
						//'"$key" => $val,"';
						$custom_data[$key]=$val;
			  }
			  
			  if(empty($data[$_POST['module']."_usage"])){
				  $custom_data[$_POST['module']."_usage"]=1;
			  }else{
				  $custom_data[$_POST['module']."_usage"]=$custom_data[$_POST['module']."_usage"]+1;
			  }
			// echo "\n---------------------------------------\n";
			/// print_r($data2);
			
			}else{        // if there is none, lets create one
				           $custom_data = array(
							"month" => date("m"),
							$_POST['module']."_usage" => 1
							);
				echo "2";
			}
			
			$query = mysqli_query($sqlConnect, "UPDATE "  . T_USERS . " SET `custom_data` = '".json_encode($custom_data)."'  WHERE user_id = '".$wo['user']['user_id']."' ");
			
}			
/*
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
*/
die;

?>