<?php
global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

/*ini_set('display_errors',1);
error_reporting(E_ALL);
*/

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


	$perpage = 5;
	$page_number = $_POST['page_number'];
	if($_POST['hidden_submit'] == 'submit_form'){
		$address = $_POST['address'];
		$property_type = $_POST['property_type'];
		$finance_type = $_POST['finance_type'];
		$deal_type = $_POST['deal_type'];
		$beds = $_POST['beds'];
		$baths = $_POST['baths'];
		$min_area = $_POST['min_area'];
		$max_area = $_POST['max_area'];
		$min_amount = $_POST['min_amount'];
		$max_amount = $_POST['max_amount'];
		$offset_input = $_POST['offset_input'];
		$sort_order = $_POST['sort_order'];
		
		/*if (empty($address)){
			$address = '(No address)';
		}*/
		$query = "SELECT * FROM `Wo_Filter` B left join `Wo_Listing` A on B.property_id =A.id WHERE A.status = 1";

		if(!empty($min_amount) && !empty($max_amount)){
			$query.=" AND B.price_range BETWEEN $min_amount AND $max_amount";
		}
		if(!empty($address)){
			$query.=" And B.address like '%$address%'";
		}
		if($property_type != 'pro_type'){
			$query.=" AND B.property_type = '$property_type'";
		}
// 		if($finance_type != 'ava_finance'){
// 			$query.=" AND B.available_finance = '$finance_type'";
// 		}
		if($deal_type != 'deal_type'){
			$query.=" AND B.deal_type LIKE '%$deal_type%'";
		}
		if($beds != 'beds'){
			$query.=" AND B.bedroom = $beds";
		}
		if($baths != 'bath'){
			$query.=" AND B.bathroom = $baths";
		}
		if(!empty($min_area) && !empty($max_area)){
			$query.=" AND B.area BETWEEN $min_area AND $max_area";
		}
		if(empty($min_area) && !empty($max_area)){
			$query.=" AND B.area < $max_area";
		}
		if(!empty($min_area) && empty($max_area)){
			$query.=" AND B.area > $min_area ";
		}
		
			//B.price_range   A.dtae_time
		
		if($sort_order=="dtae_time_desc"){
		  	 $query.=" ORDER BY A.dtae_time DESC ";
		}

		if($sort_order=="dtae_time_asc"){
		  	 $query.=" ORDER BY A.dtae_time ASC ";
		}
		
		if($sort_order=="price_range_low"){
		  	 $query.=" ORDER BY B.price_range ASC ";
		}	

		if($sort_order=="price_range_high"){
		  	 $query.=" ORDER BY B.price_range DESC ";
		}				
		
		$querypagination = $query;
		$query.= " LIMIT ".$offset_input.", ".$perpage;
		
		
	
		
	} else {
		$query = "SELECT * FROM `Wo_Listing` WHERE status = 1 LIMIT ".$offset_input.",".$perpage;

	}

	
	/*$querypagination = "SELECT * FROM `Wo_Listing` WHERE status = 1";*/

	$queryrespagiantion = mysqli_query($sqlConnect,$querypagination);

	$numrows = mysqli_num_rows($queryrespagiantion);

	$totalPages = ceil($numrows / $perpage);

	$query = mysqli_query($sqlConnect,$query); 

	$k=0; 
	while($row = mysqli_fetch_assoc($query)) {

					$tab1 = json_decode($row["tab1"]);
					$tab4 = json_decode($row["tab4"]);
					$tab5 = json_decode($row["tab5"]);
					$tab6 = unserialize($row["tab6"]);
					$tab2 = json_decode($row["tab2"]);
					$prop_year_built = $tab1->constructions_year;
				/*	echo "<pre>";
						print_r($tab2);
					echo "</pre>";*/
					if($row['deal_type']!="") {
						$deal_typearr = unserialize($row['deal_type']);
						$deal_type = implode(",", $deal_typearr);
					}
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
				<div class="<?php if($_REQUEST['listtypeview'] == 'list') { ?> col-md-12 <?php } ?> listing-item2 item-id-<?php echo $row["id"]; ?> <?php echo $row["user_id"]; ?> <?php if($_REQUEST['listtypeview'] == 'grid') { ?> col-md-3 col-sm-6 <?php } ?>">
							<div class="listing-item-wrap">
							    
							<?php if($statustag!="") echo '<span class="featured-tag">'.$statustag.'</span>'; ?>
								<div class="left">
								     <?php if($tab1->deal_type!="") : ?>
                            	        <div class="deal_type">
                            	          <span>
                            	            <?php echo $tab1->deal_type; ?>
                            	          </span>
                            	        </div>
                            		  <?php endif; ?>
									<div class="fav-icon">
										<span <?php if($favourite==1) echo 'style="display:none"'; ?> class="glyphicon glyphicon-heart-empty make-favourite" data-id="<?php echo $row["id"]; ?>" aria-hidden="true"></span>
										<span <?php if($favourite==0) echo 'style="display:none"'; ?> class="glyphicon glyphicon-heart make-favourite" data-id="<?php echo $row["id"]; ?>" aria-hidden="true"></span>
									</div>
									<?php

									$server = $_SERVER['HTTP_HOST'].'/themes/wondertag/uploads_images/'.$tab6[0];


									$address = $tab1->entered_address;
									

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

									$slug2 = $row["id"];

								// 	if ($wo['loggedin'] == true) {
								// 		if(get_property_slug($row["id"])!="")
								// 			$slug = get_property_slug($row["id"]);
								// 	}
									
									$single_page_url = $wo['config']['site_url']."/property/".$slug2; ?>
									<a href="<?php echo $single_page_url; ?>">
										<div class="listing-wrap">
											<?php if ( empty($tab6) ){ ?>
													<img src="/upload/photos/d-property.jpg" />
													<?php } else { ?>
													<img src="http://<?php echo $server; ?>" alt="Listing 1"/>
													<?php }?>
											<div>
												<div class="col-md-5 price">
													<h5 class="price_div_6">$<?php echo number_format($tab1->flip_price); ?></h5>
												</div>
												<div class="col-md-7 amenities">
													<p><?php echo $tab1->beds; ?> <i class="fa fa-bed"></i> | <?php echo $tab1->baths; ?> <i class="fa fa-bath"></i> | <?php echo $tab1->property_size; ?> sq. ft.</p>
												</div>
											</div>
										</div>
									</a>
								</div>

								<div class="right">

									<a href="<?php echo $single_page_url; ?>">
									<div class="col-md-12 address">
											<h6><?php if(!empty($tab1->listing_title)){echo $tab1->listing_title;}else{echo substr($address,0,30);} ?></h6>
									</div>
									     <?php
                                          $p_arv = $tab1->flip_arv;
                                          $purchace_price = $tab1->flip_price;
                                          $rehab_price = $tab1->flip_ext_repair;
                                          
                                          $equility = $p_arv - $purchace_price - $rehab_price;
                                          ?>
                                		<div class="col-md-12 other_info">
                                	   <span class="att_label">ARV</span><span class="att_value">$<?php echo number_format($tab1->flip_arv);?></span>
                                	   <span class="att_label">Equity</span><span class="att_value">$<?php echo number_format($equility);?></span>
                                	   </div>
									</a>
									<div class="col-md-6 spec-col spec-col-1">
										<div>
											<div class="col-xs-6 text-left no-padding">Year Built</div>
											<div class="col-xs-6 text-right no-padding">
											<?php echo $tab1->constructions_year; ?></div>
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
											<a href="<?php echo $single_page_url; ?>"class="btn btn-view-details">View details</a>	<a href="#"class="btn btn-go-tour">Go tour</a>
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
		   		echo ' <a href="javascript:void(0);" class="pagination_class '.$active.'" data-startsat="'.$startAt.'">'.$i.'</a>';

		   } ?>
		   <input class="total_pages" value="<?php echo $totalPages; ?>" type="hidden">
		   <a href="javascript:void(0);" class="left_right_pagination_arrow right_arrow active" value="right">»</a>
		</div>

	<?php
	if($k==0) {
		
		echo "<h4 style='text-align: center'>No Listing Found!!</h4>";
	}
	die;
?>