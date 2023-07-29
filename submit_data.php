<?php

global $wo, $sqlConnect;
$root=__DIR__;
require_once($root.'/config.php');
require_once('assets/init.php');

$listing_idd  = $_POST['listing_idd'];
$user_id = $wo['user']['user_id'];

if(isset($_POST['action']) && $_POST['action'] == "delete_property"){
    
    $ds = DIRECTORY_SEPARATOR;
    
    $storeFolder = 'themes/wondertag/uploads_images';
    $uploadDir = 'themes/wondertag/uploads_docs';
    $uploadDir_jpg = 'themes/wondertag/uploads_docs/converted_image';
    
    
    $prop_id = $_POST['pid'];
    
    // SELECT PROPERTY DETAILS BY ID
    $queryfilter = mysqli_query($sqlConnect,"SELECT * FROM ".T_LISTINGS." WHERE `id` = $prop_id");
	$rowfilter = mysqli_fetch_array($queryfilter);
    
    // DELETE PICTURES OF A PROPERTY
    $tab6 = unserialize($rowfilter["tab6"]);
    foreach ($tab6 as $key => $value) {
        $targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;
        $filename = $targetPath.$value;  
        unlink($filename); 
    }
    
    // DELETE DOCUMENT ATTACHED TO THE PROPERTY
    $tab7 = unserialize($rowfilter["tab7"]);
    foreach ($tab7 as $key7 => $value7) {
        $targetPathD = dirname( __FILE__ ) . $ds. $uploadDir . $ds;
        $filenameD = $targetPathD.$value7; 
        
        $targetPathDI = dirname( __FILE__ ) . $ds. $uploadDir_jpg . $ds;
        $filenameDI = $targetPathDI.$value7; 
         
        unlink($filenameD); 
        unlink($filenameDI);
    }
    
    // package posts
    if(isset($_POST['access_id'])){
        $access_id = $_POST['access_id'];
    }
    
    if(isset($_POST['path'])){
        $path = $_POST['path'];
    }
    
    if(isset($_POST['access_remain'])){
        $access_remain = $_POST['access_remain'];
    }
    
    if(isset($_POST['access_used'])){
        $access_used = $_POST['access_used']; 
    }
    
    $deleteProp = deletePropertyById($prop_id);
    
    
    if($deleteProp){
        
        if($access_id == 2){
            packageFeatureChecker($path,$access_used,2);
        }
        
        $data = array(
            'status' => 200,
            'message' => 'Property Deleted Successfully',
        );
        
    }else{
        
        $data = array(
            'status' => 400,
            'message' => 'Error while deleting Property',
        );
        
    }
    
    header("Content-type: application/json");
    echo json_encode($data);
    die;
    
}

if(isset($_POST['action']) && $_POST['action'] == "delete_all_property"){
    
  $ds = DIRECTORY_SEPARATOR;
  
  $storeFolder = 'themes/wondertag/uploads_images';
  $uploadDir = 'themes/wondertag/uploads_docs';
  $uploadDir_jpg = 'themes/wondertag/uploads_docs/converted_image';
  
  
  $ids = filter('ids');
  if(count($ids)){

    foreach($ids as $prop_id) {
  
      // SELECT PROPERTY DETAILS BY ID
      $queryfilter = mysqli_query($sqlConnect,"SELECT * FROM ".T_LISTINGS." WHERE `id` = $prop_id");
      $rowfilter = mysqli_fetch_array($queryfilter);
      
      // DELETE PICTURES OF A PROPERTY
      $tab6 = unserialize($rowfilter["tab6"]);
      foreach ($tab6 as $key => $value) {
          $targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;
          $filename = $targetPath.$value;  
          unlink($filename); 
      }
      
      // DELETE DOCUMENT ATTACHED TO THE PROPERTY
      $tab7 = unserialize($rowfilter["tab7"]);
      foreach ($tab7 as $key7 => $value7) {
          $targetPathD = dirname( __FILE__ ) . $ds. $uploadDir . $ds;
          $filenameD = $targetPathD.$value7; 
          
          $targetPathDI = dirname( __FILE__ ) . $ds. $uploadDir_jpg . $ds;
          $filenameDI = $targetPathDI.$value7; 
          
          unlink($filenameD); 
          unlink($filenameDI);
      }
      
      // package posts
      if(isset($_POST['access_id'])){
          $access_id = $_POST['access_id'];
      }
      
      if(isset($_POST['path'])){
          $path = $_POST['path'];
      }
      
      if(isset($_POST['access_remain'])){
          $access_remain = $_POST['access_remain'];
      }
      
      if(isset($_POST['access_used'])){
          $access_used = $_POST['access_used']; 
      }
      
      $deleteProp = deletePropertyById($prop_id);
      
      if($deleteProp){
          
          if($access_id == 2){
              packageFeatureChecker($path,$access_used,2);
          }
          
      }
    }

    $data = array(
      'status' => 200,
      'message' => 'All the selected properties has been deleted successfully!',
    );

  }else{
    $data = array(
      'status' => 400,
      'message' => "Please selected atleast one listing to delete",
    );
  
  }
  
  header("Content-type: application/json");
  echo json_encode($data);
  die;
  
}

if(isset($_POST['action']) && $_POST['action']=="delete_my_buyer_matches") {

	$criteria_id = $_POST['eid'];

	 $query_one = mysqli_query($sqlConnect, "DELETE FROM `contact` WHERE id = $criteria_id AND type = 2");
			
     if($query_one){
         $data = array(
            'status' => 200,
            'message' => 'Buyer Criteria Deleted Successfully',
        );
     }else{
        $data = array(
            'status' => 400,
            'message' => 'Error while deleting Buyer Criteria',
        );
     }
     
    header("Content-type: application/json");
    echo json_encode($data);
    die;
}

if(isset($_POST['action']) && $_POST['action']=="delete_my_contact") {

	$criteria_id = $_POST['eid'];

	 $query_one = mysqli_query($sqlConnect, "DELETE FROM `contact` WHERE id = $criteria_id AND type = 1");
			
     if($query_one){
         $data = array(
            'status' => 200,
            'message' => 'Contact Deleted Successfully',
        );
     }else{
        $data = array(
            'status' => 400,
            'message' => 'Error while deleting Contact',
        );
     }
     
    header("Content-type: application/json");
    echo json_encode($data);
    die;
}

if(isset($_POST['action']) && $_POST['action']=="delete_my_property") {

	$criteria_id = $_POST['eid'];

	 $query_one = mysqli_query($sqlConnect, "DELETE FROM `contact` WHERE id = $criteria_id AND type = 3");
			
     if($query_one){
         $data = array(
            'status' => 200,
            'message' => 'Property Deleted Successfully',
        );
     }else{
        $data = array(
            'status' => 400,
            'message' => 'Error while deleting Property',
        );
     }
     
    header("Content-type: application/json");
    echo json_encode($data);
    die;
}

if(isset($_POST['action']) && $_POST['action']=="delete_my_email") {

	$email_id = $_POST['e_id'];

	 $query_one = mysqli_query($sqlConnect, "DELETE FROM `My_Email_Lists` WHERE id = $email_id AND user_id = $user_id ");
			
     if($query_one){
         
         $data = array(
            'status' => 200,
            'message' => 'Email Data Deleted Successfully',
        );
        
     }else{
         $data = array(
            'status' => 400,
            'message' => 'Error while deleting Email Data',
        );
     }
     
    header("Content-type: application/json");
    echo json_encode($data);
    die;
}

if(isset($_POST['action']) && $_POST['action']=="delete_my_number") {

	$sms_id = $_POST['e_id'];

	 $query_one = mysqli_query($sqlConnect, "DELETE FROM `My_SMS_Contact_Lists` WHERE id = $sms_id AND user_id = $user_id ");
			
     if($query_one){
         $data = array(
            'status' => 200,
            'message' => 'Phone Number Data Deleted Successfully',
        );
     }else{
         $data = array(
            'status' => 400,
            'message' => 'Error while deleting Phone Number Data',
        );
     }
     
    header("Content-type: application/json");
    echo json_encode($data);
    die;
}

if(isset($_POST['action']) && $_POST['action']=="delete_my_list") {

	$list_id = $_POST['l_id'];
	

	 $query_one = mysqli_query($sqlConnect, "DELETE FROM `My_Email_Lists` WHERE list_id = $list_id AND user_id = $user_id ");
	 
			
     if($query_one){
         $query_two = mysqli_query($sqlConnect, "DELETE FROM `My_List` WHERE id = $list_id AND user_id = $user_id ");
         if($query_two){
             
             
             $data = array(
                'status' => 200,
                'message' => 'List Data Deleted Successfully',
            );
         }else{
             
            $data = array(
                'status' => 400,
                'message' => 'Error while deleting List',
            );
             
         }
         
     }else{
         $data = array(
            'status' => 400,
            'message' => 'Error while deleting List Data',
        );
     }
     
    header("Content-type: application/json");
    echo json_encode($data);
    die;
}

if(isset($_POST['action']) && $_POST['action']=="delete_my_sms_list") {

	$list_id = $_POST['l_id'];
	

	 $query_one = mysqli_query($sqlConnect, "DELETE FROM `My_SMS_Contact_Lists` WHERE list_id = $list_id AND user_id = $user_id ");
	 
			
     if($query_one){
         $query_two = mysqli_query($sqlConnect, "DELETE FROM `My_SMS_List` WHERE id = $list_id AND user_id = $user_id ");
         if($query_two){
             
             
             $data = array(
                'status' => 200,
                'message' => 'List Data Deleted Successfully',
            );
         }else{
             
            $data = array(
                'status' => 400,
                'message' => 'Error while deleting List',
            );
             
         }
         
     }else{
         $data = array(
            'status' => 400,
            'message' => 'Error while deleting List Data',
        );
     }
     
    header("Content-type: application/json");
    echo json_encode($data);
    die;
}

if(isset($_POST['action']) && $_POST['action']=="save_tag") {
    
	$params = array();
    $form_datanew = parse_str($_POST['form_data'], $params);
    
    $form_data =	json_encode($params);
    
    $query_one = mysqli_query($sqlConnect, "UPDATE `Wo_Listing` SET `tab8` = '$form_data' WHERE `id`='{$listing_idd}' ");
    if($query_one){
        $query = "SELECT * FROM " . T_LISTINGS . ' WHERE tab1 LIKE \'%"parent_id":"' . $listing_idd . '%\' ';
        $listings = getTableRows($query);
        
        if(count($listings)){
            foreach($listings as $list){

                $query = "UPDATE `Wo_Listing` SET `tab8` = '$form_data' WHERE `id`='" . $list['id'] ."' ";
                
                if($sqlConnect->query($query)){
                    sendTagChangeSystemNotification($list['id'], $user_id);
                    sendTagChangeEmailNotification($list['id'], $user_id);
                }

            }
        }
    }
			
}


if(isset($_POST['action']) && $_POST['action']=="save_all_tag") {


    $status = 400;
    $ids = filter('ids');
    $tags = array_map(function($tag){
      return $tag['value'];
    }, filter('tags'));

    if(count($ids)) {

        foreach($ids as $id) {

            $where = [
              'id' => $id
            ];

            $listing = getTableData('wo_listing', $where, 1);
            if($listing) {

                $newTags = $tags;
                $oldTags = json_decode($listing['tab8'], 1);

                foreach($oldTags['tags'] as $tag) {
                  $newTags[] = $tag;
                }

                $newTags = array_unique($newTags);
                $form_data =	json_encode([
                  'tags' => $newTags
                ]);

                $query = "UPDATE `Wo_Listing` SET `tab8` = '$form_data' WHERE `id` = '". $id ."'";
	              mysqli_query($sqlConnect, $query);

            }
        }

        $message = "Tags has been added to selected listings";
        $status = 200;
    }else{
      $message = "Please select atleast one listing";
    }
    
    
        
    header("Content-type: application/json");
    echo json_encode([
      'message' => $message,
      'status' => $status
    ]);
    die(); 
}

if(isset($_POST['action']) && $_POST['action']=="load_tag") { 

  $query = mysqli_query($sqlConnect,"SELECT * FROM `Wo_Listing` WHERE id='".$_POST['listing_idd']."' ");

  while($row = mysqli_fetch_array($query)) {
    $tab8 = json_decode($row["tab8"]); ?>
    <h3>Tags</h3>
    <!--<input type="checkbox" name="tags[]" value="New" <?php if(in_array("New",$tab8->tags)){echo "checked";}?>>&nbsp;New&nbsp;<br>-->
    <input type="checkbox" name="tags[]" value="Active" <?php if(in_array("Active",$tab8->tags)){echo "checked";}?>>&nbsp;Active&nbsp;<br>
    <input type="checkbox" name="tags[]" value="Pending" <?php if(in_array("Pending",$tab8->tags)){echo "checked";}?>>&nbsp;Pending&nbsp;<br>
    <input type="checkbox" name="tags[]" value="Under Contract" <?php if(in_array("Under Contract",$tab8->tags)){echo "checked";}?>>&nbsp;Under Contract&nbsp;<br>
    <input type="checkbox" name="tags[]" value="Sold" <?php if(in_array("Sold",$tab8->tags)){echo "checked";}?>>&nbsp;Sold&nbsp;<br>
    <input type="checkbox" name="tags[]" value="Back On Market" <?php if(in_array("Back On Market",$tab8->tags)){echo "checked";}?>>&nbsp;Back On Market&nbsp;<br>
    <input type="checkbox" name="tags[]" value="Price Cut" <?php if(in_array("Price Cut",$tab8->tags)){echo "checked";}?>>&nbsp;Price Cut&nbsp;<br>
    <center><input type="button" onClick="saveTags()" id="save_tags" class="save_tags btn btn-strastic-orange btn-primary" value="Save"></center>

    <?php }  
}


if(isset($_POST['action']) && $_POST['action'] == 'loadPropMore_Details'){
    
    if( isset($_POST['row']) && isset($_POST['limit']) ){
        
        $start = $_POST['row'];
        $Syslimit = $_POST['limit'];
        
        if(isset($_POST['search']) && !empty($_POST['search'])){
    
            $search = $_POST['search'];
        
            $sqlAddress ='tab1 LIKE \'%"entered_address":"'.$search."%' ";
            $sqlState = 'OR tab1 LIKE \'%"state":"'.$search."%' ";
            $sqlCity = 'OR tab1 LIKE \'%"city":"'.$search."%' ";
            $sqlCityF = 'OR tab1 LIKE \'%"city_r":"'.$search."%' ";
            
            $cc = $sqlAddress. $sqlState. $sqlCity. $sqlCityF;
            $qr = "AND $cc AND `user_id` = $user_id ORDER BY `id` desc LIMIT $start,$Syslimit";
            
        }else{
            
            $search = "";
            $qr = " `user_id` = $user_id ORDER BY `id` desc LIMIT $start,$Syslimit";
            
            
        }
        $prop = getAll_MyPropertyBySearch($qr);
        
            if ($prop->num_rows > 0) {
                while($row = mysqli_fetch_assoc($prop)) {
                    
                    // Tab 1 details
					$tab1 = json_decode($row["tab1"]);
					
					$title = $tab1->listing_title;
					$address = $tab1->entered_address;
					$beds = $tab1->beds;
					$baths = $tab1->baths;
					$price = number_format($tab1->flip_price);
					$constructions_year = $tab1->constructions_year;
					$arv_price = $tab1->flip_arv;
					$flip_ext_repair = $tab1->flip_ext_repair;
					$rental_price = $tab1->rental_price;
					$rental_arv = $tab1->rental_arv;
					$rental_ext_rent = $tab1->rental_ext_rent;
					$property_size = $tab1->property_size;
					
		            //  Tab 4 Details	
		            $tab4 = json_decode($row["tab4"]);
		            
		            // Tab 6 details
		            $tab6 = unserialize($row["tab6"]);
		            $server = $wo['config']['site_url'].'/themes/wondertag/uploads_images/'.$tab6[0];
		            
		            
		            // General Values
		            $prop_id = $row['id'];
		            $single_page_url = $wo['config']['site_url']."/property/".$prop_id;
		            
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
		           
		           ?> 
		        
                <div class="col-md-12 listing-item">
				    <div class="listing-item-wrap">
						<div class="row">
						    <div class="left listing-left col-sm-5">
								<!-- <div class="fav-icon">
									<span class="glyphicon glyphicon-heart-empty" aria-hidden="true"></span>
									<span class="glyphicon glyphicon-heart" aria-hidden="true"></span>
								</div> -->
								
						        <a href="<?php echo $single_page_url; ?>">
									<div class="listing-wrap">
										<?php if($statustag!="") echo '<span class="featured-tag">'.$statustag.'</span>'; ?>
										<div class="listing-fig">
										    <?php if(empty($tab6)){ ?>
										       <img src="/upload/photos/d-property.jpg" alt="Listing 1"/>
										    <?php }else{ ?>
										       <img src="<?= $server ?>" alt="Listing 1"/> 
										    <?php } ?>
										   
										</div>
										<div class="property-price">
											<div class="row">
												<div class="col-sm-5 price">
													<h5>$<?= $price ?></h5>
												</div>
												<div class="col-sm-7 amenities">
													<p class="text-right amenities-list">
														<span class="amenities-1"><?= $beds ?> <i class="fa fa-bed"></i> </span> 
														<b class="dvd_line">|</b> 
														<span class="amenities-2"><?= $baths ?> <i class="fa fa-bath"></i> </span>
														<b class="dvd_line">|</b>  
														<span class="amenities-3"><?= $property_size ?> sq. ft.</span>
													</p>
												</div>
											</div>
										</div>
	                                </div>
								</a>
				            </div>
				            <!-- ./ End listing-left col-md-5 -->

				            <div class="right listing-right col-sm-7">

								<a href="<?= $single_page_url ?>">
									<div class="prop-address">
										<h5><?= $address ?></h5>
									</div>
								</a>
					            <div class="prop-details">
                        <div class="row">
							<div class="col-sm-6 spec-col spec-col-1">
								<div class="prop-year">
									<div class="col-xs-4 text-left no-padding">Year Built</div>
									<div class="col-xs-8 text-right no-padding"><strong><?= $constructions_year ?></strong></div>
									<div class="clearfix"></div>
								</div>
								<hr>
								<div class="prop-hoa">
									<div class="col-xs-4 text-left no-padding">HOA</div>
									<div class="col-xs-8 text-right no-padding"><strong><?php //echo $tab4->association_fee; ?>/<?php //echo $tab4->association_fee_due; ?></strong></div>
									<div class="clearfix"></div>
								</div>
							</div>
							<div class="col-sm-6 spec-col spec-col-2">
								 <div class="prop-strastic">
									<div class="col-xs-4 text-left no-padding">On Strastic</div>
									<div class="col-xs-8 text-right no-padding"><strong><?= $time ?></strong></div>
									<div class="clearfix"></div>
								</div>
								<hr>

								<div class="prop-status">
									<div class="col-xs-4 text-left no-padding">Status</div>
									<div class="col-xs-8 text-right no-padding">
									    <strong class="status-pos">
									        <?php ($statustag == '') ? "Active" : $statustag ; ?>
									    </strong>
									</div>
									<div class="clearfix"></div>
								</div>
							</div>
						</div>
						<div class="fav-cta no-padding">
							<div class="row"><!-- 
								<div class="col-md-2 add-favorite">
									<span class="glyphicon glyphicon-heart-empty" aria-hidden="true"></span>
									<span class="glyphicon glyphicon-heart" aria-hidden="true"></span>
								</div> -->
								<div class="col-md-12 cta">
									<div class="prop-action">
										<a href="javascript:void(0)" onClick="javascript:openTagForm('<?= $prop_id ?>');" class="btn btn-strastic-orange">Tags</a>
										<a type="button" href="<?= $single_page_url ?>" class="btn btn-strastic-orange">View</a>
										<a type="button" href="/edit-listing/<?= $prop_id ?>" class="btn btn-strastic-orange">Edit</a>
                    <a class="btn btn-strastic-orange" onclick="loadListingID(<?= $prop_id; ?>)"  href="javascript:void();"
                      data-toggle="modal"  data-target="#modal-tools" data-backdrop="static" data-keyboard="false" > 
                      Marketing Share Tools
                    </a>
									 	<a type="button" href="#" class="btn btn-strastic-orange">Go tour</a>
									 	<div class="btn-group">
										 <a type="button" class="btn btn-strastic-orange dropdown-toggle" data-toggle="dropdown">
										 Change status <span class="caret"></span></a>
										 <ul class="dropdown-menu" role="menu">
											 <li><a data-propertyid="<?= $prop_id ?>" data-status="under-contract" class="status_change"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Under contract</a></li>
											 <li><a data-propertyid="<?= $prop_id ?>" data-status="closed" class="status_change"><i class="fa fa-ban" aria-hidden="true"></i> Closed</a></li>
											 <li><a data-propertyid="<?= $prop_id ?>" data-status="inactive" class="status_change"><i class="fa fa-minus-circle" aria-hidden="true"></i> Inactive</a></li>
											 <li><a data-propertyid="<?= $prop_id ?>" data-status="" href="javascript:void(0);" onclick="deleteProperty(<?= $prop_id ?>)" class=""><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</a></li>
										 </ul>
									</div>
								 </div>
								</div>
							</div>
						</div>
					</div>
				            </div>
			            </div>
			            <!-- /. End row -->
				        <div class="clear"></div>
			        </div>
	            </div>
	            
	            
            
            <?php 
                    }
            }
            
            
        
    }
    
    
    
}


if(isset($_POST['action']) && $_POST['action'] == 'loadPropPromoteMore_Details'){
    
    if( isset($_POST['row']) && isset($_POST['limit']) ){
        
        $start = $_POST['row'];
        $Syslimit = $_POST['limit'];
        $search = $_POST['search'];
        
        // package access 
        $access_remain = $_POST['access_remain'];
        $access = $_POST['access'];
        
         if(isset($search) && !empty($search)){
            
            $sqlAddress ='p.tab1 LIKE \'%"entered_address":"'.$search."%' ";
            $sqlState = 'OR p.tab1 LIKE \'%"state":"'.$search."%' ";
            $sqlCity = 'OR p.tab1 LIKE \'%"city":"'.$search."%' ";
            $sqlCityF = 'OR p.tab1 LIKE \'%"city_r":"'.$search."%' ";
            
            $cc = $sqlAddress. $sqlState. $sqlCity. $sqlCityF;
            $qr = "AND ($cc) ORDER BY p.id desc LIMIT $start,$Syslimit";
        }else{
            
            $search = "";
            $qr = "ORDER BY p.id desc LIMIT $start,$Syslimit";
            
        }
        
        $prop = getAll_AvailProperty4PromoteBySearch($qr);
                                    
            if ($prop->num_rows > 0) {
            while($row = mysqli_fetch_assoc($prop)) {
                
                // Tab 1 details
				$tab1 = json_decode($row["tab1"]);
				
				$title = $tab1->listing_title;
				$address = $tab1->entered_address;
				$beds = $tab1->beds;
				$baths = $tab1->baths;
				$price = number_format($tab1->flip_price);
				$constructions_year = $tab1->constructions_year;
				$arv_price = $tab1->flip_arv;
				$flip_ext_repair = $tab1->flip_ext_repair;
				$rental_price = $tab1->rental_price;
				$rental_arv = $tab1->rental_arv;
				$rental_ext_rent = $tab1->rental_ext_rent;
				$property_size = $tab1->property_size;
				
				$promote_inst = $tab1->promotion_note;
            	$gift_price = $tab1->gift_price;
            	
            	$allow_promotion = $tab1->allow_promotion;
				
	            //  Tab 4 Details	
	            $tab4 = json_decode($row["tab4"]);
	            
	            // Tab 6 details
	            $tab6 = unserialize($row["tab6"]);
	            $server = $wo['config']['site_url'].'/themes/wondertag/uploads_images/'.$tab6[0];
	            
	            
	            // General Values
	            $prop_id = $row['id'];
	            $single_page_url = ($allow_promotion == 1) ? $wo['config']['site_url']."/promote-listing/".$prop_id : '#' ;
	            
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
            
    	?>
    	
         <?php if(Wo_disable_promote_button($prop_id) == 0){ ?>
         
        <div class="col-md-12 listing-item item-id- item_number_simple_">
        
                		
    	    <div class="listing-item-wrap this_is_second">
    	      <?php if($statustag!="") echo '<span class="featured-tag">'.$statustag.'</span>'; ?>
    	        <div class="listing-item-boxCard">
            
                    <!--Main Container start here-->
                    <div class="prop-list-card">
                        <div class="row">
                          <div class="col-sm-4">
                            <a href="<?= $single_page_url ?>">
                              <div class="listing-wrap">
                                <?php if ( empty($tab6) ){ ?>
                                  <div class="listing-fig-pro">
                                    <img src="/upload/photos/d-property.jpg" />
                                  </div>
                                    <?php } else { ?>
                                  <div class="listing-fig-pro">
                                    <img src="<?= $server ?>" alt="Listing 1"/> 
                                  </div>
                                <?php }?>
                              </div>
                            </a>
                          </div>
                          <div class="col-sm-8">
                
                            <a class="adrd_link d-block" href="<?= $single_page_url ?>">
                                <div class="prop-address address">
                                  <h5>
                                    <?= $address ?>
                                  </h5>
                                </div>
                            </a>
                    
                    	    <div class="prop-details">
                              <div class="row">
                                <div class="col-sm-6 spec-col-1">
                                  <div class="prop-list-price  spec-col">
                                    <div class="col-xs-4 text-left no-padding">Price
                                    </div>
                                    <div class="col-xs-8 text-right no-padding">
                                       <strong>$<?= $price ?> </strong>
                                    </div>
                                    <div class="clearfix"></div>
                                  </div>
                                  <div class="prop-arv  spec-col">
                                      <div class="col-xs-4 text-left no-padding">ARV
                                      </div>
                                      <div class="col-xs-8 text-right no-padding">
                                       <strong>$<?= $flip_arv ?></strong>
                                      </div>
                                      <div class="clearfix"></div>
                                  </div>
                                  <!-- ./ End prop-arv -->
                                </div>  
                                <div class="col-sm-6 spec-col-1">
                                    <div class="prop-year  spec-col">
                                      <div class="col-xs-4 text-left no-padding">Year Built
                                      </div>
                                      <div class="col-xs-8 text-right no-padding">
                                        <strong><?= $constructions_year ?></strong>
                                      </div>
                                      <div class="clearfix"></div>
                                    </div>
                                    <div class="prop-hoa  spec-col">
                                      <div class="col-xs-4 text-left no-padding">HOA
                                      </div>
                                      <div class="col-xs-8 text-right no-padding">
                                        <strong><?php //echo $tab4->association_fee; ?>/
                                        <?php //echo $tab4->association_fee_due; ?></strong>
                                      </div>
                                      <div class="clearfix">
                                      </div>
                                    </div>
                                </div>
                              </div>
                                <!-- ./ End row -->
                            </div>
                            <style>
                                .promoted{
                                    background-color: #F37934;
                                }
                            </style>
                                    <!-- ./ End prop-details -->
                        
                            <div class="fav-cta no-padding">
                                <div class="row">
                                    <div class="col-md-12 fav-cta">
                                     
                                         <?php if(Wo_disable_promote_button($prop_id) == 0){ ?>
                                         <div class="prop-action">
                                             
                                            <?php 
                                                if( isset($allow_promotion) && $allow_promotion == 1 ){
                                                if( $access_remain > 0 || $access == 1 ){ 
                                                
                                            ?>
                                             <a href="<?= $single_page_url ?>" class="btn btn-view-prom btn-strastic-orange">Promote</a>
                                            <?php } } ?>
                                            
                                            <?php if($gift_price != ""){ ?>
                                                <p class="card-price"><?= "Promoted Fee: $".number_format($gift_price) ?></p>
                                            <?php } ?>
                                                                    
                                                                    
                                         </div>
                                        <?php }else{ ?>
                                         <div class="prop-action">
                                            <a class="btn btn-view-prom promoted btn-strastic-orange">Promoted</a>
                                         </div>
                                        <?php } ?>
                                        <?php if($promote_inst != ""){ ?>
                                                                 
                                            <div class="prop-action">
                                                <span style="color: red;" class="tool btn" data-tip="<?= $promote_inst ?>" tabindex="1">Hover Here to see Seller Instruction  </span>
                                            </div>
                                            
                                        <?php } ?> 
                                         
                                    </div>
                                  <div class="clear"></div>
                                </div>
                            </div>
                              <div class="clear"></div>
                            </div>
                        
                            
                        </div>
                        </div>
                        
                </div>
                
                
                    
    	    </div>


        </div>
        
        <?php }  }
          
        }  
            
        
    }
    
    
    
}


if(isset($_POST['action']) && $_POST['action'] == 'loadMyPropPromoteMore_Details'){
    
    if( isset($_POST['row']) && isset($_POST['limit']) ){
        
        $start = $_POST['row'];
        $Syslimit = $_POST['limit'];
        $search = $_POST['search'];
        
         if(isset($search) && !empty($search)){
            
            $sqlAddress ='p.tab1 LIKE \'%"entered_address":"'.$search."%' ";
            $sqlState = 'OR p.tab1 LIKE \'%"state":"'.$search."%' ";
            $sqlCity = 'OR p.tab1 LIKE \'%"city":"'.$search."%' ";
            $sqlCityF = 'OR p.tab1 LIKE \'%"city_r":"'.$search."%' ";
            
            $cc = $sqlAddress. $sqlState. $sqlCity. $sqlCityF;
            $qr = "AND ($cc) ORDER BY p.id desc LIMIT $start,$Syslimit";
        }else{
            
            $search = "";
            $qr = "ORDER BY p.id desc LIMIT $start,$Syslimit";
            
        }
        
        $prop = getAll_MyAvailProperty4PromoteBySearch($user_id,$qr);
                                    
            if ($prop->num_rows > 0) {
            while($row = mysqli_fetch_assoc($prop)) {
                
                // Tab 1 details
    			$tab1 = json_decode($row["tab1"]);
    			
    			$title = $tab1->listing_title;
    			$address = $tab1->entered_address;
    			$beds = $tab1->beds;
    			$baths = $tab1->baths;
    			$price = number_format($tab1->flip_price);
				$constructions_year = $tab1->constructions_year;
				$arv_price = number_format($tab1->flip_arv);
				$flip_ext_repair = number_format($tab1->flip_ext_repair);
				$rental_price = number_format($tab1->rental_price);
				$rental_arv = number_format($tab1->rental_arv);
				$rental_ext_rent = number_format($tab1->rental_ext_rent);
    			$property_size = $tab1->property_size;
    			
    			$promote_inst = $tab1->promotion_note;
    			$gift_price = $tab1->gift_price;
    			
    			
                //Promoted Table details
                $promote_price = number_format($row['price']);
    			
                //  Tab 4 Details	
                $tab4 = json_decode($row["tab4"]);
                
                // Tab 6 details
                $tab6 = unserialize($row["tab6"]);
                $server = $wo['config']['site_url'].'/themes/wondertag/uploads_images/'.$tab6[0];
                
                
                // General Values
                $prop_id = $row['id'];
                $promote_id = $row['ID'];
                $single_page_url = $wo['config']['site_url']."/promote-listing/".$prop_id;
                
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
            
    	?>
    	
    	<div id="promote_id_<?= $promote_id ?>" class="col-md-12 listing-item item-id- item_number_simple_">
                                
                                        		
	        <div class="listing-item-wrap this_is_second">
	      <?php if($statustag!="") echo '<span class="featured-tag">'.$statustag.'</span>'; ?>
	        <div class="listing-item-boxCard">
        
                <!--Main Container start here-->
                <div class="prop-list-card">
                    <div class="row">
                      <div class="col-sm-4">
                        <a href="<?= $single_page_url ?>">
                          <div class="listing-wrap">
                            <?php if ( empty($tab6) ){ ?>
                              <div class="listing-fig-pro">
                                <img src="/upload/photos/d-property.jpg" />
                              </div>
                                <?php } else { ?>
                              <div class="listing-fig-pro">
                                <img src="<?= $server ?>" alt="Listing 1"/> 
                              </div>
                            <?php }?>
                          </div>
                        </a>
                      </div>
                      
                      <div class="col-sm-8">
                        <a class="adrd_link d-block" href="<?= $single_page_url ?>">
                          <div class="prop-address address">
                            <h5>
                              <?= $address ?>
                            </h5>
                          </div>
                          
                          <?php
                          
                          
                          ?>
                    	  
                    	   <div class="col-md-12 other_info">
                    	   <span class="att_label">ARV</span><span class="att_value">$<?= $arv_price ?></span>
                    	   <span class="att_label">Equity</span><span class="att_value">$<?php //echo number_format($equilityprice);?></span>
                    	   </div>
                        </a>
                    	  <div class="prop-details">
                          <div class="row">
                            <div class="col-sm-6 spec-col-1">
                                <div class="prop-list-price  spec-col">
                                    <div class="col-xs-4 text-left no-padding">Price
                                    </div>
                                    <div class="col-xs-8 text-right no-padding">
                                       <strong>$<?= $promote_price  ?> </strong> <span style="font-size:8.5pt">(Orig Price $<?= $price ?>)</span>
                                    </div>
                                    <div class="clearfix"></div>
                              </div>
                              <div class="prop-arv  spec-col">
                                <div class="col-xs-4 text-left no-padding">ARV
                                </div>
                                <div class="col-xs-8 text-right no-padding">
                                 <strong>$<?= $arv_price ?></strong>
                                </div>
                                <div class="clearfix"></div>
                              </div>
                            </div>	
                            <div class="col-sm-6 spec-col-1">	  
                              <div class="prop-year  spec-col">
                                <div class="col-xs-4 text-left no-padding">Year Built
                                </div>
                                <div class="col-xs-8 text-right no-padding">
                                  <strong><?= $constructions_year ?></strong>
                                </div>
                                <div class="clearfix">
                                </div>
                              </div>
                              <div class="prop-hoa  spec-col">
                                <div class="col-xs-4 text-left no-padding">HOA
                                </div>
                                <div class="col-xs-8 text-right no-padding">
                                  <strong><?php //echo $tab4->association_fee; ?>/
                                  <?php //echo $tab4->association_fee_due; ?></strong>
                                </div>
                                <div class="clearfix">
                                </div>
                              </div>
                            </div>
                            <div class="col-md-6 spec-col spec-col-2" style="display:none">
                              <div>
                                <div class="col-xs-4 text-left no-padding">On Strastic
                                </div>
                                <div class="col-xs-8 text-right no-padding">
                                  <?= $time ?>
                                </div>
                                <div class="clearfix">
                                </div>
                              </div>
                              <div class="prop-status">
                                <div class="col-xs-4 text-left no-padding">Status
                                </div>
                                <div class="col-xs-8 text-right no-padding"><strong class="status-pos">Active</strong>
                                </div>
                                <div class="clearfix">
                                </div>
                              </div>
                            </div>
                          </div>
                          <!-- ./ End prop-details -->
                        </div>
                        <div class="fav-cta no-padding">
                            <div class="row">
                          
                                <div class="col-md-12 cta">
                                    <div class="prop-action">
                                       <a href="?eid=<?php //echo $row["ID"];?>&id=<?php echo $row["id"];?>" class="btn btn-view-edit btn-strastic-orange">Edit</a>
                                       <a onClick="deleteList('<?php //echo $row["ID"];?>')" href="javascript:void(0)" class="btn btn-view-delete btn-strastic-orange">Delete</a>
                                   </div>
                                </div>
                            </div>
                        <!-- ./ End row -->
                        </div>
                      <div class="clear">
                      </div>
                    </div>
                    
                        
                    </div>
                    </div>
                    
                    
            </div>
        
            
            
	    </div>
        

        </div>
                            	
    	
    	
         
        <?php  }
          
        }  
            
        
    }
    
    
    
}
?>