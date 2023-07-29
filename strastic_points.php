<?php

$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');

require_once('assets/init.php');
global $wo, $sqlConnect;


$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);

if(isset($_POST['action']) && $_POST['action']=="purchase_points") {

    $payment_amount = $_POST['payment_amount'];
    $userid = $wo['user']['user_id'];    
    $pointsperdollar = Wo_get_stratistic_points("points_per_dollar");

    $pointstoadd = $pointsperdollar * $payment_amount;

    $query = "SELECT wallet,points FROM Wo_Users WHERE user_id=".$userid;
    $sql       = mysqli_query($sqlConnect, $query);
    $fetched_data = mysqli_fetch_assoc($sql);

    if($fetched_data['wallet'] >= $payment_amount) {
        $oldpoints = $fetched_data['points'];
        $newpoints = $oldpoints + $pointstoadd;
        $oldwallet = $fetched_data['wallet'];

        $newwallet = $oldwallet - $payment_amount;

        $query_one = mysqli_query($sqlConnect, "UPDATE `Wo_Users` SET `wallet`='$newwallet',`points`='$newpoints' where `user_id`=".$userid); 

        if($query_one)
            echo "Successfully Purchased!!";

    } else {
        echo "Insufficient Balance in your wallet!!";
    }

    die;

} else {

    //print_r($_POST);
   
    $offer = $_POST['Offers'];
    $Email = $_POST['Email'];
    $Text = $_POST['Text'];
    $Messages = $_POST['Messages'];
    $Add_Listing = $_POST['Add_Listing'];
    $Add_Buyers = $_POST['Add_Buyers'];
    $Featured_Listing = $_POST['Featured_Listing'];
    $contact = $_POST['contact'];
    $schedule = $_POST['schedule'];
    $pointsperdollar = $_POST['pointsperdollar'];
    

    $query_one = mysqli_query($sqlConnect, "UPDATE `wo_Strastic_point` SET `offer`='$offer',`email`='$Email',`text`='$Text',`message`='$Messages',`add_listing`='$Add_Listing',`add_buyer`='$Add_Buyers',`featured_listing`='$Featured_Listing',`contact`='$contact',`schedul`='$schedule',`points_per_dollar`='$pointsperdollar' where `id`=1 ");

    //echo "UPDATE `wo_Strastic_point` SET `offer`='$offer',`email`='$Email',`text`='$Text',`message`='$Messages',`add_listing`='$Add_Listing',`add_buyer`='$Add_Buyers',`featured_listing`='$Featured_Listing' where `id`=1 ";
}
 

die;

?>