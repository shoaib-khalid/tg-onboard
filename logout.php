<?php

if (!file_exists('madeline.php')) {
    copy('https://phar.madelineproto.xyz/madeline.php', 'madeline.php');
}

include 'madeline.php';


if (isset($_GET['phonenumber'])){

    $phonenumber = $_GET['phonenumber'];
    $pattern = '/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/i';
    if (!preg_match($pattern, $phonenumber)){ // Outputs 1
        die("Wrong Format");
    }    

    // initiate madeline proto
    $MadelineProto = new \danog\MadelineProto\API('./sessions/session.'.$phonenumber);

    // log user out
    $MadelineProto->logout();

    $MadelineProto->async(true);
    $MadelineProto->loop(function() use ($MadelineProto) {
        yield $MadelineProto->logout();
    });

    $pid = exec("pgrep -f session.$phonenumber | tr '\n' ' '");

    exec("kill -9 $pid");

    foreach(glob("./sessions/session.$phonenumber*") as $f) {
        unlink($f);
    }

    // response
    $MadelineProto->echo('OK, done!');

} else {
    die("404");
}