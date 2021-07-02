<?php

if (!file_exists('madeline.php')) {
    copy('https://phar.madelineproto.xyz/madeline.php', 'madeline.php');
}
include 'madeline.php';

$MadelineProto = new \danog\MadelineProto\API('session.madeline');
$MadelineProto->async(true);
$MadelineProto->loop(function () use ($MadelineProto) {

    // ob_start();
    // include('content.php');
    // $content = ob_get_contents();
    // ob_end_clean();

    yield $MadelineProto->start();
    yield $MadelineProto->setWebTemplate("STEP 1");
    $template = yield $MadelineProto->getWebTemplate();
    echo $template;

    sleep(10000);

    yield $MadelineProto->setWebTemplate("STEP 2");
    $template = yield $MadelineProto->getWebTemplate();
    echo $template;
    
});

// $MadelineProto->loop();
