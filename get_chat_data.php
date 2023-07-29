<?php

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

 $phone_number  =  $_POST['phone_number'];
 $user_id  =  $_POST['user_id'];

 
 $sms_firstdata11 =  getJoinOrderByChatData('all_sms','  where `from_number`='. $phone_number.'  AND `user_id`='.$user_id.' ORDER BY `receive_date` ASC',1);
 

  $sms_data11 =   getJoinOrderByChatData('all_sms','  where `from_number`='. $phone_number.'  AND `user_id`='.$user_id);
  





echo '<input type="hidden" id="active_number" value="'.$sms_firstdata11['from_number'].'" >';
echo '<input type="hidden" name="twilio_phonenumber" id="twilio_phonenumber" value="'.$sms_firstdata11['to_number'].'" >';
   foreach ($sms_data11 as $value) { 
    
        if($value['direction']=="inbound"){
      
           echo  '<div class="row no-gutters">
                   <div class="col-md-3" style="float:left; width: 50%;">
                     <div class="chat-bubble chat-bubble--left">'.
                     $value['sms_text'].'
                     </div>
                   </div>
                 </div>';
        }else{ 
            echo '<div class="row no-gutters">
                <div class="col-md-3 offset-md-9"  style="float:right; width: 50%;" >
                  <div class="chat-bubble chat-bubble--right">'
                  . $value['sms_text'].
                 ' </div>
                </div>
              </div>';
        } 
       
   }

 ?>





