<?php
global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

$user_id = $wo['user']['user_id'];
$pro_type = $wo['user']['pro_type'];


$pro_list = array(1,2,3,4);



$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);

if(isset($_POST['action']) && $_POST['action']=="get_lead_details_sms_MD"){
    if($_POST['lead_id'] > 0){
        
        // database table name
        $t_b =  $_POST['t_b'];
        $t_b = constant($t_b);
        
        
        $lead_id = $_POST['lead_id'];
        // select lead details from database
        $leadquery = "SELECT *, COUNT(`id`) as `count` FROM ".$t_b." WHERE `id` = $lead_id";
        $sqllead_code  = mysqli_query($sqlConnect, $leadquery);
        $s_f_l = mysqli_fetch_assoc($sqllead_code);
        
        // get list of my purchased phone numbers
        $my_number_list = mysqli_query($sqlConnect, "SELECT * FROM `purchased_numbers` WHERE user_id = $user_id");
        
        
        
        $myretunleadfound = $s_f_l['count'];
        if($myretunleadfound > 0){
            $tab1 = json_decode($s_f_l["tab1"]);
            $tab3 = unserialize($s_f_l["tab3"]);
            
            if(in_array($pro_type,$pro_list)){
                
            // restriction and counter functionalities 
            $realleadSMS = Wo_get_features_count('broadcast_sms');
            
            
            $mypronametype = Wo_GetUser_Pro_type_name();
            
            
            $getuserprotype = Wo_get_my_pro_type();
            
            
            $leadSMSleft = $realleadSMS;
            if($leadSMSleft == 0 || $leadSMSleft < 0 || Wo_GetUserPackage_status() == 0 || Wo_GetUser_NewPackage_status() == 0){
                $leadSMSleft = '<b>No</b>';
            }
            
        ?> 
        <style>
            /*Message style stye code start here*/

            .ui-input-container {
              background-color: #fff;
              padding: 3rem;
              border-radius: 4px;
              width: 100%;
              margin: 0 auto;
            }
            .ui-input-container h2 {
              font-family: sans-serif;
              margin-bottom: 20px;
              font-weight: 700;
              text-transform: capitalize;
            }
            .ui-form-input-container {
              position: relative;
              font-size: 1.5rem;
              margin-bottom: 15px;
              display: block;
            }
            .ui-form-input {
              padding: 13px 15px;
              border-radius: 8px;
              border: 2px solid #f37934;
              outline: 0;
              width: 100%;
            }
            
            .form-input-label {
              position: absolute;
              top: -7px;
              left: 10px;
              color: #f37934;
              font-size: 0.85rem;
              padding-right: 0.33rem;
              padding-left: 0.33rem;
              background: #fff;
              transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
              font-family: sans-serif;
              text-transform: capitalize;
            }
            
            .ui-form-btn {
              padding: 13px 15px;
              border-radius: 8px;
              background: #1a73e8;
              outline: 0;
              width: 100%;
              border: none;
              cursor: pointer;
              font-size: 1rem;
              color: white;
              font-weight: 500;
            }
            
            .error .ui-form-input,
            .error .form-input-label {
              border-color: #f37934;
              color: #f37934;
            }
            
            textarea {
              min-height: 6em;
              max-height: 50vh;
              width: 100%;
            }
            
            
            /*Message container style code end here*/
        </style>
        <div class="col-lg-12">
            <h3> <b id="remail_limit2"><?= $leadSMSleft; ?></b>SMS Left</h3>
        </div>
        <form method="post" action="" id="form_sms">
            <div class="contact-us-alert2"></div>
            <div class="col-lg-12">
                <label class="tag_field">
        		    <input id="to_phone" name="to_phone" value="<?= $tab1->seller_phone ?>" type="text" readonly required placeholder=" ">
        		    <input type="hidden" value="<?= $lead_id ?>" id="lead_id" name="lead_id" />
        			<span>To</span>
        			<span id="to_phone-info" class="info filedErrors">
        		</label>
        		<label class="tag_field">
        		    <select id="from_phone" name="from_phone" class="">
        		         <?php while($num = mysqli_fetch_assoc($my_number_list) ): ?>
        		            <option value=""></option>
                            <option value="+<?= $num['number'] ?>">+<?= $num['number'] ?></option>
                         <?php endwhile; ?>
        		    </select>
        			<span>From</span>
        			<span id="from_phone-info" class="info filedErrors">
        		</label>
        		<div class="form-group ml-auto mt-4">
                    <div class="ui-input-container">
                      <label class="ui-form-input-container">
                        <textarea name="msg" class="ui-form-input" rows="8" maxlength="160" id="word-count-input"></textarea>
                        <span class="form-input-label">Message</span>
                      </label>
                      <p aria-live="polite"><strong><span id="word-count">0</span> words</strong> | <strong><span id="character-count">0</span> characters</strong></p>
                    </div>
                </div>
                
            </div>
            <div class="text-center" style="margin:0; display:block; left: 50%; margin-bottom: 20px;">
        		<button class="btn btn-main btn-mat btn-mat-raised disable_btn" type="submit" name="send" id="send_sms_btn">Send SMS</button>
        	</div>
        </form>
        <input type="hidden" id="module_limit2">
        <input type="hidden" id="module_limit3">
        
        
        
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script>
        var path = "<?php echo $wo['config']['site_url']; ?>";
        var t_b = "<?= $_POST['t_b'] ?>";
          
          // Word and sentence
            var countTarget = document.querySelector("#word-count-input");
            var wordCount = document.querySelector("#word-count");
            var characterCount = document.querySelector("#character-count");
            var set = 160;
            
            var count = function () {
              var characters = countTarget.value;
              var characterLength = characters.length;
                
              var remain = parseInt(set - characterLength);
              if(remain <= 0){
                swal("Sorry Only 160 characters is allowed", {
                  icon: "warning",
                });
              }
              
             var words = characters.split(/[\n\r\s]+/g).filter(function (word) {
                return word.length > 0;
              }); 
        
              
            
              wordCount.innerHTML = words.length;
              characterCount.innerHTML = characterLength;
            };
            
            count();
            
            window.addEventListener(
              "input",
              function (event) {
                if (event.target.matches("#word-count-input")) {
                  count();
                }
              },
              false
            );
            // End of word and sentence counter 
          
          jQuery("#send_sms_btn").on('click',function(e){
            e.preventDefault();
        
  
             var valid_token = true;
            
         
    	    if(!jQuery("#from_phone").val()) {
    	        jQuery("#from_phone-info").html('<p style="color: red;">Please Select Valid Phone Number starting with +1</p>');
    	        jQuery("#from_phone").css('background-color','#FFFFDF');
    	        valid_token = false;
    	    }
            
            if(valid_token == false){
    	    	return false;
    	    }
	    
        
            var lead_id = $("#lead_id").val();
            var to_phone = $("#to_phone").val();
            var from_phone = $("#from_phone").val();
            var message = $("#word-count-input").val();
            
            $.ajax({
            	  type:"POST",
            	  url:path+"/newsubmit_leadproperty.php",
            	  data: {action: "SendSMSLead", lead_id:lead_id, to_phone:to_phone,from_phone:from_phone,message:message,t_b: t_b},
            	  beforeSend: function() {
            		 $('form#form_sms').find('#send_sms_btn').attr('disabled','disabled').html('<i class="fa fa-spinner fa-spin"></i> Please Wait');
                   },
            	  success: function (data) {
            	   if(data.status == 200){
                            
                        $('.contact-us-alert2').html('<div class="alert alert-success">' + data.message +'</div>');
                        $('.alert-success').fadeIn(300);
                        $('form#form_sms').find('#send_sms_btn').removeAttr("disabled").html('<?php echo "Send SMS"; ?>');
                        
                        
                        check_log_user("log","broadcast_sms",0);	
                        check_log_user("check","broadcast_sms",1); 
                        
            	        var module_limit2 = $("#module_limit2").val();
                 
                        var new_module_limit2 = parseInt(module_limit2)-1;
                	 
                		
                      $("#modal-alert-msg").html("You have "+ new_module_limit2 +" SMS calculation left.");
                      
                      $("#modal-alert").fadeIn(500);
                      setTimeout(
                          function(){ 
                            $("#modal-alert").fadeOut(500);
                            window.location.reload();
                          }, 4000);
                    
                        $('#module_limit2').val(new_module_limit2);
                        document.getElementById("remail_limit2").innerHTML = new_module_limit2;
                        
                        $('html, #SMSLeadModalMD').animate({ scrollTop: 0 }, 0);
                            
                            
                    }else if(data.status == 401){
                        
                        LeadWalletPopUp(0.03,lead_id,to_phone,from_phone,message);
            			$('form#form_sms').find('#send_sms_btn').removeAttr("disabled").html('<?php echo "Send SMS"; ?>');
                    }
                    else{
                        $('html, #SMSLeadModalMD').animate({ scrollTop: 0 }, 0);
                        $('.contact-us-alert2').html('<div class="alert alert-danger">'+data.message+'</div>');
                        $('form#form_sms').find('#send_sms_btn').removeAttr("disabled").html('<?php echo "Send SMS"; ?>');
                        $('.alert-danger').fadeIn(300);
                    }
                        
                }
             });
    
             
    	 
            
        });
        
        
        function LeadWalletPopUp(price,lead_id,to_phone,from_phone,message){
    
             var newwallet  = $("#module_limit3").val();
             if(newwallet >= price){
                swal({
                  title: "$" +price +" Charge Request From Your Wallet. " ,
                  text: "$" +price +" Will Be Charged From Your Wallet Account to Send your SMS.",
                  icon: "warning",
                  buttons: true,
                  dangerMode: true,
              })
              .then((willDelete) => {
                    if (willDelete) {
                        
                        $.ajax({
                            type: 'post',
                            url:path+"/newsubmit_leadproperty.php",
            	            data: {action: "SendSMSLeadWallet", lead_id:lead_id, to_phone:to_phone,from_phone:from_phone,message:message,t_b: t_b},
                            beforeSend: function() {
                                $('.loadingPay').modal('show', {backdrop: 'true'});
                            },
                            success: function(data){
                                $('.loadingPay').modal('hide');
                                if(data.status == 200){
                                    $('#SMSLeadModalMD').modal('hide');
                                    swal({
                                         title: data.message,
                                         text: "Please Wait.......",
                                         type: "success",
                                         timer: 3000
                                    });
                                    
                                    window.setTimeout(function(){ 
                                        location.reload();
                                    } ,3000);
                                     
                                    check_wallet_remain("reduce_wallet",price);
                                    check_wallet_remain("check_wallet",price);
                                    
                                    
                                    
                                }else{
                                    swal(data.message, {
                                      icon: "warning",
                                    });
                                    
                                }
                                
                            }
                        });
                        
                  
                  }else {
                    
                  }
              
              });
            }else{
                $("#belowPrice").text(price);
                $('.mynew').modal('show', {backdrop: 'true'});
                
            }
         }
        
        
        
        function check_log_user(action_post,module_id,number){
            $.ajax({
            	  type:"POST",
            	  url:path+"/check-mypackage.php",
            	  data:{
            		action : action_post,
            		module : module_id,
            		number: number
            	  },
            	  success: function (response) {
            		 $("#module_limit2").val(response);
            		 return response;
            	  }
             });
        }
        
        check_log_user("check","broadcast_sms",0);
        
        
        function check_wallet_remain(action_text,price){
            
            $.ajax({
            	  type:"POST",
            	  url:path+"/reducewallet.php",
            	  data:{action : action_text, price: price},
            	  success: function (response) {
            		 $("#module_limit3").val(response);
            		 return response;
            	  }
             });
        }
        check_wallet_remain("check_wallet",0);
        </script>
        
        <?php
        
        
            }else{ ?>
                
                <h3 style="text-align:center; color: red; font-weight: 500;">Sorry! You do not have access to this feature. Please Upgrade to PRO <a href="/go-pro" style="margin-top: 10px;" class="btn btn-main btn-mat btn-mat-raised disable_btn">Click Here To Upgrade</a></h3>
                
          <?php      
            }
        
        }
        
    }
    
    
}

?>