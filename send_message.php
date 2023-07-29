<?php

$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');

require_once('assets/init.php');
/*require_once($root.'/assets/includes/functions_one.php');*/

/*require_once($root.'/assets/includes/functions_two.php');
require_once($root.'/assets/includes/functions_three.php');*/
global $wo, $sqlConnect;




$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);

function get_stratistic_points($action) {

	global $sqlConnect;

	$query = mysqli_query($sqlConnect, "SELECT $action FROM `wo_Strastic_point`");
	
	$Poitdetails = mysqli_fetch_array($query);

	return $Poitdetails[$action];
}


if(isset($_POST['user_id']) && !isset($_POST['action']) && $_POST['action']!="single_listing_send") {

	$userid = $_POST['user_id'];

	$straticpints = get_stratistic_points('message');


	$Userpointquery = mysqli_query($sqlConnect, "SELECT points FROM `Wo_Users` WHERE user_id=".$userid);
		
	$Poitdetails = mysqli_fetch_array($Userpointquery);

	$points = $Poitdetails['points'];


	if($wo['user']['username']!="admin") {

		if($points < $straticpints  ) {

			echo "insufficient points";
			die();
		}

	}
	

	$remainingpoints =  $points - $straticpints;



	mysqli_query($sqlConnect, "UPDATE `Wo_Users` SET points=".$remainingpoints." WHERE user_id=".$userid);

}


if(isset($_POST['action']) && $_POST['action']=="single_listing_send") {
    //if(empty($_POST['user_id'])){die("cant");}
	
	/*$straticpints = get_stratistic_points('contact');
	
	
	$Userpointquery = mysqli_query($sqlConnect, "SELECT points,username FROM `Wo_Users` WHERE user_id=".$_POST['user_id']);
		
	$Poitdetails = mysqli_fetch_array($Userpointquery);

	$points = $Poitdetails['points'];


	if($Poitdetails['username']!="admin") {

		if($points < $straticpints  ) {

			echo "You do not have sufficient points to make Contact .";
			die();
		}

	}


	$remainingpoints =  $points - $straticpints;

	mysqli_query($sqlConnect, "UPDATE `Wo_Users` SET points=".$remainingpoints." WHERE user_id=".$_POST['user_id']);

*/

	$query = mysqli_query($sqlConnect,"SELECT * FROM `Wo_Users` WHERE user_id=".$_POST['user_id']);


	$row = mysqli_fetch_array($query);

	$useremail = $row["email"];
	$userfirstname = $row["first_name"];
	$userlastname = $row["last_name"];


	/******** Authr details *********/
	$query = mysqli_query($sqlConnect,"SELECT * FROM `Wo_Users` WHERE user_id=".$_POST['authorid']);
	$Authordetail = mysqli_fetch_array($query);
	
	if(!empty($Authordetail)) {

		$authoremail = $Authordetail['email'];
		$authorfirstname = $row["first_name"];
		$authorlastname = $row["last_name"];


		$message_subject = $_POST['message_subject'];
		
		if(!empty($_POST['message_email'])){
			$message_content  = "Name: ".$_POST['message_name']."\n";
			$message_content .= "Email: ".$_POST['message_email']."\n";
			$message_content .= "Phone: ".$_POST['message_phone']."\n";
			$message_content .= "Message: ".$_POST['message_content'];
			$useremail = $_POST['message_email'];
			$userfirstname = $_POST['message_name'];
			$userlastname = "";
		}else{
		    $message_content  = $_POST['message_content']."\n";	
		}
		$property_id = $_POST['property_id'];



		$send_to = $authoremail;
		$send_from = $useremail;
		/*$field1 = $_POST['field1'];*/
		$message = $message_content;
		$date = date('Y-m-d H:i:s');


		$msg=str_ireplace('<p>','',$message);



		$current_date    = date( 'Y-m-d 00:00:00' );

		$authorid = $_POST['authorid'];

		$result_query = mysqli_query($sqlConnect, "SELECT * FROM  `Wo_email_markting` WHERE author_id = ".$_POST['authorid']."  AND pro_id= $property_id AND datettime= '$current_date'" );

		$result_data = mysqli_fetch_array($result_query);
		$count_sms   =  mysqli_num_rows($result_query); 

		if ($count_sms == 0 ) {
			$count_sms  = 1;

			$dateval = date( "Y-m-d" );
			

			$query   = "INSERT INTO Wo_email_markting (`pro_id`, `author_id`, `count`,`datettime`) VALUES ({$property_id},{$authorid},'{$count_sms}','{$dateval}')";

			$sql_query = mysqli_query($sqlConnect, $query);

		} else {
			$count_sms = $count_sms + 1;

			$query   = "UPDATE `Wo_email_markting` SET `count` = $count_sms WHERE `id` = ".$result_data['id'];
			$sql_query = mysqli_query($sqlConnect, $query);
			//$count_user_emails =  get_user_meta($get_current_user_id(),'count_emails_',true);

		}
		$userid = $wo['user']['user_id'];
		if(!empty($userid)){
			$query   = "INSERT INTO Wo_user_messages (`send_from`, `send_to`, `message`,`time`) VALUES ('{$userid}','{$authorid}','{$msg}','{$date}')";
			$sql_query = mysqli_query($sqlConnect, $query);
		}	
			 //dvm if($sql_query) {
             
			$remainingpoints =  $points - $straticpints;

			/*mysqli_query($sqlConnect, "UPDATE `Wo_Users` SET points=".$remainingpoints." WHERE user_id=".$_POST['user_id']);*/
			$from = $wo['user']['user_id']."<". strip_tags($send_from) . ">";

			$headers = "From: " .$userfirstname."<". strip_tags($send_from) . ">\r\n";
			/*$headers .= "Reply-To: " . strip_tags( $send_from ) . "\r\n";
			$headers .= "CC: info@strastic.com\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";*/
			$subject = $message_subject;
			//Wo_send_email_by_Send_Grid($send_to,$from,$subject,'text/html',$htmlcontent);
			$emailcontent = ucfirst($wo['user']['username'])." sent you a message";
			$url = $wo['config']['site_url']."/".$wo['user']['username'];

			$typetext = "Sent Message";
			//Wo_send_email_by_Send_Grid_custom($send_to,$from,$subject,$authorid,$typetext,$msg,$url);
			
			mail($send_to,$subject,$msg,$headers);
			
			echo "Successfully Sent.";
			//}
			
	} else
		echo "Athour details did not found!!";


	

} else {

	//$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);

	$send_to = $_POST['send_to'];
	$send_from = $_POST['send_from'];
	/*$field1 = $_POST['field1'];*/
	$message = $_POST['message'];
	$date = date('Y-m-d H:i:s');


	$straticpints = get_stratistic_points('message');
	
	
	$Userpointquery = mysqli_query($sqlConnect, "SELECT points,username FROM `Wo_Users` WHERE user_id=".$send_from);
		
	$Poitdetails = mysqli_fetch_array($Userpointquery);

	$points = $Poitdetails['points'];


	if($Poitdetails['username']!="admin") {

		if($points < $straticpints  ) {

			echo "insufficient points";
			die();
		}

	}

	$remainingpoints =  $points - $straticpints;

	mysqli_query($sqlConnect, "UPDATE `Wo_Users` SET points=".$remainingpoints." WHERE user_id=".$send_from);


	$msg=str_ireplace('<p>','',$message);

	$query   = "INSERT INTO Wo_user_messages (`send_from`, `send_to`, `message`,`time`) VALUES ('{$send_from}','{$send_to}','{$msg}','{$date}')";

	$sql_query = mysqli_query($sqlConnect, $query);
	$timea = time();

	$query2   = "INSERT INTO Wo_Messages (`from_id`, `to_id`, `text`,`time`) VALUES ('{$send_from}','{$send_to}','{$msg}',{$timea})";

	$sql_query = mysqli_query($sqlConnect, $query2);


	/**************** Notify user ******************/

	$notifychekarr = array();

	$sellerquery = mysqli_query($sqlConnect,"SELECT * FROM `Wo_Users` where user_id=$send_to");
	$sellerdata = mysqli_fetch_array($sellerquery);

	$notifycheck = 0;
	$notifycheckemail = 0;

	if(!empty($sellerdata['notification_settings'])) {
		$notifycheckarr = unserialize(html_entity_decode($sellerdata['notification_settings']));
		$notifycheck = $notifycheckarr["e_notify_sentme_msg"];
	}

	$notifycheckemail = $sellerdata['e_sentme_msg'];

	if($notifycheck==1) :

		$url = $wo['config']['site_url'];
		$Notificationquery = mysqli_query($sqlConnect,"INSERT INTO `Wo_Notifications` (notifier_id,recipient_id,type,type2,text,url,time) VALUES('$send_from','$send_to','created_request','$type2','Send you a message','$url/messages/$send_from','".time()."')");

	endif;

	if($notifycheckemail==1) : 
		$recievermail = $sellerdata['email'];

		$emailcontent = ucfirst($wo['user']['username'])." sent you a message";

		$from = 'Strastic<noreply@strastic.com>';
		/*$headers = 'From: '.strip_tags( $from ). "\r\n" .
							   'Reply-To: ' . strip_tags( $from ) . "\r\n" .
					    	   'X-Mailer: PHP/' . phpversion();
		$headers .= "CC: info@strastic.com\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";*/
		$subject = 'Message From Appstrastic';


		$url = $wo['config']['site_url']."/".$sellerdata['username']; 


		$typetext = "Sent Message";
		//Wo_send_email_by_Send_Grid_custom($recievermail,$from,$subject,$send_to,$typetext,$emailcontent,$url);
      mail($recievermail,$subject,$emailcontent,$headers);


    endif;


	echo "Successfully Sent";

}

function Notifyusertempalte($emailcontent,$send_to) {


	$wo['emailNotification']['notifier'] = $wo['user'];
	$wo['emailNotification']['type']     = 'sent_message';
	$wo['emailNotification']['url']      = '';
	$wo['emailNotification']['msg_text'] =$email_content;


	$recieverdata = Wo_Userdata($send_to);

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
		<head></head>
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
																		<a href="https://strastic.com" style="text-emphasis:none" target="_blank">
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
																				You got a New Message .</h1></td>
																			</tr>
																			<tr>
																				<td width="100%" height="40" style="border-collapse:collapse"></td>
																			</tr>
																			<tr>
																				<td style="font-family:Helvetica;font-size:24px;color:#494949;text-align:left;line-height:20px;font-weight:bold;border-collapse:collapse" align="left">
																					Hi '.$recieverdata['username'].',
																				</td>
																			</tr>
																			<tr>
																				<td width="100%" height="40" style="border-collapse:collapse"></td>
																			</tr>
																			
																			<tr>
																				<td width="100%" height="20" style="border-collapse:collapse"></td>
																			</tr>
																			<tr>
																				<td style="font-family:Helvetica,arial,sans-serif;font-size:14.5px;color:#666666;text-align:left;line-height:20px;border-collapse:collapse" align="left">';
																		/*if (!empty($wo['emailNotification']['post_data']['text'])) {
											                                $htmlcontent .= $wo[emailNotification]['post_data'][ 'text'];
											                              }

											                              else if(!empty($wo['emailNotification']['msg_text'])){
											                                $htmlcontent .="\"" . $wo['emailNotification']['msg_text'] . "\"";
											                              }
*/
						                              	$htmlcontent .= $emailcontent;

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
																					<a style="background-image: linear-gradient(#193d70,#16345b);color: #ffffff;text-decoration: none;padding: 10px 20px;border-radius: 6px" href="'.$wo['config']['site_url'].'/messages/'.$wo['emailNotification']['notifier']['user_id'].'">View Message details</a>
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
																	<br>Sincerely,<br>Strastic Support
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
															© 2019 Strastic, LLC.
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


//echo "INSERT INTO Wo_user_messages (`send_from`, `send_to`, `message`,`time`) VALUES ({$send_from},{$send_to},'{$msg}','{$date}')";



/*}*/

die;

?>