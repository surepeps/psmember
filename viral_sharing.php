<?php

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);
$user_id = $wo['user']['user_id'];

if(isset($_POST['action']) && $_POST['action']=="check_share_status") {
    
    if($wo['user']['pro_type'] > 0){
        
        $mylimit =  $wo['config']['pro_day_limit'];
        $mycurrentdaylimit = $wo['user']['daily_points'];
        $myshare_point_limit = Wo_get_share_limit($mylimit,$mycurrentdaylimit);
        echo $myshare_point_limit;
        
    }else{
        
        $mylimit = $wo['config']['free_day_limit'];
        $mycurrentdaylimit = $wo['user']['daily_points'];
        $myshare_point_limit = Wo_get_share_limit($mylimit,$mycurrentdaylimit);
        echo $myshare_point_limit;
    }
    
	die();
    
}


if(isset($_POST['action']) && $_POST['action']=="check_position_n_points"){
    if(!empty($_POST['where'])){
        $where = $_POST['where'];
        $type = $_POST['type'];

        $query = mysqli_query($sqlConnect, "SELECT COUNT(*) AS rank, t.userid, t.total FROM ( SELECT   userid , SUM(points) AS total FROM wo_user_points WHERE userid = $user_id AND $where GROUP BY userid ) AS t JOIN ( SELECT DISTINCT SUM(points) AS total FROM wo_user_points WHERE $where GROUP BY userid ) AS dt ON t.total <= dt.total GROUP BY t.userid ORDER BY rank , userid");
        $row = mysqli_fetch_assoc($query);
        $myrank = $row['rank'];
        $mypoint = $row['total'];
        
        if($type == 'rank'){
            if($myrank > 0){
                echo $myrank;
            }else{
                echo 0;
            }
        }
        
        if($type == 'points'){
            if($mypoint > 0){
                echo $mypoint;
            }else{
                echo 0;
            }
        }
        
     
      
    }else{
       echo 0;
    }
    
    die();
    
}




    if($_POST['type'] != '' || $_POST['point'] != '' || $_POST['link'] != '' || $_POST['day'] != '' || $_POST['user_id'] != ''){
            
            $type = Wo_Secure($_POST['type']);
            if($type == 'fb'){
              $share_id = 1;
            }elseif($type == 'tw'){
              $share_id = 2;
            }elseif($type == 'em'){
              $share_id = 3;
            }elseif($type == 'lk'){
              $share_id = 4;
            }
            
            $point = Wo_Secure($_POST['point']);
            $day = Wo_Secure($_POST['day']);
            $user_id = Wo_Secure($_POST['user_id']);
            $time = Wo_Secure($_POST['time']);
            $link = Wo_Secure($_POST['link']);
            
            $entersharepoints  = Wo_insert_new_shared_points($user_id,$point,$type,$time,$day,$link);
            
            if($entersharepoints){
                Wo_RegisterPoint($share_id, $type);
                
                $data = array(
                    'status' => 200,
                    'message' => 'Success in Confirming Your Sharing'
                );
                
            }else{
                $data = array(
                    'status' => 400,
                    'message' => 'Error While Confirming Your Sharing'
                );
            }
            
            
        }else{
            $data = array(
                    'status' => 400,
                    'message' => 'Values not found'
            );
        }
        header("Content-type: application/json");
        echo json_encode($data);
        die();

