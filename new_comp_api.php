<?php
// 
// API FUNCTION
    function SalesComparables($api_id,$params){
        
        $ch = curl_init();
    
        curl_setopt($ch, CURLOPT_URL, 'https://api.gateway.attomdata.com/property/v2/salescomparables/address/'.$params.'?searchType=Radius&minComps=1&maxComps=10&miles=5&bedroomsRange=2&bathroomRange=2&sqFeetRange=600&lotSizeRange=2000&saleDateRange=6&yearBuiltRange=10&ownerOccupied=Both&distressed=IncludeDistressed');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        
        
        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Apikey: '.$api_id;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $result = curl_exec($ch);
        
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        
        
        return $result;
         
    }
    
     // MAP SETMAKER FUNCTION
    function setmapmarker($address,$propid) {
    	echo '<script type="text/javascript"> codeAddress('.$address.','.$propid.');</script>';
    }
    
    

    // API CONFIG
    $api_id = "2b1e86b638620bf2404521e6e9e1b19e";

    // VALUES FOR API INIT
    $address = str_replace("%20","+",$_GET['address']);
	$city = str_replace("%20","+",$_GET['city']);
	$state = $_GET['state'];
	$county = $_GET['county'];
	$zip = $_GET['zip'];
	
	$citystate = $_GET['city']." ".$_GET['state'];
	
	
	$all_address = str_replace("%20","+",$_GET['address']);
	$all_city = str_replace("%20","+",$citystate);
	
	$param = $address."/".$city."/".$county."/".$state."/".$zip;
// 	$param = "11235%20S%20STEWART%20AVE/Chicago/US/IL/60628";
	
	$result = SalesComparables($api_id,$param);
	$jsonD = json_decode($result,true);
	$dataR = $jsonD['RESPONSE_GROUP']['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE_ext']['SUBJECT_PROPERTY_ext']['PROPERTY'];
    $status_cond = $jsonD['RESPONSE_GROUP']['PRODUCT']['STATUS']['@_Condition'];
    $status_code = $jsonD['RESPONSE_GROUP']['PRODUCT']['STATUS']['@_Code'];
    
    
	$response = $dataR[0];
	$bathrooms = $dataR[0]['STRUCTURE']['@TotalBathroomCount'];
	$bedrooms = $dataR[0]['STRUCTURE']['@TotalBedroomCount'];
	$finishedSqFt = $dataR[0]['SITE']['@LotSquareFeetCount'];
	$lotSizeSqFt = $dataR[0]['SITE']['@LotSquareFeetCount'];
	$yearBuilt = $dataR[0]['STRUCTURE']['STRUCTURE_ANALYSIS']['@PropertyStructureBuiltYear']; 
	$lastSoldDate = $dataR[0]['SALES_HISTORY']['@PropertySalesDate'];
	$lastSoldPrice = $dataR[0]['SALES_HISTORY']['@PropertySalesAmount'];
    $zetimate_amount = $dataR[0]['SALES_HISTORY']['@PricePerSquareFootAmount'];
	

	$properties_info  = "Beds: <strong>".$bedrooms."</strong> Baths: <strong>".$bathrooms."</strong> Sqft: <strong>".$finishedSqFt."</strong>";
	$properties_info .= "Year: <strong>".$yearBuilt."</strong> <br>Price: <strong>$".$lastSoldPrice."</strong> Sold Date: <strong>".$lastSoldDate."</strong>";


    if($status_code != 0){
	  
	   echo "No record available";
	   $no_records=1;
	
        
    }else{
	    
    	$origin_ip = $response['_IDENTIFICATION']['@LatitudeNumber'].",".$response['_IDENTIFICATION']['@LongitudeNumber'];  //for the distance G map call
    	
    	foreach($dataR as $u){
    	    $pd = $u['COMPARABLE_PROPERTY_ext'];
    	    if($u['COMPARABLE_PROPERTY_ext']){
    	        
    	        $ips .= $pd['@LatitudeNumber'];
				$ips .= "%2C";
				$ips .= $pd['@LongitudeNumber'];
				$ips .= "%7C";
    	        
    	        
    	    }
    	    
    	}
    	
    	$properties_comps_det_arr2 = $properties_comps_det_arr;
    	foreach($properties_comps_det_arr2 as $key2){
    				$ips .= $key2->address->latitude;
    				$ips .= "%2C";
    				$ips .= $key2->address->longitude;
    				$ips .= "%7C";
    	}
    	
    	$google_url_call  = "https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=".$origin_ip;
	    $google_url_call .= "&destinations=$ips&key=AIzaSyC-DrWoVislpgtWChmylA1FpmMarXjvrxs";
	
	
			$curl = curl_init();
			curl_setopt_array($curl, array(
			CURLOPT_URL => $google_url_call,
			CURLOPT_HTTPGET=> true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPGET=> true,
			CURLOPT_POST => true
			));

			$response2 = curl_exec($curl);
			$data_arr = (array)json_decode($response2);
			$data_arr = $data_arr["rows"][0]->elements;

			$cnt_distance_loop = 1;
			foreach($data_arr as $var){
			    
					$distance_array[$cnt_distance_loop] = $var->distance->text;
					$cnt_distance_loop++;
			}
			
			
			foreach($dataR as $key){
			    $cnt++;
			    
			    $pd2 = $key['COMPARABLE_PROPERTY_ext'];
    	        if($key['COMPARABLE_PROPERTY_ext']){
    	            
    	            $address  = $pd2['@_StreetAddress'].", ";
    				$address .=  $pd2['@_City'].", ";
    				$address .=  $pd2['@_State']." ";
    				$address .=  $pd2['@_PostalCode'];	
    				$latitude =  $pd2['@LatitudeNumber'];		
    				$longitude = $pd2['@LongitudeNumber'];
    				
    				$priceT = $pd2['SALES_HISTORY']['@PropertySalesAmount'];
    				
    				$bedsT = $pd2['STRUCTURE']['@TotalBedroomCount'];
    	            $bathsT = $pd2['STRUCTURE']['@TotalBathroomCount'];
    	            $sqftT = $pd2['STRUCTURE']['@GrossLivingAreaSquareFeetCount'];
    	            $yearT = $pd2['STRUCTURE']['STRUCTURE_ANALYSIS']['@PropertyStructureBuiltYear'];
    	            $soldT = $pd2['SALES_HISTORY']['@TransferDate_ext'];
    	            
    	            $comps_lng_lat[$cnt]="$longitude, $latitude";
    	            
    	            $property_price = $pd2['SALES_HISTORY']['@PropertySalesAmount'];
        			if($property_price==0){$property_price="N/A";}
        			
        			$box_info_details ="
				        Beds : ".$bedsT."<br>
						Baths : ".$bathsT."<br>
						Sqft : ".$sqftT."<br>
						Year : ".$yearT."<br>
						Price : $".$priceT."<br>
						Sold Date : ".$soldT."<br>
						<a class='street_view_link' href='javascript:void(0);' onClick='toggleStreetView($latitude,$longitude);'>Street View</a>
					";
					
					
					$box_info.="
            				<div class='col-md-12 listing-item item-id-".$cnt."  item_number_simple_".$cnt."'>
            				 <strong>".$address."</strong><br>$box_info_details
            				</div>
            		";
            		
            		setmapmarker('"'.$address.'"',$cnt);
            		
            		$address_per_id .= ',"'.$address.'"';
        				$formated_amount = number_format($priceT);
        				$distance = str_replace(" mi","",$distance_array[$cnt]);
        				$js_data.="
        					  {
        						selected: '<input type=\'checkbox\' onClick=\'pickList(".$cnt.");\' value=\'".$cnt."\' id=\'".$cnt."\'>',  
        						address: '".$address."',
        						beds: '".$bedsT."',
        						baths: '".$bathsT."',
        						sqft: '".$sqftT."',
        						year: '".$yearT."',
        						price: '".$formated_amount."',
        						solddate: '".$soldT."',
        						distance: '".$distance."',
        					 },
        				";  //
        				
        				
        				$table_data_row.="
        				    <tr>
        				      <div class='card mb-1'>
								<div class='card-header p-2'>
									<div class='row form-inline'>
									
										<div class='col-sm-1'> <input type='checkbox' onClick='pickList(".$cnt.");' value='".$cnt."' id='".$cnt."'> </div>
										<div class='col-sm-4'> ".$address." </div>
										<div class='col-sm-2'> ".$bedsT." </div>
										<div class='col-sm-2'> ".$bathsT." </div>
										<div class='col-sm-2'> ".$formated_amount." </div>
										<div class='col-sm-1 text-right'>
											<i class='fas fa-chevron-down' style='cursor:pointer' data-toggle='collapse' data-target='#row-".$cnt."' aria-expanded='false' aria-controls='row-1'></i>
										</div>
									</div>
								</div>
								<div class='collapse' id='row-".$cnt."'>
									<div class='card-body'>
										<div class='row'>
											<div class='col-sm-4'>
											   	Year Built: <b>".$yearT."</b> 
											</div>
											<div class='col-sm-4'>
												SQFT: <b>".$sqftT."</b> 
											</div>
											<div class='col-sm-4'>
												Distance: <b>".$distance."</b> 
											</div>
										</div>
									</div>
								</div>
							</div>
						</tr>
        					  <tr>
        						<td><input type='checkbox' onClick='pickList(".$cnt.");' value='".$cnt."' id='".$cnt."'></td>  
        						<td>".$address."</td>
        						<td>".$bedsT."</td>
        						<td>".$bathsT."</td>
        						<td>".$sqftT."</td>
        						<td>".$yearT."</td>
        						<td>".$formated_amount."</td>
        						<td>".$soldT."</td>
        						<td>".$distance."</td>
        					  </tr>
        				";  //
        				
        				
        				$table_data[]=array("id"=>$cnt, "address"=>$address,"bedrooms"=>$bedsT,"bathrooms"=>$bathsT,
        						    "finishedSqFt"=>$sqftT,"yearBuilt"=>$yearT,"formated_amount"=>$formated_amount,
        							"lastSoldDate"=>$soldT,"distance"=>$distance);
    	            
    	            
    	        }
        								
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

   


