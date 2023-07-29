<?php 

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

$user_id = $wo['user']['user_id'];

require_once('sendgrid-php/sendgrid-php.php');

// Scan Buyers Matching...
if( isset($_POST['action']) && ($_POST['action'] == "Scan_buyers_mat") ) {
    
    // Input fields......
    
    if(isset($_POST['entered_address'])){
        $entered_address = trim($_POST['entered_address']);
    }
    
    if(isset($_POST['city'])){
        $city = trim($_POST['city']);
    }else{
        $city = '';
    }
    
    if(isset($_POST['state'])){
        $state = trim($_POST['state']);
    }else{
        $state = ''; 
    }
    
    if(isset($_POST['country'])){
        $country = trim($_POST['country']);
    }
    
    // if(isset($_POST['city_r'])){
    //     $city_r = $_POST['city_r'];
    // }else{
    //     $city_r = $city." ".$state;
    // }
    if($city != "" && $state != ""){
        $city_r = $city." ".$state;
        $city_k = 1;
    }else{
        $city_r = '';
    }
    
    
    if(isset($_POST['postal_code'])){
        $postal_code = trim($_POST['postal_code']);
    }
    
    if(isset($_POST['bedroom'])){
        $bedroom = $_POST['bedroom'];
        $bed_k = 1;
    }
    
    if(isset($_POST['bathroom'])){
        $bathroom = $_POST['bathroom'];
        $bed_k = 1;
    }
    
    if(isset($_POST['prop_type'])){
        $prop_type = $_POST['prop_type'];
        $pt_k = 1;
    }
    
    if(isset($_POST['deal_type'])){
        $deal_type = $_POST['deal_type'];
        $dt_k = 1;
    }
    
    if(isset($_POST['price'])){
        $price = $_POST['price'];
        $p_k = 1;
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
    
    
    // Checkboxes....
    
    
    

     $status = 1;
    
    if($user_id === $wo['user']['user_id'] ){
        
        $dataToSave = array(
            'entered_address' => $entered_address,
            'city' => $city,
            'state' => $state,
            'country' => $country,
            'city_r' => $city_r,
            'postal_code' => $postal_code,
            'bedroom' => $bedroom,
            'bathroom' => $bathroom,
            'prop_type' => $prop_type,
            'deal_type' => $deal_type,
            'price' => $price,
            'user_id' => $user_id,
        );
    
            
           $preact = 0;
        
        // SEARCH WITHOUT MUST ACTIONS
        
            // BEDROOMS
            if($bedroom > 0){
                
                if( isset($_POST['bed_matched'])){
                    
                    if($preact > 0 && $mus > 0){
                        $sqlBedM = " AND (`beds` LIKE '%".$bedroom."%') "; 
                    }else{
                        $sqlBedM = " (`beds` LIKE '%".$bedroom."%') "; 
                    }
                    
                    $musBed = 1;
                    $mus = 1;
                    
                }else{
                    
                    if($preact > 0){
                        $sqlBed = " OR (`beds` LIKE '%".$bedroom."%') "; 
                    }else{
                        $sqlBed = " (`beds` LIKE '%".$bedroom."%') "; 
                    }
                    
                }
                
                
                
                $preact = 1;
                
                $andUser = "AND `contactinsertedby` = $user_id AND type = 2";
                
                
            }
            
            // BATHROOMS
            if($bathroom > 0){
                
                if( isset($_POST['bath_matched'])){
                    
                    if($preact > 0 && $mus > 0){
                        $sqlBathM = " AND (`bath` LIKE '%".$bathroom."%') "; 
                    }else{
                        $sqlBathM = " (`bath` LIKE '%".$bathroom."%') ";
                    }
                    
                    $musBath = 1;
                    $mus = 1;
                }else{
                    
                    if($preact > 0){
                        $sqlBath = " OR (`bath` LIKE '%".$bathroom."%') "; 
                    }else{
                        $sqlBath = " (`bath` LIKE '%".$bathroom."%') ";
                    }
                    
                }
                
                $preact = 1;
                
                $andUser = "AND `contactinsertedby` = $user_id AND type = 2";
            }
            
            // CITY
            if(!empty($city_r)){
                
                if(isset($_POST['city_matched'])){
                    
                    if($preact > 0 && $mus > 0){
                        $sqlCityM = " AND `city` LIKE '%".$city_r."%' ";
                    }else{
                        $sqlCityM = " `city` LIKE '%".$city_r."%' ";
                    }
                    
                    $musCity = 1;
                    $mus = 1;
                }else{
                    
                    if($preact > 0){
                        $sqlCity = " OR `city` LIKE '%".$city_r."%' ";
                    }else{
                        $sqlCity = " `city` LIKE '%".$city_r."%' ";
                    }
                
                }
                
                
                $preact = 1;
                
                $andUser = "AND `contactinsertedby` = $user_id AND type = 2";
                
            }
            
            // PROPERTY TYPE
            if(!empty($prop_type)){
                
                if(isset($_POST['propType_matched'])){
                    
                    if($preact > 0 && $mus > 0){
                        $sqlPTypeM = " AND `property_type` LIKE '%".$prop_type."%' ";
                    }else{
                        $sqlPTypeM = " `property_type` LIKE '%".$prop_type."%' ";
                    }
                    
                    $musPropType = 1;
                    $mus = 1;
                    
                }else{
                    
                    if($preact > 0){
                        $sqlPType = " OR `property_type` LIKE '%".$prop_type."%' ";
                    }else{
                        $sqlPType = " `property_type` LIKE '%".$prop_type."%' ";
                    }
                    
                }
                
                
                $preact = 1;
                
                $andUser = "AND `contactinsertedby` = $user_id AND type = 2";
                
            }
            
            // DEAL TYPE
            if(!empty($deal_type)){
                
                if(isset($_POST['dealType_matched'])){
                    
                    if($preact > 0 && $mus > 0){
                        $sqlDTypeM = " AND `buying_strategy` LIKE '%".$deal_type."%' ";
                    }else{
                        $sqlDTypeM = " `buying_strategy` LIKE '%".$deal_type."%' ";
                    }
                    
                    $musDealType = 1;
                    $mus = 1;
                }else{
                    
                   if($preact > 0){
                        $sqlDType = " OR `buying_strategy` LIKE '%".$deal_type."%' ";
                    }else{
                        $sqlDType = " `buying_strategy` LIKE '%".$deal_type."%' ";
                    } 
                }
                
                
                $preact = 1;
                
                $andUser = "AND `contactinsertedby` = $user_id AND type = 2";
                
            }
            
            // PRICE
            if(!empty($price)){
                
                if(isset($_POST['price_matched'])){
                    
                    if($preact > 0 && $mus > 0){
                        $sqlPriceM = " AND (min_price <= $price AND max_price >= $price)";
                    }else{
                        $sqlPriceM = " (min_price <= $price OR max_price >= $price)";
                    }
                    
                    $musPrice = 1;
                    $mus = 1;
                    
                }else{
                    
                    if($preact > 0){
                        $sqlPrice = " OR (min_price <= $price AND max_price >= $price)";
                    }else{
                        $sqlPrice = " (min_price <= $price OR max_price >= $price)";
                    }
                    
                }
                
                
                $preact = 1;
                
                $andUser = "AND `contactinsertedby` = $user_id AND type = 2";
                
            }
    
            
            
            $opb = "(";
            $clb = ")";
                
            if($preact == 0){
                
                $andUser = "`contactinsertedby` = $user_id AND type = 2";
                $opb = "";
                $clb = "";
                
            }
            
            
            if( $bedroom == 0 && $bathroom == 0 && empty($city_r) && empty($prop_type) && empty($deal_type) && empty($price) ){
                
                $sql = "";
                
            }else{
                
                if($musBed > 0 || $musBath > 0 || $musCity > 0 || $musPropType > 0 || $musDealType > 0 || $musPrice > 0){
                 
                    $sql = "SELECT * FROM `contact` WHERE ".$opb." ".$sqlBedM." ".$sqlBathM." ".$sqlCityM." ".$sqlPTypeM." ".$sqlDTypeM." ".$sqlPriceM." ".$clb." ".$andUser;
                     
                }else{
                     
                    $sql = "SELECT * FROM `contact` WHERE ".$opb." ".$sqlBed." ".$sqlBath." ".$sqlCity." ".$sqlPType." ".$sqlDType." ".$sqlPrice." ".$clb." ".$andUser;
                     
                }
                
                
            }

   	    $result2 = mysqli_query($sqlConnect,$sql);
   	    $srows_count2 =  mysqli_num_rows($result2);
        $twilio_number = getTableData('lcn_table', ['user_id' => $user_id], 1);
        
   	    if($srows_count2 > 0){
   	        $request = '<div class="table-responsive">';
   	        $request .= '<table class="table-striped table align-middle" id="buyers_mat_table">';
   	        $request .=     '<thead>';
   	        $request .=         '<tr bgcolor="#fff">';
   	        $request .=             '<th><input type="checkbox" id="selectAll" /></th>';
            $request .=             '<th scope="col" class=" idColumn">Buyer(s) Name</th>';
            $request .=			    '<th scope="col" class=" idColumn">Email </th>';
            $request .=             '<th scope="col" class="firstnameColumn">Phone</th>';
            $request .=             '<th scope="col" class="lastnameColumn">City</th>';
            $request .=             '<th scope="col" class="phoneColumn">Property Type</th>';
            $request .=             '<th scope="col" class="emailColumn">Deal Type</th>';
            $request .=             '<th scope="col" class=" tagsColumn">Beds</th>';
            $request .=             '<th scope="col" class=" cityColumn">Baths</th>';
            $request .=             '<th scope="col" class=" stateColumn">List/Sale Price</th>';
            $request .=         '</tr>';
   	        $request .=     '<thead>';
   	        $count = 0;
   	        
   	        while($row2 = mysqli_fetch_array($result2)) {
   	            
   	            // BED MATCHED ICON
   	            $bedquery  = "SELECT * From `contact` WHERE `id` =" .$row2['id']. " AND `beds` LIKE '%".$bedroom."%' ";
                $inibedqu = mysqli_query($sqlConnect,$bedquery);
                $srows_bedcount =  mysqli_num_rows($inibedqu);
                if($srows_bedcount > 0){
                    $bedIcon = "✔";
                }else{
                    $bedIcon = "❌";
                }
                
                // BATH MATCHED ICON
                $bathquery  = "SELECT * From `contact` WHERE `id` =" .$row2['id']. " AND `bath` LIKE '%".$bathroom."%' ";
                $inibathqu = mysqli_query($sqlConnect,$bathquery);
                $srows_bathcount =  mysqli_num_rows($inibathqu);
                if($srows_bathcount > 0){
                    $bathIcon = "✔";
                }else{
                    $bathIcon = "❌";
                }
                
                // PRICE MATCHED ICON
                $pricequery  = "SELECT * From `contact` WHERE `id` =" .$row2['id']. " AND (min_price <= $price OR max_price >= $price) ";
                $inipricequ = mysqli_query($sqlConnect,$pricequery);
                $srows_pricecount =  mysqli_num_rows($inipricequ);
                if($srows_pricecount > 0){
                    $priceIcon = "✔";
                }else{
                    $priceIcon = "❌";
                }
                
                // CITY MATCHED ICON
                if( !empty($city_r) ){
                    $cityquery  = "SELECT * From `contact` WHERE `id` =" .$row2['id']. " AND `city` LIKE '%".$city_r."%'";
                    $inicityqu = mysqli_query($sqlConnect,$cityquery);
                    $srows_citycount =  mysqli_num_rows($inicityqu);
                    if($srows_citycount > 0){
                        $cityIcon = "✔";
                    }else{
                        $cityIcon = "❌";
                    }
                }else{
                    $cityIcon = "❌";
                }
                
                // PROPERTY TYPE MATCHED ICON $dealTypeIcon
                if( !empty($prop_type) ){
                    $propTypequery  = "SELECT * From `contact` WHERE `id` =" .$row2['id']. " AND `property_type` LIKE '%".$prop_type."%' ";
                    $inipropTypequ = mysqli_query($sqlConnect,$propTypequery);
                    $srows_propTypecount =  mysqli_num_rows($inipropTypequ);
                    if($srows_propTypecount > 0){
                        $propTypeIcon = "✔";
                    }else{
                        $propTypeIcon = "❌";
                    }
                }else{
                    $propTypeIcon = "❌";
                }
                
                
                // PROPERTY TYPE MATCHED ICON 
                if( !empty($deal_type) ){
                    $dealTypequery  = "SELECT * From `contact` WHERE `id` =" .$row2['id']. " AND `buying_strategy` LIKE '%".$deal_type."%'";
                    $inidealTypequ = mysqli_query($sqlConnect,$dealTypequery);
                    $srows_dealTypecount =  mysqli_num_rows($inidealTypequ);
                    if($srows_dealTypecount > 0){
                        $dealTypeIcon = "✔";
                    }else{
                        $dealTypeIcon = "❌";
                    }
                }else{
                    $dealTypeIcon = "❌";
                }
   	            
   	            $request .= '<tbody id="contact_result_all">';
                $request .=     '<tr class="contact-" '.($count % 2 == 0 ? "bgcolor=#fff": "").'>';
                    
                $request .=         '<td scope="row" class="align-middle"><input type="checkbox" class="contact-checkbox cts_checkbox" data-cts-id="'. $row2['id'] .'" name="contact-checkbox[]" value="'. $row2['id'] .'" /></td>';
                $request .=         '<td scope="row" class="align-middle">'.$row2['firstname'].'</td>';
                                    

                $request .=         '<td class="align-middle firstnameColumn">'.$row2['email'].'</td>';
                $request .=         '<td class="align-middle idColumn"><a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#ClickToCall" ><button onclick="Click_To_Call( ' . $twilio_number['number'] . ',' . $row2['mobile'] . ')" class="btn tblbtn" ><i class="fa fa-phone-alt"></i> ' . $row2['mobile'] . '</button></a></td>';
                
                $request .=         '<td class="align-middle lastnameColumn">'.$cityIcon.'</td>';
                $request .=         '<td class="align-middle phoneColumn">'.$propTypeIcon.'</td>';
                $request .=         '<td class="align-middle emailColumn">'.$dealTypeIcon.'</td>';
                $request .=         '<td class="align-middle tagsColumn listTags" >'.$bedIcon.'</td>';
                $request .=         '<td class=" cityColumn">'.$bathIcon.'</td>';
    			$request .=         '<td class=" stateColumn">'.$priceIcon.'</td>';
                $request .=     '</tr>';
                            
                $request .= '</tbody>';
   	            
   	            
   	            $count++;
   	            
   	        }
   	        
   	      $request .= '</table>';
   	      $request .= '</div>';

            $status = 200;
            // $request = $sql;
   	    }else{
   	        // $request = $sql;
   	        $request = '<br><img class="imgcE" src="upload/not_found.png" >';
            $status = 400;
   	    }




    }else{
        
        $request = '<tr><td colspan="7"><h5 class="mx-auto" style=" text-align:center;">Authentication Failed Please login</h5><td></tr>';
        $status = 400;
    }


    header("Content-type: application/json");
    echo json_encode([
        'status' => $status,
        'message' => $request
    ]);
    die();

//    echo $request;
//	 exit;
    
    
    
}

// Send SMS in BULK
if( isset($_POST['action']) && ($_POST['action'] == "SendSMSBulk") ) {
    
    if($_POST['from_phone']){
	    $from_phone = $_POST['from_phone'];
	}
	
	if($_POST['num']){
	    $num = $_POST['num'];
	}
	
	if($_POST['message']){
	    $message = $_POST['message'];
	}
    
    if(isset($_POST['contacts_id'])){
         $contacts_id = trim($_POST['contacts_id']);
    }
    
    if($num > 0 || $num != ""){
        
        $amot = $num * 0.03;
        $wBalance = checkWalletBalance($user_id);
        
        if($wBalance >= $amot){
            
            $Contsarray = explode(',', $contacts_id);
    
            // select lead details from database
            $leadSmsquery = "SELECT mobile FROM contact WHERE  mobile IS NOT NULL" ;
            if(count($Contsarray)) {
                $leadSmsquery .= " AND `id` IN (" . implode(',', $Contsarray) . ") ";
            }
            
            $psql = mysqli_query($sqlConnect,$leadSmsquery);
            while($resA = mysqli_fetch_assoc($psql)){
                $to_phones[] = $resA['mobile'];
            }
            
            if(count($to_phones)) {
        
                foreach($to_phones as $to_phone) {
                    if($to_phone != $from_phone){
                
                        $sendSMSMsg = send_bulk_sms_broadcast($to_phone,$from_phone,$message);
                        if($sendSMSMsg){
                            $sent ++;
                            
                            $from_phone = stringCounterReduce(array(
                                'lenght' => 10,
                                'string' => $from_phone
                            ));
                            
                            // Data to save
                            $data = [
                                'from_number' => $to_phone,
                                'sms_text' => $message,
                                'to_number' => $from_phone,
                                'user_id' => $user_id,
                                'status' => "seen",
                                'direction' =>'outbound',
                                'm_time' => time(),
                                'receive_date' => date("m d Y h:i:s A")
                            ];
                            
                            $messages = createSMSChat($data);
                            
                            $data = array(
                                'status' => 200,
                                'message' => "Successfully Sent"
                            ); 
                            
                        } else {
                           $data = array(
                                'status' => 400,
                                'message' => "Error SMS Could Not send to "
                            );  
                            break;
                        }
                    
                    }else{
        
                        $data = array(
                            'status' => 400,
                            'message' => "Sorry SMS Could Not send. Senders Phone Number can't be the same as reciever."
                        );
                        break;
                    }
                }
                
            }else{
                $data = array(
                    'status' => 400,
                    'message' => "Sorry the system could not process your request now try again later."
                );
                
            }
    
            
        }else{
            
            $data = array(
                'status' => 401,
                'message' => "Sorry SMS Could Not send due to low funds in your wallet, please replenish your wallet."
            );
            
            
        }
        
        
        
    }else{
        
        $data = array(
            'status' => 400,
            'message' => "Error Input Invalid....."
        );
        
        
    }
    
    
    
    
    
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
    
}

// Send EMAIL in Bulk
if( isset($_POST['action']) && ($_POST['action'] == "SendEMAILBulk") ) {
    
    if($_POST['from_email']){
	    $from_email = $_POST['from_email'];
	}
	
	if($_POST['num']){
	    $num = $_POST['num'];
	}
	
	if($_POST['message']){
	    $message = $_POST['message'];
	}
	
	if($_POST['subject']){
	    $subject = $_POST['subject'];
	}
    
    if(isset($_POST['contacts_id'])){
         $contacts_id = trim($_POST['contacts_id']);
    }
    
    
    
    if($num > 0 || $num != ""){
        
        $amot = $num * 0.006;
        $wBalance = checkWalletBalance($user_id);
        
        if($wBalance >= $amot){
            
            $Contsarray = explode(',', $contacts_id);
    
            // select lead details from database
            $leadEmsquery = "SELECT email FROM contact WHERE email IS NOT NULL" ;
            if(count($Contsarray)) {
                $leadEmsquery .= " AND `id` IN (" . implode(',', $Contsarray) . ") ";
            }
            
            $psql = mysqli_query($sqlConnect,$leadEmsquery);
            while($resA = mysqli_fetch_assoc($psql)){
                $to_emails[] = $resA['email'];
            }
            
            if(count($to_emails)) {
            
                foreach($to_emails as $to_email) {
                    
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
                            
                            if($response->statusCode() == 202) {
                                $sent ++;
                                
                                $data = array(
                                    'status' => 200,
                                    'message' => "Successfully Sent"
                                );
                                
                            }else{  
                                
                                $errorsObj = json_decode($response->body());
                                $error = $errorsObj->errors[0]->message;
    
                                $data = array(
                                    'status' => 400,
                                    'message' => $error
                                );
                                break;
                            }
                            
                        } catch (Exception $e) {
                            $data = array(
                                'status' => 400,
                                'message' => "Error" .$response->statusCode(). "Mail Could Not send"
                            );
                            break;
                        }
                    
                    }else{
                        $data = array(
                            'status' => 400,
                            'message' => "Sorry Mail Could Not send. Senders Email cant be the same as reciever."
                        );
                        break;
                    }
                }
                
            }else{
                
                $data = array(
                    'status' => 400,
                    'message' => "please select at least one contact."
                );
                
            }
            
            
        }else{
            
             $data = array(
                'status' => 401,
                'message' => "Sorry EMAIL Could Not send due to low funds in your wallet, please replenish your wallet."
            );
            
            
        }
    
    }else{
        
        $data = array(
            'status' => 400,
            'message' => "Error Input Invalid....."
        );
        
    }
    
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
}


if( isset($_POST['action']) && ($_POST['action'] == "SendSingleSMS") ) {
    
    if($_POST['from_phone']){
	    $from_phone = $_POST['from_phone'];
	}
	
	if($_POST['to_phone']){
	    $to_phone = $_POST['to_phone'];
	}
	
	if($_POST['message']){
	    $message = $_POST['message'];
	}
	
	if( isset($to_phone) && !empty($to_phone) ){
	    
	    $amot = 0.03;
        $wBalance = checkWalletBalance($user_id);
	    
	    if($wBalance >= $amot){
	    
            $sendSMSMsg = send_bulk_sms_broadcast($to_phone,$from_phone,$message);
            if($sendSMSMsg){
                
                $from_phone = stringCounterReduce(array(
                    'lenght' => 10,
                    'string' => $from_phone
                ));
                
                $to_phone = stringCounterReduce(array(
                    'lenght' => 10,
                    'string' => $to_phone
                ));
                
                // Data to save
                $data = [
                    'from_number' => $to_phone,
                    'sms_text' => $message,
                    'to_number' => $from_phone,
                    'user_id' => $user_id,
                    'status' => "seen",
                    'direction' =>'outbound',
                    'm_time' => time(),
                    'receive_date' => date("m d Y h:i:s A")
                ];
                
                $messages = createSMSChat($data);
                
                $data = array(
                    'status' => 200,
                    'message' => "Successfully Sent"
                ); 
                
            } else {
                
               $data = array(
                    'status' => 400,
                    'message' => "Error SMS Could Not send to"
                );  
            }
            
	    
	    }else{
	        
	        $data = array(
                'status' => 401,
                'message' => "Sorry SMS Could Not send due to low funds in your wallet, please replenish your wallet."
            );
	        
	    }
	    
	    
	    
	}else{
	    
	    $data = array(
            'status' => 400,
            'message' => "Error Reciever not selected....."
        );
	    
	}
	
    
    
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
}