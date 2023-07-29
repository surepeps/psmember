<?php

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);
$user_id = $wo['user']['user_id'];

$newmonth = $_POST['month'];
$newyear = $_POST['year'];
$newday = $_POST['day'];

$getmonth = "FROM_UNIXTIME(joined)";
$getmonth2 = "FROM_UNIXTIME(time)";

if($newmonth > 0){
    
    if($newday > 0){
        $wheremonth = "YEAR(transaction_dt) =".$newyear." AND MONTH(transaction_dt)= ".$newmonth. " AND DAY(transaction_dt) =".$newday;
        $wheremonth1 = "YEAR(created_date) =".$newyear." AND MONTH(created_date)= ".$newmonth. " AND DAY(created_date) =".$newday;
        $wheremonth2 = "YEAR($getmonth) =".$newyear." AND MONTH($getmonth)= ".$newmonth." AND DAY($getmonth) =".$newday;
        $wheremonth3 = "YEAR(dtae_time) =".$newyear." AND MONTH(dtae_time)= ".$newmonth. " AND DAY(dtae_time) =".$newday;
        $wheremonth4 = "YEAR($getmonth2) =".$newyear." AND MONTH($getmonth2)= ".$newmonth." AND DAY($getmonth2) =".$newday;
    }else{
        $wheremonth = "YEAR(transaction_dt) =".$newyear." AND MONTH(transaction_dt)= ".$newmonth;
        $wheremonth1 = "YEAR(created_date) =".$newyear." AND MONTH(created_date)= ".$newmonth;
        $wheremonth2 = "YEAR($getmonth) =".$newyear." AND MONTH($getmonth)= ".$newmonth;
        $wheremonth3 = "YEAR(dtae_time) =".$newyear." AND MONTH(dtae_time)= ".$newmonth;
        $wheremonth4 = "YEAR($getmonth2) =".$newyear." AND MONTH($getmonth2)= ".$newmonth;
    }
}else{
    $wheremonth = "YEAR(transaction_dt) =".$newyear;
    $wheremonth1 = "YEAR(created_date) =".$newyear;
    $wheremonth2 = "YEAR($getmonth) =".$newyear;
    $wheremonth3 = "YEAR(dtae_time) =".$newyear;
    $wheremonth4 = "YEAR($getmonth2) =".$newyear;
}

if(isset($_POST['action']) && $_POST['action'] == "tma"){
    
    // Query for Total Registered Members by status
    $tmaquery = mysqli_query($sqlConnect,"SELECT COUNT(user_id) AS tma FROM Wo_Users WHERE active = '1' AND $wheremonth2");
    $tmas = mysqli_fetch_assoc($tmaquery);
    echo $tma = $tmas['tma'];

}

if(isset($_POST['action']) && $_POST['action'] == "tmia"){
    
    // Query for Total Registered Members by status
    $tmiaquery = mysqli_query($sqlConnect,"SELECT COUNT(user_id) AS tmia FROM Wo_Users WHERE (active = '2' OR active = '0') AND $wheremonth2");
    $tmias = mysqli_fetch_assoc($tmiaquery);
    echo $tmia = $tmias['tmia'];

}

if(isset($_POST['action']) && $_POST['action'] == "tmb"){
    
    // Query for Total Registered Members by status
    $tmbquery = mysqli_query($sqlConnect,"SELECT COUNT(user_id) AS tmb FROM Wo_Users WHERE $wheremonth2");
    $tmbs = mysqli_fetch_assoc($tmbquery);
    echo $tmb = $tmbs['tmb'];

}


// ACTIVE PROMEMBERS

if(isset($_POST['action']) && $_POST['action'] == "tpb"){
    
    // Query for Total Registered Members by status
    $tpbquery = mysqli_query($sqlConnect,"SELECT COUNT(user_id) AS tpb FROM Wo_Users WHERE active = '1' AND is_pro = '1' AND pro_type = 1 AND $wheremonth2");
    $tpbs = mysqli_fetch_assoc($tpbquery);
    echo $tpb = $tpbs['tpb'];

}

if(isset($_POST['action']) && $_POST['action'] == "tps"){
    
    // Query for Total Registered Members by status
    $tpsquery = mysqli_query($sqlConnect,"SELECT COUNT(user_id) AS tps FROM Wo_Users WHERE active = '1' AND is_pro = '1' AND pro_type = 2 AND $wheremonth2");
    $tpss = mysqli_fetch_assoc($tpsquery);
    echo $tps = $tpss['tps'];

}

if(isset($_POST['action']) && $_POST['action'] == "tpg"){
    
    // Query for Total Registered Members by status
    $tpgquery = mysqli_query($sqlConnect,"SELECT COUNT(user_id) AS tpg FROM Wo_Users WHERE active = '1' AND is_pro = '1' AND pro_type = 3 AND $wheremonth2");
    $tpgs = mysqli_fetch_assoc($tpgquery);
    echo $tpg = $tpgs['tpg'];

}

if(isset($_POST['action']) && $_POST['action'] == "tpp"){
    
    // Query for Total Registered Members by status
    $tppquery = mysqli_query($sqlConnect,"SELECT COUNT(user_id) AS tpp FROM Wo_Users WHERE active = '1' AND is_pro = '1' AND pro_type = 4 AND $wheremonth2");
    $tpps = mysqli_fetch_assoc($tppquery);
    echo $tpp = $tpps['tpp'];

}

if(isset($_POST['action']) && $_POST['action'] == "tppt"){
    
    // Query for Total Registered Members by status
    $tpptquery = mysqli_query($sqlConnect,"SELECT COUNT(user_id) AS tppt FROM Wo_Users WHERE active = '1' AND is_pro = '1' AND $wheremonth2");
    $tppts = mysqli_fetch_assoc($tpptquery);
    echo $tppt = $tppts['tppt'];

}



// GET ALL EXPENSES BY PACKAGES
// Get total expense of Bronze
    if(isset($_POST['action']) && $_POST['action'] == "totalexpbronze"){
        
        $totalexpbronzequery = mysqli_query($sqlConnect,"SELECT coalesce(SUM(amount),0) AS totalexpbronze FROM Wo_Payment_Transactions WHERE kind = 'PRO' AND amount = 10 AND $wheremonth");
        $totalexpbronzes = mysqli_fetch_assoc($totalexpbronzequery);
        $totalexpbronze2 = $totalexpbronzes['totalexpbronze'];
        echo "$". number_format($totalexpbronze2);
    }
    
    // Get total expense of Silver
    if(isset($_POST['action']) && $_POST['action'] == "totalexpsilver"){
      
        $totalexpsilverquery = mysqli_query($sqlConnect,"SELECT coalesce(SUM(amount),0) AS totalexpsilver FROM Wo_Payment_Transactions WHERE kind = 'PRO' AND amount = 40 AND $wheremonth");
        $totalexpsilvers = mysqli_fetch_assoc($totalexpsilverquery);
        $totalexpsilver2 = $totalexpsilvers['totalexpsilver'];
        echo "$". number_format($totalexpsilver2);
    }
    
    // Get total expense of Gold
    if(isset($_POST['action']) && $_POST['action'] == "totalexpgold"){
        
        $totalexpgoldquery = mysqli_query($sqlConnect,"SELECT coalesce(SUM(amount),0) AS totalexpgold FROM Wo_Payment_Transactions WHERE kind = 'PRO' AND amount = 70 AND $wheremonth");
        $totalexpgolds = mysqli_fetch_assoc($totalexpgoldquery);
        $totalexpgold2 = $totalexpgolds['totalexpgold'];
        echo "$". number_format($totalexpgold2);
    }
    
    // Get total expense of Platinum
    if(isset($_POST['action']) && $_POST['action'] == "totalexpplatinum"){
        
        $totalexpplatinumquery = mysqli_query($sqlConnect,"SELECT coalesce(SUM(amount),0) AS totalexpplatinum FROM Wo_Payment_Transactions WHERE kind = 'PRO' AND amount = 100 AND $wheremonth");
        $totalexpplatinums = mysqli_fetch_assoc($totalexpplatinumquery);
        $totalexpplatinum2 = $totalexpplatinums['totalexpplatinum'];
        echo "$". number_format($totalexpplatinum2);
    }
    
    // Get total year Revenue
    if(isset($_POST['action']) && $_POST['action'] == "totalyearrevenue"){
        
        $totalyearrevenuequery = mysqli_query($sqlConnect,"SELECT coalesce(SUM(amount), 0) AS totalyearrevenue FROM Wo_Payment_Transactions WHERE kind = 'PRO' AND $wheremonth");
        $totalyearreves = mysqli_fetch_assoc($totalyearrevenuequery);
        $totalyearrevenue2 = $totalyearreves['totalyearrevenue'];
        echo "$". number_format($totalyearrevenue2);
    }
    
    // Get total offers count
    if(isset($_POST['action']) && $_POST['action'] == "totaloffercount"){
        
        $tocquery = mysqli_query($sqlConnect,"SELECT COUNT(id) AS totaloffers FROM Wo_offers WHERE $wheremonth1");
        $tocque = mysqli_fetch_assoc($tocquery);
        $tocque2 = $tocque['totaloffers'];
        echo number_format($tocque2);
    }
    
    
    




// IN-ACTIVE PRO MEMBERS 

if(isset($_POST['action']) && $_POST['action'] == "tpib"){
    
    // Query for Total Registered Members by status
    $tpibquery = mysqli_query($sqlConnect,"SELECT COUNT(user_id) AS tpib FROM Wo_Users WHERE (active = '2' OR active = '0') AND is_pro = '1' AND pro_type = 1 AND $wheremonth2");
    $tpibs = mysqli_fetch_assoc($tpibquery);
    echo $tpib = $tpibs['tpib'];

}

if(isset($_POST['action']) && $_POST['action'] == "tpis"){
    
    // Query for Total Registered Members by status
    $tpisquery = mysqli_query($sqlConnect,"SELECT COUNT(user_id) AS tpis FROM Wo_Users WHERE (active = '2' OR active = '0') AND is_pro = '1' AND pro_type = 2 AND $wheremonth2");
    $tpiss = mysqli_fetch_assoc($tpisquery);
    echo $tpis = $tpiss['tpis'];

}

if(isset($_POST['action']) && $_POST['action'] == "tpig"){
    
    // Query for Total Registered Members by status
    $tpigquery = mysqli_query($sqlConnect,"SELECT COUNT(user_id) AS tpig FROM Wo_Users WHERE (active = '2' OR active = '0') AND is_pro = '1' AND pro_type = 3 AND $wheremonth2");
    $tpigs = mysqli_fetch_assoc($tpigquery);
    echo $tpig = $tpigs['tpig'];

}

if(isset($_POST['action']) && $_POST['action'] == "tpip"){
    
    // Query for Total Registered Members by status
    $tpipquery = mysqli_query($sqlConnect,"SELECT COUNT(user_id) AS tpip FROM Wo_Users WHERE (active = '2' OR active = '0') AND is_pro = '1' AND pro_type = 4 AND $wheremonth2");
    $tpips = mysqli_fetch_assoc($tpipquery);
    echo $tpip = $tpips['tpip'];

}

if(isset($_POST['action']) && $_POST['action'] == "tpipt"){
    
    // Query for Total Registered Members by status
    $tpiptquery = mysqli_query($sqlConnect,"SELECT COUNT(user_id) AS tpipt FROM Wo_Users WHERE (active = '2' OR active = '0') AND is_pro = '1' AND $wheremonth2");
    $tpipts = mysqli_fetch_assoc($tpiptquery);
    echo $tpipts['tpipt'];

}


if(isset($_POST['action']) && $_POST['action'] == "totalporptcount"){
    
   $totalproptquery = mysqli_query($sqlConnect,"SELECT COUNT(id) AS totalporpt FROM Wo_Listing WHERE $wheremonth3");
    $totalpropts = mysqli_fetch_assoc($totalproptquery);
    $totalpropts2 = $totalpropts['totalporpt'];
    echo number_format($totalpropts2);

}

if(isset($_POST['action']) && $_POST['action'] == "totalpostscount"){
    
   $totalpostquery = mysqli_query($sqlConnect,"SELECT COUNT(id) AS totalposts FROM Wo_Posts WHERE $wheremonth4");
    $totalposts = mysqli_fetch_assoc($totalpostquery);
    $totalpost2 = $totalposts['totalposts'];
    echo number_format($totalpost2);

}

if(isset($_POST['action']) && $_POST['action'] == "totalschvisitcount"){
    
   $totalschvisiquery = mysqli_query($sqlConnect,"SELECT COUNT(sid) AS totalschvisitcount FROM Wo_Schedule_Visits WHERE $wheremonth1");
    $totalschvisis = mysqli_fetch_assoc($totalschvisiquery);
    $totalschvisi2 = $totalschvisis['totalschvisitcount'];
    echo number_format($totalschvisi2);

}





if(isset($_GET['count']) && $_GET['count'] == "ccc"){
    $data         = array();
    $type_table   = T_USERS;
    $type_id      = Wo_Secure('user_id');
    $time         = time() - 60;
    $query_one    = mysqli_query($sqlConnect, "SELECT COUNT(`{$type_id}`) as count FROM {$type_table} WHERE `lastseen` > {$time}");
    $fetched_data = mysqli_fetch_assoc($query_one);
    echo $fetched_data['count'];
}



// 
// 
// 
// PROPERTY AFFLIATE ACTIONS 
// 
// 
// 
// 

if(isset($_POST['action']) && $_POST['action'] == "getAllPromotedPropertyRecords"){
    
    if( isset($_POST['promote_id']) ){
        $prop_id = $_POST['promote_id'];
    }
    
    if( isset($_POST['promote_code']) ){
        $prop_code = $_POST['promote_code'];
    }
    
    // Views of property
    $views = ViewsCounterPromotedProperty($prop_code,0);
    
    // Unique Views for a promoted property
    $unique_views = ViewsCounterPromotedProperty($prop_code,1);
    
    // Offers made for a promoted property
    $offers = OffersCounterPromotedProperty($prop_code);
    
    // Schedule Visit made for a promoted property
    $schdul = SchedulevisitPromotedProperty($prop_code);
    
    // Contacts made for a promoted property
    $contacts = ContactsPromotedProperty($prop_code);
    
    $data = array(
        'status' => 200,
        'views' => $views,
        'unique_views' => $unique_views,
        'offers' => $offers,
        'schedule_visit' => $schdul,
        'contacts' => $contacts
    );
    
    header("Content-type: application/json");
    echo json_encode($data);
    die();
    
    
}

?>