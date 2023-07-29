<?php 

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

$web_url = $wo['config']['site_url'];

require_once('sendgrid-php/sendgrid-php.php');
use SendGrid\Mail\Personalization;
use SendGrid\Mail\To;


//
// 
// 
// CRON JOB MOVEMENT
// 
// 
// 


// Get all campaign pipelines 
$allactivepipelines = listAllPipelineForCron_2();
foreach($allactivepipelines as $aapl){
    
    // campaign pipeline ID
    $pipeID = $aapl['pipe_id'];
    
    // Get Campaign Pipeline Owner Id
    $user_id = getUserIdByPipeId($pipeID);
    
    // get all contacts in a pipeline
    // $conts = GetAllContactsfrocronJob($pipeID);
    
    // get all active crons by pipeline ID.
    $activeCrons = getAllActiveCronsByPipelineId($pipeID);
    
    // get first stage in the pipeline
    $firstStageId = GetThefirstStageInPipe(1,$pipeID);
    
    // get first step in the first stage
    $firststepinStage = getFirstStepInAStage(1,$firstStageId,$pipeID);
    
    
    // LOGS OF CRON JOBS 
    if(count($activeCrons) > 0){
        
        $numCont = GetTotalNumbersofContactsInPipe($pipeID);
        
        echo "<ul>";
        echo "</li><b> Total Numbers of Contacts in Pipe ID:- ".$pipeID." is => ".$numCont."</b></li>";
        echo "</ul>";
        
        foreach($activeCrons as $g){
            
            $sdata = array(
                'cron_id' =>  $g['cron_id'],
                'croncode' => $g['croncode'],
                'pipe_id' => $g['pipe_id'],
                'stage_id' => $g['stage_id'],
                'action_id' => $g['action_id'],
                'step_id' => $g['step_id'],
                'duration_b_stage' => $g['duration_b_stage'],
                'duration_b_step' => $g['duration_b_step'],
                'duration_b_action' => $g['duration_b_action'],
                'trigger_type' => $g['trigger_type'],
            );
            
            // get total numbers of contacts attached to a cron code
            $numContCode = getTotalContactsInACronCode($g['cron_id']);
            
            $stepD = GetSingleStageStepDetails($g['stage_id'],$g['step_id'],$pipeID);
                
            $time = array(
                'minutes' => $stepD['minutes'],
                'hours' => $stepD['hours'],
                'days' => $stepD['days'],
                'time' => $stepD['time'],
                'last_updt' => $pipeDate,
            );
            
            $tm = getTimeConversion($time);
            
            $res1 = intval($g['duration_b_stage'])-$tm;
            $res2 = intval($g['duration_b_step'])-$tm;
            $res3 = intval($g['duration_b_action'])-$tm;
            
            echo "Cron Code id ".$g['croncode']." Has total Number of Contacts :( ".$numContCode." ) in it.";
            
            echo "<ul>
            
                    <li>Row ID Of:- ".$g['cron_id']." Cron Code:- ".$g['croncode']." Ready For Movement In Pipe ID:- ".$g['pipe_id']." in Stage ID:- ".$g['stage_id']." With Step ID:- ".$g['step_id']."</li>
                    <ul>
                        <li>Next Stage ID:- ". getNextStageIdPipe($g['stage_id'],$pipeID)."| Time To use:- ".$g['duration_b_stage']."</<li>
                        <li>Next Step ID:-".getNextStageStepIdPipe($g['step_id'],$g['stage_id'],$pipeID)."| Time To use:- ".$g['duration_b_step']."</<li>
                        <li>Next Action ID:-".getNextStageStepActionIdPipe($g['action_id'],$g['step_id'],$g['stage_id'],$pipeID)."| Time To use:- ".$g['duration_b_action']."</<li>
                    </ul>
                    
                    <hr>
                    
                    <ul>
                        <li>Current Stage ID:- ".$g['stage_id']."| Time To use:- ".$res1."</<li>
                        <li>Current Step ID:-".$g['step_id']."| Time To use:- ".$res2 ."</<li>
                        <li>Current Action ID:-".$g['action_id']."| Time To use:- ".$res3 ." Current Time -: ".time()."</<li>
                    </ul>
                    <hr>
                </ul>";
                
            
            // SECOND TRIAL.....
             $getFirstAct = GetStageStepFirstAction($g['step_id'],$g['stage_id'],$pipeID);
             
            if( $getFirstAct > 0 && $g['action_id'] == null ) {
                
                //   $getFirstAct = GetStageStepFirstAction($g['step_id'],$g['stage_id'],$pipeID);
                    
                 updateNextActionToPipelineCRONID($getFirstAct,$g['cron_id'],$pipeID);
                    
                  $userBal = checkWalletBalance($g['added_by']);
                  $getActionCost = calculateActionContsTogeth($getFirstAct,$pipeID);
                   
                  if($userBal >= $getActionCost){
                      
                      updatethefirstactioninstep($sdata,$pipeID,$tm);
                      
                  }else{
                     
                      InsertLowWalletfundNotifi($g['added_by'],$g['pipe_id']);
                  }
                

            }else{
                
                $getNstage = getNextStageIdPipe($g['stage_id'],$pipeID);
                $getNstep = getNextStageStepIdPipe($g['step_id'],$g['stage_id'],$pipeID);
                $getNaction = getNextStageStepActionIdPipe($g['action_id'],$g['step_id'],$g['stage_id'],$pipeID);
                
                if($getNaction > 0){
                   
                  updatetriggertypeInCron($pipeID,3,$g['cron_id'],$tm);
                  
                  updateNextActionToPipelineCRONID($getNaction,$g['cron_id'],$pipeID);
                   
                  $userBal = checkWalletBalance($g['added_by']);
                  $getActionCost = calculateActionContsTogeth($getNaction,$pipeID);
                   
                  if($userBal >= $getActionCost){
                      
                      MoveContacttoNextAction($sdata,$pipeID,$tm);
                      
                  }else{
                      
                      InsertLowWalletfundNotifi($g['added_by'],$g['pipe_id']);
                      
                  }
                    
                    
                }elseif($getNstep > 0){
             
                    updatetriggertypeInCron($pipeID,2,$g['cron_id'],$tm);
                    MoveContstoNextStep($sdata,$pipeID,$tm);
                  
                }elseif($getNstage > 0){
                    
                    updatetriggertypeInCron($pipeID,1,$g['cron_id'],$tm);
                    MoveContstoNextStage($sdata,$pipeID,$tm);
                  
                }else{
                    echo "Cron Finished <br> ";
                }
             
                
            }
            
            
                
            
        }
        
        
        
    }else{
        
        echo "<p> No Cron for pipe ID:- ".$pipeID."</p>";
        
    }

    
    
}


// 
// 
// CRON JOB FOR ACTIONS
// 
//
$allacticronrecords = listAllPipelineForCron_2();

if( count($allacticronrecords) > 0 ){
    
    
    // 
    // EMAIL ACTION
    // 
    // 
    
    // Get all actions corresponding to email action table
    $allcorrespondingemailact = GetAllcorrespondingEmiailActions();
    
    foreach($allcorrespondingemailact as $acea){
        
        $act_id = $acea['step_action_id'];
        $pID = $acea['pipeId'];
        
        
        
        $gct = GetTotalNumbersofContactsInAction($act_id);
        
        if($gct > 0){
            
            $allcronrecordsEmail = listAllcronRecords($act_id);
        
            while ($fetched_data = mysqli_fetch_assoc($allcronrecordsEmail)) {
                
                
                $ownnerIdemail = getUserIdByPipeId($fetched_data['pipe_id']);
       
        	    $contD = getSingleContact($fetched_data['contact_id']);
                $emailD = getSINGLEEMAILActionDetails($fetched_data['action_id']);
              
                $fromNum = getsinglelcnnumberbypipeId($fetched_data['pipe_id']);
               
                    $Fname = $contD['firstname'];
                    $Lname = $contD['lastname'];
                    $Tname = $Fname." ".$Lname;
                    
                    if($contD['email'] != ""){
                        
                      $allEmails[] = $contD['email'];
                    
                        $vvem[] = [
                            "cronrecord_id" => $fetched_data['id'],
                            "firstname" => $contD['firstname'],
                            "lastname" => $contD['lastname'],
                            "email" => $contD['email'], 
                            "mobile" =>$contD['mobile'], 
                            "pipeOwner_id" => $ownnerIdemail,
                            "contact_id" => $fetched_data['contact_id'],
                            "action_id" => $fetched_data['action_id'], 
                            "userfullname" =>$Tname,
                            "sendingDetails" => [
                                "LCN" => $fromNum,
                                "from" => $emailD['email_from'],
                                "message" => $emailD['message'],
                                "subject" => $emailD['subject']
                            ],
                        ];  
                        
                    }

                    
        	}	
            
            
           
        }
        
    }
    
    foreach ( $allEmails as $email_address ) {

        $personalization = new Personalization();
        
        $personalization->addTo( new To( $email_address ) );
        
        $sendgridPersonalization[] = $personalization;
        
    }
    
    $vv = array_chunk($vvem, 1000, true);
    $chunkedUsers = array_chunk($sendgridPersonalization, 1000, true);
    
    // GET ALL POSSIBLE UNKNOWN VARIABLES
    $unknvar = ["~ContactFirstName~","~ContactLastName~","~ContactEmail~","~UserFirstName~","~LCN~","~PropertyAddress~","~PropertyCity~"];
    
    // NEW
    foreach($vv as $w => $edd){
        
        $ck = $chunkedUsers[$w];

        foreach($edd as $w2 => $edd2){
           
            // EMAIL DATAS   
            $uId = $edd2['pipeOwner_id'];
            $corecodID = $edd2['cronrecord_id'];
            
            $firstnameR = $edd2['firstname'] ? $edd2['firstname'] : "";
            $lastnameR = $edd2['lastname'] ? $edd2['lastname'] : "";
            $emailR = $edd2['email'] ? $edd2['email'] : "";
            $myLCN = $edd2['sendingDetails']['LCN'] ? $edd2['sendingDetails']['LCN'] : "";
    
            $subject = $edd2['sendingDetails']['subject'];
            $mybname = $edd2['sendingDetails']['from'];
            $Send_Email = $edd2['sendingDetails']['from'];
            $senderD = Wo_UserData($uId);
            
            if(!empty($senderD) || $senderD != ""){
                $senderName = $senderD['name'];
            }else{
                $senderName = "PropertySalers";
            }
            
            
            // GET ALL POSSIBLE UNKNW VARIABLE ANSWER
            $unvanas = [$firstnameR,$lastnameR,$emailR,$senderName,$myLCN,"",""];
            
            // GET MESSAGE FROM INPUT TEXTAREA
            $my_bc_msg = $edd2['sendingDetails']['message'];
            
            $newPhrase = str_replace($unknvar, $unvanas, $my_bc_msg);
            
            $email = new \SendGrid\Mail\Mail(); 
            $email->setfrom($Send_Email, $mybname);
            $email->setSubject($subject);
            $testing = "emails@propertysalers.com";
            $email->addto($testing, "Bulk Email");
            
            $email->addContent("text/html", $newPhrase);
            $sendgrid = new \SendGrid('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');
    
            // EMAIL GOTTEN OUT
            $personalization_r = $ck[$w2];
            $email->addPersonalization( $personalization_r );
            
            $response = $sendgrid->send($email);
            
            if($response){
                
                UpdateCronRecordAsDone($corecodID,1);
                reduceWalletBalance_A(0.006,$uId);
                 
            }else{
                
                UpdateCronRecordAsDone($corecodID,1);
                
            }
           
            
        }
        
        
        
        
    }
    
    
    
    
    
    
    
    
    //              //
    //              //
    //              //
    // SMS ACTION   //
    //              //
    //              //
    
    $allcorrespondingsmsact = GetAllcorrespondingSMSActions();
    foreach($allcorrespondingsmsact as $acsa){
        
        $smsact_id = $acsa['step_action_id'];
        
        
        
        $smsgct = GetTotalNumbersofContactsInAction($smsact_id);
        
        
        
        if($smsgct > 0){
            
            $actionsid[] = $smsact_id;
            $allsmscronrecords = listAllcronRecords($smsact_id);
            
            while ( $fetched_data = mysqli_fetch_assoc($allsmscronrecords) ) {
                
                $ownnerIdsms = getUserIdByPipeId($fetched_data['pipe_id']);
                
                $fromNum = getsinglelcnnumberbypipeId($fetched_data['pipe_id']);
                
                if($fromNum != ""){
                    $contD = getSingleContact($fetched_data['contact_id']);
                    $smsD = getSINGLESMSActionDetails($fetched_data['action_id']);
                    
                    
                        $Fname = $contD['firstname'];
                        $Lname = $contD['lastname'];
                        $Tname = $Fname." ".$Lname;
                        
                        $vvs[] = [
                            "cronrecord_id" => $fetched_data['id'],
                            "firstname" => $contD['firstname'],
                            "lastname" => $contD['lastname'],
                            "email" => $contD['email'], 
                            "mobile" =>$contD['mobile'], 
                            "contact_id" => $fetched_data['contact_id'],
                            "pipeOwner_id" => $ownnerIdsms,
                            "pipe_id" => $fetched_data['pipe_id'],
                            "action_id" => $fetched_data['action_id'], 
                            "userfullname" =>$Tname,
                            "sendingDetails" => [
                                "from" => $fromNum,
                                "message" => $smsD['message'],
                            ],
                        ];
                    
                }  
            }
            
        }
        
        
        
    }

    
    foreach($vvs as $sms){
        
        if($sms['mobile'] != "" && $sms['sendingDetails']['message'] != "" && $sms['sendingDetails']['from'] != ""){
            
            $corecodID = $sms['cronrecord_id'];
            $tos = $sms['mobile'];
            $UiDsMS = $sms['pipeOwner_id'];
            $messageSent = $sms['sendingDetails']['message'];
            $from = $sms['sendingDetails']['from'];
            
            if($tos != ""){
                
                $sendresponse = send_bulk_sms_broadcast($tos,$from,$messageSent);
        
                 
                if($sendresponse){
                    
                    $from = stringCounterReduce(array(
                        'lenght' => 10,
                        'string' => $from
                    ));
                    
                    // Data to save
                    $data = [
                        'from_number' => $tos,
                        'sms_text' => $messageSent,
                        'to_number' => $from,
                        'user_id' => $user_id,
                        'status' => "seen",
                        'direction' =>'outbound',
                        'm_time' => time(),
                        'receive_date' => date("m d Y h:i:s A")
                    ];
                    
                    $messages = createSMSChat($data);
                    
                    echo "Sent Successfully";
                    // Update each cronrecords as done
                    UpdateCronRecordAsDone($corecodID,1);
                    reduceWalletBalance_A(0.03,$UiDsMS);
                
                }else{
                    echo "Error while processing";
                }
                
                
            }
            
        
        
        
        }
    
    }
                
    







    
    //                              //
    //                              //
    //                              //
    // RINGLESS VOICEMAIL ACTION   //
    //                              //
    //                              //
    
    $allcorrespondingrvmact = GetAllcorrespondingRVMActions();
    foreach($allcorrespondingrvmact as $acrvm){
        $rvmact_id = $acrvm['step_action_id'];
        $pIDRVM = $acrvm['pipeId'];
        
        $rvmgct = GetTotalNumbersofContactsInAction($rvmact_id);
    
        if($rvmgct > 0){
            
            $allrvmcronrecords = listAllcronRecords($rvmact_id);
            while ( $fetched_data2 = mysqli_fetch_assoc($allrvmcronrecords) ) {
                
                $ownnerIdRvm = getUserIdByPipeId($fetched_data2['pipe_id']);
                
                $RVMfromNum = getsinglelcnnumberbypipeId($fetched_data2['pipe_id']);
                
                if($RVMfromNum != ""){
                    
                    $RVMcontD = getSingleContact($fetched_data2['contact_id']);
                    $RVMD = getSINGLERVMActionDetails($fetched_data2['action_id']);
                    
                    
                        $Fname = $RVMcontD['firstname'];
                        $Lname = $RVMcontD['lastname'];
                        $Tname = $Fname." ".$Lname;
                        
                        $RVMVs[] = [
                            "cronrecord_id" => $fetched_data2['id'],
                            "firstname" => $RVMcontD['firstname'],
                            "lastname" => $RVMcontD['lastname'],
                            "email" => $RVMcontD['email'], 
                            "mobile" =>$RVMcontD['mobile'], 
                            "contact_id" => $fetched_data2['contact_id'],
                            "pipe_id" => $fetched_data2['pipe_id'],
                            "pipeOwner_id" => $ownnerIdRvm,
                            "action_id" => $fetched_data2['action_id'], 
                            "userfullname" =>$Tname,
                            "sendingDetails" => [
                                "from" => $RVMfromNum,
                                "audio_url" => $RVMD['audio_url'],
                            ],
                        ];
                    
                    
                }
                
                
            }
            
            
        }
        
        
    }
    
    foreach($RVMVs as $rvm){
        
            
            if($rvm['mobile'] != "" && $rvm['sendingDetails']['audio_url'] != "" && $rvm['sendingDetails']['from'] != ""){
            
                $RVMcorecodID = $rvm['cronrecord_id'];
                $cronUserId = $rvm['pipeOwner_id'];
                $RVMContact = $rvm['mobile'];
                $RVMAudioUrl = $web_url."/".$rvm['sendingDetails']['audio_url'];
                $from = $rvm['sendingDetails']['from'];
                
                $pData = array(
                  'team_id' => "7e000961-a496-4e83-bcf3-547a54deb4be",
            	  'secret' => "0be52e1a-7d3a-4ed4-8d3f-5b64bc146ef0",
            	  'foreign_id' => array(
                	  'user_id' => $cronUserId,
                	  'concronid' => $RVMcorecodID
            	   ),
            	  'audio_url' => $RVMAudioUrl,
            	  'audio_type' => "wav",
            	  'phone_number' => PhoneNumberFormat($RVMContact),
                  'caller_id' => PhoneNumberFormat($from),
            	  'callback_url' => $web_url."/ringlessresponse.php"
                );
                
                    
                $RVMsendresponse = RVMAPI($pData);
        
                if($RVMsendresponse['status'] == "queue"){
                    
                    // Update each cronrecords as done
                    UpdateCronRecordAsDone($RVMcorecodID,1);
                
                }else{
                    
                    echo "Error while processing <br>";
                    
                }
                    
            
            }
    
    }
    
    
    
    
    
    
    

    // 
    // 
    // DIRECT MAIL ACTION
    // 
    // 
    // 
    
    
    
    
    
    
    
}



// MOVE TO NEXT STEP IF AVAILABLE
function MoveContstoNextStep($g,$pipeID,$tm){
    
    $getNstep = getNextStageStepIdPipe($g['step_id'],$g['stage_id'],$pipeID);
    if($getNstep > 0){
        
        if($g['trigger_type'] == 2){
            
            $timeTU = $g['duration_b_step'];

            if(time() >= $timeTU){
                
                // Move to the next step
                moveContactstothenestStepsCronID($g['cron_id'],$pipeID,$getNstep);
                
                $getNaction = getNextStageStepActionIdPipe($g['action_id'],$getNstep,$g['stage_id'],$pipeID);
                $getNstep = getNextStageStepIdPipe($getNstep,$g['stage_id'],$pipeID);
                $getNstage = getNextStageIdPipe($g['stage_id'],$pipeID);
                
                if($getNaction > 0){
                    updatetriggertypeInCron($pipeID,3,$g['cron_id'],$tm);
                }elseif($getNstep > 0){
                    updatetriggertypeInCron($pipeID,2,$g['cron_id'],$tm);
                }elseif($getNstage > 0){
                    updatetriggertypeInCron($pipeID,1,$g['cron_id'],$tm);
                }
                
                echo "Step Moved Successfull To:- $getNstep<br>";
                
            }
            
        }elseif($g['trigger_type'] == 3){
            MoveContacttoNextAction($g,$pipeID,$tm);
        }elseif($g['trigger_type'] == 1){
            MoveContstoNextStage($g,$pipeID,$tm);
        }
        
        
    }
    
}

// MOVE TO NEXT STAGE IF AVAILABLE
function MoveContstoNextStage($g,$pipeID,$tm){
    
    $getNstage = getNextStageIdPipe($g['stage_id'],$pipeID);
        if($getNstage > 0) {
    
        // if no more step then move to the next stage and next step in the new stage

            if($g['trigger_type'] == 1){
                $timeTU = $g['duration_b_stage'];
                
                if(time() >= $timeTU){
                    
                    moveContactstothenestStagesCronID($g['cron_id'],$pipeID,$getNstage);
                    
                    $getNaction = getNextStageStepActionIdPipe($g['action_id'],$g['step_id'],$getNstage,$pipeID);
                    $getNstep = getNextStageStepIdPipe($g['step_id'],$getNstage,$pipeID);
                    $getNstage = getNextStageIdPipe($getNstage,$pipeID);
            
            
                    if($getNstep > 0){
                        updatetriggertypeInCron($pipeID,2,$g['cron_id'],$tm);
                    }elseif($getNstage > 0){
                        updatetriggertypeInCron($pipeID,1,$g['cron_id'],$tm);
                    }else{
                        updatetriggertypeInCron($pipeID,3,$g['cron_id'],$tm);
                    }
                    
                    echo "No more Step but moved to the next stage :- $getNstage and new step <br>";
                    
                }
            }elseif($g['trigger_type'] == 2){
                MoveContstoNextStep($g,$pipeID,$tm);
            }elseif($g['trigger_type'] == 3){
                MoveContacttoNextAction($g,$pipeID,$tm);
            }
            
        }else{
            
            echo "Cron Finished <br> ";
        }
    
}

// MOVE TO NEXT ACTION IF AVAILABEL
function MoveContacttoNextAction($g,$pipeID,$tm){
    
    $timeTU = $g['duration_b_action'];
    
    $getNaction = getNextStageStepActionIdPipe($g['action_id'],$g['step_id'],$g['stage_id'],$pipeID);
    
    $confirmAct = getifcronalreadyrunforactionCRONID($getNaction,$pipeID,$g['cron_id'],$g['step_id'],$g['stage_id']);
    
    if($g['trigger_type'] == 3){
        
        if(time() >= $timeTU && $confirmAct == 0){
            
            unset($g['action_id']);
            $g['action_id'] = $getNaction;
            $AcType = getJustSingleActionData($getNaction);
            
            if($AcType == 5){
                $waitTimes = getSingleWaitActionData($getNaction);
                
                $Wtime = array(
                    'minutes' => $waitTimes['minutes'],
                    'hours' => $waitTimes['hours'],
                    'days' => $waitTimes['days'],
                    'time' => $waitTimes['time'],
                );
                
                $Wtm = getTimeConversion($Wtime);
                
                $tm = $Wtm;
            }else{
                
                $tm = $tm;
                
            }
                
            insertcronrecordsCRONID($pipeID,$g,$tm);
         
            
            updateActionToPipelineCRONID($getNaction,$g['cron_id'],$pipeID);
            
            $getNaction = getNextStageStepActionIdPipe($getNaction,$g['step_id'],$g['stage_id'],$pipeID);
            $getNstep = getNextStageStepIdPipe($g['step_id'],$g['stage_id'],$pipeID);
            $getNstage = getNextStageIdPipe($g['stage_id'],$pipeID);
            
            if($getNaction > 0){
                updatetriggertypeInCron($pipeID,3,$g['cron_id'],$tm);
            }elseif($getNstep > 0){
                updatetriggertypeInCron($pipeID,2,$g['cron_id'],$tm);
            }elseif($getNstage > 0){
                updatetriggertypeInCron($pipeID,1,$g['cron_id'],$tm);
            }
            
        }
            
    
    }elseif($g['trigger_type'] == 1){
        MoveContstoNextStage($g,$pipeID,$tm);
    }elseif($g['trigger_type'] == 2){
        MoveContstoNextStep($g,$pipeID,$tm);
    }
    
    
} 

// GET FIRST ACTION IF NOT SET
function updatethefirstactioninstep($g,$pipeID,$tm){
    
     // Check if pipeline > stage > step > has action else move to the next step
    $getFirstAct = GetStageStepFirstAction($g['step_id'],$g['stage_id'],$pipeID);
    
    unset($g['action_id']);
    $g['action_id'] = $getFirstAct;
    insertcronrecordsCRONID($pipeID,$g,$tm);
    
    $AcType = getJustSingleActionData($getFirstAct);
            
    if($AcType == 5){
        $waitTimes = getSingleWaitActionData($getFirstAct);
        
        $Wtime = array(
            'minutes' => $waitTimes['minutes'],
            'hours' => $waitTimes['hours'],
            'days' => $waitTimes['days'],
            'time' => $waitTimes['time'],
        );
        
        $Wtm = getTimeConversion($Wtime);
        
        $tm = $Wtm;
    }else{
        
        $tm = $tm;
        
    }
    
    // Then update the first action in the contact to pipeline table
    updateActionToPipelineCRONID($getFirstAct,$g['cron_id'],$pipeID);
    
    $getNaction = getNextStageStepActionIdPipe($getFirstAct,$g['step_id'],$g['stage_id'],$pipeID);
    $getNstep = getNextStageStepIdPipe($g['step_id'],$g['stage_id'],$pipeID);
    $getNstage = getNextStageIdPipe($g['stage_id'],$pipeID);
    
    if($getNaction > 0){
        updatetriggertypeInCron($pipeID,3,$g['cron_id'],$tm);
    }elseif($getNstep > 0){
        updatetriggertypeInCron($pipeID,2,$g['cron_id'],$tm);
    }elseif($getNstage > 0){
        updatetriggertypeInCron($pipeID,1,$g['cron_id'],$tm);
    }
    
}


// TIME AND DATE CONVERSION FUNCTIONS
function getTimeConversion($stepD){
    
    if( $stepD['minutes'] != 0 ){
        $tm = toSeconds(0,0,$stepD['minutes'],0);
    }elseif( ($stepD['minutes'] == 0 ) && ($stepD['hours'] != 0) ){
        $tm = toSeconds(0,$stepD['hours'],0,0);
    }elseif( ($stepD['minutes'] == 0) && ($stepD['hours'] == 0) &&  $stepD['days'] != 0 ){
        $tm = toSeconds($stepD['days'],0,$stepD['minutes'],0);
    }elseif( ($stepD['minutes'] == 0) && ($stepD['hours'] == 0) &&  $stepD['days'] == 0 && $stepD['time'] != 0){
        $tm = timeToSecond($stepD['time']);
    }else{
        $tm = toSeconds(0,0,1,0);
    }
    
    return $tm;
}

// CONVERT TIME TO SECONDS
function toSeconds($days, $hours, $minutes, $seconds) {
    return ($days * 86400) + ($hours * 3600) + ($minutes * 60) + $seconds;
}

// OTHER CONVERSION TO SECONDS
function timeToSecond($time){
    $time_parts = explode(":",$time);
    $seconds= ($time_parts[0]*86400) + ($time_parts[1]*3600) + ($time_parts[2]*60) + $time_parts[3] ; 
    return $seconds;
}


// RINGLESS VOICE MAIL API
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
        return $result;
    }else{
        return $result;
    }
    
    curl_close($ch);
    
}

// PHONE NUMBER
function PhoneNumberFormat($phoneNumber) {
    
    $phoneNumber = preg_replace('/[^0-9]/','',$phoneNumber);

    if(strlen($phoneNumber) > 10) {
		
        $countryCode = substr($phoneNumber, 0, strlen($phoneNumber)-10);
        $areaCode = substr($phoneNumber, -10, 3);
        $nextThree = substr($phoneNumber, -7, 3);
        $lastFour = substr($phoneNumber, -4, 4);

        $phoneNumber = '+'.$countryCode.$areaCode.$nextThree.$lastFour;
		
    }
    else if(strlen($phoneNumber) == 10) {
		
		$countryCode = 1;
        $areaCode = substr($phoneNumber, 0, 3);
        $nextThree = substr($phoneNumber, 3, 3);
        $lastFour = substr($phoneNumber, 6, 4);

        $phoneNumber = '+'.$countryCode.$areaCode.$nextThree.$lastFour;
		
    }
    else{
		
        $nextThree = substr($phoneNumber, 0, 3);
        $lastFour = substr($phoneNumber, 3, 4);

        $phoneNumber = $nextThree.$lastFour;
		
    }

    return $phoneNumber;
    
}

