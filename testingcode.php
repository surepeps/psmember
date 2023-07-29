<?php

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

// require_once('sendgrid-php/sendgrid-php.php');
// use SendGrid\Mail\Personalization;
// use SendGrid\Mail\To;

// Update the path below to your autoload.php,
// see https://getcomposer.org/doc/01-basic-usage.md
require_once 'assets/libraries/twilio/vendor/autoload.php';

use Twilio\Rest\Client;

// Find your Account SID and Auth Token at twilio.com/console
// and set the environment variables. See http://twil.io/secure
// $sid = getenv("TWILIO_ACCOUNT_SID");
// $token = getenv("TWILIO_AUTH_TOKEN");

$sid = "AC320b0875fbb7330a67f422502ed34089";
$token  = "ad2303a60eb3a127ab166837cd5fbb0f";
$twilio = new Client($sid, $token);

// $local = $twilio->availablePhoneNumbers("US")->local->read(["areaCode" => 402], 20);

// foreach ($local as $record) {
//     print($record->friendlyName);
// }

$message = $twilio->messages->create("8506912713", ["body" => "Hi there Miquelle", "from" => "4025321513"]);

print($message->sid);

// $emails = array("gatukurh1@gmail.com","hassangatukurh@gmail.com","tijani@dervac.com");
// foreach($emails as $em){
   
//   $personalization = new Personalization();
//     $personalization->addTo( new To( $em ) );
//     $sendgridPersonalization[] = $personalization; 
    
// }

// $chunkedUsers = array_chunk($sendgridPersonalization, 1000, true);

// foreach($chunkedUsers as $singleChunk){
    
//     // $subject = "Hello There";
//     // $mybname = "Hassan To All";
//     // $Send_Email = "info@propertysalers.com";
    
//     // $my_bc_msg = "Just checking stuff out my guys";
    
//     // $email = new \SendGrid\Mail\Mail(); 
//     // $email->setfrom($Send_Email, $mybname);
//     // $email->setSubject($subject);
//     // $testing = "emails@propertysalers.com";
//     // $email->addto($testing, "Hello There");
    
//     // $email->addContent("text/html", $my_bc_msg);
//     // $sendgrid = new \SendGrid('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');
    
//     foreach ( $singleChunk as $personalization_r ) {
//         echo "<pre>";
//         print_r($personalization_r);
//         echo "</pre>";
        
//         // $email->addPersonalization( $personalization_r );
//     }


//     try {
//         $response = $sendgrid->send($email);
        
//         // Update each cronrecords as done
//         // UpdateCronRecordAsDone($corecodID);
        
//         echo "sent successfully ";
//         die();
        
//     } catch (Exception $e) {
//         // $data = array(
//         //     'status' => 400,
//         //     'message' => "Error" .$response->statusCode(). "Mail Could Not send"
//         // );
        
//         echo "Error while sendind";
//         die();
        
//     }


    
// }



