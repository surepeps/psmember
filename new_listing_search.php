<?php
global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php'); 


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

$min_amount = $_POST['min-price'];
$max_amount = $_POST['max-price'];
$deal_type = $_POST['type'];
$propt_type = $_POST['status'];
$beds = $_POST['bedrooms'];
$baths = $_POST['bathrooms'];
$sort_order = $_POST['sortby'];
$min_area = $_POST['min-area'];
$max_area = $_POST['max-area'];
$address = $_POST['search_location'];
// Pagination code goes here
$pageIn = $_POST['paged'];


$query = "SELECT * FROM `Wo_Filter` B left join `Wo_Listing` A on B.property_id =A.id WHERE A.status = 1";

$query = "
    SELECT * FROM `Wo_Filter` B 
    left join `Wo_Listing` A on B.property_id =A.id 
    left join `Wo_Users` U on U.user_id = A.user_id 
    WHERE A.status = 1  AND A.tab1 NOT LIKE '%\"is_push\":\"1\"%'
";

// Other Search Queries

if(!empty($min_amount) && !empty($max_amount)){
	$query.=" AND B.price_range BETWEEN $min_amount AND $max_amount";
}

if($deal_type != ''){
    $query.=" AND B.deal_type LIKE '%$deal_type%'";
}

if(!empty($address)){
	$query.=" And B.address like '%$address%'";
}

if($propt_type != ''){
    $query.=" AND B.property_type = '$propt_type'";
}

if($beds != ''){
	$query.=" AND B.bedroom = $beds";
}
if($baths != ''){
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

if($sort_order == "d_date"){
  	 $query.=" ORDER BY A.dtae_time DESC ";
}

if($sort_order == "a_date"){
  	 $query.=" ORDER BY A.dtae_time ASC ";
}

if($sort_order == "d_price"){
  	 $query.=" ORDER BY B.price_range DESC ";
}	

if($sort_order == "a_price"){
  	 $query.=" ORDER BY B.price_range ASC ";
}		

$querypagination = $query;

if($pageIn > 0){
    $start_from = ($pageIn - 1)*$perpage;
}else{
    $start_from = 0;
}
$query.= " LIMIT ".$start_from.", ".$perpage;
// echo $query;
// exit;

// Get numbers of listing in the pagination page
$queryrespagiantion = mysqli_query($sqlConnect,$querypagination);

$numrows = mysqli_num_rows($queryrespagiantion);

$totalPages = ceil($numrows / $perpage);

$query = mysqli_query($sqlConnect,$query); 

$k=0; 

if($numrows > 0){
    while($fetched_data = mysqli_fetch_assoc($query)){
                                
        $data[] = $fetched_data;

    }
    
    foreach($data as $row){
        
        // Get all columns out with while loop
        $tab1 = json_decode($row["tab1"]);
		$tab4 = json_decode($row["tab4"]);
		$tab5 = json_decode($row["tab5"]);
		$tab6 = unserialize($row["tab6"]);
		$tab2 = json_decode($row["tab2"]);
		$tab8 = json_decode($row["tab8"]);
		
        // Get Construction year 		
		$prop_year_built = $tab1->constructions_year;
		$title = $tab1->listing_title;
        $price = $tab1->flip_price;
        $beds = $tab1->beds;
        $baths = $tab1->baths;
        $size = $tab1->property_size;
        $prop_type = $tab1->prop_type;
        $prop_arv = $tab1->flip_arv;
                            
                            
        $prop_equitiy = number_format($prop_arv - $price);
        $equility = 0;
        if($prop_equitiy == 0 || $prop_equitiy < 0){
            $equility = 0;
        }else{
            $equility = $prop_equitiy;
        }
        
        // SQFT PRICE
        $sqft_p =  $price/$size;
        
        //Lat and Long
        $lat = $row['lat'];
        $lang = $row['lang'];
        
        // Property Id
        $prop_id = $row['id'];
        
        // URL
        $prop_url = $wo['config']['site_url'].'/new-property/'.$prop_id;
         
        // Prop type
        $term_id = 0;
        if($prop_type == "1/2 Duplex"){
            $term_id = 1;
        }elseif($prop_type == "Apartment"){
            $term_id = 2;
        }elseif($prop_type == "Condo"){
            $term_id = 3;
        }elseif($prop_type == "Duplex"){
            $term_id = 4;
        }elseif($prop_type == "Land"){
            $term_id = 5;
        }elseif($prop_type == "Mobile Homes"){
            $term_id = 6;
        }elseif($prop_type == "Multi Family Home"){
            $term_id = 7;
        }elseif($prop_type == "Single Family Home"){
            $term_id = 8;
        }elseif($prop_type == "Townhouse"){
            $term_id = 9;
        }
        
        // Author details fetch
        $author_d = Wo_UserData($row['user_id']);
        $autho_name = $author_d['name'];
		
        //Image
        $server = $wo['config']['site_url'].'/themes/wondertag/uploads_images/'.$tab6[0];
        $default_img = "/upload/photos/d-property.jpg";
        
        // Time/Duration
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
		$favourite = Wo_get_favourite($row["id"]);
		$property_status = $row["status"];
		
        // Get property status 		
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
		
        
        updateListingFilter_New($row);
        
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
            "property_type" => $prop_type,
            "lat" => $lat,
            "lng" => $lang,
            "term_id" => 12,
            "marker" => "https://demo01.houzez.co/wp-content/themes/houzez/img/map/pin-single-family.png",
            "retinaMarker" => "https://demo01.houzez.co/wp-content/themes/houzez/img/map/pin-single-family.png",
            "thumbnail" => !empty($tab6) ? $server : $default_img
        );
        
        $data_v[] = $array;
        
        $mysearchedlist = '<div class="item-listing-wrap hz-item-gallery-js card item-id-'.$row["id"].' '.$row["user_id"].' item_number_simple_'. $l .'" >
            <div class="item-wrap item-wrap-v1 item-wrap-no-frame h-100">
                <div class="d-flex align-items-center h-100">
                    <div class="item-header">';
                         if($tab1->deal_type != '') {
        $mysearchedlist .=  '<span class="label-featured label">'. $tab1->deal_type.'</span>';
                         }
        $mysearchedlist .= '<div class="labels-wrap labels-right">';
                            
                                //check if listing is new
                    			$to_date = time(); // Input your date here e.g. strtotime("2014-01-02")
                    			$posted_date = date("Y-m-d",strtotime($row["dtae_time"]));
                    			$from_date = strtotime($posted_date);
                    			$day_diff = $to_date - $from_date;
                    			$dateDifff = floor($day_diff/(60*60*24))."\n";
                    			
                    			
                    			if(count($tab8->tags)>0 OR $dateDifff<3){
                    		
                		     if($dateDifff<3){
        $mysearchedlist .=  '<span class="label-status label status-color-18">New</span>';
                             }
                    		 foreach($tab8->tags as $tag_name){
                    		     if($tag_name=="Active"){
                    		         $tag_color = "";
                    		     }elseif($tag_name == "Pending"){
                    		         $tag_color = "#20be26;";
                    		     }elseif($tag_name == "Under Contract"){
                    		         $tag_color = "#12a9e5;";
                    		     }elseif($tag_name == "Sold"){
                    		         $tag_color = "#f2c829;";
                    		     }elseif($tag_name == "Back On Market"){
                    		         $tag_color = "#f45414;";
                    		     }elseif($tag_name=="Price Cut"){
                    		         $tag_color = "#936fff;";
                    		     }
        $mysearchedlist .=  '<span class="label-status label" style="background:'.$tag_color.'">' .$tag_name .'</span>';
                    		 
                    		 } 
                    			    
                    			}
                            
        $mysearchedlist .= '</div>
                        <ul class="item-price-wrap hide-on-list">
                            <li class="item-price">$'. number_format($price, 2). '</li>
                            <li class="item-sub-price">$'. number_format($sqft_p, 2) .'/sq ft</li>
                        </ul>
                        <ul class="item-tools">
        
                            <li class="item-tool item-preview">
                                <span class="hz-show-lightbox-js" data-listid="2803" data-toggle="tooltip" data-placement="top" title="Preview">
                                        <i class="houzez-icon icon-expand-3"></i>   
                                </span><!-- item-tool-favorite -->
                            </li><!-- item-tool -->
        
                        </ul>
                        <a href="'.$prop_url.'" class="hover-effect">';
                        if(!empty($tab6)){
        $mysearchedlist .= '<img style="width: 500px; height: 200px;" src="'.$server.'" data-src="'.$server.'" class=" img-fluid wp-post-image" alt="" loading="lazy" srcset="" data-srcset="'. $server. ' 592w, '. $server .' 300w, '. $server .' 1024w, '. $server .' 768w, '. $server .' 584w, '. $server .' 800w, '. $server .' 120w, '. $server .' 496w, '. $server .' 1170w" sizes="(max-width: 592px) 100vw, 592px" />';
                        }else{
        $mysearchedlist .= '<img style="width: 500px; height: 200px;" src="'.$default_img.'" data-src="'.$default_img.'" class=" img-fluid wp-post-image" alt="" loading="lazy" srcset="" data-srcset="'. $default_img. ' 592w, '. $default_img .' 300w, '. $default_img .' 1024w, '. $default_img .' 768w, '. $default_img .' 584w, '. $default_img .' 800w, '. $default_img .' 120w, '. $default_img .' 496w, '. $default_img .' 1170w" sizes="(max-width: 592px) 100vw, 592px" />';
                        }
        $mysearchedlist .= '</a>
                        <div class="preview_loader"></div>
                    </div>
                    
                    <div class="item-body flex-grow-1">
                        <div class="labels-wrap labels-right">';
                            
                            
                                //check if listing is new
                    			$to_date = time(); // Input your date here e.g. strtotime("2014-01-02")
                    			$posted_date = date("Y-m-d",strtotime($row["dtae_time"]));
                    			$from_date = strtotime($posted_date);
                    			$day_diff = $to_date - $from_date;
                    			$dateDifff = floor($day_diff/(60*60*24))."\n";
                    			
                    			
                    			if(count($tab8->tags)>0 OR $dateDifff<3){
                    	
                		          if($dateDifff<3){
        $mysearchedlist .= '        <span class="label-status label status-color-18">New</span>';
                                  }
                    		 foreach($tab8->tags as $tag_name){
                    		     if($tag_name=="Active"){
                    		         $tag_color = "";
                    		     }elseif($tag_name == "Pending"){
                    		         $tag_color = "#20be26;";
                    		     }elseif($tag_name == "Under Contract"){
                    		         $tag_color = "#12a9e5;";
                    		     }elseif($tag_name == "Sold"){
                    		         $tag_color = "#f2c829;";
                    		     }elseif($tag_name == "Back On Market"){
                    		         $tag_color = "#f45414;";
                    		     }elseif($tag_name=="Price Cut"){
                    		         $tag_color = "#936fff;";
                    		     }
                    		     
        $mysearchedlist .= '	    <span class="label-status label" style="background:'.$tag_color.'">'.$tag_name.'</span>';
                    		 } 
                    			    
                    			}
                    			
                    			
        $promoteBtn = $promotePrice = null;
        if ($tab1->allow_promotion == 1) {
            $promotePrice = '<span>Promote Price: $' . number_format($tab1->gift_price) . '</span>';
            if (in_array($row['id'], $alreadyPromoted)) {
                $promoteBtn = '<a class="btn btn-primary btn-item promote-btn" href="javascript:void(0)">Already Promoted</a><!-- btn-item -->';
            } else {
                $promoteBtn = '<a class="btn btn-primary btn-item promote-btn" onclick="updatePromoteId(' . $prop_id . ')" href="javascript:void(0)" data-target="#promotePopup" data-toggle="modal">Promote</a><!-- btn-item -->';
            }
        }
        $mysearchedlist .= '</div>
                        <h2 class="item-title">
                            <a href="'.$prop_url.'">'. $title .'</a>
                        </h2>';
                            if($prop_arv != "" || $prop_arv > 0){ 
                               $arv_n = number_format($prop_arv); 
                            }else{ 
                               $arv_n = "0";
                            }
        $mysearchedlist .= '<div>
                            <span>ARV: $'.$arv_n.'</span>
                        </div>
                        <div>
                            <span>Equity: $'. $equility.'</span>
                        </div>
                        <div>
                            ' . $promotePrice . '
                        </div>
                        <ul class="item-price-wrap hide-on-list">
                            <li class="item-price"> $'. number_format($price, 2) .'</li>
                            <li class="item-sub-price">$'. number_format($sqft_p, 2) .'/sq ft</li>
                        </ul>
                        <ul class="item-amenities item-amenities-with-icons">
                            <li class="h-beds"><i class="houzez-icon icon-hotel-double-bed-1 mr-1"></i>
                                <span class="item-amenities-text">Beds:</span> 
                                <span class="hz-figure">'. $beds .'</span>
                            </li>
                            <li class="h-baths">
                                <i class="houzez-icon icon-bathroom-shower-1 mr-1"></i>
                                <span class="item-amenities-text">Baths:</span>
                                <span class="hz-figure">'. $baths .'</span>
                            </li>
                            <li class="h-area">
                                <i class="houzez-icon icon-ruler-triangle mr-1"></i>
                                <span class="hz-figure">'. number_format($size) .'</span> 
                                <span class="area_postfix">Sq Ft</span>
                            </li>
                        </ul>
                        <a class="btn btn-primary btn-item " href="'.$prop_url.'">Details</a>
                        ' . $promoteBtn . '
                        <div class="item-author">
                        	<i class="houzez-icon icon-single-neutral mr-1"></i>
                        	<a href="">'. $autho_name .'</a>
                        </div>
                        <div class="item-date">
                            <i class="houzez-icon icon-attachment mr-1"></i>
                                '. $time .'
                        </div>
                    </div>
        
        	        <div class="item-footer clearfix">
                        <div class="item-author">
                            <i class="houzez-icon icon-single-neutral mr-1"></i>
                            <a href="">'. $autho_name .'</a>
                        </div>
                        <div class="item-date">
                            <i class="houzez-icon icon-attachment mr-1"></i>
                                '. $time .'
                        </div>
                    </div>
               </div>
            </div>
        
        </div>';
        
        $result[] = $mysearchedlist;
         
    }
       
                        
        $pagination = '<div class="pagination-wrap houzez_ajax_pagination">
                            <nav>
                                <ul class="pagination justify-content-center">';
                                $PrevPg = $pageIn - 1;
                                if($pageIn <= 1){ $disable = "disabled";}else{ $disable = "";} 
        $pagination .= '            <li class="page-item AAP '.$disable.'">
                                        <a class="page-link left_arrow js-page" data-houzepagi="'.$PrevPg.'" aria-label="Previous" data-page="prev" href="javascript:;">
                                            <i class="houzez-icon icon-arrow-left-1"></i>
                                        </a>
                                    </li>';
                                    
                                    if($totalPages > 1){
                                        for ($i=1; $i <= $totalPages; $i++) {
                                        $active = "";
                                        if($i==$pageIn) {
                                            $active = "active";	
                                        }
                                        if($i == 1 && $pageIn == 0){
                                            $active = "active";
                                            $pageIn = $pageIn + 1;
                                        }
                                    
        $pagination .= '                <li class="page-item '. $active .'" id="js-page- '.$i.' ">
                                            <a class="page-link js-page" data-houzepagi="'. $i .'" href="javascript:void(0);" data-page="'. $i .'">'. $i .'<span class="sr-only"></span></a>
                                        </li>';
                                      }
                                    }else{
         $pagination .= '               <li class="page-item active" id="js-page-1">
                                            <a class="page-link" data-houzepagi="1" href="javascript:void(0);" data-page="1">1<span class="sr-only"></span></a>
                                        </li>';
                                    }
                                    $nextPg = $pageIn + 1;
                                    if($pageIn >= $totalPages){ $disable2 = "disabled";}else{ $disable2 = "";} 
                                    
        $pagination .= '             <input class="total_pages" value="'. $totalPages .'" type="hidden">
        							 <input type="hidden" id="current-page" value="'.$pageIn.'" >
                                    <li class="page-item AAN '.$disable2.'">
                                        <a class="page-link right_arrow js-page" data-houzepagi="'.$nextPg.'" data-page="next" rel="Next" href="javascript:;">
                                            <i class="houzez-icon icon-arrow-right-1"></i>
                                        </a>
                                    </li>
                                    
                                </ul>
                            </nav>
                        </div>';
        // $pagination .= '<script> var houzez_map_properties = '.json_encode($data_v).';</script>';

    
    $mymessage = "Success";
    $data = array(
        'status' => 200,
        'numberRes' => $numrows,
        'listHtml' => $result,
        'properties' => $data_v,
        'pagination' => $pagination,
        'message' => $mymessage. " About ".$numrows ." Search Found"
    );
    
    
}else{
    $mymessage = "Error";
    $numrows = 0;
    
    $data = array(
        'status' => 400,
        'numberRes' => $numrows,
        'pagination' => '',
        'properties' => '',
        'listHtml' => "<h3 style='text-align: center;'>Sorry! No listing(s) Found</h3>",
        'message' => $mymessage
    );
}

header("Content-type: application/json");
echo json_encode($data);
die();   


    


	




















