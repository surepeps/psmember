<?php
global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
/*ini_set('display_errors',1);
error_reporting(E_ALL);
*/
require_once('assets/init.php');
$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);


function get_stratistic_points($action) {

	global $sqlConnect;

	$query = mysqli_query($sqlConnect, "SELECT $action FROM `wo_Strastic_point`");
	
	$Poitdetails = mysqli_fetch_array($query);

	return $Poitdetails[$action];
}


if(isset($_POST['action']) && $_POST['action']=="property_avaiable_time") {


	$messageid = "";
	$visit_id = "";
	if(isset($_POST['messageid']))
		$messageid = $_POST['messageid'];

	if(isset($_POST['visit_id']))
		$visit_id = $_POST['visit_id'];

	
	$query = mysqli_query($sqlConnect,"SELECT * FROM `Wo_Listing` where id=".$_POST['property_id']);
	$row = mysqli_fetch_array($query);

	$tab7 = json_decode($row["tab7"]);
	$datetext = $_POST['dateText'];

	$authorid = $row["user_id"];

	$fromarr = $tab7->availability_farr1->$datetext;
	$toarr   = $tab7->availability_toarr1->$datetext;

	if($fromarr!="") {

		echo '<h4 id="labeltext" class="m-t"></h4><div class="row panel">'; ?>
			<?php foreach ( $fromarr as $key=>$time ) {

				echo '<div class="col-md-12 text-left property_content-time "><p class="col-md-7">' .$time. ' To '. $toarr[$key] . '</p>';
				echo '<button id="btn_53638" data-visitid="'.$visit_id.'" data-messageid="'.$messageid.'" data_time="' . $time. ' To '. $toarr[$key] . '" class="btn-time sendrequest" style="width:120px">Request</button></div>';

			}
		echo '</div>';

	}
	die;
}


if(isset($_POST['action']) && $_POST['action']=="request_to_book_time") {

	$applydate = $_POST['applydate'];
	$property_id = $_POST['property_id'];
	$visit_time = $_POST['visit_time'];
	$authorid = $_POST['authorid'];
	$user_id = $_POST['user_id'];

	$userfirstname = $_POST['userfirstname'];
	$userlastname = $_POST["userlastname"];
	$useremail = $_POST["useremail"];
	$userphone = $_POST['userphone'];
	$username = $_POST['user_name'];

	$title = "";
	if ( $userfirstname!="") {
		$title = $userfirstname;
	}
	if ( $userlastname!="") {
		$title .= $userlastname;
	}

	$pagecountvisits = 0;

	$Pagecountquery = "SELECT count(*) as pagecount FROM `Wo_Schedule_Visits` WHERE user_id=".$wo['user']['user_id'];
	$sql = mysqli_query($sqlConnect, $Pagecountquery);
	$fetched_data = mysqli_fetch_assoc($sql);
	if(!empty($fetched_data))
		$pagecountvisits = $fetched_data['pagecount'];

	$pagepermissonvisits = Wo_Package_permission_Count('free_visits');


	/*if($pagecountvisits >= $pagepermissonvisits) {

		$straticpints = get_stratistic_points('schedul');
		
		
		$Userpointquery = mysqli_query($sqlConnect, "SELECT points,username FROM `Wo_Users` WHERE user_id=".$user_id);
			
		$Poitdetails = mysqli_fetch_array($Userpointquery);

		$points = $Poitdetails['points'];


		if($wo['user']['username']!="admin") {

			if($points < $straticpints  ) {

				echo "You do not have sufficient points to Schedule Visit.<br><a href='/wallet'>Purchase Points</a>";
				die();
			}

		}


		$remainingpoints =  $points - $straticpints;

		mysqli_query($sqlConnect, "UPDATE `Wo_Users` SET points=".$remainingpoints." WHERE user_id=".$_POST['user_id']);
	} */

	$query = mysqli_query($sqlConnect,"INSERT INTO `Wo_Schedule_Visits` (`property_id`,`user_id`,`stitle`,`read_unread_status_agent`,`read_unread_status_buyer`,`read_unread_status_employee`,`read_unread_status_wholeseller`,`read_unread_status_strastic_agent`,`property_author`,`visit_date`,`visit_time`,`fname`,`lname`,`email`,`phone`,`visits_status`,`created_date`,`modified_date`) VALUES($property_id,$user_id,'$title','unread','unread','unread','unread','unread',$authorid,'$applydate','$visit_time','$userfirstname','$userlastname','$useremail','$userphone','pending',now(),now())");

	$sid = mysqli_insert_id($sqlConnect);	

	if($query) {
		/*************** Insert in messages ***************/

		$msg = mysqli_real_escape_string($sqlConnect,$username." scheduled visit for you .");

		$querymessage   = "INSERT INTO Wo_Messages (`from_id`, `to_id`, `text`,`time`) VALUES ('{$user_id}','{$authorid}','{$msg}','".time()."')";
		
		$sql_query = mysqli_query($sqlConnect, $querymessage);
		$messageid = mysqli_insert_id($sqlConnect);	

		$Useroffermessageid = mysqli_query($sqlConnect, "UPDATE `Wo_Schedule_Visits` SET message_id=".$messageid." WHERE sid=".$sid); 

		$sellerquery = mysqli_query($sqlConnect,"SELECT * FROM `Wo_Users` where user_id=$authorid");
		$sellerdata = mysqli_fetch_array($sellerquery);

		$notifycheck = 0;
		$notifycheckemail = 0;

		if(!empty($sellerdata['notification_settings'])) {
			$notifycheckarr = unserialize(html_entity_decode($sellerdata['notification_settings']));

			if(!empty($notifycheckarr['e_schedulevisit']))
				$notifycheck = $notifycheckarr['e_schedulevisit'];

			$notifycheckemail = $notifycheckarr['e_email_schedulevisit'];
		}


		if($notifycheck==1) :
			$url = $wo['config']['site_url'];
			$Notificationquery = mysqli_query($sqlConnect,"INSERT INTO `Wo_Notifications` (notifier_id,recipient_id,type,type2,text,url,time) VALUES('$user_id','$authorid','created_request','haha','Created Schedule Visit request on your Listing.','$url/messages/$user_id','".time()."')");
		endif;

		if($notifycheckemail==1) : 


			$emailseller = $sellerdata['email'];

			$emailcontent = ucfirst($wo['user']['username'])." has scheduled a visit. ";

			$from = 'Strastic <noreply@strastic.com>';

			$subject = 'Message From Appstrastic';


			$useridemail = $wo['user']['user_id'];
			$url = $wo['config']['site_url']."/messages/$useridemail";


			$typetext = "Scheduled Visit";
			Wo_send_email_by_Send_Grid_custom($emailseller,$from,$subject,$authorid,$typetext,$emailcontent,$url);

	    endif;


		echo "Successfully Sent";
	}

	die;
} if(isset($_POST['action']) && $_POST['action']=="schedule_visit_update") { 

	$visitid = $_POST['visitid'];
	$applydate = $_POST['applydate'];
	$visit_time = $_POST['visit_time'];
	$userid = $_POST['userid'];

	$messageid = $_POST['messageid'];


	$query = mysqli_query($sqlConnect,"SELECT *	 FROM `Wo_Schedule_Visits` WHERE sid=".$visitid); 

	$VisitDetails = mysqli_fetch_array($query);

	$text = "Visit Rescheduled , for date ".$VisitDetails ['visit_date']." ,Time: ".$VisitDetails ['visit_time'];

	$messagequery = mysqli_query($sqlConnect,"UPDATE `Wo_Messages` SET text='".$text."' WHERE id=".$messageid);


	/************************ Message id update *******************************/
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
	    	$toid = $VisitDetails['property_author'];
	    else
	    	$toid = $VisitDetails['user_id'];


	    $msg = "Visit Rescheduled new Timings are  ".$applydate." ,Time: ".$visit_time;

		$querymessage   = "INSERT INTO Wo_Messages (`from_id`, `to_id`, `text`,`time`) VALUES ('{$userid}','{$toid}','{$msg}','".time()."')";
		
		$sql_query = mysqli_query($sqlConnect, $querymessage);
		$newmessageid = mysqli_insert_id($sqlConnect);



		$Useroffermessageid = mysqli_query($sqlConnect, "UPDATE `Wo_Schedule_Visits` SET visit_date='".$applydate."',visit_time='".$visit_time."',visits_status='rescheduled',last_action_by=".$userid.",message_id=".$newmessageid.",modified_date=now() WHERE sid=".$visitid); 

		$sellerquery = mysqli_query($sqlConnect,"SELECT * FROM `Wo_Users` where user_id=$toid");
		$sellerdata = mysqli_fetch_array($sellerquery);

		if($Useroffermessageid) {

			$notifycheck = 0;
			$notifycheckemail = 0;

			if(!empty($sellerdata['notification_settings'])) {
				$notifycheckarr = unserialize(html_entity_decode($sellerdata['notification_settings']));

				if(!empty($notifycheckarr['e_reschedulevisit']))
					$notifycheck = $notifycheckarr['e_reschedulevisit'];

				$notifycheckemail = $notifycheckarr['e_email_reschedulevisit'];
			}


			if($notifycheck==1) :
				$url = $wo['config']['site_url'];
				$Notificationquery = mysqli_query($sqlConnect,"INSERT INTO `Wo_Notifications` (notifier_id,recipient_id,type,type2,text,url,time) VALUES('$userid','$toid','created_request','haha','Rescheduled your Visit request.','$url/messages/$userid','".time()."')");
			endif;

			if($notifycheckemail) : 


				$emailseller = $sellerdata['email'];

				$emailcontent = $wo['user']['username']." has rescheduled your visit";

				$from = 'Strastic<noreply@strastic.com>';
				/*$headers = "From: " . strip_tags( $from ) . "\r\n";
				$headers .= "Reply-To: " . strip_tags( $from ) . "\r\n";
				$headers .= "CC: info@strastic.com\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";*/
				$subject = 'Message From Appstrastic';

				//mail($email,$subject,$email_content,$headers);

				//$htmlcontent = Notifyusertempalte($emailcontent,$toid,$Headingmessage,$sellerdata['username'],$emailcontent);

				$url = $wo['config']['site_url']."/messages/".$wo['user']['user_id'];
			/*	Wo_send_email_by_Send_Grid_custom($emailseller,$from,$subject,$Headingmessage,"Great News","Good luck! We have finger crossed for you.",$sellerdata['username'],$emailcontent,$url);*/


				$typetext = "Schedule Visit Rescheduled ";
				Wo_send_email_by_Send_Grid_custom($emailseller,$from,$subject,$toid,$typetext,$emailcontent,$url);
		        //@mail($emailseller,$subject,$htmlcontent,$headers);

		    endif;


			echo "Rescheduled Successfully!!";
		}
	die;
}

die;

?>