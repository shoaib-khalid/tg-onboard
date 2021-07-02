<!DOCTYPE html>
<html>
    <head>
        <title>Symplified</title>
        <style>
        .label {
            display: inline-block;
            width: 300px;
            vertical-align: middle;
        } 
        
        .input {
            margin: 10px 0 10px 0;
            width:200px;
            vertical-align: middle;
        }
        </style>
    </head>
    <body>
        <h1>Symplified</h1>
        <form method="post" onsubmit="return validate_form()" action="">
            
            <label class="label" for="phonenumber">Tell us the phone number would you want to associate with this Telegram Bot</label>  
            <input class="input" id="phonenumber" name="phonenumber" type="text" placeholder="eg: +60123456789" onfocusout="check_phonenumber()"/> 
            <span id="phonenumber_msg"></span> <br><br>
            
            <label class="label" for="botname">What name would you want to give your store in Telegram</label> 
            <input class="input" id="botname" name="botname" type="text" placeholder="eg: Symplified Bot" onfocusout="check_botname()"/>
            <span id="botname_msg"></span> <br><br>
            
            <label class="label" for="botname">What name would you want to give your Telegram</label> 
            <input class="input" id="botuname" name="botuname" type="text" placeholder="eg. @SymplifiedBot" onfocusout="check_botuname()"/>
            <span id="botuname_msg"></span> <br><br>

            <button type="submit">Go</button>
        </form>
        
        <script>
            function check_phonenumber() {
                let status = false;
                let phonenumber = document.getElementById("phonenumber");
                let phonenumber_msg = document.getElementById("phonenumber_msg");

                const regex = new RegExp('^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$');
                if (phonenumber.value == "") {
                    phonenumber_msg.innerHTML = "Phonenumber can't be empty";
                } else if (phonenumber.value.length < 7) {
                    phonenumber_msg.innerHTML = "Not a valid phonenumber format (minimum length does not meet)";
                } else if (!regex.test(phonenumber.value)){
                    phonenumber_msg.innerHTML = "Not a valid phonenumber format";
                } else {
                    phonenumber_msg.innerHTML = "Good";
                    status = true;
                }
                phonenumber.value = (phonenumber.value).replace(/[^0-9]/g, '');

                return status;
            }

            function check_botname() {
                let status = false;
                let botname = document.getElementById("botname");
                let botname_msg = document.getElementById("botname_msg");

                if (botname.value == "") {
                    botname_msg.innerHTML = "Bot name can't be empty";
                } else {
                    botname_msg.innerHTML = "Good";
                    status = true;
                }
                return status;
            }

            function check_botuname() {
                let status = false;
                let botuname = document.getElementById("botuname");
                let botuname_msg = document.getElementById("botuname_msg");

                const ending_name = new RegExp('.*bot$','i');
                const special_char = new RegExp('^[@]{0,1}[a-z0-9]+$','i');
                const first_char = new RegExp('^[@]{0,1}[a-z][a-z0-9]+$','i');

                if (botuname.value == "") {
                    botuname_msg.innerHTML = "Bot username can't be empty";
                } else if (!ending_name.test(botuname.value)){
                    botuname_msg.innerHTML = "Bot username must end with `bot`";
                } else if (!special_char.test(botuname.value)){
                    botuname_msg.innerHTML = "Special Character not allowed";
                } else if (!first_char.test(botuname.value)){
                    botuname_msg.innerHTML = "First character can't be a number";
                } else {
                    botuname_msg.innerHTML = "Good";
                    status = true;
                }
                return status;
            }

            function validate_form() {
                let status = false;
                var status_phonenumber = check_phonenumber();
                var status_botname = check_botname();
                var status_botuname = check_botuname();

                if (status_phonenumber == true && status_botname == true && status_botuname == true)
                     status = true;

                return status;
            }

        </script>
    </body>
</html>