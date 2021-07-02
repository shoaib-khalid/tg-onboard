<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BotTokenController extends Controller
{
    //
    function loadView(Request $request) {
        $phonenumber = "";
        if(session('phonenumber')) { 
            $phonenumber = session('phonenumber'); 
        } else {
            return redirect('/failed');
        }

        $url = config('app.url');
        $endpoint = $url . "/logout";
        $object = ['_token' => csrf_token()];
        
        \Log::channel('transaction')->info("TGO <- PATH " . $endpoint);
        \Log::channel('transaction')->info("TGO <- PARAM " . $parameters);
        $response = Http::post($endpoint, $object);
        \Log::channel('transaction')->info("TGO <- RESP " . $response);
        
        $url = config('services.userservice.url');
        $endpoint = $url . "/v1/userChannels";
        $header = ["Content-type: application/json"];
        $object = [
            'refId' => '@symplifiedBot',
            'userId' => 'merchantId',
            'channelName' => 'telegram',
            'token' => 'token'
        ];

        \Log::channel('transaction')->info("User Service <- PATH " . $endpoint);
        \Log::channel('transaction')->info("User Service <- BODY " . json_encode($object));
        $response = Http::withHeaders($header)->post($endpoint, $object);
        \Log::channel('transaction')->info("User Service <- RESP " . $response);


        return view("bottoken");
    }

    function setWebhook () {
        $url = config('services.telegram.url');
        $endpoint = $url . "/bot" . $token . "/setwebhook";

        $botuname = "@symplifiedbot";
        $webhookUrl = "https://tgw.symplified.biz/telegram/incoming/" . $botuname;
        $data = ['url' => $webhookUrl];
        $parameters = http_build_query($data);

        \Log::channel('transaction')->info("Telegram <- PATH " . $endpoint);
        \Log::channel('transaction')->info("Telegram <- PARAM " . $parameters);
        $response =Http::get($endpoint . "?" . $parameters);
        \Log::channel('transaction')->info("Telegram <- RESP " . $response);
    }
}
