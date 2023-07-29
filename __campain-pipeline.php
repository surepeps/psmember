<?php 

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');


// Create New Pipeline
if( isset($_POST['action']) && ($_POST['action'] == "submitPipeLineF") ) {
    
    if(isset($_POST['pipeName'])){
        $pipeName = $_POST['pipeName'];
    }
    
    if(isset($_POST['pipeDesc'])){
        $pipeDesc = $_POST['pipeDesc'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
    
    if(isset($_POST['pipeLCN'])){
        $pipeLCN = $_POST['pipeLCN'];
    }
    
    if(isset($_POST['pipeTimeZone'])){
        $pipeTimeZone = $_POST['pipeTimeZone'];
    }
    
    if(isset($_POST['pipeType'])){
        $pipeType = $_POST['pipeType'];
    }
    
    if(isset($_POST['pipeTarget'])){
        $pipeTarget = $_POST['pipeTarget'];
    }
    
    if($pipeType == 0){
        $typ = 'lead';
    }else if($pipeType == 2){
        $typ = 'deal';
    }
    
     $status = 1;
    
    if($user_id === $wo['user']['user_id'] ){
        
        $value = array(
            'pipeName'=> $pipeName,
            'pipe_code' => "pipe_".substr(md5(mt_rand()), 0, 7),
            'pipeDesc' => $pipeDesc,
            'pipeLCN' => $pipeLCN,
            'pipeTimeZone' => $pipeTimeZone,
            'pipeType' => $pipeType,
            'type' => $typ,
            'pipeTarget' => $pipeTarget,
            'user_id' => $user_id,
            'status' => $status  
        );
        
        // Insert Pipeline details into database.....
        $query = CreateNewPipeLine($value);
        
        if($query > 0){
        
           $data = array(
        		'status' => 200,
        		'pipe_id' => $query,
        		'message' => 'Successfully Created pipeline',
        	); 
        	
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, while processing your request',
        	);
        	
        }
    
    }else{
        
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Unautorized Action',
        	);
        	
    }
    
    	
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
}

// Edit Pipeline
if( isset($_POST['action']) && ($_POST['action'] == "editPipeLineF") ) {
    
    if(isset($_POST['pipeName'])){
        $pipeName = $_POST['pipeName'];
    }
    
    if(isset($_POST['pipeDesc'])){
        $pipeDesc = $_POST['pipeDesc'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
    
    if(isset($_POST['pipeLCN'])){
        $pipeLCN = $_POST['pipeLCN'];
    }
    
    if(isset($_POST['pipeTimeZone'])){
        $pipeTimeZone = $_POST['pipeTimeZone'];
    }
    
    if(isset($_POST['pipeType'])){
        $pipeType = $_POST['pipeType'];
    }
    
    if(isset($_POST['pipeTarget'])){
        $pipeTarget = $_POST['pipeTarget'];
    }
    
    if(isset($_POST['pipe_id'])){
        $pipe_id = $_POST['pipe_id'];
    }
    
    if($pipeType == 0){
        $typ = 'lead';
    }else if($pipeType == 2){
        $typ = 'deal';
    }
    
     $status = 1;
     
    if($user_id === $wo['user']['user_id'] ){
    
        $value = array(
            'pipeName'=> $pipeName,
            'pipeDesc' => $pipeDesc,
            'pipeLCN' => $pipeLCN,
            'pipeTimeZone' => $pipeTimeZone,
            'pipeType' => $pipeType,
            'type' => $typ,
            'pipeTarget' => $pipeTarget,
            'user_id' => $user_id,
            'status' => $status  
        );
        
        // Update Pipeline details into database.....
        $query = EditPipeLine($value,$pipe_id,$user_id);
        
        
        if($query){
        
           $data = array(
        		'status' => 200,
        		'time_zone' => getTimeZoneNameSingle($pipeTimeZone),
        		'message' => 'Pipeline Successfully updated',
        	); 
        	
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, while processing your request',
        	);
        	
        }
    
    }else{
        
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Unautorized Action',
        	);
        	
    }
    
    	
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
}

// GET PIPELINE ZONE 
if( isset($_POST['action']) && ($_POST['action'] == "loadZone") ) {
    
    if(isset($_POST['pipe_id'])){
        $pipe_id = $_POST['pipe_id'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
    
    if($user_id === $wo['user']['user_id'] ){
    
    $query = getPipeZoneStatus($pipe_id);
    
        if($query['no'] > 0){
        
           $data = array(
        		'status' => 200,
        		'zone' => $query['zone'],
				'pmarket' => $query['market'],
        		'message' => 'loaded',
        	); 
        	
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, while processing your request',
        	);
        	
        }
        
    }else{
        
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Unautorized Action',
        	);
        	
    }
    
    	
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
}

// SET PIPELINE ZONE
if( isset($_POST['action']) && ($_POST['action'] == "SetloadZone") ) {
    
    if(isset($_POST['pipe_id'])){
        $pipe_id = $_POST['pipe_id'];
    }
    
    if(isset($_POST['pZone'])){
        $pZone = $_POST['pZone'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
    
    // If the zone is off
    if($pZone == 2){
        
        $value = array(
            'zone'=> $pZone,
    		'market' => 0
        );
        
    }else{
        
        $value = array(
            'zone'=> $pZone,
    		'market' => 0
        );
        
    }
    
    

    
    if($user_id === $wo['user']['user_id'] ){
        
        $smsRunAllow = countnumbersofSMSActionForApipeline($pipe_id);
        $checkiflcnisavailable = getsinglelcnnumberbypipeId($pipe_id);
        if( ($smsRunAllow > 0 && $checkiflcnisavailable != "") || ($smsRunAllow == 0 && $checkiflcnisavailable != "") || ($smsRunAllow == 0 && $checkiflcnisavailable == "") ){
        
            $query = setPipeZoneStatus($pipe_id,$value);
            
            if($query){
                
                if($pZone == 0){
                    
                    makeLCNUNAvailable($pipe_id,$user_id);
                    clearCronContRec($pipe_id);
                    
                }else{
                    
                    makeLCNAvailable($pipe_id,$user_id);
                    
                }
    
               $data = array(
            		'status' => 200,
            		'zone' => $pZone,
            		'message' => 'loaded Set',
            	); 
            	
            }else{
                
                $data = array(
            		'status' => 400,
            		'message' => 'Error!, while processing your request',
            	);
            	
            }
            
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'SMS Actions are found but no LCN to trigger the messages',
        	);
        	
        }
        
        
        
    }else{
        
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Unautorized Action',
        	);
        	
    }
    
    
    	
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
}

// SET ZONE AND MARKETING
if( isset($_POST['action']) && ($_POST['action'] == "SetloadZoneMarket") ) {
    
    if(isset($_POST['pipe_id'])){
        $pipe_id = $_POST['pipe_id'];
    }
    
	if(isset($_POST['pMarket'])){
        $pMarket = $_POST['pMarket'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
	

	$value = array(
	    'zone'=> 1,
	    'market' => $pMarket
	);
		
    
    if($user_id === $wo['user']['user_id'] ){
        
        $smsRunAllow = countnumbersofSMSActionForApipeline($pipe_id);
        
        $checkiflcnisavailable = getsinglelcnnumberbypipeId($pipe_id);
        
        if( ($smsRunAllow > 0 && $checkiflcnisavailable != "") || ($smsRunAllow == 0 && $checkiflcnisavailable != "") || ($smsRunAllow == 0 && $checkiflcnisavailable == "") ){
    
            $query = setPipeZoneStatus($pipe_id,$value);
            
            if($query){
                
               $data = array(
            		'status' => 200,
            		'zone' => 1,
            		'pmarket' => $pMarket,
            		'message' => 'Market loaded Set',
            	); 
            	
            }else{
                
                $data = array(
            		'status' => 400,
            		'message' => 'Error!, while processing your request',
            	);
            	
            }
        
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'SMS Actions are found but no LCN to trigger the messages',
        	);
            
        }
        
    }else{
        
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Unautorized Action',
        	);
        	
    }
    
    
    	
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
}

// DUPLICATE PIPELINE 
if( isset($_POST['action']) && ($_POST['action'] == "DuplicatePipe") ) {
    
    if(isset($_POST['pipe_id'])){
        $pipe_id = $_POST['pipe_id'];
    }
    
    if(isset($_POST['name'])){
        $Gname = $_POST['name'];
        $name = "Copy of ".$Gname;
    }
    
    if($pipe_id > 0){
        $pipe_code = "pipe_".substr(md5(mt_rand()), 0, 7);
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
    
    if($user_id === $wo['user']['user_id'] ){
        
        $query = DuplicatePipeLine($pipe_id,$name,$pipe_code);
        
        if($query > 0){
        
           $data = array(
        		'status' => 200,
        		'message' => 'Pipeline Successfully Duplicated',
        	); 
        	
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, while processing your request',
        	);
        	
        }
        
    }else{
        
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Unautorized Action',
        	);
        	
    }
    
    
    	
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
}

// DELETE PIPELINE 
if( isset($_POST['action']) && ($_POST['action'] == "DeletePipe") ) {
    
    if(isset($_POST['pipe_id'])){
        $pipe_id = $_POST['pipe_id'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
    
    if($user_id === $wo['user']['user_id'] ){
        
        $query = DeletePipeLine($pipe_id);
    
        if($query > 0){
        
           $data = array(
        		'status' => 200,
        		'message' => 'Pupeline Successfully Duplicated',
        	); 
        	
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, while processing your request',
        	);
        	
        }
    
    }else{
        
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Unautorized Action',
        	);
        	
    }
    
    
    
    
    	
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
}

// ADD NEW STAGE TO PIPELINE
if( isset($_POST['action']) && ($_POST['action'] == "AddNewStagePipe") ) {
    
    if(isset($_POST['pipe_id'])){
        $pipe_id = $_POST['pipe_id'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
    
    if($user_id === $wo['user']['user_id'] ){
        
        $query = CreateNewStagePipeLine($user_id,$pipe_id);
        extract($query);
        
        if($s_id){
        
           $data = array(
        		'status' => 200,
        		'pipe_id' => $pipe_id,
        		's_id' => $s_id,
        		's_no' => $s_no,
        		'message' => 'Pipeline Stage Successfully Duplicated',
        	); 
        	
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, while processing your request',
        	);
        	
        }
    
    }else{
        
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Unautorized Action',
        	);
        	
    }
    
    
    
    
    	
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
}

// GET STAGE DETAILS FRO DELETE OR UPDATE 
if( isset($_POST['action']) && ($_POST['action'] == "GetStageDetails") ) {
    
    if(isset($_POST['pipe_id'])){
        $pipe_id = $_POST['pipe_id'];
    }
    
    if(isset($_POST['stage_id'])){
        $stage_id = $_POST['stage_id'];
    }
    
    if(isset($_POST['stage_no'])){
        $stage_no = $_POST['stage_no'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
    
    if($user_id === $wo['user']['user_id'] ){
        
        $query = GetSingleStageDetails($stage_id,$pipe_id);
    
        if($query){
        
           $data = array(
        		'status' => 200,
        		's_name' => $query['name'],
        		's_desc' => $query['description'],
        		's_s_g' => $query['stage_goal'],
        		's_w_g_m' => $query['stage_w_g_m'],
        		's_w_g_nm' => $query['stage_w_g_nm'],
        	); 
        	
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, while processing your request',
        	);
        	
        }
    
    }else{
        
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Unautorized Action',
        	);
        	
    }
    
    
    
    
    	
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
}

// GET STEP DETAILS FRO DELETE OR UPDATE 
if( isset($_POST['action']) && ($_POST['action'] == "GetStepDetails") ) {
    
    if(isset($_POST['step_id'])){
        $step_id = $_POST['step_id'];
    }
    
    if(isset($_POST['stage_id'])){
        $stage_id = $_POST['stage_id'];
    }
    
    if(isset($_POST['step_no'])){
        $step_no = $_POST['step_no'];
    }
    
    if(isset($_POST['pipe_id'])){
        $pipe_id = $_POST['pipe_id'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
    
    if($user_id === $wo['user']['user_id'] ){
        
        $query = GetSingleStageStepDetails($stage_id,$step_id,$pipe_id);
        $pipeline = GetSinglePipelimeDetails($pipe_id,$user_id);
        
        if($query){
        
           $data = array(
        		'status' => 200,
        		'stepName' => $query['name'],
        		'stepDays' => $query['days'],
        		'stepTime' => $query['time'],
        		'stepHours' => $query['hours'],
        		'stepMins' => $query['minutes'],
        		'stepTimeZone' => $pipeline['time_zone'],
        	); 
        	
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, while processing your request',
        	);
        	
        }
    
    }else{
        
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Unautorized Action',
        	);
        	
    }
    
    
    
    
    	
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
}

// UPDATE STAGE 
if( isset($_POST['action']) && ($_POST['action'] == "updateStageN") ) {


    if(isset($_POST['pipe_id'])){
        $pipe_id = $_POST['pipe_id'];
    }
    
    if(isset($_POST['stage_id'])){
        $stage_id = $_POST['stage_id'];
    }
    
    if(isset($_POST['stage_no'])){
        $stage_no = $_POST['stage_no'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
    
    if(isset($_POST['mystageName'])){
        $mystageName = $_POST['mystageName'];
    }
    
    if(isset($_POST['mystageDesc'])){
        $mystageDesc = $_POST['mystageDesc'];
    }
    
    if(isset($_POST['mystageGoal'])){
        $mystageGoal = $_POST['mystageGoal'];
    }
    
    if(isset($_POST['mystagewhenGM'])){
        $mystagewhenGM = $_POST['mystagewhenGM'];
    }
    
    if(isset($_POST['mystagewhenGNM'])){
        $mystagewhenGNM = $_POST['mystagewhenGNM'];
    }
    
    $value = array(
        'name' => $mystageName,
        'description' => $mystageDesc,
        'stage_goal' => $mystageGoal,
        'stage_w_g_m' => $mystagewhenGM,
        'stage_w_g_nm' => $mystagewhenGNM
    );
    
    if($user_id === $wo['user']['user_id'] ){
        
        $query = UpdateStageDetails($stage_id,$pipe_id,$value);
    
        if($query){
        
           $data = array(
        		'status' => 200,
        		'message' => 'Successfully Updated Stage'
        	); 
        	
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, while processing your request',
        	);
        	
        }
    
    }else{
        
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Unautorized Action',
        	);
        	
    }
    
    
    
    
    	
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
}

// UPDATE STEP 
if( isset($_POST['action']) && ($_POST['action'] == "updateStepN") ) {


    if(isset($_POST['pipe_id'])){
        $pipe_id = $_POST['pipe_id'];
    }
    
    if(isset($_POST['stage_id'])){
        $stage_id = $_POST['stage_id'];
    }
    
    if(isset($_POST['step_id'])){
        $step_id = $_POST['step_id'];
    }
    
    if(isset($_POST['step_no'])){
        $step_no = $_POST['step_no'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
    
    if(isset($_POST['stepName'])){
        $stepName = $_POST['stepName'];
    }
    
    if(isset($_POST['stepDays'])){
        $stepDays = $_POST['stepDays'];
    }
    
    if(isset($_POST['stepTime'])){
        $stepTime = $_POST['stepTime'];
    }
    
    if(isset($_POST['stepHours'])){
        $stepHours = $_POST['stepHours'];
    }
    
    if(isset($_POST['stepMins'])){
        $stepMins = $_POST['stepMins'];
    }
    
    $value = array(
        'name' => $stepName,
        'days' => $stepDays,
        'time' => $stepTime,
        'hours' => $stepHours,
        'minutes' => $stepMins
    );
    
    if($user_id === $wo['user']['user_id'] ){
        
        $query = UpdateStepDetails($stage_id,$step_id,$value);
    
        if($query){
        
           $data = array(
        		'status' => 200,
        		'message' => 'Successfully Updated Step'
        	); 
        	
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, while processing your request',
        	);
        	
        }
    
    }else{
        
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Unautorized Action',
        	);
        	
    }   
    	
    header("Content-type: application/json");
    echo json_encode($data);
    die();    
    
}

// SHOW STAGE STEPS  
if( isset($_POST['action']) && ($_POST['action'] == "ShowStageSteps") ) {


    if(isset($_POST['pipe_id'])){
        $pipe_id = $_POST['pipe_id'];
    }
    
    if(isset($_POST['stage_id'])){
        $stage_id = $_POST['stage_id'];
    }
    
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
    
    
    if($user_id === $wo['user']['user_id'] ){
        
        $query = GetStageSteps($stage_id,$pipe_id);
        
        foreach($query as $ste){ ?>
        
        <div style="position:static !important;" class="col-xl-3 col-lg-3 col-md-3 col-sm-12 dropRow" data-action-id="<?= $ste['id'] ?>" id="stepss">
			<div class="stgmn ste">
				<div class="d-flex justify-content-between w-100 tostg">
					<div class="d-flex align-items-center stgcnt">
						<span class="nomnm">Step</span> <span class="ms-2 nom"><?= $ste['step_no'] ?></span>
					</div>
					
					<div class="nvstg">
					    
						<a href="javascript:void(0)" onclick="ShowStepModal(<?= $ste['step_no'] ?>,<?= $ste['stage_id'] ?>,<?= $ste['id'] ?>)" data-backdrop="static" data-keyboard="false"><i class="fas fa-ellipsis-h"></i></a>
					</div>
				</div>
				
				<div class="midstg mb-5">
					<h4 class="text-uppercase mb-4 " id="step_<?= $ste['id'] ?>_N"><?= $ste['name'] ?></h4>
					<p></p>
				</div>
				
				<div class="collapse" id="collapseExample_<?= $ste['id'] ?>">
  					<div class="card12 card-bod21y">
						<div class="text-center drpin clps">
                			<ul class="p-0 m-0" id="stepListAction">
                			    
                			    <?php $actStep = GetAllActionInAStep($ste['id']); foreach($actStep as $sat) { $asd = GetSingleStepActDetails($sat['type']); ?>
                            			 
                    			    <li class="d-block">
        								<a class="p-3 d-flex w-100 justify-content-between align-items-center" data-step-id="<?= $sat['step_id'] ?>" data-step-actionid="<?= $sat['type'] ?>" data-action-no="<?= $sat['action_no'] ?>" href="javascript:void(0)" onclick="getAllModalActionDetailsF(<?= $sat['step_id'] ?>,<?= $sat['id'] ?>,<?= $sat['type'] ?>)" data-backdrop="static" data-keyboard="false">
        									<div class="">										
        										<p><i class="<?= $asd['icon'] ?>"></i> <?= $asd['name'] ?></p>
        									</div>
        									
        									<div class="">
        										<i class="fas fa-ellipsis-v"></i>
        									</div>
        								</a>
        							</li>
        							
        						<?php } ?>
    							
                			</ul>
		                </div>
					</div>
				</div>
				
				<div class="text-center position-relative btmstg">
					<p class="m-0"><span id="actionNo" ><?= $ste['actions'] ?></span> Actions</p>
					<a class="openstep" id="stepOpenAndClose" data-toggle="collapse" href="#collapseExample_<?= $ste['id'] ?>" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-chevron-down"></i></a>
				</div>
			</div>
		</div>
        
        
        
        
        
    <?php } 
    
    ?>
    <script>
    
            $(".openstep").click(function (e) {
                
                var stagesh = $(this).attr("data-stid");
                
                $(this).parent().parent().addClass("active");
                $(this).find("#stepOpenAndClose").html('<i class="fa fa-chevron-down"></i>');
                
                $(".openstep").not(this).parent().parent().removeClass("active");
                $(".openstep").not(this).find("#stepOpenAndClose").html('<i class="fa fa-chevron-up"></i>');
                
               
                
                
            });
            
            $(".dragA").draggable({
                
                cursor: 'move',
                helper: 'clone'
                
            });
            
            // DROP FOR STEP ACTIONS...
            $(".dropRow, .collectRow").droppable({
                drop: function (ev, ui) {
             
                    var draggableId = ui.draggable.attr("id");
                    var dragmodal = ui.draggable.attr("data-modal-name");
                    var droppableId = $(this).attr("data-action-id");
                    
                    var thisv = $(this);
                    
                    var stagenid = $("#stageID").val();
                    
                    // Insert Action under the step
                    $.ajax({
                      type:"POST",
                      url: websiteUrl + "/"+endpoint,
                      data: {
                		action : "PutActionUnderStep",
                		pipe_id : pipeId,
                		step_id: droppableId,
                		stage_id: stagenid,
                		action_id: draggableId,
                		user_id : user_idr
                	  },
                      beforeSend: function() {
                            run_waitMe($('div #contnet'), 1, 'win8');
                      },
                
                        success: function (data) {
                            $('div #contnet').waitMe('hide');
                            
                            if(data.status == 200){
                                // get new step action id
                                var acId =  data.myactionId;
                                var noactt = data.actionData;
                                
                                if(draggableId == 1){
                                    var mytemp = smsTextStepActionTemplate(droppableId,acId,draggableId);
                                }else if(draggableId == 2){
                                    var mytemp = ringlessVoiceMailStepActionTemplate(droppableId,acId,draggableId);
                                }else if(draggableId == 3){
                                    var mytemp = emailStepActionTemplate(droppableId,acId,draggableId);
                                }else if(draggableId == 4){
                                    var mytemp = directEmailStepActionTemplate();
                                }else if(draggableId == 5){
                                    var mytemp = waitStepActionTemplate(droppableId,acId,draggableId);
                                }
                                
                                thisv.find("#stepListAction").append(mytemp);
                                thisv.find("#actionNo").text(noactt);
                                
                                getAllModalActionDetailsF(droppableId,acId,draggableId);
                                
                            }else{
                                // Show error message
                                toastr_call("warning",data.message);
                                
                            }
                            
                        }
                    });
                    
                }
            });
            
    </script>
    
    <?php
    
    } 
    
}

// DELETE STAGE 
if( isset($_POST['action']) && ($_POST['action'] == "deleteStageN") ) {
    
   if(isset($_POST['pipe_id'])){
        $pipe_id = $_POST['pipe_id'];
    }
    
    if(isset($_POST['stage_id'])){
        $stage_id = $_POST['stage_id'];
    }
    
    if(isset($_POST['stage_no'])){
        $stage_no = $_POST['stage_no'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
    
    
    if($user_id === $wo['user']['user_id'] ){
        
        $query = DeletePipeLineStage($stage_id,$stage_no,$pipe_id);
    
        if($query > 0){
        
           $data = array(
        		'status' => 200,
        		'message' => 'Stage Successfully Deleted',
        	); 
        	
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, while processing your request',
        	);
        	
        }
    
    }else{
        
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Unautorized Action',
        	);
        	
    }
    
    
    
    
    	
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
}

// DELETE STEP 
if( isset($_POST['action']) && ($_POST['action'] == "deleteStepN") ) {
    
   if(isset($_POST['pipe_id'])){
        $pipe_id = $_POST['pipe_id'];
    }
    
    if(isset($_POST['stage_id'])){
        $stage_id = $_POST['stage_id'];
    }
    
    if(isset($_POST['step_id'])){
        $step_id = $_POST['step_id'];
    }
    
    if(isset($_POST['step_no'])){
        $step_no = $_POST['step_no'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
    
    
    if($user_id === $wo['user']['user_id'] ){
        
        $query = DeletePipeLineStep($stage_id,$step_id,$step_no,$pipe_id);
        extract($query);
        if($query){
        
           $data = array(
        		'status' => 200,
        		'TstepsNo' => $totalSteps,
        		'message' => 'Step Successfully Deleted',
        	); 
        	
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, while processing your request',
        	);
        	
        }
    
    }else{
        
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Unautorized Action',
        	);
        	
    }
    
    
    
    
    	
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
}

// CREATE STEP FOR STAGE
if( isset($_POST['action']) && ($_POST['action'] == "AddNewStepPipe") ) {
    
    if(isset($_POST['pipe_id'])){
        $pipe_id = $_POST['pipe_id'];
    }
    
    if(isset($_POST['stage_id'])){
        $stage_id = $_POST['stage_id'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
    
    if($user_id === $wo['user']['user_id'] ){
        
        $query = CreateNewStepPipeLine($user_id,$pipe_id,$stage_id);
        extract($query);
        
        if($step_id){
        
           $data = array(
        		'status' => 200,
        		'pipe_id' => $pipe_id,
        		'step_id' => $step_id,
        		'step_no' => $step_no,
        		'TstepsNo' => $totalSteps,
        		'message' => 'Pipeline Step Successfully Created',
        	); 
        	
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, while processing your request',
        	);
        	
        }
    
    }else{
        
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Unautorized Action',
        	);
        	
    }
    
    
    
    
    	
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
}

// CREATE ACTION WHEN DRAGGED
if( isset($_POST['action']) && ($_POST['action'] == "PutActionUnderStep") ) {
    
    if(isset($_POST['pipe_id'])){
        $pipe_id = $_POST['pipe_id'];
    }
    
    if(isset($_POST['step_id'])){
        $step_id = $_POST['step_id'];
    }
    
    if(isset($_POST['stage_id'])){
        $stage_id = $_POST['stage_id'];
    }
    
    if(isset($_POST['action_id'])){
        $s_action_id = $_POST['action_id'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
    
    if($user_id === $wo['user']['user_id'] ){
        
        $query = CreateStepAction($step_id,$stage_id,$pipe_id,$s_action_id);
        extract($query);
        
        if($myactionId > 0){
        
           $data = array(
        		'status' => 200,
        		'myactionId' => $myactionId,
        		'actionData' => $actionData,
        		'message' => 'Step Action Successfully Created',
        	); 
        	
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, while processing your request',
        	);
        	
        }
    
    }else{
        
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Unautorized Action',
        	);
        	
    }
    
    
    
    
    	
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
}

// GET ALL ACTIONS DETAILS
if( isset($_POST['action']) && ($_POST['action'] == "GetAllForModalActionsTypeDetails") ) {
    
    if(isset($_POST['step_id'])){
        $step_id = $_POST['step_id'];
    }
    
    if(isset($_POST['action_id'])){
        $action_id = $_POST['action_id'];
    }
    
    if(isset($_POST['type_id'])){
        $type_id = $_POST['type_id'];
    }
    
    if(isset($_POST['pipe_id'])){
        $pipe_id = $_POST['pipe_id'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
    
    if($user_id === $wo['user']['user_id'] ){
        
        if($type_id == 1){
            $query = getJustSingleSmsTextActionDetails($step_id,$action_id);
            
            $value = array(
        		'status' => 200,
        		'message' => $query['message'],
        	); 
        	
        }elseif($type_id == 2){
            $query = getJustSingleRinglessVoiceActionDetails($step_id,$action_id);
            
            $value = array(
        		'status' => 200,
        		'LCN' => $query['LCN'],
        		'audio_url' => $query['audio_url'],
        		'audio_type' => $query['audio_type'],
        		'title' => $query['title'],
        	);
        	
        }elseif($type_id == 3){
            
            $query = getJustSingleEmailActionDetails($step_id,$action_id);
            
            $value = array(
        		'status' => 200,
        		'subject' => $query['subject'],
        		'emailfrom' => $query['email_from'],
        		'message' => $query['message'],
        	); 
            
        }elseif($type_id == 4){
            
        }elseif($type_id == 5){
            
            $query = getJustSingleWaitActionDetails($step_id,$action_id);
            $queryt = GetSinglePipelimeDetails($pipe_id,$user_id);
            $value = array(
        		'status' => 200,
        		'days' => $query['days'],
        		'minutes' => $query['minutes'],
        		'hours' => $query['hours'],
        		'time' => $query['time'],
        		'timeZone' => $queryt['time_zone']
        	); 
        }
        
        
        if($query){
        
           $data = $value;
        	
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, while processing your request',
        	);
        	
        }
    
    }else{
        
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Unautorized Action',
        	);
        	
    }
    
    
    
    
    	
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
}

// DELETE (EMAIL) ACTION DETAILS  
if( isset($_POST['action']) && ($_POST['action'] == "deleteEmailActStepN") ) {
    
   if(isset($_POST['step_id'])){
        $step_id = $_POST['step_id'];
    }
    
    if(isset($_POST['action_id'])){
        $action_id = $_POST['action_id'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
    
    
    if($user_id === $wo['user']['user_id'] ){
        
        $query = DeleteEmailActionStep($step_id,$action_id);
    
        if($query > 0){
        
           $data = array(
        		'status' => 200,
        		'message' => 'Action Successfully Deleted',
        	); 
        	
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, while processing your request',
        	);
        	
        }
    
    }else{
        
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Unautorized Action',
        	);
        	
    }
    
    
    
    
    	
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
}

// UPDATE (EMAIL) ACTION DETAILS 
if( isset($_POST['action']) && ($_POST['action'] == "updateEmailActionStepN") ) {

    if(isset($_POST['step_id'])){
        $step_id = $_POST['step_id'];
    }
    
    if(isset($_POST['action_id'])){
        $action_id = $_POST['action_id'];
    }
    
    if(isset($_POST['subject'])){
        $subject = mysqli_real_escape_string($sqlConnect, $_POST['subject']);
    }
    
    if(isset($_POST['emailFrom'])){
        $emailFrom = mysqli_real_escape_string($sqlConnect, $_POST['emailFrom']);
    }
    
    if(isset($_POST['message'])){
        $message = mysqli_real_escape_string($sqlConnect, $_POST['message']);
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
    
    if($user_id === $wo['user']['user_id'] ){
        
        $vd = array(
            'email_from' => $emailFrom,
            'subject' => $subject,
            'message' => $message
        );
        
        $query = UpdateEmailAcStepDetails($step_id,$action_id,$vd);
        
        if($query){
        
           $data = array(
        		'status' => 200,
        		'message' => "Successfully Updated",
        	); 
        	
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, while processing your request',
        	);
        	
        }
    
    }else{
        
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Unautorized Action',
        	);
        	
    }
    
    
    
    
    	
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
}

// UPDATE (SMS/TEXT) ACTION DETAILS 
if( isset($_POST['action']) && ($_POST['action'] == "updateSmsTextActionStepN") ) {

    if(isset($_POST['step_id'])){
        $step_id = $_POST['step_id'];
    }
    
    if(isset($_POST['action_id'])){
        $action_id = $_POST['action_id'];
    }
    
    if(isset($_POST['message'])){
        $message = $_POST['message'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
    
    if($user_id === $wo['user']['user_id'] ){
        
        $vd = array(
            'message' => $message
        );
        
        $query = UpdateSmsTextAcStepDetails($step_id,$action_id,$vd);
        
        if($query){
        
           $data = array(
        		'status' => 200,
        		'message' => "Successfully Updated",
        	); 
        	
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, while processing your request',
        	);
        	
        }
    
    }else{
        
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Unautorized Action',
        	);
        	
    }
    
    
    
    
    	
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
}

// DELETE (SMS/TEXT) ACTION DETAILS 
if( isset($_POST['action']) && ($_POST['action'] == "deleteSmsTextActStepN") ) {
    
   if(isset($_POST['step_id'])){
        $step_id = $_POST['step_id'];
    }
    
    if(isset($_POST['action_id'])){
        $action_id = $_POST['action_id'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
    
    
    if($user_id === $wo['user']['user_id'] ){
        
        $query = DeleteSmsTextActionStep($step_id,$action_id);
    
        if($query > 0){
        
           $data = array(
        		'status' => 200,
        		'message' => 'Action Successfully Deleted',
        	); 
        	
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, while processing your request',
        	);
        	
        }
    
    }else{
        
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Unautorized Action',
        	);
        	
    }
    
    
    
    
    	
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
}

// UPDATE (WAIT) ACTION DETAILS 
if( isset($_POST['action']) && ($_POST['action'] == "updateWaitActionStepN") ) {

    if(isset($_POST['step_id'])){
        $step_id = $_POST['step_id'];
    }
    
    if(isset($_POST['action_id'])){
        $action_id = $_POST['action_id'];
    }
    
    if(isset($_POST['days'])){
        $days = $_POST['days'];
    }
    
    if(isset($_POST['hours'])){
        $hours = $_POST['hours'];
    }
    
    if(isset($_POST['minutes'])){
        $minutes = $_POST['minutes'];
    }
    
    if(isset($_POST['time'])){
        $time = $_POST['time'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
    
    if($user_id === $wo['user']['user_id'] ){
        
        $vd = array(
            'days' => $days,
            'hours' => $hours,
            'minutes' => $minutes,
            'time' => $time
        );
        
        $query = UpdateWaitAcStepDetails($step_id,$action_id,$vd);
        
        if($query){
        
           $data = array(
        		'status' => 200,
        		'message' => "Successfully Updated",
        	); 
        	
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, while processing your request',
        	);
        	
        }
    
    }else{
        
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Unautorized Action',
        	);
        	
    }
    
    
    
    
    	
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
}

// DELETE (WAIT) ACTION DETAILS 
if( isset($_POST['action']) && ($_POST['action'] == "deleteWaitActStepN") ) {
    
   if(isset($_POST['step_id'])){
        $step_id = $_POST['step_id'];
    }
    
    if(isset($_POST['action_id'])){
        $action_id = $_POST['action_id'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
    
    
    if($user_id === $wo['user']['user_id'] ){
        
        $query = DeleteWaitActionStep($step_id,$action_id);
    
        if($query > 0){
        
           $data = array(
        		'status' => 200,
        		'message' => 'Action Successfully Deleted',
        	); 
        	
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, while processing your request',
        	);
        	
        }
    
    }else{
        
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Unautorized Action',
        	);
        	
    }    
    	
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
}

/// get contacts for live stage
if( isset($_POST['action']) && ($_POST['action'] == "getLiveStageContact") ) {

    if(isset($_POST['pipe_id'])){
        $pipe_id = $_POST['pipe_id'];
    }
    
    if(isset($_POST['stage_id'])){
        $stage_id = $_POST['stage_id'];
    }
    
    if(isset($_POST['stage_no'])){
        $stage_no = $_POST['stage_no'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
        
		$counter=0;
        $query = GetPipeLineStagesAllcontact($pipe_id,$stage_id);
        
        if(count($query)>0){
        foreach($query as $stl){ 
	?>
        
			<tr id="cts_<?= $stl['id']; ?>" class="cts" <?= ($counter % 2 == 0) ? 'bgcolor=#fff': '' ?> >
        	   <td scope="row" class="align-middle">
        	       <input type="checkbox" name="row-check" class="cts_checkbox" data-cts-id="<?= $stl['id']; ?>" id="contact_<?= $stl['id']; ?>" value="<?= $stl['id']; ?>" >
        	   </td>
              <td class="align-middle"><?= $stl['firstname'];?></td>
        	 
              <td class="align-middle"><?= $stl['lastname'];?></td>
              <td class="align-middle"><?= $stl['mobile'];?></td>
              <td class="align-middle"><?= $stl['email'];?></td>
              <?php if($stl['type'] == 1){ ?>
              <td class="align-middle">Contact</td>
              <?php }else if($stl['type'] == 2){ ?>
              <td class="align-middle">Buyer</td>
              <?php }else{ ?>
              <td class="align-middle">Property</td>
              <?php } ?>
        	  <td class="align-middle tagsColumn listTags" >
                <?php 
					$contactTagsWithCategory = getContactTagsWithCategory($stl['id'], $user_id, $contentType); 
                    foreach($contactTagsWithCategory as $key => $tag) {  
						echo "<span>{$tag['tag']},</span>";
                    }
                ?>
              </td>
              <td class="align-middle">
                  <?php if($stl['type'] == 1){ ?>
                  <a href="<?= $wo['site_url'] ?>/contact/profile/<?= $stl['id']; ?>">
                  <?php }else if($stl['type'] == 2){ ?>
                  <a href="<?= $wo['site_url'] ?>/vip-buyer/edit/<?= $stl['id']; ?>">
                  <?php }else{ ?>
                  <a href="<?= $wo['site_url'] ?>/properties/profile/<?= $stl['id']; ?>">
                  <?php } ?>
                      <img src="<?= $wo['site_url'] ?>/img/button-viewall.png" width="30" alt="">
                  </a>
              </td>
            </tr>   
            
            
       <?php
		$counter++;	   
		}
		}else
		{
			echo '<tr><td colspan="7"></td>';
		}
    
}

// SHOW LIVE STAGE STEPS
if( isset($_POST['action']) && ($_POST['action'] == "ShowLiveStageSteps") ) {
	
	//print_r($_POST);

    if(isset($_POST['pipe_id'])){
        $pipe_id = $_POST['pipe_id'];
    }
    
    if(isset($_POST['stage_id'])){
        $stage_id = $_POST['stage_id'];
    }
    
    if(isset($_POST['stage_no'])){
        $stage_no = $_POST['stage_no'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
        //echo $user_id.'==='. $wo['user']['user_id'];
    if($user_id === $wo['user']['user_id'] ){
        
        $query = GetStageSteps($stage_id,$pipe_id);
        
        foreach($query as $ste){ ?>        
        <div style="position:static !important;" class="col-xl-3 col-lg-3 col-md-3 col-sm-12 dropRow" data-action-id="<?= $ste['id'] ?>" id="stepss">
			<div class="stgmn ste">
				<div class="d-flex justify-content-between w-100 tostg">
					<div class="d-flex align-items-center stgcnt">
						<span class="nomnm">Step</span> <span class="ms-2 nom"><?= $ste['step_no'] ?></span>
					</div>
					<div class="nvstg getcontacts" data-pipe-id="<?= $pipe_id;?>" data-stage_id="<?=$ste['stage_id']?>" data-step-no="<?=$ste['id']?>" >									
									<p class="m-0"><i class="fa fa-user"></i> <?php echo GetPipeLineStagesindiviualcontact($pipe_id,$ste['stage_id'],$ste['id']);
										?></p>
					</div>
					
				</div>
				
				<div class="midstg mb-5">
					<h4 class="text-uppercase mb-4 " id="step_<?= $ste['id'] ?>_N"><?= $ste['name'] ?></h4>
					<p></p>
				</div>
				
				<div class="collapse" id="collapseExample_<?= $ste['id'] ?>">
  					<div class="card12 card-bod21y">
						<div class="text-center drpin clps">
                			<ul class="p-0 m-0" id="stepListAction">
                			    
                			  
								<?php $actStep = GetAllActionInAStep($ste['id']); 
                            			        
                			        foreach($actStep as $sat) { 
                			            $asd = GetSingleStepActDetails($sat['type']);		
                    			        $stepsQuesContacts = GetPipeLineStagesActionindiviualcontact($pipe_id,$sat['stage_id'],$sat['step_id'],$sat['id']);
										// $stepsQuesContacts =  GetPipeLineStagesStepsQuesContact($pipe_id, $ste['stage_id'],  $ste['id'],$sat['id']);
										$quescont = 0;						
									    if($stepsQuesContacts>0) $quescont = $stepsQuesContacts;
								?>  
                            			 
                    			    <li class="d-block">
        								<a class="p-3 d-flex w-100 justify-content-between align-items-center" data-step-id="<?= $sat['step_id'] ?>" data-step-actionid="<?= $sat['type'] ?>" data-action-no="<?= $sat['action_no'] ?>" href="javascript:void(0)" onclick="#" data-backdrop="static" data-keyboard="false">
        									<div class="">										
        										<p><i class="<?= $asd['icon'] ?>"></i> <?= $asd['name'] ?></p>
        									</div>
        									
        									<div class="">        										
												<p class="m-0"><i class="fa fa-user"></i><?=$quescont;?></p>
        									</div>
        								</a>
        							</li>
        							
        						<?php } ?>
    							
                			</ul>
		                </div>
					</div>
				</div>
				
				<div class="text-center position-relative btmstg">
					<p class="m-0"><span id="actionNo" ><?= $ste['actions'] ?></span> Actions</p>
					<a class="openstep" id="stepOpenAndClose" data-toggle="collapse" href="#collapseExample_<?= $ste['id'] ?>" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-chevron-down"></i></a>
				</div>
			</div>
		</div>
        
    <?php } 
    
    ?>
    <script>
    
            $(".openstep").click(function (e) {
                
                var stagesh = $(this).attr("data-stid");
                
                $(this).parent().parent().addClass("active");
                $(this).find("#stepOpenAndClose").html('<i class="fa fa-chevron-down"></i>');
                
                $(".openstep").not(this).parent().parent().removeClass("active");
                $(".openstep").not(this).find("#stepOpenAndClose").html('<i class="fa fa-chevron-up"></i>');
                
            });
            
            $(".getcontacts").click(function (e) {
                
                $(this).addClass("active");               
                var pipe_id = $(this).attr("data-pipe-id");
                var stage_id = $(this).attr("data-stage_id"); 
                var step_no = $(this).attr("data-step-no");
                
                $.ajax({
                  type:"POST",
                  url: websiteUrl + "/"+endpoint,
                  data: {
            		action : "getLiveStageStepContact",
            		pipe_id : pipeId,
            		stage_id: stage_id,
            		step_no: step_no,
            		user_id : user_idr
            	  },
                  beforeSend: function() {
                        //run_waitMe($('div #contnet'), 1, 'win8');
                  },
            
                    success: function (data) {
                        
                        $('div #contnet').waitMe('hide')
                        
                        
                        if(data){
                            
                            // REALEASE THE STAGE ID
                            // $("#stageID").val(stage_id);
                            
                            // REMOVE THE PREVIOUS STEPS AND ADD NEW ONES
                            //$('div#stepss').remove();
                            $("#liveCon2").html(data);
                            
                            
                        }else{
                            
                            // Show error message
                            toastr_call("warning","Error!");
                            
                        }
                        
                    }
                 });
                
                
            });
            $(".dragA").draggable({
                
                cursor: 'move',
                helper: 'clone'
                
            });
            
            // DROP FOR STEP ACTIONS...
            $(".dropRow, .collectRow").droppable({
                drop: function (ev, ui) {
             
                    var draggableId = ui.draggable.attr("id");
                    var dragmodal = ui.draggable.attr("data-modal-name");
                    var droppableId = $(this).attr("data-action-id");
                    
                    var thisv = $(this);
                    
                    var stagenid = $("#stageID").val();
                    
                    // Insert Action under the step
                    $.ajax({
                      type:"POST",
                      url: websiteUrl + "/"+endpoint,
                      data: {
                		action : "PutActionUnderStep",
                		pipe_id : pipeId,
                		step_id: droppableId,
                		stage_id: stagenid,
                		action_id: draggableId,
                		user_id : user_idr
                	  },
                      beforeSend: function() {
                            run_waitMe($('div #contnet'), 1, 'win8');
                      },
                
                        success: function (data) {
                            $('div #contnet').waitMe('hide');
                            
                            if(data.status == 200){
                                // get new step action id
                                var acId =  data.myactionId;
                                var noactt = data.actionData;
                                
                                if(draggableId == 1){
                                    var mytemp = smsTextStepActionTemplate(droppableId,acId,draggableId);
                                }else if(draggableId == 2){
                                    var mytemp = ringlessVoiceMailStepActionTemplate(droppableId,acId,draggableId);
                                }else if(draggableId == 3){
                                    var mytemp = emailStepActionTemplate(droppableId,acId,draggableId);
                                }else if(draggableId == 4){
                                    var mytemp = directEmailStepActionTemplate();
                                }else if(draggableId == 5){
                                    var mytemp = waitStepActionTemplate(droppableId,acId,draggableId);
                                }
                                
                                thisv.find("#stepListAction").append(mytemp);
                                thisv.find("#actionNo").text(noactt);
                                
                                getAllModalActionDetailsF(droppableId,acId,draggableId);
                                
                            }else{
                                // Show error message
                                toastr_call("warning",data.message);
                                
                            }
                            
                        }
                    });
                    
                }
            });
            
    </script>
    
    <?php
    
    } 
    
}

// UPLOAD LIVE RECORDED AUDIO 
if( isset($_POST['action']) && ($_POST['action'] == "upload_recoded_file") ) {
    
    if(isset($_POST['step_id'])){
        $step_id = $_POST['step_id'];
    }
    
    if(isset($_POST['s_action_id'])){
        $action_id = $_POST['s_action_id'];
    }
    
    if(isset($_POST['mode'])){
        $mode = $_POST['mode'];
    }
    
    if (isset($_FILES['audio_data']) && !empty($_FILES['audio_data'])) {

		if (!empty($_FILES['audio_data']["tmp_name"])) {
			$orignalname = $_FILES['audio_data']["name"];
			$filename = "";
			$fileInfo = array(
				'file' => $_FILES["audio_data"]["tmp_name"],
				'name' => $_FILES['audio_data']['name'],
				'size' => $_FILES["audio_data"]["size"],
				'type' => $_FILES["audio_data"]["type"],
				'types' => 'mp3,wav',
			);

			$media = Wo_ShareFile($fileInfo, 0, false);
			if (!empty($media)) {
				$filename = $media['filename'];

			}
			
			$updata = array(
			    'audio_url' => $filename,
			    'audio_type' => $mode
			 );
			
			
			$update_RVM = UpdateRinglessVoiceMailDetails($step_id,$action_id,$updata);
			
			if($update_RVM){
			    
			    $data = array(
                    'status' => 200,
                    'message' => 'success',
                    'audUrl' => $filename
                );
        
			}else{
			    
			    $data = array(
                    'status' => 400,
                    'message' => 'error',
                );
                
			}
			

		}
		
		
    }
    
    
    header("Content-type: application/json");
    echo json_encode($data);
    die;
    
    
    
    
    
}

// DELETE RECORDED AUDIO
if( isset($_POST['action']) && ($_POST['action'] == "DeleteRecordFile") ) {
    
    if(isset($_POST['step_id'])){
        $step_id = $_POST['step_id'];
    }
    
    if(isset($_POST['s_action_id'])){
        $action_id = $_POST['s_action_id'];
    }
    
    $sRVM = getJustSingleRinglessVoiceActionDetails($step_id,$action_id);
    
    if( $sRVM['audio_type'] != "" || !empty($sRVM['audio_type']) ){
        
        $aURL = $sRVM['audio_url'];
        
        if (unlink($aURL)) {
            
            $rdata = array(
                'audio_url' => "",
			    'audio_type' => "",
			    'title' => ""
            );
            
            UpdateRinglessVoiceMailDetails($step_id,$action_id,$rdata);
            
            $data = array(
                'status' => 200,
                'message' => 'Deleted Sucessfully',
            );
            
        }else{
            
            $data = array(
                'status' => 400,
                'message' => 'Error',
            );
            
        }
        
        
    }else{
        
        $data = array(
            'status' => 400,
            'message' => 'Sorry! The system could not process your request',
        );
        
    }
    
    
    header("Content-type: application/json");
    echo json_encode($data);
    die;

}

// UPDATE RINGLESS VOICEMAIL ACTION DETAILS
if( isset($_POST['action']) && ($_POST['action'] == "updateRinglessVMActionStepN") ) {

    if(isset($_POST['step_id'])){
        $step_id = $_POST['step_id'];
    }
    
    if(isset($_POST['action_id'])){
        $action_id = $_POST['action_id'];
    }
    
    if(isset($_POST['title'])){
        $title = $_POST['title'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
    
    if($user_id === $wo['user']['user_id'] ){
        
        $vd = array(
            'title' => $title
        );
        
        $query = UpdateRinglessVoiceMailDetails($step_id,$action_id,$vd);
        
        if($query){
        
           $data = array(
        		'status' => 200,
        		'message' => "Successfully Updated",
        	); 
        	
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, while processing your request',
        	);
        	
        }
    
    }else{
        
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Unautorized Action',
        	);
        	
    }
    
    
    
    
    	
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
}

// DELETE (RINGLESS VOICEMAIL) ACTION DETAILS 
if( isset($_POST['action']) && ($_POST['action'] == "deleteRinglessVMActStepN") ) {
    
   if(isset($_POST['step_id'])){
        $step_id = $_POST['step_id'];
    }
    
    if(isset($_POST['action_id'])){
        $action_id = $_POST['action_id'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
    
    
    if($user_id === $wo['user']['user_id'] ){
        
        $query = DeleteRinglessVoiceMailActionStep($step_id,$action_id);
    
        if($query > 0){
        
           $data = array(
        		'status' => 200,
        		'message' => 'Action Successfully Deleted',
        	); 
        	
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, while processing your request',
        	);
        	
        }
    
    }else{
        
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Unautorized Action',
        	);
        	
    }
    
    
    
    
    	
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
}





// 
// 
// CONTACTS ACTIONS
// 
//

// DELETE CONTACTS FROM PIPELINE 
if( isset($_POST['action']) && ($_POST['action'] == "DeleteContactsfromPipe") ) {
    
    if(isset($_POST['pipeId'])){
        $pipe_id = $_POST['pipeId'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
    
    if(isset($_POST['contacts_id'])){
         $contacts_id = trim($_POST['contacts_id']);
    }
    
    $Contsarray = explode(',', $contacts_id);
    
    if($user_id === $wo['user']['user_id'] ){
        
        $delete = DeleteContactsInPipeLine($pipe_id,$contacts_id);
    
        if($delete){
        
           $data = array(
        		'status' => 200,
        		'cts_id' => $contacts_id,
        		'message' => count($Contsarray) .' Contacts Successfully Deleted',
        	); 
        	
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, while processing your request ',
        	);
        	
        }
    
    }else{
        
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Unautorized Action',
        	);
        	
    }
    
    
    
    
    	
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
    
    
    
}

// DELETE CONTACTS FROM PIPELINE AND CONTACT 
if( isset($_POST['action']) && ($_POST['action'] == "DeleteContactsfromPipeandCont") ) {
    
    if(isset($_POST['pipeId'])){
        $pipe_id = $_POST['pipeId'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
    
    if(isset($_POST['contacts_id'])){
         $contacts_id = trim($_POST['contacts_id']);
    }
    
    $Contsarray = explode(',', $contacts_id);
    
    if($user_id === $wo['user']['user_id'] ){
        
        $delete = DeleteContactsInPipeLineandContact($contacts_id);
    
        if($delete){
            
            DeleteContactsInPipeLine($pipe_id,$contacts_id);
        
           $data = array(
        		'status' => 200,
        		'cts_id' => $contacts_id,
        		'message' => count($Contsarray) .' Contacts Successfully Deleted',
        	); 
        	
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, while processing your request ',
        	);
        	
        }
    
    }else{
        
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Unautorized Action',
        	);
        	
    }
    
    
    
    
    	
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
    
    
    
}

// GET STEPS UNDER STAGE ALONG WITH PIPELINE 
if( isset($_POST['action']) && ($_POST['action'] == "getStepsInStage") ) {
    
    if(isset($_POST['pipeId'])){
        $pipe_id = $_POST['pipeId'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
    
    if(isset($_POST['stage_id'])){
         $stage_id = $_POST['stage_id'];
    }
    
        
    $sdata = mysqli_query($sqlConnect, "SELECT * FROM ". T_CAMPAIGN_STEPS ." WHERE `stage_id` = $stage_id AND `pipe_id` = $pipe_id AND `status` = 1");
    
    if($sdata->num_rows > 0){ 
        echo '<option value="">Select Step</option>'; 
        while($row = $sdata->fetch_assoc()){  
            echo '<option value="'.$row['id'].'">'.$row['name'].'</option>'; 
        } 
    }else{ 
        echo '<option value="">Step not available</option>'; 
    } 

    
}

if( isset($_POST['action']) && ($_POST['action'] == "getStagesInPipe") ) {
    
    if(isset($_POST['pipeId'])){
        $pipe_id = $_POST['pipeId'];
    }
        
    $sdata = mysqli_query($sqlConnect, "SELECT * FROM ". T_CAMPAIGN_STAGES ." WHERE `pipe_id` = $pipe_id AND `status` = 1");
    
    if($sdata->num_rows > 0){ 
        echo '<option value="">Select Stage</option>'; 
        while($row = $sdata->fetch_assoc()){  
            echo '<option value="'.$row['id'].'">'.$row['name'].'</option>'; 
        } 
    }else{ 
        echo '<option value="">Stages not available</option>'; 
    } 

    
}

// MOVE CONTACTS TO ANOTHER STAGE AND STEP WITHING THE PIPELINE 
if( isset($_POST['action']) && ($_POST['action'] == "MoveSelectedContacts") ) {
    
    if(isset($_POST['pipeId'])){
        $pipe_id = $_POST['pipeId'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
    
    if(isset($_POST['sel_stage_id'])){
         $stage_id = $_POST['sel_stage_id'];
    }
    
    if(isset($_POST['sel_step_id'])){
         $step_id = $_POST['sel_step_id'];
    }
    
    if(isset($_POST['contacts_id'])){
         $contacts_id = trim($_POST['contacts_id']);
    }
    
    $Contsarray = explode(',', $contacts_id);
    
    
    if($user_id === $wo['user']['user_id'] ){
        
       $movcont = MoveContactsInPipeLine($pipe_id,$stage_id,$step_id,$contacts_id);
    
        if($movcont){
        
           $data = array(
        		'status' => 200,
        		'cts_id' => $contacts_id,
        		'message' => count($Contsarray) .' Contacts Successfully Moved',
        	); 
        	
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, while processing your request ',
        	);
        	
        }
        
    }else{
        
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Unautorized Action',
        	);
        	
    }
    
    
    
    
    	
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
    
    
    
}

// MOVE CONTACTS TO DIFFERENT DEAL OR LEAD PIPEINE 
if( isset($_POST['action']) && ($_POST['action'] == "MoveSelectedContactsDiffDLP") ) {
    
    if(isset($_POST['pipeId'])){
        $pipe_id = $_POST['pipeId'];
    }
    
    if(isset($_POST['PpipeId'])){
        $Ppipe_id = $_POST['PpipeId'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
    
    if(isset($_POST['sel_stage_id'])){
         $stage_id = $_POST['sel_stage_id'];
    }
    
    if(isset($_POST['sel_step_id'])){
         $step_id = $_POST['sel_step_id'];
    }
    
    if(isset($_POST['contacts_id'])){
         $contacts_id = trim($_POST['contacts_id']);
    }
    
    
    if($user_id === $wo['user']['user_id'] ){
        
        $moveDa = MoveContactsInDiffDRPipeLine($Ppipe_id,$pipe_id,$stage_id,$step_id,$contacts_id);
        if($moveDa > 0){
            
             $data = array(
        		'status' => 200,
        		'cts_id' => $contacts_id,
        		'message' => $moveDa.' Contacts Successfully Moved',
        	); 
        	
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Contact(s) are existing already',
        	);
        }
            
        
    }else{
        
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Unautorized Action',
        	);
        	
    }
    
    
    
    
    	
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
    
    
    
}

// ADD TAG TO CONTACTS 
if( isset($_POST['action']) && ($_POST['action'] == "addTagToContact") ) {
    
    if(isset($_POST['tag_id'])){
        $tag_id = $_POST['tag_id'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }

    
    if(isset($_POST['contacts_id'])){
         $contacts_id = trim($_POST['contacts_id']);
    }
    
    
    if($user_id === $wo['user']['user_id'] ){
        
        $moveDa = AddTagToContactsFunc($tag_id,$user_id,$contacts_id);
        
        if($moveDa > 0){
            
             $data = array(
        		'status' => 200,
        		'cts_id' => $contacts_id,
        		'message' => $moveDa.' Contacts Successfully Tagged',
        	); 
        	
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Contact(s) are existing already with the Tag',
        	);
        }
            
        
    }else{
        
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Unautorized Action',
        	);
        	
    }
    
    
    
    
    	
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
    
    
    
}


// GET BULK ACTION OPTIONS
if(isset($_POST['action']) && ($_POST['action'] == "getBulkActionOpt")){
    
    
    if(isset($_POST['pipe_id'])){
        $pipe_id = $_POST['pipe_id'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
    
    //Get Pipe details by Pipe ID
    $pipeD = GetSinglePipelimeDetails($pipe_id,$user_id);
    
    if($pipeD['setting']['market'] == 1){ ?>
        
        <div class="form-group">
			<select name="" class="form-control" id="bulkAct" disabled>
				<option><a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#ii" data-backdrop="static" data-keyboard="false" id="bulk_action">Bulk Actions</a></option>
				<option class="options" disabled   value="MoveContasTo" class="dropdown-item" href="javascript:void(0)" id="MoveContasTo">Move Contact(s)</option>
				<option class="options" disabled   value="DeleteCTSfromPipe" class="dropdown-item" href="javascript:void(0)" id="DeleteCTSfromPipe">Remove From Pipeline</option>
				<option class="options" disabled   value="attachedTagtoContactsAc" class="dropdown-item" href="javascript:void(0)" id="attachedTagtoContactsAc">Apply / Remove Tag</option>
				<option class="options" disabled   value="MoveContasToDifFlEDP" class="dropdown-item" href="javascript:void(0)" id="MoveContasToDifFlEDP">Move to Different <?= ucfirst($pipeD['type']) ?> Pipeline</option>
				<option class="options" disabled  value="MoveContasToAnoDifFlEDP" class="dropdown-item" href="javascript:void(0)" id="MoveContasToAnoDifFlEDP">Move to <?= $AtypeName ?> Pipeline</option>
				<option class="options" disabled   value="DeleteCTSfromPipeandCont" class="dropdown-item" href="javascript:void(0)" id="DeleteCTSfromPipeandCont">Delete Contact(s)</option>
			</select>
		</div>
        
 <?php   }else{  ?>
 
        <div class="form-group">
			<select name="" class="form-control" id="bulkAct" disabled>
				<option><a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#ii" data-backdrop="static" data-keyboard="false" id="bulk_action">Bulk Actions</a></option>
				<option class="options"  value="MoveContasTo" class="dropdown-item" href="javascript:void(0)" id="MoveContasTo">Move Contact(s)</option>
				<option class="options"  value="DeleteCTSfromPipe" class="dropdown-item" href="javascript:void(0)" id="DeleteCTSfromPipe">Remove From Pipeline</option>
				<option class="options"  value="attachedTagtoContactsAc" class="dropdown-item" href="javascript:void(0)" id="attachedTagtoContactsAc">Apply / Remove Tag</option>
				<option class="options" value="MoveContasToDifFlEDP" class="dropdown-item" href="javascript:void(0)" id="MoveContasToDifFlEDP">Move to Different <?= ucfirst($pipeD['type']) ?> Pipeline</option>
				<option class="options" value="MoveContasToAnoDifFlEDP" class="dropdown-item" href="javascript:void(0)" id="MoveContasToAnoDifFlEDP">Move to <?= $AtypeName ?> Pipeline</option>
				<option class="options"  value="DeleteCTSfromPipeandCont" class="dropdown-item" href="javascript:void(0)" id="DeleteCTSfromPipeandCont">Delete Contact(s)</option>
			</select>
		</div>
    
 <?php   }
    
    
    
}



// SELECT FIRST STAGE IN PIPELINE 
if( isset($_POST['action']) && ($_POST['action'] == "getFirstStepInStage") ) {
    
    if(isset($_POST['pipeId'])){
        $pipe_id = $_POST['pipeId'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
    
    
    if($user_id == $wo['user']['user_id']){
        
        $stage_id = GetThefirstStageInPipe($step_no,$pipe_id);
        
        $data = array(
    		'status' => 200,
    		'stage_id' => $stage_id,
    		'message' => 'Successfully',
    	);
        	
    }else{
        
        $data = array(
    		'status' => 400,
    		'message' => 'Error!, Unautorized Action',
    	);
        
    }
    
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
}


// REMOVE TAG FROM CONTACTS
if( isset($_POST['action']) && ($_POST['action'] == "removeTagFromContact") ) {
    
    if(isset($_POST['tag_id'])){
        $tag_id = $_POST['tag_id'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }

    
    if(isset($_POST['contacts_id'])){
         $contacts_id = trim($_POST['contacts_id']);
    }
    
    
    if($user_id === $wo['user']['user_id'] ){
        
        $moveDa = RemoveTagFromContactsFunc($tag_id,$user_id,$contacts_id);
        
        if($moveDa > 0){
            
             $data = array(
        		'status' => 200,
        		'cts_id' => $contacts_id,
        		'message' => $moveDa.' Contacts Successfully Tagged',
        	); 
        	
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Contact(s) are existing already with the Tag',
        	);
        }
            
        
    }else{
        
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Unautorized Action',
        	);
        	
    }
    
    
    
    
    	
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
    
    
    
}

// RESET LOW WALLET NOTIFICATION 
if( isset($_POST['action']) && ($_POST['action'] == "ResetLowWalletNot") ) {
    
    if(isset($_POST['pipeId'])){
        $pipe_id = $_POST['pipeId'];
    }
    
    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }
    
    
    if($user_id === $wo['user']['user_id'] ){
        
        $moveDa = ResetLowWalletNotification($user_id,$pipe_id);
        
        if($moveDa){
            
             $data = array(
        		'status' => 200,
        		'message' => 'Successfully Done',
        	); 
        	
        }else{
            
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, System Could not process your request',
        	);
        }
            
        
    }else{
        
            $data = array(
        		'status' => 400,
        		'message' => 'Error!, Unautorized Action',
        	);
        	
    }
    
    
    
    
    	
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
    
    
    
}


// 
// 
// REAL TIME LIVE PAGE 
// 
// 


// FETCH EDIT CONTAINERS
if( isset($_POST['action']) && ($_POST['action'] == "getFetchEditCont") ) {
    
    $user_id = $_POST['user_id'];
    $pipe_id = $_POST['pipe_id'];
    
    if($user_id === $wo['user']['user_id'] ){ ?>
        
       <div class="mt-3 mb-5 mdbdy">
          <div class="container-fluid">
			<div class="scrolling-wrapper row m-auto flex-row flex-nowrap pb-4 pt-5" id="newRow">
			    
			    <?php 
			        $pipeStages = GetPipeLineStages($pipe_id);
			        $count = 0;
			        foreach($pipeStages as $st){ ?>
			        
					<div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
						<div class="stgmn stag <?php if($count==0){ echo "active"; } ?>"  data-sid="<?= $st['id'] ?>" data-sno="<?= $st['s_no'] ?>" id="stage_<?= $st['id'] ?>_id" data-id="stage_no_<?= $st['s_no'] ?>">
							<div class="d-flex justify-content-between w-100 tostg">
								<div class="d-flex align-items-center stgcnt">
									<span class="nomnm">Stage</span> <span class="ms-2 nom"><?= $st['s_no'] ?></span>
								</div>
								
								<div class="nvstg" >
									
									<a href="javascript:void(0)" onclick="ShowStageModal(<?= $st['id'] ?>,<?= $pipe_id ?>,<?= $st['s_no'] ?>)" data-backdrop="static" data-keyboard="false"><i class="fas fa-ellipsis-h"></i></a>
								</div>
							</div>
							
							<div class="midstg mb-5">
								<h4 class="text-uppercase mb-4 " id="stage_<?= $st['id'] ?>_N"><?= $st['name'] ?></h4>
								<p id="stage_<?= $st['id'] ?>_D"><?= $st['description'] ?>.</p>
							</div>
							
							<div class="text-center position-relative btmstg">
								<p class="m-0"><span id="NumStepof_<?= $st['id'] ?>_No"><?= $st['steps']['stno'] ?></span> Steps</p>
								
								<?php if($count == 0) { ?>
								    <a href="javascript:void(0)" id="stageOpenAndClose"><i class="fa fa-chevron-down"></i></a>
								<?php }else{ ?>
								    <a href="javascript:void(0)" id="stageOpenAndClose"><i class="fa fa-chevron-up"></i></a>
								<?php } ?>
								
							</div>
						</div>
					</div>
					
                <?php $count++; } ?>
					
				
				<div class="order-3 col-xl-3 col-lg-3 col-md-3 col-sm-3 col-sm-12">
					<div class="stgmn">
						<div class="align-items-center align-content-center d-flex w-100 midact" style="min-height: 168px;" id="addRow">
							<div class="m-auto plsicn">
								<i class="fas fa-plus"></i>
							</div>							
						</div>
						
						
					</div>
				</div>
				
		    </div>
	 
        </div>
	   </div>
	  
	   <div class="mb-5 drgdrp">
    	  	<div class="container-fluid">
    		  	<div class="row">
    				<div class="col-sm-12">
    					<div class="mb-4 ttl">
    						<h3 class="fw-bold text-center">Drag actions to your steps below</h3>
    					</div>
    					<div class="text-center drpin">
    						<ul class="p-0 m-0">
    							<li class="d-xl-inline-block d-lg-inline-block d-none"><img src="<?= $wo['config']['site_url'] ?>/design/img/la.png" alt="" width="100"></li>
    							
    							<?php $steaction = GetStepDragActions(); foreach($steaction as $sa) { ?>
        							<li class="d-inline-block dragA" id="<?= $sa['id'] ?>" data-modal-name="<?= $sa['modal'] ?>">
        								<a href="javascript:void(0)" data-toggle="modal" data-num= "<?= $sa['no'] ?>" data-backdrop="static" data-keyboard="false">
        									<i class="<?= $sa['icon'] ?>"></i>
        									<p><?= $sa['name'] ?></p>
        								</a>
        							</li>
    							<?php } ?>
    							
    							<li class="d-xl-inline-block d-lg-inline-block d-none"><img src="<?= $wo['config']['site_url'] ?>/design/img/ra.png" alt="" width="100"></li>
    						</ul>
    					</div>
    				</div>
    			</div>
    		</div>
	    </div>
	  
	   <div class="mt-3 mb-5 mdbdy">
	  	    <div class="container-fluid">
			    <div class="scrolling-wrapper row m-auto flex-row flex-nowrap pb-4 pt-5 awad" id="newRow2">
    			    
    			    <?php $query2 = selectFirstStageSteps(1,$pipe_id); foreach($query2 as $ste){ ?>
					
                    <div style="position:static !important;" class="col-xl-3 col-lg-3 col-md-3 col-sm-12 dropRow ui-droppable" data-action-id="<?= $ste['id'] ?>" id="stepss">
            		    <div class="stgmn ste">
            				<div class="d-flex justify-content-between w-100 tostg">
            					<div class="d-flex align-items-center stgcnt">
            						<span class="nomnm">Step</span> <span class="ms-2 nom"><?= $ste['step_no'] ?></span>
            					</div>
            					
            					<div class="nvstg">
            					    
            						<a href="javascript:void(0)" onclick="ShowStepModal(<?= $ste['step_no'] ?>,<?= $ste['stage_id'] ?>,<?= $ste['id'] ?>)" data-backdrop="static" data-keyboard="false"><i class="fas fa-ellipsis-h"></i></a>
            						
            					</div>
            				</div>
            				
            				<div class="midstg mb-5">
            					<h4 class="text-uppercase mb-4 " id="step_<?= $ste['id'] ?>_N"><?= $ste['name'] ?></h4>
            					<p></p>
            				</div>
            				
            				<div class="collapse" data-parent="#newStepRows" id="collapseExample_<?= $ste['id'] ?>">
              					<div class="card12 card-bod21y">
            						<div class="text-center drpin clps">
                            			<ul class="p-0 m-0 " id="stepListAction">
                            			    
                            			   <?php $actStep = GetAllActionInAStep($ste['id']); foreach($actStep as $sat) { $asd = GetSingleStepActDetails($sat['type']); ?>
                            			   
                            			 
                            			    <li class="d-block">
                								<a class="p-3 d-flex w-100 justify-content-between align-items-center" data-step-id="<?= $sat['step_id'] ?>" data-step-actionid="<?= $sat['type'] ?>" data-action-no="<?= $sat['action_no'] ?>" href="javascript:void(0)" onclick="getAllModalActionDetailsF(<?= $sat['step_id'] ?>,<?= $sat['id'] ?>,<?= $sat['type'] ?>)" data-backdrop="static" data-keyboard="false">
                									<div class="">										
                										<p><i class="<?= $asd['icon'] ?>"></i> <?= $asd['name'] ?></p>
                									</div>
                									
                									<div class="">
                										<i class="fas fa-ellipsis-v"></i>
                									</div>
                								</a>
                							</li>
                							
                							<?php } ?>
                						
                							
                							
                            			</ul>
            		                </div>
            					</div>
            				</div>
            				
            				<div class="text-center position-relative btmstg">
            					<p class="m-0"><span id="actionNo"><?= $ste['actions'] ?></span> Actions</p>
            					<a class="openstep" data-stid="<?= $ste['id'] ?>" data-toggle="collapse" href="#collapseExample_<?= $ste['id'] ?>" role="button" aria-expanded="false" aria-controls="collapseExample" id="stepOpenAndClose"><i class="fa fa-chevron-up"></i></a>
            				</div>
            			</div>
            		</div>
            		
            		<?php } ?>
            		
            	
    				<div style="float:left;" class="order-3 col-xl-3 col-lg-3 col-md-3 col-sm-12">
    					<div class="stgmn">
    						<div class="align-items-center align-content-center d-flex w-100 midact" style="min-height: 168px;" id="addRow2">
    							<div class="m-auto plsicn">
    								<i class="fas fa-plus"></i>
    							</div>							
    						</div>
    						
    						
    					</div>
    				</div>
    				
    			</div>
			 
		  	
		  </div>
	    </div>
        
<?php        
        
        
 }else{
        
        
        	
 }
    
}



?> 