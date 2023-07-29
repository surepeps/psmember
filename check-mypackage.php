<?php
global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

$packagestatus = Wo_GetUserPackage_status();


if($_POST['action']=="log"){
    if(isset($_POST['number']) && $_POST['number'] > 0){
        $number = $_POST['number'];
    }else{
        $number = 1;
    }
    if($wo['user']['pro_type'] > 0 && $packagestatus == 1){
        $modules_limit = Wo_reduce_features_permission_and_return($_POST['module'],$number);
        
        if($modules_limit > 0){
            echo $modules_limit;
        }else{
            echo "over_limit";
        }    
    
	}else{
		 echo "over_limit";
	}
	
	die();
}


if($_POST['action']=="check"){
    
    if($wo['user']['pro_type'] > 0 && $packagestatus == 1){
        $modules_limit = Wo_get_features_count($_POST['module']);
        
        if($modules_limit > 0){
            echo $modules_limit;
        }else{
            echo "over_limit";
        }    
    
	}else{
		 echo "over_limit";
	}
	
	die();
}




?>