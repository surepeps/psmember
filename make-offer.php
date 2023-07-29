<?php

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);

// current logged in user id which is the send's id
$user_id = $wo['user']['user_id'];
if(isset($_POST['property_id']) && $_POST['property_id'] > 0){
    
    $id = $_POST['property_id'];

    // GET PROPERTY DETAILS FROM DATABASE TABLE
    $query = mysqli_query($sqlConnect,"SELECT * FROM `Wo_Listing` where id=$id");
    $row = mysqli_fetch_array($query);
    $tab1 = json_decode($row["tab1"]);
    $tab4 = json_decode($row["tab4"]);
    $tab6 = unserialize($row["tab6"]);
    $authorid = $row["user_id"];
    
    
    //GET PROPERTY AUTHOR DETAILS
    $author_info = mysqli_query($sqlConnect,"SELECT username,email FROM `Wo_Users` WHERE user_id=$authorid");
    $user_infor = mysqli_fetch_array($author_info);
    
    $owner_name = $user_infor['username'];
    $owner_email = $user_infor['email'];
    
    
    $proeprty_id = $row["id"];
	$prop_title = $tab1->listing_title;
	$property_map_address = $tab1->entered_address;
	$offerstatus = "pending";
	
	$offer_closing_date = date("Y-m-d",strtotime($_POST['offer_closing_date']));
	$expiration_offer_date =  date("Y-m-d",strtotime($_POST['expiration_offer_date']));
	
	$myoffdetails['proeprty_id'] = $proeprty_id;
	$myoffdetails['prop_title'] = $prop_title;
	$myoffdetails['property_map_address'] = $property_map_address;
	
    $myoffdetails['offer_price'] = $_POST['offer_price'];
    $myoffdetails['offer_initial_price'] = $_POST['offer_initial_price'];
    $myoffdetails['offer_closing_date'] = $_POST['offer_closing_date'];
    $myoffdetails['expiration_offer_date'] = $_POST['expiration_offer_date'];
    $myoffdetails['inspection_period'] = $_POST['inspection_period'];
    
    $myoffdetails['authorid'] = $authorid;
	
	$offers_details = json_encode($myoffdetails);
	$last_updated_by_user = $user_id;
	$readunreadstatus = "unread";
	$notifier_id = $user_id;
	
	$seller_id = $authorid;
	$recipient_id = $seller_id;

    // check if user already made offer on the samee property
    $getoffer_details = mysqli_query($sqlConnect, "SELECT *, COUNT(id) AS myid FROM `Wo_offers` WHERE property_id = $proeprty_id AND last_updated_by_user = $user_id");
    $off_det = mysqli_fetch_assoc($getoffer_details);
    $count_user_offer = $off_det['myid'];

    if($count_user_offer > 0){
    $status = "pending";
    $offer_id = $off_det['id'];
    $offerquery = mysqli_query($sqlConnect,"UPDATE `Wo_offers` SET  `counter_id` = 0, `offer_status` = '$status', `offers_details` = '$offers_details', `modified_date` = now(),`offer_start_date` = '$offer_closing_date',`offer_end_date` = '$expiration_offer_date'  WHERE id = $offer_id");
        
    }else{

    $offerquery = mysqli_query($sqlConnect,"INSERT INTO `Wo_offers` (`property_id`,`offers_details`,`offer_status`,`last_updated_by_user`,`read_unread_status`,
		`seller_id`,`created_date`,`modified_date`,`offer_start_date`,`offer_end_date`,`last_action_by`) VALUES($proeprty_id,'$offers_details','$offerstatus',
									$last_updated_by_user,'$readunreadstatus',$seller_id,now(),now(),'$offer_closing_date','$expiration_offer_date','$last_updated_by_user')");
    }

    $Notificationquery = mysqli_query($sqlConnect,"INSERT INTO `Wo_Notifications` (notifier_id,recipient_id,type,type2,text,url,time) VALUES('$notifier_id','$recipient_id','created_request','-','You have received an offer	','".$wo['config']['site_url']."/messages/".$user_id."','".time()."')");
	//$offerid = mysqli_insert_id($sqlConnect);
	
	    $linkkk = $wo['config']['site_url'].'/seller-dashboard-offer-pending';
        $internal_msg = $wo['user']['username']." sent you an offer on property - <a href=/my-counter-offer?search=".urlencode($prop_title).">".$prop_title."</a>";
		
		
		$internal_msg2_link = '/my-counter-offer?search='.urlencode($prop_title);
		$internal_msg2 = ' Made an offer on your property '.$prop_title;

     // loop the internal message to sent to users (reciever's id)
    if($offerquery){  
        $r_id = $_POST['authorid'];
            $messages = Wo_RegisterMessage(array(
                'from_id' => Wo_Secure($user_id),
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
                    if ($notify['e_makeoffer'] == 1) {
                        $db->where("user_id", $to_id)->update(T_USERS, array(
                            'e_last_notif' => (time() + 3600)
                        ));
                        $wo['emailNotification']['notifier'] = $wo['user'];
                        $wo['emailNotification']['type']     = 'offer_message';
                        $wo['emailNotification']['url']      = $internal_msg2_link;
                        $wo['emailNotification']['msg_text'] = Wo_Secure($internal_msg2);
                        $send_message_data                   = array(
                            'from_email' => $wo['config']['siteEmail'],
                            'from_name' => $wo['config']['siteName'],
                            'to_email' => $recipient['email'],
                            'to_name' => $recipient['name'],
                            'subject' => 'Offer Notification',
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
                        'message' => 'Offer Made Successfully'
                    );
                
            }else{
                 $data = array(
                    'status' => 400,
                    'message' => 'Error While trying to make offer'
                );
            }
            
            
    }else{
        $data = array(
            'status' => 400,
            'message' => 'Error While trying to insert Offer Property'
        );
    }



}else{
    $data = array(
        'status' => 400,
        'message' => 'Error No Property id Found '
    );
}

header("Content-type: application/json");
echo json_encode($data);
die;


?>