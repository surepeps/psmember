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
    'RecordingUrl' => $RecordingUrl,
    'CallStatus' => 'Completed',
    'CallDuration' => $RecordingDuration,
    'parentcallsid' => '_'.$CallSid
];

$where = [
    'parentcallsid' => $CallSid
];


$query = updateRow('all_calls', $data, $where);
if(!$sqlConnect->query($query)) {
    pre(mysqli_error($sqlConnect)); exit; 
} 
echo $response;
?>
