<?php

    global $wo, $sqlConnect; 
    $root=$_SERVER['DOCUMENT_ROOT'];
    require_once($root.'/config.php');
    require_once('assets/init.php');
    $sqlConnect = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);
    
    $user_id = $wo['user']['user_id'];
    
    
    // add new Tracking Code
    if( isset($_POST['action']) && $_POST['action'] == 'SendTrackingCode_'.$user_id ){
        
        // get form
        if($user_id == $_POST['user_id']){

            // collect all form fields 
            $code_name = Wo_secure($_POST['code_name']);
            $tracking_script = base64_encode($_POST['tracking_script']);

            $path = $_POST['path'];

            // validate package with feature deduction number
            $pData = [
                'path' => $path,
                'user_id' => $user_id,
                'userPackage' => $wo['user']['my_package'],
                'number' => 1
            ];
            packageReducerValidator($pData); // will continue if it is true then terminate the code if false


            // process Tracking Code
            $data = array(
                'code_name' => $code_name,
                'tracking_script' => $tracking_script,
                'status' => 1,
                'user_id' => $user_id
            );
            
            // create Tracking Code
            $createCode = CreateTracking($data);
            
            if($createCode){
                
                $data = array(
                    'status' => 200,
                    'message' => "Keyword Successfully Created",
                );
                
            }else{
                
                $data = array(
                    'status' => 400,
                    'message' => "Error System could not process the request",
                );
                
            }
            
        }else{
            
            $data = array(
                'status' => 400,
                'message' => "Access restricted please contact the admin",
            );
            
            
        }
        
        
        header("Content-type: application/json");
        echo json_encode($data);
	    die();
        
    }
    
    
        // Update tracking Code
    if( isset($_POST['action']) && $_POST['action'] == 'SendEditTrackingCode' ){
        
        // get form
        if($user_id == $_POST['user_id']){
            
            // collect all form fields
            $code_name = Wo_secure($_POST['code_name']);
            $tracking_script = base64_encode($_POST['tracking_script']);
            $status = $_POST['status'];
            
            $code_id = $_POST['code_id'];

            // process keyword
            $data = array(
                'code_name' => $code_name,
                'tracking_script' => $tracking_script,
                'status' => $status,
                'user_id' => $user_id
            );
            
            // create keyword
            $updateKey = UpdateTrackingCode($data,$code_id);
            
            if($updateKey){
                
                $data = array(
                    'status' => 200,
                    'message' => "Tracking Code Successfully Updated",
                );
                
            }else{
                
                $data = array(
                    'status' => 400,
                    'message' => "Error System could not process the request",
                );
                
            }
            
        }else{
            
            $data = array(
                'status' => 400,
                'message' => "Access restricted please contact the admin",
            );
            
            
        }
        
        
        header("Content-type: application/json");
        echo json_encode($data);
	    die();
        
    }


    // delete keyword
    if( isset($_POST['action']) && $_POST['action'] == "deleteTrackingCode"){
        
        // get form

        $codeId = $_POST['code_id'];
        
        $deleteCode = DeleteTrackingCode($codeId,$user_id);
        
        if($deleteCode){
            
            $data = array(
                'status' => 200,
                'message' => "Tracking Code Successfully Deleted",
            );
            
        }else{
            
            $data = array(
                'status' => 400,
                'message' => "Error System could not process the request",
            );
            
        }
            
      
        header("Content-type: application/json");
        echo json_encode($data);
	    die();
        
        
    }
    
    
    // get keyword data
    if( isset($_POST['action']) && $_POST['action'] == "get_trackingcode_data" ){
        
        // get form
        $codeId = $_POST['code_id'];
        $userId = $_POST['user_id'];
        
        if($userId == $user_id){
            
            $codeData = GetTrackingCodeData($codeId);
            
            if($codeData){ ?>
    			        
                
                <div class="col-lg-12">
    		        <div class="form-group">
    					<label for="code_name">
    					    <b>Code Name</b>
    					</label>
    			        <input name="user_id" id="user_id" value="<?= $user_id ?>" type="hidden" />
    			        <input name="code_id" id="code_id" value="<?= $codeId ?>" type="hidden" />
    			        <input type="text" name="code_name" value="<?= $codeData['code_name'] ?>" id="code_name" class="form-control name_keyword" required>
    					
    					<span id="code_name-info" class="validation disabling"></span>
    				</div>
    		    </div>
    		    
    		    <div class="col-lg-12">
    		        <div class="form-group">
    					<label for="tracking_script">
    					    <b>Tracking Script</b>
    					</label>
    			        <textarea rows="8" class="form-control tracking_script" name="tracking_script" id="tracking_script" required><?= base64_decode($codeData['tracking_script']) ?></textarea>
    					<span id="tracking_script-info" class="validation disabling"></span>
    				</div>
    		    </div>
    			
    			<div class="col-lg-12">
    				<div class="form-group">
    					<label for="status">
    					    <b>Change Status</b>
    					</label>
    					<select name="status" class="form-control" id="status" required>
    						<option value="" >Status</option>
    						<option value="1" <?= $codeData['status'] == 1 ? "selected" : "" ?> >Active</option>
    						<option value="0" <?= $codeData['status'] == 0 ? "selected" : "" ?> >In-active</option>
    			        </select>
    					<span id="status-info" class="validation disabling"></span>
    				</div>
    			</div>
                
                
        <?php }else{ ?>
                
                
            <h3 style="margin-top: 10px; margin-bottom: 10px; text-align: center;"> Sorry System could not process your request </h3>
                
                
                
        <?php }
            
            
        }else{ ?>
            
            
            <h3 style="margin-top: 10px; margin-bottom: 10px; text-align: center;"> Sorry System could not capture your user Id </h3>
            
     <?php   }
   
    }
    
    // Delete Bulk Keyword 
    if( isset($_POST['action']) && ($_POST['action'] == "BulkCodesDelete") ) {
        
        if(isset($_POST['codes_id'])){
             $codes_id = trim($_POST['codes_id']);
        }
        
        if(isset($_POST['user_id'])){
            $user_id = $_POST['user_id'];
        }
    
        $Contsarray = explode(',', $codes_id);
        
        if($user_id === $wo['user']['user_id'] ){
            
            $delete = DeletebulkTrackingCodes($codes_id);
        
            if($delete){
               
               $actionTaken = array(
                    'user_id' => $user_id,
                    'page' => "tracking-code",
                    'action_description' => "Deleted ".count($Contsarray)." Tracking Codes From Tracking Code Table",
                    'action_type' => "delete",
                );
                
                saveUserActions($actionTaken);
                
               $data = array(
            		'status' => 200,
            		'cts_id' => $codes_id,
            		'message' => count($Contsarray) .' Tracking Code(s) Successfully Deleted',
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
    
    // if no action is set
    if( !isset($_POST['action']) && $_POST['action'] == ''){
        
        $data = array(
            'status' => 400,
            'message' => "Error Page Not Found",
        );
            
            
        header("Content-type: application/json");
        echo json_encode($data);
	    die();
        
        
    }