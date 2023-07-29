<?php 
global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');


clearstatcache();
// Include the bundled autoload from the Twilio PHP Helper Library
require_once('twilio-php-master/src/Twilio/autoload.php');
use Twilio\TwiML\VoiceResponse;

$response = new VoiceResponse();
foreach($_REQUEST as $key =>$value)
{

    $$key = $value;
}


$user_id = getUserIdFromCallidAndLcn($CallSid);

$timeconv = $DialCallDuration/60;
$amoutnD = $timeconv * 0.01;
reduceWalletBalance_A($amoutnD,$user_id);


$data = [
    'RecordingUrl' => $RecordingUrl,
    'CallStatus' => $DialCallStatus,
    'CallDuration' => $DialCallDuration
];

$where = [
    'callsid' => $CallSid
];


$query = updateRow('all_calls', $data, $where);

if(!$sqlConnect->query($query)) {
    pre(mysqli_error($sqlConnect)); exit; 
} 




echo $response;


 ?>


