<?php

    
    if (isset($_POST["phonenumber"])) {
        session_start();
        $_SESSION["phonenumber"] = $_POST["phonenumber"];
    }

    //include('tg-login.php');
    require_once 'madeline.php';

    //$MadelineProto = new \danog\MadelineProto\API('sessions/'+$_SESSION["phonenumber"]);
    if (isset($_POST["phonenumber"])) {
        $MadelineProto = new \danog\MadelineProto\API('session.madeline');
    } else {
        $MadelineProto = new \danog\MadelineProto\API($_SESSION["phonenumber"]);
    }

    if (isset($_POST["phonenumber"]) || isset($_POST["botname"]) || isset($_POST["botuname"])){
        // do phone number validation here
        $MadelineProto->async(true);
        $MadelineProto->loop(function() use ($MadelineProto) {
            //$MadelineProto->start();
            yield $MadelineProto->phoneLogin($_POST["phonenumber"]);
        });
        //$loadjsfunc = 'onload="tg_login()"';
    }

    if (isset($_POST["tgcode"])) {
        $MadelineProto->async(true);
        $MadelineProto->loop(function() use ($MadelineProto) {
            // $MadelineProto->start();
            print "Line 1";
            //print_r($MadelineProto->completePhoneLogin($_POST["tgcode"]));
            print_r(yield $MadelineProto->completePhoneLogin($_POST["tgcode"]));
            // print_r($authorization);
            print "Line 2";

            // if ($authorization['_'] === 'account.password') {
            //     $authorization = yield $MadelineProto->complete2falogin(yield $MadelineProto->readline('Please enter your password (hint '.$authorization['hint'].'): '));
            // }
            // if ($authorization['_'] === 'account.needSignup') {
            //     $authorization = yield $MadelineProto->completeSignup(yield $MadelineProto->readline('Please enter your first name: '), readline('Please enter your last name (can be empty): '));
            // }

        });
        $MadelineProto->loop();

        print "<h3>Completed</h3>";
        exit;
    }

?>

<!DOCTYPE html>
<html>
    <head>
        <title>MadelineProto</title>
    </head>
    <body>
        <h1>MadelineProto by Miqdaad and Naz</h1>
        <?php
            if (!isset($_POST["phonenumber"]) || !isset($_POST["botname"]) || !isset($_POST["botuname"])) {
        ?>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <table>
                <tr>
                    <td>Phonenumber</td>
                    <td>:</td>
                    <td>
                        <input name="phonenumber" type="text" placeholder="+60123456789" />
                    </td>
                </tr>
                <tr>
                    <td>Bot Name</td>
                    <td>:</td>
                    <td>
                        <input name="botname" type="text" placeholder="Bot Name" />
                    </td>
                </tr>
                <tr>
                    <td>Bot Username</td>
                    <td>:</td>
                    <td>
                    <input name="botuname" type="text" placeholder="eg. kalsymbot, kalsym_bot" /> <span><i>Bot Username must end with "<b>bot</b>"</i></span>
                    </td>
                </tr>
            </table>
            <button type="submit">Go</button>
        </form>
        <?php
            } else {
        ?>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <div>
                <?php 
                    echo "phonenumber: " . $_POST["phonenumber"] . "<br>";
                    echo "botname: " . $_POST["botname"] . "<br>";
                    echo "botuname: " . $_POST["botuname"] . "<br>";
                ?>  
            </div>
            <table>
                <tr>
                    <td>Verification Code</td>
                    <td>:</td>
                    <td>
                        <input name="tgcode" type="text" placeholder="012345" />
                    </td>
                </tr>
            </table>
            <button type="submit">Go</button>      
            </form>
            
        <?php        
            }
        ?>
        <pre>
            <?php
                $MadelineProto->loop(function () use ($MadelineProto) {
                    print_r(yield $MadelineProto->getSelf());
                });
            ?>
        </pre>

        <!-- <script>
            function tg_login(){

                // check msisdn ... disini

                // ajax

                var xmlhttp = new XMLHttpRequest();
                xmlhttp.open("GET", "tg-login.php?msisdn="+msidn+"&code="+code, true);
                xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xmlhttp.send();
            }
        </script> -->

    </body>
</html>