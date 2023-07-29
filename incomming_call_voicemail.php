<?php
global $wo, $sqlConnect;

require_once('config.php');

require_once('assets/init.php');
// Include the bundled autoload from the Twilio PHP Helper Library
require_once('twilio-php-master/src/Twilio/autoload.php');
use Twilio\TwiML\VoiceResponse;

$response = new VoiceResponse();

$twilio_number  = substr($_REQUEST['To'],1);


$voicemail_data = getTableData('voicemail', ['twilio_number' => $twilio_number], 1);




foreach($_REQUEST as $key =>$value)
{

    $$key = $value;
}
if($_REQUEST['DialCallStatus']=='no-answer'){
 
// If no input was sent, use the <Gather> verb to collect user input
$gather = $response->gather(['speechTimeout' => "10",'input'=>"speech",'action'=>$site_url.'/save_recorded_message.php']);
// use the <Say> verb to request input from the user
//echo $voicemail_data['text_voice_mail'];
if($voicemail_data['audio_voice_mail']){
     $gather->play($ru."/assets/media/audio/".$voicemail_data['audio_voice_mail']);
    }else{
 $gather->say($voicemail_data['text_voice_mail'],['voice' => 'woman']);
    }

}

echo $response;
?>
