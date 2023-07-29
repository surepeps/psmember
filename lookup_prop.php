<?php

global $wo, $sqlConnect;
$root = $_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);
$st_com_id = $_POST['scid'];

$user_id = $wo['user']['user_id'];

if($_POST['action']=="lookup_property"){
    
    $token = "82rzrShdRgnuXwf8tB2PGJnH7XhLSe";
    $newaddress  =  Wo_Secure($_POST['address']);
    $newcity  =  Wo_Secure($_POST['city']);
    $newstate  =  Wo_Secure($_POST['state']);
    $newzip  =  Wo_Secure($_POST['zip']);
    



    $address = substr($newaddress,0,15);
    

    $select_query = "SELECT * FROM `Wo_LookUpProp` WHERE `status` = 1 AND street_address LIKE '%{$address}%' AND `state` = '{$newstate}' OR `city` = '{$newcity}' ";
    
    $queryresList = mysqli_query($sqlConnect,$select_query);
    $srows_count =  mysqli_num_rows($queryresList);
    
    if($srows_count > 0){
         while($row = mysqli_fetch_array($queryresList)) {
             
            //  geting all values
             $addcol2 = json_decode($row['address'], true);
             $valucol = json_decode($row['valuation'], true);
             $metacol = json_decode($row['metadata'], true);
             $struccol2 = json_decode($row['structure'], true);
             $parcol2 = json_decode($row['parcel'], true);
             $owncol2 = json_decode($row['owner'], true);
             $taxcol2 = json_decode($row['taxes'], true);
             $asscol2 = json_decode($row['assessments'], true);
             $marasscol2 = json_decode($row['market_assessments'], true);
             
             $dedscolde = base64_decode($row['deeds']);
             $dedscol2 = json_decode($dedscolde, true);
             
            //  Address column change from capital letter to first word cap
             $addcol3 = array_map('strtolower', $addcol2);
             $addcol = array_map('ucwords', $addcol3);
             
             // Structure column change from capital letter to first word cap
             $struccol3 = array_map('strtolower', $struccol2);
             $struccol = array_map('ucwords', $struccol3);
             
             // Parcel column change from capital letter to first word cap
             $parcol3 = array_map('strtolower', $parcol2);
             $parcol = array_map('ucwords', $parcol3);
             
             // Owner column change from capital letter to first word cap
             $owncol3 = array_map('strtolower', $owncol2);
             $owncol = array_map('ucwords', $owncol3);
             
             // Taxes column change from capital letter to first word cap
             $taxcol3 = array_map('strtolower', $taxcol2);
             $taxcol = array_map('ucwords', $taxcol3);
             
             // Assessments column change from capital letter to first word cap
             $asscol3 = array_map('strtolower', $asscol2);
             $asscol = array_map('ucwords', $asscol3);
             
             // Deeds column change from capital letter to first word cap
             $dedscol3 = array_map('strtolower', $dedscol2);
             $dedscol = array_map('ucwords', $dedscol3);
             
             
            //  google map address
            $streetmap = $addcol['formatted_street_address'];
            $statemap = $addcol['state'];
            $citymap = $addcol['city'];
            $addressmap = $streetmap.','.$citymap.','.$statemap; 
            
            
             
                $request =  '<div class="tab-pane active" id="report" role="tabpanel" aria-labelledby="report-tab">';
                            // First Container
                $request .= '        <div class="card mb-4">';
                $request .= '            <div class="card-body">';
                $request .= '                <div class="d-sm-flex">';
                $request .= '                   <div>';
                $request .= '                        <h4 class="mb-1 font-weight-medium">'.ucwords($addcol['formatted_street_address']).' '.$newwallet.' </h4>';
                $request .= '                        <p class="text-500">'.$addcol['city'].', '.$addcol['state'].'  '.$addcol['zip_code'].'</p>';
                $request .= '                        <p class="small text-secondary mb-0"> Published: '. date('F d Y', strtotime($metacol['publishing_date'])).' </p>';
                $request .= '                    </div>';
            
                $request .= '                    <div class="ml-auto text-sm-right">';
                $request .= '                        <hr class="d-sm-none"> ';
                $request .= '                        <h4 class="font-weight-medium">';
                $request .= '                            <small class="fs-12 text-secondary">Estimated:</small> <sup>$</sup>'.number_format($valucol['value']);
                $request .= '                        </h4>';
                $request .= '                        <small class="fs-12 text-secondary">Probable range: <span class="text-black"><sup>$</sup>'.number_format($valucol['low']).' – <sup>$</sup>'.number_format($valucol['high']).'</span> </small>';
                $request .= '                    </div>';
                $request .= '                </div>';
                $request .= '            </div>';
                $request .= '            <div class="card-body p-0">';
                $request .= '                <iframe src="https://www.google.com/maps/embed/v1/place?key=AIzaSyCey3pPT-jLhjUtHfo6m26976mFvYBZeNs&amp;q='.urlencode($addressmap).'" width="100%" height="300" frameborder="0" style="border:0;display: block;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>';
                $request .= '            </div>';
                $request .= '        </div>';
                        
                        // Second Container
                $request .= '        <div class="bg-gray-200 p-2 rounded-lg mb-4">';
                $request .= '            <h4 class="fs-16 p-2 m-0">Property Details</h4>';
                $request .= '            <div class="card">';
                $request .= '                <div class="card-body">';
                $request .= '                    <p class="font-weight-medium">Property Highlights</p>';
                
                                                        
                
                                                                                
                                                  
                                                    
                                                    
                $request .= '                        <div class="row">';
                $request .= '                            <div class="col-lg-12">';
                                                            if($struccol['beds_count'] != null || $struccol['beds_count'] != ''){
                $request .= '                                       <div class="col-6 col-md-4 data-block">';
                $request .= '                                           <div class="d-flex align-items-center">';
                $request .= '                                               <div>';
                $request .= '                                                   <div class="circle_icon">';
                $request .= '                                                       <i class="material-icons">single_bed</i>';
                $request .= '                                                   </div>';
                $request .= '                                               </div>';
                $request .= '                                               <div>';
                $request .= '                                                   <p class="text-500 m-0 fs-14">Bedrooms</p>';
                $request .= '                                                   <p class="m-0 font-weight-bold text-black">'.$struccol['beds_count'].'</p>';
                $request .= '                                               </div>';
                $request .= '                                           </div>';
                $request .= '                                       </div>';
                                                            }
                                                            if($struccol['baths'] != null || $struccol['baths'] != ''){
                $request .= '                                       <div class="col-6 col-md-4 data-block">';
                $request .= '                                           <div class="d-flex align-items-center">';
                $request .= '                                               <div>';
                $request .= '                                                   <div class="circle_icon">';
                $request .= '                                                       <i class="material-icons">bathtub</i>';
                $request .= '                                                   </div>';
                $request .= '                                               </div>';
                $request .= '                                               <div>';
                $request .= '                                                   <p class="text-500 m-0 fs-14">Bathrooms</p>';
                $request .= '                                                   <p class="m-0 font-weight-bold text-black">'.$struccol['baths'].'</p>';
                $request .= '                                               </div>';
                $request .= '                                           </div>';
                $request .= '                                       </div>';
                                                            }
                                                            if($struccol['stories'] != null || $struccol['stories'] != ''){
                $request .= '                                       <div class="col-6 col-md-4 data-block">';
                $request .= '                                           <div class="d-flex align-items-center">';
                $request .= '                                               <div>';
                $request .= '                                                   <div class="circle_icon">';
                $request .= '                                                       <i class="material-icons">layers</i>';
                $request .= '                                                   </div>';
                $request .= '                                               </div>';
                $request .= '                                               <div>';
                $request .= '                                                   <p class="text-500 m-0 fs-14">Stories</p>';
                $request .= '                                                   <p class="m-0 font-weight-bold text-black">'.$struccol['stories'].'</p>';
                $request .= '                                               </div>';
                $request .= '                                           </div>';
                $request .= '                                       </div>';
                                                            }
                                                            if($struccol['total_area_sq_ft'] != null || $struccol['total_area_sq_ft'] != ''){
                $request .= '                                       <div class="col-6 col-md-4 data-block">';
                $request .= '                                           <div class="d-flex align-items-center">';
                $request .= '                                               <div>';
                $request .= '                                                   <div class="circle_icon">';
                $request .= '                                                       <i class="material-icons">square_foot</i>';
                $request .= '                                                   </div>';
                $request .= '                                               </div>';
                $request .= '                                               <div>';
                $request .= '                                                   <p class="text-500 m-0 fs-14">Total Size</p>';
                $request .= '                                                   <p class="m-0 font-weight-bold text-black"> '.$struccol['total_area_sq_ft'].' sq. ft. </p>';
                $request .= '                                               </div>';
                $request .= '                                           </div>';
                $request .= '                                       </div>';
                                                            }
                                                            if($struccol['year_built'] != null || $struccol['year_built'] != ''){
                $request .= '                                       <div class="col-6 col-md-4 data-block">';
                $request .= '                                           <div class="d-flex align-items-center">';
                $request .= '                                               <div>';
                $request .= '                                                   <div class="circle_icon">';
                $request .= '                                                       <i class="material-icons">event</i>';
                $request .= '                                                   </div>';
                $request .= '                                               </div>';
                $request .= '                                               <div>';
                $request .= '                                                   <p class="text-500 m-0 fs-14">Year Built</p>';
                $request .= '                                                   <p class="m-0 font-weight-bold text-black"> '.$struccol['year_built'].' </p>';
                $request .= '                                               </div>';
                $request .= '                                           </div>';
                $request .= '                                       </div>';
                                                            }
                                                            if($struccol['air_conditioning_type'] != null || $struccol['air_conditioning_type'] != ''){
                $request .= '                                       <div class="col-6 col-md-4 data-block">';
                $request .= '                                           <div class="d-flex align-items-center">';
                $request .= '                                               <div>';
                $request .= '                                                   <div class="circle_icon">';
                $request .= '                                                       <i class="material-icons">ac_unit</i>';
                $request .= '                                                   </div>';
                $request .= '                                               </div>';
                $request .= '                                               <div>';
                $request .= '                                                   <p class="text-500 m-0 fs-14">Air conditioning</p>';
                $request .= '                                                   <p class="m-0 font-weight-bold text-black"> '.$struccol['air_conditioning_type'].' </p>';
                $request .= '                                               </div>';
                $request .= '                                           </div>';
                $request .= '                                       </div>';
                                                            }
                                                            if($struccol['heating_type'] != null || $struccol['heating_type'] != ''){
                $request .= '                                       <div class="col-6 col-md-4 data-block">';
                $request .= '                                           <div class="d-flex align-items-center">';
                $request .= '                                               <div>';
                $request .= '                                                   <div class="circle_icon">';
                $request .= '                                                       <i class="material-icons">fireplace</i>';
                $request .= '                                                   </div>';
                $request .= '                                               </div>';
                $request .= '                                               <div>';
                $request .= '                                                   <p class="text-500 m-0 fs-14">Heating type</p>';
                $request .= '                                                      <p class="m-0 font-weight-bold text-black"> '.$struccol['heating_type'].' </p>';
                $request .= '                                                  </div>';
                $request .= '                                              </div>';
                $request .= '                                          </div>';
                                                            }
                                                            if($struccol['heating_fuel_type'] != null || $struccol['heating_fuel_type'] != ''){
                $request .= '                                          <div class="col-6 col-md-4 data-block">';
                $request .= '                                              <div class="d-flex align-items-center">';
                $request .= '                                                  <div>';
                $request .= '                                                      <div class="circle_icon">';
                $request .= '                                                          <i class="material-icons">offline_bolt</i>';
                $request .= '                                                      </div>';
                $request .= '                                                  </div>';
                $request .= '                                                  <div>';
                $request .= '                                                      <p class="text-500 m-0 fs-14">Fuel</p>';
                $request .= '                                                      <p class="m-0 font-weight-bold text-black"> '.$struccol['heating_fuel_type'].' </p>';
                $request .= '                                                  </div>';
                $request .= '                                              </div>';
                $request .= '                                          </div>';
                                                            }
                                                            if($struccol['parking_type'] != null || $struccol['parking_type'] != ''){
                $request .= '                                          <div class="col-6 col-md-4 data-block">';
                $request .= '                                              <div class="d-flex align-items-center">';
                $request .= '                                                  <div>';
                $request .= '                                                      <div class="circle_icon">';
                $request .= '                                                          <i class="material-icons">drive_eta</i>';
                $request .= '                                                      </div>';
                $request .= '                                                  </div>';
                $request .= '                                                  <div>';
                $request .= '                                                      <p class="text-500 m-0 fs-14">Parking Type</p>';
                $request .= '                                                      <p class="m-0 font-weight-bold text-black"> '.$struccol['parking_type'].' </p>';
                $request .= '                                                  </div>';
                $request .= '                                              </div>';
                $request .= '                                          </div>';
                                                            }
                                                            if($struccol['condition'] != null || $struccol['condition'] != ''){
                $request .= '                                       <div class="col-6 col-md-4 data-block">';
                $request .= '                                          <div class="d-flex align-items-center">';
                $request .= '                                              <div>';
                $request .= '                                                  <div class="circle_icon">';
                $request .= '                                                      <i class="material-icons">home_work</i>';
                $request .= '                                                  </div>';
                $request .= '                                              </div>';
                $request .= '                                              <div>';
                $request .= '                                                  <p class="text-500 m-0 fs-14">Condition</p>';
                $request .= '                                                  <p class="m-0 font-weight-bold text-black"> '.$struccol['condition'].' </p>';
                $request .= '                                              </div>';
                $request .= '                                          </div>';
                $request .= '                                      </div>';       
                                                            }
                $request .= '                                <div class="col-lg-12">';
                $request .= '                                    <div class="collapse" id="full-details">';
                $request .= '                                        <div class="pt-4">';
                $request .= '                                            <div class="row">';
                                                            // ADDITIONAL ADDRESS DETAILS
                                                                        if($addcol['carrier_code'] != null || $addcol['carrier_code'] != ''){
                $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                $request .= '                                                    <span class="text-500 d-block fs-13">Carrier Code:</span>';
                $request .= '                                                    <span class="fs-14">'.$addcol['carrier_code'].'</span>';
                $request .= '                                                </div>';
                                                                        }
                                                                        if($addcol['latitude'] != null || $addcol['latitude'] != ''){
                $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                $request .= '                                                    <span class="text-500 d-block fs-13">Latitude:</span>';
                $request .= '                                                    <span class="fs-14">'.$addcol['latitude'].'</span>';
                $request .= '                                                </div>';
                                                                        }
                                                                        if($addcol['longitude'] != null || $addcol['longitude'] != ''){
                $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                $request .= '                                                    <span class="text-500 d-block fs-13">Longitude:</span>';
                $request .= '                                                    <span class="fs-14">'.$addcol['longitude'].'</span>';
                $request .= '                                                </div>';
                                                                        }
                                                                        if($addcol['geocoding_accuracy'] != null || $addcol['geocoding_accuracy'] != ''){
                $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                $request .= '                                                    <span class="text-500 d-block fs-13">Geocoding Accuracy:</span>';
                $request .= '                                                    <span class="fs-14">'.$addcol['geocoding_accuracy'].'</span>';
                $request .= '                                                </div>';
                                                                        }
                                                                        if($addcol['census_tract'] != null || $addcol['census_tract'] != ''){
                $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                $request .= '                                                    <span class="text-500 d-block fs-13">Census Tract:</span>';
                $request .= '                                                    <span class="fs-14">'.$addcol['census_tract'].'</span>';
                $request .= '                                                </div>';   
                                                                        }
                                                                        
                                                    // PARCEL DATA DETAILS
                                                                        if($parcol['apn_original'] != null || $parcol['apn_original'] != ''){
                $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                $request .= '                                                    <span class="text-500 d-block fs-13">Original APN:</span>';
                $request .= '                                                    <span class="fs-14">'.$parcol['apn_original'].'</span>';
                $request .= '                                                </div>';
                                                                        }
                                                                        if($parcol['apn_unformatted'] != null || $parcol['apn_unformatted'] != ''){
                $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                $request .= '                                                    <span class="text-500 d-block fs-13">Unformatted APN:</span>';
                $request .= '                                                    <span class="fs-14">'.$parcol['apn_unformatted'].'</span>';
                $request .= '                                                </div>';
                                                                        }
                                                                        if($parcol['fips_code'] != null || $parcol['fips_code'] != ''){
                $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                $request .= '                                                    <span class="text-500 d-block fs-13">FIPS Code:</span>';
                $request .= '                                                    <span class="fs-14">'.$parcol['fips_code'].'</span>';
                $request .= '                                                </div>';
                                                                        }
                                                                        if($parcol['area_sq_ft'] != null || $parcol['area_sq_ft'] != ''){
                $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                $request .= '                                                    <span class="text-500 d-block fs-13">Lot Size:</span>';
                $request .= '                                                    <span class="fs-14">'.$parcol['area_sq_ft'].' sq. ft.</span>';
                $request .= '                                                </div>';
                                                                        }
                                                                        if($parcol['area_acres'] != null || $parcol['area_acres'] != ''){
                $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                $request .= '                                                    <span class="text-500 d-block fs-13">Lot Size:</span>';
                $request .= '                                                    <span class="fs-14">'.$parcol['area_acres'].' acres</span>';
                $request .= '                                                </div>';
                                                                        }
                                                                        if($parcol['county_name'] != null || $parcol['county_name'] != ''){
                $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                $request .= '                                                    <span class="text-500 d-block fs-13">County Name:</span>';
                $request .= '                                                    <span class="fs-14">'.$parcol['county_name'].'</span>';
                $request .= '                                                </div>';
                                                                        }
                                                                        if($parcol['county_land_use_code'] != null || $parcol['county_land_use_code'] != ''){
                $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                $request .= '                                                    <span class="text-500 d-block fs-13">County Land Use Code:</span>';
                $request .= '                                                    <span class="fs-14">'.$parcol['county_land_use_code'].'</span>';
                $request .= '                                                </div>';
                                                                        }
                                                                        if($parcol['county_land_use_description'] != null || $parcol['county_land_use_description'] != ''){
                $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                $request .= '                                                    <span class="text-500 d-block fs-13">County Land Use Description:</span>';
                $request .= '                                                    <span class="fs-14">'.$parcol['county_land_use_description'].'</span>';
                $request .= '                                                </div>';
                                                                        }
                                                                        if($parcol['standardized_land_use_category'] != null || $parcol['standardized_land_use_category'] != ''){
                $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                $request .= '                                                    <span class="text-500 d-block fs-13">Standardized Land Use Category:</span>';
                $request .= '                                                    <span class="fs-14">'.$parcol['standardized_land_use_category'].'</span>';
                $request .= '                                                </div>';
                                                                        }
                                                                        if($parcol['standardized_land_use_type'] != null || $parcol['standardized_land_use_type'] != ''){
                $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                $request .= '                                                    <span class="text-500 d-block fs-13">Standardized Land Use Type:</span>';
                $request .= '                                                    <span class="fs-14">'.$parcol['standardized_land_use_type'].'</span>';
                $request .= '                                                </div>';
                                                                        }
                                                                        if($parcol['lot_number'] != null || $parcol['lot_number'] != ''){
                $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                $request .= '                                                    <span class="text-500 d-block fs-13">Lot Number:</span>';
                $request .= '                                                    <span class="fs-14">'.$parcol['lot_number'].'</span>';
                $request .= '                                                </div>';
                                                                        }
                                                                        if($parcol['subdivision'] != null || $parcol['subdivision'] != ''){
                $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                $request .= '                                                    <span class="text-500 d-block fs-13">Subdivision:</span>';
                $request .= '                                                    <span class="fs-14">'.$parcol['subdivision'].'</span>';
                $request .= '                                                </div>';
                                                                        }
                                                                        if($parcol['municipality'] != null || $parcol['municipality'] != ''){
                $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                $request .= '                                                    <span class="text-500 d-block fs-13">Municipality:</span>';
                $request .= '                                                    <span class="fs-14">'.$parcol['municipality'].'</span>';
                $request .= '                                                </div>';
                                                                        }
                                                                        if($parcol['zoning'] != null || $parcol['zoning'] != ''){
                $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                $request .= '                                                    <span class="text-500 d-block fs-13">Zoning:</span>';
                $request .= '                                                    <span class="fs-14">'.$parcol['zoning'].'</span>';
                $request .= '                                                </div>';
                                                                        }
                                                                        if($parcol['tax_account_number'] != null || $parcol['tax_account_number'] != ''){
                $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                $request .= '                                                    <span class="text-500 d-block fs-13">Tax Account Number:</span>';
                $request .= '                                                    <span class="fs-14">'.$parcol['tax_account_number'].'</span>';
                $request .= '                                                </div>';
                                                                        }
                                                                        if($parcol['section_township_range'] != null || $parcol['section_township_range'] != ''){
                $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                $request .= '                                                    <span class="text-500 d-block fs-13">Sec/Twp/Rng:</span>';
                $request .= '                                                    <span class="fs-14">'.$parcol['section_township_range'].'</span>';
                $request .= '                                                </div>';
                                                                        }
                                                                         
                                                                        if($parcol['legal_description'] != null || $parcol['legal_description'] != ''){
                $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                $request .= '                                                    <span class="text-500 d-block fs-13">Legal Description:</span>';
                $request .= '                                                    <span class="fs-14">'.$parcol['legal_description'].'</span>';
                $request .= '                                                </div>';  
                                                                        }
                                                // STRUCTURE DATA DETAILS
                                                                        if($struccol['fireplaces'] != null || $struccol['fireplaces'] != ''){
                $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                $request .= '                                                    <span class="text-500 d-block fs-13">Fireplace(s):</span>';
                $request .= '                                                    <span class="fs-14">'.$struccol['fireplaces'].'</span>';
                $request .= '                                                </div>'; 
                                                                        }
                                                                        if($struccol['effective_year_built'] != null || $struccol['effective_year_built'] != ''){
                $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                $request .= '                                                    <span class="text-500 d-block fs-13">Effective Year Built:</span>';
                $request .= '                                                    <span class="fs-14">'.$struccol['effective_year_built'].'</span>';
                $request .= '                                                </div>';
                                                                        }
                                                                        if($struccol['quality'] != null || $struccol['quality'] != ''){
                $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                $request .= '                                                    <span class="text-500 d-block fs-13">Quality:</span>';
                $request .= '                                                    <span class="fs-14">'.$struccol['quality'].'</span>';
                $request .= '                                                </div>';
                                                                        }
                                                                        if($struccol['parking_spaces_count'] != null || $struccol['parking_spaces_count'] != ''){
                $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                $request .= '                                                    <span class="text-500 d-block fs-13">Parking Spaces Count:</span>';
                $request .= '                                                    <span class="fs-14">'.$struccol['parking_spaces_count'].'</span>';
                $request .= '                                                </div>';
                                                                        }
                                                                        if($struccol['exterior_wall_type'] != null || $struccol['exterior_wall_type'] != ''){
                $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                $request .= '                                                    <span class="text-500 d-block fs-13">Exterior Wall Type:</span>';
                $request .= '                                                    <span class="fs-14">'.$struccol['exterior_wall_type'].'</span>';
                $request .= '                                                </div>';
                                                                        }
                                                                        if($struccol['interior_wall_type'] != null || $struccol['interior_wall_type'] != ''){
                $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                $request .= '                                                    <span class="text-500 d-block fs-13">Interior Walls:</span>';
                $request .= '                                                    <span class="fs-14">'.$struccol['interior_wall_type'].'</span>';
                $request .= '                                                </div>';
                                                                        }
                                                                        if($struccol['flooring_types'] != null || $struccol['flooring_types'] != '' || $struccol['flooring_types'] != '[]'){
                $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                $request .= '                                                    <span class="text-500 d-block fs-13">Flooring Type:</span>';
                                                                                $mee = json_decode($struccol['flooring_types'],true);
                $request .= '                                                    <span class="fs-14">'.$mee[0].'</span>';
                $request .= '                                                </div>';
                                                                        }
                                                                        if($struccol['condition'] != null || $struccol['condition'] != ''){
                $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                $request .= '                                                    <span class="text-500 d-block fs-13">Condition:</span>';
                $request .= '                                                    <span class="fs-14">'.$struccol['condition'].'</span>';
                $request .= '                                                </div>';
                                                                        }
                                                                        if($struccol['roof_material_type'] != null || $struccol['roof_material_type'] != ''){
                $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                $request .= '                                                    <span class="text-500 d-block fs-13">Roof Material:</span>';
                $request .= '                                                    <span class="fs-14">'.$struccol['roof_material_type'].'</span>';
                $request .= '                                                </div>';
                                                                        }
                                                                        if($struccol['roof_style_type'] != null || $struccol['roof_style_type'] != ''){
                $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                $request .= '                                                    <span class="text-500 d-block fs-13">Roof Style:</span>';
                $request .= '                                                    <span class="fs-14">'.$struccol['roof_style_type'].'</span>';
                $request .= '                                                </div>';
                                                                        }
                $request .= '                                            </div>';
                $request .= '                                        </div>';
                $request .= '                                    </div>';
                $request .= '                                </div>';
                
                $request .= '                                <div class="col-lg-12">';
                $request .= '                                    <p class="mb-0 mt-4">';
                $request .= '                                        <a href="#full-details" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="full-details" id="toggle-more-details">';
                $request .= '                                            View additional property details <i class="material-icons">arrow_drop_down</i>';
                $request .= '                                        </a>';
                $request .= '                                    </p>';
                $request .= '                                </div>';
                $request .= '                            </div>';
                $request .= '                        </div>';
                
                
                $request .= '                    </div>';
                $request .= '                </div>';
                $request .= '                </div>';
                                
                                        
                $request .= '                 <div class="bg-gray-200 p-2 pb-0 rounded-lg mb-4">';
                $request .= '                     <h4 class="fs-16 p-2 m-0">Current Ownership Information</h4>';
                $request .= '                     <div class="card mb-1">';
                $request .= '                         <div class="card-body">';
                $request .= '                             <div class="row">';
                $request .= '                                 <div class="col-lg-12">';
                $request .= '                                     <div class="col-md col-lg-6">';
                $request .= '                                         <div class="d-flex">';
                $request .= '                                             <div class="pr-4">';
                $request .= '                                                 <img src="https://estated.com/images/v2/report/owner-avatar.png" class="avatar" alt="owner avatar">';
                $request .= '                                             </div>';
                $request .= '                                             <div>';
                $request .= '                                                 <span class="text-500 d-block fs-13">Owner Name(s):</span>';
                $request .= '                                                 <span class="fs-14">'.wordwrap($owncol['name'],16,"<br>\n").'</span>';
                $request .= '                                             </div>';
                $request .= '                                         </div>';
                $request .= '                                     </div>';
                $request .= '                                     <div class="col-md col-lg-6">';
                $request .= '                                         <hr class="d-md-none">';
                $request .= '                                         <span class="text-500 d-block fs-13">Mailing Address:</span>';
                $request .= '                                         <span class="fs-14">'.$owncol['formatted_street_address'].' <br>'.$owncol['city'].', '.$owncol['state'].'  '. $owncol['zip_code'].' <br><small class="fs-12"><strong><em>*Owner is known to occupy subject property</em></strong></small> </span>';
                $request .= '                                     </div>';
                $request .= '                                 </div>';
                $request .= '                             </div>';
                $request .= '                         </div>';
                $request .= '                     </div>';
                $request .= '                 </div>';
                
                
                
            
                //                  Taxes 
                $request .= '                <div class="bg-gray-200 p-2 pb-0 rounded-lg mb-4">';
                $request .= '                    <h4 class="fs-16 p-2 m-0">Taxes</h4>';
                $request .= '                    <div class="card">';
                $request .= '                        <div class="card-body p-0">';
                                                    if($taxcol2 != '[]' || $taxcol2 != ''){
                $request .= '                           <div class="table-responsive">';
                $request .= '                                    <table class="table m-0">';
                $request .= '                                        <thead>';
                $request .= '                                        <tr class="fs-13">';
                $request .= '                                            <th class="font-weight-medium text-500">Year</th>';
                $request .= '                                            <th class="font-weight-medium text-500">Amount</th>';
                $request .= '                                            <th class="font-weight-medium text-500">Exemptions</th>';
                $request .= '                                            <th class="font-weight-medium text-500 text-right">Rate Code Area</th>';
                $request .= '                                        </tr>';
                $request .= '                                        </thead>';
                $request .= '                                        <tbody>';
                
                                                            foreach($taxcol2 as $tax){
                $request .= '                                            <tr class="fs-15">';
                $request .= '                                                <td>'.$tax['year'].'</td>';
                $request .= '                                                <td class="font-weight-medium"> $ '.number_format($tax['amount']).' </td>';
                $request .= '                                                <td class="font-weight-medium">'.$tax['amount'].'</td>';
                $request .= '                                                <td class="font-weight-medium text-right">'.$tax['rate_code_area'].' </td>';
                $request .= '                                            </tr>';
                                                            }
                
                $request .= '                                        </tbody>';
                $request .= '                                    </table>';
                $request .= '                                </div>';
                                                    }else{
                $request .= '                              <div class="text-center p-4">No Taxex records on file.</div>';
                                                    }
                $request .= '                        </div>';
                $request .= '                    </div>';
                $request .= '                </div>';
                
                
            
                //                  Assessments 
                $request .= '                <div class="bg-gray-200 p-2 pb-0 rounded-lg mb-4">';
                $request .= '                    <h4 class="fs-16 p-2 m-0">Assessments</h4>';
                $request .= '                    <div class="card">';
                $request .= '                        <div class="card-body p-0">';
                                                    if($asscol2 != '[]' || $asscol2 != ''){
                $request .= '                           <div class="table-responsive">';
                $request .= '                                    <table class="table m-0">';
                $request .= '                                        <thead>';
                $request .= '                                        <tr class="fs-13">';
                $request .= '                                            <th class="font-weight-medium text-500">Year</th>';
                $request .= '                                            <th class="font-weight-medium text-500">Land value</th>';
                $request .= '                                            <th class="font-weight-medium text-500">Improvement value</th>';
                $request .= '                                            <th class="font-weight-medium text-500 text-right">Total value</th>';
                $request .= '                                        </tr>';
                $request .= '                                        </thead>';
                $request .= '                                        <tbody>';
                
                                                            foreach($asscol2 as $aco){
                $request .= '                                           <tr class="fs-15">';
                $request .= '                                                <td>'.$aco['year'].'</td>';
                $request .= '                                                <td class="font-weight-medium"> $'.number_format($aco['land_value']).'</td>';
                $request .= '                                                <td class="font-weight-medium"> $'.number_format($aco['improvement_value']).' </td>';
                $request .= '                                                <td class="font-weight-medium text-right"> $'.number_format($aco['total_value']).' </td>';
                $request .= '                                           </tr>';
                                                            }
                $request .= '                                        </tbody>';
                $request .= '                                    </table>';
                $request .= '                                </div>';
                                                    }else{
                $request .= '                              <div class="text-center p-4">No Assessments records on file.</div>';
                                                    }
                $request .= '                           </div>';
                $request .= '                    </div>';
                $request .= '                </div>';
            
                                //  Market Assessments 
                $request .= '                <div class="bg-gray-200 p-2 pb-0 rounded-lg mb-4">';
                $request .= '                    <h4 class="fs-16 p-2 m-0">Market Assessments</h4>';
                $request .= '                    <div class="card">';
                $request .= '                        <div class="card-body p-0">';
                                                if($marasscol2 != '[]' || $marasscol2 != ''){
                $request .= '                           <div class="table-responsive">';
                $request .= '                                    <table class="table m-0">';
                $request .= '                                        <thead>';
                $request .= '                                        <tr class="fs-13">';
                $request .= '                                            <th class="font-weight-medium text-500">Year</th>';
                $request .= '                                            <th class="font-weight-medium text-500">Land value</th>';
                $request .= '                                            <th class="font-weight-medium text-500">Improvement value</th>';
                $request .= '                                            <th class="font-weight-medium text-500 text-right">Total value</th>';
                $request .= '                                        </tr>';
                $request .= '                                        </thead>';
                $request .= '                                        <tbody>';
                
                                                            foreach($asscol2 as $aco){
                $request .= '                                           <tr class="fs-15">';
                $request .= '                                                <td>'.$aco['year'].'</td>';
                $request .= '                                                <td class="font-weight-medium"> $'.number_format($aco['land_value']).'</td>';
                $request .= '                                                <td class="font-weight-medium"> $'.number_format($aco['improvement_value']).' </td>';
                $request .= '                                                <td class="font-weight-medium text-right"> $'.number_format($aco['total_value']).' </td>';
                $request .= '                                           </tr>';
                                                            }
                $request .= '                                        </tbody>';
                $request .= '                                    </table>';
                $request .= '                                </div>';
                                                    }else{
                $request .= '                              <div class="text-center p-4">No market assessment records on file.</div>';
                                                    }
                $request .= '                        </div>';
                $request .= '                    </div>';
                $request .= '                </div>';
            
                //                  Deeds 
                
                    
                $request .= '                <div class="bg-gray-200 p-2 pb-0 rounded-lg mb-4">';
                $request .= '                    <h4 class="fs-16 p-2 m-0">Deeds</h4>';
                    if($dedscol2 != '[]' || $dedscol2 != ''){
                        $num = 1;
                        foreach($dedscol2 as $deds){
                $request .= '                            <div class="card mb-1">';
                $request .= '                                <div class="card-body">';
                $request .= '                                    <div class="row">';
                $request .= '                                        <div class="col-lg-12">';
                $request .= '                                            <div class="col-lg-9">';
                $request .= '                                                <div class="row">';
                                                                                if($deds['document_type'] != null || $deds['document_type'] != ''){
                $request .= '                                                    <div class="col-auto">';
                $request .= '                                                        <small class="d-block text-500 fs-13">Document Type:</small>';
                $request .= '                                                        <strong class="fs-15">'.$deds['document_type'].'</strong>';
                $request .= '                                                    </div>';
                                                                                }
                                                                                if($deds['sale_price'] != null || $deds['sale_price'] != ''){
                $request .= '                                                    <div class="col-auto">';
                $request .= '                                                       <small class="d-block text-500 fs-13">Sale price:</small>';
                $request .= '                                                       <strong class="fs-15">$'.$deds['sale_price'].'</strong>';
                $request .= '                                                    </div>';
                                                                                }
                                                                                if($deds['recording_date'] != null || $deds['recording_date'] != ''){
                $request .= '                                                   <div class="col-auto">';
                $request .= '                                                        <small class="d-block text-500 fs-13">Recording date:</small>';
                $request .= '                                                        <strong class="fs-15">'.$deds['recording_date'].'</strong>';
                $request .= '                                                    </div>';
                                                                                }
                $request .= '                                               </div>';
                $request .= '                                            </div>';
                $request .= '                                            <div class="col-lg-3 ml-auto text-right">';
                $request .= '                                                <a data-toggle="collapse" href="#deed-1-'.$num.'" aria-expanded="false" aria-controls="deed-1" class="collapsed">';
                $request .= '                                                    <i class="material-icons fs-30">expand_more</i>';
                $request .= '                                                </a>';
                $request .= '                                            </div>';
                $request .= '                                        </div>';
                $request .= '                                    </div>';
                $request .= '                                </div>';
                                                
                $request .= '                                <div class="card-body border-top collapse" id="deed-1-'.$num.'" style="">';
                $request .= '                                    <div class="row">';
                $request .= '                                        <div class="col-lg-12">';
                $request .= '                                            <div class="col-md-6">';
                $request .= '                                                <h5 class="text-uppercase fs-12 font-weight-medium text-500">Seller Information:</h5>';
                $request .= '                                                <p>';
                $request .= '                                                <span class="fs-14">'.$deds['seller_first_name'].' '.$deds['seller_last_name'].' <br> '.$deds['seller2_first_name'].' '.$deds['seller2_last_name'].' </span>';
                $request .= '                                                    <br>';
                $request .= '                                                    <small class="text-muted"></small>';
                $request .= '                                                </p>';
                $request .= '                                            </div>';
                $request .= '                                            <div class="col-md-6">';
                $request .= '                                                <hr class="d-md-none">';
                $request .= '                                                <h5 class="text-uppercase fs-12 font-weight-medium text-500">Buyer Information:</h5>';
                $request .= '                                                <p>';
                $request .= '                                                   <span class="fs-14">'.$deds['buyer_first_name'].' '.$deds['buyer_last_name'].'<br>'.$deds['buyer2_first_name'].' '.$deds['buyer2_last_name'].'</span>';
                $request .= '                                                    <br>';
                $request .= '                                                    <small class="text-muted"> '.$deds['buyer_address'].', '.$deds['buyer_city'].', '.$deds['buyer_state'].' '.$deds['buyer_zip_code'].' </small>';
                $request .= '                                                </p>';
                $request .= '                                            </div>';
                $request .= '                                        </div>';
                $request .= '                                    </div>';
                    
                $request .= '                                     <div class="col-lg-12">';
                $request .= '                                         <div class="border-top border-bottom pt-3 pb-2 mb-2">';
                $request .= '                                             <h5 class="text-uppercase fs-12 font-weight-medium text-500">Lender Information:</h5>';
                $request .= '                                             <div class="row fs-14">';
                $request .= '                                                 <div class="col-auto py-2">';
                $request .= '                                                     <small class="d-block text-500">Loan Amount:</small>';
                                                                                if($deds['loan_amount'] == null || $deds['loan_amount'] == ''){
                $request .= '                                                     <span class="text-black"> - </span>';
                                                                                }else{
                $request .= '                                                     <span class="text-black">$'.number_format($deds['loan_amount']).'</span>';
                                                                                }
                $request .= '                                                 </div>';
                $request .= '                                                 <div class="col-auto py-2">';
                $request .= '                                                     <small class="d-block text-500">Lender Type:</small>';
                                                                                if($deds['lender_type'] == null || $deds['lender_type'] == ''){
                $request .= '                                                     <span class="text-black">-</span>';
                                                                                }else{
                $request .= '                                                     <span class="text-black">'.$deds['lender_type'].'</span>';
                                                                                }
                $request .= '                                                 </div>';
                $request .= '                                                 <div class="col-auto py-2">';
                $request .= '                                                     <small class="d-block text-500">Loan type: </small>';
                                                                                if($deds['loan_type'] == null || $deds['loan_type'] == ''){
                $request .= '                                                     <span class="text-black">-</span>';
                                                                                }else{
                $request .= '                                                     <span class="text-black">'.$deds['loan_type'].'</span>';
                                                                                }
                $request .= '                                                 </div>';
                $request .= '                                                 <div class="col-auto py-2">';
                $request .= '                                                     <small class="d-block text-500">Loan due date:</small>';
                                                                                if($deds['loan_due_date'] == null || $deds['loan_due_date'] == ''){
                $request .= '                                                     <span class="text-black">-</span>';
                                                                                }else{
                $request .= '                                                     <span class="text-black">'.$deds['loan_due_date'].'</span>';
                                                                                }
                $request .= '                                                 </div>';
                $request .= '                                                 <div class="col-auto py-2">';
                $request .= '                                                     <small class="d-block text-500">Finance Type:</small>';
                                                                                if($deds['loan_finance_type'] == null || $deds['loan_finance_type'] == ''){
                $request .= '                                                     <span class="text-black">-</span>';
                                                                                }else{
                $request .= '                                                     <span class="text-black">'.$deds['loan_finance_type'].'</span>';
                                                                                }
                $request .= '                                                 </div>';
                $request .= '                                                 <div class="col-auto py-2">';
                $request .= '                                                     <small class="d-block text-500">Interest Rate:</small>';
                                                                                if($deds['loan_interest_rate'] == null || $deds['loan_interest_rate'] == ''){
                $request .= '                                                     <span class="text-black">-</span>';
                                                                                }else{
                $request .= '                                                     <span class="text-black">'.$deds['loan_interest_rate'].'%</span>';
                                                                                }
                $request .= '                                                 </div>';
                $request .= '                                             </div>';
                $request .= '                                         </div>';
                        
                $request .= '                                        <div class="pb-2 mb-3 border-bottom">';
                $request .= '                                            <div class="row fs-14">';
                $request .= '                                                <div class="col-auto py-2">';
                $request .= '                                                    <small class="d-block text-500">Transfer tax:</small>';
                                                                                if($deds['transfer_tax'] == null || $deds['transfer_tax'] == ''){
                $request .= '                                                     <span class="text-black">-</span>';
                                                                                }else{
                $request .= '                                                     <span class="text-black">$'.number_format($deds['transfer_tax']).'</span>';
                                                                                }
                $request .= '                                                </div>';
                $request .= '                                                <div class="col-auto py-2">';
                $request .= '                                                    <small class="d-block text-500">Original contract date:</small>';
                                                                                if($deds['original_contract_date'] == null || $deds['original_contract_date'] == ''){
                $request .= '                                                     <span class="text-black">-</span>';
                                                                                }else{
                $request .= '                                                     <span class="text-black">'.$deds['original_contract_date'].'</span>';
                                                                                }
                $request .= '                                                </div>';
                $request .= '                                                <div class="col-auto py-2">';
                $request .= '                                                    <small class="d-block text-500">Deed book:</small>';
                                                                                if($deds['deed_book'] == null || $deds['deed_book'] == ''){
                $request .= '                                                     <span class="text-black">-</span>';
                                                                                }else{
                $request .= '                                                     <span class="text-black">'.$deds['deed_book'].'</span>';
                                                                                }
                $request .= '                                                </div>';
                $request .= '                                                <div class="col-auto py-2">';
                $request .= '                                                    <small class="d-block text-500">Deed page:</small>';
                                                                                if($deds['deed_page'] == null || $deds['deed_page'] == ''){
                $request .= '                                                     <span class="text-black">-</span>';
                                                                                }else{
                $request .= '                                                     <span class="text-black">'.$deds['deed_page'].'</span>';
                                                                                }
                $request .= '                                                </div>';
                $request .= '                                                <div class="col-auto py-2">';
                $request .= '                                                    <small class="d-block text-500">Document ID:</small>';
                                                                                if($deds['document_id'] == null || $deds['document_id'] == ''){
                $request .= '                                                     <span class="text-black">-</span>';
                                                                                }else{
                $request .= '                                                     <span class="text-black">'.$deds['document_id'].'</span>';
                                                                                }
                $request .= '                                                </div>';
                $request .= '                                            </div>';
                $request .= '                                        </div>';
                $request .= '                                        <small class="d-block text-500">Sale price description:</small>';
                                                                if($deds['sale_price_description'] == null || $deds['sale_price_description'] == ''){
                $request .= '                                        <p class="fs-14 m-0">No Sale Price Description</p>';
                                                                }else{
                $request .= '                                        <p class="fs-14 m-0">'.$deds['sale_price_description'].'</p>';    
                                                                }
                
                $request .= '                                    </div>';
                $request .= '                                </div>';
                
                    
                $request .= '                           </div>';
                    $num ++;
                        }
                    }else{
                $request .= '                           <div class="card-body p-0">';
                $request .= '                              <div class="text-center p-4">No Deeds records on file.</div>';
                $request .= '                           </div>';
                    }
                    
                $request .= '                 </div>';
                // $request .= '                 <div>';
                // $request .= '                   <button onclick="'.printDiv("print-it","Title").'">print div</button>';

                // $request .= '                   <button onclick="'.saveDiv("print-it","Title").'">save div as </button>';
                // $request .= '                 </div>';
                
            
                  $request .=  '</div>';
                  $request .= '<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>';
                  $request .= '<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>'; 
                  
         }
    }else{
            // API CODE START HERE
                
                $ch = curl_init();
            
                curl_setopt($ch, CURLOPT_URL, "https://apis.estated.com/v4/property?token=".$token."&street_address=".urlencode($newaddress)."&city=".urlencode($newcity)."&state=".urlencode($newstate)."&zip_code=".$newzip."");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                
                $properties = curl_exec($ch);
                $myresult = json_decode($properties, true);
                
                $dbaddress = $myresult['data']['address'];
                $street_address = $dbaddress['formatted_street_address'];
                $state = $dbaddress['state'];
                $city = $dbaddress['city'];
                $zip_code = $dbaddress['zip_code'];
                
                $myaddress = json_encode($myresult['data']['address']);
                $parcel = json_encode($myresult['data']['parcel']);
                $structure = json_encode($myresult['data']['structure']);
                $valuation = json_encode($myresult['data']['valuation']);
                $taxes = json_encode($myresult['data']['taxes']);
                $assessments = json_encode($myresult['data']['assessments']);
                $market_assessments = json_encode($myresult['data']['market_assessments']);
                $owner = json_encode($myresult['data']['owner']);
                $deeds = json_encode($myresult['data']['deeds']);
                $deeddb = base64_encode($deed);
                $metadata = json_encode($myresult['data']['metadata']);
                
                
                if($myresult['data'] != null || $myresult['data'] != ''){
            
                    $insert_query = mysqli_query($sqlConnect, "INSERT INTO Wo_LookUpProp (`user_id`,`street_address`,`state`,`city`,`zip_code`,`address`,`parcel`,`structure`,`valuation`,`taxes`,`assessments`,`market_assessments`,`owner`,`deeds`,`metadata`,`status`) VALUES ({$user_id},'{$street_address}','{$state}','{$city}','{$zip_code}','{$myaddress}','{$parcel}','{$structure}','{$valuation}','{$taxes}','{$assessments}','{$market_assessments}','{$owner}','{$deeddb}','{$metadata}', 1)");
                    if (2>1) {
                                $request = '<div>'.$deeds.'</div>';
                                //  geting all values
                         $addcol2 = json_decode($myaddress, true);
                         $valucol = json_decode($valuation, true);
                         $metacol = json_decode($metadata, true);
                         $struccol2 = json_decode($structure, true);
                         $parcol2 = json_decode($parcel, true);
                         $owncol2 = json_decode($owner, true);
                         $taxcol2 = json_decode($taxes, true);
                         $asscol2 = json_decode($assessments, true);
                         $marasscol2 = json_decode($market_assessments, true);
                         $dedscol2 = json_decode($deeds, true);
                         
                        //  Address column change from capital letter to first word cap
                         $addcol3 = array_map('strtolower', $addcol2);
                         $addcol = array_map('ucwords', $addcol3);
                         
                         // Structure column change from capital letter to first word cap
                         $struccol3 = array_map('strtolower', $struccol2);
                         $struccol = array_map('ucwords', $struccol3);
                         
                         // Parcel column change from capital letter to first word cap
                         $parcol3 = array_map('strtolower', $parcol2);
                         $parcol = array_map('ucwords', $parcol3);
                         
                         // Owner column change from capital letter to first word cap
                         $owncol3 = array_map('strtolower', $owncol2);
                         $owncol = array_map('ucwords', $owncol3);
                         
                         // Taxes column change from capital letter to first word cap
                         $taxcol3 = array_map('strtolower', $taxcol2);
                         $taxcol = array_map('ucwords', $taxcol3);
                         
                         // Assessments column change from capital letter to first word cap
                         $asscol3 = array_map('strtolower', $asscol2);
                         $asscol = array_map('ucwords', $asscol3);
                         
                         // Deeds column change from capital letter to first word cap
                         $dedscol3 = array_map('strtolower', $dedscol2);
                         $dedscol = array_map('ucwords', $dedscol3);
                         
                         
                        //  google map address
                        $streetmap = $addcol['formatted_street_address'];
                        $statemap = $addcol['state'];
                        $citymap = $addcol['city'];
                        $addressmap = $streetmap.','.$citymap.','.$statemap; 
                         
                            $request =  '<div class="tab-pane active" id="report" role="tabpanel" aria-labelledby="report-tab">';
                                        // First Container
                            $request .= '        <div class="card mb-4">';
                            $request .= '            <div class="card-body">';
                            $request .= '                <div class="d-sm-flex">';
                            $request .= '                   <div>';
                            $request .= '                        <h4 class="mb-1 font-weight-medium">'.ucwords($addcol['formatted_street_address']).'</h4>';
                            $request .= '                        <p class="text-500">'.$addcol['city'].', '.$addcol['state'].'  '.$addcol['zip_code'].'</p>';
                            $request .= '                        <p class="small text-secondary mb-0"> Published: '. date('F d Y', strtotime($metacol['publishing_date'])).' </p>';
                            $request .= '                    </div>';
                        
                            $request .= '                    <div class="ml-auto text-sm-right">';
                            $request .= '                        <hr class="d-sm-none"> ';
                            $request .= '                        <h4 class="font-weight-medium">';
                            $request .= '                            <small class="fs-12 text-secondary">Estimated:</small> <sup>$</sup>'.number_format($valucol['value']);
                            $request .= '                        </h4>';
                            $request .= '                        <small class="fs-12 text-secondary">Probable range: <span class="text-black"><sup>$</sup>'.number_format($valucol['low']).' – <sup>$</sup>'.number_format($valucol['high']).'</span> </small>';
                            $request .= '                    </div>';
                            $request .= '                </div>';
                            $request .= '            </div>';
                            $request .= '            <div class="card-body p-0">';
                            $request .= '                <iframe src="https://www.google.com/maps/embed/v1/place?key=AIzaSyCey3pPT-jLhjUtHfo6m26976mFvYBZeNs&amp;q='.urlencode($addressmap).'" width="100%" height="300" frameborder="0" style="border:0;display: block;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>';
                            $request .= '            </div>';
                            $request .= '        </div>';
                                    
                                    // Second Container
                            $request .= '        <div class="bg-gray-200 p-2 rounded-lg mb-4">';
                            $request .= '            <h4 class="fs-16 p-2 m-0">Property Details</h4>';
                            $request .= '            <div class="card">';
                            $request .= '                <div class="card-body">';
                            $request .= '                    <p class="font-weight-medium">Property Highlights</p>';
                            
                                                                    
                            
                                                                                            
                                                              
                                                                
                                                                
                            $request .= '                        <div class="row">';
                            $request .= '                            <div class="col-lg-12">';
                                                                        if($struccol['beds_count'] != null || $struccol['beds_count'] != ''){
                            $request .= '                                       <div class="col-6 col-md-4 data-block">';
                            $request .= '                                           <div class="d-flex align-items-center">';
                            $request .= '                                               <div>';
                            $request .= '                                                   <div class="circle_icon">';
                            $request .= '                                                       <i class="material-icons">single_bed</i>';
                            $request .= '                                                   </div>';
                            $request .= '                                               </div>';
                            $request .= '                                               <div>';
                            $request .= '                                                   <p class="text-500 m-0 fs-14">Bedrooms</p>';
                            $request .= '                                                   <p class="m-0 font-weight-bold text-black">'.$struccol['beds_count'].'</p>';
                            $request .= '                                               </div>';
                            $request .= '                                           </div>';
                            $request .= '                                       </div>';
                                                                        }
                                                                        if($struccol['baths'] != null || $struccol['baths'] != ''){
                            $request .= '                                       <div class="col-6 col-md-4 data-block">';
                            $request .= '                                           <div class="d-flex align-items-center">';
                            $request .= '                                               <div>';
                            $request .= '                                                   <div class="circle_icon">';
                            $request .= '                                                       <i class="material-icons">bathtub</i>';
                            $request .= '                                                   </div>';
                            $request .= '                                               </div>';
                            $request .= '                                               <div>';
                            $request .= '                                                   <p class="text-500 m-0 fs-14">Bathrooms</p>';
                            $request .= '                                                   <p class="m-0 font-weight-bold text-black">'.$struccol['baths'].'</p>';
                            $request .= '                                               </div>';
                            $request .= '                                           </div>';
                            $request .= '                                       </div>';
                                                                        }
                                                                        if($struccol['stories'] != null || $struccol['stories'] != ''){
                            $request .= '                                       <div class="col-6 col-md-4 data-block">';
                            $request .= '                                           <div class="d-flex align-items-center">';
                            $request .= '                                               <div>';
                            $request .= '                                                   <div class="circle_icon">';
                            $request .= '                                                       <i class="material-icons">layers</i>';
                            $request .= '                                                   </div>';
                            $request .= '                                               </div>';
                            $request .= '                                               <div>';
                            $request .= '                                                   <p class="text-500 m-0 fs-14">Stories</p>';
                            $request .= '                                                   <p class="m-0 font-weight-bold text-black">'.$struccol['stories'].'</p>';
                            $request .= '                                               </div>';
                            $request .= '                                           </div>';
                            $request .= '                                       </div>';
                                                                        }
                                                                        if($struccol['total_area_sq_ft'] != null || $struccol['total_area_sq_ft'] != ''){
                            $request .= '                                       <div class="col-6 col-md-4 data-block">';
                            $request .= '                                           <div class="d-flex align-items-center">';
                            $request .= '                                               <div>';
                            $request .= '                                                   <div class="circle_icon">';
                            $request .= '                                                       <i class="material-icons">square_foot</i>';
                            $request .= '                                                   </div>';
                            $request .= '                                               </div>';
                            $request .= '                                               <div>';
                            $request .= '                                                   <p class="text-500 m-0 fs-14">Total Size</p>';
                            $request .= '                                                   <p class="m-0 font-weight-bold text-black"> '.$struccol['total_area_sq_ft'].' sq. ft. </p>';
                            $request .= '                                               </div>';
                            $request .= '                                           </div>';
                            $request .= '                                       </div>';
                                                                        }
                                                                        if($struccol['year_built'] != null || $struccol['year_built'] != ''){
                            $request .= '                                       <div class="col-6 col-md-4 data-block">';
                            $request .= '                                           <div class="d-flex align-items-center">';
                            $request .= '                                               <div>';
                            $request .= '                                                   <div class="circle_icon">';
                            $request .= '                                                       <i class="material-icons">event</i>';
                            $request .= '                                                   </div>';
                            $request .= '                                               </div>';
                            $request .= '                                               <div>';
                            $request .= '                                                   <p class="text-500 m-0 fs-14">Year Built</p>';
                            $request .= '                                                   <p class="m-0 font-weight-bold text-black"> '.$struccol['year_built'].' </p>';
                            $request .= '                                               </div>';
                            $request .= '                                           </div>';
                            $request .= '                                       </div>';
                                                                        }
                                                                        if($struccol['air_conditioning_type'] != null || $struccol['air_conditioning_type'] != ''){
                            $request .= '                                       <div class="col-6 col-md-4 data-block">';
                            $request .= '                                           <div class="d-flex align-items-center">';
                            $request .= '                                               <div>';
                            $request .= '                                                   <div class="circle_icon">';
                            $request .= '                                                       <i class="material-icons">ac_unit</i>';
                            $request .= '                                                   </div>';
                            $request .= '                                               </div>';
                            $request .= '                                               <div>';
                            $request .= '                                                   <p class="text-500 m-0 fs-14">Air conditioning</p>';
                            $request .= '                                                   <p class="m-0 font-weight-bold text-black"> '.$struccol['air_conditioning_type'].' </p>';
                            $request .= '                                               </div>';
                            $request .= '                                           </div>';
                            $request .= '                                       </div>';
                                                                        }
                                                                        if($struccol['heating_type'] != null || $struccol['heating_type'] != ''){
                            $request .= '                                       <div class="col-6 col-md-4 data-block">';
                            $request .= '                                           <div class="d-flex align-items-center">';
                            $request .= '                                               <div>';
                            $request .= '                                                   <div class="circle_icon">';
                            $request .= '                                                       <i class="material-icons">fireplace</i>';
                            $request .= '                                                   </div>';
                            $request .= '                                               </div>';
                            $request .= '                                               <div>';
                            $request .= '                                                   <p class="text-500 m-0 fs-14">Heating type</p>';
                            $request .= '                                                      <p class="m-0 font-weight-bold text-black"> '.$struccol['heating_type'].' </p>';
                            $request .= '                                                  </div>';
                            $request .= '                                              </div>';
                            $request .= '                                          </div>';
                                                                        }
                                                                        if($struccol['heating_fuel_type'] != null || $struccol['heating_fuel_type'] != ''){
                            $request .= '                                          <div class="col-6 col-md-4 data-block">';
                            $request .= '                                              <div class="d-flex align-items-center">';
                            $request .= '                                                  <div>';
                            $request .= '                                                      <div class="circle_icon">';
                            $request .= '                                                          <i class="material-icons">offline_bolt</i>';
                            $request .= '                                                      </div>';
                            $request .= '                                                  </div>';
                            $request .= '                                                  <div>';
                            $request .= '                                                      <p class="text-500 m-0 fs-14">Fuel</p>';
                            $request .= '                                                      <p class="m-0 font-weight-bold text-black"> '.$struccol['heating_fuel_type'].' </p>';
                            $request .= '                                                  </div>';
                            $request .= '                                              </div>';
                            $request .= '                                          </div>';
                                                                        }
                                                                        if($struccol['parking_type'] != null || $struccol['parking_type'] != ''){
                            $request .= '                                          <div class="col-6 col-md-4 data-block">';
                            $request .= '                                              <div class="d-flex align-items-center">';
                            $request .= '                                                  <div>';
                            $request .= '                                                      <div class="circle_icon">';
                            $request .= '                                                          <i class="material-icons">drive_eta</i>';
                            $request .= '                                                      </div>';
                            $request .= '                                                  </div>';
                            $request .= '                                                  <div>';
                            $request .= '                                                      <p class="text-500 m-0 fs-14">Parking Type</p>';
                            $request .= '                                                      <p class="m-0 font-weight-bold text-black"> '.$struccol['parking_type'].' </p>';
                            $request .= '                                                  </div>';
                            $request .= '                                              </div>';
                            $request .= '                                          </div>';
                                                                        }
                                                                        if($struccol['condition'] != null || $struccol['condition'] != ''){
                            $request .= '                                       <div class="col-6 col-md-4 data-block">';
                            $request .= '                                          <div class="d-flex align-items-center">';
                            $request .= '                                              <div>';
                            $request .= '                                                  <div class="circle_icon">';
                            $request .= '                                                      <i class="material-icons">home_work</i>';
                            $request .= '                                                  </div>';
                            $request .= '                                              </div>';
                            $request .= '                                              <div>';
                            $request .= '                                                  <p class="text-500 m-0 fs-14">Condition</p>';
                            $request .= '                                                  <p class="m-0 font-weight-bold text-black"> '.$struccol['condition'].' </p>';
                            $request .= '                                              </div>';
                            $request .= '                                          </div>';
                            $request .= '                                      </div>';       
                                                                        }
                            $request .= '                                <div class="col-lg-12">';
                            $request .= '                                    <div class="collapse" id="full-details">';
                            $request .= '                                        <div class="pt-4">';
                            $request .= '                                            <div class="row">';
                                                                        // ADDITIONAL ADDRESS DETAILS
                                                                                    if($addcol['carrier_code'] != null || $addcol['carrier_code'] != ''){
                            $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                            $request .= '                                                    <span class="text-500 d-block fs-13">Carrier Code:</span>';
                            $request .= '                                                    <span class="fs-14">'.$addcol['carrier_code'].'</span>';
                            $request .= '                                                </div>';
                                                                                    }
                                                                                    if($addcol['latitude'] != null || $addcol['latitude'] != ''){
                            $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                            $request .= '                                                    <span class="text-500 d-block fs-13">Latitude:</span>';
                            $request .= '                                                    <span class="fs-14">'.$addcol['latitude'].'</span>';
                            $request .= '                                                </div>';
                                                                                    }
                                                                                    if($addcol['longitude'] != null || $addcol['longitude'] != ''){
                            $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                            $request .= '                                                    <span class="text-500 d-block fs-13">Longitude:</span>';
                            $request .= '                                                    <span class="fs-14">'.$addcol['longitude'].'</span>';
                            $request .= '                                                </div>';
                                                                                    }
                                                                                    if($addcol['geocoding_accuracy'] != null || $addcol['geocoding_accuracy'] != ''){
                            $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                            $request .= '                                                    <span class="text-500 d-block fs-13">Geocoding Accuracy:</span>';
                            $request .= '                                                    <span class="fs-14">'.$addcol['geocoding_accuracy'].'</span>';
                            $request .= '                                                </div>';
                                                                                    }
                                                                                    if($addcol['census_tract'] != null || $addcol['census_tract'] != ''){
                            $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                            $request .= '                                                    <span class="text-500 d-block fs-13">Census Tract:</span>';
                            $request .= '                                                    <span class="fs-14">'.$addcol['census_tract'].'</span>';
                            $request .= '                                                </div>';   
                                                                                    }
                                                                                    
                                                                // PARCEL DATA DETAILS
                                                                                    if($parcol['apn_original'] != null || $parcol['apn_original'] != ''){
                            $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                            $request .= '                                                    <span class="text-500 d-block fs-13">Original APN:</span>';
                            $request .= '                                                    <span class="fs-14">'.$parcol['apn_original'].'</span>';
                            $request .= '                                                </div>';
                                                                                    }
                                                                                    if($parcol['apn_unformatted'] != null || $parcol['apn_unformatted'] != ''){
                            $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                            $request .= '                                                    <span class="text-500 d-block fs-13">Unformatted APN:</span>';
                            $request .= '                                                    <span class="fs-14">'.$parcol['apn_unformatted'].'</span>';
                            $request .= '                                                </div>';
                                                                                    }
                                                                                    if($parcol['fips_code'] != null || $parcol['fips_code'] != ''){
                            $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                            $request .= '                                                    <span class="text-500 d-block fs-13">FIPS Code:</span>';
                            $request .= '                                                    <span class="fs-14">'.$parcol['fips_code'].'</span>';
                            $request .= '                                                </div>';
                                                                                    }
                                                                                    if($parcol['area_sq_ft'] != null || $parcol['area_sq_ft'] != ''){
                            $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                            $request .= '                                                    <span class="text-500 d-block fs-13">Lot Size:</span>';
                            $request .= '                                                    <span class="fs-14">'.$parcol['area_sq_ft'].' sq. ft.</span>';
                            $request .= '                                                </div>';
                                                                                    }
                                                                                    if($parcol['area_acres'] != null || $parcol['area_acres'] != ''){
                            $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                            $request .= '                                                    <span class="text-500 d-block fs-13">Lot Size:</span>';
                            $request .= '                                                    <span class="fs-14">'.$parcol['area_acres'].' acres</span>';
                            $request .= '                                                </div>';
                                                                                    }
                                                                                    if($parcol['county_name'] != null || $parcol['county_name'] != ''){
                            $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                            $request .= '                                                    <span class="text-500 d-block fs-13">County Name:</span>';
                            $request .= '                                                    <span class="fs-14">'.$parcol['county_name'].'</span>';
                            $request .= '                                                </div>';
                                                                                    }
                                                                                    if($parcol['county_land_use_code'] != null || $parcol['county_land_use_code'] != ''){
                            $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                            $request .= '                                                    <span class="text-500 d-block fs-13">County Land Use Code:</span>';
                            $request .= '                                                    <span class="fs-14">'.$parcol['county_land_use_code'].'</span>';
                            $request .= '                                                </div>';
                                                                                    }
                                                                                    if($parcol['county_land_use_description'] != null || $parcol['county_land_use_description'] != ''){
                            $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                            $request .= '                                                    <span class="text-500 d-block fs-13">County Land Use Description:</span>';
                            $request .= '                                                    <span class="fs-14">'.$parcol['county_land_use_description'].'</span>';
                            $request .= '                                                </div>';
                                                                                    }
                                                                                    if($parcol['standardized_land_use_category'] != null || $parcol['standardized_land_use_category'] != ''){
                            $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                            $request .= '                                                    <span class="text-500 d-block fs-13">Standardized Land Use Category:</span>';
                            $request .= '                                                    <span class="fs-14">'.$parcol['standardized_land_use_category'].'</span>';
                            $request .= '                                                </div>';
                                                                                    }
                                                                                    if($parcol['standardized_land_use_type'] != null || $parcol['standardized_land_use_type'] != ''){
                            $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                            $request .= '                                                    <span class="text-500 d-block fs-13">Standardized Land Use Type:</span>';
                            $request .= '                                                    <span class="fs-14">'.$parcol['standardized_land_use_type'].'</span>';
                            $request .= '                                                </div>';
                                                                                    }
                                                                                    if($parcol['lot_number'] != null || $parcol['lot_number'] != ''){
                            $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                            $request .= '                                                    <span class="text-500 d-block fs-13">Lot Number:</span>';
                            $request .= '                                                    <span class="fs-14">'.$parcol['lot_number'].'</span>';
                            $request .= '                                                </div>';
                                                                                    }
                                                                                    if($parcol['subdivision'] != null || $parcol['subdivision'] != ''){
                            $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                            $request .= '                                                    <span class="text-500 d-block fs-13">Subdivision:</span>';
                            $request .= '                                                    <span class="fs-14">'.$parcol['subdivision'].'</span>';
                            $request .= '                                                </div>';
                                                                                    }
                                                                                    if($parcol['municipality'] != null || $parcol['municipality'] != ''){
                            $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                            $request .= '                                                    <span class="text-500 d-block fs-13">Municipality:</span>';
                            $request .= '                                                    <span class="fs-14">'.$parcol['municipality'].'</span>';
                            $request .= '                                                </div>';
                                                                                    }
                                                                                    if($parcol['zoning'] != null || $parcol['zoning'] != ''){
                            $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                            $request .= '                                                    <span class="text-500 d-block fs-13">Zoning:</span>';
                            $request .= '                                                    <span class="fs-14">'.$parcol['zoning'].'</span>';
                            $request .= '                                                </div>';
                                                                                    }
                                                                                    if($parcol['tax_account_number'] != null || $parcol['tax_account_number'] != ''){
                            $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                            $request .= '                                                    <span class="text-500 d-block fs-13">Tax Account Number:</span>';
                            $request .= '                                                    <span class="fs-14">'.$parcol['tax_account_number'].'</span>';
                            $request .= '                                                </div>';
                                                                                    }
                                                                                    if($parcol['section_township_range'] != null || $parcol['section_township_range'] != ''){
                            $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                            $request .= '                                                    <span class="text-500 d-block fs-13">Sec/Twp/Rng:</span>';
                            $request .= '                                                    <span class="fs-14">'.$parcol['section_township_range'].'</span>';
                            $request .= '                                                </div>';
                                                                                    }
                                                                                     
                                                                                    if($parcol['legal_description'] != null || $parcol['legal_description'] != ''){
                            $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                            $request .= '                                                    <span class="text-500 d-block fs-13">Legal Description:</span>';
                            $request .= '                                                    <span class="fs-14">'.$parcol['legal_description'].'</span>';
                            $request .= '                                                </div>';  
                                                                                    }
                                                            // STRUCTURE DATA DETAILS
                                                                                    if($struccol['fireplaces'] != null || $struccol['fireplaces'] != ''){
                            $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                            $request .= '                                                    <span class="text-500 d-block fs-13">Fireplace(s):</span>';
                            $request .= '                                                    <span class="fs-14">'.$struccol['fireplaces'].'</span>';
                            $request .= '                                                </div>'; 
                                                                                    }
                                                                                    if($struccol['effective_year_built'] != null || $struccol['effective_year_built'] != ''){
                            $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                            $request .= '                                                    <span class="text-500 d-block fs-13">Effective Year Built:</span>';
                            $request .= '                                                    <span class="fs-14">'.$struccol['effective_year_built'].'</span>';
                            $request .= '                                                </div>';
                                                                                    }
                                                                                    if($struccol['quality'] != null || $struccol['quality'] != ''){
                            $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                            $request .= '                                                    <span class="text-500 d-block fs-13">Quality:</span>';
                            $request .= '                                                    <span class="fs-14">'.$struccol['quality'].'</span>';
                            $request .= '                                                </div>';
                                                                                    }
                                                                                    if($struccol['parking_spaces_count'] != null || $struccol['parking_spaces_count'] != ''){
                            $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                            $request .= '                                                    <span class="text-500 d-block fs-13">Parking Spaces Count:</span>';
                            $request .= '                                                    <span class="fs-14">'.$struccol['parking_spaces_count'].'</span>';
                            $request .= '                                                </div>';
                                                                                    }
                                                                                    if($struccol['exterior_wall_type'] != null || $struccol['exterior_wall_type'] != ''){
                            $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                            $request .= '                                                    <span class="text-500 d-block fs-13">Exterior Wall Type:</span>';
                            $request .= '                                                    <span class="fs-14">'.$struccol['exterior_wall_type'].'</span>';
                            $request .= '                                                </div>';
                                                                                    }
                                                                                    if($struccol['interior_wall_type'] != null || $struccol['interior_wall_type'] != ''){
                            $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                            $request .= '                                                    <span class="text-500 d-block fs-13">Interior Walls:</span>';
                            $request .= '                                                    <span class="fs-14">'.$struccol['interior_wall_type'].'</span>';
                            $request .= '                                                </div>';
                                                                                    }
                                                                                    if($struccol['flooring_types'] != null || $struccol['flooring_types'] != ''){
                            $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                            $request .= '                                                    <span class="text-500 d-block fs-13">Flooring Type:</span>';
                            $request .= '                                                    <span class="fs-14">'.$struccol['flooring_types'].'</span>';
                            $request .= '                                                </div>';
                                                                                    }
                                                                                    if($struccol['condition'] != null || $struccol['condition'] != ''){
                            $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                            $request .= '                                                    <span class="text-500 d-block fs-13">Condition:</span>';
                            $request .= '                                                    <span class="fs-14">'.$struccol['condition'].'</span>';
                            $request .= '                                                </div>';
                                                                                    }
                                                                                    if($struccol['roof_material_type'] != null || $struccol['roof_material_type'] != ''){
                            $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                            $request .= '                                                    <span class="text-500 d-block fs-13">Roof Material:</span>';
                            $request .= '                                                    <span class="fs-14">'.$struccol['roof_material_type'].'</span>';
                            $request .= '                                                </div>';
                                                                                    }
                                                                                    if($struccol['roof_style_type'] != null || $struccol['roof_style_type'] != ''){
                            $request .= '                                                <div class="col-6 col-sm-4 mb-3">';
                            $request .= '                                                    <span class="text-500 d-block fs-13">Roof Style:</span>';
                            $request .= '                                                    <span class="fs-14">'.$struccol['roof_style_type'].'</span>';
                            $request .= '                                                </div>';
                                                                                    }
                            $request .= '                                            </div>';
                            $request .= '                                        </div>';
                            $request .= '                                    </div>';
                            $request .= '                                </div>';
                            
                            $request .= '                                <div class="col-lg-12">';
                            $request .= '                                    <p class="mb-0 mt-4">';
                            $request .= '                                        <a href="#full-details" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="full-details" id="toggle-more-details">';
                            $request .= '                                            View additional property details <i class="material-icons">arrow_drop_down</i>';
                            $request .= '                                        </a>';
                            $request .= '                                    </p>';
                            $request .= '                                </div>';
                            $request .= '                            </div>';
                            $request .= '                        </div>';
                            
                            
                            $request .= '                    </div>';
                            $request .= '                </div>';
                            $request .= '                </div>';
                                            
                                                    
                            $request .= '                 <div class="bg-gray-200 p-2 pb-0 rounded-lg mb-4">';
                            $request .= '                     <h4 class="fs-16 p-2 m-0">Current Ownership Information</h4>';
                            $request .= '                     <div class="card mb-1">';
                            $request .= '                         <div class="card-body">';
                            $request .= '                             <div class="row">';
                            $request .= '                                 <div class="col-lg-12">';
                            $request .= '                                     <div class="col-md col-lg-6">';
                            $request .= '                                         <div class="d-flex">';
                            $request .= '                                             <div class="pr-4">';
                            $request .= '                                                 <img src="https://estated.com/images/v2/report/owner-avatar.png" class="avatar" alt="owner avatar">';
                            $request .= '                                             </div>';
                            $request .= '                                             <div>';
                            $request .= '                                                 <span class="text-500 d-block fs-13">Owner Name(s):</span>';
                            $request .= '                                                 <span class="fs-14">'.wordwrap($owncol['name'],16,"<br>\n").'</span>';
                            $request .= '                                             </div>';
                            $request .= '                                         </div>';
                            $request .= '                                     </div>';
                            $request .= '                                     <div class="col-md col-lg-6">';
                            $request .= '                                         <hr class="d-md-none">';
                            $request .= '                                         <span class="text-500 d-block fs-13">Mailing Address:</span>';
                            $request .= '                                         <span class="fs-14">'.$owncol['formatted_street_address'].' <br>'.$owncol['city'].', '.$owncol['state'].'  '. $owncol['zip_code'].' <br><small class="fs-12"><strong><em>*Owner is known to occupy subject property</em></strong></small> </span>';
                            $request .= '                                     </div>';
                            $request .= '                                 </div>';
                            $request .= '                             </div>';
                            $request .= '                         </div>';
                            $request .= '                     </div>';
                            $request .= '                 </div>';
                            
                            
                            
                        
                            //                  Taxes 
                            $request .= '                <div class="bg-gray-200 p-2 pb-0 rounded-lg mb-4">';
                            $request .= '                    <h4 class="fs-16 p-2 m-0">Taxes</h4>';
                            $request .= '                    <div class="card">';
                            $request .= '                        <div class="card-body p-0">';
                                                                if($taxcol2 != '[]' || $taxcol2 != ''){
                            $request .= '                           <div class="table-responsive">';
                            $request .= '                                    <table class="table m-0">';
                            $request .= '                                        <thead>';
                            $request .= '                                        <tr class="fs-13">';
                            $request .= '                                            <th class="font-weight-medium text-500">Year</th>';
                            $request .= '                                            <th class="font-weight-medium text-500">Amount</th>';
                            $request .= '                                            <th class="font-weight-medium text-500">Exemptions</th>';
                            $request .= '                                            <th class="font-weight-medium text-500 text-right">Rate Code Area</th>';
                            $request .= '                                        </tr>';
                            $request .= '                                        </thead>';
                            $request .= '                                        <tbody>';
                            
                                                                        foreach($taxcol2 as $tax){
                            $request .= '                                            <tr class="fs-15">';
                            $request .= '                                                <td>'.$tax['year'].'</td>';
                            $request .= '                                                <td class="font-weight-medium"> $ '.number_format($tax['amount']).' </td>';
                            $request .= '                                                <td class="font-weight-medium">'.$tax['amount'].'</td>';
                            $request .= '                                                <td class="font-weight-medium text-right">'.$tax['rate_code_area'].' </td>';
                            $request .= '                                            </tr>';
                                                                        }
                            
                            $request .= '                                        </tbody>';
                            $request .= '                                    </table>';
                            $request .= '                                </div>';
                                                                }else{
                            $request .= '                              <div class="text-center p-4">No Taxex records on file.</div>';
                                                                }
                            $request .= '                        </div>';
                            $request .= '                    </div>';
                            $request .= '                </div>';
                            
                            
                        
                            //                  Assessments 
                            $request .= '                <div class="bg-gray-200 p-2 pb-0 rounded-lg mb-4">';
                            $request .= '                    <h4 class="fs-16 p-2 m-0">Assessments</h4>';
                            $request .= '                    <div class="card">';
                            $request .= '                        <div class="card-body p-0">';
                                                                if($asscol2 != '[]' || $asscol2 != ''){
                            $request .= '                           <div class="table-responsive">';
                            $request .= '                                    <table class="table m-0">';
                            $request .= '                                        <thead>';
                            $request .= '                                        <tr class="fs-13">';
                            $request .= '                                            <th class="font-weight-medium text-500">Year</th>';
                            $request .= '                                            <th class="font-weight-medium text-500">Land value</th>';
                            $request .= '                                            <th class="font-weight-medium text-500">Improvement value</th>';
                            $request .= '                                            <th class="font-weight-medium text-500 text-right">Total value</th>';
                            $request .= '                                        </tr>';
                            $request .= '                                        </thead>';
                            $request .= '                                        <tbody>';
                            
                                                                        foreach($asscol2 as $aco){
                            $request .= '                                           <tr class="fs-15">';
                            $request .= '                                                <td>'.$aco['year'].'</td>';
                            $request .= '                                                <td class="font-weight-medium"> $'.number_format($aco['land_value']).'</td>';
                            $request .= '                                                <td class="font-weight-medium"> $'.number_format($aco['improvement_value']).' </td>';
                            $request .= '                                                <td class="font-weight-medium text-right"> $'.number_format($aco['total_value']).' </td>';
                            $request .= '                                           </tr>';
                                                                        }
                            $request .= '                                        </tbody>';
                            $request .= '                                    </table>';
                            $request .= '                                </div>';
                                                                }else{
                            $request .= '                              <div class="text-center p-4">No Assessments records on file.</div>';
                                                                }
                            $request .= '                           </div>';
                            $request .= '                    </div>';
                            $request .= '                </div>';
                        
                                            //  Market Assessments 
                            $request .= '                <div class="bg-gray-200 p-2 pb-0 rounded-lg mb-4">';
                            $request .= '                    <h4 class="fs-16 p-2 m-0">Market Assessments</h4>';
                            $request .= '                    <div class="card">';
                            $request .= '                        <div class="card-body p-0">';
                                                            if($marasscol2 != '[]' || $marasscol2 != ''){
                            $request .= '                           <div class="table-responsive">';
                            $request .= '                                    <table class="table m-0">';
                            $request .= '                                        <thead>';
                            $request .= '                                        <tr class="fs-13">';
                            $request .= '                                            <th class="font-weight-medium text-500">Year</th>';
                            $request .= '                                            <th class="font-weight-medium text-500">Land value</th>';
                            $request .= '                                            <th class="font-weight-medium text-500">Improvement value</th>';
                            $request .= '                                            <th class="font-weight-medium text-500 text-right">Total value</th>';
                            $request .= '                                        </tr>';
                            $request .= '                                        </thead>';
                            $request .= '                                        <tbody>';
                            
                                                                        foreach($asscol2 as $aco){
                            $request .= '                                           <tr class="fs-15">';
                            $request .= '                                                <td>'.$aco['year'].'</td>';
                            $request .= '                                                <td class="font-weight-medium"> $'.number_format($aco['land_value']).'</td>';
                            $request .= '                                                <td class="font-weight-medium"> $'.number_format($aco['improvement_value']).' </td>';
                            $request .= '                                                <td class="font-weight-medium text-right"> $'.number_format($aco['total_value']).' </td>';
                            $request .= '                                           </tr>';
                                                                        }
                            $request .= '                                        </tbody>';
                            $request .= '                                    </table>';
                            $request .= '                                </div>';
                                                                }else{
                            $request .= '                              <div class="text-center p-4">No market assessment records on file.</div>';
                                                                }
                            $request .= '                        </div>';
                            $request .= '                    </div>';
                            $request .= '                </div>';
                        
                            //                  Deeds 
                            
                                
                            $request .= '                <div class="bg-gray-200 p-2 pb-0 rounded-lg mb-4">';
                            $request .= '                    <h4 class="fs-16 p-2 m-0">Deeds</h4>';
                                if($dedscol2 != '[]' || $dedscol2 != ''){
                                    $num = 1;
                                    foreach($dedscol2 as $deds){
                            $request .= '                            <div class="card mb-1">';
                            $request .= '                                <div class="card-body">';
                            $request .= '                                    <div class="row">';
                            $request .= '                                        <div class="col-lg-12">';
                            $request .= '                                            <div class="col-lg-9">';
                            $request .= '                                                <div class="row">';
                                                                                            if($deds['document_type'] != null || $deds['document_type'] != ''){
                            $request .= '                                                    <div class="col-auto">';
                            $request .= '                                                        <small class="d-block text-500 fs-13">Document Type:</small>';
                            $request .= '                                                        <strong class="fs-15">'.$deds['document_type'].'</strong>';
                            $request .= '                                                    </div>';
                                                                                            }
                                                                                            if($deds['sale_price'] != null || $deds['sale_price'] != ''){
                            $request .= '                                                    <div class="col-auto">';
                            $request .= '                                                       <small class="d-block text-500 fs-13">Sale price:</small>';
                            $request .= '                                                       <strong class="fs-15">$'.$deds['sale_price'].'</strong>';
                            $request .= '                                                    </div>';
                                                                                            }
                                                                                            if($deds['recording_date'] != null || $deds['recording_date'] != ''){
                            $request .= '                                                   <div class="col-auto">';
                            $request .= '                                                        <small class="d-block text-500 fs-13">Recording date:</small>';
                            $request .= '                                                        <strong class="fs-15">'.$deds['recording_date'].'</strong>';
                            $request .= '                                                    </div>';
                                                                                            }
                            $request .= '                                               </div>';
                            $request .= '                                            </div>';
                            $request .= '                                            <div class="col-lg-3 ml-auto text-right">';
                            $request .= '                                                <a data-toggle="collapse" href="#deed-1-'.$num.'" aria-expanded="false" aria-controls="deed-1" class="collapsed">';
                            $request .= '                                                    <i class="material-icons fs-30">expand_more</i>';
                            $request .= '                                                </a>';
                            $request .= '                                            </div>';
                            $request .= '                                        </div>';
                            $request .= '                                    </div>';
                            $request .= '                                </div>';
                                                            
                            $request .= '                                <div class="card-body border-top collapse" id="deed-1-'.$num.'" style="">';
                            $request .= '                                    <div class="row">';
                            $request .= '                                        <div class="col-lg-12">';
                            $request .= '                                            <div class="col-md-6">';
                            $request .= '                                                <h5 class="text-uppercase fs-12 font-weight-medium text-500">Seller Information:</h5>';
                            $request .= '                                                <p>';
                            $request .= '                                                <span class="fs-14">'.$deds['seller_first_name'].' '.$deds['seller_last_name'].' <br> '.$deds['seller2_first_name'].' '.$deds['seller2_last_name'].' </span>';
                            $request .= '                                                    <br>';
                            $request .= '                                                    <small class="text-muted"></small>';
                            $request .= '                                                </p>';
                            $request .= '                                            </div>';
                            $request .= '                                            <div class="col-md-6">';
                            $request .= '                                                <hr class="d-md-none">';
                            $request .= '                                                <h5 class="text-uppercase fs-12 font-weight-medium text-500">Buyer Information:</h5>';
                            $request .= '                                                <p>';
                            $request .= '                                                   <span class="fs-14">'.$deds['buyer_first_name'].' '.$deds['buyer_last_name'].'<br>'.$deds['buyer2_first_name'].' '.$deds['buyer2_last_name'].'</span>';
                            $request .= '                                                    <br>';
                            $request .= '                                                    <small class="text-muted"> '.$deds['buyer_address'].', '.$deds['buyer_city'].', '.$deds['buyer_state'].' '.$deds['buyer_zip_code'].' </small>';
                            $request .= '                                                </p>';
                            $request .= '                                            </div>';
                            $request .= '                                        </div>';
                            $request .= '                                    </div>';
                                
                            $request .= '                                     <div class="col-lg-12">';
                            $request .= '                                         <div class="border-top border-bottom pt-3 pb-2 mb-2">';
                            $request .= '                                             <h5 class="text-uppercase fs-12 font-weight-medium text-500">Lender Information:</h5>';
                            $request .= '                                             <div class="row fs-14">';
                            $request .= '                                                 <div class="col-auto py-2">';
                            $request .= '                                                     <small class="d-block text-500">Loan Amount:</small>';
                                                                                            if($deds['loan_amount'] == null || $deds['loan_amount'] == ''){
                            $request .= '                                                     <span class="text-black"> - </span>';
                                                                                            }else{
                            $request .= '                                                     <span class="text-black">$'.number_format($deds['loan_amount']).'</span>';
                                                                                            }
                            $request .= '                                                 </div>';
                            $request .= '                                                 <div class="col-auto py-2">';
                            $request .= '                                                     <small class="d-block text-500">Lender Type:</small>';
                                                                                            if($deds['lender_type'] == null || $deds['lender_type'] == ''){
                            $request .= '                                                     <span class="text-black">-</span>';
                                                                                            }else{
                            $request .= '                                                     <span class="text-black">'.$deds['lender_type'].'</span>';
                                                                                            }
                            $request .= '                                                 </div>';
                            $request .= '                                                 <div class="col-auto py-2">';
                            $request .= '                                                     <small class="d-block text-500">Loan type: </small>';
                                                                                            if($deds['loan_type'] == null || $deds['loan_type'] == ''){
                            $request .= '                                                     <span class="text-black">-</span>';
                                                                                            }else{
                            $request .= '                                                     <span class="text-black">'.$deds['loan_type'].'</span>';
                                                                                            }
                            $request .= '                                                 </div>';
                            $request .= '                                                 <div class="col-auto py-2">';
                            $request .= '                                                     <small class="d-block text-500">Loan due date:</small>';
                                                                                            if($deds['loan_due_date'] == null || $deds['loan_due_date'] == ''){
                            $request .= '                                                     <span class="text-black">-</span>';
                                                                                            }else{
                            $request .= '                                                     <span class="text-black">'.$deds['loan_due_date'].'</span>';
                                                                                            }
                            $request .= '                                                 </div>';
                            $request .= '                                                 <div class="col-auto py-2">';
                            $request .= '                                                     <small class="d-block text-500">Finance Type:</small>';
                                                                                            if($deds['loan_finance_type'] == null || $deds['loan_finance_type'] == ''){
                            $request .= '                                                     <span class="text-black">-</span>';
                                                                                            }else{
                            $request .= '                                                     <span class="text-black">'.$deds['loan_finance_type'].'</span>';
                                                                                            }
                            $request .= '                                                 </div>';
                            $request .= '                                                 <div class="col-auto py-2">';
                            $request .= '                                                     <small class="d-block text-500">Interest Rate:</small>';
                                                                                            if($deds['loan_interest_rate'] == null || $deds['loan_interest_rate'] == ''){
                            $request .= '                                                     <span class="text-black">-</span>';
                                                                                            }else{
                            $request .= '                                                     <span class="text-black">'.$deds['loan_interest_rate'].'%</span>';
                                                                                            }
                            $request .= '                                                 </div>';
                            $request .= '                                             </div>';
                            $request .= '                                         </div>';
                                    
                            $request .= '                                        <div class="pb-2 mb-3 border-bottom">';
                            $request .= '                                            <div class="row fs-14">';
                            $request .= '                                                <div class="col-auto py-2">';
                            $request .= '                                                    <small class="d-block text-500">Transfer tax:</small>';
                                                                                            if($deds['transfer_tax'] == null || $deds['transfer_tax'] == ''){
                            $request .= '                                                     <span class="text-black">-</span>';
                                                                                            }else{
                            $request .= '                                                     <span class="text-black">$'.number_format($deds['transfer_tax']).'</span>';
                                                                                            }
                            $request .= '                                                </div>';
                            $request .= '                                                <div class="col-auto py-2">';
                            $request .= '                                                    <small class="d-block text-500">Original contract date:</small>';
                                                                                            if($deds['original_contract_date'] == null || $deds['original_contract_date'] == ''){
                            $request .= '                                                     <span class="text-black">-</span>';
                                                                                            }else{
                            $request .= '                                                     <span class="text-black">'.$deds['original_contract_date'].'</span>';
                                                                                            }
                            $request .= '                                                </div>';
                            $request .= '                                                <div class="col-auto py-2">';
                            $request .= '                                                    <small class="d-block text-500">Deed book:</small>';
                                                                                            if($deds['deed_book'] == null || $deds['deed_book'] == ''){
                            $request .= '                                                     <span class="text-black">-</span>';
                                                                                            }else{
                            $request .= '                                                     <span class="text-black">'.$deds['deed_book'].'</span>';
                                                                                            }
                            $request .= '                                                </div>';
                            $request .= '                                                <div class="col-auto py-2">';
                            $request .= '                                                    <small class="d-block text-500">Deed page:</small>';
                                                                                            if($deds['deed_page'] == null || $deds['deed_page'] == ''){
                            $request .= '                                                     <span class="text-black">-</span>';
                                                                                            }else{
                            $request .= '                                                     <span class="text-black">'.$deds['deed_page'].'</span>';
                                                                                            }
                            $request .= '                                                </div>';
                            $request .= '                                                <div class="col-auto py-2">';
                            $request .= '                                                    <small class="d-block text-500">Document ID:</small>';
                                                                                            if($deds['document_id'] == null || $deds['document_id'] == ''){
                            $request .= '                                                     <span class="text-black">-</span>';
                                                                                            }else{
                            $request .= '                                                     <span class="text-black">'.$deds['document_id'].'</span>';
                                                                                            }
                            $request .= '                                                </div>';
                            $request .= '                                            </div>';
                            $request .= '                                        </div>';
                            $request .= '                                        <small class="d-block text-500">Sale price description:</small>';
                                                                            if($deds['sale_price_description'] == null || $deds['sale_price_description'] == ''){
                            $request .= '                                        <p class="fs-14 m-0">No Sale Price Description</p>';
                                                                            }else{
                            $request .= '                                        <p class="fs-14 m-0">'.$deds['sale_price_description'].'</p>';    
                                                                            }
                            
                            $request .= '                                    </div>';
                            $request .= '                                </div>';
                            
                                
                            $request .= '                           </div>';
                                $num ++;
                                    }
                                }else{
                            $request .= '                           <div class="card-body p-0">';
                            $request .= '                              <div class="text-center p-4">No Deeds records on file.</div>';
                            $request .= '                           </div>';
                                }
                                
                            $request .= '                 </div>';
                            
                        
                              $request .=  '</div>';
                              $request .= '<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>';
                              $request .= '<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>'; 
                        
                    }else{
                        $request = "<div>Error</div>";
                    }
                }else{
                    $request = '<div class="p-4 p-md-5 border border-danger bg-danger-light rounded-lg text-center">';
                    $request .= '  <p class="text-danger fs-42">';
                    $request .= '       <svg class="bi bi-question-diamond" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">';
                    $request .= '          <path fill-rule="evenodd" d="M6.95.435c.58-.58 1.52-.58 2.1 0l6.515 6.516c.58.58.58 1.519 0 2.098L9.05 15.565c-.58.58-1.519.58-2.098 0L.435 9.05a1.482 1.482 0 0 1 0-2.098L6.95.435zm1.4.7a.495.495 0 0 0-.7 0L1.134 7.65a.495.495 0 0 0 0 .7l6.516 6.516a.495.495 0 0 0 .7 0l6.516-6.516a.495.495 0 0 0 0-.7L8.35 1.134z"></path>';
                    $request .= '              <path d="M5.25 6.033h1.32c0-.781.458-1.384 1.36-1.384.685 0 1.313.343 1.313 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.007.463h1.307v-.355c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.326 0-2.786.647-2.754 2.533zm1.562 5.516c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"></path>';
                    $request .= '       </svg>';
                    $request .= '  </p>';
                    $request .= '  <h3>We couldnt find that address.</h3>';
                    $request .= '  <p class="m-0 fs-14"> Our database didnt return any results for <strong>'.$newaddress.', '.$newcity.', '.$newstate.', '.$newzip.'</strong>. Please double check youve entered all data correctly, or try a different search method.</p>';
                    $request .= '  <div class="text-center pt-4 d-xl-none">';
                    $request .= '  </div>';
                    $request .= '</div> <br>';
                }



                
                
        //  $request = "<div>Sorry Not Found</div>";
    }
    
    echo $request;
	 exit;
    
}


