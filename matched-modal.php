<?php

require_once('config.php');
require_once('assets/init.php');


global $wo, $sqlConnect;

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);
$matched_id = $_POST['mid'];

$user_id = $wo['user']['user_id'];


$MyPackageID = $wo['user']['my_package'];
$package = getTableData(T_PACKAGES, ['id' => $MyPackageID], 1);

if ($_POST['action'] == "display_matched_properties") {
	if (isset($_POST['mid'])) {

		$selectmathced_cri = "SELECT * FROM `contact` WHERE `id`=" . $matched_id . " AND type = 2";
		$result = mysqli_query($sqlConnect, $selectmathced_cri);

		while ($row = mysqli_fetch_assoc($result)) {
			if ($row["send_all_properties"] == 0) {

				// get beds
				$beds_arr = explode(":", $row["beds"]);
				$sqlBeds = "";
				foreach ($beds_arr as $bed) {
					$sqlBeds .= 'OR tab1 LIKE \'%"beds":"' . $bed . "%' ";
				}
				$sqlBeds = substr($sqlBeds, 2);

				// get baths
				$bath_arr = explode(":", $row["bath"]);
				$sqlBath = "";
				foreach ($bath_arr as $bath) {
					$sqlBath .= 'OR tab1 LIKE \'%"baths":"' . $bath . "%' ";
				}
				$sqlBath = substr($sqlBath, 2);

				// get prop type 
				$property_type_arr = explode(":", $row["property_type"]);
				$sqlPropertyType = "";
				foreach ($property_type_arr as $property_type) {
					$sqlPropertyType .= 'OR tab1 LIKE \'%"prop_type":"' . $property_type . "%' ";
				}
				$sqlPropertyType = substr($sqlPropertyType, 2);

				// get cities
				$city_arr = str_replace('["', '', $row["city"]);
				$city_arr = str_replace('"]', '', $city_arr);
				$city_arr = explode('","', $city_arr);

				$sqlCity = "";
				foreach ($city_arr as $city) {
					$sqlCity .= 'OR tab1 LIKE \'%"city_r":"' . $city . "%' ";
					$sqlCity .= 'OR tab1 LIKE \'%"city":"' . $city . "%' ";
				}
				$sqlCity = substr($sqlCity, 2);

				$sqlList = "
					SELECT * FROM `Wo_Listing` WHERE `status` = 1
					AND ($sqlBath)			
					AND ($sqlBeds)
					AND ($sqlPropertyType)
					AND ($sqlCity)
				";

				$queryresList = mysqli_query($sqlConnect, $sqlList);

				$la = 1;
				$srows_count =  mysqli_num_rows($queryresList);

				if ($srows_count > 0) {
					$response = '<div class="container"><div class="row">';

					$counter = 0;
					$style = null;
					if ($package && $package['pack_type'] == 0) {
						$style = "style='filter: blur(5px)'";
					}

					while ($srow = mysqli_fetch_assoc($queryresList)) {
						$counter++;
						$stab1 = json_decode($srow["tab1"]);
						$stab4 = json_decode($srow["tab4"]);
						$prop_price = $stab1->flip_price;
						//echo $row["max_price"]."-$prop_price-".$row["min_price"];
						if ($prop_price >= $row["min_price"] and $prop_price <= $row["max_price"]) {

							// $slug = Wo_Property_Slug($srow["id"]);
							$propertyurl = $wo['config']['site_url'] . "/property/" . $srow["id"];
							$userData = Wo_UserData($srow['user_id']);
							$address = $stab1->entered_address;


							$button = '<a class="message-btn" onclick="copyAddress(\'' . $address . '\')" href="' . $wo['site_url'] . '/messages/' . $srow['user_id'] . '?c=' . $address . '">Send Message</a>';
							if ($counter > 1 && $package['pack_type'] == 0) {
								$button = '<a class="message-btn"  href="' . $wo['site_url'] . '/go-pro">Upgrade to Pro</a>';
							}

							$response .= '
								<div class="aspect-tab ">   
									<input id="item-18" type="checkbox" class="aspect-input" name="aspect">   
									<label for="item-18" class="aspect-label"></label>   
									<div class="aspect-content">    
										  
										
										<div class="user-info">
											<img ' . ($counter > 1 ? $style : null)  . ' src="' . $userData['avatar'] . '" />
											
										</div> 
										<div class="aspect-info">          
											<a href="' . $propertyurl . '">
												<span ' . ($counter > 1 ? $style : null)  . ' class="aspect-name">' . $address . '</span> 
											</a>
											' . $button . '
										</div>   
									</div>
								</div>
							';
						} else {
							$response = '<div style="text-align: center; font-size: 20px;">No property match to display by Price</div>';
						}
					}
					$response .= '</div></div>';
				} else {
					$response = '<div style="text-align: center; font-size: 20px;">No property match to display</div>';
				}
			}
		}
	} else {
		$response = '<div style="text-align: center; font-size: 20px;">Error While fetching Data</div>';
	}


	echo $response;
	exit;
}




if ($_POST['action'] == "display_matched_buyers") {
	if (isset($_POST['prop_id'])) {



		$listing = getTableData(T_LISTINGS, ['id' => $_POST['prop_id']], 1);
		$tab1 = json_decode($listing['tab1'], 1);
		$address = $tab1['entered_address'];
		$matched = getMatchedBuyersWithProperty($_POST['prop_id']);

		if (count($matched) > 0) {
			$response = '<div class="container"><div class="row">';

			$counter = 0;
			$style = null;
			if ($package && $package['pack_type'] == 0) {
				$style = "style='filter: blur(5px)'";
			}

			foreach ($matched as $userId => $buyers) {

				$user = Wo_UserData($userId);
				$counter++;

				$button = '<a class="message-btn" target="_blank" href="' . $wo['site_url'] . '/messages/' . $user['user_id'] . '?c=' . $address . '">Send Message</a>';
				if ($counter > 1 && $package['pack_type'] == 0) {
					$button = '<a class="message-btn"  href="' . $wo['site_url'] . '/go-pro">Upgrade to Pro</a>';
				}

				$response .= '
					<div class="aspect-tab ">   
						<input id="item-18" type="checkbox" class="aspect-input" name="aspect">   
						<label for="item-18" class="aspect-label"></label>   
						<div class="aspect-content">    
							<div class="user-info">
								<img src="' . $user['avatar'] . '" />
							</div> 
							<div class="aspect-info">          
								<a href="#">
									We\'ve found member(s) that have <span class="col-orange">' . count($buyers) . '</span> Buyers that matches this property
								</a>
								' . $button . '
							</div>   
						</div>
					</div>
				';
			}
			$response .= '</div></div>';
		} else {
			$response = '<div style="text-align: center; font-size: 20px;">No buyer match to display</div>';
		}
	} else {
		$response = '<div style="text-align: center; font-size: 20px;">Error While fetching Data</div>';
	}

	echo $response;
	exit;
}
