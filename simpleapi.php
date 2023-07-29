<?php

header_remove('Server');
header("Content-type: application/json");
require('assets/init.php');
require('simpleapi/init.php');

$wo['loggedin'] = false;

$response_data     = array();
$error_code    = 0;
$error_message = '';
$type   = (!empty($_GET['type'])) ? Wo_Secure($_GET['type'], 0) : false;
$username  = (!empty($_POST['domain'])) ? Wo_Secure($_POST['domain'], 0) : false;

if (empty($type)) {
	$response_data       = array(
        'api_status'     => '404',
        'errors'         => array(
            'error_id'   => '1',
            'error_text' => 'Error: 404 API Type not specified'
        )
    );
    echo json_encode($response_data, JSON_PRETTY_PRINT);
    exit();
}

if (empty($username)){
	$response_data       = array(
		'api_status'     => '404',
		'errors'         => array(
			'error_id'   => '1',
			'error_text' => 'Error: 404 POST (Access Key) not specified, Please put valid Access Key Contact the admin for Access Key'
		)
	);
	echo json_encode($response_data, JSON_PRETTY_PRINT);
	exit();
}

// Check if username exist 
$getId = getServerKeyApi("new_domain",$username); 
// Wo_UserIdFromUsername($username);

if ( empty($getId['SK']) ) {
    $response_data       = array(
        'api_status'     => '404',
        'errors'         => array(
            'error_id'   => '1',
            'error_text' => 'Error: invalid Access Key, Contact the admin for Access Key'
        )
    );
    echo json_encode($response_data, JSON_PRETTY_PRINT);
    exit();
}


$api = "simpleapi/endpoints/$type.php"; 

if (!file_exists($api)) {
    $response_data       = array(
        'api_status'     => '404',
        'errors'         => array(
            'error_id'   => '1',
            'error_text' => 'Error: 404 API Type Not Found'
        )
    );
    echo json_encode($response_data, JSON_PRETTY_PRINT);
    exit();
}


require_once  "simpleapi/functions.php";
require_once  $api;


if (!empty($error_code)) {
    $response_data       = array(
        'api_status'     => '400',
        'errors'         => array(
            'error_id'   => $error_code,
            'error_text' => $error_message
        )
    );
}

echo json_encode($response_data, JSON_PRETTY_PRINT);
exit();

mysqli_close($sqlConnect);
unset($wo);






?>