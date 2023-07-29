<?php
global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);

$ds = DIRECTORY_SEPARATOR;

$storeFolder = '/themes/wondertag/upload_lists';

$user_id = $wo['user']['user_id'];

if(isset($_POST['list_id']) || isset($_POST['file'])){
 $list_id = $_POST['list_id'];
//  Upload file into themes/wondertag/upload_lists folder
if(!empty($list_id)){
    if(!empty($_FILES["file"]["name"])){
        
        // Confirm if file sent is a csv file`
        $fileName = basename($_FILES["file"]["name"]); 
        $targetFilePath = $storeFolder. $ds . $fileName; 
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION); 
        
        // Allow certain file formats 
        $allowTypes = array('csv'); 
        
        if(in_array($fileType, $allowTypes)){
            $realPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;
            $completePath = $realPath . $_FILES["file"]["name"];
            
            // Upload file to the server 
            if(move_uploaded_file($_FILES["file"]["tmp_name"], $completePath)){ 
                
                    // Allowed mime types
                        $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
                        if(in_array($_FILES['file']['type'], $csvMimes)){
                            
                            // Open uploaded CSV file with read-only mode
                            $csvFile = fopen($_FILES['file']['tmp_name'], 'r');
                            
                            // Skip the first line
                            $csv = file($completePath, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
                            //$csv = array_map('str_getcsv', file($completePath));
                            
                            $count = 1;
                            
                            $num_rows = count($csv);
                             // Parse data from CSV file line by line
                            foreach($csv as $line){
                                $csvl = str_getcsv($line);
                                
                                if (empty($csvl[2])) {
                                    $num_rows--;
                                    
                                }else{
                                    
                                   if ($count == 1) {
                                          $count++;
                                          continue;
                                    }
                                    
                                    $FirstName  = $csvl[0];
                                    $LastName = $csvl[1];
                                    $Email = $csvl[2];
                                    
                                    
                                    $createEmail = mysqli_query($sqlConnect, "INSERT INTO `My_Email_Lists` (`user_id`, `list_id`, `List_FirstName`, `List_LastName`, `List_Email`, `date_created`) VALUES ($user_id, $list_id, '$FirstName', '$LastName', '$Email', now() )");
                                    
                                
                                    if($createEmail){
                                        
                                        $data = array(
                                            'status' => 200,
                                            'message' => 'Successfully Process Lists'
                                        );
                                    }else{
                                        $data = array(
                                            'status' => 400,
                                            'message' => 'Error While Processing Your List'
                                        );
                                    }
                                    
                                }
                                
                                
                                
                            }
                            
                            
                        }else{
                            $data = array(
                                'status' => 400,
                                'message' => 'Sorry, File Extension not found.' 
                            );
                        }
            }else{
                $data = array(
                    'status' => 400,
                    'message' => 'Sorry, there was an error uploading your file.' 
                );
            } 
        }else{
            $data = array(
                'status' => 400,
                'message' => 'Sorry, only CSV files are allowed to upload.' 
            );
        } 

      
    }
    
    
    
    
    
}else{
    $data = array(
        'status' => 400,
        'message' => 'Sorry, List Name Required.'
    );
}


}


if(isset($_POST['action']) && $_POST['action'] == "Single_Email_List"){
    $list_id = $_POST['list_id'];
    $FirstName = $_POST['fN'];
    $LastName = $_POST['lN'];
    $Email = $_POST['eM'];
    
    
     $createEmail1 = mysqli_query($sqlConnect, "INSERT INTO `My_Email_Lists` (`user_id`, `list_id`, `List_FirstName`, `List_LastName`, `List_Email`, `date_created`) VALUES ($user_id, $list_id, '$FirstName', '$LastName', '$Email', now() )");
                                
                            
    if($createEmail1){
        
        $data = array(
            'status' => 200,
            'message' => 'Successfully Added Email Contact To List'
        );
    }else{
        $data = array(
            'status' => 400,
            'message' => 'Error While Processing Your Email Contact details'
        );
    }
    
    
    
}



if(isset($_POST['action']) && $_POST['action'] == "Get_Single_Email_List"){
    $email_id = $_POST['email_id'];
    
    
     $getEmailDetails = mysqli_query($sqlConnect, "SELECT * FROM `My_Email_Lists` WHERE id = $email_id AND user_id = $user_id");
     $get_e_d = mysqli_fetch_array($getEmailDetails);                          
                            
    if($getEmailDetails){
        
        $data = array(
            'status' => 200,
            'firstName' => $get_e_d['List_FirstName'],
            'lastName' => $get_e_d['List_LastName'],
            'email' => $get_e_d['List_Email'],
            'e_id' => $get_e_d['id'],
            'message' => 'Success'
        );
    }else{
        $data = array(
            'status' => 400,
            'message' => 'Error While Getting Email details'
        );
    }
    
    
    
}


if(isset($_POST['action']) && $_POST['action'] == "Single_Edit_List"){
    $email_id = $_POST['email_id'];
    $FirstName = $_POST['fN'];
    $LastName = $_POST['lN'];
    $Email = $_POST['eM'];
    
    
     $updateEmail = mysqli_query($sqlConnect, "UPDATE `My_Email_Lists` SET `List_FirstName` = '$FirstName', `List_LastName` = '$LastName', `List_Email` = '$Email', `date_created` = now() WHERE user_id = $user_id AND id = $email_id");
                                
                            
    if($updateEmail){
        
        $data = array(
            'status' => 200,
            'message' => 'Successfully Updated Email Contact'
        );
    }else{
        $data = array(
            'status' => 400,
            'message' => 'Error While Processing Your Email Contact details'
        );
    }
    
    
    
}



header("Content-type: application/json");
        echo json_encode($data);
        die();


?>
