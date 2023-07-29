<?php
global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

$packagestatus = Wo_GetUserPackage_status();


if($_POST['action']=="log"){
    
    if($packagestatus == 1){
        $modules_limit = Wo_reduce_features_permission_and_return($_POST['module']);
        
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
    
    if($packagestatus == 1){
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