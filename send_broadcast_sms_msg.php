<?php
global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);

$user_id = $wo['user']['user_id'];

if(isset($_POST['my_prop_id']) && isset($_POST['my_bc_msg']) && isset($_POST['options']) && isset($_POST['number_s'])){
  
    
    if(isset($_POST['my_bc_msg'])){
        $my_bc_msg = $_POST['my_bc_msg'];
        
    }
    
    if(isset($_POST['my_prop_id'])){
        $my_prop_id = $_POST['my_prop_id'];
        
    }
    
    if(isset($_POST['number_s'])){
        $Send_Phone = $_POST['number_s'];
        
    }
    
    if(isset($_POST['options'])){
        $lists = $_POST['options'];
    }
    
    if(isset($_POST['city_r'])){
        $city_r = $_POST['city_r'];
    }
    
    $smsFee = 0.03;
    
    $my_list_ids = serialize($lists);
    $my_new_msg = serialize($my_bc_msg);
    
    $list_count = count($my_list_ids);
    
    $total = 0;
    foreach($lists as $ls){
        $cc = getAllTagSmsCounbter($ls);
        $total += $cc;
    }
    
     // Multiply the counter with sms fee
    $CwalletFee = $total*$smsFee;
    
    // CHECK WALLLET 
    $wBalance = checkWalletBalance($user_id);
    
    if($wBalance >= $CwalletFee){
        
        foreach($lists as $key => $values){
            $tagid = $values;
            
            // Select all contacts in a tag
           $contLinkTag = getContactIdFromTagId($tagid);
           if($contLinkTag){
               
                foreach($contLinkTag as $cotd){
                    
                    $c_id = $cotd['contact_id'];
                    
                    // Get mobile number from contact Details
                    $ctde = GetSingleContactDetails($c_id);
                    if($ctde['mobile'] != ""){
                        $mobile_numbers[] = $ctde['mobile'];
                    }
                    
                }
                
            }
       
        }
        
        // Call all email address in the array above
        foreach ( $mobile_numbers as $mMobile ) {
            
            $mMobile = trim($mMobile);
            if(!$mMobile) continue;
            
            $sendresponse = send_bulk_sms_broadcast($mMobile,$Send_Phone,$my_bc_msg);
                 
            if($sendresponse){
                
                
                $Send_Phone = stringCounterReduce(array(
                    'lenght' => 10,
                    'string' => $Send_Phone
                ));
                
                // Data to save
                $data = [
                    'from_number' => $mMobile,
                    'sms_text' => $my_bc_msg,
                    'to_number' => $Send_Phone,
                    'user_id' => $user_id,
                    'status' => "seen",
                    'direction' =>'outbound',
                    'm_time' => time(),
                    'receive_date' => date("m d Y h:i:s A")
                ];
                
                $messages = createSMSChat($data);
                
                // Deduct the money from users wallet
                $newwallet = (float)$wBalance - (float)$CwalletFee;
                reduceWalletBalance_A($smsFee,$user_id);
                
                $data = array(
                    'status' => 200,
                    'wallet' => $newwallet,
                    'message' => "Broadcast SMS Successfully Sent"
                );
            }else{
                $data = array(
                    'status' => 400,
                    'message' => "Error SMS Could Not send"
                );
            }
            
            
        }
        
        
    }else{
        
        $data = array(
            'status' => 401,
            'message' => "Sorry, Insufficient Fund in the wallet."
        ); 
        
        
    }
    
    
    // $featurename = "broadcast_sms";
    // $get_my_email_requestleft = Wo_get_features_count($featurename);
    
    
    // if ($get_my_email_requestleft >= $list_count) {
    //     foreach($lists as $values ){
            
    //         if($values == "x"){
                
    //             $query = array("query" => "AND buyer_phone_number != '' ");
    //             $list__C = get_buyerInfo_data_record($user_id,"count",$query);
    //             if($list__C > 0){
    //                 $list_ddetails = get_buyerInfo_data_record($user_id,"read",$query);
    //                 $type = "buyer";
    //             }else{
    //                 return false;
    //             }
                
    //         }
            
            
    //         if($values == "y"){
               
    //           $query = array("query" => "AND city LIKE '%$city_r%' AND buyer_phone_number != '' ");
    //           $list__C2 = get_buyerCityInfo_data_record($user_id,"count",$query);
    //           if($list__C2 > 0 ){
    //               $list_ddetails = get_buyerCityInfo_data_record($user_id,"read",$query);
    //               $type = "buyer"; 
    //           }else{
    //                 return false;
    //             }
                
    //         }
            
            
    //         if($values != "x" && $values != "y"){
    //             // get all emails and FullName under the list id
    //             $list_ddetails = mysqli_query($sqlConnect, "SELECT * FROM `My_SMS_Contact_Lists` WHERE list_id = $values");
    //             $type = "smsList";
    //         }
            
            
    //     }
            
    //         while($get_my_list_d = mysqli_fetch_assoc($list_ddetails)){
    //             if($type == "buyer"){
    //                 $mylistFirstName = $get_my_list_d['buyer_name'];
    //                 $mylistPhone = $get_my_list_d['buyer_phone_number'];
                    
    //             }else{
    //                 $mylistFirstName = $get_my_list_d['List_FirstName'];
    //                 $mylistLastName = $get_my_list_d['List_LastName'];
    //                 $mylistPhone = $get_my_list_d['List_Phone'];
                    
    //             }
                
                
    //             $sendresponse = send_bulk_sms_broadcast($mylistPhone,$Send_Phone,$my_bc_msg);
                 
    //             if($sendresponse){
    //                 $data = array(
    //                     'status' => 200,
    //                     'message' => "Broadcast SMS Successfully Sent"
    //                 );
    //             }else{
    //                 $data = array(
    //                     'status' => 400,
    //                     'message' => "Error SMS Could Not send"
    //                 );
    //             }
                 
    //         }
            
        
        
    // }else{
    //   $data = array(
    //         'status' => 401,
    //         'message' => "Sorry, you have reached your SMS Broadcast limit for this month."
    //     );  
    // }
    
    
    
   
    
}else{
    $data = array(
        'status' => 400,
        'message' => 'Error! Please fill all neccessary Form Fields'
    );
}

header("Content-type: application/json");
echo json_encode($data);
die;