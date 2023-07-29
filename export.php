<?php

global $wo, $sqlConnect;


require_once('config.php');
require_once('assets/init.php');
$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);
$user_id = $wo['user']['user_id'];


$action = filter('action');

if($action == 'buyers'){

	if(!$user_id){
		include('sources/oops.php');
	} else {

		$buyers = getSearchedBuyers('contact', $user_id);
		
		$headers = [
			'id' => 'ID',
			'firstname' => 'Name',			
			'email' => 'Email',			
			'phone' => 'Phone',			
			'property_type' => 'Property Type',			
			'buying_strategy' => 'Deal Type',			
			'beds' => 'Beds',			
			'bath' => 'Baths',			
			'sqft' => 'Property Size',			
			'min_price' => 'Min Price',			
			'max_price' => 'Max Price',		
			'fund_available' => "Available Funds",
			'how_will_you_purchasing_home' => "Purchasing Strategy",
			'city' => 'City', 
			'first_time_home_buyer' => "First Time Home Buyer",
			'send_all_properties' => ' Send Direct Notification',
		];
		

		$content = [];
		foreach($buyers as $bKey => $buyer) {
			$buyer['city'] = str_replace("\"", "", str_replace(",", ':', preg_replace("/\[|\]/", '', $buyer['city'])));
			foreach($headers as $key => $value) {
				$content[$bKey][$key] = $buyer[$key];
			}
		}


        $name = "Buyers.csv";
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename={$name};");
        $fp = fopen('php://output', 'w');

        fputcsv($fp, array_values($headers));
        foreach ($content as $fields) {
            fputcsv($fp, $fields);
        }

        fclose($fp);
        exit; 
	}

}