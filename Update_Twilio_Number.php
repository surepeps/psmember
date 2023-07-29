<?php

global $wo, $sqlConnect;

require_once('config.php');

require_once('assets/init.php');
// Include the bundled autoload from the Twilio PHP Helper Library
require_once('twilio-php-master/src/Twilio/autoload.php');

use Twilio\Rest\Client;

// Find your Account SID and Auth Token at twilio.com/console
// and set the environment variables. See http://twil.io/secure

$account_sid = $wo['config']['sms_twilio_username'];
$auth_token  = $wo['config']['sms_twilio_password'];
$twilio = new Client($account_sid, $auth_token);




//Search IncomingPhoneNumber of account



$incomingPhoneNumbers = $twilio->incomingPhoneNumbers
                               ->read([], 1000);

foreach ($incomingPhoneNumbers as $record) {
    print($record->sid);

    $twilio->incomingPhoneNumbers($record->sid)
    ->update([
                 
        "voiceUrl" => $site_url."/incomming_call.php",
        "smsUrl" => $site_url."/incomming_sms.php"

             ]
    );
}






