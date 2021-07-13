<!DOCTYPE html>
<html>
<head>
    <title>Symplified</title>
    <link href="css/app.css" rel="stylesheet">
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
            <img class="mx-auto h-30 w-auto" src="images/logo-header.png" alt="Workflow">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Bot Creation by Symplified
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Already have a telegram bot ?
                <a href="/bottoken" class="font-medium text-indigo-600 hover:text-indigo-500">
                Onboard Your Telegram Bot With Us
                </a>
            </p>
        </div>
        <p>%s</p>
        <form method="POST" onsubmit="return validate_form()">
            <input name="_token" value="<?php print csrf_token() ?>" type="hidden">
            <div class="border border-gray-300 w-min">
                %s
            </div>
            <br>
            <button id="submitBtn" class="bg-blue-500 hover:bg-blue-400 text-white font-bold py-2 px-4 rounded" type="submit"/>%s</button>
        </form>
    </div>
    </div>
</div>
<script>
        function validate_form() {
            document.getElementById("submitBtn").disabled = true;
            document.getElementById("submitBtn").classList.add("disabled");

            return true;
        }
</script>
</body>
</html>