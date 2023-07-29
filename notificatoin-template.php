<?php 

function Notifyusertempalte($email_content) {


	$wo['emailNotification']['notifier'] = $wo['user'];
	$wo['emailNotification']['type']     = 'sent_message';
	$wo['emailNotification']['url']      = '';
	$wo['emailNotification']['msg_text'] =$email_content;
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
	$htmlcontent = '
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
	                                $htmlcontent .= $wo[emailNotification]['post_data'][ 'text'];
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

	return $htmlcontent;


}


	

?>