<?php
$RVMAudioUrl = "https://dev.propertysalers.com/upload/sounds/2021/08/Qoaz42zyswElbYywO4Fv_15_4e2120cf4154e6361e11a54e638d11b8_soundFile.wav";
$RVMContact = "+18506912713";
$from = "+14025321513";

$pData = array(
	'team_id' => "7e000961-a496-4e83-bcf3-547a54deb4be",
	'secret' => "0be52e1a-7d3a-4ed4-8d3f-5b64bc146ef0",
	'foreign_id' => array(
	    'user_id' => 598,
	    'concronid' => 12
	 ),
	'audio_url' => $RVMAudioUrl,
	'audio_type' => "wav",
	'phone_number' => $RVMContact,
	'caller_id' => $from,
	'callback_url' => "https://dev.propertysalers.com/ringlessresponse.php"
);

$RVMsendresponse = RVMAPI($pData);

echo "<pre>";
print_r($RVMsendresponse);
echo "</pre>";

function RVMAPI($postData){
    
    // Crul Api
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, 'https://api.dropcowboy.com/v1/rvm');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    
    $headers = array();
    $headers[] = 'Content-Type: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $result = curl_exec($ch);
    
    if (curl_errno($ch)) {
        // echo 'Error:' . curl_error($ch);
        return curl_error($ch);
    }else{
        return $result;
    }
    
    curl_close($ch);
    
}


        
//if($RVMsendresponse['status'] == "success"){
//	echo "Sent Successfully";
	
	// Update each cronrecords as done
	//UpdateCronRecordAsDone($RVMcorecodID);
	
//}else{
	
//	echo "Error while processing";
	
//}