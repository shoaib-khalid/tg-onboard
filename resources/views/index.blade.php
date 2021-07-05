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

        #cont_phonenumber_msg, #cont_botname_msg, #cont_botuname_msg {
            display: none;
        }
        </style>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    </head>
    <body class="bg-blue-700">
        <div class="container">

        <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full space-y-8">
                <div>
                    <img class="mx-auto h-30 w-auto" src="https://symplified.biz/assets/SYMplified%20_%20Simply%20built%20for%20all_files/logo-header.png" alt="Workflow">
                    <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                        Telegram Onboard by Symplified
                    </h2>
                    <p class="mt-2 text-center text-sm text-gray-600">
                        Or
                        <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Set Your Bot Webhook With Us
                        </a>
                    </p>
                </div>
                <h1></h1>
                <form method="post" onsubmit="return validate_form()" action="">
                    
                    @csrf

                    <label class="label block w-full" for="phonenumber">Tell us the phone number would you want to associate with this Telegram Bot</label>  
                    <input class="input appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" id="phonenumber" name="phonenumber" type="text" placeholder="eg: +60123456789" onfocusout="check_phonenumber()"/> 
                    <div id="cont_phonenumber_msg" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative my-5" role="error">
                        <strong class="font-bold">Alert!</strong>
                        <span id="phonenumber_msg"></span> <br>
                    </div>

                    
                    <label class="label block w-full" for="botname">What name would you want to give your store in Telegram</label> 
                    <input class="input appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" id="botname" name="botname" type="text" placeholder="eg: Symplified Bot" onfocusout="check_botname()"/>
                    <div id="cont_botname_msg" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative my-5" role="error">
                        <strong class="font-bold">Alert!</strong>
                        <span id="botname_msg"></span> <br>
                    </div>
                    
                    <label class="label block w-full" for="botname">What name would you want to give your Telegram</label> 
                    <input class="input appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" id="botuname" name="botuname" type="text" placeholder="eg. @SymplifiedBot" onfocusout="check_botuname()"/>
                    <div id="cont_botuname_msg" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative my-5" role="error">
                        <strong class="font-bold">Alert!</strong>
                        <span id="botuname_msg"></span> <br>
                    </div>

                    <input type="hidden" name="userid" value="{{$userid}}">

                    <button class="bg-blue-500 hover:bg-blue-400 text-white font-bold py-2 px-4 rounded" type="submit">Lets Go</button>
                </form>
            </div>
        </div>
        </div>
        
        <script>
            function check_phonenumber() {
                let status = false;
                let phonenumber = document.getElementById("phonenumber");
                let phonenumber_msg = document.getElementById("phonenumber_msg");

                const regex = new RegExp('^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$');
                if (phonenumber.value == "") {
                    phonenumber_msg.innerHTML = "Phonenumber can't be empty";
                    cont_phonenumber_msg.style.display = "block";
                } else if (phonenumber.value.length < 7) {
                    phonenumber_msg.innerHTML = "Not a valid phonenumber format (minimum length does not meet)";
                    cont_phonenumber_msg.style.display = "block";
                } else if (!regex.test(phonenumber.value)){
                    phonenumber_msg.innerHTML = "Not a valid phonenumber format";
                    cont_phonenumber_msg.style.display = "block";
                } else {
                    phonenumber_msg.innerHTML = "Good";
                    cont_phonenumber_msg.style.display = "none";
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
                    cont_botname_msg.style.display = "block";
                } else {
                    botname_msg.innerHTML = "Good";
                    cont_botname_msg.style.display = "none";
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
                    cont_botuname_msg.style.display = "block";
                } else if (!ending_name.test(botuname.value)){
                    botuname_msg.innerHTML = "Bot username must end with `bot`";
                    cont_botuname_msg.style.display = "block";
                } else if (!special_char.test(botuname.value)){
                    botuname_msg.innerHTML = "Special Character not allowed";
                    cont_botuname_msg.style.display = "block";
                } else if (!first_char.test(botuname.value)){
                    botuname_msg.innerHTML = "First character can't be a number";
                    cont_botuname_msg.style.display = "block";
                } else {
                    botuname_msg.innerHTML = "Good";
                    cont_botuname_msg.style.display = "none";
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