<?php

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);
$user_id = $wo['user']['user_id'];

if(isset($_POST['action']) && $_POST['action']=="lesson_access_ac") {
    
    $price = $_POST['lesson_price'];
    $lesson_id = $_POST['lesson_id'];
    
     $querywall = "SELECT `wallet` FROM `Wo_Users` WHERE `user_id`=".$user_id;

    $sqlwall       = mysqli_query($sqlConnect, $querywall);
    $fetched_datawall = mysqli_fetch_assoc($sqlwall);
    $mywallet = $fetched_datawall['wallet'];
    
     // Check if lesson details actuallyu exist
    $query = "SELECT price, COUNT(id) AS lessid FROM `Wo_Blog` WHERE `id` = $lesson_id";
    $sql  = mysqli_query($sqlConnect, $query);
    $fetched_data = mysqli_fetch_assoc($sql);
    $lessid = $fetched_data['lessid'];
    $real_price = $fetched_data['price'];
    
    if($mywallet > $price && $price == $real_price){
        
    
        if($lessid > 0){
            
          $insertquery = mysqli_query($sqlConnect,"INSERT INTO `my_blog_access` (`blog_id`,`user_id`,`price`,`status`,`date_created`,`date_updated`) VALUES ($lesson_id, $user_id, $price, 1, now(), now())");
           
           $data = array(
                'status' => 200,
                'message' => 'Payment Made Successfully'
            ); 
            
        }else{
            $data = array(
                'status' => 400,
                'message' => 'Sorry Lesson Not Found'
            );
        }
     
        
    }else{
        $data = array(
                'status' => 400,
                'message' => 'Sorry Your wallet is too low to access this lesson'
        );
    }
    
   
    
	
    
}

if(!isset($_POST['action']) && $_POST['action']==""){
    $data = array(
        'status' => 404,
        'message' => 'Error Access Denied'
    );   
}


header("Content-type: application/json");
echo json_encode($data);
die();

