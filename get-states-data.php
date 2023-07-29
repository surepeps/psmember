<?php
$root=__DIR__;

require_once($root.'/config.php');
require_once('assets/init.php'); 

global $wo, $sqlConnect;


$action = filter('action');
$status = $buyers = 0;
if($action == 'getStateCities') {
    
    $html = "";
    $state = filter('state');
    $user_id = filter('user_id');

    if(!$state){
        $message = "Please selete a valid state.";
    }else{
        
        $citiesCount = getCitiesAndBuyerCountsByState($state);
        foreach($citiesCount as $city => $count){

            $html .= "
                <tr>
                    <td>{$city}</td>
                    <td>{$count}</td>
                </tr>
            ";
        }   

        $status = 1;
        $buyers = count($citiesCount);
        $message = $buyers . " Cities found with buyers";
    }
    
    $data = [
        'message' => $message,
        'status' => $status,
        'html' => $html,
        'total_buyers' => $buyers
    ];

}else if($action == 'getTeamStateCities') {
    
    $html = "";
    $state = filter('state');
    if(!$state){
        $message = "Please selete a valid state.";
    }else{
        
        $citiesCount = getCitiesAndBuyerCountsByState($state, 1);
        foreach($citiesCount as $city => $count){

            $html .= "
                <tr>
                    <td>{$city}</td>
                    <td>{$count}</td>
                </tr>
            ";
        }   

        $status = 1;
        $buyers = count($citiesCount);
        $message = $buyers . " Cities found with buyers";
    }
    
    $data = [
        'message' => $message,
        'status' => $status,
        'html' => $html,
        'total_buyers' => $buyers
    ];

}
header("Content-type: application/json");
echo json_encode($data);
die();   





function getCitiesAndBuyerCountsByState($state, $team = 0){

    
    $output = [];
    if($state){
        
        $query = "
            SELECT city FROM contact
            WHERE type='2' AND city LIKE '% {$state}\"%'
        ";

        if($user_id = filter("user_id")) {
            $user_ids = [$user_id];
        }else if($team) {
            global $wo;
            $user_ids = array_column(getUserUsers($wo['user']['user_id']), 'user_id');
        }

        
        if(count($user_ids)){
            $query .= "  AND contactinsertedby IN (" . implode(',', $user_ids) . ")";
        }
        
        
        $result = array_map(function($city) {
            return $city['city'];
        }, getTableRows($query));

        foreach($result as $cities){
            if(!$cities) continue;
            
            $cities = preg_replace("/\[|\]/i", '', $cities);
            $collan = explode(",", $cities);
            
            foreach($collan as $city){
                $city = preg_replace("/\"/i", '', $city);
                $city = trim($city);
                if($city){
                    $city = strtoupper($city);
                    if(strpos($city, $state) !== false){
                        
                        $query = "
                            SELECT COUNT(*) total_buyers, city, contactinsertedby FROM contact
                            WHERE type='2' AND city LIKE '%{$city}%'
                        ";
                        if($user_id = filter('user_id')){
                            $query .= " AND contactinsertedby = '{$user_id}'";
                        }
                        $query .= " HAVING total_buyers > 0";
                        
                        
                        $result = getRow($query);
                        if(!$result['total_buyers']) continue;

                        $output[$city] = $result['total_buyers'];
                    }       
                }
            }
        }
    }

    unset($output[$state]);
    return $output;
}
