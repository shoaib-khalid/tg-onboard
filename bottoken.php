<?php
session_start();

$phonenumber = "";
if (isset($_SESSION["phonenumber"])) $phonenumber = $_SESSION["phonenumber"];

$req = curl_init();
curl_setopt($req, CURLOPT_URL,"https://tgw.symplified.biz/kbot/telegram-onboard/logout?phonenumber=$phonenumber");
curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
$output=curl_exec($req);

session_destroy();
?>

<html>
<head>
</head>
<body>
<pre>
BotFather have created the bot for you, now we need token generated by bot father for us to automate your bot...

Copy and Paste token given by BotFather to below input field:
</pre>
<form action="">
    Bot token : <input style="width:30%" type="text" placeholder="example: 161053088:AAFfd36hf6btwTjGNF1R_9_gt48tgdhtzRK8"/>
    <button>submit</button>
</form>
</body>
</html>