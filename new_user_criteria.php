<?php
global $wo, $sqlConnect;
$root=__DIR__;
require_once($root.'/config.php');
require_once('assets/init.php');


$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);

$action = filter('action');

  if(isset($_POST['action']) && $_POST['action'] == 'Insert_cre'){
    // get all passed form values
        $buyer_name = $_POST['fullname'];
	    $buyer_email = $_POST['email'];
	    $buyer_phone_number = $_POST['phonenumber'];
        $amount1 = $_POST['amount1'];
	    $amount2 = $_POST['amount2'];
	    $beds = $_POST['beds'];
        $baths = $_POST['baths'];
        $purchase_type = $_POST['purchase_type'];
	    $prop_type = $_POST['prop_type'];
	    $strategy = $_POST['strategy'];
	    $referrer = $_POST['referrer'];
	    $areasofinterest = $_POST['city'];
	    $criteria_one = 1;
	    $date = date('Y-m-d H:i:s');
	    $areasofinterest = $_POST['city'];

	    
	    $user_id = 0;
	    $thePostIdArray = explode(', ', $areasofinterest);

	    $cityjson = json_encode($thePostIdArray);
	    
	   // $buyer_name = Wo_UserNameFromId($user_id);
    //     $buyer_email = Wo_UserEmailFromId($user_id);
    //     $buyer_phone_number = Wo_UserPhoneNumberFromId($user_id);
        
        // $INCQ = mysqli_query($sqlConnect, "INSERT INTO `Wo_MyCriteriainfo` (`user_id`, `property_type`, `city`, `buying_strategy`, `time`,`min_price`, `max_price`)VALUES ({$user_id},'{$prop_type}','{$cityjson}','{$strategy}','{$date}','{$amount1}','{$amount2}')");
        
        
        if(check_if_buyer_email_alreadyexist($buyer_email,$referrer) < 1){
            
            
            // $INUCQ = mysqli_query($sqlConnect, "INSERT INTO `Wo_Buyerinfo` (`user_id`, `property_type`, `reg_buyer_id`, `city`, `buying_strategy`, `time`,`min_price`, `max_price`, `beds`, `bath`, `how_will_you_purchasing_home`, `buyer_name`,`buyer_email`,`buyer_phone_number`)VALUES ({$referrer},'{$prop_type}',{$user_id},'{$cityjson}','{$strategy}','{$date}','{$amount1}','{$amount2}', '{$beds}', '{$baths}', '{$purchase_type}', '{$buyer_name}','{$buyer_email}','{$buyer_phone_number}')");
            $INUCQ = mysqli_query($sqlConnect, "INSERT INTO `contact` (`contactinsertedby`, `property_type`, `reg_buyer_id`, `city`, `buying_strategy`, `time`,`min_price`, `max_price`, `beds`, `bath`, `how_will_you_purchasing_home`, `firstname`,`email`,`mobile`,`type`)VALUES ({$referrer},'{$prop_type}',{$user_id},'{$cityjson}','{$strategy}','{$date}','{$amount1}','{$amount2}', '{$beds}', '{$baths}', '{$purchase_type}', '{$buyer_name}','{$buyer_email}','{$buyer_phone_number}',2)");
            
            if($INUCQ){

                // Sending SMS and System notification
                $contact_id = $sqlConnect->insert_id;
                sendSmsAndNotificationToBuyer($contact_id);
                updatedBuyerTags($contact_id);
                addBuyerToPipeline($contact_id);

                $data = array(
                    'status' => 200,
                    'message' => "Successfully Joined ",
                    'url' => $wo['config']['site_url'],
                    'id' => $contact_id
                );
            }else{
                $data = array(
                    'status' => 400,
                    'message' => "Sorry the system could not process your request for now, Please try again. ",
                );
            }
            
        }else{
            $data = array(
                'status' => 400,
                'message' => "Sorry You are Already on",
            );
        }
        
        
        
        // $Update_data = array(
        //     'criteria_one' => $criteria_one
        // );
        
        
        // if (Wo_UpdateUserData($user_id, $Update_data)) {
        //     $data = array(
        //         'status' => 200
        //     );
        // }
        
        
        
    	header("Content-type: application/json");
        echo json_encode($data);
	    die(); 
  }
  
  if(isset($_POST['action']) && $_POST['action'] == 'Update_cre'){
      
    $beds = $_POST['beds'];
    $baths = $_POST['baths'];
    $purchase_type = $_POST['purchase_type'];
    $referrer = $_POST['referrer'];
    $user_id = $_POST['user_id'];
    $criteria_two = 1;
    $date = date('Y-m-d H:i:s');
    
    $username = $wo['user']['username'];
    
    // $UNCQ = mysqli_query($sqlConnect, "UPDATE `Wo_Buyerinfo` SET `beds`='$beds',`bath`='$baths',`how_will_you_purchasing_home`='$purchase_type',`time`='$date' WHERE `user_id` = $referrer AND `buyer_name` = '$username' AND `reg_buyer_id` = $user_id ");
    $UNCQ = mysqli_query($sqlConnect, "UPDATE `contact` SET `beds`='$beds',`bath`='$baths',`how_will_you_purchasing_home`='$purchase_type',`time`='$date' WHERE `contactinsertedby` = $referrer AND `firstname` = '$username' AND `reg_buyer_id` = $user_id ");
    $UNUCQ = mysqli_query($sqlConnect, "UPDATE `Wo_MyCriteriainfo` SET `beds`='$beds',`bath`='$baths',`how_will_you_purchasing_home`='$purchase_type',`time`='$date' WHERE `user_id` = $user_id ");
    
    $Update_data = array(
        'criteria_two' => $criteria_two
    );
    
    if (Wo_UpdateUserData($user_id, $Update_data)) {
        $data = array(
            'status' => 200
        );
    }
    
    
    header("Content-type: application/json");
    echo json_encode($data);
    die();   
      
  }


  if($action == 'Agreement_cre'){
    showPhpErrors();
    $status = 400;
    $signature = filter('signature');
    $buyer_id = filter('buyer_id');

    if(!$buyer_id){
        $message = "Buyer does not exists";
    }else if(!$signature) {
        $message = "Please draw a valid signature";
    }else{
        $where = [
            'id' => $buyer_id
        ];
    
        $contact = getTableData('contact', $where, 1);

        if(!$contact) {
            $message = "Buyer does not exists";
        }else{
            $query = updateRow('contact', [
                'terms_agreed' => 1
            ], $where);
    

            if($sqlConnect->query($query)){
        
                $filename = "Agreement-Signature";
                $directory = "upload/files/contact/";
                    
                if(file_exists($directory)){
        
                    $image_parts = explode(";base64,", $signature);
                    $image_type_aux = explode("image/", $image_parts[0]);
                    $image_type = $image_type_aux[1];
                    $image_base64 = base64_decode($image_parts[1]);
                    $path = "{$filename}-{$buyer_id}" . '.'.$image_type;
                    $newFilename = $path;
                    $file = $directory . $path ;
                    file_put_contents($file, $image_base64);

                    

        
                    $upload = [
                        'contact_id' => $buyer_id,
                        'filename' => $path,
                        'type' => 'buyer',
                    ];
        
                    if($sqlConnect->query(insertRow('wo_contact_files', $upload))){

                        makeAgreementPDFWithSignature($buyer_id);
                        $status = 200;
                        $message = "Successfully Joined";
                    }else{
                        $message = mysqli_error($sqlConnect);
                    }
                }
            }else{
                $message = mysqli_error($sqlConnect);
            }
        }
    }


    $data = [
        'status' => $status,
        'message' => $message
    ];

    header("Content-type: application/json");
    echo json_encode($data); die();  

  }

  function makeAgreementPDFWithSignature($contact_id){


    error_reporting (E_ALL ^ E_NOTICE);
    require_once 'vendor/autoload.php';
    global $wo, $sqlConnect; 
    $file = $wo['site_url'] . "/upload/files/contact/Agreement Signature-{$contact_id}.png";
    $css = file_get_contents("themes/wondertag/stylesheet/agreement.css");

    $arrContextOptions=array(
        "ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ),
    ); 
    $body = file_get_contents($wo['site_url'] . "/agreement.php?id=" . $contact_id, false, stream_context_create($arrContextOptions));

    $mpdf = new \Mpdf\Mpdf();
    
    $mpdf->WriteHTML($css, 1);
    $mpdf->WriteHTML($body, 2);

    $mpdf->SetDisplayMode('fullpage');
    $mpdf->list_indent_first_level = 0; 

    //call watermark content and image
    $mpdf->SetWatermarkText('propertysalers');
    $mpdf->showWatermarkText = true;
    $mpdf->watermarkTextAlpha = 0.1;

    //output in browser
    $filename = "Buyer-Aggrement-{$contact_id}.pdf";
    $path = "upload/files/contact/" . $filename;
    $mpdf->Output($path, 'F');
    $upload = [
        'contact_id' => $contact_id,
        'filename' => $filename,
        'type' => 'buyer',
    ];

    return $sqlConnect->query(insertRow('wo_contact_files', $upload));

  }
?>