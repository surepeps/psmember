<?php

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);

$user_id = $wo['user']['user_id'];


if($_POST['action']=="incoming_messages"){
    if(isset($_POST['phone_no'])){
        
        $phone_no = $_POST['phone_no'];
        
        // get all incoming message to a number
        $get_incom_msg = Get_my_incoming_messages($phone_no);
    		
    	if($get_incom_msg){
    	    $request  =  '<table data-toolbar="#toolbar" data-search="true" data-show-toggle="true" data-show-fullscreen="true" data-show-columns="true" data-show-columns-toggle-all="true" data-show-export="true" data-click-to-select="true" data-detail-formatter="detailFormatter" data-minimum-count-columns="2" data-pagination="true" data-id-field="id" data-page-list="[10, 25, 50, 100, all]" data-show-footer="true" data-response-handler="responseHandler" id="fresh-table" class="table table-striped table-bordered" style="width:100%">
                                          
                <thead>
                  <th data-field="sender"><i class="far fa-user"></i> Sender</th>
                  <th data-field="to"><i class="fas fa-envelope-open-text"></i> To</th>
                  <th data-field="message"><i class="far fa-comment-alt"></i> Message</th>
                  <th data-field="actions"><i class="fas fa-cogs"></i> Actions</th>
                </thead>
                <tbody class="incoming_rep_t">';
                  
                    foreach($get_incom_msg as $inc){
                   
                       $request .=  '<tr data-id="" class="myrow" id="myrow_"> 
                                <td class="textshadow">'.$inc->from.'</td>
                                <td class="textshadow">'.$inc->to.'</td>
                                <td rel="tooltip" title="">'.mb_strimwidth($inc->body, 0, 20, "...").'</td>
                                <td>
                                    <a rel="tooltip" title="'.mb_strimwidth($inc->body, 0, 20, "...").'" onClick="ShowFullMessage(\''. $inc->sid. '\')" class="table-action like" href="javascript:void(0)">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            </tr>';
                        
                     } 
               
            $request .=  '</tbody>
              </table> <script> $("#fresh-table").bootstrapTable({ locale: "en-US"}); </script>';
              echo $request;
                                   
    	}else{
    	    $request = '<div style="text-align: center; font-size: 20px;">No incoming message(s) to display</div>';
    	    echo $request;
    	}
        
        
    }else{
        $request = '<div style="text-align: center; font-size: 20px;">No Phone Number set</div>';
        echo $request;
    }
    
	 exit;
}


if($_POST['action']=="outgoing_messages"){
    if(isset($_POST['phone_no_out'])){
        
        $phone_no_out = $_POST['phone_no_out'];
        
        // get all incoming message to a number
        $get_outgo_msg = Get_my_outgoing_messages($phone_no_out);
    		
    	if($get_outgo_msg){
    	    $request  =  '<table data-toolbar="#toolbar" data-search="true" data-show-toggle="true" data-show-fullscreen="true" data-show-columns="true" data-show-columns-toggle-all="true" data-show-export="true" data-click-to-select="true" data-detail-formatter="detailFormatter" data-minimum-count-columns="2" data-pagination="true" data-id-field="id" data-page-list="[10, 25, 50, 100, all]" data-show-footer="true" data-response-handler="responseHandler" id="fresh-table2" class="table table-striped table-bordered" style="width:100%">
                                          
                <thead>
                  <th data-field="sender"><i class="far fa-user"></i> Sender</th>
                  <th data-field="to"><i class="fas fa-envelope-open-text"></i> To</th>
                  <th data-field="message"><i class="far fa-comment-alt"></i> Message</th>
                  <th data-field="actions"><i class="fas fa-cogs"></i> Actions</th>
                </thead>
                <tbody class="incoming_rep_t">';
                  
                    foreach($get_outgo_msg as $out){
                   
                       $request .=  '<tr data-id="" class="myrow" id="myrow_"> 
                                <td class="textshadow">'.$out->from.'</td>
                                <td class="textshadow">'.$out->to.'</td>
                                <td rel="tooltip" title="">'.mb_strimwidth($out->body, 0, 20, "...").'</td>
                                <td>
                                    <a rel="tooltip" title="'.mb_strimwidth($out->body, 0, 20, "...").'" onClick="ShowFullMessageOut(\''. $out->sid. '\')" class="table-action like" href="javascript:void(0)">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            </tr>';
                        
                     } 
               
            $request .=  '</tbody>
              </table> <script> $("#fresh-table2").bootstrapTable({ locale: "en-US"}); </script>';
              echo $request;
                                   
    	}else{
    	    $request = '<div style="text-align: center; font-size: 20px;">No OutGoing message(s) to display</div>';
    	    echo $request;
    	}
        
        
    }else{
        $request = '<div style="text-align: center; font-size: 20px;">No Phone Number set</div>';
        echo $request;
    }
    
	 exit;
}


if($_POST['action']=="show_incoming_message"){
    $sender_id = $_POST['sender_id'];
    
    // Check and get message details...
    $mymessage = Get_single_incomANDoutg_message($sender_id);
    
    if($mymessage){

        $from = $mymessage->from;
        $to = $mymessage->to;
        $message = $mymessage->body;
        //$date_sent = $mymessage->date_sent;
        
        $response = '<div class="container-body">
                        <table id="table">
                            <tr>
                              <td>From: </td>
                              <td id="s_name">'.$from.'</td>
                            </tr>
                            <tr>
                              <td>To:</td>
                              <td id="s_email">'.$to.'</td>
                            </tr>
                            <tr>
                              <td>Message:</td>
                              <td id="s_phone">'.$message.'</td>
                            </tr>
                        </table>
                    </div>';
        
        $data = array(
            'status' => 200,
            'result' => $response,
            'message' => 'Success '
        );
    }else{
        $data = array(
            'status' => 400,
            'message' => 'Message Not Found please Reload the page'
        );
    }
   
    header("Content-type: application/json");
    echo json_encode($data);
    die;
}


if($_POST['action']=="show_outgoing_message"){
    $sender_id = $_POST['sender_id'];
    
    // Check and get message details...
    $mymessage = Get_single_incomANDoutg_message($sender_id);
    
    if($mymessage){

        $from = $mymessage->from;
        $to = $mymessage->to;
        $message = $mymessage->body;
        //$date_sent = $mymessage->date_sent;
        
        $response = '<div class="container-body">
                        <table id="table">
                            <tr>
                              <td>From: </td>
                              <td id="s_name">'.$from.'</td>
                            </tr>
                            <tr>
                              <td>To:</td>
                              <td id="s_email">'.$to.'</td>
                            </tr>
                            <tr>
                              <td>Message:</td>
                              <td id="s_phone">'.$message.'</td>
                            </tr>
                        </table>
                    </div>';
        
        $data = array(
            'status' => 200,
            'result' => $response,
            'message' => 'Success '
        );
    }else{
        $data = array(
            'status' => 400,
            'message' => 'Message Not Found please Reload the page'
        );
    }
   
    header("Content-type: application/json");
    echo json_encode($data);
    die;
}