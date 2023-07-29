<?php

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);


// if Action is set from the frontend
if(isset($_POST['action']) && $_POST['action'] == "downgrade_package"){
    
    $pro_type = 0;
    $userid = $wo['user']['user_id'];
  
    $pro_features = "SELECT * FROM `Wo_Manage_Pro` WHERE id=".$pro_type;
    $sql2       = mysqli_query($sqlConnect, $pro_features);
    $fetched_feat = mysqli_fetch_assoc($sql2);
    $mylisting = $fetched_feat['strastic_listing'];
    $myfeaturedlisting = 0;
    
    $myfixandflip = 0;
    $myruncomp = 0;
    $mybuyandrent = 0;
    $mypromote = 0;
    $mybuyermatch = 0;
    $myaudio = 0;
    $myvideo = 0;
    $mymessage = 0;
    $mylesson = 0;
    $mycompany = 0;
    $mydeal_review = 0;
    $broadcast_mail = 0;
    $myleadlisting = $fetched_feat['myleadlisting'];
    $time = date("Y-m-d h:i:s");
    
    $status = 0;
    $query_two = mysqli_query($sqlConnect, " UPDATE `Wo_my_package_manage`  SET `fix_and_flip` = '{$myfixandflip}', `myleadlisting` = '{$myleadlisting}', `date_created` = '{$time}', `company_direc` = '{$mycompany}', `broadcast_mail` = '{$broadcast_mail}', `run_comps` = '{$myruncomp}', `buy_and_hold` = '{$mybuyandrent}', `promote` = '{$mypromote}', `buyer_match` = '{$mybuyermatch}', `pro_type` = '{$protype}', `myleademail` = '{$myleademail}', `myfeaturedlisting` = '{$myfeaturedlisting}', `mylisting` = '{$mylisting}', `status` = '{$status}', `message_chat` = '{$mymessage}', `audio_chat` = '{$myaudio}', `video_chat` = '{$myvideo}', `advance_lesson` = '{$mylesson}', `deal_review` = '{$mydeal_review}' WHERE `userid` = '{$userid}' ");
    
    
    Wo_UpdateUserData($userid, array(
        'is_pro' => 0,
        'is_free' => 0,
        'pro_type' => $pro_type,
        'pro_time' => time(),
        'update_type' => 'payment'
    ));
            
            if($query_two){
                $data = array(
                    'status' => 200,
                    'message' => 'Downgraded Success'
                );
            }
header("Content-type: application/json");
echo json_encode($data);
die;  


}

// If no action is set from the frontend
if(!isset($_POST['action']) || $_POST['action'] == ""){
    
    $data = array(
        'status' => 404,
        'message' => 'Sorry Page Not Found....'
    );
 header("Content-type: application/json");
echo json_encode($data);
die;   
}

