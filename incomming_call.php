<?php

global $wo, $sqlConnect;

require_once('config.php');

require_once('assets/init.php');

clearstatcache();
// Include the bundled autoload from the Twilio PHP Helper Library
require_once('twilio-php-master/src/Twilio/autoload.php');

use Twilio\TwiML\VoiceResponse;
use Twilio\Rest\Client;
$response = new VoiceResponse();


foreach($_REQUEST as $key =>$value)
{

    $$key = $value;
}

$twilio_number  = $_REQUEST['To'];

$user_data = getJoinOrderByChatData('lcn_table','t LEFT JOIN `Wo_Users` u on t.`user_id`= u.`user_id` where t.`number`='. "'".$twilio_number."'",1);

$forwards_data = getJoinOrderByChatData('lcn_table','t LEFT JOIN `forwarding_number` f on t.`id`= f.`lcn_id` where t.`number`='. "'".$twilio_number."'",);


$client =  str_replace(" ","_", $user_data['first_name']).'_'.str_replace(" ","_",$user_data['last_name']);




$list = getTableData('lcn_table', ['number' => $twilio_number], 1);



if($forwards_data[0]['phone_number'] !=''){

        $dial = $response->dial('',['record' => 'record-from-answer-dual','timeout'=>'10','action'=>$site_url.'/incomming_call_voicemail.php','recordingStatusCallback' => $site_url.'/incomming_update.php']);
        //$dial->number('+15558675310');
        
      foreach($forwards_data as $key => $value)
        {
        
            
        
                $dial->number($value['phone_number'],['statusCallbackEvent'=>'answered','statusCallback' => $site_url.'/incomming_save.php']);
        
        }
}else{

        $dial = $response->dial('',['record' => 'record-from-answer-dual','timeout'=>'10','action'=>$site_url.'/incomming_call_voicemail.php','recordingStatusCallback' => $site_url.'/incomming_update.php']);
        //$dial->number('+15558675310');
        $dial->client($client,['statusCallbackEvent'=>'answered','statusCallback' => $site_url.'/incomming_save.php']);
        
        
}


echo $response;
