<?php 

global $wo, $sqlConnect;
$root = $_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');


// Create New Contact
if( isset($_POST['action']) && ($_POST['action'] == "addContactAction") ) {
    
    if(isset($_POST['fname'])){
        $fname = test_input($_POST['fname']);
    }
    
    if(isset($_POST['lname'])){
        $lname = test_input($_POST['lname']);
    }
    
    if(isset($_POST['mobile'])){
        $mobile = test_input($_POST['mobile']);
    }
    
    if(isset($_POST['email'])){
        $email = test_input($_POST['email']);
    }
    
    if(isset($_POST['prstreetadd'])){
        $prstreetadd = test_input($_POST['prstreetadd']);
    }
    
    if(isset($_POST['prcity'])){
        $prcity = test_input($_POST['prcity']);
    }
    
    if(isset($_POST['prstate'])){
        $prstate = test_input($_POST['prstate']);
    }
    
    if(isset($_POST['przip'])){
        $przip = test_input($_POST['przip']);
    }
    
    if(isset($_POST['taxmailstreetadd'])){
        $taxmailstreetadd = test_input($_POST['taxmailstreetadd']);
    }
    
    if(isset($_POST['taxmailcity'])){
        $taxmailcity = test_input($_POST['taxmailcity']);
    }
    
    if(isset($_POST['taxmailstate'])){
        $taxmailstate = test_input($_POST['taxmailstate']);
    }
    
    if(isset($_POST['taxmailzip'])){
        $taxmailzip = test_input($_POST['taxmailzip']);
    }
    
    if(isset($_POST['otherstreetadd'])){
        $otherstreetadd = test_input($_POST['otherstreetadd']);
    }
    
    if(isset($_POST['othercity'])){
        $othercity = test_input($_POST['othercity']);
    }
    
    if(isset($_POST['otherstate'])){
        $otherstate = test_input($_POST['otherstate']);
    }
    
    if(isset($_POST['otherzip'])){
        $otherzip = test_input($_POST['otherzip']);
    }
    
    if(isset($_POST['user_id'])){
        $user_id = test_input($_POST['user_id']);
    }
    
    // package inputs
    if(isset($_POST['path'])){
        $path = $_POST['path'];
    }
    

    if($user_id == $wo['user']['user_id']){
        
        // Process form 
        $contForm = array(
            'firstname' => $fname,
            'lastname' => $lname,
            'mobile' => $mobile,
            'email' => $email,
            'prstreetadd' => $prstreetadd,
            'prcity' => $prcity,
            'prstate' => $prstate,
            'otherstate' => $otherstate,
            'otherzip' => $otherzip,
            'przip' => $przip,
            'taxmailstreetadd' => $taxmailstreetadd,
            'taxmailcity' => $taxmailcity,
            'taxmailstate' => $taxmailstate,
            'taxmailzip' => $taxmailzip,
            'otherstreetadd' => $otherstreetadd,
            'othercity' => $othercity,
            'contactinsertedby' => $user_id,
            'createddate' => date('Y-m-d h:i:s'),
			'updateddate' => date('Y-m-d h:i:s'),
			'status' => 0,
            'type' => 1,
        );
        
        // check if some required fields are empty
        if($fname != '' && $lname != '' && $email != ''){

            // validate package with feature deduction number
            $pData = [
                'path' => $path,
                'user_id' => $user_id,
                'userPackage' => $wo['user']['my_package'],
                'number' => 1
            ];
            packageReducerValidator($pData); // will continue if it is true then terminate the code if false


            // Insert Contact details into database.....
                $query = CreateNewContactData($contForm);
                
                if($query > 0){
                    
                   $actionTaken = array(
                        'user_id' => $user_id,
                        'page' => $path,
                        'action_description' => "Created New Contact Details With Contact ID of {".$query."}",
                        'action_type' => "insert",
                    );
                    
                    saveUserActions($actionTaken);
                
                   $data = array(
                		'status' => 200,
                		'cont_id' => $query,
                		'message' => 'Successfully Created Contact',
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
        		'message' => 'Please Provide First Name, Last Name and Email',
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

// Create New Property
if( isset($_POST['action']) && ($_POST['action'] == "addPropertyAction") ) {
    
    if(isset($_POST['firstname'])){
        $firstname = test_input($_POST['firstname']);
    }
    
    if(isset($_POST['email'])){
        $email = test_input($_POST['email']);
    }
    
    if(isset($_POST['mobile'])){
        $mobile = test_input($_POST['mobile']);
    }
    
    if(isset($_POST['prstreetadd'])){
        $prstreetadd = test_input($_POST['prstreetadd']);
    }
    
    if(isset($_POST['prcity'])){
        $prcity = test_input($_POST['prcity']);
    }
    
    if(isset($_POST['prstate'])){
        $prstate = test_input($_POST['prstate']);
    }
    
    if(isset($_POST['przip'])){
        $przip = test_input($_POST['przip']);
    }
    
    if(isset($_POST['sqft'])){
        $sqft = test_input($_POST['sqft']);
    }
    
    if(isset($_POST['garage'])){
        $garage = test_input($_POST['garage']);
    }
    
    if(isset($_POST['year_built'])){
        $year_built = test_input($_POST['year_built']);
    }
    
    if(isset($_POST['estimated_arv'])){
        $estimated_arv = test_input($_POST['estimated_arv']);
    }
    
    if(isset($_POST['estimated_repairs'])){
        $estimated_repairs = test_input($_POST['estimated_repairs']);
    }
    
    if(isset($_POST['offer_amount'])){
        $offer_amount = test_input($_POST['offer_amount']);
    }
    
    if(isset($_POST['compos_3'])){
        $compos_3 = test_input($_POST['compos_3']);
    }
    
    if(isset($_POST['lockbox_code'])){
        $lockbox_code = test_input($_POST['lockbox_code']);
    }
    
    if(isset($_POST['beds'])){
        $beds = test_input($_POST['beds']);
    }
    
    if(isset($_POST['bath'])){
        $bath = test_input($_POST['bath']);
    }
    
    if(isset($_POST['property_type'])){
        $property_type = test_input($_POST['property_type']);
    }
    
    if(isset($_POST['occupancy_type'])){
        $occupancy_type = test_input($_POST['occupancy_type']);
    }
    
    if(isset($_POST['taxmailstreetadd'])){
        $taxmailstreetadd = test_input($_POST['taxmailstreetadd']);
    }
    
    if(isset($_POST['buyer_id'])){
        $buyer_id = test_input($_POST['buyer_id']);
    }
    
    if(isset($_POST['contact_id'])){
        $contact_id = test_input($_POST['contact_id']);
    }
    
    if(isset($_POST['buying_strategy'])){
        $buying_strategy = test_input($_POST['buying_strategy']);
    }
    
    if(isset($_POST['deal_notes'])){
        $deal_notes = test_input($_POST['deal_notes']);
    }
    
    if(isset($_POST['user_id'])){
        $user_id = test_input($_POST['user_id']);
    }
    
    if(isset($_POST['path'])){
        $path = $_POST['path'];
    }

    if($user_id == $wo['user']['user_id']){
        
        // Process form 
        $propForm = array(
            'firstname' => $firstname,
            'email' => $email,
            'mobile' => $mobile,
            'prstreetadd' => $prstreetadd,
            'prcity' => $prcity,
            'prstate' => $prstate,
            'przip' => $przip,
            'sqft' => $sqft,
            'garage' => $garage,
            'year_built' => $year_built,
            'estimated_arv' => $estimated_arv,
            'estimated_repairs' => $estimated_repairs,
            'offer_amount' => $offer_amount,
            'compos_3' => $compos_3,
            'lockbox_code' => $lockbox_code,
            'beds' => $beds,
            'bath' => $bath,
            'property_type' => $property_type,
            'occupancy_type' => $occupancy_type,
            'taxmailstreetadd' => $taxmailstreetadd,
            'buyer_id' => $buyer_id,
            'contact_id' => $contact_id,
            'buying_strategy' => $buying_strategy,
            'deal_notes' => $deal_notes,
            'contactinsertedby' => $user_id,
            'createddate' => date('Y-m-d h:i:s'),
			'updateddate' => date('Y-m-d h:i:s'),
			'status' => 0,
            'type' => 3,
        );
        
        
        // check if some required fields are empty
        if($firstname != '' && $email != ''){

                // validate package with feature deduction number
                $pData = [
                    'path' => $path,
                    'user_id' => $user_id,
                    'userPackage' => $wo['user']['my_package'],
                    'number' => 1
                ];
                packageReducerValidator($pData); // will continue if it is true then terminate the code if false
        
                // Insert Contact details into database.....
                $query = CreateNewContactData($propForm);
                
                if($query > 0){
                    
                    $actionTaken = array(
                        'user_id' => $user_id,
                        'page' => $path,
                        'action_description' => "Created New Property Details With Property ID of {".$query."}",
                        'action_type' => "insert",
                    );
                    
                    saveUserActions($actionTaken);
                
                   $data = array(
                		'status' => 200,
                		'prop_id' => $query,
                		'message' => 'Successfully Created Property',
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
        		'message' => 'Please Provide Full Name and Email',
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

// No action send to the server
if( !isset($_POST['action']) || ($_POST['action'] == "") ) {
    
    $data = array(
		'status' => 404,
		'message' => 'Error!, Page Not Found',
	);
    	
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
}
