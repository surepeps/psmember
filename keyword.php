<?php

    global $wo, $sqlConnect; 
    $root=$_SERVER['DOCUMENT_ROOT'];
    require_once($root.'/config.php');
    require_once('assets/init.php');
    $sqlConnect = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);
    
    $user_id = $wo['user']['user_id'];
    
    
    // add new keyword
    if( isset($_POST['action']) && $_POST['action'] == 'SendKeyWord_'.$user_id ){
        
        // get form
        if($user_id == $_POST['user_id']){
            
            // collect all form fields
            $name_keyword = Wo_secure($_POST['name_keyword']);
            $key_descrip = Wo_secure($_POST['key_descrip']);
            $key_phone = $_POST['key_phone'];
            $key_workflow = $_POST['key_workflow'];
            
            // process keyword
            $data = array(
                'name_keyword' => $name_keyword,
                'key_descrip' => $key_descrip,
                'key_phone' => $key_phone,
                'key_workflow' => $key_workflow,
                'status' => 1,
                'user_id' => $user_id
            );
            
            // create keyword
            $createKey = CreateKeyword($data);
            
            if($createKey){
                
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
    
    // Update keyword
    if( isset($_POST['action']) && $_POST['action'] == 'SendEditKeyWord' ){
        
        // get form
        if($user_id == $_POST['user_id']){
            
            // collect all form fields
            $name_keyword = Wo_secure($_POST['name_keyword']);
            $key_descrip = Wo_secure($_POST['key_descrip']);
            $key_phone = $_POST['key_phone'];
            $key_id = $_POST['key_id'];
            $key_workflow = $_POST['key_workflow'];
            $status = $_POST['status'];
            
            
            // process keyword
            $data = array(
                'name_keyword' => $name_keyword,
                'key_descrip' => $key_descrip,
                'key_phone' => $key_phone,
                'status' => $status,
                'key_workflow' => $key_workflow,
                'user_id' => $user_id
            );
            
            // create keyword
            $updateKey = UpdateKeyword($data,$key_id);
            
            if($updateKey){
                
                $data = array(
                    'status' => 200,
                    'message' => "Keyword Successfully Updated",
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
    if( isset($_POST['action']) && $_POST['action'] == "deleteKeyword"){
        
        // get form

        $keyId = $_POST['key_id'];
        
        $deleteKey = DeleteKeyword($keyId,$user_id);
        
        if($deleteKey){
            
            $data = array(
                'status' => 200,
                'message' => "Keyword Successfully Deleted",
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
    if( isset($_POST['action']) && $_POST['action'] == "get_keyword_data" ){
        
        // get form
        $keyId = $_POST['key_id'];
        $userId = $_POST['user_id'];
        
        if($userId == $user_id){
            
            $keyData = GetKeywordData($keyId);
            
            // get LCN Number
            $lcnall = getAllMyLCN($userId);
            
            // Pipeline fetching
            $pipesL = FetchallPipelines($userId,'lead'); 
            $pipesD = FetchallPipelines($userId,'deal');
            
            if($keyData){ ?>
                
                <div class="col-lg-12">
    		        <div class="form-group">
    					<label for="name_keyword">
    					    <b>Name Keyword - Can only contain letters, numbers, and dashes! (<span style="color: red;">Note: Seperate each keywords with comma</span>)</b>
    					</label>
    			        <input name="user_id" id="user_id" value="<?= $user_id ?>" type="hidden" />
    			        <input name="key_id" id="key_id" value="<?= $keyId ?>" type="hidden" />
    			        <input type="text" data-role="tagsinput" name="name_keyword" value="<?= $keyData['name_keyword'] ?>" id="name_keyword" class="form-control name_keyword" required>
    					
    					<span id="name_keyword-info" class="validation disabling"></span>
    				</div>
    		    </div>
    		    
    		    <div class="col-lg-12">
    		        <div class="form-group">
    					<label for="key_descrip">
    					    <b>Description</b>
    					</label>
    			        <textarea class="form-control key_descrip" name="key_descrip" id="key_descrip" required><?= $keyData['key_descrip'] ?></textarea>
    					<span id="key_descrip-info" class="validation disabling"></span>
    				</div>
    		    </div>
    		    
    			<div class="col-lg-12">
    				<div class="form-group">
    					<label for="key_phone">
    					    <b>Select the phone number utilizing this keyword</b>
    					</label>
    					<select name="key_phone" class="form-control" id="key_phone" required>
    						<option value="" >Select Number</option>
    						
            				<?php foreach( $lcnall as $lcn ){  ?>
                            <option value="<?= $lcn['number'] ?>" <?= ($lcn['number'] == $keyData['key_phone']) ? "selected" : "" ?>><b><?= $lcn['number'] ?></b></option>
                            <?php } ?>
    			        </select>
    					<span id="key_phone-info" class="validation disabling"></span>
    				</div>
    			</div>
    			
    			<div class="col-lg-12">
    				<div class="form-group">
    					<label for="key_workflow">
    					    <b>Choose the workflow triggered by this keyword</b>
    					</label>
    					<select name="key_workflow" class="form-control" id="key_workflow" required>
    						<option value="">Select Pipeline</option>
                            <option value="" disabled><b>LEAD PIPELINE</b></option>
    						<?php foreach($pipesL as $pipeL) { ?>
    			                <option  value="<?= $pipeL['id'] ?>" <?= ($pipeL['id'] == $keyData['key_workflow']) ? "selected" : "" ?>><?= $pipeL['pipeName'] ?></option>
    						<?php } ?>
    			                <option value="" disabled><b>DEAL PIPELINE</b></option>
    					    <?php foreach($pipesD as $pipeD) { ?>
    			                <option  value="<?= $pipeD['id'] ?>" <?= ($pipeD['id'] == $keyData['key_workflow']) ? "selected" : "" ?>><?= $pipeD['pipeName'] ?></option>
    						<?php } ?>
        			    </select>
    					<span id="key_workflow-info" class="validation disabling"></span>
    				</div>
    			</div>
    			
    			<div class="col-lg-12">
    				<div class="form-group">
    					<label for="status">
    					    <b>Change Status</b>
    					</label>
    					<select name="status" class="form-control" id="status" required>
    						<option value="" >Status</option>
    						<option value="1" <?= $keyData['status'] == 1 ? "selected" : "" ?> >Active</option>
    						<option value="0" <?= $keyData['status'] == 0 ? "selected" : "" ?> >In-active</option>
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
    if( isset($_POST['action']) && ($_POST['action'] == "BulkKeysDelete") ) {
        
        if(isset($_POST['keys_id'])){
             $keys_id = trim($_POST['keys_id']);
        }
        
        if(isset($_POST['user_id'])){
            $user_id = $_POST['user_id'];
        }
    
        $Contsarray = explode(',', $keys_id);
        
        if($user_id === $wo['user']['user_id'] ){
            
            $delete = DeletebulkKeywords($keys_id);
        
            if($delete){
               
               $actionTaken = array(
                    'user_id' => $user_id,
                    'page' => "keyword",
                    'action_description' => "Deleted ".count($Contsarray)." Keywords From Keyword Table",
                    'action_type' => "delete",
                );
                
                saveUserActions($actionTaken);
                
               $data = array(
            		'status' => 200,
            		'cts_id' => $keys_id,
            		'message' => count($Contsarray) .' Keywords Successfully Deleted',
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