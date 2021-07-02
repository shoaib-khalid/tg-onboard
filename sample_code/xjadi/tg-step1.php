<?php

if (!file_exists('madeline.php')) {
    copy('https://phar.madelineproto.xyz/madeline.php', 'madeline.php');
}

include 'madeline.php';
require_once("./libs/getjsonbody.php");

// fetch request body
$smart = new smart\get_response();
$arr_obj = $smart->object;

$msisdn = $arr_obj['msisdn'];
$message = $arr_obj['message'];

// initiate madeline proto
$MadelineProto = new \danog\MadelineProto\API('session.madeline');
$MadelineProto->start();

// log user out
$MadelineProto->phoneLogin("60108312189");

// response
$MadelineProto->echo('OK, done!');