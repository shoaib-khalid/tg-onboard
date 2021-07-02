<?php

if (!file_exists('madeline.php')) {
    copy('https://phar.madelineproto.xyz/madeline.php', 'madeline.php');
}

include 'madeline.php';
require_once("./libs/getjsonbody.php");

// fetch request body
$smart = new smart\get_response();
$arr_obj = $smart->object;

$peer = $arr_obj['peer'];
$message = $arr_obj['message'];

// initiate madeline proto
$MadelineProto = new \danog\MadelineProto\API('session.madeline');
$MadelineProto->start();

// start sending message
$MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => $message]);

//response
$MadelineProto->echo('OK, done!');
