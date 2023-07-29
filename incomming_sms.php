<?php

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

// $user_id = $wo['user']['user_id'];
// Include the bundled autoload from the Twilio PHP Helper Library
require_once('twilio-php-master/src/Twilio/autoload.php');
use Twilio\TwiML\MessagingResponse;

$response = new MessagingResponse();
foreach($_REQUEST as $key =>$value)
{

    $$key = $value;
}

$userdata = getTableData('lcn_table', ['number' => $To], 1);
$user_id  = $userdata['user_id'];

$From  =  substr($From, 2);
$lcnNumber = $From;

$To  =  substr($To, 2);

$contactdata = getTableData('contact', ['mobile' => $From], 1);


if(empty($contactdata)){
  
    $new_contact = [
        'firstname' =>"Unknown",
        'lastname' =>"Unknown",
        'email' => "",
        'mobile' =>$From
        
    ];

    $queryconact = insertRow('contact', $new_contact); 
    if($sqlConnect->query($queryconact)) {
        $contactdata = getTableData('contact', ['id' => $sqlConnect->last_insert_id], 1);
    }

}


$data = [
    'from_number' => $From,
    'sms_text' => $Body,
    'to_number' => $To,
    'user_id' => $user_id,
    'status' => "useen",
    'direction' =>'inbound',
    'm_time' => time(),
    'receive_date' => date("m d Y h:i:s A")
];
$query = insertRow('all_sms', $data);  
if($sqlConnect->query($query)) {
    
    //Insert the notification for incoming sms
    $sender = $contactdata['firstname'] . ' ' . $contactdata['lastname'];
    // $sender = addslashes(json_encode($contactdata));
    $notificationData = [
        'notifier_id' => $contactdata['id'],
        'recipient_id' => $user_id,
        'type' => 'incomming_sms',
        'text' =>   $sender . ' sent you a new message',
        'url' => $wo['site_url'] . "/conversations?lcn={$To}&rec={$lcnNumber}",
        'time' => time()
    ];
    
    $notifierQuery = insertRow(T_NOTIFICATION, $notificationData);
    $sqlConnect->query($notifierQuery);

}

echo $response;


