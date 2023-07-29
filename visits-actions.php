<?php
global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');
/*require_once($root.'/config.php');
require_once($root.'/assets/init.php');
die;*/

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);

if(isset($_POST['action']) && $_POST['action']=="update_visits") {

	$visit_status = $_POST['visit_status'];
	$visit_id = $_POST['visit_id'];
	$user_id = $wo['user']['user_id'];
	
	$query = mysqli_query($sqlConnect,"UPDATE `Wo_Schedule_Visits` SET visits_status='$visit_status' WHERE sid=".$visit_id);

	if($query) {

				$query = mysqli_query($sqlConnect,"SELECT * FROM `Wo_Schedule_Visits` WHERE sid=".$visit_id); 


				$VisitDetails = mysqli_fetch_array($query);
				if($VisitDetails['last_action_by']==0)
					$reciepeintid = $VisitDetails['user_id'];
				else
					$reciepeintid = $VisitDetails['last_action_by'];

				$query = mysqli_query($sqlConnect,"UPDATE `Wo_Schedule_Visits` SET last_action_by='$user_id' WHERE sid=".$visit_id);
				/********************** Message update on offer status change **************************/

				$messageid = $VisitDetails['message_id'];
				$text = "Visit ".$visit_status." , for date ".$VisitDetails ['visit_date']." ,Time: ".$VisitDetails ['visit_time'];

				$messagequery = mysqli_query($sqlConnect,"UPDATE `Wo_Messages` SET text='".$text."' WHERE id=".$messageid);


				$userid = $user_id;

				switch ($visit_status) {

					case 'accepted':
						$notifyvar = "e_acceptvisit";
						$notifyvaremail = "e_email_acceptvisit";
						$type2 = "haha";
						$textemail = "accepted";
						$url = $wo['config']['site_url']."/messages/".$userid;
						break;
					case 'rejected':
						$notifyvar = "e_rejectvisit";
						$notifyvaremail = "e_email_rejectvisit";	
						$type2 = "sad";
						$textemail = "rejected";
						$url = $wo['config']['site_url']."/messages/".$userid;
						break;
					
					default:
						# code...
						break;
				}

				/**************** Notify user ******************/

				$notifychekarr = array();

				$sellerquery = mysqli_query($sqlConnect,"SELECT * FROM `Wo_Users` where user_id=$reciepeintid");
				$sellerdata = mysqli_fetch_array($sellerquery);

				$notifycheck = 0;
				$notifycheckemail = 0;

				if(!empty($sellerdata['notification_settings'])) {
					$notifycheckarr = unserialize(html_entity_decode($sellerdata['notification_settings']));

					
					if(!empty($notifycheckarr[$notifyvar]))
						$notifycheck = $notifycheckarr[$notifyvar];

						$notifycheckemail = $notifycheckarr[$notifyvaremail];
				}

				if($notifycheck==1) :

					$Notificationquery = mysqli_query($sqlConnect,"INSERT INTO `Wo_Notifications` (notifier_id,recipient_id,type,type2,text,url,time) VALUES('$userid','$reciepeintid','created_request','$type2','$textemail your Visit request','$url','".time()."')");
				
				endif;

				if($notifycheckemail==1) :
					$recievermail = $VisitDetails['email'];


					$emailcontent = ucfirst($wo['user']['username'])." '".$textemail."' your scheduled visit request.";

					$from = 'Strastic<noreply@strastic.com>';

					$subject = 'Message From Appstrastic';

					//mail($email,$subject,$email_content,$headers);

					//$htmlcontent = Notifyusertempalte($emailcontent,$textemail,$url);
					$Headingmessage = "Your Visit get ".$textemail;

						$typetext = "Schedule Visit ".$textemail;
						if(Wo_send_email_by_Send_Grid_custom($recievermail,$from,$subject,$reciepeintid,$typetext,$emailcontent,$url))
						echo "Visit Status Changed!";
			        /*if(mail($recievermail,'New notification',$htmlcontent,$headers))
			        	echo "Visit Status Changed!";*/
					

			    endif;



		
	}
	die;
}

function Notifyusertempalte($emailcontent,$textemail,$url) {


	$wo['emailNotification']['notifier'] = $wo['user'];
	$wo['emailNotification']['type']     = 'sent_message';
	$wo['emailNotification']['url']      = '';
	$wo['emailNotification']['msg_text'] =$email_content;

	if($textemail=="Rejected")
		$newstype = "Sorry";
	else
		$newstype = "Great News";

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
																				Your Visit get '.$textemail.'!</h1></td>
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
																						'.$newstype.'!
																				</td>
																			</tr>
																			<tr>
																				<td width="100%" height="20" style="border-collapse:collapse"></td>
																			</tr>
																			<tr>
																				<td style="font-family:Helvetica,arial,sans-serif;font-size:14.5px;color:#666666;text-align:left;line-height:20px;border-collapse:collapse" align="left">';

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
																					<a style="background-image: linear-gradient(#193d70,#16345b);color: #ffffff;text-decoration: none;padding: 10px 20px;border-radius: 6px" href="'.$url.'">View Visit details</a>
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


	/*$htmlcontent = '
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
	  <head>
	    <meta name="viewport" content="width=device-width" />
	    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	    <style type="text/css">
	      img {
	      max-width: 100%;
	      }
	      body {
	      -webkit-font-smoothing: antialiased;
	      -webkit-text-size-adjust: none;
	      width: 100% !important;
	      height: 100%;
	      line-height: 1.6em;
	      }
	      body {
	      background-color: #f6f6f6;
	      }
	      @media only screen and (max-width: 640px) {
	      body {
	      padding: 0 !important;
	      }
	      h1 {
	      font-weight: 800 !important;
	      margin: 20px 0 5px !important;
	      }
	      h2 {
	      font-weight: 800 !important;
	      margin: 20px 0 5px !important;
	      }
	      h3 {
	      font-weight: 800 !important;
	      margin: 20px 0 5px !important;
	      }
	      h4 {
	      font-weight: 800 !important;
	      margin: 20px 0 5px !important;
	      }
	      h1 {
	      font-size: 22px !important;
	      }
	      h2 {
	      font-size: 18px !important;
	      }
	      h3 {
	      font-size: 16px !important;
	      }
	      .container {
	      padding: 0 !important;
	      width: 100% !important;
	      }
	      .content {
	      padding: 0 !important;
	      }
	      .content-wrap {
	      padding: 10px !important;
	      }
	      .invoice {
	      width: 100% !important;
	      }
	      }
	    </style>
	  </head>
	  <body itemscope itemtype="http://schema.org/EmailMessage" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6">
	    <table class="body-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6">
	      <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
	        <td style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
	        <td class="container" width="600" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;" valign="top">
	          <div class="content" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
	            <table class="main" width="100%" cellpadding="0" cellspacing="0" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border: 1px solid #e9e9e9;" bgcolor="#fff">
	              <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
	                <td class="alert alert-warning" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; color: #fff; font-weight: 500; text-align: center; border-radius: 3px 3px 0 0; background-color: #f58220; margin: 0; padding: 10px;" align="center" bgcolor="#FF9F00" valign="top">
	                  <b>New notification from '.$wo['config']['siteName'].'</b>
	                </td>
	              </tr>
	              <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
	                <td class="content-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 20px;" valign="top">
	                  <table width="100%" cellpadding="0" cellspacing="0" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
	                    <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
	                      <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
	                        <a href="'.$wo['emailNotification']['notifier']['url'].'" style="text-decoration:none;color:#444;"><img src="'.$wo['emailNotification']['notifier']['avatar'].'" style="width:60px;float:left;border-radius:100px; margin-right:10px;" alt="" />
	                        '.$wo['emailNotification']['notifier']['name'].'</a>
	                        <div style="color:#666;font-size:12px;">
	                          <b>
	                            '.$wo['emailNotification']['type_text'].':
	                          </b>
	                          <span>';
	                            
	                              if (!empty($wo['emailNotification']['post_data']['text'])) {
	                                $htmlcontent .= $wo['emailNotification']['post_data'][ 'text'];
	                              }

	                              else if(!empty($wo['emailNotification']['msg_text'])){
	                                $htmlcontent .="\"" . $wo['emailNotification']['msg_text'] . "\"";
	                              }
	                            
	                          $htmlcontent .='</span>
	                        </div>
	                      </td>
	                    </tr>
	                    <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
	                    </tr>
	                    <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
	                      <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 5px;" valign="top">
	                        <a href="'.$wo['emailNotification']['url'].'" class="btn-primary" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background-color: #16345b; margin: 0; border-color: #16345b; border-style: solid; border-width: 4px 10px;">View on '.$wo['config']['siteName'].'</a>
	                      </td>
	                    </tr>
	                    </tr>
	                  </table>
	                </td>
	              </tr>
	            </table>
	            <table width="100%" cellpadding="0" cellspacing="0">
	              
	                <tr>
	                  <td>
	                    <p style="text-align:center;padding-bottom:0;font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif;font-size:12px"><b>Our mailing address is:</b></p>
	                      <p style="text-align:center;margin-bottom:2px;font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif;font-size:11px;line-height: 1.5">
	                      <img src="'.$wo['config']['site_url'].'/themes/wowonder/img/logo.png" alt="Strastic" style="width:150px;margin-bottom:5px"><br>
	                      <b>Strastic, LLC.</b><br>
	                      Example Street, Orlando, FL 12345<br>
	                      </p>
	                      <p style="margin-top:0;margin-bottom:5px;text-align:center;font-size:11px"><a href="http://strastic.com" target="_blank">https://strastic.com</a></p>
	                  </td>
	                </tr>
	                <tr>
	                  <td>
	                    <p style="text-align:center;font-style: italic;margin-bottom: 20px;font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif;font-size: 11px">Copyright © 2018 Strastic, All rights reserved.</p>
	                  </td>
	                </tr>
	            </table>
	          </div>
	        </td>
	      </tr>
	    </table>
	  </body>
	</html>';
*/
	return $htmlcontent;


}

die;

?>