<?php
session_start();

// if (isset($_SESSION["phonenumber"])) {
//     $test = $_SESSION["phonenumber"];
//     die("ada".$test);
// } else {
//     die("xda");
// }


if ((!isset($_POST["phonenumber"]) && !isset($_POST["botname"]) && !isset($_POST["botuname"])) && !isset($_SESSION["phonenumber"])) {
    ob_start();
    include('tgui-formx.php');
    $content = ob_get_contents();
    ob_end_clean();

    print $content;
} else {

    if (isset($_POST["phonenumber"])) {
        $_SESSION["phonenumber"] = $_POST["phonenumber"];
    }

    if (!file_exists('madeline.php')) {
        copy('https://phar.madelineproto.xyz/madeline.php', 'madeline.php');
    }
    include 'madeline.php';

    $MadelineProto = new \danog\MadelineProto\API('sessions/'+$_SESSION["phonenumber"]);
    $MadelineProto->async(true);
    $MadelineProto->loop(function () use ($MadelineProto) {

        ob_start();
        include('tgui-form.php');
        $content = ob_get_contents();
        ob_end_clean();

        yield $MadelineProto->setWebTemplate($content);
        yield $MadelineProto->start();
        $template = yield $MadelineProto->getWebTemplate();
        echo $template;
        
    });

    // $MadelineProto->loop();

}

?>