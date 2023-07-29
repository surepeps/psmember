<?php
global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

$user_id = $wo['user']['user_id'];
$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);


    $prop_id = $_POST['listing_id'];
    $buyer_id = $_POST['buyer_id'];
    $date_request = $_POST['schedule_date'];
    $time_request = $_POST['schedule_time'];
    $seller_id = $_POST['owner_id'];
    $message = $_POST['message'];
    
    $fname = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $uname = $_POST['username'];
    
    $sch_status = "pending";
    
    
    $getvisit_details = mysqli_query($sqlConnect, "SELECT *, COUNT(sid) AS myid FROM `Wo_Schedule_Visits` WHERE property_id = $prop_id AND user_id = $buyer_id");
    $vis_det = mysqli_fetch_assoc($getvisit_details);
    $count_user_visit = $vis_det['myid'];
    $vis_id = $vis_det['sid'];
    // INSERT SCHEDULE VISIT DETAILS INTO DATABASE
    if($count_user_visit < 1){
        $schedulequery = mysqli_query($sqlConnect,"INSERT INTO `Wo_Schedule_Visits` (`property_id`,`user_id`,`property_author`,`visit_date`,`visit_time`,`stitle`,`name`,`uname`,`email`,`phone`,`visits_status`,`created_date`,`modified_date`) VALUES($prop_id,$buyer_id,$seller_id,'$date_request','$time_request','$message','$fname','$uname','$email','$phone','$sch_status',now(),now() )");
    }else{
        $schedulequery = mysqli_query($sqlConnect,"UPDATE `Wo_Schedule_Visits` SET `property_id` = $prop_id, `user_id` = $buyer_id, `property_author` = $seller_id,`visit_date` = '$date_request',`visit_time` = '$time_request',`stitle` = '$message',`name` = '$fname',`uname` = '$uname',`email` = '$email',`phone` ='$phone',`visits_status` = '$sch_status', `counter_id` = 0, `modified_date` = now() WHERE sid = $vis_id ");
    }
    // NOTIFCATION SETTING
    $subject = "Schedule Visit Nofication";
    $notfy_type = "schedule_message";
    
    
        
    $internal_msg = "Hello I made a request for Schedule Visit on your Property <b>".Wo_PropertyNameFromId($prop_id)."</b> <a href=/my-schedule-visit?search=".urlencode(Wo_PropertyNameFromId($prop_id)).">Click here</a> to view my schedule Visit Details.";
    
    $internal_msg2 = "You have a schedule visit request from ".$uname." on your property <b>".Wo_PropertyNameFromId($prop_id)."</b> Please kindly on the button below to view the schedule details";
    $internal_msg2_link = $wo['config']['site_url']."/my-schedule-visit?search=".urlencode(Wo_PropertyNameFromId($prop_id));
    
 
     if($schedulequery){
        $r_id = $seller_id;
            $messages = Wo_RegisterMessage(array(
                'from_id' => Wo_Secure($buyer_id),
                'to_id' => Wo_Secure($r_id),
                'text' => Wo_Secure($internal_msg),
                'time' => time()
            ));
            
            if ($messages > 0) {
                
                $messages = Wo_GetMessages(array(
                    'message_id' => $messages,
                    'user_id' => $r_id
                ));
                
                foreach ($messages as $wo['message']) {
                    $wo['message']['color'] = Wo_GetChatColor($wo['user']['user_id'], $r_id);
                    $html .= Wo_LoadPage('messages/messages-text-list');
                }
                
            
                $to_id        = $r_id;
                $recipient    = Wo_UserData($to_id);
                
                $data['messages_count'] = Wo_CountMessages(array('new' => false,'user_id' => $r_id));
                $data['posts_count'] = $recipient['details']['post_count'];
                $notify = json_decode($recipient['notification_settings'], true);
                if ($wo['config']['emailNotification'] == 1) {
                    $send_notif   = array();
                    $send_notif[] = (!empty($recipient) && ($recipient['lastseen'] < (time() - 120)));
                    $send_notif[] = ($recipient['e_last_notif'] < time() && $recipient['e_sentme_msg'] == 1);
                    if ($notify['e_email_reschedulevisit'] == 1) {
                        $db->where("user_id", $to_id)->update(T_USERS, array(
                            'e_last_notif' => (time() + 3600)
                        ));
                        $wo['emailNotification']['notifier'] = $wo['user'];
                        $wo['emailNotification']['type']     = $notfy_type;
                        $wo['emailNotification']['url']      = $internal_msg2_link;
                        $wo['emailNotification']['msg_text'] = Wo_Secure($internal_msg2);
                        $send_message_data                   = array(
                            'from_email' => $wo['config']['siteEmail'],
                            'from_name' => $wo['config']['siteName'],
                            'to_email' => $recipient['email'],
                            'to_name' => $recipient['name'],
                            'subject' => $subject,
                            'charSet' => 'utf-8',
                            'message_body' => Wo_LoadPage('emails/notifiction-email'),
                            'is_html' => true
                        );
                        if ($wo['config']['smtp_or_mail'] == 'smtp') {
                            $send_message_data['insert_database'] = 1;
                        }
                        Wo_SendMessage($send_message_data);
                    }
                    
                    
                }
                
                $data = array(
                        'status' => 200,
                        'html' => $html,
                        'message' => 'Request visit has been submitted successfully'
                    );
                
            }else{
                 $data = array(
                    'status' => 400,
                    'message' => 'Error While trying to Make Schedule'
                );
            }
            
            
    }else{
        $data = array(
            'status' => 400,
            'message' => 'Error While trying to Send Schedule Visit Message'
        );
    }
    
    
    
    
header("Content-type: application/json");
echo json_encode($data);
die;


?>