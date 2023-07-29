<?php

// Setting Global values
global $wo, $sqlConnect;

// Set all required files 
require_once('config.php');
require_once('assets/init.php');

// database setter
$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);

// SendGrid Configuration
require_once('sendgrid-php/sendgrid-php.php');
use SendGrid\Mail\Personalization;
use SendGrid\Mail\To;



// 
// 
// 
// ADD NEW PROPERTY ACTION
// 
// 
// 
// 
// 


// Setting Values
$user_id = $wo['user']['user_id'];
$userPackage = $wo['user']['my_package'];
$path = "add-listing";

if(isset($_POST['action']) && $_POST['action'] == "addNewProperty_".$user_id) {
    
    // check if user id is not the same as the one in the form
    if($user_id === $_POST['user_id']){

        // get the feature value from package system
        $packageSystem = getFeatureValue($path,$userPackage);

        // Check if access is countable
        $up = getUserPackages($user_id,$userPackage,$path);
        // get the user package value by the page Path name
        $up = json_decode(getUserPackages($user_id,$userPackage,$path)[$path], true);
        
        if( $packageSystem && ($up['value'] == "counter" && $up['expectedValue'] - $up['usedValue'] >= 1) || ($up['value'] == "allow")){
            
            // get all form fields
            $formId = 0;
        	if(isset($_POST['form_id'])) {
        	    $formId = $_POST['form_id'];
        	}
        	
        	$form_data = $_POST['form_data'];
    
        	if($_POST['user_id']){
        	    $user_id = $_POST['user_id'];
        	}
        
        	if($_POST['map_address']){
        	    $map_address = $_POST['map_address'];
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
            
            $filename_wb = "";
            if (isset($_FILES['wireBuyUp']) && !empty($_FILES['wireBuyUp'])) {
        
                if (!empty($_FILES['wireBuyUp']["tmp_name"])) {
                    
        			$orignalname_wb = $_FILES['wireBuyUp']["name"];
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
            
            
           	if($_POST['hide_address']){
        	    $hide_address = $_POST['hide_address'];
        	}
        	
        	if($_POST['about_property']){
        	    $about_property = Wo_Secure($_POST['about_property']);
        	}
        	
        	if($_POST['property_size']){
        	    $property_size = $_POST['property_size'];
        	}
        	
        	if($_POST['beds']){
        	    $beds = $_POST['beds'];
        	}
        	
        	if($_POST['baths']){
        	    $baths = $_POST['baths'];
        	}
        	
            $city_r = "";
        	if(isset($_POST['city_r'])){
        	    $city_r = Wo_Secure($_POST['city_r']);
        	}
        	
            $lot_size = 0;
        	if(isset($_POST['lot_size'])){
        	    $lot_size = $_POST['lot_size'];
        	}
        	
        	if($_POST['gift_price']){
        	    $gift_price = $_POST['gift_price'];
        	}
        	
        	if($_POST['flip_price']){
        	    $flip_price = $_POST['flip_price'];
        	}
        	
        	if($_POST['flip_arv']){
        	    $flip_arv = $_POST['flip_arv'];
        	}
        	
        	if($_POST['flip_ext_repair']){
        	    $flip_ext_repair = $_POST['flip_ext_repair'];
        	}
        	
        	if($_POST['rental_price']){
        	    $rental_price = $_POST['rental_price'];
        	}
        	
        	if($_POST['rental_arv']){
        	    $rental_arv = $_POST['rental_arv'];
        	}
        	
        	if($_POST['rental_ext_rent']){
        	    $rental_ext_rent = $_POST['rental_ext_rent'];
        	}
        	
        	if($_POST['listing_title']){
        	    $listing_title = Wo_Secure($_POST['listing_title']);
        	}
        	
        	if($_POST['prop_type']){
        	    $prop_type = $_POST['prop_type'];
        	}
        	
        	if($_POST['deal_type']){
        	    $deal_type = $_POST['deal_type'];
        	}
        	
        	if($_POST['allow_promotion']){
        	    $allow_promotion = $_POST['allow_promotion'];
        	}
        
        	if($_POST['promotion_note']){
        	    $promotion_note = serialize(Wo_Secure($_POST['promotion_note']));
        	}
        	
        	if($_POST['video_link']){
        	    $video_link = $_POST['video_link'];
        	}
        	
        	if($_POST['tracking_script']){
        	    $tracking_script = $_POST['tracking_script'];
        	}
        
        	//PACKAGE INPUTS
            if($_POST['access_id']){
        	    $access_id = $_POST['access_id'];
        	}
        	
        	if($_POST['access_used']){
        	    $access_used = $_POST['access_used'];
        	}
        	
        	if($_POST['access_remain']){
        	    $access_remain = $_POST['access_remain'];
        	}
        	
        	
        	if($_POST['visibility']){
        	    $visibility = serialize($_POST['visibility']);
        	}
        	
        	$is_push = filter('is_push', 0);
        	
        	
        	
        	// Trigger variable for notification 	
        	$success = 0;
        	
        	$propertyid = $_POST['propertycode'];
            $is_private = $_POST['is_private'];
        	
        	// Get all form fields as an array
        	$params = array();
        	
            $is_push = filter('is_push', 0);
            $parent_id = filter('parent_id', 0);
        	$form_datanew = parse_str($_POST['form_data'], $params);
        	
        	unset($params['about_property']);
        	unset($params['tracking_script']);
        	unset($params['wireBuyUp']);
        	unset($params['contractBuyUp']);
        	
        	// Buy Now documents
            $buyNow_docs = json_encode([
            	"BN_upload_contract" => $filename_cb,
            	"BN_upload_wire" => $filename_wb,
            ]);
    	
            $params['is_push'] = $is_push;
            $params['parent_id'] = $parent_id;
            
        	$form_data = json_encode($params);
        	$date = date('Y-m-d H:i:s');
        	$status = 1;
        	
            // Get if image was uploaded to update the table details
            $query_selectcode = "SELECT COUNT(`id`) as `count` FROM `Wo_Listing` WHERE `propertycode` = '{$propertyid}'";
            $sql_code       = mysqli_query($sqlConnect, $query_selectcode);
            $sql_fetch_selectcode = mysqli_fetch_assoc($sql_code);
            $myretuncodenum = $sql_fetch_selectcode['count'];
            
            
            parse_str($_POST['form_data'], $formData);
            
            $propertyFilters = [
                'firstname' => $formData['listing_title'], 
                'prstreetadd' => $formData['entered_address'], 
                'prcity' => $formData['city'], 
                'prstate' => $formData['state'], 
                'przip' => $formData['postal_code'], 
                'beds' => $formData['beds'], 
                'bath' => $formData['baths'],  
                'offer_amount' => $formData['rental_price'],
                'deal_notes' => $formData['about_property'], 
                'type' => '3',
                'contactinsertedby' => $wo['user']['user_id']
            ]; 
            
            
            // Slugify the address
            $address = $map_address;
        	$singlepageurarr = explode(" ",strtolower($address));
        	$slug = implode("",explode(",",implode("-", $singlepageurarr)));

            	if($myretuncodenum > 0){
            	    
            	    $data = array(
            	        'user_id' => $user_id,
            	        'dtae_time' => $date,
            	        'tab1' => $form_data,
            	        'allow_promotion' => $allow_promotion,
            	        'tab4' => $buyNow_docs,
            	        'description' => $about_property,
            	        'status' => $status,
            	        'tracking_script' => $tracking_script,
            	        'is_private' => $is_private
            	    );
            	    
            	    
            	    $sql_query = updatePropertyByPropCode($data,$propertyid);
            	    
            	    
                    // UPDATE PROPERTY DETIALS IF ROW HAS BEEN CREATED BEFORE
                    // $query   = "UPDATE Wo_Listing SET `user_id`='{$user_id}',`dtae_time`='{$date}',`tab1`='{$form_data}',`allow_promotion` = '{$allow_promotion}', `tab4` ='{$buyNow_docs}', `description` = '{$about_property}', `status` = '{$status}' WHERE propertycode = '{$propertyid}' ";
                    // $sql_query = mysqli_query($sqlConnect, $query);
                    
                    
                    // select property id with property code
                    $proidquery = "SELECT `id` FROM `Wo_Listing` WHERE `propertycode` = '{$propertyid}'";
                    $pro_sql_code  = mysqli_query($sqlConnect, $proidquery);
                    $finalpro_id = mysqli_fetch_assoc($pro_sql_code);
                    $property_id = $finalpro_id['id'];
            	
    		
    		
    		        
                    // Sending Notifications
            		sendNotificationWithType('property_match', $property_id);
            		
            		

                    // Uploading and updating the verification file.
                    if ($verificationFile = filterUpload('verify_property_file')) {
                        uploadVerificationFile($property_id, $verificationFile);
                    }
                
                    $data2 = array(
            	       'property_id' => $property_id,
            	       'property_type' => $prop_type,
            	       'bathroom' => $baths,
            	       'bedroom' => $beds,
            	       'address' => $map_address,
            	       'area' => $property_size,
            	       'user_id' => $user_id,
            	    );
            	    
            	    
            	   // $sql_queryfilt1 = CreatePropertyFilter($data2);
                
                    $queryfilter1   = "INSERT INTO Wo_Filter (`property_id`, `property_type`, `bathroom`, `bedroom`,`address`,`area`,`user_id`) VALUES ({$property_id},'{$prop_type}','{$baths}','{$beds}','{$map_address}','{$property_size}',{$user_id})";
                    $sql_queryfilt1 = mysqli_query($sqlConnect, $queryfilter1);
                    
                    
                    
                     // Instert listing into newsfeed section and award point on the home page   
                    if($sql_queryfilt1 && !$is_push){
                        
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
                        
                        $queryc = insertRow('contact', $propertyFilters);
                        $sqlConnect->query($queryc);
                        
                        // Create Criteria Cron Job
                        $now = time();
                        $minutes = 5;
                        $seconds = ($minutes * 60); // 2400 seconds
                        $future  = ($now + $seconds);
                        
                        $criteriaCron = array(
                            'cron_id' => $property_id,
                            'time_to_run' => $future,
                            'user_type' => $wo['user']['admin'] == "2" ? "admin" : "user",
                            'status' => 1
                        );
                        
                        saveCriteriaCronJobs($criteriaCron);
                        $matched = getMatchedBuyersWithProperty($property_id, 1);
                        
                        $success = 1;
                        $data = array(
                            'status' => 200,
                            'r_id' => $property_id,
                            'message' => 'Property Added Successfully',
                            'url' => '',
                            'count' => $matched
                        );
                        
                    }else{
                        $data = array(
                            'status' => 400,
                            'message' => 'Error While uploading Property'
                        );
                    }
            
            

                }else{
                    
                    
                    $data = array(
            	        'user_id' => $user_id,
            	        'dtae_time' => $date,
            	        'tab1' => $form_data,
            	        'allow_promotion' => $allow_promotion,
            	        'tab4' => $buyNow_docs,
            	        'description' => $about_property,
            	        'status' => $status,
            	        'propertycode' => $propertyid,
            	        'tracking_script' => $tracking_script,
            	    );
            	    
            	    $last_inserted_id = saveNewProperty($data);
            	
            	    if(!$is_push){
            	       // Sending Notifications
            		    sendNotificationWithType('property_match', $last_inserted_id);
            	    }
    		
                    
            		
            		

                    // Uploading and updating the verification file.
                    if ($verificationFile = filterUpload('verify_property_file')) {
                        uploadVerificationFile($last_inserted_id, $verificationFile);
                    }
            		
            	    $data2 = array(
            	       'property_id' => $last_inserted_id,
            	       'property_type' => $prop_type,
            	       'bathroom' => $baths,
            	       'bedroom' => $beds,
            	       'address' => $map_address,
            	       'area' => $property_size,
            	       'user_id' => $user_id,
            	    );
            	    
            	   // $sql_queryfilt = CreatePropertyFilter($data2);
                    
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
                	
                    
                    
                    if($last_inserted_id > 0){
                        
                        
            
                        $queryc = insertRow('contact', $propertyFilters);
                        $sqlConnect->query($queryc);
                        
                        // Create Criteria Cron Job
                        $now     = time(); // Seconds since 1970-01-01 00:00:00
                        $minutes = 5;
                        $seconds = ($minutes * 60); // 2400 seconds
                        $future  = ($now + $seconds);
                        
                        $criteriaCron = array(
                            'cron_id' => $last_inserted_id,
                            'time_to_run' => $future,
                            'user_type' => $wo['user']['admin'] == "2" ? "admin" : "user",
                            'status' => 1
                        );
                        
                        saveCriteriaCronJobs($criteriaCron);
                        
                        $matched = getMatchedBuyersWithProperty($property_id, 1);
                        $success = 1;
                        $data = array(
                            'status' => 200,
                            'r_id' => $last_inserted_id,
                            'message' => 'Property Added Successfully',
                            'url' => '',
                            'count' => $matched
                        );
                        
                    }else{
                        
                        $data = array(
                            'status' => 400,
                            'message' => 'Error While uploading Property'
                        );
                        
                    }
                
                
                
            }

            
        }else{
            
            $data = array(
        		'status' => 401,
        		'message' => $wo['message']['package_limit'],
        	); 
            
            
        }
        
    
        
    }else{
        
        $data = array(
    		'status' => 401,
    		'message' => $wo['message']['account_denied'],
    	); 
        	
    } 
    
    
    header("Content-type: application/json");
    echo json_encode($data);
    die;
    
}else{
    
    $data = array(
		'status' => 401,
		'message' => $wo['message']['account_denied'],
	); 
    
    header("Content-type: application/json");
    echo json_encode($data);
    die;
}

if(!isset($_POST['action']) && $_POST['action'] == ""){
    
    $data = array(
        'status' => 400,
        'message' => $wo['message']['Action_Access_denied'],
    );
    
}


if(isset($_POST['action']) && $_POST['action']=="change_status"){
    
    $property_id = $_POST['property_id'];
 	$property_status = $_POST['property_status'];


 	switch ($property_status) {
 		case 'under-contract':
 			$status = 4;
 			break;

		case 'closed':
 			$status = 3;
 			break;

		case 'delete':
 			$status = 2;
 			break;

		case 'inactive':
 			$status = 0;
 			break;
 		default:
 			# code...
 			break;
 	}


	$query_one = mysqli_query($sqlConnect, "UPDATE `Wo_Listing` SET `status` = $status WHERE `id` = $property_id");
	
	$removenewsfeed = mysqli_query($sqlConnect, "DELETE FROM " . T_POSTS . " WHERE listing_id =".$property_id);
    
}


if(isset($_POST['action']) && $_POST['action']=="move_property_to_marketing"){
    
    $storeFolderP = 'themes/wondertag/uploads_images';
    $pro_id = $_POST['prop_id'];
    $my_id = $wo['user']['user_id'];
    if($pro_id != 0 || $pro_id != ''){
    
        $query = mysqli_query($sqlConnect,"SELECT * FROM `Wo_Listing` WHERE id = $pro_id");
    	$row = mysqli_fetch_array($query);
    
    	$propertydesc = $row['description'];
    	$propertyCode = $row['propertycode'];
    	$tab1 = json_decode($row["tab1"]);
    	$tab2 = json_decode($row["tab2"]);
    	$tab3 = json_decode($row["tab3"]);
    	$tab4 = json_decode($row["tab4"]);
    	$tab5 = json_decode($row["tab5"]);
    	$tab6 = $row["tab6"];
    	$tab7 = json_decode($row["tab7"]);
        $tab7new = $row["tab7"];
        
        // Get Construction year 		
    	$prop_year_built = $tab1->constructions_year;
    	$title = $tab1->listing_title;
        $price = $tab1->flip_price;
        $beds = $tab1->beds;
        $baths = $tab1->baths;
        $size = $tab1->property_size;
        $prop_type = $tab1->prop_type;
        $postal_code = $tab1->postal_code;
        $country = $tab1->country;
        $state = $tab1->state;
        $city = $tab1->city;
        $area = $tab1->city_r;
        $video_link = $tab1->video_link;
        $deal_type = $tab1->deal_type;
        $flip_arv = $tab1->flip_arv;
        $flip_ext_repair = $tab1->flip_ext_repair;
        $rental_price = $tab1->rental_price;
        $rental_arv = $tab1->rental_arv;
        $rental_ext_rent = $tab1->rental_ext_rent;
        $promotion_note = $tab1->promotion_note;
        
        
        $embed_video = Wo_get_youtube_link($video_link);
        
        $address = $tab1->entered_address;
    	
    	$visibility = $tab1->visibility;
        
        // SQFT PRICE
        $sqft_p =  $price/$size;
        
        // Author details fetch
        $author_id = $row['user_id'];
        $author_d = Wo_UserData($author_id);
        $autho_name = $author_d['name'];
        $author_avat = $author_d['avatar'];
        $autho_uname = $author_d['username'];
        $autho_phone = $author_d['phone_number'];
        $autho_email = $author_d['email'];
        
        $stage = 1;
        $propertyid = $pro_id;
        $date = date('Y-m-d H:i:s');
        $about_property = $propertydesc;
        $status = 1;
        
        
        $n_tab1 = array();
        $n_tab1['seller_name'] = $autho_name;
        $n_tab1['seller_email'] = $autho_email;
        $n_tab1['entered_address'] = $address;
        $n_tab1['city'] = $city;
        $n_tab1['postal_code'] = $postal_code;
        $n_tab1['country'] = $country;
        $n_tab1['user_id'] = $my_id;
        $n_tab1['beds'] = $beds;
        $n_tab1['baths'] = $baths;
        $n_tab1['property_size'] = $size;
        $n_tab1['deal_type'] = $deal_type;
        $n_tab1['estimated_repairs'] = 0;
        $n_tab1['prop_type'] = $prop_type;
        $n_tab1['seller_phone'] = $autho_phone;
        $n_tab1['occupancy'] = '';
        $n_tab1['lead_temp'] = 0;
        $n_tab1['constructions_year'] = $prop_year_built;
        $n_tab1['month_rent'] = '';
        $n_tab1['arv_amount'] = '';
        $n_tab1['flip_price'] = $price;
        $n_tab1['flip_arv'] = $flip_arv;
        $n_tab1['flip_ext_repair'] = $flip_ext_repair;
        $n_tab1['rental_price'] = $rental_price;
        $n_tab1['rental_arv'] = $rental_arv;
        $n_tab1['rental_ext_rent'] = $rental_ext_rent;
        $n_tab1['listing_title'] = $title;
        $n_tab1['promotion_note'] = $promotion_note;
        $n_tab1['stage'] = $stage;
        $n_tab1['visibility'] = 0;
        $n_tab1['moved'] = 1;
        
    	$form_data = json_encode($n_tab1);
    	
    	$query2 = "INSERT INTO crm_lead_property_marketing (`user_id`,`crm_stage_id`,`propertycode`,`dtae_time`,`tab1`,`tab3`,`description`,`status`) VALUES ({$my_id},{$stage},'{$propertyCode}','{$date}','{$form_data}','{$tab6}','{$about_property}',{$status})";
        $sql_query2 = mysqli_query($sqlConnect, $query2);
        $last_inserted_id = mysqli_insert_id($sqlConnect);
        
            
            if($sql_query2)
            {
                
                
                $data = array(
                    'status' => 200,
                    'message' => 'Property Successfully Moved',
                    'url' => $wo['config']['site_url'].'/property-funnel'
                );
            }else{
                $data = array(
                    'status' => 400,
                    'message' => 'Error While Moving Property'
                );
            }
        
    }else{
      $data = array(
            'status' => 400,
            'message' => 'Please Provide Propety Id'
        ); 
        
    }
    
    header("Content-type: application/json");
    echo json_encode($data);
    die;

}



	
	?>