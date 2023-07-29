<?php

global $wo, $sqlConnect;
$root = $_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

$sqlConnect = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);

$user_id = $wo['user']['user_id'];

if($_POST['action']=="reduce_wallet"){
    
    $price = $_POST['price'];
    
    $mywallet = checkWalletBalance($user_id);
    
    if($mywallet > $price){
        
        $newwallet = $mywallet - $price;
    
        // Update wallet of user
        $lastwallup = reduceWalletBalance_A($newwallet,$user_id);
        
        if($lastwallup){
            
            echo $newwallet;
            
        }else{
            
            echo 0;
            
        }
        
    }else{
        
        echo 0;
        
    }
    
   
}

if($_POST['action']=="action_wallet"){
    
    if(isset($_POST['number'])){
        $number = $_POST['number'];
    }
    
    if(isset($_POST['type'])){
        $type = $_POST['type'];
    }
    
    if($type > 0){
        
        if($type == 1){
            // Type = SMS
            $c = 0.03;
        }elseif($type == 2){
            // Type = EMAIL
            $c = 0.006;
        }
        
        if($number > 0){
            $amt = $number * $c;
            
            // Update wallet of user
            $lastwallup = reduceWalletBalance_A($amt,$user_id);
            
            if($lastwallup){
                
                echo $lastwallup;
                
            }else{
                
                echo 0;
                
            }
                
        }else{
            
            echo "low_contact";
            
        }
        
    }else{
        
        echo "invalid_type";
        
    }
    
}


if($_POST['action']=='check_wallet'){
    
    $wBalance = checkWalletBalance($user_id);
    
    echo $wBalance;
}

if($_POST['action']=='' || !isset($_POST['action'])){
    
    $data = array(
        'status' => 404,
        'message' => 'Sorry Access Denied'
    );
    
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
}