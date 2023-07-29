<?php

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php'); 
require_once('assets/init.php');

require_once('sendgrid-php/sendgrid-php.php');
use SendGrid\Mail\Personalization;
use SendGrid\Mail\To;

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);

$user_id = $wo['user']['user_id'];

if(isset($_POST['my_prop_id']) && isset($_POST['my_bc_msg']) && isset($_POST['options']) && isset($_POST['Send_Email'])){
    
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
    $my_new_msg = serialize($my_bc_msg);
    
    $list_count = count($my_list_ids);
    
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
        
        foreach($lists as $key => $values){
            $tagid = $values;
            
            // Select all contacts in a tag
           $contLinkTag = getContactIdFromTagId($tagid);
           if($contLinkTag){
               
                foreach($contLinkTag as $cotd){
                    
                    $c_id = $cotd['contact_id'];
                    
                    // Get email from contact Details
                    $ctde = GetSingleContactDetails($c_id);
                    if($ctde['email'] != ""){
                        $email_addresses[] = $ctde['email'];
                    }
                    
                }
                
            }
       
        }
        
        
        // Call all email address in the array above
        foreach ( $email_addresses as $email_address ) {

            $personalization = new Personalization();
            
            $personalization->addTo( new To( $email_address ) );
            
            $sendgridPersonalization[] = $personalization;
            
        }
        
        // Sending bulk email segment
        $chunkedUsers = array_chunk($sendgridPersonalization, 1000, true);
        foreach ($chunkedUsers as $singleChunk) {
    
            $email = new \SendGrid\Mail\Mail(); 
            $email->setfrom($Send_Email, $mybname);
            $email->setSubject($subject);
            $testing = "bulkemails@".$wo['config']['siteName'].".com";
            $email->addto($testing, $wo['config']['siteName']);
            
            $email->addContent("text/html", $my_bc_msg);
            $sendgrid = new \SendGrid('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');
    
            foreach ( $singleChunk as $personalization_r ) {
                $email->addPersonalization( $personalization_r );
            }
            
            
            try {
                $response = $sendgrid->send($email);
                
                // Deduct the money from users wallet
                $newwallet = (float)$wBalance - (float)$CwalletFee;
                reduceWalletBalance_A($emailFee,$user_id);
                
                $data = array(
                    'status' => 200,
                    'wallet' => $newwallet,
                    'message' => "Broadcast Mail Successfully Sent"
                );
                
            } catch (Exception $e) {
                $data = array(
                    'status' => 400,
                    'message' => "Error" .$response->statusCode(). "Mail Could Not send"
                );
            }
            
                
        
        }
        
        
    
    }else{
        
        $data = array(
            'status' => 401,
            'message' => "Sorry, Insufficient Fund in the wallet."
        ); 
        
    }
    
    

    
    // $featurename = "broadcast_mail";
    // $get_my_email_requestleft = Wo_get_features_count($featurename);
    
    // CHECK WALLLET 
    // $wBalance = checkWalletBalance($user_id);
    
    
    // if ($get_my_email_requestleft >= $list_count) {
        
        
    //     foreach($lists as $key => $values){

    //         // get all emails and FullName under the Contacts id
    //         $list_ddetails = mysqli_query($sqlConnect, "SELECT * FROM `My_Email_Lists` WHERE list_id = $values");
    //         $type = "emailList";

            
            
    //         // if($values == "x"){
    //         //     $query = array("query" => "AND buyer_email != '' ");
    //         //     $list__C = get_buyerInfo_data_record($user_id,"count",$query);
    //         //     if($list__C > 0){
    //         //         $list_ddetails = get_buyerInfo_data_record($user_id,"read",$query);
    //         //         $type = "buyer";
    //         //     }else{
    //         //         return false;
    //         //     }
                
    //         // }
            
    //         // if($values == "y"){
               
    //         //   $query = array("query" => "AND city LIKE '%$city_r%' AND buyer_email != '' ");
    //         //   $list__C2 = get_buyerCityInfo_data_record($user_id,"count",$query);
    //         //   if($list__C2 > 0 ){
    //         //       $list_ddetails = get_buyerCityInfo_data_record($user_id,"read",$query);
    //         //       $type = "buyer"; 
    //         //   }else{
    //         //         return false;
    //         //     }
                
    //         // }
            
    //         // if($values == "z"){
                
    //         // }
            
    //         // if($values != "x" && $values != "y"){
                
    //         //     // get all emails and FullName under the list id
    //         //     $list_ddetails = mysqli_query($sqlConnect, "SELECT * FROM `My_Email_Lists` WHERE list_id = $values");
    //         //     $type = "emailList";
    //         // }
            
            
    //     }
        
        
        
    //     while($get_my_list_d = mysqli_fetch_assoc($list_ddetails)){
                    
    //         if($type == "buyer"){
    //             $mylistFirstName = $get_my_list_d['buyer_name'];
    //             $email_addresses[] = $get_my_list_d['buyer_email'];
                
    //             $bname = $mylistFirstName;
    //         }else{
    //             $mylistFirstName = $get_my_list_d['List_FirstName'];
    //             $mylistLastName = $get_my_list_d['List_LastName'];
    //             $email_addresses[] = $get_my_list_d['List_Email'];
                
    //             $bname = $mylistFirstName. ' '.$mylistLastName;
    //         }
    
            
    //     }
        
    //     foreach ( $email_addresses as $email_address ) {

    //         $personalization = new Personalization();
            
    //         $personalization->addTo( new To( $email_address ) );
            
    //         // $email->addPersonalization( $personalization );
            
    //         $sendgridPersonalization[] = $personalization;
            
    //     }
        
    
    //     // Sending bulk email segment
    //     $chunkedUsers = array_chunk($sendgridPersonalization, 1000, true);
    //     foreach ($chunkedUsers as $singleChunk) {
        
    //         $email = new \SendGrid\Mail\Mail(); 
    //         $email->setfrom($Send_Email, $mybname);
    //         $email->setSubject($subject);
    //         $testing = "emails@strastic.com";
    //         $email->addto($testing, "Hello There");
            
    //         $email->addContent("text/html", $my_bc_msg);
    //         $sendgrid = new \SendGrid('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');
    
    //         foreach ( $singleChunk as $personalization_r ) {
    //             $email->addPersonalization( $personalization_r );
    //         }
            
            
    //         try {
    //             $response = $sendgrid->send($email);
                
    //             $data = array(
    //                 'status' => 200,
    //                 'message' => "Broadcast Mail Successfully Sent"
    //             );
                
    //         } catch (Exception $e) {
    //             $data = array(
    //                 'status' => 400,
    //                 'message' => "Error" .$response->statusCode(). "Mail Could Not send"
    //             );
    //         }
            
                
        
    //     }
        
        
        
        
    
    // }else{
    //   $data = array(
    //         'status' => 401,
    //         'message' => "Sorry, you have reached your Email Broadcast limit for this month."
    //     );  
    // }
    
    
    // $data = array(
    //     'status' => 401,
    //     'message' => "Selected -> ".$list_count
    // );
    
    
    

    
}else{
    $data = array(
        'status' => 400,
        'message' => 'Error! Please fill all neccessary Form Fields'
    );
}

header("Content-type: application/json");
echo json_encode($data);
die;