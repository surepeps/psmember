<?php
$root=__DIR__;

require_once($root.'/config.php');
require_once('assets/init.php'); 

global $wo, $sqlConnect;


$action = filter('action');
$status = 0;
if($action == 'addUsers') {

    $name = filter('name');
    $email = filter('email');
    $password = filter('password');
    $phone = filter('phone');
    $user_id = filter('user_id');
    
    
    if(!$user_id) {
        $message = "Please login first to add user";
    }else if(!$name) {
        $message = "Please enter a valid name";
    }else if(!$email) {
        $message = "Please enter a valid email";
    }else if(!$password) {
        $message = "Please enter a valid password";
    }else if(!$phone) {
        $message = "Please enter a valid phone";
    }else if(strlen($password) < 6) {
        $message = "Password length should be minimum 6";
    }else{

        // Making password HASH for not readable
        $password = password_hash($password, PASSWORD_DEFAULT);

        $d = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
            'user_id' => $user_id,
        ];
        $query = insertRow('wo_offer_onspot_users', $d); 
        
        if($sqlConnect->query($query)) {
            $message = "User has been added successfully.";
            $status = 1; 
        }else{
            $message = mysqli_error($sqlConnect);
        }
    }
    
    
    $data = [
        'message' => $message,
        'status' => $status
    ];

}else if($action == 'deleteUsers') {
    $user_id = filter('user_id');

    if(!$user_id) {
        $message = "Please select a valid user";
    }else{

        $where = ['id' => $user_id];
        $user = getTableData('wo_offer_onspot_users', $where, 1);
        if(!$user) {
            $message = "User is deleted please try with another plan.";
        }else{
            
            $query = deleteRow('wo_offer_onspot_users', $where);
            if($sqlConnect->query($query)) {
                $message = "User has been deleted successfully.";
                $status = 1; 
            }else{
                $message = mysqli_error($sqlConnect);
            }
        }
        
    }
    
    $data = [
        'message' => $message,
        'status' => $status
    ];

}else if($action == 'getUserData') {
    $user_id = filter('user_id');

    $user = null;
    if(!$user_id) {
        $message = "Please select a valid user";
    }else{

        $where = ['id' => $user_id];
        $user = getTableData('wo_offer_onspot_users', $where, 1);
        if(!$user) {
            $message = "User is deleted please try with another user.";
        }else{
            $status = 1;
            $message = "User found";
        }
        
    }
    
    $data = [
        'message' => $message,
        'status' => $status,
        'user' => $user
    ];

}else if($action == 'editUsers') {

    $name = filter('name');
    $email = filter('email');
    $password = filter('password');
    $phone = filter('phone');
    $user_id = filter('user_id');
    
    if(!$user_id) {
        $message = "Please login first to add user";
    }else if(!$name) {
        $message = "Please enter a valid name";
    }else if(!$email) {
        $message = "Please enter a valid email";
    }else if(!$phone) {
        $message = "Please enter a valid phone";
    }else if($password && strlen($password) < 6) {
        $message = "Password length should be minimum 6";
    }else{

        $where = [
            'id' => $user_id
        ];
        $user = getTableData('wo_offer_onspot_users', $where, 1);

        if(!$user){
            $message = "This user is already deleted, please try again with another user";  
        }else{
            $d = [
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
            ];
            
            if($password){

                // Making password HASH for not readable
                $password = password_hash($password, PASSWORD_DEFAULT);
                $d['password'] = $password;

            }
    
            $query = updateRow('wo_offer_onspot_users', $d, $where); 
            if($sqlConnect->query($query)) {
                $message = "User has been edited successfully.";
                $status = 1; 
            }else{
                $message = mysqli_error($sqlConnect);
            }
        }
        
    }
    
    
    $data = [
        'message' => $message,
        'status' => $status
    ];

}


if($data['status']){
    $data['message'] = "<div class='alert alert-success'><i class='fa fa-check'></i> " . $data['message'] . "</div>";
}else{
    $data['message'] = "<div class='alert alert-danger'><i class='fa fa-times'></i> " . $data['message'] . "</div>";
}

header("Content-type: application/json");
echo json_encode($data);
die();   