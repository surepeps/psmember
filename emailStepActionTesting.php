<?php

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php'); 
require_once('assets/init.php');

require_once('sendgrid-php/sendgrid-php.php');
use SendGrid\Mail\Personalization;
use SendGrid\Mail\To;

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);

// TEST (EMAIL) ACTION STEP.. 
if( isset($_POST['action']) && ($_POST['action'] == "testEmailStepAction") ) {
    
    if(isset($_POST['subject'])){
        $subject = $_POST['subject'];
    }
    
    if(isset($_POST['testEmail'])){
        $testEmail = $_POST['testEmail'];
    }
    
    if(isset($_POST['message'])){
        $message = $_POST['message'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
    
    if($user_id === $wo['user']['user_id'] ){
    
        $email = new \SendGrid\Mail\Mail(); 
        $testing = "emails@strastic.com";
        $mybname = "yhnjjj";
        $email->setfrom($testing, $mybname);
        $email->setSubject($subject);
        
        $email->addto($testEmail, $mybname);
                
        $email->addContent("text/html", $message);
        
        $sendgrid = new \SendGrid('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');
        
        try {
            
            $response = $sendgrid->send($email);
            
            $data = array(
                'status' => 200,
                'message' => "Mail Successfully Sent",
				'result' => $response
            );
            
        } catch (Exception $e) {
            $data = array(
                'status' => 400,
                'message' => "Error" .$response->statusCode(). "Mail Could Not send"
            );
        }
        
    }else{
        
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Unautorized Action',
        	);
        	
    }
    
    
    
    
    	
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
}