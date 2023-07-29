<?php
global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
/*
ini_set('display_errors',1);
error_reporting(E_ALL);*/


$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);
	$user_id = $wo['user']['user_id'];
	function get_property_slug($propertyid) {
		global $sqlConnect;
		$query = mysqli_query($sqlConnect,"SELECT property_slug FROM `Wo_Listing_Meta` WHERE property_id=".$propertyid);
		$row = mysqli_fetch_array($query);

		$slug = $row['property_slug'];
		return $slug;
	}

	function get_favourite($propertyid) {

		global $sqlConnect,$wo;
		$user_id = $wo['user']['user_id'];
		$query = mysqli_query($sqlConnect,"SELECT count(*)  as countfavourites FROM `Wo_Favourite_Peoperties` WHERE property_id=".$propertyid." AND user_id=".$user_id);

		//echo "SELECT count(*)  as countfavourites FROM `Wo_Favourite_Peoperties` WHERE property_id=".$propertyid." AND user_id=".$user_id;
		$row = mysqli_fetch_array($query);
		$favourite = 0;

		if($row['countfavourites'] > 0)
			$favourite = 1;
		return $favourite;

	}

	function setmapmarker($address,$propid) {
					
		echo '<script type="text/javascript"> codeAddress('.$address.','.$propid.');</script>';
	}


	/* start Searched property listing  price_range_low
price_range_high
dtae_time_asc
dtae_time_desc*/

	$perpage = 5;
	$page_number = $_POST['page_number_advance'];


	if($_POST['hidden_submit'] == 'submit_form'){
		
		

		$address = $_POST['keyword'];
		$property_type = $_POST['type'];
		$finance_type = $_POST['finance_type'];
		$deal_type = $_POST['property_deal_type'];
		$beds = $_POST['bedrooms'];
		$baths = $_POST['bathrooms'];
		$min_area = $_POST['min_area_drop'];
		$max_area = $_POST['max_area_drop'];
		$min_amount = $_POST['min_price_drop'];
		$max_amount = $_POST['max_price_drop'];

		$offset_input = $_POST['offset_input_advance'];
		/*if (empty($address)){
			$address = '(No address)';
		}*/
		$query = "SELECT * FROM `Wo_Filter` B left join `Wo_Listing` A on B.property_id =A.id WHERE A.status = 1";

		if(!empty($min_amount) && !empty($max_amount)){
			if($min_amount!=0 && $max_amount!=0)
				$query.=" AND B.price_range BETWEEN $min_amount AND $max_amount";
		}


		if(!empty($address)){
			$address = explode(",", $address);
			array_pop($address);
			array_pop($address);
			$query.=" And ( ";
			foreach ($address as $key => $value) {
				if($key==0)
					$query.=" B.address like '%$value%' ";
				else	
					$query.=" OR B.address like '%$value%' ";
			}
			$query.=" ) ";
			
		}
		if($property_type != 'pro_type'){
			$query.=" AND B.property_type = '$property_type'";
		}
		/*if($finance_type != 'ava_finance'){
			$query.=" AND B.available_finance = '$finance_type'";
		}*/
		if($deal_type != 'deal_type'){
			$query.=" AND B.deal_type LIKE '%$deal_type%'";
		}
		if($beds != 'beds'){
			$query.=" AND B.bedroom = $beds";
		}
		if($baths != 'baths'){
			$query.=" AND B.bathroom = $baths";
		}
		if(!empty($min_area) && !empty($max_area)){
			$query.=" AND B.area BETWEEN $min_area AND $max_area";
		}
		if(empty($min_area) && !empty($max_area)){
			$query.=" AND B.area < $max_area";
		}
		if(!empty($min_area) && empty($max_area)){
			$query.=" AND B.area > $min_area";
		}

		if(isset($_POST['days_on_strastic']) && $_POST['days_on_strastic']!="Any") {
			if($_POST['days_on_strastic'] >0 )
				$query.=" AND A.dtae_time >= DATE(NOW()) - INTERVAL ".$_POST['days_on_strastic']." DAY";
			else
				$query.=" AND A.dtae_time < DATE(NOW()) + INTERVAL ".$_POST['days_on_strastic']." DAY";

		}



		/*price_range_high
dtae_time_asc
dtae_time_desc*/
		if(isset($_POST['sort_value']) && $_POST['sort_value'] == 'price_range_high') {
			$query.=" ORDER BY B.price_range DESC";
		}

		if(isset($_POST['sort_value']) && $_POST['sort_value'] == 'price_range_low') {
			$query.=" ORDER BY B.price_range ASC";
		}

		if(isset($_POST['sort_value']) && $_POST['sort_value'] == 'dtae_time_asc') {
			$query.=" ORDER BY A.dtae_time ASC";
		}

		if(isset($_POST['sort_value']) && $_POST['sort_value'] == 'dtae_time_desc') {
			$query.=" ORDER BY A.dtae_time DESC";
		}

		$querypagination = $query;
		$query.= " LIMIT ".$offset_input.", ".$perpage;

	} else {
		/*$query = "SELECT * FROM `Wo_Listing` WHERE status = 1";*/
		$query = "SELECT * FROM `Wo_Listing` WHERE status = 1 LIMIT ".$offset_input.",".$perpage;
	}

	$query = mysqli_query($sqlConnect,$query); 

	echo "<h4>Showing ".mysqli_num_rows($query) ." Homes for Sale</h4>";
	$k=0; ?>


	<script type="text/javascript">
				initialize();
				function codeAddress(address,propid) {


			      	var iconpath = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|F6741C");
					
					var geocoder = new google.maps.Geocoder();

					geocoder.geocode( { 'address': address}, function(results, status) {

					  if (status == google.maps.GeocoderStatus.OK) {
				    	window.addresslat = results[0].geometry.location.lat();
					    window.addresslong = results[0].geometry.location.lng();

			          	var latlng = new google.maps.LatLng(addresslat, addresslong);

			          	var marker = new google.maps.Marker({
			              position: latlng,
			              map: map,
			              icon:iconpath
			          	});

			          	var contentString = '<div class="listing-item item-id-'+propid+'"><div class="listing listing-grid">'+$(".item-id-"+propid).html()+'</div></div>';

			            var infowindow = new google.maps.InfoWindow({
			                content: '',
			                maxWidth: 250 
			              });
						google.maps.event.addListener(marker, 'click', function() {
			              /*jQuery('#map img[src="https://maps.gstatic.com/mapfiles/api-3/images/mapcnt6.png"]').trigger('click');*/
			              infowindow.setContent(contentString);
			              infowindow.open(map, this);
			              /*window.location.href = this.url;*/
			            });
			           
			            map.setCenter(latlng);
			        }
			    });

			}
			</script>

	<?php

	$queryrespagiantion = mysqli_query($sqlConnect,$querypagination);

	$numrows = mysqli_num_rows($queryrespagiantion);

	$totalPages = ceil($numrows / $perpage);

	$k=0; 
	while($row = mysqli_fetch_assoc($query)) {

		$tab1 = json_decode($row["tab1"]);
		$tab4 = json_decode($row["tab4"]);
		$tab6 = unserialize($row["tab6"]);

		$seconds = strtotime(date('Y-m-d H:i:s')) - strtotime($row['dtae_time']);
		$months = floor($seconds / (3600*24*30));
		$day = floor($seconds / (3600*24));
		$hours = floor($seconds / 3600);
		$mins = floor(($seconds - ($hours*3600)) / 60);
		$secs = floor($seconds % 60);

		if($seconds < 60)
			$time = $secs." seconds ago";
		else if($seconds < 60*60 )
			$time = $mins." min ago";
		else if($seconds < 24*60*60)
			$time = $hours." hours ago";
		else if($seconds < 24*60*60*60)
			$time = $day." day ago";
		else
			$time = $months." month ago";

		$year = $row['dtae_time'];
		$datetime = explode(" ",$year);
		$date = $datetime[0];
		$date = explode("-",$date);

		$favourite = get_favourite($row["id"]);

		$property_status = $row["status"];

		switch ($property_status) {
			case '4':
				$statustag = "Under Contract";
				break;

			case '3':
				$statustag = "Closed";
				break;
			default:
				$statustag = "";
			break;
		}  ?>
			<div class="col-md-6 listing-item item-id-<?php echo $row["id"]; ?> <?php echo $row["user_id"]; ?>">
				<div class="listing-item-wrap">
				<?php if($statustag!="") echo '<span class="featured-tag">'.$statustag.'</span>'; ?>
					<div class="left">
						<div class="deal_type">
							<span>Fix &amp; Flip, For Sale</span>
						</div>
						<div class="fav-icon">
							<span <?php if($favourite==1) echo 'style="display:none"'; ?> class="glyphicon glyphicon-heart-empty make-favourite" data-id="<?php echo $row["id"]; ?>" aria-hidden="true"></span>
							<span <?php if($favourite==0) echo 'style="display:none"'; ?> class="glyphicon glyphicon-heart make-favourite" data-id="<?php echo $row["id"]; ?>" aria-hidden="true"></span>
						</div>
						<!-- <div class="fav-icon">
							<span class="glyphicon glyphicon-heart-empty" aria-hidden="true"></span>
							<span class="glyphicon glyphicon-heart" aria-hidden="true"></span>
						</div> -->
						<?php

						$server = $_SERVER['HTTP_HOST'].'/themes/wowonder/uploads_images/'.$tab6[0];


						$address = $tab1->entered_address;
						setmapmarker('"'.$tab1->entered_address.'"',$row["id"]);
						if (empty($address)){
							$address = '(No address)';
						}
						
						$singlepageurarr = explode(" ",strtolower($address));
						$slug = implode("",explode(",",implode("-", $singlepageurarr)));

						$query_meta= mysqli_query($sqlConnect,"SELECT * FROM `Wo_Listing_Meta` WHERE property_id= ".$row["id"]);
						$row_meta = mysqli_num_rows($query_meta);

						
						if($row_meta == 0) {

							$querymeta   = "INSERT INTO Wo_Listing_Meta (`property_id`, `property_slug`) VALUES('".$row["id"]."','{$slug}')";

							$sql_querymeta = mysqli_query($sqlConnect, $querymeta);
						}

						$slug = $row["id"];
						if ($wo['loggedin'] == true) {
							if(get_property_slug($row["id"])!="")
								$slug = get_property_slug($row["id"]);
							}
						
						$single_page_url = $wo['config']['site_url']."/property/".$slug;
						?>
						<a href="<?php echo $single_page_url; ?>">
							<div class="listing-wrap">
								<?php if ( empty($tab6) ){ ?>
										<img src="/upload/photos/d-property.jpg" />
										<?php } else { ?>
										<img src="https://<?php echo $server; ?>" alt="Listing 1"/>
										<?php }?>
									<div class="col-md-12 price">
										<h5 class="price_div_5">$<?php echo number_format($tab1->flip_price); ?></h5>
									</div>

							</div>
							<div class="listinDetails-bottom">
								<div class="col-md-12 amenities">
									<p><?php echo $tab1->beds; ?> <i class="fa fa-bed"></i> | <?php echo $tab1->baths; ?> <i class="fa fa-bath"></i> | <?php echo $tab1->property_size; ?> sq. ft.</p>
								</div>
							</div>
						</a>
					</div>
					
					
					

					<div class="right">

						<a href="<?php echo $single_page_url; ?>">
						<div class="col-md-12 address">
								<h6><?php echo substr($address,0,30); ?></h6>
						</div>
						</a>
						<div class="col-md-6 spec-col spec-col-1">
							<div>
								<div class="col-xs-6 text-left no-padding">Year Built</div>
								<div class="col-xs-6 text-right no-padding"><?php echo $tab1->constructions_year; ?></div>
								<div class="clearfix"></div>
							</div>
							<hr>
							<div>
								<div class="col-xs-6 text-left no-padding">HOA</div>
								<div class="col-xs-6 text-right no-padding"><?php echo $tab4->association_fee; ?>/<?php echo $tab4->association_fee_due; ?></div>
								<div class="clearfix"></div>
							</div>
						</div>
						<hr class="hidden-lg hidden-md">
						<div class="col-md-6 spec-col spec-col-2">
							<div>
								<div class="col-xs-6 text-left no-padding">On Strastic</div>
								<div class="col-xs-6 text-right no-padding"><?php echo $time; ?></div>
								<div class="clearfix"></div>
							</div>
							<hr>

							<div>
								<div class="col-xs-6 text-left no-padding">Status</div>
								<div class="col-xs-6 text-right no-padding">Active</div>
								<div class="clearfix"></div>
							</div>


						</div>
						<div class="col-md-12 fav-cta no-padding">

							<div class="col-md-2 add-favorite ">
								<span <?php if($favourite==1) echo 'style="display:none!important"'; ?> class="glyphicon glyphicon-heart-empty make-favourite" data-id="<?php echo $row["id"]; ?>" ></span>
								<span <?php if($favourite==0) echo 'style="display:none!important"'; ?> class="glyphicon glyphicon-heart make-favourite" data-id="<?php echo $row["id"]; ?>" ></span>

							</div>
							<div class="col-md-10 cta">
								<a href="<?php echo $single_page_url; ?>" class="btn btn-view-details">View details</a>	<a href="#"class="btn btn-go-tour">Go tour</a>
							</div>
							<div class="clear"></div>
						</div>
					</div>
					<div class="clear"></div>
				</div>
			</div>
			

			<?php 
			$k++;
		} ?>
		<div class="clear"></div>
		<div class="pagination">
		   <a href="javascript:void(0);" class="left_right_pagination_arrow left_arrow">«</a>
		   <?php 
		  
		   for ($i=1; $i <= $totalPages; $i++) { 
	   		 	$active = "";
	   		 	$startAt = $perpage * ($i - 1);

		   		if($i==$page_number) { $active = "active";	} 		   			
		   		echo ' <a href="javascript:void(0);" class="pagination_class_advance '.$active.'" data-startsat="'.$startAt.'">'.$i.'</a>';

		   } ?>
		   <input class="total_pages_advance" value="<?php echo $totalPages; ?>" type="hidden">
		   <a href="javascript:void(0);" class="left_right_pagination_arrow right_arrow active" value="right">»</a>
		</div>
		
	<?php
	if($k==1)
		echo "<script> map.setZoom(18); </script>";
	if($k==0) {
		echo "<script> setcentercustom ('".$address."');  map.setZoom(15); </script>";
		
		echo "<h4 style='text-align: center;'>No Listing Found!!</h4>";
	}
	die;
?>
<style type="text/css">
			.offer_from_list {
		text-align: right;
		list-style: disc;
	}

	.offer_to_list {
		text-align: left;
		list-style: disc;
	}

	.offer-counter textarea {
		width: 100%;
		height: 100px;
	}

	.offer_submit_reply {
		float: right;
	}

	.show_message_otp {
	}

	.listing-grid .cta {
		width: 100%;
	}
	.gm-style-iw div { overflow:hidden!important; }

	.pagination_class_advance.active {
		color: #F58220!important;
	}
	/*7-3-19*/
	.listing-wrap{ min-height: 75px; }
	.listing-grid .amenities {padding:0 15px 0; }
	.amenities p {height: auto; margin: 10px 0; }
	.listing-grid .listing-wrap > div {padding:0 15px; }
	.listing-grid .price {position: absolute; bottom: 0; left: 0px; padding-left: 15px; }
	
	.listing-grid .listing-wrap p {font-weight: bold; font-size: 11px; text-align: left; line-height: normal; margin: 10px 0 0; height: auto; }
	@media(max-width:1366px){ .listing-grid .cta a{ padding: 7px 10px; }  }
	@media(max-width:1250px){ .listing-grid .cta a{ padding: 7px; }  }
	@media(max-width:991px){ .listing-grid .price{ width:100%; }  }
	@media(max-width:767px){ .listing-grid .listing-wrap h5{ font-size:18px; } .listing-grid .cta a {padding: 10px 15px; line-height: 10px; height: auto; margin: 4px; } }
	
</style>