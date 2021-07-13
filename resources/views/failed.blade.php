<html>
<head>
    <title>Symplified - Configure</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-blue-700">
    <div class="container">
        <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full space-y-8">
                <div>
                    <img class="mx-auto h-30 w-auto" src="{{ asset('images/logo-header.png') }}" alt="Workflow">
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
            <div>
            <div>
                @if ($error !== "")
                <div class="mt-6 text-center text-2xl font-bold text-gray-900">
                    <h1>Sorry</h1>
                    <p class="label block w-full">Process Failed</p>
                </div>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative my-5">
                    <p >Error - {{ $error }}</p>
                    <p>Description - {{ $description }}</p>
                </div>
                @else
                <div class="mt-6 text-center text-2xl font-bold text-gray-900">
                    <h1>Page not Found</h1>
                    <p class="label block w-full">404</p>
                    <br>
                    <br>
                </div>
                @endif
                
                <a class="cursor-pointer bg-blue-500 hover:bg-blue-400 text-white font-bold py-2 px-4 rounded" href="{{ config('app.url') . '/?userid=' . $userid }}">Go Back</a>
            </div>
        </div>
    </div>
</body>
</html>