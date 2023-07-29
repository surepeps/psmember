<?php

global $wo, $sqlConnect;
require_once('config.php');
require_once('assets/init.php');

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);
$st_com_id = $_POST['scid'];

$user_id = $wo['user']['user_id'];


if( isset($_POST['action']) && ($_POST['action'] == "display_strastic_community") ){
    if(isset($_POST['scid'])){
        
        $selectmathced_cri = "SELECT * FROM `Wo_Listing` WHERE `id`=".$st_com_id;
        $result = mysqli_query($sqlConnect,$selectmathced_cri);
        $srows_count =  mysqli_num_rows($result);
    		
    	if($srows_count>0){ 
            
            while($row = mysqli_fetch_array($result)) {
                
               	$stab1 = json_decode($row["tab1"]);
               	$list_beds = $stab1->beds;
               	$list_baths = $stab1->baths;
               	$list_prop_price = $stab1->flip_price;
               	$city_r = $stab1->city; // before it wwas city_r
               	
                $q = "SELECT *, COUNT(user_id) AS NumOccurrences FROM `Wo_Buyerinfo` WHERE `status`='0' ";
                $q .= " AND (`beds` LIKE '%".$list_beds."%' OR `bath` LIKE '%".$list_baths."%')";
                // $q .= " AND ( $list_prop_price BETWEEN `min_price` AND `max_price` ) ";
                $q .= " AND (min_price <= $list_prop_price OR max_price >= $list_prop_price) ";
                $q .= "OR `city` LIKE '%".$city_r."%'";
                $q .= "GROUP BY user_id ORDER BY NumOccurrences DESC";


//                echo($q);
//                exit;

                $result2 = mysqli_query($sqlConnect,$q);
                $srows_count2 =  mysqli_num_rows($result2);

                if($srows_count2 > 0){

                    $deal_site = GetDeal_site_details($row['user_id']);
                    $propertyurl = $wo['config']['site_url']."/property/".$row["id"];

                    if($deal_site){
                        $propertyurl = 'https://' . strtolower($deal_site['domain']) . '.' . 'psmembers.com/property/' .$row["id"] ;
                    }
                    

                    // writing the internal mssg text here
                    $internal_msg = "Hi power matching  indicated that you have buyers that matches my properties.<br>
                    Let me tell you a little bit about it. It’s a <b>".$list_beds." bedroom.</b> <b>".$list_baths." bathrooms</b> at a great price. There is good room to make a good profit on this property. I’d love to work with you on it.<br>
                    Check it out at property link. ".$propertyurl."
                    If you think it’s something your buyers might be interested in  please respond or reach out to me. thanks";
                    // 03454070427
                    $request = '<div class="col-lg-12 messgbox"><textarea name="editor1" class="inputtext">'.$internal_msg.'</textarea><button class="btn btn-primary" style="margin-top: 20px; background-color: #f37934; border: #f37934; border-radius: 5px; color: white;" type="submit" id="cli" >Send Internal Message</button></div>';
                    $request .= '<table data-toolbar="#toolbar" data-search="true" data-show-toggle="true" data-show-fullscreen="true" data-show-columns="true" data-show-columns-toggle-all="true" data-show-export="true" data-click-to-select="true" data-detail-formatter="detailFormatter" data-minimum-count-columns="2" data-pagination="true" data-id-field="id" data-page-list="[10, 25, 50, 100, all]" data-show-footer="true" data-response-handler="responseHandler" id="example" class="table table-striped table-bordered" style="width:100%">';
                    $request .= '<thead>';
                    $request .= '    <tr>';
                    $request .= '      <th data-field="id"><i class="fas fa-sort-numeric-up"></i> Check</th>';
                    $request .= '      <th data-sortable="true" data-field="name"> Member </th>';
                    $request .= '      <th data-sortable="true" data-field="phone"> Total Matches</th>';
                    $request .= '      <th data-sortable="true" data-field="city"> Cities </th>';
                    $request .= '      <th data-sortable="true" data-field="min-price"> Bedrooms</th>';
                    $request .= '      <th data-sortable="true" data-field="max-price"> Bathrooms </th>';
                    $request .= '      <th data-sortable="true" data-field="price"> Price </th>';
                    $request .= '    </tr>';
                    $request .= '</thead>';
                    $request .= '<tbody class="myst-prop">';


                    while($row2 = mysqli_fetch_array($result2)) {
                        $nuser_id = $row2['user_id'];


                        // City query
                        $cityquery  = "SELECT id From `Wo_Buyerinfo` WHERE `user_id` =" .$nuser_id. " AND `city` LIKE '%".$city_r."%' ";
                        $cities = getTableRows($cityquery);
                        
                        $srows_citycount =  count($cities);

                        if(!$srows_citycount){
                            continue;
                        }

                        $cityIds = array_map(function($id) {
                            return $id['id'];
                        }, $cities);

                        // bed query
                        $bedquery  = "SELECT * From `Wo_Buyerinfo` WHERE `user_id` =" .$nuser_id. " AND id IN(" . implode(',', $cityIds) . ") AND `beds` LIKE '%".$list_beds."%'  ";
                        $inibedqu = mysqli_query($sqlConnect,$bedquery);
                        $srows_bedcount =  mysqli_num_rows($inibedqu);
                        
                        
                        // baths query
                        $bathquery  = "SELECT * From `Wo_Buyerinfo` WHERE `user_id` =" .$nuser_id. " AND id IN(" . implode(',', $cityIds) . ") AND `bath` LIKE '%".$list_baths."%' ";
                        $inibathqu = mysqli_query($sqlConnect,$bathquery);
                        $srows_bathcount =  mysqli_num_rows($inibathqu);
                        
                        // Price range query
                        $pricerangequery  = "SELECT COUNT(`user_id`) AS `price` From `Wo_Buyerinfo` WHERE `user_id` =" .$nuser_id. " AND id IN(" . implode(',', $cityIds) . ") AND (min_price <= $list_prop_price) AND (max_price >= $list_prop_price ) ";
                        $inipricequ          = mysqli_query($sqlConnect, $pricerangequery);
                        $fetched_data = mysqli_fetch_assoc($inipricequ);
                        $srows_pricecount = $fetched_data['price'];
                        
                        
                        // $inipricequ = mysqli_query($sqlConnect,$pricerangequery);
                        // $srows_pricecount =  mysqli_num_rows($inipricequ);
                        
                        
                        $request  .= '<tr id="promote_id_"'.$row2['id'].'>';
                        $request .= '<td><input type="checkbox" value="'.$row2['user_id'].'" name="data[]" class="st_check" id="data" /></td>';
                        $request .= '<td>'. Wo_UserNameFromId($row2['user_id']) .'</td>';
                        $request .= '<td>'. $srows_citycount .'</td>';
                        $request .= '<td>'. $srows_citycount .'</td>';
                        $request .= '<td>'.$srows_bedcount.'</td>';
                        $request .= '<td>'.$srows_bathcount.'</td>';
                        $request .= '<td>'.$srows_pricecount.'</td>';
                        $request .=  '</tr>';
                        
                        // echo $request;
                    }
                    
                    $request .= ' </tbody>';
                    $request .= '    <tfoot>';
                    $request .= '        <tr>';
                    $request .= '            <th data-field="id"><i class="fas fa-sort-numeric-up"></th>';
                    $request .= '            <th data-field="name"> Member</th>';
                    $request .= '            <th data-field="phone"> Total Matches</th>';
                    $request .= '            <th data-field="city"> Cities </th>';
                    $request .= '            <th data-field="min-price"> Bedrooms</th>';
                    $request .= '            <th data-field="max-price"> Bathrooms </th>';
                    $request .= '            <th data-field="actions"> Price </th>';
                    $request .= '        </tr>';
                    $request .= '    </tfoot>';
                    $request .=  '</table>';
                    $request .= '<script> $("#example").bootstrapTable({ locale: "en-US", exportTypes: ["xml", "csv", "txt", "excel"]}); </script>';
                    $request .= '<script>CKEDITOR.replace( "editor1" );</script>';
                    
                
                }else{
                    $request = '<tr><td colspan="7"><h5 style="text-align:center;">No property match to display</h5><td></tr>';
                }        
                                                      
            }

    	}else{
    	    $request = '<div style="text-align: center; font-size: 20px;">No property match to display</div>';
    	}
        
    }else{
        $request = '<div style="text-align: center; font-size: 20px;">No Property id set</div>';
    }
    echo $request;
	 exit;
}

if ( isset($_POST['action']) && ($_POST['action'] == "fetchByPropertyData") ){

    if(isset($_POST['entered_address'])){
        $entered_address = trim($_POST['entered_address']);
    }

    if(isset($_POST['city'])){
        $city = trim($_POST['city']);
    }else{
        $city = '';
    }

    if(isset($_POST['state'])){
        $state = trim($_POST['state']);
    }else{
        $state = '';
    }

    if(isset($_POST['country'])){
        $country = trim($_POST['country']);
    }

    if($city != "" && $state != ""){
        $city_r = $city." ".$state;
        $city_k = 1;
    }else{
        $city_r = '';
    }


    if(isset($_POST['postal_code'])){
        $postal_code = trim($_POST['postal_code']);
    }

    if(isset($_POST['bedroom'])){
        $bedroom = $_POST['bedroom'];
        $bed_k = 1;
    }

    if(isset($_POST['bathroom'])){
        $bathroom = $_POST['bathroom'];
        $bed_k = 1;
    }

    if(isset($_POST['prop_type'])){
        $prop_type = $_POST['prop_type'];
        $pt_k = 1;
    }

    if(isset($_POST['deal_type'])){
        $deal_type = $_POST['deal_type'];
        $dt_k = 1;
    }

    if(isset($_POST['price'])){
        $price = $_POST['price'];
        $p_k = 1;
    }

    if(isset($_POST['user_id'])){
        $user_id = $_POST['user_id'];
    }


    $status = 1;

    // data to save
    $dataToSave = array(
        'entered_address' => $entered_address,
        'city' => $city,
        'state' => $state,
        'country' => $country,
        'city_r' => $city_r,
        'postal_code' => $postal_code,
        'bedroom' => $bedroom,
        'bathroom' => $bathroom,
        'prop_type' => $prop_type,
        'deal_type' => $deal_type,
        'price' => $price,
        'user_id' => $user_id,
    );


    $preact = 0;
    $mus = 0;

    // query composer
    $sql = "SELECT *, COUNT(user_id) AS NumOccurrences FROM `Wo_Buyerinfo` WHERE ";


    // BEDROOMS
    if($bedroom > 0){

        if( isset($_POST['bed_matched'])){

            if( ($preact > 0 && $mus > 0) || ($preact > 0 && $mus == 0) ){
                $sql .= " AND (`beds` LIKE '%".$bedroom."%') ";
            }else{
                $sql .= " (`beds` LIKE '%".$bedroom."%') ";
            }

            $musBed = 1;
            $mus = 1;

        }else{

            if($preact > 0){
                $sql .= " OR (`beds` LIKE '%".$bedroom."%') ";
            }else{
                $sql .= " (`beds` LIKE '%".$bedroom."%') ";
            }

        }

        $preact = 1;

//        $andUser = "AND `contactinsertedby` = $user_id AND type = 2";


    }


    // BATHROOMS
    if($bathroom > 0){

        if( isset($_POST['bath_matched'])){

            if( ($preact > 0 && $mus > 0) || ($preact > 0 && $mus == 0) ){
                $sql .= " AND (`bath` LIKE '%".$bathroom."%') ";
            }else{
                $sql .= " (`bath` LIKE '%".$bathroom."%') ";
            }

            $musBath = 1;
            $mus = 1;
        }else{

            if($preact > 0){
                $sql .= " OR (`bath` LIKE '%".$bathroom."%') ";
            }else{
                $sql .= " (`bath` LIKE '%".$bathroom."%') ";
            }

        }

        $preact = 1;

//        $andUser = "AND `contactinsertedby` = $user_id AND type = 2";
    }


    // CITY
    if(!empty($city_r)){

        if(isset($_POST['city_matched'])){

            if( ($preact > 0 && $mus > 0) || ($preact > 0 && $mus == 0) ){
                $sql .= " AND (`city` LIKE '%".$city_r."%') ";
            }else{
                $sql .= " (`city` LIKE '%".$city_r."%') ";
            }

            $singleMustCity =

            $musCity = 1;
            $mus = 1;
        }else{

            if($preact > 0){
                $sql .= " OR (`city` LIKE '%".$city_r."%') ";
            }else{
                $sql .= " (`city` LIKE '%".$city_r."%') ";
            }

        }


        $preact = 1;

//        $andUser = "AND `contactinsertedby` = $user_id AND type = 2";

    }


    // PROPERTY TYPE
    if(!empty($prop_type)){

        if(isset($_POST['propType_matched'])){

            if( ($preact > 0 && $mus > 0) || ($preact > 0 && $mus == 0) ){
                $sql .= " AND (`property_type` LIKE '%".$prop_type."%') ";
            }else{
                $sql .= " (`property_type` LIKE '%".$prop_type."%') ";
            }

            $musPropType = 1;
            $mus = 1;

        }else{

            if($preact > 0){
                $sql .= " OR (`property_type` LIKE '%".$prop_type."%') ";
            }else{
                $sql .= " (`property_type` LIKE '%".$prop_type."%') ";
            }

        }


        $preact = 1;

//        $andUser = "AND `contactinsertedby` = $user_id AND type = 2";

    }


    // DEAL TYPE
    if(!empty($deal_type)){

        if(isset($_POST['dealType_matched'])){

            if( ($preact > 0 && $mus > 0) || ($preact > 0 && $mus == 0) ){
                $sql .= " AND (`buying_strategy` LIKE '%".$deal_type."%') ";
            }else{
                $sql .= " (`buying_strategy` LIKE '%".$deal_type."%') ";
            }

            $musDealType = 1;
            $mus = 1;
        }else{

            if($preact > 0){
                $sql .= " OR (`buying_strategy` LIKE '%".$deal_type."%') ";
            }else{
                $sql .= " (`buying_strategy` LIKE '%".$deal_type."%') ";
            }
        }


        $preact = 1;

//        $andUser = "AND `contactinsertedby` = $user_id AND type = 2";

    }



    // PRICE
    if(!empty($price)){

        if(isset($_POST['price_matched'])){

            if( ($preact > 0 && $mus > 0) || ($preact > 0 && $mus == 0) ){
                $sql .= " AND (min_price <= $price AND max_price >= $price)";
            }else{
                $sql .= " (min_price <= $price OR max_price >= $price)";
            }

            $musPrice = 1;
            $mus = 1;

        }else{

            if($preact > 0){
                $sql .= " OR (min_price <= $price AND max_price >= $price)";
            }else{
                $sql .= " (min_price <= $price OR max_price >= $price)";
            }

        }


        $preact = 1;

//        $andUser = "AND `contactinsertedby` = $user_id AND type = 2";

    }


    $opb = "(";
    $clb = ")";

    if($preact == 0){

//        $andUser = "`contactinsertedby` = $user_id AND type = 2";
        $opb = "";
        $clb = "";

    }


    // check if none of the input is filled
    if( $bedroom == 0 && $bathroom == 0 && empty($city_r) && empty($prop_type) && empty($deal_type) && empty($price) ){
        $sql = "";
    }


//    if( $bedroom == 0 && $bathroom == 0 && empty($city_r) && empty($prop_type) && empty($deal_type) && empty($price) ){
//
//        $sql = "";
//
//    }else{
//
//        if($musBed > 0 || $musBath > 0 || $musCity > 0 || $musPropType > 0 || $musDealType > 0 || $musPrice > 0){
//
//            $sql = "SELECT *, COUNT(user_id) AS NumOccurrences FROM `Wo_Buyerinfo` WHERE ".$opb." ".$sqlBedM." ".$sqlBathM." ".$sqlCityM." ".$sqlPTypeM." ".$sqlDTypeM." ".$sqlPriceM." ".$clb." AND `status`='0' GROUP BY user_id ORDER BY NumOccurrences DESC";
//
//        }else{
//
//            $sql = "SELECT *, COUNT(user_id) AS NumOccurrences FROM `Wo_Buyerinfo` WHERE ".$opb." ".$sqlBed." ".$sqlBath." ".$sqlCity." ".$sqlPType." ".$sqlDType." ".$sqlPrice." ".$clb. " AND `status`='0' GROUP BY user_id ORDER BY NumOccurrences DESC";
//
//        }
//
//    }

    if ($preact > 0){
        $sql .= " AND `status`='0' GROUP BY user_id ORDER BY NumOccurrences DESC";
    }else{
        $sql .= " `status`='0' GROUP BY user_id ORDER BY NumOccurrences DESC";
    }



    $result2 = mysqli_query($sqlConnect,$sql);
    $srows_count2 =  mysqli_num_rows($result2);

    if($srows_count2 > 0){

//        $deal_site = GetDeal_site_details($user_id);
//        $propertyurl = $wo['config']['site_url']."/property/".$row["id"];
//
//        if($deal_site){
//            $propertyurl = 'https://' . strtolower($deal_site['domain']) . '.' . 'psmembers.com/property/' .$row["id"] ;
//        }


        // writing the internal mssg text here
//        $internal_msg = "Hi power matching  indicated that you have buyers that matches my properties.<br>
//                    Let me tell you a little bit about it. It’s a <b>".$bedroom." bedroom.</b> <b>".$bathroom." bathrooms</b> at a great price. There is good room to make a good profit on this property. I’d love to work with you on it.<br>
//                    Check it out at property link. ".$propertyurl."
//                    If you think it’s something your buyers might be interested in  please respond or reach out to me. thanks";

        $internal_msg = "Hi power matching indicated that you have Property(s) that matches my buyer’s criteria.
I would love to link up to see if we can do a JV together. <br> My buyer is looking for a <b>(". $prop_type .")</b> Property Type, <b>(". $deal_type. ")</b> Deal Type  <b>". $bedroom ."</b> bedroom(s) <b>". $bathroom ."</b> bathroom(s) in <b>(". $city_r .")</b> area of interest. <br><br><br> Let me know if you have a property that matches his criteria. ";
        // 03454070427
        $request = '<div class="col-lg-12 messgbox"><textarea name="editor1" class="inputtext">'.$internal_msg.'</textarea><button class="btn btn-primary" style="margin-top: 20px; background-color: #f37934; border: #f37934; border-radius: 5px; color: white;" type="submit" id="cli" >Send Internal Message</button></div>';
        $request .= '<table data-toolbar="#toolbar" data-search="true" data-show-toggle="true" data-show-fullscreen="true" data-show-columns="true" data-show-columns-toggle-all="true" data-show-export="true" data-click-to-select="true" data-detail-formatter="detailFormatter" data-minimum-count-columns="2" data-pagination="true" data-id-field="id" data-page-list="[10, 25, 50, 100, all]" data-show-footer="true" data-response-handler="responseHandler" id="example" class="table table-striped table-bordered" style="width:100%">';
        $request .= '<thead>';
        $request .= '    <tr>';
        $request .= '      <th data-field="id"><i class="fas fa-sort-numeric-up"></i> Check</th>';
        $request .= '      <th data-sortable="true" data-field="name"> Member </th>';
        $request .= '      <th data-sortable="true" data-field="phone"> Total Matches</th>';
        $request .= '      <th data-sortable="true" data-field="city"> Cities </th>';
        $request .= '      <th data-sortable="true" data-field="min-price"> Bedrooms</th>';
        $request .= '      <th data-sortable="true" data-field="max-price"> Bathrooms </th>';
        $request .= '      <th data-sortable="true" data-field="price"> Price </th>';
        $request .= '    </tr>';
        $request .= '</thead>';
        $request .= '<tbody class="myst-prop">';

        while($row2 = mysqli_fetch_array($result2)) {
            $nuser_id = $row2['user_id'];


            // City query
            $cityquery  = "SELECT id From `Wo_Buyerinfo` WHERE `user_id` =" .$nuser_id. " AND `city` LIKE '%".$city_r."%' ";
            $cities = getTableRows($cityquery);

            $srows_citycount =  count($cities);

            if(!$srows_citycount){
                continue;
            }

            $cityIds = array_map(function($id) {
                return $id['id'];
            }, $cities);

            // bed query
            $bedquery  = "SELECT * From `Wo_Buyerinfo` WHERE `user_id` =" .$nuser_id. " AND id IN(" . implode(',', $cityIds) . ") AND `beds` LIKE '%".$bedroom."%'  ";
            $inibedqu = mysqli_query($sqlConnect,$bedquery);
            $srows_bedcount =  mysqli_num_rows($inibedqu);


            // baths query
            $bathquery  = "SELECT * From `Wo_Buyerinfo` WHERE `user_id` =" .$nuser_id. " AND id IN(" . implode(',', $cityIds) . ") AND `bath` LIKE '%".$bathroom."%' ";
            $inibathqu = mysqli_query($sqlConnect,$bathquery);
            $srows_bathcount =  mysqli_num_rows($inibathqu);


            // Price range query
            $pricerangequery  = "SELECT COUNT(`user_id`) AS `price` From `Wo_Buyerinfo` WHERE `user_id` =" .$nuser_id. " AND id IN(" . implode(',', $cityIds) . ") AND (min_price <= $price) AND (max_price >= $price ) ";
            $inipricequ          = mysqli_query($sqlConnect, $pricerangequery);
            $fetched_data = mysqli_fetch_assoc($inipricequ);
            $srows_pricecount = $fetched_data['price'];


            // $inipricequ = mysqli_query($sqlConnect,$pricerangequery);
            // $srows_pricecount =  mysqli_num_rows($inipricequ);


            $request  .= '<tr id="promote_id_"'.$row2['id'].'>';
            $request .= '<td><input type="checkbox" value="'.$row2['user_id'].'" name="data[]" class="st_check cts_checkbox" id="data" /></td>';
            $request .= '<td>'. Wo_UserNameFromId($row2['user_id']) .'</td>';
            $request .= '<td>'. $srows_citycount .'</td>';
            $request .= '<td>'. $srows_citycount .'</td>';
            $request .= '<td>'.$srows_bedcount.'</td>';
            $request .= '<td>'.$srows_bathcount.'</td>';
            $request .= '<td>'.$srows_pricecount.'</td>';
            $request .=  '</tr>';

            // echo $request;
        }

        $request .= ' </tbody>';
        $request .= '    <tfoot>';
        $request .= '        <tr>';
        $request .= '            <th data-field="id"><i class="fas fa-sort-numeric-up"></th>';
        $request .= '            <th data-field="name"> Member</th>';
        $request .= '            <th data-field="phone"> Total Matches</th>';
        $request .= '            <th data-field="city"> Cities </th>';
        $request .= '            <th data-field="min-price"> Bedrooms</th>';
        $request .= '            <th data-field="max-price"> Bathrooms </th>';
        $request .= '            <th data-field="actions"> Price </th>';
        $request .= '        </tr>';
        $request .= '    </tfoot>';
        $request .=  '</table>';
        $request .= '<script> $("#example").bootstrapTable({ locale: "en-US", exportTypes: ["xml", "csv", "txt", "excel"]}); </script>';
        $request .= '<script>CKEDITOR.replace( "editor1" );</script>';

    }else{
        $request = '<tr><td colspan="7"><h5 style="text-align:center;">No property match to display</h5><td></tr>';
    }

    echo $request;
    exit;
}

if ( isset($_POST['action']) && ($_POST['action'] == "fetchByCriteriaData") ){

}