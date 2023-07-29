<?php 

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');


// Reduce Feature Value when added
if( isset($_POST['action']) && ($_POST['action'] == "ProcessFeatAction") ) {
    
    $user_id = $wo['user']['user_id'];
    $userPackId = $wo['user']['my_package'];
    $packPath = Wo_Secure($_POST['path']);
    
    $num = $_POST['num_times'];
    
    // get feature values from user
    $feat = getUserPackages($user_id,$userPackId,$packPath);
    $featVal = $feat[$packPath];
    
    $P_type = $_POST['type'];
    
    // Reduce Feature when Add Button clicked
    if($P_type == 1){
        
        if($featVal >= $num){
            $newVal = $featVal - $num;
            $errors = 0;
            $data['result'] = $newVal;
        }else{
            $errors = 1;
            $data['message'] = "Error";
            $newVal = 0;
            $data['result'] = $newVal;
        }
        
    }else{
    // Add Feature when Delete Button clicked
        $newVal = $featVal + $num;
        $errors = 0;
        $data['result'] = $newVal;
    }
    
    if($errors == 0){
        // Update data
        $updateFeact = PackageFeatureUpdateCol($user_id,$packPath,$newVal,$userPackId);
        $data['status'] = 200;
        
    }else{
        $data['status'] = 400;
    }
    
    
    header("Content-type: application/json");
    echo json_encode($data);
    die();
}


// Add Feature Value when deleted
if( isset($_POST['action']) && ($_POST['action'] == "check") ) {
    
    $user_id = $wo['user']['user_id'];
    $userPackId = $wo['user']['my_package'];
    $packPath = Wo_Secure($_POST['path']);
    
    $num = $_POST['num_times'];
    
    // get feature values from user
    $feat = getUserPackages($user_id,$userPackId,$packPath);
    $featVal = $feat[$packPath];
    
    $data['result'] = $featVal;
    
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
}
