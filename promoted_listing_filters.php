<?php


global $wo, $sqlConnect;
$root = __DIR__;

require_once($root.'/config.php');
require_once('assets/init.php');

$con = $wo['sqlConnect'];

$filterType = filter('type');
$dealType = filter('deal_type');
$tagType = filter('tag');
$promotedFee = filter('promoted_fee');
$equityAmount = filter('equity_amount');


$page = 0;
$limit = 25;

$start = $page * $limit;

$all = $newest = '
        SELECT *, p.id as new_id FROM Wo_Listing p 
        LEFT JOIN Wo_list_promotion q ON p.id = q.listing_id
        WHERE p.tab1 LIKE \'%"allow_promotion":"1%\' AND (q.user != ' . $user_id . ' OR q.user IS NULL) 
    ';

    $query = $price = $promoted = '
        SELECT *, p.id as new_id, COUNT(q.listing_id) as promoted FROM Wo_list_promotion q
        LEFT JOIN Wo_Listing p  ON p.id = q.listing_id
        WHERE p.tab1 LIKE \'%"allow_promotion":"1%\' AND (q.user != ' . $user_id . ' )
    ';
    

    $popular = '
        SELECT *, p.id as new_id, COUNT(l.property_id) as viewed FROM property_view_log l 
        LEFT JOIN Wo_Listing p ON p.id = l.property_id 
        LEFT JOIN Wo_list_promotion q ON p.id = q.listing_id 
        WHERE l.view_type = "property" AND p.tab1 LIKE \'%"allow_promotion":"1%\'  AND (q.user != "' . $user_id . '")
    ';


$queryArray = [
    'type' => [],
    'deal_type' => [],
    'tag' => [],
];

if($search = filter('search')){
    
    $sqlAddress =' tab1 LIKE \'%"entered_address":"'.$search."%' ";
    $sqlState = 'OR tab1 LIKE \'%"state":"'.$search."%' ";
    $sqlCity = 'OR tab1 LIKE \'%"city":"'.$search."%' ";
    $sqlCityF = 'OR tab1 LIKE \'%"city_r":"'.$search."%' ";
    $sqlCity = 'OR tab1 LIKE \'%"city":"'.$search."%' ";
    $sqlCountry = 'OR tab1 LIKE \'%"country":"'.$search."%' ";

    $cc = $sqlAddress. $sqlState. $sqlCity. $sqlCityF . $sqlCountry;
    $all .= " AND ($cc) ";
    $newest .= " AND ($cc) ";
    $price .= " AND ($cc) ";
    $promoted .= " AND ($cc) ";
    $popular .= " AND ($cc) ";
    

}

foreach(array_keys($filterType) as $key => $value) {
        $queryArray['type'][] = ' tab1 LIKE \'%"prop_type":"'.$value."%' ";
}

foreach(array_keys($dealType) as $key => $value) {
        $queryArray['deal_type'][] = ' tab1 LIKE \'%"deal_type":"'.$value."%' ";
}

foreach(array_keys($tagType) as $key => $value) {
        $queryArray['tag'][] = ' tab8 LIKE \'%"tags":["'.$value."%' ";
}

if(count($queryArray['type'])){
    $query = " AND (" . implode('OR' , $queryArray['type']) . ") \n" ;


    $all .= $query;
    $newest .=  $query;
    $price .= $query;
    $promoted .= $query;
    $popular .= $query;
}

if(count($queryArray['deal_type'])){
    $query = " AND (" . implode('OR' , $queryArray['deal_type']) . ") \n" ;
    
    $all .= $query;
    $newest .=  $query;
    $price .= $query;
    $promoted .= $query;
    $popular .= $query;
}

if(count($queryArray['tag'])){
    $query = " AND (" . implode('OR' , $queryArray['tag']) . ") \n" ;
    
    $all .= $query;
    $newest .=  $query;
    $price .= $query;
    $promoted .= $query;
    $popular .= $query;
}

$all .= " GROUP BY p.id LIMIT $start,$limit";
$newest .= " ORDER BY p.id DESC LIMIT $start,$limit";
$price .= " ORDER BY q.price DESC LIMIT $start,$limit";
$promoted .= " GROUP BY q.listing_id  ORDER BY promoted DESC  LIMIT $start,$limit";
$popular .= " GROUP BY l.property_id  ORDER BY viewed DESC  LIMIT $start,$limit";


?>
<div class="tab-content p-3" id="myTabContent">

    <?php

        // All Tab
        $wo['tab'] = 'all';
        $wo['query'] = $all;
        echo Wo_LoadPromotedPage('store/tabs');

        // Most Popular Tab
        $wo['tab'] = 'most-popular';
        $wo['query'] = $popular;
        echo Wo_LoadPromotedPage('store/tabs'); 

        // // Most Promoted Tab
        $wo['tab'] = 'most-promoted';
        $wo['query'] = $promoted;
        echo Wo_LoadPromotedPage('store/tabs'); 

        // // Newest Tab
        $wo['tab'] = 'newest';
        $wo['query'] = $newest;
        echo Wo_LoadPromotedPage('store/tabs');  

    ?>

</div>