<?php

global $wo, $sqlConnect;
$root = __DIR__;
require_once($root.'/config.php');
require_once('assets/init.php');

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);

$user_id = $wo['user']['user_id'];

if($_POST['action']=="my_broadcast_message_details"){
    if(isset($_POST['prop_id'])){
        $st_com_id = $_POST['prop_id'];
        $prop_type = $_POST['prop_type'];
        
        if($prop_type == "prop" && $prop_type != ''){
            
            $selectmathced_cri = "SELECT * FROM `Wo_Listing` WHERE `id`=".$st_com_id;
            $result = mysqli_query($sqlConnect,$selectmathced_cri);
            
            $section = 1;
            
        }
        
        if($prop_type == "promoted" && $prop_type != ''){
            
            $selectmathced_cri = "SELECT * FROM `Wo_Listing` WHERE `id`=".$st_com_id;
            $result = mysqli_query($sqlConnect,$selectmathced_cri);
            
            // select from promoted property also
            $selectpromotedlistquery = "SELECT * FROM `Wo_list_promotion` WHERE `listing_id`=".$st_com_id." AND user = $user_id";
            $result2 = mysqli_query($sqlConnect,$selectpromotedlistquery);
            

            $row2 = mysqli_fetch_assoc($result2);
            
            $section = 2;
        }
        
        
        
        $srows_count =  mysqli_num_rows($result);
    		
    	if($srows_count>0){
                   
            $row = mysqli_fetch_assoc($result);
                $stab1 = json_decode($row["tab1"]);
               	$list_beds = $stab1->beds;
               	$list_baths = $stab1->baths;
               	$list_sqft = $stab1->property_size;
               	$list_built = $stab1->constructions_year;
               	$list_repair = $stab1->flip_ext_repair;
               	$list_arv = $stab1->flip_arv;
               	$list_address = $stab1->entered_address;
               	$city_r = $stab1->city_r;
               	$city = $stab1->city;
               	$state = $stab1->state;
               	$prop_type = $stab1->prop_type;
               	$list_image = unserialize($row["tab6"]);
               	
               	// Author details fetch
                $author_d = Wo_UserData($row['user_id']);
                $autho_name = $author_d['name'];
                
               	$list_image_1 = $wo['config']['site_url'].'/themes/wondertag/uploads_images/'.$list_image[0];
               	
                $deal_site = GetDeal_site_details($row['user_id']);
               	if($section == 1){
                    

               	    $list_title = $stab1->listing_title;
                   	$list_descrip = $row['description'];
                   	$list_prop_price = $stab1->flip_price;

                       

                   	$propertyurl = $wo['config']['site_url']."/property/".$row["id"];
                    if($deal_site) {
                        $propertyurl = "https://" . $deal_site['domain'].".psmembers.com/property/".$row["id"];
                    }
                   
               	}else{
               	    $list_title = $row2['title'];
               	    $list_descrip = $row2['description'];
                   	$list_prop_price = $row2['price'];

                   	$propertyurl = $wo['config']['site_url']."/promoted-property/".$row2['promote_code'];
               	}
               	
               	if($city_r != ""){
               	    $sqlCity = mysqli_query($sqlConnect, "SELECT COUNT(id) AS myId FROM `Wo_Buyerinfo` WHERE user_id = $user_id AND city LIKE '%$city_r%' AND buyer_email != '' " );
               	    $p_query_B = mysqli_fetch_assoc($sqlCity);
                    $numBuyers = $p_query_B['myId'];
                    
                    if($numBuyers > 0){
                        $mymatchedcityBuyers = $numBuyers;
                        $mycityName = $city_r;
                    }else{
                        $mymatchedcityBuyers = 0;
                        $mycityName = "None City";
                    }
                    
               	}else{
               	    $mymatchedcityBuyers = 0;
               	    $mycityName = "None City";
               	}
               	
               	
               	
               	$wo['myTempEmail']['title'] = $list_title;
               	$wo['myTempEmail']['list_img'] = $list_image_1;
               	$wo['myTempEmail']['address'] = $list_address;
               	$wo['myTempEmail']['prop_price'] = $list_prop_price;
               	$wo['myTempEmail']['list_repair'] = $list_repair;
               	$wo['myTempEmail']['beds'] = $list_beds;
               	$wo['myTempEmail']['baths'] = $list_baths;
               	$wo['myTempEmail']['sqft'] = $list_sqft;
               	$wo['myTempEmail']['arv'] = $list_arv;
               	$wo['myTempEmail']['built'] = $list_built;
               	$wo['myTempEmail']['description'] = $list_descrip;
               	$wo['myTempEmail']['url'] = $propertyurl;
               	$wo['myTempEmail']['author'] = $autho_name;
               	$wo['myTempEmail']['city'] = $city;
               	$wo['myTempEmail']['prop_type'] = $prop_type;
               	$wo['myTempEmail']['state'] = $state;
               	$c_l = get_my_company_logo($user_id);
               	// $wo['myTempEmail']['company_logo'] = $wo['config']['site_url']."/xhr/company/".$c_l;
               	$wo['myTempEmail']['company_logo'] = $wo['config']['site_url']. "/" . $deal_site['logo'];
               	$wo['myTempEmail']['dealsite'] = $deal_site;
               	
               	$mytemp_id = get_my_temp_design_id($user_id);
               	if($mytemp_id == 2){
               	    $internal_msg = Wo_LoadPage('my_broadcast/temp_2');
               	}else{
               	    $internal_msg = Wo_LoadPage('my_broadcast/temp_1');
               	}
               	
               
                
                $data = array(
                    'status' => 200,
                    'title' => $list_title,
                    'cityName' => $mycityName,
                    'cityBuyers' => $mymatchedcityBuyers,
                    'msg' => $internal_msg
                );
            //}
            
    	}else{
    	    $internal_msg = "Please Select Property";
    	    $data = array(
                'status' => 400,
                'msg' => $internal_msg
            );
    	    
    	}
    	
        
    }
    header("Content-type: application/json");
    echo json_encode($data);
    die;
    
}

if(isset($_POST['action']) && $_POST['action']=="select_template"){
    if(isset($_POST['temp_id'])){
      $temp_id = $_POST['temp_id'];
      $select_temp = get_and_update_temp_design($temp_id);
      if($select_temp > 0){
          $data = array(
            'status' => 200,
            'msg' => "Selected Succefully"
        );
      }else{
          $data = array(
            'status' => 400,
            'msg' => "Error While Processing your request"
        );
      }
        
    }else{
        $data = array(
            'status' => 400,
            'msg' => "Please select Template"
        );
    }
    header("Content-type: application/json");
    echo json_encode($data);
    die;
}

if(isset($_POST['action']) && $_POST['action']=="my_sms_broadcast_message_details"){
    if(isset($_POST['prop_id'])){
      
      $st_com_id = $_POST['prop_id'];
      $prop_type = $_POST['prop_type'];
        
        if($prop_type == "prop" && $prop_type != ''){
            
            $selectmathced_cri = "SELECT * FROM `Wo_Listing` WHERE `id`=".$st_com_id;
            $result = mysqli_query($sqlConnect,$selectmathced_cri);
            
            $section = 1;
            
        }
        
        if($prop_type == "promoted" && $prop_type != ''){
            
            $selectmathced_cri = "SELECT * FROM `Wo_Listing` WHERE `id`=".$st_com_id;
            $result = mysqli_query($sqlConnect,$selectmathced_cri);
            
            // select from promoted property also
            $selectpromotedlistquery = "SELECT * FROM `Wo_list_promotion` WHERE `listing_id`=".$st_com_id." AND user = $user_id";
            $result2 = mysqli_query($sqlConnect,$selectpromotedlistquery);
            
            $row2 = mysqli_fetch_assoc($result2);
            
            $section = 2;
        }
        
        $srows_count =  mysqli_num_rows($result);
        if($srows_count>0){
            $row = mysqli_fetch_assoc($result);
            
            $stab1 = json_decode($row["tab1"]);
           	$list_repair = $stab1->flip_ext_repair;
           	$list_address = $stab1->entered_address;
           	$city_r = $stab1->city_r;
           	$list_image = unserialize($row["tab6"]);
           	//$list_image_1 = $wo['config']['site_url'].'/themes/wondertag/uploads_images/'.$list_image[0];
           	
           	if($section == 1){
           	    $list_title = $stab1->listing_title;
               	$list_prop_price = $stab1->flip_price;
                   
                $deal_site = GetDeal_site_details($row['user_id']);
                $propertyurl = $wo['config']['site_url']."/property/".$row["id"];
                if($deal_site) {
                    $propertyurl = "https://" . $deal_site['domain'].".psmembers.com/property/".$row["id"];
                }
           	}else{
           	    $list_title = $row2['title'];
               	$list_prop_price = $row2['price'];

                $propertyurl = $wo['config']['site_url']."/promoted-property/".$row2['promote_code'];
           	}
           	
           	$internal_msg = "Hey, Check out this ".$list_title." PRICE: $". number_format($list_prop_price). " REPAIR: ". number_format($list_repair) . "Click here ".$propertyurl." for more details and photos.";
               
            if($city_r != ""){
           	    $sqlCity = mysqli_query($sqlConnect, "SELECT COUNT(id) AS myId FROM `Wo_Buyerinfo` WHERE user_id = $user_id AND city LIKE '%$city_r%' AND buyer_phone_number != '' " );
           	    $p_query_B = mysqli_fetch_assoc($sqlCity);
                $numBuyers = $p_query_B['myId'];
                
                if($numBuyers > 0){
                    $mymatchedcityBuyers = $numBuyers;
                    $mycityName = $city_r;
                }else{
                    $mymatchedcityBuyers = 0;
                    $mycityName = "None City";
                }
                
           	}else{
           	    $mymatchedcityBuyers = 0;
           	    $mycityName = "None City";
           	} 
           	
           	
                $data = array(
                    'status' => 200,
                    'cityName' => $mycityName,
                    'cityBuyers' => $mymatchedcityBuyers,
                    'msg' =>  substr($internal_msg,0,160)
                );
           	
            
        }else{
    	    $internal_msg = "Please Select Property";
    	    $data = array(
                'status' => 400,
                'msg' => $internal_msg
            );
    	    
    	}
        
        
        
        
    }
    
    
   header("Content-type: application/json");
    echo json_encode($data);
    die; 
}

if(isset($_POST['action']) && $_POST['action']=="buy_new_phone_number"){
    
    $phone_no = $_POST['phone_no'];
    
    if(isset($phone_no) && $phone_no != ''){
        
        $getInsertResponse = purchase_new_number($phone_no);
        
        if($getInsertResponse > 1){
            $internal_msg = "Successfully Purchased ".$phone_no. " Number";
            
            $data = array(
                'status' => 200,
                'message' => $internal_msg
            );
        }else{
            $internal_msg = "Error While Processing Your Item ";
           $data = array(
                'status' => 400,
                'message' => $internal_msg
            );
        }
        
    }else{
       $internal_msg = "Please Select a phone number to purchase";
       $data = array(
            'status' => 400,
            'message' => $internal_msg
        ); 
    }
    
    header("Content-type: application/json");
    echo json_encode($data);
    die;   
}

if(!isset($_POST['action']) || $_POST['action'] == ""){
    $internal_msg = "Error Page Not Found";
  $data = array(
    'status' => 400,
    'msg' => $internal_msg
);
 
header("Content-type: application/json");
echo json_encode($data);
die; 

}

