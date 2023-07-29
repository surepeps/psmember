<?php
global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');
$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);
$user_id = $_POST['user_id'];
$role = $_POST['role'];


$query = mysqli_query($sqlConnect, "SELECT `twilio_number` FROM `Wo_Contact_Info` WHERE `user_id` = " . $user_id);


/*$twilio_number = "";
if (mysqli_num_rows($query) > 0) {
    $fetched_data = mysqli_fetch_assoc($query);
    $twilio_number = $fetched_data ['twilio_number'];
} else {

     $twilio_number = Wo_purchase_number($user_id);

     $query1 = mysqli_query($sqlConnect, "INSERT INTO Wo_Contact_Info VALUES('','".$user_id."','".$twilio_number['phone_number']."','".$twilio_number['account_sid']."') ");
}*/


	
	if($role=='buyer'){
		$query = mysqli_query($sqlConnect,"SELECT * FROM `contact` WHERE contactinsertedby= $user_id AND type = 2");
		
		
		$query_selectcode = "SELECT COUNT(`contactinsertedby`) as `count` FROM `contact` WHERE `contactinsertedby` = '{$user_id}' AND type = 2";
        $sql_code       = mysqli_query($sqlConnect, $query_selectcode);
        $sql_fetch_selectcode = mysqli_fetch_assoc($sql_code);
        $myretunnumuser = $sql_fetch_selectcode['count'];

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
        $market_available = $_POST['market_available'];
		$fund_available = $_POST['fund_available'];
		$buying_strategy = $_POST['buying_strategy'];
		$date = date('Y-m-d H:i:s');
		$upload_file_name = $_POST['upload_file_name'];
		$prop_bathroom = $_POST['prop_bathroom'];
		$buyer_name = $_POST['buyer_name'];
        $path = $_POST['path'];
		$buying_strategy = implode(",",$buying_strategy);

// 		unset($city[end($city)]);

// 		$zipcodejson = json_encode($zip_code);
        $thePostIdArray = explode(',', $city);

		$cityjson = json_encode($thePostIdArray);

        $is_editing = $_POST['is_editing'];
        
		 if($is_editing){
    
            $query_one =  "UPDATE `contact` SET `send_to_buyer` = '$send_to_buyer', `send_all_properties`='$send_all_properties', `first_time_home_buyer`='$home_buyer', `property_type`='$prop_type', `city`='$cityjson', `zip_code`='$zipcodejson', `beds`='$prop_rooms',`bath`='$prop_bathroom',`how_will_you_purchasing_home`='$prop_purchase_type',`fund_available`='$fund_available',`can_you_provide_proof_of_fund`='$fund_proof', `market_available` = '$market_available', `proof_image`='$upload_file_name',`buying_strategy`='$buying_strategy',`time`='$date',`min_price`='$amount1',`max_price`='$amount2',`firstname`='$buyer_name',`email`='$buyer_email',`mobile`='$buyer_phone_number' WHERE `id` = $is_editing AND type = 2";
		 	$mymessage = "Buyer Criteria Updated Successfully";
		 }else{

             // validate package with feature deduction number
             $pData = [
                 'path' => $path,
                 'user_id' => $user_id,
                 'userPackage' => $wo['user']['my_package'],
                 'number' => 1
             ];
             packageReducerValidator($pData); // will continue if it is true then terminate the code if false


             $query_one   = "INSERT INTO `contact` (`contactinsertedby`,`send_to_buyer`,`send_all_properties`, `first_time_home_buyer`, `property_type`, `city`, `zip_code`, `beds`, `how_will_you_purchasing_home`, `fund_available`, `can_you_provide_proof_of_fund`, `market_available`, `buying_strategy`, `time`,`proof_image`,`min_price`, `max_price`,`bath`,`firstname`,`email`,`mobile`,`type`,`Status`)VALUES ('{$user_id}','{$send_to_buyer}','{$send_all_properties}','{$home_buyer}','{$prop_type}','{$cityjson}','{$zipcodejson}','{$prop_rooms}','{$prop_purchase_type}','{$fund_available}','{$fund_proof}', '{$market_available}', '{$buying_strategy}','{$date}','{$upload_file_name}','{$amount1}','{$amount2}','{$prop_bathroom}','{$buyer_name}','{$buyer_email}','{$buyer_phone_number}',2,1)";
			$mymessage = "Buyer Criteria Added Successfully";

		}

		$message = 'Error While Updating Criteria details';

		$pinCode = filter('pin_code');
		if(!$pinCode || $is_editing) {
			$checkPinCode = 1;
		}else{
			$checkPinCode = checkValidPinCode($pinCode);
			if($checkPinCode != 1) {
				$message = $checkPinCode;
			}
		}
		
		
		$sql_query = mysqli_query($sqlConnect, $query_one);
		if(($checkPinCode == 1) && ($sql_query)){

			if($pinCode) {
				$contact_id = $sqlConnect->insert_id;
				$response = createPinCode($contact_id, $pinCode);
			}

			$contact_id = $is_editing;
			if(!$is_editing){
				$contact_id = $sqlConnect->insert_id;
			}

			updatedBuyerTags($contact_id);

			if(!$is_editing){
				updateBuyerCustomerNumber($contact_id);
			}

		    $data = array(
                'status' => 200,
                'message' => $mymessage
            );

		}else{
		    $data = array(
                'status' => 400,
                'message' => $message
            );
		}
		
			$beds = explode(":",$prop_rooms);
			$baths = explode(":",$prop_bathroom);
			$propertytypes = explode(":",$prop_type);


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

			$from = 'no-reply<noreply@'.$wo['config']['siteName'].'.com>';

			$headers = 'From: '.strip_tags( $from ). "¥r¥n" .
					   'Reply-To: ' . strip_tags( $from ) . "¥r¥n" .
			    	   'X-Mailer: PHP/' . phpversion();
			$headers .= "CC: info@".$wo['config']['siteName'].".com¥r¥n";
			$headers .= "MIME-Version: 1.0¥r¥n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1¥r¥n";
			$subject = 'Message From App '.$wo['config']['siteName'];

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

	

    if($role=='agent'){

			$query = mysqli_query($sqlConnect,"SELECT * FROM `Wo_Agent` WHERE user_id= $user_id");

			//echo "SELECT * FROM `Wo_Agent` WHERE user_id= $user_id";
			//echo " num rows = ".$query->num_rows;

			$agent_experience=$_POST['agent_experience'];
			$months_sales=$_POST['months_sales'];
			$home_solds=$_POST['home_solds'];
			$specialities=$_POST['specialities'];
			$languages=$_POST['languages'];
			$serviceArea=$_POST['serviceArea'];
			$date = date('Y-m-d H:i:s');
			$min_amount = $_POST['min_amount'];
			$max_amount = $_POST['max_amount'];

			$company_broker=$_POST['company_broker'];
			$license_number = $_POST['license_number'];
			$office_address = $_POST['office_address'];
			$profile_description = $_POST['profile_description'];
			$neighborhood = $_POST['agent_neighborhood'];

			

			$languages = implode(":",$languages);
			$specialities = implode(":",$specialities);

			/*print_r($languages);
			print_r($specialities);
*/




	        if($query->num_rows>0){


	        	$query_one = mysqli_query($sqlConnect, "UPDATE `Wo_Agent` SET `experiance`='$agent_experience',`sales_last_12_month`='$months_sales',`total_home_sold`='$home_solds',`specialities`='$specialities',`languages`='$languages',`service_area`='$serviceArea',`min_price`='$min_amount',`max_price`='$max_amount',`company_broker`='$company_broker',`licence_number`='$license_number',`office_address`='$office_address',`profile_description`='$profile_description',`neighborhood`='452' WHERE `user_id` = {$user_id}");


	        	//echo "UPDATE `Wo_Agent` SET `experiance`='$agent_experience',`sales_last_12_month`='$months_sales',`total_home_sold`='$home_solds',`specialities`='$specialities',`languages`='$languages',`service_area`='$serviceArea',`min_price`='$min_amount',`max_price`='$max_amount',`company_broker`='$company_broker',`licence_number`='$license_number',`office_address`='$office_address',`profile_description`='$profile_description',`neighborhood`='452' WHERE `user_id` = {$user_id}";


	        	if($query_one){
					die(" update succesfully ");
	        	}
	        	
	        	die(" not update ");

	        }else{ 
	        	$query   = "INSERT INTO `Wo_Agent`(`user_id`, `experiance`, `sales_last_12_month`, `total_home_sold`, `specialities`, `languages`, `service_area`, `min_price`, `max_price`,`company_broker`, `licence_number`, `office_address`, `profile_description`,`neighborhood`) VALUES ({$user_id},'{$agent_experience}','{$months_sales}','{$home_solds}','{$specialities}','{$languages}','{$serviceArea}',{$min_amount},{$max_amount},'{$company_broker}','{$license_number}','{$office_address}','{$profile_description}','{$neighborhood}')";

				//echo "INSERT INTO `Wo_Agent`(`user_id`, `experiance`, `sales_last_12_month`, `total_home_sold`, `specialities`, `languages`, `service_area`, `min_price`, `max_price`,`company_broker`, `licence_number`, `office_address`, `profile_description`,`neighborhood`) VALUES ({$user_id},'{$agent_experience}','{$months_sales}','{$home_solds}','{$specialities}','{$languages}','{$serviceArea}',{$min_amount},{$max_amount},'{$company_broker}','{$license_number}','{$office_address}','{$profile_description}','{$neighborhood}')";

				$sql_query = mysqli_query($sqlConnect, $query);
				die;
	        }
	}







function Notifyusertempalte($emailcontent) {

	global $wo;
	$wo['emailNotification']['notifier'] = $wo['user'];
	$wo['emailNotification']['type']     = 'sent_message';
	$wo['emailNotification']['url']      = '';
	$wo['emailNotification']['msg_text'] =$emailcontent;
	/* $send_message_data                   = array(
	'from_email' => $wo['config']['siteEmail'],
	'from_name' => $wo['config']['siteName'],
	'to_email' => $email,
	'to_name' => '',
	'subject' => 'New notification',
	'charSet' => 'utf-8',
	'message_body' => Wo_LoadPage($root.'/emails/notifiction-email'),
	'is_html' => true
	);*/

	$wo['emailNotification']['type_text'] = '';
	if ($wo['emailNotification']['type'] == "following") {
	    $wo['emailNotification']['type_text'] .= 'is following you';
	}
	if ($wo['emailNotification']['type'] == "visited_profile") {
	    $wo['emailNotification']['type_text'] .= 'visited your profile';
	}
	if ($wo['emailNotification']['type'] == 'comment_mention') {
	    $wo['emailNotification']['type_text'] .= 'mentioned you on a comment';
	}
	if ($wo['emailNotification']['type'] == 'post_mention') {
	    $wo['emailNotification']['type_text'] .= 'mentioned you on a post';
	}
	if ($wo['emailNotification']['type'] == 'liked_post') {
	    $wo['emailNotification']['type_text'] .= 'liked your post';
	}
	if ($wo['emailNotification']['type'] == 'wondered_post') {
	    $lang_type = ($wo['config']['second_post_button'] == 'wonder') ? 'wondered your post' : 'disliked your post';
	    $wo['emailNotification']['type_text'] .= $lang_type;
	}
	if ($wo['emailNotification']['type'] == 'share_post') {
	    $wo['emailNotification']['type_text'] .= 'shared your post';
	}
	if ($wo['emailNotification']['type'] == 'comment') {
	    $wo['emailNotification']['type_text'] .= 'commented on your post';
	}
	if ($wo['emailNotification']['type'] == 'liked_comment') {
	    $wo['emailNotification']['type_text'] .= 'liked your comment "' . $wo['emailNotification']['text'] . '"';
	}
	if ($wo['emailNotification']['type'] == 'wondered_comment') {
	    $lang_type = ($wo['config']['second_post_button'] == 'wonder') ? 'wondered your comment' : 'disliked your comment';
	    $wo['emailNotification']['type_text'] .= $lang_type . ' "' . $wo['emailNotification']['text'] . '"';
	}
	if ($wo['emailNotification']['type'] == 'profile_wall_post') {
	    $wo['emailNotification']['type_text'] .= 'posted on your timeline';
	}
	if ($wo['emailNotification']['type'] == 'accepted_request') {
	    $request_type = ($wo['config']['connectivitySystem'] == 1) ? 'friend' : 'follow';
	    $wo['emailNotification']['type_text'] .= "accepted your {$request_type} request.";
	}
	if ($wo['emailNotification']['type'] == 'liked_page') {
	    $page = Wo_PageData($wo['emailNotification']['page_id']);
	    $wo['emailNotification']['type_text'] = 'liked your page (' . $page['name'] . ')';
	}
	if ($wo['emailNotification']['type'] == 'joined_group') {
	    $group = Wo_GroupData($wo['emailNotification']['group_id']);
	    $wo['emailNotification']['type_text'] = 'joined your group (' . $group['name'] . ')';
	}

	if ($wo['emailNotification']['type'] == 'sent_message') {
	  $wo['emailNotification']['type_text'] = 'Sent you a new message';
	}


	$htmlcontent =  '<!doctype html>
		<html>
		<head><meta charset="shift_jis"></head>
		<body>
			<div style="width:100%!important;background:#f2f2f2;margin:0;padding:0" bgcolor="#f2f2f2">
				<div>
					<table width="100%" bgcolor="#f2f2f2" cellpadding="0" cellspacing="0" border="0" style="width:100%!important;line-height:100%!important;border-collapse:collapse;margin:0;padding:0">
						<tbody>
							<tr>
								<td style="border-collapse:collapse">
									<table width="542" cellpadding="0" cellspacing="0" border="0" align="center" style="display:block;border-collapse:collapse">
										<tbody style="display:table;width:100%">
											<tr>
												<td style="border-collapse:collapse">
													<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" style="border-collapse:collapse">
														<tbody>
															<tr>
																<td valign="middle" width="100%" align="center" style="border-collapse:collapse;padding:30px 0">
																	<div>
																		<a href="'.$wo['config']['site_url'].'" style="text-emphasis:none" target="_blank">
																			<img src="'.$wo['config']['site_url'].'/themes/wowonder/img/logo.png" alt="Upwork" width="200" border="0" style="display:block;outline:none;text-decoration:none;border:none">
																		</a>
																	</div>
																</td>
															</tr>
														</tbody>
													</table>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div>
					<table width="100%" bgcolor="#f2f2f2" cellpadding="0" cellspacing="0" border="0"  style="width:100%!important;line-height:100%!important;border-collapse:collapse;margin:0;padding:0">
						<tbody>
							<tr>
								<td style="border-collapse:collapse">
									<table bgcolor="#ffffff" width="542" align="center" cellspacing="0" cellpadding="0" border="0"  style="border-collapse:collapse">
										<tbody>
											<tr>
												<td>
													<table width="502" align="center" cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse">
														<tbody style="border-collapse:collapse">
															<tr>
																<td width="100%" height="40" style="border-collapse:collapse"></td>
															</tr>
															<tr>
																<td style="font-family:Helvetica,arial,sans-serif;font-size:14.5px;color:#666666;text-align:left;line-height:20px;border-collapse:collapse">
																	<table width="100%" align="center" cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse">
																		<tbody>
																			<tr>
																				<td><h1 style="line-height:1.1;text-align:center"><br> 
																				Congratulations !! A new buyer having match with your property criteria!</h1></td>
																			</tr>
																			<tr>
																				<td width="100%" height="40" style="border-collapse:collapse"></td>
																			</tr>
																			<tr>
																				<td style="font-family:Helvetica;font-size:24px;color:#494949;text-align:left;line-height:20px;font-weight:bold;border-collapse:collapse" align="left">
																					Hi,
																				</td>
																			</tr>
																			<tr>
																				<td width="100%" height="40" style="border-collapse:collapse"></td>
																			</tr>
																			<tr>
																				<td style="font-family:Helvetica,arial,sans-serif;font-size:14.5px;color:#666666;text-align:left;line-height:20px;border-collapse:collapse" align="left">
																						Great news!
																				</td>
																			</tr>
																			<tr>
																				<td width="100%" height="20" style="border-collapse:collapse"></td>
																			</tr>
																			<tr>
																				<td style="font-family:Helvetica,arial,sans-serif;font-size:14.5px;color:#666666;text-align:left;line-height:20px;border-collapse:collapse" align="left">';
																		if (!empty($wo['emailNotification']['post_data']['text'])) {
											                                $htmlcontent .= $wo[emailNotification]['post_data'][ 'text'];
											                              }

											                              else if(!empty($wo['emailNotification']['msg_text'])){
											                                $htmlcontent .="¥".$wo['emailNotification']['msg_text'] . "¥";
											                              }


														$htmlcontent .=	'
																</td>
															</tr>
															<tr>
																<td width="100%" height="40" style="border-collapse:collapse"></td>
															</tr>
		
																			<tr>
																				<td width="100%" height="40" style="border-collapse:collapse"></td>
																			</tr>
																			<tr>
																				<td>
																					<a style="background-image: linear-gradient(#193d70,#16345b);color: #ffffff;text-decoration: none;padding: 10px 20px;border-radius: 6px" href="'.$wo['config']['site_url'].'/seller-dashboard">View Match details</a>
																				</td>
																			</tr>
																			<tr>
																				<td width="100%" height="40" style="border-collapse:collapse"></td>
																			</tr>												
																		</tbody>
																	</table>
																</td>
															</tr>
															<tr>
																<td style="font-family:Helvetica,arial,sans-serif;font-size:14.5px;color:#666666;text-align:left;line-height:20px;border-collapse:collapse" align="left">
																	Good luck! We have finger crossed for you.
																</td>
															</tr>
															<tr>
																<td width="100%" height="20" style="border-collapse:collapse"></td>
															</tr>                             
															<tr>
																<td style="font-family:Helvetica,arial,sans-serif;font-size:14.5px;color:#666666;text-align:left;line-height:20px;border-collapse:collapse" align="left">
																	<br>Sincerely,<br>'.$wo['config']['siteName'].' Support
																</td>
															</tr>
															<tr>
																<td width="100%" height="40" style="border-collapse:collapse"></td>
															</tr>
														</tbody>
													</table>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div>
				<!--<table cellspacing="0" cellpadding="0" width="100%" border="0" align="center" bgcolor="#f2f2f2">
					<tbody>
						<tr>
							<td valign="middle" align="center" width="100%" style="padding-top:30px">
								<span style="font-family:Helvetica,Arial,Sans serif;font-weight:bold;line-height:18pt;font-size:13pt;color:#7d7d7d">
									Download our mobile app on <br>
				<a href="#" style="color:#37a000;text-decoration:none" target="_blank">iPhone</a> or
				<a href="#" style="color:#37a000;text-decoration:none" target="_blank">Android</a>
								</span>
							</td>
						</tr>
					</tbody>
				</table> -->
						<table cellspacing="0" cellpadding="0" width="100%" border="0" align="center" bgcolor="#f2f2f2">
							<tbody>
								<tr>
									<td valign="middle" align="center" width="100%" height="25" style="padding:30px 0">
										<table cellspacing="0" cellpadding="0" width="177" border="0">
											<tbody><tr>
												<td width="25">
													<a href="#" title="Twitter" target="_blank">
														<img src="'.$wo['config']['site_url'].'/themes/wowonder/icons/social/twitter.png" height="25" width="25" alt="Twitter" style="border:0;outline:none;display:block">
													</a>
												</td>
												<td width="13">&nbsp;</td>
												<td width="25">
													<a href="#" title="Facebook" target="_blank">
														<img src="'.$wo['config']['site_url'].'/themes/wowonder/icons/social/facebook.png" height="25" width="25" alt="Facebook" style="border:0;outline:none;display:block">
													</a>
												</td>
												<td width="13">&nbsp;</td>
												<td width="25">
													<a href="#" title="Google+" target="_blank">
														<img src="'.$wo['config']['site_url'].'/themes/wowonder/icons/social/gplus.png" height="25" width="25" alt="Google+" style="border:0;outline:none;display:block">
													</a>
												</td>
												<td width="13">&nbsp;</td>
												<td width="25">
													<a href="#" title="LinkedIn" target="_blank">
														<img src="'.$wo['config']['site_url'].'/themes/wowonder/icons/social/linkedin.png" height="25" width="25" alt="LinkedIn" style="border:0;outline:none;display:block">
													</a>
												</td>
												<td width="13">&nbsp;</td>
												<td width="25">
														<a href="#" title="Pinterest" target="_blank">
														<img src="'.$wo['config']['site_url'].'/themes/wowonder/icons/social/pinterest.png" height="25" width="25" alt="Pinterest" style="border:0;outline:none;display:block">
													</a>
												</td>
											</tr>
										</tbody>
									</table>
									</td>
								</tr>
							</tbody>
						</table>
						<table cellspacing="0" cellpadding="0" width="100%" border="0" align="center" bgcolor="#f2f2f2">
								<tbody>
									<tr>
										<td valign="top" align="center" width="100%" style="padding-bottom:15px">
											<table width="502" border="0" cellspacing="0" cellpadding="0" align="center">
												<tbody><tr>
													<td align="center" style="padding:0px 10px 0px 10px">
														<span style="font-family:Arial,Helvetica,Sans serif;font-size:10px;line-height:12px;color:#494949">
															<a href="'.$wo['config']['site_url'].'/setting/notifications-settings" style="color:#494949;text-decoration:underline" target="_blank">Unsubscribe</a> |
															<a href="'.$wo['config']['site_url'].'/terms/privacy-policy" style="color:#494949;text-decoration:underline" target="_blank">Privacy Policy</a> |
															<a href="'.$wo['config']['site_url'].'/contact-us" style="color:#494949;text-decoration:underline" target="_blank">Contact Support</a>
														</span>
													</td>
												</tr>
												<tr>
													<td align="center" style="padding:10px 10px 0px 10px">
															<span style="font-family:Arial,Helvetica,Sans serif;font-size:10px;line-height:12px;color:#494949">
															Battleship Dr, Honolulu, HI 96818, USA
														  </span>
													</td>
												</tr>
												<tr>
													<td align="center" style="padding:10px 10px 0px 10px">
															<span style="font-family:Arial,Helvetica,Sans serif;font-size:10px;line-height:12px;color:#494949">
															ﾂｩ'.date("Y").' '.$wo['config']['siteName'].', LLC.
														  </span>
													</td>
												</tr>
											</tbody></table>
										</td>
									</tr>
							</tbody>
						</table>
				</div>
			</div>
		</body>
		</html>';


	

	return $htmlcontent;


}
die;
?>