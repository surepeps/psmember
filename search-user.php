<?php
$root=__DIR__;

require_once($root.'/config.php');
require_once('assets/init.php'); 

global $wo, $sqlConnect;


    $status = 0;
    $html = NULL;
    $text = filter('text');

    if($text){

        $query =  "SELECT user_id, username FROM " . T_USERS . " WHERE username LIKE '{$text}%'";
        $users = getTableRows($query);
        if(count($users)) {

            $html = "<ul>";
            foreach($users as $user):
                $html .= "<li><a data-id='" . $user['user_id'] . "' data-name='" . $user['username'] . "' >" . $user['username'] . "</a></li>";
            endforeach; 
            
            $html .= "</ul>";
            $status = 1; 
        }
        
    }
    
    $data = [
        'html' => $html,
        'status' => $status
    ];


header("Content-type: application/json");
echo json_encode($data);
die();   