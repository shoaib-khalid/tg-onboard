<html>
<head>
    <title>Symplified - Configure</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        button.disabled:hover {
            cursor:not-allowed
        }
        #cont_token_msg, #cont_botuname_msg {
            display: none;
        }
    </style>
</head>
<body class="bg-blue-700">
    <div class="container">
        <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full space-y-8">
                <div>
                    <img class="mx-auto h-30 w-auto" src="{{ asset('images/logo-header.png') }}" alt="Workflow">
                    <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                        Telegram Onboard by Symplified
                    </h2>
                    <p class="mt-2 text-center text-sm text-gray-600">
                        Watch video below on how to create a telegram bot
                        <br>
                        <iframe class="w-full px-10" src="https://www.youtube.com/embed/RqzmQpI3kFU" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </p>
                </div>
            <div>
                @if ($message === "")
                <p class="" id="message">
                @elseif ($status == "success")
                <p class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative my-5" id="message">
                @else
                <p class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative my-5" id="message">
                @endif
                    {!! $message !!}
                </p>
            </div>
            <form onsubmit="loadDoc(); return false;">
                @csrf
                <label class="label block w-full" for="token">Bot token</label>  
                <input class="input input-text-1" type="text" id="token" name="token" placeholder="example: 99853088:AAFfd36hf6btwTjGNF1R_9_gt48tgdhtzRK8"/> <br/>
                <div id="cont_token_msg" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative my-5" role="error">
                    <strong class="font-bold">Alert!</strong>
                    <span id="token_msg"></span> <br>
                </div>

                <label class="label block w-full" for="botuname">Bot Username</label>  
                <input class="input input-text-1" type="text" id="botuname" name="botuname" value="{{$botuname}}" placeholder="eg. @SymplifiedBot" onfocusout="check_botuname()"/> <br/>
                <div id="cont_botuname_msg" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative my-5" role="error">
                    <strong class="font-bold">Alert!</strong>
                    <span id="botuname_msg"></span> <br>
                </div>

                <!-- <label class="label block w-full" for="userid">Merchant Id</label>   -->
                <input class="input input-text-1" type="hidden" id="userid" name="userid" value="{{$userid}}" placeholder="0644ddb5-f7af-4700-b4b4-59dc44f90d88"/> <br/>
                
                <button id="submitBtn" class="bg-blue-500 hover:bg-blue-400 text-white font-bold py-2 px-4 rounded">Submit</button>
            </form>
        </div>
    </div>
</body>
    <script>
        function loadDoc() {

            action = document.getElementById("submitBtn").innerHTML;

            if (action === "Submit") {
                let status = false;
                botuname = document.getElementById("botuname").value;
                userid = document.getElementById("userid").value;
                token = document.getElementById("token").value;

                // check whether input in empty
                document.getElementById("message").style.display = "none";
                if (!botuname || !userid  || !token ) {
                    document.getElementById("message").style.display = "block";
                    document.getElementById("message").className = "bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative my-5";
                    document.getElementById("message").innerHTML = "All field are required";
                    return false;
                }

                // check botusername validity
                var status_botuname = check_botuname();
                if (status_botuname !== true) {
                    document.getElementById("submitBtn").disabled = false;
                    document.getElementById("submitBtn").classList.remove("disabled");
                    return false;
                }

                // disabled submit button (to avoid double request by users)
                document.getElementById("submitBtn").disabled = true;
                document.getElementById("submitBtn").classList.add("disabled");

                var data = {
                    botuname : botuname,
                    userid : userid,
                    token : token
                };

                var csrf_token =  "{{ csrf_token() }}";

                const xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange  = function() {
                    if(xhttp.readyState == 4) {
                        if (xhttp.status == 200) {
                            document.getElementById("message").className = "bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative my-5";
                            document.getElementById("message").innerHTML = JSON.parse(xhttp.responseText)["description"];
                            // change submit button to close button
                            document.getElementById("submitBtn").innerHTML = "Close";
                        } else {
                            document.getElementById("message").className = "bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative my-5";
                            document.getElementById("message").innerHTML = JSON.parse(xhttp.responseText)["description"];
                        }
                        // re-display close button
                        document.getElementById("message").style.display = "block";
                        document.getElementById("submitBtn").disabled = false;
                        document.getElementById("submitBtn").classList.remove("disabled");
                    }
                }
                xhttp.open("POST", "/bottoken");
                xhttp.setRequestHeader("Content-Type", "application/json");
                xhttp.setRequestHeader("Accept", "application/json");
                xhttp.setRequestHeader("X-CSRF-Token",csrf_token);
                xhttp.send(JSON.stringify(data));
                
                return false;
            } else {
                window.open('','_self').close();
            }
        }

        function check_botuname() {
            let status = false;
            let botuname = document.getElementById("botuname");
            let botuname_msg = document.getElementById("botuname_msg");
            let cont_botuname_msg = document.getElementById("cont_botuname_msg");

            const ending_name = new RegExp('.*bot$','i');
            const special_char = new RegExp('^[@]{0,1}[a-z0-9]+$','i');
            const first_char = new RegExp('^[@]{0,1}[a-z][a-z0-9]+$','i');

            cont_botuname_msg.className = "bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative my-5";
            if (botuname.value == "") {
                botuname_msg.innerHTML = "Bot username can't be empty";
                cont_botuname_msg.style.display = "block";
            } else if (botuname.value.length > 50){
                botuname_msg.innerHTML = "Bot username length must not exceed 50 character";
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
                status = true;
                // botuname_msg.innerHTML = "Good";
                cont_botuname_msg.style.display = "none";
                // cont_botuname_msg.className = "bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative my-5";
            }
            return status;
        }
    </script>
</html>