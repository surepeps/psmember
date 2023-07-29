<?php

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);
$user_id = $wo['user']['user_id'];

if($_POST['action']=="my_paid_leads"){
    
    $price = $_POST['price'];
    $numapp = $_POST['numapp'];
    $list_lead = explode(",", $_POST['leads_id']);
    
    
    $oneleadPrice = $price / $numapp;
    
    
    // fetch lead package prices
    $query = mysqli_query($sqlConnect,"SELECT * FROM `lead_price_package` WHERE `initvalue` = 'leadprice'");
     if(mysqli_num_rows($query) > 0){
      $data = mysqli_fetch_assoc($query);
      
      $free = $data['free'];
      $bronze = $data['bronze'];
      $silver = $data['silver'];
      $gold = $data['gold'];
      $platinum = $data['platinum'];
      
     }
     
    //  user pro type
     $my_pro_type = $wo['user']['pro_type'];
     
    //  allocate user's by their lead price package
     if($my_pro_type == 0){
        $myprice =  $free;
     }elseif($my_pro_type == 1){
         $myprice = $bronze;
     }elseif($my_pro_type == 2){
         $myprice = $silver;
     }elseif($my_pro_type == 3){
         $myprice = $gold;
     }elseif($my_pro_type == 4){
         $myprice = $platinum;
     }
     
    if($oneleadPrice == $myprice){
        foreach($list_lead as $ld){
            
            $intl = InsertLeadPaid_By_User($ld,$oneleadPrice);
            
            if($intl > 0){
                $data = array(
                    'status' => 200,
                    'message' => 'Successfully Paid '
                );
            }else{
                $data = array(
                    'status' => 400,
                    'message' => 'Payment Error please try again'
                );
            }
        }  
    }else{
        $data = array(
            'status' => 400,
            'message' => 'Error With Wallet'
        );
    }
    
    
    
     
}

if($_POST['action']=="my_lead_details"){
    $lead_id = $_POST['lead_id'];
    
    
    // Check if user actually bought the lead
    $mypaidlead = mysqli_query($sqlConnect, "SELECT * FROM `paid_lead` WHERE user_id = $user_id AND lead_id = $lead_id");
    $countmyleads = mysqli_num_rows($mypaidlead);
    
    if($countmyleads > 0){
        
        // get the lead details
        $myleaddetailsquery = mysqli_query($sqlConnect, "SELECT * FROM `lead_lists` WHERE id = $lead_id");
        $dd = mysqli_fetch_assoc($myleaddetailsquery);
        
        $address = $dd['property_address'];
        $s_name = $dd['seller_name'];
        $s_phone = $dd['seller_phone'];
        $s_email = $dd['seller_email'];
        $city = $dd['city'];
        $state = $dd['state'];
        $zip_code = $dd['zip_code'];
        $beds = $dd['beds'];
        $baths = $dd['baths'];
        $lead_note = $dd['lead_note'];
        
        $r_address = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $address);
        $response = '<div class="container-body">
                        <table id="table">
                            <tr>
                              <th colspan="3" style="text-align: center;" id="address">'.$r_address.'</th>
                            </tr>
                            <tr>
                              <td>Seller Name: </td>
                              <td id="s_name">'.$s_name.'</td>
                            </tr>
                            <tr>
                              <td>Seller Email:</td>
                              <td id="s_email">'.$s_email.'</td>
                            </tr>
                            <tr>
                              <td>Seller Phone:</td>
                              <td id="s_phone">'.$s_phone.'</td>
                            </tr>
                            <tr>
                              <td>Property State:</td>
                              <td id="s_state">'.$state.'</td>
                            </tr>
                            <tr>
                              <td>Property City:</td>
                              <td id="s_city">'.$city.'</td>
                            </tr>
                            <tr>
                              <td>Property Zip Code:</td>
                              <td id="s_zip_code">'.$zip.'</td>
                            </tr>
                            <tr>
                              <td>Beds:</td>
                              <td ="s_beds">'.$beds.'</td>
                            </tr>
                            <tr>
                              <td>Baths:</td>
                              <td id="s_baths">'.$baths.'</td>
                            </tr>
                            <tr>
                              <td>Note:</td>
                              <td id="s_note">'.$lead_note.'</td>
                            </tr>
                        </table>
                    </div>';
        
        $data = array(
            'status' => 200,
            'result' => $response,
            'message' => 'Success '
        );
    }else{
        $data = array(
            'status' => 400,
            'message' => 'Lead Not Found please Purchase to access this Lead'
        );
    }
   
    
}

if($_POST['action'] == ""){
    $data = array(
        'status' => 404,
        'message' => 'Page Not Found'
    );
}

// 

header("Content-type: application/json");
echo json_encode($data);
die;