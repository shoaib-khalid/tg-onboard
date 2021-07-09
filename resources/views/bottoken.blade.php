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
    </style>
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
                        <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Do not have your own telegram bot yet ?<br> Create your own telegram bot now
                        </a>
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
                <label class="label block w-full" for="phonenumber">Bot token</label>  
                <input class="input appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" type="text" id="token" name="token" placeholder="example: 99853088:AAFfd36hf6btwTjGNF1R_9_gt48tgdhtzRK8"/> <br/>
                
                <label class="label block w-full" for="phonenumber">Bot Username</label>  
                <input class="input appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" type="text" id="botuname" name="botuname" value="{{$botuname}}" placeholder="eg. @SymplifiedBot"/> <br/>

                <label class="label block w-full" for="phonenumber">Merchant Id</label>  
                <input class="input appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" type="text" id="userid" name="userid" value="{{$userid}}" placeholder="0644ddb5-f7af-4700-b4b4-59dc44f90d88"/> <br/>
                
                <button id="submitBtn" class="bg-blue-500 hover:bg-blue-400 text-white font-bold py-2 px-4 rounded">Submit</button>
            </form>
        </div>
    </div>
</body>
    <script>
        function loadDoc() {

            action = document.getElementById("submitBtn").innerHTML;

            if (action === "Submit") {

                botuname = document.getElementById("botuname").value;
                userid = document.getElementById("userid").value;
                token = document.getElementById("token").value;

                if (!botuname || !userid  || !token ) {
                    document.getElementById("message").className = "bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative my-5";
                    document.getElementById("message").innerHTML = "All field are required";
                    return false;
                }

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
                            document.getElementById("message").innerHTML = "Bot Registration Sucess";
                            document.getElementById("submitBtn").innerHTML = "Close";
                        } else {
                            document.getElementById("message").className = "bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative my-5";
                            document.getElementById("message").innerHTML = JSON.parse(xhttp.responseText)["description"];
                        }
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
    </script>
</html>