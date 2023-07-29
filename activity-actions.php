<?php

    $root=__DIR__;
    require_once($root.'/config.php');
    require_once('assets/init.php'); 
    global $wo, $sqlConnect;

    $action = filter('action');

    if($action == 'getActivityLogs'){
        
        $user_id = filter('user_id');
        $type = filter('type');
        $activities = getUserActivityComparison($user_id, $type);
        
        foreach($activities as $activity) {
            $avg = 0;
            if(($activity['total'] != 0 && ($activity['outbound'] != 0 || $activity['inbound'] != 0))){
                $avg = ($activity['total']/($activity['outbound'] + $activity['inbound']));
            }
    ?>
        <tr>
            <td class="align-middle"><?= $activity['username'] ?></td>
            <td class="align-middle"><?= $activity['inbound'] ?></td>
            <td class="align-middle"><?= $activity['outbound'] ?></td>
            <td class="align-middle"><?= gmdate("H:i:s", $activity['total']); ?>s</td>
            <td class="align-middle"><?= gmdate("H:i:s", $avg); ?>s</td>
        </tr>
    <?php } 
} ?>

