<?php

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');
$ds  = DIRECTORY_SEPARATOR;

require_once('sendgrid-php/sendgrid-php.php');

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);
$storeFolder = 'themes/wondertag/uploads_images';

if(isset($_POST['action']) && $_POST['action']== "SendEmailLead"){
    $realleademail = Wo_get_features_count('broadcast_mail'); 
    
    $lead_id = $_POST['lead_id'];
    
	if($_POST['to_email']){
	    $to_email = $_POST['to_email'];
	}
	
	if($_POST['from_email']){
	    $from_email = $_POST['from_email'];
	}
	
	if($_POST['subject']){
	    $subject = $_POST['subject'];
	}
	
	if($_POST['message']){
	    $message = $_POST['message'];
	}
    
    if($realleademail > 0){
        
        if($to_email != $from_email){
            
            // messsage function..
            $email = new \SendGrid\Mail\Mail(); 
            $email->setfrom($from_email);
            $email->setSubject($subject);
            $email->addto($to_email);
            $email->addContent("text/html", $message);
            $sendgrid = new \SendGrid('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');
            try {
                $response = $sendgrid->send($email);
                
                $data = array(
                    'status' => 200,
                    'message' => 'Email Message Successfully Sent'
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
                'message' => "Sorry Mail Could Not send. Senders Email cant be the same as reciever."
            );
            
        }
        
        
        }else{ 
            $data = array(
                'status' => 401,
                'message' => 'over_limit'
            );
        }
    
     header("Content-type: application/json");
echo json_encode($data);
die;
    
}


if(isset($_POST['action']) && $_POST['action']== "SendSMSLead"){
    $realleadSMS = Wo_get_features_count('broadcast_sms'); 
    
    $lead_id = $_POST['lead_id'];
    
	if($_POST['to_phone']){
	    $to_phone = $_POST['to_phone'];
	}
	
	if($_POST['from_phone']){
	    $from_phone = $_POST['from_phone'];
	}
	
	
	if($_POST['message']){
	    $message = $_POST['message'];
	}
    
    if($realleadSMS > 0){
        
        if($to_phone != $from_phone){
            
            $sendSMSMsg = send_bulk_sms_broadcast($to_phone,$from_phone,$message);
            if($sendSMSMsg){
                
                $data = array(
                    'status' => 200,
                    'message' => 'SMS Message Successfully Sent'
                );
            }else{
              
               $data = array(
                    'status' => 400,
                    'message' => "Error SMS Could Not send"
                );  
            }
           
        
        }else{
            $data = array(
                'status' => 400,
                'message' => "Sorry SMS Could Not send. Senders Phone Number can't be the same as reciever."
            );
            
        }
        
        
        }else{ 
        $data = array(
            'status' => 401,
            'message' => 'over_limit'
        );
    }
    
     header("Content-type: application/json");
echo json_encode($data);
die;
    
}

if(isset($_POST['action']) && $_POST['action']== "SendEmailLeadWallet"){
    
    $lead_id = $_POST['lead_id'];
    
	if($_POST['to_email']){
	    $to_email = $_POST['to_email'];
	}
	
	if($_POST['from_email']){
	    $from_email = $_POST['from_email'];
	}
	
	if($_POST['subject']){
	    $subject = $_POST['subject'];
	}
	
	if($_POST['message']){
	    $message = $_POST['message'];
	}
	
	if($to_email != $from_email){
            
            // messsage function..
            $email = new \SendGrid\Mail\Mail(); 
            $email->setfrom($from_email);
            $email->setSubject($subject);
            $email->addto($to_email);
            $email->addContent("text/html", $message);
            $sendgrid = new \SendGrid('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');
            try {
                $response = $sendgrid->send($email);
                
                $data = array(
                    'status' => 200,
                    'message' => 'Email Message Successfully Sent'
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
                'message' => "Sorry Mail Could Not send. Senders Email cant be the same as reciever."
            );
            
        }
    
    
    header("Content-type: application/json");
    echo json_encode($data);
    die;
    

}


if(isset($_POST['action']) && $_POST['action']== "SendSMSLeadWallet"){
    
    $lead_id = $_POST['lead_id'];
    
	if($_POST['to_phone']){
	    $to_phone = $_POST['to_phone'];
	}
	
	if($_POST['from_phone']){
	    $from_phone = $_POST['from_phone'];
	}
	
	
	if($_POST['message']){
	    $message = $_POST['message'];
	}
	
	if($to_phone != $from_phone){
            
            $sendSMSMsg = send_bulk_sms_broadcast($to_phone,$from_phone,$message);
            if($sendSMSMsg){
                
                $data = array(
                    'status' => 200,
                    'message' => 'SMS Message Successfully Sent'
                );
            }else{
              
               $data = array(
                    'status' => 400,
                    'message' => "Error SMS Could Not send"
                );  
            }
        
        }else{
            $data = array(
                'status' => 400,
                'message' => "Sorry SMS Could Not send. Senders Phone Number can't be the same as reciever."
            );
            
        }
    
    
    header("Content-type: application/json");
    echo json_encode($data);
    die;
    

}

if(isset($_POST['action']) && $_POST['action']=="delete_lead_prop"){
    if($_POST['lead_id'] > 0){
        $id = $_POST['lead_id'];
        
        $queryfilter = mysqli_query($sqlConnect,"SELECT `tab3` FROM `crm_lead_property_marketing` WHERE `id` = $id");
	    $rowfilter = mysqli_fetch_array($queryfilter);
    
        // Delete files
        $tab3 = unserialize($rowfilter["tab3"]);
        if(isset($tab3)){
            $targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;
            foreach ($tab3 as $key => $value) {
                $filename = $targetPath.$tab3[$key];  
                  unlink($filename); 
                 // exit;
            } 
            closedir($targetPath);
            
            
        }
        
        $deletequery = mysqli_query($sqlConnect,"DELETE FROM `crm_lead_property_marketing` WHERE `id` = $id"); 
        
        
        if($deletequery){
            
            $data = array(
                'status' => 200,
                'message' => 'Lead Property Deleted Successfully'
            );
            
        }else{
            $data = array(
                'status' => 400,
                'message' => 'Error while processing delete request'
            );
        }
        
        
        
        
    }else{
        $data = array(
                'status' => 400,
                'message' => 'Sorry Lead not found'
        );
    }
    
    header("Content-type: application/json");
echo json_encode($data);
die;
}


if(isset($_POST['action']) && $_POST['action']=="edit_lead_edit_modal"){
    if($_POST['property_id'] > 0){
        
            $formId = $_POST['form_id'];
        	
        	$form_data = $_POST['form_data'];
        
        	if($_POST['user_id']){
        	$user_id = $_POST['user_id'];
        	}
        	
        	if($_POST['about_property']){
        	$about_property = $_POST['about_property'];
        	}
        	
        	if($_POST['stage']){
        	$stage = $_POST['stage'];
        	}
        	
        	$propertyid = $_POST['property_id'];
        	
        
            // Get all form fields as an array
        	$params = array();
        	$form_datanew = parse_str($_POST['form_data'], $params);
        	unset($params['about_property']);
        	$form_data =	json_encode($params);
        	$date = date('Y-m-d H:i:s');
        	$status = 1;
        	
        	
            // Get if image was uploaded to update the table details
            $query_selectcode2 = "SELECT COUNT(`id`) as `count` FROM `crm_lead_property_marketing` WHERE `id` = '{$propertyid}'";
            $sql_code2       = mysqli_query($sqlConnect, $query_selectcode2);
            $sql_fetch_selectcode2 = mysqli_fetch_assoc($sql_code2);
            $myretuncodenum2 = $sql_fetch_selectcode2['count'];
            
        
        	if($myretuncodenum2 > 0){
        	    
        
                $query   = "UPDATE crm_lead_property_marketing SET `user_id`='{$user_id}', `crm_stage_id` = '{$stage}', `dtae_time`='{$date}',`tab1`='{$form_data}', `description` = '{$about_property}', `status` = '{$status}' WHERE id = '{$propertyid}' ";
                $sql_query = mysqli_query($sqlConnect, $query);
                
                if($sql_query)
                {
                    $data = array(
                        'status' => 200,
                        'message' => 'Lead Property Edited Successfully',
                    );
                    
                }else{
                    $data = array(
                        'status' => 400,
                        'message' => 'Error While Editing Lead Property'
                    );
                }
                
                
    
            }else{
                    
                
                $data = array(
                    'status' => 400,
                    'message' => 'No lead value sent'
                );
            }
      
    
    
    }
    
header("Content-type: application/json");
echo json_encode($data);
die;
        
}

if(isset($_POST['action']) && $_POST['action']=="restage_lead") {

 	$stage_id = $_POST['stage_id'];
 	$lead_id = $_POST['lead_id'];



	$query_one = mysqli_query($sqlConnect, "UPDATE `crm_lead_property_marketing` SET `crm_stage_id` = '$stage_id' WHERE `id` = {$lead_id}");
	


}


if(isset($_POST['action']) && $_POST['action']=="Add_new_lead_prop") {
    $reallisting = Wo_get_features_count('myleadlisting');
    

    	$formId = $_POST['form_id'];
    	
    	$form_data = $_POST['form_data'];
    
    	if($_POST['user_id']){
    	$user_id = $_POST['user_id'];
    	}
    	
    	if($_POST['about_property']){
    	$about_property = $_POST['about_property'];
    	}
    	
    	if($_POST['stage']){
    	$stage = $_POST['stage'];
    	}
    	
    	$propertyid = $_POST['propertycode'];
    	
    
        // Get all form fields as an array
    	$params = array();
    	$form_datanew = parse_str($_POST['form_data'], $params);
    	unset($params['about_property']);
    	$form_data =	json_encode($params);
    	$date = date('Y-m-d H:i:s');
    	$status = 1;
    	
        // Get if image was uploaded to update the table details
        $query_selectcode = "SELECT COUNT(`id`) as `count` FROM `crm_lead_property_marketing` WHERE `propertycode` = '{$propertyid}'";
        $sql_code       = mysqli_query($sqlConnect, $query_selectcode);
        $sql_fetch_selectcode = mysqli_fetch_assoc($sql_code);
        $myretuncodenum = $sql_fetch_selectcode['count'];
        
        
        // Slugify the address
        $address = $map_address;
    	$singlepageurarr = explode(" ",strtolower($address));
    	$slug = implode("",explode(",",implode("-", $singlepageurarr)));
    	
    	if($reallisting > 0){
    
        	if($myretuncodenum > 0){
        	    
        
                $query   = "UPDATE crm_lead_property_marketing SET `user_id`='{$user_id}', `crm_stage_id` = '{$stage}', `dtae_time`='{$date}',`tab1`='{$form_data}', `description` = '{$about_property}', `status` = '{$status}' WHERE propertycode = '{$propertyid}' ";
                $sql_query = mysqli_query($sqlConnect, $query);
                
    			
        				
            if($sql_query)
            {
                $data = array(
                    'status' => 200,
                    'message' => 'Lead Property Added Successfully',
                    'url' => ''
                );
                
            }else{
                $data = array(
                    'status' => 400,
                    'message' => 'Error While uploading Lead Property'
                );
            }
            
            

        }else{
                
            $query2 = "INSERT INTO crm_lead_property_marketing (`user_id`,`crm_stage_id`,`propertycode`,`dtae_time`,`tab1`,`description`,`status`) VALUES ({$user_id},{$stage},'{$propertyid}','{$date}','{$form_data}','{$about_property}',{$status})";
            $sql_query2 = mysqli_query($sqlConnect, $query2);
            $last_inserted_id = mysqli_insert_id($sqlConnect);
        
            
            if($sql_query2)
            {
                $data = array(
                    'status' => 200,
                    'message' => 'Lead Property Added Successfully',
                    'url' => ''
                );
            }else{
                $data = array(
                    'status' => 400,
                    'message' => 'Error While uploading Lead Property'
                );
            }
            
            
            
        }
    }else{
        $data = array(
                'status' => 401,
                'message' => 'over_limit'
            );
    }

header("Content-type: application/json");
echo json_encode($data);
die;
        
}

	
	?>