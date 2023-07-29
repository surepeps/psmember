<?php

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php'); 
require_once('assets/init.php');

// ALLOW ACCESS TO THE PAGE SUBMITTING FROM....
if( isset($_POST['action']) && ($_POST['action'] == "give_access") ) {
        
        //Get the form datas
        $path = $_POST['access_path'];
        $user_id = $_POST['user_id'];
        
        
        // Get current user package id
        $pack_id = $wo['user']['my_package'];
        
        // Get the package details from package table
        $gAccess = getUserPackageComparisms($user_id,$pack_id,$path);
        

        if( $gAccess ){
            
            $data = array(
                'status' => 200,
                'message' => 'Page Access Successfully Granted ',
            );
            
            
            
        }else{
            
            $data = array(
                'status' => 400,
                'message' => 'Error While Making Access',
            );
            
            
        }
        
        
        header("Content-type: application/json");
        echo json_encode($data);
        die;
}