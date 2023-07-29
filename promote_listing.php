<?php

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);



// get promote listing data (Inputs)
if(isset($_POST['action']) && $_POST['action'] == "promote"){
    
    // All form fields
    $url = $_POST['url'];
    $p_id = $_POST['listing_id'];
    $price = $_POST['price'];
    $title = Wo_Secure($_POST['title']);
    $desc = Wo_Secure($_POST['description']);
    $script_code = base64_encode($_POST['script_code']); 

    if (!isset($_POST['path'])){
        return;
    }

    $path = $_POST['path'];

    
    // Generate the promoted link (Both landpage and Property page)
    $user_id = $wo['user']['user_id'];
    $promote_code = generateRandNum();
    
    // Process the form....
    if($url != "" || $p_id != "" || $price != "" || $title != "" || $desc != ""){

        // validate package with feature deduction number
        $pData = [
            'path' => $path,
            'user_id' => $user_id,
            'userPackage' => $wo['user']['my_package'],
            'number' => 1
        ];
        packageReducerValidator($pData); // will continue if it is true then terminate the code if false

            // Other Variable
            $date = date("Y-m-d g:i:s");
            
            // Image/Logo if set
            // if(!empty($_FILES["logo"]["name"])){
                //     /* Getting file name */
                //     $filename = $_FILES['logo']['name'];
                
                //     /* Location */
                //     $location = "upload/promote-logo/".$filename;
                //     $uploadOk = 1;
                //     $with_error = 0;
                //     $imageFileType = pathinfo($location,PATHINFO_EXTENSION);
                
                //     /* Valid Extensions */
                //     $valid_extensions = array("jpg","jpeg","png");
                //     /* Check file extension */
            
                //     if( !in_array(strtolower($imageFileType),$valid_extensions) ) {
                        
                //         $uploadOk = 0;
                //         $data = array(
                //             'status' => 400,
                //             'message' => 'File type is not allowed, please upload .jpg, and .png files'
                //         );   
                        
                //     }else{
                //         /* Upload file */
                //         if(move_uploaded_file($_FILES['logo']['tmp_name'],$location)){
                //             //echo $location;
                //         }else{
                            
                //             $data = array(
                //                 'status' => 400,
                //                 'message' => 'Image failed upload, please upload a smaller file size'
                //             );  
                            
                //         }
                //     }
                
                
            // }	

            // Check if Url is already choosen
            // if(Wo_DvmCheckSlug( $url ) == true) {
                
            //     $data = array(
            //         'status' => 400,
            //         'message' => 'Sorry URL already taken, please provide a new one.'
            //     );  	
                    
            // }else{
                
                $promote_data = array(
                    'slug' =>   $url,
                    'user' => $user_id,
                    'promote_code' => $promote_code,
                    'listing_id' => $p_id,
                    'price' => $price,
                    'title' => $title,
                    'description' => $desc,
                    'script_code' => $script_code,
                    'logo' => $filename,
                    'dateinsert' => $date,
                );
                
                $promote_id = createPromoteListing($promote_data);
                
                //$query = mysqli_query($sqlConnect, "INSERT INTO `Wo_list_promotion` (`slug`, `user`, `promote_code`, `listing_id`, `price`, `title`, `description`, `script_code`, `logo`, `dateinsert`) VALUES ( '$url', $user_id, '$promote_code', $p_id, $price, '$title', '$desc', '$script_code', '$filename', '$date') ");
                // $promote_id = mysqli_insert_id($sqlConnect);
                        
                    if($promote_id){
                        
                        $actionTaken = array(
                            'user_id' => $user_id,
                            'page' => $path,
                            'action_description' => "Promoted Listing with Property ID of {".$p_id."} With Promotion ID of {".$promote_id."}",
                            'action_type' => "insert",
                        );
                        
                        saveUserActions($actionTaken);
                            
                        $data = array(
                            'status' => 200,
                            'promote_id' => $promote_id,
                            'promote_code' => $promote_code,
                            'message' => 'Successfully Promoted'
                        );
                    
                    
                    }else{
                        $data = array(
                            'status' => 400,
                            'message' => 'Error While processing your Request'
                        );
                    }
            // }
            

    	
    	
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

if(!isset($_POST['action']) && $_POST['action'] == ""){
    
    $data = array(
        'status' => 404,
        'message' => 'Error 404 Page Not Found'
    ); 
        
    header("Content-type: application/json");
    echo json_encode($data);
    die;
    
    
}