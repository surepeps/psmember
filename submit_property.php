<?php

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);


// function get_stratistic_points($action) {
// 	global $sqlConnect;

// 	$query = mysqli_query($sqlConnect, "SELECT $action FROM `wo_Strastic_point`");
	
// 	$Poitdetails = mysqli_fetch_array($query);

// 	return $Poitdetails[$action];
// }





if (isset($_POST['action']) && $_POST['action'] == "verifyProperty") {
	$property_id = filter('property_id');

	$status = 0;

	if (!$property_id) {
		$message = "Please select a valid property to verify!";
	} else {

		$where = ['id' => $property_id];
		$property = getTableData(T_LISTINGS, $where, 1);

		if (!$property) {
			$message = "This property is already deleted!";
		} else {
			$tab1 = json_decode($property['tab1'], 1);

			if (isset($tab1['is_verified']) && $tab1['is_verified'] == 1) {
				$message = "This property is already verified!";
			} else {
				$tab1['is_verified'] = 1;

				$query = updateRow(T_LISTINGS, ['tab1' => json_encode($tab1)], $where);

				if ($sqlConnect->query($query)) {
					$message = "Property is verified successfully!";
					$status = 1;
				} else {
					$status = 0;
					$message = mysqli_error($sqlConnect);
				}
			}
		}
	}



	if ($status == 0) {
		$message = '<div class="alert alert-danger"><i class="fa fa-times"></i> ' . $message . '</div>';
	} else {
		$message = '<div class="alert alert-success"><i class="fa fa-check"></i> ' . $message . '</div>';
	}

	$data = [
		'message' => $message,
		'status' => $status
	];



	header("Content-type: application/json");
	echo json_encode($data);
	die();
}

if(isset($_POST['action']) && $_POST['action']=="change_status") {

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


	$query_one = mysqli_query($sqlConnect, "UPDATE `Wo_Listing` SET `status` = '$status' WHERE `id` = {$property_id}");



} else {


	$formId = $_POST['form_id'];
	
	$form_data = $_POST['form_data'];

	
	$make_featured_listing = "";
	if($_POST['make_featured_listing']){
		$make_featured_listing = $_POST['make_featured_listing'];
	}

	if($_POST['user_id']){
	$user_id = $_POST['user_id'];
	}

	if($_POST['last_insert_id']){
	$last_insert_id = $_POST['last_insert_id'];
	}
	
	if($_POST['prop_type']){
	$prop_type = $_POST['prop_type'];
	}
	if($_POST['map_address']){
	$map_address = $_POST['map_address'];
	}
	if($_POST['bedroom']){
	$bedroom = $_POST['bedroom'];
	}
	if($_POST['bathroom']){
	$bathroom = $_POST['bathroom'];
	}
	if($_POST['prop_size']){
	$prop_size = $_POST['prop_size'];
	}

	if($_POST['deal_type']){
	$deal_type = serialize($_POST['deal_type']);
	}


	$params = array();
	$form_datanew = parse_str($_POST['form_data'], $params);
	$form_data =	json_encode($params);
	//$user_id = 110;

	$date = date('Y-m-d H:i:s');

	if($formId=='form_tab1'){

		
		if(isset($_POST['action']) && $_POST['action']=="update_property") {


			$propertyid = $_POST['propertyid'];

			/*$query   = "INSERT INTO Wo_Listing (`user_id`,`dtae_time`,`tab1`,`featured_listing`) VALUES ({$user_id},'{$date}','{$form_data}','{$make_featured_listing}')";*/
		
			$query   = "UPDATE Wo_Listing SET `user_id`='{$user_id}',`dtae_time`='{$date}',`tab1`='{$form_data}',`featured_listing`='{$make_featured_listing}' WHERE id =".$propertyid;
		
			$sql_query = mysqli_query($sqlConnect, $query);
			$last_inserted_id = $propertyid;

			/*propertyid:"<?php echo $id; ?>"*/
			$address = $map_address;
			$singlepageurarr = explode(" ",strtolower($address));
			$slug = implode("",explode(",",implode("-", $singlepageurarr)));


			/************ Check if slug already exists ************/
			$Getslugquery = mysqli_query($sqlConnect, "SELECT count(*) as slugcounts FROM `Wo_Listing_Meta` WHERE property_slug='".$slug."'");
					
			$Slugrows = mysqli_fetch_array($Getslugquery);

			$Slugcounts = $Slugrows['slugcounts'];

			if($Slugcounts > 1)
				$slug = $slug.'-'.$last_inserted_id;

			$querymeta   = "UPDATE Wo_Listing_Meta SET `property_slug`='{$slug}' WHERE `property_id`=".$propertyid;


			$sql_querymeta = mysqli_query($sqlConnect, $querymeta);


			$query2   = "UPDATE Wo_Filter SET `property_type`='{$prop_type}', `bathroom`='{$bathroom}', `bedroom`='{$bedroom}',`address`='{$map_address}',`area`='{$prop_size}',`user_id`={$user_id} WHERE property_id=".$propertyid;

			/*echo "INSERT INTO Wo_Filter (`property_id`, `property_type`, `bathroom`, `bedroom`,`address`,`area`,`user_id`) VALUES ({$last_inserted_id},'{$prop_type}','{$bathroom}','{$bedroom}','{$map_address}','{$prop_size}',{$user_id})";*/

			$sql_query2 = mysqli_query($sqlConnect, $query2);

			$last_inserted_id = $propertyid;
			if($sql_query){
				die($last_inserted_id);
			}else{
				die;
			}


		} else {

			@session_start();

			$_SESSION['property_action'] = "add_property";
			
			$query   = "INSERT INTO Wo_Listing (`user_id`,`dtae_time`,`tab1`,`featured_listing`,`status`) VALUES ({$user_id},'{$date}','{$form_data}','{$make_featured_listing}',1)";
				
			$sql_query = mysqli_query($sqlConnect, $query);
			$last_inserted_id = mysqli_insert_id($sqlConnect);

            Wo_reduce_features_permission('mylisting');
            if($make_featured_listing){
                Wo_reduce_features_permission('myfeaturedlisting');
            }
            
			$address = $map_address;
			$singlepageurarr = explode(" ",strtolower($address));
			$slug = implode("",explode(",",implode("-", $singlepageurarr)));

			/************ Check if slug already exists ************/
			$Getslugquery = mysqli_query($sqlConnect, "SELECT count(*) as slugcounts FROM `Wo_Listing_Meta` WHERE property_slug='".$slug."'");
					
			$Slugrows = mysqli_fetch_array($Getslugquery);

			$Slugcounts = $Slugrows['slugcounts'];
			if($Slugcounts > 0)
				$slug = $slug.'-'.$last_inserted_id;

			$querymeta   = "INSERT INTO Wo_Listing_Meta (`property_id`, `property_slug`) VALUES({$last_inserted_id},'{$slug}')";

			$sql_querymeta = mysqli_query($sqlConnect, $querymeta);


			$query2   = "INSERT INTO Wo_Filter (`property_id`, `property_type`, `bathroom`, `bedroom`,`address`,`area`,`user_id`) VALUES ({$last_inserted_id},'{$prop_type}','{$bathroom}','{$bedroom}','{$map_address}','{$prop_size}',{$user_id})";

			/*echo "INSERT INTO Wo_Filter (`property_id`, `property_type`, `bathroom`, `bedroom`,`address`,`area`,`user_id`) VALUES ({$last_inserted_id},'{$prop_type}','{$bathroom}','{$bedroom}','{$map_address}','{$prop_size}',{$user_id})";*/

			$sql_query2 = mysqli_query($sqlConnect, $query2);

			if($sql_query2){

				$userid = $_POST['user_id'];

				// $straticpints = 0;

				// if((isset($_POST['is_free']) && $_POST['is_free']==0 ) || $wo['user']['pro_type']!=1) {

				// 	$straticpints = get_stratistic_points('add_listing');
				// }
				

				// $Userpointquery = mysqli_query($sqlConnect, "SELECT points FROM `Wo_Users` WHERE user_id=".$userid);
					
				// $Poitdetails = mysqli_fetch_array($Userpointquery);

				// $points = $Poitdetails['points'];
				// $remainingpoints =  $points - $straticpints;

				// $straticpintsfeatured = 0;
				// if($wo['user']['pro_type']!=1) {

				// 	if($make_featured_listing==1) {
				// 		if((isset($_POST['is_free_featured']) && $_POST['is_free_featured']==0)) {
							
				// 			$straticpintsfeatured = get_stratistic_points('featured_listing');
				// 		}
				// 		$remainingpoints = $remainingpoints - $straticpintsfeatured;
				// 	}

				// }
				

				mysqli_query($sqlConnect, "UPDATE `Wo_Users` SET points=".$remainingpoints." WHERE user_id=".$userid);
				echo $last_inserted_id;
				die;
			}else{
				echo $last_inserted_id;
				die;
			}

		}

	}
	else if($formId=='form_tab2'){


		$form_datadecode = json_decode($form_data,true);

		$propdesc = $form_datadecode['property_description'];

		unset($form_datadecode['property_description']);

		$form_data = json_encode($form_datadecode);		

		$query_one = mysqli_query($sqlConnect, "UPDATE `Wo_Listing` SET `tab2` = '$form_data' WHERE `id` = {$last_insert_id}");
		
		$query_desc = mysqli_query($sqlConnect, "UPDATE `Wo_Listing_Meta` SET `property_desc` = '$propdesc' WHERE `property_id` = {$last_insert_id}");

		if($deal_type){
			$query_two = mysqli_query($sqlConnect, "UPDATE `Wo_Filter` SET `deal_type` = '$deal_type' WHERE `property_id` = {$last_insert_id}");
		}


		if($query_one){
			echo $last_insert_id; 
			die;
		}else{
			die(" not updated ");
		}
	}

	else if($formId=='form_tab3'){
		$query_one = mysqli_query($sqlConnect, "UPDATE `Wo_Listing` SET `tab3` = '$form_data' WHERE `id` = {$last_insert_id}");
		
		if($query_one){
			echo $last_insert_id;
			die;
		}else{
			die(" not updated ");
		}

	}

	else if($formId=='form_tab4'){
		$query_one = mysqli_query($sqlConnect, "UPDATE `Wo_Listing` SET `tab4` = '$form_data' WHERE `id` = {$last_insert_id}");
			
			if($_POST['prop_price']){

				$prop_price=$_POST['prop_price'];
				

				$query_two = mysqli_query($sqlConnect, "UPDATE `Wo_Filter` SET `price_range` = '$prop_price' WHERE `property_id` = {$last_insert_id}");
			}
		 
		if($query_one){
			echo $last_insert_id; 
			die;
		}else{
			die(" not updated ");
		}
	}

	else if($formId=='form_tab5'){
		$query_one = mysqli_query($sqlConnect, "UPDATE `Wo_Listing` SET `tab5` = '$form_data' WHERE `id` = {$last_insert_id}");
		 
		if($query_one){
			echo $last_insert_id;
			die;
		}else{
			die(" not updated ");
		}
	}


	else if($formId=='form_tab7'){
		$query_one = mysqli_query($sqlConnect, "UPDATE `Wo_Listing` SET `tab7` = '$form_data' WHERE `id` = {$last_insert_id}");


		/****************************** Email Buyers about new Property **********************************/

			


			$propertyurl = "";
			if(Wo_Property_Slug($last_insert_id)!="") {
				$slug = Wo_Property_Slug($last_insert_id);
				$propertyurl = $wo['config']['site_url']."/property/".$slug;
			}

			$query_meta= mysqli_query($sqlConnect,"SELECT `Wo_Listing`.id,`Wo_Listing`.user_id,`Wo_Filter`.* FROM `Wo_Listing` INNER JOIN `Wo_Filter` 
												ON `Wo_Listing`.id=`Wo_Filter`.property_id WHERE `Wo_Listing`.id =".$last_insert_id);
			$row_meta = mysqli_fetch_array($query_meta);
			if(!empty($row_meta))
				$id = $row_meta['property_id'];

			$addmeta = $zipcode = $city = "";
			$zipcode = "null";
			$authorid = $row_meta['user_id'];


			/*$querytwilio = mysqli_query($sqlConnect, "SELECT `twilio_number` FROM `Wo_Contact_Info` WHERE `user_id` = " . $user_id);

			$twilio_number = "";
			if (mysqli_num_rows($querytwilio) > 0) {
			    $fetched_data = mysqli_fetch_assoc($querytwilio);
			    $twilio_number = $fetched_data ['twilio_number'];
			} else {

			     $twilio_number = Wo_purchase_number($authorid);

			     $query1 = mysqli_query($sqlConnect, "INSERT INTO Wo_Contact_Info VALUES('','".$authorid."','".$twilio_number['phone_number']."','".$twilio_number['account_sid']."') ");
			}
*/


			$address = explode(",", $row_meta['address']);
				if(!empty($address)) {
					$city = $address[0];

					if(isset($address[1])) {
						$ziparr = explode(" ",$address[1]);
						$zipcode = end($ziparr);
			
					}
					
					if(isset($address[2])) {
						$ziparr2 = explode(" ",$address[2]);
						$zipcode2 = end($ziparr2);
					}
					
				}
			$property_type = $row_meta['property_type'];

			if($property_type=="1/2 Duplex")
				$property_type= "Â½ Duplex";
			$beds = $row_meta['bedroom'];
			$baths = $row_meta['bathroom'];
			$price_range = $row_meta['price_range'];

			

			$query = "SELECT `Wo_Users`.pro_type, `Wo_Users`.email,`Wo_Users`.user_id,`Wo_Buyerinfo`.* FROM `Wo_Users` INNER JOIN `Wo_Buyerinfo` ON `Wo_Users`.user_id =`Wo_Buyerinfo`.user_id WHERE 1=1 ";

			$query2  = $query;
			$query2 .=" AND Wo_Buyerinfo.send_all_properties=1 ";		
			
			
			if(!empty($price_range)){
				$query.=" AND `Wo_Buyerinfo`.min_price <= $price_range AND `Wo_Buyerinfo`.max_price >= $price_range";
			}

			if(!empty($property_type)){
				$property_type = str_replace("Single Family Home","Single Family",$property_type);
				$query.=" AND `Wo_Buyerinfo`.property_type like '%$property_type%'";
			}
			if(!empty($beds)){
				$beds = str_replace(".5","",$beds);
				$query.=" AND `Wo_Buyerinfo`.beds like '%$beds%'";
			}
			if(!empty($baths)){
				$baths = str_replace(".5","",$baths);
				$query.=" AND `Wo_Buyerinfo`.bath like '%$baths%'";
			}
			

			$query.=" AND Wo_Buyerinfo.send_all_properties!=1 ";
			
			if($address!=""){
				$query.=" AND (`Wo_Buyerinfo`.city like '%$city%' OR `Wo_Buyerinfo`.zip_code like '%$zipcode%' 
								OR `Wo_Buyerinfo`.city like '%$zipcode%' OR `Wo_Buyerinfo`.zip_code like '%$city%'
								OR `Wo_Buyerinfo`.zip_code like '%$zipcode2%'
								) ";
			}			
			$sqllll = $query;
			$query = mysqli_query($sqlConnect,$query);

			$allemails = array();

			$from = 'Strastic<noreply@strastic.com>';
			$subject = 'Message From Appstrastic';
			$typetext = "Property Match ";
			//echo "-$sqllll-";
			$ff = fopen("match.txt","a");
				fwrite($ff,"$sqllll\n==============================================================\n$query2 \n======================================================\n");
			
			while($Buyerdetails = mysqli_fetch_array($query)){ 

				$user_data = Wo_UserData($Buyerdetails['user_id']);
				$notifycheckemail = $notifychecksms = $notifycheck = "";
				if (!empty($user_data['notification_settings'])) {
					$notifychekarr = unserialize(html_entity_decode($user_data['notification_settings']));
					$notifycheck = $notifychekarr['e_matchcriteria'];
					$notifycheckemail = $notifychekarr['e_email_matchcriteria'];
					$notifychecksms = $notifychekarr['e_sms_matchcriteria'];
				}
				
				
				if($notifycheckemail==1) {
					/*$allemails [] = $Buyerdetails['email'];*/
					$emailcontent = "Your profile matches to one of property ";
					//echo $Buyerdetails['email'];
					
					//echo "<br>";
					//Wo_send_email_by_Send_Grid_custom($Buyerdetails['email'],$from,$subject,$Buyerdetails['user_id'],$typetext,$emailcontent,$propertyurl,$Buyerdetails['pro_type']);
				}
				
			
				//$Buyerdetails['pro_type']
				if($Buyerdetails['pro_type']==1){$is_pro=0;}else{$is_pro=1;}
				Wo_send_email_all_buyers_sendgrid_dvm($last_insert_id,$Buyerdetails['email'],$is_pro);
					fwrite($ff,$Buyerdetails['email']."-is pro-".$is_pro."\n---------------------------------------------------");
				
				if($notifychecksms==1) {

					$recieverphone = $Buyerdetails['phone_number'];
		        	$message = "Your profile matches to following properties \n ".$propertyurl;
			
		        	//$send = Wo_SendSMSMessage_dashboard($recieverphone,'+14079880583',$message);

				}

	         	
			}
			fclose($ff);
			
			if(isset($_SESSION['property_action']) && $_SESSION['property_action'] == "add_property" && Wo_IsAdmin()){
				//Wo_send_email_all_buyers_sendgrid($last_insert_id);
			}
		/************************************************************************************************/
		
	
			$ff2 = fopen("match2.txt","a");
			
				
		$query2 = mysqli_query($sqlConnect,$query2);
		
			while($Buyerdetails2 = mysqli_fetch_array($query2)){ 

				$user_data2 = Wo_UserData($Buyerdetails2['user_id']);
				$notifycheckemail2 = $notifychecksms2 = $notifycheck2 = "";
				if (!empty($user_data2['notification_settings'])) {
					$notifychekarr2 = unserialize(html_entity_decode($user_data2['notification_settings']));
					$notifycheck2 = $notifychekarr2['e_matchcriteria'];
					$notifycheckemail2 = $notifychekarr2['e_email_matchcriteria'];
					$notifychecksms2 = $notifychekarr2['e_sms_matchcriteria'];
				}
				
				
				if($notifycheckemail2==1) {
					/*$allemails [] = $Buyerdetails2['email'];*/
					$emailcontent = "Your profile matches to one of property ";
					//echo $Buyerdetails2['email'];
					
					//echo "<br>";
					//Wo_send_email_by_Send_Grid_custom($Buyerdetails2['email'],$from,$subject,$Buyerdetails2['user_id'],$typetext,$emailcontent,$propertyurl,$Buyerdetails2['pro_type']);
				}
				
			
				//$Buyerdetails2['pro_type']
				if($Buyerdetails2['pro_type']==1){$is_pro=0;}else{$is_pro=1;}
				Wo_send_email_all_buyers_sendgrid_dvm($last_insert_id,$Buyerdetails2['email'],$is_pro);
					fwrite($ff2,$Buyerdetails2['email']."-is pro-".$is_pro."\n");
				
				if($notifychecksms2==1) {

					$recieverphone = $Buyerdetails2['phone_number'];
		        	$message = "Your profile matches to following properties \n ".$propertyurl;
			
		        	//$send = Wo_SendSMSMessage_dashboard($recieverphone,'+14079880583',$message);

				}

	         	
			}
			fclose($ff2);
		
		if($query_one){
			echo $last_insert_id;
			die;
		}else{
			die(" not updated ");
		}
	}

}

die;
	
	?>