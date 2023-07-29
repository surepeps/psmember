<?php

use Twilio\Rest\Serverless\V1\Service\TwilioFunction\FunctionVersionPage;

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
// $root .= '/pro/newdevsite';


require_once($root.'/config.php');
require_once('assets/init.php');

// Create and Update Appointment for contacts
if( isset($_POST['action']) && ($_POST['action'] == "appointAction") ) {
    
    if(isset($_POST['date'])){
        $date = strtotime($_POST['date']);
    }
    
    if(isset($_POST['appt_id'])){
        $appt_id = $_POST['appt_id'];
    }
    
    if(isset($_POST['upid'])){
        $user_id = $_POST['upid'];
    }
    
    if(isset($_POST['contact_id'])){
        $contact_id = $_POST['contact_id'];
    }
    
    if(isset($_POST['contact_type'])){
        $contact_type = $_POST['contact_type'];
    }
    
    
    if($user_id == $wo['user']['user_id']){
        
        if(!empty($date)){
            
            
            if($contact_type == 1){
                $ctype = "contact";
            }else if($contact_type == 2){
                $ctype = "buyer";
            }else{
                $ctype = "property";
            }
            
            $createdBy = $user_id;
            $createAt = time();
            $options = [
                'date' => $date,
                'contact_type' => $ctype,
                'contact_id' => $contact_id,
                'created_by' => $createdBy,
                'created_at' => $createAt
            ];
    
            if($appt_id){
                $where['appt_id'] = $appt_id;
                $query = updateRow(T_APPT, $options, $where);
                $st = "Updated";
                
            }else{
                $query = insertRow(T_APPT, $options);
                $st = "Created";
            }
            
            if($sqlConnect->query($query)){
                    
                    if($appt_id > 0){
                        $aptid = $appt_id;
                    }else{
                        $aptid = $sqlConnect->insert_id;
                    }
                    
                   $data = array(
                		'status' => 200,
                		'date' => date('d/m/Y', $date),
                		'appt_id' => $aptid,
                		'message' => 'Successfully '.$st.' Appointment',
                	); 
            	
            }else{
                
                $data = array(
            		'status' => 400,
            		'message' => 'Error!, while processing your request',
            	);
            	
            }
            
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'Enter Appointment Date',
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
    
// Create and update Offer for Contacts
if( isset($_POST['action']) && ($_POST['action'] == "offerAction") ) {
    
    if(isset($_POST['date'])){
        $date = strtotime($_POST['date']);
    }
    
    if(isset($_POST['price'])){
        $price = $_POST['price'];
    }

    if(isset($_POST['offer_id'])){
        $offer_id = $_POST['offer_id'];
    }
    
    if(isset($_POST['upid'])){
        $user_id = $_POST['upid'];
    }
    
    if(isset($_POST['contact_id'])){
        $contact_id = $_POST['contact_id'];
    }
    
    if(isset($_POST['contact_type'])){
        $contact_type = $_POST['contact_type'];
    }
    
    
    if($user_id == $wo['user']['user_id']){
        
        if($contact_type == 1){
            $ctype = "contact";
        }else if($contact_type == 2){
            $ctype = "buyer";
        }else{
            $ctype = "property";
        }
        
        
        $optionsC = [
            'offer_id' => $offer_id
        ];
        
        $offer = getBuyerOffer($optionsC);
        
        if(empty($date)){
            
            $data = array(
        		'status' => 400,
        		'message' => 'Please enter a valid date for offer',
        	);
        	
        }else if($offer_id && !$offer){
            
            $data = array(
        		'status' => 400,
        		'message' => 'Offer not found, please try again with different data',
        	);
        	
        }else if(empty($price) || !is_numeric($price)){
            
            $data = array(
        		'status' => 400,
        		'message' => 'Please enter a valid price for this offer',
        	);
        	
        }else{
            
            $createdBy = $user_id;
            $createAt = time();
            $options = [
                'date' => $date,
                'contact_type' => $ctype,
                'contact_id' => $contact_id,
                'created_by' => $createdBy,
                'created_at' => $createAt,
                'price' => $price
            ];
    
            if($offer_id){
                
                $where['offer_id'] = $offer_id;
                $query = updateRow(T_OFFER_C, $options, $where);
                
                $st = "Updated";
            }else{
                $query = insertRow(T_OFFER_C, $options);
                
                $st = "Created";
            }
            
            if($sqlConnect->query($query)){
                    
                    if($offer_id > 0){
                        $offid = $offer_id;
                    }else{
                        $offid = $sqlConnect->insert_id;
                    }
                    
                   $data = array(
                		'status' => 200,
                		'date' => date('d/m/Y', $date),
                		'offer_id' => $offid,
                		'message' => 'Successfully '.$st.' Offer',
                	); 
            	
            }else{
                
                $data = array(
            		'status' => 400,
            		'message' => 'Error!, while processing your request',
            	);
            	
            }
            
            
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

// Delete Appointment for contacts
if( isset($_POST['action']) && ($_POST['action'] == "DeleteappointAction") ) {
    
    if(isset($_POST['appt_id'])){
        $appt_id = $_POST['appt_id'];
    }
    
    if(isset($_POST['upid'])){
        $user_id = $_POST['upid'];
    }
    
    if(isset($_POST['contact_id'])){
        $contact_id = $_POST['contact_id'];
    }
    
    if(isset($_POST['contact_type'])){
        $contact_type = $_POST['contact_type'];
    }
    
    
    if($user_id == $wo['user']['user_id']){
        
        if($contact_type == 1){
            $ctype = "contact";
        }else if($contact_type == 2){
            $ctype = "buyer";
        }else{
            $ctype = "property";
        }
        
        
        $contentOptions = [
            'contact_id' => $id,
            'appt_id' => $appt_id,
            'contact_type' => $ctype
        ];
    
        // Appoint 
        $appointment = getBuyerAppt($contentOptions);

        if($appointment){
            
            $query = deleteRow(T_APPT, $contentOptions);
            
             if($sqlConnect->query($query)){
            
                   $data = array(
                		'status' => 200,
                		'date' => "-",
                		'appt_id' => 0,
                		'message' => 'Successfully Deleted Appointment',
                	); 
            	
            }else{
                
                $data = array(
            		'status' => 400,
            		'message' => 'Error!, while processing your request',
            	);
            	
            }
            
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Could Not Find Appointment Details',
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

// Delete Offer for contacts
if( isset($_POST['action']) && ($_POST['action'] == "DeleteofferAction") ) {
    
    if(isset($_POST['offer_id'])){
        $offer_id = $_POST['offer_id'];
    }
    
    if(isset($_POST['upid'])){
        $user_id = $_POST['upid'];
    }
    
    if(isset($_POST['contact_id'])){
        $contact_id = $_POST['contact_id'];
    }
    
    if(isset($_POST['contact_type'])){
        $contact_type = $_POST['contact_type'];
    }
    
    
    if($user_id == $wo['user']['user_id']){
        
        if($contact_type == 1){
            $ctype = "contact";
        }else if($contact_type == 2){
            $ctype = "buyer";
        }else{
            $ctype = "property";
        }
        
        $contentOptions = [
            'contact_id' => $id,
            'offer_id' => $offer_id,
            'contact_type' => $ctype
        ];
    
        // Appoint 
        $offer = getBuyerOffer($contentOptions);
        
        if($offer){
            
            $query = deleteRow(T_OFFER_C, $contentOptions);
            
             if($sqlConnect->query($query)){
            
                   $data = array(
                		'status' => 200,
                		'date' => "-",
                		'offer_id' => 0,
                		'message' => 'Successfully Deleted Offer',
                	); 
            	
            }else{
                
                $data = array(
            		'status' => 400,
            		'message' => 'Error!, while processing your request',
            	);
            	
            }
            
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Could Not Find Offer Details',
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

// Create and Update Contract of Contacts
if( isset($_POST['action']) && ($_POST['action'] == "contractAction") ) {
   


    if(isset($_POST['date'])){
        $date = strtotime($_POST['date']);
    }
    
    if(isset($_POST['deal_id'])){
        $deal_id = $_POST['deal_id'];
    }
    
    if(isset($_POST['upid'])){
        $user_id = $_POST['upid'];
    }
    
    if(isset($_POST['pipe_id'])){
        $pipe_id = $_POST['pipe_id'];
    }
    
    if(isset($_POST['stage_id'])){
        $stage_id = $_POST['stage_id'];
    }
    
    if(isset($_POST['step_id'])){
        $step_id = $_POST['step_id'];
    }
    
    if(isset($_POST['contact_id'])){
        $contact_id = $_POST['contact_id'];
    }
    
    if(isset($_POST['contact_type'])){
        $contact_type = $_POST['contact_type'];
    }
    
    
    if($user_id == $wo['user']['user_id']){
        
        if($stage_id > 0 && $step_id > 0 && $pipe_id > 0 && !empty($date)){
          
            
            $createdBy = $user_id;
            $createAt = time();
            
            $options = [
                'date' => $date,
                'contact_type' => 'property',
                'contact_id' => $contact_id,
                'created_by' => $createdBy,
                'created_at' => $createAt,
                'pipeline_id' => $pipe_id,
                'stage_id' => $stage_id,
                'step_id' => $step_id
            ];
    
            if($deal_id){
                $where['contract_id'] = $deal_id;
    
                $query = updateRow(T_CONTRACT_C, $options, $where);
                
                $st = "Updated";
                
            }else{
                $query = insertRow(T_CONTRACT_C, $options);
                
                $st = "Created";
            }
            
            
            if($sqlConnect->query($query)){
                    
                createListing();

                if($deal_id > 0){
                    $dealid = $deal_id;
                }else{
                    $dealid = $sqlConnect->insert_id;
                }
                
                $data = array(
                    'status' => 200,
                    'date' => date('d/m/Y', $date),
                    'deal_id' => $dealid,
                    'message' => 'Successfully '.$st.' Contract',
                ); 
            	
            }else{
                
                pre(mysqli_error($sqlConnect)); exit; 
                $data = array(
            		'status' => 400,
            		'message' => 'Error!, while processing your request',
            	);
            	
            }
          
          
            
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'Error! Please fill all form fields',
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

// Delete Contract for contacts
if( isset($_POST['action']) && ($_POST['action'] == "DeletecontractAction") ) {
    
    if(isset($_POST['deal_id'])){
        $deal_id = $_POST['deal_id'];
    }
    
    if(isset($_POST['upid'])){
        $user_id = $_POST['upid'];
    }
    
    if(isset($_POST['contact_id'])){
        $contact_id = $_POST['contact_id'];
    }
    
    if(isset($_POST['contact_type'])){
        $contact_type = $_POST['contact_type'];
    }
    
    
    if($user_id == $wo['user']['user_id']){
        
        if($contact_type == 1){
            $ctype = "contact";
        }else if($contact_type == 2){
            $ctype = "buyer";
        }else{
            $ctype = "property";
        }
        
        $contentOptions = [
            'contact_id' => $id,
            'contract_id' => $deal_id,
            'contact_type' => $ctype
        ];
        
        $contentOptions2 = [
            'contract_id' => $deal_id,
        ];
    
        // Appoint 
        $offer = getBuyerContract($contentOptions);
        
        if($offer){
            
            $query = deleteRow(T_CONTRACT_C, $contentOptions);
            
             if($sqlConnect->query($query)){
            
                  $query2 = deleteRow(T_CONTRACT_DETAILS, $contentOptions2);
                  $sqlConnect->query($query2);
                    
                   $data = array(
                		'status' => 200,
                		'date' => "-",
                		'deal_id' => 0,
                		'message' => 'Successfully Deleted Contract',
                	); 
            	
            }else{
                
                $data = array(
            		'status' => 400,
            		'message' => 'Error!, while processing your request',
            	);
            	
            }
            
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Could Not Find Contract Details',
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

// Update Contract Details
if( isset($_POST['action']) && ($_POST['action'] == "updateContractDetails") ) {
    

    if(isset($_POST['c_title'])){
        $c_title = $_POST['c_title'];
    }
    
    if(isset($_POST['c_contract_id'])){
        $c_contract_id = $_POST['c_contract_id'];
    }
    
    if(isset($_POST['c_as_title'])){
        $c_as_title = $_POST['c_as_title'];
    }
    
    if(isset($_POST['c_purchase_price'])){
        $c_purchase_price = $_POST['c_purchase_price'];
    }
    
    if(isset($_POST['c_rehab_amount'])){
        $c_rehab_amount = $_POST['c_rehab_amount'];
    }
    
    if(isset($_POST['c_estimated_amount'])){
        $c_estimated_amount = $_POST['c_estimated_amount'];
    }
    
    if(isset($_POST['c_date'])){
        $c_date = strtotime($_POST['c_date']);
    }
    
    if(isset($_POST['c_buyer_id'])){
        $c_buyer_id = $_POST['c_buyer_id'];
    }
    
    if(isset($_POST['c_contact_id'])){
        $c_contact_id = $_POST['c_contact_id'];
    }
    
    if(isset($_POST['c_street'])){
        $c_street = $_POST['c_street'];
    }
    
    if(isset($_POST['c_city'])){
        $c_city = $_POST['c_city'];
    }
    
    if(isset($_POST['c_state'])){
        $c_state = $_POST['c_state'];
    }
    
    if(isset($_POST['c_zip'])){
        $c_zip = $_POST['c_zip'];
    }
    
    if(isset($_POST['c_property_type'])){
        $c_property_type = $_POST['c_property_type'];
    }
    
    if(isset($_POST['c_square_feet'])){
        $c_square_feet = $_POST['c_square_feet'];
    }
    
    if(isset($_POST['c_year_built'])){
        $c_year_built = $_POST['c_year_built'];
    }
    
    if(isset($_POST['c_has'])){
        $c_has = implode(',', $_POST['c_has']);
    }
    
    if(isset($_POST['c_stories'])){
        $c_stories = $_POST['c_stories'];
    }
    
    if(isset($_POST['c_bedrooms'])){
        $c_bedrooms = $_POST['c_bedrooms'];
    }
    
    if(isset($_POST['c_bathrooms'])){
        $c_bathrooms = $_POST['c_bathrooms'];
    }
    
    if(isset($_POST['c_offer_amount'])){
        $c_offer_amount = $_POST['c_offer_amount'];
    }
    
    if(isset($_POST['c_actual_profit'])){
        $c_actual_profit = $_POST['c_actual_profit'];
    }
    
    if(isset($_POST['c_exit_strategy'])){
        $c_exit_strategy = $_POST['c_exit_strategy'];
    }
    
    if(isset($_POST['c_lockbox'])){
        $c_lockbox = $_POST['c_lockbox'];
    }
    
    if(isset($_POST['upid'])){
        $user_id = $_POST['upid'];
    }
    
    $as_title = $c_as_title ? 1 : 2;
    
    $formInsert = [
      'title' => $c_title,
      'as_title' => $as_title,
       'contract_id' => $c_contract_id,
      'purchase_price' => $c_purchase_price,
      'rehab_amount' => $c_rehab_amount,
      'estimated_amount' => $c_estimated_amount,
      'date' => $c_date,
      'buyer_id' => $c_buyer_id,
      'contact_id' => $c_contact_id,
      'street' => $c_street,
      'city' => $c_city,
      'state' => $c_state,
      'zip' => $c_zip,
      'property_type' => $c_property_type,
      'square_feet' => $c_square_feet,
      'year_built' => $c_year_built,
      'has' => $c_has,
      'stories' => $c_stories,
      'bedrooms' => $c_bedrooms,
      'bathrooms' => $c_bathrooms,
      'offer_amount' => $c_offer_amount,
      'actual_profit' => $c_actual_profit,
      'lockbox' => $c_lockbox,
       'created_by' => $user_id,
       'created_at' => time(),
            
    ];
    
    $contractOption = [
        'contract_id' => $c_contract_id,
        'created_by' => $user_id,
    ];
    
    $cContent = getTableData('wo_contract_content', $contractOption, 1);
    
    if($user_id == $wo['user']['user_id']){
        
        if($cContent){
            
            $where = ['contract_id' => $cContent['contract_id']];
            $query = updateRow('wo_contract_content', $formInsert, $where);
            
            $st = "Updated";
            
        }else{
            
            $query = insertRow('wo_contract_content', $formInsert);
            
            $st = "Created";
            
        }
        if($sqlConnect->query($query)){
                
            createListing();
            $data = array(
        		'status' => 200,
        		'message' => 'Successfully '.$st.' Contract Details',
        	);
		
		}else{
		    
		    $data = array(
        		'status' => 400,
        		'message' => 'Error!, while processing your request',
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


function createListing() {

    global $sqlConnect, $wo;

    $reallisting = Wo_get_features_count('mylisting');
    $row = getTableData('Wo_Listing', ['id' => 795], 1);
    $formId = 0;
    if(isset($_POST['form_id'])) {
        $formId = $_POST['form_id'];
    }

    $form_data = [
        'user_id' => filter('user_id'), 
        'listing_title' => filter('c_title'),
        'entered_address' => filter('c_street'),
        'prop_type' => filter('c_property_type'),
        'deal_type' => filter('deal_type'),
        'beds' => filter('c_bedrooms'),
        'baths' => filter('c_bathrooms'),
        'constructions_year' => filter('c_built_year'),
        'flip_price' => filter('c_purchase_price'),
        'flip_arv' => filter('estimated_arv'),
        'flip_ext_repair' => filter('c_rehab_amount'),
        'rental_price' => filter('rental_price'),
        'rental_arv' => filter('expected_rent'),
        'rental_ext_rent' => filter('gross_rois'),
        'visibility' => filter('hide_address'),
        'promotion_note' => filter('instruction_notes'),
        'contract_id' => filter('c_contract_id'),
        'allow_promotion' => filter('allow_promotion') == 'true' ? 1 : 0,
        'estimated_repairs' => filter('c_estimated_repairs'),
        'video_link' => filter('video_link'), 
        'property_size' => filter('c_square_feet'), 
        'hide_details' => filter('hide_details') == 'true' ? 1 : 0, 
        'buy_now' => filter('buy_now') == 'true' ? 1 : 0, 
        'gift_price' => filter('gift_price'),
        'buy_now_price' => filter('buyPrice')
    ];
    
    
    if($_POST['user_id']){
        $user_id = $_POST['user_id'];
    }

    if($form_data['entered_address']){
        $map_address = $form_data['entered_address'];
    }
    
    // check if author activate buy now price....
    if(isset($_POST['buyNowBt'])){
        $buyNowBt = $_POST['buyNowBt'];
    }
    
    if(isset($_POST['buyPrice'])){
        $buyPrice = $_POST['buyPrice'];
    }

    
    $filename_cb = "";
    if (isset($_FILES['contractBuyUp']) && !empty($_FILES['contractBuyUp'])) {
        
        if (!empty($_FILES['contractBuyUp']["tmp_name"])) {
            
            $orignalname_cb = $_FILES['contractBuyUp']["name"];
            $fileInfo_cb = array(
                'file' => $_FILES["contractBuyUp"]["tmp_name"],
                'name' => $_FILES['contractBuyUp']['name'],
                'size' => $_FILES["contractBuyUp"]["size"],
                'type' => $_FILES["contractBuyUp"]["type"],
                'types' => 'doc,docx,pdf',
            );

            $media_cb = Wo_ShareFile($fileInfo_cb, 0, false);
            if (!empty($media_cb)) {
                
                $filename_cb = $media_cb['filename'];

            } 
            
        }
        
    }
    
    if (isset($_FILES['wireBuyUp']) && !empty($_FILES['wireBuyUp'])) {

        if (!empty($_FILES['wireBuyUp']["tmp_name"])) {
            
            $orignalname_wb = $_FILES['wireBuyUp']["name"];
            $filename_wb = "";
            $fileInfo_wb = array(
                'file' => $_FILES["wireBuyUp"]["tmp_name"],
                'name' => $_FILES['wireBuyUp']['name'],
                'size' => $_FILES["wireBuyUp"]["size"],
                'type' => $_FILES["wireBuyUp"]["type"],
                'types' => 'doc,docx,pdf',
            );

            $media_wb = Wo_ShareFile($fileInfo_wb, 0, false);
            if (!empty($media_wb)) {
                
                $filename_wb = $media_wb['filename'];

            } 
            
        }
        
    }
    
    $hide_address = $form_data['hide_address'];

    $about_property = mysqli_real_escape_string($sqlConnect, filter('about_property'));
    $property_size = $form_data['property_size'];
    $beds = $form_data['beds'];
    $baths = $form_data['baths'];
    $city_r = filter('c_city');
    $lot_size = 0;
    $gift_price = $_POST['gift_price'];
    $flip_price = $_POST['flip_price'];
    $flip_arv = $_POST['flip_arv'];
    $flip_ext_repair = $_POST['flip_ext_repair'];
    $rental_price = $form_data['rental_price'];
    $rental_arv = $form_data['rental_arv'];
    $listing_title = $form_data['listing_title'];
    $prop_type = $form_data['prop_type'];
    $deal_type = $form_data['deal_type'];
    $allow_promotion = $form_data['allow_promotion'];
    $promotion_note = serialize($_POST['promotion_note']);
    $video_link = $_POST['video_link'];

    if($_POST['tracking_script']){
        $tracking_script = $_POST['tracking_script'];
    }
        
    if($_POST['visibility']){
        $visibility = serialize($_POST['visibility']);
    }
    
    $protype = $wo['user']['pro_type'];
    
    if($protype > 1){
        $contact_per = "";
        if(isset($_POST['contact_per'])){
            $contact_per = $_POST['contact_per'];
        }
    }
    
    // Trigger variable for notification 	
    $success = 0;
    
    $propertyid = $_POST['propertycode'];
    
    // Buy Now documents
    $buyNow_docs = json_encode([
        "BN_upload_contract" => $filename_cb,
        "BN_upload_wire" => $filename_wb,
    ]);
    
    $form_data = json_encode($form_data);
    
    $date = date('Y-m-d H:i:s');
    $status = 1;
    // Get if image was uploaded to update the table details
    $query_selectcode = getRow("SELECT COUNT(`id`) as `count` FROM `Wo_Listing` WHERE `propertycode` = '{$propertyid}'");
    $myretuncodenum = $query_selectcode['count'];
    
    // Slugify the address
    $address = $map_address;
    
    $singlepageurarr = explode(" ",strtolower($address));
    $slug = implode("",explode(",",implode("-", $singlepageurarr)));
    
    if($reallisting > 0){
        if($myretuncodenum > 0){
            
            
            // UPDATE PROPERTY DETIALS IF ROW HAS BEEN CREATED BEFORE
            $query   = "UPDATE Wo_Listing SET `user_id`='{$user_id}',`dtae_time`='{$date}',`tab1`='{$form_data}',`allow_promotion` = '{$allow_promotion}', `tab4` ='{$buyNow_docs}', `description` = '{$about_property}', `tracking_script` = '{$tracking_script}', `status` = '{$status}' WHERE propertycode = '{$propertyid}' ";
            $sql_query = mysqli_query($sqlConnect, $query);
                
            
            // select property id with property code
            $proidquery = "SELECT `id` FROM `Wo_Listing` WHERE `propertycode` = '{$propertyid}'";
            $pro_sql_code  = mysqli_query($sqlConnect, $proidquery);
            $finalpro_id = mysqli_fetch_assoc($pro_sql_code);
            $property_id = $finalpro_id['id'];
            
            
            $queryfilter1   = "INSERT INTO Wo_Filter (`property_id`, `property_type`, `bathroom`, `bedroom`,`address`,`area`,`user_id`) VALUES ({$property_id},'{$prop_type}','{$baths}','{$beds}','{$map_address}','{$property_size}',{$user_id})";
            $sql_queryfilt1 = mysqli_query($sqlConnect, $queryfilter1);
            
                // Instert listing into newsfeed section and award point on the home page   
            if($sql_queryfilt1){
                
                Wo_RegisterPost(array(                    
                    'user_id' => Wo_Secure($wo['user']['user_id']),                    
                    'listing_id' => Wo_Secure($property_id),                    
                    'postText' => "<b>".$listing_title."</b>",                    
                    'time' => time(),                    
                    'postPrivacy' => '0',                    
                    'active' => 1                
                ),$property_id);  
                
            }
            
            
            /************ Check if slug already exists ************/
            $Getslugquery = mysqli_query($sqlConnect, "SELECT count(*) as slugcounts FROM `Wo_Listing_Meta` WHERE property_slug='".$slug."'");
            $Slugrows = mysqli_fetch_array($Getslugquery);
            $Slugcounts = $Slugrows['slugcounts'];

            if($Slugcounts > 0){
                
                $slug = $slug.'-'.$property_id;
                $querymeta   = "UPDATE Wo_Listing_Meta SET `property_slug`='{$slug}' WHERE `property_id`=".$property_id;
                $sql_querymeta = mysqli_query($sqlConnect, $querymeta);
                
            }else{
                
                $slug = $slug.'-'.$property_id;
            
                $querymeta   = "INSERT INTO Wo_Listing_Meta (`property_id`, `property_slug`) VALUES({$property_id},'{$slug}')";
                $sql_querymeta = mysqli_query($sqlConnect, $querymeta);
                
            }
            
                    
            if($sql_query){
                $success = 1;
                $data = array(
                    'status' => 200,
                    'r_id' => $property_id,
                    'message' => 'Property Added Successfully',
                    'url' => ''
                );
                
            }else{
                $data = array(
                    'status' => 400,
                    'message' => 'Error While uploading Property'
                );
            }

        }else{

            $query2 = "INSERT INTO Wo_Listing (`user_id`,`propertycode`,`dtae_time`,`tab1`,`allow_promotion`,`tab4`,`description`,`tracking_script`,`status`) VALUES ({$user_id},'{$propertyid}','{$date}','{$form_data}','{$allow_promotion}','{$buyNow_docs}','{$about_property}','{$tracking_script}',{$status})";
            $sql_query2 = mysqli_query($sqlConnect, $query2);
            $last_inserted_id = mysqli_insert_id($sqlConnect);
            
            
            $queryfilter   = "INSERT INTO Wo_Filter (`property_id`, `property_type`, `bathroom`, `bedroom`,`address`,`area`,`user_id`) VALUES ({$last_inserted_id},'{$prop_type}','{$baths}','{$beds}','{$map_address}','{$property_size}',{$user_id})";
            $sql_queryfilt = mysqli_query($sqlConnect, $queryfilter);
            
            
             // Instert listing into newsfeed section and award point on the home page
            if($sql_queryfilt){
                
                Wo_RegisterPost(array(                    
                    'user_id' => Wo_Secure($wo['user']['user_id']),                    
                    'listing_id' => Wo_Secure($last_inserted_id),                    
                    'postText' => "<b>".$listing_title."</b>",                    
                    'time' => time(),                    
                    'postPrivacy' => '0',                    
                    'active' => 1                
                ),$last_inserted_id);  
                
            }
        
            
            /************ Check if slug already exists ************/
            $Getslugquery = mysqli_query($sqlConnect, "SELECT count(*) as slugcounts FROM `Wo_Listing_Meta` WHERE property_slug='".$slug."'");
            $Slugrows = mysqli_fetch_array($Getslugquery);
            $Slugcounts = $Slugrows['slugcounts'];
            
            if($Slugcounts < 1){
                
                $slug = $slug.'-'.$last_inserted_id;
                
                $querymeta   = "INSERT INTO Wo_Listing_Meta (`property_id`, `property_slug`) VALUES({$last_inserted_id},'{$slug}')";
                $sql_querymeta = mysqli_query($sqlConnect, $querymeta);
                
            }else{
                
                 $slug = $slug.'-'.$last_inserted_id;
                 $querymeta   = "UPDATE Wo_Listing_Meta SET `property_slug`='{$slug}' WHERE `property_id`=".$last_inserted_id;
                 $sql_querymeta = mysqli_query($sqlConnect, $querymeta);
                 
            }
            
            
            
            if($sql_query2){
                
                $success = 1;
                $data = array(
                    'status' => 200,
                    'r_id' => $last_inserted_id,
                    'message' => 'Property Added Successfully',
                    'url' => ''
                );
                
            }else{
                
                $data = array(
                    'status' => 400,
                    'message' => 'Error While uploading Property'
                );
                
            }
    
        }
    }
}


?>