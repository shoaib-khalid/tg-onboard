<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// MadelineProto
include 'madeline.php';
$MadelineProto = new \danog\MadelineProto\API('session.madeline');
$MadelineProto->async(true);

// REST API 
require 'reponse-desc.php';

// receive packet from core
$packet = trim(file_get_contents("php://input"));
$packet = trim(preg_replace('/\s\s+/', '', $packet));
$object = json_decode($packet, true);

$botname = $object['bot_username'];
$merchant_status = 1;
$response_status = 'failed';

if (isset($botname)) {

    $MadelineProto->start();
    $MadelineProto->messages->sendMessage(['peer' => '@BotFather', 'message' => "$botname"]);

    $query_data = [];
    $response_code = '0000';
    $response_status = 'success';

    // $query_data = [
    //     'bot_token' => 'test m id',
    //     'callback_url' => '$callback_url',
    //     'status' => $merchant_status,
    //     'timestamp' => date('d-m-Y g:i:s a', strtotime("2020-12-19 02:37:22"))
    // ];

    // switch ($merchant_status) {
    //     case 0:
    //         $response_code = '0005';
    //         break;
    //     case 1:
    //         $response_code = '0000';
    //         $response_status = 'success';
    //         break;
    //     case 2:
    //         $response_code = '0006';
    //         break;
    //     default:
    //         $response_code = '0007';
    //         break;
    // }
    
} else{
	$response_code = '0001';
}

// RESPONSE
if ($response_status == 'success') {
	$data = [ 
		'response' => $response_status,
		'response_code' => $response_code,
		'response_desc' => getResponseDesc($response_code),
		'data' => $query_data
	];
}else{
	$data = [ 
		'response' => $response_status,
		'response_code' => $response_code,
		'response_desc' => getResponseDesc($response_code)
	];
}

// response data
$response = $data;
echo json_encode($response);

?>