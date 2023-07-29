<?php
global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);


if(isset($_POST['action']) && $_POST['action'] == "lat_nd_lang"){ 
    $lat = $_POST['lat'];
    $lang = $_POST['lang'];
    
    $pro_id = $_POST['property_id'];
    if($pro_id < 0 || $pro_id == 0 || $pro_id == ''){
        return false;
    }
    
    // Listing database
    $query  = "UPDATE Wo_Listing SET `lat`='{$lat}', `lang` = '{$lang}' WHERE id =".$pro_id;
    $sql_query = mysqli_query($sqlConnect, $query);
    
    // Search listing database
    $filtquery2  = "UPDATE Wo_Filter SET `lat`='{$lat}', `lang`='{$lang}' WHERE property_id=".$pro_id;
    $sql_queryfilty2 = mysqli_query($sqlConnect, $filtquery2);
    
    if($sql_query){
        $data = array(
            'status' => 200,
            'message' => 'Success'
        );
    }else{
        $data = array(
            'status' => 400,
            'message' => 'Error While Processing Your Request'
        );
    }
    
    
    
}else{
    $data = array(
        'status' => 400,
        'message' => 'Sorry, Make proper request.'
    );
}

header("Content-type: application/json");
echo json_encode($data);
die();