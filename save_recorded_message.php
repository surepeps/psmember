<?php
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

    
       
           if($SpeechResult!=''){
           
            
         

           $new_record = [
            'from_number' =>$From,
            'to_number' =>$To,
            'recorded_message' =>addslashes($SpeechResult),
            'recorded_date' => date('Y-m-d h:i:s')
           
            
        ];
        
        $queryrecord = insertRow('recorded_message', $new_record); 
      
        
        
        if($sqlConnect->query($queryrecord)) {
            
        }
    }


echo $response;
?>
