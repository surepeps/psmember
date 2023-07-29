<?php
global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');

require_once('assets/init.php');


$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);

if(isset($_POST['action']) && $_POST['action']=="update_offers") {

	$offer_status = $_POST['offer_status'];
	$offer_id = $_POST['offer_id'];
	
	$query = mysqli_query($sqlConnect,"UPDATE `Wo_offers` SET offer_status='$offer_status' WHERE id=".$offer_id);

	if($query) {
			
				/********************** Message update on offer status change **************************/

				$query = mysqli_query($sqlConnect,"SELECT *	 FROM `Wo_offers` WHERE id=".$offer_id); 

				$OffersDetails = mysqli_fetch_array($query);

				$Offerdetailsarr = json_decode($OffersDetails['offers_details'],1);

				$messageid = $OffersDetails['message_id'];
				$text = "Offer ".$offer_status." at price of ".$Offerdetailsarr['offer_price'];
				$messagequery = mysqli_query($sqlConnect,"UPDATE `Wo_Messages` SET text='".$text."' WHERE id=".$messageid);


				$query = mysqli_query($sqlConnect,"SELECT * FROM `Wo_offers` WHERE id=".$offer_id); 


				$OffersDetails = mysqli_fetch_array($query);

				$seller_id = $OffersDetails['last_action_by'];

				$userid = $wo['user']['user_id'];
				$query = mysqli_query($sqlConnect,"UPDATE `Wo_offers` SET last_action_by='$userid' WHERE id=".$offer_id);
				

				switch ($offer_status) {

					case 'counter':
						$notifyvar = "e_counteroffer";
						$notifyvaremail = "e_email_counteroffer";
						$type2 = "haha";
						$textemail = "countered";
						$url = $wo['config']['site_url']."/messages/".$userid;
						break;
					case 'rejected':
						$notifyvar = "e_rejectoffer";	
						$notifyvaremail = "e_email_rejectoffer";
						$type2 = "sad";
						$textemail = "rejected";
						$url = $wo['config']['site_url']."/messages/".$userid;
						break;
					case 'accepted':
						$notifyvar = "e_acceptoffer";	
						$notifyvaremail = "e_email_acceptoffer";
						$type2 = "sad";
						$textemail = "accepted";
						$url = $wo['config']['site_url']."/messages/".$userid;
						break;
					default:
						# code...
						break;
				}

				/**************** Notify user ******************/

				$notifycheck = 0;
				$notifycheckemail = 0;

				$sellerquery = mysqli_query($sqlConnect,"SELECT * FROM `Wo_Users` where user_id=$seller_id");
				$sellerdata = mysqli_fetch_array($sellerquery);

				if(!empty($sellerdata['notification_settings'])) {
					$notifycheckarr = unserialize(html_entity_decode($sellerdata['notification_settings']));

					if(!empty($notifycheckarr[$notifyvar]))
						$notifycheck = $notifycheckarr[$notifyvar];

					$notifycheckemail = $notifycheckarr[$notifyvaremail];
				}
				
				if($notifycheck==1) :

					$Notificationquery = mysqli_query($sqlConnect,"INSERT INTO `Wo_Notifications` (notifier_id,recipient_id,type,type2,text,url,time) VALUES('$userid','$seller_id','created_request','$type2','$textemail Your Offer','$url','".time()."')");
				endif;
				
				if($notifycheckemail==1):

					$sellerdata = Wo_UserData($seller_id);

					$emailseller = $sellerdata['email'];

					$emailcontent = ucfirst($wo['user']['username'])." ".$textemail." your offer . ";

					$from = 'Strastic<noreply@strastic.com>';

					$subject = 'Message From Appstrastic';


					$propertyid = $OffersDetails['property_id'];
					$query = mysqli_query($sqlConnect,"SELECT * FROM `Wo_Listing` WHERE id=".$propertyid); 

					$propertyDetails = mysqli_fetch_array($query);

					$tab1 = json_decode($propertyDetails["tab1"]);

					$address = $tab1->property_map_address;
					$singlepageurarr = explode(" ",strtolower($address));
					$slug = implode("",explode(",",implode("-", $singlepageurarr)));

					$query_meta= mysqli_query($sqlConnect,"SELECT * FROM `Wo_Listing_Meta` WHERE property_id= ".$propertyDetails["id"]);


					$slug = $propertyDetails["id"];

					if(Wo_Property_Slug($propertyDetails["id"])!="")
						$slug = Wo_Property_Slug($propertyDetails["id"]);

					$single_page_url = $wo['config']['site_url']."/property/".$slug; 


					$typetext = "Offer ".ucfirst($textemail);
					Wo_send_email_by_Send_Grid_custom($emailseller,$from,$subject,$seller_id,$typetext,$emailcontent,$url);

			    endif;

			
		echo "Offer Status Changed!";
	}
	
}  else if(isset($_POST['action']) && $_POST['action']=="check_stratistic_points") {

	$userid = $_POST['user_id'];
	$activity =  $_POST["activity"];
	$straticpints = Wo_get_stratistic_points($activity);

	if($activity=="featured_listing") {
		$listingpoints = Wo_get_stratistic_points("add_listing");
		$straticpints = $straticpints + $listingpoints;
	}
	$Userpointquery = mysqli_query($sqlConnect, "SELECT * FROM `Wo_Users` WHERE user_id=".$userid);
		
	$Poitdetails = mysqli_fetch_array($Userpointquery);

	$points = $Poitdetails['points'];


	if($points >= $straticpints || $Poitdetails['username']=="admin") {

		echo "1";
	} else {
		echo "0";
	}
	die;

} 

if(isset($_POST['action']) && $_POST['action']=="get_stratistic_points") {

	$userid = $_POST['user_id'];
	$activity =  $_POST["activity"];
	$straticpints = Wo_get_stratistic_points($activity);


	$Userpointquery = mysqli_query($sqlConnect, "SELECT * FROM `Wo_Users` WHERE user_id=".$userid);
		
	$Poitdetails = mysqli_fetch_array($Userpointquery);

	echo $points = $Poitdetails['points'];
	die;

} 

if(isset($_POST['action']) && $_POST['action']=="counter_offer_update") { 

	$offerid = $_POST['offer_id'];
	$newprice = $_POST['offer_price'];
	$userid = $_POST['user_id'];


	$query = mysqli_query($sqlConnect,"SELECT *	 FROM `Wo_offers` WHERE id=".$offerid); 

	$OffersDetails = mysqli_fetch_array($query);

	$Offerdetailsarr = json_decode($OffersDetails['offers_details'],1);

	$messageid = $OffersDetails['message_id'];
	$text = "Offer countered at price of ".$Offerdetailsarr['offer_price'];
	$messagequery = mysqli_query($sqlConnect,"UPDATE `Wo_Messages` SET text='".$text."' WHERE id=".$messageid);
	
	$newofferarr = array();
	foreach ($Offerdetailsarr as $key => $value) {
		if($key=="offer_price")
			$value = $newprice;

		$newofferarr[$key] = $value;
	}
	/*print_r($newofferarr);*/
	$Offerdetailsnew = json_encode($newofferarr);
	/********************* Message id update *****************************/

		$query_one = "SELECT `fid_3` FROM Wo_UserFields WHERE `user_id` =".$userid;
	    $sql       = mysqli_query($sqlConnect, $query_one);
	    $fetched_data = mysqli_fetch_assoc($sql);


	    $user_role = $fetched_data['fid_3'];


	    switch ($user_role) {
	        case '1':
	            $role="seller";
	            break;
	        case '2':
	           $role="buyer";
	            break;

	        case '3':
	            $role="investor";
	            break;
	        case '4':
	            $role="agent";
	            break;

	        case '5':
	           $role="wholesaler";
	            break;
	    }
	    if($role=="buyer" || $role=="investor")
	    	$toid = $OffersDetails['seller_id'];
	    else
	    	$toid = $OffersDetails['last_updated_by_user'];


	    $msg = "Offer is countered , new offer Price is ".$newprice;

		$querymessage   = "INSERT INTO Wo_Messages (`from_id`, `to_id`, `text`,`time`) VALUES ('{$userid}','{$toid}','{$msg}','')";
		
		$sql_query = mysqli_query($sqlConnect, $querymessage);
		$newmessageid = mysqli_insert_id($sqlConnect);

		$sellerdata = Wo_UserData($toid);

		if(!empty($sellerdata['notification_settings'])) {
			$notifycheckarr = unserialize(html_entity_decode($sellerdata['notification_settings']));
			if(!empty($notifycheckarr['e_counteroffer']))
				$notifycheck = $notifycheckarr['e_counteroffer'];

			$notifycheckemail = $notifycheckarr['e_email_counteroffer'];
		}


		if($notifycheck==1) :

			$Notificationquery = mysqli_query($sqlConnect,"INSERT INTO `Wo_Notifications` (notifier_id,recipient_id,type,type2,text,url,time) VALUES('$userid','$toid','created_request','$type2','countered Your Offer','/messages/".$toid."','".time()."')");
		endif;

		if($notifycheckemail==1):

			$sellerdata = Wo_UserData($toid);

			$emailseller = $sellerdata['email'];

			$emailcontent = ucfirst($wo['user']['username'])." countered your offer. ";

			$from = 'Strastic<noreply@strastic.com>';

			$subject = 'Message From Appstrastic';

			$propertyid = $OffersDetails['property_id'];
			$query = mysqli_query($sqlConnect,"SELECT * FROM `Wo_Listings` WHERE id=".$propertyid); 


			$propertyDetails = mysqli_fetch_array($query);

			$tab1 = json_decode($propertyDetails["tab1"]);

			$address = $tab1->property_map_address;
			$singlepageurarr = explode(" ",strtolower($address));
			$slug = implode("",explode(",",implode("-", $singlepageurarr)));

			$query_meta= mysqli_query($sqlConnect,"SELECT * FROM `Wo_Listing_Meta` WHERE property_id= ".$row["id"]);

			$slug = $row["id"];

			if(Wo_Property_Slug($row["id"])!="")
				$slug = Wo_Property_Slug($row["id"]);

			$single_page_url = $wo['config']['site_url']."/property/".$slug; 
			$url = $wo['config']['site_url']."/messages/".$toid;

			$typetext = "Offer Countered ";
			Wo_send_email_by_Send_Grid_custom($emailseller,$from,$subject,$toid,$typetext,$emailcontent,$url);


	    endif;


	$offerquery = mysqli_query($sqlConnect,"UPDATE `Wo_offers` SET offers_details = '$Offerdetailsnew' , offer_status = 'counter' ,last_action_by=".$userid.", message_id=".$newmessageid." WHERE id=".$offerid);

	if($offerquery) {


		echo "Offer Updated Successfully!!";
	}
}


if(isset($_POST['action']) && $_POST['action']=="update_property_images") { 

	$last_insert_id = $_POST['last_insert_id'];
	$form_data = $_POST['form_data'];

	$multipel_serialize_images = serialize($form_data);

	$query = mysqli_query($sqlConnect, "UPDATE `Wo_Listing` SET `tab6` = '$multipel_serialize_images' WHERE `id` = {$last_insert_id}");


	
}
die;

?>