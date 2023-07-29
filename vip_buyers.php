<?php

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php'); 
require_once('assets/init.php');

require_once('sendgrid-php/sendgrid-php.php');
use SendGrid\Mail\Personalization;
use SendGrid\Mail\To;


// ADD NEW VIP BUYERS.
if( isset($_POST['action']) && ($_POST['action'] == "addVipBuyers") ) {
    
    
    // FORM FIELDS
    $home_buyer = $_POST['home_buyer'];
	$send_all_properties = $_POST['send_all_properties'];
	$prop_type = $_POST['prop_type'];
	$city = $_POST['city'];
	$buyer_email = $_POST['buyer_email'];
	$buyer_phone_number = $_POST['buyer_phone_number'];
	$zip_code = $_POST['zip_code'];
	$send_to_buyer = $_POST['send_to_buyer'];
	$prop_rooms = $_POST['prop_rooms'];
	$amount1 = $_POST['amount1'];
	$amount2 = $_POST['amount2'];
	$prop_purchase_type = $_POST['prop_purchase_type'];
	$fund_proof = $_POST['fund_proof'];
	$fund_available = $_POST['fund_available'];
	$buying_strategy = $_POST['buying_strategy'];
	$date = date('Y-m-d H:i:s');
	$upload_file_name = $_POST['upload_file_name'];
	$prop_bathroom = $_POST['prop_bathroom'];
	$buyer_name = $_POST['buyer_name'];
    
    
}