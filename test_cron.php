<?php 

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
//$root=$root.'/newpropertysalers/';
require_once($root.'/config.php');
require_once('assets/init.php');

$allActivepipe = listAllPipelineForCron();
foreach($allActivepipe as $pipe)
{
	//pre($pipe);
	$pipeStages = GetPipeLineStages($pipe['pipe_id']);
	foreach($pipeStages as $stl){
		//pre($stl);exit;
		if($stl['stage_goal'] == 0){}
		$pipesteps = getPipelineSteps($stl['pipe_id'], $stl['id']);
		
		foreach($pipesteps as $pstl){
			//pre($pstl);
			$stepActions = GetAllActionInAStep($pstl['id']);
			foreach($stepActions as $stpAction){
				//pre($stpAction);
				//echo $stpAction['id'];
				$singleInfo = GetSingleStepActDetails($stpAction['type']);
				//echo $stl['pipe_id'].','.$stl['id'].','.$stpAction['step_id'].','.$stpAction['action_no'];
				//exit;
				$StepDelayDetails = GetSingleStepDelay($stl['pipe_id'],$stl['id'],$stpAction['step_id'],$stpAction['action_no']);
				//echo $stl['pipe_id'].', '.$stl['id'].','.$stpAction['action_no'];
				//pre($StepDelayDetails);
				//exit;
				$days = $StepDelayDetails['days'];
				if($days>0){
				$date = date('Y-m-d', strtotime($StepDelayDetails['date'])).' '.$StepDelayDetails['time'];
				$date = $StepDelayDetails['date'];				
				//echo $StepDelayDetails['time'];
				//echo time();
				echo '<br/>';
				$datetime1 = new DateTime($date);
				$finaldate =  $datetime1->format('Y-m-d h:i:s A');
				$finalexutiondate = date('Y-m-d h:i:s A', strtotime($finaldate. ' + '.$days.' days'));
				}else
				{
				$datetime1 = new DateTime();
				$finalexutiondate =  $datetime1->format('Y-m-d h:i:s A');
				}
				echo '<br/>';
				$datetime2 = new DateTime();
				$todatDate = $datetime2->format('Y-m-d h:i:s A');

				//$difference = $datetime1->diff($datetime2);
				echo '<br/>';
				//echo 'Difference: '.$difference->y.' years, '.$difference->m.' months, '.$difference->d.' days';
				//   echo '<br/>';
				//echo $noofdays = date('l', strtotime($date));
				//echo 'type => '.$stpAction['type'].' pipe_id=>'.$stl['pipe_id'].', '.$stl['id'].','.$stpAction['action_no'].'<br/>';
				//echo 'days =>'.$StepDelayDetails['days'].' == 0 &&'. $StepDelayDetails['time'].' == 0 &&'. $StepDelayDetails['hours'].' == 0 &&'. $StepDelayDetails['minutes'];
				//exit;
				echo $stpAction['type'].'=>>'.$finalexutiondate." == ".$todatDate;
				echo '<br/>';
				//exit;
				if($stpAction['type'] == 3 && $finalexutiondate >= $todatDate){			
					$step_id = $stpAction['step_id'];
					$action_id = $stpAction['id'];
					
					//echo $stl['pipe_id'].', '.$stl['id'].','.$stpAction['action_no'].'<br/>';
					//echo '<br/>';
					$contacts = GetPipeLineStagesStepsindiviualcontact($stl['pipe_id'], $stl['id'], $step_id);
					//pre($contacts);
					global $sqlConnect;
					$table='pipeline_cron_logs';
					$is_created = date('Y-m-d H:i');
					$start_time = date('Y-m-d H:i');
					$end_time = date('Y-m-d H:i');
					$contact_in_ques = count($contacts);
					if($contact_in_ques>0){
					$options = array( 
									"id" => '',
									"pipe_id" => $pipe['pipe_id'],
									"stage_id" => $stl['id'],
									"step_id" => $step_id,
									"action_id" => $action_id,
									"contact_in_ques" => $contact_in_ques,
									"start_time" => $start_time,
									"end_time" => $end_time,
									"is_active" => 1,
									"created_at" => $is_created
									);
					//pre($options);
					$query1 = insertRow($table, $options);
					$sqlConnect->query($query1);
					}
					$query = getJustSingleEmailActionDetails($step_id,$action_id);
					//pre($query);
					$value = array(
						'status' => 200,
						'subject' => $query['subject'],
						'emailfrom' => $query['email_from'],
						'message' => $query['message'],
					);
					$msg = '';
					if(count($contacts)>0){	
					//die;
					$sent = 0;
					foreach($contacts as $contact)
					{
						$sent = 0;
						//pre($contact);
						$options = array( "id" => $contact['contact_id']);
						//pre($options);
						$contactsDetails = getContact($options);
						$user_id = 44;
						$to = $contactsDetails['email'];
						$emailFrom = $query['email_from'];
						$subject = $query['subject'];
						$message = $query['message'];
						//pre($contactsDetails);
						//echo $to.','.$emailFrom.','.$subject.','.$message.'<br/>';
						$mailsent = sendAutomail($to,$emailFrom,$subject,$message,$user_id);
						//pre($resul);
						if($mailsent) $sent=1;
						
					}
					//echo 'step_id >> '.$stpAction['step_id'].'sent >> '.$sent.'pipeid>> '.$pipe['pipe_id'].'stage_id =>'.$stl['id'].'action_id =>'. $action_id;
					if($sent ===1)
					{	
						$where = ['step_id' => $stpAction['step_id'],'pipe_id' => $pipe['pipe_id'],'action_id' => $action_id, 'stage_id' => $stl['id']];
						$noptions = [							
									"end_time" => $end_time,
									"is_active" => 2									
									];					
           
						$update_query = updateRow($table, $noptions, $where);						
						$sqlConnect->query($update_query);
					}
					
					}
					else
					{
						$msg="No Contact in Pipeline";
					}
					
				}elseif($stpAction['type'] == 2 && $finalexutiondate >= $todatDate){					
					$step_id = $stpAction['step_id'];
					$action_id = $stpAction['id'];
					
					echo $stl['pipe_id'].', '.$stl['id'].','.$stpAction['action_no'].'<br/>';exit;
					
					$contacts = GetPipeLineStagesStepsindiviualcontact($stl['pipe_id'], $stl['id'], $stpAction['action_no']);
					//pre($contacts);
					global $sqlConnect;
					$table='pipeline_cron_logs';
					$is_created = date('Y-m-d H:i');
					$start_time = date('Y-m-d H:i');
					$end_time = date('Y-m-d H:i');
					$contact_in_ques = count($contacts);
					$options = array( 
									"id" => '',
									"pipe_id" => $pipe['pipe_id'],
									"stage_id" => $stl['id'],
									"step_id" => $step_id,
									"action_id" => $action_id,
									"contact_in_ques" => $contact_in_ques,
									"start_time" => $start_time,
									"end_time" => $end_time,
									"is_active" => 1,
									"created_at" => $is_created
									);
					//pre($options);
					$query1 = insertRow($table, $options);
					$sqlConnect->query($query1);
					
					$query = getJustSingleEmailActionDetails($step_id,$action_id);
					//pre($query);
					$value = array(
						'status' => 200,
						'subject' => $query['subject'],
						'emailfrom' => $query['email_from'],
						'message' => $query['message'],
					);
					$msg = '';
					if(count($contacts)>0){	
					//die;
					$sent = 0;
					foreach($contacts as $contact)
					{
						$sent = 0;
						//pre($contact);
						$options = array( "id" => $contact['contact_id']);
						//pre($options);
						$contactsDetails = getContact($options);
						$user_id = 44;
						$to = $contactsDetails['email'];
						$emailFrom = $query['email_from'];
						$subject = $query['subject'];
						$message = $query['message'];
						//pre($contactsDetails);
						//echo $to.','.$emailFrom.','.$subject.','.$message.'<br/>';
						$mailsent = sendAutomail($to,$emailFrom,$subject,$message,$user_id);
						//pre($resul);
						if($mailsent) $sent=1;
						
					}
					if($sent ===1)
					{	
						$where = ['id' => 1];
						$noptions = [							
									"end_time" => $end_time,
									"is_active" => 2									
									];					
           
						$update_query = updateRow($table, $noptions, $where);						
						$sqlConnect->query($update_query);
					}
					
					}
					else
					{
						$msg="No Contact in Pipeline";
					}
					
				}
			}
		}
	}
}
echo $msg;
?>