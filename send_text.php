<?php
// +------------------------------------------------------------------------+
// | @author Deen Doughouz (DoughouzForest)
// | @author_url 1: http://www.wowonder.com
// | @author_url 2: http://codecanyon.net/user/doughouzforest
// | @author_email: wowondersocial@gmail.com   
// +------------------------------------------------------------------------+
// | WoWonder - The Ultimate Social Networking Platform
// | Copyright (c) 2017 WoWonder. All rights reserved.
// +------------------------------------------------------------------------+

require_once('assets/init.php');

global $wo, $sqlConnect;

function get_stratistic_points($action) {

    global $sqlConnect;

    $query = mysqli_query($sqlConnect, "SELECT $action FROM `wo_Strastic_point`");
    
    $Poitdetails = mysqli_fetch_array($query);

    return $Poitdetails[$action];
}


if(isset($_POST['message_text'])) {

    $recieverid = $_POST['profile_id'];
    $recieverphone = $_POST['phone_number'];
    
    $userid = $wo['user']['user_id'];
    $username = $wo['user']['username'];
    $message = $_POST['message_text'];


    $query = mysqli_query($sqlConnect, "SELECT `twilio_number` FROM `Wo_Contact_Info` WHERE `user_id` = " . $userid);
    
    $twilio_number = "";
    if (mysqli_num_rows($query) > 0) {
        $fetched_data = mysqli_fetch_assoc($query);
        $twilio_number = $fetched_data ['twilio_number'];
    } else {

         $twilio_numbearray = Wo_purchase_number($userid);
         if(isset($twilio_numbearray['phone_number'])) {
            $twilio_number = $twilio_numbearray['phone_number'];

            $twilio_sid = $twilio_numbearray['account_sid'];

            $query1 = mysqli_query($sqlConnect, "INSERT INTO Wo_Contact_Info VALUES('','".$userid."','".$twilio_number."','".$twilio_sid."') ");
         }
        
    }

   
    
    if($twilio_number!="") {

         $send = Wo_SendSMSMessage_dashboard($recieverphone,$twilio_number, $message);
         if($send) {


            $straticpints = get_stratistic_points('text');


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

            
            $url = $wo['config']['site_url'];
            $Notificationquery = mysqli_query($sqlConnect,"INSERT INTO `Wo_Notifications` (notifier_id,recipient_id,type,type2,text,url,time) VALUES('$userid','$recieverid','created_request','haha',' Send you a SMS.','$url/$username','".time()."')");


            $sellerquery = mysqli_query($sqlConnect,"SELECT email FROM `Wo_Users` where user_id=$recieverid");
            $sellerdata = mysqli_fetch_array($sellerquery);

            $recievermail = $sellerdata['email'];


            $emailcontent = ucfirst($wo['user']['username'])." sent you an SMS message.";

            $from = 'Strastic<noreply@strastic.com>';
            /*$headers = 'From: '.strip_tags( $from ). "\r\n" .
                       'Reply-To: ' . strip_tags( $from ) . "\r\n" .
                       'X-Mailer: PHP/' . phpversion();
            $headers .= "CC: info@strastic.com\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";*/
            $subject = 'Message From Appstrastic';

            //mail($email,$subject,$email_content,$headers);
            $url = $wo['config']['site_url']."/".$wo['user']['username'];
            //$htmlcontent = Notifyusertempalte($emailcontent,$textemail,$url);

            $typetext = "SMS Message";

            if(Wo_send_email_by_Send_Grid_custom($recievermail,$from,$subject,$recieverid,$typetext,$emailcontent,$url))
                echo "1";
            /*if(mail($recievermail,'New notification',$htmlcontent,$headers))*/            
         } else {
            echo "Reciepent number is not valid.";
         }
    }
    exit();
}


function Notifyusertempalte($emailcontent,$textemail,$url) {


    $wo['emailNotification']['notifier'] = $wo['user'];
    $wo['emailNotification']['type']     = 'sent_message';
    $wo['emailNotification']['url']      = '';
    $wo['emailNotification']['msg_text'] =$email_content;


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
                                                                                 You got an SMS .</h1></td>
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


                                                        $htmlcontent .= '
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
                                                                                    <a style="background-image: linear-gradient(#193d70,#16345b);color: #ffffff;text-decoration: none;padding: 10px 20px;border-radius: 6px" href="'.$wo['config']['site_url'].'/'.$wo['user']['username'].'">View SMS</a>
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




mysqli_close($sqlConnect);
unset($wo);
?>