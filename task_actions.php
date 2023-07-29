<?php
$root=__DIR__;

require_once($root.'/config.php');
require_once('assets/init.php'); 

global $wo, $sqlConnect;


$action = filter('action');
$status = 0;
if($action == 'addTask') {


    $title = filter('task_title');
    $description = filter('description');
    $start_date = filter('start_date');
    $end_date = filter('end_date');
    $user_id = filter('user_id');
    $category = filter('category');
    $alert_before = filter('alert_before');


    if(!$start_date) {
        $message = "Please select a valid start date";
    }else if(!$end_date) {
        $message = "Please select a valid end date";
    }else if(!$alert_before) {
        $message = "Please select a valid alert before hours";
    }else if(!$user_id) {
        $message = "Please select a valid user to send this notification.";
    }else if(!$category) {
        $message = "Please select a valid category";
    }else if(!$title) {
        $message = "Please enter a valid title";
    }else if(!$description) {
        $message = "Please enter a valid description";
    }else{

        $alert_on = date('Y-m-d H:i:s', strtotime("-{$alert_before} hours", strtotime($start_date)));

        $taskData = [
            'title' => $title,
            'description' => $description,
            'user_id' => $user_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'type' => $category,
            'alert_before' => $alert_before,
            'alert_on' => $alert_on,
        ];
        
        $query = insertRow('wo_user_tasks', $taskData);
        if($sqlConnect->query($query)) {
            $message = "Task is added successfully.";
            $status = 1; 
        }else{
            $message = mysqli_error($sqlConnect);
        }
        
    }
    
    
    $data = [
        'message' => $message,
        'status' => $status,
        'tasks' => getTasksJson()
    ];

}else if($action == 'getTaskContent'){
    $task_id = filter('task_id');
    
    showPhpErrors();

    $status = $html = 0;
    if($task_id){
        $task = getRow("
            SELECT t.*, u.user_id, u.username, u.email, u.phone_number, u.avatar, u.cover
            FROM wo_user_tasks t
            LEFT JOIN Wo_Users u ON u.user_id = t.user_id
            WHERE t.id = '{$task_id}'
        ");
        $wo['task'] = $task;

        $html = Wo_LoadPage('my-tasks/task-content');
        $status = 1;
    }
    
    $data = [
        'status' => $status,
        'html' => $html,
        'task' => $task
    ];

}

header("Content-type: application/json");
echo json_encode($data);
die();   