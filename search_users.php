<?php

/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');
global $wo, $sqlConnect;
	$clicked_page = $_POST['clicked_page'];
	$total_pagination_data = $_POST['total_pagination_data'];
	$role_type = $_POST['role_type'];
	$previous_data = ($clicked_page-1)*10;
	$new_starting_data = $previous_data;
	$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);

	if($role_type == 0)
	{
		$query = mysqli_query($sqlConnect,"SELECT * FROM Wo_Users LIMIT 10 OFFSET $new_starting_data");
	}
	else if($role_type == 10 || $role_type == 2 || $role_type == 3 || $role_type == 1){
		$split_data = explode('_', $total_pagination_data);
		$property_type = $split_data[0];
		$finance_type = $split_data[1];
		$beds = $split_data[2];
		$baths = $split_data[3];
		$zip = $split_data[4];
		$location = $split_data[5];
		$min_amount = $split_data[6];
		$max_amount = $split_data[7];

		if($role_type == 1){
			$table = 'Wo_Filter';
		}
		else{
			$table = 'Wo_Buyerinfo';
		}

		$token = 0;

		if($role_type == 1){
			$query = "SELECT * FROM `Wo_Users` B left join `Wo_UserFields` A on B.user_id = A.user_id LEFT JOIN $table C ON C.user_id = B.user_id WHERE";

			if($role_type != 'Any'){
				$query.=" A.fid_3 = $role_type";
				$token = 1;
			}
			if(!empty($min_amount) && !empty($max_amount)){
				if($token == 0){
					$query.=" C.price_range BETWEEN $min_amount AND $max_amount";
				}
				else{
					$query.=" AND C.price_range BETWEEN $min_amount AND $max_amount";
				}
			}
			if(!empty($location)){
				$query.=" And C.address like '%$location%'";
			}
			if($property_type != 'Any'){
				$query.=" AND C.property_type like '%$property_type%'";
			}
			if($finance_type != 'Any'){
				$query.=" AND C.available_finance = '$finance_type'";
			}
			/*if(!empty($zip)){
				$query.=" AND C.zip_code = $zip";
			}*/
			if($beds != 'Any'){
				$query.=" AND C.bedroom like '%$beds%'";
			}
			if($baths != 'Any'){
				$query.=" AND C.bathroom like '%$baths%'";
			}

			$query.= " LIMIT 10 OFFSET $new_starting_data";
		}
		else{
			$query = "SELECT * FROM `Wo_Users` B left join `Wo_UserFields` A on B.user_id = A.user_id LEFT JOIN $table C ON C.user_id = B.user_id WHERE";

			if($role_type != 'Any' && $role_type != 10){
				$query.=" A.fid_3 = $role_type";
				$token = 1;
			}
			if(!empty($min_amount) && !empty($max_amount)){
				if($token == 0){
					$query.=" C.min_price >= $min_amount AND C.max_price <= $max_amount";
				}
				else{
					$query.=" AND C.min_price >= $min_amount AND C.max_price <= $max_amount";
				}
			}
			if(!empty($location)){
				$query.=" And C.city like '%$location%'";
			}
			if($property_type != 'Any'){
				$query.=" AND C.property_type like '%$property_type%'";
			}
			if($finance_type != 'Any'){
				$query.=" AND C.how_will_you_purchasing_home = '$finance_type'";
			}
			if(!empty($zip)){
				$query.=" AND C.zip_code = $zip";
			}
			if($beds != 'Any'){
				$query.=" AND C.beds like '%$beds%'";
			}
			if($baths != 'Any'){
				$query.=" AND C.bath like '%$baths%'";
			}

			$query.= " LIMIT 10 OFFSET $new_starting_data";
		}

		$query = mysqli_query($sqlConnect,$query);
	}
	else if($role_type == 4 || $role_type == 5)
	{
		$split_data = explode('_', $total_pagination_data);
		$address = $split_data[0];
		$specialties = $split_data[1];
		$languages = $split_data[2];
		$min_amount = $split_data[3];
		$max_amount = $split_data[4];

		$token = 0;

		$query = "SELECT * FROM `Wo_Users` B left join `Wo_UserFields` A on B.user_id = A.user_id LEFT JOIN Wo_Agent C ON C.user_id = B.user_id WHERE";

		if($role_type != 'Any'){
			$query.=" A.fid_3 = $role_type";
			$token = 1;
		}
		if(!empty($min_amount) && !empty($max_amount)){
			if($token == 0){
				$query.=" C.min_price >= $min_amount AND C.max_price <= $max_amount";
			}
			else{
				$query.=" AND C.min_price >= $min_amount AND C.max_price <= $max_amount";
			}
		}
		if($specialties != 'Any'){
			$query.=" And C.specialities = '$specialties'";
		}
		if(!empty($address)){
			$query.=" AND C.service_area like '%$address%'";
		}
		if($languages != 'Any'){
			$query.=" AND C.languages = '$languages'";
		}

		$query.= " LIMIT 10 OFFSET $new_starting_data";

		$query = mysqli_query($sqlConnect,$query);
	}
	while($row = mysqli_fetch_assoc($query)) {
	$cover = $row['avatar'];
?>
		<div class="col-md-4 user-entry pagination_user_div display-none-user-<?php echo $row['user_id']; ?>">
			<div class="user-wrap">
				<div class="col-md-6">
					<div class="user-dp" style="background-image: url('/<?php echo $cover; ?>')">
						<?php
						$user_id = $row['user_id'];
						$role_query = mysqli_query($sqlConnect,"SELECT fid_3 FROM `Wo_UserFields` WHERE user_id = $user_id");
						$role_data = mysqli_fetch_array($role_query);
						$user_role = $role_data['fid_3'];
	 					
	 					$role='';

						if($user_role==1){
							$role="Seller";
						}
						
						if($user_role==2){
							$role="Buyer";
						}

						if($user_role==3){
							$role="Investor";
						}
						
						if($user_role==4){
							$role="Agent";
						}

						if($user_role==5){
							$role="Wholesaler";
						}
						
						?>
						<span><?php echo $role; ?></span>
					</div>
					<div class="text-rating">
						 <div class="average-review">
							<span style="cursor: default;"><div class="rating-symbol" style="display: inline-block; position: relative;"><div class="rating-symbol-background glyphicon glyphicon-star" style="visibility: visible;"></div><div class="rating-symbol-foreground" style="display: inline-block; position: absolute; overflow: hidden; left: 0px; right: 0px; width: 0%;"><span></span></div></div><div class="rating-symbol" style="display: inline-block; position: relative;"><div class="rating-symbol-background glyphicon glyphicon-star" style="visibility: visible;"></div><div class="rating-symbol-foreground" style="display: inline-block; position: absolute; overflow: hidden; left: 0px; right: 0px; width: 0px;"><span></span></div></div><div class="rating-symbol" style="display: inline-block; position: relative;"><div class="rating-symbol-background glyphicon glyphicon-star" style="visibility: visible;"></div><div class="rating-symbol-foreground" style="display: inline-block; position: absolute; overflow: hidden; left: 0px; right: 0px; width: 0px;"><span></span></div></div><div class="rating-symbol" style="display: inline-block; position: relative;"><div class="rating-symbol-background glyphicon glyphicon-star" style="visibility: visible;"></div><div class="rating-symbol-foreground" style="display: inline-block; position: absolute; overflow: hidden; left: 0px; right: 0px; width: 0px;"><span></span></div></div><div class="rating-symbol" style="display: inline-block; position: relative;"><div class="rating-symbol-background glyphicon glyphicon-star" style="visibility: visible;"></div><div class="rating-symbol-foreground" style="display: inline-block; position: absolute; overflow: hidden; left: 0px; right: 0px; width: 0px;"><span class="glyphicon glyphicon-star"></span></div></div></span><input type="hidden" value="0" data-readonly="true" class="rating rating-loading" data-min="0" data-size="xs" data-max="5" data-step="1"> &nbsp; <span class="average-ratings">5.0</span>
						</div>
					</div>
				</div>

				<div class="col-md-6 no-padding">
					<p class="full_name"><a href="/<?php echo $row['username']; ?>"><span><?php echo $row['username']; ?></span> </a> <span title="Verified by Strastic" class="verified-color"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="feather feather-check-circle" title="Verified User" data-toggle="tooltip"><path d="M23,12L20.56,9.22L20.9,5.54L17.29,4.72L15.4,1.54L12,3L8.6,1.54L6.71,4.72L3.1,5.53L3.44,9.21L1,12L3.44,14.78L3.1,18.47L6.71,19.29L8.6,22.47L12,21L15.4,22.46L17.29,19.28L20.9,18.46L20.56,14.78L23,12M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z"></path></svg> </span></p>
					<?php if($row['working']){ ?>
					<p class="company_name"><?php echo $row['working'] ?></p>
					<?php
						}else{
							?>
						<p class="company_name"><?php echo "Please Update Company Info" ?></p>
							<?php
						}
					?>
					<a href="/<?php echo $row['username']?>/" class="profile-btn">View Profile</a>
				</div>
				<div class="col-md-12 connect-msg-btn">
					<div class="col-md-6">
						<?php echo Wo_GetFollowButton($row['user_id']);?>
					</div>
					<div class="col-md-6">
						<button type="button" class="btn btn-default btn-md" onclick="location.href='#'">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-square"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
							<span class="button-text"> &nbsp;Message</span>
						</button>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>

	<style type="text/css">
	.display-none-user-<?php echo $wo['user']['user_id']; ?> {
		display: none;
	}
</style>
