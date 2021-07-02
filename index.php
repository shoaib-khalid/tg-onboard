<?php
session_start();

if (
    (isset($_POST["phonenumber"]) && isset($_POST["botname"]) && isset($_POST["botuname"])) || 
    (isset($_SESSION["phonenumber"]) && isset($_SESSION["botname"]) && isset($_SESSION["botuname"]))
    ) {
    
        if (!isset($_SESSION["phonenumber"])) $_SESSION["phonenumber"] = $_POST["phonenumber"];
        if (!isset($_SESSION["botname"])) $_SESSION["botname"] = $_POST["botname"];
        if (!isset($_SESSION["botuname"])) $_SESSION["botuname"] = $_POST["botuname"];
        
        $phonenumber = $_SESSION["phonenumber"];
        $botname = $_SESSION["botname"];
        $botuname = $_SESSION["botuname"];
    
        if (!file_exists('./madeline/madeline.php')) {
            copy('https://phar.madelineproto.xyz/madeline.php', './madeline/madeline.php');
        }

        include './madeline/madeline.php';
        include './ApiWrappers/Templates.php';
        include './ApiWrappers/Start.php';
        include './Wrappers/Templates.php';
        // include './Settings/Templates.php';
        // include './MTProtoTools/MTProto.php';
        // include './MTProtoTools/ResponseInfo.php';
        // include './MTProtoTools/MyTelegramOrgWrapper.php';
        
        $MadelineProto = new \danog\MadelineProto\API('./sessions/session.' . $phonenumber);

        // ob_start();
        // include('template.html');
        // $content = ob_get_contents();
        // ob_end_clean();
        // $MadelineProto->setWebTemplate($content);

        $MadelineProto->start();

        $me = $MadelineProto->getSelf();
        
        $MadelineProto->logger($me);
        
        if (!$me['bot']) {
            $MadelineProto->messages->sendMessage(['peer' => '@BotFather', 'message' => "/start"]);
            sleep(2);
            $MadelineProto->messages->sendMessage(['peer' => '@BotFather', 'message' => "/newbot"]);
            sleep(2);
            $MadelineProto->messages->sendMessage(['peer' => '@BotFather', 'message' => $botname]);
            sleep(2);
            $MadelineProto->messages->sendMessage(['peer' => '@BotFather', 'message' => $botuname]);
            sleep(2);
        }
        
        print "Redirecting Page...";
        header("Location: https://tgw.symplified.biz/kbot/telegram-onboard/bottoken");

} else {
    ob_start();
    include('form.php');
    $content = ob_get_contents();
    ob_end_clean();

    print $content;

}

?>


