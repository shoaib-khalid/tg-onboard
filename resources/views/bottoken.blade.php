<html>
<head>
    <title>Configure</title>
</head>
<body>
    <div>
        <p>
        BotFather have created the bot for you, now we need token generated by bot father for us to automate your bot. <br>
        Copy and Paste token given by BotFather to below input field:
        </p>
    </div>
    <form method="post" action="">
        @csrf
        Bot token : <input style="width:30%" type="text" placeholder="example: 99853088:AAFfd36hf6btwTjGNF1R_9_gt48tgdhtzRK8"/>
        Bot Username : <input type="text" name="botuname" value="{{$botuname}}">
        Bot Token : <input type="text" name="botuname" value="{{$token}}">
        Merchant Id : <input type="text" name="botuname" value="{{$userId}}" readonly>
        <button>submit</button>
    </form>
</body>
</html>