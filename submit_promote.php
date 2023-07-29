<?php

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);



if($_POST['action']=="delete_promote"){
    
	 mysqli_query($sqlConnect,"DELETE FROM `Wo_list_promotion` WHERE ID ='".$_POST['eid']."' ");
	 
	 $packId = $wo['user']['my_package'];
     $num_conts = 1;
     $path = "promoted-listings";
     $user_id = $wo['user']['user_id'];
    
	 $up = getUserPackages($user_id,$packId,$path);
	 if(is_numeric($up[$path]) && $up[$path] >= 0 ){
	     SpecialPackReducer($user_id,$path,$num_conts,$packId,2);
	 }

	 echo "success";
}

if($_POST['action']=="update_promote"){
	$location = "none";
	if(!empty($_FILES["logo"]["name"])){
	/* Getting file name */
	$filename = $_FILES['logo']['name'];
 
	/* Location */
	$location = "upload/promote-logo/".$filename;
	$uploadOk = 1;
	$with_error = 0;
	$imageFileType = pathinfo($location,PATHINFO_EXTENSION);

	/* Valid Extensions */
	$valid_extensions = array("jpg","jpeg","png");
	/* Check file extension */

		if( !in_array(strtolower($imageFileType),$valid_extensions) ) {
		   $uploadOk = 0;
		   $with_error = 1;
		   $error_message.="File type is not allowed, please upload .jpg, and .png files.<br>";   
		}else{
		   /* Upload file */
		   if(move_uploaded_file($_FILES['logo']['tmp_name'],$location)){
			  //echo $location;
		   }else{
			   $with_error = 1;
			   $error_message.="Image failed upload, please upload a smaller file size.<br>";   
		   }
		}
	}	


	if($_POST['url']!=$_POST['cslug']){
		if(Wo_DvmCheckSlug($_POST['url'])==true){
			   $with_error = 1;
			   $error_message.="URL already taken, please provide a new one.<br>";   	
		}
    }
	
	if($with_error==0){
		
		$eid = $_POST['eid'];
		
		$price = $_POST['price'];
		$title = Wo_Secure($_POST['title']);
		$description = Wo_Secure($_POST['description']);
		$slug = Wo_Secure($_POST['cslug']);
		
		$dataPro = array(
		    'title' => Wo_Secure($_POST['title']),
		    'price' => $_POST['price'],
		    'description' => Wo_Secure($_POST['description']),
		    'slug' => Wo_Secure($_POST['cslug'])
		);
		
        //
        $updateProperty = UpdatePromotedProperty($dataPro,$eid);
        
        
        if($updateProperty){
            
            
            if(!empty($_FILES["logo"]["name"])){
    			mysqli_query($sqlConnect,"UPDATE `Wo_list_promotion` SET logo ='$filename' WHERE ID ='$eid' ");
    			
    		 }
            
            $actionTaken = array(
                'user_id' => $user_id,
                'page' => $path,
                'action_description' => "Promoted Listing with Property ID of {".$p_id."} With Promotion ID of {".$promote_id."}",
                'action_type' => "update",
            );
            
            saveUserActions($actionTaken);
            
            header("Content-type: application/json");
    		 $data = array(
    			'status' => 'success',
    			'new_logo' => $location,														
    			);
    
           echo json_encode($data);	
        }

		
	}else{
		 header("Content-type: application/json");
		 $data = array(
			'status' => $error_message,
			'new_logo' => '',														
			);
			echo json_encode($data);		
	  // echo $error_message;
	}
	//print_r($_POST);

}

if(isset($_POST['action']) && $_POST['action'] == "promote"){
    // All form fields
    $url = $_POST['url'];
    $p_id = $_POST['listing_id'];
    $price = $_POST['price'];
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $script_code = base64_encode($_POST['script_code']);
    $user_id = $wo['user']['user_id'];
    
    if($url != "" || $p_id != "" || $price != "" || $title != "" || $desc != ""){
    
        // Other Variable
        $date = date("Y-m-d g:i:s");
        
        // Image/Logo if set
    	if(!empty($_FILES["logo"]["name"])){
    	/* Getting file name */
    	$filename = $_FILES['logo']['name'];
    
    	/* Location */
    	$location = "upload/promote-logo/".$filename;
    	$uploadOk = 1;
    	$with_error = 0;
    	$imageFileType = pathinfo($location,PATHINFO_EXTENSION);
    
    	/* Valid Extensions */
    	$valid_extensions = array("jpg","jpeg","png");
    	/* Check file extension */
    
    		if( !in_array(strtolower($imageFileType),$valid_extensions) ) {
    		    
    		   $uploadOk = 0;
    		   
    		   $data = array(
                    'status' => 400,
                    'message' => 'File type is not allowed, please upload .jpg, and .png files'
                );   
                
    		}else{
    		   /* Upload file */
    		   if(move_uploaded_file($_FILES['logo']['tmp_name'],$location)){
    			  //echo $location;
    		   }else{
    		       
    			   $data = array(
                        'status' => 400,
                        'message' => 'Image failed upload, please upload a smaller file size'
                    );  
                    
    		   }
    		}
    	}	
    
            
    	
    	

    	   // Check if Promotion can be proceed...
    	    $count = Wo_get_features_count('promote'); 
    	    
    	    if($count > 0){
    	        
    	            // Check if Url is already choosen
                	if(Wo_DvmCheckSlug( $url )==true) {
                	    
                		   $data = array(
                                'status' => 400,
                                'message' => 'Sorry URL already taken, please provide a new one.'
                            );  	
                            
                	}else{
                	    $query = mysqli_query($sqlConnect, "INSERT INTO `Wo_list_promotion` (`slug`, `user`, `listing_id`, `price`, `title`, `description`, `script_code`, `logo`, `dateinsert`) VALUES ( '$url', $user_id, $p_id, $price, '$title', '$desc', '$script_code', '$filename', '$date') ");
        					 
                    	 if($query){
                    	     
                    	    $data = array(
                                'status' => 200,
                                'message' => 'Successfully Promoted'
                            );
                            
                    	  
                    	 }else{
                    	    $data = array(
                                'status' => 400,
                                'message' => 'Error While processing your Request'
                            );
                    	 }
                	}
        	      
    	    }else{
    	        
    	        $data = array(
                    'status' => 401,
                    'message' => 'over_limit'
                );
    	    }
    		 
    	
    	
    }else{
        $data = array(
            'status' => 400,
            'message' => 'Error! Please Fill in all required Form Fields'
        ); 
    }
    
    
    header("Content-type: application/json");
    echo json_encode($data);
    die;


}


	
	?>