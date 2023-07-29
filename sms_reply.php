<?php
global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

// Include the bundled autoload from the Twilio PHP Helper Library
require_once('twilio-php-master/src/Twilio/autoload.php');
use Twilio\Rest\Client;

$userId = $wo['user']['user_id'];

$CwalletFee = 0.03;

$wBalance = checkWalletBalance($userId);
    
// if($wBalance >= $CwalletFee){
    
//     // Get Post Values
//     foreach($_POST as $key =>$value){

//         $$key = $value;
//     }
    
//     // Get LCN data
//     $user_data = getSingleLCNBYNumber($twilio_number);

   
//     $user_id  = $userId;
    
//     // Twilio Config INIT
//     $account_sid = $wo['config']['sms_twilio_username'];
//     $auth_token  = $wo['config']['sms_twilio_password'];
//     $twilio = new Client($account_sid, $auth_token);

//     try{
//         // Trigger message api action
//         $message = $twilio->messages->create(US_formate($sms_to),["body" => $sms_text, "from" => US_formate($twilio_number)]);
        
//         // Deduct from wallet
//         reduceWalletBalance_A($CwalletFee,$userId);
        
//         // Data to save
//         $data = [
//             'from_number' => $sms_to,
//             'sms_text' => $sms_text,
//             'to_number' => $twilio_number,
//             'user_id' => $user_id,
//             'status' => "seen",
//             'direction' =>'outbound',
//             'receive_date' => date("m d Y h:i:s A")
//         ];
        
//         $query = insertRow('all_sms', $data); 
//         if($sqlConnect->query($query)){
            
//         }
        
//         echo "success";
        
//         exit;
        
//     }catch(Exception $e){
//         echo  $e->getMessage();
       
//         exit;
//     }
                
     
// }else{
    
//     echo "Insufficient Fund | Sorry, You need to replenish your wallet";
//     exit;
// }


if( isset($_POST['action']) && ($_POST['action'] == "send_message") ) {
        
    if ($wo['config']['who_upload'] == 'pro' && $wo['user']['is_pro'] == 0 && !Wo_IsAdmin() && (!empty($_FILES['sendMessageFile']) || !empty($_POST['message-record']))) {
        $data['status']       = 500;
        $data['invalid_file'] = 3;
    }
    else{
            
            $realFrom_number = $_POST['from_number'];
            $realTo_number = $_POST['to_number'];

        if ($realTo_number != $realFrom_number) {
            
            if (isset($_POST['user_id']) && is_numeric($_POST['user_id']) && $_POST['user_id'] > 0 ) {
                
                if($wBalance >= $CwalletFee){
                
                    $html          = '';
                    $media         = '';
                    $mediaFilename = '';
                    $mediaName     = '';
                    $invalid_file  = 0;
                    if (isset($_FILES['sendMessageFile']['name'])) {
                        if ($_FILES['sendMessageFile']['size'] > $wo['config']['maxUpload']) {
                            $invalid_file = 1;
                        } else if (!in_array($_FILES["sendMessageFile"]["type"], explode(',', $wo['config']['mime_types']))) {
                            $invalid_file = 2;
                        } else {
                            $fileInfo      = array(
                                'file' => $_FILES["sendMessageFile"]["tmp_name"],
                                'name' => $_FILES['sendMessageFile']['name'],
                                'size' => $_FILES["sendMessageFile"]["size"],
                                'type' => $_FILES["sendMessageFile"]["type"]
                            );
                            $media         = Wo_ShareFile($fileInfo);
                            $mediaFilename = $media['filename'];
                            $mediaName     = $media['name'];
                        }
                    } else if (!empty($_POST['record-file']) && !empty($_POST['record-name'])) {
                        $mediaFilename = $_POST['record-file'];
                        $mediaName     = $_POST['record-name'];
                    }
                    if (!empty($_POST['chatSticker'])) {
                        $fileend =  '_sticker_' . rand(111111,999999);
                        $mediaFilename = Wo_ImportImageFromUrl($_POST['chatSticker'], $fileend);
                    }
                    $sticker = '';
                    if (isset($_POST['chatSticker']) && Wo_IsUrl($_POST['chatSticker']) && strpos($_POST['chatSticker'], '.gif') !== false && !$mediaFilename && !$mediaName) {
                        $sticker = (isset($_POST['chatSticker']) && Wo_IsUrl($_POST['chatSticker'])) ? $_POST['chatSticker'] : '';
                    }
                    if (empty($_POST['textSendMessage']) && empty($mediaFilename) && empty($sticker)) {
                        exit();
                    }
                    $user_data    = Wo_UserData($_POST['user_id']);
                    if (!empty($user_data) && $user_data['message_privacy'] == 2) {
                        exit();
                    }
                    if (!empty($user_data) && $user_data['message_privacy'] == 1 && Wo_IsFollowing($wo['user']['user_id'], $_POST['user_id']) === false) {
                        exit();
                    }
                    
                    
                    // INPUTS
                    $realMessage = $_POST['textSendMessage'];
                    $realuser_id = $wo['user']['user_id'];
                    $user_id = $realuser_id;
                    
                
                    
                    // Twilio Config INIT
                    $account_sid = $wo['config']['sms_twilio_username'];
                    $auth_token  = $wo['config']['sms_twilio_password'];
                    $twilio = new Client($account_sid, $auth_token);
                
                    try{
                        // Trigger message api action
                        $message = $twilio->messages->create(US_formate($realFrom_number),["body" => $realMessage, "from" => US_formate($realTo_number)]);
                        
                        // Deduct from wallet
                        reduceWalletBalance_A($CwalletFee,$userId);
                        
                        // Data to save
                        $data = [
                            'from_number' => $realFrom_number,
                            'sms_text' => $realMessage,
                            'to_number' => $realTo_number,
                            'user_id' => $user_id,
                            'status' => "seen",
                            'direction' =>'outbound',
                            'm_time' => time(),
                            'receive_date' => date("m d Y h:i:s A")
                        ];
                        
                        $messages = createSMSChat($data);
                    
                        
                        if ($messages > 0) {
                            
                            
                            $messages = getSingleUserChat(array(
                                'user_id' => $user_id,
                                'from_number' => $realFrom_number,
                                'to_number' => $realTo_number,
                                'message_id' => $messages,
                            ));
                            
                            
                            foreach ($messages as $wo['message']) {
                                $wo['message']['color'] = Wo_GetChatColor($wo['user']['user_id'], $_POST['user_id']);
                                $html .= Wo_LoadPage('conversations/messages-text-list');
                            }
                            
                            $data = array(
                                'status' => 200,
                                'html' => $html,
                                'invalid_file' => $invalid_file
                            );
                            
                            $to_id        = $_POST['user_id'];
                            $recipient    = Wo_UserData($to_id);
                            $data['messages_count'] = Wo_CountMessages(array('new' => false,'user_id' => $_POST['user_id']));
                            $data['posts_count'] = $recipient['details']['post_count'];
                           
                           
                        }else{
                            
                            $data = array(
                                'status' => 400,
                                'error_type' => 1,
                                'message' => "Error sms message could not be sent"
                            );
                            
                        }
                        
                    }catch(Exception $e){
                        
                        $data = array(
                            'status' => 400,
                            'error_type' => 2,
                            'message' => $e->getMessage()
                        );
                        
                    }
                    
                    
                    
                    if ($invalid_file > 0 && empty($messages)) {
                        $data = array(
                            'status' => 500,
                            'invalid_file' => $invalid_file
                        );
                    }
                    
                    
                }else{
                    
                    $data = array(
                        'status' => 400,
                        'error_type' => 3,
                        'message' => 'Insufficient Fund | Sorry, You need to replenish your wallet'
                    );
                    
                }
                
                
                
                
            }else{
                
                $data = array(
                    'status' => 400,
                    'error_type' => 1,
                    'message' => "Error sms message could not be sent last error"
                );
                
            }
        }
        else{
            $data = array(
                        'status' => 400,
                        'error_type' => 2,
                        'message' => "Error reciever cannot be the same as sender"
                    );

        }
    }



    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}

  

?>
