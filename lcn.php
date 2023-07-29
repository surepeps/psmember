<?php 

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];

require_once('config.php');

require_once('assets/init.php');



if(isset($_POST['action']) && $_POST['action']=="update_forwarding_numbers"){
    
    //pre($_POST); 
    $cName           = $_POST['cName'];  
    $cPhone          = $_POST['cPhone']; 
    $user_id         = $_POST['forword_user_id'];
    $forword_lcn_id  = $_POST['forword_lcn_id']; 
    
//echo  $cName."<br>".$cPhone."<br>".$user_id."<br>".$forword_lcn_id."<br>";

    if(isset($cPhone) && $cPhone != ''){
        
//echo  $cName."<br>".$cPhone."<br>".$user_id."<br>".$forword_lcn_id."<br>"; exit;
       /// $getupdateResponse = mysqli_query($sqlConnect, "UPDATE `lcn_table` SET `forwards` ='".$cPhone.",  `forword_name` = '$cName' WHERE `id` = $forword_lcn_id");
       $exiting_forward = getTableData('forwarding_number', ['lcn_id' => $forword_lcn_id,'phone_number' => $cPhone], 1);
      if($exiting_forward){

        $internal_msg = $cPhone." Already exist in forward";
        $data = array(
             'status' => 400,
             'message' => $internal_msg
         );
      }else{
  
        $new_contact = [
            'lcn_id' => $forword_lcn_id,
            'name' => $cName,
            'phone_number' => $cPhone,
            'user_id' => $user_id
        ];
    
        $queryconact = insertRow('forwarding_number', $new_contact); 
        //pre($queryconact); exit;
        if($sqlConnect->query($queryconact)) {
            $internal_msg = "Foward Numbers Updated Successfully !";
            
            $data = array(
                'status' => 200,
                'message' => $internal_msg
            );
        }else{
        
            $internal_msg = "Please Select a phone number";
            $data = array(
                 'status' => 400,
                 'message' => $internal_msg
             ); 
             
         }


       
    }
        
    }else{
        
       $internal_msg = "Please Select a phone number";
       $data = array(
            'status' => 400,
            'message' => $internal_msg
        ); 
        
    }
    
    header("Content-type: application/json");
    echo json_encode($data);
    die;   
    
}

if(isset($_POST['action']) && $_POST['action']=="remove_forwarding_numbers"){
    

   
    $cPhone          = $_POST['cPhone']; 
   

   

   
$deletequery =  deleteRow('forwarding_number', ['phone_number' => $cPhone], 1);
if(!$sqlConnect->query($deletequery)) {
pre(mysqli_error($sqlConnect)); exit; 
} 
echo  "Forward deleted successfully";

}
if(isset($_POST['action']) && $_POST['action']=="buy_new_phone_number"){
    
    $phone_no = $_POST['phone_no'];
    
    $user_id = $wo['user']['user_id'];
    if(isset($phone_no) && $phone_no != ''){
        
        $getInsertResponse = purchase_new_lcn_number($user_id,$phone_no);
       
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


// SEARCH FOR NUMBER.....
if( isset($_POST['action']) && ($_POST['action'] == "searchLcnAction") ) {
    
    if( isset($_POST['area_code']) ){
        $area_code = $_POST['area_code'];
    }
    
    if( isset($_POST['num_search']) ){
        $num_search = $_POST['num_search'];
    }
    
    $query = get_list_of_avail_numbers($area_code);
    
    
    if( count($query) ){ ?>
        <select class="form-control" id="getNumber" name="selectNumber">
            <option value="select-number">Select Number form the result</option>
            <?php foreach($query as $as){ ?>
            
            <option value="<?= $as->phoneNumber ?>"><?= formatPhoneNumber($as->phoneNumber) ?></option>
            
           <?php } ?> 
        </select>
        
        <script>
            $("#getNumber").on('change', function(e){
                var aaa = $(this).val();
                
                if(aaa != "select-number"){
                    
                    $("#selectNewLCNbTN").removeAttr('disabled');
                    
                    BuyPhoneNum(aaa);
                    
                    $("#newLCN").val(aaa);
                    $("#lcn-number-search-display").text(aaa);
                    
                }else{
                    
                    $("#selectNewLCNbTN").attr('disabled','disabled');
                    $("#lcn-number-search-display").text("Search for a Number");
                    $("#newLCN").val('');
                }
                
            });
        </script>
   <?php }else{ ?>
        <select class="form-control">
            <option>Select Number</option>
        </select>
        <script>
            $("#selectNewLCNbTN").attr('disabled','disabled');
            $("#lcn-number-search-display").text("Search for a Number");
            $("#newLCN").val('');
        </script>
        
   <?php }
    
    // if($query > 0){
        
    //     $data = array(
    // 		'status' => 200,
    // 		'numbers' => $query,
    // 		'message' => 'Success',
    // 	);
    // }else{
    //     $data = array(
    // 		'status' => 400,
    // 		'numbers' => "",
    // 		'message' => 'Success',
    // 	);
    // }
    
    // header("Content-type: application/json");
    // echo json_encode($data);
    // die();
    
}


// GET SINGLE LCN PHONE 
if( isset($_POST['action']) && ($_POST['action'] == "getSingleLCNEdit") ) {
    
    if( isset($_POST['lcn_id']) ){
        $lcn_id = $_POST['lcn_id'];
    }
    
    if( isset($_POST['user_id']) ){
        $user_id = $_POST['user_id'];
    }

   // echo     "SELECT * FROM `forwarding_number` WHERE `lcn_id` = $lcn_id";
    $getfowards =   GetAllForwardNumbers($lcn_id);

  $fowards = [];
    $htmlF   =   [];

 foreach($getfowards as $key => $table){
        //pre($table['name']); exit;
        
      $fowards[]    =  $table['name'].' - '.$table['phone_number'];
          
     }
     foreach($getfowards as $key => $table22){
        //pre($table['name']); exit;
        
      $nameone = $table22['name'];
      $phone_number = $table22['phone_number'];
      $htmlF[]    = '<div class="col-12 multi-fields"><div class="multi-field input-group mb-4"><div class="col-md-4"><input type="text" class="col-lg-5 form-control" id="cName" name="cName[]" value="'. $nameone.'" placeholder="Forward Name" aria-label="Forward Name"></div><div class="col-md-4"><input type="text" class="col-lg-5 form-control" id="cPhone" name="cPhone[]" value="'.$phone_number.'" placeholder="Forward Phone" aria-label="Forward Phone"></div><div class="col-md-4 input-group-append"><button class="save-field btn btn-primary update_forwarding_numbers"  type="button">Save</button><button class="remove-field btn btn-danger" type="button">Remove</button></div></div></div>';
   
          
     }


     $fowardsvar =   implode("<br>",$fowards);
     $htmlFvar =   implode("<br>",$htmlF);
    

    $getDetails = GetSingleLCNDetails($lcn_id,$user_id);
    
        if($getDetails){
            
            $data = array(
                'status' => 200,
                'lcnNo' => $getDetails['number'],
                'forwards' => $fowardsvar,
                'htmlF' => $htmlFvar
            );
            
        }else{
            
            $internal_msg = "Error While Processing Your Request ";
            $data = array(
                'status' => 400,
                'message' => $internal_msg
            );
            
        }
        
        
    header("Content-type: application/json");
    echo json_encode($data);
    die;  
    
    
}





?>