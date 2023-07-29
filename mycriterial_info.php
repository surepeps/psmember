<?php
global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');
$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);
$user_id = $_POST['user_id'];
$role = $_POST['role'];

$query = mysqli_query($sqlConnect, "SELECT `twilio_number` FROM `Wo_Contact_Info` WHERE `user_id` = " . $user_id);

	
	if($role=='buyer'){
		$query = mysqli_query($sqlConnect,"SELECT * FROM `Wo_MyCriteriainfo` WHERE user_id= $user_id");
		
		
		$query_selectcode = "SELECT COUNT(`user_id`) as `count` FROM `Wo_MyCriteriainfo` WHERE `user_id` = '{$user_id}'";
        $sql_code       = mysqli_query($sqlConnect, $query_selectcode);
        $sql_fetch_selectcode = mysqli_fetch_assoc($sql_code);
        $myretunnumuser = $sql_fetch_selectcode['count'];

		$home_buyer = $_POST['home_buyer'];
		$send_all_properties = $_POST['send_all_properties'];
		$prop_type = $_POST['prop_type'];
		$city = $_POST['city'];
		$zip_code = $_POST['zip_code'];
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

		$buying_strategy = implode(",",$buying_strategy);
// 		unset($city[end($city)]);

// 		$zipcodejson = json_encode($zip_code);
        $thePostIdArray = explode(', ', $city);

		$cityjson = json_encode($thePostIdArray);

		 if($myretunnumuser > 0){

		 	$query_one =  "UPDATE `Wo_MyCriteriainfo` SET `send_all_properties`='$send_all_properties', `first_time_home_buyer`='$home_buyer',`property_type`='$prop_type',`city`='$cityjson',`zip_code`='$zipcodejson',`beds`='$prop_rooms',`bath`='$prop_bathroom',`how_will_you_purchasing_home`='$prop_purchase_type',`fund_available`='$fund_available',`can_you_provide_proof_of_fund`='$fund_proof',`proof_image`='$upload_file_name',`buying_strategy`='$buying_strategy',`time`='$date',`min_price`='$amount1',`max_price`='$amount2',`buyer_name`='$buyer_name' WHERE `user_id` = ".$user_id;
            $mymessage = "Criteria Updated Successfully";
		 }else{

		 	$query_one   = "INSERT INTO Wo_MyCriteriainfo (`user_id`,`send_all_properties`, `first_time_home_buyer`, `property_type`, `city`, `zip_code`, `beds`, `how_will_you_purchasing_home`, `fund_available`, `can_you_provide_proof_of_fund`, `buying_strategy`, `time`,`proof_image`,`min_price`, `max_price`,`bath`,`buyer_name`)VALUES ({$user_id},'{$send_all_properties}','{$home_buyer}','{$prop_type}','{$cityjson}','{$zipcodejson}','{$prop_rooms}','{$prop_purchase_type}','{$fund_available}','{$fund_proof}','{$buying_strategy}','{$date}','{$upload_file_name}','{$amount1}','{$amount2}','{$prop_bathroom}','{$buyer_name}')";
			$mymessage = "Criteria Added Successfully";

		}
// 		echo $query_one;
		$sql_query = mysqli_query($sqlConnect, $query_one);
		
		if($sql_query){
		    $data = array(
                    'status' => 200,
                    'message' => $mymessage
            );
		}else{
		    $data = array(
                    'status' => 400,
                    'message' => 'Error While Updating Criteria details'
            );
		}
		
			$beds = explode(":",$prop_rooms);
			$baths = explode(":",$prop_bathroom);
			$propertytypes = explode(":",$prop_type);

			/*print_r($propertytypes);*/

			$newprotypearr = array();
			foreach ($propertytypes as $key => $value) {
				if($value=="ﾃつｽ Duplex")
					$value = "1/2 Duplex";	
				$newprotypearr[] = $value;
			}


			/*print_r($newprotypearr);*/

			$min_amount = $amount1;
			$max_amount = $amount2;

			$query = "SELECT A.user_id,A.id as proid FROM `Wo_Filter` B left join `Wo_Listing` A on B.property_id =A.id WHERE A.status = 1";

			if($min_amount!="" && $max_amount!=""){
				$query.=" AND (B.price_range BETWEEN $min_amount AND $max_amount) ";
			}

			if(!empty($cityval)) {
				$query.=" AND ( ";
				foreach ($cityval as $key => $city) {
					if($key==0)
						$query.="  B.address LIKE '%$city%' ";
					else
						$query.=" OR B.address LIKE '%$city%' ";
				}

				if(!empty($zipcode)) {
					foreach ($zipcode as $key => $zip) {

							$query.=" OR B.address LIKE '%$zip%' ";
					}
					$query.=")";
				} else {
					$query.=")";	
				}
				
			}


			if(!empty($beds)){

				$bedstring = str_replace(",","','", implode(",", $beds));
				$query.=" AND B.bedroom IN ('$bedstring')";
			}

			if(!empty($baths)){
				$bathstring = str_replace(",","','", implode(",", $baths));
				$query.=" AND B.bathroom IN ('$bathstring')";
			} 

			if(!empty($newprotypearr)){
				$propertytypestring = str_replace(",","','", implode(",", $newprotypearr));
				$query.=" OR B.property_type IN ('$propertytypestring')";
			}


			$query = mysqli_query($sqlConnect,$query);

			$useremails = array();
			while($row = mysqli_fetch_array($query)) :
				$userid = $row['user_id'];
				$propid = $row['proid'];
				$query1 = "SELECT email FROM `Wo_Users` WHERE user_id=".$userid;
				$query1 = mysqli_query($sqlConnect,$query1);

				$row = mysqli_fetch_array($query1);

				$useremails [$userid][] = $propid;
			endwhile;


			/**************** Notify user ******************/

			$notifychekarr = array();
			$emailstosend = array();

			$from = 'Strastic<noreply@strastic.com>';

			$headers = 'From: '.strip_tags( $from ). "¥r¥n" .
					   'Reply-To: ' . strip_tags( $from ) . "¥r¥n" .
			    	   'X-Mailer: PHP/' . phpversion();
			$headers .= "CC: info@strastic.com¥r¥n";
			$headers .= "MIME-Version: 1.0¥r¥n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1¥r¥n";
			$subject = 'Message From Appstrastic';

			$typetext = "Property Match ";


			/******************* Send notification to Buyer *************************/

			if (!empty($wo['user']['notification_settings'])) {
				$notifychekarr = unserialize(html_entity_decode($wo['user']['notification_settings']));
				$notifycheck = $notifychekarr['e_matchcriteria'];
				$notifycheckemail = $notifychekarr['e_email_matchcriteria'];
				$notifychecksms = $notifychekarr['e_sms_matchcriteria'];

			}

			

				foreach ($useremails as $key => $proid) {

					foreach ($proid as $k => $v) {
						if(Wo_Property_Slug($v)!="") {
							$slug = Wo_Property_Slug($v);
							$propertyurl = $wo['config']['site_url']."/property/".$slug;
						}

						if($notifycheck==1) :
							$Notificationquery = mysqli_query($sqlConnect,"INSERT INTO `Wo_Notifications` (notifier_id,recipient_id,type,type2,text,url,time) VALUES(1,'$user_id','created_request','$type2','Your profile matches to one of property','".$propertyurl."','".time()."')");
						endif;
					}
					
				}
				
			
			$prourls = array();
			if($notifycheckemail==1) :
				
				foreach ($useremails as $key => $proid) { 
					
					foreach ($proid as $k => $v) {
						if(Wo_Property_Slug($v)!="") {
							$slug = Wo_Property_Slug($v);
							$propertyurl = $wo['config']['site_url']."/property/".$slug;
						}
						
						$emailcontent = "Your profile matches to one of property ";

			        	Wo_send_email_by_Send_Grid_custom($wo['user']['email'],$from,$subject,$key,$typetext,$emailcontent,$propertyurl);
					}
					

				}

			endif;

			if($notifychecksms==1) :
				foreach ($useremails as $key => $proid) { 
					
					foreach ($proid as $k => $v) {
						if(Wo_Property_Slug($v)!="") {
							$slug = Wo_Property_Slug($v);
							$propertyurl = $wo['config']['site_url']."/property/".$slug;
							$prourls[] = $propertyurl;
						}
						
					}
					

				}

				$propertyurls = implode(" ¥n ", $prourls);
				

		        $recieverphone = $wo['user']['phone_number'];
		        $message = "Your profile matches to following properties ¥n ".$propertyurls;
		        

	         	$send = Wo_SendSMSMessage_dashboard($recieverphone,'+14079880583',$message);

			endif;


			/****************** Send notificatoin to Seller *************************/


			foreach ($useremails as $sellerid => $proid) {
				$sellerdata = Wo_UserData($sellerid);
				$notifycheck = 0;
				$notifycheckemail = 0;

				$url = $wo['config']['site_url']."/".$wo['user']['username'];

				if (!empty($sellerdata['notification_settings'])) {
					$notifychekarr = unserialize(html_entity_decode($sellerdata['notification_settings']));
					$notifycheck = $notifychekarr['e_matchcriteria'];
					$notifycheckemail = $notifychekarr['e_email_matchcriteria'];
					$notifychecksms = $notifychekarr['e_sms_matchcriteria'];
					

				}

				if($notifycheck==1) :

					$Notificationquery = mysqli_query($sqlConnect,"INSERT INTO `Wo_Notifications` (notifier_id,recipient_id,type,type2,text,url,time) VALUES('$user_id','$sellerid','created_request','$type2','New buyer matches to one of your property','".$url."','".time()."')");
				endif;

				if($notifycheckemail==1) :
					
					$emailcontent = ucfirst($wo['user']['username'])." matches your property ".$url;

			        Wo_send_email_by_Send_Grid_custom($sellerdata['email'],$from,$subject,$key,$typetext,$emailcontent,$url);
				endif;

				if($notifychecksms==1) :
					
					$recieverphone = $sellerdata['phone_number'];
			        $message = $emailcontent;

			       
			        
		         	$send = Wo_SendSMSMessage_dashboard($recieverphone,'+14079880583',$message);
				endif;

				
			}
			header("Content-type: application/json");
            echo json_encode($data);
    	    die();

	}
die;
?>