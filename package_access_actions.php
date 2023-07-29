<?php


$root=__DIR__;
require_once($root .'/config.php');
require_once($root .'/assets/init.php'); 
// showPhpErrors();
$action = filter('action');



if($action == "addPackageAccess") {
    $package_id = filter('package_id');
    $cities = filter('num_of_cities');

    $status = 0;
    if(!$package_id) {
        $message = "Please select a valid package!";
    }else if(!$cities){
        $message = "Number of cities must be greater then 0!";
    }else{

        $package = getTableData('wo_buyer_finder_access', ['package_id' => $package_id], 1);
        if($package) {
            $message = "This package is already added. Please try again with another";
        }else{
            $data = [
                'package_id' => $package_id,
                'num_of_cities' => $cities,
            ];
            if($sqlConnect->query(insertRow('wo_buyer_finder_access', $data))){
                $status = 1;
                $message = "Package access is added successfully";
            }else{
                $message = mysqli_error($sqlConnect);
            }
        }

    }

    if($status == 0) {
        $message = '<div class="alert alert-danger"><i class="fa fa-times"></i> ' .$message. '</div>';
    }else{
        $message = '<div class="alert alert-success"><i class="fa fa-check"></i> ' .$message. '</div>';
    }

    $data = [
        'message' => $message,
        'status' => $status
    ];
}else if($action == "editPackageAccess") {
    $id = filter('access_id');
    $package_id = filter('package_id');
    $cities = filter('num_of_cities');

    $status = 0;
    if(!$package_id) {
        $message = "Please select a valid package!";
    }else if(!$cities){
        $message = "Number of cities must be greater then 0!";
    }else{

        $where = ['id' => $id];
        $package = getTableData('wo_buyer_finder_access', $where, 1);
        if(!$package) {
            $message = "This package is deleted. Please try again with another";
        }else if($package['package_id'] != $package_id) {
            $message = "Package can not be changed.";
        }else{
            $data = [
                'package_id' => $package_id,
                'num_of_cities' => $cities,
            ];
            if($sqlConnect->query(updateRow('wo_buyer_finder_access', $data, $where))){
                $status = 1;
                $message = "Package access is updated successfully";
            }else{
                $message = mysqli_error($sqlConnect);
            }
        }

    }

    if($status == 0) {
        $message = '<div class="alert alert-danger"><i class="fa fa-times"></i> ' .$message. '</div>';
    }else{
        $message = '<div class="alert alert-success"><i class="fa fa-check"></i> ' .$message. '</div>';
    }

    $data = [
        'message' => $message,
        'status' => $status
    ];
}else if($action == 'deletePackageAccess'){

    $status = 0;
    $id = filter('access_id');
    
    $where = ['id' => $id];
    $cron = getRow("SELECT * FROM wo_buyer_finder_access WHERE ". getWhere($where));
    if(!$id || !$cron) {
        $message = 'Package access not found, please try again with different package access!';
    }else{

        $query = deleteRow('wo_buyer_finder_access', $where);
        if($sqlConnect->query($query)) {
            $status = 1;
            $message = "Package Access Deleted Successfully";
        }else {
            $message = mysqli_error($sqlConnect);
        }
        
    }

    if($status) {
        $message = '<div class="alert alert-success"><i class="fa fa-check"></i> ' .$message. '</div>';
    }else{
        $message = '<div class="alert alert-danger"><i class="fa fa-times"></i> ' .$message. '</div>';
    }
    
    $data = [
        'message' => $message,
        'status' => $status,
    ];
}else if($action == 'updateUserCities' ){
    $user_id = filter('user_id');
    $cities = filter('areaofinterest');


    $status = 0;
    if(!$user_id) {
        $message = "You are not logged in. Please login first";
    }else if(!$cities) {
        $message = "Please enter atleast one city";
    }else{

        $package = getUserPackageAccess($user_id);
        if(!$package) {
            $message = "You have not subscribed any package. Please try again by subscribing any package!";
        }else{
            $table = "wo_buyer_access_cities";

            $data = [
                'user_id' => $user_id,
                'package_id' => $package['package_id'],
                'cities' => $cities
            ];

            $where = ['user_id' => $user_id];
            $accessCities = getTableData($table, $where);

            if($accessCities) {
                $query = updateRow($table, $data, $where);
            }else{
                $query = insertRow($table, $data);
            }

            if($sqlConnect->query($query)){
                $status = 1;
                $message = "Buyer finder cities are updated succecssfully";
            }else{
                $message = mysqli_error($sqlConnect);
            }
        }
        
    }

    $data = [
        'status' => $status,
        'message' => $message
    ];

}

header("Content-type: application/json");
echo json_encode($data);
die();   
