<?php

// Allow Json Response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Recieve the response
$json = file_get_contents('php://input');
$obj = json_decode($json, TRUE);

// Check if response if empty or filled
if( count($json) > 0 ){
    
    // Get System Connection
    global $wo, $sqlConnect;
    $root=$_SERVER['DOCUMENT_ROOT'];
    require_once($root.'/config.php');
    require_once('assets/init.php');

    // Get all response out
    
    // {
    //     "drop_id":"611ae669a183f5000850edd5",
    //     "phone_number":"+18506912713",
    //     "attempt_date":"2021-08-16T22:27:59.555Z",
    //     "status":"success",
    //     "reason":"",
    //     "dnc":false,
    //     "spam":false,
    //     "quantity":1,
    //     "product_code":"rvm",
    //     "product_cost":0.099,
    //     "network":{
    //         "name":"NONE",
    //         "type":"wireless"
    //     },
    //     "foreign_id":{
    //         "user_id":598,
    //         "concronid":12
    //     }
        
    // }
    
    
    $attempt_date = $obj['attempt_date'];
    $reason = $obj['reason'];
    $drop_id = $obj['drop_id'];
    $pNum = $obj['phone_number'];
    $RVMstatus = $obj['status'];
    $cost = $obj['product_cost'];
    $userId = $obj['foreign_id']['user_id'];
    $cronId = $obj['foreign_id']['concronid'];
    
    $ndata = array(
        'cron_id' => $cronId,
        'user_id' => $userId,
        'status' => $RVMstatus,
        'product_cost' => (float)$cost,
        'phone_number' => $pNum,
        'drop_id' => $drop_id,
        'reason' => $reason,
        'attempt_date' => $attempt_date
    );
    
    if($RVMstatus == "success"){
        
        $cBalance = checkWalletBalance($userId);
        
        if($cBalance >= (float)$cost){
            
            createRVMPaymentRecord($ndata);
            reduceWalletBalance_A((float)$cost,$userId);
            UpdateCronRecordAsDone($cronId,2);
        }
        
        
    }else{
        
        createRVMPaymentRecord($ndata);
        UpdateCronRecordAsDone($cronId,2);
    }
    
    
    
}else{
    
    $data = array(
        'status' => 404,
        'message' => 'Sorry! Access Denied, Invalid Response passed'
    );
    
    header("Content-type: application/json");
    echo json_encode($data);
    die();
}


?>