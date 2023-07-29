<?php

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

/*require_once('notification-template.php');*/

function Notifyusertempalte($email_content,$toid,$url) {
	global $wo;

	$userid = $wo['user']['user_id']; 

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

	$todata = Wo_Userdata($toid);
$htmlcontent = '
	<!doctype html>
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
																		<td><h1 style="line-height:1.1;text-align:center"><br> You got a Message !</h1></td>
																	</tr>
																	<tr>
																		<td width="100%" height="40" style="border-collapse:collapse"></td>
																	</tr>
																	<tr>
																		<td style="font-family:Helvetica;font-size:24px;color:#494949;text-align:left;line-height:20px;font-weight:bold;border-collapse:collapse" align="left">
																			Hi '.$todata['username'].',
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
																		<td style="font-family:Helvetica,arial,sans-serif;font-size:14.5px;color:#666666;text-align:left;line-height:20px;border-collapse:collapse" align="left">
																				'.$email_content.'
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
																			<a style="background-image: linear-gradient(#193d70,#16345b);color: #ffffff;text-decoration: none;padding: 10px 20px;border-radius: 6px" href="'.$url.'">View Email</a>
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



$userid = $_POST['user_id'];

$email = $_POST['email_value'];
		$email_content = $_POST['email_content'];
		$employee_email_content = $_POST['employee_email_content'];

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);


function get_stratistic_points($action) {

	global $sqlConnect;

	$query = mysqli_query($sqlConnect, "SELECT $action FROM `wo_Strastic_point`");
	
	$Poitdetails = mysqli_fetch_array($query);

	return $Poitdetails[$action];
}
$straticpints = get_stratistic_points('email');

$Userpointquery = mysqli_query($sqlConnect, "SELECT points FROM `Wo_Users` WHERE user_id=".$userid);
	
$Poitdetails = mysqli_fetch_array($Userpointquery);

$points = $Poitdetails['points'];

if($wo['user']['username']!="admin") {

	if($points < $straticpints) {

		echo "insufficient points";
		die();
	}


}


$remainingpoints  = 0;


if(isset($_POST['email_action']) && $_POST['email_action']=="from_dashboard") {

	$email = $_POST['email_value'];
	$subject = $_POST['email_subject'];

	$query   = "INSERT INTO Wo_Emails (`user_id`, `email_to`, `subject`,`message`,`date_time`) VALUES ('{$userid}','{$email}','Message From Appstrastic','{$email_content}',now())";

	$sql_query = mysqli_query($sqlConnect, $query);

	if($sql_query) {

		if($wo['user']['username']!="admin") {
			$remainingpoints  = $points  - $straticpints;
			$pointsupdate = mysqli_query($sqlConnect, "UPDATE `Wo_Users` SET points = ".$remainingpoints." WHERE user_id=".$userid);
		}
		

		$toidquery = mysqli_query($sqlConnect, "SELECT * FROM `Wo_Users` WHERE email='".$email."'");
		$userdetail = mysqli_fetch_array($toidquery);
		$userid = $userdetail['user_id'];

		$email_content = $_POST['email_content'];
		$employee_email_content = $_POST['employee_email_content'];
		$from = $wo['user']['username']."<noreply@strastic.com>";
		/*$headers = "From: " . strip_tags( $from ) . "\r\n";
		$headers .= "Reply-To: " . strip_tags( $from ) . "\r\n";
		$headers .= "CC: info@strastic.com\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";*/
		$subject = $subject;

		$url = $wo['config']['site_url']."/".$userdetail['username'];
		//mail($email,$subject,$email_content,$headers);

		//$htmlcontent = Notifyusertempalte($email_content,$userid,$url);

		//Wo_send_email_by_Send_Grid($email,$from,$subject,'text/html',$htmlcontent);

/*		Wo_send_email_by_Send_Grid_custom($email,$from,$subject,"You got a Message !","Great News","Good luck! We have finger crossed for you.",$userdetail['username'],$emailcontent,$url);*/

		$typetext = "Sent you a email ";
		Wo_send_email_by_Send_Grid_custom($email,$from,$subject,$userid,$typetext,strip_tags($email_content),$url);
        //mail($email,$subject,$htmlcontent,$headers);

	}

}





die;
?>