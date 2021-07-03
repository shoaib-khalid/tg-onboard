<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BotTokenController extends Controller
{
    //
    function loadView() {

        // if there's a madeline login session, log it out
        if(session('phonenumber')) { 
            $phonenumber = session('phonenumber'); 

            $url = config('app.url');
            $endpoint = $url . "/logout";
            $object = [
                'phonenumber' => $phonenumber
            ];
            
            \Log::channel('transaction')->info("TGO <- PATH " . $endpoint);
            \Log::channel('transaction')->info("TGO <- BODY " . json_encode($object));
            $response = Http::post($endpoint, $object);
            \Log::channel('transaction')->info("TGO <- RESP " . $response);
        } 

        return view("bottoken",[
            'botuname' => session('botuname'),
            'userId' => session('userId'),
            'token' => session('token')
        ]);
    }

    /**
     * Set webhook to telegram api
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    function setWebhook (Request $request) {

        $data = $request->input();

        if (isset($data['botuname']) && isset($data['userId']) && isset($data['token'])){
            $botuname = $data['botuname'];
            $userId = $data['userId'];
            $token = $data['token'];
        } else {
            return redirect('/failed');
        }

        /**
         * Set Webhook
         */
        $endpoint = config('services.telegram.url') . "/bot" . $token . "/setwebhook";
        $webhookUrl = config('services.tgw.url') . "/incoming/" . $botuname;
        $data = ['url' => $webhookUrl];
        $parameters = http_build_query($data);

        \Log::channel('transaction')->info("Telegram <- PATH " . $endpoint);
        \Log::channel('transaction')->info("Telegram <- PARAM " . $parameters);
        $response = Http::get($endpoint . "?" . $parameters);
        \Log::channel('transaction')->info("Telegram <- RESP " . $response);


        /**
         * Update User Service
         */

        $url = config('services.userservice.url');
        $endpoint = $url . "/v1/userChannels";
        $header = ["Content-type: application/json"];
        $object = [
            'refId' => $botuname,
            'userId' => $userId,
            'channelName' => 'telegram',
            'token' => $token
        ];
        
        \Log::channel('transaction')->info("User Service <- PATH " . $endpoint);
        \Log::channel('transaction')->info("User Service <- BODY " . json_encode($object));
        $response = Http::withHeaders($header)->post($endpoint, $object);
        \Log::channel('transaction')->info("User Service <- RESP " . $response);
    }
}
