<?php
global $wo, $sqlConnect;

require_once('config.php');

require_once('assets/init.php');

// Include the bundled autoload from the Twilio PHP Helper Library
require_once('twilio-php-master/src/Twilio/autoload.php');
use Twilio\TwiML\VoiceResponse;

$response = new VoiceResponse();

foreach($_REQUEST as $key =>$value)
{

    $$key = $value;
}



$data = [
    'from_number' => $From,
    'callsid' => $CallSid,
    'parentcallsid' => $ParentCallSid,   
     'to_number' => $CalledVia,
    'RecordingUrl' => $RecordingUrl,
    'CallStatus' => $CallStatus,
    'direction' =>"Inbound",
    'CallDuration' => $CallDuration,
    'receive_date' => date('m-d-Y')
];
$query = insertRow('all_calls', $data); 
if($sqlConnect->query($query)) {
    
}



echo $response;