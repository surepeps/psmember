<?php

$table_data = json_decode($_REQUEST["table_data"]);
$selected_comps = explode(",",$_REQUEST["selected_comps"]);
if($_REQUEST["is_comp"]==1){$is_comp = 1; $ischecked="checked";}else{$is_comp = 0;}
foreach($table_data as $val){
	   $val = (array)$val;
	   
	   $id = $val["id"];
	   $address = $val["address"];
	   $price_amount = $val["formated_amount"];
	   $distance = $val["distance"];
	   
	   $bedrooms_arr = ((array)$val["bedrooms"]);
	   $bedrooms = $bedrooms_arr[0];
	   
	   $bathrooms_arr = ((array)$val["bathrooms"]);
	   $bathrooms = $bathrooms_arr[0];
	   
	   $finishedSqFt_arr = ((array)$val["finishedSqFt"]);
	   $finishedSqFt = $finishedSqFt_arr[0];

	   $yearBuilt_arr = ((array)$val["yearBuilt"]);
	   $yearBuilt = $yearBuilt_arr[0];
	   
	   $lastSoldDate_arr = ((array)$val["lastSoldDate"]);
	   $lastSoldDate = $lastSoldDate_arr[0];
       
	   if($is_comp==1){
		   if(in_array($id,$selected_comps)){
		   $new_data[] = array('id' => $id, 
							   'address' => $address, 
							   'bedrooms' => (int)$bedrooms, 
							   'bathrooms' => $bathrooms, 
							   'finishedSqFt' => $finishedSqFt, 
							   'yearBuilt' => $yearBuilt, 
							   'price_amount' => $price_amount, 
							   'lastSoldDate' => $lastSoldDate, 	
							   'distance' => $distance." mi");
		   }
	   }else{
		      if(!in_array($id,$selected_comps)){
				   $new_data[] = array('id' => $id, 
							   'address' => $address, 
							   'bedrooms' => (int)$bedrooms, 
							   'bathrooms' => $bathrooms, 
							   'finishedSqFt' => $finishedSqFt, 
							   'yearBuilt' => $yearBuilt, 
							   'price_amount' => $price_amount, 
							   'lastSoldDate' => $lastSoldDate, 	
							   'distance' => $distance." mi");   
		      }
	   }
} 


$columns = array_column($new_data, $_REQUEST['sort_field']);
if($_REQUEST['sort_direction']=="SORT_DESC"){array_multisort($columns, SORT_DESC, $new_data);}
else{array_multisort($columns, SORT_ASC, $new_data);}

//echo $_REQUEST['sort_field']."--".$_REQUEST['sort_direction'];
//echo "<br>";
//print_r($new_data);

echo "<hr>";
foreach($new_data as $nkey => $nval){
					//echo $nkey => $nval;
					//echo "<br>";
					$id = $nval["id"];
					$address = $nval["address"];
					$bedrooms = $nval["bedrooms"];
					$bathrooms = $nval["bathrooms"];
					$finishedSqFt = $nval["finishedSqFt"];
					$yearBuilt = $nval["yearBuilt"];
					$price_amount = $nval["price_amount"];
					$lastSoldDate = $nval["lastSoldDate"];
					$distance = $nval["distance"];

					
	   				$table_data_row.="
					  <tr>
						<td><input $ischecked type='checkbox' ".(($is_comp == 1) ? "onClick='unPickList(".$id.");'" : " onClick='pickList(".$id.");' ")." value='".$id."' id='".$id."'></td>  
						<td>".$address."</td>
						<td>".$bedrooms."</td>
						<td>".$bathrooms."</td>
						<td>".$finishedSqFt."</td>
						<td>".$yearBuilt."</td>
						<td class='price_field'>".$price_amount."</td>
						<td>".$lastSoldDate."</td>
						<td>".$distance."</td>
					  </tr>
				";  //
				
}

?>
			<table class="table gs-table">
				<thead class="gs-table-head"><tr><th><span role="button" class="gs-button">selected</span></th>
			<tr><th>&nbsp;</th>
			<th><span role="button" class="gs-button 
			<?php if($_REQUEST['sort_field']=="address"){if($_REQUEST['sort_direction']=="SORT_DESC"){$address_sd="SORT_ASC";}else{$address_sd="SORT_DESC";} echo $_REQUEST['sort_direction'];}else{$address_sd="SORT_DESC";}?>"
			onClick="sortTable('address','<?php echo $address_sd;?>',<?php echo $is_comp;?>)">Address</span></th>
			<th><span role="button" class="gs-button 
			<?php if($_REQUEST['sort_field']=="bedrooms"){if($_REQUEST['sort_direction']=="SORT_DESC"){$address_sd="SORT_ASC";}else{$address_sd="SORT_DESC";} echo $_REQUEST['sort_direction'];}else{$address_sd="SORT_DESC";}?>"
			 onClick="sortTable('bedrooms','<?php echo $address_sd;?>',<?php echo $is_comp;?>)">Beds</span></th>
			<th><span role="button" class="gs-button 
			<?php if($_REQUEST['sort_field']=="bathrooms"){if($_REQUEST['sort_direction']=="SORT_DESC"){$address_sd="SORT_ASC";}else{$address_sd="SORT_DESC";} echo $_REQUEST['sort_direction'];}else{$address_sd="SORT_DESC";}?>"
			 onClick="sortTable('bathrooms','<?php echo $address_sd;?>',<?php echo $is_comp;?>)">Baths</span></th>
			<th><span role="button" class="gs-button 
			<?php if($_REQUEST['sort_field']=="finishedSqFt"){if($_REQUEST['sort_direction']=="SORT_DESC"){$address_sd="SORT_ASC";}else{$address_sd="SORT_DESC";} echo $_REQUEST['sort_direction'];}else{$address_sd="SORT_DESC";}?>"
			 onClick="sortTable('finishedSqFt','<?php echo $address_sd;?>',<?php echo $is_comp;?>)">Sqft</span></th>
			<th><span role="button" class="gs-button 
			<?php if($_REQUEST['sort_field']=="yearBuilt"){if($_REQUEST['sort_direction']=="SORT_DESC"){$address_sd="SORT_ASC";}else{$address_sd="SORT_DESC";} echo $_REQUEST['sort_direction'];}else{$address_sd="SORT_DESC";}?>"
			 onClick="sortTable('yearBuilt','<?php echo $address_sd;?>',<?php echo $is_comp;?>)">Year</span></th>
			<th><span role="button" class="gs-button 
			<?php if($_REQUEST['sort_field']=="price_amount"){if($_REQUEST['sort_direction']=="SORT_DESC"){$address_sd="SORT_ASC";}else{$address_sd="SORT_DESC";} echo $_REQUEST['sort_direction'];}else{$address_sd="SORT_DESC";}?>"
			 onClick="sortTable('price_amount','<?php echo $address_sd;?>',<?php echo $is_comp;?>)">Price</span></th>
			<th><span role="button" class="gs-button 
			<?php if($_REQUEST['sort_field']=="lastSoldDate"){if($_REQUEST['sort_direction']=="SORT_DESC"){$address_sd="SORT_ASC";}else{$address_sd="SORT_DESC";} echo $_REQUEST['sort_direction'];}else{$address_sd="SORT_DESC";}?>"
			 onClick="sortTable('lastSoldDate','<?php echo $address_sd;?>',<?php echo $is_comp;?>)">Sold Date</span></th>
			<th><span role="button" class="gs-button 
			<?php if($_REQUEST['sort_field']=="distance"){if($_REQUEST['sort_direction']=="SORT_DESC"){$address_sd="SORT_ASC";}else{$address_sd="SORT_DESC";} echo $_REQUEST['sort_direction'];}else{$address_sd="SORT_DESC";}?>"
			 onClick="sortTable('distance','<?php echo $address_sd;?>',<?php echo $is_comp;?>)">Distance</span></th>
			</tr>	
		</thead>	
		<tbody class="gs-table-body" id="current_comps_row">
 <?php echo $table_data_row;?>
		 </tbody>
        </table>	