<?php
error_reporting(E_ALL);
ini_set("display_errors",1);
require_once 'sendgrid-php/sendgrid-php.php';



$email = new \SendGrid\Mail\Mail(); 
$email->setFrom("info@strastic.com", "Example User");
$email->setSubject("4444 Sending with Twilio SendGrid is Fun");
$email->addTo("gatukurh1@gmail.com", "Example User");
$email->addContent(
    "text/html", "<strong>and easy to do anywhere, even with PHP</strong>"
);
$sendgrid = new \SendGrid('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');
try {
    $response = $sendgrid->send($email);
    print $response->statusCode() . "\n";
    print_r($response->headers());
    print $response->body() . "\n";
} catch (Exception $e) {
    echo 'Caught exception: '. $e->getMessage() ."\n";
}