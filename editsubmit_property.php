<?php


global $wo, $sqlConnect;
require_once('config.php');

require_once('assets/init.php');




$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);


    	$formId = $_POST['form_id'];
    	
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
        
        if(!isset($_POST['buycontEd']) || $_POST['buycontEd'] == ""){
           
               if (isset($_FILES['contractBuyUp']) && !empty($_FILES['contractBuyUp'])) {
                
                if (!empty($_FILES['contractBuyUp']["tmp_name"])) {
                    
        			$orignalname_cb = $_FILES['contractBuyUp']["name"];
        			$filename_cb = "";
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
            
            
        }else{
            $filename_cb = $_POST['buycontEd'];
        }
        
        
        if(!isset($_POST['buywireEd']) || $_POST['buywireEd'] == ""){
            
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
            
        }else{
            
            $filename_wb = $_POST['buywireEd'];
        }
        
        
    	
    	if($_POST['hide_address']){
    	$hide_address = $_POST['hide_address'];
    	}
    	
    	if($_POST['about_property']){
    	$about_property = mysqli_real_escape_string($sqlConnect,$_POST['about_property']);
    	}
    	
    	if($_POST['property_size']){
    	$property_size = $_POST['property_size'];
    	}
    	
    	if($_POST['prop_type']){
    	$prop_type = $_POST['prop_type'];
    	}
    	
    	if($_POST['beds']){
    	$beds = $_POST['beds'];
    	}
    	
    	if($_POST['baths']){
    	$baths = $_POST['baths'];
    	}
    	
    	if($_POST['lot_size']){
    	$lot_size = $_POST['lot_size'];
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
    	$listing_title = $_POST['listing_title'];
    	}
    	
    	if($_POST['allow_promotion']){
    	$allow_promotion = $_POST['allow_promotion'];
    	}
    	
    	if($_POST['gift_price']){
    	$gift_price = $_POST['gift_price'];
    	}
    
    	if($_POST['promotion_note']){
    	$promotion_note = serialize($_POST['promotion_note']);
    	}
    	
    	if($_POST['video_link']){
    	$video_link = $_POST['video_link'];
    	}
    
    	if($_POST['visibility']){
    	$visibility = serialize($_POST['visibility']);
    	}
    	
    	if($_POST['tracking_script']){
    	    $tracking_script = $_POST['tracking_script'];
    	   // $tracking_script = base64_encode($_POST['tracking_script']);
    	}
    	
    	$protype = $wo['user']['pro_type'];
    	
    	if($protype > 1){
    	    if($_POST['contact_per']){
        	    $contact_per = $_POST['contact_per'];
        	}
    	}
    	
    	$propertyid = $_POST['property_id'];
        $is_private = $_POST['is_private'];
    	
    
        // Get all form fields as an array
    	$params = array();
    	$form_datanew = parse_str($_POST['form_data'], $params);
    	unset($params[ 'about_property' ]);
    	unset($params['tracking_script']);
    	
    	unset($params['wireBuyUp']);
    	unset($params['contractBuyUp']);
    	
    	unset($params['buywireEd']);
    	unset($params['buycontEd']);
    	
    	// Buy Now documents
        $buyNow_docs = json_encode([
        	"BN_upload_contract" => $filename_cb,
        	"BN_upload_wire" => $filename_wb,
        ]);
        
        
    	$form_data = json_encode($params);
    	
    	$date = date('Y-m-d H:i:s');
    	$status = 1;
    	
        
        // Slugify the address
        $address = $map_address;
    	$singlepageurarr = explode(" ",strtolower($address));
    	$slug = implode("",explode(",",implode("-", $singlepageurarr)));
    	
        
 
        $query   = "UPDATE Wo_Listing SET `user_id`='{$user_id}',`dtae_time`='{$date}',`tab1`='{$form_data}', `allow_promotion` = '{$allow_promotion}', `tab4` ='{$buyNow_docs}', `description` = '{$about_property}', `tracking_script` = '{$tracking_script}', `status` = '{$status}', `is_private` = '{$is_private}' WHERE id = '{$propertyid}' ";
        $sql_query = mysqli_query($sqlConnect, $query);
      
        
        $filtquery2   = "UPDATE Wo_Filter SET `property_type`='{$prop_type}', `bathroom`='{$baths}', `bedroom`='{$beds}',`address`='{$map_address}',`area`='{$property_size}',`user_id`={$user_id} WHERE property_id=".$propertyid;
        $sql_queryfilty2 = mysqli_query($sqlConnect, $filtquery2);
        
        // update data to post news feed
        $newlisting_title = "<b>".$listing_title."</b>";
        $getitin = mysqli_query($sqlConnect, "UPDATE ".T_POSTS." SET `postText` = '{$newlisting_title}' WHERE `listing_id` = {$propertyid}");
        
		/************ Check if slug already exists ************/
		$Getslugquery = mysqli_query($sqlConnect, "SELECT count(*) as slugcounts FROM `Wo_Listing_Meta` WHERE property_slug='".$slug."'");
		$Slugrows = mysqli_fetch_array($Getslugquery);
		$Slugcounts = $Slugrows['slugcounts'];

		if($Slugcounts > 0){
		    $slug = $slug.'-'.$propertyid;
		    $querymeta   = "UPDATE Wo_Listing_Meta SET `property_slug`='{$slug}' WHERE `property_id`=".$propertyid;
		    $sql_querymeta = mysqli_query($sqlConnect, $querymeta);
		}else{
		    $slug = $slug.'-'.$propertyid;
        
            $querymeta   = "INSERT INTO Wo_Listing_Meta (`property_id`, `property_slug`) VALUES({$propertyid},'{$slug}')";
            $sql_querymeta = mysqli_query($sqlConnect, $querymeta);
		}
		
				
        if($sql_query)
        {
            
            
            // Uploading and updating the verification file.
            
            if ($verificationFile = filterUpload('verify_property_file')) {
            	uploadVerificationFile($propertyid, $verificationFile);
            }
            
            // Sending Notifications
    		//sendNotificationWithType('property_match', $propertyid);
    		
            $data = array(
                'status' => 200,
                'message' => 'Property Edited Successfully',
                'url' => ''
            );
            
        }else{
            $data = array(
                'status' => 400,
                'message' => 'Error While uploading Property'
            );
        }
    
        header("Content-type: application/json");
        echo json_encode($data);
        die;



	
	?>