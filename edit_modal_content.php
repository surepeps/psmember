<?php
global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);

if(isset($_POST['action']) && $_POST['action']=="get_lead_edit_modal"){
    if($_POST['lead_id'] > 0){
        
        // database table name
        $t_b =  $_POST['t_b'];
        $t_b_s =  $_POST['t_b_s'];
        $t_b = constant($t_b);
        
        $lead_id = $_POST['lead_id'];
        // select lead details from database
        $leadquery = "SELECT *, COUNT(`id`) as `count` FROM ".$t_b." WHERE `id` = $lead_id";
        $sqllead_code  = mysqli_query($sqlConnect, $leadquery);
        $s_f_l = mysqli_fetch_array($sqllead_code);
        //$s_f_l = mysqli_fetch_assoc($sqllead_code);
        
        $myretunleadfound = $s_f_l['count'];
        if($myretunleadfound > 0){
            $tab1 = json_decode($s_f_l["tab1"]);
            $tab3 = unserialize($s_f_l["tab3"]);
            
        // $get_stages_all = "SELECT * FROM ".$t_b_s." WHERE status = 1 ORDER BY id ASC ";
        // $all_stages = mysqli_query($sqlConnect,$get_stages_all);
        
        $mystg = Wo_get_features_count($t_b_s);
        $mystage = json_decode($mystg);
        ?>
            
        <form method="post" action="" id="form_tab2" enctype="multipart/form-data">
            <div class="contact-us-alert"></div>
            <div class="col-lg-6">
                <label class="tag_field">
        		    <input id="seller_name2" name="seller_name" value="<?= $tab1->seller_name ?>" type="text" placeholder=" ">
        			<span>Seller Name</span>
        			<span id="seller_name2-info" class="info filedErrors">
        		</label>
        		<label class="tag_field">
        		    <input id="seller_email" value="<?= $tab1->seller_email ?>" name="seller_email" type="email" placeholder=" ">
        			<span>Seller Email</span>
        		</label>
        		<label class="tag_field">
        			<input id="autocomplete2" name="entered_address" value="<?= $tab1->entered_address ?>" placeholder="" onFocus="geolocate()" type="text"/>
        			<input type="hidden" value="<?= $tab1->city ?>" class="field" id="locality" style="width:80%" name="city" />
                    <input type="hidden" value="<?= $tab1->postal_code ?>" class="field" id="postal_code" name="postal_code" />
                    <input type="hidden" value="<?= $tab1->country ?>" class="field" id="country" name="country" />
                    <input type="hidden" id="user_id" name="user_id" value="<?= $s_f_l['user_id'] ?>" />
                    <input type="hidden" name="propid" class="propid_tab2" value="form_tab2">
                    
        			<span><?= $wo["lang"]["address"] ?></span>
        			<span id="prop_address2-info" class="info filedErrors">
        		</label>
        		<label class="tag_field">
                        <select name="beds" id="beds">
        		    <?php for($i = 1; $i < 8; $i++): ?>
        	                <option value="<?= $i ?>" <?= ($i == $tab1->beds ? "selected" : " ") ?> ><?= $i ?></option>
        		    <?php endfor; ?>
        	            </select>
        			<span>Beds</span>
        		</label>
        		<label class="tag_field">
        		    <select name="baths" id="baths">
        		    
        		    <?php for($i = 1; $i < 8; $i++): ?>
        		        
                        <option value="<?= $i ?>" <?= ($i == $tab1->baths ? "selected" : " ") ?> ><?= $i ?></option>
        		    <?php endfor; ?>
                    </select>
        	<span>Baths</span>
        		</label>
        		<label class="tag_field">
        			<input id="Sqft" name="property_size" value="<?= $tab1->property_size ?>" type="text" placeholder=" ">
        			<span>Sqft *</span>
        			<span style="color:red;" id="Sqft_err"></span>
        		</label>
        		<label class="tag_field">
        		    <select name="deal_type" id="deal_type" class="form-control" data-live-search="false" data-live-search-style="begins" data-placeholder="What is your Deal Type?">
        				<option value="Retail" <?= ("Retail" == $tab1->deal_type ? "selected" : " ") ?>>Retail</option>
        				<option value="Fix and Flip" <?= ("Fix and Flip" == $tab1->deal_type ? "selected" : " ") ?>>Fix and Flip </option>
        				<option value="Rent to Hold" <?= ("Rent to Hold" == $tab1->deal_type ? "selected" : " ") ?>>Rent to Hold </option>
        				<option value="Lease Option" <?= ("Lease Option" == $tab1->deal_type ? "selected" : " ") ?>>Lease Option </option>
        				<option value="Lease Purchase" <?= ("Lease Purchase" == $tab1->deal_type ? "selected" : " ") ?>>Lease Purchase </option>
        				<option value="Seller Financing" <?= ("Seller Financing" == $tab1->deal_type ? "selected" : " ") ?>>Seller Financing </option>
        			</select>
        		</label>
        		<label class="tag_field">
        		    <input id="estimated_repairs" name="estimated_repairs" value="<?= $tab1->estimated_repairs ?>" type="text" placeholder=" ">
        			<span>Estimated Repairs</span>
        			<span style="color:red;" id="estimated_repairs_err"></span>
        			
        		</label>
        		<label class="tag_field">
        		       <select name="prop_type" id="prop_type" class="form-control">
        					<option selected="selected" value="">None</option>
        					<option value="1/2 Duplex" <?= ("1/2 Duplex" == $tab1->prop_type ? "selected" : " ") ?> >1/2 Duplex</option>
        					<option value="Apartment" <?= ("Apartment" == $tab1->prop_type ? "selected" : " ") ?> >Apartment</option>
        					<option value="Condo" <?= ("Condo" == $tab1->prop_type ? "selected" : " ") ?> >Condo</option>
        					<option value="Duplex" <?= ("Duplex" == $tab1->prop_type ? "selected" : " ") ?> >Duplex</option>
        					<option value="Land" <?= ("Land" == $tab1->prop_type ? "selected" : " ") ?> >Land</option>
        					<option value="Mobile Homes" <?= ("Mobile Homes" == $tab1->prop_type ? "selected" : " ") ?> >Mobile Homes</option>
        					<option value="Multi Family Home" <?= ("Multi Family Home" == $tab1->prop_type ? "selected" : " ") ?> >Multi Family Home</option>
        					<option value="Single Family Home" <?= ("Single Family Home" == $tab1->prop_type ? "selected" : " ") ?> >Single Family Home</option>
        					<option value="Townhouse" <?= ("Townhouse" == $tab1->prop_type ? "selected" : " ") ?> >Townhouse</option>
        				</select>
        				<span>Property type</span>
        				
        		 </label>
        		
        	</div>
        	<div class="col-lg-6">
        	    <label class="tag_field">
        		    <input id="seller_phone" name="seller_phone" value="<?= $tab1->seller_phone ?>" type="text" placeholder=" ">
        			<span>Seller Phone</span>
        		</label>
        		<label class="tag_field">
        		    <select name="occupancy" id="occupancy">
        		        <option value=""></option>
        		        <option value="Hot" <?= ("Hot" == $tab1->occupancy ? "selected" : " ") ?> >Occupied</option>
        		        <option value="Rented" <?= ("Rented" == $tab1->occupancy ? "selected" : " ") ?> >Rented</option>
        		        <option value="Vacant" <?= ("Vacant" == $tab1->occupancy ? "selected" : " ") ?> >Vacant</option>
        		        <option value="N/A" <?= ("N/A" == $tab1->occupancy ? "selected" : " ") ?> >N/A</option>
        		    </select>
        			<span>Occupancy</span>
        		</label>
        		<label class="tag_field">
        		    <select name="lead_temp" id="lead_temp">
        		        <option value=""></option>
        		        <option value="Hot Lead" <?= ("Hot Lead" == $tab1->lead_temp ? "selected" : " ") ?> >Hot Lead</option>
        		        <option value="Warm Lead" <?= ("Warm Lead" == $tab1->lead_temp ? "selected" : " ") ?> >Warm Lead</option>
        		        <option value="Cold Lead" <?= ("Cold Lead" == $tab1->lead_temp ? "selected" : " ") ?> >Cold Lead</option>
        		    </select>
        			<span>Lead Temperature</span>
        		</label>
        		<label class="tag_field">
        			<input id="year_built" maxlength="4" value="<?= $tab1->constructions_year ?>"  id="pin" pattern="\d{4}" required name="constructions_year" type="text" placeholder=" ">
        			<span>Year Built</span>
        			<span style="color:red;" id="year_built_err"></span>
        		</label>
        		<label class="tag_field">
        			<input id="month_rent" value="<?= $tab1->month_rent ?>" name="month_rent" type="text" placeholder=" ">
        			<span>Monthly Rent?</span>
        			<span style="color:red;" id="month_rent_err"></span>
        		</label>
        		<label class="tag_field">
        			<input id="arv_amount" name="arv_amount" value="<?= $tab1->arv_amount ?>" type="text" placeholder=" ">
        			<span>ARV Amount</span>
        			<span style="color:red;" id="arv_amount_err"></span>
        			
        			
        			<!--Property hidden form fields-->
        			<input id="flip_price"  value="0" name="flip_price" type="hidden"/>
        			<input id="flip_arv"  value="0" name="flip_arv" type="hidden"/>
        			<input id="flip_ext_repair" value="0" name="flip_ext_repair" type="hidden"/>
        			<input id="rental_price" value="0" name="rental_price" type="hidden"/>
        			<input id="rental_arv" value="0" name="rental_arv" type="hidden"/>
        			<input id="rental_ext_rent" value="0" name="rental_ext_rent" type="hidden"/>
        			<input id="listing_title" name="listing_title" type="hidden"/>
        			<input id="allow_promotion" name="allow_promotion" type="hidden"/>
        			<input id="promotion_note" name="promotion_note" type="hidden"/>
        			<input id="video_link" name="video_link" type="hidden"/>
        			<input id="visibility" name="visibility" type="hidden"/>
        			<input id="property_id" type="hidden" value="<?= $s_f_l['id'] ?>" />
        			
        		</label>
        		<label class="tag_field">
        		    <select name="stage" id="stage">
        		        <?php foreach($mystage as $key => $value): ?>
        		            <option value="<?= $key ?>" <?= ($key == $s_f_l['crm_stage_id'] ? "selected" : " ") ?> > <?= $value ?></option>
        		        <?php endforeach; ?>
        		    
                    </select>
        			<span>Lead Stage</span>
        		</label>
        		<label class="tag_field">
        		    <input id="a_o_a" name="a_o_a" value="<?= $tab1->a_o_a ?>" type="text" placeholder=" ">
        			<span>Accepted Offer Amount</span>
        			<span style="color:red;" id="a_o_a_err"></span>
        		</label>
        		<label class="tag_field">
        		    <select name="offer_status" id="offer_status">
        		        <option value=""></option>
        		        <option value="No Contact" <?= ("No Contact" == $tab1->offer_status ? "selected" : " ") ?> >No Contact</option>
        		        <option value="Deal Analyzing" <?= ("Deal Analyzing" == $tab1->offer_status ? "selected" : " ") ?> >Deal Analyzing</option>
        		        <option value="Made Offer" <?= ("Made Offer" == $tab1->offer_status ? "selected" : " ") ?> >Made Offer</option>
        		        <option value="Offer Negotiation" <?= ("Offer Negotiation" == $tab1->offer_status ? "selected" : " ") ?> >Offer Negotiation</option>
        		        <option value="Offer/Accepted" <?= ("Offer/Accepted" == $tab1->offer_status ? "selected" : " ") ?> >Offer/Accepted</option>
        		        <option value="Offer/Rejected" <?= ("Offer/Rejected" == $tab1->offer_status ? "selected" : " ") ?> >Offer/Rejected</option>
        		        <option value="Pending Contract" <?= ("Pending Contract" == $tab1->offer_status ? "selected" : " ") ?> >Pending Contract</option>
        		        <option value="Made Offer/ Follow-up" <?= ("Made Offer/ Follow-up" == $tab1->offer_status ? "selected" : " ") ?> >Made Offer/ Follow-up</option>
        		        <option value="Under Contract" <?= ("Under Contract" == $tab1->offer_status ? "selected" : " ") ?> >Under Contract</option>
        		        <option value="Follow up" <?= ("Follow up" == $tab1->offer_status ? "selected" : " ") ?> >Follow up</option>
        		        <option value="Dead" <?= ("Dead" == $tab1->offer_status ? "selected" : " ") ?> >Dead</option>
        		    </select>
        			<span>Offer Status</span>
        		</label>
        		
        	</div>
        	<div class="col-lg-12">
        	    <label>Image</label> (First image will be featured image. Sort images by dragging them)
        		<div id="mydropzone2" class="dropzone dz-clickable image"></div>
        		<br>
        	</div>
        	<div class="col-lg-12">
        	    <label class="tag_field">
        	        <textarea id="deal_note" name="about_property" row="4"><?= $s_f_l['description'] ?></textarea>
        			<span>Deal Note</span>
        		</label>
        	</div>
        	<div class="text-center" style="margin:0; display:block; left: 50%; margin-bottom: 20px;">
        		<button class="btn btn-main btn-mat btn-mat-raised disable_btn" type="submit" name="send" id="submit_edit_lead_btn">Edit Lead</button>
        	</div>
        </form>
        
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCaKk9lOoS2QXFpxovEoX3lxWwT-poxTz0&libraries=places" async defer></script>
        <script type="text/javascript">
            var t_b = "<?= $_POST['t_b'] ?>";
            var placeSearch, autocomplete2;

                var componentForm = {
                  locality: 'long_name',
                  country: 'long_name',
                  postal_code: 'short_name'
                };
                
                function initAutocomplete() {
                  // Create the autocomplete object, restricting the search predictions to
                  // geographical location types.
                  autocomplete2 = new google.maps.places.Autocomplete(
                      document.getElementById('autocomplete2'), { types: [ 'geocode' ] });
                
                  // Avoid paying for data that you don't need by restricting the set of
                  // place fields that are returned to just the address components.
                  autocomplete2.setFields(['address_component']);
                
                  // When the user selects an address from the drop-down, populate the
                  // address fields in the form.
                  autocomplete2.addListener('place_changed', fillInAddress2);
                  
                }
                
                
                function fillInAddress2(){
                // Get the place details from the autocomplete object.
                  var place = autocomplete2.getPlace();
                
                  for (var component in componentForm) {
                    document.getElementById(component).value = '';
                    document.getElementById(component).disabled = false;
                  }
                
                  // Get each component of the address from the place details,
                  // and then fill-in the corresponding field on the form.
                  for (var i = 0; i < place.address_components.length; i++) {
                    var addressType = place.address_components[i].types[0];
                    if (componentForm[addressType]) {
                      var val = place.address_components[i][componentForm[addressType]];
                      document.getElementById(addressType).value = val;
                	  if(addressType=="locality"){var str_locality = val;}
                    }
                  }
                
                }
                  
                function geolocate() {
                  if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                      var geolocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                      };
                      var circle = new google.maps.Circle(
                          {center: geolocation, radius: position.coords.accuracy});
                      autocomplete2.setBounds(circle.getBounds());
                      //autocomplete1.setBounds(circle.getBounds());
                    });
                  }
                }
            
        	$(document).ready(function(){
        	    $("#seller_name2").on('keyup', function(){
            	    var seller_name3 = jQuery("#seller_name2").val();
            	    if(seller_name3 != ''){
            	    	jQuery("#seller_name2-info").html("");
            	    }
            	    else{
            	    	jQuery("#seller_name2-info").html('<p style="color: red;">Please Enter Seller Name</p>');
            	        jQuery("#seller_name2").css('background-color','#FFFFDF');
            	    }
            	});
            	
            	$("#autocomplete2").on('keyup', function(){
            	    var autocomplete3 = jQuery("#autocomplete2").val();
            	    if(autocomplete3 != ''){
            	    	jQuery("#prop_address2-info").html("");
            	    }
            	    else{
            	    	jQuery("#prop_address2-info").html('<p style="color: red;">Please Enter Property Valid Address</p>');
            	        jQuery("#autocomplete2").css('background-color','#FFFFDF');
            	    }
            	});
            		<?php  
                    
            	if(isset($tab3)): 
            		foreach ($tab3 as $key => $value) { 
            	    $size = filesize($wo['config']['site_url'].'/themes/wondertag/uploads_images/'.$value);
            	    $urlImg = $wo['config']['site_url'].'/themes/wondertag/uploads_images/'.$value;
                ?> 
                
                    previewThumbailFromUrl({
                        selector: 'image',
                        fileName: '<?php echo $value; ?>',
                        imageURL: "<?= $urlImg ?>"
                    });
                    
                    function previewThumbailFromUrl(opts) {
                        var imgDropzone = Dropzone.forElement("." + opts.selector);
                        var mockFile = {
                            name: opts.fileName,
                            size: 12345,
                            accepted: true,
                            kind: 'image'
                        };
                        imgDropzone.emit("addedfile", mockFile);
                        imgDropzone.files.push(mockFile);
                        imgDropzone.emit("thumbnail", mockFile, opts.imageURL);
                        $('.dz-preview').addClass('dz-complete');
                       
                        $('.dz-image').last().find('img').attr({width: '120px', height: '120px'});
        
                        $('.dz-image').css({"width":"120px", "height":"120px"});
                
                    }
        
        		<?php } ?>	
        		<?php endif; ?>
        
        		 var imgDropzone = Dropzone.forElement(".image");
        		 imgDropzone.on("removedfile", function (file) { 
        		     var prop_id = jQuery("#property_id").val();
                      var user_n_id = jQuery("#user_id").val();
                      
                      var name = file.name;
                      
                      $.ajax({
                            type: "POST",
                            url: "<?php glob($_SERVER["DOCUMENT_ROOT"])?>/leadupload.php",
                            data: {
                                action: "update_delete_file",
                                fileName: name,
                                prop_id: prop_id,
                                user_id: user_n_id,
                                t_b: t_b,
                                delete_file: 1
                            }
                        });
        
        		 });
        	});
        	
        	
        	Dropzone.autoDiscover = false;
            $("div#mydropzone2").dropzone({
                    addRemoveLinks: true,
                    autoProcessQueue: true,
                    parallelUploads: 50,
                    maxFilesize: 2, // MB
                    acceptedFiles: ".png, .jpeg, .jpg, .gif",
                    thumbnailWidth: null,
                    thumbnailHeight: null,
            	    url: "<?php glob($_SERVER["DOCUMENT_ROOT"])?>/leadupload.php",
            	    init: function() {
                    dzClosure = this; // Makes sure that 'this' is understood inside the functions below.
                    
                    //send all the form data along with the files:
                    this.on("sending", function(data, xhr, formData) {
                        formData.append("property_id", jQuery("#property_id").val());
                        formData.append("user_id", jQuery("#user_id").val());
                        formData.append("t_b", t_b);
                        formData.append("action", "Update_img");
                    });
                }
            });
            
            $(function() {
                $(".dropzone").sortable({
                    items:'.dz-preview',
                    cursor: 'move',
                    opacity: 0.5,
                    containment: '.dropzone',
                    distance: 20,
                    tolerance: 'pointer',
                    update: function(e, ui){
                    
                        
                        var filenames = [];
                
                        $('.dz-preview .dz-filename').each(function() {
                          filenames.push($(this).find('span').text());
                        });
                        
                        var prop_id = jQuery("#property_id").val();
                        var user_n_id = jQuery("#user_id").val();
                        
                        $.ajax({
                            data: {action:"update_sort_file",filenames: filenames, user_id: user_n_id, prop_id: prop_id, t_b: t_b},
                            type: 'POST',
                            url: "<?php glob($_SERVER["DOCUMENT_ROOT"])?>/leadupload.php"
                        });
                            
                
                    },
            	    
            	    
                });
            
            });
            
            
            jQuery("#submit_edit_lead_btn").on('click',function(e){
                e.preventDefault();
                
                
                var formid = jQuery(".propid_tab2").val();
            	var user_id = jQuery("#user_id").val();
            	var tab2 = jQuery("#"+formid).serialize();
            	var deal_note = jQuery("#deal_note").val();
            	var property_id = jQuery("#property_id").val();
                var stage = $("#stage").val();
        
                 var valid_token = true;
                
                if(!jQuery("#seller_name2").val()) {
        	        jQuery("#seller_name2-info").html('<p style="color: red;">Please Enter Seller Name</p>');
        	        jQuery("#seller_name2").css('background-color','#FFFFDF');
        	        valid_token = false;
        	    }
        	    
        	    if(!jQuery("#autocomplete2").val()) {
        	        jQuery("#prop_address2-info").html('<p style="color: red;">Please Enter Property Valid Address</p>');
        	        jQuery("#autocomplete2").css('background-color','#FFFFDF');
        	        valid_token = false;
        	    }
                
                if(valid_token == false){
        	    	return false;
        	    }
        	  
            	var form_data = new FormData();                   
            	form_data.append("user_id", user_id);
            	form_data.append("form_data", tab2);
            	form_data.append("about_property", deal_note);
                form_data.append("stage",stage);
                form_data.append("action","edit_lead_edit_modal");
                form_data.append("property_id", property_id);
                form_data.append("t_b", t_b);
            
            
                $.ajax({
                    type:"POST",
                   url: path + "/newsubmit_leadproperty.php",
                   data:form_data,
                   cache: false,
                   contentType : false, // you can also use multipart/form-data replace of false
                   processData: false,
                   beforeSend: function() {
            		 $('form#form_tab2').find('#submit_edit_lead_btn').attr('disabled','disabled').html('<i class="fa fa-spinner fa-spin"></i> Please Wait');
                   },
            
                    success: function (data) {
                        if(data.status == 200){
                            
                            $('.contact-us-alert').html('<div class="alert alert-success">' + data.message +'</div>');
                            $('.alert-success').fadeIn(300);
                            
                            $('form#form_tab2').find('#submit_edit_lead_btn').removeAttr("disabled").html('<?php echo "Edit Lead"; ?>');
                            
                          setTimeout(
                              function(){ 
                                  window.location.reload();
                              }, 3000);
                        
                            
                            $('html, body').animate({ scrollTop: 0 }, 0);
                            
                        }
                        else{
                            $('html, body').animate({ scrollTop: 0 }, 0);
                            $('.contact-us-alert').html('<div class="alert alert-danger">' + errors + '</div>');
                            $('form#form_tab2').find('#submit_edit_lead_btn').removeAttr("disabled").html('<?php echo "Edit Lead"; ?>');
                            $('.alert-danger').fadeIn(300);
                        }
                        
                    }
                 });
                 
        	 
                
            });
        
        </script>
            
       <?php  }else{ ?>
            
            <h3 style="color: red; text-align: center;">Data Not Found to edit</h3>
            
      <?php  } 
    }else{ ?>
        
        <h3 style="color: red; text-align: center;">No Lead Value to Edit</h3>
    <?php }
    
}



?>

