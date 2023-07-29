<?php
    
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    
    
    require_once("assets/init.php");
    
    $from = "fashioninninternational@gmail.com";
    $to = 'surprise001.AA@gmail.com';
    $bcc = [
        'frasool959@gmail.com',
        'hammadfaiz12435@gmail.com'
    ];
    $subject = "Aftab testing email";
    $message = "this is a test email"; 
    
    sendBulkEmailToUser($from, $to, $bcc, $subject, $message);