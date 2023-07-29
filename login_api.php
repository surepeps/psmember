<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type: application/json');
header('Accept: application/json');

require('assets/init.php');

$response_data   = array(
    'api_status' => 400
);
$required_fields = array(
    'username',
    'password'
);

//foreach ($required_fields as $key => $value) {
//    if (empty($_POST[$value]) && empty($error_code)) {
//        $error_code    = 3;
//        $error_message = $value . ' (POST) is missing';
//    }
//}
//
//if (empty($error_code)) {
//
//    $username       = $_POST['username'];
//    $password       = $_POST['password'];
//    $user_id        = Wo_UserIdForLogin($username);
//    $recipient_data = Wo_UserData($user_id);
//
//    if (empty($recipient_data)) {
//        $error_code    = 4;
//        $error_message = 'Username not found';
//    } else {
//
//        $login = Wo_Login($username, $password);
//        if (!$login) {
//            $error_code    = 5;
//            $error_message = 'Password is incorrect';
//        } else {
//
//            if (Wo_TwoFactor($_POST['username']) != false) {
//                $time           = time();
//                $cookie         = '';
//                $access_token   = sha1(rand(111111111, 999999999)) . md5(microtime()) . rand(11111111, 99999999) . md5(rand(5555, 9999));
//                $timezone       = 'UTC';
//                $create_session = mysqli_query($sqlConnect, "INSERT INTO " . T_APP_SESSIONS . " (`user_id`, `session_id`, `platform`, `time`) VALUES ('{$user_id}', '{$access_token}', 'phone', '{$time}')");
//                if (!empty($_POST['timezone'])) {
//                    $timezone = Wo_Secure($_POST['timezone']);
//                }
//                $add_timezone = mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET `timezone` = '{$timezone}' WHERE `user_id` = {$user_id}");
//                // if (!empty($_POST['device_id'])) {
//                //     $device_id = Wo_Secure($_POST['device_id']);
//                //     $update    = mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET `device_id` = '{$device_id}' WHERE `user_id` = '{$user_id}'");
//                // }
//                if (!empty($_POST['android_m_device_id'])) {
//                    $device_id  = Wo_Secure($_POST['android_m_device_id']);
//                    $update  = mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET `android_m_device_id` = '{$device_id}' WHERE `user_id` = '{$user_id}'");
//                }
//                if (!empty($_POST['ios_m_device_id'])) {
//                    $device_id  = Wo_Secure($_POST['ios_m_device_id']);
//                    $update  = mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET `ios_m_device_id` = '{$device_id}' WHERE `user_id` = '{$user_id}'");
//                }
//                if (!empty($_POST['android_n_device_id'])) {
//                    $device_id  = Wo_Secure($_POST['android_n_device_id']);
//                    $update  = mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET `android_n_device_id` = '{$device_id}' WHERE `user_id` = '{$user_id}'");
//                }
//                if (!empty($_POST['ios_n_device_id'])) {
//                    $device_id  = Wo_Secure($_POST['ios_n_device_id']);
//                    $update  = mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET `ios_n_device_id` = '{$device_id}' WHERE `user_id` = '{$user_id}'");
//                }
//                if ($create_session) {
//                    $response_data = array(
//                        'api_status' => 200,
//                        'timezone' => $timezone,
//                        'access_token' => $access_token,
//                        'user_id' => $user_id,
//                    );
//                }
//            }
//            else{
//                $response_data = array(
//                    'api_status' => 200,
//                    'message' => 'Please enter your confirmation code',
//                    'user_id' => $user_id
//                );
//            }
//
//        }
//
//
//    }
//
//}else{
//    $response_data       = array(
//        'api_status'     => '404',
//        'errors'         => array(
//            'error_id'   => $error_code,
//            'error_text' => $error_message
//        )
//    );
//}


echo json_encode($response_data, JSON_PRETTY_PRINT);
exit();