<?php
    
    // Setting Global values
    global $wo, $sqlConnect;
    $root=$_SERVER['DOCUMENT_ROOT'];
    
    // Set all required files 
    require_once($root.'/config.php');
    require_once('assets/init.php');
    
    // database setter
    $sqlConnect = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);
    
    // SendGrid Configuration
    // require_once('sendgrid-php/sendgrid-php.php');
    // use SendGrid\Mail\Personalization;
    // use SendGrid\Mail\To;
    
    
    // 
    // 
    // 
    // 
    // GET AVAILABLE CRON JOBS
    // 
    // 
    // 
    // 
    // 
    
    // $criteria_cron_job = Get_NewPropertyForCriteria_CJ();
    
    
    // 
    // 
    // CRITERIA CRON JOB (ON USERS AND ADMIN)
    // 
    // 
    
    // foreach($criteria_cron_job as $ccj){
        
    //     if( 2>1 ){
            
            
    //         // Get each property data
    //         $propData = getSinglePropertyBy_id($ccj['cron_id']);
            
    //         // Split out the Columns in the Listings Database table
    //         $tab1 = json_decode($propData["tab1"]);
    //         $tab2 = json_decode($propData["tab2"]);
    //         $tab3 = json_decode($propData["tab3"]);
    //         $tab4 = json_decode($propData["tab4"]);
    //         $tab5 = json_decode($propData["tab5"]);
    //         $tab6 = unserialize($propData["tab6"]);
    //         $tab7 = unserialize($propData["tab7"]);
            
    //         // Author/Owner Data
    //         $user_id = $propData['user_id'];
    //         $author_d = Wo_UserData($user_id);
    //         $autho_name = $author_d['name'];
    //         $author_avat = $author_d['avatar'];
    //         $autho_uname = $author_d['username'];
    //         $autho_phone = $author_d['phone_number'];
    //         $autho_email = $author_d['email'];
            
    //         $deal_site = GetDeal_site_details($user_id);
    //         $ownerPage = "https://".$deal_site['new_domain'].".".$wo['config']['siteName'].".com";
            
    //         if($deal_site['logo'] == ""){
    //             $Companylogo = $wo['config']['site_url']."/themes/wondertag/img/logo2.png";
    //         }else{
    //             $Companylogo = $wo['config']['site_url']."/".$deal_site['logo'];
    //         }
            
    //         //Lat and Long
    //         $lat = $propData['lat'];
    //         $lang = $propData['lang'];
            
            
    //         // property description
    //         $description = $propData['description'];
            
    //         // Property Id
    //         $prop_id = $propData['id'];
            
    //         // Property date
    //         $prop_date = $propData['dtae_time'];
            
    //         //Image
    //         $server = $wo['config']['site_url'].'/themes/wondertag/uploads_images/'.$tab6[0];
    //         $default_img = $wo['config']['site_url']."/upload/photos/d-property.jpg";
            
    //         // Property Criteria Values (Tab 1)
    //         $prop_year_built = $tab1->constructions_year;
    //         $title = $tab1->listing_title;
    //         $price = $tab1->flip_price;
    //         $price_arv = $tab1->flip_arv;
    //         $beds = $tab1->beds;
    //         $baths = $tab1->baths;
    //         $size = $tab1->property_size;
    //         $prop_type = $tab1->prop_type;
    //         $postal_code = $tab1->postal_code;
    //         $deal_type = $tab1->deal_type;
    //         $country = $tab1->country;
    //         $state = $tab1->state;
    //         $city = $tab1->city;
    //         $area = $tab1->city_r;
    //         $contact_per = $tab1->contact_per;
    //         $video_link = $tab1->video_link;
    //         $address = $tab1->entered_address;
        
    //         $visibility = $tab1->visibility;
        
    //         $hide_address = $tab1->hide_address;
        
    //         $buyNowBTN = $tab1->allow_buynow;
            
    //         // Well structures money 
    //         $n_price = "$".number_format($price);
            
    //         if($baths){
    //             $sqlBath = " (`bath` LIKE '%".$baths."%') "; 
    //         }
            
    //         if($beds){
    //             $sqlBed = " OR (`beds` LIKE '%".$beds."%') "; 
    //         }
            
    //         if($area){
    //             $sqlCity = " OR `city` LIKE '%".$area."%' ";
    //         }
            
    //         if($prop_type){
    //             $sqlPType = " OR `property_type` LIKE '%".$prop_type."%' ";
    //         }
            
    //         if($deal_type){
    //             $sqlDType = " OR `buying_strategy` LIKE '%".$deal_type."%' ";
    //         }
            
    //         if($price){
    //             $sqlPrice = " OR (min_price <= $price AND max_price >= $price)";
    //         }
            
            
    //         $opb = "(";
    //         $clb = ")";
            
    //         if( $ccj['user_type'] == "admin"){
                
    //             $andUser = "AND `reg_buyer_id` > 0 AND `type` = 2";
    //             $new_stage_url_d = $wo['config']['site_url']."/property/".$prop_id;
                
    //             $internal_msg = "Hello, Your Criteria matches this Property (<b>".$title."</b>) Click <a href='".$new_stage_url_d."'here to view the property details";
                
    //             $close_sql = "SELECT * FROM `contact` WHERE ".$opb." ".$sqlBath." ".$sqlBed." ".$sqlCity." ".$sqlPType." ".$sqlDType." ".$sqlPrice." ".$clb." ".$andUser;
            
    //             $result2 = mysqli_query($sqlConnect,$close_sql);
    //       	    $srows_count2 =  mysqli_num_rows($result2);
          	    
    //       	    if($srows_count2 > 0){
      	            
    //   	            while($udata = mysqli_fetch_assoc($result2)){
    //   	                $userData_id[] = $udata['reg_buyer_id'];
    //   	            }
      	            
      	            
    //   	            foreach($userData_id as $recieverId){
      	                
    //   	                $Notificationquery = mysqli_query($sqlConnect,"INSERT INTO `Wo_Notifications` (notifier_id,recipient_id,type,type2,text,url,time) VALUES('$user_id','$recieverId','created_request','-','Your Criteria matches a Property	','".$wo['config']['site_url']."/messages/".$user_id."','".time()."')");
      	                
    //   	                $messages = Wo_RegisterMessage(array(
    //                         'from_id' => Wo_Secure($user_id),
    //                         'to_id' => Wo_Secure($recieverId),
    //                         'text' => Wo_Secure($internal_msg),
    //                         'time' => time()
    //                     ));
                        
    //                     if($messages){
                            
    //                         $data = array(
    //                             'status' => 200,
    //                             'message' => "Matched Criteria Mail Sent"
    //                         );
                            
    //                     }else{
                            
    //                         $data = array(
    //                             'status' => 400,
    //                             'message' => "Error Message Could Not send"
    //                         );
                            
    //                     }
    //   	            }
      	                
      	                
    //   	        }else{
      	            
    //   	            $data = array(
    //                     'status' => 400,
    //                     'message' => "Nothing To send out at (ADMIN END)"
    //                 );
    //   	        }
          	    
          	    
      	    
    //         }else{
                
    //             $andUser = "AND `contactinsertedby` = $user_id AND `send_to_buyer` = 1 AND `type` = 2";
                
    //             // Property Owner Deal Site link   
    //   	        $new_stage_url_d = $ownerPage."/property/".$prop_id;
      	        
    //   	        $close_sql = "SELECT * FROM `contact` WHERE ".$opb." ".$sqlBath." ".$sqlBed." ".$sqlCity." ".$sqlPType." ".$sqlDType." ".$sqlPrice." ".$clb." ".$andUser;
            
    //             $result2 = mysqli_query($sqlConnect,$close_sql);
    //       	    $srows_count2 =  mysqli_num_rows($result2);
      	        
    //   	        if($srows_count2 > 0){
      	            
    //   	            while($Bemails = mysqli_fetch_assoc($result2)){
    //       	            $allEmails[] = $Bemails['email'];
    //       	        }
      	            
    //       	        if ($wo['config']['emailNotification'] == 1) {
          	            
    //       	            $subject = "Your Criteria matches ".$title;
          	            
    //       	            // template variables
    //                     $wo['matchedNoti']['title'] = $title;
    //                     $wo['matchedNoti']['company_logo'] = $Companylogo;
    //                     $wo['matchedNoti']['image'] = !empty($tab6) ? $server : $default_img;
    //                     $wo['matchedNoti']['bed'] = $beds;
    //                     $wo['matchedNoti']['baths'] = $baths;
    //                     $wo['matchedNoti']['property_address'] = $address;
    //                     $wo['matchedNoti']['sqft']  = $size;
    //                     $wo['matchedNoti']['url_d'] = $new_stage_url_d;
    //                     $wo['matchedNoti']['asking_price'] = $price;
    //                     $wo['matchedNoti']['arv_price'] = $flip_arv;
    //                     $wo['matchedNoti']['description'] = $description;
    //                     $wo['matchedNoti']['seller'] = $autho_name;
    //                     $wo['matchedNoti']['city_r'] = $area;
    //                     $wo['matchedNoti']['prop_type'] = $prop_type;
    //                     $wo['matchedNoti']['subject'] = $subject;
                        
    //                     $Send_Email = "c_m@".$wo['config']['siteName'].".com";
    //                     $mybname = $wo['config']['siteName'];
                        
    //                     foreach ( $allEmails as $email_address ) {
        
    //                         $personalization = new Personalization();
                            
    //                         $personalization->addTo( new To( $email_address ) );
                            
    //                         $sendgridPersonalization[] = $personalization;
                            
    //                     }
                        
    //                     $chunkedUsers = array_chunk($sendgridPersonalization, 1000, true);
                        
    //                     $template = "matched_property";
    //                     $message_body = Wo_LoadPage('emails/'.$template);
    //                     $bodyMessage = mysqli_real_escape_string($sqlConnect, $message_body);
                        
                        
    //                     foreach ($chunkedUsers as $singleChunk) {
                        
    //                         $email = new \SendGrid\Mail\Mail(); 
    //                         $email->setfrom($Send_Email, $mybname);
    //                         $email->setSubject($subject);
    //                         $testing = $wo['config']['siteEmail'];
    //                         $email->addto($testing, $wo['config']['siteName']);
                            
    //                         $email->addContent("text/html", $message_body);
    //                         $sendgrid = new \SendGrid('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');
                            
    //                         foreach ( $singleChunk as $personalization_r ) {
    //                             $email->addPersonalization( $personalization_r );
    //                         }
                            
                            
    //                         try {
    //                             $response = $sendgrid->send($email);
                                
    //                             updateCron_Criteria($ccj['cron_id']);
                                
    //                             $data = array(
    //                                 'status' => 200,
    //                                 'message' => "Matched Criteria Mail Sent"
    //                             );
                                
    //                         } catch (Exception $e) {
                                
    //                             $data = array(
    //                                 'status' => 400,
    //                                 'message' => "Error" .$response->statusCode(). "Mail Could Not send"
    //                             );
                                
    //                         }
                            
                                
                        
    //                     }
                        
                        
          	            
    //       	        }else{
          	            
    //       	            $data = array(
    //                         'status' => 400,
    //                         'message' => "Notification Not allowed"
    //                     );
          	            
    //       	        }
          	       
    //             }else{
          	        
    //       	        $data = array(
    //                     'status' => 400,
    //                     'message' => "Nothing To send out at (USER END)"
    //                 );
          	        
    //       	    }
      	        
      	        
                
    //         }
            
            
            
      	    
      	    
           	        
      	        
      	        
    
      	        
      	        
    //   	     //   print_r($allEmails);
    //   	     //   echo "<br><br><br>";
    //   	     //   echo "Total Numbers ($srows_count2) of Users That Matches the criterials as <br>";
    //   	     //   echo " <li>Bed: ".$beds."</li><br>";
    //   	     //   echo " <li>Bath: ".$baths."</li><br>";
    //   	     //   echo " <li>city: ".$area."</li><br>";
    //   	     //   echo " <li>Property Type: ".$prop_type."</li><br>";
    //   	     //   echo " <li>Deal Type: ".$deal_type."</li><br>";
    //   	     //   echo " <li> Min Price ".$price."</li><br>";
    //   	     //   echo " <li> Max Price ".$price."</li><br>";
    //   	     //   echo "<br><br><br>";
      	        
    //   	     //   print_r($deal_site);
      	        
      	        
      	    
            
            
    //     }else{
            
    //         $data = array(
    //             'status' => 500,
    //             'message' => "Timed Out"
    //         );
            
            
            
    //     }
        
       	        
        
    // }


    // header("Content-type: application/json");
    // echo json_encode($data);
    // die;
    
    
    
    // 
    // 
    // 
    // KEYWORD CRON JOB
    // 
    // 
    // 

     runKeywordCronJobs();
     
     
     
    //  
    // 
    // 
    // CAMPAING PIPELINE CRON JOB
    // 
    // 
    // 
    // 

    campaignPipelineCronJob();
    
    
    // 
    // 
    // CRON TESTER
    // 
    // 
    // 
    
    // $owner = 135;
    // $from = "4072509955";
    // $pipeId = 89;
    
    // $check = ProcessNumberAndMove($owner,$from,$pipeId);
    // if($check){
    //     echo "Yes";
    // }else{
    //     echo "No";
    // }

?>