<?php
require_once('assets/init.php');

$action = filter('action');

if ($action == "updateUsers") {
    $popup_users = filter('popup_users');
    $message = "";

    $status = 0;

    if (!$popup_users) {
        $message = "Please enter atleast one username for restriction";
    } else {

        $where = [];

        foreach (explode(',', $popup_users) as $user) {
            if (!$user) continue;
            $where[] = "'{$user}'";
        }

        $query = "
            SELECT username FROM " . T_USERS . "
            WHERE username IN (" . implode(',', $where) . ")
        ";
        $users = getTableRows($query);
        

        if (!$users) {
            $message = "These users does not exist in the Database, please try again with different one";
        } else {

            $users = array_column($users, 'username');

            $allowed_users = implode(',', $users);

            $updated = updateConfig('allowed_without_pop_users', $allowed_users);
            if ($updated) {

                $status = 1;
                $data['users'] = $allowed_users;
                $message = "Users saved successfully";
            }
        }
    }

    $data["message"] = $message;
    $data["status"] = $status;
}

header("Content-type: application/json");
echo json_encode($data);
die();
