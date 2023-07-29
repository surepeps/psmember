<?php
global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

$user_id = $wo['user']['user_id'];
$pro_type = $wo['user']['pro_type'];
$email = $wo['user']['email'];

$pro_list = array(1,2,3,4);



$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);

if(isset($_POST['action']) && $_POST['action']=="get_lead_details_email_MD"){
    if($_POST['lead_id'] > 0){
        
        // database table name
        $t_b =  $_POST['t_b'];
        $t_b = constant($t_b);
        
        $lead_id = $_POST['lead_id'];
        // select lead details from database
        $leadquery = "SELECT *, COUNT(`id`) as `count` FROM ".$t_b." WHERE `id` = $lead_id";
        $sqllead_code  = mysqli_query($sqlConnect, $leadquery);
        $s_f_l = mysqli_fetch_array($sqllead_code);
        
        $myretunleadfound = $s_f_l['count'];
        if($myretunleadfound > 0){
            $tab1 = json_decode($s_f_l["tab1"]);
            $tab3 = unserialize($s_f_l["tab3"]);
            
            if(in_array($pro_type,$pro_list)){
                
            // restriction and counter functionalities 
            $realleademail = Wo_get_features_count('broadcast_mail');
            
            
            $mypronametype = Wo_GetUser_Pro_type_name();
            
            
            $getuserprotype = Wo_get_my_pro_type();
            
            
            $leademailleft = $realleademail;
            if($leademailleft == 0 || $leademailleft < 0 || Wo_GetUserPackage_status() == 0 || Wo_GetUser_NewPackage_status() == 0){
                $leademailleft = '<b>No</b>';
            }
            
        ?>   
        <div class="col-lg-12">
            <h3> <b id="remail_limit2"><?= $leademailleft; ?></b>Email(s) Left</h3>
        </div>
        <form method="post" action="" id="form_email" enctype="multipart/form-data">
            <div class="contact-us-alert2"></div>
            <div class="col-lg-12">
                <label class="tag_field">
        		    <input id="to_email" name="to_email" value="<?= $tab1->seller_email ?>" type="email" readonly required placeholder=" ">
        		    <input type="hidden" value="<?= $lead_id ?>" id="lead_id" name="lead_id" />
        			<span>To</span>
        			<span id="to_email-info" class="info filedErrors">
        		</label>
        		<label class="tag_field">
        		    <input id="from_email" name="from_email" value="<?= $email ?>" required type="email" placeholder=" ">
        			<span>From</span>
        			<span id="from_email-info" class="info filedErrors">
        		</label>
        		<label class="tag_field">
        		    <input id="subject_email" value="" name="subject_email" required type="text" placeholder=" ">
        			<span>Subject</span>
        			<span id="subject_email-info" class="info filedErrors">
        		</label>
                <div class="form-group ml-auto mt-4">
                    <textarea name="my_bc_msg" class="summernote" id="summernote" title=""></textarea>
                </div>
                
            </div>
            <div class="text-center" style="margin:0; display:block; left: 50%; margin-bottom: 20px;">
        		<button class="btn btn-main btn-mat btn-mat-raised disable_btn" type="submit" name="send" id="send_email_btn">Send Mail</button>
        	</div>
        </form>
        <input type="hidden" id="module_limit2">
        <input type="hidden" id="module_limit3">
        
        
        
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script>
        var path = "<?php echo $wo['config']['site_url']; ?>";
        var t_b = "<?= $_POST['t_b'] ?>";
        
          $('#summernote').summernote({
            placeholder: 'Enter Your Email Message here....',
            tabsize: 2,
            height: 300,
            toolbar: [
                  ['style', ['style']],
                  ['font', ['bold', 'underline', 'clear']],
                  ['color', ['color']],
                  ['para', ['ul', 'ol', 'paragraph']],
                  ['table', ['table']],
                  ['insert', ['link', 'picture','video']],
                  ['view', ['fullscreen']]
            ]
          });
          
          
          
          jQuery("#send_email_btn").on('click',function(e){
            e.preventDefault();
        
  
             var valid_token = true;
            
            if(!jQuery("#subject_email").val()) {
    	        jQuery("#subject_email-info").html('<p style="color: red;">Please Enter Email subject</p>');
    	        jQuery("#subject_email").css('background-color','#FFFFDF');
    	        valid_token = false;
    	    }
    	    
    	    if(!jQuery("#from_email").val()) {
    	        jQuery("#from_email-info").html('<p style="color: red;">Please Enter Property Valid Email</p>');
    	        jQuery("#from_email").css('background-color','#FFFFDF');
    	        valid_token = false;
    	    }
            
            if(valid_token == false){
    	    	return false;
    	    }
	    
        
            var lead_id = $("#lead_id").val();
            var to_email = $("#to_email").val();
            var from_email = $("#from_email").val();
            var subject = $("#subject_email").val();
            var message = $("#summernote").val();
            
            $.ajax({
            	  type:"POST",
            	  url:path+"/newsubmit_leadproperty.php",
            	  data: {action: "SendEmailLead", lead_id:lead_id, to_email:to_email,from_email:from_email,subject:subject,message:message,t_b:t_b},
            	  beforeSend: function() {
            		 $('form#form_email').find('#send_email_btn').attr('disabled','disabled').html('<i class="fa fa-spinner fa-spin"></i> Please Wait');
                   },
            	  success: function (data) {
            	   if(data.status == 200){
                            
                        $('.contact-us-alert2').html('<div class="alert alert-success">' + data.message +'</div>');
                        $('.alert-success').fadeIn(300);
                        $('form#form_email').find('#send_email_btn').removeAttr("disabled").html('<?php echo "Send Mail"; ?>');
                        
                        
                        check_log_user("log","broadcast_mail",0);	
                        check_log_user("check","broadcast_mail",1); 
                        
            	        var module_limit2 = $("#module_limit2").val();
                 
                        var new_module_limit2 = parseInt(module_limit2)-1;
                	 
                		
                      $("#modal-alert-msg").html("You have "+ new_module_limit2 +" Email(s) calculation left.");
                      
                      $("#modal-alert").fadeIn(500);
                      setTimeout(
                          function(){ 
                            $("#modal-alert").fadeOut(500);
                            window.location.reload();
                          }, 4000);
                    
                        $('#module_limit2').val(new_module_limit2);
                        document.getElementById("remail_limit2").innerHTML = new_module_limit2;
                        
                        $('html, #EmailLeadModalMD').animate({ scrollTop: 0 }, 0);
                            
                            
                    }else if(data.status == 401){
                        
                        LeadWalletPopUp(0.03,lead_id,to_email,from_email,subject,message);
            			$('form#form_email').find('#send_email_btn').removeAttr("disabled").html('<?php echo "Send Mail"; ?>');
                    }
                    else{
                        $('html, #EmailLeadModalMD').animate({ scrollTop: 0 }, 0);
                        $('.contact-us-alert2').html('<div class="alert alert-danger">'+data.message+'</div>');
                        $('form#form_email').find('#send_email_btn').removeAttr("disabled").html('<?php echo "Send Mail"; ?>');
                        $('.alert-danger').fadeIn(300);
                    }
                        
                }
             });
    
             
    	 
            
        });
        
        
        function LeadWalletPopUp(price,lead_id,to_email,from_email,subject,message){
    
             var newwallet  = $("#module_limit3").val();
             if(newwallet >= price){
                swal({
                  title: "$" +price +" Charge Request From Your Wallet. " ,
                  text: "$" +price +" Will Be Charged From Your Wallet Account to Send your Mail.",
                  icon: "warning",
                  buttons: true,
                  dangerMode: true,
              })
              .then((willDelete) => {
                    if (willDelete) {
                        
                        $.ajax({
                            type: 'post',
                            url:path+"/newsubmit_leadproperty.php",
            	            data: {action: "SendEmailLeadWallet", lead_id:lead_id, to_email:to_email,from_email:from_email,subject:subject,message:message,t_b:t_b},
                            beforeSend: function() {
                                $('.loadingPay').modal('show', {backdrop: 'true'});
                            },
                            success: function(data){
                                $('.loadingPay').modal('hide');
                                if(data.status == 200){
                                    $('#EmailLeadModalMD').modal('hide');
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
        
        check_log_user("check","broadcast_mail",0);
        
        
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