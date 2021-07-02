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
$MadelineProto->async(true);

$MadelineProto->loop(function() use ($MadelineProto) {

    ob_start();
    include('tgui-content.php');
    $content = ob_get_contents();
    ob_end_clean();

    yield $MadelineProto->setWebTemplate($content);
    yield $MadelineProto->start();
    $template = yield $MadelineProto->getWebTemplate();
    echo $template;

    yield $MadelineProto->phoneLogin(yield $MadelineProto->readline('Enter your phone number: '));
    $authorization = yield $MadelineProto->completePhoneLogin(yield $MadelineProto->readline('Enter the phone code: '));
    if ($authorization['_'] === 'account.password') {
        $authorization = yield $MadelineProto->complete2falogin(yield $MadelineProto->readline('Please enter your password (hint '.$authorization['hint'].'): '));
    }
    if ($authorization['_'] === 'account.needSignup') {
        $authorization = yield $MadelineProto->completeSignup(yield $MadelineProto->readline('Please enter your first name: '), readline('Please enter your last name (can be empty): '));
    }
});

// response
$MadelineProto->echo('OK, done!');