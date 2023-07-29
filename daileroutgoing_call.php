<?php
global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');


clearstatcache();

// Include the bundled autoload from the Twilio PHP Helper Library
require_once('twilio-php-master/src/Twilio/autoload.php');
use Twilio\TwiML\VoiceResponse;
$response = new VoiceResponse;


// get the phone number from the page request parameters, if given
if (isset($_POST['PhoneNumber'])) {
    
   foreach($_REQUEST as $key =>$value)
   {
   
       $$key = $value;
   }

   $PhoneNumber =  US_formate($PhoneNumber);
    $data = [
       'from_number' => $CallerId,
       'callsid' => $CallSid, 
        'to_number' => $PhoneNumber,  
       'direction' =>"Outbound",  
       'receive_date' => date('m-d-Y')
    ];
    $query = insertRow('all_calls', $data); 
    
    if($sqlConnect->query($query)) {
       
    }

    
    $response = new VoiceResponse();
    //$dial = $response->dial('', ['callerId' => $CallerId,'record' => 'record-from-answer-dual','recordingStatusCallbackEvent' => 'complete','recordingStatusCallback' => $site_url.'/DailerCallAction.php']);
    $dial = $response->dial('',['callerId' => $CallerId,'record' => 'record-from-answer-dual','timeout'=>'10','action'=>$site_url.'/DailerCallAction.php']);
          
    $dial->number($PhoneNumber);
    
    echo $response;
}
   ?>
   