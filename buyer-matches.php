<?php

$root  = __DIR__;

require_once('assets/init.php'); 

global $wo, $sqlConnect;

$user = null;

if(isset($wo['user'])){
    $user = $wo['user'];
}

$action = filter('action');

$error = "";


if($action == "SendSMSBulk" ){

    $contacts_ids = filter('contacts_ids');
    $message = filter('message');
    $from_phone = filter('from_phone');
    $status = $count = 0;
    $property_id = filter('property_id');

    if(!$message) {
        $resp = "Please enter a valid message";
    }else if(!$from_phone) {
        $resp = "Please select a valid from phone number.";
    }else if(count($contacts_ids) == 0) {
        $resp = "Please select at least one contact to send sms.";
    }else if(!$property_id) {
        $resp = "You have not choosed any property!";
    }else{
        
        $property = getTableData(T_LISTINGS, ['id' => $property_id], 1);
        
        
        foreach($contacts_ids as $id)  {
            
            $contact = getTableData(T_CONTACT, ['id' => $id], 1);
            $message = getConvertedMessage($property, $contact, $user, $message);
 
 
            if($contact && !empty($contact['mobile'])) {
                $to_phone = $contact['mobile'];
                
                
                
                /** Send SMS */
                $sendresponse = send_bulk_sms_broadcast($to_phone, $from_phone, $message);
                if($sendresponse) {
                    $count ++;
                }
            }
        }
        if($count) {
            $resp = "Sms has been sent to {$count} items successfully";
            $status = 1;
        }else{
            $resp = "The selected items doesn't contain the phone.";
        }
        
    }
    

    $data = [
        'status' => $status,
        'message' => $resp
    ];
}else if($action == "SendEMAILBulk" ){

    
    $contacts_ids = filter('contacts_ids');
    $message = filter('message');
    $from_email = filter('from_email');
    $from_name = filter('from_name');
    $subject = filter('subject');
    $property_id = filter('property_id');
    $status = $count = 0;

    if(!$from_email) {
        $resp = "Please enter a valid from email.";
    }else if(!$subject) {
        $resp = "Please enter a valid subject.";
    }else if(!$message) {
        $resp = "Please enter a valid message.";
    }else if(!$property_id) {
        $resp = "You have not choosed any property!";
    }else if(count($contacts_ids) == 0) {
        $resp = "Please select at least one contact to send sms.";
    }else{

        $property = getTableData(T_LISTINGS, ['id' => $property_id], 1);
    
     
        foreach($contacts_ids as $id)  {
            

            $contact = getTableData(T_CONTACT, ['id' => $id], 1);
            
            if($contact && !empty($contact['email'])) {

                $message = getConvertedMessage($property, $contact, $user, $message);
                $to_email = $contact['email'];
                
                $send_message_data = array(
                    'from_email' => $from_email,
                    'from_name' => $from_name,
                    'to_email' => $to_email,
                    'to_name' => $contact['firstname'] . ' ' . $contact['lastname'],
                    'subject' => $subject,
                    'is_html' => true,
                    'charSet' => 'utf-8',
                    'message' => $message
                );
                
                if(sendSandGridEmail($send_message_data)) {
                    $count ++;
                }
            }
        }

        if($count) {
            $resp = "Email has been sent to {$count} contact successfully";
            $status = 1;
        }else{
            $resp = "The selected items doesn't contain the email.";
        }
    }

    $data = [
        'status' => $status,
        'message' => $resp
    ];
}


header("Content-type: application/json");
echo json_encode($data);
die();   


function getConvertedMessage($property, $contact, $user, $message){
    
    $tab = json_decode($property['tab1'], 1);

    $filters = [
        'UserFirstName' => $user['first_name'],
        'LCN' => '',
        'PropertyAddress' => $tab['entered_address'],
        'PropertyTitle' => $tab['listing_title'],
        'PropertyBeds' => $tab['beds'],
        'PropertyBaths' => $tab['baths'],
        'PropertySize' => $tab['property_size'],
        'BuyerName' => $contact['firstname'],
        'BuyerEmail' => $contact['email'],
        'BuyerPhone' => $contact['mobile'],
        'BuyerAreaOfInterest' => $contact['city'],
    ];

    foreach($filters as $key => $filter) {
        $replace = "~{$key}~";
        $message = str_replace($replace, $filter, $message);
    }

    return $message; 
}
