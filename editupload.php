<?php
global $wo, $sqlConnect;
$ds          = DIRECTORY_SEPARATOR;
require_once('config.php');
require_once('assets/init.php');

$storeFolder = 'themes/wondertag/uploads_images';
$uploadDir = 'themes/wondertag/uploads_docs';
$uploadDir_jpg = 'themes/wondertag/uploads_docs/converted_image';

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);



if($_POST['action'] == "delete_prop_imgs"){
    
    $u_id = $_POST['user_id'];
    $p_id = $_POST['prop_id'];
    $fileName = $_POST['fileName'];
    
    $queryfilter = mysqli_query($sqlConnect,"SELECT `tab6` FROM `Wo_Listing` WHERE `id` = $p_id");
	$rowfilter = mysqli_fetch_array($queryfilter);
    
    $tab6 = unserialize($rowfilter["tab6"]);
    foreach ($tab6 as $key => $value) {
        if($value==$fileName)
            unset($tab6[$key]);
    }

    $tab6 = serialize( $tab6);
    $query = mysqli_query($sqlConnect, "UPDATE `Wo_Listing` SET `tab6` = '$tab6' WHERE `id` = $p_id");
    if($query){
        $targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;
        $filename = $targetPath.$fileName;  
          unlink($filename); 
          exit;
    }
    
    
}

if($_POST['action'] == "sort_prop_imgs"){
    
    $u_id = $_POST['user_id'];
    $p_id = $_POST['prop_id'];
    
	$multipel_serialize_images = serialize($_POST['filenames']);

	$query = mysqli_query($sqlConnect, "UPDATE `Wo_Listing` SET `tab6` = '$multipel_serialize_images' WHERE `id` = $p_id");
    
    if($query){
        $data = array(
            'status' => 200,
            'msg' => 'yes done'
        );
    }else{
        $data = array(
            'status' => 400,
            'msg' => 'Error'
        );
    }
    
    header("Content-type: application/json");
    echo json_encode($data);
    die;
    
}



if ($_POST['action'] == "upload_prop_imgs") {


    $img_name = "";
    if (!empty($_FILES)) {

        $tempFile = $_FILES['file']['tmp_name'];
        $img_name = $_FILES['file']['name'];

        $img_name = str_replace(' ', '-', $img_name);
        $additional = "no-wm-";
        if (strpos($img_name, $additional) === 0) {
            $img_name = str_replace($additional, "", $img_name);
        }

        $additional_img_name = $additional . $_FILES['file']['name'];

        $targetPath = dirname(__FILE__) . $ds . $storeFolder . $ds;

        $targetFile =  $targetPath . $img_name;
        $additionalTargetFile =  $targetPath . $additional_img_name;

        if (copy($tempFile, $targetFile) && copy($tempFile, $additionalTargetFile)) {
            pasteWatermarkOnImage($additionalTargetFile);
        }
    }

    $property_id = $_POST['property_id'];
    $user_id = $_POST['user_id'];

    $first_img = array();

    $first_img[] = $img_name;
    $first_img[] = $additional_img_name;

    $serialize_images = serialize($first_img);

    $query_one = "SELECT tab6 FROM `Wo_Listing` WHERE `id` = '{$property_id}'";
    $sql       = mysqli_query($sqlConnect, $query_one);
    $sql_fetch_one = mysqli_fetch_assoc($sql);

    if (!empty($sql_fetch_one['tab6'])) {
        $unserialize_data = unserialize($sql_fetch_one['tab6']);
        $pre_data = array($img_name, $additional_img_name);
        $total_data = array_merge($unserialize_data, $pre_data);
        $multipel_serialize_images = serialize($total_data);
        $query = mysqli_query($sqlConnect, "UPDATE `Wo_Listing` SET `tab6` = '$multipel_serialize_images' WHERE `id` = '{$property_id}'");
    } else {
        $query = mysqli_query($sqlConnect, "UPDATE `Wo_Listing` SET `tab6` = '$serialize_images' WHERE `id` = '{$property_id}'");
    }
}


if($_POST['action'] == "upload_prop_docs"){
    // Upload Property Comps Docs
    
    			
	if(!empty($_FILES["file"]["name"])){
	    $fileName = basename($_FILES["file"]["name"]); 
        $targetFilePath = dirname( __FILE__ ) . $ds. $uploadDir . $ds . $fileName; 
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        
        $allowTypes = array('pdf'); 
        if(in_array($fileType, $allowTypes)){ 
            // Upload file to the server 
            if(move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)){
                 Wo_convert_pdf_to_image($targetFilePath);
                $uploadedFile = $fileName; 
            }
        }
        
    }
    
    $property_id = $_POST['property_id'];
    $user_id = $_POST['user_id'];

    $first_img = array();

    $first_img[] = $_FILES['file']['name'];
    
    $serialize_images = serialize($first_img);

    $query_one = "SELECT tab7 FROM `Wo_Listing` WHERE `id` = '{$property_id}'";
    $sql       = mysqli_query($sqlConnect, $query_one);
    $sql_fetch_one = mysqli_fetch_assoc($sql);
    
    if(!empty($sql_fetch_one['tab7'])){
    	$unserialize_data = unserialize($sql_fetch_one['tab7']);
    
    	$pre_data = array($_FILES['file']['name']);
    
    	$total_data = array_merge($unserialize_data, $pre_data);
    
    	$multipel_serialize_images = serialize($total_data);
    
    	$query = mysqli_query($sqlConnect, "UPDATE `Wo_Listing` SET `tab7` = '$multipel_serialize_images' WHERE `id` = '{$property_id}'");
    }
    else{
    	$query = mysqli_query($sqlConnect, "UPDATE `Wo_Listing` SET `tab7` = '$serialize_images' WHERE `id` = '{$property_id}'");
    }
    
    

}
    


if($_POST['action'] == "delete_prop_docs"){
    
    $u_id = $_POST['user_id'];
    $p_id = $_POST['prop_id'];
    $fileName = $_POST['fileName'];
    
    $queryfilter = mysqli_query($sqlConnect,"SELECT `tab7` FROM `Wo_Listing` WHERE `id` = '{$p_id}'");
	$rowfilter = mysqli_fetch_array($queryfilter);
    
    $tab7 = unserialize($rowfilter["tab7"]);
    foreach ($tab7 as $key => $value) {
        if($value==$fileName)
            unset($tab7[$key]);
    }

    $tab7 = serialize( $tab7);
    $query = mysqli_query($sqlConnect, "UPDATE `Wo_Listing` SET `tab7` = '$tab7' WHERE `id` = '{$p_id}'");
    if($query){
        // Get image and document directory
        $targetPath = dirname( __FILE__ ) . $ds. $uploadDir . $ds;
        $targetPath_jpg = dirname( __FILE__ ) . $ds. $uploadDir_jpg . $ds;
        
        // set document full file name dicrectory
        $filename = $targetPath.$fileName;  
        
        // Split doc name for image view
        $allpath = pathinfo($fileName);
        $filenewname = $allpath['filename'];
        $imagefileName = $targetPath_jpg.$filenewname.'.jpg';
        
        // Delete files
          unlink($filename);
          unlink($imagefileName);
          exit;
    }
    
    
}


?>