<?php
global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);

$perpage = 5;
$querypagination = "SELECT * FROM `Wo_Listing` WHERE status = 1";
$queryrespagiantion = mysqli_query($sqlConnect,$querypagination);
$numrows = mysqli_num_rows($queryrespagiantion);
$totalPages = ceil($numrows / $perpage);

$query = "SELECT * FROM `Wo_Filter` B left join `Wo_Listing` A on B.property_id =A.id WHERE A.status = 1  ORDER BY dtae_time DESC LIMIT 0,".$perpage;
$queryres = mysqli_query($sqlConnect,$query); 
$l = 1;


while($row =  mysqli_fetch_assoc($queryres)){
    // $data[] = $row;
    
    $tab1 = json_decode($row["tab1"]);
	$tab4 = json_decode($row["tab4"]);
	$tab5 = json_decode($row["tab5"]);
	$tab6 = unserialize($row["tab6"]);
	$tab2 = json_decode($row["tab2"]);
	$tab8 = json_decode($row["tab8"]);
	
    //Lat and Long
    $lat = $row['lat'];
    $lang = $row['lang'];
    
    // Property Id
    $prop_id = $row['id'];
    
    // Author ID
    $us_id = $row['user_id'];
    
    //Image
    $server = $wo['config']['site_url'].'/themes/wondertag/uploads_images/'.$tab6[0];
    
    // URL
    $prop_url = $wo['config']['site_url'].'/property/'.$prop_id;
		
	
    // Get Construction year 		
	$prop_year_built = $tab1->constructions_year;
	$title = $tab1->listing_title;
    $price = $tab1->flip_price;
    $beds = $tab1->beds;
    $baths = $tab1->baths;
    $size = $tab1->property_size;
    $address = $tab1->entered_address;
    
    // Well structures money 
    $n_price = "$".number_format($price);
    
    $array = array(
        "title" => $title,
        "url" => $prop_url,
        "price" => $n_price,
        "property_id" => $prop_id,
        "pricePin" => $n_price,
        "address" => $address,
        "property_type" => "Villa",
        "lat" => $lat,
        "lng" => $lang,
        "term_id" => $us_id,
        "marker" => "https://demo01.houzez.co/wp-content/themes/houzez/img/map/pin-single-family.png",
        "retinaMarker" => "https://demo01.houzez.co/wp-content/themes/houzez/img/map/pin-single-family.png",
        "thumbnail" => $server
    );
    
    $data[] = $array;
}

// $data = array($result);


// foreach($data as $row){
//     $tab1 = json_decode($row["tab1"]);
//     $lat = $row['lat'];
//     $lang = $row['lang'];
    
//     $address = $tab1->entered_address;
//     if($lat == '' || $lang == ''){ 
        
        
//   }
// }

header("Content-type: application/json");
echo json_encode($data);
die();   



?>


