<?php 
global $wo, $sqlConnect;

require_once('config.php');

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

if($CallStatus =='initiated'){

    //Run the code
}
if($CallStatus =='ringing'){

    //Run the code
}
if($CallStatus =='answered'){

    //Run the code
}
if($CallStatus =='completed'){

    //Run the code
}




echo $response;


 ?>


