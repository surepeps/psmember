<?php

	/**
	 * @author Kenson Goo, founder of Sidepon.com
	 * http://www.sidepon.com
	 */

	class Zillow_Api {

		private $zws_id;
		private $zpid;

		public function __construct($zws_id = null)
		{
			if ( ! empty($zws_id)) {
				$this->zws_id = $zws_id;
			}
		}

		public function __set($property, $value)
		{
			$this->$property = $value;
		}

		public function __get($property)
		{
			if (isset($this->$property)) {
				return $this->$property;
			}
		}


		/* Home Valuation API
		 --------------------------------------------------------*/

		/**
		 * @param array $params
		 *		- allowed values in $params are address, citysatezip, and rentzestimate
		 * @retun object
		 */
		public function GetSearchResults($params)
		{
			if ( empty($this->zws_id)) {
				throw new Exception('ZWS_id is required.');
			}
			$params['zws-id'] = $this->zws_id;
			$url = 'http://www.zillow.com/webservice/GetSearchResults.htm?' . http_build_query($params);
			$result = new SimpleXMLElement($url, 0, true);

			// save this in object so that we could reuse it
			if ( isset($result->response->results->result->zpid) ) {
				$this->zpid = (string)$result->response->results->result->zpid;
			}

			return $result;
		}

		/**
		 * @param array $params
		 *		- allowed values in $params are zpid (optional), and rentzestimate
		 * @retun object
		 */
		public function GetZestimate($params = null)
		{
			if ( empty($this->zws_id)) {
				throw new Exception('ZWS_id is required.');
			}
			$params['zws-id'] = $this->zws_id;

			// if zpid is not set, we use the previous zpid value
			if ( ! isset($params['zpid'])) {
				if ( ! empty($this->zpid)) {
					$params['zpid'] = $this->zpid;
				}
				else {
					throw new Exception('zpid is required.');
				}
			}

			$url = 'http://www.zillow.com/webservice/GetZestimate.htm?' . http_build_query($params);
			$result = new SimpleXMLElement($url, 0, true);

			// save this in object so that we could reuse it
			if ( isset($result->response->zpid) ) {
				$this->zpid = (string)$result->response->zpid;
			}

			return $result;
		}

		/**
		 * @param array $params
		 *		- allowed values in $params are zpid (optional), unit-type, width, height, chartDuration
		 * @retun object
		 */
		public function GetChart($params)
		{
			if ( empty($this->zws_id)) {
				throw new Exception('ZWS_id is required.');
			}
			$params['zws-id'] = $this->zws_id;

			// if zpid is not set, we use the previous zpid value
			if ( ! isset($params['zpid'])) {
				if ( ! empty($this->zpid)) {
					$params['zpid'] = $this->zpid;
				}
				else {
					throw new Exception('zpid is required.');
				}
			}

			$url = 'http://www.zillow.com/webservice/GetChart.htm?' . http_build_query($params);
			$result = new SimpleXMLElement($url, 0, true);

			return $result;
		}

		/**
		 * @param array $params
		 *		- allowed values in $params are zpid (optional), count, and rentzestimate
		 * @retun object
		 */
		public function GetComps($params)
		{
			if ( empty($this->zws_id)) {
				throw new Exception('ZWS_id is required.');
			}
			$params['zws-id'] = $this->zws_id;

			// if zpid is not set, we use the previous zpid value
			if ( ! isset($params['zpid'])) {
				if ( ! empty($this->zpid)) {
					$params['zpid'] = $this->zpid;
				}
				else {
					throw new Exception('zpid is required.');
				}
			}

			$url = 'http://www.zillow.com/webservice/GetComps.htm?' . http_build_query($params);
			$result = new SimpleXMLElement($url, 0, true);

			// save this in object so that we could reuse it
			if ( isset($result->response->properties->principal->zpid) ) {
				$this->zpid = (string)$result->response->properties->principal->zpid;
			}

			return $result;
		}

		/* Neighborhood Data API
		 --------------------------------------------------------*/

		/**
		 * @param array $params
		 *		- allowed values in $params are regionid, state, city, neighborhood, zip
		 * @retun object
		 */
		public function GetDemographics($params = null)
		{
			if ( empty($this->zws_id)) {
				throw new Exception('ZWS_id is required.');
			}
			$params['zws-id'] = $this->zws_id;

			$url = 'http://www.zillow.com/webservice/GetDemographics.htm?' . http_build_query($params);
			$result = new SimpleXMLElement($url, 0, true);

			return $result;
		}

		/**
		 * @param array $params
		 *		- allowed values in $params are regionId, state, county, city, childtype
		 * @retun object
		 */
		public function GetRegionChildren($params)
		{
			if ( empty($this->zws_id)) {
				throw new Exception('ZWS_id is required.');
			}
			$params['zws-id'] = $this->zws_id;

			$url = 'http://www.zillow.com/webservice/GetRegionChildren.htm?' . http_build_query($params);
			$result = new SimpleXMLElement($url, 0, true);

			return $result;
		}

		/**
		 * @param array $params
		 *		- allowed values in $params are city, state, neighborhood, zip, unit-type (required), width, height, chartDuration
		 * @retun object
		 */
		public function GetRegionChart($params)
		{
			if ( empty($this->zws_id)) {
				throw new Exception('ZWS_id is required.');
			}
			$params['zws-id'] = $this->zws_id;

			$url = 'http://www.zillow.com/webservice/GetRegionChart.htm?' . http_build_query($params);
			$result = new SimpleXMLElement($url, 0, true);

			return $result;
		}
		
		/* Mortgage API
		 --------------------------------------------------------*/
		
		/**
		 * @param array $params
		 *		- allowed values in $params are state, output, and callback
		 * @retun object
		 */
		public function GetRateSummary($params = null)
		{
			if ( empty($this->zws_id)) {
				throw new Exception('ZWS_id is required.');
			}
			$params['zws-id'] = $this->zws_id;

			$url = 'http://www.zillow.com/webservice/GetRateSummary.htm?' . http_build_query($params);
			$result = new SimpleXMLElement($url, 0, true);

			return $result;
		}

		/**
		 * @param array $params
		 *		- allowed values in $params are price (required), down, dollarsdown, zip, output, and callback
		 * @retun object
		 */
		public function GetMonthlyPayments($params)
		{
			if ( empty($this->zws_id)) {
				throw new Exception('ZWS_id is required.');
			}
			$params['zws-id'] = $this->zws_id;

			$url = 'http://www.zillow.com/webservice/GetMonthlyPayments.htm?' . http_build_query($params);
			$result = new SimpleXMLElement($url, 0, true);

			return $result;
		}
		
		/* Property Details API
		 --------------------------------------------------------*/

		/**
		 * @param array $params
		 *		- allowed values in $params are addres (required), citystatezip (required), and rentzestimate
		 * @retun object
		 */
		public function GetDeepSearchResults($params)
		{
			if ( empty($this->zws_id)) {
				throw new Exception('ZWS_id is required.');
			}
			$params['zws-id'] = $this->zws_id;

			$url = 'http://www.zillow.com/webservice/GetDeepSearchResults.htm?' . http_build_query($params);
			$result = new SimpleXMLElement($url, 0, true);
			
			// save this in object so that we could reuse it
			if ( isset($result->response->results->result->zpid) ) {
				$this->zpid = (string)$result->response->results->result->zpid;
			} 

			return $result;
		}
		
		/**
		 * @param array $params
		 *		- allowed values in $params are zpid (required, could be reuse), count, and rentzestimate
		 * @retun object
		 */
		public function GetDeepComps($params = null)
		{
			if ( empty($this->zws_id)) {
				throw new Exception('ZWS_id is required.');
			}
			$params['zws-id'] = $this->zws_id;
			
			// if zpid is not set, we use the previous zpid value
			if ( ! isset($params['zpid'])) {
				if ( ! empty($this->zpid)) {
					$params['zpid'] = $this->zpid;
				}
				else {
					throw new Exception('zpid is required.');
				}
			}

			$url = 'http://www.zillow.com/webservice/GetDeepComps.htm?' . http_build_query($params);
			$result = new SimpleXMLElement($url, 0, true);
			
			// save this in object so that we could reuse it
			if ( isset($result->response->properties->principal->zpid) ) {
				$this->zpid = (string)$result->response->properties->principal->zpid;
			}

			return $result;
		}

		/**
		 * @param array $params
		 *		- allowed values in $params are zpid (required, could be reuse)
		 * @retun object
		 */
		public function GetUpdatedPropertyDetails($params = null)
		{
			if ( empty($this->zws_id)) {
				throw new Exception('ZWS_id is required.');
			}
			$params['zws-id'] = $this->zws_id;
			
			// if zpid is not set, we use the previous zpid value
			if ( ! isset($params['zpid'])) {
				if ( ! empty($this->zpid)) {
					$params['zpid'] = $this->zpid;
				}
				else {
					throw new Exception('zpid is required.');
				}
			}

			$url = 'http://www.zillow.com/webservice/GetUpdatedPropertyDetails.htm?' . http_build_query($params);
			$result = new SimpleXMLElement($url, 0, true);
			
			// save this in object so that we could reuse it
			if ( isset($result->response->properties->principal->zpid) ) {
				$this->zpid = (string)$result->response->properties->principal->zpid;
			}

			return $result;
		}

	}
	
	
	function setmapmarker($address,$propid) {
		echo '<script type="text/javascript"> codeAddress('.$address.','.$propid.');</script>';
	}
	
	$zws_id="X1-ZWz18v5lka0hzf_4srxq";

	$all_address = str_replace("%20","+",$_GET['address']);
	$all_city    = str_replace("%20","+",$_GET['citystatezip']);
	

	$zillow_api = new Zillow_Api($zws_id); 
	$search_result = (array)$zillow_api->GetDeepSearchResults(array('address' => $all_address, 'citystatezip' => $all_city));
	
	$search_result_array = (array)$search_result;
	$response = (array)$search_result_array["response"]->results->result->address;
	$bathrooms = (array)$search_result_array["response"]->results->result->bathrooms;
	$bedrooms = (array)$search_result_array["response"]->results->result->bedrooms;
	$finishedSqFt = (array)$search_result_array["response"]->results->result->finishedSqFt;
	$lotSizeSqFt = (array)$search_result_array["response"]->results->result->lotSizeSqFt;
	$yearBuilt = (array)$search_result_array["response"]->results->result->yearBuilt;
	$lastSoldDate = (array)$search_result_array["response"]->results->result->lastSoldDate;
	$lastSoldPrice = (array)$search_result_array["response"]->results->result->lastSoldPrice;
    $zetimate_amount = (array)$search_result_array["response"]->results->result->zestimate->amount;
	

	$properties_info  = "Beds: <strong>".$bedrooms[0]."</strong> Baths: <strong>".$bathrooms[0]."</strong> Sqft: <strong>".$finishedSqFt[0]."</strong>";
	$properties_info .= "Year: <strong>".$yearBuilt[0]."</strong> <br>Price: <strong>$".$lastSoldPrice[0]."</strong> Sold Date: <strong>".$lastSoldDate[0]."</strong>";
	//$property = $zillow_api->GetSearchResults(array('address' => '7356 CARTER AVE', 'citystatezip' => 'NEWARK'));

   if($search_result["message"]->code!=0){
	   echo "No record available";
	   $no_records=1;
	}else{
	
	$comps = $zillow_api->GetDeepComps(array('count' => '10', 'rentzestimate' => true));	
	
    $comps = (array)$comps;
	$total_properties = $comps["request"]->count;
	$properties_arr = $comps["response"]->properties;
	$properties_main_arr = $properties_arr->principal;
	$properties_comps_arr = (array)$properties_arr->comparables;
	$properties_comps_det_arr = $properties_comps_arr["comp"];
	$origin_ip = $response["latitude"].",".$response["longitude"];  //for the distance G map call
	
	$properties_comps_det_arr2 = $properties_comps_det_arr;
	foreach($properties_comps_det_arr2 as $key2){
				$ips .= $key2->address->latitude;
				$ips .= "%2C";
				$ips .= $key2->address->longitude;
				$ips .= "%7C";
	}
	
		$google_url_call  = "https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=".$origin_ip;
	    $google_url_call .= "&destinations=$ips&key=AIzaSyC-DrWoVislpgtWChmylA1FpmMarXjvrxs";
		//echo $google_url_call;
			$curl = curl_init();
			curl_setopt_array($curl, array(
			CURLOPT_URL => $google_url_call,
			CURLOPT_HTTPGET=> true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPGET=> true,
			CURLOPT_POST => true
			));

			$response = curl_exec($curl);
			$data_arr = (array)json_decode($response);
			$data_arr = $data_arr["rows"][0]->elements;

			$cnt_distance_loop=1;
			foreach($data_arr as $var){
					$distance_array[$cnt_distance_loop]=$var->distance->text;
					$cnt_distance_loop++;
			}

	
	foreach($properties_comps_det_arr as $key){$cnt++;
			   /* echo $cnt.") ";
				echo $key->zpid;
				echo " | Beds:";
				echo $key->bedrooms;
				echo " | Baths:";
				echo $key->bathrooms;				
				echo " | finishedSqFt:";
				echo $key->finishedSqFt;				
				echo " | yearBuilt:";
				echo $key->yearBuilt;				
				echo " | price:";
				echo $key->zestimate->amount;								
				echo " | lastSoldDate:";
				echo $key->lastSoldDate;				
				echo " | address:"; */


   			    $address  = $key->address->street.", ";//
				$address .= $key->address->city.", ";
				$address .= $key->address->state." ";
				$address .= $key->address->zipcode;	
				$latitude = $key->address->latitude;		
				$longitude = $key->address->longitude;	
				
				$comps_lng_lat[$cnt]="$longitude, $latitude";
				
				//echo "<br>";
			   $property_price = $key->zestimate->amount;
			   if($property_price==0){$property_price="N/A";}
			   //$property_price = number_format($property_price); (int)


				 $box_info_details ="
				        Beds : ".$key->bedrooms."<br>
						Baths : ".$key->bathrooms."<br>
						Sqft : ".$key->finishedSqFt."<br>
						Year : ".$key->yearBuilt."<br>
						Price : $".$key->zestimate->amount."<br>
						Sold Date : ".$key->lastSoldDate."<br>
						<a class='street_view_link' href='javascript:void(0);' onClick='toggleStreetView($latitude,$longitude);'>Street View</a>
					";
					
					
				$box_info.="
				<div class='col-md-12 listing-item item-id-".$cnt."  item_number_simple_".$cnt."'>
				 <strong>".$address."</strong><br>$box_info_details
				</div>
				";
				
				 
				
				 setmapmarker('"'.$address.'"',$cnt);  //$key->zpid
				$address_per_id .= ',"'.$address.'"';
				$formated_amount = number_format((int)$key->zestimate->amount);
				$distance = str_replace(" mi","",$distance_array[$cnt]);
				$js_data.="
					  {
						selected: '<input type=\'checkbox\' onClick=\'pickList(".$cnt.");\' value=\'".$cnt."\' id=\'".$cnt."\'>',  
						address: '".$address."',
						beds: '".$key->bedrooms."',
						baths: '".$key->bathrooms."',
						sqft: '".$key->finishedSqFt."',
						year: '".$key->yearBuilt."',
						price: '".$formated_amount."',
						solddate: '".$key->lastSoldDate."',
						distance: '".$distance."',
					 },
				";  //
				
				$table_data_row.="
					  <tr>
						<td><input type='checkbox' onClick='pickList(".$cnt.");' value='".$cnt."' id='".$cnt."'></td>  
						<td>".$address."</td>
						<td>".$key->bedrooms."</td>
						<td>".$key->bathrooms."</td>
						<td>".$key->finishedSqFt."</td>
						<td>".$key->yearBuilt."</td>
						<td>".$formated_amount."</td>
						<td>".$key->lastSoldDate."</td>
						<td>".$distance."</td>
					  </tr>
				";  //

		$table_data[]=array("id"=>$cnt, "address"=>$address,"bedrooms"=>$key->bedrooms,"bathrooms"=>$key->bathrooms,
						    "finishedSqFt"=>$key->finishedSqFt,"yearBuilt"=>$key->yearBuilt,"formated_amount"=>$formated_amount,
							"lastSoldDate"=>$key->lastSoldDate,"distance"=>$distance);
								
	}  
	setmapmarker('"'.$all_address.", ".$all_city.'"','101');  //this is the house marker call


		
   }
   
   $js_data = "var data = [".$js_data."]";
   $js_data .= "
			var columns = {
			selected: 'selected', 	
			address: 'Address',
			beds: 'Beds',
			baths: 'Baths',
			sqft: 'Sqft',
			year: 'Year',
			price: 'Price',
			solddate: 'Sold Date',
			distance: 'Distance'
			}
   
   ";
   
	//echo "<hr>";
	//echo "<pre>";
	//print_r($properties_comps_det_arr);


	//echo $property;
?>
  <?php if($no_records!=1){?>
  <script>
  
function pickList(pick){
	     var tr_row = $("#nearby_props #"+pick+":checked").closest("tr").html();
		 var current_selected_comps = $("#selected_comps").val();
		 $("#selected_comps").val($("#selected_comps").val()+","+pick);
		 tr_row2 = tr_row.replace("pickList","unPickList");
		 tr_row2 = tr_row2.replace("input","input checked ");
		 $('#current_comps table tr:last').after("<tr>"+tr_row2+"</tr>");
		 $("#nearby_props #"+pick+":checked").closest("tr").remove();
		 $("#current_comps").show();
		 $('#print_but').prop('disabled', false);
		 $('#save_pdf_but').prop('disabled', false);
		 calculate_estimate_offer(pick);
}


function unPickList(pick){
         var tr_row = $("#current_comps #"+pick+"").closest("tr").html();
		 var current_selected_comps = $("#selected_comps").val();
		 current_selected_comps_r = current_selected_comps.replace(","+pick,"");
		
		 $("#selected_comps").val(current_selected_comps_r);
		 tr_row2 = tr_row.replace("unPickList","pickList");
		 tr_row2 = tr_row2.replace("checked"," ");		 
		 $('#nearby_props table tr:last').after("<tr>"+tr_row2+"</tr>");
		 $("#current_comps #"+pick+"").closest("tr").remove();
		 calculate_estimate_offer();
	     var address_per_id_array = [""<?php echo $address_per_id;?>];
		
		 codeAddress(address_per_id_array[pick],pick); 
}

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function calculate_estimate_offer(pick){
          var comp_amount_total = 0;
		  var comp_count = 0;
		  var comp_avg_la = 0;
		  var comp_sqft_total= 0;
  		  var comp_amount = $("#current_comps td:eq(6)" ).html();   
		  var comp_amount2 = $("#current_comps td:eq(15)" ).html(); 
		  var comp_amount3 = $("#current_comps td:eq(24)" ).html();
	      var comp_amount4 = $("#current_comps td:eq(33)" ).html();	
	      var comp_amount5 = $("#current_comps td:eq(42)" ).html();	
	      var comp_amount6 = $("#current_comps td:eq(51)" ).html();			  
		  
	      if(!comp_amount){ /*do nothing*/}else{ comp_amount = comp_amount.replace(",",""); comp_amount_total = parseInt(comp_amount); comp_count = 1; 
						   codeAddress($("#current_comps td:eq(1)" ).html(), $("#current_comps td:eq(0) input[type='checkbox']" ).val(),1); 
		  }
		  if(!comp_amount2){ /*do nothing*/}else{ comp_amount2 = comp_amount2.replace(",",""); comp_amount_total = comp_amount_total+parseInt(comp_amount2); comp_count = 2; 
						   codeAddress($("#current_comps td:eq(10)" ).html(), $("#current_comps td:eq(9) input[type='checkbox']" ).val(),1); 
		  } 
		  if(!comp_amount3){ /*do nothing*/}else{ comp_amount3 = comp_amount3.replace(",",""); comp_amount_total = comp_amount_total+parseInt(comp_amount3); comp_count = 3; 
						   codeAddress($("#current_comps td:eq(19)" ).html(), $("#current_comps td:eq(18) input[type='checkbox']" ).val(),1); 
		  } 
		  if(!comp_amount4){ /*do nothing*/}else{ comp_amount4 = comp_amount4.replace(",",""); comp_amount_total = comp_amount_total+parseInt(comp_amount4); comp_count = 4; 
						   codeAddress($("#current_comps td:eq(28)" ).html(), $("#current_comps td:eq(27) input[type='checkbox']" ).val(),1); 
		  } 
		  if(!comp_amount5){ /*do nothing*/}else{ comp_amount5 = comp_amount5.replace(",",""); comp_amount_total = comp_amount_total+parseInt(comp_amount5); comp_count = 5; 
						   codeAddress($("#current_comps td:eq(37)" ).html(), $("#current_comps td:eq(36) input[type='checkbox']" ).val(),1); 
		  } 
		  if(!comp_amount6){ /*do nothing*/}else{ comp_amount6 = comp_amount6.replace(",",""); comp_amount_total = comp_amount_total+parseInt(comp_amount6); comp_count = 6; 
						   codeAddress($("#current_comps td:eq(46)" ).html(), $("#current_comps td:eq(45) input[type='checkbox']" ).val(),1); 
		  } 

		  var comp_sqft = $("#current_comps td:eq(4)" ).html();			  
		  var comp_sqft2 = $("#current_comps td:eq(13)" ).html();
		  var comp_sqft3 = $("#current_comps td:eq(22)" ).html();	
	      var comp_sqft4 = $("#current_comps td:eq(31)" ).html();	
	      var comp_sqft5 = $("#current_comps td:eq(40)" ).html();	
	      var comp_sqft6 = $("#current_comps td:eq(49)" ).html();				
		  
	      if(!comp_sqft){ /*do nothing*/}else{ comp_sqft_total = parseInt(comp_sqft);}
		  if(!comp_sqft2){ /*do nothing*/}else{ comp_sqft_total = comp_sqft_total+parseInt(comp_sqft2);} 
		  if(!comp_sqft3){ /*do nothing*/}else{ comp_sqft_total = comp_sqft_total+parseInt(comp_sqft3);} 
		  if(!comp_sqft4){ /*do nothing*/}else{ comp_sqft_total = comp_sqft_total+parseInt(comp_sqft4);} 
		  if(!comp_sqft5){ /*do nothing*/}else{ comp_sqft_total = comp_sqft_total+parseInt(comp_sqft5);} 
		  if(!comp_sqft6){ /*do nothing*/}else{ comp_sqft_total = comp_sqft_total+parseInt(comp_sqft6);} 		  
		  
		  var avg_comp_sqft = Math.round(comp_sqft_total/comp_count);
		  var estimate_price = Math.round(comp_amount_total/comp_count);
		  var estimate_price_formatted = numberWithCommas(estimate_price);
		  var avg_price_pe_sqft = Math.round(estimate_price/avg_comp_sqft);
		  var estimate_box = "<br><h4>Estimate</h4>My Estimate : $"+estimate_price_formatted+"<br>&nbsp;&nbsp;&nbsp;Avg Living Area : "+avg_comp_sqft+" sqft";
			  estimate_box = estimate_box+"<br>&nbsp;&nbsp;&nbsp;Avg Price Per Sqft : $"+avg_price_pe_sqft;
		  $("#comps_estimates_box").html(estimate_box);
		  
		  
	   var reset_offer_str = '<h4>Offer Chart</h4><div class="percent_block">30%</div><div class="percent_figure_block">$'+numberWithCommas((estimate_price*30)/100)+'</div>';
	   reset_offer_str =  reset_offer_str+'<div class="percent_block">40%</div><div class="percent_figure_block">$'+numberWithCommas(Math.round((estimate_price*40)/100))+'</div>';
	   reset_offer_str =  reset_offer_str+'<div class="percent_block">50%</div><div class="percent_figure_block">$'+numberWithCommas(Math.round((estimate_price*50)/100))+'</div>';
	   reset_offer_str =  reset_offer_str+'<div class="percent_block">60%</div><div class="percent_figure_block">$'+numberWithCommas(Math.round((estimate_price*60)/100))+'</div>';
	   reset_offer_str =  reset_offer_str+'<div class="percent_block">70%</div><div class="percent_figure_block">$'+numberWithCommas(Math.round((estimate_price*70)/100))+'</div>';
	   reset_offer_str =  reset_offer_str+'<div class="percent_block">80%</div><div class="percent_figure_block">$'+numberWithCommas(Math.round((estimate_price*80)/100))+'</div>';
	   
       $("#comps_offer_chart_box").html(reset_offer_str);	  
	   if(comp_count==0){reset_estimate_offer(); $("#current_comps").hide(); 		 
	     $('#print_but').prop('disabled', true);
		 $('#save_pdf_but').prop('disabled', true);
		}
		 //if(comp_count==1){
			sortTable('finishedSqFt','SORT_DESC',1)
		// }
}

function reset_estimate_offer(){
   var reset_estimate_str = "<br><h4>Estimate</h4>My Estimate : N/A<br>&nbsp;&nbsp;&nbsp;Avg Living Area : N/A<br>&nbsp;&nbsp;&nbsp;Avg Price Per Sqft : N/A";
   $("#comps_estimates_box").html(reset_estimate_str);
   
   var reset_offer_str = '<h4>Offer Chart</h4><div class="percent_block">30%</div><div class="percent_figure_block">N/A</div>';
	   reset_offer_str =  reset_offer_str+'<div class="percent_block">40%</div><div class="percent_figure_block">N/A</div>';
	   reset_offer_str =  reset_offer_str+'<div class="percent_block">50%</div><div class="percent_figure_block">N/A</div>';
	   reset_offer_str =  reset_offer_str+'<div class="percent_block">60%</div><div class="percent_figure_block">N/A</div>';
	   reset_offer_str =  reset_offer_str+'<div class="percent_block">70%</div><div class="percent_figure_block">N/A</div>';
	   reset_offer_str =  reset_offer_str+'<div class="percent_block">80%</div><div class="percent_figure_block">N/A</div>';
	   
   $("#comps_offer_chart_box").html(reset_offer_str);
}

  reset_estimate_offer();
  </script>

  
	 <div id="current_comps" style="display:none">
		<h4>Your Comps</h4>
		 <div id="current_comps_table_div">
		<table class="table gs-table">
		<thead class="gs-table-head"><tr>
			<th><span role="button" class="gs-button" onClick="sortTable('address','SORT_DESC',1)">Address</span></th>
			<th><span role="button" class="gs-button" onClick="sortTable('bedrooms','SORT_DESC',1)">Beds</span></th>
			<th><span role="button" class="gs-button" onClick="sortTable('bathrooms','SORT_DESC',1)">Baths</span></th>
			<th><span role="button" class="gs-button" onClick="sortTable('finishedSqFt','SORT_DESC',1)">Sqft</span></th>
			<th><span role="button" class="gs-button" onClick="sortTable('yearBuilt','SORT_DESC',1)">Year</span></th>
			<th><span role="button" class="gs-button" onClick="sortTable('price_amount','SORT_DESC',1)">Price</span></th>
			<th><span role="button" class="gs-button" onClick="sortTable('lastSoldDate','SORT_DESC',1)">Sold Date</span></th>
			<th><span role="button" class="gs-button" onClick="sortTable('distance','SORT_DESC',1)">Distance</span></th>
		</tr>
		</thead>
		<tbody class="gs-table-body" id="current_comps_row">
		 </tbody>	
        </table>		 
		</div>
    </div>
	 
<h4>Nearby Properties</h4>		
  <div id="nearby_props" class="page-container comp_table">
        <div class="container comp_table">
	
			
          <div id="root">
			<table class="table gs-table">
			<thead class="gs-table-head"><tr>
			<th><span role="button" class="gs-button">selected</span></th>
			<tr><th>&nbsp;</th>
			<th><span role="button" class="gs-button" onClick="sortTable('address','SORT_DESC')">Address</span></th>
			<th><span role="button" class="gs-button" onClick="sortTable('bedrooms','SORT_DESC')">Beds</span></th>
			<th><span role="button" class="gs-button" onClick="sortTable('bathrooms','SORT_DESC')">Baths</span></th>
			<th><span role="button" class="gs-button" onClick="sortTable('finishedSqFt','SORT_DESC')">Sqft</span></th>
			<th><span role="button" class="gs-button" onClick="sortTable('yearBuilt','SORT_DESC')">Year</span></th>
			<th><span role="button" class="gs-button" onClick="sortTable('price_amount','SORT_DESC')">Price</span></th>
			<th><span role="button" class="gs-button" onClick="sortTable('lastSoldDate','SORT_DESC')">Sold Date</span></th>
			<th><span role="button" class="gs-button" onClick="sortTable('distance','SORT_DESC')">Distance</span></th>
			</tr>	
		</thead>	
		<tbody class="gs-table-body" id="current_comps_row">
     	 <?php echo $table_data_row;?>
		 </tbody>
        </table>	
		  </div>
		   <input type="hidden" id="selected_comps">
           <input type="hidden" id="sort_field">
		   <input type="hidden" id="sort_direction">
		   <input type="hidden" id="table_data" value='<?php echo json_encode($table_data);?>'>
        </div>
    </div>

    <!-- <script src="/themes/wowonder/javascript/table-sorter/data.js?v3"></script>-->
	<script>
	$("#property_info_div").html("<?php echo $properties_info;?>"); //set main property info
	$("#property_ip").val('<?php echo $origin_ip;?>');
	$("#comps_lng_lat").val('<?php echo json_encode($comps_lng_lat);?>');
	<?php  echo str_replace("'',","'0',",$js_data);?>
	</script>
    <script src="/themes/wowonder/javascript/table-sorter/table-sortable.js?v=1.<?php echo date("gis");?>"></script>
    <script>
	sortTable('finishedSqFt','SORT_DESC',0);

       /* var table = $('#root').tableSortable({
            data,
            columns,
			sorting:true,
            searchField: '#searchField',
            responsive: {
                1100: {
                    columns: {
                        formCode: 'Form Code',
                        formName: 'Form Name',
                    },
                },
            },
            rowsPerPage: 25,
            pagination: true,
            tableWillMount: () => {
                console.log('table will mount')
            },
            tableDidMount: () => {
                console.log('table did mount')
            },
            tableWillUpdate: () => console.log('table will update'),
            tableDidUpdate: () => addClassToTds(),
            tableWillUnmount: () => console.log('table will unmount'),
            tableDidUnmount: () => console.log('table did unmount'),
            onPaginationChange: function(nextPage, setPage) {
                setPage(nextPage);
            }
        });

        $('#changeRows').on('change', function() {
            table.updateRowsPerPage(parseInt($(this).val(), 10));
        })

        $('#rerender').click(function() {
            table.refresh(true);
        })

        $('#distory').click(function() {
            table.distroy();
        })

        $('#refresh').click(function() {
            table.refresh();
        })

        $('#setPage2').click(function() {
            table.setPage(1);
        })
		*/

		
    </script>
    
 <script> 
 /*
 	var data2 = [
					  {
						selected: '<input type=\'checkbox\' onClick=\'pickList(1);\' value=\'1\' id=\'1\'>',  
						address: '1654 W 65th St, Los Angeles, CA 90047',
						beds: '3',
						baths: '2.0',
						sqft: '1576',
						year: '1930',
						price: '564074',
						solddate: '07/09/2019',
						distance: 'N/A',
					 }];
					 
        var table2 = $('#current_comps_table_div').tableSortable({
            data,
            columns,
			sorting:true,
            searchField: '#searchField',
            responsive: {
                1100: {
                    columns: {
                        formCode: 'Form Code',
                        formName: 'Form Name',
                    },
                },
            },
            rowsPerPage: 25,
            pagination: true,
            tableWillMount: () => {
                console.log('table will mount')
            },
            tableDidMount: () => {
                console.log('table did mount')
            },
            tableWillUpdate: () => console.log('table will update'),
            tableDidUpdate: () => console.log('table did update'),
            tableWillUnmount: () => console.log('table will unmount'),
            tableDidUnmount: () => console.log('table did unmount'),
            onPaginationChange: function(nextPage, setPage) {
                setPage(nextPage);
            }
        });

        $('#changeRows').on('change', function() {
            table2.updateRowsPerPage(parseInt($(this).val(), 10));
        })

        $('#rerender').click(function() {
            table2.refresh(true);
        })

        $('#distory').click(function() {
            table2.distroy();
        })

        $('#refresh').click(function() {
            table2.refresh();
        })

        $('#setPage2').click(function() {
            table2.setPage(1);
        }) */
    </script>	
	<div style="display:none;" class="hidden_marker_boxes">
	<?php echo $box_info;?>
	</div>
 <?php } ?>	