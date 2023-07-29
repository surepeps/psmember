<?php
global $wo, $sqlConnect;
$ds = DIRECTORY_SEPARATOR;

$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);
$storeFolder = 'themes/wondertag/uploads_images';
$uploadDir = 'themes/wondertag/uploads_docs';
$uploadDir_jpg = 'themes/wondertag/uploads_docs/converted_image';

if($_POST['action'] == "sort_prop_imgs"){
    
    $u_id = $_POST['user_id'];
    $p_cod = $_POST['prop_code'];
    
	$multipel_serialize_images = serialize($_POST['filenames']);

	$query = mysqli_query($sqlConnect, "UPDATE `Wo_Listing` SET `tab6` = '$multipel_serialize_images' WHERE `propertycode` = '{$p_cod}'");
    
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


if($_POST['action'] == "delete_prop_imgs"){
    
    $u_id = $_POST['user_id'];
    $p_cod = $_POST['prop_code'];
    $fileName = $_POST['fileName'];
    
    $queryfilter = mysqli_query($sqlConnect,"SELECT `tab6` FROM `Wo_Listing` WHERE `propertycode` = '{$p_cod}'");
	$rowfilter = mysqli_fetch_array($queryfilter);
    
    $tab6 = unserialize($rowfilter["tab6"]);
    
    if(strpos($fileName, $p_cod) == false) {
        $fileName = $p_cod . '_' . $fileName;
    }
    
    foreach ($tab6 as $key => $value) {
        if($value==$fileName)
            unset($tab6[$key]);
    }

    $tab6 = serialize( $tab6);
    $query = mysqli_query($sqlConnect, "UPDATE `Wo_Listing` SET `tab6` = '$tab6' WHERE `propertycode` = '{$p_cod}'");
    if($query){
        $targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;
        $filename = $targetPath.$fileName;  
          unlink($filename); 
          exit;
    }
    
    
}




if ($_POST['action'] == "upload_prop_imgs") {
    // Upload Property Images

    $tempFile = "";
    if (!empty($_FILES)) {

        $tempFile = $_FILES['file']['tmp_name'];
        $img_name = $_FILES['file']['name'];


        showPhpErrors();
        $additional = "no-wm-";
        if (strpos($img_name, $additional) === 0) {
            $img_name = str_replace($additional, "", $img_name);
        }

        $additional_img_name = $additional . $_FILES['file']['name'];
        $img_name = str_replace(' ', '-', $img_name);
        $targetPath = dirname(__FILE__) . $ds . $storeFolder . $ds;
        $targetFile =  $targetPath . $img_name;
        $additionalTargetFile =  $targetPath . $additional_img_name;

        if (copy($tempFile, $targetFile) && copy($tempFile, $additionalTargetFile)) {
            pasteWatermarkOnImage($additionalTargetFile);
        }
    }
    // Insert into database
    $property_id = $_POST['propertycode'];
    $property_code = $_POST['propertycode'];
    $user_id = $_POST['user_id'];

    $query_selectcode = "SELECT COUNT(`id`) as `count` FROM `Wo_Listing` WHERE `propertycode` = '{$property_code}'";
    $sql_code       = mysqli_query($sqlConnect, $query_selectcode);
    $sql_fetch_selectcode = mysqli_fetch_assoc($sql_code);

    $myretuncodenum = $sql_fetch_selectcode['count'];

    $first_img = array();

    $first_img[] = $img_name;
    $first_img[] = $additional_img_name;

    $serialize_images = serialize($first_img);

    if ($myretuncodenum > 0) {

        $query_one = "SELECT tab6 FROM `Wo_Listing` WHERE `propertycode` = '{$property_id}'";
        $sql = mysqli_query($sqlConnect, $query_one);
        $sql_fetch_one = mysqli_fetch_assoc($sql);

        if (!empty($sql_fetch_one['tab6'])) {
            $unserialize_data = unserialize($sql_fetch_one['tab6']);
            $pre_data = array($img_name, $additional_img_name);
            $total_data = array_merge($unserialize_data, $pre_data);
            $multipel_serialize_images = serialize($total_data);
            $query = mysqli_query($sqlConnect, "UPDATE `Wo_Listing` SET `tab6` = '$multipel_serialize_images' WHERE `propertycode` = '{$property_id}'");
        } else {
            $query = mysqli_query($sqlConnect, "UPDATE `Wo_Listing` SET `tab6` = '$serialize_images' WHERE `propertycode` = '{$property_id}'");
        }
    } else {

        $query = mysqli_query($sqlConnect, "INSERT INTO `Wo_Listing` (`user_id`,`propertycode`, `tab6`) VALUES ({$user_id},'{$property_code}','{$serialize_images}') ");
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
                
                
    // Insert into database
    $property_id = $_POST['propertycode'];
    $property_code = $_POST['propertycode'];
    $user_id = $_POST['user_id'];
    
    $query_selectcode = "SELECT COUNT(`id`) as `count` FROM `Wo_Listing` WHERE `propertycode` = '{$property_code}'";
    $sql_code       = mysqli_query($sqlConnect, $query_selectcode);
    $sql_fetch_selectcode = mysqli_fetch_assoc($sql_code);
    $myretuncodenum = $sql_fetch_selectcode['count'];
    
        $first_img = array();
    
        $first_img[] = $_FILES['file']['name'];
        
        $serialize_images = serialize($first_img);
        
    if($myretuncodenum > 0){
        
        $query_one = "SELECT tab7 FROM `Wo_Listing` WHERE `propertycode` = '{$property_id}'";
        $sql       = mysqli_query($sqlConnect, $query_one);
        $sql_fetch_one = mysqli_fetch_assoc($sql);
        
        if(!empty($sql_fetch_one['tab7'])){
        	$unserialize_data = unserialize($sql_fetch_one['tab7']);
        
        	$pre_data = array($_FILES['file']['name']);
        
        	$total_data = array_merge($unserialize_data, $pre_data);
        
        	$multipel_serialize_images = serialize($total_data);
        
        	$query = mysqli_query($sqlConnect, "UPDATE `Wo_Listing` SET `tab7` = '$multipel_serialize_images' WHERE `propertycode` = '{$property_id}'");
        }
        else{
        	$query = mysqli_query($sqlConnect, "UPDATE `Wo_Listing` SET `tab7` = '$serialize_images' WHERE `propertycode` = '{$property_id}'");
        }
    }else{
        
        $query = mysqli_query($sqlConnect, "INSERT INTO `Wo_Listing` (`user_id`,`propertycode`, `tab7`) VALUES ({$user_id},'{$property_code}','{$serialize_images}') ");
    }
}


if($_POST['action'] == "delete_prop_docs"){
    
    $u_id = $_POST['user_id'];
    $p_cod = $_POST['prop_code'];
    $fileName = $_POST['fileName'];
    
    $queryfilter = mysqli_query($sqlConnect,"SELECT `tab7` FROM `Wo_Listing` WHERE `propertycode` = '{$p_cod}'");
	$rowfilter = mysqli_fetch_array($queryfilter);
    
    $tab7 = unserialize($rowfilter["tab7"]);
    foreach ($tab7 as $key => $value) {
        if($value==$fileName)
            unset($tab7[$key]);
    }

    $tab7 = serialize( $tab7);
    $query = mysqli_query($sqlConnect, "UPDATE `Wo_Listing` SET `tab7` = '$tab7' WHERE `propertycode` = '{$p_cod}'");
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