<?php

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);

// current logged in user id which is the send's id
$user_id = $wo['user']['user_id'];


if($_POST['action'] == "send_internal_msg"){
    if(isset($_POST['recie_id'])){
        // get the internal message
        $internal_msg = $_POST['mymsg'];
        
        // loop the internal message to sent to users (reciever's id)
        foreach($_POST["recie_id"] as $r_id){
            
            $messages = Wo_RegisterMessage(array(
                'from_id' => Wo_Secure($user_id),
                'to_id' => Wo_Secure($r_id),
                'text' => Wo_Secure($internal_msg),
                'time' => time()
            ));
            
            // $query_two     = "INSERT INTO " . T_MESSAGES . " (`from_id`, `to_id`, `text`, `time`) VALUES ({$user_id}, {$r_id}, '{$internal_msg}', " . time() . ")";
            // $sql_query_two = mysqli_query($sqlConnect, $query_two
            if ($messages > 0) {
                
                $messages = Wo_GetMessages(array(
                    'message_id' => $messages,
                    'user_id' => $r_id
                ));
                
                foreach ($messages as $wo['message']) {
                    $wo['message']['color'] = Wo_GetChatColor($wo['user']['user_id'], $r_id);
                    $html .= Wo_LoadPage('messages/messages-text-list');
                }
                
                $data = array(
                    'status' => 200,
                    'html' => $html,
                    'message' => 'Internal Message Sent Successfully'
                );
                
                $to_id        = $r_id;
                $recipient    = Wo_UserData($to_id);
                
                $data['messages_count'] = Wo_CountMessages(array('new' => false,'user_id' => $r_id));
                $data['posts_count'] = $recipient['details']['post_count'];
                if ($wo['config']['emailNotification'] == 1) {
                    $send_notif   = array();
                    $send_notif[] = (!empty($recipient) && ($recipient['lastseen'] < (time() - 120)));
                    $send_notif[] = ($recipient['e_last_notif'] < time() && $recipient['e_sentme_msg'] == 1);
                    if (!in_array(false, $send_notif)) {
                        $db->where("user_id", $to_id)->update(T_USERS, array(
                            'e_last_notif' => (time() + 3600)
                        ));
                        $wo['emailNotification']['notifier'] = $wo['user'];
                        $wo['emailNotification']['type']     = 'sent_message';
                        $wo['emailNotification']['url']      = $recipient['url'];
                        $wo['emailNotification']['msg_text'] = Wo_Secure($internal_msg);
                        $send_message_data   = array(
                            'from_email' => $wo['config']['siteEmail'],
                            'from_name' => $wo['config']['siteName'],
                            'to_email' => $recipient['email'],
                            'to_name' => $recipient['name'],
                            'subject' => 'New notification',
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
            }else{
                 $data = array(
                    'status' => 400,
                    'message' => 'Error While trying to send internal message'
                );
            }
            
           
        }
        
        
    }else{
       $data = array(
            'status' => 400,
            'message' => 'Reciever id cant be null'
        ); 
    }
    
    header("Content-type: application/json");
    echo json_encode($data);
    die;
}

if ($_POST['action'] == "send_internal_buyers_msg"){
    if(isset($_POST['recie_id'])){

        // get the internal message
        $internal_msg = $_POST['mymsg'];

        // loop the internal message to sent to users (reciever's id)
        foreach($_POST["recie_id"] as $r_id){

            $messages = Wo_RegisterMessage(array(
                'from_id' => Wo_Secure($user_id),
                'to_id' => Wo_Secure($r_id),
                'text' => Wo_Secure($internal_msg),
                'time' => time()
            ));

            // $query_two     = "INSERT INTO " . T_MESSAGES . " (`from_id`, `to_id`, `text`, `time`) VALUES ({$user_id}, {$r_id}, '{$internal_msg}', " . time() . ")";
            // $sql_query_two = mysqli_query($sqlConnect, $query_two
            if ($messages > 0) {

                $messages = Wo_GetMessages(array(
                    'message_id' => $messages,
                    'user_id' => $r_id
                ));

                foreach ($messages as $wo['message']) {
                    $wo['message']['color'] = Wo_GetChatColor($wo['user']['user_id'], $r_id);
                    $html .= Wo_LoadPage('messages/messages-text-list');
                }

                $data = array(
                    'status' => 200,
                    'html' => $html,
                    'message' => 'Internal Message Sent Successfully'
                );

                $to_id        = $r_id;
                $recipient    = Wo_UserData($to_id);

                $data['messages_count'] = Wo_CountMessages(array('new' => false,'user_id' => $r_id));
                $data['posts_count'] = $recipient['details']['post_count'];
                if ($wo['config']['emailNotification'] == 1) {
                    $send_notif   = array();
                    $send_notif[] = (!empty($recipient) && ($recipient['lastseen'] < (time() - 120)));
                    $send_notif[] = ($recipient['e_last_notif'] < time() && $recipient['e_sentme_msg'] == 1);
                    if (!in_array(false, $send_notif)) {
                        $db->where("user_id", $to_id)->update(T_USERS, array(
                            'e_last_notif' => (time() + 3600)
                        ));
                        $wo['emailNotification']['notifier'] = $wo['user'];
                        $wo['emailNotification']['type']     = 'sent_message';
                        $wo['emailNotification']['url']      = $recipient['url'];
                        $wo['emailNotification']['msg_text'] = Wo_Secure($internal_msg);
                        $send_message_data   = array(
                            'from_email' => $wo['config']['siteEmail'],
                            'from_name' => $wo['config']['siteName'],
                            'to_email' => $recipient['email'],
                            'to_name' => $recipient['name'],
                            'subject' => 'New notification',
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
            }else{
                $data = array(
                    'status' => 400,
                    'message' => 'Error While trying to send internal message'
                );
            }


        }

    }else{
        $data = array(
            'status' => 400,
            'message' => 'Reciever id cant be null'
        );
    }

    header("Content-type: application/json");
    echo json_encode($data);
    die;
}