<?php

global $wo, $sqlConnect;

require_once('config.php');

require_once('assets/init.php');


foreach($_POST as $key =>$value)
{

    $$key = $value;
}


   


if($_POST['action']=="addVoiceMail"){
    
$voicemaildata = getTableData('voicemail', ['twilio_number' => $twilio_number], 1);
if(empty($voicemaildata)){
$new_voicemail = [
    'user_id' =>$user_id,
    'twilio_number' =>$twilio_number,
    'text_voice_mail' => $text_voice_mail
   
    
];

$queryvoicemail= insertRow('voicemail', $new_voicemail); 



if($sqlConnect->query($queryvoicemail)) {
   
}

$data = [
    'voicemail_id' => 1
];

$where = [
    'number' => $twilio_number
];


$query = updateRow('lcn_table', $data, $where);
if(!$sqlConnect->query($query)) {
    pre(mysqli_error($sqlConnect)); exit; 
} 

 echo "Voicemail Added Successfully"; exit;



}
echo $twilio_number."  Have voicemail you can update."; exit;
}

if($_POST['action']=="updateVoiceMail"){
    
    
    $new_voicemail = [
        'user_id' =>$user_id,
        'twilio_number' =>$twilio_number,
        'text_voice_mail' => $text_voice_mail
       
        
    ];

$where = [
    'id' => $edit_id
];


$query = updateRow('voicemail', $new_voicemail, $where);

if(!$sqlConnect->query($query)) {
    pre(mysqli_error($sqlConnect)); exit; 
} 
echo "Voicemail Updated Successfully";
    
    }


    if($_POST['action']=="editVoiceMail"){
    
        $voicemaildata = getTableData('voicemail', ['id' => $edit_id], 1);
       
    echo  $voicemaildata['text_voice_mail'];
        
    }

    
    if($_POST['action']=="deletevoicemail"){
    

        
                        $data = [
                            'voicemail_id' => 2
                        ];

                        $where = [
                            'number' => '+'.$twilio_number
                        ];


                        //$query = updateRow('lcn_table', $data, $where);
                        $query = "UPDATE `lcn_table` SET `voicemail_id`=0 WHERE `number` = +$twilio_number";
                        if(!$sqlConnect->query($query)) {
                            pre(mysqli_error($sqlConnect)); exit; 
                        } 
        $deletequery =  deleteRow('voicemail', ['id' => $edit_id], 1);
        if(!$sqlConnect->query($deletequery)) {
            pre(mysqli_error($sqlConnect)); exit; 
        } 
    echo  "Voicemail deleted successfully";
        
    }