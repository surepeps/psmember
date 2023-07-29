<?php

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');

$user_id = $wo['user']['user_id'];

// crontest

$allacticronrecords = listAllPipelineForCron_2();

if( count($allacticronrecords) > 0 ){
    
    
    // 
    // EMAIL ACTION
    // 
    // 
    
    // Get all actions corresponding to email action table
    $allcorrespondingemailact = GetAllcorrespondingEmiailActions();
    foreach($allcorrespondingemailact as $acea){
        
        $act_id = $acea['step_action_id'];
        $pID = $acea['pipeId'];
        
        echo "ACTION ID:- ".$act_id." with PIPELINE ID of:- ".$pID." <br>";
        
        
    }
    
    echo "working....";
    




}