<?php


header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET, POST');

header("Access-Control-Allow-Headers: X-Requested-With");


global $wo, $sqlConnect;
$root = __DIR__;

require_once($root.'/config.php');
require_once('assets/init.php');

$con = $wo['sqlConnect'];


// Make contact Message
if( isset($_POST['action']) && ($_POST['action'] == "make_new_contact") ) {
    

    if(isset($_POST['name'])){
        $name = $_POST['name'];
    }
    
    if(isset($_POST['phone'])){
        $phone = $_POST['phone'];
    }
    
    if(isset($_POST['email'])){
        $email = $_POST['email'];
    }
    
    if(isset($_POST['user_type'])){
        $user_type = $_POST['user_type'];
    }
    
    if(isset($_POST['message'])){
        $message = $_POST['message'];
    }
    
    if(isset($_POST['listing_id'])){
        $listing_id = $_POST['listing_id'];
    }
    
    if(isset($_POST['owner_id'])){
        $owner_id = $_POST['owner_id'];
    }
    
    if(isset($_POST['buyer_id'])){
        $buyer_id = $_POST['buyer_id'];
    }
    
    if(isset($_POST['c_source'])){
        $c_source = $_POST['c_source'];
    }
    
    $status = 1;

    // miquelle@propsaler.com
    /** Send Sms to Owner of Property */
    $owner = getTableData(T_USERS, ['user_id' => $owner_id], 1);

    $queryS =  "INSERT INTO My_Contact_Info (prop_id,owner_id,buyer_id,buyer_name,buyer_phone,buyer_email,buyer_type,message,source,status) VALUES ($listing_id,$owner_id,$buyer_id,'$name','$phone','$email','$user_type','$message','$c_source',$status)";
    $query = mysqli_query($con,$queryS);

    if($query){


        // Get Property address
        $prop_Address = get_listing_by_specific_colm("tab1","entered_address",$listing_id);


        $from_number = $wo['config']['sms_t_phone_number'];
        if($owner && $owner['phone_number'] && $from_number){
            $smsText = "
                You have recieved a new message from ({$prop_Address}). \nMessage: {$message} \n
                Name: {$name}\n
                Email: {$email}\n
                Phone: {$phone}\n
            ";
            $sendresponse = send_bulk_sms_broadcast(US_formate($owner['phone_number']), US_formate($from_number), $smsText);
        }

        // Send Email To Owner of property...
		if ($wo['config']['emailNotification'] == 1) {
            
            $send_message_data = array(
                'from_email' => $wo['config']['siteEmail'],
                'from_name' => $wo['config']['siteName'],
                'to_email' => $email,
                'to_name' => $name,
                'subject' => "You have a new message from ({$prop_Address})",
                'charSet' => 'utf-8',
                'message_body' => "
                    Name: {$name}<br>
                    Email: {$email}<br>
                    Phone: {$phone}<br>
                    Type of user: {$user_type}<br>
                    Message: {$message}
                ",
            );

            
            if ($wo['config']['smtp_or_mail'] == 'smtp') {
                $send_message_data['insert_database'] = 1;
            }
            
            Wo_SendMessage($send_message_data);
            
            // sendEmailToUser(
            //     $wo['config']['siteEmail'], 
            //     [$owner['email']], 
            //     "You have a new message from ({$prop_Address})",
            //     "
            //         Name: {$name}<br>
            //         Email: {$email}<br>
            //         Phone: {$phone}<br>
            //         Type of user: {$user_type}<br>
            //         Message: {$message}
            //     "
            // );
                
        }
       $data = array(
    		'status' => 200,
    		'message' => 'Success',
    	); 
    	
    }else{
        
        $data = array(
    		'status' => 400,
    		'message' => 'Error',
    	);
    	
    }
    
    
    		
    	
    header("Content-type: application/json");
    echo json_encode($data);
    die();
}


// Create New Offer
if( isset($_POST['action']) && ($_POST['action'] == "make_new_offer") ) {
    
	$offer_price = $_POST['offer_amt'];
	$offer_emb_price = $_POST['emd_amt'];
	$cash_type = $_POST['cash_type'];
	$name = $_POST['name'];
	$company = $_POST['company'];
	$action = $_POST['action'];
	$listing_id = $_POST['listing_id'];
	$owner_id = $_POST['owner_id'];
	$lender_name = $_POST['lender_name'];
	
	$first_signerEmail = $_POST['buyer_email_o'];
    $first_signerPhone = $_POST['buyer_phone_o'];
	
	$user_id = $_POST['buyer_id'];
	$source = $_POST['source'];
	  
	
	$offer_stage = 1;
	$second_signerEmail = $_POST['second_signerEmail'];
	$second_signername = $_POST['second_signername'];
	
	$buyer_id = $_POST['buyer_id'];
	$property_id = $_POST['listing_id'];
	$seller_id = $_POST['owner_id'];
	$id = $_POST['listing_id'];


	$query = mysqli_query($con, "SELECT `Wo_Listing`.*,`Wo_Listing_Meta`.property_desc FROM `Wo_Listing` LEFT JOIN  `Wo_Listing_Meta` ON `Wo_Listing_Meta`.property_id=`Wo_Listing`.id WHERE `Wo_Listing`.id=$id");

	$row = mysqli_fetch_array($query);

	$propertydesc = $row['description'];
	$tab1 = json_decode($row["tab1"]);

	$property_id = $row['id'];
	$prop_title = $tab1->listing_title;
	$property_map_address = $tab1->entered_address;
	$offer_initial_price = $tab1->flip_price;

	$notifier_id = $user_id;
	$author_id = $row['user_id'];
	$seller_id = $author_id;
	$recipient_id = $seller_id;

	$countData = mysqli_query($con, 'SELECT (count(*) + 1)  as c from Wo_offers');

	$offersData = mysqli_fetch_assoc($countData);
	$offer_closing_date = "1970-01-01";
	$expiration_offer_date = "1970-01-01";
	$message_id = "1";
	$counter_id = "0";
	$customer_id = $_POST['customer_id'];

	$offers_details = json_encode([
		"proeprty_id" => $property_id,
		"prop_title" => $prop_title,
		"property_map_address" => $property_map_address,
		'cash_type' => $cash_type,
		'name' => $name,
		'company' => $company,
		'buyer_id' => $buyer_id,
		'lender_name' => $lender_name,
		'first_signerEmail' => $first_signerEmail,
		'first_signerPhone' => $first_signerPhone,
		'second_signerEmail' => $second_signerEmail,
		'second_signername' => $second_signername,
		"offer_price" => $offer_price,
		"offer_emd_price" => $offer_emb_price,
		"offer_initial_price" => $offer_initial_price,
		"offer_closing_date" => $offer_closing_date,
		"expiration_offer_date" => $expiration_offer_date,
		"inspection_period" => "0 Days",
		"authorid" => $row['user_id'],
		"source" => $source
	]);
	
	
	
    $contact = getBuyerByEmailAndCustomerID($first_signerEmail, $customer_id);
    
    if($contact){
        
        if($wo['loggedin'] == false){
            
            // check if Non-Registered User already made offer for the property
            $getGuestEmail_nd_propid = mysqli_query($con, "SELECT id as id, COUNT(id) AS myO_Nid FROM `Wo_offers` WHERE `property_id` = $property_id AND `email` = '$first_signerEmail' AND `last_updated_by_user` = 0");
            $gGE_nP = mysqli_fetch_assoc($getGuestEmail_nd_propid);
            
            if($gGE_nP['myO_Nid'] > 0){
                
                $status = "pending";
                $offer_id = $gGE_nP['id'];
                
                $query_update = "UPDATE `Wo_offers` SET  `counter_id` = 0, `customer_id` = '$customer_id', `offer_status` = '$status', `offer_stage` = $offer_stage, `email` = '$first_signerEmail', `offers_details` = '$offers_details', `source` = '$source', `modified_date` = now(), `offer_start_date` = '$offer_closing_date', `offer_end_date` = '$expiration_offer_date'  WHERE id = $offer_id";
                $offerquery = mysqli_query($con, $query_update);
                
            }else{
                $myoffer_o = uniqid();
                
                $query_insert =  "INSERT INTO Wo_offers (property_id,offers_details,offer_status,last_updated_by_user,read_unread_status,seller_id,created_date,modified_date,offer_start_date,offer_end_date,message_id,last_action_by,counter_id,offer_code,source,offer_stage,email, customer_id) VALUES ($id,'$offers_details','pending',$user_id,'unread',$seller_id,now(),now(),'$offer_closing_date','$expiration_offer_date',$message_id,$user_id,$counter_id,'$myoffer_o','$source',$offer_stage,'$first_signerEmail','$customer_id')";
                $offerquery = mysqli_query($con,$query_insert);
                
            
            }
            
        }else{
                
                // check if user already made offer on the samee property
                $getoffer_details = mysqli_query($con, "SELECT id as id, COUNT(id) AS myid FROM `Wo_offers` WHERE property_id = $property_id AND last_updated_by_user = $user_id");
                $off_det = mysqli_fetch_assoc($getoffer_details);
                $count_user_offer = $off_det['myid'];
                
                if ($count_user_offer > 0) {
                    $status = "pending";
                    $offer_id = $off_det['id'];
            
                    $query_update = "UPDATE `Wo_offers` SET  `counter_id` = 0, `offer_status` = '$status', `offer_stage` = $offer_stage, `email` = '$first_signerEmail', `offers_details` = '$offers_details', `source` = $source, `modified_date` = now(), `offer_start_date` = '$offer_closing_date', `offer_end_date` = '$expiration_offer_date'  WHERE id = $offer_id";
                    $offerquery = mysqli_query($con, $query_update);
            
                } else {
                    
                    $myoffer_o = uniqid();
                    $query_insert =  "INSERT INTO Wo_offers (property_id,offers_details,offer_status,last_updated_by_user,read_unread_status,seller_id,created_date,modified_date,offer_start_date,offer_end_date,message_id,last_action_by,counter_id,offer_code,source,offer_stage,email) VALUES ($id,'$offers_details','pending',$user_id,'unread',$seller_id,now(),now(),'$offer_closing_date','$expiration_offer_date',$message_id,$user_id,$counter_id,'$myoffer_o','$source',$offer_stage,'$first_signerEmail')";
                    $offerquery = mysqli_query($con,$query_insert);
                    
                }
                
            }
        
            if ($offerquery) {
                $data = array(
                    'status' => 200,
                    'step' => '1',
                    'message' => 'Step1 Form Saved successfully',
                );
            } else {
            $data = array(
                'status' => 400,
                'message' => 'There are some error',
            );
        }
    }else{
        $data = [
            'message' => "Incorrect Customer ID, please try again.",
            'status' => 404
        ];
    }
	
	echo json_encode($data);
	die;

}


// Check offer
if( isset($_POST['action']) && ($_POST['action'] == "check_m_offer") ){
    
    $email = $_POST['email'];
	$prop_id = $_POST['property_id'];
	$o_user_id = $_POST['user_id'];
	
    // confirm if email and property are matched in the offer table
    $getGuestEmail_nd_propid = mysqli_query($sqlConnect, "SELECT *, COUNT(email) AS myO_Nid FROM `Wo_offers` WHERE property_id = $prop_id AND email = '{$email}' AND last_updated_by_user = $o_user_id");
    $gGE_nP = mysqli_fetch_assoc($getGuestEmail_nd_propid);
    $allo_d = json_decode($gGE_nP['offers_details'],true);
    
    // Check if Offer Is Countered...
    $counter_F_id = $gGE_nP['counter_id'];
    $off_sta = $gGE_nP['offer_status'];
            
    if($counter_F_id > 0){
        $getof_details = mysqli_query($sqlConnect, "SELECT * FROM `Wo_counter_offer` WHERE counter_id = $counter_F_id");
        $coloffer_det = mysqli_fetch_assoc($getof_details);
        $covi_type = $coloffer_det['type'];
        $c_p = $coloffer_det['counter_price'];
        $covi_time = $coloffer_det['my_time'];

        if($off_sta == "counter"){
            $O_S = "Offer Countered For $".number_format($c_p);
            $off_details = " Your Offered Price Request was Countered for : <b>$".number_format($c_p)."</b>";
            $stateColor = 'color: blue; text-align: center; font-weight: 600;';
        }

    }

    if($off_sta == 'rejected'){
        $O_S = "Offer Rejected ";
        $off_details = "Your Offer Request was Rejected You can try again"; 
        $stateColor = 'color: red; text-align: center; font-weight: ;';
    }

    if($off_sta == 'pending'){
        $O_S = "Offer Still Pending";
        $off_details = "Your Offer Request is Pending Please hold on while the property author respond to your request soon"; 
        $stateColor = 'color: black; text-align: center; font-weight: 600;';
    }

    if($off_sta == 'accepted'){
        $O_S = "Offer Accepted";
        $off_details = "Your Offer Request has been sent and is in pending status"; 
        $stateColor = 'color: green; text-align: center; font-weight: 600;';
    }
    
    if($gGE_nP['myO_Nid'] > 0){
        $data = array(
			'status' => 200,
			'message' => 'Offer details found already',
			'stage' => $gGE_nP['offer_stage'],
			'offer_p' => $allo_d['offer_price'],
            'emd_p' => $allo_d['offer_emd_price'],
            'email' => $allo_d['first_signerEmail'],
            'phone' => $allo_d['first_signerPhone'],
            'name' => $allo_d['name'],
            'source2' => $gGE_nP['source'],
            'company' => $allo_d['company'],
            'type' => $allo_d['cash_type'],
            'lender' => $allo_d['lender_name'],
            'state' => $O_S,
            'state_details' => $off_details,
            'offer_status' => $off_sta,
            'state_color' => $stateColor
		);
    }else{
        $data = array(
			'status' => 400,
			'message' => 'Not Found',
		);
    }
    
    
    
    
    $pinCode = getBuyerPinByEmail($email);
    if($pinCode){
        $data['customer_id'] = $pinCode['pin_code'];
        $data['customer_id_found'] = 1;
    }else{
        $data['customer_id'] = 0;
        $data['customer_id_found'] = 0;
    }
    
    header("Content-type: application/json");
    echo json_encode($data);
    die();
}


// Check Visit 
if( isset($_POST['action']) && ($_POST['action'] == "check_m_visit") ){
    
    $email = $_POST['email'];
	$prop_id = $_POST['property_id'];
	$v_user_id = $_POST['user_id'];
	
    // confirm if email and property are matched in the offer table
    $getGuestEmail_nd_propid_v = mysqli_query($sqlConnect, "SELECT *, COUNT(email) AS myV_Nid FROM `Wo_Schedule_Visits` WHERE property_id = $prop_id AND email = '$email' AND user_id = $v_user_id");
    $allo_d = mysqli_fetch_assoc($getGuestEmail_nd_propid_v);
    


    if($allo_d['myV_Nid'] > 0){
        // split date and time
        $buyerdate = $allo_d['visit_date'];
        $buyertime = $allo_d['visit_time'];
        
        $month_s = date('F',strtotime($buyerdate));
        $day_s = date('d', strtotime($buyerdate));
        $year_s = date('Y', strtotime($buyerdate));
        $time_t_s = date('h:i a', strtotime($buyertime));
        
        $count_id = $allo_d['counter_id'];
        
        if($count_id > 0 && $allo_d['visits_status'] == 'countered'){
                
            $getvc_details = mysqli_query($sqlConnect, "SELECT * FROM `Wo_counter_visit` WHERE counter_id = $count_id");
            $couvisit_det = mysqli_fetch_assoc($getvc_details);
            $covi_type = $couvisit_det['type'];
            $covi_date = $couvisit_det['my_date'];
            $covi_time = $couvisit_det['my_time'];
    
            if($covi_type == 1){
                $counter_details = " Your Date Visit Request was Countered for : <b>".$covi_date."</b>";
            }
    
            if($covi_type == 2){
                $counter_details = " Your Time Visit Request was Countered By : <b>".date('h:i a', $covi_time)."</b>";
            }
    
            if($covi_type == 3){
                $counter_details = " Your Time and Date Visit Request was Countered for : <b>".$covi_date. " And By ".$covi_time."</b>";
            }
        }
    
        $data = array(
            
			'status' => 200,
			'message' => 'Schedule Visit details found already',
			's_email' => $allo_d['email'],
            's_name' => $allo_d['name'],
            's_uname' => $allo_d['uname'],
            's_source2' => $allo_d['source'],
            's_phone' => $allo_d['phone'],
            's_message' => $allo_d['stitle'],
            's_date' => $allo_d['visit_date'],
            's_time' => $allo_d['visit_time'],
            's_status' => $allo_d['visits_status'],
            's_c_message' => $counter_details,
            's_m' => $month_s,
            's_y' => $year_s,
            's_d' => $day_s,
            's_t' => $time_t_s
            
		);
    }else{
        $data = array(
			'status' => 400,
			'message' => 'Not Found',
		);
    }
    
    
    
    
    $pinCode = getBuyerPinByEmail($email);
    if($pinCode){
        $data['customer_id'] = $pinCode['pin_code'];
        $data['customer_id_found'] = 1;
    }else{
        $data['customer_id'] = 0;
        $data['customer_id_found'] = 0;
    }
    
    header("Content-type: application/json");
    echo json_encode($data);
    die(); 
    
}


// Download contract
if( isset($_POST['action']) && ($_POST['action'] == "download_contract") ){
    
    if(isset($_POST['email']) && isset($_POST['prop_id'])){
        $p_id = $_POST['prop_id'];
        $e_of = $_POST['email'];
        $user_id = isset($wo['user']['user_id']) ? $wo['user']['user_id'] : 0;
        
        $contFile = get_filedoc_by_specific_colm("accept_docs","upload_contract",$p_id,$e_of,$user_id);
        
        $fileDir = pathinfo($contFile,PATHINFO_DIRNAME);
        $exfileName = pathinfo($contFile,PATHINFO_BASENAME);
        $fileEx = pathinfo($contFile,PATHINFO_EXTENSION);
        
        $title_prop = get_listing_by_specific_colm("tab1","listing_title",$p_id);
        
        // Rename the contract file as the property title name
        $nFN = $title_prop.'.'.$fileEx;
        
        
        $data = array(
			'status' => 200,
			'message' => 'Contract Document Downloaded',
			'contFile' => $contFile,
			'contFileName' => $nFN,
            'email' => $e_of,
		);
    }else{
        $data = array(
            'status' => 400,
            'message' => "Error Invalid Offer Details"
        );
        
    }
    
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
}


// Make schedule visit
if( isset($_POST['action']) && ($_POST['action'] == "make_new_schedule_visit") ){
    
    $l_id = $_POST['listing_id'];

    $buyer_id = $_POST['buyer_id'];
    $date_request = $_POST['schedule_date'];
    $time_request = $_POST['schedule_time'];
    $seller_id = $_POST['owner_id'];
    $message = $_POST['message'];
    
    $fname = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $uname = $_POST['username'];
    $source = $_POST['source'];
    $sch_status = "pending";
    $subject = "Schedule Visit Nofication";
    $template = "schedule";
    $customer_id = $_POST['customer_id'];
    
    if($l_id == 0){
        return false;
    }
    
    if($seller_id == 0){
        return false;
        
    }
    
    // Author Data
    $author_d = Wo_UserData($seller_id);
    $autho_name = $author_d['name'];
    $author_email = $author_d['email'];
    $autho_uname = $author_d['username'];
    $autho_phone = $author_d['phone_number'];
    
    $pDetails = getAll_listings_by_id($l_id);
    $p_query = mysqli_fetch_assoc($pDetails);
    $checkAvail = $p_query['p_idC'];
    
    if($checkAvail > 0){
        
        $contact = getBuyerByEmailAndCustomerID($email, $customer_id);
        
    
        if($contact){

            // Get Property details
            $prop_Address = get_listing_by_specific_colm("tab1","entered_address",$l_id);
            
            $title_prop = get_listing_by_specific_colm("tab1","listing_title",$l_id);
            
            $internal_msg2_link = $wo['config']['site_url']."/my-schedule-visit?search=".urlencode(Wo_PropertyNameFromId($l_id));
            
            
            $getvisit_details = mysqli_query($sqlConnect, "SELECT *, COUNT(sid) AS mynid FROM `Wo_Schedule_Visits` WHERE property_id = $l_id AND email = '$email' AND user_id = $buyer_id");
            $vis_det = mysqli_fetch_assoc($getvisit_details);
            $count_user_visit = $vis_det['mynid'];
            $vis_id = $vis_det['sid'];
            // INSERT SCHEDULE VISIT DETAILS INTO DATABASE
            if($count_user_visit < 1){
                $schedulequery = mysqli_query($sqlConnect,"INSERT INTO `Wo_Schedule_Visits` (`property_id`,`user_id`,`property_author`,`visit_date`,`visit_time`,`stitle`,`name`,`uname`,`email`,`phone`,`source`,`visits_status`,`created_date`,`modified_date`,`customer_id`) VALUES($l_id,$buyer_id,$seller_id,'$date_request','$time_request','$message','$fname','$uname','$email','$phone','$source','$sch_status',now(),now(), '$customer_id' )");
            }else{
                $schedulequery = mysqli_query($sqlConnect,"UPDATE `Wo_Schedule_Visits` SET `property_id` = $l_id,  `customer_id` = $customer_id, `user_id` = $buyer_id, `property_author` = $seller_id,`visit_date` = '$date_request',`visit_time` = '$time_request',`stitle` = '$message',`name` = '$fname',`uname` = '$uname',`email` = '$email',`phone` ='$phone',`source` = '$source',`visits_status` = '$sch_status', `counter_id` = 0, `modified_date` = now() WHERE sid = $vis_id ");
            }
            
            if($schedulequery){
                
                //Email for seller.......
                if ($wo['config']['emailNotification'] == 1) {
                    
                    if (isset($author_email)) {
                        
                        $wo['offerNoti']['name'] = $fname;
                        $wo['offerNoti']['uname'] = $uname;
                        $wo['offerNoti']['email'] = $email;
                        $wo['offerNoti']['phone'] = $phone;
                        $wo['offerNoti']['property_address'] = $prop_Address;
                        $wo['offerNoti']['url']      = $internal_msg2_link;
                        $wo['offerNoti']['visit_date'] = $date_request;
                        $wo['offerNoti']['visit_time'] = $time_request;
                        $wo['offerNoti']['subject'] = $subject;
                        
                        $send_message_data = array(
                            'from_email' => $wo['config']['siteEmail'],
                            'from_name' => $wo['config']['siteName'],
                            'to_email' => $author_email,
                            'to_name' => $autho_name,
                            'subject' => $subject,
                            'charSet' => 'utf-8',
                            'message_body' => Wo_LoadPage('emails/'.$template),
                            'is_html' => true
                        );
                        
                        if ($wo['config']['smtp_or_mail'] == 'smtp') {
                            $send_message_data['insert_database'] = 1;
                        }
                        
                        $emailSent = sendEmailToUser($wo['config']['siteEmail'], ['email' => $author_email], $subject, Wo_LoadPage('emails/' . $template));
                        // Wo_SendMessage($send_message_data);

                        /** Send Sms to Seller */
                        $seller = getTableData(T_USERS, ['user_id' => $seller_id], 1);
                        $property = getTableData(T_LISTINGS, ['id' => $l_id], 1);
                        if($property && $seller && $seller['phone_number']){
                            $tab1 = json_decode($property['tab1'], 1);
                            $address = $tab1['entered_address'];
                            $link = $wo['site_url'] . "/my-counter-offer?status=pending&search=" . $tab1['listing_title'] ;
                            $message = "Hey, you have a new offer on {$address}, <a href='{$link}'>click here</a> to see your offer";
                            $sendresponse = send_bulk_sms_broadcast($seller['phone_number'], $first_signerPhone, $message);
                        }

                        $data = array(
                            'status' => 200,
                            'message' => 'Your Schedule Visit Request Was successfully Made',
                        );
                    
                    }else{
                        $data = array(
                            'status' => 400,
                            'message' => "Error Invalid Email Reciever Address"
                        );
                    }
                    
                }else{
                    $data = array(
                        'status' => 400,
                        'message' => "Error! Email Option not active"
                    );
                }
                
                
            }else{
                
                $data = array(
                    'status' => 400,
                    'message' => 'Error While trying to insert request'
                );
                    
            }

        }else{
            $data = array(
                'status' => 400,
                'message' => 'Incorrect Customer ID, please try again....'
            );
        }
        
        
        
    }else{
        
        $data = array(
            'status' => 400,
            'message' => 'Invalid Property Details....'
        );

        
    }
    
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
}


// Get Schedule Visit Data...
if( isset($_POST['action']) && ($_POST['action'] == "get_counter_visit") ){
    
    $prop_id = $_POST['prop_id'];
    $visit_id = $_POST['visit_id'];
    
    $oneVisit = get_One_schedule_details($prop_id,$visit_id);
    
    if($oneVisit){
        
        $data = array(
            'status' => 200,
            'message' => 'Success',
            'buyer_id' => $oneVisit['user_id'],
            'r_buyer_date' => $oneVisit['visit_date'],
            'r_buyer_time' => $oneVisit['visit_time'],
            'buyer_name' => $oneVisit['name'],
            'buyer_date' => $oneVisit['visit_date'],
            'buyer_time' => $oneVisit['visit_time'],
            'buyer_message' => $oneVisit['stitle'],
            
        );
    }else{
        
        $data = array(
            'status' => 400,
            'message' => 'Error!',
            
        );
    }
    
    
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
}


// Make Schedule Counter.....
if( isset($_POST['action']) && ($_POST['action'] == "make_S_V_Counter") ){
    
    $counter_type = $_POST['counter_type'];
    $visit_id = $_POST['visit_id'];
    $prop_id = $_POST['prop_id'];
    $buyer_date = $_POST['buyer_date'];
    $buyer_time = $_POST['buyer_time'];
    $buyer_id = $_POST['buyer_id'];
    $seller_date = $_POST['seller_date'];
    $seller_time = $_POST['seller_time'];
    
    
    $template = "counterVisit";
    
    $subject = "Sorry your Schedule visit request has been countered";
    
    // Property details
    $prop_Address = get_listing_by_specific_colm("tab1","entered_address",$prop_id);
    
    
    
    // Get buyer name and email from the schedule made previously since buyers might be either logged in user or not logged in user...
    $b_n = get_buyerSchDetails_by($prop_id,$visit_id,'name');
    $b_e = get_buyerSchDetails_by($prop_id,$visit_id,'email');
    $b_un = get_buyerSchDetails_by($prop_id,$visit_id,'uname');
    $b_vd = get_buyerSchDetails_by($prop_id,$visit_id,'visit_date');
    $b_vt = get_buyerSchDetails_by($prop_id,$visit_id,'visit_time');
    $source = get_buyerSchDetails_by($prop_id,$visit_id,'source');
    
    // Link
    $internal_msg2_link = $source."/property/".$prop_id;
    
    // Seller details....
    $seller_id = $wo['user']['user_id'];
    $author_d = Wo_UserData($seller_id);
    $autho_name = $author_d['name'];
    $author_email = $author_d['email'];
    
    
    $counterSch = make_schedule_visit_counter($counter_type,$visit_id,$prop_id,$buyer_date,$buyer_time,$buyer_id,$seller_date,$seller_time);
    if($counterSch){
        
        if ($wo['config']['emailNotification'] == 1) {
		        
		        if (isset($b_e)) {
		            
                    $wo['offerNoti']['name'] = $b_n;
                    $wo['offerNoti']['uname'] = $b_un;
                    $wo['offerNoti']['email'] = $b_e;
                    $wo['offerNoti']['property_address'] = $prop_Address;
                    $wo['offerNoti']['url'] = $internal_msg2_link;
                    $wo['offerNoti']['c_visit_date'] = $seller_date;
                    $wo['offerNoti']['c_visit_time'] = $seller_time;
                    $wo['offerNoti']['b_visit_date'] = $b_vd;
                    $wo['offerNoti']['b_visit_time'] = $b_vt;
                    $wo['offerNoti']['c_visit_type'] = $counter_type;
                    $wo['offerNoti']['subject'] = $subject;
                    
                    $send_message_data = array(
                        'from_email' => $wo['config']['siteEmail'],
                        'from_name' => $wo['config']['siteName'],
                        'to_email' => $b_e,
                        'to_name' => $b_n,
                        'subject' => $subject,
                        'charSet' => 'utf-8',
                        'message_body' => Wo_LoadPage('emails/'.$template),
                        'is_html' => true
                    );
                    
                    if ($wo['config']['smtp_or_mail'] == 'smtp') {
                        $send_message_data['insert_database'] = 1;
                    }
                    
                    
                    Wo_SendMessage($send_message_data);
                    
                    $data = array(
    					'status' => 200,
    					'message' => 'You have Successfully countered Schedule Visit',
    				);
                
		        }else{
		            $data = array(
                        'status' => 400,
                        'message' => "Error Invalid Email Reciever Address"
                    );
		        }
		        
		    }else{
		        $data = array(
                    'status' => 400,
                    'message' => "Error! Email Option not active"
                );
		    }
        
    
    }else{
        $data = array(
            'status' => 400,
            'message' => 'Error',
        );
    }
    
    
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
}


// Accept or reject schedule visit request....
if( isset($_POST['action']) && ($_POST['action'] == "acc_o_rej_visit") ){
    
    $visit_id = $_POST['visit_id'];
    $prop_id = $_POST['prop_id'];
    $type = $_POST['type'];
    
    if($type == "Accept"){
        
        $template = "acceptVisit";
        $subject = "Congratulations Your Schedule Visit Request has been accepted.";
        $subTitle = "Your Schedule Visit successfully Accepted";
        $status = "accepted";
        
    }else{
        
        $template = "rejectVisit";
        $subject = "Sorry seller could not accept Your Schedule Visit Request.";
        $subTitle = "Sorry! Seller couldn't make your time happen.";
        $status = "rejected";
    }
    
    // Property details
    $prop_Address = get_listing_by_specific_colm("tab1","entered_address",$prop_id);
    
    $b_n = get_buyerSchDetails_by($prop_id,$visit_id,'name');
    $b_e = get_buyerSchDetails_by($prop_id,$visit_id,'email');
    $b_un = get_buyerSchDetails_by($prop_id,$visit_id,'uname');
    $b_vd = get_buyerSchDetails_by($prop_id,$visit_id,'visit_date');
    $b_vt = get_buyerSchDetails_by($prop_id,$visit_id,'visit_time');
    $source = get_buyerSchDetails_by($prop_id,$visit_id,'source');
    
    $internal_msg2_link = $source."/property/".$prop_id;
    
    $AcceptorRejectSch = rejectOraccept_Schedule($prop_id,$visit_id,$status);
    
    if($AcceptorRejectSch){
        
        if ($wo['config']['emailNotification'] == 1) {
		        
		        if (isset($b_e)) {
		            
                    $wo['offerNoti']['name'] = $b_n;
                    $wo['offerNoti']['uname'] = $b_un;
                    $wo['offerNoti']['email'] = $b_e;
                    $wo['offerNoti']['property_address'] = $prop_Address;
                    $wo['offerNoti']['url'] = $internal_msg2_link;
                    $wo['offerNoti']['b_visit_date'] = $b_vd;
                    $wo['offerNoti']['b_visit_time'] = $b_vt;
                    $wo['offerNoti']['subtitle'] = $subTitle;
                    $wo['offerNoti']['subject'] = $subject;
                    
                    $send_message_data = array(
                        'from_email' => $wo['config']['siteEmail'],
                        'from_name' => $wo['config']['siteName'],
                        'to_email' => $b_e,
                        'to_name' => $b_n,
                        'subject' => $subject,
                        'charSet' => 'utf-8',
                        'message_body' => Wo_LoadPage('emails/'.$template),
                        'is_html' => true
                    );
                    
                    if ($wo['config']['smtp_or_mail'] == 'smtp') {
                        $send_message_data['insert_database'] = 1;
                    }
                    
                    
                    Wo_SendMessage($send_message_data);
                    
                    $data = array(
    					'status' => 200,
    					'message' => 'You have Successfully '.$type.' Schedule Visit',
    				);
                
		        }else{
		            $data = array(
                        'status' => 400,
                        'message' => "Error Invalid Email Reciever Address"
                    );
		        }
		        
		    }else{
		        $data = array(
                    'status' => 400,
                    'message' => "Error! Email Option not active"
                );
		    }
        
    
    }else{
        $data = array(
            'status' => 400,
            'message' => 'Error',
        );
    }
    
    
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
    
    
}


// Proof of fund
if ( isset($_POST['action']) && ($_POST['action'] == "proof_funds") ) {

	if (isset($_FILES['media_file']) && !empty($_FILES['media_file'])) {
        $user_id = isset($wo['user']['user_id']) ? $wo['user']['user_id'] : 0;
    
		if (!empty($_FILES['media_file']["tmp_name"])) {
			$filename = "";
			$fileInfo = array(
				'file' => $_FILES["media_file"]["tmp_name"],
				'name' => $_FILES['media_file']['name'],
				'size' => $_FILES["media_file"]["size"],
				'type' => $_FILES["media_file"]["type"],
			    'types' => 'jpg,png,gif,jpeg,doc,docx,pdf',
			);

			$media = Wo_ShareFile($fileInfo, 0, false);
			if (!empty($media)) {
				$filename = $media['filename'];
			}

			$media_file = Wo_Secure($filename);
            $property_id = $_POST['listing_id'];
            
            if(isset($_POST['offer_code_oo'])){
                $offer_code = $_POST['offer_code_oo'];
            }
            
            if(isset($_POST['buyer_email_oo'])){
                $buyer_email = $_POST['buyer_email_oo'];
            }
			
            //get offer code with email and property id and user id
            $query_4code = mysqli_query($con,"SELECT offer_code as CodeO FROM `Wo_offers` where property_id = $property_id AND last_action_by = $user_id AND email = '$buyer_email'");
		    $getCode_O = mysqli_fetch_assoc($query_4code);
		    $myoffer_code = $getCode_O['CodeO'];
		    
			$qq = "Select * from Wo_offers where property_id = $property_id AND last_action_by = $user_id AND offer_code = '$myoffer_code'";
			$getData = mysqli_query($con, $qq);
			$dataDetails = mysqli_fetch_assoc($getData);

			$newOfferDetails = json_decode($dataDetails['offers_details'], true);
			$seller_id = $dataDetails['seller_id'];
			$offer_pr = $newOfferDetails['offer_price'];
            $emd_pr = $newOfferDetails['offer_emd_price'];
			
            // 	Get author/seller details for mail notification....
            $author_d = Wo_UserData($seller_id);
            $autho_name = $author_d['name'];
            $author_email = $author_d['email'];
            $autho_uname = $author_d['username'];
            $autho_phone = $author_d['phone_number'];

			$newOfferDetails['url'] = $media_file;
			$newOfferDetails = json_encode($newOfferDetails);
			
			$offer_stage = 2;
			
			$subject = "New Offer Notification";
			
            //Email tamplate....
            $template = "offer_recieved";
            
            $title_prop = get_listing_by_specific_colm("tab1","listing_title",$property_id);
            $new_stage_url = $wo['config']['site_url'].'/my-counter-offer?search=' . urlencode($title_prop);
			
			// Get Property address
            $prop_Address = get_listing_by_specific_colm("tab1","entered_address",$property_id);
			
			$qq1 = "UPDATE `Wo_offers` SET `offers_details` = '$newOfferDetails', `offer_stage` = $offer_stage WHERE `property_id` = $property_id AND `last_action_by` = $user_id AND `offer_code` = '$myoffer_code' ";
			$updateRecord = mysqli_query($con, $qq1);
			

			if ($updateRecord) {
			    
			    
			    /** Get Property address */
                $prop_Address = get_listing_by_specific_colm("tab1","entered_address",$property_id);
                $listing_title = get_listing_by_specific_colm("tab1","listing_title",$property_id);

                /** Send Sms to Owner of Property */
                $owner = getTableData(T_USERS, ['user_id' => $seller_id], 1);
                $from_number = $wo['config']['sms_t_phone_number'];
                
                
                
                if($owner && $owner['phone_number'] && $from_number){
                    // $smsText = "You have recieved a new message from ({$prop_Address}). \nMessage: {$message} \nCheck email for more details.";

                    $link = $wo['site_url'] . "/my-counter-offer?status=pending&search=" . $listing_title ;
                    $smsText = "Hey, you have a new offer on {$address}, Click here {$link} to see your offer";
                    
                    $sendresponse = send_bulk_sms_broadcast(US_formate($owner['phone_number']), US_formate($from_number), $smsText);
                }
			    
			    //Email for seller.......
			    if ($wo['config']['emailNotification'] == 1) {
			        
			        if (isset($author_email)) {
			            
			            $wo['offerNoti']['name'] = $autho_name;
                        $wo['offerNoti']['email']     = $author_email;
                        $wo['offerNoti']['phone'] = $autho_phone;
                        $wo['offerNoti']['property_address'] = $prop_Address;
                        $wo['offerNoti']['url']      = $new_stage_url;
                        $wo['offerNoti']['offer_p'] = $offer_pr;
                        $wo['offerNoti']['emd_p'] = $emd_pr;
                        $wo['offerNoti']['subject'] = $subject;
                        
                        $send_message_data = array(
                            'from_email' => $wo['config']['siteEmail'],
                            'from_name' => $wo['config']['siteName'],
                            'to_email' => $author_email,
                            'to_name' => $autho_name,
                            'subject' => $subject,
                            'charSet' => 'utf-8',
                            'message_body' => Wo_LoadPage('emails/'.$template),
                            'is_html' => true
                        );
                        
                        if ($wo['config']['smtp_or_mail'] == 'smtp') {
                            $send_message_data['insert_database'] = 1;
                        }
                        
                        // $emailSent = Wo_SendMessage($send_message_data);
                        $emailSent = sendEmailToUser($wo['config']['siteEmail'], ['email' => $author_email], $subject, Wo_LoadPage('emails/'.$template));
                        // pre($emailSent); exit; 
                        
                        
                       

                        $data = array(
        					'status' => 200,
        					'step' => '2',
        					'media_file_url' => $media_file,
        					'baseUrl' => $wo['config']['site_url'],
        					'message' => 'Proof Fund Saved successfully',
        				);
                    
			        }else{
			            $data = array(
                            'status' => 400,
                            'message' => "Error Invalid Email Reciever Address"
                        );
			        }
			        
			    }else{
			        $data = array(
                        'status' => 400,
                        'message' => "Error! Email Option not active"
                    );
			    }
			    
			  
			}else{
                $data = array(
                    'status' => 400,
                    'message' => 'Error While trying to insert Offer Property'
                );
            }
            
			echo json_encode($data);die;

		}
	}

	die();

}


// Buy Now Button...
if( isset($_POST['action']) && ($_POST['action'] == "submit_BuyNow") ){
    $p_id = $_POST['listing_id'];
    
    $pDetails = getAll_listings_by_id($p_id);
    $p_query = mysqli_fetch_assoc($pDetails);
    $checkAvail = $p_query['p_idC'];
    
    if($checkAvail > 0){
        
        $name_b = $_POST['name_b'];
        $company_b = $_POST['company_b'];
        $buyer_email_b = $_POST['buyer_email_b'];
        $buyer_phone_b = $_POST['buyer_phone_b'];
        $seller_id = $_POST['seller_id'];
        $user_id = $wo['user']['user_id'];
        
        $buttonActive = get_listing_by_specific_colm('tab1','allow_buynow',$p_id);
        
        if($buttonActive == 1 || $buttonActive > 0){
            
            $buyNowPrice = get_listing_by_specific_colm('tab1','buy_nowP',$p_id);
            
            // Get Property address
            $prop_Address = get_listing_by_specific_colm("tab1","entered_address",$p_id);
            
            $title_prop = get_listing_by_specific_colm("tab1","listing_title",$p_id);
            
            $wireInstruct = $wo['config']['site_url'].'/'.get_listing_by_specific_colm("tab4","BN_upload_wire",$p_id);
            
            
            $subject = "Buy Now Request For ".$title_prop;
            $subject2 = "Successfully Purchase ".$title_prop;
            
            $template = "buyEmail";
            $template2 = "buy_nowaccept";
            
            $buyNow_details = json_encode([
            	"name_b" => $name_b,
            	"company_b" => $company_b,
            	"buyer_email_b" => $buyer_email_b,
            	"buyer_phone_b" => $buyer_phone_b,
            	"seller_id" => $seller_id,
            	"buyer_id" => $user_id,
            	"prop_title" => $title_prop,
            	"price_b" => $buyNowPrice,
            ]);
            
            // 	Get author/seller details for mail notification....
            $author_d = Wo_UserData($seller_id);
            $autho_name = $author_d['name'];
            $author_email = $author_d['email'];
            $autho_uname = $author_d['username'];
            $autho_phone = $author_d['phone_number'];
            
            if (isset($_FILES['signedCont_b']) && !empty($_FILES['signedCont_b'])) {
            
                if (!empty($_FILES['signedCont_b']["tmp_name"])) {
                    
        			$orignalname_cb = $_FILES['signedCont_b']["name"];
        			$filename_cb = "";
        			$fileInfo_cb = array(
        				'file' => $_FILES["signedCont_b"]["tmp_name"],
        				'name' => $_FILES['signedCont_b']['name'],
        				'size' => $_FILES["signedCont_b"]["size"],
        				'type' => $_FILES["signedCont_b"]["type"],
        				'types' => 'doc,docx,pdf',
        			);
        
        			$media_cb = Wo_ShareFile($fileInfo_cb, 0, false);
        			if (!empty($media_cb)) {
        			    
        				$filename_cb = $media_cb['filename'];
        
        			} 
        			
                }
                
            }
            
            $mydoc_sg = $wo['config']['site_url'].'/' .$filename_cb;
            
            // Database Insertion code here...
            $InsertRecord =  mysqli_query($con,"INSERT INTO `buyNow_Request` (prop_id,tab1,signed_doc,buyer_id,seller_id) VALUES ($p_id,'$buyNow_details','$filename_cb',$user_id,$seller_id)");
    	
            if ($InsertRecord) {
			    
			    //Email for seller.......
			    if ($wo['config']['emailNotification'] == 1) {
			        
			        if (isset($buyer_email_b)) {
			            
                        $wo['offerNoti']['name'] = $name_b;
                        $wo['offerNoti']['email']     = $buyer_email_b;
                        $wo['offerNoti']['phone'] = $buyer_phone_b;
                        $wo['offerNoti']['property_address'] = $prop_Address;
                        $wo['offerNoti']['url']      = $mydoc_sg;
                        $wo['offerNoti']['url1']      = $wireInstruct;
                        $wo['offerNoti']['offer_p'] = $buyNowPrice;
                        $wo['offerNoti']['b_company'] = $company_b;
                        $wo['offerNoti']['b_name'] = $name_b;
                        $wo['offerNoti']['b_email'] = $buyer_email_b;
                        $wo['offerNoti']['b_phone'] = $buyer_phone_b;
                        $wo['offerNoti']['subject'] = $subject;
                        $wo['offerNoti']['subject2'] = $subject2;
                        
                        $send_message_data = array(
                            'from_email' => $wo['config']['siteEmail'],
                            'from_name' => $wo['config']['siteName'],
                            'to_email' => $author_email,
                            'to_name' => $autho_name,
                            'subject' => $subject,
                            'charSet' => 'utf-8',
                            'message_body' => Wo_LoadPage('emails/'.$template),
                            'is_html' => true
                        );
                        
                        if ($wo['config']['smtp_or_mail'] == 'smtp') {
                            $send_message_data['insert_database'] = 1;
                        }
                        
                        $send_message_data2 = array(
                            'from_email' => $wo['config']['siteEmail'],
                            'from_name' => $wo['config']['siteName'],
                            'to_email' => $buyer_email_b,
                            'to_name' => $name_b,
                            'subject' => $subject2,
                            'charSet' => 'utf-8',
                            'message_body' => Wo_LoadPage('emails/'.$template2),
                            'is_html' => true
                        );
                        
                        if ($wo['config']['smtp_or_mail'] == 'smtp') {
                            $send_message_data2['insert_database'] = 1;
                        }
                        
                        Wo_SendMessage($send_message_data);
                        Wo_SendMessage($send_message_data2);
                        
                        /** Send SMS to Seller */
                        
                        $seller = getTableData(T_USERS, ['user_id' => $seller_id], 1);
                        $property = getTableData(T_LISTINGS, ['id' => $p_id], 1);
                        if($property && $seller && $seller['phone_number']){
                            $tab1 = json_decode($property['tab1'], 1);
                            $address = $tab1['entered_address'];
                            $message = "Someone wants to buy your property on {$address}. They have elected to buy it now!  Check email for more details ";
                            $sendresponse = send_bulk_sms_broadcast($seller['phone_number'], $buyer_phone_b, $message);
                        }

                        $data = array(
        					'status' => 200,
        					'message' => 'Your Buy Now Request Was successfully Made',
        				);
                    
			        }else{
			            $data = array(
                            'status' => 400,
                            'message' => "Error Invalid Email Reciever Address"
                        );
			        }
			        
			    }else{
			        $data = array(
                        'status' => 400,
                        'message' => "Error! Email Option not active"
                    );
			    }
			    
			  
			}else{
                $data = array(
                    'status' => 400,
                    'message' => 'Error While trying to insert request'
                );
            }
            
            
        }
        
    }else{
        
        $data = array(
            'status' => 400,
            'message' => 'Invalid Property Details....'
        );
        
        
    }
    
    
    
    header("Content-type: application/json");
    echo json_encode($data);
    die();
}


// Add Document
if( isset($_POST['action']) && ($_POST['action'] == "add_document") ) {

	$user_id = $wo['user']['user_id'];

	if (isset($_FILES['upload_document']) && !empty($_FILES['upload_document'])) {
		$total = count($_FILES['upload_document']['tmp_name']);
		$media_data = [];
		for ($i = 0; $i < $total; $i++) {
			$tmpFilePath = $_FILES['upload_document']['tmp_name'][$i];
			$originalFileName = $_FILES['upload_document']['name'][$i];
			if ($tmpFilePath != "") {
				$filename = "";
				$fileInfo = array(
					'file' => $_FILES["upload_document"]["tmp_name"][$i],
					'name' => $_FILES['upload_document']['name'][$i],
					'size' => $_FILES["upload_document"]["size"][$i],
					'type' => $_FILES["upload_document"]["type"][$i],
					'types' => 'jpg,png,gif,jpeg,doc,docx,pdf',
				);
				$media = Wo_ShareFile($fileInfo, 0, false);
				if (!empty($media)) {
					$filename = $media['filename'];
				}
				$media_file = Wo_Secure($filename);
				$media_data[] = [
					'path' => $media_file,
					'name' => $originalFileName,
				];
			}

		}
		$titles = $_POST['imagesTitle'];
		$titles[] = "original";
		$combine = [];
		$i = 0;
		foreach ($media_data as $mData) {
			$combine[] = [
				'path' => $mData['path'],
				'originalName' => $mData['name'],
				'title' => $titles[$i],
			];
			$i++;
		}

		$offer_document = $combine;

		if ($offer_document) {
			$property_id = $_POST['listing_id'];
			$qq = "Select offers_details from Wo_offers where property_id =  $property_id AND last_updated_by_user = $user_id";

			$getData = mysqli_query($con, $qq);

			$dataDetails = mysqli_fetch_assoc($getData);

			$newOfferDetails = json_decode($dataDetails['offers_details'], true);

			$newOfferDetails['document'] = $offer_document;

			$newOfferDetails = json_encode($newOfferDetails);

			$qq = "UPDATE Wo_offers SET offers_details = '$newOfferDetails' WHERE property_id =  $property_id AND last_updated_by_user = $user_id";
			$updateRecord = mysqli_query($con, $qq);
			$data = array(
				'status' => 200,
				'baseUrl' => $wo['config']['site_url'],
				'message' => 'Document upload successfully',
				'step' => '3',
			);
			echo json_encode($data);die;

		}
		$data = array(
			'status' => 400,
			'baseUrl' => $wo['config']['site_url'],

		);
		echo json_encode($data);die;

	}
	;
}




?>
