<?php
$root=__DIR__;

require_once($root.'/config.php');
require_once('assets/init.php'); 

global $wo, $sqlConnect;
$user_id = $wo['user']['user_id'];


$action = filter('action');

$status = 400;
if($action == 'add_scheduled_email') {
    
    if(isset($_POST['Send_Subject'])){
        $subject = $_POST['Send_Subject'];
        
    }
    
    if(isset($_POST['my_bc_msg'])){
        $my_bc_msg = $_POST['my_bc_msg'];
        
    }
    
    if(isset($_POST['my_prop_id'])){
        $my_prop_id = $_POST['my_prop_id'];
        
    }
    
    if(isset($_POST['Send_Email'])){
        $Send_Email = $_POST['Send_Email'];
        
    }
    
    if(isset($_POST['options'])){
        $lists = $_POST['options'];
    }
    
    if(isset($_POST['city_r'])){
        $city_r = $_POST['city_r'];
    }
    
    $emailFee = 0.006;
    
    $myFirstName = $wo['user']['first_name'];  
    $myLastName = $wo['user']['last_name'];
    
    $mybname = $myFirstName. ' '. $myLastName;
    
    
    $list_count = count($lists);
    $my_list_ids = serialize($lists);
    $my_new_msg = $my_bc_msg;
    
    $total = 0;
    foreach($lists as $ls){
        $cc = getAllTagEmailsCounbter($ls);
        $total += $cc;
    }
    
    // Multiply the counter with email fee
    $CwalletFee = $total*$emailFee;


    // CHECK WALLLET 
    $wBalance = checkWalletBalance($user_id);

    if($wBalance >= $CwalletFee){
        
        $is_scheduled = filter('schedule_email');
        if($is_scheduled) {

            $email_date = filter('email_date');
            if(!$email_date) {
                $data = array(
                    'status' => 401,
                    'message' => "Please select a valid schedule date."
                ); 
            }else{

                $queryData = [
                    'prop_id' => $my_prop_id,
                    'subject' => $subject,
                    'email_text' => $my_new_msg,
                    'email_date' => $email_date,
                    'from_email' => $Send_Email,
                    'tags' => $my_list_ids,
                    'user_id' => $user_id,
                    'type' => filter('demo02')
                ];
                
                $query = insertRow('wo_scheduled_emails', $queryData);

                if($sqlConnect->query($query)){
                    
                    // Deduct the money from users wallet
                    $newwallet = (float)$wBalance - (float)$CwalletFee;
                    reduceWalletBalance_A($CwalletFee,$user_id);
                    
                    $data = array(
                        'status' => 200,
                        'wallet' => $newwallet,
                        'message' => "You have $ {$newwallet} In Your wallet Left."
                    );
                }else{
                    $data = array(
                        'status' => 401,
                        'message' => mysqli_error($sqlConnect)
                    ); 
                }
                
            }


        }
    }else{
        
        $data = array(
            'status' => 401,
            'message' => "Sorry, Insufficient Fund in the wallet."
        ); 
        
    }
    

}else if($action == 'edit_scheduled_email') {
    
    if(isset($_POST['Send_Subject'])){
        $subject = $_POST['Send_Subject'];
        
    }
    
    if(isset($_POST['my_bc_msg'])){
        $my_bc_msg = $_POST['my_bc_msg'];
        
    }
    
    if(isset($_POST['my_prop_id'])){
        $my_prop_id = $_POST['my_prop_id'];
        
    }
    
    if(isset($_POST['Send_Email'])){
        $Send_Email = $_POST['Send_Email'];
        
    }
    
    if(isset($_POST['options'])){
        $lists = $_POST['options'];
    }
    
    if(isset($_POST['city_r'])){
        $city_r = $_POST['city_r'];
    }
    
    $emailFee = 0.006;
    
    $myFirstName = $wo['user']['first_name'];  
    $myLastName = $wo['user']['last_name'];
    
    $mybname = $myFirstName. ' '. $myLastName;
    
    $my_list_ids = serialize($lists);
    $my_new_msg = $my_bc_msg;
    
    $list_count = count($my_list_ids);
    
    
    $schedule_id = filter('schedule_id');
    
    if($schedule_id) {

        $where = ['id' => $schedule_id];

        $schedule_email = getTableData('wo_scheduled_emails', $where, 1);

        $email_date = filter('email_date');
        if(!$schedule_email) {
            $data = array(
                'status' => 401,
                'message' => "This schedule email is deleted."
            ); 
        }else if(!$email_date) {
            $data = array(
                'status' => 401,
                'message' => "Please select a valid schedule date."
            ); 
        }else{

            $queryData = [
                'prop_id' => $my_prop_id,
                'subject' => $subject,
                'email_text' => $my_new_msg,
                'email_date' => $email_date,
                'from_email' => $Send_Email,
                'tags' => $my_list_ids,
                'user_id' => $user_id,
                'type' => filter('demo02')
            ];
            
            $query = updateRow('wo_scheduled_emails', $queryData, $where);

            if($sqlConnect->query($query)){
                
                $data = array(
                    'status' => 200,
                    'wallet' => $newwallet,
                    'message' => "Email broadcast is updated successfully"
                );

            }else{
                $data = array(
                    'status' => 401,
                    'message' => mysqli_error($sqlConnect)
                ); 
            }
        }

    }else{
        $data = array(
            'status' => 401,
            'message' => "Please select a valid scheduled email"
        ); 
    }
        
    

}else if($action == 'delete_scheduled_email') {
    
    $schedule_id = filter('scheduled_id');
    
    if($schedule_id) {
        $where = ['id' => $schedule_id];
        $schedule_email = getTableData('wo_scheduled_emails', $where, 1);

        if(!$schedule_email) {
            $data = array(
                'status' => 401,
                'message' => "This schedule email is deleted."
            ); 
        }else{
            
            $query = deleteRow('wo_scheduled_emails', $where);

            if($sqlConnect->query($query)){
                
                $data = array(
                    'status' => 200,
                    'wallet' => $newwallet,
                    'message' => "Scheduled email is deleted successfully"
                );

            }else{
                $data = array(
                    'status' => 401,
                    'message' => mysqli_error($sqlConnect)
                ); 
            }
        }

    }else{
        $data = array(
            'status' => 401,
            'message' => "Please select a valid scheduled email"
        ); 
    }
        
    

}else if($action == 'add_scheduled_sms') {
    
    if(isset($_POST['Send_Subject'])){
        $subject = $_POST['Send_Subject'];
        
    }
    
    if(isset($_POST['my_bc_msg'])){
        $my_bc_msg = $_POST['my_bc_msg'];
        
    }
    
    if(isset($_POST['my_prop_id'])){
        $my_prop_id = $_POST['my_prop_id'];
        
    }
    
    if(isset($_POST['number_s'])){
        $number_s = $_POST['number_s'];
        
    }
    
    if(isset($_POST['options'])){
        $lists = $_POST['options'];
    }
    
    if(isset($_POST['city_r'])){
        $city_r = $_POST['city_r'];
    }
    
    $emailFee = 0.003;
    
    $myFirstName = $wo['user']['first_name'];  
    $myLastName = $wo['user']['last_name'];
    
    $mybname = $myFirstName. ' '. $myLastName;
    
    
    $list_count = count($lists);
    $my_list_ids = serialize($lists);
    $my_new_msg = $my_bc_msg;
    
    $total = 0;
    foreach($lists as $ls){
        $cc = getAllTagEmailsCounbter($ls);
        $total += $cc;
    }
    
    // Multiply the counter with email fee
    $CwalletFee = $total*$emailFee;


    // CHECK WALLLET 
    $wBalance = checkWalletBalance($user_id);

    if($wBalance >= $CwalletFee){
        
        $is_scheduled = filter('schedule_sms');
        if($is_scheduled) {

            $sms_date = filter('sms_date');
            if(!$sms_date) {
                $data = array(
                    'status' => 401,
                    'message' => "Please select a valid schedule date."
                ); 
            }else{

                $queryData = [
                    'prop_id' => $my_prop_id,
                    'sms_text' => $my_new_msg,
                    'sms_date' => $sms_date,
                    'from_phone' => $number_s,
                    'tags' => $my_list_ids,
                    'user_id' => $user_id,
                    'type' => filter('demo02')
                ];
                
                $query = insertRow('wo_scheduled_sms', $queryData);

                if($sqlConnect->query($query)){
                    
                    // Deduct the money from users wallet
                    $newwallet = (float)$wBalance - (float)$CwalletFee;
                    reduceWalletBalance_A($CwalletFee,$user_id);
                    
                    $data = array(
                        'status' => 200,
                        'wallet' => $newwallet,
                        'message' => "You have $ {$newwallet} In Your wallet Left."
                    );
                }else{
                    $data = array(
                        'status' => 401,
                        'message' => mysqli_error($sqlConnect)
                    ); 
                }
                
            }


        }
    }else{
        
        $data = array(
            'status' => 401,
            'message' => "Sorry, Insufficient Fund in the wallet."
        ); 
        
    }
    

}else if($action == 'edit_scheduled_sms') {
    
    if(isset($_POST['my_bc_msg'])){
        $my_bc_msg = $_POST['my_bc_msg'];
        
    }
    
    if(isset($_POST['my_prop_id'])){
        $my_prop_id = $_POST['my_prop_id'];
        
    }
    
    if(isset($_POST['number_s'])){
        $number_s = $_POST['number_s'];
        
    }
    
    if(isset($_POST['options'])){
        $lists = $_POST['options'];
    }
    
    if(isset($_POST['city_r'])){
        $city_r = $_POST['city_r'];
    }
    
    
    
    $myFirstName = $wo['user']['first_name'];  
    $myLastName = $wo['user']['last_name'];
    
    $mybname = $myFirstName. ' '. $myLastName;
    
    $my_list_ids = serialize($lists);
    $my_new_msg = $my_bc_msg;
    
    $list_count = count($my_list_ids);
    
    
    $schedule_id = filter('schedule_id');
    
    if($schedule_id) {

        $where = ['id' => $schedule_id];

        $schedule_sms = getTableData('wo_scheduled_sms', $where, 1);

        $sms_date = filter('sms_date');
        if(!$schedule_sms) {
            $data = array(
                'status' => 401,
                'message' => "This schedule sms is deleted."
            ); 
        }else if(!$sms_date) {
            $data = array(
                'status' => 401,
                'message' => "Please select a valid schedule date."
            ); 
        }else{

            $queryData = [
                'prop_id' => $my_prop_id,
                'sms_text' => $my_new_msg,
                'sms_date' => $sms_date,
                'from_phone' => $number_s,
                'tags' => $my_list_ids,
                'user_id' => $user_id,
                'type' => filter('demo02')
            ];
            
            $query = updateRow('wo_scheduled_sms', $queryData, $where);
            if($sqlConnect->query($query)){
                
                $data = array(
                    'status' => 200,
                    'wallet' => $newwallet,
                    'message' => "SMS broadcast is updated successfully"
                );

            }else{
                $data = array(
                    'status' => 401,
                    'message' => mysqli_error($sqlConnect)
                ); 
            }
        }

    }else{
        $data = array(
            'status' => 401,
            'message' => "Please select a valid scheduled email"
        ); 
    }
        
    

}else if($action == 'delete_scheduled_sms') {
    
    $schedule_id = filter('scheduled_id');
    if($schedule_id) {
        $where = ['id' => $schedule_id];
        $schedule_sms = getTableData('wo_scheduled_sms', $where, 1);

        if(!$schedule_sms) {
            $data = array(
                'status' => 401,
                'message' => "This schedule sms is deleted."
            ); 
        }else{
            
            $query = deleteRow('wo_scheduled_sms', $where);

            if($sqlConnect->query($query)){
                
                $data = array(
                    'status' => 200,
                    'wallet' => $newwallet,
                    'message' => "Scheduled sms is deleted successfully"
                );

            }else{
                $data = array(
                    'status' => 401,
                    'message' => mysqli_error($sqlConnect)
                ); 
            }
        }

    }else{
        $data = array(
            'status' => 401,
            'message' => "Please select a valid scheduled email"
        ); 
    }
        
    

}
header("Content-type: application/json");
echo json_encode($data);
die();   

