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
                'id' =>  $g['cron_id'],
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
                    
                 updateNextActionToPipeline($getFirstAct,$g['id'],$pipeID);
                    
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
                  
                  updateNextActionToPipeline($getNaction,$g['id'],$pipeID);
                   
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
    
    // $confirmAct = getifcronalreadyrunforaction($getNaction,$pipeID,$g['contact_id'],$g['step_id'],$g['stage_id']);
    $confirmAct = 0;
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

