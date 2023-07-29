<?php
global $wo, $sqlConnect;
$ds = DIRECTORY_SEPARATOR;

$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);

$storeFolder = '/themes/wondertag/uploads_images';

if (!empty($_FILES)) {
     
    $tempFile = $_FILES['file']['tmp_name'];
      
    $targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;
     
    $targetFile =  $targetPath. $_FILES['file']['name'];
 
    move_uploaded_file($tempFile,$targetFile);
}



if($_POST['action'] == "sort_file"){
    
    $u_id = $_POST['user_id'];
    $p_cod = $_POST['prop_code'];
    
    // database table name
    $t_b =  $_POST['t_b'];
    $t_b = constant($t_b);
    
	$multipel_serialize_images = serialize($_POST['filenames']);

	$query = mysqli_query($sqlConnect, "UPDATE ".$t_b." SET `tab3` = '$multipel_serialize_images' WHERE `propertycode` = '{$p_cod}'");
    
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


if($_POST['action'] == "delete_file"){
    
    $u_id = $_POST['user_id'];
    $p_cod = $_POST['prop_code'];
    $fileName = $_POST['fileName'];
    
    // database table name
    $t_b =  $_POST['t_b'];
    $t_b = constant($t_b);
    
    $queryfilter = mysqli_query($sqlConnect,"SELECT `tab3` FROM ".$t_b." WHERE `propertycode` = '{$p_cod}'");
	$rowfilter = mysqli_fetch_array($queryfilter);
    
    $tab3 = unserialize($rowfilter["tab3"]);
    foreach ($tab3 as $key => $value) {
        if($value==$fileName)
            unset($tab3[$key]);
    }

    $tab3 = serialize($tab3);
    $query = mysqli_query($sqlConnect, "UPDATE ".$t_b."SET `tab3` = '$tab3' WHERE `propertycode` = '{$p_cod}'");
    if($query){
        $targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;
        $filename = $targetPath.$fileName;  
          unlink($filename); 
          exit;
    }
    
    
}

if(isset($_POST['action']) && $_POST['action'] == "Insert_img"){
    
    $property_id = $_POST['propertycode'];
    $property_code = $_POST['propertycode'];
    $user_id = $_POST['user_id'];
    
    // database table name
    $t_b =  $_POST['t_b'];
    $t_b = constant($t_b);
    
        $query_selectcode = "SELECT COUNT(`id`) as `count` FROM ".$t_b." WHERE `propertycode` = '{$property_code}'";
        $sql_code       = mysqli_query($sqlConnect, $query_selectcode);
        $sql_fetch_selectcode = mysqli_fetch_assoc($sql_code);
        $myretuncodenum = $sql_fetch_selectcode['count'];
        
            $first_img = array();
        
            $first_img[] = $_FILES['file']['name'];
            
            $serialize_images = serialize($first_img);
            
        if($myretuncodenum > 0){
            
            $query_one = "SELECT tab3 FROM ".$t_b." WHERE `propertycode` = '{$property_id}'";
            $sql       = mysqli_query($sqlConnect, $query_one);
            $sql_fetch_one = mysqli_fetch_assoc($sql);
            
            if(!empty($sql_fetch_one['tab3'])){
            	$unserialize_data = unserialize($sql_fetch_one['tab3']);
            
            	$pre_data = array($_FILES['file']['name']);
            
            	$total_data = array_merge($unserialize_data, $pre_data);
            
            	$multipel_serialize_images = serialize($total_data);
            
            	$query = mysqli_query($sqlConnect, "UPDATE ".$t_b."  SET `tab3` = '$multipel_serialize_images' WHERE `propertycode` = '{$property_id}'");
            	
            }
            else{
            	$query = mysqli_query($sqlConnect, "UPDATE ".$t_b." SET `tab3` = '$serialize_images' WHERE `propertycode` = '{$property_id}'");
            
            }
        }else{
            
            $query = mysqli_query($sqlConnect, "INSERT INTO ".$t_b."  (`user_id`,`propertycode`, `tab3`) VALUES ({$user_id},'{$property_code}','{$serialize_images}') ");
          
        }
    
}

if($_POST['action'] == "update_sort_file"){
    
    $u_id = $_POST['user_id'];
    $p_id = $_POST['prop_id'];
    
    // database table name
    $t_b =  $_POST['t_b'];
    $t_b = constant($t_b);
    
	$multipel_serialize_images = serialize($_POST['filenames']);

	$query = mysqli_query($sqlConnect, "UPDATE ".$t_b." SET `tab3` = '$multipel_serialize_images' WHERE `id` = $p_id");
    
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


if($_POST['action'] == "update_delete_file"){
    
    $u_id = $_POST['user_id'];
    $p_id = $_POST['prop_id'];
    $fileName = $_POST['fileName'];
    // database table name
    $t_b =  $_POST['t_b'];
    $t_b = constant($t_b);
    
    $queryfilter = mysqli_query($sqlConnect,"SELECT `tab3` FROM ".$t_b." WHERE `id` = $p_id");
	$rowfilter = mysqli_fetch_array($queryfilter);
    
    $tab3 = unserialize($rowfilter["tab3"]);
    foreach ($tab3 as $key => $value) {
        if($value==$fileName)
            unset($tab3[$key]);
    }

    $tab3 = serialize($tab3);
    $query = mysqli_query($sqlConnect, "UPDATE ".$t_b." SET `tab3` = '$tab3' WHERE `id` = $p_id");
    if($query){
        $targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;
        $filename = $targetPath.$fileName;  
          unlink($filename); 
          exit;
    }
    
    
}



if(isset($_POST['action']) && $_POST['action'] == "Update_img"){
  
$property_id = $_POST['property_id'];
$user_id = $_POST['user_id'];

// database table name
    $t_b =  $_POST['t_b'];
    $t_b = constant($t_b);

$query_selectcode = "SELECT COUNT(`id`) as `count` FROM ".$t_b." WHERE `id` = $property_id";
$sql_code       = mysqli_query($sqlConnect, $query_selectcode);
$sql_fetch_selectcode = mysqli_fetch_assoc($sql_code);
$myretuncodenum = $sql_fetch_selectcode['count'];

    $first_img = array();

    $first_img[] = $_FILES['file']['name'];
    
    $serialize_images = serialize($first_img);
    
if($myretuncodenum > 0){
    
    $query_one = "SELECT tab3 FROM ".$t_b." WHERE `id` = $property_id";
    $sql       = mysqli_query($sqlConnect, $query_one);
    $sql_fetch_one = mysqli_fetch_assoc($sql);
    
    if(!empty($sql_fetch_one['tab3'])){
    	$unserialize_data = unserialize($sql_fetch_one['tab3']);
    
    	$pre_data = array($_FILES['file']['name']);
    
    	$total_data = array_merge($unserialize_data, $pre_data);
    
    	$multipel_serialize_images = serialize($total_data);
    
    	$query = mysqli_query($sqlConnect, "UPDATE ".$t_b." SET `tab3` = '$multipel_serialize_images' WHERE `id` = $property_id");
    }
    else{
    	$query = mysqli_query($sqlConnect, "UPDATE ".$t_b." SET `tab3` = '$serialize_images' WHERE `id` = $property_id");
    }
}else{
    
    return false;
}  
    
    
}





?>