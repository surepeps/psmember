<?php

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');
/*error_reporting(E_ALL);
ini_set("display_errors",1);*/

$email = $_POST['stripeEmail'];
$source = $_POST['stripeToken'];

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/customers');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "email=".$email."&source=".$source);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_USERPWD, 'sk_live_0JbozWMwoKfQJJ2elLo33wNA' . ':' . '');

	$headers = array();
	$headers[] = 'Content-Type: application/x-www-form-urlencoded';
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	$result = curl_exec($ch);

	if (curl_errno($ch)) {
	    echo 'Error:' . curl_error($ch);
	}
	curl_close ($ch);

	$Customerobject = json_decode($result);

	$customerid = $Customerobject->id;

	
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/subscriptions');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "customer=".$customerid."&items[0][plan]=".PRO_PLAN_ID);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_USERPWD, 'sk_live_0JbozWMwoKfQJJ2elLo33wNA' . ':' . '');

	$headers = array();
	$headers[] = 'Content-Type: application/x-www-form-urlencoded';
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	$result = curl_exec($ch);
	if (curl_errno($ch)) {
	    echo 'Error:' . curl_error($ch);
	}
	curl_close ($ch);

	$response = json_decode($result);


    $newesarr = array();
    if($response->status=="active") {

        
        try {
            
            $subscriptionid =  $response->id;
            $planid = $response->items->data[0]->plan->id;
            $planstartdate = date("Y-m-d H:i:s",$response->current_period_start);
            $renewdate = date("Y-m-d H:i:s",$response->current_period_end);


            $item_id = $response->items->data[0]->id;
            $packagetype = "";

            $packageid = 3;

            $newesarr['packageid2'] = $packageid;
            $Packageinfoquery = mysqli_query($sqlConnect, "SELECT stastic_point FROM `Wo_Package_permission` WHERE `id` = '{$packageid}'");
            $Packagedata   = mysqli_fetch_assoc($Packageinfoquery);


            $Strasticpoints = $Packagedata['stastic_point'];
            $pro_type = $packageid;
            $pro_time = strtotime(date("Y-m-d H:i:s"));

            $amount = $response->plan->amount;
            $amount = $amount/100;
            $query_one = "SELECT user_id FROM " . T_USERS . " WHERE email = '".$Customerobject->email."'";
            $sql       = mysqli_query($sqlConnect, $query_one);
            $fetched_data = mysqli_fetch_assoc($sql);

            if(empty($fetched_data)) {
            	$username = array_shift(explode("@", $Customerobject->email));
            	$registration_data = array("email"=>$Customerobject->email,"name"=>$username,"username"=>$Customerobject->email);
            	$userid = Wo_RegisterUser_stripe($registration_data);
            } else {
            	$userid = $fetched_data['user_id']; 
            }

            $Userquery = mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET is_pro=1, points=".$Strasticpoints.", pro_type='".$pro_type."', pro_time=".$pro_time." WHERE user_id=".$userid);

            $next_payment_date = date("Y-m-d",$response->current_period_end);


            /************************** Refferal Commission ****************************/

            $refferalid = Wo_get_refferal_id($userid);
            if ($refferalid!=0 && $wo['config']['affiliate_system'] == 1 && $wo['config']['affiliate_type'] == 0) {
                $ref_user_id = $refferalid;
                if (!empty($ref_user_id) && is_numeric($ref_user_id)) {
                    /*$update_user    = Wo_UpdateUserData($wo['user']['user_id'], array(
                        'referrer' => $ref_user_id,
                        'src' => 'Referrer'
                    ));*/
                    $update_balance = Wo_UpdateBalance($ref_user_id, $wo['config']['amount_ref']);
                    //unset($_SESSION['ref']);
                }
            }
            //record affiliate with percentage
            if ($refferalid!=0 && $wo['config']['affiliate_system']==1 && $wo['config']['affiliate_type'] == 1) {
                if ($wo['config']['amount_percent_ref'] > 0) {
                    $ref_user_id = $refferalid;
                    if (!empty($ref_user_id) && is_numeric($ref_user_id)) {
                    
                        $ref_amount     = ($wo['config']['amount_percent_ref'] * $amount) / 100;
                        $update_balance = Wo_UpdateBalance($ref_user_id, $ref_amount);
                        //unset($_SESSION['ref']);
                    }
                } else if ($wo['config']['amount_ref'] > 0) {
                    $ref_user_id = Wo_UserIdFromUsername($_SESSION['ref']);
                    if (!empty($ref_user_id) && is_numeric($ref_user_id)) {
                        $update_user    = Wo_UpdateUserData($wo['user']['user_id'], array(
                            'referrer' => $ref_user_id,
                            'src' => 'Referrer'
                        ));
                        $update_balance = Wo_UpdateBalance($ref_user_id, $wo['config']['amount_ref']);
                        //unset($_SESSION['ref']);
                    }
                }
            }

            $type = "monthly_price";

            $date  = date('n') . '/' . date("Y");
            $query1 = mysqli_query($sqlConnect, "INSERT INTO " . T_PAYMENTS . " (`user_id`, `amount`, `date`, `type`) VALUES ({$userid}, {$amount}, '{$date}', '{$type}')");

            $date  = date("Y-m-d H:i:s");
            $query2 = mysqli_query($sqlConnect, "INSERT INTO  `Wo_Payment_Transactions` (`userid`, `amount`, `transaction_dt`, `kind`,`notes`) VALUES ({$userid}, {$amount}, '{$date}', '".ucwords($packagetype)."','Upgrade')");

            $query3 = mysqli_query($sqlConnect, "INSERT INTO `Wo_Package_upgrade_transactions` (`user_id`, `price`,`transaction_date`, `package_id`, `transaction_id`,`next_payment_date`) VALUES ({$userid}, {$amount}, '{$date}', '".$packageid."','{$subscriptionid}','{$next_payment_date}')");

            $query4 = mysqli_query($sqlConnect, "INSERT INTO `Wo_user_subscription_details` (`user_id`, `subcription_id`,`customer_id`,`plan_id`,`item_id`, `plan_start_date`, `next_payment_date`,`created_date`,`modified_date`,`status`) VALUES ({$userid},'{$subscriptionid}','{$customerid}', '{$planid}','{$item_id}','{$planstartdate}','{$renewdate}','{$planstartdate}','{$planstartdate}',1)");


            $newesarr['stastic_point'] = $Strasticpoints;
            $newesarr['pro_type'] = $pro_type;
            $newesarr['pro_time'] = $pro_time;
            $newesarr['amount'] = $amount;
            $newesarr['type'] = $type;

            
/*            $mysqli = mysqli_query($sqlConnect, "INSERT INTO `send_owl_responses` VALUES ('','".$responsetype."','".json_encode($newesarr)."')");*/
        }
        
        //catch exception
        catch(Exception $e) {
          $newesarr['Error'] = $e->getMessage();
          $mysqli = mysqli_query($sqlConnect, "INSERT INTO `send_owl_responses` VALUES ('','false','".json_encode($newesarr)."')");
          print_r($newesarr);
        }
        
        @header("location:".$wo['config']['site_url']);
    }

die;
?>