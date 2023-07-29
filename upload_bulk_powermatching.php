<?php
global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);

$ds = DIRECTORY_SEPARATOR;

$storeFolder = '/themes/wondertag/upload_power_matching';

$user_id = $wo['user']['user_id'];

if(isset($user_id) || isset($_POST['file'])){
    
//  Upload file into themes/wondertag/upload_lists folder
if(!empty($user_id)){
        
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
                            $csv = array_map('str_getcsv', file($completePath));
                            
                            $count = 1;
                             // Parse data from CSV file line by line
                            foreach($csv as $line){
                                
                                if ($count == 1) {
                                      $count++;
                                      continue;
                                }
                                
                                $buyer_name  = $line[0];
                                $buyer_email = $line[1];
                                $buyer_phone_number = $line[2];
                                $city = $line[3];
                                
                                $amount1 = preg_replace("/[^0-9.]/", "", $line[4]);
                                $amount2 = preg_replace("/[^0-9.]/", "", $line[5]);
                                
                                $bed = $line[6];
                                $bath = $line[7];
                                $date = date('Y-m-d H:i:s');
                                
                                // Convert city, beds, baths to database fromat
                                $prop_rooms = str_replace(",",":",$bed);
                                $prop_bathroom = str_replace(",",":",$bath);
                                
                                $thePostIdArray = explode(',', $city);
                                $cityjson = json_encode($thePostIdArray);
                                
                                $query_one   = "INSERT INTO Wo_Buyerinfo (`user_id`, `city`, `beds`, `time`, `min_price`, `max_price`,`bath`,`buyer_name`,`buyer_email`,`buyer_phone_number`)VALUES ({$user_id},'{$cityjson}','{$prop_rooms}','{$date}','{$amount1}','{$amount2}','{$prop_bathroom}','{$buyer_name}','{$buyer_email}','{$buyer_phone_number}')";
			                    $mymessage = "Buyer Criteria(s) Added Successfully";
                                
                                $sql_query = mysqli_query($sqlConnect, $query_one);
                            
                                if($sql_query){
                                    
                                    $data = array(
                                        'status' => 200,
                                        'message' => $mymessage
                                    );
                                }else{
                                    $data = array(
                                        'status' => 400,
                                        'message' => 'Error While Processing Your Buyer Criteria(s)'
                                    );
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

     
    
    
    
    
}else{
    $data = array(
        'status' => 400,
        'message' => 'Sorry, List Name Required.'
    );
}


}else{
     $data = array(
        'status' => 400,
        'message' => 'Sorry, File Required.'
    );
}






header("Content-type: application/json");
        echo json_encode($data);
        die();


?>
