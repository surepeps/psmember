<?php

$root=__DIR__;
require_once($root.'/config.php');
require_once('assets/init.php');
global $wo, $sqlConnect;

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);
$matched_id = $_POST['mid'];

$user_id = $wo['user']['user_id'];


if(isset($_POST['action']) && $_POST['action']=="get_counter_prices"){
    if(isset($_POST['prop_id']) && isset($_POST['offer_id'])){
        $prop_id = $_POST['prop_id'];
        $offer_id = $_POST['offer_id'];
        // Fetch property title and property real price
        $offer = getRow("SELECT * FROM `Wo_offers` WHERE id = $offer_id ");
        
        $wo['offer_id'] = $offer_id;
        $html = Wo_LoadPage("my-counter-offer/counter-popup");

        $data = array(
            'status' => 200,
            'html' => $html,
            'message' => 'success'
        );
    
    }
}  
    
if(isset($_POST['action']) && $_POST['action']=="send_counter_prices"){
    
    $status = 400;
    $offer_id = filter('offer_id');
    $type = filter('type');
    $counter_price = filter('counter_price');

    if(!$offer_id){
        $message = "Please select a valid offer";
    }else if(!$counter_price) {
        $message = "Please enter a valid counter offer price";
    }else{
        $where = ['id' => $offer_id];
        $offer = getTableData("Wo_offers", $where, 1);

        if(!$offer){
            $message = "This offer is deleted";
        }else{

            $offer_details = json_decode($offer['offers_details'], 1);
            $buyer_email_o = $offer_details['first_signerEmail'];
            $contact = getBuyerPinByEmail($buyer_email_o);
            
             
            if(!$contact || $contact['pin_code'] != $offer['customer_id']) {
                $message = "Contact does not exists anymore.";
            }else{

                $where = [
                    'offer_id' => $offer_id
                ];
                $counter_offer = getTableData('wo_counter_offer_price', $where, 1);

                $data = [
                    'offer_id' => $offer_id,
                    'counter_price' => $counter_price,
                    'type' => 'user'
                ];


                $counterQuery = insertRow('wo_counter_offer_price', $data);
                $offerQuery = updateRow('Wo_offers', [
                    'offer_status' => 'counter'
                ], ['id' => $offer_id]);

                if(
                   $sqlConnect->query($counterQuery) && $sqlConnect->query($offerQuery)
                ){

                    OfferCounterNotification($offer_id);

                    $message = "Offer is countered successfully";
                    $status = 200;
                }else{
                    $message = mysqli_error($sqlConnect);
                }
            }

        }
    }

    $data = [
        'status' => $status,
        'message' => $message
    ];
    
}


if(isset($_POST['action']) && $_POST['action'] == "buyer_signed_cont"){
    $email = $_POST['us_em'];
    $prop_id = $_POST['property_id'];
    $user_id = isset($wo['user']['user_id']) ? $wo['user']['user_id'] : 0;
    
    if (isset($_FILES['upload_contract_b']) && !empty($_FILES['upload_contract_b'])) {
        if (!empty($_FILES['upload_contract_b']["tmp_name"])) {
            
			$filename2 = "";
			$fileInfo2 = array(
				'file' => $_FILES["upload_contract_b"]["tmp_name"],
				'name' => $_FILES['upload_contract_b']['name'],
				'size' => $_FILES["upload_contract_b"]["size"],
				'type' => $_FILES["upload_contract_b"]["type"],
			    'types' => 'jpg,png,gif,jpeg,doc,docx,pdf',
			);
			
			$a_offer_status = "accepted";
			
			$media2 = Wo_ShareFile($fileInfo2, 0, false);
			if (!empty($media2)) {
				$filename2 = $media2['filename'];
			}

			$media_file2 = Wo_Secure($filename2);
			
			// signed document 
            $signed_docs = json_encode([
        		"signed_upload_contract" => $media_file2,
        	]);
        	
        	$query_4code = mysqli_query($sqlConnect,"SELECT * FROM `Wo_offers` where property_id = $prop_id AND last_action_by = $user_id AND email = '$email'");
		    $getCode_O = mysqli_fetch_assoc($query_4code);
		    $offer_id = $getCode_O['id'];
		    $seller_id = $getCode_O['seller_id'];
		    
		    $offer_details = json_decode($getCode_O['offers_details'],true);
            $counter_id = $getCode_O['counter_id'];
            
            // Buyer's details with offer details
            $buyer_email_o = $offer_details['first_signerEmail'];
            $buyer_name_o = $offer_details['name'];
            $buyer_company_o = $offer_details['company'];
            $buyer_phone_o = $offer_details['first_signerPhone'];
            $offer_code = $getCode_O['offer_code'];
            
            $offer_pr = $offer_details['offer_price'];
            $emd_pr = $offer_details['offer_emd_price'];
            
            $mydoc_sg = $wo['config']['site_url']."/".$media_file2;
            
            // Get Property address
            $prop_Address = get_listing_by_specific_colm("tab1","entered_address",$prop_id);
            
            // Template 
            $template = "offer_signed";
		    
		    // 	Get author/seller details for mail notification....
            $author_d = Wo_UserData($seller_id);
            $autho_name = $author_d['name'];
            $author_email = $author_d['email'];
            $autho_uname = $author_d['username'];
            $autho_phone = $author_d['phone_number'];
		    
        	// Update Offer accept status
            $updateSigned = mysqli_query($sqlConnect, "UPDATE `Wo_offers` SET `signed_docs` = '$signed_docs', `offer_status` = '$a_offer_status', `offer_stage` = 4, `sold_status` = 1 WHERE `id` = $offer_id");
            
            $subject = 'Contract Signed Notification';
            
            if($updateSigned){
                
                if ($wo['config']['emailNotification'] == 1) {
			        
			        if (isset($author_email)) {
			            
			            $wo['offerNoti']['name'] = $autho_name;
                        $wo['offerNoti']['email']     = $author_email;
                        $wo['offerNoti']['phone'] = $autho_phone;
                        $wo['offerNoti']['property_address'] = $prop_Address;
                        $wo['offerNoti']['url']      = $mydoc_sg;
                        $wo['offerNoti']['offer_p'] = $offer_pr;
                        $wo['offerNoti']['b_company'] = $buyer_company_o;
                        $wo['offerNoti']['b_name'] = $buyer_name_o;
                        $wo['offerNoti']['b_email'] = $buyer_email_o;
                        $wo['offerNoti']['b_phone'] = $buyer_phone_o;
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
                        
                        
                        Wo_SendMessage($send_message_data);
                        
                        $data = array(
        					'status' => 200,
        					'message' => 'Signed Document Uploaded Successfully',
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
                    'message' => "Error! While Processing Your Request"
                );
            }
        	
        	
			
        }
        
    
    }else{
        $data = array(
            'status' => 400,
            'message' => "Error! Empty Documents"
        );
    }
    
    
}


if(isset($_POST['action']) && $_POST['action'] == "accept_or_reject_offer"){
    $type = $_POST['type'];
    $offer_id = $_POST['offer_id'];
    
    //Fetch Offer Details
    $o_d_info = mysqli_query($sqlConnect,"SELECT * FROM `Wo_offers` WHERE id = $offer_id");
    $o_d_infor = mysqli_fetch_array($o_d_info);
    $buyer_id = $o_d_infor['last_updated_by_user'];
    
    $property_id = $o_d_infor['property_id'];
    $offer_details = json_decode($o_d_infor['offers_details'],true);
    $counter_id = $o_d_infor['counter_id'];
    
    // Buyer's details with offer details
    $buyer_email_o = $offer_details['first_signerEmail'];
    $buyer_name_o = $offer_details['name'];
    $buyer_phone_o = $offer_details['first_signerPhone'];
    $offer_code = $o_d_infor['offer_code'];
    
    $source = $o_d_infor['source'];
    
    $offer_pr = $offer_details['offer_price'];
    $emd_pr = $offer_details['offer_emd_price'];
    
    // Get Property address
    $prop_Address = get_listing_by_specific_colm("tab1","entered_address",$property_id);
    
    
    // Set a new offer status
    $r_offer_status = "rejected";
    $a_offer_status = "accepted";
    
    // convert offer id to encrypted sting
    $offerid = base64_encode($offer_code." ".$buyer_email_o." ".$property_id);
    
    if($type == "Accept"){
        
        // Process Acceptance documents
        if ( (isset($_FILES['wire_instruction']) && !empty($_FILES['wire_instruction'])) && (isset($_FILES['upload_contract']) && !empty($_FILES['upload_contract'])) ) {
            
            // For Wire Instruction
            if (!empty($_FILES['wire_instruction']["tmp_name"])) {
    			$filename_WireI = "";
    			$fileInfo_WireI = array(
    				'file' => $_FILES["wire_instruction"]["tmp_name"],
    				'name' => $_FILES['wire_instruction']['name'],
    				'size' => $_FILES["wire_instruction"]["size"],
    				'type' => $_FILES["wire_instruction"]["type"],
    			    'types' => 'jpg,png,gif,jpeg,doc,docx,pdf',
    			);
    			
    			$media_WireI = Wo_ShareFile($fileInfo_WireI, 0, false);
    			if (!empty($media_WireI)) {
    				$filename_WireI = $media_WireI['filename'];
    			}
    
    			$media_file_WireI = Wo_Secure($filename_WireI);
    			
    			
    			
            }
            
            // For Upload Contract
            if (!empty($_FILES['upload_contract']["tmp_name"])) {
    			$filename_UploadC = "";
    			$fileInfo_UploadC = array(
    				'file' => $_FILES["upload_contract"]["tmp_name"],
    				'name' => $_FILES['upload_contract']['name'],
    				'size' => $_FILES["upload_contract"]["size"],
    				'type' => $_FILES["upload_contract"]["type"],
    			    'types' => 'jpg,png,gif,jpeg,doc,docx,pdf',
    			);
    			
    			$media_UploadC = Wo_ShareFile($fileInfo_UploadC, 0, false);
    			if (!empty($media_UploadC)) {
    				$filename_UploadC = $media_UploadC['filename'];
    			}
    
    			$media_file_UploadC = Wo_Secure($filename_UploadC);
    			
    			
            }
            
            // Conbime both uploads into an encoded array
            $accept_docs = json_encode([
        		"upload_contract" => $media_file_UploadC,
        		"wire_instruction" => $media_file_WireI,
        	]);
        	
        	
        	
        	// Get Final aggreed Price and initial price
            $acceptedPrice = $offer_details['offer_price'];
            $acceptedInPrice = $offer_details['offer_initial_price'];
            
            
            // Get Offer new url
            $new_stage_url = $source."/property/".$property_id."/".$offerid;
            
            // get wire offerlink to download
            $new_stage_url_d = $wo['config']['site_url']."/".$media_file_WireI;
            
            // Type of offer Accept oR RejeCT Mail Temp
            $template = "offer_accept";
            
            //Auto Update All other offers from the same property
            // $rejectOtherOffers = mysqli_query($sqlConnect, "UPDATE `Wo_offers` SET  `offer_status` = '$r_offer_status', `sold_status` = 1 WHERE property_id = $property_id AND id != $offer_id");
            
            // Update Offer accept status
            $acceptRejectOffer = mysqli_query($sqlConnect, "UPDATE `Wo_offers` SET `accept_docs` = '$accept_docs',  `offer_stage` = 4, `offer_status` = '$a_offer_status', `sold_status` = 1 WHERE  id = $offer_id");
            
            $subject = 'Accept Offer Notification';
            
            
            
        }
    
    
    
    }elseif($type == "Reject"){
        
    // Type of offer Accept oR RejeCT Mail Temp
    $template = "offer_reject";
    
    // Update Offer reject status
     $acceptRejectOffer = mysqli_query($sqlConnect, "UPDATE `Wo_offers` SET `offer_stage` = 0, `offer_status` = '$r_offer_status' WHERE  id = $offer_id");
     
     // Get Offer new url
    $new_stage_url = $wo['config']['site_url']."/property/".$property_id."/".$offerid;
     
     $subject = 'Reject Offer Notification';

        
    }
    
    
    if($acceptRejectOffer){
                
        if ($wo['config']['emailNotification'] == 1) {
            
            if (isset($buyer_email_o)) {
                
                $wo['offerNoti']['name'] = $buyer_name_o;
                $wo['offerNoti']['email']     = $buyer_email_o;
                $wo['offerNoti']['phone'] = $buyer_phone_o;
                $wo['offerNoti']['property_address'] = $prop_Address;
                $wo['offerNoti']['url']      = $new_stage_url;
                $wo['offerNoti']['url_d']      = $new_stage_url_d;
                $wo['offerNoti']['offer_p'] = $offer_pr;
                $wo['offerNoti']['emd_p'] = $emd_pr;
                $wo['offerNoti']['subject'] = $subject;
                
                $send_message_data = array(
                    'from_email' => $wo['config']['siteEmail'],
                    'from_name' => $wo['config']['siteName'],
                    'to_email' => $buyer_email_o,
                    'to_name' => $buyer_name_o,
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
                    'message' => $type.' Offer Made Successfully'
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
    
    
    
    
    
}

    
if(!isset($_POST['action']) && $_POST['action'] == ""){  
    $data = array(
        'status' => 400,
        'message' => 'Error'
    );
}


header("Content-type: application/json");
echo json_encode($data);
die;

    
function OfferCounterNotification($offer_id){
    global $wo;
    if($offer_id){
        
        $counter_price = filter('counter_price');
        $where = ['id' => $offer_id];
        $offer = getTableData("Wo_offers", $where, 1);

        if($offer){

            $property_id = $offer['property_id'];
            $offer_details = json_decode($offer['offers_details'],true);
            $email = $offer_details['first_signerEmail'];
            $contact = getBuyerPinByEmail($email);
            


            $property_id = $offer['property_id'];
            
            $name = $offer_details['name'];
            $phone = $offer_details['first_signerPhone'];
            $offer_code = $offer['offer_code'];
            $prop_price = $offer_details['offer_initial_price'];
            $offer_pr = $offer_details['offer_price'];
            $emd_pr = $offer_details['offer_emd_price'];
            $source = $offer['source'];
            
            // fetch property address
            $prop_Address = get_listing_by_specific_colm("tab1","entered_address",$property_id);
            
            $subject = "Offer Price Counter Notification";
            
            // convert offer id to encrypted sting
            $offerid = base64_encode($offer_code." ".$email." ".$property_id);
            
            // Get Offer new url
            $new_stage_url = $source."/property/".$property_id."/".$offerid;
            
            $subject = "Offer Price Counter Notification";
            
            // convert offer id to encrypted sting
            $offerid = base64_encode($offer_code." ".$email." ".$property_id);
            
            // Get Offer new url
            $new_stage_url = $source."/property/".$property_id."/".$offerid;
            
             
            if(isAllowed('offer_countered_email', $contact['id'])){
                $wo['offerNoti']['name'] = $name;
                $wo['offerNoti']['email']     = $email;
                $wo['offerNoti']['phone'] = $phone;
                $wo['offerNoti']['property_address'] = $prop_Address;
                $wo['offerNoti']['url']      = $new_stage_url;
                $wo['offerNoti']['offer_p'] = ($counter_price < $offer_pr) ? $offer_pr : $counter_price;
                $wo['offerNoti']['emd_p'] = $emd_pr;
                $wo['offerNoti']['counter_p'] = $counter_price;
                $wo['offerNoti']['subject'] = $subject;
                
                
                $send_message_data  = array(
                    'from_email' => $wo['config']['siteEmail'],
                    'from_name' => $wo['config']['siteName'],
                    'to_email' => $email,
                    'to_name' => $name,
                    'subject' => $subject,
                    'charSet' => 'utf-8',
                    'message_body' => Wo_LoadPage('emails/offer_counter'),
                    'is_html' => true,
                    'insert_database' => 1
                );
                

                Wo_SendMessage($send_message_data);
            }
            
            if(isAllowed('offer_countered_email', $contact['id'])){
                sendOfferSms($phone);
            }

        }
    }
    
}

function sendOfferSms($to) {

    global $wo;
    
    // Send sms notification on mobile
    $to = US_formate($to);
    $from_number = US_formate($wo['config']['sms_t_phone_number']);
    $smsText = "You have A Counter Offer\nPlease Counter, Reject or Accept offer within 24 hours";
    send_bulk_sms_broadcast($to, $from_number, $smsText);

}
